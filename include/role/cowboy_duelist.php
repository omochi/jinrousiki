<?php
/*
  ◆無鉄砲者 (cowboy_duelist)
  ○仕様
  ・追加役職：なし
  ・処刑投票：投票数が -1 される
*/
class Role_cowboy_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){}

  function FilterVoteDo(&$vote_number){ $vote_number--; }
}
