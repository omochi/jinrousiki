<?php
//-- �ǡ����١��������ε��ꥯ�饹 --//
class DatabaseConfigBase{
  //�ǡ����١�����³
  /*
    $header : HTML�إå����Ͼ��� [true: ���ϺѤ� / false: ̤����]
    $exit   : ���顼���� [true: exit ���֤� / false �ǽ�λ]
  */
  function Connect($header = false, $exit = true){
    //�ǡ����١��������Ф˥�������
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //������������
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //�ǡ����١�����³
	//mysql_query("SET NAMES utf8");
	//����������ϥ�ɥ���֤��ƽ�����λ
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = '�ǡ����١�����³����';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQL��������³����';
      $error_name  = $this->host;
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
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) UnlockTable(); //��å����
    mysql_close($this->db_handle);
    unset($this->db_handle); //�ϥ�ɥ�򥯥ꥢ
  }
}

//-- ���å����������饹 --//
class Session{
  var $id;
  var $user_no;

  function Session(){ $this->__construct(); }
  function __construct(){
    session_start();
    $this->Set();
  }

  //ID ���å�
  function Set(){
    $this->id = session_id();
    return $this->id;
  }

  //ID �ꥻ�å�
  function Reset(){
    //PHP �ΥС�����󤬸Ť����ϴؿ����ʤ��ΤǼ����ǽ�������
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

  //ID ����
  function Get($uniq = false){
    return $uniq ? $this->GetUniq() : $this->id;
  }

  //DB ����Ͽ����Ƥ��륻�å���� ID �����ʤ��褦�ˤ���
  function GetUniq(){
    $query = 'SELECT COUNT(room_no) FROM user_entry WHERE session_id = ';
    do{
      $this->Reset();
    }while(FetchResult($query ."'{$this->id}'") > 0);
    return $this->id;
  }

  function GetUser(){
    return $this->user_no;
  }

  //ǧ��
  function Certify($exit = true){
    global $RQ_ARGS;
    //$ip_address = $_SERVER['REMOTE_ADDR']; //IP���ɥ쥹ǧ�ڤϸ��ߤϹԤäƤ��ʤ�

    //���å���� ID �ˤ��ǧ��
    $query = "SELECT user_no FROM user_entry WHERE room_no = {$RQ_ARGS->room_no} " .
      "AND session_id ='{$this->id}' AND user_no > 0";
    $array = FetchArray($query);
    if(count($array) == 1){
      $this->user_no = $array[0];
      return true;
    }

    if($exit){ //���顼����
      $title = '���å����ǧ�ڥ��顼';
      $sentence = $title . "\n" . '<a href="./" target="_top">�ȥåץڡ���</a>����' .
	'�����󤷤ʤ����Ƥ�������';
      OutputActionResult($title, $sentence);
    }
    return false;
  }
}

//-- ���å����ǡ����Υ��ɽ��� --//
class CookieDataSet{
  var $day_night;  //������
  var $vote_times; //��ɼ���
  var $objection;  //�ְ۵Ĥ���פξ���

  function CookieDataSet(){ $this->__construct(); }
  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- ��������������饹 --//
class ExternalLinkBuilder{
  //�������̿����֥����å�
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

  function Generate($title, $data){
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  function GenerateBBS($data){
    $title = '<a href="' . $this->view_url . $this->thread . 'l50' . '">���Υ���åɾ���</a>';
    return $this->Generate($title, $data);
  }

  function GenerateSharedServerRoom($name, $url, $data){
    return $this->Generate('��������� (<a href="' . $url . '">' . $name . '</a>)', $data);
  }
}

//-- �Ǽ��ľ�������δ��쥯�饹 --//
class BBSConfigBase extends ExternalLinkBuilder{
  function Output(){
    global $SERVER_CONF;

    if($this->disable) return;
    if(! $this->CheckConnection($this->raw_url)){
      echo $this->GenerateBBS($this->host . ': Connection timed out (3 seconds)');
      return;
    }

    //����åɾ�������
    $url = $this->raw_url . $this->thread . 'l' . $this->size . 'n';
    if(($data = @file_get_contents($url)) == '') return;
    //PrintData($data, 'Data'); //�ƥ�����
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

//-- �桼��������������δ��쥯�饹 --//
class UserIconBase{
  // ���������ʸ����
  function IconNameMaxLength(){
    return 'Ⱦ�Ѥ�' . $this->name . 'ʸ�������Ѥ�' . floor($this->name / 2) . 'ʸ���ޤ�';
  }

  // ��������Υե����륵����
  function IconFileSizeMax(){
    return ($this->size > 1024 ? floor($this->size / 1024) . 'k' : $this->size) . 'Byte �ޤ�';
  }

  // ��������νĲ��Υ�����
  function IconSizeMax(){
    return '��' . $this->width . '�ԥ����� �� �⤵' . $this->height . '�ԥ�����ޤ�';
  }
}

//-- ���������δ��쥯�饹 --//
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

//-- �����رĤβ��������δ��쥯�饹 --//
class VictoryImageBase extends ImageManager{
  function Generate($name){
    switch($name){
    case 'human':
      $alt = '¼�;���';
      break;

    case 'wolf':
      $alt = '��ϵ����';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '�ŸѾ���';
      break;

    case 'lovers':
      $alt = '���;���';
      break;

    case 'quiz':
      $alt = '����Ծ���';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '����ʬ��';
      break;

    default:
      return '-';
      break;
    }
    return parent::Generate($name, $alt);
  }
}

//-- ��˥塼���ɽ���Ѥδ��쥯�饹 --//
class MenuLinkConfigBase{
  //��ή�ѥ�����ɽ��
  function Output(){
    //���������
    $this->str = '';
    $this->header = '<li>';
    $this->footer = "</li>\n";

    $this->AddHeader('��ή�ѥ�����');
    $this->AddLink($this->list);
    $this->AddFooter();

    if(count($this->add_list) > 0){
      $this->AddHeader('�������');
      foreach($this->add_list as $group => $list){
	$this->str .= $this->header . $group . $this->footer;
	$this->AddLink($list);
      }
      $this->AddFooter();
    }
    echo $this->str;
  }

  //�إå��ɲ�
  function AddHeader($title){
    $this->str .= '<div class="menu">' . $title . "</div>\n<ul>\n";
  }

  //�������
  function AddLink($list){
    $header = $this->header . '<a href="';
    $footer = '</a>' . $this->footer;
    foreach($list as $name => $url) $this->str .= $header . $url . '">' . $name . $footer;
  }

  //�եå��ɲ�
  function AddFooter(){
    $this->str .= "</ul>\n";
  }
}

//-- Copyright ɽ���Ѥδ��쥯�饹 --//
class CopyrightConfigBase{
  //��ƽ���
  function Output(){
    $stack = $this->list;
    foreach($this->add_list as $class => $list){
      $stack[$class] = array_key_exists($class, $stack) ? array_merge($stack[$class], $list) :
	$list;
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

//-- ���������δ��쥯�饹 --//
class SoundBase{
  //�����Ĥ餹
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

//-- Twitter ����Ѥδ��쥯�饹 --//
class TwitterConfigBase{
  //��ƽ���
  function Send($id, $name, $comment){
    if($this->disable) return;
    require_once(JINRO_MOD . "/twitter/Twitter.php"); //�饤�֥������

    $message = "��{$this->server}��{$id}���Ϥ�{$name}¼\n��{$comment}�� �������ޤ���";
    $st =& new Services_Twitter($this->user, $this->password);
    if($st->setUpdate(mb_convert_encoding($message, 'UTF-8', 'auto'))) return;

    //���顼����
    $sentence = 'Twitter �ؤ���Ƥ˼��Ԥ��ޤ�����<br>'."\n" .
      '�桼��̾��' . $this->user . '<br>'."\n" . '��å�������' . $message;
    PrintData($sentence);
  }
}

//-- �ڡ����������������饹 --//
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

  //ɽ������ڡ����Υ��ɥ쥹�򥻥å�
  function SetPage($page){
    $total = ceil($this->view_total / $this->view_count);
    $start = $page == 'all' ? 1 : $page;
    if($total - $start < $this->view_page){ //�Ĥ�ڡ��������ʤ�����ɽ�����ϰ��֤򤺤餹
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

  //���ץ������ɲä���
  function AddOption($type, $value = 'on'){
    $this->option[$type] = $type . '=' . $value;
  }

  //�ڡ��������ѤΥ�󥯥������������
  function Generate($page, $title = NULL, $force = false){
    if($page == $this->page->set && ! $force) return '[' . $page . ']';
    $list = $this->option;
    array_unshift($list, $this->type . '=' . $page);
    if(is_null($title)) $title = '[' . $page . ']';
    return $this->url . implode('&', $list) . '">' . $title . '</a>';
  }

  //�ڡ�����󥯤���Ϥ���
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
      $url_stack[] = '[ɽ����]';
      $url_stack[] = $this->set_reverse ? '������' : '�Ţ���';
      $name = ($this->set_reverse xor $this->reverse) ? '�����᤹' : '�����ؤ���';
      $url_stack[] =  $this->Generate($this->page->set, $name, true);
    }
    echo $this->header . implode(' ', $url_stack) . $this->footer;
  }
}

//-- ��������δ��쥯�饹 --//
class CastConfigBase{
  //��ʡ�����פ�������Ԥäƥꥹ�Ȥ��ɲä���
  function AddRandom(&$list, $random_list, $count){
    $total = count($random_list) - 1;
    for(; $count > 0; $count--) $list[$random_list[mt_rand(0, $total)]]++;
  }

  //����פ����󤫤��ʡ�����פ��������
  function GenerateRandomList($list){
    $stack = array();
    foreach($list as $role => $rate){
      for($i = $rate; $i > 0; $i--) $stack[] = $role;
    }
    return $stack;
  }

  //����פ���ֳ�Ψ�פ��Ѵ����� (�ƥ�����)
  function RateToProbability($list){
    $stack = array();
    $total_rate = array_sum($list);
    foreach($list as $role => $rate){
      $stack[$role] = sprintf("%01.2f", $rate / $total_rate * 100);
    }
    PrintData($stack);
  }
}
