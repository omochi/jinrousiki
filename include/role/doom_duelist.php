<?php
/*
  ◆黒幕 (doom_duelist)
  ○仕様
  ・追加役職：死の宣告 (7日目)
*/
class Role_doom_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){ return $this->GetActor()->GetID('rival') . ' death_warrant[7]'; }
}
