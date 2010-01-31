<?php
//-- データベース設定 --//
class DatabaseConfig extends DatabaseConfigBase{
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

//-- サーバ設定 --//
class ServerConfig{
  //サーバのURL
  var $site_root = 'http://localhost/jinrou/';

  //タイトル
  var $title = '汝は人狼なりや？';

  //サーバのコメント
  var $comment = '';

  //サーバの文字コード
  /*
    変更する場合は全てのファイル自体の文字コードを自前で変更してください
    include/contenttyep.php も参照してください
  */
  var $encode = 'EUC-JP';

  //戻り先のページ
  var $back_page = '';

  //管理者用パスワード
  var $system_password = 'xxxxxxxx';

  //パスワード暗号化用 salt
  var $salt = 'xxxx';

  //タイムゾーンが設定できない場合に時差を秒単位で設定するか否か
  var $adjust_time_difference = false;

  //$adjust_time_difference が有効な時の時差 (秒数)
  var $offset_seconds = 32400; //9時間
}

//-- 村情報共有サーバの設定 --//
class SharedServerConfig{
  var $disable = true; //無効設定 <表示を [true:無効 / false:有効] にする>

  //表示する他のサーバのリスト
  var $server_list = array(
    'sanae' => array('name' => '早苗鯖',
		     'url' => 'http://alicegame.dip.jp/sanae/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),
    /*
    'satori' => array('name' => 'さとり鯖',
		      'url' => 'http://satori.crz.jp/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => true),
    */
    'sakuya' => array('name' => '咲夜鯖',
		      'url' => 'http://www7.atpages.jp/izayoi398/',
		      'encode' => 'EUC-JP',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</div></small></a><br>',
		      'disable' => false),

    'cirno' => array('name' => 'チルノ鯖',
		     'url' => 'http://www12.atpages.jp/cirno/',
		     'encode' => 'EUC-JP',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),
    /*
    'sasuga' => array('name' => '流石兄弟鯖',
		      'url' => 'http://www12.atpages.jp/yaruo/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</div></small></a><br>',
		      'disable' => true),
    */
    'sasugabros' => array('name' => '流石弟者鯖',
			  'url' => 'http://www16.atpages.jp/sasugabros/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>',
			  'disable' => true),

    'suisei' => array('name' => '翠星石鯖',
		      'url' => 'http://alicegame.dip.jp/suisei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'bara' => array('name' => '薔薇姉妹鯖',
		    'url' => 'http://www13.atpages.jp/yaranai/',
		    'encode' => 'UTF-8',
		    'separator' => '<!-- atpages banner tag -->',
		    'footer' => '</a><br>',
		    'disable' => false),

    'suigin' => array('name' => '水銀鯖',
		      'url' => 'http://www13.atpages.jp/suigintou/',
		      'encode' => 'UTF-8',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</a><br>',
		      'disable' => false),

    'sousei' => array('name' => '蒼星石テスト鯖',
		      'url' => 'http://alicegame.dip.jp/sousei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'mohican' => array('name' => '世紀末テスト鯖',
		       'url' => 'http://www15.atpages.jp/seikima2/jinro_php/',
		       'encode' => 'UTF-8',
		       'separator' => '<!-- atpages banner tag -->',
		       'footer' => '</div></small></a><br>',
		       'disable' => true),

    'mmr' => array('name' => '世紀末鯖',
		   'url' => 'http://www14.atpages.jp/mmr1/',
		   'encode' => 'UTF-8',
		   'separator' => '<!-- atpages banner tag -->',
		   'footer' => '</div></small></a><br>',
		   'disable' => true),

    'bourbon_test' => array('name' => 'バーボンハウス鯖（仮）',
			    'url' => 'http://www16.atpages.jp/bourbonjinro/',
			    'encode' => 'UTF-8',
			    'separator' => '<!-- atpages banner tag -->',
			    'footer' => '</div></small></a><br>',
			    'disable' => true),

    'bourbonhouse' => array('name' => 'バーボンハウス鯖',
			    'url' => 'http://bourbonhouse.xsrv.jp/jinro/',
			    'encode' => 'EUC-JP',
			    'separator' => '',
			    'footer' => '',
			    'disable' => false),

    'bourbon_chaos' => array('name' => '裏世界鯖',
			     'url' => 'http://dynamis.xsrv.jp/jinro/',
			     'encode' => 'EUC-JP',
			     'separator' => '',
			     'footer' => '',
			     'disable' => true),

    'kotori' => array('name' => '小鳥鯖',
		      'url' => 'http://kiterew.tv/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => true)
			   );
}

//-- 開発用ソースアップロード設定 --//
class SourceUploadConfig{
  var $disable = true; //無効設定 <アップロードを [true:無効 / false:有効] にする>

  //ソースアップロードフォームのパスワード
  var $password = 'upload';

  //フォームの最大文字数と表示名
  var $form_list = array('name'     => array('size' => 20, 'label' => 'ファイル名'),
			 'caption'  => array('size' => 80, 'label' => 'ファイルの説明'),
			 'user'     => array('size' => 20, 'label' => '作成者名'),
			 'password' => array('size' => 20, 'label' => 'パスワード'));

  //最大ファイルサイズ (バイト)
  var $max_size = 10485760; //10 Mbyte
}
?>
