<?php
/*
  ◆雛狼 (sex_wolf)
  ○仕様
  ・襲撃：性別鑑定
*/
RoleManager::LoadFile('wolf');
class Role_sex_wolf extends Role_wolf {
  public $mix_in = 'sex_mage';
  public $result = 'SEX_WOLF_RESULT';
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date > 1) OutputSelfAbilityResult($this->result);
  }

  function WolfEatAction($user){
    $result = $this->DistinguishSex($user);
    $target = DB::$USER->GetHandleName($user->uname, true);
    DB::$ROOM->ResultAbility($this->result, $result, $target, $this->GetWolfVoter()->user_no);

    $user->wolf_eat = true; //襲撃は成功扱い
    return true;
  }
}
