<?php
/*
  �����ԡ����� (speaker)
  ������
  �������礭�������ʳ��礭���ʤꡢ�����ϲ���줷�Ƥ��ޤ�
  ����ͭ�Ԥ��񤭤��Ѵ��оݳ�
  ��������ץ쥤�����¸���Τ�ͭ��

  ��������
  ������⡼�ɤˤ�������̤˸����Ƥ��ޤ�
*/
class Role_speaker extends RoleTalkFilter{
  function Role_speaker(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function AddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    $this->ChangeVolume('up', $volume, $sentence);
  }

  function AddWhisper($role, $talk, &$user_info, &$volume, &$sentence){
    if($role == 'wolf') $this->ChangeVolume('up', $volume, $sentence);
  }
}
