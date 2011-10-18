<?php
/*
  ◆小心者 (chicken)
  ○仕様
  ・ショック死：得票
*/
class Role_chicken extends Role{
  public $sudden_death = 'CHICKEN';
  function __construct(){ parent::__construct(); }

  //ショック死判定
  function SuddenDeath(){
    if($this->IsSuddenDeath()) $this->SetSuddenDeath($this->sudden_death);
  }

  //ショック死データ登録
  function SetSuddenDeath($type){ $this->SetStack($type, 'sudden_death'); }

  //ショック死セット判定
  function IsSuddenDeath(){ return ! $this->IgnoreSuddenDeath() && $this->GetVotedCount() > 0; }

  //ショック死セット済み判定
  function IgnoreSuddenDeath(){ return $this->GetStack('sudden_death') != ''; }

  //投票先人数取得
  function GetVoteCount(){
    $count  = $this->GetStack('count');
    $target = $this->GetStack('target');
    $key    = $target[$this->GetUname()];
    return array_key_exists($key, $count) ? $count[$key] : 0;
  }

  //得票人数取得
  function GetVotedCount(){
    $count  = $this->GetStack('count');
    return array_key_exists($this->GetUname(), $count) ? $count[$this->GetUname()] : 0;
  }

  //発動日判定
  function IsDoom(){
    global $ROOM;
    return $this->GetActor()->GetDoomDate($this->role) == $ROOM->date;
  }
}
