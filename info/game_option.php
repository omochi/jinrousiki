<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('CAST_CONF', 'GAME_OPT_CAPT');
OutputInfoPageHeader('ゲームオプション');
?>
<p>
<a href="#wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a>
<a href="#real_time"><?php echo $GAME_OPT_MESS->real_time ?></a>
<a href="#liar"><?php echo $GAME_OPT_MESS->liar ?></a>
<a href="#open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a>
</p>
<p>
<a href="#poison"><?php echo $GAME_OPT_MESS->poison ?></a>
<a href="#assassin"><?php echo $GAME_OPT_MESS->assassin ?></a>
<a href="#boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a>
<a href="#poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a>
<a href="#possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a>
<a href="#sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a>
<a href="#cupid"><?php echo $GAME_OPT_MESS->cupid ?></a>
<a href="#medium"><?php echo $GAME_OPT_MESS->medium ?></a>
<a href="#mania"><?php echo $GAME_OPT_MESS->mania ?></a>
<a href="#decide"><?php echo $GAME_OPT_MESS->decide ?></a>
<a href="#authority"><?php echo $GAME_OPT_MESS->authority ?></a>
</p>
<p>
<a href="#gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a>
<a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a>
<a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a>
<a href="#full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a>
<a href="#detective"><?php echo $GAME_OPT_MESS->detective ?></a>
<a href="#festival"><?php echo $GAME_OPT_MESS->festival ?></a>
</p>
<p>
<a href="#quiz"><?php echo $GAME_OPT_MESS->quiz ?></a>
<a href="#duel"><?php echo $GAME_OPT_MESS->duel ?></a>
</p>

<h2><a name="wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a></h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->wish_role ?></li>
  <li>村人登録 (プレイヤー登録) の際になりたい役職を選択することができます</li>
  <li>オプションの組み合わせによって希望できる役職の数や種類が違います</li>
</ul>

<h2><a name="real_time"><?php echo $GAME_OPT_MESS->real_time ?></a></h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->real_time ?></li>
  <li>昼と夜を個別に指定できます</li>
</ul>

<h2><a name="open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a></h2>
<ul>
  <li>昼の処刑投票数が公開されます</li>
  <li><?php echo $GAME_OPT_CAPT->open_vote ?></li>
</ul>

<h2><a name="poison"><?php echo $GAME_OPT_MESS->poison ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->poison ?>人以上になったら埋毒者が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->poison ?></li>
</ul>
<h2><a name="assassin"><?php echo $GAME_OPT_MESS->assassin ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->assassin ?>人以上になったら<a href="new_role/human.php#assassin">暗殺者</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->assassin ?></li>
</ul>
</p>
<p>
<h2><a name="boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->boss_wolf ?>人以上になったら<a href="new_role/wolf.php#boss_wolf">白狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->boss_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->poison_wolf ?>人以上になったら<a href="new_role/wolf.php#poison_wolf">毒狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->poison_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->possessed_wolf ?>人以上になったら<a href="new_role/wolf.php#possessed_wolf">憑狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->possessed_wolf ?></li>
</ul>
</p>
<h2><a name="sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->sirius_wolf ?>人以上になったら<a href="new_role/wolf.php#sirius_wolf">天狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->sirius_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="cupid"><?php echo $GAME_OPT_MESS->cupid ?></a></h2>
<ul>
  <li>村の人口が14人もしくは<?php echo $CAST_CONF->cupid ?>人以上になったらキューピッドが登場します</li>
  <li><?php echo $GAME_OPT_CAPT->cupid ?></li>
</ul>
</p>
<p>
<h2><a name="medium"><?php echo $GAME_OPT_MESS->medium ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->medium ?>人以上になったら<a href="new_role/human.php#medium">巫女</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->medium ?></li>
</ul>
</p>
<p>
<h2><a name="mania"><?php echo $GAME_OPT_MESS->mania ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->mania ?>人以上になったら<a href="new_role/human.php#mania">神話マニア</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->mania ?></li>
</ul>
</p>
<p>
<h2><a name="decide"><?php echo $GAME_OPT_MESS->decide ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->decide ?>人以上になったら決定者が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->decide ?></li>
  <li>自分が決定者であることはわかりません</li>
</ul>
</p>
<p>
<h2><a name="authority"><?php echo $GAME_OPT_MESS->authority ?></a></h2>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->authority ?>人以上になったら権力者が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->authority ?></li>
  <li>自分が権力者であることはわかります</li>
</ul>

<h2><a name="liar"><?php echo $GAME_OPT_MESS->liar ?></a></h2>
<ul>
  <li>全ユーザに一定の確率 (70% 程度) で<a href="new_role/sub_role.php#liar">狼少年</a>がつきます</li>
</ul>

<h2><a name="gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a></h2>
<ul>
  <li>全ユーザに登録時の性別に応じた<a href="new_role/sub_role.php#gentleman">紳士</a>か<a href="new_role/sub_role.php#gentleman">淑女</a>がつきます</li>
  <li>闇鍋モードでランダムに付加される時は個々の性別を参照していません</li>
  <li>発動率はランダム付加の場合と同じです</li>
</ul>

<h2><a name="sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a></h2>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#chicken_group">小心者系</a>のどれかがつきます</li>
  <li><a href="new_role/sub_role.php#impatience">短気</a>がつくのは最大で一人です</li>
  <li><a href="new_role/sub_role.php#panelist">解答者</a>はつきません (ついたらバグです)</li>
  <li><a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a>と併用できません</li>
</ul>

<h2><a name="perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a></h2>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#perverseness">天の邪鬼</a>がつきます</li>
  <li><a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a>と併用できません</li>
</ul>

<h2><a name="full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a></h2>
<ul>
  <li>村人が全員<a href="new_role/human.php#mania">神話マニア</a>になります</li>
  <li>表記が村人となる役職が存在する事に注意してください</li>
</ul>

<h2><a name="detective"><?php echo $GAME_OPT_MESS->detective ?></a></h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->detective ?></li>
  <li>普通村の場合は、共有者がいれば共有者を、いなければ村人を一人<a href="new_role/human.php#detective_common">探偵</a>に入れ替えます</li>
  <li>真・闇鍋モードの場合は固定枠に<a href="new_role/human.php#detective_common">探偵</a>が追加されます</li>
  <li>このオプションを使用した場合は、身代わり君が<a href="new_role/human.php#detective_common">探偵</a>にはなりません</li>
  <li>「身代わり君はGM」+「霊界を常時非公開」オプションと併用すると「霊界探偵モード」になります</li>
  <li>「霊界探偵モード」はゲーム開始直後に探偵が死亡して、霊界に移動します。指示は GM 経由で行います</li>
</ul>

<h2><a name="festival"><?php echo $GAME_OPT_MESS->festival ?></a></h2>
<ul>
  <li>管理人がカスタムする特殊設定です</li>
  <li>初期設定では、以下に示す人数の範囲だけ、固定編成になります</li>
</ul>
<pre>
 8人：村人1　占い師1　霊能者1　狩人1　白狼1　狂人1　白狐1　蝙蝠1
 9人：狩人2　夢守人4　人狼1　銀狼1　天狐1
10人：占い師1　霊能者1　狩人1　上海人形1　人形遣い1　人狼2　狂人1　妖狐1　蝙蝠1
11人：無意識1　魂の占い師1　雲外鏡1　預言者1　狩人1　厄神1　河童1　呪狼1　銀狼1　月兎1　呪蝙蝠1
12人：賢狼1　月兎8　九尾2　妖精1
13人：村人1　占い師1　精神鑑定士1　夢見人1　霊能者1　夢枕人1　狩人1　夢守人1　共有者1　人狼1　毒狼1　罠師1　呪蝙蝠1
14人：霊能1　銀狼2　妖狐1　蝙蝠10
15人：埋毒者3　人狼3　狂信者1　妖狐1　蝙蝠6　大蝙蝠1
16人：夢守人1　強毒者1　夢毒者5　天狼3　獏1　小悪魔1　鏡妖精4
17人：ひよこ鑑定士1　霊能者1　狩人1　共有者2　人狼2　金狼1　狂人1　妖狐1　蝙蝠7
18人：聖女1　魂の占い師1　雲外鏡1　忍者1　策士1　亡霊嬢1　潜毒者1　反魂師1　賢狼1　憑狼1　天狼1　月兎1　呪術師1　九尾1　仙狐1　天使1　光妖精1　奇術師1
19人：天人1　厄神1　夢毒者1　猫又1　蝕暗殺者2　橋姫1　毒狼1　憑狼1　天狼1　狂信者1　扇動者1　天狐2　女神1　出題者1　光妖精1　闇妖精1　鏡妖精1
20人：蒼狼1　翠狼1　銀狼2　呪術師2　蒼狐1　翠狐1　銀狐1　蝙蝠5　大蝙蝠1　妖精5
21人：埋毒者7　連毒者2　毒狼4　抗毒狼1　管狐2　出題者3　毒蝙蝠2
22人：村人8　占い師1　霊能者1　狩人1　共有者2　猫又1　人狼4　白狼1　狂人1　妖狐1　子狐1
出展：
10人：奴隷村＠世紀末鯖
15人：マインスイーパ村＠世紀末鯖
22人：バルサン村＠わかめて鯖
</pre>
<h2><a name="quiz"><?php echo $GAME_OPT_MESS->quiz ?></a></h2>
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

<h2><a name="duel"><?php echo $GAME_OPT_MESS->duel ?></a></h2>
<ul>
  <li><a href="new_role/human.php#assassin">暗殺者</a>、人狼、<a href="new_role/wolf.php#trap_mad">罠師</a>しか出現しません</li>
  <li>「霊界で配役を公開しない」オプションの設定によって配役が変わります。デフォルトは以下です</li>
  <ol>
    <li>常時公開：暗殺者ベース</li>
    <li>自動公開：キューピッドベース</li>
    <li>非公開：埋毒者ベース</li>
  </ol>
</ul>
</body></html>
