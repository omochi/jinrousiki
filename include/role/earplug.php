<?php
class Role_earplug extends Role{
  function Role_earplug($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function OnAddTalk($user, $talk, &$user_info, &$sentence, &$volume){
    global $MESSAGE;
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
        $volume = 'normal';
        break;
      case 'normal':
        $volume = 'weak';
        break;
      case 'weak':
        $sentence = $MESSAGE->common_talk;
        break;
      }
    }
  }

  function OnAddWhisper($role, $talk, &$user_info, &$message, &$volume){
    global $MESSAGE;
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
        $volume = 'normal';
        break;
      case 'normal':
        $volume = 'weak';
        break;
      case 'weak':
        $sentence = $MESSAGE->common_talk;
        break;
      }
    }
  }
}
?>
