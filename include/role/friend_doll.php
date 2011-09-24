<?php
/*
  ◆仏蘭西人形 (friend_doll)
  ○仕様
*/
RoleManager::LoadFile('doll');
class Role_friend_doll extends Role_doll{
  public $doll_partner = true;
  function __construct(){ parent::__construct(); }
}
