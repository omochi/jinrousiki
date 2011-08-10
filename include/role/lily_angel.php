<?php
/*
  ◆百合天使 (lily_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：両方女性
*/
class Role_lily_angel extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){ return $this->GetActor()->GetID('lovers'); }

  function IsSympathy($lovers_a, $lovers_b){
    return $lovers_a->IsFemale() && $lovers_b->IsFemale();
  }
}
