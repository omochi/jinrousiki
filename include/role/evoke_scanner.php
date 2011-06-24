<?php
/*
  ◆イタコ (evoke_scanner)
  ○仕様
  ・追加役職：口寄せ
*/
class Role_evoke_scanner extends Role{
  public $mind_role = 'mind_evoke';

  function __construct(){ parent::__construct(); }
}
