<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('USER_ICON');

if($USER_ICON->disable_upload){
  OutputActionResult('ユーザアイコンアップロード', '現在アップロードは停止しています');
}
OutputHTMLHeader('ユーザアイコンアップロード', 'icon_upload');
?>
</head>
<body>
<a href="index.php">←戻る</a><br>
<img class="title" src="img/icon_upload_title.jpg"><br>
<table align="center">
<tr><td class="link"><a href="icon_view.php">→アイコン一覧</a></td><tr>
<tr><td class="caution">＊あらかじめ指定する大きさ (<?php echo $USER_ICON->IconSizeMax(); ?>
 ) にリサイズしてからアップロードしてください。</td></tr>
<tr><td>
<fieldset><legend>アイコン指定
<?php echo '(jpg, gif, png 画像を登録して下さい。' . $USER_ICON->IconFileSizeMax() . ')'; ?>
</legend>
<form method="POST" action="icon_upload_check.php" enctype="multipart/form-data">
<table>
<tr><td><label>ファイル選択</label>
<?php echo '<input type="hidden" name="max_file_size" value="' . $USER_ICON->size . '">'; ?>
<input type="file" name="file" size="80">
<input type="submit" value="登録">
</td></tr>

<tr><td><label>アイコンの名前</label>
<input type="text" name="name" maxlength="20" size="20">
<?php echo $USER_ICON->IconNameMaxLength(); ?>
</td></tr>

<tr><td><label>アイコンに合った色を選択してください</label></td></tr>
<tr><td>
<input id="fix_color" type="radio" name="color"><label for="fix_color">色を指定する</label>
<input type="text" name="color" size="10px" maxlength="7">(例：#6699CC)
</td></tr>

<tr><td>
<table class="color" align="center">
<tr>
<td bgcolor="#000000"><label for="#000000"><input type="radio" id="#000000" name="color" value="#000000"><font color="#FFFFFF">#000000</font></label></td>
<td bgcolor="#333333"><label for="#333333"><input type="radio" id="#333333" name="color" value="#333333"><font color="#FFFFFF">#333333</font></label></td>
<td bgcolor="#666666"><label for="#666666"><input type="radio" id="#666666" name="color" value="#666666"><font color="#FFFFFF">#666666</font></label></td>
<td bgcolor="#999999"><label for="#999999"><input type="radio" id="#999999" name="color" value="#999999"><font color="#FFFFFF">#999999</font></label></td>
<td bgcolor="#CCCCCC"><label for="#CCCCCC"><input type="radio" id="#CCCCCC" name="color" value="#CCCCCC">#CCCCCC</label></td>
<td bgcolor="#FFFFFF"><label for="#FFFFFF"><input type="radio" id="#FFFFFF" name="color" value="#FFFFFF">#FFFFFF</label></td>
</tr>
<tr>
<td bgcolor="#000033"><label for="#000033"><input type="radio" id="#000033" name="color" value="#000033"><font color="#FFFFFF">#000033</font></label></td>
<td bgcolor="#333300"><label for="#333300"><input type="radio" id="#333300" name="color" value="#333300"><font color="#FFFFFF">#333300</font></label></td>
<td bgcolor="#666600"><label for="#666600"><input type="radio" id="#666600" name="color" value="#666600"><font color="#FFFFFF">#666600</font></label></td>
<td bgcolor="#999900"><label for="#999900"><input type="radio" id="#999900" name="color" value="#999900"><font color="#FFFFFF">#999900</font></label></td>
<td bgcolor="#CCCC00"><label for="#CCCC00"><input type="radio" id="#CCCC00" name="color" value="#CCCC00">#CCCC00</label></td>
<td bgcolor="#FFFF00"><label for="#FFFF00"><input type="radio" id="#FFFF00" name="color" value="#FFFF00">#FFFF00</label></td>
</tr>
<tr>
<td bgcolor="#000066"><label for="#000066"><input type="radio" id="#000066" name="color" value="#000066"><font color="#FFFFFF">#000066</font></label></td>
<td bgcolor="#333366"><label for="#333366"><input type="radio" id="#333366" name="color" value="#333366"><font color="#FFFFFF">#333366</font></label></td>
<td bgcolor="#666633"><label for="#666633"><input type="radio" id="#666633" name="color" value="#666633"><font color="#FFFFFF">#666633</font></label></td>
<td bgcolor="#999933"><label for="#999933"><input type="radio" id="#999933" name="color" value="#999933"><font color="#FFFFFF">#999933</font></label></td>
<td bgcolor="#CCCC33"><label for="#CCCC33"><input type="radio" id="#CCCC33" name="color" value="#CCCC33">#CCCC33</label></td>
<td bgcolor="#FFFF33"><label for="#FFFF33"><input type="radio" id="#FFFF33" name="color" value="#FFFF33">#FFFF33</label></td>
</tr>
<tr>
<td bgcolor="#000099"><label for="#000099"><input type="radio" id="#000099" name="color" value="#000099"><font color="#FFFFFF">#000099</font></label></td>
<td bgcolor="#333399"><label for="#333399"><input type="radio" id="#333399" name="color" value="#333399"><font color="#FFFFFF">#333399</font></label></td>
<td bgcolor="#666699"><label for="#666699"><input type="radio" id="#666699" name="color" value="#666699"><font color="#FFFFFF">#666699</font></label></td>
<td bgcolor="#999966"><label for="#999966"><input type="radio" id="#999966" name="color" value="#999966"><font color="#FFFFFF">#999966</font></label></td>
<td bgcolor="#CCCC66"><label for="#CCCC66"><input type="radio" id="#CCCC66" name="color" value="#CCCC66">#CCCC66</label></td>
<td bgcolor="#FFFF66"><label for="#FFFF66"><input type="radio" id="#FFFF66" name="color" value="#FFFF66">#FFFF66</label></td>
</tr>
<tr>
<td bgcolor="#0000CC"><label for="#0000CC"><input type="radio" id="#0000CC" name="color" value="#0000CC"><font color="#FFFFFF">#0000CC</font></label></td>
<td bgcolor="#3333CC"><label for="#3333CC"><input type="radio" id="#3333CC" name="color" value="#3333CC"><font color="#FFFFFF">#3333CC</font></label></td>
<td bgcolor="#6666CC"><label for="#6666CC"><input type="radio" id="#6666CC" name="color" value="#6666CC"><font color="#FFFFFF">#6666CC</font></label></td>
<td bgcolor="#9999CC"><label for="#9999CC"><input type="radio" id="#9999CC" name="color" value="#9999CC"><font color="#FFFFFF">#9999CC</font></label></td>
<td bgcolor="#CCCC99"><label for="#CCCC99"><input type="radio" id="#CCCC99" name="color" value="#CCCC99">#CCCC99</label></td>
<td bgcolor="#FFFF99"><label for="#FFFF99"><input type="radio" id="#FFFF99" name="color" value="#FFFF99">#FFFF99</label></td>
</tr>
<tr>
<td bgcolor="#0000FF"><label for="#0000FF"><input type="radio" id="#0000FF" name="color" value="#0000FF"><font color="#FFFFFF">#0000FF</font></label></td>
<td bgcolor="#3333FF"><label for="#3333FF"><input type="radio" id="#3333FF" name="color" value="#3333FF"><font color="#FFFFFF">#3333FF</font></label></td>
<td bgcolor="#6666FF"><label for="#6666FF"><input type="radio" id="#6666FF" name="color" value="#6666FF"><font color="#FFFFFF">#6666FF</font></label></td>
<td bgcolor="#9999FF"><label for="#9999FF"><input type="radio" id="#9999FF" name="color" value="#9999FF"><font color="#FFFFFF">#9999FF</font></label></td>
<td bgcolor="#CCCCFF"><label for="#CCCCFF"><input type="radio" id="#CCCCFF" name="color" value="#CCCCFF">#CCCCFF</label></td>
<td bgcolor="#FFFFCC"><label for="#FFFFCC"><input type="radio" id="#FFFFCC" name="color" value="#FFFFCC">#FFFFCC</label></td>
</tr>
<tr>
<td bgcolor="#003300"><label for="#003300"><input type="radio" id="#003300" name="color" value="#003300"><font color="#FFFFFF">#003300</font></label></td>
<td bgcolor="#336633"><label for="#336633"><input type="radio" id="#336633" name="color" value="#336633"><font color="#FFFFFF">#336633</font></label></td>
<td bgcolor="#669966"><label for="#669966"><input type="radio" id="#669966" name="color" value="#669966"><font color="#FFFFFF">#669966</font></label></td>
<td bgcolor="#99CC99"><label for="#99CC99"><input type="radio" id="#99CC99" name="color" value="#99CC99">#99CC99</label></td>
<td bgcolor="#CCFFCC"><label for="#CCFFCC"><input type="radio" id="#CCFFCC" name="color" value="#CCFFCC">#CCFFCC</label></td>
<td bgcolor="#FF00FF"><label for="#FF00FF"><input type="radio" id="#FF00FF" name="color" value="#FF00FF"><font color="#FFFFFF">#FF00FF</font></label></td>
</tr>
<tr>
<td bgcolor="#006600"><label for="#006600"><input type="radio" id="#006600" name="color" value="#006600"><font color="#FFFFFF">#006600</font></label></td>
<td bgcolor="#339933"><label for="#339933"><input type="radio" id="#339933" name="color" value="#339933"><font color="#FFFFFF">#339933</font></label></td>
<td bgcolor="#66CC66"><label for="#66CC66"><input type="radio" id="#66CC66" name="color" value="#66CC66">#66CC66</label></td>
<td bgcolor="#99FF99"><label for="#99FF99"><input type="radio" id="#99FF99" name="color" value="#99FF99">#99FF99</label></td>
<td bgcolor="#CC00CC"><label for="#CC00CC"><input type="radio" id="#CC00CC" name="color" value="#CC00CC"><font color="#FFFFFF">#CC00CC</font></label></td>
<td bgcolor="#FF33FF"><label for="#FF33FF"><input type="radio" id="#FF33FF" name="color" value="#FF33FF"><font color="#FFFFFF">#FF33FF</font></label></td>
</tr>
<tr>
<td bgcolor="#009900"><label for="#009900"><input type="radio" id="#009900" name="color" value="#009900"><font color="#FFFFFF">#009900</font></label></td>
<td bgcolor="#33CC33"><label for="#33CC33"><input type="radio" id="#33CC33" name="color" value="#33CC33">#33CC33</label></td>
<td bgcolor="#66FF66"><label for="#66FF66"><input type="radio" id="#66FF66" name="color" value="#66FF66">#66FF66</label></td>
<td bgcolor="#990099"><label for="#990099"><input type="radio" id="#990099" name="color" value="#990099"><font color="#FFFFFF">#990099</font></label></td>
<td bgcolor="#CC33CC"><label for="#CC33CC"><input type="radio" id="#CC33CC" name="color" value="#CC33CC"><font color="#FFFFFF">#CC33CC</font></label></td>
<td bgcolor="#FF66FF"><label for="#FF66FF"><input type="radio" id="#FF66FF" name="color" value="#FF66FF"><font color="#FFFFFF">#FF66FF</font></label></td>
</tr>
<tr>
<td bgcolor="#00CC00"><label for="#00CC00"><input type="radio" id="#00CC00" name="color" value="#00CC00">#00CC00</label></td>
<td bgcolor="#33FF33"><label for="#33FF33"><input type="radio" id="#33FF33" name="color" value="#33FF33">#33FF33</label></td>
<td bgcolor="#660066"><label for="#660066"><input type="radio" id="#660066" name="color" value="#660066"><font color="#FFFFFF">#660066</font></label></td>
<td bgcolor="#993399"><label for="#993399"><input type="radio" id="#993399" name="color" value="#993399"><font color="#FFFFFF">#993399</font></label></td>
<td bgcolor="#CC66CC"><label for="#CC66CC"><input type="radio" id="#CC66CC" name="color" value="#CC66CC"><font color="#FFFFFF">#CC66CC</font></label></td>
<td bgcolor="#FF99FF"><label for="#FF99FF"><input type="radio" id="#FF99FF" name="color" value="#FF99FF"><font color="#FFFFFF">#FF99FF</font></label></td>
</tr>
<tr>
<td bgcolor="#00FF00"><label for="#00FF00"><input type="radio" id="#00FF00" name="color" value="#00FF00">#00FF00</label></td>
<td bgcolor="#330033"><label for="#330033"><input type="radio" id="#330033" name="color" value="#330033"><font color="#FFFFFF">#330033</font></label></td>
<td bgcolor="#663366"><label for="#663366"><input type="radio" id="#663366" name="color" value="#663366"><font color="#FFFFFF">#663366</font></label></td>
<td bgcolor="#996699"><label for="#996699"><input type="radio" id="#996699" name="color" value="#996699"><font color="#FFFFFF">#996699</font></label></td>
<td bgcolor="#CC99CC"><label for="#CC99CC"><input type="radio" id="#CC99CC" name="color" value="#CC99CC"><font color="#FFFFFF">#CC99CC</font></label></td>
<td bgcolor="#FFCCFF"><label for="#FFCCFF"><input type="radio" id="#FFCCFF" name="color" value="#FFCCFF">#FFCCFF</label></td>
</tr>
<tr>
<td bgcolor="#00FF33"><label for="#00FF33"><input type="radio" id="#00FF33" name="color" value="#00FF33">#00FF33</label></td>
<td bgcolor="#330066"><label for="#330066"><input type="radio" id="#330066" name="color" value="#330066"><font color="#FFFFFF">#330066</font></label></td>
<td bgcolor="#663399"><label for="#663399"><input type="radio" id="#663399" name="color" value="#663399"><font color="#FFFFFF">#663399</font></label></td>
<td bgcolor="#9966CC"><label for="#9966CC"><input type="radio" id="#9966CC" name="color" value="#9966CC"><font color="#FFFFFF">#9966CC</font></label></td>
<td bgcolor="#CC99FF"><label for="#CC99FF"><input type="radio" id="#CC99FF" name="color" value="#CC99FF"><font color="#FFFFFF">#CC99FF</font></label></td>
<td bgcolor="#FFCC00"><label for="#FFCC00"><input type="radio" id="#FFCC00" name="color" value="#FFCC00">#FFCC00</label></td>
</tr>
<tr>
<td bgcolor="#00FF66"><label for="#00FF66"><input type="radio" id="#00FF66" name="color" value="#00FF66">#00FF66</label></td>
<td bgcolor="#330099"><label for="#330099"><input type="radio" id="#330099" name="color" value="#330099"><font color="#FFFFFF">#330099</font></label></td>
<td bgcolor="#6633CC"><label for="#6633CC"><input type="radio" id="#6633CC" name="color" value="#6633CC"><font color="#FFFFFF">#6633CC</font></label></td>
<td bgcolor="#9966FF"><label for="#9966FF"><input type="radio" id="#9966FF" name="color" value="#9966FF"><font color="#FFFFFF">#9966FF</font></label></td>
<td bgcolor="#CC9900"><label for="#CC9900"><input type="radio" id="#CC9900" name="color" value="#CC9900"><font color="#FFFFFF">#CC9900</font></label></td>
<td bgcolor="#FFCC33"><label for="#FFCC33"><input type="radio" id="#FFCC33" name="color" value="#FFCC33">#FFCC33</label></td>
</tr>
<tr>
<td bgcolor="#00FF99"><label for="#00FF99"><input type="radio" id="#00FF99" name="color" value="#00FF99">#00FF99</label></td>
<td bgcolor="#3300CC"><label for="#3300CC"><input type="radio" id="#3300CC" name="color" value="#3300CC"><font color="#FFFFFF">#3300CC</font></label></td>
<td bgcolor="#6633FF"><label for="#6633FF"><input type="radio" id="#6633FF" name="color" value="#6633FF"><font color="#FFFFFF">#6633FF</font></label></td>
<td bgcolor="#996600"><label for="#996600"><input type="radio" id="#996600" name="color" value="#996600"><font color="#FFFFFF">#996600</font></label></td>
<td bgcolor="#CC9933"><label for="#CC9933"><input type="radio" id="#CC9933" name="color" value="#CC9933"><font color="#FFFFFF">#CC9933</font></label></td>
<td bgcolor="#FFCC66"><label for="#FFCC66"><input type="radio" id="#FFCC66" name="color" value="#FFCC66">#FFCC66</label></td>
</tr>
<tr>
<td bgcolor="#00FFCC"><label for="#00FFCC"><input type="radio" id="#00FFCC" name="color" value="#00FFCC">#00FFCC</label></td>
<td bgcolor="#3300FF"><label for="#3300FF"><input type="radio" id="#3300FF" name="color" value="#3300FF"><font color="#FFFFFF">#3300FF</font></label></td>
<td bgcolor="#663300"><label for="#663300"><input type="radio" id="#663300" name="color" value="#663300"><font color="#FFFFFF">#663300</font></label></td>
<td bgcolor="#996633"><label for="#996633"><input type="radio" id="#996633" name="color" value="#996633"><font color="#FFFFFF">#996633</font></label></td>
<td bgcolor="#CC9966"><label for="#CC9966"><input type="radio" id="#CC9966" name="color" value="#CC9966"><font color="#FFFFFF">#CC9966</font></label></td>
<td bgcolor="#FFCC99"><label for="#FFCC99"><input type="radio" id="#FFCC99" name="color" value="#FFCC99">#FFCC99</label></td>
</tr>
<tr>
<td bgcolor="#00FFFF"><label for="#00FFFF"><input type="radio" id="#00FFFF" name="color" value="#00FFFF">#00FFFF</label></td>
<td bgcolor="#330000"><label for="#330000"><input type="radio" id="#330000" name="color" value="#330000"><font color="#FFFFFF">#330000</font></label></td>
<td bgcolor="#663333"><label for="#663333"><input type="radio" id="#663333" name="color" value="#663333"><font color="#FFFFFF">#663333</font></label></td>
<td bgcolor="#996666"><label for="#996666"><input type="radio" id="#996666" name="color" value="#996666"><font color="#FFFFFF">#996666</font></label></td>
<td bgcolor="#CC9999"><label for="#CC9999"><input type="radio" id="#CC9999" name="color" value="#CC9999"><font color="#FFFFFF">#CC9999</font></label></td>
<td bgcolor="#FFCCCC"><label for="#FFCCCC"><input type="radio" id="#FFCCCC" name="color" value="#FFCCCC">#FFCCCC</label></td>
</tr>
<tr>
<td bgcolor="#00CCCC"><label for="#00CCCC"><input type="radio" id="#00CCCC" name="color" value="#00CCCC">#00CCCC</label></td>
<td bgcolor="#33FFFF"><label for="#33FFFF"><input type="radio" id="#33FFFF" name="color" value="#33FFFF">#33FFFF</label></td>
<td bgcolor="#660000"><label for="#660000"><input type="radio" id="#660000" name="color" value="#660000"><font color="#FFFFFF">#660000</font></label></td>
<td bgcolor="#993333"><label for="#993333"><input type="radio" id="#993333" name="color" value="#993333"><font color="#FFFFFF">#993333</font></label></td>
<td bgcolor="#CC6666"><label for="#CC6666"><input type="radio" id="#CC6666" name="color" value="#CC6666"><font color="#FFFFFF">#CC6666</font></label></td>
<td bgcolor="#FF9999"><label for="#FF9999"><input type="radio" id="#FF9999" name="color" value="#FF9999"><font color="#FFFFFF">#FF9999</font></label></td>
</tr>
<tr>
<td bgcolor="#009999"><label for="#009999"><input type="radio" id="#009999" name="color" value="#009999">#009999</label></td>
<td bgcolor="#33CCCC"><label for="#33CCCC"><input type="radio" id="#33CCCC" name="color" value="#33CCCC">#33CCCC</label></td>
<td bgcolor="#66FFFF"><label for="#66FFFF"><input type="radio" id="#66FFFF" name="color" value="#66FFFF">#66FFFF</label></td>
<td bgcolor="#990000"><label for="#990000"><input type="radio" id="#990000" name="color" value="#990000"><font color="#FFFFFF">#990000</font></label></td>
<td bgcolor="#CC3333"><label for="#CC3333"><input type="radio" id="#CC3333" name="color" value="#CC3333"><font color="#FFFFFF">#CC3333</font></label></td>
<td bgcolor="#FF6666"><label for="#FF6666"><input type="radio" id="#FF6666" name="color" value="#FF6666"><font color="#FFFFFF">#FF6666</font></label></td>
</tr>
<tr>
<td bgcolor="#006666"><label for="#006666"><input type="radio" id="#006666" name="color" value="#006666"><font color="#FFFFFF">#006666</font></label></td>
<td bgcolor="#339999"><label for="#339999"><input type="radio" id="#339999" name="color" value="#339999">#339999</label></td>
<td bgcolor="#66CCCC"><label for="#66CCCC"><input type="radio" id="#66CCCC" name="color" value="#66CCCC">#66CCCC</label></td>
<td bgcolor="#99FFFF"><label for="#99FFFF"><input type="radio" id="#99FFFF" name="color" value="#99FFFF">#99FFFF</label></td>
<td bgcolor="#CC0000"><label for="#CC0000"><input type="radio" id="#CC0000" name="color" value="#CC0000"><font color="#FFFFFF">#CC0000</font></label></td>
<td bgcolor="#FF3333"><label for="#FF3333"><input type="radio" id="#FF3333" name="color" value="#FF3333"><font color="#FFFFFF">#FF3333</font></label></td>
</tr>
<tr>
<td bgcolor="#003333"><label for="#003333"><input type="radio" id="#003333" name="color" value="#003333"><font color="#FFFFFF">#003333</font></label></td>
<td bgcolor="#336666"><label for="#336666"><input type="radio" id="#336666" name="color" value="#336666"><font color="#FFFFFF">#336666</font></label></td>
<td bgcolor="#669999"><label for="#669999"><input type="radio" id="#669999" name="color" value="#669999">#669999</label></td>
<td bgcolor="#99CCCC"><label for="#99CCCC"><input type="radio" id="#99CCCC" name="color" value="#99CCCC">#99CCCC</label></td>
<td bgcolor="#CCFFFF"><label for="#CCFFFF"><input type="radio" id="#CCFFFF" name="color" value="#CCFFFF">#CCFFFF</label></td>
<td bgcolor="#FF0000"><label for="#FF0000"><input type="radio" id="#FF0000" name="color" value="#FF0000"><font color="#FFFFFF">#FF0000</font></label></td>
</tr>
<tr>
<td bgcolor="#003366"><label for="#003366"><input type="radio" id="#003366" name="color" value="#003366"><font color="#FFFFFF">#003366</font></label></td>
<td bgcolor="#336699"><label for="#336699"><input type="radio" id="#336699" name="color" value="#336699"><font color="#FFFFFF">#336699</font></label></td>
<td bgcolor="#6699CC"><label for="#6699CC"><input type="radio" id="#6699CC" name="color" value="#6699CC">#6699CC</label></td>
<td bgcolor="#99CCFF"><label for="#99CCFF"><input type="radio" id="#99CCFF" name="color" value="#99CCFF">#99CCFF</label></td>
<td bgcolor="#CCFF00"><label for="#CCFF00"><input type="radio" id="#CCFF00" name="color" value="#CCFF00">#CCFF00</label></td>
<td bgcolor="#FF0033"><label for="#FF0033"><input type="radio" id="#FF0033" name="color" value="#FF0033"><font color="#FFFFFF">#FF0033</font></label></td>
</tr>
<tr>
<td bgcolor="#003399"><label for="#003399"><input type="radio" id="#003399" name="color" value="#003399"><font color="#FFFFFF">#003399</font></label></td>
<td bgcolor="#3366CC"><label for="#3366CC"><input type="radio" id="#3366CC" name="color" value="#3366CC"><font color="#FFFFFF">#3366CC</font></label></td>
<td bgcolor="#6699FF"><label for="#6699FF"><input type="radio" id="#6699FF" name="color" value="#6699FF">#6699FF</label></td>
<td bgcolor="#99CC00"><label for="#99CC00"><input type="radio" id="#99CC00" name="color" value="#99CC00">#99CC00</label></td>
<td bgcolor="#CCFF33"><label for="#CCFF33"><input type="radio" id="#CCFF33" name="color" value="#CCFF33">#CCFF33</label></td>
<td bgcolor="#FF0066"><label for="#FF0066"><input type="radio" id="#FF0066" name="color" value="#FF0066"><font color="#FFFFFF">#FF0066</font></label></td>
</tr>
<tr>
<td bgcolor="#0033CC"><label for="#0033CC"><input type="radio" id="#0033CC" name="color" value="#0033CC"><font color="#FFFFFF">#0033CC</font></label></td>
<td bgcolor="#3366FF"><label for="#3366FF"><input type="radio" id="#3366FF" name="color" value="#3366FF"><font color="#FFFFFF">#3366FF</font></label></td>
<td bgcolor="#669900"><label for="#669900"><input type="radio" id="#669900" name="color" value="#669900">#669900</label></td>
<td bgcolor="#99CC33"><label for="#99CC33"><input type="radio" id="#99CC33" name="color" value="#99CC33">#99CC33</label></td>
<td bgcolor="#CCFF66"><label for="#CCFF66"><input type="radio" id="#CCFF66" name="color" value="#CCFF66">#CCFF66</label></td>
<td bgcolor="#FF0099"><label for="#FF0099"><input type="radio" id="#FF0099" name="color" value="#FF0099"><font color="#FFFFFF">#FF0099</font></label></td>
</tr>
<tr>
<td bgcolor="#0033FF"><label for="#0033FF"><input type="radio" id="#0033FF" name="color" value="#0033FF"><font color="#FFFFFF">#0033FF</font></label></td>
<td bgcolor="#336600"><label for="#336600"><input type="radio" id="#336600" name="color" value="#336600"><font color="#FFFFFF">#336600</font></label></td>
<td bgcolor="#669933"><label for="#669933"><input type="radio" id="#669933" name="color" value="#669933">#669933</label></td>
<td bgcolor="#99CC66"><label for="#99CC66"><input type="radio" id="#99CC66" name="color" value="#99CC66">#99CC66</label></td>
<td bgcolor="#CCFF99"><label for="#CCFF99"><input type="radio" id="#CCFF99" name="color" value="#CCFF99">#CCFF99</label></td>
<td bgcolor="#FF00CC"><label for="#FF00CC"><input type="radio" id="#FF00CC" name="color" value="#FF00CC"><font color="#FFFFFF">#FF00CC</font></label></td>
</tr>
<tr>
<td bgcolor="#0066FF"><label for="#0066FF"><input type="radio" id="#0066FF" name="color" value="#0066FF"><font color="#FFFFFF">#0066FF</font></label></td>
<td bgcolor="#339900"><label for="#339900"><input type="radio" id="#339900" name="color" value="#339900"><font color="#FFFFFF">#339900</font></label></td>
<td bgcolor="#66CC33"><label for="#66CC33"><input type="radio" id="#66CC33" name="color" value="#66CC33">#66CC33</label></td>
<td bgcolor="#99FF66"><label for="#99FF66"><input type="radio" id="#99FF66" name="color" value="#99FF66">#99FF66</label></td>
<td bgcolor="#CC0099"><label for="#CC0099"><input type="radio" id="#CC0099" name="color" value="#CC0099"><font color="#FFFFFF">#CC0099</font></label></td>
<td bgcolor="#FF33CC"><label for="#FF33CC"><input type="radio" id="#FF33CC" name="color" value="#FF33CC"><font color="#FFFFFF">#FF33CC</font></label></td>
</tr>
<tr>
<td bgcolor="#0099FF"><label for="#0099FF"><input type="radio" id="#0099FF" name="color" value="#0099FF"><font color="#FFFFFF">#0099FF</font></label></td>
<td bgcolor="#33CC00"><label for="#33CC00"><input type="radio" id="#33CC00" name="color" value="#33CC00">#33CC00</label></td>
<td bgcolor="#66FF33"><label for="#66FF33"><input type="radio" id="#66FF33" name="color" value="#66FF33">#66FF33</label></td>
<td bgcolor="#990066"><label for="#990066"><input type="radio" id="#990066" name="color" value="#990066"><font color="#FFFFFF">#990066</font></label></td>
<td bgcolor="#CC3399"><label for="#CC3399"><input type="radio" id="#CC3399" name="color" value="#CC3399"><font color="#FFFFFF">#CC3399</font></label></td>
<td bgcolor="#FF66CC"><label for="#FF66CC"><input type="radio" id="#FF66CC" name="color" value="#FF66CC"><font color="#FFFFFF">#FF66CC</font></label></td>
</tr>
<tr>
<td bgcolor="#00CCFF"><label for="#00CCFF"><input type="radio" id="#00CCFF" name="color" value="#00CCFF">#00CCFF</label></td>
<td bgcolor="#33FF00"><label for="#33FF00"><input type="radio" id="#33FF00" name="color" value="#33FF00">#33FF00</label></td>
<td bgcolor="#660033"><label for="#660033"><input type="radio" id="#660033" name="color" value="#660033"><font color="#FFFFFF">#660033</font></label></td>
<td bgcolor="#993366"><label for="#993366"><input type="radio" id="#993366" name="color" value="#993366"><font color="#FFFFFF">#993366</font></label></td>
<td bgcolor="#CC6699"><label for="#CC6699"><input type="radio" id="#CC6699" name="color" value="#CC6699"><font color="#FFFFFF">#CC6699</font></label></td>
<td bgcolor="#FF99CC"><label for="#FF99CC"><input type="radio" id="#FF99CC" name="color" value="#FF99CC"><font color="#FFFFFF">#FF99CC</font></label></td>
</tr>
<tr>
<td bgcolor="#00CC33"><label for="#00CC33"><input type="radio" id="#00CC33" name="color" value="#00CC33">#00CC33</label></td>
<td bgcolor="#33FF66"><label for="#33FF66"><input type="radio" id="#33FF66" name="color" value="#33FF66">#33FF66</label></td>
<td bgcolor="#660099"><label for="#660099"><input type="radio" id="#660099" name="color" value="#660099"><font color="#FFFFFF">#660099</font></label></td>
<td bgcolor="#9933CC"><label for="#9933CC"><input type="radio" id="#9933CC" name="color" value="#9933CC"><font color="#FFFFFF">#9933CC</font></label></td>
<td bgcolor="#CC66FF"><label for="#CC66FF"><input type="radio" id="#CC66FF" name="color" value="#CC66FF"><font color="#FFFFFF">#CC66FF</font></label></td>
<td bgcolor="#FF9900"><label for="#FF9900"><input type="radio" id="#FF9900" name="color" value="#FF9900"><font color="#FFFFFF">#FF9900</font></label></td>
</tr>
<tr>
<td bgcolor="#00CC66"><label for="#00CC66"><input type="radio" id="#00CC66" name="color" value="#00CC66">#00CC66</label></td>
<td bgcolor="#33FF99"><label for="#33FF99"><input type="radio" id="#33FF99" name="color" value="#33FF99">#33FF99</label></td>
<td bgcolor="#6600CC"><label for="#6600CC"><input type="radio" id="#6600CC" name="color" value="#6600CC"><font color="#FFFFFF">#6600CC</font></label></td>
<td bgcolor="#9933FF"><label for="#9933FF"><input type="radio" id="#9933FF" name="color" value="#9933FF"><font color="#FFFFFF">#9933FF</font></label></td>
<td bgcolor="#CC6600"><label for="#CC6600"><input type="radio" id="#CC6600" name="color" value="#CC6600"><font color="#FFFFFF">#CC6600</font></label></td>
<td bgcolor="#FF9933"><label for="#FF9933"><input type="radio" id="#FF9933" name="color" value="#FF9933"><font color="#FFFFFF">#FF9933</font></label></td>
</tr>
<tr>
<td bgcolor="#00CC99"><label for="#00CC99"><input type="radio" id="#00CC99" name="color" value="#00CC99">#00CC99</label></td>
<td bgcolor="#33FFCC"><label for="#33FFCC"><input type="radio" id="#33FFCC" name="color" value="#33FFCC">#33FFCC</label></td>
<td bgcolor="#6600FF"><label for="#6600FF"><input type="radio" id="#6600FF" name="color" value="#6600FF"><font color="#FFFFFF">#6600FF</font></label></td>
<td bgcolor="#993300"><label for="#993300"><input type="radio" id="#993300" name="color" value="#993300"><font color="#FFFFFF">#993300</font></label></td>
<td bgcolor="#CC6633"><label for="#CC6633"><input type="radio" id="#CC6633" name="color" value="#CC6633"><font color="#FFFFFF">#CC6633</font></label></td>
<td bgcolor="#FF9966"><label for="#FF9966"><input type="radio" id="#FF9966" name="color" value="#FF9966"><font color="#FFFFFF">#FF9966</font></label></td>
</tr>
<tr>
<td bgcolor="#009933"><label for="#009933"><input type="radio" id="#009933" name="color" value="#009933"><font color="#FFFFFF">#009933</font></label></td>
<td bgcolor="#33CC66"><label for="#33CC66"><input type="radio" id="#33CC66" name="color" value="#33CC66">#33CC66</label></td>
<td bgcolor="#66FF99"><label for="#66FF99"><input type="radio" id="#66FF99" name="color" value="#66FF99">#66FF99</label></td>
<td bgcolor="#9900CC"><label for="#9900CC"><input type="radio" id="#9900CC" name="color" value="#9900CC"><font color="#FFFFFF">#9900CC</font></label></td>
<td bgcolor="#CC33FF"><label for="#CC33FF"><input type="radio" id="#CC33FF" name="color" value="#CC33FF"><font color="#FFFFFF">#CC33FF</font></label></td>
<td bgcolor="#FF6600"><label for="#FF6600"><input type="radio" id="#FF6600" name="color" value="#FF6600"><font color="#FFFFFF">#FF6600</font></label></td>
</tr>
<tr>
<td bgcolor="#006633"><label for="#006633"><input type="radio" id="#006633" name="color" value="#006633"><font color="#FFFFFF">#006633</font></label></td>
<td bgcolor="#339966"><label for="#339966"><input type="radio" id="#339966" name="color" value="#339966"><font color="#FFFFFF">#339966</font></label></td>
<td bgcolor="#66CC99"><label for="#66CC99"><input type="radio" id="#66CC99" name="color" value="#66CC99">#66CC99</label></td>
<td bgcolor="#99FFCC"><label for="#99FFCC"><input type="radio" id="#99FFCC" name="color" value="#99FFCC">#99FFCC</label></td>
<td bgcolor="#CC00FF"><label for="#CC00FF"><input type="radio" id="#CC00FF" name="color" value="#CC00FF"><font color="#FFFFFF">#CC00FF</font></label></td>
<td bgcolor="#FF3300"><label for="#FF3300"><input type="radio" id="#FF3300" name="color" value="#FF3300"><font color="#FFFFFF">#FF3300</font></label></td>
</tr>
<tr>
<td bgcolor="#009966"><label for="#009966"><input type="radio" id="#009966" name="color" value="#009966"><font color="#FFFFFF">#009966</font></label></td>
<td bgcolor="#33CC99"><label for="#33CC99"><input type="radio" id="#33CC99" name="color" value="#33CC99">#33CC99</label></td>
<td bgcolor="#66FFCC"><label for="#66FFCC"><input type="radio" id="#66FFCC" name="color" value="#66FFCC">#66FFCC</label></td>
<td bgcolor="#9900FF"><label for="#9900FF"><input type="radio" id="#9900FF" name="color" value="#9900FF"><font color="#FFFFFF">#9900FF</font></label></td>
<td bgcolor="#CC3300"><label for="#CC3300"><input type="radio" id="#CC3300" name="color" value="#CC3300"><font color="#FFFFFF">#CC3300</font></label></td>
<td bgcolor="#FF6633"><label for="#FF6633"><input type="radio" id="#FF6633" name="color" value="#FF6633"><font color="#FFFFFF">#FF6633</font></label></td>
</tr>
<tr>
<td bgcolor="#0099CC"><label for="#0099CC"><input type="radio" id="#0099CC" name="color" value="#0099CC"><font color="#FFFFFF">#0099CC</font></label></td>
<td bgcolor="#33CCFF"><label for="#33CCFF"><input type="radio" id="#33CCFF" name="color" value="#33CCFF">#33CCFF</label></td>
<td bgcolor="#66FF00"><label for="#66FF00"><input type="radio" id="#66FF00" name="color" value="#66FF00">#66FF00</label></td>
<td bgcolor="#990033"><label for="#990033"><input type="radio" id="#990033" name="color" value="#990033"><font color="#FFFFFF">#990033</font></label></td>
<td bgcolor="#CC3366"><label for="#CC3366"><input type="radio" id="#CC3366" name="color" value="#CC3366"><font color="#FFFFFF">#CC3366</font></label></td>
<td bgcolor="#FF6699"><label for="#FF6699"><input type="radio" id="#FF6699" name="color" value="#FF6699"><font color="#FFFFFF">#FF6699</font></label></td>
</tr>
<tr>
<td bgcolor="#0066CC"><label for="#0066CC"><input type="radio" id="#0066CC" name="color" value="#0066CC"><font color="#FFFFFF">#0066CC</font></label></td>
<td bgcolor="#3399FF"><label for="#3399FF"><input type="radio" id="#3399FF" name="color" value="#3399FF"><font color="#FFFFFF">#3399FF</font></label></td>
<td bgcolor="#66CC00"><label for="#66CC00"><input type="radio" id="#66CC00" name="color" value="#66CC00">#66CC00</label></td>
<td bgcolor="#99FF33"><label for="#99FF33"><input type="radio" id="#99FF33" name="color" value="#99FF33">#99FF33</label></td>
<td bgcolor="#CC0066"><label for="#CC0066"><input type="radio" id="#CC0066" name="color" value="#CC0066"><font color="#FFFFFF">#CC0066</font></label></td>
<td bgcolor="#FF3399"><label for="#FF3399"><input type="radio" id="#FF3399" name="color" value="#FF3399"><font color="#FFFFFF">#FF3399</font></label></td>
</tr>
<tr>
<td bgcolor="#006699"><label for="#006699"><input type="radio" id="#006699" name="color" value="#006699"><font color="#FFFFFF">#006699</font></label></td>
<td bgcolor="#3399CC"><label for="#3399CC"><input type="radio" id="#3399CC" name="color" value="#3399CC"><font color="#FFFFFF">#3399CC</font></label></td>
<td bgcolor="#66CCFF"><label for="#66CCFF"><input type="radio" id="#66CCFF" name="color" value="#66CCFF">#66CCFF</label></td>
<td bgcolor="#99FF00"><label for="#99FF00"><input type="radio" id="#99FF00" name="color" value="#99FF00">#99FF00</label></td>
<td bgcolor="#CC0033"><label for="#CC0033"><input type="radio" id="#CC0033" name="color" value="#CC0033"><font color="#FFFFFF">#CC0033</font></label></td>
<td bgcolor="#FF3366"><label for="#FF3366"><input type="radio" id="#FF3366" name="color" value="#FF3366"><font color="#FFFFFF">#FF3366</font></label></td>
</tr>
</table>

</td></tr></form></fieldset>
</td></tr></table>
</body></html>
