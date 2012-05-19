<?php
//-- アイコンアップロード処理クラス --//
class IconUpload {
  //投稿処理
  static function Execute(){
    if (Security::CheckReferer('icon_upload.php')) { //リファラチェック
      HTML::OutputResult('ユーザアイコンアップロード', '無効なアクセスです');
    }
    $title    = 'アイコン登録エラー'; // エラーページ用タイトル
    $back_url = '<br>'. "\n" . '<a href="icon_upload.php">戻る</a>';
    $query_no = sprintf(' WHERE icon_no = %d', RQ::$get->icon_no);

    switch (RQ::$get->command) {
    case 'upload':
      break;

    case 'success': //セッション ID 情報を DB から削除
      $url = 'icon_view.php';
      $str = '登録完了：アイコン一覧のページに飛びます。<br>'."\n" .
	'切り替わらないなら <a href="%s">ここ</a> 。';
      DB::Connect();
      if (! DB::FetchBool('UPDATE user_icon SET session_id = NULL' . $query_no)) {
	$str .= "<br>\nセッションの削除に失敗しました。";
      }
      HTML::OutputResult('アイコン登録完了', sprintf($str, $url), $url);
      break;

    case 'cancel': //アイコン削除
      //負荷エラー用
      $str = "サーバが混雑しているため、削除に失敗しました。<br>\n管理者に問い合わせてください。";

      DB::Connect();
      //トランザクション開始
      if (! DB::Lock('icon')) HTML::OutputResult($title, $str . $back_url);

      //アイコンのファイル名と登録時のセッション ID を取得
      $stack = DB::FetchAssoc('SELECT icon_filename, session_id FROM user_icon' . $query_no, true);
      if (count($stack) < 1) HTML::OutputResult($title, $str . $back_url);
      extract($stack);

      if ($session_id != Session::Get()) { //セッション ID 確認
	$str = '削除失敗：アップロードセッションが一致しません';
	HTML::OutputResult('アイコン削除失敗', $str . $back_url);
      }

      if (! IconDB::Delete(RQ::$get->icon_no, $icon_filename)) { //削除処理
	HTML::OutputResult($title, $str . $back_url);
      }
      DB::Disconnect();

      $url = 'icon_upload.php';
      $str = '削除完了：登録ページに飛びます。<br>'."\n" .
	'切り替わらないなら <a href="%s">ここ</a> 。';
      HTML::OutputResult('アイコン削除完了', sprintf($str, $url), $url);
      break;

    default:
      HTML::OutputResult($title, '無効なコマンドです' . $back_url);
      break;
    }

    //アップロードされたファイルのエラーチェック
    if ($_FILES['upfile']['error'][$i] != 0) {
      $str = "ファイルのアップロードエラーが発生しました。<br>\n再度実行してください。";
      HTML::OutputResult($title, $str . $back_url);
    }
    extract(RQ::ToArray()); //引数を展開

    //空白チェック
    if ($icon_name == '') HTML::OutputResult($title, 'アイコン名を入力してください' . $back_url);
    UserIcon::CheckText($title, $back_url); //アイコン名の文字列長のチェック
    $color = UserIcon::CheckColor($color, $title, $back_url); //色指定のチェック

    //ファイルサイズのチェック
    if ($size == 0) HTML::OutputResult($title, 'ファイルが空です' . $back_url);
    if ($size > UserIcon::FILE) {
      HTML::OutputResult($title, 'ファイルサイズは ' . UserIcon::GetFileLimit() . $back_url);
    }

    //ファイルの種類のチェック
    switch ($type) {
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
      $str = $type . ' : jpg、gif、png 以外のファイルは登録できません';
      HTML::OutputResult($title, $str . $back_url);
      break;
    }

    //アイコンの高さと幅をチェック
    list($width, $height) = getimagesize($tmp_name);
    if ($width > UserIcon::WIDTH || $height > UserIcon::HEIGHT) {
      $str = 'アイコンは ' . UserIcon::GetSizeLimit() . ' しか登録できません。<br>'."\n" .
	'送信されたファイル → <span class="color">幅 ' . $width . '、高さ ' . $height . '</span>';
      HTML::OutputResult($title, $str . $back_url);
    }

    //負荷エラー用
    $str = "サーバが混雑しています。<br>\n時間を置いてから再登録をお願いします。" . $back_url;

    DB::Connect();
    if (! DB::Lock('icon')) HTML::OutputResult($title, $str); //トランザクション開始

    //登録数上限チェック
    if (DB::Count('SELECT icon_no FROM user_icon') >= UserIcon::NUMBER) {
      HTML::OutputResult($title, 'これ以上登録できません');
    }

    //アイコン名チェック
    if (DB::Count("SELECT icon_no FROM user_icon WHERE icon_name = '{$icon_name}'") > 0) {
      $str = 'アイコン名 "' . $icon_name . '" は既に登録されています';
      HTML::OutputResult($title, $str . $back_url);
    }

    $icon_no = DB::FetchResult('SELECT MAX(icon_no) + 1 FROM user_icon'); //次のアイコン No を取得
    if ($icon_no === false) HTML::OutputResult($title, $str); //負荷エラー対策

    //ファイルをテンポラリからコピー
    $file_name = sprintf('%03s.%s', $icon_no, $ext); //ファイル名の桁を揃える
    if (! move_uploaded_file($tmp_name, Icon::GetFile($file_name))) {
      $str = "ファイルのコピーに失敗しました。<br>\n再度実行してください。";
      HTML::OutputResult($title, $str . $back_url);
    }

    //データベースに登録
    $data = '';
    $session_id = Session::Reset(); //セッション ID を取得
    $items  = 'icon_no, icon_name, icon_filename, icon_width, icon_height, color, ' .
      'session_id, regist_date';
    $values = "{$icon_no}, '{$icon_name}', '{$file_name}', {$width}, {$height}, '{$color}', " .
      "'{$session_id}', NOW()";

    if ($appearance != '') {
      $data   .= '<br>[S]' . $appearance;
      $items  .= ', appearance';
      $values .= ", '{$appearance}'";
    }
    if ($category != '') {
      $data   .= '<br>[C]' . $category;
      $items  .= ', category';
      $values .= ", '{$category}'";
    }
    if ($author != '') {
      $data   .= '<br>[A]' . $author;
      $items  .= ', author';
      $values .= ", '{$author}'";
    }

    if (DB::Insert('user_icon', $items, $values)) {
      DB::Commit();
      DB::Disconnect();
    }
    else {
      HTML::OutputResult($title, $str);
    }

    //確認ページを出力
    HTML::OutputHeader('ユーザアイコンアップロード処理[確認]', 'icon_upload_check', true);
    $path = Icon::GetFile($file_name);
    echo <<<EOF
<p>ファイルをアップロードしました。<br>今だけやりなおしできます</p>
<p>[S] 出典 / [C] カテゴリ / [A] アイコンの作者</p>
<table><tr>
<td><img src="{$path}" width="{$width}" height="{$height}"></td>
<td class="name">No. {$icon_no} {$icon_name}<br><font color="{$color}">◆</font>{$color}{$data}</td>
</tr>
<tr><td colspan="2">よろしいですか？</td></tr>
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

EOF;
    HTML::OutputFooter();
  }

  //アップロードフォーム出力
  static function Output(){
    HTML::OutputHeader('ユーザアイコンアップロード', 'icon_upload', true);
    $file      = UserIcon::GetFileLimit();
    $length    = UserIcon::GetMaxLength(true);
    $size      = UserIcon::GetSizeLimit();
    $caution   = UserIcon::GetCaution();
    $file_size = UserIcon::FILE;
    echo <<<EOF
<a href="./">←戻る</a><br>
<img class="title" src="img/icon_upload_title.jpg" title="アイコン登録" alt="アイコン登録"><br>
<table align="center">
<tr><td class="link"><a href="icon_view.php">→アイコン一覧</a></td><tr>
<tr><td class="caution">＊あらかじめ指定する大きさ ({$size}) にリサイズしてからアップロードしてください。{$caution}</td></tr>
<tr><td>
<fieldset><legend>アイコン指定 (jpg / gif / png 形式で登録して下さい。{$file})</legend>
<form method="POST" action="icon_upload.php" enctype="multipart/form-data">
<table>
<tr><td><label>ファイル選択</label></td>
<td>
<input type="file" name="file" size="80">
<input type="hidden" name="max_file_size" value="{$file_size}">
<input type="hidden" name="command" value="upload">
<input type="submit" value="登録">
</td></tr>
<tr><td><label>アイコンの名前</label></td>
<td><input type="text" name="icon_name" {$length}</td></tr>
<tr><td><label>出典</label></td>
<td><input type="text" name="appearance" {$length}</td></tr>
<tr><td><label>カテゴリ</label></td>
<td><input type="text" name="category" {$length}</td></tr>
<tr><td><label>アイコンの作者</label></td>
<td><input type="text" name="author" {$length}</td></tr>
<tr><td><label>アイコン枠の色</label></td>
<td>
<input id="fix_color" type="radio" name="color"><label for="fix_color">手入力</label>
<input type="text" name="color" size="10px" maxlength="7">(例：#6699CC)
</td></tr>
<tr><td colspan="2">
<table class="color" align="center">
<tr>

EOF;

    $count  = 0;
    $format = '<td bgcolor="%s"><label for="%s">' .
      '<input type="radio" id="%s" name="color" value="%s">%s</label></td>'."\n";
    $color_base = array();
    for ($i = 0; $i < 256; $i += 51) $color_base[] = sprintf('%02X', $i);
    foreach ($color_base as $i => $r) {
      foreach ($color_base as $j => $g) {
	foreach ($color_base as $k => $b) {
	  if ($count > 0 && ($count % 6) == 0) echo "</tr>\n<tr>\n"; //6個ごとに改行
	  $color = "#{$r}{$g}{$b}";
	  $name  = ($i + $j + $k) < 8  && ($i + $j) < 5 ?
	    sprintf('<font color="#FFFFFF">%s</font>', $color) : $color;
	  printf($format, $color, $color, $color, $color, $name);
	  $count++;
	}
      }
    }

    echo <<<EOF
</tr>
</table>
</td></tr></table></form></fieldset>
</td></tr></table>

EOF;
    HTML::OutputFooter();
  }
}