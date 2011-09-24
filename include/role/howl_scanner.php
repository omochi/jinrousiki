<?php
/*
  ◆吠騒霊 (howl_scanner)
  ○仕様
*/
RoleManager::LoadFile('whisper_scanner');
class Role_howl_scanner extends Role_whisper_scanner{
  function __construct(){ parent::__construct(); }
}
