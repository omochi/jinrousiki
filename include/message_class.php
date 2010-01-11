<?php
// システムメッセージ格納クラス
class Message{
  //-- room_manger.php --//
  //CreateRoom() : 村作成
  //身代わり君のコメント
  var $dummy_boy_comment = '僕はおいしくないよ';

  //身代わり君の遺言
  var $dummy_boy_last_words = '僕はおいしくないって言ったのに……';

  var $game_option_wish_role            = '役割希望制';
  var $game_option_real_time            = 'リアルタイム制';
  var $game_option_dummy_boy            = '初日の夜は身代わり君';
  var $game_option_gm_login             = '身代わり君は GM';
  var $game_option_open_vote            = '投票した票数を公表する';
  var $game_option_not_open_cast        = '霊界で配役を公開しない';
  var $game_option_decide               = '決定者登場';
  var $game_option_authority            = '権力者登場';
  var $game_option_poison               = '埋毒者登場';
  var $game_option_cupid                = 'キューピッド登場';
  var $game_option_boss_wolf            = '白狼登場';
  var $game_option_poison_wolf          = '毒狼登場';
  var $game_option_mania                = '神話マニア登場';
  var $game_option_medium               = '巫女登場';
  var $game_option_liar                 = '狼少年村';
  var $game_option_gentleman            = '紳士・淑女村';
  var $game_option_sudden_death         = '虚弱体質村';
  var $game_option_perverseness         = '天邪鬼村';
  var $game_option_full_mania           = '神話マニア村';
  var $game_option_chaos                = '闇鍋モード';
  var $game_option_chaosfull            = '真・闇鍋モード';
  var $game_option_chaos_open_cast      = '配役を通知する';
  var $game_option_chaos_open_cast_camp = '陣営を通知する';
  var $game_option_chaos_open_cast_role = '役職を通知する';
  var $game_option_secret_sub_role      = 'サブ役職を表示しない';
  var $game_option_no_sub_role          = 'サブ役職をつけない';
  var $game_option_quiz                 = 'クイズ村';
  var $game_option_duel                 = '決闘村';

  //-- user_manager.php --//
  //EntryUser() : ユーザ登録
  //入村メッセージ
  // var $entry_user = 'さんが村の集会場にやってきました'
  var $entry_user = 'が幻想郷入りしました';
  // var $entry_user = 'が村の寺子屋にやってきました';

  //-- game_view.php & OutputGameHTMLHeader() --//
  var $vote_announce = '時間がありません。投票してください'; //会話の制限時間切れ

  //-- game_functions.php --//
  //OutputVictory() : 村・本人の勝敗結果
  //村人勝利
  var $victory_human = '[村人勝利] 村人たちは人狼の血を根絶することに成功しました';

  //人狼・狂人勝利
  var $victory_wolf = '[人狼・狂人勝利] 最後の一人を食い殺すと人狼達は次の獲物を求めて村を後にした';

  //妖狐勝利 (村人勝利版)
  var $victory_fox1 = '[妖狐勝利] 人狼がいなくなった今、我の敵などもういない';

  //妖狐勝利 (人狼勝利版)
  var $victory_fox2 = '[妖狐勝利] マヌケな人狼どもを騙すことなど容易いことだ';

  //恋人・キューピッド勝利
  var $victory_lovers = '[恋人・キューピッド勝利] 愛の前には何者も無力だったのでした';

  //出題者勝利
  var $victory_quiz = '[出題者勝利] 真の解答者にはまだ遠い……修行あるのみ';

  //出題者死亡
  var $victory_quiz_dead = '[引き分け] 何という事だ！このままでは決着が付かないぞ！';

  //引き分け
  var $victory_draw = '[引き分け] 引き分けとなりました';

  //全滅
  var $victory_vanish = '[引き分け] そして誰も居なくなった……';

  //廃村
  var $victory_none = '過疎が進行して人がいなくなりました';

  var $win  = 'あなたは勝利しました'; //本人勝利
  var $lose = 'あなたは敗北しました'; //本人敗北
  var $draw = '引き分けとなりました'; //引き分け

  //OutputRevoteList() : 再投票アナウンス
  var $revote = '再投票となりました'; //投票結果
  var $draw_announce = '再投票となると引き分けになります'; //引き分け告知

  //OutputTalkLog() : 会話、システムメッセージ出力
  var $objection = 'が「異議」を申し立てました'; //「異議」あり
  // var $game_start = 'はゲーム開始に投票しました' //ゲーム開始投票 //現在は不使用
  var $kick_do          = 'に KICK 投票しました'; //KICK 投票
  var $vote_do          = 'に処刑投票しました'; //処刑投票
  var $wolf_eat         = 'に狙いをつけました'; //人狼の投票
  var $mage_do          = 'を占います'; //占い師の投票
  var $voodoo_killer_do = 'の呪いを祓います'; //陰陽師の投票
  var $jammer_do        = 'の占いを妨害します'; //月兎の投票
  var $trap_do          = 'の周辺に罠を仕掛けました'; //罠師の投票
  var $trap_not_do      = 'は罠設置を行いませんでした'; //罠師のキャンセル投票
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
  var $mania_do         = 'の能力を真似ることにしました'; //神話マニアの投票

  var $morning_header = '朝日が昇り'; //朝のヘッダー
  var $morning_footer = '日目の朝がやってきました'; //朝のフッター
  var $night = '日が落ち、暗く静かな夜がやってきました'; //夜
  var $dummy_boy = '身代わり君：'; //仮想GMモード用ヘッダー

  var $wolf_howl = 'アオォーン・・・'; //狼の遠吠え
  // var $common_talk = 'ヒソヒソ・・・'; //共有者の小声
  var $common_talk = 'あーうー・・・あーうー・・・'; //共有者の小声
  var $howling = 'キィーーン・・・'; //スピーカーの音割れ効果音

  //OutputLastWords() : 遺言の表示
  var $lastwords = '夜が明けると前の日に亡くなった方の遺言書が見つかりました';

  //OutoutDeadManType() : 死因の表示
  // var $vote_killed      = 'は投票の結果処刑されました'; //吊り
  var $vote_killed       = 'を弾幕ごっこ (投票) の結果ぴちゅーん (処刑) しました';
  // var $deadman           = 'は無残な姿で発見されました'; //全員に表示されるメッセージ
  var $deadman           = 'は無残な負け犬の姿で発見されました';
  var $wolf_killed       = 'は人狼の餌食になったようです'; //人狼の襲撃
  var $trapped           = 'は罠にかかって死亡したようです'; //罠
  var $fox_dead          = '(妖狐) は占い師に呪い殺されたようです'; //狐呪殺
  var $cursed            = 'は呪詛に呪い殺されたようです'; //呪返し
  var $hunted            = 'は狩人に狩られたようです'; //狩人の狩り
  var $reporter_duty     = '(ブン屋) は人外を尾行してしまい、襲われたようです'; //ブン屋の殉職
  var $poison_dead       = 'は毒に冒され死亡したようです'; //埋毒者の道連れ
  var $dream_killed      = 'は獏の餌食になったようです'; //獏の襲撃
  var $assassin_killed   = 'は暗殺されたようです'; //暗殺者の襲撃
  var $revive_success    = 'は生き返りました'; //蘇生成功
  var $revive_failed     = 'の蘇生に失敗したようです'; //蘇生失敗
  var $lovers_followed   = 'は恋人の後を追い自殺しました'; //恋人の後追い自殺
  var $vote_sudden_death = 'はショック死しました'; //投票系ショック死
  var $chicken           = 'は小心者だったようです'; //小心者
  var $rabbit            = 'はウサギだったようです'; //ウサギ
  var $perverseness      = 'は天邪鬼だったようです'; //天邪鬼
  var $flattery          = 'はゴマすりだったようです'; //ゴマすり
  var $impatience        = 'は短気だったようです'; //短気
  var $jealousy          = '(恋人) は橋姫に妬まれたようです'; //橋姫の妬み返し
  var $celibacy          = 'は独身貴族だったようです'; //独身貴族
  var $panelist          = 'は解答者 (不正解) だったようです'; //解答者

  //OutputAbility() : 能力の表示
  var $ability_dead     = 'アナタは息絶えました・・・'; //死んでいる場合

  //CheckNightVote() : 夜の投票
  var $ability_vote             = '処刑する人を選択してください'; //昼の処刑投票
  var $ability_wolf_eat         = '喰い殺す人を選択してください'; //人狼
  var $ability_mage_do          = '占う人を選択してください'; //占い師系
  var $ability_voodoo_killer_do = '呪いを祓う人を選択してください'; //陰陽師
  var $ability_jammer_do        = '占いを妨害する人を選択してください'; //邪魔狂人
  var $ability_trap_do          = '罠を設置する人を選択してください'; //罠師
  var $ability_dream_eat        = '夢を喰べる人を選択してください'; //獏
  var $ability_voodoo_do        = '呪いをかける人を選択してください'; //呪術師・九尾
  var $ability_guard_do         = '護衛する人を選択してください'; //狩人系
  var $ability_anti_voodoo_do   = '厄を祓う人を選択してください'; //厄神
  var $ability_reporter_do      = '尾行する人を選択してください'; //ブン屋
  var $ability_revive_do        = '蘇生する人を選択してください'; //猫又
  var $ability_assassin_do      = '暗殺する人を選択してください'; //暗殺者
  var $ability_mind_scanner_do  = '心を読む人を選択してください'; //さとり
  var $ability_cupid_do         = '結びつける二人を選択してください'; //キューピッド
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

  //OutputVoteBeforeGame()
  var $submit_kick_do    = '対象をキックするに一票'; //Kick 投票ボタン
  var $submit_game_start = 'ゲームを開始するに一票'; //ゲーム開始ボタン

  //OutputVoteDay()
  var $submit_vote_do = '対象を処刑するに一票'; //処刑投票ボタン

  //OutputVoteNight()
  //投票ボタン
  var $submit_wolf_eat         = '対象を喰い殺す (先着)'; //人狼
  var $submit_mage_do          = '対象を占う'; //占い師系
  var $submit_voodoo_killer_do = '対象の呪いを祓う'; //陰陽師
  var $submit_jammer_do        = '対象の占いを妨害する'; //月兎
  var $submit_dream_eat        = '対象の夢を喰う'; //獏
  var $submit_trap_do          = '対象の周辺に罠を設置する'; //罠師
  var $submit_trap_not_do      = '罠を設置しない'; //罠師(キャンセル)
  var $submit_voodoo_do        = '対象に呪いをかける'; //呪術師
  var $submit_guard_do         = '対象を護衛する'; //狩人系
  var $submit_anti_voodoo_do   = '対象の厄を祓う'; //厄神
  var $submit_reporter_do      = '対象を尾行する'; //ブン屋
  var $submit_revive_do        = '対象を蘇生する'; //猫又
  var $submit_revive_not_do    = '誰も蘇生しない'; //猫又(キャンセル)
  var $submit_assassin_do      = '対象を暗殺する'; //暗殺者系
  var $submit_assassin_not_do  = '誰も暗殺しない'; //暗殺者系(キャンセル)
  var $submit_mind_scanner_do  = '対象の心を読む'; //さとり
  var $submit_cupid_do         = '対象に愛の矢を放つ'; //キューピッド系
  var $submit_mania_do         = '対象を真似る'; //神話マニア

  //InsertRandomMessage()
  var $random_message_list = array(
    '開発者からのどうでもいい話 (1)：このメッセージスペースは管理者の宣伝用に作ったものです。',
    '開発者からのどうでもいい話 (2)：実は式神研発足当初に PHP をまともに書いた事がある人はいませんでした。',
    '開発者からのどうでもいい話 (3)：実は式神研発足当初に mysql をまともに扱える人はほとんどいませんでした。',
    '開発者からのどうでもいい話 (4)：闇鍋のコードは流石鯖の GM さんより頂いたものです。',
    '開発者からのどうでもいい話 (5)：式神研の闇鍋のコンセプトは「理不尽」「推理なんてさせない」「楽しんだ者勝ち」です。',
    '開発者からのどうでもいい話 (6)：式神研の発足当時のメインプログラマは三人ほどでした。',
    '開発者からのどうでもいい話 (7)：式神研の闇鍋役職はほぼ一人のプログラマが作ったものです。',
    '開発者からのどうでもいい話 (8)：「魂の占い師」にはモデルがいます。「魂」で次々人外を当てる凄い占い師を何度も演じたプレイヤーさんです。',
    '開発者からのどうでもいい話 (10)：「精神鑑定士」は対狂人専門の占い師としてデザインしたものです。他の役職と雰囲気を合わせる為に意図的に「カウンセラー」という名前を採用しませんでした。',
    '開発者からのどうでもいい話 (11)：一部で大人気の「ひよこ鑑定士」のアイディアは流石鯖の GM さんより頂いたものです。',
    '開発者からのどうでもいい話 (12)：「陰陽師」は対呪い能力者専門の占い師としてデザインしたものです。解呪成功メッセージを見るのは中々難しいでしょう。',
    '開発者からのどうでもいい話 (13)：「夢見人」の占いは夢の中で行われています。だから呪殺できないし、呪いも占い妨害も効かないのです。',
    '開発者からのどうでもいい話 (14)：「雲外鏡」にはモデルがいます。圧倒的信用度を誇る霊能者を何度も演じたプレイヤーさんです。',
    '開発者からのどうでもいい話 (15)：「夢枕人」は、とある鯖のプレイヤーさんの「夢って占い師だけだよね？夢霊能者とかいないよね？」というつぶやきを見たのがきっかけで作られました。',
    '開発者からのどうでもいい話 (16)：「夢枕人」の鑑定は夢の中で行われています。だから火車の霊能妨害が効かないのです。',
    '開発者からのどうでもいい話 (17)：「巫女」は中々出番が無い役職の一つですが、村の設定次第では一度に画面を埋め尽くす量のシステムメッセージが出る可能性があります。油断しないようにしましょう。',
    '開発者からのどうでもいい話 (18)：「騎士」にはモデルがいます。多くの人狼を泣かせた高性能狩人を何度も演じたプレイヤーさんです。',
    '開発者からのどうでもいい話 (19)：最初に「ブン屋」になった方はブン屋の考案者でした。アイディア提供ありがとうございます。',
    '開発者からのどうでもいい話 (20)：「ブン屋」は、尾行した人外を人狼が襲撃して失敗した場合でもスクープが手に入ります。一度に人外を二人捕捉できる大チャンスです。',
    '開発者からのどうでもいい話 (21)：「夢守人」は夢を食らう「獏」に対して圧倒的なアドバンテージを持っています。夢守人は夢の番人。夢の世界では最強なのです。',
    '開発者からのどうでもいい話 (22)：人狼や共有者の仲間表示ルーチンは相手が実在しているかちゃんと確認しています。従って、身代わり君が不在の場合は「夢共有者」の相方は誰もいないことになります。',
    '開発者からのどうでもいい話 (23)：「強毒者」にはモデルがいます。吊られた時に高精度で人外を巻き込む埋毒者を何度も演じたプレイヤーさんです。',
    '開発者からのどうでもいい話 (24)：「潜毒者」にはモデルがいます。潜伏を続けて人狼の噛みを引き寄せる埋毒者を何度も演じたプレイヤーさんです。',
    '開発者からのどうでもいい話 (25)：「猫又」に蘇生された恋人は即座に後追い自殺します。カップルが同時に蘇生した場合であっても、「ロミオとジュリエット」方式で自殺してしまいます。',
    '開発者からのどうでもいい話 (26)：「暗殺者」は狩人の護衛を受けられません。これは、「暗殺者は秘密裏に行動するもの」というストーリーに基づいて実質 CO できなくさせるのが狙いです。',
    '開発者からのどうでもいい話 (27)：「橋姫」の仕様は橋を渡った恋人を別れさせてしまう伝説が元になっています。',
    '開発者からのどうでもいい話 (28)：「萌狼」にはモデルがいます。2日目の朝一に「はい、では喰いましょうか」と発言してしまった可愛い人狼さんです。',
    '開発者からのどうでもいい話 (29)：「鵺」は、初心者をコピーして指南してあげることを想定して作成した役職です。',
    '開発者からのどうでもいい話 (30)：通し番号は適当です。次回の更新時には番号を付け直しているかも。あと、このメッセージは多分次回には消えてます。',
			      );
}
?>
