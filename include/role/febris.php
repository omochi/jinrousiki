<?php
/*
  ��Ǯ�� (febris)
  ������
  ��ȯư�����ʤ饷��å��ह��
*/
class Role_febris extends Role{
  function Role_febris(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $ROOM;
    if($reason == '' && $ROOM->date == max($ROLES->actor->GetPartner('febris'))) $reason = 'FEBRIS';
  }
}
