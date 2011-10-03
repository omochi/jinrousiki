<?php
/*
  ◆妖精 (fairy)
  ○仕様
  ・悪戯：発言妨害 (共有者の囁き)
*/
class Role_fairy extends Role{
  public $mix_in = 'mage';
  public $action = 'FAIRY_DO';
  public $bad_status = NULL;
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    parent::OutputAbility();
    if($this->IsVote()) OutputVoteMessage('fairy-do', 'fairy_do', $this->action);
  }

  //投票能力判定
  function IsVote(){
    global $ROOM;
    return $ROOM->IsNight();
  }

  //占い (悪戯)
  function Mage($user){
    if($this->IsJammer($user) || $this->IsCursed($user)) return false;
    $this->FairyAction($user);
  }

  //悪戯
  function FairyAction($user){
    global $ROOM;
    $date = $ROOM->date + 1;
    $user->AddRole('bad_status[' . $this->GetActor()->user_no . '-' . $date . ']');
  }

  //発言変換 (悪戯)
  function FilterSay(&$str){ $str = $this->GetBadStatus() . $str; }

  //悪戯内容取得
  function GetBadStatus(){
    global $MESSAGE;
    return is_null($this->bad_status) ? $MESSAGE->common_talk : $this->bad_status;
  }
}
