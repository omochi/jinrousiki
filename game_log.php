<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES');

//-- データ収集 --//
$RQ_ARGS =& new RequestGameLog(); //引数を取得
$DB_CONF->Connect(); //DB 接続

session_start(); //セッション開始
$uname = CheckSession(session_id()); //セッション ID からユーザ名を取得

$ROOM =& new Room($RQ_ARGS); //村情報を取得
$ROOM->log_mode = true;

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報を取得
$SELF = $USERS->ByUname($uname);

if(! ($SELF->IsDead() || $ROOM->IsAfterGame())){ //死者かゲーム終了後だけ
  OutputActionResult('ユーザ認証エラー',
		     'ログ閲覧許可エラー<br>' .
		     '<a href="index.php" target="_top">トップページ</a>' .
		     'からログインしなおしてください');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;

//-- ログ出力 --//
OutputGamePageHeader(); //HTMLヘッダ

echo '<table><tr><td width="1000" align="right">ログ閲覧 ' . $ROOM->date . ' 日目 (' .
  ($ROOM->IsBeforeGame() ? '開始前' : ($ROOM->IsDay() ? '昼' : '夜')) . ')</td></tr></table>'."\n";

OutputTalkLog();       //会話ログ
OutputAbilityAction(); //能力発揮
OutputLastWords();     //遺言
OutputDeadMan();       //死亡者
if($ROOM->IsNight()) OutputVoteList(); //投票結果
OutputHTMLFooter(); //HTMLフッタ
?>
