<?php
/*
  ◆悲恋 (sweet_status)
  ○仕様
*/
class Role_sweet_status extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    OutputPartner($this->GetLovers(), 'partner_header', 'lovers_footer');
    if($ROOM->date == 2) parent::OutputAbility();
  }

  //仮想恋人も含めた恋人を取得
  function GetLovers(){
    global $ROOM, $USERS;

    $stack = array();
    $actor = $this->GetActor();
    if($actor->IsRole('lovers')) return $stack; //恋人入りなら恋人側で処理
    foreach($this->GetUser() as $user){
      if($this->IsActor($user->uname)) continue;
      if($actor->IsPartner('dummy_chiroptera', $user->user_no) ||
	 ($ROOM->date == 1 && $user->IsPartner($this->role, $actor->partner_list))){
	$stack[] = $USERS->GetHandleName($user->uname, true); //憑依を追跡する
      }
    }
    return $stack;
  }
}
