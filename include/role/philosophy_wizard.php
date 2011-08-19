<?php
/*
  ◆賢者 (philosopy_wizard)
  ○仕様
  ・魔法：河童・錬金術師・蛇姫・火車・土蜘蛛・釣瓶落とし・弁財天
  ・魔法結果：薬師
*/
class Role_philosophy_wizard extends Role{
  public $action;

  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'alchemy_pharmacist';
    if($ROOM->IsEvent('debilitate_wizard')) return 'corpse_courier_mad';
    $stack = array('cure_pharmacist', 'alchemy_pharmacist', 'miasma_jealousy', 'corpse_courier_mad',
		   'miasma_mad', 'critical_mad', 'sweet_cupid');
    return GetRandom($stack);
  }

  function OutputResult(){ OutputSelfAbilityResult('PHARMACIST_RESULT'); }
}
