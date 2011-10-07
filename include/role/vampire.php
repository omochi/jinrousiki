<?php
/*
  ◆吸血鬼 (vampire)
  ○仕様
  ・吸血：通常
*/
class Role_vampire extends Role{
  public $action = 'VAMPIRE_DO';
  public $ignore_message = '初日は襲撃できません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2){
      //自分の感染者と洗脳者を表示
      $id = $this->GetActor()->user_no;
      $partner = 'infected';
      $role    = 'psycho_infected';
      $pertner_list = array();
      $role_list    = array();
      foreach($this->GetUser() as $user){
	if($user->IsPartner($partner, $id)) $partner_list[] = $user->handle_name;
	if($user->IsRole($role)) $role_list[] = $user->handle_name;
      }
      OutputPartner($partner_list, $partner . '_list');
      OutputPartner($role_list, $role . '_list');
      if(isset($this->result)) OutputSelfAbilityResult($this->result);
    }
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('vampire-do', 'vampire_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  //吸血処理
  function Infect($user){ $user->AddRole($this->GetActor()->GetID('infected')); }
}
