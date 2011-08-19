<?php
/*
  ◆霊能者 (necromancer)
  ○仕様
  ・霊能：通常
*/
class Role_necromancer extends Role{
  function __construct(){ parent::__construct(); }

  function Necromancer($user, $flag){ return $flag ? 'stolen' : $user->DistinguishNecromancer(); }
}
