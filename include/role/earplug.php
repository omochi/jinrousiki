<?php
class Role_earplug extends Role{
  function Role_earplug($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function AddTalk($symbol, $user_info, $sentence, $volume,
		   $row_class = 'user-talk', $user_class = 'user-name'){
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
	$volume = 'normal';
	break;

      case 'normal':
	$volume = 'weak';
	break;

      case 'weak':
	$sentence = '';
	break;
      }
    }
    parent::AddTalk($symbol, $user_info, $sentence, $volume, $row_class, $user_class);
  }

  function AddWhisper($user_info, $sentence, $volume = 'normal', $user_class = '', $say_class = ''){
    if(! $this->Ignored()){
      switch($volume){
      case 'strong':
	$volume = 'normal';
	break;

      case 'normal':
	$volume = 'weak';
	break;

      case 'weak':
	$sentence = '';
	break;
      }
    }
    parent::AddWhisper($user_info, $sentence, $volume, $user_class, $say_class);
  }
}
?>
