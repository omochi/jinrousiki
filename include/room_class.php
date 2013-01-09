<?php
//-- 個別村クラス --//
class Room {
  public $id;
  public $name;
  public $comment;
  public $game_option = '';
  public $option_role = '';
  public $date;
  public $scene;
  public $status;
  public $system_time;
  public $sudden_death;
  public $view_mode        = false;
  public $dead_mode        = false;
  public $heaven_mode      = false;
  public $log_mode         = false;
  public $watch_mode       = false;
  public $single_log_mode  = false;
  public $single_view_mode = false;
  public $personal_mode    = false;
  public $test_mode        = false;

  function __construct($request = null, $lock = false) {
    if (is_null($request)) return;
    $stack = $request->IsVirtualRoom() ? $request->GetTestRoom() :
      $this->LoadRoom($request->room_no, $lock);
    foreach ($stack as $name => $value) $this->$name = $value;
    $this->ParseOption();
  }

  //指定した部屋番号の DB 情報を取得する
  function LoadRoom($room_no, $lock = false) {
    $stack = RoomDataDB::Get($room_no, $lock);
    if (count($stack) < 1) HTML::OutputResult('村番号エラー', '無効な村番号です: ' . $room_no);
    return $stack;
  }

  //option_role を追加ロードする
  function LoadOption() {
    $option_role = RQ::Get()->IsVirtualRoom() ? RQ::GetTest()->test_room['option_role'] :
      RoomDB::Fetch('option_role');
    $this->option_role = new OptionParser($option_role);
    $this->option_list = array_merge($this->option_list, array_keys($this->option_role->list));
  }

  //最大参加人数を取得する
  function LoadMaxUser() { return RoomDB::Fetch('max_user'); }

  //シーンを取得する
  function LoadScene() { return RoomDB::Fetch('scene', true); }

  //経過時間取得
  function LoadTime() { return RoomDB::Fetch('UNIX_TIMESTAMP() - last_update_time'); }

  //シーンに合わせた投票情報を取得する
  function LoadVote($kick = false) {
    if (RQ::Get()->IsVirtualRoom()) {
      if (is_null($vote_list = RQ::GetTest()->vote->{$this->scene})) return null;
    }
    else {
      $format = 'SELECT %s FROM vote' . RoomDB::DATE . " AND scene = '%s' AND vote_count = %d";
      switch ($this->scene) {
      case 'beforegame':
      case 'night':
	$data = 'user_no, target_no, type';
	break;

      case 'day': //必要に応じて revote_count を WHERE に足す (不要のはず)
	$data = 'user_no, target_no, vote_number';
	break;

      default:
	return null;
      }
      $query = sprintf($format, $data, $this->id, $this->date, $this->scene, $this->vote_count);
      DB::Prepare($query);
      $vote_list = DB::FetchAssoc();
    }

    //Text::p($vote_list);
    $stack = array();
    switch ($this->scene) {
    case 'beforegame':
      $type = $kick ? 'KICK_DO' : 'GAMESTART';
      foreach ($vote_list as $list) {
	if ($list['type'] != $type) continue;
	if ($kick) {
	  $stack[$list['user_no']][] = $list['target_no'];
	}
	else {
	  $stack[] = $list['user_no'];
	}
      }
      break;

    case 'day':
      foreach ($vote_list as $list) {
	$id = $list['user_no'];
	unset($list['user_no']);
	$stack[$id] = $list;
      }
      break;

    case 'night':
      foreach ($vote_list as $list) {
	$id = $list['user_no'];
	unset($list['user_no']);
	$stack[$id][] = $list;
      }
      break;
    }

    $this->vote = $stack;
    return count($this->vote);
  }

  //特殊イベント判定用の情報を DB から取得する
  function LoadEvent() {
    if (! $this->IsPlaying()) return null;
    $this->event = new StdClass();
    if ($this->test_mode) {
      $stack = array();
      foreach (RQ::GetTest()->system_message as $date => $date_list) {
	if ($date != $this->date) continue;
	//Text::p($date_list, $date);
	foreach ($date_list as $type => $type_list) {
	  switch ($type) {
	  case 'WEATHER':
	  case 'EVENT':
	  case 'SAME_FACE':
	  case 'VOTE_DUEL':
	  case 'BLIND_VOTE':
	    foreach ($type_list as $event) {
	      $stack[] = array('type' => $type, 'message' => $event);
	    }
	    break;
	  }
	}
      }
      $this->event->rows = $stack;
      return;
    }
    $type_list = array("'WEATHER'", "'EVENT'", "'BLIND_VOTE'", "'SAME_FACE'");
    if ($this->IsDay()) $type_list[] = "'VOTE_DUEL'";

    $format = 'SELECT type, message FROM system_message' . RoomDB::DATE . ' AND type IN (%s)';
    $query  = sprintf($format, $this->id, $this->date, implode(',', $type_list));
    DB::Prepare($query);
    $this->event->rows = DB::FetchAssoc();
  }

  //天候判定用の情報を DB から取得する
  function LoadWeather($shift = false) {
    if (! $this->IsPlaying()) return null;
    $date = $this->date;
    if (($shift && RQ::Get()->reverse_log) || $this->IsAfterGame()) $date++;
    $format = 'SELECT message FROM system_message' . RoomDB::DATE . " AND type = 'WEATHER'";
    $result = DB::FetchResult(sprintf($format, $this->id, $date));
    $this->event->weather = $result === false ? null : $result; //天候を格納
  }

  //勝敗情報を DB から取得する
  function LoadWinner() {
    if (! isset($this->winner)) { //未設定ならキャッシュする
      $this->winner = $this->test_mode ? RQ::GetTest()->winner : RoomDB::Fetch('winner');
    }
    return $this->winner;
  }

  //player 情報を DB から取得する
  function LoadPlayer() {
    $query = 'SELECT id AS role_id, date, scene, user_no, role FROM player WHERE room_no = ?';
    DB::Prepare($query, array($this->id));
    $result = new StdClass();
    foreach (DB::FetchAssoc() as $stack) {
      extract($stack);
      $result->roles[$role_id] = $role;
      $result->users[$user_no][] = $role_id;
      $result->timeline[$date][$scene][] = $role_id;
    }
    //Text::p($result, 'Player');
    return $result;
  }

  //投票情報をコマンド毎に分割する
  function ParseVote() {
    $stack = array();
    foreach ($this->vote as $id => $vote_stack) {
      if ($this->IsDay()) {
	$stack[$vote_stack['type']][$id] = $vote_stack['target_no'];
      }
      else {
	foreach ($vote_stack as $list) $stack[$list['type']][$id] = $list['target_no'];
      }
    }
    return $stack;
  }

  //ゲームオプションの展開処理
  function ParseOption($join = false) {
    $this->game_option = new OptionParser($this->game_option);
    $this->option_role = new OptionParser($this->option_role);
    $this->option_list = $join ?
      array_merge(array_keys($this->game_option->list), array_keys($this->option_role->list)) :
      array_keys($this->game_option->list);

    if ($this->IsRealTime()) {
      $this->real_time = new StdClass();
      $this->real_time->day   = $this->game_option->list['real_time'][0];
      $this->real_time->night = $this->game_option->list['real_time'][1];
    }
  }

  //今までの投票を全部削除
  function DeleteVote() {
    if (is_null($this->id)) return true;

    $query = 'DELETE FROM vote' . $this->GetQuery();
    if ($this->IsDay()) {
      $query .= " AND type = 'VOTE_KILL' AND revote_count = " . $this->revote_count;
    }
    elseif ($this->IsNight()) {
      if ($this->date == 1) {
	$query .= " AND type NOT IN ('CUPID_DO', 'DUELIST_DO')";
      }
      else {
	$query .= " AND type NOT IN ('VOTE_KILL')";
      }
    }
    DB::Execute($query);
    DB::Optimize('vote');
    return true;
  }

  //共通クエリを取得
  function GetQuery($date = true, $count = null) {
    $query = (is_null($count) ? '' : 'SELECT COUNT(uname) FROM ' . $count) .
      ' WHERE room_no = ' . $this->id;
    return $date ? $query . ' AND date = ' . $this->date : $query;
  }

  //共通クエリヘッダを取得
  function GetQueryHeader($data) {
    $stack = func_get_args();
    $from = array_shift($stack);
    return 'SELECT ' . implode(', ', $stack) . ' FROM ' . $from . $this->GetQuery(false);
  }

  //特殊イベント判定用の情報を取得する
  function GetEvent($force = false) {
    if (! $this->IsPlaying()) return array();
    if ($force || ! isset($this->event)) $this->LoadEvent();
    return $this->event->rows;
  }

  //特殊オプションの配役データ取得
  function GetOptionList($option) {
    return $this->IsOption($option) ?
      ChaosConfig::${$option . '_list'}[$this->option_role->list[$option][0]] : array();
  }

  //オプション判定
  function IsOption($option) { return in_array($option, $this->option_list); }

  //オプショングループ判定
  function IsOptionGroup($option) {
    foreach ($this->option_list as $this_option) {
      if (strpos($this_option, $option) !== false) return true;
    }
    return false;
  }

  //リアルタイム制判定
  function IsRealTime() { return $this->IsOption('real_time'); }

  //身代わり君使用判定
  function IsDummyBoy() { return $this->IsOption('dummy_boy'); }

  //クイズ村判定
  function IsQuiz() { return $this->IsOption('quiz'); }

  //村人置換村グループオプション判定
  function IsReplaceHumanGroup() {
    return $this->IsOption('replace_human') || $this->IsOptionGroup('full_');
  }

  //闇鍋式希望制オプション判定
  function IsChaosWish() {
    return $this->IsOptionGroup('chaos') || $this->IsOption('duel') ||
      $this->IsOption('festival') || $this->IsReplaceHumanGroup() ||
      $this->IsOptionGroup('change_');
  }

  //霊界公開判定
  function IsOpenCast() {
    if (! isset($this->open_cast)) { //未設定ならキャッシュする
      if ($this->IsOption('not_open_cast')) { //常時非公開
	$user = DB::$USER->ByID(1); //身代わり君の蘇生辞退判定
	$this->open_cast = $user->IsDummyBoy() && $user->IsDrop() && DB::$USER->IsOpenCast();
      }
      elseif ($this->IsOption('auto_open_cast')) { //自動公開
	$this->open_cast = DB::$USER->IsOpenCast();
      }
      else { //常時公開
	$this->open_cast = true;
      }
    }
    return $this->open_cast;
  }

  //情報公開判定
  function IsOpenData($virtual = false) {
    return DB::$SELF->IsDummyBoy() ||
      (DB::$SELF->IsDead() && ! $this->single_view_mode && $this->IsOpenCast()) ||
      ($virtual ? $this->IsAfterGame() : ($this->IsFinished() && ! $this->single_view_mode));
  }

  //ゲーム開始前判定
  function IsBeforeGame() { return $this->scene == 'beforegame'; }

  //ゲーム中 (昼) 判定
  function IsDay() { return $this->scene == 'day'; }

  //ゲーム中 (夜) 判定
  function IsNight() { return $this->scene == 'night'; }

  //ゲーム終了後判定
  function IsAfterGame() { return $this->scene == 'aftergame'; }

  //ゲーム開始前判定
  function IsWaiting() { return $this->status == 'waiting'; }

  //ゲーム中判定 (仮想処理をする為、status では判定しない)
  function IsPlaying() { return $this->IsDay() || $this->IsNight(); }

  //ゲーム終了判定
  function IsFinished() { return $this->status == 'finished'; }

  //特殊イベント判定
  function IsEvent($type) {
    if (! isset($this->event)) $this->event = new StdClass();
    return isset($this->event->$type) ? $this->event->$type : null;
  }

  //超過警告メッセージ出力済み判定
  function IsOvertimeAlert() {
    $query = RoomDB::SetID('overtime_alert') . ' AND overtime_alert IS FALSE';
    return DB::Count($query) < 1;
  }

  //天候セット
  function SetWeather() {
    if ($this->watch_mode || $this->single_view_mode) {
      $this->LoadWeather();
      if (isset(RoleData::$weather_list[$this->event->weather])) {
	$this->event->{RoleData::$weather_list[$this->event->weather]['event']} = true;
      }
    }
    $this->LoadWeather(true);
  }

  //突然死タイマーセット
  function SetSuddenDeath() {
    $this->sudden_death = TimeConfig::SUDDEN_DEATH - $this->LoadTime();
  }

  //発言登録
  function Talk($sentence, $action = null, $uname = '', $scene = '', $location = null,
		$font_type = null, $role_id = null, $spend_time = 0) {
    if ($uname == '') $uname = 'system';
    if ($scene == '') {
      $scene = $this->scene;
      if (is_null($location)) $location = 'system';
    }
    if ($this->test_mode) {
      $str = "Talk: {$uname}: {$scene}: {$location}: {$action}: {$font_type}";
      Text::p(Text::ConvertLine($sentence), $str);
      return true;
    }

    switch ($scene) {
    case 'beforegame':
    case 'aftergame':
      $table = 'talk_' . $scene;
      break;

    default:
      $table = 'talk';
      break;
    }
    $items  = 'room_no, date, scene, uname, sentence, spend_time, time';
    $values = "{$this->id}, {$this->date}, '{$scene}', '{$uname}', '{$sentence}', {$spend_time}, " .
      "UNIX_TIMESTAMP()";

    if (isset($action)) {
      $items  .= ', action';
      $values .= ", '{$action}'";
    }
    if (isset($location)) {
      $items  .= ', location';
      $values .= ", '{$location}'";
    }
    if (isset($font_type)) {
      $items  .= ', font_type';
      $values .= ", '{$font_type}'";
    }
    if (isset($role_id)) {
      $items  .= ', role_id';
      $values .= ", {$role_id}";
    }
    return DB::Insert($table, $items, $values);
  }

  //発言登録 (ゲーム開始前専用
  function TalkBeforeGame($sentence, $uname, $handle_name, $color, $font_type = null) {
    if ($this->test_mode) {
      $str = "Talk: {$uname}: {$handle_name}: {$color}: {$font_type}";
      Text::p(Text::ConvertLine($sentence), $str);
      return true;
    }

    $items  = 'room_no, date, scene, uname, handle_name, color, sentence, time';
    $values = "{$this->id}, 0, '{$this->scene}', '{$uname}', '{$handle_name}', '{$color}', " .
      "'{$sentence}', UNIX_TIMESTAMP()";
    if (isset($font_type)) {
      $items  .= ', font_type';
      $values .= ", '{$font_type}'";
    }
    return DB::Insert('talk_' . $this->scene, $items, $values);
  }

  //超過警告メッセージ登録
  function OvertimeAlert($str) {
    if ($this->IsOvertimeAlert()) return true;
    $this->Talk($str);
    $this->UpdateTime();
    return $this->UpdateOvertimeAlert(true);
  }

  //システムメッセージ登録
  function SystemMessage($str, $type, $add_date = 0) {
    $date = $this->date + $add_date;
    if ($this->test_mode) {
      Text::p("{$type} ({$date}): {$str}", 'SystemMessage');
      if (is_array(RQ::GetTest()->system_message)) {
	RQ::GetTest()->system_message[$date][$type][] = $str;
      }
      return true;
    }
    $items = 'room_no, date, type, message';
    $values = "{$this->id}, {$date}, '{$type}', '{$str}'";
    return DB::Insert('system_message', $items, $values);
  }

  //能力発動結果登録
  function ResultAbility($type, $result, $target = null, $user_no = null) {
    $date = $this->date;
    if ($this->test_mode) {
      Text::p("{$type}: {$result}: {$target}: {$user_no}", 'ResultAbility');
      if (is_array(RQ::GetTest()->result_ability)) {
	$stack = array('user_no' => $user_no, 'target' => $target, 'result' => $result);
	RQ::GetTest()->result_ability[$date][$type][] = $stack;
      }
      return true;
    }
    $items  = 'room_no, date, type';
    $values = "{$this->id}, {$date}, '{$type}'";
    foreach (array('result', 'target', 'user_no') as $data) {
      if (isset($$data)) {
	$items  .= ", {$data}";
	$values .= ", '{$$data}'";
      }
    }
    return DB::Insert('result_ability', $items, $values);
  }

  //システムメッセージ登録
  function ResultDead($name, $type, $result = null) {
    $date = $this->date;
    if ($this->test_mode) {
      Text::p("{$name}: {$type} ({$date}): {$result}", 'ResultDead');
      if (is_array(RQ::GetTest()->result_dead)) {
	$stack = array('type' => $type, 'handle_name' => $name, 'result' => $result);
	RQ::GetTest()->result_dead[] = $stack;
      }
      return true;
    }
    $items = 'room_no, date, scene, type';
    $values = "{$this->id}, {$date}, '{$this->scene}', '{$type}'";
    if (isset($name)) {
      $items  .= ', handle_name';
      $values .= ", '{$name}'";
    }
    if (isset($result)) {
      $items  .= ', result';
      $values .= ", '{$result}'";
    }
    return DB::Insert('result_dead', $items, $values);
  }

  //天候登録
  function EntryWeather($id, $date, $priest = false) {
    $this->SystemMessage($id, 'WEATHER', $date);
    if ($priest) { //祈祷師の処理
      $result = 'prediction_weather_' . RoleData::$weather_list[$id]['event'];
      $this->ResultAbility('WEATHER_PRIEST_RESULT', $result);
    }
  }

  //投票回数を更新
  function UpdateVoteCount($reset = false) {
    if ($this->test_mode) return true;
    DB::Execute('UPDATE room SET vote_count = vote_count + 1' . $this->GetQuery(false));
    $this->UpdateOvertimeAlert();
    if (! $reset && $this->date != 1) return true;
    $query = 'UPDATE vote SET vote_count = vote_count + 1' . $this->GetQuery() .
      " AND type IN ('CUPID_DO', 'DUELIST_DO')";
    return DB::FetchBool($query);
  }

  //超過警告メッセージ判定フラグ変更
  function UpdateOvertimeAlert($bool = false) {
    if ($this->test_mode) return true;
    $flag = $bool ? 'TRUE' : 'FALSE';
    return DB::FetchBool('UPDATE room SET overtime_alert = ' . $flag . $this->GetQuery(false));
  }

  //最終更新時刻を更新
  function UpdateTime() {
    if ($this->test_mode) return true;
    $query = 'UPDATE room SET last_update_time = UNIX_TIMESTAMP()' . $this->GetQuery(false);
    return DB::FetchBool($query);
  }

  //シーンを更新
  function UpdateScene($date = false) {
    $query = "scene = '{$this->scene}', vote_count = 1, overtime_alert = FALSE, ".
      "scene_start_time = UNIX_TIMESTAMP()";
    if ($date) $query .= ", date = {$this->date}, revote_count = 0";
    return DB::FetchBool('UPDATE room SET ' . $query . $this->GetQuery(false));
  }

  //夜にする
  function ChangeNight() {
    $this->scene = 'night';
    if ($this->test_mode) return true;
    $this->UpdateScene();
    return $this->Talk('', 'NIGHT'); //夜がきた通知
  }

  //次の日にする
  function ChangeDate() {
    $this->date++;
    $this->scene = 'day';
    if ($this->test_mode) return true;
    $this->UpdateScene(true);

    //夜が明けた通知
    $this->Talk($this->date, 'MORNING');
    $this->UpdateTime(); //最終書き込みを更新
    //$this->DeleteVote(); //今までの投票を全部削除

    $status = Winner::Check(); //勝敗のチェック
    return $status;
  }

  //夜を飛ばす
  function SkipNight() {
    if ($this->IsEvent('skip_night')) {
      Vote::AggregateNight(true);
      $this->talk(Message::$skip_night);
    }
  }

  //仮想的にシーンをずらす
  function ShiftScene($unshift = false) {
    if ($unshift) {
      $this->date--;
      $this->scene = 'night';
    } else {
      $this->date++;
      $this->scene = 'day';
    }
  }

  //背景設定 CSS タグを生成
  function GenerateCSS() {
    if (isset($this->scene)) return HTML::LoadCSS(sprintf('%s/game_%s', JINRO_CSS, $this->scene));
  }

  //村のタイトルタグを生成
  function GenerateTitleTag() {
    return '<td class="room"><span>' . $this->name . '村</span>　[' . $this->id .
      '番地]<br>～' . $this->comment . '～</td>'."\n";
  }
}

//-- DB アクセス (Room 拡張) --//
class RoomDB {
  const SELECT = 'SELECT %s FROM room';
  const ID     = ' WHERE room_no = %d';
  const DATE   = ' WHERE room_no = %d AND date = %d';
  const LOCK   = ' FOR UPDATE';

  //基礎条件取得
  static function GetID($lock = false) {
    return self::SELECT . self::ID . ($lock ? self::LOCK : '');
  }

  //日付入り条件取得
  static function GetDate() { return self::SELECT . self::Date; }

  //前日の能力発動結果取得
  static function GetAbility() {
    $query = <<<EOF
SELECT message, type FROM system_message WHERE room_no = ? AND date = ? AND type IN 
EOF;
    $date = DB::$ROOM->date - 1;
    $list = array(DB::$ROOM->id, $date);
    $action_list = array('MAGE_DO', 'STEP_MAGE_DO', 'VOODOO_KILLER_DO', 'MIND_SCANNER_DO',
			 'WOLF_EAT', 'STEP_WOLF_EAT', 'SILENT_WOLF_EAT', 'JAMMER_MAD_DO',
			 'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
    If ($date == 1) {
      array_push($action_list, 'CUPID_DO', 'DUELIST_DO', 'MANIA_DO');
    }
    else {
      array_push($action_list, 'GUARD_DO', 'STEP_GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO',
		 'WIZARD_DO', 'SPREAD_WIZARD_DO', 'ESCAPE_DO', 'DREAM_EAT', 'ASSASSIN_DO',
		 'ASSASSIN_NOT_DO',
		 'POISON_CAT_DO', 'POISON_CAT_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
		 'POSSESSED_DO', 'POSSESSED_NOT_DO', 'VAMPIRE_DO', 'OGRE_DO', 'OGRE_NOT_DO',
		 'DEATH_NOTE_DO', 'DEATH_NOTE_NOT_DO');
    }
    $query .= sprintf('(%s)', implode(',', array_fill(0, count($action_list), '?')));
    DB::Prepare($query, array_merge($list, $action_list));
    return DB::FetchAssoc();
  }

  //死者情報取得
  static function GetDead($yesterday = false) {
    $query = <<<EOF
SELECT date, type, handle_name, result FROM result_dead
WHERE room_no = ? AND date = ? AND scene = ?
EOF;
    $list = array(DB::$ROOM->id);
    if ($yesterday) {
      array_push($list, DB::$ROOM->date - 1, DB::$ROOM->scene);
    } elseif (DB::$ROOM->IsDay()) {
      array_push($list, DB::$ROOM->date - 1, 'night');
    } else {
      array_push($list, DB::$ROOM->date, 'day');
    }
    DB::Prepare($query, $list);
    return DB::FetchAssoc();
  }

  //遺言取得
  static function GetLastWords($shift = false) {
    $query = 'SELECT handle_name, message FROM result_lastwords WHERE room_no = ? AND date = ?';
    DB::Prepare($query, array(DB::$ROOM->id, DB::$ROOM->date - ($shift ? 0 : 1)));
    return DB::FetchAssoc();
  }

  //基礎 SQL セット
  static function SetID($data, $lock = false) {
    return sprintf(self::GetID($lock), $data, DB::$ROOM->id);
  }

  //日付入り SQL セット
  static function SetDate() { return sprintf(self::DATE, DB::$ROOM->id, DB::$ROOM->date); }

  //基礎 SQL 取得
  static function Fetch($data, $lock = false) {
    return DB::FetchResult(self::SetID($data, $lock));
  }

  //村データ UPDATE
  static function Update($list) {
    $query  = 'UPDATE room SET ';
    $update = array();
    foreach ($list as $key => $value) {
      $update[] = sprintf("%s = '%s'", $key, $value);
    }
    return DB::Execute($query . implode(', ', $update) . sprintf(self::ID, DB::$ROOM->id));
  }

  //村終了処理
  static function Finish($winner) {
    $query = <<<EOF
UPDATE room SET status = 'finished', scene = 'aftergame',
scene_start_time = UNIX_TIMESTAMP(), winner = '%s', finish_datetime = NOW()
EOF;
    return DB::FetchBool(sprintf($query, $winner) . sprintf(self::ID, DB::$ROOM->id));
  }
}

//-- DB アクセス (RoomData 拡張) --//
class RoomDataDB {
  //村データ取得
  static function Get($room_no, $lock = false) {
    $query = <<<EOF
SELECT room_no AS id, name, comment, game_option, status, date, scene,
vote_count, revote_count, scene_start_time FROM room WHERE room_no = ?
EOF;
    if ($lock) $query .= ' FOR UPDATE';
    DB::Prepare($query, array($room_no));
    return DB::FetchAssoc(true);
  }

  //終了した村番地を取得
  static function GetFinished($reverse) {
    $order = $reverse ? 'DESC' : 'ASC';
    $query = 'SELECT room_no FROM room WHERE status = ? ORDER BY room_no ' . $order;
    if (RQ::Get()->page != 'all') {
      $view = OldLogConfig::VIEW;
      $query .= sprintf(' LIMIT %d, %d', $view * (RQ::Get()->page - 1), $view);
    }
    DB::Prepare($query, array('finished'));
    return DB::FetchColumn();
  }

  //終了した村数を取得
  static function GetFinishedCount() {
    DB::Prepare('SELECT room_no FROM room WHERE status = ?', array('finished'));
    return DB::Count();
  }

  //村クラス取得 (進行中)
  static function LoadOpening() {
    $query = <<<EOF
SELECT room_no AS id, name, comment, game_option, option_role, max_user, status
FROM room WHERE status != ? ORDER BY room_no DESC
EOF;
    DB::Prepare($query, array('finished'));
    $stack = DB::FetchClass('room');
    if (count($stack) < 1) die('村一覧の取得に失敗しました');

    $result = array();
    foreach ($stack as $room) {
      $room->ParseOption();
      $result[] = $room;
    }
    return $result;
  }

  //村クラス取得 (終了)
  static function LoadFinished($room_no) {
    $query = <<<EOF
SELECT room_no AS id, name, comment, date, game_option, option_role, max_user, winner,
  establish_datetime, start_datetime, finish_datetime,
  (SELECT COUNT(user_no) FROM user_entry WHERE user_entry.room_no = room.room_no
   AND user_entry.user_no > 0) AS user_count
FROM room WHERE room_no = ? AND status = ?
EOF;
    DB::Prepare($query, array($room_no, 'finished'));
    return DB::FetchClass('Room', true);
  }

  //村クラス取得 (ユーザ登録用)
  static function LoadEntryUser($room_no) {
    $query = <<<EOF
SELECT room_no AS id, date, scene, status, game_option, max_user
FROM room WHERE room_no = ? FOR UPDATE
EOF;
    DB::Prepare($query, array($room_no));
    return DB::FetchClass('Room', true);
  }

  //村クラス取得 (ユーザ登録画面用)
  static function LoadEntryUserPage() {
    $query = <<<EOF
SELECT room_no AS id, name, comment, status, game_option, option_role
FROM room WHERE room_no = ?
EOF;
    DB::Prepare($query, array(RQ::Get()->room_no));
    return DB::FetchClass('Room', true);
  }

  //村存在判定
  static function Exists() {
    $query = 'SELECT room_no FROM room WHERE room_no = ?';
    DB::Prepare($query, array(RQ::Get()->room_no));
    return DB::Count() > 0;
  }
}
