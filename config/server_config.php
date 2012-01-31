<?php
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
  public $system_password = 'xxxxxxxx';

  //パスワード暗号化用 salt
  public $salt = 'xxxx';

  //デバッグモードのオン/オフ
  public $debug_mode = false;

  //村作成パスワード (null 以外を設定しておくと村作成画面にパスワード入力欄が表示されます)
  public $room_password = null;

  //村作成テストモード (村作成時の DB アクセス処理をスキップします。開発者テスト用スイッチです)
  public $dry_run_mode = false;

  //村作成禁止 (true にすると村の作成画面が表示されず、村を作成できなくなります)
  public $disable_establish = false;

  //村メンテナンス停止 (true にすると村の自動廃村処理などが実行されなくなります)
  public $disable_maintenance = false;

  //村情報非表示モード (村作成テストなどの開発者テスト用スイッチです)
  public $secret_room = false;

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

//-- 開発用ソースアップロード設定 --//
class SourceUploadConfig{
  public $disable = true; //無効設定 <アップロードを [true:無効 / false:有効] にする>

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
