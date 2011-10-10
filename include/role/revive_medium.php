<?php
/*
  ◆風祝 (revive_medium)
  ○仕様
  ・蘇生率：25% / 誤爆有り
*/
RoleManager::LoadFile('medium');
class Role_revive_medium extends Role_medium{
  public $mix_in = 'poison_cat';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    $this->OutputReviveAbility();
  }

  function SetVoteNight(){ $this->filter->SetVoteNight(); }

  function GetVoteIconPath($user, $live){ return $this->filter->GetVoteIconPath($user, $live); }

  function IsVoteCheckbox($user, $live){ return $this->filter->IsVoteCheckbox($user, $live); }

  function IgnoreVoteNight($user, $live){ return $this->filter->IgnoreVoteNight($user, $live); }
}
