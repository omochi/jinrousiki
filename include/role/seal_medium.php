<?php
/*
  ◆封印師 (seal_medium)
  ○仕様
  ・処刑投票：投票先が回数限定の能力を持っている人外なら封印する
*/
RoleManager::LoadFile('bacchus_medium');
class Role_seal_medium extends Role_bacchus_medium{
  public $seal_list = array(
    'phantom_wolf', 'resist_wolf', 'revive_wolf', 'fire_wolf', 'tongue_wolf',
    'trap_mad', 'possessed_mad', 'revive_mad',
    'phantom_fox', 'spell_fox', 'emerald_fox', 'revive_fox', 'possessed_fox', 'trap_fox',
    'revive_cupid', 'revive_avenger');
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsRole($this->seal_list)){
	$target->IsActive() ? $target->LostAbility() :
	  $USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_SEALED');
      }
    }
  }
}
