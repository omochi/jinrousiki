<?php
/*
  ◆霊能者 (necromancer)
  ○仕様
  ・霊能：通常
*/
class Role_necromancer extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult(strtoupper($this->role) . '_RESULT');
  }

  //霊能
  function Necromancer($user, $flag){
    global $USERS;
    return $USERS->GetHandleName($user->uname, true) . "\t" .
      ($flag ? 'stolen' : $this->DistinguishNecromancer($user));
  }

  //霊能判定
  function DistinguishNecromancer($user, $reverse = false){
    if($user->IsOgre()) return 'ogre';
    if($user->IsRoleGroup('vampire') || $user->IsRole('cute_chiroptera')) return 'chiroptera';
    if($user->IsChildFox()) return 'child_fox';
    if($user->IsRole('white_fox', 'black_fox', 'mist_fox', 'phantom_fox', 'sacrifice_fox',
		     'possessed_fox', 'cursed_fox')){
      return 'fox';
    }
    if($user->IsRole('boss_wolf', 'mist_wolf', 'phantom_wolf', 'cursed_wolf', 'possessed_wolf')){
      return $user->main_role;
    }
    return ($user->IsWolf() xor $reverse) ? 'wolf' : 'human';
  }
}
