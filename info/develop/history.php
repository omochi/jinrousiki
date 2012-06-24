<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('info_functions');
InfoHTML::OutputHeader('開発履歴', 1, 'develop_history');
?>
<p>
Ver. 2.1.0
<a href="#ver210a1">α1</a>
<a href="#ver210a2">α2</a>
<a href="#ver210a3">α3</a>
</p>
<p>
<a href="history_1.3.php">～ 1.3</a>
<a href="history_1.4.php">1.4</a>
<a href="history_1.5.php">1.5</a>
<a href="history_2.0.php">2.0</a>
</p>

<h2 id="ver210a3">Ver. 2.1.0 α3 (Rev. 608) : 2012/06/24 (Sun) 06:21</h2>
<ul>
<li>「朔狼」実装</li>
<li>サブ役職「告白」実装</li>
<li>「縁切地蔵」「蛇姫」仕様変更</li>
<li>初期化処理を再設計</li>
</ul>

<h2 id="ver210a2">Ver. 2.1.0 α2 (Rev. 560) : 2012/04/30 (Mon) 16:16</h2>
<ul>
<li>「傾奇者」実装</li>
<li>「長老」「老兵」仕様変更</li>
<li>システムクラスを再設計</li>
</ul>

<h2 id="ver210a1">Ver. 2.1.0 α1 (Rev. 542) : 2012/04/17 (Tue) 01:10</h2>
<ul>
<li>Ver. 1.5.2 から「因幡兎」を移植</li>
</ul>
</body>
</html>
