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

//投票結果出力
function OutputVoteResult($sentence, $unlock = false, $reset_vote = false){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  if($reset_vote) $ROOM->DeleteVote(); //今までの投票を全部削除
  $title  = $SERVER_CONF->title . ' [投票結果]';
  $header = '<div align="center"><a id="#game_top"></a>';
  $footer = '<br>'."\n" . $RQ_ARGS->back_url . '</div>';
  OutputActionResult($title, $header . $sentence . $footer, '', $unlock);
}

//人数とゲームオプションに応じた役職テーブルを返す
function GetRoleList($user_count){
  global $GAME_CONF, $CAST_CONF, $ROLE_DATA, $ROOM;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $CAST_CONF->role_list[$user_count]; //人数に応じた配役リストを取得
  if(is_null($role_list)){ //リストの有無をチェック
    OutputVoteResult($error_header.$user_count.'人は設定されていません'.$error_footer, true, true);
  }
  //PrintData($ROOM->option_list);

  if($ROOM->IsOptionGroup('chaos')){ //闇鍋モード
    $random_role_list = array(); //ランダム配役結果
    foreach(array('chaos', 'chaosfull', 'chaos_hyper', 'chaos_verso') as $option){ //グレードを検出
      if($ROOM->IsOption($option)){
	$base_name   = $option;
	$chaos_verso = $option == 'chaos_verso';
	break;
      }
    }

    //-- 固定枠設定 --//
    $fix_role_list = $CAST_CONF->{$base_name.'_fix_role_list'}; //グレード個別設定

    //固定配役追加モード
    if($ROOM->IsOption('topping') &&
       is_array($stack = $CAST_CONF->topping_list[$ROOM->option_role->options['topping'][0]])){
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

    if(! $chaos_verso){ //-- 最小出現補正 --//
      $stack = array(); //役職系統別配役数
      foreach($fix_role_list as $key => $value){ //固定枠内の該当グループをカウント
	$stack[$ROLE_DATA->DistinguishRoleGroup($key)] = $value;
      }
      //PrintData($stack, 'FixRole');

      foreach(array('wolf', 'fox') as $role){
	$name  = $base_name.'_'.$role.'_list';
	$rate  = $CAST_CONF->{'chaos_min_'.$role.'_rate'};
	$list  = $CAST_CONF->GenerateRandomList($CAST_CONF->$name);
	$count = round($user_count / $rate) - $stack[$role];
	//PrintData($list, $count);
	//$CAST_CONF->RateToProbability($CAST_CONF->$name); //テスト用
	$CAST_CONF->AddRandom($random_role_list, $list, $count);
	//PrintData($random_role_list);
      }
    }
    //PrintData($random_role_list, 'random('.array_sum($random_role_list).')');

    //-- ランダム配役 --//
    $name  = $base_name . '_random_role_list';
    $list  = $CAST_CONF->GenerateRandomList($CAST_CONF->$name);
    $count = $user_count - (array_sum($random_role_list) + array_sum($fix_role_list));
    //PrintData($list, $count);
    //PrintData(array_sum($CAST_CONF->$name));
    //$CAST_CONF->RateToProbability($CAST_CONF->$random_name); //テスト用
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
	$name = $base_name . '_replace_human_role_list';
	$list = $CAST_CONF->GenerateRandomList($CAST_CONF->$name);
	//PrintData($list, $count);
	//$CAST_CONF->RateToProbability($CAST_CONF->$name); //テスト用
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
    //埋毒者 (村人2 → 埋毒者1・人狼1)
    if($ROOM->IsOption('poison') && $user_count >= $CAST_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //暗殺者 (村人2 → 暗殺者1・人狼1)
    if($ROOM->IsOption('assassin') && $user_count >= $CAST_CONF->assassin){
      $role_list['human'] -= 2;
      $role_list['assassin']++;
      $role_list['wolf']++;
    }

    //白狼 (人狼 → 白狼)
    if($ROOM->IsOption('boss_wolf') && $user_count >= $CAST_CONF->boss_wolf){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //毒狼 (人狼 → 毒狼、村人 → 薬師)
    if($ROOM->IsOption('poison_wolf') && $user_count >= $CAST_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //憑狼 (人狼 → 憑狼)
    if($ROOM->IsOption('possessed_wolf') && $user_count >= $CAST_CONF->possessed_wolf){
      $role_list['wolf']--;
      $role_list['possessed_wolf']++;
    }

    //天狼 (人狼 → 天狼)
    if($ROOM->IsOption('sirius_wolf') && $user_count >= $CAST_CONF->sirius_wolf){
      $role_list['wolf']--;
      $role_list['sirius_wolf']++;
    }

    //キューピッド (村人 → キューピッド)
    if($ROOM->IsOption('cupid') && ! $ROOM->IsOption('full_cupid') &&
       $user_count >= $CAST_CONF->cupid){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //巫女 (村人2 → 巫女1・女神1)
    if($ROOM->IsOption('medium') && $user_count >= $CAST_CONF->medium){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['mind_cupid']++;
    }

    //神話マニア (村人 → 神話マニア)
    if($ROOM->IsOption('mania') && ! $ROOM->IsOption('full_mania') &&
       $user_count >= $CAST_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //探偵 (共有 or 村人 → 探偵)
    if($ROOM->IsOption('detective')){
      if($role_list['common'] > 0){
	$role_list['common']--;
	$role_list['detective_common']++;
      }
      else{
	$role_list['human']--;
	$role_list['detective_common']++;
      }
    }
  }

  //-- 村人置換村 --//
  $count = $role_list['human'];
  if($ROOM->IsOption('gerd') && $count > 0) $count--; //ゲルト君モードなら一人確保
  $CAST_CONF->ReplaceHuman($role_list, $count);
  $CAST_CONF->ChangeMad($role_list, $role_list['mad']); //狂人置換村

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

  if($role_list['human'] < 0){ //"村人" の人数をチェック
    $sentence = '"村人" の人数がマイナスになってます';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }
  if($role_list['wolf'] < 0){ //"人狼" の人数をチェック
    $sentence = '"人狼" の人数がマイナスになってます';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
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

    $sentence = '村人 (' . $user_count . ') と配役の数 (' . $role_count . ') が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
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
      $sentence = '身代わり君に役が与えられていません';
      OutputVoteResult($error_header . $sentence . $error_footer, $reset_flag, $reset_flag);
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
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
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
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $fix_role_list_count = count($fix_role_list); //配役の数
  if($fix_uname_list_count != $fix_role_list_count){
    $uname_str = '配役決定者の人数 (' . $fix_uname_list_count . ') ';
    $role_str  = '配役の数 (' . $fix_role_list_count . ') ';
    $sentence  = $uname_str . 'と' . $role_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $role_list_count = count($role_list); //残り配役数
  if($role_list_count > 0){
    $sentence = '配役リストに余り (' . $role_list_count .') があります';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //兼任となる役割の設定
  shuffle($rand_keys = array_rand($fix_role_list, $user_count)); //ランダムキーを取得
  $rand_keys_index = 0;
  $sub_role_count_list = array();
  $roled_list = array(); //配役済み番号
  //割り振り対象外役職のリスト
  $delete_role_list = array('febris', 'frostbite', 'death_warrant', 'panelist', 'day_voter',
			    'wirepuller_luck', 'occupied_luck', 'mind_read', 'mind_receiver',
			    'mind_friend', 'mind_sympathy', 'mind_evoke', 'mind_presage',
			    'mind_lonely', 'mind_sheep', 'sheep_wisp', 'lovers', 'challenge_lovers',
			    'possessed_exchange', 'joker', 'rival', 'enemy', 'supported',
			    'possessed_target', 'possessed', 'infected', 'psycho_infected',
			    'bad_status', 'protected', 'lost_ability', 'muster_ability',
			    'changed_therian', 'copied', 'copied_trick', 'copied_basic',
			    'copied_soul', 'copied_teller');

  //サブ役職テスト用
  /*
  $stack = array('whisper_ringing', 'howl_ringing', 'critical_luck');
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

  $vote_duel = $ROOM->event->vote_duel; //特殊イベントを取得
  if(is_array($vote_duel) && ! in_array($RQ_ARGS->target_no, $vote_duel)){
    OutputVoteResult('処刑：決選投票対象者以外には投票できません');
  }
  if(! $ROOM->test_mode) LockVote(); //テーブルを排他的ロック

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

  //天候の処理
  if($ROOM->IsEvent('hyper_random_voter')) $vote_number += mt_rand(0, 5);
  if($vote_number < 0) $vote_number = 0;

  if(! $SELF->Vote('VOTE_KILL', $target->uname, $vote_number)){ //投票処理
    OutputVoteResult('データベースエラー', true);
  }

  //システムメッセージ
  if($ROOM->test_mode) return true;
  $ROOM->Talk("VOTE_DO\t" . $USERS->GetHandleName($target->uname, true), $SELF->uname);

  AggregateVoteDay(); //集計処理
  OutputVoteResult('投票完了', true);
}

//昼の投票集計処理
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS;

  //-- 投票処理実行判定 --//
  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //コマンドチェック
  $user_list = $USERS->GetLivingUsers(); //生きているユーザを取得
  if($ROOM->LoadVote() != count($user_list)) return false; //投票数と照合
  //PrintData($ROOM->vote, 'Vote');

  //-- 初期化処理 --//
  $live_uname_list        = array(); //生きている人のユーザ名リスト
  $vote_message_list      = array(); //システムメッセージ用 (ユーザID => array())
  $vote_target_list       = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list        = array(); //得票リスト (ユーザ名 => 投票数)
  $pharmacist_result_list = array(); //薬師系の鑑定結果
  if($ROOM->IsOption('joker')) $joker_id = $USERS->SetJoker(); //現在のジョーカー所持者の ID

  //-- 投票データ収集 --//
  foreach($ROOM->vote as $uname => $list){ //初期得票データを収集
    $target_uname = $USERS->ByVirtualUname($list['target_uname'])->uname;
    $vote_count_list[$target_uname] += $list['vote_number'];
  }
  //PrintData($vote_count_list, 'VoteCountBase');

  foreach($user_list as $uname){ //個別の投票データを収集
    $user = $USERS->ByVirtualUname($uname); //仮想ユーザを取得
    $list = $ROOM->vote[$uname]; //投票データ
    $target = $USERS->ByVirtualUname($list['target_uname']); //投票先の仮想ユーザ
    $vote_number  = (int)$list['vote_number']; //投票数
    $voted_number = (int)$vote_count_list[$user->uname]; //得票数

    $ROLES->actor = $user; //得票者をセット
    //メイン役職の得票補正
    foreach($ROLES->Load('voted_main') as $filter) $filter->FilterVoted($voted_number);

    //サブ役職の得票補正
    if(! $ROOM->IsEvent('no_authority')){ //蜃気楼ならスキップ
      foreach($ROLES->Load('voted_sub') as $filter) $filter->FilterVoted($voted_number);
      //if($user->IsRole('critical_luck')) $voted_number += 100; //テスト用 (痛恨強制発動)
    }
    if($voted_number < 0) $voted_number = 0; //マイナスになっていたら 0 にする

    //システムメッセージ用の配列を生成
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);
    //PrintData($message_list, $uname);

    //リストにデータを追加
    $live_uname_list[$user->user_no]   = $user->uname;
    $vote_message_list[$user->user_no] = $message_list;
    $vote_target_list[$user->uname]    = $target->uname;
    $vote_count_list[$user->uname]     = $voted_number;
    foreach($ROLES->Load('vote_ability') as $filter) $filter->SetVoteAbility($target->uname);
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
    //PrintData($vote_message_list, 'VoteMessage[rebel]');
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
  $vote_kill_uname = ''; //処刑される人のユーザ名
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
      $filter->DistinguishPoison($pharmacist_result_list);
    }
    //PrintData($pharmacist_result_list, 'DistinguishPoison');

    do{ //-- 処刑者の毒処理 --//
      if(! $vote_target->IsPoison()) break; //毒能力の発動判定

      //薬師系の解毒判定 (夢毒者は対象外)
      $ROLES->actor = $USERS->ByVirtual($vote_target->user_no); //投票データは仮想ユーザ
      if(! $vote_target->IsRole('dummy_poison')){
	foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_result_list);
	if($ROLES->actor->detox_flag) break;
      }

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = array(); //毒の対象リスト
      $target_list = $GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list;
      //PrintData($target_list);

      foreach($target_list as $uname){ //常時対象外の役職を除く
	$user = $USERS->ByRealUname($uname);
	if($user->IsLive(true) && ! $user->IsAvoid(true)) $poison_target_list[] = $uname;
      }
      //PrintData($poison_target_list, 'BasePoisonTarget');

      //特殊毒の場合はターゲットが限定される
      if($ROLES->actor->alchemy_flag || $ROOM->IsEvent('alchemy_pharmacist')){ //錬金術師
	$stack = array();
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsCamp('human')) $stack[] = $uname;
	}
	$poison_target_list = $stack;
      }
      else{
	$ROLES->actor = $vote_target;
	foreach($ROLES->Load('poison') as $filter) $filter->FilterPoisonTarget($poison_target_list);
      }
      //PrintData($poison_target_list, 'PoisonTarget');
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
      foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_result_list);
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
	  foreach($ROLES->LoadFilter('detox') as $filter) $filter->Detox($pharmacist_result_list);
	  if(! $ROLES->actor->detox_flag) $chain_count++;
	}
      }
    }while(false);
    //PrintData($pharmacist_result_list, 'EndDetox');

    //-- 処刑者カウンター処理 --//
    $ROLES->actor = $vote_target;
    foreach($ROLES->Load('vote_kill_counter') as $filter) $filter->VoteKillCounter($voter_list);

    //-- 特殊投票発動者の処理 --//
    foreach($ROLES->LoadFilter('vote_action') as $filter) $filter->VoteAction();

    //-- 霊能者系の処理 --//
    $result_header = $USERS->GetHandleName($vote_target->uname, true) . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能判定
    if($vote_target->IsRole('boss_wolf', 'phantom_wolf', 'cursed_wolf', 'possessed_wolf')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('white_fox', 'black_fox', 'phantom_fox', 'possessed_fox',
				'cursed_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsChildFox()){
      $necromancer_result = 'child_fox';
    }
    elseif($vote_target->IsWolf()){
      $necromancer_result = 'wolf';
    }
    elseif($vote_target->IsRoleGroup('vampire') || $vote_target->IsRole('cute_chiroptera')){
      $necromancer_result = 'chiroptera';
    }
    elseif($vote_target->IsOgre()){
      $necromancer_result = 'ogre';
    }
    else{
      $necromancer_result = 'human';
    }

    //火車の妨害判定
    if($ROOM->IsEvent('corpse_courier_mad')){ //砂塵嵐判定
      $flag_stolen = true;
    }
    else{
      $flag_stolen = false;
      foreach($voter_list as $uname){
	if($USERS->ByRealUname($uname)->IsRole('corpse_courier_mad')){
	  $flag_stolen = true;
	  break;
	}
      }
    }

    foreach($USERS->rows as $user) $role_flag->{$user->main_role} = true; //役職出現判定
    //PrintData($role_flag, 'ROLE_FLAG');
    if($role_flag->spiritism_wizard && ! $ROOM->IsEvent('new_moon')){ //交霊術師の処理
      if($ROOM->IsEvent('full_wizard')) //霧雨
	$stack = array('soul_');
      elseif($ROOM->IsEvent('debilitate_wizard')) //木枯らし
	$stack = array('sex_');
      else
	$stack = array('', 'soul_', 'psycho_', 'embalm_', 'sex_'); //sex_necromancer は未実装
      $wizard_flag->{GetRandom($stack) . 'necromancer'} = true;
      $wizard_action = 'SPIRITISM_WIZARD_RESULT';
      if($wizard_flag->sex_necromancer){
	$str = $result_header . ($flag_stolen ? 'stolen' : 'sex_' . $vote_target->sex);
	$ROOM->SystemMessage($str, $wizard_action);
      }
    }

    $role = 'necromancer'; //霊能者の処理
    if($role_flag->$role || $wizard_flag->$role){
      $str = $result_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      if($role_flag->$role)   $ROOM->SystemMessage($str, $action);
      if($wizard_flag->$role) $ROOM->SystemMessage($str, $wizard_action);
    }

    $role = 'soul_necromancer'; //雲外鏡の処理
    if($role_flag->$role || $wizard_flag->$role){
      $str = $result_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      if($role_flag->$role)   $ROOM->SystemMessage($str, 'SOUL_' . $action);
      if($wizard_flag->$role) $ROOM->SystemMessage($str, $wizard_action);
    }

    $role = 'psycho_necromancer'; //精神感応者の処理
    if($role_flag->$role || $wizard_flag->$role){
      $str = $result_header;
      if($flag_stolen){
	$str .= 'stolen';
      }
      else{ //判定は順番依存有り
	$str .= 'psycho_necromancer_';
	if($vote_target->IsRole('changed_therian'))
	  $str .= 'mad';
	elseif($vote_target->IsRoleGroup('copied'))
	  $str .= 'mania';
	elseif($vote_target->IsRoleGroup('mad'))
	  $str .= 'wolf';
	elseif($vote_target->IsLiar())
	  $str .= 'mad';
	else
	  $str .= 'human';
      }
      if($role_flag->$role)   $ROOM->SystemMessage($str, 'PSYCHO_' . $action);
      if($wizard_flag->$role) $ROOM->SystemMessage($str, $wizard_action);
    }

    $role = 'embalm_necromancer'; //死化粧師の処理
    if($role_flag->$role || $wizard_flag->$role){
      $str = $result_header;
      if($flag_stolen){
	$str .= 'stolen';
      }
      else{
	$target = $USERS->ByRealUname($vote_target_list[$vote_target->uname]); //投票先を取得
	$str .= 'embalm_' .
	  ($vote_target->GetCamp(true) == $target->GetCamp(true) ? 'agony' : 'reposeful');
      }
      if($role_flag->$role)   $ROOM->SystemMessage($str, 'EMBALM_' . $action);
      if($wizard_flag->$role) $ROOM->SystemMessage($str, $wizard_action);
    }

    if($role_flag->emissary_necromancer){ //密偵の処理
      $camp  = $vote_target->GetCamp(true);
      $count = 0;
      //処刑者への投票者を検出して同一陣営の人をカウント
      foreach(array_keys($vote_target_list, $vote_target->uname) as $uname){
	if($USERS->ByRealUname($uname)->IsCamp($camp, true)) $count++;
      }
      $ROOM->SystemMessage($count, 'EMISSARY_' . $action);
    }

    if($role_flag->dummy_necromancer && ! $ROOM->IsEvent('no_dream')){ //夢枕人の処理
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      $ROOM->SystemMessage($result_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //-- 得票カウンター処理 --//
  foreach($ROLES->LoadFilter('voted_reaction') as $filter) $filter->VotedReaction();

  //-- サブ役職のショック死処理 --//
  if(! $ROOM->IsEvent('no_sudden_death')){ //凪ならスキップ
    //判定用データを登録
    $ROLES->stack->count  = array_count_values($vote_target_list); //投票者対象ユーザ名 => 人数
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
      $reason = in_array($uname, $thunderbolt_list) ? 'THUNDERBOLT' : '';
      foreach($ROLES->Load('sudden_death') as $filter) $filter->FilterSuddenDeath($reason);
      if($reason == '') continue;

      //薬師系の治療判定
      foreach($ROLES->LoadFilter('cure') as $filter) $filter->Cure($pharmacist_result_list);
      if(! $ROLES->actor->cured_flag){
	$USERS->SuddenDeath($ROLES->actor->user_no, 'SUDDEN_DEATH_' . $reason);
      }
    }
  }

  if($role_flag->follow_mad){ //舟幽霊の処理
    $target_stack = array(); //対象者リスト
    $follow_stack = array(); //有効投票先リスト
    $count = 0; //能力発動カウント
    foreach($user_list as $uname){ //情報収集
      $user = $USERS->ByRealUname($uname);
      if($user->IsLive(true) && ! $user->IsAvoid(true)) $target_stack[] = $user->user_no;
      if($user->IsSame($vote_kill_uname) || ! $user->IsRole('follow_mad')) continue;

      $target_uname = $vote_target_list[$user->uname]; //投票先を取得
      if($target_uname == $vote_kill_uname) continue; //処刑者ならスキップ

      $target = $USERS->ByRealUname($target_uname);
      $target->suicide_flag ? $count++ : $follow_stack[$uname] = $target->user_no;
    }
    //PrintData($follow_stack, 'follow_mad:' . $count);
    //PrintData($target_stack, 'BaseFollowTarget');

    while($count > 0 && count($target_stack) > 0){ //道連れ処理
      $count--;
      shuffle($target_stack); //配列をシャッフル
      $id = array_shift($target_stack);
      $USERS->SuddenDeath($id, 'SUDDEN_DEATH_FOLLOWED'); //死亡処理

      if(! in_array($id, $follow_stack)) continue;//連鎖判定
      $stack = array();
      foreach($follow_stack as $uname => $user_no){
	$id == $user_no ? $count++ : $stack[$uname] = $user_no;
      }
      $follow_stack = $stack;
    }
  }

  foreach($pharmacist_result_list as $uname => $result){ //薬師系の鑑定結果を登録
    $user = $USERS->ByUname($uname);
    $target_uname = $ROLES->stack->{$user->main_role}[$user->uname];
    $handle_name = $USERS->GetHandleName($target_uname, true);
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

//夜の集計処理
function AggregateVoteNight($skip = false){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  $ROOM->LoadVote(); //投票情報を取得
  //PrintData($ROOM->vote, 'Vote Row');

  $vote_data = $ROOM->ParseVote(); //コマンド毎に分割
  //PrintData($vote_data, 'Vote Data');

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
	       'POSSESSED_DO', 'VAMPIRE_DO', 'OGRE_DO');
  }
  foreach($stack as $action){
    if(is_null($vote_data[$action])) $vote_data[$action] = array();
  }
  //PrintData($vote_data);

  //-- 変数の初期化 --//
  $trap_target_list             = array(); //罠師の罠の設置先
  $trapped_list                 = array(); //罠死予定者
  $snow_trap_target_list        = array(); //雪女の罠の設置先
  $frostbite_list               = array(); //凍傷予定者
  $guard_target_list            = array(); //狩人系の護衛対象
  $gatekeeper_guard_target_list = array(); //門番の護衛対象
  $dummy_guard_target_list      = array(); //夢守人の護衛対象
  $wizard_target_list           = array(); //魔法使い系 (拡散型) の対象
  $escaper_target_list          = array(); //逃亡者の逃亡先
  $sacrifice_list               = array(); //身代わり死した人
  $anti_voodoo_target_list      = array(); //厄神の護衛対象
  $anti_voodoo_success_list     = array(); //厄払い成功者
  $reverse_assassin_target_list = array(); //反魂師の対象
  $possessed_target_list        = array(); //憑依予定者 => 憑依成立フラグ
  $possessed_target_id_list     = array(); //憑依対象者

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
      $ROLES->actor = $USERS->ByUname($uname);
      $role = $ROLES->Load('main_role', true)->GetRole();
      $ROLES->actor->virtual_role = $role; //仮想役職を登録
      //PrintData($role, "Wizard: {$uname}");
      if(strpos($role, 'mage') !== false){
	$vote_data['MAGE_DO'][$uname] = $target_uname;
      }
      elseif(strpos($role, 'guard') !== false){
	$vote_data['GUARD_DO'][$uname] = $target_uname;
      }
      elseif(strpos($role, 'assassin') !== false || $role == 'doom_fox'){
	$vote_data['ASSASSIN_DO'][$uname] = $target_uname;
      }
      elseif($role == 'jammer_mad'){
	$vote_data['JAMMER_MAD_DO'][$uname] = $target_uname;
      }
      elseif($role == 'voodoo_mad'){
	$vote_data['VOODOO_MAD_DO'][$uname] = $target_uname;
      }
      elseif($role == 'dream_eater_mad'){
	$vote_data['DREAM_EAT'][$uname] = $target_uname;
      }
      elseif($role == 'snow_trap_mad'){
	$vote_data['TRAP_MAD_DO'][$uname] = $target_uname;
      }
      elseif(strpos($role, 'fairy') !== false){
	$vote_data['FAIRY_DO'][$uname] = $target_uname;
      }
    }
  }

  //-- 接触系レイヤー --//
  $voted_wolf  =& new User();
  $wolf_target =& new User();
  foreach($vote_data['WOLF_EAT'] as $uname => $target_uname){ //人狼の情報収集
    $voted_wolf  = $USERS->ByUname($uname);
    $wolf_target = $USERS->ByUname($target_uname);
  }

  if($ROOM->date > 1){
    foreach($vote_data['TRAP_MAD_DO'] as $uname => $target_uname){ //罠能力者の情報収集
      $user = $USERS->ByUname($uname);

      //人狼に狙われていたら自分自身への設置以外は無効
      if($user->IsSame($wolf_target->uname) && ! $user->IsSame($target_uname)) continue;
      if($user->IsRole('trap_mad', 'trap_fox')){ //役職別に罠をセット
	$trap_target_list[$user->uname] = $target_uname;
	$user->LostAbility(); //罠師・狡狐は一度設置したら能力喪失
      }
      else{
	$snow_trap_target_list[$user->uname] = $target_uname;
      }
    }

    //狡狼の自動設置判定 (花曇・雪明りは無効)
    if($ROOM->date > 4 && ! $ROOM->IsEvent('no_contact') && ! $ROOM->IsEvent('no_trap')){
      foreach($USERS->rows as $user){
	if($user->IsLiveRole('trap_wolf')) $trap_target_list[$user->uname] = $user->uname;
      }
    }
    //PrintData($trap_target_list, 'List [trap_mad]');
    //PrintData($snow_trap_target_list, 'List [snow_trap_mad]');

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $stack = array_count_values($trap_target_list);
    foreach($trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $stack[$target_uname] > 1) $trapped_list[] = $uname;
    }

    foreach($snow_trap_target_list as $uname => $target_uname){ //雪女の罠死判定
      if($uname != $target_uname && in_array($target_uname, $trap_target_list)){
	$trapped_list[] = $uname;
      }
    }

    //雪女が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は凍傷になる
    $stack = array_count_values($snow_trap_target_list);
    foreach($snow_trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $stack[$target_uname] > 1) $frostbite_list[] = $uname;
    }

    foreach($trap_target_list as $uname => $target_uname){ //罠師の凍傷判定
      if($uname != $target_uname && in_array($target_uname, $snow_trap_target_list)){
	$frostbite_list[] = $uname;
      }
    }

    foreach($vote_data['GUARD_DO'] as $uname => $target_uname){ //狩人系の護衛先をセット
      $user = $USERS->ByUname($uname);
      if($user->IsRole('dummy_guard')){ //夢守人は罠無効
	//熱帯夜ならスキップ
	if(! $ROOM->IsEvent('no_dream')) $dummy_guard_target_list[$user->uname] = $target_uname;
	continue;
      }
      elseif($ROOM->IsEvent('no_contact')) continue; //花曇ならスキップ

      $guard_target_list[$user->uname] = $target_uname;
      if($user->IsRole('gatekeeper_guard')){ //門番の護衛先を登録
	$gatekeeper_guard_target_list[$user->uname] = $target_uname;
      }
      if(in_array($target_uname, $trap_target_list)) //罠死判定
	$trapped_list[] = $user->uname;
      elseif(in_array($target_uname, $snow_trap_target_list)) //凍傷判定
	$frostbite_list[] = $user->uname;
    }
    //PrintData($guard_target_list, 'Target [guard]');
    //PrintData($gatekeeper_guard_target_list, 'Target [gatekeeper_guard]');
    //PrintData($dummy_guard_target_list, 'Target [dummy_guard]');

    //魔法使い系 (拡散型) の情報収集
    foreach($vote_data['SPREAD_WIZARD_DO'] as $uname => $target_list){
      $trapped   = false;
      $frostbite = false;
      foreach(explode(' ', $target_list) as $id){
	$target_uname = $USERS->ByID($id)->uname;
	$wizard_target_list[$uname][] = $target_uname;
	$trapped   |= in_array($target_uname, $trap_target_list); //罠死判定
	$frostbite |= in_array($target_uname, $snow_trap_target_list); //凍傷判定
      }
      if($trapped)
	$trapped_list[] = $uname;
      elseif($frostbite)
	$frostbite_list[] = $uname;
    }
    //PrintData($wizard_target_list, 'Target [wizard]');

    foreach($vote_data['ESCAPE_DO'] as $uname => $target_uname){ //逃亡者系の情報収集
      $user   = $USERS->ByUname($uname);
      $target = $USERS->ByUname($target_uname);
      $ROLES->actor = $user;
      $filter = $ROLES->Load('main_role', true);
      if(in_array($target_uname, $trap_target_list)){ //罠死判定
	$trapped_list[] = $user->uname;
      }
      elseif($filter->EscapeFailed($target)){ //逃亡失敗判定
	$USERS->Kill($user->user_no, 'ESCAPER_DEAD');
      }
      else{
	//凍傷判定
	if(in_array($target->uname, $snow_trap_target_list)) $frostbite_list[] = $user->uname;
	$filter->EscapeAction($target); //逃亡処理
	$escaper_target_list[$user->uname] = $target->uname; //逃亡先をセット
      }
    }
    //PrintData($escaper_target_list, 'Target [escaper]');
  }

  do{ //人狼の襲撃成功判定
    if($skip || $ROOM->IsQuiz()) break; //スキップモード・クイズ村仕様

    if(! $voted_wolf->IsSiriusWolf(false)){ //罠判定 (覚醒天狼は無効)
      if(in_array($wolf_target->uname, $trap_target_list)){ //罠死判定
	$trapped_list[] = $voted_wolf->uname;
	break;
      }
      if(in_array($wolf_target->uname, $snow_trap_target_list)){ //凍傷判定
	$frostbite_list[] = $voted_wolf->uname;
      }
    }

    //逃亡者の巻き添え判定
    foreach(array_keys($escaper_target_list, $wolf_target->uname) as $uname){
      $USERS->Kill($USERS->UnameToNumber($uname), 'WOLF_KILLED'); //死亡処理
    }

    //狩人系の護衛判定
    $stack = array_keys($guard_target_list, $wolf_target->uname); //護衛者を検出
    $rate = $ROOM->IsEvent('full_wizard') ? 1.25 :
      ($ROOM->IsEvent('debilitate_wizard') ? 0.75 : 1);
    foreach($wizard_target_list as $uname => $target_list){ //結界師を追加
      if(in_array($wolf_target->uname, $target_list) &&
	 mt_rand(1, 100) <= (100 - count($target_list) * 20) * $rate) $stack[] = $uname;
    }
    //PrintData($stack, 'List [gurad]');

    if(count($stack) > 0){
      $guard_flag = false; //護衛成功フラグ
      //護衛制限判定
      $guard_limited = ! $ROOM->IsEvent('full_guard') && $wolf_target->IsGuardLimited();
      foreach($stack as $uname){
	$user = $USERS->ByUname($uname);

	//個別護衛成功判定
	$guard_flag |= ! ($ROOM->IsEvent('half_guard') && mt_rand(0, 1) > 0) &&
	  (! $guard_limited || $user->IsRole(true, 'blind_guard', 'poison_guard'));

	if($user->IsRole('hunter_guard')) //猟師の処理
	  $USERS->Kill($user->user_no, 'WOLF_KILLED');
	elseif($user->IsRole('blind_guard')) //夜雀の処理
	  $voted_wolf->AddRole('blinder');

	//護衛成功メッセージを登録
	$ROOM->SystemMessage($user->GetHandleName($wolf_target->uname), 'GUARD_SUCCESS');
	$user->guard_success[] = $wolf_target->uname;
      }
      if($guard_flag && ! $voted_wolf->IsSiriusWolf()) break; //護衛成功判定
    }

    if(! $wolf_target->IsDummyBoy()){ //特殊能力者判定 (身代わり君は対象外)
      if(! $voted_wolf->IsSiriusWolf()){ //特殊襲撃失敗判定 (サブの判定が先/完全覚醒天狼は無効)
	$ROLES->actor = $wolf_target;
	foreach($ROLES->Load('wolf_eat_resist') as $filter){
	  if($filter->WolfEatResist()) break 2;
	}

	if($wolf_target->IsOgre()){ //確率無効タイプ (鬼陣営)
	  $rate = mt_rand(1, 100); //襲撃成功判定乱数
	  //$rate = 5; //テスト用
	  $ROLES->actor = $wolf_target;
	  $resist_rate = $ROLES->Load('main_role', true)->resist_rate;

	  //朧月なら確定無効 (茨木童子対応で +100 にはしない。また、現在の最低値は 20%)
	  if($ROOM->IsEvent('full_ogre')) $resist_rate *= 10;
	  elseif($ROOM->IsEvent('seal_ogre')) $resist_rate = 0;
	  //PrintData("{$rate} ({$resist_rate})", 'Rate [ogre resist]');
	  if($rate <= $resist_rate) break;
	}
      }
      if($ROOM->date > 1 && $wolf_target->IsRoleGroup('escaper')) break; //逃亡者系判定
      if(! $voted_wolf->IsRole('hungry_wolf')){ //人狼・妖狐襲撃判定 (餓狼は対象外)
	if($wolf_target->IsWolf()){ //人狼系判定 (例：銀狼出現)
	  if($voted_wolf->IsRole('emerald_wolf')){ //翠狼の処理
	    $role = $voted_wolf->GetID('mind_friend');
	    $voted_wolf->AddRole($role);
	    $wolf_target->AddRole($role);
	  }
	  $wolf_target->wolf_killed = true; //尾行判定は成功扱い
	  break;
	}
	if($wolf_target->IsResistFox()){ //妖狐判定
	  $ROLES->actor = $voted_wolf; //妖狐襲撃能力処理
	  foreach($ROLES->Load('fox_eat_action') as $filter) $filter->FoxEatAction($wolf_target);

	  $ROLES->actor = $wolf_target; //妖狐襲撃カウンター処理
	  foreach($ROLES->Load('fox_eat_counter') as $filter) $filter->FoxEatCounter($voted_wolf);

	  $ROOM->SystemMessage($wolf_target->handle_name, 'FOX_EAT');
	  $wolf_target->wolf_killed = true; //尾行判定は成功扱い
	  break;
	}
      }

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
	      $sacrifice_list[] = $target->uname;
	      break 2;
	    }
	  }
	}

	$ROLES->actor = $voted_wolf; //人狼襲撃能力処理
	foreach($ROLES->Load('wolf_eat_action') as $filter){
	  if($filter->WolfEatAction($wolf_target)) break 2;
	}

	$ROLES->actor = $wolf_target;  //人狼襲撃カウンター処理
	foreach($ROLES->Load('wolf_eat_counter') as $filter) $filter->WolfEatCounter($voted_wolf);
      }
    }

    //-- 襲撃処理 --//
    //憑狼の処理
    if($voted_wolf->IsRole('possessed_wolf') && ! $wolf_target->IsDummyBoy() &&
       ! $wolf_target->IsCamp('fox') && ! $wolf_target->IsPossessedLimited()){
      $possessed_target_list[$voted_wolf->uname] = $wolf_target->uname;
      $wolf_target->dead_flag = true;
      //襲撃先が厄神なら憑依リセット
      if($wolf_target->IsRole('anti_voodoo')) $voted_wolf->possessed_reset = true;
    }
    else{
      $action = $voted_wolf->IsRole('hungry_wolf') ? 'HUNGRY_WOLF_KILLED' : 'WOLF_KILLED';
      $USERS->Kill($wolf_target->user_no, $action); //通常狼の襲撃処理
    }
    $wolf_target->wolf_killed = true;

    if($voted_wolf->IsActive('tongue_wolf')){ //舌禍狼の処理
      if($wolf_target->IsRole('human')) $voted_wolf->LostAbility(); //村人なら能力失効
      $str = $voted_wolf->GetHandleName($wolf_target->uname, $wolf_target->main_role);
      $ROOM->SystemMessage($str, 'TONGUE_WOLF_RESULT');
    }

    if($wolf_target->IsPoison() && ! $voted_wolf->IsSiriusWolf()){ //-- 毒死判定 --//
      //襲撃者が抗毒狼か、襲撃者固定設定なら対象固定
      if($voted_wolf->IsRole('resist_wolf') || $GAME_CONF->poison_only_eater)
	$poison_target = $voted_wolf;
      else //生きている狼からランダム選出
	$poison_target = $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));

      $ROLES->actor = $wolf_target; //襲撃毒死回避判定
      foreach($ROLES->Load('avoid_poison_eat') as $filter){
	if($filter->AvoidPoisonEat($poison_target)) break 2;
      }
      if($poison_target->IsChallengeLovers()) break; //難題なら無効

      if($poison_target->IsActive('resist_wolf')) //抗毒狼の処理
	$poison_target->LostAbility();
      else
	$USERS->Kill($poison_target->user_no, 'POISON_DEAD_night'); //毒死処理
    }
  }while(false);
  //PrintData($possessed_target_list, 'PossessedTarget [possessed_wolf]');

  if($ROOM->date > 1){
    if(! $ROOM->IsEvent('no_hunt')){ //川霧ならスキップ
      foreach($guard_target_list as $uname => $target_uname){ //狩人系の狩り判定
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //直前に死んでいたら無効

	$target = $USERS->ByUname($target_uname);
	$ROLES->actor = $user;
	//対象が身代わり死していた場合はスキップ
	if(! in_array($target->uname, $sacrifice_list) &&
	   $ROLES->Load('main_role', true)->IsHuntTarget($target)){
	  $USERS->Kill($target->user_no, 'HUNTED');
	  $ROOM->SystemMessage($user->GetHandleName($target->uname), 'GUARD_HUNTED');
	}
      }
    }

    $vampire_target_list = array(); //吸血対象者リスト
    $vampire_killed_list = array(); //吸血死対象者リスト
    foreach($vote_data['VAMPIRE_DO'] as $uname => $target_uname){ //吸血鬼の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      if(in_array($target_uname, $trap_target_list)){ //罠死判定
	$trapped_list[] = $user->uname;
	continue;
      }
      //凍傷判定
      if(in_array($target_uname, $snow_trap_target_list)) $frostbite_list[] = $user->uname;

      //吸血鬼に逃亡した逃亡者を対象者リストに追加
      foreach(array_keys($escaper_target_list, $user->uname) as $escaper_uname){
	$vampire_target_list[$user->uname][] = $escaper_uname;
      }
      //逃亡者の巻き添え判定
      foreach(array_keys($escaper_target_list, $target_uname) as $escaper_uname){
	$vampire_target_list[$user->uname][] = $escaper_uname;
      }
      $target = $USERS->ByUname($target_uname);

      //狩人系の護衛判定
      $guard_flag = false; //護衛成功フラグ
      $guard_limited = ! $ROOM->IsEvent('full_guard') && $target->IsGuardLimited(); //護衛制限判定
      $stack = array_keys($guard_target_list, $target->uname); //護衛者を検出
      $rate = $ROOM->IsEvent('full_wizard') ? 1.25 :
	($ROOM->IsEvent('debilitate_wizard') ? 0.75 : 1);
      foreach($wizard_target_list as $wizard_uname => $target_list){ //結界師を追加
	if(in_array($target->uname, $target_list) &&
	   mt_rand(1, 100) <= (100 - count($target_list) * 20) * $rate) $stack[] = $wizard_uname;
      }
      //PrintData($stack, 'List [gurad]');

      foreach($stack as $guard_uname){
	$guard_user = $USERS->ByUname($guard_uname);
	if($guard_user->IsDead(true)) continue; //直前に死んでいたら無効

	//個別護衛成功判定
	$guard_flag |= ! ($ROOM->IsEvent('half_guard') && mt_rand(0, 1) > 0) &&
	  (! $guard_limited || $guard_user->IsRole(true, 'blind_guard', 'poison_guard'));

	if($guard_user->IsRole('blind_guard')) $user->AddRole('blinder'); //夜雀の処理

	if($guard_user->IsFirstGuardSuccess($target->uname)){ //護衛成功メッセージを登録
	  $ROOM->SystemMessage($guard_user->GetHandleName($target->uname), 'GUARD_SUCCESS');
	}
      }

      //スキップ判定
      if($target->IsDead(true) || $guard_flag || $target->IsRoleGroup('escaper') ||
	 in_array($target->uname, $trapped_list)) continue;

      if($target->IsRoleGroup('vampire')){ //吸血鬼襲撃判定
	if($target->IsRole('doom_vampire')) continue; //冥血鬼は無効
	$id = $target->IsRole('soul_vampire') ? $user->user_no : $target->user_no; //吸血姫は反射
	$vampire_killed_list[$id] = true;
      }
      elseif(! $target->IsCamp('vampire')){ //吸血成立判定
	$vampire_target_list[$user->uname][] = $target->uname;
      }
    }
    //PrintData($vampire_target_list, 'Target [vampire]');
    //PrintData($vampire_killed_list, 'Target [vampire_killed]');

    //吸血死処理 (罠死の方が優先されることに注意)
    foreach($vampire_killed_list as $id => $flag) $USERS->Kill($id, 'VAMPIRE_KILLED');

    //吸血処理 (吸血死より罠死の方が優先されることに注意)
    foreach($vampire_target_list as $uname => $stack){
      $ROLES->actor = $USERS->ByUname($uname);
      $filter = $ROLES->Load('main_role', true);
      foreach($stack as $target_uname) $filter->Infect($USERS->ByUname($target_uname));
    }

    $assassin_target_list = array(); //暗殺対象者リスト
    foreach($vote_data['ASSASSIN_DO'] as $uname => $target_uname){ //暗殺者の情報収集
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      if(in_array($target_uname, $trap_target_list)){ //罠死判定
	$trapped_list[] = $user->uname;
	continue;
      }
      //凍傷判定
      if(in_array($target_uname, $snow_trap_target_list)) $frostbite_list[] = $user->uname;

      $target = $USERS->ByUname($target_uname);

      $stack = array_keys($gatekeeper_guard_target_list, $target->uname); //門番の護衛判定
      if(count($stack) > 0){
	foreach($stack as $guard_uname){ //護衛成功メッセージを登録
	  $guard_user = $USERS->ByUname($guard_uname);
	  if($guard_user->IsFirstGuardSuccess($target->uname)){
	    $ROOM->SystemMessage($guard_user->GetHandleName($target->uname), 'GUARD_SUCCESS');
	  }
	}
	continue;
      }

      if($target->IsRoleGroup('escaper')) continue; //逃亡者は無効
      if($target->IsRefrectAssassin()){ //反射判定
	$assassin_target_list[$uname] = true;
	continue;
      }

      if($user->IsRole(true, 'reverse_assassin')){ //反魂対象者をリストに追加
	$reverse_assassin_target_list[$uname] = $target_uname;
	continue;
      }
      if($target->IsDead(true)) continue; //直前に死んでいたらスキップ

      if($user->IsRoleGroup(true, 'doom')){ //死の宣告能力者の処理
	$date = $user->IsRole(true, 'doom_fox') ? 4 :
	  ($user->IsRole('pierrot_wizard') ? GetRandom(range(2, 10)) : 2);
	$target->AddDoom($date);
	continue;
      }

      if($user->IsRole(true, 'soul_assassin')){ //辻斬りの処理
	$str = $user->GetHandleName($target->uname, $target->main_role);
	$ROOM->SystemMessage($str, 'ASSASSIN_RESULT'); //役職を登録

	//暗殺先が毒能力者なら死亡
	if($target->IsPoison()) $USERS->Kill($user->user_no, 'POISON_DEAD_night');
      }
      //蝕暗殺者の自滅判定
      elseif(! $ROOM->IsEvent('no_reflect_assassin') && $user->IsRole('eclipse_assassin')){
	if(mt_rand(1, 100) <= 30) $target_uname = $uname;
      }
      $assassin_target_list[$target_uname] = true; //暗殺対象者リストに追加
    }

    $ogre_target_list = array(); //人攫い対象者リスト
    foreach($vote_data['OGRE_DO'] as $uname => $target_uname){ //鬼の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      if(in_array($target_uname, $trap_target_list)){ //罠死判定
	$trapped_list[] = $user->uname;
	continue;
      }
      //凍傷判定
      if(in_array($target_uname, $snow_trap_target_list)) $frostbite_list[] = $user->uname;

      $target = $USERS->ByUname($target_uname);

      $stack = array_keys($gatekeeper_guard_target_list, $target->uname); //門番の護衛判定
      if(count($stack) > 0){
	foreach($stack as $guard_uname){ //護衛成功メッセージを登録
	  $guard_user = $USERS->ByUname($guard_uname);
	  if($guard_user->IsFirstGuardSuccess($target->uname)){
	    $ROOM->SystemMessage($guard_user->GetHandleName($target->uname), 'GUARD_SUCCESS');
	  }
	}
	continue;
      }

      if($target->IsDead(true) || $target->IsRoleGroup('escaper')) continue; //無効判定
      if($target->IsRefrectAssassin()){ //反射判定
	$ogre_target_list[$uname] = true;
	continue;
      }

      $ROLES->actor = $user;
      $filter = $ROLES->Load('main_role', true);
      if($user->IsRoleGroup('yaksa') && $filter->Ignored($target)) continue; //夜叉系の無効判定

      //人攫い成功判定
      $rate = mt_rand(1, 100); //襲撃成功判定乱数
      //$rate = 5; //テスト用
      $ogre_times = (int)$user->GetMainRoleTarget();
      //$ogre_times = 1; //テスト用
      if($ROOM->IsEvent('full_ogre'))
	$ogre_rate = 100;
      elseif($ROOM->IsEvent('seal_ogre'))
	$ogre_rate = 0;
      else
	$ogre_rate = ceil(100 * pow($filter->GetReduceRate(), $ogre_times));
      //PrintData($rate, 'Rate [OGRE_DO]: ' . $ogre_rate);

      if($rate > $ogre_rate) continue; //成功判定
      if($user->IsRole('poison_ogre')){ //榊鬼は解答者を追加
	if(! $target->IsRole('quiz')) $target->AddRole('panelist');
      }
      elseif($user->IsRole('sacrifice_ogre')){ //酒呑童子は洗脳者を追加
	if(! $target->IsCamp('vampire')) $target->AddRole('psycho_infected');
      }
      else{
	$ogre_target_list[$target_uname] = true; //人攫い対象者リストに追加
      }

      if($ROOM->IsEvent('full_ogre')) continue; //朧月ならスキップ
      $base_role = $user->main_role; //成功回数更新処理
      if($ogre_times > 0) $base_role .= '[' . $ogre_times . ']';
      $new_role = $user->main_role . '[' . ($ogre_times + 1) . ']';
      $user->ReplaceRole($base_role, $new_role);
    }

    //罠死処理
    foreach($trapped_list as $uname) $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');

    //PrintData($assassin_target_list, 'Target [assassin]');
    foreach($assassin_target_list as $uname => $flag){ //暗殺処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'ASSASSIN_KILLED');
    }

    //PrintData($ogre_target_list, 'Target [ogre]');
    foreach($ogre_target_list as $uname => $flag){ //人攫い処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'OGRE_KILLED');
    }

    $reverse_list = array(); //反魂対象リスト
    foreach($reverse_assassin_target_list as $uname => $target_uname){
      $target = $USERS->ByUname($target_uname);
      if($target->IsLive(true))
	$USERS->Kill($target->user_no, 'ASSASSIN_KILLED');
      elseif(! $target->IsLovers())
	$reverse_list[$target_uname] = ! $reverse_list[$target_uname];
    }
    //PrintData($reverse_list, 'ReverseList'); //テスト用

    foreach($frostbite_list as $uname){ //凍傷処理
      $target = $USERS->ByUname($uname);
      if($target->IsLive(true)) $target->AddDoom(1, 'frostbite');
    }

    //-- 夢系レイヤー --//
    foreach($vote_data['DREAM_EAT'] as $uname => $target_uname){ //獏の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      $str = "\t" . $user->handle_name;

      if($target->IsLiveRole('dummy_guard', true)){ //対象が夢守人なら返り討ちに合う
	$USERS->Kill($user->user_no, 'HUNTED');
	$ROOM->SystemMessage($target->handle_name . $str, 'GUARD_HUNTED');
	continue;
      }

      if(in_array($target->uname, $dummy_guard_target_list)){ //夢守人の護衛判定
	$hunted_flag = false;
	foreach(array_keys($dummy_guard_target_list, $target->uname) as $uname){ //護衛者を検出
	  $guard_user = $USERS->ByUname($uname);
	  if($guard_user->IsDead(true)) continue; //直前に死んでいたら無効
	  $hunted_flag = true;
	  $ROOM->SystemMessage($guard_user->handle_name . $str, 'GUARD_HUNTED');
	}

	if($hunted_flag){
	  $USERS->Kill($user->user_no, 'HUNTED');
	  continue;
	}
      }

      //夢食い判定 (夢系能力者・妖精系)
      if($target->IsRoleGroup('dummy', 'fairy')) $USERS->Kill($target->user_no, 'DREAM_KILLED');
    }

    $hunted_list = array(); //狩り成功者リスト
    foreach($dummy_guard_target_list as $uname => $target_uname){ //夢守人の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname);
      if(($target->IsRole('dream_eater_mad') || $target->IsRoleGroup('fairy')) &&
	 $target->IsLive(true)){ //狩り判定 (獏・妖精系)
	$hunted_list[$user->handle_name] = $target;
      }

      //常時護衛成功メッセージだけが出る
      $ROOM->SystemMessage($user->GetHandleName($target->uname), 'GUARD_SUCCESS');
    }

    foreach($hunted_list as $handle_name => $target){ //夢狩り処理
      $USERS->Kill($target->user_no, 'HUNTED');
      //憑依能力者は対象外なので仮想ユーザを引く必要なし
      $ROOM->SystemMessage($handle_name . "\t" . $target->handle_name, 'GUARD_HUNTED');
    }
    unset($hunted_list);

    //-- 呪い系レイヤー --//
    foreach($vote_data['ANTI_VOODOO_DO'] as $uname => $target_uname){ //厄神の情報収集
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname);
      $anti_voodoo_target_list[$user->uname] = $target->uname;

      //憑依予定先ならキャンセル
      $possessed_list = array_keys($possessed_target_list, $target->uname);
      if(count($possessed_list) > 0){
	foreach($possessed_list as $possessed_uname){
	  $USERS->ByUname($possessed_uname)->possessed_cancel = true;
	}
      }
      //憑依者なら強制送還
      elseif($target->IsPossessedGroup() && $target != $USERS->ByVirtual($target->user_no)){
	if(! array_key_exists($target->uname, $possessed_target_list)){
	  $possessed_target_list[$target->uname] = NULL; //憑依リストに追加
	}
	$target->possessed_reset = true;
      }
      //襲撃を行った憑狼ならキャンセル
      elseif($voted_wolf->IsRole('possessed_wolf') && $voted_wolf->IsSame($target->uname)){
	$voted_wolf->possessed_cancel = true;
      }
      else{
	continue;
      }
      $anti_voodoo_success_list[$target->uname] = true;
    }
    //PrintData($possessed_target_list, 'PossessedTarget [anti_voodoo]'); //テスト用
  }

  $voodoo_killer_target_list  = array(); //陰陽師の解呪対象リスト
  $voodoo_killer_success_list = array(); //陰陽師の解呪成功者対象リスト
  foreach($vote_data['VOODOO_KILLER_DO'] as $uname => $target_uname){ //陰陽師の情報収集
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname);

    //呪殺判定 (呪い所持者・憑依能力者)
    if($target->IsLive(true) && ($target->IsRoleGroup('cursed') || $target->IsPossessedGroup())){
      $USERS->Kill($target->user_no, 'CURSED');
      $voodoo_killer_success_list[$target->uname] = true;
    }

    //憑依予定先ならキャンセル
    $possessed_list = array_keys($possessed_target_list, $target->uname);
    if(count($possessed_list) > 0){
      foreach($possessed_list as $possessed_uname){
	$USERS->ByUname($possessed_uname)->possessed_cancel = true;
      }
      $voodoo_killer_success_list[$target->uname] = true;
    }

    $voodoo_killer_target_list[$user->uname] = $target->uname; //解呪対象リストに追加
  }

  //呪術系能力者の処理
  $voodoo_target_list = array(); //呪術系能力者の対象リスト
  foreach($vote_data['VOODOO_MAD_DO'] + $vote_data['VOODOO_FOX_DO'] as $uname => $target_uname){
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname);
    if(! $ROOM->IsEvent('no_cursed') && $target->IsLiveRoleGroup('cursed')){ //呪返し判定
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $voodoo_killer_target_list)) //陰陽師の解呪判定
      $voodoo_killer_success_list[$target->uname] = true;
    else
      $voodoo_target_list[$user->uname] = $target->uname;
  }

  //呪術系能力者の対象先が重なった場合は呪返しを受ける
  $voodoo_count_list = array_count_values($voodoo_target_list);
  foreach($voodoo_target_list as $uname => $target_uname){
    if($voodoo_count_list[$target_uname] < 2) continue;

    if(in_array($uname, $anti_voodoo_target_list)) //厄神の護衛判定
      $anti_voodoo_success_list[$uname] = true;
    else
      $USERS->Kill($USERS->UnameToNumber($uname), 'CURSED');
  }

  //-- 占い系レイヤー --//
  $jammer_target_list = array(); //妨害対象リスト
  foreach($vote_data['JAMMER_MAD_DO'] as $uname => $target_uname){ //月兎・月狐の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname); //対象者の情報を取得
    //呪返し判定
    if((! $ROOM->IsEvent('no_cursed') && $target->IsLiveRoleGroup('cursed')) ||
       in_array($target->uname, $voodoo_target_list)){
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
      $anti_voodoo_success_list[$target->uname] = true;
    }
    else{ //妨害対象者リストに追加
      if($user->IsRole('jammer_fox') && mt_rand(1, 10) > 7) continue; //月狐は一定確率で失敗する
      $jammer_target_list[$user->uname] = $target->uname;
    }
  }
  //PrintData($jammer_target_list, 'Target [jammer_mad]');

  //占い能力者の処理を合成 (array_merge() は $uname が整数だと添え字と認識されるので使わないこと)
  $mage_list = array();
  foreach(array('MAGE_DO', 'CHILD_FOX_DO', 'FAIRY_DO') as $action){
    $mage_list += $vote_data[$action];
  }
  $flower_list = range('A', 'Z'); //花妖精・星妖精のメッセージ作成用リスト

  $phantom_user_list = array(); //幻系の発動者リスト
  foreach($mage_list as $uname => $target_uname){ //占い系の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByRealUname($target_uname);
    if($user->IsRole('dummy_mage')){ //夢見人の判定
      if($ROOM->IsEvent('no_dream')) continue; //熱帯夜ならスキップ
      $result = $target->DistinguishMage(true);
    }
    else{
      //-- 占い妨害判定 --//
      $half_moon_flag = $ROOM->IsEvent('half_moon') && mt_rand(0, 1) > 0; //半月の判定
      $phantom_flag   = $target->IsAbilityPhantom(); //幻系の判定
      //厄神の護衛判定
      if(($half_moon_flag || $phantom_flag) &&
	 in_array($user->uname, $anti_voodoo_target_list)){
	$anti_voodoo_success_list[$user->uname] = true;
	$phantom_flag   = false;
	$half_moon_flag = false;
      }

      //月兎・月狐の妨害判定
      if($half_moon_flag || in_array($user->uname, $jammer_target_list)){
	$result = $user->IsRole(true, 'psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
      }
      elseif($phantom_flag){
	$result = $user->IsRole(true, 'psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
	$phantom_user_list[] = $target;
      }
      elseif($user->IsActive('awake_wizard') && mt_rand(1, 10) > 3){ //比丘尼の失敗判定
	$result = 'failed';
      }
      elseif($user->IsRole(true, 'psycho_mage')){ //精神鑑定士の判定
	$result = $target->DistinguishLiar();
      }
      elseif($user->IsRole(true, 'sex_mage')){ //ひよこ鑑定士の判定
	$result = $target->DistinguishSex();
      }
      elseif($user->IsRole(true, 'stargazer_mage')){ //占星術師の判定
	$result = $target->DistinguishVoteAbility();
      }
      elseif($user->IsRole('sex_fox')){ //雛狐の判定
	$result = mt_rand(1, 10) > 7 ? 'failed' : $target->DistinguishSex();
      }
      elseif($user->IsRole('stargazer_fox')){ //星狐の判定
	$result = mt_rand(1, 10) > 7 ? 'failed' : $target->DistinguishVoteAbility();
      }
      else{
	//呪返し判定
	if((! $ROOM->IsEvent('no_cursed') &&$target->IsLiveRoleGroup('cursed')) ||
	   in_array($target->uname, $voodoo_target_list)){
	  if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	    $anti_voodoo_success_list[$user->uname] = true;
	  }
	  else{
	    $USERS->Kill($user->user_no, 'CURSED');
	    continue;
	  }
	}

	if($user->IsRole('emerald_fox')){ //翠狐の処理
	  if($target->IsChildFox() || $target->IsLonely('fox')){
	    $role = $user->GetID('mind_friend');
	    $user->LostAbility();
	    $user->AddRole($role);
	    $target->AddRole($role);
	  }
	}
	elseif($user->IsRole('child_fox')){ //子狐の判定
	  $result = mt_rand(1, 10) > 7 ? 'failed' : $target->DistinguishMage();
	}
	elseif($user->IsRole(true, 'flower_fairy')){ //花妖精の処理
	  $action = 'FLOWERED_' . GetRandom($flower_list);
	  $ROOM->SystemMessage($USERS->GetHandleName($target->uname), $action);
	}
	elseif($user->IsRole(true, 'star_fairy')){ //星妖精の処理
	  $action = 'CONSTELLATION_' . GetRandom($flower_list);
	  $ROOM->SystemMessage($USERS->GetHandleName($target->uname), $action);
	}
	elseif($user->IsRole(true, 'pierrot_fairy')){ //道化師 (妖精タイプ) の処理
	  $action = 'PIERROT_' . GetRandom($flower_list);
	  $ROOM->SystemMessage($USERS->GetHandleName($target->uname), $action);
	}
	elseif($user->IsRole(true, 'ice_fairy')){ //氷妖精の処理
	  mt_rand(1, 10) > 7 ? $user->AddDoom(1, 'frostbite') : $target->AddDoom(1, 'frostbite');
	}
	//狢・妖精系の処理
	elseif($user->IsRoleGroup(true, 'fairy') || $user->IsRole('enchant_mad')){
	  $target_date = $ROOM->date + 1;
	  $target->AddRole("bad_status[{$user->user_no}-{$target_date}]");
	}
	else{
	  if(array_key_exists($target->uname, $possessed_target_list)){ //憑依キャンセル判定
	    $target->possessed_cancel = true;
	  }

	  if($user->IsRole(true, 'soul_mage')){ //魂の占い師の判定
	    $result = $target->main_role;
	  }
	  else{ //占い師の処理
	    //呪殺判定
	    if($target->IsLive(true) && $target->IsFox() && ! $target->IsChildFox() &&
	       ! $target->IsRole('white_fox', 'black_fox') && ! $ROOM->IsEvent('no_fox_dead')){
	      $USERS->Kill($target->user_no, 'FOX_DEAD');
	    }
	    $result = $target->DistinguishMage(); //占い判定
	  }
	}
      }
    }

    //占い結果を登録 (特殊占い能力者は除外)
    if($user->IsRole('emerald_fox') || $user->IsRoleGroup(true, 'fairy')) continue;
    $action = $user->IsChildFox() ? 'CHILD_FOX_RESULT' : 'MAGE_RESULT';
    $ROOM->SystemMessage($user->GetHandleName($target->uname, $result), $action);
  }
  foreach($phantom_user_list as $user) $user->LostAbility(); //幻系の能力失効処理

  if($ROOM->date == 1){
    //-- コピー系レイヤー --//
    foreach($vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){ //さとり系の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $ROLES->actor = $user;
      $role = $ROLES->Load('main_role', true)->mind_role;
      $USERS->ByUname($target_uname)->AddRole($user->GetID($role));
    }

    foreach($vote_data['MANIA_DO'] as $uname => $target_uname){ //神話マニア系の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $ROLES->actor = $user;
      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      if($user->IsUnknownMania()){ //鵺系
	$user->AddMainRole($target->user_no); //コピー先をセット

	//共鳴者を追加
	$role = $user->GetID('mind_friend');
	$user->AddRole($role);

	$ROLES->Load('main_role', true)->AddManiaRole(&$role); //追加役職を取得
	$target->AddRole($role);
	continue;
      }

      $result = $ROLES->Load('main_role', true)->Copy($target, $vote_data); //コピー処理
      //コピー結果を出力
      $str = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
      $ROOM->SystemMessage($str, 'MANIA_RESULT');
    }

    if(! $ROOM->IsOpenCast()){
      foreach($USERS->rows as $user){ //天人の帰還処理
	if($user->IsDummyBoy() || ! $user->IsRole('revive_priest')) continue;
	if($user->IsLovers()) $user->LostAbility();
	elseif($user->IsLive(true)) $USERS->Kill($user->user_no, 'PRIEST_RETURNED');
      }
    }

    //魂移使の処理
    $exchange_angel_list  = array();
    $exchange_lovers_list = array();
    $fix_angel_stack      = array();
    $exec_exchange_stack  = array();
    foreach($USERS->rows as $user){ //魂移使が打った恋人の情報を収集
      if($user->IsDummyBoy() || ! $user->IsLovers()) continue;
      foreach($user->GetPartner('lovers') as $cupid_id){
	if($USERS->ById($cupid_id)->IsRole('exchange_angel')){
	  $exchange_angel_list[$cupid_id][] = $user->user_no;
	  $exchange_lovers_list[$user->user_no][] = $cupid_id;
	  if($user->IsPossessedGroup()) $fix_angel_stack[$cupid_id] = true; //憑依能力者なら対象外
	}
      }
    }
    //PrintData($exchange_angel_list, 'exchange_angel: 1st');
    //PrintData($exchange_lovers_list, 'exchange_lovers: 1st');

    foreach($exchange_angel_list as $id => $lovers_stack){ //抽選処理
      if(array_key_exists($id, $fix_angel_stack)) continue;
      $duplicate_stack = array();
      //PrintData($fix_angel_stack, 'fix_angel:'. $id);
      foreach($lovers_stack as $lovers_id){
	foreach($exchange_lovers_list[$lovers_id] as $cupid_id){
	  if(! array_key_exists($cupid_id, $fix_angel_stack)) $duplicate_stack[$cupid_id] = true;
	}
      }
      //PrintData($duplicate_stack, 'duplicate:' . $id);
      $duplicate_list = array_keys($duplicate_stack);
      if(count($duplicate_list) > 1){
	$exec_exchange_stack[] = GetRandom($duplicate_list);
	foreach($duplicate_list as $duplicate_id) $fix_angel_stack[$duplicate_id] = true;
      }
      else{
	$exec_exchange_stack[] = $id;
      }
      $fix_angel_stack[$id] = true;
    }
    //PrintData($exec_exchange_stack, 'exec_exchange');

    foreach($exec_exchange_stack as $id){
      $target_list = $exchange_angel_list[$id];
      $lovers_a = $USERS->ByID($target_list[0]);
      $lovers_b = $USERS->ByID($target_list[1]);
      $lovers_a->AddRole('mind_sympathy possessed_exchange[' . $target_list[1] . ']');
      $sentence = $lovers_a->handle_name . "\t" . $lovers_b->handle_name . "\t";
      $ROOM->SystemMessage($sentence . $lovers_b->main_role, 'SYMPATHY_RESULT');

      $lovers_b->AddRole('mind_sympathy possessed_exchange[' . $target_list[0] . ']');
      $sentence = $lovers_b->handle_name . "\t" . $lovers_a->handle_name . "\t";
      $ROOM->SystemMessage($sentence . $lovers_a->main_role, 'SYMPATHY_RESULT');
    }
  }
  else{
    //-- 尾行系レイヤー --//
    foreach($vote_data['REPORTER_DO'] as $uname => $target_uname){ //ブン屋の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      if(in_array($target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$USERS->Kill($user->user_no, 'TRAPPED');
	continue;
      }
      //凍傷判定
      if(in_array($target->uname, $snow_trap_target_list)) $user->AddDoom(1, 'frostbite');

      if($target->IsSame($wolf_target->uname)){ //尾行成功
	if(! $target->wolf_killed) continue; //人狼に襲撃されていなかったらスキップ
	$str = $user->GetHandleName($wolf_target->uname,
				    $USERS->GetHandleName($voted_wolf->uname, true));
	$ROOM->SystemMessage($str, 'REPORTER_SUCCESS');
      }
      elseif($target->IsLiveRoleGroup('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
	$USERS->Kill($user->user_no, 'REPORTER_DUTY');
      }
    }

    foreach($vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){ //猩々の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname);
      if(in_array($target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$USERS->Kill($user->user_no, 'TRAPPED');
	continue;
      }
      //凍傷判定
      if(in_array($target->uname, $snow_trap_target_list)) $user->AddDoom(1, 'frostbite');

      /*
	複数の投票イベントを持つタイプが出現した場合は複数のメッセージを発行する必要がある
	対象が NULL でも有効になるタイプ (キャンセル投票はスキップ) は想定していない
      */
      foreach($vote_data as $action => $vote_stack){
	if(strpos($action, '_NOT_DO') !== false ||
	   ! array_key_exists($target->uname, $vote_stack)) continue;
	$str = $user->GetHandleName($target->uname) . "\t";
	$target_stack = $vote_stack[$target->uname];

	if($target->IsRole('barrier_wizard')){
	  $str_stack = array();
	  foreach(explode(' ', $target_stack) as $id){
	    $voted_user = $USERS->ByVirtual($id);
	    $str_stack[$voted_user->user_no] = $str . $voted_user->handle_name;
	  }
	  ksort($str_stack);
	  foreach($str_stack as $str) $ROOM->SystemMessage($str, 'CLAIRVOYANCE_RESULT');
	}
	else{
	  $str .= $USERS->GetHandleName($target_stack, true);
	  $ROOM->SystemMessage($str, 'CLAIRVOYANCE_RESULT');
	}
	break;
      }
    }
  }

  //-- 反魂系レイヤー --//
  if(! $ROOM->IsEvent('no_revive')){ //快晴なら無効
    //身代わり君・恋人・完全覚醒天狼なら無効
    if($wolf_target->IsDead(true) && ! $wolf_target->IsDummyBoy() && ! $wolf_target->IsLovers() &&
       $wolf_target->wolf_killed  && ! $voted_wolf->IsSiriusWolf()){
      //仙人・蛇神・西蔵人形・夜刀神の蘇生判定
      if($wolf_target->IsRole('revive_pharmacist', 'revive_brownie', 'revive_doll',
			      'revive_avenger') && $wolf_target->IsActive()){
	$wolf_target->Revive();
	$wolf_target->LostAbility();
      }
      //茨木童子の蘇生判定
      elseif($wolf_target->IsRole('revive_ogre') && ! $ROOM->IsEvent('seal_ogre') &&
	     ($ROOM->IsEvent('full_ogre') || mt_rand(1, 100) <= 40)){
	$wolf_target->Revive();
      }
    }

    foreach($USERS->rows as $user){ //仙狼の処理
      //夜に死亡 + 能力が有効な場合のみ蘇生する
      if($user->IsActive('revive_wolf') && ! $user->IsLovers() && $user->IsLive() &&
	 $user->IsDead(true)){
	$user->Revive();
	$user->LostAbility();
      }
    }
  }

  if($ROOM->date > 1){
    foreach($reverse_list as $target_uname => $flag){ //反魂師の処理
      if(! $flag) continue;
      $target = $USERS->ByUname($target_uname);
      if($target->IsPossessedGroup()){ //憑依能力者対応
	if($target->revive_flag) break; //蘇生済みならスキップ

	$virtual_target = $USERS->ByVirtual($target->user_no);
	if($target != $virtual_target){ //憑依中ならリセット
	  $target->ReturnPossessed('possessed_target', $ROOM->date + 1); //本人
	  $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1); //憑依先
	}

	//憑依予定者が居たらキャンセル
	if(array_key_exists($target->uname, $possessed_target_list)){
	  $target->possessed_reset  = false;
	  $target->possessed_cancel = true;
	}
	elseif(in_array($target->uname, $possessed_target_list)){
	  //憑依中の犬神に憑依しようとした憑狼を検出
	  $stack = array_keys($possessed_target_list, $target->uname);
	  $USERS->ByUname($stack[0])->possessed_cancel = true;
	}

	//特殊ケースなのでベタに処理
	$virtual_target->Update('live', 'live');
	$virtual_target->revive_flag = true;
	$ROOM->SystemMessage($virtual_target->handle_name, 'REVIVE_SUCCESS');
      }
      else{
	if($target != $USERS->ByReal($target->user_no)){ //憑依されていたらリセット
	  $target->ReturnPossessed('possessed', $ROOM->date + 1);
	}
	$target->Revive(); //蘇生処理
      }
    }

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

	$target = $USERS->ByUname($target_uname); //対象者の情報を取得

	//蘇生判定
	$missfire_rate = 0; //誤爆率
	if($ROOM->IsEvent('full_revive')){ //雷雨
	  $revive_rate = 100;
	  $missfire_rate = -1;
	}
	elseif($ROOM->IsEvent('no_revive')){ //快晴
	  $revive_rate = 0;
	}
	elseif($user->IsRole('revive_cat')){ //仙狸
	  $revive_times = (int)$user->GetMainRoleTarget();
	  $revive_rate = ceil(80 / pow(4, $revive_times));
	}
	elseif($user->IsRole('sacrifice_cat')){ //猫神
	  $revive_rate = 100;
	  $missfire_rate = -1;
	}
	elseif($user->IsRole('eclipse_cat')){ //蝕仙狸
	  $revive_rate = 40;
	  $missfire_rate = 20;
	}
	elseif($user->IsRole('revive_fox')){ //仙狐
	  $revive_rate = 100;
	}
	else{
	  $revive_rate = 25;
	}
	$rate = mt_rand(1, 100); //蘇生判定用乱数
	if($boost_revive) $revive_rate *= 1.3;
	if($revive_rate > 100) $revive_rate = 100; //上限は 100%
	if($missfire_rate == 0) $missfire_rate = floor($revive_rate / 5);
	if($ROOM->IsEvent('missfire_revive')) $missfire_rate *= 2;
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

	    $virtual_target = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //確定死者
	      if($target != $virtual_target){ //憑依後に死亡していた場合はリセット処理を行う
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
		//憑依先が他の憑依能力者に憑依されていないのならリセット処理を行う
		$stack = $virtual_target->GetPartner('possessed');
		if($target->user_no == $stack[max(array_keys($stack))]){
		  $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
		}
	      }
	    }
	    elseif($target->IsLive(true)){ //生存者 (憑依状態確定)
	      if($virtual_target->IsDrop()){ //蘇生辞退者対応
		$result = 'failed';
		break;
	      }

	      //見かけ上の蘇生処理
	      $target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      $ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');

	      //本当の死者の蘇生処理
	      $virtual_target->Revive(true);
	      $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);

	      //憑依予定者が居たらキャンセル
  	      if(array_key_exists($target->uname, $possessed_target_list)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	      break;
	    }
	    else{ //当夜に死んだケース
	      if($target != $virtual_target){ //憑依中ならリセット
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1); //本人
		$virtual_target->ReturnPossessed('possessed', $ROOM->date + 1); //憑依先
	      }

	      //憑依予定者が居たらキャンセル
	      if(array_key_exists($target->uname, $possessed_target_list)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	    }
	  }
	  elseif($target != $USERS->ByReal($target->user_no)){ //憑依されていたらリセット
	    $target->ReturnPossessed('possessed', $ROOM->date + 1);
	  }
	  $target->Revive(); //蘇生処理
	}while(false);

	if($result == 'success'){
	  if(! $ROOM->IsEvent('full_revive')){ //雷雨ならスキップ
	    if($user->IsRole('revive_cat')){ //仙狸の蘇生成功カウントを更新
	      //$revive_times = (int)$user->GetMainRoleTarget(); //取得済みのはず
	      $base_role = $user->main_role;
	      if($revive_times > 0) $base_role .= '[' . $revive_times . ']';

	      $new_role = $user->main_role . '[' . ($revive_times + 1) . ']';
	      $user->ReplaceRole($base_role, $new_role);
	    }
	    elseif($user->IsRole('sacrifice_cat')){ //猫神の死亡処理
	      $USERS->Kill($user->user_no, 'SACRIFICE');
	    }
	    elseif($user->IsRole('revive_fox')){ //仙狐の能力失効処理
	      $user->LostAbility();
	    }
	  }
	}
	else{
	  $ROOM->SystemMessage($target->handle_name, 'REVIVE_FAILED');
	}
	$str = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname) . "\t" . $result;
	$ROOM->SystemMessage($str, 'POISON_CAT_RESULT');
      }
    }
  }

  //-- 憑依レイヤー --//
  //PrintData($possessed_target_list, 'Target [possessed_wolf]');
  if($ROOM->date > 1){
    //憑依能力者の処理
    $possessed_do_stack = array(); //有効憑依情報リスト (死亡判定と厄神リセット判定)
    foreach($vote_data['POSSESSED_DO'] as $uname => $target_uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true) || $user->revive_flag) continue; //直前に死んでいたら無効 (蘇生でも無効)

      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[$user->uname] = true;
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
      //失敗判定2：蘇生されている / 憑狼の憑依制限役職である
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
      $possessed_target_list[$user->uname] = $target_uname;
    }
    //PrintData($possessed_target_list, 'Target [Possessed]');
  }

  //-- 憑依処理 --//
  $possessed_date = $ROOM->date + 1; //憑依する日を取得
  foreach($possessed_target_list as $uname => $target_uname){
    $user         = $USERS->ByUname($uname); //憑依者
    $target       = $USERS->ByUname($target_uname); //憑依予定先
    $virtual_user = $USERS->ByVirtual($user->user_no); //現在の憑依先
    $array = array(); //一時処理

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

      if($user != $virtual_user){ //憑依中なら元の体に戻される
	//憑依先のリセット処理
	$virtual_user->ReturnPossessed('possessed', $possessed_date);
	$virtual_user->SaveLastWords();
	$ROOM->SystemMessage($virtual_user->handle_name, 'POSSESSED_RESET');

	//見かけ上の蘇生処理
	$user->ReturnPossessed('possessed_target', $possessed_date);
	$user->SaveLastWords($virtual_user->handle_name);
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
      $ROOM->SystemMessage($virtual_user->handle_name, 'POSSESSED');
      $user->SaveLastWords($virtual_user->handle_name);
      $user->Update('last_words', '');
    }

    if($user != $virtual_user){
      $virtual_user->ReturnPossessed('possessed', $possessed_date);
      if($user->IsLive(true)) $virtual_user->SaveLastWords();
    }
  }

  //PrintData($voodoo_killer_success_list, 'SUCCESS [voodoo_killer]');
  foreach($voodoo_killer_success_list as $target_uname => $flag){ //陰陽師の解呪結果処理
    $str = "\t" . $USERS->GetHandleName($target_uname, true);
    foreach(array_keys($voodoo_killer_target_list, $target_uname) as $uname){ //成功者を検出
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $str, 'VOODOO_KILLER_SUCCESS');
    }
  }

  //PrintData($anti_voodoo_success_list, 'SUCCESS [anti_voodoo]');
  foreach($anti_voodoo_success_list as $target_uname => $flag){ //厄神の厄払い結果処理
    $str = "\t" . $USERS->GetHandleName($target_uname, true);
    foreach(array_keys($anti_voodoo_target_list, $target_uname) as $uname){ //成功者を検出
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $str, 'ANTI_VOODOO_SUCCESS');
    }
  }

  if($ROOM->date == 3){ //覚醒者・夢語部のコピー処理
    foreach($USERS->rows as $user){
      if($user->IsDummyBoy() || ! $user->IsRole('soul_mania', 'dummy_mania')) continue;
      $target = $USERS->ById($user->GetMainRoleTarget());
      //PrintData($target->DistinguishRoleGroup(), $user->uname);
      $ROLES->actor = $user;
      $filter = $ROLES->Load('main_role', true);
      $result = $filter->GetRole($target);
      $user->ReplaceRole($target->GetID($user->main_role), $result);
      $user->AddRole($filter->copied);
      $ROOM->SystemMessage(str_repeat($user->handle_name . "\t", 2) . $result, 'MANIA_RESULT');
    }
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  //-- 司祭系レイヤー --//
  unset($role_flag); //役職出現判定フラグを初期化
  foreach($USERS->rows as $user){
    if($user->IsLive(true)) $role_flag->{$user->main_role}[$user->user_no] = $user->uname;
  }
  //PrintData($role_flag);

  if($ROOM->date > 1 && count($role_flag->attempt_necromancer) > 0){ //蟲姫の処理
    $stack = array();
    if($wolf_target->IsLive(true)) $stack[$wolf_target->uname] = true; //人狼襲撃
    foreach($vote_data['ASSASSIN_DO'] as $uname){ //暗殺
      if($USERS->ByUname($uname)->IsLive(true)) $stack[$uname] = true;
    }
    foreach($vote_data['OGRE_DO'] as $uname){ //人攫い
      if($USERS->ByUname($uname)->IsLive(true)) $stack[$uname] = true;
    }
    //PrintData($stack);
    $str_stack = array();
    foreach(array_keys($stack) as $uname){ //仮想ユーザの ID 順に出力
      $user = $USERS->ByVirtualUname($uname);
      $str_stack[$user->user_no] = $user->handle_name . "\t" . 'attempt';
    }
    ksort($str_stack);
    foreach($str_stack as $str) $ROOM->SystemMessage($str, 'ATTEMPT_NECROMANCER_RESULT');
  }

  $live_count = array('total' => 0, 'human' => 0, 'wolf' => 0, 'fox' => 0, 'lovers' => 0,
		      'human_side' => 0, 'dead' => 0, 'dream' => 0, 'sub_role' => 0);
  $dowser_priest_flag  = count($role_flag->dowser_priest)  > 0;
  $weather_priest_flag = count($role_flag->weather_priest) > 0;
  $crisis_priest_flag  = count($role_flag->crisis_priest)  > 0;
  $dummy_priest_flag   = count($role_flag->dummy_priest)   > 0 && ! $ROOM->IsEvent('no_dream');
  $revive_priest_list  = array();
  foreach($USERS->rows as $user){ //司祭系の情報収集
    if(! $user->IsDummyBoy() && $user->IsActive('revive_priest')) $revive_priest_list[] = $user;
    if($user->IsDead(true)){
      if(! $user->IsCamp('human', true)) $live_count['dead']++;
      continue;
    }
    $live_count['total']++;

    if($user->IsWolf())    $live_count['wolf']++;
    elseif($user->IsFox()) $live_count['fox']++;
    else{
      $live_count['human']++;
      if($user->IsCamp('human')) $live_count['human_side']++;
    }
    if($user->IsLovers()) $live_count['lovers']++;
    if($dowser_priest_flag){
      $dummy_user = new User();
      $dummy_user->ParseRoles($user->GetRole());
      $live_count['sub_role'] += count($dummy_user->role_list) - 1;
    }
    if($dummy_priest_flag && $user->IsRoleGroup('dummy', 'fairy')) $live_count['dream']++;
  }
  //PrintData($live_count, 'LiveCount');

  if($ROOM->date > 2 && ($ROOM->date % 2) == 1){ //司祭・探知師・大司祭・夢司祭・恋司祭の処理
    if(count($role_flag->priest) > 0 || (count($role_flag->high_priest) > 0 && $ROOM->date > 4)){
      $ROOM->SystemMessage($live_count['human_side'], 'PRIEST_RESULT');
    }
    if($dowser_priest_flag){
      $ROOM->SystemMessage($live_count['sub_role'], 'DOWSER_PRIEST_RESULT');
    }
    if($dummy_priest_flag){
      $ROOM->SystemMessage($live_count['dream'], 'DUMMY_PRIEST_RESULT');
    }
    if(count($role_flag->priest_jealousy) > 0){
      $ROOM->SystemMessage($live_count['lovers'], 'PRIEST_JEALOUSY_RESULT');
    }
  }

  //司教・大司祭の処理
  if(($ROOM->date % 2) == 0 &&
     ((count($role_flag->bishop_priest) > 0 && $ROOM->date > 1) ||
      (count($role_flag->high_priest)   > 0 && $ROOM->date > 3))){
    $ROOM->SystemMessage($live_count['dead'], 'BISHOP_PRIEST_RESULT');
  }

  if(count($role_flag->border_priest) > 0 && $ROOM->date > 1){ //境界師の処理
    foreach($role_flag->border_priest as $uname){
      $user  = $USERS->ByUname($uname);
      $count = 0;
      foreach($ROOM->vote as $stack){
	if($user->IsSame($stack['target_uname'])) $count++;
      }
      $ROOM->SystemMessage($user->handle_name . "\t" . $count, 'BORDER_PRIEST_RESULT');
    }
  }

  if($ROOM->date == 1 && $ROOM->IsDummyBoy() && count($role_flag->widow_priest) > 0){ //未亡人の処理
    $dummy_boy = $USERS->ByID(1);
    $str = "\t" . $dummy_boy->handle_name . "\t" . $dummy_boy->main_role;
    foreach($role_flag->widow_priest as $uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDummyBoy()) continue;
      $user->AddRole('mind_sympathy');
      $ROOM->SystemMessage($user->handle_name . $str, 'SYMPATHY_RESULT');
    }
  }

  if($crisis_priest_flag || count($revive_priest_list) > 0){ //預言者・天人の処理
    $crisis_priest_result = ''; //「人外勝利前日」判定
    if($live_count['total'] - $live_count['lovers'] <= 2){
      $crisis_priest_result = 'lovers';
    }
    elseif($live_count['human'] - $live_count['wolf'] <= 2 || $live_count['wolf'] == 1){
      if($live_count['lovers'] > 1)
	$crisis_priest_result = 'lovers';
      elseif($live_count['fox'] > 0)
	$crisis_priest_result = 'fox';
      elseif($live_count['human'] - $live_count['wolf'] <= 2)
	$crisis_priest_result = 'wolf';
    }

    if($crisis_priest_flag && $crisis_priest_result != ''){ //預言者の処理
      $ROOM->SystemMessage($crisis_priest_result, 'CRISIS_PRIEST_RESULT');
    }

    //天人の蘇生判定処理
    if(! $ROOM->IsOpenCast() && count($revive_priest_list) > 0 &&
       ($ROOM->date == 4 || $crisis_priest_result != '' || $live_count['wolf'] == 1 ||
	count($USERS->rows) >= $live_count['total'] * 2)){
      foreach($revive_priest_list as $user){
	if($user->IsLovers() || ($ROOM->date >= 4 && $user->IsLive(true))){
	  $user->LostAbility();
	}
	elseif($user->IsDead(true)){
	  $user->Revive();
	  $user->LostAbility();
	}
      }
    }
  }

  //祈祷師・天候あり判定
  if(($ROOM->IsOption('weather') && ($ROOM->date % 3) == 1) ||
     ($weather_priest_flag && $ROOM->date > 2 && ($ROOM->date % 3) == 0 &&
      $live_count['total'] - $live_count['human_side'] > $live_count['wolf'] * 2)){
    //天候補正処理
    $vote_margin = ceil(($live_count['total'] - 2) / 2) - $live_count['wolf'] - $live_count['fox'];
    //PrintData($vote_margin, 'VoteMargin');

    $target =& $GAME_CONF->weather_list;
    if($live_count['fox'] > $live_count['wolf']){ //妖狐陣営優勢
      foreach(array(3, 8, 31, 36) as $id){
	$target[$id] = ceil($target[$id] * 0.8);
      }
    }

    if($vote_margin > 2){ //村人陣営優勢
      foreach(array(17, 18, 20, 23, 30, 33, 35, 37, 41, 45, 47) as $id){
	$target[$id] = ceil($target[$id] * 1.2);
      }
      foreach(array(6, 7, 9, 16, 22, 32, 34, 46) as $id){
	$target[$id] = ceil($target[$id] * 0.8);
      }
    }
    elseif($vote_marget < 1){ //村人陣営劣勢
      foreach(array(6, 7, 8, 9, 32, 34, 42, 46) as $id){
	$target[$id] = ceil($target[$id] * 1.2);
      }
      foreach(array(4, 5, 17, 18, 23, 33, 37, 39, 45, 47) as $id){
	$target[$id] = ceil($target[$id] * 0.8);
      }
    }

    $stack = array(
      'human' => 24, 'suspect' => 42, 'bacchus_medium' => 21, 'brownie' => 24,
      'revive_brownie' => 22, 'cursed_brownie' => 17, 'jammer_mad' => 36, 'trap_mad' => 37,
      'snow_trap_mad' => 33, 'corpse_courier_mad' => 45, 'amaze_mad' => 2, 'critical_mad' => 4,
      'follow_mad' => 17, 'critical_avenger' => 4);
    foreach($role_flag as $role => $list){
      $id = NULL;
      if(array_key_exists($role, $stack))
	$id = $stack[$role];
      elseif(strpos($role, 'cute') !== false)
	$id = 42;
      elseif(strpos($role, 'jealousy') !== false)
	$id = 27;
      elseif(strpos($role, 'vampire') !== false)
	$id = 40;
      elseif(strpos($role, 'fairy') !== false)
	$id = 29;
      //PrintData($role, $id);
      if(isset($id)) $target[$id] = ceil($target[$id] * (1 + count($list) * 0.1));
    }
    /*
    PrintData($GAME_CONF->weather_list);
    $stack = array();
    for($i = 0; $i < 20; $i++) $stack[$GAME_CONF->GetWeather()]++;
    PrintData($stack);
    */
    $weather = $GAME_CONF->GetWeather();
    //$weather = 44; //テスト用
    $date = 2;
    $ROOM->EntryWeather($weather, $date, $weather_priest_flag);
  }

  $status = $ROOM->test_mode || $ROOM->ChangeDate();
  if($ROOM->test_mode || ! $status) $USERS->ResetJoker(true); //ジョーカー再配置処理
  return $status;
}

//ランダムメッセージを挿入する
function InsertRandomMessage(){
  global $GAME_CONF, $MESSAGE, $ROOM;
  if($GAME_CONF->random_message) $ROOM->Talk(GetRandom($MESSAGE->random_message_list));
}
