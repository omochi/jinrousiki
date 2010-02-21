<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SESSION', 'ICON_CONF', 'USER_ICON');

if($USER_ICON->disable_upload){
  OutputActionResult('�桼���������󥢥åץ���', '���ߥ��åץ��ɤ���ߤ��Ƥ��ޤ�');
}
$INIT_CONF->LoadRequest('RequestIconUpload'); //���������
$RQ_ARGS->command != '' ? UploadIcon() : OutputUploadIconPage();

//-- �ؿ� --//
//��ƥǡ��������å�
function UploadIcon(){
  global $DB_CONF, $ICON_CONF, $USER_ICON, $RQ_ARGS, $SESSION;

  if(CheckReferer('icon_upload.php')){ //��ե�������å�
    OutputActionResult('�桼���������󥢥åץ���', '̵���ʥ��������Ǥ�');
  }
  $title = '����������Ͽ���顼'; // ���顼�ڡ����ѥ����ȥ�
  $query_no = "WHERE icon_no = {$RQ_ARGS->icon_no}";

  switch($RQ_ARGS->command){
  case 'upload':
    break;

  case 'success': //���å���� ID ����� DB ������
    $DB_CONF->Connect(); //DB ��³

    //���å���� ID �򥯥ꥢ
    mysql_query("UPDATE user_icon SET session_id = NULL $query_no");
    mysql_query('COMMIT');

    OutputActionResult('����������Ͽ��λ',
		       '��Ͽ��λ��������������Υڡ��������Ӥޤ���<br>'."\n" .
		       '�ڤ��ؤ��ʤ��ʤ� <a href="icon_view.php">����</a> ��',
		       'icon_view.php');
    break;

  case 'cancel':
    //DB ���饢������Υե�����̾����Ͽ���Υ��å���� ID �����
    $DB_CONF->Connect(); //DB ��³
    extract(FetchAssoc("SELECT icon_filename, session_id FROM user_icon $query_no", true));

    //���å���� ID ��ǧ
    if($session_id != $SESSION->Get()){
      OutputActionResult('��������������', '������ԡ����åץ��ɥ��å���󤬰��פ��ޤ���');
    }
    unlink($ICON_CONF->path . '/' . $icon_filename);
    mysql_query("DELETE FROM user_icon $query_no");
    mysql_query("OPTIMIZE TABLE user_icon");
    mysql_query('COMMIT'); //������ߥå�

    //DB ��³����� OutputActionResult() ��ͳ
    $sentence = '�����λ����Ͽ�ڡ��������Ӥޤ���<br>'."\n" .
      '�ڤ��ؤ��ʤ��ʤ� <a href="icon_upload.php">����</a> ��';
    OutputActionResult('������������λ', $sentence, 'icon_upload.php');
    break;

  default:
    OutputActionResult($title, '̵���ʥ��ޥ�ɤǤ�');
    break;
  }

  //���åץ��ɤ��줿�ե�����Υ��顼�����å�
  if($_FILES['upfile']['error'][$i] != 0){
    $sentence = "�ե�����Υ��åץ��ɥ��顼��ȯ�����ޤ�����<br>\n���ټ¹Ԥ��Ƥ���������";
    OutputActionResult($title, $sentence);
  }

  extract($RQ_ARGS->ToArray()); //������Ÿ��

  //��������̾�����򤫥����å�
  if($name == '') OutputActionResult($title, '��������̾�����Ϥ��Ƥ�������');

  //��������̾��ʸ����Ĺ�Υ����å�
  if(strlen($name) > $USER_ICON->name){
    OutputActionResult($title, $USER_ICON->IconNameMaxLength());
  }

  //�ե����륵�����Υ����å�
  if($size == 0) OutputActionResult($title, '�ե����뤬���Ǥ�');
  if($size > $USER_ICON->size){
    OutputActionResult($title, '�ե����륵������ ' . $USER_ICON->IconFileSizeMax());
  }

  //�ե�����μ���Υ����å�
  switch($type){
  case 'image/jpeg':
  case 'image/pjpeg':
    $ext = 'jpg';
    break;

  case 'image/gif':
    $ext = 'gif';
    break;

  case 'image/png':
  case 'image/x-png':
    $ext = 'png';
    break;

  default:
    OutputActionResult($title, $type . ' : jpg��gif��png �ʳ��Υե��������Ͽ�Ǥ��ޤ���');
    break;
  }

  //������Υ����å�
  if(strlen($color) != 7 && ! preg_match('/^#[0123456789abcdefABCDEF]{6}/', $color)){
    $sentence = '�����꤬����������ޤ���<br>'."\n" .
      '����� (�㡧#6699CC) �Τ褦�� RGB 16�ʿ�����ǹԤäƤ���������<br>'."\n" .
      '�������줿������ �� <span class="color">' . $color . '</span>';
    OutputActionResult($title, $sentence);
  }
  $color = strtoupper($color);

  //��������ι⤵����������å�
  list($width, $height) = getimagesize($tmp_name);
  if($width > $USER_ICON->width || $height > $USER_ICON->height){
    $sentence = '��������� ' . $USER_ICON->IconSizeMax() . ' ������Ͽ�Ǥ��ޤ���<br>'."\n" .
      '�������줿�ե����� �� <span class="color">�� ' . $width . '���⤵ ' . $height . '</span>';
    OutputActionResult($title, $sentence);
  }

  $DB_CONF->Connect(); //DB ��³

  //���������̾����������Ͽ����Ƥ��ʤ��������å�
  if(FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_name = '$name'") > 0){
    OutputActionResult($title, '��������̾ "' . $name . '" �ϴ�����Ͽ����Ƥ��ޤ�');
  }

  if(! mysql_query('LOCK TABLES user_icon WRITE')){ //user_icon �ơ��֥���å�
    $sentence = "�����Ф��������Ƥ��ޤ���<br>\n���֤��֤��Ƥ������Ͽ�򤪴ꤤ���ޤ���";
    OutputActionResult($title, $sentence);
  }

  //����������Ͽ���������ͤ�Ķ���Ƥʤ��������å�
  //������Ͽ����Ƥ��륢������ʥ�С���߽�˼���
  $icon_no = FetchResult('SELECT icon_no FROM user_icon ORDER BY icon_no DESC') + 1; //�����礭��No + 1
  if($icon_no >= $USER_ICON->number) OutputActionResult($title, '����ʾ���Ͽ�Ǥ��ޤ���', '', true);

  //�ե�����̾�η�򤽤���
  $file_name = sprintf("%03s.%s", $icon_no, $ext);

  //�ե������ƥ�ݥ�꤫�饳�ԡ�
  if(! move_uploaded_file($tmp_name, $ICON_CONF->path . '/' . $file_name)){
    $sentence = "�ե�����Υ��ԡ��˼��Ԥ��ޤ�����<br>\n���ټ¹Ԥ��Ƥ���������";
    OutputActionResult($title, $sentence, '', true);
  }

  //�ǡ����١�������Ͽ
  $session_id = $SESSION->Set(true); //���å���� ID �����
  $items = 'icon_no, icon_name, icon_filename, icon_width, icon_height, color, session_id';
  $values = "$icon_no, '$name', '$file_name', $width, $height, '$color', '$session_id'";
  InsertDatabase('user_icon', $items, $values);
  mysql_query('COMMIT'); //������ߥå�
  $DB_CONF->Disconnect(true); //DB ��³���

  //��ǧ�ڡ��������
  OutputHTMLHeader('�桼���������󥢥åץ��ɽ���[��ǧ]', 'icon_upload_check');
  echo <<<EOF
</head>
<body>
<p>�ե�����򥢥åץ��ɤ��ޤ�����<br>���������ʤ����Ǥ��ޤ�</p>
<img src="{$ICON_CONF->path}/{$file_name}" width="{$width}" height="{$height}"><br>
<table>
<tr><td>No. {$icon_no}<font color="{$color}">��</font>{$color}</td></tr>
<tr><td>������Ǥ�����</td></tr>
<tr><td><form method="POST" action="icon_upload.php">
  <input type="hidden" name="command" value="cancel">
  <input type="hidden" name="icon_no" value="$icon_no">
  <input type="submit" value="���ʤ���">
</form></td>
<td><form method="POST" action="icon_upload.php">
  <input type="hidden" name="command" value="success">
  <input type="hidden" name="icon_no" value="{$icon_no}">
  <input type="submit" value="��Ͽ��λ">
</form></td></tr></table>
</body></html>

EOF;
}

//���åץ��ɥե��������
function OutputUploadIconPage(){
  global $USER_ICON;

  OutputHTMLHeader('�桼���������󥢥åץ���', 'icon_upload');
  $icon_size_max = $USER_ICON->IconSizeMax();
  $icon_file_size_max = $USER_ICON->IconFileSizeMax();
  $icon_name_length_max = $USER_ICON->IconNameMaxLength();

  echo <<<EOF
</head>
<body>
<a href="./">�����</a><br>
<img class="title" src="img/icon_upload_title.jpg"><br>
<table align="center">
<tr><td class="link"><a href="icon_view.php">�������������</a></td><tr>
<tr><td class="caution">�����餫������ꤹ���礭�� ({$icon_size_max}) �˥ꥵ�������Ƥ��饢�åץ��ɤ��Ƥ���������</td></tr>
<tr><td>
<fieldset><legend>����������� (jpg, gif, png ��������Ͽ���Ʋ�������{$icon_file_size_max})</legend>
<form method="POST" action="icon_upload.php" enctype="multipart/form-data">
<table>
<tr><td><label>�ե���������</label>
<input type="file" name="file" size="80">
<input type="hidden" name="max_file_size" value="{$USER_ICON->size}">
<input type="hidden" name="command" value="upload">
<input type="submit" value="��Ͽ">
</td></tr>

<tr><td><label>���������̾��</label>
<input type="text" name="name" maxlength="20" size="20">{$icon_name_length_max}</td></tr>

<tr><td><label>��������˹�ä��������򤷤Ƥ�������</label></td></tr>
<tr><td>
<input id="fix_color" type="radio" name="color"><label for="fix_color">������ꤹ��</label>
<input type="text" name="color" size="10px" maxlength="7">(�㡧#6699CC)
</td></tr>

<tr><td>
<table class="color" align="center">
<tr>

EOF;

  $color_base = array();
  for($i = 0; $i < 256; $i += 51){
    $color_base[] = sprintf('%02X', $i);
  }

  $color_list = array();
  foreach($color_base as $i => $r){
    foreach($color_base as $j => $g){
      foreach($color_base as $k => $b){
	$color_list["#{$r}{$g}{$b}"] = (($i + $j + $k) < 8  && ($i + $j) < 5);
      }
    }
  }

  $count = 0;
  foreach($color_list as $color => $bright){
    if($count > 0 && ($count % 6) == 0) echo "</tr>\n<tr>\n"; //6�Ĥ��Ȥ˲���
    $count++;

    echo <<<EOF
<td bgcolor="{$color}"><label for="{$color}"><input type="radio" id="{$color}" name="color" value="{$color}">
EOF;

    if($bright) $color = '<font color="#FFFFFF">' . $color . '</font>';
    echo $color . "</label></td>\n";
  }
  echo <<<EOF
</tr>
</table>

</td></tr></table></form></fieldset>
</td></tr></table>
</body></html>

EOF;
}
