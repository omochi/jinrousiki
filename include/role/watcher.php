<?php
/*
  ��˵�Ѽ� (watcher)
  ������
  ����ɼ���� 0 �Ǹ��ꤵ���
*/
class Role_watcher extends Role{
  function Role_watcher(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number = 0;
  }
}
