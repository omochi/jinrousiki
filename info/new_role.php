<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>汝は人狼なりや？<?php echo $server_comment; ?></title>
</head>
<body>
<pre>
◆新役職について [Ver. 1.4.0 alpha14]
・調整中なのでバージョンアップするたびに変わる可能性があります。

◎村人陣営
○魂の占い師 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha3-7〜]
占った人の役職が分かる占い師(妖狐を占えば呪殺する)。

※Ver. 1.4.0 alpha8〜
通常闇鍋モードでは16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、魂の占い師視点、魂の占い師を名乗る人は偽者です。
魂の占い師が出現した場合は出現人数と同じだけ占い師が減ります。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
白狼だろうが子狐だろうが分かってしまうので村側最強クラスですが、その分狙われやすいでしょう。

[作成者からのアドバイス]
始めは普通の占い師として振舞うのも有効と思われる。
また、魂の占い師をCOしていても村側役職 (狩人や霊能者) を素直に言うと
人狼に狙われるので適度に結果を騙る必要があるかもしれない (狩人を騎士と騙るなど)。


○巫女 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha3-7〜]
突然死した人の所属陣営が分かる特殊な霊能者。
闇鍋モードで登場する「ショック死」する人たちの情報を取るのが主な仕事だが
普通の霊能と違うので注意。

所属陣営とは、勝敗が決まったときの陣営なので
人狼系と狂人系は「人狼」
妖狐・子狐は「妖狐」
キューピッドは「恋人」
出題者は「出題者」
それ以外は「村人」と出る

また、メイン役職のみが判定の対象 (サブ役職は分からない)
つまり、恋人はサブ役職なので「恋人」と判定されるのはキューピッドのみ。

※Ver. 1.4.0 alpha8〜
闇鍋村ではキューピッドが出現している場合は確実に出現します。
(ただし、巫女が出現してもキューピッドが出現しているとは限りません)

※Ver. 1.4.0 alpha9〜
恋人後追いにも対応。(後追いした恋人のみ、元の所属陣営が分かる)

[作成者からのコメント]
式神研のオリジナル役職です。
闇鍋モードで大量の突然死が出ることになったので作ってみましたが
霊能者より地味な存在ですね。騙るのも容易なのでなかなか報われないかもしれません。


○騎士 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha3-7〜]
いわゆる毒＋狩人ですが、吊られた場合は毒は発動しません。

※Ver. 1.4.0 alpha8〜
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、騎士視点、騎士を名乗る人は偽者です。
しかし、埋毒者や狩人が騎士を騙る可能性もあるので敵対陣営とは限りません。
騎士が出現した場合は出現人数と同じだけ狩人と埋毒者が減ります。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
技術的に簡単だったので軽い気持ちで作ってみましたがとんでもなく強いようです。
alpha8 以降は出現率を大幅に落としたのでこれでバランスが取れるかな？


○ブン屋 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha14〜]
夜に投票(尾行)した人が噛まれた場合に、噛んだ狼が分かる特殊な狩人です。
また、以下のような特徴があります。
1. 人狼や妖狐を尾行したら殺されます (生きている人には普通に襲撃されたメッセージが出ます)
2. 遺言を残せません
3. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は噛まれます)
4. 騎士はブン屋でも通常通り護衛可能です
5. 尾行対象者が狩人に護衛されていた場合は何も出ません
6. 尾行した妖狐が噛まれた場合は尾行成功扱いとなります
   (尾行成功メッセージ＆対象が死んでいない＝狼が狐を噛んだ)

通常闇鍋モードの出現法則は調整中です。

[作成者からのコメント]
新役職考案スレ (最下参照) の 5 が原型です。
活躍するのは非常に難しいですが、成功したときには大きなリターンがあります。
狐噛みの現場をスクープするブン屋を是非見てみたいものです。
○不審者 (占い結果：人狼、霊能結果：村人) [Ver. 1.4.0 alpha9〜]
不審なあまり、占い師に人狼と判定されてしまう村人。
本人には一切自覚がない (村人と表示されている) ので偽黒を貰った様にしか感じない。
ライン推理を狂わせるとんでもない存在。

[作成者からのコメント]
村人陣営ですが人狼陣営の切り札的存在ですね。
立ち回りの上手い人がこれを引くと大変なことになりそうです。
ただし、人狼サイドにも不審者である事は分からないので
占い師の真贋が読みづらくなります。


○神話マニア (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha11〜]
初日の夜に誰か一人を選んでその人の役職をコピーします。
神話マニアを選んでしまった場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。
通常闇鍋モードでは16人以上から出現します。

[作成者からのコメント]
カード人狼にある役職です。元と違い、占いや狼以外の役職もコピーします。
COするべきかどうかは、コピーした役職次第です。


○薬師 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha12〜]
毒持ちを吊ったときに、薬師が投票していたら解毒(毒が発動しない)します。

[作成者からのコメント]
新役職考案スレ (最下参照) の 24 が原型です。
毒狼の対抗役職です。
埋毒者に対しても効果を発揮します。


○無意識 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha13〜]
他の国で言うと「自覚のない夢遊病者」。
本人には「村人」と表示されているが、夜になると無意識に歩きまわるため
人狼に無意識であることが分かってしまう。

[作成者からのコメント]
不審者同様、村人陣営ですが人狼陣営に有利な存在です。
人狼サイドから見ると無能力であることが確定の村人なので噛まれにくいです。
確定白で長期間放置されると白狼扱いされるかも。


○夢見人 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha14〜]
本人には「占い師」と表示されており、占い行動もできるが結果はランダム。
自覚がない分、狂人の占い騙りよりたちが悪い。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 17 が原型です。
占い結果はでたらめですが所属は村陣営です。
魂の占い師に鑑定されたり、自身の占い結果が破綻したときに
どんなショックを受けるのか……


◎人狼陣営
○白狼 (占い結果：村人、霊能結果：白狼) [Ver. 1.4.0 alpha3-7〜]
他の国でいうところの「大狼」。
占い師を欺ける人狼だが、霊能者には見抜かれる。

[作成者からのコメント]
占われるように上手く動きつつ潜伏すると強い。
村サイドとしては、確定白すら信用できなくなるのがつらい。


○毒狼 (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 alpha12〜]
いわゆる埋毒+人狼。
吊られた時に巻き込む対象の決定法則は埋毒者と同じだが、狼が選ばれた場合は不発となる。

[作成者からのコメント]
新役職考案スレ (最下参照) の 31 が原型です。
毒狼CO した人を吊って誰も巻き込まれなかった場合は以下のケースがありえます。
1. 薬師が投票していた
2. 狼が選ばれた
3. 偽(騙り)だった

Ver. 1.4.0 alpha12 からは毒持ちを吊った時に巻き込まれる対象を設定で制限できます。
(設定は管理者に確認を取ってください)
「投票者ランダム」だった場合、この状況から推理を詰めることが可能になります。
薬師が投票していない場合、毒狼を真と見るなら投票者に狼がいる事になります。
しかし、これが騙りの場合は・・・？


○舌禍狼 (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 alpha13〜]
自ら噛み投票を行った場合のみ、次の日に噛んだ人の役職が分かる。
ただし、村人を噛んだ場合は能力を失ってしまう。

[作成者からのコメント]
新役職考案スレ (最下参照) の 69 の「賢狼」が原型です。
ベーグル(真偽の付かない占い噛み) 時に動くのが一番有効でしょう。
ベーグル後はワンチャンスの狩人確認に使うと良いと思います。
また、村人の場合に能力を失いますが身代わり君を噛むのも面白いですね。
結果が出てから仲間に伝える前に吊られる可能性があるので
事前にブロックサインを決めておくと良いでしょう。


○萌狼 (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 alpha14〜]
低確率 (現在は 1%) で発言が遠吠えに入れ替わってしまう。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗がモデルです。


○狂信者 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha3-7〜]
人狼が誰か分かる狂人 (人狼からは狂信者は分からない)。
狼にとって完璧な騙りが出来るはずです！

※Ver. 1.4.0 alpha8〜
通常闇鍋モードでは16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、狂信者視点、狂信者を名乗る人は偽者です。
狂信者が出現した場合は出現人数と同じだけ狂人が減ります。

[作成者からのコメント]
他の国に実在する役職です。
狼サイドの新兵器です。作った当初は魂の占い師や騎士と出現率のせいもあって
活躍できなかったようですが、本来はかなり強いはず。



◎妖狐陣営
○子狐 (占い結果：村人、霊能結果：子狐) [Ver. 1.4.0 alpha3-7〜]
呪殺されない代わりに人狼に襲われると食べられてしまう狐です。
妖狐は妖狐の仲間が、子狐は妖狐・子狐全て分かります
妖狐は子狐が誰か分かりません
妖狐同士は夜会話(念話)できます(他人からはいっさい見えません)が、
子狐は念話を見ることも参加することも出来ません。

※Ver. 1.4.0 alpha8〜
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、子狐視点、子狐を名乗る人は偽者です。
子狐が出現した場合は出現人数と同じだけ妖狐が減ります。

[作成者からのコメント]
他の国に実在する役職で、未完成です。
狐陣営自体の出現数が少ないのでかなりのレア役職になりそうな予感。



◎出題者陣営
○出題者 (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 alpha2〜]
クイズ村の GM です。闇鍋村にも低確率で出現します。
勝利条件は「GM 以外が全滅していること」。
ルールの特殊なクイズ村以外ではまず勝ち目はありません。
引いたら諦めてください。

[作成者からのコメント]
クイズ村以外では恋人になったほうがまだましという涙目すぎる存在ですが
闇鍋なので全役職を出します。一回くらいは奇跡的な勝利を見てみたいですね。


--ここからサブ役職--
※Ver. 1.4.0 alpha8 現在、恋人以外のサブ役職は重なりません。
　(大声と小心者を同時にＣＯする人は最低でもどちらかが嘘です)

◎発言変化
○大声 [Ver. 1.4.0 alpha3-7〜]
発言が常に大声になります。

○不器用 [Ver. 1.4.0 alpha3-7〜]
発言の大きさを変えられません。

○小声 [Ver. 1.4.0 alpha3-7〜]
発言が常に小声になります。

[作成者からのコメント]
声の大きさも狼を推理するヒントになります。
ただのネタと思うこと無かれ。


○臆病者 [Ver. 1.4.0 alpha14〜]
声の大きさがランダムに変わります。

[作成者からのコメント]
固定があるならランダムもありだろうと思って作ってみました。
唐突に大声になるのは固定より鬱陶しいかも。


◎発言封印
○筆不精 [Ver. 1.4.0 alpha9〜]
遺言を残せません。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。
「遺言残せばいいや」と思って潜伏する役職にプレッシャーがかかります。
また、安直な遺言騙りもできなくなります。
昼の発言がより盛り上がるといいな、と思って作ってみました。


○目隠し [Ver. 1.4.0 alpha14〜]
発言者の名前が見えません (空白に見えます)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 66 の「宵闇」の布石です。


○耳栓 [Ver. 1.4.0 alpha14〜]
小声が聞こえません (空白発言に見えます)。

[作成者からのコメント]
ニコ生専用鯖のPLさん考案のネタ役職です。
聞こえないなら取れ？ネタにマジレスしてはいけません。


○無口 [Ver. 1.4.0 alpha14〜]
発言の文字数に制限がかかります (制限を越えるとそれ以降が「……」になります)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 51 が原型です。
よほど長い名前の人でもない限り、最低限の占い師のCO等には
影響が出ない程度にしてあります。


○狼少年 [Ver. 1.4.0 alpha11〜]
発言時に一部のキーワードが入れ替えられてしまいます。
(例えば、人⇔狼、白⇔黒、○⇔●です)

※Ver. 1.4.0 alpha14〜
変換対象キーワードが増えました (何が変わるかは自ら試してください)。
時々変換されないことがあります (たまには真実を語るのです)。

[作成者からのコメント]
流石鯖の管理人さんとの会話から生まれた役職です。
占い師がそれと知らずにCOすると大変なことになりそうです。
回避するのは簡単ですがそれを意識しないといけないだけでも
結構な負担ではないでしょうか？


○光学迷彩 [Ver. 1.4.0 alpha14〜]
発言の一部が空白に入れ替えられてしまいます。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんのアイディアが原型です。
変換される確率は設定で変更できます。


○紳士 [Ver. 1.4.0 alpha14〜]
時々発言が「紳士」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

○淑女 [Ver. 1.4.0 alpha14〜]
時々発言が「淑女」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

[作成者からのコメント]
紳士はウミガメ人狼のとあるプレイヤーさんの RP が原型です。
淑女は新役職考案スレ (最下参照) の 135 さんのリクエストに応えました。
発言内容が完全に入れ替わるので狼少年より酷いです。
どんな言葉に入れ替わるのかは管理人さんの気紛れ次第。


◎投票数変化
○反逆者 [Ver. 1.4.0 alpha14〜]
権力者と同じ人に投票した場合に自分と権力者の投票数が 0 になります。
それ以外のケースなら通常通り(1票)です。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗をヒントに
対権力者を作成してみました。


○気分屋 [Ver. 1.4.0 alpha14〜]
投票数が 0-2 の範囲でランダムになります(毎回変わります)。
(投票数の範囲や確率は調整される可能性があります)

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。


○傍観者 [Ver. 1.4.0 alpha9〜]
投票数が 0 になります(投票行為自体は必要です)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。


◎得票数変化
・投票ショック死判定には影響しません (ショック死判定は投票「人数」で行なわれます)。
・得票数が減る場合でもマイナスにはなりません。
例：得票が 1 で -2 された場合 → 得票数は 0 と計算される

○雑草魂 [Ver. 1.4.0 alpha14〜]
2日目の得票数が +2 される代わりに、3日目以降は -2 されます。

○一発屋 [Ver. 1.4.0 alpha14〜]
2日目の得票数が -2 される代わりに、3日目以降は +2 されます。

[作成者からのコメント]
雑草魂はウミガメ人狼のとあるプレイヤーさんがモデルです。


○人気者 [Ver. 1.4.0 alpha14〜]
得票数が -1 されます。

○不人気 [Ver. 1.4.0 alpha14〜]
得票数が +1 されます。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 64 が原型です。
得票数が変化するタイプは権力者同様、終盤になると大きな影響を与えます。
「投票した票数を公表する」がオフになっていると誰が何を持っているのか
全然分からなくなるので一体どうなることやら。


◎処刑者候補変化
この系統が複数いる場合の判定順は以下です。
決定者の投票先＞不運＞幸運が逃れる＞疫病神の投票先が逃れる

○疫病神 [Ver. 1.4.0 alpha9〜]
自分が最多得票者に投票していて、処刑者候補が複数いた場合に
その人が吊り候補から除外される。

[作成者からのコメント]
いわゆる逆決定者です (決定者同様、本人には分かりません)。
新役職考案スレ (最下参照) の 8 が原型です。


○幸運 [Ver. 1.4.0 alpha14〜]
自分が最多得票者で処刑者候補が複数いた場合は吊り候補から除外される。
(本人には分からない)

○不運 [Ver. 1.4.0 alpha14〜]
自分が最多得票者で処刑者候補が複数いた場合は優先的に吊られる。
(本人には分からない)

[作成者からのコメント]
本人に付随する決定者/逆決定者です (本人には分かりません)。
ウミガメ人狼のPLさんから原案を頂きました。


◎投票系ショック死
○小心者 [Ver. 1.4.0 alpha3-7〜]
昼の投票時に一票でも貰うとショック死します。

○ウサギ [Ver. 1.4.0 alpha3-7〜]
昼の投票時に一票も貰えないとショック死します。

○天邪鬼 [Ver. 1.4.0 alpha3-7〜]
昼の投票時に他の人と投票先が被るとショック死します。

[作成者からのコメント]
原案はウミガメ人狼のPLさん達に提供してもらったものです。
ウサギにはモデルがいます。
ウサギ＆天邪鬼がいいコンビになりつつありますが
お互いが敵対陣営の可能性もあるのが面白いですね。


◆作って欲しい役職などがあったらこちらのスレへどうぞ
【ネタ歓迎】あったらいいな、こんな役職【ガチ大歓迎】
http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50

参考スレッド
新役職提案スレッド＠やる夫
http://jbbs.livedoor.jp/bbs/read.cgi/game/48159/1243197597/


◎ 採用予定・作成中役職リスト
○ 採用予定 (レス 99までの状況)
・橋姫 (レス 2)
挙動は案募集中
改善案 (レス 21、44)

・さとり、さとられ (レス4)
実装難易度高め

・暗殺者系 (レス 8)
挙動は案募集中

・泥棒 (レス 13)
挙動は案募集中

・河童 (レス 17)
挙動は案募集中

・ミシャグジさま (レス 19)
改善 (レス 33)

・土蜘蛛 (レス 33)
挙動は案募集中
改善 (レス 43)

・厄神 (レス 43)
バッドステータスシステムを導入した際に実装する予定

・火車 (レス48)
挙動は未定

・司祭 (レス 72)
挙動は案募集中

・マグロ (レス 80)
「短気」という名前で、決定者の能力を付加して採用予定
(自覚がある決定者だが、再投票になったらショック死)

・宣教師 (レス 86)
挙動は未定

・主人公 (レス 89)

○ 採用思案中
・降霊術師 (レス 13)

・呪術師 (レス 13)

・従者 (レス 13)
改善 (レス 64)

・天人 (レス 54)

・九尾 (レス 58)

・蟲師 (レス 68)

・怠惰な死神 (レス85)


◎村編成案
○採用予定
グレラン村 (レス 65)

</pre>
</body></html>
