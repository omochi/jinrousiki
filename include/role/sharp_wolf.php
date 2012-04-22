<?php
/*
  ◆鋭狼 (sharp_wolf)
  ○仕様
  ・襲撃：危機回避
*/
RoleManager::LoadFile('wolf');
class Role_sharp_wolf extends Role_wolf {
  public $result = 'SHARP_WOLF_RESULT';
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date > 1 && ! DB::$ROOM->IsOption('seal_message')) {
      OutputSelfAbilityResult($this->result);
    }
  }

  function WolfEatAction($user){
    if (! $user->IsRoleGroup('mad') && ! $user->IsPoison()) return false;
    if (DB::$ROOM->IsOption('seal_message')) return true;
    $target = DB::$USER->GetHandleName($user->uname, true);
    DB::$ROOM->ResultAbility($this->result, 'wolf_avoid', $target, $this->GetWolfVoter()->user_no);
    return true;
  }
}
