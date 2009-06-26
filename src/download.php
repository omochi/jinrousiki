<table id="download">
<caption>定置ファイル</caption>
<?php
$caption = <<< EOF
<tr class="caption">
  <td>ファイル</td>
  <td>拡張子</td>
  <td>サイズ</td>
  <td>説明</td>
  <td>作成者</td>
  <td>日時</td>
</tr>

EOF;

echo <<< EOF
$caption
<tr>
  <td class="link"><a href="fix/jinro_php_1.2.2.zip">Ver. 1.2.2</a></td>
  <td class="type">zip</td>
  <td class="size">1.21 Mbyte</td>
  <td class="explain">ソースコード Ver. 1.2.2</td>
  <td class="name">埋めチル</td>
  <td class="date">2009/06/03</td>
</tr>
<tr>
  <td class="link"><a href="fix/jinro_php_1.2.1.zip">Ver. 1.2.1</a>
  <td class="type">zip</td>
  <td class="size">1.19 Mbyte</td>
  <td class="explain">ソースコード Ver. 1.2.1</td>
  <td class="name">お肉</td>
  <td class="date">2009/04/15</td>
</tr>
</table>

EOF;

$array = array();
if($handle = opendir('html')){
  while (false !== ($file = readdir($handle))){
    if($file != '.' && $file != '..') array_push($array, $file);
  }
  closedir($handle);
}
if(count($array) < 1) return;
rsort($array);

echo '<table id="download">'."\n" . '<caption>アップロードされたファイル</caption>' . $caption;
foreach($array as $key => $file){
  echo '<tr>'."\n";
  if($html = file_get_contents('html/' . $file)){
    echo $html;
  }
  else{
    echo '<td colspan="6">読み込み失敗: ' . $file . '</td>'."\n";
  }
  echo '<tr>'."\n";
}
echo '</table>'."\n";
?>
