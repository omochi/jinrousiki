<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('session_class', 'user_class', 'game_vote_functions');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'ROOM_OPT', 'GAME_OPT_CONF');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameVote', true);
DB::Connect();
Session::Certify(); //セッション認証

//ロック処理
if (! DB::Transaction()) OutputVoteResult('サーバが混雑しています。再度投票をお願いします。');

DB::$ROOM = new Room(RQ::$get, true); //村情報をロード
if (DB::$ROOM->IsFinished()) OutputVoteError('ゲーム終了', 'ゲームは終了しました');
DB::$ROOM->system_time = Time::Get(); //現在時刻を取得

DB::$USER = new UserDataSet(RQ::$get, true); //ユーザ情報をロード
DB::$SELF = DB::$USER->BySession(); //自分の情報をロード

//-- メインルーチン --//
if (RQ::$get->vote) { //投票処理
  if (DB::$ROOM->IsBeforeGame()) { //ゲーム開始 or Kick 投票処理
    switch (RQ::$get->situation) {
    case 'GAMESTART':
      $INIT_CONF->LoadFile('chaos_config'); //配役情報をロード
      VoteGameStart();
      break;

    case 'KICK_DO':
      VoteKick();
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('ゲーム開始前投票');
      break;
    }
  }
  elseif (DB::$SELF->IsDead()) { //死者の霊界投票処理
    if (DB::$SELF->IsDummyBoy() && RQ::$get->situation == 'RESET_TIME') {
      VoteResetTime();
    }
    else {
      VoteDeadUser();
    }
  }
  elseif (RQ::$get->target_no == 0) { //空投票検出
    OutputVoteError('空投票', '投票先を指定してください');
  }
  elseif (DB::$ROOM->IsDay()) { //昼の処刑投票処理
    VoteDay();
  }
  elseif (DB::$ROOM->IsNight()) { //夜の投票処理
    VoteNight();
  }
  else { //ここに来たらロジックエラー
    OutputVoteError('投票コマンドエラー', '投票先を指定してください');
  }
}
else { //シーンに合わせた投票ページを出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  if (DB::$SELF->IsDead()) {
    DB::$SELF->IsDummyBoy() ? OutputVoteDummyBoy() : OutputVoteDeadUser();
  }
  else {
    switch (DB::$ROOM->scene) {
    case 'beforegame':
      OutputVoteBeforeGame();
      break;

    case 'day':
      OutputVoteDay();
      break;

    case 'night':
      OutputVoteNight();
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('投票シーンエラー');
      break;
    }
  }
}
DB::Disconnect();

//-- 関数 --//
//エラーページ出力
function OutputVoteError($title, $str = null){
  $header = '<div align="center"><a id="game_top"></a>';
  $footer = sprintf("<br>\n%s</div>", RQ::$get->back_url);
  if (is_null($str)) $str = 'プログラムエラーです。管理者に問い合わせてください。';
  HTML::OutputResult(sprintf('投票エラー [%s]', $title), $header . $str . $footer);
}

//ゲーム開始投票の処理
function VoteGameStart(){
  CheckSituation('GAMESTART');
  $str = 'ゲーム開始';
  if (DB::$SELF->IsDummyBoy(true)) { //出題者以外の身代わり君
    if (GameConfig::$power_gm) { //強権モードによる強制開始処理
      if (! AggregateVoteGameStart(true)) $str .= '：開始人数に達していません。';
      DB::Commit();
      OutputVoteResult($str);
    }
    else {
      OutputVoteResult($str . '：身代わり君は投票不要です');
    }
  }

  //投票済みチェック
  DB::$ROOM->LoadVote();
  if (in_array(DB::$SELF->user_no, DB::$ROOM->vote)) OutputVoteResult($str . '：投票済みです');

  if (DB::$SELF->Vote('GAMESTART')) { //投票処理
    AggregateVoteGameStart(); //集計処理
    DB::Commit();
    OutputVoteResult($str . '：投票完了');
  }
  else {
    OutputVoteResult($str . '：データベースエラー');
  }
}

//開始前の Kick 投票の処理
function VoteKick(){
  CheckSituation('KICK_DO'); //コマンドチェック
  $str = 'Kick 投票：';
  $target = DB::$USER->ByID(RQ::$get->target_no); //投票先のユーザ情報を取得
  if ($target->uname == '' || $target->live == 'kick') {
    OutputVoteResult($str . '投票先が指定されていないか、すでに Kick されています');
  }
  if ($target->IsDummyBoy()) OutputVoteResult($str . '身代わり君には投票できません');
  if (! GameConfig::$self_kick && $target->IsSelf()) {
    OutputVoteResult($str . '自分には投票できません');
  }

  //ゲーム開始チェック
  if (DB::FetchResult(DB::$ROOM->GetQueryHeader('room', 'scene')) != 'beforegame') {
    OutputVoteResult($str . '既にゲーム開始されています');
  }

  DB::$ROOM->LoadVote(true); //投票情報をロード
  $id = DB::$SELF->user_no;
  if (isset(DB::$ROOM->vote[$id]) && in_array($target->user_no, DB::$ROOM->vote[$id])) {
    OutputVoteResult($str . "{$target->handle_name} さんへ Kick 投票済み");
  }

  if (DB::$SELF->Vote('KICK_DO', $target->user_no)) { //投票処理
    DB::$ROOM->Talk($target->handle_name, 'KICK_DO', DB::$SELF->uname); //投票しました通知
    $vote_count = AggregateVoteKick($target); //集計処理
    DB::Commit();
    $add_str = '投票完了：%s さん：%d 人目 (Kick するには %d 人以上の投票が必要です)';
    $str .= sprintf($add_str, $target->handle_name, $vote_count, GameConfig::$kick);
    OutputVoteResult($str);
  }
  else {
    OutputVoteResult($str . 'データベースエラー');
  }
}

//Kick 投票の集計処理 ($target : 対象 HN, 返り値 : 対象 HN の投票合計数)
function AggregateVoteKick($target){
  global $MESSAGE;

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
  DB::$ROOM->Talk($target->handle_name . $MESSAGE->kick_out);
  DB::$ROOM->Talk($MESSAGE->vote_reset);

  //投票リセット処理
  DB::$ROOM->UpdateVoteCount();
  DB::$ROOM->UpdateTime();
  return $vote_count;
}

//死者の投票処理
function VoteDeadUser(){
  CheckSituation('REVIVE_REFUSE'); //コマンドチェック
  if (DB::$SELF->IsDrop())     OutputVoteResult('蘇生辞退：投票済み'); //投票済みチェック
  if (DB::$ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です'); //霊界公開判定

  //-- 投票処理 --//
  if (! DB::$SELF->Update('live', 'drop')) OutputVoteResult('データベースエラー');

  //システムメッセージ
  $str = 'システム：' . DB::$SELF->handle_name . 'さんは蘇生を辞退しました。';
  DB::$ROOM->Talk($str, null, DB::$SELF->uname, 'heaven', null, 'normal');

  DB::Commit();
  OutputVoteResult('投票完了');
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
  OutputVoteResult('投票完了');
}

//開始前の投票ページ出力
function OutputVoteBeforeGame(){
  global $ICON_CONF, $VOTE_MESS;

  CheckScene(); //投票する状況があっているかチェック
  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page"><tr>'."\n";

  $count  = 0;
  $header = '<input type="radio" name="target_no" id="';
  foreach (DB::$USER->rows as $id => $user) {
    if ($count > 0 && $count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    $checkbox = ! $user->IsDummyBoy() && (GameConfig::$self_kick || ! $user->IsSelf()) ?
      $header . $id . '" value="' . $id . '">'."\n" : '';
    echo $user->GenerateVoteTag($ICON_CONF->path . '/' . $user->icon_filename, $checkbox);
  }

  $str = <<<EOF
</tr></table>
<span class="vote-message">* Kick するには %d 人の投票が必要です</span>
<div class="vote-page-link" align="right"><table><tr>
<td>%s</td>
<td><input type="submit" value="%s"></form></td>
<td>
<form method="POST" action="%s">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="%s">
</form>
</td>
</tr></table></div>
</body></html>

EOF;

  printf($str, GameConfig::$kick, RQ::$get->back_url, $VOTE_MESS->kick_do, RQ::$get->post_url,
	 $VOTE_MESS->game_start);
}

//昼の投票ページを出力する
function OutputVoteDay(){
  global $ICON_CONF, $VOTE_MESS;

  CheckScene(); //投票する状況があっているかチェック
  if (DB::$ROOM->date == 1) OutputVoteResult('処刑：初日は投票不要です');
  $revote_count = DB::$ROOM->revote_count;

  //投票済みチェック
  $str = " AND scene = '%s' AND vote_count = %d AND revote_count = %d AND user_no = %d";
  $query = DB::$ROOM->GetQuery(true, 'vote') .
    sprintf($str, DB::$ROOM->scene, DB::$ROOM->vote_count, $revote_count, DB::$SELF->user_no);

  if (DB::FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');
  if (isset(DB::$ROOM->event->vote_duel) && is_array(DB::$ROOM->event->vote_duel)) { //特殊イベントを取得
    $user_stack = array();
    foreach (DB::$ROOM->event->vote_duel as $id) $user_stack[$id] = DB::$USER->rows[$id];
  }
  else {
    $user_stack = DB::$USER->rows;
  }
  $virtual_self = DB::$USER->ByVirtual(DB::$SELF->user_no); //仮想投票者を取得

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="revote_count" value="{$revote_count}">
<table class="vote-page"><tr>

EOF;

  $checkbox_header = "\n".'<input type="radio" name="target_no" id="';
  $count = 0;
  foreach ($user_stack as $id => $user) {
    if ($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    $is_live = DB::$USER->IsVirtualLive($id);

    //生きていればユーザアイコン、死んでれば死亡アイコン
    $path = $is_live ? $ICON_CONF->path . '/' . $user->icon_filename : $ICON_CONF->dead;
    $checkbox = ($is_live && ! $user->IsSame($virtual_self->uname)) ?
      $checkbox_header . $id . '" value="' . $id . '">' : '';
    echo $user->GenerateVoteTag($path, $checkbox);
  }

  $url = RQ::$get->back_url;
  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$url}</td>
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//死者の投票ページ出力
function OutputVoteDeadUser(){
  global $VOTE_MESS;

  //投票済みチェック
  if (DB::$SELF->IsDrop())     OutputVoteResult('蘇生辞退：投票済み');
  if (DB::$ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です');

  OutputVotePageHeader();
  $url = RQ::$get->back_url;
  echo <<<EOF
<input type="hidden" name="situation" value="REVIVE_REFUSE">
<span class="vote-message">* 投票の取り消しはできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$url}</td>
<td><input type="submit" value="{$VOTE_MESS->revive_refuse}"></form></td>
</tr></table></div>
</body></html>

EOF;
}

//身代わり君 (霊界) の投票ページ出力
function OutputVoteDummyBoy(){
  global $VOTE_MESS;

  OutputVotePageHeader();
  $url = RQ::$get->back_url;
  echo <<<EOF
<span class="vote-message">* 投票の取り消しはできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$url}</td>
<td>
<input type="hidden" name="situation" value="RESET_TIME">
<input type="submit" value="{$VOTE_MESS->reset_time}"></form>
</td>

EOF;

  //蘇生辞退ボタン表示判定
  if (! DB::$SELF->IsDrop() && DB::$ROOM->IsOption('not_open_cast') && ! DB::$ROOM->IsOpenCast()) {
    $url = RQ::$get->post_url;
    echo <<<EOF
<td>
<form method="POST" action="{$url}">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="REVIVE_REFUSE">
<input type="submit" value="{$VOTE_MESS->revive_refuse}">
</form>
</td>

EOF;
  }

  echo <<<EOF
</tr></table></div>
</body></html>

EOF;
}

//投票済みチェック
function CheckAlreadyVote($action, $not_action = ''){
  if (CheckSelfVoteNight($action, $not_action)) OutputVoteResult('夜：投票済み');
}
