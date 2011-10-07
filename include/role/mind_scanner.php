<?php
/*
  ◆さとり (mind_scanner)
  ○仕様
  ・追加役職：サトラレ
  ・投票結果：なし
  ・投票：1日目のみ
*/
class Role_mind_scanner extends Role{
  public $action = 'MIND_SCANNER_DO';
  public $mind_role = 'mind_read';
  public $result = NULL;
  public $ignore_message = '初日以外は投票できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2 && isset($this->result)) OutputSelfAbilityResult($this->result);
    if($ROOM->date > 1 && isset($this->mind_role)){
      $id = $this->GetActor()->user_no;
      $stack = array();
      foreach($this->GetUser() as $user){
	if($user->IsPartner($this->mind_role, $id)) $stack[] = $user->handle_name;
      }
      OutputPartner($stack, 'mind_scanner_target');
    }
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date == 1;
  }

  function IsVoteCheckbox($user, $live){
    return parent::IsVoteCheckbox($user, $live) && ! $user->IsDummyBoy();
  }

  function IgnoreVoteNight($user, $live){
    if(! is_null($str = parent::IgnoreVoteNight($user, $live))) return $str;
    return $user->IsDummyBoy() ? '身代わり君には投票できません' : NULL;
  }

  //透視処理
  function MindScan($user){ $user->AddRole($this->GetActor()->GetID($this->mind_role)); }
}
