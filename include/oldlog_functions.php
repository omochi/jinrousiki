<?php
//-- クラス定義 --//
//-- ページ送りリンク生成クラス --//
class PageLinkBuilder {
  function __construct($file, $page, $count, $title = 'Page', $type = 'page'){
    $this->view_total = $count;
    $this->view_page  = OldlogConfig::$page;
    $this->view_count = OldlogConfig::$view;
    $this->reverse    = OldlogConfig::$reverse;

    $this->file   = $file;
    $this->url    = '<a href="' . $file . '.php?';
    $this->title  = $title;
    $this->type   = $type;
    $this->option = array();
    $this->SetPage($page);
  }

  //表示するページのアドレスをセット
  private function SetPage($page){
    $total = ceil($this->view_total / $this->view_count);
    $start = $page == 'all' ? 1 : $page;
    if ($total - $start < $this->view_page) { //残りページが少ない場合は表示開始位置をずらす
      $start = $total - $this->view_page + 1;
      if ($start < 1) $start = 1;
    }
    $end = $start + $this->view_page - 1;
    if ($end > $total) $end = $total;

    $this->page->set   = $page;
    $this->page->total = $total;
    $this->page->start = $start;
    $this->page->end   = $end;

    $this->limit = $page == 'all' ? '' : $this->view_count * ($page - 1);
    $this->query = $page == 'all' ? '' : sprintf(' LIMIT %d, %d', $this->limit, $this->view_count);
  }

  //オプションを追加する
  function AddOption($type, $value = 'on'){ $this->option[$type] = $type . '=' . $value; }

  //ページリンクを生成する
  function Generate(){
    $url_stack = array('[' . $this->title . ']');
    if ($this->file == 'index') $url_stack[] = '[<a href="index.html">new</a>]';
    if ($this->page->start > 1 && $this->page->total > $this->view_page){
      $url_stack[] = $this->GenerateTag(1, '[1]...');
      $url_stack[] = $this->GenerateTag($this->page->start - 1, '&lt;&lt;');
    }

    for ($i = $this->page->start; $i <= $this->page->end; $i++) {
      $url_stack[] = $this->GenerateTag($i);
    }

    if ($this->page->end < $this->page->total){
      $url_stack[] = $this->GenerateTag($this->page->end + 1, '&gt;&gt;');
      $url_stack[] = $this->GenerateTag($this->page->total, '...[' . $this->page->total . ']');
    }
    if ($this->file != 'index') $url_stack[] = $this->GenerateTag('all');

    if ($this->file == 'old_log'){
      $this->AddOption('reverse', $this->set_reverse ? 'off' : 'on');
      $url_stack[] = '[表示順]';
      $url_stack[] = $this->set_reverse ? '新↓古' : '古↓新';
      $name = ($this->set_reverse xor $this->reverse) ? '元に戻す' : '入れ替える';
      $url_stack[] =  $this->GenerateTag($this->page->set, $name, true);
    }
    return $this->header . implode(' ', $url_stack) . $this->footer;
  }

  //ページ送り用のリンクタグを作成する
  protected function GenerateTag($page, $title = null, $force = false){
    if ($page == $this->page->set && ! $force) return '[' . $page . ']';
    if (is_null($title)) $title = '[' . $page . ']';
    if ($this->file == 'index') {
      $footer = $page . '.html';
    }
    else {
      $list = $this->option;
      array_unshift($list, $this->type . '=' . $page);
      $footer = implode('&', $list);
    }
    return $this->url . $footer . '">' . $title . '</a>';
  }

  //ページリンクを出力する
  function Output(){ echo $this->Generate(); }
}

//-- 関数 --//
//過去ログ一覧生成
function GenerateFinishedRooms($page){
  //村数の確認
  $title = ServerConfig::$title . ' [過去ログ]';
  $query = "SELECT room_no FROM room WHERE status = 'finished'";
  $room_count = DB::Count($query);
  if ($room_count < 1){
    HTML::OutputResult($title, 'ログはありません。<br>'."\n" . '<a href="./">←戻る</a>'."\n");
  }

  //ページリンクデータの生成
  $is_reverse = empty(RQ::$get->reverse) ? OldLogConfig::$reverse : (RQ::$get->reverse == 'on');
  if (RQ::$get->generate_index) {
    $max = RQ::$get->max_room_no;
    if (is_int($max) && $max > 0 && $room_count > $max) $room_count = $max;
    $builder = new PageLinkBuilder('index', RQ::$get->page, $room_count);
    $builder->set_reverse = $is_reverse;
    $builder->url = '<a href="index';
  }
  else {
    $builder = new PageLinkBuilder('old_log', RQ::$get->page, $room_count);
    $builder->set_reverse = $is_reverse;
    $builder->AddOption('reverse', $is_reverse     ? 'on' : 'off');
    $builder->AddOption('watch',   RQ::$get->watch ? 'on' : 'off');
    $db_no = RQ::$get->db_no;
    if (is_int($db_no) && $db_no > 0) $builder->AddOption('db_no', $db_no);
  }

  $back_url = RQ::$get->generate_index ? '../' : './';
  $img_url  = RQ::$get->generate_index ? '../' : '';

  $str = HTML::GenerateHeader($title, 'old_log_list') . <<<EOF
</head>
<body>
<p><a href="{$back_url}">←戻る</a></p>
<img src="{$img_url}img/old_log_title.jpg"><br>
<div>
<table>
<caption>{$builder->Generate()}</caption>
<thead>
<tr><th>村No</th><th>村名</th><th>人数</th><th>日数</th><th>勝</th></tr>
</thead>
<tbody>

EOF;

  //全部表示の場合、一ページで全部表示する。それ以外は設定した数毎に表示
  $current_time = Time::Get(); // 現在時刻の取得
  $query .= ' ORDER BY room_no ' . ($is_reverse ? 'DESC' : 'ASC');
  if (RQ::$get->page != 'all'){
    $view = OldLogConfig::$view;
    $query .= sprintf(' LIMIT %d, %d', $view * (RQ::$get->page - 1), $view);
  }
  foreach (DB::FetchArray($query) as $room_no) {
    $ROOM = RoomDataSet::LoadFinishedRoom($room_no);

    $dead_room = $ROOM->date == 0 ? ' vanish' : ''; //廃村の場合、色を灰色にする
    $establish = $ROOM->establish_datetime == '' ? '' :
      Time::ConvertTimeStamp($ROOM->establish_datetime);
    if (RQ::$get->generate_index) {
      $base_url = $ROOM->id . '.html';
      $login    = '';
      $log_link = '(<a href="' .  $ROOM->id . 'r.html">逆</a>)';
    }
    else {
      $base_url = 'old_log.php?room_no=' . $ROOM->id;
      if (is_int(RQ::$get->db_no) && RQ::$get->db_no > 0) $base_url .= '&db_no=' . RQ::$get->db_no;
      $login = $current_time - strtotime($ROOM->finish_datetime) > RoomConfig::$clear_session_id ?
	'' : '<a href="login.php?room_no=' . $ROOM->id . '"' . $dead_room . ">[再入村]</a>\n";
      $log_link = GenerateLogLink($base_url, true, '(') . ' )' .
	GenerateLogLink($base_url . '&add_role=on', false, "\n[役職表示] (", $dead_room) . ' )';
    }
    $max_user    = Image::GenerateMaxUser($ROOM->max_user);
    $game_option = RoomOption::Wrap($ROOM->game_option, $ROOM->option_role)->GenerateImageList();
    $winner      = RQ::$get->watch ? '-' : Image::Winner()->Generate($ROOM->winner);
    $str .= <<<EOF
<tr>
<td class="number" rowspan="3">{$ROOM->id}</td>
<td class="title{$dead_room}"><a href="{$base_url}">{$ROOM->name} 村</a></td>
<td class="upper">{$ROOM->user_count} {$max_user}</td>
<td class="upper">{$ROOM->date}</td>
<td class="side">{$winner}</td>
</tr>
<tr class="list middle">
<td class="comment side">～{$ROOM->comment}～</td>
<td class="time comment" colspan="3">{$establish}</td>
</tr>
<tr class="lower list">
<td class="comment{$dead_room}">
{$login}{$log_link}
</td>
<td colspan="3">{$game_option}</td>
</tr>

EOF;
  }

  return $str . <<<EOF
</tbody>
</table>
</div>

EOF;
}

//過去ログ一覧表示
function OutputFinishedRooms($page){ echo GenerateFinishedRooms($page); }

//過去ログ一覧のHTML化処理
function GenerateLogIndex(){
  RQ::$get->reverse = 'off';
  if (RQ::$get->max_room_no < 1) return false;
  $header = sprintf('../log/%sindex', RQ::$get->prefix);
  $footer = '</body></html>'."\n";
  $end_page = ceil((RQ::$get->max_room_no - RQ::$get->min_room_no + 1) / OldLogConfig::$view);
  for ($i = 1; $i <= $end_page; $i++) {
    RQ::$get->page = $i;
    $index = RQ::$get->index_no - $i + 1;
    file_put_contents("{$header}{$index}.html",  GenerateFinishedRooms($i) . $footer);
  }
}

//指定の部屋番号のログを生成する
function GenerateOldLog(){
  $base_title = ServerConfig::$title . ' [過去ログ]'; //
  if (! DB::$ROOM->IsFinished() || ! DB::$ROOM->IsAfterGame()){ //閲覧判定
    $url = RQ::$get->generate_index ? 'index.html' : 'old_log.php';
    $str = 'まだこの部屋のログは閲覧できません。<br>'."\n".'<a href="'.$url.'">←戻る</a>'."\n";
    HTML::OutputResult($base_title, $str);
  }
  if (DB::$ROOM->watch_mode) {
    DB::$ROOM->status    = 'playing';
    DB::$ROOM->scene = 'day';
  }
  $title  = sprintf('[%d番地] %s - %s', DB::$ROOM->id, DB::$ROOM->name, $base_title);
  $option = RoomOption::Wrap(DB::$ROOM->game_option->row, DB::$ROOM->option_role->row)->GenerateImageList();
  $log    = GeneratePlayerList() . (RQ::$get->heaven_only ? LayoutHeaven() : LayoutTalkLog());
  $link = '<a href="#beforegame">前</a>'."\n";
  for($i = 1; $i <= DB::$ROOM->last_date ; $i++) $link .= '<a href="#date'.$i.'">'.$i.'</a>'."\n";
  $link .= '<a href="#aftergame">後</a>'."\n";
  $str = DB::$ROOM->GenerateTitleTag();
  return HTML::GenerateHeader($title, 'old_log', true) . <<<EOF
<a href="old_log.php">←戻る</a><br>
{$str}<br>
{$option}<br>
{$link}<br>
{$log}
EOF;
}

//指定の部屋番号のログを出力する
function OutputOldLog(){ echo GenerateOldLog(); }

//通常のログ表示順を表現します。
function LayoutTalkLog(){
  if (RQ::$get->reverse_log){
    $str = GenerateDateTalkLog(0, 'beforegame');
    for($i = 1; $i <= DB::$ROOM->last_date; $i++) $str .= GenerateDateTalkLog($i, '');
    $str .= GenerateWinner() . GenerateDateTalkLog(DB::$ROOM->last_date, 'aftergame');
  }
  else {
    $str = GenerateDateTalkLog(DB::$ROOM->last_date, 'aftergame') . GenerateWinner();
    for($i = DB::$ROOM->last_date; $i > 0; $i--) $str .= GenerateDateTalkLog($i, '');
    $str .= GenerateDateTalkLog(0, 'beforegame');
  }
  return $str;
}

//霊界のみのログ表示順を表現します。
function LayoutHeaven(){
  $str = '';
  if (RQ::$get->reverse_log) {
    for ($i = 1; $i <= DB::$ROOM->last_date; $i++) $str .= GenerateDateTalkLog($i, 'heaven_only');
  }
  else {
    for ($i = DB::$ROOM->last_date; $i > 0; $i--)  $str .= GenerateDateTalkLog($i, 'heaven_only');
  }
  return $str;
}

//指定の日付の会話ログを生成
function GenerateDateTalkLog($set_date, $set_scene){
  global $ROLES;

  //シーンに合わせた会話ログを取得するためのクエリを生成
  $flag_border_game = false;
  $query_select = 'scene, location, uname, action, sentence, font_type';
  $query_table  = 'talk';
  $query_where  = sprintf('room_no = %d AND ', DB::$ROOM->id);
  if (RQ::$get->time) $query_select .= ', time';

  switch ($set_scene){
  case 'beforegame':
    $table_class = $set_scene;
    $query_table  .= '_' . $set_scene;
    $query_select .= ', handle_name, color';
    $query_where  .= "scene = '{$set_scene}'";
    if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode){
      DB::$USER->ResetRoleList();
      unset(DB::$ROOM->event);
    }
    if (! RQ::$get->reverse_log) DB::$USER->ResetPlayer(); //player 復元処理
    break;

  case 'aftergame':
    $table_class = $set_scene;
    $query_table .= '_' . $set_scene;
    $query_where .= "scene = '{$set_scene}'";
    if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode){
      DB::$USER->ResetRoleList();
      unset(DB::$ROOM->event);
    }
    if (RQ::$get->reverse_log) DB::$USER->ResetPlayer(); //player 復元処理
    break;

  case 'heaven_only':
    $table_class = RQ::$get->reverse_log && $set_date != 1 ? 'day' : 'night'; //2日目以降は昼から
    $query_where .= "date = {$set_date} AND (scene = 'heaven' OR uname = 'system')";
    break;

  default:
    $flag_border_game = true;
    $table_class = RQ::$get->reverse_log && $set_date != 1 ? 'day' : 'night'; //2日目以降は昼から
    $query_select .= ', role_id';
    $scene_list = array("'day'", "'night'");
    if (RQ::$get->heaven_talk) $scene_list[] = "'heaven'";
    $query_where .= "date = {$set_date} AND scene IN (" . implode(',', $scene_list) . ')';
    if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode){
      DB::$USER->ResetRoleList();
      DB::$USER->SetEvent(true);
    }
    break;
  }
  if (DB::$ROOM->personal_mode) $query_where .= " AND uname = 'system'"; //個人結果表示モード
  $query = "SELECT {$query_select} FROM {$query_table} WHERE {$query_where}";
  $query .= ' ORDER BY id' . (RQ::$get->reverse_log ? '' : ' DESC'); //ログの表示順

  //PrintData($query, $set_scene);
  $talk_list = DB::FetchObject($query, 'Talk');

  //-- 仮想稼動モードテスト用 --//
  //DB::$SELF = DB::$USER->rows[3];
  //DB::$SELF->ParseRoles('human earplug');
  //DB::$SELF->live = 'live';
  //DB::$ROOM->status = 'playing';
  //DB::$ROOM->option_list[] = 'not_open_cast';

  //出力
  $str = '';
  if ($flag_border_game && ! RQ::$get->reverse_log){
    DB::$ROOM->date = $set_date + 1;
    DB::$ROOM->scene = 'day';
    $str .= GenerateLastWords() . GenerateDeadMan();//死亡者を出力
  }
  DB::$ROOM->date = $set_date;
  DB::$ROOM->scene = $table_class;
  if ($set_scene != 'heaven_only') DB::$ROOM->SetWeather();

  $builder = new DocumentBuilder();
  $id = DB::$ROOM->IsPlaying() ? 'date' . DB::$ROOM->date : DB::$ROOM->scene;
  $builder->BeginTalk('talk ' . $table_class, $id);
  if (RQ::$get->reverse_log) OutputTimeStamp($builder);
  //if (DB::$ROOM->watch_mode) $builder->AddSystemTalk(DB::$ROOM->date . print_r(DB::$ROOM->event, true));

  foreach ($talk_list as $talk){
    switch ($talk->scene){
    case 'day':
      if (DB::$ROOM->IsDay() || $talk->type == 'dummy_boy') break;
      $str .= $builder->RefreshTalk() . GenerateSceneChange($set_date);
      DB::$ROOM->scene = $talk->scene;
      $builder->BeginTalk('talk ' . $talk->scene);
      break;

    case 'night':
      if (DB::$ROOM->IsNight() || $talk->type == 'dummy_boy') break;
      $str .= $builder->RefreshTalk() . GenerateSceneChange($set_date);
      DB::$ROOM->scene = $talk->scene;
      $builder->BeginTalk('talk ' . $talk->scene);
      break;
    }
    OutputTalk($talk, $builder); //会話出力
  }

  if (! RQ::$get->reverse_log) OutputTimeStamp($builder);
  $str .= $builder->RefreshTalk();

  if ($flag_border_game && RQ::$get->reverse_log){
    //突然死で勝敗が決定したケース
    if ($set_date == DB::$ROOM->last_date && DB::$ROOM->IsDay()) $str .= GenerateVoteResult();

    DB::$ROOM->date = $set_date + 1;
    DB::$ROOM->scene = 'day';
    $str .= GenerateDeadMan() . GenerateLastWords(); //遺言を出力
  }
  return $str;
}

//指定の日付の会話ログを出力
function OutputDateTalkLog($set_date, $set_scene){
  echo GenerateDateTalkLog($set_date, $set_scene);
}

//シーン切り替え時のログ出力
function GenerateSceneChange($set_date){
  $str = '';
  if (RQ::$get->heaven_only) return $str;
  DB::$ROOM->date = $set_date;
  if (RQ::$get->reverse_log){
    DB::$ROOM->scene = 'night';
    $str .= GenerateVoteResult() . GenerateDeadMan();
  }
  else {
    $str .= GenerateDeadMan() . GenerateVoteResult();
  }
  return $str;
}
