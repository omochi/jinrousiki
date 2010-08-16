<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'ICON_CONF');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame');
$RQ_ARGS->room_no = 1;
$RQ_ARGS->TestItems->test_room = array(
  'id' => $RQ_ARGS->room_no,
  'name' => '配役テスト村',
  'comment' => '',
  #'game_option'  => 'dummy_boy real_time:6:4',
  'game_option'  => 'dummy_boy wish_role real_time:6:4',
  #'game_option'  => 'dummy_boy wish_role full_mania chaosfull no_sub_role real_time:6:4',
  #'game_option'  => 'dummy_boy wish_role chaosfull real_time:6:4',
  'game_option'  => 'dummy_boy wish_role chaos_hyper real_time:6:4',
  #'game_option'  => 'dummy_boy wish_role real_time:6:4',
  #'option_role' => '',
  #'option_role' => 'decide replace_human',
  #'option_role' => 'chaos_open_cast_role',
  'option_role' => 'chaos_open_cast_camp',
  #'option_role' => 'chaos_open_cast_camp sub_role_limit_easy',
  #'option_role' => 'chaos_open_cast_camp sub_role_limit_normal',
  #'option_role' => 'chaos_open_cast_camp no_sub_role',
  #'option_role' => 'sudden_death',
  'date' => 0,
  'day_night' => 'aftergame',
  'status' => 'waiting'
);
$RQ_ARGS->TestItems->is_virtual_room = true;
$RQ_ARGS->vote_times = 1;
$RQ_ARGS->TestItems->test_users = array();

$RQ_ARGS->TestItems->test_users[1] =& new User();
$RQ_ARGS->TestItems->test_users[1]->user_no = 1;
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->sex = 'male';
$RQ_ARGS->TestItems->test_users[1]->role = '';
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] =& new User();
$RQ_ARGS->TestItems->test_users[2]->user_no = 2;
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->sex = 'male';
$RQ_ARGS->TestItems->test_users[2]->role = 'human';
$RQ_ARGS->TestItems->test_users[2]->icon_filename = '001.gif';
$RQ_ARGS->TestItems->test_users[2]->color = '#DDDDDD';

$RQ_ARGS->TestItems->test_users[3] =& new User();
$RQ_ARGS->TestItems->test_users[3]->user_no = 3;
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->sex = 'male';
$RQ_ARGS->TestItems->test_users[3]->role = 'fox';
$RQ_ARGS->TestItems->test_users[3]->icon_filename = '002.gif';
$RQ_ARGS->TestItems->test_users[3]->color = '#999999';

$RQ_ARGS->TestItems->test_users[4] =& new User();
$RQ_ARGS->TestItems->test_users[4]->user_no = 4;
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->sex = 'female';
$RQ_ARGS->TestItems->test_users[4]->role = 'mage';
$RQ_ARGS->TestItems->test_users[4]->icon_filename = '003.gif';
$RQ_ARGS->TestItems->test_users[4]->color = '#FFD700';

$RQ_ARGS->TestItems->test_users[5] =& new User();
$RQ_ARGS->TestItems->test_users[5]->user_no = 5;
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->sex = 'female';
$RQ_ARGS->TestItems->test_users[5]->role = 'cupid';
$RQ_ARGS->TestItems->test_users[5]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[5]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->user_no = 6;
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->sex = 'male';
$RQ_ARGS->TestItems->test_users[6]->role = 'assassin';
$RQ_ARGS->TestItems->test_users[6]->icon_filename = '005.gif';
$RQ_ARGS->TestItems->test_users[6]->color = '#FF0000';

$RQ_ARGS->TestItems->test_users[7] =& new User();
$RQ_ARGS->TestItems->test_users[7]->user_no = 7;
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->sex = 'male';
$RQ_ARGS->TestItems->test_users[7]->role = 'guard';
$RQ_ARGS->TestItems->test_users[7]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[7]->color = '#99CCFF';

$RQ_ARGS->TestItems->test_users[8] =& new User();
$RQ_ARGS->TestItems->test_users[8]->user_no = 8;
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->sex = 'male';
$RQ_ARGS->TestItems->test_users[8]->profile = '';
$RQ_ARGS->TestItems->test_users[8]->role = 'possessed_wolf';
$RQ_ARGS->TestItems->test_users[8]->icon_filename = '007.gif';
$RQ_ARGS->TestItems->test_users[8]->color = '#0066FF';

$RQ_ARGS->TestItems->test_users[9] =& new User();
$RQ_ARGS->TestItems->test_users[9]->user_no = 9;
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->sex = 'female';
$RQ_ARGS->TestItems->test_users[9]->role = 'mad';
$RQ_ARGS->TestItems->test_users[9]->icon_filename = '008.gif';
$RQ_ARGS->TestItems->test_users[9]->color = '#00EE00';

$RQ_ARGS->TestItems->test_users[10] =& new User();
$RQ_ARGS->TestItems->test_users[10]->user_no = 10;
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->sex = 'female';
$RQ_ARGS->TestItems->test_users[10]->role = 'wolf';
$RQ_ARGS->TestItems->test_users[10]->icon_filename = '009.gif';
$RQ_ARGS->TestItems->test_users[10]->color = '#CC00CC';

$RQ_ARGS->TestItems->test_users[11] =& new User();
$RQ_ARGS->TestItems->test_users[11]->user_no = 11;
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->sex = 'female';
$RQ_ARGS->TestItems->test_users[11]->role = 'fox';
$RQ_ARGS->TestItems->test_users[11]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[11]->color = '#FF9999';

$RQ_ARGS->TestItems->test_users[12] =& new User();
$RQ_ARGS->TestItems->test_users[12]->user_no = 12;
$RQ_ARGS->TestItems->test_users[12]->uname = 'white';
$RQ_ARGS->TestItems->test_users[12]->handle_name = '白';
$RQ_ARGS->TestItems->test_users[12]->sex = 'male';
$RQ_ARGS->TestItems->test_users[12]->role = '';
$RQ_ARGS->TestItems->test_users[12]->icon_filename = '001.gif';
$RQ_ARGS->TestItems->test_users[12]->color = '#DDDDDD';

$RQ_ARGS->TestItems->test_users[13] =& new User();
$RQ_ARGS->TestItems->test_users[13]->user_no = 13;
$RQ_ARGS->TestItems->test_users[13]->uname = 'black';
$RQ_ARGS->TestItems->test_users[13]->handle_name = '黒';
$RQ_ARGS->TestItems->test_users[13]->sex = 'male';
$RQ_ARGS->TestItems->test_users[13]->role = 'wolf';
$RQ_ARGS->TestItems->test_users[13]->icon_filename = '002.gif';
$RQ_ARGS->TestItems->test_users[13]->color = '#999999';

$RQ_ARGS->TestItems->test_users[14] =& new User();
$RQ_ARGS->TestItems->test_users[14]->user_no = 14;
$RQ_ARGS->TestItems->test_users[14]->uname = 'gold';
$RQ_ARGS->TestItems->test_users[14]->handle_name = '金';
$RQ_ARGS->TestItems->test_users[14]->sex = 'female';
$RQ_ARGS->TestItems->test_users[14]->role = 'mage';
$RQ_ARGS->TestItems->test_users[14]->icon_filename = '003.gif';
$RQ_ARGS->TestItems->test_users[14]->color = '#FFD700';

$RQ_ARGS->TestItems->test_users[15] =& new User();
$RQ_ARGS->TestItems->test_users[15]->user_no = 15;
$RQ_ARGS->TestItems->test_users[15]->uname = 'frame';
$RQ_ARGS->TestItems->test_users[15]->handle_name = '炎';
$RQ_ARGS->TestItems->test_users[15]->sex = 'female';
$RQ_ARGS->TestItems->test_users[15]->role = 'mad';
$RQ_ARGS->TestItems->test_users[15]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[15]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[16] =& new User();
$RQ_ARGS->TestItems->test_users[16]->user_no = 16;
$RQ_ARGS->TestItems->test_users[16]->uname = 'scarlet';
$RQ_ARGS->TestItems->test_users[16]->handle_name = '紅';
$RQ_ARGS->TestItems->test_users[16]->sex = 'male';
$RQ_ARGS->TestItems->test_users[16]->role = 'wolf';
$RQ_ARGS->TestItems->test_users[16]->icon_filename = '005.gif';
$RQ_ARGS->TestItems->test_users[16]->color = '#FF0000';

$RQ_ARGS->TestItems->test_users[17] =& new User();
$RQ_ARGS->TestItems->test_users[17]->user_no = 17;
$RQ_ARGS->TestItems->test_users[17]->uname = 'ice';
$RQ_ARGS->TestItems->test_users[17]->handle_name = '氷';
$RQ_ARGS->TestItems->test_users[17]->sex = 'male';
$RQ_ARGS->TestItems->test_users[17]->role = 'medium';
$RQ_ARGS->TestItems->test_users[17]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[17]->color = '#99CCFF';

$RQ_ARGS->TestItems->test_users[18] =& new User();
$RQ_ARGS->TestItems->test_users[18]->user_no = 18;
$RQ_ARGS->TestItems->test_users[18]->uname = 'deep_blue';
$RQ_ARGS->TestItems->test_users[18]->handle_name = '蒼';
$RQ_ARGS->TestItems->test_users[18]->sex = 'male';
$RQ_ARGS->TestItems->test_users[18]->profile = '';
$RQ_ARGS->TestItems->test_users[18]->role = 'guard';
$RQ_ARGS->TestItems->test_users[18]->icon_filename = '007.gif';
$RQ_ARGS->TestItems->test_users[18]->color = '#0066FF';

$RQ_ARGS->TestItems->test_users[19] =& new User();
$RQ_ARGS->TestItems->test_users[19]->user_no = 19;
$RQ_ARGS->TestItems->test_users[19]->uname = 'emerald';
$RQ_ARGS->TestItems->test_users[19]->handle_name = '翠';
$RQ_ARGS->TestItems->test_users[19]->sex = 'female';
$RQ_ARGS->TestItems->test_users[19]->role = 'poison';
$RQ_ARGS->TestItems->test_users[19]->icon_filename = '008.gif';
$RQ_ARGS->TestItems->test_users[19]->color = '#00EE00';

$RQ_ARGS->TestItems->test_users[20] =& new User();
$RQ_ARGS->TestItems->test_users[20]->user_no = 20;
$RQ_ARGS->TestItems->test_users[20]->uname = 'rose';
$RQ_ARGS->TestItems->test_users[20]->handle_name = '薔薇';
$RQ_ARGS->TestItems->test_users[20]->sex = 'female';
$RQ_ARGS->TestItems->test_users[20]->role = 'vampire';
$RQ_ARGS->TestItems->test_users[20]->icon_filename = '009.gif';
$RQ_ARGS->TestItems->test_users[20]->color = '#CC00CC';

$RQ_ARGS->TestItems->test_users[21] =& new User();
$RQ_ARGS->TestItems->test_users[21]->user_no = 21;
$RQ_ARGS->TestItems->test_users[21]->uname = 'peach';
$RQ_ARGS->TestItems->test_users[21]->handle_name = '桃';
$RQ_ARGS->TestItems->test_users[21]->sex = 'female';
$RQ_ARGS->TestItems->test_users[21]->role = 'cupid';
$RQ_ARGS->TestItems->test_users[21]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[21]->color = '#FF9999';

$RQ_ARGS->TestItems->test_users[22] =& new User();
$RQ_ARGS->TestItems->test_users[22]->user_no = 22;
$RQ_ARGS->TestItems->test_users[22]->uname = 'sky';
$RQ_ARGS->TestItems->test_users[22]->handle_name = '空';
$RQ_ARGS->TestItems->test_users[22]->sex = 'female';
$RQ_ARGS->TestItems->test_users[22]->role = '';
$RQ_ARGS->TestItems->test_users[22]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[22]->color = '#99CCFF';

foreach($RQ_ARGS->TestItems->test_users as $user){
  $user->room_no = $RQ_ARGS->room_no;
  $user->prifile = '';
  $user->live = 'live';
  $user->last_load_day_night = 'beforegame';
  $user->is_system = $user->user_no == 1;
}

//-- 設定調整 --//
//$CAST_CONF->decide = 11;
#$RQ_ARGS->TestItems->test_users[3]->live = 'kick';

//-- データ収集 --//
//$DB_CONF->Connect(); // DB 接続
$ROOM =& new Room($RQ_ARGS); //村情報を取得
$ROOM->test_mode = true;
$ROOM->log_mode = true;
$ROOM->day_night = 'beforegame';

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->ByID(1);

//-- データ出力 --//
OutputHTMLHeader('配役テスト', 'game'); //HTMLヘッダ
OutputPlayerList(); //プレイヤーリスト
AggregateVoteGameStart(); //配役処理
$ROOM->date++;
$ROOM->day_night = 'night';
foreach($USERS->rows as $user) $user->ReparseRoles();
OutputPlayerList(); //プレイヤーリスト
OutputHTMLFooter(); //HTMLフッタ
