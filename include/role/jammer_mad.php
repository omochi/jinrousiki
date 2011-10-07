<?php
/*
  ◆月兎 (jammer_mad)
  ○仕様
*/
class Role_jammer_mad extends Role{
  public $action = 'JAMMER_MAD_DO';
  public $submit = 'jammer_do';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    $this->OutputAction();
  }

  function OutputAction(){
    global $ROOM;
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', $this->submit, $this->action);
  }

  //妨害対象セット
  function SetJammer($user){
    if($this->IsJammer($user)) $this->AddStack($user->uname, 'jammer');
  }

  //妨害対象セット成立判定
  function IsJammer($user){
    global $ROOM, $ROLES;

    $filter_list = $ROLES->LoadFilter('anti_voodoo'); //厄払い
    //呪返し判定
    if((! $ROOM->IsEvent('no_cursed') && $user->IsLiveRoleGroup('cursed')) ||
       in_array($user->uname, $this->GetStack('voodoo'))){
      foreach($filter_list as $filter){ //厄神の護衛判定
	if($filter->GuardCurse($this->GetActor())) return false;
      }
    }

    foreach($filter_list as $filter){ //厄神の妨害無効判定
      if($filter->IsGuard($user->uname)) return false;
    }
    return true;
  }
}
