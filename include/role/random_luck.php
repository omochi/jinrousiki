<?php
/*
  ¢¡ÇÈÍðËü¾æ (random_luck)
  ¡û»ÅÍÍ
  ¡¦ÆÀÉ¼¿ô¤Ë -2¡Á+2 ¤ÎÈÏ°Ï¤Ç¥é¥ó¥À¥à¤ËÊäÀµ¤¬¤«¤«¤ë
*/
class Role_random_luck extends Role{
  function Role_random_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number += (mt_rand(1, 5) - 3);
  }
}
