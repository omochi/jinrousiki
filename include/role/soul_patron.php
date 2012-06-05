<?php
/*
  ◆家神 (soul_patron)
  ○仕様
  ・追加役職：受援者の役職結果
*/
RoleManager::LoadFile('patron');
class Role_soul_patron extends Role_patron {
  public $result = 'PATRON_RESULT';
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date == 2) $this->OutputAbilityResult($this->result);
  }

  protected function AddDuelistRole($user){
    $id = $this->GetActor()->user_no;
    DB::$ROOM->ResultAbility($this->result, $user->main_role, $user->handle_name, $id);
  }
}
