<?php
/*
  ◆古狐 (elder_fox)
  ○仕様
  ・投票数が +1 される
*/
class Role_elder_fox extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
