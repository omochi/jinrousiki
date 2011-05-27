<?php
/*
  ◆件
  ○仕様
  ・追加役職：受託者
  ・人狼襲撃：受託者に襲撃者を通知
*/
class Role_presage_scanner extends Role{
  function __construct(){ parent::__construct(); }

  function AddScanRole($user){ $user->AddRole($this->GetActor()->GetID('mind_presage')); }

  function WolfEatCounter($target){
    global $ROOM, $USERS;

    $actor = $this->GetActor();
    foreach($USERS->rows as $user){
      if($user->IsPartner('mind_presage', $actor->user_no)){
	$str = $user->handle_name . "\t" .
	  $USERS->GetHandleName($actor->uname, true) . "\t" .
	  $USERS->GetHandleName($target->uname, true);
	$ROOM->SystemMessage($str, 'PRESAGE_RESULT');
	break;
      }
    }
  }
}
