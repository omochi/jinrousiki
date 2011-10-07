<?php
/*
  ◆仙狐 (revive_fox)
  ○仕様
  ・蘇生率：100% / 誤爆有り
  ・蘇生後：能力喪失
*/
RoleManager::LoadFile('poison_cat');
class Role_revive_fox extends Role_poison_cat{
  public $mix_in = 'fox';
  public $revive_rate   = 100;
  public $missfire_rate =   0;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    $this->filter->OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult('POISON_CAT_RESULT'); //蘇生結果
    //投票
    if($this->IsVote() && $this->GetActor()->IsActive() && $ROOM->IsNight() &&
       ! $ROOM->IsOpenCast()){
      OutputVoteMessage('revive-do', $this->submit, $this->action, $this->not_action);
    }
  }

  function IgnoreVote(){
    if(! is_null($str = parent::IgnoreVote())) return $str;
    return $this->GetActor()->IsActive() ? NULL : '能力喪失しています';
  }

  function AfterRevive(){ $this->GetActor()->LostAbility(); }
}
