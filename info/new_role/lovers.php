<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [恋人陣営]', 'new_role');
?>
</head>
<body>
<h1>恋人陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#cupid_group">キューピッド系</a>
<a href="#angel_group">天使系</a>
</p>

<h2><a name="cupid_group">キューピッド系</a></h2>
<p>
<a href="#self_cupid">求愛者</a>
<a href="#mind_cupid">女神</a>
<a href="#triangle_cupid">小悪魔</a>
</p>

<h3><a name="self_cupid">求愛者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
自分撃ち固定のキューピッド。
矢を撃った相手に自分を対象にした<a href="sub_role.php#mind_receiver">受信者</a>が付く。
</pre>
<h4>Ver. 1.4.0 α22〜</h4>
<pre>
矢を撃った相手に自分を対象にした<a href="sub_role.php#mind_receiver">受信者</a>がつきます。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職です。
対象が制限される代わりに、相手にメッセージを (一方的に) 送ることができます。
思う存分自分の想いを語ってください。
</pre>

<h3><a name="mind_cupid">女神</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α23〜]</h3>
<pre>
矢を撃った二人を<a href="sub_role.php#mind_friend">共鳴者</a>にする上位キューピッド。
他人撃ちの場合は、さらに自分が二人を対象にした<a href="sub_role.php#mind_receiver">受信者</a>になります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
会話能力を持った恋人を作る上位キューピッドです。
自分撃ちの場合は<a href="#self_cupid">求愛者</a>の相互撃ちと同様の状態になります。
また、他人撃ちでも<a href="sub_role.php#mind_receiver">受信者</a>になるので、矢を撃った対象の発言が
必ず見えることになります。
</pre>

<h3><a name="triangle_cupid">小悪魔</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
矢を三本撃てるキューピッド。
他人撃ち制限などは普通のキューピッドと同じ。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
翠星石鯖＠やる夫人狼で一時的に施されていた改造に別名を与えてみました。
</pre>

<h2><a name="angel_group">天使系</a></h2>
<p>
<a href="#angel">天使</a>
<a href="#rose_angel">薔薇天使</a>
<a href="#lily_angel">百合天使</a>
<a href="#ark_angel">大天使</a>
</p>

<h3><a name="angel">天使</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
矢を撃った二人が男女だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
組み合わせ次第で特典が追加されるキューピッドの上位種です。
鉄板カップルが増えることを狙って作成してみました。
</pre>

<h3><a name="rose_angel">薔薇天使</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
矢を撃った二人が男性同士だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#rose_angle">天使</a>の男性版です。
<a href="#rose_angle">天使</a>とは逆に予想外のカップリングが増えるかもしれませんね。
</pre>

<h3><a name="lily_angel">百合天使</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
矢を撃った二人が女性だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#rose_angle">天使</a>の女性版です。
<a href="human.php#sex_mage">ひよこ鑑定士</a>対策で性別をごまかすケースがあることに気をつけましょう。
</pre>

<h3><a name="ark_angel">大天使</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
他の<a href="#angel_group">天使系</a>が作成した<a href="sub_role.php#mind_sympathy">共感者</a>の結果を見ることができる上位天使。
ただし、本人は<a href="sub_role.php#mind_sympathy">共感者</a>を作ることはできない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#rose_angle">天使</a>の作成中の状態に別名を与えてみました。
組み合わせ次第で、二日目の朝に内訳をほぼ掌握することが可能になります。
</pre>
</body></html>
