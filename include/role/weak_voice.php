<?php
/*
  ������ (weak_voice)
  ������
  �������礭��������־����פǸ��ꤵ���
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_weak_voice extends RoleTalkFilter{
  function Role_weak_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $volume = 'weak';
  }
}
