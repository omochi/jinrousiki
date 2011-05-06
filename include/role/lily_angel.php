<?php
/*
  ◆百合天使 (lily_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：両方女性
*/
class Role_lily_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){ return true; }

  function IsSympathy($lovers_a, $lovers_b){
    return $lovers_a->sex == 'female' && $lovers_b->sex == 'female';
  }
}
