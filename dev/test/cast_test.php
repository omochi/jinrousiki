<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'ICON_CONF', 'ROLES', 'ROOM_OPT');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');

//-- 仮想村データをセット --//
$INIT_CONF->LoadRequest('RequestBaseGame');
RQ::$get->room_no = 1;
RQ::$get->TestItems = new StdClass();
RQ::GetTest()->test_room = array(
  'id' => RQ::$get->room_no, 'name' => '配役テスト村', 'comment' => '',
  'game_option' => 'dummy_boy real_time:6:4 wish_role',
  'option_role' => '',
  'date' => 0, 'scene' => 'beforegame', 'status' => 'waiting'
);
#RQ::AddTestRoom('game_option', 'quiz');
#RQ::AddTestRoom('game_option', 'chaosfull');
RQ::AddTestRoom('game_option', 'chaos_hyper');
#RQ::AddTestRoom('game_option', 'blinder');
#RQ::AddTestRoom('option_role', 'gerd');
#RQ::AddTestRoom('option_role', 'poison cupid medium mania');
RQ::AddTestRoom('option_role', 'decide');
#RQ::AddTestRoom('option_role', 'detective');
RQ::AddTestRoom('option_role', 'joker');
#RQ::AddTestRoom('option_role', 'gentleman');
#RQ::AddTestRoom('option_role', 'sudden_death');
#RQ::AddTestRoom('option_role', 'replace_human');
#RQ::AddTestRoom('option_role', 'full_mania');
RQ::AddTestRoom('option_role', 'chaos_open_cast');
#RQ::AddTestRoom('option_role', 'chaos_open_cast_role');
#RQ::AddTestRoom('option_role', 'chaos_open_cast_camp');
#RQ::AddTestRoom('option_role', 'sub_role_limit_easy');
#RQ::AddTestRoom('option_role', 'sub_role_limit_normal');
#RQ::AddTestRoom('option_role', 'sub_role_limit_hard');
RQ::GetTest()->is_virtual_room = true;
RQ::$get->vote_times = 1;
RQ::GetTest()->test_users = array();
for ($id = 1; $id <= 22; $id++) RQ::GetTest()->test_users[$id] = new User();

RQ::GetTest()->test_users[1]->uname = 'dummy_boy';
RQ::GetTest()->test_users[1]->handle_name = '身代わり君';
RQ::GetTest()->test_users[1]->role = '';
RQ::GetTest()->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
RQ::GetTest()->test_users[1]->color = '#000000';

RQ::GetTest()->test_users[2]->uname = 'light_gray';
RQ::GetTest()->test_users[2]->handle_name = '明灰';
RQ::GetTest()->test_users[2]->role = 'human';

RQ::GetTest()->test_users[3]->uname = 'dark_gray';
RQ::GetTest()->test_users[3]->handle_name = '暗灰';
RQ::GetTest()->test_users[3]->role = 'fox';

RQ::GetTest()->test_users[4]->uname = 'yellow';
RQ::GetTest()->test_users[4]->handle_name = '黄色';
RQ::GetTest()->test_users[4]->role = 'mage';

RQ::GetTest()->test_users[5]->uname = 'orange';
RQ::GetTest()->test_users[5]->handle_name = 'オレンジ';
RQ::GetTest()->test_users[5]->role = 'cupid';

RQ::GetTest()->test_users[6]->uname = 'red';
RQ::GetTest()->test_users[6]->handle_name = '赤';
RQ::GetTest()->test_users[6]->role = 'assassin';

RQ::GetTest()->test_users[7]->uname = 'light_blue';
RQ::GetTest()->test_users[7]->handle_name = '水色';
RQ::GetTest()->test_users[7]->role = 'guard';

RQ::GetTest()->test_users[8]->uname = 'blue';
RQ::GetTest()->test_users[8]->handle_name = '青';
RQ::GetTest()->test_users[8]->role = 'possessed_wolf';

RQ::GetTest()->test_users[9]->uname = 'green';
RQ::GetTest()->test_users[9]->handle_name = '緑';
RQ::GetTest()->test_users[9]->role = 'mad';

RQ::GetTest()->test_users[10]->uname = 'purple';
RQ::GetTest()->test_users[10]->handle_name = '紫';
RQ::GetTest()->test_users[10]->role = 'duelist';

RQ::GetTest()->test_users[11]->uname = 'cherry';
RQ::GetTest()->test_users[11]->handle_name = 'さくら';
RQ::GetTest()->test_users[11]->role = 'fox';

RQ::GetTest()->test_users[12]->uname = 'white';
RQ::GetTest()->test_users[12]->handle_name = '白';
RQ::GetTest()->test_users[12]->role = '';

RQ::GetTest()->test_users[13]->uname = 'black';
RQ::GetTest()->test_users[13]->handle_name = '黒';
RQ::GetTest()->test_users[13]->role = 'wizard';

RQ::GetTest()->test_users[14]->uname = 'gold';
RQ::GetTest()->test_users[14]->handle_name = '金';
RQ::GetTest()->test_users[14]->role = 'mage';

RQ::GetTest()->test_users[15]->uname = 'frame';
RQ::GetTest()->test_users[15]->handle_name = '炎';
RQ::GetTest()->test_users[15]->role = 'mad';

RQ::GetTest()->test_users[16]->uname = 'scarlet';
RQ::GetTest()->test_users[16]->handle_name = '紅';
RQ::GetTest()->test_users[16]->role = 'wolf';

RQ::GetTest()->test_users[17]->uname = 'ice';
RQ::GetTest()->test_users[17]->handle_name = '氷';
RQ::GetTest()->test_users[17]->role = 'medium';

RQ::GetTest()->test_users[18]->uname = 'deep_blue';
RQ::GetTest()->test_users[18]->handle_name = '蒼';
RQ::GetTest()->test_users[18]->role = 'guard';

RQ::GetTest()->test_users[19]->uname = 'emerald';
RQ::GetTest()->test_users[19]->handle_name = '翠';
RQ::GetTest()->test_users[19]->role = 'poison';

RQ::GetTest()->test_users[20]->uname = 'rose';
RQ::GetTest()->test_users[20]->handle_name = '薔薇';
RQ::GetTest()->test_users[20]->role = 'vampire';

RQ::GetTest()->test_users[21]->uname = 'peach';
RQ::GetTest()->test_users[21]->handle_name = '桃';
RQ::GetTest()->test_users[21]->role = 'ogre';

RQ::GetTest()->test_users[22]->uname = 'gust';
RQ::GetTest()->test_users[22]->handle_name = '霧';
RQ::GetTest()->test_users[22]->role = '';

$icon_color_list = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
			 '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
foreach (RQ::GetTest()->test_users as $id => $user) {
  $user->room_no = RQ::$get->room_no;
  $user->user_no = $id;
  $user->sex = $id % 2 == 0 ? 'female' : 'male';
  $user->profile = '';
  $user->live = 'live';
  $user->last_load_scene = 'beforegame';
  if ($id > 1) {
    $user->color = $icon_color_list[($id - 2) % 10];
    $user->icon_filename = sprintf('%03d.gif', ($id - 2) % 10 + 1);
  }
}
//PrintData(RQ::GetTest()->test_users[22]);

//-- 設定調整 --//
#$CAST_CONF->decide = 11;
#RQ::GetTest()->test_users[3]->live = 'kick';

//-- データ収集 --//
//DB::Connect(); //DB接続 (必要なときだけ設定する)
DB::$ROOM = new Room(RQ::$get); //村情報を取得
DB::$ROOM->test_mode = true;
DB::$ROOM->log_mode  = true;
DB::$ROOM->scene = 'beforegame';
DB::$ROOM->vote = array();

DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報をロード
DB::$SELF = DB::$USER->ByID(1);

//-- データ出力 --//
OutputHTMLHeader('配役テスト', 'game'); //HTMLヘッダ
echo '</head><body>'."\n";

OutputPlayerList(); //プレイヤーリスト
AggregateVoteGameStart(); //配役処理
DB::$ROOM->date++;
DB::$ROOM->scene = 'night';
foreach (DB::$USER->rows as $user) $user->ReparseRoles();
OutputPlayerList(); //プレイヤーリスト
OutputHTMLFooter(); //HTMLフッタ
