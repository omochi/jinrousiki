<?php
/*
  変更履歴 from Ver. 1.5.0β11
  + ServerConfig
    - 追加：$set_header_encode
*/

//-- データベース設定 --//
class DatabaseConfig extends DatabaseConfigBase{
  //データベースサーバのホスト名 hostname:port
  //ポート番号を省略するとデフォルトポートがセットされます。(MySQL:3306)
  public $host = 'localhost';

  //データベースのユーザ名
  #public $user = 'xxxx';
  public $user = 'grayran';

  //データベースサーバのパスワード
  #public $password = 'xxxxxxxx';
  public $password = 'satorituri';

  //データベース名
  public $name = 'jinrou';

  //サブデータベースのリスト (サーバによってはサブのデータベースを作れないので注意)
  /*
    過去ログ表示専用です。old_log.php の引数に db_no=[数字] を追加すると
    設定したサブのデータベースに切り替えることができます。
    例) $name_list = array('log_a', 'log_b');
        old_log.php?db_no=2 => log_b のデータベースのログを表示
  */
  public $name_list = array();

  //文字コード
  public $encode = 'utf8';
}

//-- サーバ設定 --//
class ServerConfig{
  //サーバのURL
  public $site_root = 'http://localhost/jinrou/';

  //タイトル
  public $title = '汝は人狼なりや？';

  //サーバのコメント
  public $comment = '';

  //管理者 (任意)
  public $admin = '';

  //サーバの文字コード
  /*
    変更する場合は全てのファイル自体の文字コードを自前で変更してください
    include/init.php も参照してください
  */
  public $encode = 'UTF-8';

  //ヘッダ強制指定 (海外サーバ等で文字化けする場合に使用します)
  public $set_header_encode = false;

  //戻り先のページ
  public $back_page = '';

  //管理者用パスワード
  #public $system_password = 'xxxxxxxx';
  public $system_password = 'pass';

  //パスワード暗号化用 salt
  #public $salt = 'xxxx';
  public $salt = 'testtest';

  //デバッグモードのオン/オフ
  #public $debug_mode = false;
  public $debug_mode = true;

  //村作成パスワード (NULL 以外を設定しておくと村作成画面にパスワード入力欄が表示されます)
  public $room_password = NULL;

  //村作成テストモード (村作成時の DB アクセス処理をスキップします。開発者テスト用スイッチです)
  public $dry_run_mode = false;

  //村作成禁止 (true にすると村の作成画面が表示されず、村を作成できなくなります)
  public $disable_establish = false;

  //村メンテナンス停止 (true にすると村の自動廃村処理などが実行されなくなります)
  public $disable_maintenance = false;

  //村情報非表示モード (村作成テストなどの開発者テスト用スイッチです)
  public $secret_room = false;

  //talk テーブルのソート処理をクエリではなく、PHP で行います
  //負荷テスト実験用に一時的に設置したスイッチです (廃止される可能性があります)
  public $sort_talk_by_php = true;

  //タイムゾーンが設定できない場合に時差を秒単位で設定するか否か
  public $adjust_time_difference = false;

  //$adjust_time_difference が有効な時の時差 (秒数)
  public $offset_seconds = 32400; //9時間

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
  public $last_updated_revision = 365;
}

//-- 村情報共有サーバの設定 --//
class SharedServerConfig extends ExternalLinkBuilder{
  public $disable = false; //無効設定 <表示を [true:無効 / false:有効] にする>

  //表示する他のサーバのリスト
  public $server_list = array(
    'cirno' => array('name' => 'チルノ鯖',
		     'url' => 'http://www12.atpages.jp/cirno/',
		     'encode' => 'UTF-8',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),

    'youmu' => array('name' => '妖夢鯖',
		     'url' => 'http://www23.atpages.jp/youmu/',
		     'encode' => 'UTF-8',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),

    'eva' => array('name' => 'Eva 鯖',
		   'url' => 'http://jinrou.kuroienogu.net/',
		   'encode' => 'EUC-JP',
		   'separator' => '',
		   'footer' => '</a><br>',
		   'disable' => true),

    'sanae' => array('name' => '早苗鯖',
		     'url' => 'http://alicegame.dip.jp/sanae/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),

    'sakuya' => array('name' => '咲夜鯖',
		      'url' => 'http://www7.atpages.jp/izayoi398/',
		      'encode' => 'EUC-JP',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</div></small></a><br>',
		      'disable' => false),

    'sasugasister' => array('name' => '流石妹者鯖',
			    'url' => 'http://www21.atpages.jp/sasugasister/',
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

    'bourbonhouse' => array('name' => 'バーボンハウス鯖',
			    'url' => 'http://bourbonhouse.xsrv.jp/jinro/',
			    'encode' => 'EUC-JP',
			    'separator' => '',
			    'footer' => '',
			    'disable' => false),

    'bourbon_chaos' => array('name' => '裏世界鯖',
			     'url' => 'http://dynamis.xsrv.jp/jinro/',
			     'encode' => 'UTF-8',
			     'separator' => '',
			     'footer' => '',
			     'disable' => false),

    'kotori' => array('name' => '小鳥鯖',
		      'url' => 'http://kiterew.tv/jinro/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'inaba' => array('name' => '因幡鯖',
		     'url' => 'http://jinro.usamimi.info/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),

    'acjinrou' => array('name' => 'AC人狼鯖',
			'url' => 'http://acjinrou.blue-sky-server.com/',
			'encode' => 'EUC-JP',
			'separator' => '',
			'footer' => '',
			'disable' => true),

    'prg_i' => array('name' => 'Twitter鯖',
		     'url' => 'http://www28.atpages.jp/pururiru/jinrou/',
		     'encode' => 'UTF-8',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false)
			   );
}

//アイコン登録設定
class UserIcon extends UserIconBase{
  public $disable_upload = false; //アイコンのアップロードの停止設定 (true:停止する / false:しない)
  public $name   = 30;    //アイコン名につけられる文字数(半角)
  public $size   = 15360; //アップロードできるアイコンファイルの最大容量(単位：バイト)
  public $width  = 45;    //アップロードできるアイコンの最大幅
  public $height = 45;    //アップロードできるアイコンの最大高さ
  public $number = 1000;  //登録できるアイコンの最大数
  public $column = 4;     //一行に表示する個数
  public $gerd   = 0;     //ゲルト君モード用のアイコン番号
  public $password = 'xxxx'; //アイコン編集パスワード
  public $cation = ''; //注意事項 (空なら何も表示しない)
}

//メニューリンク表示設定
class MenuLinkConfig{
  public $list = array('SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
		       '開発・バグ報告スレ' =>
		       'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
		       '新役職提案スレ' =>
		       'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1305122951/l50',
		       'レアケ脳向け人狼wiki' =>
		       'http://www44.atwiki.jp/rarecasejinro/'
		       );
  /* 早苗鯖用
  public $list = array('本スレ (告知はここ)' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1275564772/l50',
		    'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
		    '掲示板' => 'http://jbbs.livedoor.jp/netgame/2829/',
		    'チャットルーム' => 'http://umigamejinrou.chatx2.whocares.jp/',
		    //'旧ウミガメ雑談村' => 'http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi',
		    '反省・議論用スレ' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50'
		    );
  */
  public $add_list = array(
    '式神研系' => array('チルノ鯖' => 'http://www12.atpages.jp/cirno/',
			'妖夢鯖' => 'http://www23.atpages.jp/youmu/',
			//'Eva 鯖' => 'http://jinrou.kuroienogu.net/',
			'SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
			'開発・バグ報告スレ' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
			'新役職提案スレ' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1305122951/l50'),
    '東方ウミガメ系' => array('早苗鯖' => 'http://alicegame.dip.jp/sanae/',
			      'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
			      '掲示板' => 'http://jbbs.livedoor.jp/netgame/2829/',
			      'チャットルーム' => 'http://umigamejinrou.chatx2.whocares.jp/'),
    '東方ウミガメ系予備' => array('咲夜鯖' => 'http://www7.atpages.jp/izayoi398/'),
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
			    '世紀末鯖' => 'http://www14.atpages.jp/mmr1/',
			    '平賀鯖' => 'http://yarujin.m29.coreserver.jp/hiraga/'),
    '東方陰陽鉄系' => array('バーボンハウス鯖' => 'http://bourbonhouse.xsrv.jp/jinro/',
			    '裏世界鯖' => 'http://dynamis.xsrv.jp/jinro/',
			    'ミストさんテスト鯖' => 'http://bourbonhouse.xsrv.jp/test/',
			    'Wiki' => 'http://www29.atwiki.jp/onmyoutetu-jinro/'),
    '東方陰陽鉄系予備' => array('旧バーボンハウス鯖' => 'http://www16.atpages.jp/bourbonjinro/'),
    'iM@S系' => array('小鳥鯖' => 'http://kiterew.tv/jinro/',
		      'Wiki' => 'http://www38.atwiki.jp/ijinrou/'),
    '猫又系' => array('因幡鯖' => 'http://jinro.usamimi.info/',
		      'ちぇぇんテスト鯖' => 'http://www30.atpages.jp/cheen/jinro/',
		      'Wiki' => 'http://wikiwiki.jp/jinro/',
		      '掲示板' => 'http://jbbs.livedoor.jp/netgame/4598/'),
    'AC 人狼系' => array('AC 人狼鯖' => 'http://acjinrou.blue-sky-server.com/',
			 '掲示板' => 'http://acjinrou.bbs.fc2.com/'),
    'Twitter人狼系' => array('Twitter鯖' => 'http://www28.atpages.jp/pururiru/jinrou/')
			);
}

//告知スレッド表示設定
class BBSConfig extends ExternalLinkBuilder{
  public $disable = true; //表示無効設定 (true:無効にする / false:しない)
  public $title = '告知スレッド情報'; //表示名
  public $raw_url = 'http://jbbs.livedoor.jp/bbs/rawmode.cgi'; //データ取得用 URL
  public $view_url = 'http://jbbs.livedoor.jp/bbs/read.cgi'; //表示用 URL
  public $thread = '/game/43883/1275564772/'; //スレッドのアドレス
  public $encode = 'EUC-JP'; //スレッドの文字コード
  public $size = 5; //表示するレスの数
}

//素材情報設定
class CopyrightConfig{
  //システム標準情報
  public $list = array(
    'システム' => array('PHP4 + MYSQLスクリプト' => 'http://f45.aaa.livedoor.jp/~netfilms/',
			'mbstringエミュレータ' => 'http://sourceforge.jp/projects/mbemulator/',
			'Twitter投稿モジュール' => 'https://github.com/abraham/twitteroauth'
			),
    '写真素材' => array('天の欠片' => 'http://keppen.web.infoseek.co.jp/'),
    'フォント素材' => array('あずきフォント' => 'http://azukifont.mints.ne.jp/')
		       );

  //追加情報
  public $add_list = array(
    '写真素材' => array('Le moineau - すずめのおやど -' => 'http://moineau.fc2web.com/'),
    /*
    'アイコン素材' => array('夏蛍' => 'http://natuhotaru.yukihotaru.com/',
                            'ジギザギのさいはて' => 'http://jigizagi.s57.xrea.com/')
    */
			   );
}

//-- 開発用ソースアップロード設定 --//
class SourceUploadConfig{
  public $disable = false; //無効設定 <アップロードを [true:無効 / false:有効] にする>

  //ソースアップロードフォームのパスワード
  public $password = 'upload';

  //フォームの最大文字数と表示名
  public $form_list = array('name'     => array('size' => 20, 'label' => 'ファイル名'),
			    'caption'  => array('size' => 80, 'label' => 'ファイルの説明'),
			    'user'     => array('size' => 20, 'label' => '作成者名'),
			    'password' => array('size' => 20, 'label' => 'パスワード'));

  //最大ファイルサイズ (バイト)
  public $max_size = 10485760; //10 Mbyte
}

//-- Twitter 投稿設定 --//
class TwitterConfig extends TwitterConfigBase{
  public $disable = true; //Twitter 投稿停止設定 (true:停止する / false:しない)
  public $server = 'localhost'; //サーバ名
  public $hash = ''; //ハッシュタグ (任意、「#」は不要)
  public $add_url    = false; //サーバの URL 追加設定 (true:追加する/false:しない)
  public $direct_url = false; //村への直リンク追加設定 (要：$add_url:true / true: 追加する/false しない)
  public $short_url  = false; //TinyURL を用いた URL 短縮処理設定 (true:行う / false:行わない)
  public $key_ck = 'xxxx'; //Consumer key
  public $key_cs = 'xxxx'; //Consumer secret
  public $key_at = 'xxxx'; //Access Token
  public $key_as = 'xxxx'; //Access Token Secret

  //-- 関数 --//
  //メッセージのセット
  function GenerateMessage($id, $name, $comment){
    return "【{$this->server}】{$id}番地に{$name}村\n～{$comment}～ が建ちました";
  }
}
