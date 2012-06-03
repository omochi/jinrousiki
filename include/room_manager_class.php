<?php
//-- RoomManager クラス --//
class RoomManager {
  //村のメンテナンス処理
  /* call する位置を調整して、他のサーバから起動されないようにする */
  static function Maintenance() {
    if (ServerConfig::DISABLE_MAINTENANCE) return; //スキップ判定

    //一定時間更新の無い村は廃村にする
    $query = "UPDATE room SET status = 'finished', scene = 'aftergame' " .
      "WHERE STATUS IN ('waiting', 'playing') AND last_update_time < UNIX_TIMESTAMP() - " .
      RoomConfig::DIE_ROOM;
    /*
    //RSS更新 (廃村が 0 の時も必要な処理なので false に限定していない)
    OutputSiteSummary();
    */
    DB::Execute($query);

    //終了した部屋のセッションデータを削除する
    $second = RoomConfig::KEEP_SESSION;
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
  static function Create() {
    global $TWITTER;

    if (ServerConfig::DISABLE_ESTABLISH) {
      HTML::OutputResult('村作成 [制限事項]', '村作成はできません');
    }
    if (Security::CheckReferer('', array('127.0.0.1', '192.168.'))) { //リファラチェック
      HTML::OutputResult('村作成 [入力エラー]', '無効なアクセスです。');
    }
    RQ::Load();

    //-- 入力データのエラーチェック --//
    foreach (array('room_name', 'room_comment') as $str) { //村の名前・説明のデータチェック
      RoomOption::LoadPost($str);
      if (RQ::$get->$str == '') { //未入力チェック
	return self::OutputResult('empty', OptionManager::GenerateCaption($str), false);
      }
      //文字列チェック
      if (strlen(RQ::$get->$str) > RoomConfig::$$str ||
	  preg_match(RoomConfig::NG_WORD, RQ::$get->$str)) {
	return self::OutputResult('comment', OptionManager::GenerateCaption($str), false);
      }
    }

    RoomOption::LoadPost('max_user'); //最大人数チェック
    if (! in_array(RQ::$get->max_user, RoomConfig::$max_user_list)) {
      HTML::OutputResult('村作成 [入力エラー]', '無効な最大人数です。');
    }

    if (! DB::Lock('room')) return self::OutputResult('busy'); //トランザクション開始

    $ip_address = @$_SERVER['REMOTE_ADDR']; //処理実行ユーザの IP を取得
    if (! ServerConfig::DEBUG_MODE) { //デバッグモード時は村作成制限をスキップ
      $room_password = ServerConfig::ROOM_PASSWORD;
      if (isset($room_password)) { //パスワードチェック
	$str = 'room_password';
	RQ::$get->Parse('Escape', 'post.' . $str);
	if (RQ::$get->$str != ServerConfig::ROOM_PASSWORD) {
	  HTML::OutputResult('村作成 [制限事項]', '村作成パスワードが正しくありません。');
	}
      }

      if (Security::CheckBlackList()) { //ブラックリストチェック
	HTML::OutputResult('村作成 [制限事項]', '村立て制限ホストです。');
      }

      $query = "FROM room WHERE status IN ('waiting', 'playing')"; //チェック用の共通クエリ
      $time  = DB::FetchResult("SELECT MAX(establish_datetime) {$query}"); //連続作成制限チェック
      if (isset($time) &&
	  Time::Get() - Time::ConvertTimeStamp($time, false) <= RoomConfig::ESTABLISH_WAIT) {
	return self::OutputResult('establish_wait');
      }

      //最大稼働数チェック
      if (DB::Count("SELECT room_no {$query}") >= RoomConfig::MAX_ACTIVE_ROOM) {
	return self::OutputResult('full');
      }

      //同一ユーザの連続作成チェック
      if (DB::Count("SELECT room_no {$query} AND establisher_ip = '{$ip_address}'") > 0) {
	return self::OutputResult('over_establish');
      }
    }

    //-- ゲームオプションをセット --//
    RoomOption::LoadPost('wish_role', 'real_time');
    if (RQ::$get->real_time) { //制限時間チェック
      $day   = RQ::$get->real_time_day;
      $night = RQ::$get->real_time_night;
      if ($day <= 0 || 99 < $day || $night <= 0 || 99 < $night) return self::OutputResult('time');
      RoomOption::SetOption(RoomOption::GAME_OPTION, sprintf('real_time:%d:%d', $day, $night));
      RoomOption::LoadPost('wait_morning');
    }
    RoomOption::LoadPost(
      'open_vote', 'seal_message', 'open_day', 'dummy_boy_selector', 'not_open_cast_selector',
      'perverseness', 'replace_human_selector', 'special_role');

    if (RQ::$get->quiz) { //クイズ村
      RQ::$get->Parse('Escape', 'post.gm_password'); //GM ログインパスワードをチェック
      if (RQ::$get->gm_password == '') return self::OutputResult('no_password');
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = RQ::$get->gm_password;
      RoomOption::SetOption(RoomOption::GAME_OPTION, 'dummy_boy');
      RoomOption::SetOption(RoomOption::GAME_OPTION, 'gm_login');
    }
    else {
      //身代わり君関連のチェック
      if (RQ::$get->dummy_boy) {
	$dummy_boy_handle_name = '身代わり君';
	$dummy_boy_password    = ServerConfig::PASSWORD;
	RoomOption::LoadPost('gerd');
      }
      elseif (RQ::$get->gm_login) {
	RQ::$get->Parse('Escape', 'post.gm_password'); //GM ログインパスワードをチェック
	if (RQ::$get->gm_password == '') return self::OutputResult('no_password');
	$dummy_boy_handle_name = 'GM';
	$dummy_boy_password    = RQ::$get->gm_password;
	RoomOption::SetOption(RoomOption::GAME_OPTION, 'dummy_boy');
	RoomOption::LoadPost('gerd');
      }

      //闇鍋モード
      if (RQ::$get->chaos || RQ::$get->chaosfull || RQ::$get->chaos_hyper ||
	  RQ::$get->chaos_verso) {
	RoomOption::LoadPost('secret_sub_role', 'topping', 'boost_rate', 'chaos_open_cast',
			     'sub_role_limit');
      }
      elseif (! RQ::$get->duel && ! RQ::$get->gray_random) { //通常村
	RoomOption::LoadPost('poison', 'assassin', 'wolf', 'boss_wolf', 'poison_wolf',
			     'possessed_wolf', 'sirius_wolf', 'fox', 'child_fox', 'medium');
	if (! RQ::$get->full_cupid)   RoomOption::LoadPost('cupid');
	if (! RQ::$get->full_mania)   RoomOption::LoadPost('mania');
	if (! RQ::$get->perverseness) RoomOption::LoadPost('decide', 'authority');
      }

      if (! RQ::$get->perverseness) RoomOption::LoadPost('sudden_death');
      RoomOption::LoadPost(
        'liar', 'gentleman', 'deep_sleep', 'mind_open', 'blinder', 'critical', 'joker',
	'death_note', 'detective', 'weather', 'festival', 'change_common_selector',
	'change_mad_selector', 'change_cupid_selector');
    }

    //登録
    $room_no     = DB::FetchResult('SELECT MAX(room_no) FROM room') + 1; //村番号の最大値を取得
    $game_option = RoomOption::GetOption(RoomOption::GAME_OPTION);
    $option_role = RoomOption::GetOption(RoomOption::ROLE_OPTION);
    //PrintData($_POST, 'Post');
    //PrintData(RQ::$get, 'RQ');
    //PrintData($game_option, 'GameOption');
    //PrintData($option_role, 'OptionRole');
    //HTML::OutputFooter(true); //テスト用

    do {
      if (! ServerConfig::DRY_RUN) {
	//村作成
	$items  = 'room_no, name, comment, max_user, game_option, ' .
	  'option_role, status, date, scene, vote_count, scene_start_time, last_update_time, ' .
	  'establisher_ip, establish_datetime';
	$format = "%d, '%s', '%s', %d, '%s', '%s', 'waiting', 0, 'beforegame', 1, " .
	  "UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '%s', NOW()";
	$values = sprintf($format, $room_no, RQ::$get->room_name, RQ::$get->room_comment,
			  RQ::$get->max_user, $game_option, $option_role, $ip_address);
	if (! DB::Insert('room', $items, $values)) break;

	//身代わり君を入村させる
	if (RQ::$get->dummy_boy &&
	    DB::Count('SELECT uname FROM user_entry WHERE room_no = ' . $room_no) == 0) {
	  if (! DB::InsertUser($room_no, 'dummy_boy', $dummy_boy_handle_name, $dummy_boy_password,
			       1, RQ::$get->gerd ? UserIcon::GERD : 0)) break;
	}
      }

      JinroTwitter::Send($room_no, RQ::$get->room_name, RQ::$get->room_comment); //Twitter 投稿処理
      //OutputSiteSummary(); //RSS更新 //テスト中

      DB::Commit();
      return self::OutputResult('success', RQ::$get->room_name, false);
    } while (false);

    return self::OutputResult('busy');
  }

  //稼働中の村のリストを出力する
  static function OutputList() {
    if (ServerConfig::SECRET_ROOM) return; //シークレットテストモード

    /* RSS (テスト中)
    if (! ServerConfig::DEBUG_MODE){
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
    foreach (DB::FetchAssoc($query) as $stack) {
      extract($stack);
      $delete     = ServerConfig::DEBUG_MODE ? $delete_header . $room_no . $delete_footer : '';
      $status_img = Image::Room()->Generate($status, $status == 'waiting' ? '募集中' : 'プレイ中');
      $option_img = RoomOption::Wrap($game_option, $option_role)->GenerateImageList() .
	Image::GenerateMaxUser($max_user);
      echo <<<EOF
{$delete}<a href="login.php?room_no={$room_no}">
{$status_img}<span>[{$room_no}番地]</span>{$name}村<br>
<div>～{$comment}～ {$option_img}</div>
</a><br>

EOF;
    }
  }

  //部屋作成画面を出力
  static function OutputCreate() {
    if (ServerConfig::DISABLE_ESTABLISH) {
      echo '村作成はできません';
      return;
    }

    echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>

EOF;
    OptionForm::Output();
    $password = is_null(ServerConfig::ROOM_PASSWORD) ? '' :
      '<label for="room_password">村作成パスワード</label>：' .
      '<input type="password" id="room_password" name="room_password" size="20">　';
    echo <<<EOF
<tr><td class="make" colspan="2">{$password}<input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
  }

  //結果出力
  private function OutputResult($type, $str = '', $rollback = true) {
    $status = false;
    switch ($type) {
    case 'empty':
      HTML::OutputResultHeader('村作成 [入力エラー]');
      echo 'エラーが発生しました。<br>';
      echo '以下の項目を再度ご確認ください。<br>';
      echo "<ul><li>{$str}が記入されていない。</li>";
      break;

    case 'comment':
      HTML::OutputResultHeader('村作成 [入力エラー]');
      echo 'エラーが発生しました。<br>';
      echo '以下の項目を再度ご確認ください。<br>';
      echo "<ul><li>{$str}の文字数が長すぎる。</li>";
      echo "<li>{$str}に入力禁止文字列が含まれている。</li></ul>";
      break;

    case 'establish_wait':
      HTML::OutputResultHeader('村作成 [制限事項]');
      echo 'サーバで設定されている村立て許可時間間隔を経過していません。<br>'."\n";
      echo 'しばらく時間を開けてから再度登録してください。';
      break;

    case 'full':
      HTML::OutputResultHeader('村作成 [制限事項]');
      echo '現在プレイ中の村の数がこのサーバで設定されている最大値を超えています。<br>'."\n";
      echo 'どこかの村で決着がつくのを待ってから再度登録してください。';
      break;

    case 'over_establish':
      HTML::OutputResultHeader('村作成 [制限事項]');
      echo 'あなたが立てた村が現在稼働中です。<br>'."\n";
      echo '立てた村の決着がつくのを待ってから再度登録してください。';
      break;

    case 'no_password':
      HTML::OutputResultHeader('村作成 [入力エラー]');
      echo '有効な GM ログインパスワードが設定されていません。';
      break;

    case 'time':
      HTML::OutputResultHeader('村作成 [入力エラー]');
      echo 'エラーが発生しました。<br>';
      echo '以下の項目を再度ご確認ください。<br>';
      echo '<ul><li>リアルタイム制の昼・夜の時間を記入していない。</li>';
      echo '<li>リアルタイム制の昼・夜の時間が 0 以下、または 99 以上である。</li>';
      echo '<li>リアルタイム制の昼・夜の時間を全角で入力している。</li>';
      echo '<li>リアルタイム制の昼・夜の時間が数字ではない。</li></ul>';
      break;

    case 'busy':
      HTML::OutputResultHeader('村作成 [データベースエラー]');
      echo 'データベースサーバが混雑しています。<br>'."\n";
      echo '時間を置いて再度登録してください。';
      break;

    case 'success':
      HTML::OutputResultHeader('村作成', ServerConfig::SITE_ROOT);
      echo $str . ' 村を作成しました。トップページに飛びます。';
      echo '切り替わらないなら <a href="' . ServerConfig::SITE_ROOT . '">ここ</a> 。';
      $status = true;
      break;
    }
    if ($rollback) DB::Rollback();
    HTML::OutputFooter();
    return $status;
  }
}