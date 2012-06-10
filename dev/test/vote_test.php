<?php
error_reporting(E_ALL);
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('room_config', 'role_class', 'icon_class', 'game_play_functions',
		     'game_vote_functions');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame', true);
RQ::$get->room_no = 94;
RQ::$get->reverse_log = null;
RQ::$get->TestItems = new StdClass();
RQ::GetTest()->test_room = array(
  'id' => RQ::$get->room_no,
  'name' => '投票テスト村',
  'comment' => '',
  //'game_option' => 'dummy_boy full_mania chaosfull chaos_open_cast no_sub_role real_time:6:4 joker',
  'game_option' => 'dummy_boy chaosfull chaos_open_cast no_sub_role real_time:6:4 joker weather',
  'date' => 9,
  'scene' => 'night',
  //'scene' => 'aftergame',
  'status' => 'playing'
  //'status' => 'finished'
);
RQ::AddTestRoom('game_option', 'not_open_cast');
RQ::AddTestRoom('game_option', 'open_vote death_note');
#RQ::AddTestRoom('game_option', 'seal_message');
#RQ::AddTestRoom('game_option', 'quiz');
RQ::GetTest()->is_virtual_room = true;
RQ::GetTest()->test_users = array();
for ($id = 1; $id <= 25; $id++) RQ::GetTest()->test_users[$id] = new User();

RQ::GetTest()->test_users[1]->uname = 'dummy_boy';
RQ::GetTest()->test_users[1]->handle_name = '身代わり君';
RQ::GetTest()->test_users[1]->sex = 'female';
RQ::GetTest()->test_users[1]->role = 'resurrect_mania';
RQ::GetTest()->test_users[1]->live = 'dead';
RQ::GetTest()->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
RQ::GetTest()->test_users[1]->color = '#000000';

RQ::GetTest()->test_users[2]->uname = 'light_gray';
RQ::GetTest()->test_users[2]->handle_name = '明灰';
RQ::GetTest()->test_users[2]->sex = 'male';
RQ::GetTest()->test_users[2]->role = 'trap_wolf lost_ability authority';
RQ::GetTest()->test_users[2]->live = 'live';

RQ::GetTest()->test_users[3]->uname = 'dark_gray';
RQ::GetTest()->test_users[3]->handle_name = '暗灰';
RQ::GetTest()->test_users[3]->sex = 'male';
RQ::GetTest()->test_users[3]->role = 'possessed_wolf possessed_target[3-17]';
RQ::GetTest()->test_users[3]->live = 'live';

RQ::GetTest()->test_users[4]->uname = 'yellow';
RQ::GetTest()->test_users[4]->handle_name = '黄色';
RQ::GetTest()->test_users[4]->sex = 'female';
RQ::GetTest()->test_users[4]->role = 'stargazer_mage lovers[16] challenge_lovers rebel';
RQ::GetTest()->test_users[4]->live = 'live';

RQ::GetTest()->test_users[5]->uname = 'orange';
RQ::GetTest()->test_users[5]->handle_name = 'オレンジ';
RQ::GetTest()->test_users[5]->sex = 'female';
RQ::GetTest()->test_users[5]->role = 'soul_mage febris[6]';
RQ::GetTest()->test_users[5]->live = 'live';

RQ::GetTest()->test_users[6]->uname = 'red';
RQ::GetTest()->test_users[6]->handle_name = '赤';
RQ::GetTest()->test_users[6]->sex = 'female';
RQ::GetTest()->test_users[6]->role = 'seal_medium possessed[4-15]';
RQ::GetTest()->test_users[6]->live = 'dead';

RQ::GetTest()->test_users[7]->uname = 'light_blue';
RQ::GetTest()->test_users[7]->handle_name = '水色';
RQ::GetTest()->test_users[7]->sex = 'male';
RQ::GetTest()->test_users[7]->role = 'dummy_guard';
RQ::GetTest()->test_users[7]->live = 'live';

RQ::GetTest()->test_users[8]->uname = 'blue';
RQ::GetTest()->test_users[8]->handle_name = '青';
RQ::GetTest()->test_users[8]->sex = 'male';
RQ::GetTest()->test_users[8]->role = 'priest';
RQ::GetTest()->test_users[8]->live = 'live';

RQ::GetTest()->test_users[9]->uname = 'green';
RQ::GetTest()->test_users[9]->handle_name = '緑';
RQ::GetTest()->test_users[9]->sex = 'female';
RQ::GetTest()->test_users[9]->role = 'missfire_cat joker[2]';
RQ::GetTest()->test_users[9]->live = 'live';

RQ::GetTest()->test_users[10]->uname = 'purple';
RQ::GetTest()->test_users[10]->handle_name = '紫';
RQ::GetTest()->test_users[10]->sex = 'female';
RQ::GetTest()->test_users[10]->role = 'reverse_assassin death_note[5]';
RQ::GetTest()->test_users[10]->live = 'live';

RQ::GetTest()->test_users[11]->uname = 'cherry';
RQ::GetTest()->test_users[11]->handle_name = 'さくら';
RQ::GetTest()->test_users[11]->sex = 'female';
RQ::GetTest()->test_users[11]->role = 'angel downer_luck';
RQ::GetTest()->test_users[11]->live = 'live';

RQ::GetTest()->test_users[12]->uname = 'white';
RQ::GetTest()->test_users[12]->handle_name = '白';
RQ::GetTest()->test_users[12]->sex = 'male';
RQ::GetTest()->test_users[12]->role = 'amaze_mad death_selected[5]';
RQ::GetTest()->test_users[12]->live = 'live';

RQ::GetTest()->test_users[13]->uname = 'black';
RQ::GetTest()->test_users[13]->handle_name = '黒';
RQ::GetTest()->test_users[13]->sex = 'male';
RQ::GetTest()->test_users[13]->role = 'wolf mind_presage[23]';
RQ::GetTest()->test_users[13]->live = 'live';

RQ::GetTest()->test_users[14]->uname = 'gold';
RQ::GetTest()->test_users[14]->handle_name = '金';
RQ::GetTest()->test_users[14]->sex = 'female';
RQ::GetTest()->test_users[14]->role = 'leader_common';
RQ::GetTest()->test_users[14]->live = 'live';

RQ::GetTest()->test_users[15]->uname = 'frame';
RQ::GetTest()->test_users[15]->handle_name = '炎';
RQ::GetTest()->test_users[15]->sex = 'female';
RQ::GetTest()->test_users[15]->role = 'possessed_fox possessed_target[4-6] lost_ability';
RQ::GetTest()->test_users[15]->live = 'live';

RQ::GetTest()->test_users[16]->uname = 'scarlet';
RQ::GetTest()->test_users[16]->handle_name = '紅';
RQ::GetTest()->test_users[16]->sex = 'female';
RQ::GetTest()->test_users[16]->role = 'sweet_fairy lovers[16] challenge_lovers';
RQ::GetTest()->test_users[16]->live = 'live';

RQ::GetTest()->test_users[17]->uname = 'sky';
RQ::GetTest()->test_users[17]->handle_name = '空';
RQ::GetTest()->test_users[17]->sex = 'male';
RQ::GetTest()->test_users[17]->role = 'necromancer possessed[3-3] disfavor';
RQ::GetTest()->test_users[17]->live = 'dead';

RQ::GetTest()->test_users[18]->uname = 'sea';
RQ::GetTest()->test_users[18]->handle_name = '海';
RQ::GetTest()->test_users[18]->sex = 'male';
RQ::GetTest()->test_users[18]->role = 'detective_common';
RQ::GetTest()->test_users[18]->live = 'live';

RQ::GetTest()->test_users[19]->uname = 'land';
RQ::GetTest()->test_users[19]->handle_name = '陸';
RQ::GetTest()->test_users[19]->sex = 'female';
RQ::GetTest()->test_users[19]->role = 'enchant_mad psycho_infected';
RQ::GetTest()->test_users[19]->live = 'live';

RQ::GetTest()->test_users[20]->uname = 'rose';
RQ::GetTest()->test_users[20]->handle_name = '薔薇';
RQ::GetTest()->test_users[20]->sex = 'female';
RQ::GetTest()->test_users[20]->role = 'vampire';
RQ::GetTest()->test_users[20]->live = 'live';

RQ::GetTest()->test_users[21]->uname = 'peach';
RQ::GetTest()->test_users[21]->handle_name = '桃';
RQ::GetTest()->test_users[21]->sex = 'female';
RQ::GetTest()->test_users[21]->role = 'basic_mania panelist';
RQ::GetTest()->test_users[21]->live = 'live';

RQ::GetTest()->test_users[22]->uname = 'gust';
RQ::GetTest()->test_users[22]->handle_name = '霧';
RQ::GetTest()->test_users[22]->sex = 'female';
RQ::GetTest()->test_users[22]->role = 'fairy reduce_voter';
RQ::GetTest()->test_users[22]->live = 'live';

RQ::GetTest()->test_users[23]->uname = 'cloud';
RQ::GetTest()->test_users[23]->handle_name = '雲';
RQ::GetTest()->test_users[23]->sex = 'male';
RQ::GetTest()->test_users[23]->role = 'soul_vampire deep_sleep';
RQ::GetTest()->test_users[23]->live = 'live';

RQ::GetTest()->test_users[24]->uname = 'moon';
RQ::GetTest()->test_users[24]->handle_name = '月';
RQ::GetTest()->test_users[24]->sex = 'female';
RQ::GetTest()->test_users[24]->role = 'barrier_wizard infected[20]';
RQ::GetTest()->test_users[24]->live = 'live';

RQ::GetTest()->test_users[25]->uname = 'sun';
RQ::GetTest()->test_users[25]->handle_name = '太陽';
RQ::GetTest()->test_users[25]->sex = 'male';
RQ::GetTest()->test_users[25]->role = 'poison_ogre[1] disfavor';
RQ::GetTest()->test_users[25]->live = 'live';
RQ::GetTest()->test_users[25]->profile = "あーうー\nうーあー";

//RQ::GetTest()->test_users = 30;
$icon_color_list = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
			 '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
foreach (RQ::GetTest()->test_users as $id => $user) {
  $user->room_no = RQ::$get->room_no;
  $user->user_no = $id;
  $user->role_id = $id;
  if (! isset($user->profile)) $user->profile = $id;
  $user->last_load_scene = 'night';
  if ($id > 1) {
    $user->color = $icon_color_list[($id - 2) % 10];
    $user->icon_filename = sprintf('%03d.gif', ($id - 2) % 10 + 1);
  }
}

//-- 仮想投票データをセット --//
RQ::GetTest()->vote = new StdClass();
RQ::GetTest()->vote->day = array();
RQ::GetTest()->vote_target_day = array(
  array('id' =>  2, 'target_no' => 11),
  array('id' =>  3, 'target_no' =>  7),
  //array('id' =>  3, 'target_no' => 10),
  array('id' =>  4, 'target_no' => 11),
  array('id' =>  5, 'target_no' => 25),
  //array('id' =>  6, 'target_no' =>  3),
  //array('id' =>  7, 'target_no' =>  3),
  array('id' =>  7, 'target_no' => 25),
  array('id' =>  8, 'target_no' =>  9),
  array('id' =>  9, 'target_no' =>  3),
  array('id' => 10, 'target_no' =>  3),
  array('id' => 11, 'target_no' =>  3),
  array('id' => 12, 'target_no' =>  3),
  array('id' => 13, 'target_no' =>  3),
  array('id' => 14, 'target_no' =>  3),
  array('id' => 15, 'target_no' =>  7),
  array('id' => 16, 'target_no' => 23),
  //array('id' => 17, 'target_no' => 22),
  array('id' => 18, 'target_no' => 16),
  //array('id' => 18, 'target_no' => 3),
  array('id' => 19, 'target_no' => 22),
  array('id' => 20, 'target_no' => 22),
  array('id' => 21, 'target_no' => 18),
  array('id' => 22, 'target_no' => 25),
  array('id' => 23, 'target_no' => 14),
  array('id' => 24, 'target_no' => 25),
  //array('id' => 25, 'target_no' =>  3),
  array('id' => 25, 'target_no' => 12),
);
//決選投票用
/*
RQ::GetTest()->vote_target_day = array(
  array('id' =>  2, 'target_no' => 4),
  array('id' =>  3, 'target_no' => 5),
  //array('id' =>  3, 'target_no' => 10),
  array('id' =>  4, 'target_no' => 5),
  array('id' =>  5, 'target_no' => 4),
  //array('id' =>  6, 'target_no' =>  3),
  //array('id' =>  7, 'target_no' =>  3),
  array('id' =>  7, 'target_no' => 5),
  //array('id' =>  8, 'target_no' =>  9),
  array('id' =>  9, 'target_no' => 4),
  array('id' => 10, 'target_no' => 4),
  array('id' => 11, 'target_no' => 4),
  array('id' => 12, 'target_no' => 4),
  array('id' => 13, 'target_no' => 4),
  array('id' => 14, 'target_no' => 4),
  array('id' => 15, 'target_no' => 4),
  array('id' => 16, 'target_no' => 4),
  //array('id' => 17, 'target_no' => 22),
  array('id' => 18, 'target_no' => 5),
  //array('id' => 18, 'target_no' => 3),
  array('id' => 19, 'target_no' => 5),
  array('id' => 20, 'target_no' => 5),
  array('id' => 21, 'target_no' => 5),
  array('id' => 22, 'target_no' => 5),
  array('id' => 23, 'target_no' => 5),
  array('id' => 24, 'target_no' => 5),
  //array('id' => 25, 'target_no' =>  3),
  array('id' => 25, 'target_no' => 4),
);
*/
//初日用
/*
RQ::GetTest()->vote->night = array(
  array('user_no' =>  2,	'target_no' =>  1,	'type' => 'WOLF_EAT'),
  array('user_no' =>  4,	'target_no' => 14,	'type' => 'MAGE_DO'),
  array('user_no' =>  5,	'target_no' => 14,	'type' => 'MAGE_DO'),
  #array('user_no' => 11,	'target_no' =>  4,	'type' => 'VOODOO_MAD_DO'),
  #array('user_no' => 13,	'target_no' => 18,	'type' => 'MAGE_DO'),
  array('user_no' => 14,	'target_no' =>  4,	'type' => 'CHILD_FOX_DO'),
  array('user_no' => 16,	'target_no' => '16 18',	'type' => 'CUPID_DO'),
  array('user_no' => 19,	'target_no' => 20,	'type' => 'FAIRY_DO'),
  #array('user_no' => 21,	'target_no' => '18 21',	'type' => 'CUPID_DO'),
  array('user_no' => 21,	'target_no' =>  5,	'type' => 'MANIA_DO'),
  #array('user_no' => 22,	'target_no' => 24,	'type' => 'DUELIST_DO'),
  #array('user_no' => 23,	'target_no' =>  4,	'type' => 'MANIA_DO'),
  #array('user_no' => 23,	'target_no' =>  4,	'type' => 'CHILD_FOX_DO'),
  #array('user_no' => 24,	'target_no' =>  2,	'type' => 'MIND_SCANNER_DO'),
);
*/

RQ::GetTest()->vote->night = array(
  array('user_no' => 2, 	'target_no' => 18,	'type' => 'WOLF_EAT'),
  #array('user_no' => 3, 	'target_no' => 12,	'type' => 'WOLF_EAT'),
  array('user_no' => 4, 	'target_no' => 3,	'type' => 'MAGE_DO'),
  array('user_no' => 5, 	'target_no' => 13,	'type' => 'MAGE_DO'),
  array('user_no' => 7, 	'target_no' => 11,	'type' => 'GUARD_DO'),
  #array('user_no' => 8, 	'target_no' => 18,	'type' => 'GUARD_DO'),
  #array('user_no' => 8, 	'target_no' => 3,	'type' => 'ANTI_VOODOO_DO'),
  array('user_no' => 9, 	'target_no' => 15,	'type' => 'POISON_CAT_DO'),
  #array('user_no' => 9, 	'target_no' => null,	'type' => 'POISON_CAT_NOT_DO'),
  array('user_no' => 10, 	'target_no' => 12,	'type' => 'ASSASSIN_DO'),
  array('user_no' => 10, 	'target_no' => null,	'type' => 'ASSASSIN_NOT_DO'),
  #array('user_no' => 10, 	'target_no' => 12,	'type' => 'DEATH_NOTE_DO'),
  #array('user_no' => 11, 	'target_no' => 16,	'type' => 'JAMMER_MAD_DO'),
  #array('user_no' => 11, 	'target_no' => 4,	'type' => 'VOODOO_FOX_DO'),
  #array('user_no' => 11, 	'target_no' => 4,	'type' => 'VOODOO_MAD_DO'),
  #array('user_no' => 11, 	'type' => 'DREAM_EAT',	'target_no' => 11),
  #array('user_no' => 12, 	'type' => 'TRAP_MAD_DO',	'target_no' => 16),
  #array('user_no' => 12, 	'type' => 'TRAP_MAD_NOT_DO',	'target_no' => null),
  #array('user_no' => 12, 	'type' => 'POSSESSED_DO',	'target_no' => 23),
  #array('user_no' => 12, 	'type' => 'POSSESSED_NOT_DO',	'target_no' => null),
  #array('user_no' => 12, 	'type' => 'ANTI_VOODOO_DO',	'target_no' => 4),
  #array('user_no' => 12, 	'type' => 'MAGE_DO', 'target_no' => 16),
  #array('user_no' => 12, 	'type' => 'WOLF_EAT', 'target_no' => 2),
  #array('user_no' => 12, 	'type' => 'VOODOO_FOX_DO',	'target_no' => 21),
  #array('user_no' => 13, 	'type' => 'POSSESSED_DO',	'target_no' => 23),
  #array('user_no' => 13, 	'type' => 'POSSESSED_NOT_DO',	'target_no' => null),
  #array('user_no' => 13, 	'type' => 'POISON_CAT_DO',	'target_no' => 6),
  #array('user_no' => 13, 	'type' => 'POISON_CAT_NOT_DO',	'target_no' => null),
  #array('user_no' => 13, 	'type' => 'TRAP_MAD_DO',	'target_no' => 13),
  #array('user_no' => 13, 	'type' => 'TRAP_MAD_NOT_DO',	'target_no' => null),
  #array('user_no' => 13, 	'type' => 'VOODOO_KILLER_DO',	'target_no' =>  7),
  #array('user_no' => 14, 	'type' => 'CHILD_FOX_DO',	'target_no' => 18),
  #array('user_no' => 14, 	'type' => 'VOODOO_KILLER_DO',	'target_no' => 10),
  #array('user_no' => 14, 	'type' => 'JAMMER_MAD_DO',	'target_no' => 5),
  #array('user_no' => 17, 	'type' => 'FAIRY_DO', 'target_no' => 22),
  #array('user_no' => 18, 	'type' => 'VOODOO_FOX_DO', 'target_no' => 20),
  array('user_no' => 19, 	'type' => 'FAIRY_DO', 'target_no' => 23),
  array('user_no' => 20, 	'type' => 'VAMPIRE_DO', 'target_no' => 23),
  #array('user_no' => 21, 	'type' => 'CHILD_FOX_DO',	'target_no' => 5),
  #array('user_no' => 22, 	'type' => 'ESCAPE_DO', 'target_no' => 12),
  array('user_no' => 22, 	'type' => 'FAIRY_DO', 'target_no' => 13),
  #array('user_no' => 22, 	'type' => 'TRAP_MAD_DO', 'target_no' => 22),
  #array('user_no' => 22, 	'type' => 'OGRE_DO', 'target_no' => 24),
  #array('user_no' => 22, 	'type' => 'OGRE_NOT_DO', 'target_no' => null),
  #array('user_no' => 22, 	'type' => 'WIZARD_DO', 'target_no' => 23),
  #array('user_no' => 23, 	'type' => 'REPORTER_DO', 'target_no' => 12),
  #array('user_no' => 23, 	'type' => 'ESCAPE_DO', 'target_no' => 11),
  #array('user_no' => 23, 	'type' => 'REPORTER_DO', 'target_no' => 13),
  #array('user_no' => 23, 	'type' => 'ASSASSIN_DO', 'target_no' => 3),
  #array('user_no' => 23, 	'type' => 'MIND_SCANNER_DO', 'target_no' => 24),
  array('user_no' => 23, 	'type' => 'VAMPIRE_DO', 'target_no' => 16),
  #array('user_no' => 24, 	'type' => 'MIND_SCANNER_DO', 'target_no' => 2),
  #array('user_no' => 24, 	'type' => 'WIZARD_DO', 'target_no' => 11),
  array('user_no' => 24, 	'type' => 'SPREAD_WIZARD_DO', 'target_no' => '12 13 18'),
  #array('user_no' => 24, 	'type' => 'SPREAD_WIZARD_DO', 'target_no' => 12),
  #array('user_no' => 25, 	'type' => 'TRAP_MAD_DO', 'target_no' => 22),
  array('user_no' => 25, 	'type' => 'OGRE_DO', 'target_no' => 8),
  #array('user_no' => 25, 	'type' => 'OGRE_NOT_DO', 'target_no' => null),
);

//-- 仮想システムメッセージをセット --//
RQ::GetTest()->winner = 'wolf';
RQ::GetTest()->result_ability = array();
RQ::GetTest()->result_dead    = array();
RQ::GetTest()->system_message = array(
  //-- 仮想イベントをセット --//
  7 => array(#'EVENT'   => array('blinder'),
	     #'VOTE_DUEL' => array(8),
	     #'WEATHER' => array(53),
	     ),
  8 => array('WEATHER' => array(33)
	     )
);
RQ::GetTest()->event = array();

//-- 仮想発言をセット --//
RQ::$get->say = '';
#RQ::$get->say = "占いCO！\n赤は村人！今日は木曜日ですよwww？";
RQ::$get->font_type = 'weak'; 'normal';

//-- データ収集 --//
//DB::Connect(); //DB接続 (必要なときだけ設定する)
DB::$ROOM = new Room(RQ::$get); //村情報を取得
DB::$ROOM->test_mode = true;
DB::$ROOM->log_mode = true;
DB::$ROOM->revote_count = 0;
DB::$ROOM->date = 7;
#DB::$ROOM->scene = 'beforegame';
#DB::$ROOM->scene = 'day';
DB::$ROOM->scene = 'night';
#DB::$ROOM->scene = 'aftergame';
//DB::$ROOM->system_time = Time::Get(); //現在時刻を取得
DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報をロード
if (DB::$ROOM->date == 1) {
  foreach (DB::$USER->rows as $user) $user->live = 'live'; //初日用
}
DB::$USER->ByID(9)->live = 'live';
#DB::$SELF = new User();
DB::$SELF = DB::$USER->ByID(1);
DB::$SELF = DB::$USER->ByID(10);
#DB::$SELF = DB::$USER->TraceExchange(14);

//-- データ出力 --//
$vote_view_mode = false;
if ($vote_view_mode) { //投票表示モード
  $INIT_CONF->LoadFile('vote_message');
  $stack = new RequestGameVote();
  RQ::$get->vote = $stack->vote;
  RQ::$get->target_no = $stack->target_no;
  RQ::$get->situation = $stack->situation;
  RQ::$get->back_url  = '';
  if (RQ::$get->vote) { //投票処理
    HTML::OutputHeader('投票テスト', 'game', true); //HTMLヘッダ
    if (RQ::$get->target_no == 0) { //空投票検出
      HTML::OutputResult('空投票', '投票先を指定してください');
    }
    elseif (DB::$ROOM->IsDay()) { //昼の処刑投票処理
      //Vote::VoteDay();
    }
    elseif (DB::$ROOM->IsNight()) { //夜の投票処理
      Vote::VoteNight();
    }
    else { //ここに来たらロジックエラー
      VoteHTML::OutputError('投票コマンドエラー', '投票先を指定してください');
    }
  }
  else {
    RQ::$get->post_url = 'vote_test.php';
    #DB::$SELF->last_load_scene = DB::$ROOM->scene;

    if (DB::$SELF->IsDead()) {
      DB::$SELF->IsDummyBoy() ? VoteHTML::OutputDummyBoy() : VoteHTML::OutputHeaven();
    }
    else {
      switch(DB::$ROOM->scene) {
      case 'beforegame':
	VoteHTML::OutputBeforeGame();
	break;

      case 'day':
	VoteHTML::OutputDay();
	break;

      case 'night':
	VoteHTML::OutputNight();
	break;

      default: //ここに来たらロジックエラー
	VoteHTML::OutputError('投票シーンエラー');
	break;
      }
    }
  }
  DB::$SELF = DB::$USER->ByID(1);
  GameHTML::OutputPlayer();
  HTML::OutputFooter(true);
}
HTML::OutputHeader('投票テスト', 'game'); //HTMLヘッダ
$talk_view_mode = false;
if ($talk_view_mode) { //発言表示モード
  echo DB::$ROOM->GenerateCSS();
  HTML::OutputBodyHeader();
  $INIT_CONF->LoadFile('talk_class');
  RQ::$get->add_role = false;
  RQ::GetTest()->talk_data = new StdClass();
  //昼の発言
  $stack = array(
    array('uname' => 'moon',
	  'font_type' => 'normal', 'sentence' => '●かー'),
    array('uname' => 'light_blue',
	  'font_type' => 'weak', 'sentence' => 'えっ'),
    array('uname' => 'green',
	  'location' => 'system', 'action' => 'OBJECTION'),
    array('uname' => 'dark_gray',
	  'font_type' => 'weak', 'sentence' => 'チラッ'),
    array('uname' => 'yellow',
	  'font_type' => 'strong', 'sentence' => "占いCO\n黒は●"),
    array('uname' => 'light_gray',
	  'font_type' => 'normal', 'sentence' => 'おはよう'),
    array('uname' => 'system',
	  'location' => 'system', 'action' => 'MORNING', 'sentence' => DB::$ROOM->date),
  );
  foreach ($stack as &$list) {
    $list['scene'] = 'day';
  }
  RQ::GetTest()->talk_data->day = $stack;

  $stack = array(
    array('uname' => 'cloud',
	  'font_type' => 'normal', 'sentence' => '吸血鬼なんだ'),
    array('uname' => 'light_blue',
	  'font_type' => 'weak', 'sentence' => 'えっ'),
    array('uname' => 'moon',
	  'font_type' => 'normal', 'sentence' => 'あーうー'),
    array('uname' => 'gold',
	  'location' => 'common', 'font_type' => 'normal',
	  'sentence' => 'やあやあ'),
    array('uname' => 'rose',
	  'font_type' => 'strong', 'sentence' => '誰吸血しようかな'),
    array('uname' => 'frame',
	  'font_type' => 'normal', 'sentence' => 'どうしよう'),
    array('uname' => 'black',
	  'location' => 'fox', 'font_type' => 'weak',
	  'sentence' => '占い師早く死んで欲しいなぁ'),
    array('uname' => 'cherry',
	  'location' => 'mad', 'font_type' => 'weak',
	  'sentence' => 'やあ'),
    array('uname' => 'green',
	  'font_type' => 'normal', 'sentence' => 'てすてす'),
    array('uname' => 'dark_gray',
	  'font_type' => 'weak', 'sentence' => 'チラッ'),
    array('uname' => 'yellow',
	  'font_type' => 'strong', 'sentence' => "占いCO\n黒は●"),
    array('uname' => 'light_gray',
	  'location' => 'wolf', 'font_type' => 'normal',
	  'sentence' => '生き延びたか'),
    array('uname' => 'system', 'action' => 'NIGHT')
  );
  foreach ($stack as &$list) {
    $list['scene'] = 'night';
    if (! isset($list['location'])) {
      $list['location'] = $list['uname'] == 'system' ? 'system' : 'self_talk';
    }
  }
  RQ::GetTest()->talk_data->night = $stack;

  RQ::GetTest()->talk = array();
  foreach (RQ::GetTest()->talk_data->{DB::$ROOM->scene} as $stack) {
    RQ::GetTest()->talk[] = new TalkParser($stack);
  }
  //PrintData(RQ::GetTest()->talk);
  GameHTML::OutputPlayer();
  if (DB::$SELF->user_no > 0) PlayHTML::OutputAbility();
  Talk::Output();
  HTML::OutputFooter(true);
}
HTML::OutputBodyHeader();
$role_view_mode = false;
if ($role_view_mode) { //画像表示モード
  foreach (array_keys(RoleData::$main_role_list) as $role) Image::Role()->Output($role);
  #foreach (array_keys(RoleData::$sub_role_list)  as $role) Image::Role()->Output($role);
  #foreach (array_keys(RoleData::$main_role_list) as $role) Image::Role()->Output('result_'.$role);
  $header = 'prediction_weather_';
  #foreach (RoleData::$weather_list as $stack) Image::Role()->Output($header.$stack['event']);
  HTML::OutputFooter(true);
}
$cast_view_mode = false;
if ($cast_view_mode) { //配役情報表示モード
  $INIT_CONF->LoadFile('chaos_config');
  //PrintData(Lottery::RateToProbability(ChaosConfig::$chaos_hyper_random_role_list));
  //PrintData(array_sum(ChaosConfig::$chaos_hyper_random_role_list));
  //PrintData(ChaosConfig::$role_group_rate_list);
  echo '<table border="1" cellspacing="0">'."\n".'<tr><th>人口</th>';
  foreach (ChaosConfig::$role_group_rate_list as $group => $rate) {
    $role  = RoleData::DistinguishRoleGroup($group);
    $class = RoleData::DistinguishRoleClass($role);
    echo '<th class="' . $class . '">' . RoleData::$short_role_list[$role] . '</th>';
  }
  echo '</tr>'."\n";
  for($i = 8; $i <= 32; $i++) {
    echo '<tr align="right"><td><strong>' . $i . '</strong></td>';
    foreach (ChaosConfig::$role_group_rate_list as $rate) {
      echo '<td>' . round($i / $rate) . '</td>';
    }
    echo '</tr>'."\n";
  }
  echo '</table>';
  HTML::OutputFooter(true);
}
GameHTML::OutputPlayer();
PlayHTML::OutputAbility();
if (RQ::$get->say != '') { //発言変換テスト
  Play::ConvertSay(RQ::$get->say);
  Play::Talk(RQ::$get->say, 'day', 0);
}
if (DB::$ROOM->IsDay()) { //昼の投票テスト
  $self_id = DB::$SELF->user_no;
  RQ::$get->situation = 'VOTE_KILL';
  RQ::$get->back_url = '';
  foreach (RQ::GetTest()->vote_target_day as $stack) {
    DB::$SELF = DB::$USER->ByID($stack['id']);
    RQ::$get->target_no = $stack['target_no'];
    Vote::VoteDay();
  }
  $vote_message_list = Vote::AggregateDay();
  if (! is_array($vote_message_list)) $vote_message_list = array();
  $stack = array();
  foreach ($vote_message_list as $uname => $vote_data) {
    $vote_data['handle_name'] = DB::$USER->GetHandleName($uname);
    $vote_data['count'] = DB::$ROOM->revote_count + 1;
    $stack[] = $vote_data;
  }
  echo GameHTML::ParseVote($stack, DB::$ROOM->date);
  DB::$ROOM->date++;
  DB::$ROOM->log_mode = false; //イベント確認用
  DB::$ROOM->scene = 'day'; //イベント確認用
  //DB::$ROOM->scene = 'night';
  DB::$SELF = DB::$USER->ByID($self_id);
}
elseif (DB::$ROOM->IsNight()) { // 夜の投票テスト
  //PrintData(RQ::GetTest()->vote->night);
  Vote::AggregateNight();
}
elseif (DB::$ROOM->IsAfterGame()) { //勝敗判定表示
  $INIT_CONF->LoadFile('winner_message');
  DB::$ROOM->log_mode = false;
  DB::$ROOM->personal_mode = false;
  Winner::Output();
  HTML::OutputFooter();
}
//PrintData(RQ::GetTest()->system_message, 'System');
//PrintData(RQ::GetTest()->result_ability, 'Ability');
//PrintData(RQ::GetTest()->result_dead, 'Dead');

do {
  //break;
  foreach (DB::$USER->rows as $user) {
    unset($user->virtual_role);
    $user->live = $user->IsLive(true) ? 'live' : 'dead';
    $user->Reparse();
  }

  foreach (RQ::GetTest()->vote->night as $stack) {
    //$uname = DB::$USER->GetHandleName($stack['uname'], true);
    $uname = DB::$USER->ByVirtual($stack['user_no'])->handle_name;
    switch($stack['type']) {
    case 'CUPID_DO':
      $target_stack = array();
      foreach (explode(' ', $stack['target_no']) as $id) {
	$user = DB::$USER->ByVirtual($id);
	$target_stack[$user->user_no] = $user->handle_name;
      }
      $target_uname = implode(' ', $target_stack);
      break;

    case 'SPREAD_WIZARD_DO':
      $target_stack = array();
      foreach (explode(' ', $stack['target_no']) as $id) {
	$user = DB::$USER->ByVirtual($id);
	$target_stack[$user->user_no] = $user->handle_name;
      }
      ksort($target_stack);
      $target_uname = implode(' ', $target_stack);
      break;

    default:
      //$target_uname = DB::$USER->GetHandleName($stack['target_uname'], true);
      if (isset($stack['target_no'])) {
	$target_uname = DB::$USER->ByVirtual($stack['target_no'])->handle_name;
      }
      break;
    }
    $stack_list[] = array('type' => $stack['type'],
			  'message' =>  $uname . "\t" . $target_uname);
    RQ::GetTest()->ability_action_list = $stack_list;
  }
  GameHTML::OutputAbilityAction();

  //PrintData(RQ::GetTest()->system_message, 'SystemMessage');
  DB::$ROOM->LoadEvent();
  DB::$USER->SetEvent();
  //PrintData(DB::$ROOM->event);
  GameHTML::OutputDead();

  //DB::$ROOM->status = 'finished';
  GameHTML::OutputPlayer();
  PlayHTML::OutputAbility();
  //foreach (array(5, 18, 2, 9, 13, 14, 23) as $id) {
  foreach (range(1, 25) as $id) {
    DB::$SELF = DB::$USER->ByID($id); PlayHTML::OutputAbility();
  }
  //var_dump(DB::$USER->IsOpenCast());
} while(false);
//PrintData(Lottery::RateToProbability(GameConfig::$weather_list));
//InsertLog();
//PrintData(RoleManager::$file);
PrintData(array_keys(RoleManager::$class));
PrintData(DB::$USER->role);
//PrintData($INIT_CONF->loaded->class);
HTML::OutputFooter();
