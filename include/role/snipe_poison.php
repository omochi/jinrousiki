<?php
/*
  ◆狙毒者 (snipe_poison)
  ○仕様
  ・毒：処刑投票先と同陣営(恋人は恋人陣営)
*/
class Role_snipe_poison extends Role{
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $camp  = $this->GetVoteUser()->GetCamp(true);
    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsCamp($camp, true)) $stack[] = $uname;
    }
    $list = $stack;
  }
}
