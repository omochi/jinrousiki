<?php
/*
  ◆出題者 (quiz)
  ○仕様
  ・決定能力：自分の投票先を優先的に処刑する (対象を一人に固定できる時のみ有効)
*/
class Role_quiz extends RoleVoteAbility{
  public $data_type = 'action';
  public $decide_type = 'same';
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROLE_IMG, $ROOM;

    parent::OutputAbility();
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->Output('quiz_chaos');
  }
}
