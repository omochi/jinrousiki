<?php
//-- 村メンテナンス・作成設定 --//
class RoomConfig{
  //村内の最後の発言から廃村になるまでの時間 (秒)
  //(あまり短くすると沈黙等と競合する可能性あり)
  var $die_room = 1200;
  #var $die_room = 12000; //テスト用

  //最大並列プレイ可能村数
  var $max_active_room = 4;

  //次の村を立てられるまでの待ち時間 (秒)
  var $establish_wait = 120;

  //終了した村のユーザのセッション ID データをクリアするまでの時間 (秒)
  //この時間内であれば、過去ログページに再入村のリンクが出現します
  var $clear_session_id = 86400; //24時間

  //最大人数のリスト (RoomImage->max_user_list と連動させる → 現在は不要)
  var $max_user_list = array(8, 16, 22, 32, 50);
  var $default_max_user = 22; //デフォルトの最大人数 ($max_user_list にある値を入れること)

  //-- OutputCreateRoom() --//
  var $room_name = 60; //村名の最大文字数
  var $room_comment = 60; //村の説明の最大文字数
  var $ng_word = '/http:\/\//i'; //入力禁止文字列 (正規表現)

  //各オプションを有効に [true:する / false:しない]
  //デフォルトでチェックを [true:つける / false:つけない]
  var $wish_role = true; //役割希望制
  var $default_wish_role = false;

  var $real_time = true; //リアルタイム制 (初期設定は TimeConfig->default_day/night 参照)
  var $default_real_time = true;

  var $open_vote = true; //投票した票数を公表する
  var $default_open_vote = false;

  var $open_day = true; //オープニングあり
  var $default_open_day = false;

  var $dummy_boy = true; //初日の夜は身代わり君
  var $default_dummy_boy = true;

  var $gerd = true; //ゲルト君モード
  var $default_gerd = false;

  var $not_open_cast = true; //霊界で配役を公開しない
  var $auto_open_cast = true; //霊界で配役を自動で公開する

  //霊界オフモードのデフォルト [NULL:無し / 'auto':自動オフ / 'full': 完全オフ ]
  var $default_not_open_cast = 'auto';

  var $poison = true; //埋毒者出現 (必要人数は CastConfig->poison 参照)
  var $default_poison = true;

  var $assassin = true; //暗殺者出現 (必要人数は CastConfig->assassin 参照)
  var $default_assassin = false;

  var $boss_wolf = true; //白狼出現 (必要人数は CastConfig->boss_wolf 参照)
  var $default_boss_wolf = false;

  var $poison_wolf = true; //毒狼出現 (必要人数は CastConfig->poison_wolf 参照)
  var $default_poison_wolf = false;

  var $possessed_wolf = true; //憑狼出現 (必要人数は CastConfig->possessed_wolf 参照)
  var $default_possessed_wolf = false;

  var $sirius_wolf = true; //天狼出現 (必要人数は CastConfig->sirius_wolf 参照)
  var $default_sirius_wolf = false;

  var $cupid = true; //キューピッド出現 (必要人数は CastConfig->cupid 参照)
  var $default_cupid = false;

  var $medium = true; //巫女出現 (必要人数は CastConfig->medium 参照)
  var $default_medium = false;

  var $mania = true; //神話マニア出現 (必要人数は CastConfig->mania 参照)
  var $default_mania = false;

  var $decide = true; //決定者出現 (必要人数は CastConfig->decide 参照)
  var $default_decide = true;

  var $authority = true; //権力者出現 (必要人数は CastConfig->authority 参照)
  var $default_authority = true;

  var $liar = true; //狼少年村
  var $default_liar = false;

  var $gentleman = true; //紳士・淑女村
  var $default_gentleman = false;

  var $sudden_death = true; //虚弱体質村
  var $default_sudden_death = false;

  var $perverseness = true; //天邪鬼村
  var $default_perverseness = false;

  var $critical = true; //急所村
  var $default_critical = false;

  var $detective = true; //探偵村
  var $default_detective = false;

  var $festival = true; //お祭り村
  var $default_festival = false;

  var $replace_human = true; //村人置換村
  var $full_mania = true; //神話マニア村
  var $full_chiroptera = true; //蝙蝠村
  var $full_cupid = true; //キューピッド村
  //置換モードの内訳 (replace_human：管理人カスタムモード)
  var $replace_human_list = array('full_mania', 'full_chiroptera', 'full_cupid', 'replace_human');

  var $chaos = true; //闇鍋モード
  var $chaosfull = true; //真・闇鍋モード
  var $chaos_hyper = true; //超・闇鍋モード

  //闇鍋モードのデフォルト
  //[NULL:通常人狼 / 'chaos':通常闇鍋 / 'chaosfull':真・闇鍋 / 'chaos_hyper':超・闇鍋]
  var $default_chaos = NULL; //通常人狼

  var $chaos_open_cast = true; //配役内訳を表示する (闇鍋モード専用オプション)
  var $chaos_open_cast_camp = true; //陣営毎の総数を表示する (闇鍋モード専用オプション)
  var $chaos_open_cast_role = true; //役職の種類毎の総数を表示する (闇鍋モード専用オプション)

  //通知モードのデフォルト [NULL:無し / 'camp':陣営 / 'role':役職 / 'full':完全]
  var $default_chaos_open_cast = 'camp'; //陣営通知

  var $secret_sub_role = true; //サブ役職を本人に通知しない (闇鍋モード専用オプション)
  var $default_secret_sub_role = false;

  var $sub_role_limit = true; //サブ役職制限 (闇鍋モード専用オプション)
  var $sub_role_limit_easy   = true; //サブ役職制限：EASYモード
  var $sub_role_limit_normal = true; //サブ役職制限：NORMALモード
  var $no_sub_role = true; //サブ役職をつけない
  //サブ役職制限のデフォルト [NULL:制限無し / no:つけない / easy:EASYモード / normal:NORMALモード]
  var $default_sub_role_limit = 'no'; //つけない (no_sub_role)

  var $quiz = true; //クイズ村
  var $default_quiz = false;

  var $duel = true; //決闘村
  var $default_duel = false;
}

//-- ゲーム設定 --//
class GameConfig{
  //-- 住人登録 --//
  //入村制限 (同じ部屋に同じ IP で複数登録) (true：許可しない / false：許可する)
  var $entry_one_ip_address = true;

  //トリップ対応 (true：変換する / false： "#" が含まれていたらエラーを返す)
  var $trip = true;
  var $trip_2ch = true; //2ch 互換 (12桁対応) モード (true：有効 / false：無効)

  //文字数制限
  var $entry_uname_limit = 50; //ユーザ名と村人の名前
  var $entry_profile_limit = 300; //プロフィール

  //-- 表示設定 --//
  var $quote_words = false; //発言を「」で括る
  var $display_talk_limit = 500; //ゲーム開始前後の発言表示数の限界値

  //-- 投票 --//
  var $self_kick = true; //自分への KICK (true：有効 / false：無効)
  var $kick = 3; //何票で KICK 処理を行うか
  var $draw = 5; //再投票何回目で引き分けとするか

  //-- 役職の能力設定 --//
  //毒能力者を吊った際に巻き込まれる対象 (true:投票者ランダム / false:完全ランダム)
  var $poison_only_voter = false; //1.3 系のデフォルトは false

  //狼が毒能力者を噛んだ際に巻き込まれる対象 (true:投票者固定 / false:ランダム)
  var $poison_only_eater = true; //1.3 系のデフォルトは false

  var $cupid_self_shoot = 18; //キューピッドが他人撃ち可能となる最低村人数
  var $cute_wolf_rate = 1; //萌狼の発動率 (%)
  var $gentleman_rate = 13; //紳士・淑女の発動率 (%)
  var $liar_rate = 95; //狼少年の発動率 (%)

  //狼少年の変換テーブル
  var $liar_replace_list = array('村人' => '人狼', '人狼' => '村人',
				 'むらびと' => 'おおかみ', 'おおかみ' => 'むらびと',
				 'ムラビト' => 'オオカミ', 'オオカミ' => 'ムラビト',
				 '本当' => '嘘', '嘘' => '本当',
				 '真' => '偽', '偽' => '真',
				 '人' => '狼', '狼' => '人',
				 '白' => '黒', '黒' => '白',
				 '○' => '●', '●' => '○',
				 'CO' => '潜伏', 'ＣＯ' => '潜伏', '潜伏' => 'CO',
				 '吊り' => '噛み', '噛み' => '吊り',
				 'グレラン' => 'ローラー', 'ローラー'  => 'グレラン',
				 '少年' => '少女', '少女' => '少年',
				 'しょうねん' => 'しょうじょ', 'しょうじょ' => 'しょうねん',
				 'おはよう' => 'おやすみ', 'おやすみ' => 'おはよう'
				 );

  //虹色迷彩の変換テーブル
  var $rainbow_replace_list = array('赤' => '橙', '橙' => '黄', '黄' => '緑', '緑' => '青',
				    '青' => '藍', '藍' => '紫', '紫' => '赤');

  //七曜迷彩の変換テーブル
  var $weekly_replace_list = array('月' => '火', '火' => '水', '水' => '木', '木' => '金',
				   '金' => '土', '土' => '日', '日' => '月');

  //役者の変換テーブル
  var $actor_replace_list = array('です' => 'みょん');

  var $invisible_rate = 10; //光学迷彩の発言が空白に入れ替わる確率 (%)
  var $silent_length  = 25; //無口が発言できる最大文字数

  //-- 「異議」あり --//
  var $objection = 5; //最大回数
  var $objection_image = 'img/objection.gif'; //「異議」ありボタンの画像パス

  //-- 自動更新 --//
  var $auto_reload = true; //game_view.php で自動更新を有効にする / しない (サーバ負荷に注意)
  var $auto_reload_list = array(15, 30, 45, 60, 90, 120); //自動更新モードの更新間隔(秒)のリスト

  //-- その他 --//
  var $power_gm = false; //強権 GM モード (ON：true / OFF：false)
  var $random_message = false; //ランダムメッセージの挿入 (する：true / しない：false)
}

//ゲームの時間設定
class TimeConfig{
  //日没、夜明け残り時間ゼロでこの閾値を過ぎると投票していない人は突然死します(秒)
  var $sudden_death = 120; //180;

  //超過のマイナス時間がこの閾値を越えた場合はサーバが一時的にダウンしていたと判定して、
  //超過時間をリセットします (秒)
  var $server_disconnect = 90;

  //-- リアルタイム制 --//
  var $default_day   = 5; //デフォルトの昼の制限時間(分)
  var $default_night = 3; //デフォルトの夜の制限時間(分)

  //-- 会話を用いた仮想時間制 --//
  //昼の制限時間(昼は12時間、spend_time=1(半角100文字以内) で 12時間 ÷ $day 進みます)
  var $day = 96;

  //夜の制限時間(夜は 6時間、spend_time=1(半角100文字以内) で  6時間 ÷ $night 進みます)
  var $night = 24;

  //非リアルタイム制でこの閾値を過ぎると沈黙となり、設定した時間が進みます(秒)
  var $silence = 60;

  //沈黙経過時間 (12時間 ÷ $day(昼) or 6時間 ÷ $night (夜) の $silence_pass 倍の時間が進みます)
  var $silence_pass = 8;
}

//-- 村のオプション画像 --//
class RoomImage extends ImageManager{
  var $path      = 'room_option';
  var $extension = 'gif';
  var $class     = 'option';
  /*
  //村の最大人数リスト (RoomConfig->max_user_list と連動させる)
  //現在は不使用
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8人
			     16 => 'img/room_option/max16.gif',  //16人
			     22 => 'img/room_option/max22.gif'   //22人
			     );
  */
}

//-- 役職の画像 --//
class RoleImage extends ImageManager{
  var $path      = 'role';
  var $extension = 'gif';
  var $class     = '';
}

//-- 勝利陣営の画像 --//
class VictoryImage extends VictoryImageBase{
  var $path      = 'victory_role';
  var $extension = 'gif';
  var $class     = 'winner';
}

//ゲームプレイ時のアイコン表示設定
class IconConfig{
  var $path   = 'user_icon'; //ユーザアイコンのパス
  var $dead   = 'grave.gif'; //死者
  var $wolf   = 'wolf.gif';  //狼
  var $width  = 45; //表示サイズ(幅)
  var $height = 45; //表示サイズ(高さ)
  var $view   = 100; //一画面に表示するアイコンの数
  var $page   = 10; //一画面に表示するページ数の数

  function IconConfig(){ $this->__construct(); }
  function __construct(){
    $this->path = JINRO_ROOT . '/' . $this->path;
    $this->dead = JINRO_IMG  . '/' . $this->dead;
    $this->wolf = JINRO_IMG  . '/' . $this->wolf;
  }
}

//-- 音源設定 --//
class Sound extends SoundBase{
  var $path      = 'swf'; //音源のパス
  var $extension = 'swf'; //拡張子

  var $morning          = 'sound_morning';          //夜明け
  var $revote           = 'sound_revote';           //再投票
  var $objection_male   = 'sound_objection_male';   //異議あり(男)
  var $objection_female = 'sound_objection_female'; //異議あり(女)
}

//過去ログ表示設定
class OldLogConfig{
  var $view = 20; //一画面に表示する村の数
  var $page =  5; //一画面に表示するページ数の数
  var $reverse = true; //デフォルトの村番号の表示順 (true:逆にする / false:しない)
}
