<?php
/*
  ���Կ͵� (disfavor)
  ������
  ����ɼ���� +1 �����
*/
class Role_disfavor extends Role{
  function Role_disfavor(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number++;
  }
}
