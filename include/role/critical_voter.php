<?php
/*
  ���� (critical_voter)
  ������
  ��5% �γ�Ψ����ɼ���� +100 �����
*/
class Role_critical_voter extends Role{
  function Role_critical_voter(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number += mt_rand(1, 100) <= 5 ? 100 : 0;
  }
}
