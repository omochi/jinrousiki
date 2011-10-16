<?php
/*
  ◆目隠し (blinder)
  ○仕様
  ・自分以外のハンドルネームが見えなくなる
  ・人狼の遠吠え、共有者のひそひそ声には影響しない
  ・ゲームプレイ中で生存時のみ有効

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
class Role_blinder extends Role{
  function __construct(){ parent::__construct(); }

  //発言フィルタ
  function AddTalk($user, $talk, &$user_info, &$voice, &$str){
    global $ROOM;

    if($this->IgnoreTalk() || ! $ROOM->IsDay() || $this->GetViewer()->IsSame($user->uname)) return;
    $user_info = '<font style="color:' . $user->color . '">◆</font>';
  }

  //囁きフィルタ
  function AddWhisper($role, $talk, &$user_info, &$voice, &$str){}

  //スキップ判定
  function IgnoreTalk(){
    global $USERS;
    return  ! $this->GetViewer()->virtual_live &&
      ! $USERS->IsVirtualLive($this->GetViewer()->user_no);
  }
}
