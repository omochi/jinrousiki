<?php
/*
  ◆執筆者 (scripter)
  ○仕様
  ・投票数：+1 (5日目以降)
*/
class Role_scripter extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 4) OutputAbilityResult('ability_scripter', NULL); //能力発現
  }

  function FilterVoteDo(&$number){
    global $ROOM;
    if($ROOM->date > 4) $number++;
  }
}
