<?php
/*
  ◆聖女 (saint)
  ○仕様
  ・役職表示：村人
  ・決定能力：候補者の内訳によって変化する
*/
class Role_saint extends RoleVoteAbility{
  public $display_role = 'human';
  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $USERS;

    if(parent::DecideVoteKill($uname)) return true;
    $stack = array();
    $target_stack = array();
    foreach($this->GetVotePossible() as $target_uname){ //最多得票者の情報を収集
      $user = $USERS->ByRealUname($target_uname); //$target_uname は仮想ユーザ
      if($user->IsRole('saint')) $stack[] = $target_uname;
      if(! $user->IsCamp('human', true)) $target_stack[] = $target_uname;
    }
    if(count($stack) > 0 && count($target_stack) < 2){ //対象を一人に固定できる時のみ有効
      if(isset($target_stack[0])) $uname = $target_stack[0];
      elseif(count($stack) == 1)  $uname = $stack[0];
    }
  }
}
