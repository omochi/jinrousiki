<?php
//投票コマンドがあっているかチェック
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return true;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return true;

  OutputVoteResult('無効な投票です');
}

//シーンの一致チェック
function CheckScene(){
  global $ROOM, $SELF;
  if($ROOM->day_night != $SELF->last_load_day_night) OutputVoteResult('戻ってリロードしてください');
}

//投票結果出力
function OutputVoteResult($sentence, $reset_vote = false){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  if($reset_vote) $ROOM->DeleteVote(); //今までの投票を全部削除
  $title  = $SERVER_CONF->title . ' [投票結果]';
  $header = '<div id="game_top" align="center">';
  $footer = '<br>'."\n" . $RQ_ARGS->back_url . '</div>';
  OutputActionResult($title, $header . $sentence . $footer, '', true);
}

//投票ページ HTML ヘッダ出力
function OutputVotePageHeader(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  OutputHTMLHeader($SERVER_CONF->title . ' [投票]', 'game');
  $css_path = JINRO_CSS;
  if($ROOM->day_night != ''){
    $css = $css_path . '/game_' . $ROOM->day_night . '.css';
    echo '<link rel="stylesheet" href="' . $css . '">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="{$css_path}/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a id="game_top"></a>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">

EOF;
}

//人数とゲームオプションに応じた役職テーブルを返す
function GetRoleList($user_count){
  global $GAME_CONF, $CAST_CONF, $ROLE_DATA, $ROOM;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $CAST_CONF->role_list[$user_count]; //人数に応じた配役リストを取得
  if(is_null($role_list)){ //リストの有無をチェック
    $str = $user_count . '人は設定されていません';
    OutputVoteResult($error_header . $str . $error_footer, true);
  }
  //PrintData($ROOM->option_list);

  if($ROOM->IsOptionGroup('chaos')){ //闇鍋モード
    $random_role_list = array(); //ランダム配役結果
    foreach(array('chaos', 'chaosfull', 'chaos_hyper', 'chaos_verso') as $option){ //グレード検出
      if($ROOM->IsOption($option)){
	$base_name   = $option;
	$chaos_verso = $option == 'chaos_verso';
	break;
      }
    }

    //-- 固定枠設定 --//
    $fix_role_list = $CAST_CONF->{$base_name.'_fix_role_list'}; //グレード個別設定

    if(count($stack = $ROOM->GetOptionList('topping')) > 0){ //固定配役追加モード
      //PrintData($stack);
      if(is_array($stack['fix'])){ //定数
	foreach($stack['fix'] as $role => $count) $fix_role_list[$role] += $count;
      }
      if(is_array($stack['random'])){ //ランダム
	foreach($stack['random'] as $key => $list){
	  $random_list = $CAST_CONF->GenerateRandomList($list);
	  //PrintData($random_list, $stack['count'][$key]);
	  for($count = $stack['count'][$key]; $count > 0; $count--){
	    $fix_role_list[GetRandom($random_list)]++;
	  }
	}
      }
      //PrintData($fix_role_list, 'Topping('.array_sum($fix_role_list).')');
    }

    //個別オプション(ゲルト君モード：村人 / 探偵村：探偵)
    foreach(array('gerd' => 'human', 'detective' => 'detective_common') as $option => $role){
      if($ROOM->IsOption($option) && is_null($fix_role_list[$role])) $fix_role_list[$role] = 1;
    }
    //PrintData($fix_role_list, 'Fix('.array_sum($fix_role_list).')');

    $boost_list = $ROOM->GetOptionList('boost_rate'); //出現率補正リスト
    //PrintData($boost_list);
    if(! $chaos_verso){ //-- 最小出現補正 --//
      $stack = array(); //役職系統別配役数
      foreach($fix_role_list as $key => $value){ //固定枠内の該当グループをカウント
	$stack[$ROLE_DATA->DistinguishRoleGroup($key)] = $value;
      }
      //PrintData($stack, 'FixRole');

      foreach(array('wolf', 'fox') as $role){
	$rate  = $CAST_CONF->GetChaosRateList($base_name.'_'.$role.'_list', $boost_list);
	$list  = $CAST_CONF->GenerateRandomList($rate);
	$count = round($user_count / $CAST_CONF->{'chaos_min_'.$role.'_rate'}) - $stack[$role];
	//PrintData($list, $count);
	//$CAST_CONF->RateToProbability($rate); //テスト用
	$CAST_CONF->AddRandom($random_role_list, $list, $count);
	//PrintData($random_role_list, $role);
      }
    }
    //PrintData($random_role_list, 'random('.array_sum($random_role_list).')');

    //-- ランダム配役 --//
    $rate  = $CAST_CONF->GetChaosRateList($base_name.'_random_role_list', $boost_list);
    $list  = $CAST_CONF->GenerateRandomList($rate);
    $count = $user_count - (array_sum($random_role_list) + array_sum($fix_role_list));
    //PrintData($list, $count);
    //PrintData(array_sum($base_list));
    //$CAST_CONF->RateToProbability($rate); //テスト用
    $CAST_CONF->AddRandom($random_role_list, $list, $count);

    //固定とランダムを合計
    $role_list = $random_role_list;
    foreach($fix_role_list as $key => $value) $role_list[$key] += (int)$value;
    //PrintData($role_list, '1st('.array_sum($role_list).')');

    if(! $chaos_verso){ //-- 上限補正 --//
      //役職グループ毎に集計
      $total_stack  = array(); //グループ別リスト (全配役)
      $random_stack = array(); //グループ別リスト (ランダム)
      foreach($role_list as $role => $count){
	$total_stack[$ROLE_DATA->DistinguishRoleGroup($role)][$role] = $count;
      }
      foreach($random_role_list as $role => $count){
	$random_stack[$ROLE_DATA->DistinguishRoleGroup($role)][$role] = $count;
      }

      foreach($CAST_CONF->chaos_role_group_rate_list as $name => $rate){
	$target =& $random_stack[$name];
	if(! (is_array($total_stack[$name]) && is_array($target))) continue;
	$count = array_sum($total_stack[$name]) - round($user_count / $rate);
	//if($count > 0) PrintData($count, $name); //テスト用
	for(; $count > 0; $count--){
	  if(array_sum($target) < 1) break;
	  //PrintData($target, "　　$count: before");
	  arsort($target);
	  //PrintData($target, "　　$count: after");
	  $key = key($target);
	  //PrintData($key, "　　target");
	  $target[$key]--;
	  $role_list[$key]--;
	  $role_list['human']++;
	  //PrintData($target, "　　$count: delete");

	  //0 になった役職はリストから除く
	  if($role_list[$key] < 1) unset($role_list[$key]);
	  if($target[$key]    < 1) unset($target[$key]);
	}
      }
      //PrintData($role_list, '2nd('.array_sum($role_list).')');
    }

    if($ROOM->IsDummyBoy()){ //-- 身代わり君モード補正 --//
      $dummy_count   = $user_count; //身代わり君対象役職数
      $target_stack  = array(); //補正対象リスト
      $disable_stack = $CAST_CONF->GetDummyBoyRoleList(); //身代わり君の対象外役職リスト
      foreach($role_list as $role => $count){ //対象役職の情報を収集
	foreach($disable_stack as $disable_role){
	  if(strpos($role, $disable_role) !== false){
	    $target_stack[$disable_role][$role] = $count;
	    $dummy_count -= $count;
	    break; //多重カウント防止 (例：poison_wolf)
	  }
	}
      }

      if($dummy_count < 1){
	//PrintData($target_stack, "for dummy");
	foreach($target_stack as $role => $stack){ //対象役職からランダムに村人へ置換
	  //PrintData($stack, "　　$role");
	  //人狼・探偵村の探偵はゼロにしない
	  if(($role == 'wolf' || ($ROOM->IsOption('detective') && $role == 'detective')) &&
	     array_sum($stack) < 2) continue;

	  arsort($stack);
	  //PrintData($stack, "　　list");
	  $key = key($stack);
	  //PrintData($key, "　　role");
	  $role_list[$key]--;
	  $role_list['human']++;
	  if($role_list[$key] < 1) unset($role_list[$key]); //0 になった役職はリストから除く
	  break;
	}
	//PrintData($role_list, '3rd_list('.array_sum($role_list).')');
      }
    }

    if(! $chaos_verso && ! $ROOM->IsReplaceHumanGroup()){ //-- 村人上限補正 --//
      $role  = 'human';
      $count = $role_list[$role] - round($user_count / $CAST_CONF->chaos_max_human_rate);
      if($ROOM->IsOption('gerd')) $count--;
      if($count > 0){
	$rate = $CAST_CONF->GetChaosRateList($base_name.'_replace_human_role_list', $boost_list);
	$list = $CAST_CONF->GenerateRandomList($rate);
	//PrintData($list, $count);
	//$CAST_CONF->RateToProbability($rate); //テスト用
	$CAST_CONF->AddRandom($role_list, $list, $count);
	$role_list[$role] -= $count;
	if($role_list[$role] < 1) unset($role_list[$role]); //0 になったらリストから除く
	//PrintData($role_list, '4th_list('.array_sum($role_list).')');
      }
    }
  }
  elseif($ROOM->IsOption('duel')){ //決闘村
    $role_list = $CAST_CONF->SetDuel($user_count);
  }
  elseif($ROOM->IsOption('gray_random')){ //グレラン村
    $role_list = $CAST_CONF->SetGrayRandom($user_count);
  }
  elseif($ROOM->IsQuiz()){ //クイズ村
    $role_list = $CAST_CONF->SetQuiz($user_count);
  }
  else{ //通常村
    //探偵 (共有 or 村人 → 探偵)
    if($ROOM->IsOption('detective')){
      if($role_list['common'] > 0){
	$role_list['common']--;
	$role_list['detective_common']++;
      }
      elseif($role_list['human'] > 0){
	$role_list['human']--;
	$role_list['detective_common']++;
      }
    }

    //埋毒者 (村人2 → 埋毒者1・人狼1)
    if($ROOM->IsOption('poison') && $user_count >= $CAST_CONF->poison && $role_list['human'] > 1){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //暗殺者 (村人2 → 暗殺者1・人狼1)
    if($ROOM->IsOption('assassin') && $user_count >= $CAST_CONF->assassin &&
       $role_list['human'] > 1){
      $role_list['human'] -= 2;
      $role_list['assassin']++;
      $role_list['wolf']++;
    }

    //白狼 (人狼 → 白狼)
    if($ROOM->IsOption('boss_wolf') && $user_count >= $CAST_CONF->boss_wolf &&
       $role_list['wolf'] > 0){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //毒狼 (人狼 → 毒狼、村人 → 薬師)
    if($ROOM->IsOption('poison_wolf') && $user_count >= $CAST_CONF->poison_wolf &&
       $role_list['wolf'] > 0 && $role_list['human'] > 0){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //憑狼 (人狼 → 憑狼)
    if($ROOM->IsOption('possessed_wolf') && $user_count >= $CAST_CONF->possessed_wolf &&
       $role_list['wolf'] > 0){
      $role_list['wolf']--;
      $role_list['possessed_wolf']++;
    }

    //天狼 (人狼 → 天狼)
    if($ROOM->IsOption('sirius_wolf') && $user_count >= $CAST_CONF->sirius_wolf &&
       $role_list['wolf'] > 0){
      $role_list['wolf']--;
      $role_list['sirius_wolf']++;
    }

    //妖狐 (村人 → 妖狐)
    if($ROOM->IsOption('fox') && $user_count >= $CAST_CONF->fox && $role_list['human'] > 0){
      $role_list['human']--;
      $role_list['fox']++;
    }

    //子狐 (妖狐 → 子狐)
    if($ROOM->IsOption('child_fox') && $user_count >= $CAST_CONF->child_fox &&
       $role_list['fox'] > 0){
      $role_list['fox']--;
      $role_list['child_fox']++;
    }

    //キューピッド (村人 → キューピッド)
    if($ROOM->IsOption('cupid') && ! $ROOM->IsOption('full_cupid') &&
       $user_count >= $CAST_CONF->cupid && $role_list['human'] > 0){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //巫女 (村人2 → 巫女1・女神1)
    if($ROOM->IsOption('medium') && $user_count >= $CAST_CONF->medium && $role_list['human'] > 1){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['mind_cupid']++;
    }

    //神話マニア (村人 → 神話マニア)
    if($ROOM->IsOption('mania') && ! $ROOM->IsOption('full_mania') &&
       $user_count >= $CAST_CONF->mania && $role_list['human'] > 0){
      $role_list['human']--;
      $role_list['mania']++;
    }
  }
  $CAST_CONF->ReplaceRole($role_list); //村人置換村

  //$is_single_role = true;
  $is_single_role = false;
  if($is_single_role){ //一人一職村対応
    $role_list = array(); //配役をリセット
    $base_role_list = array('wolf', 'mage', 'human', 'jammer_mad', 'necromancer',
			    'common', 'crisis_priest', 'boss_wolf', 'guard', 'dark_fairy',
			    'poison', 'agitate_mad', 'fox', 'cupid', 'soul_mage',
			    'resist_wolf', 'trap_common', 'yama_necromancer', 'child_fox', 'mania',
			    'tongue_wolf', 'assassin', 'fend_guard', 'cute_fox', 'ghost_common',
			    'cute_wolf', 'black_fox', 'light_fairy', 'poison_jealousy', 'self_cupid',
			    'silver_wolf','scarlet_wolf','wise_wolf', 'mind_cupid', 'dummy_chiroptera',);
    for($i = $user_count; $i > 0; $i--) $role_list[array_shift($base_role_list)]++;
  }

  //お祭り村
  if($ROOM->IsOption('festival') &&
     is_array($target =& $CAST_CONF->festival_role_list[$user_count])) $role_list = $target;

  if($role_list['human'] < 0){ //村人の人数をチェック
    $str = '「村人」の人数がマイナスになってます';
    OutputVoteResult($error_header . $str . $error_footer, true);
  }
  if($role_list['wolf'] < 0){ //人狼の人数をチェック
    $str = '「人狼」の人数がマイナスになってます';
    OutputVoteResult($error_header . $str . $error_footer, true);
  }

  //役職名を格納した配列を生成
  $now_role_list = array();
  foreach($role_list as $key => $value){
    for($i = 0; $i < $value; $i++) array_push($now_role_list, $key);
  }
  $role_count = count($now_role_list);

  if($role_count != $user_count){ //配列長をチェック
    if($ROOM->test_mode){
      PrintData($role_count, 'エラー：配役数');
      return $now_role_list;
    }
    $str = '村人 (' . $user_count . ') と配役の数 (' . $role_count . ') が一致していません';
    OutputVoteResult($error_header . $str . $error_footer, true);
  }

  return $now_role_list;
}

//役職の人数通知リストを作成する
function GenerateRoleNameList($role_count_list, $css = false){
  global $ROLE_DATA, $ROOM;

  $chaos = $ROOM->IsOption('chaos_open_cast_camp') ? 'camp' :
    ($ROOM->IsOption('chaos_open_cast_role') ? 'role' : NULL);
  switch($chaos){
  case 'camp':
    $header = '出現陣営：';
    $main_type = '陣営';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(array_key_exists($key, $ROLE_DATA->main_role_list)){
	$main_role_list[$ROLE_DATA->DistinguishCamp($key, true)] += $value;
      }
    }
    break;

  case 'role':
    $header = '出現役職種：';
    $main_type = '系';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(array_key_exists($key, $ROLE_DATA->main_role_list)){
	$main_role_list[$ROLE_DATA->DistinguishRoleGroup($key)] += $value;
      }
    }
    break;

  default:
    $header = '出現役職：';
    $main_role_list = $role_count_list;
    break;
  }

  switch($chaos){
  case 'camp':
  case 'role':
    $sub_type = '系';
    $sub_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! array_key_exists($key, $ROLE_DATA->sub_role_list)) continue;
      foreach($ROLE_DATA->sub_role_group_list as $list){
	if(in_array($key, $list)) $sub_role_list[$list[0]] += $value;
      }
    }
    break;

  default:
    $sub_role_list = $role_count_list;
    break;
  }

  $stack = array();
  foreach($ROLE_DATA->main_role_list as $key => $value){
    $count = (int)$main_role_list[$key];
    if($count > 0){
      if($css) $value = $ROLE_DATA->GenerateMainRoleTag($key);
      $stack[] = $value . $main_type . $count;
    }
  }

  foreach($ROLE_DATA->sub_role_list as $key => $value){
    $count = (int)$sub_role_list[$key];
    if($count > 0) $stack[] = '(' . $value . $sub_type . $count . ')';
  }
  return $header . implode('　', $stack);
}

//ゲーム開始投票集計処理
function AggregateVoteGameStart($force_start = false){
  global $CAST_CONF, $MESSAGE, $ROLE_DATA, $ROOM, $USERS;

  $user_count = $USERS->GetUserCount(); //ユーザ総数を取得
  if($ROOM->test_mode){
    $vote_count = $user_count;
  }
  else{
    CheckSituation('GAMESTART');

    //投票総数を取得
    if($force_start){ //強制開始モード時はスキップ
      $vote_count = $user_count;
    }
    else{
      $vote_count = $ROOM->LoadVote(); //投票情報をロード (ロック前の情報は使わない事)
      //クイズ村以外の身代わり君の分を加算
      if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;
    }
  }

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count != $user_count || $vote_count < min(array_keys($CAST_CONF->role_list))){
    return false;
  }

  //-- 配役決定ルーチン --//
  $ROOM->LoadOption(); //配役設定オプションの情報を取得
  //PrintData($ROOM->option_role);
  //PrintData($ROOM->option_list);

  //配役決定用変数をセット
  $uname_list        = $USERS->GetLivingUsers(); //ユーザ名の配列
  $role_list         = GetRoleList($user_count); //役職リストを取得
  $fix_uname_list    = array(); //役割の決定したユーザ名を格納する
  $fix_role_list     = array(); //ユーザ名に対応する役割
  $remain_uname_list = array(); //希望の役割になれなかったユーザ名を一時的に格納
  //PrintData($uname_list, 'Uname');
  //PrintData($role_list, 'Role');

  //フラグセット
  $is_gerd      = $ROOM->IsOption('gerd');
  $is_chaos     = $ROOM->IsOptionGroup('chaos'); //chaosfull も含む
  $is_quiz      = $ROOM->IsQuiz();
  $is_detective = $ROOM->IsOption('detective');
  //エラーメッセージ
  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';
  $reset_flag   = ! $ROOM->test_mode;

  if($ROOM->IsDummyBoy()){ //身代わり君の役職を決定
    if(($is_gerd && in_array('human', $role_list)) || $is_quiz){ //役職固定オプション判定
      $fit_role = $is_gerd ? 'human' : 'quiz';
      if(($key = array_search($fit_role, $role_list)) !== false){
	$fix_role_list[] = $fit_role;
	unset($role_list[$key]);
      }
    }
    else{
      shuffle($role_list); //配列をシャッフル
      $stack = $CAST_CONF->GetDummyBoyRoleList(); //身代わり君の対象外役職リスト

      for($i = count($role_list); $i > 0; $i--){
	$role = array_shift($role_list); //配役リストから先頭を抜き出す
	foreach($stack as $disable_role){
	  if(strpos($role, $disable_role) !== false){
	    $role_list[] = $role; //配役リストの末尾に戻す
	    continue 2;
	  }
	}
	$fix_role_list[] = $role;
	break;
      }
    }

    if(count($fix_role_list) < 1){ //身代わり君に役が与えられているかチェック
      $str = '身代わり君に役が与えられていません';
      OutputVoteResult($error_header . $sentence . $error_footer, $reset_flag);
    }
    $fix_uname_list[] = 'dummy_boy'; //決定済みリストに身代わり君を追加
    unset($uname_list[array_search('dummy_boy', $uname_list)]); //身代わり君を削除
    //PrintData($fix_role_list, 'dummy_boy');
  }

  shuffle($uname_list); //ユーザリストをランダムに取得
  //PrintData($uname_list, 'ShuffleUname');

  //希望役職を参照して一次配役を行う
  if($ROOM->IsOption('wish_role')){ //役割希望制の場合
    $wish_group = $ROOM->IsChaosWish(); //特殊村用
    foreach($uname_list as $uname){
      do{
	$role = $USERS->GetRole($uname); //希望役職を取得
	if($role == '' || mt_rand(1, 100) > $CAST_CONF->wish_role_rate) break;
	$fit_role = $role;

	if($wish_group){ //特殊村はグループ単位で希望処理を行なう
	  $stack = array();
	  foreach($role_list as $stack_role){
	    if($role == $ROLE_DATA->DistinguishRoleGroup($stack_role)) $stack[] = $stack_role;
	  }
	  $fit_role = GetRandom($stack);
	}
	//希望役職の存在チェック
	if(($role_key = array_search($fit_role, $role_list)) === false) break;

	//希望役職があれば決定
	$fix_uname_list[] = $uname;
	$fix_role_list[]  = $fit_role;
	unset($role_list[$role_key]);
	continue 2;
      }while(false);
      $remain_uname_list[] = $uname; //決まらなかった場合は未決定リスト行き
    }
  }
  else{
    shuffle($role_list); //配列をシャッフル
    $fix_uname_list = array_merge($fix_uname_list, $uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //残り配役リストをリセット
  }

  //一次配役の結果を検証
  $remain_uname_list_count = count($remain_uname_list); //未決定者の人数
  $role_list_count         = count($role_list); //残り配役数
  if($remain_uname_list_count != $role_list_count){
    $uname_str = '配役未決定者の人数 (' . $remain_uname_list_count . ') ';
    $role_str  = '残り配役の数 (' . $role_list_count . ') ';
    $sentence  = $uname_str . 'と' . $role_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true);
  }

  //未決定者を二次配役
  if($remain_uname_list_count > 0){
    shuffle($role_list); //配列をシャッフル
    $fix_uname_list = array_merge($fix_uname_list, $remain_uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //残り配役リストをリセット
  }

  //二次配役の結果を検証
  $fix_uname_list_count = count($fix_uname_list); //決定者の人数
  if($user_count != $fix_uname_list_count){
    $user_str  = '村人の人数 (' . $user_count . ') ';
    $uname_str = '配役決定者の人数 (' . $fix_uname_list_count . ') ';
    $sentence  = $user_str . 'と' . $uname_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true);
  }

  $fix_role_list_count = count($fix_role_list); //配役の数
  if($fix_uname_list_count != $fix_role_list_count){
    $uname_str = '配役決定者の人数 (' . $fix_uname_list_count . ') ';
    $role_str  = '配役の数 (' . $fix_role_list_count . ') ';
    $sentence  = $uname_str . 'と' . $role_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true);
  }

  $role_list_count = count($role_list); //残り配役数
  if($role_list_count > 0){
    $sentence = '配役リストに余り (' . $role_list_count .') があります';
    OutputVoteResult($error_header . $sentence . $error_footer, true);
  }

  //兼任となる役割の設定
  $rand_keys = array_keys($fix_role_list); //人数分の ID リストを取得
  shuffle($rand_keys); //シャッフルしてランダムキーに変換
  $rand_keys_index = 0;
  $sub_role_count_list = array();
  $roled_list = array(); //配役済み番号
  //割り振り対象外役職のリスト
  $delete_role_list = array(
    'febris', 'frostbite', 'death_warrant', 'panelist', 'day_voter', 'wirepuller_luck',
    'occupied_luck', 'mind_read', 'mind_receiver', 'mind_friend', 'mind_sympathy', 'mind_evoke',
    'mind_presage', 'mind_lonely', 'mind_sheep', 'sheep_wisp', 'lovers', 'challenge_lovers',
    'possessed_exchange', 'joker', 'rival', 'enemy', 'supported', 'death_note', 'death_selected',
    'possessed_target', 'possessed', 'infected', 'psycho_infected', 'bad_status', 'sweet_status',
    'protected', 'lost_ability', 'muster_ability', 'changed_therian', 'copied', 'copied_trick',
    'copied_basic', 'copied_soul', 'copied_teller');

  //サブ役職テスト用
  /*
  $stack = array('wisp', 'black_wisp', 'spell_wisp', 'foughten_wisp', 'gold_wisp');
  $delete_role_list = array_merge($delete_role_list, $stack);
  for($i = 0; $i < $user_count; $i++){
    if(($role = array_shift($stack)) == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      $stack[] = $role;
      continue;
    }
    $fix_role_list[$i] .= ' ' . $role;
    $roled_list[] = $i;
  }
  */

  foreach(array('decide', 'authority') as $role){ //オプションでつけるサブ役職
    if(! $ROOM->IsOption($role)) continue;
    $delete_role_list[] = $role;
    if($user_count >= $CAST_CONF->$role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $role;
    }
  }

  if($ROOM->IsOption('liar')){ //狼少年村
    $role = 'liar';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($ROOM->IsOption('gentleman')){ //紳士・淑女村
    $stack = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $stack);
    for($i = 0; $i < $user_count; $i++){ //全員に性別に応じた紳士か淑女をつける
      $fix_role_list[$i] .= ' ' . $stack[$USERS->ByUname($fix_uname_list[$i])->sex];
    }
  }

  foreach(array('deep_sleep', 'blinder', 'mind_open') as $role){ //静寂村・宵闇村・白夜村
    if($ROOM->IsOption($role)){
      $delete_role_list[] = $role;
      for($i = 0; $i < $user_count; $i++) $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($ROOM->IsOption('critical')){ //急所村
    array_push($delete_role_list, 'critical_voter', 'critical_luck');
    $role = ' critical_voter critical_luck';
    for($i = 0; $i < $user_count; $i++) $fix_role_list[$i] .= $role;
  }

  if($ROOM->IsOption('sudden_death')){ //虚弱体質村
    $stack = array_diff($ROLE_DATA->sub_role_group_list['sudden-death'],
			array('febris', 'frostbite', 'death_warrant', 'panelist'));
    //PrintData($stack, 'SuddenDeath');
    $delete_role_list = array_merge($delete_role_list, $stack);
    for($i = 0; $i < $user_count; $i++){ //全員に小心者系を何かつける
      $role = GetRandom($stack);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience') $stack = array_diff($stack, array('impatience')); //短気は一人だけ
    }
  }
  elseif($ROOM->IsOption('perverseness')){ //天邪鬼村
    $role = 'perverseness';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++) $fix_role_list[$i] .= ' ' . $role;
  }

  //ババ抜き村
  if($ROOM->IsOption('joker')) $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' joker[2]';

  if($is_chaos && ! $ROOM->IsOption('no_sub_role')){
    //ランダムなサブ役職のコードリストを作成
    if($ROOM->IsOption('sub_role_limit_easy'))
       $sub_role_keys = $CAST_CONF->chaos_sub_role_limit_easy_list;
    elseif($ROOM->IsOption('sub_role_limit_normal'))
       $sub_role_keys = $CAST_CONF->chaos_sub_role_limit_normal_list;
    elseif($ROOM->IsOption('sub_role_limit_hard'))
       $sub_role_keys = $CAST_CONF->chaos_sub_role_limit_hard_list;
    else
      $sub_role_keys = array_keys($ROLE_DATA->sub_role_list);
    //PrintData($delete_role_list, 'DeleteRoleList');

    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    //PrintData($sub_role_keys, 'SubRoleList');
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count - 1) break; //$rand_keys_index は 0 から
      $i = $rand_keys[$rand_keys_index++];
      if(! in_array($i, $roled_list)) $fix_role_list[$i] .= ' ' . $key;
    }
  }

  if($is_quiz){ //クイズ村
    $role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //出題者以外に解答者をつける
      if($fix_uname_list[$i] != 'dummy_boy') $fix_role_list[$i] .= ' ' . $role;
    }
  }
  /*
  if($ROOM->IsOption('festival')){ //お祭り村 (内容は管理人が自由にカスタムする)
    $role = 'nervy';
    for($i = 0; $i < $user_count; $i++){ //全員に自信家をつける
      $fix_role_list[$i] .= ' ' . $role;
    }
  }
  */
  //テスト用
  //PrintData($fix_uname_list); PrintData($fix_role_list); $ROOM->DeleteVote(); return false;

  //役割をDBに更新
  $role_count_list = array();
  $detective_list  = array();
  if($ROOM->IsOption('joker')) $role_count_list['joker'] = 1; //joker[2] 対策
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $user = $USERS->ByUname($fix_uname_list[$i]);
    $user->ChangeRole($role);
    $stack = explode(' ', $role);
    foreach($stack as $role) $role_count_list[$role]++;
    if($is_detective && in_array('detective_common', $stack)) $detective_list[] = $user;
  }

  //KICK の後処理
  $user_no = 1;
  foreach($USERS->rows as $user){
    if($user->user_no != $user_no) $user->Update('user_no', $user_no);
    $user_no++;
  }
  foreach($USERS->kicked as $user) $user->Update('user_no', '-1');

  //役割リスト通知
  if($is_chaos){
    $sentence = $ROOM->IsOptionGroup('chaos_open_cast') ?
      GenerateRoleNameList($role_count_list) : $MESSAGE->chaos;
  }
  else{
    $sentence = GenerateRoleNameList($role_count_list);
  }

  //ゲーム開始
  $ROOM->date++;
  $ROOM->day_night = $ROOM->IsOption('open_day') ? 'day' : 'night';
  if(! $ROOM->test_mode){
    $query = "UPDATE room SET date = {$ROOM->date}, day_night = '{$ROOM->day_night}', " .
      "status = 'playing', start_time = NOW() WHERE room_no = {$ROOM->id}";
    SendQuery($query);
    //OutputSiteSummary(); //RSS機能はテスト中
  }
  $ROOM->Talk($sentence);
  if($is_detective && count($detective_list) > 0){ //探偵村の指名
    $detective_user = GetRandom($detective_list);
    $ROOM->Talk('探偵は ' . $detective_user->handle_name . ' さんです');
    if($ROOM->IsOption('gm_login') && $ROOM->IsOption('not_open_cast') && $user_count > 7){
      $detective_user->ToDead(); //霊界探偵モードなら探偵を霊界に送る
    }
  }
  if($ROOM->test_mode) return true;

  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //処刑投票カウントを初期化 (再投票で増える)
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  $ROOM->DeleteVote(); //今までの投票を全部削除
  CheckVictory(); //配役時に勝敗が決定している可能性があるので勝敗判定を行う
  return true;
}

//昼の投票処理
function VoteDay(){
  global $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //コマンドチェック

  $target = $USERS->ByReal($RQ_ARGS->target_no); //投票先のユーザ情報を取得
  if($target->uname == '') OutputVoteResult('処刑：投票先が指定されていません');
  if($target->IsSelf())    OutputVoteResult('処刑：自分には投票できません');
  if(! $target->IsLive())  OutputVoteResult('処刑：生存者以外には投票できません');

  //特殊イベントを取得
  $vote_duel = property_exists($ROOM->event, 'vote_duel') ? $ROOM->event->vote_duel : NULL;
  if(is_array($vote_duel) && ! in_array($RQ_ARGS->target_no, $vote_duel)){
    OutputVoteResult('処刑：決選投票対象者以外には投票できません');
  }

  //投票済みチェック
  if($ROOM->test_mode){
    if(array_key_exists($SELF->uname, $RQ_ARGS->TestItems->vote->day)){
      PrintData($SELF->uname, 'AlreadyVoted');
      return false;
    }
  }
  else{
    $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
      "AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
    if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');
  }

  //-- 投票処理 --//
  $vote_number = 1; //投票数を初期化

  //メイン役職の補正
  $ROLES->actor = $SELF; //投票者をセット
  foreach($ROLES->Load('vote_do_main') as $filter) $filter->FilterVoteDo($vote_number);

  //サブ役職の補正
  if(! $ROOM->IsEvent('no_authority')){ //蜃気楼ならスキップ
    $ROLES->actor = $USERS->ByVirtual($SELF->user_no); //仮想投票者をセット
    foreach($ROLES->Load('vote_do_sub') as $filter) $filter->FilterVoteDo($vote_number);
  }

  if($ROOM->IsEvent('hyper_random_voter')) $vote_number += mt_rand(0, 5); //天候補正
  if($vote_number < 0) $vote_number = 0; //マイナス補正

  if(! $SELF->Vote('VOTE_KILL', $target->uname, $vote_number)){ //投票処理
    OutputVoteResult('データベースエラー');
  }

  //システムメッセージ
  if($ROOM->test_mode) return true;
  $ROOM->Talk("VOTE_DO\t" . $USERS->GetHandleName($target->uname, true), $SELF->uname);

  AggregateVoteDay(); //集計処理
  OutputVoteResult('投票完了');
}

//昼の投票集計処理
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS;

  //-- 投票処理実行判定 --//
  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //コマンドチェック
  $user_list = $USERS->GetLivingUsers(); //生きているユーザを取得
  if($ROOM->LoadVote() != count($user_list)) return false; //投票数と照合

  //-- 初期化処理 --//
  $live_uname_list   = array(); //生存者リスト (ユーザ名)
  $vote_message_list = array(); //システムメッセージ用 (ユーザID => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $pharmacist_list   = array(); //薬師系の鑑定結果
  if($ROOM->IsOption('joker')) $joker_id = $USERS->SetJoker(); //現在のジョーカー所持者の ID

  //-- 投票データ収集 --//
  foreach($ROOM->vote as $uname => $list){ //初期得票データを収集
    $target_uname = $USERS->ByVirtualUname($list['target_uname'])->uname;
    if(! array_key_exists($target_uname, $vote_count_list)) $vote_count_list[$target_uname] = 0;
    $vote_count_list[$target_uname] += $list['vote_number'];
  }
  //PrintData($vote_count_list, 'VoteCountBase');

  foreach($user_list as $uname){ //個別の投票データを収集
    $list   = $ROOM->vote[$uname]; //投票データ
    $user   = $USERS->ByVirtualUname($uname); //仮想ユーザを取得
    $target = $USERS->ByVirtualUname($list['target_uname']); //投票先の仮想ユーザ
    $vote_number  = (int)$list['vote_number']; //投票数
    $voted_number = array_key_exists($user->uname, $vote_count_list) ?
      (int)$vote_count_list[$user->uname] : 0; //得票数

    $ROLES->actor = $user; //得票者をセット
    //メイン役職の得票補正
    foreach($ROLES->Load('voted_main') as $filter) $filter->FilterVoted($voted_number);

    //サブ役職の得票補正
    if(! $ROOM->IsEvent('no_authority')){ //蜃気楼ならスキップ
      foreach($ROLES->Load('voted_sub') as $filter) $filter->FilterVoted($voted_number);
      //if($user->IsRole('critical_luck')) $voted_number += 100; //テスト用 (痛恨強制発動)
    }
    if($voted_number < 0) $voted_number = 0; //マイナス補正

    //システムメッセージ用の配列を生成
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);

    //リストにデータを追加
    $live_uname_list[$user->user_no]   = $user->uname;
    $vote_message_list[$user->user_no] = $message_list;
    $vote_target_list[$user->uname]    = $target->uname;
    $vote_count_list[$user->uname]     = $voted_number;
    if($USERS->ByReal($user->user_no)->IsRole('philosophy_wizard')){ //賢者の魔法発動
      $user->virtual_role = $ROLES->Load('main_role', true)->GetRole();
      $ROLES->actor = new User($user->virtual_role);
      $ROLES->actor->uname = $user->uname;
    }
    foreach($ROLES->Load('vote_ability') as $filter) $filter->SetVoteDay($target->uname);
  }
  $ROLES->stack->target = $vote_target_list;
  //PrintData($ROLES->stack, 'RoleStack');
  ksort($vote_message_list);
  $stack = array();
  foreach($vote_message_list as $id => $list) $stack[$USERS->ByID($id)->uname] = $list;
  $vote_message_list = $stack;
  //PrintData($vote_message_list, 'VoteMessage');

  //-- 反逆者の処理 --//
  //PrintData($vote_count_list, 'VoteCount');
  if(! $ROOM->IsEvent('no_authority')){ //蜃気楼ならスキップ
    foreach($ROLES->LoadFilter('rebel') as $filter){
      $filter->FilterRebel($vote_message_list, $vote_count_list);
    }
  }

  //-- 投票結果登録 --//
  $max_voted_number = 0; //最多得票数
  foreach($live_uname_list as $uname){ //タブ区切りのデータをシステムメッセージに登録
    extract($vote_message_list[$uname]); //配列を展開
    if($voted_number > $max_voted_number) $max_voted_number = $voted_number; //最大得票数を更新

    //(誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $USERS->GetHandleName($uname) . "\t" . $target . "\t" .
      $voted_number ."\t" . $vote_number . "\t" . $RQ_ARGS->vote_times;
    if(! $ROOM->test_mode) $ROOM->SystemMessage($sentence, 'VOTE_KILL');
  }

  //-- 処刑者決定処理 --//
  $vote_kill_uname = ''; //処刑者 (ユーザ名)
  //最大得票数のユーザ名 (処刑候補者) のリストを取得
  $ROLES->stack->max_voted = array_keys($vote_count_list, $max_voted_number);
  //PrintData($ROLES->stack->max_voted, 'MaxVoted');
  if(count($ROLES->stack->max_voted) == 1){ //一人だけなら決定
    $vote_kill_uname = array_shift($ROLES->stack->max_voted);
  }
  else{ //決定能力者判定
    $ROLES->stack->vote_possible = $ROLES->stack->max_voted;
    foreach($ROLES->LoadFilter('vote_kill') as $filter) $filter->DecideVoteKill($vote_kill_uname);
  }
  $ROLES->stack->vote_kill_uname = $vote_kill_uname;
  //PrintData($vote_kill_uname, 'VoteTarget');

  if($vote_kill_uname != ''){ //-- 処刑実行処理 --//
    //-- 処刑者情報収取 --//
    $vote_target = $USERS->ByRealUname($vote_kill_uname); //ユーザ情報を取得
    $USERS->Kill($vote_target->user_no, 'VOTE_KILLED'); //処刑処理
    //処刑者を生存者リストから除く
    unset($live_uname_list[array_search($vote_kill_uname, $live_uname_list)]);
    $voter_list = array_keys($vote_target_list, $vote_target->uname); //投票した人を取得

    foreach($ROLES->LoadFilter('distinguish_poison') as $filter){ //薬師系の情報収集
      $filter->DistinguishPoison($pharmacist_list);
    }

    do{ //-- 処刑者の毒処理 --//
      if(! $vote_target->IsPoison()) break; //毒能力の発動判定

      //薬師系の解毒判定 (夢毒者は対象外)
      $ROLES->actor = $USERS->ByVirtual($vote_target->user_no); //投票データは仮想ユーザ
      if(! $vote_target->IsRole('dummy_poison')){
	foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_list);
	if($ROLES->actor->detox_flag) break;
      }

      $poison_target_list = array(); //毒の対象リスト

      //毒の対象オプションをチェックして初期候補者リストを作成
      $stack = $GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list;
      foreach($stack as $uname){ //常時対象外の役職を除く
	$user = $USERS->ByRealUname($uname);
	if($user->IsLive(true) && ! $user->IsAvoid(true)) $poison_target_list[] = $uname;
      }

      //特殊毒の場合はターゲットが限定される
      if($ROLES->actor->alchemy_flag || $ROOM->IsEvent('alchemy_pharmacist')){ //錬金術師
	$ROLES->LoadMain(new User('alchemy_pharmacist'))->FilterPoisonTarget($poison_target_list);
      }
      else{
	$ROLES->actor = $vote_target;
	foreach($ROLES->Load('poison') as $filter) $filter->FilterPoisonTarget($poison_target_list);
      }
      if(count($poison_target_list) < 1) break;
      $poison_target = $USERS->ByRealUname(GetRandom($poison_target_list)); //対象者を決定

      if($poison_target->IsActive('resist_wolf')){ //抗毒判定
	$poison_target->LostAbility();
	break;
      }
      $USERS->Kill($poison_target->user_no, 'POISON_DEAD_day'); //死亡処理

      //-- 連毒者の処理 --//
      if(! $poison_target->IsRole('chain_poison')) break; //連毒者判定
      $ROLES->actor = $USERS->ByVirtual($poison_target->user_no); //解毒判定
      foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_list);
      if($ROLES->actor->detox_flag) break;

      $target_stack = array();
      foreach($USERS->GetLivingUsers(true) as $uname){ //生存者から常時対象外の役職を除く
	$user = $USERS->ByRealUname($uname);
	if(! $user->IsAvoid(true)) $target_stack[] = $user->user_no;
      }
      //PrintData($target_stack, 'BaseChainPoisonTarget');

      $chain_count = 1; //連鎖カウントを初期化
      while($chain_count > 0){
	$chain_count--;
	shuffle($target_stack); //配列をシャッフル
	for($i = 0; $i < 2; $i++){
	  if(count($target_stack) < 1) break 2;
	  $id = array_shift($target_stack);
	  $target = $USERS->ByReal($id);

	  if($target->IsActive('resist_wolf')){ //抗毒判定
	    $target->LostAbility();
	    $target_stack[] = $id;
	    continue;
	  }
	  $USERS->Kill($id, 'POISON_DEAD_day'); //死亡処理

	  if(! $target->IsRole('chain_poison')) continue; //連鎖判定
	  $ROLES->actor = $USERS->ByVirtual($target->user_no); //解毒判定
	  foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_list);
	  if(! $ROLES->actor->detox_flag) $chain_count++;
	}
      }
    }while(false);
    //PrintData($pharmacist_list, 'EndDetox');

    //-- 処刑者カウンター処理 --//
    $ROLES->actor = $vote_target;
    foreach($ROLES->Load('vote_kill_counter') as $filter) $filter->VoteKillCounter($voter_list);

    //-- 特殊投票発動者の処理 --//
    foreach($ROLES->LoadFilter('vote_action') as $filter) $filter->VoteAction();

    //-- 霊能者系の処理 --//
    $result_header = $USERS->GetHandleName($vote_target->uname, true) . "\t";
    //火車の妨害判定
    $stolen_flag = $ROOM->IsEvent('corpse_courier_mad') ||
      (property_exists($vote_target, 'stolen_flag') && $vote_target->stolen_flag);

    $role_flag   = new StdClass();
    $wizard_flag = new StdClass();
    $stack = array('', 'soul_', 'psycho_', 'embalm_', 'emissary_', 'dummy_');
    foreach($stack as $header){ //対象役職を初期化
      $role = $header . 'necromancer';
      $role_flag->$role   = false;
      $wizard_flag->$role = false;
    }
    foreach($USERS->rows as $user) $role_flag->{$user->main_role} = true; //役職出現判定
    //PrintData($role_flag, 'ROLE_FLAG');
    $role = 'spiritism_wizard';
    if(property_exists($role_flag, $role) && ! $ROOM->IsEvent('new_moon')){ //交霊術師の処理
      $filter = $ROLES->LoadMain(new User($role));
      $wizard_flag->{$filter->GetRole()} = true;
      $wizard_action = 'SPIRITISM_WIZARD_RESULT';
      if(property_exists($wizard_flag, 'sex_necromancer')){
	$result = $filter->Necromancer($vote_target, $stolen_flag);
	$ROOM->SystemMessage($result_header . $result, $wizard_action);
      }
    }

    foreach($stack as $header){
      $role = $header . 'necromancer';
      if($role_flag->$role || $wizard_flag->$role){
	$result = $ROLES->LoadMain(new User($role))->Necromancer($vote_target, $stolen_flag);
	if(is_null($result)) continue;
	$str = $result_header . $result;
	if($role_flag->$role)   $ROOM->SystemMessage($str, strtoupper($role . '_result'));
	if($wizard_flag->$role) $ROOM->SystemMessage($str, $wizard_action);
      }
    }
  }

  //-- 得票カウンター処理 --//
  foreach($ROLES->LoadFilter('voted_reaction') as $filter) $filter->VotedReaction();

  //-- ショック死処理 --//
  //判定用データを登録
  $ROLES->stack->count = array_count_values($vote_target_list); //投票者対象ユーザ名 => 人数
  //PrintData($ROLES->stack->count, 'count');

  $thunderbolt_list = array(); //青天の霹靂判定用
  if($ROOM->IsEvent('thunderbolt')){
    $stack = array();
    foreach($user_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if($user->IsLive(true) && ! $user->IsAvoid(true)) $stack[] = $user->user_no;
    }
    //PrintData($stack, 'ThunderboltBase');
    $thunderbolt_list[] = $USERS->ByVirtual(GetRandom($stack))->uname;
    //PrintData($thunderbolt_list, 'ThunderboltTarget');
  }

  foreach($live_uname_list as $uname){
    $ROLES->actor = $USERS->ByUname($uname); //$live_uname_list は仮想ユーザ名
    $ROLES->actor->cured_flag = false;
    $reason = in_array($uname, $thunderbolt_list) ? 'THUNDERBOLT' : '';
    if(! $ROOM->IsEvent('no_sudden_death')){ //凪ならスキップ
      foreach($ROLES->Load('sudden_death_sub') as $filter) $filter->FilterSuddenDeath($reason);
    }
    foreach($ROLES->Load('sudden_death_main') as $filter) $filter->FilterSuddenDeath($reason);
    if($reason == '') continue;

    //薬師系の治療判定
    foreach($ROLES->LoadFilter('cure') as $filter) $filter->Cure($pharmacist_list);
    if(! $ROLES->actor->cured_flag){
      $USERS->SuddenDeath($ROLES->actor->user_no, 'SUDDEN_DEATH_' . $reason);
    }
  }

  foreach($ROLES->LoadFilter('followed') as $filter) $filter->Followed($user_list); //道連れ処理

  foreach($pharmacist_list as $uname => $result){ //薬師系の鑑定結果を登録
    $user = $USERS->ByUname($uname);
    $target_uname = $ROLES->stack->{$user->GetMainRole(true)}[$user->uname];
    $handle_name  = $USERS->GetHandleName($target_uname, true);
    $sentence = $user->handle_name . "\t" . $handle_name . "\t" . $result;
    $ROOM->SystemMessage($sentence, 'PHARMACIST_RESULT');
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  if($vote_kill_uname != ''){ //夜に切り替え
    //-- 処刑得票カウンターの処理 --//
    foreach($ROLES->LoadFilter('vote_kill_reaction') as $filter) $filter->VoteKillReaction();

    if($ROOM->IsEvent('frostbite')){ //-- 雪の処理 --//
      $stack = array();
      foreach($user_list as $uname){
	$user = $USERS->ByRealUname($uname);
	if($user->IsLive(true) && ! $user->IsAvoid(true)) $stack[] = $user->user_no;
      }
      //PrintData($stack, 'Target [frostbite]');
      $USERS->ByID(GetRandom($stack))->AddDoom(1, 'frostbite');
    }
    elseif($ROOM->IsEvent('psycho_infected')){ //-- 濃霧の処理 --//
      $stack = array();
      foreach($user_list as $uname){
	$user = $USERS->ByRealUname($uname);
	if($user->IsLive(true) && ! $user->IsAvoid(true) && ! $user->IsRole('psycho_infected') &&
	   ! $user->IsCamp('vampire')) $stack[] = $user->user_no;
      }
      //PrintData($stack, 'Target [psycho_infected]');
      $USERS->ByID(GetRandom($stack))->AddRole('psycho_infected');
    }

    $joker_flag = ! $ROOM->IsOption('joker'); //ジョーカー移動成立フラグ
    if(! $joker_flag){ //ジョーカー移動判定
      $joker_user   = $USERS->ByID($joker_id); //現在の所持者を取得
      $virtual_user = $USERS->ByVirtual($joker_user->user_no); //仮想ユーザを取得

      $joker_target_uname = $vote_target_list[$virtual_user->uname]; //ジョーカーの投票先
      $joker_voted_list   = array_keys($vote_target_list, $virtual_user->uname); //ジョーカー投票者
      $joker_target_list  = array(); //移動可能者リスト
      foreach($joker_voted_list as $voter_uname){
	$voter = $USERS->ByRealUname($voter_uname);
	if($voter->IsLive(true) && ! $voter->IsJoker($ROOM->date - 1)){ //死者と前日所持者を除外
	  $joker_target_list[] = $voter_uname;
	}
      }
      //PrintData($joker_voted_list, $joker_target_uname);
      //PrintData($joker_target_list, 'Target[joker]');

      do{ //移動判定ルーチン
	//対象者か現在のジョーカー所持者が処刑者なら無効
	if($joker_target_uname == $vote_kill_uname || $joker_user->IsSame($vote_kill_uname)) break;

	if(in_array($joker_target_uname, $joker_voted_list)){ //相互投票なら無効
	  //複数から投票されていた場合は残りからランダム
	  unset($joker_target_list[array_search($joker_target_uname, $joker_target_list)]);
	  //PrintData($joker_target_list, 'ReduceTarget');
	  if(count($joker_target_list) == 0) break;
	  $joker_target_uname = GetRandom($joker_target_list);
	}
	elseif($USERS->ByRealUname($joker_target_uname)->IsDead(true)){ //対象者が死亡していた場合
	  if(count($joker_target_list) == 0) break;
	  $joker_target_uname = GetRandom($joker_target_list); //ジョーカー投票者から選出
	}
	$USERS->ByRealUname($joker_target_uname)->AddJoker();
	$joker_flag = true;
      }while(false);
    }

    $ROOM->ChangeNight();
    if(CheckVictory()){
      if(! $joker_flag){ //ゲーム終了時のみ、処刑先への移動許可 (それ以外なら本人継承)
	($joker_target_uname == $vote_kill_uname && ! $joker_user->IsSame($vote_kill_uname)) ?
	  $USERS->ByRealUname($joker_target_uname)->AddJoker() : $joker_user->AddJoker();
      }
    }
    else{
      if(! $joker_flag){
	//生きていたら本人継承 / 処刑者なら前日所持者以外の投票者ランダム / 死亡なら完全ランダム
	if($joker_user->IsLive(true))
	  $joker_user->AddJoker();
	elseif($joker_user->IsSame($vote_kill_uname) && count($joker_target_list) > 0)
	  $USERS->ByRealUname(GetRandom($joker_target_list))->AddJoker();
	else
	  $USERS->ByRealUname(GetRandom($USERS->GetLivingUsers(true)))->AddJoker();
      }
      InsertRandomMessage(); //ランダムメッセージ
    }
    if($ROOM->test_mode) return $vote_message_list;
    $ROOM->SkipNight();
  }
  else{ //再投票処理
    if($ROOM->test_mode) return $vote_message_list;
    $next_vote_times = $RQ_ARGS->vote_times + 1; //投票回数を増やす
    $query = 'UPDATE system_message SET message = ' . $next_vote_times . $ROOM->GetQuery() .
      " AND type = 'VOTE_TIMES'";
    SendQuery($query);

    //システムメッセージ
    $ROOM->SystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    $ROOM->Talk("再投票になりました( {$RQ_ARGS->vote_times} 回目)");
    //勝敗判定＆ジョーカー処理
    if(CheckVictory(true) && $ROOM->IsOption('joker')) $USERS->ByID($joker_id)->AddJoker();
  }
  $ROOM->UpdateTime(true); //最終書き込み時刻を更新
}

//夜の投票の基礎チェック
function CheckVoteNight(){
  global $ROOM, $ROLES, $SELF;

  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  foreach(array('', 'not_') as $header){   //データを初期化
    foreach(array('action', 'submit') as $data) $ROLES->stack->{$header . $data} = NULL;
  }
  if($SELF->IsDoomRole('death_note')){ //デスノート
    /*
      配役設定上、初日に配布されることはなく、バグで配布された場合でも
      集計処理は実施されないので、ここではそのまま投票させておく。
      逆にスキップ判定を実施した場合、初日投票能力者が詰む。
    */
    //if($ROOM->date == 1) OutputVoteResult('夜：初日は暗殺できません');
    if($ROOM->test_mode || ! CheckSelfVoteNight('DEATH_NOTE_DO', 'DEATH_NOTE_NOT_DO')){
      $filter = $ROLES->LoadMain(new User('mage')); //上記のバグ対策用 (本来は assassin 相当)
      $ROLES->actor->uname = $SELF->uname; //同一ユーザ判定用
      $ROLES->stack->action     = 'DEATH_NOTE_DO';
      $ROLES->stack->not_action = 'DEATH_NOTE_NOT_DO';
      $death_note = true;
    }
  }
  if(! $death_note){
    $filter = $ROLES->LoadMain($SELF);
    $filter->SetVoteNight();
  }
  return $filter;
}

//夜の投票ページを出力する
function OutputVoteNight(){
  global $VOTE_MESS, $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  CheckScene(); //投票シーンチェック
 //-- 投票済みチェック --//
  $filter = CheckVoteNight();
  if(! $ROOM->test_mode) CheckAlreadyVote($ROLES->stack->action, $ROLES->stack->not_action);

  OutputVotePageHeader();
  //PrintData($filter);
  //PrintData($ROLES->stack);
  echo '<table class="vote-page"><tr>'."\n";
  $count = 0;
  foreach($filter->GetVoteTargetUser() as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    $live = $USERS->IsVirtualLive($id);
    /*
      死者は死亡アイコン (蘇生能力者は死亡アイコンにしない)
      生存者はユーザアイコン (狼仲間なら狼アイコン)
    */
    $path     = $filter->GetVoteIconPath($user, $live);
    $checkbox = $filter->GetVoteCheckbox($user, $id, $live);
    echo $user->GenerateVoteTag($path, $checkbox);
  }

  if(is_null($ROLES->stack->submit)) $ROLES->stack->submit = strtolower($ROLES->stack->action);
  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<input type="hidden" name="situation" value="{$ROLES->stack->action}">
<td><input type="submit" value="{$VOTE_MESS->{$ROLES->stack->submit}}"></td></form>

EOF;

  if(isset($ROLES->stack->not_action)){
    if(is_null($ROLES->stack->not_submit)){
      $ROLES->stack->not_submit = strtolower($ROLES->stack->not_action);
    }
    echo <<<EOF
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$ROLES->stack->not_action}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
<input type="submit" value="{$VOTE_MESS->{$ROLES->stack->not_submit}}"></form>
</td>

EOF;
  }

  echo <<<EOF
</tr></table></div>
</body></html>

EOF;
}

//夜の投票処理
function VoteNight(){
  global $RQ_ARGS, $ROOM, $ROLES, $SELF;

  //-- イベント名と役職の整合チェック --//
  $filter = CheckVoteNight();
  if(empty($RQ_ARGS->situation))
    OutputVoteResult('夜：投票イベントが空です');
  elseif($RQ_ARGS->situation == $ROLES->stack->not_action)
    $not_action = true;
  elseif($RQ_ARGS->situation != $ROLES->stack->action)
    OutputVoteResult('夜：投票イベントが一致しません');
  else
    $not_action = false;
  //PrintData($filter);
  if(! $ROOM->test_mode) CheckAlreadyVote($RQ_ARGS->situation); //投票済みチェック

  //-- 投票処理 --//
  if($not_action){ //投票キャンセルタイプは何もしない
    if(! $SELF->Vote($RQ_ARGS->situation)) OutputVoteResult('データベースエラー'); //投票処理
    $ROOM->SystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation, $SELF->uname);
  }
  else{
    $filter->CheckVoteNight();
    //PrintData($ROLES->stack);
    if(! $SELF->Vote($RQ_ARGS->situation, $ROLES->stack->target_uname)){
      OutputVoteResult('データベースエラー'); //投票処理
    }
    $str = $SELF->handle_name . "\t" . $ROLES->stack->target_handle;
    $ROOM->SystemMessage($str, $ROLES->stack->message);
    $ROOM->Talk($ROLES->stack->message . "\t" . $ROLES->stack->target_handle, $SELF->uname);
  }
  if($ROOM->test_mode) return;
  AggregateVoteNight(); //集計処理
  OutputVoteResult('投票完了');
}

//夜の集計処理
function AggregateVoteNight($skip = false){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  $ROOM->LoadVote(); //投票情報を取得
  //PrintData($ROOM->vote, 'VoteRow');

  $vote_data = $ROOM->ParseVote(); //コマンド毎に分割
  //PrintData($vote_data, 'VoteData');

  if(! $skip){
    foreach($USERS->rows as $user){ //未投票チェック
      if($user->CheckVote($vote_data) === false){
	//PrintData($user->uname, $user->main_role); //テスト用
	return false;
      }
    }
  }

  //処理対象コマンドチェック
  $stack = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'MIND_SCANNER_DO', 'JAMMER_MAD_DO',
		 'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  if($ROOM->date == 1){
    $stack[] = 'MANIA_DO';
  }
  else{
    array_push($stack, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'POISON_CAT_DO', 'ASSASSIN_DO',
	       'WIZARD_DO', 'SPREAD_WIZARD_DO', 'ESCAPE_DO', 'DREAM_EAT', 'TRAP_MAD_DO',
	       'POSSESSED_DO', 'VAMPIRE_DO', 'OGRE_DO', 'DEATH_NOTE_DO');
  }
  foreach($stack as $action){
    if(! array_key_exists($action, $vote_data)) $vote_data[$action] = array();
  }
  //PrintData($vote_data);

  //-- 変数の初期化 --//
  $ROLES->stack->trap                = array(); //罠師の罠の設置先
  $ROLES->stack->trapped             = array(); //罠死予定者
  $ROLES->stack->snow_trap           = array(); //雪女の罠の設置先
  $ROLES->stack->frostbite           = array(); //凍傷予定者
  $ROLES->stack->guard               = array(); //狩人系の護衛対象
  $ROLES->stack->gatekeeper_guard    = array(); //門番の護衛対象
  $ROLES->stack->dummy_guard         = array(); //夢守人の護衛対象
  $ROLES->stack->spread_wizard       = array(); //結界師の護衛対象
  $ROLES->stack->escaper             = array(); //逃亡者の逃亡先
  $ROLES->stack->sacrifice           = array(); //身代わり死した人
  $ROLES->stack->anti_voodoo         = array(); //厄神の護衛対象
  $ROLES->stack->anti_voodoo_success = array(); //厄払い成功者
  $ROLES->stack->reverse_assassin    = array(); //反魂師の対象
  $ROLES->stack->possessed           = array(); //憑依予定者 => 憑依成立フラグ

  //-- 天候の処理 --//
  $stack = array();
  if($ROOM->IsEvent('full_moon')){ //満月
    array_push($stack, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'JAMMER_MAD_DO',
	       'VOODOO_MAD_DO', 'VOODOO_FOX_DO');
  }
  elseif($ROOM->IsEvent('new_moon')){ //新月
    $skip = true; //影響範囲に注意
    array_push($stack, 'MAGE_DO', 'VOODOO_KILLER_DO', 'WIZARD_DO', 'SPREAD_WIZARD_DO',
	       'CHILD_FOX_DO', 'VAMPIRE_DO', 'FAIRY_DO');
  }
  elseif($ROOM->IsEvent('no_contact')){ //花曇 (さとり系に注意)
    $skip = true; //影響範囲に注意
    array_push($stack, 'REPORTER_DO', 'ASSASSIN_DO', 'MIND_SCANNER_DO', 'ESCAPE_DO',
	       'TRAP_MAD_DO', 'VAMPIRE_DO', 'OGRE_DO');
  }
  elseif($ROOM->IsEvent('no_trap')){ //雪明り
    $stack[] = 'TRAP_MAD_DO';
  }
  elseif($ROOM->IsEvent('no_dream')){ //熱帯夜
    $stack[] = 'DREAM_EAT';
  }
  foreach($stack as $action) $vote_data[$action] = array();

  //-- 魔法使い系の振り替え処理 --//
  if($ROOM->date > 1){
    foreach($vote_data['WIZARD_DO'] as $uname => $target_uname){
      list($role, $action) = $ROLES->LoadMain($USERS->ByUname($uname))->GetRole();
      $ROLES->actor->virtual_role = $role; //仮想役職を登録
      //PrintData($role, "Wizard: {$uname}: {$action}");
      $vote_data[$action][$uname] = $target_uname;
    }
  }
  $ROLES->stack->vote_data = $vote_data;

  //-- 接触系レイヤー --//
  $voted_wolf  = new User();
  $wolf_target = new User();
  foreach($vote_data['WOLF_EAT'] as $uname => $target_uname){ //人狼の襲撃情報取得
    $voted_wolf  = $USERS->ByUname($uname);
    $wolf_target = $USERS->ByUname($target_uname);
  }
  $ROLES->stack->voted_wolf  = $voted_wolf;
  $ROLES->stack->wolf_target = $wolf_target;

  if($ROOM->date > 1){
    foreach($vote_data['TRAP_MAD_DO'] as $uname => $target_uname){ //罠能力者の設置処理
      $ROLES->LoadMain($USERS->ByUname($uname))->SetTrap($target_uname);
    }

    //狡狼の自動罠設置判定 (花曇・雪明りは無効)
    if($ROOM->date > 2 && ! $ROOM->IsEvent('no_contact') && ! $ROOM->IsEvent('no_trap')){
      foreach($USERS->rows as $user){
	if($user->IsLiveRole('trap_wolf')) $ROLES->LoadMain($user)->SetTrap($user->uname);
      }
    }

    if(count($ROLES->stack->trap) > 0) $ROLES->SetClass('trap_mad');
    foreach($ROLES->LoadFilter('trap') as $filter) $filter->TrapToTrap(); //罠能力者の罠判定
    //PrintData($ROLES->stack->trap, 'Target [trap]');
    //PrintData($ROLES->stack->trapped, 'Trap [trap]');

    $half_guard = $ROOM->IsEvent('half_guard'); //曇天
    foreach($vote_data['GUARD_DO'] as $uname => $target_uname){ //狩人系の護衛先をセット
      $ROLES->LoadMain($USERS->ByUname($uname))->SetGuard($target_uname);
    }
    if(count($ROLES->stack->guard) > 0) $ROLES->SetClass('guard');
    //PrintData($ROLES->stack->guard, 'Target [guard]');

    foreach($vote_data['SPREAD_WIZARD_DO'] as $uname => $target_list){ //結界師の情報収集
      $ROLES->LoadMain($USERS->ByUname($uname))->SetGuard($target_list);
    }
    //PrintData($ROLES->stack->barrier_wizard, 'Target [wizard]');

    foreach($vote_data['ESCAPE_DO'] as $uname => $target_uname){ //逃亡者系の情報収集
      $ROLES->LoadMain($USERS->ByUname($uname))->Escape($USERS->ByUname($target_uname));
    }
    //PrintData($ROLES->stack->escaper, 'Target [escaper]');
  }

  do{ //人狼の襲撃成功判定
    if($skip || $ROOM->IsQuiz()) break; //スキップモード・クイズ村仕様

    if(! $voted_wolf->IsSiriusWolf(false)){ //罠判定 (覚醒天狼は無効)
      foreach($ROLES->LoadFilter('trap') as $filter){
	if($filter->TrapStack($voted_wolf, $wolf_target->uname)) break 2;
      }
    }

    //逃亡者の巻き添え判定
    foreach(array_keys($ROLES->stack->escaper, $wolf_target->uname) as $uname){
      $USERS->Kill($USERS->UnameToNumber($uname), 'WOLF_KILLED'); //死亡処理
    }

    //狩人系の護衛判定
    foreach($ROLES->LoadFilter('guard') as $filter) $filter->GetGuard($wolf_target->uname, $stack);
    //PrintData($stack, 'List [gurad]');

    if(count($stack) > 0){
      $guard_flag = false; //護衛成功フラグ
      //護衛制限判定
      $guard_limited = ! $ROOM->IsEvent('full_guard') && $wolf_target->IsGuardLimited();
      foreach($stack as $uname){
	$user   = $USERS->ByUname($uname);
	$filter = $ROLES->LoadMain($user);

	if($flag = $filter->GuardFailed()) continue; //個別護衛成功判定
	$guard_flag |= ! ($half_guard && mt_rand(0, 1) > 0) && (! $guard_limited || is_null($flag));

	$filter->GuardAction($voted_wolf); //護衛処理
	if(! $ROOM->IsOption('seal_message') &&
	   $user->IsFirstGuardSuccess($wolf_target->uname)){ //護衛成功メッセージを登録
	  $ROOM->SystemMessage($user->GetHandleName($wolf_target->uname), 'GUARD_SUCCESS');
	}
      }
      if($guard_flag && ! $voted_wolf->IsSiriusWolf()) break; //護衛成功判定
    }

    $wolf_filter = $ROLES->LoadMain($voted_wolf);
    if(! $wolf_target->IsDummyBoy()){ //特殊能力者判定 (身代わり君は対象外)
      if(! $voted_wolf->IsSiriusWolf()){ //特殊襲撃失敗判定 (サブの判定が先/完全覚醒天狼は無効)
	$ROLES->actor = $wolf_target;
	foreach($ROLES->Load('wolf_eat_resist') as $filter){
	  if($filter->WolfEatResist()) break 2;
	}
	//確率無効タイプ (鬼陣営)
	if($wolf_target->IsOgre() && $ROLES->LoadMain($wolf_target)->WolfEatResist()) break;
      }
      if($ROOM->date > 1 && $wolf_target->IsRoleGroup('escaper')) break; //逃亡者系判定
      if($wolf_filter->WolfEatSkip($wolf_target)) break; //人狼襲撃失敗判定
      if(! $voted_wolf->IsSiriusWolf()){ //特殊能力者の処理 (完全覚醒天狼は無効)
	$ROLES->actor = $wolf_target; //人狼襲撃得票カウンター処理
	foreach($ROLES->Load('wolf_eat_reaction') as $filter){
	  if($filter->WolfEatReaction()) break 2;
	}

	if(! $ROOM->IsEvent('no_sacrifice')){ //身代わり能力者判定
	  $ROLES->actor = $wolf_target;
	  foreach($ROLES->Load('sacrifice') as $filter){
	    $stack = $filter->GetSacrificeList();
	    //PrintData($stack, 'List [Sacrifice]');
	    if(count($stack) > 0){
	      $target = $USERS->ByID(GetRandom($stack));
	      $USERS->Kill($target->user_no, 'SACRIFICE');
	      $ROLES->stack->sacrifice[] = $target->uname;
	      break 2;
	    }
	  }
	}
	if($wolf_filter->WolfEatAction($wolf_target)) break; //人狼襲撃能力処理

	$ROLES->actor = $wolf_target;  //人狼襲撃カウンター処理
	foreach($ROLES->Load('wolf_eat_counter') as $filter) $filter->WolfEatCounter($voted_wolf);
      }
    }

    //-- 襲撃処理 --//
    $wolf_filter->WolfKill($wolf_target, $ROLES->stack->possessed);
    $wolf_target->wolf_killed = true;

    if($wolf_target->IsPoison() && ! $voted_wolf->IsSiriusWolf()){ //-- 毒死判定 --//
      $poison_target = $wolf_filter->GetPoisonTarget(); //対象選出
      if($poison_target->IsChallengeLovers()) break; //難題なら無効

      $ROLES->actor = $wolf_target; //襲撃毒死回避判定
      foreach($ROLES->Load('avoid_poison_eat') as $filter){
	if($filter->AvoidPoisonEat($poison_target)) break 2;
      }
      $ROLES->LoadMain($poison_target)->PoisonDead(); //毒死処理
    }
  }while(false);
  //PrintData($ROLES->stack->possessed, 'Possessed [possessed_wolf]');

  if($ROOM->date > 1){
    foreach($vote_data['DEATH_NOTE_DO'] as $uname => $target_uname){ //デスノートの処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効
      $USERS->Kill($USERS->UnameToNumber($target_uname), 'ASSASSIN_KILLED');
    }

    if(! $ROOM->IsEvent('no_hunt')){ //川霧ならスキップ
      foreach($ROLES->stack->guard as $uname => $target_uname){ //狩人系の狩り判定
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //直前に死んでいたら無効
	$ROLES->LoadMain($user)->Hunt($USERS->ByUname($target_uname));
      }
    }
    foreach($ROLES->LoadFilter('trap') as $filter) $filter->DelayTrapKill(); //罠死処理

    $vampire_target_list = array(); //吸血対象者リスト
    $vampire_killed_list = array(); //吸血死対象者リスト
    foreach($vote_data['VAMPIRE_DO'] as $uname => $target_uname){ //吸血鬼の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      foreach($ROLES->LoadFilter('trap') as $filter){ //罠判定
	if($filter->DelayTrap($user, $target_uname)) continue 2;
      }

      //吸血鬼に逃亡した逃亡者を対象者リストに追加
      foreach(array_keys($ROLES->stack->escaper, $user->uname) as $escaper_uname){
	$vampire_target_list[$user->uname][] = $escaper_uname;
      }
      //逃亡者の巻き添え判定
      foreach(array_keys($ROLES->stack->escaper, $target_uname) as $escaper_uname){
	$vampire_target_list[$user->uname][] = $escaper_uname;
      }
      $target = $USERS->ByUname($target_uname);

      //狩人系の護衛判定
      $guard_flag = false; //護衛成功フラグ
      $guard_limited = ! $ROOM->IsEvent('full_guard') && $target->IsGuardLimited(); //護衛制限判定
      //護衛者を検出
      foreach($ROLES->LoadFilter('guard') as $filter) $filter->GetGuard($target->uname, $stack);
      //PrintData($stack, 'List [gurad/vampire]');

      foreach($stack as $guard_uname){
	$guard_user = $USERS->ByUname($guard_uname);
	if($guard_user->IsDead(true)) continue; //直前に死んでいたら無効

	$filter = $ROLES->LoadMain($guard_user);
	if($flag = $filter->GuardFailed()) continue; //個別護衛成功判定
	$guard_flag |= ! ($half_guard && mt_rand(0, 1) > 0) && (! $guard_limited || is_null($flag));

	$filter->GuardAction($user, true); //護衛処理

	if(! $ROOM->IsOption('seal_message') &&
	   $guard_user->IsFirstGuardSuccess($target->uname)){ //護衛成功メッセージを登録
	  $ROOM->SystemMessage($guard_user->GetHandleName($target->uname), 'GUARD_SUCCESS');
	}
      }

      //スキップ判定
      if($target->IsDead(true) || $guard_flag || $target->IsRoleGroup('escaper')) continue;

      //吸血鬼襲撃判定
      if($target->IsRoleGroup('vampire') ||
	 ($target->IsRole('soul_mania', 'dummy_mania') && $target->IsCamp('vampire'))){
	if($target->IsRole('doom_vampire')) continue; //冥血鬼は無効
	$id = $target->IsRole('soul_vampire') ? $user->user_no : $target->user_no; //吸血姫は反射
	$vampire_killed_list[$id] = true;
      }
      else{
	$vampire_target_list[$user->uname][] = $target->uname;
      }
    }
    //PrintData($vampire_target_list, 'Target [vampire]');
    //PrintData($vampire_killed_list, 'Target [vampire_killed]');

    foreach($ROLES->LoadFilter('trap') as $filter) $filter->DelayTrapKill(); //罠死処理
    foreach($vampire_killed_list as $id => $flag) $USERS->Kill($id, 'VAMPIRE_KILLED'); //吸血死処理
    unset($vampire_killed_list);

    //吸血処理
    foreach($vampire_target_list as $uname => $stack){
      $filter = $ROLES->LoadMain($USERS->ByUname($uname));
      foreach($stack as $target_uname) $filter->Infect($USERS->ByUname($target_uname));
    }
    unset($vampire_target_list);

    $ROLES->stack->assassin = array(); //暗殺対象者リスト
    foreach($vote_data['ASSASSIN_DO'] as $uname => $target_uname){ //暗殺能力者の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      foreach($ROLES->LoadFilter('trap') as $filter){ //罠判定
	if($filter->TrapStack($user, $target_uname)) continue 2;
      }
      foreach($ROLES->LoadFilter('guard_assassin') as $filter){ //門番の護衛判定
	if($filter->GuardAssassin($target_uname)) continue 2;
      }

      $target = $USERS->ByUname($target_uname);
      if($target->IsRoleGroup('escaper')) continue; //逃亡者は無効
      if($target->IsRefrectAssassin()){ //反射判定
	$ROLES->stack->assassin[$user->user_no] = true;
	continue;
      }
      $ROLES->LoadMain($user)->Assassin($target);
    }
    $role = 'assassin'; //暗殺処理
    //PrintData($ROLES->stack->$role, "Target [{$role}]");
    if(count($ROLES->stack->$role) > 0) $ROLES->LoadMain(new User($role))->AssassinKill();
    unset($ROLES->stack->$role);

    $ROLES->stack->ogre = array(); //人攫い対象者リスト
    foreach($vote_data['OGRE_DO'] as $uname => $target_uname){ //鬼の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      foreach($ROLES->LoadFilter('trap') as $filter){ //罠判定
	if($filter->DelayTrap($user, $target_uname)) continue 2;
      }
      foreach($ROLES->LoadFilter('guard_assassin') as $filter){ //門番の護衛判定
	if($filter->GuardAssassin($target_uname)) continue 2;
      }

      $target = $USERS->ByUname($target_uname);
      if($target->IsDead(true) || $target->IsRoleGroup('escaper')) continue; //無効判定
      if($target->IsRefrectAssassin()){ //反射判定
	$ROLES->stack->ogre[$user->user_no] = true;
	continue;
      }
      $ROLES->LoadMain($user)->Assassin($target);
    }
    foreach($ROLES->LoadFilter('trap') as $filter) $filter->DelayTrapKill(); //罠死処理
    $role = 'ogre'; //人攫い処理
    //PrintData($ROLES->stack->$role, "Target [{$role}]");
    if(count($ROLES->stack->$role) > 0) $ROLES->LoadMain(new User($role))->AssassinKill();
    unset($ROLES->stack->$role);

    //オシラ遊びの処理
    $role = 'death_selected';
    foreach($USERS->rows as $user){
      if($user->IsDead(true)) continue;
      $virtual = $USERS->ByVirtual($user->user_no);
      if($virtual->IsRole($role) && $virtual->GetDoomDate($role) == $ROOM->date){
	$USERS->Kill($user->user_no, 'PRIEST_RETURNED');
      }
    }

    $role = 'reverse_assassin'; //反魂師の暗殺処理
    $ROLES->stack->reverse = array(); //反魂対象リスト
    if(count($ROLES->stack->$role) > 0) $ROLES->LoadMain(new User($role))->AssassinKill();
    unset($ROLES->stack->$role);
    //PrintData($ROLES->stack->reverse, 'ReverseList');

    foreach($ROLES->stack->frostbite as $uname){ //凍傷処理
      $target = $USERS->ByUname($uname);
      if($target->IsLive(true)) $target->AddDoom(1, 'frostbite');
    }
    unset($ROLES->stack->frostbite);

    //-- 夢系レイヤー --//
    foreach($vote_data['DREAM_EAT'] as $uname => $target_uname){ //獏の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効
      $ROLES->LoadMain($user)->DreamEat($USERS->ByUname($target_uname));
    }

    $hunted_list = array(); //狩り成功者リスト
    foreach($ROLES->LoadFilter('guard_dream') as $filter) $filter->DreamGuard($hunted_list);
    foreach($ROLES->LoadFilter('guard_dream') as $filter) $filter->DreamHunt($hunted_list);
    unset($hunted_list);

    //-- 呪い系レイヤー --//
    foreach($vote_data['ANTI_VOODOO_DO'] as $uname => $target_uname){ //厄神の情報収集
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効
      $ROLES->LoadMain($user)->SetGuard($USERS->ByUname($target_uname));
    }
    //PrintData($ROLES->stack->anti_voodoo, 'Target [anti_voodoo]');
  }

  $ROLES->stack->voodoo_killer         = array(); //陰陽師の解呪対象リスト
  $ROLES->stack->voodoo_killer_success = array(); //陰陽師の解呪成功者対象リスト
  foreach($vote_data['VOODOO_KILLER_DO'] as $uname => $target_uname){ //陰陽師の情報収集
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効
    $ROLES->LoadMain($user)->Mage($USERS->ByUname($target_uname));
  }
  //PrintData($ROLES->stack->voodoo_killer, 'Target [voodoo_killer]');
  //PrintData($ROLES->stack->voodoo_killer_success, 'Success [voodoo_killer]');

  //呪術系能力者の処理
  $ROLES->stack->voodoo = array(); //呪術対象リスト
  foreach($vote_data['VOODOO_MAD_DO'] + $vote_data['VOODOO_FOX_DO'] as $uname => $target_uname){
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効
    $ROLES->LoadMain($user)->SetVoodoo($USERS->ByUname($target_uname));
  }
  //PrintData($ROLES->stack->voodoo, 'Target [voodoo]');
  //PrintData($ROLES->stack->voodoo_killer_success, 'Success [voodoo_killer/voodoo]');
  //PrintData($ROLES->stack->anti_voodoo_success, 'Success [anti_voodoo/voodoo]');

  //呪術系能力者の対象先が重なった場合は呪返しを受ける
  if(count($ROLES->stack->voodoo) > 0) $ROLES->LoadMain(new User('voodoo_mad'))->VoodooToVoodoo();

  //-- 占い系レイヤー --//
  $ROLES->stack->jammer = array(); //占い妨害対象リスト
  foreach($vote_data['JAMMER_MAD_DO'] as $uname => $target_uname){ //占い妨害能力者の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効
    $ROLES->LoadMain($user)->SetJammer($USERS->ByUname($target_uname));
  }
  //PrintData($ROLES->stack->jammer, 'Target [jammer]');
  //PrintData($ROLES->stack->anti_voodoo_success, 'Success [anti_voodoo/jammer]');

  //占い能力者の処理を合成 (array_merge() は $uname が整数だと添え字と認識されるので使わないこと)
  $mage_list = array();
  foreach(array('MAGE_DO', 'CHILD_FOX_DO', 'FAIRY_DO') as $action){
    $mage_list += $vote_data[$action];
  }
  $ROLES->stack->phantom = array(); //幻系の発動者リスト
  foreach($mage_list as $uname => $target_uname){ //占い系の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効
    $ROLES->LoadMain($user)->Mage($USERS->ByUname($target_uname));
  }
  $role = 'phantom'; //幻系の能力失効処理
  //PrintData($ROLES->stack->$role, "Target [{$role}]");
  foreach($ROLES->stack->$role as $id => $flag) $USERS->ByID($id)->LostAbility();
  unset($ROLES->stack->$role, $mage_list);

  if($ROOM->date == 1){
    //-- コピー系レイヤー --//
    foreach($vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){ //さとり系の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効
      $ROLES->LoadMain($user)->MindScan($USERS->ByUname($target_uname));
    }

    foreach($vote_data['MANIA_DO'] as $uname => $target_uname){ //神話マニアの処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効
      $ROLES->LoadMain($user)->Copy($USERS->ByUname($target_uname));
    }

    if(! $ROOM->IsOpenCast()){
      foreach($USERS->rows as $user){ //天人の帰還処理
	if($user->IsRole('revive_priest')) $ROLES->LoadMain($user)->PriestReturn();
      }
    }
    $ROLES->LoadMain(new User('exchange_angel'))->Exchange(); //魂移使の処理
  }
  else{
    //-- 尾行系レイヤー --//
    //ブン屋・猩々
    foreach($vote_data['REPORTER_DO'] + $vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      foreach($ROLES->LoadFilter('trap') as $filter){ //罠判定
	if($filter->TrapKill($user, $target_uname)) continue 2;
      }
      $ROLES->LoadMain($user)->Report($USERS->ByUname($target_uname));
    }
  }

  //-- 反魂系レイヤー --//
  if(! $ROOM->IsEvent('no_revive')){ //快晴なら無効
    $ROLES->actor = $ROLES->stack->wolf_target;
    foreach($ROLES->Load('resurrect') as $filter) $filter->Resurrect();

    foreach($USERS->rows as $user){ //仙狼の処理
      if($user->IsRole('revive_wolf')) $ROLES->LoadMain($user)->Resurrect();
    }
  }

  if($ROOM->date > 1){
    $role = 'reverse_assassin';  //反魂師の反魂処理
    $name = 'reverse';
    if(count($ROLES->stack->$name) > 0) $ROLES->LoadMain(new User($role))->Resurrect();
    unset($ROLES->stack->$name);

    //-- 蘇生系レイヤー --//
    if(! $ROOM->IsOpenCast()){
      $boost_revive = false; //蛇神生存判定
      foreach($USERS->rows as $user){
	if($user->IsLiveRole('revive_brownie', true)){
	  $boost_revive = true;
	  break;
	}
      }

      foreach($vote_data['POISON_CAT_DO'] as $uname => $target_uname){ //蘇生能力者の処理
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //直前に死んでいたら無効

	$filter = $ROLES->LoadMain($user);
	$target = $USERS->ByUname($target_uname); //対象者の情報を取得

	//蘇生判定
	$revive_rate   = $filter->GetReviveRate($boost_revive);
	$missfire_rate = $filter->GetMissfireRate($revive_rate);
	$rate = mt_rand(1, 100); //蘇生判定用乱数
	//$rate = 5; //mt_rand(1, 10); //テスト用
	//PrintData("{$revive_rate} ({$missfire_rate})", "ReviveInfo: $uname => $target_uname");
	//PrintData($rate, 'ReviveRate: ' . $user->uname);

	$result = 'failed';
	do{
	  if($rate > $revive_rate) break; //蘇生失敗
	  if($rate <= $missfire_rate){ //誤爆蘇生
	    $revive_target_list = array();
	    //現時点の身代わり君と蘇生能力者が選んだ人以外の死者と憑依者を検出
	    foreach($USERS->rows as $revive_target){
	      if($revive_target->IsDummyBoy() || $revive_target->revive_flag ||
		 $target == $revive_target || $revive_target->IsReviveLimited()) continue;

	      if($revive_target->dead_flag ||
		 ! $USERS->IsVirtualLive($revive_target->user_no, true)){
		$revive_target_list[] = $revive_target->uname;
	      }
	    }
	    //PrintData($revive_target_list, 'ReviveTarget');
	    if(count($revive_target_list) > 0){ //候補がいる時だけ入れ替える
	      $target = $USERS->ByUname(GetRandom($revive_target_list));
	    }
	  }
	  //$target = $USERS->ByID(24); //テスト用
	  //PrintData($target->uname, 'ReviveUser');
	  if($target->IsReviveLimited()) break; //蘇生失敗判定

	  $result = 'success';
	  if($target->IsPossessedGroup()){ //憑依能力者対応
	    if($target->revive_flag) break; //蘇生済みならスキップ

	    $virtual = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //確定死者
	      if($target != $virtual){ //憑依後に死亡していた場合はリセット処理を行う
		$target->ReturnPossessed('possessed_target');

		//憑依先が他の憑依能力者に憑依されていないのならリセット処理を行う
		$stack = $virtual->GetPartner('possessed');
		if($target->user_no == $stack[max(array_keys($stack))]){
		  $virtual->ReturnPossessed('possessed');
		}
	      }
	    }
	    elseif($target->IsLive(true)){ //生存者 (憑依状態確定)
	      if($virtual->IsDrop()){ //蘇生辞退者対応
		$result = 'failed';
		break;
	      }

	      //見かけ上の蘇生処理
	      $target->ReturnPossessed('possessed_target');
	      $ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');

	      //本当の死者の蘇生処理
	      $virtual->Revive(true);
	      $virtual->ReturnPossessed('possessed');

	      //憑依予定者が居たらキャンセル
  	      if(array_key_exists($target->uname, $ROLES->stack->possessed)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	      break;
	    }
	    else{ //当夜に死んだケース
	      if($target != $virtual){ //憑依中ならリセット
		$target->ReturnPossessed('possessed_target'); //本人
		$virtual->ReturnPossessed('possessed'); //憑依先
	      }

	      //憑依予定者が居たらキャンセル
	      if(array_key_exists($target->uname, $ROLES->stack->possessed)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	    }
	  }
	  elseif($target != $USERS->ByReal($target->user_no)){ //憑依されていたらリセット
	    $target->ReturnPossessed('possessed');
	  }
	  $target->Revive(); //蘇生処理
	}while(false);

	if($result == 'success'){
	  if(! $ROOM->IsEvent('full_revive')) $filter->AfterRevive(); //雷雨ならスキップ
	}
	else{
	  $ROOM->SystemMessage($target->handle_name, 'REVIVE_FAILED');
	}
	//蘇生結果を登録
	if($ROOM->IsOption('seal_message')) continue;
	$str = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname) . "\t" . $result;
	$ROOM->SystemMessage($str, 'POISON_CAT_RESULT');
      }
    }
  }

  //-- 憑依レイヤー --//
  //PrintData($ROLES->stack->possessed, 'Target [possessed_wolf]');
  if($ROOM->date > 1){
    //憑依能力者の処理
    $possessed_do_stack = array(); //有効憑依情報リスト (死亡判定と厄神リセット判定)
    foreach($vote_data['POSSESSED_DO'] as $uname => $target_uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true) || $user->revive_flag) continue; //直前に死んでいたら無効 (蘇生でも無効)

      if(in_array($user->uname, $ROLES->stack->anti_voodoo)){ //厄神の護衛判定
	$ROLES->stack->anti_voodoo_success[$user->uname] = true;
	continue;
      }
      $possessed_do_stack[$uname] = $target_uname;
    }

    foreach($possessed_do_stack as $uname => $target_uname){ //憑依能力者の処理
      $user = $USERS->ByUname($uname);

      //失敗判定1：憑依先が競合 / 誰かが憑依してる
      if(count(array_keys($possessed_do_stack, $target_uname)) > 1 ||
	 ! $USERS->ByRealUname($target_uname)->IsSame($target_uname)) continue;

      $target = $USERS->ByUname($target_uname);
      //失敗判定2：蘇生されている / 憑狼の憑依制限役職である //憑依能力者を憑依制限に追加する
      if($target->revive_flag || $target->IsPossessedLimited()) continue;

      //失敗判定3：人狼 ⇔ 妖狐 と恋人陣営には憑依できない
      switch($target->GetCamp(true)){
      case 'wolf':
	if($user->IsRole('possessed_fox')) continue 2;
	break;

      case 'fox':
	if($user->IsRole('possessed_mad')) continue 2;
	break;

      case 'lovers':
	continue 2;
      }
      $ROLES->stack->possessed[$user->uname] = $target_uname;
    }
    //PrintData($ROLES->stack->possessed, 'Target [Possessed]');
  }

  //-- 憑依処理 --//
  $possessed_date = $ROOM->date + 1; //憑依する日を取得
  foreach($ROLES->stack->possessed as $uname => $target_uname){
    $user    = $USERS->ByUname($uname); //憑依者
    $target  = $USERS->ByUname($target_uname); //憑依予定先
    $virtual = $USERS->ByVirtual($user->user_no); //現在の憑依先
    if(! property_exists($user, 'possessed_reset'))  $user->possessed_reset  = NULL;
    if(! property_exists($user, 'possessed_cancel')) $user->possessed_cancel = NULL;

    if($user->IsDead(true)){ //憑依者死亡
      $target->dead_flag = false; //死亡フラグをリセット
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
    }
    elseif($user->possessed_reset){ //憑依リセット
      if(isset($target->user_no)){
	$target->dead_flag = false; //死亡フラグをリセット
	$USERS->Kill($target->user_no, 'WOLF_KILLED');
	if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
      }

      if($user != $virtual){ //憑依中なら元の体に戻される
	//憑依先のリセット処理
	$virtual->ReturnPossessed('possessed');
	$virtual->SaveLastWords();
	$ROOM->SystemMessage($virtual->handle_name, 'POSSESSED_RESET');

	//見かけ上の蘇生処理
	$user->ReturnPossessed('possessed_target');
	$user->SaveLastWords($virtual->handle_name);
	$ROOM->SystemMessage($user->handle_name, 'REVIVE_SUCCESS');
      }
      continue;
    }
    elseif($user->possessed_cancel || $target->revive_flag){ //憑依失敗
      $target->dead_flag = false; //死亡フラグをリセット
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
      continue;
    }
    else{ //憑依成功
      if($user->IsRole('possessed_wolf')){
	$target->dead_flag = false; //死亡フラグをリセット
	$USERS->Kill($target->user_no, 'POSSESSED_TARGETED'); //憑依先の死亡処理
	//憑依先が誰かに憑依しているケースがあるので仮想ユーザで上書きする
	$target = $USERS->ByVirtual($target->user_no);
      }
      else{
	$ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');
	$user->LostAbility();
      }
      $target->AddRole("possessed[{$possessed_date}-{$user->user_no}]");

      //憑依処理
      $user->AddRole("possessed_target[{$possessed_date}-{$target->user_no}]");
      $ROOM->SystemMessage($virtual->handle_name, 'POSSESSED');
      $user->SaveLastWords($virtual->handle_name);
      $user->Update('last_words', '');
    }

    if($user != $virtual){
      $virtual->ReturnPossessed('possessed');
      if($user->IsLive(true)) $virtual->SaveLastWords();
    }
  }

  if(! $ROOM->IsOption('seal_message')){  //陰陽師・厄神の成功結果登録
    foreach(array('voodoo_killer', 'anti_voodoo') as $role){
      $name = $role . '_success';
      //PrintData($ROLES->stack->$name, "SUCCESS [{$role}]");
      if(count($ROLES->stack->$name) > 0) $ROLES->LoadMain(new User($role))->SaveSuccess();
      unset($ROLES->stack->$name);
    }
  }

  if($ROOM->date == 3){ //覚醒者・夢語部のコピー処理
    foreach($USERS->rows as $user){
      if($user->IsDummyBoy() || ! $user->IsRole('soul_mania', 'dummy_mania')) continue;
      if(is_null($id = $user->GetMainRoleTarget())) continue;
      $ROLES->LoadMain($user)->DelayCopy($USERS->ById($id));
    }
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  //-- 司祭系レイヤー --//
  $role_flag = new StdClass(); //役職出現判定フラグを初期化
  foreach($USERS->rows as $user){ //生存者 + 能力発動前の天人を検出
    if(($user->IsLive(true) && ! $user->IsRole('revive_priest')) ||
       (! $ROOM->IsOpenCast() && ! $user->IsDummyBoy() && $user->IsActive('revive_priest'))){
      $role_flag->{$user->main_role}[$user->user_no] = $user->uname;
    }
  }
  //PrintData($role_flag);

  $role = 'attempt_necromancer'; //蟲姫の処理
  if($ROOM->date > 1 && property_exists($role_flag, $role) && count($role_flag->$role) > 0){
    $ROLES->LoadMain(new User($role))->Necromancer($wolf_target, $vote_data);
  }

  $ROLES->LoadMain(new User('priest'))->AggregatePriest($role_flag, $priest_data);
  //PrintData($priest_data->list, 'PriestList');
  //PrintData($priest_data->count, 'LiveCount');
  //PrintData($priest_data->crisis, 'Crisis');
  foreach($priest_data->list as $role){
    $ROLES->LoadMain(new User($role))->Priest($role_flag, $priest_data);
  }

  $status = $ROOM->ChangeDate();
  if($ROOM->test_mode || ! $status) $USERS->ResetJoker(true); //ジョーカー再配置処理
  if($ROOM->IsOption('death_note')) $USERS->ResetDeathNote(); //デスノートの再配布処理
  return $status;
}

//ランダムメッセージを挿入する
function InsertRandomMessage(){
  global $GAME_CONF, $MESSAGE, $ROOM;
  if($GAME_CONF->random_message) $ROOM->Talk(GetRandom($MESSAGE->random_message_list));
}
