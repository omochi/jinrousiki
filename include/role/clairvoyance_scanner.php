<?php
/*
  ◆猩々 (clairvoyance_scanner)
  ○仕様
  ・追加役職：なし
  ・投票結果：透視
  ・投票：2日目以降
*/
RoleManager::LoadFile('mind_scanner');
class Role_clairvoyance_scanner extends Role_mind_scanner{
  public $mind_role = NULL;
  public $result = 'CLAIRVOYANCE_RESULT';
  function __construct(){ parent::__construct(); }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }
}
