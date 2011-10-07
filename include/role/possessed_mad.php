<?php
/*
  ◆犬神 (possessed_mad)
  ○仕様
*/
class Role_possessed_mad extends Role{
  public $action = 'POSSESSED_DO';
  public $not_action = 'POSSESSED_NOT_DO';
  public $ignore_message = '初日は憑依できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    $this->OutputAction();
  }

  function OutputAction(){
    global $ROOM;

    if($this->GetActor()->IsActive()){
      if($this->IsVote() && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'possessed_do', $this->action, $this->not_action);
      }
    }
    elseif($ROOM->date > 2) OutputPossessedTarget(); //現在の憑依先
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  function IgnoreVote(){
    if(! is_null($str = parent::IgnoreVote())) return $str;
    return $this->GetActor()->IsActive() ? NULL : '能力喪失しています';
  }

  function GetVoteIconPath($user, $live){
    global $ICON_CONF;
    return $ICON_CONF->path . '/' . $user->icon_filename;
  }

  function IsVoteCheckbox($user, $live){
    return ! $live && ! $this->IsSameUser($user->uname) && ! $user->IsDummyBoy();
  }

  function IgnoreVoteNight($user, $live){ return $live ? '死者以外には投票できません' : NULL; }
}
