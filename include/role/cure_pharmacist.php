<?php
/*
  ◆河童 (cure_pharmacist)
  ○仕様
  ・解毒/ショック死抑制
*/
RoleManager::LoadFile('pharmacist');
class Role_cure_pharmacist extends Role_pharmacist {
  protected function SetDetoxFlag($uname) {
    $this->GetActor()->detox = true;
    $this->AddStackName('cured', 'pharmacist_result', $uname);
  }
}
