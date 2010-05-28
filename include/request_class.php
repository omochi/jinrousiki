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
      elseif(! $this->GetDefault($item, $value)){
	$value = NULL;
      }

      $this->$item = empty($processor) ? $value :
	(method_exists($this, $processor) ? $this->$processor($value) : $processor($value));
    }
  }

  function GetDefault($item, &$value){
    return false;
  }

  function IsOn($arg){
    return $arg == 'on';
  }

  function SetPage($arg){
    if($arg == 'all') return $arg;
    $int = intval($arg);
    return $int > 0 ? $int : 1;
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
    $this->GetItems('IsOn', 'get.list_down', 'get.play_sound');
  }
}

//-- icon �Ѷ��̥��饹 --//
class RequestBaseIcon extends RequestBase{
  function RequestBaseIcon(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('EscapeStrings', 'post.icon_name', 'post.appearance',
		    'post.category', 'post.author', 'post.color');
    $this->GetItems('intval', 'post.icon_no');
  }

  function GetIconData(){
    $this->GetItems('SetPage', 'get.page', 'get.appearance_page',
		    'get.category_page', 'get.author_page');
    $this->GetItems('SetCategory', 'get.appearance', 'get.category', 'get.author');
  }

  function SetCategory($arg){
    if($arg == '') return NULL;
    if($arg == 'all') return $arg;
    $int = intval($arg);
    return $int < 0 ? 0 : $int;
  }
}

//-- login.php --//
class RequestLogin extends RequestBase{
  function RequestLogin(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no');
    $this->GetItems('IsOn', 'post.login_manually');
    $this->GetItems('ConvertTrip', 'post.uname');
    $this->GetItems('EscapeStrings', 'post.password');
  }
}

//-- user_manager.php --//
class RequestUserManager extends RequestBaseIcon{
  function RequestUserManager(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no', 'post.icon_no');
    $this->GetItems('ConvertTrip', 'post.uname', 'post.handle_name');
    $this->GetItems('EscapeStrings', 'post.password');
    $this->GetItems('IsOn', 'post.entry');
    $this->GetItems(NULL, 'post.profile', 'post.sex', 'post.role');
    $this->GetIconData();
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
    $this->GetItems('IsOn', 'get.dead_mode', 'get.heaven_mode', 'post.set_objection');
    $this->GetItems('EscapeStrings', 'post.font_type');
    $this->GetItems(NULL, 'post.say');
    EscapeStrings($this->say, false);
    $this->last_words = $this->font_type == 'last_words';
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
    $this->GetItems('IsOn', 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation');
    $this->GetItems('EscapeStrings', 'post.target_handle_name');
    $this->AttachTestParameters(); //�ƥ����Ѱ����Υ���
    $this->SetURL();
  }

  function SetURL(){
    $url_option = 'room_no=' . $this->room_no;
    if($this->auto_reload > 0) $url_option .= '&auto_reload=' . $this->auto_reload;
    if($this->play_sound)      $url_option .= '&play_sound=on';
    if($this->list_down)       $url_option .= '&list_down=on';
    $url_option . '#game_top';
    $this->post_url = 'game_vote.php?' . $url_option;
    $this->back_url = '<a href="game_up.php?' . $url_option . '">����� &amp; reload</a>';
  }
}

//-- old_log.php --//
class RequestOldLog extends RequestBase{
  function RequestOldLog(){ $this->__construct(); }
  function __construct(){
    if($this->is_room = isset($_GET['room_no'])){
      $this->GetItems('intval', 'get.room_no');
      $this->GetItems('IsOn', 'get.reverse_log', 'get.heaven_talk',
		      'get.heaven_only','get.debug', 'get.add_role');
      $this->AttachTestParameters();
    }
    else{
      $this->GetItems(NULL, 'get.reverse');
      $this->GetItems('IsOn', 'get.add_role');
      $this->GetItems('SetPage', 'get.page');
    }
  }
}

//-- icon_view.php --//
class RequestIconView extends RequestBaseIcon{
  function RequestIconView(){ $this->__construct(); }
  function __construct(){
    $this->GetIconData();
    $this->GetItems('intval', 'get.icon_no');
  }
}

//-- icon_edit.php --//
class RequestIconEdit extends RequestBaseIcon{
  function RequestIconEdit(){ $this->__construct(); }
  function __construct(){
    parent::__construct();
    $this->GetItems('EscapeStrings', 'post.password');
  }
}

//-- icon_upload.php --//
class RequestIconUpload extends RequestBaseIcon{
  function RequestIconUpload(){ $this->__construct(); }
  function __construct(){
    parent::__construct();
    $this->GetItems('intval', 'file.size');
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
