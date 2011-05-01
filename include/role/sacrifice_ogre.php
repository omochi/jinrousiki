<?php
/*
  ◆酒呑童子
  ○仕様
  ・勝利条件：自分自身の生存 + 村人陣営以外の勝利
  ・身代わり対象者：洗脳者
*/
class Role_sacrifice_ogre extends Role{
  var $resist_rate = 0;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 2; }

  function DistinguishVictory($victory){ return $victory != 'human' && $this->IsLive(); }

  function GetSacrificeList(){
    global $USERS;

    $stack = array();
    foreach($USERS->rows as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLiveRole('psycho_infected', true)){
	$stack[] = $user->user_no;
      }
    }
    return $stack;
  }
}
