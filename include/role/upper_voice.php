<?php
/*
  ���ᥬ�ۥ� (upper_voice)
  ������
  �������礭�������ʳ��礭��ȯ�����졢�����ϲ���줷�Ƥ��ޤ�
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_upper_voice extends RoleTalkFilter{
  function Role_upper_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $this->ChangeVolume('up', $volume, $sentence);
  }
}
