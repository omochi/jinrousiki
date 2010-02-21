<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [人狼陣営]', 'new_role');
?>
</head>
<body>
<h1>人狼陣営</h1>
<a href="./" target="_top">←メニュー</a>
<a href="summary.php">一覧表に戻る</a><br>
<p>
<a href="#wolf_group">人狼系</a>
<a href="#mad_group">狂人系</a>
</p>

<h2><a name="wolf_group">人狼系</a></h2>
<p>
<a href="#boss_wolf">白狼</a>
<a href="#tongue_wolf">舌禍狼</a>
<a href="#wise_wolf">賢狼</a>
<a href="#poison_wolf">毒狼</a>
<a href="#resist_wolf">抗毒狼</a>
<a href="#cursed_wolf">呪狼</a>
<a href="#possessed_wolf">憑狼</a>
<a href="#cute_wolf">萌狼</a>
<a href="#scarlet_wolf">紅狼</a>
<a href="#silver_wolf">銀狼</a>
</p>

<h3><a name="boss_wolf">白狼</a> (占い結果：村人、霊能結果：白狼) [Ver. 1.4.0 α3-7〜]</h3>
<pre>
他の国でいうところの「大狼」。
<a href="human.php#mage_group">占い師</a>を欺ける人狼だが、<a href="human.php#necromancer_group">霊能者</a>には見抜かれる。

[作成者からのコメント]
占いが怖くないので LW を担うことを前提に動くことを勧めます。
占いなどを騙らずに潜伏することはもちろん、噛み投票も
なるべくしない (毒噛み対策) 方がいいと思います。
</pre>


<h3><a name="tongue_wolf">舌禍狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α13〜]</h3>
<pre>
自ら噛み投票を行った場合のみ、次の日に噛んだ人の役職が分かる。
ただし、村人を噛んだ場合は能力を失ってしまう。

[作成者からのコメント]
新役職考案スレ (最下参照) の 69 の「賢狼」が原型です。
ベーグル(真偽の付かない占い噛み) 時に動くのが一番有効でしょう。
ベーグル後はワンチャンスの狩人確認に使うと良いと思います。
また、村人の場合に能力を失いますが身代わり君を噛むのも面白いですね。
結果が出てから仲間に伝える前に吊られる可能性があるので
事前にブロックサインを決めておくと良いでしょう。
</pre>


<h3><a name="wise_wolf">賢狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α24〜]</h3>
<pre>
<a href="fox.php#fox_talk">妖狐の念話</a>が共有者の囁きに変換されて聞こえる人狼。
結果として、念話ができる妖狐が生存していることだけが分かる。
本物の共有者の囁きと混ざって表示されるので本人には区別できない。

[作成者からのコメント]
名称は他の国に実在しますが、仕様はオリジナルです。
狼サイドから妖狐の生存がわかります。
<a href="fox.php#silver_fox">銀狐</a>や<a href="fox.php#child_fox">子狐</a>など、念話できない妖狐の
生存は感知できないので注意してください。
</pre>


<h3><a name="poison_wolf">毒狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α12〜]</h3>
<pre>
いわゆる埋毒 + 人狼。
吊られた時に巻き込む対象の決定時に人狼が除かれるため
投票者ランダムの場合は不発となるケースがある。

[作成者からのコメント]
新役職考案スレ (最下参照) の 31 が原型です。
毒狼 CO した人を吊って誰も巻き込まれなかった場合は以下のケースがありえます。
1. <a href="human.php#pharmacist">薬師</a>が投票していた
2. 投票者ランダムの設定で、投票者が全員狼だった
3. 騙りだった

Ver. 1.4.0 α12 からは毒持ちを吊った時に巻き込まれる対象を設定で制限できます。
(設定は管理者に確認を取ってください)
「投票者ランダム」だった場合、この状況から推理を詰めることが可能になります。
<a href="human.php#pharmacist">薬師</a>が投票していない場合、毒狼を真と見るなら投票者に狼がいる事になります。
しかし、これが騙りの場合は・・・？
</pre>


<h3><a name="resist_wolf">抗毒狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α17〜]</h3>
<pre>
一度だけ毒に耐えられる(毒に中っても死なない)人狼です。
毒吊りの巻き込み、毒噛み両方に対応しています。
Ver. 1.4.0 α24 からは毒能力者を襲撃した場合はサーバ設定や能力失効の有無に
関わらず毒の対象が襲撃者に固定されます。

※Ver. 1.4.0 α24〜
襲撃先が毒能力者で、投票者が抗毒狼だった場合はサーバ設定に関わらず
毒の対象者が投票した抗毒狼に固定されます。
ただし、能力を失効しても固定化処理は有効です。
つまり、<a href="human.php#poison_group">埋毒者</a>を意図的に襲撃して毒を無効化したり、
能力失効後にわざと毒に中りにいく事が可能になります。

[作成者からのコメント]
新役職考案スレ (最下参照) の 25 を参考にしています。
現時点でほぼ無敵の能力を誇る<a href="human.php#poison_guard">騎士</a>への対抗策として作成しました。
安易に CO する騎士・埋毒者を葬ってやりましょう！
</pre>


<h3><a name="cursed_wolf">呪狼</a> (占い結果：人狼(呪返し)、霊能結果：呪狼) [Ver. 1.4.0 α17〜]</h3>
<pre>
占われたら占ってきた<a href="human.php#mage_group">占い師</a>を呪い殺す人狼です。
呪殺された占い師には襲撃されたときと同じ死亡メッセージが出ます。

※Ver. 1.4.0 β3〜
霊能結果を「人狼」から「呪狼」に変更しました (霊能の基本ルール対応抜け)

[作成者からのコメント]
他の国に実在する役職です。
新役職考案スレ (最下参照) の 69 を参考にしています。
<a href="human.php#soul_mage">魂の占い師</a>や<a href="fox.php#child_fox">子狐</a>も呪い殺せます。
<a href="human.php#mage_group">占い師</a>側の対策は、遺言に占い先をきちんと書いておく事です。
死体の数や状況にもよりますが、残った村人がきっと仇を討ってくれるでしょう。
</pre>


<h3><a name="possessed_wolf">憑狼</a> (占い結果：人狼、霊能結果：憑狼) [Ver. 1.4.0 α24〜]</h3>
<pre>
噛んだ人を乗っ取る人狼。
乗っ取るのはアイコンと恋人を除くサブ役職全て。
身代わり君、<a href="human.php#revive_priest">天人</a>、妖狐は乗っ取れません。

1. 基本システム
噛みが成功した場合は、噛まれた人が霊界に行きますが、
見かけ上は噛んだ憑狼が無残な死体で発見されます。

例1-1) A[憑狼] → B[村人]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])

実際に死ぬのは B で、B の中の人は霊界へ行く。
下界の人間には A の発言は B が発言したように見える。
狼の仲間リストから A が消えて B が増える (つまり、憑依したことが分かる)。

夜の発言も含めて全て乗っ取った人のものになるので
例えば共有を乗っ取った場合は狼仲間＋共有仲間と
会話できるようになり、他の人からはひそひそ声に見えます。
つまり、憑狼の遠吠えが消える事になります。

発言は乗っ取れてもメイン役職の能力は乗っ取らない仕様です。
占い師を乗っ取った場合は占い騙りをしないと不自然になります。
また、共有と会話できますが共有仲間が誰か分かるわけではありません。

憑依状態の憑狼が他の村人を噛んだ場合は次々と乗り移ります。
一度憑依を始めた憑狼は現在自分が憑依している対象が常に表示されます。
様々な要因で自分の体に戻されるケースがありますが、その場合は
「あなたはあなたに憑依しています」と表記されます。

2. 遺言について
遺言は憑狼が偽装したものと見かけ上死んだ村人の両方が出ます。
また、憑狼が憑依に成功するたびに憑狼の現在の遺言が空になります。

例2-1) A[憑狼] → B[村人]
A が書いた遺言が A の遺言として表示される。

例2-2) B[村人](A[憑狼]) → C[村人]
B になりすました A の遺言と B 本人が死んだ時点で書いていた遺言の
両方が表示される。


3. 投票結果の基本仕様について
結果は「投票をした時点の中の人」で判定されます。

例3-1) A[憑狼] → B[村人] ← C[占い師]
占い結果：B は「村人」でした。
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])

例3-2) C[占い師] → B[村人](A[憑狼])
占い結果：B は「人狼」でした。

例3-3) C[<a href="human.php#psycho_mage">精神鑑定士</a>] → B[狂人](A[憑狼])
鑑定結果：B は「正常」でした。

例3-4) C[<a href="human.php#pharmacist">薬師</a>] → B[埋毒者](A[憑狼])
鑑定結果：B は「毒を持っていません」。

例3-5) C[<a href="#silver_wolf">銀狼</a>] → B[村人](A[憑狼])
死体：無し


4. 霊能結果について
結果は吊られた中の人で判定されます。

例4-1) 吊り：B[村人](A[憑狼])
霊能結果：B は「憑狼」でした。


5. 毒噛みについて
憑狼が毒能力者を襲撃して毒に中ったら憑依がキャンセルされます。

例5-1) A[憑狼] → B[埋毒者]、毒死：A[憑狼]
死体：A が無残な死体で発見されました (死因：「毒に中った」)
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)

例5-2) A[憑狼] → B[埋毒者]、毒死：C[人狼]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
死体：C が無残な死体で発見されました (死因：「毒に中った」)
憑依：B[埋毒者](A[憑狼])


6. 対占い系能力者
占い師と<a href="human.php#soul_mage">魂の占い師</a>が憑狼を占った場合は、憑依がキャンセルされます。
<a href="human.php#dummy_mage">夢見人</a>や<a href="fox.php#child_fox">子狐</a>が占ってもキャンセルされません。
<a href="human.php#psycho_mage">精神鑑定士</a>や<a href="human.php#sex_mage">ひよこ鑑定士</a>など、「村人 / 人狼」以外を判定するタイプの
占い系能力者が占ってもキャンセルされません。

例6-1) C[占い師] → A[憑狼] → B[村人]
占い結果：A は「人狼」でした。
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。

例6-2) C[<a href="human.php#sex_mage">ひよこ鑑定士</a>] → B[蝙蝠](A[憑狼]) → D[村人]
鑑定結果：B は「男性 / 女性」(A の性別) でした。
死体：B が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：D[村人](A[憑狼])


7. 対<a href="human.php#voodoo_killer">陰陽師</a>
陰陽師が憑狼を占った場合は、呪殺します。
陰陽師が憑狼の憑依先を占った場合は、憑依がキャンセルされます。

例7-1) C[陰陽師] → A[憑狼] → B[村人]
占い結果： A の「解呪成功」
死体：A が無残な死体で発見されました (死因：「呪詛に呪われた」)
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)

例7-2) A[憑狼] → B[村人] ← C[陰陽師]
占い結果： B の「解呪成功」
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。


8. 対<a href="human.php#anti_voodoo">厄神</a>
厄神が憑狼か憑狼の憑依先を護衛した場合は、憑依がキャンセルされます。
憑依中の憑狼を護衛するか、憑狼に襲撃されると憑依状態を解くことができます。
憑依を解かれた憑狼は見かけ上は「蘇生した」ように見えます。

例8-1) A[憑狼] → B[村人] ← C[厄神]
護衛結果： B の「厄払い成功」
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。

例8-2) C[厄神] → A[憑狼] → B[村人]
護衛結果： A の「厄払い成功」
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。

例8-3) C[厄神] → B[村人](A[憑狼]) → D[村人]
護衛結果： B の「厄払い成功」
蘇生：A は生き返りました
死体：B が無残な死体で発見されました (死因：「憑依から開放された」)
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)

例8-4) A[憑狼] → B[厄神]
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。

例8-5) B[村人](A[憑狼]) → C[厄神]
蘇生：A は生き返りました
死体：B が無残な死体で発見されました (死因：「憑依から開放された」)
死体：C が無残な死体で発見されました (死因：「人狼に襲撃された」)


9. 対<a href="human.php#reporter">ブン屋</a>
ブン屋が憑狼の憑依先を護衛すると、「生存者は死者に襲撃された」という
尾行結果が表示されることになるので本人視点で憑狼の位置が確定します。

例9-1) A[憑狼] → B[村人] ← C[ブン屋]
尾行結果：B は A に襲撃されました。
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])


10. 対<a href="human.php#assassin">暗殺者</a>
直接憑狼を狙うことで憑依中でも殺すことができます。
また、自分と暗殺先が同時に生き残っていることはありえないので、
本人視点で憑狼の位置が確定します。

例10-1) C[暗殺者] → B[村人](A[憑狼]) → D[村人]
死体：B が無残な死体で発見されました (死因：「暗殺された」) (実際に死ぬのは A)
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)

例10-2) A[憑狼] → B[村人] ← C[暗殺者]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])


11. 対<a href="human.php#poison_cat">猫又</a>
憑依中に「見かけ上死んでいる」憑狼本体を蘇生されると元の体に戻されます。
実際に生き返るのは憑依先です。

猫又の蘇生処理のタイミングの仕様上、同じ夜に殺された人が誤爆蘇生する可能性があります。
暗殺などで憑依中の憑狼が死亡 + 憑依先を誤爆蘇生の場合は憑依されていた本人が生き返ります。

例11-1) C[猫又] → A[憑狼](見かけ上の死体)、B[村人](A[憑狼]) → D[村人]
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：A は生き返りました (実際に生き返るのは B)

例11-2) C[猫又] → A[憑狼](見かけ上の死体)、B[村人](A[憑狼]) → D[村人]
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：D は生き返りました (誤爆蘇生：実際に生き返るのも D)
A は B から D への憑依がキャンセルされて B に憑依したまま。

例11-3) C[猫又] → A[憑狼](見かけ上の死体)、E[暗殺者] → B[村人](A[憑狼]) → D[村人]
死体：B が無残な死体で発見されました (死因：「暗殺された」) (実際に死ぬのは A)
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：A は生き返りました (実際に生き返るのも A)

例11-4) C[猫又] → A[憑狼](見かけ上の死体)、E[暗殺者] → B[村人](A[憑狼]) → D[村人]
死体：B が無残な死体で発見されました (死因：「暗殺された」) (実際に死ぬのは A)
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)
蘇生：B は生き返りました (誤爆蘇生：実際に生き返るのも B)


12. 対<a href="human.php#revive_priest">天人</a>
天人は憑依対象外なので、生存している天人は憑狼ではない事が保証されます。
また、霊界視点からは憑依者がはっきり分かるので蘇生した天人の情報は重要です。

例12-1) B[村人](A[憑狼]) → C[天人]
死体：C が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は B に憑依したまま。


13. 対<a href="human.php#evoke_scanner">イタコ</a>
基本システムにあるとおり、メイン役職の能力は乗っ取らないので
イタコに憑依しても口寄せ先からのメッセージは届きませんが、
口寄せ先を憑依すると、霊界からイタコにメッセージが届くので
憑依していることがばれます。

例13-1) A[憑狼] → B[イタコ] → C[村人][口寄せ] (死亡中)
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[イタコ](A[憑狼])
C が遺言メッセージを送っても B に憑依している A の遺言窓は変わらない。

例13-2) A[憑狼] → B[村人][口寄せ] ← C[イタコ]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])
B が遺言メッセージを送ると C の遺言窓が変更される。


14. 恋人について
恋人だけは乗っ取りません。
憑狼自身が恋人だった場合は、恋人からは憑依していることが分かります。
もし、後追いした恋人が遺言で「憑依先と恋人である」 と書いていた場合は
状況と矛盾することになります。

例14-1) A[憑狼][恋人] → B[村人]、C[村人][恋人]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼][恋人])
C の恋人の相手が A から B に変わる (C 視点から憑依したことが分かる)

例14-2) A[憑狼] → B[村人][恋人]、C[村人][恋人]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
死体：C が恋人の後を追い自殺しました
憑依：B[村人][恋人](A[憑狼])
後追いした「確定恋人」の恋人が生存している状態になる。

例14-3) A[憑狼] → B[村人][恋人]、D[暗殺者] → C[<a href="lovers.php#self_cupid">求愛者</a>][恋人]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
死体：C が無残な死体で発見されました (死因：「暗殺された」)
憑依：B[村人][恋人](A[憑狼])
C の恋人が生存している状態になるが、C が<a href="chiroptera#dummy_chiroptera">夢求愛者</a>だった場合は
無関係の村人が恋人に憑依した憑狼扱いされる可能性がある。


15. サブ役職について
恋人以外のサブは全て乗っ取ります。
憑依している憑狼には現在自分に適用されているサブが表示されます。

例15-1) A[憑狼][小心者] → B[村人][天邪鬼]
B に憑依している A は天邪鬼になります。
(小心者の表示が消えて天邪鬼が表示されます)

例15-2) C[さとり] → A[憑狼] → B[村人] ← D[さとり]
C は 「死亡した」 A の発言が見えなくなります。
D は B になりすましている A の発言が見えます。

[作成者からのコメント]
長期で実在する特殊狼です。
単純に「中の人が入れ替わる狼」というだけでも十分にややこしいかと思いますが、
オリジナルの国に存在しない恋人や猫又の存在が複雑さに拍車をかけています。
これから短期向けに色々と調整しながら仕上げる予定です。
</pre>


<h3><a name="cute_wolf">萌狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α14〜]</h3>
<pre>
低確率で発言が遠吠えに入れ替わってしまう。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗がモデルです。
</pre>


<h3><a name="scarlet_wolf">紅狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α24〜]</h3>
<pre>
<a href="fox.php#child_fox">妖狐陣営</a>から<a href="fox.php#child_fox">子狐</a>に見える人狼。
本物の子狐と混ざって表示されるため、妖狐側からは区別できない。

[作成者からのコメント]
<a href="fox.php#scarlet_fox">紅狐</a>の人狼バージョンです。
<a href="fox.php#child_fox">子狐</a>は念話できないので夜の会話で直接ばれることはありません。
占いを騙ることで妖狐側から子狐に見えるよう振舞う手もありますが
紅狼が妖狐を把握してるわけではないので「味方」に黒を出す可能性も……
</pre>


<h3><a name="silver_wolf">銀狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α21〜]</h3>
<pre>
仲間が分からない人狼。
(他の人狼・<a href="#fanatic_mad">狂信者</a>・<a href="#whisper_mad">囁き狂人</a>からも仲間であると分からない)
人狼同士の会話もできず、発言は他の人からは遠吠えに見える。

人狼/銀狼の夜の投票は共通。
(銀狼が先に投票したら他の人狼は投票できない)
互いに認識できないので、味方を襲撃する可能性がある。

狼が狼を襲撃した場合は失敗扱いとなる。
妖狐と違い、襲撃された方にも何も表示されない。

※Ver. 1.4.0 α23〜
独り言が他の人から遠吠えに見える。

1. 人狼視点の遠吠えは銀狼確定
2. 銀狼視点の遠吠えは自分以外の狼(種類は不明)
3. 村人視点の遠吠えは銀狼も含めた狼(種類は不明)

[作成者からのコメント]
他の国に実在する役職です。
仲間と連携が取れないので動き方が難しくなると思います。
仲間に襲撃されて狐扱いされて吊られる可能性も……
</pre>


<h2><a name="mad_group">狂人系</a></h2>
<p>
<a href="#mad_rule">基本ルール</a>
</p>
<p>
<a href="#fanatic_mad">狂信者</a>
<a href="#whisper_mad">囁き狂人</a>
<a href="#jammer_mad">月兎</a>
<a href="#voodoo_mad">呪術師</a>
<a href="#corpse_courier_mad">火車</a>
<a href="#dream_eater_mad">獏</a>
<a href="#trap_mad">罠師</a>
</p>

<h2><a name="mad_rule">基本ルール</a></h2>
<pre>
○基本ルール
騙りにリスクを与えるために、特殊能力を持った狂人は
<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される仕様となっています。
</pre>

<h3><a name="fanatic_mad">狂信者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]</h3>
<pre>
人狼が誰か分かる上位狂人 (人狼からは狂信者は分からない)。
狼にとって完璧な騙りが出来るはず。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、狂信者視点、狂信者を名乗る人は偽者です。
狂信者が出現した場合は出現人数と同じだけ狂人が減ります。

[作成者からのコメント]
他の国に実在する役職です。
狼サイドの新兵器です。作った当初は<a href="human.php#soul_mage">魂の占い師</a>や<a href="human.php#poison_guard">騎士</a>と出現率のせいもあって
活躍できなかったようですが、本来はかなり強いはず。
</pre>


<h3><a name="whisper_mad">囁き狂人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]</h3>
<pre>
人狼の夜の相談に参加できる上位狂人です。
人狼と違い、発言が遠吠えに変換されません。

[作成者からのコメント]
通称「C国狂人」、最強クラスの狂人です。「ささやき きょうじん」と読みます。
相談できるので完璧な連携が取れます。
</pre>


<h3><a name="jammer_mad">月兎 (邪魔狂人)</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α19〜]</h3>
<pre>
夜に村人一人を選び、その人の占い行動を妨害する特殊な狂人。
具体的な仕様は以下。

1. 妨害可能な占い行動は<a href="human.php#dummy_mage">夢見人</a>を除く<a href="human.php#mage_group">占い師系</a>と<a href="fox.php#child_fox">子狐</a>です
2. 妨害された占い師は「〜さんの占いに失敗しました」と出ます
3. 妨害が成功したかどうかは本人には分かりません
4. 呪われている役職を選んだ場合は呪返しを受けます
5. <a href="human.php#guard_hunt">狩人系に護衛</a>されると殺されます
6. <a href="human.php#voodoo_killer">陰陽師</a>は対象外です

※Ver. 1.4.0 α21〜
名称を邪魔狂人から月兎に変更しました。

[作成者からのコメント]
実在する役職ですね。狂人系の中でも上位に属します。
うかつに CO する事ができなくなるので占い師は非常につらくなりますね。
</pre>


<h3><a name="voodoo_mad">呪術師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α20〜]</h3>
<pre>
夜に村人一人を選び、その人に呪いをかける特殊な狂人。
具体的な仕様は以下。

1. 呪われた人を占った<a href="human.php#mage_group">占い師</a>は呪返しを受けます
2. 呪われている役職を選んだ場合は本人が呪返しを受けます
3. 呪いをかけた人が他の人にも呪いをかけられていた場合は本人が呪返しを受けます
4. <a href="human.php#guard_hunt">狩人系</a>に護衛されると殺されます

[作成者からのコメント]
対<a href="human.php#mage_group">占い師</a>系専門の<a href="#trap_mad">罠師</a>的存在です。
新役職考案スレ (最下参照) の 13 が原型です。
占い師の占い先を先読みして呪いをかけておくことで呪返しを狙うのが
基本的な立ち回りになると思います。
</pre>


<h3><a name="corpse_courier_mad">火車</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
自分が投票して吊った人の霊能結果を隠蔽できる特殊な狂人。
投票先を吊ることさえ出来れば、投票した日にショック死しようと
その夜に何らかの形で死のうと効果が発動する。

霊能結果が隠蔽されると、霊能者と<a href="human.php#soul_necromancer">雲外鏡</a>の霊能結果が
「〜さんの死体が盗まれた」という趣旨のメッセージになる。
<a href="human.php#dummy_necromancer">夢枕人</a>は火車の影響は受けない。

<a href="human.php#guard_hunt">狩人系に護衛</a>されると殺される。

[作成者からのコメント]
<a href="#jammer_mad">月兎</a> (邪魔狂人) の霊能バージョンです。「かしゃ」と読みます。
新役職考案スレ (最下参照) の 48 が原型です。
火車の能力が発動しているのに霊能結果を出す人は
夢枕人か騙りになります。
</pre>


<h3><a name="dream_eater_mad">獏</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]</h3>
<pre>
夜に投票した夢系能力者(<a href="human.php#dummy_mage">夢見人</a>、<a href="human.php#dummy_necromancer">夢枕人</a>、<a href="human.php#dummy_common">夢共有者</a>、<a href="human.php#dummy_poison">夢毒者</a>、<a href="chiroptera.php#dummy_chiroptera">夢求愛者</a>) を
殺すことができる狂人。<a href="human.php#guard_hunt">狩人に護衛</a>されると殺される。

何らかの形で<a href="human.php#dummy_guard">夢守人</a>に接触した場合は殺される。

夢守人に殺される条件は以下
1. 襲撃先が夢守人だった
2. 夢守人に自分が護衛された
3. 襲撃先が夢守人に護衛されていた

※Ver. 1.4.0 α23〜
初日の襲撃はできません (暗殺者系と挙動を揃えました)。

[作成者からのコメント]
対夢系能力者専門の<a href="human.php#assassin">暗殺者</a>という位置づけです。「ばく」と読みます。
夢系は村陣営なので獏は狂人相当になります。
</pre>


<h3><a name="trap_mad">罠師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]</h3>
<pre>
一度だけ夜に村人一人に罠を仕掛けることができる特殊な狂人。
罠を仕掛けられた人の元に訪れた<a href="#wolf_group">人狼系</a>・<a href="human.php#guard_group">狩人系</a>・<a href="human.php#assassin_group">暗殺者</a>は死亡する。
具体的な仕様は以下。

1. 人狼に襲撃されたら無効になります
2. 罠を仕掛けに行った先に他の罠師が罠を仕掛けていた場合は死亡します
3. 自分にも仕掛けることができます
4. 自分に仕掛けた場合は人狼に襲撃されても有効です (人狼が死にます)
5. 自分に仕掛けた場合は他の罠師が罠をかけていても本人は死にません
   (仕掛けに来た他の罠師は死にます)
6. 狩人系に護衛されると殺されます
7. 自分に仕掛けて狩人に護衛された場合は相打ちになります
8. 暗殺者が罠にかかった場合、暗殺は無効になります

[作成者からのコメント]
狼陣営に対暗殺者を何か……と考案してこういう形に落ち着きました。
一行動で多くの能力者を葬れる可能性を秘めています。
人狼の襲撃先を外しつつ狩人の護衛先や暗殺者の襲撃先を読み切って
ピンポイントで罠を仕掛けないといい仕事にならないので活躍するのは
非常に難しいと思いますが、当たればきっと最高の気分になれるはず。
</pre>
</body></html>
