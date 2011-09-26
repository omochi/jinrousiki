<?php
/*
  ◆さとり (mind_scanner)
  ○仕様
  ・追加役職：サトラレ
  ・投票結果：なし
  ・投票：1日目のみ
*/
class Role_mind_scanner extends Role{
  public $mind_role = 'mind_read';
  public $result = NULL;
  function __construct(){ parent::__construct(); }

  //役職情報表示
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
      OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
    }
  }

  //投票能力判定
  function IsVote(){
    global $ROOM;
    return $ROOM->date == 1;
  }

  //透視処理
  function MindScan($user){ $user->AddRole($this->GetActor()->GetID($this->mind_role)); }
}
