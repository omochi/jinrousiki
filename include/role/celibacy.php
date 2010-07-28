<?php
/*
  ¢¡ÆÈ¿Èµ®Â² (celibacy)
  ¡û»ÅÍÍ
  ¡¦Îø¿Í¤ËÅêÉ¼¤µ¤ì¤¿¤é¥·¥ç¥Ã¥¯»à¤¹¤ë
*/
class Role_celibacy extends Role{
  function Role_celibacy(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $USERS;

    if($reason != '') return;
    foreach(array_keys($ROLES->stack->target, $ROLES->actor->uname) as $uname){
      if($USERS->ByUname($uname)->IsLovers()){
	$reason = 'CELIBACY';
	break;
      }
    }
  }
}
