<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('info_functions');
OutputRolePageHeader('決闘者陣営');
?>
<p>
<a href="#rule">基本ルール</a>
<a href="#duelist_do_spec">投票の仕様</a>
<a href="#rival">宿敵の仕様</a>
</p>
<p>
<a href="#duelist_group">決闘者系</a>
</p>

<h2 id="rule">基本ルール</h2>
<ol>
  <li>初日の夜に「宿敵」を作る特殊陣営です。</li>
  <li>勝利条件は「自分の作った宿敵が一人だけ生存すること」で、自身の生死は不問です。</li>
  <li><a href="chiroptera.php">蝙蝠陣営</a>同様、他陣営の勝敗と競合しません。</li>
  <li>決闘者陣営をコピーした<a href="mania.php#unknown_mania_group">鵺系</a>の勝利条件は「自身の生存のみ」です。</li>
  <li>なんらかの理由で自分の宿敵を持っていない場合 (例：<a href="mania.php#mania_group">神話マニア系</a>のコピー) は、<br>
    例外的に勝利条件は「自身の生存のみ」となります。
  </li>
  <li>生存カウントは村人です。</li>
  <li><a href="human.php#psycho_mage">精神鑑定士</a>の判定は「正常」、<a href="human.php#sex_mage">ひよこ鑑定士</a>の判定は「性別」です。</li>
</ol>

<h2 id="duelist_do_spec">投票の仕様</h2>
<ol>
  <li>初日の夜に「<a href="#rival">宿敵</a>」にする人を二人選びます (人数は例外あり)。</li>
  <li>投票結果は<a href="lovers.php">恋人陣営</a>同様、即座に反映されます。</li>
  <li>自分以外を宿敵の対象に選ぶことができる (他人撃ち) 人数の制限は<a href="lovers.php">恋人陣営</a>と同じです。</li>
</ol>

<h2 id="rival">宿敵の仕様</h2>
<ol>
  <li>勝利条件に「自分の宿敵が全員死亡し、自分だけが生存している」ことが追加されます。</li>
  <li><a href="sub_role.php#lovers">恋人</a>になった場合は、宿敵は無効化されます。</li>
</ol>
<h3>勝利条件例</h3>
<pre>
1. X[戦乙女] → A-B / A[村人] B[人狼]
X：「A、B どちらかのみの生存」
A：「村人陣営の勝利」+「Bの死亡」+「自分自身の生存」
B：「人狼陣営の勝利」+「Aの死亡」+「自分自身の生存」
決闘者陣営が絡んだ場合の基本パターンになります。
別陣営が対象になった場合は基本的には生存勝利のみで
条件をほぼ満たせることになります。

2. X[決闘者] → X-A / X[決闘者] A[妖狐]
X：「Aの死亡」+「自分自身の生存」
A：「妖狐陣営の勝利」+「Xの死亡」+「自分自身の生存」
自打ちの場合、決闘者は自分自身で宿敵の条件を満たす必要があります。

3. X[戦乙女] → A-B、Y[戦乙女] B-C / A[人狼] B[狂人] C[人狼]
X：「A、B どちらかのみの生存」
Y：「B、C どちらかのみの生存」
A：「人狼陣営の勝利」+「Bの死亡」+「自分自身の生存」
B：「人狼陣営の勝利」+「A、Cの死亡」+「自分自身の生存」
C：「人狼陣営の勝利」+「Bの死亡」+「自分自身の生存」
A・C が生存、B が死亡して人狼陣営が勝利することで B 以外は全員勝利になります。

4. X[決闘者] → A-B、A[求愛者] → X-A / X[決闘者][恋人] A[求愛者][恋人] B[共有者]
X：「恋人陣営の勝利」
A：「恋人陣営の勝利」
B：「村人陣営の勝利」+「Aの死亡」+「自分自身の生存」
恋人になった時点で宿敵は勝利条件から除かれます

5. X[決闘者] → A-B、A[求愛者] A-B / A[求愛者][恋人] B[共有者][恋人]
X：「A、B どちらかのみの生存」 (実質勝利不可能)
A：「恋人陣営の勝利」
B：「恋人陣営の勝利」
恋人同士を宿敵にしてしまうと決闘者は勝利条件を満たすことが不可能になります。
</pre>

<h2 id="duelist_group">決闘者系</h2>
<p>
<a href="#duelist">決闘者</a>
<a href="#valkyrja_duelist">戦乙女</a>
<a href="#triangle_duelist">舞首</a>
</p>

<h3 id="duelist">決闘者 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β1～]</h3>
<pre>
決闘者陣営の基本種。
自分撃ち固定で、矢を撃った相手に自分を対象にした<a href="sub_role.php#mind_receiver">受信者</a>が付く。
</pre>
<h4>関連役職</h4>
<pre>
<a href="lovers.php#self_cupid">求愛者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する「邪気悪魔」をアレンジしたもので、<a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/780" target="_top">新役職考案スレ(780)</a> が原型です。
投票能力はほぼ<a href="lovers.php">恋人陣営</a>互換です。
</pre>

<h3 id="valkyrja_duelist">戦乙女 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β1～]</h3>
<pre>
自分撃ち固定制限がない、決闘者系の標準種。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="lovers.php#cupid">キューピッド</a>の決闘者バージョンです。
能力的にはこちらが標準ですが、名称の都合で基本種を変更しています。
</pre>

<h3 id="triangle_duelist">舞首 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β1～]</h3>
<pre>
宿敵を三人作れる決闘者。他人撃ち制限は<a href="#valkyrja_duelist">戦乙女</a>と同じ。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="lovers.php#triangle_cupid">小悪魔</a>の決闘者バージョンです。
</pre>
</body></html>
