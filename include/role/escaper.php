<?php
/*
  ◆逃亡者 (escaper)
  ○仕様
  ・逃亡失敗：人狼系
  ・逃亡処理：なし
*/
class Role_escaper extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('escape-do', 'escape_do', 'ESCAPE_DO');
    }
  }

  //逃亡処理
  function Escape($user){
    global $USERS, $ROLES;

    $actor = $this->GetActor();
    if(in_array($user->uname, $ROLES->stack->trap)){ //罠死判定
      $USERS->Kill($actor->user_no, 'TRAPPED');
    }
    elseif($this->EscapeFailed($user)){ //逃亡失敗判定
      $USERS->Kill($actor->user_no, 'ESCAPER_DEAD');
    }
    else{
      if(in_array($user->uname, $ROLES->stack->snow_trap)){ //凍傷判定
	$ROLES->stack->frostbite[] = $actor->uname;
      }
      $this->EscapeAction($user); //逃亡処理
      $ROLES->stack->escaper[$actor->uname] = $user->uname; //逃亡先をセット
    }
  }

  //逃亡失敗判定
  function EscapeFailed($user){ return $user->IsWolf(); }

  //逃亡後処理
  function EscapeAction($user){}
}
