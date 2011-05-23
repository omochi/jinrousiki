<?php
/*
  ◆出題者 (quiz)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先を優先的に処刑する
  ・対象を一人に固定できる時のみ有効
*/
class Role_quiz extends RoleVoteAbility{
  public $data_type = 'action';
  public $decide_type = 'same';

  function __construct(){ parent::__construct(); }
}
