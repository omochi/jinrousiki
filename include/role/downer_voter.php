<?php
/*
  ◆没落者 (downer_voter)
  ○仕様
  ・投票数：-1 (5日目以降)
*/
class Role_downer_voter extends Role {
  function FilterVoteDo(&$count) {
    if (DB::$ROOM->date > 4) $count--;
  }
}
