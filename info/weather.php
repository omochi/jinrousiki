<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('info_functions');
$INIT_CONF->LoadClass('GAME_OPT_MESS', 'ROLE_DATA');
OutputInfoPageHeader('天候システム');
?>
<p>
<a href="#game_option">関連オプション</a>
<a href="#summary">一覧</a>
</p>
<p>
<a href="#type_talk">会話妨害</a>
<a href="#type_vote_day">処刑投票妨害</a>
<a href="#type_ability">能力強化・封印</a>
</p>

<h2><a id="game_option">関連オプション</a></h2>
<ul>
  <li><a href="game_option.php#weather"><?php echo $GAME_OPT_MESS->weather ?></a></li>
</ul>

<h2><a id="summary">一覧</a></h2>
<p>
Ver. 1.5.0
<a href="#150alpha2">α2</a>
<a href="#150alpha3">α3</a>
<a href="#150alpha4">α4</a>
</p>
<table>
<tr>
  <th>名称</th>
  <th>タイプ</th>
  <th>簡易説明</th>
  <th>初登場</th>
</tr>
<tr>
  <td><a href="#weather_grassy" id="150alpha2"><?php echo $ROLE_DATA->weather_list[0]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[0]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_mower"><?php echo $ROLE_DATA->weather_list[1]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[1]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_blind_vote"><?php echo $ROLE_DATA->weather_list[2]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[2]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_no_fox_dead"><?php echo $ROLE_DATA->weather_list[3]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[3]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_critical"><?php echo $ROLE_DATA->weather_list[4]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[4]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_blind_talk_day"><?php echo $ROLE_DATA->weather_list[5]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[5]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_blind_talk_night"><?php echo $ROLE_DATA->weather_list[6]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[6]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_full_moon"><?php echo $ROLE_DATA->weather_list[7]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[7]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_new_moon"><?php echo $ROLE_DATA->weather_list[8]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[8]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_no_contact"><?php echo $ROLE_DATA->weather_list[9]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[9]['caption'] ?></td>
  <td>Ver. 1.5.0 α2</td>
</tr>
<tr>
  <td><a href="#weather_invisible" id="150alpha3"><?php echo $ROLE_DATA->weather_list[10]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[10]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_rainbow"><?php echo $ROLE_DATA->weather_list[11]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[11]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_side_reverse"><?php echo $ROLE_DATA->weather_list[12]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[12]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_line_reverse"><?php echo $ROLE_DATA->weather_list[13]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[13]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_actor"><?php echo $ROLE_DATA->weather_list[14]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[14]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_critical_luck"><?php echo $ROLE_DATA->weather_list[15]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[15]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_no_sudden_death"><?php echo $ROLE_DATA->weather_list[16]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[16]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_thunderbolt"><?php echo $ROLE_DATA->weather_list[17]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[17]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_no_last_words"><?php echo $ROLE_DATA->weather_list[18]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[18]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_no_dream"><?php echo $ROLE_DATA->weather_list[19]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[19]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_full_ogre"><?php echo $ROLE_DATA->weather_list[20]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[20]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_seal_ogre"><?php echo $ROLE_DATA->weather_list[21]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[21]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_full_revive"><?php echo $ROLE_DATA->weather_list[22]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[22]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_no_revive"><?php echo $ROLE_DATA->weather_list[23]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[23]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_brownie"><?php echo $ROLE_DATA->weather_list[24]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[24]['caption'] ?></td>
  <td>Ver. 1.5.0 α3</td>
</tr>
<tr>
  <td><a href="#weather_whisper_ringing" id="150alpha4"><?php echo $ROLE_DATA->weather_list[25]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[25]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_howl_ringing"><?php echo $ROLE_DATA->weather_list[26]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[26]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_sweet_ringing"><?php echo $ROLE_DATA->weather_list[27]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[27]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_deep_sleep"><?php echo $ROLE_DATA->weather_list[28]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[28]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_silent"><?php echo $ROLE_DATA->weather_list[29]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[29]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_missfire_revive"><?php echo $ROLE_DATA->weather_list[30]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[30]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_no_hunt"><?php echo $ROLE_DATA->weather_list[31]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[31]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_full_guard"><?php echo $ROLE_DATA->weather_list[32]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[32]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_frostbite"><?php echo $ROLE_DATA->weather_list[33]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[33]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_alchemy_pharmacist"><?php echo $ROLE_DATA->weather_list[34]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[34]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_hyper_random_voter"><?php echo $ROLE_DATA->weather_list[35]['name'] ?></a></td>
  <td><a href="#type_vote_day">処刑投票妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[35]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_half_moon"><?php echo $ROLE_DATA->weather_list[36]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[36]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_half_guard"><?php echo $ROLE_DATA->weather_list[37]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[37]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_passion"><?php echo $ROLE_DATA->weather_list[38]['name'] ?></a></td>
  <td><a href="#type_talk">会話妨害</a></td>
  <td><?php echo $ROLE_DATA->weather_list[38]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
<tr>
  <td><a href="#weather_no_poison"><?php echo $ROLE_DATA->weather_list[39]['name'] ?></a></td>
  <td><a href="#type_ability">能力強化・封印</a></td>
  <td><?php echo $ROLE_DATA->weather_list[39]['caption'] ?></td>
  <td>Ver. 1.5.0 α4</td>
</tr>
</table>

<h2><a id="type_talk">会話妨害</a></h2>
<ul>
  <li><a href="#weather_invisible">黄砂</a></li>
  <li><a href="#weather_rainbow">虹</a></li>
  <li><a href="#weather_passion">箒星</a></li>
  <li><a href="#weather_grassy">スコール</a></li>
  <li><a href="#weather_side_reverse">ダイヤモンドダスト</a></li>
  <li><a href="#weather_line_reverse">バナナの皮</a></li>
  <li><a href="#weather_actor">スポットライト</a></li>
  <li><a href="#weather_whisper_ringing">波風</a></li>
  <li><a href="#weather_howl_ringing">小夜嵐</a></li>
  <li><a href="#weather_sweet_ringing">流星群</a></li>
  <li><a href="#weather_deep_sleep">春時雨</a></li>
  <li><a href="#weather_silent">木漏れ日</a></li>
  <li><a href="#weather_mower">酸性雨</a></li>
  <li><a href="#weather_blind_talk_day">強風</a></li>
  <li><a href="#weather_blind_talk_night">風雨</a></li>
</ul>

<h3><a id="weather_invisible">黄砂</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#invisible">光学迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_rainbow">虹</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#rainbow">虹色迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_passion">箒星</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#passion">恋色迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_grassy">スコール</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#grassy">草原迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_side_reverse">ダイヤモンドダスト</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#side_reverse">鏡面迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_line_reverse">バナナの皮</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#line_reverse">天地迷彩</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_actor">スポットライト</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#actor">役者</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_whisper_ringing">波風</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#whisper_ringing">囁耳鳴</a>がつきます。</li>
</ul>

<h3><a id="weather_howl_ringing">小夜嵐</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#howl_ringing">吠耳鳴</a>がつきます。</li>
</ul>

<h3><a id="weather_sweet_ringing">流星群</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#sweet_ringing">恋耳鳴</a>がつきます。</li>
</ul>

<h3><a id="weather_deep_sleep">春時雨</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#deep_sleep">爆睡者</a>がつきます。</li>
</ul>

<h3><a id="weather_silent">木漏れ日</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#silent">無口</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_mower">酸性雨</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#mower">草刈り</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_blind_talk_day">強風</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>自分の入村位置の上下左右の人以外の昼の発言が共有者の囁きに変換されて見えます。</li>
  <li>霊界からのログでは通常通り見ることができます。</li>
</ul>
<h5>Ver. 1.5.0 α3～</h5>
<ul>
  <li>自分以外→自分の入村位置の上下左右の人以外</li>
</ul>

<h3><a id="weather_blind_talk_night">風雨</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>夜の発言が全て独り言になります。</li>
  <li>結果として、<a href="new_role/human.php#common_group">共有者系</a>の囁き・<a href="new_role/wolf.php#wolf_group">人狼系</a>の遠吠え・<a href="new_role/fox.php#fox_group">妖狐</a>の念話が消滅します。</li>
  <li><a href="new_role/sub_role.php#mind_read_group">サトラレ系</a>には影響がありません (通常通り見えます)。</li>
</ul>

<h2><a id="type_vote_day">処刑投票妨害</a></h2>
<ul>
  <li><a href="#weather_brownie">慈雨</a></li>
  <li><a href="#weather_critical_luck">タライ</a></li>
  <li><a href="#weather_hyper_random_voter">雹</a></li>
  <li><a href="#weather_critical">烈日</a></li>
  <li><a href="#weather_alchemy_pharmacist">梅雨</a></li>
  <li><a href="#weather_frostbite">雪</a></li>
  <li><a href="#weather_blind_vote">晴嵐</a></li>
  <li><a href="#weather_thunderbolt">青天の霹靂</a></li>
  <li><a href="#weather_no_sudden_death">凪</a></li>
</ul>

<h3><a id="weather_brownie">慈雨</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/human.php#human">村人</a>の処刑投票数が +1 されます (<a href="new_role/human.php#brownie">座敷童子</a>相当)。</li>
  <li><a href="new_role/human.php#brownie">座敷童子</a>の効果と重複しません。</li>
</ul>

<h3><a id="weather_critical_luck">タライ</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#critical_luck">痛恨</a>がつきます (昼限定)。</li>
</ul>

<h3><a id="weather_hyper_random_voter">雹</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>処刑投票数がランダムで 0～5 増えます (毎回変わります)。</li>
  <li><a href="new_role/sub_role.php">サブ役職</a>の補正後に適用されます。</li>
</ul>

<h3><a id="weather_critical">烈日</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li><a href="new_role/sub_role.php#critical_voter">会心</a>・<a href="new_role/sub_role.php#critical_luck">痛恨</a>の発動率が 100% になります。</li>
</ul>

<h3><a id="weather_alchemy_pharmacist">梅雨</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>処刑者に<a href="new_role/human.php#alchemy_pharmacist">錬金術師</a>の能力が適用されます。</li>
</ul>

<h3><a id="weather_frostbite">雪</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>処刑者決定後、ランダムで生存者の誰か一人に<a href="new_role/sub_role.php#frostbite">凍傷</a>が付加されます。</li>
  <li><a href="new_role/human.php#detective_common">探偵</a>・<a href="new_role/wolf.php#sirius_wolf">天狼</a> (完全覚醒状態)・<a href="new_role/quiz.php#quiz">出題者</a>・<a href="new_role/sub_role.php#challenge_lovers">難題</a> (耐性期間中) は対象外です。</li>
</ul>

<h3><a id="weather_blind_vote">晴嵐</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>処刑者が決定した投票結果を見ることができなくなります (<a href="new_role/wolf.php#amaze_mad">傘化け</a>相当)。</li>
  <li>再投票は隠蔽されません。</li>
  <li>効果はその日だけなので、翌日には解除されます。</li>
</ul>

<h3><a id="weather_thunderbolt">青天の霹靂</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>処刑投票結果が出るたびにランダムで誰か一人が落雷でショック死します。</li>
  <li>死因は「落雷を受けたようです」で、<a href="new_role/sub_role.php">サブ役職</a>によるショック死より優先されます。</li>
  <li><a href="new_role/human.php#detective_common">探偵</a>・<a href="new_role/wolf.php#sirius_wolf">天狼</a> (完全覚醒状態)・<a href="new_role/quiz.php#quiz">出題者</a>・<a href="new_role/sub_role.php#challenge_lovers">難題</a> (耐性期間中) は対象外です。</li>
  <li>対象者に投票した<a href="new_role/ability.php#anti_sudden_death">ショック死抑制能力者</a>の能力は有効です。</li>
</ul>

<h3><a id="weather_no_sudden_death">凪</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/sub_role.php">サブ役職</a>によるショック死が発生しなくなります。</li>
</ul>


<h2><a id="type_ability">能力強化・封印</a></h2>
<ul>
  <li><a href="#weather_no_last_words">涙雨</a></li>
  <li><a href="#weather_no_fox_dead">天気雨</a></li>
  <li><a href="#weather_full_moon">満月</a></li>
  <li><a href="#weather_half_moon">半月</a></li>
  <li><a href="#weather_new_moon">新月</a></li>
  <li><a href="#weather_full_guard">蒼天</a></li>
  <li><a href="#weather_half_guard">曇天</a></li>
  <li><a href="#weather_no_hunt">川霧</a></li>
  <li><a href="#weather_no_contact">花曇</a></li>
  <li><a href="#weather_no_dream">熱帯夜</a></li>
  <li><a href="#weather_full_ogre">朧月</a></li>
  <li><a href="#weather_seal_ogre">叢雲</a></li>
  <li><a href="#weather_no_poison">旱魃</a></li>
  <li><a href="#weather_full_revive">雷雨</a></li>
  <li><a href="#weather_missfire_revive">疎雨</a></li>
  <li><a href="#weather_no_revive">快晴</a></li>
</ul>

<h3><a id="weather_no_last_words">涙雨</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li>全員に<a href="new_role/sub_role.php#no_last_words">筆不精</a>がつきます。</li>
</ul>

<h3><a id="weather_no_fox_dead">天気雨</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>妖狐の呪殺が発生しなくなります。</li>
</ul>

<h3><a id="weather_full_moon">満月</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>夜の投票で発生する<a href="new_role/ability.php#phantom">占い妨害能力</a>・<a href="new_role/ability.php#voodoo">呪術能力</a>と<a href="new_role/human.php#guard_group">狩人系</a>の能力が全て無効化されます。</li>
  <li><a href="new_role/human.php#wizard_group">魔法</a>による能力は有効です。</li>
  <li>投票を必要としない<a href="new_role/ability.php#phantom">占い妨害能力</a>・<a href="new_role/ability.php#cursed">呪い</a>は有効です。</li>
  <li>無効化される能力者も投票自体は必要です (集計処理をする際になかったことにされます)。</li>
</ul>

<h3><a id="weather_half_moon">半月</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li><a href="new_role/human.php#mage_group">占い能力者</a>に成功率 50% の<a href="new_role/wolf.php#jammer_mad">月兎</a>相当の能力が適用されます (対象者は<a href="new_role/wolf.php#jammer_mad">月兎</a>参照)。</li>
  <li>対象者に対する<a href="new_role/human.php#anti_voodoo">厄神</a>の厄払い能力は有効です。</li>
</ul>

<h3><a id="weather_new_moon">新月</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li>夜の投票で発生する<a href="new_role/human.php#mage_group">占い能力</a>・<a href="new_role/human.php#wizard_group">魔法能力</a>・<a href="new_role/wolf.php#wolf_group">人狼襲撃</a>・<a href="new_role/vampire.php">吸血能力</a>・<a href="new_role/chiroptera.php#fairy_group">悪戯能力</a>が全て無効化されます。</li>
  <li>占い能力は<a href="new_role/fox.php#child_fox_group">子狐系</a>も含まれます。</li>
  <li>無効化される能力者も投票自体は必要です (集計処理をする際になかったことにされます)。</li>
</ul>

<h3><a id="weather_full_guard">蒼天</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li><a href="new_role/human.php#guard_limit">護衛制限</a>が無くなります。</li>
</ul>

<h3><a id="weather_half_guard">曇天</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li>50% の確率で<a href="new_role/ability.php#guard">護衛能力者</a>の護衛が突破されます。</li>
  <li><a href="new_role/wolf.php#wolf_group">人狼系</a>・<a href="new_role/vampire.php">吸血鬼陣営</a>の襲撃両方に適用され、判定は個々の襲撃毎に行われます。</li>
  <li>突破されても護衛成功メッセージは表示されます。</li>
</ul>

<h3><a id="weather_no_hunt">川霧</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li><a href="spec.php#vote_night">接触系能力者</a>の<a href="new_role/human.php#guard_hunt">狩り</a>が無くなります。</li>
</ul>

<h3><a id="weather_no_contact">花曇</a> [Ver. 1.5.0 α2～]</h3>
<ul>
  <li><a href="spec.php#vote_night">接触系能力者</a>の夜の投票が全て無効化されます。</li>
  <li><a href="new_role/human.php#anti_voodoo">厄神</a>・<a href="new_role/human.php#dummy_guard">夢守人</a>の能力は有効、<a href="new_role/human.php#reporter">ブン屋</a>・<a href="new_role/human.php#clairvoyance_scanner">猩々</a>の能力は無効です。</li>
  <li>無効化される能力者も投票自体は必要です (集計処理をする際になかったことにされます)。</li>
</ul>

<h3><a id="weather_no_dream">熱帯夜</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="spec.php#vote_night">夢系能力者</a>の夜の投票と一部の<a href="new_role/ability.php#dummy">夢系能力者</a>の能力が無効化されます。</li>
  <li>対象となるのは<a href="new_role/human.php#dummy_mage">夢見人</a>・<a href="new_role/human.php#dummy_necromancer">夢枕人</a>・<a href="new_role/human.php#dummy_priest">夢司祭</a>・<a href="new_role/human.php#dummy_guard">夢守人</a>・<a href="new_role/wolf.php#dream_eater_mad">獏</a>です。</li>
  <li>無効化される能力者も投票自体は必要です (集計処理をする際になかったことにされます)。</li>
</ul>

<h3><a id="weather_full_ogre">朧月</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/ogre.php">鬼陣営</a>の人狼襲撃無効・暗殺反射・人攫いの成功率が 100% になります。</li>
  <li><a href="new_role/ogre.php#revive_ogre">茨木童子</a>の蘇生率が 100% になります。</li>
  <li>人攫いが成立してもカウントされません (次回の成功率が低下しません)。</li>
</ul>

<h3><a id="weather_seal_ogre">叢雲</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/ogre.php">鬼陣営</a>の人狼襲撃無効・暗殺反射・人攫いの成功率が 0% になります。</li>
  <li><a href="new_role/ogre.php#revive_ogre">茨木童子</a>の蘇生率が 0% になります。</li>
</ul>

<h3><a id="weather_no_poison">旱魃</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li><a href="new_role/ability.php#poison">毒能力</a>が全て無効化されます。</li>
  <li>昼夜両方に適用され、<a href="new_role/human.php#dummy_poison">夢毒者</a>も無効化されます。</li>
</ul>

<h3><a id="weather_full_revive">雷雨</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/ability.php#revive_other">他者蘇生能力者</a>の蘇生率が 100%、誤爆率が 0% になります。</li>
  <li>蘇生が成立してもカウントされません。<br>
    (<a href="new_role/human.php#revive_cat">仙狸</a>の成功率低下・<a href="new_role/human.php#sacrifice_cat">猫神</a>の身代わり死・<a href="new_role/fox.php#revive_fox">仙狐</a>の能力喪失が発生しない)
  </li>
</ul>

<h3><a id="weather_missfire_revive">疎雨</a> [Ver. 1.5.0 α4～]</h3>
<ul>
  <li><a href="new_role/ability.php#revive_other">他者蘇生能力者</a>の誤爆率が 2 倍になります。</li>
  <li>個々の誤爆率決定後に補正されます。</li>
</ul>

<h3><a id="weather_no_revive">快晴</a> [Ver. 1.5.0 α3～]</h3>
<ul>
  <li><a href="new_role/ability.php#revive_other">他者蘇生能力者</a>の蘇生率が 0% になります。</li>
</ul>
</body></html>
