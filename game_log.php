<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//セッション開始
session_start();
$session_id = session_id();

//引数を取得
$room_no       = (int)$_GET['room_no'];
$log_mode      = $_GET['log_mode'];
$get_date      = (int)$_GET['date'];
$get_day_night = $_GET['day_night'];
if($get_day_night != 'day' && $get_day_night != 'night'){
  OutputActionResult('引数エラー', '引数エラー<br>無効な引数です');
}

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

//日付とシーンを取得
$ROOM = new RoomDataSet($room_no);
$date        = $ROOM->date;
$day_night   = $ROOM->day_night;
$game_option = $ROOM->game_option;

//自分のハンドルネーム、役割、生存を取得
$USERS = new UserDataSet($room_no); //ユーザ情報をロード
$user_no     = $USERS->UnameToNumber($uname);
$handle_name = $USERS->rows[$user_no]->handle_name;
$sex         = $USERS->rows[$user_no]->sex;
$role        = $USERS->rows[$user_no]->role;
$live        = $USERS->rows[$user_no]->live;

if($live != 'dead' && ! $ROOM->is_aftergame()){ //死者かゲーム終了後だけ
  OutputActionResult('ユーザ認証エラー',
		     'ログ閲覧許可エラー<br>' .
		     '<a href="index.php" target="_top">トップページ</a>' .
		     'からログインしなおしてください');
}

$live = 'dead';
$ROOM->date = $get_date;
$ROOM->day_night = $get_day_night;

OutputGamePageHeader(); //HTMLヘッダ
echo '<table><tr><td width="1000" align="right">ログ閲覧 ' . $ROOM->date . ' 日目 (' .
  ($ROOM->is_day() ? '昼' : '夜') . ')</td></tr></table>'."\n";
//OutputPlayerList();    //プレイヤーリスト
OutputTalkLog();       //会話ログ
OutputAbilityAction(); //能力発揮
OutputDeadMan();       //死亡者
if($ROOM->is_night()) OutputVoteList(); //投票結果
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB 接続解除
?>
