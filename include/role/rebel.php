<?php
/*
  ¢¡È¿µÕ¼Ô (rebel)
  ¡û»ÅÍÍ
  ¡¦¸¢ÎÏ¼Ô¤ÈÆ±¤¸¿Í¤ËÅêÉ¼¤¹¤ë¤È£°É¼¤Ë¤Ê¤ë
*/
class Role_rebel extends Role{
  function Role_rebel(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->rebel = $ROLES->actor->uname;
    $ROLES->stack->rebel_uname = $uname;
  }

  function FilterRebel(&$message_list, &$count_list){
    global $ROLES;

    //Ç½ÎÏÈ¯Æ°È½Äê
    if(is_null($ROLES->stack->authority) || is_null($ROLES->stack->rebel) ||
       $ROLES->stack->authority_uname != $ROLES->stack->rebel_uname) return;

    //¸¢ÎÏ¼Ô¤ÈÈ¿µÕ¼Ô¤ÎÅêÉ¼¿ô¤ò 0 ¤Ë¤¹¤ë
    $message_list[$ROLES->stack->authority]['vote_number'] = 0;
    $message_list[$ROLES->stack->rebel]['vote_number'] = 0;

    //ÅêÉ¼Àè¤ÎÆÀÉ¼¿ô¤òÊäÀµ¤¹¤ë
    $uname = $ROLES->stack->rebel_uname;
    if($message_list[$uname]['voted_number'] > 3)
      $message_list[$uname]['voted_number'] -= 3;
    else
      $message_list[$uname]['voted_number'] = 0;
    $count_list[$uname] = $message_list[$uname]['voted_number'];
  }
}
