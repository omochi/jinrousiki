<?php
/*
  ◆天邪鬼 (perverseness)
  ○仕様
  ・自分の投票先に複数の人が投票していたらショック死する
*/
class Role_perverseness extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteCount() > 1) $reason = 'PERVERSENESS';
  }
}
