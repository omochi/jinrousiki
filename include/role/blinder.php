<?php
/*
  ◆目隠し (blinder)
  ○仕様
  ・自分以外のハンドルネームが見えなくなる
  ・生存時＆ゲームプレイ中のみ発動
  ・人狼の遠吠え、共有者のひそひそ声には影響しない

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
class Role_blinder extends Role{
  function Role_blinder($user){
    parent::__construct($user);
  }

  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    if($this->Ignored() || $this->SameUser($user_info)) return;
    $user_info = '<font style="color:'.$user->color.'">◆</font>';
  }
}
?>
