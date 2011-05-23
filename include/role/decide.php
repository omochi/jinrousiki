<?php
/*
  ◆決定者 (decide)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が処刑される
*/
class Role_decide extends RoleVoteAbility{
  public $data_type = 'target';
  public $decide_type = 'decide';

  function __construct(){ parent::__construct(); }
}
