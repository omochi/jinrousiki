<?php
/*
  ◆仙狸 (revive_cat)
  ○仕様
  ・蘇生率：80% (減衰 1/4) / 誤爆有り
  ・蘇生後：蘇生回数更新
*/
RoleManager::LoadFile('poison_cat');
class Role_revive_cat extends Role_poison_cat{
  public $revive_rate   = 80;
  public $missfire_rate =  0;
  function __construct(){ parent::__construct(); }

  function GetRate(){ return ceil(parent::GetRate() / pow(4, $this->GetTimes())); }

  function AfterRevive(){
    $times = $this->GetTimes();
    $role  = $times > 0 ? $this->role . '[' . $times . ']' : $this->role;
    $this->GetActor()->ReplaceRole($role, $this->role . '[' . ++$times . ']');
  }

  private function GetTimes(){ return (int)$this->GetActor()->GetMainRoleTarget(); }
}
