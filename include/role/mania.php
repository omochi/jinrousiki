<?php
/*
  ◆神話マニア (mania)
  ○仕様
  ・コピー：メイン役職
*/
class Role_mania extends Role{
  public $action = 'MANIA_DO';
  public $copied = 'copied';
  public $delay_copy = false;
  public $camp_copy  = false;
  public $ignore_message = '初日以外は投票できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date == 2 && $this->delay_copy) OutputSelfAbilityResult('MANIA_RESULT');
    if($this->IsVote() && $ROOM->IsNight()){
      OutputVoteMessage('mania-do', 'mania_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date == 1;
  }

  //コピー処理
  function Copy($user){
    global $ROOM;

    $actor = $this->GetActor();
    $role  = $this->GetRole($user);
    $this->CopyAction($user, $role);

    $this->delay_copy || $this->camp_copy ? $actor->AddMainRole($user->user_no) :
      $actor->ReplaceRole($this->role, $role);
    if(! $this->delay_copy) $actor->AddRole($this->copied);

    if($this->camp_copy) return;
    $str = $actor->handle_name . "\t" . $user->handle_name . "\t" . $role;
    $ROOM->SystemMessage($str, 'MANIA_RESULT');  //コピー結果
  }

  //特殊コピー処理
  function CopyAction($user, $role){}

  //コピー結果役職取得
  function GetRole($user){
    return $user->IsRoleGroup('mania') ? 'human' : $this->GetCopyRole($user);
  }

  //コピー役職取得
  function GetCopyRole($user){ return $user->main_role; }
}
