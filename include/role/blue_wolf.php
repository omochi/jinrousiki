<?php
/*
  ◆蒼狼
  ○仕様
  ・妖狐襲撃：はぐれ者
*/
class Role_blue_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatAction($user){ if(! $user->IsLonely()) $user->AddRole('mind_lonely'); }
}
