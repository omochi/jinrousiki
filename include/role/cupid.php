<?php
/*
  ◆キューピッド (cupid)
  ○仕様
  ・追加役職：なし
*/
class Role_cupid extends Role{
  public $action = 'CUPID_DO';
  public $ignore_message = '初日以外は投票できません';
  public $self_shoot = false;
  public $shoot_count = 2;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    //自分が矢を打った恋人 (自分自身含む) を表示
    $id = $this->GetActor()->user_no;
    $stack = array();
    foreach($this->GetUser() as $user){
      if($user->IsPartner('lovers', $id)) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'cupid_pair');
    $this->OutputCupidAbility();
    $this->OutputAction();
  }

  //特殊キューピッドの情報表示
  function OutputCupidAbility(){}

  function OutputAction(){
    global $ROOM;
    if($this->IsVote() && $ROOM->IsNight()){
      OutputVoteMessage('cupid-do', 'cupid_do', $this->action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date == 1;
  }

  function SetVoteNight(){
    global $GAME_CONF, $USERS;

    parent::SetVoteNight();
    $this->SetStack($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot, 'self_shoot');
  }

  function GetVoteCheckbox($user, $id, $live){
    return $live && ! $user->IsDummyBoy() ?
      '<input type="checkbox" name="target_no[]"' .
      ($this->IsSelfShoot() && $this->IsActor($user->uname) ? ' checked' : '') .
      ' id="' . $id . '" value="' . $id . '">'."\n" : '';
  }

  //自分撃ち判定
  function IsSelfShoot(){ return $this->GetStack('self_shoot') || $this->self_shoot; }

  function VoteNight(){
    global $USERS;

    $stack = $this->GetVoteNightTarget();
    //人数チェック
    $count = $this->GetVoteNightTargetCount();
    if(count($stack) != $count) return '指定人数は' . $count . '人にしてください';

    $self_shoot = false; //自分撃ちフラグ
    $user_list  = array();
    foreach($stack as $id){
      $user = $USERS->ByID($id);
      if(! $user->IsLive() || $user->IsDummyBoy()){ //例外判定
	return '生存者以外と身代わり君には投票できません';
      }
      $user_list[] = $user;
      $self_shoot |= $this->IsActor($user->uname); //自分撃ち判定
    }

    //自分撃ちエラー判定
    if(! $self_shoot){
      $str = '必ず自分を対象に含めてください';
      if($this->self_shoot)    return $str; //自分撃ち固定役職
      if($this->IsSelfShoot()) return '少人数村の場合は、' . $str; //参加人数
    }
    $method = 'VoteNightAction';
    $self   = 'Role_' . $this->role;
    $class  = method_exists($self, $method) ? $self : $this;
    $class::$method($user_list, $self_shoot);
    return NULL;
  }

  //投票人数取得
  function GetVoteNightTargetCount(){ return $this->shoot_count; }

  //キューピッドの投票処理
  function VoteNightAction($list, $flag){
    $uname_stack  = array();
    $handle_stack = array();
    foreach($list as $user){
      $uname_stack[]  = $user->uname;
      $handle_stack[] = $user->handle_name;
      $user->AddRole($this->GetRole($user, $flag)); //役職追加
      $user->ReparseRoles(); //再パース (魂移使判定用：反映が保障されているのは恋人のみ)
    }
    $this->SetStack(implode(' ', $uname_stack), 'target_uname');
    $this->SetStack(implode(' ', $handle_stack), 'target_handle');
  }

  //追加役職セット
  function GetRole($user, $flag){ return $this->GetActor()->GetID('lovers'); }
}
