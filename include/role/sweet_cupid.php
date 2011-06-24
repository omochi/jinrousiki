<?php
/*
  ◆弁財天 (sweet_cupid)
  ○仕様
  ・追加役職：両方に共鳴者
  ・処刑投票：投票先が生存していたら恋耳鳴を付加する
*/
class Role_sweet_cupid extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){
    $role .= ' ' . $this->GetActor()->GetID('mind_friend');
  }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true)) $target->AddRole('sweet_ringing');
    }
  }
}
