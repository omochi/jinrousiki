<?php
/*
  ◆厄神 (anti_voodoo)
  ○仕様
*/
class Role_anti_voodoo extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2 && ! $ROOM->IsOption('seal_message')){
      OutputSelfAbilityResult('ANTI_VOODOO_SUCCESS'); //厄払い結果
    }
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO');
    }
  }

  //厄払い先セット
  function SetGuard($user){
    global $USERS;

    $this->AddStack($user->uname);
    if(count($stack = array_keys($this->GetStack('possessed'), $user->uname)) > 0){ //憑依妨害判定
      foreach($stack as $uname) $USERS->ByUname($uname)->possessed_cancel = true;
    }
    //憑依者なら強制送還
    elseif($user->IsPossessedGroup() && $user != $USERS->ByVirtual($user->user_no)){
      if(! array_key_exists($user->uname, $this->GetStack('possessed'))){
	$this->AddSuccess($user->uname, 'possessed', true); //憑依リストに追加
      }
      $user->possessed_reset = true;
    }
    //襲撃を行った憑狼ならキャンセル
    elseif($this->GetVoter()->IsRole('possessed_wolf') && $this->GetVoter()->IsSame($user->uname)){
      $this->GetVoter()->possessed_cancel = true;
    }
    else return;
    $this->AddSuccess($user->uname, 'anti_voodoo_success');
  }

  //厄払い成立判定
  function IsGuard($uname){
    if(! in_array($uname, $this->GetStack())) return false;
    $this->AddSuccess($uname, 'anti_voodoo_success');
    return true;
  }

  //対呪い処理
  function GuardCurse($user){
    global $USERS;

    if($this->IsGuard($user->uname)) return false;
    $USERS->Kill($user->user_no, 'CURSED');
    return true;
  }

  //対占い妨害処理
  function GuardJammer($uname){
    if($flag = in_array($uname, $this->GetStack())){
      $this->AddSuccess($uname, 'anti_voodoo_success');
    }
    return ! $flag;
  }
}
