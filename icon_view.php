<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF');
OutputHTMLHeader('�桼�������������', 'icon_view')
?>
</head>
<body>
<a href="./">�����</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">������������Ͽ</a></div>

<fieldset><legend>�桼�������������</legend>
<table><tr>
<?php
$DB_CONF->Connect(true); //DB ��³

//�桼����������Υơ��֥뤫����������
$query = "SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color, appearance, " .
  "category, author FROM user_icon WHERE icon_no > 0 ORDER BY icon_no";
$icon_list = FetchAssoc($query);

//ɽ�ν���
$count = 0;
foreach($icon_list as $array){
  if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
  $count++;

  extract($array);
  $location = $ICON_CONF->path . '/' . $icon_filename;
  $data = '';
  if(isset($appearance)) $data .= '<br>' . $appearance;
  if(isset($category))   $data .= '<br>' . $category;
  if(isset($author))     $data .= '<br>' . $author;
  echo <<< EOF
<td><img src="$location" width="$icon_width" height="$icon_height" style="border-color:$color;"></td>
<td class="name">No. $icon_no<br>$icon_name<br><font color="$color">��</font>$color{$data}</td>

EOF;
}

$DB_CONF->Disconnect(); //DB ��³���
?>
</tr></table>
</fieldset>
</body>
</html>
