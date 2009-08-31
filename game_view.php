<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//引数を取得
$RQ_ARGS = new RequestGameView();
$room_no = $RQ_ARGS->room_no;
$url = 'game_view.php?room_no=' . $room_no;

$dbHandle = ConnectDatabase(); // DB 接続

$ROOM = new RoomDataSet($RQ_ARGS); //村情報をロード
$ROOM->view_mode = true;
$ROOM->system_time = TZTime(); //現在時刻を取得
switch($ROOM->day_night){
case 'day': //昼
  $time_message = '　日没まで ';
  break;

case 'night': //夜
  $time_message = '　夜明けまで ';
  break;
}
$USERS = new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF  = new User();

OutputHTMLHeader('汝は人狼なりや？[観戦]', 'game_view'); //HTMLヘッダ

if($GAME_CONF->auto_reload && $RQ_ARGS->auto_reload != 0){ //自動更新
  echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
}

//シーンに合わせた文字色と背景色 CSS をロード
echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";

//経過時間を取得
if($ROOM->is_real_time()){ //リアルタイム制
  list($start_time, $end_time) = GetRealPassTime(&$left_time, true);
  if($ROOM->is_playing()){
    $on_load = ' onLoad="output_realtime();"';
    OutputRealTimer($start_time, $end_time);
  }
}
else{ //会話で時間経過制
  $left_talk_time = GetTalkPassTime(&$left_time);
}

echo <<<EOF
</head>
<body{$on_load}>
<a name="#game_top"></a>
<table class="login"><tr>
<td classs="room"><span>{$ROOM->name}村</span>　〜{$ROOM->comment}〜[{$room_no}番地]</td>
<td class="login-link">

EOF;

if($GAME_CONF->auto_reload){ //自動更新設定が有効ならリンクを表示
  echo '<a href="' . $url . '&auto_reload=' . $RQ_ARGS->auto_reload . '">[更新]</a>'."\n";
  OutputAutoReloadLink('<a href="' . $url . '&auto_reload=');
}
else{
  echo '<a href="' . $url . '">[更新]</a>'."\n";
}

echo <<<EOF
<a href="index.php">[戻る]</a>
</td></tr>
<tr><td><form method="POST" action="login.php?room_no=$room_no">
<label>ユーザ名</label><input type="text" name="uname" size="20">
<label>パスワード</label><input type="password" class="login-password" name="password" size="20">
<input type="hidden" name="login_type" value="manually">
<input type="submit" value="ログイン">
</form></td>

EOF;

if($ROOM->is_beforegame()){ //ゲーム開始前なら登録画面のリンクを表示
  echo '<td class="login-link">';
  echo '<a href="user_manager.php?room_no=' . $room_no . '"><span>[住民登録]</span></a>';
  echo '</td>'."\n";
}
echo '</tr></table>'."\n";

if(! $ROOM->is_finished()) OutputGameOption(); //ゲームオプションを表示

echo '<table class="time-table"><tr>'."\n";
OutputTimeTable(); //経過日数と生存人数

if($ROOM->is_playing()){
  if($ROOM->is_real_time()){ //リアルタイム制
    echo '<td class="real-time"><form name="realtime_form">'."\n";
    echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
    echo '</form></td>'."\n";
  }
  elseif($left_time){ //会話で時間経過制
    echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
  }

  if($left_time == 0){
    echo '</tr><tr>'."\n" . '<td class="system-vote" colspan="2">' . $time_message .
      $MESSAGE->vote_announce . '</td>'."\n";
  }
}
echo '</tr></table>'."\n";

OutputPlayerList(); //プレイヤーリスト
if($ROOM->is_finished()) OutputVictory(); //勝敗結果
OutputRevoteList(); //再投票メッセージ
OutputTalkLog();    //会話ログ
OutputLastWords();  //遺言
OutputDeadMan();    //死亡者
OutputVoteList();   //投票結果
OutputHTMLFooter(); //HTMLフッタ

DisconnectDatabase($dbHandle); //DB 接続解除
?>
