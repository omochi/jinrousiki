<?php
/*
  ◆封印師 (seal_medium)
  ○仕様
  ・処刑投票先が回数限定の能力を持っている人外なら封印する
*/
class Role_seal_medium extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    //封印対象者リスト
    $seal_list = array('phantom_wolf', 'resist_wolf', 'revive_wolf', 'tongue_wolf',
		       'trap_mad', 'possessed_mad',
		       'phantom_fox', 'emerald_fox', 'revive_fox', 'possessed_fox');
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;

      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsRole($seal_list)){
	$target->IsActive() ? $target->LostAbility() :
	  $USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_SEALED');
      }
    }
  }
}