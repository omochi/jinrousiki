<?php
require_once(dirname(__FILE__) . '/include/game_vote_functions.php');

//セッション開始
session_start();
$session_id = session_id();

//引数を取得
$room_no     = (int)$_GET['room_no'];
$auto_reload = (int)$_GET['auto_reload'];
$play_sound  = $_GET['play_sound'];
$list_down   = $_GET['list_down'];

//PHP の引数を作成
$php_argv = 'room_no=' . $room_no;
if($auto_reload > 0)     $php_argv .= '&auto_reload=' . $auto_reload;
if($play_sound  == 'on') $php_argv .= '&play_sound=on';
if($list_down   == 'on') $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">←戻る &amp; reload</a>';

//クッキーからシーンを取得 //DB に問い合わせるので不要
//$day_night = $_COOKIE['day_night'];

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

//ゲームオプション、日付、シーン、ステータスを取得
$sql = mysql_query("SELECT game_option, date, day_night, status FROM room WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$game_option = $array['game_option'];
$date        = $array['date'];
$day_night   = $array['day_night'];
$status      = $array['status'];

//自分のハンドルネーム、役割、生存状態を取得
$sql = mysql_query("SELECT user_no, handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no     = $array['user_no'];
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];

$command = $_POST['command']; //投票ボタンを押した or 投票ページの表示の制御用
$system_time = TZTime(); //現在時刻を取得

if($status == 'finished'){ //ゲームは終了しました
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>ゲームは終了しました<br>'."\n" .
		     $back_url . '</div>');
}

if($live == 'dead'){ //死んでます
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>死者は投票できません<br>'."\n" .
		     $back_url . '</div>');
}

if($command == 'vote'){ //投票処理
  $target_no = $_POST['target_no']; //投票先の user_no
  $situation = $_POST['situation']; //投票の分類 (Kick、処刑、占い、狼など) //SQL インジェクション注意

  if($date == 0){ //ゲーム開始 or Kick 投票処理
    if($situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($situation == 'KICK_DO'){
      //target_no はタイミングで入れ替わる可能性があるので Kick のみ target_handle_name を参照する
      $target_handle_name = $_POST['target_handle_name'];
      EscapeStrings(&$target_handle_name); //エスケープ処理
      VoteKick($target_handle_name);
    }
    else{ //ここに来たらロジックエラー
      OutputActionResult('投票エラー[ゲーム開始前投票]',
			 '<div align="center">' .
			 '<a name="#game_top"></a>プログラムエラーです。'.
			 '管理者に問い合わせてください<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($target_no == 0){
    OutputActionResult('投票エラー',
		       '<div align="center">' .
		       '<a name="#game_top"></a>投票先を指定してください<br>'."\n" .
		       $back_url . '</div>');
  }
  elseif($day_night == 'day'){ //昼の処刑投票処理
    $vote_times = (int)$_POST['vote_times']; //投票回数 (再投票の場合)
    VoteDay();
  }
  elseif($day_night == 'night'){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputActionResult('投票エラー',
		       '<div align="center">' .
		       '<a name="#game_top"></a>プログラムエラーです。管理者に問い合わせてください<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($date == 0){ //ゲーム開始 or Kick 投票ページ出力
  OutputVoteBeforeGame();
}
elseif($day_night == 'day'){ //昼の処刑投票ページ出力
  OutputVoteDay();
}
elseif($day_night == 'night'){ //夜の投票ページ出力
  OutputVoteNight();
}
else{ //既に投票されております //ここに来たらロジックエラーじゃないかな？
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>既に投票されております<br>'."\n" .
		     $back_url . '</div>');
}

DisconnectDatabase($dbHandle); //DB 接続解除

// 関数 //
//投票ページ HTML ヘッダ出力
function OutputVotePageHeader(){
  global $day_night, $php_argv;

  OutputHTMLHeader('汝は人狼なりや？[投票]', 'game');
  if($day_night != '') echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="command" value="vote">

EOF;
}

//ゲーム開始投票の処理
function VoteGameStart(){
  global $room_no, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('ゲームスタート：無効な投票です');

  //投票済みチェック
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0 || $uname == 'dummy_boy')
    OutputVoteResult('ゲームスタート：投票済みです');

  LockTable(); //テーブルを排他的ロック

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, situation)
			VALUES($room_no, 0, '$uname', 'GAMESTART')");
  if($sql && mysql_query('COMMIT')){//一応コミット
    CheckVoteGameStart(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//ゲーム開始投票集計処理
function CheckVoteGameStart(){
  global $GAME_CONF, $MESSAGE, $system_time, $room_no, $game_option, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('ゲームスタート：無効な投票です');

  //投票総数、ゲームオプションを取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);

  //身代わり君使用なら身代わり君の分を加算
  if(strpos($game_option, 'dummy_boy') !== false) $vote_count++;

  //ユーザ総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //役割振り分け
  //決定者、権力者、埋毒者のオプション役割(他と兼任できるもの)を決定
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);

  //役職リストを取得
  $now_role_list = GetRoleList($user_count, $option_role);

  $uname_array    = array(); //役割の決定したユーザ名を格納する
  $role_array     = array(); //ユーザ名に対応する役割
  $re_uname_array = array(); //希望の役割になれなかったユーザ名を一時的に格納

  //フラグセット
  $quiz      = (strpos($game_option, 'quiz')      !== false);
  $chaos     = (strpos($game_option, 'chaos')     !== false); //chaosfull も含む
  $chaosfull = (strpos($game_option, 'chaosfull') !== false);

  //ユーザリストをランダムに取得
  //クイズ村対応 //同じ方法でゲルト君実装できそう
  if($quiz){
    array_push($uname_array, 'dummy_boy');
    array_push($role_array, 'quiz');
    $now_role_list = array_diff($now_role_list, $role_array);

    $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) AS MyRand FROM user_entry
					WHERE room_no = $room_no AND uname <> 'dummy_boy'
					AND user_no > 0 ORDER BY MyRand");
  }
  elseif($DEBUG_MODE){ //ゲルト君
    array_push($uname_array, 'dummy_boy');
    array_push($role_array, 'human'); //村人がいない場合はエラーになるので注意
    $now_role_list = array_diff($now_role_list, $role_array);

    $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) AS MyRand FROM user_entry
					WHERE room_no = $room_no AND uname <> 'dummy_boy'
					AND user_no > 0 ORDER BY MyRand");
  }
  else{
    $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) AS MyRand FROM user_entry
					WHERE room_no = $room_no AND user_no > 0 ORDER BY MyRand");
  }

  for($i = 0; $i < $user_count; $i++){ //希望の役割を選別
    $user_list_array = mysql_fetch_assoc($sql_user_list); //ランダムなユーザ情報を取得
    $this_uname = $user_list_array['uname'];

    //役割希望制の場合、希望を取得
    if(strpos($game_option, 'wish_role') !== false && ! $chaos)
      $this_role = $user_list_array['role'];
    else
      $this_role = 'none';

    if(($this_index = array_search($this_role, $now_role_list)) != false){ //希望どおり
      array_push($uname_array, $this_uname);
      array_push($role_array,  $this_role);

      array_splice($now_role_list, $this_index, 1); //割り振った役割は削除する
    }
    else{ //希望の役割がない
      array_push($re_uname_array, $this_uname);
    }
  }

  $re_count = count($re_uname_array); //役割が決まらなかった人の数
  for($i = 0; $i < $re_count; $i++){ //余った役割を割り当てる
    array_push($uname_array, $re_uname_array[$i]);
    array_push($role_array,  $now_role_list[$i]);
  }

  //兼任となる役割の設定
  $rand_keys = array_rand($role_array, $user_count); //ランダムキーを取得

  //兼任となるオプション役割(決定者、権力者)
  $sub_role_index = 0;
  $sub_role_count_list = array();
  if(strpos($option_role, 'decide') !== false && $user_count >= $GAME_CONF->decide){
    $role_array[$rand_keys[$sub_role_index]] .= ' decide';
    $sub_role_index++;
    $sub_role_count_list['decide']++;
  }
  if(strpos($option_role, 'authority') !== false && $user_count >= $GAME_CONF->authority){
    $role_array[$rand_keys[$sub_role_index]] .= ' authority';
    $sub_role_index++;
    $sub_role_count_list['authority']++;
  }
  if($chaos){
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if($user_count < $sub_role_index) break;
      if($key == 'decite' || $key == 'authority') continue; //決定者と権力者はオプションで制御する
      if($key == 'lovers') continue; //恋人は現在は対象外
      if((int)$sub_role_count_list[$key] > 0) continue; //既に誰かに渡していればスキップ
      $role_array[$rand_keys[$sub_role_index]] .= ' ' . $key;
      $sub_role_index++;
      $sub_role_count_list[$key]++;
    }
  }

  //身代わり君使用の場合、身代わり君は狼、狐、埋毒者、キューピッド以外にする
  if(strpos($game_option, 'dummy_boy') !== false){
    $dummy_boy_index = array_search('dummy_boy', $uname_array); //身代わり君の配列インデックスを取得
    if(CheckRole($role_array[$dummy_boy_index])){
      for($i = 0; $i < $user_count; $i++){
	//狼、狐、埋毒者、キューピッド以外が見つかったら入れ替える
	if(! CheckRole($role_array[$i])){
	  $tmp_role = $role_array[$dummy_boy_index];
	  $role_array[$dummy_boy_index] = $role_array[$i];
	  $role_array[$i] = $tmp_role;
	  break;
	}
      }
      if(CheckRole($role_array[$dummy_boy_index])){ //身代わり君の役職を再度チェック
	if($chaosfull){ //真・闇鍋の時は強制入れ替え
	  $role_array[$dummy_boy_index] = 'human';
	}
	else{
	  OutputVoteResult('ゲームスタート[配役設定エラー]：' .
			   '身代わり君が狼、狐、埋毒者、キューピッドのいずれかになっています。<br>' .
			   '管理者に問い合わせて下さい。', true, true);
	}
      }
    }
  }

  //ゲーム開始
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //今までの投票を全部削除

  //役割をDBに更新
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $entry_uname = $uname_array[$i];
    $entry_role  = $role_array[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    $role_count_list[GetMainRole($entry_role)]++;
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if(strpos($entry_role, $key) !== false) $role_count_list[$key]++;
    }
  }

  //それぞれの役割が何人ずつなのかシステムメッセージ
  if($chaos && ! $GAME_CONF->chaos_open_role)
    $sentence = $MESSAGE->chaos;
  else
    $sentence = MakeRoleNameList($role_count_list);
  InsertSystemTalk($sentence, $system_time, 'night system', 1);  //役割リスト通知
  InsertSystemMessage('1', 'VOTE_TIMES', 1); //初日の処刑投票のカウントを1に初期化(再投票で増える)
  UpdateTime(); //最終書き込み時刻を更新
  if($chaosfull) CheckVictory(); //真・闇鍋はいきなり終了してる可能性あり
  mysql_query('COMMIT'); //一応コミット
}

//開始前の Kick 投票の処理 ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $day_night, $uname, $handle_name, $target_no;

  //エラーチェック
  if($situation != 'KICK_DO') OutputVoteResult('Kick：無効な投票です');
  if($target == '') OutputVoteResult('Kick：投票先を指定してください');
  if($target == '身代わり君') OutputVoteResult('Kick：身代わり君には投票できません');
  if(strpos($game_option, 'quiz') !== false && $target == 'GM')
    OutputVoteResult('Kick：GM には投票できません'); //クイズ村対応

  //投票済みチェック
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND user_entry.handle_name = '$target' AND vote.room_no = $room_no
			AND vote.uname = '$uname' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：' . $target . ' へ Kick 投票済み');

  //自分に投票できません
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND handle_name ='$target' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：自分には投票できません');

  LockTable(); //テーブルを排他的ロック

  //ゲーム開始チェック
  $sql = mysql_query("SELECT day_night FROM room WHERE room_no = $room_no");
  if(mysql_result($sql, 0, 0) != 'beforegame')
    OutputVoteResult('Kick：既にゲームは開始されています', true);

  //ターゲットのユーザ名を取得
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick：'. $target . ' はすでに Kick されています', true);

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, situation)
			VALUES($room_no, 0, '$uname', '$target_uname', 'KICK_DO')");
  InsertSystemTalk("KICK_DO\t" . $target, $system_time, '', 0, $uname); //投票しました通知

  //投票成功
  if($sql && mysql_query('COMMIT')){ //一応コミット
    $vote_count = CheckVoteKick($target); //集計処理
    OutputVoteResult('投票完了：' . $target . '：' . $vote_count . '人目 (Kick するには ' .
		     $GAME_CONF->kick . ' 人以上の投票が必要です)', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//Kick 投票の集計処理 ($target : 対象 HN, 返り値 : 対象 HN の投票合計数)
function CheckVoteKick($target){
  global $GAME_CONF, $MESSAGE, $system_time, $room_no, $situation, $uname;

  if($situation != 'KICK_DO') OutputVoteResult('Kick：無効な投票です');

  //今回投票した相手へ何人投票しているか
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = '$situation' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //投票総数を取得

  //規定数以上の投票があったかキッカーが身代わり君の場合に処理
  if($vote_count < $GAME_CONF->kick && $uname != 'dummy_boy') return $vote_count;

  //ユーザ総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //Kick する人の user_no を取得
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick された人は死亡、user_no を -1、セッション ID を削除する
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = $room_no AND handle_name = '$target' AND user_no > 0");

  // //満員の場合、募集中に戻す //現在は満員時に表示を変えないのでこの処理は不要じゃないかな？
  // mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = $room_no");

  //キックされて空いた場所を詰める
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = $room_no AND user_no = $next");
  }

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$system_time); //出て行ったメッセージ
  InsertSystemTalk($MESSAGE->vote_reset, ++$system_time); //投票リセット通知
  UpdateTime(); //最終書き込み時刻を更新
  DeleteVote(); //今までの投票を全部削除
  mysql_query('COMMIT'); //一応コミット
  return $vote_count;
}

//昼の投票処理
function VoteDay(){
  global $system_time, $room_no, $situation, $date, $vote_times, $uname, $handle_name, $target_no;

  if($situation != 'VOTE_KILL') OutputVoteResult('処刑：投票エラー');

  //投票済みチェック
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation' AND vote_times = $vote_times");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('処刑：投票済み');

  //投票相手のユーザ情報取得
  $sql = mysql_query("SELECT uname, handle_name, live FROM user_entry
			WHERE room_no = $room_no AND user_no = $target_no");
  $array = mysql_fetch_assoc($sql);
  $target_uname  = $array['uname'];
  $target_handle = $array['handle_name'];
  $target_live   = $array['live'];

  //自分宛、死者宛、相手が居ない場合は無効
  if($target_live == 'dead' || $target_uname == $uname || $target_uname == '')
    OutputVoteResult('処刑：投票先が正しくありません');

  LockTable(); //テーブルを排他的ロック

  //投票処理
  //自分の役割を取得
  $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  $role = mysql_result($sql, 0, 0);

  //役職に応じて票数を決定
  $vote_number = 1;
  if(strpos($role, 'authority') !== false) $vote_number++; //権力者
  elseif(strpos($role, 'watcher') !== false) $vote_number = 0; //傍観者

  //投票＆システムメッセージ
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number,
			vote_times, situation)
			VALUES($room_no, $date, '$uname', '$target_uname', $vote_number,
			$vote_times, '$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname);

  //登録成功
  if($sql && mysql_query('COMMIT')){
    CheckVoteDay(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//昼の投票集計処理
function CheckVoteDay(){
  global $system_time, $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('処刑：投票エラー');

  //投票総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = $date AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //生きているユーザ数を取得
  $sql_user = mysql_query("SELECT uname, handle_name, role FROM user_entry WHERE room_no = $room_no
		AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false;  //全員が投票していなければ処理スキップ

  $max_voted_number = 0; //最多得票数
  $handle_list = array(); //ユーザ名とハンドルネームの対応表
  $role_list   = array(); //ユーザ名と役職の対応表
  $live_list   = array(); //生きている人のユーザ名リスト
  $vote_target_list = array(); //投票リスト (ユーザ名 => 投票先ハンドルネーム)
  $vote_count_list  = array(); //得票リスト (ユーザ名 => 投票数)
  $decide_target = ''; //決定者の投票先ハンドルネーム
  $plague_target = ''; //疫病神の投票先ハンドルネーム

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  for($i = 0; $i < $user_count; $i++){ //ユーザ No 順に処理
    $array = mysql_fetch_assoc($sql_user);
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    //自分の得票数を取得
    $sql = mysql_query("SELECT SUM(vote_number) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times
			AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);

    //自分の投票数を取得 //DB に問い合わせなくても役職から算出できるのでは？
    $sql =mysql_query("SELECT vote_number FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times
			AND uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //自分が投票した人のハンドルネームを取得
    $sql = mysql_query("SELECT user_entry.handle_name AS handle_name FROM user_entry, vote
			WHERE user_entry.room_no = $room_no AND vote.room_no = $room_no
			AND vote.date = $date AND vote.situation = '$situation'
			AND vote_times = $vote_times AND vote.uname = '$this_uname'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //投票結果をタブ区切りで生成してシステムメッセージに登録
    //(誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $this_handle . "\t" .  $this_vote_target . "\t" .
      $this_voted_number ."\t" . $this_vote_number . "\t" . (int)$vote_times ;
    InsertSystemMessage($sentence, $situation);

    //最大得票数を更新
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //リストにデータを追加
    $handle_list[$this_uname] = $this_handle;
    $role_list[$this_uname]   = $this_role;
    $vote_target_list[$this_uname] = $this_vote_target;
    $vote_count_list[$this_uname]  = $this_voted_number;
    array_push($live_list, $this_uname);
    if(strpos($this_role, 'decide') !== false) //決定者なら投票先を記録
      $decide_target = $this_vote_target;
    elseif(strpos($this_role, 'plague') !== false) //疫病神なら投票先を記録
      $plague_target = $this_vote_target;
  }

  //ハンドルネーム => ユーザ名 の配列を生成
  $uname_list = array_flip($handle_list);

  //最大得票数を集めた人の数を取得
  $voted_member_list = array_count_values($vote_count_list); //得票数 => 人数 の配列を生成
  $max_voted_member = $voted_member_list[$max_voted_number]; //最大得票数を集めた人の数

  //最大得票数のユーザ名(処刑候補者) のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);

  $vote_kill_target = ''; //処刑される人のユーザ名
  if($max_voted_member == 1) //一人だけなら処刑者決定
    $vote_kill_target = $max_voted_uname_list[0];
  else{ //複数いた場合、サブ役職をチェックする
    $decide_uname = $uname_list[$decide_target]; //決定者の投票先ユーザ名
    if(in_array($decide_uname, $max_voted_uname_list)) //最多投票者に投票していれば処刑者決定
      $vote_kill_target = $decide_uname;
    elseif(count($max_voted_uname_list) < 3){ //疫病神は一人しか出現しない
      //疫病神の投票先を決定者候補から除いて一人になれば処刑者決定
      $plague_uname = $uname_list[$plague_target]; //疫病神の投票先ユーザ名
      $max_voted_uname_list = array_diff($max_voted_uname_list, array($plague_uname));
      if($max_voted_member == 1) $vote_kill_target = $max_voted_uname_list[0];
    }
  }

  if($vote_kill_target != ''){ //処刑処理実行
    //ユーザ情報を取得
    $target_handle = $handle_list[$vote_kill_target];
    $target_role   = $role_list[$vote_kill_target];

    //処刑処理
    KillUser($vote_kill_target); //死亡処理
    InsertSystemMessage($target_handle, 'VOTE_KILLED'); //システムメッセージ
    SaveLastWords($target_handle); //処刑者の遺言

    //処刑された人が埋毒者の場合
    if(strpos($target_role, 'poison') !== false &&
       strpos($target_role, 'poison_guard') === false){ //騎士は対象外
      //他の人からランダムに一人選ぶ
      //恋人後追い処理を先にすると後追いした恋人も含めてしまうので
      //改めて「現在の生存者」を DB に問い合わせるべきじゃないかな？
      $array = array_diff($live_list, array($vote_kill_target));
      $rand_key = array_rand($array, 1);
      $poison_target_uname  = $array[$rand_key];
      $poison_target_handle = $handle_list[$poison_target_uname];
      $poison_target_role   = $role_list[$poison_target_uname];

      KillUser($poison_target_uname); //死亡処理
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //システムメッセージ
      SaveLastWords($poison_target_handle); //遺言処理

      //毒死した人が恋人の場合
      if(strpos($poison_target_role, 'lovers') !== false) LoversFollowed($poison_target_role);
    }

    //処刑された人が恋人の場合
    //処刑後すぐ後追いするのが筋だと思うけど
    //現状では埋毒者のターゲット選出処理が甘いのでここで処理
    if(strpos($target_role, 'lovers') !== false) LoversFollowed($target_role);

    //霊能者の結果(システムメッセージ)
    if(strpos($target_role, 'boss_wolf') !== false)
      $necromancer_result = 'boss_wolf';
    elseif(strpos($target_role, 'wolf') !== false)
      $necromancer_result = 'wolf';
    elseif(strpos($target_role, 'child_fox') !== false)
      $necromancer_result = 'child_fox';
    else
      $necromancer_result = 'human';

    InsertSystemMessage($target_handle . "\t" . $necromancer_result, 'NECROMANCER_RESULT');
  }

  //特殊サブ役職の突然死処理
  //投票者対象ハンドルネーム => 人数 の配列を生成
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($uname_list as $this_uname => $this_handle){
    $this_role = $role_list[$this_uname];
    if($voted_target_member_list[$this_handle] > 0){ //投票されていたら小心者はショック死
      if(strpos($this_role, 'chicken') !== false)
	SuddenDeath($this_uname, $this_handle, $this_role, 'CHICKEN');
    }
    else{ //投票されていなかったらウサギはショック死
      if(strpos($this_role, 'rabbit') !== false)
	SuddenDeath($this_uname, $this_handle, $this_role, 'RABBIT');
    }
    if(strpos($this_role, 'perverseness') !== false){
      //自分の投票先に複数の人が投票していたら天邪鬼はショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1)
	SuddenDeath($this_uname, $this_handle, $this_role, 'PERVERSENESS');
    }
  }

  if($vote_kill_target != ''){ //夜に切り替え
    $check_draw = false; //引き分け判定実行フラグをオフ
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //夜にする
    InsertSystemTalk('NIGHT', ++$system_time, 'night system'); //夜がきた通知
    UpdateTime(); //最終書き込みを更新
    DeleteVote(); //今までの投票を全部削除
    mysql_query('COMMIT'); //一応コミット
  }
  else{ //再投票処理
    $check_draw = true; //引き分け判定実行フラグをオン
    $next_vote_times = $vote_times + 1; //投票回数を増やす
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

    //システムメッセージ
    InsertSystemMessage($vote_times, 'RE_VOTE');
    InsertSystemTalk("再投票になりました( $vote_times 回目)", ++$system_time);
    UpdateTime(); //最終書き込みを更新
  }
  CheckVictory($check_draw);
}

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation, $date,
    $user_no, $uname, $handle_name, $role, $target_no;

  switch($situation){
    case 'WOLF_EAT':
      if(strpos($role, 'wolf') === false) OutputVoteResult('夜：人狼以外は投票できません');
      break;

    case 'MAGE_DO':
      if(strpos($role, 'mage') === false) OutputVoteResult('夜：占い師以外は投票できません');
      if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の占いは無効です');
      break;

    case 'GUARD_DO':
      if(strpos($role, 'guard') === false) OutputVoteResult('夜：狩人以外は投票できません');
      break;

    case 'REPORTER_DO':
      if(strpos($role, 'reporter') === false) OutputVoteResult('夜：ブン屋以外は投票できません');
      break;

    case 'CUPID_DO':
      if(strpos($role, 'cupid') === false) OutputVoteResult('夜：キューピッド以外は投票できません');
      break;

    default:
      OutputVoteResult('夜：あなたは投票できません');
      break;
  }
  CheckAlreadyVote($situation); //投票済みチェック

 //エラーメッセージのヘッダ
  $error_header = '夜：投票先が正しくありません<br>';

  if(strpos($role, 'cupid') !== false){  //キューピッドの場合の投票処理
    if(count($target_no) != 2) OutputVoteResult('夜：指定人数が２人ではありません');
    $self_shoot = false;
    foreach($target_no as $lovers_target_no){
      //投票相手のユーザ情報取得
      $sql = mysql_query("SELECT uname, live FROM user_entry WHERE room_no = $room_no
				AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname = $array['uname'];
      $target_live  = $array['live'];

      //自分打ちかどうかチェック
      if($target_uname == $uname) $self_shoot = true;

      //死者宛、身代わり君への投票は無効
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('死者、身代わり君へは投票できません');
    }

    //ユーザ総数を取得
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
    }
  }
  else{ //キューピッド以外の投票処理
    //投票相手のユーザ情報取得
    $sql = mysql_query("SELECT uname, handle_name, role, live FROM user_entry
			WHERE room_no = $room_no AND user_no = $target_no");
    $array = mysql_fetch_assoc($sql);
    $target_uname  = $array['uname'];
    $target_handle = $array['handle_name'];
    $target_role   = $array['role'];
    $target_live   = $array['live'];

    //自分宛、死者宛、狼同士の投票は無効
    if($target_live == 'dead' || $target_uname == $uname ||
       (strpos($role, 'wolf') !== false && strpos($target_role, 'wolf') !== false)){
      OutputVoteResult($error_header . '死者、自分、狼同士は投票できません');
    }

    if($situation == 'WOLF_EAT'){
      //クイズ村は GM 以外無効
      if(strpos($game_option, 'quiz') !== false && $target_uname != 'dummy_boy'){
	OutputVoteResult($error_header . 'クイズ村では GM 以外に投票できません');
      }

      //狼の初日の投票は身代わり君使用の場合は身代わり君以外無効
      if(strpos($game_option, 'dummy_boy') !== false && $target_uname != 'dummy_boy' && $date == 1){
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
      }
    }
  }

  LockTable(); //テーブルを排他的ロック
  if(strpos($role, 'cupid') !== false){ // キューピッドの処理
    $target_uname_str  = '';
    $target_handle_str = '';
    foreach ($target_no as $lovers_target_no){
      //投票相手のユーザ情報取得
      $sql = mysql_query("SELECT uname, handle_name, role FROM user_entry
				WHERE room_no = $room_no AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname  = $array['uname'];
      $target_handle = $array['handle_name'];
      $target_role   = $array['role'];

      $target_uname_str  .= $target_uname  . ' ';
      $target_handle_str .= $target_handle . ' ';

      //役職に恋人を追加
      $target_role .= ' lovers[' . strval($user_no) . ']';
      mysql_query("UPDATE user_entry SET role = '$target_role' WHERE room_no = $room_no
			AND uname = '$target_uname' AND user_no > 0");
    }
    $target_uname_str  = rtrim($target_uname_str);
    $target_handle_str = rtrim($target_handle_str);
  }
  else{ // キューピッド以外の処理
    $target_uname_str  = $target_uname;
    $target_handle_str = $target_handle;
  }

  //投票
  $sql_vote = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number, situation)
			VALUES($room_no, $date, '$uname', '$target_uname_str', 1, '$situation')");
  //システムメッセージ
  InsertSystemMessage($handle_name . "\t" . $target_handle_str, $situation);
  //投票しました通知
  InsertSystemTalk($situation . "\t" . $target_handle_str, $system_time, 'night system', '', $uname);

  //登録成功
  if($sql_vote && mysql_query('COMMIT')){
    CheckVoteNight(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//夜の集計処理
function CheckVoteNight(){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  if(! ($situation == 'WOLF_EAT' || $situation == 'MAGE_DO' || $situation == 'GUARD_DO' ||
	$situation == 'REPORTER_DO' || $situation == 'CUPID_DO')){
    OutputVoteResult('夜：投票エラー');
  }

  $query_header = "SELECT COUNT(uname) FROM";
  $query_vote   = "$query_header vote WHERE room_no = $room_no AND date = $date AND situation = ";
  $query_role   = "$query_header user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND role LIKE ";

  //狼の投票チェック
  $sql = mysql_query($query_vote . "'WOLF_EAT'");
  if(mysql_result($sql, 0, 0) < 1) return false; //狼は全員で一人分

  //占い師の投票チェック
  $sql = mysql_query($query_vote . "'MAGE_DO'");
  $vote_count = mysql_result($sql, 0, 0);

  //生きている占い師の数を取得
  $sql = mysql_query($query_role . "'%mage%'");
  $mage_count = mysql_result($sql, 0, 0);

  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    //初日、身代わり君の役割が占い師の場合占い師の数に入れない
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    if(strpos(mysql_result($sql, 0, 0), 'mage') !== false) $mage_count--;
  }
  if($vote_count != $mage_count) return false;

  $guard_count    = 0;
  $reporter_count = 0;
  $cupid_count    = 0;
  if($date == 1){ //初日のみキューピッドの投票チェック
    $sql = mysql_query($query_vote . "'CUPID_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //生きているキューピッドの数を取得
    $sql = mysql_query($query_role . "'cupid%'");
    $cupid_count = mysql_result($sql, 0, 0);
    if($vote_count != $cupid_count) return false;
  }
  else{ //初日以外の狩人・ブン屋の投票チェック
    $sql = mysql_query($query_vote . "'GUARD_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'%guard%'");
    $guard_count = mysql_result($sql, 0, 0);
    if($vote_count != $guard_count) return false;

    // $sql = mysql_query($query_vote . "'REPORTER_DO'");
    // $vote_count = mysql_result($sql, 0, 0);
    // 
    // $sql = mysql_query($query_role . "'%reporter%'");
    // $reporter_count = mysql_result($sql, 0, 0);
    // if($vote_count != $reporter_count) return false;
  }

  //狼と狩人・ブン屋は同時に処理
  //共通クエリ
  $query_vote_header = "SELECT vote.target_uname, user_entry.uname user_entry.handle_name
				FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = '";
  $query_vote_footer = "' AND vote.uname = user_entry.uname AND user_entry.user_no > 0";


  //狩人・ブン屋のハンドルネームと投票先ユーザ名を取得
  $sql_guard    = mysql_query($query_vote_header . 'GUARD_DO'    . $query_vote_footer);
  // $sql_reporter = mysql_query($query_vote_header . 'REPORTER_DO' . $query_vote_footer);

  //狼の投票先ユーザ名とその役割を取得
  $sql_wolf = mysql_query("SELECT vote.target_uname, user_entry.handle_name, user_entry.role
				FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'WOLF_EAT'
				AND vote.target_uname = user_entry.uname AND user_entry.user_no > 0");
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_handle = $wolf_target_array['handle_name'];
  $wolf_target_role   = $wolf_target_array['role'];
  // $wolf_target_live   = $wolf_target_array['live'];//DBから引いてないような？？？

  // //ブン屋の尾行リストを作成
  // $reporter_target_list = array(); //尾行対象ユーザ名 => 尾行したブン屋のハンドルネーム
  // for($i = 0; $i < $reporter_count; $i++ ){
  //   $reporter_array  = mysql_fetch_assoc($sql_reporter);
  //   $reporter_target = $reporter_array['target_uname'];
  //   $reporter_handle = $reporter_array['handle_name'];
  //   $reporter_target_list[$reporter_target] = $reporter_handle;
  // }

  $guard_success_flag = false;
  for($i = 0; $i < $guard_count; $i++ ){ //護衛成功かチェック
    $guard_array  = mysql_fetch_assoc($sql_guard);
    $guard_uname  = $guard_array['target_uname'];
    $guard_handle = $guard_array['handle_name'];

    if($guard_uname == $wolf_target_uname){ //護衛成功
      //護衛成功のメッセージ
      InsertSystemMessage($guard_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');
      $guard_success_flag = true;

      // //尾行成功チェック
      // foreach($reporter_target_list as $reporter_target => $repoter_handle){
      // 	if($reporter_target != $guard_uname) continue;
      // 	InsertSystemMessage($reporter_handle . "\t" . $wolf_target_handle, 'REPORT_SUCCESS');
      // }
    }
  }

  if($guard_success_flag || strpos($game_option, 'quiz') !== false){ //護衛判定は狐判定の前に行う仕様
    //護衛成功 or クイズ村仕様
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox') === false){ //食べる先が狐の場合食べれない
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');

    // //ブン屋が尾行していた場合はとばっちりで狼に殺される
    // foreach($reporter_target_list as $reporter_target => $repoter_handle){
    //   if($reporter_target == $wolf_target_uname) ReporterDuty($reporter_handle);
    // }
  }
  else{ //護衛されてなければ食べる
    KillUser($wolf_target_uname); //食べられた人死亡
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED'); //システムメッセージ
    SaveLastWords($wolf_target_handle); //食べられた人の遺言を残す

    //食べられた人が埋毒者の場合
    if(strpos($wolf_target_role, 'poison') !== false){
      if($GAME_CONF->poison_only_eater){ //噛んだ狼を取得
	$sql_wolf_list = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role
					FROM user_entry, vote WHERE user_entry.room_no = $room_no
					AND user_entry.uname = vote.uname AND vote.date = $date
					AND vote.situation = 'WOLF_EAT' AND user_no > 0");
      }
      else{ //生きている狼を取得
	$sql_wolf_list = mysql_query("SELECT uname, handle_name, role FROM user_entry
					WHERE room_no = $room_no AND role LIKE '%wolf%'
					AND live = 'live' AND user_no > 0");
      }
      $wolf_list = array();
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false){
	array_push($wolf_list, $wolf);
      }

      $rand_key = array_rand($wolf_list, 1);
      $poison_target_array  = $wolf_list[$rand_key];
      $poison_target_uname  = $poison_target_array['uname'];
      $poison_target_handle = $poison_target_array['handle_name'];
      $poison_target_role   = $poison_target_array['role'];

      KillUser($poison_target_uname); //死亡処理
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night'); //システムメッセージ
      SaveLastWords($poison_target_handle); //遺言処理
      if(strpos($poison_target_role, 'lovers') !== false)
	LoversFollowed($poison_target_role); //毒死した狼が恋人の場合
    }
    if(strpos($wolf_target_role, 'lovers') !== false)
      LoversFollowed($wolf_target_role); //食べられた人が恋人の場合
  }

  //占い師のユーザ名、ハンドルネームと、占い師の生存、占い師が占ったユーザ名取得
  $sql_mage = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role,
				user_entry.live, vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'MAGE_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //占い師の人数分、処理
  for($i = 0; $i < $mage_count; $i++){
    $array = mysql_fetch_assoc($sql_mage);
    $mage_uname  = $array['uname'];
    $mage_handle = $array['handle_name'];
    $mage_role   = $array['role'];
    $mage_live   = $array['live'];
    $mage_target_uname = $array['target_uname'];

    //直前に死んでいたら占い無効
    if($mage_live == 'dead') continue;

    //占い師に占われた人のハンドルネームと生存、役割を取得
    $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$mage_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $mage_target_handle = $array['handle_name'];
    $mage_target_role   = $array['role'];
    $mage_target_live   = $array['live'];

    if($mage_target_live == 'live' && strpos($mage_target_role, 'fox') !== false &&
       strpos($mage_target_role, 'child_fox') === false){ //狐が占われたら死亡
      KillUser($mage_target_uname);
      InsertSystemMessage($mage_target_handle, 'FOX_DEAD');
      SaveLastWords($mage_target_handle); //占われた狐の遺言を残す
      if(strpos($mage_target_role, 'lovers') !== false)
	LoversFollowed($mage_target_role); //占われた狐が恋人の場合
    }

    //占い結果を出力
    if(strpos($mage_role, 'soul_mage') !== false)
      $mage_result = GetMainRole($mage_target_role);
    else{
      if(strpos($mage_target_role, 'boss_wolf') !== false)
	$mage_result = 'human';
      elseif(strpos($mage_target_role, 'wolf') !== false ||
	     strpos($mage_target_role, 'suspect') !== false)
	$mage_result = 'wolf';
      else
	$mage_result = 'human';
    }
    $sentence = $mage_handle . "\t" . $mage_target_handle . "\t" . $mage_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  //次の日にする
  $next_date = $date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");

  //次の日の処刑投票のカウントを 1 に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //夜が明けた通知
  InsertSystemTalk("MORNING\t" . $next_date, ++$system_time, $location = 'day system', $next_date);
  UpdateTime(); //最終書き込みを更新
  DeleteVote(); //今までの投票を全部削除

  CheckVictory(); //勝敗のチェック
  mysql_query('COMMIT'); //一応コミット
}

//開始前の投票ページ出力
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $day_night, $uname, $php_argv;

  //ユーザ情報を取得
  $sql = mysql_query("SELECT user_entry.uname, user_entry.handle_name,
			user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  $count  = mysql_num_rows($sql);
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_file   = $array['icon_filename'];
    $this_color  = $array['color'];

    //5個ごとに改行
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";
    $location = $ICON_CONF->path . '/' . $this_file;

    //HTML出力
    echo <<<EOF
<td><label for="$this_handle">
<img src="$location" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_uname != 'dummy_boy' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_handle . '" name="target_handle_name" value="' .
	$this_handle . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* Kick するには {$GAME_CONF->kick} 人の投票が必要です</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">←戻る &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE->submit_kick_do}"></form></td>
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="command" value="vote">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$MESSAGE->submit_game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//昼の投票ページを出力する
function OutputVoteDay(){
  global $MESSAGE, $ICON_CONF, $room_no, $date, $uname, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票回数を取得(再投票なら $vote_times は増える)
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND type = 'VOTE_TIMES' AND date = $date");
  $vote_times = (int)mysql_result($sql, 0, 0);

  //投票済みかどうか
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  if(mysql_result($sql, 0, 0)) OutputVoteResult('処刑：投票済み');

  //ユーザ一覧とアイコンのデータ取得
  $sql_user = mysql_query("SELECT user_entry.user_no, user_entry.uname,
			user_entry.handle_name, user_entry.live,
			user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_no > 0 ORDER BY user_entry.user_no");
  $user_count = mysql_num_rows($sql_user); //ユーザ数

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  for($i=0; $i < $user_count; $i++){
    $array = mysql_fetch_assoc($sql_user);
    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];

    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n"; //5個ごとに改行
    if($this_live == 'live'){ //生きていればユーザアイコン
      $path = $ICON_CONF->path . '/' . $this_file;
    }
    else{ //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_live == 'live' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">←戻る &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE->submit_vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//夜の投票ページを出力する
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $game_option,
    $date, $uname, $role, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票済みチェック
  if($role_wolf = (strpos($role, 'wolf') !== false)){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif($role_mage = (strpos($role, 'mage') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の占いは無効です');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_guard = (strpos($role, 'guard') !== false)){
    if($date == 1) OutputVoteResult('夜：初日の護衛はできません');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = (strpos($role, 'reporter') !== false)){
    if($date == 1) OutputVoteResult('夜：初日の尾行はできません');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_cupid = (strpos($role, 'cupid') !== false)){
    if($date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('CUPID_DO');
  }
  else{
    OutputVoteResult('夜：あなたは投票できません');
  }

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && (strpos($game_option, 'dummy_boy') !== false && $date == 1 ||
		    strpos($game_option, 'quiz') !== false)){
    //身代わり君のユーザ情報
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.handle_name,
			user_entry.live, user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.uname = 'dummy_boy' AND user_entry.live = 'live'");
  }
  else{
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.uname, user_entry.handle_name,
			user_entry.live, user_entry.role, user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  }
  $count = mysql_num_rows($sql);
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $cupid_self_shoot = ($count < $GAME_CONF->cupid_self_shoot);

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  for($i = 0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);

    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_role    = $array['role'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];
    $this_wolf    = ($role_wolf && strpos($this_role, 'wolf') !== false);

    //5個ごとに改行
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";
    if($this_live == 'live'){
      if($this_wolf) //狼同士なら狼アイコン
	$path = $ICON_CONF->wolf;
      else //生きていればユーザアイコン
	$path = $ICON_CONF->path . '/' . $this_file;
    }
    else{ //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($role_cupid){
      if($this_uname != 'dummy_boy'){
	$checked = (($cupid_self_shoot && $this_uname == $uname) ? ' checked' : '');
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '"' . $checked . '>'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname && ! $this_wolf){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">←戻る &amp; reload</a></td>

EOF;

  if($role_wolf){
    $type   = 'WOLF_EAT';
    $submit = 'submit_wolf_eat';
  }
  elseif($role_mage){
    $type   = 'MAGE_DO';
    $submit = 'submit_mage_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'submit_guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'submit_reporter_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'submit_cupid_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$MESSAGE->$submit}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//テーブルを排他的ロック
function LockTable(){
  if(! mysql_query("LOCK TABLES room WRITE, user_entry WRITE, vote WRITE,
			system_message WRITE, talk WRITE")){
    OutputVoteResult('サーバが混雑しています。<br>再度投票をお願いします。');
  }
}

//投票する状況があっているかチェック
function CheckDayNight(){
  global $room_no, $day_night, $uname;

  $sql = mysql_query("SELECT last_load_day_night FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != $day_night) OutputVoteResult('戻ってリロードしてください');
}

//投票済みチェック
function CheckAlreadyVote($situation){
  global $room_no, $date, $uname;

  if($situation == 'WOLF_EAT'){
    $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' GROUP BY situation");
    $count = mysql_num_rows($sql);
  }
  else{
    $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation'");
    $count = mysql_result($sql, 0, 0);
  }
  if($count != 0) OutputVoteResult('夜：投票済み');
}

//遺言を取得して保存する ($target : HN)
function SaveLastWords($target){
  global $room_no;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $last_words = mysql_result($sql, 0, 0);
  if($last_words != ''){
    InsertSystemMessage($target . "\t" . $last_words, 'LAST_WORDS');
  }
}

//基本役職を抜き出して返す
function GetMainRole($target_role){
  //基本役職リスト (strpos() を使うので判定順に注意)
  //闇鍋用に 役職 => 出現率 と config に定義するのはどうかな？
  $role_list = array('human', 'boss_wolf', 'wolf', 'soul_mage', 'mage', 'necromancer',
		     'medium', 'fanatic_mad', 'mad', 'poison_guard', 'guard', 'common',
		     'child_fox', 'fox', 'poison', 'cupid', 'quiz');

  foreach($role_list as $this_role){
    if(strpos($target_role, $this_role) !== false) return $this_role;
  }
  return NULL;
}
?>
