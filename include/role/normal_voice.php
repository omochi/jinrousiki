<?php
/*
  ���Դ��� (normal_voice)
  ������
  �������礭����������������פǸ��ꤵ���
  ��������ץ쥤�����¸���Τ�ͭ��
*/
class Role_normal_voice extends RoleTalkFilter{
  function Role_normal_voice(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoice(&$volume, &$sentence){
    $volume = 'normal';
  }
}
