<?php
/*
  ◆スピーカー (speaker)
  ○仕様
  ・声の大きさが一段階大きくなり、大声は音割れしてしまう
  ・生存時＆ゲームプレイ中のみ発動
  ・共有者のヒソヒソ声は変換対象外

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
class Role_speaker extends Role{
  function Role_speaker($user){
    parent::__construct($user);
  }

  function converter(&$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;

    switch($volume){
    case 'strong':
      $sentence = $MESSAGE->howling;
      break;
    case 'normal':
      $volume = 'strong';
      break;
    case 'weak':
      $volume = 'normal';
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
