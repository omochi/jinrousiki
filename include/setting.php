<?php
//データベース設定
class DatabaseConfig{
  //データベースサーバのホスト名 hostname:port
  //ポート番号を省略するとデフォルトポートがセットされます。(MySQL:3306)
  var $host = 'localhost';

  //データベースのユーザ名
  #var $user = 'xxxx';
  var $user = 'grayran';

  //データベースサーバのパスワード
  #var $password = 'xxxxxxxx';
  var $password = 'satorituri';

  //データベース名
  var $name = 'jinrou';
}

//サーバ設定
class ServerConfig{
  //サーバのURL
  #var $site_root = 'http://localhost/jinro/';
  var $site_root = 'http://localhost/jinrou/';

  //タイトル
  var $title = '汝は人狼なりや？';

  //サーバのコメント
  var $comment = '';

  //戻り先のページ
  var $back_page = '';

  //管理者用パスワード
  var $system_password = 'xxxxxxxx';

  //パスワード暗号化用 salt
  var $salt = 'xxxx';

  //ソースアップロードフォームのパスワード
  var $src_upload_password = 'upload';

  //タイムゾーンが設定できない場合に時差を秒単位で設定するか否か
  var $adjust_time_difference = true;
  
  //$adjust_time_differenceが有効な時の時差 (秒数)
  var $offset_seconds = 32400; //9時間

  //他の人狼サーバの村情報を表示する
  var $shared_server = false;

  // GM権限強化するか否か(強化時は即時開始、突然死が使用可能)
  var $power_gm = false;

  //表示する他のサーバのリスト
  var $shared_server_list = array(
	'sanae' => array('name' => '早苗鯖',
			  'url' => 'http://alicegame.dip.jp/sanae/',
			  'encode' => 'UTF-8'),

	'satori' => array('name' => 'さとり鯖',
			  'url' => 'http://satori.crz.jp/',
			  'encode' => 'EUC-JP'),

	'sakuya' => array('name' => '咲夜鯖',
			  'url' => 'http://www7.atpages.jp/izayoi398/',
			  'encode' => 'EUC-JP',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'cirno' => array('name' => 'チルノ鯖',
			 'url' => 'http://www12.atpages.jp/cirno/',
			  'encode' => 'EUC-JP',
			 'separator' => '<!-- atpages banner tag -->',
			 'footer' => '</a><br>'),

	'sasuga' => array('name' => '流石兄弟鯖',
			  'url' => 'http://www12.atpages.jp/yaruo/jinro/',
			  'encode' => 'EUC-JP',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'suise' => array('name' => '翠星石鯖',
			  'url' => 'http://alicegame.dip.jp/suisei/',
			  'encode' => 'UTF-8'),

	'bara' => array('name' => '薔薇姉妹鯖',
			'url' => 'http://www13.atpages.jp/yaranai/',
			'encode' => 'UTF-8',
			'separator' => '<!-- atpages banner tag -->',
			'footer' => '</a><br>'),

	'suigin' => array('name' => '水銀鯖',
			  'url' => 'http://www13.atpages.jp/suigintou/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</a><br>'),

	'mohican' => array('name' => '世紀末鯖',
			   'url' => 'http://www12.atpages.jp/yagio/jinro_php_files/jinro_php/',
			   'encode' => 'EUC-JP',
			   'separator' => '<!-- atpages banner tag -->',
			   'footer' => '</div></small></a><br>')
				  );
}
?>
