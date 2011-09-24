<?php
/*
  ◆妖精 (fairy)
  ○仕様
  ・悪戯：文頭に共有者の囁きを追加する
*/
class Role_fairy extends Role{
  public $action = 'FAIRY_DO';
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

  //発言変換 (悪戯)
  function FilterSay(&$str){
    global $MESSAGE;
    $str = $MESSAGE->common_talk . $str;
  }
}
