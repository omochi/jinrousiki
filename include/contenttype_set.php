<?php
//-- ������ץȷ���ʸ�������� --//
/*
  �ѹ�����������ƤΥե����뼫�Τ�ʸ�������ɤ������ѹ����Ƥ�������
  $ENCODE = 'EUC-JP';

  Ver. 1.4.0 ��24 �����ʸ�������ɤ����ꤹ�����ʲ��ξ����ѹ����ޤ���
  include/init.php
*/

// ���󥳡��ǥ��󥰻��� PHP�С������ˤ�äƻ�����ˡ���ۤʤ� //
$php_version_array = explode('.', phpversion());
if($php_version_array[0] <= 4 && $php_version_array[1] < 3){ //4.3.x̤��
  //	encoding $SERVER_CONF->encode;  //���顼���Ф롩��
}
else{ //4.3.x�ʹ�
  declare(encoding='EUC-JP'); //�ѿ��������ȥѡ������顼���֤�Τǥϡ��ɥ�����
}

// �ޥ���Х��������ϻ��� //
if(extension_loaded('mbstring')){
  mb_language('ja');
  mb_internal_encoding($SERVER_CONF->encode);
  mb_http_input ('auto');
  mb_http_output($SERVER_CONF->encode);
}

// �����Υ����ФǤ�ư���褦�˥إå���������   //
// ��������������ʸ������������˻��ꤷ�ޤ� //

//�إå����ޤ�������������Ƥ��ʤ������������
if(! headers_sent()){
  header("Content-type: text/html; charset={$SERVER_CONF->encode}");
  header('Content-Language: ja');
}
?>
