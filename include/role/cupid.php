<?php
/*
  ◆キューピッド (cupid)
  ○仕様
  ・追加役職：なし
*/
class Role_cupid extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    //自分が矢を打った恋人 (自分自身含む) を表示
    $id = $this->GetActor()->user_no;
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $id)) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'cupid_pair');
    $this->OutputCupidAbility();
    if($ROOM->date == 1 && $ROOM->IsNight()) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO');
  }

  //特殊キューピッドの情報表示
  function OutputCupidAbility(){}

  //追加役職セット
  function GetRole($user, $flag){ return $this->GetActor()->GetID('lovers'); }
}
