<?php
/*
  ������ (strong_voice)
  ������
  �������礭��������������פǸ��ꤵ���
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_strong_voice extends RoleTalkFilter{
  function Role_strong_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $volume = 'strong';
  }
}
