<?php
class Role_blinder extends Role {
  function Role_blinder($user){
    parent::__construct($user);
  }

  function __construct($user){
    parent::__construct($user);
  }

  function OnAddTalk($user, $talk, &$user_info, &$sentence, &$volume){
    if($this->Ignored() || $this->SameUser($user_info)){
    }
    else{
      //�ϥ�ɥ�̾����ɽ��
      $user_info = '<font style="color:'.$user->color.'">��</font>';
    }
  }
}
?>
