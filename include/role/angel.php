<?php
/*
  ◆天使 (angel)
  ○仕様
  ・共感者判定：男女
*/
RoleManager::LoadFile('cupid');
class Role_angel extends Role_cupid{
  function __construct(){ parent::__construct(); }

  function VoteNightAction($list, $flag){
    parent::VoteNightAction($list, $flag);
    //共感者判定
    $lovers_a = $list[0];
    $lovers_b = $list[1];
    if($this->IsSympathy($lovers_a, $lovers_b)) $this->SetSympathy($loves_a, $lovers_b);
  }

  protected function IsSympathy($lovers_a, $lovers_b){ return $lovers_a->sex != $lovers_b->sex; }

  //共感者処理
  protected function SetSympathy($a, $b){
    global $ROOM;

    $action = 'SYMPATHY_RESULT';
    $a->AddRole('mind_sympathy');
    $b->AddRole('mind_sympathy');
    $ROOM->ResultAbility($action, $b->main_role, $b->handle_name, $a->user_no);
    $ROOM->ResultAbility($action, $a->main_role, $a->handle_name, $b->user_no);
  }
}
