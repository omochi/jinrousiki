<?php
/*
  ◆人狼 (wolf)
  ○仕様
*/
class Role_wolf extends Role {
  public $action = 'WOLF_EAT';
  public $wolf_action_list = array('WOLF_EAT', 'STEP_WOLF_EAT', 'SILENT_WOLF_EAT');

  protected function OutputPartner() {
    $wolf_list        = array();
    $mad_list         = array();
    $unconscious_list = array();
    foreach (DB::$USER->rows as $user) {
      if ($this->IsActor($user)) continue;
      if ($user->IsRole('possessed_wolf')) {
	$wolf_list[] = $user->GetName(); //憑依先を追跡する
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
      RoleHTML::OutputPartner($wolf_list, 'wolf_partner'); //人狼
      RoleHTML::OutputPartner($mad_list, 'mad_partner'); //囁き狂人
    }
    if (DB::$ROOM->IsNight()) {
      RoleHTML::OutputPartner($unconscious_list, 'unconscious_list'); //無意識
    }
  }

  function OutputAction() {
    RoleHTML::OutputVote('wolf-eat', 'wolf_eat', $this->action);
  }

  //身代わり君襲撃固定判定
  final function IsDummyBoy() {
    return DB::$ROOM->IsQuiz() || (DB::$ROOM->IsDummyBoy() && DB::$ROOM->date == 1);
  }

  function IsVoteCheckboxChecked(User $user) { return $this->IsDummyBoy() && $user->IsDummyBoy(); }

  function IsFinishVote(array $list) {
    return count(array_intersect($this->wolf_action_list, array_keys($list))) > 0;
  }

  //遠吠え
  function Howl(TalkBuilder $builder, $voice) {
    if (! $builder->flag->wolf_howl) return false; //スキップ判定

    $str = Message::$wolf_howl;
    foreach ($builder->filter as $filter) $filter->FilterWhisper($voice, $str); //フィルタリング処理
    $builder->AddRaw('', '狼の遠吠え', $str, $voice);
    return true;
  }

  function GetVoteTargetUser() {
    $stack = parent::GetVoteTargetUser();
    //身代わり君適用判定
    if ($this->IsDummyBoy()) $stack = array(1 => $stack[1]); //dummy_boy = 1番は保証されている？
    return $stack;
  }

  function GetVoteIconPath(User $user, $live) {
    return ! $live ? Icon::GetDead() :
      ($this->IsWolfPartner($user->id) ? Icon::GetWolf() : Icon::GetFile($user->icon_filename));
  }

  //仲間狼判定
  protected function IsWolfPartner($id) { return DB::$USER->ByReal($id)->IsWolf(true); }

  function IsVoteCheckbox(User $user, $live) {
    return parent::IsVoteCheckbox($user, $live) && $this->IsWolfEatTarget($user->id);
  }

  //仲間狼襲撃可能判定
  protected function IsWolfEatTarget($id) { return ! $this->IsWolfPartner($id); }

  function IgnoreVoteNight(User $user, $live) {
    if (! is_null($str = parent::IgnoreVoteNight($user, $live))) return $str;
    //クイズ村は GM 以外無効
    if (DB::$ROOM->IsQuiz() && ! $user->IsDummyBoy()) return 'クイズ村では GM 以外に投票できません';
    if (DB::$ROOM->IsDummyBoy() && DB::$ROOM->date == 1 && ! $user->IsDummyBoy()) { //身代わり君判定
      return '身代わり君使用の場合は、身代わり君以外に投票できません';
    }
    if (! $this->IsWolfEatTarget($user->id)) return '狼同士には投票できません'; //仲間狼判定
    return null;
  }

  //護衛カウンター
  function GuardCounter() {}

  //人狼襲撃失敗判定
  function WolfEatSkip(User $user) {
    if ($user->IsWolf()) { //人狼系判定 (例：銀狼出現)
      $this->WolfEatSkipAction($user);
      $user->wolf_eat = true; //襲撃は成功扱い
      return true;
    }
    if ($user->IsFox()) { //妖狐判定
      $filter = RoleManager::LoadMain($user);
      if (! $filter->resist_wolf) return false;
      $this->FoxEatAction($user); //妖狐襲撃処理
      $filter->FoxEatCounter($this->GetWolfVoter()); //妖狐襲撃カウンター処理

      //人狼襲撃メッセージを登録
      if (! DB::$ROOM->IsOption('seal_message')) {
	DB::$ROOM->ResultAbility('FOX_EAT', 'targeted', null, $user->id);
      }
      $user->wolf_eat = true; //襲撃は成功扱い
      return true;
    }
    return false;
  }

  //人狼襲撃失敗処理
  protected function WolfEatSkipAction(User $user) {}

  //妖狐襲撃処理
  protected function FoxEatAction(User $user) {}

  //人狼襲撃処理
  function WolfEatAction(User $user) {}

  //人狼襲撃死亡処理
  function WolfKill(User $user) {
    DB::$USER->Kill($user->id, 'WOLF_KILLED');
    $this->WolfKillAction($user);
  }

  //人狼襲撃追加処理
  protected function WolfKillAction(User $user) {}

  //毒対象者選出 (襲撃)
  function GetPoisonEatTarget() {
    return GameConfig::POISON_ONLY_EATER ? $this->GetWolfVoter() :
      DB::$USER->ByID(Lottery::Get(DB::$USER->GetLivingWolves()));
  }

  //毒死処理
  function PoisonDead() { DB::$USER->Kill($this->GetID(), 'POISON_DEAD'); }
}
