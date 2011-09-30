<?php
/*
  ◆結界師 (barrier_wizard)
  ○仕様
  ・護衛失敗：特殊 (別判定)
  ・護衛処理：なし
*/
RoleManager::LoadFile('wizard');
class Role_barrier_wizard extends Role_wizard{
  public $wizard_list = array('barrier_wizard' => 'SPREAD_WIZARD_DO');
  public $result_list = array('GUARD_SUCCESS');
  public $action = 'SPREAD_WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function SetGuardTarget($list){
    global $USERS, $ROLES;

    $uname     = $this->GetActor()->uname;
    $trapped   = false;
    $frostbite = false;
    foreach(explode(' ', $list) as $id){
      $target_uname = $USERS->ByID($id)->uname;
      $ROLES->stack->{$this->role}[$uname][] = $target_uname;
      $trapped   |= in_array($target_uname, $ROLES->stack->trap); //罠死判定
      $frostbite |= in_array($target_uname, $ROLES->stack->snow_trap); //凍傷判定
    }
    if($trapped)
      $ROLES->stack->trapped[] = $uname;
    elseif($frostbite)
      $ROLES->stack->frostbite[] = $uname;
  }

  function GetRate(){
    global $ROOM;
    return $ROOM->IsEvent('full_wizard') ? 1.25 : ($ROOM->IsEvent('debilitate_wizard') ? 0.75 : 1);
  }

  function GetGuard($uname, &$list){
    $rate  = $this->GetRate();
    foreach($this->GetStack() as $target_uname => $target_list){
      if(in_array($uname, $target_list) &&
	 mt_rand(1, 100) <= (100 - count($target_list) * 20) * $rate) $list[] = $target_uname;
    }
  }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}
}
