<?php
/*
  ���ޥ��� (downer_voice)
  ������
  �������礭�������ʳ�������ȯ�����졢�����϶�ͭ�Ԥ��񤭤��Ѵ�����Ƥ��ޤ�
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_downer_voice extends RoleTalkFilter{
  function Role_downer_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $this->ChangeVolume('down', $volume, $sentence);
  }
}
