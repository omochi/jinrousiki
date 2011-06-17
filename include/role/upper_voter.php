<?php
/*
  ◆わらしべ長者 (upper_voter)
  ○仕様
  ・5日目以降、投票数が +1 される
*/
class Role_upper_voter extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    global $ROOM;
    if($ROOM->date > 4) $vote_number++;
  }
}
