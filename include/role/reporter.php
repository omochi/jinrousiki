<?php
/*
  ◆ブン屋 (reporter)
  ○仕様
*/
class Role_reporter extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult('REPORTER_SUCCESS'); //尾行結果
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO');
    }
  }
}
