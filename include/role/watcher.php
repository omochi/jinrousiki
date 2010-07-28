<?php
/*
  ¢¡Ëµ´Ñ¼Ô (watcher)
  ¡û»ÅÍÍ
  ¡¦ÅêÉ¼¿ô¤¬ 0 ¤Ç¸ÇÄê¤µ¤ì¤ë
*/
class Role_watcher extends Role{
  function Role_watcher(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number = 0;
  }
}
