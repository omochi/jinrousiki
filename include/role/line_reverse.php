<?php
/*
  ��ŷ���º� (line_reverse)
  ������
  ����ʬ��ȯ������ñ�̤Ǿ岼�������ؤ��
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)

  ��������
  ���Ǹ夬���Ԥ��ä����ϥ��åȤ���� (explode + implode �λ���)
*/
class Role_line_reverse extends Role{
  function Role_line_reverse(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    $sentence = implode("\n", array_reverse(explode("\n", $sentence)));
  }
}
