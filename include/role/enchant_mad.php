<?php
/*
  ◆狢 (enchant_mad)
  ○仕様
  ・悪戯：迷彩 (同一アイコン)
*/
class Role_enchant_mad extends Role{
  public $mix_in = 'fairy';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->IsNight()) OutputVoteMessage('fairy-do', 'fairy_do', 'FAIRY_DO');
  }

  function SetVoteNight(){ $this->filter->SetVoteNight(); }

  function SetEvent($user){
    global $ROOM;
    $ROOM->event->same_face[] = $user->user_no;
  }

  function BadStatus(){
    global $ROOM, $USERS;

    if(! property_exists($ROOM->event, 'same_face')) return;
    $target = $USERS->ById(GetRandom($ROOM->event->same_face));
    if(! property_exists($target, 'icon_filename')) return;
    foreach($USERS->rows as $user) $user->icon_filename = $target->icon_filename;
  }
}
