<?php
//-- 観戦画面出力クラス --//
class GameView {
  const AUTO_RELOAD = "<meta http-equiv=\"Refresh\" content=\"%d\">\n";
  const BODY = '<body onLoad="output_realtime();">';
  const URL_HEADER  = '<a href="game_view.php?room_no=%d';

  static function Output() {
    //-- データ収集 --//
    DB::Connect();
    DB::$ROOM = new Room(RQ::Get()); //村情報を取得
    DB::$ROOM->view_mode   = true;
    DB::$ROOM->system_time = Time::Get();

    //シーンに応じた追加クラスをロード
    if (DB::$ROOM->IsFinished()) {
      Loader::LoadFile('winner_message');
    } else {
      Loader::LoadFile('cast_config', 'image_class', 'room_option_class');
    }

    //ユーザ情報を取得
    if (DB::$ROOM->IsBeforeGame()) RQ::Set('retrive_type', DB::$ROOM->scene);
    DB::$USER = new UserData(RQ::Get());
    DB::$SELF = new User();

    //-- 出力 --//
    HTML::OutputHeader(ServerConfig::TITLE . '[観戦]', 'game_view');
    if (GameConfig::AUTO_RELOAD && RQ::Get()->auto_reload > 0) { //自動更新
      printf(self::AUTO_RELOAD, RQ::Get()->auto_reload);
    }
    echo DB::$ROOM->GenerateCSS(); //シーンに合わせた文字色と背景色 CSS をロード

    //ヘッダ
    $header = <<<EOF
</head>
%s
<table id="game_top" class="login"><tr>
%s<td class="login-link">

EOF;
    $body = DB::$ROOM->IsPlaying() && DB::$ROOM->IsRealTime() ? self::BODY : '<body>';
    printf($header, $body, DB::$ROOM->GenerateTitleTag());

    //更新
    $url = sprintf(self::URL_HEADER, RQ::Get()->room_no);
    $auto_reload = RQ::Get()->auto_reload > 0 ? '&auto_reload=' . RQ::Get()->auto_reload : '';
    printf('%s%s">[更新]</a>'."\n", $url, $auto_reload);
    if (GameConfig::AUTO_RELOAD) GameHTML::OutputAutoReloadLink($url); //自動更新設定

    printf('%s" target="_blank">別ページ</a>'."\n".'<a href="./">[戻る]</a>', $url); //別ページ
    if (DB::$ROOM->IsFinished()) GameHTML::OutputLogLink(); //ログ

    //ログインフォーム
    $login_form = <<<EOF
</td></tr></table>
<table class="login"><tr>
<td><form method="POST" action="login.php?room_no=%d">
<label for="uname">ユーザ名</label><input type="text" id="uname" name="uname" size="20" value="">%s
<label for="login_password">パスワード</label><input type="password" class="login-password" id="login_password" name="password" size="20" value="">
<input type="hidden" name="login_manually" value="on">
<input type="submit" value="ログイン">
</form></td>

EOF;
    if (GameConfig::TRIP) { //トリップ対応
      $trip = <<<EOF

<label for="trip">＃</label><input type="text" id="trip" name="trip" size="15" maxlength="15" value="">
EOF;
    } else {
      $trip = '';
    }
    printf($login_form, DB::$ROOM->id, $trip);

    if (DB::$ROOM->IsBeforeGame()) { //登録画面リンク
      $user_entry = <<<EOF
<td class="login-link">
<a href="user_manager.php?room_no=%d"><span>[住民登録]</span></a>
</td>

EOF;
      printf($user_entry, DB::$ROOM->id);
    }
    Text::Output('</tr></table>');
    if (! DB::$ROOM->IsFinished()) RoomOption::Output(); //ゲームオプション
    GameHTML::OutputTimeTable(); //経過日数と生存人数

    switch (DB::$ROOM->scene) {
    case 'day':
      $time_message = '日没まで ';
      break;

    case 'night':
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
    Text::Output('</tr></table>');

    if (DB::$ROOM->IsPlaying()) {
      $format = '<div class="system-vote">%s</div>'."\n";
      if ($left_time == 0) {
	printf($format, $time_message . Message::$vote_announce);
      }
      elseif (DB::$ROOM->IsEvent('wait_morning')) {
	printf($format, Message::$wait_morning);
      }
    }

    GameHTML::OutputPlayer();
    if (DB::$ROOM->IsFinished()) Winner::Output();
    if (DB::$ROOM->IsPlaying())  GameHTML::OutputRevote();
    Talk::Output();
    GameHTML::OutputLastWords();
    GameHTML::OutputDead();
    GameHTML::OutputVote();
    HTML::OutputFooter();
  }
}
