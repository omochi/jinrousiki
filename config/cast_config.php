<?php
//-- 配役設定 --//
class CastConfig extends CastConfigBase{
  //-- 配役テーブル --//
  /* 設定の見方
    [ゲーム参加人数] => array([配役名1] => [配役名1の人数], [配役名2] => [配役名2の人数], ...),
    ゲーム参加人数と配役名の人数の合計が合わない場合はゲーム開始投票時にエラーが返る
  */
  var $role_list = array(
     4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     5 => array('wolf' =>   1, 'mage' => 2, 'mad' => 2),
     6 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'poison' => 1, 'fox' => 1, 'cupid' => 1),
     7 => array('human' =>  3, 'wolf' => 1, 'mage' => 1, 'guard' => 1, 'fox' => 1),
     8 => array('human' =>  5, 'wolf' => 2, 'mage' => 1),
     9 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1),
    10 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1),
    11 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    12 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    13 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common'=> 2),
    14 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2),
    15 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    16 => array('human' =>  6, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    17 => array('human' =>  7, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    18 => array('human' =>  8, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    19 => array('human' =>  9, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    20 => array('human' => 10, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    21 => array('human' => 11, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    22 => array('human' => 12, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    23 => array('human' => 12, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    24 => array('human' => 13, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    25 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    26 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    27 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 2),
    28 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    29 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    30 => array('human' => 16, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    31 => array('human' => 17, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    32 => array('human' => 16, 'wolf' => 5, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 2, 'common' => 3, 'fox' => 2),
    33 => array('human' => 32, 'wolf' => 1),
    34 => array('human' => 33, 'wolf' => 1),
    35 => array('human' => 34, 'wolf' => 1),
    36 => array('human' => 35, 'wolf' => 1),
    37 => array('human' => 36, 'wolf' => 1),
    38 => array('human' => 37, 'wolf' => 1),
    39 => array('human' => 38, 'wolf' => 1),
    40 => array('human' => 39, 'wolf' => 1),
    41 => array('human' => 40, 'wolf' => 1),
    42 => array('human' => 41, 'wolf' => 1),
    43 => array('human' => 42, 'wolf' => 1),
    44 => array('human' => 43, 'wolf' => 1),
    45 => array('human' => 44, 'wolf' => 1),
    46 => array('human' => 45, 'wolf' => 1),
    47 => array('human' => 46, 'wolf' => 1),
    48 => array('human' => 47, 'wolf' => 1),
    49 => array('human' => 48, 'wolf' => 1),
    50 => array('human' => 49, 'wolf' => 1)
                         );
  //-- 役職出現人数 --//
  //各役職の出現に必要な人数を設定する
  var $poison         = 20; //埋毒者 [村人2 → 埋毒者1、人狼1]
  var $assassin       = 22; //暗殺者 [村人2 → 暗殺者1、人狼1]
  var $boss_wolf      = 18; //白狼 [人狼1 → 白狼]
  var $poison_wolf    = 20; //毒狼 (+ 薬師) [人狼1 → 毒狼1、村人1 → 薬師1]
  var $possessed_wolf = 17; //憑狼 [人狼1 → 憑狼1]
  var $sirius_wolf    = 17; //天狼 [人狼1 → 天狼1]
  var $cupid          = 16; //キューピッド (14人の方は現在ハードコード) [村人1 → キューピッド1]
  var $medium         = 20; //巫女 (+ 女神) [村人2 → 巫女1、女神1]
  var $mania          = 16; //神話マニア [村人1 → 神話マニア1]
  var $decide         = 16; //決定者 [兼任]
  var $authority      = 16; //権力者 [兼任]

  //希望制で役職希望が通る確率 (%) (身代わり君がいる場合は 100% にしても保証されません)
  var $wish_role_rate = 100;

  //身代わり君がならない役職グループのリスト
  var $disable_dummy_boy_role_list = array('wolf', 'fox', 'poison', 'doll_master',
					   'boss_chiroptera');

  //-- 真・闇鍋の配役設定 --//
  //固定配役
  var $chaos_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //人狼の最低出現枠 (役職名 => 出現比)
  var $chaos_wolf_list = array(
    'wolf'           => 58,
    'boss_wolf'      =>  3,
    'gold_wolf'      =>  2,
    'wise_wolf'      =>  3,
    'poison_wolf'    =>  3,
    'resist_wolf'    =>  4,
    'cursed_wolf'    =>  1,
    'blue_wolf'      =>  2,
    'emerald_wolf'   =>  2,
    'sex_wolf'       =>  1,
    'tongue_wolf'    =>  2,
    'possessed_wolf' =>  1,
    'sirius_wolf'    =>  1,
    'elder_wolf'     =>  2,
    'cute_wolf'      => 10,
    'scarlet_wolf'   =>  3,
    'silver_wolf'    =>  2);

  //妖狐の最低出現枠 (役職名 => 出現比)
  var $chaos_fox_list = array(
    'fox'           => 63,
    'white_fox'     =>  2,
    'black_fox'     =>  3,
    'gold_fox'      =>  3,
    'poison_fox'    =>  3,
    'blue_fox'      =>  2,
    'emerald_fox'   =>  2,
    'voodoo_fox'    =>  2,
    'revive_fox'    =>  1,
    'possessed_fox' =>  1,
    'cursed_fox'    =>  1,
    'elder_fox'     =>  2,
    'cute_fox'      =>  5,
    'scarlet_fox'   =>  3,
    'silver_fox'    =>  2,
    'child_fox'     =>  3,
    'sex_fox'       =>  2);

  //ランダム配役テーブル (役職名 => 出現比)
  var $chaos_random_role_list = array(
    'human'              =>  1,
    'elder'              =>  5,
    'saint'              =>  5,
    'executor'           =>  5,
    'suspect'            =>  5,
    'unconscious'        => 10,
    'mage'               => 20,
    'soul_mage'          =>  5,
    'psycho_mage'        => 10,
    'sex_mage'           => 10,
    'voodoo_killer'      => 10,
    'dummy_mage'         => 10,
    'necromancer'        => 35,
    'soul_necromancer'   =>  5,
    'yama_necromancer'   => 10,
    'dummy_necromancer'  => 15,
    'medium'             => 20,
    'priest'             => 10,
    'bishop_priest'      =>  5,
    'crisis_priest'      =>  5,
    'revive_priest'      => 10,
    'guard'              => 30,
    'poison_guard'       =>  5,
    'fend_guard'         => 10,
    'reporter'           => 10,
    'anti_voodoo'        => 15,
    'dummy_guard'        => 20,
    'common'             => 50,
    'detective_common'   =>  5,
    'trap_common'        =>  5,
    'ghost_common'       =>  5,
    'dummy_common'       => 10,
    'poison'             => 20,
    'strong_poison'      =>  5,
    'incubate_poison'    => 10,
    'chain_poison'       =>  5,
    'dummy_poison'       => 10,
    'poison_cat'         =>  5,
    'revive_cat'         =>  5,
    'sacrifice_cat'      =>  5,
    'pharmacist'         => 15,
    'cure_pharmacist'    =>  5,
    'assassin'           => 10,
    'doom_assassin'      =>  5,
    'reverse_assassin'   =>  5,
    'eclipse_assassin'   =>  5,
    'mind_scanner'       => 10,
    'evoke_scanner'      => 10,
    'jealousy'           => 10,
    'poison_jealousy'    =>  5,
    'doll'               => 10,
    'poison_doll'        =>  5,
    'friend_doll'        =>  5,
    'doll_master'        => 10,
    'wolf'               => 10,
    'boss_wolf'          =>  5,
    'gold_wolf'          => 10,
    'wise_wolf'          => 10,
    'poison_wolf'        => 15,
    'resist_wolf'        => 15,
    'cursed_wolf'        =>  5,
    'blue_wolf'          => 15,
    'emerald_wolf'       => 15,
    'sex_wolf'           =>  5,
    'tongue_wolf'        => 10,
    'possessed_wolf'     => 10,
    'sirius_wolf'        =>  5,
    'elder_wolf'         => 10,
    'cute_wolf'          => 15,
    'scarlet_wolf'       => 10,
    'silver_wolf'        => 15,
    'mad'                => 10,
    'fanatic_mad'        => 10,
    'whisper_mad'        =>  5,
    'jammer_mad'         => 10,
    'voodoo_mad'         => 10,
    'corpse_courier_mad' => 10,
    'agitate_mad'        =>  5,
    'miasma_mad'         =>  5,
    'dream_eater_mad'    => 10,
    'trap_mad'           => 10,
    'possessed_mad'      =>  5,
    'fox'                =>  7,
    'white_fox'          =>  4,
    'black_fox'          =>  4,
    'gold_fox'           =>  4,
    'poison_fox'         =>  4,
    'blue_fox'           =>  3,
    'emerald_fox'        =>  3,
    'voodoo_fox'         =>  3,
    'revive_fox'         =>  3,
    'possessed_fox'      =>  2,
    'cursed_fox'         =>  2,
    'elder_fox'          =>  4,
    'cute_fox'           =>  5,
    'scarlet_fox'        =>  4,
    'silver_fox'         =>  4,
    'child_fox'          => 10,
    'sex_fox'            =>  4,
    'cupid'              =>  8,
    'self_cupid'         =>  5,
    'mind_cupid'         =>  3,
    'triangle_cupid'     =>  3,
    'angel'              =>  5,
    'rose_angel'         =>  5,
    'lily_angel'         =>  5,
    'exchange_angel'     =>  3,
    'ark_angel'          =>  3,
    'quiz'               =>  2,
    'chiroptera'         => 10,
    'poison_chiroptera'  =>  5,
    'cursed_chiroptera'  =>  3,
    'boss_chiroptera'    =>  2,
    'elder_chiroptera'   =>  5,
    'dummy_chiroptera'   => 10,
    'fairy'              =>  3,
    'spring_fairy'       =>  2,
    'summer_fairy'       =>  2,
    'autumn_fairy'       =>  2,
    'winter_fairy'       =>  2,
    'light_fairy'        =>  2,
    'dark_fairy'         =>  2,
    'mirror_fairy'       =>  3,
    'mania'              =>  3,
    'trick_mania'        =>  2,
    'soul_mania'         =>  2,
    'unknown_mania'      =>  5,
    'dummy_mania'        =>  2);
  var $chaos_min_wolf_rate = 10; //人狼の最低出現比 (総人口/N)
  var $chaos_min_fox_rate  = 15; //妖狐の最低出現比 (総人口/N)

  //役職グループの最大出現率 (グループ => 最大人口比)
  var $chaos_role_group_rate_list = array(
    'wolf' => 0.21, 'mad' => 0.15, 'fox' => 0.1, 'child_fox' => 0.08, 'cupid' => 0.1, 'angel' => 0.07,
    'chiroptera' => 0.12, 'fairy' => 0.12, 'mage' => 0.18, 'necromancer' => 0.15,
    'priest' => 0.1, 'guard' => 0.15, 'common' => 0.18, 'poison' => 0.15, 'cat' => 0.1,
    'pharmacist' => 0.15, 'assassin' => 0.15, 'scanner' => 0.15, 'jealousy' => 0.1, 'doll' => 0.15);

  //村人の出現上限補正
  var $chaos_max_human_rate = 0.1; //村人の最大人口比 (1.0 = 100%)
  //村人から振り返る役職 => 出現比
  var $chaos_replace_human_role_list = array(
    'mania' => 4, 'trick_mania' => 2, 'soul_mania' => 1,
    'unknown_mania' => 2, 'dummy_mania' => 1,);

  //-- 関数 --//
  //決闘村
  function SetDuel($user_count){
    global $ROOM;

    $role_list = array(); //初期化処理
    $duel_fix_list = array();

    //暗殺11:人狼4:罠師5 (初期設定)
    /*
    $duel_fix_list = array();
    $duel_rate_list = array('assassin' => 11, 'wolf' => 4, 'trap_mad' => 5);
    */
    //暗殺2:狼1.5:求愛6.5 (求愛決闘バージョン)
    /*
    $duel_fix_list = array();
    $duel_rate_list = array('assassin' => 2, 'wolf' => 1.5, 'self_cupid' => 6.5);
    */
    //暗殺3:人狼1.5:求愛3.5:女神2  + 巫女1人:夢求愛1人 (恋色決闘バージョン)
    /*
    $duel_fix_list = array('medium' => 1, 'dummy_chiroptera' => 1);
    $duel_rate_list = array('assassin' => 3, 'wolf' => 1.5, 'self_cupid' => 3.5, 'mind_cupid' => 2);
    */

    //霊界自動公開オプションによる配役設定分岐
    if($ROOM->IsOption('not_open_cast')){
      //埋毒決闘
      /*
	【埋毒系2.0/毒狼1.75/連毒者3.0/女神2.5/暗殺0.5】
	埋毒系：（埋毒3:毒橋姫1の確率でランダム配分）
	（暗殺1）（17人以上で毒蝙蝠1）
      */
      $duel_fix_list = array('assassin' => 1); //固定配役
      if($user_count > 16) $duel_fix_list['poison_chiroptera']++;
      $duel_rate_list = array('assassin' => 2, 'poison_wolf' => 7, 'chain_poison' => 12 ,
			      'mind_cupid' => 10 ,'poison' => 9);
    }
    elseif($ROOM->IsOption('auto_open_cast')){
      //恋色決闘
      /*
	【狼1.5、暗殺2.75、罠師0.75、求愛2.5、女神2.5】（+夢求愛1）（狼1→銀狼1）
      $duel_fix_list = array('dummy_chiroptera' => 1, 'medium' => 1);
      $duel_rate_list = array('assassin' => 11, 'wolf' => 6, 'self_cupid' => 10,
			      'mind_cupid' => 10 ,'trap_mad' => 3); //配分比率
      */
      $duel_rate_list = array('assassin' => 3, 'doom_assassin' => 1, 'wolf' => 3,
			      'self_cupid' => 1, 'mind_cupid' => 4,
			      'triangle_cupid' => 2, 'exchange_angel' => 1); //配分比率
      $duel_fix_list = array('possessed_wolf' => 1, 'medium' => 1, 'dummy_chiroptera' => 1);
    }
    else{
      $duel_fix_list = array();
      $duel_rate_list = array('assassin' => 11, 'wolf' => 4, 'trap_mad' => 5);
    }

    if(array_sum($duel_fix_list) <= $user_count){
      foreach($duel_fix_list as $role => $count){
	$role_list[$role] = $count;
      }
    }
    $rest_user_count = $user_count - array_sum($role_list);
    asort($duel_rate_list);
    $total_rate = array_sum($duel_rate_list);
    $max_rate_role = array_pop(array_keys($duel_rate_list));
    foreach($duel_rate_list as $role => $rate){
      if($role == $max_rate_role) continue;
      $role_list[$role] = round($rest_user_count / $total_rate * $rate);
    }
    $role_list[$max_rate_role] = $user_count - array_sum($role_list);

    //【以下、決闘の仕様に伴う独自コード】埋毒・狼の置換処理。
    //恋色決闘：狼1→銀狼1。
    if(false){
      if($role_list['wolf'] > 0){
	$role_list['wolf']--;
	$role_list['silver_wolf']++;
      }
      elseif($role_list['poison_wolf'] == 0){
	//恋色決闘：狼ゼロの時の例外処理。
	$role_list['wolf']++;
	$role_list['medium']--;
      }
      //埋毒決闘：毒一人当たり4分の1の確率で、毒橋姫に置換。
      for($i = $role_list['poison']; $i > 0; $i--){
	$rand = mt_rand(1,4);
	if($rand == 1){
	  $role_list['poison']--;
	  $role_list['poison_jealousy']++;
	}
      }
    }
    return $role_list;
  }
}
