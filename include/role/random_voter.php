<?php
/*
  ����ʬ�� (random_voter)
  ������
  ����ɼ���� -1��+1 ���ϰϤǥ������������������
*/
class Role_random_voter extends Role{
  function Role_random_voter(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number += mt_rand(0, 2) - 1;
  }
}
