<?php
/*
  ◆吸血姫 (soul_vampire)
  ○仕様
  ・吸血：通常 + 役職取得
*/
class Role_soul_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function Infect($user){
    global $ROOM;

    $user->AddRole($this->GetActor()->GetID('infected'));
    $str = $this->GetActor()->GetHandleName($user->uname, $user->main_role);
    $ROOM->SystemMessage($str, 'VAMPIRE_RESULT');
  }
}
