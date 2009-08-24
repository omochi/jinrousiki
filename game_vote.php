<?php
require_once(dirname(__FILE__) . '/include/game_vote_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//セッション開始
session_start();
$session_id = session_id();

//引数を取得
if($_POST['situation'] == 'KICK_DO') EncodePostData(); //KICK 処理対応
$RQ_ARGS = new RequestGameVote();
$room_no = $RQ_ARGS->room_no;

//PHP の引数を作成
$php_argv = 'room_no=' . $room_no;
if($RQ_ARGS->auto_reload > 0) $php_argv .= '&auto_reload=' . $RQ_ARGS->auto_reload;
if($RQ_ARGS->play_sound) $php_argv .= '&play_sound=on';
if($RQ_ARGS->list_down)  $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">←戻る &amp; reload</a>';

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

$ROOM = new RoomDataSet($room_no); //村情報をロード
$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS = new UserDataSet($room_no); //ユーザ情報をロード
$SELF  = $USERS->ByUname($uname); //自分の情報をロード

if($ROOM->is_finished()){ //ゲームは終了しました
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>ゲームは終了しました<br>'."\n" .
		     $back_url . '</div>');
}

if($SELF->is_dead()){ //死んでます
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>死者は投票できません<br>'."\n" .
		     $back_url . '</div>');
}

if($RQ_ARGS->vote){ //投票処理
  if($ROOM->is_beforegame()){ //ゲーム開始 or Kick 投票処理
    if($RQ_ARGS->situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($RQ_ARGS->situation == 'KICK_DO'){
      VoteKick($RQ_ARGS->target_handle_name);
    }
    else{ //ここに来たらロジックエラー
      OutputActionResult('投票エラー[ゲーム開始前投票]',
			 '<div align="center">' .
			 '<a name="#game_top"></a>プログラムエラーです。'.
			 '管理者に問い合わせてください<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputActionResult('投票エラー',
		       '<div align="center">' .
		       '<a name="#game_top"></a>投票先を指定してください<br>'."\n" .
		       $back_url . '</div>');
  }
  elseif($ROOM->is_day()){ //昼の処刑投票処理
    VoteDay();
  }
  elseif($ROOM->is_night()){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputActionResult('投票エラー',
		       '<div align="center">' .
		       '<a name="#game_top"></a>プログラムエラーです。管理者に問い合わせてください<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($ROOM->is_beforegame()){ //ゲーム開始 or Kick 投票ページ出力
  OutputVoteBeforeGame();
}
elseif($ROOM->is_day()){ //昼の処刑投票ページ出力
  OutputVoteDay();
}
elseif($ROOM->is_night()){ //夜の投票ページ出力
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
  global $ROOM, $php_argv;

  OutputHTMLHeader('汝は人狼なりや？[投票]', 'game');
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="vote" value="on">

EOF;
}

//ゲーム開始投票の処理
function VoteGameStart(){
  global $room_no, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->is_dummy_boy() && ! $ROOM->is_quiz()){
    OutputVoteResult('ゲームスタート：身代わり君は投票不要です');
  }

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = 0 " .
    "AND uname = '{$SELF->uname}' AND situation = 'GAMESTART'";
  if(FetchResult($query) > 0) OutputVoteResult('ゲームスタート：投票済みです');

  LockTable(); //テーブルを排他的ロック

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, situation)
			VALUES($room_no, 0, '{$SELF->uname}', 'GAMESTART')");
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
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $USERS;

  CheckSituation('GAMESTART');

  //投票総数を取得
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
    "AND date = 0 AND situation = 'GAMESTART'";
  $vote_count = FetchResult($query);

  //身代わり君使用なら身代わり君の分を加算
  if($ROOM->is_dummy_boy() && ! $ROOM->is_quiz()) $vote_count++;

  //ユーザ総数を取得
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0";
  $user_count = FetchResult($query);

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- 配役決定ルーチン --//
  //配役設定オプションの情報を取得
  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = $room_no");

  //配役決定用変数をセット
  $uname_list        = $USERS->names; //ユーザ名 => user_no の配列
  $role_list         = GetRoleList($user_count, $option_role); //役職リストを取得
  $fix_uname_list    = array(); //役割の決定したユーザ名を格納する
  $fix_role_list     = array(); //ユーザ名に対応する役割
  $remain_uname_list = array(); //希望の役割になれなかったユーザ名を一時的に格納

  //フラグセット
  $gerd      = (strpos($ROOM->game_option, 'gerd')      !== false);
  $chaos     = (strpos($ROOM->game_option, 'chaos')     !== false); //chaosfull も含む
  $chaosfull = (strpos($ROOM->game_option, 'chaosfull') !== false);
  $wish_role = (strpos($ROOM->game_option, 'wish_role') !== false);
  $quiz      = $ROOM->is_quiz();

  //エラーメッセージ
  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  if($ROOM->is_dummy_boy()){ //身代わり君の役職を決定
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
	   strpos($this_role, 'poison') === false){
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
  $delete_role_list = array('lovers', 'copied', 'panelist'); //割り振り対象外役職のリスト

  //サブ役職テスト用
  /*
  $test_role_list = array('blinder', 'speaker');
  $delete_role_list = array_merge($delete_role_list, $test_role_list);
  for($i = 0; $i < $user_count; $i++){
    $this_test_role = array_shift($test_role_list);
    if($this_test_role == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      array_push($test_role_list, $this_test_role);
      continue;
    }
    $fix_role_list[$i] .= ' ' . $this_test_role;
  }
  */
  /*
  $add_sub_role = 'perverseness';
  array_push($delete_role_list, $add_sub_role);
  for($i = 0; $i < $user_count; $i++){
    #if(mt_rand(1, 100) <= 70){
      $fix_role_list[$i] .= ' ' . $add_sub_role;
    #}
  }
  */
  $now_sub_role_list = array('decide', 'authority'); //オプションでつけるサブ役職のリスト
  $delete_role_list  = array_merge($delete_role_list, $now_sub_role_list);
  foreach($now_sub_role_list as $this_role){
    if(strpos($option_role, $this_role) !== false && $user_count >= $GAME_CONF->$this_role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $this_role;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //狼少年村
    $this_role = 'liar';
    array_push($delete_role_list, $this_role);
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $this_role;
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //紳士・淑女村
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員に性別に応じて紳士か淑女をつける
      $this_uname = $fix_uname_list[$i];
      $this_role  = $sub_role_list[$USERS->GetSex($this_uname)];
      $fix_role_list[$i] .= ' ' . $this_role;
    }
  }

  if(strpos($option_role, 'sudden_death') !== false){ //虚弱体質村
    $sub_role_list = array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員にショック死系を何かつける
      $rand_key = array_rand($sub_role_list);
      $this_role = $sub_role_list[$rand_key];
      $fix_role_list[$i] .= ' ' . $this_role;
      if($this_role == 'impatience'){ //短気は一人だけ
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif(strpos($option_role, 'perverseness') !== false){ //天邪鬼村
    $this_role = 'perverseness';
    array_push($delete_role_list, $this_role);
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $this_role;
    }
  }

  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //ランダムなサブ役職のコードリストを作成
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    // $sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //デバッグ用
    // array_push($delete_role_list, 'earplug', 'speaker'); //デバッグ用
    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //声変化形をスキップ
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
    }
  }
  if($quiz){ //クイズ村
    $this_role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //出題者以外に解答者をつける
      if($fix_uname_list[$i] != 'dummy_boy') $fix_role_list[$i] .= ' ' . $this_role;
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
    $this_role_list = explode(' ', $entry_role);
    foreach($this_role_list as $this_role) $role_count_list[$this_role]++;
  }

  //それぞれの役割が何人ずつなのかシステムメッセージ
  if($chaos && strpos($option_role, 'chaos_open_cast') === false){
    $sentence = $MESSAGE->chaos;
    // $sentence = MakeRoleNameList($role_count_list, true);
  }
  else{
    $sentence = MakeRoleNameList($role_count_list);
  }
  InsertSystemTalk($sentence, $ROOM->system_time, 'night system', 1);  //役割リスト通知
  InsertSystemMessage('1', 'VOTE_TIMES', 1); //初日の処刑投票のカウントを1に初期化(再投票で増える)
  UpdateTime(); //最終書き込み時刻を更新
  if($chaosfull) CheckVictory(); //真・闇鍋はいきなり終了してる可能性あり
  mysql_query('COMMIT'); //一応コミット
}

//開始前の Kick 投票の処理 ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $room_no, $ROOM, $SELF;

  //エラーチェック
  CheckSituation('KICK_DO');
  if($target == '') OutputVoteResult('Kick：投票先を指定してください');
  if($target == '身代わり君') OutputVoteResult('Kick：身代わり君には投票できません');
  if(($ROOM->is_quiz() || strpos($ROOM->game_option, 'gm_login') !== false) && $target == 'GM'){
    OutputVoteResult('Kick：GM には投票できません'); //仮想 GM 対応
  }

  //投票済みチェック
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND user_entry.handle_name = '$target' AND vote.room_no = $room_no
			AND vote.uname = '{$SELF->uname}' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：' . $target . ' へ Kick 投票済み');

  //自分に投票できません
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND handle_name ='$target' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：自分には投票できません');

  LockTable(); //テーブルを排他的ロック

  //ゲーム開始チェック
  if(FetchResult("SELECT day_night FROM room WHERE room_no = $room_no") != 'beforegame'){
    OutputVoteResult('Kick：既にゲームは開始されています', true);
  }

  //ターゲットのユーザ名を取得
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick：'. $target . ' はすでに Kick されています', true);

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, situation)
			VALUES($room_no, 0, '{$SELF->uname}', '$target_uname', 'KICK_DO')");
  InsertSystemTalk("KICK_DO\t" . $target, $ROOM->system_time, '', 0, $SELF->uname); //投票しました通知

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
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $SELF;

  CheckSituation('KICK_DO');

  //今回投票した相手へ何人投票しているか
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = 'KICK_DO' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //投票総数を取得

  //規定数以上の投票があったかキッカーが身代わり君の場合に処理
  if($vote_count < $GAME_CONF->kick && ! $SELF->is_dummy_boy()) return $vote_count;

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

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$ROOM->system_time); //出て行ったメッセージ
  InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //投票リセット通知
  UpdateTime(); //最終書き込み時刻を更新
  DeleteVote(); //今までの投票を全部削除
  mysql_query('COMMIT'); //一応コミット
  return $vote_count;
}

//昼の投票処理
function VoteDay(){
  global $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //コマンドチェック

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
    "AND uname = '{$SELF->uname}' AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');

  $target_uname = $USERS->NumberToUname($RQ_ARGS->target_no); //投票先のユーザ名を取得
  if($target_uname == '') OutputVoteResult('処刑：投票先が指定されていません');
  if($target_uname == $SELF->uname) OutputVoteResult('処刑：自分には投票できません');

  $target_user = $USERS->ByUname($target_uname); //投票先のユーザ情報を取得
  if($target_user->is_dead()) OutputVoteResult('処刑：死者には投票できません');

  LockTable(); //テーブルを排他的ロック

  //-- 投票処理 --//
  //役職に応じて票数を決定
  $vote_number = 1;
  if($SELF->is_role('authority')){
    $vote_number++; //権力者
  }
  elseif($SELF->is_role('watcher', 'panelist')){
    $vote_number = 0; //傍観者・解答者
  }
  elseif($SELF->is_role('random_voter')){
    $vote_number = mt_rand(0, 2); //気分屋
  }

  //投票＆システムメッセージ
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number,
			vote_times, situation)
			VALUES($room_no, {$ROOM->date}, '{$SELF->uname}', '$target_uname', $vote_number,
			{$RQ_ARGS->vote_times}, 'VOTE_KILL')");
  $sentence = "VOTE_DO\t" . $target_user->handle_name;
  InsertSystemTalk($sentence, $ROOM->system_time, 'day system', '', $SELF->uname);

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
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS;

  CheckSituation('VOTE_KILL'); //コマンドチェック

  //投票総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date}
			AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}");
  $vote_count = mysql_result($sql, 0, 0);

  //生きているユーザ数を取得
  $sql_user = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
				AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false; //全員が投票していなければ処理スキップ

  $max_voted_number = 0;  //最多得票数
  $vote_kill_uname  = ''; //処刑される人のユーザ名
  $live_uname_list   = array(); //生きている人のユーザ名リスト
  $vote_message_list = array(); //システムメッセージ用 (ユーザ名 => array())
  $vote_target_list  = array(); //投票リスト (ユーザ名 => 投票先ユーザ名)
  $vote_count_list   = array(); //得票リスト (ユーザ名 => 投票数)
  $ability_list      = array(); //能力者たちの投票結果
  $dead_lovers_list  = array(); //後追いする恋人のリスト
  $query = "FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND"; //共通クエリ

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  for($i = 0; $i < $user_count; $i++){ //ユーザ No 順に処理
    $this_user = $USERS->ByUname(mysql_result($sql_user, $i, 0));

    //自分の得票数を取得
    $query_voted_number = "SELECT SUM(vote_number) $query target_uname = '{$this_user->uname}'";
    $this_voted_number = FetchResult($query_voted_number);

    //特殊サブ役職の得票補正
    if($this_user->is_role('upper_luck')) //雑草魂
      $this_voted_number += ($ROOM->date == 2 ? 4 : -2);
    elseif($this_user->is_role('downer_luck')) //一発屋
      $this_voted_number += ($ROOM->date == 2 ? -4 : 2);
    elseif($this_user->is_role('random_luck')) //波乱万丈
      $this_voted_number += (mt_rand(1, 5) - 3);
    elseif($this_user->is_role('star')) //人気者
      $this_voted_number--;
    elseif($this_user->is_role('disfavor')) //不人気
      $this_voted_number++;
    if($this_voted_number < 0) $this_voted_number = 0; //マイナスになっていたら 0 にする

    //自分の投票先の情報を取得
    $sql =mysql_query("SELECT target_uname, vote_number $query uname = '$this_uname'");
    $array = mysql_fetch_assoc($sql);
    $this_target = $USERS->ByUname($array['target_uname']);
    $this_vote_number = (int)$array['vote_number'];

    //システムメッセージ用の配列を生成
    $this_message_list = array('target'       => $this_target->handle_name,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //リストにデータを追加
    array_push($live_uname_list, $this_user->uname);
    $vote_message_list[$this_user->uname] = $this_message_list;
    $vote_target_list[$this_user->uname]  = $this_target->uname;
    $vote_count_list[$this_user->uname]   = $this_voted_number;
    if($this_user->is_role('authority')){ //権力者なら投票先とユーザ名を記録
      $ability_list['authority'] = $this_target->uname;
      $ability_list['authority_uname'] = $this_user->uname;
    }
    elseif($this_user->is_role('rebel')){ //反逆者なら投票先とユーザ名を記録
      $ability_list['rebel'] = $this_target->uname;
      $ability_list['rebel_uname'] = $this_user->uname;
    }
    elseif($this_user->is_role('decide')) //決定者なら投票先を記録
      $ability_list['decide'] = $this_target->uname;
    elseif($this_user->is_role('plague')) //疫病神なら投票先を記録
      $ability_list['plague'] = $this_target->uname;
    elseif($this_user->is_role('impatience')) //短気なら投票先を記録
      $ability_list['impatience'] = $this_target->uname;
    elseif($this_user->is_role('good_luck')) //幸運ならユーザ名を記録
      $ability_list['good_luck'] = $this_user->uname;
    elseif($this_user->is_role('bad_luck')) //不運ならユーザ名を記録
      $ability_list['bad_luck'] = $this_user->uname;
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
      $this_voted_number ."\t" . $this_vote_number . "\t" . $RQ_ARGS->vote_times;
    InsertSystemMessage($sentence, 'VOTE_KILL');
  }

  //最大得票数のユーザ名(処刑候補者) のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  do{
    if(count($max_voted_uname_list) == 1){ //一人だけなら処刑者決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    if(in_array($ability_list['decide'], $max_voted_uname_list)){ //決定者
      $vote_kill_uname = $ability_list['decide'];
      break;
    }

    if(in_array($ability_list['bad_luck'], $max_voted_uname_list)){ //不幸
      $vote_kill_uname = $ability_list['bad_luck'];
      break;
    }

    if(in_array($ability_list['impatience'], $max_voted_uname_list)){ //短気
      $vote_kill_uname = $ability_list['impatience'];
      break;
    }

    //幸運を処刑者候補から除く
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['good_luck']));
    if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    //疫病神の投票先を処刑者候補から除く
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['plague']));
    if(count($max_voted_uname_list) == 1){ //この時点で候補が一人なら処刑者決定
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }
  }while(false);

  if($vote_kill_uname != ''){ //処刑処理実行
    $vote_target = $USERS->ByUname($vote_kill_uname); //ユーザ情報を取得

    //処刑処理
    KillUser($vote_target->uname, 'VOTE_KILLED', &$dead_lovers_list);

    //処刑者を生存者リストから除く
    $live_uname_list = array_diff($live_uname_list, array($vote_target->uname));

    //処刑された人が毒を持っていた場合
    do{
      if(! $vote_target->is_role_group('poison')) break; //毒を持っていなければ発動しない
      if($vote_target->is_role('dummy_poison', 'poison_guard')) break; //夢毒者・騎士は対象外
      if($vote_target->is_role('incubate_poison') && $ROOM->date < 5) break; //潜毒者は 5 日目以降

      $pharmacist_success = false; //解毒成功フラグを初期化
      $poison_voter_list  = array_keys($vote_target_list, $vote_target->uname); //投票した人を取得
      foreach($poison_voter_list as $this_uname){ //薬師のチェック
	$this_user = $USERS->ByUname($this_uname);
	if(! $this_user->is_role('pharmacist')) continue;

	//解毒成功
	$sentence = $this_user->handle_name . "\t" . $vote_target->handle_name;
	InsertSystemMessage($sentence, 'PHARMACIST_SUCCESS');
	$pharmacist_success = true;
      }
      if($pharmacist_success) break;

      //毒の対象オプションをチェックして候補者リストを作成
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $poison_voter_list : $live_uname_list);
      if($vote_target->is_role('strong_poison')){ //強毒者ならターゲットから村人を除く
	$strong_poison_target_list = array();
	foreach($poison_target_list as $this_uname){
	  $this_user = $USERS->ByUname($this_uname);
	  if($this_user->is_role_group('wolf', 'fox')){
	    array_push($strong_poison_target_list, $this_uname);
	  }
	}
	$poison_target_list = $strong_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      //対象者を決定
      $rand_key = array_rand($poison_target_list);
      $poison_target = $USERS->ByUname($poison_target_list[$rand_key]);

      //不発判定
      if($vote_target->is_role('poison_wolf') && $poison_target->is_wolf()){ //毒狼の毒は人狼には無効
	//仕様が固まってないのでシステムメッセージは保留
	// InsertSystemMessage($poison_target->handle_name, 'POISON_WOLF_TARGET');
	break;
      }

      if($vote_target->is_role('poison_fox') && $poison_target->is_fox()){ //管狐の毒は妖狐には無効
	break;
      }

      if($poison_target->is_active_role('resist_wolf')){ //抗毒狼には無効
	UpdateRole($poison_target->uname, $poison_target->role . ' lost_ability');
	break;
      }

      KillUser($poison_target->uname, 'POISON_DEAD_day', &$dead_lovers_list); //死亡処理
    }while(false);

    //霊能系の出現チェック
    $flag_necromancer       = false;
    $flag_soul_necromancer  = false;
    $flag_dummy_necromancer = false;
    foreach($USERS->rows as $this_user){
      switch($this_user->main_role){
      case 'necromancer':
	$flag_necromancer = true;
	break;

      case 'soul_necromancer':
	$flag_soul_necromancer = true;
	break;

      case 'dummy_necromancer':
	$flag_dummy_necromancer = true;
	break;
      }
    }

    //霊能系の判定結果
    $sentence = $vote_target->handle_name . "\t";
    $action = 'NECROMANCER_RESULT';

    //霊能者の判定結果
    if($vote_target->is_role('boss_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->is_role('cursed_fox', 'white_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->is_wolf()){
      $necromancer_result = 'wolf';
    }
    else{
      $necromancer_result = 'human';
    }

    if($flag_necromancer){ //霊能者がいればシステムメッセージを登録
      InsertSystemMessage($sentence . $necromancer_result, $action);
    }

    if($flag_soul_necromancer){ //雲外鏡の判定結果
      InsertSystemMessage($sentence . $vote_target->main_role, 'SOUL_' . $action);
    }

    if($flag_dummy_necromancer){ //夢枕人の判定結果は村人と人狼が反転する
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //特殊サブ役職の突然死処理
  //投票者対象ユーザ名 => 人数 の配列を生成
  $voted_target_member_list = array_count_values($vote_target_list);
  $flag_medium = CheckMedium(); //巫女の出現チェック
  foreach($live_uname_list as $this_uname){
    $this_user = $USERS->ByUname($this_uname);
    $this_type = '';

    if($this_user->is_role('chicken')){ //小心者は投票されていたらショック死
      if($voted_target_member_list[$this_uname] > 0) $this_type = 'CHICKEN';
    }
    elseif($this_user->is_role('rabbit')){ //ウサギは投票されていなかったらショック死
      if($voted_target_member_list[$this_uname] == 0) $this_type = 'RABBIT';
    }
    elseif($this_user->is_role('perverseness')){
      //天邪鬼は自分の投票先に複数の人が投票していたらショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $this_type = 'PERVERSENESS';
    }
    elseif($this_user->is_role('flattery')){
      //ゴマすりは自分の投票先に他の人が投票していなければショック死
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $this_type = 'FLATTERY';
    }
    elseif($this_user->is_role('impatience')){
      if($vote_kill_uname == '') $this_type = 'IMPATIENCE'; //短気は再投票ならショック死
    }
    elseif($this_user->is_role('panelist')){ //解答者は出題者に投票したらショック死
      if($vote_target_list[$this_uname] == 'dummy_boy') $this_type = 'PANELIST';
    }

    if($this_type == '') continue;
    SuddenDeath($this_uname, $flag_medium, $this_type);
    if($this_user->is_lovers()) array_push($dead_lovers_list, $this_user->role);
  }
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //恋人後追い処理
  }

  if($vote_kill_uname != ''){ //夜に切り替え
    $check_draw = false; //引き分け判定実行フラグをオフ
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //夜にする
    InsertSystemTalk('NIGHT', ++$ROOM->system_time, 'night system'); //夜がきた通知
    UpdateTime(); //最終書き込みを更新
    // DeleteVote(); //今までの投票を全部削除
  }
  else{ //再投票処理
    $check_draw = true; //引き分け判定実行フラグをオン
    $next_vote_times = $RQ_ARGS->vote_times + 1; //投票回数を増やす
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = {$ROOM->date} AND type = 'VOTE_TIMES'");

    //システムメッセージ
    InsertSystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    InsertSystemTalk("再投票になりました( {$RQ_ARGS->vote_times} 回目)", ++$ROOM->system_time);
    UpdateTime(); //最終書き込みを更新
  }
  mysql_query('COMMIT'); //一応コミット
  CheckVictory($check_draw);
}

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  if($SELF->is_dummy_boy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  switch($RQ_ARGS->situation){
  case 'WOLF_EAT':
    if(! $SELF->is_wolf()) OutputVoteResult('夜：人狼以外は投票できません');
    break;

  case 'MAGE_DO':
    if(! $SELF->is_role_group('mage')) OutputVoteResult('夜：占い師以外は投票できません');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->is_role('jammer_mad')) OutputVoteResult('夜：邪魔狂人以外は投票できません');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->is_role('trap_mad')) OutputVoteResult('夜：罠師以外は投票できません');
    if($SELF->is_role('lost_ability')) OutputVoteResult('夜：罠は一度しか設置できません');
    $not_type = ($RQ_ARGS->situation == 'TRAP_MAD_NOT_DO');
    break;

  case 'GUARD_DO':
    if(! $SELF->is_role_group('guard')) OutputVoteResult('夜：狩人以外は投票できません');
    break;

  case 'REPORTER_DO':
    if(! $SELF->is_role('reporter')) OutputVoteResult('夜：ブン屋以外は投票できません');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->is_role('poison_cat')) OutputVoteResult('夜：猫又以外は投票できません');
    if($ROOM->is_open_cast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    $not_type = ($RQ_ARGS->situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->is_role('assassin')) OutputVoteResult('夜：暗殺者以外は投票できません');
    $not_type = ($RQ_ARGS->situation == 'ASSASSIN_NOT_DO');
    break;

  case 'MANIA_DO':
    if(! $SELF->is_role('mania')) OutputVoteResult('夜：神話マニア以外は投票できません');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->is_role('child_fox')) OutputVoteResult('夜：子狐以外は投票できません');
    break;

  case 'CUPID_DO':
    if(! $SELF->is_role('cupid')) OutputVoteResult('夜：キューピッド以外は投票できません');
    break;

  default:
    OutputVoteResult('夜：あなたは投票できません');
    break;
  }
  CheckAlreadyVote($RQ_ARGS->situation); //投票済みチェック

 //エラーメッセージのヘッダ
  $error_header = '夜：投票先が正しくありません<br>';

  if($not_type); //投票キャンセルタイプは何もしない
  elseif($SELF->is_role('cupid')){  //キューピッドの場合の投票処理
    if(count($RQ_ARGS->target_no) != 2) OutputVoteResult('夜：指定人数が２人ではありません');
    $self_shoot = false; //自分撃ちフラグを初期化
    foreach($RQ_ARGS->target_no as $lovers_target_no){
      //投票相手のユーザ情報取得
      $target_uname = $USERS->NumberToUname($lovers_target_no);
      $target_live  = $USERS->GetLive($target_uname);

      //死者、身代わり君への投票は無効
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('死者、身代わり君へは投票できません');

      if($target_uname == $SELF->uname) $self_shoot = true; //自分撃ちかどうかチェック
    }

    //ユーザ総数を取得
    $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0";
    if(FetchResult($query) < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
    }
  }
  else{ //キューピッド以外の投票処理
    //投票相手のユーザ情報取得
    $target_uname  = $USERS->NumberToUname($RQ_ARGS->target_no);
    $target_handle = $USERS->GetHandleName($target_uname);
    $target_role   = $USERS->GetRole($target_uname);
    $target_live   = $USERS->GetLive($target_uname);

    if($SELF->is_role('poison_cat')){ //猫又は自分宛、正者への投票は無効
      if($target_name == $SELF->uname || $target_live == 'live'){
	OutputVoteResult($error_header . '自分と生者には投票できません');
      }
    }
    elseif($SELF->is_role('trap_mad')){//罠師は死者宛の投票は無効
      if($target_live == 'dead'){
	OutputVoteResult($error_header . '死者には投票できません');
      }
    }
    else{//自分宛、死者宛、狼同士の投票は無効
      if($target_uname == $SELF->uname || $target_live == 'dead' ||
	 ($SELF->is_wolf() && strpos($target_role, 'wolf') !== false))
	OutputVoteResult($error_header . '自分、死者、狼同士へは投票できません');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){
      //クイズ村は GM 以外無効
      if($ROOM->is_quiz() && $target_uname != 'dummy_boy')
	OutputVoteResult($error_header . 'クイズ村では GM 以外に投票できません');

      //狼の初日の投票は身代わり君使用の場合は身代わり君以外無効
      if($ROOM->is_dummy_boy() && $target_uname != 'dummy_boy' && $ROOM->date == 1)
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
    }
  }

  LockTable(); //テーブルを排他的ロック
  if($not_type){
    //投票処理
    $sql = mysql_query("INSERT INTO vote(room_no, date, uname, vote_number, situation)
			VALUES($room_no, {$ROOM->date}, '{$SELF->uname}', 1, '{$RQ_ARGS->situation}')");
    InsertSystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    InsertSystemTalk($RQ_ARGS->situation, $ROOM->system_time, 'night system', '', $SELF->uname);
  }
  else{
    if($SELF->is_role('cupid')){ // キューピッドの処理
      $target_uname_str  = '';
      $target_handle_str = '';
      foreach ($RQ_ARGS->target_no as $lovers_target_no){
	//投票相手のユーザ情報取得
	$target_uname  = $USERS->NumberToUname($lovers_target_no);
	$target_handle = $USERS->GetHandleName($target_uname);
	$target_role   = $USERS->GetRole($target_uname);
	$target_uname_str  .= $target_uname  . ' ';
	$target_handle_str .= $target_handle . ' ';

	//役職に恋人を追加
	UpdateRole($target_uname, $target_role . ' lovers[' . strval($SELF->user_no) . ']');
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
			VALUES($room_no, {$ROOM->date}, '{$SELF->uname}', '$target_uname_str',
			1, '{$RQ_ARGS->situation}')");
    InsertSystemMessage($SELF->handle_name . "\t" . $target_handle_str, $RQ_ARGS->situation);
    $sentence = $RQ_ARGS->situation . "\t" . $target_handle_str;
    InsertSystemTalk($sentence, $ROOM->system_time, 'night system', '', $SELF->uname);
  }

  //登録成功
  if($sql && mysql_query('COMMIT')){
    AggregateVoteNight(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else OutputVoteResult('データベースエラー', true);
}

//夜の役職の投票状況をチェックして投票結果を返す
function CheckVoteNight($action, $role, $dummy_boy_role = '', $not_type = ''){
  global $room_no, $ROOM;

  //投票情報を取得
  $sql_vote = mysql_query("SELECT uname, target_uname FROM vote WHERE room_no = $room_no
				AND date = {$ROOM->date} AND situation = '$action'");
  $vote_count = mysql_num_rows($sql_vote); //投票人数を取得

  if($not_type != ''){ //キャンセルタイプの投票情報を取得
    $query_not_type = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
      "AND date = {$ROOM->date} AND situation = '$not_type'";
    $vote_count += FetchResult($query_not_type); //投票人数に追加
  }

  //狼の噛みは一人で OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $sql_vote : false);

  //生きている対象役職の人数をカウント
  $query_role = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no ".
    "AND live = 'live' AND user_no > 0 AND role LIKE '{$role}%'";
  if($action == 'TRAP_MAD_DO') $query_role .= " AND !(role LIKE '%lost_ability%')";
  $role_count = FetchResult($query_role);

  //初日、身代わり君が特定の役職だった場合はカウントしない
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $sql_vote : false);
}

//夜の集計処理
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  //コマンドチェック
  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'JAMMER_MAD_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
			  'GUARD_DO', 'REPORTER_DO', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO',
			  'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'MANIA_DO', 'CHILD_FOX_DO', 'CUPID_DO');
  CheckSituation($situation_list);

  //狼の投票チェック
  if(($sql_wolf = CheckVoteNight('WOLF_EAT', '%wolf')) === false) return false;

  //初日、身代わり君が特定の役職だった場合はカウントしない
  if($ROOM->date == 1 && $ROOM->is_dummy_boy()){
    $this_dummy_boy_role = $USERS->GetRole('dummy_boy');
    $exclude_role_list   = array('mage', 'jammer_mad', 'mania', 'cupid'); //カウント対象外役職リスト

    foreach($exclude_role_list as $this_role){
      if(strpos($this_dummy_boy_role, $this_role) !== false){
	$dummy_boy_role = $this_role;
	break;
      }
    }
  }

  //常に投票できる役職の投票チェック
  if(($sql_mage = CheckVoteNight('MAGE_DO', '%mage', $dummy_boy_role)) === false) return false;
  $sql_jammer_mad = CheckVoteNight('JAMMER_MAD_DO', 'jammer_mad', $dummy_boy_role);
  if($sql_jammer_mad === false) return false;
  if(($sql_child_fox = CheckVoteNight('CHILD_FOX_DO', 'child_fox')) === false) return false;

  if($ROOM->date == 1){ //初日のみ投票できる役職をチェック
    if(($sql_mania = CheckVoteNight('MANIA_DO', 'mania', $dummy_boy_role)) === false) return false;
    if(CheckVoteNight('CUPID_DO', 'cupid', $dummy_boy_role) === false) return false;
  }
  else{ //二日目以降投票できる役職をチェック
    $sql_trap_mad = CheckVoteNight('TRAP_MAD_DO', 'trap_mad', '', 'TRAP_MAD_NOT_DO');
    if($sql_trap_mad === false) return false;
    if(($sql_guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;
    if(($sql_reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;
    if(! $ROOM->is_open_cast()){
      $sql_poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat', '', 'POISON_CAT_NOT_DO');
      if($sql_poison_cat === false) return false;
    }
    $sql_assassin = CheckVoteNight('ASSASSIN_DO', 'assassin', '', 'ASSASSIN_NOT_DO');
    if($sql_assassin === false) return false;
  }

  //人狼の襲撃情報を取得
  $wolf_target_array = mysql_fetch_assoc($sql_wolf);
  $voted_wolf  = $USERS->ByUname($wolf_target_array['uname']);
  $wolf_target = $USERS->ByUname($wolf_target_array['target_uname']);

  $guarded_uname = ''; //護衛された人のユーザ名 //複数噛みに対応するならここは配列に変える
  $dead_uname_list    = array(); //死亡者リスト
  $dead_lovers_list   = array(); //恋人後追い対象者リスト
  $trap_target_list   = array(); //罠の設置先リスト
  $trapped_uname_list = array(); //罠にかかった人リスト

  if($ROOM->date != 1){
    //罠師の設置先リストを作成
    $trap_mad_list = array();
    while(($array = mysql_fetch_assoc($sql_trap_mad)) !== false){
      $this_uname        = $array['uname'];
      $this_target_uname = $array['target_uname'];

      //一度設置したら能力失効
      UpdateRole($this_uname, $USERS->GetRole($this_uname) . ' lost_ability');

      //人狼に狙われていたら自分自身への設置以外は無効
      if($this_uname != $wolf_target->uname || $this_uname == $this_target_uname){
	$trap_mad_list[$this_uname] = $this_target_uname;
      }
    }

    //罠師が自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は死亡
    $trap_count_list = array_count_values($trap_mad_list);
    foreach($trap_mad_list as $this_uname => $this_target_uname){
      if($this_uname != $this_target_uname && $trap_count_list[$this_target_uname] > 1){
	array_push($trapped_uname_list, $this_uname);
      }
    }
    $trap_target_list = array_keys($trap_count_list);

    while(($array = mysql_fetch_assoc($sql_guard)) !== false){ //狩人系の処理
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);
      $sentence    = $this_user->handle_name . "\t";

      if($this_user->is_role('dummy_guard')){ //夢守人は必ず護衛成功メッセージだけが出る
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_SUCCESS');
	continue;
      }

      if($this_target->is_role('jammer_mad', 'trap_mad', 'cursed_fox')){ //狩り判定
	KillUser($this_target->uname, 'HUNTED', &$dead_lovers_list);
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_HUNTED');
	array_push($dead_uname_list, $this_target->uname);
      }

      if(in_array($this_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	array_push($trapped_uname_list, $this_user->uname);
	continue;
      }

      //護衛成功判定
      if($this_target->uname != $wolf_target->uname) continue;
      InsertSystemMessage($sentence . $wolf_target->handle_name, 'GUARD_SUCCESS');

      //騎士でない場合、一部の役職は護衛されていても人狼に襲撃される
      if($this_user->is_role('poison_guard') || ! $wolf_target->is_role('reporter', 'assassin')){
	$guarded_uname = $this_target->uname;
      }
    }
  }

  do{ //人狼の襲撃成功判定
    //護衛成功 or クイズ村仕様
    if($guarded_uname != '' || $ROOM->is_quiz()) break;

    //襲撃先が妖狐の場合は失敗する
    if($wolf_target->is_fox() && ! $wolf_target->is_role('child_fox', 'poison_fox', 'white_fox')){
      InsertSystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    if(in_array($wolf_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
      array_push($trapped_uname_list, $voted_wolf->uname);
      break;
    }

    //襲撃処理
    KillUser($wolf_target->uname, 'WOLF_KILLED', &$dead_lovers_list);
    array_push($dead_uname_list, $wolf_target->uname);

    if($voted_wolf->is_active_role('tongue_wolf')){ //舌禍狼の処理
      $wolf_target_main_role = GetMainRole($wolf_target->role);
      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      InsertSystemMessage($sentence . $wolf_target_main_role, 'TONGUE_WOLF_RESULT');

      if($wolf_target_main_role == 'human'){ //村人なら能力失効
	UpdateRole($voted_wolf->uname, $voted_wolf->role . ' lost_ability');
      }
    }

    //食べられた人が毒持ちだった場合
    do{
      if(! $wolf_target->is_role_group('poison')) break; //毒を持っていなければ発動しない
      if($wolf_target->is_role('dummy_poison')) break;//夢毒者は対象外
      if($wolf_target->is_role('incubate_poison') && $ROOM->date < 5) break; //潜毒者は 5 日目以降

      //生きている狼を取得
      $live_wolf_list = ($GAME_CONF->poison_only_eater ? array($voted_wolf->uname) : GetLiveWolves());

      $rand_key = array_rand($live_wolf_list);
      $poison_target = $USERS->ByUname($live_wolf_list[$rand_key]);

      if($poison_target->is_active_role('resist_wolf')){ //抗毒狼なら無効
	UpdateRole($poison_target->uname, $poison_target->role . ' lost_ability');
	break;
      }

      //毒死処理
      KillUser($poison_target->uname, 'POISON_DEAD_night', &$dead_lovers_list);
      array_push($dead_uname_list, $poison_target->uname);
    }while(false);
  }while(false);

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

  if($ROOM->date != 1){
    $assassin_target_list = array(); //暗殺対象者リスト
    while(($array = mysql_fetch_assoc($sql_assassin)) !== false){ //暗殺者の処理
      $this_uname = $array['uname'];
      if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

      $this_target_uname = $array['target_uname'];
      if(in_array($this_target_uname, $trap_target_list)){ //罠が設置されていたら死亡
	array_push($trapped_uname_list, $this_uname);
	continue;
      }

      array_push($assassin_target_list, $this_target_uname); //暗殺対象者リストに追加
    }

    foreach($trapped_uname_list as $this_uname){ //罠の死亡処理
      if(in_array($this_uname, $dead_uname_list)) continue;
      KillUser($this_uname, 'TRAPPED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
    }

    foreach($assassin_target_list as $this_uname){ //暗殺処理
      if(in_array($this_uname, $dead_uname_list)) continue;
      KillUser($this_uname, 'ASSASSIN_KILLED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
    }
  }

  $jammer_target_list = array(); //妨害対象リスト
  while(($array = mysql_fetch_assoc($sql_jammer_mad)) !== false){ //邪魔狂人の処理
    $this_uname = $array['uname'];
    if(in_array($this_uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($this_target->is_role_group('cursed')){ //対象が呪持ちだった場合は呪返しを受ける
      KillUser($this_uname, 'CURSED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
      continue;
    }

    array_push($jammer_target_list, $this_target->uname); //妨害対象者リストに追加
  }

  //精神鑑定士の嘘つき判定対象役職リスト
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  while(($array = mysql_fetch_assoc($sql_mage)) !== false){//占い師系の処理
    $this_user = $USERS->ByUname($array['uname']);
    if(in_array($this_user->uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

    if($this_user->is_role('dummy_mage')){ //夢見人の占い結果は村人と人狼を反転させる
      $this_result = DistinguishMage($this_target->role);
      if($this_result == 'human')    $this_result = 'wolf';
      elseif($this_result == 'wolf') $this_result = 'human';
    }
    elseif($this_user->is_role('psycho_mage')){ //精神鑑定士の判定
      if(in_array($this_user->uname, $jammer_target_list)){ //邪魔狂人の妨害判定
	$this_result = 'mage_failed';
      }
      else{
	$this_result = 'psycho_mage_normal';
	foreach($psycho_mage_liar_list as $this_liar_role){
	  if($this_target->is_role_group($this_liar_role)){
	    $this_result = 'psycho_mage_liar';
	    break;
	  }
	}
      }
    }
    elseif($this_user->is_role('sex_mage')){ //ひよこ鑑定士の判定
      if(in_array($this_user->uname, $jammer_target_list)){ //邪魔狂人の妨害判定
	$this_result = 'mage_failed';
      }
      else{
	$this_result = 'sex_' . $this_target->sex;
      }
    }
    else{
      if($this_target->is_role_group('cursed')){ //呪い持ちを占ったら呪返しを受ける
	KillUser($this_user->uname, 'CURSED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_user->uname);
	continue;
      }

      if(in_array($this_user->uname, $jammer_target_list)){ //邪魔狂人の妨害判定
	$this_result = 'failed';
      }
      else{
	if($this_user->is_role('soul_mage')){ //魂の占い師の占い結果はメイン役職
	  $this_result = GetMainRole($this_target->role);
	}
	else{
	  do{ //呪殺判定
	    if(in_array($this_target->uname, $dead_uname_list)) break; //既に死んでいたらスキップ
	    if(! $this_target->is_fox()) break; //妖狐以外は対象外
	    if($this_target->is_role('child_fox', 'white_fox')) break; //一部の妖狐は対象外
	    KillUser($this_target->uname, 'FOX_DEAD', &$dead_lovers_list);
	    array_push($dead_uname_list, $this_target->uname);
	  }while(false);

	  $this_result = DistinguishMage($this_target->role); //判定結果を取得
	}
      }
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  while(($array = mysql_fetch_assoc($sql_child_fox)) !== false){ //子狐の処理
    $this_user = $USERS->ByUname($array['uname']);
    if(in_array($this_user->uname, $dead_uname_list)) continue; //直前に死んでいたら無効

    $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
    if($this_target->is_role_group('cursed')){ //呪い持ちを占ったら呪返しを受ける
      KillUser($this_user->uname, 'CURSED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_user->uname);
      continue;
    }

    //占い結果を作成
    if(in_array($this_user->uname, $jammer_target_list) || mt_rand(1, 100) <= 30){ //一定確率で失敗する
      $this_result = 'failed';
    }
    else{
      $this_result = DistinguishMage($this_target->role);
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  if($ROOM->date == 1){
    while(($array = mysql_fetch_assoc($sql_mania)) !== false){ //神話マニアの処理
      $this_user = $USERS->ByUname($array['uname']);
      if(in_array($this_user->uname, $dead_uname_list)) continue; //直前に死んでいたら無効

      $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

      //コピー処理 (神話マニアを指定した場合は村人にする)
      if(($this_result = GetMainRole($this_target->role)) == 'mania' ||
	 $this_target->is_role('copied')) $this_result = 'human';
      $this_new_role = str_replace('mania', $this_result, $this_target->role) . ' copied';
      UpdateRole($this_user->uname, $this_new_role);

      $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'MANIA_RESULT');
    }
  }
  else{
    //ブン屋の処理
    while(($array = mysql_fetch_assoc($sql_reporter)) !== false){
      $this_user = $USERS->ByUname($array['uname']);
      if(in_array($this_user->uname, $dead_uname_list)) continue; //直前に死んでいたら無効

      $this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得
      if(in_array($this_target->uname, $trap_target_list)){ //罠が設置されていたら死亡
	UpdateLive($this_user->uname);
	InsertSystemMessage($this_user->handle_name, 'TRAPPED');
	if($this_user->is_lovers()){ //恋人後追い処理
	  array_push($dead_lovers_list, $this_user->role);
	}
	array_push($dead_uname_list, $this_user->uname);
	continue;
      }

      if($this_target->uname == $wolf_target->uname){ //尾行成功
	if($this_target->uname == $guarded_uname) continue; //護衛されていた場合は何も出ない
	$sentence = $this_user->handle_name . "\t" . $wolf_target->handle_name . "\t";
	InsertSystemMessage($sentence . $voted_wolf->handle_name, 'REPORTER_SUCCESS');
	continue;
      }

      //尾行対象が直前に死んでいたら何も起きない
      if(in_array($this_target->uname, $dead_uname_list)) continue;

      if($this_target->is_role_group('wolf', 'fox')){ //尾行対象が人狼か妖狐なら殺される
	UpdateLive($this_user->uname);
	InsertSystemMessage($this_user->handle_name, 'REPORTER_DUTY');
	if($this_user->is_lovers()){ //恋人後追い処理
	  array_push($dead_lovers_list, $this_user->role);
	}
	array_push($dead_uname_list, $this_user->uname);
      }
    }

    if(! $ROOM->is_open_cast()){ //猫又の処理
      $revive_uname_list = array(); //蘇生者リスト
      while(($array = mysql_fetch_assoc($sql_poison_cat)) !== false){
	$this_user = $USERS->ByUname($array['uname']);
	if(in_array($this_user->uname, $dead_uname_list)) continue; //直前に死んでいたら無効

	$this_target = $USERS->ByUname($array['target_uname']); //対象者の情報を取得

	//蘇生判定
	$this_rand = mt_rand(1, 100); //蘇生判定用乱数
	$this_result = 'failed';
	do{
	  if($this_rand > 25) break; //蘇生失敗
	  if($this_rand <= 5){ //誤爆蘇生
	    $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no AND live = 'dead'
				AND uname <> 'dummy_boy' AND uname <> '{$this_target->uname}'
				AND user_no > 0 ORDER BY MD5(RAND()*NOW())");
	    if(mysql_num_rows($sql) > 0){ //他に対象がいる場合だけ入れ替わる
	      $this_target = $USERS->ByUname(mysql_result($sql, 0, 0));
	    }
	  }
	  if($this_target->is_role('poison_cat')) break; //猫又なら蘇生失敗

	  $this_result = 'success';
	  if(in_array($this_target->uname, $revive_uname_list)) break; //蘇生済みならスキップ

	  UpdateLive($this_target->uname, true);
	  InsertSystemMessage($this_target->handle_name, 'REVIVE_SUCCESS');
	  if($this_target->is_lovers()){ //恋人なら即自殺
	    array_push($dead_lovers_list, $this_target->role);
	  }
	  array_push($revive_uname_list, $this_target->uname);
	}while(false);

	if($this_result == 'failed') InsertSystemMessage($this_target->handle_name, 'REVIVE_FAILED');
	$sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }
  $flag_medium = CheckMedium();
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //恋人後追い処理
  }

  //次の日にする
  $next_date = $ROOM->date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");

  //次の日の処刑投票のカウントを 1 に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //夜が明けた通知
  InsertSystemTalk("MORNING\t" . $next_date, ++$ROOM->system_time, 'day system', $next_date);
  UpdateTime(); //最終書き込みを更新
  // DeleteVote(); //今までの投票を全部削除

  CheckVictory(); //勝敗のチェック
  mysql_query('COMMIT'); //一応コミット
}

//開始前の投票ページ出力
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $ROOM, $SELF, $php_argv;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_file   = $ICON_CONF->path . '/' . $this_user->icon_filename;
    $this_color  = $this_user->color;

    //HTML出力
    echo <<<EOF
<td><label for="$this_handle">
<img src="$this_file" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if(! $this_user->is_dummy_boy() && $this_user->uname != $SELF->uname){
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
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$MESSAGE->submit_game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//昼の投票ページを出力する
function OutputVoteDay(){
  global $MESSAGE, $ICON_CONF, $USERS, $room_no, $ROOM, $SELF, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票回数を取得
  $vote_times = GetVoteTimes();

  //投票済みかどうか
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND date = {$ROOM->date}
			AND vote_times = $vote_times AND situation = 'VOTE_KILL'");
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
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_color  = $this_user->color;

    if($this_user->is_live()) //生きていればユーザアイコン
      $path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    else //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_user->is_live() && $this_user->uname != $SELF->uname){
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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $ROOM, $USERS, $SELF, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票済みチェック
  if($SELF->is_dummy_boy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  if($role_wolf = $SELF->is_wolf()){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif($role_mage = $SELF->is_role_group('mage')){
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_jammer_mad = $SELF->is_role('jammer_mad')){
    CheckAlreadyVote('JAMMER_MAD_DO');
  }
  elseif($role_trap_mad = $SELF->is_role('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の罠設置はできません');
    if($SELF->is_role('lost_ability')) OutputVoteResult('夜：罠は一度しか設置できません');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_guard = $SELF->is_role_group('guard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の護衛はできません');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = $SELF->is_role('reporter')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の尾行はできません');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_poison_cat = $SELF->is_role('poison_cat')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    if($ROOM->is_open_cast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
  }
  elseif($role_assassin = $SELF->is_role('assassin')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の暗殺はできません');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_mania = $SELF->is_role('mania')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('MANIA_DO');
  }
  elseif($role_child_fox = $SELF->is_role('child_fox')){
    CheckAlreadyVote('CHILD_FOX_DO');
  }
  elseif($role_cupid = $SELF->is_role('cupid')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('CUPID_DO');
    $cupid_self_shoot = ($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot);
  }
  else OutputVoteResult('夜：あなたは投票できません');

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && ($ROOM->is_dummy_boy() && $ROOM->date == 1 || $ROOM->is_quiz())){
    //身代わり君のユーザ情報
    $this_rows = array(1 => $USERS->rows[1]); //dummy_boy = 1番は保証されている？
  }
  else{
    $this_rows = $USERS->rows;
  }
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";
  foreach($this_rows as $this_user_no => $this_user){
    $this_color = $this_user->color;
    $this_wolf  = ($role_wolf && $this_user->is_wolf());
    $is_self    = ($this_user->uname == $SELF->uname);

    if($this_user->is_live() || $role_poison_cat){ //猫又は死亡アイコンにしない
      if($this_wolf) //狼同士なら狼アイコン
	$path = $ICON_CONF->wolf;
      else //生きていればユーザアイコン
	$path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    }
    else{
      $path = $ICON_CONF->dead; //死んでれば死亡アイコン
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_user->handle_name<br>

EOF;

    if($role_cupid){
      if(! $this_user->is_dummy_boy()){
	$checked = (($cupid_self_shoot && $is_self) ? ' checked' : '');
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '"' . $checked . '>'."\n";
      }
    }
    elseif($role_poison_cat){
      if($this_user->is_dead() && ! $is_self && ! $this_user->is_dummy_boy()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($role_trap_mad){
      if($this_user->is_live()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_user->is_live() && ! $is_self && ! $this_wolf){
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
  elseif($role_jammer_mad){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'submit_jammer_mad_do';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'submit_trap_mad_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'submit_trap_mad_not_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'submit_guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'submit_reporter_do';
  }
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'submit_poison_cat_do';
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'submit_poison_cat_not_do';
  }
  elseif($role_assassin){
    $type   = 'ASSASSIN_DO';
    $submit = 'submit_assassin_do';
    $not_type   = 'ASSASSIN_NOT_DO';
    $not_submit = 'submit_assassin_not_do';
  }
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'submit_mania_do';
  }
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
    $submit = 'submit_mage_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'submit_cupid_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$MESSAGE->$submit}"></td></form>

EOF;

  if($not_type != ''){
    echo <<<EOF
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
<input type="submit" value="{$MESSAGE->$not_submit}"></form>
</td>

EOF;
  }

  echo <<<EOF
</tr></table></div>
</body></html>

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

//死亡処理
function KillUser($uname, $reason, &$dead_lovers_list){
  global $USERS;

  $target_handle = $USERS->GetHandleName($uname);
  $target_role   = $USERS->GetRole($uname);

  UpdateLive($uname);
  InsertSystemMessage($target_handle, $reason);
  SaveLastWords($target_handle);
  if(strpos($target_role, 'lovers') !== false){ //恋人後追い処理
    array_push($dead_lovers_list, $target_role);
  }
}

//投票コマンドがあっているかチェック
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return;

  OutputVoteResult('無効な投票です');
}

//投票する状況があっているかチェック
function CheckDayNight(){
  global $ROOM, $SELF;

  if($ROOM->day_night != $SELF->last_load_day_night){
    OutputVoteResult('戻ってリロードしてください');
  }
}

//投票済みチェック
function CheckAlreadyVote($situation, $not_situation = ''){
  if(CheckSelfVoteNight($situation, $not_situation)) OutputVoteResult('夜：投票済み');
}
?>
