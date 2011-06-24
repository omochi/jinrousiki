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
<li>初日の夜に一定数の「<a href="sub_role.php#rival">宿敵</a>・<a href="sub_role.php#enemy">仇敵</a>・<a href="sub_role.php#supported">受援者</a>」にする人を選びます。</li>
<li>投票結果は<a href="lovers.php">恋人陣営</a>同様、即座に反映されます。</li>
<li><a href="#avenger_group">復讐者系</a>・<a href="#patron_group">後援者系</a>は自分を対象に選ぶことはできません。</li>
</ol>


<h2 id="duelist_group">決闘者系</h2>
<p><a href="#duelist_rule">基本ルール</a></p>
<p>
<a href="#duelist">決闘者</a>
<a href="#valkyrja_duelist">戦乙女</a>
<a href="#doom_duelist">黒幕</a>
<a href="#critical_duelist">剣闘士</a>
<a href="#triangle_duelist">舞首</a>
</p>

<h2 id="duelist_rule">基本ルール [決闘者系]</h2>
<ol>
<li>初日の夜に「<a href="sub_role.php#rival">宿敵</a>」を二人作ります (人数は例外あり)。</li>
<li>勝利条件は「自分の作った宿敵が一人だけ生存すること」で、自身の生死は不問です。</li>
<li>自分以外を宿敵の対象に選ぶことができる (<a href="../rule.php#system_vote">他人撃ち</a>) 人数の制限は<a href="lovers.php">恋人陣営</a>と同じで、<br>
  明記されていなければ自分撃ち固定制限はありません。
</li>
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
<a href="sub_role.php#rival">宿敵</a>に加えて、<a href="sub_role.php#death_warrant">死の宣告</a> (7日目昼) を付加する特殊な決闘者。
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

<h3 id="critical_duelist">剣闘士 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<pre>
自分撃ち固定で、<a href="sub_role.php#critical_voter">会心</a>相当 (<a href="../weather.php#weather_critical">烈日</a>は無効) の能力を持つ上位決闘者。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#critical_avenger">狂骨</a>の逆アプローチです。
</pre>

<h3 id="triangle_duelist">舞首 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β1～]</h3>
<pre>
<a href="sub_role.php#rival">宿敵</a>を三人作る特殊な決闘者。
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
<a href="#poison_avenger">山わろ</a>
<a href="#cursed_avenger">がしゃどくろ</a>
<a href="#critical_avenger">狂骨</a>
<a href="#revive_avenger">夜刀神</a>
</p>

<h2 id="avenger_rule">基本ルール [復讐者系]</h2>
<ol>
<li>初日の夜に一定数の「<a href="sub_role.php#enemy">仇敵</a>」を作ります。</li>
<li>勝利条件は「自分の作った仇敵を全滅させること」で、自身の生死は不問です。</li>
<li><a href="sub_role.php#enemy">仇敵</a>の人数は村の人口の四分の一で、人口判定は端数切り捨てです (例：22人村なら5人)。</li>
</ol>

<h3 id="avenger">復讐者 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β3～]</h3>
<pre>
復讐者系の基本種。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
「自分の敵を自分で選んでもらう」ことがコンセプトです。
相手には自覚がないので、一方的に敵視する形になります。
</pre>

<h3 id="poison_avenger">山わろ (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<h4>[毒能力] 処刑：人狼系 + 妖狐陣営 + 自分の仇敵 / 襲撃：有り / 薬師判定：有り</h4>
<pre>
毒を持った特殊な復讐者。
毒の対象は、人狼系、妖狐陣営、自分の仇敵のいずれか。
自分の仇敵であっても、毒の対象外だった場合は中らない。
</pre>
<h4>関連役職</h4>
<pre>
<a href="ability.php#poison">毒能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="chiroptera.php#poison_chiroptera">毒蝙蝠</a>の復讐者バージョンです。
勝利条件の差を考慮して<a href="human.php#guard_hunt">狩り</a>対象からは外してあります。
</pre>

<h3 id="cursed_avenger">がしゃどくろ (占い結果：村人(呪返し) / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<h4>[耐性] 占い：呪返し / 陰陽師：死亡 / 護衛：狩り</h4>
<pre>
呪いを持った特殊な復讐者。処刑投票先が<a href="wolf.php#wolf_group">人狼系</a>・<a href="fox.php">妖狐</a>なら<a href="sub_role.php#death_warrant">死の宣告</a> (4日後)を付加する。
<a href="human.php#voodoo_killer">陰陽師</a>の占い・<a href="human.php#guard_hunt">狩人の護衛</a>で死亡する。
</pre>
<ol>
<li><a href="../spec.php#vote_day">判定</a>時に対象が死亡していた場合は無効 (例：処刑・毒死)。</li>
<li>自分が毒やショック死で死亡した場合でも有効。</li>
<li>自分が処刑された場合は無効。</li>
<li>変化前の<a href="mania.php#soul_mania">覚醒者</a>・<a href="mania.php#dummy_mania">夢語部</a>・<a href="mania.php#unknown_mania_group">鵺系</a>は対象外。</li>
<li><a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態)・<a href="sub_role.php#challenge_lovers">難題</a>には無効。</li>
</ol>
<h4>関連役職</h4>
<pre>
<a href="ability.php#vote_action">処刑投票付加能力者</a>・<a href="ability.php#cursed_group">呪い能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="chiroptera.php#cursed_chiroptera">呪蝙蝠</a>の復讐者バージョンです。
伝承では<a href="ogre.php#cursed_yaksa">滝夜叉姫</a>と関連があるので能力を合わせてあります。
</pre>

<h3 id="critical_avenger">狂骨 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<h4>[耐性] 護衛：狩り</h4>
<pre>
処刑投票先に<a href="sub_role.php#critical_luck">痛恨</a>を付加する特殊な復讐者。
<a href="human.php#guard_hunt">狩人の護衛</a>で死亡する。
</pre>
<ol>
<li><a href="../spec.php#vote_day">判定</a>時に対象が死亡していた場合は無効 (例：処刑・毒死)。</li>
<li>自分が毒やショック死で死亡した場合でも有効。</li>
<li>自分が処刑された場合は無効。</li>
<li><a href="human.php#detective_common">探偵</a>・<a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態)・<a href="sub_role.php#challenge_lovers">難題</a>には無効。</li>
</ol>
<h4>関連役職</h4>
<pre>
<a href="ability.php#vote_action">処刑投票付加能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#critical_mad">釣瓶落とし</a>の復讐者バージョンです。
</pre>

<h3 id="revive_avenger">夜刀神 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<h4>[耐性] 人狼襲撃：死亡 + 蘇生 (1回限定) / 蘇生：不可 / 憑依：無効</h4>
<pre>
人狼に襲撃されて死亡した場合、一度だけ即座に蘇生する特殊な復讐者。
</pre>
<ol>
<li>一度蘇生すると能力を失う (<a href="sub_role.php#lost_ability">能力喪失</a>)。</li>
<li>恋人になったら蘇生能力は無効。</li>
<li>人狼の襲撃以外で死亡した場合 (例：暗殺)、蘇生能力は無効。</li>
<li>身代わり君か、襲撃者が<a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態) だった場合、蘇生能力は無効。</li>
<li><a href="ability.php#revive_limit">蘇生対象外</a> (選ばれた場合は失敗する)。</li>
<li><a href="ability.php#possessed">憑依能力者</a>の憑依対象外。</li>
</ol>
<h4>関連役職</h4>
<pre>
<a href="ability.php#revive">蘇生能力者</a>・<a href="ability.php#possessed_limit">憑依制限能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#revive_pharmacist">仙人</a>の復讐者バージョンです。「やとのかみ」と読みます。
</pre>


<h2 id="patron_group">後援者系</h2>
<p><a href="#patron_rule">基本ルール</a></p>
<p>
<a href="#patron">後援者</a>
<a href="#sacrifice_patron">身代わり地蔵</a>
<a href="#shepherd_patron">羊飼い</a>
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

<h3 id="sacrifice_patron">身代わり地蔵 (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<h4>[耐性] 人狼襲撃：無効</h4>
<pre>
<a href="sub_role.php#supported">受援者</a>に<a href="sub_role.php#protected">庇護者</a>を付加する上位後援者。
人狼に襲撃されても死亡しない (襲撃は失敗扱い)。
襲撃者が<a href="wolf.php#sirius_wolf">天狼</a> (完全覚醒状態) だった場合は耐性無効。
</pre>
<h4>関連役職</h4>
<pre>
<a href="ability.php#resist_wolf">人狼襲撃耐性能力者</a>・<a href="ability.php#sacrifice">身代わり能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="mania.php#sacrifice_mania">影武者</a>の後援者バージョンです。
</pre>

<h3 id="shepherd_patron">羊飼い (占い結果：村人 / 霊能結果：村人) [Ver. 1.5.0 β4～]</h3>
<pre>
人口の六分の一の<a href="sub_role.php#supported">受援者</a>を作ることができるが、<a href="sub_role.php#mind_sheep">羊</a>を付加してしまう特殊な後援者。
人口判定は端数切り捨てで、6人以下の場合は1人 (例：22人村なら3人)。
</pre>
<h4>関連役職</h4>
<pre>
<a href="ability.php#resist_wolf">人狼襲撃耐性能力者</a>・<a href="ability.php#sacrifice">身代わり能力者</a>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="mania.php#sacrifice_mania">影武者</a>の後援者バージョンです。
</pre>
</body></html>
