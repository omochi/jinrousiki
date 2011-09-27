<?php
/*
  ◆雪女 (snow_trap_mad)
  ○仕様
*/
RoleManager::LoadFile('trap_mad');
class Role_snow_trap_mad extends Role_trap_mad{
  function __construct(){ parent::__construct(); }

  function IsVoteTrap(){ return true; }

  function SetTrapAction($user, $uname){
    global $ROLES;
    $ROLES->stack->snow_trap[$user->uname] = $uname;
  }

  function TrapToTrap(){
    global $ROLES;

    //雪女が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は凍傷になる
    $stack = array_count_values($ROLES->stack->snow_trap);
    foreach($ROLES->stack->snow_trap as $uname => $target_uname){
      if($uname != $target_uname && $stack[$target_uname] > 1) $ROLES->stack->frostbite[] = $uname;
    }

    foreach($ROLES->stack->trap as $uname => $target_uname){ //罠師の凍傷判定
      if($uname != $target_uname && in_array($target_uname, $ROLES->stack->snow_trap)){
	$ROLES->stack->frostbite[] = $uname;
      }
    }
  }

  function TrapKill($user, $uname){
    global $ROLES;
    if(in_array($uname, $ROLES->stack->snow_trap)) $user->AddDoom(1, 'frostbite');
  }

  function DelayTrap($user, $uname){
    global $ROLES;
    if(in_array($uname, $ROLES->stack->snow_trap)) $ROLES->stack->frostbite[] = $user->uname;
    return false;
  }

  function TrapStack($user, $uname){ return $this->DelayTrap($user, $uname); }
}
