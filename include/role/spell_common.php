<?php
/*
  ◆葛の葉 (spell_common)
  ○仕様
 ・処刑投票：魔が言付加 (人外カウント + 恋人限定)
*/
RoleManager::LoadFile('common');
class Role_spell_common extends Role_common {
  public $mix_in = 'critical_mad';

  function SetVoteAction(User $user) {
    if ($user->IsInhuman() || $user->IsLovers()) $user->AddRole('cute_camouflage');
  }
}
