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
  if($uname == 'dummy_boy') OutputVoteResult('ゲームスタート：身代わり君は投票不要です');

  //投票済みチェック
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0) OutputVoteResult('ゲームスタート：投票済みです');

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
  shuffle($now_role_list); //配列をシャッフル

  $fix_uname_list    = array(); //役割の決定したユーザ名を格納する
  $fix_role_list     = array(); //ユーザ名に対応する役割
  $remain_uname_list = array(); //希望の役割になれなかったユーザ名を一時的に格納

  //フラグセット
  $gerd      = (strpos($game_option, 'gerd')      !== false);
  $quiz      = (strpos($game_option, 'quiz')      !== false);
  $chaos     = (strpos($game_option, 'chaos')     !== false); //chaosfull も含む
  $chaosfull = (strpos($game_option, 'chaosfull') !== false);
  $wish_role = (strpos($game_option, 'wish_role') !== false);

  //エラーメッセージ
  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  //ユーザリストを取得
  //身代わり君の役職を決定
  if(strpos($game_option, 'dummy_boy') !== false){
    // $gerd = true; //デバッグ用
    $count = count($now_role_list);
    for($i = 0; $i < $count; $i++){
      $this_role = array_shift($now_role_list); //配役リストから先頭を抜き出す
      if($gerd)     $fit_role = ($this_role == 'human'); //ゲルト君
      elseif($quiz) $fit_role = ($this_role == 'quiz');  //クイズ村
      else          $fit_role = (! CheckRole($this_role));

      if($fit_role){
	array_push($fix_role_list, $this_role);
	break;
      }
      array_push($now_role_list, $this_role); //配役リストの末尾に戻す
    }

    if(count($fix_role_list) < 1){ //身代わり君に役が与えられているかチェック
      OutputVoteResult($error_header . '身代わり君に役が与えられていません' .
		       $error_footer, true, true);
    }
    array_push($fix_uname_list, 'dummy_boy'); //決定済みリストに身代わり君を追加
    shuffle($now_role_list); //念のためもう一度配列をシャッフル

    $sql_user_list = mysql_query("SELECT uname, role FROM user_entry WHERE room_no = $room_no
					AND uname <> 'dummy_boy' AND user_no > 0 ORDER BY user_no");
  }
  else{
    $sql_user_list = mysql_query("SELECT uname, role FROM user_entry WHERE room_no = $room_no
					AND user_no > 0 ORDER BY user_no");
  }

  //希望役職を参照して一次配役を行う
  while(($user_list_array = mysql_fetch_assoc($sql_user_list)) !== false){
    $this_uname = $user_list_array['uname'];
    $this_role  = array_shift($now_role_list); //配役リストから先頭を抜き出す

    if($wish_role && ! $chaos){ //役割希望制の場合 (闇鍋は希望を無視)
      $this_wish_role = $user_list_array['role']; //希望役職を取得
      $rand = mt_rand(1, 100); //成功判定用乱数
      if($this_role == $this_wish_role && $rand <= $GAME_CONF->wish_role_success){ //希望通り
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list,  $this_role);
      }
      else{ //決まらなかった場合は未決定リスト行き
	array_push($remain_uname_list, $this_uname);
	array_push($now_role_list,  $this_role); //配役リストの末尾に戻す
      }
    }
    else{ //それ以外はそのまま配役決定
      array_push($fix_uname_list, $this_uname);
      array_push($fix_role_list,  $this_role);
    }
  }

  //一次配役の結果を検証
  $remain_uname_list_count = count($remain_uname_list); //未決定者の人数
  $now_role_list_count = count($now_role_list); //残り配役数
  if($remain_uname_list_count != $now_role_list_count){
    OutputVoteResult($error_header . '配役未決定者の人数 (' . $remain_uname_list_count .
		     ') と配役の数 (' . $now_role_list_count . ') が一致していません' .
		     $error_footer, true, true);
  }

  //未決定者を二次配役
  foreach($remain_uname_list as $this_uname){
    array_push($fix_uname_list, $this_uname);
    array_push($fix_role_list, array_shift($now_role_list));
  }

  //二次配役の結果を検証
  $fix_uname_list_count = count($fix_uname_list); //決定者の人数
  if($fix_uname_list_count != $user_count){
    OutputVoteResult($error_header . '村人 (' . $user_count . ') と配役決定者の人数 (' .
		     $fix_uname_list_count . ') が一致していません' .
		     $error_footer, true, true);
  }

  $now_role_list_count = count($now_role_list); //残り配役数
  if($now_role_list_count > 0){
    OutputVoteResult($error_header . '配役リストに余り (' . $now_role_list_count .
		     ') があります' . $error_footer, true, true);
  }

  //兼任となる役割の設定
  $rand_keys = array_rand($fix_role_list, $user_count); //ランダムキーを取得
  $rand_keys_index = 0;
  $sub_role_count_list = array();

  // //サブ役職テスト用
  // $test_role_list = array('plague', 'watcher');
  // for($i = 0; $i < $user_count; $i++){
  //   $this_test_role = array_shift($test_role_list);
  //   if($this_test_role == '') break;
  //   if($fix_uname_list[$i] == 'dummy_boy') continue;
  //   $fix_role_list[$i] .= ' ' . $this_test_role;
  //   $sub_role_count_list[$this_test_role]++;
  // }
  // // $test_role = 'gentleman';
  // for($i = 0; $i < $user_count; $i++){
  //   // if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $test_role;
  //   $test_role = (mt_rand(0, 1) == 1 ? 'gentleman' : 'lady');
  //   $fix_role_list[$i] .= ' ' . $test_role . ' liar';
  // }
  // $sub_role_count_list[$test_role]++;
  // $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $test_role;

  if(strpos($option_role, 'liar') !== false){ //狼少年村
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' liar';
    }
  }
  if(strpos($game_option, 'sudden_death') !== false){ //虚弱体質村
    $sudden_death_list = array('chicken', 'rabbit', 'perverseness');
    for($i = 0; $i < $user_count; $i++){ //全員にショック死系を何かつける
      $rand_key = array_rand($sudden_death_list);
      $fix_role_list[$i] .= ' ' . $sudden_death_list[$rand_key];
    }
  }
  if(strpos($option_role, 'decide') !== false && $user_count >= $GAME_CONF->decide){
    $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' decide';
    $sub_role_count_list['decide']++;
  }
  if(strpos($option_role, 'authority') !== false && $user_count >= $GAME_CONF->authority){
    $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' authority';
    $sub_role_count_list['authority']++;
  }
  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //ランダムなサブ役職のコードリストを作成
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    // $sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //デバッグ用
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      //ランダム割り振り対象外役職をスキップ
      if($key == 'lovers' || $key == 'copied') continue;
      if((int)$sub_role_count_list[$key] > 0) continue; //既に誰かに渡していればスキップ
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
      $sub_role_count_list[$key]++;
    }
  }

  //ゲーム開始
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //今までの投票を全部削除

  //役割をDBに更新
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $entry_uname = $fix_uname_list[$i];
    $entry_role  = $fix_role_list[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    $role_count_list[GetMainRole($entry_role)]++;
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if(strpos($entry_role, $key) !== false) $role_count_list[$key]++;
    }
  }

  //それぞれの役割が何人ずつなのかシステムメッセージ
  if($chaos && strpos($option_role, 'chaos_open_cast') === false)
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
  elseif(strpos($role, 'random_voter') !== false) $vote_number = mt_rand(0, 2); //気分屋

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
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //生きているユーザ数を取得
  $sql_user = mysql_query("SELECT uname, handle_name, role FROM user_entry WHERE room_no = $room_no
				AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false;  //全員が投票していなければ処理スキップ

  $max_voted_number = 0;  //最多得票数
  $vote_kill_target = ''; //処刑される人のユーザ名
  $uname_to_handle_list = array(); //ユーザ名とハンドルネームの対応表
  $uname_to_role_list   = array(); //ユーザ名と役職の対応表
  $live_uname_list      = array(); //生きている人のユーザ名リスト
  $vote_message_list    = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list     = array(); //投票リスト (ユーザ名 => 投票先ハンドルネーム)
  $vote_count_list      = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list         = array(); //能力者たちの投票結果
  $query = " FROM vote WHERE room_no = $room_no AND date = $date AND situation = '$situation' " .
    "AND vote_times = $vote_times "; //共通クエリ

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  while(($array = mysql_fetch_assoc($sql_user)) !== false){ //ユーザ No 順に処理
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    //自分の得票数を取得
    $sql = mysql_query("SELECT SUM(vote_number)" . $query . "AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);
    //特殊サブ役職の得票補正
    if(strpos($this_role, 'upper_luck') !== false){ //雑草魂
      if($date == 2) $this_voted_number += 2;
      else{
	if($this_voted_number > 1) $this_voted_number -= 2;
	else $this_voted_number = 0;
      }
    }
    elseif(strpos($this_role, 'downer_luck') !== false){ //一発屋
      if($date == 2){
	if($this_voted_number > 1) $this_voted_number -= 2;
	else $this_voted_number = 0;
      }
      else $this_voted_number += 2;
    }
    elseif(strpos($this_role, 'star') !== false){ //人気者
      if($this_voted_number > 0) $this_voted_number--;
    }
    elseif(strpos($this_role, 'disfavor') !== false){ //不人気
      $this_voted_number++;
    }

    //自分の投票数を取得
    $sql =mysql_query("SELECT vote_number" . $query . "AND uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //自分が投票した人のハンドルネームを取得
    $sql = mysql_query("SELECT user_entry.handle_name AS handle_name FROM user_entry, vote
			WHERE user_entry.room_no = $room_no AND vote.room_no = $room_no
			AND vote.date = $date AND vote.situation = '$situation'
			AND vote_times = $vote_times AND vote.uname = '$this_uname'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //システムメッセージ用の配列を生成
    $this_message_list = array('handle_name'  => $this_handle,
			       'target'       => $this_vote_target,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //リストにデータを追加
    array_push($live_uname_list, $this_uname);
    $uname_to_handle_list[$this_uname] = $this_handle;
    $uname_to_role_list[$this_uname]   = $this_role;
    $vote_message_list[$this_uname]    = $this_message_list;
    $vote_target_list[$this_uname]     = $this_vote_target;
    $vote_count_list[$this_uname]      = $this_voted_number;
    if(strpos($this_role, 'authority') !== false){ //権力者なら投票先とユーザ名を記録
      $ability_list['authority'] = $this_vote_target;
      $ability_list['authority_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'rebel') !== false){ //反逆者なら投票先とユーザ名を記録
      $ability_list['rebel'] = $this_vote_target;
      $ability_list['rebel_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'decide') !== false) //決定者なら投票先を記録
      $ability_list['decide'] = $this_vote_target;
    elseif(strpos($this_role, 'plague') !== false) //疫病神なら投票先を記録
      $ability_list['plague'] = $this_vote_target;
    elseif(strpos($this_role, 'good_luck') !== false) //幸運ならユーザ名を記録
      $ability_list['good_luck'] = $this_uname;
    elseif(strpos($this_role, 'bad_luck') !== false) //不運ならユーザ名を記録
      $ability_list['bad_luck'] = $this_uname;
  }

  //ハンドルネーム => ユーザ名 の配列を生成
  $handle_to_uname_list = array_flip($uname_to_handle_list);

  //反逆者の判定
  if($ability_list['rebel'] == $ability_list['authority']){
    //権力者と反逆者の投票数を 0 にする
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //投票先の票数補正
    $this_uname = $handle_to_uname_list[$ability_list['rebel']];
    if($vote_message_list[$this_uname]['voted_number'] > 3)
      $vote_message_list[$this_uname]['voted_number'] -= 3;
    else
      $vote_message_list[$this_uname]['voted_number'] = 0;
    $vote_count_list[$this_uname] = $vote_message_list[$this_uname]['voted_number'];
  }

  //投票結果をタブ区切りで生成してシステムメッセージに登録
  // print_r($vote_message_list); //デバッグ用
  foreach($live_uname_list as $this_uname){
    $this_array = $vote_message_list[$this_uname];
    $this_handle       = $this_array['handle_name'];
    $this_target       = $this_array['target'];
    $this_voted_number = $this_array['voted_number'];
    $this_vote_number  = $this_array['vote_number'];

    //最大得票数を更新
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //(誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $this_handle . "\t" . $this_target . "\t" .
      $this_voted_number ."\t" . $this_vote_number . "\t" . (int)$vote_times;
    InsertSystemMessage($sentence, $situation);
  }

  //最大得票数のユーザ名(処刑候補者) のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);

  if(count($max_voted_uname_list) == 1) //一人だけなら処刑者決定
    $vote_kill_target = array_shift($max_voted_uname_list);
  else{ //複数いた場合、サブ役職をチェックする
    $decide_uname = $handle_to_uname_list[$ability_list['decide']]; //決定者の投票先ユーザ名
    $plague_uname = $handle_to_uname_list[$ability_list['plague']]; //疫病神の投票先ユーザ名
    $good_luck_uname = $ability_list['good_luck']; //幸運のユーザ名
    $bad_luck_uname  = $ability_list['bad_luck'];  //不運のユーザ名

    if(in_array($decide_uname, $max_voted_uname_list)) //決定者の投票先がいれば処刑者決定
      $vote_kill_target = $decide_uname;
    elseif(in_array($bad_luck_uname, $max_voted_uname_list)) //処刑者候補に不幸がいれば処刑者決定
      $vote_kill_target = $bad_luck_uname;
    else{
      //幸運を処刑者候補から除く
      $max_voted_uname_list = array_diff($max_voted_uname_list, array($good_luck_uname));
      if(count($max_voted_uname_list) == 1) //この時点で候補が一人なら処刑者決定
	$vote_kill_target = array_shift($max_voted_uname_list);
      else{ //疫病神の投票先を処刑者候補から除く
	$max_voted_uname_list = array_diff($max_voted_uname_list, array($plague_uname));
	if(count($max_voted_uname_list) == 1) //この時点で候補が一人なら処刑者決定
	  $vote_kill_target = array_shift($max_voted_uname_list);
      }
    }
  }

  if($vote_kill_target != ''){ //処刑処理実行
    //ユーザ情報を取得
    $target_handle = $uname_to_handle_list[$vote_kill_target];
    $target_role   = $uname_to_role_list[$vote_kill_target];

    //処刑処理
    KillUser($vote_kill_target); //死亡処理
    InsertSystemMessage($target_handle, 'VOTE_KILLED'); //システムメッセージ
    SaveLastWords($target_handle); //処刑者の遺言

    //処刑された人が毒を持っていた場合
    if(strpos($target_role, 'poison') !== false &&
       strpos($target_role, 'poison_guard') === false){ //騎士は対象外
      $poison_voter_list = array_keys($vote_target_list, $target_handle); //投票した人を取得

      $poison_dead = true; //毒発動フラグを初期化
      foreach($poison_voter_list as $voter_uname){ //薬師のチェック
	if(strpos($uname_to_role_list[$voter_uname], 'pharmacist') !== false){ //解毒成功
	  InsertSystemMessage($uname_to_handle_list[$voter_uname] . "\t" . $target_handle,
			      'PHARMACIST_SUCCESS');
	  $poison_dead = false;
	}
      }

      if($poison_dead){
	if($GAME_CONF->poison_only_voter) //毒の対象オプションをチェック
	  $poison_target_list = $poison_voter_list; //投票者固定
	else{ //完全ランダム
	  //他の人からランダムに一人選ぶ
	  //恋人後追い処理を先にすると後追いした恋人も含めてしまうので
	  //改めて「現在の生存者」を DB に問い合わせるべきじゃないかな？
	  $poison_target_list = array_diff($live_uname_list, array($vote_kill_target));
	}
	$rand_key = array_rand($poison_target_list);
	$poison_target_uname  = $poison_target_list[$rand_key];
	$poison_target_handle = $uname_to_handle_list[$poison_target_uname];
	$poison_target_role   = $uname_to_role_list[$poison_target_uname];

	if(strpos($target_role, 'poison_wolf') !== false &&
	   strpos($poison_target_role, 'wolf') !== false){ //毒狼の毒は狼には無効
	  //仕様が固まってないのでシステムメッセージは保留
	  // InsertSystemMessage($poison_target_handle, 'POISON_WOLF_TARGET');
	  $poison_dead = false;
	}

	if($poison_dead){
	  KillUser($poison_target_uname); //死亡処理
	  InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //システムメッセージ
	  SaveLastWords($poison_target_handle); //遺言処理

	  //毒死した人が恋人の場合
	  if(strpos($poison_target_role, 'lovers') !== false) LoversFollowed($poison_target_role);
	}
      }
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
  //echo "sub<br>\n"; print_r($vote_target_list); echo "<br>\n";
  //print_r($uname_to_role_list); echo "<br>\n";
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($uname_to_handle_list as $this_uname => $this_handle){
    $this_role = $uname_to_role_list[$this_uname];
    //echo "$this_uname, $this_handle, $this_role, $voted_target_member_list[$this_handle]<br>\n";
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
  mysql_query('COMMIT'); //一応コミット
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

  case 'MANIA_DO':
    if(strpos($role, 'mania') === false) OutputVoteResult('夜：神話マニア以外は投票できません');
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君のコピーは無効です');
    break;

  case 'POISON_CAT_DO':
    if(strpos($role, 'poison_cat') === false) OutputVoteResult('夜：猫又以外は投票できません');
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
    $self_shoot = false; //自分撃ちフラグを初期化
    foreach($target_no as $lovers_target_no){
      //投票相手のユーザ情報取得
      $sql = mysql_query("SELECT uname, live FROM user_entry WHERE room_no = $room_no
				AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname = $array['uname'];
      $target_live  = $array['live'];

      //死者、身代わり君への投票は無効
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('死者、身代わり君へは投票できません');

      if($target_uname == $uname) $self_shoot = true; //自分撃ちかどうかチェック
    }

    //ユーザ総数を取得
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot)
      OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
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

    if(strpos($role, 'poison_cat') !== false){ //猫又は自分宛、正者への投票は無効
      if($target_name == $uname || $target_live == 'live')
	OutputVoteResult($error_header . '自分と生者には投票できません');
    }
    else{//自分宛、死者宛、狼同士の投票は無効
      if($target_uname == $uname || $target_live == 'dead' ||
	 (strpos($role, 'wolf') !== false && strpos($target_role, 'wolf') !== false))
	OutputVoteResult($error_header . '自分、死者、狼同士へは投票できません');
    }

    if($situation == 'WOLF_EAT'){
      //クイズ村は GM 以外無効
      if(strpos($game_option, 'quiz') !== false && $target_uname != 'dummy_boy')
	OutputVoteResult($error_header . 'クイズ村では GM 以外に投票できません');

      //狼の初日の投票は身代わり君使用の場合は身代わり君以外無効
      if(strpos($game_option, 'dummy_boy') !== false && $target_uname != 'dummy_boy' && $date == 1)
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
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

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number, situation)
			VALUES($room_no, $date, '$uname', '$target_uname_str', 1, '$situation')");
  InsertSystemMessage($handle_name . "\t" . $target_handle_str, $situation);
  InsertSystemTalk($situation . "\t" . $target_handle_str, $system_time, 'night system', '', $uname);

  //登録成功
  if($sql && mysql_query('COMMIT')){
    CheckVoteNight(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else OutputVoteResult('データベースエラー', true);
}

//夜の集計処理
function CheckVoteNight(){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'REPORTER_DO', 'GUARD_DO', 'CUPID_DO', 'MANIA_DO');
  if(! in_array($situation, $situation_list)) OutputVoteResult('夜：投票エラー');

  //共通クエリをセット
  $query_header = "SELECT COUNT(uname) FROM";
  $query_vote   = "$query_header vote WHERE room_no = $room_no AND date = $date AND situation = ";
  $query_role   = "$query_header user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND role LIKE ";

  $role_count_list = array(); //役職 => 人数 のリスト

  //狼の投票チェック
  $sql = mysql_query($query_vote . "'WOLF_EAT'");
  if(mysql_result($sql, 0, 0) < 1) return false; //狼は全員で一人分

  //占い師の投票チェック
  $sql = mysql_query($query_vote . "'MAGE_DO'");
  $vote_count = mysql_result($sql, 0, 0);

  //生きている占い師の数を取得
  $sql = mysql_query($query_role . "'%mage%'");
  $role_count_list['mage'] = mysql_result($sql, 0, 0);

  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    //初日、身代わり君の役職が占い師の場合はカウントしない
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    if(strpos(mysql_result($sql, 0, 0), 'mage') !== false) $role_count_list['mage']--;
  }
  if($vote_count != (int)$role_count_list['mage']) return false;

  if($date == 1){ //初日のみキューピッド・神話マニアの投票チェック
    $sql = mysql_query($query_vote . "'CUPID_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //生きているキューピッドの数を取得
    $sql = mysql_query($query_role . "'cupid%'");
    $role_count_list['cupid'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['cupid']) return false;

    $sql = mysql_query($query_vote . "'MANIA_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //生きている神話マニアの数を取得
    $sql = mysql_query($query_role . "'mania%'");
    $role_count_list['mania'] = mysql_result($sql, 0, 0);

    //暫定処理。占い師の処理とまとめたい
    if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
      //初日、身代わり君の役割が神話マニアの場合は数に入れない
      $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			  AND uname = 'dummy_boy' AND user_no > 0");
      if(strpos(mysql_result($sql, 0, 0), 'mania') !== false) $role_count_list['mania']--;
    }
    if($vote_count != (int)$role_count_list['mania']) return false;
  }
  else{ //初日以外の狩人・ブン屋の投票チェック
    $sql = mysql_query($query_vote . "'GUARD_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'%guard%'");
    $role_count_list['guard'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['guard']) return false;

    $sql = mysql_query($query_vote . "'REPORTER_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'%reporter%'");
    $role_count_list['reporter'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['reporter']) return false;
  }

  //護衛系共通クエリ
  $query_vote_header = "SELECT vote.target_uname, user_entry.handle_name, user_entry.role " .
    "FROM vote, user_entry WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no " .
    "AND vote.date = $date AND vote.situation = '";
  $query_vote_footer = "' AND vote.uname = user_entry.uname AND user_entry.user_no > 0";

  //狩人のハンドルネームと投票先ユーザ名を取得
  $sql_guard = mysql_query($query_vote_header . 'GUARD_DO' . $query_vote_footer);

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

  $guarded_uname = ''; //護衛された人のユーザ名
  while(($array = mysql_fetch_assoc($sql_guard)) !== false){ //護衛成功判定
    $this_target = $array['target_uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    if($this_target == $wolf_target_uname){ //護衛成功ならメッセージを出力
      InsertSystemMessage($this_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');

      //護衛された人がブン屋の場合は成功メッセージは出るがブン屋は噛まれる (騎士は護衛可能)
      if(strpos($this_role, 'poison_guard') !== false ||
	 strpos($wolf_target_role, 'reporter') === false) $guarded_uname = $this_target;
    }
  }

  if($guarded_uname != '' || strpos($game_option, 'quiz') !== false){ //護衛判定が最優先される
    //護衛成功 or クイズ村仕様
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox') === false){ //襲撃先が妖狐の場合は失敗する
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //護衛されてなければ襲撃成功
    KillUser($wolf_target_uname);
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED');
    SaveLastWords($wolf_target_handle);

    if(strpos($wolf_target_role, 'poison') !== false){ //食べられた人が埋毒者の場合
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
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false) array_push($wolf_list, $wolf);

      $rand_key = array_rand($wolf_list);
      $poison_target_array  = $wolf_list[$rand_key];
      $poison_target_uname  = $poison_target_array['uname'];
      $poison_target_handle = $poison_target_array['handle_name'];
      $poison_target_role   = $poison_target_array['role'];

      KillUser($poison_target_uname);
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night');
      SaveLastWords($poison_target_handle);
      if(strpos($poison_target_role, 'lovers') !== false) //毒死した狼が恋人の場合
	LoversFollowed($poison_target_role);
    }
    if(strpos($wolf_target_role, 'lovers') !== false) //食べられた人が恋人の場合
      LoversFollowed($wolf_target_role);
  }

  //その他の能力者の投票処理
  $query_action_header = "SELECT user_entry.uname, user_entry.handle_name, user_entry.role,
				user_entry.live, vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.uname = user_entry.uname
				AND user_entry.user_no > 0 AND vote.situation = ";

  $sql_mage = mysql_query($query_action_header . "'MAGE_DO'");  //占い師の情報を取得
  while(($array = mysql_fetch_assoc($sql_mage)) !== false){ //占い師の人数分、処理
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //直前に死んでいたら無効

    if(strpos($this_role, 'dummy_mage') !== false) //夢見人の占い結果はランダム
      $this_result = (mt_rand(0, 1) == 0 ? 'human' : 'wolf');
    else{ //占われた人の情報を取得
      $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
				AND uname = '$this_target_uname' AND user_no > 0");
      $array = mysql_fetch_assoc($sql);
      $this_target_handle = $array['handle_name'];
      $this_target_role   = $array['role'];
      $this_target_live   = $array['live'];

      if($this_target_live == 'live' && strpos($this_target_role, 'fox') !== false &&
	 strpos($this_target_role, 'child_fox') === false){ //妖狐が占われたら死亡
	KillUser($this_target_uname);
	InsertSystemMessage($this_target_handle, 'FOX_DEAD');
	SaveLastWords($this_target_handle);
	if(strpos($this_target_role, 'lovers') !== false) //占われた狐が恋人の場合
	  LoversFollowed($this_target_role);
      }

      //占い結果を作成
      if(strpos($this_role, 'soul_mage') !== false) //魂の占い師はメイン役職
	$this_result = GetMainRole($this_target_role);
      else{
	if(strpos($this_target_role, 'boss_wolf') !== false) //白狼は村人判定
	  $this_result = 'human';
	elseif(strpos($this_target_role, 'wolf') !== false ||
	       strpos($this_target_role, 'suspect') !== false) //それ以外の狼と不審者は人狼判定
	  $this_result = 'wolf';
	else
	  $this_result = 'human';
      }
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  $sql_reporter = mysql_query($query_action_header . "'REPORTER_DO'");  //ブン屋の情報を取得
  while(($array = mysql_fetch_assoc($sql_reporter)) !== false){ //ブン屋の人数分、処理
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //直前に死んでいたら無効

    if($this_target_uname == $wolf_target_uname){ //尾行成功
      if($this_target_uname != $guarded_uname){ //護衛されていた場合は何も出ない
	//噛んだ狼のユーザ名を取得
	$sql = mysql_query("SELECT user_entry.handle_name FROM user_entry, vote
				WHERE user_entry.room_no = $room_no
				AND user_entry.uname = vote.uname AND vote.date = $date
				AND vote.situation = 'WOLF_EAT' AND user_no > 0");
	$sentence = $this_handle . "\t" . $wolf_target_handle . "\t" . mysql_result($sql, 0, 0);
	InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
      }
    }
    else{ //尾行した人の情報を取得
      $sql = mysql_query("SELECT role, live FROM user_entry WHERE room_no = $room_no
				AND uname = '$this_target_uname' AND user_no > 0");
      $array = mysql_fetch_assoc($sql);
      $this_target_role = $array['role'];
      $this_target_live = $array['live'];
      if($this_target_live == 'dead') continue; //直前に死んでいたら何も起きない

      if(strpos($this_target_role, 'wolf') !== false || strpos($this_target_role, 'fox') !== false){
	KillUser($this_uname); //狼か狐なら殺される
	InsertSystemMessage($this_handle, 'REPORTER_DUTY');
	if(strpos($this_role, 'lovers') !== false) LoversFollowed($this_role); //恋人後追い処理
      }
    }
  }

  $sql_mania = mysql_query($query_action_header . "'MANIA_DO'"); //神話マニアの情報を取得
  while(($array = mysql_fetch_assoc($sql_mania)) !== false){ //神話マニアの人数分、処理
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //直前に死んでいたら無効

    //神話マニアのターゲットとなった人のハンドルネームと役職を取得
    $sql = mysql_query("SELECT handle_name, role FROM user_entry WHERE room_no = $room_no
			AND uname = '$this_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $this_target_handle = $array['handle_name'];
    $this_target_role   = $array['role'];

    //コピー処理 (神話マニアを指定した場合は村人にする)
    if(($this_result = GetMainRole($this_target_role)) == 'mania' ||
       strpos($this_target_role, 'copied') !== false) $this_result = 'human';
    $this_role = str_replace('mania', $this_result, $this_role) . ' copied';
    mysql_query("UPDATE user_entry SET role = '$this_role' WHERE room_no = $room_no
		 AND uname = '$this_uname' AND user_no > 0");

    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MANIA_RESULT');
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

  for($i = 0; $i < $count; $i++){
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
  for($i = 0; $i < $user_count; $i++){
    $array = mysql_fetch_assoc($sql_user);
    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];

    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n"; //5個ごとに改行
    if($this_live == 'live') //生きていればユーザアイコン
      $path = $ICON_CONF->path . '/' . $this_file;
    else //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;

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
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の投票は無効です');
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
  elseif($role_mania = (strpos($role, 'mania') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の投票は無効です');
    if($date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('MANIA_DO');
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
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'submit_mania_do';
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
?>
