<?php
/*
  ◆夢求愛者 (dummy_chiroptera)
  ○仕様
*/
class Role_dummy_chiroptera extends Role {
  public $mix_in = 'self_cupid';
  public $display_role = 'self_cupid';

  protected function OutputPartner() {
    $user   = $this->GetActor();
    $target = $user->GetPartner($this->role);
    $stack  = $target;
    if (is_array($stack)) { //仮想恋人作成結果を表示
      $stack[] = $user->id;
      asort($stack);
      $pair = array();
      foreach ($stack as $id) $pair[] = DB::$USER->ById($id)->handle_name;
      RoleHTML::OutputPartner($pair, 'cupid_pair');
    }
    //仮想恋人を表示 (憑依追跡 / 恋人・悲恋持ちなら処理委託)
    if (! is_array($target) || $this->GetActor()->IsRole('lovers', 'sweet_status')) return;
    $lovers = array();
    foreach ($target as $id) {
      $lovers[] = DB::$USER->ByVirtual($id)->handle_name;
    }
    RoleHTML::OutputPartner($lovers, 'partner_header', 'lovers_footer');
  }

  function OutputAction() { $this->filter->OutputAction(); }

  function IsVote() { return DB::$ROOM->IsDate(1); }

  function IsFinishVote(array $list) { return $this->filter->IsFinishVote($list); }

  function SetVoteNight() { $this->filter->SetVoteNight(); }

  function GetVoteCheckbox(User $user, $id, $live) {
    return $this->filter->GetVoteCheckbox($user, $id, $live);
  }

  function CheckVoteNight() { $this->filter->CheckVoteNight(); }

  function VoteNightAction(array $list, $flag) {
    $stack = array();
    foreach ($list as $user) {
      $stack[] = $user->handle_name;
      if (! $this->IsActor($user)) $this->GetActor()->AddMainRole($user->id);
    }

    $this->SetStack(implode(' ', array_keys($list)), 'target_no');
    $this->SetStack(implode(' ', $stack), 'target_handle');
  }
}
