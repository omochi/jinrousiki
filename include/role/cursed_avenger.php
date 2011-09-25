<?php
/*
  ◆がしゃどくろ (cursed_avenger)
  ○仕様
  ・処刑投票：投票先が生存していたら死の宣告を付加する (人外限定)
*/
RoleManager::LoadFile('avenger');
class Role_cursed_avenger extends RoleVoteAbility{
  public $mix_in = 'avenger';
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsRoleGroup('wolf', 'fox') &&! $target->IsAvoid()){
	$target->AddDoom(4);
      }
    }
  }
}
