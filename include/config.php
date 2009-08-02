<?php
require_once(dirname(__FILE__) . '/message_class.php'); //システムメッセージ格納クラス
require_once(dirname(__FILE__) . '/system_class.php');  //システム情報格納クラス

//部屋メンテナンス・作成設定
class RoomConfig{
  //部屋最後の会話から廃村になるまでの時間 (秒)
  //(あまり短くすると沈黙等と競合する可能性あり)
  var $die_room = 1200;
  #var $die_room = 12000; //デバッグ用に長くしておく

  //終了した部屋のユーザのセッション ID データをクリアするまでの時間 (秒)
  var $clear_session_id = 1200;

  //最大人数のリスト (RoomImage->max_user_list と連動させる → 現在は連動は不要)
  var $max_user_list = array(8, 16, 22, 32);
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

  var $poison_wolf = true; //白狼出現 (必要人数は GameConfig->poison_wolf 参照)
  var $default_poison_wolf = false;

  var $mania = true; //神話マニア出現 (必要人数は GameConfig->mania 参照)
  var $default_mania = false;

  var $medium = true; //巫女出現 (必要人数は GameConfig->medium 参照)
  var $default_medium = false;

  var $liar = true; //狼少年村
  var $default_liar = false;

  var $gentleman = true; //紳士・淑女村
  var $default_gentleman = false;

  var $sudden_death = true; //虚弱体質村
  var $default_sudden_death = false;

  var $full_mania = true; //神話マニア村
  var $default_full_mania = false;

  var $quiz = true; //クイズ村 //現在調整中
  //var $quiz = false; //クイズ村
  var $default_quiz = false;

  var $chaos = true; //闇鍋
  // var $default_chaos = false; //現在未対応

  var $chaosfull = true; //真・闇鍋

  var $chaos_open_cast = true; //闇鍋村でも配役内訳を表示する
  var $secret_sub_role = true; //サブ役職を本人に通知しない
  var $no_sub_role = true; //サブ役職をつけない

  //表示する他のサーバのリスト
  var $shared_server_list = array(
	'satori' => array('name' => 'さとり鯖',
			  'url' => 'http://satori.crz.jp/'),

	'sakuya' => array('name' => '咲夜鯖',
			  'url' => 'http://www7.atpages.jp/izayoi398/',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),
	/*
	'cirno' => array('name' => 'チルノ鯖',
			 'url' => 'http://www12.atpages.jp/cirno/',
			 'separator' => '<!-- atpages banner tag -->',
			 'footer' => '</a><br>'),
	*/
	'nico' => array('name' => 'ニコ生鯖',
			'url' => 'http://jinro.ebb.jp/'),
	/*
	'nico_test' => array('name' => 'ニコ生テスト鯖',
			     'url' => 'http://jinro.s369.xrea.com/'),
	*/
	'sasuga' => array('name' => '流石兄弟鯖',
			  'url' => 'http://www12.atpages.jp/yaruo/jinro/',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'bara' => array('name' => '薔薇姉妹鯖',
			'url' => 'http://www13.atpages.jp/yaranai/',
			'encode' => 'UTF-8',
			'separator' => '<!-- atpages banner tag -->',
			'footer' => '</a><br>'),

	'suigin' => array('name' => '水銀鯖',
			  'url' => 'http://www13.atpages.jp/suigintou/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</a><br>')
				  );

}

//ゲーム設定
class GameConfig{
  //-- 住人登録 --//
  //入村制限 (同じ部屋に同じ IP で複数登録) (true：許可しない / false：許可する)
  #var $entry_one_ip_address = true;
  var $entry_one_ip_address = false; //デバッグ用

  //トリップ対応 (true：変換する / false： "#" が含まれていたらエラーを返す)
  // var $trip = true; //まだ実装されていません
  var $trip = false;

  //発言を「」で括る
  var $quote_words = false;

  //-- 投票 --//
  var $kick = 3; //何票で KICK 処理を行うか
  var $draw = 5; //再投票何回目で引き分けとするか

  //-- 役職 --//
  //希望制で役職希望が通る確率 (%) (身代わり君がいる場合は 100% にしても保証されません)
  var $wish_role_rate = 100;

  //配役テーブル
  /* 設定の見方
    [ゲーム参加人数] => array([配役名1] => [配役名1の人数], [配役名2] => [配役名2の人数], ...),
    ゲーム参加人数と配役名の人数の合計が合わない場合はゲーム開始投票時にエラーが返る
  */
  var $role_list = array(
     #4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     4 => array('human' =>  3, 'wolf' => 1),
     // 4 => array('wolf' => 1, 'mage' => 1, 'poison' => 1, 'cupid' => 1), //毒・恋人連鎖テスト用
     5 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1, 'poison' => 1),
     6 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'poison' => 1, 'fox' => 1, 'cupid' => 1),
     7 => array('human' =>  3, 'wolf' => 1, 'mage' => 1, 'guard' => 1, 'fox' => 1),
     8 => array('human' =>  5, 'wolf' => 2, 'mage' => 1),
     9 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1),
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
    22 => array('human' => 12, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    23 => array('human' => 12, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    24 => array('human' => 13, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    25 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    26 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    27 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 2),
    28 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    29 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    30 => array('human' => 16, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    31 => array('human' => 17, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    32 => array('human' => 16, 'wolf' => 5, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 2, 'common' => 3, 'fox' => 2)
                         );

  var $decide      = 16;  //決定者出現に必要な人数
  var $authority   = 16;  //権力者出現に必要な人数
  var $poison      = 20;  //埋毒者出現に必要な人数
  var $boss_wolf   = 18;  //白狼出現に必要な人数
  var $poison_wolf = 20;  //毒狼出現に必要な人数
  var $mania       = 16;  //神話マニア出現に必要な人数
  var $medium      = 20;  //巫女出現に必要な人数

  //埋毒者を吊った際に巻き込まれる対象 (true:投票者ランダム / false:完全ランダム)
  var $poison_only_voter = false; //1.3 系のデフォルト
  // var $poison_only_voter = true;

  //狼が埋毒者を噛んだ際に巻き込まれる対象 (true:投票者固定 / false:ランダム)
  var $poison_only_eater = true;

  var $cupid = 16; //キューピッド出現に必要な人数 (14人の方は現在ハードコード)
  var $cupid_self_shoot = 18; //キューピッドが他人打ち可能となる最低村人数

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

  var $invisible_rate = 15; //光学迷彩の発言が空白に入れ替わる確率
  var $silent_length  = 25; //無口が発言できる最大文字数

  //-- 「異議」あり --//
  var $objection = 5; //最大回数
  var $objection_image = 'img/objection.gif'; //「異議」ありボタンの画像パス

  //-- 自動更新 --//
  var $auto_reload = true; //game_view.php で自動更新を有効にする / しない (サーバ負荷に注意)
  var $auto_reload_list = array(15, 30, 45, 60, 90, 120); //自動更新モードの更新間隔(秒)のリスト

  //-- 役職名の翻訳 --//
  //メイン役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  var $main_role_list = array('human'             => '村人',
			      'wolf'              => '人狼',
			      'boss_wolf'         => '白狼',
			      'cursed_wolf'       => '呪狼',
			      'cute_wolf'         => '萌狼',
			      'poison_wolf'       => '毒狼',
			      'resist_wolf'       => '抗毒狼',
			      'tongue_wolf'       => '舌禍狼',
			      'mage'              => '占い師',
			      'soul_mage'         => '魂の占い師',
			      'dummy_mage'        => '夢見人',
			      'necromancer'       => '霊能者',
			      'soul_necromancer'  => '雲外鏡',
			      'dummy_necromancer' => '夢枕人',
			      'medium'            => '巫女',
			      'mad'               => '狂人',
			      'fanatic_mad'       => '狂信者',
			      'whisper_mad'       => '囁き狂人',
			      'guard'             => '狩人',
			      'poison_guard'      => '騎士',
			      'dummy_guard'       => '夢守人',
			      'reporter'          => 'ブン屋',
			      'common'            => '共有者',
			      'dummy_common'      => '夢共有者',
			      'fox'               => '妖狐',
			      'child_fox'         => '子狐',
			      'cursed_fox'        => '天狐',
			      'poison_fox'        => '管狐',
			      'white_fox'         => '白狐',
			      'poison'            => '埋毒者',
			      'strong_poison'     => '強毒者',
			      'incubate_poison'   => '潜毒者',
			      'dummy_poison'      => '夢毒者',
			      'poison_cat'        => '猫又',
			      'pharmacist'        => '薬師',
			      'suspect'           => '不審者',
			      'unconscious'       => '無意識',
			      'cupid'             => 'キューピッド',
			      'mania'             => '神話マニア',
			      'quiz'              => 'GM');

  //サブ役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  var $sub_role_list = array('authority'     => '権力者',
			     'random_voter'  => '気分屋',
			     'rebel'         => '反逆者',
			     'watcher'       => '傍観者',
			     'decide'        => '決定者',
			     'plague'        => '疫病神',
			     'good_luck'     => '幸運',
			     'bad_luck'      => '不運',
			     'upper_luck'    => '雑草魂',
			     'downer_luck'   => '一発屋',
			     'random_luck'   => '波乱万丈',
			     'star'          => '人気者',
			     'disfavor'      => '不人気',
			     'strong_voice'  => '大声',
			     'normal_voice'  => '不器用',
			     'weak_voice'    => '小声',
			     'upper_voice'   => 'メガホン',
			     'downer_voice'  => 'マスク',
			     'random_voice'  => '臆病者',
			     'no_last_words' => '筆不精',
			     'blinder'       => '目隠し',
			     'earplug'       => '耳栓',
			     'speaker'       => 'スピーカー',
			     'silent'        => '無口',
			     'liar'          => '狼少年',
			     'invisible'     => '光学迷彩',
			     'rainbow'       => '虹色迷彩',
			     'gentleman'     => '紳士',
			     'lady'          => '淑女',
			     'chicken'       => '小心者',
			     'rabbit'        => 'ウサギ',
			     'perverseness'  => '天邪鬼',
			     'flattery'      => 'ゴマすり',
			     'impatience'    => '短気',
			     'panelist'      => '解答者',
			     'lovers'        => '恋人',
			     'copied'        => '元神話マニア');

  function GetRoleName($role, $short = false){
    if(! $short) return ($this->main_role_list[$role] || $this->sub_role_list[$role]);

    static $short_role_list = array('human'             => '村',
				    'wolf'              => '狼',
				    'boss_wolf'         => '白狼',
				    'cursed_wolf'       => '呪狼',
				    'cute_wolf'         => '萌狼',
				    'poison_wolf'       => '毒狼',
				    'resist_wolf'       => '抗狼',
				    'tongue_wolf'       => '舌狼',
				    'mage'              => '占',
				    'soul_mage'         => '魂',
				    'dummy_mage'        => '夢見',
				    'reporter'          => '聞',
				    'necromancer'       => '霊',
				    'soul_necromancer'  => '雲',
				    'dummy_necromancer' => '夢枕',
				    'medium'            => '巫',
				    'mad'               => '狂',
				    'fanatic_mad'       => '狂信',
				    'whisper_mad'       => '囁狂',
				    'guard'             => '狩',
				    'poison_guard'      => '騎',
				    'dummy_guard'       => '夢守',
				    'common'            => '共',
				    'dummy_common'      => '夢共',
				    'fox'               => '狐',
				    'child_fox'         => '子狐',
				    'cursed_fox'        => '天狐',
				    'poison_fox'        => '管狐',
				    'white_fox'         => '白狐',
				    'poison'            => '毒',
				    'strong_poison'     => '強毒',
				    'dummy_poison'      => '夢毒',
				    'poison_cat'        => '猫',
				    'pharmacist'        => '薬',
				    'suspect'           => '不審',
				    'unconscious'       => '無',
				    'cupid'             => 'QP',
				    'mania'             => 'マ',
				    'quiz'              => 'GM',
				    'authority'         => '権',
				    'random_voter'      => '気',
				    'rebel'             => '反',
				    'watcher'           => '傍',
				    'decide'            => '決',
				    'plague'            => '疫',
				    'good_luck'         => '幸',
				    'bad_luck'          => '不運',
				    'upper_luck'        => '雑草',
				    'downer_luck'       => '一発',
				    'random_luck'       => '波乱',
				    'star'              => '人気',
				    'disfavor'          => '不人',
				    'strong_voice'      => '大',
				    'normal_voice'      => '不',
				    'weak_voice'        => '小',
				    'upper_voice'       => '拡声',
				    'downer_voice'      => '覆',
				    'random_voice'      => '臆',
				    'no_last_words'     => '筆',
				    'blinder'           => '目',
				    'earplug'           => '耳',
				    'speaker'           => '集音',
				    'silent'            => '無口',
				    'liar'              => '嘘',
				    'invisible'         => '迷彩',
				    'rainbow'           => '虹',
				    'gentleman'         => '紳',
				    'lady'              => '淑',
				    'chicken'           => '酉',
				    'rabbit'            => '卯',
				    'perverseness'      => '邪',
				    'flattery'          => '胡麻',
				    'impatience'        => '短',
				    'panelist'          => '解',
				    'copied'            => '元マ',
				    'lovers'            => '恋');
    return $short_role_list[$role];
  }
}

//ゲームの時間設定
class TimeConfig{
  //日没、夜明け残り時間ゼロでこの閾値を過ぎると投票していない人は突然死します(秒)
  var $sudden_death = 180;
  #var $sudden_death = 30; //デバッグ用

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
