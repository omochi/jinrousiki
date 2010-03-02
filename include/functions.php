<?php
//-- �������ƥ���Ϣ --//
//��ե�������å�
function CheckReferer($page, $white_list = NULL){
  global $SERVER_CONF;

  if(is_array($white_list)){ //�ۥ磻�ȥꥹ�ȥ����å�
    foreach($white_list as $host){
      if(strpos($_SERVER['REMOTE_ADDR'], $host) === 0) return false;
    }
  }
  $url = $SERVER_CONF->site_root . $page;
  return strncmp(@$_SERVER['HTTP_REFERER'], $url, strlen($url)) != 0;
}

//-- DB ��Ϣ --//
//DB �䤤��碌�����Υ�åѡ��ؿ�
function SendQuery($query, $commit = false){
  if(($sql = mysql_query($query)) !== false){
    return $commit ? SendCommit() : $sql;
  }
  $backtrace = debug_backtrace(); //�Хå��ȥ졼�������

  //SendQuery() �� call �����ؿ��Ȱ��֤�������ơ�SQL���顼�פȤ����֤�
  $trace_stack = array_shift($backtrace);
  $stack = array($trace_stack['line'], $query);
  $trace_stack = array_shift($backtrace);
  array_unshift($stack, $trace_stack['function'] . '()');
  PrintData(implode(': ', $stack), 'SQL���顼');

  foreach($backtrace as $trace_stack){ //�ƤӽФ���������ʤ��ɲäǽ���
    $stack = array($trace_stack['function'] . '()', $trace_stack['line']);
    PrintData(implode(': ', $stack), 'Caller');
  }
  return false;
}

//���ߥåȽ���
function SendCommit(){
  return mysql_query('COMMIT');
}

//DB ����ñ�Τ��ͤ������������Υ�åѡ��ؿ�
function FetchResult($query){
  if(($sql = SendQuery($query)) === false) return false;
  $data = mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false;
  mysql_free_result($sql);
  return $data;
}

//DB ���鳺������ǡ����ιԿ��������������Υ�åѡ��ؿ�
function FetchCount($query){
  if(($sql = SendQuery($query)) === false) return false;
  $data = mysql_num_rows($sql);
  mysql_free_result($sql);
  return $data;
}

//DB ����켡��������������������Υ�åѡ��ؿ�
function FetchArray($query){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  $count = mysql_num_rows($sql);
  for($i = 0; $i < $count; $i++) $array[] = mysql_result($sql, $i, 0);
  mysql_free_result($sql);
  return $array;
}

//DB ����Ϣ������������������Υ�åѡ��ؿ�
function FetchAssoc($query, $shift = false){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($stack = mysql_fetch_assoc($sql)) !== false) $array[] = $stack;
  mysql_free_result($sql);
  return $shift ? array_shift($array) : $array;
}

//DB ���饪�֥������ȷ���������������������Υ�åѡ��ؿ�
function FetchObject($query, $class, $shift = false){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($stack = mysql_fetch_object($sql, $class)) !== false) $array[] = $stack;
  mysql_free_result($sql);
  return $shift ? array_shift($array) : $array;
}

//�ǡ����١�����Ͽ�Υ�åѡ��ؿ�
function InsertDatabase($table, $items, $values){
  return SendQuery("INSERT INTO {$table}({$items}) VALUES({$values})", true);
}

//�桼����Ͽ����
function InsertUser($room_no, $uname, $handle_name, $password, $user_no = 1, $icon_no = 0,
		    $profile = NULL, $sex = 'male', $role = NULL, $session_id = NULL){
  global $MESSAGE;

  $crypt_password = CryptPassword($password);
  $items = 'room_no, user_no, uname, handle_name, icon_no, sex, password, live, profile, last_words';
  $values = "{$room_no}, {$user_no}, '{$uname}', '{$handle_name}', {$icon_no}, '{$sex}', " .
    "'{$crypt_password}', 'live', ";
  if($uname == 'dummy_boy'){
    $values .= "'{$MESSAGE->dummy_boy_comment}', '{$MESSAGE->dummy_boy_last_words}'";
  }
  else{
    $ip_address = $_SERVER['REMOTE_ADDR']; //�桼����IP���ɥ쥹�����
    $items .= ', role, session_id, ip_address, last_load_day_night';
    $values .= "'$profile', '', '$role', '$session_id', '$ip_address', 'beforegame'";
  }
  return InsertDatabase('user_entry', $items, $values);
}

//-- ������Ϣ --//
//TZ �����򤫤���������֤� (�Ķ��ѿ� TZ ���ѹ��Ǥ��ʤ��Ķ����ꡩ)
function TZTime(){
  global $SERVER_CONF;

  $time = time();
  if($SERVER_CONF->adjust_time_difference) $time += $SERVER_CONF->offset_seconds;
  return $time;
  /* // �ߥ����б��Υ�����(��) 2009-08-08 enogu
     return preg_replace('/([0-9]+)( [0-9]+)?/i', '$$2.$$1', microtime()) + $SERVER_CONF->offset_seconds; // �ߥ���
     �б��Υ�����(��) 2009-08-08 enogu
  */
}

//TZ �����򤫤����������֤�
function TZDate($format, $time){
  global $SERVER_CONF;
  return $SERVER_CONF->adjust_time_difference ? gmdate($format, $time) : date($format, $time);
}

//TIMESTAMP �����λ�����Ѵ�����
function ConvertTimeStamp($time_stamp, $convert_date = true){
  global $SERVER_CONF;

  $time = strtotime($time_stamp);
  if($SERVER_CONF->adjust_time_difference) $time += $SERVER_CONF->offset_seconds;
  return $convert_date ? TZDate('Y/m/d (D) H:i:s', $time) : $time;
}

//����(��)���Ѵ�����
function ConvertTime($seconds){
  $sentence = '';
  $hours    = 0;
  $minutes  = 0;

  if($seconds >= 60){
    $minutes = floor($seconds / 60);
    $seconds %= 60;
  }
  if($minutes >= 60){
    $hours = floor($minutes / 60);
    $minutes %= 60;
  }

  if($hours   > 0) $sentence .= $hours   . '����';
  if($minutes > 0) $sentence .= $minutes . 'ʬ';
  if($seconds > 0) $sentence .= $seconds . '��';
  return $sentence;
}

//-- ʸ��������Ϣ --//
//POST���줿�ǡ�����ʸ�������ɤ����줹��
function EncodePostData(){
  global $SERVER_CONF;

  foreach($_POST as $key => $value){
    $encode = mb_detect_encoding($value, 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    if($encode != '' && $encode != $SERVER_CONF->encode){
      $_POST[$key] = mb_convert_encoding($value, $SERVER_CONF->encode, $encode);
    }
  }
}

//�ü�ʸ���Υ��������׽���
//htmlentities() ��Ȥ���ʸ�������򵯤����Ƥ��ޤ��褦�ʤΤǴ����Ƥ٤��˽���
function EscapeStrings(&$str, $trim = true){
  if(get_magic_quotes_gpc()) $str = stripslashes($str); // \ ��ư�ǤĤ���������к�
  // $str = htmlentities($str, ENT_QUOTES); //UTF �˰ܹԤ����鵡ǽ���롩
  $replace_list = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;',
			'\\' => '&yen;', '"' => '&quot;', "'" => '&#039;');
  $str = strtr($str, $replace_list);
  $str = ($trim ? trim($str) : str_replace(array("\r\n", "\r", "\n"), "\n", $str));
  return $str;
}

//���ԥ����ɤ� <br> ���Ѵ����� (nl2br() ���� <br /> �ʤΤ� HTML 4.01 �����Ը���)
function LineToBR(&$str){
  $str = str_replace("\n", '<br>', $str);
  return $str;
}

//�ѥ���ɰŹ沽
function CryptPassword($raw_password){
  global $SERVER_CONF;
  return sha1($SERVER_CONF->hash_salt . $raw_password);
}

//-- ���ϴ�Ϣ --//
//�ѿ�ɽ���ؿ� (�ǥХå���)
function PrintData($data, $name = NULL){
  $str = is_null($name) ? '' : $name . ': ';
  $str .= (is_array($data) || is_object($data)) ? print_r($data, true) : $data;
  echo $str . '<br>';
}

//�ڡ��������ѤΥ�󥯥�������Ϥ���
function OutputPageLink($url, $CONFIG, $total_count, $url_option, $reverse = NULL){
  global $RQ_ARGS;

  $page_count = ceil($total_count / $CONFIG->view);
  $start_page = $RQ_ARGS->page == 'all' ? 1 : $RQ_ARGS->page;
  if($page_count - $RQ_ARGS->page < $CONFIG->page){
    $start_page = $page_count - $CONFIG->page + 1;
    if($start_page < 1) $start_page = 1;
  }
  $end_page = $RQ_ARGS->page + $CONFIG->page - 1;
  if($end_page > $page_count) $end_page = $page_count;

  $url_stack = array('[Page]');
  $url_header = '<a href="' . $url . '.php?';

  if($page_count > $CONFIG->page && $RQ_ARGS->page > 1){
    $url_stack[] = GeneratePageLink($url_header, $url_option, 1, '[1]...');
    $url_stack[] = GeneratePageLink($url_header, $url_option, $start_page - 1, '&lt;&lt;');
  }

  for($page_number = $start_page; $page_number <= $end_page; $page_number++){
    $url_stack[] = GeneratePageLink($url_header, $url_option, $page_number);
  }

  if($page_number <= $page_count){
    $url_stack[] = GeneratePageLink($url_header, $url_option, $page_number, '&gt;&gt;');
    $url_stack[] = GeneratePageLink($url_header, $url_option, $page_count, '...[' . $page_count . ']');
  }
  $url_stack[] = GeneratePageLink($url_header, $url_option, 'all');

  if($url == 'old_log'){
    $list = $url_option;
    $list['page'] = 'page=' . $RQ_ARGS->page;
    $list['reverse'] = 'reverse=' . ($reverse ? 'off' : 'on');
    $url_stack[] = '[ɽ����]';
    $url_stack[] = ($reverse ? '������' : '�Ţ���');

    $url = $url_header . implode('&', $list) . '">';
    $url_stack[] =  $url . (($reverse xor $CONFIG->reverse) ? '�����᤹' : '�����ؤ���') . '</a>';
  }
  echo implode(' ', $url_stack);
}

//�ڡ��������ѤΥ�󥯥������������
function GeneratePageLink($url, $list, $page, $title = NULL){
  global $RQ_ARGS;
  if($page == $RQ_ARGS->page) return '[' . $page . ']';
  array_unshift($list, 'page=' . $page);
  if(is_null($title)) $title = '[' . $page . ']';
  return $url . implode('&', $list) . '">' . $title . '</a>';
}

//�����४�ץ����β����������������
function GenerateGameOptionImage($game_option, $option_role = ''){
  global $CAST_CONF, $ROOM_IMG, $GAME_OPT_MESS;

  $str = '';
  if(strpos($game_option, 'wish_role') !== false){
    $str .= $ROOM_IMG->Generate('wish_role', $GAME_OPT_MESS->wish_role);
  }
  if(strpos($game_option, 'real_time') !== false){ //�»��֤����»��֤����
    $real_time_str = strstr($game_option, 'real_time');
    sscanf($real_time_str, "real_time:%d:%d", &$day, &$night);
    $sentence = "{$GAME_OPT_MESS->real_time}���롧 $day ʬ���롧 $night ʬ";
    $str .= $ROOM_IMG->Generate('real_time', $sentence) . '['. $day . '��' . $night . ']';
  }

  $option_list = explode(' ', $game_option . ' ' . $option_role);
  //PrintData($option_list); //�ƥ�����
  $display_order_list = array('dummy_boy', 'gm_login', 'open_vote', 'not_open_cast', 'auto_open_cast',
			      'poison', 'assassin', 'boss_wolf', 'poison_wolf', 'possessed_wolf',
			      'cupid', 'medium', 'mania', 'decide', 'authority', 'liar', 'gentleman',
			      'sudden_death', 'perverseness', 'full_mania', 'quiz', 'duel',
			      'chaos', 'chaosfull', 'chaos_open_cast', 'chaos_open_cast_camp',
			      'chaos_open_cast_role', 'secret_sub_role', 'no_sub_role');

  foreach($display_order_list as $option){
    if(! in_array($option, $option_list)) continue;
    if($GAME_OPT_MESS->$option == '') continue;
    $sentence = '';
    if($option == 'cupid'){
      $sentence = '14�ͤޤ���' . $CAST_CONF->$option . '�Ͱʾ��';
    }
    elseif(is_integer($CAST_CONF->$option)){
      $sentence = $CAST_CONF->$option . '�Ͱʾ��';
    }
    $sentence .= $GAME_OPT_MESS->$option;

    $str .= $ROOM_IMG->Generate($option, $sentence);
  }

  return $str;
}

function OutputCastTable($min = 0, $max = NULL){
  global $GAME_CONF, $CAST_CONF;

  $header = '<table class="member">';
  $str = '<tr><th>���Ϳ�</th>';

  //���ꤵ��Ƥ�����̾�����
  $all_cast = array();
  foreach($CAST_CONF->role_list as $key => $value){
    if($key < $min) continue;
    $all_cast = array_merge($all_cast, array_keys($value));
    if($key == $max) break;
  }
  $all_cast = array_unique($all_cast);

  //ɽ��������
  $role_list = array_intersect(array_keys($GAME_CONF->main_role_list), $all_cast);
  foreach($role_list as $role){
    $class = 'human';
    foreach($GAME_CONF->main_role_group_list as $key => $value){
      if(strpos($role, $key) !== false){
	$class = $value;
	break;
      }
    }
    $str .= '<th class="' . $class . '">' . $GAME_CONF->main_role_list[$role] . '</th>';
  }
  $str .= '</tr>'."\n";
  echo $header . $str;

  //�Ϳ���������ɽ��
  foreach($CAST_CONF->role_list as $key => $value){
    if($key < $min) continue;
    $tag = "<td><strong>$key</strong></td>";
    foreach($role_list as $role) $tag .= '<td>' . (int)$value[$role] . '</td>';
    echo '<tr>' . $tag . '</tr>'."\n";
    if($key == $max) break;
    if($key % 20 == 0) echo $str;
  }
  echo '</table>';
}

//���� HTML �إå�����
function GenerateHTMLHeader($title, $css = 'action'){
  global $SERVER_CONF;

  $css_path = JINRO_CSS . '/' . $css . '.css';
  return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset={$SERVER_CONF->encode}">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>{$title}</title>
<link rel="stylesheet" href="{$css_path}">

EOF;
}

//���� HTML �إå�����
function OutputHTMLHeader($title, $css = 'action'){
  echo GenerateHTMLHeader($title, $css);
}

//��̥ڡ��� HTML �إå�����
function OutputActionResultHeader($title, $url = ''){
  global $ROOM;

  OutputHTMLHeader($title);
  if($url != '') echo '<meta http-equiv="Refresh" content="1;URL=' . $url . '">'."\n";
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo '</head><body>'."\n";
}

//��̥ڡ�������
function OutputActionResult($title, $body, $url = '', $unlock = false){
  global $DB_CONF;

  $DB_CONF->Disconnect($unlock); //DB ��³���

  OutputActionResultHeader($title, $url);
  echo $body . "\n";
  OutputHTMLFooter(true);
}

//HTML �եå�����
function OutputHTMLFooter($exit = false){
  global $DB_CONF;

  $DB_CONF->Disconnect(); //DB ��³���
  echo '</body></html>'."\n";
  if($exit) exit;
}

//��ͭ�ե졼�� HTML �إå�����
function OutputFrameHTMLHeader($title){
  global $SERVER_CONF;

  echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset={$SERVER_CONF->encode}">
<title>{$title}</title>
</head>

EOF;
}

//�ե졼�� HTML �եå�����
function OutputFrameHTMLFooter(){
  echo <<<EOF
<noframes><body>
�ե졼�����б��Υ֥饦�����������ѤǤ��ޤ���
</body></noframes>
</frameset></html>

EOF;
}

//��������ڡ��� HTML �إå�����
function OutputInfoPageHeader($title, $level = 0, $css = 'info'){
  global $SERVER_CONF;

  $info = $level == 0 ? './' : str_repeat('../', $level);
  $top  = str_repeat('../', $level + 1);
  OutputHTMLHeader($SERVER_CONF->title . '[' . $title . ']', $css);
  echo <<<EOF
</head>
<body>
<h1>{$title}</h1>
<p>
<a href="{$top}" target="_top">&lt;= TOP</a>
<a href="{$info}" target="_top">���������</a>
</p>

EOF;
}
