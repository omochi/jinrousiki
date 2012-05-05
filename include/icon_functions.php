<?php
//-- DB アクセス (アイコン拡張) --//
class IconDB {
  //村で使用中のアイコンチェック
  static function IsUsing($icon_no){
    $format = 'SELECT icon_no FROM user_icon INNER JOIN ' .
      '(user_entry INNER JOIN room USING (room_no)) USING (icon_no) ' .
      "WHERE icon_no = %d AND room.status IN ('waiting', 'playing')";
    return DB::Count(sprintf($format, $icon_no)) > 0;
  }

  //アイコン情報取得
  static function GetInfo($icon_no){
    $format = 'SELECT * FROM user_icon WHERE icon_no = %d';
    return DB::FetchAssoc(sprintf($format, $icon_no));;
  }

  //アイコンリスト取得
  static function GetList($where){
    global $ICON_CONF;

    $format  = 'SELECT * FROM user_icon WHERE %s ORDER BY %s';
    $where[] = 'icon_no > 0';
    $sort    = RQ::$get->sort_by_name ? 'icon_name, icon_no' : 'icon_no, icon_name';
    $query   = sprintf($format, implode(' AND ', $where), $sort);
    if (RQ::$get->page != 'all') {
      $limit = max(0, $ICON_CONF->view * (RQ::$get->page - 1));
      $query .= sprintf(' LIMIT %d, %d', $limit, $ICON_CONF->view);
    }
    return DB::FetchAssoc($query);
  }

  //アイコン数取得
  static function GetCount($where){
    $format  = 'SELECT icon_no FROM user_icon WHERE %s';
    $where[] = 'icon_no > 0';
    return DB::Count(sprintf($format, implode(' AND ', $where)));
  }

  //カテゴリ取得
  static function GetCategoryList($type){
    $stack = array('SELECT', 'FROM user_icon WHERE', 'IS NOT NULL GROUP BY', 'ORDER BY icon_no');
    return DB::FetchArray(implode(" {$type} ", $stack));
  }

  //検索項目とタイトル、検索条件のセットから選択肢を抽出し、表示します。
  static function GetSelectionByType($type){
    //選択状態の抽出
    $data   = RQ::$get->search ? RQ::$get->$type : $_SESSION['icon_view'][$type];
    $target = empty($data) ? array() : (is_array($data) ? $data : array($data));
    $_SESSION['icon_view'][$type] = $target;
    //PrintData($data, $type);
    if ($type == 'keyword') return $target;

    $format = 'SELECT DISTINCT %s FROM user_icon WHERE %s IS NOT NULL';
    return DB::FetchArray(sprintf($format, $type, $type));
  }

  //検索項目と検索値のセットから抽出条件を生成する
  static function GetInClause($type, $list){
    if (in_array('__null__', $list)) return $type . ' IS NULL';
    $stack = array();
    foreach ($list as $value) $stack[] = sprintf("'%s'", Text::Escape($value));
    return $type . sprintf(' IN (%s)', implode(',', $stack));
  }

  //アイコン削除
  static function Delete($icon_no, $file){
    global $ICON_CONF;

    $query = sprintf('DELETE FROM user_icon WHERE icon_no = %d', $icon_no);
    if (! DB::FetchBool($query)) return false; //削除処理
    unlink($ICON_CONF->path . '/' . $file); //ファイル削除
    DB::Optimize('user_icon'); //テーブル最適化 + コミット
    return true;
  }
}

//-- HTML 生成クラス (icon 拡張) --//
class IconHTML {
  //アイコン情報出力
  static function Output($base_url = 'icon_view'){
    /*
      初回表示前に検索条件をリセットする
      TODO: リファラーをチェックすることで GET リクエストによる取得にも対処できる
      現時点では GET で直接検索を試みたユーザーのセッション情報まで配慮していないが、
      いずれ必要になるかも知れない (enogu)
    */
    if (is_null(RQ::$get->page)) unset($_SESSION['icon_view']);

    //編集フォームの表示
    if ($base_url == 'icon_view') {
      $footer = "</fieldset>\n</form>\n";
      if (RQ::$get->icon_no > 0) {
	$params = RQ::ToArray();
	unset($params['icon_no']);
	echo <<<HTML
<div class="link"><a href="icon_view.php">→アイコン一覧に戻る</a></div>
<form action="icon_edit.php" method="POST">
<fieldset><legend>アイコン設定の変更</legend>

HTML;
	self::OutputEdit(RQ::$get->icon_no);
	echo $footer;
      }
      else {
	echo <<<HTML
<form id="icon_search" method="GET">
<fieldset><legend>ユーザアイコン一覧</legend>

HTML;
	self::OutputConcrete($base_url);
	echo $footer;
      }
    }
    else {
      self::OutputConcrete($base_url);
    }
  }

  //アイコン編集フォーム出力
  private function OutputEdit($icon_no){
    global $ICON_CONF, $USER_ICON;

    $size  = sprintf(' size="%d" maxlength="%d"', $USER_ICON->name, $USER_ICON->name);
    foreach (IconDB::GetInfo($icon_no) as $stack) {
      extract($stack);
      $location = $ICON_CONF->path . '/' . $icon_filename;
      $checked  = $disable > 0 ? ' checked' : '';
      echo <<<EOF
<form method="POST" action="icon_edit.php">
<input type="hidden" name="icon_no" value="{$icon_no}">
<table cellpadding="3">
<tr>
  <td rowspan="7"><img src="{$location}" style="border:3px solid {$color};"></td>
  <td><label for="name">アイコンの名前</label></td>
  <td><input type="text" id="name" name="icon_name" value="{$icon_name}"{$size}></td>
</tr>
<tr>
  <td><label for="appearance">出典</label></td>
  <td><input type="text" id="appearance" name="appearance" value="{$appearance}"{$size}></td>
</tr>
<tr>
  <td><label for="category">カテゴリ</label></td>
  <td><input type="text" id="category" name="category" value="{$category}"{$size}></td>
</tr>
<tr>
  <td><label for="author">アイコンの作者</label></td>
  <td><input type="text" id="author" name="author" value="{$author}"{$size}></td>
</tr>
<tr>
  <td><label for="color">アイコン枠の色</label></td>
  <td><input type="text" id="color" name="color" value="{$color}" size="10px" maxlength="7"> (例：#6699CC)</td>
</tr>
<tr>
  <td><label for="disable">非表示</label></td>
  <td><input type="checkbox" id="disable" name="disable" value="on"{$checked}></td>
</tr>
<tr>
  <td><label for="password">編集パスワード</label></td>
  <td><input type="password" id="password" name="password" size="20" value=""></td>
</tr>
<tr>
  <td colspan="3"><input type="submit" value="変更"></td>
</tr>
</table>
</form>

EOF;
    }
  }

  //アイコン情報を収集して表示する
  private function OutputConcrete($base_url = 'icon_view'){
    global $ICON_CONF, $USER_ICON;

    //-- ヘッダ出力 --//
    $colspan       = $USER_ICON->column * 2;
    $line_header   = sprintf('<tr><td colspan="%d">', $colspan);
    $line_footer   = '</td></tr>'."\n";
    $url_header    = sprintf('<a href="%sphp?', $base_url);
    $url_option    = array();
    $query_stack   = array();
    $category_list = IconDB::GetCategoryList('category');
    //PrintData($category_list);
    echo '<table class="selector">'."\n<tr>\n";

    //検索条件の表示
    $where = array();
    if ($base_url == 'user_manager') $where[] = "disable IS NOT TRUE";
    $stack = self::OutputByType('category', 'カテゴリ');
    if (0 < count($stack)) {
      foreach ($stack as $data) $url_option[] = sprintf("category[]={$data}");
      $where[] = IconDB::GetInClause('category', $stack);
    }

    $stack = self::OutputByType('appearance', '出典');
    if (0 < count($stack)) {
      foreach ($stack as $data) $url_option[] = "appearance[]={$data}";
      $where[] = IconDB::GetInClause('appearance', $stack);
    }

    $stack = self::OutputByType('author', 'アイコン作者');
    if (0 < count($stack)) {
      foreach ($stack as $data) $url_option[] = "author[]={$data}";
      $where[] = IconDB::GetInClause('author', $stack);
    }

    $stack = self::OutputByType('keyword', 'キーワード');
    if (0 < count($stack)) {
      $str = "LIKE '%{$stack[0]}%'";
      $where[] = "(category {$str} OR appearance {$str} OR author {$str} OR icon_name {$str})";
    }
    else {
      $stack = array('');
    }
    $keyword = $stack[0];
    //PrintData($where);

    $sort_by_name_checked = RQ::$get->sort_by_name ? ' checked' : '';
    echo <<<EOF
</tr>
<tr>
<td colspan="{$colspan}">
<label for="sort_by_name"><input id="sort_by_name" name="sort_by_name" type="checkbox" value="on"{$sort_by_name_checked}>名前順に並べ替える</label>
<label for="keyword">キーワード：<input id="keyword" name="keyword" type="text" value="{$keyword}"></label>
<input id="search" name="search" type="submit" value="検索">
<input id="page" name="page" type="hidden" value="1">
</td></tr></table>

EOF;

    //検索結果の表示
    if (empty(RQ::$get->room_no)) {
      $method = 'OutputDetailForIconView';
      echo <<<HTML
<table>
<caption>
[S] 出典 / [C] カテゴリ / [A] アイコンの作者<br>
アイコンをクリックすると編集できます (要パスワード)
</caption>
<thead>
<tr>

HTML;
    }
    elseif (isset(RQ::$get->room_no)) {
      $method = 'OutputDetailForUserEntry';
      echo <<<HTML
<table>
<caption>
あなたのアイコンを選択して下さい。
</caption>
<thead>
<tr>

HTML;
    }
    else {
      $method = null;
    }

    //ユーザアイコンのテーブルから一覧を取得
    $PAGE_CONF = $ICON_CONF;
    $PAGE_CONF->url        = $base_url;
    $PAGE_CONF->count      = IconDB::GetCount($where);
    $PAGE_CONF->current    = RQ::$get->page;
    $PAGE_CONF->option     = $url_option;
    $PAGE_CONF->attributes = array('onclick' => 'return "return submit_icon_search(\'$page\');";');
    if (RQ::$get->room_no > 0) $PAGE_CONF->option[] = 'room_no=' . RQ::$get->room_no;
    if (RQ::$get->icon_no > 0) $PAGE_CONF->option[] = 'icon_no=' . RQ::$get->icon_no;
    printf('<td colspan="%d" class="page-link">', $colspan);
    //PrintData($PAGE_CONF, 'PAGE_CONF');
    OutputPageLink($PAGE_CONF);
    echo <<<HTML
</td>
</tr>
</thead>
<tbody>
<tr>

HTML;

    //アイコン情報の表示
    if (isset($method)) {
      $column = 0;
      foreach (IconDB::GetList($where) as $icon_info) {
	self::$method($icon_info, 162);
	if ($USER_ICON->column <= ++$column) {
	  $column = 0;
	  echo '</tr><tr>';
	}
      }
    }
    echo <<<HTML
</tr>
</tbody>
</table>

HTML;
  }

  //検索項目とタイトル、検索条件のセットから選択肢を抽出し、表示する
  private function OutputByType($type, $caption){
    $format = <<<EOF
<td>
<label for="%s[]">%s</label><br>
<select name="%s[]" size="6" multiple>
<option value="__all__">全て</option>%s
EOF;
    printf($format, $type, $caption, $type, "\n");

    $list   = IconDB::GetSelectionByType($type);
    array_unshift($list, '__null__');

    $target = $_SESSION['icon_view'][$type];
    $format = '<option value="%s"%s>%s</option>';
    foreach ($list as $name) {
      printf($format,
	     $name, in_array($name, $target) ? ' selected' : '',
	     $name == '__null__' ? 'データ無し' : (strlen($name) > 0 ? $name : '空欄'));
    }
    echo "</select>\n</td>\n";

    return in_array('__all__', $target) ? array() : $target;
  }

  //アイコン詳細画面 (IconView 用)
  private function OutputDetailForIconView($icon_list, $cellwidth){
    global $ICON_CONF;

    extract($icon_list);
    $location      = $ICON_CONF->path . '/' . $icon_filename;
    $wrapper_width = $icon_width + 6;
    $info_width    = $cellwidth - $icon_width;
    $edit_url      = "icon_view.php?icon_no={$icon_no}";
    if ($disable > 0) $icon_name = sprintf('<s>%s</s>', $icon_name);
    echo <<<HTML
<td class="icon-details">
<label for="icon_{$icon_no}">
<a href="{$edit_url}" class="icon_wrapper" style="width:{$wrapper_width}px">
<img alt="{$icon_name}" src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border:3px solid {$color};">
</a>
<ul style="width:{$info_width}px;">
<li><a href="{$edit_url}">No. {$icon_no}</a></li>
<li><a href="{$edit_url}">{$icon_name}</a></li>
<li><font color="{$color}">◆</font>{$color}</li>

HTML;

    $data = '';
    if (! empty($appearance)) $data .= '<li>[S]' . $appearance;
    if (! empty($category))   $data .= '<li>[C]' . $category;
    if (! empty($author))     $data .= '<li>[A]' . $author;
    echo $data;
    echo <<<HTML
</ul>
</label>
</td>

HTML;
  }

  //アイコン詳細画面 (UserEntry 用)
  private function OutputDetailForUserEntry($icon_list, $cellwidth){
    global $ICON_CONF;

    extract($icon_list);
    $location      = $ICON_CONF->path . '/' . $icon_filename;
    $wrapper_width = $icon_width + 6;
    $info_width    = $cellwidth - $wrapper_width;
    echo <<<HTML
<td class="icon_details"><label for="icon_{$icon_no}"><img alt="{$icon_name}" src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border:3px solid {$color};"><br clear="all">
<input type="radio" id="icon_{$icon_no}" name="icon_no" value="{$icon_no}"> No. {$icon_no}<br>
<font color="{$color}">◆</font>{$icon_name}</label></td>

HTML;
  }
}

//-- アイコン情報チェッカー --//
class IconInfo {
  //文字列長チェック
  static function CheckText($title, $url){
    global $USER_ICON;

    $stack = array();
    $list  = array('icon_name'  => 'アイコン名',
		   'appearance' => '出典',
		   'category'   => 'カテゴリ',
		   'author'     => 'アイコンの作者');
    foreach ($list as $key => $label) {
      $value = RQ::$get->$key;
      if (strlen($value) > $USER_ICON->name) {
	HTML::OutputResult($title, $label . ': ' . $USER_ICON->MaxNameLength() . $url);
      }
      $stack[$key] = strlen($value) > 0 ? $value : null;
    }
    return $stack;
  }

  //RGB カラーチェック
  static function CheckColor($str, $title, $url){
    if (strlen($str) != 7 || substr($str, 0, 1) != '#' || ! ctype_xdigit(substr($str, 1, 7))) {
      $error = '色指定が正しくありません。<br>'."\n" .
	'指定は (例：#6699CC) のように RGB 16進数指定で行ってください。<br>'."\n" .
	'送信された色指定 → <span class="color">' . $str . '</span>';
      HTML::OutputResult($title, $error . $url);
    }
    return strtoupper($str);
  }
}
