<?php
/*
  ◆辻斬り (soul_assassin)
  ○仕様
  ・暗殺：役職判定 + 毒死(毒能力者)
*/
RoleManager::LoadFile('assassin');
class Role_soul_assassin extends Role_assassin{
  public $result = 'ASSASSIN_RESULT';
  function __construct(){ parent::__construct(); }

  function Assassin($user){
    global $ROOM, $USERS, $ROLES;

    if($user->IsDead(true)) return;
    $str = $this->GetActor()->GetHandleName($user->uname, $user->main_role);
    $ROOM->SystemMessage($str, 'ASSASSIN_RESULT');

    //暗殺先が毒能力者なら死亡
    if($user->IsPoison()) $USERS->Kill($this->GetActor()->user_no, 'POISON_DEAD_night');
    $ROLES->stack->assassin[$user->uname] = true;
  }
}
