<?php
/*
  ◆榊鬼 (poison_ogre)
  ○仕様
  ・勝利条件：出題者陣営の勝利、または自分自身の生存
  ・人攫い無効：出題者
  ・人攫い：解答者付加
  ・毒：人狼系 + 妖狐陣営 + 鬼陣営
*/
RoleManager::LoadFile('ogre');
class Role_poison_ogre extends Role_ogre{
  public $reduce_rate = 3;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return $user->IsRole('quiz'); }

  function Assassin($user, &$list){ $user->AddRole('panelist'); }

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
