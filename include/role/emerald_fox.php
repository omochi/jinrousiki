<?php
/*
  ◆翠狐 (emerald_fox)
  ○仕様
  ・占い：共鳴
*/
RoleManager::LoadFile('fox');
class Role_emerald_fox extends Role_fox{
  public $mix_in = 'mage';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if($ROOM->IsNight() && $this->GetActor()->IsActive()){
      OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
    }
  }

  function Mage($user){
    if($this->IsJammer($user) || $this->IsCursed($user)) return false;
    if(! $user->IsChildFox() && ! $user->IsLonely('fox')) return false;
    $role = $this->GetActor()->GetID('mind_friend');
    $this->GetActor()->LostAbility();
    $this->GetActor()->AddRole($role);
    $user->AddRole($role);
  }
}
