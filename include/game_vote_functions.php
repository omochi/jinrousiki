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
  global $GAME_CONF, $ROOM;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $GAME_CONF->role_list[$user_count]; //人数に応じた設定リストを取得
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
    foreach($GAME_CONF->chaos_fix_role_list as $key => $value){ //最小補正用リスト
      $fix_role_group_list[DistinguishRoleGroup($key)] = $value;
    }

    //人狼
    $add_count = round($user_count / $GAME_CONF->min_wolf_rate) - $fix_role_group_list['wolf'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if($rand < 3)      $random_role_list['boss_wolf']++;
      elseif($rand <  4) $random_role_list['cursed_wolf']++;
      elseif($rand < 14) $random_role_list['cute_wolf']++;
      elseif($rand < 17) $random_role_list['poison_wolf']++;
      elseif($rand < 21) $random_role_list['resist_wolf']++;
      elseif($rand < 24) $random_role_list['tongue_wolf']++;
      elseif($rand < 26) $random_role_list['silver_wolf']++;
      else               $random_role_list['wolf']++;
    }

    //妖狐
    $add_count = floor($user_count / $GAME_CONF->min_fox_rate) - $fix_role_group_list['fox'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if($rand < 1)       $random_role_list['cursed_fox']++;
      elseif($rand <  3)  $random_role_list['voodoo_fox']++;
      elseif($rand <  6)  $random_role_list['poison_fox']++;
      elseif($rand <  9)  $random_role_list['white_fox']++;
      elseif($rand < 10)  $random_role_list['silver_fox']++;
      elseif($rand < 15)  $random_role_list['child_fox']++;
      else                $random_role_list['fox']++;
    }

    //-- ランダム配役 --//
    $add_count = $user_count - (array_sum($random_role_list) +
				array_sum($GAME_CONF->chaos_fix_role_list));
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 1000);
      if(    $rand <  20) $random_role_list['mage']++;
      elseif($rand <  25) $random_role_list['soul_mage']++;
      elseif($rand <  35) $random_role_list['psycho_mage']++;
      elseif($rand <  50) $random_role_list['sex_mage']++;
      elseif($rand <  58) $random_role_list['voodoo_killer']++;
      elseif($rand <  70) $random_role_list['dummy_mage']++;
      elseif($rand < 110) $random_role_list['necromancer']++;
      elseif($rand < 115) $random_role_list['soul_necromancer']++;
      elseif($rand < 125) $random_role_list['yama_necromancer']++;
      elseif($rand < 150) $random_role_list['dummy_necromancer']++;
      elseif($rand < 180) $random_role_list['medium']++;
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
      elseif($rand < 440) $random_role_list['assassin']++;
      elseif($rand < 460) $random_role_list['mind_scanner']++;
      elseif($rand < 475) $random_role_list['jealousy']++;
      elseif($rand < 505) $random_role_list['pharmacist']++;
      elseif($rand < 520) $random_role_list['suspect']++;
      elseif($rand < 540) $random_role_list['unconscious']++;
      elseif($rand < 610) $random_role_list['wolf']++;
      elseif($rand < 620) $random_role_list['boss_wolf']++;
      elseif($rand < 650) $random_role_list['tongue_wolf']++;
      elseif($rand < 665) $random_role_list['poison_wolf']++;
      elseif($rand < 680) $random_role_list['resist_wolf']++;
      elseif($rand < 685) $random_role_list['cursed_wolf']++;
      elseif($rand < 715) $random_role_list['cute_wolf']++;
      elseif($rand < 730) $random_role_list['silver_wolf']++;
      elseif($rand < 750) $random_role_list['mad']++;
      elseif($rand < 760) $random_role_list['fanatic_mad']++;
      elseif($rand < 765) $random_role_list['whisper_mad']++;
      elseif($rand < 775) $random_role_list['jammer_mad']++;
      elseif($rand < 785) $random_role_list['voodoo_mad']++;
      elseif($rand < 800) $random_role_list['corpse_courier_mad']++;
      elseif($rand < 810) $random_role_list['dream_eater_mad']++;
      elseif($rand < 820) $random_role_list['trap_mad']++;
      elseif($rand < 850) $random_role_list['fox']++;
      elseif($rand < 860) $random_role_list['white_fox']++;
      elseif($rand < 867) $random_role_list['poison_fox']++;
      elseif($rand < 872) $random_role_list['voodoo_fox']++;
      elseif($rand < 875) $random_role_list['cursed_fox']++;
      elseif($rand < 880) $random_role_list['silver_fox']++;
      elseif($rand < 890) $random_role_list['child_fox']++;
      elseif($rand < 915) $random_role_list['cupid']++;
      elseif($rand < 925) $random_role_list['self_cupid']++;
      elseif($rand < 930) $random_role_list['mind_cupid']++;
      elseif($rand < 950) $random_role_list['chiroptera']++;
      elseif($rand < 955) $random_role_list['poison_chiroptera']++;
      elseif($rand < 960) $random_role_list['cursed_chiroptera']++;
      elseif($rand < 980) $random_role_list['mania']++;
      elseif($rand < 990) $random_role_list['unknown_mania']++;
      elseif($rand < 993) $random_role_list['quiz']++;
      else                $random_role_list['human']++;
    }

    //ランダムと固定を合計
    $role_list = $random_role_list;
    foreach($GAME_CONF->chaos_fix_role_list as $key => $value){
      $role_list[$key] += (int)$value;
    }
    // echo "<br> 1st_list: "; print_r($role_list); echo "<br>"; // テスト用

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
    //補正対象グループ
    $role_group_rate_list = array('wolf' => 0.25, 'mad' => 0.15, 'fox' => 0.12,
				  'mage' => 0.2, 'necromancer' => 0.15, 'guard' => 0.15,
				  'common' => 0.2, 'poison' => 0.15, 'assassin' => 0.15,
				  'mind_scanner' => 0.15, 'jealousy' => 0.1,
				  'cupid' => 0.1, 'chiroptera' => 0.15);

    foreach($role_group_rate_list as $name => $rate){
      if(! (is_array($role_group_list->$name) && is_array($random_role_group_list->$name))){
	continue;
      }
      $over_count = array_sum($role_group_list->$name) - round($user_count * $rate);
      // if($over_count > 0) echo "　$name : $over_count <br>";
      for(; $over_count > 0; $over_count--){
	if(array_sum($random_role_group_list->$name) < 1) break;
	// echo "　　$over_count: before: "; print_r($random_role_group_list->$name); echo "<br>";
	arsort($random_role_group_list->$name);
	// echo "　　$over_count: after : "; print_r($random_role_group_list->$name); echo "<br>";
	$this_key = key($random_role_group_list->$name);
	// echo "　　target: $this_key <br>";
	$random_role_group_list->{$name}[$this_key]--;
	$role_list[$this_key]--;
	$role_list['human']++;
	// echo "　　$over_count: delete: "; print_r($random_role_group_list->$name); echo "<br>";

	//0 になった役職はリストから除く
	if($role_list[$this_key] < 1) unset($role_list[$this_key]);
	if($random_role_group_list->$name[$this_key] < 1){
	  unset($random_role_group_list->$name[$this_key]);
	}
      }
    }
    // echo "2nd_list: "; print_r($role_list); echo "<br>"; //テスト用

    //神話マニア村以外なら一定数以上の村人を神話マニアに入れ替える
    if(strpos($option_role, 'full_mania') === false){
      $max_human_rate = 0.1;
      $over_count = $role_list['human'] - round($user_count * $max_human_rate);
      if($over_count > 0){
	$role_list['mania'] += $over_count;
	$role_list['human'] -= $over_count;
	// echo "3rd_list: "; print_r($role_list); echo "<br>"; //テスト用
      }
    }
    /*
    $reviver_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'poison_cat') !== false) $revivier_count_list[$key] = $value;
    }
    $over_reviver_count = array_sum($reviver_count_list) - round($user_count * 0.1);
    for(; $over_reviver_count > 0; $over_reviver_count--){
      arsort($reviver_count_list);
      $this_key = key($reviver_count_list);
      $reviver_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['poison']++;
    }
    */
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
      if($rand <= 80) $wolf_count = 1;
      else $wolf_count = 2;
    }
    elseif($user_count < 16){ //1:2:3 = 15:70:15
      if($rand <= 15) $wolf_count = 1;
      elseif($rand <= 85) $wolf_count = 2;
      else $wolf_count = 3;
    }
    elseif($user_count < 21){ //1:2:3:4:5 = 5:10:70:10:5
      if($rand <= 5) $wolf_count = 1;
      elseif($rand <= 15) $wolf_count = 2;
      elseif($rand <= 85) $wolf_count = 3;
      elseif($rand <= 95) $wolf_count = 4;
      else $wolf_count = 5;
    }
    else{ //以後、5人増えるごとに 1人ずつ増加
      $base_count = floor(($user_count - 20) / 5) + 3;
      if($rand <= 5) $wolf_count = $base_count - 2;
      elseif($rand <= 15) $wolf_count = $base_count - 1;
      elseif($rand <= 85) $wolf_count = $base_count;
      elseif($rand <= 95) $wolf_count = $base_count + 1;
      else $wolf_count = $base_count + 2;
    }

    //妖狐陣営
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){
      $fox_count = 0;
    }
    elseif($user_count < 15){ //0:1 = 90:10
      if($rand <= 90) $fox_count = 0;
      else $fox_count = 1;
    }
    elseif($user_count < 23){ //1:2 = 90:10
      if($rand <= 90) $fox_count = 1;
      else $fox_count = 2;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = ceil($user_count / 20);
      if($rand <= 10) $fox_count = $base_count - 1;
      elseif($rand <= 90) $fox_count = $base_count;
      else $fox_count = $base_count + 1;
    }

    //恋人陣営 (実質キューピッド)
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){
      $lovers_count = 0;
    }
    elseif($user_count < 10){ //0:1 = 95:5
      if($rand <= 95) $lovers_count = 0;
      else $lovers_count = 1;
    }
    elseif($user_count < 16){ //0:1 = 70:30
      if($rand <= 70) $lovers_count = 0;
      else $lovers_count = 1;
    }
    elseif($user_count < 23){ //0:1:2 = 5:90:5
      if($rand <= 5) $lovers_count = 0;
      elseif($rand <= 95) $lovers_count = 1;
      else $lovers_count = 2;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      //基礎-1:基礎:基礎+1 = 5:90:5
      $base_count = floor($user_count / 20);
      if($rand <= 5) $lovers_count = $base_count - 1;
      elseif($rand <= 95) $lovers_count = $base_count;
      else $lovers_count = $base_count + 1;
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
      if($rand <= 10) $mage_count = 0;
      else $mage_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $mage_count = 1;
      else $mage_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $mage_count = 1;
      else $mage_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mage_count = $base_count - 1;
      elseif($rand <= 90) $mage_count = $base_count;
      else $mage_count = $base_count + 1;
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
      if($rand <= 70) $medium_count = 0;
      else $medium_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $medium_count = 0;
      elseif($rand <= 90) $medium_count = 1;
      else $medium_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $medium_count = $base_count - 1;
      elseif($rand <= 90) $medium_count = $base_count;
      else $medium_count = $base_count + 1;
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
      if($rand <= 10) $necromancer_count = 0;
      else $necromancer_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $necromancer_count = $base_count - 1;
      elseif($rand <= 90) $necromancer_count = $base_count;
      else $necromancer_count = $base_count + 1;
    }

    //霊能系の配役を決定
    if($necromancer_count > 0 && $human_count >= $necromancer_count){
      $human_count -= $necromancer_count; //村人陣営の残り人数
      $role_list['necromancer'] = $necromancer_count;
    }

    //狂人系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 10){ //0:1 = 30:70
      if($rand <= 30) $mad_count = 0;
      else $mad_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $mad_count = 0;
      elseif($rand <= 90) $mad_count = 1;
      else $mad_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mad_count = $base_count - 1;
      elseif($rand <= 90) $mad_count = $base_count;
      else $mad_count = $base_count + 1;
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
      if($rand <= 10) $guard_count = 0;
      else $guard_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $guard_count = 0;
      elseif($rand <= 90) $guard_count = 1;
      else $guard_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $guard_count = $base_count - 1;
      elseif($rand <= 90) $guard_count = $base_count;
      else $guard_count = $base_count + 1;
    }

    //狩人系の配役を決定
    if($guard_count > 0 && $human_count >= $guard_count){
      $human_count -= $guard_count; //村人陣営の残り人数
      $special_guard_count = 0; //特殊狩人の人数
      if($user_count < 16) $base_count = 0; //16人未満では出現しない
      else $base_count = ceil($user_count / 15); //特殊狩人判定回数を算出
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
      if($rand <= 10) $common_count = 0;
      else $common_count = 1;
    }
    elseif($user_count < 22){ //1:2:3 = 10:80:10
      if($rand <= 10) $common_count = 1;
      elseif($rand <= 90) $common_count = 2;
      else $common_count = 3;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15) + 1;
      if($rand <= 10) $common_count = $base_count - 1;
      elseif($rand <= 90) $common_count = $base_count;
      else $common_count = $base_count + 1;
    }

    //共有者の配役を決定
    if($common_count > 0 && $human_count >= $common_count){
      $role_list['common'] = $common_count;
      $human_count -= $common_count; //村人陣営の残り人数
    }

    //埋毒者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 95:5
      if($rand <= 95) $poison_count = 0;
      else $poison_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $poison_count = 0;
      else $poison_count = 1;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 20);
      if($rand <= 10) $poison_count = $base_count - 1;
      elseif($rand <= 90) $poison_count = $base_count;
      else $poison_count = $base_count + 1;
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
      if($rand <= 95) $pharmacist_count = 0;
      else $pharmacist_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $pharmacist_count = 0;
      else $pharmacist_count = 1;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 20);
      if($rand <= 10) $pharmacist_count = $base_count - 1;
      elseif($rand <= 90) $pharmacist_count = $base_count;
      else $pharmacist_count = $base_count + 1;
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
      if($rand <= 40) $mania_count = 0;
      else $mania_count = 1;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 20);
      if($rand <= 10) $mania_count = $base_count - 1;
      elseif($rand <= 90) $mania_count = $base_count;
      else $mania_count = $base_count + 1;
    }

    //神話マニアの配役を決定
    if($mania_count > 0 && $human_count >= $mania_count){
      $role_list['mania'] = $mania_count;
      $human_count -= $mania_count; //村人陣営の残り人数
    }

    //不審者系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 90:10
      if($rand <= 90) $strangers_count = 0;
      else $strangers_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 80:20
      if($rand <= 80) $strangers_count = 0;
      else $strangers_count = 1;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 20);
      if($rand <= 10) $strangers_count = $base_count - 1;
      elseif($rand <= 90) $strangers_count = $base_count;
      else $strangers_count = $base_count + 1;
    }

    //不審者系の配役を決定
    if($strangers_count > 0 && $human_count >= $strangers_count){
      if($user_count < 20){ //全人口が20人未満の場合は無意識を出やすくする
	for($i = 0; $i < $strangers_count; $i++){
	  $rand = mt_rand(1, 100);
	  if($rand <= 60) $role_list['unconscious']++;
	  else $role_list['suspect']++;
	}
      }
      else{ //20人以上ならやや不審者を出やすくする
	for($i = 0; $i < $strangers_count; $i++){
	  $rand = mt_rand(1, 100);
	  if($rand <= 40) $role_list['unconscious']++;
	  else $role_list['suspect']++;
	}
      }
      $human_count -= $strangers_count; //村人陣営の残り人数
    }

    $role_list['human'] = $human_count; //村人の人数
  }
  else{ //通常村
    //埋毒者 (村人2 → 埋毒者1、人狼1)
    if(strpos($option_role, 'poison') !== false && $user_count >= $GAME_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //キューピッド (14人はハードコード / 村人 → キューピッド)
    if(strpos($option_role, 'cupid') !== false &&
       ($user_count == 14 || $user_count >= $GAME_CONF->cupid)){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //白狼 (人狼 → 白狼)
    if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $GAME_CONF->boss_wolf){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //毒狼 (人狼 → 毒狼、村人 → 薬師)
    if(strpos($option_role, 'poison_wolf') !== false && $user_count >= $GAME_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //神話マニア (村人 → 神話マニア)
    if(strpos($option_role, 'mania') !== false && $user_count >= $GAME_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //巫女 (村人 → 巫女1、狂信者1)
    if(strpos($option_role, 'medium') !== false && $user_count >= $GAME_CONF->medium){
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
    // echo 'エラー：配役数：' . $role_count;
    // return $now_role_list;
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

  //生きているユーザ数を取得
  $user_list = $USERS->GetLivingUsers();

  if(! $ROOM->test_mode){
    //投票総数を取得
    $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";
    if(FetchResult($query) != count($user_list)) return false; //全員が投票していなければ処理スキップ
  }

  $max_voted_number = 0;  //最多得票数
  $vote_kill_uname  = ''; //処刑される人のユーザ名
  $live_uname_list   = array(); //生きている人のユーザ名リスト
  $vote_message_list = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list      = array(); //能力者たちの投票結果
  if(! $ROOM->test_mode){
    $query = "FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND"; //共通クエリ
  }

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  foreach($user_list as $this_uname){
    $user = $USERS->ByUname($this_uname);

    //自分の得票数を取得
    $voted_number = ($ROOM->test_mode ? (int)$RQ_ARGS->TestItems->vote_day_count_list[$user->uname] :
		     FetchResult("SELECT SUM(vote_number) $query target_uname = '{$user->uname}'"));

    //特殊サブ役職の得票補正
    if(    $user->IsRole('upper_luck'))  $voted_number += ($ROOM->date == 2 ?  4 : -2); //雑草魂
    elseif($user->IsRole('downer_luck')) $voted_number += ($ROOM->date == 2 ? -4 :  2); //一発屋
    elseif($user->IsRole('random_luck')) $voted_number += (mt_rand(1, 5) - 3); //波乱万丈
    elseif($user->IsRole('star'))        $voted_number--; //人気者
    elseif($user->IsRole('disfavor'))    $voted_number++; //不人気

    if($voted_number < 0) $voted_number = 0; //マイナスになっていたら 0 にする

    //自分の投票先の情報を取得
    $array = ($ROOM->test_mode ? $RQ_ARGS->TestItems->vote_day[$user->uname] :
	      FetchNameArray("SELECT target_uname, vote_number $query uname = '{$user->uname}'"));
    $target = $USERS->ByUname($array['target_uname']);
    $vote_number = (int)$array['vote_number'];

    //システムメッセージ用の配列を生成
    $message_list = array('target'       => $target->handle_name,
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
  if($ability_list['rebel'] != '' && $ability_list['rebel'] == $ability_list['authority']){
    //権力者と反逆者の投票数を 0 にする
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //投票先の票数補正
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
    $vote_target = $USERS->ByUname($vote_kill_uname); //ユーザ情報を取得
    $vote_target->Kill('VOTE_KILLED'); //処刑処理
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
      $sentence = $user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'PHARMACIST_RESULT');
    }

    //処刑された人が毒を持っていた場合
    do{
      if($pharmacist_success) break; //解毒されていればスキップ
      if(! $vote_target->IsRoleGroup('poison')) break; //毒を持っていなければ発動しない
      if($vote_target->IsRole('dummy_poison', 'poison_guard')) break; //夢毒者・騎士は対象外
      if($vote_target->IsRole('incubate_poison') && $ROOM->date < 5) break; //潜毒者は 5 日目以降

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list);
      $limited_poison_target_list = array(); //特殊毒の場合はターゲットが限定される

      if($vote_target->IsRole('strong_poison', 'incubate_poison')){ //強毒者・潜毒者
	foreach($poison_target_list as $this_uname){
	  if($USERS->ByUname($this_uname)->IsRoleGroup('wolf', 'fox')){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_wolf')){ //毒狼
	foreach($poison_target_list as $this_uname){
	  if(! $USERS->ByUname($this_uname)->IsWolf()){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_fox')){ //管狐
	foreach($poison_target_list as $this_uname){
	  if(! $USERS->ByUname($this_uname)->IsFox()){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      elseif($vote_target->IsRole('poison_chiroptera')){ //毒蝙蝠
	foreach($poison_target_list as $this_uname){
	  if($USERS->ByUname($this_uname)->IsRoleGroup('wolf', 'fox', 'chiroptera')){
	    $limited_poison_target_list[] = $this_uname;
	  }
	}
	$poison_target_list = $limited_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      $poison_target = $USERS->ByUname(GetRandom($poison_target_list)); //対象者を決定

      if($poison_target->IsActiveRole('resist_wolf')){ //抗毒狼には無効
	$poison_target->AddRole('lost_ability');
	break;
      }

      $poison_target->Kill('POISON_DEAD_day'); //死亡処理
    }while(false);

    //霊能系の判定結果
    $sentence_header = $vote_target->handle_name . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能者の判定結果
    if($vote_target->IsRole('boss_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('cursed_fox', 'white_fox')){
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
      $flag_stolen |= $USERS->ByUname($this_uname)->IsRole('corpse_courier_mad');
    }

    if($USERS->is_appear('necromancer')){ //霊能者がいればシステムメッセージを登録
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      InsertSystemMessage($sentence, $action);
    }

    if($USERS->is_appear('soul_necromancer')){ //雲外鏡の判定結果
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      InsertSystemMessage($sentence, 'SOUL_' . $action);
    }

    if($USERS->is_appear('dummy_necromancer')){ //夢枕人の判定結果は村人と人狼が反転する
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  foreach($user_list as $this_uname){ //橋姫の処理
    $user = $USERS->ByUname($this_uname);
    if($vote_kill_uname == $user->uname || ! $user->IsRole('jealousy')) continue;

    $cupid_list = array(); //キューピッドのID => 恋人のID
    $jealousy_voted_list = array_keys($vote_target_list, $user->uname); //橋姫への投票者リスト
    foreach($jealousy_voted_list as $this_voted_uname){
      $voted_user = $USERS->ByUname($this_voted_uname);
      if($voted_user->dead_flag || ! $voted_user->IsLovers()) continue;
      foreach($voted_user->partner_list['lovers'] as $id){
	$cupid_list[$id][] = $voted_user->user_no;
      }
    }

    //同一キューピッドの恋人が複数いたらショック死
    foreach($cupid_list as $cupid_id => $lovers_list){
      if(count($lovers_list) < 2) continue;
      foreach($lovers_list as $id) $USERS->ById($id)->SuddenDeath('SUDDEN_DEATH_JEALOUSY');
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

    if($reason != '') $user->SuddenDeath('SUDDEN_DEATH_' . $reason);
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
    "AND live = 'live' AND user_no > 0 AND role LIKE '{$role}%'";
  if($action == 'TRAP_MAD_DO') $query_role .= " AND !(role LIKE '%lost_ability%')";
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
				   'mind_scanner', 'cupid', 'mania');

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

  $guarded_uname = ''; //護衛された人のユーザ名 //複数噛みに対応するならここは配列に変える
  $trap_target_list         = array(); //罠の設置先リスト
  $trapped_list             = array(); //罠にかかった人リスト
  $anti_voodoo_target_list  = array(); //厄神の護衛対象リスト
  $anti_voodoo_success_list = array(); //厄払い成功者リスト
  $dummy_guard_target_list  = array(); //夢守人の護衛対象リスト

  if($ROOM->date != 1){
    foreach($vote_data->trap_mad as $array){ //罠師の設置先リストを作成
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);

      $this_user->AddRole('lost_ability'); //一度設置したら能力失効

      //人狼に狙われていたら自分自身への設置以外は無効
      if($this_user != $wolf_target || $this_user == $this_target){
	$trap_target_list[$this_user->uname] = $this_target->uname;
      }
    }

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $trap_count_list = array_count_values($trap_target_list);
    foreach($trap_target_list as $this_uname => $this_target_uname){
      if($this_uname != $this_target_uname && $trap_count_list[$this_target_uname] > 1){
	$trapped_list[] = $this_uname;
      }
    }

    //狩人系の狩り対象リスト
    $hunt_target_list = array('jammer_mad', 'voodoo_mad', 'corpse_courier_mad',
			      'dream_eater_mad', 'trap_mad',
			      'cursed_fox', 'voodoo_fox',
			      'poison_chiroptera', 'cursed_chiroptera');
    foreach($vote_data->guard as $array){ //狩人系の処理
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);
      $sentence    = $this_user->handle_name . "\t";

      if($this_user->IsRole('dummy_guard')){ //夢守人
	if($this_target->IsRole('dream_eater_mad')){ //獏の狩り判定
	  $this_target->Kill('HUNTED');
	  $action = 'GUARD_HUNTED';
	}
	else{ //獏を狩れなかった場合は護衛成功メッセージだけが出る
	  $action = 'GUARD_SUCCESS';
	  $dummy_guard_target_list[$this_user->uname] = $this_target->uname;
	}
	InsertSystemMessage($sentence . $this_target->handle_name, $action);
	continue;
      }

      if($this_target->IsRole($hunt_target_list)){ //狩り判定
	$this_target->Kill('HUNTED');
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_HUNTED');
      }

      if(in_array($this_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$trapped_list[] = $this_user->uname;
	continue;
      }

      if($this_target != $wolf_target) continue; //護衛成功判定
      InsertSystemMessage($sentence . $wolf_target->handle_name, 'GUARD_SUCCESS');

      //騎士でない場合、一部の役職は護衛されていても人狼に襲撃される
      if($this_user->IsRole('poison_guard') || ! $wolf_target->IsRole('reporter', 'assassin')){
	$guarded_uname = $this_target->uname;
      }
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

    $wolf_target->Kill('WOLF_KILLED'); //襲撃処理

    if($voted_wolf->IsActiveRole('tongue_wolf')){ //舌禍狼の処理
      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      InsertSystemMessage($sentence . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');

      if($wolf_target->main_role == 'human') $voted_wolf->AddRole('lost_ability'); //村人なら能力失効
    }

    do{ //毒死判定処理
      if(! $wolf_target->IsRoleGroup('poison')) break; //毒を持っていなければ発動しない
      if($wolf_target->IsRole('dummy_poison')) break; //夢毒者は対象外
      if($wolf_target->IsRole('incubate_poison') && $ROOM->date < 5) break; //潜毒者は 5 日目以降

      //生きている狼を取得
      $live_wolf_list = ($GAME_CONF->poison_only_eater ? array($voted_wolf->uname) :
			 $USERS->GetLivingWolves());

      $poison_target = $USERS->ByUname(GetRandom($live_wolf_list));

      if($poison_target->IsActiveRole('resist_wolf')){ //抗毒狼なら無効
	$poison_target->AddRole('lost_ability');
	break;
      }

      $poison_target->Kill('POISON_DEAD_night'); //毒死処理
    }while(false);
  }while(false);

  //その他の能力者の投票処理
  /*
    人狼、占い師、ブン屋など、行動結果で死者が出るタイプは判定順に注意

    ケース1) どちらの判定を先に行うかで妖狐の生死が決まる (基本的には人狼の襲撃を優先する)
    人狼   → 占い師
    占い師 → 妖狐

    ケース2) どちらの判定を先に行うかでブン屋の生死が決まる (現在は占い師が先)
    占い師 → 妖狐
    ブン屋 → 妖狐
  */

  if($ROOM->date != 1){
    $assassin_target_list = array(); //暗殺対象者リスト
    foreach($vote_data->assassin as $array){ //暗殺者の処理
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //直前に死んでいたら無効

      $this_target_uname = $array['target_uname'];
      if(in_array($this_target_uname, $trap_target_list)){ //罠が設置されていたら死亡
	$trapped_list[] = $this_user->uname;
	continue;
      }

      $assassin_target_list[$this_target_uname] = true; //暗殺対象者リストに追加
    }

    foreach($trapped_list as $this_uname){ //罠の死亡処理
      $USERS->ByUname($this_uname)->Kill('TRAPPED');
    }

    foreach($assassin_target_list as $this_uname => $value){ //暗殺処理
      $USERS->ByUname($this_uname)->Kill('ASSASSIN_KILLED');
    }

    foreach($vote_data->anti_voodoo as $array){ //厄神の処理
      $this_user = $USERS->ByUname($array['uname']);
      if(! $this_user->dead_flag){ //直前に死んでいたら無効
	$anti_voodoo_target_list[$this_user->uname] = $array['target_uname'];
      }
    }

    foreach($vote_data->dream_eater_mad as $array){ //獏の処理
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //直前に死んでいたら無効

      $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
      if($this_target->IsRole('dummy_guard')){ //夢守人が対象なら返り討ちに合う
	$this_user->Kill('HUNTED');
	$sentence = $this_target->handle_name . "\t" . $this_user->handle_name;
	InsertSystemMessage($sentence, 'GUARD_HUNTED');
      }
      elseif(in_array($this_target->uname, $dummy_guard_target_list)){ //夢守人の護衛判定
	$this_user->Kill('HUNTED');
	$hunted_dummy_guard_list = array_keys($dummy_guard_target_list, $this_target->uname);
	foreach($hunted_dummy_guard_list as $this_dummy_guard_uname){
	  $sentence = $USERS->GetHandleName($this_dummy_guard_uname);
	  InsertSystemMessage($sentence . "\t" . $this_user->handle_name, 'GUARD_HUNTED');
	}
      }
      elseif($this_target->IsRoleGroup('dummy')){
	$this_target->Kill('DREAM_KILLED');
      }
    }
  }

  $voodoo_killer_target_list = array(); //陰陽師の対象リスト
  $voodoo_killer_success_list = array(); //陰陽師の解呪成功者対象リスト
  foreach($vote_data->voodoo_killer as $array){ //陰陽師の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($this_target->IsRoleGroup('cursed')){ //呪い持ちなら呪殺
      $this_target->Kill('CURSED');
      $voodoo_killer_success_list[] = $this_target->uname;
    }
    $voodoo_killer_target_list[$this_user->uname] = $this_target->uname; //解呪リストに追加
  }

  $voodoo_target_list = array(); //呪い系能力者の対象リスト
  foreach($vote_data->voodoo_mad as $array){ //呪術師の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($this_target->IsRoleGroup('cursed') && ! $this_target->dead_flag){ //呪返し判定
      if(in_array($this_user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $this_user->uname;
      }
      else{
	$this_user->Kill('CURSED');
	continue;
      }
    }

    if(in_array($this_target->uname, $voodoo_killer_target_list)){ //陰陽師の解呪判定
      $voodoo_killer_success_list[] = $this_target->uname;
    }
    else{
      $voodoo_target_list[$this_user->uname] = $this_target->uname;
    }
  }

  foreach($vote_data->voodoo_fox as $array){ //九尾の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($this_target->IsRoleGroup('cursed') && ! $this_target->dead_flag){ //呪返し判定
      if(in_array($this_user->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
	$anti_voodoo_success_list[] = $this_user->uname;
      }
      else{
	$this_user->Kill('CURSED');
	continue;
      }
    }

    if(in_array($this_target->uname, $voodoo_killer_target_list)){ //陰陽師の解呪判定
      $voodoo_killer_success_list[] = $this_target->uname;
    }
    else{
      $voodoo_target_list[$this_user->uname] = $this_target->uname;
    }
  }

  //呪いをかけた先が他の能力者に呪いをかけられていた場合は呪返しを受ける
  $voodoo_count_list = array_count_values($voodoo_target_list);
  foreach($voodoo_target_list as $this_uname => $this_target_uname){
    if($voodoo_count_list[$this_target_uname] > 1){
      if(in_array($this_user->uname, $anti_voodoo_target_list)){
	$anti_voodoo_success_list[] = $this_user->uname;
      }
      else{
	$USERS->ByUname($this_uname)->Kill('CURSED');
      }
    }
  }

  $jammer_target_list = array(); //妨害対象リスト
  foreach($vote_data->jammer_mad as $array){ //月兎の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    //呪返し判定
    if(($this_target->IsRoleGroup('cursed')  && ! $this_target->dead_flag) ||
       in_array($this_target->uname, $voodoo_target_list)){
      if(in_array($this_user->uname, $anti_voodoo_target_list)){
	$anti_voodoo_success_list[] = $this_user->uname;
      }
      else{
	$this_user->Kill('CURSED');
	continue;
      }
    }

    if(in_array($this_target->uname, $anti_voodoo_target_list)){ //厄神の護衛判定
      $anti_voodoo_success_list[] = $this_target->uname;
    }
    else{ //妨害対象者リストに追加
      $jammer_target_list[$this_target->uname] = $this_target->uname;
    }
  }

  //精神鑑定士の嘘つき判定対象役職リスト
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  foreach($vote_data->mage as $array){ //占い師系の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

    if($this_user->IsRole('dummy_mage')){ //夢見人の占い結果は村人と人狼を反転させる
      $this_result = $this_target->DistinguishMage(true);
    }
    elseif(in_array($this_user->uname, $jammer_target_list)){ //月兎の妨害判定
      $this_result = ($this_user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed');
    }
    elseif($this_user->IsRole('psycho_mage')){ //精神鑑定士の判定
      $this_result = 'psycho_mage_normal';
      foreach($psycho_mage_liar_list as $this_liar_role){
	if($this_target->IsRoleGroup($this_liar_role)){
	  $this_result = 'psycho_mage_liar';
	  break;
	}
      }
    }
    elseif($this_user->IsRole('sex_mage')){ //ひよこ鑑定士の判定
      $this_result =  $this_target->IsRoleGroup('chiroptera') ? 'chiroptera' :
	'sex_' . $this_target->sex;
    }
    else{
      //呪返し判定
      if(($this_target->IsRoleGroup('cursed') && ! $this_target->dead_flag) ||
	 in_array($this_target->uname, $voodoo_target_list)){
	if(in_array($this_user->uname, $anti_voodoo_target_list)){
	  $anti_voodoo_success_list[] = $this_user->uname;
	}
	else{
	  $this_user->Kill('CURSED');
	  continue;
	}
      }

      if($this_user->IsRole('soul_mage')){ //魂の占い師の処理
	$this_result = $this_target->main_role; //占い結果はメイン役職
      }
      else{ //占い師の処理
	if($this_target->IsFox() && ! $this_target->IsRole('white_fox', 'child_fox')){ //呪殺判定
	  $this_target->Kill('FOX_DEAD');
	}
	$this_result = $this_target->DistinguishMage(); //判定結果を取得
      }
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  foreach($vote_data->child_fox as $array){ //子狐の処理
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    //呪返し判定
    if(($this_target->IsRoleGroup('cursed') && ! $this_target->dead_flag) ||
       in_array($this_target->uname, $voodoo_target_list)){
      if(in_array($this_user->uname, $anti_voodoo_target_list)){
	$anti_voodoo_success_list[] = $this_user->uname;
      }
      else{
	$this_user->Kill('CURSED');
	continue;
      }
    }

    //占い結果を作成
    //月兎に邪魔されるか、一定確率で失敗する
    $failed_flag = (in_array($this_user->uname, $jammer_target_list) || mt_rand(1, 100) <= 30);
    $this_result = ($failed_flag ? 'failed' : $this_target->DistinguishMage());
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  foreach($voodoo_killer_target_list as $this_uname => $this_target_uname){ //陰陽師の払い結果処理
    if(! in_array($this_target_uname, $voodoo_killer_success_list)) continue; //成功判定
    $this_handle   = $USERS->ByUname($this_uname)->handle_name;
    $target_handle = $USERS->ByUname($this_target_uname)->handle_name;
    InsertSystemMessage($this_handle . "\t" . $target_handle, 'VOODOO_KILLER_SUCCESS');
  }

  foreach($anti_voodoo_target_list as $this_uname => $this_target_uname){ //厄神の払い結果処理
    if(! in_array($this_target_uname, $anti_voodoo_success_list)) continue; //成功判定
    $this_handle   = $USERS->ByUname($this_uname)->handle_name;
    $target_handle = $USERS->ByUname($this_target_uname)->handle_name;
    InsertSystemMessage($this_handle . "\t" . $target_handle, 'ANTI_VOODOO_SUCCESS');
  }

  if($ROOM->date == 1){
    foreach($vote_data->mind_scanner as $array){ //さとりの処理
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //直前に死んでいたら無効

      //対象者にサトラレを追加
      $add_role = 'mind_read[' . strval($this_user->user_no) . ']';
      $USERS->ByUname($array['target_uname'])->AddRole($add_role);
    }

    foreach($vote_data->mania as $array){ //神話マニア系の処理
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //直前に死んでいたら無効

      $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

      if($this_user->IsRole('unknown_mania')){ //鵺
	$this_user->ReplaceRole('unknown_mania', 'unknown_mania[' . $this_target->user_no . ']');
	$this_user->AddRole('mind_friend[' . $this_user->user_no . ']');
	$this_target->AddRole('mind_friend[' . $this_user->user_no . ']');
      }
      else{ //神話マニア
	//コピー処理 (神話マニア系を指定した場合は村人にする)
	$this_result = ($this_target->IsRoleGroup('mania') ? 'human' : $this_target->main_role);
	$this_user->ReplaceRole('mania', $this_result);
	$this_user->AddRole('copied');

	$sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
	InsertSystemMessage($sentence, 'MANIA_RESULT');
      }
    }
  }
  else{
    foreach($vote_data->reporter as $array){ //ブン屋の処理
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //直前に死んでいたら無効

      $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
      if(in_array($this_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	$this_user->Kill('TRAPPED');
	continue;
      }

      if($this_target == $wolf_target){ //尾行成功
	if($this_target->uname == $guarded_uname) continue; //護衛されていた場合は何も出ない
	$sentence = $this_user->handle_name . "\t" . $wolf_target->handle_name . "\t";
	InsertSystemMessage($sentence . $voted_wolf->handle_name, 'REPORTER_SUCCESS');
	continue;
      }

      if($this_target->dead_flag) continue; //尾行対象が直前に死んでいたら何も起きない

      if($this_target->IsRoleGroup('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
	$this_user->Kill('REPORTER_DUTY');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($vote_data->poison_cat as $array){ //猫又の処理
	$this_user = $USERS->ByUname($array['uname']);
	if($this_user->dead_flag) continue; //直前に死んでいたら無効

	$this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

	//蘇生判定
	$this_rand = mt_rand(1, 100); //蘇生判定用乱数
	$this_result = 'failed';
	if($ROOM->test_mode) echo "revive rand : $this_rand <br>";
	do{
	  if($this_rand > 25) break; //蘇生失敗
	  if($this_rand <= 5){ //誤爆蘇生
	    if($ROOM->test_mode){
	      $new_target_list = array();
	      foreach($USERS->rows as $this_new_target){
		if($this_new_target->IsDead() && ! $this_new_target->IsDummyBoy()){
		  $new_target_list[] = $this_new_target->uname;
		}
	      }
	      if(count($new_target_list) > 0){
		$this_target = $USERS->ByUname(GetRandom($new_target_list));
	      }
	    }
	    else{
	      $query = "SELECT uname FROM user_entry WHERE room_no = {$ROOM->id} AND live = 'dead' " .
		"AND uname <> 'dummy_boy' AND uname <> '{$this_target->uname}' " .
		"AND user_no > 0 ORDER BY MD5(RAND()*NOW())";
	      if(($new_target = FetchResult($query)) !== false){ //他に対象がいる場合だけ入れ替わる
		$this_target = $USERS->ByUname($new_target);
	      }
	    }
	  }
	  if($this_target->IsRole('poison_cat')) break; //猫又なら蘇生失敗

	  $this_result = 'success';
	  $this_target->Revive(); //蘇生処理
	}while(false);

	if($this_result == 'failed') InsertSystemMessage($this_target->handle_name, 'REVIVE_FAILED');
	$sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  LoversFollowed(); //恋人後追い処理
  InsertMediumMessage(); //巫女のシステムメッセージ
  if($ROOM->test_mode) return;

  //次の日にする
  $next_date = $ROOM->date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = {$ROOM->id}");

  //次の日の処刑投票のカウントを 1 に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //夜が明けた通知
  InsertSystemTalk("MORNING\t" . $next_date, ++$ROOM->system_time, 'day system', $next_date);
  UpdateTime(); //最終書き込みを更新
  // DeleteVote(); //今までの投票を全部削除

  CheckVictory(); //勝敗のチェック
  mysql_query('COMMIT'); //一応コミット
}

//役職の所属グループを判別する
function DistinguishRoleGroup($role){
  $role_group_list = array('wolf' => 'wolf',
			   'mad' => 'mad',
			   'fox' => 'fox',
			   'cupid' => 'cupid',
			   'quiz' => 'quiz',
			   'chiroptera' => 'chiroptera',
			   'mage' => 'mage', 'voodoo_killer' => 'mage',
			   'necromancer' => 'necromancer', 'medium' => 'necromancer',
			   'guard' => 'guard', 'anti_voodoo' => 'guard', 'reporter' => 'guard',
			   'common' => 'common',
			   'poison' => 'poison',
			   'pharmacist' => 'pharmacist',
			   'assassin' => 'assassin',
			   'mind_scanner' => 'mind_scanner',
			   'jealousy' => 'jealousy',
			   'mania' => 'mania');

  foreach($role_group_list as $key => $value){
    if(strpos($role, $key) !== false) return $value;
  }
  return 'human';
}

//投票コマンドがあっているかチェック
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return;

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
