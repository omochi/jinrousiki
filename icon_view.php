<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF');
OutputHTMLHeader('ユーザアイコン一覧', 'icon_view')
?>
</head>
<body>
<a href="index.php">←戻る</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">→アイコン登録</a></div>

<fieldset><legend>ユーザアイコン一覧</legend>
<table><tr>
<?php
$DB_CONF->Connect(true); //DB 接続

//ユーザアイコンのテーブルから一覧を取得
$query = "SELECT icon_name, icon_filename, icon_width, icon_height, color " .
  "FROM user_icon WHERE icon_no > 0 ORDER BY icon_no";
$icon_list = FetchAssoc($query);

//表の出力
$count = 0;
foreach($icon_list as $array){
  extract($array);
  $location = $ICON_CONF->path . '/' . $icon_filename;

  echo <<< EOF
<td><img src="$location" width="$icon_width" height="$icon_height" style="border-color:$color;"></td>
<td class="name">$icon_name<br><font color="$color">◆</font>$color</td>

EOF;
  if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
}

$DB_CONF->Disconnect(); //DB 接続解除
?>
</tr></table>
</fieldset>
</body>
</html>
