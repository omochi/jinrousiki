<?php
//-- ¥Ç¡¼¥¿¥Ù¡¼¥¹½èÍý¤Î´ðÄì¥¯¥é¥¹ --//
class DatabaseConfigBase{
  //¥Ç¡¼¥¿¥Ù¡¼¥¹ÀÜÂ³
  /*
    $header : HTML¥Ø¥Ã¥À½ÐÎÏ¾ðÊó [true: ½ÐÎÏºÑ¤ß / false: Ì¤½ÐÎÏ]
    $exit   : ¥¨¥é¡¼½èÍý [true: exit ¤òÊÖ¤¹ / false ¤Ç½ªÎ»]
  */
  function Connect($header = false, $exit = true){
    //¥Ç¡¼¥¿¥Ù¡¼¥¹¥µ¡¼¥Ð¤Ë¥¢¥¯¥»¥¹
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //¥¢¥¯¥»¥¹À®¸ù
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //¥Ç¡¼¥¿¥Ù¡¼¥¹ÀÜÂ³
	//mysql_query("SET NAMES utf8");
	//À®¸ù¤·¤¿¤é¥Ï¥ó¥É¥ë¤òÊÖ¤·¤Æ½èÍý½ªÎ»
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = '¥Ç¡¼¥¿¥Ù¡¼¥¹ÀÜÂ³¼ºÇÔ';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQL¥µ¡¼¥ÐÀÜÂ³¼ºÇÔ';
      $error_name  = $this->host;
    }

    $error_message = $error_title . ': ' . $error_name; //¥¨¥é¡¼¥á¥Ã¥»¡¼¥¸ºîÀ®
    if($header){
      echo '<font color="#FF0000">' . $error_message . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($error_title, $error_message);
  }

  //¥Ç¡¼¥¿¥Ù¡¼¥¹¤È¤ÎÀÜÂ³¤òÊÄ¤¸¤ë
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) UnlockTable(); //¥í¥Ã¥¯²ò½ü
    mysql_close($this->db_handle);
    unset($this->db_handle); //¥Ï¥ó¥É¥ë¤ò¥¯¥ê¥¢
  }
}

//-- ¥»¥Ã¥·¥ç¥ó´ÉÍý¥¯¥é¥¹ --//
class Session{
  var $id;
  var $user_no;

  function Session(){ $this->__construct(); }
  function __construct(){
    session_start();
    $this->Set();
  }

  //ID ¥»¥Ã¥È
  function Set(){
    $this->id = session_id();
    return $this->id;
  }

  //ID ¥ê¥»¥Ã¥È
  function Reset(){
    //PHP ¤Î¥Ð¡¼¥¸¥ç¥ó¤¬¸Å¤¤¾ì¹ç¤Ï´Ø¿ô¤¬¤Ê¤¤¤Î¤Ç¼«Á°¤Ç½èÍý¤¹¤ë
    if(function_exists('session_regenerate_id')){
      session_regenerate_id();
    }
    else{
      $id = serialize($_SESSION);
      session_destroy();
      session_id(md5(uniqid(rand(), 1)));
      session_start();
      $_SESSION = unserialize($id);
    }
    return $this->Set();
  }

  //ID ¼èÆÀ
  function Get($uniq = false){
    return $uniq ? $this->GetUniq() : $this->id;
  }

  //DB ¤ËÅÐÏ¿¤µ¤ì¤Æ¤¤¤ë¥»¥Ã¥·¥ç¥ó ID ¤ÈÈï¤é¤Ê¤¤¤è¤¦¤Ë¤¹¤ë
  function GetUniq(){
    $query = 'SELECT COUNT(room_no) FROM user_entry WHERE session_id = ';
    do{
      $this->Reset();
    }while(FetchResult($query ."'{$this->id}'") > 0);
    return $this->id;
  }

  //Ç§¾Ú¤·¤¿¥æ¡¼¥¶¤Î ID ¼èÆÀ
  function GetUser(){
    return $this->user_no;
  }

  //Ç§¾Ú
  function Certify($exit = true){
    global $RQ_ARGS;
    //$ip_address = $_SERVER['REMOTE_ADDR']; //IP¥¢¥É¥ì¥¹Ç§¾Ú¤Ï¸½ºß¤Ï¹Ô¤Ã¤Æ¤¤¤Ê¤¤

    //¥»¥Ã¥·¥ç¥ó ID ¤Ë¤è¤ëÇ§¾Ú
    $query = "SELECT user_no FROM user_entry WHERE room_no = {$RQ_ARGS->room_no} " .
      "AND session_id ='{$this->id}' AND user_no > 0";
    $array = FetchArray($query);
    if(count($array) == 1){
      $this->user_no = $array[0];
      return true;
    }

    if($exit){ //¥¨¥é¡¼½èÍý
      $title = '¥»¥Ã¥·¥ç¥óÇ§¾Ú¥¨¥é¡¼';
      $sentence = $title . '¡§<a href="./" target="_top">¥È¥Ã¥×¥Ú¡¼¥¸</a>¤«¤é' .
	'¥í¥°¥¤¥ó¤·¤Ê¤ª¤·¤Æ¤¯¤À¤µ¤¤';
      OutputActionResult($title, $sentence);
    }
    return false;
  }
}

//-- ¥¯¥Ã¥­¡¼¥Ç¡¼¥¿¤Î¥í¡¼¥É½èÍý --//
class CookieDataSet{
  var $day_night;  //ÌëÌÀ¤±
  var $vote_times; //ÅêÉ¼²ó¿ô
  var $objection;  //¡Ö°ÛµÄ¤¢¤ê¡×¤Î¾ðÊó

  function CookieDataSet(){ $this->__construct(); }
  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- ³°Éô¥ê¥ó¥¯À¸À®¤Î´ðÄì¥¯¥é¥¹ --//
class ExternalLinkBuilder{
  //¥µ¡¼¥ÐÄÌ¿®¾õÂÖ¥Á¥§¥Ã¥¯
  function CheckConnection($url){
    $url_stack = explode('/', $url);
    $this->host = $url_stack[2];
    $io = @fsockopen($this->host, 80, $err_no, $err_str, 3);
    if(! $io) return false;

    stream_set_timeout($io, 3);
    fwrite($io, "GET / HTTP/1.1\r\nHost: {$host}\r\nConnection: Close\r\n\r\n");
    $data = fgets($io, 128);
    $stream_stack = stream_get_meta_data($io);
    fclose($io);
    //PrintData($data, 'Connection');
    return ! $stream_stack['timed_out'];
  }

  //HTML ¥¿¥°À¸À®
  function Generate($title, $data){
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  //BBS ¥ê¥ó¥¯À¸À®
  function GenerateBBS($data){
    $title = '<a href="' . $this->view_url . $this->thread . 'l50' . '">¹ðÃÎ¥¹¥ì¥Ã¥É¾ðÊó</a>';
    return $this->Generate($title, $data);
  }

  //³°ÉôÂ¼¥ê¥ó¥¯À¸À®
  function GenerateSharedServerRoom($name, $url, $data){
    return $this->Generate('¥²¡¼¥à°ìÍ÷ (<a href="' . $url . '">' . $name . '</a>)', $data);
  }
}

//-- ·Ç¼¨ÈÄ¾ðÊó¼èÆÀ¤Î´ðÄì¥¯¥é¥¹ --//
class BBSConfigBase extends ExternalLinkBuilder{
  function Output(){
    global $SERVER_CONF;

    if($this->disable) return;
    if(! $this->CheckConnection($this->raw_url)){
      echo $this->GenerateBBS($this->host . ': Connection timed out (3 seconds)');
      return;
    }

    //¥¹¥ì¥Ã¥É¾ðÊó¤ò¼èÆÀ
    $url = $this->raw_url . $this->thread . 'l' . $this->size . 'n';
    if(($data = @file_get_contents($url)) == '') return;
    //PrintData($data, 'Data'); //¥Æ¥¹¥ÈÍÑ
    if($this->encode != $SERVER_CONF->encode){
      $data = mb_convert_encoding($data, $SERVER_CONF->encode, $this->encode);
    }
    $str = '';
    $str_stack = explode("\n", $data);
    array_pop($str_stack);
    foreach($str_stack as $res){
      $res_stack = explode('<>', $res);
      $str .= '<dt>' . $res_stack[0] . ' : <font color="#008800"><b>' . $res_stack[1] .
	'</b></font> : ' . $res_stack[3] . ' ID : ' . $res_stack[6] . '</dt>' . "\n" .
	'</dt><dd>' . $res_stack[4] . '</dd>';
    }
    echo $this->GenerateBBS($str);
  }
}

//-- ¥æ¡¼¥¶¥¢¥¤¥³¥ó´ÉÍý¤Î´ðÄì¥¯¥é¥¹ --//
class UserIconBase{
  // ¥¢¥¤¥³¥ó¤ÎÊ¸»ú¿ô
  function IconNameMaxLength(){
    return 'È¾³Ñ¤Ç' . $this->name . 'Ê¸»ú¡¢Á´³Ñ¤Ç' . floor($this->name / 2) . 'Ê¸»ú¤Þ¤Ç';
  }

  // ¥¢¥¤¥³¥ó¤Î¥Õ¥¡¥¤¥ë¥µ¥¤¥º
  function IconFileSizeMax(){
    return ($this->size > 1024 ? floor($this->size / 1024) . 'k' : $this->size) . 'Byte ¤Þ¤Ç';
  }

  // ¥¢¥¤¥³¥ó¤Î½Ä²£¤Î¥µ¥¤¥º
  function IconSizeMax(){
    return 'Éý' . $this->width . '¥Ô¥¯¥»¥ë ¡ß ¹â¤µ' . $this->height . '¥Ô¥¯¥»¥ë¤Þ¤Ç';
  }
}

//-- ²èÁü´ÉÍý¤Î´ðÄì¥¯¥é¥¹ --//
class ImageManager{
  function Generate($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . JINRO_IMG . '/' . $this->path . '/' . $name . '.' . $this->extension . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }

  function Output($name){
    echo $this->Generate($name) . "<br>\n";
  }
}

//-- ¾¡Íø¿Ø±Ä¤Î²èÁü½èÍý¤Î´ðÄì¥¯¥é¥¹ --//
class VictoryImageBase extends ImageManager{
  function Generate($name){
    switch($name){
    case 'human':
      $alt = 'Â¼¿Í¾¡Íø';
      break;

    case 'wolf':
      $alt = '¿ÍÏµ¾¡Íø';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = 'ÍÅ¸Ñ¾¡Íø';
      break;

    case 'lovers':
      $alt = 'Îø¿Í¾¡Íø';
      break;

    case 'quiz':
      $alt = '½ÐÂê¼Ô¾¡Íø';
      break;

    case 'vampire':
      $alt = 'µÛ·ìµ´¾¡Íø';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '°ú¤­Ê¬¤±';
      break;

    default:
      return '-';
      break;
    }
    return parent::Generate($name, $alt);
  }
}

//-- ¥á¥Ë¥å¡¼¥ê¥ó¥¯É½¼¨ÍÑ¤Î´ðÄì¥¯¥é¥¹ --//
class MenuLinkConfigBase{
  //¸òÎ®ÍÑ¥µ¥¤¥ÈÉ½¼¨
  function Output(){
    //½é´ü²½½èÍý
    $this->str = '';
    $this->header = '<li>';
    $this->footer = "</li>\n";

    $this->AddHeader('¸òÎ®ÍÑ¥µ¥¤¥È');
    $this->AddLink($this->list);
    $this->AddFooter();

    if(count($this->add_list) > 0){
      $this->AddHeader('³°Éô¥ê¥ó¥¯');
      foreach($this->add_list as $group => $list){
	$this->str .= $this->header . $group . $this->footer;
	$this->AddLink($list);
      }
      $this->AddFooter();
    }
    echo $this->str;
  }

  //¥Ø¥Ã¥ÀÄÉ²Ã
  function AddHeader($title){
    $this->str .= '<div class="menu">' . $title . "</div>\n<ul>\n";
  }

  //¥ê¥ó¥¯À¸À®
  function AddLink($list){
    $header = $this->header . '<a href="';
    $footer = '</a>' . $this->footer;
    foreach($list as $name => $url) $this->str .= $header . $url . '">' . $name . $footer;
  }

  //¥Õ¥Ã¥¿ÄÉ²Ã
  function AddFooter(){
    $this->str .= "</ul>\n";
  }
}

//-- Copyright É½¼¨ÍÑ¤Î´ðÄì¥¯¥é¥¹ --//
class CopyrightConfigBase{
  //Åê¹Æ½èÍý
  function Output(){
    $stack = $this->list;
    foreach($this->add_list as $class => $list){
      $stack[$class] = array_key_exists($class, $stack) ?
	array_merge($stack[$class], $list) : $list;
    }

    foreach($stack as $class => $list){
      $str = '<h2>' . $class . '</h2>'."\n";
      foreach($list as $name => $url){
	$str .= '<a href="' . $url . '">' . $name . '</a><br>'."\n";
      }
      echo $str;
    }
  }
}

//-- ²»¸»½èÍý¤Î´ðÄì¥¯¥é¥¹ --//
class SoundBase{
  //²»¤òÌÄ¤é¤¹
  function Output($type, $loop = false){
    $path = JINRO_ROOT . '/' . $this->path . '/' . $this->$type . '.' . $this->extension;
    if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

    echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$path}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$path}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
  }
}

//-- Twitter Åê¹ÆÍÑ¤Î´ðÄì¥¯¥é¥¹ --//
class TwitterConfigBase{
  //Åê¹Æ½èÍý
  function Send($id, $name, $comment){
    if($this->disable) return;
    require_once(JINRO_MOD . '/twitter/Twitter.php'); //¥é¥¤¥Ö¥é¥ê¤ò¥í¡¼¥É

    $message = "¡Ú{$this->server}¡Û{$id}ÈÖÃÏ¤Ë{$name}Â¼\n¡Á{$comment}¡Á ¤¬·ú¤Á¤Þ¤·¤¿";
    if(strlen($this->hash) > 0) $message .= " #{$this->hash}";
    $st =& new Services_Twitter($this->user, $this->password);
    if($st->setUpdate(mb_convert_encoding($message, 'UTF-8', 'auto'))) return;

    //¥¨¥é¡¼½èÍý
    $sentence = 'Twitter ¤Ø¤ÎÅê¹Æ¤Ë¼ºÇÔ¤·¤Þ¤·¤¿¡£<br>'."\n" .
      '¥æ¡¼¥¶Ì¾¡§' . $this->user . '<br>'."\n" . '¥á¥Ã¥»¡¼¥¸¡§' . $message;
    PrintData($sentence);
  }
}

//-- ¥Ú¡¼¥¸Á÷¤ê¥ê¥ó¥¯À¸À®¥¯¥é¥¹ --//
class PageLinkBuilder{
  function PageLinkBuilder($file, $page, $count, $config, $title = 'Page', $type = 'page'){
    $this->__construct($file, $page, $count, $config, $title, $type);
  }
  function __construct($file, $page, $count, $config, $title = 'Page', $type = 'page'){
    $this->view_total = $count;
    $this->view_page  = $config->page;
    $this->view_count = $config->view;
    $this->reverse    = $config->reverse;

    $this->file   = $file;
    $this->url    = '<a href="' . $file . '.php?';
    $this->title  = $title;
    $this->type   = $type;
    $this->option = array();
    $this->SetPage($page);
  }

  //É½¼¨¤¹¤ë¥Ú¡¼¥¸¤Î¥¢¥É¥ì¥¹¤ò¥»¥Ã¥È
  function SetPage($page){
    $total = ceil($this->view_total / $this->view_count);
    $start = $page == 'all' ? 1 : $page;
    if($total - $start < $this->view_page){ //»Ä¤ê¥Ú¡¼¥¸¤¬¾¯¤Ê¤¤¾ì¹ç¤ÏÉ½¼¨³«»Ï°ÌÃÖ¤ò¤º¤é¤¹
      $start = $total - $this->view_page + 1;
      if($start < 1) $start = 1;
    }
    $end = $start + $this->view_page - 1;
    if($end > $total) $end = $total;

    $this->page->set   = $page;
    $this->page->total = $total;
    $this->page->start = $start;
    $this->page->end   = $end;

    $this->limit = $page == 'all' ? '' : $this->view_count * ($page - 1);
    $this->query = $page == 'all' ? '' : sprintf(' LIMIT %d, %d', $this->limit, $this->view_count);
  }

  //¥ª¥×¥·¥ç¥ó¤òÄÉ²Ã¤¹¤ë
  function AddOption($type, $value = 'on'){
    $this->option[$type] = $type . '=' . $value;
  }

  //¥Ú¡¼¥¸Á÷¤êÍÑ¤Î¥ê¥ó¥¯¥¿¥°¤òºîÀ®¤¹¤ë
  function Generate($page, $title = NULL, $force = false){
    if($page == $this->page->set && ! $force) return '[' . $page . ']';
    $list = $this->option;
    array_unshift($list, $this->type . '=' . $page);
    if(is_null($title)) $title = '[' . $page . ']';
    return $this->url . implode('&', $list) . '">' . $title . '</a>';
  }

  //¥Ú¡¼¥¸¥ê¥ó¥¯¤ò½ÐÎÏ¤¹¤ë
  function Output(){
    $url_stack = array('[' . $this->title . ']');
    if($this->page->start > 1 && $this->page->total > $this->view_page){
      $url_stack[] = $this->Generate(1, '[1]...');
      $url_stack[] = $this->Generate($this->page->start - 1, '&lt;&lt;');
    }

    for($i = $this->page->start; $i <= $this->page->end; $i++){
      $url_stack[] = $this->Generate($i);
    }

    if($this->page->end < $this->page->total){
      $url_stack[] = $this->Generate($this->page->end + 1, '&gt;&gt;');
      $url_stack[] = $this->Generate($this->page->total, '...[' . $this->page->total . ']');
    }
    $url_stack[] = $this->Generate('all');

    if($this->file == 'old_log'){
      $this->AddOption('reverse', $this->set_reverse ? 'off' : 'on');
      $url_stack[] = '[É½¼¨½ç]';
      $url_stack[] = $this->set_reverse ? '¿·¢­¸Å' : '¸Å¢­¿·';
      $name = ($this->set_reverse xor $this->reverse) ? '¸µ¤ËÌá¤¹' : 'Æþ¤ìÂØ¤¨¤ë';
      $url_stack[] =  $this->Generate($this->page->set, $name, true);
    }
    echo $this->header . implode(' ', $url_stack) . $this->footer;
  }
}

//-- Ìò¿¦¥Ç¡¼¥¿¥Ù¡¼¥¹ --//
class RoleData{
  //-- Ìò¿¦Ì¾¤ÎËÝÌõ --//
  //¥á¥¤¥óÌò¿¦¤Î¥ê¥¹¥È (¥³¡¼¥ÉÌ¾ => É½¼¨Ì¾)
  //½éÆü¤ÎÌò¿¦ÄÌÃÎ¥ê¥¹¥È¤Ï¤³¤Î½çÈÖ¤ÇÉ½¼¨¤µ¤ì¤ë
  var $main_role_list = array(
    'human'              => 'Â¼¿Í',
    'elder'              => 'Ä¹Ï·',
    'saint'              => 'À»½÷',
    'executor'           => '¼¹¹Ô¼Ô',
    'escaper'            => 'Æ¨Ë´¼Ô',
    'suspect'            => 'ÉÔ¿³¼Ô',
    'unconscious'        => 'Ìµ°Õ¼±',
    'mage'               => 'Àê¤¤»Õ',
    'soul_mage'          => 'º²¤ÎÀê¤¤»Õ',
    'psycho_mage'        => 'Àº¿À´ÕÄê»Î',
    'sex_mage'           => '¤Ò¤è¤³´ÕÄê»Î',
    'stargazer_mage'     => 'ÀêÀ±½Ñ»Õ',
    'voodoo_killer'      => '±¢ÍÛ»Õ',
    'dummy_mage'         => 'Ì´¸«¿Í',
    'necromancer'        => 'ÎîÇ½¼Ô',
    'soul_necromancer'   => '±À³°¶À',
    'yama_necromancer'   => 'ïåËâ',
    'dummy_necromancer'  => 'Ì´Ëí¿Í',
    'medium'             => 'Öà½÷',
    'seal_medium'        => 'Éõ°õ»Õ',
    'revive_medium'      => 'É÷½Ë',
    'priest'             => '»Êº×',
    'bishop_priest'      => '»Ê¶µ',
    'border_priest'      => '¶­³¦»Õ',
    'crisis_priest'      => 'ÍÂ¸À¼Ô',
    'revive_priest'      => 'Å·¿Í',
    'guard'              => '¼í¿Í',
    'hunter_guard'       => 'ÎÄ»Õ',
    'blind_guard'        => 'Ìë¿ý',
    'poison_guard'       => 'µ³»Î',
    'fend_guard'         => 'Ç¦¼Ô',
    'reporter'           => '¥Ö¥ó²°',
    'anti_voodoo'        => 'Ìñ¿À',
    'dummy_guard'        => 'Ì´¼é¿Í',
    'common'             => '¶¦Í­¼Ô',
    'detective_common'   => 'ÃµÄå',
    'trap_common'        => 'ºö»Î',
    'ghost_common'       => 'Ë´Îî¾î',
    'dummy_common'       => 'Ì´¶¦Í­¼Ô',
    'poison'             => 'ËäÆÇ¼Ô',
    'strong_poison'      => '¶¯ÆÇ¼Ô',
    'incubate_poison'    => 'ÀøÆÇ¼Ô',
    'guide_poison'       => 'Í¶ÆÇ¼Ô',
    'chain_poison'       => 'Ï¢ÆÇ¼Ô',
    'dummy_poison'       => 'Ì´ÆÇ¼Ô',
    'poison_cat'         => 'Ç­Ëô',
    'revive_cat'         => 'ÀçÃ¬',
    'sacrifice_cat'      => 'Ç­¿À',
    'pharmacist'         => 'Ìô»Õ',
    'cure_pharmacist'    => '²ÏÆ¸',
    'assassin'           => '°Å»¦¼Ô',
    'doom_assassin'      => '»à¿À',
    'reverse_assassin'   => 'È¿º²»Õ',
    'soul_assassin'      => 'ÄÔ»Â¤ê',
    'eclipse_assassin'   => '¿ª°Å»¦¼Ô',
    'mind_scanner'       => '¤µ¤È¤ê',
    'evoke_scanner'      => '¥¤¥¿¥³',
    'whisper_scanner'    => 'ÓñÁûÎî',
    'howl_scanner'       => 'ËÊÁûÎî',
    'telepath_scanner'   => 'Ç°ÁûÎî',
    'jealousy'           => '¶¶É±',
    'poison_jealousy'    => 'ÆÇ¶¶É±',
    'doll'               => '¾å³¤¿Í·Á',
    'friend_doll'        => 'Ê©ÍöÀ¾¿Í·Á',
    'poison_doll'        => 'ÎëÍö¿Í·Á',
    'doom_doll'          => 'Ë©Íé¿Í·Á',
    'doll_master'        => '¿Í·Á¸¯¤¤',
    'wolf'               => '¿ÍÏµ',
    'boss_wolf'          => 'ÇòÏµ',
    'gold_wolf'          => '¶âÏµ',
    'phantom_wolf'       => '¸¸Ïµ',
    'cursed_wolf'        => '¼öÏµ',
    'wise_wolf'          => '¸­Ïµ',
    'poison_wolf'        => 'ÆÇÏµ',
    'resist_wolf'        => '¹³ÆÇÏµ',
    'blue_wolf'          => 'ÁóÏµ',
    'emerald_wolf'       => '¿éÏµ',
    'sex_wolf'           => '¿÷Ïµ',
    'tongue_wolf'        => 'Àå²ÒÏµ',
    'possessed_wolf'     => 'ØáÏµ',
    'hungry_wolf'        => '²îÏµ',
    'sirius_wolf'        => 'Å·Ïµ',
    'elder_wolf'         => '¸ÅÏµ',
    'cute_wolf'          => 'Ë¨Ïµ',
    'scarlet_wolf'       => '¹ÈÏµ',
    'silver_wolf'        => '¶äÏµ',
    'mad'                => '¶¸¿Í',
    'fanatic_mad'        => '¶¸¿®¼Ô',
    'whisper_mad'        => 'Óñ¤­¶¸¿Í',
    'jammer_mad'         => '·îÅÆ',
    'voodoo_mad'         => '¼ö½Ñ»Õ',
    'corpse_courier_mad' => '²Ð¼Ö',
    'agitate_mad'        => 'ÀðÆ°¼Ô',
    'miasma_mad'         => 'ÅÚÃØéá',
    'dream_eater_mad'    => 'àÓ',
    'trap_mad'           => 'æ«»Õ',
    'possessed_mad'      => '¸¤¿À',
    'fox'                => 'ÍÅ¸Ñ',
    'white_fox'          => 'Çò¸Ñ',
    'black_fox'          => '¹õ¸Ñ',
    'gold_fox'           => '¶â¸Ñ',
    'phantom_fox'        => '¸¸¸Ñ',
    'poison_fox'         => '´É¸Ñ',
    'blue_fox'           => 'Áó¸Ñ',
    'emerald_fox'        => '¿é¸Ñ',
    'voodoo_fox'         => '¶åÈø',
    'revive_fox'         => 'Àç¸Ñ',
    'possessed_fox'      => 'Øá¸Ñ',
    'cursed_fox'         => 'Å·¸Ñ',
    'elder_fox'          => '¸Å¸Ñ',
    'cute_fox'           => 'Ë¨¸Ñ',
    'scarlet_fox'        => '¹È¸Ñ',
    'silver_fox'         => '¶ä¸Ñ',
    'child_fox'          => '»Ò¸Ñ',
    'sex_fox'            => '¿÷¸Ñ',
    'stargazer_fox'      => 'À±¸Ñ',
    'jammer_fox'         => '·î¸Ñ',
    'miasma_fox'         => 'êµ¸Ñ',
    'cupid'              => '¥­¥å¡¼¥Ô¥Ã¥É',
    'self_cupid'         => 'µá°¦¼Ô',
    'moon_cupid'         => '¤«¤°¤äÉ±',
    'mind_cupid'         => '½÷¿À',
    'triangle_cupid'     => '¾®°­Ëâ',
    'angel'              => 'Å·»È',
    'rose_angel'         => 'é¬é¯Å·»È',
    'lily_angel'         => 'É´¹çÅ·»È',
    'exchange_angel'     => 'º²°Ü»È',
    'ark_angel'          => 'ÂçÅ·»È',
    'quiz'               => '½ÐÂê¼Ô',
    'vampire'            => 'µÛ·ìµ´',
    'chiroptera'         => 'éþéõ',
    'poison_chiroptera'  => 'ÆÇéþéõ',
    'cursed_chiroptera'  => '¼öéþéõ',
    'boss_chiroptera'    => 'Âçéþéõ',
    'elder_chiroptera'   => '¸Åéþéõ',
    'dummy_chiroptera'   => 'Ì´µá°¦¼Ô',
    'fairy'              => 'ÍÅÀº',
    'spring_fairy'       => '½ÕÍÅÀº',
    'summer_fairy'       => '²ÆÍÅÀº',
    'autumn_fairy'       => '½©ÍÅÀº',
    'winter_fairy'       => 'ÅßÍÅÀº',
    'flower_fairy'       => '²ÖÍÅÀº',
    'star_fairy'         => 'À±ÍÅÀº',
    'sun_fairy'          => 'ÆüÍÅÀº',
    'moon_fairy'         => '·îÍÅÀº',
    'grass_fairy'        => 'ÁðÍÅÀº',
    'light_fairy'        => '¸÷ÍÅÀº',
    'dark_fairy'         => '°ÇÍÅÀº',
    'mirror_fairy'       => '¶ÀÍÅÀº',
    'mania'              => '¿ÀÏÃ¥Þ¥Ë¥¢',
    'trick_mania'        => '´ñ½Ñ»Õ',
    'soul_mania'         => '³ÐÀÃ¼Ô',
    'unknown_mania'      => 'ó¬',
    'dummy_mania'        => 'Ì´¸ìÉô');

  //¥µ¥ÖÌò¿¦¤Î¥ê¥¹¥È (¥³¡¼¥ÉÌ¾ => É½¼¨Ì¾)
  //½éÆü¤ÎÌò¿¦ÄÌÃÎ¥ê¥¹¥È¤Ï¤³¤Î½çÈÖ¤ÇÉ½¼¨¤µ¤ì¤ë
  var $sub_role_list = array(
    'chicken'          => '¾®¿´¼Ô',
    'rabbit'           => '¥¦¥µ¥®',
    'perverseness'     => 'Å·¼Ùµ´',
    'flattery'         => '¥´¥Þ¤¹¤ê',
    'impatience'       => 'Ã»µ¤',
    'celibacy'         => 'ÆÈ¿Èµ®Â²',
    'nervy'            => '¼«¿®²È',
    'androphobia'      => 'ÃËÀ­¶²ÉÝ¾É',
    'gynophobia'       => '½÷À­¶²ÉÝ¾É',
    'febris'           => 'Ç®ÉÂ',
    'death_warrant'    => '»à¤ÎÀë¹ð',
    'panelist'         => '²òÅú¼Ô',
    'liar'             => 'Ïµ¾¯Ç¯',
    'invisible'        => '¸÷³ØÌÂºÌ',
    'rainbow'          => 'Æú¿§ÌÂºÌ',
    'weekly'           => '¼·ÍËÌÂºÌ',
    'grassy'           => 'Áð¸¶ÌÂºÌ',
    'side_reverse'     => '¶ÀÌÌÌÂºÌ',
    'line_reverse'     => 'Å·ÃÏÌÂºÌ',
    'gentleman'        => '¿Â»Î',
    'lady'             => '½Ê½÷',
    'actor'            => 'Ìò¼Ô',
    'authority'        => '¸¢ÎÏ¼Ô',
    'critical_voter'   => '²ñ¿´',
    'random_voter'     => 'µ¤Ê¬²°',
    'rebel'            => 'È¿µÕ¼Ô',
    'watcher'          => 'Ëµ´Ñ¼Ô',
    'decide'           => '·èÄê¼Ô',
    'plague'           => '±ÖÉÂ¿À',
    'good_luck'        => '¹¬±¿',
    'bad_luck'         => 'ÉÔ±¿',
    'upper_luck'       => '»¨Áðº²',
    'downer_luck'      => '°ìÈ¯²°',
    'star'             => '¿Íµ¤¼Ô',
    'disfavor'         => 'ÉÔ¿Íµ¤',
    'critical_luck'    => 'ÄËº¨',
    'random_luck'      => 'ÇÈÍðËü¾æ',
    'strong_voice'     => 'ÂçÀ¼',
    'normal_voice'     => 'ÉÔ´ïÍÑ',
    'weak_voice'       => '¾®À¼',
    'upper_voice'      => '¥á¥¬¥Û¥ó',
    'downer_voice'     => '¥Þ¥¹¥¯',
    'inside_voice'     => 'ÆâÊÛ·Ä',
    'outside_voice'    => '³°ÊÛ·Ä',
    'random_voice'     => '²²ÉÂ¼Ô',
    'no_last_words'    => 'É®ÉÔÀº',
    'blinder'          => 'ÌÜ±£¤·',
    'earplug'          => '¼ªÀò',
    'speaker'          => '¥¹¥Ô¡¼¥«¡¼',
    'whisper_ringing'  => 'Óñ¼ªÌÄ',
    'howl_ringing'     => 'ËÊ¼ªÌÄ',
    'deep_sleep'       => 'Çú¿ç¼Ô',
    'silent'           => 'Ìµ¸ý',
    'mower'            => 'Áð´¢¤ê',
    'mind_read'        => '¥µ¥È¥é¥ì',
    'mind_open'        => '¸ø³«¼Ô',
    'mind_receiver'    => '¼õ¿®¼Ô',
    'mind_friend'      => '¶¦ÌÄ¼Ô',
    'mind_sympathy'    => '¶¦´¶¼Ô',
    'mind_evoke'       => '¸ý´ó¤»',
    'mind_lonely'      => '¤Ï¤°¤ì¼Ô',
    'lovers'           => 'Îø¿Í',
    'challenge_lovers' => 'ÆñÂê',
    'infected'         => '´¶À÷¼Ô',
    'copied'           => '¸µ¿ÀÏÃ¥Þ¥Ë¥¢',
    'copied_trick'     => '¸µ´ñ½Ñ»Õ',
    'copied_soul'      => '¸µ³ÐÀÃ¼Ô',
    'copied_teller'    => '¸µÌ´¸ìÉô',
    'lost_ability'     => 'Ç½ÎÏÁÓ¼º');

  //Ìò¿¦¤Î¾ÊÎ¬Ì¾ (²áµî¥í¥°ÍÑ)
  var $short_role_list = array(
    'human'              => 'Â¼',
    'elder'              => 'Ï·',
    'saint'              => 'À»',
    'executor'           => '¼¹',
    'escaper'            => 'Æ¨',
    'suspect'            => 'ÉÔ¿³',
    'unconscious'        => 'Ìµ',
    'mage'               => 'Àê',
    'soul_mage'          => 'º²',
    'psycho_mage'        => '¿´Àê',
    'sex_mage'           => '¿÷Àê',
    'stargazer_mage'     => 'À±Àê',
    'voodoo_killer'      => '±¢ÍÛ',
    'dummy_mage'         => 'Ì´¸«',
    'necromancer'        => 'Îî',
    'soul_necromancer'   => '±À',
    'yama_necromancer'   => 'ïå',
    'dummy_necromancer'  => 'Ì´Ëí',
    'medium'             => 'Öà',
    'seal_medium'        => 'Éõ',
    'revive_medium'      => 'É÷',
    'priest'             => '»Êº×',
    'bishop_priest'      => '»Ê¶µ',
    'border_priest'      => '¶­',
    'crisis_priest'      => 'ÍÂ',
    'revive_priest'      => 'Å·¿Í',
    'guard'              => '¼í',
    'hunter_guard'       => 'ÎÄ',
    'blind_guard'        => '¿ý',
    'poison_guard'       => 'µ³',
    'fend_guard'         => 'Ç¦',
    'reporter'           => 'Ê¹',
    'anti_voodoo'        => 'Ìñ',
    'dummy_guard'        => 'Ì´¼é',
    'common'             => '¶¦',
    'detective_common'   => 'Ãµ',
    'trap_common'        => 'ºö',
    'ghost_common'       => 'Ë´',
    'dummy_common'       => 'Ì´¶¦',
    'poison'             => 'ÆÇ',
    'strong_poison'      => '¶¯ÆÇ',
    'incubate_poison'    => 'ÀøÆÇ',
    'guide_poison'       => 'Í¶ÆÇ',
    'chain_poison'       => 'Ï¢ÆÇ',
    'dummy_poison'       => 'Ì´ÆÇ',
    'poison_cat'         => 'Ç­',
    'revive_cat'         => 'ÀçÃ¬',
    'sacrifice_cat'      => 'Ç­¿À',
    'pharmacist'         => 'Ìô',
    'cure_pharmacist'    => '²Ï',
    'assassin'           => '°Å',
    'doom_assassin'      => '»à¿À',
    'reverse_assassin'   => 'È¿º²',
    'soul_assassin'      => 'ÄÔ',
    'eclipse_assassin'   => '¿ª°Å',
    'mind_scanner'       => '¸ç',
    'evoke_scanner'      => '¥¤',
    'whisper_scanner'    => 'ÓñÁû',
    'howl_scanner'       => 'ËÊÁû',
    'telepath_scanner'   => 'Ç°Áû',
    'jealousy'           => '¶¶',
    'poison_jealousy'    => 'ÆÇ¶¶',
    'doll'               => '¾å³¤',
    'friend_doll'        => 'Ê©Íö',
    'poison_doll'        => 'ÎëÍö',
    'doom_doll'          => 'Ë©Íé',
    'doll_master'        => '¿Í¸¯',
    'wolf'               => 'Ïµ',
    'boss_wolf'          => 'ÇòÏµ',
    'gold_wolf'          => '¶âÏµ',
    'phantom_wolf'       => '¸¸Ïµ',
    'cursed_wolf'        => '¼öÏµ',
    'wise_wolf'          => '¸­Ïµ',
    'poison_wolf'        => 'ÆÇÏµ',
    'resist_wolf'        => '¹³Ïµ',
    'blue_wolf'          => 'ÁóÏµ',
    'emerald_wolf'       => '¿éÏµ',
    'sex_wolf'           => '¿÷Ïµ',
    'tongue_wolf'        => 'ÀåÏµ',
    'possessed_wolf'     => 'ØáÏµ',
    'hungry_wolf'        => '²îÏµ',
    'sirius_wolf'        => 'Å·Ïµ',
    'elder_wolf'         => '¸ÅÏµ',
    'cute_wolf'          => 'Ë¨Ïµ',
    'scarlet_wolf'       => '¹ÈÏµ',
    'silver_wolf'        => '¶äÏµ',
    'mad'                => '¶¸',
    'fanatic_mad'        => '¶¸¿®',
    'whisper_mad'        => 'Óñ¶¸',
    'jammer_mad'         => '·îÅÆ',
    'voodoo_mad'         => '¼ö¶¸',
    'corpse_courier_mad' => '²Ð¼Ö',
    'agitate_mad'        => 'Àð',
    'miasma_mad'         => 'ÃØ',
    'dream_eater_mad'    => 'àÓ',
    'trap_mad'           => 'æ«',
    'possessed_mad'      => '¸¤',
    'fox'                => '¸Ñ',
    'white_fox'          => 'Çò¸Ñ',
    'black_fox'          => '¹õ¸Ñ',
    'gold_fox'           => '¶â¸Ñ',
    'phantom_fox'        => '¸¸¸Ñ',
    'poison_fox'         => '´É¸Ñ',
    'blue_fox'           => 'Áó¸Ñ',
    'emerald_fox'        => '¿é¸Ñ',
    'voodoo_fox'         => '¶åÈø',
    'revive_fox'         => 'Àç¸Ñ',
    'possessed_fox'      => 'Øá¸Ñ',
    'cursed_fox'         => 'Å·¸Ñ',
    'elder_fox'          => '¸Å¸Ñ',
    'cute_fox'           => 'Ë¨¸Ñ',
    'scarlet_fox'        => '¹È¸Ñ',
    'silver_fox'         => '¶ä¸Ñ',
    'child_fox'          => '»Ò¸Ñ',
    'sex_fox'            => '¿÷¸Ñ',
    'stargazer_fox'      => 'À±¸Ñ',
    'jammer_fox'         => '·î¸Ñ',
    'miasma_fox'         => 'êµ¸Ñ',
    'cupid'              => 'QP',
    'self_cupid'         => 'µá°¦',
    'moon_cupid'         => 'É±',
    'mind_cupid'         => '½÷¿À',
    'triangle_cupid'     => '¾®°­',
    'angel'              => 'Å·»È',
    'rose_angel'         => 'é¬Å·',
    'lily_angel'         => 'É´Å·',
    'exchange_angel'     => 'º²°Ü',
    'ark_angel'          => 'ÂçÅ·',
    'quiz'               => 'GM',
    'vampire'            => '·ì',
    'chiroptera'         => 'éþ',
    'poison_chiroptera'  => 'ÆÇéþ',
    'cursed_chiroptera'  => '¼öéþ',
    'boss_chiroptera'    => 'Âçéþ',
    'elder_chiroptera'   => '¸Åéþ',
    'dummy_chiroptera'   => 'Ì´°¦',
    'fairy'              => 'ÍÅÀº',
    'spring_fairy'       => '½ÕÀº',
    'summer_fairy'       => '²ÆÀº',
    'autumn_fairy'       => '½©Àº',
    'winter_fairy'       => 'ÅßÀº',
    'flower_fairy'       => '²ÖÀº',
    'star_fairy'         => 'À±Àº',
    'sun_fairy'          => 'ÆüÀº',
    'moon_fairy'         => '·îÀº',
    'grass_fairy'        => 'ÁðÀº',
    'light_fairy'        => '¸÷Àº',
    'dark_fairy'         => '°ÇÀº',
    'mirror_fairy'       => '¶ÀÀº',
    'mania'              => '¥Þ',
    'trick_mania'        => '´ñ',
    'soul_mania'         => '³ÐÀÃ',
    'unknown_mania'      => 'ó¬',
    'dummy_mania'        => 'Ì´¸ì',
    'chicken'            => 'ÆÓ',
    'rabbit'             => '±¬',
    'perverseness'       => '¼Ù',
    'flattery'           => '¸ÕËã',
    'impatience'         => 'Ã»',
    'celibacy'           => 'ÆÈ',
    'nervy'              => '¿®',
    'androphobia'        => 'ÃË¶²',
    'gynophobia'         => '½÷¶²',
    'febris'             => 'Ç®',
    'death_warrant'      => 'Àë',
    'panelist'           => '²ò',
    'liar'               => '±³',
    'invisible'          => '¸÷ÌÂ',
    'rainbow'            => 'ÆúÌÂ',
    'weekly'             => 'ÍËÌÂ',
    'grassy'             => 'ÁðÌÂ',
    'side_reverse'       => '¶ÀÌÂ',
    'line_reverse'       => 'Å·ÌÂ',
    'gentleman'          => '¿Â',
    'lady'               => '½Ê',
    'actor'              => 'Ìò',
    'authority'          => '¸¢',
    'critical_voter'     => '²ñ',
    'random_voter'       => 'µ¤',
    'rebel'              => 'È¿',
    'watcher'            => 'Ëµ',
    'decide'             => '·è',
    'plague'             => '±Ö',
    'good_luck'          => '¹¬',
    'bad_luck'           => 'ÉÔ±¿',
    'upper_luck'         => '»¨',
    'downer_luck'        => '°ìÈ¯',
    'star'               => '¿Íµ¤',
    'disfavor'           => 'ÉÔ¿Í',
    'critical_luck'      => 'ÄË',
    'random_luck'        => 'Íð',
    'strong_voice'       => 'Âç',
    'normal_voice'       => 'ÉÔ',
    'weak_voice'         => '¾®',
    'upper_voice'        => '³ÈÀ¼',
    'downer_voice'       => 'Ê¤',
    'inside_voice'       => 'ÆâÊÛ',
    'outside_voice'      => '³°ÊÛ',
    'random_voice'       => '²²',
    'no_last_words'      => 'É®',
    'blinder'            => 'ÌÜ',
    'earplug'            => '¼ª',
    'speaker'            => '½¸²»',
    'whisper_ringing'    => 'ÓñÌÄ',
    'howl_ringing'       => 'ËÊÌÄ',
    'deep_sleep'         => 'Çú¿ç',
    'silent'             => 'Ìµ¸ý',
    'mower'              => 'Áð´¢',
    'mind_read'          => 'Ï³',
    'mind_evoke'         => '¸ý´ó',
    'mind_open'          => '¸ø',
    'mind_receiver'      => '¼õ',
    'mind_friend'        => 'ÌÄ',
    'mind_sympathy'      => '´¶',
    'mind_lonely'        => '°ï',
    'lovers'             => 'Îø',
    'challenge_lovers'   => 'Æñ',
    'infected'           => 'À÷',
    'copied'             => '¸µ¥Þ',
    'copied_trick'       => '¸µ´ñ',
    'copied_soul'        => '¸µ³Ð',
    'copied_teller'      => '¸µ¸ì',
    'lost_ability'       => '¼º');

  //¥á¥¤¥óÌò¿¦¤Î¥°¥ë¡¼¥×¥ê¥¹¥È (Ìò¿¦ => ½êÂ°¥°¥ë¡¼¥×)
  // ¤³¤Î¥ê¥¹¥È¤ÎÊÂ¤Ó½ç¤Ë strpos ¤ÇÈ½ÊÌ¤¹¤ë (ÆÇ·Ï¤Ê¤É¡¢½çÈÖ°ÍÂ¸¤ÎÌò¿¦¤¬¤¢¤ë¤Î¤ÇÃí°Õ)
  var $main_role_group_list = array(
    'wolf' => 'wolf',
    'mad' => 'mad',
    'child_fox' => 'child_fox', 'sex_fox' => 'child_fox', 'stargazer_fox' => 'child_fox',
    'jammer_fox' => 'child_fox', 'miasma_fox' => 'child_fox',
    'fox' => 'fox',
    'cupid' => 'cupid',
    'angel' => 'angel',
    'quiz' => 'quiz',
    'vampire' => 'vampire',
    'chiroptera' => 'chiroptera',
    'fairy' => 'fairy',
    'mage' => 'mage', 'voodoo_killer' => 'mage',
    'necromancer' => 'necromancer',
    'medium' => 'medium',
    'priest' => 'priest',
    'guard' => 'guard', 'anti_voodoo' => 'guard', 'reporter' => 'guard',
    'common' => 'common',
    'cat' => 'poison_cat',
    'jealousy' => 'jealousy',
    'doll' => 'doll',
    'poison' => 'poison',
    'pharmacist' => 'pharmacist',
    'assassin' => 'assassin',
    'scanner' => 'mind_scanner',
    'mania' => 'mania');

  //¥µ¥ÖÌò¿¦¤Î¥°¥ë¡¼¥×¥ê¥¹¥È (CSS ¤Î¥¯¥é¥¹Ì¾ => ½êÂ°Ìò¿¦)
  var $sub_role_group_list = array(
    'lovers'       => array('lovers', 'challenge_lovers'),
    'mind'         => array('mind_read', 'mind_open', 'mind_receiver', 'mind_friend', 'mind_sympathy',
			    'mind_evoke', 'mind_lonely'),
    'mania'        => array('copied', 'copied_trick', 'copied_soul', 'copied_teller'),
    'vampire'      => array('infected'),
    'sudden-death' => array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience',
			    'celibacy', 'nervy', 'androphobia', 'gynophobia', 'febris',
			    'death_warrant', 'panelist'),
    'convert'      => array('liar', 'invisible', 'rainbow', 'weekly', 'grassy', 'side_reverse',
			    'line_reverse', 'gentleman', 'lady', 'actor'),
    'authority'    => array('authority', 'critical_voter', 'random_voter', 'rebel', 'watcher'),
    'decide'       => array('decide', 'plague', 'good_luck', 'bad_luck'),
    'luck'         => array('upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
			    'random_luck'),
    'voice'        => array('strong_voice', 'normal_voice', 'weak_voice', 'upper_voice',
			    'downer_voice', 'inside_voice', 'outside_voice', 'random_voice'),
    'seal'         => array('no_last_words', 'blinder', 'earplug', 'speaker', 'whisper_ringing',
			    'howl_ringing', 'deep_sleep', 'silent', 'mower'),
    'human'        => array('lost_ability'));
}

//-- ÇÛÌòÀßÄê¤Î´ðÄì¥¯¥é¥¹ --//
class CastConfigBase{
  //¡ÖÊ¡°ú¤­¡×¤ò°ìÄê²ó¿ô¹Ô¤Ã¤Æ¥ê¥¹¥È¤ËÄÉ²Ã¤¹¤ë
  function AddRandom(&$list, $random_list, $count){
    $total = count($random_list) - 1;
    for(; $count > 0; $count--) $list[$random_list[mt_rand(0, $total)]]++;
  }

  //¡ÖÈæ¡×¤ÎÇÛÎó¤«¤é¡ÖÊ¡°ú¤­¡×¤òºîÀ®¤¹¤ë
  function GenerateRandomList($list){
    $stack = array();
    foreach($list as $role => $rate){
      for($i = $rate; $i > 0; $i--) $stack[] = $role;
    }
    return $stack;
  }

  //¡ÖÈæ¡×¤«¤é¡Ö³ÎÎ¨¡×¤ËÊÑ´¹¤¹¤ë (¥Æ¥¹¥ÈÍÑ)
  function RateToProbability($list){
    $stack = array();
    $total_rate = array_sum($list);
    foreach($list as $role => $rate){
      $stack[$role] = sprintf("%01.2f", $rate / $total_rate * 100);
    }
    PrintData($stack);
  }

  //·èÆ®Â¼¤ÎÇÛÌò½é´ü²½½èÍý
  function InitializeDuel($user_count){
    return true;
  }

  //·èÆ®Â¼¤ÎÇÛÌòºÇ½ª½èÍý
  function FinalizeDuel($user_count, &$role_list){
    return true;
  }

  //·èÆ®Â¼¤ÎÇÛÌò½èÍý
  function SetDuel($user_count){
    $role_list = array(); //½é´ü²½½èÍý
    $this->InitializeDuel($user_count);

    if(array_sum($this->duel_fix_list) <= $user_count){
      foreach($this->duel_fix_list as $role => $count){
	$role_list[$role] = $count;
      }
    }
    $rest_user_count = $user_count - array_sum($role_list);
    asort($this->duel_rate_list);
    $total_rate = array_sum($this->duel_rate_list);
    $max_rate_role = array_pop(array_keys($this->duel_rate_list));
    foreach($this->duel_rate_list as $role => $rate){
      if($role == $max_rate_role) continue;
      $role_list[$role] = round($rest_user_count / $total_rate * $rate);
    }
    $role_list[$max_rate_role] = $user_count - array_sum($role_list);

    $this->FinalizeDuel($user_count, $role_list);
    return $role_list;
  }
}
