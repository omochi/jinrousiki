<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//引数を取得
$RQ_ARGS = new RequestGameLog();
if($RQ_ARGS->day_night != 'day' && $RQ_ARGS->day_night != 'night'){
  OutputActionResult('引数エラー', '引数エラー：無効な引数です');
}
$room_no = $RQ_ARGS->room_no;

//セッション開始
session_start();
$session_id = session_id();

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

$ROOM = new RoomDataSet($RQ_ARGS); //部屋情報を取得
$ROOM->log_mode = true;

//自分のハンドルネーム、役割、生存を取得
$USERS = new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF  = $USERS->ByUname($uname);

if(! ($SELF->is_dead() || $ROOM->is_aftergame())){ //死者かゲーム終了後だけ
  OutputActionResult('ユーザ認証エラー',
		     'ログ閲覧許可エラー<br>' .
		     '<a href="index.php" target="_top">トップページ</a>' .
		     'からログインしなおしてください');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;

OutputGamePageHeader(); //HTMLヘッダ
echo '<table><tr><td width="1000" align="right">ログ閲覧 ' . $ROOM->date . ' 日目 (' .
  ($ROOM->is_day() ? '昼' : '夜') . ')</td></tr></table>'."\n";
OutputTalkLog();       //会話ログ
OutputAbilityAction(); //能力発揮
OutputDeadMan();       //死亡者
if($ROOM->is_night()) OutputVoteList(); //投票結果
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB 接続解除
?>
