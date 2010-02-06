<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB 接続
$icon_no = (int)$_GET['icon_no'];
$file = FetchResult("SELECT icon_filename FROM user_icon WHERE icon_no = $icon_no");

unlink($ICON_CONF->path . '/' . $file); //ファイルの存在をチェックしていないので要注意
mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
mysql_query('COMMIT'); //一応コミット

//DB 接続解除は OutputActionResult() 経由
OutputActionResult('アイコン削除完了',
		   '削除完了：登録ページに飛びます。<br>'."\n" .
		   '切り替わらないなら <a href="../icon_upload.php">ここ</a> 。',
		   '../icon_upload.php');
