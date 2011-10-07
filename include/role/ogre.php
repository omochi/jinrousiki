<?php
/*
  ◆鬼 (ogre)
  ○仕様
  ・勝利条件：自分自身と人狼系の生存
*/
class Role_ogre extends Role{
  public $action = 'OGRE_DO';
  public $not_action = 'OGRE_NOT_DO';
  public $resist_rate = 30;
  public $reduce_rate = 5;
  public $ignore_message = '初日は人攫いできません';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    $this->OutputOgreAbility();
    if($this->IsVote() && $ROOM->IsNight()){
      OutputVoteMessage('ogre-do', 'ogre_do', $this->action, $this->not_action);
    }
  }

  function IsVote(){
    global $ROOM;
    return $ROOM->date > 1;
  }

  function SetVoteNight(){
    global $ROOM;

    parent::SetVoteNight();
    if($ROOM->IsEvent('force_assassin_do')) $this->SetStack(NULL, 'not_action');
  }

  //特殊鬼の情報表示
  function OutputOgreAbility(){}

  //天候情報取得
  protected function GetEvent(){
    global $ROOM;
    return $ROOM->IsEvent('full_ogre') ? 100 : ($ROOM->IsEvent('seal_ogre') ? 0 : NULL);
  }

  //人狼襲撃耐性判定
  function WolfEatResist(){
    $rate = mt_rand(1, 100);
    //$rate = 5; //テスト用
    $resist_rate = $this->GetResistRate();
    //PrintData("{$rate} ({$resist_rate})", 'Rate [ogre resist]');
    return $rate <= $resist_rate;
  }

  //人狼襲撃耐性率取得
  function GetResistRate(){
    $event = $this->GetEvent();
    return is_null($event) ? $this->resist_rate : $event;
  }

  //人攫い成功減衰率取得
  protected function GetReduceRate(){ return 1 / $this->reduce_rate; }

  //人攫い成功率取得
  function GetAssassinRate($times){
    $event = $this->GetEvent();
    return is_null($event) ? ceil(100 * pow($this->GetReduceRate(), $times)) : $event;
  }

  //人攫い失敗判定
  function Ignored($user){}

  //人攫い処理
  function Assassin($user){
    global $ROOM;

    if($this->Ignored($user)) return; //無効判定

    //人攫い成功判定
    $rate = mt_rand(1, 100); //襲撃成功判定乱数
    //$rate = 5; //テスト用
    $ogre_times = (int)$this->GetActor()->GetMainRoleTarget();
    $ogre_rate  = $this->GetAssassinRate($ogre_times);
    //PrintData($rate, 'Rate [OGRE_DO]: ' . $ogre_rate);

    if($rate > $ogre_rate) return; //成功判定
    $this->AssassinAction($user);

    if($ROOM->IsEvent('full_ogre')) return; //朧月ならスキップ
    $base_role = $this->GetActor()->main_role; //成功回数更新処理
    if($ogre_times > 0) $base_role .= '[' . $ogre_times . ']';
    $new_role = $this->GetActor()->main_role . '[' . ($ogre_times + 1) . ']';
    $this->GetActor()->ReplaceRole($base_role, $new_role);
  }

  //人攫い実行処理
  function AssassinAction($user){ $this->AddSuccess($user->user_no, 'ogre'); }

  //人攫い死処理
  function AssassinKill(){
    global $USERS;
    foreach($this->GetStack() as $id => $flag) $USERS->Kill($id, 'OGRE_KILLED');
  }

  //勝敗判定
  function Win($victory){
    if($this->IsDead()) return false;
    if($victory == 'wolf') return true;
    foreach($this->GetUser() as $user){
      if($user->IsLiveRoleGroup('wolf')) return true;
    }
    return false;
  }
}
