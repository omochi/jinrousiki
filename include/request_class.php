<?php
class RequestBase{
  var $log;
  var $argc;

  function GetGameVariables(){
    $this->GetItems('get.room_no', 'get.auto_reload', 'get.dead_mode', 'get.heaven_mode', 'get.list_down', 'get.play_sound');
  }

  function GetItems(){
    $this->argc = func_num_args();
    foreach (func_get_args() as $spec){
      $src = strtok($spec, '.');
      $item = strtok('.');
      switch(strtolower($src)){
      case 'get':
        $this->$item = $_GET[$item];
        break;
      case 'post':
        $this->$item = $_POST[$item];
        break;
      default:
        $this->$spec = $_REQUEST[$spec];
        break;
      }
    }
  }
}

class Say extends RequestBase{
  function Say(){
    $this->GetGameVariables();
    $this->GetItems('post.say', 'post.font_type');
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
      $this->GetItems('get.room_no', 'get.log_mode', 'get.reverse_log', 'get.heaven_talk', 'get.heaven_only');
    }
    else {
      $this->GetItems('get.page', 'get.reverse');
    }
  }
}
?>
