<?php
/*
  ◆雑草魂 (upper_luck)
  ○仕様
  ・得票数：+4 (2日目) / -2 (3日目以降)
*/
class Role_upper_luck extends Role {
  function FilterVotePoll(&$count) {
    $count += DB::$ROOM->date == 2 ? 4 : -2;
  }
}
