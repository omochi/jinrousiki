<?php
/*
  ◆幸運 (good_luck)
  ○仕様
  ・処刑投票が拮抗したら自分が候補から除外される
*/
class Role_good_luck extends RoleVoteAbility{
  public $data_type = 'self';
  public $decide_type = 'escape';

  function __construct(){ parent::__construct(); }
}
