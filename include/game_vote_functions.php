<?php
//投票結果出力
function OutputVoteResult($sentence, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //今までの投票を全部削除
  $title  = '汝は人狼なりや？[投票結果]';
  $header = '<div align="center"><a name="#game_top"></a>';
  $footer = '<br>'."\n" . $back_url . '</div>';
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
    $role_list = array(); //配列をリセット
    $role_list['wolf'] = round($user_count / 5);
    $role_list['trap_mad'] = round(($user_count - $role_list['wolf']) / 3);
    $role_list['assassin'] = $user_count - ($role_list['wolf'] + $role_list['trap_mad']);
  }
  elseif($ROOM->IsOption('chaosfull')){ //真・闇鍋
    $random_role_list = array();

    //-- 最小補正 --//
    foreach($CAST_CONF->chaos_fix_role_list as $key => $value){ //最小補正用リスト
      $fix_role_group_list[DistinguishRoleGroup($key)] = $value;
    }

    //人狼
    $add_count = round($user_count / $CAST_CONF->min_wolf_rate) - $fix_role_group_list['wolf'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if(    $rand <  3) $random_role_list['boss_wolf']++;
      elseif($rand <  5) $random_role_list['tongue_wolf']++;
      elseif($rand <  8) $random_role_list['wise_wolf']++;
      elseif($rand < 11) $random_role_list['poison_wolf']++;
      elseif($rand < 15) $random_role_list['resist_wolf']++;
      elseif($rand < 16) $random_role_list['cursed_wolf']++;
      elseif($rand < 18) $random_role_list['possessed_wolf']++;
      elseif($rand < 28) $random_role_list['cute_wolf']++;
      elseif($rand < 31) $random_role_list['scarlet_wolf']++;
      elseif($rand < 33) $random_role_list['silver_wolf']++;
      else               $random_role_list['wolf']++;
    }

    //妖狐
    $add_count = floor($user_count / $CAST_CONF->min_fox_rate) - $fix_role_group_list['fox'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if($rand < 1)       $random_role_list['cursed_fox']++;
      elseif($rand <  3)  $random_role_list['voodoo_fox']++;
      elseif($rand <  6)  $random_role_list['poison_fox']++;
      elseif($rand <  9)  $random_role_list['white_fox']++;
      elseif($rand < 10)  $random_role_list['black_fox']++;
      elseif($rand < 12)  $random_role_list['cute_fox']++;
      elseif($rand < 15)  $random_role_list['scarlet_fox']++;
      elseif($rand < 16)  $random_role_list['silver_fox']++;
      elseif($rand < 18)  $random_role_list['child_fox']++;
      else                $random_role_list['fox']++;
    }

    //-- ランダム配役 --//
    $add_count = $user_count - (array_sum($random_role_list) +
				array_sum($CAST_CONF->chaos_fix_role_list));
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 1000);
      if(    $rand <  20) $random_role_list['mage']++;
      elseif($rand <  25) $random_role_list['soul_mage']++;
      elseif($rand <  35) $random_role_list['psycho_mage']++;
      elseif($rand <  50) $random_role_list['sex_mage']++;
      elseif($rand <  58) $random_role_list['voodoo_killer']++;
      elseif($rand <  70) $random_role_list['dummy_mage']++;
      elseif($rand < 105) $random_role_list['necromancer']++;
      elseif($rand < 110) $random_role_list['soul_necromancer']++;
      elseif($rand < 120) $random_role_list['yama_necromancer']++;
      elseif($rand < 140) $random_role_list['dummy_necromancer']++;
      elseif($rand < 170) $random_role_list['medium']++;
      elseif($rand < 180) $random_role_list['priest']++;
      elseif($rand < 260) $random_role_list['common']++;
      elseif($rand < 270) $random_role_list['dummy_common']++;
      elseif($rand < 310) $random_role_list['guard']++;
      elseif($rand < 315) $random_role_list['poison_guard']++;
      elseif($rand < 325) $random_role_list['reporter']++;
      elseif($rand < 340) $random_role_list['anti_voodoo']++;
      elseif($rand < 360) $random_role_list['dummy_guard']++;
      elseif($rand < 380) $random_role_list['poison']++;
      elseif($rand < 385) $random_role_list['strong_poison']++;
      elseif($rand < 395) $random_role_list['incubate_poison']++;
      elseif($rand < 410) $random_role_list['dummy_poison']++;
      elseif($rand < 420) $random_role_list['poison_cat']++;
      elseif($rand < 450) $random_role_list['pharmacist']++;
      elseif($rand < 470) $random_role_list['assassin']++;
      elseif($rand < 490) $random_role_list['mind_scanner']++;
      elseif($rand < 505) $random_role_list['jealousy']++;
      elseif($rand < 520) $random_role_list['suspect']++;
      elseif($rand < 540) $random_role_list['unconscious']++;
      elseif($rand < 590) $random_role_list['wolf']++;
      elseif($rand < 600) $random_role_list['boss_wolf']++;
      elseif($rand < 615) $random_role_list['tongue_wolf']++;
      elseif($rand < 630) $random_role_list['wise_wolf']++;
      elseif($rand < 645) $random_role_list['poison_wolf']++;
      elseif($rand < 660) $random_role_list['resist_wolf']++;
      elseif($rand < 665) $random_role_list['cursed_wolf']++;
      elseif($rand < 675) $random_role_list['possessed_wolf']++;
      elseif($rand < 705) $random_role_list['cute_wolf']++;
      elseif($rand < 715) $random_role_list['scarlet_wolf']++;
      elseif($rand < 730) $random_role_list['silver_wolf']++;
      elseif($rand < 750) $random_role_list['mad']++;
      elseif($rand < 760) $random_role_list['fanatic_mad']++;
      elseif($rand < 765) $random_role_list['whisper_mad']++;
      elseif($rand < 775) $random_role_list['jammer_mad']++;
      elseif($rand < 785) $random_role_list['voodoo_mad']++;
      elseif($rand < 800) $random_role_list['corpse_courier_mad']++;
      elseif($rand < 810) $random_role_list['dream_eater_mad']++;
      elseif($rand < 820) $random_role_list['trap_mad']++;
      elseif($rand < 835) $random_role_list['fox']++;
      elseif($rand < 842) $random_role_list['white_fox']++;
      elseif($rand < 849) $random_role_list['black_fox']++;
      elseif($rand < 856) $random_role_list['poison_fox']++;
      elseif($rand < 861) $random_role_list['voodoo_fox']++;
      elseif($rand < 864) $random_role_list['cursed_fox']++;
      elseif($rand < 870) $random_role_list['cute_fox']++;
      elseif($rand < 875) $random_role_list['scarlet_fox']++;
      elseif($rand < 880) $random_role_list['silver_fox']++;
      elseif($rand < 890) $random_role_list['child_fox']++;
      elseif($rand < 915) $random_role_list['cupid']++;
      elseif($rand < 925) $random_role_list['self_cupid']++;
      elseif($rand < 930) $random_role_list['mind_cupid']++;
      elseif($rand < 945) $random_role_list['chiroptera']++;
      elseif($rand < 950) $random_role_list['poison_chiroptera']++;
      elseif($rand < 955) $random_role_list['cursed_chiroptera']++;
      elseif($rand < 960) $random_role_list['dummy_chiroptera']++;
      elseif($rand < 980) $random_role_list['mania']++;
      elseif($rand < 990) $random_role_list['unknown_mania']++;
      elseif($rand < 993) $random_role_list['quiz']++;
      else                $random_role_list['human']++;
    }

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
	$this_key = key($random_role_group_list->$name);
	//PrintData($this_key, "　　target");
	$random_role_group_list->{$name}[$this_key]--;
	$role_list[$this_key]--;
	$role_list['human']++;
	//PrintData($random_role_group_list->$name, "　　$over_count: delete");

	//0 になった役職はリストから除く
	if($role_list[$this_key] < 1) unset($role_list[$this_key]);
	if($random_role_group_list->{$name}[$this_key] < 1){
	  unset($random_role_group_list->{$name}[$this_key]);
	}
      }
    }
    //PrintData($role_list, '2nd_list'); //テスト用

    //神話マニア村以外なら一定数以上の村人を別の役職に振り返る
    if(strpos($option_role, 'full_mania') === false){
      $over_count = $role_list['human'] - round($user_count * $CAST_CONF->max_human_rate);
      if($over_count > 0){
	$role_list[$CAST_CONF->chaos_replace_human_role] += $over_count;
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
  }
  else{ //通常村
    //埋毒者 (村人2 → 埋毒者1、人狼1)
    if(strpos($option_role, 'poison') !== false && $user_count >= $CAST_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //キューピッド (14人はハードコード / 村人 → キューピッド)
    if(strpos($option_role, 'cupid') !== false &&
       ($user_count == 14 || $user_count >= $CAST_CONF->cupid)){
      $role_list['human']--;
      $role_list['cupid']++;
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

    //神話マニア (村人 → 神話マニア)
    if(strpos($option_role, 'mania') !== false && $user_count >= $CAST_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //巫女 (村人 → 巫女1、狂信者1)
    if(strpos($option_role, 'medium') !== false && $user_count >= $CAST_CONF->medium){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['fanatic_mad']++;
    }
  }

  //神話マニア村
  if(strpos($option_role, 'full_mania') !== false){
    $role_list['mania'] += $role_list['human'];
    $role_list['human'] = 0;
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
function MakeRoleNameList($role_count_list, $chaos = NULL){
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
      elseif(strpos($key, 'cupid') !== false)
	$main_role_list['cupid'] += $value;
      elseif(strpos($key, 'mania') !== false)
	$main_role_list['mania'] += $value;
      elseif(strpos($key, 'quiz') !== false)
	$main_role_list['quiz'] += $value;
      elseif(strpos($key, 'chiroptera') !== false)
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

  $user_list = $USERS->GetLivingUsers(); //生きているユーザ数を取得
  if(! $ROOM->test_mode){ //投票総数を取得して全員が投票していなければ処理スキップ
    //共通クエリをセット
    $query = "FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";

    if(FetchResult("SELECT COUNT(uname) $query") != count($user_list)) return false;
    $query .= ' AND';
  }

  $max_voted_number = 0; //最多得票数
  $vote_kill_uname = ''; //処刑される人のユーザ名
  $live_uname_list   = array(); //生きている人のユーザ名リスト
  $vote_message_list = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list      = array(); //能力者たちの投票結果

  foreach($user_list as $uname){ //個別の投票データを収集
    $user = $USERS->ByVirtualUname($uname);

    //自分の得票数を取得
    $voted_number = ($ROOM->test_mode ? (int)$RQ_ARGS->TestItems->vote_day_count_list[$uname] :
		     FetchResult("SELECT SUM(vote_number) $query target_uname = '{$uname}'"));

    //サブ役職の得票補正
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

    //自分の投票先の情報を取得
    $array = ($ROOM->test_mode ? $RQ_ARGS->TestItems->vote_day[$uname] :
	      FetchNameArray("SELECT target_uname, vote_number $query uname = '{$uname}'"));
    $target = $USERS->ByUname($array['target_uname']);
    $virtual_target = $USERS->ByVirtual($target->user_no);
    $vote_number = (int)$array['vote_number'];

    //システムメッセージ用の配列を生成
    $message_list = array('target'       => $virtual_target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);

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

  //反逆者の判定
  if(isset($ability_list['rebel']) && $ability_list['rebel'] == $ability_list['authority']){
    //権力者と反逆者の投票数を 0 にする
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //投票先の得票数を補正する
    $this_uname = $ability_list['rebel'];
    if($vote_message_list[$this_uname]['voted_number'] > 3)
      $vote_message_list[$this_uname]['voted_number'] -= 3;
    else
      $vote_message_list[$this_uname]['voted_number'] = 0;
    $vote_count_list[$this_uname] = $vote_message_list[$this_uname]['voted_number'];
  }

  //投票結果をタブ区切りで生成してシステムメッセージに登録
  // print_r($vote_message_list); //デバッグ用
  foreach($live_uname_list as $this_uname){
    $this_array = $vote_message_list[$this_uname];
    $this_handle       = $USERS->GetHandleName($this_uname);
    $this_target       = $this_array['target'];
    $this_voted_number = $this_array['voted_number'];
    $this_vote_number  = $this_array['vote_number'];

    //最大得票数を更新
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //(誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $this_handle . "\t" . $this_target . "\t" .
      (int)$this_voted_number ."\t" . (int)$this_vote_number . "\t" . $RQ_ARGS->vote_times;
    InsertSystemMessage($sentence, 'VOTE_KILL');
  }

  //最大得票数のユーザ名 (処刑候補者) のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  do{ //処刑者決定ルーチン
    if(count($max_voted_uname_list) == 1){ //一人だけなら決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    if(in_array($ability_list['decide'], $max_voted_uname_list)){ //決定者
      $vote_kill_uname = $ability_list['decide'];
      break;
    }

    if(in_array($ability_list['bad_luck'], $max_voted_uname_list)){ //不幸
      $vote_kill_uname = $ability_list['bad_luck'];
      break;
    }

    if(in_array($ability_list['impatience'], $max_voted_uname_list)){ //短気
      $vote_kill_uname = $ability_list['impatience'];
      break;
    }

    //幸運を処刑者候補から除く
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['good_luck']));
    if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    //疫病神の投票先を処刑者候補から除く
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['plague']));
    if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }
  }while(false);

  if($vote_kill_uname != ''){ //処刑処理実行
    $vote_target = $USERS->ByRealUname($vote_kill_uname); //ユーザ情報を取得
    $USERS->Kill($vote_target->user_no, 'VOTE_KILLED'); //処刑処理
    unset($live_uname_list[$vote_target->user_no]); //処刑者を生存者リストから除く
    $voter_list = array_keys($vote_target_list, $vote_target->uname); //投票した人を取得

    $pharmacist_success = false; //解毒成功フラグを初期化
    foreach($user_list as $this_uname){ //薬師の処理
      $user = $USERS->ByUname($this_uname);
      if(! $user->IsRole('pharmacist')) continue;

      $this_target = $USERS->ByUname($vote_target_list[$user->uname]); //投票先の情報を取得
      if(! $this_target->IsRoleGroup('poison') || $this_target->IsRole('dummy_poison')){
	$this_result = 'nothing'; //非毒能力者か夢毒者
      }
      elseif($this_target->IsRole('poison_guard')) $this_result = 'limited'; //騎士は対象外
      else{
	if($this_target->IsRole('strong_poison')) $this_result = 'strong'; //強毒者
	elseif($this_target->IsRole('incubate_poison')){ //潜毒者は 5 日目以降に強毒を持つ
	  $this_result = ($ROOM->date >= 5 ? 'strong' : 'nothing');
	}
	else $this_result = 'poison';

	//処刑者なら解毒する
	if($this_target == $vote_target && ($this_result == 'strong' || $this_result == 'poison')){
	  $this_result = 'success';
	  $pharmacist_success = true;
	}
      }

      //鑑定結果を登録
      $virtual_handle_name = $USERS->ByVirtual($this_target->user_no)->handle_name;
      $sentence = $user->handle_name . "\t" . $virtual_handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'PHARMACIST_RESULT');
    }

    //処刑された人が毒を持っていた場合
    do{
      if($pharmacist_success || ! $vote_target->IsPoison()) break; //毒能力の発動判定

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list);
      $limited_poison_target_list = array(); //特殊毒の場合はターゲットが限定される

      //print_r($poison_target_list); //デバッグ用
      if($vote_target->IsRole('strong_poison', 'incubate_poison')){ //強毒者・潜毒者
	foreach($poison_target_list as $this_uname){
	  if($USERS->ByRealUname($this_uname)->IsRoleGroup('wolf', 'fox')){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_wolf')){ //毒狼
	foreach($poison_target_list as $this_uname){
	  if(! $USERS->ByRealUname($this_uname)->IsWolf()){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_fox')){ //管狐
	foreach($poison_target_list as $this_uname){
	  if(! $USERS->ByRealUname($this_uname)->IsFox()){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_chiroptera')){ //毒蝙蝠
	foreach($poison_target_list as $this_uname){
	  if($USERS->ByRealUname($this_uname)->IsRoleGroup('wolf', 'fox', 'chiroptera')){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      print_r($poison_target_list); //デバッグ用
      $poison_target = $USERS->ByRealUname(GetRandom($poison_target_list)); //対象者を決定

      if($poison_target->IsActiveRole('resist_wolf')){ //抗毒狼には無効
	$poison_target->AddRole('lost_ability');
	break;
      }

      $USERS->Kill($poison_target->user_no, 'POISON_DEAD_day'); //死亡処理
    }while(false);

    //霊能系の判定結果
    $virtual_handle_name = $USERS->ByVirtual($vote_target->user_no)->handle_name;
    $sentence_header = $virtual_handle_name . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能者の判定結果
    if($vote_target->IsRole('boss_wolf', 'possessed_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('cursed_fox', 'white_fox', 'black_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsWolf()){
      $necromancer_result = 'wolf';
    }
    else{
      $necromancer_result = 'human';
    }

    //火車の判定
    $flag_stolen = false;
    foreach($voter_list as $this_uname){
      $flag_stolen |= $USERS->ByRealUname($this_uname)->IsRole('corpse_courier_mad');
    }

    if($USERS->IsAppear('necromancer')){ //霊能者がいればシステムメッセージを登録
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      InsertSystemMessage($sentence, $action);
    }

    if($USERS->IsAppear('soul_necromancer')){ //雲外鏡の判定結果
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      InsertSystemMessage($sentence, 'SOUL_' . $action);
    }

    if($USERS->IsAppear('dummy_necromancer')){ //夢枕人の判定結果は村人と人狼が反転する
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  foreach($user_list as $this_uname){ //橋姫の処理
    $user = $USERS->ByRealUname($this_uname);
    if($vote_kill_uname == $user->uname || ! $user->IsRole('jealousy')) continue;

    $cupid_list = array(); //キューピッドのID => 恋人のID
    $jealousy_voted_list = array_keys($vote_target_list, $user->uname); //橋姫への投票者リスト
    foreach($jealousy_voted_list as $this_voted_uname){
      $voted_user = $USERS->ByRealUname($this_voted_uname);
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

  //特殊サブ役職の突然死処理
  //投票者対象ユーザ名 => 人数 の配列を生成
  // print_r($vote_target_list); //デバッグ用
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

    if($reason != '') $USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_' . $reason);
  }
  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  if($ROOM->test_mode) return $vote_message_list;

  if($vote_kill_uname != ''){ //夜に切り替え
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = {$ROOM->id}"); //夜にする
    InsertSystemTalk('NIGHT', ++$ROOM->system_time, 'night system'); //夜がきた通知
    if(! CheckVictory()) InsertRandomMessage(); //ランダムメッセージ
  }
  else{ //再投票処理
    $next_vote_times = $RQ_ARGS->vote_times + 1; //投票回数を増やす
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = {$ROOM->id}
			AND date = {$ROOM->date} AND type = 'VOTE_TIMES'");

    //システムメッセージ
    InsertSystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    InsertSystemTalk("再投票になりました( {$RQ_ARGS->vote_times} 回目)", ++$ROOM->system_time);
    CheckVictory(true); //勝敗判定
  }
  UpdateTime(); //最終書き込みを更新
  mysql_query('COMMIT'); //一応コミット
}

//夜の役職の投票状況をチェックして投票結果を返す
function CheckVoteNight($action, $role, $dummy_boy_role = '', $not_type = ''){
  global $ROOM;

  //投票情報を取得
  $query_vote = "SELECT uname, target_uname FROM vote WHERE room_no = {$ROOM->id} " .
    "AND date = {$ROOM->date} AND situation = '$action'";
  $vote_data = FetchAssoc($query_vote);
  $vote_count = count($vote_data); //投票人数を取得

  if($not_type != ''){ //キャンセルタイプの投票情報を取得
    $query_not_type = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
      "AND date = {$ROOM->date} AND situation = '$not_type'";
    $vote_count += FetchResult($query_not_type); //投票人数に追加
  }

  //狼の噛みは一人で OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $vote_data[0] : false);

  //生きている対象役職の人数をカウント
  $query_role = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} ".
    "AND live = 'live' AND user_no > 0 AND ";
  if($action == 'CUPID_DO'){ //夢求愛者対応
    $query_role .= "(role LIKE '{$role}%' OR role LIKE 'dummy_chiroptera%')";
  }
  else{
    $query_role .= "role LIKE '{$role}%'";
  }
  if($action == 'TRAP_MAD_DO') $query_role .= " AND !(role LIKE '%lost_ability%')"; //罠師対応
  $role_count = FetchResult($query_role);

  //初日、身代わり君が特定の役職だった場合はカウントしない
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $vote_data : false);
}

//夜の集計処理
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  if($ROOM->test_mode){
    $vote_data = $RQ_ARGS->TestItems->vote_night;
  }
  else{
    //コマンドチェック
    $situation_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO', 'DREAM_EAT',
			    'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO', 'VOODOO_MAD_DO',
			    'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO',
			    'POISON_CAT_DO', 'POISON_CAT_NOT_DO', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO',
			    'MIND_SCANNER_DO', 'MANIA_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'CUPID_DO');
    CheckSituation($situation_list);

    //狼の投票チェック
    $vote_data->wolf = CheckVoteNight('WOLF_EAT', '%wolf');
    if($vote_data->wolf === false) return false;

    //初日、身代わり君が特定の役職だった場合はカウントしない
    if($ROOM->date == 1 && $ROOM->IsDummyBoy()){
      $this_dummy_boy_role = $USERS->GetRole('dummy_boy');
      //カウント対象外役職リスト
      $exclude_role_list   = array('mage', 'voodoo_killer', 'jammer_mad', 'voodoo_mad',
				   'mind_scanner', 'cupid', 'dummy_chiroptera', 'mania');

      foreach($exclude_role_list as $this_role){
	if(strpos($this_dummy_boy_role, $this_role) !== false){
	  $dummy_boy_role = $this_role;
	  break;
	}
      }
    }

    //常に投票できる役職の投票チェック
    $vote_data->mage = CheckVoteNight('MAGE_DO', '%mage', $dummy_boy_role);
    if($vote_data->mage === false) return false;

    $vote_data->voodoo_killer = CheckVoteNight('VOODOO_KILLER_DO', 'voodoo_killer', $dummy_boy_role);
    if($vote_data->voodoo_killer === false) return false;

    $vote_data->jammer_mad = CheckVoteNight('JAMMER_MAD_DO', 'jammer_mad', $dummy_boy_role);
    if($vote_data->jammer_mad === false) return false;

    $vote_data->voodoo_mad = CheckVoteNight('VOODOO_MAD_DO', 'voodoo_mad', $dummy_boy_role);
    if($vote_data->voodoo_mad === false) return false;

    $vote_data->voodoo_fox = CheckVoteNight('VOODOO_FOX_DO', 'voodoo_fox');
    if($vote_data->voodoo_fox === false) return false;

    $vote_data->child_fox = CheckVoteNight('CHILD_FOX_DO', 'child_fox');
    if($vote_data->child_fox === false) return false;

    if($ROOM->date == 1){ //初日のみ投票できる役職をチェック
      $vote_data->mania = CheckVoteNight('MANIA_DO', '%mania', $dummy_boy_role);
      if($vote_data->mania === false) return false;

      $vote_data->mind_scanner = CheckVoteNight('MIND_SCANNER_DO', 'mind_scanner', $dummy_boy_role);
      if($vote_data->mind_scanner === false) return false;

      if(CheckVoteNight('CUPID_DO', '%cupid', $dummy_boy_role) === false) return false;
    }
    else{ //二日目以降投票できる役職をチェック
      $vote_data->dream_eater_mad = CheckVoteNight('DREAM_EAT', 'dream_eater_mad', $dummy_boy_role);
      if($vote_data->dream_eater_mad === false) return false;

      $vote_data->trap_mad = CheckVoteNight('TRAP_MAD_DO', 'trap_mad', '', 'TRAP_MAD_NOT_DO');
      if($vote_data->trap_mad === false) return false;

      if(($vote_data->guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;

      $vote_data->anti_voodoo = CheckVoteNight('ANTI_VOODOO_DO', 'anti_voodoo');
      if($vote_data->anti_voodoo === false) return false;

      if(($vote_data->reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;

      if(! $ROOM->IsOpenCast()){
	$vote_data->poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat', '', 'POISON_CAT_NOT_DO');
	if($vote_data->poison_cat === false) return false;
      }

      $vote_data->assassin = CheckVoteNight('ASSASSIN_DO', 'assassin', '', 'ASSASSIN_NOT_DO');
      if($vote_data->assassin === false) return false;
    }
  }

  //人狼の襲撃情報を取得
  $voted_wolf  = $USERS->ByUname($vote_data->wolf['uname']);
  $wolf_target = $USERS->ByUname($vote_data->wolf['target_uname']);

  $guarded_uname = ''; //護衛成功した人のユーザ名 //複数噛みに対応するならここは配列に変える
  $trap_target_list         = array(); //罠の設置先リスト
  $trapped_list             = array(); //罠にかかった人リスト
  $anti_voodoo_target_list  = array(); //厄神の護衛対象リスト
  $anti_voodoo_success_list = array(); //厄払い成功者リスト
  $dummy_guard_target_list  = array(); //夢守人の護衛対象リスト
  $possessed_target_list    = array(); //憑依予定者リスト => 憑依成立フラグ
  $possessed_target_id_list = array(); //憑依対象者リスト

  if($ROOM->date > 1){
    foreach($vote_data->trap_mad as $array){ //罠師の設置先リストを作成
      $user   = $USERS->ByUname($array['uname']);
      $target = $USERS->ByUname($array['target_uname']);

      $user->AddRole('lost_ability'); //一度設置したら能力失効

      //人狼に狙われていたら自分自身への設置以外は無効
      if($user != $wolf_target || $user == $target){
	$trap_target_list[$user->uname] = $target->uname;
      }
    }

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $trap_count_list = array_count_values($trap_target_list);
    foreach($trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $trap_count_list[$target_uname] > 1){
	$trapped_list[] = $uname;
      }
    }

    //狩人系の狩り対象リスト
    $hunt_target_list = array('jammer_mad', 'voodoo_mad', 'corpse_courier_mad',
			      'dream_eater_mad', 'trap_mad',
			      'cursed_fox', 'voodoo_fox',
			      'poison_chiroptera', 'cursed_chiroptera');
    foreach($vote_data->guard as $array){ //狩人系の処理
      $user     = $USERS->ByUname($array['uname']);
      $target   = $USERS->ByUname($array['target_uname']);
      $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);

      if($user->IsRole('dummy_guard')){ //夢守人
	$dummy_guard_target_list[$user->uname] = $target->uname; //護衛先をセット
	if($target->IsRole('dream_eater_mad')){ //獏の狩り判定
	  $USERS->Kill($target->user_no, 'HUNTED');
	  $action = 'GUARD_HUNTED';
	}
	else{ //獏を狩れなかった場合は護衛成功メッセージだけが出る
	  $action = 'GUARD_SUCCESS';
	}
	InsertSystemMessage($sentence, $action);
	continue;
      }

      if($target->IsRole($hunt_target_list)){ //狩り判定
	$USERS->Kill($target->user_no, 'HUNTED');
	InsertSystemMessage($sentence, 'GUARD_HUNTED');
      }

      if(in_array($target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$trapped_list[] = $user->uname;
	continue;
      }

      if($target != $wolf_target) continue; //護衛成功判定

      //騎士でない場合、一部の役職は護衛していても人狼に襲撃される
      if($user->IsRole('poison_guard') ||
	 ! $wolf_target->IsRole('reporter', 'assassin', 'priest')){
	$guarded_uname = $target->uname;
      }
      InsertSystemMessage($sentence, 'GUARD_SUCCESS');
    }
  }

  do{ //人狼の襲撃成功判定
    if($guarded_uname != '' || $ROOM->IsQuiz()) break; //護衛成功 or クイズ村仕様

    if(in_array($wolf_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
      $trapped_list[] = $voted_wolf->uname;
      break;
    }

    //襲撃先が人狼の場合は失敗する (銀狼が出現している場合のみ起きる)
    if($wolf_target->IsWolf()) break;

    //襲撃先が妖狐の場合は失敗する
    if($wolf_target->IsFox() && ! $wolf_target->IsRole('poison_fox', 'white_fox', 'child_fox')){
      InsertSystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    //襲撃処理
    if($voted_wolf->IsRole('possessed_wolf') && ! $wolf_target->IsDummyBoy() &&
       ! $wolf_target->IsFox()){ //憑狼の処理
      //襲撃先が厄神なら憑依リセット
      $possessed_target_list[$voted_wolf->user_no] = array(
	'target' => $wolf_target->user_no,
	'status' => ($wolf_target->IsRole('anti_voodoo') ? 'reset' : NULL));
      $wolf_target->dead_flag = true;
    }
    else{
      $USERS->Kill($wolf_target->user_no, 'WOLF_KILLED'); //通常狼の襲撃処理
    }

    if($voted_wolf->IsActiveRole('tongue_wolf')){ //舌禍狼の処理
      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      if($wolf_target->IsRole('human')) $voted_wolf->AddRole('lost_ability'); //村人なら能力失効

      InsertSystemMessage($sentence . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');
    }

    if($wolf_target->IsPoison()){ //毒死判定処理
      //襲撃者が抗毒狼か、襲撃者固定設定なら対象固定
      if($voted_wolf->IsRole('resist_wolf') || $GAME_CONF->poison_only_eater){
	$poison_target = $voted_wolf;
      }
      else{ //生きている狼からランダム選出
	$poison_target = $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));
      }

      if($poison_target->IsActiveRole('resist_wolf')){ //抗毒狼なら無効
	$poison_target->AddRole('lost_ability');
      }
      else{
	$USERS->Kill($poison_target->user_no, 'POISON_DEAD_night'); //毒死処理
      }
    }
  }while(false);

  //その他の能力者の投票処理
  /*
    人狼、占い師、ブン屋など、行動結果で死者が出るタイプは判定順に注意

    例1) どちらの判定を先に行うかで妖狐の生死が決まる (基本的には人狼の襲撃を優先する)
    人狼 → 占い師 → 妖狐

    例2) どちらの判定を先に行うかでブン屋の生死が決まる (現在は占い師が先)
    占い師 → 妖狐 ← ブン屋
  */

  foreach($possessed_target_list as $id => $array){ //憑依対象者の ID を収集
    $possessed_target_id_list[] = $array['target'];
  }

  if($ROOM->date > 1){
    $assassin_target_list = array(); //暗殺対象者リスト
    foreach($vote_data->assassin as $array){ //暗殺者の処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      $target_uname = $array['target_uname'];
      if(in_array($target_uname, $trap_target_list)){ //罠が設置されていたら死亡
	$trapped_list[] = $user->uname;
	continue;
      }

      $assassin_target_list[$target_uname] = true; //暗殺対象者リストに追加
    }

    foreach($trapped_list as $uname){ //罠の死亡処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');
    }

    foreach($assassin_target_list as $uname => $flag){ //暗殺処理
      $USERS->Kill($USERS->UnameToNumber($uname), 'ASSASSIN_KILLED');
    }

    foreach($vote_data->anti_voodoo as $array){ //厄神の処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($array['target_uname']);
      $anti_voodoo_target_list[$user->uname] = $target->uname;

      //憑依能力者対応
      if($target == $wolf_target &&
	 in_array($target->user_no, $possessed_target_id_list)){ //憑依予定先なら憑依キャンセル
	if($possessed_target_list[$voted_wolf->user_no]['status'] != 'reset'){
	  $possessed_target_list[$voted_wolf->user_no]['status'] = 'cancel';
	}
      }
      elseif($target->IsRole('possessed_wolf') &&
	     $target != $USERS->ByVirtual($target->user_no)){ //憑依者なら強制送還
	$possessed_target_list[$target->user_no]['status'] = 'reset';
      }
      else{
	continue;
      }
      $anti_voodoo_success_list[] = $target->uname;
    }
    //PrintData($possessed_target_list, 'Possessed Target [anti_voodoo]'); //テスト用

    foreach($vote_data->dream_eater_mad as $array){ //獏の処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
      $sentence = "\t" . $user->handle_name;

      if($target->IsRole('dummy_guard')){ //対象が夢守人なら返り討ちに合う
	$USERS->Kill($user->user_no, 'HUNTED');
	InsertSystemMessage($target->handle_name . $sentence, 'GUARD_HUNTED');
      }
      elseif(in_array($target->uname, $dummy_guard_target_list)){ //夢守人の護衛判定
	$USERS->Kill($user->user_no, 'HUNTED');
	$hunted_dummy_guard_list = array_keys($dummy_guard_target_list, $target->uname);
	foreach($hunted_dummy_guard_list as $uname){
	  InsertSystemMessage($USERS->GetHandleName($uname) . $sentence, 'GUARD_HUNTED');
	}
      }
      elseif($target->IsRoleGroup('dummy')){ //夢系能力者なら食い殺す
	$USERS->Kill($target->user_no, 'DREAM_KILLED');
      }
    }
  }

  $voodoo_killer_target_list  = array(); //陰陽師の解呪対象リスト
  $voodoo_killer_success_list = array(); //陰陽師の解呪成功者対象リスト
  foreach($vote_data->voodoo_killer as $array){ //陰陽師の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($target->IsRoleGroup('cursed', 'possessed_wolf')){ //呪い持ちか憑狼なら呪殺
      $USERS->Kill($target->user_no, 'CURSED');
      $voodoo_killer_success_list[] = $target->uname;
    }
    elseif($target == $wolf_target &&
	   in_array($target->user_no, $possessed_target_id_list)){ //憑依予定先ならキャンセル
      if($possessed_target_list[$voted_wolf->user_no]['status'] != 'reset'){
	$possessed_target_list[$voted_wolf->user_no]['status'] = 'cancel';
      }
      $voodoo_killer_success_list[] = $target->uname;
    }
    $voodoo_killer_target_list[$user->uname] = $target->uname; //解呪対象リストに追加
  }

  $voodoo_target_list = array(); //呪い系能力者の対象リスト
  foreach($vote_data->voodoo_mad as $array){ //呪術師の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($target->IsRoleGroup('cursed') && ! $target->dead_flag){ //呪返し判定
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $user->uname;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $voodoo_killer_target_list)){ //陰陽師の解呪判定
      $voodoo_killer_success_list[] = $target->uname;
    }
    else{
      $voodoo_target_list[$user->uname] = $target->uname;
    }
  }

  foreach($vote_data->voodoo_fox as $array){ //九尾の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($target->IsRoleGroup('cursed') && ! $target->dead_flag){ //呪返し判定
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $user->uname;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $voodoo_killer_target_list)){ //陰陽師の解呪判定
      $voodoo_killer_success_list[] = $target->uname;
    }
    else{
      $voodoo_target_list[$user->uname] = $target->uname;
    }
  }

  //呪い系能力者の対象先が重なった場合は呪返しを受ける
  $voodoo_count_list = array_count_values($voodoo_target_list);
  foreach($voodoo_target_list as $uname => $target_uname){
    if($voodoo_count_list[$target_uname] < 2) continue;

    if(in_array($uname, $anti_voodoo_target_list)){ //厄神の護衛判定
      $anti_voodoo_success_list[] = $user->uname;
    }
    else{
      $USERS->Kill($USERS->UnameToNumber($uname), 'CURSED');
    }
  }

  $jammer_target_list = array(); //妨害対象リスト
  foreach($vote_data->jammer_mad as $array){ //月兎の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    //呪返し判定
    if(($target->IsRoleGroup('cursed') && ! $target->dead_flag) ||
       in_array($target->uname, $voodoo_target_list)){
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $user->uname;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
      $anti_voodoo_success_list[] = $target->uname;
    }
    else{ //妨害対象者リストに追加
      $jammer_target_list[$target->uname] = $target->uname;
    }
  }

  //精神鑑定士の嘘つき判定対象役職リスト
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  foreach($vote_data->mage as $array){ //占い師系の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByRealUname($array['target_uname']); //対象者の情報を取得
    if($user->IsRole('dummy_mage')){ //夢見人の占い結果は村人と人狼を反転させる
      $result = $target->DistinguishMage(true);
    }
    elseif(in_array($user->uname, $jammer_target_list)){ //月兎の妨害判定
      $result = ($user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed');
    }
    elseif($user->IsRole('psycho_mage')){ //精神鑑定士の判定
      $result = 'psycho_mage_normal';
      foreach($psycho_mage_liar_list as $liar_role){
	if($target->IsRoleGroup($liar_role)){
	  $result = 'psycho_mage_liar';
	  break;
	}
      }
    }
    elseif($user->IsRole('sex_mage')){ //ひよこ鑑定士の判定
      $result = ($target->IsRoleGroup('chiroptera') ? 'chiroptera' : 'sex_' . $target->sex);
    }
    else{
      //呪返し判定
      if(($target->IsRoleGroup('cursed') && ! $target->dead_flag) ||
	 in_array($target->uname, $voodoo_target_list)){
	if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	  $anti_voodoo_success_list[] = $user->uname;
	}
	else{
	  $USERS->Kill($user->user_no, 'CURSED');
	  continue;
	}
      }

      if($user->IsRole('soul_mage')){ //魂の占い師の処理
	$result = $target->main_role; //占い結果はメイン役職
      }
      else{ //占い師の処理
	if($target->IsFox() &&
	   ! $target->IsRole('white_fox', 'black_fox', 'child_fox')){ //呪殺判定
	  $USERS->Kill($target->user_no, 'FOX_DEAD');
	}
	$result = $target->DistinguishMage(); //判定結果を取得
      }

      if(is_array($possessed_target_list[$target->user_no])){ //憑依予定者ならキャンセル
	if($possessed_target_list[$target->user_no]['status'] != 'reset'){
	  $possessed_target_list[$target->user_no]['status'] = 'cancel';
	}
      }
    }
    $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
    InsertSystemMessage($sentence . "\t" . $result, 'MAGE_RESULT');
  }

  foreach($vote_data->child_fox as $array){ //子狐の処理
    $user = $USERS->ByUname($array['uname']);
    if($user->dead_flag) continue; //直前に死んでいたら無効

    $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    //呪返し判定
    if(($target->IsRoleGroup('cursed') && ! $target->dead_flag) ||
       in_array($target->uname, $voodoo_target_list)){
      if(in_array($user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $user->uname;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    //占い結果を作成 (月兎に妨害されるか、一定確率で失敗する)
    $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
    $failed_flag = (in_array($user->uname, $jammer_target_list) || mt_rand(1, 100) <= 30);
    $result = ($failed_flag ? 'failed' : $target->DistinguishMage());
    InsertSystemMessage($sentence. "\t" . $result, 'CHILD_FOX_RESULT');
  }

  foreach($voodoo_killer_target_list as $uname => $target_uname){ //陰陽師の解呪結果処理
    if(! in_array($target_uname, $voodoo_killer_success_list)) continue; //成功判定
    $sentence = $USERS->ByUname($uname)->handle_name  . "\t";
    $sentence .= $USERS->GetHandleName($target_uname, true);
    InsertSystemMessage($sentence, 'VOODOO_KILLER_SUCCESS');
  }

  foreach($anti_voodoo_target_list as $uname => $target_uname){ //厄神の払い結果処理
    if(! in_array($target_uname, $anti_voodoo_success_list)) continue; //成功判定
    $sentence = $USERS->ByUname($uname)->handle_name  . "\t";
    $sentence .= $USERS->GetHandleName($target_uname, true);
    InsertSystemMessage($sentence, 'ANTI_VOODOO_SUCCESS');
  }

  if($ROOM->date == 1){
    foreach($vote_data->mind_scanner as $array){ //さとりの処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      //対象者にサトラレを追加
      $add_role = 'mind_read[' . strval($user->user_no) . ']';
      $USERS->ByUname($array['target_uname'])->AddRole($add_role);
    }

    foreach($vote_data->mania as $array){ //神話マニア系の処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

      if($user->IsRole('unknown_mania')){ //鵺
	$user->ReplaceRole('unknown_mania', 'unknown_mania[' . $target->user_no . ']');
	$user->AddRole('mind_friend[' . $user->user_no . ']');
	$target->AddRole('mind_friend[' . $user->user_no . ']');
      }
      else{ //神話マニア
	//コピー処理 (神話マニア系を指定した場合は村人にする)
	$result = ($target->IsRoleGroup('mania') ? 'human' : $target->main_role);
	$user->ReplaceRole('mania', $result);
	$user->AddRole('copied');

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	InsertSystemMessage($sentence, 'MANIA_RESULT');
      }
    }
  }
  else{
    foreach($vote_data->reporter as $array){ //ブン屋の処理
      $user = $USERS->ByUname($array['uname']);
      if($user->dead_flag) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
      if(in_array($target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$USERS->Kill($user->user_no, 'TRAPPED');
	continue;
      }

      if($target == $wolf_target){ //尾行成功
	if($target->uname == $guarded_uname) continue; //護衛されていた場合は何も出ない
	$sentence = $user->handle_name . "\t";
	$sentence .= $USERS->GetHandleName($wolf_target->uname, true) . "\t";
	$sentence .= $USERS->GetHandleName($voted_wolf->uname, true);
	InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
	continue;
      }

      if($target->dead_flag) continue; //尾行対象が直前に死んでいたら何も起きない

      if($target->IsRoleGroup('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
	$USERS->Kill($user->user_no, 'REPORTER_DUTY');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($vote_data->poison_cat as $array){ //猫又の処理
	$user = $USERS->ByUname($array['uname']);
	if($user->dead_flag) continue; //直前に死んでいたら無効

	$target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

	//蘇生判定
	$rate = mt_rand(1, 100); //蘇生判定用乱数
	//$rate = 5; //mt_rand(1, 10); //テスト用
	//PrintData($rate, 'Revive Rate: ' . $user->uname . ' => ' . $target->uname);
	$result = 'failed';
	do{
	  if($rate > 25) break; //蘇生失敗
	  if($rate <= 5){ //誤爆蘇生
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
	  //$target = $USERS->ByID(8); //テスト用
	  //PrintData($target->uname, 'Revive User');
	  if($target->IsRole('poison_cat') || $target->IsLovers()) break; //猫又か恋人なら蘇生失敗

	  $result = 'success';
	  if($target->IsRole('possessed_wolf')){ //憑狼対応
	    if($target->revive_flag) break; //蘇生済みならスキップ

	    $virtual_target = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //確定死者
	      if($target != $virtual_target){ //憑依後に死亡していた場合はリセット処理を行う
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      }
	    }
	    elseif($target->IsLive(true)){ //生存者 (憑依状態確定)
	      //見かけ上の蘇生処理
	      $target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      InsertSystemMessage($target->handle_name, 'REVIVE_SUCCESS');

	      //本当の死者の蘇生処理
	      $virtual_target->Revive(true);
	      $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);

	      //憑依予定者が居たらキャンセル
  	      if($target->IsSame($voted_wolf->uname) &&
		 is_array($possessed_target_list[$target->user_no])){
		$possessed_target_list[$target->user_no]['status'] = 'cancel';
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
  	      if($target->IsSame($voted_wolf->uname) &&
		 is_array($possessed_target_list[$target->user_no])){
		$possessed_target_list[$target->user_no]['status'] = 'cancel';
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

	if($result == 'failed') InsertSystemMessage($target->handle_name, 'REVIVE_FAILED');
	$sentence = $user->handle_name . "\t";
	$sentence .= $USERS->GetHandleName($target->uname) . "\t" . $result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  //憑依処理
  //PrintData($possessed_target_list, 'Possessed Target');
  foreach($possessed_target_list as $user_no => $array){
    $user   = $USERS->ByID($user_no); //憑依者のユーザ情報を取得
    $target = $USERS->ByID($array['target']); //憑依先のユーザ情報を取得

    if($array['status'] == 'cancel'){ //憑依失敗
      $target->dead_flag = false; //死亡フラグをリセット
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
      continue;
    }

    $possessed_date = $ROOM->date + 1; //憑依する日を取得
    $virtual_user = $USERS->ByVirtual($user->user_no); //現在の憑依先を取得

    if(! $user->IsLive(true)){ //憑依者死亡
      $target->dead_flag = false; //死亡フラグをリセット
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
    }
    elseif($array['status'] == 'reset'){ //憑依リセット
      if(isset($target->user_no)){
	$target->dead_flag = false; //死亡フラグをリセット
	$USERS->Kill($target->user_no, 'WOLF_KILLED');
	if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
      }

      if($user != $virtual_user){ //憑依中なら元の体に戻される
	//憑依先のリセット処理
	$virtual_user->ReturnPossessed('possessed', $possessed_date);
	$virtual_user->SaveLastWords();
	InsertSystemMessage($virtual_user->handle_name, 'POSSESSED_RESET');

	//見かけ上の蘇生処理
	$user->ReturnPossessed('possessed_target', $possessed_date);
	$user->SaveLastWords($virtual_user->handle_name);
	InsertSystemMessage($user->handle_name, 'REVIVE_SUCCESS');
      }
      continue;
    }
    else{ //憑依成功
      $target->dead_flag = false; //死亡フラグをリセット
      $USERS->Kill($target->user_no, 'POSSESSED_TARGETED'); //憑依先の死亡処理
      $target->AddRole("possessed[{$possessed_date}-{$user->user_no}]");

      //憑依処理
      $user->AddRole("possessed_target[{$possessed_date}-{$target->user_no}]");
      InsertSystemMessage($virtual_user->handle_name, 'POSSESSED');
      $user->SaveLastWords($virtual_user->handle_name);
      $user->Update('last_words', '');
    }

    if($user != $virtual_user){
      $virtual_user->ReturnPossessed('possessed', $possessed_date);
      if($user->IsLive(true)) $virtual_user->SaveLastWords();
    }
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ

  if($ROOM->date > 2 && ($ROOM->date % 2) == 1 && $USERS->IsAppear('priest')){ //司祭の判定
    $live_count = 0;
    foreach($USERS->rows as $user){
      if($user->IsLive(true) && $user->DistinguishCamp() == 'human') $live_count++;
    }
    InsertSystemMessage($live_count, 'PRIEST_RESULT');
  }

  if($USERS->IsAppear('crisis_priest')){ //預言者の判定
    $live_count = array();
    foreach($USERS->rows as $this_user){
      if(! $this_user->IsLive(true)) continue;

      $live_count['total']++;
      if($this_user->IsWolf())
	$live_count['wolf']++;
      elseif($this_user->IsFox())
	$live_count['fox']++;
      else
	$live_count['human']++;

      if($this_user->IsLovers()) $live_count['lovers']++;
    }
    //PrintData($live_count, 'Live Count');

    $crisis_priest_result = '';
    if($live_count['wolf'] >= $live_count['human'] - 1 || $live_count['wolf'] == 1){
      if($live_count['lovers'] > 1)
	$crisis_priest_result = 'lovers';
      elseif($live_count['fox'] > 0)
	$crisis_priest_result = 'fox';
      elseif($live_count['wolf'] >= $live_count['human'] - 1)
	$crisis_priest_result = 'wolf';
    }
    if($crisis_priest_result != ''){
      InsertSystemMessage($crisis_priest_result, 'CRISIS_PRIEST_RESULT');
    }
  }

  if($ROOM->test_mode) return;

  //次の日にする
  $next_date = $ROOM->date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = {$ROOM->id}");

  //次の日の処刑投票のカウントを 1 に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //夜が明けた通知
  InsertSystemTalk("MORNING\t" . $next_date, ++$ROOM->system_time, 'day system', $next_date);
  UpdateTime(); //最終書き込みを更新
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

//投票コマンドがあっているかチェック
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return true;
  }
  if($RQ_ARGS->situation == $applay_situation) return true;

  OutputVoteResult('無効な投票です');
}

//ランダムメッセージを挿入する
function InsertRandomMessage(){
  global $MESSAGE, $GAME_CONF, $ROOM;

  if(! $GAME_CONF->random_message) return;
  $sentence = GetRandom($MESSAGE->random_message_list);
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system');
}
?>
