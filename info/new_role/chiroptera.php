<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [蝙蝠陣営]', 'new_role');
?>
</head>
<body>
<h1>蝙蝠陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#rule">基本ルール</a>
</p>
<p>
<a href="#chiroptera_group">蝙蝠系</a>
<a href="#fairy_group">妖精系</a>
</p>

<h2><a name="rule">基本ルール</a></h2>
<ol>
  <li>自分が生き残ったら勝利、死んだら敗北となる特殊な陣営です。<br>
    自分以外の蝙蝠の生死と勝敗は無関係です。
  </li>
  <li>他の陣営の勝敗と競合しません。<br>
    例) 村人陣営 + 生き残った蝙蝠が勝利
  </li>
  <li>他の蝙蝠がいても誰か分かりません。</li>
  <li><a href="human.php#psycho_mage">精神鑑定士</a>の判定は「正常」です。</li>
  <li><a href="human.php#sex_mage">ひよこ鑑定士</a>の判定は「蝙蝠」です。</li>
</ol>

<h2><a name="chiroptera_group">蝙蝠系</a></h2>
<p>
<a href="#chiroptera">蝙蝠</a>
<a href="#poison_chiroptera">毒蝙蝠</a>
<a href="#cursed_chiroptera">呪蝙蝠</a>
<a href="#elder_chiroptera">古蝙蝠</a>
<a href="#dummy_chiroptera">夢求愛者</a>
</p>

<h3><a name="chiroptera">蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
蝙蝠陣営の基本職。能力は何も持っていない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職です。
他の陣営は如何に自陣の PP に引き込むかがポイントです。
</pre>

<h3><a name="poison_chiroptera">毒蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
毒を持った蝙蝠。
吊られた場合の毒の発動対象は人外(狼と狐)+蝙蝠。
つまり、劣化<a href="human.php#strong_poison">強毒者</a>相当となります。
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺されます。
</pre>
<h4>Ver. 1.4.0 α22〜</h4>
<pre>
毒の発動対象を人外+蝙蝠に変更しました。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#poison_group">埋毒者</a>の蝙蝠バージョンです。
死んだ時点で負けなので本人には何の利益もない上に、
素直に CO するとほぼ間違いなく吊られるでしょう。
</pre>

<h3><a name="cursed_chiroptera">呪蝙蝠</a> (占い結果：村人(呪返し)、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
占われたら占った<a href="human.php#mage_group">占い師</a>を呪い殺す蝙蝠。
<a href="human.php#voodoo_killer">陰陽師</a>に占われると殺される。
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#cursed_wolf">呪狼</a>の蝙蝠バージョンです。
どちらかと言うと、これを騙る狼や狐が非常にやっかいですね。
素直に CO しても信用を取るのは難しいでしょう。
</pre>

<h3><a name="elder_chiroptera">古蝙蝠</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β5〜]</h3>
<pre>
投票数が +1 される蝙蝠。
権力者とセットになった場合はさらに +1 される (合計 3 票)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#elder">長老</a>の蝙蝠バージョンです。
PP 要員に組み込まれることの多い蝙蝠陣営の花形と言える存在ですが
それゆえに目を付けられやすいでしょう。
</pre>

<h3><a name="dummy_chiroptera">夢求愛者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α24〜]</h3>
<pre>
本人には<a href="lovers.php#self_cupid">求愛者</a>と表示されている蝙蝠。
矢を撃つことはできるが恋人にはならず、矢を撃った先に<a href="sub_role.php#mind_receiver">受信者</a>もつかない。
<a href="wolf.php#dream_eater_mad">獏</a>に襲撃されると殺される。

矢を撃ったはずの恋人が死んだのに自分が後追いしていない、
<a href="human.php#psycho_mage">精神鑑定士</a>から「嘘つき」、<a href="human.php#sex_mage">ひよこ鑑定士</a>から「蝙蝠」判定されるなどで
自分の正体を確認することができる。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="lovers.php#self_cupid">求愛者</a>の夢バージョンです。
キューピッド相当にすると出現時点で勝ち目がないケースも
出てくるので扱いとしては特殊蝙蝠です。
<a href="wolf.php#possessed_wolf">憑狼</a>が恋人を噛んでも破綻しない状況を作るために作成しました。
</pre>

<h2><a name="fairy_group">妖精系</a></h2>
<p>
<a href="#fairy_spec">基本スペック</a>
</p>
<p>
<a href="#fairy">妖精</a>
<a href="#spring_fairy">春妖精</a>
<a href="#summer_fairy">夏妖精</a>
<a href="#autumn_fairy">秋妖精</a>
<a href="#winter_fairy">冬妖精</a>
</p>

<h3><a name="fairy_spec">基本スペック</a></h3>
<ol>
  <li>蝙蝠陣営の<a href="#rule">基本ルール</a>が適用されます。</li>
  <li>夜に村人一人に投票して、対象に悪戯します。</li>
  <li>悪戯の内容は明記していない限りは、「対象の発言の先頭に無意味な文字列を追加する」です。</li>
  <li>能力は「占い」と同等の扱いです (呪い・占い妨害・厄払いの影響を受けます)。</li>
  <li>悪戯の結果は相手の発言を見れば分かるので本人には何も表示されません。</li>
  <li>悪戯の効果は重複します (複数の妖精から悪戯されたら人数分の効果が出ます)。</li>
</ol>

<h3><a name="fairy">妖精</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
妖精の基本職。追加する文字列は「共有者の囁き」(内容は管理者が設定ファイルで変更可能)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
自己証明能力を持った蝙蝠です。
しかし、証明方法が鬱陶しいので信用を得た上で吊られることもあるでしょう。
</pre>

<h3><a name="spring_fairy">春妖精</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
春を告げる妖精。追加する文字列は「春ですよー」。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方 Project の「リリーホワイト」がモデルで、妖精系の作成の着想となった存在です。
</pre>

<h3><a name="summer_fairy">夏妖精</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
夏を告げる妖精。追加する文字列は「夏ですよー」。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#spring_fairy">春妖精</a>の夏バージョンです。
見え透いた位置ばかりを悪戯し続けると呪いで死ぬ可能性があるので気をつけましょう。
</pre>

<h3><a name="autumn_fairy">秋妖精</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
秋を告げる妖精。追加する文字列は「秋ですよー」。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#spring_fairy">春妖精</a>の秋バージョンです。
<a href="sub_role.php#silent">無口</a>が同時にたくさんの妖精に悪戯されると何も発言できなくなる可能性があります。
理不尽ですね。
</pre>

<h3><a name="winter_fairy">冬妖精</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
冬を告げる妖精。追加する文字列は「冬ですよー」。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#spring_fairy">春妖精</a>の冬バージョンです。
一見単純な能力に見えて、実は<a href="wolf.php#possessed_wolf">憑狼</a>システムの応用で実装されています。
</pre>
</body></html>
