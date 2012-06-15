<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT  . '/include/init.php');
if (Security::CheckValue($_FILES)) die;

Loader::LoadFile('src_upload_config');
Loader::LoadRequest('RequestSrcUpload'); //引数をセット

if (SrcUploadConfig::DISABLE){
  HTML::OutputResult('ファイルアップロード', '現在アップロードは停止しています');
}

//引数のエラーチェック
foreach (RQ::$get as $key => $value) {
  $label = SrcUploadConfig::$form_list[$key]['label'];
  $size  = SrcUploadConfig::$form_list[$key]['size'];

  //未入力チェック
  if ($value == '') OutputUploadResult('<span>' . $label . '</span> が未入力です。');

  //文字列長チェック
  if (strlen($value) > $size){
    OutputUploadResult('<span>' . $label . '</span> は ' .
		       '<span>' . $size . '</span> 文字以下にしてください。');
  }
}

//パスワードのチェック
if (RQ::$get->password != SrcUploadConfig::PASSWORD) OutputUploadResult('パスワード認証エラー。');

//ファイルの種類のチェック
//PrintData($_FILES['file']);
$file_name = strtolower(trim($_FILES['file']['name']));
$file_type = $_FILES['file']['type'];
if (! (preg_match('/application\/(octet-stream|zip|lzh|lha|x-zip-compressed)/i', $file_type) &&
      preg_match('/^.*\.(zip|lzh)$/', $file_name))){
  PrintData($_FILES['file']);
  OutputUploadResult('<span>' . $file_name . '</span> : <span>' . $file_type . '</span><br>'."\n".
		     'zip/lzh 以外のファイルはアップロードできません。');
}

//ファイルサイズのチェック
$file_size = $_FILES['file']['size'];
if ($file_size == 0 || $file_size > SrcUploadConfig::MAX_SIZE){
  OutputUploadResult('ファイルサイズは <span>' . SrcUploadConfig::MAX_SIZE . 'byte</span> まで。');
}

//ファイル番号の取得
$number = (int)file_get_contents('file/number.txt');
if (! ($io = fopen('file/number.txt', 'wb+'))){ //ファイルオープン
  OutputUploadResult('ファイルの IO エラーです。<br>' .
		     '時間をおいてからアップロードしなおしてください。');
}
stream_set_write_buffer($io, 0); //バッファを 0 に指定 (排他制御の保証)

if (! flock($io, LOCK_EX)){ //ファイルのロック
  fclose($io);
  OutputUploadResult('ファイルのロックエラーです。<br>' .
		     '時間をおいてからアップロードしなおしてください。');
}
rewind($io); //ファイルポインタを先頭に移動
fwrite($io, $number + 1); //インクリメントして書き込み

flock($io, LOCK_UN); //ロック解除
fclose($io); //ファイルのクローズ

//HTMLソースを出力
$number = sprintf("%04d", $number); //桁揃え
$ext    = substr($file_name, -3); //拡張子
$time   = Time::GetDate('Y/m/d (D) H:i:s', Time::Get()); //日時
if ($file_size > 1024 * 1024) // Mbyte
  $file_size = sprintf('%.2f', $file_size / (1024 * 1024)) . ' Mbyte';
elseif ($file_size > 1024) // Kbyte
  $file_size = sprintf('%.2f', $file_size / 1024) . ' Kbyte';
else
  $file_size = sprintf('%.2f', $file_size) . ' byte';

$html = <<<EOF
<td class="link"><a href="file/{$number}.{$ext}">{RQ::$get->name}</a></td>
<td class="type">$ext</td>
<td class="size">$file_size</td>
<td class="explain">{RQ::$get->caption}</td>
<td class="name">{RQ::$get->user}</td>
<td class="date">$time</td>

EOF;

if (! ($io = fopen('html/' . $number . '.html', 'wb+'))){ //ファイルオープン
  OutputUploadResult('ファイルの IO エラーです。<br>' .
		     '時間をおいてからアップロードしなおしてください。');
}
stream_set_write_buffer($io, 0); //バッファを 0 に指定 (排他制御の保証)

if (! flock($io, LOCK_EX)){ //ファイルのロック
  fclose($io);
  OutputUploadResult('ファイルのロックエラーです。<br>' .
		     '時間をおいてからアップロードしなおしてください。');
}
rewind($io); //ファイルポインタを先頭に移動
fwrite($io, $html); //書き込み

flock($io, LOCK_UN); //ロック解除
fclose($io); //ファイルのクローズ

//ファイルのコピー
if (move_uploaded_file($_FILES['file']['tmp_name'], 'file/' . $number . '.' . $ext)){
  OutputUploadResult('ファイルのアップロードに成功しました。');
}
else {
  OutputUploadResult('ファイルのコピー失敗。<br>' .
		     '時間をおいてからアップロードしなおしてください。');
}

// 関数 //
//結果出力
function OutputUploadResult($body){
  HTML::OutputHeader('ファイルアップロード処理', 'src', true);
  echo $body . '<br><br>' . "\n" . '<a href="./">←戻る</a>'."\n";
  HTML::OutputFooter(true);
}
