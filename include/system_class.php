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

    if($unlock) mysql_query('UNLOCK TABLES'); //��å����
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
  function Set($reset = false){
    if($reset) session_regenerate_id();
    $this->id = session_id();
    return $this->id;
  }

  //ID ����
  function Get($uniq = false){
    return $uniq ? $this->GetUniq() : $this->id;
  }

  //DB ����Ͽ����Ƥ��륻�å���� ID �����ʤ��褦�ˤ���
  function GetUniq(){
    $query = "SELECT COUNT(room_no) FROM user_entry, admin_manage WHERE " .
      "user_entry.session_id =";
    do{
      $this->Set(true);
    }while(FetchResult("$query '{$this->id}' OR admin_manage.session_id = '{$this->id}'") > 0);
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
      "AND session_id ='$this->id' AND user_no > 0";
    $array = FetchArray($query);
    if(count($array) == 1){
      $this->user_no = $array[0];
      return true;
    }

    if($exit){ //���顼����
      OutputActionResult('���å����ǧ�ڥ��顼',
			 '���å����ǧ�ڥ��顼<br>'."\n" .
			 '<a href="./" target="_top">�ȥåץڡ���</a>����' .
			 '�����󤷤ʤ����Ƥ�������');
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
