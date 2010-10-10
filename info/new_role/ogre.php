<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputRolePageHeader('鬼陣営');
?>
<p>
<a href="#rule">基本ルール</a>
<a href="#ogre_do_spec">人攫いの仕様</a>
</p>
<p>
<a href="#ogre_group">鬼系</a>
</p>

<h2><a id="rule">基本ルール</a></h2>
<ol>
  <li>勝利条件が個々によって違う特殊陣営です。</li>
  <li>共通の勝利条件は「自分自身の生存」で、<a href="chiroptera.php">蝙蝠陣営</a>同様、他陣営の勝敗と競合しません。</li>
  <li><a href="mania.php#unknown_mania_group">鵺系</a>が鬼陣営をコピーした場合は、例外的に勝利条件は「自身の生存のみ」となります。</li>
  <li>恋人は恋人陣営と判定します (例：恋人狼は人狼陣営とはカウントしない)。</li>
  <li>2日目以降の夜に村人一人を攫う (暗殺の一種) ことができます。</li>
  <li>人狼に襲撃されても一定確率で無効化します (襲撃は失敗扱い)。</li>
  <li>襲撃者が<a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態) だった場合は耐性無効です。</a>
  <li>暗殺を一定確率で反射します。</li>
  <li>生存カウントは村人です。</li>
  <li><a href="human.php#psycho_mage">精神鑑定士</a>・<a href="human.php#sex_mage">ひよこ鑑定士</a>の判定は「鬼」です。</li>
</ol>

<h2><a id="ogre_do_spec">人攫いの仕様</a></h2>
<ol>
  <li>暗殺カテゴリに属し、<a href="human.php#assassin_spec">暗殺の仕様</a>が適用されます</li>
  <li>「人攫いする / しない」を必ず投票する必要があります</li>
  <li>暗殺された人の死亡メッセージは人狼の襲撃と同じで、死因は「鬼に攫われた」です</li>
  <li>暗殺が成立するたびに成功率が 1/5 になります (100% → 20% → 4% → 1% (以降は 1% で固定)</li>
</ol>

<h2><a id="ogre_group">鬼系</a></h2>
<p>
<a href="#ogre">鬼</a>
<a href="#orange_ogre">前鬼</a>
<a href="#indigo_ogre">後鬼</a>
</p>
<h3><a id="ogre">鬼</a> (占い結果：鬼 / 霊能結果：鬼) [Ver. 1.4.0 β18〜]</h3>
<h4>[耐性] 人狼襲撃：無効 (30%) / 暗殺：確率反射 (30%) / 罠：有効</h4>
<pre>
鬼陣営の基本種。勝利条件は「自分自身と<a href="wolf.php#wolf_group">人狼系</a> (種類・恋人不問)の生存」。
人狼が一人でも生存していればいいので、人狼陣営の勝利を目指す必要はない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
テーマは、「全陣営に対する不確定要素」です。
<a href="wolf.php#mad_group">狂人系</a>とは似て非なる勝利条件なので狐や恋人が出現している場合は
逆に人狼にとって脅威になる可能性があります。
</pre>

<h3><a id="orange_ogre">前鬼</a> (占い結果：鬼 / 霊能結果：鬼) [Ver. 1.4.0 β18〜]</h3>
<h4>[耐性] 人狼襲撃：無効 (30%) / 暗殺：確率反射 (30%) / 罠：有効</h4>
<pre>
鬼系の一種で、勝利条件は「自分自身の生存 + <a href="wolf.php">人狼陣営</a>の全滅」。
<a href="wolf.php#mad_group">狂人</a>や人狼陣営に付いた<a href="mania.php#unknown_mania_group">鵺系</a>も含まれることに注意。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
勝利条件の関係で<a href="#ogre">鬼</a>とは完全に敵対することになります。
</pre>

<h3><a id="indigo_ogre">後鬼</a> (占い結果：鬼 / 霊能結果：鬼) [Ver. 1.4.0 β18〜]</h3>
<h4>[耐性] 人狼襲撃：無効 (30%) / 暗殺：確率反射 (30%) / 罠：有効</h4>
<pre>
鬼系の一種で、勝利条件は「自分自身の生存 + <a href="wolf.php">妖狐陣営</a>の全滅」。
妖狐陣営に付いた<a href="mania.php#unknown_mania_group">鵺系</a>も含まれることに注意。
妖狐陣営が出現していない場合は自己の生存のみで勝利となる。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#orange_ogre">前鬼</a>の対妖狐バージョンです。
他の鬼の勝利条件とは競合していないので比較的動きやすいと思います。
</pre>
</body></html>
