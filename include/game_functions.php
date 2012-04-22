<?php
//-- 基礎関数 --//
//配列からランダムに一つ取り出す
function GetRandom($array){ return $array[array_rand($array)]; }

//-- 時間関連 --//
//リアルタイムの経過時間
function GetRealPassTime(&$left_time){
  global $TIME_CONF;

  $start_time = DB::$ROOM->scene_start_time; //シーンの最初の時刻を取得
  $base_time  = DB::$ROOM->real_time->{DB::$ROOM->scene} * 60; //設定された制限時間
  $pass_time  = DB::$ROOM->system_time - $start_time;
  if (DB::$ROOM->IsOption('wait_morning') && DB::$ROOM->IsDay()){ //早朝待機制
    $base_time += $TIME_CONF->wait_morning; //制限時間を追加する
    DB::$ROOM->event->wait_morning = $pass_time <= $TIME_CONF->wait_morning; //待機判定
  }
  if (($left_time = $base_time - $pass_time) < 0) $left_time = 0; //残り時間
  return $start_time + $base_time;
}

//会話で時間経過制の経過時間
function GetTalkPassTime(&$left_time, $silence = false){
  global $TIME_CONF;

  $query = 'SELECT SUM(spend_time) FROM talk' . DB::$ROOM->GetQuery() .
    " AND scene = '{DB::$ROOM->scene}'";
  $spend_time = (int)DB::FetchResult($query);

  if (DB::$ROOM->IsDay()) { //昼は12時間
    $base_time = $TIME_CONF->day;
    $full_time = 12;
  }
  else { //夜は6時間
    $base_time = $TIME_CONF->night;
    $full_time = 6;
  }
  if (($left_time = $base_time - $spend_time) < 0) $left_time = 0; //残り時間
  $base_left_time = $silence ? $TIME_CONF->silence_pass : $left_time; //仮想時間の計算
  return ConvertTime($full_time * $base_left_time * 60 * 60 / $base_time);
}

//-- 役職関連 --//
//巫女の判定結果 (システムメッセージ)
function InsertMediumMessage(){
  $flag  = false; //巫女の出現判定
  $stack = array();
  foreach (DB::$USER->rows as $user) {
    $flag |= $user->IsRoleGroup('medium');
    if ($user->suicide_flag){
      $virtual_user = DB::$USER->ByVirtual($user->user_no);
      $id = $virtual_user->user_no;
      $stack[$id] = array('target' => $virtual_user->handle_name, 'result' => $user->GetCamp());
    }
  }
  if (! $flag) return;
  ksort($stack);
  foreach ($stack as $list) {
    DB::$ROOM->ResultAbility('MEDIUM_RESULT', $list['result'], $list['target']);
  }
}

//恋人の後追い死処理
function LoversFollowed($sudden_death = false){
  global $MESSAGE;

  $cupid_list      = array(); //キューピッドのID => 恋人のID
  $lost_cupid_list = array(); //恋人が死亡したキューピッドのリスト
  $checked_list    = array(); //処理済キューピッドのID

  foreach (DB::$USER->rows as $user) { //キューピッドと死んだ恋人のリストを作成
    foreach ($user->GetPartner('lovers', true) as $id){
      $cupid_list[$id][] = $user->user_no;
      if ($user->dead_flag || $user->revive_flag) $lost_cupid_list[$id] = $id;
    }
  }

  while (count($lost_cupid_list) > 0) { //対象キューピッドがいれば処理
    $cupid_id = array_shift($lost_cupid_list);
    $checked_list[] = $cupid_id;
    foreach ($cupid_list[$cupid_id] as $lovers_id) { //キューピッドのリストから恋人の ID を取得
      $user = DB::$USER->ById($lovers_id); //恋人の情報を取得
      if (! DB::$USER->Kill($user->user_no, 'LOVERS_FOLLOWED')) continue;
      if ($sudden_death) DB::$ROOM->Talk($user->handle_name . $MESSAGE->lovers_followed); //突然死の処理
      $user->suicide_flag = true;

      foreach ($user->GetPartner('lovers') as $id){ //後追いした恋人のキューピッドのIDを取得
	if (! (in_array($id, $checked_list) || in_array($id, $lost_cupid_list))){ //連鎖判定
	  $lost_cupid_list[] = $id;
	}
      }
    }
  }
}

//勝敗をチェック
function CheckWinner($check_draw = false){
  global $GAME_CONF;

  if (DB::$ROOM->test_mode) return false;

  //コピー能力者がいるのでキャッシュを更新するかクエリから引くこと
  $query_count = DB::$ROOM->GetQuery(false, 'user_entry') . " AND live = 'live' AND user_no > 0 AND ";
  $human  = DB::FetchResult($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')"); //村人
  $wolf   = DB::FetchResult($query_count . "role LIKE '%wolf%'"); //人狼
  $fox    = DB::FetchResult($query_count . "role LIKE '%fox%'"); //妖狐
  $lovers = DB::FetchResult($query_count . "role LIKE '%lovers%'"); //恋人
  $quiz   = DB::FetchResult($query_count . "role LIKE 'quiz%'"); //出題者

  //-- 吸血鬼の勝利判定 --//
  $vampire = false;
  $living_id_list = array(); //生存者の ID リスト
  $infected_list  = array(); //吸血鬼 => 感染者リスト
  foreach (DB::$USER->GetLivingUsers(true) as $uname){
    $user = DB::$USER->ByUname($uname);
    $user->ReparseRoles();
    if (! $user->IsRole('psycho_infected')) $living_id_list[] = $user->user_no;
    if ($user->IsRole('infected')){
      foreach ($user->GetPartner('infected') as $id) $infected_list[$id][] = $user->user_no;
    }
  }
  if (count($living_id_list) == 1){
    $vampire = DB::$USER->ByID(array_shift($living_id_list))->IsRoleGroup('vampire');
  }
  else {
    foreach ($infected_list as $id => $stack){
      $diff_list = array_diff($living_id_list, $stack);
      if (count($diff_list) == 1 && in_array($id, $diff_list)){
	$vampire = true;
	break;
      }
    }
  }

  $winner = ''; //勝利陣営
  if ($human == $quiz && $wolf == 0 && $fox == 0) //全滅
    $winner = $quiz > 0 ? 'quiz' : 'vanish';
  elseif ($vampire) //吸血鬼支配
    $winner = $lovers > 1 ? 'lovers' : 'vampire';
  elseif ($wolf == 0) //狼全滅
    $winner = $lovers > 1 ? 'lovers' : ($fox > 0 ? 'fox1' : 'human');
  elseif ($wolf >= $human) //村全滅
    $winner = $lovers > 1 ? 'lovers' : ($fox > 0 ? 'fox2' : 'wolf');
  elseif ($human + $wolf + $fox == $lovers) //恋人支配
    $winner = 'lovers';
  elseif (DB::$ROOM->IsQuiz() && $quiz == 0) //クイズ村 GM 死亡
    $winner = 'quiz_dead';
  elseif ($check_draw && DB::$ROOM->revote_count >= $GAME_CONF->draw) //引き分け
    $winner = 'draw';

  if ($winner == '') return false;

  //ゲーム終了
  //OutputSiteSummary(); //RSS機能はテスト中
  $query = "UPDATE room SET status = 'finished', scene = 'aftergame', " .
    "scene_start_time = UNIX_TIMESTAMP(), winner = '{$winner}', finish_datetime = NOW() ".
    "WHERE room_no = {DB::$ROOM->id}";
  return DB::FetchBool($query);
}

//-- 投票関連 --//
//夜の自分の投票先取得
function GetSelfVoteNight($type, $not_type = ''){
  $query = DB::$ROOM->GetQueryHeader('vote', 'type', 'target_no') .
    sprintf(" AND date = %d AND vote_count = %d AND ", DB::$ROOM->date, DB::$ROOM->vote_count);
  if ($type == 'WOLF_EAT'){
    $query .= "type = '{$type}'";
  }
  elseif ($not_type != ''){
    $query .= sprintf("user_no = %d AND type IN ('%s', '%s')", DB::$SELF->user_no, $type, $not_type);
  }
  else {
    $query .= sprintf("user_no = %d AND type = '%s'", DB::$SELF->user_no, $type);
  }
  return DB::FetchAssoc($query, true);
}

//夜の自分の投票済みチェック
function CheckSelfVoteNight($situation, $not_situation = ''){
  return count(GetSelfVoteNight($situation, $not_situation)) > 0;
}

//-- 出力関連 --//
//HTMLヘッダー出力
function OutputGamePageHeader(){
  global $SERVER_CONF, $GAME_CONF, $TIME_CONF;

  //引数を格納
  $url_header = 'game_frame.php?room_no=' . DB::$ROOM->id;
  if (RQ::$get->auto_reload > 0) $url_header .= '&auto_reload=' . RQ::$get->auto_reload;
  if (RQ::$get->play_sound)      $url_header .= '&play_sound=on';
  if (RQ::$get->list_down)       $url_header .= '&list_down=on';

  $title = $SERVER_CONF->title . ' [プレイ]';
  $anchor_header = '<br>'."\n";
  /*
    Mac に JavaScript でエラーを吐くブラウザがあった当時のコード
    現在の Safari・Firefox では不要なので false でスキップしておく
    //if (preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){
  */
  if (false){
    $sentence = '';
    $anchor_header .= '<a href="';
    $anchor_footer = '" target="_top">ここをクリックしてください</a>';
  }
  else {
    $sentence = '<script type="text/javascript"><!--'."\n" .
      'if (top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $anchor_header .= '切り替わらないなら <a href="';
    $anchor_footer = '" target="_top">ここ</a>';
  }

  //ゲーム画面→天国モード (ゲーム中に死亡)
  if (DB::$ROOM->IsPlaying() && DB::$SELF->IsDead() &&
      ! (DB::$ROOM->log_mode || DB::$ROOM->dead_mode || DB::$ROOM->heaven_mode)) {
    $jump_url = $url_header . '&dead_mode=on';
    $sentence .= '天国モードに切り替えます。';
  }
  elseif (DB::$ROOM->IsAfterGame() && DB::$ROOM->dead_mode) { //天国モード→ゲーム終了画面
    $jump_url = $url_header;
    $sentence .= 'ゲーム終了後のお部屋に飛びます。';
  }
  elseif (DB::$SELF->IsLive() && (DB::$ROOM->dead_mode || DB::$ROOM->heaven_mode)) {
    $jump_url = $url_header;
    $sentence .= 'ゲーム画面に飛びます。';
  }
  else {
    $jump_url = '';
  }

  if ($jump_url != '') { //移動先が設定されていたら画面切り替え
    $sentence .= $anchor_header . $jump_url . $anchor_footer;
    OutputActionResult($title, $sentence, $jump_url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . DB::$ROOM->scene . '.css">'."\n";
  if (! DB::$ROOM->log_mode){ //過去ログ閲覧時は不要
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load = sprintf("change_css('%s');", DB::$ROOM->scene);
  }

  if (RQ::$get->auto_reload != 0 && ! DB::$ROOM->IsAfterGame()){ //自動リロードをセット
    printf('<meta http-equiv="Refresh" content="%s">'."\n", RQ::$get->auto_reload);
  }

  //ゲーム中、リアルタイム制なら経過時間を Javascript でリアルタイム表示
  $game_top = '<a id="game_top"></a>';
  if (DB::$ROOM->IsPlaying() && DB::$ROOM->IsRealTime() &&
      ! (DB::$ROOM->log_mode || DB::$ROOM->heaven_mode)) {
    $end_time   = GetRealPassTime($left_time);
    $sound_type = null;
    $alert_flag = false;
    $on_load .= 'output_realtime();';
    if ($left_time < 1 && DB::$SELF->IsLive()) { //超過判定
      DB::$ROOM->LoadVote(); //投票情報を取得
      if (DB::$ROOM->IsDay()) { //未投票判定
	$novote_flag = ! array_key_exists(DB::$SELF->user_no, DB::$ROOM->vote);
      }
      elseif (DB::$ROOM->IsNight()) {
	$novote_flag = DB::$SELF->CheckVote(DB::$ROOM->ParseVote()) === false;
      }

      if ($novote_flag) {
	$query = DB::$ROOM->GetQueryHeader('room', 'UNIX_TIMESTAMP() - last_update_time');
	if ($TIME_CONF->alert > $TIME_CONF->sudden_death - DB::FetchResult($query)) { //警告判定
	  $alert_flag = true;
	  $sound_type = 'alert';
	}
	else {
	  $sound_type = 'novote';
	}
      }
    }
    OutputRealTimer($end_time, $sound_type, $alert_flag);
    $game_top .= "\n".'<span id="vote_alert"></span>';
  }
  $body = isset($on_load) ? '<body onLoad="' . $on_load . '">' : '<body>';
  echo '</head>'."\n".$body."\n".$game_top."\n";
}

//リアルタイム表示に使う JavaScript の変数を出力
function OutputRealTimer($end_time, $type = null, $flag = false){
  global $TIME_CONF, $SOUND;

  $js_path     = JINRO_ROOT . '/javascript/';
  $sound_path  = is_null($type) || ! is_object($SOUND) ? '' : $SOUND->GenerateJS($type);
  $sentence    = '　' . (DB::$ROOM->IsDay() ? '日没' : '夜明け') . 'まで ';
  $start_date  = GenerateJavaScriptDate(DB::$ROOM->scene_start_time);
  $end_date    = GenerateJavaScriptDate($end_time);
  $server_date = GenerateJavaScriptDate(DB::$ROOM->system_time);
  echo '<script type="text/javascript" src="' . $js_path . 'output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var sentence = "' . $sentence . '";'."\n";
  echo "var end_date = {$end_date} * 1 + (new Date() - {$server_date});\n";
  echo "var diff_seconds = Math.floor(({$end_date} - {$start_date}) / 1000);\n";
  echo 'var sound_flag = ' . (is_null($type) ? 'false' : 'true') . ';'."\n";
  echo 'var countdown_flag = ' . ($flag ? 'true' : 'false') . ';'."\n";
  echo 'var sound_file = "' . $sound_path . '";'."\n";
  echo 'var alert_distance = "' . $TIME_CONF->alert_distance . '";'."\n";
  echo '// --></script>'."\n";
}

//JavaScript の Date() オブジェクト作成コードを生成する
function GenerateJavaScriptDate($time){
  $time_list = explode(',', TZDate('Y,m,j,G,i,s', $time));
  $time_list[1]--;  //JavaScript の Date() の Month は 0 からスタートする
  return 'new Date(' . implode(',', $time_list) . ')';
}

//自動更新のリンクを出力
function OutputAutoReloadLink($url){
  global $GAME_CONF;

  $str = sprintf('[自動更新](%s">%s</a>', $url, RQ::$get->auto_reload > 0 ? '手動' : '【手動】');
  foreach ($GAME_CONF->auto_reload_list as $time) {
    $name  = $time . '秒';
    $value = RQ::$get->auto_reload == $time ? sprintf('【%s】', $name) : $name;
    $str .= sprintf(' %s&auto_reload=%s">%s</a>', $url, $time, $value);
  }
  echo $str . ')'."\n";
}

//ログへのリンクを出力
function OutputLogLink(){
  $url = 'old_log.php?room_no=' . DB::$ROOM->id;
  echo GenerateLogLink($url, true, "<br>\n" . (DB::$ROOM->view_mode ? '[ログ]' : '[全体ログ]')) .
    GenerateLogLink($url . '&add_role=on', false, "<br>\n[役職表示ログ]");
}

//ゲームオプション画像を出力
function OutputGameOption(){
  $query = DB::$ROOM->GetQueryHeader('room', 'game_option', 'option_role', 'max_user');
  extract(DB::FetchAssoc($query, true));
  echo '<div class="game-option">ゲームオプション：' .
    RoomOption::Wrap($game_option, $option_role)->GenerateImageList() .
    GenerateMaxUserImage($max_user) . '</div>'."\n";
}

//日付と生存者の人数を出力
function OutputTimeTable(){
  echo '<table class="time-table"><tr>'."\n"; //ヘッダを表示

  if (DB::$ROOM->IsBeforeGame()) return false; //ゲームが始まっていなければスキップ
  $query = DB::$ROOM->GetQuery(false, 'user_entry') . " AND live = 'live'";
  echo '<td>' . DB::$ROOM->date . ' 日目<span>(生存者' . DB::FetchResult($query) . '人)</span></td>'."\n";
}

//プレイヤー一覧生成
function GeneratePlayerList(){
  global $SERVER_CONF, $ICON_CONF;

  //PrintData(DB::$ROOM->event);
  //キャッシュデータをセット
  $beforegame = DB::$ROOM->IsBeforeGame();
  $open_data  = DB::$ROOM->IsOpenData(true);
  $base_path  = $ICON_CONF->path . '/';
  $img_header = '<img'. $ICON_CONF->tag . ' alt="icon" title="';
  if ($open_data) {
    $trip_from = array('◆', '◇');
    $trip_to   = array('◆<br>', '◇<br>');
  }

  $count = 0; //改行カウントを初期化
  $str = '<div class="player"><table><tr>'."\n";
  foreach (DB::$USER->rows as $id => $user) {
    if ($count > 0 && ($count % 5) == 0) $str .= "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    //ゲーム開始投票をしていたら背景色を変える
    if ($beforegame && ($user->vote_type == 'GAMESTART' || $user->IsDummyBoy(true))) {
      $td_header = '<td class="already-vote">';
    }
    else {
      $td_header = '<td>';
    }

    //ユーザプロフィールと枠線の色を追加
    //Title 内の改行はブラウザ依存あり (Firefox 系は無効)
    $str .= $td_header . $img_header . str_replace("\n", '&#13;&#10', $user->profile) .
      '" style="border-color: ' . $user->color . ';"';

    //生死情報に応じたアイコンを設定
    $path = $base_path . $user->icon_filename;
    if ($beforegame || DB::$ROOM->watch_mode || DB::$USER->IsVirtualLive($id)) {
      $live = '(生存中)';
    }
    else {
      $live = '(死亡)';
      $str .= ' onMouseover="this.src=' . "'$path'" . '"'; //元のアイコン

      $path = $ICON_CONF->dead; //アイコンを死亡アイコンに入れ替え
      $str .= ' onMouseout="this.src=' . "'$path'" . '"';
    }

    if (DB::$ROOM->personal_mode) $live .= '<br>(' . GenerateWinner($user->user_no) . ')';
    $str .= ' src="' . $path . '"></td>'."\n";

    //HN を追加
    $str .= $td_header . '<font color="' . $user->color . '">◆</font>' . $user->handle_name;
    if ($SERVER_CONF->debug_mode) $str .= ' (' . $id . ')';

    if ($open_data) { //ゲーム終了後・死亡後＆霊界役職公開モードなら、役職・ユーザネームも表示
      $str .= '<br>　(' . str_replace($trip_from, $trip_to, $user->uname); //トリップ対応

      //憑依状態なら憑依しているユーザを追加
      $real_user = DB::$USER->ByReal($id);
      if ($real_user->IsSame($user->uname)) $real_user = DB::$USER->TraceExchange($id); //交換憑依判定
      if (! $real_user->IsSame($user->uname) && $real_user->IsLive()) {
	$str .= '<br>[' . $real_user->uname . ']';
      }
      $str .= ')<br>' . $user->GenerateRoleName(); //役職情報を追加
    }
    $str .= '<br>' . $live . '</td>'."\n";
  }
  return $str . '</tr></table></div>'."\n";
}

//プレイヤー一覧出力
function OutputPlayerList(){ echo GeneratePlayerList(); }

//勝敗結果の生成
function GenerateWinner($id = 0){
  global $WINNER_MESS, $ROLES;

  //-- 村の勝敗結果 --//
  $winner = DB::$ROOM->LoadWinner();
  $class  = $winner;
  $text   = $winner;

  switch ($winner){ //特殊ケース対応
  //妖狐勝利
  case 'fox1':
  case 'fox2':
    $winner = 'fox';
    $class  = $winner;
    break;

  //引き分け
  case 'draw': //引き分け
  case 'vanish': //全滅
  case 'quiz_dead': //クイズ村 GM 死亡
    $class = 'draw';
    break;

  //廃村
  case null:
    $class = 'none';
    $text  = DB::$ROOM->date > 0 ? 'unfinished' : 'none';
    break;
  }
  $str = <<<EOF
<table class="winner winner-{$class}"><tr>
<td>{$WINNER_MESS->$text}</td>
</tr></table>

EOF;

  //-- 個々の勝敗結果 --//
  //スキップ判定 (勝敗未決定/観戦モード/ログ閲覧モード)
  if (is_null($winner) || DB::$ROOM->view_mode ||
      (DB::$ROOM->log_mode && ! DB::$ROOM->single_view_mode && ! DB::$ROOM->personal_mode)) {
    return $id > 0 ? '不明' : $str;
  }

  $result = 'win';
  $class  = null;
  $user   = $id > 0 ? DB::$USER->ByID($id) : DB::$SELF;
  if ($user->user_no < 1) return $str;
  $camp   = $user->GetCamp(true); //所属陣営を取得

  switch ($winner){
  case 'draw':   //引き分け
  case 'vanish': //全滅
    $result = 'draw';
    $class  = $result;
    break;

  case 'quiz_dead': //出題者死亡
    $result = $camp == 'quiz' ? 'lose' : 'draw';
    $class  = $result;
    break;

  default:
    $ROLES->stack->class = null;
    switch ($camp){
    case 'human':
    case 'wolf':
    case 'fox':
      $win_flag = $winner == $camp && $ROLES->LoadMain($user)->Win($winner);
      break;

    case 'vampire':
      $win_flag = $winner == $camp && (DB::$SELF->IsRoleGroup('mania') || $user->IsLive());
      break;

    case 'chiroptera':
      $win_flag = $user->IsLive();
      break;

    case 'ogre':
    case 'duelist':
      $win_flag = $user->IsRoleGroup('mania') ? $user->IsLive()
	: $ROLES->LoadMain($user)->Win($winner);
      break;

    default:
      $win_flag = $winner == $camp;
      break;
    }

    if ($win_flag){ //ジョーカー系判定
      $ROLES->actor = $user;
      foreach ($ROLES->Load('joker') as $filter) $filter->FilterWin($win_flag);
    }

    if ($win_flag){
      $class = is_null($ROLES->stack->class) ? $camp : $ROLES->stack->class;
    }
    else {
      $result = 'lose';
      $class  = $result;
    }
    break;
  }
  if ($id > 0){
    switch ($result){
    case 'win':
      return '勝利';

    case 'lose':
      return '敗北';

    case 'draw':
      return '引分';

    case 'win':
      return '不明';
    }
  }
  $result = 'self_' . $result;

  return $str . <<<EOF
<table class="winner winner-{$class}"><tr>
<td>{$WINNER_MESS->$result}</td>
</tr></table>

EOF;
}

//勝敗結果の出力
function OutputWinner(){ echo GenerateWinner(); }

//投票の集計生成
function GenerateVoteResult(){
  global $MESSAGE;

  if (! DB::$ROOM->IsPlaying()) return null; //ゲーム中以外は出力しない
  if (DB::$ROOM->IsEvent('blind_vote') && ! DB::$ROOM->IsOpenData()) return null; //傘化けの判定

  //昼なら前日、夜ならの今日の集計を表示
  return GetVoteList((DB::$ROOM->IsDay() && ! DB::$ROOM->log_mode) ? DB::$ROOM->date - 1 : DB::$ROOM->date);
}

//投票の集計出力
function OutputVoteList(){
  if (is_null($str = GenerateVoteResult())) return false;
  echo $str;
}

//再投票の時、メッセージを表示
function OutputRevoteList(){
  global $GAME_CONF, $MESSAGE, $COOKIE, $SOUND;

  if (RQ::$get->play_sound && ! DB::$ROOM->view_mode && DB::$ROOM->vote_count > 1 &&
      DB::$ROOM->vote_count > $COOKIE->vote_times) {
    $SOUND->Output('revote'); //音を鳴らす (未投票突然死対応)
  }
  if (! DB::$ROOM->IsDay() || DB::$ROOM->revote_count < 1) return false; //投票結果表示は再投票のみ

  //投票済みチェック
  $query = DB::$ROOM->GetQuery(true, 'vote') .
    sprintf(' AND vote_count = %d AND user_no = %d', DB::$ROOM->vote_count, DB::$SELF->user_no);
  if (DB::FetchResult($query) == 0) {
    $str = '<div class="revote">%s (%d回%s)</div><br>'."\n";
    printf($str, $MESSAGE->revote, $GAME_CONF->draw, $MESSAGE->draw_announce);
  }

  echo GetVoteList(DB::$ROOM->date); //投票結果を出力
}

//指定した日付の投票結果をロードして GenerateVoteList() に渡す
function GetVoteList($date){
  if (DB::$ROOM->personal_mode) return null; //スキップ判定
  //指定された日付の投票結果を取得
  $query = DB::$ROOM->GetQueryHeader('result_vote_kill', 'count', 'handle_name', 'target_name',
				     'vote', 'poll') . " AND date = {$date} ORDER BY count, id";
  return GenerateVoteList(DB::FetchAssoc($query), $date);
}

//投票データから結果を生成する
function GenerateVoteList($raw_data, $date){
  if (count($raw_data) < 1) return null; //投票総数

  $open_vote   = DB::$ROOM->IsOpenData() || DB::$ROOM->IsOption('open_vote'); //投票数開示判定
  $header      = '<td class="vote-name">';
  $table_stack = array();
  foreach ($raw_data as $raw){ //個別投票データのパース
    extract($raw);
    $stack = array('<tr>' .  $header . $handle_name, '<td>' . $poll . ' 票',
		   '<td>投票先' . ($open_vote ? ' ' . $vote . ' 票' : '') . ' →',
		   $header . $target_name, '</tr>');
    $table_stack[$count][] = implode('</td>', $stack);
  }
  if (! RQ::$get->reverse_log) krsort($table_stack); //正順なら逆転させる

  $header = '<tr><td class="vote-times" colspan="4">' . $date . ' 日目 ( ';
  $footer = ' 回目)</td>';
  $str    = '';
  foreach ($table_stack as $count => $stack){
    array_unshift($stack, '<table class="vote-list">', $header . $count . $footer);
    $stack[] = '</table>'."\n";
    $str .= implode("\n", $stack);
  }
  return $str;
}

//会話ログ出力
function OutputTalkLog(){
  $builder = new DocumentBuilder();
  $builder->BeginTalk('talk');
  foreach (DB::$ROOM->LoadTalk() as $talk) OutputTalk($talk, $builder); //会話出力
  OutputTimeStamp($builder);
  $builder->EndTalk();
}

//会話出力
function OutputTalk($talk, &$builder){
  global $GAME_CONF, $MESSAGE, $ROLES;

  //PrintData($talk);
  //発言ユーザを取得
  /*
    $uname は必ず $talk から取得すること。
    DB::$USER にはシステムユーザー 'system' が存在しないため、$actor は常に null になっている。
  */
  $actor = DB::$USER->ByUname($talk->uname);
  $real  = $actor;
  if (DB::$ROOM->log_mode && isset($talk->role_id)) $actor->ChangePlayer($talk->role_id);
  switch ($talk->scene) {
  case 'day':
  case 'night':
    $virtual = DB::$USER->ByVirtual($actor->user_no);
    if ($actor->user_no != $virtual->user_no) $actor = $virtual;
    break;
  }

  //基本パラメータを取得
  if ($talk->uname == 'system') {
    $symbol = '';
    $name   = '';
    $actor->user_no = 0;
  }
  else {
    $color  = isset($talk->color) ? $talk->color : $actor->color;
    $symbol = sprintf('<font color="%s">◆</font>', $color);
    $name   = isset($talk->handle_name) ? $talk->handle_name : $actor->handle_name;
  }

  //実ユーザを取得
  if (RQ::$get->add_role && $actor->user_no > 0) { //役職表示モード対応
    $real_user = isset($real) ? $real : $actor;
    $name .= $real_user->GenerateShortRoleName($talk->scene == 'heaven');
  }
  else {
    $real_user = DB::$USER->ByRealUname($talk->uname);
  }

  switch ($talk->location) {
  case 'system': //システムメッセージ
    $str = $talk->sentence;
    if (isset($talk->time)) $str .= sprintf(' <span class="date-time">%s</span>', $talk->date_time);

    if (! isset($talk->action)) return $builder->AddSystemTalk($str); //標準
    switch ($talk->action) { //投票情報
    case 'GAMESTART_DO': //現在は不使用
      return true;

    case 'OBJECTION': //「異議」ありは常時表示
      return $builder->AddSystemMessage('objection-' . $actor->sex, $name . $str);

    case 'MORNING':
    case 'NIGHT':
      return $builder->AddSystemTalk($str);

    default: //ゲーム開始前の投票 (例：KICK) は常時表示
      return $builder->flag->open_talk || DB::$ROOM->IsBeforeGame() ?
	$builder->AddSystemMessage($talk->class, $name . $str) : false;
    }
    return false;

  case 'dummy_boy': //身代わり君専用システムメッセージ
    $str = sprintf('◆%s　%s', $real_user->handle_name, $talk->sentence);
    if ($GAME_CONF->quote_words) $str = sprintf('「%s」', $str);
    if (isset($talk->time)) $str .= sprintf(' <span class="date-time">%s</span>', $talk->date_time);
    return $builder->AddSystemTalk($str, 'dummy-boy');
  }

  switch ($talk->scene) {
  case 'day':
    //強風判定 (身代わり君と本人は対象外)
    if (DB::$ROOM->IsEvent('blind_talk_day') &&
	! $builder->flag->dummy_boy && ! $builder->actor->IsSame($talk->uname)) {
      //位置判定 (観戦者以外の上下左右)
      $viewer = $builder->actor->user_no;
      $target = $actor->user_no;
      if (is_null($viewer) ||
	  ! (abs($target - $viewer) == 5 ||
	     ($target == $viewer - 1 && ($target % 5) != 0) ||
	     ($target == $viewer + 1 && ($viewer % 5) != 0))) {
	$talk->sentence = $MESSAGE->common_talk;
      }
    }
    return $builder->AddTalk($actor, $talk, $real);

  case 'night':
    if ($builder->flag->open_talk) {
      $class = '';
      $voice = $talk->font_type;
      switch ($talk->location) {
      case 'common':
	$name .= '<span>(共有者)</span>';
	$class = 'night-common';
	$voice .= ' ' . $class;
	break;

      case 'wolf':
	$name .= '<span>(人狼)</span>';
	$class = 'night-wolf';
	$voice .= ' ' . $class;
	break;

      case 'mad':
	$name .= '<span>(囁き狂人)</span>';
	$class = 'night-wolf';
	$voice .= ' ' . $class;
	break;

      case 'fox':
	$name .= '<span>(妖狐)</span>';
	$class = 'night-fox';
	$voice .= ' ' . $class;
	break;

      case 'self_talk':
	$name .= '<span>の独り言</span>';
	$class = 'night-self-talk';
	break;
      }
      $str = $talk->sentence; //改行を入れるため再セット
      if (isset($talk->time)) $name .= sprintf('<br><span>%s</span>', $talk->date_time);
      return $builder->RawAddTalk($symbol, $name, $str, $voice, '', $class);
    }
    else {
      $mind_read = false; //特殊発言透過判定
      $ROLES->actor = $actor;
      foreach ($ROLES->Load('mind_read') as $filter) $mind_read |= $filter->IsMindRead();

      $ROLES->actor = $builder->actor;
      foreach ($ROLES->Load('mind_read_active') as $filter) {
	$mind_read |= $filter->IsMindReadActive($actor);
      }

      $ROLES->actor = $real_user;
      foreach ($ROLES->Load('mind_read_possessed') as $filter) {
	$mind_read |= $filter->IsMindReadPossessed($actor);
      }

      $ROLES->actor = $actor;
      switch ($talk->location) {
      case 'common': //共有者
	if ($builder->flag->common || $mind_read) return $builder->AddTalk($actor, $talk, $real);
	if ($ROLES->LoadMain($actor)->Whisper($builder, $talk->font_type)) return;
	foreach ($ROLES->Load('talk_whisper') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	return false;

      case 'wolf': //人狼
	if ($builder->flag->wolf || $mind_read) return $builder->AddTalk($actor, $talk, $real);
	if ($ROLES->LoadMain($actor)->Howl($builder, $talk->font_type)) return;
	foreach ($ROLES->Load('talk_whisper') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	return false;

      case 'mad': //囁き狂人
	if ($builder->flag->wolf || $mind_read) return $builder->AddTalk($actor, $talk, $real);
	foreach ($ROLES->Load('talk_whisper') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	return false;

      case 'fox': //妖狐
	if ($builder->flag->fox || $mind_read) return $builder->AddTalk($actor, $talk, $real);
	$ROLES->actor = DB::$SELF;
	foreach ($ROLES->Load('talk_fox') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	$ROLES->actor = $actor;
	foreach ($ROLES->Load('talk_whisper') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	return false;

      case 'self_talk': //独り言
	if ($builder->flag->dummy_boy || $mind_read || $builder->actor->IsSame($talk->uname)) {
	  return $builder->AddTalk($actor, $talk, $real);
	}
	foreach ($ROLES->Load('talk_self') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	$ROLES->actor = $builder->actor;
	foreach ($ROLES->Load('talk_ringing') as $filter) {
	  if ($filter->Whisper($builder, $talk->font_type)) return;
	}
	return false;
      }
    }
    return false;

  case 'heaven':
    if (! $builder->flag->open_talk) return false;
    if (isset($talk->time)) $name .= sprintf('<br><span>%s</span>', $talk->date_time);
    return $builder->RawAddTalk($symbol, $name, $talk->sentence, $talk->font_type, $talk->scene);

  default:
    return $builder->AddTalk($actor, $talk, $real);
  }
}

//天国の霊話ログ出力
function OutputHeavenTalkLog(){
  //出力条件をチェック
  //if (DB::$SELF->IsDead()) return false; //呼び出し側でチェックするので現在は不要

  $is_open = DB::$ROOM->IsOpenCast(); //霊界公開判定
  $builder = new DocumentBuilder();
  $builder->BeginTalk('talk');
  foreach (DB::$ROOM->LoadTalk(true) as $talk){
    $user = DB::$USER->ByUname($talk->uname); //ユーザを取得

    $symbol = '<font color="' . $user->color . '">◆</font>';
    $handle_name = $user->handle_name;
    if ($is_open) $handle_name .= '<span>(' . $talk->uname . ')</span>'; //HN 追加処理

    $builder->RawAddTalk($symbol, $handle_name, $talk->sentence, $talk->font_type);
  }
  $builder->EndTalk();
}

//[村立て / ゲーム開始 / ゲーム終了] 時刻を出力
function OutputTimeStamp($builder){
  $talk = new Talk();
  if (DB::$ROOM->IsBeforeGame()){ //村立て時刻を取得して表示
    $type = 'establish_datetime';
    $talk->sentence = '村作成';
  }
  elseif (DB::$ROOM->IsNight() && DB::$ROOM->date == 1){ //ゲーム開始時刻を取得して表示
    $type = 'start_datetime';
    $talk->sentence = 'ゲーム開始';
  }
  elseif (DB::$ROOM->IsAfterGame()){ //ゲーム終了時刻を取得して表示
    $type = 'finish_datetime';
    $talk->sentence = 'ゲーム終了';
  }
  else return false;

  if (is_null($time = DB::FetchResult(DB::$ROOM->GetQueryHeader('room', $type)))) return false;
  $talk->uname    = 'system';
  $talk->scene    = DB::$ROOM->scene;
  $talk->location = 'system';
  $talk->sentence .= '：' . ConvertTimeStamp($time);
  OutputTalk($talk, $builder);
}

//前日の能力発動結果を出力
function OutputAbilityAction(){
  global $MESSAGE;

  //昼間で役職公開が許可されているときのみ表示
  if (! DB::$ROOM->IsDay() || ! (DB::$SELF->IsDummyBoy() || DB::$ROOM->IsOpenCast())) return false;

  $header = '<b>前日の夜、';
  $footer = '</b><br>'."\n";
  if (DB::$ROOM->test_mode) {
    $stack_list = RQ::GetTest()->ability_action_list;
  }
  else {
    $yesterday = DB::$ROOM->date - 1;
    $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'MIND_SCANNER_DO',
			 'JAMMER_MAD_DO', 'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO',
			 'FAIRY_DO');
    if ($yesterday == 1){
      array_push($action_list, 'CUPID_DO', 'DUELIST_DO', 'MANIA_DO');
    }
    else {
      array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'WIZARD_DO',
		 'SPREAD_WIZARD_DO', 'ESCAPE_DO', 'DREAM_EAT', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO',
		 'POISON_CAT_DO', 'POISON_CAT_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
		 'POSSESSED_DO', 'POSSESSED_NOT_DO', 'VAMPIRE_DO', 'OGRE_DO', 'OGRE_NOT_DO',
		 'DEATH_NOTE_DO', 'DEATH_NOTE_NOT_DO');
    }
    $action = '';
    foreach ($action_list as $this_action){
      if ($action != '') $action .= ' OR ';
      $action .= "type = '$this_action'";
    }
    $query = DB::$ROOM->GetQueryHeader('system_message', 'message', 'type') .
      " AND date = {$yesterday} AND ({$action})";
    $stack_list = DB::FetchAssoc($query);
  }

  foreach ($stack_list as $stack){
    list($actor, $target) = explode("\t", $stack['message']);
    echo $header.DB::$USER->ByHandleName($actor)->GenerateShortRoleName(false, true).' ';
    switch ($stack['type']){
    case 'CUPID_DO': //DB 登録時にタブ区切りで登録していないので個別の名前は取得不可
    case 'FAIRY_DO':
    case 'DUELIST_DO':
    case 'SPREAD_WIZARD_DO':
      $target = 'は '.$target;
      break;

    case 'SPREAD_WIZARD_DO': //テストコード (現在は不使用)
      $str_stack = array();
      foreach (explode(' ', $target) as $id){
	$str_stack[] = DB::$USER->ByID($id)->GenerateShortRoleName(false, true);
      }
      $target = 'は '.implode(' ', $str_stack);
      break;

    default:
      $target = 'は '.DB::$USER->ByHandleName($target)->GenerateShortRoleName(false, true).' ';
      break;
    }

    switch ($stack['type']){
    case 'GUARD_DO':
    case 'REPORTER_DO':
    case 'ASSASSIN_DO':
    case 'WIZARD_DO':
    case 'ESCAPE_DO':
    case 'WOLF_EAT':
    case 'DREAM_EAT':
    case 'CUPID_DO':
    case 'VAMPIRE_DO':
    case 'FAIRY_DO':
    case 'OGRE_DO':
    case 'DUELIST_DO':
    case 'DEATH_NOTE_DO':
      echo $target.$MESSAGE->{strtolower($stack['type'])};
      break;

    case 'ASSASSIN_NOT_DO':
    case 'POSSESSED_NOT_DO':
    case 'OGRE_NOT_DO':
    case 'DEATH_NOTE_NOT_DO':
      echo $MESSAGE->{strtolower($stack['type'])};
      break;

    case 'POISON_CAT_DO':
      echo $target.$MESSAGE->revive_do;
      break;

    case 'POISON_CAT_NOT_DO':
      echo $MESSAGE->revive_not_do;
      break;

    case 'SPREAD_WIZARD_DO':
      echo $target.$MESSAGE->wizard_do;
      break;

    case 'TRAP_MAD_DO':
      echo $target.$MESSAGE->trap_do;
      break;

    case 'TRAP_MAD_NOT_DO':
      echo $MESSAGE->trap_not_do;
      break;

    case 'MAGE_DO':
    case 'CHILD_FOX_DO':
      echo $target.'を占いました';
      break;

    case 'VOODOO_KILLER_DO':
      echo $target.'の呪いを祓いました';
      break;

    case 'ANTI_VOODOO_DO':
      echo $target.'の厄を祓いました';
      break;

    case 'MIND_SCANNER_DO':
      echo $target.'の心を読みました';
      break;

    case 'JAMMER_MAD_DO':
      echo $target.'の占いを妨害しました';
      break;

    case 'VOODOO_MAD_DO':
    case 'VOODOO_FOX_DO':
      echo $target.'に呪いをかけました';
      break;

    case 'POSSESSED_DO':
      echo $target.'を狙いました';
      break;

    case 'MANIA_DO':
      echo $target.'を真似しました';
      break;
    }
    echo $footer;
  }
}

//死亡者の遺言を生成
function GenerateLastWords($shift = false){
  global $MESSAGE;

  //スキップ判定
  if (! (DB::$ROOM->IsPlaying() || DB::$ROOM->log_mode) || DB::$ROOM->personal_mode) return null;

  $query = DB::$ROOM->GetQueryHeader('result_lastwords', 'handle_name', 'message') . ' AND date = ';
  $date  = DB::$ROOM->date - ($shift ? 0 : 1); //基本は前日
  $stack = DB::FetchAssoc($query . $date);
  if (count($stack) < 1) return null;
  shuffle($stack); //表示順はランダム

  $str = '';
  foreach ($stack as $list){
    extract($list);
    LineToBR($message);
    $str .= <<<EOF
<tr>
<td class="lastwords-title">{$handle_name}<span>さんの遺言</span></td>
<td class="lastwords-body">{$message}</td>
</tr>

EOF;
  }

  return <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">
{$str}</table>

EOF;
}

//死亡者の遺言を出力
function OutputLastWords($shift = false){
  if (is_null($str = GenerateLastWords($shift))) return false;
  echo $str;
}

//前の日の 狼が食べた、狐が占われて死亡、投票結果で死亡のメッセージ
function GenerateDeadMan(){
  //ゲーム中以外は出力しない
  if (! DB::$ROOM->IsPlaying()) return null;

  $yesterday = DB::$ROOM->date - 1;

  if (DB::$ROOM->test_mode){
    $stack_list = RQ::GetTest()->result_dead;
  }
  else {
    //共通クエリ
    $query_header = DB::$ROOM->GetQueryHeader('result_dead', 'date', 'type', 'handle_name', 'result');
    if (DB::$ROOM->IsDay()) {
      $query = sprintf("date = %d AND scene = '%s'", $yesterday, 'night');
    }
    else {
      $query = sprintf("date = %d AND scene = '%s'", DB::$ROOM->date, 'day');
    }
    $stack_list = DB::FetchAssoc("{$query_header} AND {$query} ORDER BY RAND()");
  }

  $str = GenerateWeatherReport();
  foreach ($stack_list as $stack){
    $str .= GenerateDeadManType($stack['handle_name'], $stack['type'], $stack['result']);
  }

  //ログ閲覧モード以外なら二つ前も死亡者メッセージ表示
  if ($yesterday < 1 || DB::$ROOM->log_mode || DB::$ROOM->test_mode || (DB::$ROOM->date == 2 && DB::$ROOM->Isday())){
    return $str;
  }
  $str .= '<hr>'; //死者が無いときに境界線を入れない仕様にする場合はクエリの結果をチェックする
  $query = sprintf("date = %d AND scene = '%s'", $yesterday, DB::$ROOM->scene);
  foreach (DB::FetchAssoc("{$query_header} AND {$query} ORDER BY RAND()") as $stack){
    $str .= GenerateDeadManType($stack['handle_name'], $stack['type'], $stack['result']);
  }
  return $str;
}

//天候メッセージの生成
function GenerateWeatherReport(){
  global $ROLE_DATA;

  if (! isset(DB::$ROOM->event->weather) || (DB::$ROOM->log_mode && DB::$ROOM->IsNight())) return '';
  $weather = $ROLE_DATA->weather_list[DB::$ROOM->event->weather];
  return '<div class="weather">今日の天候は<span>' . $weather['name'] . '</span>です (' .
    $weather['caption'] . ')</div>';
}


//死者のタイプ別に死亡メッセージを生成
function GenerateDeadManType($name, $type, $result){
  global $MESSAGE;

  if (isset($name)) $name .= ' ';
  $base   = true;
  $class  = null;
  $reason = null;
  $action = strtolower($type);
  $open_reason = DB::$ROOM->IsOpenData();
  $show_reason = $open_reason || DB::$SELF->IsLiveRole('yama_necromancer');
  $str = '<table class="dead-type">'."\n";
  switch ($type){
  case 'VOTE_KILLED':
  case 'BLIND_VOTE':
    $base  = false;
    $class = 'vote';
    break;

  case 'LOVERS_FOLLOWED':
    $base  = false;
    $class = 'lovers';
    break;

  case 'REVIVE_SUCCESS':
    $base  = false;
    $class = 'revive';
    break;

  case 'REVIVE_FAILED':
    if (! DB::$ROOM->IsFinished() &&
	! (DB::$SELF->IsDead() || DB::$SELF->IsRole('attempt_necromancer'))) {
      return;
    }
    $base  = false;
    $class = 'revive';
    break;

  case 'POSSESSED_TARGETED':
    if (! $open_reason) return;
    $base = false;
    break;

  case 'NOVOTED':
    $base  = false;
    $class = 'sudden-death';
    break;

  case 'SUDDEN_DEATH':
    $base   = false;
    $class  = 'sudden-death';
    $action = 'vote_sudden_death';
    if ($show_reason) $reason = strtolower($result);
    break;

  case 'FLOWERED':
  case 'CONSTELLATION':
  case 'PIERROT':
    $base   = false;
    $class  = 'fairy';
    $action = strtolower($type . '_' . $result);
    break;

  case 'JOKER_MOVED':
  case 'DEATH_NOTE_MOVED':
    if (! $open_reason) return;
    $base  = false;
    $class = 'fairy';
    break;

  default:
    if ($show_reason) $reason = $action;
    break;
  }
  $str .= is_null($class) ? '<tr>' : '<tr class="dead-type-'.$class.'">';
  $str .= '<td>'.$name.$MESSAGE->{$base ? 'deadman' : $action}.'</td>';
  if (isset($reason)) $str .= "</tr>\n<tr><td>(".$name.$MESSAGE->$reason.')</td>';
  return $str."</tr>\n</table>\n";
}

//死者のタイプ別に死亡メッセージを生成
function GenerateDeadManTypeOld($name, $type){
  global $MESSAGE;

  //タイプの解析
  $base_type    = $type;
  $parsed_type  = explode('_', $type);
  $footer_type  = array_pop($parsed_type);
  $implode_type = implode('_', $parsed_type);
  switch ($footer_type){
  case 'day':
  case 'night':
    $base_type = $implode_type;
    break;

  default:
    switch ($implode_type){
    case 'SUDDEN_DEATH':
    case 'FLOWERED':
    case 'CONSTELLATION':
    case 'PIERROT':
      $base_type = $implode_type;
      break;
    }
    break;
  }

  $name .= ' ';
  $base   = true;
  $class  = null;
  $reason = null;
  $action = strtolower($base_type);
  $open_reason = DB::$ROOM->IsOpenData();
  $show_reason = $open_reason || DB::$SELF->IsLiveRole('yama_necromancer');
  $str = '<table class="dead-type">'."\n";
  switch ($base_type){
  case 'VOTE_KILLED':
    $base  = false;
    $class = 'vote';
    break;

  case 'BLIND_VOTE':
    $name  = '';
    $base  = false;
    $class = 'vote';
    break;

  case 'LOVERS_FOLLOWED':
    $base  = false;
    $class = 'lovers';
    break;

  case 'REVIVE_SUCCESS':
    $base  = false;
    $class = 'revive';
    break;

  case 'REVIVE_FAILED':
    if (! DB::$ROOM->IsFinished() &&
	! (DB::$SELF->IsDead() || DB::$SELF->IsRole('attempt_necromancer'))) {
      return;
    }
    $base  = false;
    $class = 'revive';
    break;

  case 'POSSESSED_TARGETED':
    if (! $open_reason) return;
    $base = false;
    break;

  case 'NOVOTED':
    $base  = false;
    $class = 'sudden-death';
    break;

  case 'SUDDEN_DEATH':
    $base   = false;
    $class  = 'sudden-death';
    $action = 'vote_sudden_death';
    if ($show_reason) $reason = strtolower($footer_type);
    break;

  case 'FLOWERED':
  case 'CONSTELLATION':
  case 'PIERROT':
    $base   = false;
    $class  = 'fairy';
    $action = strtolower($type);
    break;

  case 'JOKER_MOVED':
  case 'DEATH_NOTE_MOVED':
    if (! $open_reason) return;
    $base  = false;
    $class = 'fairy';
    break;

  default:
    if ($show_reason) $reason = $action;
    break;
  }
  $str .= is_null($class) ? '<tr>' : '<tr class="dead-type-'.$class.'">';
  $str .= '<td>'.$name.$MESSAGE->{$base ? 'deadman' : $action}.'</td>';
  if (isset($reason)) $str .= "</tr>\n<tr><td>(".$name.$MESSAGE->$reason.')</td>';
  return $str."</tr>\n</table>\n";
}

//前日に死亡メッセージの出力
function OutputDeadMan(){
  if (is_null($str = GenerateDeadMan())) return false;
  echo $str;
}
