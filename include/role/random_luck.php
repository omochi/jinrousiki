<?php
/*
  ���������� (random_luck)
  ������
  ����ɼ���� -2��+2 ���ϰϤǥ������������������
*/
class Role_random_luck extends Role{
  function Role_random_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number += (mt_rand(1, 5) - 3);
  }
}
