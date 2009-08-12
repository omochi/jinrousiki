<?php
require_once(dirname(__FILE__) . '/include/functions.php');

EncodePostData();//�ݥ��Ȥ��줿ʸ����򥨥󥳡��ɤ���

if($_GET['room_no'] == ''){
  $sentence = '���顼��¼���ֹ椬����ǤϤ���ޤ���<br>'."\n".'<a href="index.php">�����</a>';
  OutputActionResult('¼����Ͽ [¼�ֹ楨�顼]', $sentence);
}

$dbHandle = ConnectDatabase(); //DB ��³

if($_POST['command'] == 'entry'){
  EntryUser((int)$_GET['room_no'], $_POST['uname'], $_POST['handle_name'], (int)$_POST['icon_no'],
	    $_POST['profile'], $_POST['password'], $_POST['sex'], $_POST['role']);
}
else{
  OutputEntryUserPage((int)$_GET['room_no']);
}

DisconnectDatabase($dbHandle); //DB ��³���

// �ؿ� //
//�桼������Ͽ����
function EntryUser($room_no, $uname, $handle_name, $icon_no, $profile, $password, $sex, $role){
  global $GAME_CONF, $MESSAGE;

  //�ȥ�åס����������׽���
  ConvertTrip(&$uname);
  ConvertTrip(&$handle_name);
  EscapeStrings(&$profile, false);
  EscapeStrings(&$password);

  //����ϳ������å�
  if($uname == '' || $handle_name == '' || $icon_no == '' || $profile == '' ||
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
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no";

  //�桼��̾��¼��̾
  $sql = mysql_query("$query AND (uname = '$uname' OR handle_name = '$handle_name') AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0){
    OutputActionResult('¼����Ͽ [��ʣ��Ͽ���顼]',
		       '�桼��̾���ޤ���¼��̾��������Ͽ���Ƥ���ޤ���<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }

  //���å����줿�ͤ�Ʊ���桼��̾
  $sql = mysql_query("$query AND uname = '$uname' AND user_no = -1");
  if(mysql_result($sql, 0, 0) != 0){
    OutputActionResult('¼����Ͽ [���å����줿�桼��]',
		       '���å����줿�ͤ�Ʊ���桼��̾�ϻ��ѤǤ��ޤ��� (¼��̾�ϲ�)<br>'."\n" .
		       '�̤�̾���ˤ��Ƥ���������');
  }

  //IP���ɥ쥹�����å�
  $ip_address = $_SERVER['REMOTE_ADDR']; //�桼����IP���ɥ쥹�����
  if($GAME_CONF->entry_one_ip_address){
    $sql = mysql_query("$query AND ip_address = '$ip_address' AND user_no > 0");
    if(mysql_result($sql, 0, 0) != 0){
      OutputActionResult('¼����Ͽ [¿����Ͽ���顼]', '¿����Ͽ�ϤǤ��ޤ���');
    }
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
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = $room_no
			AND user_no > 0 ORDER BY user_no DESC");
  $array = mysql_fetch_assoc($sql);
  $user_no = (int)$array['user_no'] + 1; //�Ǥ��礭�� No + 1

  //DB�������Ϳ������
  $sql = mysql_query("SELECT day_night, status, max_user FROM room WHERE room_no = $room_no");
  $array  = mysql_fetch_assoc($sql);
  $day_night = $array['day_night'];
  $status    = $array['status'];
  $max_user  = $array['max_user'];

  //��������С����Ƥ���Ȥ�
  if($user_no > $max_user || $day_night != 'beforegame' || $status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]',
		       '¼�������������������ब���Ϥ���Ƥ��ޤ���', '', true);
  }

  //���å���󳫻�
  session_start();
  $session_id = '';

  do{ //DB ����Ͽ����Ƥ��륻�å���� ID �����ʤ��褦�ˤ���
    session_regenerate_id();
    $session_id = session_id();
    $sql = mysql_query("SELECT COUNT(room_no) FROM user_entry, admin_manage
			WHERE user_entry.session_id = '$session_id'
			OR admin_manage.session_id = '$session_id'");
  }while(mysql_result($sql, 0, 0) != 0);

  //DB �˥桼���ǡ�����Ͽ
  $entry = mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name,
			icon_no, profile, sex, password, role, live, session_id,
			last_words, ip_address, last_load_day_night)
			VALUES($room_no, $user_no, '$uname', '$handle_name', $icon_no,
			'$profile', '$sex', '$password', '$role', 'live',
			'$session_id', '', '$ip_address', 'beforegame')");

  //��¼��å�����
  InsertTalk($room_no, 0, 'beforegame system', 'system', $system_time,
	     $handle_name . ' ' . $MESSAGE->entry_user, NULL, 0);

  mysql_query('COMMIT'); //������ߥå�
  //��Ͽ���������Ƥ��ơ�����Υ桼�����Ǹ�Υ桼���ʤ��罸��λ����
  // if($entry && ($user_no == $max_user))
  //   mysql_query("update room set status = 'playing' where room_no = $room_no");

  if($entry){
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
function ConvertTrip(&$str){
  global $ENCODE, $GAME_CONF;

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
      $key  = mb_convert_encoding($key, 'SJIS', $ENCODE);
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

  EscapeStrings(&$str); //�ü�ʸ���Υ���������
}

//�桼����Ͽ����ɽ��
function OutputEntryUserPage($room_no){
  global $SERVER_CONF, $ICON_CONF;

  $sql = mysql_query("SELECT room_name, room_comment, status, game_option, option_role
			FROM room WHERE room_no = $room_no");
  if(mysql_num_rows($sql) == 0){
    OutputActionResult('¼����Ͽ [¼�ֹ楨�顼]', "No.$room_no ���Ϥ�¼��¸�ߤ��ޤ���");
  }

  $array = mysql_fetch_assoc($sql);
  $room_name    = $array['room_name'];
  $room_comment = $array['room_comment'];
  $status       = $array['status'];
  $game_option  = $array['game_option'];
  $option_role  = $array['option_role'];
  if($status != 'waiting'){
    OutputActionResult('¼����Ͽ [��¼�Բ�]', '¼�������������������ब���Ϥ���Ƥ��ޤ���');
  }

  //�桼�������������
  $sql_icon = mysql_query("SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color
				FROM user_icon WHERE icon_no > 0 ORDER BY icon_no");
  $trip_str = '(�ȥ�å׻���' . ($GAME_CONF->trip ? '��ǽ' : '�Բ�') . ')';

  OutputHTMLHeader($SERVER_CONF->title .'[¼����Ͽ]', 'entry_user');
  echo <<<HEADER
</head>
<body>
<a href="index.php">�����</a><br>
<form method="POST" action="user_manager.php?room_no=$room_no">
<input type="hidden" name="command" value="entry">
<div align="center">
<table class="main">
<tr><td><img src="img/entry_user/title.gif"></td></tr>
<tr><td class="title">$room_name ¼<img src="img/entry_user/top.gif"></td></tr>
<tr><td class="number">��{$room_comment}�� [{$room_no} ����]</td></tr>
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
<td class="explain">���å�����ڤ줿���˥�������˻Ȥ��ޤ�<br> (�Ź沽����Ƥ��ʤ��Τ������)</td>
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

  if(strpos($game_option, 'wish_role') !== false){
    echo <<<IMAGE
<tr>
<td class="role"><img src="img/entry_user/role.gif"></td>
<td colspan="2">

IMAGE;

    if(strpos($game_option, 'quiz') !== false){
      $wish_role_list = array('none', 'human', 'wolf', 'mad', 'common', 'fox');
    }
    else{
      $wish_role_list = array('none', 'human', 'wolf', 'mage', 'necromancer',
			      'mad', 'guard', 'common', 'fox');
    }
    if(strpos($option_role, 'poison')      !== false) array_push($wish_role_list, 'poison');
    if(strpos($option_role, 'cupid')       !== false) array_push($wish_role_list, 'cupid');
    if(strpos($option_role, 'boss_wolf')   !== false) array_push($wish_role_list, 'boss_wolf');
    if(strpos($option_role, 'poison_wolf') !== false){
      array_push($wish_role_list, 'poison_wolf');
      array_push($wish_role_list, 'pharmacist');
    }
    if(strpos($option_role, 'mania')       !== false) array_push($wish_role_list, 'mania');
    if(strpos($option_role, 'medium')      !== false){
      array_push($wish_role_list, 'medium');
      array_push($wish_role_list, 'fanatic_mad');
    }

    $count = 0;
    foreach($wish_role_list as $this_role){
      echo <<<TAG
<label for="{$this_role}"><img src="img/entry_user/role_{$this_role}.gif"><input type="radio" id="{$this_role}" name="role" value="{$this_role}"></label>

TAG;
      if(++$count % 4 == 0) echo '<br>'; //4�Ĥ��Ȥ˲���
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
<tr>

BODY;

  //��������ν���
  $count = 0;
  while(($array = mysql_fetch_assoc($sql_icon)) !== false){
    $icon_no       = $array['icon_no'];
    $icon_name     = $array['icon_name'];
    $icon_filename = $array['icon_filename'];
    $icon_width    = $array['icon_width'];
    $icon_height   = $array['icon_height'];
    $color         = $array['color'];
    $icon_location = $ICON_CONF->path . '/' . $icon_filename;

    echo <<<ICON
<td><label for="$icon_no"><img src="$icon_location" width="$icon_width" height="$icon_height" style="border-color:$color;">
$icon_name<br><font color="$color">��</font><input type="radio" id="$icon_no" name="icon_no" value="$icon_no"></label></td>

ICON;
    if(++$count % 5 == 0) echo '</tr><tr>'; //5�Ĥ��Ȥ˲���
  }

  echo <<<FOOTER
</tr></table>
</fieldset>
</td></tr>

</table></div></form>
</body></html>

FOOTER;
}
?>
