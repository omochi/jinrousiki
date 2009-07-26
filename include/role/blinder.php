<?php
class Role_blinder extends Role {
  function Role_blinder($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function AddTalk($user_info, $sentence, $volume, $row_class = 'user-talk', $user_class = 'user-name'){
    if ($this->Ignored() || $this->SameUser($user_info)) {
      parent::AddTalk($user_info, $sentence, $volume, $row_class, $user_class);
    }
    else {
      parent::AddTalk('', $sentence, $volume, $row_class, $user_class);
    }
  }
}
?>
