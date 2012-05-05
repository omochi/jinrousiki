<?php
//-- アイコン変更処理クラス --//
class IconEdit {
  static function Execute(){
    global $USER_ICON;

    $title = 'ユーザアイコン編集';
    //リファラチェック
    if (Security::CheckReferer('icon_view.php')) HTML::OutputResult($title, '無効なアクセスです');

    //入力データチェック
    extract(RQ::ToArray()); //引数を展開
    $back_url = sprintf("<br>\n".'<a href="icon_view.php?icon_no=%d">戻る</a>', $icon_no);
    if ($password != $USER_ICON->password) {
      HTML::OutputResult($title, 'パスワードが違います。' . $back_url);
    }

    //アイコン名の文字列長のチェック
    if (strlen($icon_name) < 1) {
      HTML::OutputResult($title, 'アイコン名が空欄になっています。' . $back_url);
    }
    $query_stack = array();
    foreach (Icon::CheckText($title, $back_url) as $key => $value) {
      $query_stack[] = sprintf("%s = %s", $key, is_null($value) ? 'NULL' : "'{$value}'");
    }

    if (strlen($color) > 0) { //色指定のチェック
      $color = Icon::CheckColor($color, $title, $back_url);
      $query_stack[] = sprintf("color = '%s'", $color);
    }

    DB::Connect();
    $lock = "サーバが混雑しています。<br>\n時間を置いてから再登録をお願いします。" . $back_url;
    if (! DB::Lock('icon')) HTML::OutputResult($title, $lock); //トランザクション開始

    $header = 'SELECT icon_no FROM user_icon WHERE ';
    if (DB::Count(sprintf('%s icon_no = %d', $header, $icon_no)) < 1) { //存在チェック
      HTML::OutputResult($title, '無効なアイコン番号です：' . $icon_no . $back_url);
    }

    //アイコンの名前が既に登録されていないかチェック
    $query = sprintf("%s icon_no <> %d AND icon_name = '%s'", $header, $icon_no, $icon_name);
    if (DB::Count($query) > 0) {
      $str = sprintf('アイコン名 "%s" は既に登録されています。', $icon_name);
      HTML::OutputResult($title, $str . $back_url);
    }

    //編集制限チェック
    if (IconDB::IsUsing($icon_no)) {
      $str = '募集中・プレイ中の村で使用されているアイコンは編集できません。';
      HTML::OutputResult($title, $str . $back_url);
    }

    //非表示フラグチェック
    $query = sprintf('%s icon_no = %d AND disable = TRUE', $header, $icon_no);
    if (DB::Count($query) > 0 !== $disable) {
      $query_stack[] = sprintf('disable = %s', $disable ? 'TRUE' : 'FALSE');
    }

    //PrintData($query_stack);
    if (count($query_stack) < 1) {
      HTML::OutputResult($title, '変更内容はありません' . $back_url);
    }
    $format = 'UPDATE user_icon SET %s WHERE icon_no = %d';
    $query  = sprintf($format, implode(', ', $query_stack), $icon_no);
    //HTML::OutputResult($title, $query . $back_url); //テスト用

    if (DB::ExecuteCommit($query)) {
      HTML::OutputResult($title, '編集完了', sprintf('icon_view.php?icon_no=%d', $icon_no));
    }
    else {
      HTML::OutputResult($title, $lock);
    }
  }
}