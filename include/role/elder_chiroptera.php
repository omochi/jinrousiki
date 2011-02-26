<?php
/*
  ◆古蝙蝠 (elder_chiroptera)
  ○仕様
  ・投票数が +1 される
*/
class Role_elder_chiroptera extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
