<?php
/*
  ◆左道使い
  ○仕様
  ・魔法：反魂師・月兎・呪術師・獏・雪女・冥狐・闇妖精
  ・魔法結果：なし
*/
class Role_astray_wizard extends Role{
  public $action = 'WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'reverse_assassin';
    if($ROOM->IsEvent('debilitate_wizard')) return 'dark_fairy';
    $stack = array('reverse_assassin', 'jammer_mad', 'voodoo_mad', 'dream_eater_mad',
		   'snow_trap_mad', 'doom_fox', 'dark_fairy');
    return GetRandom($stack);
  }

  function OutputResult(){}
}
