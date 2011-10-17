<?php
/*
  ◆罠師 (trap_mad)
  ○仕様
*/
class Role_trap_mad extends Role{
  public $action     = 'TRAP_MAD_DO';
  public $not_action = 'TRAP_MAD_NOT_DO';
  public $submit     = 'trap_do';
  public $not_submit = 'trap_not_do';
  public $ignore_message = '初日は罠を設置できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    $this->OutputAction();
  }

  function OutputAction(){
    global $ROOM;
    if($this->IsVote() && $this->IsVoteTrap() && $ROOM->IsNight()){
      OutputVoteMessage('wolf-eat', $this->submit, $this->action, $this->not_action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  //罠能力判定
  function IsVoteTrap(){ return $this->GetActor()->IsActive(); }

  function IgnoreVote(){
    if(! is_null($str = parent::IgnoreVote())) return $str;
    return $this->IsVoteTrap() ? NULL : '能力喪失しています';
  }

  function IsVoteCheckbox($user, $live){ return $live; }

  function IgnoreVoteNight($user, $live){ return $live ? NULL : '死者には投票できません'; }

  //罠設置
  function SetTrap($uname){
    //人狼に狙われていたら自己設置以外は無効
    if($this->IsActor($this->GetWolfTarget()->uname) && ! $this->IsActor($uname)) return;
    $this->SetTrapAction($this->GetActor(), $uname);
  }

  //罠設置後処理
  function SetTrapAction($user, $uname){
    $this->AddStack($uname, 'trap', $user->uname);
    $user->LostAbility();
  }

  //罠能力者の罠判定
  function TrapToTrap(){
    global $ROLES;

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $stack = array_count_values($ROLES->stack->trap);
    foreach($ROLES->stack->trap as $uname => $target_uname){
      if($uname != $target_uname && $stack[$target_uname] > 1) $ROLES->stack->trapped[] = $uname;
    }

    foreach($ROLES->stack->snow_trap as $uname => $target_uname){ //雪女の罠死判定
      if($uname != $target_uname && in_array($target_uname, $ROLES->stack->trap)){
	$ROLES->stack->trapped[] = $uname;
      }
    }
  }

  //罠死判定
  function TrapKill($user, $uname){
    global $USERS, $ROLES;

    $flag = in_array($uname, $ROLES->stack->trap);
    if($flag) $USERS->Kill($user->user_no, 'TRAPPED');
    return $flag;
  }

  //罠死リスト判定
  function DelayTrap($user, $uname){
    global $ROLES;

    $flag = in_array($uname, $ROLES->stack->trap);
    if($flag) $ROLES->stack->trapped[] = $user->uname;
    return $flag;
  }

  //罠死+凍傷リスト判定
  function TrapStack($user, $uname){ return $this->TrapKill($user, $uname); }

  //罠死リストの死亡処理
  function DelayTrapKill(){
    global $USERS, $ROLES;
    while($uname = array_shift($ROLES->stack->trapped)){
      $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');
    }
  }
}
