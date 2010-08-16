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
<a href="vampire.php">吸血鬼陣営</a>
<a href="chiroptera.php">蝙蝠陣営</a>
<a href="mania.php">神話マニア陣営</a>
<a href="sub_role.php">サブ役職</a>
</p>

<h2><a id="table">早見表</a></h2>
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
<a href="#140beta9">β9</a>
<a href="#140beta10">β10</a>
<a href="#140beta11">β11</a>
<a href="#140beta12">β12</a>
<a href="#140beta13">β13</a>
<a href="#140beta14">β14</a>
<a href="#140beta15">β15</a>
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
<a href="#sub_140beta9">β9</a>
<a href="#sub_140beta10">β10</a>
<a href="#sub_140beta11">β11</a>
<a href="#sub_140beta14">β14</a>
</p>

<table>
<caption><a id="main_role">新役職早見表</a></caption>
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
    <td><a href="quiz.php#quiz" id="140alpha2">出題者</a></td>
    <td><a href="quiz.php">出題者</a></td>
    <td><a href="quiz.php#quiz_group">出題者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">クイズ村の GM。</td>
    <td>Ver. 1.4.0 α2</td>
  </tr>
  <tr>
    <td><a href="wolf.php#boss_wolf" id="140alpha3">白狼</a></td>
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
    <td><a href="human.php#medium_group">巫女系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">突然死した人の所属陣営が分かる。</td>
    <td>Ver. 1.4.0 α3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_guard">騎士</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った上位狩人 (吊られても毒は発動しない)。<br>
      <a href="human.php#guard_limit">護衛制限</a>の影響を受けない。</td>
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
    <td><a href="human.php#suspect" id="140alpha9">不審者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>人狼</td>
    <td>村人</td>
    <td class="ability">占い師に人狼と判定されてしまう村人 (表示は「村人」)。<br>
      低確率で発言が遠吠えに入れ替わってしまう (<a href="wolf.php#cute_wolf">萌狼</a>と同じ)。</td>
    <td>Ver. 1.4.0 α9</td>
  </tr>
  <tr>
    <td><a href="mania.php#mania" id="140alpha11">神話マニア</a></td>
    <td><a href="mania.php">神話マニア</td>
    <td><a href="mania.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">初日の夜に誰か一人を選んでその人の役職をコピーする (入れ替わるのは 2 日目の朝)。</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="wolf.php#poison_wolf" id="140alpha12">毒狼</a></td>
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
    <td><a href="human.php#unconscious" id="140alpha13">無意識</a></td>
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
    <td class="ability">襲撃に成功した人の役職が分かる人狼。<br>
      本人が投票した場合のみ有効で、村人だった場合は能力を失う。</td>
    <td>Ver. 1.4.0 α13</td>
  </tr>
  <tr>
    <td><a href="human.php#reporter" id="140alpha14">ブン屋</a></td>
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
    <td><a href="human.php#dummy_necromancer" id="140alpha17">夢枕人</a></td>
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
    <td class="ability">埋毒者と思い込んでいる村人 (表示は「埋毒者」)。<br>
      吊られた場合は<a href="wolf.php#dream_eater_mad">獏</a>のみを巻き込む (<a href="human.php#pharmacist_group">薬師系</a>による解毒は不可)。</td>
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
    <td class="ability">時が経つと (現在は 5 日目以降) <a href="human.php#strong_poison">強毒者</a>相当の毒を持つ特殊な埋毒者。</td>
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
    <td><a href="human.php#poison_cat" id="140alpha18">猫又</a></td>
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
      <a href="wolf.php#mad_group">狂人系</a>・夢系・<a href="human.php#suspect">不審者</a>・<a href="human.php#unconscious">無意識</a>を占うと「嘘をついている」と判定される。</td>
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
    <td><a href="wolf.php#jammer_mad" id="140alpha19">月兎</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に投票した人の占い行動を妨害する特殊な狂人。<br>
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
      <a href="chiroptera.php">蝙蝠</a>・<a href="wolf.php#gold_wolf">金狼</a>・<a href="fox.php#gold_fox">金狐</a>を占うと「蝙蝠」と判定される。</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="wolf.php#voodoo_mad" id="140alpha20">呪術師</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に投票した人に呪いをかける特殊な狂人。<br>
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
      占った人が呪い持ちか憑依能力者の場合は呪殺し、誰かに呪いをかけられていた場合は解呪して、呪返しの発動をキャンセルする。呪殺か解呪が成功した場合のみ、次の日に専用のシステムメッセージが表示される。呪殺の死亡メッセージは「呪返し」と同じ。
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
    <td><a href="lovers.php#self_cupid" id="140alpha21">求愛者</a></td>
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
    <td class="ability">自分の投票先が処刑されたら霊能結果を隠蔽できる特殊な狂人。<br>
      <a href="human.php#dummy_necromancer">夢枕人</a>には影響しない。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#dream_eater_mad">獏</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に投票した夢系能力者・<a href="chiroptera.php#fairy_group">妖精系</a>を殺すことができる特殊な狂人。<br>
      何らかの形で<a href="human.php#dummy_guard">夢守人</a>に接触した場合は殺される。狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="human.php#jealousy" id="140alpha22">橋姫</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#jealousy_group">橋姫系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼の投票時に、同一キューピッドの恋人が揃って自分に投票したら投票した恋人をショック死させる。吊られた場合は無効。</td>
    <td>Ver. 1.4.0 α22</td>
  </tr>
  <tr>
    <td><a href="mania.php#unknown_mania" id="140alpha23">鵺</a></td>
    <td><a href="mania.php">神話マニア</td>
    <td><a href="mania.php#mania_group">神話マニア系</a></td>
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
    <td><a href="human.php#priest" id="140alpha24">司祭</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在、生存している村人陣営の人数が分かる。<br>
     表示されるのは 4日目以降の偶数日 (4 → 6 → 8 →...)。</td>
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
    <td class="ability">襲撃に成功した人を乗っ取る人狼。<br>
      身代わり君・<a href="human.php#revive_priest">天人</a>・<a href="human.php#detective_common">探偵</a>・妖狐は乗っ取れない。</td>
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
    <td><a href="human.php#crisis_priest" id="140beta2">預言者</a></td>
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
    <td><a href="human.php#elder" id="140beta5">長老</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑投票数が +1 される村人。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="wolf.php#elder_wolf">古狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">処刑投票数が +1 される人狼。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="fox.php#elder_fox">古狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability">処刑投票数が +1 される妖狐。</td>
    <td>Ver. 1.4.0 β5</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#elder_chiroptera">古蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑投票数が +1 される蝙蝠。</td>
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
    <td><a href="human.php#trap_common" id="140beta6">策士</a></td>
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
    <td class="ability">他の毒能力者に巻き込まれたら、さらに二人巻き込む特殊な埋毒者。本人の表記は「村人」。</td>
    <td>Ver. 1.4.0 β6</td>
  </tr>
  <tr>
    <td><a href="human.php#saint" id="140beta7">聖女</a></td>
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
    <td><a href="wolf.php#emerald_wolf" id="140beta8">翠狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">襲撃した人が狼だった場合に自分と<a href="sub_role.php#mind_friend">共鳴者</a>にする人狼。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="wolf.php#blue_wolf">蒼狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">襲撃した人が<a href="fox.php#silver_fox">銀狐</a>以外の噛み殺せない妖狐だった場合に<a href="sub_role.php#mind_lonely">はぐれ者</a>を付加する人狼。</td>
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
  <tr>
    <td><a href="wolf.php#gold_wolf">金狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability"><a href="human.php#sex_mage">ひよこ鑑定士</a>の判定が<a href="chiroptera.php">蝙蝠</a>になる人狼。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="fox.php#gold_fox">金狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>村人</td>
    <td class="ability"><a href="human.php#sex_mage">ひよこ鑑定士</a>の判定が<a href="chiroptera.php">蝙蝠</a>になる妖狐。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="wolf.php#sex_wolf">雛狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">襲撃に成功した人の性別が分かるが、噛み殺せない人狼。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="fox.php#sex_fox">雛狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#child_fox_group">子狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability"><a href="human.php#sex_mage">ひよこ鑑定士</a>相当の能力を持つ子狐。成功率は 70%。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="lovers.php#angel">天使</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#angel_group">天使系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人が男女だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="lovers.php#rose_angel">薔薇天使</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#angel_group">天使系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人が男性同士だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="lovers.php#lily_angel">百合天使</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#angel_group">天使系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人が女性同士だった場合に<a href="sub_role.php#mind_sympathy">共感者</a>を付加するキューピッド。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="lovers.php#ark_angel">大天使</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#angel_group">天使系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">他の<a href="#angel_group">天使系</a>が作成した<a href="sub_role.php#mind_sympathy">共感者</a>の結果を見ることができる上位天使。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="lovers.php#triangle_cupid">小悪魔</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#cupid_group">キューピッド系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を三本撃てるキューピッド。</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="human.php#reverse_assassin" id="140beta9">反魂師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#assassin_group">暗殺者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜に選んだ人が生きていたら暗殺、死んでいたら蘇生する特殊な暗殺者。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="wolf.php#miasma_mad">土蜘蛛</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑者決定後に、投票先が処刑者ではなかったら<a href="sub_role#febris">熱病</a>にする特殊な狂人。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="human.php#cure_pharmacist">河童</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#pharmacist_group">薬師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">昼に投票した人を解毒・ショック死抑制する特殊な薬師。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="human.php#executor">執行者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">再投票発生時に非村人に投票していた場合は吊る事が出来る村人。本人の表記は「村人」。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="wolf.php#possessed_mad">犬神</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一度だけ、死体に憑依できる特殊な狂人。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="fox.php#possessed_fox">憑狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>妖狐</td>
    <td class="ability">一度だけ、死体に憑依できる妖狐。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="human.php#sacrifice_cat">猫神</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_cat_group">猫又系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分の命と引き換えに誤爆なし、成功率100%の蘇生ができる特殊な猫又</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="wolf.php#sirius_wolf">天狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">仲間の狼が減ると特殊能力が発現する狼。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="human.php#eclipse_assassin">蝕暗殺者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#assassin_group">暗殺者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">30% の確率で暗殺反射が発生する劣化暗殺者。本人の表記は「暗殺者」。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="mania.php#trick_mania">奇術師</a></td>
    <td><a href="mania.php">神話マニア</td>
    <td><a href="mania.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">コピー先が「初日投票をしてなかった」場合はその役職を奪い取り、
相手はその系統の基本職に入れ替わってしまう、特殊な神話マニア。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#boss_chiroptera">大蝙蝠</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#chiroptera_group">蝙蝠系</a></td>
    <td>蝙蝠</td>
    <td>村人</td>
    <td class="ability">人狼に襲撃された時、他の蝙蝠陣営の人を身代わりして生き延びることができる蝙蝠。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="human.php#doll" id="140beta10">上海人形</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#doll_group">上海人形系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">他の国で言う「奴隷」。<br>勝利条件は「<a href="human.php#doll_master">人形遣い</a>が全員死亡している＋村が勝利」で、自身の生死は不問。<br>
<a href="human.php#doll_master">人形遣い</a>が人狼に襲撃されたら代わりに死亡する。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#doll_master">人形遣い</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#doll_group">上海人形系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">他の国で言う「貴族」。<br>人狼に襲撃された時、他の人形遣い以外の人形系の人を身代わりして生き延びることができる。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_doll">鈴蘭人形</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#doll_group">上海人形系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒を持った人形。毒の対象は上海人形系以外 (<a href="human.php#doll_master">人形遣い</a>には中る)。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#friend_doll">仏蘭西人形</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#doll_group">上海人形系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">他の人形が誰か分かる人形。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#detective_common">探偵</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#common_group">共有者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒無効・暗殺反射などの様々な特殊耐性を持つ、上位共有者。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#doom_assassin">死神</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#assassin_group">暗殺者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">暗殺行動の代りに<a href="sub_role.php#death_warrant">死の宣告</a>を付加する特殊な暗殺者。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="human.php#bishop_priest">司教</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在、死亡している村人陣営以外の人数が分かる、特殊な司祭。<br>
    表示されるのは 3日目以降の奇数日 (3 → 5 → 7 →...)。</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="lovers.php#exchange_angel" id="140beta11">魂移使</a></td>
    <td><a href="lovers.php">恋人</td>
    <td><a href="lovers.php#angel_group">天使系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">矢を撃った二人を交換憑依させてしまうキューピッド。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="mania.php#soul_mania">覚醒者</a></td>
    <td><a href="mania.php">神話マニア</td>
    <td><a href="mania.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">4日目にコピー先の上位種に変化する、特殊な神話マニア。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="mania.php#dummy_mania">夢語部</a></td>
    <td><a href="mania.php">神話マニア</td>
    <td><a href="mania.php#mania_group">神話マニア系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">4日目にコピー先の基本・劣化種に変化する、特殊な神話マニア。<br>
    本人表記は「<a href="mania.php#soul_mania">覚醒者</a>」で、変化前に<a href="wolf.php#dream_eater_mad">獏</a>に襲撃されると殺される。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="wolf.php#phantom_wolf">幻狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>幻狼</td>
    <td class="ability">一度だけ、自分が占われても占い妨害をする事ができる人狼。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="fox.php#phantom_fox">幻狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>妖狐</td>
    <td class="ability">一度だけ、自分が占われても占い妨害をする事ができる妖狐。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="human.php#guide_poison">誘毒者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#poison_group">埋毒者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">毒能力者のみに中る特殊な埋毒者。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="human.php#escaper">逃亡者</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">2日目の夜以降、生きている誰かの側に逃亡して生存を図ろうとする村人。<br>
    勝利条件は「村人陣営の勝利」＋「自身の生存」。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="human.php#whisper_scanner">囁騒霊</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mind_scanner_group">さとり系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">2日目夜以降、<a href="human.php#common_group">共有者系</a>に一方的に声が届く特殊なさとり。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="human.php#howl_scanner">吠騒霊</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mind_scanner_group">さとり系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">2日目夜以降、<a href="wolf.php#wolf_group">人狼系</a>に一方的に声が届く特殊なさとり。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="human.php#telepath_scanner">念騒霊</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mind_scanner_group">さとり系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">2日目夜以降、<a href="fox.php#fox_group">妖狐系</a>に一方的に声が届く特殊なさとり。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="lovers.php#moon_cupid">かぐや姫</a></td>
    <td><a href="lovers.php">恋人</a></td>
    <td><a href="lovers.php#cupid_group">キューピッド系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">自分撃ち固定のキューピッド。自分に<a href="sub_role.php#mind_receiver">受信者</a>、自分と相手に<a href="sub_role.php#challenge_lovers">難題</a>が追加される。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="wolf.php#hungry_wolf" id="140beta12">餓狼</a></td>
    <td><a href="wolf.php">人狼</a></td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">人狼と妖狐しか噛み殺せない人狼。</td>
    <td>Ver. 1.4.0 β12</td>
  </tr>
  <tr>
    <td><a href="human.php#stargazer_mage">占星術師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#mage_group">占い師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">夜の投票能力の有無を判定する特殊な占い師。</td>
    <td>Ver. 1.4.0 β12</td>
  </tr>
  <tr>
    <td><a href="human.php#border_priest">境界師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">二日目以降、「夜、自分に何らかの投票をした人の数」が分かる、特殊な司祭。</td>
    <td>Ver. 1.4.0 β12</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#flower_fairy">花妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">悪戯が成功すると、花に関するメッセージを死亡メッセージ欄に表示できる妖精。</td>
    <td>Ver. 1.4.0 β12</td>
  </tr>
  <tr>
    <td><a href="human.php#doom_doll" id="140beta13">蓬莱人形</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#doll_group">上海人形系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">吊られた時に、自分に投票した人からランダムで一人に<a href="sub_role.php#death_warrant">死の宣告</a>を付加する人形。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="fox.php#miasma_fox">蟲狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#child_fox_group">子狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability">吊られるか人狼に襲撃されたら<a href="sub_role.php#febris">熱病</a>を付加する子狐。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="fox.php#jammer_fox">月狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#child_fox_group">子狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability"<a href="wolf.php#jammer_mad">月兎</a>相当の能力を持つ子狐。成功率は 70%。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="fox.php#stargazer_fox">星狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#child_fox_group">子狐系</a></td>
    <td>村人<br>(呪殺無し)</td>
    <td>子狐</td>
    <td class="ability"<a href="human.php#stargazer_mage">占星術師</a>相当の能力を持つ子狐。成功率は 70%。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#sun_fairy">日妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">悪戯先が人狼に襲撃されたら、次の日の昼を全員<a href="sub_role.php#invisible">光学迷彩</a>にする妖精。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#moon_fairy">月妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">悪戯先が人狼に襲撃されたら、次の日の昼を全員<a href="sub_role.php#earplug">耳栓</a>にする妖精。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#star_fairy">星妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">悪戯が成功すると、星に関するメッセージを死亡メッセージ欄に表示できる妖精。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#grass_fairy">草妖精</a></td>
    <td><a href="chiroptera.php">蝙蝠</a></td>
    <td><a href="chiroptera.php#fairy_group">妖精系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">悪戯先が人狼に襲撃されたら、次の日の昼を全員<a href="sub_role.php#grassy">草原迷彩</a>にする妖精。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="human.php#hunter_guard">猟師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">妖狐も狩る事ができる特殊な狩人。護衛先が人狼に襲撃されたら本人は殺される。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="human.php#soul_assassin">辻斬り</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#assassin_group">暗殺者系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">暗殺した人の役職を知る事ができる上位暗殺者。毒能力者を暗殺したら本人は毒死する。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="human.php#seal_medium">封印師</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#medium_group">巫女系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑投票先が回数限定の能力を持っている人外の場合に封じることができる上位巫女。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_medium">風祝</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#medium_group">巫女系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability"><a href="human.php#poison_cat">猫又</a>相当の蘇生能力を持った上位巫女。</td>
    <td>Ver. 1.4.0 β13</td>
  </tr>
  <tr>
    <td><a href="human.php#blind_guard" id="140beta14">夜雀</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#guard_group">狩人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability"><a href="human.php#guard_hunt">狩り能力</a>は持たないが、護衛先を襲撃した人狼に<a href="sub_role.php#blinder">目隠し</a>を付加する特殊な狩人。<br>
      <a href="human.php#guard_limit">護衛制限</a>の影響を受けない。
    </td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="vampire.php#vampire">吸血鬼</a></td>
    <td><a href="vampire.php">吸血鬼</td>
    <td><a href="vampire.php#vampire_group">吸血鬼系</a></td>
    <td>蝙蝠</td>
    <td>蝙蝠</td>
    <td class="ability">他国で言うカルトリーダー相当。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="wolf.php#doom_wolf" id="140beta15">冥狼</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#wolf_group">人狼系</a></td>
    <td>人狼</td>
    <td>人狼</td>
    <td class="ability">襲撃に成功した人を噛み殺す代わりに<a href="sub_role.php#death_warrant">死の宣告</a>を付加する人狼。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="fox.php#doom_fox">冥狐</a></td>
    <td><a href="fox.php">妖狐</a></td>
    <td><a href="fox.php#fox_group">妖狐系</a></td>
    <td>村人<br>(呪殺)</td>
    <td>妖狐</td>
    <td class="ability">遅効性の<a href="human.php#doom_assassin">死神</a>相当の暗殺能力を持った妖狐。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="human.php#brownie">座敷童子</a></td>
    <td><a href="human.php">村人</a></td>
    <td><a href="human.php#human_group">村人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">役職「村人」の処刑投票数を +1 する村人。<br>
      処刑されたら投票した人からランダムで一人に<a href="sub_role.php#febris">熱病</a>を付加する。
    </td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="human.php#dowser_priest">探知師</a></td>
    <td><a href="human.php">村人</a></td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在の生存者が所持している<a href="sub_role.php">サブ役職</a>の合計が分かる特殊な司祭。<br>
     表示されるのは 4日目以降の偶数日 (4 → 6 → 8 →...)。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_priest">夢司祭</a></td>
    <td><a href="human.php">村人</a></td>
    <td><a href="human.php#priest_group">司祭系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">一定日数ごとに現在、生きている夢系能力者と<a href="chiroptera.php#fairy_group">妖精系</a>の人数が分かる特殊な司祭。<br>
      本人表記は<a href="human.php#priest">司祭</a>で仕様も同じ。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_pharmacist">仙人</a></td>
    <td><a href="human.php">村人</td>
    <td><a href="human.php#pharmacist_group">薬師系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">処刑投票した人のショック死を抑制する特殊な薬師。<br>
      人狼に襲撃されて死亡した場合、一度だけ即座に蘇生する。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
  <tr>
    <td><a href="wolf.php#therian_mad">獣人</a></td>
    <td><a href="wolf.php">人狼</td>
    <td><a href="wolf.php#mad_group">狂人系</a></td>
    <td>村人</td>
    <td>村人</td>
    <td class="ability">人狼 (種類は問わない) に襲撃されたら「人狼」に変化する、特殊な狂人。<br>
      狩人に護衛されると殺される。</td>
    <td>Ver. 1.4.0 β15</td>
  </tr>
</table>

<table>
<caption><a id="sub_role">新サブ役職早見表</a></caption>
  <tr>
    <th>名称</th>
    <th>所属</th>
    <th>表示</th>
    <th>能力</th>
    <th>初登場</th>
  </tr>
  <tr>
    <td><a href="sub_role.php#strong_voice" id="sub_140alpha3">大声</a></td>
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
    <td><a href="sub_role.php#no_last_words" id="sub_140alpha9">筆不精</a></td>
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
    <td><a href="sub_role.php#liar" id="sub_140alpha11">狼少年</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言時に「人⇔狼」等が入れ替わる (たまに変換されないこともある)</td>
    <td>Ver. 1.4.0 α11</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_voice" id="sub_140alpha14">臆病者</a></td>
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
    <td><a href="sub_role.php#random_luck" id="sub_140alpha15">波乱万丈</a></td>
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
    <td><a href="sub_role.php#speaker" id="sub_140alpha17">スピーカー</a></td>
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
    <td><a href="sub_role.php#weekly" id="sub_140alpha19">七曜迷彩</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言に曜日が含まれていたら曜日の順番に合わせて入れ替えられてしまう</td>
    <td>Ver. 1.4.0 α19</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_read" id="sub_140alpha21">サトラレ</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability"><a href="human.php#mind_scanner">さとり</a>に夜の発言が見られてしまう</td>
    <td>Ver. 1.4.0 α21</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_open" id="sub_140alpha22">公開者</a></td>
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
    <td><a href="sub_role.php#inside_voice" id="sub_140alpha23">内弁慶</a></td>
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
    <td><a href="sub_role.php#mind_evoke" id="sub_140beta2">口寄せ</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">死後に特定の人の遺言窓にメッセージを送れるようになる</td>
    <td>Ver. 1.4.0 β2</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_lonely" id="sub_140beta8">はぐれ者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">仲間が分からなくなり、会話できなくなる</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_sympathy">共感者</a></td>
    <td><a href="sub_role.php#mind_read_group">サトラレ系</a></td>
    <td>○</td>
    <td class="ability">お互いの役職が分かる</td>
    <td>Ver. 1.4.0 β8</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#nervy" id="sub_140beta9">自信家</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">同一陣営の人に投票するとショック死する</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#febris">熱病</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">表示された日の昼の投票集計後にショック死する</td>
    <td>Ver. 1.4.0 β9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#death_warrant" id="sub_140beta10">死の宣告</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">予告された日の昼の投票集計後にショック死する</td>
    <td>Ver. 1.4.0 β10</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#challenge_lovers" id="sub_140beta11">難題</a></td>
    <td><a href="sub_role.php#lovers_group">恋人系</a></td>
    <td>○</td>
    <td class="ability">4日目夜までは人狼の襲撃無効・毒無効・暗殺反射を持つ。<br>
      5日目以降は恋人の相方と同じ人に投票しないとショック死する。</td>
    <td>Ver. 1.4.0 β11</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#critical_voter" id="sub_140beta14">会心</a></td>
    <td><a href="sub_role.php#authority_group">権力者系</a></td>
    <td>×</td>
    <td class="ability">5% の確率で投票数が +100 される。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#critical_luck">痛恨</a></td>
    <td><a href="sub_role.php#upper_luck_group">雑草魂系</a></td>
    <td>×</td>
    <td class="ability">5% の確率で得票数が +100 される。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#whisper_ringing">囁耳鳴</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">他人の独り言が共有者の囁きに見えるようになる。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#howl_ringing">吠耳鳴</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">他人の独り言が人狼の遠吠えに見えるようになる。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#deep_sleep">爆睡者</a></td>
    <td><a href="sub_role.php#no_last_words_group">筆不精系</a></td>
    <td>○</td>
    <td class="ability">共有の囁き・人狼の遠吠えが一切見えなくなる。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#actor">役者</a></td>
    <td><a href="sub_role.php#liar_group">狼少年系</a></td>
    <td>○</td>
    <td class="ability">発言時に一部のキーワードが入れ替わる。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#androphobia">男性恐怖症</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に男性に投票するとショック死します。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#gynophobia">女性恐怖症</a></td>
    <td><a href="sub_role.php#chicken_group">小心者系</a></td>
    <td>○</td>
    <td class="ability">昼の投票時に女性に投票するとショック死します。</td>
    <td>Ver. 1.4.0 β14</td>
  </tr>
</table>

<h2><a id="reference">参考リンク</a></h2>
<pre>
作って欲しい役職などがあったらこちらのスレへどうぞ
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50" target="_top">【ネタ歓迎】あったらいいな、こんな役職【ガチ大歓迎】</a>

参考スレッド
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/48159/1243197597/l50" target="_top">新役職提案スレッド＠やる夫</a>
</pre>

<h2><a id="memo">作成予定メモ</a></h2>
<h3>基本作成方針</h3>
<ol>
<li>単独で出現しても勝利可能</li>
<li>複数出現しても破綻しない (神話マニア対応)</li>
<li>勝利陣営は二日目朝の時点で決定する</li>
<li>村の勝利を阻害する存在なら占い・霊能のどちらかで分かる</li>
<li>[能力を騙れる / 対応・排除が容易 / 発動率が低い] のどれかを満たす</li>
<li>名称は和名・妖怪を軸にする</li>
</ol>
</ol>
<h3>役職</h3>
<h4>能力リスト</h4>
<pre>
○耐毒者 (村人陣営 / 薬師系？)
・毒がいっさい効かない村人

○仕事人 (村人陣営 / 暗殺系)
・村人陣営を殺したら能力を失う (結果として人外を殺した事が自覚できる)

○嫉狐 (妖狐陣営 / 妖狐系？)
・橋姫能力をもった妖狐
</pre>

<h4>名称リスト</h4>
<pre>
○泥棒 (レス 13)

○祟神 / ミシャグジさま (レス 19 / 33)

○宣教師 (レス 86)

○従者 (レス 13 / 64)

○蟲師 (レス 68)
</pre>

<h3>村編成案</h3>
<h4>採用予定</h4>
<pre>
グレラン村 (レス 65)
</pre>
</body></html>
