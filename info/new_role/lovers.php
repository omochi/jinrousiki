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
</p>

<h2><a name="cupid_group">キューピッド系</a></h2>
<p>
<a href="#self_cupid">求愛者</a>
<a href="#mind_cupid">女神</a>
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
</body></html>
