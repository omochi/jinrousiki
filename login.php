<?php
require_once('include/init.php');

$INIT_CONF->LoadRequest('RequestLogin'); //���������
$DB_CONF->Connect(); //DB ��³
session_start(); //���å���󳫻�

//-- ��������� --//
//DB ��³����� OutputActionResult() ���Ԥ�
if($RQ_ARGS->login_type == 'manually'){ //�桼��̾�ȥѥ���ɤǼ�ư������
  if(LoginManually()){
    OutputLoginResult('�����󤷤ޤ���', 'game_frame');
  }
  else{
    OutputLoginResult('��������', NULL, '�桼��̾�ȥѥ���ɤ����פ��ޤ���<br>' .
		      '(����Ȳ��ԥ����ɤ���Ͽ���˼�ư�Ǻ������Ƥ��������դ��Ƥ�������)');
  }
}
elseif(CheckSession(session_id(), false)){ //���å����ID���鼫ư������
  OutputLoginResult('�����󤷤Ƥ��ޤ�', 'game_frame');
}
else{ //ñ�˸ƤФ줿�����ʤ����ڡ����˰�ư������
  OutputLoginResult('����ڡ����˥�����', 'game_view', '����ڡ����˰�ư���ޤ�');
}

//-- �ؿ� --//
//��̽��ϴؿ�
function OutputLoginResult($title, $jump, $body = NULL){
  global $RQ_ARGS;

  if(is_null($body)) $body = $title;
  if(is_null($jump)){
    $url = '';
  }
  else{
    $url = $jump . '.php?room_no=' . $RQ_ARGS->room_no;
    $body .= '��<br>' . "\n" . '�ڤ��ؤ��ʤ��ʤ� <a href="' . $url . '" target="_top">����</a> ��';
  }
  OutputActionResult($title, $body, $url);
}

//�桼��̾�ȥѥ���ɤǥ�����
//�֤��͡�������Ǥ��� true / �Ǥ��ʤ��ä� false
function LoginManually(){
  global $RQ_ARGS;

  //���å����򼺤ä���硢�桼��̾�ȥѥ���ɤǥ����󤹤�
  $room_no  = $RQ_ARGS->room_no;
  $uname    = $RQ_ARGS->uname;
  $password = $RQ_ARGS->password;
  if($uname == '' || $password == '') return false;

  //���̥�����
  $query = "WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0";

  // //IP���ɥ쥹����
  // $ip_address = $_SERVER['REMOTE_ADDR']; //�ä˻��Ȥ��Ƥʤ��褦�����ɡġ�
  $crypt_password = CryptPassword($password);
  //$crypt_password = $password; //�ǥХå���

  //��������桼��̾�ȥѥ���ɤ����뤫��ǧ
  $query_password = "SELECT COUNT(uname) FROM user_entry $query AND password = '$crypt_password'";
  if(FetchResult($query_password) != 1) return false;

  //���å����ID�κ���Ͽ
  $session_id = GetUniqSessionID();

  //DB�Υ��å����ID�򹹿�
  mysql_query("UPDATE user_entry SET session_id = '$session_id' $query");
  mysql_query('COMMIT'); //������ߥå�
  return true;
}
