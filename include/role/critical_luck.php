<?php
/*
  ¢¡ÄËº¨ (critical_luck)
  ¡û»ÅÍÍ
  ¡¦5% ¤Î³ÎÎ¨¤ÇÆÀÉ¼¿ô¤¬ +100 ¤µ¤ì¤ë
*/
class Role_critical_luck extends Role{
  function Role_critical_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number += mt_rand(1, 100) <= 5 ? 100 : 0;
  }
}
