<?php
//�ǡ����١�����³
//$header : ���Ǥ� HTML�إå������Ϥ���� [���� / ���ʤ�]
//$exit   : ���顼���� [HTML ���Ĥ��� exit ���֤� / false �ǽ�λ]
function ConnectDatabase($header = false, $exit = true){
  global $DB_CONF, $ENCODE;

  //�ǡ����١��������Ф˥�������
  $db_handle = mysql_connect($DB_CONF->host, $DB_CONF->user, $DB_CONF->password);
  if($db_handle){ //������������
    mysql_set_charset('ujis');
    if(mysql_select_db($DB_CONF->name, $db_handle)){ //�ǡ����١�����³
      // mysql_query("SET NAMES utf8");
      return $db_handle; //����������ϥ�ɥ���֤��ƽ�����λ
    }
    else{
      $error_title = '�ǡ����١�����³����';
      $error_name  =$DB_CONF->name;
    }
  }
  else{
    $error_title = 'MySQL��������³����';
    $error_name  = $DB_CONF->host;
  }

  $error_message = $error_title . ': ' . $error_name; //���顼��å���������
  if($header){
    echo '<font color="#FF0000">' . $error_message . '</font><br>';
    if($exit) OutputHTMLFooter($exit);
    return false;
  }
  OutputActionResult($error_title, $error_message);
}

//�ǡ����١����Ȥ���³���Ĥ���
function DisconnectDatabase($dbHandle){
  mysql_close($dbHandle);
}

//�ǡ����١�����Ͽ�Υ�åѡ��ؿ�
function InsertDatabase($table, $items, $values){
  return mysql_query("INSERT INTO $table($items) VALUES($values)");
}

//ȯ����ǡ����١�������Ͽ���� (talk Table)
function InsertTalk($room_no, $date, $location, $uname, $time, $sentence, $font_type, $spend_time){
  $items  = 'room_no, date, location, uname, time, sentence, font_type, spend_time';
  $values = "$room_no, $date, '$location', '$uname', '$time', '$sentence', '$font_type', $spend_time";
  return InsertDatabase('talk', $items, $values);
}

//���å����ID�򿷤�������(PHP�ΥС�����󤬸Ť��Ȥ��δؿ���̵���Τ��������)
if(! function_exists('session_regenerate_id')){
  function session_regenerate_id(){
    $QQ = serialize($_SESSION);
    session_destroy();
    session_id(md5(uniqid(rand(), 1)));
    session_start();
    $_SESSION = unserialize($QQ);
  }
}

//���å����ǧ�� �֤��� OK:�桼��̾ / NG: false
function CheckSession($session_id, $exit = true){
  global $RQ_ARGS;
  // $ip_address = $_SERVER['REMOTE_ADDR']; //IP���ɥ쥹ǧ�ڤϸ��ߤϹԤäƤ��ʤ�

  //���å���� ID �ˤ��ǧ��
  $query = "SELECT uname FROM user_entry WHERE room_no = {$RQ_ARGS->room_no} " .
    "AND session_id ='$session_id' AND user_no > 0";
  $array = FetchArray($query);
  if(count($array) == 1) return $array[0];

  if($exit){ //���顼����
    OutputActionResult('���å����ǧ�ڥ��顼',
		       '���å����ǧ�ڥ��顼<br>'."\n" .
		       '<a href="index.php" target="_top">�ȥåץڡ���</a>����' .
		       '�����󤷤ʤ����Ƥ�������');
  }
  return false;
}

//DB����Ͽ����Ƥ��륻�å����ID�����ʤ��褦�ˤ���
function GetUniqSessionID(){
  //���å���󳫻�
  session_start();
  $session_id = '';

  do{
    session_regenerate_id();
    $session_id = session_id();
    $query = "SELECT COUNT(room_no) FROM user_entry, admin_manage " .
      "WHERE user_entry.session_id = '$session_id' OR admin_manage.session_id = '$session_id'";
  }while(FetchResult($query) > 0);
  return $session_id;
}

//DB �䤤��碌�����Υ�åѡ��ؿ�
function SendQuery($query){
  $sql = mysql_query($query);
  if($sql) return $sql;
  $backtrace = debug_backtrace();
  echo "SQL���顼��{$backtrace[1]['function']}()��{$backtrace[0]['line']}��$query <br>";
  return false;
}

//DB ����ñ�Τ��ͤ������������Υ�åѡ��ؿ�
function FetchResult($query){
  if(($sql = SendQuery($query)) === false) return false;
  $data = (mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false);
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

//DB ����ñ�Τ�Ϣ������������������Υ�åѡ��ؿ�
function FetchNameArray($query){
  if(($sql = SendQuery($query)) === false) return false;
  $array = (mysql_num_rows($sql) > 0 ? mysql_fetch_assoc($sql) : false);
  mysql_free_result($sql);
  return $array;
}

//DB ����Ϣ������������������Υ�åѡ��ؿ�
function FetchAssoc($query){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($assoc = mysql_fetch_assoc($sql)) !== false) $array[] = $assoc;
  mysql_free_result($sql);
  return $array;
}

//DB ���饪�֥������ȷ���������������������Υ�åѡ��ؿ�
function FetchObjectArray($query, $class){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($object = mysql_fetch_object($sql, $class)) !== false) $array[] = $object;
  mysql_free_result($sql);
  return $array;
}

//TZ �����򤫤���������֤� (�Ķ��ѿ� TZ ���ѹ��Ǥ��ʤ��Ķ����ꡩ)
function TZTime(){
  global $SERVER_CONF;
  $cur_time = time();
  if ($SERVER_CONF->adjust_time_difference) $curtime += $SERVER_CONF->offset_seconds;
  return $cur_time;
  /* // �ߥ����б��Υ�����(��) 2009-08-08 enogu
     return preg_replace('/([0-9]+)( [0-9]+)?/i', '$$2.$$1', microtime()) + $SERVER_CONF->offset_seconds; // �ߥ���
     �б��Υ�����(��) 2009-08-08 enogu
  */
}

//TIMESTAMP �����λ�����Ѵ�����
function ConvertTimeStamp($time){
  global $SERVER_CONF;

  $str = strtotime($time);
  if ($SERVER_CONF->adjust_time_difference) {
    $str += $SERVER_CONF->offset_seconds;
    return gmdate('Y/m/d (D) H:i:s', $str);
  }
  // else
  return date('Y/m/d (D) H:i:s', $str);
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

//POST���줿�ǡ�����ʸ�������ɤ����줹��
function EncodePostData(){
  global $ENCODE;

  foreach($_POST as $key => $value){
    $encode_type = mb_detect_encoding($value, 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    if($encode_type != '' && $encode_type != $ENCODE)
      $_POST[$key] = mb_convert_encoding($value, $ENCODE, $encode_type);
  }
}

//���϶ػ�ʸ���Υ����å�
function CheckForbiddenStrings($str){
  return (strstr($str, "'") || strstr($str, "\\"));
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

//�����४�ץ����β����������������
function MakeGameOptionImage($game_option, $option_role = ''){
  global $GAME_CONF, $ROOM_IMG, $MESSAGE;

  $str = '';
  if(strpos($game_option, 'wish_role') !== false){
    $str .= $ROOM_IMG->GenerateTag('wish_role', '����˾��');
  }
  if(strpos($game_option, 'real_time') !== false){ //�»��֤����»��֤����
    $real_time_str = strstr($game_option, 'real_time');
    sscanf($real_time_str, "real_time:%d:%d", &$day, &$night);
    $sentence = "�ꥢ�륿���������롧 $day ʬ���롧 $night ʬ";
    $str .= $ROOM_IMG->GenerateTag('real_time', $sentence) . '['. $day . '��' . $night . ']';
  }

  $option_list = explode(' ', $game_option . ' ' .$option_role);
  // print_r($option_list);
  $display_order_list = array('dummy_boy', 'gm_login', 'open_vote', 'not_open_cast', 'decide',
			      'authority', 'poison', 'cupid', 'boss_wolf', 'poison_wolf',
			      'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			      'perverseness', 'full_mania', 'quiz', 'duel', 'chaos', 'chaosfull',
			      'chaos_open_cast', 'chaos_open_cast_camp', 'chaos_open_cast_role',
			      'secret_sub_role', 'no_sub_role');

  foreach($display_order_list as $this_option){
    if(! in_array($this_option, $option_list)) continue;
    $this_str = 'game_option_' . $this_option;
    if($MESSAGE->$this_str == '') continue;

    $sentence = '';
    if($this_option == 'cupid'){
      $sentence = '14�ͤޤ���' . $GAME_CONF->$this_option . '�Ͱʾ��';
    }
    elseif(is_integer($GAME_CONF->$this_option)){
      $sentence = $GAME_CONF->$this_option . '�Ͱʾ��';
    }
    $sentence .= $MESSAGE->$this_str;

    $str .= $ROOM_IMG->GenerateTag($this_option, $sentence);
  }

  /*
  $text_game_option_list = array();
  foreach($text_game_option_list as $this_option){
    if(strpos($game_option, $this_option) !== false){
      $message_str = 'game_option_' . $this_option;
      $str .= '[' . $MESSAGE->$message_str . ']';
    }
  }
  */

  /*
  $text_option_role_list = array(, 'open_sub_role');
  foreach($text_option_role_list as $this_option){
    if(ereg("{$this_option}([[:space:]]+[^[[:space:]]]*)?", $option_role)){
      $message_str = 'game_option_' . $this_option;
      $str .= '[' . $MESSAGE->$message_str . ']';
    }
  }
  */

  return $str;
}

//���� HTML �إå�����
function OutputHTMLHeader($title, $css = 'action'){
  global $ENCODE, $CSS_PATH;

  $path = ($CSS_PATH == '' ? 'css' : $CSS_PATH);
  echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset={$ENCODE}">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>{$title}</title>
<link rel="stylesheet" href="{$path}/{$css}.css">

EOF;
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
  global $dbHandle;

  if($unlock) mysql_query('UNLOCK TABLES'); //��å����
  if($dbHandle != '') DisconnectDatabase($dbHandle); //DB ��³���

  OutputActionResultHeader($title, $url);
  echo $body . "\n";
  OutputHTMLFooter(true);
}

//HTML �եå�����
function OutputHTMLFooter($exit = false){
  echo '</body></html>'."\n";
  if($exit) exit;
}
?>
