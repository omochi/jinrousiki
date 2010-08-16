<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF', 'USER_ICON');
$INIT_CONF->LoadRequest('RequestIconEdit'); //引数を取得
EditIcon();

//-- 関数 --//
function EditIcon(){
  global $DB_CONF, $USER_ICON, $ICON_CONF, $RQ_ARGS;

  $title = 'ユーザアイコン編集';
  if(CheckReferer('icon_view.php')){ //リファラチェック
    OutputActionResult($title, '無効なアクセスです');
  }

  extract($RQ_ARGS->ToArray()); //引数を展開
  if($password != $USER_ICON->password){
    OutputActionResult($title, 'パスワードが違います');
  }
  $query_stack = array();

  $DB_CONF->Connect(); //DB 接続
  //アイコンの名前が既に登録されていないかチェック
  if(FetchResult('SELECT COUNT(icon_no) FROM user_icon WHERE icon_no = ' . $icon_no) < 1){
    OutputActionResult($title, '無効なアイコン番号です：' . $icon_no);
  }

  //アイコン名の文字列長のチェック
  $text_list = array('icon_name' => 'アイコン名',
		     'appearance' => '出典',
		     'category' => 'カテゴリ',
		     'author' => 'アイコンの作者');
  foreach($text_list as $text => $label){
    $value = $RQ_ARGS->$text;
    if(strlen($value) < 1) continue;
    if(strlen($value) > $USER_ICON->name){
      OutputActionResult($title, $label . ': ' . $USER_ICON->IconNameMaxLength());
    }
    $query_stack[] = "{$text} = '{$value}'";
  }

  //アイコンの名前が既に登録されていないかチェック
  if(strlen($icon_name) > 0 &&
     FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_name = '{$icon_name}'") > 0){
    OutputActionResult($title, 'アイコン名 "' . $icon_name . '" は既に登録されています');
  }

  //色指定のチェック
  if(strlen($color) > 0){
    if(strlen($color) != 7 && ! preg_match('/^#[0123456789abcdefABCDEF]{6}/', $color)){
      $sentence = '色指定が正しくありません。<br>'."\n" .
	'指定は (例：#6699CC) のように RGB 16進数指定で行ってください。<br>'."\n" .
	'送信された色指定 → <span class="color">' . $color . '</span>';
      OutputActionResult($title, $sentence);
    }
    $color = strtoupper($color);
    $query_stack[] = "color = '{$color}'";
  }

  if(count($query_stack) < 1){
    OutputActionResult($title, '変更内容はありません');
  }
  $query = 'UPDATE user_icon SET ' . implode(', ', $query_stack) . ' WHERE icon_no = ' . $icon_no;
  //OutputActionResult($title, $query); //テスト用

  if(! mysql_query('LOCK TABLES user_icon WRITE')){ //user_icon テーブルをロック
    $sentence = "サーバが混雑しています。<br>\n時間を置いてから再登録をお願いします。";
    OutputActionResult($title, $sentence);
  }
  SendQuery($query, true);
  OutputActionResult($title, '編集完了', 'icon_view.php?icon_no=' . $icon_no, true);
}
