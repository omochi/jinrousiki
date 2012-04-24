<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

$disable = true; //使用時には false に変更する
if ($disable) {
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

$INIT_CONF->LoadClass('CAST_CONF', 'ICON_CONF');
$INIT_CONF->LoadFile('room_config', 'game_vote_functions', 'user_class');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame');
RQ::$get->room_no = 1;
RQ::GetTest()->test_room = array(
  'id' => RQ::$get->room_no,
  'name' => '配役テスト村',
  'comment' => '',
  'game_option'  => 'dummy_boy real_time:6:4',
  'option_role' => '',
  'date' => 0,
  'scene' => 'day',
  'status' => 'waiting'
);
RQ::GetTest()->is_virtual_room = true;
RQ::$get->vote_times = 1;
RQ::GetTest()->test_users = array();
for($id = 1; $id <= 11; $id++) RQ::GetTest()->test_users[$id] = new User();

RQ::GetTest()->test_users[1]->uname = 'dummy_boy';
RQ::GetTest()->test_users[1]->handle_name = '身代わり君';
RQ::GetTest()->test_users[1]->role = 'mage';
RQ::GetTest()->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
RQ::GetTest()->test_users[1]->color = '#000000';

RQ::GetTest()->test_users[2]->uname = 'light_gray';
RQ::GetTest()->test_users[2]->handle_name = '明灰';
RQ::GetTest()->test_users[2]->role = 'human';

RQ::GetTest()->test_users[3]->uname = 'dark_gray';
RQ::GetTest()->test_users[3]->handle_name = '暗灰';
RQ::GetTest()->test_users[3]->role = 'human';

RQ::GetTest()->test_users[4]->uname = 'yellow';
RQ::GetTest()->test_users[4]->handle_name = '黄色';
RQ::GetTest()->test_users[4]->role = 'human';

RQ::GetTest()->test_users[5]->uname = 'orange';
RQ::GetTest()->test_users[5]->handle_name = 'オレンジ';
RQ::GetTest()->test_users[5]->role = 'human';

RQ::GetTest()->test_users[6]->uname = 'red';
RQ::GetTest()->test_users[6]->handle_name = '赤';
RQ::GetTest()->test_users[6]->role = 'human';

RQ::GetTest()->test_users[7]->uname = 'light_blue';
RQ::GetTest()->test_users[7]->handle_name = '水色';
RQ::GetTest()->test_users[7]->role = 'necromancer';

RQ::GetTest()->test_users[8]->uname = 'blue';
RQ::GetTest()->test_users[8]->handle_name = '青';
RQ::GetTest()->test_users[8]->role = 'guard';

RQ::GetTest()->test_users[9]->uname = 'green';
RQ::GetTest()->test_users[9]->handle_name = '緑';
RQ::GetTest()->test_users[9]->role = 'wolf';

RQ::GetTest()->test_users[10]->uname = 'purple';
RQ::GetTest()->test_users[10]->handle_name = '紫';
RQ::GetTest()->test_users[10]->role = 'wolf';

RQ::GetTest()->test_users[11]->uname = 'cherry';
RQ::GetTest()->test_users[11]->handle_name = 'さくら';
RQ::GetTest()->test_users[11]->role = 'mad';

$icon_color_list = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
			 '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
foreach(RQ::GetTest()->test_users as $id => $user){
  $user->room_no = RQ::$get->room_no;
  $user->user_no = $id;
  $user->sex = $id % 1 == 0 ? 'female' : 'male';
  $user->profile = '';
  $user->live = 'live';
  $user->last_load_scene = 'beforegame';
  if($id > 1){
    $user->color = $icon_color_list[($id - 2) % 10];
    $user->icon_filename = sprintf('%03d.gif', ($id - 2) % 10 + 1);
  }
}
//PrintData(RQ::GetTest()->test_users[22]);

//-- 設定調整 --//
#$CAST_CONF->decide = 11;
#RQ::GetTest()->test_users[3]->live = 'kick';

//-- データ収集 --//
//DB::Connect(); // DB 接続
DB::$ROOM = new Room(RQ::$get); //村情報を取得
DB::$ROOM->test_mode = true;
DB::$ROOM->log_mode  = true;
switch($_GET['scene']){
case 'beforegame':
case 'day':
case 'night':
  DB::$ROOM->scene = $_GET['scene'];
  break;
}
DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報をロード
DB::$SELF = DB::$USER->ByID(1);

//テストデータ設定
DB::$USER->rows[3]->live = 'dead';
DB::$USER->rows[7]->live = 'dead';
DB::$USER->rows[8]->live = 'dead';

if(false){
  switch(intval($_GET['dummy_boy'])){
  case '1':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/normal/dummy_boy/dummy_boy_01.jpg';
    break;

  case '2':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/normal/dummy_boy/dummy_boy_02.gif';
    break;

  case '3':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/normal/dummy_boy/gerd.jpg';
    break;
  }

  $dead_list = array();
  $dead = intval($_GET['dead']);
  if(array_key_exists($dead - 1, $dead_list)){
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/normal/dead/' . $dead_list[$dead];
  }

  $wolf = intval($_GET['wolf']) - 1;
  switch($wolf){
  case '0':
    $ICON_CONF->dead = $ICON_CONF->wolf;
    break;

  case '1':
  case '2':
  case '3':
  case '4':
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/normal/wolf/wolf_0' . $wolf . '.gif';
    break;
  }

  $t_dummy_list = array();
  $t_dummy = is_null($_GET['t_dummy_boy']) ? -1 : intval($_GET['t_dummy_boy']);
  if(array_key_exists($t_dummy, $t_dummy_list)){
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/touhou/dummy_boy/' . $t_dummy_list[$t_dummy];
  }

  $t_wolf_list = array();
  $t_wolf = is_null($_GET['t_wolf']) ? -1 : intval($_GET['t_wolf']);
  if(array_key_exists($t_wolf, $t_wolf_list)){
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/touhou/wolf/' . $t_wolf_list[$t_wolf];
  }

  $t_dead_list = array();
  $t_dead = is_null($_GET['t_dead']) ? -1 : intval($_GET['t_dead']);
  if(array_key_exists($t_dead, $t_dead_list)){
    $ICON_CONF->dead = JINRO_ROOT . '/dev/skin/icon/touhou/dead/' . $t_dead_list[$t_dead];
  }
}

//-- データ出力 --//
OutputHTMLHeader('表示テスト', 'game'); //HTMLヘッダ
echo '<link rel="stylesheet" href="' . JINRO_CSS . '/game_' . DB::$ROOM->scene . '.css">'."\n";
echo '</head><body>'."\n";
//PrintData(DB::$ROOM->scene, $_GET['scene']);
OutputPlayerList(); //プレイヤーリスト
OutputHTMLFooter(true); //HTMLフッタ

//PrintData(DB::$USER->rows[1]);
//PrintData($dead_list);
echo <<<EOF
[昼]：<br>
身代わり君：
<a href="view_test.php?dummy_boy=1">1</a> /
<a href="view_test.php?dummy_boy=2">2</a> /
<a href="view_test.php?dummy_boy=3">3</a><br>
人狼：
<a href="view_test.php?wolf=1">1</a> /
<a href="view_test.php?wolf=2">2</a> /
<a href="view_test.php?wolf=3">3</a> /
<a href="view_test.php?wolf=4">4</a> /
<a href="view_test.php?wolf=5">5</a><br>
死亡：
EOF;

foreach(array_keys($dead_list) as $id){
  echo '<a href="view_test.php?dead=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
身代わり君(東方)：
EOF;
foreach(array_keys($t_dummy_list) as $id){
  echo '<a href="view_test.php?t_dummy_boy=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
人狼(東方)：
EOF;
foreach(array_keys($t_wolf_list) as $id){
  echo '<a href="view_test.php?t_wolf=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
死亡(東方)：
EOF;
foreach(array_keys($t_dead_list) as $id){
  echo '<a href="view_test.php?t_dead=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
<br><br>
[夜]：<br>
身代わり君：
<a href="view_test.php?scene=night&dummy_boy=1">1</a> /
<a href="view_test.php?scene=night&dummy_boy=2">2</a> /
<a href="view_test.php?scene=night&dummy_boy=3">3</a><br>
人狼：
<a href="view_test.php?scene=night&wolf=1">1</a> /
<a href="view_test.php?scene=night&wolf=2">2</a> /
<a href="view_test.php?scene=night&wolf=3">3</a> /
<a href="view_test.php?scene=night&wolf=4">4</a> /
<a href="view_test.php?scene=night&wolf=5">5</a><br>
死亡：
EOF;
foreach(array_keys($dead_list) as $id){
  echo '<a href="view_test.php?scene=night&dead=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
身代わり君(東方)：
EOF;
foreach(array_keys($t_dummy_list) as $id){
  echo '<a href="view_test.php?scene=night&t_dummy_boy=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
人狼(東方)：
EOF;
foreach(array_keys($t_wolf_list) as $id){
  echo '<a href="view_test.php?scene=night&t_wolf=' . $id . '">' . $id . '</a> /'."\n";
}

echo <<<EOF
<br>
死亡(東方)：
EOF;
foreach(array_keys($t_dead_list) as $id){
  echo '<a href="view_test.php?scene=night&t_dead=' . $id . '">' . $id . '</a> /'."\n";
}

OutputHTMLFooter(); //HTMLフッタ
