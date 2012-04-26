<?php
/*
  ◆妖狐 (fox)
  ○仕様
  ・仲間表示：妖狐系・子狐系
  ・人狼襲撃耐性：有り
*/
class Role_fox extends Role {
  public $resist_wolf = true;
  function __construct(){ parent::__construct(); }

  protected function OutputPartner(){
    if ($this->GetActor()->IsLonely()) return;
    $fox_list       = array();
    $child_fox_list = array();
    foreach (DB::$USER->rows as $user) {
      if ($this->IsActor($user->uname)) continue;
      if ($user->IsRole('possessed_fox')) {
	$fox_list[] = DB::$USER->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif ($user->IsFox(true)) {
	$fox_list[] = $user->handle_name;
      }
      elseif ($user->IsChildFox() || $user->IsRoleGroup('scarlet')) {
	$child_fox_list[] = $user->handle_name;
      }
    }
    OutputPartner($fox_list, 'fox_partner'); //妖狐系
    OutputPartner($child_fox_list, 'child_fox_partner'); //子狐系
  }

  protected function OutputResult(){
    if ($this->resist_wolf && DB::$ROOM->date > 1 && ! DB::$ROOM->IsOption('seal_message')) {
      OutputSelfAbilityResult('FOX_EAT'); //人狼襲撃
    }
  }

  //人狼襲撃カウンター
  function FoxEatCounter($user){}
}
