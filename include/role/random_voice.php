<?php
/*
  �����¼� (random_voice)
  ������
  �������礭������������Ѳ�����
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_random_voice extends RoleTalkFilter{
  function Role_random_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $volume = GetRandom($this->volume_list);
  }
}
