<?php
class Role_blinder extends Role {
  function Role_blinder($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function AddTalk($symbol, $user_info, $sentence, $volume,
		   $row_class = 'user-talk', $user_class = 'user-name'){
    if($this->Ignored() || $this->SameUser($user_info)){
    }
    else{
      $user_info = '';
    }
    parent::AddTalk($symbol, $user_info, $sentence, $volume, $row_class, $user_class);
  }
}
?>
