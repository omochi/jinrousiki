<?php
/*
  ◆雛狼 (sex_wolf)
  ○仕様
  ・襲撃：性別鑑定
*/
RoleManager::LoadFile('wolf');
class Role_sex_wolf extends Role_wolf{
  public $mix_in = 'sex_mage';
  public $result = 'SEX_WOLF_RESULT';
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
  }

  function WolfEatAction($user){
    global $ROOM;

    $str = $this->GetActor()->GetHandleName($user->uname, $this->DistinguishSex($user));
    $ROOM->SystemMessage($str, $this->result);
    $user->wolf_killed = true; //尾行判定は成功扱い
    return true;
  }
}
