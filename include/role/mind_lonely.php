<?php
/*
  ◆はぐれ者 (mind_lonely)
  ○仕様
*/
class Role_mind_lonely extends Role{
  public $mix_in = 'silver_wolf';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  function Whisper($builder, $voice){
    return $this->GetActor()->IsWolf() && parent::Whisper($builder, $voice);
  }
}
