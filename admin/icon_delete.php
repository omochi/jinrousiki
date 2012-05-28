<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

if (! ServerConfig::DEBUG_MODE) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
$icon_no = intval($unsafe_icon_no);
$title   = 'アイコン削除[エラー]';
if ($icon_no < 1) HTML::OutputResult($title, '無効なアイコン番号です。');

$INIT_CONF->LoadFile('icon_functions');
DB::Connect();

$error = "サーバが混雑しています。<br>\n時間を置いてから再度アクセスしてください。";
if (! DB::Lock('icon')) HTML::OutputResult($title, $error); //トランザクション開始
if (IconDB::IsUsing($icon_no)) { //使用中判定
  HTML::OutputResult($title, '募集中・プレイ中の村で使用されているアイコンは削除できません。');
}
$file = DB::FetchResult('SELECT icon_filename FROM user_icon WHERE icon_no = ' . $icon_no);
if ($file === false || is_null($file)) HTML::OutputResult($title, 'ファイルが存在しません');
if (IconDB::Delete($icon_no, $file)) {
  $url = '../icon_upload.php';
  $str = '削除完了：登録ページに飛びます。<br>'."\n" .
    '切り替わらないなら <a href="' . $url . '">ここ</a> 。';
  HTML::OutputResult('アイコン削除完了', $str, $url);
}
else {
  HTML::OutputResult($title, $error);
}
