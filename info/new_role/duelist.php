<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('info_functions');
OutputRolePageHeader('決闘者陣営');
?>
<p>
<a href="#rule">基本ルール</a>
<a href="#duelist_do">投票の仕様</a>
</p>
<p>
<a href="#duelist_group">決闘者系</a>
<a href="#avenger_group">復讐者系</a>
<a href="#patron_group">後援者系</a>
</p>

<h2 id="rule">基本ルール</h2>
<ol>
<li>初日の夜に「<a href="sub_role.php#rival">宿敵</a>・<a href="sub_role.php#enemy">仇敵</a>・<a href="sub_role.php#supported">受援者</a>」などの自分だけの勝利条件対象者を作る特殊陣営です。</li>
<li>勝利条件は系統毎に設定され、基本的に自身の生死は不問です。</li>
<li><a href="chiroptera.php">蝙蝠陣営</a>同様、他陣営の勝敗と競合しません。</li>
<li>決闘者陣営をコピーした<a href="mania.php#unknown_mania_group">鵺系</a>の勝利条件は「自身の生存のみ」です。</li>
<li>なんらかの理由で自分の<a href="sub_role.php#rival">宿敵</a>・<a href="sub_role.php#enemy">仇敵</a>・<a href="sub_role.php#supported">受援者</a>を持っていない場合 (例：<a href="mania.php#mania_group">神話マニア系</a>のコピー) は、<br>
  例外的に勝利条件は「自身の生存のみ」となります。
</li>
<li>生存カウントは村人です。</li>
<li><a href="human.php#psycho_mage">精神鑑定士</a>の判定は「正常」、<a href="human.php#sex_mage">ひよこ鑑定士</a>の判定は「性別」です。</li>
</ol>

<h2 id="duelist_do">投票の仕様</h2>
<ol>
<li>初日の夜に「<a href="sub_role.php#rival">宿敵</a>・<a href="sub_role.php#enemy">仇敵</a>・<a href="sub_role.php#supported">受援者</a>」にする人を二人選びます (人数は例外あり)。</li>
<li>投票結果は<a href="lovers.php">恋人陣営</a>同様、即座に反映されます。</li>
<li>自分以外を宿敵の対象に選ぶことができる (<a href="../rule.php#system_vote">他人撃ち</a>) 人数の制限は<a href="lovers.php">恋人陣営</a>と同じです。</li>
<li><a href="#avenger_group">復讐者系</a>・<a href="#patron_group">後援者系</a>は自分を対象に選ぶことはできません。</li>
</ol>


<h2 id="duelist_group">決闘者系</h2>
<p><a href="#duelist_rule">基本ルール</a></p>
<p>
<a href="#duelist">決闘者</a>
<a href="#valkyrja_duelist">戦乙女</a>
<a href="#doom_duelist">黒幕</a>
<a href="#triangle_duelist">舞首</a>
</p>

<h2 id="duelist_rule">基本ルール [決闘者系]</h2>
<ol>
<li>初日の夜に「<a href="sub_role.php#rival">宿敵</a>」を作ります。</li>
<li>勝利条件は「自分の作った宿敵が一人だけ生存すること」で、自身の生死は不問です。</li>
</ol>

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

<h3 id="doom_duelist">黒幕 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β3～]</h3>
<pre>
宿敵に加えて、<a href="sub_role.php#death_warrant">死の宣告</a> (7日目昼) を付加する決闘者。
</pre>
<h4>関連役職</h4>
<pre>
<a href="ability.php#doom">死の宣告能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
「一週間戦争」がテーマです。
<a href="sub_role.php#death_warrant">死の宣告</a>は適用直後に表示されるので対象者はすぐ自覚できます。
一部のレアケースを除いて短期決戦が必要となるので難易度は高めです。
</pre>

<h3 id="triangle_duelist">舞首 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β1～]</h3>
<pre>
宿敵を三人作れる決闘者。他人撃ち制限は<a href="#valkyrja_duelist">戦乙女</a>と同じ。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="lovers.php#triangle_cupid">小悪魔</a>の決闘者バージョンです。
勝利条件の仕様上、対象を増やしても有利になるわけではないので
単純に巻き込まれて迷惑する人が増えるだけの存在と言えます。
</pre>


<h2 id="avenger_group">復讐者系</h2>
<p><a href="#avenger_rule">基本ルール</a></p>
<p>
<a href="#avenger">復讐者</a>
</p>

<h2 id="avenger_rule">基本ルール [復讐者系]</h2>
<ol>
<li>初日の夜に一定数の「<a href="sub_role.php#enemy">仇敵</a>」を作ります。</li>
<li>勝利条件は「自分の作った仇敵を全滅させること」で、自身の生死は不問です。</li>
</ol>

<h3 id="avenger">復讐者 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β3～]</h3>
<pre>
復讐者系の基本種。<a href="sub_role.php#enemy">仇敵</a>の人数は村の人口の四分の一。
人口判定は端数切り捨て (例：22人村なら5人)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
「自分の敵を自分で選んでもらう」ことがコンセプトです。
相手には自覚がないので、一方的に敵視する形になります。
</pre>


<h2 id="patron_group">後援者系</h2>
<p><a href="#patron_rule">基本ルール</a></p>
<p>
<a href="#patron">後援者</a>
</p>

<h2 id="patron_rule">基本ルール [後援者系]</h2>
<ol>
<li>初日の夜に一定数の「<a href="sub_role.php#supported">受援者</a>」を作ります。</li>
<li>勝利条件は「自分の作った受援者が一人以上生存していること」で、自身の生死は不問です。</li>
</ol>

<h3 id="patron">後援者 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β3～]</h3>
<pre>
後援者系の基本種。<a href="sub_role.php#supported">受援者</a>の人数は一人。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#avenger">復讐者</a>の逆バージョンです。
決闘者陣営の中では比較的勝利条件は緩めですが「事故」に弱いので
運に左右されやすいとも言えます。
</pre>
</body></html>
