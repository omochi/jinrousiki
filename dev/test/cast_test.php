<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'ICON_CONF');
$INIT_CONF->LoadFile('game_vote_functions', 'game_play_functions');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame');
$RQ_ARGS->room_no = 461; #94;
$RQ_ARGS->TestItems->test_room = array(
  'id' => $RQ_ARGS->room_no,
  'name' => '【水銀69】やる夫達の真闇鍋村',
  'comment' => 'クイズが苦手なんで鍋でも食べよう',
  #'game_option'  => 'dummy_boy real_time:6:4',
  #'game_option'  => 'dummy_boy wish_role real_time:6:4',
  #'game_option'  => 'dummy_boy wish_role full_mania chaosfull no_sub_role real_time:6:4',
  'game_option'  => 'dummy_boy wish_role chaosfull real_time:6:4',
  #'option_role' => '',
  #'option_role' => 'decide',
  'option_role' => 'chaos_open_cast_camp',
  'date' => 0,
  'day_night' => 'aftergame',
  'status' => 'waiting'
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
$RQ_ARGS->TestItems->test_users[1]->role = '';
$RQ_ARGS->TestItems->test_users[1]->live = 'live';
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
$RQ_ARGS->TestItems->test_users[2]->role = 'human';
$RQ_ARGS->TestItems->test_users[2]->live = 'live';
$RQ_ARGS->TestItems->test_users[2]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[3]->role = 'fox';
$RQ_ARGS->TestItems->test_users[3]->live = 'live';
$RQ_ARGS->TestItems->test_users[3]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[4]->role = 'mage';
$RQ_ARGS->TestItems->test_users[4]->live = 'live';
$RQ_ARGS->TestItems->test_users[4]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[5]->role = 'cupid';
$RQ_ARGS->TestItems->test_users[5]->live = 'live';
$RQ_ARGS->TestItems->test_users[5]->last_load_day_night = 'beforegame';
$RQ_ARGS->TestItems->test_users[5]->is_system = false;
$RQ_ARGS->TestItems->test_users[5]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[5]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[6]->user_no = 6;
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->sex = 'male';
$RQ_ARGS->TestItems->test_users[6]->profile = '';
$RQ_ARGS->TestItems->test_users[6]->role = 'assassin';
$RQ_ARGS->TestItems->test_users[6]->live = 'live';
$RQ_ARGS->TestItems->test_users[6]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[7]->role = 'guard';
$RQ_ARGS->TestItems->test_users[7]->live = 'live';
$RQ_ARGS->TestItems->test_users[7]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[8]->role = 'possessed_wolf';
$RQ_ARGS->TestItems->test_users[8]->live = 'live';
$RQ_ARGS->TestItems->test_users[8]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[9]->role = 'mad';
$RQ_ARGS->TestItems->test_users[9]->live = 'live';
$RQ_ARGS->TestItems->test_users[9]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[10]->role = 'wolf';
$RQ_ARGS->TestItems->test_users[10]->live = 'live';
$RQ_ARGS->TestItems->test_users[10]->last_load_day_night = 'beforegame';
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
$RQ_ARGS->TestItems->test_users[11]->role = 'fox';
$RQ_ARGS->TestItems->test_users[11]->live = 'live';
$RQ_ARGS->TestItems->test_users[11]->last_load_day_night = 'beforegame';
$RQ_ARGS->TestItems->test_users[11]->is_system = false;
$RQ_ARGS->TestItems->test_users[11]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[11]->color = '#FF9999';

//$RQ_ARGS->TestItems->test_users = 30;

//-- 設定調整 --//
$CAST_CONF->decide = 11;

//-- データ収集 --//
$DB_CONF->Connect(); // DB 接続
$ROOM =& new Room($RQ_ARGS); //村情報を取得
$ROOM->test_mode = true;
$ROOM->log_mode = true;
$ROOM->day_night = 'beforegame';
//$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
#$SELF =& new User();
$SELF = $USERS->ByID(1);
#$SELF = $USERS->ByID(7);

//-- データ出力 --//
OutputHTMLHeader($SERVER_CONF->title . '[配役テスト]', 'game'); //HTMLヘッダ
//OutputGameOption($ROOM->game_option, '');
OutputPlayerList(); //プレイヤーリスト
#PrintData($ROOM);
#PrintData($USERS);
#PrintData($SELF);
#PrintData($USERS->ByID(8));
#OutputAbility();
//PrintData($USERS->ByID(5)->GetCamp());

$vote_message_list = AggregateVoteGameStart();
$ROOM->date++;
$ROOM->day_night = 'night';

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
