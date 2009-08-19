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
        $this->$item = empty($processor) ? $_GET[$item] : $processor($_GET[$item]);
        break;
      case 'post':
        $this->$item = empty($processor) ? $_POST[$item] : $processor($_POST[$item]);
        break;
      default:
        $this->$spec = empty($processor) ? $_REQUEST[$spec] : $processor($_REQUEST[$spec]);
        break;
      }
    }
  }

  function CheckOn($arg){
    return ($arg == 'on');
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
    $this->GetItems("$this->CheckOn", 'get.view_mode', 'get.dead_mode', 'get.heaven_mode',
		    'post.set_objection');
    $this->GetItems('EscapeStrings', 'post.font_type');
    $this->GetItems(NULL, 'post.say');
    EscapeStrings($this->say, false);
  }

  function is_last_words(){
    return ($this->font_type == 'last_words');
  }
}

class RequestGameVote extends RequestBase{
  //�ϐ��̗p�r
  /*
    vote : ���[�{�^���������� or ���[�y�[�W�̕\���̐���p
    vote_times : ���̓��[��
    target_no : ���[��� user_no (�L���[�s�b�h�����邽�ߒP���ɐ����^�ɃL���X�g���Ă͂���)
    situation : ���[�̕��� (Kick�A���Y�A�肢�A�T�Ȃ�)
    target_handle_name :
    target_no �̓^�C�~���O�œ���ւ��\��������̂� Kick �̂� target_handle_name ���Q�Ƃ���
  */
  function RequestGameVote(){
    $this->RequestBaseGamePlay();
    $this->GetItems('intval', 'post.vote_times');
    $this->GetItems("$this->CheckOn", 'post.vote');
    $this->GetItems(NULL, 'post.target_no', 'post.situation', 'post.target_handle_name');
    EscapeStrings($this->target_handle_name);
  }
}

class RequestGameLog extends RequestBase{
  function RequestGameLog(){
    $this->GetItems('intval', 'get.room_no', 'get.date');
    $this->GetItems(NULL, 'get.day_night');
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
    }
    else{
      $this->GetItems(NULL, 'get.page', 'get.reverse');
      $this->GetItems("$this->CheckOn", 'get.add_role');
    }
  }
}
?>
