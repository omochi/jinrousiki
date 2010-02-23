<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('room_class');
$INIT_CONF->LoadClass('SESSION', 'GAME_CONF', 'ICON_CONF', 'MESSAGE');

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
  if($uname == '' || $handle_name == '' || $icon_no < 1 || $profile == '' ||
     $password == '' || $sex == '' || $role == ''){
    OutputActionResult('¼����Ͽ [���ϥ��顼]',
		       '����ϳ�줬����ޤ���<br>'."\n" .
		       '�������Ϥ��Ƥ������� (����Ȳ��ԥ����ɤϼ�ư�Ǻ������ޤ�)��');
  }

  //�����ƥ�桼�������å�
  if($uname == 'dummy_boy' || $uname == 'system' ||
     $handle_name == '�����귯' || $handle_name == '�����ƥ�'){
    OutputActionResult('¼����Ͽ [���ϥ��顼]',
		       '������̾������Ͽ�Ǥ��ޤ���<br>'."\n" .
		       '�桼��̾��dummy_boy or system<br>'."\n" .
		       '¼�ͤ�̾���������귯 or �����ƥ�');
  }

  //�����������å�
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND";

  //�桼��̾��¼��̾
  if(FetchResult("$query (uname = '$uname' OR handle_name = '$handle_name') AND user_no > 0") > 0){
    OutputActionResult('¼����Ͽ [��ʣ��Ͽ���顼]',
		       '�桼��̾���ޤ���¼��̾��������Ͽ���Ƥ���ޤ���<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }

  //���å����줿�ͤ�Ʊ���桼��̾
  if(FetchResult("$query uname = '$uname' AND user_no = -1") > 0){
    OutputActionResult('¼����Ͽ [���å����줿�桼��]',
		       '���å����줿�ͤ�Ʊ���桼��̾�ϻ��ѤǤ��ޤ��� (¼��̾�ϲ�)<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }

  //IP���ɥ쥹�����å�
  $ip_address = $_SERVER['REMOTE_ADDR']; //�桼����IP���ɥ쥹�����
  if(! $DEBUG_MODE && $GAME_CONF->entry_one_ip_address &&
     FetchResult("$query ip_address = '$ip_address' AND user_no > 0") > 0){
    OutputActionResult('¼����Ͽ [¿����Ͽ���顼]', '¿����Ͽ�ϤǤ��ޤ���');
  }

  //�ơ��֥���å�
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, talk WRITE, admin_manage READ')){
    OutputActionResult('¼����Ͽ [�����Х��顼]',
		       '�����Ф��������Ƥ��ޤ���<br>'."\n" .
		       '������Ͽ���Ƥ�������');
  }

  //���å����κ��
  $system_time = TZTime(); //���߻�������
  $cookie_time = $system_time - 3600;
  setcookie('day_night',  '', $cookie_time);
  setcookie('vote_times', '', $cookie_time);
  setcookie('objection',  '', $cookie_time);

  //DB����桼��No��߽�˼���
  $query_no = "SELECT user_no FROM user_entry WHERE room_no = $room_no " .
    "AND user_no > 0 ORDER BY user_no DESC";
  $user_no = (int)FetchResult($query_no) + 1; //�Ǥ��礭�� No + 1

  //DB�������Ϳ������
  $query_status = "SELECT day_night, status, max_user FROM room WHERE room_no = {$room_no}";
  extract(FetchAssoc($query_status, true));

  //��������С����Ƥ���Ȥ�
  if($user_no > $max_user || $day_night != 'beforegame' || $status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]',
		       '¼�������������������ब���Ϥ���Ƥ��ޤ���', '', true);
  }

  //DB �˥桼���ǡ�������Ͽ
  $session_id = $SESSION->Get(true); //���å���� ID �����
  $crypt_password = CryptPassword($password);
  $items = 'room_no, user_no, uname, handle_name, icon_no, profile, sex, password, role, live, ' .
    'session_id, last_words, ip_address, last_load_day_night';
  $values = "$room_no, $user_no, '$uname', '$handle_name', $icon_no, '$profile', '$sex', " .
    "'$crypt_password', '$role', 'live', '$session_id', '', '$ip_address', 'beforegame'";

  if(InsertDatabase('user_entry', $items, $values)){
    //��¼��å�����
    InsertTalk($room_no, 0, 'beforegame system', 'system', $system_time,
	       $handle_name . ' ' . $MESSAGE->entry_user, NULL, 0);
    mysql_query('COMMIT'); //������ߥå�

    $url = "game_frame.php?room_no=$room_no";
    OutputActionResult('¼����Ͽ',
		       $user_no . ' ���ܤ�¼����Ͽ��λ��¼�δ��礤�ڡ��������Ӥޤ���<br>'."\n" .
		       '�ڤ��ؤ��ʤ��ʤ� <a href="' . $url. '">����</a> ��',
		       $url, true);
  }
  else{
    OutputActionResult('¼����Ͽ [�ǡ����١��������Х��顼]',
		       '�ǡ����١��������Ф��������Ƥ��ޤ���<br>'."\n" .
		       '���֤��֤��ƺ�����Ͽ���Ƥ���������', '', true);
  }
  mysql_query('UNLOCK TABLES'); //��å����
}

//�ȥ�å��Ѵ�
/*
  �Ѵ��ƥ��ȷ�̡�2ch (2009/07/26)
  [����ʸ����] => [�Ѵ����] (ConvetTrip()�η��)
  test#test                     => test ��.CzKQna1OU (test��.CzKQna1OU)
  �ƥ���#�ƥ���                 => �ƥ��� ��SQ2Wyjdi7M (�ƥ��Ȣ�SQ2Wyjdi7M)
  �Ƥ��ȡ��Ƥ���                => �Ƥ��� ��ZUNa78GuQc (�Ƥ��Ȣ�ZUNa78GuQc)
  �Ƥ��ȥƥ���#�Ƥ��ȡ��ƥ���   => �Ƥ��ȥƥ��� ��TBYWAU/j2qbJ (�Ƥ��ȥƥ��Ȣ�sXitOlnF0g)
  �ƥ��ȤƤ��ȡ��ƥ��ȤƤ���    => �ƥ��ȤƤ��� ��RZ9/PhChteSA (�ƥ��ȤƤ��Ȣ�XuUGgmt7XI)
  �ƥ��ȤƤ��ȡ��ƥ��ȤƤ���#   => �ƥ��ȤƤ��� ��rtfFl6edK5fK (�ƥ��ȤƤ��Ȣ�XuUGgmt7XI)
  �ƥ��ȤƤ��ȡ��ƥ��ȤƤ��ȡ�  => �ƥ��ȤƤ��� ��rtfFl6edK5fK (�ƥ��ȤƤ��Ȣ�XuUGgmt7XI)
*/
function ConvertTrip($str){
  global $SERVER_CONF, $GAME_CONF;

  if($GAME_CONF->trip){ //�ޤ���������Ƥ��ޤ���
    OutputActionResult('¼����Ͽ [���ϥ��顼]',
                       '�ȥ�å��Ѵ������ϼ�������Ƥ��ޤ���<br>'."\n" .
                       '�����Ԥ��䤤��碌�Ƥ���������');

    //�ȥ�å״�Ϣ�Υ�����ɤ��ִ�
    $str = str_replace(array('��', '��'), array('��', '#'), $str);
    if(($trip_start = mb_strpos($str, '#')) !== false){ //�ȥ�åץ����ΰ��֤򸡺�
      $name = mb_substr($str, 0, $trip_start);
      $key  = mb_substr($str, $trip_start + 1);
      #echo 'trip_start: '.$trip_start.', name: '.$name.', key:'.$key.'<br>'; //�ǥХå���

      //ʸ�������ɤ��Ѵ�
      $key  = mb_convert_encoding($key, 'SJIS', $SERVER_CONF->encode);
      $salt = substr($key.'H.', 1, 2);

      //$salt =~ s/[^\.-z]/\./go;�ˤ�����ս�
      $pattern = '/[\x00-\x20\x7B-\xFF]/';
      $salt = preg_replace($pattern, '.', $salt);

      //�ü�ʸ�����ִ�
      $from_list = array(':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`');
      $to_list   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'a', 'b', 'c', 'd', 'e', 'f');
      $salt = str_replace($from_list, $to_list, $salt);

      $trip = crypt($key, $salt);
      $str = $name.'��'.substr($trip, -10);
    }
    #echo 'result: '.$str.'<br>'; //�ǥХå���
  }
  elseif(strpos($str, '#') !== false || strpos($str, '��') !== false){
    OutputActionResult('¼����Ͽ [���ϥ��顼]',
		       '�ȥ�åפϻ����ԲĤǤ���<br>'."\n" .
		       '"#" ���� "��" ��ʸ��������ԲĤǤ���');
  }

  return EscapeStrings($str); //�ü�ʸ���Υ���������
}

//�桼����Ͽ����ɽ��
function OutputEntryUserPage(){
  global $SERVER_CONF, $GAME_CONF, $ICON_CONF, $RQ_ARGS;

  extract($RQ_ARGS->ToArray()); //���������
  $ROOM = RoomDataSet::LoadEntryUser($room_no);
  if(is_null($ROOM->id)){
    OutputActionResult('¼����Ͽ [¼�ֹ楨�顼]', "{$room_no} ���Ϥ�¼��¸�ߤ��ޤ���");
  }

  if($ROOM->status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]', '¼�������������������ब���Ϥ���Ƥ��ޤ���');
  }
  $ROOM->ParseOption(true);
  $trip_str = '(�ȥ�å׻���' . ($GAME_CONF->trip ? '��ǽ' : '�Բ�') . ')';

  OutputHTMLHeader($SERVER_CONF->title .'[¼����Ͽ]', 'entry_user');
  echo <<<HEADER
</head>
<body>
<a href="./">�����</a><br>
<form method="POST" action="user_manager.php?room_no={$room_no}">
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
<td class="explain">���ʤ�ɽ�����줺��¾�Υ桼��̾���狼��Τ�<br>��˴�����Ȥ��ȥ����ཪλ��ΤߤǤ�{$trip_str}</td>
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
    if($ROOM->IsOption('duel')){
      array_push($wish_role_list, 'wolf', 'trap_mad', 'assassin');
    }
    else{
      if($ROOM->IsOption('chaos')){
	array_push($wish_role_list, 'human', 'mage', 'necromancer', 'guard', 'common',
		   'poison', 'pharmacist', 'wolf', 'mad', 'fox', 'cupid', 'mania');
      }
      elseif($ROOM->IsOption('chaosfull')){
	array_push($wish_role_list, 'human', 'mage', 'necromancer', 'priest', 'guard', 'common',
		   'poison', 'poison_cat', 'pharmacist', 'assassin', 'mind_scanner', 'jealousy',
		   'wolf', 'mad', 'fox', 'cupid', 'quiz', 'chiroptera', 'mania');
      }
      else{
	if(! $ROOM->IsOption('full_mania')) $wish_role_list[] = 'human';
	$wish_role_list[] = 'wolf';
	if($ROOM->IsQuiz()){
	  array_push($wish_role_list, 'mad', 'common', 'fox');
	}
	else{
	  array_push($wish_role_list, 'mage', 'necromancer', 'mad', 'guard', 'common', 'fox');
	}
      }
    }
    if($ROOM->IsOption('poison')) $wish_role_list[] = 'poison';
    if($ROOM->IsOption('assassin')) $wish_role_list[] = 'assassin';
    if($ROOM->IsOption('boss_wolf')) $wish_role_list[] = 'boss_wolf';
    if($ROOM->IsOption('poison_wolf')){
      array_push($wish_role_list, 'poison_wolf', 'pharmacist');
    }
    if($ROOM->IsOption('possessed_wolf')) $wish_role_list[] = 'possessed_wolf';
    if($ROOM->IsOption('cupid')) $wish_role_list[] = 'cupid';
    if($ROOM->IsOption('medium')) array_push($wish_role_list, 'medium', 'mind_cupid');
    if($ROOM->IsOptionGroup('mania')) $wish_role_list[] = 'mania';

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

  //��������ν���
  $url_option = array('room_no' => 'room_no='. $room_no);
  $icon_count = FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_no > 0");
  echo '<tr><td colspan="5">'."\n";
  OutputPageLink('user_manager', $ICON_CONF, $icon_count, $url_option);
  echo "</td></tr>\n<tr>\n";

  //�桼����������Υơ��֥뤫����������
  $query_icon = "SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color " .
    "FROM user_icon WHERE icon_no > 0 ORDER BY icon_no";
  if($RQ_ARGS->page != 'all'){
    $query_icon .= sprintf(' LIMIT %d, %d', $ICON_CONF->view * ($RQ_ARGS->page - 1), $ICON_CONF->view);
  }
  $icon_list = FetchAssoc($query_icon);

  //ɽ�ν���
  $count = 0;
  foreach($icon_list as $array){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;
    extract($array);
    $icon_location = $ICON_CONF->path . '/' . $icon_filename;

    echo <<<ICON
<td><label for="{$icon_no}"><img src="{$icon_location}" width="{$icon_width}" height="{$icon_height}" style="border-color:{$color};"> No. {$icon_no}<br> {$icon_name}<br>
<font color="{$color}">��</font><input type="radio" id="{$icon_no}" name="icon_no" value="{$icon_no}"></label></td>

ICON;
  }

  echo <<<FOOTER
</tr></table>
</fieldset>
</td></tr>

</table></div></form>
</body></html>

FOOTER;
}
