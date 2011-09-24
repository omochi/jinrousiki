<?php
/*
  ◆戦乙女 (valkyrja_duelist)
  ○仕様
  ・追加役職：なし
*/
class Role_valkyrja_duelist extends Role{
  public $partner_role   = 'rival';
  public $partner_header = 'duelist_pair';
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    $id = $this->GetActor()->user_no;
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsPartner($this->partner_role, $id)) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, $this->partner_header);
    if($ROOM->date == 2 && isset($this->result)) OutputSelfAbilityResult($this->result);
    if($ROOM->date == 1 && $ROOM->IsNight()){
      OutputVoteMessage('duelist-do', 'duelist_do', 'DUELIST_DO');
    }
  }

  function GetRole($user){ return $this->GetActor()->GetID($this->partner_role); }
}
