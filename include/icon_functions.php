<?php
function OutputIconPageHeader(){
  OutputHTMLHeader('ユーザアイコン一覧', 'icon_view');
  echo <<<EOF
</head>
<body>
<a href="./">←戻る</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">→アイコン登録</a></div>
<fieldset><legend>ユーザアイコン一覧</legend>
<table>

EOF;
}

function OutputIconList($base_url = 'icon_view'){
  global $ICON_CONF, $USER_ICON, $RQ_ARGS;

  $icon_count = FetchResult('SELECT COUNT(icon_no) FROM user_icon WHERE icon_no > 0');
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

  //カテゴリ
  $config->view = 5;
  $config->page = 5;
  $builder = new PageLinkBuilder($base_url, $RQ_ARGS->category_page, count($category_list),
				 $config, 'カテゴリ', 'category_page');
  if($RQ_ARGS->room_no > 0) $builder->AddOption('room_no', $RQ_ARGS->room_no);
  $builder->header = '<tr><td colspan="10">';
  $builder->footer = '</td></tr>'."\n";
  $builder->Output();
  $builder->AddOption('category_page', $RQ_ARGS->category_page);
  //PrintData($builder->query);

  $source_list = GetIconCategoryList('category', $builder->query);
  $count = count($source_list);
  $stack = array();
  for($i = 0; $i < $count; $i++){
    $list = $builder->option;
    $list[] = 'category=' . ($i + $builder->limit);
    $name = $source_list[$i];
    $stack[] = $RQ_ARGS->category === $i ? $name :
      $url_header . implode('&', $list) . '">' . $name . '</a>';
  }
  $stack[] = $RQ_ARGS->category == 'all' ? 'all' : $all_url . 'category=all">all</a>';

  echo $line_header . implode(' / ', $stack) . $line_footer;
  if(is_int($RQ_ARGS->category)){
    $type = $category_list[$RQ_ARGS->category];
    $query_stack[] = "category = '{$type}'";
    $url_option[] = "category={$RQ_ARGS->category}";
    $builder->AddOption('category', $RQ_ARGS->category);
  }
  AddIconURLOption($url_option, 'category_page');

  //出典
  $appearance_list = GetIconCategoryList('appearance', '', $query_stack);
  $builder->view_total = count($appearance_list);
  $builder->title      = '出典';
  $builder->type       = 'appearance_page';
  $builder->SetPage($RQ_ARGS->appearance_page);
  $source_list = GetIconCategoryList('appearance', $builder->query, $query_stack);
  $builder->Output();
  $builder->AddOption('appearance_page', $RQ_ARGS->appearance_page);

  $count = count($source_list);
  $stack = array();
  for($i = 0; $i < $count; $i++){
    $list = $builder->option;
    $list[] = 'appearance=' . ($i + $builder->limit);
    $name = $source_list[$i];
    $stack[] = $RQ_ARGS->appearance === $i ? $name :
      $url_header . implode('&', $list) . '">' . $name . '</a>';
  }
  $stack[] = $RQ_ARGS->appearance == 'all' ? 'all' : $all_url . 'appearance=all">all</a>';

  echo $line_header . implode(' / ', $stack) . $line_footer;
  if(is_int($RQ_ARGS->appearance)){
    $type = $appearance_list[$RQ_ARGS->appearance];
    $query_stack[] = "appearance = '{$type}'";
    $url_option[] = "appearance={$RQ_ARGS->appearance}";
  }
  AddIconURLOption($url_option, 'appearance_page');

  //出典
  $author_list = GetIconCategoryList('author', '', $query_stack);
  $builder->view_total = count($author_list);
  $builder->title      = 'アイコンの作者';
  $builder->type       = 'author_page';
  $builder->SetPage($RQ_ARGS->author_page);
  $source_list = GetIconCategoryList('author', $builder->query, $query_stack);
  $builder->Output();
  $builder->AddOption('author_page', $RQ_ARGS->author_page);

  $count = count($source_list);
  $stack = array();
  for($i = 0; $i < $count; $i++){
    $list = $builder->option;
    $list[] = 'author=' . ($i + $builder->limit);
    $name = $source_list[$i];
    $stack[] = $RQ_ARGS->author === $i ? $name :
      $url_header . implode('&', $list) . '">' . $name . '</a>';
  }
  $stack[] = $RQ_ARGS->author == 'all' ? 'all' : $all_url . 'author=all">all</a>';

  echo $line_header . implode(' / ', $stack) . $line_footer;
  if(is_int($RQ_ARGS->author)){
    $type = $author_list[$RQ_ARGS->author];
    $query_stack[] = "author = '{$type}'";
    $url_option[] = "author={$RQ_ARGS->author}";
  }
  AddIconURLOption($url_option, 'author_page');

  //ユーザアイコンのテーブルから一覧を取得
  $query = 'SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color, ' .
    'appearance, category, author FROM user_icon WHERE ';
  if($RQ_ARGS->icon_no > 0){
    $query .= 'icon_no = ' . $RQ_ARGS->icon_no;
  }
  else{
    $query_stack[] = 'icon_no > 0';
    $query .= implode(' AND ', $query_stack);
    $query .= ' ORDER BY icon_no';
  }

  $PAGE_CONF = $ICON_CONF;
  $PAGE_CONF->count = count(FetchAssoc($query));
  $PAGE_CONF->url     = $base_url;
  $PAGE_CONF->current = $RQ_ARGS->page;
  $PAGE_CONF->option  = $url_option;
  if($RQ_ARGS->room_no > 0) $PAGE_CONF->option[] = 'room_no=' . $RQ_ARGS->room_no;
  echo $line_header . "\n";
  //PrintData($PAGE_CONF, 'PAGE_CONF');
  OutputPageLink($PAGE_CONF);
  echo "</td></tr>\n";
  if(is_null($RQ_ARGS->room_no)){
    echo $line_header . '[S] 出典 / [C] カテゴリ / [A] アイコンの作者 / [U] 使用回数' . $line_footer;
    echo $line_header . 'アイコンをクリックすると編集できます (要パスワード)' . $line_footer;
  }

  //表の出力
  if($RQ_ARGS->page != 'all'){
    $limit_min = $ICON_CONF->view * ($RQ_ARGS->page - 1);
    if($limit_min < 1) $limit_min = 0;
    $query .= sprintf(' LIMIT %d, %d', $limit_min, $ICON_CONF->view);
  }
  $icon_list = FetchAssoc($query);
  $count = 0;
  $query_use_count = 'SELECT COUNT(uname) FROM user_entry WHERE icon_no = ';
  foreach($icon_list as $array){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    extract($array);
    $anchor = '';
    $location = $ICON_CONF->path . '/' . $icon_filename;
    if($RQ_ARGS->room_no > 0){
      echo <<<EOF
<td><label for="{$icon_no}"><img src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border-color:{$color};"> No. {$icon_no}<br>{$icon_name}<br>
<font color="{$color}">◆</font><input type="radio" id="{$icon_no}" name="icon_no" value="{$icon_no}"></label></td>

EOF;
    }
    else{
      $data = '';
      if(isset($appearance)) $data .= '<br>[S]' . $appearance;
      if(isset($category))   $data .= '<br>[C]' . $category;
      if(isset($author))     $data .= '<br>[A]' . $author;
      $data .= '<br>[U]' . FetchResult($query_use_count . $icon_no);
      echo <<<EOF
<td><a href="{$base_url}.php?icon_no={$icon_no}">
<img src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border-color:{$color};">
</a></td>
<td class="name">No. {$icon_no}<br>{$icon_name}<br><font color="{$color}">◆</font>{$color}{$data}</td>

EOF;
    }

    if($RQ_ARGS->icon_no > 0){
      echo <<<EOF
<td><form method="POST" action="icon_edit.php">
<input type="hidden" name="icon_no" value="{$icon_no}">
<table>
<tr><td><label>アイコンの名前</label></td>
<td><input type="text" name="icon_name" maxlength="{$USER_ICON->name}" size="{$USER_ICON->name}">{$icon_name_length_max}</td></tr>

<tr><td><label>出典</label></td>
<td><input type="text" name="appearance" maxlength="{$USER_ICON->name}" size="{$USER_ICON->name}">{$icon_name_length_max}</td></tr>

<tr><td><label>カテゴリ</label></td>
<td><input type="text" name="category" maxlength="{$USER_ICON->name}" size="{$USER_ICON->name}">{$icon_name_length_max}</td></tr>

<tr><td><label>アイコンの作者</label></td>
<td><input type="text" name="author" maxlength="{$USER_ICON->name}" size="{$USER_ICON->name}">{$icon_name_length_max}</td></tr>

<tr><td><label>アイコン枠の色</label></td>
<td><input type="text" name="color" size="10px" maxlength="7"> (例：#6699CC)</td></tr>

<tr><td><label>編集パスワード</label></td>
<td><input type="password" name="password" size="20"></td></tr>

<tr><td colspan="2"><input type="submit" value="変更"></td></tr>
</table>
</form></td>

EOF;
    }
    else{
      echo "</td>\n";
    }
  }
  for($i = $count; $i < 5; $i++) echo '<td></td>';
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
  echo "</tr></table>\n</fieldset>\n";
  OutputHTMLFooter();
}
