<?php
/*
  ◆弁財天 (sweet_cupid)
  ○仕様
  ・処刑投票：投票先が生存していたら恋耳鳴を付加する
  ・追加役職：両方に共鳴者
*/
class Role_sweet_cupid extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true)) $target->AddRole('sweet_ringing');
    }
  }

  function AddLoversRole(&$role, $user, $flag){
    global $SELF;
    $role .= ' ' . $SELF->GetID('mind_friend');
  }
}
