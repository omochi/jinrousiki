<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [出題者陣営]', 'new_role');
?>
</head>
<body>
<h1>出題者陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#rule">基本ルール</a>
</p>
<p>
<a href="#quiz_group">出題者系</a>
</p>

<h2><a id="rule">基本ルール</a></h2>
<ol>
  <li>勝利条件は「生存者が出題者陣営のみになっていること」です。</li>
  <li>生存カウントは村人です。</li>
</ol>

<h2><a id="quiz_group">出題者系</a></h2>
<p>
<a href="#quiz">出題者</a>
</p>
<h3><a id="quiz">出題者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α2〜]</h3>
<pre>
<a href="../game_option.php#quiz">クイズ村</a>の GM です。闇鍋モードにも低確率で出現します。
ルールの特殊なクイズ村以外ではまず勝ち目はありません。
引いたら諦めてください。
</pre>
<h4>Ver. 1.4.0 β2〜</h4>
<pre>
毒吊りで巻き込まれる対象になりません。
例えば、出題者、埋毒者、毒狼の編成で毒能力者を吊った場合は確実に出題者が生き残ります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
クイズ村以外では恋人になったほうがまだましという涙目すぎる存在ですが
闇鍋なので全役職を出します。一回くらいは奇跡的な勝利を見てみたいですね。
</pre>
</body></html>
