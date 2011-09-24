<?php
/*
  ◆厄神 (anti_voodoo)
  ○仕様
*/
class Role_anti_voodoo extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2 && ! $ROOM->IsOption('seal_message')){
      OutputSelfAbilityResult('ANTI_VOODOO_SUCCESS'); //厄払い結果
    }
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO');
    }
  }
}
