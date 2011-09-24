<?php
/*
  ◆天使 (angel)
  ○仕様
  ・共感者判定：男女
*/
RoleManager::LoadFile('cupid');
class Role_angel extends Role_cupid{
  function __construct(){ parent::__construct(); }

  function IsSympathy($lovers_a, $lovers_b){ return $lovers_a->sex != $lovers_b->sex; }
}
