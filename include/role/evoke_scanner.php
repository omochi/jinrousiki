<?php
/*
  ◆イタコ (evoke_scanner)
  ○仕様
  ・追加役職：口寄せ
*/
class Role_evoke_scanner extends Role{
  function __construct(){ parent::__construct(); }

  function AddScanRole($user){ $user->AddRole($this->GetActor()->GetID('mind_evoke')); }
}
