<?php
/*
  ◆獣人
  ○仕様
  ・人狼襲撃得票カウンター：人狼変化
*/
class Role_therian_mad extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatReaction(){
    $user = $this->GetActor();
    $user->ReplaceRole($user->main_role, 'wolf');
    $user->AddRole('changed_therian');
    $user->wolf_killed = true; //尾行判定は成功扱い
    return true;
  }
}
