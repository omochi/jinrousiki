<?php
/*
  ◆猫又 (poison_cat)
  ○仕様
  ・蘇生率：25% / 誤爆有り
  ・蘇生後：なし
*/
class Role_poison_cat extends Role{
  public $action = 'POISON_CAT_DO';
  public $not_action = 'POISON_CAT_NOT_DO';
  public $submit = 'revive_do';
  public $not_submit = 'revive_not_do';
  public $ignore_message = '初日は蘇生できません';
  public $revive_rate   = 25;
  public $missfire_rate =  0;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    $this->OutputReviveAbility();
  }

  //蘇生情報表示
  function OutputReviveAbility(){
    global $ROOM;

    if($ROOM->IsOpenCast()) return;
    if($ROOM->date > 2 && ! $ROOM->IsOption('seal_message')){
      OutputSelfAbilityResult('POISON_CAT_RESULT'); //蘇生結果
    }
    if($this->IsVote() && $ROOM->IsNight()){ //投票
      OutputVoteMessage('revive-do', $this->submit, $this->action, $this->not_action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  function IgnoreVote(){
    global $ROOM;

    if(! is_null($str = parent::IgnoreVote())) return $str;
    return $ROOM->IsOpenCast() ?
      '「霊界で配役を公開しない」オプションがオフの時は投票できません' : NULL;
  }

  function GetVoteIconPath($user, $live){
    global $ICON_CONF;
    return $ICON_CONF->path . '/' . $user->icon_filename;
  }

  function IsVoteCheckbox($user, $live){
    return ! $live && ! $this->IsSameUser($user->uname) && ! $user->IsDummyBoy();
  }

  function IgnoreVoteNight($user, $live){ return $live ? '死者以外には投票できません' : NULL; }

  //天候情報取得
  protected function GetEvent(){
    global $ROOM;
    return $ROOM->IsEvent('full_revive') ? 100 : ($ROOM->IsEvent('no_revive') ? 0 : NULL);
  }

  //基礎蘇生率取得
  function GetRate(){ return $this->revive_rate; }

  //蘇生率取得
  function GetReviveRate($flag){
    $event = $this->GetEvent();
    $rate  = is_null($event) ? $this->GetRate() : $event;
    if($flag) $rate = ceil($rate * 1.3);
    return $rate > 100 ? 100 : $rate;
  }

  //誤爆率取得
  function GetMissfireRate($revive_rate){
    global $ROOM;

    if($this->GetEvent() || $this->missfire_rate < 0) return 0;
    $rate = $this->missfire_rate == 0 ? floor($revive_rate / 5) : $this->missfire_rate;
    if($ROOM->IsEvent('missfire_revive')) $rate *= 2;
    return $rate > $revive_rate ? $revive_rate : $rate;
  }

  //蘇生後処理
  function AfterRevive(){}
}
