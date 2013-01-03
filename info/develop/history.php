<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('info_functions');
InfoHTML::OutputHeader('開発履歴', 1, 'develop_history');
?>
<p>
Ver. 2.2.0
</p>
<p>
<a href="#ver220a1">α1</a>
<a href="#ver220a2">α2</a>
</p>
<p>
<a href="history_1.3.php">～ 1.3</a>
<a href="history_1.4.php">1.4</a>
<a href="history_1.5.php">1.5</a>
<a href="history_2.0.php">2.0</a>
<a href="history_2.1.php">2.1</a>
</p>

<h2 id="ver220a2">Ver. 2.2.0 α2 (Rev. 699) : 2013/01/03 (Thu) 03:16</h2>
<ul>
<li>ログイン画面にトリップ入力欄を追加</li>
<li>基幹ライブラリの再構成</li>
</ul>

<h2 id="ver220a1">Ver. 2.2.0 α1 (Rev. 692) : 2012/12/24 (Mon) 03:01</h2>
<ul>
<li>PDO の導入</li>
</ul>
</body>
</html>
