<?php
/*
  ◆古狼 (elder_wolf)
  ○仕様
  ・投票数：+1
*/
RoleManager::LoadFile('wolf');
class Role_elder_wolf extends Role_wolf {
  function FilterVoteDo(&$count) { $count++; }
}
