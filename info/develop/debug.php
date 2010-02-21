<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader($SERVER_CONF->title . '[�ǥХå�����]', 'info');
?>
</head>
<body>
<h1>�ǥХå�����</h1>
<p>
<a href="../../" target="_top">&lt;= TOP</a>
<a href="../" target="_top">���������</a>
</p>

<h2>Ver. 1.4.0 ��3</h2>
<h3>game_play.php % 259�����ն�</h3>
<pre>
�� if($ROOM->IsPlaying() && $virtual->IsLive()){
�� if($ROOM->IsPlaying() && $virtual_self->IsLive()){
</pre>

<h3>include/game_format.php % 60�����ն�</h3>
<pre>
�� global $RQ_ARGS;
�� global $GAME_CONF, $RQ_ARGS;
</pre>

<h3>include/game_format.php % 83�����ն�</h3>
<h4>[before]</h4>
<pre>
if($RQ_ARGS->add_role) $handle_name .= $user->GenarateShortRoleName(); //��ɽ���⡼���б�
</pre>
<h4>[after]</h4>
<pre>
if($RQ_ARGS->add_role){ //��ɽ���⡼���б�
  $real_user = $talk->scene == 'heaven' ? $user : $USERS->ByReal($user->user_no);
  $handle_name .= $real_user->GenerateShortRoleName();
}
</pre>

<h3>include/talk_class.php % 38�����ն�</h3>
<h4>[before]</h4>
<pre>
case 'dummy_boy':
  if($this->type == $this->uname){
</pre>
<h4>[after]</h4>
<pre>
case 'dummy_boy':
  if($this->type == 'system') break;
  if($this->type == $this->uname){
</pre>


<h2>Ver. 1.4.0 ��2</h2>
<h3>include/game_vote_functions.php % 1188����</h3>
<pre>
�� elseif(! $ROOM->IsOpenCast() && $user->IsGroup('evoke_scanner')){
�� elseif(! $ROOM->IsOpenCast() && $user->IsRole('evoke_scanner')){
</pre>

<h3>game_play.php % 449 ����</h3>
<pre>
�� array_push($actor_list, 'poison_cat');
�� array_push($actor_list, '%cat', 'revive_fox');
</pre>

<h2>Ver. 1.4.0 ��24</h2>
<h3>game_play.php % 731 ����</h3>
<pre>
�� $USERS->GetHandleName($target_uname) . '�������ɼ�Ѥ�');
�� $USERS->GetHandleName($target_uname, true) . '�������ɼ�Ѥ�');
</pre>

<h3>include/game_functions.php % 705 ����</h3>
<pre>
��elseif($pseud_self->IsRole('wise_wolf')){
��elseif($virtual_self->IsRole('wise_wolf')){
</pre>

<h3>user_manager.php % 276 ���� (2010/01/30 02:30 �ɵ�)</h3>
<pre>
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',
</pre>

<h3>include/game_functions.php % 400 �����ն� (2010/02/01 (Mon) 00:15 �ɵ�)</h3>
<h4>[before]</h4>
<pre>
$said_user = $USERS->ByVirtualUname($talk->uname);
</pre>
<h4>[after]</h4>
<pre>
if(strpos($talk->location, 'heaven') === false)
  $said_user = $USERS->ByVirtualUname($talk->uname);
else
  $said_user = $USERS->ByUname($talk->uname);
</pre>

<h3>include/game_vote_functions % 1865 �����ն�</h3>
<h4>[before]</h4>
<pre>
$target->dead_flag = false; //��˴�ե饰��ꥻ�å�
$USERS->Kill($target->user_no, 'WOLF_KILLED');
if($target->revive_flag) $target->Update('live', 'live'); //�����б�
</pre>
<h4>[after]</h4>
<pre>
if(isset($target->user_no)){
  $target->dead_flag = false; //��˴�ե饰��ꥻ�å�
  $USERS->Kill($target->user_no, 'WOLF_KILLED');
  if($target->revive_flag) $target->Update('live', 'live'); //�����б�
}
</pre>
</body></html>
