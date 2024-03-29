<?php
/*
  ◆翠狼 (emerald_wolf)
  ○仕様
  ・人狼襲撃失敗：共鳴者 (人狼限定)
*/
RoleManager::LoadFile('wolf');
class Role_emerald_wolf extends Role_wolf {
  protected function WolfEatSkipAction(User $user) {
    $role = $this->GetWolfVoter()->GetID('mind_friend');
    $this->GetWolfVoter()->AddRole($role);
    $user->AddRole($role);
  }
}
