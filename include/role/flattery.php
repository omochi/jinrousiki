<?php
/*
  ◆ゴマすり (flattery)
  ○仕様
  ・ショック死：自分の投票先に他の人が投票していない
*/
class Role_flattery extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteCount() < 2) $reason = 'FLATTERY';
  }
}
