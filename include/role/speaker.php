<?php
/*
  �����ԡ����� (speaker)
  ������
  �������礭�������ʳ��礭���ʤꡢ�����ϲ���줷�Ƥ��ޤ�
  ����¸����������ץ쥤��Τ�ȯư
  ����ͭ�ԤΥҥ��ҥ������Ѵ��оݳ�

  ��������
  ������⡼�ɤˤ�������̤˸����Ƥ��ޤ�
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
