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
  'game_option'  => 'dummy_boy real_time:6:4',
  'option_role' => '',
  'date' => 0,
  'day_night' => 'beforegame',
  'status' => 'waiting'
);
$RQ_ARGS->TestItems->test_room['game_option'] .= ' wish_role';
#$RQ_ARGS->TestItems->test_room['game_option'] .= ' chaosfull';
$RQ_ARGS->TestItems->test_room['game_option'] .= ' chaos_hyper';
#$RQ_ARGS->TestItems->test_room['game_option'] .= ' deep_sleep';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' gerd';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' poison';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' decide';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' detective';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' joker';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' sudden_death';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' full_mania';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' replace_human';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' chaos_open_cast';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' chaos_open_cast_role';
$RQ_ARGS->TestItems->test_room['option_role'] .= ' chaos_open_cast_camp';
#$RQ_ARGS->TestItems->test_room['option_role'] .= ' sub_role_limit_easy';
$RQ_ARGS->TestItems->is_virtual_room = true;
$RQ_ARGS->vote_times = 1;
$RQ_ARGS->TestItems->test_users = array();

$RQ_ARGS->TestItems->test_users[1] =& new User();
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->role = '';
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] =& new User();
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->role = 'human';

$RQ_ARGS->TestItems->test_users[3] =& new User();
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->role = 'fox';

$RQ_ARGS->TestItems->test_users[4] =& new User();
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->role = 'mage';

$RQ_ARGS->TestItems->test_users[5] =& new User();
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->role = 'cupid';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->role = 'assassin';

$RQ_ARGS->TestItems->test_users[7] =& new User();
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->role = 'guard';

$RQ_ARGS->TestItems->test_users[8] =& new User();
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->role = 'possessed_wolf';

$RQ_ARGS->TestItems->test_users[9] =& new User();
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->role = 'mad';

$RQ_ARGS->TestItems->test_users[10] =& new User();
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->role = 'wolf';

$RQ_ARGS->TestItems->test_users[11] =& new User();
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->role = 'fox';

$RQ_ARGS->TestItems->test_users[12] =& new User();
$RQ_ARGS->TestItems->test_users[12]->uname = 'white';
$RQ_ARGS->TestItems->test_users[12]->handle_name = '白';
$RQ_ARGS->TestItems->test_users[12]->role = '';

$RQ_ARGS->TestItems->test_users[13] =& new User();
$RQ_ARGS->TestItems->test_users[13]->uname = 'black';
$RQ_ARGS->TestItems->test_users[13]->handle_name = '黒';
$RQ_ARGS->TestItems->test_users[13]->role = 'wolf';

$RQ_ARGS->TestItems->test_users[14] =& new User();
$RQ_ARGS->TestItems->test_users[14]->uname = 'gold';
$RQ_ARGS->TestItems->test_users[14]->handle_name = '金';
$RQ_ARGS->TestItems->test_users[14]->role = 'mage';

$RQ_ARGS->TestItems->test_users[15] =& new User();
$RQ_ARGS->TestItems->test_users[15]->uname = 'frame';
$RQ_ARGS->TestItems->test_users[15]->handle_name = '炎';
$RQ_ARGS->TestItems->test_users[15]->role = 'mad';

$RQ_ARGS->TestItems->test_users[16] =& new User();
$RQ_ARGS->TestItems->test_users[16]->uname = 'scarlet';
$RQ_ARGS->TestItems->test_users[16]->handle_name = '紅';
$RQ_ARGS->TestItems->test_users[16]->role = 'wolf';

$RQ_ARGS->TestItems->test_users[17] =& new User();
$RQ_ARGS->TestItems->test_users[17]->uname = 'ice';
$RQ_ARGS->TestItems->test_users[17]->handle_name = '氷';
$RQ_ARGS->TestItems->test_users[17]->role = 'medium';

$RQ_ARGS->TestItems->test_users[18] =& new User();
$RQ_ARGS->TestItems->test_users[18]->uname = 'deep_blue';
$RQ_ARGS->TestItems->test_users[18]->handle_name = '蒼';
$RQ_ARGS->TestItems->test_users[18]->role = 'guard';

$RQ_ARGS->TestItems->test_users[19] =& new User();
$RQ_ARGS->TestItems->test_users[19]->uname = 'emerald';
$RQ_ARGS->TestItems->test_users[19]->handle_name = '翠';
$RQ_ARGS->TestItems->test_users[19]->role = 'poison';

$RQ_ARGS->TestItems->test_users[20] =& new User();
$RQ_ARGS->TestItems->test_users[20]->uname = 'rose';
$RQ_ARGS->TestItems->test_users[20]->handle_name = '薔薇';
$RQ_ARGS->TestItems->test_users[20]->role = 'vampire';

$RQ_ARGS->TestItems->test_users[21] =& new User();
$RQ_ARGS->TestItems->test_users[21]->uname = 'peach';
$RQ_ARGS->TestItems->test_users[21]->handle_name = '桃';
$RQ_ARGS->TestItems->test_users[21]->role = 'ogre';

$RQ_ARGS->TestItems->test_users[22] =& new User();
$RQ_ARGS->TestItems->test_users[22]->uname = 'gust';
$RQ_ARGS->TestItems->test_users[22]->handle_name = '霧';
$RQ_ARGS->TestItems->test_users[22]->role = '';

$icon_color_list = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
			 '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
foreach($RQ_ARGS->TestItems->test_users as $id => $user){
  $user->room_no = $RQ_ARGS->room_no;
  $user->user_no = $id;
  $user->sex = $id % 1 == 0 ? 'female' : 'male';
  $user->profile = '';
  $user->live = 'live';
  $user->last_load_day_night = 'beforegame';
  $user->is_system = $user->user_no == 1;
  if($id > 1){
    $user->color = $icon_color_list[($id - 2) % 10];
    $user->icon_filename = sprintf('%03d.gif', ($id - 2) % 10 + 1);
  }
}
//PrintData($RQ_ARGS->TestItems->test_users[22]);

//-- 設定調整 --//
#$CAST_CONF->decide = 11;
#$RQ_ARGS->TestItems->test_users[3]->live = 'kick';

//-- データ収集 --//
//$DB_CONF->Connect(); // DB 接続
$ROOM =& new Room($RQ_ARGS); //村情報を取得
$ROOM->test_mode = true;
$ROOM->log_mode  = true;
$ROOM->day_night = 'beforegame';

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->ByID(1);

//-- データ出力 --//
OutputHTMLHeader('配役テスト', 'game'); //HTMLヘッダ
echo '</head><body>'."\n";
OutputPlayerList(); //プレイヤーリスト
AggregateVoteGameStart(); //配役処理
$ROOM->date++;
$ROOM->day_night = 'night';
foreach($USERS->rows as $user) $user->ReparseRoles();
OutputPlayerList(); //プレイヤーリスト
OutputHTMLFooter(); //HTMLフッタ
