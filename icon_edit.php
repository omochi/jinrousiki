<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('icon_functions');
$INIT_CONF->LoadRequest('RequestIconEdit'); //引数を取得
EditIcon();

//-- 関数 --//
function EditIcon(){
  global $USER_ICON, $ICON_CONF;

  $title = 'ユーザアイコン編集';
  //リファラチェック
  if (Security::CheckReferer('icon_view.php')) HTML::OutputResult($title, '無効なアクセスです');

  //入力データチェック
  extract(RQ::ToArray()); //引数を展開
  $back_url = "<br>\n".'<a href="icon_view.php?icon_no=' . $icon_no . '">戻る</a>';
  if ($password != $USER_ICON->password) {
    HTML::OutputResult($title, 'パスワードが違います。' . $back_url);
  }

  //アイコン名の文字列長のチェック
  if (strlen($icon_name) < 1) {
    HTML::OutputResult($title, 'アイコン名が空欄になっています。' . $back_url);
  }
  $query_stack = array();
  foreach (CheckIconText($title, $back_url) as $key => $value) {
    $query_stack[] = "{$key} = " . (is_null($value) ? 'NULL' : "'{$value}'");
  }

  if (strlen($color) > 0) { //色指定のチェック
    $color = CheckColorString($color, $title, $back_url);
    $query_stack[] = "color = '{$color}'";
  }

  //負荷エラー用
  $str = "サーバが混雑しています。<br>\n時間を置いてから再登録をお願いします。" . $back_url;

  DB::Connect();
  if (! DB::Lock('icon')) HTML::OutputResult($title, $str); //トランザクション開始

  $query_header = 'SELECT icon_no FROM user_icon WHERE ';
  if (DB::Count($query_header . 'icon_no = ' . $icon_no) < 1) { //存在チェック
    HTML::OutputResult($title, '無効なアイコン番号です：' . $icon_no . $back_url);
  }

  //アイコンの名前が既に登録されていないかチェック
  if (DB::Count("{$query_header}  icon_no <> {$icon_no} AND icon_name = '{$icon_name}'") > 0) {
    $str = 'アイコン名 "' . $icon_name . '" は既に登録されています。';
    HTML::OutputResult($title, $str . $back_url);
  }

  //編集制限チェック
  if (IsUsingIcon($icon_no)) {
    $str = '募集中・プレイ中の村で使用されているアイコンは編集できません。';
    HTML::OutputResult($title, $str . $back_url);
  }

  //非表示フラグチェック
  if (DB::Count("{$query_header} icon_no = {$icon_no} AND disable = TRUE") > 0 !== $disable) {
    $query_stack[] = 'disable = ' . ($disable ? 'TRUE' : 'FALSE');
  }

  //PrintData($query_stack);
  if (count($query_stack) < 1) {
    HTML::OutputResult($title, '変更内容はありません' . $back_url);
  }
  $query = 'UPDATE user_icon SET ' . implode(', ', $query_stack) . ' WHERE icon_no = ' . $icon_no;
  //HTML::OutputResult($title, $query . $back_url); //テスト用

  if (DB::ExecuteCommit($query)) {
    HTML::OutputResult($title, '編集完了', 'icon_view.php?icon_no=' . $icon_no);
  }
  else {
    HTML::OutputResult($title, $str);
  }
}
