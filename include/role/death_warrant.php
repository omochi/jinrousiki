<?php
/*
  �������� (death_warrant)
  ������
  ��ȯư�����ʤ饷��å��ह��
*/
class Role_death_warrant extends Role{
  function Role_death_warrant(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $ROOM;
    if($reason == '' && $ROOM->date == max($ROLES->actor->GetPartner('death_warrant'))){
      $reason = 'WARRANT';
    }
  }
}
