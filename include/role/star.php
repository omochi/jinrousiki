<?php
/*
  ���͵��� (star)
  ������
  ����ɼ���� -1 �����
*/
class Role_star extends Role{
  function Role_star(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number--;
  }
}
