<?php
//-- ページ送りリンク生成クラス --//
class PageLinkBuilder {
  function __construct($file, $page, $count, $title = 'Page', $type = 'page') {
    $this->view_total = $count;
    $this->view_page  = OldLogConfig::PAGE;
    $this->view_count = OldLogConfig::VIEW;
    $this->reverse    = OldLogConfig::REVERSE;

    $this->file   = $file;
    $this->url    = '<a href="' . $file . '.php?';
    $this->title  = $title;
    $this->type   = $type;
    $this->option = array();
    $this->SetPage($page);
  }

  //表示するページのアドレスをセット
  private function SetPage($page) {
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
  function AddOption($type, $value = 'on') {
    $this->option[$type] = $type . '=' . $value;
  }

  //ページリンクを生成する
  function Generate() {
    $url_stack = array('[' . $this->title . ']');
    if ($this->file == 'index') $url_stack[] = '[<a href="index.html">new</a>]';
    if ($this->page->start > 1 && $this->page->total > $this->view_page) {
      $url_stack[] = $this->GenerateTag(1, '[1]...');
      $url_stack[] = $this->GenerateTag($this->page->start - 1, '&lt;&lt;');
    }

    for ($i = $this->page->start; $i <= $this->page->end; $i++) {
      $url_stack[] = $this->GenerateTag($i);
    }

    if ($this->page->end < $this->page->total) {
      $url_stack[] = $this->GenerateTag($this->page->end + 1, '&gt;&gt;');
      $url_stack[] = $this->GenerateTag($this->page->total, '...[' . $this->page->total . ']');
    }
    if ($this->file != 'index') $url_stack[] = $this->GenerateTag('all');

    if ($this->file == 'old_log') {
      $this->AddOption('reverse', $this->set_reverse ? 'off' : 'on');
      $url_stack[] = '[表示順]';
      $url_stack[] = $this->set_reverse ? '新↓古' : '古↓新';
      $name = ($this->set_reverse xor $this->reverse) ? '元に戻す' : '入れ替える';
      $url_stack[] =  $this->GenerateTag($this->page->set, $name, true);
      if (RQ::Get()->watch) {
	$this->AddOption('reverse', $this->set_reverse ? 'on' : 'off');
	$this->AddOption('watch', 'off');
	$url_stack[] = $this->GenerateTag($this->page->set, '勝敗表示', true);
      }
    }
    return implode(' ', $url_stack);
  }

  //ページ送り用のリンクタグを作成する
  protected function GenerateTag($page, $title = null, $force = false) {
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
  function Output() { echo $this->Generate(); }
}

//-- HTML 生成クラス (OldLog 拡張) --//
class OldLogHTML {
  const BACK_URL    = "<br>\n<a href=\"%s\">←戻る</a>\n";
  const TITLE       = '[%d番地] %s - %s';
  const DATE_BEFORE = "<a href=\"#beforegame\">前</a>\n";
  const DATE_LINK   = "<a href=\"#date%d\">%d</a>\n";
  const DATE_AFTER  = "<a href=\"#aftergame\">後</a>\n";

  //指定の部屋番号のログを生成する
  static function Generate() {
    $base_title = ServerConfig::TITLE . ' [過去ログ]';
    if (! DB::$ROOM->IsFinished() || ! DB::$ROOM->IsAfterGame()) { //閲覧判定
      $url = RQ::Get()->generate_index ? 'index.html' : 'old_log.php';
      $str = 'まだこの部屋のログは閲覧できません。' . sprintf(self::BACK_URL, $url);
      HTML::OutputResult($base_title, $str);
    }
    if (DB::$ROOM->watch_mode) {
      DB::$ROOM->status = 'playing';
      DB::$ROOM->scene  = 'day';
    }

    if (CacheConfig::ENABLE && CacheConfig::ENABLE_OLD_LOG) { //キャッシュ取得判定
      DocumentCache::Load('old_log/' . print_r(RQ::Get(), true), CacheConfig::OLD_LOG_EXPIRE);
      $str = DocumentCache::GetData();
      if (isset($str)) return $str;
    }

    $list = array(
      'game_option' => DB::$ROOM->game_option->row,
      'option_role' => DB::$ROOM->option_role->row,
      'max_user'    => 0);
    RoomOption::Load($list);

    $title = sprintf(self::TITLE, DB::$ROOM->id, DB::$ROOM->name, $base_title);
    $br = Text::BR . Text::LF;

    $str  = HTML::GenerateHeader($title, 'old_log', true);
    $str .= '<a href="old_log.php">←戻る</a>';
    $str .= $br;
    $str .= DB::$ROOM->GenerateTitleTag(true);
    $str .= $br;
    $str .= RoomOption::GenerateImage();
    $str .= $br;
    $str .= self::DATE_BEFORE;
    for ($i = 1; $i <= DB::$ROOM->last_date; $i++) $str .= sprintf(self::DATE_LINK, $i, $i);
    $str .= self::DATE_AFTER;
    $str .= $br;
    $str .= GameHTML::GeneratePlayer();
    if (RQ::Get()->role_list) $str .= self::GenerateRoleLink();
    $str .= RQ::Get()->heaven_only ? self::GenerateHeavenLog() : self::GenerateLog();
    if (CacheConfig::ENABLE && CacheConfig::ENABLE_OLD_LOG) DocumentCache::Save($str);

    return $str;
  }

  //過去ログ一覧生成
  static function GenerateList($page) {
    //村数の確認
    $title = ServerConfig::TITLE . ' [過去ログ]';
    $room_count = RoomDataDB::GetFinishedCount();
    if ($room_count < 1) {
      HTML::OutputResult($title, 'ログはありません。' . sprintf(self::BACK_URL, './'));
    }

    //ページリンクデータの生成
    $is_reverse = empty(RQ::Get()->reverse) ? OldLogConfig::REVERSE : RQ::Get()->reverse == 'on';
    if (RQ::Get()->generate_index) {
      $max = RQ::Get()->max_room_no;
      if (is_int($max) && $max > 0 && $room_count > $max) $room_count = $max;
      $builder = new PageLinkBuilder('index', RQ::Get()->page, $room_count);
      $builder->set_reverse = $is_reverse;
      $builder->url = '<a href="index';
    }
    else {
      $builder = new PageLinkBuilder('old_log', RQ::Get()->page, $room_count);
      $builder->set_reverse = $is_reverse;
      $builder->AddOption('reverse', $is_reverse      ? 'on' : 'off');
      $builder->AddOption('watch',   RQ::Get()->watch ? 'on' : 'off');
      $db_no = RQ::Get()->db_no;
      if (is_int($db_no) && $db_no > 0) $builder->AddOption('db_no', $db_no);
    }

    $back_url = RQ::Get()->generate_index ? '../' : './';
    $img_url  = RQ::Get()->generate_index ? '../' : '';

    $str = HTML::GenerateHeader($title, 'old_log_list', true) . <<<EOF
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
    $current_time = Time::Get();
    foreach (RoomDataDB::GetFinished($is_reverse) as $room_no) {
      $ROOM = RoomDataDB::LoadFinished($room_no);

      $dead      = $ROOM->date == 0 ? ' vanish' : ''; //廃村判定
      $establish = $ROOM->establish_datetime == '' ? '' :
	Time::ConvertTimeStamp($ROOM->establish_datetime);
      if (RQ::Get()->generate_index) {
	$base_url = $ROOM->id . '.html';
	$login    = '';
	$log_link = '(<a href="' .  $ROOM->id . 'r.html">逆</a>)';
      }
      else {
	$base_url = 'old_log.php?room_no=' . $ROOM->id;
	if (is_int(RQ::Get()->db_no) && RQ::Get()->db_no > 0) {
	  $base_url .= '&db_no=' . RQ::Get()->db_no;
	}
	if (RQ::Get()->watch) $base_url .= '&watch=on';
	$login = $current_time - strtotime($ROOM->finish_datetime) > RoomConfig::KEEP_SESSION ? '' :
	  '<a href="login.php?room_no=' . $ROOM->id . '"' . $dead . ">[再入村]</a>\n";

	if (RQ::Get()->watch) {
	  $log_link = HTML::GenerateWatchLogLink($base_url, '(') . ' )';
	}
	else {
	  $log_link = HTML::GenerateLogLink($base_url, true, '(') . ' )';

	  $url = $base_url . '&add_role=on';
	  $log_link .= HTML::GenerateLogLink($url, false, "\n[役職表示] (", $dead) . ' )';
	}
      }
      $list = array(
        'game_option' => $ROOM->game_option,
	'option_role' => $ROOM->option_role,
	'max_user'    => $ROOM->max_user);
      RoomOption::Load($list);
      $max_user    = Image::GenerateMaxUser($ROOM->max_user);
      $game_option = RoomOption::GenerateImage();
      $winner      = RQ::Get()->watch ? '-' : Image::Winner()->Generate($ROOM->winner);
      $str .= <<<EOF
<tr>
<td class="number" rowspan="3"><a href="game_view.php?room_no={$ROOM->id}">{$ROOM->id}</a></td>
<td class="title{$dead}"><a href="{$base_url}">{$ROOM->name} 村</a></td>
<td class="upper">{$ROOM->user_count} {$max_user}</td>
<td class="upper">{$ROOM->date}</td>
<td class="side">{$winner}</td>
</tr>
<tr class="list middle">
<td class="comment side">～{$ROOM->comment}～</td>
<td class="time comment" colspan="3">{$establish}</td>
</tr>
<tr class="lower list">
<td class="comment{$dead}">
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

  //過去ログ一覧のHTML化処理
  static function GenerateIndex() {
    RQ::Set('reverse', 'off');
    if (RQ::Get()->max_room_no < 1) return false;
    $header = sprintf('../log/%sindex', RQ::Get()->prefix);
    $footer = '</body></html>'."\n";
    $end_page = ceil((RQ::Get()->max_room_no - RQ::Get()->min_room_no + 1) / OldLogConfig::VIEW);
    for ($i = 1; $i <= $end_page; $i++) {
      RQ::Set('page', $i);
      $index = RQ::Get()->index_no - $i + 1;
      file_put_contents("{$header}{$index}.html",  self::GenerateList($i) . $footer);
    }
  }

  //指定の部屋番号のログを出力する
  static function Output() { echo self::Generate(); }

  //過去ログ一覧表示
  static function OutputList($page) { echo self::GenerateList($page); }

  //通常ログ出力
  private static function GenerateLog() {
    if (RQ::Get()->reverse_log) {
      $str = self::GenerateTalk(0, 'beforegame');
      for ($i = 1; $i <= DB::$ROOM->last_date; $i++) {
	$str .= self::GenerateTalk($i, '');
      }
      $str .= Winner::Generate() . self::GenerateTalk(DB::$ROOM->last_date, 'aftergame');
    }
    else {
      $str = self::GenerateTalk(DB::$ROOM->last_date, 'aftergame') . Winner::Generate();
      for ($i = DB::$ROOM->last_date; $i > 0; $i--) {
	$str .= self::GenerateTalk($i, '');
      }
      $str .= self::GenerateTalk(0, 'beforegame');
    }
    return $str;
  }

  //霊界ログ出力
  private static function GenerateHeavenLog() {
    $str = '';
    if (RQ::Get()->reverse_log) {
      for ($i = 1; $i <= DB::$ROOM->last_date; $i++) {
	$str .= self::GenerateTalk($i, 'heaven_only');
      }
    }
    else {
      for ($i = DB::$ROOM->last_date; $i > 0; $i--) {
	$str .= self::GenerateTalk($i, 'heaven_only');
      }
    }
    return $str;
  }

  //指定の日付の会話ログを生成
  private static function GenerateTalk($date, $scene) {
    $flag_border_game = false;
    switch ($scene) { //シーンに合わせたデータをセット
    case 'beforegame':
      $table_class = $scene;
      if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode) {
	DB::$USER->ResetRoleList();
	unset(DB::$ROOM->event);
      }
      if (! RQ::Get()->reverse_log) DB::$USER->ResetPlayer(); //player 復元処理
      break;

    case 'aftergame':
      $table_class = $scene;
      if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode) {
	DB::$USER->ResetRoleList();
	unset(DB::$ROOM->event);
      }
      if (RQ::Get()->reverse_log) DB::$USER->ResetPlayer(); //player 復元処理
      break;

    case 'heaven_only':
      $table_class = RQ::Get()->reverse_log && $date != 1 ? 'day' : 'night'; //2日目以降は昼から
      break;

    default:
      $flag_border_game = true;
      $table_class = RQ::Get()->reverse_log && $date != 1 ? 'day' : 'night'; //2日目以降は昼から
      if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode) {
	DB::$USER->ResetRoleList();
	DB::$USER->SetEvent(true);
      }
      break;
    }

    //-- 仮想稼動モードテスト用 --//
    //DB::$SELF = DB::$USER->rows[3];
    //DB::$SELF->Parse('human earplug');
    //DB::$SELF->live = 'live';
    //DB::$ROOM->status = 'playing';
    //DB::$ROOM->option_list[] = 'not_open_cast';

    //出力
    $str = '';
    if ($flag_border_game && ! RQ::Get()->reverse_log) {
      DB::$ROOM->date  = $date + 1;
      DB::$ROOM->scene = 'day';
      $str .= GameHTML::GenerateLastWords() . GameHTML::GenerateDead(); //死亡者を出力
    }
    DB::$ROOM->date  = $date;
    DB::$ROOM->scene = $table_class;
    if ($scene != 'heaven_only') DB::$ROOM->SetWeather();

    $id = DB::$ROOM->IsPlaying() ? 'date' . DB::$ROOM->date : DB::$ROOM->scene;
    $builder = new TalkBuilder('talk ' . $table_class, $id);
    if (RQ::Get()->reverse_log) $builder->GenerateTimeStamp();
    //if (DB::$ROOM->watch_mode) $builder->AddSystem(DB::$ROOM->date . print_r(DB::$ROOM->event, true));

    foreach (TalkDB::GetLog($date, $scene) as $talk) {
      switch ($talk->scene) {
      case 'day':
	if (DB::$ROOM->IsDay() || $talk->location == 'dummy_boy') break;
	$str .= $builder->Refresh() . self::GenerateSceneChange($date);
	DB::$ROOM->scene = $talk->scene;
	$builder->Begin('talk ' . $talk->scene);
	break;

      case 'night':
	if (DB::$ROOM->IsNight() || $talk->location == 'dummy_boy') break;
	$str .= $builder->Refresh() . self::GenerateSceneChange($date);
	DB::$ROOM->scene = $talk->scene;
	$builder->Begin('talk ' . $talk->scene);
	break;
      }
      $builder->Generate($talk); //会話生成
    }

    if (! RQ::Get()->reverse_log) $builder->GenerateTimeStamp();
    $str .= $builder->Refresh();

    if ($flag_border_game && RQ::Get()->reverse_log) {
      //突然死で勝敗が決定したケース
      if ($date == DB::$ROOM->last_date && DB::$ROOM->IsDay()) $str .= GameHTML::GenerateVote();

      DB::$ROOM->date  = $date + 1;
      DB::$ROOM->scene = 'day';
      $str .= GameHTML::GenerateDead() . GameHTML::GenerateLastWords(); //遺言を出力
    }
    return $str;
  }

  //シーン切り替え処理
  private static function GenerateSceneChange($date) {
    $str = '';
    if (RQ::Get()->heaven_only) return $str;
    DB::$ROOM->date = $date;
    if (RQ::Get()->reverse_log) {
      DB::$ROOM->scene = 'night';
      $str .= GameHTML::GenerateVote() . GameHTML::GenerateDead();
    } else {
      $str .= GameHTML::GenerateDead() . GameHTML::GenerateVote();
    }
    return $str;
  }

  //役職リンク生成
  private static function GenerateRoleLink() {
    $stack = array();
    foreach (DB::$USER->role as $role => $list) {
      $stack[] = $role;
    }

    $str_stack  = array();
    $role_stack = array();
    foreach (array_intersect(array_keys(RoleData::$main_role_list), $stack) as $role) {
      if (! isset($camp)) $camp = RoleData::GetCamp($role);
      if ($camp != RoleData::GetCamp($role) || count($role_stack) > 9) {
	$str_stack[] = implode(' / ', $role_stack);
	$role_stack = array();
	$camp = RoleData::GetCamp($role);
      }
      $role_stack[] = RoleDataHTML::GenerateMain($role) . count(DB::$USER->role[$role]);
    }
    $str_stack[] = implode(' / ', $role_stack);

    $role_stack = array();
    foreach (array_intersect(array_keys(RoleData::$sub_role_list), $stack) as $role) {
      if (count($role_stack) > 9) {
	$str_stack[] = implode(' / ', $role_stack);
	$role_stack = array();
      }
      $role_stack[] = RoleDataHTML::GenerateSub($role) . count(DB::$USER->role[$role]);
    }
    $str_stack[] = implode(' / ', $role_stack);
    return implode(Text::BR . Text::LF, $str_stack);
  }
}
