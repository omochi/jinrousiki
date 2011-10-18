<?php
/*
  ◆ブン屋 (reporter)
  ○仕様
  ・尾行：襲撃情報取得
*/
class Role_reporter extends Role{
  public $action = 'REPORTER_DO';
  public $result = 'REPORTER_SUCCESS';
  public $ignore_message = '初日の尾行はできません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult($this->result); //尾行結果
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'reporter_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  //尾行
  function Report($user){
    global $ROOM, $USERS;

    $target = $this->GetWolfTarget();
    if($user->IsSame($target->uname)){ //尾行成功
      if(! $user->wolf_eat) return; //人狼襲撃が失敗していたらスキップ
      $result = $USERS->GetHandleName($this->GetWolfVoter()->uname, true);
      $str    = $this->GetActor()->GetHandleName($target->uname, $result);
      $ROOM->SystemMessage($str, $this->result);
    }
    elseif($user->IsLiveRoleGroup('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
      $USERS->Kill($this->GetActor()->user_no, 'REPORTER_DUTY');
    }
  }
}
