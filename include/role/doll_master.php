<?php
/*
  ◆人形遣い (doll_master)
  ○仕様
  ・身代わり対象者：人形
*/
class Role_doll_master extends Role{
  public $mix_in = 'protected';
  function __construct(){ parent::__construct(); }

  function IsSacrifice($user){ return $user->IsDoll(); }
}
