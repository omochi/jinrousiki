<?php
/*
  ◆無精者 (reduce_voter)
  ○仕様
  ・投票数が -1 される
*/
class Role_reduce_voter extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){ $vote_number--; }
}
