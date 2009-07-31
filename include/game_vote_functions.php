<?php
require_once(dirname(__FILE__) . '/game_functions.php');

//投票結果出力
function OutputVoteResult($str, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //今までの投票を全部削除
  OutputActionResult('汝は人狼なりや？[投票結果]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $str . '<br>'."\n" .
		     $back_url . '</div>', '', $unlock);
}

//人数とゲームオプションに応じた役職テーブルを返す (エラー処理は暫定)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $game_option;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $GAME_CONF->role_list[$user_count]; //人数に応じた設定リストを取得
  if($role_list == NULL){ //リストの有無をチェック
    OutputVoteResult($error_header . $user_count . '人は設定されていません' .
                     $error_footer, true, true);
  }

  if(strpos($game_option, 'quiz') !== false){ //クイズ村
    $temp_role_list = array();
    $temp_role_list['human'] = $role_list['human'];
    foreach($role_list as $key => $value){
      if($key == 'wolf' || $key == 'mad' || $key == 'common' || $key == 'fox')
	$temp_role_list[$key] = (int)$value;
      elseif($key != 'human')
	$temp_role_list['human'] += (int)$value;
    }
    $temp_role_list['human']--;
    $temp_role_list['quiz'] = 1;
    $role_list = $temp_role_list;
  }
  elseif(strpos($game_option, 'chaosfull') !== false){ //真・闇鍋
    $role_list = array(); //配列をリセット
    $role_list['wolf'] = 1; //狼1確保
    $role_list['mage'] = 1; //占い師1確保
    // $role_list['reporter'] = 1; //ブン屋1確保
    // $role_list['mad'] = 1; //狂人1確保
    $start_count = 2;

    //最低限人狼枠
    $wolf_count = ceil($user_count / 10) - 1;
    if($wolf_count > 0) $start_count += $wolf_count;
    for($i = 0; $i < $wolf_count; $i++){
      $rand = mt_rand(1, 100);
      if($rand < 3)      $role_list['boss_wolf']++;
      elseif($rand <  4) $role_list['cursed_wolf']++;
      elseif($rand < 14) $role_list['cute_wolf']++;
      elseif($rand < 17) $role_list['poison_wolf']++;
      elseif($rand < 21) $role_list['resist_wolf']++;
      elseif($rand < 24) $role_list['tongue_wolf']++;
      else               $role_list['wolf']++;
    }

    //最低限妖狐枠
    $fox_count = ceil($user_count / 20);
    if($fox_count > 0) $start_count += $fox_count;
    for($i = 0; $i < $fox_count; $i++){
      $rand = mt_rand(1, 100);
      if($rand < 2)       $role_list['cursed_fox']++;
      elseif($rand <  5)  $role_list['poison_fox']++;
      elseif($rand <  8)  $role_list['white_fox']++;
      elseif($rand < 15)  $role_list['child_fox']++;
      else                $role_list['fox']++;
    }

    for($i = $start_count; $i < $user_count; $i++){
      $rand = mt_rand(1, 1000);
      if($rand < 80)      $role_list['wolf']++;
      elseif($rand <  90) $role_list['cursed_wolf']++;
      elseif($rand < 115) $role_list['cute_wolf']++;
      elseif($rand < 130) $role_list['boss_wolf']++;
      elseif($rand < 155) $role_list['poison_wolf']++;
      elseif($rand < 165) $role_list['resist_wolf']++;
      elseif($rand < 180) $role_list['tongue_wolf']++;
      elseif($rand < 195) $role_list['fox']++;
      elseif($rand < 200) $role_list['cursed_fox']++;
      elseif($rand < 210) $role_list['poison_fox']++;
      elseif($rand < 220) $role_list['white_fox']++;
      elseif($rand < 235) $role_list['child_fox']++;
      elseif($rand < 260) $role_list['human']++;
      elseif($rand < 300) $role_list['mage']++;
      elseif($rand < 320) $role_list['soul_mage']++;
      elseif($rand < 350) $role_list['dummy_mage']++;
      elseif($rand < 390) $role_list['necromancer']++;
      elseif($rand < 410) $role_list['soul_necromancer']++;
      elseif($rand < 440) $role_list['dummy_necromancer']++;
      elseif($rand < 480) $role_list['medium']++;
      elseif($rand < 520) $role_list['mad']++;
      elseif($rand < 545) $role_list['fanatic_mad']++;
      elseif($rand < 560) $role_list['whisper_mad']++;
      elseif($rand < 630) $role_list['common']++;
      elseif($rand < 650) $role_list['dummy_common']++;
      elseif($rand < 690) $role_list['guard']++;
      elseif($rand < 715) $role_list['poison_guard']++;
      elseif($rand < 750) $role_list['dummy_guard']++;
      elseif($rand < 780) $role_list['reporter']++;
      elseif($rand < 810) $role_list['poison']++;
      elseif($rand < 820) $role_list['strong_poison']++;
      elseif($rand < 840) $role_list['incubate_poison']++;
      elseif($rand < 850) $role_list['dummy_poison']++;
      elseif($rand < 890) $role_list['pharmacist']++;
      elseif($rand < 910) $role_list['suspect']++;
      elseif($rand < 930) $role_list['unconscious']++;
      elseif($rand < 970) $role_list['cupid']++;
      elseif($rand < 997) $role_list['mania']++;
      else                $role_list['quiz']++;
    }

    //最低限補正
    $wolf_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false) $wolf_count_list[$key] = $value;
    }
    $over_wolf_count = array_sum($wolf_count_list) - floor($user_count * 0.3);
    for(; $over_wolf_count > 0; $over_wolf_count--){
      arsort($wolf_count_list);
      $this_key = key($wolf_count_list);
      $wolf_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $fox_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'fox') !== false) $fox_count_list[$key] = $value;
    }
    $over_fox_count = array_sum($fox_count_list) - floor($user_count * 0.15);
    for(; $over_fox_count > 0; $over_fox_count--){
      arsort($fox_count_list);
      $this_key = key($fox_count_list);
      $fox_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $over_cupid_count = $role_list['cupid'] - floor($user_count * 0.15);
    if($over_cupid_count > 0){
      $role_list['cupid'] -= $over_cupid_count;
      $role_list['human'] += $over_cupid_count;;
    }

    $mage_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'mage') !== false) $mage_count_list[$key] = $value;
    }
    $over_mage_count = array_sum($mage_count_list) - floor($user_count * 0.25);
    for(; $over_mage_count > 0; $over_mage_count--){
      arsort($mage_count_list);
      $this_key = key($mage_count_list);
      $mage_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $guard_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'guard') !== false) $guard_count_list[$key] = $value;
    }
    $over_guard_count = array_sum($guard_count_list) - floor($user_count * 0.15);
    for(; $over_guard_count > 0; $over_guard_count--){
      arsort($guard_count_list);
      $this_key = key($guard_count_list);
      $guard_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $common_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'common') !== false) $common_count_list[$key] = $value;
    }
    $over_common_count = array_sum($common_count_list) - floor($user_count * 0.2);
    for(; $over_common_count > 0; $over_common_count--){
      arsort($common_count_list);
      $this_key = key($common_count_list);
      $common_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $poison_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'poison') !== false) $poison_count_list[$key] = $value;
    }
    $over_poison_count = array_sum($poison_count_list) - floor($user_count * 0.2);
    for(; $over_poison_count > 0; $over_poison_count--){
      arsort($poison_count_list);
      $this_key = key($poison_count_list);
      $poison_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $mad_count_list = array();
    foreach($role_list as $key => $value){
      if($key == 'mad') $mad_count_list[$key] = $value;
    }
    $over_mad_count = array_sum($mad_count_list) - floor($user_count * 0.15);
    for(; $over_mad_count > 0; $over_mad_count--){
      arsort($mad_count_list);
      $this_key = key($mad_count_list);
      $mad_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }
  }
  elseif(strpos($game_option, 'chaos') !== false){ //闇鍋
    //-- 各陣営の人数を決定 (人数 = 各人数の出現率) --//
    $role_list = array(); //配列をリセット

    //人狼陣営
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){ //1:2 = 80:20
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
    if($user_count < 15){ //0:1 = 90:10
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
    if($user_count < 10){ //0:1 = 95:5
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
	$role_list['boss_wolf'] = $special_wolf_count;
      }
      if($user_count < 20){ //20人未満で舌禍狼出現
	if(mt_rand(1, 100) <= 40) $role_list['tongue_wolf']++;
	$role_list['boss_wolf'] = $special_wolf_count - $role_list['tongue_wolf'];
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
	$role_list['boss_wolf'] = $special_wolf_count;
      }
    }
    // //調整
    // if($wolf_count > 0){
    //   $wolf_count--;
    //   $poison_wolf_count++;
    // }

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
	  elseif($rand <= 90) $role_list['suspect']++;
	  else $role_list['dummy_mage']++;
	}
      }
      else{ //20人以上ならやや不審者を出やすくする
	for($i = 0; $i < $strangers_count; $i++){
	  $rand = mt_rand(1, 100);
	  if($rand <= 40) $role_list['unconscious']++;
	  elseif($rand <= 85) $role_list['suspect']++;
	  else $role_list['dummy_mage']++;
	}
      }
      $human_count -= $strangers_count; //村人陣営の残り人数
    }

    //出題者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 30){ //0:1 = 99:1
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }
    else{ //以後、参加人数が30人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 30) - 1;
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }

    //出題者の配役を決定
    if($quiz_count > 0 && $human_count >= $quiz_count){
      $role_list['quiz'] = $quiz_count;
      $human_count -= $quiz_count; //村人陣営の残り人数
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

  if($role_list['human'] < 0){ //"村人" の人数をチェック
    OutputVoteResult($error_header . '"村人" の人数がマイナスになってます' .
                     $error_footer, true, true);
  }
  if($role_list['wolf'] < 0){ //"人狼" の人数をチェック
    OutputVoteResult($error_header . '"人狼" の人数がマイナスになってます' .
                     $error_footer, true, true);
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
    OutputVoteResult($error_header . '村人 (' . $user_count . ') と配役の数 (' . $role_count .
                     ') が一致していません' . $error_footer, true, true);
  }

  return $now_role_list;
}

//役職の人数通知リストを作成する
function MakeRoleNameList($role_count_list){
  global $GAME_CONF;

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '　' . $value . $count;
  }
  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '　(' . $value . $count . ')';
  }
  return $sentence;
}
?>
