<?php
class RequestBase{
  function GetGameVariables(){
    $this->GetItems('intval', 'get.room_no', 'get.auto_reload');
    $this->GetItems(null, 'get.dead_mode', 'get.heaven_mode', 'get.list_down', 'get.play_sound');
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
}

class Say extends RequestBase{
  function Say(){
    $this->GetGameVariables();
    $this->GetItems('EscapeString', 'post.say', 'post.font_type');
    #現在のサニタイジング仕様と適合しないため保留中。
    #$this->say = htmlspecialchars($this->say, ENT_QUOTES);
  }
}

class Objection extends RequestBase{
  function Objection(){
    $this->GetGameVariables();
    $this->GetItems('post.set_objection');
  }
}

class LogView extends RequestBase{
  function LogView(){
    if ($this->is_room = isset($_GET['room_no'])){
      $this->GetItems(
        'intval',
        'get.room_no'
      );
      $this->GetItems(
        null,
        'get.log_mode',
        'get.reverse_log',
        'get.heaven_talk',
        'get.heaven_only',
        'get.debug',
        'get.add_role'
      );
    }
    else{
      $this->GetItems(null, 'get.page', 'get.reverse');
    }
  }
}
?>
