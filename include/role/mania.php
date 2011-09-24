<?php
/*
  ◆神話マニア (mania)
  ○仕様
  ・コピー：メイン役職
*/
class Role_mania extends Role{
  public $copied = 'copied';

  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date == 2 && $this->delay_copy) OutputSelfAbilityResult('MANIA_RESULT');
    if($ROOM->date == 1 && $ROOM->IsNight()) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO');
  }

  //コピー処理
  function Copy($user, $vote_data){
    return $this->ChangeRole($user->IsRoleGroup('mania') ? 'human' : $user->main_role);
  }

  //役職変化処理
  function ChangeRole($role){
    $this->GetActor()->ReplaceRole($this->role, $role);
    $this->GetActor()->AddRole($this->copied);
    return $role;
  }
}
