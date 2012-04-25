<?php
/*
  ◆人狼 (wolf)
  ○仕様
*/
class Role_wolf extends Role {
  public $action = 'WOLF_EAT';
  function __construct(){ parent::__construct(); }

  protected function OutputPartner(){
    $wolf_list        = array();
    $mad_list         = array();
    $unconscious_list = array();
    foreach (DB::$USER->rows as $user) {
      if ($this->IsActor($user->uname)) continue;
      if ($user->IsRole('possessed_wolf')) {
	$wolf_list[] = DB::$USER->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif ($user->IsWolf(true)) {
	$wolf_list[] = $user->handle_name;
      }
      elseif ($user->IsRole('whisper_mad')) {
	$mad_list[] = $user->handle_name;
      }
      elseif ($user->IsRole('unconscious') || $user->IsRoleGroup('scarlet')) {
	$unconscious_list[] = $user->handle_name;
      }
    }
    if ($this->GetActor()->IsWolf(true)) {
      OutputPartner($wolf_list, 'wolf_partner'); //人狼
      OutputPartner($mad_list, 'mad_partner'); //囁き狂人
    }
    if (DB::$ROOM->IsNight()) OutputPartner($unconscious_list, 'unconscious_list'); //無意識
  }

  function OutputAction(){ OutputVoteMessage('wolf-eat', 'wolf_eat', $this->action); }

  //遠吠え
  function Howl($builder, $voice){
    global $MESSAGE;

    if (! $builder->flag->wolf_howl) return false; //スキップ判定
    $str = $MESSAGE->wolf_howl;
    foreach ($builder->filter as $filter) $filter->FilterWhisper($voice, $str); //フィルタリング処理
    $builder->RawAddTalk('', '狼の遠吠え', $str, $voice);
    return true;
  }

  function GetVoteTargetUser(){
    $stack = parent::GetVoteTargetUser();
    //身代わり君適用判定
    if ((DB::$ROOM->IsDummyBoy() && DB::$ROOM->date == 1) || DB::$ROOM->IsQuiz()) {
      $stack = array(1 => $stack[1]); //dummy_boy = 1番は保証されている？
    }
    return $stack;
  }

  function GetVoteIconPath($user, $live){
    global $ICON_CONF;
    return ! $live ? $ICON_CONF->dead :
      ($this->IsWolfPartner($user->user_no) ? $ICON_CONF->wolf :
       $ICON_CONF->path . '/' . $user->icon_filename);
  }

  //仲間狼判定
  function IsWolfPartner($id){ return DB::$USER->ByReal($id)->IsWolf(true); }

  function IsVoteCheckbox($user, $live){
    return parent::IsVoteCheckbox($user, $live) && $this->IsWolfEatTarget($user->user_no);
  }

  //仲間狼襲撃可能判定
  function IsWolfEatTarget($id){ return ! $this->IsWolfPartner($id); }

  function IgnoreVoteNight($user, $live){
    if (! is_null($str = parent::IgnoreVoteNight($user, $live))) return $str;
    if (! $this->IsWolfEatTarget($user->user_no)) return '狼同士には投票できません'; //仲間狼判定
    //クイズ村は GM 以外無効
    if (DB::$ROOM->IsQuiz() && ! $user->IsDummyBoy()) return 'クイズ村では GM 以外に投票できません';
    if (DB::$ROOM->IsDummyBoy() && DB::$ROOM->date == 1 && ! $user->IsDummyBoy()) { //身代わり君判定
      return '身代わり君使用の場合は、身代わり君以外に投票できません';
    }
    return null;
  }

  //人狼襲撃失敗判定
  function WolfEatSkip($user){
    global $ROLES;

    if ($user->IsWolf()) { //人狼系判定 (例：銀狼出現)
      $this->WolfEatSkipAction($user);
      $user->wolf_eat = true; //襲撃は成功扱い
      return true;
    }
    if ($user->IsFox()) { //妖狐判定
      $filter = $ROLES->LoadMain($user);
      if (! $filter->resist_wolf) return false;
      $this->FoxEatAction($user); //妖狐襲撃処理
      $filter->FoxEatCounter($this->GetWolfVoter()); //妖狐襲撃カウンター処理

      //人狼襲撃メッセージを登録
      if (! DB::$ROOM->IsOption('seal_message')) {
	DB::$ROOM->ResultAbility('FOX_EAT', 'targeted', null, $user->user_no);
      }
      $user->wolf_eat = true; //襲撃は成功扱い
      return true;
    }
    return false;
  }

  //人狼襲撃失敗処理
  function WolfEatSkipAction($user){}

  //妖狐襲撃処理
  function FoxEatAction($user){}

  //人狼襲撃処理
  function WolfEatAction($user){}

  //人狼襲撃死亡処理
  function WolfKill($user){
    DB::$USER->Kill($user->user_no, 'WOLF_KILLED');
  }

  //毒対象者選出 (襲撃)
  function GetPoisonEatTarget(){
    global $GAME_CONF;
    return $GAME_CONF->poison_only_eater ? $this->GetWolfVoter() :
      DB::$USER->ByUname(GetRandom(DB::$USER->GetLivingWolves()));
  }

  //毒死処理
  function PoisonDead(){
    DB::$USER->Kill($this->GetActor()->user_no, 'POISON_DEAD');
  }
}
