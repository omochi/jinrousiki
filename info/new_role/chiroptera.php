<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [蝙蝠陣営]', 'new_role');
?>
</head>
<body>
<h1>蝙蝠陣営</h1>
<a href="./" target="_top">←メニュー</a>
<a href="summary.php">一覧表に戻る</a><br>
<p>
<p>
<a href="#rule">基本ルール</a>
</p>
<p>
<a href="#chiroptera_group">蝙蝠系</a>
</p>

<h2><a name="rule">基本ルール</a></h2>
<pre>
自分が生き残ったら勝利、死んだら敗北となる特殊な陣営。
他の陣営の勝敗と競合しないので、例えば、「村人陣営+生き残った蝙蝠が勝利」
という扱いになる。
<a href="human.php#psycho_mage">精神鑑定士</a>の結果は「正常」、<a href="human.php#sex_mage">ひよこ鑑定士</a>の結果は「蝙蝠」となる。
</pre>

<h2><a name="chiroptera_group">蝙蝠系</a></h2>
<p>
<a href="#chiroptera">蝙蝠</a>
<a href="#poison_chiroptera">毒蝙蝠</a>
<a href="#cursed_chiroptera">呪蝙蝠</a>
<a href="#dummy_chiroptera">夢求愛者</a>
</p>

<h3><a name="chiroptera">蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
蝙蝠陣営の基本役職。能力は何も持っていない。
他の蝙蝠が誰か分からず、会話もできない。
また、自分以外の蝙蝠の生死と勝敗は無関係。

[作成者からのコメント]
他の国に実在する役職です。
他の陣営は如何に自陣の PP に引き込むかがポイントです。
</pre>

<h3><a name="poison_chiroptera">毒蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
毒を持った蝙蝠。
吊られた場合の毒の発動対象は人外(狼と狐)+蝙蝠です。
つまり、劣化<a href="human.php#strong_poison">強毒者</a>相当となります。
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺されます。

※Ver. 1.4.0 α22〜
毒の発動対象を人外+蝙蝠に変更しました。

[作成者からのコメント]
死んだ時点で負けなので本人には何の利益もありませんが
信用を取れれば生き残れる可能性が大幅に上がるでしょう。
それ故にこれを騙る人がたくさん出てくると予想されますが……

Ver. 1.4.0 α22 から劣化<a href="human.php#strong_poison">強毒者</a>相当に変更しました。
村視点、毒蝙蝠は吊ったほうがメリットが大きいので
CO するとむしろ吊られるリスクが高まります。
</pre>

<h3><a name="cursed_chiroptera">呪蝙蝠</a> (占い結果：村人(呪返し)、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
占われたら占った<a href="human.php#mage_group">占い師</a>を呪い殺す蝙蝠。
<a href="human.php#voodoo_killer">陰陽師</a>に占われると殺される。
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される。

[作成者からのコメント]
<a href="wolf.php#cursed_wolf">呪狼</a>の蝙蝠バージョンです。
どちらかと言うと、これを騙る狼や狐が非常にやっかいですね。
素直に CO しても信用を取るのは難しいでしょう。
</pre>

<h3><a name="dummy_chiroptera">夢求愛者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α24〜]</h3>
<pre>
本人には<a href="lovers.php#self_cupid">求愛者</a>と表示されている蝙蝠。
矢を撃つことはできるが恋人にはならず、矢を撃った先に<a href="sub_role.php#mind_receiver">受信者</a>もつかない。
<a href="wolf.php#dream_eater_mad">獏</a>に襲撃されると殺される。

矢を撃ったはずの恋人が死んだのに自分が後追いしていない、
<a href="human.php#psycho_mage">精神鑑定士</a>から「嘘つき」、<a href="human.php#sex_mage">ひよこ鑑定士</a>から「蝙蝠」判定されるなどで
自分の正体を確認することができる。

[作成者からのコメント]
<a href="lovers.php#self_cupid">求愛者</a>の夢バージョンです。
キューピッド相当にすると出現時点で勝ち目がないケースも
出てくるので扱いとしては特殊蝙蝠です。
<a href="wolf.php#possessed_wolf">憑狼</a>が恋人を噛んでも破綻しない状況を作るために実装しました。
</pre>
</body></html>
