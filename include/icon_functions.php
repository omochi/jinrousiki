<?php
function OutputIconPageHeader(){
  OutputHTMLHeader('ユーザアイコン一覧', 'icon_view');
  echo <<<HTML
</head>
<body>
<a href="./">←ホームページに戻る</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">→アイコン登録</a></div>

HTML;
}


function OutputIconList($base_url = 'icon_view'){
  global $RQ_ARGS;
  //初回表示前に検索条件をリセットする
  //TODO:リファラーをチェックすることでGETリクエストによる取得にも対処できる
  //現時点ではGETで直接検索を試みたユーザーのセッション情報まで配慮していないが、いずれ必要になるかも知れない (enogu)
  if (!isset($RQ_ARGS->page)) {
    unset($_SESSION['icon_view']);
  }
  //編集フォームの表示
  if ($base_url == 'icon_view') {
    if(0 < $RQ_ARGS->icon_no){
      $params = $RQ_ARGS->ToArray();
      unset($params['icon_no']);
      echo <<<HTML
<div class="link"><a href="icon_view.php">→アイコン一覧に戻る</a></div>
<form action="icon_edit.php" method="POST">
<fieldset><legend>アイコン設定の変更</legend>

HTML;
      OutputIconEditForm($RQ_ARGS->icon_no);
      echo <<<HTML
</fieldset>
</form>

HTML;
    }
    else {
      echo <<<HTML
<form id="icon_search" method="GET">
<fieldset><legend>ユーザアイコン一覧</legend>

HTML;
      ConcreteOutputIconList($base_url);
      echo <<<HTML
</fieldset>
</form>

HTML;
    }
  }
  else {
    ConcreteOutputIconList($base_url);
  }
}

function OutputIconEditForm($icon_no) {
  global $ICON_CONF, $USER_ICON, $RQ_ARGS;
  foreach(FetchAssoc("SELECT * FROM user_icon WHERE icon_no = {$icon_no}") as $selected) {
    extract($selected, EXTR_PREFIX_ALL, 'selected');
    $location = $ICON_CONF->path . '/' . $selected_icon_filename;
    echo <<<EOF
<form method="POST" action="icon_edit.php">
<input type="hidden" name="icon_no" value="{$selected_icon_no}">
<table>
<tr><td rowspan="6"><img src="{$location}"></td>
<td><label>アイコンの名前</label></td>
<td><input type="text" name="icon_name" maxlength="{$icon_name_length_max}" size="{$icon_name_length_max}" value="{$selected_icon_name}"></td></tr>

<tr><td><label>出典</label></td>
<td><input type="text" name="appearance" maxlength="{$icon_name_length_max}" size="{$icon_name_length_max}" value="{$selected_appearance}"></td></tr>

<tr><td><label>カテゴリ</label></td>
<td><input type="text" name="category" maxlength="{$icon_name_length_max}" size="{$icon_name_length_max}" value="{$selected_category}"></td></tr>

<tr><td><label>アイコンの作者</label></td>
<td><input type="text" name="author" maxlength="{$icon_name_length_max}" size="{$icon_name_length_max}" value="{$selected_author}"></td></tr>

<tr><td><label>アイコン枠の色</label></td>
<td><input type="text" name="color" size="10px" maxlength="7" value="{$selected_color}"> (例：#6699CC)</td></tr>

<tr><td><label>編集パスワード</label></td>
<td><input type="password" name="password" size="20"></td></tr>

<tr><td colspan="2"><input type="submit" value="変更"></td></tr>
</table>
</form>

EOF;
  }
}

function ConcreteOutputIconList($base_url = 'icon_view') {
  global $ICON_CONF, $USER_ICON, $RQ_ARGS;

  //アイコン情報の準備
  $sql_prepare = <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS _icons
SELECT
  ico.icon_no,
  ico.icon_name,
  ico.icon_filename,
  ico.icon_width,
  ico.icon_height,
  ico.color,
  ico.appearance,
  ico.category,
  ico.author,
  COUNT( usr.uname ) AS num_used
FROM
  user_icon AS ico
  LEFT JOIN user_entry AS usr USING (icon_no)
GROUP BY
  ico.icon_no,
  ico.icon_name,
  ico.icon_filename,
  ico.icon_width,
  ico.icon_height,
  ico.color,
  ico.appearance,
  ico.category,
  ico.author

SQL;
  SendQuery($sql_prepare);

  //ヘッダーの出力
  $icon_count = FetchResult('SELECT COUNT(icon_no) FROM _icons WHERE icon_no > 0');
  $line_header = '<tr><td colspan="10">';
  $line_footer = '</td></tr>'."\n";
  $url_header  = '<a href="' . $base_url . '.php?';
  $url_option  = array();
  $query_stack = array();
  $category_list = GetIconCategoryList('category');
  $all_url = $url_header;
  if($RQ_ARGS->room_no > 0) $all_url .= 'room_no=' . $RQ_ARGS->room_no;

  //PrintData($category_list);
  //PrintData($RQ_ARGS);

  echo <<<EOF
<table>
<tr>

EOF;

  //検索項目とタイトル、検索条件のセットから選択肢を抽出し、表示します。
  function _outputSelectionByType($type, $caption, $filter = array()) {
    global $RQ_ARGS;
    //選択状態の抽出
    $selection_source = $RQ_ARGS->search ? $RQ_ARGS->$type : $_SESSION['icon_view'][$type];
    $selected
      = !empty($selection_source)
        ? is_array($selection_source) ? $selection_source : array($selection_source)
        : array();
    $_SESSION['icon_view'][$type] = $selected;
    //選択肢の生成
    $sql = "SELECT DISTINCT {$type} FROM _icons WHERE {$type} IS NOT NULL";
    if (count($filter)) {
      $sql .= ' AND '.implode(' AND ', $filter);
    }
    $list = FetchArray($sql);
    //表示
    echo <<<HTML
<td>
<label for="{$type}[]">{$caption}</label><br>
<select name="{$type}[]" size="6" multiple style="width:12em;">
<option value="__all__">全て</option>',

HTML;
    foreach($list as $name){
      printf(
        '<option value="%s" %s>%s</option>',
        $name,
        in_array($name, $selected) ? 'selected' : '',
        strlen($name) ? $name : '空欄'
      );
    }
    echo '</select></td>';
    return in_array('__all__', $selected) ? array() : $selected;
  }

  //検索項目と検索値のセットから抽出条件を生成します。
  function _generateInClause($type, $values) {
    $safe_values = array();
    foreach($values as $value) {
      $safe_values[] = sprintf("'%s'", mysql_real_escape_string($value));
    }
    return $type.' IN ('.implode(',', $safe_values).')';
  }

  //検索条件の表示
  $where_cond = array();

  $selected_categories = _outputSelectionByType('category', 'カテゴリ');
  if(0 < count($selected_categories)){
    foreach($selected_categories as $cat) {
      $url_option[] = "category[]={$cat}";
    }
    $where_cond[] = _generateInClause('category', $selected_categories);
  }

  $selected_appearances = _outputSelectionByType('appearance', '出典');
  if(0 < count($selected_appearances)){
    foreach($selected_appearances as $apr) {
      $url_option[] = "appearance[]={$apr}";
    }
    $where_cond[] = _generateInClause('appearance', $selected_appearances);
  }

  $selected_authors = _outputSelectionByType('author', 'アイコン作者');
  if(0 < count($selected_authors)){
    foreach($selected_authors as $ath) {
      $url_option[] = "author[]={$ath}";
    }
    $where_cond[] = _generateInClause('author', $selected_authors);
  }
  $sort_by_name_checked = $RQ_ARGS->sort_by_name ? 'checked' : '';
  echo <<<EOF
</tr>
<tr>
<td colspan="10">
<label><input id="sort_by_name" name="sort_by_name" type="checkbox" value="1" $sort_by_name_checked>名前順に並べ替える</label>
<input id="search" name="search" type="submit" value="検索">
<input id="page" name="page" type="hidden" value="1">
</td>
</tr>
</table>

EOF;

  //検索結果の表示
  if ($is_icon_view = empty($RQ_ARGS->room_no)) {
    echo <<<HTML
<table>
<caption>
[S] 出典 / [C] カテゴリ / [A] アイコンの作者 / [U] 使用回数'<br>
アイコンをクリックすると編集できます (要パスワード)'
</caption>

HTML;
  }
  if ($is_user_entry = isset($RQ_ARGS->room_no)) {
    echo <<<HTML
<table>
<caption>
あなたのアイコンを選択して下さい。
</caption>
<thead>
<tr>

HTML;
  }

  //ユーザアイコンのテーブルから一覧を取得
  $query = 'SELECT * FROM _icons WHERE ';
  $where_cond[] = 'icon_no > 0';
  $query .= implode(' AND ', $where_cond);
  if ($RQ_ARGS->sort_by_name) {
    $query .= ' ORDER BY icon_name, icon_no';
  }
  else {
    $query .= ' ORDER BY icon_no, icon_name';
  }
  if($RQ_ARGS->page != 'all'){
    $limit_min = $ICON_CONF->view * ($RQ_ARGS->page - 1);
    if($limit_min < 1) $limit_min = 0;
    $query .= sprintf(' LIMIT %d, %d', $limit_min, $ICON_CONF->view);
  }
  $records = FetchAssoc($query);

  //ページリンクの作成
  echo <<<HTML
<script type="text/javascript"><!--
function submitIconSearch(page) {
  if (window.document.forms.icon_search) {
    window.document.forms.icon_search.page.value = page;
    window.document.forms.icon_search.submit();
  }
  else {
    window.document.forms[0].page.value = page;
    window.document.forms[0].submit();
  }
  return false;
}
//--></script>

HTML;
  $query = 'SELECT COUNT(icon_no) AS total_count FROM _icons WHERE ';
  $where_cond[] = 'icon_no > 0';
  $query .= implode(' AND ', $where_cond);
  $total_count = FetchResult($query);
  $PAGE_CONF = $ICON_CONF;
  $PAGE_CONF->count = $total_count;
  $PAGE_CONF->url     = $base_url;
  $PAGE_CONF->current = $RQ_ARGS->page;
  $PAGE_CONF->option  = $url_option;
  $PAGE_CONF->attributes  = array('onclick'=>'return "return submitIconSearch(\'$page\');";');
  if($RQ_ARGS->room_no > 0) $PAGE_CONF->option[] = 'room_no=' . $RQ_ARGS->room_no;
  echo '<td colspan="10" style="text-align:right;">';
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
  $icon_list = array();
  $columns = 0;
  if ($is_icon_view) {
    $method = 'OutputIconDetailsForIconView';
  }
  elseif ($is_user_entry) {
    $method = 'OutputIconDetailsForUserEntry';
  }
  else {
    $method = false;
  }
  if ($method !== false) {
    foreach($records as $icon_info) {
      $method($icon_info, array('cellwidth'=>162));
      if (4 <= ++$columns) {
        $columns = 0;
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

function OutputIconDetailsForIconView($icon_info, $format_info) {
    global $ICON_CONF;
    extract($icon_info);
    extract($format_info, EXTR_PREFIX_ALL, 'frm');
    $location = $ICON_CONF->path . '/' . $icon_filename;
    $wrapper_width = $icon_width + 6;
    $info_width = $frm_cellwidth - $icon_width;
    $edit_url = "icon_view.php?icon_no={$icon_no}";
    echo <<<HTML
<td class="icon_details">
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
    if(!empty($appearance)) $data .= '<li>[S]' . $appearance;
    if(!empty($category))   $data .= '<li>[C]' . $category;
    if(!empty($author))     $data .= '<li>[A]' . $author;
    $data .= '<li>[U]' . $num_used;
    echo $data;
    echo <<<HTML
</ul>
</label>
</td>

HTML;
}

function OutputIconDetailsForUserEntry($icon_info, $format_info) {
    global $ICON_CONF;
    extract($icon_info);
    extract($format_info, EXTR_PREFIX_ALL, 'frm');
    $location = $ICON_CONF->path . '/' . $icon_filename;
    $wrapper_width = $icon_width + 6;
    $info_width = $frm_cellwidth - $wrapper_width;
    echo <<<HTML
<th>
<input type="radio" id="icon_{$icon_no}" name="icon_no" value="{$icon_no}">
</th>
<td class="icon_details">
<label for="icon_{$icon_no}">
<div class="icon_wrapper" style="width:{$wrapper_width}px">
<img alt="{$icon_name}" src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border:3px solid {$color};">
</div>
<ul style="width:{$info_width}px;">
<li>No. {$icon_no}</li>
<li><font color="{$color}">◆</font>{$icon_name}</li>
</ul>
</label>
</td>

HTML;
}

function GetIconCategoryList($type, $limit = '', $query_stack = array()){
  $stack = array('SELECT', 'FROM user_icon WHERE', 'IS NOT NULL GROUP BY', 'ORDER BY icon_no');
  if(count($query_stack) > 0){
    $list = $query_stack;
    $list[] = '';
    $stack[1] .= ' ' . implode(' AND ', $list);
  }
  return FetchArray(implode(" {$type} ", $stack) . $limit);
}

function AddIconURLOption(&$stack, $option){
  global $RQ_ARGS;
  if(is_int($RQ_ARGS->$option)) $stack[] = "{$option}={$RQ_ARGS->$option}";
}

function OutputIconPageFooter(){
  OutputHTMLFooter();
}
