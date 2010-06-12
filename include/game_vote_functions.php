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
  global $SERVER_CONF, $RQ_ARGS;

  if($reset_vote) DeleteVote(); //今までの投票を全部削除
  $title  = $SERVER_CONF->title . ' [投票結果]';
  $header = '<div align="center"><a name="#game_top"></a>';
  $footer = '<br>'."\n" . $RQ_ARGS->back_url . '</div>';
  OutputActionResult($title, $header . $sentence . $footer, '', $unlock);
}

//人数とゲームオプションに応じた役職テーブルを返す (エラー処理は暫定)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $CAST_CONF, $ROOM;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $CAST_CONF->role_list[$user_count]; //人数に応じた設定リストを取得
  if($role_list == NULL){ //リストの有無をチェック
    $sentence = $user_count . '人は設定されていません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  if($ROOM->IsQuiz()){ //クイズ村
    $quiz_role_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false)
	$quiz_role_list['wolf'] += (int)$value;
      elseif(strpos($key, 'mad') !== false)
	$quiz_role_list['mad'] += (int)$value;
      elseif(strpos($key, 'common') !== false)
	$quiz_role_list['common'] += (int)$value;
      elseif(strpos($key, 'fox') !== false)
	$quiz_role_list['fox'] += (int)$value;
      else
	$quiz_role_list['human'] += (int)$value;
    }
    $quiz_role_list['human']--;
    $quiz_role_list['quiz'] = 1;
    $role_list = $quiz_role_list;
  }
  elseif(strpos($option_role, 'duel') !== false){ //決闘村
    $role_list = $CAST_CONF->SetDuel($user_count);
  }
  elseif($ROOM->IsOption('chaosfull')){ //真・闇鍋
    $random_role_list = array();

    //-- 最小補正 --//
    //探偵村なら固定枠に追加する
    if(strpos($option_role, 'detective') !== false &&
       is_null($CAST_CONF->chaos_fix_role_list['detective_common'])){
       $CAST_CONF->chaos_fix_role_list['detective_common'] = 1;
    }

    foreach($CAST_CONF->chaos_fix_role_list as $key => $value){ //最小補正用リスト
      $fix_role_group_list[DistinguishRoleGroup($key)] = $value;
    }

    //人狼
    $random_wolf_list = $CAST_CONF->GenerateRandomList($CAST_CONF->chaos_wolf_list);
    //PrintData($random_wolf_list); //テスト用
    //$CAST_CONF->RateToProbability($CAST_CONF->chaos_wolf_list); //テスト用

    $add_count = round($user_count / $CAST_CONF->chaos_min_wolf_rate) - $fix_role_group_list['wolf'];
    $CAST_CONF->AddRandom($random_role_list, $random_wolf_list, $add_count);
    //PrintData($random_role_list); //テスト用

    //妖狐
    $random_fox_list = $CAST_CONF->GenerateRandomList($CAST_CONF->chaos_fox_list);
    //PrintData($random_fox_list); //テスト用
    //$CAST_CONF->RateToProbability($chaos_fox_list); //テスト用

    $add_count = floor($user_count / $CAST_CONF->chaos_min_fox_rate) - $fix_role_group_list['fox'];
    $CAST_CONF->AddRandom($random_role_list, $random_fox_list, $add_count);
    //PrintData($random_role_list); //テスト用

    //-- ランダム配役 --//
    $random_full_list = $CAST_CONF->GenerateRandomList($CAST_CONF->chaos_random_role_list);
    //PrintData($random_full_list); //テスト用
    //$CAST_CONF->RateToProbability($CAST_CONF->chaos_random_role_list); //テスト用

    $add_count = $user_count - (array_sum($random_role_list) +
				array_sum($CAST_CONF->chaos_fix_role_list));
    $CAST_CONF->AddRandom($random_role_list, $random_full_list, $add_count);
    //PrintData($random_role_list); //テスト用

    //ランダムと固定を合計
    $role_list = $random_role_list;
    foreach($CAST_CONF->chaos_fix_role_list as $key => $value){
      $role_list[$key] += (int)$value;
    }
    //PrintData($role_list, '1st_list'); //テスト用

    //役職グループ毎に集計
    foreach($role_list as $key => $value){
      $role_group = DistinguishRoleGroup($key);
      $role_group_list->{$role_group}[$key] = $value;
    }
    foreach($random_role_list as $key => $value){ //補正用リスト
      $role_group = DistinguishRoleGroup($key);
      $random_role_group_list->{$role_group}[$key] = $value;
    }

    //-- 最大補正 --//
    foreach($CAST_CONF->chaos_role_group_rate_list as $name => $rate){
      if(! (is_array($role_group_list->$name) && is_array($random_role_group_list->$name))){
	continue;
      }
      $over_count = array_sum($role_group_list->$name) - round($user_count * $rate);
      //if($over_count > 0) PrintData($over_count, $name); //テスト用
      for(; $over_count > 0; $over_count--){
	if(array_sum($random_role_group_list->$name) < 1) break;
	//PrintData($random_role_group_list->$name, "　　$over_count: before");
	arsort($random_role_group_list->$name);
	//PrintData($random_role_group_list->$name, "　　$over_count: after");
	$key = key($random_role_group_list->$name);
	//PrintData($key, "　　target");
	$random_role_group_list->{$name}[$key]--;
	$role_list[$key]--;
	$role_list['human']++;
	//PrintData($random_role_group_list->$name, "　　$over_count: delete");

	//0 になった役職はリストから除く
	if($role_list[$key] < 1) unset($role_list[$key]);
	if($random_role_group_list->{$name}[$key] < 1){
	  unset($random_role_group_list->{$name}[$key]);
	}
      }
    }
    //PrintData($role_list, '2nd_list'); //テスト用

    //神話マニア村以外なら一定数以上の村人を別の役職に振り返る
    if(strpos($option_role, 'full_mania') === false){
      $over_count = $role_list['human'] - round($user_count * $CAST_CONF->chaos_max_human_rate);
      if($over_count > 0){
	$stack = $CAST_CONF->chaos_replace_human_role_list;
	$random_replace_list = $CAST_CONF->GenerateRandomList($stack);
	$CAST_CONF->AddRandom($role_list, $random_replace_list, $over_count);
	$role_list['human'] -= $over_count;
	//PrintData($role_list, '3rd_list'); //テスト用
      }
    }
  }
  elseif($ROOM->IsOption('chaos')){ //闇鍋
    //-- 各陣営の人数を決定 (人数 = 各人数の出現率) --//
    $role_list = array(); //配列をリセット

    //人狼陣営
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 5){
      $wolf_count = 1;
    }
    elseif($user_count < 8){ //1:2 = 80:20
      $wolf_count = ($rand <= 80 ? 1 : 2);
    }
    elseif($user_count < 16){ //1:2:3 = 15:70:15
      $wolf_count = 1;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
    }
    elseif($user_count < 21){ //1:2:3:4:5 = 5:10:70:10:5
      $wolf_count = 1;
      if($rand >  5) $wolf_count++;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
      if($rand > 95) $wolf_count++;
    }
    else{ //以後、5人増えるごとに 1人ずつ増加
      $wolf_count = floor(($user_count - 20) / 5) + 1;
      if($rand >  5) $wolf_count++;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
      if($rand > 95) $wolf_count++;
    }

    //妖狐陣営
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){
      $fox_count = 0;
    }
    elseif($user_count < 15){ //0:1 = 90:10
      $fox_count = ($rand <= 90 ? 0 : 1);
    }
    elseif($user_count < 23){ //1:2 = 90:10
      $fox_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $fox_count = ceil($user_count / 20) - 1;
      if($rand > 10) $fox_count++;
      if($rand > 90) $fox_count++;
    }

    //恋人陣営 (実質キューピッド)
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){
      $lovers_count = 0;
    }
    elseif($user_count < 10){ //0:1 = 95:5
      $lovers_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1 = 70:30
      $lovers_count = ($rand <= 70 ? 0 : 1);
    }
    elseif($user_count < 23){ //0:1:2 = 5:90:5
      $lovers_count = 0;
      if($rand >  5) $lovers_count++;
      if($rand > 95) $lovers_count++;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      //基礎-1:基礎:基礎+1 = 5:90:5
      $lovers_count = floor($user_count / 20) - 1;
      if($rand >  5) $lovers_count++;
      if($rand > 95) $lovers_count++;
    }
    $role_list['cupid'] = $lovers_count;

    //村人陣営の人数を算出
    $human_count = $user_count - $wolf_count - $fox_count - $lovers_count;

    //人狼系の配役を決定
    $special_wolf_count = 0; //特殊狼の人数
    $base_count = ceil($user_count / 15); //特殊狼判定回数を算出
    for(; $base_count > 0; $base_count--){
      if(mt_rand(1, 100) <= $user_count) $special_wolf_count++; //参加人数 % の確率で特殊狼出現
    }
    if($special_wolf_count > 0){ //特殊狼の割り当て
      //狼の総数を超えていたら補正する
      if($special_wolf_count > $wolf_count) $special_wolf_count = $wolf_count;
      $wolf_count -= $special_wolf_count; //特殊狼の数だけ通常狼を減らす

      if($user_count <= 16){ //16人未満の場合は白狼のみ
	if(mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
      elseif($user_count < 20){ //20人未満で舌禍狼出現
	if(mt_rand(1, 100) <= 40){
	  $role_list['tongue_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
      else{ //20人以上なら毒狼を先に判定してやや出やすくする
	if(mt_rand(1, 100) <= $user_count){
	  $role_list['poison_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['tongue_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
    }
    $role_list['wolf'] = $wolf_count;

    //妖狐系の配役を決定
    if($user_count < 20){ //全人口が20人未満の場合は子狐は出現しない
      $role_list['fox'] = $fox_count;
      $role_list['child_fox'] = 0;
    }
    else{ //参加人数 % で子狐が一人出現
      if(mt_rand(1, 100) <= $user_count) $role_list['child_fox'] = 1;
      $role_list['fox'] = $fox_count - (int)$role_list['child_fox'];
    }

    //占い系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){ //0:1 = 10:90
      $mage_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //1:2 = 95:5
      $mage_count = ($rand <= 95 ? 1 : 2);
    }
    elseif($user_count < 30){ //1:2 = 90:10
      $mage_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $mage_count = floor($user_count / 15) - 1;
      if($rand > 10) $mage_count++;
      if($rand > 90) $mage_count++;
    }

    //占い系の配役を決定
    if($mage_count > 0 && $human_count >= $mage_count){
      $human_count -= $mage_count; //村人陣営の残り人数
      if($user_count < 16){ //16人未満の場合は特殊占い師はなし
	$role_list['mage'] = $mage_count;
      }
      else{ //参加人数 % で魂の占い師が一人出現
	if(mt_rand(1, 100) <= $user_count) $role_list['soul_mage'] = 1;
	$role_list['mage'] = $mage_count - (int)$role_list['soul_mage'];
      }
    }

    //巫女の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 9){ //0:1 = 70:30
      $medium_count = ($rand <= 70 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $medium_count = 0;
      if($rand > 10) $medium_count++;
      if($rand > 90) $medium_count++;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $medium_count = floor($user_count / 15) - 1;
      if($rand > 10) $medium_count++;
      if($rand > 90) $medium_count++;
    }
    if($cupid_count > 0 && $medium_count == 0) $medium_count++;

    //巫女の配役を決定
    if($medium_count > 0 && $human_count >= $medium_count){
      $human_count -= $medium_count; //村人陣営の残り人数
      $role_list['medium'] = $medium_count;
    }

    //霊能系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 9){ //0:1 = 10:90
      $necromancer_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //1:2 = 95:5
      $necromancer_count = ($rand <= 95 ? 1 : 2);
    }
    elseif($user_count < 30){ //1:2 = 90:10
      $necromancer_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $necromancer_count = floor($user_count / 15) - 1;
      if($rand > 10) $necromancer_count++;
      if($rand > 90) $necromancer_count++;
    }

    //霊能系の配役を決定
    if($necromancer_count > 0 && $human_count >= $necromancer_count){
      $human_count -= $necromancer_count; //村人陣営の残り人数
      $role_list['necromancer'] = $necromancer_count;
    }

    //狂人系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 10){ //0:1 = 30:70
      $mad_count = ($rand <= 30 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $mad_count = 0;
      if($rand > 10) $mad_count++;
      if($rand > 90) $mad_count++;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $mad_count = floor($user_count / 15) - 1;
      if($rand > 10) $mad_count++;
      if($rand > 90) $mad_count++;
    }

    //狂人系の配役を決定
    if($mad_count > 0 && $human_count >= $mad_count){
      $human_count -= $mad_count; //村人陣営の残り人数
      if($user_count < 16){ //全人口が16人未満の場合は狂信者は出現しない
	$role_list['mad'] = $mad_count;
	$role_list['fanatic_mad'] = 0;
      }
      else{ //参加人数 % で狂信者が一人出現
	if(mt_rand(1, 100) <= $user_count) $role_list['fanatic_mad'] = 1;
	$role_list['mad'] = $mad_count - (int)$role_list['fanatic_mad'];
      }
    }

    //狩人系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 11){ //0:1 = 10:90
      $guard_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $guard_count = 0;
      if($rand > 10) $guard_count++;
      if($rand > 90) $guard_count++;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $guard_count = floor($user_count / 15) - 1;
      if($rand > 10) $guard_count++;
      if($rand > 90) $guard_count++;
    }

    //狩人系の配役を決定
    if($guard_count > 0 && $human_count >= $guard_count){
      $human_count -= $guard_count; //村人陣営の残り人数
      $special_guard_count = 0; //特殊狩人の人数
      //16人以上なら特殊狩人判定回数を算出
      $base_count = ($user_count >= 16 ? ceil($user_count / 15) : 0);
      for(; $base_count > 0; $base_count--){
	if(mt_rand(1, 100) <= $user_count) $special_guard_count++; //参加人数 % の確率で特殊狩人出現
      }

      if($special_guard_count > 0){ //特殊狩人の割り当て
	//狩人の総数を超えていたら補正する
	if($special_guard_count > $guard_count) $special_guard_count = $guard_count;
	$guard_count -= $special_guard_count; //特殊狩人の数だけ狩人を減らす
	
	if($user_count < 20){ //20人未満の場合はブン屋のみ
	  $role_list['reporter'] = $special_guard_count;
	}
	else{
	  if(mt_rand(1, 100) <= $user_count){ //騎士は最大一人
	    $role_list['poison_guard']++;
	    $special_guard_count--;
	  }
	  $role_list['reporter'] = $special_guard_count;
	}
      }
      $role_list['guard'] = $guard_count;
    }

    //共有者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 13){ //0:1 = 10:90
      $common_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 22){ //1:2:3 = 10:80:10
      $common_count = 1;
      if($rand > 10) $common_count++;
      if($rand > 90) $common_count++;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $common_count = floor($user_count / 15);
      if($rand > 10) $common_count++;
      if($rand > 90) $common_count++;
    }

    //共有者の配役を決定
    if($common_count > 0 && $human_count >= $common_count){
      $role_list['common'] = $common_count;
      $human_count -= $common_count; //村人陣営の残り人数
    }

    //埋毒者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 95:5
      $poison_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 85:15
      $poison_count = ($rand <= 85 ? 0 : 1);
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $poison_count = floor($user_count / 20) - 1;
      if($rand > 10) $poison_count++;
      if($rand > 90) $poison_count++;
    }
    $poison_count -= $poison_guard_count; //騎士の数だけ減らす

    //埋毒者の配役を決定
    if($poison_count > 0 && $human_count >= $poison_count){
      $role_list['poison'] = $poison_count;
      $human_count -= $poison_count; //村人陣営の残り人数
    }

    //薬師の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 95:5
      $pharmacist_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 85:15
      $pharmacist_count = ($rand <= 85 ? 0 : 1);
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $pharmacist_count = floor($user_count / 20) - 1;
      if($rand > 10) $pharmacist_count++;
      if($rand > 90) $pharmacist_count++;
    }
    if($poison_wolf_count > 0 && $pharmacist_count == 0) $pharmacist_count++;

    //薬師の配役を決定
    if($pharmacist_count > 0 && $human_count >= $pharmacist_count){
      $role_list['pharmacist'] = $pharmacist_count;
      $human_count -= $pharmacist_count; //村人陣営の残り人数
    }

    //神話マニアの人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 16){ //16人未満では出現しない
      $mania_count = 0;
    }
    elseif($user_count < 23){ //0:1 = 40:60
      $mania_count = ($rand <= 40 ? 0 : 1);
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $mania_count = floor($user_count / 20) - 1;
      if($rand > 10) $mania_count++;
      if($rand > 90) $mania_count++;
    }

    //神話マニアの配役を決定
    if($mania_count > 0 && $human_count >= $mania_count){
      $role_list['mania'] = $mania_count;
      $human_count -= $mania_count; //村人陣営の残り人数
    }

    //不審者系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 90:10
      $strangers_count = ($rand <= 90 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 80:20
      $strangers_count = ($rand <= 80 ? 0 : 1);
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $strangers_count = floor($user_count / 20) - 1;
      if($rand > 10) $strangers_count++;
      if($rand > 90) $strangers_count++;
    }

    //不審者系の配役を決定
    if($strangers_count > 0 && $human_count >= $strangers_count){
      //全人口が20人未満の場合は無意識、それ以上なら不審者をやや出やすくする
      $strangers_rate = ($user_count < 20 ? 60 : 40);
      for($i = 0; $i < $strangers_count; $i++){
	$strangers_role = (mt_rand(1, 100) <= $strangers_rate ? 'unconscious' : 'suspect');
	$role_list[$strangers_role]++;
      }
      $human_count -= $strangers_count; //村人陣営の残り人数
    }

    $role_list['human'] = $human_count; //村人の人数

    //探偵 (共有 or 村人 → 探偵)
    if(strpos($option_role, 'detective') !== false){
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
  else{ //通常村
    //埋毒者 (村人2 → 埋毒者1、人狼1)
    if(strpos($option_role, 'poison') !== false && $user_count >= $CAST_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //暗殺者 (村人2 → 暗殺者1、人狼1)
    if(strpos($option_role, 'assassin') !== false && $user_count >= $CAST_CONF->assassin){
      $role_list['human'] -= 2;
      $role_list['assassin']++;
      $role_list['wolf']++;
    }

    //白狼 (人狼 → 白狼)
    if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $CAST_CONF->boss_wolf){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //毒狼 (人狼 → 毒狼、村人 → 薬師)
    if(strpos($option_role, 'poison_wolf') !== false && $user_count >= $CAST_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //憑狼 (人狼 → 憑狼)
    if(strpos($option_role, 'possessed_wolf') !== false && $user_count >= $CAST_CONF->possessed_wolf){
      $role_list['wolf']--;
      $role_list['possessed_wolf']++;
    }

    //天狼 (人狼 → 天狼)
    if(strpos($option_role, 'sirius_wolf') !== false && $user_count >= $CAST_CONF->sirius_wolf){
      $role_list['wolf']--;
      $role_list['sirius_wolf']++;
    }

    //キューピッド (14人はハードコード / 村人 → キューピッド)
    if(strpos($option_role, 'cupid') !== false &&
       ($user_count == 14 || $user_count >= $CAST_CONF->cupid)){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //巫女 (村人 → 巫女1、女神1)
    if(strpos($option_role, 'medium') !== false && $user_count >= $CAST_CONF->medium){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['mind_cupid']++;
    }

    //神話マニア (村人 → 神話マニア)
    if(strpos($option_role, 'mania') !== false && strpos($option_role, 'full_mania') === false &&
       $user_count >= $CAST_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //探偵 (共有 or 村人 → 探偵)
    if(strpos($option_role, 'detective') !== false){
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

  //神話マニア村
  if(strpos($option_role, 'full_mania') !== false){
    $role_list['mania'] += $role_list['human'];
    $role_list['human'] = 0;
  }

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

  if($ROOM->IsOption('festival')){ //お祭り村 (内容は管理人が自由にカスタムする)
    $festival_role_list = array(
        8 => array('human' => 1, 'mage' => 1, 'necromancer' => 1, 'guard' => 1, 'boss_wolf' => 1, 'mad' => 1, 'white_fox' => 1, 'chiroptera' => 1),
        9 => array('guard' => 2, 'dummy_guard' => 4, 'wolf' => 1, 'silver_wolf' => 1, 'cursed_fox' => 1),
       10 => array('mage' => 1, 'necromancer' => 1, 'guard' => 1, 'doll' => 1, 'doll_master' => 1, 'wolf' => 2, 'mad' => 1, 'fox' => 1, 'chiroptera' => 1),
       11 => array('unconscious' => 1, 'soul_mage' => 1, 'soul_necromancer' => 1, 'crisis_priest' => 1, 'guard' => 1, 'anti_voodoo' => 1, 'cure_pharmacist' => 1, 'cursed_wolf' => 1, 'silver_wolf' => 1, 'jammer_mad' => 1, 'cursed_chiroptera' => 1),
       12 => array('wise_wolf' => 1, 'jammer_mad' => 8, 'voodoo_fox' => 2, 'fairy' => 1),
       13 => array('human' => 1, 'mage' => 1, 'psycho_mage' => 1, 'dummy_mage' => 1,'necromancer' => 1, 'dummy_necromancer' => 1, 'guard' => 1, 'dummy_guard' => 1, 'common'=> 1, 'wolf' => 1, 'poison_wolf' => 1,'trap_mad' => 1, 'cursed_chiroptera' => 1),
       14 => array('necromancer' => 1, 'silver_wolf' => 2, 'fox' => 1, 'chiroptera' => 10),
       15 => array('poison' => 3, 'wolf' => 3, 'fanatic_mad' => 1, 'fox' => 1, 'chiroptera' => 6, 'boss_chiroptera' => 1),
       16 => array('dummy_guard' => 1, 'strong_poison' => 1, 'dummy_poison' => 5, 'sirius_wolf' => 3, 'dream_eater_mad' => 1, 'triangle_cupid' => 1, 'mirror_fairy' => 4),
       17 => array('sex_mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'wolf' => 2, 'gold_wolf' => 1, 'fox' => 1, 'chiroptera' => 7),
       18 => array('saint' => 1, 'soul_mage' => 1, 'soul_necromancer' => 1, 'fend_guard' => 1, 'trap_common' => 1, 'ghost_common' => 1, 'incubate_poison' => 1, 'reverse_assassin' => 1, 'wise_wolf' => 1, 'possessed_wolf' => 1, 'sirius_wolf' => 1, 'jammer_mad' => 1, 'voodoo_mad' => 1, 'voodoo_fox' => 1, 'revive_fox' => 1, 'angel' => 1, 'light_fairy' => 1, 'trick_mania' => 1),
       19 => array('revive_priest' => 1, 'anti_voodoo' => 1, 'dummy_poison' => 1, 'eclipse_assassin' => 2, 'poison_cat' => 1, 'jealousy' => 1, 'poison_wolf' => 1, 'possessed_wolf' => 1, 'sirius_wolf' => 1, 'fanatic_mad' => 1, 'agitate_mad' => 1, 'cursed_fox' => 2, 'quiz' => 1, 'mind_cupid' => 1, 'light_fairy' => 1, 'dark_fairy' => 1, 'mirror_fairy' => 1),
       20 => array('eclipse_assassin' => 13, 'silver_wolf' => 5, 'fox' => 2),
       21 => array('poison' => 7, 'chain_poison' => 2, 'poison_wolf' => 4, 'resist_wolf' => 1, 'poison_fox' => 2, 'quiz' => 3, 'poison_chiroptera' => 2),
       22 => array('human' => 8, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'poison_cat' => 1, 'wolf' => 4, 'boss_wolf' => 1, 'fox' => 1, 'child_fox' => 1)
				);
    if(is_array($festival_role_list[$user_count])) $role_list = $festival_role_list[$user_count];
  }

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
    //PrintData($role_count, 'エラー：配役数');
    //return $now_role_list;
    $sentence = '村人 (' . $user_count . ') と配役の数 (' . $role_count . ') が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  return $now_role_list;
}

//役職の人数通知リストを作成する
function GenerateRoleNameList($role_count_list, $chaos = NULL){
  global $GAME_CONF;

  $main_role_key_list = array_keys($GAME_CONF->main_role_list);
  switch($chaos){
  case 'camp':
    $header = '出現陣営：';
    $main_type = '陣営';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $main_role_key_list)) continue;
      if(strpos($key, 'wolf') !== false || strpos($key, 'mad') !== false)
	$main_role_list['wolf'] += $value;
      elseif(strpos($key, 'fox') !== false)
	$main_role_list['fox'] += $value;
      elseif(strpos($key, 'cupid') !== false || strpos($key, 'angel') !== false)
	$main_role_list['cupid'] += $value;
      elseif(strpos($key, 'mania') !== false)
	$main_role_list['mania'] += $value;
      elseif(strpos($key, 'quiz') !== false)
	$main_role_list['quiz'] += $value;
      elseif(strpos($key, 'chiroptera') !== false || strpos($key, 'fairy') !== false)
	$main_role_list['chiroptera'] += $value;
      else
	$main_role_list['human'] += $value;
    }
    break;

  case 'role':
    $header = '出現役職種：';
    $main_type = '系';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $main_role_key_list)) continue;
      $main_role_list[DistinguishRoleGroup($key)] += $value;
    }
    break;

  default:
    $header = '出現役職：';
    $main_role_list = $role_count_list;
    break;
  }

  $sub_role_key_list = array_keys($GAME_CONF->sub_role_list);
  switch($chaos){
  case 'camp':
  case 'role':
    $sub_type = '系';
    $sub_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $sub_role_key_list)) continue;
      foreach($GAME_CONF->sub_role_group_list as $class => $list){
	if(! in_array($key, $list)) continue;
	$sub_role_list[$list[0]] += $value;
      }
    }
    break;

  default:
    $sub_role_list = $role_count_list;
    break;
  }

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$main_role_list[$key];
    if($count < 1) continue;
    if($sentence != '') $sentence .= '　';
    $sentence .= $value . $main_type . $count;
  }

  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$sub_role_list[$key];
    if($count > 0) $sentence .= '　(' . $value . $sub_type . $count . ')';
  }
  return $header . $sentence;
}

//昼の投票集計処理
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS;

  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //コマンドチェック

  $user_list = $USERS->GetLivingUsers(); //生きているユーザを取得

  //投票情報を取得して全員が投票していなければ処理スキップ
  if($ROOM->LoadVote() != count($user_list)) return false;
  //PrintData($ROOM->vote, 'Vote'); //テスト用

  $max_voted_number = 0; //最多得票数
  $vote_kill_uname = ''; //処刑される人のユーザ名
  $live_uname_list   = array(); //生きている人のユーザ名リスト
  $vote_message_list = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list      = array(); //能力者たちの投票結果
  $pharmacist_target_list = array(); //薬師の投票先
  $cure_pharmacist_target_list = array(); //河童の投票先
  $pharmacist_result_list = array(); //薬師系の鑑定結果

  foreach($ROOM->vote as $uname => $list){ //初期得票データを収集
    $target_uname = $USERS->ByVirtualUname($list['target_uname'])->uname;
    $vote_count_list[$target_uname] += $list['vote_number'];
  }
  //PrintData($vote_count_list, 'Vote Count Base');

  foreach($user_list as $uname){ //個別の投票データを収集
    $user = $USERS->ByVirtualUname($uname); //仮想ユーザを取得
    $list = $ROOM->vote[$uname]; //投票データ
    $target = $USERS->ByVirtualUname($list['target_uname']); //投票先の仮想ユーザ
    $vote_number  = (int)$list['vote_number']; //投票数
    $voted_number = (int)$vote_count_list[$user->uname]; //得票数

    //役職の得票補正
    if($user->IsRole('upper_luck')) //雑草魂
      $voted_number += ($ROOM->date == 2 ?  4 : -2);
    elseif($user->IsRole('downer_luck')) //一発屋
      $voted_number += ($ROOM->date == 2 ? -4 :  2);
    elseif($user->IsRole('random_luck')) //波乱万丈
      $voted_number += (mt_rand(1, 5) - 3);
    elseif($user->IsRole('star')) //人気者
      $voted_number--;
    elseif($user->IsRole('disfavor')) //不人気
      $voted_number++;

    if($voted_number < 0) $voted_number = 0; //マイナスになっていたら 0 にする

    //システムメッセージ用の配列を生成
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);
    //PrintData($message_list, $uname); //テスト用

    //リストにデータを追加
    $live_uname_list[$user->user_no] = $user->uname;
    $vote_message_list[$user->uname] = $message_list;
    $vote_target_list[$user->uname]  = $target->uname;
    $vote_count_list[$user->uname]   = $voted_number;
    if($user->IsRole('authority')){ //権力者なら投票先とユーザ名を記録
      $ability_list['authority'] = $target->uname;
      $ability_list['authority_uname'] = $user->uname;
    }
    elseif($user->IsRole('rebel')){ //反逆者なら投票先とユーザ名を記録
      $ability_list['rebel'] = $target->uname;
      $ability_list['rebel_uname'] = $user->uname;
    }
    elseif($user->IsRole('decide')) //決定者なら投票先を記録
      $ability_list['decide'] = $target->uname;
    elseif($user->IsRole('plague')) //疫病神なら投票先を記録
      $ability_list['plague'] = $target->uname;
    elseif($user->IsRole('impatience')) //短気なら投票先を記録
      $ability_list['impatience'] = $target->uname;
    elseif($user->IsRole('good_luck')) //幸運ならユーザ名を記録
      $ability_list['good_luck'] = $user->uname;
    elseif($user->IsRole('bad_luck')) //不運ならユーザ名を記録
      $ability_list['bad_luck'] = $user->uname;
  }
  //PrintData($vote_count_list, 'Vote Count'); //テスト用

  //反逆者の判定
  if(isset($ability_list['rebel']) && $ability_list['rebel'] == $ability_list['authority']){
    //権力者と反逆者の投票数を 0 にする
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //投票先の得票数を補正する
    $uname = $ability_list['rebel'];
    if($vote_message_list[$uname]['voted_number'] > 3)
      $vote_message_list[$uname]['voted_number'] -= 3;
    else
      $vote_message_list[$uname]['voted_number'] = 0;
    $vote_count_list[$uname] = $vote_message_list[$uname]['voted_number'];
  }

  //投票結果をタブ区切りで生成してシステムメッセージに登録
  //PrintData($vote_message_list, 'Vote Message'); //テスト用
  foreach($live_uname_list as $uname){
    extract($vote_message_list[$uname]); //配列を展開

    //最大得票数を更新
    if($voted_number > $max_voted_number) $max_voted_number = $voted_number;

    //(誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $USERS->GetHandleName($uname) . "\t" . $target . "\t" .
      $voted_number ."\t" . $vote_number . "\t" . $RQ_ARGS->vote_times;
    $ROOM->SystemMessage($sentence, 'VOTE_KILL');
  }

  //最大得票数のユーザ名 (処刑候補者) のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  //PrintData($max_voted_uname_list, 'Max Voted'); //テスト用
  do{ //処刑者決定ルーチン
    if(count($max_voted_uname_list) == 1){ //一人だけなら決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    $decide_order_list = array('decide', 'bad_luck', 'impatience');
    foreach($decide_order_list as $role){ //決定能力者の判定
      $ability = $ability_list[$role];
      if(isset($ability) && in_array($ability, $max_voted_uname_list)){
	$vote_kill_uname = $ability;
	break 2;
      }
    }

    $luck_order_list = array('good_luck', 'plague');
    foreach($luck_order_list as $role){ //幸運能力者の判定
      if(is_null($ability = $ability_list[$role])) continue;
      if(($key = array_search($ability, $max_voted_uname_list)) === false) continue;
      unset($max_voted_uname_list[$key]);
      if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
	$vote_kill_uname = array_shift($max_voted_uname_list);
	break 2;
      }
    }

    //-- 執行者判定 --//
    $stack = array();
    foreach($user_list as $uname){ //最多得票者に投票した執行者の投票先を収集
      $user = $USERS->ByUname($uname); //$uname は実ユーザ
      if(! $user->IsRole('executor')) continue;
      $target = $USERS->ByVirtualUname($ROOM->vote[$uname]['target_uname']);
      if(in_array($target->uname, $max_voted_uname_list) &&
	 $target->GetCamp(true) != 'human'){ //最多得票者リストは仮想ユーザ
	$stack[$target->uname] = true;
      }
    }
    //PrintData($stack);
    if(count($stack) == 1){ //対象を一人に固定できる時のみ有効
      $vote_kill_uname = array_shift(array_keys($stack));
      break;
    }

    //-- 聖女判定 --//
    //最多得票者を再取得
    $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
    $stack = array();
    $target_stack = array();
    foreach($max_voted_uname_list as $uname){//最多得票者の情報を収集
      $user = $USERS->ByRealUname($uname); //$uname は仮想ユーザ
      if($user->IsRole('saint')) $stack[] = $uname;
      if($user->GetCamp(true) != 'human') $target_stack[] = $uname;
    }
    //対象を一人に固定できる時のみ有効
    if(count($stack) > 0 && count($target_stack) < 2){
      if(isset($target_stack[0])){
	$vote_kill_uname = $target_stack[0];
	break;
      }
      elseif(count($stack) == 1){
	$vote_kill_uname = $stack[0];
	break;
      }
    }

    //-- 扇動者判定 --//
    $stack = array();
    foreach($user_list as $uname){ //最多得票者に投票した扇動者の投票先を収集
      $user = $USERS->ByUname($uname); //$uname は実ユーザ
      if(! $user->IsRole('agitate_mad')) continue;
      $target = $USERS->ByVirtualUname($ROOM->vote[$uname]['target_uname']);
      if(in_array($target->uname, $max_voted_uname_list)){ //最多得票者リストは仮想ユーザ
	$stack[$target->uname] = true;
      }
    }
    //PrintData($stack);
    if(count($stack) != 1) break; //対象を一人に固定できる時のみ有効

    $vote_kill_uname = array_shift(array_keys($stack));
    foreach($max_voted_uname_list as $uname){
      if($uname == $vote_kill_uname) continue;
      $user = $USERS->ByRealUname($uname); //$uname は仮想ユーザ
      $USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_AGITATED');
    }
  }while(false);
  //PrintData($vote_kill_uname, 'VOTE TARGET');

  if($vote_kill_uname != ''){ //処刑処理実行
    $vote_target = $USERS->ByRealUname($vote_kill_uname); //ユーザ情報を取得
    $USERS->Kill($vote_target->user_no, 'VOTE_KILLED'); //処刑処理
    unset($live_uname_list[$vote_target->user_no]); //処刑者を生存者リストから除く
    $voter_list = array_keys($vote_target_list, $vote_target->uname); //投票した人を取得

    foreach($user_list as $uname){ //薬師系の情報収集
      $user = $USERS->ByUname($uname);
      if(! $user->IsRoleGroup('pharmacist')) continue;

      $target = $USERS->ByUname($vote_target_list[$user->uname]); //投票先の情報を取得
      $pharmacist_target_list[$user->uname] = $target->uname;
      if($user->IsRole('cure_pharmacist')){ //河童には種類は不明
	$cure_pharmacist_target_list[$user->uname] = $target->uname;
	continue;
      }

      if(! $target->IsRoleGroup('poison') || $target->IsRole('dummy_poison')){
	$pharmacist_result_list[$user->uname] = 'nothing'; //非毒能力者・夢毒者
      }
      elseif($target->IsRole('strong_poison')){
	$pharmacist_result_list[$user->uname] = 'strong'; //強毒者
      }
      elseif($target->IsRole('incubate_poison')){ //潜毒者は 5 日目以降に強毒を持つ
	$pharmacist_result_list[$user->uname] = $ROOM->date >= 5 ? 'strong' : 'nothing';
      }
      elseif($target->IsRole('poison_guard', 'chain_poison', 'poison_jealousy')){
	$pharmacist_result_list[$user->uname] = 'limited'; //騎士・連毒者・毒橋姫
      }
      else{
	$pharmacist_result_list[$user->uname] = 'poison';
      }
    }

    //処刑された人が毒を持っていた場合
    do{
      if(! $vote_target->IsPoison()) break; //毒能力の発動判定

      //薬師系の解毒判定 (夢毒者は対象外)
      if(in_array($vote_target->uname, $pharmacist_target_list) &&
	 ! $vote_target->IsRole('dummy_poison')){
	$stack = array_keys($pharmacist_target_list, $vote_target->uname); //投票者を検出
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	break;
      }

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = array(); //毒の対象リスト
      $target_list = $GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list;
      //PrintData($target_list); //テスト用

      //天狼の LW 判定
      $living_wolves_list = $USERS->GetLivingWolves();
      //PrintData($living_wolves_list); //テスト用
      if(count($living_wolves_list) == 1){
	$last_wolf = $USERS->ByUname(array_shift($living_wolves_list));
	$last_wolf_flag = $last_wolf->IsRole('sirius_wolf');
      }
      else{
	$last_wolf_flag = false;
      }

      foreach($target_list as $uname){ //常時対象外の役職を除く
	$user = $USERS->ByRealUname($uname);
	if($user->IsRole('detective_common', 'quiz') ||
	   $last_wolf_flag && $user->IsSame($last_wolf->uname)) continue;
	if($user->IsLive(true)) $poison_target_list[] = $uname;
      }
      //PrintData($poison_target_list); //テスト用

      $limited_list = array(); //特殊毒の場合はターゲットが限定される
      if($vote_target->IsRole('strong_poison', 'incubate_poison')){ //強毒者・潜毒者
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('dummy_poison')){ //夢毒者
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('dream_eater_mad', 'fairy')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_jealousy')){ //毒橋姫
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsLovers()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_doll')){ //鈴蘭人形
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsDoll()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_wolf')){ //毒狼
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsWolf()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_fox')){ //管狐
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsFox()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_chiroptera')){ //毒蝙蝠
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox', 'chiroptera', 'fairy')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      if(count($poison_target_list) < 1) break;

      //PrintData($poison_target_list, 'Poison Target'); //テスト用
      $poison_target = $USERS->ByRealUname(GetRandom($poison_target_list)); //対象者を決定

      if($poison_target->IsActive('resist_wolf')){ //抗毒判定
	$poison_target->LostAbility();
	break;
      }

      $USERS->Kill($poison_target->user_no, 'POISON_DEAD_day'); //死亡処理

      if(! $poison_target->IsRole('chain_poison')) break; //連毒者判定
      if(in_array($poison_target->uname, $pharmacist_target_list)){ //薬師系の解毒判定
	$stack = array_keys($pharmacist_target_list, $poison_target->uname); //投票者を検出
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	break;
      }

      $live_stack   = $USERS->GetLivingUsers(true); //生存者を取得
      $target_stack = array();
      foreach($live_stack as $uname){ //常時対象外の役職を除く
	$user = $USERS->ByRealUname($uname);
	if($user->IsRole('detective_common', 'quiz') ||
	   $last_wolf_flag && $user->IsSame($last_wolf->uname)) continue;
	$target_stack[] = $user->user_no;
      }
      //PrintData($target_stack); //テスト用

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
	  if(in_array($target->uname, $pharmacist_target_list)){ //薬師系の解毒判定
	    $stack = array_keys($pharmacist_target_list, $target->uname); //投票者を検出
	    foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	  }
	  else $chain_count++;
	}
      }
    }while(false);

    //霊能者系の処理
    $sentence_header = $USERS->GetHandleName($vote_target->uname, true) . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能判定
    if($vote_target->IsRole('boss_wolf', 'cursed_wolf', 'possessed_wolf')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('white_fox', 'black_fox', 'possessed_fox', 'cursed_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsChildFox()){
      $necromancer_result = 'child_fox';
    }
    elseif($vote_target->IsWolf()){
      $necromancer_result = 'wolf';
    }
    else{
      $necromancer_result = 'human';
    }

    //火車の妨害判定
    $flag_stolen = false;
    foreach($voter_list as $this_uname){
      $flag_stolen |= $USERS->ByRealUname($this_uname)->IsRole('corpse_courier_mad');
    }

    if($USERS->IsAppear('necromancer')){ //霊能者の処理
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      $ROOM->SystemMessage($sentence, $action);
    }

    if($USERS->IsAppear('soul_necromancer')){ //雲外鏡の処理
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      $ROOM->SystemMessage($sentence, 'SOUL_' . $action);
    }

    if($USERS->IsAppear('dummy_necromancer')){ //夢枕人は「村人」⇔「人狼」反転
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      $ROOM->SystemMessage($sentence_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //策士の処理 //先に策士の有無を検出して策士のみでループを回す処理に書き換える
  foreach($user_list as $uname){ //非村人陣営の ID と仮想投票者名を収集
    $user = $USERS->ByRealUname($uname);
    if($user->GetCamp(true) != 'human'){
      $target_stack[$user->user_no] = $USERS->ByVirtual($user->user_no)->uname;
    }
  }
  //PrintData($target_stack, '! HUMAN');
  //PrintData(array_values($target_stack), 'target');

  foreach($user_list as $uname){ //策士の投票者リストと照合
    $user = $USERS->ByRealUname($uname);
    if(! $user->IsRole('trap_common')) continue;
    $voted_uname_stack = array_keys($vote_target_list, $user->uname); //策士への投票者リスト
    //PrintData($voted_uname_stack, 'voted');
    if($voted_uname_stack != array_values($target_stack)) continue;
    foreach($target_stack as $id => $uname) $USERS->Kill($id, 'TRAPPED');
  }

  foreach($user_list as $uname){ //橋姫の処理
    $user = $USERS->ByRealUname($uname);
    if($vote_kill_uname == $user->uname || ! $user->IsRole('jealousy')) continue;

    $cupid_list = array(); //キューピッドのID => 恋人のID
    $jealousy_voted_list = array_keys($vote_target_list, $user->uname); //橋姫への投票者リスト
    foreach($jealousy_voted_list as $voted_uname){
      $voted_user = $USERS->ByRealUname($voted_uname);
      if($voted_user->dead_flag || ! $voted_user->IsLovers()) continue;
      foreach($voted_user->partner_list['lovers'] as $id){
	$cupid_list[$id][] = $voted_user->user_no;
      }
    }

    //同一キューピッドの恋人が複数いたらショック死
    foreach($cupid_list as $cupid_id => $lovers_list){
      if(count($lovers_list) < 2) continue;
      foreach($lovers_list as $id) $USERS->SuddenDeath($id, 'SUDDEN_DEATH_JEALOUSY');
    }
  }

  if($vote_kill_uname != ''){ //土蜘蛛の処理
    foreach($user_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsRole('miasma_mad')) continue;

      $target = $USERS->ByUname($vote_target_list[$user->uname]); //本体に付ける
      if($target->IsLive(true) && ! $target->IsRole('detective_common')){ //探偵には無効
	$target->AddRole('febris[' . ($ROOM->date + 1) . ']');
      }
    }
  }

  //特殊サブ役職の突然死処理
  //投票者対象ユーザ名 => 人数 の配列を生成
  //PrintData($vote_target_list); //テスト用
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($live_uname_list as $this_uname){
    $user = $USERS->ByUname($this_uname);
    $reason = '';

    if($user->IsRole('chicken')){ //小心者は投票されていたらショック死
      if($voted_target_member_list[$this_uname] > 0) $reason = 'CHICKEN';
    }
    if($user->IsRole('rabbit')){ //ウサギは投票されていなかったらショック死
      if($voted_target_member_list[$this_uname] == 0) $reason = 'RABBIT';
    }
    elseif($user->IsRole('perverseness')){
      //天邪鬼は自分の投票先に複数の人が投票していたらショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $reason = 'PERVERSENESS';
    }
    elseif($user->IsRole('flattery')){
      //ゴマすりは自分の投票先に他の人が投票していなければショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $reason = 'FLATTERY';
    }
    elseif($user->IsRole('impatience')){ //短気は再投票ならショック死
      if($vote_kill_uname == '') $reason = 'IMPATIENCE';
    }
    elseif($user->IsRole('nervy')){ //自信家は同一陣営に投票したらショック死
      $target = $USERS->ByRealUname($vote_target_list[$this_uname]);
      if($user->GetCamp(true) == $target->GetCamp(true)) $reason = 'NERVY';
    }
    elseif($user->IsRole('celibacy')){ //独身貴族は恋人に投票されたらショック死
      $celibacy_voted_list = array_keys($vote_target_list, $user->uname); //自分への投票者リスト
      foreach($celibacy_voted_list as $this_voted_uname){
	if($USERS->ByUname($this_voted_uname)->IsLovers()){
	  $reason = 'CELIBACY';
	  break;
	}
      }
    }
    elseif($user->IsRole('panelist')){ //解答者は出題者に投票したらショック死
      if($vote_target_list[$this_uname] == 'dummy_boy') $reason = 'PANELIST';
    }

    if($user->IsRole('febris')){ //熱病は発動当日ならショック死
      if($ROOM->date == max($user->GetPartner('febris'))) $reason = 'FEBRIS';
    }

    if($user->IsRole('death_warrant')){ //死の宣告は発動当日ならショック死
      if($ROOM->date == max($user->GetPartner('death_warrant'))) $reason = 'WARRANT';
    }

    if($reason != ''){
      if(in_array($user->uname, $cure_pharmacist_target_list)){ //河童の治療判定
	//投票者を検出
	$stack = array_keys($cure_pharmacist_target_list, $user->uname);
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
      }
      else{
	$USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_' . $reason);
      }
    }
  }

  foreach($pharmacist_result_list as $uname => $result){ //薬師系の鑑定結果を登録
    $user = $USERS->ByUname($uname);
    $target = $USERS->ByUname($pharmacist_target_list[$user->uname]); //投票先の情報を取得

    //鑑定結果を登録
    $handle_name = $USERS->GetHandleName($target->uname, true);
    if($user->IsRole('cure_pharmacist')) $result = 'cured';
    $sentence = $user->handle_name . "\t" . $handle_name . "\t" . $result;
    $ROOM->SystemMessage($sentence, 'PHARMACIST_RESULT');
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  if($ROOM->test_mode) return $vote_message_list;

  if($vote_kill_uname != ''){ //夜に切り替え
    $ROOM->day_night = 'night';
    SendQuery("UPDATE room SET day_night = '{$ROOM->day_night}' WHERE room_no = {$ROOM->id}"); //夜にする
    $ROOM->Talk('NIGHT'); //夜がきた通知
    if(! CheckVictory()) InsertRandomMessage(); //ランダムメッセージ
  }
  else{ //再投票処理
    $next_vote_times = $RQ_ARGS->vote_times + 1; //投票回数を増やす
    $query = 'UPDATE system_message SET message = ' . $next_vote_times . $ROOM->GetQuery() .
      " AND type = 'VOTE_TIMES'";
    SendQuery($query);

    //システムメッセージ
    $ROOM->SystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    $ROOM->Talk("再投票になりました( {$RQ_ARGS->vote_times} 回目)");
    CheckVictory(true); //勝敗判定
  }
  $ROOM->UpdateTime(); //最終書き込みを更新
  SendCommit(); //一応コミット
}

//夜の集計処理
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $ROOM->LoadVote(); //投票情報を取得
  //PrintData($ROOM->vote, 'Vote Row');

  $vote_data = $ROOM->ParseVote(); //コマンド毎に分割
  //PrintData($vote_data, 'Vote Data');

  foreach($USERS->rows as $user){ //未投票チェック
    if($user->CheckVote($vote_data) === false){
      //PrintData($user->uname, $user->main_role); //テスト用
      return false;
    }
  }

  //処理対象コマンドチェック
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  if($ROOM->date == 1){
    array_push($action_list , 'MIND_SCANNER_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list , 'DREAM_EAT', 'TRAP_MAD_DO', 'POSSESSED_DO', 'GUARD_DO',
	       'ANTI_VOODOO_DO', 'REPORTER_DO', 'POISON_CAT_DO', 'ASSASSIN_DO');
  }
  foreach($action_list as $action){
    if(is_null($vote_data[$action])) $vote_data[$action] = array();
  }
  //PrintData($vote_data);

  //-- 変数の初期化 --//
  $guarded_uname = ''; //護衛成功した人のユーザ名 //複数噛みに対応するならここは配列に変える
  $trap_target_list         = array(); //罠の設置先リスト
  $trapped_list             = array(); //罠にかかった人リスト
  $guard_target_list        = array(); //狩人系の護衛対象リスト
  $dummy_guard_target_list  = array(); //夢守人の護衛対象リスト

  $anti_voodoo_target_list  = array(); //厄神の護衛対象リスト
  $anti_voodoo_success_list = array(); //厄払い成功者リスト

  $reverse_assassin_target_list = array(); //反魂師の対象リスト

  $possessed_target_list    = array(); //憑依予定者リスト => 憑依成立フラグ
  $possessed_target_id_list = array(); //憑依対象者リスト

  //-- 接触系レイヤー --//
  foreach($vote_data['WOLF_EAT'] as $uname => $target_uname){ //人狼の情報収集
    $voted_wolf  = $USERS->ByUname($uname);
    $wolf_target = $USERS->ByUname($target_uname);
  }

  if($ROOM->date > 1){
    foreach($vote_data['TRAP_MAD_DO'] as $uname => $target_uname){ //罠師の情報収集
      $user   = $USERS->ByUname($uname);
      $target = $USERS->ByUname($target_uname);

      $user->LostAbility(); //一度設置したら能力失効

      //人狼に狙われていたら自分自身への設置以外は無効
      if($user != $wolf_target || $user == $target){
	$trap_target_list[$user->uname] = $target->uname; //罠をセット
      }
    }

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $trap_count_list = array_count_values($trap_target_list);
    foreach($trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $trap_count_list[$target_uname] > 1){
	$trapped_list[] = $uname;
      }
    }

    foreach($vote_data['GUARD_DO'] as $uname => $target_uname){ //狩人系の情報収集
      $user = $USERS->ByUname($uname);
      if($user->IsRole('dummy_guard')){ //夢守人
	$dummy_guard_target_list[$user->uname] = $target_uname; //護衛先をセット
	continue;
      }

      //罠が設置されていたら死亡
      if(in_array($target_uname, $trap_target_list)) $trapped_list[] = $user->uname;

      $guard_target_list[$user->uname] = $target_uname; //護衛先をセット
    }
    //PrintData($guard_target_list, 'Target [guard]');
    //PrintData($dummy_guard_target_list, 'Target [dummy_guard]');
  }

  do{ //人狼の襲撃成功判定
    if($ROOM->IsQuiz()) break; //クイズ村仕様

    //天狼の LW 判定
    $living_wolves_list = $USERS->GetLivingWolves();
    //PrintData($living_wolves_list); //テスト用
    $last_wolf_flag = count($living_wolves_list) == 1 && $voted_wolf->IsRole('sirius_wolf');

    //罠が設置されていたら死亡
    if(in_array($wolf_target->uname, $trap_target_list) &&
       ! (count($living_wolves_list) <= 2  && $voted_wolf->IsRole('sirius_wolf'))){
      $trapped_list[] = $voted_wolf->uname;
      break;
    }

    //狩人系の護衛判定
    $guard_list = array_keys($guard_target_list, $wolf_target->uname); //護衛者を検出
    //PrintData($guard_list, 'Guard List');
    if(count($guard_list) > 0){
      foreach($guard_list as $uname){
	$user = $USERS->ByUname($uname);

	//騎士でない場合、一部の役職は護衛していても人狼に襲撃される
	if($user->IsRole('poison_guard') ||
	   ! ($wolf_target->IsRole('priest', 'bishop_priest', 'detective_common', 'reporter',
				   'doll_master') || $wolf_target->IsRoleGroup('assassin'))){
	  $guarded_uname = $wolf_target->uname;
	}

	//護衛成功メッセージを作成
	$sentence = $user->handle_name . "\t" . $USERS->GetHandleName($wolf_target->uname, true);
	$ROOM->SystemMessage($sentence, 'GUARD_SUCCESS');
      }
      if(! $last_wolf_flag && $guarded_uname != '') break;
    }

    //襲撃先が人狼の場合は失敗する (例：銀狼出現)
    if($wolf_target->IsWolf()){
      if($voted_wolf->IsRole('emerald_wolf')){ //翠狼の処理
	$add_role = 'mind_friend[' . strval($voted_wolf->user_no) . ']';
	$voted_wolf->AddRole($add_role);
	$wolf_target->AddRole($add_role);
      }
      break;
    }
    //襲撃先が妖狐の場合は失敗する
    if($wolf_target->IsFox() && ! $wolf_target->IsRole('poison_fox', 'white_fox') &&
       ! $wolf_target->IsChildFox()){
      if($voted_wolf->IsRole('blue_wolf') && ! $wolf_target->IsRole('silver_fox')){ //蒼狼の処理
	$wolf_target->AddRole('mind_lonely');
      }
      if($wolf_target->IsRole('blue_fox')) $voted_wolf->AddRole('mind_lonely'); //蒼狐の処理

      $ROOM->SystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    if(! $wolf_target->IsDummyBoy()){ //特殊能力者の処理 (身代わり君は例外)
      if($voted_wolf->IsRole('sex_wolf')){ //雛狼の処理
	$sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
	$ROOM->SystemMessage($sentence . $wolf_target->DistinguishSex(), 'SEX_WOLF_RESULT');
	break;
      }
      //襲撃先が忍者の場合は一度だけ耐える
      if(! $last_wolf_flag && $wolf_target->IsActive('fend_guard')){
	$wolf_target->LostAbility();
	break;
      }

      //襲撃先が亡霊嬢の場合は小心者が付く
      if(! $last_wolf_flag && $wolf_target->IsRole('ghost_common')) $voted_wolf->AddRole('chicken');

      //身代わり能力者の判定
      $sacrifice_list = array();
      if($wolf_target->IsRole('boss_chiroptera')){ //大蝙蝠 (他の蝙蝠陣営)
	foreach($USERS->rows as $user){
	  if($user->IsLive() && ! $user->IsSame($wolf_target->uname) &&
	     $user->IsRoleGroup('chiroptera', 'fairy')) $sacrifice_list[] = $user->uname;
	}
      }
      elseif($wolf_target->IsRole('doll_master')){ //人形遣い (人形系)
	foreach($USERS->rows as $user){
	  if($user->IsLive() && $user->IsRoleGroup('doll') && ! $user->IsRole('doll_master')){
	    $sacrifice_list[] = $user->uname;
	  }
	}
      }
      if(count($sacrifice_list) > 0){
	$wolf_target = $USERS->ByUname(GetRandom($sacrifice_list));
	$USERS->Kill($wolf_target->user_no, 'SACRIFICE');
	break;
      }
    }

    //-- 襲撃処理 --//
    //憑狼の処理
    if($voted_wolf->IsRole('possessed_wolf') && ! $wolf_target->IsDummyBoy() &&
       ! $wolf_target->IsFox() && ! $wolf_target->IsRole('detective_common', 'revive_priest')){
      $possessed_target_list[$voted_wolf->uname] = $wolf_target->uname;
      $wolf_target->dead_flag = true;
      //襲撃先が厄神なら憑依リセット
      if($wolf_target->IsRole('anti_voodoo')) $voted_wolf->possessed_reset = true;
    }
    else{
      $USERS->Kill($wolf_target->user_no, 'WOLF_KILLED'); //通常狼の襲撃処理
    }

    if($voted_wolf->IsActive('tongue_wolf')){ //舌禍狼の処理
      if($wolf_target->IsRole('human')) $voted_wolf->LostAbility(); //村人なら能力失効

      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      $ROOM->SystemMessage($sentence . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');
    }

    if(! $last_wolf_flag && $wolf_target->IsPoison()){ //毒死判定処理
      //襲撃者が抗毒狼か、襲撃者固定設定なら対象固定
      if($voted_wolf->IsRole('resist_wolf') || $GAME_CONF->poison_only_eater){
	$poison_target = $voted_wolf;
      }
      else{ //生きている狼からランダム選出
	$poison_target = $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));
      }

      if($wolf_target->IsRole('poison_jealousy') && ! $poison_target->IsLovers()); //毒橋姫は恋人だけ
      elseif($poison_target->IsActive('resist_wolf')){ //抗毒狼なら無効
	$poison_target->LostAbility();
      }
      else{
	$USERS->Kill($poison_target->user_no, 'POISON_DEAD_night'); //毒死処理
      }
    }
  }while(false);
  //PrintData($possessed_target_list, 'Possessed Target [possessed_wolf]');

  if($ROOM->date > 1){
    //狩人系の狩り対象リスト
    $hunt_target_list = array('jammer_mad', 'voodoo_mad', 'corpse_courier_mad', 'agitate_mad',
			      'miasma_mad', 'dream_eater_mad', 'trap_mad', 'possessed_mad',
			      'voodoo_fox', 'revive_fox', 'possessed_fox', 'cursed_fox',
			      'poison_chiroptera', 'cursed_chiroptera', 'boss_chiroptera');
    foreach($guard_target_list as $uname => $target_uname){ //狩人系の狩り判定
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      /*
	この時点で死んでいるのは人狼の襲撃先、人狼の毒死、罠(予約)のみ。
	狩り対象 + 護衛無効となる役職が存在しないので、
	狩人系の護衛先が狩り以外で死ぬことはありえない。
	憑依能力者が対象に含まれていないので、仮想ユーザを引く必要はない。
      */
      $target = $USERS->ByUname($target_uname);
      if($target->IsRole($hunt_target_list)){
	$USERS->Kill($target->user_no, 'HUNTED');
	$sentence = $user->handle_name . "\t" . $target->handle_name;
	$ROOM->SystemMessage($sentence, 'GUARD_HUNTED');
      }
    }

    $assassin_target_list = array(); //暗殺対象者リスト
    $anti_assassin_flag = count($living_wolves_list) <= 2;
    foreach($vote_data['ASSASSIN_DO'] as $uname => $target_uname){ //暗殺者の情報収集
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      if(in_array($target_uname, $trap_target_list)){ //罠が設置されていたら死亡
	$trapped_list[] = $user->uname;
	continue;
      }

      $target = $USERS->ByUname($target_uname);
      if(($anti_assassin_flag && $target->IsRole('sirius_wolf')) ||
	 $target->IsRole('detective_common', 'cursed_fox')){ //探偵・天狼・天狐なら暗殺反射
	$assassin_target_list[$uname] = true;
      }
      elseif($user->IsRole('doom_assassin')){
	$add_role = 'death_warrant[' . ($ROOM->date + 2) . ']';
	$USERS->ByVirtualUname($target_uname)->AddRole($add_role);
      }
      elseif($user->IsRole('reverse_assassin')){
	$reverse_assassin_target_list[$uname] = $target_uname; //反魂対象者リストに追加
      }
      else{
	if($user->IsRole('cute_assassin') && mt_rand(1, 100) <= 30) $target_uname = $uname;
	$assassin_target_list[$target_uname] = true; //暗殺対象者リストに追加
      }
    }

    foreach($trapped_list as $uname){ //罠の死亡処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');
    }

    foreach($assassin_target_list as $uname => $flag){ //暗殺処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'ASSASSIN_KILLED');
    }

    $reverse_list = array(); //反魂対象リスト
    foreach($reverse_assassin_target_list as $uname => $target_uname){
      $target = $USERS->ByUname($target_uname);
      if($target->IsLive(true)){
	$USERS->Kill($target->user_no, 'ASSASSIN_KILLED');
      }
      elseif(! $target->IsLovers()){
	$reverse_list[$target_uname] = ! $reverse_list[$target_uname];
      }
    }
    //PrintData($reverse_list, 'Reverse List'); //テスト用

    //-- 夢系レイヤー --//
    foreach($vote_data['DREAM_EAT'] as $uname => $target_uname){ //獏の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      $sentence = "\t" . $user->handle_name;

      if($target->IsRole('dummy_guard') && $target->IsLive(true)){ //対象が夢守人なら返り討ちに合う
	$USERS->Kill($user->user_no, 'HUNTED');
	$ROOM->SystemMessage($target->handle_name . $sentence, 'GUARD_HUNTED');
	continue;
      }

      if(in_array($target->uname, $dummy_guard_target_list)){ //夢守人の護衛判定
	$hunted_flag = false;
	$guard_list = array_keys($dummy_guard_target_list, $target->uname); //護衛者を検出
	foreach($guard_list as $uname){
	  $guard_user = $USERS->ByUname($uname);
	  if($guard_user->IsDead(true)) continue; //直前に死んでいたら無効
	  $hunted_flag = true;
	  $ROOM->SystemMessage($guard_user->handle_name . $sentence, 'GUARD_HUNTED');
	}

	if($hunted_flag){
	  $USERS->Kill($user->user_no, 'HUNTED');
	  continue;
	}
      }

      //夢系能力者なら食い殺す
      if($target->IsRoleGroup('dummy', 'fairy')) $USERS->Kill($target->user_no, 'DREAM_KILLED');
    }

    $hunted_list = array(); //狩り成功者リスト
    foreach($dummy_guard_target_list as $uname => $target_uname){ //夢守人の狩り判定
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname);
      if(($target->IsRole('dream_eater_mad') || $target->IsRoleGroup('fairy')) &&
	 $target->IsLive(true)){ //獏の狩り判定
	$hunted_list[$user->handle_name] = $target;
      }

      //常時護衛成功メッセージだけが出る
      $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
      $ROOM->SystemMessage($sentence, 'GUARD_SUCCESS');
    }

    foreach($hunted_list as $handle_name => $target){ //獏狩り処理
      $USERS->Kill($target->user_no, 'HUNTED');
      $sentence = $handle_name . "\t" . $target->handle_name; //憑依能力者は対象外
      $ROOM->SystemMessage($sentence, 'GUARD_HUNTED');
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
      elseif($target->IsPossessedGroup() &&
	     $target != $USERS->ByVirtual($target->user_no)){ //憑依者なら強制送還
	if(! array_key_exists($target->uname, $possessed_target_list)){
	  $possessed_target_list[$target->uname] = NULL;
	}
	$target->possessed_reset = true;
      }
      else{
	continue;
      }
      $anti_voodoo_success_list[$target->uname] = true;
    }
    //PrintData($possessed_target_list, 'Possessed Target [anti_voodoo]'); //テスト用
  }

  $voodoo_killer_target_list  = array(); //陰陽師の解呪対象リスト
  $voodoo_killer_success_list = array(); //陰陽師の解呪成功者対象リスト
  foreach($vote_data['VOODOO_KILLER_DO'] as $uname => $target_uname){ //陰陽師の情報収集
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname); //対象者の情報を取得

    //呪殺判定 (呪い系・憑依能力者)
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

  $voodoo_target_list = array(); //呪術系能力者の対象リスト
  $voodoo_list = array_merge($vote_data['VOODOO_MAD_DO'], $vote_data['VOODOO_FOX_DO']);
  foreach($voodoo_list as $uname => $target_uname){ //呪術系能力者の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname); //対象者の情報を取得
    if($target->IsRoleGroup('cursed') && $target->IsLive(true)){ //呪返し判定
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $voodoo_killer_target_list)){ //陰陽師の解呪判定
      $voodoo_killer_success_list[$target->uname] = true;
    }
    else{
      $voodoo_target_list[$user->uname] = $target->uname;
    }
  }

  //呪術系能力者の対象先が重なった場合は呪返しを受ける
  $voodoo_count_list = array_count_values($voodoo_target_list);
  foreach($voodoo_target_list as $uname => $target_uname){
    if($voodoo_count_list[$target_uname] < 2) continue;

    if(in_array($uname, $anti_voodoo_target_list)){ //厄神の護衛判定
      $anti_voodoo_success_list[$uname] = true;
    }
    else{
      $USERS->Kill($USERS->UnameToNumber($uname), 'CURSED');
    }
  }

  //-- 占い系レイヤー --//
  $jammer_target_list = array(); //妨害対象リスト
  foreach($vote_data['JAMMER_MAD_DO'] as $uname => $target_uname){ //月兎の処理
    $user = $USERS->ByUname($uname);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($target_uname); //対象者の情報を取得
    //呪返し判定
    if(($target->IsRoleGroup('cursed') && ! $target->dead_flag) ||
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
      $jammer_target_list[$user->uname] = $target->uname;
    }
  }
  //PrintData($jammer_target_list, 'Target [jammer_mad]');

  //精神鑑定士の嘘つき判定対象役職リスト
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');

  //占い能力者の処理を合成 (array_merge() は $uname が整数だと添え字と認識されるので使わないこと)
  $mage_list = array();
  $mage_action_list = array('MAGE_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  foreach($mage_action_list as $action){
    foreach($vote_data[$action] as $uname => $target_uname) $mage_list[$uname] = $target_uname;
  }
  foreach($mage_list as $uname => $target_uname){ //占い系の処理
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //直前に死んでいたら無効

    $target = $USERS->ByRealUname($target_uname); //対象者の情報を取得
    if($user->IsRole('dummy_mage')){ //夢見人の判定 (村人と人狼を反転させる)
      $result = $target->DistinguishMage(true);
    }
    elseif(in_array($user->uname, $jammer_target_list)){ //月兎の妨害判定
      $result = $user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
    }
    elseif($user->IsRole('psycho_mage')){ //精神鑑定士の判定 (正常 / 嘘つき)
      $result = 'psycho_mage_normal';
      foreach($psycho_mage_liar_list as $liar_role){
	if($target->IsRoleGroup($liar_role)){
	  $result = 'psycho_mage_liar';
	  break;
	}
      }
    }
    elseif($user->IsRole('sex_mage')){ //ひよこ鑑定士の判定 (蝙蝠 / 性別)
      $result = $target->DistinguishSex();
    }
    elseif($user->IsRole('sex_fox')){ //雛狐の判定 (蝙蝠 / 性別 + 一定確率で失敗)
      $result = mt_rand(1, 100) > 30 ? $target->DistinguishSex() : 'failed';
    }
    else{
      //呪返し判定
      if(($target->IsRoleGroup('cursed') && $target->IsLive(true)) ||
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
	  $add_role = 'mind_friend[' . strval($user->user_no) . ']';
	  $user->LostAbility();
	  $user->AddRole($add_role);
	  $target->AddRole($add_role);
	}
      }
      elseif($user->IsRole('child_fox')){ //子狐の判定 (一定確率で失敗する)
	$result = $last_wolf_flag ? 'human' :
	  (mt_rand(1, 100) > 30 ? $target->DistinguishMage() : 'failed');
      }
      elseif($user->IsRoleGroup('fairy')){ //妖精系の処理
	$target_date = $ROOM->date + 1;
	$target->AddRole("bad_status[{$user->user_no}-{$target_date}]");
      }
      else{
	if(array_key_exists($target->uname, $possessed_target_list)){ //憑依キャンセル判定
	  $target->possessed_cancel = true;
	}

	if($user->IsRole('soul_mage')){ //魂の占い師の判定 (メイン役職)
	  $result = $target->main_role;
	}
	else{ //占い師の処理
	  if($target->IsLive(true) && $target->IsFox() && ! $target->IsChildFox() &&
	     ! $target->IsRole('white_fox', 'black_fox')){ //呪殺判定
	    $USERS->Kill($target->user_no, 'FOX_DEAD');
	  }
	  $result = $last_wolf_flag ? 'human' : $target->DistinguishMage(); //判定結果を取得
	}
      }
    }

    //占い結果を登録 (特殊占い能力者は除外)
    if($user->IsRole('emerald_fox') || $user->IsRoleGroup('fairy')) continue;
    $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
    $action = $user->IsChildFox() ? 'CHILD_FOX_RESULT' : 'MAGE_RESULT';
    $ROOM->SystemMessage($sentence . "\t" . $result, $action);
  }

  if($ROOM->date == 1){
    //-- コピー系レイヤー --//
    //さとり系の追加サブ役職リスト (さとり => サトラレ, イタコ => 口寄せ)
    $scanner_list = array('mind_scanner' => 'mind_read', 'evoke_scanner' => 'mind_evoke');
    foreach($vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){ //さとり系の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      //対象者にサブ役職を追加
      $add_role = $scanner_list[$user->main_role] . '[' . strval($user->user_no) . ']';
      $USERS->ByUname($target_uname)->AddRole($add_role);
    }

    foreach($vote_data['MANIA_DO'] as $uname => $target_uname){ //神話マニア系の処理
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      if($user->IsRole('unknown_mania')){ //鵺
	//コピー先をセット
	$user->ReplaceRole('unknown_mania', 'unknown_mania[' . strval($target->user_no) . ']');

	//共鳴者を追加
	$add_role = 'mind_friend[' . strval($user->user_no) . ']';
	$user->AddRole($add_role);
	$target->AddRole($add_role);
      }
      elseif($user->IsRole('trick_mania')){ //神話マニア
	//コピー処理
	$actor_flag = false;
	if($target->IsRoleGroup('mania')){ //神話マニア系を指定した場合は村人
	  $result =  'human';
	  $actor_flag = true;
	}
	elseif($target->IsRole('revive_priest')){
	  $result = $target->main_role;
	  $actor_flag = true;
	}
	else{
	  foreach($vote_data as $action => $stack){
	    if(array_key_exists($target->uname, $stack)){
	      $actor_flag = true;
	      break;
	    }
	  }
	  $result = $target->main_role;
	}
	$user->ReplaceRole('trick_mania', $result);
	$user->AddRole('copied');
	if(! $actor_flag && ! $target->IsDummyBoy()){
	  switch($target->main_role){
	  case 'human':
	  case 'elder':
	  case 'saint':
	  case 'executor':
	  case 'suspect':
	  case 'unconscious':
	    $stack_role = 'human';
	    break;

	  case 'medium':
	    $stack_role = 'necromancer';
	    break;

	  case 'reporter':
	  case 'anti_voodoo':
	    $stack_role = 'guard';
	    break;

	  case 'poison_cat':
	  case 'revive_cat':
	  case 'sacrifice_cat':
	    $stack_role = 'poison_cat';
	    break;

	  case 'doll_master':
	    $stack_role = 'doll';
	    break;

	  default:
	    $stack_role = array_pop(explode('_', $target->main_role));
	    break;
	  }
	  $target->ReplaceRole($target->main_role, $stack_role);
	}

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'MANIA_RESULT');
      }
      else{ //神話マニア
	//コピー処理 (神話マニア系を指定した場合は村人)
	$result = $target->IsRoleGroup('mania') ? 'human' : $target->main_role;
	$user->ReplaceRole('mania', $result);
	$user->AddRole('copied');

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'MANIA_RESULT');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($USERS->rows as $user){ //天人の帰還処理
	if($user->IsDummyBoy() || ! $user->IsRole('revive_priest')) continue;
	if($user->IsLovers()){
	  $user->LostAbility();
	}
	elseif($user->IsLive(true)){
	  $USERS->Kill($user->user_no, 'PRIEST_RETURNED');
	}
      }
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

      if($target == $wolf_target){ //尾行成功
	if($target->uname == $guarded_uname) continue; //護衛されていた場合は何も出ない
	$sentence = $user->handle_name . "\t" .
	  $USERS->GetHandleName($wolf_target->uname, true) . "\t" .
	  $USERS->GetHandleName($voted_wolf->uname, true);
	$ROOM->SystemMessage($sentence, 'REPORTER_SUCCESS');
      }
      elseif($target->IsRoleGroup('wolf', 'fox') && $target->IsLive(true)){
	//尾行対象が人狼か妖狐なら殺される
	$USERS->Kill($user->user_no, 'REPORTER_DUTY');
      }
    }

    //-- 反魂系レイヤー --//
    foreach($reverse_list as $target_uname => $flag){
      if(! $flag) continue;
      $target = $USERS->ByUname($target_uname);
      if($target->IsPossessedGroup()){ //憑依能力者対応
	if($target->revive_flag) break; //蘇生済みならスキップ

	$virtual_target = $USERS->ByVirtual($target->user_no);
	if($target != $virtual_target){ //憑依中ならリセット
	  //本人の憑依リセット処理
	  $target->ReturnPossessed('possessed_target', $ROOM->date + 1);

	  //憑依先のリセット処理
	  $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
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
      else{ //憑依されていたらリセット
	$real_target = $USERS->ByReal($target->user_no);
	if($target != $real_target){
	  $target->ReturnPossessed('possessed', $ROOM->date + 1);
	}
	$target->Revive(); //蘇生処理
      }
    }

    //-- 蘇生系レイヤー --//
    if(! $ROOM->IsOpenCast()){
      foreach($vote_data['POISON_CAT_DO'] as $uname => $target_uname){ //蘇生能力者の処理
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //直前に死んでいたら無効

	$target = $USERS->ByUname($target_uname); //対象者の情報を取得

	//蘇生判定
	if($user->IsRole('poison_cat')){
	  $revive_rate = 25;
	}
	elseif($user->IsRole('revive_cat')){
	  $revive_times = (int)$user->partner_list['revive_cat'][0];
	  $revive_rate = ceil(80 / pow(4, $revive_times));
	}
	elseif($user->IsRole('sacrifice_cat', 'revive_fox')){
	  $revive_rate = 100;
	}
	$rate = mt_rand(1, 100); //蘇生判定用乱数
	//$rate = 5; //mt_rand(1, 10); //テスト用
	//PrintData($revive_rate, 'Revive Info: ' . $user->uname . ' => ' . $target->uname);

	$result = 'failed';
	do{
	  if($rate > $revive_rate) break; //蘇生失敗
	  if(! $user->IsRole('sacrifice_cat') && $rate <= floor($revive_rate / 5)){ //誤爆蘇生
	    $revive_target_list = array();
	    //現時点の身代わり君と猫又が選んだ人以外の死者と憑依者を検出
	    foreach($USERS->rows as $revive_target){
	      if($revive_target->IsDummyBoy() || $revive_target->revive_flag ||
		 $target == $revive_target) continue;

	      if($revive_target->dead_flag ||
		 ! $USERS->IsVirtualLive($revive_target->user_no, true)){
		$revive_target_list[] = $revive_target->uname;
	      }
	    }
	    if($ROOM->test_mode) PrintData($revive_target_list, 'Revive Target');
	    if(count($revive_target_list) > 0){ //候補がいる時だけ入れ替える
	      $target = $USERS->ByUname(GetRandom($revive_target_list));
	    }
	  }
	  //$target = $USERS->ByID(3); //テスト用
	  //PrintData($target->uname, 'Revive User');
	  if($target->IsRoleGroup('cat', 'revive') || $target->IsRole('detective_common') ||
	     $target->IsLovers() || $target->possessed_reset || $target->IsDrop()){
	    break; //蘇生能力者・恋人・蘇生辞退者なら蘇生失敗
	  }

	  $result = 'success';
	  if($target->IsPossessedGroup()){ //憑依能力者対応
	    if($target->revive_flag) break; //蘇生済みならスキップ

	    $virtual_target = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //確定死者
	      if($target != $virtual_target){ //憑依後に死亡していた場合はリセット処理を行う
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
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
		//本人の憑依リセット処理
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);

		//憑依先のリセット処理
		$virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
	      }

	      //憑依予定者が居たらキャンセル
	      if(array_key_exists($target->uname, $possessed_target_list)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	    }
	  }
	  else{ //憑依されていたらリセット
	    $real_target = $USERS->ByReal($target->user_no);
	    if($target != $real_target){
	      $target->ReturnPossessed('possessed', $ROOM->date + 1);
	    }
	  }
	  $target->Revive(); //蘇生処理
	}while(false);

	if($result == 'success'){
	  if($user->IsRole('revive_cat')){ //仙狸の蘇生成功カウントを更新
	    //$revive_times = (int)$user->partner_list['revive_cat'][0]; //取得済みのはず
	    $base_role = $user->main_role;
	    if($revive_times > 0) $base_role .= '[' . strval($revive_times) . ']';

	    $new_role = $user->main_role . '[' . strval($revive_times + 1) . ']';
	    $user->ReplaceRole($base_role, $new_role);
	  }
	  elseif($user->IsRole('sacrifice_cat')){ //猫神の死亡処理
	    $USERS->Kill($user->user_no, 'SACRIFICE');
	  }
	  elseif($user->IsRole('revive_fox')){ //仙狐の能力失効処理
	    $user->LostAbility();
	  }
	}
	else{
	  $ROOM->SystemMessage($target->handle_name, 'REVIVE_FAILED');
	}
	$sentence = $user->handle_name . "\t";
	$sentence .= $USERS->GetHandleName($target->uname) . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'POISON_CAT_RESULT');
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

      //憑依先が競合したら失敗扱い
      if(count(array_keys($possessed_do_stack, $target_uname)) > 1) continue;

      //誰かが憑依していたら失敗扱い
      if($target_uname != $USERS->ByRealUname($target_uname)->uname) continue;

      $target = $USERS->ByUname($target_uname); //対象者の情報を取得
      if($target->revive_flag) continue; //蘇生されていたら失敗扱い

      //人外が他陣営の人外には憑依できない
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
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'VOODOO_KILLER_SUCCESS';

    $voodoo_killer_list = array_keys($voodoo_killer_target_list, $target_uname); //成功者を検出
    foreach($voodoo_killer_list as $uname){
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

  //PrintData($anti_voodoo_success_list, 'SUCCESS [anti_voodoo]');
  foreach($anti_voodoo_success_list as $target_uname => $flag){ //厄神の厄払い結果処理
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'ANTI_VOODOO_SUCCESS';

    $anti_voodoo_list = array_keys($anti_voodoo_target_list, $target_uname); //成功者を検出
    foreach($anti_voodoo_list as $uname){
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  //-- 司祭系レイヤー --//
  $priest_flag = false;
  $bishop_priest_flag = false;
  $crisis_priest_flag = false;
  $revive_priest_list = array();
  $live_count = array();
  $dead_count = 0;
  foreach($USERS->rows as $user){ //司祭系の情報収集
    if(! $user->IsDummyBoy()){
      $priest_flag        |= $user->IsRole('priest');
      $bishop_priest_flag |= $user->IsRole('bishop_priest');
      $crisis_priest_flag |= $user->IsRole('crisis_priest');
      if($user->IsActive('revive_priest')) $revive_priest_list[] = $user->uname;
    }
    if($user->IsDead(true)){
      if($user->GetCamp() != 'human') $dead_count++;
      continue;
    }

    $live_count['total']++;
    if($user->IsWolf()) $live_count['wolf']++;
    elseif($user->IsFox()) $live_count['fox']++;
    else{
      $live_count['human']++;
      if($user->GetCamp() == 'human') $live_count['human_side']++;
    }
    if($user->IsLovers()) $live_count['lovers']++;
  }
  //PrintData($live_count, 'Live Count');

  if($priest_flag && $ROOM->date > 2 && ($ROOM->date % 2) == 1){ //司祭の処理
    $ROOM->SystemMessage($live_count['human_side'], 'PRIEST_RESULT');
  }
  if($bishop_priest_flag && $ROOM->date > 1 && ($ROOM->date % 2) == 0){ //司教の処理
    $ROOM->SystemMessage($dead_count, 'BISHOP_PRIEST_RESULT');
  }

  if($crisis_priest_flag || count($revive_priest_list) > 0){ //預言者、天人の処理
    //「人外勝利前日」判定
    $crisis_priest_result = '';
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
      foreach($revive_priest_list as $uname){
	$user = $USERS->ByUname($uname);
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

  if($ROOM->test_mode) return;

  //次の日にする
  $ROOM->date++;
  $ROOM->day_night = 'day';
  SendQuery("UPDATE room SET date = {$ROOM->date}, day_night = 'day' WHERE room_no = {$ROOM->id}");

  //夜が明けた通知
  $ROOM->Talk("MORNING\t" . $ROOM->date);
  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //処刑投票のカウントを 1 に初期化(再投票で増える)
  $ROOM->UpdateTime(); //最終書き込みを更新
  //DeleteVote(); //今までの投票を全部削除

  CheckVictory(); //勝敗のチェック
  mysql_query('COMMIT'); //一応コミット
}

//役職の所属グループを判別する
function DistinguishRoleGroup($role){
  global $GAME_CONF;

  foreach($GAME_CONF->main_role_group_list as $key => $value){
    if(strpos($role, $key) !== false) return $value;
  }
  return 'human';
}

//ランダムメッセージを挿入する
function InsertRandomMessage(){
  global $MESSAGE, $GAME_CONF, $ROOM;

  if(! $GAME_CONF->random_message) return;
  $ROOM->Talk(GetRandom($MESSAGE->random_message_list));
}
