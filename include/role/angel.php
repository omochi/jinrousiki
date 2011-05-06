<?php
/*
  ◆天使 (angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：男女
*/
class Role_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){ return true; }

  function IsSympathy($lovers_a, $lovers_b){
    return $lovers_a->sex != $lovers_b->sex ;
  }
}
