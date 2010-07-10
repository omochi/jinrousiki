<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('MESSAGE');
OutputInfoPageHeader('詳細な仕様');
?>
<p>
<a href="#decide_role">配役決定ルーチン</a>
<a href="#dummy_boy">身代わり君 (GM)</a>
<a href="#dead">死因一覧</a>
<a href="#vote">投票</a>
<a href="#revive_refuse">蘇生辞退システム</a>
</p>

<h2><a name="decide_role">配役決定ルーチン</a></h2>
<p>
<a href="#decide_role_room">村</a>
<a href="#decide_role_dummy_boy">身代わり君</a>
<a href="#decide_role_user">ユーザ</a>
</p>

<h3><a name="decide_role_room">村</a></h3>
</p>
<ol>
<li>参加人数を取得</li>
<li>人数毎に設定されている配役データを取得 (<a href="../rule.php" target="_top">ルール</a>参照)</li>
<li>特殊村なら全て差し替える</li>
<li>通常村ならゲームオプションに応じて個別に入れ替える</li>
<li>配役決定</li>
</ol>

<h3><a name="decide_role_dummy_boy">身代わり君</a></h3>
<ol>
<li>配役を取得</li>
<li>ランダムな配役リストを作る</li>
<li>身代わり君がなれる役職に当たるまで先頭からチェック</li>
<li>全てチェックして見つからなければエラーを返す</li>
<li>配役決定
</ol>

<h3><a name="decide_role_user">ユーザ</a></h3>
<ol>
<li>身代わり君の配役を決定してユーザリストから「決定済みリスト」へ移動</li>
<li>ランダムなユーザリストを作る</li>
<li>リストの先頭の人の希望役職を確認</li>
<li>何か希望してて空きがあればその人の役が決定、「決定済みリスト」へ移動</li>
<li>希望なしか空きがなければ「未決定リスト」へ移動</li>
<li>全部振り終えたら「未決定リスト」の人に余りを割り振る</li>
</ol>

<h2><a name="dummy_boy">身代わり君 (GM) の仕様</a></h2>
<ul>
<li>常時、ゲーム終了後相当の情報が見えます</li>
<li>ゲーム開始前のユーザの「役職」は「希望役職」です</li>
<li>単独の KICK 投票でユーザを蹴りだします</li>
<li>ゲーム中は「遺言」発言をすると専用システムメッセージになります</li>
<li>投票能力がある役職であっても投票することはできません</li>
</ul>

<h2><a name="dead">死因一覧</a></h2>
<p>
<a href="#dead_common">共通</a>
<a href="#dead_day">昼</a>
<a href="#dead_night">夜</a>
</p>

<h3><a name="dead_common">共通</a></h3>
<h4>〜<?php echo $MESSAGE->sudden_death ?></h4>
<ul>
<li>突然死 (投票忘れ)</li>
</ul>

<h4>〜<?php echo $MESSAGE->lovers_followed ?></h4>
<ul>
<li>後追い (恋人)</li>
</ul>


<h3><a name="dead_day">昼</a></h3>
<h4>〜<?php echo $MESSAGE->vote_killed ?></h4>
<ul>
<li>処刑 (昼の投票)</li>
</ul>

<h4>〜<?php echo $MESSAGE->deadman ?></h4>
<ul>
<li>毒 (<a href="new_role/human.php#poison_group">埋毒者系</a>)</li>
<li>罠 (<a href="new_role/human.php#trap_common">策士</a>)</li>
</ul>

<h4>〜<?php echo $MESSAGE->vote_sudden_death ?></h4>
<ul>
<li>ショック死 (<a href="new_role/sub_role.php#chicken_group">小心者系</a>・<a href="new_role/human.php#jealousy">橋姫</a>・<a href="new_role/wolf.php#agitate_mad">扇動者</a>)</li>
</ul>

<h3><a name="dead_night">夜</a></h3>
<h4>〜<?php echo $MESSAGE->deadman ?></h4>
<ul>
<li>襲撃 (<a href="new_role/wolf.php#wolf_group">人狼系</a>)</li>
<li>餓狼襲撃 (<a href="new_role/wolf.php#hungry_wolf">餓狼</a>)</li>
<li>毒 (<a href="new_role/human.php#poison_group">埋毒者系</a>)</li>
<li>罠 (<a href="new_role/wolf.php#trap_mad">罠師</a>)</li>
<li><a href="new_role/human.php#guard_hunt">狩り</a> (<a href="new_role/human.php#guard_group">狩人系</a>)</li>
<li>暗殺 (<a href="new_role/human.php#assassin_group">暗殺者系</a>)</li>
<li>夢食い (<a href="new_role/wolf.php#dream_eater_mad">獏</a>)</li>
<li>呪殺 (<a href="new_role/human.php#mage_group">占い師系</a>)</li>
<li>呪返し (<a href="new_role/wolf.php#cursed_wolf">呪狼</a>などの呪い持ち、<a href="new_role/wolf.php#voodoo_mad">呪術師</a>などの呪い能力者)</li>
<li>憑依 (<a href="new_role/wolf.php#possessed_wolf">憑狼</a>など)</li>
<li>憑依解放 (<a href="new_role/human.php#anti_voodoo">厄神</a>)</li>
<li>帰還 (<a href="new_role/human.php#revive_priest">天人</a>)</li>
<li>人外尾行 (<a href="new_role/human.php#reporter">ブン屋</a>)</li>
<li>身代わり (<a href="new_role/human.php#sacrifice_cat">猫神</a>・<a href="new_role/human.php#doll_master">人形遣い</a>・<a href="new_role/chiroptera.php#boss_chiroptera">大蝙蝠</a>)</li>
</ul>
<h4>〜<?php echo $MESSAGE->revive_success ?></h4>
<ul>
<li>蘇生 (<a href="new_role/human.php#poison_cat_group">猫又系</a>、<a href="new_role/fox.php#revive_fox">仙狐</a>、<a href="new_role/human.php#revive_priest">天人</a>)</li>
</ul>

<h4>〜<?php echo $MESSAGE->revive_failed ?></h4>
<ul>
<li>蘇生失敗 (霊界からしか見えない) (<a href="new_role/human.php#poison_cat_group">猫又系</a>、<a href="new_role/fox.php#revive_fox">仙狐</a>)</li>
</ul>

<h2><a name="vote">投票処理の仕様</a></h2>
<p>
<a href="#vote_legend">判例</a>
<a href="#vote_day">昼</a>
<a href="#vote_night">夜</a>
</p>

<h3><a name="vote_legend">判例</a></h3>
<ul>
  <li>「→」死因決定の単位</li>
  <li>「＞」判定優先順位 (判定上書き)</li>
</ul>

<h3><a name="vote_day">昼</a></h3>
<pre>
+ 処理順序
  - 投票集計 → 処刑者決定 → 役職判定 → 後追い

+ 処刑者決定法則
  - 単独トップ ＞ 決定者 ＞ <a href="new_role/sub_role.php#bad_luck">不運</a> ＞ <a href="new_role/sub_role.php#impatience">短気</a> ＞ <a href="new_role/sub_role.php#good_luck">幸運</a>が逃れる ＞ <a href="new_role/sub_role.php#plague">疫病神</a>の投票先が逃れる

+ 役職判定順
  - <a href="new_role/human.php#executor">執行者</a> → <a href="new_role/human.php#saint">聖女</a> → <a href="new_role/wolf.php#agitate_mad">扇動者</a> → <a href="new_role/human.php#pharmacist">薬師</a> ＞ 抗毒判定 ＞ 毒発動判定 → <a href="new_role/human.php#trap_common">策士</a> → <a href="new_role/human.php#jealousy">橋姫</a> → <a href="new_role/sub_role.php#chicken_group">ショック死</a>

</pre>

<h3><a name="vote_night">夜</a></h3>
<pre>
+ 処理順序
  - 恋人 → 接触 → 夢 → 占い → &lt;日にち別処理&gt; → 憑依 → 後追い → 司祭
    &lt;[初日] コピー → 帰還 / [二日目以降] 尾行 → 反魂 → 蘇生&gt;

+ 恋人 (キューピッド系)
  - 相互作用はないので投票直後に処理を行う

+ 接触 (人狼、狩人、暗殺者、罠師)
  - 罠 ＞ 狩人護衛 ＞ 人狼襲撃 → 狩人の狩り → 暗殺

+ 夢 (夢守人、獏)
  - 夢守人護衛 ＞ 獏襲撃 → 夢守人の狩り

+ 占い (占い系、厄神、月兎、呪術系)
  - 厄払い ＞ 占い妨害 ＞ 呪い ＞ 占い (呪殺)
</pre>

<h2><a name="revive_refuse">蘇生辞退システム</a></h2>
<pre>
死亡後、霊界オフ状態の時に投票画面をクリックすると
「蘇生を辞退する」(デフォルト) というボタンが出現します。
それをクリックすると「システム：〜さんは蘇生を辞退しました」という
霊界発言が挿入されます。

この状態でその人が蘇生先に選ばれた場合は 100% 蘇生に失敗します。
憑依に関するシステム情報となってしまうため、下界には告知しません。

これは、死亡後に急な用事が入って抜けなければならない人の為の救済措置です。
</pre>
</body></html>
