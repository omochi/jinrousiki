<?php
/*
  �����ϼ� (authority)
  ������
  ����ɼ���� +1 �����
*/
class Role_authority extends Role{
  function Role_authority(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
