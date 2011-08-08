<?php
/*
  ◆短気 (impatience)
  ○仕様
  ・処刑者決定：優先順位が低めの決定者相当
  ・ショック死：再投票
*/
class Role_impatience extends RoleVoteAbility{
  public $data_type = 'target';
  public $decide_type = 'decide';

  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteKillUname() == '')  $reason = 'IMPATIENCE';
  }
}
