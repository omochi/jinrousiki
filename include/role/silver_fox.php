<?php
/*
  ◆銀狐 (silver_fox)
  ○仕様
  ・仲間表示：なし
*/
RoleManager::LoadFile('fox');
class Role_silver_fox extends Role_fox {
  protected function OutputPartner() { return; }
}
