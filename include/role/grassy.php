<?php
/*
  �����º� (grassy)
  ������
  ����ʬ��ȯ���ΰ�ʸ������𤬤Ĥ�
  �����Ԥθ� (��Ƭ) �ˤϤĤ��ʤ�
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_grassy extends Role{
  function Role_grassy(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    $result = '';
    $count = mb_strlen($sentence);
    for($i = 0; $i < $count; $i++){
      $str = mb_substr($sentence, $i, 1);
      $result .= ($str == "\n" ? $str : $str . 'w ');
    }
    $sentence = $result;
  }
}
