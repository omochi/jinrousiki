<?php
/*
  ◆奇術師 (trick_mania)
  ○仕様
  ・コピー：交換コピー
*/
RoleManager::LoadFile('mania');
class Role_trick_mania extends Role_mania {
  public $copied = 'copied_trick';

  protected function CopyAction(User $user, $role) {
    //スキップ判定
    if ($role == 'human' || $user->IsDummyBoy() || $user->IsRole('widow_priest', 'revive_priest')) {
      return;
    }

    foreach ($this->GetStack('vote_data') as $stack) { //交換コピー判定
      if (array_key_exists($user->id, $stack)) return;
    }
    $user->ReplaceRole($role, $user->DistinguishRoleGroup());
  }
}
