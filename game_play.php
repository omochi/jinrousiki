<?php
require_once('include/init.php');
Loader::LoadFile('time_config', 'icon_class', 'talk_class', 'session_class',
		 'game_play_functions');

//-- データ収集 --//
Loader::LoadRequest('RequestGamePlay', true);
if (RQ::$get->play_sound) Loader::LoadFile('cookie_class'); //音でお知らせ

DB::Connect();
Session::CertifyGamePlay(); //セッション認証

DB::$ROOM = new Room(RQ::$get); //村情報をロード
DB::$ROOM->dead_mode    = RQ::$get->dead_mode;
DB::$ROOM->heaven_mode  = RQ::$get->heaven_mode;
DB::$ROOM->system_time  = Time::Get();
DB::$ROOM->sudden_death = 0; //突然死実行までの残り時間

//シーンに応じた追加クラスをロード
if (DB::$ROOM->IsBeforeGame()) { //ゲームオプション表示
  Loader::LoadFile('cast_config', 'image_class', 'room_option_class');
  RQ::$get->retrive_type = DB::$ROOM->scene;
}
elseif (! DB::$ROOM->heaven_mode && DB::$ROOM->IsDay()) {
  RQ::$get->retrive_type = DB::$ROOM->scene;
}
elseif (DB::$ROOM->IsFinished()) { //勝敗結果表示
  Loader::LoadFile('winner_message');
}

DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報をロード
DB::$SELF = DB::$USER->BySession(); //自分の情報をロード

//「異議」ありセット判定
if (RQ::$get->set_objection && DB::$SELF->objection < GameConfig::OBJECTION &&
    (DB::$ROOM->IsBeforeGame() || (DB::$SELF->IsLive() && DB::$ROOM->IsDay()))) {
  DB::$SELF->objection++;
  DB::$SELF->Update('objection', DB::$SELF->objection);
  DB::$ROOM->Talk('', 'OBJECTION', DB::$SELF->uname);
}

if (RQ::$get->play_sound) JinroCookie::Set(); //クッキー情報セット

//-- 発言処理 --//
$say_limit = null;
if (! DB::$ROOM->dead_mode || DB::$ROOM->heaven_mode) { //発言が送信されるのは bottom フレーム
  $say_limit = Play::ConvertSay(RQ::$get->say); //発言置換処理

  if (RQ::$get->say == '') {
    Play::CheckSilence(); //発言が空ならゲーム停滞のチェック (沈黙、突然死)
  }
  elseif (RQ::$get->last_words && (! DB::$SELF->IsDummyBoy() || DB::$ROOM->IsBeforeGame())) {
    Play::EntryLastWords(RQ::$get->say); //遺言登録 (細かい判定条件は関数内で行う)
  }
  //死者 or 身代わり君 or 同一ゲームシーンなら書き込む
  elseif (DB::$SELF->IsDead() || DB::$SELF->IsDummyBoy() ||
	  DB::$SELF->last_load_scene == DB::$ROOM->scene) {
    Play::EntrySay(RQ::$get->say);
  }
  else {
    Play::CheckSilence(); //発言ができない状態ならゲーム停滞チェック
  }

  //ゲームシーンを更新
  if (DB::$SELF->last_load_scene != DB::$ROOM->scene) {
    DB::$SELF->Update('last_load_scene', DB::$ROOM->scene);
  }
}
elseif (DB::$ROOM->dead_mode && DB::$ROOM->IsPlaying() && DB::$SELF->IsDummyBoy()) {
  Play::SetSuddenDeath(); //霊界の GM でも突然死タイマーを見れるようにする
}

//-- データ出力 --//
ob_start();
GameHTML::OutputHeader();
PlayHTML::OutputHeader();
if ($say_limit === false) echo '<font color="#FF0000">' . Message::$say_limit . '</font><br>';
if (! DB::$ROOM->heaven_mode) {
  if (! RQ::$get->list_down) GameHTML::OutputPlayer();
  PlayHTML::OutputAbility();
  if (DB::$ROOM->IsDay() && DB::$SELF->IsLive() && DB::$ROOM->date != 1) CheckSelfVoteDay();
  if (DB::$ROOM->IsPlaying()) GameHTML::OutputRevote();
}

(DB::$SELF->IsDead() && DB::$ROOM->heaven_mode) ? Talk::OutputHeaven() : Talk::Output();

if (! DB::$ROOM->heaven_mode) {
  if (DB::$SELF->IsDead()) GameHTML::OutputAbilityAction();
  GameHTML::OutputLastWords();
  GameHTML::OutputDead();
  GameHTML::OutputVote();
  if (! DB::$ROOM->dead_mode) PlayHTML::OutputLastWords();
  if (RQ::$get->list_down) GameHTML::OutputPlayer();
}
HTML::OutputFooter();
ob_end_flush();

//-- 関数 --//
//昼の自分の未投票チェック
function CheckSelfVoteDay() {
  $str = '<div class="self-vote">投票 ' . (DB::$ROOM->revote_count + 1) . ' 回目：';
  if (is_null(DB::$SELF->target_no)) {
    $str .= '<font color="#FF0000">まだ投票していません</font></div>'."\n" .
      '<span class="ability vote-do">' . Message::$ability_vote . '</span><br>';
  }
  else {
    $str .= DB::$USER->ByVirtual(DB::$SELF->target_no)->handle_name . ' さんに投票済み</div>';
  }
  echo $str."\n";
}
