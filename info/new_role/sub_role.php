<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [サブ役職]', 'new_role');
?>
</head>
<body>
<h1>サブ役職</h1>
<a href="./" target="_top">←メニュー</a>
<a href="summary.php">一覧表に戻る</a><br>
<p>
<p>
<a href="#rule">基本ルール</a>
</p>
<p>
<a href="#chicken_group">小心者系</a>
<a href="#liar_group">狼少年系</a>
<a href="#authority_group">権力者系</a>
<a href="#upper_luck_group">雑草魂系</a>
<a href="#decide_group">決定者系</a>
<a href="#strong_voice_group">大声系</a>
<a href="#no_last_words_group">筆不精系</a>
<a href="#mind_read_group">サトラレ系</a>
</p>

<h2><a name="rule">基本ルール</a></h2>
<pre>
※Ver. 1.4.0 α8 現在、恋人以外のサブ役職は重なりません。
　(大声と小心者を同時にＣＯする人は最低でもどちらかが嘘です)
</pre>


<h2><a name="chicken_group">小心者系 (投票ショック死系)</a></h2>
<p>
<a href="#chicken">小心者</a>
<a href="#rabbit">ウサギ</a>
<a href="#perverseness">天邪鬼</a>
<a href="#flattery">ゴマすり</a>
<a href="#impatience">短気</a>
<a href="#celibacy">独身貴族</a>
<a href="#panelist">解答者</a>
</p>

<h3><a name="chicken">小心者</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
昼の投票時に一票でも貰うとショック死します。
</pre>

<h3><a name="rabbit">ウサギ</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
昼の投票時に一票も貰えないとショック死します。

[作成者からのコメント]
ウミガメ人狼のプレイヤーさんがモデルです。
</pre>

<h3><a name="perverseness">天邪鬼</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
昼の投票時に他の人と投票先が被るとショック死します。

[作成者からのコメント]
原案はウミガメ人狼のプレイヤーさん達に提供してもらったものです。
ウサギ＆天邪鬼がいいコンビになりつつありますが
お互いが敵対陣営の可能性もあるのが面白いですね。
</pre>

<h3><a name="flattery">ゴマすり</a> [Ver. 1.4.0 α15〜]</h3>
<pre>
昼の投票時に投票先が誰とも被っていないとショック死します。

[作成者からのコメント]
<a href="#perverseness">天邪鬼</a>の逆ですね。アイディア自体は早くからありましたが
なかなかいい名前が思いつかなかったので実装が遅れました。
</pre>

<h3><a name="impatience">短気</a> [Ver. 1.4.0 α15〜]</h3>
<pre>
決定者と同等の能力がある代わりに再投票になるとショック死します。

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。
自覚のある決定者です。
その分だけ判定の優先度が決定者より低めになっています。
</pre>

<h3><a name="celibacy">独身貴族</a> [Ver. 1.4.0 α22〜]</h3>
<pre>
昼の投票時に恋人から一票でも貰うとショック死します。

[作成者からのコメント]
<a href="human.php#jealousy">橋姫</a>同様、対<a href="lovers.php"恋人</a>用役職ですが、こっちはショック死系という事もあって
より理不尽な仕様となっています。
</pre>

<h3><a name="panelist">解答者</a> [Ver. 1.4.0 α17〜]</h3>
<pre>
投票数が 0 になり、<a href="quiz.php#quiz">出題者</a>に投票したらショック死します。
クイズ村専用です (闇鍋モードにも出現しません)。
</pre>


<h2><a name="liar_group">狼少年系 (発言変換系)</a></h2>
<p>
<a href="#liar">狼少年</a>
<a href="#invisible">光学迷彩</a>
<a href="#rainbow">虹色迷彩</a>
<a href="#weekly">七曜迷彩</a>
<a href="#grassy">草原迷彩</a>
<a href="#side_reverse">鏡面迷彩</a>
<a href="#line_reverse">天地迷彩</a>
<a href="#gentleman">紳士</a>
<a href="#lady">淑女</a>
</p>

<h3><a name="liar">狼少年</a> [Ver. 1.4.0 α11〜]</h3>
<pre>
発言時に一部のキーワードが入れ替えられてしまいます。
(例えば、人⇔狼、白⇔黒、○⇔●です)

※Ver. 1.4.0 α14〜
変換対象キーワードが増えました (何が変わるかは自ら試してください)。
時々変換されないことがあります (たまには真実を語るのです)。

[作成者からのコメント]
流石鯖の管理人さんとの会話から生まれた役職です。
<a href="human.php#mage_group">占い師</a>がそれと知らずにCOすると大変なことになりそうです。
回避するのは簡単ですがそれを意識しないといけないだけでも
結構な負担ではないでしょうか？
</pre>

<h3><a name="invisible">光学迷彩</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
発言の一部が空白に入れ替えられてしまいます。

※Ver. 1.4.0 α17〜
変換率を落とした代わりに文字数が増えると変換率がアップします。
一定文字数を超えると完全に消えます。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんのアイディアが原型です。
変換される確率は設定で変更できます。
</pre>

<h3><a name="rainbow">虹色迷彩</a> [Ver. 1.4.0 α17〜]</h3>
<pre>
発言に虹の色が含まれていたら虹の順番に合わせて入れ替えられてしまいます。
(例：赤→橙、橙→黄、黄→緑)

[作成者からのコメント]
<a href="#liar">狼少年</a>の話題中に、循環変換されるタイプが提案されたので実装してみました。
あまり影響は無いでしょうが、ひとたびハマると対応は非常に面倒だと思われます。
</pre>

<h3><a name="weekly">七曜迷彩</a> [Ver. 1.4.0 α19〜]</h3>
<pre>
発言に曜日が含まれていたら曜日の順番に合わせて入れ替えられてしまいます。
(例：日→月、月→火、火→水)

[作成者からのコメント]
<a href="#rainbow">虹色迷彩</a>の曜日バージョンです。
比較的引っ掛かりやすいでしょうが、対応も簡単ですね。
</pre>

<h3><a name="grassy">草原迷彩</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
発言の一文字毎に「w」が付け加えられます。

[作成者からのコメント]
いわゆる Vipper の再現です。
機械的につけるので<a href="human.php#mage_group">占い師</a>などにこれがつくとかなり悲惨な事になると思われます。
</pre>

<h3><a name="side_reverse">鏡面迷彩</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
発言の文字の並びが一行単位で逆になります。

[作成者からのコメント]
要するに、「鏡面迷彩」→「彩迷面鏡」になると言う事です。
理論的には回文で発言すれば影響が出ないという事になります。
</pre>

<h3><a name="line_reverse">天地迷彩</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
発言の行の並びの上下が入れ替わります。

[作成者からのコメント]
常に一行で発言をしている場合は影響がでませんし
対応しようと思えば簡単なので<a href="#side_reverse">鏡面迷彩</a>ほどは苦労しないと思われます。
</pre>

<h3><a name="gentleman">紳士</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
時々発言が「紳士」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんの RP が原型です。
発言内容が完全に入れ替わるので<a href="#liar">狼少年</a>より酷いです。
どんな言葉に入れ替わるのかは管理人さんの気紛れ次第。
</pre>

<h3><a name="lady">淑女</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
時々発言が「淑女」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

[作成者からのコメント]
<a href="#gentleman">紳士</a>の女性バージョンです。
新役職考案スレ (最下参照) の 135 さんのリクエストに応えました。
</pre>


<h2><a name="authority_group">権力者系 (投票数変化系)</a></h2>
<p>
<a href="#rebel">反逆者</a>
<a href="#random_voter">気分屋</a>
<a href="#watcher">傍観者</a>
</p>

<h3><a name="rebel">反逆者</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
権力者と同じ人に投票した場合に自分と権力者の投票数が 0 になります。
それ以外のケースなら通常通り(1票)です。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗をヒントに
対権力者を作成してみました。
</pre>

<h3><a name="random_voter">気分屋</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
投票数が 0-2 の範囲でランダムになります(毎回変わります)。
(投票数の範囲や確率は調整される可能性があります)

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。
</pre>

<h3><a name="watcher">傍観者</a> [Ver. 1.4.0 α9〜]</h3>
<pre>
投票数が 0 になります(投票行為自体は必要です)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。
</pre>


<h2><a name="upper_luck_group">雑草魂系 (得票数変化系)</a></h2>
<p>
<a href="#upper_luck_rule">基本ルール</a>
</p>
<p>
<a href="#upper_luck">雑草魂</a>
<a href="#downer_luck">一発屋</a>
<a href="#star">人気者</a>
<a href="#disfavor">不人気</a>
<a href="#random_luck">波乱万丈</a>
</p>

<h3><a name="upper_luck_rule">基本ルール</a></h3>
<pre>
1. 投票ショック死判定には影響しません (ショック死判定は投票「人数」で行なわれます)。
2. 得票数が減る場合でもマイナスにはなりません。
   例) 得票が 1 で -2 された場合 → 得票数は 0 と計算される
</pre>

<h3><a name="upper_luck">雑草魂</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
2日目の得票数が +4 される代わりに、3日目以降は -2 されます。

※Ver. 1.4.0 α14〜
2日目の得票数補正を +2 から +4 に変更しました

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
</pre>

<h3><a name="downer_luck">一発屋</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
2日目の得票数が -4 される代わりに、3日目以降は +2 されます。

※Ver. 1.4.0 α14〜
2日目の得票数補正を -2 から -4 に変更しました
</pre>


<h3><a name="star">人気者</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
得票数が -1 されます。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 64 が原型です。
得票数が変化するタイプは権力者同様、終盤になると大きな影響を与えます。
「投票した票数を公表する」がオフになっていると誰が何を持っているのか
全然分からなくなるので一体どうなることやら。
</pre>

<h3><a name="disfavor">不人気</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
得票数が +1 されます。
</pre>

<h3><a name="random_luck">波乱万丈</a> [Ver. 1.4.0 α15〜]</h3>
<pre>
得票数に -2〜+2 の範囲でランダムに補正がかかります。

[作成者からのコメント]
発想は<a href="#random_voice">臆病者</a>と同じですね。得票数変化バージョンです。
得票数の変動の程度には補正をかけていません。
その方が波乱万丈らしいでしょう？
</pre>


<h2><a name="decide_group">決定者系 (処刑者候補変化系)</a></h2>
<p>
<a href="#decide_rule">基本ルール</a>
</p>
<p>
<a href="#plague">疫病神</a>
<a href="#good_luck">幸運</a>
<a href="#bad_luck">不運</a>
</p>

<h3><a name="decide_rule">基本ルール</a></h3>
<pre>
この系統が複数いる場合の判定順は以下です。
決定者の投票先＞<a href="#bad_luck">不運</a>＞<a href="#impatience">短気</a>の投票先＞<a href="#good_luck">幸運</a>が逃れる＞<a href="#plague">疫病神</a>の投票先が逃れる
</pre>

<h3><a name="plague">疫病神</a> [Ver. 1.4.0 α9〜]</h3>
<pre>
自分が最多得票者に投票していて、処刑者候補が複数いた場合に
その人が吊り候補から除外される。

[作成者からのコメント]
いわゆる逆決定者です (決定者同様、本人には分かりません)。
新役職考案スレ (最下参照) の 8 が原型です。
</pre>

<h3><a name="good_luck">幸運</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
自分が最多得票者で処刑者候補が複数いた場合は吊り候補から除外される。
(本人には分からない)

[作成者からのコメント]
本人に付随する決定者です (本人には分かりません)。
ウミガメ人狼のプレイヤーさんから原案を頂きました。
</pre>

<h3><a name="bad_luck">不運</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
自分が最多得票者で処刑者候補が複数いた場合は優先的に吊られる。
(本人には分からない)

[作成者からのコメント]
<a href="#good_luck">幸運</a>の逆バージョンです (本人には分かりません)。
</pre>


<h2><a name="strong_voice_group">大声系 (発言変化系)</a></h2>
<a href="#strong_voice">大声</a>
<a href="#normal_voice">不器用</a>
<a href="#weak_voice">小声</a>
<a href="#inside_voice">内弁慶</a>
<a href="#outside_voice">外弁慶</a>
<a href="#upper_voice">メガホン</a>
<a href="#downer_voice">マスク</a>
<a href="#random_voice">臆病者</a>

<h3><a name="strong_voice">大声</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
発言が常に大声になります。

[作成者からのコメント]
声の大きさも狼を推理するヒントになります。
ただのネタと思うこと無かれ。
</pre>

<h3><a name="normal_voice">不器用</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
発言の大きさを変えられません。
</pre>

<h3><a name="weak_voice">小声</a> [Ver. 1.4.0 α3-7〜]</h3>
<pre>
発言が常に小声になります。
</pre>

<h3><a name="inside_voice">内弁慶</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
昼は<a href="#weak_voice">小声</a>、夜は<a href="#strong_voice">大声</a>になります。

[作成者からのコメント]
大声/小声が LW だと圧倒的に不利だと思ったので捻ってみました。
</pre>

<h3><a name="outside_voice">外弁慶</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
昼は<a href="#strong_voice">大声</a>、夜は<a href="#weak_voice">小声</a>になります。
</pre>

<h3><a name="upper_voice">メガホン</a> [Ver. 1.4.0 α17〜]</h3>
<pre>
発言が一段階大きくなり、大声は音割れして聞き取れなくなります。
</pre>

<h3><a name="downer_voice">マスク</a> [Ver. 1.4.0 α17〜]</h3>
<pre>
発言が一段階小さくなり、小声は聞き取れなくなります。
</pre>

<h3><a name="random_voice">臆病者</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
声の大きさがランダムに変わります。

[作成者からのコメント]
固定があるならランダムもありだろうと思って作ってみました。
唐突に大声になるのは固定より鬱陶しいかも。
</pre>


<h2><a name="no_last_words_group">筆不精系 (発言封印系)</a></h2>
<a href="#no_last_words">筆不精</a>
<a href="#blinder">目隠し</a>
<a href="#earplug">耳栓</a>
<a href="#speaker">スピーカー</a>
<a href="#silent">無口</a>
<a href="#mower">草刈り</a>

<h3><a name="no_last_words">筆不精</a> [Ver. 1.4.0 α9〜]</h3>
<pre>
遺言を残せません。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。
「遺言残せばいいや」と思って潜伏する役職にプレッシャーがかかります。
また、安直な遺言騙りもできなくなります。
昼の発言がより盛り上がるといいな、と思って作ってみました。
</pre>

<h3><a name="blinder">目隠し</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
発言者の名前が見えません (空白に見えます)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 66 の「宵闇」の布石です。

※Ver. 1.4.0 α16〜
名前の最初に付いてる◆の色は変更しません。
これで、ユーザアイコンを見ればある程度推測できるようになります。
</pre>

<h3><a name="earplug">耳栓</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
発言が一段階小さく見えるようになり、小声が聞き取れなります。
小声は共有者の囁きに入れ替わります。

※Ver. 1.4.0 α16〜
小声が聞こえないだけではなく、大声→普通、普通→小声になります。

※Ver. 1.4.0 α17〜
小声は空白ではなく、共有者のヒソヒソ声に入れ替わります。

[作成者からのコメント]
ニコ生専用鯖のとあるプレイヤーさん考案のネタ役職です。
聞こえないなら取れ？ネタにマジレスしてはいけません。
</pre>

<h3><a name="speaker">スピーカー</a> [Ver. 1.4.0 α17〜]</h3>
<pre>
発言が一段階大きく見えるようになり、<a href="#strong_voice">大声</a>が音割れして聞き取れなくなります
大声は<a href="#upper_voice">メガホン</a>の大声と同じです。
</pre>

<h3><a name="silent">無口</a> [Ver. 1.4.0 α14〜]</h3>
<pre>
発言の文字数に制限がかかります (制限を越えるとそれ以降が「……」になります)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 51 が原型です。
よほど長い名前の人でもない限り、最低限の占い師のCO等には
影響が出ない程度にしてあります。
</pre>

<h3><a name="mower">草刈り</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
発言から「w」が削られます。

[作成者からのコメント]
いわゆる「(w」に当たるかどうかの判定をしていないので
場合によっては名前を呼ぶ事ができない可能性もあります。
とっても理不尽ですね。
</pre>

<h2><a name="mind_read_group">サトラレ系 (夜発言公開系)</a></h2>
<a href="#mind_read">サトラレ</a>
<a href="#mind_receiver">受信者</a>
<a href="#mind_open">公開者</a>
<a href="#mind_friend">共鳴者</a>
<a href="#mind_evoke">口寄せ</a>

<h3><a name="mind_read">サトラレ</a> [Ver. 1.4.0 α21〜]</h3>
<pre>
夜の発言が<a href="human.php#mind_scanner">さとり</a>に見られてしまいます。
誰に見られているのかは分かりません。
表示されるのは 2 日目の朝で、発言が見られるのは 2 日目夜以降です。
本人か、見ている<a href="human.php#mind_scanner">さとり</a>が死んだら効力が切れます。
自分が<a href="human.php#unconscious">無意識</a>だった場合は相手には見られません。
この役職はサブ役職非公開設定でも必ず表示されます。

[作成者からのコメント]
新役職考案スレ (最下参照) の 4 が原型です。
相談できる人外がこれになるとかなり大変になると思われます。
</pre>

<h3><a name="mind_receiver">受信者</a> [Ver. 1.4.0 α22〜]</h3>
<pre>
特定の人の夜の発言を見ることができます。
<a href="#mind_read">サトラレ</a>と違い、誰の発言を見ているのか分かります。
本人か発言者が死んだら効力が切れます。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="lovers.php#self_cupid">求愛者</a>の矢を撃たれた相手にこれがつきます。

[作成者からのコメント]
<a href="human.php#mind_scanner">さとり</a> - <a href="#mind_read">サトラレ</a>の逆バージョンです。
<a href="human.php#common_group">共有者</a>などの会話に混ざって表示されるのでうっかり
返事をしないように気をつけましょう。
</pre>

<h3><a name="mind_open">公開者</a> [Ver. 1.4.0 α22〜]</h3>
<pre>
夜の発言が参加者全員に見られてしまいます。
観戦者には見えません。
<a href="#mind_read">サトラレ</a>と違い、初日の夜から有効です。
本人が死んだら効力が切れます。
この役職はサブ役職非公開設定でも必ず表示されます。

[作成者からのコメント]
<a href="#mind_read">サトラレ</a>をパワーアップしてみました。
相談できる系統の役職の人には大迷惑ですね。
くれぐれもいきなり自分の役職をつぶやかないように注意してください。
</pre>

<h3><a name="mind_friend">共鳴者</a> [Ver. 1.4.0 α23〜]</h3>
<pre>
特定の人と夜に会話できるようになります。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="human.php#unknown_mania">鵺</a>と<a href="lovers.php#mind_cupid">女神</a>の矢を撃たれた相手にこれがつきます。

[作成者からのコメント]
互いに認識できる<a href="#mind_receiver">受信者</a>ですね。
共有者が<a href="human.php#unknown_mania">鵺</a>と<a href="lovers.php#mind_cupid">女神</a>に同時に矢を撃たれた場合は、誰が誰の発言が
見えるのか、非常にややこしい状況になりますね。
</pre>

<h3><a name="mind_evoke">口寄せ</a> [Ver. 1.4.0 β2〜]</h3>
<pre>
死後に特定の人の遺言窓にメッセージを送れます。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="human.php#evoke_scanner">イタコ</a>に投票された相手にこれがつきます。

1. 生きている時から表示されます (死んでも表示されます)
2. 生きている間は通常通り自分の遺言窓が更新されます
3. 死んでから「遺言を残す」で発言するとイタコの遺言窓が更新されます
4. <a href="human.php#reporter">ブン屋</a>、<a href="#no_last_words">筆不精</a>など、生きている間は遺言を残せない役職でも有効です

[作成者からのコメント]
霊界から一方的にメッセージを送ることができます。
当然ですが、霊界オフモードにしないと機能しません。
</pre>
</body></html>
