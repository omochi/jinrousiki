<?php
/*
  ◆元神話マニア (copied)
  ○仕様
  ・結果表示：2日目
*/
class Role_copied extends Role{
  public $display_date = 2;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date == $this->display_date) OutputSelfAbilityResult('MANIA_RESULT');
  }
}
