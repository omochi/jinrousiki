<?php
/*
  ◆狩人 (guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：通常
*/
class Role_guard extends Role{
  public $action = 'GUARD_DO';
  public $ignore_message = '初日は護衛できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2){
      OutputSelfAbilityResult('GUARD_SUCCESS'); //護衛結果
      if(! $ROOM->IsOption('seal_message')) OutputSelfAbilityResult('GUARD_HUNTED');  //狩り結果
    }
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'guard_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  //護衛先セット
  function SetGuard($uname){
    global $ROOM, $ROLES;

    if($ROOM->IsEvent('no_contact')) return false; //花曇ならスキップ

    $this->AddStack($uname, 'guard');
    foreach($ROLES->LoadFilter('trap') as $filter){ //罠判定
      if($filter->DelayTrap($this->GetActor(), $uname)) break;
    }
    return true;
  }

  //護衛成功者検出
  function GetGuard($uname, &$list){ $list = array_keys($this->GetStack('guard'), $uname); }

  //護衛失敗判定
  function GuardFailed(){ return false; }

  //護衛処理
  function GuardAction($user, $flag = false){}

  //狩り
  function Hunt($user){
    global $ROOM, $USERS, $ROLES;

    //対象が身代わり死していた場合はスキップ
    if(in_array($user->uname, $ROLES->stack->sacrifice) || ! $this->IsHunt($user)) return false;
    $USERS->Kill($user->user_no, 'HUNTED');
    if(! $ROOM->IsOption('seal_message')){ //狩りメッセージを登録
      $ROOM->SystemMessage($this->GetActor()->GetHandleName($user->uname), 'GUARD_HUNTED');
    }
  }

  //狩り対象判定
  function IsHunt($user){ return $this->IsHuntTarget($user); }

  //狩り対象判定
  function IsHuntTarget($user){
    return $user->IsRole(
      'phantom_fox', 'voodoo_fox', 'revive_fox', 'possessed_fox', 'doom_fox', 'trap_fox',
      'cursed_fox', 'cursed_angel', 'poison_chiroptera', 'cursed_chiroptera', 'boss_chiroptera',
      'cursed_avenger', 'critical_avenger') ||
      ($user->IsRoleGroup('mad') &&
       ! $user->IsRole('mad', 'fanatic_mad', 'whisper_mad', 'therian_mad', 'immolate_mad')) ||
      ($user->IsRoleGroup('vampire') && ! $user->IsRole('vampire', 'scarlet_vampire'));
  }
}
