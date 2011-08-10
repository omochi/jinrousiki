<?php
/*
  ◆薔薇天使 (rose_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：両方男性
*/
class Role_rose_angel extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){ return $this->GetActor()->GetID('lovers'); }

  function IsSympathy($lovers_a, $lovers_b){
    return $lovers_a->IsMale() && $lovers_b->IsMale();
  }
}
