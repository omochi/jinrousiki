<?php
/*
  ¢¡ÉÔ±¿ (bad_luck)
  ¡û»ÅÍÍ
  ¡¦½è·ºÅêÉ¼¤¬ÙÉ¹³¤·¤¿¤é¼«Ê¬¤¬½è·º¤µ¤ì¤ë
*/
class Role_bad_luck extends Role{
  function Role_bad_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->bad_luck = $ROLES->actor->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;
    if($uname == '' && in_array($ROLES->stack->bad_luck, $ROLES->stack->vote_possible)){
      $uname = $ROLES->stack->bad_luck;
    }
  }
}
