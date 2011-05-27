<?php
/*
  ◆雛狼
  ○仕様
  ・襲撃：性別判定
*/
class Role_sex_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatAction($user){
    global $ROOM;

    $str = $this->GetActor()->GetHandleName($user->uname, $user->DistinguishSex());
    $ROOM->SystemMessage($str, 'SEX_WOLF_RESULT');
    $user->wolf_killed = true; //尾行判定は成功扱い
    return true;
  }
}
