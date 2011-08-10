<?php
/*
  ◆無鉄砲者 (cowboy_duelist)
  ○仕様
  ・追加役職：なし
  ・投票数：-1
*/
class Role_cowboy_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){ return $this->GetActor()->GetID('rival'); }

  function FilterVoteDo(&$vote_number){ $vote_number--; }
}
