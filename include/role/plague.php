<?php
/*
  ◆疫病神 (plague)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が候補から除外される
*/
class Role_plague extends RoleVoteAbility{
  public $data_type = 'target';
  public $decide_type = 'escape';

  function __construct(){ parent::__construct(); }
}
