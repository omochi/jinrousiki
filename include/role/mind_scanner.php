<?php
/*
  ◆さとり (mind_scanner)
  ○仕様
  ・追加役職：サトラレ
*/
class Role_mind_scanner extends Role{
  function __construct(){ parent::__construct(); }

  function AddScanRole($user){ $user->AddRole($this->GetActor()->GetID('mind_read')); }
}
