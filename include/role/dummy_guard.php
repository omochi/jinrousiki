<?php
/*
  ◆夢守人 (dummy_guard)
  ○仕様
*/
RoleManager::LoadFile('guard');
class Role_dummy_guard extends Role_guard{
  public $display_role = 'guard';
  function __construct(){ parent::__construct(); }

  function SetGuardTarget($uname){
    global $ROOM;
    if(! $ROOM->IsEvent('no_dream')) $this->AddStack($uname); //熱帯夜ならスキップ
    return false;
  }

  //夢防衛
  function GuardDream($user, $uname){
    global $ROOM, $USERS;

    if(! in_array($uname, $this->GetStack())) return false;
    $flag = false;
    foreach(array_keys($this->GetStack(), $uname) as $guard_uname){ //護衛者を検出
      $guard_user = $USERS->ByUname($guard_uname);
      if($guard_user->IsDead(true)) continue; //直前に死んでいたら無効

      $flag = true;
      if(! $ROOM->IsOption('seal_message')){ //狩りメッセージを登録
	$ROOM->SystemMessage($guard_user->handle_name . "\t" . $user->handle_name, 'GUARD_HUNTED');
      }
    }
    if($flag) $USERS->Kill($user->user_no, 'HUNTED');
    return $flag;
  }

  //護衛処理
  function DreamGuard(&$list){
    global $ROOM, $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //直前に死んでいたら無効

      $target = $USERS->ByUname($target_uname);
      if(($target->IsRole('dream_eater_mad') || $target->IsRoleGroup('fairy')) &&
	 $target->IsLive(true)){ //狩り判定 (獏・妖精系)
	$list[$user->handle_name] = $target;
      }
      //常時護衛成功メッセージだけが出る
      $ROOM->SystemMessage($user->GetHandleName($target->uname), 'GUARD_SUCCESS');
    }
  }

  //狩り処理
  function DreamHunt($list){
    global $ROOM, $USERS;

    foreach($list as $handle_name => $target){
      $USERS->Kill($target->user_no, 'HUNTED');
      //憑依能力者は対象外なので仮想ユーザを引く必要なし
      if(! $ROOM->IsOption('seal_message')){ //狩りメッセージを登録
	$ROOM->SystemMessage($handle_name . "\t" . $target->handle_name, 'GUARD_HUNTED');
      }
    }
  }
}
