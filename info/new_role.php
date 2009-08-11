<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="../css/new_role.css">
<title>新役職情報</title>
</head>
<body>

<p>◆新役職について [Ver. 1.4.0 α18]<br>
・調整中なのでバージョンアップするたびに変わる可能性があります。
</p>
<p>
<a href="#human_side">村人陣営</a>
<a href="#wolf_side">人狼陣営</a>
<a href="#fox_side">妖狐陣営</a>
<a href="#quiz_side">出題者陣営</a>
<a href="#sub_role">サブ役職</a>
</p>
<table>
<caption>新役職早見表</caption>
  <tr>
    <th>名称</th>
    <th>陣営</th>
    <th>占い結果</th>
    <th>霊能結果</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="#quiz">出題者</a></td>
    <td>出題者</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">クイズ村の GM。</td>
    <td>Ver. 1.4.0 α2</td>
  </tr>
  <tr>
    <td><a href="#boss_wolf">白狼</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td>白狼</td>
    <td class="ability">占い結果が「村人」、霊能結果が「白狼」と出る人狼。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#soul_mage">魂の占い師</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">占った人の役職が分かる上位占い師だが、妖狐は呪殺できない。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#medium">巫女</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">突然死した人の所属陣営が分かる特殊な霊能者。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#poison_guard">騎士</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った狩人 (吊られても毒は発動しない)。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#fanatic_mad">狂信者</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼が誰か分かる狂人 (人狼からは狂信者は分からない)。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#child_fox">子狐</a></td>
    <td>妖狐</td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability">呪殺されないが人狼には食べられてしまう妖狐。<br>
      占いも出来るが時々失敗する。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#suspect">不審者</a></td>
    <td>村人</td>
    <td>人狼</td>
    <td>村人</td>
    <td class="ability">占い師に人狼と判定されてしまう村人 (本人には自覚なし)。<br>
      低確率で発言が遠吠えに入れ替わってしまう。</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="#mania">神話マニア</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人の役職をコピーする。<br>
      (入れ替わるのは2日目の朝)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="#poison_wolf">毒狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">毒を持った人狼 (人狼に当たったら不発)。</td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="#pharmacist">薬師</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒持ちを吊ったときに、投票していたら解毒(毒が発動しない)する。</td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="#unconscious">無意識</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼に無意識であることが分かってしまう村人 (本人には自覚なし)。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="#tongue_wolf">舌禍狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">自ら噛み投票を行った場合のみ、次の日に噛んだ人の役職が分かる。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="#reporter">ブン屋</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">尾行した人が噛まれた場合に、噛んだ人狼が誰か分かる特殊な狩人。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#dummy_mage">夢見人</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る占い師<br>
      (本人には「占い師」と表示される)。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#cute_wolf">萌狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">低確率で発言が遠吠えに入れ替わってしまう。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#dummy_necromancer">夢枕人</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る占い師<br>
      (本人には「霊能者」と表示される)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_guard">夢守人</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">狩人と思い込んでいる村人。<br>
      常に護衛成功メッセージが出るが誰も護衛していない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_common">夢共有者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「相方が身代わり君の共有者」と思い込んでいる村人。<br>
      共有者の囁きが見えない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_poison">夢毒者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">埋毒者と思い込んでいる村人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#soul_necromancer">雲外鏡</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑した人の役職が分かる上位霊能者。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#strong_poison">強毒者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">吊られた時に人外のみを巻き込む埋毒者 (村人に当たったら不発)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#incubate_poison">潜毒者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">時が経つと (現在は 5 日目以降) 毒を持つ村人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#resist_wolf">抗毒狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">一度だけ毒に耐えられる人狼。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#cursed_wolf">呪狼</a></td>
    <td>人狼</td>
    <td>人狼<br>(呪返し)</td>
    <td>人狼</td>
    <td class="ability">占われたら占った占い師を呪い殺す人狼。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#whisper_mad">囁き狂人</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼の夜の相談に参加できる狂人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#cursed_fox">天狐</a></td>
    <td>妖狐</td>
    <td>村人<br>(呪返し)</td>
    <td>村人</td>
    <td class="ability">占われたら占った占い師を呪い殺す妖狐。<br>
    人狼に噛まれても死なないが、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#poison_fox">管狐</a></td>
    <td>妖狐</td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">毒を持った妖狐。占われたら呪殺される (能力は調整中)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#white_fox">白狐</a></td>
    <td>妖狐</td>
    <td>村人<br>(呪殺無し)</td>
    <td>白狐</td>
    <td class="ability">呪殺されないが人狼には食べられてしまう妖狐 (能力は調整中)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#poison_cat">猫又</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">蘇生能力を持った埋毒者。<br>
    蘇生成功率は25%程度で選んだ人と違う人が復活することもある。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#assassin">暗殺者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人を暗殺できる村人。<br>
    狼・狐も暗殺できるが狩人の護衛を受けられない。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#psycho_mage">精神鑑定士</a></td>
    <td>村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人の心理状態を判定する特殊な占い師。<br>
      狂人・夢系・不審者・無意識を占うと「嘘をついている」と判定される。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#trap_mad">罠師</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一度だけ夜に村人一人に罠を仕掛けることができる狂人。<br>
    罠を仕掛けられた人の元に訪れた人狼・狩人系・暗殺者は死亡する。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
</table>

<table>
<caption>新サブ役職早見表</caption>
  <tr>
    <th>名称</th>
    <th>表示</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="#strong_voice">大声</a></td>
    <td>○</td>
    <td class="ability">常に大声になる</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#normal_voice">不器用</a></td>
    <td>○</td>
    <td class="ability">発言の大きさを変えられない</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#weak_voice">小声</a></td>
    <td>○</td>
    <td class="ability">常に小声になる</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#chicken">小心者</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に一票でも貰うとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#rabbit">ウサギ</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に一票も貰えないとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#perverseness">天邪鬼</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に他の人と投票先が被るとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#no_last_words">筆不精</a></td>
    <td>○</td>
    <td class="ability">遺言を残せない</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="#watcher">傍観者</a></td>
    <td>○</td>
    <td class="ability">投票数が 0 になる</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="#plague">疫病神</a></td>
    <td>×</td>
    <td class="ability">処刑者候補が複数いた場合に自分の投票先が吊り候補から除外される</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="#liar">狼少年</a></td>
    <td>○</td>
    <td class="ability">発言時に「人⇔狼」等が入れ替わる (たまに変換されないこともある)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="#random_voice">臆病者</a></td>
    <td>○</td>
    <td class="ability">声の大きさがランダムに変わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#earplug">耳栓</a></td>
    <td>○</td>
    <td class="ability">発言が一段階小さく見えるようになり、小声が聞き取れなくなる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#good_luck">幸運</a></td>
    <td>×</td>
    <td class="ability">自分が最多得票者で処刑者候補が複数いた場合は吊り候補から除外される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#bad_luck">不運</a></td>
    <td>×</td>
    <td class="ability">自分が最多得票者で処刑者候補が複数いた場合は優先的に吊られる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#upper_luck">雑草魂</a></td>
    <td>○</td>
    <td class="ability">2日目の得票数が +4 される代わりに、3日目以降は -2 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#downer_luck">一発屋</a></td>
    <td>○</td>
    <td class="ability">2日目の得票数が -4 される代わりに、3日目以降は +2 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#star">人気者</a></td>
    <td>○</td>
    <td class="ability">得票数が -1 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#disfavor">不人気</a></td>
    <td>○</td>
    <td class="ability">得票数が +1 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#random_voter">気分屋</a></td>
    <td>○</td>
    <td class="ability">投票数が 0-2 の範囲でランダムになる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#rebel">反逆者</a></td>
    <td>○</td>
    <td class="ability">権力者と同じ人に投票した場合に自分と権力者の投票数が 0 になる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#gentleman">紳士</a></td>
    <td>○</td>
    <td class="ability">時々発言が「紳士」な言葉に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#lady">淑女</a></td>
    <td>○</td>
    <td class="ability">時々発言が「淑女」な言葉に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#blinder">目隠し</a></td>
    <td>○</td>
    <td class="ability">発言者の名前が見えない (空白に見える)</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#silent">無口</a></td>
    <td>○</td>
    <td class="ability">発言の文字数に制限がかかる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#invisible">光学迷彩</a></td>
    <td>○</td>
    <td class="ability">発言の一部が空白に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#random_luck">波乱万丈</a></td>
    <td>○</td>
    <td class="ability">得票数に -2〜+2 の範囲でランダムに補正がかかる</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="#flattery">ゴマすり</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に投票先が誰とも被っていないとショック死する</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="#impatience">短気</a></td>
    <td>○</td>
    <td class="ability">決定者と同等の能力がある代わりに再投票になるとショック死する</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="#speaker">スピーカー</a></td>
    <td>○</td>
    <td class="ability">発言が一段階大きく見えるようになり、大声が聞き取れなくなります。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#upper_voice">メガホン</a></td>
    <td>○</td>
    <td class="ability">発言が一段階大きくなり、大声は音割れして聞き取れなくなります。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#downer_voice">マスク</a></td>
    <td>○</td>
    <td class="ability">発言が一段階小さくなり、小声は聞き取れなくなります。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#rainbow">虹色迷彩</a></td>
    <td>○</td>
    <td class="ability">発言時に虹の順番に合わせて色が入れ替えられてしまいます。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#panelist">解答者</a></td>
    <td>○</td>
    <td class="ability">投票数が 0 になり、出題者に投票したらショック死する。<br>
    クイズ村専用。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
</table>

<pre>
◎<a name="human_side">村人陣営</a>
◆占い師系
○<a name="soul_mage">魂の占い師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
占った人の役職が分かる上位占い師。 妖狐を占っても呪殺できません。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、魂の占い師視点、魂の占い師を名乗る人は偽者です。
魂の占い師が出現した場合は出現人数と同じだけ占い師が減ります。

※Ver. 1.4.0 α15〜
妖狐を占っても呪殺できません。
判定は「妖狐」と出るので、頑張って信用を勝ち取って吊ってください。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
白狼だろうが子狐だろうが分かってしまうので村側最強クラスですが、その分狙われやすいでしょう。

[作成者からのアドバイス]
状況によっては、始めは普通の占い師として振舞うのも手。
また、魂の占い師を CO していても村側役職 (狩人や霊能者) を素直に言うと
人狼に狙われるので適度に結果を騙り、相手と合わせるのも手。
(例えば、狩人→騎士、埋毒者→狩人、夢毒人→埋毒者)
逆に言うと、騙る場合は「村側陣営」などとごまかす事で破綻しにくくなる。


○<a name="dummy_mage">夢見人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α14〜]
本人には「占い師」と表示されており、占い行動もできるが結果は逆になる。
呪殺できない代わりに呪返しも受けない。

※Ver. 1.4.0 α18〜
占い結果がランダムから「村人」⇔「人狼」反転に変わりました。
確定白(例えば共有者)を占って人狼判定が出たら本人視点夢見人確定です。
また、精神鑑定士から「嘘つき」判定を受けても同様です。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 17 が原型です。
完全ランダムでは占い結果が全く役に立たなくなるので
白黒反転に変更しました。


○<a name="psycho_mage">精神鑑定士</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
村人の心理状態を調べて「嘘つき」を探し出す特殊な占い師。
狂人・夢系・不審者・無意識を占うと「嘘をついている」と判定される。
それ以外は「正常である」と判定される。

[作成者からのコメント]
対狂人専門の占い師です。本人視点と実際の役職が違うタイプ
(夢系・不審者・無意識)にも対応しています。
精神鑑定士を真と見るなら占われた人視点の役職がほぼ確定します。
人狼や妖狐の騙りは見抜けないので注意してください。


◆霊能者系
○<a name="soul_necromancer">雲外鏡</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
「人」と「狼」だけではなく、役職が分かる上位霊能者。

[作成者からのコメント]
魂の占い師の霊能者バージョンです。
占いと違ってメリットが少ないので後回しにしていましたが夢枕人とセットで出すことで
こっちは本人視点、判定に偽りが絶対に無いというアドバンテージが与えられます。
しかし、「死人に口無し」故に魂の占い師よりもはるかに騙りやすいですね。


○<a name="dummy_necromancer">夢枕人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「霊能者」と表示されており、霊能判定も表示されるが結果は逆になる。
白狼、狐等は正しい結果が表示される。

※Ver. 1.4.0 α18〜
霊能結果がランダムから「村人」⇔「人狼」反転に変わりました。
精神鑑定士から「嘘つき」判定を受けたら本人視点夢枕人確定です。

[作成者からのコメント]
夢見人の霊能者バージョンです。
完全ランダムでは霊能結果が全く役に立たなくなるので
白黒反転に変更しました。
もう少し調整を入れるかもしれません。


○<a name="medium">巫女</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
突然死した人の所属陣営が分かる特殊な霊能者。
闇鍋モードで登場する「ショック死」する人たちの情報を取るのが主な仕事だが
普通の霊能と違うので注意。

所属陣営とは、勝敗が決まったときの陣営なので
人狼系と狂人系は「人狼」
妖狐・子狐は「妖狐」
キューピッドは「恋人」
出題者は「出題者」
それ以外は「村人」と出る

また、メイン役職のみが判定の対象 (サブ役職は分からない)。
つまり、恋人はサブ役職なので「恋人」と判定されるのはキューピッドのみ。

※Ver. 1.4.0 α8〜
通常闇鍋モードではキューピッドが出現している場合は確実に出現します。
(ただし、巫女が出現してもキューピッドが出現しているとは限りません)

※Ver. 1.4.0 α9〜
恋人後追いにも対応。(後追いした恋人のみ、元の所属陣営が分かる)

[作成者からのコメント]
式神研のオリジナル役職です。
闇鍋モードで大量の突然死が出ることになったので作ってみましたが
霊能者より地味な存在ですね。騙るのも容易なのでなかなか報われないかもしれません。


◆狩人系
○<a name="poison_guard">騎士</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
いわゆる毒＋狩人ですが、吊られた場合は毒は発動しません。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、騎士視点、騎士を名乗る人は偽者です。
しかし、埋毒者や狩人が騎士を騙る可能性もあるので敵対陣営とは限りません。
騎士が出現した場合は出現人数と同じだけ狩人と埋毒者が減ります。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
技術的に簡単だったので軽い気持ちで作ってみましたがとんでもなく強いようです。
α8 以降は出現率を大幅に落としたのでこれでバランスが取れるかな？


○<a name="reporter">ブン屋</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α14〜]
夜に投票(尾行)した人が噛まれた場合に、噛んだ狼が分かる特殊な狩人です。
また、以下のような特徴があります。
1. 人狼や妖狐を尾行したら殺されます (生きている人には普通に襲撃されたメッセージが出ます)
2. 遺言を残せません
3. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は噛まれます)
4. 騎士はブン屋でも通常通り護衛可能です
5. 尾行対象者が狩人に護衛されていた場合は何も出ません
6. 尾行した妖狐が噛まれた場合は尾行成功扱いとなります (殺されません)
   (尾行成功メッセージ＆対象が死んでいない＝狼が狐を噛んだ)

※通常闇鍋モードの出現法則は調整中です。

[作成者からのコメント]
新役職考案スレ (最下参照) の 5 が原型です。
活躍するのは非常に難しいですが、成功したときには大きなリターンがあります。
狐噛みの現場をスクープするブン屋を是非見てみたいものです。


○<a name="dummy_guard">夢守人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「狩人」と表示されており、護衛行動を取ることができる。
必ず護衛成功メッセージが表示されるが、表示されるだけで誰も護衛していない。

[作成者からのコメント]
夢見人の狩人バージョンです。
常に護衛成功メッセージが出るので一度でも失敗したら夢守人で無い事を確認できます。
みなさん一度くらいは全て護衛成功して勝利してみたいと思った事、ありませんか？


◆共有者系
○<a name="dummy_common">夢共有者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「『相方が身代わり君』の共有者」と表示されている村人。
が、夜に発言しても「ひそひそ声」にはならないし、本物の共有者の声も聞こえない。

[作成者からのコメント]
夢見人の共有者バージョンです。
「ひそひそ声」が発生しないので真共有にはなれません。
(闇鍋モードであっても「ひそひそ声」は消しません。仕様です)
証明手段が無いので容易に騙れますね。きっと真でも吊られることでしょう。


◆埋毒者系
○<a name="strong_poison">強毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
吊られた時に人外(狼と狐)のみを巻き込む埋毒者です。
ただし、本人には「埋毒者」と表示されているので自覚はありません。
埋毒者の巻き込み対象設定 (管理者に確認を取ってください) が投票者ランダムだった場合、
人外が投票していなければ毒は不発となります。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
状況にもよりますが、魂の占い師に鑑定してもらったら即吊ってもらうと強いですね。
投票者ランダムの設定であれば、その時の投票結果は重要な推理材料にもなります。


○<a name="incubate_poison">潜毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
一定期間後 (現在は 5 日目以降) に毒を持つ村人です。
毒を持ったら本人に追加のシステムメッセージが表示されます。
毒能力は埋毒者と同じです(調整が入る可能性があります)。


[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
いかに毒を持つまで時間を稼ぐかがポイントです
逆に埋毒者がこれを騙って狼の噛みを引き寄せたりするのもありですね。
毒がらみの騙りのパターンが広がると思います。


○<a name="dummy_poison">夢毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「埋毒者」と表示されている村人。

[作成者からのコメント]
夢見人の埋毒者バージョンです。
毒は持っていませんが、身代わり君がこれになることはありません。
偽者ではありますがどちらかと言うと人狼が不利になる役職ですね。
夢毒者である事に賭けて真埋毒者を噛みに行く狼が出るかもしれません。


○<a name="poison_cat">猫又</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
蘇生能力を持った埋毒者。
「霊界で配役を公開しない」オプションが有効になっていないと蘇生行動はできません。
2日目の夜以降から、死んでいる人を一人選んで蘇生を試みます。
「蘇生を行わない」を選ぶこともできます。
蘇生成功率は25%程度で、選んだ人と違う人が復活することもあります。

[作成者からのコメント]
他の国に実在する役職です。
「霊界で配役を公開しない」オプションを有効にしておかないと
ただの埋毒者になる点に注意してください。


◆その他
○<a name="pharmacist">薬師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α12〜]
毒持ちを吊ったときに、薬師が投票していたら解毒(毒が発動しない)します。

[作成者からのコメント]
新役職考案スレ (最下参照) の 24 が原型です。
毒狼の対抗役職です。
埋毒者系に対しても効果を発揮します。


○<a name="mania">神話マニア</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α11〜]
初日の夜に誰か一人を選んでその人の役職をコピーします。
入れ替わるのは2日目の朝です。
神話マニアを選んでしまった場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。
通常闇鍋モードでは16人以上から出現します。

[作成者からのコメント]
カード人狼にある役職です。元と違い、占いや狼以外の役職もコピーします。
CO するべきかどうかは、コピーした役職次第です。


○<a name="suspect">不審者</a> (占い結果：人狼、霊能結果：村人) [Ver. 1.4.0 α9〜]
不審なあまり、占い師に人狼と判定されてしまう村人。
本人には一切自覚がない (村人と表示されている) ので偽黒を貰った様にしか感じない。
ライン推理を狂わせるとんでもない存在。

※Ver. 1.4.0 α16〜
低確率で発言が遠吠えに入れ替わってしまう (萌狼と同じ)。

[作成者からのコメント]
村人陣営ですが人狼陣営の切り札的存在ですね。
立ち回りの上手い人がこれを引くと大変なことになりそうです。
ただし、人狼サイドにも不審者である事は分からないので
占い師の真贋が読みづらくなります。


○<a name="unconscious">無意識</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α13〜]
他の国で言うと「自覚のない夢遊病者」。
本人には「村人」と表示されているが、夜になると無意識に歩きまわるため
人狼に無意識であることが分かってしまう。

[作成者からのコメント]
不審者同様、村人陣営ですが人狼陣営に有利な存在です。
人狼サイドから見ると無能力であることが確定の村人なので噛まれにくいです。
確定白で長期間放置されると白狼扱いされるかも。


○<a name="assassin">暗殺者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
夜に村人一人を選んで暗殺できます。現在の仕様は以下です。

1. 暗殺可能な役職に制限はありません (人狼・妖狐でも暗殺可能)
2. 人狼に襲撃されたり、罠師の罠にかかると暗殺は無効です
3. 「暗殺する / しない」を必ず投票する必要があります
4. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は噛まれます)
5. 騎士はブン屋でも通常通り護衛可能です
6. 暗殺者がお互いを襲撃した場合は相打ちになります
7. 暗殺者に暗殺された占い師の呪殺、猫又の蘇生は無効になります
8. 暗殺者に暗殺されても狩人系の護衛判定は有効です

[作成者からのコメント]
村側陣営の最終兵器とも呼べる存在ですね。
判定順とかが複雑なので色々調整が入るかもしれません。


◎<a name="wolf_side">人狼陣営</a>
◆人狼系
○<a name="boss_wolf">白狼</a> (占い結果：村人、霊能結果：白狼) [Ver. 1.4.0 α3-7〜]
他の国でいうところの「大狼」。
占い師を欺ける人狼だが、霊能者には見抜かれる。

[作成者からのコメント]
占いが怖くないので LW を担うことを前提に動くことを勧めます。
占いなどを騙らずに潜伏することはもちろん、噛み投票も
なるべくしない (毒噛み対策) 方がいいと思います。


○<a name="poison_wolf">毒狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α12〜]
いわゆる埋毒+人狼。
吊られた時に巻き込む対象の決定法則は埋毒者と同じだが、狼が選ばれた場合は不発となる。

[作成者からのコメント]
新役職考案スレ (最下参照) の 31 が原型です。
毒狼CO した人を吊って誰も巻き込まれなかった場合は以下のケースがありえます。
1. 薬師が投票していた
2. 狼が選ばれた
3. 偽(騙り)だった

Ver. 1.4.0 α12 からは毒持ちを吊った時に巻き込まれる対象を設定で制限できます。
(設定は管理者に確認を取ってください)
「投票者ランダム」だった場合、この状況から推理を詰めることが可能になります。
薬師が投票していない場合、毒狼を真と見るなら投票者に狼がいる事になります。
しかし、これが騙りの場合は・・・？


○<a name="tongue_wolf">舌禍狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α13〜]
自ら噛み投票を行った場合のみ、次の日に噛んだ人の役職が分かる。
ただし、村人を噛んだ場合は能力を失ってしまう。

[作成者からのコメント]
新役職考案スレ (最下参照) の 69 の「賢狼」が原型です。
ベーグル(真偽の付かない占い噛み) 時に動くのが一番有効でしょう。
ベーグル後はワンチャンスの狩人確認に使うと良いと思います。
また、村人の場合に能力を失いますが身代わり君を噛むのも面白いですね。
結果が出てから仲間に伝える前に吊られる可能性があるので
事前にブロックサインを決めておくと良いでしょう。


○<a name="cute_wolf">萌狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α14〜]
低確率で発言が遠吠えに入れ替わってしまう。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗がモデルです。


○<a name="resist_wolf">抗毒狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α17〜]
一度だけ毒に耐えられる(毒に中っても死なない)人狼です。
毒吊りの巻き込み、毒噛み両方に対応しています。

[作成者からのコメント]
新役職考案スレ (最下参照) の 25 を参考にしています。
現時点でほぼ無敵の能力を誇る騎士への対抗策として作成しました。
安易に CO する騎士・埋毒者を葬ってやりましょう！

○<a name="cursed_wolf">呪狼</a> (占い結果：人狼(呪返し)、霊能結果：人狼) [Ver. 1.4.0 α17〜]
占われたら占ってきた占い師を呪い殺す人狼です。
呪殺された占い師には襲撃されたときと同じ死亡メッセージが出ます。

[作成者からのコメント]
他の国に実在する役職です。
新役職考案スレ (最下参照) の 69 を参考にしています。
魂の占い師や子狐も呪い殺せます。
占い師側の対策は、遺言に占い先をきちんと書いておく事です。
死体の数や状況にもよりますが、残った村人がきっと仇を討ってくれるでしょう。


◆狂人系
○<a name="fanatic_mad">狂信者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
人狼が誰か分かる狂人 (人狼からは狂信者は分からない)。
狼にとって完璧な騙りが出来るはず。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは16人未満では出現しません。
16人以上で参加人数と同じ割合で出現します。(16人なら16%、50人なら50%)
最大出現人数は1人です。
つまり、狂信者視点、狂信者を名乗る人は偽者です。
狂信者が出現した場合は出現人数と同じだけ狂人が減ります。

[作成者からのコメント]
他の国に実在する役職です。
狼サイドの新兵器です。作った当初は魂の占い師や騎士と出現率のせいもあって
活躍できなかったようですが、本来はかなり強いはず。


○<a name="whisper_mad">囁き狂人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
人狼の夜の相談に参加できる狂人です。
人狼と違い、発言が遠吠えに変換されません。

[作成者からのコメント]
通称「C国狂人」、最強クラスの狂人です。
相談できるので完璧な連携が取れます。


○<a name="trap_mad">罠師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
一度だけ夜に村人一人に罠を仕掛けることができる狂人。
罠を仕掛けられた人の元に訪れた人狼・狩人系・暗殺者は死亡する。
具体的な仕様は以下。

1. 人狼に襲撃されたら無効になります
2. 罠を仕掛けに行った先に他の罠師が罠を仕掛けていた場合は死亡します
3. 自分にも仕掛けることができます
4. 自分に仕掛けた場合は人狼に襲撃されても有効です (人狼が死にます)
5. 自分に仕掛けた場合は他の罠師が罠をかけていても本人は死にません
   (仕掛けに来た他の罠師は死にます)
6. 狩人系に護衛されると殺されます
7. 自分に仕掛けて狩人に護衛された場合は相打ちになります
8. 暗殺者が罠にかかった場合は暗殺行動は無効になります


[作成者からのコメント]
狼陣営に対暗殺者を何か……と考案してこういう形に落ち着きました。
一行動で多くの能力者を葬れる可能性を秘めています。
人狼の襲撃先を外しつつ狩人の護衛先や暗殺者の襲撃先を読み切って
ピンポイントで罠を仕掛けないといい仕事にならないので活躍するのは
非常に難しいと思いますが、当たればきっと最高の気分になれるはず。


◎<a name="fox_side">妖狐陣営</a>
※個々の能力は調整中です。
妖狐は妖狐の仲間が、子狐は妖狐・子狐全て分かります
妖狐は子狐が誰か分かりません
妖狐同士は夜会話(念話)できます(他人からはいっさい見えません)が、
子狐は念話を見ることも参加することも出来ません。

○<a name="child_fox">子狐</a> (占い結果：村人、霊能結果：子狐) [Ver. 1.4.0 α3-7〜]
呪殺されない代わりに人狼に襲われると食べられてしまう狐です。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、子狐視点、子狐を名乗る人は偽者です。
子狐が出現した場合は出現人数と同じだけ妖狐が減ります。

※Ver. 1.4.0 α17〜
占いができます。
判定結果は普通の占い師と同じで、呪殺は出来ません。
また、時々失敗します。

[作成者からのコメント]
他の国に実在する役職ですね。
狐陣営自体の出現数が少ないのでかなりのレア役職になりそうな予感。


○<a name="cursed_fox">天狐</a> (占い結果：村人(呪返し)、霊能結果：村人) [Ver. 1.4.0 α17〜]
占われたら占った占い師を呪い殺す妖狐。
人狼に噛まれても死なないが、狩人に護衛されると殺される。

○<a name="poison_fox">管狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α17〜]
毒を持った狐。吊られたり、噛まれたら発動します。(調整中)

○<a name="white_fox">白狐</a> (占い結果：村人、霊能結果：白狐) [Ver. 1.4.0 α17〜]
呪殺されない代わりに人狼に襲われると食べられてしまう狐です。(調整中)

[作成者からのコメント]
せっかくなので狐にも色々変種を投入してみます。
占い、霊能、噛み耐性、念話などの能力組み合わせはこれから調整します。


◎<a name="quze_side">出題者陣営</a>
○<a name="quiz">出題者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α2〜]
クイズ村の GM です。闇鍋村にも低確率で出現します。
勝利条件は「GM 以外が全滅していること」。
ルールの特殊なクイズ村以外ではまず勝ち目はありません。
引いたら諦めてください。

[作成者からのコメント]
クイズ村以外では恋人になったほうがまだましという涙目すぎる存在ですが
闇鍋なので全役職を出します。一回くらいは奇跡的な勝利を見てみたいですね。


--<a name="sub_role">ここからサブ役職</a>--
※Ver. 1.4.0 α8 現在、恋人以外のサブ役職は重なりません。
　(大声と小心者を同時にＣＯする人は最低でもどちらかが嘘です)

◎発言変化
○<a name="strong_voice">大声</a> [Ver. 1.4.0 α3-7〜]
発言が常に大声になります。

○<a name="normal_voice">不器用</a> [Ver. 1.4.0 α3-7〜]
発言の大きさを変えられません。

○<a name="weak_voice">小声</a> [Ver. 1.4.0 α3-7〜]
発言が常に小声になります。

[作成者からのコメント]
声の大きさも狼を推理するヒントになります。
ただのネタと思うこと無かれ。


○<a name="upper_voice">メガホン</a> [Ver. 1.4.0 α17〜]
発言が一段階大きくなり、大声は音割れして聞き取れなくなります。

○<a name="downer_voice">マスク</a> [Ver. 1.4.0 α17〜]
発言が一段階小さくなり、小声は聞き取れなくなります。

○<a name="random_voice">臆病者</a> [Ver. 1.4.0 α14〜]
声の大きさがランダムに変わります。

[作成者からのコメント]
固定があるならランダムもありだろうと思って作ってみました。
唐突に大声になるのは固定より鬱陶しいかも。


◎発言封印
○<a name="no_last_words">筆不精</a> [Ver. 1.4.0 α9〜]
遺言を残せません。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。
「遺言残せばいいや」と思って潜伏する役職にプレッシャーがかかります。
また、安直な遺言騙りもできなくなります。
昼の発言がより盛り上がるといいな、と思って作ってみました。


○<a name="blinder">目隠し</a> [Ver. 1.4.0 α14〜]
発言者の名前が見えません (空白に見えます)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 66 の「宵闇」の布石です。

※Ver. 1.4.0 α16〜
名前の最初に付いてる◆の色は変更しません。
これで、ユーザアイコンを見ればある程度推測できるようになります。

○<a name="earplug">耳栓</a> [Ver. 1.4.0 α14〜]
発言が一段階小さく見えるようになり、小声が聞き取れなります。
小声は共有者のヒソヒソ声に入れ替わります。

※Ver. 1.4.0 α16〜
小声が聞こえないだけではなく、大声→普通、普通→小声になります。

※Ver. 1.4.0 α17〜
小声は空白ではなく、共有者のヒソヒソ声に入れ替わります。

[作成者からのコメント]
ニコ生専用鯖のとあるプレイヤーさん考案のネタ役職です。
聞こえないなら取れ？ネタにマジレスしてはいけません。

○<a name="speaker">スピーカー</a> [Ver. 1.4.0 α17〜]
発言が一段階大きく見えるようになり、大声が音割れして聞き取れなくなります
大声はメガホンの大声と同じです。

○<a name="silent">無口</a> [Ver. 1.4.0 α14〜]
発言の文字数に制限がかかります (制限を越えるとそれ以降が「……」になります)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 51 が原型です。
よほど長い名前の人でもない限り、最低限の占い師のCO等には
影響が出ない程度にしてあります。


◎発言変換
○<a name="liar">狼少年</a> [Ver. 1.4.0 α11〜]
発言時に一部のキーワードが入れ替えられてしまいます。
(例えば、人⇔狼、白⇔黒、○⇔●です)

※Ver. 1.4.0 α14〜
変換対象キーワードが増えました (何が変わるかは自ら試してください)。
時々変換されないことがあります (たまには真実を語るのです)。

[作成者からのコメント]
流石鯖の管理人さんとの会話から生まれた役職です。
占い師がそれと知らずにCOすると大変なことになりそうです。
回避するのは簡単ですがそれを意識しないといけないだけでも
結構な負担ではないでしょうか？


○<a name="invisible">光学迷彩</a> [Ver. 1.4.0 α14〜]
発言の一部が空白に入れ替えられてしまいます。

※Ver. 1.4.0 α17〜
変換率を落とした代わりに文字数が増えると変換率がアップします。
一定文字数を超えると完全に消えます。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんのアイディアが原型です。
変換される確率は設定で変更できます。


○<a name="rainbow">虹色迷彩</a> [Ver. 1.4.0 α17〜]
発言時に虹の順番に合わせて色が入れ替えられてしまいます。
(例：赤→橙、橙→黄色)


○<a name="gentleman">紳士</a> [Ver. 1.4.0 α14〜]
時々発言が「紳士」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

○<a name="lady">淑女</a> [Ver. 1.4.0 α14〜]
時々発言が「淑女」な言葉に入れ替えられてしまいます。
(発言内容は設定で変更可能です)

[作成者からのコメント]
紳士はウミガメ人狼のとあるプレイヤーさんの RP が原型です。
淑女は新役職考案スレ (最下参照) の 135 さんのリクエストに応えました。
発言内容が完全に入れ替わるので狼少年より酷いです。
どんな言葉に入れ替わるのかは管理人さんの気紛れ次第。


◎投票数変化
○<a name="rebel">反逆者</a> [Ver. 1.4.0 α14〜]
権力者と同じ人に投票した場合に自分と権力者の投票数が 0 になります。
それ以外のケースなら通常通り(1票)です。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんが実際にやってしまった失敗をヒントに
対権力者を作成してみました。


○<a name="random_voter">気分屋</a> [Ver. 1.4.0 α14〜]
投票数が 0-2 の範囲でランダムになります(毎回変わります)。
(投票数の範囲や確率は調整される可能性があります)

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。


○<a name="watcher">傍観者</a> [Ver. 1.4.0 α9〜]
投票数が 0 になります(投票行為自体は必要です)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 8 が原型です。


◎得票数変化
・投票ショック死判定には影響しません (ショック死判定は投票「人数」で行なわれます)。
・得票数が減る場合でもマイナスにはなりません。
例：得票が 1 で -2 された場合 → 得票数は 0 と計算される

○<a name="upper_luck">雑草魂</a> [Ver. 1.4.0 α14〜]
2日目の得票数が +4 される代わりに、3日目以降は -2 されます。

※Ver. 1.4.0 α14〜
2日目の得票数補正を +2 から +4 に変更しました

○<a name="downer_luck">一発屋</a> [Ver. 1.4.0 α14〜]
2日目の得票数が -4 される代わりに、3日目以降は +2 されます。

※Ver. 1.4.0 α14〜
2日目の得票数補正を -2 から -4 に変更しました

[作成者からのコメント]
雑草魂はウミガメ人狼のとあるプレイヤーさんがモデルです。


○<a name="star">人気者</a> [Ver. 1.4.0 α14〜]
得票数が -1 されます。

○<a name="disfavor">不人気</a> [Ver. 1.4.0 α14〜]
得票数が +1 されます。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 64 が原型です。
得票数が変化するタイプは権力者同様、終盤になると大きな影響を与えます。
「投票した票数を公表する」がオフになっていると誰が何を持っているのか
全然分からなくなるので一体どうなることやら。

○<a name="random_luck">波乱万丈</a> [Ver. 1.4.0 α15〜]
得票数に -2〜+2 の範囲でランダムに補正がかかります。

[作成者からのコメント]
発想は臆病者と同じですね。得票数変化バージョンです。
得票数の変動の程度には補正をかけていません。
その方が波乱万丈らしいでしょう？


◎処刑者候補変化
この系統が複数いる場合の判定順は以下です。
決定者の投票先＞不運＞短気の投票先＞幸運が逃れる＞疫病神の投票先が逃れる

○<a name="plague">疫病神</a> [Ver. 1.4.0 α9〜]
自分が最多得票者に投票していて、処刑者候補が複数いた場合に
その人が吊り候補から除外される。

[作成者からのコメント]
いわゆる逆決定者です (決定者同様、本人には分かりません)。
新役職考案スレ (最下参照) の 8 が原型です。


○<a name="good_luck">幸運</a> [Ver. 1.4.0 α14〜]
自分が最多得票者で処刑者候補が複数いた場合は吊り候補から除外される。
(本人には分からない)

○<a name="bad_luck">不運</a> [Ver. 1.4.0 α14〜]
自分が最多得票者で処刑者候補が複数いた場合は優先的に吊られる。
(本人には分からない)

[作成者からのコメント]
本人に付随する決定者/逆決定者です (本人には分かりません)。
ウミガメ人狼のプレイヤーさんから原案を頂きました。


◎投票系ショック死
○<a name="chicken">小心者</a> [Ver. 1.4.0 α3-7〜]
昼の投票時に一票でも貰うとショック死します。

○<a name="rabbit">ウサギ</a> [Ver. 1.4.0 α3-7〜]
昼の投票時に一票も貰えないとショック死します。

○<a name="perverseness">天邪鬼</a> [Ver. 1.4.0 α3-7〜]
昼の投票時に他の人と投票先が被るとショック死します。

[作成者からのコメント]
原案はウミガメ人狼のプレイヤーさん達に提供してもらったものです。
ウサギにはモデルがいます。
ウサギ＆天邪鬼がいいコンビになりつつありますが
お互いが敵対陣営の可能性もあるのが面白いですね。

○<a name="flattery">ゴマすり</a> [Ver. 1.4.0 α15〜]
昼の投票時に投票先が誰とも被っていないとショック死します。

[作成者からのコメント]
天邪鬼の逆ですね。アイディア自体は早くからありましたが
なかなかいい名前が思いつかなかったに実装が遅れました。

○<a name="impatience">短気</a> [Ver. 1.4.0 α15〜]
決定者と同等の能力がある代わりに再投票になるとショック死します。

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。
自覚のある決定者です。
その分だけ判定の優先度が決定者より低めになっています。

○<a name="panelist">解答者</a> [Ver. 1.4.0 α17〜]
投票数が 0 になり、出題者に投票したらショック死します。
クイズ村専用です (闇鍋モードにも出現しません)。


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
