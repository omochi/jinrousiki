<?php
/*
  ◆餓狼
  ○仕様
  ・襲撃：人狼系・妖狐陣営以外無効
*/
class Role_hungry_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatAction($user){ return ! $user->IsWolf() && ! $user->IsFox(); }
}
