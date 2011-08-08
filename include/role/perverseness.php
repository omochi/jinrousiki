<?php
/*
  ◆天邪鬼 (perverseness)
  ○仕様
  ・ショック死：自分の投票先に複数の人が投票している
*/
class Role_perverseness extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteCount() > 1) $reason = 'PERVERSENESS';
  }
}
