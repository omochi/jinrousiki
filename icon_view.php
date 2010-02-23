<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF');
OutputHTMLHeader('ユーザアイコン一覧', 'icon_view')
?>
</head>
<body>
<a href="./">←戻る</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">→アイコン登録</a></div>

<fieldset><legend>ユーザアイコン一覧</legend>
<table>
<?php
$INIT_CONF->LoadRequest('RequestIconView'); //引数を取得
$DB_CONF->Connect(true); //DB 接続
$icon_count = FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_no > 0");

//ページリンクの出力
echo '<tr><td colspan="5">'."\n";
OutputPageLink('icon_view', $ICON_CONF, $icon_count, array());
echo "</td></tr>\n";
echo '<tr><td colspan="5">[S] 出典 / [C] カテゴリ / [A] アイコンの作者</td></tr>'."\n";

//ユーザアイコンのテーブルから一覧を取得
$query = "SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color, appearance, " .
  "category, author FROM user_icon WHERE icon_no > 0 ORDER BY icon_no";
if($RQ_ARGS->page != 'all'){
  $query .= sprintf(' LIMIT %d, %d', $ICON_CONF->view * ($RQ_ARGS->page - 1), $ICON_CONF->view);
}
$icon_list = FetchAssoc($query);

//表の出力
$count = 0;
foreach($icon_list as $array){
  if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
  $count++;

  extract($array);
  $location = $ICON_CONF->path . '/' . $icon_filename;
  $data = '';
  if(isset($appearance)) $data .= '<br>[S]' . $appearance;
  if(isset($category))   $data .= '<br>[C]' . $category;
  if(isset($author))     $data .= '<br>[A]' . $author;
  echo <<< EOF
<td><img src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border-color:{$color};"></td>
<td class="name">No. {$icon_no}<br>{$icon_name}<br><font color="{$color}">◆</font>{$color}{$data}</td>

EOF;
}

$DB_CONF->Disconnect(); //DB 接続解除
?>
</tr></table>
</fieldset>
</body>
</html>
