<?php
/*
  ◆スピーカー (speaker)
  ○仕様
  ・声の大きさが一段階大きくなり、大声は音割れしてしまう
  ・共有者の囁きは変換対象外
  ・ゲームプレイ中で生存時のみ有効

  ○問題点
  ・観戦モードにすると普通に見えてしまう
*/
RoleManager::LoadFile('strong_voice');
class Role_speaker extends Role_strong_voice{
  public $mix_in = 'blinder';
  function __construct(){ parent::__construct(); }

  function IgnoreTalk(){
    global $ROOM;
    return parent::IgnoreTalk() || ! $ROOM->IsPlaying();
  }

  function AddTalk($user, $talk, &$user_info, &$voice, &$str){
    if(! $this->IgnoreTalk()) $this->ShiftVoice($voice, $str);
  }

  function AddWhisper($role, $talk, &$user_info, &$voice, &$str){
    if(! $this->IgnoreTalk() && $role == 'wolf') $this->ShiftVoice($voice, $str);
  }
}
