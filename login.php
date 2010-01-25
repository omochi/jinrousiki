<?php
require_once('include/init.php');

$dbHandle = ConnectDatabase(); //DB ��³

//���å���󳫻�
session_start();
$session_id = session_id();

$RQ_ARGS =& new RequestLogin(); //���������

//�ѿ��򥻥å�
$url     = 'game_frame.php?room_no=' . $RQ_ARGS->room_no;
$header  = '��<br>' . "\n" . '�ڤ��ؤ��ʤ��ʤ� <a href="';
$footer  = '" target="_top">����</a> ��';
$anchor  = $header . $url . $footer;

//���������
//DB ��³����� OutputActionResult() ���Ԥ�
if($RQ_ARGS->login_type == 'manually'){ //�桼��̾�ȥѥ���ɤǼ�ư������
  if(LoginManually()){
    OutputActionResult('�����󤷤ޤ���', '�����󤷤ޤ���' . $anchor, $url);
  }
  else{
    OutputActionResult('��������', '�桼��̾�ȥѥ���ɤ����פ��ޤ���<br>' .
		       '(����Ȳ��ԥ����ɤ���Ͽ���˼�ư�Ǻ������Ƥ��������դ��Ƥ�������)');
  }
}
elseif(CheckSession($session_id, false)){ //���å����ID���鼫ư������
  OutputActionResult('�����󤷤Ƥ��ޤ�', '�����󤷤Ƥ��ޤ�' . $anchor, $url);
}
else{ //ñ�˸ƤФ줿�����ʤ����ڡ����˰�ư������
  $url    = 'game_view.php?room_no=' . $RQ_ARGS->room_no;
  $anchor = $header . $url . $footer;
  OutputActionResult('����ڡ����˥�����', '����ڡ����˰�ư���ޤ�' . $anchor, $url);
}

//-- �ؿ� --//
//�桼��̾�ȥѥ���ɤǥ�����
//�֤��͡�������Ǥ��� true / �Ǥ��ʤ��ä� false
function LoginManually(){
  global $RQ_ARGS;

  //���å����򼺤ä���硢�桼��̾�ȥѥ���ɤǥ����󤹤�
  $room_no  = $RQ_ARGS->room_no;
  $uname    = $RQ_ARGS->uname;
  $password = $RQ_ARGS->password;
  if($uname == '' || $password == '') return false;

  // //IP���ɥ쥹����
  // $ip_address = $_SERVER['REMOTE_ADDR']; //�ä˻��Ȥ��Ƥʤ��褦�����ɡġ�
  $crypt_password = CryptPassword($password);
  // $crypt_password = $password; //�ǥХå���

  //��������桼��̾�ȥѥ���ɤ����뤫��ǧ
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no " .
    "AND uname = '$uname' AND password = '$crypt_password' AND user_no > 0";
  if(FetchResult($query) != 1) return false;

  //���å����ID�κ���Ͽ
  $session_id = GetUniqSessionID();

  //DB�Υ��å����ID�򹹿�
  mysql_query("UPDATE user_entry SET session_id = '$session_id'
		WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
  mysql_query('COMMIT'); //������ߥå�
  return true;
}
?>
