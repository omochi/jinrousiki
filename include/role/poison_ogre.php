<?php
/*
  ◆榊鬼
  ○仕様
  ・勝利条件：出題者陣営の勝利、または自分自身の生存
*/
class Role_poison_ogre extends Role{
  function Role_poison_ogre(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function DistinguishVictory($victory){
    return $victory == 'quiz' || $this->IsLive();
  }
}
