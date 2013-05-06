<?php
//-- 配役設定 --//
class ChaosConfig {
  //-- 固定枠 --//
  //闇鍋
  static $chaos_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //真・闇鍋
  static $chaosfull_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //超・闇鍋
  static $chaos_hyper_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //裏・闇鍋
  static $chaos_verso_fix_role_list = array();

  //-- 配役テーブル --//
  //人狼の最小出現枠 (役職名 => 出現比)
  //闇鍋
  static $chaos_wolf_list = array(
    'wolf'        => 60,
    'boss_wolf'   =>  5,
    'poison_wolf' => 10,
    'tongue_wolf' =>  5,
    'silver_wolf' => 20);

  //真・闇鍋
  static $chaosfull_wolf_list = array(
    'wolf'        => 60,
    'boss_wolf'   =>  5,
    'cursed_wolf' =>  5,
    'poison_wolf' =>  5,
    'resist_wolf' =>  5,
    'tongue_wolf' =>  5,
    'cute_wolf'   => 10,
    'silver_wolf' =>  5);

  //超・闇鍋
  static $chaos_hyper_wolf_list = array(
    'wolf'           => 15,
    'boss_wolf'      =>  2,
    'mist_wolf'      =>  2,
    'gold_wolf'      =>  2,
    'phantom_wolf'   =>  2,
    'cursed_wolf'    =>  2,
    'quiet_wolf'     =>  2,
    'wise_wolf'      =>  3,
    'disguise_wolf'  =>  2,
    'purple_wolf'    =>  2,
    'snow_wolf'      =>  2,
    'ascetic_wolf'   =>  2,
    'poison_wolf'    =>  3,
    'resist_wolf'    =>  2,
    'revive_wolf'    =>  2,
    'trap_wolf'      =>  2,
    'fire_wolf'      =>  2,
    'step_wolf'      =>  2,
    'blue_wolf'      =>  2,
    'emerald_wolf'   =>  2,
    'decieve_wolf'   =>  2,
    'doom_wolf'      =>  2,
    'sex_wolf'       =>  2,
    'sharp_wolf'     =>  2,
    'hungry_wolf'    =>  2,
    'tongue_wolf'    =>  2,
    'possessed_wolf' =>  2,
    'sirius_wolf'    =>  2,
    'elder_wolf'     =>  3,
    'cute_wolf'      =>  8,
    'scarlet_wolf'   =>  3,
    'silver_wolf'    => 10,
    'emperor_wolf'   =>  5);

  //妖狐の最小出現枠 (役職名 => 出現比)
  //闇鍋
  static $chaos_fox_list = array(
    'fox'       => 90,
    'child_fox' => 10);

  //真・闇鍋
  static $chaosfull_fox_list = array(
    'fox'        => 80,
    'white_fox'  =>  3,
    'poison_fox' =>  3,
    'voodoo_fox' =>  3,
    'cursed_fox' =>  3,
    'silver_fox' =>  3,
    'child_fox'  =>  5);

  //超・闇鍋
  static $chaos_hyper_fox_list = array(
    'fox'            => 24,
    'white_fox'      =>  2,
    'black_fox'      =>  2,
    'mist_fox'       =>  2,
    'gold_fox'       =>  2,
    'phantom_fox'    =>  2,
    'purple_fox'     =>  2,
    'snow_fox'       =>  3,
    'poison_fox'     =>  3,
    'blue_fox'       =>  2,
    'spell_fox'      =>  2,
    'sacrifice_fox'  =>  2,
    'emerald_fox'    =>  2,
    'voodoo_fox'     =>  2,
    'step_fox'       =>  2,
    'revive_fox'     =>  2,
    'possessed_fox'  =>  2,
    'doom_fox'       =>  2,
    'trap_fox'       =>  2,
    'cursed_fox'     =>  2,
    'elder_fox'      =>  3,
    'cute_fox'       =>  5,
    'scarlet_fox'    =>  3,
    'silver_fox'     =>  3,
    'immolate_fox'   =>  2,
    'child_fox'      =>  4,
    'sex_fox'        =>  2,
    'stargazer_fox'  =>  2,
    'jammer_fox'     =>  2,
    'monk_fox'       =>  2,
    'miasma_fox'     =>  2,
    'howl_fox'       =>  2,
    'vindictive_fox' =>  2,
    'critical_fox'   =>  2);

  //ランダム配役テーブル (役職名 => 出現比)
  //闇鍋
  static $chaos_random_role_list = array(
    'human'           => 88,
    'mage'            => 50,
    'soul_mage'       =>  5,
    'psycho_mage'     => 10,
    'necromancer'     => 60,
    'medium'          => 30,
    'guard'           => 70,
    'poison_guard'    =>  5,
    'reporter'        => 15,
    'common'          => 75,
    'poison'          => 40,
    'incubate_poison' => 10,
    'pharmacist'      => 20,
    'assassin'        => 20,
    'doll'            => 20,
    'doll_master'     => 10,
    'escaper'         => 30,
    'wolf'            => 80,
    'boss_wolf'       => 10,
    'poison_wolf'     => 40,
    'tongue_wolf'     => 20,
    'silver_wolf'     => 30,
    'mad'             => 60,
    'fanatic_mad'     => 20,
    'whisper_mad'     => 10,
    'fox'             => 50,
    'child_fox'       => 20,
    'cupid'           => 30,
    'self_cupid'      => 10,
    'quiz'            =>  2,
    'chiroptera'      => 50,
    'mania'           => 10);

  //真・闇鍋
  static $chaosfull_random_role_list = array(
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
    'wolf'               => 75,
    'boss_wolf'          => 10,
    'cursed_wolf'        => 10,
    'poison_wolf'        => 15,
    'resist_wolf'        => 15,
    'tongue_wolf'        => 20,
    'cute_wolf'          => 30,
    'silver_wolf'        => 15,
    'mad'                => 20,
    'fanatic_mad'        => 10,
    'whisper_mad'        =>  5,
    'jammer_mad'         => 10,
    'voodoo_mad'         => 10,
    'dream_eater_mad'    => 10,
    'trap_mad'           => 10,
    'corpse_courier_mad' => 15,
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
  static $chaos_hyper_random_role_list = array(
    'human'                 =>  10,
    'saint'                 =>  15,
    'executor'              =>  10,
    'elder'                 =>  20,
    'scripter'              =>  15,
    'eccentricer'           =>  10,
    'suspect'               =>  20,
    'unconscious'           =>  20,
    'mage'                  => 100,
    'puppet_mage'           =>  60,
    'step_mage'             =>  25,
    'soul_mage'             =>  20,
    'psycho_mage'           =>  60,
    'sex_mage'              =>  40,
    'stargazer_mage'        =>  40,
    'voodoo_killer'         =>  90,
    'cute_mage'             =>  45,
    'dummy_mage'            =>  70,
    'necromancer'           => 100,
    'soul_necromancer'      =>  20,
    'psycho_necromancer'    =>  65,
    'embalm_necromancer'    =>  60,
    'emissary_necromancer'  =>  30,
    'attempt_necromancer'   =>  60,
    'yama_necromancer'      =>  90,
    'dummy_necromancer'     =>  75,
    'medium'                =>  60,
    'bacchus_medium'        =>  45,
    'seal_medium'           =>  35,
    'revive_medium'         =>  25,
    'eclipse_medium'        =>  35,
    'priest'                =>  40,
    'bishop_priest'         =>  30,
    'dowser_priest'         =>  20,
    'weather_priest'        =>  20,
    'high_priest'           =>  20,
    'crisis_priest'         =>  30,
    'widow_priest'          =>  25,
    'holy_priest'           =>  20,
    'revive_priest'         => 100,
    'border_priest'         =>  20,
    'dummy_priest'          =>  25,
    'guard'                 => 140,
    'hunter_guard'          =>  90,
    'blind_guard'           =>  30,
    'gatekeeper_guard'      =>  45,
    'step_guard'            =>  40,
    'reflect_guard'         =>  40,
    'poison_guard'          =>  20,
    'fend_guard'            =>  30,
    'reporter'              => 100,
    'anti_voodoo'           => 150,
    'elder_guard'           =>  75,
    'dummy_guard'           =>  90,
    'common'                => 250,
    'leader_common'         =>  35,
    'detective_common'      =>  40,
    'trap_common'           =>  60,
    'sacrifice_common'      =>  50,
    'ghost_common'          =>  25,
    'spell_common'          =>  30,
    'critical_common'       =>  40,
    'hermit_common'         =>  70,
    'dummy_common'          => 150,
    'poison'                => 120,
    'strong_poison'         =>  30,
    'incubate_poison'       =>  80,
    'guide_poison'          =>  60,
    'snipe_poison'          =>  60,
    'chain_poison'          =>  60,
    'dummy_poison'          =>  90,
    'poison_cat'            =>  40,
    'revive_cat'            =>  30,
    'sacrifice_cat'         =>  30,
    'missfire_cat'          =>  20,
    'eclipse_cat'           =>  30,
    'pharmacist'            =>  60,
    'cure_pharmacist'       =>  40,
    'revive_pharmacist'     =>  30,
    'alchemy_pharmacist'    =>  30,
    'centaurus_pharmacist'  =>  40,
    'assassin'              =>  30,
    'doom_assassin'         =>  18,
    'select_assassin'       =>  18,
    'sweep_assassin'        =>  16,
    'professional_assassin' =>  12,
    'ascetic_assassin'      =>  16,
    'reverse_assassin'      =>  12,
    'soul_assassin'         =>  10,
    'eclipse_assassin'      =>  18,
    'mind_scanner'          =>  40,
    'evoke_scanner'         =>  30,
    'presage_scanner'       =>  30,
    'clairvoyance_scanner'  =>  15,
    'whisper_scanner'       =>  20,
    'howl_scanner'          =>  20,
    'telepath_scanner'      =>  20,
    'dummy_scanner'         =>  25,
    'jealousy'              =>  30,
    'divorce_jealousy'      =>  20,
    'priest_jealousy'       =>  20,
    'poison_jealousy'       =>  15,
    'miasma_jealousy'       =>  15,
    'critical_jealousy'     =>  20,
    'brownie'               =>  20,
    'thunder_brownie'       =>  15,
    'echo_brownie'          =>  10,
    'revive_brownie'        =>  15,
    'harvest_brownie'       =>  15,
    'maple_brownie'         =>  15,
    'cursed_brownie'        =>  10,
    'sun_brownie'           =>  10,
    'history_brownie'       =>  10,
    'wizard'                =>  20,
    'soul_wizard'           =>   8,
    'awake_wizard'          =>  16,
    'mimic_wizard'          =>  16,
    'spiritism_wizard'      =>  12,
    'philosophy_wizard'     =>  16,
    'barrier_wizard'        =>  10,
    'astray_wizard'         =>  12,
    'pierrot_wizard'        =>  10,
    'doll'                  =>  50,
    'friend_doll'           =>  30,
    'phantom_doll'          =>  20,
    'poison_doll'           =>  20,
    'doom_doll'             =>  20,
    'revive_doll'           =>  20,
    'scarlet_doll'          =>  20,
    'silver_doll'           =>  20,
    'doll_master'           => 100,
    'escaper'               =>  30,
    'psycho_escaper'        =>  25,
    'incubus_escaper'       =>  20,
    'succubus_escaper'      =>  20,
    'doom_escaper'          =>  15,
    'divine_escaper'        =>  10,
    'wolf'                  => 150,
    'boss_wolf'             =>  30,
    'mist_wolf'             =>  40,
    'gold_wolf'             =>  40,
    'phantom_wolf'          =>  50,
    'cursed_wolf'           =>  40,
    'quiet_wolf'            =>  50,
    'wise_wolf'             => 100,
    'disguise_wolf'         =>  40,
    'purple_wolf'           =>  40,
    'snow_wolf'             =>  40,
    'ascetic_wolf'          =>  40,
    'poison_wolf'           => 150,
    'resist_wolf'           => 100,
    'revive_wolf'           =>  40,
    'trap_wolf'             =>  40,
    'fire_wolf'             =>  40,
    'step_wolf'             =>  40,
    'blue_wolf'             =>  40,
    'emerald_wolf'          =>  40,
    'decieve_wolf'          =>  40,
    'doom_wolf'             =>  40,
    'sex_wolf'              =>  40,
    'sharp_wolf'            =>  30,
    'hungry_wolf'           =>  50,
    'tongue_wolf'           =>  30,
    'possessed_wolf'        =>  30,
    'sirius_wolf'           =>  20,
    'elder_wolf'            =>  50,
    'cute_wolf'             => 100,
    'scarlet_wolf'          =>  80,
    'silver_wolf'           =>  90,
    'emperor_wolf'          =>  50,
    'mad'                   =>  90,
    'fanatic_mad'           =>  30,
    'whisper_mad'           =>  20,
    'swindle_mad'           =>  30,
    'jammer_mad'            =>  60,
    'voodoo_mad'            =>  40,
    'enchant_mad'           =>  40,
    'step_mad'              =>  30,
    'dream_eater_mad'       =>  60,
    'possessed_mad'         =>  40,
    'trap_mad'              =>  40,
    'snow_trap_mad'         =>  30,
    'corpse_courier_mad'    =>  50,
    'amaze_mad'             =>  40,
    'agitate_mad'           =>  40,
    'miasma_mad'            =>  30,
    'critical_mad'          =>  30,
    'fire_mad'              =>  30,
    'follow_mad'            =>  40,
    'therian_mad'           =>  40,
    'revive_mad'            =>  40,
    'immolate_mad'          =>  50,
    'fox'                   =>  60,
    'white_fox'             =>  15,
    'black_fox'             =>  15,
    'mist_fox'              =>  16,
    'gold_fox'              =>  16,
    'phantom_fox'           =>  16,
    'purple_fox'            =>  20,
    'snow_fox'              =>  20,
    'poison_fox'            =>  20,
    'blue_fox'              =>  18,
    'spell_fox'             =>  20,
    'sacrifice_fox'         =>  25,
    'emerald_fox'           =>  18,
    'voodoo_fox'            =>  12,
    'step_fox'              =>  15,
    'revive_fox'            =>  15,
    'possessed_fox'         =>  15,
    'doom_fox'              =>  12,
    'trap_fox'              =>  12,
    'cursed_fox'            =>  10,
    'elder_fox'             =>  25,
    'cute_fox'              =>  30,
    'scarlet_fox'           =>  25,
    'silver_fox'            =>  20,
    'immolate_fox'          =>  30,
    'child_fox'             =>  18,
    'sex_fox'               =>  12,
    'stargazer_fox'         =>  10,
    'jammer_fox'            =>   8,
    'monk_fox'              =>   8,
    'miasma_fox'            =>  12,
    'howl_fox'              =>  12,
    'vindictive_fox'        =>   8,
    'critical_fox'          =>  12,
    'cupid'                 =>  40,
    'self_cupid'            =>  25,
    'moon_cupid'            =>  12,
    'mind_cupid'            =>  15,
    'sweet_cupid'           =>  20,
    'minstrel_cupid'        =>   8,
    'triangle_cupid'        =>  15,
    'revive_cupid'          =>  10,
    'snow_cupid'            =>  15,
    'angel'                 =>  30,
    'rose_angel'            =>  20,
    'lily_angel'            =>  20,
    'exchange_angel'        =>  12,
    'ark_angel'             =>  12,
    'sacrifice_angel'       =>   8,
    'scarlet_angel'         =>  18,
    'cursed_angel'          =>  20,
    'quiz'                  =>  20,
    'vampire'               =>  25,
    'poison_vampire'        =>  18,
    'incubus_vampire'       =>  15,
    'succubus_vampire'      =>  15,
    'passion_vampire'       =>  15,
    'step_vampire'          =>  12,
    'doom_vampire'          =>  12,
    'sacrifice_vampire'     =>  10,
    'soul_vampire'          =>   8,
    'scarlet_vampire'       =>  20,
    'chiroptera'            =>  50,
    'poison_chiroptera'     =>  25,
    'cursed_chiroptera'     =>  30,
    'boss_chiroptera'       =>  20,
    'elder_chiroptera'      =>  25,
    'cute_chiroptera'       =>  30,
    'scarlet_chiroptera'    =>  30,
    'dummy_chiroptera'      =>  20,
    'fairy'                 =>  12,
    'spring_fairy'          =>  10,
    'summer_fairy'          =>  10,
    'autumn_fairy'          =>  10,
    'winter_fairy'          =>  10,
    'flower_fairy'          =>   9,
    'star_fairy'            =>   9,
    'sun_fairy'             =>   9,
    'moon_fairy'            =>   8,
    'grass_fairy'           =>   8,
    'light_fairy'           =>   8,
    'dark_fairy'            =>   8,
    'shadow_fairy'          =>   9,
    'greater_fairy'         =>   8,
    'mirror_fairy'          =>   8,
    'sweet_fairy'           =>   8,
    'ice_fairy'             =>   6,
    'ogre'                  =>  12,
    'orange_ogre'           =>   9,
    'indigo_ogre'           =>   9,
    'cow_ogre'              =>   7,
    'horse_ogre'            =>   7,
    'poison_ogre'           =>   9,
    'west_ogre'             =>   8,
    'east_ogre'             =>   8,
    'north_ogre'            =>   8,
    'south_ogre'            =>   8,
    'incubus_ogre'          =>   8,
    'wise_ogre'             =>   8,
    'power_ogre'            =>   6,
    'revive_ogre'           =>   8,
    'sacrifice_ogre'        =>   5,
    'yaksa'                 =>  12,
    'betray_yaksa'          =>  10,
    'cursed_yaksa'          =>  10,
    'succubus_yaksa'        =>  10,
    'hariti_yaksa'          =>  10,
    'vajra_yaksa'           =>  10,
    'power_yaksa'           =>  10,
    'dowser_yaksa'          =>   8,
    'duelist'               =>  12,
    'valkyrja_duelist'      =>   9,
    'critical_duelist'      =>   5,
    'cowboy_duelist'        =>   6,
    'triangle_duelist'      =>   6,
    'doom_duelist'          =>   6,
    'sea_duelist'           =>   6,
    'avenger'               =>  12,
    'poison_avenger'        =>   8,
    'cursed_avenger'        =>   6,
    'critical_avenger'      =>   8,
    'revive_avenger'        =>   6,
    'cute_avenger'          =>  10,
    'patron'                =>  12,
    'soul_patron'           =>   7,
    'sacrifice_patron'      =>   6,
    'shepherd_patron'       =>   8,
    'plumage_patron'        =>   7,
    'critical_patron'       =>  10,
    'mania'                 =>  65,
    'trick_mania'           =>  20,
    'basic_mania'           =>  20,
    'scarlet_mania'         =>  15,
    'soul_mania'            =>  10,
    'dummy_mania'           =>  10,
    'unknown_mania'         =>  12,
    'wirepuller_mania'      =>   8,
    'fire_mania'            =>   8,
    'sacrifice_mania'       =>   6,
    'resurrect_mania'       =>   8,
    'revive_mania'          =>   8);

  //裏・闇鍋
  static $chaos_verso_random_role_list = array(
    'human'       => 14,
    'mage'        => 10,
    'necromancer' => 10,
    'guard'       =>  5,
    'common'      => 10,
    'poison'      =>  5,
    'assassin'    =>  5,
    'wolf'        => 20,
    'mad'         => 10,
    'fanatic_mad' =>  5,
    'fox'         =>  5,
    'quiz'        =>  1);

  //村人から振り返る役職 => 出現比
  //闇鍋
  static $chaos_replace_human_role_list = array('mania' => 1);

  //真・闇鍋
  static $chaosfull_replace_human_role_list = array('mania' => 7, 'unknown_mania' => 3);

  //超・闇鍋
  static $chaos_hyper_replace_human_role_list = array(
    'mania'            => 15,
    'trick_mania'      => 12,
    'basic_mania'      => 12,
    'scarlet_mania'    =>  9,
    'soul_mania'       =>  7,
    'dummy_mania'      =>  5,
    'unknown_mania'    => 15,
    'wirepuller_mania' =>  5,
    'fire_mania'       =>  5,
    'sacrifice_mania'  =>  5,
    'resurrect_mania'  =>  5,
    'revive_mania'     =>  5);

  //-- 出現補正値 --//
  static $min_wolf_rate  = 10; //人狼の最小出現比 (総人口 / N)
  static $min_fox_rate   = 15; //妖狐の最小出現比 (総人口 / N)
  static $max_human_rate = 10; //村人の最大出現比 (総人口 / N)

  //役職グループの最大出現比 (グループ => 総人口 / N)
  static $role_group_rate_list = array(
    'mage'        =>  6.2,
    'necromancer' =>  6.2,
    'medium'      =>  6.2,
    'priest'      =>  7.1,
    'guard'       =>  7.1,
    'common'      =>  7.1,
    'poison'      =>  7.1,
    'cat'         => 10,
    'pharmacist'  =>  6.2,
    'assassin'    =>  9,
    'scanner'     =>  7.1,
    'jealousy'    =>  8,
    'wizard'      =>  9,
    'doll'        =>  7.1,
    'escaper'     =>  7.1,
    'wolf'        =>  4.8,
    'mad'         =>  7.1,
    'fox'         => 10,
    'child_fox'   => 14.2,
    'cupid'       => 10,
    'angel'       => 12,
    'quiz'        =>  8,
    'vampire'     =>  6.7,
    'chiroptera'  =>  8,
    'fairy'       =>  9,
    'ogre'        =>  9,
    'yaksa'       =>  9,
    'duelist'     => 15.0,
    'avenger'     => 15.0,
    'patron'      => 14.2);

  //-- 固定配役追加モード --//
  /*
    fix    : 固定枠
    random : ランダム枠 (各配列の中身は役職 => 出現比)
    count  : ランダム出現数 (ランダム枠毎の出現数)

    例)
    doll_master が +1, [doll:poison_doll = 2:1] の割合でランダムに +1,
    [scarlet_doll:silver_doll = 5:1] の割合でランダムに +2
    'a' => array('fix'    => array('doll_master' => 1),
		 'random' => array(array('doll'  => 2, 'poison_doll' => 1),
				   array('scarlet_doll' => 5, 'silver_doll' => 1)),
		 'count'  => array(1, 2)),
  */
  static $topping_list = array(
    'a' => array('fix' => array('doll_master' => 1),
		 'random' => array(
                    array('doll'         => 30,
			  'friend_doll'  =>  5,
			  'phantom_doll' => 10,
			  'poison_doll'  => 15,
			  'doom_doll'    => 15,
			  'revive_doll'  => 10,
			  'scarlet_doll' => 10,
			  'silver_doll'  =>  5),
                    array('puppet_mage'        => 15,
			  'scarlet_doll'       =>  5,
			  'scarlet_wolf'       => 25,
			  'scarlet_fox'        => 15,
			  'scarlet_angel'      => 15,
			  'scarlet_vampire'    => 10,
			  'scarlet_chiroptera' => 10,
			  'scarlet_mania'      =>  5)),
		 'count'  => array(1, 1)),
    'b' => array('fix' => array('quiz' => 1, 'poison_ogre' => 1)),
    'c' => array('random' => array(
                   array('vampire'           => 15,
			 'poison_vampire'    => 10,
			 'incubus_vampire'   => 10,
			 'succubus_vampire'  => 10,
			 'passion_vampire'   => 10,
			 'step_vampire'      => 10,
			 'doom_vampire'      => 10,
			 'sacrifice_vampire' => 10,
			 'soul_vampire'      =>  5,
			 'scarlet_vampire'   => 10)),
		 'count' => array(1)),
    'd' => array('fix' => array('resist_wolf' => 1),
		 'random' => array(
                    array('poison_cat'    => 3,
			  'revive_cat'    => 2,
			  'sacrifice_cat' => 2,
			  'missfire_cat'  => 1,
			  'eclipse_cat'   => 2)),
		 'count'  => array(1)),
    'e' => array('fix' => array('anti_voodoo' => 1, 'possessed_wolf' => 1)),
    'f' => array('random' => array(
                   array('ogre'           => 10,
			 'orange_ogre'    =>  5,
			 'indigo_ogre'    =>  5,
			 'cow_ogre'       =>  3,
			 'horse_ogre'     =>  3,
			 'poison_ogre'    =>  3,
			 'west_ogre'      =>  3,
			 'east_ogre'      =>  3,
			 'north_ogre'     =>  3,
			 'south_ogre'     =>  3,
			 'incubus_ogre'   =>  3,
			 'wise_ogre'      =>  5,
			 'power_ogre'     =>  5,
			 'revive_ogre'    =>  5,
			 'sacrifice_ogre' =>  3,
			 'yaksa'          =>  7,
			 'betray_yaksa'   =>  5,
			 'cursed_yaksa'   =>  5,
			 'succubus_yaksa' =>  3,
			 'hariti_yaksa'   =>  5,
			 'vajra_yaksa'    =>  5,
			 'power_yaksa'    =>  5,
			 'dowser_yaksa'   =>  3)),
		 'count' => array(2)),
    'g' => array('random' => array(
		   array('mad'                => 5,
			 'fanatic_mad'        => 3,
			 'whisper_mad'        => 3,
			 'swindle_mad'        => 5,
			 'jammer_mad'         => 4,
			 'voodoo_mad'         => 4,
			 'enchant_mad'        => 5,
			 'step_mad'           => 5,
			 'dream_eater_mad'    => 5,
			 'possessed_mad'      => 5,
			 'trap_mad'           => 5,
			 'snow_trap_mad'      => 5,
			 'corpse_courier_mad' => 5,
			 'amaze_mad'          => 4,
			 'agitate_mad'        => 4,
			 'miasma_mad'         => 4,
			 'critical_mad'       => 5,
			 'fire_mad'           => 4,
			 'follow_mad'         => 5,
			 'therian_mad'        => 5,
			 'revive_mad'         => 5,
			 'immolate_mad'       => 5),
                   array('suspect'           => 1,
			 'unconscious'       => 1,
			 'dummy_mage'        => 1,
			 'dummy_necromancer' => 1,
			 'dummy_priest'      => 1,
			 'dummy_guard'       => 1,
			 'dummy_common'      => 1,
			 'dummy_poison'      => 1,
			 'dummy_scanner'     => 1,
			 'dummy_chiroptera'  => 1,
			 'dummy_mania'       => 1),
		   array('psycho_mage'        => 10,
			 'psycho_necromancer' =>  5,
			 'psycho_escaper'     => 20,
			 'dream_eater_mad'    => 10,
			 'revive_ogre'        =>  5)),
		 'count' => array(1, 1, 1)),
    'h' => array('fix' => array('human' => 2)),
    'i' => array('random' => array(
		   array('jealousy'          => 30,
			 'divorce_jealousy'  => 20,
			 'priest_jealousy'   => 15,
			 'poison_jealousy'   => 10,
			 'miasma_jealousy'   =>  5,
			 'critical_jealousy' => 20),
                   array('cupid'           => 10,
			 'self_cupid'      =>  8,
			 'moon_cupid'      =>  5,
			 'mind_cupid'      =>  3,
			 'sweet_cupid'     =>  5,
			 'minstrel_cupid'  =>  3,
			 'triangle_cupid'  =>  5,
			 'revive_cupid'    =>  3,
			 'snow_cupid'      =>  8,
			 'angel'           =>  8,
			 'rose_angel'      =>  8,
			 'lily_angel'      =>  8,
			 'exchange_angel'  =>  5,
			 'ark_angel'       =>  5,
			 'sacrifice_angel' =>  5,
			 'scarlet_angel'   =>  5,
			 'cursed_angel'    =>  6)),
		 'count' => array(1, 2)),
    'j' => array('random' => array(
		   array('duelist'          => 12,
			 'valkyrja_duelist' => 10,
			 'critical_duelist' =>  5,
			 'cowboy_duelist'   =>  6,
			 'triangle_duelist' =>  6,
			 'doom_duelist'     =>  6,
			 'sea_duelist'      =>  5,
			 'avenger'          =>  5,
			 'poison_avenger'   =>  3,
			 'cursed_avenger'   =>  3,
			 'critical_avenger' =>  3,
			 'revive_avenger'   =>  3,
			 'cute_avenger'     =>  3,
			 'patron'           =>  8,
			 'soul_patron'      =>  4,
			 'sacrifice_patron' =>  4,
			 'shepherd_patron'  =>  4,
			 'plumage_patron'   =>  4,
			 'critical_patron'  =>  6)),
		 'count' => array(1)),
    'k' => array('random' => array(
		   array('executor'             => 8,
			 'soul_mage'            => 4,
			 'soul_necromancer'     => 6,
			 'revive_medium'        => 6,
			 'high_priest'          => 6,
			 'poison_guard'         => 4,
			 'ghost_common'         => 4,
			 'strong_poison'        => 6,
			 'revive_cat'           => 6,
			 'alchemy_pharmacist'   => 6,
			 'soul_assassin'        => 4,
			 'clairvoyance_scanner' => 6,
			 'miasma_jealousy'      => 6,
			 'history_brownie'      => 6,
			 'soul_wizard'          => 6,
			 'doll_master'          => 8,
			 'divine_escaper'       => 8),
                   array('boss_wolf'      => 2,
			 'resist_wolf'    => 2,
			 'tongue_wolf'    => 1,
			 'sharp_wolf'     => 1,
			 'possessed_wolf' => 1,
			 'sirius_wolf'    => 1,
			 'whisper_mad'    => 3),
		   array('cursed_fox'       => 10,
			 'jammer_fox'       =>  5,
			 'minstrel_cupid'   =>  5,
			 'sacrifice_angel'  => 10,
			 'quiz'             =>  5,
			 'soul_vampire'     => 15,
			 'boss_chiroptera'  => 10,
			 'ice_fairy'        =>  5,
			 'sacrifice_ogre'   =>  5,
			 'dowser_yaksa'     => 10,
			 'critical_duelist' =>  4,
			 'revive_avenger'   =>  3,
			 'sacrifice_patron' =>  3,
			 'soul_mania'       =>  5,
			 'sacrifice_mania'  =>  5)),
		 'count' => array(1, 1, 1)),
    'l' => array('fix' => array('ghost_common' => 1, 'boss_wolf' => 1,
				'silver_wolf' => 1, 'howl_fox' => 1)),
    'm' => array('fix' => array('sweep_assassin' => 2, 'trap_wolf' => 1, 'doom_fox' => 1)),
    'n' => array('fix' => array('guard' => 1, 'trap_wolf' => 1, 'trap_mad' => 1)),
    'o' => array('fix' => array('voodoo_killer' => 1, 'cursed_yaksa' => 1, 'cursed_avenger' => 1),
		 'random' => array(
                    array('wizard'            => 20,
			  'soul_wizard'       =>  5,
			  'awake_wizard'      => 12,
			  'mimic_wizard'      => 12,
			  'spiritism_wizard'  => 10,
			  'philosophy_wizard' => 15,
			  'barrier_wizard'    =>  8,
			  'astray_wizard'     => 10,
			  'pierrot_wizard'    =>  8)),
		 'count'  => array(1)),
    'p' => array('fix' => array('step_wolf' => 1),
		 'random' => array(
                    array('step_mage'    => 2,
			  'step_guard'   => 2,
			  'step_mad'     => 2,
			  'step_fox'     => 2,
			  'step_vampire' => 2)),
		 'count'  => array(2)),
    'q' => array('random' => array(
                    array('sex_mage' => 2,
			  'sex_wolf' => 1,
			  'sex_fox'  => 1),
                    array('incubus_escaper'  => 3,
			  'succubus_escaper' => 3,
			  'angel'            => 1,
			  'rose_angel'       => 1,
			  'lily_angel'       => 1,
			  'incubus_vampire'  => 2,
			  'succubus_vampire' => 2,
			  'incubus_ogre'     => 2,
			  'succubus_yaksa'   => 2)),
		 'count'  => array(1, 1)),
    'r' => array('random' => array(
                    array('fairy'         => 1,
			  'spring_fairy'  => 1,
			  'summer_fairy'  => 1,
			  'autumn_fairy'  => 1,
			  'winter_fairy'  => 1,
			  'flower_fairy'  => 1,
			  'star_fairy'    => 1,
			  'sun_fairy'     => 1,
			  'moon_fairy'    => 1,
			  'grass_fairy'   => 1,
			  'light_fairy'   => 1,
			  'dark_fairy'    => 1,
			  'shadow_fairy'  => 1,
			  'greater_fairy' => 1,
			  'mirror_fairy'  => 1,
			  'sweet_fairy'   => 1,
			  'ice_fairy'     => 1),
                    array('dummy_guard'     => 1,
			  'dummy_poison'    => 1,
			  'dream_eater_mad' => 2)),
		 'count'  => array(1, 1)),
			    );

  //-- 出現率変動モード --//
  /* 役職 => 倍率 (0 なら出現しなくなる) */
  static $boost_rate_list = array(
    'a' => array('plumage_patron' => 17,
		 'scarlet_mania'  => 10),
    'b' => array('elder'             => 0,
		 'scripter'          => 0,
		 'eccentricer'       => 0,
		 'elder_guard'       => 0,
		 'critical_common'   => 0,
		 'critical_jealousy' => 0,
		 'brownie'           => 0,
		 'harvest_brownie'   => 0,
		 'maple_brownie'     => 0,
		 'philosophy_wizard' => 0,
		 'divine_escaper'    => 0,
		 'ascetic_wolf'      => 0,
		 'elder_wolf'        => 0,
		 'possessed_mad'     => 0,
		 'elder_fox'         => 0,
		 'elder_chiroptera'  => 0,
		 'critical_mad'      => 0,
		 'critical_fox'      => 0,
		 'poison_ogre'       => 0,
		 'critical_duelist'  => 0,
		 'cowboy_duelist'    => 0,
		 'critical_avenger'  => 0,
		 'critical_patron'   => 0,
		 'wirepuller_mania'  => 0),
    'c' => array('human'         => 0,
		 'mage'          => 0,
		 'necromancer'   => 0,
		 'medium'        => 0,
		 'priest'        => 0,
		 'guard'         => 0,
		 'common'        => 0,
		 'poison'        => 0,
		 'poison_cat'    => 0,
		 'pharmacist'    => 0,
		 'assassin'      => 0,
		 'mind_scanner'  => 0,
		 'jealousy'      => 0,
		 'brownie'       => 0,
		 'wizard'        => 0,
		 'doll'          => 0,
		 'escaper'       => 0,
		 'wolf'          => 0,
		 'mad'           => 0,
		 'fox'           => 0,
		 'child_fox'     => 0,
		 'cupid'         => 0,
		 'angel'         => 0,
		 'quiz'          => 0,
		 'vampire'       => 0,
		 'chiroptera'    => 0,
		 'fairy'         => 0,
		 'ogre'          => 0,
		 'yaksa'         => 0,
		 'duelist'       => 0,
		 'avenger'       => 0,
		 'patron'        => 0,
		 'mania'         => 0,
		 'unknown_mania' => 0),
    'd' => array('revive_medium' => 0,
		 'poison_cat'    => 0,
		 'revive_cat'    => 0,
		 'sacrifice_cat' => 0,
		 'missfire_cat'  => 0,
		 'eclipse_cat'   => 0,
		 'revive_fox'    => 0,
		 'revive_mania'  => 0),
    'e' => array('possessed_wolf' => 0,
		 'possessed_mad'  => 0,
		 'possessed_fox'  => 0,
		 'exchange_angel' => 0),
    'f' => array('chiroptera'         =>  0,
		 'poison_chiroptera'  =>  0,
		 'cursed_chiroptera'  =>  0,
		 'boss_chiroptera'    =>  0,
		 'elder_chiroptera'   =>  0,
		 'cute_chiroptera'    =>  0,
		 'scarlet_chiroptera' =>  0,
		 'dummy_chiroptera'   =>  0,
		 'fairy'              =>  0,
		 'spring_fairy'       =>  0,
		 'summer_fairy'       =>  0,
		 'autumn_fairy'       =>  0,
		 'winter_fairy'       =>  0,
		 'flower_fairy'       =>  0,
		 'star_fairy'         =>  0,
		 'sun_fairy'          =>  0,
		 'moon_fairy'         =>  0,
		 'grass_fairy'        =>  0,
		 'light_fairy'        =>  0,
		 'dark_fairy'         =>  0,
		 'shadow_fairy'       =>  0,
		 'greater_fairy'      =>  0,
		 'mirror_fairy'       =>  0,
		 'sweet_fairy'        =>  0,
		 'ice_fairy'          =>  0,
		 'ogre'               =>  0,
		 'orange_ogre'        =>  0,
		 'indigo_ogre'        =>  0,
		 'cow_ogre'           =>  0,
		 'horse_ogre'         =>  0,
		 'poison_ogre'        =>  0,
		 'west_ogre'          =>  0,
		 'east_ogre'          =>  0,
		 'north_ogre'         =>  0,
		 'south_ogre'         =>  0,
		 'incubus_ogre'       =>  0,
		 'wise_ogre'          =>  0,
		 'power_ogre'         =>  0,
		 'revive_ogre'        =>  0,
		 'sacrifice_ogre'     =>  0,
		 'yaksa'              =>  0,
		 'betray_yaksa'       =>  0,
		 'cursed_yaksa'       =>  0,
		 'succubus_yaksa'     =>  0,
		 'hariti_yaksa'       =>  0,
		 'vajra_yaksa'        =>  0,
		 'power_yaksa'        =>  0,
		 'dowser_yaksa'       =>  0,
		 'duelist'            =>  0,
		 'valkyrja_duelist'   =>  0,
		 'critical_duelist'   =>  0,
		 'cowboy_duelist'     =>  0,
		 'triangle_duelist'   =>  0,
		 'doom_duelist'       =>  0,
		 'sea_duelist'        =>  0,
		 'avenger'            =>  0,
		 'poison_avenger'     =>  0,
		 'cursed_avenger'     =>  0,
		 'critical_avenger'   =>  0,
		 'revive_avenger'     =>  0,
		 'cute_avenger'       =>  0,
		 'patron'             =>  0,
		 'soul_patron'        =>  0,
		 'sacrifice_patron'   =>  0,
		 'shepherd_patron'    =>  0,
		 'plumage_patron'     =>  0,
		 'critical_patron'    =>  0),
    'g' => array('jealousy'          =>  0,
		 'divorce_jealousy'  =>  0,
		 'priest_jealousy'   =>  0,
		 'poison_jealousy'   =>  0,
		 'miasma_jealousy'   =>  0,
		 'critical_jealousy' =>  0,
		 'cupid'             =>  0,
		 'self_cupid'        =>  0,
		 'moon_cupid'        =>  0,
		 'mind_cupid'        =>  0,
		 'sweet_cupid'       =>  0,
		 'minstrel_cupid'    =>  0,
		 'triangle_cupid'    =>  0,
		 'revive_cupid'      =>  0,
		 'snow_cupid'        =>  0,
		 'angel'             =>  0,
		 'rose_angel'        =>  0,
		 'lily_angel'        =>  0,
		 'exchange_angel'    =>  0,
		 'ark_angel'         =>  0,
		 'sacrifice_angel'   =>  0,
		 'scarlet_angel'     =>  0,
		 'cursed_angel'      =>  0),
    'h' => array('poison_guard'         => 0,
		 'poison'               => 0,
		 'strong_poison'        => 0,
		 'incubate_poison'      => 0,
		 'guide_poison'         => 0,
		 'snipe_poison'         => 0,
		 'chain_poison'         => 0,
		 'dummy_poison'         => 0,
		 'poison_cat'           => 0,
		 'pharmacist'           => 0,
		 'cure_pharmacist'      => 0,
		 'revive_pharmacist'    => 0,
		 'alchemy_pharmacist'   => 0,
		 'centaurus_pharmacist' => 0,
		 'poison_jealousy'      => 0,
		 'poison_doll'          => 0,
		 'poison_wolf'          => 0,
		 'resist_wolf'          => 0,
		 'poison_fox'           => 0,
		 'poison_vampire'       => 0,
		 'poison_chiroptera'    => 0,
		 'horse_ogre'           => 0,
		 'poison_ogre'          => 0,
		 'poison_avenger'       => 0,
		 'plumage_patron'       => 0)
			       );

  //サブ役職制限：EASYモード
  static $chaos_sub_role_limit_easy_list = array(
    'decide', 'plague', 'counter_decide', 'dropout', 'good_luck', 'bad_luck', 'authority',
    'reduce_voter', 'upper_voter', 'downer_voter', 'critical_voter', 'random_voter', 'rebel',
    'watcher');

  //サブ役職制限：NORMALモード
  static $chaos_sub_role_limit_normal_list = array(
    'decide', 'plague', 'counter_decide', 'dropout', 'good_luck', 'bad_luck', 'authority',
    'reduce_voter', 'upper_voter', 'downer_voter', 'critical_voter', 'random_voter', 'rebel',
    'watcher', 'upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck', 'random_luck',
    'wisp', 'black_wisp', 'spell_wisp', 'foughten_wisp', 'gold_wisp');

  //サブ役職制限：HARDモード
  static $chaos_sub_role_limit_hard_list = array(
    'decide', 'plague', 'counter_decide', 'dropout', 'good_luck', 'bad_luck', 'authority',
    'reduce_voter', 'upper_voter', 'downer_voter', 'critical_voter', 'random_voter', 'rebel',
    'watcher', 'upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck', 'random_luck',
    'strong_voice', 'normal_voice', 'weak_voice', 'upper_voice', 'downer_voice', 'inside_voice',
    'outside_voice', 'random_voice', 'mind_open', 'wisp', 'black_wisp', 'spell_wisp',
    'foughten_wisp', 'gold_wisp');
}
