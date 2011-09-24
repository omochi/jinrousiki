<?php
/*
  ◆吸血姫 (soul_vampire)
  ○仕様
  ・吸血：通常 + 役職取得
*/
RoleManager::LoadFile('vampire');
class Role_soul_vampire extends Role_vampire{
  public $result = 'VAMPIRE_RESULT';
  function __construct(){ parent::__construct(); }

  function Infect($user){
    global $ROOM;

    $user->AddRole($this->GetActor()->GetID('infected'));
    $str = $this->GetActor()->GetHandleName($user->uname, $user->main_role);
    $ROOM->SystemMessage($str, $this->result);
  }
}
