<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'ICON_CONF', 'ROLES');
$INIT_CONF->LoadFile('game_vote_functions', 'game_play_functions');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame');
$RQ_ARGS->room_no = 267; #94;
$RQ_ARGS->TestItems->test_room = array(
  'id' => $RQ_ARGS->room_no,
  'name' => '【水銀69】やる夫達の真闇鍋村',
  'comment' => 'クイズが苦手なんで鍋でも食べよう',
  'game_option'  => 'dummy_boy full_mania chaosfull chaos_open_cast no_sub_role real_time:6:4 not_open_cast',
  #'game_option'  => 'dummy_boy full_mania chaosfull chaos_open_cast no_sub_role real_time:6:4',
  //'date' => 9,
  'date' => 1,
  //'day_night' => 'aftergame',
  'day_night' => 'night',
  //'status' => 'finished'
  'status' => 'playing'
);
$RQ_ARGS->TestItems->is_virtual_room = true;
$RQ_ARGS->vote_times = 1;
$RQ_ARGS->TestItems->test_users = array();

$RQ_ARGS->TestItems->test_users[1] =& new User();
$RQ_ARGS->TestItems->test_users[1]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[1]->user_no = 1;
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->sex = 'female';
$RQ_ARGS->TestItems->test_users[1]->profile = '僕はおいしくないよ';
$RQ_ARGS->TestItems->test_users[1]->role = 'unconscious bad_status[2-2]';
$RQ_ARGS->TestItems->test_users[1]->live = 'dead';
$RQ_ARGS->TestItems->test_users[1]->last_load_day_night = NULL;
$RQ_ARGS->TestItems->test_users[1]->is_system = true;
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] =& new User();
$RQ_ARGS->TestItems->test_users[2]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[2]->user_no = 2;
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->sex = 'male';
$RQ_ARGS->TestItems->test_users[2]->profile = '';
$RQ_ARGS->TestItems->test_users[2]->role = 'vampire lovers[5] challenge_lovers';
$RQ_ARGS->TestItems->test_users[2]->live = 'live';
$RQ_ARGS->TestItems->test_users[2]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[2]->is_system = false;
$RQ_ARGS->TestItems->test_users[2]->icon_filename = '001.gif';
$RQ_ARGS->TestItems->test_users[2]->color = '#DDDDDD';

$RQ_ARGS->TestItems->test_users[3] =& new User();
$RQ_ARGS->TestItems->test_users[3]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[3]->user_no = 3;
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->sex = 'male';
$RQ_ARGS->TestItems->test_users[3]->profile = '';
$RQ_ARGS->TestItems->test_users[3]->role = 'soul_mania[11]';
$RQ_ARGS->TestItems->test_users[3]->live = 'live';
$RQ_ARGS->TestItems->test_users[3]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[3]->is_system = false;
$RQ_ARGS->TestItems->test_users[3]->icon_filename = '002.gif';
$RQ_ARGS->TestItems->test_users[3]->color = '#999999';

$RQ_ARGS->TestItems->test_users[4] =& new User();
$RQ_ARGS->TestItems->test_users[4]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[4]->user_no = 4;
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->sex = 'female';
$RQ_ARGS->TestItems->test_users[4]->profile = '';
$RQ_ARGS->TestItems->test_users[4]->role = 'stargazer_mage authority';
$RQ_ARGS->TestItems->test_users[4]->live = 'live';
$RQ_ARGS->TestItems->test_users[4]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[4]->is_system = false;
$RQ_ARGS->TestItems->test_users[4]->icon_filename = '003.gif';
$RQ_ARGS->TestItems->test_users[4]->color = '#FFD700';

$RQ_ARGS->TestItems->test_users[5] =& new User();
$RQ_ARGS->TestItems->test_users[5]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[5]->user_no = 5;
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->sex = 'female';
$RQ_ARGS->TestItems->test_users[5]->profile = '';
$RQ_ARGS->TestItems->test_users[5]->role = 'moon_cupid lovers[5] mind_receiver[2] challenge_lovers';
$RQ_ARGS->TestItems->test_users[5]->live = 'live';
$RQ_ARGS->TestItems->test_users[5]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[5]->is_system = false;
$RQ_ARGS->TestItems->test_users[5]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[5]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[6]->user_no = 6;
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->sex = 'female';
$RQ_ARGS->TestItems->test_users[6]->profile = '';
$RQ_ARGS->TestItems->test_users[6]->role = 'soul_assassin';
$RQ_ARGS->TestItems->test_users[6]->live = 'live';
$RQ_ARGS->TestItems->test_users[6]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[6]->is_system = false;
$RQ_ARGS->TestItems->test_users[6]->icon_filename = '005.gif';
$RQ_ARGS->TestItems->test_users[6]->color = '#FF0000';

$RQ_ARGS->TestItems->test_users[7] =& new User();
$RQ_ARGS->TestItems->test_users[7]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[7]->user_no = 7;
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->sex = 'male';
$RQ_ARGS->TestItems->test_users[7]->profile = '';
$RQ_ARGS->TestItems->test_users[7]->role = 'blind_guard nervy';
$RQ_ARGS->TestItems->test_users[7]->live = 'live';
$RQ_ARGS->TestItems->test_users[7]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[7]->is_system = false;
$RQ_ARGS->TestItems->test_users[7]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[7]->color = '#99CCFF';

$RQ_ARGS->TestItems->test_users[8] =& new User();
$RQ_ARGS->TestItems->test_users[8]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[8]->user_no = 8;
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->sex = 'male';
$RQ_ARGS->TestItems->test_users[8]->profile = '';
$RQ_ARGS->TestItems->test_users[8]->role = 'possessed_wolf possessed_target[3-9] decide';
$RQ_ARGS->TestItems->test_users[8]->live = 'live';
$RQ_ARGS->TestItems->test_users[8]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[8]->is_system = false;
$RQ_ARGS->TestItems->test_users[8]->icon_filename = '007.gif';
$RQ_ARGS->TestItems->test_users[8]->color = '#0066FF';

$RQ_ARGS->TestItems->test_users[9] =& new User();
$RQ_ARGS->TestItems->test_users[9]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[9]->user_no = 9;
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->sex = 'female';
$RQ_ARGS->TestItems->test_users[9]->profile = '';
$RQ_ARGS->TestItems->test_users[9]->role = 'dummy_poison upper_luck possessed[3-8]';
$RQ_ARGS->TestItems->test_users[9]->live = 'drop';
$RQ_ARGS->TestItems->test_users[9]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[9]->is_system = false;
$RQ_ARGS->TestItems->test_users[9]->icon_filename = '008.gif';
$RQ_ARGS->TestItems->test_users[9]->color = '#00EE00';

$RQ_ARGS->TestItems->test_users[10] =& new User();
$RQ_ARGS->TestItems->test_users[10]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[10]->user_no = 10;
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->sex = 'female';
$RQ_ARGS->TestItems->test_users[10]->profile = '';
$RQ_ARGS->TestItems->test_users[10]->role = 'elder_wolf';
$RQ_ARGS->TestItems->test_users[10]->live = 'live';
$RQ_ARGS->TestItems->test_users[10]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[10]->is_system = false;
$RQ_ARGS->TestItems->test_users[10]->icon_filename = '009.gif';
$RQ_ARGS->TestItems->test_users[10]->color = '#CC00CC';

$RQ_ARGS->TestItems->test_users[11] =& new User();
$RQ_ARGS->TestItems->test_users[11]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[11]->user_no = 11;
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->sex = 'female';
$RQ_ARGS->TestItems->test_users[11]->profile = '';
$RQ_ARGS->TestItems->test_users[11]->role = 'miasma_fox gynophobia';
$RQ_ARGS->TestItems->test_users[11]->live = 'live';
$RQ_ARGS->TestItems->test_users[11]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[11]->is_system = false;
$RQ_ARGS->TestItems->test_users[11]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[11]->color = '#FF9999';

//$RQ_ARGS->TestItems->test_users = 30;

//-- 仮想投票データをセット --//
$RQ_ARGS->TestItems->vote_day = array(
  #array('uname' => 'light_gray', 'target_uname' => 'dark_gray',     'vote_number' => 1),
  array('uname' => 'light_gray', 'target_uname' => 'blue',     'vote_number' => 1),
  #array('uname' => 'light_gray', 'target_uname' => 'light_blue',     'vote_number' => 1),
  #array('uname' => 'light_gray', 'target_uname' => 'cherry',     'vote_number' => 1),
  #array('uname' => 'light_gray', 'target_uname' => 'purple',     'vote_number' => 1),
  array('uname' => 'dark_gray',  'target_uname' => 'purple',       'vote_number' => 1),
  array('uname' => 'yellow',     'target_uname' => 'blue',     'vote_number' => 2),
  #array('uname' => 'yellow',     'target_uname' => 'dummy_boy',     'vote_number' => 2),
  #array('uname' => 'yellow',     'target_uname' => 'red',     'vote_number' => 2),
  #array('uname' => 'yellow',     'target_uname' => 'light_blue',     'vote_number' => 2),
  #array('uname' => 'yellow',     'target_uname' => 'cherry',     'vote_number' => 2),
  #array('uname' => 'orange',     'target_uname' => 'cherry',     'vote_number' => 1),
  #array('uname' => 'orange',     'target_uname' => 'dark_gray',     'vote_number' => 1),
  array('uname' => 'orange',     'target_uname' => 'dark_gray',     'vote_number' => 1),
  #array('uname' => 'orange',     'target_uname' => 'red',     'vote_number' => 1),
  #array('uname' => 'red',        'target_uname' => 'green',     'vote_number' => 1),
  #array('uname' => 'red',        'target_uname' => 'purple',     'vote_number' => 1),
  array('uname' => 'red',        'target_uname' => 'dark_gray',     'vote_number' => 1),
  #array('uname' => 'light_blue', 'target_uname' => 'purple',     'vote_number' => 1),
  #array('uname' => 'light_blue', 'target_uname' => 'dark_gray',     'vote_number' => 1),
  array('uname' => 'light_blue', 'target_uname' => 'orange',     'vote_number' => 1),
  array('uname' => 'blue',       'target_uname' => 'dark_gray',     'vote_number' => 1),
  #array('uname' => 'blue',       'target_uname' => 'cherry',     'vote_number' => 1),
  #array('uname' => 'green',      'target_uname' => 'light_blue', 'vote_number' => 1),
  array('uname' => 'purple',     'target_uname' => 'dark_gray',       'vote_number' => 1),
  #array('uname' => 'cherry',     'target_uname' => 'dark_gray',       'vote_number' => 1)
  array('uname' => 'cherry',     'target_uname' => 'light_blue',       'vote_number' => 1)
);

$RQ_ARGS->TestItems->vote_night = array(
  #array('uname' => 'light_gray',  'target_uname' => 'blue',  'situation' => 'POISON_CAT_DO'),
  #array('uname' => 'light_gray',  'target_uname' => NULL,  'situation' => 'POISON_CAT_NOT_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'purple',     'situation' => 'MIND_SCANNER_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'light_blue',     'situation' => 'VOODOO_KILLER_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'orange',     'situation' => 'ANTI_VOODOO_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'red',     'situation' => 'GUARD_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'cherry',     'situation' => 'GUARD_DO'),
  #array('uname' => 'light_gray', 'target_uname' => 'light_blue',     'situation' => 'DREAM_EAT'),
  #array('uname' => 'light_gray', 'target_uname' => 'red',     'situation' => 'DREAM_EAT'),
  #array('uname' => 'light_gray',  'target_uname' => 'dummy_boy',  'situation' => 'FAIRY_DO'),
  #array('uname' => 'light_gray',  'target_uname' => 'red',  'situation' => 'MANIA_DO'),
  #array('uname' => 'light_gray',  'target_uname' => 'light_blue orange',  'situation' => 'FAIRY_DO'),
  #array('uname' => 'light_gray',  'target_uname' => 'light_blue',  'situation' => 'POSSESSED_DO'),
  #array('uname' => 'light_gray',  'target_uname' => NULL,  'situation' => 'POSSESSED_NOT_DO'),
  array('uname' => 'light_gray',  'target_uname' => 'red',  'situation' => 'VAMPIRE_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'yellow',  'situation' => 'VOODOO_MAD_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'yellow',  'situation' => 'DREAM_EAT'),
  #array('uname' => 'dark_gray',  'target_uname' => NULL,  'situation' => 'TRAP_MAD_NOT_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'dark_gray',  'situation' => 'TRAP_MAD_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'light_gray',  'situation' => 'JAMMER_MAD_DO'),
  #array('uname' => 'dark_gray', 'target_uname' => 'light_gray',     'situation' => 'GUARD_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'cherry',  'situation' => 'FAIRY_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'cherry',  'situation' => 'ESCAPE_DO'),
  #array('uname' => 'dark_gray',  'target_uname' => 'cherry',  'situation' => 'VAMPIRE_DO'),
  #array('uname' => 'light_gray',  'target_uname' => 'blue',  'situation' => 'VOODOO_MAD_DO'),
  #array('uname' => 'yellow',     'target_uname' => 'cherry',     'situation' => 'VOODOO_KILLER_DO'),
  array('uname' => 'yellow',     'target_uname' => 'light_blue',     'situation' => 'MAGE_DO'),
  #array('uname' => 'yellow',     'target_uname' => 'purple',     'situation' => 'MAGE_DO'),
  #array('uname' => 'yellow',     'target_uname' => 'cherry',     'situation' => 'MAGE_DO'),
  #array('uname' => 'orange',  'target_uname' => 'red blue',  'situation' => 'CUPID_DO'),
  #array('uname' => 'orange',  'target_uname' => 'orange purple',  'situation' => 'CUPID_DO'),
  #array('uname' => 'orange',     'target_uname' => 'yellow',     'situation' => 'JAMMER_MAD_DO'),
  #array('uname' => 'orange',     'target_uname' => 'green',     'situation' => 'WOLF_EAT'),
  #array('uname' => 'orange',  'target_uname' => 'purple',  'situation' => 'MANIA_DO'),
  #array('uname' => 'red',        'target_uname' => 'cherry',     'situation' => 'VOODOO_KILLER_DO'),
  #array('uname' => 'red',        'target_uname' => 'purple',     'situation' => 'ASSASSIN_DO'),
  #array('uname' => 'red',        'target_uname' => 'light_gray',     'situation' => 'ASSASSIN_DO'),
  #array('uname' => 'red',        'target_uname' => 'blue',     'situation' => 'ASSASSIN_DO'),
  array('uname' => 'red',        'target_uname' => 'cherry',     'situation' => 'ASSASSIN_DO'),
  #array('uname' => 'red',        'target_uname' => NULL,     'situation' => 'ASSASSIN_NOT_DO'),
  #array('uname' => 'red',        'target_uname' => NULL,     'situation' => 'POISON_CAT_NOT_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'yellow',     'situation' => 'MAGE_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'purple',     'situation' => 'MAGE_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'dark_gray',     'situation' => 'GUARD_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'yellow',     'situation' => 'GUARD_DO'),
  array('uname' => 'light_blue', 'target_uname' => 'cherry',     'situation' => 'GUARD_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'red',     'situation' => 'REPORTER_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'cherry',     'situation' => 'REPORTER_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'yellow',     'situation' => 'ANTI_VOODOO_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'red',     'situation' => 'ANTI_VOODOO_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'cherry',     'situation' => 'MIND_SCANNER_DO'),
  #array('uname' => 'light_blue', 'target_uname' => 'red',     'situation' => 'VOODOO_FOX_DO'),
  #array('uname' => 'light_blue',  'target_uname' => 'light_blue orange',  'situation' => 'CUPID_DO'),
  #array('uname' => 'light_blue',  'target_uname' => 'red',     'situation' => 'ASSASSIN_DO'),
  #array('uname' => 'light_blue',  'target_uname' => 'red',     'situation' => 'MANIA_DO'),
  #array('uname' => 'light_blue',  'target_uname' => 'light_blue purple',  'situation' => 'CUPID_DO'),
  #array('uname' => 'light_blue',     'target_uname' => 'light_gray',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'blue',       'target_uname' => 'dummy_boy',  'situation' => 'WOLF_EAT'),
  #array('uname' => 'blue',       'target_uname' => 'dark_gray',  'situation' => 'WOLF_EAT'),
  #array('uname' => 'blue',       'target_uname' => 'light_gray',  'situation' => 'WOLF_EAT'),
  #array('uname' => 'blue',       'target_uname' => 'cherry',  'situation' => 'WOLF_EAT'),
  #array('uname' => 'green',      'target_uname' => 'blue', 'situation' => 'VOODOO_FOX_DO'),
  #array('uname' => 'purple',     'target_uname' => 'dark_gray',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'purple',     'target_uname' => 'red',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'purple',     'target_uname' => 'yellow',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'purple',     'target_uname' => 'dummy_boy',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'purple',     'target_uname' => 'light_blue',       'situation' => 'WOLF_EAT'),
  array('uname' => 'purple',     'target_uname' => 'cherry',       'situation' => 'WOLF_EAT'),
  #array('uname' => 'cherry',     'target_uname' => 'light_blue',  'situation' => 'VOODOO_FOX_DO')
  #array('uname' => 'cherry',     'target_uname' => 'yellow',  'situation' => 'JAMMER_MAD_DO')
  array('uname' => 'cherry',     'target_uname' => 'purple',  'situation' => 'CHILD_FOX_DO')
  #array('uname' => 'cherry',     'target_uname' => 'blue',  'situation' => 'POISON_CAT_DO')
  #array('uname' => 'cherry',     'target_uname' => 'dark_gray',  'situation' => 'POISON_CAT_DO')
  #array('uname' => 'cherry',     'target_uname' => NULL,  'situation' => 'POISON_CAT_NOT_DO')
  #array('uname' => 'cherry',     'target_uname' => 'dark_gray',  'situation' => 'POSSESSED_DO') 
  #array('uname' => 'cherry',     'target_uname' => NULL,  'situation' => 'POSSESSED_NOT_DO')
  #array('uname' => 'cherry',  'target_uname' => 'blue cherry',  'situation' => 'CUPID_DO'),
  #array('uname' => 'cherry',  'target_uname' => 'dark_gray',  'situation' => 'VAMPIRE_DO'),
  #array('uname' => 'cherry',  'target_uname' => 'purple',  'situation' => 'VAMPIRE_DO'),
);

//-- 仮想システムメッセージをセット --//
$RQ_ARGS->TestItems->system_message = array(
  1 => array('MAGE_RESULT' => array('黄色	紫	stargazer_mage_nothing'),
	     ),
  2 => array('BORDER_PRIEST_RESULT' => array('水色	0'),
	     ),
  3 => array('MAGE_RESULT' => array('黄色	さくら	human',
				    'さくら	紫	wolf'),
	     'CHILD_FOX_RESULT' => array('さくら	赤	failed'),
	     'MANIA_RESULT' => array('明灰	明灰	cute_fox'),
	     'GUARD_HUNTED' => array('明灰	暗灰'),
	     'GUARD_SUCCESS' => array('明灰	暗灰'),
	     'ASSASSIN_RESULT' => array('赤	さくら	poison_wolf'),
	     'POISON_CAT_RESULT' => array('さくら	暗灰	failed')
	     )
);

//-- 仮想イベントをセット --//
$RQ_ARGS->TestItems->event = array(
  #array('message' => 'light_gray', 'type' => 'VOTE_KILLED'),
  array('message' => 'dummy_boy', 'type' => 'WOLF_KILLED'),
);

//-- データ収集 --//
$DB_CONF->Connect(); // DB 接続
$ROOM =& new Room($RQ_ARGS); //村情報を取得
$ROOM->test_mode = true;
$ROOM->log_mode = true;
$ROOM->date = 3;
#$ROOM->day_night = 'beforegame';
#$ROOM->day_night = 'day';
$ROOM->day_night = 'night';
//$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
#foreach($USERS->rows as $user) $user->live = 'live';
#$USERS->ByID(9)->live = 'live';
#$SELF =& new User();
$SELF = $USERS->ByID(1);
$SELF = $USERS->ByID(10);
#$SELF = $USERS->TraceExchange(11);

//-- データ出力 --//
OutputHTMLHeader('投票テスト', 'game'); //HTMLヘッダ
//OutputGameOption($ROOM->game_option, '');
OutputPlayerList(); //プレイヤーリスト
#PrintData($ROOM);
#PrintData($ROOM->event);
#PrintData($USERS);
#PrintData($SELF);
#PrintData($RQ_ARGS->TestItems->vote_night);
#PrintData($USERS->ByID(8));
//$ROOM->LoadVote();
//PrintData($ROOM->vote);
//PrintData(FetchAssoc("SELECT uname, situation FROM vote WHERE room_no = {$ROOM->id}"));
//PrintData($INIT_CONF->loaded);
OutputAbility();
//PrintData($USERS->ByID(5)->GetCamp());

if($ROOM->IsDay()){ //昼の投票テスト
  $vote_message_list = AggregateVoteDay();
  if(! is_array($vote_message_list)) $vote_message_list = array();
  $stack = array();
  foreach($vote_message_list as $uname => $vote_data){
    array_unshift($vote_data, $USERS->GetHandleName($uname));
    $vote_data[] = $RQ_ARGS->vote_times;
    $stack[] = implode("\t", $vote_data);
  }
  echo GenerateVoteList($stack, $ROOM->date);
  $ROOM->day_night = 'night';
}
elseif($ROOM->IsNight()){ // 夜の投票テスト
  AggregateVoteNight();
  $ROOM->date++;
  $ROOM->day_night = 'day';
}

$view_after = true;
if($view_after){
  foreach($USERS->rows as $user){
    $user->live = $user->IsLive(true) ? 'live' : 'dead';
    if($user->updated['role']) $user->ParseRoles($user->updated['role']);
  }
  OutputPlayerList(); //プレイヤーリスト
  //OutputAbility();
}
#InsertLog();
OutputHTMLFooter(); //HTMLフッタ
