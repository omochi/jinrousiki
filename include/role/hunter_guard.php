<?php
/*
  ◆猟師 (hunter_guard)
  ○仕様
  ・狩り：通常 + 妖狐陣営
*/
class Role_hunter_guard extends Role{
  function __construct(){ parent::__construct(); }

  function IsHuntTarget($user){ return $user->IsHuntTarget() || $user->IsFox(); }
}
