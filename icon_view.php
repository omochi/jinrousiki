<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF');
OutputHTMLHeader('�桼�������������', 'icon_view')
?>
</head>
<body>
<a href="./">�����</a><br>
<img class="title" src="img/icon_view_title.jpg"><br>
<div class="link"><a href="icon_upload.php">������������Ͽ</a></div>

<fieldset><legend>�桼�������������</legend>
<table>
<?php
$INIT_CONF->LoadRequest('RequestIconView'); //���������
$DB_CONF->Connect(true); //DB ��³
$icon_count = FetchResult("SELECT COUNT(icon_no) FROM user_icon WHERE icon_no > 0");
$line_header = '<tr><td colspan="10">';
$line_footer = '</td></tr>'."\n";
$url_header  = '<a href="icon_view.php?';
$url_option  = array();
$query_stack = array();
$category_list   = GetIconCategoryList('category');
//PrintData($category_list);
//PrintData($RQ_ARGS);

//���ƥ���
$config->view =  5;
$config->page = 5;
$builder = new PageLinkBuilder('icon_view', $RQ_ARGS->category_page, count($category_list),
			       $config, '���ƥ���', 'category_page');
$builder->header = '<tr><td colspan="10">';
$builder->footer = '</td></tr>'."\n";
$builder->Output();

//PrintData($builder->query);
$source_list = GetIconCategoryList('category', $builder->query);
$count = count($source_list);
$stack = array();
for($i = 0; $i < $count; $i++){
  $list = $builder->option;
  $list[] = 'category=' . ($i + $builder->limit);
  $name = $source_list[$i];
  $stack[] = ($RQ_ARGS->category === $i ? $name :
	      $url_header . implode('&', $list) . '">' . $name . '</a>');
}
$stack[] = $RQ_ARGS->category == 'all' ? 'all' : $url_header . 'category=all">all</a>';

echo $line_header . implode(' / ', $stack) . $line_footer;
if(is_int($RQ_ARGS->category)){
  $type = $category_list[$RQ_ARGS->category];
  $query_stack[] = "category = '{$type}'";
  $builder->AddOption('category', $RQ_ARGS->category);
}
$builder->AddOption('category_page', $RQ_ARGS->category_page);

//��ŵ
$appearance_list = GetIconCategoryList('appearance', '', $query_stack);
$builder->view_total = count($appearance_list);
$builder->title      = '��ŵ';
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
  $stack[] = ($RQ_ARGS->appearance === $i ? $name :
	      $url_header . implode('&', $list) . '">' . $name . '</a>');
}
$stack[] = $RQ_ARGS->appearance == 'all' ? 'all' : $url_header . 'appearance=all">all</a>';

echo $line_header . implode(' / ', $stack) . $line_footer;
if(is_int($RQ_ARGS->appearance)){
  $type = $appearance_list[$RQ_ARGS->appearance];
  $query_stack[] = "appearance = '{$type}'";
  $url_option[] = "appearance={$RQ_ARGS->appearance}";
}

//�桼����������Υơ��֥뤫����������
$query = "SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color, appearance, " .
  "category, author FROM user_icon WHERE ";
$query_stack[] = 'icon_no > 0';
$query .= implode(' AND ', $query_stack);
$query .= ' ORDER BY icon_no';

$PAGE_CONF = $ICON_CONF;
$PAGE_CONF->count = count(FetchAssoc($query));
$PAGE_CONF->url     = 'icon_view';
$PAGE_CONF->current = $RQ_ARGS->page;
$PAGE_CONF->option  = $url_option;
echo $line_header . "\n";
#PrintData($PAGE_CONF);
OutputPageLink($PAGE_CONF);
echo "</td></tr>\n";
echo $line_header . '[S] ��ŵ / [C] ���ƥ��� / [A] ��������κ��' . $line_footer;

//ɽ�ν���
if($RQ_ARGS->page != 'all'){
  $limit_min = $ICON_CONF->view * ($RQ_ARGS->page - 1);
  if($limit_min < 1) $limit_min = 0;
  $query .= sprintf(' LIMIT %d, %d', $limit_min, $ICON_CONF->view);
}
$icon_list = FetchAssoc($query);
$count = 0;
foreach($icon_list as $array){
  if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
  $count++;

  extract($array);
  $location = $ICON_CONF->path . '/' . $icon_filename;
  $data = '';
  if(isset($appearance)) $data .= '<br>[S]' . $appearance;
  if(isset($category))   $data .= '<br>[C]' . $category;
  if(isset($author))     $data .= '<br>[A]' . $author;
  echo <<< EOF
<td><img src="{$location}" width="{$icon_width}" height="{$icon_height}" style="border-color:{$color};"></td>
<td class="name">No. {$icon_no}<br>{$icon_name}<br><font color="{$color}">��</font>{$color}{$data}</td>

EOF;
}

$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
function GetIconCategoryList($type, $limit = '', $query_stack = array()){
  $stack = array('SELECT', 'FROM user_icon WHERE', 'IS NOT NULL GROUP BY', 'ORDER BY icon_no');
  if(count($query_stack) > 0){
    $list = $query_stack;
    $list[] = '';
    $stack[1] .= ' ' . implode(' AND ', $list);
  }
  return FetchArray(implode(" {$type} ", $stack) . $limit);
}
?>
</tr></table>
</fieldset>
</body>
</html>
