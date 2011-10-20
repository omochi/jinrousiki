<?php
/*
  ◆目隠し (blinder)
  ○仕様
  ・自分以外の名前が見えなくなる
  ・人狼の遠吠え、共有者のひそひそ声には影響しない

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
class Role_blinder extends Role{
  function __construct(){ parent::__construct(); }

  //スキップ判定
  function IgnoreTalk(){
    global $USERS;
    return  ! $this->GetViewer()->virtual_live &&
      ! $USERS->IsVirtualLive($this->GetViewer()->user_no);
  }

  //発言フィルタ
  function FilterTalk($user, &$name, &$voice, &$str){
    global $ROOM;
    if($this->IgnoreTalk() || ! $ROOM->IsDay() || $this->GetViewer()->IsSame($user->uname)) return;
    $name = '';
  }

  //囁きフィルタ
  function FilterWhisper(&$voice, &$str){}
}
