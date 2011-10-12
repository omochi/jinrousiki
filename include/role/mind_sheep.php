<?php
/*
  ◆羊 (mind_sheep)
  ○仕様
  ・人狼襲撃：羊皮
*/
class Role_mind_sheep extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM, $USERS;

    if($ROOM->date < 2) return;
    parent::OutputAbility();
    $stack = array();
    foreach($this->GetActor()->GetPartner($this->role, true) as $id){
      $stack[$id] = $USERS->ById($id)->handle_name;
    }
    ksort($stack);
    OutputPartner($stack, 'shepherd_patron_list');
  }

  function WolfEatCounter($user){ $user->AddDoom(1, 'sheep_wisp'); }
}
