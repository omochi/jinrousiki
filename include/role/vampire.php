<?php
/*
  ◆吸血鬼 (vampire)
  ○仕様
  ・吸血：通常
*/
class Role_vampire extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2){
      //自分の感染者と洗脳者を表示
      $id = $this->GetActor()->user_no;
      $stack = array();
      foreach($this->GetUser() as $user){
	if($user->IsPartner('infected', $id)) $stack['infected'][] = $user->handle_name;
	if($user->IsRole('psycho_infected')) $stack['psycho_infected'][] = $user->handle_name;
      }
      OutputPartner($stack['infected'], 'infected_list');
      OutputPartner($stack['psycho_infected'], 'psycho_infected_list');
      if(isset($this->result)) OutputSelfAbilityResult($this->result);
    }
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('vampire-do', 'vampire_do', 'VAMPIRE_DO');
    }
  }

  //吸血処理
  function Infect($user){ $user->AddRole($this->GetActor()->GetID('infected')); }
}
