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
  var $max_user_list = array(8, 16, 22, 32);
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

  var $dummy_boy = true; //初日の夜は身代わり君
  var $default_dummy_boy = true;

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

  var $full_mania = true; //神話マニア村
  var $default_full_mania = false;

  var $detective = true; //お祭り村
  var $default_detective = false;

  var $festival = true; //お祭り村
  var $default_festival = false;

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

  var $no_sub_role = true; //サブ役職をつけない (闇鍋モード専用オプション)
  var $default_no_sub_role = true;

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

  var $invisible_rate = 10; //光学迷彩の発言が空白に入れ替わる確率 (%)
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
  var $main_role_list = array(
    'human'              => '村人',
    'elder'              => '長老',
    'saint'              => '聖女',
    'executor'           => '執行者',
    'escaper'            => '逃亡者',
    'suspect'            => '不審者',
    'unconscious'        => '無意識',
    'mage'               => '占い師',
    'soul_mage'          => '魂の占い師',
    'psycho_mage'        => '精神鑑定士',
    'sex_mage'           => 'ひよこ鑑定士',
    'voodoo_killer'      => '陰陽師',
    'dummy_mage'         => '夢見人',
    'necromancer'        => '霊能者',
    'soul_necromancer'   => '雲外鏡',
    'yama_necromancer'   => '閻魔',
    'dummy_necromancer'  => '夢枕人',
    'medium'             => '巫女',
    'priest'             => '司祭',
    'bishop_priest'      => '司教',
    'crisis_priest'      => '預言者',
    'revive_priest'      => '天人',
    'guard'              => '狩人',
    'poison_guard'       => '騎士',
    'fend_guard'         => '忍者',
    'reporter'           => 'ブン屋',
    'anti_voodoo'        => '厄神',
    'dummy_guard'        => '夢守人',
    'common'             => '共有者',
    'detective_common'   => '探偵',
    'trap_common'        => '策士',
    'ghost_common'       => '亡霊嬢',
    'dummy_common'       => '夢共有者',
    'poison'             => '埋毒者',
    'strong_poison'      => '強毒者',
    'incubate_poison'    => '潜毒者',
    'guide_poison'       => '誘毒者',
    'chain_poison'       => '連毒者',
    'dummy_poison'       => '夢毒者',
    'poison_cat'         => '猫又',
    'revive_cat'         => '仙狸',
    'sacrifice_cat'      => '猫神',
    'pharmacist'         => '薬師',
    'cure_pharmacist'    => '河童',
    'assassin'           => '暗殺者',
    'doom_assassin'      => '死神',
    'reverse_assassin'   => '反魂師',
    'eclipse_assassin'   => '蝕暗殺者',
    'mind_scanner'       => 'さとり',
    'evoke_scanner'      => 'イタコ',
    'whisper_scanner'    => '囁騒霊',
    'howl_scanner'       => '吠騒霊',
    'telepath_scanner'   => '念騒霊',
    'jealousy'           => '橋姫',
    'poison_jealousy'    => '毒橋姫',
    'doll'               => '上海人形',
    'poison_doll'        => '鈴蘭人形',
    'friend_doll'        => '仏蘭西人形',
    'doll_master'        => '人形遣い',
    'wolf'               => '人狼',
    'boss_wolf'          => '白狼',
    'gold_wolf'          => '金狼',
    'phantom_wolf'       => '幻狼',
    'cursed_wolf'        => '呪狼',
    'wise_wolf'          => '賢狼',
    'poison_wolf'        => '毒狼',
    'resist_wolf'        => '抗毒狼',
    'blue_wolf'          => '蒼狼',
    'emerald_wolf'       => '翠狼',
    'sex_wolf'           => '雛狼',
    'tongue_wolf'        => '舌禍狼',
    'possessed_wolf'     => '憑狼',
    'sirius_wolf'        => '天狼',
    'elder_wolf'         => '古狼',
    'cute_wolf'          => '萌狼',
    'scarlet_wolf'       => '紅狼',
    'silver_wolf'        => '銀狼',
    'mad'                => '狂人',
    'fanatic_mad'        => '狂信者',
    'whisper_mad'        => '囁き狂人',
    'jammer_mad'         => '月兎',
    'voodoo_mad'         => '呪術師',
    'corpse_courier_mad' => '火車',
    'agitate_mad'        => '扇動者',
    'miasma_mad'         => '土蜘蛛',
    'dream_eater_mad'    => '獏',
    'trap_mad'           => '罠師',
    'possessed_mad'      => '犬神',
    'fox'                => '妖狐',
    'white_fox'          => '白狐',
    'black_fox'          => '黒狐',
    'gold_fox'           => '金狐',
    'phantom_fox'        => '幻狐',
    'poison_fox'         => '管狐',
    'blue_fox'           => '蒼狐',
    'emerald_fox'        => '翠狐',
    'voodoo_fox'         => '九尾',
    'revive_fox'         => '仙狐',
    'possessed_fox'      => '憑狐',
    'cursed_fox'         => '天狐',
    'elder_fox'          => '古狐',
    'cute_fox'           => '萌狐',
    'scarlet_fox'        => '紅狐',
    'silver_fox'         => '銀狐',
    'child_fox'          => '子狐',
    'sex_fox'            => '雛狐',
    'cupid'              => 'キューピッド',
    'self_cupid'         => '求愛者',
    'moon_cupid'         => 'かぐや姫',
    'mind_cupid'         => '女神',
    'triangle_cupid'     => '小悪魔',
    'angel'              => '天使',
    'rose_angel'         => '薔薇天使',
    'lily_angel'         => '百合天使',
    'exchange_angel'     => '魂移使',
    'ark_angel'          => '大天使',
    'quiz'               => '出題者',
    'chiroptera'         => '蝙蝠',
    'poison_chiroptera'  => '毒蝙蝠',
    'cursed_chiroptera'  => '呪蝙蝠',
    'boss_chiroptera'    => '大蝙蝠',
    'elder_chiroptera'   => '古蝙蝠',
    'dummy_chiroptera'   => '夢求愛者',
    'fairy'              => '妖精',
    'spring_fairy'       => '春妖精',
    'summer_fairy'       => '夏妖精',
    'autumn_fairy'       => '秋妖精',
    'winter_fairy'       => '冬妖精',
    'light_fairy'        => '光妖精',
    'dark_fairy'         => '闇妖精',
    'mirror_fairy'       => '鏡妖精',
    'mania'              => '神話マニア',
    'trick_mania'        => '奇術師',
    'soul_mania'         => '覚醒者',
    'unknown_mania'      => '鵺',
    'dummy_mania'        => '夢語部');

  //サブ役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  var $sub_role_list = array(
    'chicken'          => '小心者',
    'rabbit'           => 'ウサギ',
    'perverseness'     => '天邪鬼',
    'flattery'         => 'ゴマすり',
    'celibacy'         => '独身貴族',
    'impatience'       => '短気',
    'nervy'            => '自信家',
    'febris'           => '熱病',
    'death_warrant'    => '死の宣告',
    'panelist'         => '解答者',
    'liar'             => '狼少年',
    'invisible'        => '光学迷彩',
    'rainbow'          => '虹色迷彩',
    'weekly'           => '七曜迷彩',
    //'monochrome'       => '白黒迷彩',
    'grassy'           => '草原迷彩',
    'side_reverse'     => '鏡面迷彩',
    'line_reverse'     => '天地迷彩',
    'gentleman'        => '紳士',
    'lady'             => '淑女',
    'authority'        => '権力者',
    'random_voter'     => '気分屋',
    'rebel'            => '反逆者',
    'watcher'          => '傍観者',
    'decide'           => '決定者',
    'plague'           => '疫病神',
    'good_luck'        => '幸運',
    'bad_luck'         => '不運',
    'upper_luck'       => '雑草魂',
    'downer_luck'      => '一発屋',
    'random_luck'      => '波乱万丈',
    'star'             => '人気者',
    'disfavor'         => '不人気',
    'strong_voice'     => '大声',
    'normal_voice'     => '不器用',
    'weak_voice'       => '小声',
    'upper_voice'      => 'メガホン',
    'downer_voice'     => 'マスク',
    'inside_voice'     => '内弁慶',
    'outside_voice'    => '外弁慶',
    'random_voice'     => '臆病者',
    'no_last_words'    => '筆不精',
    'blinder'          => '目隠し',
    'earplug'          => '耳栓',
    'speaker'          => 'スピーカー',
    'silent'           => '無口',
    'mower'            => '草刈り',
    'mind_read'        => 'サトラレ',
    'mind_open'        => '公開者',
    'mind_receiver'    => '受信者',
    'mind_friend'      => '共鳴者',
    'mind_sympathy'    => '共感者',
    'mind_evoke'       => '口寄せ',
    'mind_lonely'      => 'はぐれ者',
    'lovers'           => '恋人',
    'challenge_lovers' => '難題',
    'copied'           => '元神話マニア',
    'copied_trick'     => '元奇術師',
    'copied_soul'      => '元覚醒者',
    'copied_teller'    => '元夢語部',
    'lost_ability'     => '能力喪失');

  //役職の省略名 (過去ログ用)
  var $short_role_list = array(
    'human'              => '村',
    'elder'              => '老',
    'saint'              => '聖',
    'executor'           => '執',
    'escaper'            => '逃',
    'suspect'            => '不審',
    'unconscious'        => '無',
    'mage'               => '占',
    'soul_mage'          => '魂',
    'psycho_mage'        => '心占',
    'sex_mage'           => '雛占',
    'voodoo_killer'      => '陰陽',
    'dummy_mage'         => '夢見',
    'necromancer'        => '霊',
    'soul_necromancer'   => '雲',
    'yama_necromancer'   => '閻',
    'dummy_necromancer'  => '夢枕',
    'medium'             => '巫',
    'priest'             => '司祭',
    'bishop_priest'      => '司教',
    'crisis_priest'      => '預',
    'revive_priest'      => '天人',
    'guard'              => '狩',
    'poison_guard'       => '騎',
    'fend_guard'         => '忍',
    'reporter'           => '聞',
    'anti_voodoo'        => '厄',
    'dummy_guard'        => '夢守',
    'common'             => '共',
    'detective_common'   => '探',
    'trap_common'        => '策',
    'ghost_common'       => '亡',
    'dummy_common'       => '夢共',
    'poison'             => '毒',
    'strong_poison'      => '強毒',
    'incubate_poison'    => '潜毒',
    'guide_poison'       => '誘毒',
    'chain_poison'       => '連毒',
    'dummy_poison'       => '夢毒',
    'poison_cat'         => '猫',
    'revive_cat'         => '仙狸',
    'sacrifice_cat'      => '猫神',
    'pharmacist'         => '薬',
    'cure_pharmacist'    => '河',
    'assassin'           => '暗',
    'doom_assassin'      => '死神',
    'reverse_assassin'   => '反魂',
    'eclipse_assassin'   => '蝕暗',
    'mind_scanner'       => '悟',
    'evoke_scanner'      => 'イ',
    'whisper_scanner'    => '囁騒',
    'howl_scanner'       => '吠騒',
    'telepath_scanner'   => '念騒',
    'jealousy'           => '橋',
    'poison_jealousy'    => '毒橋',
    'doll'               => '上海',
    'poison_doll'        => '鈴蘭',
    'friend_doll'        => '仏蘭',
    'doll_master'        => '人遣',
    'wolf'               => '狼',
    'boss_wolf'          => '白狼',
    'gold_wolf'          => '金狼',
    'phantom_wolf'       => '幻狼',
    'cursed_wolf'        => '呪狼',
    'wise_wolf'          => '賢狼',
    'poison_wolf'        => '毒狼',
    'resist_wolf'        => '抗狼',
    'blue_wolf'          => '蒼狼',
    'emerald_wolf'       => '翠狼',
    'sex_wolf'           => '雛狼',
    'tongue_wolf'        => '舌狼',
    'possessed_wolf'     => '憑狼',
    'elder_wolf'         => '古狼',
    'sirius_wolf'        => '天狼',
    'cute_wolf'          => '萌狼',
    'scarlet_wolf'       => '紅狼',
    'silver_wolf'        => '銀狼',
    'mad'                => '狂',
    'fanatic_mad'        => '狂信',
    'whisper_mad'        => '囁狂',
    'jammer_mad'         => '月兎',
    'voodoo_mad'         => '呪狂',
    'corpse_courier_mad' => '火車',
    'agitate_mad'        => '扇',
    'miasma_mad'         => '蜘',
    'dream_eater_mad'    => '獏',
    'trap_mad'           => '罠',
    'possessed_mad'      => '犬',
    'fox'                => '狐',
    'white_fox'          => '白狐',
    'black_fox'          => '黒狐',
    'gold_fox'           => '金狐',
    'phantom_fox'        => '幻狐',
    'poison_fox'         => '管狐',
    'blue_fox'           => '蒼狐',
    'emerald_fox'        => '翠狐',
    'voodoo_fox'         => '九尾',
    'revive_fox'         => '仙狐',
    'possessed_fox'      => '憑狐',
    'cursed_fox'         => '天狐',
    'elder_fox'          => '古狐',
    'cute_fox'           => '萌狐',
    'scarlet_fox'        => '紅狐',
    'silver_fox'         => '銀狐',
    'child_fox'          => '子狐',
    'sex_fox'            => '雛狐',
    'cupid'              => 'QP',
    'self_cupid'         => '求愛',
    'moon_cupid'         => '姫',
    'mind_cupid'         => '女神',
    'triangle_cupid'     => '小悪',
    'angel'              => '天使',
    'rose_angel'         => '薔天',
    'lily_angel'         => '百天',
    'exchange_angel'     => '魂移',
    'ark_angel'          => '大天',
    'quiz'               => 'GM',
    'chiroptera'         => '蝙',
    'poison_chiroptera'  => '毒蝙',
    'cursed_chiroptera'  => '呪蝙',
    'boss_chiroptera'    => '大蝙',
    'elder_chiroptera'   => '古蝙',
    'dummy_chiroptera'   => '夢愛',
    'fairy'              => '妖精',
    'spring_fairy'       => '春精',
    'summer_fairy'       => '夏精',
    'autumn_fairy'       => '秋精',
    'winter_fairy'       => '冬精',
    'light_fairy'        => '光精',
    'dark_fairy'         => '闇精',
    'mirror_fairy'       => '鏡精',
    'mania'              => 'マ',
    'trick_mania'        => '奇',
    'soul_mania'         => '覚醒',
    'unknown_mania'      => '鵺',
    'dummy_mania'        => '夢語',
    'chicken'            => '酉',
    'rabbit'             => '卯',
    'perverseness'       => '邪',
    'flattery'           => '胡麻',
    'celibacy'           => '独',
    'impatience'         => '短',
    'nervy'              => '信',
    'febris'             => '熱',
    'death_warrant'      => '宣',
    'panelist'           => '解',
    'liar'               => '嘘',
    'invisible'          => '光迷',
    'rainbow'            => '虹迷',
    'weekly'             => '曜迷',
    'grassy'             => '草迷',
    'side_reverse'       => '鏡迷',
    'line_reverse'       => '天迷',
    'gentleman'          => '紳',
    'lady'               => '淑',
    'authority'          => '権',
    'random_voter'       => '気',
    'rebel'              => '反',
    'watcher'            => '傍',
    'decide'             => '決',
    'plague'             => '疫',
    'good_luck'          => '幸',
    'bad_luck'           => '不運',
    'upper_luck'         => '雑草',
    'downer_luck'        => '一発',
    'random_luck'        => '波乱',
    'star'               => '人気',
    'disfavor'           => '不人',
    'strong_voice'       => '大',
    'normal_voice'       => '不',
    'weak_voice'         => '小',
    'upper_voice'        => '拡声',
    'downer_voice'       => '覆',
    'inside_voice'       => '内弁',
    'outside_voice'      => '外弁',
    'random_voice'       => '臆',
    'no_last_words'      => '筆',
    'blinder'            => '目',
    'earplug'            => '耳',
    'speaker'            => '集音',
    'silent'             => '無口',
    'mower'              => '草刈',
    'mind_read'          => '漏',
    'mind_evoke'         => '口寄',
    'mind_open'          => '公',
    'mind_receiver'      => '受',
    'mind_friend'        => '鳴',
    'mind_sympathy'      => '感',
    'mind_lonely'        => '逸',
    'lovers'             => '恋',
    'challenge_lovers'   => '難',
    'copied'             => '元マ',
    'copied_trick'       => '元奇',
    'copied_soul'        => '元覚',
    'copied_teller'      => '元語',
    'lost_ability'       => '失');

  //メイン役職のグループリスト (役職 => 所属グループ)
  // このリストの並び順に strpos で判別する (毒系など、順番依存の役職があるので注意)
  var $main_role_group_list = array(
    'wolf' => 'wolf',
    'mad' => 'mad',
    'child_fox' => 'child_fox', 'sex_fox' => 'child_fox',
    'fox' => 'fox',
    'cupid' => 'cupid',
    'angel' => 'angel',
    'quiz' => 'quiz',
    'chiroptera' => 'chiroptera',
    'fairy' => 'fairy',
    'mage' => 'mage', 'voodoo_killer' => 'mage',
    'necromancer' => 'necromancer', 'medium' => 'necromancer',
    'priest' => 'priest',
    'guard' => 'guard', 'anti_voodoo' => 'guard', 'reporter' => 'guard',
    'common' => 'common',
    'cat' => 'poison_cat',
    'jealousy' => 'jealousy',
    'doll' => 'doll',
    'poison' => 'poison',
    'pharmacist' => 'pharmacist',
    'assassin' => 'assassin',
    'scanner' => 'mind_scanner',
    'mania' => 'mania');

  //サブ役職のグループリスト (CSS のクラス名 => 所属役職)
  var $sub_role_group_list = array(
    'lovers'       => array('lovers', 'challenge_lovers'),
    'mind'         => array('mind_read', 'mind_open', 'mind_receiver', 'mind_friend', 'mind_sympathy',
			    'mind_evoke', 'mind_lonely'),
    'mania'        => array('copied', 'copied_trick', 'copied_soul', 'copied_teller'),
    'sudden-death' => array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience', 'nervy',
			    'celibacy', 'febris', 'death_warrant', 'panelist'),
    'convert'      => array('liar', 'invisible', 'rainbow', 'weekly', 'grassy', 'side_reverse',
			    'line_reverse', 'gentleman', 'lady'),
    'authority'    => array('authority', 'random_voter', 'rebel', 'watcher'),
    'decide'       => array('decide', 'plague', 'good_luck', 'bad_luck'),
    'luck'         => array('upper_luck', 'downer_luck', 'random_luck', 'star', 'disfavor'),
    'voice'        => array('strong_voice', 'normal_voice', 'weak_voice', 'upper_voice',
			    'downer_voice', 'inside_voice', 'outside_voice', 'random_voice'),
    'seal'         => array('no_last_words', 'blinder', 'earplug', 'speaker', 'silent', 'mower'),
    'human'        => array('lost_ability'));

  //-- その他 --//
  var $power_gm = false; //強権 GM モード (ON：true / OFF：false)
  var $random_message = false; //ランダムメッセージの挿入 (する：true / しない：false)

  //-- 関数 --//
  function GetRoleName($role, $short = false){
    if($short) return $this->short_role_list[$role];
    return ($this->main_role_list[$role] || $this->sub_role_list[$role]);
  }
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
