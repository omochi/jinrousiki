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
  'name' => '投票テスト村',
  'comment' => '',
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
$RQ_ARGS->TestItems->test_users[1]->user_no = 1;
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->sex = 'female';
$RQ_ARGS->TestItems->test_users[1]->role = 'brownie bad_status[2-2]';
$RQ_ARGS->TestItems->test_users[1]->live = 'dead';
$RQ_ARGS->TestItems->test_users[1]->last_load_day_night = NULL;
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] =& new User();
$RQ_ARGS->TestItems->test_users[2]->user_no = 2;
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->sex = 'male';
$RQ_ARGS->TestItems->test_users[2]->role = 'dummy_guard lovers[5] challenge_lovers';
$RQ_ARGS->TestItems->test_users[2]->live = 'live';
$RQ_ARGS->TestItems->test_users[2]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[2]->icon_filename = '001.gif';
$RQ_ARGS->TestItems->test_users[2]->color = '#DDDDDD';

$RQ_ARGS->TestItems->test_users[3] =& new User();
$RQ_ARGS->TestItems->test_users[3]->user_no = 3;
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->sex = 'male';
$RQ_ARGS->TestItems->test_users[3]->role = 'soul_mania[11]';
$RQ_ARGS->TestItems->test_users[3]->live = 'live';
$RQ_ARGS->TestItems->test_users[3]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[3]->icon_filename = '002.gif';
$RQ_ARGS->TestItems->test_users[3]->color = '#999999';

$RQ_ARGS->TestItems->test_users[4] =& new User();
$RQ_ARGS->TestItems->test_users[4]->user_no = 4;
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->sex = 'female';
$RQ_ARGS->TestItems->test_users[4]->role = 'mage authority';
$RQ_ARGS->TestItems->test_users[4]->live = 'live';
$RQ_ARGS->TestItems->test_users[4]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[4]->icon_filename = '003.gif';
$RQ_ARGS->TestItems->test_users[4]->color = '#FFD700';

$RQ_ARGS->TestItems->test_users[5] =& new User();
$RQ_ARGS->TestItems->test_users[5]->user_no = 5;
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->sex = 'female';
$RQ_ARGS->TestItems->test_users[5]->role = 'moon_cupid lovers[5] mind_receiver[2] challenge_lovers';
$RQ_ARGS->TestItems->test_users[5]->live = 'live';
$RQ_ARGS->TestItems->test_users[5]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[5]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[5]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->user_no = 6;
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->sex = 'female';
$RQ_ARGS->TestItems->test_users[6]->role = 'doll_master';
$RQ_ARGS->TestItems->test_users[6]->live = 'live';
$RQ_ARGS->TestItems->test_users[6]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[6]->icon_filename = '005.gif';
$RQ_ARGS->TestItems->test_users[6]->color = '#FF0000';

$RQ_ARGS->TestItems->test_users[7] =& new User();
$RQ_ARGS->TestItems->test_users[7]->user_no = 7;
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->sex = 'male';
$RQ_ARGS->TestItems->test_users[7]->role = 'reporter';
$RQ_ARGS->TestItems->test_users[7]->live = 'live';
$RQ_ARGS->TestItems->test_users[7]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[7]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[7]->color = '#99CCFF';

$RQ_ARGS->TestItems->test_users[8] =& new User();
$RQ_ARGS->TestItems->test_users[8]->user_no = 8;
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->sex = 'male';
$RQ_ARGS->TestItems->test_users[8]->role = 'possessed_wolf decide possessed_target[2-9]';
$RQ_ARGS->TestItems->test_users[8]->live = 'live';
$RQ_ARGS->TestItems->test_users[8]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[8]->icon_filename = '007.gif';
$RQ_ARGS->TestItems->test_users[8]->color = '#0066FF';

$RQ_ARGS->TestItems->test_users[9] =& new User();
$RQ_ARGS->TestItems->test_users[9]->user_no = 9;
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->sex = 'female';
$RQ_ARGS->TestItems->test_users[9]->role = 'executor possessed[2-8]';
$RQ_ARGS->TestItems->test_users[9]->live = 'drop';
$RQ_ARGS->TestItems->test_users[9]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[9]->icon_filename = '008.gif';
$RQ_ARGS->TestItems->test_users[9]->color = '#00EE00';

$RQ_ARGS->TestItems->test_users[10] =& new User();
$RQ_ARGS->TestItems->test_users[10]->user_no = 10;
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->sex = 'female';
$RQ_ARGS->TestItems->test_users[10]->role = 'possessed_wolf';
$RQ_ARGS->TestItems->test_users[10]->live = 'live';
$RQ_ARGS->TestItems->test_users[10]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[10]->icon_filename = '009.gif';
$RQ_ARGS->TestItems->test_users[10]->color = '#CC00CC';

$RQ_ARGS->TestItems->test_users[11] =& new User();
$RQ_ARGS->TestItems->test_users[11]->user_no = 11;
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->sex = 'female';
$RQ_ARGS->TestItems->test_users[11]->role = 'common';
$RQ_ARGS->TestItems->test_users[11]->live = 'live';
$RQ_ARGS->TestItems->test_users[11]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[11]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[11]->color = '#FF9999';

//$RQ_ARGS->TestItems->test_users = 30;
foreach($RQ_ARGS->TestItems->test_users as $user){
  $user->room_no = $RQ_ARGS->room_no;
  $user->prifile = '';
  $user->is_system = $user->user_no == 1;
}

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
  array('uname' => 'light_gray', 'situation' => 'GUARD_DO', 'target_uname' => 'red'),
  #array('uname' => 'light_gray', 'situation' => 'DREAM_EAT', 'target_uname' => 'red'),
  #array('uname' => 'light_gray', 'situation' => 'POISON_CAT_DO', 'target_uname' => 'blue'),
  #array('uname' => 'light_gray', 'situation' => 'POISON_CAT_NOT_DO', 'target_uname' => NULL),
  #array('uname' => 'light_gray', 'situation' => 'FAIRY_DO', 'target_uname' => 'dummy_boy'),
  #array('uname' => 'light_gray', 'situation' => 'FAIRY_DO', 'target_uname' => 'blue'),
  #array('uname' => 'light_gray', 'situation' => 'FAIRY_DO', 'target_uname' => 'light_blue orange'),
  #array('uname' => 'light_gray', 'situation' => 'MANIA_DO', 'target_uname' => 'red'),
  #array('uname' => 'light_gray', 'situation' => 'POSSESSED_DO', 'target_uname' => 'light_blue'),
  #array('uname' => 'light_gray', 'situation' => 'POSSESSED_NOT_DO', 'target_uname' => NULL),
  #array('uname' => 'dark_gray', 'situation' => 'ESCAPE_DO',		'target_uname' => 'cherry'),
  #array('uname' => 'dark_gray', 'situation' => 'GUARD_DO',		'target_uname' => 'light_gray'),
  #array('uname' => 'dark_gray', 'situation' => 'JAMMER_MAD_DO',	'target_uname' => 'light_gray'),
  #array('uname' => 'dark_gray', 'situation' => 'VOODOO_MAD_DO',	'target_uname' => 'yellow'),
  #array('uname' => 'dark_gray', 'situation' => 'DREAM_EAT',		'target_uname' => 'yellow'),
  #array('uname' => 'dark_gray', 'situation' => 'TRAP_MAD_DO',		'target_uname' => 'dark_gray'),
  #array('uname' => 'dark_gray', 'situation' => 'TRAP_MAD_DO',		'target_uname' => 'red'),
  #array('uname' => 'dark_gray', 'situation' => 'TRAP_MAD_NOT_DO',	'target_uname' => NULL),
  #array('uname' => 'dark_gray', 'situation' => 'VAMPIRE_DO',		'target_uname' => 'cherry'),
  #array('uname' => 'dark_gray', 'situation' => 'FAIRY_DO',		'target_uname' => 'cherry'),
  #array('uname' => 'dark_gray', 'situation' => 'MANIA_DO',		'target_uname' => 'light_gray'),
  #array('uname' => 'yellow', 'situation' => 'MAGE_DO', 'target_uname' => 'dark_gray'),
  array('uname' => 'yellow', 'situation' => 'MAGE_DO', 'target_uname' => 'light_blue'),
  #array('uname' => 'yellow', 'situation' => 'MAGE_DO', 'target_uname' => 'blue'),
  #array('uname' => 'yellow', 'situation' => 'MAGE_DO', 'target_uname' => 'purple'),
  #array('uname' => 'yellow', 'situation' => 'MAGE_DO', 'target_uname' => 'cherry'),
  #array('uname' => 'yellow', 'situation' => 'VOODOO_KILLER_DO', 'target_uname' => 'cherry'),
  #array('uname' => 'orange',  'target_uname' => 'red blue',  'situation' => 'CUPID_DO'),
  #array('uname' => 'orange',  'target_uname' => 'orange purple',  'situation' => 'CUPID_DO'),
  #array('uname' => 'orange',     'target_uname' => 'yellow',     'situation' => 'JAMMER_MAD_DO'),
  #array('uname' => 'orange',     'target_uname' => 'green',     'situation' => 'WOLF_EAT'),
  #array('uname' => 'orange',  'target_uname' => 'purple',  'situation' => 'MANIA_DO'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'light_gray'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'dark_gray'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'orange'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'blue'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'purple'),
  #array('uname' => 'red', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'cherry'),
  array('uname' => 'red', 'situation' => 'ASSASSIN_NOT_DO', 'target_uname' => NULL),
  #array('uname' => 'light_blue', 'situation' => 'GUARD_DO',		'target_uname' => 'dark_gray'),
  #array('uname' => 'light_blue', 'situation' => 'GUARD_DO',		'target_uname' => 'yellow'),
  #array('uname' => 'light_blue', 'situation' => 'GUARD_DO',		'target_uname' => 'red'),
  #array('uname' => 'light_blue', 'situation' => 'GUARD_DO',		'target_uname' => 'cherry'),
  #array('uname' => 'light_blue', 'situation' => 'REPORTER_DO',		'target_uname' => 'red'),
  array('uname' => 'light_blue', 'situation' => 'REPORTER_DO',		'target_uname' => 'cherry'),
  #array('uname' => 'light_blue', 'situation' => 'ANTI_VOODOO_DO',	'target_uname' => 'yellow'),
  #array('uname' => 'light_blue', 'situation' => 'ANTI_VOODOO_DO',	'target_uname' => 'red'),
  #array('uname' => 'light_blue', 'situation' => 'MIND_SCANNER_DO',	'target_uname' => 'cherry'),
  #array('uname' => 'light_blue', 'situation' => 'CUPID_DO', 'target_uname' => 'light_blue orange'),
  #array('uname' => 'blue', 'situation' => 'WOLF_EAT', 'target_uname' => 'dummy_boy'),
  #array('uname' => 'blue', 'situation' => 'WOLF_EAT', 'target_uname' => 'light_gray'),
  #array('uname' => 'blue', 'situation' => 'WOLF_EAT', 'target_uname' => 'dark_gray'),
  #array('uname' => 'blue', 'situation' => 'WOLF_EAT', 'target_uname' => 'yellow'),
  #array('uname' => 'blue', 'situation' => 'WOLF_EAT', 'target_uname' => 'cherry'),
  #array('uname' => 'green', 'situation' => 'VOODOO_FOX_DO', 'target_uname' => 'blue'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'dummy_boy'),
  array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'dark_gray'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'yellow'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'orange'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'red'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'light_blue'),
  #array('uname' => 'purple', 'situation' => 'WOLF_EAT', 'target_uname' => 'cherry'),
  #array('uname' => 'cherry', 'situation' => 'VOODOO_FOX_DO', 'target_uname' => 'light_blue')
  #array('uname' => 'cherry', 'situation' => 'CHILD_FOX_DO', 'target_uname' => 'purple')
  #array('uname' => 'cherry', 'situation' => 'JAMMER_MAD_DO', 'target_uname' => 'yellow')
  #array('uname' => 'cherry', 'situation' => 'POISON_CAT_DO', 'target_uname' => 'blue')
  #array('uname' => 'cherry', 'situation' => 'POISON_CAT_DO', 'target_uname' => 'dark_gray')
  #array('uname' => 'cherry', 'situation' => 'POISON_CAT_NOT_DO', 'target_uname' => NULL)
  #array('uname' => 'cherry', 'situation' => 'POSSESSED_DO', 'target_uname' => 'dark_gray')
  #array('uname' => 'cherry', 'situation' => 'POSSESSED_NOT_DO', 'target_uname' => NULL)
  #array('uname' => 'cherry', 'situation' => 'ASSASSIN_DO', 'target_uname' => 'dark_gray')
  #array('uname' => 'cherry', 'situation' => 'ASSASSIN_NOT_DO', 'target_uname' => NULL)
);

//-- 仮想システムメッセージをセット --//
$RQ_ARGS->TestItems->system_message = array();

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
#$SELF = $USERS->ByID(10);
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
OutputAbility();

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
//PrintData($RQ_ARGS->TestItems->system_message);

$view_after = true;
if($view_after){
  foreach($USERS->rows as $user){
    $user->live = $user->IsLive(true) ? 'live' : 'dead';
    if($user->updated['role']) $user->ParseRoles($user->updated['role']);
  }
  foreach($RQ_ARGS->TestItems->system_message as $date => $date_list){
    foreach($date_list as $type => $type_list){
      switch($type){
      case 'FOX_EAT':
	continue 2;
      }
      foreach($type_list as $handle_name){
	if(is_null($USERS->HandleNameToUname($handle_name))) continue;
	OutputDeadManType($handle_name, $type);
      }
    }
  }
  OutputPlayerList(); //プレイヤーリスト
  OutputAbility();
}
OutputHTMLFooter(); //HTMLフッタ
