<?php
/*
  ◆占星術師 (stargazer_mage)
  ○仕様
  ・占い：投票能力鑑定
*/
RoleManager::LoadFile('psycho_mage');
class Role_stargazer_mage extends Role_psycho_mage{
  public $mage_failed = 'failed';
  function __construct(){ parent::__construct(); }

  function GetMageResult($user){ return $user->DistinguishVoteAbility(); }
}
