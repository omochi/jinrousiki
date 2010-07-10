<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [妖狐陣営]', 'new_role');
?>
</head>
<body>
<h1>妖狐陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#fox_partner">仲間表示</a>
<a href="#fox_talk">夜の会話 (念話)</a>
</p>
<p>
<a href="#fox_group">妖狐系</a>
<a href="#child_fox_group">子狐系</a>
</p>

<h2><a name="fox_partner">仲間表示</a></h2>
<pre>
全ての妖狐は<a href="#silver_fox">銀狐</a>以外の<a href="#fox_group">妖狐系</a>・<a href="#child_fox_group">子狐系</a>が誰か分かります。
<a href="#fox_group">妖狐系</a>と<a href="#child_fox_group">子狐系</a>は別枠で表示されます (<a href="wolf.php">人狼陣営</a>における<a href="wolf.php#wolf_group">人狼系</a>と<a href="wolf.php#whisper_mad">囁き狂人</a>みたいなものです)。
分けている基準は「<a href="#fox_talk">念話</a>ができるかどうか」です。
<a href="#child_fox_group">子狐系</a>の枠に<a href="wolf.php#scarlet_wolf">紅狼</a>も混ざって表示されます。
<a href="sub_role.php#mind_lonely">はぐれ者</a>になると仲間が分からなくなります (<a href="#silver_fox">銀狐</a>と同じ)。
</pre>
<h3>Ver. 1.4.0 β8〜</h3>
<pre>
<a href="sub_role.php#mind_lonely">はぐれ者</a>になると仲間が分からなくなります (<a href="#silver_fox">銀狐</a>と同じ)。
</pre>
<h3>Ver. 1.4.0 α24〜</h3>
<pre>
<a href="#child_fox_group">子狐系</a>の枠に<a href="wolf.php#scarlet_wolf">紅狼</a>も混ざって表示されます。
</pre>
<h3>Ver. 1.4.0 α20〜</h3>
<pre>
<a href="#silver_fox">銀狐</a>は他の<a href="#fox_group">妖狐系</a>・<a href="#child_fox_group">子狐系</a>からも仲間であると分かりません。
</pre>
<h3>Ver. 1.4.0 α19〜</h3>
<pre>
<a href="#fox_group">妖狐系</a>から<a href="#child_fox">子狐</a>が誰か分かります。
<a href="#fox_group">妖狐系</a>と<a href="#child_fox">子狐</a>は別枠で表示されます。
</pre>
<h3>Ver. 1.4.0 α3-7〜</h3>
<pre>
全ての妖狐は<a href="#child_fox">子狐</a>以外の<a href="#fox_group">妖狐系</a>が誰か分かります。
<a href="#child_fox">子狐</a>は全ての妖狐が誰か分かります。
同一の枠で表示されるので種類は不明です。
</pre>

<h2><a name="fox_talk">夜の会話 (念話)</a></h2>
<pre>
<a href="#silver_fox">銀狐</a>以外の<a href="#fox_group">妖狐系</a>は夜に会話(念話)できます。
他人からはいっさい見えません。
<a href="#child_fox_group">子狐系</a>は念話を見ることも参加することも出来ません。
<a href="wolf.php#wise_wolf">賢狼</a>には念話が<a href="human.php#common_group">共有者</a>の囁きに変換されて表示されます。
<a href="sub_role.php#mind_lonely">はぐれ者</a>になると夜の発言が独り言になり、念話に参加できなくなります (<a href="#silver_fox">銀狐</a>と同じ)。
</pre>
<h3>Ver. 1.4.0 β8〜</h3>
<pre>
<a href="sub_role.php#mind_lonely">はぐれ者</a>になると夜の発言が独り言になり、念話に参加できなくなります (<a href="#silver_fox">銀狐</a>と同じ)。
</pre>
<h3>Ver. 1.4.0 α24〜</h3>
<pre>
<a href="wolf.php#wise_wolf">賢狼</a>には念話が<a href="human.php#common_group">共有者</a>の囁きに変換されて表示されます。
</pre>
<h3>Ver. 1.4.0 α19〜</h3>
<pre>
<a href="#silver_fox">銀狐</a>は念話できません。
</pre>
<h3>Ver. 1.4.0 α3-7〜</h3>
<pre>
全ての<a href="#fox_group">妖狐系</a>は夜に会話(念話)できます。
<a href="#child_fox_group">子狐系</a>は念話を見ることも参加することも出来ません。
</pre>


<h2><a name="fox_group">妖狐系</a></h2>
<p>
<a href="#white_fox">白狐</a>
<a href="#black_fox">黒狐</a>
<a href="#gold_fox">金狐</a>
<a href="#phantom_fox">幻狐</a>
<a href="#poison_fox">管狐</a>
<a href="#blue_fox">蒼狐</a>
<a href="#emerald_fox">翠狐</a>
<a href="#voodoo_fox">九尾</a>
<a href="#revive_fox">仙狐</a>
<a href="#possessed_fox">憑狐</a>
<a href="#cursed_fox">天狐</a>
<a href="#elder_fox">古狐</a>
<a href="#cute_fox">萌狐</a>
<a href="#scarlet_fox">紅狐</a>
<a href="#silver_fox">銀狐</a>
</p>

<h3><a name="white_fox">白狐</a> (占い結果：村人(呪殺無し) / 霊能結果：妖狐) [Ver. 1.4.0 α17〜]</h3>
<pre>
呪殺されない代わりに<a href="wolf.php#wolf_group">人狼</a>に襲われると殺される。
<a href="#child_fox">子狐</a>との違いは占いができない代わりに他の妖狐と<a href="#fox_talk">念話</a>ができる事。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#boss_wolf">白狼</a>の妖狐バージョンです。狼サイドからは村人と大差ないですが
村サイドにはかなりの脅威となるでしょう。
</pre>

<h3><a name="black_fox">黒狐</a> (占い結果：人狼(呪殺無し) / 霊能結果：妖狐) [Ver. 1.4.0 α24〜]</h3>
<pre>
占い結果が「人狼」 / 霊能結果が「妖狐」と判定される妖狐。
<a href="wolf.php#wolf_group">人狼</a>に襲撃されても死なない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
呪殺されない代わりに人狼扱いされる妖狐です。
人狼側にとっては、黒狐自体の存在よりも、それを占った
占い師を狂人だと思って放置していたら真だった、なんて
事態になりかねないことの方が問題になりそうです。
</pre>

<h3><a name="gold_fox">金狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
<a href="human.php#sex_mage">ひよこ鑑定士</a>の判定が<a href="chiroptera.php">蝙蝠</a>になる妖狐。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#gold_wolf">金狼</a>の妖狐バージョンです。
この役職でメイン役職の総数が既存のものと合わせてちょうど 100 になりました。
</pre>

<h3><a name="phantom_fox">幻狐</a> (占い結果：村人(呪殺) / 霊能結果：妖狐) [Ver. 1.4.0 β11〜]</h3>
<pre>
一度だけ、自分が占われても占い妨害をする事ができる妖狐。
妨害能力は<a href="wolf.php#phantom_wolf">幻狼</a>参照。
<a href="human.php#guard_hunt">狩人系に護衛</a>されると殺される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#phantom_wolf">幻狼</a>の妖狐バージョンです。
</pre>

<h3><a name="poison_fox">管狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<h4>[毒能力] 吊り：妖狐以外 / 襲撃：有り / 薬師判定：有り</h4>
<pre>
毒を持った妖狐。毒能力は<a href="human.php#poison_group">埋毒者</a>と同じだが、対象から妖狐陣営が除かれるため
投票者ランダムの場合は不発となるケースがある。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 110 が原型です。「くだぎつね」と読みます。
噛み無効の代わりに毒を持った妖狐です。仲間がいるときに真価を発揮します。
</pre>

<h3><a name="blue_fox">蒼狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<pre>
<a href="wolf.php#wolf_group">人狼</a>に襲撃されたら襲撃してきた人狼を<a href="sub_role.php#mind_lonely">はぐれ者</a>にする妖狐。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#blue_wolf">蒼狼</a>の妖狐バージョンです。
発動すると相手に正体がバレてしまうので、積極的に狙うというよりは
「噛まれた相手に一矢報いる」タイプの能力ですね。
</pre>

<h3><a name="emerald_fox">翠狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 β8〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：有効 / 呪い：有効</h4>
<pre>
占った人が会話できない妖狐だった場合に自分と占った人を<a href="sub_role.php#mind_friend">共鳴者</a>にする妖狐。
</pre>
<ol>
  <li>能力の発動対象は<a href="#silver_fox">銀狐</a>・<a href="#child_fox_group">子狐系</a>・<a href="sub_role.php#mind_lonely">はぐれ者</a>の妖狐のいずれかです。</li>
  <li>インターフェイスは占いと同じですが結果は何も表示されません。</li>
  <li><a href="sub_role.php#mind_friend">共鳴者</a>を作る事に成功すると能力を失います。</li>
  <li><a href="wolf.php#blue_wolf">蒼狼</a>の襲撃先を占って、占い先が<a href="sub_role.php#mind_lonely">はぐれ者</a>になってもその夜には能力は発動しません。</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#emerald_wolf">翠狼</a>の妖狐バージョンです。
一度しか使えないので、発動するタイミングや相手の選択がポイントになるかもしれません。
</pre>

<h3><a name="voodoo_fox">九尾</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<pre>
夜に村人一人を選び、その人に呪いをかける妖狐。
<a href="wolf.php#wolf_group">人狼</a>に襲撃されても死なないが、<a href="human.php#guard_hunt">狩人系に護衛</a>されると殺される。
</pre>
<ol>
  <li>呪われた人を占った<a href="human.php#mage_group">占い師</a>は呪返しを受けます</li>
  <li>呪われている役職を選んだ場合は本人が呪返しを受けます</li>
  <li>呪いをかけた人が他の人にも呪いをかけられていた場合は本人が呪返しを受けます</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#voodoo_mad">呪術師</a>の妖狐バージョンです。
新役職考案スレ (最下参照) の 58 が原型です。
対占い・対噛み耐性は通常の妖狐と同じですが
呪い能力を持った代わりに<a href="human.php#guard_hunt">狩人</a>にも弱くなっています。
</pre>

<h3><a name="revive_fox">仙狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 β2〜]</h3>
<pre>
蘇生能力を持った妖狐。
蘇生に関するルールは<a href="human.php#about_revive">蘇生能力者の基本ルール</a>参照。
蘇生成功率は 100% で、一度成功すると能力を失う。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#revive_cat">仙狸</a>の妖狐バージョンです。
確実に成功しますが、1/5 (20%) は誤爆になるので要注意です。
単純に味方の妖狐を蘇生させる以外の選択肢が一番有効になる
ケースがあるのが妖狐陣営の蘇生能力者のポイントです。
</pre>

<h3><a name="possessed_fox">憑狐</a> (占い結果：村人(呪殺) / 霊能結果：妖狐) [Ver. 1.4.0 β9〜]</h3>
<pre>
一度だけ、死体に憑依することができる妖狐。
</pre>
<ol>
  <li>身代わり君・<a href="human.php#revive_priest">天人</a>・<a href="human.php#detective_common">探偵</a>・<a href="wolf.php">人狼陣営</a>・<a href="lovers.php">恋人陣営</a>には憑依できません</li>
  <li><a href="human.php#voodoo_killer">陰陽師</a>に占われると殺されます</li>
  <li>憑依を実行した時に<a href="human.php#anti_voodoo">厄神</a>に護衛されると憑依に失敗します</li>
  <li>憑依を実行しなければ<a href="human.php#anti_voodoo">厄神</a>に護衛されても「厄払い成功」とは判定されません</li>
  <li>憑依を実行した時に占い能力者に占われても憑依妨害は受けません</li>
  <li>憑依中に<a href="human.php#anti_voodoo">厄神</a>に護衛されると憑依状態を解かれて元の体に戻されます</li>
  <li>狩人系に<a href="human.php#guard_hunt">護衛</a>されると殺されます</li>
  <li>複数の憑依能力者が同時に同じ人に憑依しようとした場合は全員憑依失敗扱いになります</li>
</ol>
<h4>Ver. 1.4.0 β12〜</h4>
<pre>
<a href="human.php#revive_priest">天人</a>・<a href="human.php#detective_common">探偵</a>には憑依できません。
(<a href="wolf.php#possessed_wolf">憑狼</a>が憑依できない役職には憑依できません)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#possessed_mad">犬神</a>の妖狐バージョンです。
<a href="wolf.php#possessed_wolf">憑狼</a>よりも看破されやすいので、能力を使いにくいと思います。
存在自体が脅威になるタイプですね。
</pre>

<h3><a name="cursed_fox">天狐</a> (占い結果：村人(呪返し) / 霊能結果：妖狐) [Ver. 1.4.0 α17〜]</h3>
<pre>
占われたら占った<a href="human.php#mage_group">占い師</a>を呪い殺す妖狐。
<a href="wolf.php#wolf_group">人狼</a>に噛まれても死なないが、<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される。
<a href="human.php#assassin_spec">暗殺反射</a>能力を持つ。
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
<a href="human.php#assassin_spec">暗殺反射</a>能力を持つ。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#cursed_wolf">呪狼</a>の妖狐バージョンで、妖狐族最上位種です。
呪いに対抗できる役職が出現するまでは狐無双が見られそうですね。
</pre>

<h3><a name="elder_fox">古狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 β5〜]</h3>
<pre>
投票数が +1 される妖狐。詳細は<a href="human.php#elder">長老</a>参照。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="human.php#elder">長老</a>の妖狐バージョンです。
狐サイドによる PP はめったに発生しないので、能力を有効活用するのは難しいでしょう。
</pre>

<h3><a name="cute_fox">萌狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 α24〜]</h3>
<pre>
昼の間だけ、低確率で発言が遠吠えに入れ替わってしまう妖狐。
遠吠えの内容は<a href="human.php#suspect">不審者</a>や<a href="wolf.php#cute_wolf">萌狼</a>と同じ。
</pre>
<h4>Ver. 1.4.0 β7〜</h4>
<pre>
遠吠えの入れ替え発動を昼限定に変更しました。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#cute_wolf">萌狼</a>の妖狐バージョンです。
<a href="human.php#suspect">不審者</a>と違い、占われたら呪殺されますが、いずれにしても
「村人判定された人が遠吠えをした」場合、占った人は偽者です。
</pre>

<h3><a name="scarlet_fox">紅狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 α24〜]</h3>
<pre>
<a href="wolf.php#wolf_group">人狼</a>から<a href="human.php#unconscious">無意識</a>に見える妖狐。
本物の<a href="human.php#unconscious">無意識</a>と混ざって表示されるため、人狼側からは区別できない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
やる夫人狼の初日呪殺アイコンの代名詞の一つである、ローゼンメイデンの真紅がモデルです。
新役職考案スレ (最下参照) の 383 が原型です。
始めは占い師に分かる狐にしましたがバランス取りが難しいのでこういう実装になりました。
「<a href="human.php#unconscious">無意識</a>」が騙れば人狼視点でほぼ紅狐確定と見なされるので注意が必要です。
</pre>

<h3><a name="silver_fox">銀狐</a> (占い結果：村人(呪殺) / 霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<pre>
<a href="#fox_partner">仲間</a>が分からない妖狐。
(他の妖狐・<a href="#child_fox">子狐</a>からも仲間であると分からない)
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#silver_wolf">銀狼</a>の妖狐バージョンです。
元々妖狐は出現人数が少なめなので仲間が分からなくてもさほど影響は無いと思います。
占いを騙る仲間から人狼判定を出される可能性はありますが……
</pre>

<h2><a name="child_fox_group">子狐系</a></h2>
<p>
<a href="#child_fox">子狐</a>
<a href="#sex_fox">雛狐</a>
</p>

<h3><a name="child_fox">子狐</a> (占い結果：村人(呪殺無し) / 霊能結果：子狐) [Ver. 1.4.0 α3-7〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：有効 / 呪い：有効</h4>
<pre>
呪殺されない代わりに<a href="wolf.php#wolf_group">人狼</a>に襲われると殺されます。
妖狐と<a href="#fox_talk">念話</a>できない代わりに占いができます。
判定結果は普通の<a href="human.php#mage_group">占い師</a>と同じで、呪殺は出来ませんが呪返しは受けます。
占いの成功率は 70% です。
</pre>
<h4>Ver. 1.4.0 α17〜</h4>
<pre>
占い能力を持ちました。
</pre>
<h4>Ver. 1.4.0 α8〜</h4>
<pre>
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、子狐視点、子狐を名乗る人は偽者です。
子狐が出現した場合は出現人数と同じだけ妖狐が減ります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職です。
妖狐陣営自体の出現数が少ないのでかなりのレア役職になりそうな予感。
</pre>

<h3><a name="sex_fox">雛狐</a> (占い結果：村人(呪殺無し) / 霊能結果：子狐) [Ver. 1.4.0 β8〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：有効 / 呪い：無効</h4>
<pre>
呪殺されない代わりに<a href="wolf.php#wolf_group">人狼</a>に襲われると殺されます。
妖狐と<a href="#fox_talk">念話</a>できない代わりに占いができます。
判定結果は普通の<a href="human.php#sex_group">ひよこ鑑定士</a>と同じです。
占いの成功率は 70% です。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#child_fox">子狐</a>の<a href="human.php#sex_group">ひよこ鑑定士</a>バージョンです。
能力よりも、存在自体が脅威となるタイプですね。
村や狼が疑心暗鬼になって<a href="human.php#sex_group">ひよこ鑑定士</a>の排除に動くケースが出てくるでしょう。
</pre>
</body></html>
