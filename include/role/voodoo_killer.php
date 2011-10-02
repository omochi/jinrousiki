<?php
/*
  ◆陰陽師 (voodoo_killer)
  ○仕様
*/
class Role_voodoo_killer extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && ! $ROOM->IsOption('seal_message')){
      OutputSelfAbilityResult('VOODOO_KILLER_SUCCESS'); //解呪結果
    }
    //投票
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }

  //解呪処理
  function Mage($user){
    global $USERS;

    //呪殺判定 (呪い所持者・憑依能力者)
    if($user->IsLive(true) && ($user->IsRoleGroup('cursed') || $user->IsPossessedGroup())){
      $USERS->Kill($user->user_no, 'CURSED');
      $this->AddSuccess($user->uname, 'voodoo_killer_success');
    }
    if(count($stack = array_keys($this->GetStack('possessed'), $user->uname)) > 0){ //憑依妨害判定
      foreach($stack as $uname) $USERS->ByUname($uname)->possessed_cancel = true;
      $this->AddSuccess($user->uname, 'voodoo_killer_success');
    }
    $this->AddStack($user->uname); //解呪対象リストに追加
  }
}
