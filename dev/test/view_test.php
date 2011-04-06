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
  'day_night' => 'day',
  'status' => 'waiting'
);
$RQ_ARGS->TestItems->is_virtual_room = true;
$RQ_ARGS->vote_times = 1;
$RQ_ARGS->TestItems->test_users = array();

$RQ_ARGS->TestItems->test_users[1] =& new User();
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->role = 'mage';
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] =& new User();
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->role = 'human';

$RQ_ARGS->TestItems->test_users[3] =& new User();
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->role = 'human';

$RQ_ARGS->TestItems->test_users[4] =& new User();
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->role = 'human';

$RQ_ARGS->TestItems->test_users[5] =& new User();
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->role = 'human';

$RQ_ARGS->TestItems->test_users[6] =& new User();
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->role = 'human';

$RQ_ARGS->TestItems->test_users[7] =& new User();
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->role = 'necromancer';

$RQ_ARGS->TestItems->test_users[8] =& new User();
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->role = 'guard';

$RQ_ARGS->TestItems->test_users[9] =& new User();
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->role = 'wolf';

$RQ_ARGS->TestItems->test_users[10] =& new User();
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->role = 'wolf';

$RQ_ARGS->TestItems->test_users[11] =& new User();
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->role = 'mad';

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
switch($_GET['day_night']){
case 'beforegame':
case 'day':
case 'night':
  $ROOM->day_night = $_GET['day_night'];
  break;
}
$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->ByID(1);

//テストデータ設定
$USERS->rows[9]->live = 'dead';
$USERS->rows[9]->color = '#000000';

if(false){
  switch(intval($_GET['dummy_boy'])){
  case '1':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/dummy_boy/dummy_boy_01.jpg';
    break;

  case '2':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/dummy_boy/dummy_boy_02.gif';
    break;

  case '3':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/dummy_boy/gerd.jpg';
    break;
  }

  switch(intval($_GET['dead'])){
  case '1':
  case '4':
  case '5':
  case '6':
  case '7':
  case '8':
  case '10':
  case '11':
  case '12':
  case '13':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/dead/dead_' .
      sprintf('%02d', intval($_GET['dead'])) . '.gif';
    break;

  case '2':
  case '3':
  case '9':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/dead/dead_0' . intval($_GET['dead']) . '.jpg';
    break;
  }

  switch(intval($_GET['wolf'])){
  case '1':
    $ICON_CONF->dead = $ICON_CONF->wolf;
    break;

  case '2':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/wolf/wolf_01.gif';
    break;

  case '3':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/wolf/wolf_02.gif';
    break;

  case '4':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/img/wolf/wolf_03.gif';
    break;
  }
}

//-- データ出力 --//
OutputHTMLHeader('表示テスト', 'game'); //HTMLヘッダ
echo '<link rel="stylesheet" href="' . JINRO_CSS . '/game_' . $ROOM->day_night . '.css">'."\n";
echo '</head><body>'."\n";
OutputPlayerList(); //プレイヤーリスト
OutputHTMLFooter(); //HTMLフッタ
