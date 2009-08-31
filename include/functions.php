<?php
//mbstring���б��ξ�硢���ߥ�졼������Ѥ���
if(! extension_loaded('mbstring')){
  require_once(dirname(__FILE__) . '/../module/mb-emulator.php');
}
require_once(dirname(__FILE__) .  '/setting.php');

//�ǡ����١�����³
//$header : ���Ǥ� HTML�إå������Ϥ���� [���� / ���ʤ�]
//$exit   : ���顼���� [HTML ���Ĥ��� exit ���֤� / false �ǽ�λ]
function ConnectDatabase($header = false, $exit = true){
  global $DB_CONF;

  //�ǡ����١��������Ф˥�������
  $db_handle = mysql_connect($DB_CONF->host, $DB_CONF->user, $DB_CONF->password);
  if($db_handle){ //������������
    mysql_set_charset('ujis');
    if(mysql_select_db($DB_CONF->name, $db_handle)) //�ǡ����١�����³
      return $db_handle; //����������ϥ�ɥ���֤��ƽ�����λ
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

//ȯ���� DB ����Ͽ���� (talk Table)
function InsertTalk($room_no, $date, $location, $uname, $time, $sentence, $font_type, $spend_time){
  mysql_query("INSERT INTO talk(room_no, date, location, uname, time,
				sentence, font_type, spend_time)
		VALUES($room_no, $date, '$location', '$uname', '$time',
				'$sentence', '$font_type', $spend_time)");
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

//DB ����ñ�Τ��ͤ������������Υ�åѡ��ؿ�
function FetchResult($query){
  $sql = mysql_query($query);
  return (mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false);
}

//DB ���鳺������ǡ����ιԿ��������������Υ�åѡ��ؿ�
function FetchCount($query){
  return mysql_num_rows(mysql_query($query));
}

//DB ����켡��������������������Υ�åѡ��ؿ�
function FetchArray($query){
  $array = array();
  $sql   = mysql_query($query);
  $count = mysql_num_rows($sql);
  for($i = 0; $i < $count; $i++) array_push($array, mysql_result($sql, $i, 0));
  return $array;
}

//DB ����ñ�Τ�Ϣ������������������Υ�åѡ��ؿ�
function FetchNameArray($query){
  $sql = mysql_query($query);
  return (mysql_num_rows($sql) > 0 ? mysql_fetch_assoc($sql) : false);
}

//DB ����Ϣ������������������Υ�åѡ��ؿ�
function FetchAssoc($query){
  $array = array();
  $sql   = mysql_query($query);
  while(($this_array = mysql_fetch_assoc($sql)) !== false) array_push($array, $this_array);
  return  $array;
}

//DB ���饪�֥������ȷ���������������������Υ�åѡ��ؿ�
function FetchObjectArray($query, $class){
  $array = array();
  $sql   = mysql_query($query);
  while(($user = mysql_fetch_object($sql, $class)) !== false) array_push($array, $user);
  return $array;
}

//TZ �����򤫤���������֤� (�Ķ��ѿ� TZ ���ѹ��Ǥ��ʤ��Ķ����ꡩ)
function TZTime(){
  global $SERVER_CONF;
  return time() + $SERVER_CONF->offset_seconds;
  /* // �ߥ����б��Υ�����(��) 2009-08-08 enogu
     return preg_replace('/([0-9]+)( [0-9]+)?/i', '$$2.$$1', microtime()) + $SERVER_CONF->offset_seconds; // �ߥ���
     �б��Υ�����(��) 2009-08-08 enogu
  */
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
			      'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

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
