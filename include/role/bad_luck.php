<?php
/*
  ◆不運 (bad_luck)
  ○仕様
  ・処刑投票が拮抗したら自分が処刑される
*/
class Role_bad_luck extends RoleVoteAbility{
  public $data_type = 'self';
  public $decide_type = 'decide';

  function __construct(){ parent::__construct(); }
}
