<?php
/*
  ◆古狼 (elder_wolf)
  ○仕様
  ・投票数：+1
*/
class Role_elder_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){ $vote_number++; }
}
