<?php
/*
  ◆夢枕人 (dummy_necromancer)
  ○仕様
  ・霊能：村人・人狼反転
*/
class Role_dummy_necromancer extends Role{
  function __construct(){ parent::__construct(); }

  function Necromancer($user, $flag){
    global $ROOM;
    return $ROOM->IsEvent('no_dream') ? NULL : $user->DistinguishNecromancer(true);
  }
}
