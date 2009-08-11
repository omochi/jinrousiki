<?php
require_once(dirname(__FILE__) . '/../include/functions.php');

$CSS_PATH = '../css'; //CSS のパス設定

if(! $DEBUG_MODE)
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');

$icon_no = (int)$_GET['icon_no'];
$dbHandle = ConnectDatabase(); //DB 接続
$sql = mysql_query("SELECT icon_filename, session_id FROM user_icon WHERE icon_no = $icon_no");
$array = mysql_fetch_assoc($sql);
$file  = $array['icon_filename'];
unlink('../' . $ICON_CONF->path . '/' . $file);
mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
mysql_query('COMMIT'); //一応コミット

//DB 接続解除は OutputActionResult() 経由
OutputActionResult('アイコン削除完了',
		   '削除完了：登録ページに飛びます。<br>'."\n" .
		   '切り替わらないなら <a href="../icon_upload.php">ここ</a> 。',
		   '../icon_upload.php');
?>
