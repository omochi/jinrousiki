<?php
/*
  ◆子狐 (child_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_child_fox extends Role_fox{
  public $result = 'CHILD_FOX_RESULT';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if(is_null($this->result)) return;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result); //占い結果
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //投票
  }
}
