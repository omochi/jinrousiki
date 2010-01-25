<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES');

//引数を取得
$RQ_ARGS =& new RequestGameLog();
if($RQ_ARGS->day_night != 'day' && $RQ_ARGS->day_night != 'night' &&
   ! ($RQ_ARGS->day_night == 'beforegame' && $RQ_ARGS->date == 0)){
  OutputActionResult('引数エラー', '引数エラー：無効な引数です');
}

//セッション開始
session_start();
$session_id = session_id();

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

$ROOM =& new RoomDataSet($RQ_ARGS); //部屋情報を取得
$ROOM->log_mode = true;

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->ByUname($uname);

if(! ($SELF->IsDead() || $ROOM->IsAfterGame())){ //死者かゲーム終了後だけ
  OutputActionResult('ユーザ認証エラー',
		     'ログ閲覧許可エラー<br>' .
		     '<a href="index.php" target="_top">トップページ</a>' .
		     'からログインしなおしてください');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;

OutputGamePageHeader(); //HTMLヘッダ
echo '<table><tr><td width="1000" align="right">ログ閲覧 ' . $ROOM->date . ' 日目 (' .
  ($ROOM->IsBeforeGame() ? '開始前' : ($ROOM->IsDay() ? '昼' : '夜')) . ')</td></tr></table>'."\n";
OutputTalkLog();       //会話ログ
OutputAbilityAction(); //能力発揮
OutputLastWords();     //遺言
OutputDeadMan();       //死亡者
if($ROOM->IsNight()) OutputVoteList(); //投票結果
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB 接続解除
?>
