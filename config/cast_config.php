<?php
//-- ÇÛÌòÀßÄê --//
class CastConfig extends CastConfigBase{
  //-- ÇÛÌò¥Æ¡¼¥Ö¥ë --//
  /* ÀßÄê¤Î¸«Êý
    [¥²¡¼¥à»²²Ã¿Í¿ô] => array([ÇÛÌòÌ¾1] => [ÇÛÌòÌ¾1¤Î¿Í¿ô], [ÇÛÌòÌ¾2] => [ÇÛÌòÌ¾2¤Î¿Í¿ô], ...),
    ¥²¡¼¥à»²²Ã¿Í¿ô¤ÈÇÛÌòÌ¾¤Î¿Í¿ô¤Î¹ç·×¤¬¹ç¤ï¤Ê¤¤¾ì¹ç¤Ï¥²¡¼¥à³«»ÏÅêÉ¼»þ¤Ë¥¨¥é¡¼¤¬ÊÖ¤ë
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
  //-- Ìò¿¦½Ð¸½¿Í¿ô --//
  //³ÆÌò¿¦¤Î½Ð¸½¤ËÉ¬Í×¤Ê¿Í¿ô¤òÀßÄê¤¹¤ë
  var $poison         = 20; //ËäÆÇ¼Ô [Â¼¿Í2 ¢ª ËäÆÇ¼Ô1¡¢¿ÍÏµ1]
  var $assassin       = 22; //°Å»¦¼Ô [Â¼¿Í2 ¢ª °Å»¦¼Ô1¡¢¿ÍÏµ1]
  var $boss_wolf      = 18; //ÇòÏµ [¿ÍÏµ1 ¢ª ÇòÏµ]
  var $poison_wolf    = 20; //ÆÇÏµ (+ Ìô»Õ) [¿ÍÏµ1 ¢ª ÆÇÏµ1¡¢Â¼¿Í1 ¢ª Ìô»Õ1]
  var $possessed_wolf = 17; //ØáÏµ [¿ÍÏµ1 ¢ª ØáÏµ1]
  var $sirius_wolf    = 17; //Å·Ïµ [¿ÍÏµ1 ¢ª Å·Ïµ1]
  var $cupid          = 16; //¥­¥å¡¼¥Ô¥Ã¥É (14¿Í¤ÎÊý¤Ï¸½ºß¥Ï¡¼¥É¥³¡¼¥É) [Â¼¿Í1 ¢ª ¥­¥å¡¼¥Ô¥Ã¥É1]
  var $medium         = 20; //Öà½÷ (+ ½÷¿À) [Â¼¿Í2 ¢ª Öà½÷1¡¢½÷¿À1]
  var $mania          = 16; //¿ÀÏÃ¥Þ¥Ë¥¢ [Â¼¿Í1 ¢ª ¿ÀÏÃ¥Þ¥Ë¥¢1]
  var $decide         = 16; //·èÄê¼Ô [·óÇ¤]
  var $authority      = 16; //¸¢ÎÏ¼Ô [·óÇ¤]

  //´õË¾À©¤ÇÌò¿¦´õË¾¤¬ÄÌ¤ë³ÎÎ¨ (%) (¿ÈÂå¤ï¤ê·¯¤¬¤¤¤ë¾ì¹ç¤Ï 100% ¤Ë¤·¤Æ¤âÊÝ¾Ú¤µ¤ì¤Þ¤»¤ó)
  var $wish_role_rate = 100;

  //¿ÈÂå¤ï¤ê·¯¤¬¤Ê¤é¤Ê¤¤Ìò¿¦¥°¥ë¡¼¥×¤Î¥ê¥¹¥È
  var $disable_dummy_boy_role_list = array('wolf', 'fox', 'poison', 'doll_master',
					   'boss_chiroptera');

  //-- ¿¿¡¦°ÇÆé¤ÎÇÛÌòÀßÄê --//
  //¸ÇÄêÇÛÌò (ÉáÄÌ°ÇÆé)
  var $chaos_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //¸ÇÄêÇÛÌò (¿¿¡¦°ÇÆé)
  var $chaosfull_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //¸ÇÄêÇÛÌò (Ä¶¡¦°ÇÆé)
  var $chaos_hyper_fix_role_list = array('mage' => 1, 'wolf' => 1);

  //¿ÍÏµ¤ÎºÇÄã½Ð¸½ÏÈ (Ìò¿¦Ì¾ => ½Ð¸½Èæ)
  //ÉáÄÌ°ÇÆé
  var $chaos_wolf_list = array(
    'wolf'           => 60,
    'boss_wolf'      =>  5,
    'poison_wolf'    => 10,
    'tongue_wolf'    =>  5,
    'silver_wolf'    => 20);

  //¿¿¡¦°ÇÆé
  var $chaosfull_wolf_list = array(
    'wolf'           => 74,
    'boss_wolf'      =>  2,
    'cursed_wolf'    =>  1,
    'poison_wolf'    =>  4,
    'resist_wolf'    =>  4,
    'tongue_wolf'    =>  3,
    'cute_wolf'      => 10,
    'silver_wolf'    =>  2);

  //Ä¶¡¦°ÇÆé
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

  //ÍÅ¸Ñ¤ÎºÇÄã½Ð¸½ÏÈ (Ìò¿¦Ì¾ => ½Ð¸½Èæ)
  //ÉáÄÌ°ÇÆé
  var $chaos_fox_list = array(
    'fox'           => 90,
    'child_fox'     => 10);

  //¿¿¡¦°ÇÆé
  var $chaosfull_fox_list = array(
    'fox'           => 80,
    'white_fox'     =>  3,
    'poison_fox'    =>  3,
    'voodoo_fox'    =>  2,
    'cursed_fox'    =>  1,
    'cute_fox'      =>  5,
    'silver_fox'    =>  1,
    'child_fox'     =>  5);

  //Ä¶¡¦°ÇÆé
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

  //¥é¥ó¥À¥àÇÛÌò¥Æ¡¼¥Ö¥ë (Ìò¿¦Ì¾ => ½Ð¸½Èæ)
  //ÉáÄÌ°ÇÆé
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

  //¿¿¡¦°ÇÆé
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

  //Ä¶¡¦°ÇÆé
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
    'necromancer'        => 35,
    'soul_necromancer'   =>  5,
    'yama_necromancer'   => 10,
    'dummy_necromancer'  => 10,
    'medium'             => 20,
    'priest'             => 10,
    'bishop_priest'      =>  5,
    'border_priest'      =>  5,
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
    'assassin'           => 10,
    'doom_assassin'      =>  5,
    'reverse_assassin'   =>  5,
    'eclipse_assassin'   =>  5,
    'mind_scanner'       =>  8,
    'evoke_scanner'      =>  6,
    'whisper_scanner'    =>  2,
    'howl_scanner'       =>  2,
    'telepath_scanner'   =>  2,
    'jealousy'           => 10,
    'poison_jealousy'    =>  5,
    'doll'               => 10,
    'poison_doll'        =>  5,
    'friend_doll'        =>  5,
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
    'fox'                =>  6,
    'white_fox'          =>  4,
    'black_fox'          =>  4,
    'gold_fox'           =>  4,
    'phantom_fox'        =>  2,
    'poison_fox'         =>  4,
    'blue_fox'           =>  3,
    'emerald_fox'        =>  3,
    'voodoo_fox'         =>  3,
    'revive_fox'         =>  3,
    'possessed_fox'      =>  2,
    'cursed_fox'         =>  2,
    'elder_fox'          =>  3,
    'cute_fox'           =>  5,
    'scarlet_fox'        =>  4,
    'silver_fox'         =>  4,
    'child_fox'          => 10,
    'sex_fox'            =>  4,
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
    'chiroptera'         => 10,
    'poison_chiroptera'  =>  5,
    'cursed_chiroptera'  =>  3,
    'boss_chiroptera'    =>  2,
    'elder_chiroptera'   =>  5,
    'dummy_chiroptera'   => 10,
    'fairy'              =>  2,
    'spring_fairy'       =>  2,
    'summer_fairy'       =>  2,
    'autumn_fairy'       =>  2,
    'winter_fairy'       =>  2,
    'flower_fairy'       =>  2,
    'light_fairy'        =>  2,
    'dark_fairy'         =>  2,
    'mirror_fairy'       =>  2,
    'mania'              =>  3,
    'trick_mania'        =>  2,
    'soul_mania'         =>  2,
    'unknown_mania'      =>  5,
    'dummy_mania'        =>  2);

  var $chaos_min_wolf_rate = 10; //¿ÍÏµ¤ÎºÇÄã½Ð¸½Èæ (Áí¿Í¸ý/N)
  var $chaos_min_fox_rate  = 15; //ÍÅ¸Ñ¤ÎºÇÄã½Ð¸½Èæ (Áí¿Í¸ý/N)

  //Ìò¿¦¥°¥ë¡¼¥×¤ÎºÇÂç½Ð¸½Î¨ (¥°¥ë¡¼¥× => ºÇÂç¿Í¸ýÈæ)
  var $chaos_role_group_rate_list = array(
    'wolf' => 0.21, 'mad' => 0.15, 'fox' => 0.1, 'child_fox' => 0.08, 'cupid' => 0.1, 'angel' => 0.07,
    'chiroptera' => 0.12, 'fairy' => 0.12, 'mage' => 0.18, 'necromancer' => 0.15,
    'priest' => 0.1, 'guard' => 0.15, 'common' => 0.18, 'poison' => 0.15, 'cat' => 0.1,
    'pharmacist' => 0.15, 'assassin' => 0.15, 'scanner' => 0.15, 'jealousy' => 0.1, 'doll' => 0.15);

  //Â¼¿Í¤Î½Ð¸½¾å¸ÂÊäÀµ
  var $chaos_max_human_rate = 0.1; //Â¼¿Í¤ÎºÇÂç¿Í¸ýÈæ (1.0 = 100%)
  //Â¼¿Í¤«¤é¿¶¤êÊÖ¤ëÌò¿¦ => ½Ð¸½Èæ
  //ÉáÄÌ°ÇÆé
  var $chaos_replace_human_role_list = array('mania' => 1);

  //¿¿¡¦°ÇÆé
  var $chaosfull_replace_human_role_list = array('mania' => 7, 'unknown_mania' => 3);

  //Ä¶¡¦°ÇÆé
  var $chaos_hyper_replace_human_role_list = array(
    'mania' => 4, 'trick_mania' => 2, 'soul_mania' => 1,
    'unknown_mania' => 2, 'dummy_mania' => 1);

  //¤ªº×¤êÂ¼ÀìÍÑÇÛÌò¥Æ¡¼¥Ö¥ë
  var $festival_role_list = array(
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
       20 => array('emerald_wolf' => 1, 'blue_wolf' => 1, 'silver_wolf' => 2, 'voodoo_mad' => 2, 'emerald_fox' => 1, 'blue_fox' => 1, 'silver_fox' => 1, 'chiroptera' => 5, 'boss_chiroptera' => 1, 'fairy' => 5),
       21 => array('poison' => 7, 'chain_poison' => 2, 'poison_wolf' => 4, 'resist_wolf' => 1, 'poison_fox' => 2, 'quiz' => 3, 'poison_chiroptera' => 2),
       22 => array('human' => 8, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'poison_cat' => 1, 'wolf' => 4, 'boss_wolf' => 1, 'fox' => 1, 'child_fox' => 1)
				);

  //-- ´Ø¿ô --//
  //·èÆ®Â¼
  function SetDuel($user_count){
    global $ROOM;

    $role_list = array(); //½é´ü²½½èÍý
    $duel_fix_list = array();

    //Îî³¦¼«Æ°¸ø³«¥ª¥×¥·¥ç¥ó¤Ë¤è¤ëÇÛÌòÀßÄêÊ¬´ô
    if($ROOM->IsOption('not_open_cast')){
      //ËäÆÇ·èÆ®
      $duel_fix_list = array(); //¸ÇÄêÇÛÌò
      if($user_count >= 20){
	$duel_fix_list['poison_jealousy'] = 1;
	$duel_fix_list['moon_cupid'] = 1;
      }
      if($user_count >= 25) $duel_fix_list['quiz'] = 1;
      $duel_rate_list = array('poison_wolf' => 5, 'chain_poison' => 10,
			      'triangle_cupid' => 2 ,'poison' => 5);
    }
    elseif($ROOM->IsOption('auto_open_cast')){
      //Îø¿§·èÆ®
      $duel_rate_list = array('assassin' => 4, 'wolf' => 3, 'self_cupid' => 1, 'mind_cupid' => 4,
			      'triangle_cupid' => 1, 'exchange_angel' => 1); //ÇÛÊ¬ÈæÎ¨
      $duel_fix_list = array('moon_cupid' => 1,  'dummy_chiroptera' => 1);
      if($user_count >= 10) $duel_fix_list['sirius_wolf'] = 1;
      if($user_count >= 15) $duel_fix_list['howl_scanner'] = 1;
      if($user_count >= 20) $duel_fix_list['quiz'] = 1;
    }
    else{
      //°Å»¦·èÆ®
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

    //¡Ú°Ê²¼¡¢·èÆ®¤Î»ÅÍÍ¤ËÈ¼¤¦ÆÈ¼«¥³¡¼¥É¡ÛËäÆÇ¡¦Ïµ¤ÎÃÖ´¹½èÍý¡£
    //Îø¿§·èÆ®¡§Ïµ1¢ª¶äÏµ1¡£
    if(false){
      if($role_list['wolf'] > 0){
	$role_list['wolf']--;
	$role_list['silver_wolf']++;
      }
      elseif($role_list['poison_wolf'] == 0){
	//Îø¿§·èÆ®¡§Ïµ¥¼¥í¤Î»þ¤ÎÎã³°½èÍý¡£
	$role_list['wolf']++;
	$role_list['medium']--;
      }
      //ËäÆÇ·èÆ®¡§ÆÇ°ì¿ÍÅö¤¿¤ê4Ê¬¤Î1¤Î³ÎÎ¨¤Ç¡¢ÆÇ¶¶É±¤ËÃÖ´¹¡£
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
