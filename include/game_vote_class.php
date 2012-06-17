<?php
//-- GameVote �o�̓N���X --//
class GameVote {
  //�o��
  static function Output() {
    //-- �f�[�^���W --//
    DB::Connect();
    Session::Certify(); //�Z�b�V�����F��

    if (! DB::Transaction()) { //���b�N����
      VoteHTML::OutputResult('�T�[�o�����G���Ă��܂��B�ēx���[�����肢���܂��B');
    }

    DB::$ROOM = new Room(RQ::$get, true); //���������[�h
    if (DB::$ROOM->IsFinished()) VoteHTML::OutputError('�Q�[���I��', '�Q�[���͏I�����܂���');
    DB::$ROOM->system_time = Time::Get(); //���ݎ������擾

    DB::$USER = new UserDataSet(RQ::$get, true); //���[�U�������[�h
    DB::$SELF = DB::$USER->BySession(); //�����̏������[�h

    //-- ���C�����[�`�� --//
    if (RQ::$get->vote) { //���[����
      if (DB::$ROOM->IsBeforeGame()) { //�Q�[���J�n or Kick ���[����
	switch (RQ::$get->situation) {
	case 'GAMESTART':
	  Loader::LoadFile('chaos_config', 'cast_class'); //�z���������[�h
	  Vote::VoteGameStart();
	  break;

	case 'KICK_DO':
	  Vote::VoteKick();
	  break;

	default: //�����ɗ����烍�W�b�N�G���[
	  VoteHML::OutputError('�Q�[���J�n�O���[');
	  break;
	}
      }
      elseif (DB::$SELF->IsDead()) { //���҂̗�E���[����
	if (DB::$SELF->IsDummyBoy() && RQ::$get->situation == 'RESET_TIME') {
	  Vote::VoteResetTime();
	}
	else {
	  Vote::VoteHeaven();
	}
      }
      elseif (RQ::$get->target_no == 0) { //�󓊕[���o
	VoteHTML::OutputError('�󓊕[', '���[����w�肵�Ă�������');
      }
      elseif (DB::$ROOM->IsDay()) { //���̏��Y���[����
	Vote::VoteDay();
      }
      elseif (DB::$ROOM->IsNight()) { //��̓��[����
	Vote::VoteNight();
      }
      else { //�����ɗ����烍�W�b�N�G���[
	VoteHTML::OutputError('���[�R�}���h�G���[', '���[����w�肵�Ă�������');
      }
    }
    else { //�V�[���ɍ��킹�����[�y�[�W���o��
      Loader::LoadFile('vote_message');
      if (DB::$SELF->IsDead()) {
	DB::$SELF->IsDummyBoy() ? VoteHTML::OutputDummyBoy() : VoteHTML::OutputHeaven();
      }
      else {
	switch (DB::$ROOM->scene) {
	case 'beforegame':
	  VoteHTML::OutputBeforeGame();
	  break;

	case 'day':
	  VoteHTML::OutputDay();
	  break;

	case 'night':
	  VoteHTML::OutputNight();
	  break;

	default: //�����ɗ����烍�W�b�N�G���[
	  VoteHTML::OutputError('���[�V�[���G���[');
	  break;
	}
      }
    }
    DB::Disconnect();
  }
}
