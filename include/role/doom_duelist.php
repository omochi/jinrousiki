<?php
/*
  ◆黒幕 (doom_duelist)
  ○仕様
  ・追加役職：死の宣告 (7日目)
*/
RoleManager::LoadFile('valkyrja_duelist');
class Role_doom_duelist extends Role_valkyrja_duelist {
  protected function AddDuelistRole(User $user) { $user->AddRole('death_warrant[7]'); }
}
