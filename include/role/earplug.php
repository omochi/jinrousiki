<?php
/*
  ◆耳栓 (earplug)
  ○仕様
  ・声の大きさが一段階小さくなり、小声は共有者のヒソヒソ声に見える
  ・生存時＆ゲームプレイ中のみ発動
  ・共有者のヒソヒソ声は変換対象外

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
class Role_earplug extends Role{
  function Role_earplug($user){
    parent::__construct($user);
  }

  function converter(&$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;

    switch($volume){
    case 'strong':
      $volume = 'normal';
      break;
    case 'normal':
      $volume = 'weak';
      break;
    case 'weak':
      $sentence = $MESSAGE->common_talk;
      break;
    }
  }

  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    $this->converter($volume, $sentence);
  }

  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$sentence){
    if($role == 'wolf') $this->converter($volume, $sentence);
  }
}
?>
