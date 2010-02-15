<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SESSION', 'USER_ICON');

if($USER_ICON->disable_upload){
  OutputActionResult('ユーザアイコンアップロード', '現在アップロードは停止しています');
}

switch($_POST['command']){
case 'upload':
  //リファラチェック
  if(CheckReferer('icon_upload.php')){
    OutputActionResult('ユーザアイコンアップロード', '無効なアクセスです。');
  }
  EncodePostData(); //ポストされた文字列を全てエンコードする
  $INIT_CONF->LoadClass('ICON_CONF');
  $INIT_CONF->LoadRequest('RequestIconUpload');

  CheckIconUpload();
  break;

case 'success': //セッションID情報をDBから削除
  //リファラチェック
  if(CheckReferer('icon_upload.php')){
    OutputActionResult('ユーザアイコンアップロード', '無効なアクセスです。');
  }
  $icon_no = (int)$_POST['icon_no'];
  $DB_CONF->Connect(); //DB 接続

  //セッションIDをクリア
  mysql_query("UPDATE user_icon SET session_id = NULL WHERE icon_no = $icon_no");
  mysql_query('COMMIT');

  OutputActionResult('アイコン登録完了',
		     '登録完了：アイコン一覧のページに飛びます。<br>'."\n" .
		     '切り替わらないなら <a href="icon_view.php">ここ</a> 。',
		     'icon_view.php');
  break;

case 'cancel':
  //リファラチェック
  if(CheckReferer('icon_upload.php')){
    OutputActionResult('ユーザアイコンアップロード', '無効なアクセスです。');
  }
  $INIT_CONF->LoadClass('ICON_CONF');
  DeleteUploadIcon();
  break;

default:
  OutputIconUploadPage();
  break;
}

//-- 関数 --//
//投稿データチェック
function CheckIconUpload(){
  global $DB_CONF, $ICON_CONF, $USER_ICON, $RQ_ARGS, $SESSION;

  // エラーページ用タイトル
  $title = 'アイコン登録エラー';

  //アップロードされたファイルのエラーチェック
  if($_FILES['upfile']['error'][$i] != 0){
    $sentence = "ファイルのアップロードエラーが発生しました。<br>\n再度実行してください。";
    OutputActionResult($title, $sentence, '', true);
  }

  extract($RQ_ARGS->ToArray()); //引数を展開

  //アイコン名が空白かチェック
  if($name == '') OutputActionResult($title, 'アイコン名を入力してください');

  //アイコン名の文字列長のチェック
  if(strlen($name) > $USER_ICON->name){
    OutputActionResult($title, $USER_ICON->IconNameMaxLength());
  }

  //ファイルサイズのチェック
  if($size == 0) OutputActionResult($title, 'ファイルが空です');
  if($size > $USER_ICON->size){
    OutputActionResult($title, 'ファイルサイズは ' . $USER_ICON->IconFileSizeMax());
  }

  //ファイルの種類のチェック
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
    OutputActionResult($title, $type . ' : jpg、gif、png 以外のファイルは登録できません');
    break;
  }

  //色指定のチェック
  if(strlen($color) != 7 && ! preg_match('/^#[0123456789abcdefABCDEF]{6}/', $color)){
    $sentence = '色指定が正しくありません。<br>'."\n" .
      '指定は (例：#6699CC) のように RGB 16進数指定で行ってください。<br>'."\n" .
      '送信された色指定 → <span class="color">' . $color . '</span>';
    OutputActionResult($title, $sentence);
  }

  //アイコンの高さと幅をチェック
  list($width, $height) = getimagesize($tmp_name);
  if($width > $USER_ICON->width || $height > $USER_ICON->height){
    $sentence = 'アイコンは ' . $USER_ICON->IconSizeMax() . ' しか登録できません。<br>'."\n" .
      '送信されたファイル → <span class="color">幅 ' . $width . '、高さ ' . $height . '</span>';
    OutputActionResult($title, $sentence);
  }

  $DB_CONF->Connect(); //DB 接続

  //アイコンの名前が既に登録されていないかチェック
  if(FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_name = '$name'") > 0){
    OutputActionResult($title, 'アイコン名 "' . $name . '" は既に登録されています');
  }

  if(! mysql_query('LOCK TABLES user_icon WRITE')){ //user_icon テーブルをロック
    $sentence = "サーバが混雑しています。<br>\n時間を置いてから再登録をお願いします。";
    OutputActionResult($title, $sentence);
  }

  //アイコン登録数が最大値を超えてないかチェック
  //現在登録されているアイコンナンバーを降順に取得
  $icon_no = FetchResult('SELECT icon_no FROM user_icon ORDER BY icon_no DESC') + 1; //一番大きなNo + 1
  if($icon_no >= $USER_ICON->number) OutputActionResult($title, 'これ以上登録できません', '', true);

  //ファイル名の桁をそろえる
  $file_name = sprintf("%03s.%s", $icon_no, $ext);

  //ファイルをテンポラリからコピー
  if(! move_uploaded_file($tmp_name, $ICON_CONF->path . '/' . $file_name)){
    $sentence = "ファイルのコピーに失敗しました。<br>\n再度実行してください。";
    OutputActionResult($title, $sentence, '', true);
  }

  //データベースに登録
  $session_id = $SESSION->Set(true); //セッション ID を取得
  $items = 'icon_no, icon_name, icon_filename, icon_width, icon_height, color, session_id';
  $values = "$icon_no, '$name', '$file_name', $width, $height, '$color', '$session_id'";
  InsertDatabase('user_icon', $items, $values);
  mysql_query('COMMIT'); //一応コミット
  $DB_CONF->Disconnect(true); //DB 接続解除

  //確認ページを出力
  OutputHTMLHeader('ユーザアイコンアップロード処理[確認]', 'icon_upload_check');
  echo <<<EOF
</head>
<body>
<p>ファイルをアップロードしました。<br>今だけやりなおしできます</p>
<img src="{$ICON_CONF->path}/{$file_name}" width="{$width}" height="{$height}"><br>
<table>
<tr><td>No. {$icon_no}<font color="{$color}">◆</font>{$color}</td></tr>
<tr><td>よろしいですか？</td></tr>
<tr><td><form method="POST" action="icon_upload.php">
  <input type="hidden" name="command" value="cancel">
  <input type="hidden" name="icon_no" value="$icon_no">
  <input type="submit" value="やりなおし">
</form></td>
<td><form method="POST" action="icon_upload.php">
  <input type="hidden" name="command" value="success">
  <input type="hidden" name="icon_no" value="{$icon_no}">
  <input type="submit" value="登録完了">
</form></td></tr></table>
</body></html>

EOF;
}

function DeleteUploadIcon(){
  global $DB_CONF, $ICON_CONF, $SESSION;

  //DBからアイコンのファイル名と登録時のセッションIDを取得
  $icon_no = (int)$_POST['icon_no'];
  $query = "SELECT icon_filename AS file, session_id FROM user_icon WHERE icon_no = $icon_no";

  $DB_CONF->Connect(); //DB 接続
  extract(FetchNameArray($query));

  //セッション ID 確認
  if($session_id != $SESSION->Get()){
    OutputActionResult('アイコン削除失敗', '削除失敗：アップロードセッションが一致しません');
  }
  unlink($ICON_CONF->path . '/' . $file);
  mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
  mysql_query("OPTIMIZE TABLE user_icon");
  mysql_query('COMMIT'); //一応コミット

  //DB 接続解除は OutputActionResult() 経由
  $sentence = '削除完了：登録ページに飛びます。<br>'."\n" .
    '切り替わらないなら <a href="icon_upload.php">ここ</a> 。';
  OutputActionResult('アイコン削除完了', $sentence, 'icon_upload.php');
}

//アップロードフォーム出力
function OutputIconUploadPage(){
  global $USER_ICON;

  OutputHTMLHeader('ユーザアイコンアップロード', 'icon_upload');
  $icon_size_max = $USER_ICON->IconSizeMax();
  $icon_file_size_max = $USER_ICON->IconFileSizeMax();
  $icon_name_length_max = $USER_ICON->IconNameMaxLength();

  echo <<<EOF
</head>
<body>
<a href="./">←戻る</a><br>
<img class="title" src="img/icon_upload_title.jpg"><br>
<table align="center">
<tr><td class="link"><a href="icon_view.php">→アイコン一覧</a></td><tr>
<tr><td class="caution">＊あらかじめ指定する大きさ ({$icon_size_max}) にリサイズしてからアップロードしてください。</td></tr>
<tr><td>
<fieldset><legend>アイコン指定 (jpg, gif, png 画像を登録して下さい。{$icon_file_size_max})</legend>
<form method="POST" action="icon_upload.php" enctype="multipart/form-data">
<table>
<tr><td><label>ファイル選択</label>
<input type="file" name="file" size="80">
<input type="hidden" name="max_file_size" value="{$USER_ICON->size}">
<input type="hidden" name="command" value="upload">
<input type="submit" value="登録">
</td></tr>

<tr><td><label>アイコンの名前</label>
<input type="text" name="name" maxlength="20" size="20">{$icon_name_length_max}</td></tr>

<tr><td><label>アイコンに合った色を選択してください</label></td></tr>
<tr><td>
<input id="fix_color" type="radio" name="color"><label for="fix_color">色を指定する</label>
<input type="text" name="color" size="10px" maxlength="7">(例：#6699CC)
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
    if($count > 0 && ($count % 6) == 0) echo "</tr>\n<tr>\n"; //6個ごとに改行
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

</td></tr></form></fieldset>
</td></tr></table>
</body></html>

EOF;
}
