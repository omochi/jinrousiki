<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SESSION'); //���å���󥹥�����
$INIT_CONF->LoadRequest('RequestLogin'); //���������
$DB_CONF->Connect(); //DB ��³

//-- ��������� --//
//DB ��³����Ϸ�̽��ϴؿ����Ԥ�
if($RQ_ARGS->login_manually){ //�桼��̾�ȥѥ���ɤǼ�ư������
  if(LoginManually()){
    OutputLoginResult('�����󤷤ޤ���', 'game_frame');
  }
  else{
    OutputLoginResult('��������', NULL, '�桼��̾�ȥѥ���ɤ����פ��ޤ���<br>' .
		      '(����Ȳ��ԥ����ɤ���Ͽ���˼�ư�Ǻ������Ƥ��������դ��Ƥ�������)');
  }
}

if($SESSION->Certify(false)){ //���å����ID���鼫ư������
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

//��ư���������
/*
  ���å����򼺤ä���硢�桼��̾�ȥѥ���ɤǥ����󤹤�
  ����������/���Ԥ� true/false ���֤�
*/
function LoginManually(){
  global $SESSION, $RQ_ARGS;

  extract($RQ_ARGS->ToArray());
  if($uname == '' || $password == '') return false;

  //IP���ɥ쥹���� //���ߤ� IP ���ɥ쥹ǧ�ڤϹԤäƤ��ʤ�
  //$ip_address = $_SERVER['REMOTE_ADDR'];
  $crypt_password = CryptPassword($password);
  //$crypt_password = $password; //�ǥХå���

  //���̥�����
  $query_base = "WHERE room_no = {$room_no} AND uname = '{$uname}' AND user_no > 0";

  //��������桼��̾�ȥѥ���ɤ����뤫��ǧ
  $query = "SELECT COUNT(uname) FROM user_entry {$query_base} AND password = '{$crypt_password}'";
  if(FetchResult($query) != 1) return false;

  //DB�Υ��å����ID�򹹿�
  $session_id = $SESSION->Get(true); //���å����ID�κ���Ͽ
  SendQuery("UPDATE user_entry SET session_id = '{$session_id}' {$query_base}", true);
  return true;
}
