<?php
/*
  ◆鬼子母神 (hariti_yaksa)
  ○仕様
  ・勝利：生存 + 子狐系・キューピッド系・天使系全滅 + 村人陣営以外勝利
*/
RoleManager::LoadFile('yaksa');
class Role_hariti_yaksa extends Role_yaksa {
  public $reduce_rate = 2;

  protected function IgnoreWin($winner) { return $winner == 'human'; }

  protected function IgnoreAssassin(User $user) {
    return ! ($user->IsChildFox() || $user->IsMainGroup('cupid', 'angel'));
  }
}
