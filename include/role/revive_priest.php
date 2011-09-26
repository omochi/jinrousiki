<?php
/*
  ◆天人 (revive_priest)
  ○仕様
  ・結果表示：なし
*/
RoleManager::LoadFile('priest');
class Role_revive_priest extends Role_priest{
  public $result_date = NULL;
  function __construct(){ parent::__construct(); }

  function Priest($role_flag, $data){
    global $ROOM, $USERS;

    if($ROOM->date != 4 && $data->crisis == '' && $data->count['wolf'] != 1 &&
       count($USERS->rows) < $data->count['total'] * 2) return false;

    foreach($role_flag->{$this->role} as $uname){
      $user = $USERS->ByUname($uname);
      if($user->IsLovers() || ($ROOM->date >= 4 && $user->IsLive(true))){
	$user->LostAbility();
      }
      elseif($user->IsDead(true)){
	$user->Revive();
	$user->LostAbility();
      }
    }
  }

  //帰還処理
  function PriestReturn(){
    global $USERS;

    $user = $this->GetActor();
    if($user->IsDummyBoy()) return;
    if($user->IsLovers()) $user->LostAbility();
    elseif($user->IsLive(true)) $USERS->Kill($user->user_no, 'PRIEST_RETURNED');
  }
}
