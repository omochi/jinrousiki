<?php
/*
  ���𴢤� (mower)
  ������
  ����ʬ��ȯ�������𤬾ä���
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_mower extends Role{
  function Role_mower(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    $sentence = strtr($sentence, array('w' => '', '��' => '', 'W' => '', '��' => ''));
  }
}
