<?php
/*
  ◆策士 (trap_common)
  ○仕様
  ・非村人陣営の人全てから投票されたらまとめて死亡させる
*/
RoleManager::LoadFile('common');
class Role_trap_common extends RoleVoteAbility{
  public $mix_in = 'common';
  public $data_type = 'array';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function VotedReaction(){
    global $ROLES, $USERS;

    if(count($ROLES->stack->{$this->role}) < 1) return;
    $target_list = array();
    foreach(array_keys($ROLES->stack->target) as $uname){ //非村人陣営の ID と仮想ユーザ名を収集
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsCamp('human', true)){
	$target_list[$user->user_no] = $USERS->ByVirtual($user->user_no)->uname;
      }
    }
    //PrintData($target_list, '! Human');

    foreach($ROLES->stack->{$this->role} as $uname){ //策士の得票リストと照合
      if($this->GetVotedUname($uname) == array_values($target_list)){
	foreach(array_keys($target_list) as $id) $USERS->Kill($id, 'TRAPPED');
      }
    }
  }
}
