<?php
/*
  ◆薔薇天使 (rose_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：両方男性
*/
class Role_rose_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){ return true; }

  function IsSympathy($lovers_a, $lovers_b){
    return $lovers_a->sex == 'male' && $lovers_b->sex == 'male';
  }
}
