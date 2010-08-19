<?php
/*
  ◆凍傷 (frostbite)
  ○仕様
  ・発動当日に投票されていなかったらショック死する
*/
class Role_frostbite extends Role{
  function Role_frostbite(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $ROOM;
    if($reason == '' && $ROOM->date == max($ROLES->actor->GetPartner('frostbite')) &&
       $ROLES->stack->count[$ROLES->actor->uname] == 0) $reason = 'FROSTBITE';
  }
}
