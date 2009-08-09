<?php
class Role_speaker extends Role{
  function Role_speaker($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    global $MESSAGE;
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
        $sentence = $MESSAGE->howling;
        break;
      case 'normal':
        $volume = 'strong';
        break;
      case 'weak':
        $volume = 'normal';
        break;
      }
    }
  }

  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$message){
    global $MESSAGE;
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
        $sentence = $MESSAGE->howling;
        break;
      case 'normal':
        $volume = 'strong';
        break;
      case 'weak':
        $volume = 'normal';
        break;
      }
    }
  }
}
?>
