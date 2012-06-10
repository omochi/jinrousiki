<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('icon_class', 'session_class', 'user_class', 'role_class', 'room_option_class',
		     'game_vote_functions');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameVote', true);
DB::Connect();
Session::Certify(); //セッション認証

//ロック処理
if (! DB::Transaction()) VoteHTML::OutputResult('サーバが混雑しています。再度投票をお願いします。');

DB::$ROOM = new Room(RQ::$get, true); //村情報をロード
if (DB::$ROOM->IsFinished()) VoteHTML::OutputError('ゲーム終了', 'ゲームは終了しました');
DB::$ROOM->system_time = Time::Get(); //現在時刻を取得

DB::$USER = new UserDataSet(RQ::$get, true); //ユーザ情報をロード
DB::$SELF = DB::$USER->BySession(); //自分の情報をロード

//-- メインルーチン --//
if (RQ::$get->vote) { //投票処理
  if (DB::$ROOM->IsBeforeGame()) { //ゲーム開始 or Kick 投票処理
    switch (RQ::$get->situation) {
    case 'GAMESTART':
      $INIT_CONF->LoadFile('chaos_config', 'cast_class'); //配役情報をロード
      Vote::VoteGameStart();
      break;

    case 'KICK_DO':
      Vote::VoteKick();
      break;

    default: //ここに来たらロジックエラー
      VoteHML::OutputError('ゲーム開始前投票');
      break;
    }
  }
  elseif (DB::$SELF->IsDead()) { //死者の霊界投票処理
    if (DB::$SELF->IsDummyBoy() && RQ::$get->situation == 'RESET_TIME') {
      Vote::VoteResetTime();
    }
    else {
      Vote::VoteHeaven();
    }
  }
  elseif (RQ::$get->target_no == 0) { //空投票検出
    VoteHTML::OutputError('空投票', '投票先を指定してください');
  }
  elseif (DB::$ROOM->IsDay()) { //昼の処刑投票処理
    Vote::VoteDay();
  }
  elseif (DB::$ROOM->IsNight()) { //夜の投票処理
    Vote::VoteNight();
  }
  else { //ここに来たらロジックエラー
    VoteHTML::OutputError('投票コマンドエラー', '投票先を指定してください');
  }
}
else { //シーンに合わせた投票ページを出力
  $INIT_CONF->LoadFile('vote_message');
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

    default: //ここに来たらロジックエラー
      VoteHTML::OutputError('投票シーンエラー');
      break;
    }
  }
}
DB::Disconnect();
