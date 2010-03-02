<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_OPT_CAPT');
OutputInfoPageHeader('ゲームオプション');
?>
<p>
<a href="#liar"><?= $GAME_OPT_MESS->liar ?></a>
<a href="#gentleman"><?= $GAME_OPT_MESS->gentleman ?></a>
<a href="#sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a>
<a href="#perverseness"><?= $GAME_OPT_MESS->perverseness ?></a>
<a href="#full_mania"><?= $GAME_OPT_MESS->full_mania ?></a>
<a href="#quiz"><?= $GAME_OPT_MESS->quiz ?></a>
<a href="#duel"><?= $GAME_OPT_MESS->duel ?></a>
</p>

<h2><a name="liar"><?= $GAME_OPT_MESS->liar ?></a></h2>
<ul>
  <li>全ユーザに一定の確率 (70% 程度) で<a href="new_role/sub_role.php#liar">狼少年</a>がつきます</li>
</ul>

<h2><a name="gentleman"><?= $GAME_OPT_MESS->gentleman ?></a></h2>
<ul>
  <li>全ユーザに登録時の性別に応じた<a href="new_role/sub_role.php#gentleman">紳士</a>か<a href="new_role/sub_role.php#gentleman">淑女</a>がつきます</li>
  <li>闇鍋モードでランダムに付加される時は個々の性別を参照していません</li>
  <li>発動率はランダム付加の場合と同じです</li>
</ul>

<h2><a name="sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a></h2>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#chicken_group">小心者系</a>のどれかがつきます</li>
  <li><a href="new_role/sub_role.php#impatience">短気</a>がつくのは最大で一人です</li>
  <li><a href="new_role/sub_role.php#panelist">解答者</a>はつきません (ついたらバグです)</li>
  <li><a href="#perverseness"><?= $GAME_OPT_MESS->perverseness ?></a>と併用できません</li>
</ul>

<h2><a name="perverseness"><?= $GAME_OPT_MESS->perverseness ?></a></h2>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#perverseness">天の邪鬼</a>がつきます</li>
  <li><a href="#sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a>と併用できません</li>
</ul>

<h2><a name="full_mania"><?= $GAME_OPT_MESS->full_mania ?></a></h2>
<ul>
  <li>村人が全員<a href="new_role/human.php#mania">神話マニア</a>になります</li>
  <li>表記が村人となる役職が存在する事に注意してください</li>
</ul>

<h2><a name="quiz"><?= $GAME_OPT_MESS->quiz ?></a></h2>
<ul>
  <li>GM が<a href="new_role/quiz.php#quiz">出題者</a>になります</li>
  <li>GM もゲーム開始投票をする必要があります</li>
  <li>出現役職は村人、共有者、人狼、狂人、妖狐です</li>
  <li>GM 以外の全員に<a href="new_role/sub_role.php#panelist">解答者</a>がつきます</li>
  <li>人狼は常時 GM しか狙えません</li>
  <li>GM は噛まれても殺されません</li>
  <li>以下のような使い方を想定しています</li>
  <ol>
    <li>GM がクイズを出題してゲーム開始</li>
    <li>人狼が適当なタイミングで GM を噛む</li>
    <li>夜が明けたらユーザが解答する</li>
    <li>全員解答したら GM が正解発表</li>
    <li>ユーザは間違っていたら GM に投票、正解なら GM 以外に投票</li>
    <li>GM は正解者の中で一番解答が遅かった人に投票</li>
    <li>GM は日が暮れる前に次の問題を出題する</li>
    <li>以下、勝敗が決まるまで繰り返す</li>
  </ol>
</ul>

<h2><a name="duel"><?= $GAME_OPT_MESS->duel ?></a></h2>
<ul>
  <li><a href="new_role/human.php#assassin">暗殺者</a>、人狼、<a href="new_role/wolf.php#trap_mad">罠師</a>しか出現しません
</ul>
</body></html>
