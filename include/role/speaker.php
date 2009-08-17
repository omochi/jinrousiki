<?php
/*
  ���X�s�[�J�[ (speaker)
  ���d�l
  �E���̑傫������i�K�傫���Ȃ�A�吺�͉����ꂵ�Ă��܂�
  �E���������Q�[���v���C���̂ݔ���
  �E���L�҂̃q�\�q�\���͕ϊ��ΏۊO

  �����_
  �E�ϐ탂�[�h�ɂ���ƕ��ʂɌ����Ă��܂�
*/
class Role_speaker extends Role{
  function Role_speaker($user){
    parent::__construct($user);
  }

  function converter(&$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;

    switch($volume){
    case 'strong':
      $sentence = $MESSAGE->howling;
      break;
    case 'normal':
      $volume = 'strong';
      break;
    case 'weak':
      $volume = 'normal';
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
