<?php
/*
  ◆家神 (soul_patron)
  ○仕様
  ・追加役職：受援者の役職結果
*/
class Role_soul_patron extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){
    global $ROOM;
    $str = $this->GetActor()->handle_name . "\t" . $user->handle_name . "\t" . $user->main_role;
    $ROOM->SystemMessage($str, 'PATRON_RESULT');
    return $this->GetActor()->GetID('supported');
  }
}
