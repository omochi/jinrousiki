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
  #var $site_root = 'http://localhost/jinrou/';
  var $site_root = 'http://localhost/jinro/';

  //タイトル
  var $title = '汝は人狼なりや？';

  //サーバのコメント
  var $comment = '〜チルノ鯖＠ローカル〜';

  //サーバの文字コード
  /*
    変更する場合は全てのファイル自体の文字コードを自前で変更してください
    include/init.php も参照してください
  */
  var $encode = 'EUC-JP';

  //戻り先のページ
  var $back_page = '';

  //管理者用パスワード
  #var $system_password = 'xxxxxxxx';
  var $system_password = 'pass';

  //パスワード暗号化用 salt
  #var $salt = 'xxxx';
  var $salt = 'testtest';

  //タイムゾーンが設定できない場合に時差を秒単位で設定するか否か
  var $adjust_time_difference = false;

  //$adjust_time_difference が有効な時の時差 (秒数)
  var $offset_seconds = 32400; //9時間

  //更新前のスクリプトのリビジョン番号
  /*
    ※ この機能は Ver. 1.4.0 beta1 (revision 152) で実装されました。

    更新前のスクリプトの class ScriptInfo (config/version.php) で
    定義されている $revision を設定することで admin/setup.php で
    行われる処理が最適化されます。

    初めて当スクリプトを設置する場合や、データベースを一度完全消去して
    再設置する場合は 0 を設定して下さい。

    更新前のスクリプトに該当ファイルや変数がない場合や、
    バージョンが分からない場合は 1 を設定してください。

    更新後のリビジョン番号と同じか、それより大きな値を設定すると
    admin/setup.php の処理は常時スキップされます。
  */
  var $last_updated_revision = 187;

  //村情報非表示モード (村立てテストなどの開発者テスト用スイッチです)
  var $secret_room = false;
}

//-- 村情報共有サーバの設定 --//
class SharedServerConfig extends ExternalLinkBuilder{
  var $disable = true; //無効設定 <表示を [true:無効 / false:有効] にする>

  //表示する他のサーバのリスト
  var $server_list = array(
    'cirno' => array('name' => 'チルノ鯖',
		     'url' => 'http://www12.atpages.jp/cirno/',
		     'encode' => 'EUC-JP',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),

    'eva' => array('name' => 'Eva 鯖',
		   'url' => 'http://jinrou.kuroienogu.net/',
		   'encode' => 'EUC-JP',
		   'separator' => '',
		   'footer' => '</a><br>',
		   'disable' => false),

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

    'sasugasister' => array('name' => '流石妹者鯖',
			    'url' => 'http://www21.atpages.jp/sasugasister/',
			    'encode' => 'UTF-8',
			    'separator' => '<!-- atpages banner tag -->',
			    'footer' => '</div></small></a><br>',
			    'disable' => false),

    'suisei' => array('name' => '翠星石鯖',
		      'url' => 'http://alicegame.dip.jp/suisei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'sousei' => array('name' => '蒼星石テスト鯖',
		      'url' => 'http://alicegame.dip.jp/sousei/',
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
			     'disable' => false),

    'kotori' => array('name' => '小鳥鯖',
		      'url' => 'http://kiterew.tv/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    /*
    'bourbon' => array('name' => 'バーボン鯖',
		       'url' => 'http://www.freedom.xii.jp/jinro/',
		       'encode' => 'EUC-JP',
		       'separator' => '',
		       'footer' => '',
		       'disable' => false),
    */
    'nekomata' => array('name' => '猫又鯖',
			'url' => 'http://jinro.blue-sky-server.com/',
			'encode' => 'UTF-8',
			'separator' => '<!-- End Ad -->',
			'footer' => '</a>',
			'disable' => false),

    'acjinrou' => array('name' => 'AC人狼鯖',
			'url' => 'http://acjinrou.blue-sky-server.com/',
			'encode' => 'EUC-JP',
			'separator' => '',
			'footer' => '',
			'disable' => false)
			   );
}

//アイコン登録設定
class UserIcon extends UserIconBase{
  var $disable_upload = false; //true; //アイコンのアップロードの停止設定 (true:停止する / false:しない)
  var $name   = 30;    //アイコン名につけられる文字数(半角)
  var $size   = 15360; //アップロードできるアイコンファイルの最大容量(単位：バイト)
  var $width  = 45;    //アップロードできるアイコンの最大幅
  var $height = 45;    //アップロードできるアイコンの最大高さ
  var $number = 1000;  //登録できるアイコンの最大数
  var $password = 'xxxx'; //アイコン編集パスワード
}

//メニューリンク表示設定
class MenuLinkConfig extends MenuLinkConfigBase{
  var $list = array('SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
		    '開発・バグ報告スレ' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
		    '新役職提案スレ' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50'
		    );
  /* 早苗鯖用
  var $list = array('本スレ (告知はここ)' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1275564772/l50',
		    'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
		    '掲示板' => 'http://jbbs.livedoor.jp/netgame/2829/',
		    'チャットルーム' => 'http://umigamejinrou.chatx2.whocares.jp/',
		    //'旧ウミガメ雑談村' => 'http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi',
		    '反省・議論用スレ' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50'
		    );
  */
  var $add_list = array(
    '式神研系' => array('チルノ鯖' => 'http://www12.atpages.jp/cirno/',
			'Eva 鯖' => 'http://jinrou.kuroienogu.net/',
			'SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
			'開発・バグ報告スレ' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
			'新役職提案スレ' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50'),
    '東方ウミガメ系' => array('早苗鯖' => 'http://alicegame.dip.jp/sanae/',
			      'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
			      '掲示板' => 'http://jbbs.livedoor.jp/netgame/2829/',
			      'チャットルーム' => 'http://umigamejinrou.chatx2.whocares.jp/'),
    '東方ウミガメ系予備' => array(//'さとり鯖' => 'http://satori.crz.jp/',
				  '咲夜鯖' => 'http://www7.atpages.jp/izayoi398/'),
    'やる夫系' => array('流石妹者鯖' => 'http://www21.atpages.jp/sasugasister/',
			'翠星石鯖' => 'http://alicegame.dip.jp/suisei/',
			'蒼星石テスト鯖' => 'http://alicegame.dip.jp/sousei/',
			'Wiki' => 'http://www37.atwiki.jp/yaruomura/',
			'掲示板' => 'http://jbbs.livedoor.jp/game/48159/',
			'流石鯖用ツール' => 'http://www.petitnoir.net/zinro/sasuga.html',
			'流石戦績検索' => 'http://www.petitnoir.net/zinro/sasuga/yaruomura.php'),
    'やる夫系予備' => array('流石兄弟鯖' => 'http://www12.atpages.jp/yaruo/jinro/',
			    '流石弟者鯖' => 'http://www16.atpages.jp/sasugabros/',
			    '薔薇姉妹鯖' => 'http://www13.atpages.jp/yaranai/',
			    '水銀鯖' => 'http://www13.atpages.jp/suigintou/',
			    '金糸雀保管庫' => 'http://www15.atpages.jp/kanaria/',
			    '世紀末鯖' => 'http://www14.atpages.jp/mmr1/'),
    '東方陰陽鉄系' => array('バーボンハウス鯖' => 'http://bourbonhouse.xsrv.jp/jinro/',
			'裏世界鯖' => 'http://dynamis.xsrv.jp/jinro/',
			'Wiki' => 'http://www29.atwiki.jp/onmyoutetu-jinro/'),
    '東方陰陽鉄系予備' => array('旧バーボンハウス鯖' => 'http://www16.atpages.jp/bourbonjinro/'),
    'iM@S系' => array('小鳥鯖' => 'http://kiterew.tv/jinro/',
		      'Wiki' => 'http://www38.atwiki.jp/ijinrou/'),
    'バーボン鯖系' => array('バーボン鯖' => 'http://www.freedom.xii.jp/jinro/',
			    '猫又鯖' => 'http://jinro.blue-sky-server.com/',
			    'Wiki' => 'http://wikiwiki.jp/jinro/',
			    '掲示板' => 'http://jbbs.livedoor.jp/netgame/4598/'),
    'AC 人狼系' => array('AC 人狼鯖' => 'http://acjinrou.blue-sky-server.com/',
			    '掲示板' => 'http://acjinrou.bbs.fc2.com/'),
			);
}

//告知スレッド表示設定
class BBSConfig extends BBSConfigBase{
  var $disable = true; //表示無効設定 (true:無効にする / false:しない)
  var $title = '告知スレッド情報'; //表示名
  var $raw_url = 'http://jbbs.livedoor.jp/bbs/rawmode.cgi'; //データ取得用 URL
  var $view_url = 'http://jbbs.livedoor.jp/bbs/read.cgi'; //表示用 URL
  var $thread = '/game/43883/1260623018/'; //スレッドのアドレス
  var $encode = 'EUC-JP'; //スレッドの文字コード
  var $size = 5; //表示するレスの数
}

//素材情報設定
class CopyrightConfig extends CopyrightConfigBase{
  //システム標準情報
  var $list = array('システム' =>
		    array('PHP4 + MYSQLスクリプト' => 'http://f45.aaa.livedoor.jp/~netfilms/',
			  'mbstringエミュレータ' => 'http://sourceforge.jp/projects/mbemulator/'
			  ),
		    '写真素材' =>
		    array('天の欠片' => 'http://keppen.web.infoseek.co.jp/'),
		    'フォント素材' =>
		    array('あずきフォント' => 'http://azukifont.mints.ne.jp/')
		    );

  //追加情報
  var $add_list = array('システム' =>
			array('Twitter投稿モジュール' =>
			      'http://www.transrain.net/product/services_twitter/'),
			'写真素材' =>
			array('Le moineau - すずめのおやど -' => 'http://moineau.fc2web.com/'),
			'アイコン素材' =>
			array('夏蛍' => 'http://natuhotaru.yukihotaru.com/',
			      'ジギザギのさいはて' => 'http://jigizagi.s57.xrea.com/')
			);
}

//-- 開発用ソースアップロード設定 --//
class SourceUploadConfig{
  var $disable = false; //無効設定 <アップロードを [true:無効 / false:有効] にする>

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

//-- Twitter 投稿設定 --//
class TwitterConfig extends TwitterConfigBase{
  var $disable = true; //Twitter 投稿停止設定 (true:停止する / false:しない)
  var $server = 'localhost'; //サーバ名
  var $hash = ''; //ハッシュタグ (任意)
  var $user = 'xxxx'; //ユーザ名
  var $password = 'xxxx'; //パスワード
}
