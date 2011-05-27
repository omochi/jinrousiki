<?php
/*
  ◆騎士 (poison_guard)
  ○仕様
  ・狩り：通常
*/
class Role_poison_guard extends Role{
  function __construct(){ parent::__construct(); }

  function IsHuntTarget($user){ return $user->IsHuntTarget(); }
}
