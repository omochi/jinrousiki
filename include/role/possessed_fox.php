<?php
/*
  ◆憑狐 (possessed_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_possessed_fox extends Role_fox{
  public $mix_in = 'possessed_mad';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){ $this->filter->OutputAction(); }

  function SetVoteNight(){ $this->filter->SetVoteNight(); }

  function GetVoteIconPath($user, $live){ return $this->filter->GetVoteIconPath($user, $live); }

  function IsVoteCheckbox($user, $live){ return $this->filter->IsVoteCheckbox($user, $live); }

  function IgnoreVoteNight($user, $live){ return $this->filter->IgnoreVoteNight($user, $live); }
}
