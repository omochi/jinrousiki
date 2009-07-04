<?php
require_once(dirname(__FILE__) . '/message_class.php'); //システムメッセージ格納クラス
require_once(dirname(__FILE__) . '/system_class.php');  //システム情報格納クラス

//部屋メンテナンス・作成設定
class RoomConfig{
  //部屋最後の会話から廃村になるまでの時間 (秒)
  //(あまり短くすると沈黙等と競合する可能性あり)
  var $die_room = 1200;
  // var $die_room = 12000; //デバッグ用に長くしておく

  //終了した部屋のユーザのセッション ID データをクリアするまでの時間 (秒)
  var $clear_session_id = 1200;

  //最大人数のリスト (RoomImage->max_user_list と連動させる)
  var $max_user_list = array(8, 16, 22);
  var $default_max_user = 22; //デフォルトの最大人数 ($max_user_list に含むこと)

  //-- OutputCreateRoom() --//
  var $room_name = 45; //村名の最大文字数
  var $room_comment = 50; //村の説明の最大文字数

  //各オプションを有効に[する / しない]、デフォルトでチェックを [つける / つけない]
  var $wish_role = true; //役割希望制
  var $default_wish_role = false;

  var $real_time = true; //リアルタイム制 (初期設定は TimeConfig->default_day/night 参照)
  var $default_real_time = true;

  var $dummy_boy = true; //初日の夜は身代わり君
  var $default_dummy_boy = true;

  var $open_vote = true; //投票した票数を公表する
  var $default_open_vote = true;

  var $not_open_cast = true; //霊界で配役を公開しない
  var $default_not_open_cast = false;

  var $decide = true; //決定者出現 (必要人数は GameConfig->decide 参照)
  var $default_decide = true;

  var $authority = true; //権力者出現 (必要人数は GameConfig->authority 参照)
  var $default_authority = true;

  var $poison = true; //埋毒者出現 (必要人数は GameConfig->poison 参照)
  var $default_poison = true;

  var $cupid = true; //キューピッド出現 (必要人数は GameConfig->cupid 参照)
  var $default_cupid = false;

  var $boss_wolf = true; //白狼出現 (必要人数は GameConfig->boss_wolf 参照)
  var $default_boss_wolf = false;

  // var $quiz = true; //クイズ村 //現在調整中
  var $quiz = false; //クイズ村
  var $default_quiz = false;

  var $chaos = true; //闇鍋
  // var $default_chaos = false; //現在未対応

  var $chaosfull = true; //真・闇鍋
}

//ゲーム設定
class GameConfig{
  //-- 住人登録 --//
  //入村制限 (同じ部屋に同じ IP で複数登録) (true：許可しない / false：許可する)
  // var $entry_one_ip_address = true;
  var $entry_one_ip_address = false; //デバッグ用

  //トリップ対応 (true：変換する / false： "#" が含まれていたらエラーを返す)
  // var $trip = true; //まだ実装されていません
  var $trip = false;

  //-- 投票 --//
  var $kick = 3; //何票で KICK 処理を行うか
  var $draw = 3; //再投票何回目で引き分けとするか

  //-- 役職 --//
  //メイン役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  var $main_role_list = array('human'        => '村人',
			      'wolf'         => '人狼',
			      'boss_wolf'    => '白狼',
			      'mage'         => '占い師',
			      'soul_mage'    => '魂の占い師',
			      'necromancer'  => '霊能者',
			      'medium'       => '巫女',
			      'mad'          => '狂人',
			      'fanatic_mad'  => '狂信者',
			      'guard'        => '狩人',
			      'poison_guard' => '騎士',
			      'common'       => '共有者',
			      'fox'          => '妖狐',
			      'child_fox'    => '子狐',
			      'poison'       => '埋毒者',
			      'suspect'      => '不審者',
			      'cupid'        => 'キューピッド',
			      'mania'        => '神話マニア',
			      'quiz'         => 'GM');

  //サブ役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  var $sub_role_list = array('decide'        => '決定者',
			     'authority'     => '権力者',
			     'plague'        => '疫病神',
			     'watcher'       => '傍観者',
			     'strong_voice'  => '大声',
			     'normal_voice'  => '不器用',
			     'weak_voice'    => '小声',
			     'no_last_words' => '筆不精',
			     'chicken'       => '小心者',
			     'rabbit'        => 'ウサギ',
			     'perverseness'  => '天邪鬼',
			     'lovers'        => '恋人',
			     'copied'        => '元神話マニア',);

  //配役テーブル
  /* 設定の見方
    [ゲーム参加人数] => array([配役名1] => [配役名1の人数], [配役名2] => [配役名2の人数], ...),
    ゲーム参加人数と配役名の人数の合計が合わない場合はゲーム開始投票時にエラーが返る
  */
  var $role_list = array(
     4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     // 4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mania' => 1), // 神話マニアテスト用
     // 4 => array('wolf' => 1, 'mage' => 1, 'poison' => 1, 'cupid' => 1), //毒・恋人連鎖テスト用
     5 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1, 'poison' => 1),
     // 5 => array('wolf' => 1, 'mage' => 3, 'poison' => 1), //複数占いテスト用
     // 6 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1, 'poison' => 1, 'cupid' => 1),
     6 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'medium' => 1, 'fox' => 1, 'cupid' => 1),
     // 6 => array('wolf' => 2, 'necromancer' => 2, 'guard' => 2), //複数霊能＆狩人テスト用
     7 => array('human' =>  3, 'wolf' => 1, 'mage' => 1, 'guard' => 1, 'fox' => 1),
     // 7 => array('wolf' => 1, 'fox' => 2, 'child_fox' => 1, 'mage' => 2, 'soul_mage' => 1),
     // 7 => array('wolf' => 1, 'mage' => 2, 'guard' => 2, 'fox' => 2), //狐関連テスト用
     8 => array('human' =>  5, 'wolf' => 2, 'mage' => 1),
     9 => array('human' =>  5, 'wolf' => 1, 'cupid' => 2, 'necromancer' => 1),
    10 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1),
    11 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    12 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    13 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common'=> 2),
    14 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2),
    15 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    16 => array('human' =>  6, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    17 => array('human' =>  7, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    18 => array('human' =>  8, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    19 => array('human' =>  9, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    20 => array('human' => 10, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    21 => array('human' => 11, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    22 => array('human' => 12, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1)
                         );

  var $decide    = 16;  //決定者出現に必要な人数
  var $authority = 16;  //権力者出現に必要な人数
  var $poison    = 20;  //埋毒者出現に必要な人数
  var $boss_wolf = 20;  //白狼出現に必要な人数

  //埋毒者を吊った際に巻き込まれる対象 (true:投票者ランダム / false:完全ランダム)
  // var $poison_only_voter = false; //まだ実装されていません

  //狼が埋毒者を噛んだ際に巻き込まれる対象 (true:投票者固定 / false:ランダム)
  var $poison_only_eater = true;

  var $cupid = 16; //キューピッド出現に必要な人数 (14人の方は現在ハードコード)
  var $cupid_self_shoot = 18; //キューピッドが他人打ち可能となる最低村人数

  var $chaos_open_role = false; //闇鍋村でも配役内訳を表示する (闇鍋村の意味がなくなるのでデバッグ専用)
  // var $chaos_open_role = true; //デバッグ用

  //-- 「異議」あり --//
  var $objection = 5; //最大回数
  var $objection_image = 'img/objection.gif'; //「異議」ありボタンの画像パス

  //-- 自動更新 --//
  var $auto_reload = true; //game_view.php で自動更新を有効にする / しない (サーバ負荷に注意)
  var $auto_reload_list = array(30, 45, 60); //自動更新モードの更新間隔(秒)のリスト
}

//ゲームの時間設定
class TimeConfig{
  //日没、夜明け残り時間ゼロでこの閾値を過ぎると投票していない人は突然死します(秒)
  var $sudden_death = 180;
  // var $sudden_death = 30; //デバッグ用

  //クイズ村は専用の突然死発動時間を設定する
  var $sudden_death_quiz = 90;

  //-- リアルタイム制 --//
  var $default_day   = 5; //デフォルトの昼の制限時間(分)
  var $default_night = 3; //デフォルトの夜の制限時間(分)

  //-- 会話を用いた仮想時間制 --//
  //昼の制限時間(昼は12時間、spend_time=1(半角100文字以内) で 12時間 ÷ $day 進みます)
  var $day = 48;

  //夜の制限時間(夜は 6時間、spend_time=1(半角100文字以内) で  6時間 ÷ $night 進みます)
  var $night = 24;

  //非リアルタイム制でこの閾値を過ぎると沈黙となり、設定した時間が進みます(秒)
  var $silence = 60;

  //沈黙経過時間 (12時間 ÷ $day(昼) or 6時間 ÷ $night (夜) の $silence_pass 倍の時間が進みます)
  var $silence_pass = 4;
}

//ゲームプレイ時のアイコン表示設定
class IconConfig{
  var $path   = './user_icon';   //ユーザアイコンのパス
  var $width  = 45;              //表示サイズ(幅)
  var $height = 45;              //表示サイズ(高さ)
  var $dead   = 'img/grave.jpg'; //死者
  var $wolf   = 'img/wolf.gif';  //狼
}

//アイコン登録設定
class UserIcon{
  var $name   = 20;    //アイコン名につけられる文字数(半角)
  var $size   = 15360; //アップロードできるアイコンファイルの最大容量(単位：バイト)
  var $width  = 45;    //アップロードできるアイコンの最大幅
  var $height = 45;    //アップロードできるアイコンの最大高さ
  var $number = 1000;  //登録できるアイコンの最大数
}

//過去ログ表示設定
class OldLogConfig{
  var $one_page = 20;   //過去ログ一覧で1ページでいくつの村を表示するか
  var $reverse  = true; //デフォルトの村番号の表示順 (true:逆にする / false:しない)
}

//データ格納クラスをロード
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
