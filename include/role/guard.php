<?php
/*
  ◆狩人 (guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：通常
*/
class Role_guard extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2){
      OutputSelfAbilityResult('GUARD_SUCCESS'); //護衛結果
      if(! $ROOM->IsOption('seal_message')) OutputSelfAbilityResult('GUARD_HUNTED');  //狩り結果
    }
    //投票
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO');
  }

  //護衛先セット
  function SetGuardTarget($uname){
    global $ROOM, $ROLES;

    if($ROOM->IsEvent('no_contact')) return false; //花曇ならスキップ

    $user = $this->GetActor();
    $ROLES->stack->guard[$user->uname] = $uname;

    if(in_array($uname, $ROLES->stack->trap)) //罠死判定
      $ROLES->stack->trapped[] = $user->uname;
    elseif(in_array($uname, $ROLES->stack->snow_trap)) //凍傷判定
      $ROLES->stack->frostbite[] = $user->uname;
    return true;
  }

  //護衛失敗判定
  function GuardFailed(){ return false; }

  //護衛処理
  function GuardAction($user, $flag = false){}

  //狩り対象判定
  function IsHuntTarget($user){ return $user->IsHuntTarget(); }
}
