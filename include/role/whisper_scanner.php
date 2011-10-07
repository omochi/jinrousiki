<?php
/*
  ◆囁騒霊 (whisper_scanner)
  ○仕様
  ・追加役職：なし
  ・投票：なし
*/
RoleManager::LoadFile('mind_scanner');
class Role_whisper_scanner extends Role_mind_scanner{
  public $action = NULL;
  public $mind_role = NULL;
  function __construct(){ parent::__construct(); }

  function IsVote(){ return false; }
}
