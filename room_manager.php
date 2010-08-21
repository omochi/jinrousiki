<?php
require_once('include/init.php');
//$INIT_CONF->LoadFile('feedengine'); //RSS機能はテスト中
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'TIME_CONF', 'ROOM_IMG', 'MESSAGE', 'GAME_OPT_CAPT');

if(! $DB_CONF->Connect(true, false)) return false; //DB 接続
MaintenanceRoom();
EncodePostData();
$_POST['command'] == 'CREATE_ROOM' ? CreateRoom() : OutputRoomList();
$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
//村のメンテナンス処理
function MaintenanceRoom(){
  global $ROOM_CONF;

  //一定時間更新の無い村は廃村にする
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' " .
    "WHERE status <> 'finished' AND last_updated < UNIX_TIMESTAMP() - " . $ROOM_CONF->die_room;
  /*
  //RSS更新(廃村が0の時も必要ない処理なのでfalseに限定していない)
  if(SendQuery($query)) OutputSiteSummary();
  */
  SendQuery($query);

  //終了した部屋のセッションIDのデータをクリアする
  $query = <<<EOF
UPDATE room, user_entry SET user_entry.session_id = NULL
WHERE room.room_no = user_entry.room_no
AND room.status = 'finished' AND !(user_entry.session_id IS NULL)
AND (room.finish_time IS NULL OR
     room.finish_time < DATE_SUB(NOW(), INTERVAL {$ROOM_CONF->clear_session_id} SECOND))
EOF;
  SendQuery($query, true);
}

//村(room)の作成
function CreateRoom(){
  global $DEBUG_MODE, $SERVER_CONF, $ROOM_CONF, $MESSAGE;

  if(CheckReferer('', array('127.', '192.168.'))){ //リファラチェック
    OutputActionResult('村作成 [入力エラー]', '無効なアクセスです。');
  }

  //入力データのエラーチェック
  $room_name    = $_POST['room_name'];
  $room_comment = $_POST['room_comment'];
  EscapeStrings($room_name);
  EscapeStrings($room_comment);
  if($room_name == '' || $room_comment == ''){ //未入力チェック
    OutputRoomAction('empty');
    return false;
  }

  //文字列チェック
  if(strlen($room_name)    > $ROOM_CONF->room_name ||
     strlen($room_comment) > $ROOM_CONF->room_comment ||
     preg_match($ROOM_CONF->ng_word, $room_name) ||
     preg_match($ROOM_CONF->ng_word, $room_comment)){
    OutputRoomAction('comment');
    return false;
  }

  //指定された人数の配役があるかチェック
  $max_user = (int)$_POST['max_user'];
  if(! in_array($max_user, $ROOM_CONF->max_user_list)){
    OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
  }

  $query = "FROM room WHERE status <> 'finished'"; //チェック用の共通クエリ
  $ip_address = $_SERVER['REMOTE_ADDR']; //村立てを行ったユーザの IP を取得

  //デバッグモード時は村立て制限をしない
  if(! $DEBUG_MODE){
    if(CheckBlackList()){ //ブラックリストチェック
      OutputRoomAction('black_list');
      return false;
    }

    //同じユーザが立てた村が終了していなければ新しい村を作らない
    if(FetchResult("SELECT COUNT(room_no) {$query} AND establisher_ip = '{$ip_address}'") > 0){
      OutputRoomAction('over_establish');
      return false;
    }

    //最大並列村数を超えているようであれば新しい村を作らない
    if(FetchResult('SELECT COUNT(room_no)' . $query) >= $ROOM_CONF->max_active_room){
      OutputRoomAction('full');
      return false;
    }

    //連続村立て制限チェック
    $time_stamp = FetchResult("SELECT establish_time {$query} ORDER BY room_no DESC");
    if(isset($time_stamp) &&
       TZTime() - ConvertTimeStamp($time_stamp, false) <= $ROOM_CONF->establish_wait){
      OutputRoomAction('establish_wait');
      return false;
    }
  }

  //ゲームオプションをセット
  $chaos        = ($ROOM_CONF->chaos        && $_POST['chaos'] == 'chaos');
  $chaosfull    = ($ROOM_CONF->chaosfull    && $_POST['chaos'] == 'chaosfull');
  $chaos_hyper  = ($ROOM_CONF->chaos_hyper  && $_POST['chaos'] == 'chaos_hyper');
  $perverseness = ($ROOM_CONF->perverseness && $_POST['perverseness']  == 'on');
  $full_mania   = ($ROOM_CONF->full_mania   && $_POST['replace_human']  == 'full_mania');
  $full_cupid   = ($ROOM_CONF->full_cupid   && $_POST['replace_human']  == 'full_cupid');
  $quiz         = ($ROOM_CONF->quiz         && $_POST['quiz']  == 'on');
  $duel         = ($ROOM_CONF->duel         && $_POST['duel']  == 'on');
  $game_option_list = array();
  $option_role_list = array();
  $check_game_option_list = array('wish_role', 'open_vote', 'open_day', 'not_open_cast');
  $check_option_role_list = array();
  if($quiz){
    $game_option_list[] = 'quiz';

    //GM ログインパスワードをチェック
    $quiz_password = $_POST['quiz_password'];
    EscapeStrings(&$quiz_password);
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    $game_option_list[]    = 'dummy_boy';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
  }
  else{
    if($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'on'){
      $game_option_list[]    = 'dummy_boy';
      $dummy_boy_handle_name = '身代わり君';
      $dummy_boy_password    = $SERVER_CONF->system_password;
      $check_option_role_list[] = 'gerd';
    }
    elseif($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'gm_login'){
      //GM ログインパスワードをチェック
      $gm_password = $_POST['gm_password'];
      if($gm_password == ''){
	OutputRoomAction('empty');
	return false;
      }
      EscapeStrings(&$gm_password);
      array_push($game_option_list, 'dummy_boy', 'gm_login');
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = $gm_password;
      $check_option_role_list[] = 'gerd';
    }

    if($chaos || $chaosfull || $chaos_hyper){
      $game_option_list[] = $chaos ? 'chaos' : ($chaosfull ? 'chaosfull' : 'chaos_hyper');
      $check_game_option_list[] = 'secret_sub_role';
      array_push($check_option_role_list, 'chaos_open_cast', 'chaos_open_cast_camp',
		 'chaos_open_cast_role');
      if($perverseness){
	$option_role_list[] = 'sub_role_limit';
	$check_option_role_list[] = 'perverseness';
      }
      else{
	$check_option_role_list[] = 'sub_role_limit';
      }
    }
    else{
      if($duel){
	$option_role_list[] = 'duel';
      }
      else{
	array_push($check_option_role_list, 'poison', 'assassin', 'boss_wolf', 'poison_wolf',
		   'possessed_wolf', 'sirius_wolf');
	if(! $full_cupid) $check_option_role_list[] = 'cupid';
	$check_option_role_list[] = 'medium';
	if(! $perverseness) array_push($check_option_role_list, 'decide', 'authority');
	if(! $full_mania) $check_option_role_list[] = 'mania';
      }
    }
    array_push($check_option_role_list, 'liar', 'gentleman');
    $check_option_role_list[] = $perverseness ? 'perverseness' : 'sudden_death';
    $check_option_role_list[] = 'critical';
    if(! $duel) array_push($check_option_role_list, 'detective', 'replace_human');
    $check_game_option_list[] = 'festival';
  }

  //PrintData($_POST, 'Post');
  //PrintData($check_game_option_list, 'CheckGameOption');
  foreach($check_game_option_list as $option){
    if(! $ROOM_CONF->$option) continue;
    if($option == 'not_open_cast'){
      switch($_POST[$option]){
      case 'full':
	$option = 'not_open_cast';
	break;

      case 'auto':
	$option = 'auto_open_cast';
	break;

      default:
	continue 2;
      }
    }
    elseif($_POST[$option] != 'on') continue;
    $game_option_list[] = $option;
  }
  //PrintData($game_option_list);

  //PrintData($check_option_role_list, 'CheckOptionRole');
  foreach($check_option_role_list as $option){
    if(! $ROOM_CONF->$option) continue;
    if($option == 'chaos_open_cast'){
      switch($_POST[$option]){
      case 'full':
	$option = 'chaos_open_cast';
	break;

      case 'camp':
	$option = 'chaos_open_cast_camp';
	break;

      case 'role':
	$option = 'chaos_open_cast_role';
	break;

      default:
	continue 2;
      }
    }
    elseif($option == 'replace_human'){
      $target = $_POST[$option];
      switch($target){
      case 'full_mania':
      case 'full_chiroptera':
      case 'full_cupid':
      case 'replace_human':
	if(! $ROOM_CONF->$target) continue 2;
	$option = $target;
	break;

      default:
	continue 2;
      }
    }
    elseif($option == 'sub_role_limit'){
      $target = $_POST[$option];
      switch($target){
      case 'no_sub_role':
      case 'sub_role_limit_easy':
      case 'sub_role_limit_normal':
	if(! $ROOM_CONF->$target) continue;
	$option = $target;
	break;

      default:
	continue 2;
      }
    }
    elseif($_POST[$option] != 'on') continue;
    $option_role_list[] = $option;
  }

  if($ROOM_CONF->real_time && $_POST['real_time'] == 'on'){
    $day   = $_POST['real_time_day'];
    $night = $_POST['real_time_night'];

    //制限時間が0から99以内の数字かチェック
    if($day   != '' && ! preg_match('/[^0-9]/', $day)   && $day   > 0 && $day   < 99 &&
       $night != '' && ! preg_match('/[^0-9]/', $night) && $night > 0 && $night < 99){
      $game_option_list[] = 'real_time:' . $day . ':' . $night;
    }
    else{
      OutputRoomAction('time');
      return false;
    }
  }

  //PrintData($game_option_list, 'GameOption');
  //PrintData($option_role_list, 'OptionRole');
  //OutputHTMLFooter(true);

  //テーブルをロック
  if(! LockTable()){
    OutputRoomAction('busy');
    return false;
  }

  //降順にルーム No を取得して最も大きな No を取得
  $room_no = FetchResult('SELECT room_no FROM room ORDER BY room_no DESC') + 1;

  //登録
  $game_option = implode(' ', $game_option_list);
  $option_role = implode(' ', $option_role_list);
  $status = false;
  do{
    //村作成
    $time = TZTime();
    $items = 'room_no, room_name, room_comment, establisher_ip, establish_time, ' .
      'game_option, option_role, max_user, status, date, day_night, last_updated';
    $values = "{$room_no}, '{$room_name}', '{$room_comment}', '{$ip_address}', NOW(), " .
      "'{$game_option}', '{$option_role}', {$max_user}, 'waiting', 0, 'beforegame', '{$time}'";
    if(! InsertDatabase('room', $items, $values)) break;

    //身代わり君を入村させる
    if(strpos($game_option, 'dummy_boy') !== false &&
       FetchResult('SELECT COUNT(uname) FROM user_entry WHERE room_no = ' . $room_no) == 0){
      if(! InsertUser($room_no, 'dummy_boy', $dummy_boy_handle_name, $dummy_boy_password)) break;
    }

    if($SERVER_CONF->secret_room){ //村情報非表示モードの処理
      OutputRoomAction('success', $room_name);
      return true;
    }

    //Twitter 投稿処理
    $twitter = new TwitterConfig();
    $twitter->Send($room_no, $room_name, $room_comment);

    //OutputSiteSummary(); //RSS更新 //テスト中

    OutputRoomAction('success', $room_name);
    $status = true;
  }while(false);
  if(! $status) OutputRoomAction('busy');
  return true;
}

//結果出力 (CreateRoom() 用)
function OutputRoomAction($type, $room_name = ''){
  global $SERVER_CONF;

  switch($type){
  case 'empty':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo '<ul><li>村の名前が記入されていない。</li>';
    echo '<li>村の説明が記入されていない。</li></ul>';
    break;

  case 'comment':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo '<ul><li>村の名前・村の説明の文字数が長すぎる</li>';
    echo '<li>村の名前・村の説明に入力禁止文字列が含まれている。</li></ul>';
    break;

  case 'time':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo '<ul><li>リアルタイム制の昼、夜の時間を記入していない。</li>';
    echo '<li>リアルタイム制の昼、夜の時間を全角で入力している</li>';
    echo '<li>リアルタイム制の昼、夜の時間が0以下、または99以上である</li>';
    echo '<li>リアルタイム制の昼、夜の時間が数字ではない、または異常な文字列</li></ul>';
    break;

  case 'success':
    OutputActionResultHeader('村作成', $SERVER_CONF->site_root);
    echo $room_name . ' 村を作成しました。トップページに飛びます。';
    echo '切り替わらないなら <a href="' . $SERVER_CONF->site_root . '">ここ</a> 。';
    break;

  case 'busy':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo 'データベースサーバが混雑しています。<br>'."\n";
    echo '時間を置いて再度登録してください。';
    break;

  case 'black_list':
    OutputActionResultHeader('村作成 [制限事項]');
    echo '村立て制限ホストです。';
    break;

  case 'full':
    OutputActionResultHeader('村作成 [制限事項]');
    echo '現在プレイ中の村の数がこのサーバで設定されている最大値を超えています。<br>'."\n";
    echo 'どこかの村で決着がつくのを待ってから再度登録してください。';
    break;

  case 'over_establish':
    OutputActionResultHeader('村作成 [制限事項]');
    echo 'あなたが立てた村が現在稼働中です。<br>'."\n";
    echo '立てた村で決着がつくのを待ってから再度登録してください。';
    break;

  case 'establish_wait':
    OutputActionResultHeader('村作成 [制限事項]');
    echo 'サーバで設定されている村立て時間間隔を経過していません。<br>'."\n";
    echo 'しばらく時間を開けてから再度登録してください。';
    break;
  }
  OutputHTMLFooter(); //フッタ出力
}

//村(room)のwaitingとplayingのリストを出力する
function OutputRoomList(){
  global $DEBUG_MODE, $SERVER_CONF, $ROOM_IMG;

  if($SERVER_CONF->secret_room) return;

  /* RSS機能はテスト中
  if(! $DEBUG_MODE){
    $filename = JINRO_ROOT.'/rss/rooms.rss';
    if(file_exists($filename)){
      $rss = FeedEngine::Initialize('site_summary.php');
      $rss->Import($filename);
    }
    else{
      $rss = OutputSiteSummary();
    }
    foreach($rss->items as $item){
      extract($item, EXTR_PREFIX_ALL, 'room');
      echo $room_description;
    }
  }
  */

  //return; //シークレットテスト用
  //ルームNo、ルーム名、コメント、最大人数、状態を取得
  $query = "SELECT room_no, room_name, room_comment, game_option, option_role, max_user, status " .
    "FROM room WHERE status <> 'finished' ORDER BY room_no DESC";
  $list = FetchAssoc($query);
  foreach($list as $array){
    extract($array);
    $option_img_str = GenerateGameOptionImage($game_option, $option_role); //ゲームオプションの画像
    //$option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //最大人数

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->Generate($status)}<span>[{$room_no}番地]</span>{$room_name}村<br>
<div>〜{$room_comment}〜 {$option_img_str}(最大{$max_user}人)</div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' .
	$room_no . ' 番地を削除 (緊急用)</a><br>'."\n";
    }
  }
}

//他のサーバの部屋画面を出力
function OutputSharedServerRoom(){
  global $SERVER_CONF;

  $SHARED_CONF =& new SharedServerConfig();
  if($SHARED_CONF->disable) return false;

  foreach($SHARED_CONF->server_list as $server => $array){
    extract($array);
    //PrintData($url, 'URL'); //テスト用
    if($disable || $url == $SERVER_CONF->site_root) continue;

    if(! $SHARED_CONF->CheckConnection($url)){ //サーバ通信状態チェック
      $data = $SHARED_CONF->host . ': Connection timed out (3 seconds)';
      echo $SHARED_CONF->GenerateSharedServerRoom($name, $url, $data);
      continue;
    }

    //部屋情報を取得
    if(($data = @file_get_contents($url.'room_manager.php')) == '') continue;
    //PrintData($data, 'Data'); //テスト用
    if($encode != '' && $encode != $SHARED_CONF->encode){
      $data = mb_convert_encoding($data, $SHARED_CONF->encode, $encode);
    }
    if($separator != ''){
      $split_list = mb_split($separator, $data);
      //PrintData($split_list, 'Split'); //テスト用
      $data = array_pop($split_list);
    }
    if($footer != ''){
      if(($position = mb_strrpos($data, $footer)) === false) continue;
      $data = mb_substr($data, 0, $position + mb_strlen($footer));
    }
    if($data == '') continue;

    $replace_list = array('href="' => 'href="' . $url, 'src="'  => 'src="' . $url);
    $data = strtr($data, $replace_list);
    echo $SHARED_CONF->GenerateSharedServerRoom($name, $url, $data);
  }
}

//部屋作成画面を出力
function OutputCreateRoomPage(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>
<tr>
<td><label>{$GAME_OPT_MESS->room_name}：</label></td>
<td><input type="text" name="room_name" size="{$ROOM_CONF->room_name}"> 村</td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->room_comment}：</label></td>
<td><input type="text" name="room_comment" size="{$ROOM_CONF->room_comment}"></td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->max_user}：</label></td>
<td>
<select name="max_user">
<optgroup label="最大人数">

EOF;

  foreach($ROOM_CONF->max_user_list as $number){
    echo '<option' . ($number == $ROOM_CONF->default_max_user ? ' selected' : '') . '>' .
      $number . '</option>'."\n";
  }

  echo <<<EOF
</optgroup>
</select>
<span class="explain">({$GAME_OPT_CAPT->max_user})</span></td>
</tr>

EOF;

  OutputRoomOption(array('wish_role', 'real_time', 'open_vote', 'open_day'));
  OutputRoomOptionDummyBoy();
  OutputRoomOptionOpenCast();

  $stack = array('poison', 'assassin', 'boss_wolf', 'poison_wolf', 'possessed_wolf',
		 'sirius_wolf', 'cupid', 'medium', 'mania', 'decide', 'authority');
  OutputRoomOption($stack, 'role');

  $stack = array('liar', 'gentleman', 'sudden_death', 'perverseness', 'critical', 'detective',
		 'festival',  'replace_human');
  OutputRoomOption($stack, 'role');

  OutputRoomOptionChaos();
  OutputRoomOption(array('quiz', 'duel'));

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr><td class="make" colspan="2"><input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
}

function GenerateRoomOption($option, $label = ''){
  global $ROOM_CONF, $TIME_CONF, $CAST_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->$option) return NULL;

  $default = 'default_' . $option;
  $checked = ($ROOM_CONF->$default ? ' checked' : '');
  if($label != '') $label .= '_';
  $label .= $option;

  $sentence = $CAST_CONF->$option;
  if(isset($sentence)) $sentence .= '人以上で';
  if($option == 'cupid') $sentence = '14人もしくは' . $sentence . '<br>　';
  $sentence .= $GAME_OPT_MESS->$option;

  $caption = $GAME_OPT_CAPT->$option;
  switch($option){
  case 'real_time':
    $caption .= <<<EOF
　昼：
<input type="text" name="real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">分 夜：
<input type="text" name="real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">分
EOF;
    break;

  case 'quiz':
    $add_caption = <<<EOF
<br>
<label for="quiz_password">GM ログインパスワード：</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
　　{$GAME_OPT_CAPT->gm_login_footer}
EOF;
    break;

  case 'replace_human':
    $str = <<<EOF
<tr>
<td><label>{$GAME_OPT_MESS->$option}：</label></td>
<td>
<select name="{$option}">
<optgroup label="モード名">
<option value="" selected>なし</option>

EOF;

    foreach($ROOM_CONF->replace_human_list as $role){
      if($ROOM_CONF->$role){
	$str .= '<option value="' . $role . '">' . $GAME_OPT_MESS->$role . '</option>'."\n";
      }
    }

    $str .= <<<EOF
</optgroup>
</select>
<span class="explain">({$GAME_OPT_CAPT->$option})</span></td>
</tr>

EOF;
    return $str;
  }

  return <<<EOF
<tr>
<td><label for="{$label}">{$sentence}：</label></td>
<td class="explain">
<input id="{$label}" type="checkbox" name="{$option}" value="on"{$checked}>
({$caption}){$add_caption}
</td>
</tr>

EOF;
}

function OutputRoomOption($option_list, $label = '', $border = true){
  $stack = array();
  foreach($option_list as $option) $stack[] = GenerateRoomOption($option, $label);
  if(count($stack) < 1) return NULL;
  if($border) array_unshift($stack, '<tr><td colspan="2"><hr></td></tr>');
  echo implode('', $stack);
}

function OutputRoomOptionDummyBoy(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->dummy_boy) return NULL;

  if($ROOM_CONF->default_dummy_boy)
    $checked_dummy_boy = ' checked';
  elseif($ROOM_CONF->default_gm_login)
    $checked_gm = ' checked';
  else
    $checked_nothing = ' checked';

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->dummy_boy}：</label></td>
<td class="explain">
<input type="radio" name="dummy_boy" value=""{$checked_nothing}>
{$GAME_OPT_CAPT->no_dummy_boy}<br>

<input type="radio" name="dummy_boy" value="on"{$checked_dummy_boy}>
{$GAME_OPT_CAPT->dummy_boy}<br>

<input type="radio" name="dummy_boy" value="gm_login"{$checked_gm_login}>
{$GAME_OPT_MESS->gm_login} ({$GAME_OPT_CAPT->gm_login_header})<br>
<label for="gm_password">GM ログインパスワード：</label>
<input id="gm_password" type="password" name="gm_password" size="20"><br>
　　{$GAME_OPT_CAPT->gm_login_footer}
</td>
</tr>

EOF;

  if($ROOM_CONF->gerd){
    $checked = $ROOM_CONF->default_gerd ? ' checked' : '';
    echo <<<EOF
<tr>
<td><label for="gerd">{$GAME_OPT_MESS->gerd}：</label></td>
<td class="explain">
<input id="gerd" type="checkbox" name="gerd" value="on"{$checked}>
{$GAME_OPT_CAPT->gerd}
</td>
</tr>
EOF;
  }
}

function OutputRoomOptionOpenCast(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->not_open_cast) return NULL;

  switch($ROOM_CONF->default_not_open_cast){
  case 'full':
    $checked_close = ' checked';
    break;

  case 'auto':
    if($ROOM_CONF->auto_open_cast){
      $checked_auto = ' checked';
      break;
    }

  default:
    $checked_open = ' checked';
    break;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->not_open_cast}：</label></td>
<td class="explain">
<input type="radio" name="not_open_cast" value=""{$checked_open}>
{$GAME_OPT_CAPT->no_close_cast}<br>

<input type="radio" name="not_open_cast" value="full"{$checked_full}>
{$GAME_OPT_CAPT->not_open_cast}<br>

EOF;

  if($ROOM_CONF->auto_open_cast){
    echo <<<EOF
<input type="radio" name="not_open_cast" value="auto"{$checked_auto}>
{$GAME_OPT_CAPT->auto_open_cast}
</td>

EOF;
  }
}

function OutputRoomOptionChaos(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->chaos) return NULL;

  switch($ROOM_CONF->default_chaos){
  case 'chaos':
    $checked_chaos = ' checked';
    break;

  case 'chaosfull':
    if($ROOM_CONF->chaosfull){
      $checked_chaosfull = ' checked';
      break;
    }

  case 'chaos_hyper':
    if($ROOM_CONF->chaos_hyper){
      $checked_chaos_hyper = ' checked';
      break;
    }

  default:
    $checked_normal = ' checked';
    break;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->chaos}：</label></td>
<td class="explain">
<input type="radio" name="chaos" value=""{$checked_normal}>
{$GAME_OPT_CAPT->no_chaos}<br>

<input type="radio" name="chaos" value="chaos"{$checked_chaos}>
{$GAME_OPT_CAPT->chaos}<br>

EOF;

  if($ROOM_CONF->chaosfull){
    echo <<<EOF
<input type="radio" name="chaos" value="chaosfull"{$checked_chaosfull}>
{$GAME_OPT_CAPT->chaosfull}<br>

EOF;
  }

  if($ROOM_CONF->chaos_hyper){
    echo <<<EOF
<input type="radio" name="chaos" value="chaos_hyper"{$checked_chaos_hyper}>
{$GAME_OPT_CAPT->chaos_hyper}

EOF;
  }
  echo '</td></tr>'."\n";

  if($ROOM_CONF->chaos_open_cast){
    switch($ROOM_CONF->default_chaos_open_cast){
    case 'full':
      $checked_chaos_open_cast_full = ' checked';
      break;

    case 'camp':
      $checked_chaos_open_cast_camp = ' checked';
      break;

    case 'role':
      $checked_chaos_open_cast_role = ' checked';
      break;

    default:
      $checked_chaos_open_cast_none = ' checked';
      break;
    }

    echo <<<EOF
<tr>
<td><label>{$GAME_OPT_MESS->chaos_open_cast}：</label></td>
<td class="explain">
<input type="radio" name="chaos_open_cast" value=""{$checked_chaos_open_cast_none}>
{$GAME_OPT_CAPT->chaos_not_open_cast}<br>

<input type="radio" name="chaos_open_cast" value="camp"{$checked_chaos_open_cast_camp}>
{$GAME_OPT_CAPT->chaos_open_cast_camp}<br>

<input type="radio" name="chaos_open_cast" value="role"{$checked_chaos_open_cast_role}>
{$GAME_OPT_CAPT->chaos_open_cast_role}<br>

<input type="radio" name="chaos_open_cast" value="full"{$checked_chaos_open_cast_full}>
{$GAME_OPT_CAPT->chaos_open_cast_full}
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->sub_role_limit){
    switch($ROOM_CONF->default_sub_role_limit){
    case 'no':
      $checked_no_sub_role = ' checked';
      break;

    case 'easy':
      $checked_sub_role_limit_easy = ' checked';
      break;

    case 'normal':
      $checked_sub_role_limit_normal = ' checked';
      break;

    default:
      $checked_sub_role_limit_none = ' checked';
      break;
    }

    echo <<<EOF
<tr>
<td><label>{$GAME_OPT_MESS->sub_role_limit}：</label></td>
<td class="explain">
<input type="radio" name="sub_role_limit" value="no_sub_role"{$checked_no_sub_role}>
{$GAME_OPT_CAPT->no_sub_role}<br>

<input type="radio" name="sub_role_limit" value="sub_role_limit_easy"{$checked_sub_role_limit_easy}>
{$GAME_OPT_CAPT->sub_role_limit_easy}<br>

<input type="radio" name="sub_role_limit" value="sub_role_limit_normal"{$checked_sub_role_limit_normal}>
{$GAME_OPT_CAPT->sub_role_limit_normal}<br>

<input type="radio" name="sub_role_limit" value=""{$checked_sub_role_limit_none}>
{$GAME_OPT_CAPT->sub_role_limit_none}<br>
</td>
</tr>

EOF;
  }
  OutputRoomOption(array('secret_sub_role'), '', false);
}
