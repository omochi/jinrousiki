<?php
//-- GameView 出力クラス --//
class GameView {
  //出力
  static function Output(){
    self::Load();
    HTML::OutputHeader(ServerConfig::$title . '[観戦]', 'game_view');
    if (GameConfig::$auto_reload && RQ::$get->auto_reload > 0) { //自動更新
      printf('<meta http-equiv="Refresh" content="%d">'."\n", RQ::$get->auto_reload);
    }
    echo DB::$ROOM->GenerateCSS(); //シーンに合わせた文字色と背景色 CSS をロード

    //ヘッダ
    $header = <<<EOF
</head>
%s
<table id="game_top" class="login"><tr>
%s<td class="login-link">%s
EOF;
    $body = DB::$ROOM->IsPlaying() && DB::$ROOM->IsRealTime() ?
      '<body onLoad="output_realtime();">' : '<body>';
    printf($header, $body, DB::$ROOM->GenerateTitleTag(), "\n");

    //更新
    $url = sprintf('<a href="game_view.php?room_no=%d', RQ::$get->room_no);
    $auto_reload = RQ::$get->auto_reload > 0 ? '&auto_reload=' . RQ::$get->auto_reload : '';
    printf('%s%s">[更新]</a>'."\n", $url, $auto_reload);
    if (GameConfig::$auto_reload) OutputAutoReloadLink($url); //自動更新設定

    printf('%s" target="_blank">別ページ</a>'."\n".'<a href="./">[戻る]</a>', $url); //別ページ
    if (DB::$ROOM->IsFinished()) OutputLogLink(); //ログ

    //ログインフォーム
    $login_form = <<<EOF
</td></tr></table>
<table class="login"><tr>
<td><form method="POST" action="login.php?room_no=%d">
<label for="uname">ユーザ名</label><input type="text" id="uname" name="uname" size="20" value="">
<label for="login_password">パスワード</label><input type="password" class="login-password" id="login_password" name="password" size="20" value="">
<input type="hidden" name="login_manually" value="on">
<input type="submit" value="ログイン">
</form></td>%s
EOF;
    printf($login_form, DB::$ROOM->id, "\n");

    if (DB::$ROOM->IsBeforeGame()) { //登録画面リンク
      $user_entry = <<<EOF
<td class="login-link">
<a href="user_manager.php?room_no=%d"><span>[住民登録]</span></a>
</td>%s
EOF;
      printf($user_entry, DB::$ROOM->id, "\n");
    }
    echo '</tr></table>'."\n";
    if (! DB::$ROOM->IsFinished()) OutputGameOption(); //ゲームオプション
    OutputTimeTable(); //経過日数と生存人数

    switch (DB::$ROOM->scene) {
    case 'day': //昼
      $time_message = '日没まで ';
      break;

    case 'night': //夜
      $time_message = '夜明けまで ';
      break;
    }

    if (DB::$ROOM->IsPlaying()) {
      if (DB::$ROOM->IsRealTime()) { //リアルタイム制
	GameTime::OutputTimer(GameTime::GetRealPass($left_time));
	echo '<td class="real-time"><form name="realtime_form">'."\n";
	echo '<input type="text" name="output_realtime" size="60" readonly>'."\n";
	echo '</form></td>'."\n";
      }
      else { //会話で時間経過制
	$left_talk_time = GameTime::GetTalkPass($left_time);
	if ($left_talk_time) printf('<td>%s%s</td>'."\n", $time_message, $left_talk_time);
      }
    }
    echo '</tr></table>'."\n";

    if (DB::$ROOM->IsPlaying()) {
      $format = '<div class="system-vote">%s</div>'."\n";
      if ($left_time == 0) {
	printf($format, $time_message . Message::$vote_announce);
      }
      elseif (DB::$ROOM->IsEvent('wait_morning')) {
	printf($format, Message::$wait_morning);
      }
    }

    OutputPlayerList();
    if (DB::$ROOM->IsFinished()) OutputWinner();
    if (DB::$ROOM->IsPlaying())  OutputRevoteList();
    OutputTalkLog();
    OutputLastWords();
    OutputDeadMan();
    OutputVoteList();
    HTML::OutputFooter();
  }

  //データ収集
  private function Load(){
    global $INIT_CONF;

    DB::Connect();
    DB::$ROOM = new Room(RQ::$get); //村情報を取得
    DB::$ROOM->view_mode   = true;
    DB::$ROOM->system_time = Time::Get();

    //シーンに応じた追加クラスをロード
    if (DB::$ROOM->IsFinished()) {
      $INIT_CONF->LoadFile('winner_message');
    }
    else {
      $INIT_CONF->LoadFile('cast_config', 'image_class', 'room_option_class');
    }

    //ユーザ情報を取得
    if (DB::$ROOM->IsBeforeGame()) RQ::$get->retrive_type = DB::$ROOM->scene;
    DB::$USER = new UserDataSet(RQ::$get);
    DB::$SELF = new User();
  }
}