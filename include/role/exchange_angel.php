<?php
/*
  ◆魂移使 (exchange_angel)
  ○仕様
  ・共感者判定：特殊 (集計後)
*/
RoleManager::LoadFile('angel');
class Role_exchange_angel extends Role_angel{
  function __construct(){ parent::__construct(); }

  function IsSympathy($lovers_a, $lovers_b){ return false; }
}
