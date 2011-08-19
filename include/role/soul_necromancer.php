<?php
/*
  ◆雲外鏡 (soul_necromancer)
  ○仕様
  ・霊能：役職
*/
class Role_soul_necromancer extends Role{
  function __construct(){ parent::__construct(); }

  function Necromancer($user, $flag){ return $flag ? 'stolen' : $user->main_role; }
}
