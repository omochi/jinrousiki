<?php
/*
  ◆亡霊嬢
  ○仕様
  ・人狼襲撃：小心者
*/
class Role_ghost_common extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatCounter($user){ $user->AddRole('chicken'); }
}
