<?php
class Role_speaker extends Role{
  function Role_speaker($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function AddTalk($symbol, $user_info, $sentence, $volume,
		   $row_class = 'user-talk', $user_class = 'user-name'){
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
    parent::AddTalk($symbol, $user_info, $sentence, $volume, $row_class, $user_class);
  }

  function AddWhisper($user_info, $sentence, $volume = 'normal', $user_class = '', $say_class = ''){
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
    parent::AddWhisper($user_info, $sentence, $volume, $user_class, $say_class);
  }
}
?>
