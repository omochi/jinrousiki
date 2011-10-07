<?php
/*
  ◆暗殺者 (assassin)
  ○仕様
  ・暗殺：標準
*/
class Role_assassin extends Role{
  public $action = 'ASSASSIN_DO';
  public $not_action = 'ASSASSIN_NOT_DO';
  public $ignore_message = '初日は暗殺できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2 && isset($this->result)) OutputSelfAbilityResult($this->result); //暗殺結果
    $this->OutputAction();
  }

  function OutputAction(){
    global $ROOM;
    if($this->IsVote() && $ROOM->IsNight()){
      OutputVoteMessage('assassin-do', 'assassin_do', $this->action, $this->not_action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  function SetVoteNight(){
    global $ROOM;

    parent::SetVoteNight();
    if($ROOM->IsEvent('force_assassin_do')) $this->SetStack(NULL, 'not_action');
  }

  //暗殺処理
  function Assassin($user){
    if($flag = $user->IsLive(true)) $this->AddSuccess($user->user_no, 'assassin');
    return $flag;
  }

  //暗殺死処理
  function AssassinKill(){
    global $USERS;
    foreach($this->GetStack() as $id => $flag) $USERS->Kill($id, 'ASSASSIN_KILLED');
  }
}
