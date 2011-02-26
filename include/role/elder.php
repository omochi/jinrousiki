<?php
/*
  ◆長老 (elder)
  ○仕様
  ・投票数が +1 される
*/
class Role_elder extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
