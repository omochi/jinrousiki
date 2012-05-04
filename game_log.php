<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('session_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameLog');
DB::Connect();
Session::Certify(); //セッション認証

DB::$ROOM = new Room(RQ::$get); //村情報を取得
DB::$ROOM->log_mode = true;
DB::$ROOM->single_log_mode = true;

DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報を取得
DB::$SELF = DB::$USER->BySession(); //自分の情報をロード

if(! (DB::$SELF->IsDead() || DB::$ROOM->IsFinished())){ //死者かゲーム終了後だけ
  HTML::OutputResult('ログ閲覧認証エラー',
		     'ログ閲覧認証エラー：<a href="./" target="_top">トップページ</a>' .
		     'からログインしなおしてください');
}

switch (RQ::$get->scene) {
case 'aftergame':
case 'heaven':
  if(! DB::$ROOM->IsFinished()){ //霊界・ゲーム終了後はゲーム終了後のみ
    HTML::OutputResult('入力データエラー', '入力データエラー：まだゲームが終了していません');
  }
  break;

default:
  if (DB::$ROOM->date < RQ::$get->date ||
      (DB::$ROOM->date == RQ::$get->date &&
       (DB::$ROOM->IsDay() || DB::$ROOM->scene == RQ::$get->scene))) { //「未来」判定
    HTML::OutputResult('入力データエラー', '入力データエラー：無効な日時です');
  }

  DB::$ROOM->last_date = DB::$ROOM->date;
  DB::$ROOM->date      = RQ::$get->date;
  DB::$ROOM->scene     = RQ::$get->scene;
  DB::$USER->SetEvent(true);
  break;
}

//-- ログ出力 --//
OutputGamePageHeader(); //HTMLヘッダ

$str = '<h1>ログ閲覧 ';
switch (RQ::$get->scene) {
case 'beforegame':
  $str .= '(開始前)';
  break;

case 'day':
  $str .= DB::$ROOM->date . ' 日目 (昼)';
  break;

case 'night':
  $str .= DB::$ROOM->date . ' 日目 (夜)';
  break;

case 'aftergame':
  $str .= DB::$ROOM->date . ' 日目 (終了後)';
  break;

case 'heaven':
  $str .= '(霊界)';
  break;
}
echo $str . '</h1>'."\n";

if (RQ::$get->scene == 'heaven') {
  DB::$ROOM->heaven_mode = true; //念のためセット
  OutputHeavenTalkLog(); //霊界会話ログ
}
else {
  if (RQ::$get->user_no > 0 && DB::$SELF->IsDummyBoy() && DB::$SELF->handle_name == '身代わり君') {
    $INIT_CONF->LoadFile('game_play_functions');
    DB::$SELF = DB::$USER->ByID(RQ::$get->user_no);
    DB::$SELF->live = 'live';
    OutputAbility();
  }
  OutputTalkLog(); //会話ログ
  if (DB::$ROOM->IsPlaying()) { //プレイ中は投票結果・遺言・死者を表示
    OutputAbilityAction();
    OutputLastWords();
    OutputDeadMan();
  }
  elseif (DB::$ROOM->IsAfterGame()) {
    OutputLastWords(true); //遺言(昼終了時限定)
  }
  if (DB::$ROOM->IsNight()) OutputVoteList(); //投票結果
}
HTML::OutputFooter();
