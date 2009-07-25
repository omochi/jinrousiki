<?php
//データベース設定
class DatabaseConfig{
  //データベースサーバのホスト名 hostname:port
  //ポート番号を省略するとデフォルトポートがセットされます。(MySQL:3306)
  var $host = 'localhost';

  //データベースのユーザ名
  #var $user = 'xxxxxx';
  var $user = 'grayran';

  //データベースサーバのパスワード
  #var $password = 'xxxxxx';
  var $password = 'satorituri';

  //データベース名
  var $name = 'jinrou';
}
$DB_CONF = new DatabaseConfig();

//サーバ設定
class ServerConfig{
  //サーバのURL
  var $site_root = 'http://localhost/jinro/';

  //タイトル
  var $title = '汝は人狼なりや？';

  //サーバのコメント
  var $server_comment = '〜東方ウミガメ村＠チルノ鯖〜';

  //戻り先のページ
  var $back_page = '';

  //管理者用パスワード
  var $system_password = 'xxxxxx';

  //ソースアップロードフォームのパスワード
  var $src_upload_password = 'upload';

  //時差 (秒数)
  var $offset_seconds = 32400; //9時間

  //他の人狼サーバの村情報を表示する
  var $shared_server = false;
}
$SERVER_CONF = new ServerConfig();

//デバッグモードのオン/オフ
$DEBUG_MODE = false;

//外部ファイルの読み込み
require_once(dirname(__FILE__) . '/config.php');          //高度な設定
require_once(dirname(__FILE__) . '/version.php');         //バージョン情報
require_once(dirname(__FILE__) . '/contenttype_set.php'); //ヘッダの文字コード設定
require_once(dirname(__FILE__) . '/../paparazzi.php');    //デバッグ用
?>
