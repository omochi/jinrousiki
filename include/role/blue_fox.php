<?php
/*
  ◆蒼狐
  ○仕様
  ・人狼襲撃カウンター：はぐれ者
*/
class Role_blue_fox extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatCounter($user){ if(! $user->IsLonely()) $user->AddRole('mind_lonely'); }
}
