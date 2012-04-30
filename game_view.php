<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('talk_class');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestBaseGame', true); //引数を取得
$url = '<a href="game_view.php?room_no=' . RQ::$get->room_no;

DB::Connect();
DB::$ROOM = new Room(RQ::$get); //村情報をロード
DB::$ROOM->view_mode   = true;
DB::$ROOM->system_time = Time::Get(); //現在時刻を取得
switch (DB::$ROOM->scene) {
case 'beforegame':
  RQ::$get->retrive_type = DB::$ROOM->scene;
  break;

case 'day': //昼
  $time_message = '日没まで ';
  break;

case 'night': //夜
  $time_message = '夜明けまで ';
  break;
}

//シーンに応じた追加クラスをロード
if (DB::$ROOM->IsFinished()) {
  $INIT_CONF->LoadClass('WINNER_MESS');
}
else {
  $INIT_CONF->LoadFile('room_config', 'cast_config');
  $INIT_CONF->LoadClass('ROOM_IMG', 'ROOM_OPT', 'GAME_OPT_MESS');
}

DB::$USER = new UserDataSet(RQ::$get); //ユーザ情報をロード
DB::$SELF = new User();

//-- データ出力 --//
ob_start();
HTML::OutputHeader(ServerConfig::$title . '[観戦]', 'game_view'); //HTMLヘッダ

if (GameConfig::$auto_reload && RQ::$get->auto_reload > 0) { //自動更新
  printf('<meta http-equiv="Refresh" content="%d">'."\n", RQ::$get->auto_reload);
}
echo DB::$ROOM->GenerateCSS(); //シーンに合わせた文字色と背景色 CSS をロード

$on_load = '';
if (DB::$ROOM->IsPlaying()) { //経過時間を取得
  if (DB::$ROOM->IsRealTime()) { //リアルタイム制
    $on_load  = ' onLoad="output_realtime();"';
    GameTime::OutputTimer(GameTime::GetRealPass($left_time));
  }
  else { //会話で時間経過制
    $INIT_CONF->LoadFile('time_config');
    $left_talk_time = GameTime::GetTalkPass($left_time);
  }
}

$title = DB::$ROOM->GenerateTitleTag();
echo <<<EOF
</head>
<body{$on_load}>
<table id="game_top" class="login"><tr>
{$title}<td class="login-link">

EOF;

if (GameConfig::$auto_reload) { //自動更新設定が有効ならリンクを表示
  echo $url . (RQ::$get->auto_reload > 0 ? '&auto_reload=' . RQ::$get->auto_reload : '') .
    '">[更新]</a>'."\n";
  OutputAutoReloadLink($url);
}
else {
  echo $url . '">[更新]</a>'."\n";
}

echo $url . '" target="_blank">別ページ</a>' . "\n" . '<a href="./">[戻る]</a>';
if (DB::$ROOM->IsFinished()) OutputLogLink();
$room_no = DB::$ROOM->id;
echo <<<EOF
</td></tr></table>
<table class="login"><tr>
<td><form method="POST" action="login.php?room_no={$room_no}">
<label for="uname">ユーザ名</label><input type="text" id="uname" name="uname" size="20" value="">
<label for="login_password">パスワード</label><input type="password" class="login-password" id="login_password" name="password" size="20" value="">
<input type="hidden" name="login_manually" value="on">
<input type="submit" value="ログイン">
</form></td>

EOF;

if (DB::$ROOM->IsBeforeGame()) { //ゲーム開始前なら登録画面のリンクを表示
  echo '<td class="login-link">';
  echo '<a href="user_manager.php?room_no=' . DB::$ROOM->id . '"><span>[住民登録]</span></a>';
  echo '</td>'."\n";
}
echo '</tr></table>'."\n";
if (! DB::$ROOM->IsFinished()) OutputGameOption(); //ゲームオプションを表示

OutputTimeTable(); //経過日数と生存人数
if (DB::$ROOM->IsPlaying()) {
  if (DB::$ROOM->IsRealTime()) { //リアルタイム制
    echo '<td class="real-time"><form name="realtime_form">'."\n";
    echo '<input type="text" name="output_realtime" size="60" readonly>'."\n";
    echo '</form></td>'."\n";
  }
  elseif ($left_talk_time) { //会話で時間経過制
    echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
  }
}
echo '</tr></table>'."\n";
if (DB::$ROOM->IsPlaying()) {
  if ($left_time == 0) {
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
  }
  elseif (DB::$ROOM->IsEvent('wait_morning')) {
    echo '<div class="system-vote">' . $MESSAGE->wait_morning . '</div>'."\n";
  }
}

OutputPlayerList(); //プレイヤーリスト
if (DB::$ROOM->IsFinished()) OutputWinner(); //勝敗結果
if (DB::$ROOM->IsPlaying())  OutputRevoteList(); //再投票メッセージ
OutputTalkLog();    //会話ログ
OutputLastWords();  //遺言
OutputDeadMan();    //死亡者
OutputVoteList();   //投票結果
HTML::OutputFooter();
ob_end_flush();
