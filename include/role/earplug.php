<?php
/*
  ������ (earplug)
  ������
  �������礭�������ʳ��������ʤꡢ�����϶�ͭ�Ԥ��񤭤˸�����
  ����ͭ�Ԥ��񤭤��Ѵ��оݳ�
  ��������ץ쥤�����¸���Τ�ͭ��

  ��������
  ������⡼�ɤˤ�������̤˸����Ƥ��ޤ�
*/
class Role_earplug extends RoleTalkFilter{
  function Role_earplug(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function AddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    $this->ChangeVolume('down', $volume, $sentence);
  }

  function AddWhisper($role, $talk, &$user_info, &$volume, &$sentence){
    if($role == 'wolf') $this->ChangeVolume('down', $volume, $sentence);
  }
}
