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

$ROOM = new RoomDataSet($RQ_ARGS); //村情報をロード
$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS = new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF  = $USERS->ByUname($uname); //自分の情報をロード

if($ROOM->IsFinished()){ //ゲームは終了しました
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>ゲームは終了しました<br>'."\n" .
		     $back_url . '</div>');
}

if(! $SELF->IsLive()){ //生存者以外は無効
  OutputActionResult('投票エラー',
		     '<div align="center">' .
		     '<a name="#game_top"></a>生存者以外は投票できません<br>'."\n" .
		     $back_url . '</div>');
}

if($RQ_ARGS->vote){ //投票処理
  if($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票処理
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
  elseif($ROOM->IsDay()){ //昼の処刑投票処理
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputActionResult('投票エラー',
		       '<div align="center">' .
		       '<a name="#game_top"></a>プログラムエラーです。管理者に問い合わせてください<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票ページ出力
  OutputVoteBeforeGame();
}
elseif($ROOM->IsDay()){ //昼の処刑投票ページ出力
  OutputVoteDay();
}
elseif($ROOM->IsNight()){ //夜の投票ページ出力
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
  if($SELF->IsDummyBoy() && ! $ROOM->IsQuiz()){
    OutputVoteResult('ゲームスタート：身代わり君は投票不要です');
  }

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = 0 " .
    "AND situation = 'GAMESTART' AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('ゲームスタート：投票済みです');

  LockTable(); //テーブルを排他的ロック

  //投票処理
  $items = 'room_no, date, uname, situation';
  $values = "$room_no, 0, '{$SELF->uname}', 'GAMESTART'";
  if(InsertDatabase('vote', $items, $values) && mysql_query('COMMIT')){//一応コミット
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
  if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;

  //ユーザ総数を取得
  $user_count = $USERS->GetUserCount();

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- 配役決定ルーチン --//
  //配役設定オプションの情報を取得
  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = $room_no");

  //配役決定用変数をセット
  $uname_list        = $USERS->GetLivingUsers(); //ユーザ名の配列
  $role_list         = GetRoleList($user_count, $option_role); //役職リストを取得
  $fix_uname_list    = array(); //役割の決定したユーザ名を格納する
  $fix_role_list     = array(); //ユーザ名に対応する役割
  $remain_uname_list = array(); //希望の役割になれなかったユーザ名を一時的に格納

  //フラグセット
  $gerd  = $ROOM->IsOption('gerd');
  $chaos = $ROOM->IsOptionGroup('chaos'); //chaosfull も含む
  $quiz  = $ROOM->IsQuiz();

  //エラーメッセージ
  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  if($ROOM->IsDummyBoy()){ //身代わり君の役職を決定
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
    unset($uname_list[array_search('dummy_boy', $uname_list)]); //身代わり君を削除
  }

  //ユーザリストをランダムに取得
  shuffle($uname_list);

  //希望役職を参照して一次配役を行う
  if($ROOM->IsOption('wish_role') && ! $chaos){ //役割希望制の場合 (闇鍋は希望を無視)
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
      $this_role = GetRandom($sub_role_list);
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

  //ゲーム開始時間を通知
  $start_time = gmdate('Y/m/j (D) G:i:s', $ROOM->system_time);
  InsertSystemTalk('ゲーム開始：' . $start_time, $ROOM->system_time, 'night system', 1);

  //役割をDBに更新
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $this_user = $USERS->ByUname($fix_uname_list[$i]);
    $this_role = $fix_role_list[$i];
    $this_user->ChangeRole($this_role);
    $this_role_list = explode(' ', $this_role);
    foreach($this_role_list as $this_role) $role_count_list[$this_role]++;
  }

  //役割リスト通知
  if($chaos){
    if(strpos($option_role, 'chaos_open_cast_camp') !== false){
      $sentence = MakeRoleNameList($role_count_list, 'camp');
    }
    elseif(strpos($option_role, 'chaos_open_cast_role') !== false){
      $sentence = MakeRoleNameList($role_count_list, 'role');
    }
    elseif(strpos($option_role, 'chaos_open_cast') !== false){
      $sentence = MakeRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = MakeRoleNameList($role_count_list);
  }
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system', 1);

  InsertSystemMessage('1', 'VOTE_TIMES', 1); //初日の処刑投票のカウントを1に初期化(再投票で増える)
  UpdateTime(); //最終書き込み時刻を更新
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //真・闇鍋はいきなり終了してる可能性あり
  mysql_query('COMMIT'); //一応コミット
}

//開始前の Kick 投票の処理 ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $room_no, $ROOM, $SELF;

  //エラーチェック
  CheckSituation('KICK_DO');
  if($target == '') OutputVoteResult('Kick：投票先を指定してください');
  if($target == '身代わり君') OutputVoteResult('Kick：身代わり君には投票できません');
  if(($ROOM->IsQuiz() || $ROOM->IsOption('gm_login')) && $target == 'GM'){
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
  $items = 'room_no, date, uname, target_uname, situation';
  $values = "$room_no, 0, '{$SELF->uname}', '$target_uname', 'KICK_DO'";
  $sql = InsertDatabase('vote', $items, $values);
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
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy()) return $vote_count;

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

  $target = $USERS->ByID($RQ_ARGS->target_no); //投票先のユーザ情報を取得
  if($target->uname == '') OutputVoteResult('処刑：投票先が指定されていません');
  if($target->IsSelf()) OutputVoteResult('処刑：自分には投票できません');
  if(! $target->IsLive()) OutputVoteResult('処刑：生存者以外には投票できません');

  LockTable(); //テーブルを排他的ロック

  //-- 投票処理 --//
  //役職に応じて票数を決定
  $vote_number = 1;
  if($SELF->IsRole('authority')){
    $vote_number++; //権力者
  }
  elseif($SELF->IsRole('watcher', 'panelist')){
    $vote_number = 0; //傍観者・解答者
  }
  elseif($SELF->IsRole('random_voter')){
    $vote_number = mt_rand(0, 2); //気分屋
  }

  //投票＆システムメッセージ
  $items = 'room_no, date, uname, target_uname, vote_number, vote_times, situation';
  $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', '{$target->uname}', $vote_number, " .
    "{$RQ_ARGS->vote_times}, 'VOTE_KILL'";
  $sql = InsertDatabase('vote', $items, $values);
  $sentence = "VOTE_DO\t" . $target->handle_name;
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

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  switch($RQ_ARGS->situation){
  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('夜：人狼以外は投票できません');
    break;

  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage')) OutputVoteResult('夜：占い師以外は投票できません');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRoleGroup('voodoo_killer')) OutputVoteResult('夜：陰陽師以外は投票できません');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRole('jammer_mad')) OutputVoteResult('夜：邪魔狂人以外は投票できません');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->IsRole('trap_mad')) OutputVoteResult('夜：罠師以外は投票できません');
    if($SELF->IsRole('lost_ability')) OutputVoteResult('夜：罠は一度しか設置できません');
    $not_type = ($RQ_ARGS->situation == 'TRAP_MAD_NOT_DO');
    break;

  case 'VOODOO_MAD_DO':
    if(! $SELF->IsRole('voodoo_mad')) OutputVoteResult('夜：呪術師以外は投票できません');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('夜：狩人以外は投票できません');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('夜：厄神以外は投票できません');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('夜：ブン屋以外は投票できません');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRole('poison_cat')) OutputVoteResult('夜：猫又以外は投票できません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    $not_type = ($RQ_ARGS->situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRole('assassin')) OutputVoteResult('夜：暗殺者以外は投票できません');
    $not_type = ($RQ_ARGS->situation == 'ASSASSIN_NOT_DO');
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRole('mania')) OutputVoteResult('夜：神話マニア以外は投票できません');
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('夜：九尾以外は投票できません');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsRole('child_fox')) OutputVoteResult('夜：子狐以外は投票できません');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRole('cupid')) OutputVoteResult('夜：キューピッド以外は投票できません');
    break;

  default:
    OutputVoteResult('夜：あなたは投票できません');
    break;
  }
  CheckAlreadyVote($RQ_ARGS->situation); //投票済みチェック

 //エラーメッセージのヘッダ
  $error_header = '夜：投票先が正しくありません<br>';

  if($not_type); //投票キャンセルタイプは何もしない
  elseif($SELF->IsRole('cupid')){  //キューピッドの場合の投票処理
    if(count($RQ_ARGS->target_no) != 2) OutputVoteResult('夜：指定人数が２人ではありません');
    $target_list = array();
    $self_shoot = false; //自分撃ちフラグを初期化
    foreach($RQ_ARGS->target_no as $this_target_no){
      //投票相手のユーザ情報取得
      $this_target = $USERS->ByID($this_target_no);

      //生存者以外と身代わり君への投票は無効
      if(! $this_target->IsLive() || $this_target->IsDummyBoy()){
	OutputVoteResult('生存者以外と身代わり君へは投票できません');
      }

      array_push($target_list, $this_target);
      if($this_target->IsSelf()) $self_shoot = true; //自分撃ちかどうかチェック
    }

    //参加人数をチェック
    if($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
    }
  }
  else{ //キューピッド以外の投票処理
    $target = $USERS->ByID($RQ_ARGS->target_no); //投票相手のユーザ情報取得

    if($target->IsSelf() && ! $SELF->IsRole('trap_mad')){ //罠師以外は自分への投票は無効
      OutputVoteResult($error_header . '自分には投票できません');
    }

    if($SELF->IsRole('poison_cat')){ //猫又は死者以外への投票は無効
      if(! $target->IsDead()){
	OutputVoteResult($error_header . '死者以外には投票できません');
      }
    }
    elseif(! $target->IsLive()){
      OutputVoteResult($error_header . '生存者以外には投票できません');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){ //人狼の投票
      if($SELF->IsWolf() && $target->IsWolf()){ //狼同士への投票は無効
	OutputVoteResult($error_header . '狼同士には投票できません');
      }

      if($ROOM->IsQuiz() && ! $target->IsDummyBoy()){ //クイズ村は GM 以外無効
	OutputVoteResult($error_header . 'クイズ村では GM 以外に投票できません');
      }

      //身代わり君使用の場合は、初日は身代わり君以外無効
      if($ROOM->IsDummyBoy() && $ROOM->date == 1 && ! $target->IsDummyBoy()){
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
      }
    }
  }

  LockTable(); //テーブルを排他的ロック
  if($not_type){
    //投票処理
    $items = 'room_no, date, uname, vote_number, situation';
    $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
    InsertSystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    InsertSystemTalk($RQ_ARGS->situation, $ROOM->system_time, 'night system', '', $SELF->uname);
  }
  else{
    if($SELF->IsRole('cupid')){ // キューピッドの処理
      $target_uname_str  = '';
      $target_handle_str = '';
      foreach($target_list as $this_target){
	if($target_uname_str != ''){
	  $target_uname_str  .= ' ';
	  $target_handle_str .= ' ';
	}
	$target_uname_str  .= $this_target->uname;
	$target_handle_str .= $this_target->handle_name;

	//役職に恋人を追加
	$this_target->AddRole('lovers[' . strval($SELF->user_no) . ']');
      }
    }
    else{ // キューピッド以外の処理
      $target_uname_str  = $target->uname;
      $target_handle_str = $target->handle_name;
    }
    //投票処理
    $items = 'room_no, date, uname, target_uname, vote_number, situation';
    $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', '$target_uname_str', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
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

    if(! $this_user->IsDummyBoy() && $this_user->uname != $SELF->uname){
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

    if($this_user->IsLive()) //生きていればユーザアイコン
      $path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    else //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_user->IsLive() && $this_user->uname != $SELF->uname){
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
  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  if($role_wolf = $SELF->IsWolf()){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif($role_mage = $SELF->IsRoleGroup('mage')){
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_voodoo_killer = $SELF->IsRole('voodoo_killer')){
    CheckAlreadyVote('VOODOO_KILLER_DO');
  }
  elseif($role_jammer_mad = $SELF->IsRole('jammer_mad')){
    CheckAlreadyVote('JAMMER_MAD_DO');
  }
  elseif($role_voodoo_mad = $SELF->IsRole('voodoo_mad')){
    CheckAlreadyVote('VOODOO_MAD_DO');
  }
  elseif($role_trap_mad = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の罠設置はできません');
    if($SELF->IsRole('lost_ability')) OutputVoteResult('夜：罠は一度しか設置できません');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_anti_voodoo = $SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の厄払いはできません');
    CheckAlreadyVote('ANTI_VOODOO_DO');
  }
  elseif($role_guard = $SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の護衛はできません');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = $SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の尾行はできません');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_poison_cat = $SELF->IsRole('poison_cat')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
  }
  elseif($role_assassin = $SELF->IsRole('assassin')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の暗殺はできません');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_mania = $SELF->IsRole('mania')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('MANIA_DO');
  }
  elseif($role_voodoo_fox = $SELF->IsRole('voodoo_fox')){
    CheckAlreadyVote('VOODOO_FOX_DO');
  }
  elseif($role_child_fox = $SELF->IsRole('child_fox')){
    CheckAlreadyVote('CHILD_FOX_DO');
  }
  elseif($role_cupid = $SELF->IsRole('cupid')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('CUPID_DO');
    $cupid_self_shoot = ($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot);
  }
  else OutputVoteResult('夜：あなたは投票できません');

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && ($ROOM->IsDummyBoy() && $ROOM->date == 1 || $ROOM->IsQuiz())){
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
    $this_wolf  = ($role_wolf && $this_user->IsWolf());

    if($this_user->IsLive() || $role_poison_cat){ //猫又は死亡アイコンにしない
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
      if(! $this_user->IsDummyBoy()){
	$checked = (($cupid_self_shoot && $this_user->IsSelf()) ? ' checked' : '');
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '"' . $checked . '>'."\n";
      }
    }
    elseif($role_poison_cat){
      if($this_user->IsDead() && ! $this_user->IsSelf() && ! $this_user->IsDummyBoy()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($role_trap_mad){
      if($this_user->IsLive()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_user->IsLive() && ! $this_user->IsSelf() && ! $this_wolf){
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
  elseif($role_voodoo_killer){
    $type   = 'VOODOO_KILLER_DO';
    $submit = 'submit_voodoo_killer_do';
  }
  elseif($role_jammer_mad){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'submit_jammer_do';
  }
  elseif($role_voodoo_mad){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'submit_voodoo_do';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'submit_trap_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'submit_trap_not_do';
  }
  elseif($role_anti_voodoo){
    $type   = 'ANTI_VOODOO_DO';
    $submit = 'submit_anti_voodoo_do';
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
    $submit = 'submit_revive_do';
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'submit_revive_not_do';
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
  elseif($role_voodoo_fox){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'submit_voodoo_do';
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
