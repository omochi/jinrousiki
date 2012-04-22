<?php
/*
  ◆公開者 (mind_open)
  ○仕様
*/
class Role_mind_open extends Role {
  function __construct(){ parent::__construct(); }

  function IsMindRead(){ return DB::$ROOM->date > 1; }
}
