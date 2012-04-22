<?php
/*
  ◆吸血姫 (soul_vampire)
  ○仕様
  ・対吸血：反射
  ・吸血：役職取得
*/
RoleManager::LoadFile('vampire');
class Role_soul_vampire extends Role_vampire {
  public $result = 'VAMPIRE_RESULT';
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date > 2) OutputSelfAbilityResult($this->result);
  }

  protected function InfectVampire($user){
    $this->AddSuccess($user->user_no, 'vampire_kill');
  }

  function Infect($user){
    parent::Infect($user);
    $target = DB::$USER->GetHandleName($user->uname, true);
    DB::$ROOM->ResultAbility($this->result, $user->main_role, $target, $this->GetActor()->user_no);
  }
}
