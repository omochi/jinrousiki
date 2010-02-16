<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
?>
</head>
<body>
<a href="../">TOP に戻る</a>
<p>Ver. 1.4.0 β2 アップロード (2010/02/08 (Mon) 03:41:23)<br>
・預言者、天人、イタコ、仙狸、仙狐の実装<br>
・猫又、出題者の仕様変更<br>
・再入村リンク表示機能の実装 (設定された時間内、過去ログページに出現します)<br>
<br>
・バグ Fix<br>
◆include/game_vote_functions.php % 1188行目<br>
× elseif(! $ROOM->IsOpenCast() && $user->IsGroup('evoke_scanner')){<br>
○ elseif(! $ROOM->IsOpenCast() && $user->IsRole('evoke_scanner')){<br>
<br>
◆game_play.php % 449 行目<br>
× array_push($actor_list, 'poison_cat');<br>
○ array_push($actor_list, '%cat', 'revive_fox');<br>
<br>
</p>

<p>Ver. 1.4.0 β1 アップロード (2010/02/02 (Tue) 04:25:52)<br>
・Ver. 1.4.0 α24 のバグ Fix、設定ファイルの再配置など<br>
</p>

<p>Ver. 1.4.0 α24 アップロード (2010/01/28 (Thu) 21:29:30)<br>
・憑狼・紅狼・賢狼・紅狐・黒狐・司祭の実装、抗毒狼の仕様変更など<br>
<br>
・バグ Fix<br>
◆game_play.php % 731 行目<br>
× $USERS->GetHandleName($target_uname) . 'さんに投票済み');<br>
○ $USERS->GetHandleName($target_uname, true) . 'さんに投票済み');<br>
<br>
◆include/game_functions.php % 705 行目<br>
×elseif($pseud_self->IsRole('wise_wolf')){<br>
○elseif($virtual_self->IsRole('wise_wolf')){<br>
<br>
◆user_manager.php % 276 行目 (2010/01/30 02:30 追記)<br>
× array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',<br>
○ array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',<br>
<br>
◆include/game_functions.php % 400 行目付近 (2010/02/01 (Mon) 00:15 追記)<br>
[before]<br>
$said_user = $USERS->ByVirtualUname($talk->uname);<br>
[after]<br>
if(strpos($talk->location, 'heaven') === false)<br>
  $said_user = $USERS->ByVirtualUname($talk->uname);<br>
else<br>
  $said_user = $USERS->ByUname($talk->uname);<br>
<br>
◆include/game_vote_functions % 1865 行目付近<br>
[before]<br>
$target->dead_flag = false; //死亡フラグをリセット<br>
$USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応<br>
[after]<br>
if(isset($target->user_no)){<br>
  $target->dead_flag = false; //死亡フラグをリセット<br>
  $USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
  if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応<br>
}<br>


</p>

<p>Ver. 1.4.0 α23 アップロード (2010/01/10 (Sun) 06:11:24)<br>
・さとり・銀狼・薬師の仕様変更、鵺・女神の実装など
</p>
</body></html>
