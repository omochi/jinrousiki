<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [村人陣営]', 'new_role');
?>
</head>
<body>
<h1>村人陣営</h1>
<p>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>
<p>
<a href="#human_group">村人系</a>
<a href="#mage_group">占い師系</a>
<a href="#necromancer_group">霊能者系</a>
<a href="#priest_group">司祭系</a>
<a href="#guard_group">狩人系</a>
<a href="#common_group">共有者系</a>
<a href="#poison_group">埋毒者系</a>
<a href="#poison_cat_group">猫又系</a>
<a href="#pharmacist_group">薬師系</a>
<a href="#assassin_group">暗殺者系</a>
<a href="#mind_scanner_group">さとり系</a>
<a href="#jealousy_group">橋姫系</a>
<a href="#doll_group">上海人形系</a>
<a href="#mania_group">神話マニア系</a>
</p>

<h2><a name="human_group">村人系</a></h2>
<p>
<a href="#human_rule">村人表記役職</a>
</p>
<p>
<a href="#elder">長老</a>
<a href="#saint">聖女</a>
<a href="#executor">執行者</a>
<a href="#escaper">逃亡者</a>
<a href="#suspect">不審者</a>
<a href="#unconscious">無意識</a>
</p>

<h3><a name="human_rule">村人表記役職</a></h3>
<pre>
本人の表示が「村人」になる役職は<a href="#saint">聖女</a>・<a href="#executor">執行者</a>・<a href="#suspect">不審者</a>・<a href="#unconscious">無意識</a>・<a href="#crisis_priest">預言者</a>・<a href="#chain_poison">連毒者</a>です。

<a href="#suspect">不審者</a>の発言が遠吠えに変換される確率は 1% (管理者は設定ファイルで変更可能) です。
変換されたかどうかは本人にしか分からず、客観的な証明は不可能なので
空発言を多数行なって確認する試みは確率的にもほとんど意味が無いでしょう。
</pre>

<h3><a name="elder">長老</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β5〜]</h3>
<pre>
投票数が +1 される村人。
</pre>
<ol>
  <li>権力者とセットになった場合はさらに +1 されます</li>
  <li><a href="sub_role.php#random_voter">気分屋</a>とセットになった場合は追加で補正されます</li>
  <li><a href="sub_role.php#watcher">傍観者</a>とセットになった場合は 0 で固定です</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
権力者相当の能力を持った村人です。
PP ラインの計算を難しくさせるために作成してみました。
能力の特性上、これを騙るのはほぼ不可能なので同じ能力を持った他陣営種が存在します。
</pre>

<h3><a name="saint">聖女</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β7〜]</h3>
<pre>
再投票の最多得票者になった場合に、内訳によって吊られる人を変化させる村人。
本人表記は「村人」。
</pre>
<ol>
  <li>聖女の判定は<a href="#executor">執行者</a>の判定の後</li>
  <li>非村人 (村人陣営以外 + 恋人) を一人だけ含む → 非村人が吊られる</li>
  <li>非村人が複数含まれている → 再投票</li>
  <li>全員村人 + 最多得票者の聖女は自分だけ → 自分が吊られる</li>
  <li>全員村人 + 最多得票者の聖女が複数いる → 再投票</li>
  <li>自分が恋人だった場合は自分も非村人扱い<br>
    例1) 聖女・聖女[恋人]・村人 → 聖女[恋人] が吊られる<br>
    例2) 聖女[恋人]・人狼 → 再投票
  </li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
やる夫人狼のプレイヤーさんがモデルです。
判定法則が少々複雑ですが、基本的には村人陣営が有利になる結果になります。
通常の決定者同様、地味ですが勝負所で効いてくる存在になることでしょう。
</pre>

<h3><a name="executor">執行者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
再投票発生時に非村人 (村人陣営以外 + 恋人) に投票していた場合は吊る事が出来る村人。
本人表記は「村人」。
</pre>
<ol>
  <li>判定は<a href="../spec.php#vote_day">決定者系</a>と<a href="#saint">聖女</a>の間</li>
  <li>投票先が非村人 → 非村人が吊られる</li>
  <li>投票先が村人 → 再投票</li>
  <li>執行者が複数 + 非村人に投票していたのは一人だけ → 非村人が吊られる<br>
    例) 執行者A → 村人A、執行者B → 村人B、執行者C → 人狼A<br>
    　=&gt; 人狼A が吊られる
  </li>
  <li>執行者が複数 + 複数が同じ非村人に投票 → 非村人が吊られる<br>
    例) 執行者A → 村人A、執行者B → 人狼A、執行者C → 人狼A<br>
    　=&gt; 人狼A が吊られる
  </li>
  <li>執行者が複数 + 別々の非村人に投票 → 再投票<br>
    例) 執行者A → 村人A、執行者B → 人狼A、執行者C → 妖狐A<br>
    　=&gt; 再投票
  </li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
iM@S人狼のプレイヤーさんへの誕生日プレゼントです。
「なんとなく人外に投票する程度の能力」を形にしてみました。
</pre>

<h3><a name="escaper">逃亡者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
2日目の夜以降、生きている誰かの側に逃亡して生存を図ろうとする村人。
</pre>
<ol>
  <li>勝利条件は「村人陣営の勝利」＋「自身の生存」です</li>
  <li>人狼に直接狙われても殺されません (襲撃は失敗扱い)</li>
  <li>人狼に狙われていたことを自覚できません</li>
  <li>逃亡先が人狼だった場合は殺されます</li>
  <li>逃亡先が人狼に襲撃されたら自分も殺されます</li>
  <li>逃亡先が護衛や狐などで人狼の襲撃が失敗しても殺されます</li>
  <li>何らかの理由で人狼に殺された場合の死因は「人狼に襲撃された」です</li>
  <li>逃亡先に<a href="wolf.php#trap_mad">罠師</a>の罠が設置されていたら死亡します</a>
  <li><a href="#assassin_group">暗殺者系</a>に狙われても殺されません</li>
  <li>遺言を残せません</li>
</ol>

<h4>[作成者からのコメント]</h4>
<pre>
他国に実在する役職です。
</pre>

<h3><a name="suspect">不審者</a> (占い結果：人狼 / 霊能結果：村人) [Ver. 1.4.0 α9〜]</h3>
<pre>
不審なあまり、占い師に人狼と判定されてしまう村人で、本人表記は「村人」。
また、昼の間だけ、低確率で発言が人狼の遠吠えに入れ替わってしまう (<a href="wolf.php#cute_wolf">萌狼</a>と同じ)。
</pre>
<h4>Ver. 1.4.0 β7〜</h4>
<pre>
遠吠えの入れ替え発動を昼限定に変更しました。
</pre>
<h4>Ver. 1.4.0 α16〜</h4>
<pre>
低確率で発言が遠吠えに入れ替わってしまう (<a href="wolf.php#cute_wolf">萌狼</a>と同じ)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
村人陣営ですが、人狼陣営に有利な存在です。
立ち回りの上手い人がこれを引くと大変なことになりそうです。
ただし、人狼サイドにも不審者である事は分からないので
占い師の真贋が読みづらくなります。
</pre>

<h3><a name="unconscious">無意識</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α13〜]</h3>
<pre>
他の国で言うと「自覚のない夢遊病者」。
本人には「村人」と表示されているが、夜になると無意識に歩きまわるため
人狼に無意識であることが分かってしまう。
</pre>
<h4>Ver. 1.4.0 α24〜</h4>
<pre>
<a href="wolf.php#wolf_group">人狼系</a>からは<a href="fox.php#scarlet_fox">紅狐</a>も無意識に見えます。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#suspect">不審者</a>同様、村人陣営ですが人狼陣営に有利な存在です。
人狼サイドから見ると無能力であることが確定の村人なので噛まれにくいです。
確定白で長期間放置されると<a href="wolf.php#boss_wolf">白狼</a>扱いされるかも。
</pre>


<h2><a name="mage_group">占い師系</a></h2>
<p>
<a href="#mage_rule">基本ルール</a>
</p>
<p>
<a href="#soul_mage">魂の占い師</a>
<a href="#psycho_mage">精神鑑定士</a>
<a href="#sex_mage">ひよこ鑑定士</a>
<a href="#voodoo_killer">陰陽師</a>
<a href="#dummy_mage">夢見人</a>
</p>

<h3><a name="mage_rule">占い能力の基本ルール</a></h3>
<pre>
占い能力は人狼の襲撃や暗殺などで事前に死んでいたら無効になります。
また、占い対象先が同様の理由で事前に死んでいたら対象の能力は無効になります。

例)
1. 人狼に噛まれた占い師が妖狐を占っていても無効
2. <a href="#assassin">暗殺者</a>に殺された<a href="wolf.php#cursed_wolf">呪狼</a>を占い師が占っても呪返しは受けない
</pre>

<h3><a name="soul_mage">魂の占い師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α3-7〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：有り / 月兎：有効 / 呪い：有効</h4>
<pre>
占った人の役職が分かる上位占い師。
<a href="fox.php#fox_group">妖狐</a>を占っても呪殺できないが、占い妨害や呪返しは受けるので注意。
</pre>
<h4>Ver. 1.4.0 α15〜</h4>
<pre>
<a href="fox.php#fox_group">妖狐</a>を占っても呪殺できません。
判定は「妖狐」と出るので、頑張って信用を勝ち取って吊ってください。
</pre>
<h4>Ver. 1.4.0 α8〜</h4>
<pre>
<a href="../chaos.php#chaos">通常闇鍋モード</a>では16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、魂の占い師視点、魂の占い師を名乗る人は偽者です。
魂の占い師が出現した場合は出現人数と同じだけ占い師が減ります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方ウミガメ人狼のプレイヤーさんがモデルです。
<a href="wolf.php#boss_wolf">白狼</a>だろうが<a href="fox.php#child_fox">子狐</a>だろうが分かってしまうので村側最強クラスですが、
その分狙われやすいでしょう。
</pre>
<h4>[作成者からのアドバイス]</h4>
<pre>
状況によっては、始めは普通の占い師として振舞うのも手。
また、魂の占い師を CO していても村側役職 (狩人や<a href="#assassin">暗殺者</a>など) を素直に言うと
人狼に狙われるので適度に結果を騙り、相手と合わせるのも手。
(例えば、狩人→<a href="#poison_guard">騎士</a>、埋毒者→狩人、<a href="#dummy_poison">夢毒者</a>→埋毒者)
逆に言うと、騙る場合は「村側陣営」などとごまかす事で破綻しにくくなる。
</pre>

<h3><a name="psycho_mage">精神鑑定士</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α18〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：有効 / 呪い：無効</h4>
<pre>
村人の心理状態を調べて「嘘つき」を探し出す特殊な占い師。
<a href="wolf.php#mad_group">狂人系</a>・夢系・<a href="#suspect">不審者</a>・<a href="#unconscious">無意識</a>を占うと「嘘をついている」と判定される。
それ以外は「正常である」と判定される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
対狂人専門の占い師です。一部の本人視点と実際の役職が違うタイプ
(夢系・<a href="#suspect">不審者</a>・<a href="#unconscious">無意識</a>)にも対応しています。
精神鑑定士を真と見るなら占われた人視点の役職がほぼ確定します。
人狼や妖狐の騙りは見抜けないので注意してください。
</pre>

<h3><a name="sex_mage">ひよこ鑑定士</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α19〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：有効 / 呪い：無効</h4>
<pre>
村人の性別を判別する特殊な占い師。
<a href="chiroptera.php">蝙蝠</a>・<a href="wolf.php#gold_wolf">金狼</a>・<a href="fox.php#gold_fox">金狐</a>を占った場合は「蝙蝠」と判定される。
</pre>
<h4>Ver. 1.4.0 β8〜</h4>
<pre>
<a href="wolf.php#gold_wolf">金狼</a>・<a href="fox.php#gold_fox">金狐</a>を占った場合は「蝙蝠」と判定される。
</pre>
<h4>Ver. 1.4.0 α21〜</h4>
<pre>
<a href="chiroptera.php">蝙蝠</a>を占った場合は「蝙蝠」と判定される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#psycho_mage">精神鑑定士</a>の話を出したときの流石鯖の管理人さんのコメントが
きっかけで生まれた役職です。
出現当初は特にメリットがありませんでしたが
Ver. 1.4.0 α21 から登場の蝙蝠陣営の鑑定能力を持ちました。
</pre>


<h3><a name="voodoo_killer">陰陽師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：特殊 / 月兎：無効 / 呪い：解呪</h4>
<pre>
対呪い専門の特殊な占い師。
占った人が呪い持ちや憑依能力者の場合は呪殺し(死亡メッセージは呪返しと同じ)、
誰かに呪いをかけられていた場合は解呪(呪返しが発動しない)する。
呪殺か解呪が成功した場合のみ、次の日に専用のシステムメッセージが表示される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
呪い系統の対抗役職です。
積極的に呪い持ち(<a href="wolf.php#cursed_wolf">呪狼</a>、<a href="fox.php#cursed_fox">天狐</a>、<a href="chiroptera.php#cursed_chiroptera">呪蝙蝠</a>)や憑依能力者(<a href="wolf.php#possessed_wolf">憑狼</a>・<a href="wolf.php#possessed_mad">犬神</a>・<a href="fox.php#possessed_fox">憑狐</a>)を探しに行く場合は
普通の占い師と同じ感覚でいいですが、呪術系能力者(<a href="wolf.php#voodoo_mad">呪術師</a>、<a href="fox.php#voodoo_fox">九尾</a>)による
占い師の呪返しを防ぐのが狙いなら、同時に同じ人を
占う必要があるので動き方が難しくなります。
そもそも呪い系がレアなので役に立つのか分かりませんが……
</pre>


<h3><a name="dummy_mage">夢見人</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α14〜]</h3>
<h4>[占い能力] 呪殺：無し / 憑依妨害：無し / 月兎：無効 / 呪い：無効</h4>
<pre>
本人には「占い師」と表示されており、占い行動もできるが結果は逆になる。
呪殺できない代わりに呪返しも受けない。
特殊蝙蝠(<a href="chiroptera.php#boss_chiroptera">大蝙蝠</a>)は正しい結果が表示される。
<a href="wolf.php#jammer_mad">月兎</a>による占い妨害の影響を受けない。
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
特殊蝙蝠(<a href="chiroptera.php#boss_chiroptera">大蝙蝠</a>)は正しい結果が表示される。
</pre>
<h4>Ver. 1.4.0 α18〜</h4>
<pre>
占い結果がランダムから「村人」⇔「人狼」反転に変わりました。
確定白(例えば共有者)を占って人狼判定が出たら本人視点夢見人確定です。
また、<a href="#psycho_mage">精神鑑定士</a>から「嘘つき」判定を受けても同様です。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職提案スレッド＠やる夫(最下参照) の 17 が原型です。
完全ランダムでは占い結果が全く役に立たなくなるので
白黒反転に変更しました。
</pre>


<h2><a name="necromancer_group">霊能者系</a></h2>
<p>
<a href="#necromancer_rule">基本ルール</a>
</p>
<p>
<a href="#soul_necromancer">雲外鏡</a>
<a href="#yama_necromancer">閻魔</a>
<a href="#dummy_necromancer">夢枕人</a>
<a href="#medium">巫女</a>
</p>


<h3><a name="necromancer_rule">霊能の基本ルール</a></h3>
<pre>
村人が吊らないといけない人外なのに、占い師では人外判定を出せないか
呪返しを受ける役職 (例：<a href="wolf.php#boss_wolf">白狼</a>、<a href="fox.php#cursed_fox">天狐</a>、<a href="fox.php#child_fox">子狐</a>) は霊能で分かります。
詳細は個々の役職の霊能結果を確認してください。
</pre>


<h3><a name="soul_necromancer">雲外鏡</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<pre>
「村人」と「人狼」だけではなく、役職が分かる上位霊能者。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#soul_mage">魂の占い師</a>の霊能者バージョンです。
占いと違ってメリットが少ないので後回しにしていましたが<a href="#dummy_necromancer">夢枕人</a>とセットで出すことで
こっちは本人視点、判定に偽りが絶対に無いというアドバンテージが与えられます。
しかし、「死人に口無し」故に魂の占い師よりもはるかに騙りやすいですね。
</pre>


<h3><a name="yama_necromancer">閻魔</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<pre>
前日の死者の<a href="../spec.php#dead">死因</a>が分かる特殊な霊能者。
死因は画面の下に表示される「〜は無残な〜」の下の行に
「(〜は人狼に襲撃されたようです)」等と表示される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="../spec.php#dead">死因</a>が多岐にわたる闇鍋用の特殊霊能者です。
死因が分かるだけなので昼の毒巻き込まれや暗殺された人等、
死者の役職が分からない可能性もある点に注意してください。
</pre>


<h3><a name="dummy_necromancer">夢枕人</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<pre>
本人には「霊能者」と表示されており、霊能判定も表示されるが結果は逆になる。
特殊狼(<a href="wolf.php#boss_wolf">白狼</a>・<a href="wolf.php#possessed_wolf">憑狼</a>など)や、特殊妖狐(<a href="fox.php#cursed_fox">天狐</a>・<a href="fox.php#child_fox">子狐</a>など)は正しい結果が表示される。
<a href="wolf.php#corpse_courier_mad">火車</a>の能力の影響を受けない。
</pre>
<h4>Ver. 1.4.0 α21〜</h4>
<pre>
<a href="wolf.php#corpse_courier_mad">火車</a>の能力の影響を受けません。
</pre>
<h4>Ver. 1.4.0 α18〜</h4>
<pre>
霊能結果がランダムから「村人」⇔「人狼」反転に変わりました。
<a href="#psycho_mage">精神鑑定士</a>から「嘘つき」判定を受けたら本人視点夢枕人確定です。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#dummy_mage">夢見人</a>の霊能者バージョンです。
完全ランダムでは霊能結果が全く役に立たなくなるので
白黒反転に変更しました。
</pre>


<h3><a name="medium">巫女</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α3-7〜]</h3>
<pre>
突然死した人の所属陣営が分かる特殊な霊能者。
闇鍋モードで登場する「ショック死」する人たちの情報を取るのが主な仕事だが
普通の霊能と違うので注意。

所属陣営とは、勝敗が決まったときの陣営で、役職表記の先頭に記載されいてる「〜陣営」を指す。
例1) <a href="wolf.php#wolf_group">人狼系</a>・<a href="wolf.php#mad_group">狂人系</a>は「人狼」
例2) <a href="fox.php#fox_group">妖狐系</a>・<a href="fox.php#child_fox_group">子狐系</a>は「妖狐」
例3) <a href="lovers.php#cupid_group">キューピッド系</a>・<a href="lovers.php#angel_group">天使系</a>は「恋人」
例4) <a href="quiz.php#quiz">出題者</a>は「出題者」
例5) <a href="chiroptera.php#chiroptera_group">蝙蝠系</a>・<a href="chiroptera.php#fairy_group">妖精系</a>は「蝙蝠」

また、メイン役職のみが判定の対象 (サブ役職は分からない)。
つまり、恋人はサブ役職なので「恋人」と判定されるのは<a href="lovers.php#cupid_group">キューピッド系</a>・<a href="lovers.php#angel_group">天使系</a>のみ。
</pre>
<h4>Ver. 1.4.0 β6〜</h4>
<pre>
<a href="#unknown_mania">鵺</a>の所属陣営が正しく出ないバグ修正 (修正前は常時村人判定)
</pre>
<h4>Ver. 1.4.0 α9〜</h4>
<pre>
恋人後追いにも対応 (後追いした恋人のみ、元の所属陣営が分かる)
</pre>
<h4>Ver. 1.4.0 α8〜</h4>
<pre>
通常闇鍋モードではキューピッドが出現している場合は確実に出現します。
(ただし、巫女が出現してもキューピッドが出現しているとは限りません)
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
式神研のオリジナル役職です。
闇鍋モードで大量の突然死が出ることになったので作ってみましたが
霊能者より地味な存在ですね。騙るのも容易なのでなかなか報われないかもしれません。
</pre>


<h2><a name="priest_group">司祭系</a></h2>
<p>
<a href="#priest_rule">人外勝利前日判定ルール</a>
</p>
<p>
<a href="#priest">司祭</a>
<a href="#bishop_priest">司教</a>
<a href="#crisis_priest">預言者</a>
<a href="#revive_priest">天人</a>
</p>


<h3><a name="priest_rule">人外勝利前日判定ルール</a></h3>
<pre>
1. 生存者 - (人狼 + 妖狐) &lt;= 人狼 + 2
その日の吊りが人狼以外 + 夜に人狼の噛みが成立すると人狼勝利となります。
メッセージは「人狼が勝利目前」です。

2. 「条件1 が成立している」または「人狼が残り一人」 + 妖狐 / 恋人が生存している
妖狐が生存していれば「妖狐が勝利目前」、
恋人が生存していれば「恋人が勝利目前」と判定されます

3. 生存者 &lt;= 恋人 + 2
生存者が全員恋人になると恋人勝利となります。
メッセージは「恋人が勝利目前」です。
</pre>


<h3><a name="priest">司祭</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α24〜]</h3>
<pre>
一定日数ごとに現在、生存している村人陣営の人数が分かる。
</pre>
<ol>
  <li>能力の発動間隔は 4日目以降、1日おき (4 → 6 → 8 →...)</li>
  <li>村人陣営の判定法則は<a href="#medium">巫女</a>と同じ</li>
  <li>判定結果は夜も表示されたままですが、昼の処刑結果は反映されていません</li>
  <li><a href="#revive_priest">天人</a>の蘇生判定は司祭判定の後に行なわれます<br>
    従って、「司祭の判定 + <a href="#revive_priest">天人</a>が蘇生した人数」 が司祭視点の正しい値です
  </li>
  <li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職で、新役職考案スレ (最下参照) の 72 が原型です。
オリジナルは配役非通知設定の闇鍋用役職なので、能力を発動した時点で生存している
役職の内訳が完全に分かりますが、式神研バージョンはかなり情報が絞られています。
</pre>

<h3><a name="bishop_priest">司教</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
一定日数ごとに現在、死亡している村人陣営以外の人数が分かる、特殊な司祭。
</pre>
<ol>
  <li>能力の発動間隔は 3日目以降、1日おき (3 → 5 → 7 →...)</li>
  <li><a href="#medium">巫女</a>の判定と違い、恋人も「村人陣営」以外と判定される</li>
  <li>判定結果は夜も表示されたままですが、昼の処刑結果は反映されていません</li>
  <li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#priest">司祭</a>の亜種でこちらは死者の内訳を推測することができます。
特に、3日目の情報は身代わり君の所属陣営を絞り込むのに有効です。
</pre>

<h3><a name="crisis_priest">預言者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β2〜]</h3>
<pre>
今が「<a href="#priest_rule">人外勝利前日</a>」かどうかが分かる特殊な司祭 (表示は「村人」)。
「人外勝利前日である」と判定された場合は、どの陣営が有利なのかメッセージが表示される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
村の危機を告げる特殊な司祭です。
いわゆる「鉄火場」は狂人や蝙蝠の存在 + 恋人の元の役職によって
機械的な判定ができないので判定条件を「システム的な勝敗決定前日」としました。
</pre>


<h3><a name="revive_priest">天人</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β2〜]</h3>
<pre>
2日目の朝にいきなり死亡して、特定の条件を満たすと生き返る特殊な司祭。
「霊界で配役を公開しない」オプションが有効になっていないと死亡も蘇生もしません。
</pre>
<h4>蘇生条件 (以下のどれか一つを満たせば蘇生する)</h4>
<ol>
  <li>「<a href="#priest_rule">人外勝利前日</a>」である</li>
  <li>5日目である</li>
  <li>村の人口が半分以下になった</li>
  <li>生存している人狼が一人になった</li>
</ol>
<h4>詳細な仕様</h4>
<ol>
  <li>2日目の朝の死亡メッセージは「〜は無惨な〜」で、死因は「天に帰った」です</li>
  <li>一度蘇生すると能力を失います (「能力を失った」という趣旨のメッセージが追加されます)</li>
  <li>恋人になると能力を失います (2日目朝の死亡も起こりません)</li>
  <li><a href="#mania">神話マニア</a>がコピーした場合は 2日目朝の死亡処理が起こりません</li>
  <li>2日目朝以降に死んでも蘇生判定を満たせば生き返ります</li>
  <li>5日目になると能力を失います</li>
  <li><a href="#poison_cat">猫又</a>系の蘇生対象外です (選ばれた場合は失敗します)</li>
  <li><a href="wolf.php#possessed_wolf">憑狼</a>の憑依対象外です (襲撃された場合は普通に殺されます)</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 54 が原型です。
復活した天人は恋人でない事が保証されるので非常に頼りになります。
</pre>

<h2><a name="guard_group">狩人系</a></h2>
<p>
<a href="#guard_limit">護衛制限</a>
<a href="#guard_hunt">狩りルール</a>
</p>
<p>
<a href="#poison_guard">騎士</a>
<a href="#fend_guard">忍者</a>
<a href="#reporter">ブン屋</a>
<a href="#anti_voodoo">厄神</a>
<a href="#dummy_guard">夢守人</a>
</p>

<h3><a name="guard_limit">護衛制限</a></h3>
<ol>
  <li>制限対象は、<a href="#priest">司祭</a>・<a href="#bishop_priest">司教</a>・<a href="#reporter">ブン屋</a>・<a href="#detective_common">探偵</a>・<a href="#assassin_group">暗殺者系</a>・<a href="#doll_master">人形遣い</a>です</li>
  <li>対象を護衛して襲撃された場合、狩人に「護衛成功」のメッセージは出ますが、護衛先は噛み殺されます</li>
  <li><a href="#poison_guard">騎士</a>には適用されません</li>
</ol>

<h3><a name="guard_hunt">狩りルール</a></h3>
<pre>
1. 狩り能力があるのは狩人・<a href="#poison_guard">騎士</a>・<a href="#fend_guard">忍者</a>です
2. <a href="wolf.php#dream_eater_mad">獏</a>と<a href="#dummy_guard">夢守人</a>の関係は<a href="wolf.php#dream_eater_mad">獏</a>の項目を参照してください
3. 狩り対象は特殊狂人・特殊妖狐・特殊蝙蝠です
3-1. 特殊狂人 (<a href="wolf.php#jammer_mad">月兎</a>・<a href="wolf.php#voodoo_mad">呪術師</a>・<a href="wolf.php#corpse_courier_mad">火車</a>・<a href="wolf.php#agitate_mad">扇動者</a>・<a href="wolf.php#miasma_mad">土蜘蛛</a>・<a href="wolf.php#dream_eater_mad">獏</a>・<a href="wolf.php#trap_mad">罠師</a>・<a href="wolf.php#possessed_mad">犬神</a>)
3-2. 特殊妖狐 (<a href="fox.php#phantom_fox">幻狐</a>・<a href="fox.php#voodoo_fox">九尾</a>・<a href="fox.php#revive_fox">仙狐</a>・<a href="fox.php#possessed_fox">憑狐</a>・<a href="fox.php#cursed_fox">天狐</a>)
3-3. 特殊蝙蝠 (<a href="chiroptera.php#poison_chiroptera">毒蝙蝠</a>・<a href="chiroptera.php#cursed_chiroptera">呪蝙蝠</a>・<a href="chiroptera.php#boss_chiroptera">大蝙蝠</a>)
4. <a href="#dummy_guard">夢守人</a>は<a href="chiroptera.php#fairy_group">妖精系</a>を狩ることができます
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
<a href="#dummy_guard">夢守人</a>は<a href="chiroptera.php#fairy_group">妖精系</a>を狩ることができます
</pre>

<h3><a name="poison_guard">騎士</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α3-7〜]</h3>
<h4>[狩人能力] 護衛：制限無し / 狩り：有り / 罠：有効</h4>
<h4>[毒能力] 吊り：無し / 襲撃：有り / 薬師判定：限定的</h4>
<pre>
噛まれた時のみ毒が発動する上位狩人。
<a href="#guard_limit">護衛制限</a>の影響を受けない。
狩り能力は<a href="#guard_hunt">狩りルール</a>を参照。
</pre>
<h4>Ver. 1.4.0 α8〜</h4>
<pre>
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、騎士視点、騎士を名乗る人は偽者です。
しかし、埋毒者や狩人が騎士を騙る可能性もあるので敵対陣営とは限りません。
騎士が出現した場合は出現人数と同じだけ狩人と埋毒者が減ります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方ウミガメ人狼のプレイヤーさんがモデルです。
技術的に簡単だったので軽い気持ちで作ってみましたがとんでもなく強いようです。
Ver. 1.4.0 α8 以降は出現率を大幅に落としたのでこれでバランスが取れるかな？
</pre>


<h3><a name="fend_guard">忍者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β5〜]</h3>
<h4>[狩人能力] 護衛：制限有り / 狩り：有り / 罠：有効</h4>
<pre>
一度だけ人狼の襲撃に耐えることができる上位狩人。
人狼に襲撃されると「あなたは能力を失いました」というメッセージが追加表示される。
誰に噛まれたかは分からないが、「能力を失って」も護衛は通常通り行える。
狩り能力は<a href="#guard_hunt">狩りルール</a>を参照。
身代わり君がこれになった場合は能力発動しない (普通に噛み殺される)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方陰陽鉄のプレイヤーさんの誕生日記念に作ってみました。
狼視点では狐と区別がつかないので、ある意味で<a href="#poison_guard">騎士</a>以上に
やっかいな存在になるでしょう。
</pre>


<h3><a name="reporter">ブン屋</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α14〜]</h3>
<h4>[狩人能力] 護衛：特殊 / 狩り：無し / 罠：有効</h4>
<pre>
夜に投票(尾行)した人が人狼に襲撃された場合に、誰が襲撃したか分かる特殊な狩人。
通常闇鍋モードの出現法則は調整中です。
</pre>
<ol>
  <li>人狼・妖狐を尾行したら殺されます (死亡メッセージは「無惨な〜」、死因は「人外尾行」)</li>
  <li>遺言を残せません</li>
  <li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
  <li>尾行対象者が狩人に護衛されていた場合は何も出ません</li>
  <li>人狼が人外(狐や<a href="wolf.php#silver_wolf">銀狼</a>など)を襲撃して失敗した場合は尾行成功扱いとなります (殺されません)<br>
    (尾行成功メッセージ＆対象が死んでいない＝狼が噛めない人外を噛んだ)</li>
</ol>
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 5 が原型です。
活躍するのは非常に難しいですが、成功したときには大きなリターンがあります。
狐噛みの現場をスクープするブン屋を是非見てみたいものです。
</pre>


<h3><a name="anti_voodoo">厄神</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<h4>[狩人能力] 護衛：特殊 / 狩り：無し / 罠：無効</h4>
<pre>
護衛した人の厄 (占い妨害、呪返し、憑依) を祓う特殊な狩人。
成功した場合は次の日に専用のシステムメッセージが表示される。
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
憑依中の<a href="wolf.php#possessed_mad">犬神</a>・<a href="fox.php#possessed_fox">憑狐</a>を直接護衛すると憑依状態を解く事ができる。
</pre>
<h4>Ver. 1.4.0 α24〜</h4>
<pre>
憑依中の<a href="wolf.php#possessed_wolf">憑狼</a>に対しては圧倒的なアドバンテージを持っており、
直接護衛するか、襲撃されると憑依状態を解くことができる。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
対占い妨害・呪い専門の狩人です。「やくじん」と読みます。
新役職考案スレ (最下参照) の 43 が原型です。
厄神の護衛を受けることで<a href="wolf.php#cursed_wolf">呪狼</a>に狼判定を出したり、
<a href="fox.php#cursed_fox">天狐</a>を呪殺することが可能になります。
通常の狩人が狂人・妖狐でも護衛成功してしまうのと同様に
狂人や妖狐にも占い妨害や呪返しを受ける役職がいるので、
「厄払い成功＝対象者は村陣営」とは限らない点に注意してください。
</pre>


<h3><a name="dummy_guard">夢守人</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<h4>[狩人能力] 護衛：特殊 / 狩り：特殊 / 罠：無効</h4>
<pre>
本人には「狩人」と表示されており、護衛行動を取ることができる。
必ず護衛成功メッセージが表示されるが、表示されるだけで誰も護衛していない。
<a href="wolf.php#dream_eater_mad">獏</a>には圧倒的なアドバンテージを持っており、何らかの形で遭遇すると
一方的に狩ることができる。
<a href="chiroptera.php#fairy_group">妖精系</a>を護衛すると狩ることができる。
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
<a href="chiroptera.php#fairy_group">妖精系</a>を護衛すると狩ることができる
</pre>
<h4>Ver. 1.4.0 α21〜</h4>
<pre>
<a href="wolf.php#dream_eater_mad">獏</a>には圧倒的なアドバンテージを持っており、何らかの形で遭遇すると
一方的に狩ることができる。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#dummy_mage">夢見人</a>の狩人バージョンです。
常に護衛成功メッセージが出るので一度でも失敗したら夢守人で無い事を確認できます。
みなさん一度くらいは全て護衛成功して勝利してみたいと思った事、ありませんか？
</pre>


<h2><a name="common_group">共有者系</a></h2>
<p>
<a href="#detective_common">探偵</a>
<a href="#trap_common">策士</a>
<a href="#ghost_common">亡霊嬢</a>
<a href="#dummy_common">夢共有者</a>
</p>

<h3><a name="detective_common">探偵</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
様々な特殊耐性を持つ、上位共有者。
</pre>
<ol>
  <li>毒吊りで巻き込まれる対象になりません</li>
  <li><a href="#assassin_spec">暗殺反射</a>能力を持つ</li>
  <li><a href="wolf.php#miasma_mad">土蜘蛛</a>の能力無効</li>
  <li><a href="#about_revive">蘇生</a>不可</li>
  <li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
  <li><a href="wolf.php#possessed_wolf">憑狼</a>の憑依対象外です (襲撃された場合は普通に殺されます)</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
闇鍋モードでも探偵村を実施できるようにチューニングした上位共有者です。
</pre>

<h3><a name="trap_common">策士</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
昼の投票時に、その時点で生存している村人陣営以外の人全てから投票されたら
まとめて死亡させる上位共有者。
</pre>
<ol>
  <li><a href="#medium">巫女</a>の判定と違い、恋人も「村人陣営」以外と判定される</li>
  <li>一人でも村人陣営の人から投票されると無効</li>
  <li>発動した場合に巻き込んだ人の死因は「罠」</li>
  <li>自分が吊られたり、再投票になっても有効</li>
  <li>本人が恋人になった場合は自分自身が「非村人陣営」になるので発動できない</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
圧倒的不利な状況をたった一回の投票でひっくり返す、究極の対 PP 兵器です。
<a href="#jealousy">橋姫</a>同様、役職の存在による抑止力の発生が主眼です。

「自分は村人だから投票しない」と主張されたらそれまでなので、能力を前面に出して
自分に投票させる作戦は通用しないでしょう。
</pre>

<h3><a name="ghost_common">亡霊嬢</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<pre>
噛まれたら、噛んだ<a href="wolf.php#wolf_group">人狼</a>に<a href="sub_role.php#chicken">小心者</a>を付加する上位共有者。
<a href="wolf.php#possessed_wolf_sub_role">憑狼</a>が噛んだ場合は本体に付加される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方ウミガメ人狼のプレイヤーさんがモデルです。
即死こそしませんが、<a href="wolf.php#resist_wolf">抗毒狼</a>でも無効化できないので非常に強力です。
<a href="#trap_common">策士</a>同様、人狼陣営にとっては役職の存在自体が脅威になるでしょう。
</pre>

<h3><a name="dummy_common">夢共有者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<pre>
本人には「『相方が身代わり君』の共有者」と表示されている村人。
が、夜に発言しても「ひそひそ声」にはならないし、本物の共有者の声も聞こえない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#dummy_mage">夢見人</a>の共有者バージョンです。
「ひそひそ声」が発生しないので真共有者にはなれません。
(闇鍋モードであっても「ひそひそ声」は消しません。仕様です)
証明手段が無いので容易に騙れますね。きっと真でも吊られることでしょう。
</pre>


<h2><a name="poison_group">埋毒者系</a></h2>
<p>
<a href="#change_poison_group">所属変更</a>
</p>
<p>
<a href="#strong_poison">強毒者</a>
<a href="#incubate_poison">潜毒者</a>
<a href="#guide_poison">誘毒者</a>
<a href="#chain_poison">連毒者</a>
<a href="#dummy_poison">夢毒者</a>
</p>

<h3><a name="change_poison_group">所属変更</a></h3>
<h4>Ver. 1.4.0 β2〜</h4>
<pre>
<a href="#poison_cat">猫又</a>の所属を<a href="#poison_cat_group">猫又系</a>に変更しました。
</pre>

<h3><a name="strong_poison">強毒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<h4>[毒能力] 吊り：人狼 + 妖狐 / 襲撃：有り / 薬師判定：強い毒</h4>
<pre>
吊られた時に人外(狼と狐)のみを巻き込む上位埋毒者です。
ただし、本人には「埋毒者」と表示されているので自覚はありません。
埋毒者の巻き込み対象設定 (<a href="../../script_info.php" target="_top">特徴と仕様</a>参照) が投票者ランダムだった場合、
人外が投票していなければ毒は不発となります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方ウミガメ人狼のプレイヤーさんがモデルです。
状況にもよりますが、<a href="#soul_mage">魂の占い師</a>に鑑定してもらったら即吊ってもらうと強いですね。
投票者ランダムの設定であれば、その時の投票結果は重要な推理材料にもなります。
</pre>


<h3><a name="incubate_poison">潜毒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<h4>[毒能力] 吊り：無し → 人狼 + 妖狐 / 襲撃：無し → 有り / 薬師判定：無し → 強い毒</h4>
<pre>
一定期間後 (現在は 5 日目以降) に毒を持つ特殊な埋毒者です。
毒を持ったら本人に追加のシステムメッセージが表示されます。
毒能力は<a href="#strong_poison">強毒者</a>相当です。
</pre>
<h4>Ver. 1.4.0 α20〜</h4>
<pre>
毒能力を埋毒者相当から強毒者相当に変更しました。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方ウミガメ人狼のプレイヤーさんがモデルです。
いかに毒を持つまで時間を稼ぐかがポイントです
</pre>

<h3><a name="guide_poison">誘毒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<h4>[毒能力] 吊り：毒能力者 / 襲撃：毒能力者 / 薬師判定：限定的</h4>
<pre>
毒能力者のみに中る特殊な埋毒者。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#pharmacist_group">薬師系</a>と違うアプローチで毒能力を持った人外を仕留めることができますが
毒と名のつく役職全てが対象なので<a href="#poison_guard">騎士</a>・<a href="#chain_poison">連毒者</a>に中ると大惨事になります。
</pre>

<h3><a name="chain_poison">連毒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<h4>[毒能力] 吊り：特殊 / 襲撃：無し / 薬師判定：限定的</h4>
<pre>
他の毒能力者に巻き込まれたら、さらに二人巻き込む特殊な埋毒者。本人の表記は「村人」。
<a href="#pharmacist">薬師</a>に投票されていたら解毒される (連鎖が発生しない)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
裏世界鯖＠東方陰陽鉄人狼のとある村がモデルです。
発動率は低いですが、ひとたび発動すると大惨事を引き起こします。
連毒者を巻き添えにするとさらに連鎖するので一回の吊りで全滅する可能性もあります。
</pre>

<h3><a name="dummy_poison">夢毒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<h4>[毒能力] 吊り：特殊 / 襲撃：無し / 薬師判定：無し</h4>
<pre>
本人には「埋毒者」と表示されている村人。
吊られた場合は<a href="wolf.php#dream_eater_mad">獏</a>・<a href="chiroptera.php#fairy_group">妖精系</a>のみ巻き込む (「解毒」はできない)。
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
吊られた場合は<a href="wolf.php#dream_eater_mad">獏</a>・<a href="chiroptera.php#fairy_group">妖精系</a>のみ巻き込む (「解毒」はできない)。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#dummy_mage">夢見人</a>の埋毒者バージョンです。
毒は持っていませんが、身代わり君がこれになることはありません。
偽者ではありますがどちらかと言うと人狼が不利になる役職ですね。
夢毒者である事に賭けて真埋毒者を噛みに行く狼が出るかもしれません。

Ver. 1.4.0 β9 からは吊られた時のみ<a href="wolf.php#dream_eater_mad">獏</a>・<a href="chiroptera.php#fairy_group">妖精系</a>に中る仕様に変更しました。
夢の世界の攻防なので<a href="#pharmacist_group">薬師系</a>による解毒はできません。
</pre>


<h2><a name="poison_cat_group">猫又系</a></h2>
<p>
<a href="#about_revive">基本ルール</a>
<a href="#change_poison_cat_group">所属変更</a>
</p>
<p>
<a href="#poison_cat">猫又</a>
<a href="#revive_cat">仙狸</a>
<a href="#sacrifice_cat">猫神</a>
</p>

<h3><a name="about_revive">蘇生能力者の基本ルール</a></h3>
<ol>
  <li>「霊界で配役を公開しない」オプションが有効になっていないと蘇生行動はできません</li>
  <li>投票可能になるのは 2日目の夜からで、[蘇生する / しない] を必ず投票する必要があります</li>
  <li>投票できるのは、身代わり君以外の死者です</li>
  <li>「蘇生を行わない」を選ぶこともできます</li>
  <li>蘇生成功率のうち、1/5 は指定した人以外が対象になる「誤爆蘇生」となります<br>
    例) 25% : 成功 : 20% / 誤爆 :  5%</li>
  <li>身代わり君、蘇生能力者 (猫又系と<a href="#revive_priest">天人</a>)、恋人、<a href="#detective_common">探偵</a>は蘇生できません</li>
  <li>蘇生対象外の人が選ばれた場合は確実に失敗します</li>
  <li>蘇生に失敗した場合は霊界にだけ見えるシステムメッセージが表示されます</li>
</ol>

<h4>Ver. 1.4.0 β2〜</h4>
<pre>
恋人を蘇生対象外に変更しました (蘇生後、即自殺から仕様変更しました)
</pre>

<h4>Ver. 1.4.0 α19〜</h4>
<pre>
<a href="#poison_cat">猫又</a>を蘇生対象外に変更しました
</pre>

<h3><a name="change_poison_cat_group">所属変更</a></h3>
<h4>Ver. 1.4.0 β2〜</h4>
<pre>
<a href="#poison_cat">猫又</a>の所属を<a href="#poison_group">埋毒者系</a>から変更しました。
</pre>

<h3><a name="poison_cat">猫又</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α18〜]</h3>
<h4>[毒能力] 吊り：有り / 襲撃：有り / 薬師判定：有り</h4>
<pre>
蘇生能力を持った埋毒者。蘇生成功率は 25%。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。
</pre>
<h4>Ver. 1.4.0 β2〜</h4>
<pre>
所属を<a href="#poison_group">埋毒者系</a>から<a href="#poison_cat_group">猫又系</a>に変更しました。
</pre>
<h4>Ver. 1.4.0 α19〜</h4>
<pre>
猫又が蘇生する事はありません。
猫又が蘇生対象者に選ばれた場合は失敗扱いになります。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職です。
「霊界で配役を公開しない」オプションを有効にしておかないと
ただの埋毒者になる点に注意してください。
</pre>

<h3><a name="revive_cat">仙狸</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β2〜]</h3>
<pre>
毒能力を失った代わりに高い蘇生能力を持った<a href="#poison_cat">猫又</a>の上位種。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。
蘇生成功率は 80% で、成功するたびに成功率が 1/4 になる。
80% → 20% → 5% → 2% → 1% (以後は 1% で固定)
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
仙狸 (センリ) とは、中国の猫の妖怪です (「狸」は山猫の意)。
東方陰陽鉄人狼のプレイヤーさんのコメントを参考に同じ猫の妖怪である
<a href="#poison_cat">猫又</a>の上位種として実装してみました。
</pre>

<h3><a name="sacrifice_cat">猫神</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
毒能力を失った代わりに確実な蘇生に特化した<a href="#poison_cat">猫又</a>の亜種。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。
蘇生成功率は 100% で、例外的に誤爆率が 0% に設定されているが、成功すると自分が死亡する。
複数の猫神が同時に同じ人を蘇生しようとした場合は「全員成功」扱いとなり、本人は死亡する。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="wolf.php#possessed_mad">犬神</a>が能力を発動した時に蘇生能力者が誰もいないと
正体がばれてしまうので、同じ状況に見える村陣営種を用意しました。
</pre>


<h2><a name="pharmacist_group">薬師系</a></h2>
<p>
<a href="#pharmacist">薬師</a>
<a href="#cure_pharmacist">河童</a>
</p>


<h3><a name="pharmacist">薬師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α12〜]</h3>
<pre>
毒持ちを吊ったときに、薬師が投票していたら解毒(毒が発動しない)します。
また、昼に投票した人が毒を持っているか翌朝に分かります。
朝に出るメッセージは以下の 5 種類。

1. 毒を持っていない (<a href="#dummy_poison">夢毒者</a>や発現前の<a href="#incubate_poison">潜毒者</a>もこれ)
2. 毒を持っている
3. 強い毒を持っている (<a href="#strong_poison">強毒者</a>と発現後の<a href="#incubate_poison">潜毒者</a>)
4. 限定的な毒を持っている (<a href="#poison_guard">騎士</a>・<a href="#chain_poison">連毒者</a>・<a href="#poison_jealousy">毒橋姫</a>)
5. 解毒に成功した (この場合は詳細な毒の種類は分からない)
</pre>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
<a href="#dummy_poison">夢毒者</a>が吊られると<a href="wolf.php#dream_eater_mad">獏</a>・<a href="chiroptera.php#fairy_group">妖精系</a>が巻き込まれる仕様に変更。
これを「解毒」する事はできません。
</pre>
<h4>Ver. 1.4.0 α23〜</h4>
<pre>
解毒成功だけでなく、前日に投票した人の詳細な毒能力が分かります
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 24 が原型です。「くすし」と読みます。
<a href="wolf.php#poison_wolf">毒狼</a>の対抗役職です。
<a href="#poison_group">埋毒者系</a>に対しても効果を発揮します。
</pre>

<h3><a name="cure_pharmacist">河童</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
昼に投票した人を解毒・ショック死抑制する特殊な薬師。
解毒能力は<a href="#pharmacist">薬師</a>と同じ。
抑制できるのは<a href="sub_role.php#chicken_group">小心者系</a>のみで、<a href="#jealousy">橋姫</a>・<a href="wolf.php#agitate_mad">扇動者</a>によるものは対象外。
解毒・ショック死抑制に成功すると次の日に「治療成功」という趣旨のメッセージが表示される。
何の「治療」に成功した(毒やショック死の種類など)のかは表示されない。
再投票時には発動しない。

例) A[河童] → B[村人][小心者]
この場合、Bがショック死する条件を満たしますが、河童の能力でキャンセルされます。
キャンセルするだけで<a href="sub_role.php#chicken">小心者</a>が消える訳ではないので注意。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 17 が原型です。
河童の膏薬伝説をヒントに、高い治療能力をもった特殊薬師としてデザインしました。
<a href="sub_role.php#febris">熱病</a>の性質上、<a href="wolf.php#miasma_mad">土蜘蛛</a>に対して完全なカウンターになっています。
</pre>


<h2><a name="assassin_group">暗殺者系</a></h2>
<p>
<a href="#assassin_spec">基本スペック</a>
</p>
<p>
<a href="#assassin">暗殺者</a>
<a href="#doom_assassin">死神</a>
<a href="#reverse_assassin">反魂師</a>
<a href="#eclipse_assassin">蝕暗殺者</a>
</p>

<h3><a name="assassin_spec">暗殺者系の基本スペック</a></h3>
<ol>
<li>暗殺対象にできない人はいません (人狼・妖狐でも選択可能)</li>
<li>特定の条件で「暗殺反射」(自分で自分を暗殺すること) が発生します</li>
<li>人狼の残り人数が二人以下の時に<a href="wolf.php#sirius_wolf">天狼</a>を対象にした場合は反射されます</li>
<li><a href="#detective_common">探偵</a>・<a href="fox.php#cursed_fox">天狐</a>を対象にした場合は反射されます</li>
<li>暗殺された人の死亡メッセージは人狼の襲撃と同じです</li>
<li>人狼に襲撃されたり、<a href="wolf.php#trap_mad">罠師</a>の罠にかかると暗殺は無効です</li>
<li>「暗殺する / しない」を必ず投票する必要があります</li>
<li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
<li>暗殺者がお互いを襲撃した場合は相打ちになります</li>
<li>暗殺者に暗殺された占い師の呪殺、<a href="#poison_cat">猫又</a>の蘇生は無効になります</li>
<li>暗殺者に暗殺されても<a href="#guard_group">狩人系</a>の護衛判定は有効です</li>
</ol>
<h4>Ver. 1.4.0 β9〜</h4>
<pre>
暗殺反射システムが実装されました
</pre>

<h3><a name="assassin">暗殺者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α18〜]</h3>
<pre>
夜に村人一人を選んで暗殺できます。詳細は<a href="#assassin_spec">暗殺者の基本スペック</a>参照。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
村人陣営の最終兵器とも呼べる存在ですね。
判定順とかが複雑なので色々調整が入るかもしれません。
新役職考案スレ (最下参照) の 8 が原型です。
</pre>

<h3><a name="doom_assassin">死神</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
夜に村人一人を選んで暗殺行動の代わりに<a href="sub_role.php#death_warrant">死の宣告</a>状態にすることができます。
詳細は<a href="#assassin_spec">暗殺者の基本スペック</a>参照。
</pre>
<ol>
<li>死の宣告の発動日は投票した夜から数えて2日後の昼です<br>
  (例：2日目夜に投票→4日目昼に発動)
</li>
<li>すでに宣告を受けている人にさらに投票した場合は期限が上書きされます<br>
  (例：4日目昼に発動表示→3日目夜にさらに投票→5日目昼に発動表示)
</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
「寿命を延ばすこともできる暗殺者」がコンセプトです。
新役職考案スレ (最下参照) の 85 が原型です。
</pre>

<h3><a name="reverse_assassin">反魂師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
夜に選んだ人が生きていたら暗殺、死んでいたら蘇生する特殊な暗殺者。
<a href="#assassin_spec">暗殺者の基本スペック</a>が適用される。
詳細な判定順は<a href="../spec.php#vote_night">詳細な仕様</a>参照。
</pre>
<ol>
<li>一度死んだ人の能力発動はキャンセルされます</li>
<li>「反魂」可能な対象は恋人以外全てです</li>
<li>自分が暗殺されても投票は有効です (暗殺系の処理は同時並行処理扱い)</li>
</ol>
<pre>
例1) A[反魂師] → B[占い師] ← C[反魂師]、B[占い師] → D[妖狐]
占い結果：何も出ない (呪殺もなし)
死体：B が無残な死体で発見されました (死因：「暗殺された」)
蘇生：B は生き返りました

例2) A[反魂師] → B[猫神] ← C[人狼]、B[猫神] → D[村人]
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：B は生き返りました
Bの蘇生処理はキャンセル

例3) A[暗殺者] → B[反魂師] → C[村人] ← D[人狼]
死体：B が無残な死体で発見されました (死因：「暗殺された」)
死体：C が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：C は生き返りました
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
基本的には<a href="#assassin">暗殺者</a>とほぼ同じ動きで問題ないでしょう。
確定で人狼に噛み殺されそうな人を狙った時に真価を発揮します。
失敗すると大惨事となりますが……
</pre>

<h3><a name="eclipse_assassin">蝕暗殺者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
<pre>
30% の確率で<a href="#assassin_spec">暗殺反射</a>が発生する劣化暗殺者。本人の表記は「<a href="#assassin">暗殺者</a>」。
<a href="#assassin_spec">暗殺者の基本スペック</a>が適用される。
<a href="#psycho_mage">精神鑑定士</a>の鑑定結果は「正常」。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#assassin">暗殺者</a>に暗殺のリスクを感じてもらうための存在です。
<a href="#assassin">暗殺者</a>が恋人になると大惨事が発生する可能性がありましたが
この役職の登場によって多少は緩和されるかもしれません。
</pre>

<h2><a name="mind_scanner_group">さとり系</a></h2>
<p>
<a href="#mind_scanner">さとり</a>
<a href="#evoke_scanner">イタコ</a>
<a href="#whisper_scanner">囁騒霊</a>
<a href="#howl_scanner">吠騒霊</a>
<a href="#telepath_scanner">念騒霊</a>
</p>

<h3><a name="mind_scanner">さとり</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の夜の発言を見ることができます。
結果が出るのは 2 日目以降で、相手にはサブ役職「<a href="sub_role.php#mind_read">サトラレ</a>」がつきます。
<a href="#unconscious">無意識</a>と死者の発言を見ることはできません。
自分が死んだら能力は無効になります。
人狼の遠吠えが一切見えません。
</pre>
<h4>Ver. 1.4.0 α23〜</h4>
<pre>
人狼の遠吠えが一切見えません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
新役職考案スレ (最下参照) の 4 が原型です。
相手も見られていることだけは自覚できるので
どこまで推理に活かせるのかは未知数ですが……
遠吠えの有無で相手が人狼かどうかの判断できてしまうので
Ver. 1.4.0 α23 からは常時遠吠えを見えなくしました。
</pre>

<h3><a name="evoke_scanner">イタコ</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β2〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人を<a href="sub_role.php#mind_evoke">口寄せ</a>にします。

1. 投票結果が出るのは 2 日目以降です。
2. 口寄せ先が死亡したら霊界から遺言窓を介してメッセージを受け取れます。
3. 自分では遺言欄を変更できません。
4. 自分の遺言欄に何が表示されていても遺言は残りません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
霊界オフモードの有効活用をできる役職を作ろうと思い、
こういう実装にしてみました。
</pre>

<h3><a name="whisper_scanner">囁騒霊</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
2日目夜以降、<a href="#common_group">共有者系</a>に一方的に声が届く特殊なさとり。
相手には誰の声が聞こえているのか分かりますが、仲間表示などには何も出ません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
名称は東方 Project のプリズムリバー姉妹がモチーフです。
共有者の囁きに同時に表示される形で実装しているので、
実質、片側通行の共有者相当になります。
</pre>

<h3><a name="howl_scanner">吠騒霊</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
2日目夜以降、<a href="wolf.php#wolf_group">人狼系</a>に一方的に声が届く特殊なさとり。
相手には誰の声が聞こえているのか分かりますが、仲間表示などには何も出ません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#whisper_scanner">囁騒霊</a>の対人狼バージョンです。
人狼の夜会話に同時に表示される形で実装しているので<a href="wolf.php#whisper_mad">囁き狂人</a>にも見えます。
</pre>

<h3><a name="telepath_scanner">念騒霊</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
2日目夜以降、<a href="fox.php#fox_group">妖狐系</a>に一方的に声が届く特殊なさとり。
相手には誰の声が聞こえているのか分かりますが、仲間表示などには何も出ません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#whisper_scanner">囁騒霊</a>の対妖狐バージョンです。
妖狐の<a href="fox.php#fox_talk">念話</a>に同時に表示される形で実装していますが、妖狐の発言ではないので
<a href="wolf.php#wise_wolf">賢狼</a>は感知できません。
</pre>

<h2><a name="jealousy_group">橋姫系</a></h2>
<p>
<a href="#jealousy">橋姫</a>
<a href="#poison_jealousy">毒橋姫</a>
</p>

<h3><a name="jealousy">橋姫</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α22〜]</h3>
<pre>
昼の投票時に、同一キューピッドの恋人が揃って自分に投票したら
投票した恋人をショック死させる。詳細は以下。

1. 自分が吊られたら無効
吊られない範囲で恋人の票を集める必要があります。
対恋人で人柱になっても無意味です。

2. 他のキューピッドの恋人たちに投票されても無効
複数のキューピッドに矢を打たれて繋がっている恋人に投票されても無効です。

3. 処理のタイミングはショック死処理の直前
つまり、投票結果が再投票になっても有効です。
また、本人が<a href="sub_role.php#celibacy">独身貴族</a>であっても有効です。
(結果的には相討ちになる)

4. カップルが別々の橋姫に投票しても無効
他の橋姫に対する投票は参照していません。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
対恋人役職です。
新役職考案スレ (最下参照) の 2、21、44 を参考にしています。
別れさせる処理が難しいのでこういう実装になりました。
</pre>

<h3><a name="poison_jealousy">毒橋姫</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β6〜]</h3>
<h4>[毒能力] 吊り：恋人 / 襲撃：恋人 / 薬師判定：限定的</h4>
<pre>
恋人のみに中る埋毒者で、本人の表記は「埋毒者」。
<a href="#jealousy">橋姫</a>の能力は持っていない。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#poison_group">埋毒者</a>の亜種ですが、恋人限定なので所属は橋姫系です。
</pre>

<h2><a name="doll_group">上海人形系</a></h2>
<p>
<a href="#doll_rule">基本ルール</a>
</p>
<p>
<a href="#doll">上海人形</a>
<a href="#poison_doll">鈴蘭人形</a>
<a href="#friend_doll">仏蘭西人形</a>
<a href="#doll_master">人形遣い</a>
</p>

<h3><a name="doll_rule">上海人形系の基本ルール</a></h3>
<ol>
<li>他の国で言う「奴隷」に相当します</li>
<li>勝利条件は「<a href="#doll_master">人形遣い</a>が全員死亡している＋村が勝利」です<br>自身の生死は問いません</li>
<li><a href="#doll_master">人形遣い</a>が出現しなかった場合の勝利条件は通常の村人陣営相当になります</li>
<li><a href="#doll_master">人形遣い</a>が誰か分かります</li>
</ol>

<h3><a name="doll">上海人形</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
上海人形系の基本職で、他の国で言う「奴隷」。
<a href="#doll_rule">基本ルール</a>が適用される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
奴隷という名称を避けたかったので、東方 Project のメディスンをヒントに
「<a href="#doll_master">人形遣い</a>の支配から逃れようとする人形」という構図を採用しました。
</pre>

<h3><a name="poison_doll">鈴蘭人形</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<h4>[毒能力] 吊り：上海人形系以外 (人形遣いには中る) / 襲撃：有り / 薬師判定：有り</h4>
<pre>
毒を持った人形。毒の対象は<a href="#doll_group">上海人形系</a>以外 (<a href="#doll_master">人形遣い</a>には中る)。
<a href="#doll_rule">基本ルール</a>が適用される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方 Project のメディスン・メランコリーがモデルです。
</pre>

<h3><a name="friend_doll">仏蘭西人形</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
他の人形が誰か分かる人形。<a href="#doll_rule">基本ルール</a>が適用される。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
東方 Project のアリス・マーガトロイドのスペルカード「博愛の仏蘭西人形」がモデルです。
</pre>

<h3><a name="doll_master">人形遣い</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β10〜]</h3>
<pre>
他の国で言う「貴族」。
</pre>
<ol>
<li>勝利条件は通常の村人陣営相当です</li>
<li><a href="wolf.php#wolf_group">人狼</a>に襲撃された際に人形が生存していたら、ランダムで誰か一人が身代わりに死んでくれます<br>
<a href="wolf.php#wolf_group">人狼</a>の襲撃自体は失敗扱いです</li>
<li>人形が誰か分かりません</li>
<li>狩人の<a href="#guard_limit">護衛制限</a>対象です</li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
他の国に実在する役職を式神研の闇鍋向きにアレンジしてみました。
</pre>


<h2><a name="mania_group">神話マニア系</a></h2>
<p>
<a href="#mania">神話マニア</a>
<a href="#trick_mania">奇術師</a>
<a href="#soul_mania">覚醒者</a>
<a href="#unknown_mania">鵺</a>
<a href="#dummy_mania">夢語部</a>
</p>

<h3><a name="mania">神話マニア</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職をコピーします。
入れ替わるのは2日目の朝で、神話マニア系を選んだ場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。
通常闇鍋モードでは16人以上から出現します。
</pre>
<h4>[作成者からのコメント]</h4>
<pre>
カード人狼にある役職です。元と違い、占いや狼以外の役職もコピーします。
CO するべきかどうかは、コピーした役職次第です。
</pre>

<h3><a name="trick_mania">奇術師</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β9〜]</h3>
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
<a href="#mage_group">占い師系</a>・<a href="#mind_scanner_group">さとり系</a>・<a href="fox.php#child_fox_group">子狐系</a>・<a href="lovers.php">恋人陣営</a>・<a href="chiroptera.php#fairy_group">妖精系</a>と、
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

<h3><a name="soul_mania">覚醒者</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職の上位種に変化する<a href="#mania">神話マニア</a>の亜種。
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
<li><a href="#human_group">村人系</a> → <a href="#executor">執行者</a></li>
<li><a href="#mage_group">占い師系</a> → <a href="#soul_mage">魂の占い師</a></li>
<li><a href="#necromancer_group">霊能者系</a> → <a href="#soul_necromancer">雲外鏡</a></li>
<li><a href="#priest_group">司祭系</a> → <a href="#bishop_priest">司教</a></li>
<li><a href="#guard_group">狩人系</a> → <a href="#poison_guard">騎士</a></li>
<li><a href="#common_group">共有者系</a> → <a href="#ghost_common">亡霊嬢</a></li>
<li><a href="#poison_group">埋毒者系</a> → <a href="#strong_poison">強毒者</a></li>
<li><a href="#poison_cat_group">猫又系</a> → <a href="#revive_cat">仙狸</a></li>
<li><a href="#pharmacist_group">薬師系</a> → <a href="#pharmacist">薬師</a></li>
<li><a href="#assassin_group">暗殺者系</a> → <a href="#reverse_assassin">反魂師</a></li>
<li><a href="#mind_scanner_group">さとり系</a> → <a href="#howl_scanner">吠騒霊</a></li>
<li><a href="#jealousy_group">橋姫系</a> → <a href="#poison_jealousy">毒橋姫</a></li>
<li><a href="#doll_group">上海人形系</a> → <a href="#doll_master">人形遣い</a></li>
<li><a href="wolf.php#wolf_group">人狼系</a> → <a href="wolf.php#sirius_wolf">天狼</a></li>
<li><a href="wolf.php#mad_group">狂人系</a> → <a href="wolf.php#whisper_mad">囁き狂人</a></li>
<li><a href="fox.php#fox_group">妖狐系</a> → <a href="fox.php#cursed_fox">天狐</a></li>
<li><a href="fox.php#child_fox_group">子狐系</a> → <a href="fox.php#child_fox">子狐</a></li>
<li><a href="lovers.php#cupid_group">キューピッド系</a> → <a href="lovers.php#mind_cupid">女神</a></li>
<li><a href="lovers.php#angel_group">天使系</a> → <a href="lovers.php#ark_angel">大天使</a></li>
<li><a href="quiz.php#quiz_group">出題者系</a> → <a href="quiz.php#quiz">出題者</a></li>
<li><a href="chiroptera.php#chiroptera_group">蝙蝠系</a> → <a href="chiroptera.php#boss_chiroptera">大蝙蝠</a></li>
<li><a href="chiroptera.php#fairy_group">妖精系</a> → <a href="chiroptera.php#light_fairy">光妖精</a></li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#incubate_poison">潜毒者</a>の亜種を作ろうとして色々構想を練った結果、こういう実装になりました。
能力発動のタイミングを考慮して<a href="#incubate_poison">潜毒者</a>より一日早く入れ替え処理を行っています。
</pre>

<h3><a name="unknown_mania">鵺</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 α23〜]</h3>
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
サブ役職は判定対象外(<a href="#medium">巫女</a>と同じ)なので
コピー先と勝利陣営が異なる、例外ケースとなります。

7. 鵺 → 人狼[サトラレ] (人狼陣営)
コピー先が村人陣営の<a href="#mind_scanner">さとり</a>に会話を覗かれている状態なので
コピー先からの情報入手が難しくなります。

8. 鵺 → 鵺 → 人狼 (全員人狼陣営)
コピー先が鵺だった場合は鵺以外の役職に当たるまで
コピー先を辿って判定します。

9. 鵺A → 鵺B → 鵺C → 鵺A (全員村人陣営)
コピー先を辿って自分に戻った場合は村人陣営になります。

10. 鵺 → 神話マニア → 妖狐 (妖狐陣営)
神話マニアをコピーした場合はコピー結果の陣営になります。

11. 鵺A → 神話マニア → 鵺B → 人狼
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

<h3><a name="dummy_mania">夢語部</a> (占い結果：村人 / 霊能結果：村人) [Ver. 1.4.0 β11〜]</h3>
<pre>
初日の夜に誰か一人を選んでその人の役職の基本・劣化種に変化する<a href="#mania">神話マニア</a>の亜種。
本人の表記は「<a href="#soul_mania">覚醒者</a>」で、仕様も同じ。
変化前に<a href="wolf.php#dream_eater_mad">獏</a>に襲撃されると殺される。
</pre>
<pre>
コピー元の系統 → コピー結果
</pre>
<ol>
<li><a href="#human_group">村人系</a> → <a href="#suspect">不審者</a></li>
<li><a href="#mage_group">占い師系</a> → <a href="#dummy_mage">夢見人</a></li>
<li><a href="#necromancer_group">霊能者系</a> → <a href="#dummy_necromancer">夢枕人</a></li>
<li><a href="#priest_group">司祭系</a> → <a href="#crisis_priest">預言者</a></li>
<li><a href="#guard_group">狩人系</a> → <a href="#dummy_guard">夢守人</a></li>
<li><a href="#common_group">共有者系</a> → <a href="#dummy_common">夢共有者</a></li>
<li><a href="#poison_group">埋毒者系</a> → <a href="#dummy_poison">夢毒者</a></li>
<li><a href="#poison_cat_group">猫又系</a> → <a href="#sacrifice_cat">猫神</a></li>
<li><a href="#pharmacist_group">薬師系</a> → <a href="#cure_pharmacist">河童</a></li>
<li><a href="#assassin_group">暗殺者系</a> → <a href="#eclipse_assassin">蝕暗殺者</a></li>
<li><a href="#mind_scanner_group">さとり系</a> → <a href="#mind_scanner">さとり</a></li>
<li><a href="#jealousy_group">橋姫系</a> → <a href="#jealousy">橋姫</a></li>
<li><a href="#doll_group">上海人形系</a> → <a href="#doll">上海人形</a></li>
<li><a href="wolf.php#wolf_group">人狼系</a> → <a href="wolf.php#cute_wolf">萌狼</a></li>
<li><a href="wolf.php#mad_group">狂人系</a> → <a href="wolf.php#mad">狂人</a></li>
<li><a href="fox.php#fox_group">妖狐系</a> → <a href="fox.php#cute_fox">萌狐</a></li>
<li><a href="fox.php#child_fox_group">子狐系</a> → <a href="fox.php#sex_fox">雛狐</a></li>
<li><a href="lovers.php#cupid_group">キューピッド系</a> → <a href="lovers.php#self_cupid">求愛者</a></li>
<li><a href="lovers.php#angel_group">天使系</a> → <a href="lovers.php#angel">天使</a></li>
<li><a href="quiz.php#quiz_group">出題者系</a> → <a href="quiz.php#quiz">出題者</a></li>
<li><a href="chiroptera.php#chiroptera_group">蝙蝠系</a> → <a href="chiroptera.php#dummy_chiroptera">夢求愛者</a></li>
<li><a href="chiroptera.php#fairy_group">妖精系</a> → <a href="chiroptera.php#mirror_fairy">鏡妖精</a></li>
</ol>
<h4>[作成者からのコメント]</h4>
<pre>
<a href="#soul_mania">覚醒者</a>の夢バージョンです。
最終的には自覚することができるので他の夢系と比べると対応はしやすいかもしれません。
</pre>
</body></html>
