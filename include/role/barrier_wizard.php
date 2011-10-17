<?php
/*
  ◆結界師 (barrier_wizard)
  ○仕様
  ・護衛失敗：特殊 (別判定)
  ・護衛処理：なし
*/
RoleManager::LoadFile('wizard');
class Role_barrier_wizard extends Role_wizard{
  public $action = 'SPREAD_WIZARD_DO';
  public $submit = 'wizard_do';
  public $wizard_list = array('barrier_wizard' => 'SPREAD_WIZARD_DO');
  public $result_list = array('GUARD_SUCCESS');
  function __construct(){ parent::__construct(); }

  function GetVoteCheckboxHeader(){ return '<input type="checkbox" name="target_no[]"'; }

  function VoteNight(){
    global $USERS;

    $stack = $this->GetVoteNightTarget();
    //人数チェック
    if(count($stack) < 1 || 4 < count($stack)) return '指定人数は1～4人にしてください';

    $user_list = array();
    foreach($stack as $id){
      $user = $USERS->ByID($id);
      //例外判定
      if($this->IsActor($user->uname) || ! $USERS->IsVirtualLive($id) || $user->IsDummyBoy()){
	return '自分・死者・身代わり君には投票できません';
      }
      $user_list[$id] = $user;
    }

    $uname_stack  = array();
    $handle_stack = array();
    foreach($user_list as $id => $user){
      $uname_stack[] = $USERS->ByReal($id)->user_no;
      $handle_stack[$id] = $user->handle_name;
    }
    sort($uname_stack);
    ksort($handle_stack);

    $this->SetStack(implode(' ', $uname_stack), 'target_uname');
    $this->SetStack(implode(' ', $handle_stack), 'target_handle');
    return NULL;
  }

  function SetGuard($list){
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

  function GetGuard($uname, &$list){
    $rate  = $this->GetGuardRate();
    foreach($this->GetStack() as $target_uname => $target_list){
      if(in_array($uname, $target_list) &&
	 mt_rand(1, 100) <= (100 - count($target_list) * 20) * $rate) $list[] = $target_uname;
    }
  }

  //護衛成功係数取得
  private function GetGuardRate(){
    global $ROOM;
    return $ROOM->IsEvent('full_wizard') ? 1.25 : ($ROOM->IsEvent('debilitate_wizard') ? 0.75 : 1);
  }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag){}
}
