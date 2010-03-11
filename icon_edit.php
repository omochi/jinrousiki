<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF', 'USER_ICON');
$INIT_CONF->LoadRequest('RequestIconEdit'); //���������
EditIcon();

//-- �ؿ� --//
function EditIcon(){
  global $DB_CONF, $USER_ICON, $ICON_CONF, $RQ_ARGS;

  $title = '�桼�����������Խ�';
  if(CheckReferer('icon_view.php')){ //��ե�������å�
    OutputActionResult($title, '̵���ʥ��������Ǥ�');
  }

  extract($RQ_ARGS->ToArray()); //������Ÿ��
  if($password != $USER_ICON->password){
    OutputActionResult($title, '�ѥ���ɤ��㤤�ޤ�');
  }
  $query_stack = array();

  $DB_CONF->Connect(); //DB ��³
  //���������̾����������Ͽ����Ƥ��ʤ��������å�
  if(FetchResult('SELECT COUNT(icon_no) FROM user_icon WHERE icon_no = ' . $icon_no) < 1){
    OutputActionResult($title, '̵���ʥ��������ֹ�Ǥ���' . $icon_no);
  }

  //��������̾��ʸ����Ĺ�Υ����å�
  $text_list = array('icon_name' => '��������̾',
		     'appearance' => '��ŵ',
		     'category' => '���ƥ���',
		     'author' => '��������κ��');
  foreach($text_list as $text => $label){
    $value = $RQ_ARGS->$text;
    if(strlen($value) < 1) continue;
    if(strlen($value) > $USER_ICON->name){
      OutputActionResult($title, $label . ': ' . $USER_ICON->IconNameMaxLength());
    }
    $query_stack[] = "{$text} = '{$value}'";
  }

  //���������̾����������Ͽ����Ƥ��ʤ��������å�
  if(FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_name = '{$name}'") > 0){
    OutputActionResult($title, '��������̾ "' . $name . '" �ϴ�����Ͽ����Ƥ��ޤ�');
  }

  //������Υ����å�
  if(strlen($color) > 0){
    if(strlen($color) != 7 && ! preg_match('/^#[0123456789abcdefABCDEF]{6}/', $color)){
      $sentence = '�����꤬����������ޤ���<br>'."\n" .
	'����� (�㡧#6699CC) �Τ褦�� RGB 16�ʿ�����ǹԤäƤ���������<br>'."\n" .
	'�������줿������ �� <span class="color">' . $color . '</span>';
      OutputActionResult($title, $sentence);
    }
    $color = strtoupper($color);
    $query_stack[] = "color = '{$color}'";
  }

  if(count($query_stack) < 1){
    OutputActionResult($title, '�ѹ����ƤϤ���ޤ���');
  }
  $query = 'UPDATE user_icon SET ' . implode(', ', $query_stack) . ' WHERE icon_no = ' . $icon_no;
  //OutputActionResult($title, $query); //�ƥ�����

  if(! mysql_query('LOCK TABLES user_icon WRITE')){ //user_icon �ơ��֥���å�
    $sentence = "�����Ф��������Ƥ��ޤ���<br>\n���֤��֤��Ƥ������Ͽ�򤪴ꤤ���ޤ���";
    OutputActionResult($title, $sentence);
  }
  SendQuery($query, true);
  OutputActionResult($title, '�Խ���λ', 'icon_view.php?icon_no=' . $icon_no);
}
