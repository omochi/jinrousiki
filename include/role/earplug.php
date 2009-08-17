<?php
/*
  ������ (earplug)
  ���d�l
  �E���̑傫������i�K�������Ȃ�A�����͋��L�҂̃q�\�q�\���Ɍ�����
  �E���������Q�[���v���C���̂ݔ���
  �E���L�҂̃q�\�q�\���͕ϊ��ΏۊO

  �����_
  �E�ϐ탂�[�h�ɂ���ƕ��ʂɌ����Ă��܂�
*/
class Role_earplug extends Role{
  function Role_earplug($user){
    parent::__construct($user);
  }

  function converter(&$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;

    switch($volume){
    case 'strong':
      $volume = 'normal';
      break;
    case 'normal':
      $volume = 'weak';
      break;
    case 'weak':
      $sentence = $MESSAGE->common_talk;
      break;
    }
  }

  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
    $this->converter($volume, $sentence);
  }

  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$sentence){
    if($role == 'wolf') $this->converter($volume, $sentence);
  }
}
?>
