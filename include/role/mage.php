<?php
/*
  ◆占い師 (mage)
  ○仕様
*/
class Role_mage extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1) OutputSelfAbilityResult('MAGE_RESULT');
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
  }
}
