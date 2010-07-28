<?php
/*
  ◆権力者 (authority)
  ○仕様
  ・投票数が +1 される
*/
class Role_authority extends Role{
  function Role_authority(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
