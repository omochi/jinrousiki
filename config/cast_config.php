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
     8 => array('human' =>  4, 'wolf' => 2, 'mage' => 1, 'vampire' => 1),
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
  //固定配役 (普通闇鍋)
  var $chaos_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //固定配役 (真・闇鍋)
  var $chaosfull_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //固定配役 (超・闇鍋)
  var $chaos_hyper_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //人狼の最低出現枠 (役職名 => 出現比)
  //普通闇鍋
  var $chaos_wolf_list = array(
    'wolf'           => 60,
    'boss_wolf'      =>  5,
    'poison_wolf'    => 10,
    'tongue_wolf'    =>  5,
    'silver_wolf'    => 20);

  //真・闇鍋
  var $chaosfull_wolf_list = array(
    'wolf'           => 74,
    'boss_wolf'      =>  2,
    'cursed_wolf'    =>  1,
    'poison_wolf'    =>  4,
    'resist_wolf'    =>  4,
    'tongue_wolf'    =>  3,
    'cute_wolf'      => 10,
    'silver_wolf'    =>  2);

  //超・闇鍋
  var $chaos_hyper_wolf_list = array(
    'wolf'           => 55,
    'boss_wolf'      =>  3,
    'gold_wolf'      =>  2,
    'phantom_wolf'   =>  2,
    'cursed_wolf'    =>  1,
    'wise_wolf'      =>  3,
    'poison_wolf'    =>  3,
    'resist_wolf'    =>  4,
    'blue_wolf'      =>  2,
    'emerald_wolf'   =>  2,
    'sex_wolf'       =>  1,
    'hungry_wolf'    =>  1,
    'tongue_wolf'    =>  2,
    'possessed_wolf' =>  1,
    'sirius_wolf'    =>  1,
    'elder_wolf'     =>  2,
    'cute_wolf'      => 10,
    'scarlet_wolf'   =>  3,
    'silver_wolf'    =>  2);

  //妖狐の最低出現枠 (役職名 => 出現比)
  //普通闇鍋
  var $chaos_fox_list = array(
    'fox'           => 90,
    'child_fox'     => 10);

  //真・闇鍋
  var $chaosfull_fox_list = array(
    'fox'           => 80,
    'white_fox'     =>  3,
    'poison_fox'    =>  3,
    'voodoo_fox'    =>  2,
    'cursed_fox'    =>  1,
    'cute_fox'      =>  5,
    'silver_fox'    =>  1,
    'child_fox'     =>  5);

  //超・闇鍋
  var $chaos_hyper_fox_list = array(
    'fox'           => 61,
    'white_fox'     =>  2,
    'black_fox'     =>  3,
    'gold_fox'      =>  3,
    'phantom_fox'   =>  2,
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
  //普通闇鍋
  var $chaos_random_role_list = array(
    'human'              => 88,
    'escaper'            => 30,
    'mage'               => 50,
    'soul_mage'          =>  5,
    'psycho_mage'        => 10,
    'necromancer'        => 60,
    'medium'             => 30,
    'guard'              => 70,
    'poison_guard'       =>  5,
    'reporter'           => 15,
    'common'             => 75,
    'poison'             => 40,
    'incubate_poison'    => 10,
    'pharmacist'         => 20,
    'assassin'           => 20,
    'doll'               => 20,
    'doll_master'        => 10,
    'wolf'               => 80,
    'boss_wolf'          => 10,
    'poison_wolf'        => 40,
    'tongue_wolf'        => 20,
    'silver_wolf'        => 30,
    'mad'                => 60,
    'fanatic_mad'        => 20,
    'whisper_mad'        => 10,
    'fox'                => 50,
    'child_fox'          => 20,
    'cupid'              => 30,
    'self_cupid'         => 10,
    'quiz'               =>  2,
    'chiroptera'         => 50,
    'mania'              => 10);

  //真・闇鍋
  var $chaosfull_random_role_list = array(
    'human'              =>  3,
    'suspect'            => 15,
    'unconscious'        => 20,
    'mage'               => 20,
    'soul_mage'          =>  5,
    'psycho_mage'        => 10,
    'sex_mage'           => 15,
    'voodoo_killer'      => 10,
    'dummy_mage'         => 15,
    'necromancer'        => 40,
    'soul_necromancer'   =>  5,
    'yama_necromancer'   => 10,
    'dummy_necromancer'  => 25,
    'medium'             => 30,
    'guard'              => 40,
    'poison_guard'       =>  5,
    'reporter'           => 10,
    'anti_voodoo'        => 15,
    'dummy_guard'        => 20,
    'common'             => 80,
    'dummy_common'       => 10,
    'poison'             => 20,
    'strong_poison'      =>  5,
    'incubate_poison'    => 10,
    'dummy_poison'       => 15,
    'poison_cat'         => 10,
    'pharmacist'         => 30,
    'assassin'           => 20,
    'mind_scanner'       => 20,
    'jealousy'           => 15,
    'wolf'               => 70,
    'boss_wolf'          => 10,
    'cursed_wolf'        =>  5,
    'poison_wolf'        => 15,
    'resist_wolf'        => 15,
    'tongue_wolf'        => 30,
    'cute_wolf'          => 30,
    'silver_wolf'        => 15,
    'mad'                => 20,
    'fanatic_mad'        => 10,
    'whisper_mad'        =>  5,
    'jammer_mad'         => 10,
    'voodoo_mad'         => 10,
    'corpse_courier_mad' => 15,
    'dream_eater_mad'    => 10,
    'trap_mad'           => 10,
    'fox'                => 30,
    'white_fox'          => 10,
    'poison_fox'         =>  7,
    'voodoo_fox'         =>  5,
    'cursed_fox'         =>  3,
    'silver_fox'         =>  5,
    'child_fox'          => 10,
    'cupid'              => 25,
    'self_cupid'         => 10,
    'mind_cupid'         =>  5,
    'quiz'               =>  2,
    'chiroptera'         => 20,
    'poison_chiroptera'  =>  5,
    'cursed_chiroptera'  =>  5,
    'mania'              => 20,
    'unknown_mania'      => 10);

  //超・闇鍋
  var $chaos_hyper_random_role_list = array(
    'human'              =>  1,
    'elder'              =>  5,
    'saint'              =>  5,
    'executor'           =>  5,
    'escaper'            =>  5,
    'suspect'            =>  5,
    'unconscious'        =>  5,
    'mage'               => 15,
    'soul_mage'          =>  5,
    'psycho_mage'        => 10,
    'sex_mage'           => 10,
    'stargazer_mage'     =>  5,
    'voodoo_killer'      => 10,
    'dummy_mage'         => 10,
    'necromancer'        => 30,
    'soul_necromancer'   =>  5,
    'yama_necromancer'   => 10,
    'dummy_necromancer'  => 10,
    'medium'             => 15,
    'seal_medium'        =>  5,
    'revive_medium'      =>  5,
    'priest'             => 10,
    'bishop_priest'      =>  5,
    'border_priest'      =>  5,
    'crisis_priest'      =>  5,
    'revive_priest'      => 10,
    'guard'              => 20,
    'hunter_guard'       => 10,
    'blind_guard'        =>  5,
    'poison_guard'       =>  5,
    'fend_guard'         => 10,
    'reporter'           => 10,
    'anti_voodoo'        => 15,
    'dummy_guard'        => 15,
    'common'             => 50,
    'detective_common'   =>  5,
    'trap_common'        =>  5,
    'ghost_common'       =>  5,
    'dummy_common'       => 10,
    'poison'             => 15,
    'strong_poison'      =>  5,
    'incubate_poison'    => 10,
    'guide_poison'       =>  5,
    'chain_poison'       =>  5,
    'dummy_poison'       => 10,
    'poison_cat'         =>  5,
    'revive_cat'         =>  5,
    'sacrifice_cat'      =>  5,
    'pharmacist'         => 15,
    'cure_pharmacist'    =>  5,
    'assassin'           =>  7,
    'doom_assassin'      =>  5,
    'reverse_assassin'   =>  5,
    'soul_assassin'      =>  3,
    'eclipse_assassin'   =>  5,
    'mind_scanner'       =>  8,
    'evoke_scanner'      =>  6,
    'whisper_scanner'    =>  2,
    'howl_scanner'       =>  2,
    'telepath_scanner'   =>  2,
    'jealousy'           => 10,
    'poison_jealousy'    =>  5,
    'doll'               =>  7,
    'friend_doll'        =>  5,
    'poison_doll'        =>  5,
    'doom_doll'          =>  3,
    'doll_master'        => 10,
    'wolf'               => 10,
    'boss_wolf'          =>  5,
    'gold_wolf'          => 10,
    'phantom_wolf'       => 10,
    'cursed_wolf'        =>  5,
    'wise_wolf'          => 10,
    'poison_wolf'        => 15,
    'resist_wolf'        => 15,
    'blue_wolf'          => 10,
    'emerald_wolf'       => 10,
    'sex_wolf'           =>  5,
    'hungry_wolf'        =>  5,
    'tongue_wolf'        => 10,
    'possessed_wolf'     => 10,
    'sirius_wolf'        =>  5,
    'elder_wolf'         => 10,
    'cute_wolf'          => 15,
    'scarlet_wolf'       => 10,
    'silver_wolf'        => 10,
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
    'white_fox'          =>  3,
    'black_fox'          =>  3,
    'gold_fox'           =>  3,
    'phantom_fox'        =>  2,
    'poison_fox'         =>  4,
    'blue_fox'           =>  3,
    'emerald_fox'        =>  3,
    'voodoo_fox'         =>  3,
    'revive_fox'         =>  3,
    'possessed_fox'      =>  2,
    'cursed_fox'         =>  2,
    'elder_fox'          =>  3,
    'cute_fox'           =>  4,
    'scarlet_fox'        =>  4,
    'silver_fox'         =>  4,
    'child_fox'          =>  5,
    'sex_fox'            =>  3,
    'stargazer_fox'      =>  3,
    'jammer_fox'         =>  3,
    'miasma_fox'         =>  3,
    'cupid'              =>  3,
    'self_cupid'         =>  5,
    'moon_cupid'         =>  3,
    'mind_cupid'         =>  3,
    'triangle_cupid'     =>  3,
    'angel'              =>  5,
    'rose_angel'         =>  5,
    'lily_angel'         =>  5,
    'exchange_angel'     =>  3,
    'ark_angel'          =>  3,
    'quiz'               =>  2,
    'vampire'            =>  5,
    'chiroptera'         =>  5,
    'poison_chiroptera'  =>  3,
    'cursed_chiroptera'  =>  3,
    'boss_chiroptera'    =>  3,
    'elder_chiroptera'   =>  3,
    'dummy_chiroptera'   =>  5,
    'fairy'              =>  2,
    'spring_fairy'       =>  2,
    'summer_fairy'       =>  2,
    'autumn_fairy'       =>  2,
    'winter_fairy'       =>  2,
    'flower_fairy'       =>  2,
    'star_fairy'         =>  2,
    'sun_fairy'          =>  2,
    'moon_fairy'         =>  2,
    'grass_fairy'        =>  2,
    'light_fairy'        =>  2,
    'dark_fairy'         =>  2,
    'mirror_fairy'       =>  2,
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
    'chiroptera' => 0.12, 'fairy' => 0.12, 'mage' => 0.18, 'necromancer' => 0.15, 'medium' => 0.1,
    'priest' => 0.1, 'guard' => 0.15, 'common' => 0.18, 'poison' => 0.15, 'cat' => 0.1,
    'pharmacist' => 0.15, 'assassin' => 0.15, 'scanner' => 0.15, 'jealousy' => 0.1, 'doll' => 0.15,
    'quiz' => 0.15, 'vampire' => 0.15);

  //村人の出現上限補正
  var $chaos_max_human_rate = 0.1; //村人の最大人口比 (1.0 = 100%)
  //村人から振り返る役職 => 出現比
  //普通闇鍋
  var $chaos_replace_human_role_list = array('mania' => 1);

  //真・闇鍋
  var $chaosfull_replace_human_role_list = array('mania' => 7, 'unknown_mania' => 3);

  //超・闇鍋
  var $chaos_hyper_replace_human_role_list = array(
    'mania' => 4, 'trick_mania' => 2, 'soul_mania' => 1,
    'unknown_mania' => 2, 'dummy_mania' => 1);

  //サブ役職制限：EASYモード
  var $chaos_sub_role_limit_easy_list = array(
    'authority', 'critical_voter', 'random_voter', 'rebel', 'watcher', 'decide', 'plague',
    'good_luck', 'bad_luck');

  //サブ役職制限：NORMALモード
  var $chaos_sub_role_limit_normal_list = array(
    'authority', 'critical_voter', 'random_voter', 'rebel', 'watcher', 'decide', 'plague',
    'good_luck', 'bad_luck', 'upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
    'random_luck', 'strong_voice', 'normal_voice', 'weak_voice', 'upper_voice', 'downer_voice',
    'inside_voice', 'outside_voice', 'random_voice');

  //お祭り村専用配役テーブル
  var $festival_role_list = array(
     8 => array('human' => 1, 'mage' => 1, 'necromancer' => 1, 'guard' => 1, 'boss_wolf' => 1, 'mad' => 1, 'white_fox' => 1, 'chiroptera' => 1),
     9 => array('guard' => 2, 'dummy_guard' => 4, 'wolf' => 1, 'silver_wolf' => 1, 'cursed_fox' => 1),
    10 => array('human' => 2, 'escaper' => 1, 'mage' => 1, 'necromancer' => 1, 'guard' => 1, 'wolf' => 2, 'mad' => 1, 'fox' => 1),
    11 => array('unconscious' => 1, 'soul_mage' => 1, 'soul_necromancer' => 1, 'crisis_priest' => 1, 'guard' => 1, 'anti_voodoo' => 1, 'cure_pharmacist' => 1, 'cursed_wolf' => 1, 'silver_wolf' => 1, 'jammer_mad' => 1, 'cursed_chiroptera' => 1),
    12 => array('wise_wolf' => 1, 'jammer_mad' => 8, 'voodoo_fox' => 2, 'fairy' => 1),
    13 => array('human' => 4, 'mage' => 1, 'necromancer' => 1, 'guard' => 1, 'doll' => 1, 'doll_master' => 1, 'wolf' => 2, 'fanatic_mad' => 1, 'chiroptera' => 1),
    14 => array('necromancer' => 1, 'silver_wolf' => 2, 'fox' => 1, 'chiroptera' => 10),
    15 => array('poison' => 3, 'wolf' => 3, 'fanatic_mad' => 1, 'fox' => 1, 'chiroptera' => 6, 'boss_chiroptera' => 1),
    16 => array('dummy_guard' => 1, 'strong_poison' => 1, 'dummy_poison' => 5, 'sirius_wolf' => 3, 'dream_eater_mad' => 1, 'triangle_cupid' => 1, 'mirror_fairy' => 4),
    17 => array('sex_mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'wolf' => 2, 'gold_wolf' => 1, 'fox' => 1, 'chiroptera' => 7),
    18 => array('saint' => 1, 'soul_mage' => 1, 'soul_necromancer' => 1, 'fend_guard' => 1, 'trap_common' => 1, 'ghost_common' => 1, 'incubate_poison' => 1, 'reverse_assassin' => 1, 'wise_wolf' => 1, 'possessed_wolf' => 1, 'sirius_wolf' => 1, 'jammer_mad' => 1, 'voodoo_mad' => 1, 'voodoo_fox' => 1, 'revive_fox' => 1, 'angel' => 1, 'light_fairy' => 1, 'trick_mania' => 1),
    19 => array('revive_priest' => 1, 'anti_voodoo' => 1, 'dummy_poison' => 1, 'eclipse_assassin' => 2, 'poison_cat' => 1, 'jealousy' => 1, 'poison_wolf' => 1, 'possessed_wolf' => 1, 'sirius_wolf' => 1, 'fanatic_mad' => 1, 'agitate_mad' => 1, 'cursed_fox' => 2, 'quiz' => 1, 'mind_cupid' => 1, 'light_fairy' => 1, 'dark_fairy' => 1, 'mirror_fairy' => 1),
    20 => array('emerald_wolf' => 1, 'blue_wolf' => 1, 'silver_wolf' => 2, 'voodoo_mad' => 2, 'emerald_fox' => 1, 'blue_fox' => 1, 'silver_fox' => 1, 'chiroptera' => 5, 'boss_chiroptera' => 1, 'fairy' => 5),
    21 => array('poison' => 7, 'chain_poison' => 2, 'poison_wolf' => 4, 'resist_wolf' => 1, 'poison_fox' => 2, 'quiz' => 3, 'poison_chiroptera' => 2),
    22 => array('human' => 8, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'poison_cat' => 1, 'wolf' => 4, 'boss_wolf' => 1, 'fox' => 1, 'child_fox' => 1)
				);

  //決闘村配役データ (実際は InitializeDuel() で設定する)
  var $duel_fix_list = array(); //固定配役
  var $duel_rate_list = array('assassin' => 11, 'wolf' => 4, 'trap_mad' => 5); //配役比率

  //-- 関数 --//
  //決闘村の配役初期化処理
  function InitializeDuel($user_count){
    global $ROOM;

    //-- 霊界自動公開オプションによる配役設定分岐 --//
    if($ROOM->IsOption('not_open_cast')){ //非公開
      //-- 埋毒決闘 --//
      $duel_fix_list = array();
      if($user_count >= 20){
	$duel_fix_list['poison_jealousy'] = 1;
	$duel_fix_list['moon_cupid'] = 1;
      }
      if($user_count >= 25) $duel_fix_list['quiz'] = 1;

      $duel_rate_list = array('poison' => 5, 'chain_poison' => 10,
			      'poison_wolf' => 5, 'triangle_cupid' => 2);
    }
    elseif($ROOM->IsOption('auto_open_cast')){ //自動公開
      //-- 恋色決闘 --//
      $duel_fix_list = array();
      if($user_count >= 15) $duel_fix_list['howl_scanner'] = 1;
      if($user_count >= 20){
	$duel_fix_list['sirius_wolf'] = 1;
	$duel_fix_list['moon_cupid'] = 1;
      }
      if($user_count >= 25) $duel_fix_list['quiz'] = 1;

      $duel_rate_list = array('assassin' => 5, 'wolf' => 3, 'self_cupid' => 1, 'mind_cupid' => 4,
			      'triangle_cupid' => 1);
    }
    else{ //常時公開
      //-- 暗殺決闘 --//
      $duel_fix_list = array();
      $duel_rate_list = array('assassin' => 11, 'wolf' => 4, 'trap_mad' => 5);
    }

    //結果を登録
    $this->duel_fix_list  = $duel_fix_list;
    $this->duel_rate_list = $duel_rate_list;
  }

  //決闘村の配役最終処理
  function FinalizeDuel($user_count, &$role_list){
    global $ROOM;

    if($ROOM->IsOption('not_open_cast')){ //非公開
    }
    elseif($ROOM->IsOption('auto_open_cast')){ //自動公開
      if($role_list['self_cupid'] > 0 && $role_list['assassin'] > 1){
	$role_list['assassin']--;
	$role_list['dummy_chiroptera']++;
      }
      if($role_list['mind_cupid'] > 2){
	$role_list['mind_cupid']--;
	$role_list['exchange_angel']++;
      }
      if($role_list['wolf'] > 1){
	$role_list['wolf']--;
	$role_list['silver_wolf']++;
      }
    }
    else{ //常時公開
    }
  }

  //村人置換村の処理
  function ReplaceHuman(&$role_list, $count, $option_list){
    if(in_array('full_mania', $option_list)){ //神話マニア村
      $role_list['mania'] += $count;
      $role_list['human'] -= $count;
    }
    elseif(in_array('full_chiroptera', $option_list)){ //蝙蝠村
      $role_list['chiroptera'] += $count;
      $role_list['human'] -= $count;
    }
    elseif(in_array('full_cupid', $option_list)){ //キューピッド村
      $role_list['cupid'] += $count;
      $role_list['human'] -= $count;
    }
    elseif(in_array('replace_human', $option_list)){ //村人置換村
      $role_list['escaper'] += $count;
      $role_list['human'] -= $count;
    }
  }
}
