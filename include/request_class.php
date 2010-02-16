<?php
//-- �������Ϥδ��쥯�饹 --//
class RequestBase{
  function GetItems($items){
    $spec_list = func_get_args();
    $processor = array_shift($spec_list);
    $src_list  = array('get' => $_GET, 'post' => $_POST, 'file' => $_FILES['file']);
    foreach($spec_list as $spec){
      list($src, $item) = explode('.', $spec);
      if(array_key_exists($src, $src_list))
	$value_list = $src_list[$src];
      else{
	$value_list = $_REQUEST;
	$item = $spec;
      }

      if(is_array($value_list) && array_key_exists($item, $value_list))
	$value = $value_list[$item];
      elseif(! $this->TryGetDefault($item, $value)){
	$this->$item = NULL;
	continue;
      }
      $this->$item = empty($processor) ? $value : $processor($value);
    }
  }

  function TryGetDefault($item, &$value){
    return false;
  }

  function CheckOn($arg){
    return $arg == 'on';
  }

  function ToArray(){
    $array = array();
    foreach($this as $key => $value) $array[$key] = $value;
    return $array;
  }

  function AttachTestParameters(){
    global $DEBUG_MODE;
    if($DEBUG_MODE) $this->TestItems = new TestParams();
  }
}

//-- �ƥ����ѥѥ�᡼�����ꥯ�饹 --//
class TestParams extends RequestBase{
  function TestParams(){ $this->__construct(); }
  function __construct(){
    $this->GetItems(NULL, 'test_users', 'test_room', 'test_mode');
    $this->is_virtual_room = isset($this->test_users);
  }
}

//-- game �Ѷ��̥��饹 --//
class RequestBaseGame extends RequestBase{
  function RequestBaseGame(){ $this->__construct(); }
  function __construct(){
    global $GAME_CONF;
    $this->GetItems('intval', 'get.room_no', 'get.auto_reload');

    $min_auto_reload = min($GAME_CONF->auto_reload_list);
    if($this->auto_reload != 0 && $this->auto_reload < $min_auto_reload){
      $this->auto_reload = $min_auto_reload;
    }
  }
}

//-- game play �Ѷ��̥��饹 --//
class RequestBaseGamePlay extends RequestBaseGame{
  function RequestBaseGamePlay(){ $this->__construct(); }
  function __construct(){
    parent::__construct();
    $this->GetItems("$this->CheckOn", 'get.list_down', 'get.play_sound');
  }
}

//-- login.php --//
class RequestLogin extends RequestBase{
  function RequestLogin(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no');
    $this->GetItems('EscapeStrings', 'post.uname', 'post.password');
    $this->GetItems(NULL, 'post.login_type');
  }
}

//-- user_manager.php --//
class RequestUserManager extends RequestBase{
  function RequestUserManager(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no', 'post.icon_no');
    $this->GetItems('ConvertTrip', 'post.uname', 'post.handle_name');
    $this->GetItems('EscapeStrings', 'post.password');
    $this->GetItems("$this->CheckOn", 'post.entry');
    $this->GetItems(NULL, 'post.profile', 'post.sex', 'post.role');
    EscapeStrings($this->profile, false);

    if($this->room_no < 1){
      $sentence = '���顼��¼���ֹ椬����ǤϤ���ޤ���<br>'."\n".'<a href="./">�����</a>';
      OutputActionResult('¼����Ͽ [¼�ֹ楨�顼]', $sentence);
    }
  }
}

//-- game_play.php --//
class RequestGamePlay extends RequestBaseGamePlay{
  function RequestGamePlay(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    parent::__construct();
    $this->GetItems("$this->CheckOn", 'get.dead_mode', 'get.heaven_mode', 'post.set_objection');
    $this->GetItems('EscapeStrings', 'post.font_type');
    $this->GetItems(NULL, 'post.say');
    EscapeStrings($this->say, false);
    $this->last_words = ($this->font_type == 'last_words');
  }
}

//-- game_log.php --//
class RequestGameLog extends RequestBase{
  function RequestGameLog(){ $this->__construct(); }
  function __construct(){
    $this->GetItems('intval', 'get.room_no', 'get.date');
    $this->GetItems(NULL, 'get.day_night');

    if($this->IsInvalidScene()){
      OutputActionResult('�������顼', '�������顼��̵���ʰ����Ǥ�');
    }
  }

  function IsInvalidScene(){
    switch($this->day_night){
    case 'beforegame':
      return $this->date != 0;

    case 'day':
    case 'night':
      return $this->date < 1;

    default:
      return true;
    }
  }
}

//-- game_vote.php --//
class RequestGameVote extends RequestBaseGamePlay{
  //�ѿ�������
  /*
    vote : ��ɼ�ܥ���򲡤��� or ��ɼ�ڡ�����ɽ����������
    vote_times : �����ɼ���
    target_no : ��ɼ��� user_no (���塼�ԥåɤ����뤿��ñ����������˥��㥹�Ȥ��ƤϤ���)
    situation : ��ɼ��ʬ�� (Kick���跺���ꤤ��ϵ�ʤ�)
    target_handle_name :
    target_no �ϥ����ߥ󥰤������ؤ���ǽ��������Τ� Kick �Τ� target_handle_name �򻲾Ȥ���
  */
  function RequestGameVote(){ $this->__construct(); }
  function __construct(){
    if($_POST['situation'] == 'KICK_DO') EncodePostData(); //KICK �����б�
    parent::__construct();
    $this->GetItems('intval', 'post.vote_times');
    $this->GetItems("$this->CheckOn", 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation');
    $this->GetItems('EscapeStrings', 'post.target_handle_name');
    $this->AttachTestParameters(); //�ƥ����Ѱ����Υ���
  }
}

//-- old_log.php --//
class RequestOldLog extends RequestBase{
  function RequestOldLog(){ $this->__construct(); }
  function __construct(){
    if($this->is_room = isset($_GET['room_no'])){
      $this->GetItems('intval', 'get.room_no');
      $this->GetItems("$this->CheckOn", 'get.reverse_log', 'get.heaven_talk',
		      'get.heaven_only','get.debug', 'get.add_role');
      $this->AttachTestParameters();
    }
    else{
      $this->GetItems(NULL, 'get.page', 'get.reverse');
      $this->GetItems("$this->CheckOn", 'get.add_role');
    }
  }
}

//-- icon_upload.php --//
class RequestIconUpload extends RequestBase{
  function RequestIconUpload(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('EscapeStrings', 'post.name', 'post.color');
    $this->GetItems('intval', 'post.icon_no', 'file.size');
    $this->GetItems(NULL, 'post.command', 'file.type', 'file.tmp_name');
  }
}

//-- src/upload.php --//
class RequestSrcUpload extends RequestBase{
  function RequestSrcUpload(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('EscapeStrings', 'post.name', 'post.caption', 'post.user', 'post.password');
  }
}
