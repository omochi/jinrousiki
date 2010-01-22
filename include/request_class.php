<?php
class RequestBase{
  function RequestBaseGame(){
    global $GAME_CONF;

    $this->GetItems('intval', 'get.room_no', 'get.auto_reload');
    if($this->auto_reload != 0 && $this->auto_reload < $GAME_CONF->auto_reload_list[0]){
      $this->auto_reload = $GAME_CONF->auto_reload_list[0];
    }
  }

  function RequestBaseGamePlay(){
    $this->RequestBaseGame();
    $this->GetItems("$this->CheckOn", 'get.list_down', 'get.play_sound');
  }

  function GetItems($processor){
    $this->argc = func_num_args();
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
	if (array_key_exists($item, $_POST) || $this->TryGetDefault($item, $value)) {
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
}

class RequestLogin extends RequestBase{
  function RequestLogin(){
    $this->GetItems('intval', 'get.room_no');
    $this->GetItems('EscapeStrings', 'post.uname', 'post.password');
    $this->GetItems(NULL, 'post.login_type');
  }
}

class RequestUserManager extends RequestBase{
  function RequestUserManager(){
    $this->GetItems('intval', 'get.room_no', 'post.icon_no');
    $this->GetItems('ConvertTrip', 'post.uname', 'post.handle_name');
    $this->GetItems('EscapeStrings', 'post.password');
    $this->GetItems(NULL, 'post.command', 'post.profile', 'post.sex', 'post.role');
    $this->profile = EscapeStrings($this->profile, false);
  }
}

class RequestGameView extends RequestBase{
  function RequestGameView(){
    $this->RequestBaseGame();
  }
}

class RequestGamePlay extends RequestBase{
  function RequestGamePlay(){
    $this->RequestBaseGamePlay();
    $this->GetItems("$this->CheckOn", 'get.dead_mode', 'get.heaven_mode', 'post.set_objection');
    $this->GetItems('EscapeStrings', 'post.font_type');
    $this->GetItems(NULL, 'post.say');
    EscapeStrings($this->say, false);
  }

  function IsLastWords(){
    return ($this->font_type == 'last_words');
  }
}

class RequestGameLog extends RequestBase{
  function RequestGameLog(){
    $this->GetItems('intval', 'get.room_no', 'get.date');
    $this->GetItems(NULL, 'get.day_night');
  }
}

class RequestGameVote extends RequestBase{
  //変数の用途
  /*
    vote : 投票ボタンを押した or 投票ページの表示の制御用
    vote_times : 昼の投票回数
    target_no : 投票先の user_no (キューピッドがいるため単純に整数型にキャストしてはだめ)
    situation : 投票の分類 (Kick、処刑、占い、狼など)
    target_handle_name :
    target_no はタイミングで入れ替わる可能性があるので Kick のみ target_handle_name を参照する
  */
  function RequestGameVote(){
    $this->RequestBaseGamePlay();
    $this->GetItems('intval', 'post.vote_times');
    $this->GetItems("$this->CheckOn", 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation', 'post.target_handle_name');
    $this->GetItems('EscapeStrings', 'post.target_handle_name');
    AttachTestParameters($this); //テスト用引数のロード
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
      AttachTestParameters($this);
    }
    else{
      $this->GetItems(NULL, 'get.page', 'get.reverse');
      $this->GetItems("$this->CheckOn", 'get.add_role');
    }
  }
}

include_once(dirname(__FILE__).'/test_initiator.php');
?>
