<?php
require_once(dirname(__FILE__) . '/include/init.php');
loadModule(
  CONFIG,
  GAME_FORMAT_CLASSES,
  ROLE_CLASSES,
  SYSTEM_CLASSES,
  USER_CLASSES,
  TALK_CLASSES,
  GAME_FUNCTIONS,
  PLAY_FUNCTIONS,
  ROOM_IMG,
  GAME_CONF,
  ICON_CONF,
  MESSAGE
  );

//部屋No取得
$RQ_ARGS = new LogView();
$room_no = $RQ_ARGS->room_no;

$dbHandle = ConnectDatabase(); //DB 接続
if($RQ_ARGS->is_room){
  $USERS = new UserDataSet($RQ_ARGS);
  $SELF  = new User();
  OutputOldLog();
}
else{
  OutputFinishedRooms($RQ_ARGS->page, $RQ_ARGS->reverse);
}
DisconnectDatabase($dbHandle); //DB 接続解除

OutputHTMLFooter();

// 関数 //
//過去ログ一覧表示
function OutputFinishedRooms($page, $reverse = NULL){
  global $SERVER_CONF, $MESSAGE, $ROOM_IMG, $RQ_ARGS;

  //村数の確認
  $sql = mysql_query("SELECT COUNT(*) FROM room WHERE status = 'finished'");
  $num_rooms = mysql_result($sql, 0);
  if($num_rooms == 0){
    OutputActionResult($SERVER_CONF->title . ' [過去ログ]', 'ログはありません。<br>' . "\n" .
		       '<a href="index.php">←戻る</a>'."\n");
  }

  OutputHTMLHeader($SERVER_CONF->title . ' [過去ログ]', 'old_log_list');
echo <<<EOF
</head>
<body id="room_list">
<p><a href="index.php">←戻る</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table><tr><td class="list">
[ページ]

EOF;

  $config = new OldLogConfig(); //設定をロード
  $is_reverse = (empty($reverse) ? $config->reverse : ($reverse == 'on'));

  //ページリンクの出力
  if($page == NULL) $page = 1;
  $num_pages = ceil($num_rooms / $config->one_page) + 1; //[all] の為に + 1 しておく
  $url_option = '&reverse='.($is_reverse ? 'on' : 'off');
  if($RQ_ARGS->add_role) $url_option .= '&add_role=on';
  for($page_number = 1; $page_number <= $num_pages; $page_number++){
    $page_title = ($page_number == $num_pages ? 'all' : $page_number);
    if($page == $page_title)
      echo " [$page_title] ";
    else
      echo " <a href=\"old_log.php?page=$page_title$url_option\">[$page_title]</a> ";
  }
  $reverse_text = ($is_reverse xor $config->reverse) ? '元に戻す' : '入れ替える';
  $base_url = 'old_log.php?'.($RQ_ARGS->add_role ? '&add_role=on' : '').'&reverse=';
  if($is_reverse)
    echo '表示順:新↓古 <a href="'.$base_url.'off">'.$reverse_text.'</a>';
  else
    echo '表示順:古↓新 <a href="'.$base_url.'on">'.$reverse_text.'</a>';

  $game_option_list = array('dummy_boy', 'open_vote', 'not_open_cast', 'decide',
			    'authority', 'poison', 'cupid', 'boss_wolf', 'poison_wolf',
			    'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			    'chaos', 'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

  echo <<<EOF
</td></tr>
<!--村一覧 ここから-->
<tr><td>
<table class="main">
<tr><th>村No</th><th>村名</th><th>村について</th><th>人数</th><th>日数</th><th>勝</th></tr>

EOF;

  //全部表示の場合、一ページで全部表示する。それ以外は設定した数ごと表示
  if($page == 'all')
    $limit_statement = '';
  else{
    $start_number = $config->one_page * ($page - 1);
    $limit_statement = sprintf('LIMIT %d, %d', $start_number, $config->one_page);
  }

  //表示する行の取得
  $room_order = ($is_reverse ? 'DESC' : '');
  $sql = mysql_query("SELECT room_no, room_name, room_comment, date AS room_date,
			game_option AS room_game_option, option_role AS room_option_role,
			max_user AS room_max_user, (SELECT COUNT(*) FROM user_entry user
			WHERE user.room_no = room.room_no AND user.user_no > 0)
			AS room_num_user, victory_role AS room_victory_role FROM room
			WHERE status = 'finished' ORDER BY room_no $room_order $limit_statement");

  $victory_img = new VictoryImage();
  while(($array = mysql_fetch_assoc($sql)) !== false){
    extract($array, EXTR_PREFIX_ALL, 'log');

    //オプションと勝敗の解析
    $game_option_str = MakeGameOptionImage($log_room_game_option, $log_room_option_role);
    $victory_role_str = $victory_img->MakeVictoryImage($log_room_victory_role);
    if($log_room_date == 0) //廃村の場合、色を灰色にする
      $dead_room_color = ' style="color:silver"';
    else
      $dead_room_color = '';

    //ユーザ総数を取得
    // $str_max_users = $ROOM_IMG->max_user_list[$log_room_max_user];
    $user_count = intval($log_room_num_user);

    $base_url = "old_log.php?room_no=$log_room_no";
    if($RQ_ARGS->add_role) $base_url .= '&add_role=on';

    /*
    if ($DEBUG_MODE){
      $debug_anchor = "<a href=\"$base_url&debug=on\" $dead_room_color >録</a>";
    }
    */

    echo <<<EOF
<tr class="list">
<td class="number" rowspan="2">$log_room_no</td>
<td class="title"><a href="$base_url" $dead_room_color>$log_room_name 村</a></td>
<td class="upper comment">〜 $log_room_comment 〜</td>
<td class="upper">$user_count (最大{$log_room_max_user})</td>
<td class="upper">$log_room_date</td>
<td class="side">$victory_role_str</td>
</tr>
<tr class="lower list">
<td class="comment">(
<a href="$base_url&reverse_log=on" $dead_room_color>逆</a>
<a href="$base_url&heaven_talk=on" $dead_room_color>霊</a>
<a href="$base_url&reverse_log=on&heaven_talk=on" $dead_room_color>逆&amp;霊</a>
<a href="$base_url&heaven_only=on" $dead_room_color >逝</a>
<a href="$base_url&reverse_log=on&heaven_only=on" $dead_room_color>逆&amp;逝</a>
$debug_anchor
)
</td>
<td colspan="4">$game_option_str</td>
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


//指定の部屋Noのログを出力する
function OutputOldLog(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM, $room_no, $last_date;

  $base_title = $SERVER_CONF->title . ' [過去ログ]';
  $url = "<br>\n<a href=\"old_log.php\">←戻る</a>\n";

  if($room_no == NULL) OutputActionResult($title, '村を指定してください。' . $url);

  //日付とシーンを取得
  $ROOM = new RoomDataSet($RQ_ARGS);
  $ROOM->log_mode = true;
  $last_date = $ROOM->date;

  if(! $ROOM->IsFinished() || ! $ROOM->IsAfterGame()){
    OutputActionResult($base_title, 'まだこの部屋のログは閲覧できません。' . $url);
  }

  $title = '[' . $room_no . '番地] ' . $ROOM->name . ' - ' . $base_title;

  //戻る先を前のページにする
  $referer_page_str = strstr($_SERVER['HTTP_REFERER'], 'page');
  sscanf($referer_page_str, "page=%s", &$referer_page);

  OutputHTMLHeader($title, 'old_log');
  echo <<<EOF
</head>
<body>
<a href="old_log.php?page=$referer_page">←戻る</a><br>
<div class="room"><span>{$ROOM->name}村</span>　〜{$ROOM->comment}〜 [{$room_no}番地]</td></div>

EOF;
  OutputPlayerList();   //プレイヤーリストを出力

  $layout = 'Layout'.($RQ_ARGS->heaven_only ? 'Heaven' : 'TalkLog');
  $layout($last_date, $RQ_ARGS->reverse_log);
}

//通常のログ表示順を表現します。
function LayoutTalkLog($last_date, $is_reverse){
  if($is_reverse){
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
    for($i = 1; $i <= $last_date; $i++){
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputVictory();
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
  }
  else{
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
    OutputVictory();
    for($i = $last_date; $i > 0; $i--){
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
  }
}

//霊界のみのログ表示順を表現します。
function LayoutHeaven($last_date, $is_reverse){
  if($is_reverse){
    for($i = 1; $i <= $last_date; $i++){
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
  else{
    for($i = $last_date; $i > 0; $i--){
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
}

//指定の日付の会話ログを出力
function OutputDateTalkLog($set_date, $set_location, $is_reverse){
  global $RQ_ARGS, $ROLES, $room_no, $ROOM, $last_date;

  if($is_reverse) //逆順、初日から最終日まで
    $select_order = 'ORDER BY time';
  else //最終日から初日まで
    $select_order = 'ORDER BY time DESC';

  switch($set_location){
  case 'beforegame':
  case 'aftergame':
    $table_class = $set_location;
    $date_select = '';
    $location_select = "AND location LIKE '$set_location%'";
    break;

  default:
    //二日目以降は昼から始まる
    $table_class = ($is_reverse && $set_date != 1) ? 'day' : 'night';
    $date_select = "AND date = $set_date";
    if($set_location == 'heaven_only')
      $location_select = "AND (location = 'heaven' OR uname = 'system')";
    else
      $location_select = "AND location <> 'aftergame' AND location <> 'beforegame'";
    break;
  }

  $flag_border_game = false;
  if($set_location == 'heaven_only'){
    //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
    $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = $room_no AND date = $set_date
			AND (location = 'heaven' OR uname = 'system') $select_order");
  }
  elseif($set_location == 'beforegame' || $set_location == 'aftergame'){
    //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
    $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = $room_no AND location like '$set_location%'
			$select_order");
  }
  else{
    $flag_border_game = true;
    $hide_heaven_query = ($RQ_ARGS->heaven_talk ? '' : "AND location <> 'heaven'");
    //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
    $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = $room_no AND date = $set_date
			AND location <> 'aftergame' AND location <> 'beforegame'
			$hide_heaven_query $select_order");
  }

  if($flag_border_game && ! $is_reverse && $set_date != $last_date){
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputLastWords(); //遺言を出力
    OutputDeadMan();   //死亡者を出力
  }
  $ROOM->day_night = $table_class;

  //出力
  $builder = DocumentBuilder::Generate();
  $builder->BeginTalk("old-log-talk {$table_class}");
  while(($talk = mysql_fetch_object($sql, 'Talk')) !== false){
    if(strpos($talk->location, 'day') !== false && ! $ROOM->IsDay()){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = 'day';
      $builder->BeginTalk('old-log-talk day');
    }
    elseif(strpos($talk->location, 'night') !== false && ! $ROOM->IsNight()){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = 'night';
      $builder->BeginTalk('old-log-talk night');
    }
    OutputTalk($talk, &$builder); //会話出力
  }
  $builder->EndTalk();

  if($flag_border_game && $is_reverse){
    if($set_date == $last_date && $ROOM->IsDay()){
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
?>