<?php
/*
  �����۷� (inside_voice)
  ������
  �������礭������ϡ־����ס���ϡ������פǸ��ꤵ���
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_inside_voice extends RoleTalkFilter{
  function Role_inside_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    global $ROOM;
    $volume = $ROOM->IsDay() ? 'weak' : 'strong';
  }
}
