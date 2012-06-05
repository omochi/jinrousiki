<?php
//-- プレイヤー処理クラス --//
class Play {
  //ゲーム停滞のチェック
  static function CheckSilence() {
    if (! DB::$ROOM->IsPlaying()) return true; //スキップ判定

    //経過時間を取得
    if (DB::$ROOM->IsRealTime()) { //リアルタイム制
      GameTime::GetRealPass($left_time);
      if ($left_time > 0) return true; //制限時間超過判定
    }
    else { //仮想時間制
      if (! DB::Transaction()) return false; //判定条件が全て DB なので即ロック

      //現在のシーンを再取得して切り替わっていたらスキップ
      $query = DB::$ROOM->GetQueryHeader('room', 'scene') . ' FOR UPDATE';
      if (DB::FetchResult($query) != DB::$ROOM->scene) return DB::Rollback();
      $silence_pass_time = GameTime::GetTalkPass($left_time, true);

      if ($left_time > 0) { //制限時間超過判定
	//最終発言時刻からの差分を取得
	$query = DB::$ROOM->GetQueryHeader('room', 'UNIX_TIMESTAMP() - last_update_time');
	if (DB::FetchResult($query) <= TimeConfig::SILENCE) return DB::Rollback(); //沈黙判定

	//沈黙メッセージを発行してリセット
	$str = '・・・・・・・・・・ ' . $silence_pass_time . ' ' . Message::$silence;
	DB::$ROOM->Talk($str, null, '', '', null, null, null, TimeConfig::SILENCE_PASS);
	DB::$ROOM->UpdateTime();
	return DB::Commit();
      }
    }

    //オープニングなら即座に夜に移行する
    if (DB::$ROOM->date == 1 && DB::$ROOM->IsOption('open_day') && DB::$ROOM->IsDay()) {
      if (DB::$ROOM->IsRealTime()) { //リアルタイム制はここでロック開始
	if (! DB::Transaction()) return false;

	//現在のシーンを再取得して切り替わっていたらスキップ
	$query = DB::$ROOM->GetQueryHeader('room', 'scene') . ' FOR UPDATE';
	if (DB::FetchResult($query) != DB::$ROOM->scene) return DB::Rollback();
      }
      DB::$ROOM->ChangeNight(); //夜に切り替え
      DB::$ROOM->UpdateTime(); //最終書き込み時刻を更新
      return DB::Commit(); //ロック解除
    }

    if (! DB::$ROOM->IsOvertimeAlert()) { //警告メッセージ出力判定
      if (DB::$ROOM->IsRealTime()) { //リアルタイム制はここでロック開始
	if (! DB::Transaction()) return false;

	//現在のシーンを再取得して切り替わっていたらスキップ
	$query = DB::$ROOM->GetQueryHeader('room', 'scene') . ' FOR UPDATE';
	if (DB::FetchResult($query) != DB::$ROOM->scene) return DB::Rollback();
      }

      //警告メッセージを出力 (最終出力判定は呼び出し先で行う)
      $str = 'あと' . Time::Convert(TimeConfig::SUDDEN_DEATH) . 'で' .
	Message::$sudden_death_announce;
      if (DB::$ROOM->OvertimeAlert($str)) { //出力したら突然死タイマーをリセットしてコミット
	DB::$ROOM->sudden_death = TimeConfig::SUDDEN_DEATH;
	return DB::Commit(); //ロック解除
      }
    }

    //最終発言時刻からの差分を取得
    /*  DB::$ROOM から推定値を計算する場合 (リアルタイム制限定 + 再投票などがあると大幅にずれる) */
    //DB::$ROOM->sudden_death = TimeConfig::SUDDEN_DEATH - (DB::$ROOM->system_time - $end_time);
    $query = DB::$ROOM->GetQueryHeader('room', 'UNIX_TIMESTAMP() - last_update_time');
    DB::$ROOM->sudden_death = TimeConfig::SUDDEN_DEATH - DB::FetchResult($query);

    //制限時間前ならスキップ (この段階でロックしているのは非リアルタイム制のみ)
    if (DB::$ROOM->sudden_death > 0) return DB::$ROOM->IsRealTime() ? true : DB::Rollback();

    //制限時間を過ぎていたら未投票の人を突然死させる
    if (DB::$ROOM->IsRealTime()) { //リアルタイム制はここでロック開始
      if (! DB::Transaction()) return false;

      //現在のシーンを再取得して切り替わっていたらスキップ
      $query = DB::$ROOM->GetQueryHeader('room', 'scene') . ' FOR UPDATE';
      if (DB::FetchResult($query) != DB::$ROOM->scene) return DB::Rollback();

      //制限時間を再計算
      $query = DB::$ROOM->GetQueryHeader('room', 'UNIX_TIMESTAMP() - last_update_time');
      DB::$ROOM->sudden_death = TimeConfig::SUDDEN_DEATH - DB::FetchResult($query);
      if (DB::$ROOM->sudden_death > 0) return DB::Rollback();
    }

    if (abs(DB::$ROOM->sudden_death) > TimeConfig::SERVER_DISCONNECT) { //サーバダウン検出
      DB::$ROOM->UpdateTime(); //突然死タイマーをリセット
      DB::$ROOM->UpdateOvertimeAlert(); //警告出力判定をリセット
      return DB::Commit(); //ロック解除
    }

    $novote_list = array(); //未投票者リスト
    DB::$ROOM->LoadVote(); //投票情報を取得
    if (DB::$ROOM->IsDay()) {
      foreach (DB::$USER->rows as $user) { //生存中の未投票者を取得
	if ($user->IsLive() && ! isset(DB::$ROOM->vote[$user->user_no])) {
	  $novote_list[] = $user->user_no;
	}
      }
    }
    elseif (DB::$ROOM->IsNight()) {
      $vote_data = DB::$ROOM->ParseVote(); //投票情報をパース
      //PrintData($vote_data, 'Vote Data');
      foreach (DB::$USER->rows as $user) { //未投票チェック
	if ($user->CheckVote($vote_data) === false) $novote_list[] = $user->user_no;
      }
    }

    //未投票突然死処理
    foreach ($novote_list as $id) DB::$USER->SuddenDeath($id, 'NOVOTED');
    LoversFollowed(true);
    InsertMediumMessage();

    DB::$ROOM->Talk(Message::$vote_reset); //投票リセットメッセージ
    DB::$ROOM->UpdateVoteCount(true); //投票回数を更新
    DB::$ROOM->UpdateTime(); //制限時間リセット
    //DB::$ROOM->DeleteVote(); //投票リセット
    if (Winner::Check()) DB::$USER->ResetJoker(); //勝敗チェック
    return DB::Commit(); //ロック解除
  }

  //超過時間セット
  static function SetSuddenDeath() {
    //最終発言時刻からの差分を取得
    $query = DB::$ROOM->GetQueryHeader('room', 'UNIX_TIMESTAMP() - last_update_time');
    $last_update_time = DB::FetchResult($query);

    //経過時間を取得
    DB::$ROOM->IsRealTime() ?
      GameTime::GetRealPass($left_time) :
      GameTime::GetTalkPass($left_time, true);
    if ($left_time == 0) DB::$ROOM->sudden_death = TimeConfig::SUDDEN_DEATH - $last_update_time;
  }

  //置換処理
  static function ConvertSay(&$say) {
    global $ROLES;

    if ($say == '') return null; //リロード時なら処理スキップ
    //文字数・行数チェック
    if (strlen($say) > GameConfig::LIMIT_SAY ||
	substr_count($say, "\n") >= GameConfig::LIMIT_SAY_LINE) {
      $say = '';
      return false;
    }
    //発言置換モード
    if (GameConfig::REPLACE_TALK) $say = strtr($say, GameConfig::$replace_talk_list);

    //死者・ゲームプレイ中以外なら以降はスキップ
    if (DB::$SELF->IsDead() || ! DB::$ROOM->IsPlaying()) return null;
    //if (DB::$SELF->IsDead()) return false; //テスト用

    $ROLES->stack->say = $say;
    $ROLES->actor = ($virtual = DB::$USER->ByVirtual(DB::$SELF->user_no)); //仮想ユーザを取得
    do { //発言置換処理
      foreach ($ROLES->Load('say_convert_virtual') as $filter) {
	if ($filter->ConvertSay()) break 2;
      }
      $ROLES->actor = DB::$SELF;
      foreach ($ROLES->Load('say_convert') as $filter) {
	if ($filter->ConvertSay()) break 2;
      }
    } while (false);

    foreach ($virtual->GetPartner('bad_status', true) as $id => $date) { //妖精の処理
      if ($date != DB::$ROOM->date) continue;
      $ROLES->actor = DB::$USER->ByID($id);
      foreach ($ROLES->Load('say_bad_status') as $filter) $filter->ConvertSay();
    }

    $ROLES->actor = $virtual;
    foreach ($ROLES->Load('say') as $filter) $filter->ConvertSay(); //他のサブ役職の処理
    $say = $ROLES->stack->say;
    unset($ROLES->stack->say);
    return true;
  }

  //発言
  static function EntrySay($say) {
    global $ROLES;

    if (! DB::$ROOM->IsPlaying()) { //ゲーム開始前後
      return self::Talk($say, DB::$ROOM->scene, null, 0, true);
    }
    if (RQ::$get->last_words && DB::$SELF->IsDummyBoy()) { //身代わり君のシステムメッセージ (遺言)
      return self::Talk($say, DB::$ROOM->scene, 'dummy_boy');
    }
    if (DB::$SELF->IsDead()) return self::Talk($say, 'heaven'); //死者の霊話

    if (DB::$ROOM->IsRealTime()) { //リアルタイム制
      GameTime::GetRealPass($left_time);
      $spend_time = 0; //会話で時間経過制の方は無効にする
    }
    else { //会話で時間経過制
      GameTime::GetTalkPass($left_time); //経過時間の和
      $spend_time = min(4, max(1, floor(strlen($say) / 100))); //経過時間 (範囲は 1 - 4)
    }
    if ($left_time < 1) return; //制限時間外ならスキップ (ここに来るのは生存者のみのはず)

    if (DB::$ROOM->IsDay()) { //昼はそのまま発言
      if (DB::$ROOM->IsEvent('wait_morning')) return; //待機時間中ならスキップ
      if (DB::$SELF->IsRole('echo_brownie')) $ROLES->LoadMain(DB::$SELF)->EchoSay(); //山彦の処理
      return self::Talk($say, DB::$ROOM->scene, null, $spend_time, true);
    }

    //if (DB::$ROOM->IsNight()) { //夜は役職毎に分ける
    $user = DB::$USER->ByVirtual(DB::$SELF->user_no); //仮想ユーザを取得
    if (DB::$ROOM->IsEvent('blind_talk_night')) { //天候：風雨
      $location = 'self_talk';
    }
    elseif ($user->IsWolf(true)) { //人狼
      $location = DB::$SELF->IsRole('possessed_mad') ? 'self_talk' : 'wolf'; //犬神判定
    }
    elseif ($user->IsRole('whisper_mad')) { //囁き狂人
      $location = DB::$SELF->IsRole('possessed_mad') ? 'self_talk' : 'mad'; //犬神判定
    }
    elseif ($user->IsCommon(true)) { //共有者
      $location = 'common';
    }
    elseif ($user->IsFox(true)) { //妖狐
      $location = 'fox';
    }
    else { //独り言
      $location = 'self_talk';
    }

    $update = DB::$SELF->IsWolf(); //時間経過するのは人狼の発言のみ (本人判定)
    return self::Talk($say, DB::$ROOM->scene, $location, $update ? $spend_time : 0, $update);
  }

  //発言を DB に登録する
  static function Talk($say, $scene, $location = null, $spend_time = 0, $update = false) {
    global $ROLES;

    //声の大きさを決定
    $voice = RQ::$get->font_type;
    if (DB::$ROOM->IsPlaying() && DB::$SELF->IsLive()) {
      $ROLES->actor = DB::$USER->ByVirtual(DB::$SELF->user_no);
      foreach ($ROLES->Load('voice') as $filter) $filter->FilterVoice($voice, $say);
    }

    $uname = DB::$SELF->uname;
    if (DB::$ROOM->IsBeforeGame()) {
      DB::$ROOM->TalkBeforeGame($say, $uname, DB::$SELF->handle_name, DB::$SELF->color, $voice);
    }
    else {
      $role_id = DB::$ROOM->IsPlaying() ? DB::$SELF->role_id : null;
      DB::$ROOM->Talk($say, null, $uname, $scene, $location, $voice, $role_id, $spend_time);
    }
    if ($update) DB::$ROOM->UpdateTime();
  }

  //遺言登録
  static function EntryLastWords($say) {
    //スキップ判定
    if ((GameConfig::LIMIT_LAST_WORDS && DB::$ROOM->IsPlaying()) || DB::$ROOM->IsFinished()) {
      return false;
    }

    if ($say == ' ') $say = null; //スペースだけなら「消去」
    if (DB::$SELF->IsLive()) { //登録しない役職をチェック
      if (! DB::$SELF->IsLastWordsLimited()) DB::$SELF->Update('last_words', $say);
    }
    elseif (DB::$SELF->IsDead() && DB::$SELF->IsRole('mind_evoke')) { //口寄せの処理
      //口寄せしているイタコすべての遺言を更新する
      foreach (DB::$SELF->GetPartner('mind_evoke') as $id) {
	$target = DB::$USER->ByID($id);
	if ($target->IsLive()) $target->Update('last_words', $say);
      }
    }
  }
}

//-- HTML 生成クラス (Play 拡張) --//
class PlayHTML {
  //ヘッダ出力
  static function OutputHeader() {
    global $COOKIE, $OBJECTION;

    $url_frame  = '<a target="_top" href="game_frame.php';
    $url_room   = '?room_no=' . DB::$ROOM->id;
    $url_reload = RQ::$get->auto_reload > 0 ? '&auto_reload=' . RQ::$get->auto_reload : '';
    $url_sound  = RQ::$get->play_sound      ? '&play_sound=on'  : '';
    $url_list   = RQ::$get->list_down       ? '&list_down=on'   : '';
    $url_dead   = DB::$ROOM->dead_mode      ? '&dead_mode=on'   : '';
    $url_heaven = DB::$ROOM->heaven_mode    ? '&heaven_mode=on' : '';

    echo '<table class="game-header"><tr>'."\n";
    //ゲーム終了後・霊界
    if (DB::$ROOM->IsFinished() || (DB::$ROOM->heaven_mode && DB::$SELF->IsDead())) {
      echo DB::$ROOM->IsFinished() ? DB::$ROOM->GenerateTitleTag() :
	'<td>&lt;&lt;&lt;幽霊の間&gt;&gt;&gt;</td>'."\n";

      //過去シーンのログへのリンク生成
      echo '<td class="view-option">ログ ';
      $header = sprintf('<a href="game_log.php%s', $url_room);
      $footer = '" target="_blank">';
      $format = $header . '&date=%d&scene=%s' . $footer . '%d(%s)</a>'."\n";

      printf($format, 0, 'beforegame', 0, '前');
      if (DB::$ROOM->date > 1) {
	if (DB::$ROOM->IsOption('open_day')) printf($format, 1, 'day', 1, '昼');
	printf($format, 1, 'night', 1, '夜');
	for ($i = 2; $i < DB::$ROOM->date; $i++) {
	  printf($format, $i, 'day',   $i, '昼');
	  printf($format, $i, 'night', $i, '夜');
	}

	if (DB::$ROOM->heaven_mode) {
	  if (DB::$ROOM->IsNight()) printf($format, $i, 'day',  $i, '昼');
	  echo "</td>\n</tr></table>\n";
	  return;
	}
      }

      if (DB::$ROOM->IsFinished()) {
	if (DB::$ROOM->date > 0) {
	  printf($format, DB::$ROOM->date, 'day', DB::$ROOM->date, '昼');
	}
	if (DB::FetchResult(DB::$ROOM->GetQuery(true, 'talk') . " AND scene = 'night'") > 0) {
	  printf($format, DB::$ROOM->date, 'night', DB::$ROOM->date, '夜');
	}

	$format = $header . '&scene=%s' . $footer . '(%s)</a>'."\n";
	printf($format, 'aftergame', '後');
	printf($format, 'heaven',    '霊');
      }
    }
    else {
      echo DB::$ROOM->GenerateTitleTag() . '<td class="view-option">'."\n";
      if (DB::$SELF->IsDead() && DB::$ROOM->dead_mode) { //死亡者の場合の、真ん中の全表示地上モード
	$str = <<<EOF
<form method="POST" action="%s" name="reload_middle_frame" target="middle">
<input type="submit" value="更新">
</form>%s
EOF;
	$url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload . $url_sound . $url_list;
	printf($str, $url, "\n");
      }
    }

    if (! DB::$ROOM->IsFinished()) { //ゲーム終了後は自動更新しない
      $url_header = $url_frame . $url_room . $url_dead . $url_heaven . $url_list;
      GameHTML::OutputAutoReloadLink($url_header . $url_sound);

      $url = $url_header . $url_reload;
      printf("[音でお知らせ](%s)\n",
	     RQ::$get->play_sound ? sprintf('on %s">off</a>', $url) :
	     $url . '&play_sound=on">on</a> off');
    }

    //プレイヤーリストの表示位置
    echo $url_frame . $url_room . $url_dead . $url_heaven . $url_reload . $url_sound  .
      sprintf("%sリスト</a>\n", RQ::$get->list_down ? '">↑' : '&list_down=on">↓');

    //別ページリンク
    $url = '<a target="_blank" href="game_play.php%s%s">別ページ</a>%s';
    printf($url, $url_room, $url_list, "\n");

    if (DB::$ROOM->IsFinished()) {
      GameHTML::OutputLogLink();
    }
    elseif (DB::$ROOM->IsBeforegame()) {
      $url = '<a target="_blank" href="user_manager.php%s&user_no=%d">登録情報変更</a>'."\n";
      printf($url, $url_room, DB::$SELF->user_no);
    }

    //音でお知らせ処理
    if (RQ::$get->play_sound && (DB::$ROOM->IsBeforeGame() || DB::$ROOM->IsDay())) {
      if (DB::$ROOM->IsBeforeGame()) { //入村・満員
	$user_count = DB::$USER->GetUserCount();
	$max_user   = DB::FetchResult(DB::$ROOM->GetQueryHeader('room', 'max_user'));
	if ($user_count == $max_user && $COOKIE->user_count != $max_user) {
	  Sound::Output('full');
	}
	elseif ($COOKIE->user_count != $user_count) {
	  Sound::Output('entry');
	}
      }
      elseif ($COOKIE->scene != DB::$ROOM->scene) { //夜明け
	Sound::Output('morning');
      }

      //「異議」あり
      $cookie_objection_list = explode(',', $COOKIE->objection); //クッキーの値を配列に格納する
      $count = count($OBJECTION);
      for ($i = 0; $i < $count; $i++) { //差分を計算 (index は 0 から)
	//差分があれば性別を確認して音を鳴らす
	if ((int)$OBJECTION[$i] > (int)$cookie_objection_list[$i]) {
	  Sound::Output('objection_' . DB::$USER->ByID($i + 1)->sex);
	}
      }
    }
    echo "</td></tr>\n</table>\n";

    switch (DB::$ROOM->scene) {
    case 'beforegame': //開始前の注意を出力
      echo '<div class="caution">'."\n";
      echo 'ゲームを開始するには全員がゲーム開始に投票する必要があります';
      echo '<span>(投票した人は村人リストの背景が赤くなります)</span>'."\n";
      echo '</div>'."\n";
      RoomOption::Output(); //ゲームオプション表示
      break;

    case 'day':
      $time_message = '日没まで ';
      break;

    case 'night':
      $time_message = '夜明けまで ';
      break;

    case 'aftergame': //勝敗結果を出力して処理終了
      Winner::Output();
      return;
    }

    GameHTML::OutputTimeTable(); //経過日数と生存人数を出力
    $left_time = 0;
    if (DB::$ROOM->IsBeforeGame()) {
      echo '<td class="real-time">';
      if (DB::$ROOM->IsRealTime()) { //実時間の制限時間を取得
	$str = '設定時間： 昼 <span>%d分</span> / 夜 <span>%d分</span>';
	printf($str, DB::$ROOM->real_time->day, DB::$ROOM->real_time->night);
      }
      printf('　突然死：<span>%s</span></td>', Time::Convert(TimeConfig::SUDDEN_DEATH));
    }
    elseif (DB::$ROOM->IsPlaying()) {
      if (DB::$ROOM->IsRealTime()) { //リアルタイム制
	GameTime::GetRealPass($left_time);
	echo '<td class="real-time"><form name="realtime_form">'."\n";
	echo '<input type="text" name="output_realtime" size="60" readonly>'."\n";
	echo '</form></td>'."\n";
      }
      else { //仮想時間制
	printf("<td>%s%s</td>\n", $time_message, GameTime::GetTalkPass($left_time));
      }
    }

    //異議あり、のボタン(夜と死者モード以外)
    if (DB::$ROOM->IsBeforeGame() ||
	(DB::$ROOM->IsDay() && ! DB::$ROOM->dead_mode &&
	 ! DB::$ROOM->heaven_mode && $left_time > 0)) {
      $str = <<<EOF
<td class="objection"><form method="POST" action="%s">
<input type="hidden" name="set_objection" value="on">
<input type="image" name="objimage" src="%s">
(%d)</form></td>%s
EOF;
      $url   = 'game_play.php' . $url_room . $url_reload . $url_sound . $url_list;
      $image = GameConfig::OBJECTION_IMAGE;
      $count = GameConfig::OBJECTION - DB::$SELF->objection;
      printf($str, $url, $image, $count, "\n");
    }
    echo "</tr></table>\n";

    if (! DB::$ROOM->IsPlaying()) return;

    $str = '<div class="system-vote">%s</div>'."\n";
    if ($left_time == 0) {
      printf($str, $time_message . Message::$vote_announce);
      if (DB::$ROOM->sudden_death > 0) {
	echo Message::$sudden_death_time . Time::Convert(DB::$ROOM->sudden_death) . '<br>'."\n";
      }
    }
    elseif (DB::$ROOM->IsEvent('wait_morning')) {
      printf($str, Message::$wait_morning);
    }

    if (DB::$SELF->IsDead() && ! DB::$ROOM->IsOpenCast()) {
      printf($str, Message::$close_cast);
    }
  }

  //能力の種類とその説明を出力
  static function OutputAbility() {
    global $ROLES;

    if (! DB::$ROOM->IsPlaying()) return false; //ゲーム中のみ表示する

    if (DB::$SELF->IsDead()) { //死亡したら口寄せ以外は表示しない
      echo '<span class="ability ability-dead">' . Message::$ability_dead . '</span><br>';
      if (DB::$SELF->IsRole('mind_evoke')) Image::Role()->Output('mind_evoke');
      if (DB::$SELF->IsDummyBoy() && ! DB::$ROOM->IsOpenCast()) { //身代わり君のみ隠蔽情報を表示
	echo '<div class="system-vote">' . Message::$close_cast . '</div>'."\n";
      }
      return;
    }
    $ROLES->LoadMain(DB::$SELF)->OutputAbility(); //メイン役職

    //-- ここからサブ役職 --//
    foreach ($ROLES->Load('display_real') as $filter) $filter->OutputAbility();

    //-- ここからは憑依先の役職を表示 --//
    $ROLES->actor = DB::$USER->ByVirtual(DB::$SELF->user_no);
    foreach ($ROLES->Load('display_virtual') as $filter) $filter->OutputAbility();

    //-- これ以降はサブ役職非公開オプションの影響を受ける --//
    if (DB::$ROOM->IsOption('secret_sub_role')) return;

    $stack = array();
    foreach (array('real', 'virtual', 'none') as $name) {
      $stack = array_merge($stack, $ROLES->{'display_' . $name . '_list'});
    }
    //PrintData($stack);
    $display_list = array_diff(array_keys(RoleData::$sub_role_list), $stack);
    $target_list  = array_intersect($display_list, array_slice($ROLES->actor->role_list, 1));
    //PrintData($target_list);
    foreach ($target_list as $role) Image::Role()->Output($role);
  }

  //自分の遺言出力
  static function OutputLastWords() {
    if (DB::$ROOM->IsAfterGame()) return false; //ゲーム終了後は表示しない

    $query = 'SELECT last_words FROM user_entry' . DB::$ROOM->GetQuery(false) .
      " AND user_no = " . DB::$SELF->user_no;
    if (($str = DB::FetchResult($query)) == '') return false;
    Text::LineToBR($str); //改行コードを変換
    if ($str == '') return false;

    echo <<<EOF
<table class="lastwords"><tr>
<td class="lastwords-title">自分の遺言</td>
<td class="lastwords-body">{$str}</td>
</tr></table>

EOF;
  }
}
