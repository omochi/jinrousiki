<?php
require_once(dirname(__FILE__) . '/config.php');          //高度な設定
require_once(dirname(__FILE__) . '/version.php');         //バージョン情報
require_once(dirname(__FILE__) . '/contenttype_set.php'); //ヘッダの文字コード設定
require_once(dirname(__FILE__) . '/../paparazzi.php');    //デバッグ用

//サーバのURL
$site_root = "http://localhost/jinro/";

//サーバのコメント
$server_comment = '〜東方ウミガメ村＠チルノ鯖〜';

//データベースサーバのホスト名 hostname:port
//ポート番号を省略するとデフォルトポートがセットされます。(MySQL:3306)
$db_host = 'localhost';

//データベースのユーザ名
$db_uname = 'grayran';

//データベースサーバのパスワード
$db_pass = 'satorituri';

//データベース名
$db_name = 'jinrou';

//管理者用パスワード
$system_password = 'pass';

//ソースアップロードフォームのパスワード
$src_upload_password = 'upload';

//戻り先のページ
$back_page = '';

//デバッグモードのオン/オフ
$DEBUG_MODE = true;

//時差 (秒数)
$OFFSET_SECONDS = 9 * 60 * 60;
?>
