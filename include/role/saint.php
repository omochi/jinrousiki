<?php
/*
  ������ (saint)
  ������
  ���跺��ɼ���ɹ����������Ԥ������ˤ�äƽ跺���䤬�Ѳ�����
*/
class Role_saint extends Role{
  function Role_saint(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){}

  function DecideVoteKill(&$uname){
    global $ROLES, $USERS;

    if($uname != '') return;
    $stack = array();
    $target_stack = array();
    foreach($ROLES->stack->max_voted as $target_uname){//��¿��ɼ�Ԥξ�������
      $user = $USERS->ByRealUname($target_uname); //$target_uname �ϲ��ۥ桼��
      if($user->IsRole('saint')) $stack[] = $target_uname;
      if($user->GetCamp(true) != 'human') $target_stack[] = $target_uname;
    }
    if(count($stack) > 0 && count($target_stack) < 2){ //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
      if(isset($target_stack[0])) $uname = $target_stack[0];
      elseif(count($stack) == 1)  $uname = $stack[0];
    }
  }
}
