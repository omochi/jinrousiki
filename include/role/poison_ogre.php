<?php
/*
  ◆榊鬼
  ○仕様
  ・勝利条件：出題者陣営の勝利、または自分自身の生存
  ・毒：人狼系 + 妖狐陣営 + 鬼陣営
*/
class Role_poison_ogre extends Role{
  public $resist_rate = 30;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 3; }

  function Win($victory){ return $victory == 'quiz' || $this->IsLive(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox', 'ogre', 'yaksa')){
	$stack[] = $uname;
      }
    }
    $list = $stack;
  }
}
