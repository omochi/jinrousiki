<?php
//-- 基礎関数 --//
//配列からランダムに一つ取り出す
function GetRandom($array){
  return $array[array_rand($array)];
}

//-- 時間関連 --//
//リアルタイムの経過時間
function GetRealPassTime(&$left_time){
  global $ROOM;

  //シーンの最初の時刻を取得
  $query = 'SELECT MIN(time) FROM talk' . $ROOM->GetQuery() .
    " AND location LIKE '{$ROOM->day_night}%'";
  if(($start_time = FetchResult($query)) === false) $start_time = $ROOM->system_time;

  $base_time = $ROOM->real_time->{$ROOM->day_night} * 60; //設定された制限時間
  if(($left_time = $base_time - ($ROOM->system_time - $start_time)) < 0) $left_time = 0; //残り時間
  return array($start_time, $start_time + $base_time);
}

//会話で時間経過制の経過時間
function GetTalkPassTime(&$left_time, $silence = false){
  global $TIME_CONF, $ROOM;

  $query = 'SELECT SUM(spend_time) FROM talk' . $ROOM->GetQuery() .
    " AND location LIKE '{$ROOM->day_night}%'";
  $spend_time = (int)FetchResult($query);

  if($ROOM->IsDay()){ //昼は12時間
    $base_time = $TIME_CONF->day;
    $full_time = 12;
  }
  else{ //夜は6時間
    $base_time = $TIME_CONF->night;
    $full_time = 6;
  }
  if(($left_time = $base_time - $spend_time) < 0) $left_time = 0; //残り時間
  $base_left_time = $silence ? $TIME_CONF->silence_pass : $left_time; //仮想時間の計算
  return ConvertTime($full_time * $base_left_time * 60 * 60 / $base_time);
}

//-- 役職関連 --//
//巫女の判定結果 (システムメッセージ)
function InsertMediumMessage(){
  global $ROOM, $USERS;

  $flag = false; //巫女の出現判定
  $stack = array();
  foreach($USERS->rows as $user){
    $flag |= $user->IsRoleGroup('medium');
    if($user->suicide_flag){
      $stack[] = $USERS->GetHandleName($user->uname, true) . "\t" . $user->GetCamp();
    }
  }
  if($flag) foreach($stack as $str) $ROOM->SystemMessage($str, 'MEDIUM_RESULT');
}

//恋人の後追い死処理
function LoversFollowed($sudden_death = false){
  global $MESSAGE, $ROOM, $USERS;

  $cupid_list      = array(); //キューピッドのID => 恋人のID
  $lost_cupid_list = array(); //恋人が死亡したキューピッドのリスト
  $checked_list    = array(); //処理済キューピッドのID

  foreach($USERS->rows as $user){ //キューピッドと死んだ恋人のリストを作成
    foreach($user->GetPartner('lovers', true) as $id){
      $cupid_list[$id][] = $user->user_no;
      if($user->dead_flag || $user->revive_flag) $lost_cupid_list[$id] = $id;
    }
  }

  while(count($lost_cupid_list) > 0){ //対象キューピッドがいれば処理
    $cupid_id = array_shift($lost_cupid_list);
    $checked_list[] = $cupid_id;
    foreach($cupid_list[$cupid_id] as $lovers_id){ //キューピッドのリストから恋人の ID を取得
      $user = $USERS->ById($lovers_id); //恋人の情報を取得

      if($sudden_death){ //突然死の処理
	if(! $user->ToDead()) continue;
	$ROOM->Talk($user->handle_name . $MESSAGE->lovers_followed);
	$user->SaveLastWords();
      }
      elseif(! $USERS->Kill($user->user_no, 'LOVERS_FOLLOWED_' . $ROOM->day_night)){ //通常処理
	continue;
      }
      $user->suicide_flag = true;

      foreach($user->GetPartner('lovers') as $id){ //後追いした恋人のキューピッドのIDを取得
	if(! (in_array($id, $checked_list) || in_array($id, $lost_cupid_list))){ //連鎖判定
	  $lost_cupid_list[] = $id;
	}
      }
    }
  }
}

//勝敗をチェック
function CheckVictory($check_draw = false){
  global $GAME_CONF, $ROOM, $USERS;

  //コピー系がいるのでキャッシュを更新するかクエリから引くこと
  $query_count = $ROOM->GetQuery(false, 'user_entry') . " AND live = 'live' AND user_no > 0 AND ";
  $human  = FetchResult($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')"); //村人
  $wolf   = FetchResult($query_count . "role LIKE '%wolf%'"); //人狼
  $fox    = FetchResult($query_count . "role LIKE '%fox%'"); //妖狐
  $lovers = FetchResult($query_count . "role LIKE '%lovers%'"); //恋人
  $quiz   = FetchResult($query_count . "role LIKE 'quiz%'"); //出題者

  //-- 吸血鬼の勝利判定 --//
  $vampire = false;
  $living_id_list = array(); //生存者の ID リスト
  $infected_list = array(); //吸血鬼 => 感染者リスト
  foreach($USERS->GetLivingUsers(true) as $uname){
    $user = $USERS->ByUname($uname);
    $user->ReparseRoles();
    $living_id_list[] = $user->user_no;
    if($user->IsRole('infected')){
      foreach($user->GetPartner('infected') as $id) $infected_list[$id][] = $user->user_no;
    }
  }
  if(count($living_id_list) == 1){
    $vampire = $USERS->ByID(array_shift($living_id_list))->IsRole('vampire');
  }
  else{
    foreach($infected_list as $id => $stack){
      $diff_list = array_diff($living_id_list, $stack);
      if(count($diff_list) == 1 && in_array($id, $diff_list)){
	$vampire = true;
	break;
      }
    }
  }

  $victory_role = ''; //勝利陣営
  if($human == $quiz && $wolf == 0 && $fox == 0) //全滅
    $victory_role = $quiz > 0 ? 'quiz' : 'vanish';
  elseif($vampire) //吸血鬼支配
    $victory_role = $lovers > 1 ? 'lovers' : 'vampire';
  elseif($wolf == 0) //狼全滅
    $victory_role = $lovers > 1 ? 'lovers' : ($fox > 0 ? 'fox1' : 'human');
  elseif($wolf >= $human) //村全滅
    $victory_role = $lovers > 1 ? 'lovers' : ($fox > 0 ? 'fox2' : 'wolf');
  elseif($human + $wolf + $fox == $lovers) //恋人支配
    $victory_role = 'lovers';
  elseif($ROOM->IsQuiz() && $quiz == 0) //クイズ村 GM 死亡
    $victory_role = 'quiz_dead';
  elseif($check_draw && $ROOM->GetVoteTimes() > $GAME_CONF->draw) //引き分け
    $victory_role = 'draw';

  if($victory_role == '') return false;

  //ゲーム終了
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame', " .
    "victory_role = '{$victory_role}', finish_time = NOW() WHERE room_no = {$ROOM->id}";
  SendQuery($query, true);
  //OutputSiteSummary(); //RSS機能はテスト中
  return true;
}

//-- 投票関連 --//
//夜の自分の投票済みチェック
function CheckSelfVoteNight($situation, $not_situation = ''){
  global $ROOM, $SELF;

  $query = $ROOM->GetQuery(true, 'vote') . ' AND ';
  if($situation == 'WOLF_EAT'){
    $query .= "situation = '{$situation}'";
  }
  elseif($not_situation != ''){
    $query .= "uname = '{$SELF->uname}' " .
      "AND(situation = '{$situation}' OR situation = '{$not_situation}')";
  }
  else{
    $query .= "uname = '{$SELF->uname}' AND situation = '{$situation}'";
  }
  return FetchResult($query) > 0;
}

//-- 出力関連 --//
//HTMLヘッダー出力
function OutputGamePageHeader(){
  global $SERVER_CONF, $GAME_CONF, $RQ_ARGS, $ROOM, $SELF;

  //引数を格納
  $url_header = 'game_frame.php?room_no=' . $ROOM->id . '&auto_reload=' . $RQ_ARGS->auto_reload;
  if($RQ_ARGS->play_sound) $url_header .= '&play_sound=on';
  if($RQ_ARGS->list_down)  $url_header .= '&list_down=on';

  $title = $SERVER_CONF->title . ' [プレイ]';
  $anchor_header = '<br>'."\n";
  /*
    Mac で JavaScript でエラーを吐くブラウザがあった当時のコード
    現在の Safari、Firefox では不要なので false でスキップしておく
    //if(preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){
  */
  if(false){
    $sentence = '';
    $anchor_header .= '<a href="';
    $anchor_footer = '" target="_top">ここをクリックしてください</a>';
  }
  else{
    $sentence = '<script type="text/javascript"><!--'."\n" .
      'if(top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $anchor_header .= '切り替わらないなら <a href="';
    $anchor_footer = '" target="_top">ここ</a>';
  }

  //ゲーム中、死んで霊話モードに行くとき
  if($ROOM->IsPlaying() && $SELF->IsDead() &&
     ! ($ROOM->log_mode || $ROOM->dead_mode || $ROOM->heaven_mode)){
    $jump_url =  $url_header . '&dead_mode=on';
    $sentence .= '天国モードに切り替えます。';
  }
  elseif($ROOM->IsAfterGame() && $ROOM->dead_mode){ //ゲームが終了して霊話から戻るとき
    $jump_url = $url_header;
    $sentence .= 'ゲーム終了後のお部屋に飛びます。';
  }
  elseif($SELF->IsLive() && ($ROOM->dead_mode || $ROOM->heaven_mode)){
    $jump_url = $url_header;
    $sentence .= 'ゲーム画面に飛びます。';
  }

  if($jump_url != ''){ //移動先が設定されていたら画面切り替え
    $sentence .= $anchor_header . $jump_url . $anchor_footer;
    OutputActionResult($title, $sentence, $jump_url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  if(! $ROOM->log_mode){ //過去ログ閲覧時は不要
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load = "change_css('{$ROOM->day_night}');";
  }

  if($RQ_ARGS->auto_reload != 0 && ! $ROOM->IsAfterGame()){ //自動リロードをセット
    echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
  }

  //ゲーム中、リアルタイム制なら経過時間を Javascript でリアルタイム表示
  if($ROOM->IsPlaying() && $ROOM->IsRealTime() && ! ($ROOM->log_mode || $ROOM->heaven_mode)){
    list($start_time, $end_time) = GetRealPassTime($left_time);
    $on_load .= 'output_realtime();';
    OutputRealTimer($start_time, $end_time);
  }
  echo '</head>'."\n" . '<body onLoad="' . $on_load . '">'."\n".'<a id="#game_top"></a>'."\n";
}

//リアルタイム表示に使う JavaScript の変数を出力
function OutputRealTimer($start_time, $end_time){
  global $ROOM;

  $sentence    = '　' . ($ROOM->IsDay() ? '日没' : '夜明け') . 'まで ';
  $start_date  = GenerateJavaScriptDate($start_time);
  $end_date    = GenerateJavaScriptDate($end_time);
  $server_date = GenerateJavaScriptDate($ROOM->system_time);

  echo '<script type="text/javascript" src="javascript/output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var sentence = "' . $sentence . '";'."\n";
  echo "var end_date = {$end_date} * 1 + (new Date() - {$server_date});\n";
  echo "var diff_seconds = Math.floor(({$end_date} - {$start_date}) / 1000);\n";
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
  global $GAME_CONF, $RQ_ARGS;

  $str = '[自動更新](' . $url . '0">' . ($RQ_ARGS->auto_reload == 0 ? '【手動】' : '手動') . '</a>';
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '秒';
    $value = $RQ_ARGS->auto_reload == $time ? '【' . $name . '】' : $name;
    $str .= ' ' . $url . $time . '">' . $value . '</a>';
  }
  echo $str . ')'."\n";
}

//ログへのリンクを出力
function OutputLogLink(){
  global $ROOM;

  $url = 'old_log.php?room_no=' . $ROOM->id;
  echo GenerateLogLink($url, true, '<br>' . ($ROOM->view_mode ? '[ログ]' : '[全体ログ]')) .
    GenerateLogLink($url . '&add_role=on', false, '<br>[役職表示ログ]');
}

//ゲームオプション画像を出力
function OutputGameOption(){
  global $ROOM;

  $query = 'SELECT game_option, option_role, max_user FROM room WHERE room_no = ' . $ROOM->id;
  extract(FetchAssoc($query, true));
  echo '<table class="time-table"><tr>'."\n" .
    '<td>ゲームオプション：' . GenerateGameOptionImage($game_option, $option_role) .
    ' 最大' . $max_user . '人</td>'."\n" . '</tr></table>'."\n";
}

//日付と生存者の人数を出力
function OutputTimeTable(){
  global $ROOM;

  if($ROOM->IsBeforeGame()) return false; //ゲームが始まっていなければ表示しない

  $query = $ROOM->GetQuery(false, 'user_entry') . " AND live = 'live' AND user_no > 0";
  echo '<td>' . $ROOM->date . ' 日目<span>(生存者' . FetchResult($query) . '人)</span></td>'."\n";
}

//プレイヤー一覧出力
function OutputPlayerList(){
  global $DEBUG_MODE, $ICON_CONF, $ROOM, $USERS, $SELF;

  //PrintData($ROOM->event); //テスト用
  //配役公開フラグを判定
  $is_open_role = $ROOM->IsAfterGame() || $SELF->IsDummyBoy() ||
    ($SELF->IsDead() && $ROOM->IsOpenCast());

  $count = 0; //改行カウントを初期化
  $str = '<div class="player"><table><tr>'."\n";
  foreach($USERS->rows as $id => $user){
    if($count > 0 && ($count % 5) == 0) $str .= "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    //ゲーム開始投票をしていたら背景色を変える
    if($ROOM->IsBeforeGame() && ($user->IsDummyBoy(true) || isset($ROOM->vote[$user->uname]))){
      $td_header = '<td class="already-vote">';
    }
    else{
      $td_header = '<td>';
    }

    //ユーザプロフィールと枠線の色を追加
    //Alt, Title 内の改行はブラウザ依存あり (Firefox 系は無効)
    $profile = str_replace("\n", '&#13;&#10', $user->profile);
    $str .= $td_header . '<img title="' . $profile . '" alt="' . $profile .
      '" style="border-color: ' . $user->color . ';"';

    //生死情報に応じたアイコンを設定
    $path = $ICON_CONF->path . '/' . $user->icon_filename;
    if($ROOM->IsBeforeGame() || $USERS->IsVirtualLive($id)){
      $live = '(生存中)';
    }
    else{
      $live = '(死亡)';
      $str .= ' onMouseover="this.src=' . "'$path'" . '"'; //元のアイコン

      $path = $ICON_CONF->dead; //アイコンを死亡アイコンに入れ替え
      $str .= ' onMouseout="this.src=' . "'$path'" . '"';
    }
    $str .= $ICON_CONF->tag . ' src="' . $path . '"></td>'."\n";

    //HN を追加
    $str .= $td_header . '<font color="' . $user->color . '">◆</font>' . $user->handle_name;
    if($DEBUG_MODE) $str .= ' (' . $id . ')';
    $str .= '<br>'."\n";

    //ゲーム終了後・死亡後＆霊界役職公開モードなら、役職・ユーザネームも表示
    if($is_open_role){
      $uname = str_replace(array('◆', '◇'), array('◆<br>', '◇<br>'), $user->uname); //トリップ対応
      $str .= '　(' . $uname; //ユーザ名を追加

      //憑依状態なら憑依しているユーザを追加
      $real_user = $USERS->ByReal($id);
      if($real_user->IsSame($user->uname)) $real_user = $USERS->TraceExchange($id); //交換憑依判定
      if(! $real_user->IsSame($user->uname) && $real_user->IsLive()){
	$str .= '<br>[' . $real_user->uname . ']';
      }
      $str .= ')<br>' . $user->GenerateRoleName() . '<br>'."\n"; //役職情報を追加
    }
    $str .= $live . '</td>'."\n";
  }
  echo $str . '</tr></table></div>'."\n";
}

//勝敗の出力
function OutputVictory(){
  global $VICT_MESS, $ROOM, $USERS, $SELF;

  //-- 村の勝敗結果 --//
  $victory = FetchResult('SELECT victory_role FROM room WHERE room_no = ' . $ROOM->id);
  $class   = $victory;
  $winner  = $victory;

  switch($victory){ //特殊ケース対応
  //妖狐勝利系
  case 'fox1':
  case 'fox2':
    $class = 'fox';
    break;

  //引き分け系
  case 'draw': //引き分け
  case 'vanish': //全滅
  case 'quiz_dead': //クイズ村 GM 死亡
    $class = 'none';
    break;

  //廃村系
  case NULL:
    $class  = 'none';
    $winner = $ROOM->date > 0 ? 'unfinished' : 'none';
    break;
  }
  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$VICT_MESS->$winner}</td>
</tr></table>

EOF;

  //-- 個々の勝敗結果 --//
  //勝敗未決定、観戦モード、ログ閲覧モードならスキップ
  if(is_null($victory) || $ROOM->view_mode || $ROOM->log_mode) return;

  $result = 'win';
  $camp = $SELF->GetCamp(true); //所属陣営を取得

  if($victory == 'draw' || $victory == 'vanish'){ //引き分け系
    $class  = 'none';
    $result = 'draw';
  }
  elseif($victory == 'quiz_dead'){ //出題者死亡
    $class  = 'none';
    $result = $camp == 'quiz' ? 'lose' : 'draw';
  }
  else{
    switch($camp){
    case 'fox':
      if(strpos($victory, $camp) !== false){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    case 'chiroptera':
      if($SELF->IsLive()){ //蝙蝠陣営は生きていれば勝利
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    case 'human':
      if($SELF->IsRole('escaper') && $SELF->IsDead()){ //逃亡者は死亡していたら敗北
	$class  = 'none';
	$result = 'lose';
	break;
      }
      elseif($SELF->IsDoll()){ //人形系は人形遣いが生存していたら敗北
	foreach($USERS->rows as $user){
	  if($user->IsRole('doll_master') && $user->IsLive()){
	    $class  = 'none';
	    $result = 'lose';
	    break 2;
	  }
	}
      }

      if($victory == $camp){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    case 'vampire':
      if($victory == $camp && $SELF->IsLive()){ //吸血鬼陣営は生き残った者だけが勝利
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    default:
      if($victory == $camp){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;
    }
  }
  $result = 'self_' . $result;

  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$VICT_MESS->$result}</td>
</tr></table>

EOF;
}

//投票の集計出力
function OutputVoteList(){
  global $ROOM;

  if(! $ROOM->IsPlaying()) return false; //ゲーム中以外は出力しない

 //昼なら前日、夜ならの今日の集計を表示
  echo GetVoteList(($ROOM->IsDay() && ! $ROOM->log_mode) ? $ROOM->date - 1 : $ROOM->date);
}

//再投票の時、メッセージを表示
function OutputRevoteList(){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $SELF, $COOKIE, $SOUND;

  if(! $ROOM->IsDay()) return false; //昼以外は出力しない
  if(($revote_times = $ROOM->GetVoteTimes(true)) == 0) return false; //再投票の回数を取得

  if($RQ_ARGS->play_sound && ! $ROOM->view_mode && $revote_times > $COOKIE->vote_times){
    $SOUND->Output('revote'); //音を鳴らす
  }

  //投票済みチェック
  $vote_times = $revote_times + 1;
  $query = $ROOM->GetQuery(true, 'vote') . " AND vote_times = {$vote_times} " .
    "AND uname = '{$SELF->uname}'";
  if(FetchResult($query) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '回' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  echo GetVoteList($ROOM->date); //投票結果を出力
}

//指定した日付の投票結果をロードして GenerateVoteList() に渡す
function GetVoteList($date){
  global $ROOM;

  //指定された日付の投票結果を取得
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$date} and type = 'VOTE_KILL'";
  return GenerateVoteList(FetchArray($query), $date);
}

//投票データから結果を生成する
function GenerateVoteList($raw_data, $date){
  global $RQ_ARGS, $ROOM, $SELF;

  if(count($raw_data) < 1) return NULL; //投票総数

  //投票数開示判定
  $is_open_vote = ($ROOM->IsFinished() || $ROOM->test_mode ||
		   ($ROOM->IsOption('open_vote') ? true :
		    ($SELF->IsDead() && $ROOM->IsOpenCast())));

  $table_stack = array();
  $header = '<td class="vote-name">';
  foreach($raw_data as $raw){ //個別投票データのパース
    list($handle_name, $target_name, $voted_number,
	 $vote_number, $vote_times) = explode("\t", $raw);

    $stack = array('<tr>' .  $header . $handle_name, '<td>' . $voted_number . ' 票',
		   '<td>投票先' . ($is_open_vote ? ' ' . $vote_number . ' 票' : '') . ' →',
		   $header . $target_name, '</tr>');
    $table_stack[$vote_times][] = implode('</td>', $stack);
  }

  if(! $RQ_ARGS->reverse_log) krsort($table_stack); //正順なら逆転させる

  $str = '';
  $header = '<tr><td class="vote-times" colspan="4">' . $date . ' 日目 ( ';
  $footer = ' 回目)</td>';
  foreach($table_stack as $vote_times => $stack){
    array_unshift($stack, '<table class="vote-list">', $header . $vote_times . $footer);
    $stack[] = '</table>';
    $str .= implode("\n", $stack);
  }
  return $str;
}

//会話ログ出力
function OutputTalkLog(){
  global $ROOM;

  $builder =& new DocumentBuilder();
  $builder->BeginTalk('talk');
  foreach($ROOM->LoadTalk() as $talk) OutputTalk($talk, $builder); //会話出力
  OutputTimeStamp($builder);
  $builder->EndTalk();
}

//会話出力
function OutputTalk($talk, &$builder){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  //PrintData($talk);
  //発言ユーザを取得
  /*
    $uname は必ず $talk から取得すること。
    $USERS にはシステムユーザー 'system' が存在しないため、$said_user は常に NULL になっている。
  */
  $said_user = $talk->scene == 'heaven' ? $USERS->ByUname($talk->uname) :
    $USERS->ByVirtualUname($talk->uname);

  //基本パラメータを取得
  $symbol      = '<font color="' . $said_user->color . '">◆</font>';
  $handle_name = $said_user->handle_name;
  $sentence    = $talk->sentence;
  $font_type   = $talk->font_type;

  //実ユーザを取得
  if($RQ_ARGS->add_role && $said_user->user_no > 0){ //役職表示モード対応
    $real_user = $talk->scene == 'heaven' ? $said_user : $USERS->ByReal($said_user->user_no);
    $handle_name .= $real_user->GenerateShortRoleName($talk->scene == 'heaven');
  }
  else{
    $real_user = $USERS->ByRealUname($talk->uname);
  }

  //サトラレ系表示判定 (サトラレ / 受信者 / 共鳴者)
  $is_mind_read = $builder->flag->mind_read &&
    (($said_user->IsPartner('mind_read', $builder->actor->user_no) &&
      ! $said_user->IsRole('unconscious')) ||
     $builder->actor->IsPartner('mind_receiver', $said_user->user_no) ||
     $said_user->IsPartner('mind_friend', $builder->actor->partner_list));

  //特殊発言表示判定 (公開者 / 騒霊 / 憑依能力者)
  $flag_mind_read = $is_mind_read ||
    ($ROOM->date > 1 && ($said_user->IsRole('mind_open') ||
			 ($builder->flag->common && $said_user->IsRole('whisper_scanner')) ||
			 ($builder->flag->wolf   && $said_user->IsRole('howl_scanner')) ||
			 ($builder->flag->fox    && $said_user->IsRole('telepath_scanner')))) ||
    ($real_user->IsRole('possessed_wolf') && $builder->flag->wolf) ||
    ($real_user->IsRole('possessed_mad')  && $said_user->IsSame($builder->actor->uname)) ||
    ($real_user->IsRole('possessed_fox')  && $builder->flag->fox);

  //発言表示フラグ判定
  $flag_dummy_boy = $builder->flag->dummy_boy;
  $flag_common    = $builder->flag->common || $flag_mind_read;
  $flag_wolf      = $builder->flag->wolf   || $flag_mind_read;
  $flag_fox       = $builder->flag->fox    || $flag_mind_read;
  $flag_open_talk = $builder->flag->open_talk;

  if($talk->type == 'system' && isset($talk->action)){ //投票情報
    /*
      + ゲーム開始前の投票 (KICK 等) は常時表示
      + 「異議」ありは常時表示
    */
    switch($talk->action){
    case 'OBJECTION':
      return $builder->AddSystemMessage('objection-' . $said_user->sex, $handle_name . $sentence);

    case 'GAMESTART_DO':
      return true;

    default:
      return $ROOM->IsBeforeGame() || $flag_open_talk ?
	$builder->AddSystemMessage($talk->class, $handle_name . $sentence) : false;
    }
  }

  //システムメッセージ
  if($talk->uname == 'system') return $builder->AddSystemTalk($sentence);

  //身代わり君専用システムメッセージ
  if($talk->type == 'dummy_boy') return $builder->AddSystemTalk($sentence, 'dummy-boy');

  switch($talk->scene){
  case 'night':
    if($flag_open_talk){
      $talk_class = '';
      switch($talk->type){
      case 'common':
	$handle_name .= '<span>(共有者)</span>';
	$talk_class = 'night-common';
	$font_type .= ' night-common';
	break;

      case 'wolf':
	$handle_name .= '<span>(人狼)</span>';
	$talk_class = 'night-wolf';
	$font_type .= ' night-wolf';
	break;

      case 'mad':
	$handle_name .= '<span>(囁き狂人)</span>';
	$talk_class = 'night-wolf';
	$font_type .= ' night-wolf';
	break;

      case 'fox':
	$handle_name .= '<span>(妖狐)</span>';
	$talk_class = 'night-fox';
	$font_type .= ' night-fox';
	break;

      case 'self_talk':
	$handle_name .= '<span>の独り言</span>';
	$talk_class = 'night-self-talk';
	break;
      }
      return $builder->RawAddTalk($symbol, $handle_name, $sentence, $font_type, '', $talk_class);
    }
    else{
      switch($talk->type){
      case 'common': //共有者
	return $flag_common ? $builder->AddTalk($said_user, $talk)
	  : $builder->AddWhisper('common', $talk);

      case 'wolf': //人狼
	return $flag_wolf ? $builder->AddTalk($said_user, $talk)
	  : $builder->AddWhisper('wolf', $talk);

      case 'mad': //囁き狂人
	return $flag_wolf ? $builder->AddTalk($said_user, $talk) : false;

      case 'fox': //妖狐
	return $flag_fox ? $builder->AddTalk($said_user, $talk)
	  : ($SELF->IsRole('wise_wolf') ? $builder->AddWhisper('common', $talk) : false);

      case 'self_talk': //独り言
	if($builder->actor->IsSame($talk->uname) || $flag_dummy_boy || $flag_mind_read){
	  return $builder->AddTalk($said_user, $talk);
	}
	elseif($builder->actor->IsRole('whisper_ringing')){ //囁耳鳴は独り言が囁きに見える
	  return $builder->AddWhisper('common', $talk);
	}
	//吠耳鳴・孤立した狼の独り言は遠吠えに見える
	elseif($builder->actor->IsRole('howl_ringing') ||
	       ($said_user->IsWolf() && $said_user->IsLonely())){
	  return $builder->AddWhisper('wolf', $talk);
	}
	return false;
      }
    }
    return false;

  case 'heaven':
    return $flag_open_talk ?
      $builder->RawAddTalk($symbol, $handle_name, $sentence, $font_type, $talk->scene) : false;

  default:
    return $builder->AddTalk($said_user, $talk);
  }
}

//[村立て / ゲーム開始 / ゲーム終了] 時刻を出力
function OutputTimeStamp($builder){
  global $ROOM;

  $talk =& new Talk();
  $query = ' FROM room' . $ROOM->GetQuery(false);
  if($ROOM->IsBeforeGame()){ //村立て時刻を取得して表示
    $time = FetchResult('SELECT establish_time' . $query);
    $talk->sentence = '村作成';
  }
  elseif($ROOM->IsNight() && $ROOM->date == 1){ //ゲーム開始時刻を取得して表示
    $time = FetchResult('SELECT start_time' . $query);
    $talk->sentence = 'ゲーム開始';
  }
  elseif($ROOM->IsAfterGame()){ //ゲーム終了時刻を取得して表示
    $time = FetchResult('SELECT finish_time' . $query);
    $talk->sentence = 'ゲーム終了';
  }

  if(is_null($time)) return false;
  $talk->uname = 'system';
  $talk->sentence .= '：' . ConvertTimeStamp($time);
  $talk->ParseLocation($ROOM->day_night . ' system');
  OutputTalk($talk, $builder);
}

//占う、狼が狙う、護衛する等、能力を使うメッセージ
function OutputAbilityAction(){
  global $MESSAGE, $ROOM, $SELF;

  //昼間で役職公開が許可されているときのみ表示
  //(猫又は役職公開時は行動できないので不要)
  if(! $ROOM->IsDay() || ! ($SELF->IsDummyBoy() || $ROOM->IsOpenCast())) return false;

  $yesterday = $ROOM->date - 1;
  $header = '<b>前日の夜、';
  $footer = '</b><br>'."\n";
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  if($yesterday == 1){
    array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list, 'ESCAPE_DO', 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO',
	       'DREAM_EAT', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
	       'POSSESSED_DO', 'POSSESSED_NOT_DO', 'VAMPIRE_DO');
  }

  $action = '';
  foreach($action_list as $this_action){
    if($action != '') $action .= ' OR ';
    $action .= "type = '$this_action'";
  }

  $query = "SELECT message AS sentence, type FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$yesterday} AND ( {$action} )";
  $message_list = FetchAssoc($query);
  foreach($message_list as $array){
    extract($array);
    list($actor, $target) = explode("\t", $sentence);
    echo $header.$actor.' ';
    switch($type){
    case 'WOLF_EAT':
    case 'DREAM_EAT':
    case 'POSSESSED_DO':
    case 'ASSASSIN_DO':
    case 'VAMPIRE_DO':
      echo 'は '.$target.' を狙いました';
      break;

    case 'ESCAPE_DO':
      echo 'は '.$target.' '.$MESSAGE->escape_do;
      break;

    case 'MAGE_DO':
    case 'CHILD_FOX_DO':
      echo 'は '.$target.' を占いました';
      break;

    case 'VOODOO_KILLER_DO':
      echo 'は '.$target.' の呪いを祓いました';
      break;

    case 'JAMMER_MAD_DO':
      echo 'は '.$target.' の占いを妨害しました';
      break;

    case 'TRAP_MAD_DO':
      echo 'は '.$target.' '.$MESSAGE->trap_do;
      break;

    case 'TRAP_MAD_NOT_DO':
      echo $MESSAGE->trap_not_do;
      break;

    case 'POSSESSED_NOT_DO':
      echo $MESSAGE->possessed_not_do;
      break;

    case 'VOODOO_MAD_DO':
    case 'VOODOO_FOX_DO':
      echo 'は '.$target.' に呪いをかけました';
      break;

    case 'GUARD_DO':
      echo 'は '.$target.' '.$MESSAGE->guard_do;
      break;

    case 'ANTI_VOODOO_DO':
      echo 'は '.$target.' の厄を祓いました';
      break;

    case 'REPORTER_DO':
      echo 'は '.$target.' '.$MESSAGE->reporter_do;
      break;

    case 'ASSASSIN_NOT_DO':
      echo $MESSAGE->assassin_not_do;
      break;

    case 'MIND_SCANNER_DO':
      echo 'は '.$target.' の心を読みました';
      break;

    case 'CUPID_DO':
      echo 'は '.$target.' '.$MESSAGE->cupid_do;
      break;

    case 'FAIRY_DO':
      echo 'は '.$target.' '.$MESSAGE->fairy_do;;
      break;

    case 'MANIA_DO':
      echo 'は '.$target.' を真似しました';
      break;
    }
    echo $footer;
  }
}

//死亡者の遺言を出力
function OutputLastWords(){
  global $MESSAGE, $ROOM;

  //ゲーム中以外は出力しない
  if(! ($ROOM->IsPlaying() || $ROOM->log_mode)) return false;

  //前日の死亡者遺言を出力
  $set_date = $ROOM->date - 1;
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$set_date} AND type = 'LAST_WORDS' ORDER BY RAND()";
  $array = FetchArray($query);
  if(count($array) < 1) return false;

  echo <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">

EOF;

  foreach($array as $result){
    list($handle_name, $sentence) = explode("\t", $result, 2);
    LineToBR(&$sentence);

    echo <<<EOF
<tr>
<td class="lastwords-title">{$handle_name}<span>さんの遺言</span></td>
<td class="lastwords-body">{$sentence}</td>
</tr>

EOF;
  }
  echo '</table>'."\n";
}

//前の日の 狼が食べた、狐が占われて死亡、投票結果で死亡のメッセージ
function OutputDeadMan(){
  global $ROOM;

  //ゲーム中以外は出力しない
  if(! $ROOM->IsPlaying()) return false;

  $yesterday = $ROOM->date - 1;

  //共通クエリ
  $query_header = "SELECT message, type FROM system_message WHERE room_no = {$ROOM->id} AND date =";

  //死亡タイプリスト
  $dead_type_list = array(
    'day' => array('VOTE_KILLED' => true, 'POISON_DEAD_day' => true,
		   'LOVERS_FOLLOWED_day' => true, 'SUDDEN_DEATH_%' => false, 'NOVOTED_day' => true),

    'night' => array('WOLF_KILLED' => true, 'HUNGRY_WOLF_KILLED' => true, 'POSSESSED' => true,
		     'POSSESSED_TARGETED' => true, 'POSSESSED_RESET' => true,
		     'DREAM_KILLED' => true, 'TRAPPED' => true, 'CURSED' => true, 'FOX_DEAD' => true,
		     'HUNTED' => true, 'REPORTER_DUTY' => true, 'ASSASSIN_KILLED' => true,
		     'PRIEST_RETURNED' => true, 'POISON_DEAD_night' => true,
		     'LOVERS_FOLLOWED_night' => true, 'REVIVE_%' => false, 'SACRIFICE' => true,
		     'FLOWERED_%' => false, 'CONSTELLATION_%' => false, 'NOVOTED_night' => true));

  foreach($dead_type_list as $scene => $action_list){
    $query_list = array();
    foreach($action_list as $action => $type){
      $query_list[] = 'type ' . ($type ? '=' : 'LIKE') . " '{$action}'";
    }
    $type_list->$scene = implode(' OR ', $query_list);
  }

  if($ROOM->IsDay()){
    $set_date = $yesterday;
    $type = $type_list->night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_list->day;
  }

  $array = FetchAssoc("{$query_header} {$set_date} AND ( {$type} ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }

  //ログ閲覧モード以外なら二つ前も死亡者メッセージ表示
  if($ROOM->log_mode) return;
  $set_date = $yesterday;
  if($set_date < 2) return;
  $type = $type_list->{$ROOM->day_night};

  echo '<hr>'; //死者が無いときに境界線を入れない仕様にする場合は $array の中身をチェックする
  $array = FetchAssoc("{$query_header} {$set_date} AND ( {$type} ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }
}

//死者のタイプ別に死亡メッセージを出力
function OutputDeadManType($name, $type){
  global $MESSAGE, $ROOM, $SELF;

  $deadman_header = '<tr><td>'.$name.' '; //基本メッセージヘッダ
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //基本メッセージ
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //追加共通ヘッダ
  $open_reason = $ROOM->IsFinished() || $SELF->IsDummyBoy() ||
    ($SELF->IsDead() && $ROOM->IsOpenCast());
  $show_reason = $open_reason || ($SELF->IsRole('yama_necromancer') && $SELF->IsLive());

  echo '<table class="dead-type">'."\n";
  switch($type){
  case 'VOTE_KILLED':
    echo '<tr class="dead-type-vote">';
    echo '<td>'.$name.' '.$MESSAGE->vote_killed.'</td>';
    break;

  case 'POISON_DEAD_day':
  case 'POISON_DEAD_night':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->poison_dead.')</td>';
    break;

  case 'LOVERS_FOLLOWED_day':
  case 'LOVERS_FOLLOWED_night':
    echo '<tr class="dead-type-lovers">';
    echo '<td>'.$name.' '.$MESSAGE->lovers_followed.'</td>';
    break;

  case 'REVIVE_SUCCESS':
    echo '<tr class="dead-type-revive">';
    echo '<td>'.$name.' '.$MESSAGE->revive_success.'</td>';
    break;

  case 'REVIVE_FAILED':
    if($ROOM->IsFinished() || $SELF->IsDead()){
      echo '<tr class="dead-type-revive">';
      echo '<td>'.$name.' '.$MESSAGE->revive_failed.'</td>';
    }
    break;

  case 'POSSESSED_TARGETED':
    if($open_reason) echo '<tr><td>'.$name.' '.$MESSAGE->possessed_targeted.'</td>';
    break;

  case 'NOVOTED_day':
  case 'NOVOTED_night':
    echo '<tr class="dead-type-sudden-death">';
    echo '<td>'.$name.' '.$MESSAGE->novoted.'</td>';
    break;

  case 'SUDDEN_DEATH_CHICKEN':
  case 'SUDDEN_DEATH_RABBIT':
  case 'SUDDEN_DEATH_PERVERSENESS':
  case 'SUDDEN_DEATH_FLATTERY':
  case 'SUDDEN_DEATH_IMPATIENCE':
  case 'SUDDEN_DEATH_CELIBACY':
  case 'SUDDEN_DEATH_NERVY':
  case 'SUDDEN_DEATH_ANDROPHOBIA':
  case 'SUDDEN_DEATH_GYNOPHOBIA':
  case 'SUDDEN_DEATH_PANELIST':
  case 'SUDDEN_DEATH_SEALED':
  case 'SUDDEN_DEATH_JEALOUSY':
  case 'SUDDEN_DEATH_AGITATED':
  case 'SUDDEN_DEATH_FEBRIS':
  case 'SUDDEN_DEATH_FROSTBITE':
  case 'SUDDEN_DEATH_WARRANT':
  case 'SUDDEN_DEATH_CHALLENGE':
    echo '<tr class="dead-type-sudden-death">';
    echo '<td>'.$name.' '.$MESSAGE->vote_sudden_death.'</td>';
    if($show_reason){
      $action = strtolower(array_pop(explode('_', $type)));
      echo $reason_header.$MESSAGE->$action.')</td>';
    }
    break;

  case 'FLOWERED_A':
  case 'FLOWERED_B':
  case 'FLOWERED_C':
  case 'FLOWERED_D':
  case 'FLOWERED_E':
  case 'FLOWERED_F':
  case 'FLOWERED_G':
  case 'FLOWERED_H':
  case 'FLOWERED_I':
  case 'FLOWERED_J':
  case 'FLOWERED_K':
  case 'FLOWERED_L':
  case 'FLOWERED_M':
  case 'FLOWERED_N':
  case 'FLOWERED_O':
  case 'FLOWERED_P':
  case 'FLOWERED_Q':
  case 'FLOWERED_R':
  case 'FLOWERED_S':
  case 'FLOWERED_T':
  case 'FLOWERED_U':
  case 'FLOWERED_V':
  case 'FLOWERED_W':
  case 'FLOWERED_X':
  case 'FLOWERED_Y':
  case 'FLOWERED_Z':
  case 'CONSTELLATION_A':
  case 'CONSTELLATION_B':
  case 'CONSTELLATION_C':
  case 'CONSTELLATION_D':
  case 'CONSTELLATION_E':
  case 'CONSTELLATION_F':
  case 'CONSTELLATION_G':
  case 'CONSTELLATION_H':
  case 'CONSTELLATION_I':
  case 'CONSTELLATION_J':
  case 'CONSTELLATION_K':
  case 'CONSTELLATION_L':
  case 'CONSTELLATION_M':
  case 'CONSTELLATION_N':
  case 'CONSTELLATION_O':
  case 'CONSTELLATION_P':
  case 'CONSTELLATION_Q':
  case 'CONSTELLATION_R':
  case 'CONSTELLATION_S':
  case 'CONSTELLATION_T':
  case 'CONSTELLATION_U':
  case 'CONSTELLATION_V':
  case 'CONSTELLATION_W':
  case 'CONSTELLATION_X':
  case 'CONSTELLATION_Y':
  case 'CONSTELLATION_Z':
    $action = strtolower($type);
    echo '<tr class="dead-type-fairy">';
    echo '<td>'.$name.' '.$MESSAGE->$action.'</td>';
    break;

  default:
    echo $deadman;
    if($show_reason){
      $action = strtolower($type);
      echo $reason_header.$MESSAGE->$action.')</td>';
    }
    break;
  }
  echo "</tr>\n</table>\n";
}
