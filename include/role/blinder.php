<?php
/*
  ���ܱ��� (blinder)
  ������
  ����ʬ�ʳ��Υϥ�ɥ�͡��ब�����ʤ��ʤ�
  ����¸����������ץ쥤��Τ�ȯư
  ����ϵ�α��ʤ�����ͭ�ԤΤҤ��Ҥ����ˤϱƶ����ʤ�

  ��������
  ������⡼�ɤˤ�������̤˸����Ƥ��ޤ�
*/
class Role_blinder extends Role{
  function Role_blinder($user){
    parent::__construct($user);
  }

  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    if($this->Ignored() || $this->SameUser($user_info)) return;
    $user_info = '<font style="color:'.$user->color.'">��</font>';
  }
}
?>
