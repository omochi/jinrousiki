<?php
/*
  ◆奇術師 (trick_mania)
  ○仕様
  ・コピー：交換コピー
*/
class Role_trick_mania extends Role{
  public $copied = 'copied_trick';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    $flag = false;
    if($user->IsRoleGroup('mania')){ //神話マニア陣営を選択した場合は村人
      $result = 'human';
      $flag = true;
    }
    elseif($user->IsRole('revive_priest')){ //天人は交換コピー対象外
      $result = $user->main_role;
      $flag = true;
    }
    else{
      foreach($vote_data as $stack){ //交換コピー判定
	if(array_key_exists($user->uname, $stack)){
	  $flag = true;
	  break;
	}
      }
      $result = $user->main_role;
    }
    $this->GetActor()->ReplaceRole($this->role, $result);
    $this->GetActor()->AddRole($this->copied);
    if(! $flag && ! $user->IsDummyBoy()){
      $user->ReplaceRole($user->main_role, $user->DistinguishRoleGroup());
    }
    return $result;
  }
}
