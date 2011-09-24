<?php
/*
  ◆暗殺者 (assassin)
  ○仕様
  ・暗殺：標準
*/
class Role_assassin extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2 && isset($this->result)) OutputSelfAbilityResult($this->result); //暗殺結果
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }

  //暗殺処理
  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $list[$user->uname] = true;
  }
}
