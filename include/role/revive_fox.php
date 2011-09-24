<?php
/*
  ◆仙狐 (revive_fox)
  ○仕様
  ・蘇生率：100% / 誤爆有り
  ・蘇生後：能力喪失
*/
RoleManager::LoadFile('fox');
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
    if($ROOM->date > 1 && $ROOM->IsNight() && $this->GetActor()->IsActive() &&
       ! $ROOM->IsOpenCast()){
      OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
    }
  }

  function AfterRevive(){ $this->GetActor()->LostAbility(); }
}
