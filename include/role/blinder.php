<?php
/*
  ���ܱ��� (blinder)
  ������
  ����ʬ�ʳ��Υϥ�ɥ�͡��ब�����ʤ��ʤ�
  ����ϵ�α��ʤ�����ͭ�ԤΤҤ��Ҥ����ˤϱƶ����ʤ�
  ��������ץ쥤�����¸���Τ�ͭ��

  ��������
  ������⡼�ɤˤ�������̤˸����Ƥ��ޤ�
*/
class Role_blinder extends RoleTalkFilter{
  function Role_blinder(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function AddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    if($this->Ignored() || $this->IsSameUser($user->uname)) return;
    $user_info = '<font style="color:' . $user->color . '">��</font>';
  }
}
