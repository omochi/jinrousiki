<?php
require_once('include/init.php');
$INIT_CONF->LoadRequest('RequestOldLog'); //引数を取得
$DB_CONF->Connect(); //DB 接続
if($RQ_ARGS->is_room){
  $INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
  $INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'VICT_MESS');

  $ROOM =& new Room($RQ_ARGS);
  $ROOM->log_mode = true;
  $ROOM->last_date = $ROOM->date;

  $USERS =& new UserDataSet($RQ_ARGS);
  $SELF  =& new User();

  OutputOldLog();
}
else{
  $INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'ROOM_IMG', 'GAME_OPT_MESS');
  OutputFinishedRooms($RQ_ARGS->page);
}
OutputHTMLFooter();

//-- 関数 --//
//過去ログ一覧表示
function OutputFinishedRooms($page){
  global $SERVER_CONF, $ROOM_CONF, $MESSAGE, $ROOM_IMG, $RQ_ARGS;

  //村数の確認
  $room_count = FetchResult("SELECT COUNT(status) FROM room WHERE status = 'finished'");
  if($room_count < 1){
    OutputActionResult($SERVER_CONF->title . ' [過去ログ]',
		       'ログはありません。<br>'."\n" . '<a href="./">←戻る</a>'."\n");
  }

  OutputHTMLHeader($SERVER_CONF->title . ' [過去ログ]', 'old_log_list');
echo <<<EOF
</head>
<body id="room_list">
<p><a href="./">←戻る</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table><tr><td class="list">

EOF;

  $LOG_CONF =& new OldLogConfig(); //設定をロード
  $is_reverse = empty($RQ_ARGS->reverse) ? $LOG_CONF->reverse : ($RQ_ARGS->reverse == 'on');
  $current_time = TZTime(); // 現在時刻の取得

  //ページリンクの出力
  $builder = new PageLinkBuilder('old_log', $RQ_ARGS->page, $room_count, $LOG_CONF);
  $builder->set_reverse = $is_reverse;
  $builder->AddOption('reverse', $is_reverse ? 'on' : 'off');
  if($RQ_ARGS->add_role) $builder->AddOption('add_role');
  $builder->Output();
  echo <<<EOF
</td></tr>
<tr><td>
<table class="main">
<tr><th>村No</th><th>村名</th><th>人数</th><th>日数</th><th>勝</th></tr>

EOF;

  //全部表示の場合、一ページで全部表示する。それ以外は設定した数ごと表示
  $query = "SELECT room_no FROM room WHERE status = 'finished' ORDER BY room_no";
  if($is_reverse) $query .=  ' DESC';
  if($RQ_ARGS->page != 'all'){
    $query .= sprintf(' LIMIT %d, %d', $LOG_CONF->view * ($RQ_ARGS->page - 1), $LOG_CONF->view);
  }
  $room_no_list = FetchArray($query);

  $VICT_IMG =& new VictoryImage();
  $ROOM_DATA =& new RoomDataSet();
  foreach($room_no_list as $room_no){
    $ROOM = $ROOM_DATA->LoadFinishedRoom($room_no);

    $base_url = 'old_log.php?room_no=' . $ROOM->id;
    if($RQ_ARGS->add_role) $base_url .= '&add_role=on';
    $dead_room = $ROOM->date == 0 ? ' style="color:silver"' : ''; //廃村の場合、色を灰色にする
    //$max_user_str = $ROOM_IMG->max_user_list[$ROOM->max_user]; //ユーザ総数画像
    $game_option_str = GenerateGameOptionImage($ROOM->game_option, $ROOM->option_role);
    $establish_time = $ROOM->establish_time == '' ? '' : ConvertTimeStamp($ROOM->establish_time);
    $login = ($current_time - strtotime($ROOM->finish_time) > $ROOM_CONF->clear_session_id ? '' :
	      '<a href="login.php?room_no=' . $ROOM->id . '"' . $dead_room . ">[再入村]</a>\n");

    echo <<<EOF
<tr class="list">
<td class="number" rowspan="3">{$ROOM->id}</td>
<td class="title"><a href="{$base_url}"{$dead_room}>{$ROOM->name} 村</a>
<td class="upper">{$ROOM->user_count} (最大{$ROOM->max_user})</td>
<td class="upper">{$ROOM->date}</td>
<td class="side">{$VICT_IMG->Generate($ROOM->victory_role)}</td>
</tr>
<tr class="list middle">
<td class="comment side">〜 {$ROOM->comment} 〜</td>
<td class="time comment" colspan="3">{$establish_time}</td>
</tr>
<tr class="lower list">
<td class="comment">
{$login}(
<a href="{$base_url}&reverse_log=on"{$dead_room}>逆</a>
<a href="{$base_url}&heaven_talk=on"{$dead_room}>霊</a>
<a href="{$base_url}&reverse_log=on&heaven_talk=on"{$dead_room}>逆&amp;霊</a>
<a href="{$base_url}&heaven_only=on"{$dead_room} >逝</a>
<a href="{$base_url}&reverse_log=on&heaven_only=on"{$dead_room}>逆&amp;逝</a>
)
</td>
<td colspan="3">{$game_option_str}</td>
</tr>

EOF;
  }

  echo <<<EOF
</table>
</td></tr>
</table>
</div>

EOF;
}

//指定の部屋番号のログを出力する
function OutputOldLog(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  //変数をセット
  $base_title = $SERVER_CONF->title . ' [過去ログ]';
  $url = "<br>\n<a href=\"old_log.php\">←戻る</a>\n";

  if(! $ROOM->IsFinished() || ! $ROOM->IsAfterGame()){
    OutputActionResult($base_title, 'まだこの部屋のログは閲覧できません。' . $url);
  }
  $title = '[' . $ROOM->id . '番地] ' . $ROOM->name . ' - ' . $base_title;

  //戻る先を前のページにする
  $referer_url = sprintf("%s", $_SERVER['HTTP_REFERER']);
  if(strpos($referer_url, $SERVER_CONF->site_root . 'old_log.php') === 0){
    $referer = $referer_url;
  }
  else{
    $referer = 'old_log.php';
  }

  OutputHTMLHeader($title, 'old_log');
  echo <<<EOF
</head>
<body>
<a href="{$referer}">←戻る</a><br>
<div class="room"><span>{$ROOM->name}村</span> 〜{$ROOM->comment}〜 [{$ROOM->id}番地]</td></div>

EOF;
  OutputPlayerList(); //プレイヤーリストを出力
  $RQ_ARGS->heaven_only ? LayoutHeaven() : LayoutTalkLog();
}

//通常のログ表示順を表現します。
function LayoutTalkLog(){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->reverse_log){
    OutputDateTalkLog(0, 'beforegame');
    for($i = 1; $i <= $ROOM->last_date; $i++){
      OutputDateTalkLog($i, '');
    }
    OutputVictory();
    OutputDateTalkLog($ROOM->last_date, 'aftergame');
  }
  else{
    OutputDateTalkLog($ROOM->last_date, 'aftergame');
    OutputVictory();
    for($i = $ROOM->last_date; $i > 0; $i--){
      OutputDateTalkLog($i, '');
    }
    OutputDateTalkLog(0, 'beforegame');
  }
}

//霊界のみのログ表示順を表現します。
function LayoutHeaven(){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->reverse_log){
    for($i = 1; $i <= $ROOM->last_date; $i++){
      OutputDateTalkLog($i, 'heaven_only');
    }
  }
  else{
    for($i = $ROOM->last_date; $i > 0; $i--){
      OutputDateTalkLog($i, 'heaven_only');
    }
  }
}

//指定の日付の会話ログを出力
function OutputDateTalkLog($set_date, $set_location){
  global $RQ_ARGS, $ROLES, $ROOM;

  if($RQ_ARGS->reverse_log) //逆順、初日から最終日まで
    $select_order = 'ORDER BY talk_id';
  else //最終日から初日まで
    $select_order = 'ORDER BY talk_id DESC';

  switch($set_location){
  case 'beforegame':
  case 'aftergame':
    $table_class = $set_location;
    $date_select = '';
    $location_select = "AND location LIKE '$set_location%'";
    break;

  default:
    //二日目以降は昼から始まる
    $table_class = ($RQ_ARGS->reverse_log && $set_date != 1) ? 'day' : 'night';
    $date_select = "AND date = $set_date";
    if($set_location == 'heaven_only')
      $location_select = "AND (location = 'heaven' OR uname = 'system')";
    else
      $location_select = "AND location <> 'aftergame' AND location <> 'beforegame'";
    break;
  }

  $flag_border_game = false;
  //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
  $query = "SELECT uname, sentence, font_type, location FROM talk WHERE room_no = {$ROOM->id} AND ";
  if($set_location == 'heaven_only'){
    $query .= "date = $set_date AND (location = 'heaven' OR uname = 'system')";
  }
  elseif($set_location == 'beforegame' || $set_location == 'aftergame'){
    $query .= "location like '$set_location%'";
  }
  else{
    $flag_border_game = true;
    $query .= "date = $set_date AND location <> 'beforegame' AND location <> 'aftergame'";
    if(! $RQ_ARGS->heaven_talk) $query .= " AND location <> 'heaven'";
  }
  $talk_list = FetchObject($query . ' ' . $select_order, 'Talk');

  //-- 仮想稼動モードテスト用 --//
  //global $USERS, $SELF;
  //$SELF = $USERS->rows[3];
  //$SELF->ParseRoles('human earplug');
  //$SELF->live = 'live';
  //$ROOM->status = 'playing';
  //$ROOM->option_list[] = 'not_open_cast';

  if($flag_border_game && ! $RQ_ARGS->reverse_log && $set_date != $ROOM->last_date){
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputLastWords(); //遺言を出力
    OutputDeadMan();   //死亡者を出力
  }
  $ROOM->date = $set_date;
  $ROOM->day_night = $table_class;

  //出力
  $builder =& new DocumentBuilder();
  $builder->BeginTalk('talk ' . $table_class);
  if($RQ_ARGS->reverse_log) OutputTimeStamp($builder);

  foreach($talk_list as $talk){
    switch($talk->scene){
    case 'day':
      if($ROOM->IsDay()) break;
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = $talk->scene;
      $builder->BeginTalk('talk ' . $talk->scene);
      break;

    case 'night':
      if($ROOM->IsNight()) break;
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = $talk->scene;
      $builder->BeginTalk('talk ' . $talk->scene);
      break;
    }
    OutputTalk($talk, &$builder); //会話出力
  }

  if(! $RQ_ARGS->reverse_log) OutputTimeStamp($builder);
  $builder->EndTalk();

  if($flag_border_game && $RQ_ARGS->reverse_log){
    if($set_date == $ROOM->last_date && $ROOM->IsDay()){
      OutputVoteList(); //突然死で勝敗が決定したケース
    }
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputDeadMan();   //死亡者を出力
    OutputLastWords(); //遺言を出力
  }
}

//シーン切り替え時のログ出力
function OutputSceneChange($set_date){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->heaven_only) return;
  $ROOM->date = $set_date;
  if($RQ_ARGS->reverse_log){
    $ROOM->day_night = 'night';
    OutputVoteList(); //投票結果出力
    OutputDeadMan();  //死亡者を出力
  }
  else{
    OutputDeadMan();  //死亡者を出力
    OutputVoteList(); //投票結果出力
  }
}
