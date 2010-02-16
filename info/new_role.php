<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="../css/new_role.css">
<title>新役職情報</title>
</head>
<body>

<p>◆新役職について [Ver. 1.4.0 β3]<br>
・調整中なのでバージョンアップするたびに変わる可能性があります。
</p>
<p>
<a href="#human_side">村人陣営</a>
<a href="#wolf_side">人狼陣営</a>
<a href="#fox_side">妖狐陣営</a>
<a href="#lovers_side">恋人陣営</a>
<a href="#chiroptera_side">蝙蝠陣営</a>
<a href="#quiz_side">出題者陣営</a>
</p>

<p>
<a href="#main_role">メイン役職</a>
Ver. 1.4.0
<a href="#140alpha2">α2</a>
<a href="#140alpha3">α3-7</a>
<a href="#140alpha9">α9</a>
<a href="#140alpha11">α11</a>
<a href="#140alpha12">α12</a>
<a href="#140alpha13">α13</a>
<a href="#140alpha14">α14</a>
<a href="#140alpha17">α17</a>
<a href="#140alpha18">α18</a>
<a href="#140alpha19">α19</a>
<a href="#140alpha20">α20</a>
<a href="#140alpha21">α21</a>
<a href="#140alpha22">α22</a>
<a href="#140alpha23">α23</a>
<a href="#140alpha24">α24</a>
<a href="#140beta2">β2</a>
</p>

<p>
<a href="#sub_role">サブ役職</a>
Ver. 1.4.0
<a href="#sub_140alpha3">α3-7</a>
<a href="#sub_140alpha9">α9</a>
<a href="#sub_140alpha11">α11</a>
<a href="#sub_140alpha14">α14</a>
<a href="#sub_140alpha15">α15</a>
<a href="#sub_140alpha17">α17</a>
<a href="#sub_140alpha19">α19</a>
<a href="#sub_140alpha21">α21</a>
<a href="#sub_140alpha22">α22</a>
<a href="#sub_140alpha23">α23</a>
<a href="#sub_140beta2">β2</a>
</p>

<table>
<caption><a name="main_role">新役職早見表</a></caption>
  <tr>
    <th>名称</th>
    <th>陣営</th>
    <th>占い結果</th>
    <th>霊能結果</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="#quiz" name="140alpha2">出題者</a></td>
    <td><a href="#quiz_side">出題者</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">クイズ村の GM。</td>
    <td>Ver. 1.4.0 α2</td>
  </tr>
  <tr>
    <td><a href="#boss_wolf" name="140alpha3">白狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>白狼</td>
    <td class="ability">占い結果が「村人」、霊能結果が「白狼」と出る人狼。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#soul_mage">魂の占い師</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">占った人の役職が分かる上位占い師。<br>
      妖狐を呪殺できないが呪返しは受ける。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#medium">巫女</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">突然死した人の所属陣営が分かる特殊な霊能者。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#poison_guard">騎士</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った狩人 (吊られても毒は発動しない)。<br>
      <a href="#reporter">ブン屋</a>や<a href="#assassin">暗殺者</a>でも護衛可能。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#fanatic_mad">狂信者</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼が誰か分かる狂人 (人狼からは狂信者は分からない)。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#child_fox">子狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability">呪殺されないが人狼に襲撃されると殺される妖狐。<br>
      仲間は分かるが念話はできない。占いも出来るが時々失敗する。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="#suspect" name="140alpha9">不審者</a></td>
    <td><a href="#human_side">村人</td>
    <td>人狼</td>
    <td>村人</td>
    <td class="ability">占い師に人狼と判定されてしまう村人 (表示は「村人」)。<br>
      低確率で発言が遠吠えに入れ替わってしまう (<a href="#cute_wolf">萌狼</a>と同じ)。</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="#mania" name="140alpha11">神話マニア</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人の役職をコピーする。<br>
      (入れ替わるのは 2 日目の朝)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="#poison_wolf" name="140alpha12">毒狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">毒を持った人狼。毒の対象は狼以外。</td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="#pharmacist">薬師</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼に投票した人が毒を持っているか翌朝に分かる。<br>
      毒持ちを吊ったときに、投票していたら解毒 (毒が発動しない) する。
    </td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="#unconscious" name="140alpha13">無意識</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼に無意識であることが分かってしまう村人 (表示は「村人」)。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="#tongue_wolf">舌禍狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">自ら襲撃投票を行って、成功した場合は襲撃した人の役職が<br>
      分かる人狼。村人を襲撃すると能力を失う。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="#reporter" name="140alpha14">ブン屋</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">尾行した人が噛まれた場合に、噛んだ人狼が誰か分かる特殊な狩人。<br>
      遺言を残せない。人外 (狼と狐) を尾行したら殺される。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#dummy_mage">夢見人</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る占い師 (表示は「占い師」)。<br>
      呪殺できない代わりに呪いや占い妨害の影響を受けない。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#cute_wolf">萌狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">低確率で発言が遠吠えに入れ替わってしまう。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="#dummy_necromancer" name="140alpha17">夢枕人</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る霊能者 (表示は「霊能者」)。<br>
      <a href="#corpse_courier_mad">火車</a>の妨害の影響を受けない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_guard">夢守人</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">狩人と思い込んでいる村人 (表示は「狩人」)。<br>
      常に護衛成功メッセージが出るが誰も護衛していない。<br>
      何らかの形で<a href="#dream_eater_mad">獏</a>に接触した場合は狩ることができる。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_common">夢共有者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「相方が身代わり君の共有者」と思い込んでいる村人。<br>
      共有者の囁きが見えない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#dummy_poison">夢毒者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">埋毒者と思い込んでいる村人 (表示は「埋毒者」)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#soul_necromancer">雲外鏡</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑した人の役職が分かる上位霊能者。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#strong_poison">強毒者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">吊られた時に人外 (狼と狐) のみを巻き込む上位埋毒者。<br>
      表示は「埋毒者」で、村人に当たったら不発。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#incubate_poison">潜毒者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">時が経つと (現在は 5 日目以降) <a href="#strong_poison">強毒者</a>相当の毒を持つ村人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#resist_wolf">抗毒狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">一度だけ毒に耐えられる人狼。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#cursed_wolf">呪狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼<br>(呪返し)</td>
    <td>呪狼</td>
    <td class="ability">占われたら占った占い師を呪い殺す人狼。霊能結果は「呪狼」。<br>
      <a href="#voodoo_killer">陰陽師</a>に占われたら殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#whisper_mad">囁き狂人</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼の夜の相談に参加できる狂人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#cursed_fox">天狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪返し)</td>
    <td>妖狐</td>
    <td class="ability">占われたら占った占い師を呪い殺す妖狐。<br>
      人狼に襲撃されても死なない。<br>
      <a href="#vookoo_killer">陰陽師</a>に占われるか狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#poison_fox">管狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">毒を持った妖狐。吊られた場合の毒の対象は狐以外。<br>
      占われたら呪殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#white_fox">白狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>妖狐</td>
    <td class="ability">呪殺されないが人狼に襲撃されると殺される妖狐。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#poison_cat" name="140alpha18">猫又</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">蘇生能力を持った特殊な埋毒者。<br>
    蘇生成功率は 25% 程度で選んだ人と違う人が復活することもある。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#assassin">暗殺者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人を暗殺できる村人。<br>
    人外 (狼や狐) でも暗殺できるが狩人の護衛を受けられない。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#psycho_mage">精神鑑定士</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人の心理状態を判定する特殊な占い師。<br>
      狂人・夢系・<a href="#suspect">不審者</a>・<a href="#unconscious">無意識</a>を占うと「嘘をついている」と判定される。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#trap_mad">罠師</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一度だけ夜に村人一人に罠を仕掛けることができる特殊な狂人。<br>
      罠を仕掛けた人の元に訪れた人狼・狩人系(狩人、<a href="#poison_guard">騎士</a>、<a href="#reporter">ブン屋</a>)<br>
      <a href="#assassin">暗殺者</a>は死亡する。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="#jammer_mad" name="140alpha19">月兎</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人の占い行動を妨害する特殊な狂人。<br>
      狩人に護衛されると殺される。α21から邪魔狂人 → 月兎 に変更。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="#sex_mage">ひよこ鑑定士</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人の性別を判別する特殊な占い師。<br>
      <a href="#chiroptera_side">蝙蝠</a>を占うと「蝙蝠」と判定される。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="#voodoo_mad" name="140alpha20">呪術師</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人に呪いをかける特殊な狂人。<br>
      呪われた人を占った占い師は呪返しを受ける。<br>狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#voodoo_fox">九尾</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人に呪いをかける妖狐。<br>
      呪われた人を占った占い師は呪返しを受ける。<br>
      人狼に襲撃されても死なないが、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#anti_voodoo">厄神</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">護衛した人の厄 (占い妨害、呪返し、憑依) を祓う特殊な狩人。<br>
      成功した場合は次の日に専用のシステムメッセージが表示される。
    </td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#voodoo_killer">陰陽師</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">対呪い専門の特殊な占い師。<br>
      占った人が呪い持ちか<a href="#possessed_wolf">憑狼</a>の場合は呪殺し、<br>
      誰かに呪いをかけられていた場合は解呪して、呪返しの発動を<br>
      キャンセルする。呪殺か解呪が成功した場合のみ、<br>
      次の日に専用のシステムメッセージが表示される。<br>
      呪殺の死亡メッセージは「呪返し」と同じ。<br>
    </td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#yama_necromancer">閻魔</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">前日の死者の死因が分かる特殊な霊能者。<br>
      死因は画面の下に表示される「〜は無残な〜」の下の行に<br>
      「(〜は人狼に襲撃されたようです)」等と表示される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#silver_fox">銀狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">仲間が分からない妖狐。<br>
      (他の妖狐、<a href="#child_fox">子狐</a>からも仲間であると分からない)</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="#self_cupid" name="140alpha21">求愛者</a></td>
    <td><a href="#lovers_side">恋人</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分撃ち確定のキューピッド。矢を打った相手に<a href="#mind_receiver">受信者</a>が追加される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#chiroptera">蝙蝠</a></td>
    <td><a href="#chiroptera_side">蝙蝠</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">生き残ったら勝利になる。<br>他の陣営の勝敗とは競合しない。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#poison_chiroptera">毒蝙蝠</a></td>
    <td><a href="#chiroptera_side">蝙蝠</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った蝙蝠。吊られた場合の毒の対象は人外 (狼と狐) と蝙蝠。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#cursed_chiroptera">呪蝙蝠</a></td>
    <td><a href="#chiroptera_side">蝙蝠</a></td>
    <td>村人<br>(呪返し)</td>
    <td>村人</td>
    <td class="ability">占われたら占った占い師を呪い殺す蝙蝠。<br>
      <a href="#voodoo_killer">陰陽師</a>に占われるか、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#silver_wolf">銀狼</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">仲間が分からない人狼。<br>
      (他の人狼、<a href="#fanatic_mad">狂信者</a>、<a href="#whisper_mad">囁き狂人</a>からも仲間であると分からない)</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#mind_scanner">さとり</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人を<a href="#mind_read">サトラレ</a>にする。<br>
      投票結果が出るのは 2 日目以降で、<a href="#unconscious">無意識</a>の発言は読めない。<br>
      狼の遠吠えが見えない。
    </td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#corpse_courier_mad">火車</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分が投票して吊った人の霊能結果を隠蔽できる特殊な狂人。<br>
      <a href="#dummy_necromancer">夢枕人</a>には影響しない。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#dream_eater_mad">獏</a></td>
    <td><a href="#wolf_side">人狼</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に投票した夢系能力者を殺すことができる特殊な狂人。<br>
      何らかの形で<a href="#dummy_guard">夢守人</a>に接触した場合は殺される。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#jealousy" name="140alpha22">橋姫</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼の投票時に、同一キューピッドの恋人が揃って自分に投票したら<br>
      投票した恋人をショック死させる。吊られた場合は無効。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="#unknown_mania" name="140alpha23">鵺</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人と同じ所属陣営にする。<br>
     自分と投票先に<a href="#mind_friend">共鳴者</a>がつく。
      (結果が表示されるのは 2 日目の朝)</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#mind_cupid">女神</a></td>
    <td><a href="#lovers_side">恋人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人を<a href="#mind_friend">共鳴者</a>にするキューピッド。<br>
      他人撃ちの場合は、さらに自分が二人の<a href="#mind_receiver">受信者</a>になる。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#priest" name="140alpha24">司祭</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在、生存している村人陣営の人数が分かる。<br>
     現在は 4日目以降、1日おき (4 → 6 → 8 →...)。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#scarlet_fox">紅狐</a></td>
    <td><a href="#fox_side">妖狐</td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">人狼からは<a href="#unconscious">無意識</a>に見える妖狐。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#wise_wolf">賢狼</a></td>
    <td><a href="#wolf_side">人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability"><a href="#fox_side">妖狐陣営</a>の念話が共有者のひそひそ声に変換されて聞こえる人狼。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#possessed_wolf">憑狼</a></td>
    <td><a href="#wolf_side">人狼</td>
    <td>人狼</td>
    <td>憑狼</td>
    <td class="ability">襲撃が成功したら襲撃した人を乗っ取る人狼。<br>
      身代わり君と妖狐は乗っ取れない。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#cute_fox">萌狐</a></td>
    <td><a href="#fox_side">妖狐</td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">低確率で発言が遠吠えに入れ替わってしまう妖狐。<br>
    遠吠えの内容は<a href="#suspect">不審者</a>や<a href="#cute_wolf">萌狼</a>と同じ。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#black_fox">黒狐</a></td>
    <td><a href="#fox_side">妖狐</td>
    <td>人狼<br>(呪殺無し)</td>
    <td>妖狐</td>
    <td class="ability">占い結果が「人狼」、霊能結果が「妖狐」と判定される妖狐。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#scarlet_wolf">紅狼</a></td>
    <td><a href="#wolf_side">人狼</td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability"><a href="#fox_side">妖狐陣営</a>から<a href="#child_fox">子狐</a>に見える人狼。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#dummy_chiroptera">夢求愛者</a></td>
    <td><a href="#chiroptera_side">蝙蝠</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">本人には<a href="#self_cupid">求愛者</a>と表示されている蝙蝠。<br>
      矢を撃つことはできるが恋人にはならず、<a href="#mind_receiver">受信者</a>もつかない。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="#crisis_priest" name="140beta2">預言者</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">今が「人外勝利前日」が分かる特殊な司祭 (表示は「村人」)。<br>
      「人外勝利前日である」と判定された場合は、どの陣営が有利なのか<br>
      メッセージが表示される。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="#revive_priest">天人</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">二日目の朝にいきなり死亡して、「人外勝利前日」「5日目以降」<br>
      「人口半減」「LW」のどれかを満たすと生き返る特殊な司祭。<br>
      恋人になると能力を失う。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="#evoke_scanner">イタコ</a></td>
    <td><a href="#human_side">村人</td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人を<a href="#mind_evoke">口寄せ</a>にする。<br>
      投票結果が出るのは 2 日目以降。<br>
      自分の遺言欄に何が表示されていても遺言は残らない。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="#revive_cat">仙狸</a></td>
    <td><a href="#human_side">村人</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒能力を失った代わりに高い蘇生能力を持った猫又の上位種。<br>
    蘇生成功率は 80% で蘇生に成功するたびに成功率が 1/4 になる。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="#revive_fox">仙狐</a></td>
    <td><a href="#fox_side">妖狐</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">蘇生能力を持った妖狐。成功率は 100% だが、一度成功すると能力を失う。<br>
      人狼に襲撃されても死なないが、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
</table>

<table>
<caption><a name="sub_role">新サブ役職早見表</a></caption>
  <tr>
    <th>名称</th>
    <th>表示</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="#strong_voice" name="sub_140alpha3">大声</a></td>
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
    <td><a href="#no_last_words" name="sub_140alpha9">筆不精</a></td>
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
    <td><a href="#liar" name="sub_140alpha11">狼少年</a></td>
    <td>○</td>
    <td class="ability">発言時に「人⇔狼」等が入れ替わる (たまに変換されないこともある)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="#random_voice" name="sub_140alpha14">臆病者</a></td>
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
    <td><a href="#random_luck" name="sub_140alpha15">波乱万丈</a></td>
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
    <td><a href="#speaker" name="sub_140alpha17">スピーカー</a></td>
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
    <td class="ability">発言に虹の色が含まれていたら虹の順番に合わせて入れ替えられてしまいます。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#panelist">解答者</a></td>
    <td>○</td>
    <td class="ability">投票数が 0 になり、出題者に投票したらショック死する。<br>
    クイズ村専用。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="#weekly" name="sub_140alpha19">七曜迷彩</a></td>
    <td>○</td>
    <td class="ability">発言に曜日が含まれていたら曜日の順番に合わせて入れ替えられてしまいます。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="#mind_read" name="sub_140alpha21">サトラレ</a></td>
    <td>○</td>
    <td class="ability"><a href="#mind_scanner">さとり</a>に夜の発言が見られてしまいます。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="#mind_open" name="sub_140alpha22">公開者</a></td>
    <td>○</td>
    <td class="ability">夜の発言が参加者全員に見られてしまいます。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="#mind_receiver">受信者</a></td>
    <td>○</td>
    <td class="ability">特定の人の夜の発言が見えるようになります。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="#celibacy">独身貴族</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に恋人から一票でも貰うとショック死する。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="#inside_voice" name="sub_140alpha23">内弁慶</a></td>
    <td>○</td>
    <td class="ability">昼は小声、夜は大声になります。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#outside_voice">外弁慶</a></td>
    <td>○</td>
    <td class="ability">昼は大声、夜は小声になります。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#mower">草刈り</a></td>
    <td>○</td>
    <td class="ability">発言から「w」が削られます。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#grassy">草原迷彩</a></td>
    <td>○</td>
    <td class="ability">発言の一文字毎に「w」が付け加えられます。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#side_reverse">鏡面迷彩</a></td>
    <td>○</td>
    <td class="ability">発言の文字の並びが一行単位で逆になります。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#line_reverse">天地迷彩</a></td>
    <td>○</td>
    <td class="ability">発言の行の並びの上下が入れ替わります。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#mind_friend">共鳴者</a></td>
    <td>○</td>
    <td class="ability">特定の人と夜に会話できるようになります。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="#mind_evoke" name="sub_140beta2">口寄せ</a></td>
    <td>○</td>
    <td class="ability">死後に特定の人の遺言窓にメッセージを送れます。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
</table>

<pre>
◎<a name="human_side">村人陣営</a>
◆占い師系
○基本ルール
占い能力は人狼の襲撃や暗殺などで事前に死んでいたら無効になります。
また、占い対象先が同様の理由で事前に死んでいたら対象の能力は無効になります。

例)
1. 人狼に噛まれた占い師が妖狐を占っていても無効
2. <a href="#assassin">暗殺者</a>に殺された<a href="#cursed_wolf">呪狼</a>を占い師が占っても呪返しは受けない

○<a name="soul_mage">魂の占い師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
占った人の役職が分かる上位占い師。
妖狐を占っても呪殺できないが、占い妨害や呪返しは受けるので注意。

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
白狼だろうが子狐だろうが分かってしまうので村側最強クラスですが、
その分狙われやすいでしょう。

[作成者からのアドバイス]
状況によっては、始めは普通の占い師として振舞うのも手。
また、魂の占い師を CO していても村側役職 (狩人や<a href="#assassin">暗殺者</a>など) を素直に言うと
人狼に狙われるので適度に結果を騙り、相手と合わせるのも手。
(例えば、狩人→<a href="#poison_guard">騎士</a>、埋毒者→狩人、<a href="#dummy_poison">夢毒者</a>→埋毒者)
逆に言うと、騙る場合は「村側陣営」などとごまかす事で破綻しにくくなる。


○<a name="dummy_mage">夢見人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α14〜]
本人には「占い師」と表示されており、占い行動もできるが結果は逆になる。
呪殺できない代わりに呪返しも受けない。
<a href="#jammer_mad">月兎</a>による占い妨害の影響を受けない。

※Ver. 1.4.0 α18〜
占い結果がランダムから「村人」⇔「人狼」反転に変わりました。
確定白(例えば共有者)を占って人狼判定が出たら本人視点夢見人確定です。
また、<a href="#psycho_mage">精神鑑定士</a>から「嘘つき」判定を受けても同様です。

[作成者からのコメント]
新役職提案スレッド＠やる夫(最下参照) の 17 が原型です。
完全ランダムでは占い結果が全く役に立たなくなるので
白黒反転に変更しました。


○<a name="psycho_mage">精神鑑定士</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
村人の心理状態を調べて「嘘つき」を探し出す特殊な占い師。
狂人・夢系・<a href="#suspect">不審者</a>・<a href="#unconscious">無意識</a>を占うと「嘘をついている」と判定される。
それ以外は「正常である」と判定される。
呪殺できないので呪返しも受けない。

[作成者からのコメント]
対狂人専門の占い師です。本人視点と実際の役職が違うタイプ
(夢系・不審者・無意識)にも対応しています。
精神鑑定士を真と見るなら占われた人視点の役職がほぼ確定します。
人狼や妖狐の騙りは見抜けないので注意してください。


○<a name="sex_mage">ひよこ鑑定士</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α19〜]
村人の性別を判別する特殊な占い師。
呪殺できないので呪返しも受けないが、<a href="#jammer_mad">月兎</a>の影響は受ける。

※Ver. 1.4.0 α21〜
<a href="#chiroptera_side">蝙蝠</a>を占った場合は「蝙蝠」と判定される。

[作成者からのコメント]
<a href="#psycho_mage">精神鑑定士</a>の話を出したときの流石鯖の管理人さんのコメントが
きっかけで生まれた役職です。
出現当初は特にメリットがありませんでしたが
Ver. 1.4.0 α21 から登場の蝙蝠陣営の鑑定能力を持ちました。


○<a name="voodoo_killer">陰陽師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α20〜]
対呪い専門の特殊な占い師。
占った人が呪い持ちや<a href="#possessed_wolf">憑狼</a>の場合は呪殺し(死亡メッセージは呪返しと同じ)、
誰かに呪いをかけられていた場合は解呪(呪返しが発動しない)する。
呪殺か解呪が成功した場合のみ、次の日に専用のシステムメッセージが表示される。

[作成者からのコメント]
呪い系統の対抗役職です。
積極的に呪い持ち(<a href="#cursed_wolf">呪狼</a>、<a href="#cursed_fox">天狐</a>、<a href="#cursed_chiroptera">呪蝙蝠</a>)を探しに行く場合は
普通の占い師と同じ感覚でいいですが、呪術系能力者(<a href="#voodoo_mad">呪術師</a>、<a href="#voodoo_fox">九尾</a>)による
占い師の呪返しを防ぐのが狙いなら、同時に同じ人を
占う必要があるので動き方が難しくなります。
そもそも呪い系がレアなので役に立つのか分かりませんが……



◆霊能者系
○基本ルール
村人が吊らないといけない人外なのに、占い師では村人と出るか呪返しを受ける
役職 (例：<a href="#boss_wolf">白狼</a>、<a href="#cursed_fox">天狐</a>、<a href="#child_fox">子狐</a>) は霊能で分かります。
詳細は個々の役職の霊能結果を確認してください。

○<a name="soul_necromancer">雲外鏡</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
「村人」と「人狼」だけではなく、役職が分かる上位霊能者。

[作成者からのコメント]
<a href="#soul_mage">魂の占い師</a>の霊能者バージョンです。
占いと違ってメリットが少ないので後回しにしていましたが<a href="#dummy_necromancer">夢枕人</a>とセットで出すことで
こっちは本人視点、判定に偽りが絶対に無いというアドバンテージが与えられます。
しかし、「死人に口無し」故に魂の占い師よりもはるかに騙りやすいですね。


○<a name="dummy_necromancer">夢枕人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「霊能者」と表示されており、霊能判定も表示されるが結果は逆になる。
特殊狼(<a href="#boss_wolf">白狼</a>、<a href="#possessed_wolf">憑狼</a>など)や、特殊妖狐(<a href="#cursed_fox">天狐</a>、<a href="#child_fox">子狐</a>など)は正しい結果が表示される。
<a href="#corpse_courier_mad">火車</a>の能力の影響を受けない。

※Ver. 1.4.0 α18〜
霊能結果がランダムから「村人」⇔「人狼」反転に変わりました。
<a href="#psycho_mage">精神鑑定士</a>から「嘘つき」判定を受けたら本人視点夢枕人確定です。

※Ver. 1.4.0 α21〜
<a href="#corpse_courier_mad">火車</a>の能力の影響を受けません。

[作成者からのコメント]
<a href="#dummy_mage">夢見人</a>の霊能者バージョンです。
完全ランダムでは霊能結果が全く役に立たなくなるので
白黒反転に変更しました。


○<a name="medium">巫女</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
突然死した人の所属陣営が分かる特殊な霊能者。
闇鍋モードで登場する「ショック死」する人たちの情報を取るのが主な仕事だが
普通の霊能と違うので注意。

所属陣営とは、勝敗が決まったときの陣営なので
人狼系と狂人系は「人狼」
妖狐系は「妖狐」
キューピッド系は「恋人」
出題者は「出題者」
<a href="#chiroptera_side">蝙蝠系</a>は「蝙蝠」
それ以外は「村人」と出る

また、メイン役職のみが判定の対象 (サブ役職は分からない)。
つまり、恋人はサブ役職なので「恋人」と判定されるのはキューピッド系のみ。

※Ver. 1.4.0 α8〜
通常闇鍋モードではキューピッドが出現している場合は確実に出現します。
(ただし、巫女が出現してもキューピッドが出現しているとは限りません)

※Ver. 1.4.0 α9〜
恋人後追いにも対応。(後追いした恋人のみ、元の所属陣営が分かる)

[作成者からのコメント]
式神研のオリジナル役職です。
闇鍋モードで大量の突然死が出ることになったので作ってみましたが
霊能者より地味な存在ですね。騙るのも容易なのでなかなか報われないかもしれません。


○<a name="yama_necromancer">閻魔</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α20〜]
前日の死者の死因が分かる特殊な霊能者。
死因は画面の下に表示される「〜は無残な〜」の下の行に
「(〜は人狼に襲撃されたようです)」等と表示される。

[作成者からのコメント]
死因が多岐にわたる闇鍋用の特殊霊能者です。
死因が分かるだけなので昼の毒巻き込まれや暗殺された人等、
死者の役職が分からない可能性もある点に注意してください。



◆司祭系
○<a name="priest">司祭</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α24〜]
一定日数ごとに現在、生存している村人陣営の人数が分かる闇鍋用役職。

1. 現在は 4日目以降、1日おき (4 → 6 → 8 →...)。
2. 村人陣営の判定法則は<a href="#medium">巫女</a>と同じ。
3. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は死亡します)
4. <a href="#poison_guard">騎士</a>は通常通り護衛可能です

[作成者からのコメント]
他の国に実在する役職で、新役職考案スレ (最下参照) の 72 が原型です。
オリジナルは生存している役職の内訳が完全に分かりますが
式神研バージョンはかなり情報が絞られています。


○<a name="crisis_priest">預言者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β2〜]
今が「人外勝利前日」かどうかが分かる特殊な司祭 (表示は「村人」)。
「人外勝利前日である」と判定された場合は、どの陣営が有利なのかメッセージが表示される。

+ 人外勝利前日判定ルール
1. 生存者 - (人狼 + 妖狐) <= 人狼 + 2
その日の吊りが人狼以外 + 夜に人狼の噛みが成立すると人狼勝利となります。
メッセージは「人狼が勝利目前」です。

2. 「条件1 が成立している」または「人狼が残り一人」 + 妖狐 / 恋人が生存している
妖狐が生存していれば「妖狐が勝利目前」、
恋人が生存していれば「恋人が勝利目前」と判定されます

3. 生存者 >= 恋人 + 2
生存者が全員恋人に恋人勝利となります。
メッセージは「恋人が勝利目前」です。

[作成者からのコメント]
村の危機を告げる特殊な司祭です。
いわゆる「鉄火場」は狂人や蝙蝠の存在 + 恋人の元の役職によって
機械的な判定ができないので判定条件を「システム的な勝敗決定前日」としました。


○<a name="revive_priest">天人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β2〜]
二日目の朝にいきなり死亡して、特定の条件を満たすと生き返る特殊な司祭。
「霊界で配役を公開しない」オプションが有効になっていないと死亡も蘇生もしません

+ 蘇生条件
1. 「人外勝利前日」である (<a href="#crisis_priest">預言者</a>参照)。
2. 5 日目である
3. 村の人口が半分以下になった
4. 生存している人狼が一人になった

+ 詳細な仕様
1. 2 日目の朝の死亡メッセージは通常通りで、死因は「天に帰った」です。
2. 一度蘇生すると能力を失います (「能力を失った」というメッセージが追加されます)
3. 恋人になると能力を失います (二日目朝の死亡も起こりません)
4. <a href="#mania">神話マニア</a>がコピーした場合は 2日目朝の死亡処理が起こりません。
5. 2 日目朝以降に死んでも蘇生判定を満たせば生き返ります
6. 5 日目になると能力を失います
7. <a href="#poison_cat">猫又</a>系の蘇生対象外です (選ばれた場合は失敗します)。
8. <a href="#possessed_wolf">憑狼</a>の憑依対象外です (襲撃された場合は普通に殺されます)。

[作成者からのコメント]
新役職考案スレ (最下参照) の 54 が原型です。
復活した天人は恋人でない事が保証されるので非常に頼りになります。


◆狩人系
○<a name="about_guard_hunt">狩人系の狩りについて</a>
1. 狩り能力があるのは狩人、<a href="#poison_guard">騎士</a>です
2. <a href="#dream_eater_mad">獏</a>と<a href="#dummy_guard">夢守人</a>の関係は<a href="#dream_eater_mad">獏</a>の項目を参照してください
3. 狩り対象は特殊狂人、特殊妖狐、特殊蝙蝠です
3-1. 特殊狂人 (<a href="#jammer_mad">月兎</a>、<a href="#voodoo_mad">呪術師</a>、<a href="#dream_eater_mad">獏</a>、<a href="#corpse_courier_mad">火車</a>、<a href="#trap_mad">罠師</a>)
3-2. 特殊妖狐 (<a href="#cursed_fox">天狐</a>、<a href="#voodoo_fox">九尾</a>、<a href="#revive_fox">仙狐</a>)
3-3. 特殊蝙蝠 (<a href="#poison_chiroptera">毒蝙蝠</a>、<a href="#cursed_chiroptera">呪蝙蝠</a>)

○<a name="poison_guard">騎士</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
噛まれた時のみ毒が発動する上位狩人。
通常の狩人では護衛できない役職 (<a href="#reporter">ブン屋</a>、<a href="#assassin">暗殺者</a>、<a href="#priest">司祭</a>) も護衛可能。
狩り能力は<a href="#about_guard_hunt">狩人系の狩りについて</a>を参照。

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
夜に投票(尾行)した人が人狼に襲撃された場合に、誰が襲撃したか分かる特殊な狩人。
現在の仕様は以下の通り。

1. 人狼や妖狐を尾行したら殺されます (死亡メッセージは人狼の襲撃と同じです)
2. 遺言を残せません
3. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は死亡します)
4. <a href="#poison_guard">騎士</a>は通常通り護衛可能です
5. 尾行対象者が狩人に護衛されていた場合は何も出ません
6. 人狼が人外(狐や<a href="#silver_wolf">銀狼</a>など)を襲撃して失敗した場合は尾行成功扱いとなります (殺されません)
   (尾行成功メッセージ＆対象が死んでいない＝狼が噛めない人外を噛んだ)

※通常闇鍋モードの出現法則は調整中です。

[作成者からのコメント]
新役職考案スレ (最下参照) の 5 が原型です。
活躍するのは非常に難しいですが、成功したときには大きなリターンがあります。
狐噛みの現場をスクープするブン屋を是非見てみたいものです。


○<a name="dummy_guard">夢守人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「狩人」と表示されており、護衛行動を取ることができる。
必ず護衛成功メッセージが表示されるが、表示されるだけで誰も護衛していない。

※Ver. 1.4.0 α21〜
<a href="#dream_eater_mad">獏</a>には圧倒的なアドバンテージを持っており、何らかの形で遭遇すると
一方的に狩ることができる(逆に言うと夢守人が狩れるのは獏だけ)。

[作成者からのコメント]
<a href="#dummy_mage">夢見人</a>の狩人バージョンです。
常に護衛成功メッセージが出るので一度でも失敗したら夢守人で無い事を確認できます。
みなさん一度くらいは全て護衛成功して勝利してみたいと思った事、ありませんか？


○<a name="anti_voodoo">厄神</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α20〜]
護衛した人の厄 (占い妨害、呪返し、憑依) を祓う特殊な狩人。
成功した場合は次の日に専用のシステムメッセージが表示される。

※Ver. 1.4.0 α24〜
憑依中の<a href="#possessed">憑狼</a>に対しては圧倒的なアドバンテージを持っており、
直接護衛するか、襲撃されると憑依状態を解くことができる。

[作成者からのコメント]
対占い妨害・呪い専門の狩人です。「やくじん」と読みます。
新役職考案スレ (最下参照) の 43 が原型です。
厄神の護衛を受けることで<a href="#cursed_wolf">呪狼</a>に狼判定を出したり、
<a href="#cursed_fox">天狐</a>を呪殺することが可能になります。
通常の狩人が狂人・妖狐でも護衛成功してしまうのと同様に
狂人や妖狐にも占い妨害や呪返しを受ける役職がいるので、
「厄払い成功＝対象者は村陣営」とは限らない点に注意してください。



◆共有者系
○<a name="dummy_common">夢共有者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「『相方が身代わり君』の共有者」と表示されている村人。
が、夜に発言しても「ひそひそ声」にはならないし、本物の共有者の声も聞こえない。

[作成者からのコメント]
<a href="#dummy_mage">夢見人</a>の共有者バージョンです。
「ひそひそ声」が発生しないので真共有者にはなれません。
(闇鍋モードであっても「ひそひそ声」は消しません。仕様です)
証明手段が無いので容易に騙れますね。きっと真でも吊られることでしょう。



◆埋毒者系
○<a name="strong_poison">強毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
吊られた時に人外(狼と狐)のみを巻き込む上位埋毒者です。
ただし、本人には「埋毒者」と表示されているので自覚はありません。
埋毒者の巻き込み対象設定 (管理者に確認を取ってください) が投票者ランダムだった場合、
人外が投票していなければ毒は不発となります。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
状況にもよりますが、<a href="#soul_mage">魂の占い師</a>に鑑定してもらったら即吊ってもらうと強いですね。
投票者ランダムの設定であれば、その時の投票結果は重要な推理材料にもなります。


○<a name="incubate_poison">潜毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
一定期間後 (現在は 5 日目以降) に毒を持つ村人です。
毒を持ったら本人に追加のシステムメッセージが表示されます。
毒能力は<a href="#strong_poison">強毒者</a>相当です。

※Ver. 1.4.0 α20〜
毒能力を埋毒者相当から強毒者相当に変更しました。

[作成者からのコメント]
ウミガメ人狼のとあるプレイヤーさんがモデルです。
いかに毒を持つまで時間を稼ぐかがポイントです
α20から毒能力が強化されたので毒を持ったら即吊ってもらうのも手。


○<a name="dummy_poison">夢毒者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
本人には「埋毒者」と表示されている村人。

[作成者からのコメント]
<a href="#dummy_mage">夢見人</a>の埋毒者バージョンです。
毒は持っていませんが、身代わり君がこれになることはありません。
偽者ではありますがどちらかと言うと人狼が不利になる役職ですね。
夢毒者である事に賭けて真埋毒者を噛みに行く狼が出るかもしれません。


◆猫又系
※Ver. 1.4.0 β2〜
猫又を埋毒者系から猫又系に独立させました。

○<a name="about_revive">蘇生能力者の基本ルール</a>
1. 「霊界で配役を公開しない」オプションが有効になっていないと蘇生行動はできません
2. 2 日目の夜以降から、死んでいる人を一人選んで蘇生を試みます
3. 身代わり君と蘇生能力者 (猫又系と<a href="#revive_priest">天人</a>) は蘇生できません
4. 「蘇生を行わない」を選ぶこともできます
5. 蘇生成功率のうち、1/5 は指定した人以外が対象になる「誤爆蘇生」となります
   例) 25% : 成功 : 20% / 誤爆 :  5%
6. 対象者が蘇生能力者だったら確実に失敗します
7. 対象者が恋人だったら確実に失敗します (蘇生後、即自殺から仕様変更しました)


○<a name="poison_cat">猫又</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
蘇生能力を持った埋毒者。蘇生成功率は 25%。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。

※Ver. 1.4.0 α19〜
猫又が蘇生する事はありません。
猫又が蘇生対象者に選ばれた場合は失敗扱いになります。

[作成者からのコメント]
他の国に実在する役職です。
「霊界で配役を公開しない」オプションを有効にしておかないと
ただの埋毒者になる点に注意してください。


○<a name="revive_cat">仙狸</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β2〜]
毒能力を失った代わりに高い蘇生能力を持った<a href="#poison_cat">猫又</a>の上位種。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。
蘇生成功率は 80% で、成功するたびに成功率が 1/4 になる。
80% → 20% → 5% → 2% → 1% (以後は 1% で固定)

[作成者からのコメント]
仙狸 (センリ) とは、中国の猫の妖怪です (「狸」は山猫の意)。
裏世界鯖のプレイヤーさんのコメントを参考に同じ猫の妖怪である
<a href="#poison_cat">猫又</a>の上位種として実装してみました。


◆薬師系
○<a name="pharmacist">薬師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α12〜]
毒持ちを吊ったときに、薬師が投票していたら解毒(毒が発動しない)します。
Ver. 1.4.0 α23 からは、昼に投票した人が毒を持っているか翌朝に分かります。
朝に出るメッセージは以下の 5 種類。

1. 毒を持っていない (<a href="#dummy_poison">夢毒者</a>や発現前の<a href="#incubate_poison">潜毒者</a>もこれ)
2. 毒を持っている
3. 強い毒を持っている (<a href="#strong_poison">強毒者</a>と発現後の<a href="#incubate_poison">潜毒者</a>)
4. 限定的な毒を持っている (現在は<a href="#poison_guard">騎士</a>のみ)
5. 解毒に成功した (この場合は詳細な毒の種類は分からない)

※Ver. 1.4.0 α23〜
解毒成功だけでなく、前日に投票した人の詳細な毒能力が分かります

[作成者からのコメント]
新役職考案スレ (最下参照) の 24 が原型です。「くすし」と読みます。
<a href="#poison_wolf">毒狼</a>の対抗役職です。
埋毒者系に対しても効果を発揮します。


◆暗殺者系
○<a name="assassin">暗殺者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
夜に村人一人を選んで暗殺できます。現在の仕様は以下です。

1. 暗殺可能な役職に制限はありません (人狼・妖狐でも暗殺可能)
2. 暗殺された人の死亡メッセージは人狼の襲撃と同じです
3. 人狼に襲撃されたり、<a href="#trap_mad">罠師</a>の罠にかかると暗殺は無効です
4. 「暗殺する / しない」を必ず投票する必要があります
5. 狩人の護衛を受けられません (狩人には護衛成功と出ますが、本人は死亡します)
6. <a href="#poison_guard">騎士</a>は通常通り護衛可能です
7. 暗殺者がお互いを襲撃した場合は相打ちになります
8. 暗殺者に暗殺された占い師の呪殺、<a href="#poison_cat">猫又</a>の蘇生は無効になります
9. 暗殺者に暗殺されても狩人系の護衛判定は有効です

[作成者からのコメント]
村側陣営の最終兵器とも呼べる存在ですね。
判定順とかが複雑なので色々調整が入るかもしれません。
新役職考案スレ (最下参照) の 8 が原型です。



◆さとり系
○<a name="mind_scanner">さとり</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
初日の夜に誰か一人を選んでその人の夜の発言を見ることができます。
結果が出るのは 2 日目以降で、相手にはサブ役職「<a href="#mind_read">サトラレ</a>」がつきます。
<a href="#unconscious">無意識</a>と死者の発言を見ることはできません。
自分が死んだら能力は無効になります。
人狼の遠吠えが一切見えません。

※Ver. 1.4.0 α23〜
人狼の遠吠えが一切見えません。

[作成者からのコメント]
新役職考案スレ (最下参照) の 4 が原型です。
相手も見られていることだけは自覚できるので
どこまで推理に活かせるのかは未知数ですが……
遠吠えの有無で相手が人狼かどうかの判断できてしまうので
Ver. 1.4.0 α23 からは常時遠吠えを見えなくしました。


○<a name="evoke_scanner">イタコ</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 β2〜]
初日の夜に誰か一人を選んでその人を<a href="#mind_evoke">口寄せ</a>にします。

1. 投票結果が出るのは 2 日目以降です。
2. 口寄せ先が死亡したら霊界から遺言窓を介してメッセージを受け取れます。
3. 自分では遺言欄を変更できません。
4. 自分の遺言欄に何が表示されていても遺言は残りません。

[作成者からのコメント]
霊界オフモードの有効活用をできる役職を作ろうと思い、
こういう実装にしてみました。


◆橋姫系
○<a name="jealousy">橋姫</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α22〜]
昼の投票時に、同一キューピッドの恋人が揃って自分に投票したら
投票した恋人をショック死させる。詳細は以下。

1. 自分が吊られたら無効
吊られない範囲で恋人の票を集める必要があります。
対恋人で人柱になっても無意味です。

2. 他のキューピッドの恋人たちに投票されても無効
複数のキューピッドに矢を打たれて繋がっている恋人に投票されても無効です。

3. 処理のタイミングはショック死処理の直前
つまり、投票結果が再投票になっても有効です。
また、本人が<a href="#celibacy">独身貴族</a>であっても有効です。
(結果的には相討ちになる)

4. カップルが別々の橋姫に投票しても無効
他の橋姫に対する投票は参照していません。

[作成者からのコメント]
対恋人役職です。
新役職考案スレ (最下参照) の 2、21、44 を参考にしています。
別れさせる処理が難しいのでこういう実装になりました。
村陣営だと恋人がかなり厳しい可能性もあるので所属陣営が変わる可能性もあります。


◆神話マニア系
○<a name="mania">神話マニア</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α11〜]
初日の夜に誰か一人を選んでその人の役職をコピーします。
入れ替わるのは2日目の朝です。
神話マニアか<a href="#unknown_mania">鵺</a>を選んだ場合は村人になります。
陣営や占い結果は全てコピー先の役職に入れ替わります。
通常闇鍋モードでは16人以上から出現します。

[作成者からのコメント]
カード人狼にある役職です。元と違い、占いや狼以外の役職もコピーします。
CO するべきかどうかは、コピーした役職次第です。


○<a name="unknown_mania">鵺</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α23〜]
初日の夜に誰か一人を選んでその人と同じ所属陣営になります。
結果が表示されるのは 2 日目の朝で、自分と投票先に<a href="#mind_friend">共鳴者</a>がつきます。
入れ替わるのは2日目の朝です。
生存カウントは常に村人なので、実質は所属陣営不明の狂人相当です。

<a href="#mania">神話マニア</a>と違い、コピー結果が出ないのでコピー先に聞かないと
自分の所属陣営が分かりません。

所属陣営の判定例
1. 鵺 → 村人 (村人陣営)
擬似共有者となります

2. 鵺 → 人狼 (人狼陣営)
投票先とだけ会話できる<a href="#whisper_mad">囁き狂人</a>相当です。

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

[作成者からのコメント]
薔薇GMに、「初心者の指南用に使える役職」を要請されてこういう実装にしてみました。
鵺が初心者をコピーして指南するイメージですね。

もしも、教えてもらう前にコピー先が死んでしまったら自分の所属陣営は
「正体不明」になる事になります。とっても理不尽ですね。



◆村人系
○<a name="suspect">不審者</a> (占い結果：人狼、霊能結果：村人) [Ver. 1.4.0 α9〜]
不審なあまり、占い師に人狼と判定されてしまう村人。
本人には一切自覚がない (村人と表示されている) ので偽黒を貰った様にしか感じない。
ライン推理を狂わせるとんでもない存在。

※Ver. 1.4.0 α16〜
低確率で発言が遠吠えに入れ替わってしまう (<a href="#cute_wolf">萌狼</a>と同じ)。

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
確定白で長期間放置されると<a href="#boss_wolf">白狼</a>扱いされるかも。



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
吊られた時に巻き込む対象の決定時に人狼が除かれるため
投票者ランダムの場合は不発となるケースがある。


[作成者からのコメント]
新役職考案スレ (最下参照) の 31 が原型です。
毒狼CO した人を吊って誰も巻き込まれなかった場合は以下のケースがありえます。
1. <a href="#pharmacist">薬師</a>が投票していた
2. 投票者ランダムの設定で、投票者が全員狼だった
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
Ver. 1.4.0 α24 からは毒能力者を襲撃した場合はサーバ設定や能力失効の有無に
関わらず毒の対象が襲撃者に固定されます。

※Ver. 1.4.0 α24〜
襲撃先が毒能力者で、投票者が抗毒狼だった場合はサーバ設定に関わらず
毒の対象者が投票した抗毒狼に固定されます。
ただし、能力を失効しても固定化処理は有効です。
つまり、埋毒者を意図的に襲撃して毒を無効化したり、
能力失効後にわざと毒に中りにいく事が可能になります。

[作成者からのコメント]
新役職考案スレ (最下参照) の 25 を参考にしています。
現時点でほぼ無敵の能力を誇る<a href="#poison_guard">騎士</a>への対抗策として作成しました。
安易に CO する騎士・埋毒者を葬ってやりましょう！

○<a name="cursed_wolf">呪狼</a> (占い結果：人狼(呪返し)、霊能結果：呪狼) [Ver. 1.4.0 α17〜]
占われたら占ってきた占い師を呪い殺す人狼です。
呪殺された占い師には襲撃されたときと同じ死亡メッセージが出ます。

※Ver. 1.4.0 β3〜
霊能結果を「人狼」から「呪狼」に変更しました (霊能の基本ルール対応抜け)

[作成者からのコメント]
他の国に実在する役職です。
新役職考案スレ (最下参照) の 69 を参考にしています。
<a href="#soul_mage">魂の占い師</a>や<a href="#child_fox">子狐</a>も呪い殺せます。
占い師側の対策は、遺言に占い先をきちんと書いておく事です。
死体の数や状況にもよりますが、残った村人がきっと仇を討ってくれるでしょう。


○<a name="silver_wolf">銀狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α21〜]
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


○<a name="wise_wolf">賢狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α24〜]
妖狐の念話が共有者のひそひそ声に変換されて聞こえる人狼。
結果として、念話ができる妖狐が生存していることだけが分かる。
本物の共有者のひそひそ声と混ざって表示されるので本人には区別できない。

[作成者からのコメント]
名称は他の国に実在しますが、仕様はオリジナルです。
狼サイドから妖狐の生存がわかります。
<a href="#silver_fox">銀狐</a>や<a href="#child_fox">子狐</a>など、念話できない妖狐の
生存は感知できないので注意してください。


○<a name="possessed_wolf">憑狼</a> (占い結果：人狼、霊能結果：憑狼) [Ver. 1.4.0 α24〜]
噛んだ人を乗っ取る人狼。
乗っ取るのはアイコンと恋人を除くサブ役職全て。
身代わり君、<a href="#revive_priest">天人</a>、妖狐は乗っ取れません。

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

例3-3) C[<a href="#psycho_mage">精神鑑定士</a>] → B[狂人](A[憑狼])
鑑定結果：B は「正常」でした。

例3-4) C[<a href="#pharmacist">薬師</a>] → B[埋毒者](A[憑狼])
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
占い師と<a href="#soul_mage">魂の占い師</a>が憑狼を占った場合は、憑依がキャンセルされます。
<a href="#dummy_mage">夢見人</a>や<a href="#child_fox">子狐</a>が占ってもキャンセルされません。
<a href="#psycho_mage">精神鑑定士</a>や<a href="#sex_mage">ひよこ鑑定士</a>など、「村人 / 人狼」以外を判定するタイプの
占い系能力者が占ってもキャンセルされません。

例6-1) C[占い師] → A[憑狼] → B[村人]
占い結果：A は「人狼」でした。
死体：B が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は憑依処理がキャンセルされて A のまま。

例6-2) C[<a href="#sex_mage">ひよこ鑑定士</a>] → B[蝙蝠](A[憑狼]) → D[村人]
鑑定結果：B は「男性 / 女性」(A の性別) でした。
死体：B が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：D[村人](A[憑狼])


7. 対<a href="#voodoo_killer">陰陽師</a>
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


8. 対<a href="#anti_voodoo">厄神</a>
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


9. 対<a href="#reporter">ブン屋</a>
ブン屋が憑狼の憑依先を護衛すると、「生存者は死者に襲撃された」という
尾行結果が表示されることになるので本人視点で憑狼の位置が確定します。

例9-1) A[憑狼] → B[村人] ← C[ブン屋]
尾行結果：B は A に襲撃されました。
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])


10. 対<a href="#assassin">暗殺者</a>
直接憑狼を狙うことで憑依中でも殺すことができます。
また、自分と暗殺先が同時に生き残っていることはありえないので、
本人視点で憑狼の位置が確定します。

例10-1) C[暗殺者] → B[村人](A[憑狼]) → D[村人]
死体：B が無残な死体で発見されました (死因：「暗殺された」) (実際に死ぬのは A)
死体：D が無残な死体で発見されました (死因：「人狼に襲撃された」)

例10-2) A[憑狼] → B[村人] ← C[暗殺者]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
憑依：B[村人](A[憑狼])


11. 対<a href="#poison_cat">猫又</a>
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


12. 対<a href="#revive_priest">天人</a>
天人は憑依対象外なので、生存している天人は憑狼ではない事が保証されます。
また、霊界視点からは憑依者がはっきり分かるので蘇生した天人の情報は重要です。

例12-1) B[村人](A[憑狼]) → C[天人]
死体：C が無残な死体で発見されました (死因：「人狼に襲撃された」)
A は B に憑依したまま。


13. 対<a href="#evoke_scanner">イタコ</a>
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

例14-3) A[憑狼] → B[村人][恋人]、D[暗殺者] → C[<a href="#self_cupid">求愛者</a>][恋人]
死体：A が無残な死体で発見されました (死因：「誰かに憑依した」)
死体：C が無残な死体で発見されました (死因：「暗殺された」)
憑依：B[村人][恋人](A[憑狼])
C の恋人が生存している状態になるが、C が<a href="#dummy_chiroptera">夢求愛者</a>だった場合は
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


○<a name="scarlet_wolf">紅狼</a> (占い結果：人狼、霊能結果：人狼) [Ver. 1.4.0 α24〜]
<a href="#child_fox">妖狐陣営</a>から<a href="#child_fox">子狐</a>に見える人狼。
本物の子狐と混ざって表示されるため、妖狐側からは区別できない。

[作成者からのコメント]
<a href="#scarlet_fox">紅狐</a>の人狼バージョンです。
<a href="#child_fox">子狐</a>は念話できないので夜の会話で直接ばれることはありません。
占いを騙ることで妖狐側から子狐に見えるよう振舞う手もありますが
紅狼が妖狐を把握してるわけではないので「味方」に黒を出す可能性も……


◆狂人系
○基本ルール
騙りにリスクを与えるために、特殊能力を持った狂人は
<a href="#about_guard_hunt">狩人に護衛されると殺される</a>仕様となっています。

○<a name="fanatic_mad">狂信者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α3-7〜]
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
狼サイドの新兵器です。作った当初は<a href="#soul_mage">魂の占い師</a>や<a href="#poison_guard">騎士</a>と出現率のせいもあって
活躍できなかったようですが、本来はかなり強いはず。


○<a name="whisper_mad">囁き狂人</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α17〜]
人狼の夜の相談に参加できる上位狂人です。
人狼と違い、発言が遠吠えに変換されません。

[作成者からのコメント]
通称「C国狂人」、最強クラスの狂人です。「ささやき きょうじん」と読みます。
相談できるので完璧な連携が取れます。


○<a name="trap_mad">罠師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α18〜]
一度だけ夜に村人一人に罠を仕掛けることができる特殊な狂人。
罠を仕掛けられた人の元に訪れた人狼・狩人系・<a href="#assassin">暗殺者</a>は死亡する。
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


○<a name="jammer_mad">月兎 (邪魔狂人)</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α19〜]
夜に村人一人を選び、その人の占い行動を妨害する特殊な狂人。
具体的な仕様は以下。

1. 妨害可能な占い行動は<a href="#dummy_mage">夢見人</a>を除く占い師系と<a href="#child_fox">子狐</a>です
2. 妨害された占い師は「〜さんの占いに失敗しました」と出ます
3. 妨害が成功したかどうかは本人には分かりません
4. 呪われている役職を選んだ場合は呪返しを受けます
5. 狩人系に護衛されると殺されます
6. <a href="#voodoo_killer">陰陽師</a>は対象外です

※Ver. 1.4.0 α21〜
名称を邪魔狂人から月兎に変更しました。

[作成者からのコメント]
実在する役職ですね。狂人系の中でも上位に属します。
うかつに CO する事ができなくなるので占い師は非常につらくなりますね。


○<a name="voodoo_mad">呪術師</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α20〜]
夜に村人一人を選び、その人に呪いをかける特殊な狂人。
具体的な仕様は以下。

1. 呪われた人を占った占い師は呪返しを受けます
2. 呪われている役職を選んだ場合は本人が呪返しを受けます
3. 呪いをかけた人が他の人にも呪いをかけられていた場合は本人が呪返しを受けます
4. 狩人系に護衛されると殺されます

[作成者からのコメント]
対占い師系専門の<a href="#trap_mad">罠師</a>的存在です。
新役職考案スレ (最下参照) の 13 が原型です。
占い師の占い先を先読みして呪いをかけておくことで呪返しを狙うのが
基本的な立ち回りになると思います。


○<a name="corpse_courier_mad">火車</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
自分が投票して吊った人の霊能結果を隠蔽できる特殊な狂人。
投票先を吊ることさえ出来れば、投票した日にショック死しようと
その夜に何らかの形で死のうと効果が発動する。

霊能結果が隠蔽されると、霊能者と<a href="#soul_necromancer">雲外鏡</a>の霊能結果が
「〜さんの死体が盗まれた」という趣旨のメッセージになる。
<a href="#dummy_necromancer">夢枕人</a>は火車の影響は受けない。

狩人系に護衛されると殺される。

[作成者からのコメント]
<a href="#jammer_mad">月兎</a> (邪魔狂人) の霊能バージョンです。「かしゃ」と読みます。
新役職考案スレ (最下参照) の 48 が原型です。
火車の能力が発動しているのに霊能結果を出す人は
夢枕人か騙りになります。


○<a name="dream_eater_mad">獏</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
夜に投票した夢系能力者(<a href="#dummy_mage">夢見人</a>、<a href="#dummy_necromancer">夢枕人</a>、<a href="#dummy_common">夢共有者</a>、<a href="#dummy_poison">夢毒者</a>、<a href="#dummy_chiroptera">夢求愛者</a>) を
殺すことができる狂人。狩人に護衛されると殺される。

何らかの形で<a href="#dummy_guard">夢守人</a>に接触した場合は殺される。

夢守人に殺される条件は以下
1. 襲撃先が夢守人だった
2. 夢守人に自分が護衛された
3. 襲撃先が夢守人に護衛されていた

※Ver. 1.4.0 α23〜
初日の襲撃はできません (暗殺者系と挙動を揃えました)。

[作成者からのコメント]
対夢系能力者専門の<a href="#assassin">暗殺者</a>という位置づけです。「ばく」と読みます。
夢系は村陣営なので獏は狂人相当になります。


◎<a name="fox_side">妖狐陣営</a>
○仲間表示について
※Ver. 1.4.0 α19〜
全ての妖狐は<a href="#silver_fox">銀狐</a>以外の妖狐系・<a href="#child_fox">子狐</a>系が誰か分かります。
妖狐系と子狐系は別枠で表示されます (人狼サイドにおける人狼系と<a href="#whisper_mad">囁き狂人</a>みたいなものです)。
分けている基準は「念話ができるかどうか」です。

※Ver. 1.4.0 α24〜
子狐の枠に<a href="#scarlet_wolf">紅狼</a>も混ざって表示されます。

○夜の会話 (念話) について
※Ver. 1.4.0 α19〜
<a href="#silver_fox">銀狐</a>以外の妖狐系は夜に会話(念話)できます。
他人からはいっさい見えません。
子狐系は念話を見ることも参加することも出来ません。

※Ver. 1.4.0 α24〜
<a href="#wise_wolf">賢狼</a>には念話が共有者のひそひそ声に変換されて表示されます。

◆妖狐系
○<a name="cursed_fox">天狐</a> (占い結果：村人(呪返し)、霊能結果：妖狐) [Ver. 1.4.0 α17〜]
占われたら占った占い師を呪い殺す妖狐。
人狼に噛まれても死なないが、狩人に護衛されると殺される。

[作成者からのコメント]
<a href="#cursed_wolf">呪狼</a>の妖狐バージョンで、妖狐族最上位種です。
呪いに対抗できる役職が出現するまでは狐無双が見られそうですね。


○<a name="poison_fox">管狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α17〜]
毒を持った妖狐。毒能力は埋毒者と同じだが、対象から妖狐が除かれるため
投票者ランダムの場合は不発となるケースがある。

[作成者からのコメント]
新役職考案スレ (最下参照) の 110 が原型です。「くだぎつね」と読みます。
噛み無効の代わりに毒を持った妖狐です。仲間がいるときに真価を発揮します。


○<a name="white_fox">白狐</a> (占い結果：村人(呪殺無し)、霊能結果：妖狐) [Ver. 1.4.0 α17〜]
呪殺されない代わりに人狼に襲われると殺される。
<a href="#child_fox">子狐</a>との違いは占いができない代わりに他の妖狐と念話ができる事。

[作成者からのコメント]
<a href="#boss_wolf">白狼</a>の妖狐バージョンです。狼サイドからは村人と大差ないですが
村サイドにはかなりの脅威となるでしょう。


○<a name="voodoo_fox">九尾</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α20〜]
夜に村人一人を選び、その人に呪いをかける妖狐。
占われると呪殺される。
人狼に襲撃されても死なないが、狩人系に護衛されると殺される。
呪い能力の具体的な仕様は以下。

1. 呪われた人を占った占い師は呪返しを受けます
2. 呪われている役職を選んだ場合は本人が呪返しを受けます
3. 呪いをかけた人が他の人にも呪いをかけられていた場合は本人が呪返しを受けます

[作成者からのコメント]
<a href="#voodoo_mad">呪術師</a>の妖狐バージョンです。
新役職考案スレ (最下参照) の 58 が原型です。
噛み、占い耐性は通常の妖狐と同じですが
呪い能力を持った代わりに狩人にも弱くなっています。


○<a name="silver_fox">銀狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α20〜]
仲間が分からない妖狐。
(他の妖狐・<a href="#child_fox">子狐</a>からも仲間であると分からない)

[作成者からのコメント]
<a href="#silver_wolf">銀狼</a>の妖狐バージョンです。
元々妖狐は出現人数が少なめなので仲間が分からなくてもさほど影響は無いと思います。
占い騙りで他の妖狐系から偽黒を貰う可能性はありますが……


○<a name="scarlet_fox">紅狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α24〜]
人狼からは<a href="#unconscious">無意識</a>に見える妖狐。
本物の無意識と混ざって表示されるため、人狼側からは区別できない。

[作成者からのコメント]
新役職考案スレ (最下参照) の 383 が原型です。
流石系鯖で初日呪殺の代名詞になったことがある、真紅がモデルです。
始めは占い師に分かる狐にしましたがバランス取りが難しいので
人狼が無意識と誤認する妖狐にしました。
ただし、「無意識」が騙れば人狼視点ほぼ紅狐確定と見なされるでしょう。


○<a name="cute_fox">萌狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 α24〜]
低確率で発言が遠吠えに入れ替わってしまう妖狐。
遠吠えの内容は<a href="#suspect">不審者</a>や<a href="#cute_wolf">萌狼</a>と同じ。

[作成者からのコメント]
<a href="#cute_wolf">萌狼</a>の妖狐バージョンです。
<a href="#suspect">不審者</a>と違い、占われたら呪殺されますが、いずれにしても
「村人判定された人が遠吠えをした」場合、占った人は偽者です。


○<a name="black_fox">黒狐</a> (占い結果：人狼(呪殺無し)、霊能結果：妖狐) [Ver. 1.4.0 α24〜]
占い結果が人狼、霊能結果が妖狐と判定される妖狐。
人狼に襲撃されても死なない。

[作成者からのコメント]
呪殺されない代わりに人狼扱いされる妖狐です。
人狼側にとっては、黒狐自体の存在よりも、それを占った
占い師を狂人だと思って放置していたら真だった、なんて
事態になりかねないことの方が問題になりそうです。


○<a name="revive_fox">仙狐</a> (占い結果：村人(呪殺)、霊能結果：村人) [Ver. 1.4.0 β2〜]
蘇生能力を持った妖狐。
蘇生に関するルールは<a href="#about_revive">蘇生能力者の基本ルール</a>参照。
蘇生成功率は 100% で、一度成功すると能力を失う。

[作成者からのコメント]
<a href="#revive_cat">仙狸</a>の妖狐バージョンです。
確実に成功しますが、1/5 (20%) は誤爆になるので要注意です。
単純に味方の妖狐を復活させる以外の選択肢が一番有効になる
ケースがあるのが妖狐陣営の蘇生能力者のポイントです。


◆子狐系
○<a name="child_fox">子狐</a> (占い結果：村人(呪殺無し)、霊能結果：子狐) [Ver. 1.4.0 α3-7〜]
呪殺されない代わりに人狼に襲われると殺されます。
妖狐と念話できない代わりに占いができます。
判定結果は普通の占い師と同じで、呪殺は出来ませんが呪返しは受けます。
占いの成功率は 70% です。

※Ver. 1.4.0 α8〜
通常闇鍋モードでは20人未満では出現しません。
20人以上で参加人数と同じ割合で出現します。(20人なら20%、50人なら50%)
最大出現人数は1人です。
つまり、子狐視点、子狐を名乗る人は偽者です。
子狐が出現した場合は出現人数と同じだけ妖狐が減ります。

※Ver. 1.4.0 α17〜
占い能力を持ちました。

[作成者からのコメント]
他の国に実在する役職です。
妖狐陣営自体の出現数が少ないのでかなりのレア役職になりそうな予感。



◎<a name="lovers_side">恋人陣営</a>
○<a name="self_cupid">求愛者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
自分撃ち確定のキューピッド。

※Ver. 1.4.0 α22〜
矢を撃った相手に自分を対象にした「<a href="#mind_receiver">受信者</a>」がつきます。

[作成者からのコメント]
他の国に実在する役職です。
Ver. 1.4.0 α22 から矢を撃った相手に自分のメッセージを(一方的に)
送ることができるようになりました。
思う存分自分の想いを語ってください。


○<a name="mind_cupid">女神</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α23〜]
矢を撃った二人を<a href="#mind_friend">共鳴者</a>にするキューピッド。
他人撃ちの場合は、さらに自分が二人を対象にした<a href="#mind_receiver">受信者</a>になります。

[作成者からのコメント]
会話能力を持った恋人を作る上位キューピッドです。
自分撃ちの場合は<a href="#self_cupid">求愛者</a>の相互撃ちと同様の状態になります。
また、他人撃ちでも受信者になるので、矢を撃った二人の発言が
必ず見えることになります。


◎<a name="chiroptera_side">蝙蝠陣営</a>
○基本ルール
自分が生き残ったら勝利、死んだら敗北となる特殊な陣営。
他の陣営の勝敗と競合しないので、例えば、「村人陣営+生き残った蝙蝠が勝利」
という扱いになる。
<a href="#psycho_mage">精神鑑定士</a>の結果は「正常」、<a href="#sex_mage">ひよこ鑑定士</a>の結果は「蝙蝠」となる。

○<a name="chiroptera">蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
蝙蝠陣営の基本役職。能力は何も持っていない。
他の蝙蝠が誰か分からず、会話もできない。
また、自分以外の蝙蝠の生死と勝敗は無関係。

[作成者からのコメント]
他の国に実在する役職です。
他の陣営は如何に自陣の PP に引き込むかがポイントです。


○<a name="poison_chiroptera">毒蝙蝠</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α21〜]
毒を持った蝙蝠。
吊られた場合の毒の発動対象は人外(狼と狐)+蝙蝠です。
つまり、劣化<a href="#strong_poison">強毒者</a>相当となります。
<a href="#about_guard_hunt">狩人に護衛される</a>と殺されます。

※Ver. 1.4.0 α22〜
毒の発動対象を人外+蝙蝠に変更しました。

[作成者からのコメント]
死んだ時点で負けなので本人には何の利益もありませんが
信用を取れれば生き残れる可能性が大幅に上がるでしょう。
それ故にこれを騙る人がたくさん出てくると予想されますが……

Ver. 1.4.0 α22 から劣化強毒者相当に変更しました。
村視点、毒蝙蝠は吊ったほうがメリットが大きいので
CO するとむしろ吊られるリスクが高まります。


○<a name="cursed_chiroptera">呪蝙蝠</a> (占い結果：村人(呪返し)、霊能結果：村人) [Ver. 1.4.0 α21〜]
占われたら占った占い師を呪い殺す蝙蝠。
<a href="#voodoo_killer">陰陽師</a>に占われると殺される。
<a href="#about_guard_hunt">狩人に護衛される</a>と殺される。

[作成者からのコメント]
<a href="#cursed_wolf">呪狼</a>の蝙蝠バージョンです。
どちらかと言うと、これを騙る狼や狐が非常にやっかいですね。
素直に CO しても信用を取るのは難しいでしょう。


○<a name="dummy_chiroptera">夢求愛者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α24〜]
本人には<a href="#self_cupid">求愛者</a>と表示されている蝙蝠。
矢を撃つことはできるが恋人にはならず、矢を撃った先に<a href="#mind_receiver">受信者</a>もつかない。
<a href="#dream_eater_mad">獏</a>に襲撃されると殺される。

矢を撃ったはずの恋人が死んだのに自分が後追いしていない、
<a href="#psycho_mage">精神鑑定士</a>から「嘘つき」、<a href="#sex_mage">ひよこ鑑定士</a>から「蝙蝠」判定されるなどで
自分の正体を確認することができる。

[作成者からのコメント]
<a href="#self_cupid">求愛者</a>の夢バージョンです。
キューピッド相当にすると出現時点で勝ち目がないケースも
出てくるので扱いとしては特殊蝙蝠です。
<a href="#possessed_wolf">憑狼</a>が恋人を噛んでも破綻しない状況を作るために実装しました。



◎<a name="quze_side">出題者陣営</a>
○<a name="quiz">出題者</a> (占い結果：村人、霊能結果：村人) [Ver. 1.4.0 α2〜]
クイズ村の GM です。闇鍋村にも低確率で出現します。
勝利条件は「GM 以外が全滅していること」。
ルールの特殊なクイズ村以外ではまず勝ち目はありません。
引いたら諦めてください。

※Ver. 1.4.0 β2〜
毒吊りで巻き込まれる対象になりません。
例えば、出題者、埋毒者、毒狼の編成で毒能力者を吊った場合は確実に出題者が生き残ります。

[作成者からのコメント]
クイズ村以外では恋人になったほうがまだましという涙目すぎる存在ですが
闇鍋なので全役職を出します。一回くらいは奇跡的な勝利を見てみたいですね。


--<a name="sub_role">ここからサブ役職</a>--
※Ver. 1.4.0 α8 現在、恋人以外のサブ役職は重なりません。
　(大声と小心者を同時にＣＯする人は最低でもどちらかが嘘です)

◎投票系ショック死 (小心者系)
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
<a href="#perverseness">天邪鬼</a>の逆ですね。アイディア自体は早くからありましたが
なかなかいい名前が思いつかなかったに実装が遅れました。

○<a name="impatience">短気</a> [Ver. 1.4.0 α15〜]
決定者と同等の能力がある代わりに再投票になるとショック死します。

[作成者からのコメント]
新役職考案スレ (最下参照) の 80 が原型です。
自覚のある決定者です。
その分だけ判定の優先度が決定者より低めになっています。

○<a name="celibacy">独身貴族</a> [Ver. 1.4.0 α22〜]
昼の投票時に恋人から一票でも貰うとショック死します。

[作成者からのコメント]
<a href="#jealousy">橋姫</a>同様、対恋人用役職ですが、こっちはショック死系という事もあって
より理不尽な仕様となっています。

○<a name="panelist">解答者</a> [Ver. 1.4.0 α17〜]
投票数が 0 になり、<a href="#quiz">出題者</a>に投票したらショック死します。
クイズ村専用です (闇鍋モードにも出現しません)。


◎発言変換 (狼少年系)
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
発言に虹の色が含まれていたら虹の順番に合わせて入れ替えられてしまいます。
(例：赤→橙、橙→黄、黄→緑)

○<a name="weekly">七曜迷彩</a> [Ver. 1.4.0 α19〜]
発言に曜日が含まれていたら曜日の順番に合わせて入れ替えられてしまいます。
(例：日→月、月→火、火→水)

[作成者からのコメント]
狼少年の話題中に、循環変換されるタイプが提案されたので実装してみました。
あまり影響は無いでしょうが、ひとたびハマると対応は非常に面倒だと思われます。


○<a name="grassy">草原迷彩</a> [Ver. 1.4.0 α23〜]
発言の一文字毎に「w」が付け加えられます。

[作成者からのコメント]
いわゆる Vipper の再現です。
機械的につけるので占い師などにこれがつくとかなり悲惨な事になると思われます。


○<a name="side_reverse">鏡面迷彩</a> [Ver. 1.4.0 α23〜]
発言の文字の並びが一行単位で逆になります。

[作成者からのコメント]
要するに、「テスト」→「トステ」になると言う事です。
理論的には回文で発言すれば影響が出ないという事になります。


○<a name="line_reverse">天地迷彩</a> [Ver. 1.4.0 α23〜]
発言の行の並びの上下が入れ替わります。

[作成者からのコメント]
常に一行で発言をしている場合は影響がでませんし
対応しようと思えば簡単なので鏡面迷彩ほどは苦労しないと思われます。


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


◎投票数変化 (権力者系)
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


◎得票数変化 (雑草魂系)
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


◎処刑者候補変化 (決定者系)
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


◎発言変化 (大声系)
○<a name="strong_voice">大声</a> [Ver. 1.4.0 α3-7〜]
発言が常に大声になります。

○<a name="normal_voice">不器用</a> [Ver. 1.4.0 α3-7〜]
発言の大きさを変えられません。

○<a name="weak_voice">小声</a> [Ver. 1.4.0 α3-7〜]
発言が常に小声になります。

[作成者からのコメント]
声の大きさも狼を推理するヒントになります。
ただのネタと思うこと無かれ。


○<a name="inside_voice">内弁慶</a> [Ver. 1.4.0 α23〜]
昼は<a href="#weak_voice">小声</a>、夜は<a href="#strong_voice">大声</a>になります。

○<a name="outside_voice">外弁慶</a> [Ver. 1.4.0 α23〜]
昼は<a href="#stong_voice">大声</a>、夜は<a href="#weak_voice">小声</a>になります。

[作成者からのコメント]
大声/小声が LW だと圧倒的に不利だと思ったので捻ってみました。


○<a name="upper_voice">メガホン</a> [Ver. 1.4.0 α17〜]
発言が一段階大きくなり、大声は音割れして聞き取れなくなります。

○<a name="downer_voice">マスク</a> [Ver. 1.4.0 α17〜]
発言が一段階小さくなり、小声は聞き取れなくなります。

○<a name="random_voice">臆病者</a> [Ver. 1.4.0 α14〜]
声の大きさがランダムに変わります。

[作成者からのコメント]
固定があるならランダムもありだろうと思って作ってみました。
唐突に大声になるのは固定より鬱陶しいかも。


◎発言封印 (筆不精系)
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


○<a name="mower">草刈り</a> [Ver. 1.4.0 α23〜]
発言から「w」が削られます。

[作成者からのコメント]
いわゆる「(w」に当たるかどうかの判定をしていないので
場合によっては名前を呼ぶ事ができない可能性もあります。
とっても理不尽ですね。


◎夜発言公開 (サトラレ系)
○<a name="mind_read">サトラレ</a> [Ver. 1.4.0 α21〜]
夜の発言が<a href="#mind_scanner">さとり</a>に見られてしまいます。
誰に見られているのかは分かりません。
表示されるのは 2 日目の朝で、発言が見られるのは 2 日目夜以降です。
本人か、見ているさとりが死んだら効力が切れます。
自分が<a href="#unconscious">無意識</a>だった場合は相手には見られません。
この役職はサブ役職非公開設定でも必ず表示されます。

[作成者からのコメント]
新役職考案スレ (最下参照) の 4 が原型です。
相談できる人外がこれになるとかなり大変になると思われます。


○<a name="mind_receiver">受信者</a> [Ver. 1.4.0 α22〜]
特定の人の夜の発言を見ることができます。
<a href="#mind_read">サトラレ</a>と違い、誰の発言を見ているのか分かります。
本人か発言者が死んだら効力が切れます。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="#self_cupid">求愛者</a>の矢を撃たれた相手にこれがつきます。

[作成者からのコメント]
さとり - サトラレの逆バージョンです。
共有者などの会話に混ざって表示されるのでうっかり
返事をしないように気をつけましょう。


○<a name="mind_open">公開者</a> [Ver. 1.4.0 α22〜]
夜の発言が参加者全員に見られてしまいます。
観戦者には見えません。
<a href="#mind_read">サトラレ</a>と違い、初日の夜から見られます。
本人が死んだら効力が切れます。
この役職はサブ役職非公開設定でも必ず表示されます。

[作成者からのコメント]
サトラレをパワーアップしてみました。
相談できる系統の役職の人には大迷惑ですね。
くれぐれもいきなり自分の役職をつぶやかないように注意してください。


○<a name="mind_friend">共鳴者</a> [Ver. 1.4.0 α23〜]
特定の人と夜に会話できるようになります。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="#unknown_mania">鵺</a>と<a href="#mind_cupid">女神</a>の矢を撃たれた相手にこれがつきます。

[作成者からのコメント]
互いに認識できる<a href="#mind_receiver">受信者</a>ですね。
共有者が鵺と女神に同時に矢を撃たれた場合は、誰が誰の発言が
見えるのか、非常にややこしい状況になりますね。


○<a name="mind_evoke">口寄せ</a> [Ver. 1.4.0 β2〜]
死後に特定の人の遺言窓にメッセージを送れます。
この役職はサブ役職非公開設定でも必ず表示されます。
<a href="#evoke_scanner">イタコ</a>に投票された相手にこれがつきます。

1. 生きている時から表示されます (死んでも表示されます)
2. 生きている間は通常通り自分の遺言窓が更新されます
3. 死んでから「遺言を残す」で発言するとイタコの遺言窓が更新されます
4. <a href="#reporter">ブン屋</a>、<a href="#no_last_words">筆不精</a>など、生きている間は遺言を残せない役職でも有効です

[作成者からのコメント]
霊界から一方的にメッセージを送ることができます。
当然ですが、霊界オフモードにしないと機能しません。


◆作って欲しい役職などがあったらこちらのスレへどうぞ
【ネタ歓迎】あったらいいな、こんな役職【ガチ大歓迎】
http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50

参考スレッド
新役職提案スレッド＠やる夫
http://jbbs.livedoor.jp/bbs/read.cgi/game/48159/1243197597/l50


◎ 採用予定・作成中役職リスト
○ 採用予定 (レス 99までの状況)
・泥棒 (レス 13)
挙動は案募集中

・河童 (レス 17)
挙動は案募集中

・ミシャグジさま (レス 19)
改善 (レス 33)

・土蜘蛛 (レス 33)
挙動は案募集中
改善 (レス 43)

・宣教師 (レス 86)
挙動は未定

・主人公 (レス 89)

○ 採用思案中
・降霊術師 (レス 13)

・従者 (レス 13)
改善 (レス 64)

・蟲師 (レス 68)

・怠惰な死神 (レス85)


◎村編成案
○採用予定
グレラン村 (レス 65)

</pre>
</body></html>
