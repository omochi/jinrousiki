<?php
/*
  ◆寒戸婆 (snow_cupid)
  ○仕様
  ・追加役職：なし
  ・処刑投票：投票先が恋人で生存していたら凍傷を付加する
*/
class Role_snow_cupid extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){ return $this->GetActor()->GetID('lovers'); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsLovers()) $target->AddDoom(1, 'frostbite');
    }
  }
}
