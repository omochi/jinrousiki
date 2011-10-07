<?php
/*
  ◆厄神 (anti_voodoo)
  ○仕様
*/
class Role_anti_voodoo extends Role{
  public $action = 'ANTI_VOODOO_DO';
  public $result = 'ANTI_VOODOO_SUCCESS';
  public $ignore_message = '初日の厄払いはできません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    //厄払い結果
    if($ROOM->date > 2 && ! $ROOM->IsOption('seal_message')) OutputSelfAbilityResult($this->result);
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('guard-do', 'anti_voodoo_do', $this->action);
    }
  }

  //投票能力判定
  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
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
    $this->AddSuccess($user->uname, $this->role . '_success');
  }

  //厄払い成立判定
  function IsGuard($uname){
    if(! in_array($uname, $this->GetStack())) return false;
    $this->AddSuccess($uname, $this->role . '_success');
    return true;
  }

  //対呪い処理
  function GuardCurse($user){
    global $USERS;

    if($this->IsGuard($user->uname)) return false;
    $USERS->Kill($user->user_no, 'CURSED');
    return true;
  }

  //成功結果登録
  function SaveSuccess(){
    global $ROOM, $USERS;

    foreach($this->GetStack($this->role . '_success') as $target_uname => $flag){
      $str = "\t" . $USERS->GetHandleName($target_uname, true);
      foreach(array_keys($this->GetStack(), $target_uname) as $uname){ //成功者を検出
	$ROOM->SystemMessage($USERS->GetHandleName($uname) . $str, $this->result);
      }
    }
  }
}
