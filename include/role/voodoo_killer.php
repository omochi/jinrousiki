<?php
/*
  ◆陰陽師 (voodoo_killer)
  ○仕様
*/
class Role_voodoo_killer extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && ! $ROOM->IsOption('seal_message')){
      OutputSelfAbilityResult('VOODOO_KILLER_SUCCESS'); //解呪結果
    }
    //投票
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
}
