<?php
/*
  ◆ブン屋 (reporter)
  ○仕様
  ・尾行：襲撃情報取得
*/
class Role_reporter extends Role{
  public $action = 'REPORTER_DO';
  public $ignore_message = '初日の尾行はできません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult('REPORTER_SUCCESS'); //尾行結果
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'reporter_do', $this->action);
    }
  }

  //投票能力判定
  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  //尾行処理
  function Report($user){
    global $ROOM, $USERS;

    $wolf_target = $this->GetWolfTarget();
    if($user->IsSame($wolf_target->uname)){ //尾行成功
      if(! $user->wolf_killed) return; //人狼に襲撃されていなかったらスキップ
      $result = $USERS->GetHandleName($this->GetVoter()->uname, true);
      $str    = $this->GetActor()->GetHandleName($wolf_target->uname, $result);
      $ROOM->SystemMessage($str, 'REPORTER_SUCCESS');
    }
    elseif($user->IsLiveRoleGroup('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
      $USERS->Kill($this->GetActor()->user_no, 'REPORTER_DUTY');
    }
  }
}
