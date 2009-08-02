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
$USERS = new Users($room_no); //ユーザ情報をロード
$user_no     = $USERS->UnameToNumber($uname);
$handle_name = $USERS->GetHandleName($uname);
$role        = $USERS->GetRole($uname);
$live        = $USERS->GetLive($uname);
/*
$sql = mysql_query("SELECT user_no, handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no     = $array['user_no'];
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];
*/
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
      EncodePostData(); //ポストされた文字列を全てエンコードする
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
  global $room_no, $game_option, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('ゲームスタート：無効な投票です');
  if(strpos($game_option, 'quiz') === false && $uname == 'dummy_boy'){
    OutputVoteResult('ゲームスタート：身代わり君は投票不要です');
  }

  //投票済みチェック
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0) OutputVoteResult('ゲームスタート：投票済みです');

  LockTable(); //テーブルを排他的ロック

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, situation)
			VALUES($room_no, 0, '$uname', 'GAMESTART')");
  if($sql && mysql_query('COMMIT')){//一応コミット
    AggregateVoteGameStart(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//ゲーム開始投票集計処理
function AggregateVoteGameStart(){
  global $GAME_CONF, $MESSAGE, $USERS, $system_time, $room_no, $game_option, $situation;

  if($situation != 'GAMESTART') OutputVoteResult('ゲームスタート：無効な投票です');

  //投票総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);

  //身代わり君使用なら身代わり君の分を加算
  if(strpos($game_option, 'quiz') === false && strpos($game_option, 'dummy_boy') !== false){
    $vote_count++;
  }

  //ユーザ総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- 配役決定ルーチン --//
  //配役設定オプションの情報を取得
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);

  //配役決定用変数をセット
  $uname_list        = $USERS->names; //ユーザ名 => user_no の配列
  $role_list         = GetRoleList($user_count, $option_role); //役職リストを取得
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

  if(strpos($game_option, 'dummy_boy') !== false){ //身代わり君の役職を決定
    #$gerd = true; //デバッグ用
    if($gerd || $quiz){ //身代わり君の役職固定オプションをチェック
      if($gerd)     $fit_role = 'human'; //ゲルト君
      elseif($quiz) $fit_role = 'quiz';  //クイズ村

      if(($key = array_search($fit_role, $role_list)) !== false){
	array_push($fix_role_list, $fit_role);
	unset($role_list[$key]);
      }
    }
    else{
      shuffle($role_list); //配列をシャッフル
      $count = count($role_list);
      for($i = 0; $i < $count; $i++){
	$this_role = array_shift($role_list); //配役リストから先頭を抜き出す
	if(strpos($this_role, 'wolf')   === false &&
	   strpos($this_role, 'fox')    === false &&
	   strpos($this_role, 'poison') === false &&
	   strpos($this_role, 'cupid')  === false){
	  array_push($fix_role_list, $this_role);
	  break;
	}
	array_push($role_list, $this_role); //配役リストの末尾に戻す
      }
    }

    if(count($fix_role_list) < 1){ //身代わり君に役が与えられているかチェック
      $sentence = '身代わり君に役が与えられていません';
      OutputVoteResult($error_header . $sentence . $error_footer, true, true);
    }
    array_push($fix_uname_list, 'dummy_boy'); //決定済みリストに身代わり君を追加
    unset($uname_list['dummy_boy']); //身代わり君を削除
  }

  //ユーザリストをランダムに取得
  $uname_list = array_keys($uname_list);
  shuffle($uname_list);

  //希望役職を参照して一次配役を行う
  if($wish_role && ! $chaos){ //役割希望制の場合 (闇鍋は希望を無視)
    foreach($uname_list as $this_uname){
      $this_role = $USERS->GetRole($this_uname); //希望役職を取得
      $role_key  = array_search($this_role, $role_list); //希望役職の存在チェック
      if($role_key !== false && mt_rand(1, 100) <= $GAME_CONF->wish_role_rate){ //希望通り
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list, $this_role);
	unset($role_list[$role_key]);
      }
      else{ //決まらなかった場合は未決定リスト行き
	array_push($remain_uname_list, $this_uname);
      }
    }
  }
  else{
    shuffle($role_list); //配列をシャッフル
    $fix_uname_list = array_merge($fix_uname_list, $uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //残り配役リストをリセット
  }

  //一次配役の結果を検証
  $remain_uname_list_count = count($remain_uname_list); //未決定者の人数
  $role_list_count         = count($role_list); //残り配役数
  if($remain_uname_list_count != $role_list_count){
    $uname_str = '配役未決定者の人数 (' . $remain_uname_list_count . ') ';
    $role_str  = '残り配役の数 (' . $role_list_count . ') ';
    $sentence  = $uname_str . 'と' . $role_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //未決定者を二次配役
  if($remain_uname_list_count > 0){
    shuffle($role_list); //配列をシャッフル
    $fix_uname_list = array_merge($fix_uname_list, $remain_uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //残り配役リストをリセット
  }

  //二次配役の結果を検証
  $fix_uname_list_count = count($fix_uname_list); //決定者の人数
  if($user_count != $fix_uname_list_count){
    $user_str  = '村人の人数 (' . $user_count . ') ';
    $uname_str = '配役決定者の人数 (' . $fix_uname_list_count . ') ';
    $sentence  = $user_str . 'と' . $uname_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $fix_role_list_count = count($fix_role_list); //配役の数
  if($fix_uname_list_count != $fix_role_list_count){
    $uname_str = '配役決定者の人数 (' . $fix_uname_list_count . ') ';
    $role_str  = '配役の数 (' . $fix_role_list_count . ') ';
    $sentence  = $uname_str . 'と' . $role_str . 'が一致していません';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $role_list_count = count($role_list); //残り配役数
  if($role_list_count > 0){
    $sentence = '配役リストに余り (' . $role_list_count .') があります';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //兼任となる役割の設定
  $rand_keys = array_rand($fix_role_list, $user_count); //ランダムキーを取得
  $rand_keys_index = 0;
  $sub_role_count_list = array();

  //サブ役職テスト用
  /*
  $test_role_list = array('blinder', 'earplug');
  for($i = 0; $i < $user_count; $i++){
    $this_test_role = array_shift($test_role_list);
    if($this_test_role == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      array_push($test_role_list, $this_test_role);
      continue;
    }
    $fix_role_list[$i] .= ' ' . $this_test_role;
    $sub_role_count_list[$this_test_role]++;
  }
  */
  /*
  $add_sub_role = 'blinder';
  for($i = 0; $i < $user_count; $i++){
    if(mt_rand(1, 100) <= 70){
      $fix_role_list[$i] .= ' ' . $add_sub_role;
      $sub_role_count_list[$add_sub_role]++;
    }
  }
  */

  $now_sub_role_list = array('decide', 'authority'); //オプションでつけるサブ役職のリスト
  foreach($now_sub_role_list as $this_role){
    if(strpos($option_role, $this_role) !== false && $user_count >= $GAME_CONF->$this_role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //狼少年村
    $this_role = 'liar';
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70){
	$fix_role_list[$i] .= ' ' . $this_role;
	$sub_role_count_list[$this_role]++;
      }
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //紳士・淑女村
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    for($i = 0; $i < $user_count; $i++){ //全員に性別に応じて紳士か淑女をつける
      $this_uname = $fix_uname_list[$i];
      $this_role  = $sub_role_list[$USERS->GetSex($this_uname)];
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if(strpos($option_role, 'sudden_death') !== false){ //虚弱体質村
    $sub_role_list = array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience');
    for($i = 0; $i < $user_count; $i++){ //全員にショック死系を何かつける
      $rand_key = array_rand($sub_role_list);
      $this_role = $sub_role_list[$rand_key];
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
      if($this_role == 'impatience'){ //短気は一人だけ
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  if($quiz){ //クイズ村
    $this_role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //出題者以外に解答者をつける
      if($fix_uname_list[$i] == 'dummy_boy') continue;
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //ランダムなサブ役職のコードリストを作成
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    #$sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //デバッグ用
    $delete_role_list = array('lovers', 'copied', 'panelist'); //割り振り対象外役職のリスト
    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //声変化形をスキップ
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
    UpdateRole($entry_uname, $entry_role);
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
    $vote_count = AggregateVoteKick($target); //集計処理
    OutputVoteResult('投票完了：' . $target . '：' . $vote_count . '人目 (Kick するには ' .
		     $GAME_CONF->kick . ' 人以上の投票が必要です)', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//Kick 投票の集計処理 ($target : 対象 HN, 返り値 : 対象 HN の投票合計数)
function AggregateVoteKick($target){
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
  global $USERS, $system_time, $room_no, $situation, $date, $vote_times,
    $uname, $handle_name, $role, $target_no;

  if($situation != 'VOTE_KILL') OutputVoteResult('処刑：投票エラー');

  //投票済みチェック
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation' AND vote_times = $vote_times");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('処刑：投票済み');

  //投票相手のユーザ情報取得
  $target_uname  = $USERS->NumberToUname($target_no);
  $target_handle = $USERS->GetHandleName($target_uname);
  $target_live   = $USERS->GetLive($target_uname);

  //自分宛、死者宛、相手が居ない場合は無効
  if($target_live == 'dead' || $target_uname == $uname || $target_uname == ''){
    OutputVoteResult('処刑：投票先が正しくありません');
  }
  LockTable(); //テーブルを排他的ロック

  //-- 投票処理 --//
  //役職に応じて票数を決定
  $vote_number = 1;
  if(strpos($role, 'authority') !== false){
    $vote_number++; //権力者
  }
  elseif(strpos($role, 'watcher') !== false || strpos($role, 'panelist') !== false){
    $vote_number = 0; //傍観者・解答者
  }
  elseif(strpos($role, 'random_voter') !== false){
    $vote_number = mt_rand(0, 2); //気分屋
  }

  //投票＆システムメッセージ
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number,
			vote_times, situation)
			VALUES($room_no, $date, '$uname', '$target_uname', $vote_number,
			$vote_times, '$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname);

  //登録成功
  if($sql && mysql_query('COMMIT')){
    AggregateVoteDay(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//昼の投票集計処理
function AggregateVoteDay(){
  global $GAME_CONF, $USERS, $system_time, $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('処刑：投票エラー');

  //投票総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //生きているユーザ数を取得
  $sql_user = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
				AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false; //全員が投票していなければ処理スキップ

  $max_voted_number = 0;  //最多得票数
  $vote_kill_target = ''; //処刑される人のユーザ名
  $live_uname_list   = array(); //生きている人のユーザ名リスト
  $vote_message_list = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list      = array(); //能力者たちの投票結果
  $dead_lovers_list  = array(); //後追いする恋人のリスト
  $query = " FROM vote WHERE room_no = $room_no AND date = $date AND situation = '$situation' " .
    "AND vote_times = $vote_times "; //共通クエリ

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  for($i = 0; $i < $user_count; $i++){ //ユーザ No 順に処理
    $this_uname = mysql_result($sql_user, $i, 0);
    $this_role  = $USERS->GetRole($this_uname);

    //自分の得票数を取得
    $sql = mysql_query("SELECT SUM(vote_number)" . $query . "AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);
    //特殊サブ役職の得票補正
    if(strpos($this_role, 'upper_luck') !== false) //雑草魂
      $this_voted_number += ($date == 2 ? 4 : -2);
    elseif(strpos($this_role, 'downer_luck') !== false) //一発屋
      $this_voted_number += ($date == 2 ? -4 : 2);
    elseif(strpos($this_role, 'random_luck') !== false) //波乱万丈
      $this_voted_number += (mt_rand(1, 5) - 3);
    elseif(strpos($this_role, 'star') !== false) //人気者
      $this_voted_number--;
    elseif(strpos($this_role, 'disfavor') !== false) //不人気
      $this_voted_number++;
    if($this_voted_number < 0) $this_voted_number = 0; //マイナスになっていたら 0 にする

    //自分の投票先の情報を取得
    $sql =mysql_query("SELECT target_uname, vote_number" . $query . "AND uname = '$this_uname'");
    $array = mysql_fetch_assoc($sql);
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_vote_number   = (int)$array['vote_number'];

    //システムメッセージ用の配列を生成
    $this_message_list = array('target'       => $this_target_handle,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //リストにデータを追加
    array_push($live_uname_list, $this_uname);
    $vote_message_list[$this_uname] = $this_message_list;
    $vote_target_list[$this_uname]  = $this_target_uname;
    $vote_count_list[$this_uname]   = $this_voted_number;
    if(strpos($this_role, 'authority') !== false){ //権力者なら投票先とユーザ名を記録
      $ability_list['authority'] = $this_target_uname;
      $ability_list['authority_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'rebel') !== false){ //反逆者なら投票先とユーザ名を記録
      $ability_list['rebel'] = $this_target_uname;
      $ability_list['rebel_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'decide') !== false) //決定者なら投票先を記録
      $ability_list['decide'] = $this_target_uname;
    elseif(strpos($this_role, 'plague') !== false) //疫病神なら投票先を記録
      $ability_list['plague'] = $this_target_uname;
    elseif(strpos($this_role, 'impatience') !== false) //短気なら投票先を記録
      $ability_list['impatience'] = $this_target_uname;
    elseif(strpos($this_role, 'good_luck') !== false) //幸運ならユーザ名を記録
      $ability_list['good_luck'] = $this_uname;
    elseif(strpos($this_role, 'bad_luck') !== false) //不運ならユーザ名を記録
      $ability_list['bad_luck'] = $this_uname;
  }

  //反逆者の判定
  if($ability_list['rebel'] == $ability_list['authority']){
    //権力者と反逆者の投票数を 0 にする
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //投票先の票数補正
    $this_uname = $ability_list['rebel'];
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
    $this_handle       = $USERS->GetHandleName($this_uname);
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
  elseif(in_array($ability_list['decide'], $max_voted_uname_list)) //決定者
    $vote_kill_target = $ability_list['decide'];
  elseif(in_array($ability_list['bad_luck'], $max_voted_uname_list)) //不幸
    $vote_kill_target = $ability_list['bad_luck'];
  elseif(in_array($ability_list['impatience'], $max_voted_uname_list)) //短気
    $vote_kill_target = $ability_list['impatience'];
  else{
    //幸運を処刑者候補から除く
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['good_luck']));
    if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
      $vote_kill_target = array_shift($max_voted_uname_list);
    }
    else{ //疫病神の投票先を処刑者候補から除く
      $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['plague']));
      if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
	$vote_kill_target = array_shift($max_voted_uname_list);
      }
    }
  }

  if($vote_kill_target != ''){ //処刑処理実行
    //ユーザ情報を取得
    $target_handle = $USERS->GetHandleName($vote_kill_target);
    $target_role   = $USERS->GetRole($vote_kill_target);

    //処刑処理
    KillUser($vote_kill_target); //死亡処理
    InsertSystemMessage($target_handle, 'VOTE_KILLED'); //システムメッセージ
    SaveLastWords($target_handle); //処刑者の遺言
    if(strpos($target_role, 'lovers') !== false){ //処刑された人が恋人の場合
      array_push($dead_lovers_list, $target_role);
    }

    //処刑者を生存者リストから除く
    $live_uname_list = array_diff($live_uname_list, array($vote_kill_target));

    //処刑された人が毒を持っていた場合
    $poison_dead = true; //毒発動フラグを初期化
    $pharmacist_success = false; //解毒成功フラグを初期化
    do{
      if(strpos($target_role, 'poison') === false) break; //毒を持っていなければ発動しない
      if(strpos($target_role, 'poison_guard') !== false) break;//騎士は対象外
      if(strpos($target_role, 'dummy_poison') !== false) break;//夢毒者は対象外
      if(strpos($target_role, 'incubate_poison') !== false && $date < 5) break; //潜毒者は 5 日目以降

      $poison_voter_list = array_keys($vote_target_list, $vote_kill_target); //投票した人を取得
      foreach($poison_voter_list as $voter_uname){ //薬師のチェック
	if(strpos($USERS->GetRole($voter_uname), 'pharmacist') === false) continue;

	//解毒成功
	$sentence = $USERS->GetHandleName($voter_uname) . "\t" . $target_handle;
	InsertSystemMessage($sentence, 'PHARMACIST_SUCCESS');
	$pharmacist_success = true;
      }
      if($pharmacist_success) break;

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $poison_voter_list : $live_uname_list);
      if(strpos($target_role, 'strong_poison') !== false){ //強毒者ならターゲットから村人を除く
	$strong_poison_target_list = array();
	foreach($poison_target_list as $this_uname){
	  $this_role = $USERS->GetRole($this_uname);
	  if(strpos($this_role, 'wolf') !== false || strpos($this_role, 'fox') !== false){
	    array_push($strong_poison_target_list, $this_uname);
	  }
	}
	$poison_target_list = $strong_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      //対象者を決定
      $rand_key = array_rand($poison_target_list);
      $poison_target_uname  = $poison_target_list[$rand_key];
      $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
      $poison_target_role   = $USERS->GetRole($poison_target_uname);

      //不発判定
      if(strpos($target_role, 'poison_wolf') !== false &&
	 strpos($poison_target_role, 'wolf') !== false){ //毒狼の毒は狼には無効
	//仕様が固まってないのでシステムメッセージは保留
	// InsertSystemMessage($poison_target_handle, 'POISON_WOLF_TARGET');
	break;
      }
      if(strpos($target_role, 'poison_fox') !== false &&
	 strpos($poison_target_role, 'fox') !== false){ //管狐の毒は狐には無効
	break;
      }
      if(strpos($poison_target_role, 'resist_wolf') !== false &&
	 strpos($poison_target_role, 'lost_ability') === false){ //能力を持った抗毒狼
	UpdateRole($poison_target_uname, $poison_target_role . ' lost_ability');
	break;
      }

      KillUser($poison_target_uname); //死亡処理
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //システムメッセージ
      SaveLastWords($poison_target_handle); //遺言処理
      if(strpos($poison_target_role, 'lovers') !== false){ //毒死した人が恋人の場合
	array_push($dead_lovers_list, $poison_target_role);
      }
      $poison_dead = false; //無限ループバグ対策
      break;
    }while($poison_dead);

    //霊能系の判定結果
    $sentence = $target_handle . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能者の判定結果
    $necromancer_result = 'human';
    $necromancer_result_list = array('child_fox', 'white_fox', 'boss_wolf', 'wolf');
    foreach($necromancer_result_list as $this_role){
      if(strpos($target_role, $this_role) !== false){
	$necromancer_result = $this_role;
	break;
      }
    }
    InsertSystemMessage($sentence . $necromancer_result, $action);

    //雲外鏡の判定結果
    InsertSystemMessage($sentence . GetMainRole($target_role), 'SOUL_' . $action);

    //夢枕人の判定結果
    array_push($necromancer_result_list, 'human');
    $rand_key = array_rand($necromancer_result_list);
    InsertSystemMessage($sentence . $necromancer_result_list[$rand_key], 'DUMMY_' . $action);
  }

  //特殊サブ役職の突然死処理
  //投票者対象ユーザ名 => 人数 の配列を生成
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($live_uname_list as $this_uname){
    $this_role = $USERS->GetRole($this_uname);
    $this_type = '';

    if(strpos($this_role, 'chicken') !== false){ //小心者は投票されていたらショック死
      if($voted_target_member_list[$this_uname] > 0) $this_type = 'CHICKEN';
    }
    elseif(strpos($this_role, 'rabbit') !== false){ //ウサギは投票されていなかったらショック死
      if($voted_target_member_list[$this_uname] == 0) $this_type = 'RABBIT';
    }
    elseif(strpos($this_role, 'perverseness') !== false){
      //天邪鬼は自分の投票先に複数の人が投票していたらショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $this_type = 'PERVERSENESS';
    }
    elseif(strpos($this_role, 'flattery') !== false){
      //ゴマすりは自分の投票先に他の人が投票していなければショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $this_type = 'FLATTERY';
    }
    elseif(strpos($this_role, 'impatience') !== false){
      if($vote_kill_target == '') $this_type = 'IMPATIENCE'; //短気は再投票ならショック死
    }
    elseif(strpos($this_role, 'panelist') !== false){ //解答者は出題者に投票したらショック死
      if($vote_target_list[$this_uname] == 'dummy_boy') $this_type = 'PANELIST';
    }

    if($this_type == '') continue;
    SuddenDeath($this_uname, $this_type);
    if(strpos($this_role, 'lovers') !== false) array_push($dead_lovers_list, $this_role);
  }
  foreach($dead_lovers_list as $this_role) LoversFollowed($this_role); //恋人後追い処理

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
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation, $date,
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

  case 'CHILD_FOX_DO':
    if(strpos($role, 'child_fox') === false) OutputVoteResult('夜：子狐以外は投票できません');
    // if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の占いは無効です');
    break;

  case 'MANIA_DO':
    if(strpos($role, 'mania') === false) OutputVoteResult('夜：神話マニア以外は投票できません');
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君のコピーは無効です');
    break;

  case 'POISON_CAT_DO':
    if(strpos($role, 'poison_cat') === false) OutputVoteResult('夜：猫又以外は投票できません');
    if(strpos($game_option, 'not_open_cast') === false)
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
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
      $target_uname = $USERS->NumberToUname($lovers_target_no);
      $target_live  = $USERS->GetLive($target_uname);

      //死者、身代わり君への投票は無効
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('死者、身代わり君へは投票できません');

      if($target_uname == $uname) $self_shoot = true; //自分撃ちかどうかチェック
    }

    //ユーザ総数を取得
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
    }
  }
  else{ //キューピッド以外の投票処理
    //投票相手のユーザ情報取得
    $target_uname  = $USERS->NumberToUname($target_no);
    $target_handle = $USERS->GetHandleName($target_uname);
    $target_role   = $USERS->GetRole($target_uname);
    $target_live   = $USERS->GetLive($target_uname);

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
      $target_uname  = $USERS->NumberToUname($lovers_target_no);
      $target_handle = $USERS->GetHandleName($target_uname);
      $target_role   = $USERS->GetRole($target_uname);
      $target_uname_str  .= $target_uname  . ' ';
      $target_handle_str .= $target_handle . ' ';

      //役職に恋人を追加
      UpdateRole($target_uname, $target_role . ' lovers[' . strval($user_no) . ']');
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
    AggregateVoteNight(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else OutputVoteResult('データベースエラー', true);
}

//夜の役職の投票状況をチェックして投票結果を返す
function CheckVoteNight($action, $role, $dummy_boy_role = ''){
  global $room_no, $game_option, $date;

  //投票情報を取得
  $sql_vote = mysql_query("SELECT uname, target_uname FROM vote WHERE room_no = $room_no
				AND  date = $date AND situation = '$action'");
  $vote_count = mysql_num_rows($sql_vote); //投票人数を取得

  //狼の噛みは一人で OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $sql_vote : false);

  //生きている対象役職の人数をカウント
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND role LIKE '{$role}%' AND live = 'live' AND user_no > 0");
  $role_count = mysql_result($sql, 0, 0);

  //初日、身代わり君が特定の役職だった場合はカウントしない
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $sql_vote : false);
}

//夜の集計処理
function AggregateVoteNight(){
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'GUARD_DO', 'REPORTER_DO', 'CUPID_DO',
			  'CHILD_FOX_DO', 'MANIA_DO', 'POISON_CAT_DO');
  if(! in_array($situation, $situation_list)) OutputVoteResult('夜：投票エラー');

  //狼の投票チェック
  if(($sql_wolf = CheckVoteNight('WOLF_EAT', '%wolf')) === false) return false;

  //初日、身代わり君が特定の役職だった場合はカウントしない
  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    $this_dummy_boy_role = $USERS->GetRole('dummy_boy');
    $exclude_role_list   = array('mage', 'mania'); //カウント対象外役職リスト

    foreach($exclude_role_list as $this_role){
      if(strpos($this_dummy_boy_role, $this_role) !== false){
	$dummy_boy_role = $this_role;
	break;
      }
    }
  }

  //常に投票できる役職の投票チェック
  if(($sql_mage = CheckVoteNight('MAGE_DO', '%mage', $dummy_boy_role)) === false) return false;
  if(($sql_child_fox = CheckVoteNight('CHILD_FOX_DO', 'child_fox')) === false) return false;

  if($date == 1){ //初日のみ投票できる役職をチェック
    if(($sql_cupid = CheckVoteNight('CUPID_DO', 'cupid')) === false) return false;
    if(($sql_mania = CheckVoteNight('MANIA_DO', 'mania', $dummy_boy_role)) === false) return false;
  }
  else{ //二日目以降投票できる役職をチェック
    if(($sql_guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;
    if(($sql_reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;
    if(($sql_poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat')) === false) return false;
  }

  //狼の投票先ユーザ名とその役割を取得
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $voted_wolf_uname   = $wolf_target_array['uname'];
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_handle = $USERS->GetHandleName($wolf_target_uname);
  $wolf_target_role   = $USERS->GetRole($wolf_target_uname);

  $guarded_uname = ''; //護衛された人のユーザ名
  $dead_uname_list  = array(); //死亡者リスト
  $dead_lovers_list = array(); //恋人後追い対象者リスト
  $hunted_fox_list  = array(); //狩られた狐のリスト

  //狩人の護衛成功判定
  while($sql_guard != '' && ($array = mysql_fetch_assoc($sql_guard)) !== false){
    $this_uname        = $array['uname'];
    $this_target_uname = $array['target_uname'];
    $this_handle       = $USERS->GetHandleName($this_uname);
    $this_role         = $USERS->GetRole($this_uname);
    $this_target_role  = $USERS->GetRole($this_target_uname);

    if(strpos($this_role, 'dummy_guard') !== false){ //夢守人は必ず成功メッセージだけが出る
      $sentence = $this_handle . "\t" . $USERS->GetHandleName($this_target_uname);
      InsertSystemMessage($sentence, 'GUARD_SUCCESS');
      continue;
    }

    if(strpos($this_target_role, 'cursed_fox') !== false){ //天狐護衛なら狩る
      array_push($hunted_fox_list, $this_target_uname);
      $sentence = $this_handle . "\t" . $USERS->GetHandleName($this_target_uname);
      InsertSystemMessage($sentence, 'GUARD_HUNTED');
    }

    if($this_target_uname != $wolf_target_uname) continue; //護衛成功ならメッセージを出力
    InsertSystemMessage($this_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');
    if(strpos($this_role, 'dummy_guard') !== false) continue;

    //護衛された人がブン屋の場合は成功メッセージは出るがブン屋は噛まれる (騎士は護衛可能)
    if(strpos($this_role, 'poison_guard') !== false ||
       strpos($wolf_target_role, 'reporter') === false){
      $guarded_uname = $this_target_uname;
    }
  }

  if($guarded_uname != '' || strpos($game_option, 'quiz') !== false){ //護衛判定が最優先される
    //護衛成功 or クイズ村仕様
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox')  === false &&
	 strpos($wolf_target_role, 'poison_fox') === false &&
	 strpos($wolf_target_role, 'white_fox')  === false){ //襲撃先が妖狐の場合は失敗する
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //護衛されてなければ襲撃成功
    KillUser($wolf_target_uname);
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED');
    SaveLastWords($wolf_target_handle);
    if(strpos($wolf_target_role, 'lovers') !== false){ //食べられた人が恋人の場合
      array_push($dead_lovers_list, $wolf_target_role);
    }
    array_push($dead_uname_list, $wolf_target_uname);

    //噛んだ狼を取得
    $voted_wolf_handle = $USERS->GetHandleName($voted_wolf_uname);
    $voted_wolf_role   = $USERS->GetRole($voted_wolf_uname);

    if(strpos($voted_wolf_role, 'tongue_wolf') !== false &&
       strpos($voted_wolf_role, 'lost_ability') === false){ //能力を持った舌禍狼
      $wolf_target_main_role = GetMainRole($wolf_target_role);
      $sentence = $voted_wolf_handle . "\t" . $wolf_target_handle . "\t" . $wolf_target_main_role;
      InsertSystemMessage($sentence, 'TONGUE_WOLF_RESULT');

      if($wolf_target_main_role == 'human'){ //村人なら能力失効
	UpdateRole($voted_wolf_uname, $voted_wolf_role . ' lost_ability');
      }
    }

    //食べられた人が毒持ちだった場合
    $poison_dead = true; //毒発動フラグを初期化
    do{
      if(strpos($wolf_target_role, 'poison') === false) break; //毒を持っていなければ発動しない
      if(strpos($wolf_target_role, 'dummy_poison') !== false) break;//夢毒者は対象外
      if(strpos($wolf_target_role, 'incubate_poison') !== false && $date < 5) break; //潜毒者は 5 日目以降

      $wolf_list = array();
      if($GAME_CONF->poison_only_eater){ //噛んだ狼を取得
	array_push($wolf_list, $voted_wolf_uname);
      }
      else{ //生きている狼を取得
	$sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
				AND role LIKE '%wolf%' AND live = 'live' AND user_no > 0");
	$count = mysql_num_rows($sql);
	for($i = 0; $i < $count; $i++) array_push($wolf_list, mysql_result($sql, $i, 0));
      }
      $rand_key = array_rand($wolf_list);
      $poison_target_uname  = $wolf_list[$rand_key];
      $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
      $poison_target_role   = $USERS->GetRole($poison_target_uname);

      if(strpos($poison_target_role, 'resist_wolf') !== false &&
	 strpos($poison_target_role, 'lost_ability') === false){ //能力を持った抗毒狼
	UpdateRole($poison_target_uname, $poison_target_role . ' lost_ability');
	break;
      }

      KillUser($poison_target_uname);
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night');
      SaveLastWords($poison_target_handle);
      if(strpos($poison_target_role, 'lovers') !== false){ //毒死した狼が恋人の場合
	array_push($dead_lovers_list, $poison_target_role);
      }
      array_push($dead_uname_list, $poison_target_uname);
      $poison_dead = false; //無限ループバグ対策
      break;
    }while($poison_dead);
  }

  //その他の能力者の投票処理
  /*
    人狼、占い師、ブン屋など、行動結果で死者が出るタイプは判定順に注意

    ケース1) どちらの判定を先に行うかで妖狐の生死が決まる (基本的には人狼の襲撃を優先する)
    人狼   → 占い師
    占い師 → 妖狐

    ケース2) どちらの判定を先に行うかでブン屋の生死が決まる (現在は占い師が先)
    占い師 → 妖狐
    ブン屋 → 妖狐
  */

  //猫又の処理
  while($sql_poison_cat != '' && ($array = mysql_fetch_assoc($sql_poison_cat)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    //蘇生対象者の情報を取得
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    //蘇生処理
    /*
      恋人を蘇生してしまった場合はどうする？
      → 蘇生者リストを確保しておいて、カップルが両方蘇生していたら復活？
      → 恋人が重なっていた場合は一人でも死んでいたら再び連鎖が起きることに
      → この辺の仕様が決まるまで猫又は表に出さないようにすること
     */
    // if(mt_rand(1, 100) <= 100){ //テスト用
    $this_rand = mt_rand(1, 100); //蘇生判定用乱数
    if($this_rand <= 20){
      $this_result = 'success';
      mysql_query("UPDATE user_entry SET live = 'live' WHERE room_no = $room_no
			AND uname = '$this_target_uname' AND user_no > 0");
    }
    else{
      $this_result = 'failed';
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
  }

  //占い師の処理
  while(($array = mysql_fetch_assoc($sql_mage)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    //対象者の情報を取得
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);
    $this_target_live   = $USERS->GetLive($this_target_uname);

    if(strpos($this_role, 'dummy_mage') !== false){ //夢見人の占い結果はランダム
      $this_result = (mt_rand(0, 1) == 0 ? 'human' : 'wolf');
    }
    else{
      if(strpos($this_target_role, 'cursed') !== false){ //呪われている役職を占ったら死亡する
	KillUser($this_uname);
	InsertSystemMessage($this_handle, 'CURSED');
	SaveLastWords($this_handle);
	if(strpos($this_role, 'lovers') !== false){ //呪い殺された占い師が恋人の場合
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
	continue;
      }

      if(strpos($this_role, 'soul_mage') !== false){ //魂の占い師の占い結果はメイン役職
	$this_result = GetMainRole($this_target_role);
      }
      else{
	if($this_target_live == 'live' && strpos($this_target_role, 'fox') !== false &&
	   strpos($this_target_role, 'child_fox') === false &&
	   strpos($this_target_role, 'white_fox') === false){//妖狐が占われたら死亡
	  KillUser($this_target_uname);
	  InsertSystemMessage($this_target_handle, 'FOX_DEAD');
	  SaveLastWords($this_target_handle);
	  if(strpos($this_target_role, 'lovers') !== false){ //占われた狐が恋人の場合
	    array_push($dead_lovers_list, $this_target_role);
	  }
	  array_push($dead_uname_list, $this_target_uname);
	}

	//占い結果を作成
	if(strpos($this_target_role, 'boss_wolf') !== false){ //白狼は村人判定
	  $this_result = 'human';
	}
	elseif(strpos($this_target_role, 'wolf') !== false ||
	       strpos($this_target_role, 'suspect') !== false){ //それ以外の狼と不審者は人狼判定
	  $this_result = 'wolf';
	}
	else{
	  $this_result = 'human';
	}
      }
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  //子狐の処理
  while(($array = mysql_fetch_assoc($sql_child_fox)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    //対象者の情報を取得
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    if(strpos($this_target_role, 'cursed') !== false){ //呪われている役職を占ったら死亡する
      KillUser($this_uname);
      InsertSystemMessage($this_handle, 'CURSED');
      SaveLastWords($this_handle);
      if(strpos($this_role, 'lovers') !== false){ //呪い殺された子狐が恋人の場合
	array_push($dead_lovers_list, $this_role);
      }
      array_push($dead_uname_list, $this_uname);
      continue;
    }

    //占い結果を作成
    if(mt_rand(1, 100) <= 30){ //一定確率で失敗する
      $this_result = 'failed';
    }
    elseif(strpos($this_target_role, 'boss_wolf') !== false){ //白狼は村人判定
      $this_result = 'human';
    }
    elseif(strpos($this_target_role, 'wolf') !== false ||
	   strpos($this_target_role, 'suspect') !== false){ //それ以外の狼と不審者は人狼判定
      $this_result = 'wolf';
    }
    else{
      $this_result = 'human';
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  foreach($hunted_fox_list as $this_uname){ //狩られた天狐の死亡処理
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);

    KillUser($this_uname);
    InsertSystemMessage($this_handle, 'HUNTED_FOX');
    if(strpos($this_role, 'lovers') !== false){ //恋人後追い処理
      array_push($dead_lovers_list, $this_role);
    }
    array_push($dead_uname_list, $this_uname);
  }

  //ブン屋の処理
  while($sql_reporter != '' && ($array = mysql_fetch_assoc($sql_reporter)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    //尾行先の情報を取得
    $this_target_uname = $array['target_uname'];
    if($this_target_uname == $wolf_target_uname){ //尾行成功
      if($this_target_uname == $guarded_uname) continue; //護衛されていた場合は何も出ない
      $voted_wolf_handle = $USERS->GetHandleName($voted_wolf_uname);
      $sentence = $this_handle . "\t" . $wolf_target_handle . "\t" . $voted_wolf_handle;
      InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
    }
    elseif(in_array($this_target_uname, $dead_uname_list)){
      continue; //尾行対象が直前に死んでいたら何も起きない
    }
    else{ //尾行した人の情報を取得
      $this_target_role = $USERS->GetRole($this_target_uname);
      if(strpos($this_target_role, 'wolf') !== false || strpos($this_target_role, 'fox') !== false){
	KillUser($this_uname); //狼か狐なら殺される
	InsertSystemMessage($this_handle, 'REPORTER_DUTY');
	if(strpos($this_role, 'lovers') !== false){ //恋人後追い処理
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
      }
    }
  }

  //神話マニアの処理
  while($sql_mania != '' && ($array = mysql_fetch_assoc($sql_mania)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    //神話マニアのターゲットとなった人のハンドルネームと役職を取得
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    //コピー処理 (神話マニアを指定した場合は村人にする)
    if(($this_result = GetMainRole($this_target_role)) == 'mania' ||
       strpos($this_target_role, 'copied') !== false) $this_result = 'human';
    $this_role = str_replace('mania', $this_result, $this_role) . ' copied';
    UpdateRole($this_uname, $this_role);

    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MANIA_RESULT');
  }

  foreach($dead_lovers_list as $this_role) LoversFollowed($this_role); //恋人後追い処理

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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $day_night, $uname, $php_argv;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_file   = $ICON_CONF->path . '/' . $this_object->icon_filename;
    $this_color  = $this_object->color;

    //HTML出力
    echo <<<EOF
<td><label for="$this_handle">
<img src="$this_file" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_uname != 'dummy_boy' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_handle . '" name="target_handle_name" value="' .
	$this_handle . '">'."\n";
    }
    echo '</label></td>'."\n";
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
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
  global $MESSAGE, $ICON_CONF, $USERS, $room_no, $date, $uname, $php_argv;

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

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_live   = $this_object->live;
    $this_file   = $this_object->icon_filename;
    $this_color  = $this_object->color;

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
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $game_option,
    $date, $uname, $role, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票済みチェック
  if($role_wolf = (strpos($role, 'wolf') !== false)) CheckAlreadyVote('WOLF_EAT');
  elseif($role_mage = (strpos($role, 'mage') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の投票は無効です');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_child_fox = (strpos($role, 'child_fox') !== false)){
    CheckAlreadyVote('CHILD_FOX_DO');
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
  elseif($role_poison_cat = (strpos($role, 'poison_cat') !== false)){
    if($date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    CheckAlreadyVote('POISON_CAT_DO');
  }
  else OutputVoteResult('夜：あなたは投票できません');

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && (strpos($game_option, 'dummy_boy') !== false && $date == 1 ||
		    strpos($game_option, 'quiz') !== false)){
    //身代わり君のユーザ情報
    $this_rows = array(1 => $USERS->rows[1]); //dummy_boy = 1番は保証されている？
  }
  else{
    $this_rows = $USERS->rows;
  }
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $cupid_self_shoot = ($count < $GAME_CONF->cupid_self_shoot);

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  foreach($this_rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_live   = $this_object->live;
    $this_role   = $this_object->role;
    $this_file   = $this_object->icon_filename;
    $this_color  = $this_object->color;
    $this_wolf   = ($role_wolf && strpos($this_role, 'wolf') !== false);

    if($this_live == 'live' || $role_poison_cat){ //猫又は死亡アイコンにしない
      if($this_wolf) //狼同士なら狼アイコン
	$path = $ICON_CONF->wolf;
      else //生きていればユーザアイコン
	$path = $ICON_CONF->path . '/' . $this_file;
    }
    else{
      $path = $ICON_CONF->dead; //死んでれば死亡アイコン
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
    elseif($role_poison_cat){
      if($this_live == 'dead' && $this_uname != $uname && $this_uname != 'dummy_boy'){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname && ! $this_wolf){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
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
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
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
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'submit_poison_cat_do';
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

//役職情報を更新する
function UpdateRole($uname, $role){
  global $room_no;

  mysql_query("UPDATE user_entry SET role = '$role' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
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
