<?php
/*
  ◆毒橋姫 (poison_jealousy)
  ○仕様
  ・毒：恋人
  ・襲撃毒死回避：恋人以外
*/
class Role_poison_jealousy extends Role {
  public $mix_in = 'poison';
  public $display_role = 'poison';

  function IsPoisonTarget(User $user) { return $user->IsLovers(); }

  function AvoidPoisonEat(User $user) { return ! $user->IsLovers(); }
}
