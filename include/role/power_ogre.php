<?php
/*
  ◆星熊童子
  ○仕様
  ・勝利条件：自分自身の生存 + 人口を三分の一以下にする
*/
class Role_power_ogre extends Role{
  var $resist_rate = 40;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 7 / 10; }

  function DistinguishVictory($victory){
    global $USERS;
    return $this->IsLive() && count($USERS->GetLivingUsers()) <= ceil(count($USERS->rows) / 3);
  }
}
