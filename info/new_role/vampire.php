<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputRolePageHeader('吸血鬼陣営');
?>
<p>
<a href="#rule">基本ルール</a>
<a href="#vampire_do_spec">襲撃の仕様</a>
</p>
<p>
<a href="#vampire_group">吸血鬼系</a>
</p>

<h2><a id="rule">基本ルール</a></h2>
<ol>
  <li>他国の「カルトリーダー」・「笛吹き」に相当します。</li>
  <li>勝利条件は「生存者が自分と自分の<a href="sub_role.php#infected">感染者</a>のみになっていること」で、本人だけが勝利扱いになります。</li>
  <li>勝利条件を満たした時に恋人が生存していた場合は<a href="lovers.php">恋人陣営</a>勝利になります。</li>
  <li>2日目以降の夜に村人一人を襲撃して<a href="sub_role.php#infected">感染者</a>にすることができます。</li>
  <li><a href="sub_role.php#infected">感染者</a>になっても自覚がありません。</li>
  <li>生存カウントは村人です。</li>
</ol>

<h2><a id="vampire_do_spec">襲撃の仕様</a></h2>
<ol>
  <li>襲撃先が<a href="human.php#guard_group">狩人系</a>に護衛されていた場合は失敗し、狩人には「護衛成功」のメッセージが出ます。</li>
  <li><a href="human.php#guard_group">狩人系</a>の護衛判定は<a href="human.php#guard_limit">護衛制限</a>が適用されます。</li>
  <li><a href="human.php#blind_guard">夜雀</a>・<a href="wolf.php#trap_mad">罠師</a>の能力は有効です。</li>
  <li><a href="human.php#hunter_guard">猟師</a>が護衛しても死亡しません。</li>
  <li>吸血鬼陣営の人を<a href="sub_role.php#infected">感染者</a>にすることはできません (<a href="mania.php#unknown_mania">鵺</a>・変化前の<a href="mania.php#soul_mania">覚醒者</a>・<a href="mania.php#dummy_mania">夢語部</a>にも適用されます)。</li>
</ol>

<h2><a id="vampire_group">吸血鬼系</a></h2>
<p>
<a href="#vampire">吸血鬼</a>
<a href="#sacrifice_vampire">吸血公</a>
</p>
<h3><a id="vampire">吸血鬼</a> (占い結果：蝙蝠 / 霊能結果：蝙蝠) [Ver. 1.4.0 β14〜]</h3>
<pre>
吸血鬼陣営の基本種。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他国に実在する役職です。
式神研の闇鍋に混ぜてどの程度勝てるのか検討が付かないので、
まずは条件を緩めに設定して様子を見てみようかと思います。
</pre>

<h3><a id="sacrifice_vampire">吸血公</a> (占い結果：蝙蝠 / 霊能結果：蝙蝠) [Ver. 1.4.0 β17〜]</h3>
<h4>[耐性] 人狼襲撃：特殊 / 護衛：狩り</h4>
<pre>
<a href="wolf.php#wolf_group">人狼</a>に襲撃された時に、自分の<a href="sub_role.php#infected">感染者</a>が身代わりで死亡する。
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される。
</pre>
<ol>
  <li>身代わりが発生した場合、<a href="wolf.php#wolf_group">人狼</a>の襲撃は失敗扱い。</li>
  <li>代わりに死んだ人の死因は「誰かの犠牲となって死亡したようです」。</li>
  <li>本人は身代わりが発生しても分からない。</li>
  <li>身代わり君か、襲撃者が<a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態) だった場合、身代わり能力は無効。</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="chiroptera.php#boss_chiroptera">大蝙蝠</a>の能力を持った吸血鬼で、<a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/728" target="_top">新役職考案スレ</a> の 728 が原型です。
</pre>
</body></html>
