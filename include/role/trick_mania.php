<?php
/*
  ◆奇術師 (trick_mania)
  ○仕様
  ・コピー：交換コピー
*/
RoleManager::LoadFile('mania');
class Role_trick_mania extends Role_mania{
  public $copied = 'copied_trick';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    if($user->IsRoleGroup('mania')) return $this->ChangeRole('human'); //神話マニア陣営は村人固定

    $role = $user->main_role;
    if($user->IsRole('widow_priest', 'revive_priest')) return $this->ChangeRole($role); //例外判定

    foreach($vote_data as $stack){ //交換コピー判定
      if(array_key_exists($user->uname, $stack)) return $this->ChangeRole($role);
    }
    if(! $user->IsDummyBoy()) $user->ReplaceRole($role, $user->DistinguishRoleGroup());

    return $this->ChangeRole($role);
  }
}
