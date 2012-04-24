<?php
require_once('include/init.php');
//$INIT_CONF->LoadFile('feedengine'); //RSS機能はテスト中
$INIT_CONF->LoadFile('room_config');
$INIT_CONF->LoadClass('ROOM_IMG', 'ROOM_OPT');

if (! DB::ConnectInHeader()) return false;
MaintenanceRoom();
EncodePostData();
if (@$_POST['command'] == 'CREATE_ROOM') {
  $INIT_CONF->LoadClass('USER_ICON', 'MESSAGE', 'TWITTER');
  CreateRoom();
}
else {
  $INIT_CONF->LoadFile('time_config');
  $INIT_CONF->LoadClass('CAST_CONF', 'GAME_OPT_CAPT');
  OutputRoomList();
}
DB::Disconnect();

//-- 関数 --//
//村のメンテナンス処理
/* call する位置を調整して、他のサーバから起動されないようにする */
function MaintenanceRoom(){
  if (ServerConfig::$disable_maintenance) return; //スキップ判定

  //一定時間更新の無い村は廃村にする
  $query = "UPDATE room SET status = 'finished', scene = 'aftergame' " .
    "WHERE STATUS IN ('waiting', 'playing') AND last_update_time < UNIX_TIMESTAMP() - " .
    RoomConfig::$die_room;
  /*
  //RSS更新(廃村が0の時も必要ない処理なのでfalseに限定していない)
  if (DB::FetchBool($query)) OutputSiteSummary();
  */
  DB::Execute($query);

  //終了した部屋のセッションIDのデータをクリアする
  $second = RoomConfig::$clear_session_id;
  $query = <<<EOF
UPDATE user_entry INNER JOIN room ON user_entry.session_id IS NOT NULL AND
  user_entry.room_no = room.room_no AND room.status = 'finished' AND
  (room.finish_datetime IS NULL OR
   room.finish_datetime < DATE_SUB(NOW(), INTERVAL {$second} SECOND))
  SET user_entry.session_id = NULL
EOF;
  DB::Execute($query);
}

//村(room)の作成
function CreateRoom(){
  global $USER_ICON, $TWITTER, $ROOM_OPT, $GAME_OPT_CONF;

  if (ServerConfig::$disable_establish) {
    OutputActionResult('村作成 [制限事項]', '村作成はできません');
  }
  if (CheckReferer('', array('127.0.0.1', '192.168.'))) { //リファラチェック
    OutputActionResult('村作成 [入力エラー]', '無効なアクセスです。');
  }
  //PrintData($ROOM_OPT);

  //-- 入力データのエラーチェック --//
  //村の名前・説明のデータチェック
  foreach (array('room_name', 'room_comment') as $str) {
    $$str = $_POST[$str];
    EscapeStrings($$str);
    if ($$str == '') { //未入力チェック
      OutputRoomAction('empty', false, $ROOM_OPT->GetCaption($str));
      return false;
    }
    //文字列チェック
    if (strlen($$str) > RoomConfig::$$str || preg_match(RoomConfig::$ng_word, $$str)) {
      OutputRoomAction('comment', false, $ROOM_OPT->GetCaption($str));
      return false;
    }
  }

  //最大人数チェック
  $max_user = @(int)$_POST['max_user'];
  if (! in_array($max_user, RoomConfig::$max_user_list)) {
    OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
  }

  if (! DB::Lock('room')) { //トランザクション開始
    OutputRoomAction('busy');
    return false;
  }

  $ip_address = @$_SERVER['REMOTE_ADDR']; //処理実行ユーザの IP を取得
  if (! ServerConfig::$debug_mode) { //デバッグモード時は村作成制限をスキップ
    $str = 'room_password'; //パスワードチェック
    if (isset(ServerConfig::$$str) && @$_POST[$str] != ServerConfig::$$str) {
      OutputActionResult('村作成 [制限事項]', '村作成パスワードが正しくありません。');
    }

    //ブラックリストチェック
    if (CheckBlackList()) OutputActionResult('村作成 [制限事項]', '村立て制限ホストです。');

    $query = "FROM room WHERE status IN ('waiting', 'playing')"; //チェック用の共通クエリ
    $time  = DB::FetchResult("SELECT MAX(establish_datetime) {$query}"); //連続作成制限チェック
    if (isset($time) && TZTime() - ConvertTimeStamp($time, false) <= RoomConfig::$establish_wait) {
      OutputRoomAction('establish_wait');
      return false;
    }

    //最大稼働数チェック
    if (DB::Count("SELECT room_no {$query}") >= RoomConfig::$max_active_room) {
      OutputRoomAction('full');
      return false;
    }

    //同一ユーザの連続作成チェック
    if (DB::Count("SELECT room_no {$query} AND establisher_ip = '{$ip_address}'") > 0) {
      OutputRoomAction('over_establish');
      return false;
    }
  }

  //-- ゲームオプションをセット --//
  $ROOM_OPT->LoadPostParams(
    'real_time', 'dummy_boy_selector', 'perverseness', 'replace_human_selector', 'special_role',
    'wish_role', 'open_vote', 'seal_message', 'open_day', 'not_open_cast_selector');
  if ($ROOM_OPT->quiz) { //クイズ村
    $gm_password = @$_POST['gm_password']; //GM ログインパスワードをチェック
    EscapeStrings($gm_password);
    if ($gm_password == '') {
      OutputRoomAction('no_password');
      return false;
    }
    $ROOM_OPT->Set(RoomOption::GAME_OPTION, 'dummy_boy', true);
    $ROOM_OPT->Set(RoomOption::GAME_OPTION, 'gm_login',  true);
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $gm_password;
  }
  else {
    //身代わり君関連のチェック
    if ($ROOM_OPT->dummy_boy) {
      $dummy_boy_handle_name = '身代わり君';
      $dummy_boy_password    = ServerConfig::$system_password;
      $ROOM_OPT->LoadPostParams('gerd');
    }
    elseif ($ROOM_OPT->gm_login) {
      $gm_password = @$_POST['gm_password']; //GM ログインパスワードをチェック
      EscapeStrings($gm_password);
      if ($gm_password == '') {
        OutputRoomAction('no_password');
        return false;
      }
      $ROOM_OPT->Set(RoomOption::GAME_OPTION, 'dummy_boy', true);
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = $gm_password;
      $ROOM_OPT->LoadPostParams('gerd');
    }

    if ($ROOM_OPT->chaos || $ROOM_OPT->chaosfull || $ROOM_OPT->chaos_hyper ||
	$ROOM_OPT->chaos_verso) { //闇鍋モード
      $ROOM_OPT->LoadPostParams('special_role', 'secret_sub_role', 'topping', 'boost_rate',
				'chaos_open_cast', 'sub_role_limit');
    }
    elseif ($ROOM_OPT->duel || $ROOM_OPT->gray_random) { //特殊配役モード
      /*
	もともとここには$_POSTの内容をロードするコードが存在した。
	このブロックは通常村と決闘村を識別するために残されている。(2012-01-18 enogu)
      */
    }
    else { //通常村
      $ROOM_OPT->LoadPostParams(
        'poison', 'assassin', 'wolf', 'boss_wolf', 'poison_wolf', 'possessed_wolf', 'sirius_wolf',
	'fox', 'child_fox', 'medium');
      if (! $ROOM_OPT->full_cupid)   $ROOM_OPT->LoadPostParams('cupid');
      if (! $ROOM_OPT->full_mania)   $ROOM_OPT->LoadPostParams('mania');
      if (! $ROOM_OPT->perverseness) $ROOM_OPT->LoadPostParams('decide', 'authority');
    }
    $ROOM_OPT->LoadPostParams(
      'liar', 'gentleman', $ROOM_OPT->perverseness ? 'perverseness' : 'sudden_death', 'deep_sleep',
      'mind_open', 'blinder', 'critical', 'joker', 'death_note', 'detective', 'weather', 'festival',
      'replace_human_selector', 'change_common_selector', 'change_mad_selector',
      'change_cupid_selector');
  }

  if ($ROOM_OPT->real_time) {
    //制限時間チェック
    list($day, $night) = $ROOM_OPT->real_time;
    if ($day <= 0 || 99 < $day || $night <= 0 || 99 < $night) {
      OutputRoomAction('time');
      return false;
    }

    $ROOM_OPT->LoadPostParams('wait_morning');
  }

  //登録
  //ALTER TABLE room_no AUTO_INCREMENT = value; //カウンタセット SQL
  $room_no     = DB::FetchResult('SELECT MAX(room_no) FROM room') + 1; //村番号の最大値を取得
  $game_option = $ROOM_OPT->GetOptionString(RoomOption::GAME_OPTION);
  $option_role = $ROOM_OPT->GetOptionString(RoomOption::ROLE_OPTION);
  $status      = false;
  //PrintData($_POST, 'Post');
  //PrintData($ROOM_OPT);
  //PrintData($game_option, 'GameOption');
  //PrintData($option_role, 'OptionRole');
  //OutputHTMLFooter(true); //テスト用

  do {
    if (! ServerConfig::$dry_run_mode) {
      //村作成
      $items  = 'room_no, name, comment, max_user, game_option, ' .
        'option_role, status, date, scene, vote_count, scene_start_time, last_update_time, ' .
        'establisher_ip, establish_datetime';
      $values = "{$room_no}, '{$room_name}', '{$room_comment}', {$max_user}, '{$game_option}', " .
        "'{$option_role}', 'waiting', 0, 'beforegame', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), " .
        "'{$ip_address}', NOW()";
      if (! DB::Insert('room', $items, $values)) break;

      //身代わり君を入村させる
      if ($ROOM_OPT->dummy_boy &&
	  DB::Count('SELECT uname FROM user_entry WHERE room_no = ' . $room_no) == 0){
        if (! DB::InsertUser($room_no, 'dummy_boy', $dummy_boy_handle_name, $dummy_boy_password,
			     1, $ROOM_OPT->gerd ? $USER_ICON->gerd : 0)) break;
      }

      if (ServerConfig::$secret_room) { //村情報非表示モードの処理
        DB::Commit();
        OutputRoomAction('success', false, $room_name);
        return true;
      }
    }

    $TWITTER->Send($room_no, $room_name, $room_comment); //Twitter 投稿処理
    //OutputSiteSummary(); //RSS更新 //テスト中

    DB::Commit();
    OutputRoomAction('success', false, $room_name);
    $status = true;
  } while (false);

  if (! $status) OutputRoomAction('busy');
  return true;
}

//結果出力 (CreateRoom() 用)
function OutputRoomAction($type, $rollback = true, $str = ''){
  switch ($type) {
  case 'empty':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo "<ul><li>{$str}が記入されていない。</li>";
    break;

  case 'comment':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo "<ul><li>{$str}の文字数が長すぎる。</li>";
    echo "<li>{$str}に入力禁止文字列が含まれている。</li></ul>";
    break;

  case 'establish_wait':
    OutputActionResultHeader('村作成 [制限事項]');
    echo 'サーバで設定されている村立て許可時間間隔を経過していません。<br>'."\n";
    echo 'しばらく時間を開けてから再度登録してください。';
    break;

  case 'full':
    OutputActionResultHeader('村作成 [制限事項]');
    echo '現在プレイ中の村の数がこのサーバで設定されている最大値を超えています。<br>'."\n";
    echo 'どこかの村で決着がつくのを待ってから再度登録してください。';
    break;

  case 'over_establish':
    OutputActionResultHeader('村作成 [制限事項]');
    echo 'あなたが立てた村が現在稼働中です。<br>'."\n";
    echo '立てた村の決着がつくのを待ってから再度登録してください。';
    break;

  case 'no_password':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo '有効な GM ログインパスワードが設定されていません。';
    break;

  case 'time':
    OutputActionResultHeader('村作成 [入力エラー]');
    echo 'エラーが発生しました。<br>';
    echo '以下の項目を再度ご確認ください。<br>';
    echo '<ul><li>リアルタイム制の昼・夜の時間を記入していない。</li>';
    echo '<li>リアルタイム制の昼・夜の時間が 0 以下、または 99 以上である。</li>';
    echo '<li>リアルタイム制の昼・夜の時間を全角で入力している。</li>';
    echo '<li>リアルタイム制の昼・夜の時間が数字ではない。</li></ul>';
    break;

  case 'busy':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo 'データベースサーバが混雑しています。<br>'."\n";
    echo '時間を置いて再度登録してください。';
    break;

  case 'success':
    OutputActionResultHeader('村作成', ServerConfig::$site_root);
    echo $str . ' 村を作成しました。トップページに飛びます。';
    echo '切り替わらないなら <a href="' . ServerConfig::$site_root . '">ここ</a> 。';
    break;
  }
  if ($rollback) DB::Rollback();
  OutputHTMLFooter(); //フッタ出力
}

//村(room)のwaitingとplayingのリストを出力する
function OutputRoomList(){
  global $ROOM_IMG;

  if (ServerConfig::$secret_room) return; //シークレットテストモード

  /* RSS機能はテスト中
  if (! ServerConfig::$debug_mode){
    $filename = JINRO_ROOT.'/rss/rooms.rss';
    if (file_exists($filename)){
      $rss = FeedEngine::Initialize('site_summary.php');
      $rss->Import($filename);
    }
    else {
      $rss = OutputSiteSummary();
    }
    foreach ($rss->items as $item){
      extract($item, EXTR_PREFIX_ALL, 'room');
      echo $room_description;
    }
  }
  */

  //部屋情報を取得
  $delete_header = '<a href="admin/room_delete.php?room_no=';
  $delete_footer = '">[削除 (緊急用)]</a>'."\n";
  $query = 'SELECT room_no, name, comment, game_option, option_role, max_user, status ' .
    "FROM room WHERE status IN ('waiting', 'playing') ORDER BY room_no DESC";
  foreach (DB::FetchAssoc($query) as $stack){
    extract($stack);
    $delete     = ServerConfig::$debug_mode ? $delete_header . $room_no . $delete_footer : '';
    $status_img = $ROOM_IMG->Generate($status, $status == 'waiting' ? '募集中' : 'プレイ中');
    $option_img = RoomOption::Wrap($game_option, $option_role)->GenerateImageList() .
      GenerateMaxUserImage($max_user);
    echo <<<EOF
{$delete}<a href="login.php?room_no={$room_no}">
{$status_img}<span>[{$room_no}番地]</span>{$name}村<br>
<div>～{$comment}～ {$option_img}</div>
</a><br>

EOF;
  }
}

//部屋作成画面を出力
function OutputCreateRoomPage(){
  global $ROOM_OPT;

  if (ServerConfig::$disable_establish) {
    echo '村作成はできません';
    return;
  }

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>

EOF;

  RoomOption::ShowBuildRoomForm();
  $password = is_null(ServerConfig::$room_password) ? '' :
    '<label for="room_password">村作成パスワード</label>：' .
    '<input type="password" id="room_password" name="room_password" size="20">　';
  echo <<<EOF
<tr><td class="make" colspan="2">{$password}<input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
}
