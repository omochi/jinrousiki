<?php
require_once('include/init.php');
//$INIT_CONF->LoadFile('feedengine'); //RSS機能はテスト中
$INIT_CONF->LoadClass('ROOM_CONF', 'ROOM_IMG');

if(! $DB_CONF->Connect(true, false)) return false; //DB 接続
MaintenanceRoom();
EncodePostData();
if(@$_POST['command'] == 'CREATE_ROOM'){
  $INIT_CONF->LoadClass('USER_ICON', 'MESSAGE', 'TWITTER');
  CreateRoom();
}
else{
  $INIT_CONF->LoadClass('CAST_CONF', 'TIME_CONF', 'GAME_OPT_CAPT');
  OutputRoomList();
}
$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
//村のメンテナンス処理
function MaintenanceRoom(){
  global $SERVER_CONF, $ROOM_CONF;

  if($SERVER_CONF->disable_maintenance) return; //スキップ判定

  //一定時間更新の無い村は廃村にする
  $query = "UPDATE room SET status = 'finished', scene = 'aftergame' " .
    "WHERE status <> 'finished' AND last_update_time < UNIX_TIMESTAMP() - " . $ROOM_CONF->die_room;
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
AND (room.finish_datetime IS NULL OR
     room.finish_datetime < DATE_SUB(NOW(), INTERVAL {$ROOM_CONF->clear_session_id} SECOND))
EOF;
  SendQuery($query, true);
}

//村(room)の作成
function CreateRoom(){
  global $SERVER_CONF, $DB_CONF, $ROOM_CONF, $USER_ICON, $TWITTER;

  if($SERVER_CONF->disable_establish) OutputActionResult('村作成 [制限事項]', '村作成はできません');
  if(CheckReferer('', array('127.0.0.1', '192.168.'))){ //リファラチェック
    OutputActionResult('村作成 [入力エラー]', '無効なアクセスです。');
  }

  //-- 入力データのエラーチェック --//
  //村の名前・説明のデータチェック
  foreach(array('room_name' => '村の名前', 'room_comment' => '村の説明') as $str => $name){
    $$str = @$_POST[$str];
    EscapeStrings($$str);
    if($$str == ''){ //未入力チェック
      OutputRoomAction('empty', false, $name);
      return false;
    }
    if(strlen($$str) > $ROOM_CONF->$str || preg_match($ROOM_CONF->ng_word, $$str)){ //文字列チェック
      OutputRoomAction('comment', false, $name);
      return false;
    }
  }

  //最大人数チェック
  $max_user = @(int)$_POST['max_user'];
  if(! in_array($max_user, $ROOM_CONF->max_user_list)){
    OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
  }

  if(! $DB_CONF->Transaction()){ //トランザクション開始
    OutputRoomAction('busy');
    return false;
  }
  //稼動数カウントをロック
  $room_limit = FetchResult("SELECT count FROM count_limit WHERE type = 'room' FOR UPDATE");

  $ip_address = @$_SERVER['REMOTE_ADDR']; //処理実行ユーザの IP を取得
  if(! $SERVER_CONF->debug_mode){ //デバッグモード時は村作成制限をスキップ
    $str = 'room_password'; //パスワードチェック
    if(isset($SERVER_CONF->$str) && @$_POST[$str] != $SERVER_CONF->$str){
      OutputActionResult('村作成 [制限事項]', '村作成パスワードが正しくありません。');
    }

    //ブラックリストチェック
    if(CheckBlackList()) OutputActionResult('村作成 [制限事項]', '村立て制限ホストです。');

    $query = "FROM room WHERE status IN ('waiting', 'playing')"; //チェック用の共通クエリ
    $time  = FetchResult("SELECT MAX(establish_datetime) {$query}"); //連続作成制限チェック
    if(isset($time) && TZTime() - ConvertTimeStamp($time, false) <= $ROOM_CONF->establish_wait){
      OutputRoomAction('establish_wait');
      return false;
    }

    //最大稼働数チェック
    if(FetchResult("SELECT COUNT(room_no) {$query}") >= $ROOM_CONF->max_active_room){
      OutputRoomAction('full');
      return false;
    }

    //同一ユーザの連続作成チェック (終了していなければエラー処理)
    if(FetchResult("SELECT COUNT(room_no) {$query} AND establisher_ip = '{$ip_address}'") > 0){
      OutputRoomAction('over_establish');
      return false;
    }
  }

  //-- ゲームオプションをセット --//
  $perverseness = $ROOM_CONF->perverseness && @$_POST['perverseness']  == 'on';
  $full_mania   = $ROOM_CONF->full_mania   && @$_POST['replace_human'] == 'full_mania';
  $full_cupid   = $ROOM_CONF->full_cupid   && @$_POST['replace_human'] == 'full_cupid';
  $chaos        = $ROOM_CONF->chaos        && @$_POST['special_role']  == 'chaos';
  $chaosfull    = $ROOM_CONF->chaosfull    && @$_POST['special_role']  == 'chaosfull';
  $chaos_hyper  = $ROOM_CONF->chaos_hyper  && @$_POST['special_role']  == 'chaos_hyper';
  $chaos_verso  = $ROOM_CONF->chaos_verso  && @$_POST['special_role']  == 'chaos_verso';
  $quiz         = $ROOM_CONF->quiz         && @$_POST['special_role']  == 'quiz';
  $special_role =
    ($ROOM_CONF->duel         && @$_POST['special_role']  == 'duel') ||
    ($ROOM_CONF->gray_random  && @$_POST['special_role']  == 'gray_random');
  $game_option_list = array();
  $option_role_list = array();
  $check_game_option_list = array('wish_role', 'open_vote', 'seal_message', 'open_day',
                                  'not_open_cast');
  $check_option_role_list = array();
  if($quiz){ //クイズ村
    $gm_password = @$_POST['gm_password']; //GM ログインパスワードをチェック
    EscapeStrings($gm_password);
    if($gm_password == ''){
      OutputRoomAction('no_password');
      return false;
    }
    array_push($game_option_list, 'dummy_boy', 'quiz');
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $gm_password;
  }
  else{
    //身代わり君関連のチェック
    if($ROOM_CONF->dummy_boy && @$_POST['dummy_boy'] == 'on'){
      $game_option_list[]       = 'dummy_boy';
      $dummy_boy_handle_name    = '身代わり君';
      $dummy_boy_password       = $SERVER_CONF->system_password;
      $check_option_role_list[] = 'gerd';
    }
    elseif($ROOM_CONF->dummy_boy && @$_POST['dummy_boy'] == 'gm_login'){
      $gm_password = @$_POST['gm_password']; //GM ログインパスワードをチェック
      EscapeStrings($gm_password);
      if($gm_password == ''){
        OutputRoomAction('no_password');
        return false;
      }
      array_push($game_option_list, 'dummy_boy', 'gm_login');
      $dummy_boy_handle_name    = 'GM';
      $dummy_boy_password       = $gm_password;
      $check_option_role_list[] = 'gerd';
    }

    if($chaos || $chaosfull || $chaos_hyper || $chaos_verso){ //闇鍋モード
      $game_option_list[] = @$_POST['special_role'];
      $check_game_option_list[] = 'secret_sub_role';
      array_push($check_option_role_list, 'topping', 'boost_rate', 'chaos_open_cast',
                 'sub_role_limit');
    }
    elseif($special_role){ //特殊配役モード
      $option_role_list[] = @$_POST['special_role'];
    }
    else{ //通常村
      array_push($check_option_role_list, 'poison', 'assassin', 'wolf', 'boss_wolf', 'poison_wolf',
                 'possessed_wolf', 'sirius_wolf', 'fox', 'child_fox');
      if(! $full_cupid) $check_option_role_list[] = 'cupid';
      $check_option_role_list[] = 'medium';
      if(! $full_mania) $check_option_role_list[] = 'mania';
      if(! $perverseness) array_push($check_option_role_list, 'decide', 'authority');
    }
    array_push($check_game_option_list, 'deep_sleep', 'blinder', 'mind_open', 'joker',
               'death_note', 'weather', 'festival');
    if(! $special_role) $check_option_role_list[] = 'detective';
    array_push($check_option_role_list, 'liar', 'gentleman', 'critical',
               $perverseness ? 'perverseness' : 'sudden_death', 'replace_human', 'change_common',
               'change_mad', 'change_cupid');
  }

  //PrintData($_POST, 'Post');
  //PrintData($check_game_option_list, 'CheckGameOption');
  foreach($check_game_option_list as $option){
    if(! $ROOM_CONF->$option) continue;

    switch($option){
    case 'not_open_cast':
      switch($target = @$_POST[$option]){
      case 'not':
      case 'auto':
        $option = $target . '_open_cast';
        if($ROOM_CONF->$option) break 2;
      }
      continue 2;

    default:
      if(@$_POST[$option] != 'on') continue 2;
    }
    $game_option_list[] = $option;
  }
  //PrintData($game_option_list);


  //PrintData($check_option_role_list, 'CheckOptionRole');
  foreach($check_option_role_list as $option){
    if(! $ROOM_CONF->$option) continue;

    switch($option){
    case 'replace_human':
    case 'change_common':
    case 'change_mad':
    case 'change_cupid':
      $target = @$_POST[$option];
      if(empty($target) || ! $ROOM_CONF->$target ||
         ! in_array($target, $ROOM_CONF->{$option.'_list'})) continue 2;
      $option = $target;
      break;

    case 'topping':
    case 'boost_rate':
      $target = @$_POST[$option];
      if(array_search($target, $ROOM_CONF->{$option.'_list'}) === false) continue 2;
      $option .= ':' . $target;
      break;

    case 'chaos_open_cast':
      switch($target = @$_POST[$option]){
      case 'full':
        break 2;

      case 'camp':
      case 'role':
        $option .= '_' . $target;
        if($ROOM_CONF->$option) break 2;
      }
      continue 2;

    case 'sub_role_limit':
      switch($target = @$_POST[$option]){
      case 'no_sub_role':
      case 'sub_role_limit_easy':
      case 'sub_role_limit_normal':
      case 'sub_role_limit_hard':
        if($ROOM_CONF->$target){
          $option = $target;
          break 2;
        }
      }
      continue 2;

    default:
      if(@$_POST[$option] != 'on') continue 2;
    }
    $option_role_list[] = $option;
  }

  if($ROOM_CONF->real_time && @$_POST['real_time'] == 'on'){
    $day   = @$_POST['real_time_day'];
    $night = @$_POST['real_time_night'];

    //制限時間チェック
    if($day   != '' && ! preg_match('/[^0-9]/', $day)   && $day   > 0 && $day   < 99 &&
       $night != '' && ! preg_match('/[^0-9]/', $night) && $night > 0 && $night < 99){
      $game_option_list[] = 'real_time:' . $day . ':' . $night;
    }
    else{
      OutputRoomAction('time');
      return false;
    }

    $option = 'wait_morning';
    if($ROOM_CONF->$option && @$_POST[$option] == 'on') $game_option_list[] = $option . ':';
  }

  //PrintData($game_option_list, 'GameOption');
  //PrintData($option_role_list, 'OptionRole');
  //OutputHTMLFooter(true);

  //登録
  //ALTER TABLE room_no AUTO_INCREMENT = value; //カウンタセット SQL
  $room_no     = FetchResult('SELECT MAX(room_no) FROM room') + 1; //村番号の最大値を取得
  $game_option = implode(' ', $game_option_list);
  $option_role = implode(' ', $option_role_list);
  $status      = false;
  do{
    if(! $SERVER_CONF->dry_run_mode){
      //村作成
      $items  = 'room_no, name, comment, max_user, game_option, ' .
        'option_role, status, date, scene, vote_count, scene_start_time, last_update_time, ' .
        'establisher_ip, establish_datetime';
      $values = "{$room_no}, '{$room_name}', '{$room_comment}', {$max_user}, '{$game_option}', " .
        "'{$option_role}', 'waiting', 0, 'beforegame', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), " .
        "'{$ip_address}', NOW()";
      if(! InsertDatabase('room', $items, $values)) break;

      //身代わり君を入村させる
      if(in_array('dummy_boy', $game_option_list) &&
         FetchResult('SELECT COUNT(uname) FROM user_entry WHERE room_no = ' . $room_no) == 0){
        if(! InsertUser($room_no, 'dummy_boy', $dummy_boy_handle_name, $dummy_boy_password,
                        1, in_array('gerd', $option_role_list) ? $USER_ICON->gerd : 0)) break;
      }

      if($SERVER_CONF->secret_room){ //村情報非表示モードの処理
        $DB_CONF->Commit();
        OutputRoomAction('success', false, $room_name);
        return true;
      }
    }

    $TWITTER->Send($room_no, $room_name, $room_comment); //Twitter 投稿処理
    //OutputSiteSummary(); //RSS更新 //テスト中

    $DB_CONF->Commit();
    OutputRoomAction('success', false, $room_name);
    $status = true;
  }while(false);

  if(! $status) OutputRoomAction('busy');
  return true;
}

//結果出力 (CreateRoom() 用)
function OutputRoomAction($type, $rollback = true, $str = ''){
  global $SERVER_CONF, $DB_CONF;

  switch($type){
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
    OutputActionResultHeader('村作成', $SERVER_CONF->site_root);
    echo $str . ' 村を作成しました。トップページに飛びます。';
    echo '切り替わらないなら <a href="' . $SERVER_CONF->site_root . '">ここ</a> 。';
    break;
  }
  if($rollback) $DB_CONF->RollBack();
  OutputHTMLFooter(); //フッタ出力
}

//村(room)のwaitingとplayingのリストを出力する
function OutputRoomList(){
  global $SERVER_CONF, $ROOM_IMG;

  if($SERVER_CONF->secret_room) return; //シークレットテストモード

  /* RSS機能はテスト中
  if(! $SERVER_CONF->debug_mode){
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

  //部屋情報を取得
  $delete_header = '<a href="admin/room_delete.php?room_no=';
  $delete_footer = '">[削除 (緊急用)]</a>'."\n";
  $query = 'SELECT room_no, name, comment, game_option, option_role, max_user, status ' .
    "FROM room WHERE status IN ('waiting', 'playing') ORDER BY room_no DESC";
  foreach(FetchAssoc($query) as $stack){
    extract($stack);
    $delete     = $SERVER_CONF->debug_mode ? $delete_header . $room_no . $delete_footer : '';
    $status_img = $ROOM_IMG->Generate($status, $status == 'waiting' ? '募集中' : 'プレイ中');
    $option_img = GenerateGameOptionImage($game_option, $option_role) .
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
  global $SERVER_CONF, $ROOM_CONF, $INIT_CONF;
	$INIT_CONF->LoadClass('GAME_OPT');

  if($SERVER_CONF->disable_establish){
    echo '村作成はできません';
    return;
  }

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>

EOF;

  RoomOptions::OutputView('all', true);
  $password = is_null($SERVER_CONF->room_password) ? '' :
    '<label for="room_password">村作成パスワード</label>：' .
    '<input type="password" id="room_password" name="room_password" size="20">　';
  echo <<<EOF
<tr><td class="make" colspan="2">{$password}<input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
}
