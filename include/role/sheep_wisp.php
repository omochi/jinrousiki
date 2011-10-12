<?php
/*
  ◆羊皮 (sheep_wisp)
  ○仕様
*/
class Role_sheep_wisp extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM; //初日に付く可能性はある？
    if($ROOM->date > 1 && $this->GetActor()->IsDoomRole($this->role)) parent::OutputAbility();
  }
}
