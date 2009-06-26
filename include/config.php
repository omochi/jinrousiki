<?php
require_once(dirname(__FILE__) . '/message_class.php'); //システムメッセージ格納クラス
require_once(dirname(__FILE__) . '/system_class.php');  //システム情報格納クラス

//部屋メンテナンス設定
class RoomConfig{
  //部屋最後の会話から廃村になるまでの時間 (秒)
  //(あまり短くすると沈黙等と競合する可能性あり)
  var $die_room = 1200;
  // var $die_room = 12000; //デバッグ用に長くしておく

  //終了した部屋のユーザのセッション ID データをクリアするまでの時間 (秒)
  var $clear_session_id = 1200;

  //最大人数のリスト (RoomImage->max_user_list と連動させる)
  var $max_user_list = array(8, 16, 22);
}

//ゲーム設定
class GameConfig{
  // 住人登録 //
  //入村制限 (同じ部屋に同じ IP で複数登録) (true：許可しない / false：許可する)
  var $entry_one_ip_address = true;
  // var $entry_one_ip_address = false; //デバッグ用

  // 投票 //
  var $kick = 3; //何票で KICK 処理を行うか
  var $draw = 5; //再投票何回目で引き分けとするか

  // 役職 //
  //埋毒者を吊った際に巻き込まれる対象 (true:投票者ランダム / false:完全ランダム)
  // var $poison_only_voter = false; // まだ実装されていません
  var $poison_only_eater = true; //狼が埋毒者を噛んだ際に巻き込まれる対象 (true:投票者固定 / false:ランダム)
  var $cupid_self_shoot  = 10; //キューピッドが他人打ち可能となる最低村人数

  // 「異議」あり //
  var $objection = 5; //最大回数
  var $objection_image = 'img/objection.gif'; //「異議」ありボタンの画像パス

  // 自動更新 //
  var $auto_reload = true; //game_view.php で自動更新を有効にする / しない (サーバ負荷に注意)
  var $auto_reload_list = array(30, 45, 60); //自動更新モードの更新間隔(秒)のリスト
}

//ゲームの時間設定
class TimeConfig{
  //日没、夜明け残り時間ゼロでこの閾値を過ぎると投票していない人は突然死します(秒)
  var $sudden_death = 180;

  // --リアルタイム制-- //
  var $default_day   = 5; //デフォルトのリアルタイム制の場合の昼の制限時間(分)
  var $default_night = 3; //デフォルトのリアルタイム制の場合の夜の制限時間(分)

  // --会話を用いた仮想時間制-- //
  //昼の制限時間(昼は12時間、spend_time=1(半角100文字以内) で 12時間 ÷ $day 進みます)
  var $day = 48;

  //夜の制限時間(夜は 6時間、spend_time=1(半角100文字以内) で  6時間 ÷ $night 進みます)
  var $night = 24;

  //非リアルタイム制でこの閾値を過ぎると沈黙となり、設定した時間が進みます(秒)
  var $silence = 60;

  //沈黙経過時間 (12時間 ÷ $day(昼) or 6時間 ÷ $night (夜) の $silence_pass 倍の時間が進みます)
  var $silence_pass = 4;
}

//ゲームプレイ時のアイコン情報
class IconConfig{
  var $path   = './user_icon';   //ユーザアイコンディレクトリ
  var $width  = 45;              //表示サイズ(幅)
  var $height = 45;              //表示サイズ(高さ)
  var $dead   = 'img/grave.jpg'; //死者
  var $wolf   = 'img/wolf.gif';  //狼
}

//開始時の役割リスト・決定者、権力者、埋毒者オプションがあるときは先頭の方から上書きされます
$role_list = array(
	  4 => array('human','wolf','mage','mad') ,
	 // 4 => array('human','wolf','poison','cupid') ,  //毒・恋人連鎖テスト用
	 5 => array('human','wolf','mage','mad','poison') ,
	 6 => array('human','mage','poison','wolf','mad','cupid') ,
	 7 => array('human','human','human','wolf','mage','guard','fox') ,
	 8 => array('human','human','human','human','human','wolf','wolf','mage') ,
	 9 => array('human','human','human','human','human','wolf','wolf','mage','necromancer') ,
	10 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad') ,
	11 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard') ,
	12 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard') ,
	13 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common') ,
	14 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common') ,
	15 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	16 => array('human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	17 => array('human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	18 => array('human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	19 => array('human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	20 => array('human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	21 => array('human','human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	22 => array('human','human','human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox')
	);

// アイコン登録設定 //
class UserIcon{
  var $name   = 20;    //アイコン名につけられる文字数(半角)
  var $size   = 15360; //アップロードできるアイコンファイルの最大容量(単位：バイト)
  var $width  = 45;    //アップロードできるアイコンの最大幅
  var $height = 45;    //アップロードできるアイコンの最大高さ
  var $number = 1000;  //登録できるアイコンの最大数
}

// 過去ログ表示設定 //
class OldLogConfig{
  var $one_page = 20;   //過去ログ一覧で1ページでいくつの村を表示するか
  var $reverse  = true; //デフォルトの村番号の表示順 (on:逆にする / off:しない)
}

// データ格納クラスをロード //
$ROOM_CONF   = new RoomConfig();   //部屋メンテナンス設定
$GAME_CONF   = new GameConfig();   //ゲーム設定
$TIME_CONF   = new TimeConfig();   //ゲームの時間設定
$ICON_CONF   = new IconConfig();   //ユーザアイコン情報
$ROOM_IMG    = new RoomImage();    //村情報の画像パス
$ROLE_IMG    = new RoleImage();    //役職の画像パス
$VICTORY_IMG = new VictoryImage(); //勝利陣営の画像パス
$SOUND       = new Sound();        //音でお知らせ機能用音源パス
$MESSAGE     = new Message();      //システムメッセージ
?>
