<?php
/*
  ◆念騒霊 (telepath_scanner)
  ○仕様
*/
RoleManager::LoadFile('whisper_scanner');
class Role_telepath_scanner extends Role_whisper_scanner{
  function __construct(){ parent::__construct(); }
}