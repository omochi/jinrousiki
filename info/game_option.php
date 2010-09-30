<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('CAST_CONF', 'GAME_OPT_CAPT');
OutputInfoPageHeader('ゲームオプション');
?>
<p>
<a href="#basic_option">基本設定</a>
<a href="#dummy_boy_option">身代わり君設定</a>
<a href="#open_cast_option">霊界公開設定</a>
<a href="#add_role_option">追加役職設定</a>
<a href="#special_option">特殊村設定</a>
<a href="#special_role_option">特殊配役設定</a>
</p>

<h2><a id="basic_option">基本設定</a></h2>
<ul>
  <li><a href="#wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a></li>
  <li><a href="#real_time"><?php echo $GAME_OPT_MESS->real_time ?></a></li>
  <li><a href="#wait_morning"><?php echo $GAME_OPT_MESS->wait_morning ?></a></li>
  <li><a href="#open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a></li>
  <li><a href="#open_day"><?php echo $GAME_OPT_MESS->open_day ?></a></li>
</ul>

<h3><a id="wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a></h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->wish_role ?></li>
  <li>村人登録 (プレイヤー登録) の際になりたい役職を選択することができます</li>
  <li>オプションの組み合わせによって希望できる役職の数や種類が違います</li>
</ul>

<h3><a id="real_time"><?php echo $GAME_OPT_MESS->real_time ?></a></h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->real_time ?></li>
  <li>昼と夜を個別に指定できます</li>
</ul>

<h3><a id="wait_morning"><?php echo $GAME_OPT_MESS->wait_morning ?></a> [Ver. 1.4.0 β17〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->wait_morning ?></li>
  <li>発言が制限されている間は画面の上方に「待機時間中です」という趣旨のメッセージが表示されます</li>
</ul>

<h3><a id="open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a></h3>
<ul>
  <li>昼の処刑投票数が公開されます</li>
  <li><?php echo $GAME_OPT_CAPT->open_vote ?></li>
</ul>

<h3><a id="open_day"><?php echo $GAME_OPT_MESS->open_day ?></a> [Ver. 1.4.0 β12〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->open_day ?></li>
  <li>自分の役職は分かりますが1日目昼は投票できません</li>
  <li>制限時間を過ぎたら自動で夜に切り替わります (通常時のスタート相当)</li>
</ul>


<h2><a id="dummy_boy_option">身代わり君設定</a></h2>
<ul>
  <li><a href="#dummy_boy"><?php echo $GAME_OPT_MESS->dummy_boy ?></a></li>
  <li><a href="#gm_login"><?php echo $GAME_OPT_MESS->gm_login ?></a></li>
  <li><a href="#gerd"><?php echo $GAME_OPT_MESS->gerd ?></a></li>
</ul>

<h3><a id="dummy_boy"><?php echo $GAME_OPT_MESS->dummy_boy ?></a></h3>
<ul>
  <li>初日の夜、身代わり君が狼に食べられます</li>
  <li>身代わり君がなれる役職には制限があります</li>
  <li>身代わり君は、基本的には能力は発動しません</li>
</ul>

<h3><a id="gm_login"><?php echo $GAME_OPT_MESS->gm_login ?></a> [Ver. 1.4.0 α18〜]</h3>
<ul>
  <li>仮想 GM が身代わり君としてログインします</li>
  <li>村を立てる際にログインパスワードを入力します</li>
  <li>身代わり君のユーザ名は「dummy_boy」です</li>
</ul>

<h3><a id="gerd"><?php echo $GAME_OPT_MESS->gerd ?></a> [Ver. 1.4.0 β12〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->gerd ?></li>
  <li><a href="#chaos">闇鍋モード</a>の固定配役に村人を一人追加します</li>
  <li><a href="#mania">神話マニア</a>オプションが付いていても村人を一人確保します</li>
  <li><a href="#duel">決闘村</a>・<a href="#festival">お祭り村</a>の配役は入れ替えません (最初から存在する場合のみ有効です)</li>
</ul>


<h2><a id="open_cast_option">霊界公開設定</a></h2>
<ul>
  <li><a href="#open_cast">常時霊界公開</a></li>
  <li><a href="#not_open_cast"><?php echo $GAME_OPT_MESS->not_open_cast ?></a></li>
  <li><a href="#auto_open_cast"><?php echo $GAME_OPT_MESS->auto_open_cast ?></a></li>
</ul>

<h3><a id="open_cast">常時霊界公開</a></h3>
<ul>
  <li>常に霊界で配役が公開されます</li>
  <li>蘇生能力は無効になります</li>
  <li>システム的にはこれが初期状態です (アイコン表示はありません)</li>
</ul>

<h3><a id="not_open_cast"><?php echo $GAME_OPT_MESS->not_open_cast ?></a></h3>
<ul>
  <li>誰がどの役職なのかが公開されません</li>
  <li>蘇生能力は有効になります</li>
</ul>

<h3><a id="auto_open_cast"><?php echo $GAME_OPT_MESS->auto_open_cast ?></a> [Ver. 1.4.0 β3〜]</h3>
<ul>
  <li>蘇生能力者などが能力を持っている間だけ霊界が非公開になります</li>
</ul>


<h2><a id="add_role_option">追加役職設定</a></h2>
<ul>
  <li><a href="#poison"><?php echo $GAME_OPT_MESS->poison ?></a></li>
  <li><a href="#assassin"><?php echo $GAME_OPT_MESS->assassin ?></a></li>
  <li><a href="#boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a></li>
  <li><a href="#poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a></li>
  <li><a href="#possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a></li>
  <li><a href="#sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a></li>
  <li><a href="#cupid"><?php echo $GAME_OPT_MESS->cupid ?></a></li>
  <li><a href="#medium"><?php echo $GAME_OPT_MESS->medium ?></a></li>
  <li><a href="#mania"><?php echo $GAME_OPT_MESS->mania ?></a></li>
  <li><a href="#decide"><?php echo $GAME_OPT_MESS->decide ?></a></li>
  <li><a href="#authority"><?php echo $GAME_OPT_MESS->authority ?></a></li>
</ul>

<h3><a id="poison"><?php echo $GAME_OPT_MESS->poison ?></a></h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->poison ?>人以上になったら<a href="new_role/human.php#poison">埋毒者</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->poison ?></li>
</ul>

<h3><a id="assassin"><?php echo $GAME_OPT_MESS->assassin ?></a> [Ver. 1.4.0 β4〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->assassin ?>人以上になったら<a href="new_role/human.php#assassin">暗殺者</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->assassin ?></li>
</ul>

<h3><a id="boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a> [Ver. 1.4.0 α3-7〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->boss_wolf ?>人以上になったら<a href="new_role/wolf.php#boss_wolf">白狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->boss_wolf ?></li>
</ul>

<h3><a id="poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->poison_wolf ?>人以上になったら<a href="new_role/wolf.php#poison_wolf">毒狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->poison_wolf ?></li>
</ul>

<h3><a id="possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a> [Ver. 1.4.0 β4〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->possessed_wolf ?>人以上になったら<a href="new_role/wolf.php#possessed_wolf">憑狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->possessed_wolf ?></li>
</ul>

<h3><a id="sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a> [Ver. 1.4.0 β9〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->sirius_wolf ?>人以上になったら<a href="new_role/wolf.php#sirius_wolf">天狼</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->sirius_wolf ?></li>
</ul>

<h3><a id="cupid"><?php echo $GAME_OPT_MESS->cupid ?></a> [Ver. 1.2.0〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->cupid ?>人以上になったら<a href="new_role/lovers.php#cupid">キューピッド</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->cupid ?></li>
  <li>Ver. 1.4.0 β17 から「14人」の固定出現を外しました</li>
</ul>

<h3><a id="medium"><?php echo $GAME_OPT_MESS->medium ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->medium ?>人以上になったら<a href="new_role/human.php#medium">巫女</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->medium ?></li>
</ul>

<h3><a id="mania"><?php echo $GAME_OPT_MESS->mania ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->mania ?>人以上になったら<a href="new_role/mania.php#mania">神話マニア</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->mania ?></li>
</ul>

<h3><a id="decide"><?php echo $GAME_OPT_MESS->decide ?></a></h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->decide ?>人以上になったら<a href="new_role/sub_role.php#decide">決定者</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->decide ?></li>
  <li>自分が決定者であることはわかりません</li>
</ul>

<h3><a id="authority"><?php echo $GAME_OPT_MESS->authority ?></a></h3>
<ul>
  <li>村の人口が<?php echo $CAST_CONF->authority ?>人以上になったら<a href="new_role/sub_role.php#authority">権力者</a>が登場します</li>
  <li><?php echo $GAME_OPT_CAPT->authority ?></li>
  <li>自分が権力者であることはわかります</li>
</ul>


<h2><a id="special_option">特殊村設定</a></h2>
<ul>
  <li><a href="#liar"><?php echo $GAME_OPT_MESS->liar ?></a></li>
  <li><a href="#gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a></li>
  <li><a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a></li>
  <li><a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a></li>
  <li><a href="#critical"><?php echo $GAME_OPT_MESS->critical ?></a></li>
  <li><a href="#detective"><?php echo $GAME_OPT_MESS->detective ?></a></li>
  <li><a href="#festival"><?php echo $GAME_OPT_MESS->festival ?></a></li>
  <li><a href="#replace_human"><?php echo $GAME_OPT_MESS->replace_human ?></a></li>
  <li><a href="#full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a></li>
  <li><a href="#full_chiroptera"><?php echo $GAME_OPT_MESS->full_chiroptera ?></a></li>
  <li><a href="#full_cupid"><?php echo $GAME_OPT_MESS->full_cupid ?></a></li>
</ul>

<h3><a id="liar"><?php echo $GAME_OPT_MESS->liar ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li>全ユーザに一定の確率 (70% 程度) で<a href="new_role/sub_role.php#liar">狼少年</a>がつきます</li>
</ul>

<h3><a id="gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->gentleman ?></li>
  <li><a href="new_role/sub_role.php#gentleman">紳士</a>・<a href="new_role/sub_role.php#gentleman">淑女</a>の発動率はランダム付加の場合と同じです</li>
  <li><a href="#chaos">闇鍋モード</a>でランダムに付加される時は個々の性別を参照していません</li>
</ul>

<h3><a id="sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a> [Ver. 1.4.0 α14〜]</h3>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#chicken_group">小心者系</a>のどれかがつきます</li>
  <li>配役制限がついているもの (例：<a href="new_role/sub_role.php#panelist">解答者</a>) はつきません</li>
  <li><a href="new_role/sub_role.php#impatience">短気</a>がつくのは最大で一人です</li>
  <li><a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a>と併用できません</li>
</ul>

<h3><a id="perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a> [Ver. 1.4.0 α19〜]</h3>
<ul>
  <li>全ユーザに<a href="new_role/sub_role.php#perverseness">天の邪鬼</a>がつきます</li>
  <li><a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a>と併用できません</li>
</ul>

<h3><a id="critical"><?php echo $GAME_OPT_MESS->critical ?></a> [Ver. 1.4.0 β15〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->critical ?></li>
  <li><a href="new_role/sub_role.php#critical_voter">会心</a>・<a href="new_role/sub_role.php#critical_luck">痛恨</a>の発動率はランダム付加の場合と同じです</li>
</ul>

<h3><a id="detective"><?php echo $GAME_OPT_MESS->detective ?></a> [Ver. 1.4.0 β10〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->detective ?></li>
  <li>普通村の場合は、共有者がいれば共有者を、いなければ村人を一人<a href="new_role/human.php#detective_common">探偵</a>に入れ替えます</li>
  <li><a href="#chaos">闇鍋モード</a>の場合は固定枠に<a href="new_role/human.php#detective_common">探偵</a>が追加されます</li>
  <li>このオプションを使用した場合は、身代わり君が<a href="new_role/human.php#detective_common">探偵</a>にはなりません</li>
  <li>「<a href="#gm_login">身代わり君はGM</a>」+「<a href="#not_open_cast">霊界を常時非公開</a>」オプションと併用すると「霊界探偵モード」になります</li>
  <li>「霊界探偵モード」はゲーム開始直後に探偵が死亡して、霊界に移動します。指示は GM 経由で行います</li>
</ul>

<h3><a id="festival"><?php echo $GAME_OPT_MESS->festival ?></a> [Ver. 1.4.0 β9〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->festival ?></li>
  <li>初期設定では、以下に示す人数の範囲だけ、固定編成になります</li>
  <li>編成の初期設定はバージョンアップ時に変更される事があります</li>
</ul>
<pre>
 8人：村人2　占い師1　霊能者1　人狼1　狂人1　囁き狂人1　妖狐1
 9人：村人3　狩人3　人狼2　蝙蝠1
10人：村人2　逃亡者1　占い師1　霊能者1　狩人1　人狼2　狂人1　妖狐1
11人：賢狼1　月兎7　九尾2　妖精1
12人：村人5　占い師1　霊能者1　狩人1　人狼2　狂人1　吸血鬼1
13人：村人4　占い師1　霊能者1　狩人1　上海人形1　人形遣い1　人狼2　狂信者1　蝙蝠1
14人：霊能1　銀狼2　妖狐1　蝙蝠10
15人：埋毒者3　人狼3　狂信者1　妖狐1　蝙蝠6　大蝙蝠1
16人：村人6　占い師1　霊能者1　狩人1　共有者2　人狼3　囁き狂人1　妖狐1
17人：夢守人1　強毒者1　夢毒者5　天狼3　獏1　小悪魔1　鏡妖精5
18人：村人7　占い師1　霊能者1　狩人1　共有者2　人狼3　狂人1　妖狐1　吸血鬼1
19人：ひよこ鑑定士1　霊能者1　狩人1　共有者2　人狼2　金狼1　狂人1　妖狐1　雛狐1　蝙蝠7　大蝙蝠1
20人：蒼狼1　翠狼1　銀狼2　呪術師2　蒼狐1　翠狐1　銀狐1　蝙蝠5　大蝙蝠1　妖精5
21人：埋毒者7　連毒者2　毒狼4　抗毒狼1　管狐2　出題者3　毒蝙蝠2
22人：村人8　占い師1　霊能者1　狩人1　共有者2　猫又1　人狼4　白狼1　狂人1　妖狐1　子狐1

出展：
 9人：狩人村 (特殊F) ＠桃栗鯖
10人：逃亡者村 (特殊R) ＠桃栗鯖
13人：奴隷村＠世紀末鯖
15人：マインスイーパ村＠世紀末鯖
16人：囁き狂人村＠人狼 BBS C国
22人：バルサン村＠わかめて鯖
</pre>

<h3><a id="replace_human"><?php echo $GAME_OPT_MESS->replace_human ?></a> [Ver. 1.4.0 β14〜]</h3>
<ul>
  <li><?php echo $GAME_OPT_CAPT->replace_human ?></li>
  <li><a href="#full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a>を拡張して実装したオプションです</li>
  <li>表記が村人となる役職が存在する事に注意してください</li>
  <li>「<?php echo $GAME_OPT_MESS->replace_human ?>」は管理人がカスタムすることを前提にしたオプションです<br>
    現在の初期設定は全員<a href="new_role/human.php#escaper">逃亡者</a>になります
  </li>
</ul>

<h4><a id="full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a> [Ver. 1.4.0 α17〜]</h4>
<ul>
  <li>村人が全員<a href="new_role/mania.php#mania">神話マニア</a>になります</li>
</ul>

<h4><a id="full_chiroptera"><?php echo $GAME_OPT_MESS->full_chiroptera ?></a> [Ver. 1.4.0 β14〜]</h4>
<ul>
  <li>村人が全員<a href="new_role/chiroptera.php#chiroptera">蝙蝠</a>になります</li>
</ul>

<h4><a id="full_cupid"><?php echo $GAME_OPT_MESS->full_cupid ?></a> [Ver. 1.4.0 β14〜]</h4>
<ul>
  <li>村人が全員<a href="new_role/lovers.php#cupid">キューピッド</a>になります</li>
</ul>


<h2><a id="special_role_option">特殊配役設定</a></h2>
<ul>
  <li><a href="#special_role"><?php echo $GAME_OPT_MESS->special_role ?></a></li>
  <li><a href="#chaos"><?php echo $GAME_OPT_MESS->chaos ?></a></li>
  <li><a href="#duel"><?php echo $GAME_OPT_MESS->duel ?></a></li>
  <li><a href="#gray_random"><?php echo $GAME_OPT_MESS->gray_random ?></a></li>
  <li><a href="#quiz"><?php echo $GAME_OPT_MESS->quiz ?></a></li>
</ul>

<h3><a id="special_role"><?php echo $GAME_OPT_MESS->special_role ?></a> [Ver. 1.4.0 β17〜]</h3>
<ul>
  <li>専用の配役テーブルを用いた特殊設定村です</li>
  <li>詳細は個々のモードを参照してください</li>
</ul>

<h4><a id="chaos"><?php echo $GAME_OPT_MESS->chaos ?></a> [Ver. 1.4.0 α1〜]</h4>
<ul>
  <li>専用ページを参照して下さい → <a href="chaos.php"><?php echo $GAME_OPT_MESS->chaos ?></a></li>
</ul>

<h4><a id="duel"><?php echo $GAME_OPT_MESS->duel ?></a> [Ver. 1.4.0 α19〜]</h4>
<ul>
  <li><a href="#open_cast_option">霊界公開設定オプション</a>の設定によって配役が変わります。初期設定は以下です</li>
  <ol>
    <li><a href="#open_cast">常時公開</a>：暗殺者ベース</li>
    <li><a href="#not_open_cast">非公開</a>：埋毒者ベース</li>
    <li><a href="#auto_open_cast">自動公開</a>：キューピッドベース</li>
  </ol>
</ul>

<h4><a id="gray_random"><?php echo $GAME_OPT_MESS->gray_random ?></a> [Ver. 1.4.0 β17〜]</h4>
<ul>
  <li>配役が基本職のみになります。初期設定は以下です。</li>
  <ol>
    <li>人狼系 → 人狼</li>
    <li>狂人系 → 狂人</li>
    <li>妖狐陣営 → 妖狐</li>
    <li>上記以外 → 村人</li>
  </ol>
</ul>

<h4><a id="quiz"><?php echo $GAME_OPT_MESS->quiz ?></a> [Ver. 1.4.0 α2〜]</h4>
<ul>
  <li>GM が<a href="new_role/quiz.php#quiz">出題者</a>になります</li>
  <li>村を立てる際には GM ログインパスワードを設定する必要があります</li>
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
</body></html>
