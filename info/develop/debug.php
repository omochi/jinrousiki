<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputInfoPageHeader('�ǥХå�����', 1);
?>
<p>
Ver. 1.4.0
<a href="#140alpha24">��24</a>
<a href="#140beta2">��2</a>
<a href="#140beta3">��3</a>
<a href="#140beta4">��4</a>
<a href="#140beta11">��11</a>
<a href="#140beta12">��12</a>
<a href="#140beta13">��13</a>
</p>

<h2><a id="140beta13">Ver. 1.4.0 ��13</a></h2>
<h3>include/game_vote_functions.php % 973�����ն�</h3>
<pre>
�� $delete_role_list = array('lovers', 'admire_lovers', 'copied', 'copied_trick', 'copied_soul',
�� $delete_role_list = array('lovers', 'challenge_lovers', 'copied', 'copied_trick', 'copied_soul',
</pre>

<h2><a id="140beta12">Ver. 1.4.0 ��12</a></h2>
<h3>include/game_vote_functinons.php % 176�����ն�</h3>
<h4>[before]</h4>
<pre>
	$random_replace_list = $CAST_CONF->GenerateRandomList($replace_human_list);
	$CAST_CONF->AddRandom($role_list, $random_replace_list, $over_count);
</pre>
<h4>[after]</h4>
<pre>
	$CAST_CONF->AddRandom($role_list, $replace_human_list, $over_count);
</pre>

<h2><a id="140beta11">Ver. 1.4.0 ��11</a></h2>
<h3>include/user_class.php % 190�����ն�</h3>
<h4>[before]</h4>
<pre>
  function IsLonely(){
    return $is_role && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }
</pre>
<h4>[after]</h4>
<pre>
  function IsLonely($role = NULL){
    $is_role = is_null($role) ? true : $this->IsRole($role);
    return $is_role && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }
</pre>
<h3>include/user_class.php % 230�����ն� (2010/07/07 (Wed) 21:40)</h3>
<pre>
�� return $ROOM->date > 1 && $ROOM < 5 && $this->IsRole('challenge_lovers');
�� return $ROOM->date > 1 && $ROOM->date < 5 && $this->IsRole('challenge_lovers');
</pre>
<h3>game_vote.php % 295�����ն� (2010/07/07 (Wed) 23:16)</h3>
<pre>
�� if(! $SELF->IsRole('scanner', 'evoke_scanner')){
�� if(! $SELF->IsRole('mind_scanner', 'evoke_scanner')){
</pre>
<h3>include/game_vote_functions.php % 2009�����ն� (2010/07/09 (Fri) 01:18)</h3>
<pre>
�� if($target->IsRole('escaper')) break; //ƨ˴�ԤϰŻ��Բ�
�� if($target->IsRole('escaper')) continue; //ƨ˴�ԤϰŻ��Բ�
</pre>
<h3>include/game_functions.php % 834�����ն� (2010/07/11 (Sun) 02:22)</h3>
<pre>
�� elseif($said_user->IsLonely('wolf')){
�� elseif($said_user->IsLonely('silver_wolf')){
</pre>

<h2><a id="140beta4">Ver. 1.4.0 ��4</a></h2>
<h3>user_manager.php % 35�����ն�</h3>
<h4>[before]</h4>
<pre>
  //�����������å�
</pre>
<h4>[after]</h4>
<pre>
  $query = "SELECT COUNT(icon_no) FROM user_icon WHERE icon_no = " . $icon_no;
  if(FetchResult($query) < 1) OutputActionResult('¼����Ͽ [���ϥ��顼]', '̵���ʥ��������ֹ�Ǥ�');

  //�����������å�
</pre>

<h3>user_manager.php % 275�����ն� (2010/02/24 (Wed) 21:40)</h3>
<h4>[before]</h4>
<pre>
if($ROOM->IsOptionGroup('mania')) $wish_role_list[] = 'mania';
</pre>
<h4>[after]</h4>
<pre>
if($ROOM->IsOptionGroup('mania') && ! in_array('mania', $wish_role_list)){
  $wish_role_list[] = 'mania';
}
</pre>

<h3>include/game_functons.php % 751�����ն� (2010/02/28 (Sun) 02:00)</h3>
<h4>[before]</h4>
<pre>
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>
<h4>[after]</h4>
<pre>
LineToBR($sentence);
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>

<h3>game_vote.php % 352�����ն� (2010/02/28 (Sun) 20:25)</h3>
<pre>
�� $sub_role_list = $GAME_CONF->sub_role_group_list['sudden-death'];
�� $sub_role_list = array_diff($GAME_CONF->sub_role_group_list['sudden-death'], array('panelist'));
</pre>

<h2><a id="140beta3">Ver. 1.4.0 ��3</a></h2>
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

<h3>include/game_functions.php % 236�����ն� (2010/02/22 (Mon) 23:00)</h3>
<pre>
�� $handle_name .= $real_user->GenarateShortRoleName();
�� $handle_name .= $real_user->GenerateShortRoleName();
</pre>

<h3>include/user_class.php % 216�����ն�</h3>
<pre>
�� function GenarateShortRoleName(){
�� function GenerateShortRoleName(){
</pre>

<h3>include/game_functons.php % 461�����ն� (2010/02/28 (Sun) 02:00)</h3>
<h4>[before]</h4>
<pre>
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>
<h4>[after]</h4>
<pre>
LineToBR($sentence);
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>


<h2><a id="140beta2">Ver. 1.4.0 ��2</a></h2>
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

<h2><a id="140alpha24">Ver. 1.4.0 ��24</a></h2>
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

<h3>user_manager.php % 276 ���� (2010/01/30 02:30)</h3>
<pre>
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',
</pre>

<h3>include/game_functions.php % 400 �����ն� (2010/02/01 (Mon) 00:15)</h3>
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
