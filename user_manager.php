<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('room_class', 'user_class', 'icon_functions');
$INIT_CONF->LoadClass('SESSION', 'GAME_CONF', 'MESSAGE');
$INIT_CONF->LoadRequest('RequestUserManager'); //���������
$DB_CONF->Connect(); //DB ��³
$RQ_ARGS->entry ? EntryUser() : OutputEntryUserPage();
$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
//�桼������Ͽ����
function EntryUser(){
  global $DEBUG_MODE, $GAME_CONF, $MESSAGE, $RQ_ARGS, $SESSION;

  extract($RQ_ARGS->ToArray()); //���������

  //����ϳ������å�
  $title = '¼����Ͽ [���ϥ��顼]';
  $sentence = '�����Ǥ� (����Ȳ��ԥ����ɤϼ�ư�Ǻ������ޤ�)';
  if($uname == '')       OutputActionResult($title, '�桼��̾'     . $sentence);
  if($handle_name == '') OutputActionResult($title, '¼�ͤ�̾��'   . $sentence);
  if($password == '')    OutputActionResult($title, '�ѥ����'   . $sentence);
  if($profile == '')     OutputActionResult($title, '�ץ�ե�����' . $sentence);
  if(empty($sex))        OutputActionResult($title, '���̤����Ϥ���Ƥ��ޤ���');
  if(empty($icon_no))    OutputActionResult($title, '���������ֹ椬���Ϥ���Ƥ��ޤ���');

  //ʸ�������¥����å�
  if(strlen($uname) > $GAME_CONF->entry_uname_limit){
    OutputActionResult($title, '�桼��̾��' . $GAME_CONF->entry_uname_limit . 'ʸ���ޤ�');
  }
  if(strlen($handle_name) > $GAME_CONF->entry_uname_limit){
    OutputActionResult($title, '¼�ͤ�̾����' . $GAME_CONF->entry_uname_limit . 'ʸ���ޤ�');
  }
  if(strlen($profile) > $GAME_CONF->entry_profile_limit){
    OutputActionResult($title, '�ץ�ե������' . $GAME_CONF->entry_profile_limit . 'ʸ���ޤ�');
  }

  //�㳰�����å�
  if($uname == 'dummy_boy' || $uname == 'system'){
    OutputActionResult($title, '�桼��̾��' . $uname . '�פϻ��ѤǤ��ޤ���');
  }
  if($handle_name == '�����귯' || $handle_name == '�����ƥ�'){
    OutputActionResult($title, '¼��̾��' . $handle_name . '�פϻ��ѤǤ��ޤ���');
  }
  if($sex != 'male' && $sex != 'female') OutputActionResult($title, '̵�������̤Ǥ�');

  $query = 'SELECT COUNT(icon_no) FROM user_icon WHERE icon_no = ' . $icon_no;
  if($icon_no < 1 || FetchResult($query) < 1) OutputActionResult($title, '̵���ʥ��������ֹ�Ǥ�');

  //�ơ��֥���å�
  if(! LockTable()){
    OutputActionResult('¼����Ͽ [�����Х��顼]',
		       '�����Ф��������Ƥ��ޤ���<br>'."\n".'������Ͽ���Ƥ�������');
  }

  $request = new RequestBase();
  $request->room_no = $room_no;
  $request->entry_user = true;
  $USERS = new UserDataSet($request);
  //PrintData($USERS); //�ƥ�����

  //�����������å�
  $user = $USERS->ByUname($uname);
  if($user->live == 'kick' || $user->user_no < 0){
    OutputActionResult('¼����Ͽ [���å����줿�桼��]',
		       '���å����줿�ͤ�Ʊ���桼��̾�ϻ��ѤǤ��ޤ��� (¼��̾�ϲ�)<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }

  //�桼��̾��¼��̾
  if($user->user_no > 0 || $USERS->ByHandleName($handle_name)->user_no > 0){
    OutputActionResult('¼����Ͽ [��ʣ��Ͽ���顼]',
		       '�桼��̾���ޤ���¼��̾��������Ͽ���Ƥ���ޤ���<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }
  //OutputActionResult('�ȥ�åץƥ���', $uname . '<br>' . $handle_name);

  //IP���ɥ쥹�����å�
  $ip_address = $_SERVER['REMOTE_ADDR']; //�桼����IP���ɥ쥹�����
  if(! $DEBUG_MODE && $GAME_CONF->entry_one_ip_address){
    foreach($USERS->rows as $user){
      if($user->ip_address == $ip_address){
	OutputActionResult('¼����Ͽ [¿����Ͽ���顼]', '¿����Ͽ�ϤǤ��ޤ���');
      }
    }
  }

  $ROOM = RoomDataSet::LoadEntryUser($room_no); //DB�������Ϳ������
  $user_no = count($USERS->names) + 1; //KICK ���줿���ͤ�ޤ᤿�������ֹ�򿶤�
  $user_count = $USERS->GetUserCount(true); //���ߤ� KICK ����Ƥ��ʤ����ͤο������

  //��������С����Ƥ���Ȥ�
  if($user_count >= $ROOM->max_user){
    OutputActionResult('¼����Ͽ [��¼�Բ�]', '¼�������Ǥ���', '', true);
  }
  if(! $ROOM->IsBeforeGame() || $ROOM->status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]', '���Ǥ˥����ब���Ϥ���Ƥ��ޤ�', '', true);
  }

  //���å����κ��
  $ROOM->system_time = TZTime(); //���߻�������
  $cookie_time = $ROOM->system_time - 3600;
  setcookie('day_night',  '', $cookie_time);
  setcookie('vote_times', '', $cookie_time);
  setcookie('objection',  '', $cookie_time);

  //DB �˥桼���ǡ�������Ͽ
  if(InsertUser($room_no, $uname, $handle_name, $password, $user_no, $icon_no, $profile,
		$sex, $role, $SESSION->Get(true))){
    $ROOM->Talk($handle_name . ' ' . $MESSAGE->entry_user); //��¼��å�����
    $url = 'game_frame.php?room_no=' . $room_no;
    $user_count++;
    OutputActionResult('¼����Ͽ',
		       $user_count . ' ���ܤ�¼����Ͽ��λ��¼�δ��礤�ڡ��������Ӥޤ���<br>'."\n" .
		       '�ڤ��ؤ��ʤ��ʤ� <a href="' . $url. '">����</a> ��',
		       $url, true);
  }
  else{
    OutputActionResult('¼����Ͽ [�ǡ����١��������Х��顼]',
		       '�ǡ����١��������Ф��������Ƥ��ޤ���<br>'."\n" .
		       '���֤��֤��ƺ�����Ͽ���Ƥ���������', '', true);
  }
  UnlockTable(); //��å����
}

//�桼����Ͽ����ɽ��
function OutputEntryUserPage(){
  global $SERVER_CONF, $GAME_CONF, $ICON_CONF, $RQ_ARGS;

  extract($RQ_ARGS->ToArray()); //���������
  $ROOM = RoomDataSet::LoadEntryUserPage($room_no);
  $sentence = $room_no . ' ���Ϥ�¼��';
  if(is_null($ROOM->id))  OutputActionResult('¼����Ͽ [¼�ֹ楨�顼]', $sentence . '¸�ߤ��ޤ���');
  if($ROOM->IsFinished()) OutputActionResult('¼����Ͽ [��¼�Բ�]',  $sentence . '��λ���ޤ���');
  if($ROOM->status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]', $sentence . '���Ǥ˥����ब���Ϥ���Ƥ��ޤ���');
  }
  $ROOM->ParseOption(true);
  $trip = '(�ȥ�å׻���' . ($GAME_CONF->trip ? '��ǽ' : '�Բ�') . ')';

  OutputHTMLHeader($SERVER_CONF->title .'[¼����Ͽ]', 'entry_user');
  echo <<<HEADER
</head>
<body>
<a href="./">�����</a><br>
<form method="POST" action="user_manager.php?room_no={$ROOM->id}">
<input type="hidden" name="entry" value="on">
<div align="center">
<table class="main">
<tr><td><img src="img/entry_user/title.gif"></td></tr>
<tr><td class="title">{$ROOM->name} ¼<img src="img/entry_user/top.gif"></td></tr>
<tr><td class="number">��{$ROOM->comment}�� [{$ROOM->id} ����]</td></tr>
<tr><td>
<table class="input">
<tr>
<td class="img"><img src="img/entry_user/uname.gif"></td>
<td><input type="text" name="uname" size="30" maxlength="30"></td>
<td class="explain">���ʤ�ɽ�����줺��¾�Υ桼��̾���狼��Τ�<br>��˴�����Ȥ��ȥ����ཪλ��ΤߤǤ�{$trip}</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/handle_name.gif"></td>
<td><input type="text" name="handle_name" size="30" maxlength="30"></td>
<td class="explain">¼��ɽ�������̾���Ǥ�</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/password.gif"></td>
<td><input type="password" name="password" size="30" maxlength="30"></td>
<td class="explain">���å�����ڤ줿���Υ�������˻Ȥ��ޤ�<br> (�Ź沽����Ƥ��ʤ��Τ������)</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/sex.gif"></td>
<td class="img">
<label for="male"><img src="img/entry_user/sex_male.gif"><input type="radio" id="male" name="sex" value="male"></label>
<label for="female"><img src="img/entry_user/sex_female.gif"><input type="radio" id="female" name="sex" value="female"></label>
</td>
<td class="explain">�ä˰�̣��̵������ġ�</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/profile.gif"></td>
<td colspan="2">
<textarea name="profile" cols="30" rows="2"></textarea>
<input type="hidden" name="role" value="none">
</td>
</tr>

HEADER;

  if($ROOM->IsOption('wish_role')){
    echo <<<IMAGE
<tr>
<td class="role"><img src="img/entry_user/role.gif"></td>
<td colspan="2">

IMAGE;

    $wish_role_list = array('none');
    if($ROOM->IsOptionGroup('chaos') || $ROOM->IsOption('duel') || $ROOM->IsOption('festival')){
      array_push($wish_role_list, 'human', 'mage', 'necromancer', 'medium', 'priest', 'guard',
		 'common', 'poison', 'poison_cat', 'pharmacist', 'assassin', 'mind_scanner',
		 'jealousy', 'doll', 'wolf', 'mad', 'fox', 'child_fox', 'cupid', 'angel', 'quiz',
		 'chiroptera', 'fairy', 'mania');
    }
    else{
      if(! $ROOM->IsOption('full_mania')) $wish_role_list[] = 'human';
      $wish_role_list[] = 'wolf';
      if($ROOM->IsQuiz()){
	array_push($wish_role_list, 'mad', 'common', 'fox');
      }
      else{
	array_push($wish_role_list, 'mage', 'necromancer', 'mad', 'guard', 'common');
	if($ROOM->IsOption('detective')) $wish_role_list[] = 'detective_common';
	$wish_role_list[] = 'fox';
      }
    }
    if($ROOM->IsOption('poison')) $wish_role_list[] = 'poison';
    if($ROOM->IsOption('assassin')) $wish_role_list[] = 'assassin';
    if($ROOM->IsOption('boss_wolf')) $wish_role_list[] = 'boss_wolf';
    if($ROOM->IsOption('poison_wolf')){
      array_push($wish_role_list, 'poison_wolf', 'pharmacist');
    }
    if($ROOM->IsOption('possessed_wolf')) $wish_role_list[] = 'possessed_wolf';
    if($ROOM->IsOption('sirius_wolf')) $wish_role_list[] = 'sirius_wolf';
    if($ROOM->IsOption('cupid')) $wish_role_list[] = 'cupid';
    if($ROOM->IsOption('medium')) array_push($wish_role_list, 'medium', 'mind_cupid');
    if($ROOM->IsOptionGroup('mania') && ! in_array('mania', $wish_role_list)){
      $wish_role_list[] = 'mania';
    }

    $count = 0;
    foreach($wish_role_list as $role){
      if($count > 0 && $count % 4 == 0) echo '<br>'; //4�Ĥ��Ȥ˲���
      $count++;
      echo <<<TAG
<label for="{$role}"><img src="img/entry_user/role_{$role}.gif"><input type="radio" id="{$role}" name="role" value="{$role}"></label>

TAG;
    }
    echo '</td>';
  }
  else{
    echo '<input type="hidden" name="role" value="none">';
  }

  echo <<<BODY
</tr>
<tr>
<td class="submit" colspan="3">
<span class="explain">
�桼��̾��¼�ͤ�̾�����ѥ���ɤ�����ζ��򤪤�Ӳ��ԥ����ɤϼ�ư�Ǻ������ޤ�
</span>
<input type="submit" value="¼����Ͽ����"></td>
</tr>
</table>
</td></tr>

<tr><td>
<fieldset><legend><img src="img/entry_user/icon.gif"></legend>
<table class="icon">
<tr><td colspan="5">
<input id="fix_number" type="radio" name="icon_no"><label for="fix_number">������</label>
<input type="text" name="icon_no" size="10px">(Ⱦ�ѱѿ������Ϥ��Ƥ�������)
</td></tr>

BODY;
  OutputIconList('user_manager');
  echo <<<FOOTER
</tr></table>
</fieldset>
</td></tr>

</table></div></form>
</body></html>

FOOTER;
}
