<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('新役職情報 - [一覧]', 'new_role');
?>
</head>
<body>

<h1>新役職情報</h1>
<ul>
  <li>バージョンアップするたびに仕様が変わる可能性があります</li>
</ul>
<p>
<a href="../" target="_top">&lt;-TOP</a>
<a href="./" target="_top">←メニュー</a>
<a href="#table">早見表</a>
<a href="#reference">参考リンク</a>
<a href="#memo">作成予定メモ</a>
</p>
<p>
<a href="human.php">村人陣営</a>
<a href="wolf.php">人狼陣営</a>
<a href="fox.php">妖狐陣営</a>
<a href="lovers.php">恋人陣営</a>
<a href="quiz.php">出題者陣営</a>
<a href="chiroptera.php">蝙蝠陣営</a>
<a href="sub_role.php">サブ役職</a>
</p>

<h2><a name="table">早見表</a></h2>
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
<a href="#140beta5">β5</a>
<a href="#140beta6">β6</a>
<a href="#140beta7">β7</a>
<a href="#140beta8">β8</a>
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
<a href="#sub_140beta8">β8</a>
</p>

<table>
<caption><a name="main_role">新役職早見表</a></caption>
  <tr>
    <th>名称</th>
    <th>陣営</th>
    <th>所属</th>
    <th>占い結果</th>
    <th>霊能結果</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="quiz.php#quiz" name="140alpha2">出題者</a></td>
    <td><a href="quiz.php">出題者</a></td>
    <td><a href="quiz.php#quiz_group">出題者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">クイズ村の GM。</td>
    <td>Ver. 1.4.0 α2</td>
  </tr>
  <tr>
    <td><a href="wolf.php#boss_wolf" name="140alpha3">白狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>村人</td>
    <td>白狼</td>
    <td class="ability">占い結果が「村人」、霊能結果が「白狼」と出る人狼。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#soul_mage">魂の占い師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">占った人の役職が分かる上位占い師。<br>
      妖狐を呪殺できないが呪返しは受ける。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#medium">巫女</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#necromancer_group">霊能者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">突然死した人の所属陣営が分かる特殊な霊能者。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_guard">騎士</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った上位狩人 (吊られても毒は発動しない)。<br>
      <a href="#guard_limit">護衛制限</a>の影響を受けない。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#fanatic_mad">狂信者</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼が誰か分かる上位狂人 (人狼からは狂信者は分からない)。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="fox.php#child_fox">子狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#child_fox_group">子狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability">呪殺されないが人狼に襲撃されると殺される妖狐。<br>
      仲間は分かるが念話はできない。占いも出来るが時々失敗する。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#suspect" name="140alpha9">不審者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td class="ability">占い師に人狼と判定されてしまう村人 (表示は「村人」)。<br>
      低確率で発言が遠吠えに入れ替わってしまう (<a href="wolf.php#cute_wolf">萌狼</a>と同じ)。</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="human.php#mania" name="140alpha11">神話マニア</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人の役職をコピーする (入れ替わるのは 2 日目の朝)。</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="wolf.php#poison_wolf" name="140alpha12">毒狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">毒を持った人狼。毒の対象は狼以外。</td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="human.php#pharmacist">薬師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#pharmacist_group">薬師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼に投票した人が毒を持っているか翌朝に分かる。<br>
      毒持ちを吊ったときに、投票していたら解毒 (毒が発動しない) する。
    </td>
    <td>Ver. 1.4.0 α12</td>
  </tr>
  <tr>
    <td><a href="human.php#unconscious" name="140alpha13">無意識</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼に無意識であることが分かってしまう村人 (表示は「村人」)。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="wolf.php#tongue_wolf">舌禍狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">自ら噛み投票を行った場合のみ、次の日に噛んだ人の役職が分かる。<br>
      襲撃に失敗したら無効。村人を襲撃すると能力を失う。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="human.php#reporter" name="140alpha14">ブン屋</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">尾行した人が噛まれた場合に、噛んだ人狼が誰か分かる特殊な狩人。<br>
      遺言を残せない。人外 (狼と狐) を尾行したら殺される。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_mage">夢見人</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る占い師 (表示は「占い師」)。<br>
      呪殺できない代わりに呪いや占い妨害の影響を受けない。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="wolf.php#cute_wolf">萌狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">低確率で発言が遠吠えに入れ替わってしまう。</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_necromancer" name="140alpha17">夢枕人</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#necromancer_group">霊能者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「村人」と「人狼」が反転した結果が出る霊能者 (表示は「霊能者」)。<br>
      <a href="wolf.php#corpse_courier_mad">火車</a>の妨害の影響を受けない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_guard">夢守人</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">狩人と思い込んでいる村人 (表示は「狩人」)。<br>
      常に護衛成功メッセージが出るが誰も護衛していない。
      何らかの形で<a href="wolf.php#dream_eater_mad">獏</a>に接触した場合は狩ることができる。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_common">夢共有者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#common_group">共有者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">「相方が身代わり君の共有者」と思い込んでいる村人。<br>
      共有者の囁きが見えない。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_poison">夢毒者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_group">埋毒者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">埋毒者と思い込んでいる村人 (表示は「埋毒者」)。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#soul_necromancer">雲外鏡</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#necromancer_group">霊能者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑した人の役職が分かる上位霊能者。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#strong_poison">強毒者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_group">埋毒者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">吊られた時に人外 (狼と狐) のみを巻き込む上位埋毒者。<br>
      表示は「埋毒者」で、村人に当たったら不発。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#incubate_poison">潜毒者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_group">埋毒者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">時が経つと (現在は 5 日目以降) <a href="human.php#strong_poison">強毒者</a>相当の毒を持つ村人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#resist_wolf">抗毒狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">一度だけ毒に耐えられる人狼。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#cursed_wolf">呪狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼<br>(呪返し)</td>
    <td>呪狼</td>
    <td class="ability">占われたら占った占い師を呪い殺す人狼。<br>
      <a href="human.php#voodoo_killer">陰陽師</a>に占われたら殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#whisper_mad">囁き狂人</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼の夜の相談に参加できる上位狂人。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="fox.php#cursed_fox">天狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪返し)</td>
    <td>妖狐</td>
    <td class="ability">占われたら占った占い師を呪い殺す妖狐。<br>
      人狼に襲撃されても死なないが、<a href="human.php#voodoo_killer">陰陽師</a>に占われるか狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="fox.php#poison_fox">管狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">毒を持った妖狐。<br>
      吊られた場合の毒の対象は狐以外。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="fox.php#white_fox">白狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>妖狐</td>
    <td class="ability">呪殺されないが人狼に襲撃されると殺される妖狐。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_cat" name="140alpha18">猫又</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_cat_group">猫又系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">蘇生能力を持った特殊な埋毒者。<br>
    蘇生成功率は 25% 程度で選んだ人と違う人が復活することもある。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="human.php#assassin">暗殺者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#assassin_group">暗殺者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人を暗殺できる村人。<br>
    人外 (狼や狐) でも暗殺できるが狩人の護衛を受けられない。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="human.php#psycho_mage">精神鑑定士</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人の心理状態を判定する特殊な占い師。<br>
      狂人・夢系・<a href="human.php#suspect">不審者</a>・<a href="human.php#unconscious">無意識</a>を占うと「嘘をついている」と判定される。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="wolf.php#trap_mad">罠師</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一度だけ夜に村人一人に罠を仕掛けることができる特殊な狂人。<br>
      罠を仕掛けた人の元に訪れた人狼・狩人系(狩人、<a href="human.php#poison_guard">騎士</a>、<a href="human.php#reporter">ブン屋</a>) <a href="human.php#assassin">暗殺者</a>は死亡する。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α18</td>
  </tr>
  <tr>
    <td><a href="wolf.php#jammer_mad" name="140alpha19">月兎</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人の占い行動を妨害する特殊な狂人。<br>
      狩人に護衛されると殺される。α21から邪魔狂人 → 月兎 に変更。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="human.php#sex_mage">ひよこ鑑定士</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人の性別を判別する特殊な占い師。<br>
      <a href="chiroptera.php">蝙蝠</a>を占うと「蝙蝠」と判定される。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="wolf.php#voodoo_mad" name="140alpha20">呪術師</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人に呪いをかける特殊な狂人。<br>
      呪われた人を占った占い師は呪返しを受ける。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="fox.php#voodoo_fox">九尾</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">夜に村人一人を選び、その人に呪いをかける妖狐。<br>
      呪われた人を占った占い師は呪返しを受ける。
      人狼に襲撃されても死なないが、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="human.php#anti_voodoo">厄神</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">護衛した人の厄 (占い妨害、呪返し、憑依) を祓う特殊な狩人。<br>
      成功した場合は次の日に専用のシステムメッセージが表示される。
    </td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="human.php#voodoo_killer">陰陽師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">対呪い専門の特殊な占い師。<br>
      占った人が呪い持ちか<a href="wolf.php#possessed_wolf">憑狼</a>の場合は呪殺し、誰かに呪いをかけられていた場合は解呪して、呪返しの発動をキャンセルする。呪殺か解呪が成功した場合のみ、次の日に専用のシステムメッセージが表示される。呪殺の死亡メッセージは「呪返し」と同じ。
    </td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="human.php#yama_necromancer">閻魔</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#necromancer_group">霊能者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">前日の死者の死因が分かる特殊な霊能者。<br>
      死因は画面の下に表示される「〜は無残な〜」の下の行に「(〜は人狼に襲撃されたようです)」等と表示される。</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="fox.php#silver_fox">銀狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">仲間が分からない妖狐。<br>
      (他の妖狐、<a href="fox.php#child_fox">子狐</a>からも仲間であると分からない)</td>
    <td>Ver. 1.4.0 α20</td>
  </tr>
  <tr>
    <td><a href="lovers.php#self_cupid" name="140alpha21">求愛者</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#cupid_group">キューピッド系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分撃ち固定のキューピッド。矢を打った相手に<a href="sub_role.php#mind_receiver">受信者</a>が追加される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#chiroptera">蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">生き残ったら勝利になる。<br>他の陣営の勝敗とは競合しない。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#poison_chiroptera">毒蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った蝙蝠。吊られた場合の毒の対象は人外 (狼と狐) と蝙蝠。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#cursed_chiroptera">呪蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人<br>(呪返し)</td>
    <td>村人</td>
    <td class="ability">占われたら占った占い師を呪い殺す蝙蝠。<br>
      <a href="human.php#voodoo_killer">陰陽師</a>に占われるか、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#silver_wolf">銀狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">仲間が分からない人狼。<br>
      (他の人狼、<a href="wolf.php#fanatic_mad">狂信者</a>、<a href="wolf.php#whisper_mad">囁き狂人</a>からも仲間であると分からない)</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="human.php#mind_scanner">さとり</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mind_scanner_group">さとり系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人を<a href="sub_role.php#mind_read">サトラレ</a>にする。<br>
      投票結果が出るのは 2 日目以降で、<a href="human.php#unconscious">無意識</a>の発言は読めない。狼の遠吠えが見えない。
    </td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#corpse_courier_mad">火車</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分が投票して吊った人の霊能結果を隠蔽できる特殊な狂人。<br>
      <a href="human.php#dummy_necromancer">夢枕人</a>には影響しない。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#dream_eater_mad">獏</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に投票した夢系能力者を殺すことができる特殊な狂人。<br>
      何らかの形で<a href="human.php#dummy_guard">夢守人</a>に接触した場合は殺される。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="human.php#jealousy" name="140alpha22">橋姫</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#jealousy_group">橋姫系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼の投票時に、同一キューピッドの恋人が揃って自分に投票したら投票した恋人をショック死させる。吊られた場合は無効。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="human.php#unknown_mania" name="140alpha23">鵺</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人と同じ所属陣営にする。<br>
     自分と投票先に<a href="sub_role.php#mind_friend">共鳴者</a>がつく (結果が表示されるのは 2 日目の朝)。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="lovers.php#mind_cupid">女神</a></td>
    <td><a href="lovers.php">恋人</td>
    <td><a href="lovers.php#cupid_group">キューピッド系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人を<a href="sub_role.php#mind_friend">共鳴者</a>にする上位キューピッド。<br>
      他人撃ちの場合は、さらに自分が二人の<a href="sub_role.php#mind_receiver">受信者</a>になる。</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="human.php#priest" name="140alpha24">司祭</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在、生存している村人陣営の人数が分かる。<br>
     現在は 4日目以降、1日おき (4 → 6 → 8 →...)。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="fox.php#scarlet_fox">紅狐</a></td>
    <td><a href="fox.php">妖狐</td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">人狼からは<a href="human.php#unconscious">無意識</a>に見える妖狐。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#wise_wolf">賢狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability"><a href="fox.php">妖狐陣営</a>の念話が共有者の囁きに変換されて聞こえる人狼。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#possessed_wolf">憑狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>憑狼</td>
    <td class="ability">襲撃が成功したら襲撃した人を乗っ取る人狼。<br>
      身代わり君・<a href="human.php#revive_priest">天人</a>・妖狐は乗っ取れない。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="fox.php#cute_fox">萌狐</a></td>
    <td><a href="fox.php">妖狐</td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">低確率で発言が遠吠えに入れ替わってしまう妖狐。<br>
    遠吠えの内容は<a href="human.php#suspect">不審者</a>や<a href="wolf.php#cute_wolf">萌狼</a>と同じ。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="fox.php#black_fox">黒狐</a></td>
    <td><a href="fox.php">妖狐</td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>人狼<br>(呪殺無し)</td>
    <td>妖狐</td>
    <td class="ability">占い結果が「人狼」、霊能結果が「妖狐」と判定される妖狐。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#scarlet_wolf">紅狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability"><a href="fox.php">妖狐陣営</a>から<a href="fox.php#child_fox">子狐</a>に見える人狼。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#dummy_chiroptera">夢求愛者</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">本人には<a href="lovers.php#self_cupid">求愛者</a>と表示されている蝙蝠。<br>
      矢を撃つことはできるが恋人にはならず、<a href="sub_role.php#mind_receiver">受信者</a>もつかない。</td>
    <td>Ver. 1.4.0 α24</td>
  </tr>
  <tr>
    <td><a href="human.php#crisis_priest" name="140beta2">預言者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">今が「人外勝利前日」が分かる特殊な司祭 (表示は「村人」)。<br>
      「人外勝利前日である」と判定された場合は、どの陣営が有利なのかメッセージが表示される。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_priest">天人</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">二日目の朝にいきなり死亡して、「人外勝利前日」「5日目以降」「人口半減」「LW」のどれかを満たすと生き返る特殊な司祭。<br>
      恋人になると能力を失う。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="human.php#evoke_scanner">イタコ</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mind_scanner_group">さとり系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人を<a href="sub_role.php#mind_evoke">口寄せ</a>にする。<br>
      投票結果が出るのは 2 日目以降。自分の遺言欄に何が表示されていても遺言は残らない。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_cat">仙狸</a></td>
    <td><a href="human.php">村人</a></td>
    <td><a href="human.php#poison_cat_group">猫又系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒能力を失った代わりに高い蘇生能力を持った猫又の上位種。<br>
    蘇生成功率は 80% で蘇生に成功するたびに成功率が 1/4 になる。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="fox.php#revive_fox">仙狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">蘇生能力を持った妖狐。成功率は 100% だが、一度成功すると能力を失う。<br>
      人狼に襲撃されても死なないが、狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="human.php#elder" name="140beta5">長老</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票数が +1 される村人。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="wolf.php#elder_wolf" name="140beta5">古狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">投票数が +1 される人狼。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="fox.php#elder_fox">古狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">投票数が +1 される妖狐。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#elder_chiroptera">古蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票数が +1 される蝙蝠。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="human.php#fend_guard">忍者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一度だけ人狼の襲撃を耐える事ができる狩人。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="human.php#trap_common" name="140beta6">策士</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#common_group">共有者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">村人陣営以外の人全てから投票されたらまとめて死亡させる上位共有者。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#fairy">妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先の発言の先頭に共有者の囁きを追加する。<br>
      呪い・占い妨害・厄払いの影響を受ける。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#spring_fairy">春妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先の発言の先頭に「春ですよー」を追加する妖精。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#summer_fairy">夏妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先の発言の先頭に「夏ですよー」を追加する妖精。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#autumn_fairy">秋妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先の発言の先頭に「秋ですよー」を追加する妖精。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#winter_fairy">冬妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先の発言の先頭に「冬ですよー」を追加する妖精。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="human.php#ghost_common">亡霊嬢</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#common_group">共有者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability"><a href="wolf.php#wolf_group">人狼</a>に襲撃されたら襲撃してきた人狼に<a href="sub_role.php#chicken">小心者</a>を付加する上位共有者。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_jealousy">毒橋姫</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#jealousy_group">橋姫系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">恋人のみに中る埋毒者。本人の表記は「埋毒者」。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="human.php#chain_poison">連毒者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_group">埋毒者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">他の毒能力者に巻き込まれたら、さらに二人巻き込む埋毒者。本人の表記は「村人」。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="human.php#saint" name="140beta7">聖女</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">再投票の最多得票者になった場合に、内訳によって吊られる人を変化させる村人。本人の表記は「村人」。</td>
    <td>Ver. 1.4.0 β7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#agitate_mad">扇動者</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">再投票の最多得票者に投票していた場合に、投票先を吊り、それ以外の最多得票者をまとめてショック死させる特殊な狂人。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#light_fairy">光妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先が人狼に襲撃されたら、次の日の夜を「白夜」(全員<a href="sub_role.php#mind_open">公開者</a>) にする妖精。</td>
    <td>Ver. 1.4.0 β7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#dark_fairy">闇妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">投票先が人狼に襲撃されたら、次の日の昼を「宵闇」(全員<a href="sub_role.php#blinder">目隠し</a>) にする妖精。</td>
    <td>Ver. 1.4.0 β7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#mirror_fairy">鏡妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">本人が吊られたら、次の日の昼を「決選投票」(初日に投票した二人にしか投票できない) にする妖精。</td>
    <td>Ver. 1.4.0 β7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#emerald_wolf" name="140beta8">翠狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">噛んだ人が狼だった場合に自分と噛んだ人を<a href="sub_role.php#mind_friend">共鳴者</a>にする人狼。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="wolf.php#blue_wolf">蒼狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">噛んだ人が<a href="fox.php#silver_fox">銀狐</a>以外の噛み殺せない妖狐だった場合に<a href="sub_role.php#mind_lonely">はぐれ者</a>を付加する人狼。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="fox.php#emerald_fox">翠狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">占った人が会話できない妖狐だった場合に自分と占った人を<a href="sub_role.php#mind_friend">共鳴者</a>にする妖狐。<br>
    一度発動すると能力を失う。
    </td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="fox.php#blue_fox">蒼狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability"><a href="wolf.php#wolf_group">人狼</a>に襲撃されたら襲撃してきた人狼を<a href="sub_role.php#mind_lonely">はぐれ者</a>にする妖狐。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
</table>

<table>
<caption><a name="sub_role">新サブ役職早見表</a></caption>
  <tr>
    <th>名称</th>
    <th>所属</th>
    <th>表示</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="sub_role.php#strong_voice" name="sub_140alpha3">大声</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">常に大声になる</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#normal_voice">不器用</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">発言の大きさを変えられない</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#weak_voice">小声</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">常に小声になる</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#chicken">小心者</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に一票でも貰うとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rabbit">ウサギ</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に一票も貰えないとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#perverseness">天邪鬼</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に他の人と投票先が被るとショック死する</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#no_last_words" name="sub_140alpha9">筆不精</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">遺言を残せない</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#watcher">傍観者</a></td>
    <td><a href="sub_role.php#authority_group">権力者系</a></td>
    <td>○</td>
    <td class="ability">投票数が 0 になる</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#plague">疫病神</a></td>
    <td><a href="sub_role.php#decide_group">決定者系</a></td>
    <td>×</td>
    <td class="ability">処刑者候補が複数いた場合に自分の投票先が吊り候補から除外される</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#liar" name="sub_140alpha11">狼少年</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言時に「人⇔狼」等が入れ替わる (たまに変換されないこともある)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_voice" name="sub_140alpha14">臆病者</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">声の大きさがランダムに変わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#earplug">耳栓</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">発言が一段階小さく見えるようになり、小声が聞き取れなくなる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#good_luck">幸運</a></td>
    <td><a href="sub_role.php#decide_group">決定者系</a></td>
    <td>×</td>
    <td class="ability">自分が最多得票者で処刑者候補が複数いた場合は吊り候補から除外される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#bad_luck">不運</a></td>
    <td><a href="sub_role.php#decide_group">決定者系</a></td>
    <td>×</td>
    <td class="ability">自分が最多得票者で処刑者候補が複数いた場合は優先的に吊られる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#upper_luck">雑草魂</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>○</td>
    <td class="ability">2日目の得票数が +4 される代わりに、3日目以降は -2 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#downer_luck">一発屋</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>○</td>
    <td class="ability">2日目の得票数が -4 される代わりに、3日目以降は +2 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#star">人気者</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>○</td>
    <td class="ability">得票数が -1 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#disfavor">不人気</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>○</td>
    <td class="ability">得票数が +1 される</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_voter">気分屋</a></td>
    <td><a href="sub_role.php#authority_group">権力者系</a></td>
    <td>○</td>
    <td class="ability">投票数に -1〜+1 の範囲でランダムに補正がかかる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rebel">反逆者</a></td>
    <td><a href="sub_role.php#authority">権力者系</a></td>
    <td>○</td>
    <td class="ability">権力者と同じ人に投票した場合に自分と権力者の投票数が 0 になる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#gentleman">紳士</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">時々発言が「紳士」な言葉に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#lady">淑女</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">時々発言が「淑女」な言葉に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#blinder">目隠し</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">発言者の名前が見えない (空白に見える)</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#silent">無口</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">発言の文字数に制限がかかる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#invisible">光学迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言の一部が空白に入れ替わる</td>
    <td>Ver. 1.4.0 α14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_luck" name="sub_140alpha15">波乱万丈</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>○</td>
    <td class="ability">得票数に -2〜+2 の範囲でランダムに補正がかかる</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#flattery">ゴマすり</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に投票先が誰とも被っていないとショック死する</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#impatience">短気</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">決定者と同等の能力がある代わりに再投票になるとショック死する</td>
    <td>Ver. 1.4.0 α15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#speaker" name="sub_140alpha17">スピーカー</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">発言が一段階大きく見えるようになり、大声が聞き取れなくなる</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#upper_voice">メガホン</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">発言が一段階大きくなり、大声は音割れして聞き取れなくなる</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#downer_voice">マスク</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">発言が一段階小さくなり、小声は聞き取れなくなる</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rainbow">虹色迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言に虹の色が含まれていたら虹の順番に合わせて入れ替えられてしまう</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#panelist">解答者</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">投票数が 0 になり、出題者に投票したらショック死する<br>
    クイズ村専用。</td>
    <td>Ver. 1.4.0 α17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#weekly" name="sub_140alpha19">七曜迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言に曜日が含まれていたら曜日の順番に合わせて入れ替えられてしまう</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_read" name="sub_140alpha21">サトラレ</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability"><a href="human.php#mind_scanner">さとり</a>に夜の発言が見られてしまう</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_open" name="sub_140alpha22">公開者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">二日目以降の夜の発言が参加者全員に見られてしまう</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_receiver">受信者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">特定の人の夜の発言が見えるようになる</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#celibacy">独身貴族</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に恋人から一票でも貰うとショック死する</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#inside_voice" name="sub_140alpha23">内弁慶</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">昼は小声、夜は大声になる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#outside_voice">外弁慶</a></td>
    <td><a href="sub_role.php#strong_voice_group">大声系</a></td>
    <td>○</td>
    <td class="ability">昼は大声、夜は小声になる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mower">草刈り</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">発言から「w」が削られる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#grassy">草原迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言の一文字毎に「w」が付加される</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#side_reverse">鏡面迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言の文字の並びが一行単位で逆になる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#line_reverse">天地迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言の行の並びの上下が入れ替わる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_friend">共鳴者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">特定の人と夜に会話できるようになる</td>
    <td>Ver. 1.4.0 α23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_evoke" name="sub_140beta2">口寄せ</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">死後に特定の人の遺言窓にメッセージを送れるようになる</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_lonely" name="sub_140beta8">はぐれ者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">仲間が分からなくなり、会話できなくなる</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
</table>

<h2><a name="reference">参考リンク</a></h2>
<pre>
作って欲しい役職などがあったらこちらのスレへどうぞ
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50" target="_top">【ネタ歓迎】あったらいいな、こんな役職【ガチ大歓迎】</a>

参考スレッド
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/48159/1243197597/l50" target="_top">新役職提案スレッド＠やる夫</a>
</pre>

<h2><a name="memo">作成予定メモ</a></h2>
<h3>役職</h3>
<h4>作成予定</h4>
<pre>
○誘毒者 (村人陣営 / 村人系？)
・毒が村に中った時だけ身代わりで死ぬ

○吉凶占い師 (村人陣営 / 占い師系)
・対象がその村でどれくらい活躍できる可能性があるかを吉凶で判定する

○耐毒者 (村人陣営 / 薬師系？)
・毒がいっさい効かない村人

○死神 (村人陣営 / 暗殺系)
・投票したら 3 日後に死ぬ
・投票先に暗殺予告が表示される
・再び投票したら寿命が延びる

○仕事人 (村人陣営 / 暗殺系)
・村人陣営を殺したら能力を失う (結果として人外を殺した事が自覚できる)

○覚醒者 (村人陣営 / 神話マニア系)
・5日目にコピー先の上位職に変化する
・上位職の定義は役職が増えたら変更する

○夢語部 (村人陣営 / 神話マニア系)
・5日目にコピー先の夢・劣化職に変化する
・夢・劣化職の定義は役職が増えたら変更する

○天狼 (人狼陣営 / 人狼系)
・LW になったら毒無効、護衛突破能力を持つ

○対妖狐狼 (人狼陣営 / 人狼系)
・妖狐を噛める人狼
・何かしらの弱点をつける
・妖狐陣営が人狼陣営に対して強力になってきたら実装する

○嫉狐 (妖狐陣営 / 妖狐系？)
・橋姫能力をもった妖狐

○雛狐 (妖狐陣営 / 子狐)
・ひよこ鑑定士の能力をもった子狐

○大蝙蝠 (蝙蝠陣営 / 蝙蝠系)
・噛まれたら他の蝙蝠に身代わりになってもらう
・弱点や詳細な仕様は要検討
</pre>

<h4>採用予定</h4>
<pre>
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
</pre>
<h4>採用思案中</h4>
<pre>
・降霊術師 (レス 13)

・従者 (レス 13)
改善 (レス 64)

・蟲師 (レス 68)

・怠惰な死神 (レス85)
</pre>


<h3>村編成案</h3>
<h4>採用予定</h4>
<pre>
グレラン村 (レス 65)
</pre>
</body></html>
