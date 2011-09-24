<?php
/*
  ◆河童 (cure_pharmacist)
  ○仕様
  ・解毒/ショック死抑制
*/
RoleManager::LoadFile('pharmacist');
class Role_cure_pharmacist extends Role_pharmacist{
  function __construct(){ parent::__construct(); }

  function SetDetoxFlag(&$list, $uname){
    $this->GetActor()->detox_flag = true;
    $list[$uname] = 'cured';
  }
}
