<?php
require_once(dirname(__FILE__) . '/include/functions.php');
OutputHTMLHeader('�桼�������������', 'icon_view')
?>
</head>
<body>
<a href="index.php">�����</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">������������Ͽ</a></div>

<fieldset><legend>�桼�������������</legend>
<table><tr>
<?php
$dbHandle = ConnectDatabase(true); //DB ��³

//�桼����������Υơ��֥뤫����������
$sql = mysql_query("SELECT icon_name, icon_filename, icon_width, icon_height, color
 			FROM user_icon WHERE icon_no > 0 ORDER BY icon_no");
//ɽ�ν���
$count = 0;
while(($array = mysql_fetch_assoc($sql)) !== false){
  extract($array);
  $location = $ICON_CONF->path . '/' . $icon_filename;

  echo <<< EOF
<td><img src="$location" width="$icon_width" height="$icon_height" style="border-color:$color;"></td>
<td class="name">$icon_name<br><font color="$color">��</font>$color</td>

EOF;
  if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
}

DisconnectDatabase($dbHandle);
?>
</tr></table>
</fieldset>
</body>
</html>
