<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [神話マニア陣営]', 'new_role');
?>
</head>
<body>
<h1>神話マニア陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#rule">基本ルール</a>
<a href="#change_mania_group">所属変更</a>
</p>
<p>
<a href="#mania_group">神話マニア系</a>
</p>

<h2><a id="rule">基本ルール</a></h2>
<ol>
  <li>初日の夜に誰か一人を選んでその人と同じ陣営に変化する特殊な陣営です。</li>
  <li>勝利条件はコピー先の陣営になります。</li>
  <li>なんらかの理由でコピーが成立しなかった場合は村人陣営と扱われます。</li>
  <li>コピーが成立する前に突然死した場合の<a href="human.php#medium">巫女</a>の判定は村人陣営です。</li>
</ol>

<h2><a id="change_poison_cat_group">所属変更</a></h2>
<h4>Ver. 1.4.0 β13〜</h4>
<pre>
<a href="#mania_group">神話マニア系</a>の所属を<a href="human.php">村人陣営</a>から変更しました。
</pre>

<h2><a id="mania_group">神話マニア系</a></h2>
<p>
<a href="#mania">神話マニア</a>
<a href="#trick_mania">奇術師</a>
<a href="#soul_mania">覚醒者</a>
<a href="#unknown_mania">鵺</a>
<a href="#dummy_mania">夢語部</a>
</p>

<h3><a id="mania">神話マニア</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職をコピーします。
入れ替わるのは2日目の朝で、神話マニア系を選んだ場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
カード人狼にある役職です。元と違い、占いや狼以外の役職もコピーします。
CO するべきかどうかは、コピーした役職次第です。
</pre>

<h3><a id="trick_mania">奇術師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職をコピーします。
入れ替わるのは2日目の朝で、神話マニア系を選んだ場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。

もし、コピー先が身代わり君以外で、「初日投票をしてなかった」場合はその役職を奪い取り、
相手はその系統の基本職に入れ替わってしまいます。
</pre>
<h4>コピーの結果例</h4>
<pre>
1. A[奇術師] → B[魂の占い師] =&gt; A[魂の占い師] B[魂の占い師]
初日に投票しているので入れ替わりが発生しません。
<a href="human.php#mage_group">占い師系</a>・<a href="human.php#mind_scanner_group">さとり系</a>・<a href="fox.php#child_fox_group">子狐系</a>・<a href="lovers.php">恋人陣営</a>・<a href="chiroptera.php#fairy_group">妖精系</a>と、
一部の<a href="wolf.php#mad_group">狂人</a>・<a href="fox.php#fox_group">妖狐</a>がこれに該当します

2. A[奇術師] → B[巫女] =&gt; A[巫女] B[霊能者]
入れ替わりが発生してもコピー先には特にメッセージが出ないので、
朝、突然役職表記が入れ替わってしまうことになります。

3. A[奇術師] → B[夢守人] =&gt; A[夢守人] B[狩人]
この場合はコピー先は入れ替わりを自覚できないことになります。

4. A[奇術師] → B[天人] =&gt; A[天人] B[天人]
天人は初日に投票しませんが、死亡処理が入るので例外的に入れ替え対象外です。

5. A[奇術師] → B[舌禍狼] → 身代わり君 =&gt; A[舌禍狼] B[舌禍狼]
投票している狼をコピーした場合は入れ替えが発生しません

6. A[奇術師] → B[罠師] =&gt; A[罠師] B[狂人]
初日に投票していない狂人は入れ替えが発生します
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
「所属陣営は初日の夜の投票で確定する」というルールの範囲内で
「相手の能力を奪う」役職を作れないかな、と思案してこういう実装になりました。
</pre>

<h3><a id="soul_mania">覚醒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職の上位種に変化する特殊な<a href="#mania">神話マニア</a>。
</pre>
<ol>
<li>入れ替わるのは4日目の朝で、それまでは覚醒者のままです</li>
<li>2日目の朝にどの役職系になるのか (コピー先の役職の系統) 分かります<br>
例) A[覚醒者] → B[白狼]  =&gt; 「Bさんは人狼でした」
</li>
<li>4日目の朝にどの役職になったのか分かります。</li>
<li>神話マニア系を選んだ場合は村人になります。</li>
<li>蘇生されるケースがあるので、死亡していても入れ替わり処理は行なわれます</li>
</ol>
<pre>
コピー元の系統 → コピー結果
</pre>
<ol>
<li><a href="human.php#human_group">村人系</a> → <a href="human.php#executor">執行者</a></li>
<li><a href="human.php#mage_group">占い師系</a> → <a href="human.php#soul_mage">魂の占い師</a></li>
<li><a href="human.php#necromancer_group">霊能者系</a> → <a href="human.php#soul_necromancer">雲外鏡</a></li>
<li><a href="human.php#medium_group">巫女系</a> → <a href="human.php#revive_medium">風祝</a> (Ver. 1.4.0 β13〜)</li>
<li><a href="human.php#priest_group">司祭系</a> → <a href="human.php#bishop_priest">司教</a></li>
<li><a href="human.php#guard_group">狩人系</a> → <a href="human.php#poison_guard">騎士</a></li>
<li><a href="human.php#common_group">共有者系</a> → <a href="human.php#ghost_common">亡霊嬢</a></li>
<li><a href="human.php#poison_group">埋毒者系</a> → <a href="human.php#strong_poison">強毒者</a></li>
<li><a href="human.php#poison_cat_group">猫又系</a> → <a href="human.php#revive_cat">仙狸</a></li>
<li><a href="human.php#pharmacist_group">薬師系</a> → <a href="human.php#pharmacist">薬師</a></li>
<li><a href="human.php#assassin_group">暗殺者系</a> → <a href="human.php#soul_assassin">辻斬り</a> (Ver. 1.4.0 β13〜)</li>
<li><a href="human.php#mind_scanner_group">さとり系</a> → <a href="human.php#howl_scanner">吠騒霊</a></li>
<li><a href="human.php#jealousy_group">橋姫系</a> → <a href="human.php#poison_jealousy">毒橋姫</a></li>
<li><a href="human.php#doll_group">上海人形系</a> → <a href="human.php#doll_master">人形遣い</a></li>
<li><a href="wolf.php#wolf_group">人狼系</a> → <a href="wolf.php#sirius_wolf">天狼</a></li>
<li><a href="wolf.php#mad_group">狂人系</a> → <a href="wolf.php#whisper_mad">囁き狂人</a></li>
<li><a href="fox.php#fox_group">妖狐系</a> → <a href="fox.php#cursed_fox">天狐</a></li>
<li><a href="fox.php#child_fox_group">子狐系</a> → <a href="fox.php#jammer_fox">月狐</a> (Ver. 1.4.0 β14〜)</li>
<li><a href="lovers.php#cupid_group">キューピッド系</a> → <a href="lovers.php#mind_cupid">女神</a></li>
<li><a href="lovers.php#angel_group">天使系</a> → <a href="lovers.php#ark_angel">大天使</a></li>
<li><a href="quiz.php#quiz_group">出題者系</a> → <a href="quiz.php#quiz">出題者</a></li>
<li><a href="vampire.php#vampire_group">吸血鬼系</a> → <a href="vampire.php#vampire">吸血鬼</a> (Ver. 1.4.0 β14〜)</li>
<li><a href="chiroptera.php#chiroptera_group">蝙蝠系</a> → <a href="chiroptera.php#boss_chiroptera">大蝙蝠</a></li>
<li><a href="chiroptera.php#fairy_group">妖精系</a> → <a href="chiroptera.php#light_fairy">光妖精</a></li>
<li><a href="mania.php#mania_group">神話マニア系</a> → 村人</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#incubate_poison">潜毒者</a>の亜種を作ろうとして色々構想を練った結果、こういう実装になりました。
能力発動のタイミングを考慮して<a href="human.php#incubate_poison">潜毒者</a>より一日早く入れ替え処理を行っています。
</pre>

<h3><a id="unknown_mania">鵺</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α23〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人と同じ所属陣営になります。
結果が表示されるのは 2 日目の朝で、自分と投票先に<a href="sub_role.php#mind_friend">共鳴者</a>がつきます。
入れ替わるのは2日目の朝です。
生存カウントは常に村人なので、実質は所属陣営不明の狂人相当です。

<a href="#mania">神話マニア</a>と違い、コピー結果が出ないのでコピー先に聞かないと
自分の所属陣営が分かりません。
</pre>
<h4>所属陣営の判定例</h4>
<pre>
1. 鵺 → 村人 (村人陣営)
擬似共有者となります

2. 鵺 → 人狼 (人狼陣営)
投票先とだけ会話できる<a href="wolf.php#whisper_mad">囁き狂人</a>相当です。

3. 鵺 → 妖狐 (妖狐陣営)
所属は妖狐ですが自身は妖狐カウントされないので気をつけましょう。

4. 鵺 → キューピッド (恋人陣営)
自分の恋人を持たないキューピッド相当になります。

5. 鵺 → 蝙蝠 (蝙蝠陣営)
投票先と会話できる蝙蝠相当になります。
相手の生死と自分の勝敗は無関係です。

6. 鵺 → 人狼[恋人] (人狼陣営)
サブ役職は判定対象外(<a href="human.php#medium">巫女</a>と同じ)なので
コピー先と勝利陣営が異なる、例外ケースとなります。

7. 鵺 → 人狼[サトラレ] (人狼陣営)
コピー先が村人陣営の<a href="human.php#mind_scanner">さとり</a>に会話を覗かれている状態なので
コピー先からの情報入手が難しくなります。

8. 鵺 → 吸血鬼 (吸血鬼陣営)
吸血鬼陣営の勝利条件の仕様上、鵺は絶対に勝てない事になります。
従って、吸血鬼は素直に自分の正体を告げない方がいいと思われます。

9. 鵺 → 鵺 → 人狼 (全員人狼陣営)
コピー先が鵺だった場合は鵺以外の役職に当たるまで
コピー先を辿って判定します。

10. 鵺A → 鵺B → 鵺C → 鵺A (全員村人陣営)
コピー先を辿って自分に戻った場合は村人陣営になります。

11. 鵺 → 神話マニア → 妖狐 (妖狐陣営)
神話マニアをコピーした場合はコピー結果の陣営になります。

12. 鵺A → 神話マニア → 鵺B → 人狼
神話マニアは鵺をコピーしたら村人になるので鵺のリンクが切れます。
結果として以下のようになります。
鵺A(村人陣営) → 村人(元神話マニア)、鵺B (人狼陣営) → 人狼
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
やる夫人狼の薔薇 GM に、「初心者の指南用に使える役職」を
要請されてこういう実装にしてみました。
鵺が初心者をコピーして指南するイメージですね。

もしも、教えてもらう前にコピー先が死んでしまったら自分の所属陣営は
「正体不明」になる事になります。とっても理不尽ですね。
</pre>

<h3><a id="dummy_mania">夢語部</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職の基本・劣化種に変化する<a href="#mania">神話マニア</a>の亜種。
本人の表記は「<a href="#soul_mania">覚醒者</a>」で、仕様も同じ。
変化前に<a href="wolf.php#dream_eater_mad">獏</a>に襲撃されると殺される。
</pre>
<pre>
コピー元の系統 → コピー結果
</pre>
<ol>
<li><a href="human.php#human_group">村人系</a> → <a href="human.php#suspect">不審者</a></li>
<li><a href="human.php#mage_group">占い師系</a> → <a href="human.php#dummy_mage">夢見人</a></li>
<li><a href="human.php#necromancer_group">霊能者系</a> → <a href="human.php#dummy_necromancer">夢枕人</a></li>
<li><a href="human.php#medium_group">巫女系</a> → <a href="human.php#medium">巫女</a></li>
<li><a href="human.php#priest_group">司祭系</a> → <a href="human.php#crisis_priest">預言者</a></li>
<li><a href="human.php#guard_group">狩人系</a> → <a href="human.php#dummy_guard">夢守人</a></li>
<li><a href="human.php#common_group">共有者系</a> → <a href="human.php#dummy_common">夢共有者</a></li>
<li><a href="human.php#poison_group">埋毒者系</a> → <a href="human.php#dummy_poison">夢毒者</a></li>
<li><a href="human.php#poison_cat_group">猫又系</a> → <a href="human.php#sacrifice_cat">猫神</a></li>
<li><a href="human.php#pharmacist_group">薬師系</a> → <a href="human.php#cure_pharmacist">河童</a></li>
<li><a href="human.php#assassin_group">暗殺者系</a> → <a href="human.php#eclipse_assassin">蝕暗殺者</a></li>
<li><a href="human.php#mind_scanner_group">さとり系</a> → <a href="human.php#mind_scanner">さとり</a></li>
<li><a href="human.php#jealousy_group">橋姫系</a> → <a href="human.php#jealousy">橋姫</a></li>
<li><a href="human.php#doll_group">上海人形系</a> → <a href="human.php#doll">上海人形</a></li>
<li><a href="wolf.php#wolf_group">人狼系</a> → <a href="wolf.php#cute_wolf">萌狼</a></li>
<li><a href="wolf.php#mad_group">狂人系</a> → <a href="wolf.php#mad">狂人</a></li>
<li><a href="fox.php#fox_group">妖狐系</a> → <a href="fox.php#cute_fox">萌狐</a></li>
<li><a href="fox.php#child_fox_group">子狐系</a> → <a href="fox.php#sex_fox">雛狐</a></li>
<li><a href="lovers.php#cupid_group">キューピッド系</a> → <a href="lovers.php#self_cupid">求愛者</a></li>
<li><a href="lovers.php#angel_group">天使系</a> → <a href="lovers.php#angel">天使</a></li>
<li><a href="quiz.php#quiz_group">出題者系</a> → <a href="quiz.php#quiz">出題者</a></li>
<li><a href="vampire.php#vampire_group">吸血鬼系</a> → <a href="vampire.php#vampire">吸血鬼</a> (Ver. 1.4.0 β14〜)</li>
<li><a href="chiroptera.php#chiroptera_group">蝙蝠系</a> → <a href="chiroptera.php#dummy_chiroptera">夢求愛者</a></li>
<li><a href="chiroptera.php#fairy_group">妖精系</a> → <a href="chiroptera.php#mirror_fairy">鏡妖精</a></li>
<li><a href="mania.php#mania_group">神話マニア系</a> → 村人</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#soul_mania">覚醒者</a>の夢バージョンです。
最終的には自覚することができるので他の夢系と比べると対応はしやすいかもしれません。
</pre>
</body></html>
