<?php
/*
  ����ȯ�� (downer_luck)
  ������
  ��2���ܤ���ɼ���� -4 ���������ˡ�3���ܰʹߤ� +2 ����롣
*/
class Role_downer_luck extends Role{
  function Role_downer_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    global $ROOM;
    $voted_number += $ROOM->date == 2 ? -4 : 2;
  }
}
