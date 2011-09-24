<?php
/*
  ◆舟幽霊 (follow_mad)
  ○仕様
  ・道連れ：投票先がショック死していたら誰か一人をさらにショック死させる
*/
class Role_follow_mad extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function Followed($user_list){
    global $USERS;

    $count = 0; //能力発動カウント
    $follow_stack = array(); //有効投票先リスト
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($this->IsVoted($target->uname)) continue;
      $target->suicide_flag ? $count++ : $follow_stack[$uname] = $target->user_no;
    }
    //PrintData($follow_stack, 'follow_mad:' . $count);
    if($count < 1) return false;

    $target_stack = array(); //対象者リスト
    foreach($user_list as $uname){ //情報収集
      $user = $USERS->ByRealUname($uname);
      if($user->IsLive(true) && ! $user->IsAvoid(true)) $target_stack[] = $user->user_no;
    }
    //PrintData($target_stack, 'BaseFollowTarget');

    while($count > 0 && count($target_stack) > 0){ //道連れ処理
      $count--;
      shuffle($target_stack); //配列をシャッフル
      $id = array_shift($target_stack);
      $USERS->SuddenDeath($id, 'SUDDEN_DEATH_FOLLOWED'); //死亡処理

      if(! in_array($id, $follow_stack)) continue;//連鎖判定
      $stack = array();
      foreach($follow_stack as $uname => $user_no){
	$id == $user_no ? $count++ : $stack[$uname] = $user_no;
      }
      $follow_stack = $stack;
    }
  }
}
