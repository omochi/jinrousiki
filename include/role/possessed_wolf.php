<?php
/*
  ◆憑狼 (possessed_wolf)
  ○仕様
  ・襲撃：憑依
*/
RoleManager::LoadFile('wolf');
class Role_possessed_wolf extends Role_wolf{
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputPossessedTarget(); //現在の憑依先
  }

  function WolfKill($user, &$list){
    if($user->IsDummyBoy() || $user->IsCamp('fox') || $user->IsPossessedLimited()){ //スキップ判定
      parent::WolfKill($user, $list);
      return;
    }
    $list[$this->GetVoter()->uname] = $user->uname;
    $user->dead_flag = true;
    if($user->IsRole('anti_voodoo')) $this->GetVoter()->possessed_reset = true; //憑依リセット判定
  }

  //憑依処理
  function Possessed(){
    global $ROOM, $USERS;

    $possessed_date = $ROOM->date + 1; //憑依する日を取得
    foreach($this->GetStack('possessed') as $uname => $target_uname){
      $user    = $USERS->ByUname($uname); //憑依者
      $target  = $USERS->ByUname($target_uname); //憑依予定先
      $virtual = $USERS->ByVirtual($user->user_no); //現在の憑依先
      //PrintData($user);
      if(! property_exists($user, 'possessed_reset'))  $user->possessed_reset  = NULL;
      if(! property_exists($user, 'possessed_cancel')) $user->possessed_cancel = NULL;

      if($user->IsDead(true)){ //憑依者死亡
	$target->dead_flag = false; //死亡フラグをリセット
	$USERS->Kill($target->user_no, 'WOLF_KILLED');
	if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
      }
      elseif($user->possessed_reset){ //憑依リセット
	if(isset($target->user_no)){
	  $target->dead_flag = false; //死亡フラグをリセット
	  $USERS->Kill($target->user_no, 'WOLF_KILLED');
	  if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
	}

	if($user != $virtual){ //憑依中なら元の体に戻される
	  //憑依先のリセット処理
	  $virtual->ReturnPossessed('possessed');
	  $virtual->SaveLastWords();
	  $ROOM->SystemMessage($virtual->handle_name, 'POSSESSED_RESET');

	  //見かけ上の蘇生処理
	  $user->ReturnPossessed('possessed_target');
	  $user->SaveLastWords($virtual->handle_name);
	  $ROOM->SystemMessage($user->handle_name, 'REVIVE_SUCCESS');
	}
	continue;
      }
      elseif($user->possessed_cancel || $target->revive_flag){ //憑依失敗
	$target->dead_flag = false; //死亡フラグをリセット
	$USERS->Kill($target->user_no, 'WOLF_KILLED');
	if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
	continue;
      }
      else{ //憑依成功
	if($user->IsRole('possessed_wolf')){
	  $target->dead_flag = false; //死亡フラグをリセット
	  $USERS->Kill($target->user_no, 'POSSESSED_TARGETED'); //憑依先の死亡処理
	  //憑依先が誰かに憑依しているケースがあるので仮想ユーザで上書きする
	  //Ver. 1.5.0 β13 の仕様変更でこのケースはなくなったはず
	  $target = $USERS->ByVirtual($target->user_no);
	}
	else{
	  $ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');
	  $user->LostAbility();
	}
	$target->AddRole("possessed[{$possessed_date}-{$user->user_no}]");

	//憑依処理
	$user->AddRole("possessed_target[{$possessed_date}-{$target->user_no}]");
	$ROOM->SystemMessage($virtual->handle_name, 'POSSESSED');
	$user->SaveLastWords($virtual->handle_name);
	$user->Update('last_words', '');
      }

      if($user != $virtual){
	$virtual->ReturnPossessed('possessed');
	if($user->IsLive(true)) $virtual->SaveLastWords();
      }
    }
  }
}
