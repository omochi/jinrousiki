<?php
/*
  ◆死化粧師 (embalm_necromancer)
  ○仕様
  ・霊能：処刑投票先との陣営比較
*/
RoleManager::LoadFile('necromancer');
class Role_embalm_necromancer extends RoleVoteAbility{
  public $mix_in = 'necromancer';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){ $this->filter->OutputAbility(); }

  function Necromancer($user, $flag){
    if($flag) return 'stolen';
    $target = $this->GetVoteUser($user->uname)->GetCamp(true);
    return 'embalm_' . ($user->GetCamp(true) == $target ? 'agony' : 'reposeful');
  }
}
