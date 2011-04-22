<?php
/*
  ◆魔法使い
  ○仕様
  ・魔法：占い師・精神鑑定士・ひよこ鑑定士・狩人・暗殺者
*/
class Role_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'mage';
    if($ROOM->IsEvent('debilitate_wizard')) return 'sex_mage';
    $stack = array('mage', 'psycho_mage', 'sex_mage', 'guard', 'assassin');
    return GetRandom($stack);
  }
}
