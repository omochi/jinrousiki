<?php
//-- 引数解析の基底クラス --//
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

//-- テスト用パラメータ設定クラス --//
class TestParams extends RequestBase{
  function TestParams(){ $this->__construct(); }
  function __construct(){
    $this->GetItems(NULL, 'test_users', 'test_room', 'test_mode');
    $this->is_virtual_room = isset($this->test_users);
  }
}

//-- game 用共通クラス --//
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

//-- game play 用共通クラス --//
class RequestBaseGamePlay extends RequestBaseGame{
  function RequestBaseGamePlay(){ $this->__construct(); }
  function __construct(){
    parent::__construct();
    $this->GetItems('IsOn', 'get.list_down', 'get.play_sound');
  }
}

//-- login.php --//
class RequestLogin extends RequestBase{
  function RequestLogin(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no');
    $this->GetItems('IsOn', 'post.login_manually');
    $this->GetItems('EscapeStrings', 'post.uname', 'post.password');
  }
}

//-- user_manager.php --//
class RequestUserManager extends RequestBase{
  function RequestUserManager(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('intval', 'get.room_no', 'post.icon_no');
    $this->GetItems('SetPage', 'get.page');
    $this->GetItems('ConvertTrip', 'post.uname', 'post.handle_name');
    $this->GetItems('EscapeStrings', 'post.password');
    $this->GetItems('IsOn', 'post.entry');
    $this->GetItems(NULL, 'post.profile', 'post.sex', 'post.role');
    EscapeStrings($this->profile, false);

    if($this->room_no < 1){
      $sentence = 'エラー：村の番号が正常ではありません。<br>'."\n".'<a href="./">←戻る</a>';
      OutputActionResult('村人登録 [村番号エラー]', $sentence);
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
      OutputActionResult('引数エラー', '引数エラー：無効な引数です');
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
  //変数の用途
  /*
    vote : 投票ボタンを押した or 投票ページの表示の制御用
    vote_times : 昼の投票回数
    target_no : 投票先の user_no (キューピッドがいるため単純に整数型にキャストしてはだめ)
    situation : 投票の分類 (Kick、処刑、占い、狼など)
    target_handle_name :
    target_no はタイミングで入れ替わる可能性があるので Kick のみ target_handle_name を参照する
  */
  function RequestGameVote(){ $this->__construct(); }
  function __construct(){
    if($_POST['situation'] == 'KICK_DO') EncodePostData(); //KICK 処理対応
    parent::__construct();
    $this->GetItems('intval', 'post.vote_times');
    $this->GetItems('IsOn', 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation');
    $this->GetItems('EscapeStrings', 'post.target_handle_name');
    $this->AttachTestParameters(); //テスト用引数のロード
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
class RequestIconView extends RequestBase{
  function RequestIconView(){ $this->__construct(); }
  function __construct(){
    $this->GetItems('SetPage', 'get.page');
  }
}

//-- icon_upload.php --//
class RequestIconUpload extends RequestBase{
  function RequestIconUpload(){ $this->__construct(); }
  function __construct(){
    EncodePostData();
    $this->GetItems('EscapeStrings', 'post.name', 'post.appearance', 'post.category',
		    'post.author', 'post.color');
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
