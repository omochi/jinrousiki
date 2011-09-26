<?php
/*
  ◆鵺 (unknown_mania)
  ○仕様
  ・追加役職：なし
*/
RoleManager::LoadFile('mania');
class Role_unknown_mania extends Role_mania{
  public $camp_copy = true;
  function __construct(){
    parent::__construct();
    $this->copied = $this->GetActor()->GetID('mind_friend');
  }

  function CopyAction($user, $role){
    $user->AddRole($this->copied . (is_null($role) ? '' : ' ' . $role));
  }

  function GetRole($user){ return $this->GetCopyRole($this->GetActor()); }

  function GetCopyRole($user){ return NULL; }
}
