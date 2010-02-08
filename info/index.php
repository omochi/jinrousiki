<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
?>
</head>
<body>
<a href="../">TOP に戻る</a>
<p>Ver. 1.4.0 α24 アップロード (2010/01/28 (Thu) 21:29:30) → <a href="../src/">ダウンロード</a><br>
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

<p>Ver. 1.4.0 α23 アップロード (2010/01/10 (Sun) 06:11:24) → <a href="../src/">ダウンロード</a><br>
・さとり・銀狼・薬師の仕様変更、鵺・女神の実装など
</p>
</body></html>
