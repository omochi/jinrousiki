<?php
//-- 引数解析の基底クラス --//
class RequestBase{
  function GetItems($processor){
    //$this->argc = func_num_args();
    foreach(array_slice(func_get_args(), 1) as $spec){
      $src = strtok($spec, '.');
      $item = strtok('.');
      switch(strtolower($src)){
      case 'get':
	$value = shot($_GET[$item], $spec);
	if(array_key_exists($item, $_GET) || $this->TryGetDefault($item, $value)){
	  $this->$item = empty($processor) ? $value : $processor($value);
	}
	break;

      case 'post':
	$value = shot($_POST[$item], $spec);
	if(array_key_exists($item, $_POST) || $this->TryGetDefault($item, $value)){
	  $this->$item = empty($processor) ? $value : $processor($value);
	}
	break;

      default:
	$value = shot($_REQUEST[$spec], $spec);
	if(array_key_exists($spec, $_REQUEST) || $this->TryGetDefault($spec, $value)){
	  $this->$spec = empty($processor) ? $value : $processor($value);
	}
	break;
      }
    }
  }

  function TryGetDefault($item, &$value){
    return false;
  }

  function CheckOn($arg){
    return ($arg == 'on');
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
    $this->GetItems("$this->CheckOn", 'get.list_down', 'get.play_sound');
  }
}

//-- login.php --//
class RequestLogin extends RequestBase{
  function RequestLogin(){ $this->__construct(); }

  function __construct(){
    $this->GetItems('intval', 'get.room_no');
    $this->GetItems('EscapeStrings', 'post.uname', 'post.password');
    $this->GetItems(NULL, 'post.login_type');
  }
}

//-- user_manager.php --//
class RequestUserManager extends RequestBase{
  function RequestUserManager(){ $this->__construct(); }

  function __construct(){
    $this->GetItems('intval', 'get.room_no', 'post.icon_no');
    $this->GetItems('ConvertTrip', 'post.uname', 'post.handle_name');
    $this->GetItems('EscapeStrings', 'post.password');
    $this->GetItems(NULL, 'post.command', 'post.profile', 'post.sex', 'post.role');
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
    parent::__construct();
    $this->GetItems("$this->CheckOn", 'get.dead_mode', 'get.heaven_mode', 'post.set_objection');
    $this->GetItems('EscapeStrings', 'post.font_type');
    $this->GetItems(NULL, 'post.say');
    EscapeStrings($this->say, false);
  }

  function IsLastWords(){
    return ($this->font_type == 'last_words');
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
      return ($this->date != 0);

    case 'day':
    case 'night':
      return ($this->date < 1);

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
    parent::__construct();
    $this->GetItems('intval', 'post.vote_times');
    $this->GetItems("$this->CheckOn", 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation', 'post.target_handle_name');
    $this->GetItems('EscapeStrings', 'post.target_handle_name');
    $this->AttachTestParameters(); //テスト用引数のロード
  }
}

class LogView extends RequestBase{
  function LogView(){
    if($this->is_room = isset($_GET['room_no'])){
      $this->GetItems('intval', 'get.room_no');
      $this->GetItems(
        "$this->CheckOn",
        'get.reverse_log',
        'get.heaven_talk',
        'get.heaven_only',
        'get.debug',
        'get.add_role'
      );
      $this->AttachTestParameters();
    }
    else{
      $this->GetItems(NULL, 'get.page', 'get.reverse');
      $this->GetItems("$this->CheckOn", 'get.add_role');
    }
  }
}

//-- src/upload.php --//
class RequestSrcUpload extends RequestBase{
  function RequestSrcUpload(){ $this->__construct(); }

  function __construct(){
    $this->GetItems('EscapeStrings', 'post.name', 'post.caption', 'post.user', 'post.password');
  }
}
?>
