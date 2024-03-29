<?php
/*
  ◆榊鬼 (poison_ogre)
  ○仕様
  ・勝利：出題者陣営勝利 or 生存
  ・人攫い無効：出題者
  ・人攫い：解答者付加
  ・毒：人外カウント + 鬼陣営
*/
RoleManager::LoadFile('ogre');
class Role_poison_ogre extends Role_ogre {
  public $mix_in = 'poison';
  public $reduce_rate = 3;

  function Win($winner) { return $winner == 'quiz' || $this->IsLive(); }

  protected function IgnoreAssassin(User $user) { return $user->IsRole('quiz'); }

  protected function Assassin(User $user) { $user->AddRole('panelist'); }

  function IsPoisonTarget(User $user) { return $user->IsInhuman() || $user->IsOgre(); }
}
