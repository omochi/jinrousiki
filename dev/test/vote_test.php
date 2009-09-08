<?php
require_once(dirname(__FILE__) . '/../../include/game_functions.php');
require_once(dirname(__FILE__) . '/../../include/game_vote_functions.php');
require_once(dirname(__FILE__) . '/../../include/request_class.php');

//引数を取得
$RQ_ARGS = new RequestGameView();
$RQ_ARGS->room_no = 94;
#$RQ_ARGS->room_no = 456;
$RQ_ARGS->TestItems->test_room = array(
  'room_name'    => '【水銀69】やる夫達の真闇鍋村',
  'room_comment' => 'クイズが苦手なんで鍋でも食べよう',
  'game_option'  => 'dummy_boy full_mania chaosfull chaos_open_cast no_sub_role real_time:6:4 not_open_cast',
  'date'         => 9,
  'day_night'    => 'aftergame',
  'status'       => 'finished');
$RQ_ARGS->TestItems->is_virtual_room = true;

$RQ_ARGS->TestItems->test_users[1] = new User();
$RQ_ARGS->TestItems->test_users[1]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[1]->user_no = 1;
$RQ_ARGS->TestItems->test_users[1]->uname = 'dummy_boy';
$RQ_ARGS->TestItems->test_users[1]->handle_name = '身代わり君';
$RQ_ARGS->TestItems->test_users[1]->sex = 'female';
$RQ_ARGS->TestItems->test_users[1]->profile = '僕はおいしくないよ';
$RQ_ARGS->TestItems->test_users[1]->role = 'human';
$RQ_ARGS->TestItems->test_users[1]->live = 'dead';
$RQ_ARGS->TestItems->test_users[1]->last_load_day_night = NULL;
$RQ_ARGS->TestItems->test_users[1]->is_system = true;
$RQ_ARGS->TestItems->test_users[1]->icon_filename = '../img/dummy_boy_user_icon.jpg';
$RQ_ARGS->TestItems->test_users[1]->color = '#000000';

$RQ_ARGS->TestItems->test_users[2] = new User();
$RQ_ARGS->TestItems->test_users[2]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[2]->user_no = 2;
$RQ_ARGS->TestItems->test_users[2]->uname = 'light_gray';
$RQ_ARGS->TestItems->test_users[2]->handle_name = '明灰';
$RQ_ARGS->TestItems->test_users[2]->sex = 'male';
$RQ_ARGS->TestItems->test_users[2]->profile = '';
$RQ_ARGS->TestItems->test_users[2]->role = 'human lovers[4]';
$RQ_ARGS->TestItems->test_users[2]->live = 'dead';
$RQ_ARGS->TestItems->test_users[2]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[2]->is_system = false;
$RQ_ARGS->TestItems->test_users[2]->icon_filename = '001.gif';
$RQ_ARGS->TestItems->test_users[2]->color = '#DDDDDD';

$RQ_ARGS->TestItems->test_users[3] = new User();
$RQ_ARGS->TestItems->test_users[3]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[3]->user_no = 3;
$RQ_ARGS->TestItems->test_users[3]->uname = 'dark_gray';
$RQ_ARGS->TestItems->test_users[3]->handle_name = '暗灰';
$RQ_ARGS->TestItems->test_users[3]->sex = 'male';
$RQ_ARGS->TestItems->test_users[3]->profile = '';
$RQ_ARGS->TestItems->test_users[3]->role = 'fox authority';
$RQ_ARGS->TestItems->test_users[3]->live = 'live';
$RQ_ARGS->TestItems->test_users[3]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[3]->is_system = false;
$RQ_ARGS->TestItems->test_users[3]->icon_filename = '002.gif';
$RQ_ARGS->TestItems->test_users[3]->color = '#999999';

$RQ_ARGS->TestItems->test_users[4] = new User();
$RQ_ARGS->TestItems->test_users[4]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[4]->user_no = 4;
$RQ_ARGS->TestItems->test_users[4]->uname = 'yellow';
$RQ_ARGS->TestItems->test_users[4]->handle_name = '黄色';
$RQ_ARGS->TestItems->test_users[4]->sex = 'female';
$RQ_ARGS->TestItems->test_users[4]->profile = '';
$RQ_ARGS->TestItems->test_users[4]->role = 'cupid rabbit lovers[4]';
$RQ_ARGS->TestItems->test_users[4]->live = 'dead';
$RQ_ARGS->TestItems->test_users[4]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[4]->is_system = false;
$RQ_ARGS->TestItems->test_users[4]->icon_filename = '003.gif';
$RQ_ARGS->TestItems->test_users[4]->color = '#FFD700';

$RQ_ARGS->TestItems->test_users[5] = new User();
$RQ_ARGS->TestItems->test_users[5]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[5]->user_no = 5;
$RQ_ARGS->TestItems->test_users[5]->uname = 'orange';
$RQ_ARGS->TestItems->test_users[5]->handle_name = 'オレンジ';
$RQ_ARGS->TestItems->test_users[5]->sex = 'female';
$RQ_ARGS->TestItems->test_users[5]->profile = '';
$RQ_ARGS->TestItems->test_users[5]->role = 'boss_wolf decide';
$RQ_ARGS->TestItems->test_users[5]->live = 'live';
$RQ_ARGS->TestItems->test_users[5]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[5]->is_system = false;
$RQ_ARGS->TestItems->test_users[5]->icon_filename = '004.gif';
$RQ_ARGS->TestItems->test_users[5]->color = '#FF9900';

$RQ_ARGS->TestItems->test_users[6] = new User();
$RQ_ARGS->TestItems->test_users[6]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[6]->user_no = 6;
$RQ_ARGS->TestItems->test_users[6]->uname = 'red';
$RQ_ARGS->TestItems->test_users[6]->handle_name = '赤';
$RQ_ARGS->TestItems->test_users[6]->sex = 'male';
$RQ_ARGS->TestItems->test_users[6]->profile = '';
$RQ_ARGS->TestItems->test_users[6]->role = 'necromancer upper_luck';
$RQ_ARGS->TestItems->test_users[6]->live = 'live';
$RQ_ARGS->TestItems->test_users[6]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[6]->is_system = false;
$RQ_ARGS->TestItems->test_users[6]->icon_filename = '005.gif';
$RQ_ARGS->TestItems->test_users[6]->color = '#FF0000';

$RQ_ARGS->TestItems->test_users[7] = new User();
$RQ_ARGS->TestItems->test_users[7]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[7]->user_no = 7;
$RQ_ARGS->TestItems->test_users[7]->uname = 'light_blue';
$RQ_ARGS->TestItems->test_users[7]->handle_name = '水色';
$RQ_ARGS->TestItems->test_users[7]->sex = 'male';
$RQ_ARGS->TestItems->test_users[7]->profile = '';
$RQ_ARGS->TestItems->test_users[7]->role = 'poison_cat';
$RQ_ARGS->TestItems->test_users[7]->live = 'live';
$RQ_ARGS->TestItems->test_users[7]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[7]->is_system = false;
$RQ_ARGS->TestItems->test_users[7]->icon_filename = '006.gif';
$RQ_ARGS->TestItems->test_users[7]->color = '#99CCFF';

$RQ_ARGS->TestItems->test_users[8] = new User();
$RQ_ARGS->TestItems->test_users[8]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[8]->user_no = 8;
$RQ_ARGS->TestItems->test_users[8]->uname = 'blue';
$RQ_ARGS->TestItems->test_users[8]->handle_name = '青';
$RQ_ARGS->TestItems->test_users[8]->sex = 'male';
$RQ_ARGS->TestItems->test_users[8]->profile = '';
$RQ_ARGS->TestItems->test_users[8]->role = 'medium disfavor';
$RQ_ARGS->TestItems->test_users[8]->live = 'live';
$RQ_ARGS->TestItems->test_users[8]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[8]->is_system = false;
$RQ_ARGS->TestItems->test_users[8]->icon_filename = '007.gif';
$RQ_ARGS->TestItems->test_users[8]->color = '#0066FF';

$RQ_ARGS->TestItems->test_users[9] = new User();
$RQ_ARGS->TestItems->test_users[9]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[9]->user_no = 9;
$RQ_ARGS->TestItems->test_users[9]->uname = 'green';
$RQ_ARGS->TestItems->test_users[9]->handle_name = '緑';
$RQ_ARGS->TestItems->test_users[9]->sex = 'female';
$RQ_ARGS->TestItems->test_users[9]->profile = '';
$RQ_ARGS->TestItems->test_users[9]->role = 'guard perverseness';
$RQ_ARGS->TestItems->test_users[9]->live = 'live';
$RQ_ARGS->TestItems->test_users[9]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[9]->is_system = false;
$RQ_ARGS->TestItems->test_users[9]->icon_filename = '008.gif';
$RQ_ARGS->TestItems->test_users[9]->color = '#00EE00';

$RQ_ARGS->TestItems->test_users[10] = new User();
$RQ_ARGS->TestItems->test_users[10]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[10]->user_no = 10;
$RQ_ARGS->TestItems->test_users[10]->uname = 'purple';
$RQ_ARGS->TestItems->test_users[10]->handle_name = '紫';
$RQ_ARGS->TestItems->test_users[10]->sex = 'female';
$RQ_ARGS->TestItems->test_users[10]->profile = '';
$RQ_ARGS->TestItems->test_users[10]->role = 'assassin random_luck';
$RQ_ARGS->TestItems->test_users[10]->live = 'live';
$RQ_ARGS->TestItems->test_users[10]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[10]->is_system = false;
$RQ_ARGS->TestItems->test_users[10]->icon_filename = '009.gif';
$RQ_ARGS->TestItems->test_users[10]->color = '#CC00CC';

$RQ_ARGS->TestItems->test_users[11] = new User();
$RQ_ARGS->TestItems->test_users[11]->room_no = $RQ_ARGS->room_no;
$RQ_ARGS->TestItems->test_users[11]->user_no = 11;
$RQ_ARGS->TestItems->test_users[11]->uname = 'cherry';
$RQ_ARGS->TestItems->test_users[11]->handle_name = 'さくら';
$RQ_ARGS->TestItems->test_users[11]->sex = 'female';
$RQ_ARGS->TestItems->test_users[11]->profile = '';
$RQ_ARGS->TestItems->test_users[11]->role = 'reporter rebel';
$RQ_ARGS->TestItems->test_users[11]->live = 'live';
$RQ_ARGS->TestItems->test_users[11]->last_load_day_night = 'day';
$RQ_ARGS->TestItems->test_users[11]->is_system = false;
$RQ_ARGS->TestItems->test_users[11]->icon_filename = '010.gif';
$RQ_ARGS->TestItems->test_users[11]->color = '#FF9999';

$RQ_ARGS->TestItems->vote_day = array(
  'light_gray'  => array('target_uname' => 'orange', 'vote_number' => 1),
  'dark_gray'   => array('target_uname' => 'orange', 'vote_number' => 2),
  'yellow'      => array('target_uname' => 'orange', 'vote_number' => 1),
  'orange'      => array('target_uname' => 'red', 'vote_number' => 1),
  'red'         => array('target_uname' => 'orange', 'vote_number' => 1),
  'light_blue'  => array('target_uname' => 'orange', 'vote_number' => 1),
  'blue'        => array('target_uname' => 'orange', 'vote_number' => 1),
  'green'       => array('target_uname' => 'orange', 'vote_number' => 1),
  'purple'      => array('target_uname' => 'orange', 'vote_number' => 1),
  'cherry'      => array('target_uname' => 'orange', 'vote_number' => 1)
);

$test_voted_uname_list = array();
foreach($RQ_ARGS->TestItems->vote_day as $this_array){
  array_push($test_voted_uname_list, $this_array['target_uname']);
}
$RQ_ARGS->TestItems->vote_day_count_list = array_count_values($test_voted_uname_list);

$RQ_ARGS->TestItems->vote_night->wolf = array('uname' => 'light_blue', 'target_uname' => 'dark_gray');
$RQ_ARGS->TestItems->vote_night->mage = array(
  #array('uname' => 'yellow', 'target_uname' => 'blue')
);
$RQ_ARGS->TestItems->vote_night->jammer_mad = array(
  #array('uname' => 'light_grey', 'target_uname' => 'yellow')
);
$RQ_ARGS->TestItems->vote_night->child_fox = array(
  #array('uname' => 'yellow', 'target_uname' => 'dummy_boy')
);
$RQ_ARGS->TestItems->vote_night->mania = array(
  #array('uname' => 'light_blue', 'target_uname' => 'orange')
);
$RQ_ARGS->TestItems->vote_night->trap_mad = array(
  #array('uname' => 'blue', 'target_uname' => 'blue')
);
$RQ_ARGS->TestItems->vote_night->guard = array(
  #array('uname' => 'green', 'target_uname' => 'cherry')
);
$RQ_ARGS->TestItems->vote_night->reporter = array(
  #array('uname' => 'cherry', 'target_uname' => 'light_blue')
);
$RQ_ARGS->TestItems->vote_night->poison_cat = array(
  array('uname' => 'light_blue', 'target_uname' => 'light_gray')
);
$RQ_ARGS->TestItems->vote_night->assassin = array(
  #array('uname' => 'purple', 'target_uname' => 'orange')
);

$dbHandle = ConnectDatabase(); // DB 接続

$room_no = $RQ_ARGS->room_no;
$ROOM = new RoomDataSet($RQ_ARGS); //村情報をロード
$ROOM->test_mode = true;
$ROOM->log_mode = true;
//$ROOM->system_time = TZTime(); //現在時刻を取得
$USERS = new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = new User();
$ROOM_IMG->path = '../../img/room_option';
$ICON_CONF->path = '../../user_icon';
$ICON_CONF->dead = '../../img/grave.gif';
$CSS_PATH = '../../css';

OutputHTMLHeader('汝は人狼なりや？[テスト]', 'game'); //HTMLヘッダ
#print_r($ROOM); echo "<br>";
#print_r($USERS); echo "<br>";
// print_r($USERS->ByUname('orange')); echo "<br>";
#OutputGameOption($ROOM->game_option, '');
OutputPlayerList(); //プレイヤーリスト
#print_r($RQ_ARGS->TestItems->vote_night); echo "<br>";
/*
$ROOM->date = 2;
$ROOM->day_night = 'day';
$vote_times = 1;
$vote_message_list = AggregateVoteDay();
echo <<<EOF
<table class="vote-list">
<td class="vote-times" colspan="4">$ROOM->date 日目 ($vote_times 回目)</td>

EOF;
foreach($vote_message_list as $this_user => $this_array){
  echo <<<EOF
<tr><td class="vote-name">{$USERS->GetHandleName($this_user)}</td>
<td>{$this_array['voted_number']} 票</td><td>投票先 {$this_array['vote_number']} 票 →</td>
<td class="vote-name">{$this_array['target']}</td>
</tr>

EOF;
}
echo '</table>';
*/

$ROOM->day_night = 'night';
AggregateVoteNight();

/*
if($ROOM->IsFinished()) OutputVictory(); //勝敗結果
OutputRevoteList(); //再投票メッセージ
OutputTalkLog();    //会話ログ
OutputLastWords();  //遺言
OutputDeadMan();    //死亡者
OutputVoteList();   //投票結果
*/
OutputHTMLFooter(); //HTMLフッタ

DisconnectDatabase($dbHandle); //DB 接続解除
?>
