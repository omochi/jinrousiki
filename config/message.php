<?php
//-- 基本システムメッセージ --//
class Message{
  //-- room_manger.php --//
  //CreateRoom() : 村作成
  //身代わり君のコメント
  var $dummy_boy_comment = '僕はおいしくないよ';

  //身代わり君の遺言
  var $dummy_boy_last_words = '僕はおいしくないって言ったのに……';

  //-- user_manager.php --//
  //EntryUser() : ユーザ登録
  //入村メッセージ
  //var $entry_user = 'さんが村の集会場にやってきました'
  var $entry_user = 'さんが幻想郷入りしました';

  //-- game_view.php & OutputGameHTMLHeader() --//
  var $vote_announce = '時間がありません。投票してください'; //会話の制限時間切れ

  //-- game_functions.php --//
  //OutputRevoteList() : 再投票アナウンス
  var $revote = '再投票となりました'; //投票結果
  var $draw_announce = '再投票となると引き分けになります'; //引き分け告知

  //OutputTalkLog() : 会話、システムメッセージ出力
  var $objection = 'が「異議」を申し立てました'; //「異議」あり
  //var $game_start = 'はゲーム開始に投票しました' //ゲーム開始投票 (現在は不使用)
  var $kick_do          = 'に KICK 投票しました'; //KICK 投票
  var $vote_do          = 'に処刑投票しました'; //処刑投票
  var $wolf_eat         = 'に狙いをつけました'; //人狼の投票
  var $escape_do        = 'の周辺に逃亡しました'; //逃亡者の投票
  var $mage_do          = 'を占います'; //占い師の投票
  var $voodoo_killer_do = 'の呪いを祓います'; //陰陽師の投票
  var $jammer_do        = 'の占いを妨害します'; //月兎の投票
  var $trap_do          = 'の周辺に罠を仕掛けました'; //罠師の投票
  var $trap_not_do      = 'は罠設置を行いませんでした'; //罠師のキャンセル投票
  var $possessed_do     = 'に憑依します'; //犬神の投票
  var $possessed_not_do = 'は憑依を行いませんでした'; //犬神のキャンセル投票
  var $voodoo_do        = 'に呪いをかけます'; //呪術師の投票
  var $guard_do         = 'の護衛に付きました'; //狩人の投票
  var $anti_voodoo_do   = 'の厄を祓います'; //厄神の投票
  var $reporter_do      = 'を尾行しました'; //ブン屋の投票
  var $revive_do        = 'に蘇生処置をしました'; //猫又の投票
  var $revive_not_do    = 'は蘇生処置をしませんでした'; //猫又のキャンセル投票
  var $assassin_do      = 'に狙いをつけました'; //暗殺者の投票
  var $assassin_not_do  = 'は暗殺を行いませんでした'; //暗殺者のキャンセル投票
  var $mind_scanner_do  = 'の心を読みます'; //さとりの投票
  var $cupid_do         = 'に愛の矢を放ちました'; //キューピッドの投票
  var $fairy_do         = 'に悪戯しました'; //妖精の投票
  var $mania_do         = 'の能力を真似ることにしました'; //神話マニアの投票

  var $morning_header = '朝日が昇り'; //朝のヘッダー
  var $morning_footer = '日目の朝がやってきました'; //朝のフッター
  var $night = '日が落ち、暗く静かな夜がやってきました'; //夜
  var $dummy_boy = '◆身代わり君　'; //仮想GMモード用ヘッダー

  var $wolf_howl = 'アオォーン・・・'; //狼の遠吠え
  //var $common_talk = 'ヒソヒソ・・・'; //共有者の小声
  //var $common_talk = 'ですぅ・・・ですぅ・・・'; //共有者の小声
  var $common_talk = 'あーうー・・・あーうー・・・'; //共有者の小声
  var $howling = 'キィーーン・・・'; //スピーカーの音割れ効果音

  //OutputLastWords() : 遺言の表示
  var $lastwords = '夜が明けると前の日に亡くなった方の遺言書が見つかりました';

  //OutoutDeadManType() : 死因の表示
  //var $vote_killed      = 'は投票の結果処刑されました'; //吊り
  var $vote_killed        = 'を弾幕ごっこ (投票) の結果ぴちゅーん (処刑) しました';
  //var $deadman           = 'は無残な姿で発見されました'; //全員に表示されるメッセージ
  var $deadman            = 'は無残な負け犬の姿で発見されました';
  var $wolf_killed        = 'は人狼の餌食になったようです'; //人狼の襲撃
  var $hungry_wolf_killed = 'は餓狼の餌食になったようです'; //餓狼の襲撃
  var $possessed          = 'は誰かに憑依したようです'; //憑依
  var $possessed_targeted = 'は憑狼に憑依されたようです'; //憑狼の襲撃
  var $possessed_reset    = 'は憑依から開放されたようです'; //憑依リセット
  var $dream_killed       = 'は獏の餌食になったようです'; //獏の襲撃
  var $trapped            = 'は罠にかかって死亡したようです'; //罠
  var $fox_dead           = '(妖狐) は占い師に呪い殺されたようです'; //狐呪殺
  var $cursed             = 'は呪詛に呪い殺されたようです'; //呪返し
  var $hunted             = 'は狩人に狩られたようです'; //狩人の狩り
  var $reporter_duty      = '(ブン屋) は人外を尾行してしまい、襲われたようです'; //ブン屋の殉職
  var $poison_dead        = 'は毒に冒され死亡したようです'; //埋毒者の道連れ
  var $assassin_killed    = 'は暗殺されたようです'; //暗殺者の襲撃
  var $priest_returned    = 'は天に帰ったようです'; //天人の帰還
  var $revive_success     = 'は生き返りました'; //蘇生成功
  var $revive_failed      = 'の蘇生に失敗したようです'; //蘇生失敗
  var $sacrifice          = 'は誰かの犠牲となって死亡したようです'; //身代わり死
  var $lovers_followed    = 'は恋人の後を追い自殺しました'; //恋人の後追い自殺
  var $vote_sudden_death  = 'はショック死しました'; //投票系ショック死
  var $chicken            = 'は小心者だったようです'; //小心者
  var $rabbit             = 'はウサギだったようです'; //ウサギ
  var $perverseness       = 'は天邪鬼だったようです'; //天邪鬼
  var $flattery           = 'はゴマすりだったようです'; //ゴマすり
  var $impatience         = 'は短気だったようです'; //短気
  var $nervy              = 'は自信家だったようです'; //自信家
  var $celibacy           = 'は独身貴族だったようです'; //独身貴族
  var $panelist           = 'は解答者 (不正解) だったようです'; //解答者
  var $jealousy           = '(恋人) は橋姫に妬まれたようです'; //橋姫の妬み返し
  var $agitated           = 'は扇動に巻き込まれたようです'; //扇動者
  var $febris             = 'は熱病にかかったようです'; //熱病
  var $warrant            = 'は死の宣告を受けたようです'; //死の宣告
  var $challenge          = 'は難題を解けなかったようです'; //難題
  var $flowered_a         = 'の頭の上に松の花が咲きました'; //花妖精
  var $flowered_b         = 'の頭の上に梅の花が咲きました'; //花妖精
  var $flowered_c         = 'の頭の上に桜の花が咲きました'; //花妖精
  var $flowered_d         = 'の頭の上に藤の花が咲きました'; //花妖精
  var $flowered_e         = 'の頭の上に菖蒲の花が咲きました'; //花妖精
  var $flowered_f         = 'の頭の上に牡丹の花が咲きました'; //花妖精
  var $flowered_g         = 'の頭の上に萩の花が咲きました'; //花妖精
  var $flowered_h         = 'の頭の上に芒の花が咲きました'; //花妖精
  var $flowered_i         = 'の頭の上に菊の花が咲きました'; //花妖精
  var $flowered_j         = 'の頭の上に紅葉の花が咲きました'; //花妖精
  var $flowered_k         = 'の頭の上に柳の花が咲きました'; //花妖精
  var $flowered_l         = 'の頭の上に桐の花が咲きました'; //花妖精
  var $flowered_m         = 'の頭の上に鬼灯の花が咲きました'; //花妖精
  var $flowered_n         = 'の頭の上に達磨草の花が咲きました'; //花妖精
  var $flowered_o         = 'の頭の上に福寿草の花が咲きました'; //花妖精
  var $flowered_p         = 'の頭の上に山茶花の花が咲きました'; //花妖精
  var $flowered_q         = 'の頭の上に彼岸花の花が咲きました'; //花妖精
  var $flowered_r         = 'の頭の上に鈴蘭の花が咲きました'; //花妖精
  var $flowered_s         = 'の頭の上に向日葵の花が咲きました'; //花妖精
  var $flowered_t         = 'の頭の上に優曇華の花が咲きました'; //花妖精
  var $flowered_u         = 'の頭の上に桃の花が咲きました'; //花妖精
  var $flowered_v         = 'の頭の上に椿の花が咲きました'; //花妖精
  var $flowered_w         = 'の頭の上に鳳仙花の花が咲きました'; //花妖精
  var $flowered_x         = 'の頭の上に薔薇の花が咲きました'; //花妖精
  var $flowered_y         = 'の頭の上に百合の花が咲きました'; //花妖精
  var $flowered_z         = 'の頭の上に仙人掌の花が咲きました'; //花妖精

  //OutputAbility() : 能力の表示
  var $ability_dead = 'アナタは息絶えました・・・'; //死んでいる場合

  //CheckNightVote() : 夜の投票
  var $ability_vote             = '処刑する人を選択してください'; //昼の処刑投票
  var $ability_wolf_eat         = '喰い殺す人を選択してください'; //人狼
  var $ability_escape_do        = '逃亡する先を選択してください'; //逃亡者
  var $ability_mage_do          = '占う人を選択してください'; //占い師系
  var $ability_voodoo_killer_do = '呪いを祓う人を選択してください'; //陰陽師
  var $ability_jammer_do        = '占いを妨害する人を選択してください'; //月兎
  var $ability_trap_do          = '罠を設置する人を選択してください'; //罠師
  var $ability_possessed_do     = '憑依する人を選択してください'; //犬神
  var $ability_dream_eat        = '夢を喰べる人を選択してください'; //獏
  var $ability_voodoo_do        = '呪いをかける人を選択してください'; //呪術師・九尾
  var $ability_guard_do         = '護衛する人を選択してください'; //狩人系
  var $ability_anti_voodoo_do   = '厄を祓う人を選択してください'; //厄神
  var $ability_reporter_do      = '尾行する人を選択してください'; //ブン屋
  var $ability_revive_do        = '蘇生する人を選択してください'; //猫又
  var $ability_assassin_do      = '暗殺する人を選択してください'; //暗殺者
  var $ability_mind_scanner_do  = '心を読む人を選択してください'; //さとり
  var $ability_cupid_do         = '結びつける人を選択してください'; //キューピッド
  var $ability_fairy_do         = '悪戯する人を選択してください'; //妖精
  var $ability_mania_do         = '能力を真似る人を選択してください'; //神話マニア

  //-- game_play.php --//
  //CheckSilence()
  var $silence = 'ほどの沈黙が続いた'; //沈黙で時間経過 (会話で時間経過制)
  //突然死の警告メッセージ
  // var $sudden_death_announce = '投票完了されない方は死して地獄へ堕ちてしまいます';
  var $sudden_death_announce = '投票完了されない方はスキマ送りされてしまいます';
  // var $sudden_death_time = '突然死になるまで後：'; //突然死発動まで
  var $sudden_death_time = 'スキマ送りされるまで後：';
  // var $sudden_death = 'さんは突然お亡くなりになられました'; //突然死
  var $sudden_death = 'さんは紫に連れ去られました';

  //投票リセット
  var $vote_reset = '＜投票がリセットされました　再度投票してください＞';

  //発言置換系役職
  var $cute_wolf = ''; //萌狼・不審者 (空なら狼の遠吠えになる)
  // var $gentleman_header = "お待ち下さい。\n";  //紳士 (前半)
  // var $gentleman_footer = 'さん、ハンケチーフを落としておりますぞ。'; //紳士 (後半)
  var $gentleman_header = "お待ち下さい。\nあぁ……";  //紳士 (前半)
  var $gentleman_footer = '様の残り湯、美味にございます……。'; //紳士 (後半)
  //var $lady_header = "お待ちなさい！\n"; //淑女 (前半)
  //var $lady_footer = '、タイが曲がっていてよ。'; //淑女 (後半)
  var $lady_header = "こんなのがいいの！？\n"; //淑女 (前半)
  var $lady_footer = '！　そこに直って！　わたくしの足をなめなさい！！'; //淑女 (後半)

  //-- game_vote.php --//
  //Kick で村から去った人
  var $kick_out = 'さんは席をあけわたし、村から去りました';

  //CheckVoteGameStart()
  // var $chaos = '闇鍋モードでは配役は明かされません'; //闇鍋村の配役通知
  var $chaos = '闇鍋モードだから配役は秘密☆カオスを楽しんでね♪';

  //-- InsertRandomMessage() --//
  //GameConfig->random_message を true にすると
  //ここに入れたメッセージがランダムに表示される
  var $random_message_list = array();
}

//-- ゲームオプション名 --//
class GameOptionMessage{
  var $room_name            = '村の名前';
  var $room_comment         = '村についての説明';
  var $max_user             = '最大人数';
  var $wish_role            = '役割希望制';
  var $real_time            = 'リアルタイム制';
  var $dummy_boy            = '初日の夜は身代わり君';
  var $gm_login             = '身代わり君は GM';
  var $gerd                 = 'ゲルト君モード';
  var $open_vote            = '投票した票数を公表する';
  var $open_day             = 'オープニングあり';
  var $not_open_cast        = '霊界で配役を公開しない';
  var $auto_open_cast       = '自動で霊界の配役を公開する';
  var $poison               = '埋毒者登場';
  var $assassin             = '暗殺者登場';
  var $boss_wolf            = '白狼登場';
  var $poison_wolf          = '毒狼登場';
  var $possessed_wolf       = '憑狼登場';
  var $sirius_wolf          = '天狼登場';
  var $cupid                = 'キューピッド登場';
  var $medium               = '巫女登場';
  var $mania                = '神話マニア登場';
  var $decide               = '決定者登場';
  var $authority            = '権力者登場';
  var $liar                 = '狼少年村';
  var $gentleman            = '紳士・淑女村';
  var $sudden_death         = '虚弱体質村';
  var $perverseness         = '天邪鬼村';
  var $full_mania           = '神話マニア村';
  var $detective            = '探偵村';
  var $festival             = 'お祭り村';
  var $chaos                = '闇鍋モード';
  var $chaosfull            = '真・闇鍋モード';
  var $chaos_hyper          = '超・闇鍋モード';
  var $chaos_open_cast      = '配役を通知する';
  var $chaos_open_cast_camp = '陣営を通知する';
  var $chaos_open_cast_role = '役職を通知する';
  var $secret_sub_role      = 'サブ役職を表示しない';
  var $no_sub_role          = 'サブ役職をつけない';
  var $quiz                 = 'クイズ村';
  var $duel                 = '決闘村';
}

//-- ゲームオプション名の説明 --//
class GameOptionCaptionMessage{
  var $max_user             = '配役は<a href="rule.php">ルール</a>を確認して下さい';
  var $wish_role            = '希望の役割を指定できますが、なれるかは運です';
  var $real_time            = '制限時間が実時間で消費されます';
  var $no_dummy_boy         = '身代わり君なし';
  var $dummy_boy            = '身代わり君あり(初日の夜、身代わり君が狼に食べられます)';
  var $gm_login_header      = '仮想 GM が身代わり君としてログインします';
  var $gm_login_footer      = 'ログインユーザ名は「dummy_boy」です。GM は入村直後に必ず名乗ってください';
  var $gerd                 = '役職が村人固定になります (村人が出現している場合のみ有効)';
  var $open_vote            = '「権力者」などのサブ役職が分かりやすくなります';
  var $open_day             = 'ゲームが1日目の「昼」からスタートします';
  var $no_close_cast        = '常時公開 (蘇生能力は無効です)';
  var $not_open_cast        = '常時非公開 (誰がどの役職なのかが公開されません。蘇生能力は有効です)';
  var $auto_open_cast       = '自動公開 (蘇生能力者などが能力を持っている間だけ霊界が非公開になります)';
  var $poison               = '処刑されたり狼に食べられた場合、道連れにします。[村人2→埋毒1、人狼1]';
  var $assassin             = '夜に村人一人を殺すことができます。[村人2→暗殺者1、人狼1]';
  var $boss_wolf            = '占い結果が「村人」、霊能結果が「白狼」と表示される狼です。[人狼1→白狼1]';
  var $poison_wolf          = '吊られた時にランダムで村人一人を巻き添えにする狼です。<br>　　　[人狼1→毒狼1、村人1→薬師1]';
  var $possessed_wolf       = '噛んだ人に憑依して乗っ取ってしまう狼です。[人狼1→憑狼1]';
  var $sirius_wolf          = '仲間が減ると特殊能力が発現する狼です。[人狼1→天狼1]';
  var $cupid                = '初日夜に選んだ相手を恋人にします。恋人となった二人は勝利条件が変化します<br>　　　[村人1→キューピッド1]';
  var $medium               = '突然死した人の所属陣営が分かる特殊な霊能者です。[村人2→巫女1、女神1]';
  var $mania                = '初日夜に他の村人の役職をコピーする特殊な役職です。[村人1→神話マニア1]';
  var $decide               = '投票が同数の時、決定者の投票先が優先されます。[兼任]';
  var $authority            = '投票の票数が二票になります。[兼任]';
  var $liar                 = 'ランダムで「狼少年」がつきます';
  var $gentleman            = '全員に性別に応じた「紳士」「淑女」がつきます';
  var $sudden_death         = '全員に投票でショック死するサブ役職のどれかがつきます';
  var $perverseness         = '全員に「天邪鬼」がつきます。一部のサブ役職系オプションが強制オフになります';
  var $full_mania           = '「村人」が全員「神話マニア」に入れ替わります';
  var $detective            = '「探偵」が登場し、初日の夜に全員に公表されます';
  var $festival             = '管理人がカスタムする特殊設定です';
  var $no_chaos             = '通常人狼';
  var $chaos                = '控えめな闇鍋モードです';
  var $chaosfull            = 'そこそこ歯応えのある真・闇鍋モードです';
  var $chaos_hyper          = '全く容赦のない超・闇鍋モードです';
  var $chaos_not_open_cast  = '通知無し';
  var $chaos_open_cast_camp = '陣営通知 (陣営毎の合計を通知)';
  var $chaos_open_cast_role = '役職通知 (役職の種類別に合計を通知)';
  var $chaos_open_cast_full = '完全通知 (通常村相当)';
  var $secret_sub_role      = 'サブ役職が分からなくなります：闇鍋モード専用オプション';
  var $no_sub_role          = 'サブ役職をつけません：闇鍋モード専用オプション';
  var $quiz                 = '身代わり君が「出題者」になってクイズを出します';
  var $duel                 = '霊界公開設定によって配役が変化する特殊村です';
}

//-- 村・本人の勝敗結果 --//
class VictoryMessage{
  //村人勝利
  var $human = '[村人勝利] 村人たちは人狼の血を根絶することに成功しました';

  //人狼・狂人勝利
  var $wolf = '[人狼・狂人勝利] 最後の一人を食い殺すと人狼達は次の獲物を求めて村を後にした';

  //妖狐勝利 (村人勝利版)
  var $fox1 = '[妖狐勝利] 人狼がいなくなった今、我の敵などもういない';

  //妖狐勝利 (人狼勝利版)
  var $fox2 = '[妖狐勝利] マヌケな人狼どもを騙すことなど容易いことだ';

  //恋人・キューピッド勝利
  var $lovers = '[恋人・キューピッド勝利] 愛の前には何者も無力だったのでした';

  //出題者勝利
  var $quiz = '[出題者勝利] 真の解答者にはまだ遠い……修行あるのみ';

  //出題者死亡
  var $quiz_dead = '[引き分け] 何という事だ！このままでは決着が付かないぞ！';

  //引き分け
  var $draw = '[引き分け] 引き分けとなりました';

  //全滅
  var $vanish = '[引き分け] そして誰も居なくなった……';

  //途中廃村
  var $unfinished = '[引き分け] 霧が濃くなって何も見えなくなりました……';

  //廃村
  var $none = '過疎が進行して人がいなくなりました';

  var $self_win  = 'あなたは勝利しました'; //本人勝利
  var $self_lose = 'あなたは敗北しました'; //本人敗北
  var $self_draw = '引き分けとなりました'; //引き分け
}

//-- 投票画面専用メッセージ --//
class VoteMessage{
  //OutputVoteBeforeGame()
  var $kick_do    = '対象をキックするに一票'; //Kick 投票ボタン
  var $game_start = 'ゲームを開始するに一票'; //ゲーム開始ボタン

  //OutputVoteDay()
  var $vote_do = '対象を処刑するに一票'; //処刑投票ボタン

  //OutputVoteNight()
  //投票ボタン
  var $wolf_eat         = '対象を喰い殺す (先着)'; //人狼
  var $escape_do        = '対象の周辺に逃亡する'; //逃亡者
  var $mage_do          = '対象を占う'; //占い師
  var $voodoo_killer_do = '対象の呪いを祓う'; //陰陽師
  var $guard_do         = '対象を護衛する'; //狩人
  var $anti_voodoo_do   = '対象の厄を祓う'; //厄神
  var $reporter_do      = '対象を尾行する'; //ブン屋
  var $revive_do        = '対象を蘇生する'; //猫又
  var $revive_not_do    = '誰も蘇生しない'; //猫又(キャンセル)
  var $assassin_do      = '対象を暗殺する'; //暗殺者
  var $assassin_not_do  = '誰も暗殺しない'; //暗殺者(キャンセル)
  var $mind_scanner_do  = '対象の心を読む'; //さとり
  var $voodoo_do        = '対象に呪いをかける'; //呪術師
  var $jammer_do        = '対象の占いを妨害する'; //月兎
  var $dream_eat        = '対象の夢を喰う'; //獏
  var $trap_do          = '対象の周辺に罠を設置する'; //罠師
  var $trap_not_do      = '罠を設置しない'; //罠師(キャンセル)
  var $possessed_do     = '対象に憑依する'; //犬神
  var $possessed_not_do = '誰にも憑依しない'; //犬神(キャンセル)
  var $cupid_do         = '対象に愛の矢を放つ'; //キューピッド
  var $fairy_do         = '対象に悪戯する'; //妖精
  var $mania_do         = '対象を真似る'; //神話マニア
  var $revive_refuse    = '蘇生を辞退する'; //蘇生辞退
}
