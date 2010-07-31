<?php
/*
  ¢¡·èÄê¼Ô (decide)
  ¡û»ÅÍÍ
  ¡¦½è·ºÅêÉ¼¤¬ÙÉ¹³¤·¤¿¤é¼«Ê¬¤ÎÅêÉ¼Àè¤¬½è·º¤µ¤ì¤ë
*/
class Role_decide extends Role{
  function Role_decide(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->decide = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;
    if($uname == '' && in_array($ROLES->stack->decide, $ROLES->stack->vote_possible)){
      $uname = $ROLES->stack->decide;
    }
  }
}
