<?php
/*
  ◆吸血公 (sacrifice_vampire)
  ○仕様
  ・身代わり：自分の感染者
*/
RoleManager::LoadFile('vampire');
class Role_sacrifice_vampire extends Role_vampire {
  public $mix_in = 'protected';

  function IsSacrifice(User $user) { return $user->IsPartner('infected', $this->GetID()); }
}
