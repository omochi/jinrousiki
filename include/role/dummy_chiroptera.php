<?php
/*
  ◆夢求愛者 (dummy_chiroptera)
  ○仕様
*/
class Role_dummy_chiroptera extends Role{
  public $display_role = 'self_cupid';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    //自分が矢を打った(つもり)の恋人 (自分自身含む) を表示
    $user  = $this->GetActor();
    $stack = $user->GetPartner($this->role);
    if(is_array($stack)){
      $stack[] = $user->user_no;
      asort($stack);
      $stack_pair = array();
      foreach($stack as $id) $stack_pair[] = $USERS->ById($id)->handle_name;
      OutputPartner($stack_pair, 'cupid_pair');
    }

    if($ROOM->date == 1 && $ROOM->IsNight()) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO');
  }
}
