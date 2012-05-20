<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_option_config', 'icon_class', 'session_class', 'user_class',
		     'game_vote_functions');
$INIT_CONF->LoadClass('ROLES', 'ROOM_OPT');

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
      VoteGameStart();
      break;

    case 'KICK_DO':
      VoteKick();
      break;

    default: //ここに来たらロジックエラー
      VoteHML::OutputError('ゲーム開始前投票');
      break;
    }
  }
  elseif (DB::$SELF->IsDead()) { //死者の霊界投票処理
    if (DB::$SELF->IsDummyBoy() && RQ::$get->situation == 'RESET_TIME') {
      VoteResetTime();
    }
    else {
      VoteHeaven();
    }
  }
  elseif (RQ::$get->target_no == 0) { //空投票検出
    VoteHTML::OutputError('空投票', '投票先を指定してください');
  }
  elseif (DB::$ROOM->IsDay()) { //昼の処刑投票処理
    VoteDay();
  }
  elseif (DB::$ROOM->IsNight()) { //夜の投票処理
    VoteNight();
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

//-- 関数 --//
//ゲーム開始投票の処理
function VoteGameStart(){
  CheckSituation('GAMESTART');
  $str = 'ゲーム開始';
  if (DB::$SELF->IsDummyBoy(true)) { //出題者以外の身代わり君
    if (GameConfig::$power_gm) { //強権モードによる強制開始処理
      if (! AggregateVoteGameStart(true)) $str .= '：開始人数に達していません。';
      DB::Commit();
      VoteHTML::OutputResult($str);
    }
    else {
      VoteHTML::OutputResult($str . '：身代わり君は投票不要です');
    }
  }

  //投票済みチェック
  DB::$ROOM->LoadVote();
  if (in_array(DB::$SELF->user_no, DB::$ROOM->vote)) {
    VoteHTML::OutputResult($str . '：投票済みです');
  }

  if (DB::$SELF->Vote('GAMESTART')) { //投票処理
    AggregateVoteGameStart(); //集計処理
    DB::Commit();
    VoteHTML::OutputResult($str . '：投票完了');
  }
  else {
    VoteHTML::OutputResult($str . '：データベースエラー');
  }
}

//開始前の Kick 投票の処理
function VoteKick(){
  CheckSituation('KICK_DO'); //コマンドチェック
  $str = 'Kick 投票：';
  $target = DB::$USER->ByID(RQ::$get->target_no); //投票先のユーザ情報を取得
  if ($target->uname == '' || $target->live == 'kick') {
    VoteHTML::OutputResult($str . '投票先が指定されていないか、すでに Kick されています');
  }
  if ($target->IsDummyBoy()) VoteHTML::OutputResult($str . '身代わり君には投票できません');
  if (! GameConfig::$self_kick && $target->IsSelf()) {
    VoteHTML::OutputResult($str . '自分には投票できません');
  }

  //ゲーム開始チェック
  if (DB::FetchResult(DB::$ROOM->GetQueryHeader('room', 'scene')) != 'beforegame') {
    VoteHTML::OutputResult($str . '既にゲーム開始されています');
  }

  DB::$ROOM->LoadVote(true); //投票情報をロード
  $id = DB::$SELF->user_no;
  if (isset(DB::$ROOM->vote[$id]) && in_array($target->user_no, DB::$ROOM->vote[$id])) {
    VoteHTML::OutputResult($str . "{$target->handle_name} さんへ Kick 投票済み");
  }

  if (DB::$SELF->Vote('KICK_DO', $target->user_no)) { //投票処理
    DB::$ROOM->Talk($target->handle_name, 'KICK_DO', DB::$SELF->uname); //投票しました通知
    $vote_count = AggregateVoteKick($target); //集計処理
    DB::Commit();
    $add_str = '投票完了：%s さん：%d 人目 (Kick するには %d 人以上の投票が必要です)';
    $str .= sprintf($add_str, $target->handle_name, $vote_count, GameConfig::$kick);
    VoteHTML::OutputResult($str);
  }
  else {
    VoteHTML::OutputResult($str . 'データベースエラー');
  }
}

//Kick 投票の集計処理 ($target : 対象 HN, 返り値 : 対象 HN の投票合計数)
function AggregateVoteKick($target){
  CheckSituation('KICK_DO'); //コマンドチェック

  //今回投票した相手にすでに投票している人数を取得
  $vote_count = 1;
  foreach (DB::$ROOM->vote as $stack) {
    if (in_array($target->user_no, $stack)) $vote_count++;
  }

  //規定数以上の投票があった / キッカーが身代わり君 / 自己 KICK が有効の場合に処理
  if ($vote_count < GameConfig::$kick && ! DB::$SELF->IsDummyBoy() &&
      ! (GameConfig::$self_kick && $target->IsSelf())) {
    return $vote_count;
  }
  $query = "UPDATE user_entry SET live = 'kick', session_id = NULL " .
    sprintf('WHERE room_no = %d AND user_no = %d', DB::$ROOM->id, $target->user_no);
  DB::Execute($query);

  //通知処理
  DB::$ROOM->Talk($target->handle_name . Message::$kick_out);
  DB::$ROOM->Talk(Message::$vote_reset);

  //投票リセット処理
  DB::$ROOM->UpdateVoteCount();
  DB::$ROOM->UpdateTime();
  return $vote_count;
}

//死者の投票処理
function VoteHeaven(){
  CheckSituation('REVIVE_REFUSE'); //コマンドチェック
  if (DB::$SELF->IsDrop())     VoteHTML::OutputResult('蘇生辞退：投票済み'); //投票済みチェック
  if (DB::$ROOM->IsOpenCast()) VoteHTML::OutputResult('蘇生辞退：投票不要です'); //霊界公開判定

  //-- 投票処理 --//
  if (! DB::$SELF->Update('live', 'drop')) VoteHTML::OutputResult('データベースエラー');

  //システムメッセージ
  $str = 'システム：' . DB::$SELF->handle_name . 'さんは蘇生を辞退しました。';
  DB::$ROOM->Talk($str, null, DB::$SELF->uname, 'heaven', null, 'normal');

  DB::Commit();
  VoteHTML::OutputResult('投票完了');
}

//最終更新時刻リセット投票処理 (身代わり君専用)
function VoteResetTime(){
  CheckSituation('RESET_TIME'); //コマンドチェック

  //-- 投票処理 --//
  DB::$ROOM->UpdateTime(); //更新時間リセット

  //システムメッセージ
  $str = 'システム：投票制限時間をリセットしました。';
  DB::$ROOM->Talk($str, null, DB::$SELF->uname, DB::$ROOM->scene, 'dummy_boy');
  DB::Commit();
  VoteHTML::OutputResult('投票完了');
}

//投票済みチェック
function CheckAlreadyVote($action, $not_action = ''){
  if (CheckSelfVoteNight($action, $not_action)) VoteHTML::OutputResult('夜：投票済み');
}
