<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//セッション開始
session_start();
$session_id = session_id();

//引数を取得
$room_no     = $_GET['room_no'];
$auto_reload = (int)$_GET['auto_reload'];
$play_sound  = $_GET['play_sound'];
$list_down   = $_GET['list_down'];

//PHP の引数を作成
$php_argv = 'room_no=' . $room_no;
if($auto_reload != '') $php_argv .= '&auto_reload=' . $auto_reload;
if($play_sound  != '') $php_argv .= '&play_sound='  . $play_sound;
if($list_down   != '') $php_argv .= '&list_down='   . $list_down;
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">←戻る &amp; reload</a>';

//クッキーからシーンを取得 //DB に問い合わせるので不要
//$day_night = $_COOKIE['day_night'];

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

//日付、シーン、ステータスを取得
$sql = mysql_query("SELECT date, day_night, status FROM room WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$date      = $array['date'];
$day_night = $array['day_night'];
$status    = $array['status'];

//自分のハンドルネーム、役割、生存状態を取得
$sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];

$command = $_POST['command'];
$type    = $_POST['type']; //投票の分類 (Kick、処刑、占い、狼など)

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
  $target_no = $_POST['target_no'];
  $situation = $_POST['situation'];

  if($date == 0){ //ゲーム開始 or Kick 投票処理
    if($situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($situation == 'KICK_DO'){
      $target_handle_name = $_POST['target_handle_name'];
      VoteKick($_POST['target_handle_name']);
    }
    else{ //ここに来たらロジックエラー
      OutputActionResult('投票エラー',
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
    $vote_times = $_POST['vote_times']; //投票回数 (再投票の場合)
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
  if($day_night != '')  echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="command" value="vote">

EOF;
}

//投票結果出力
function OutputVoteResult($sentence, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //今までの投票を全部削除
  OutputActionResult('汝は人狼なりや？[投票結果]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $sentence . '<br>'."\n" .
		     $back_url . '</div>', '', $unlock);
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
  global $GAME_CONF, $room_no, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('ゲームスタート：無効な投票です');

  //投票総数、ゲームオプションを取得
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);
  $game_option = GetGameOption();

  //身代わり君使用なら身代わり君の分を加算
  if(strstr($game_option, 'dummy_boy')) $vote_count++;

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

  //ユーザリストをランダムに取得
  $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) as MyRand FROM user_entry
				WHERE room_no = $room_no AND user_no > 0 ORDER BY MyRand");

  $uname_array    = array(); //役割の決定したユーザ名を格納する
  $role_array     = array(); //ユーザ名に対応する役割
  $re_uname_array = array(); //希望の役割になれなかったユーザ名を一時的に格納

  for($i=0; $i < $user_count; $i++){ //希望の役割を選別
    $user_list_array = mysql_fetch_assoc($sql_user_list); //ランダムなユーザ情報を取得
    $this_uname = $user_list_array['uname'];

    if(strstr($game_option, 'wish_role')) //役割希望制の場合、希望を取得
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
  for($i=0; $i < $re_count; $i++){ //余った役割を割り当てる
    array_push($uname_array, $re_uname_array[$i]);
    array_push($role_array,  $now_role_list[$i]);
  }

  //兼任となる役割の設定
  $rand_keys = array_rand($role_array, $user_count); //ランダムキーを取得

  //兼任となるオプション役割(権力者、決定者)
  $option_subrole_count = 0;
  if(strstr($option_role, 'authority') && $user_count >= 16){
    $role_array[$rand_keys[$option_subrole_count]] .= ' authority';
    $option_subrole_count++;
    $authority_count++;
  }
  if(strstr($option_role, 'decide') && $user_count >= 16){
    $role_array[$rand_keys[$option_subrole_count]] .= ' decide';
    $option_subrole_count++;
    $decide_count++;
  }

  $dummy_boy_index = array_search('dummy_boy', $uname_array); //身代わり君の配列インデックスを取得

  //身代わり君使用の場合、身代わり君は狼、狐、埋毒者、キューピッド以外にする
  if(strstr($game_option, 'dummy_boy') &&
     (strstr($role_array[$dummy_boy_index], 'wolf')   ||
      strstr($role_array[$dummy_boy_index], 'fox')    ||
      strstr($role_array[$dummy_boy_index], 'poison') ||
      strstr($role_array[$dummy_boy_index], 'cupid'))){
    for($i=0; $i < $user_count; $i++){
      //狼、狐、埋毒者、キューピッド以外が見つかったら入れ替える
      if(! (strstr($role_array[$i], 'wolf')   || strstr($role_array[$i], 'fox') ||
	    strstr($role_array[$i], 'poison') || strstr($role_array[$i], 'cupid'))){
	$tmp_role = $role_array[$dummy_boy_index];
	$role_array[$dummy_boy_index] = $role_array[$i];
	$role_array[$i] = $tmp_role;
	break;
      }
    }
  }

  //ゲーム開始
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //今までの投票を全部削除

  //役割をDBに更新
  for($i=0; $i < $user_count; $i++){
    $entry_uname = $uname_array[$i];
    $entry_role  = $role_array[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    if(strstr($entry_role, 'human'))       $role_count_list['human']++;
    if(strstr($entry_role, 'wolf'))        $role_count_list['wolf']++;
    if(strstr($entry_role, 'mage'))        $role_count_list['mage']++;
    if(strstr($entry_role, 'necromancer')) $role_count_list['necromancer']++;
    if(strstr($entry_role, 'mad'))         $role_count_list['mad']++;
    if(strstr($entry_role, 'guard'))       $role_count_list['guard']++;
    if(strstr($entry_role, 'common'))      $role_count_list['common']++;
    if(strstr($entry_role, 'fox'))         $role_count_list['fox']++;
    if(strstr($entry_role, 'poison'))      $role_count_list['poison']++;
    if(strstr($entry_role, 'cupid'))       $role_count_list['cupid']++;
    if(strstr($entry_role, 'decide'))      $role_count_list['decide']++;
    if(strstr($entry_role, 'authority'))   $role_count_list['authority']++;
  }

  //それぞれの役割が何人ずつなのかシステムメッセージ
  $sentence = '村人' . (int)$role_count_list['human'] .
    '　人狼'         . (int)$role_count_list['wolf'] .
    '　占い師'       . (int)$role_count_list['mage'] .
    '　霊能者'       . (int)$role_count_list['necromancer'] .
    '　狂人'         . (int)$role_count_list['mad'] .
    '　狩人'         . (int)$role_count_list['guard'] .
    '　共有者'       . (int)$role_count_list['common'] .
    '　妖狐'         . (int)$role_count_list['fox'] .
    '　埋毒者'       . (int)$role_count_list['poison'] .
    '　キューピッド' . (int)$role_count_list['cupid'] .
    '　(決定者'      . (int)$role_count_list['decide'] . ')' .
    '　(権力者'      . (int)$role_count_list['authority'] . ')';

  //役割リスト通知
  $time = TZTime(); //現在時刻を取得
  InsertSystemTalk($sentence, $time, 'night system', 1);
  UpdateTime($time); //最終書き込み時刻を更新

  //初日の処刑投票のカウントを1に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', 1);
  mysql_query('COMMIT'); //一応コミット
}

//人数とゲームオプションに応じた役職テーブルを返す (エラー処理は暫定)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $GAME_CONF->role_list[$user_count]; //人数に応じた設定リストを取得
  if($role_list == NULL){ //リストの有無をチェック
    OutputVoteResult($error_header . $user_count . '人は設定されていません'
                     . $error_footer, true, true);
  }

  //埋毒者 (20人以上 / 村人２ → 毒１、狼１)
  if(strstr($option_role, 'poison') && ($user_count >= 20)){
    $role_list['human'] -= 2;
    $role_list['wolf']++;
    $role_list['poison']++;
  }

  //キューピッド (14人 or 16人以上 / 村人 → キューピッド）
  if(strstr($option_role, 'cupid') && ($user_count == 14 || $user_count >= 16)){
    $role_list['human']--;
    $role_list['cupid']++;
  }

  if($role_list['human'] < 0){ //"村人" の人数をチェック
    OutputVoteResult($error_header . '"村人" の人数がマイナスになってます' .
                     $error_footer, true, true);
  }

  //役職名を格納した配列を生成
  $now_role_list = array();
  foreach($role_list as $key => $value){
    for($i = 0; $i < $value; $i++) array_push($now_role_list, $key);
  }
  $role_count = count($now_role_list);

  if($role_count != $user_count){ //配列長をチェック
    OutputVoteResult($error_header . '村人 (' . $user_count . ') と配役の数 (' . $role_count .
                     ') が一致していません' . $error_footer, true, true);
  }

  return $now_role_list;
}

//開始前の Kick 投票の処理
function VoteKick($target){
  global $GAME_CONF, $room_no, $situation, $day_night, $uname, $handle_name, $target_no;

  //エラーチェック
  if($situation != 'KICK_DO') OutputVoteResult('Kick：無効な投票です');
  if($target == '') OutputVoteResult('Kick：投票先を指定してください');
  if($target == '身代わり君') OutputVoteResult('Kick：身代わり君には投票できません');

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
  if($target_uname == '') OutputVoteResult('Kick：'. $target . 'はすでに Kick されています', true);

  //投票処理
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, situation)
			VALUES($room_no, 0, '$uname', '$target_uname', 'KICK_DO')");
  //投票しました通知
  InsertSystemTalk("KICK_DO\t" . $target, TZTime(), '', 0, $uname);

  //登録成功
  if($sql && mysql_query('COMMIT')){ //一応コミット
    CheckVoteKick($target); //集計処理
    OutputVoteResult('投票完了(Kick するには ' . $GAME_CONF->kick . ' 人以上の投票が必要です)', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//Kick 投票の集計処理
function CheckVoteKick($target){
  global $GAME_CONF, $MESSAGE, $room_no, $situation, $uname;

  if($situation != 'KICK_DO') OutputVoteResult('Kick：無効な投票です');

  //今回投票した相手へ何人投票しているか
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = '$situation' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //投票総数を取得

  //規定数以上の投票があったかキッカーが身代わり君の場合に処理
  if($vote_count < $GAME_CONF->kick && $uname != 'dummy_boy') return false;
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

  //満員の場合、募集中に戻す //現在は満員時に表示を変えないのでこの処理は不要じゃないかな？
  mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = $room_no");
  DeleteVote(); //今までの投票を全部削除

  //キックされて空いた場所を詰める
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = $room_no AND user_no = $next");
  }

  //最終書き込み時刻を更新
  $time = TZTime();  //現在時刻を取得
  UpdateTime($time);

  //出て行ったメッセージ
  InsertSystemTalk($target . $MESSAGE->kick_out, ++$time);
  InsertSystemTalk($MESSAGE->vote_reset, ++$time);

  mysql_query('COMMIT'); //一応コミット
}

//昼の投票処理
function VoteDay(){
  global $room_no, $situation, $date, $vote_times, $uname, $handle_name, $target_no;

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

  //権力者なら投票数が２
  $vote_number = (strstr($role, 'authority') ? 2 : 1);

  //投票
  $sql = mysql_query("INSERT INTO vote(room_no,date,uname,target_uname,vote_number,vote_times,situation)
		VALUES($room_no,$date,'$uname','$target_uname',$vote_number,$vote_times,'$situation')");

  //投票しました通知
  InsertSystemTalk("VOTE_DO\t" . $target_handle, TZTime(), 'day system', '', $uname);

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
  global $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('処刑：投票エラー');

  //投票総数を取得
  $sql = mysql_query("select count(uname) from vote where room_no = $room_no
			and date = $date and situation = '$situation'
			and vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //生きているユーザ数を取得
  $sql_user = mysql_query("select uname,handle_name,role from user_entry
		where room_no = $room_no and live = 'live' and user_no > 0 order by user_no");
  $user_count = mysql_num_rows($sql_user);

  //全員が投票していた場合
  if($vote_count != $user_count) return false;

  $check_draw = true; //引き分け判定実行フラグ
  $max_voted_number = 0; //最も票を入れられた人の票数
  $vote_number_list = array(); //投票された人と受けた総票数のリスト（user1に３票入っていた ：$vote_number_list['user1'] => 3）
  $vote_role_list = array(); //投票された人の役割リスト
  $live_handle_name_list = array(); //生きている人のハンドルネームリスト

  //一人ずつ自分に投票された数を調べて処刑すべき人を決定する
  for($i = 0 ; $i < $user_count ; $i++){
    //ユーザNoの若い順から処理
    $this_user_arr = mysql_fetch_assoc($sql_user);
    $this_uname = $this_user_arr['uname'];
    $this_handle_name = $this_user_arr['handle_name'];
    $this_role = $this_user_arr['role'];

    //自分に投票された総評数
    $sql = mysql_query("select sum(vote_number) from vote where room_no = $room_no and date = $date
			and situation = '$situation' and vote_times = $vote_times
			and target_uname = '$this_uname'");
    //投票された総票数
    $this_voted_number = (int)mysql_result($sql, 0, 0);

    //自分が投票した票数
    $sql =mysql_query("select vote_number from vote where room_no = $room_no and date = $date
			and situation = '$situation' and vote_times = $vote_times
			and uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //自分が投票した人のハンドルネームを取得
    $sql = mysql_query("select user_entry.handle_name as handle_name from user_entry,vote 
			where user_entry.room_no = $room_no and vote.room_no = $room_no 
			and vote.date = $date
			and vote.situation = '$situation' and vote_times = $vote_times
			and vote.uname = '$this_uname' and user_entry.uname = vote.target_uname
			and user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //投票結果をタブ区切りで出力 ( 誰が [TAB] 誰に [TAB] 自分への投票数 [TAB] 自分の投票数 [TAB] vote_times)
    $sentence = $this_handle_name . "\t" .  $this_vote_target . "\t" .
      (int)$this_voted_number ."\t" . (int)$this_vote_number . "\t" . (int)$vote_times ;

    //投票情報をシステムメッセージに登録
    InsertSystemMessage($sentence, $situation);

    //最大票数を更新
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //投票された人と受けた総票数のリスト（user1に３票入っていた ：$vote_HN_number_list['user1_handle_name'] => 3）
    $vote_HN_number_list[$this_handle_name] = $this_voted_number;
    $vote_uname_number_list[$this_uname] = $this_voted_number; //$vote_uname_number_list['user1_uname'] => 3）

    $vote_role_list[$this_handle_name] = $this_role; //$vote_role_list['user1'] => 'human'
    array_push($live_handle_name_list,$this_handle_name); //生きている人のリスト
  }

  //最大票数を集めた人の数を取得
  $max_voted_num_arr = array_count_values($vote_HN_number_list); // $max_voted_num_arr[票数] = その票数は何個あったか
  $max_voted_num = $max_voted_num_arr[$max_voted_number]; //$max_voted_num_arr[最大票数]の人の人数

  //最大票数の人のハンドルネームのリストを取得
  //$max_voted_HN_arr[0,1,2・・・] = 最大票数の人のハンドルネーム
  $max_voted_HN_arr = array_keys($vote_HN_number_list,$max_voted_number);

  //$max_voted_HN_arr[0,1,2・・・] = 最大票数の人のハンドルネーム
  $max_voted_uname_arr = array_keys($vote_uname_number_list,$max_voted_number);

  if($max_voted_num == 1){ //一人だけの場合、処刑して夜にする
    $max_voted_handle_name = $max_voted_HN_arr[0];
    //処刑される人の役割
    $max_voted_role = $vote_role_list[$max_voted_handle_name];

    //処刑
    VoteKill($max_voted_handle_name,$max_voted_role,$live_handle_name_list);
    $check_draw = false;
  }
  else{ //複数いたばあい、決定者が居なければ再投票
    $re_voting_flag = true; //再投票フラグ初期化

    for($i=0 ; $i < $max_voted_num ; $i++){
      $max_vote_uname = $max_voted_uname_arr[$i]; //投票された人のユーザ名取得
      $max_voted_handle_name = $max_voted_HN_arr[$i]; //投票された人のハンドルネーム取得
      $max_voted_role = $vote_role_list[$max_voted_handle_name]; //投票された人の役割取得

      //投票者の役割取得
      $sql_max_voter_role = mysql_query("select user_entry.role from user_entry,vote 
					where user_entry.room_no = $room_no 
					and vote.room_no = $room_no and vote.date = $date
						and vote.situation = '$situation'
					and vote.vote_times = $vote_times
					and vote.uname = user_entry.uname
					and vote.target_uname = '$max_vote_uname'
					and user_entry.user_no > 0");
      $max_voter_count = mysql_num_rows($sql_max_voter_role);

      for($j=0 ; $j < $max_voter_count ; $j++){
	$max_voter_role = mysql_result($sql_max_voter_role,$j,0);

	if(strstr($max_voter_role,"decide")){ //投票者が決定者なら処刑
	  $re_voting_flag = false;
	  break;
	}
      }
      if($re_voting_flag == false) break;
    }

    if($re_voting_flag == true){ //再投票
      //投票回数を増やす
      $next_vote_times = $vote_times +1 ;

      mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

      //システムメッセージ
      $time = TZTime();
      InsertSystemMessage($vote_times, 'RE_VOTE');
      InsertSystemTalk("再投票になりました( $vote_times 回目)", $time);
      UpdateTime(++$time); //最終書き込みを更新
    }
    else{ //処刑して夜にする
      VoteKill($max_voted_handle_name, $max_voted_role, $live_handle_name_list);
      $check_draw = false;
    }
  }
  CheckVictory($check_draw);
}

//投票で処刑する
function VoteKill($handle_name, $role, $live_list){
  global $room_no, $date;

  //処刑処理
  DeadUser($handle_name, true); //死亡処理
  InsertSystemMessage($handle_name, 'VOTE_KILLED'); //システムメッセージ
  SaveLastWords($handle_name); //処刑者の遺言

  //処刑された人が埋毒者の場合
  if(strstr($role, 'poison')){
    //他の人からランダムに一人選ぶ
    //恋人後追い処理を先にすると後追いした恋人も含めてしまうので
    //改めて「現在の生存者」を DB に問い合わせるべきじゃないかな？
    $array = array_diff($live_list, array("$handle_name"));
    $rand_key = array_rand($array, 1);
    $poison_dead_handle = $array[$rand_key];

    DeadUser($poison_dead_handle, true); //死亡処理
    InsertSystemMessage($poison_dead_handle, 'POISON_DEAD_day'); //システムメッセージ

    //毒死した人の役職、遺言を取得
    $sql = mysql_query("SELECT role, last_words FROM user_entry WHERE room_no = $room_no
				AND handle_name = '$poison_dead_handle' AND user_no > 0");
    $poison_array = mysql_fetch_assoc($sql);
    $poison_role  = $poison_array['role'];
    $poison_last_words = $poison_array['last_words'];

    //毒死した人の遺言を残す
    if($poison_last_words != '')
      InsertSystemMessage($poison_dead_handle . "\t" . $poison_last_words, 'LAST_WORDS');

    //毒死した人が恋人の場合
    if(strstr($poison_role, 'lovers')) LoversFollowed();
  }

  //処刑された人が恋人の場合
  //処刑後すぐ後追いするのが筋だと思うけど
  //現状では埋毒者のターゲット選出処理が甘いのでここで処理
  if(strstr($role, 'lovers')) LoversFollowed();

  //霊能者の結果(システムメッセージ)
  if(strstr($role, 'wolf'))
    $necro_max_voted_role = 'wolf';
  else
    $necro_max_voted_role = 'human';

  InsertSystemMessage($handle_name . "\t" . $necro_max_voted_role, 'NECROMANCER_RESULT');

  $time = TZTime();  //現在時刻を取得
  mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //夜にする
  InsertSystemTalk('NIGHT', $time, 'night system'); //夜がきた通知
  UpdateTime($time); //最終書き込みを更新
  DeleteVote(); //今までの投票を全部削除
  mysql_query('COMMIT'); //一応コミット
}

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $room_no, $situation, $date, $uname, $handle_name, $role, $target_no;

  switch($situation){
    case('WOLF_EAT'):
      if(! strstr($role, 'wolf')) OutputVoteResult('夜：人狼以外は投票できません');
      break;

    case('MAGE_DO'):
      if(! strstr($role, 'mage')) OutputVoteResult('夜：占い師以外は投票できません');
      if($uname == 'dummy_boy')   OutputVoteResult('夜：身代わり君の占いは無効です');
      break;

    case('GUARD_DO'):
      if(! strstr($role, 'guard')) OutputVoteResult('夜：狩人以外は投票できません');
      break;

    case('CUPID_DO'):
      if(! strstr($role, 'cupid')) OutputVoteResult('夜：キューピッド以外は投票できません');
      break;

    default:
      OutputVoteResult('夜：あなたは投票できません');
      break;
  }
  CheckAlreadyVote($situation); //投票済みチェック

 //エラーメッセージのヘッダ
  $error_header = '夜：投票先が正しくありません<br>';

  if(strstr($role, 'cupid')){  //キューピッドの場合の投票処理
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
       (strstr($role, 'wolf') && strstr($target_role, 'wolf'))){
      OutputVoteResult($error_header . '死者、自分、狼同士は投票できません');
    }

    //狼の初日の投票は身代わり君使用の場合は身代わり君以外無効
    if($situation == 'WOLF_EAT'){
      $game_option = GetGameOption();
      if(strstr($game_option, 'dummy_boy') && $target_uname != 'dummy_boy' && $date == 1){
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
      }
    }
  }

  LockTable(); //テーブルを排他的ロック
  if(strstr($role, 'cupid')){ // キューピッドの処理
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
      $target_role .= " lovers";
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
  InsertSystemTalk($situation . "\t" . $target_handle_str, TZTime(), 'night system', '', $uname);

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
  global $GAME_CONF, $room_no, $situation, $date, $day_night, $vote_times,
    $uname, $handle_name, $target_no, $target_handle_name;

  //ゲームオプション取得
  $game_option = GetGameOption();

  if(! ($situation == 'WOLF_EAT' || $situation == 'MAGE_DO' ||
	$situation == 'GUARD_DO' || $situation == 'CUPID_DO')){
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
  $sql = mysql_query($query_role . "'mage%'");
  $mage_count = mysql_result($sql, 0, 0);

  if($date == 1 && strstr($game_option, 'dummy_boy')){
    //初日、身代わり君の役割が占い師の場合占い師の数に入れない
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    $dummy_boy_role = mysql_result($sql, 0, 0);
    if(strstr($dummy_boy_role, 'mage')) $mage_count--;
  }
  if($vote_count != $mage_count) return false;

  $guard_count = 0;
  $cupid_count = 0;
  if($date == 1){ //初日のみキューピッドの投票チェック
    $sql = mysql_query($query_vote . "'CUPID_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //生きているキューピッドの数を取得
    $sql = mysql_query($query_role . "'cupid%'");
    $cupid_count = mysql_result($sql, 0, 0);
    if($vote_count != $cupid_count) return false;
  }
  else{ //初日以外の狩人の投票チェック
    $sql = mysql_query($query_vote . "'GUARD_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'guard%'");
    $guard_count = mysql_result($sql, 0, 0);
    if($vote_count != $guard_count) return false;
  }

  //狼と狩人は同時に処理
  //狩人の投票先ユーザ名、狩人のハンドルネームを取得
  $sql_guard = mysql_query("SELECT vote.target_uname, user_entry.handle_name FROM vote,	user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'GUARD_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //狼の投票先ユーザ名とその役割を取得
  $sql_wolf = mysql_query("SELECT vote.target_uname, user_entry.role, user_entry.handle_name
				FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'WOLF_EAT'
				AND vote.target_uname = user_entry.uname AND user_entry.user_no > 0");
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_role   = $wolf_target_array['role'];
  // $wolf_target_live   = $wolf_target_array['live'];//DBから引いてないような？？？
  $wolf_target_handle = $wolf_target_array['handle_name'];

  $guard_success_flag = false;
  for($i=0; $i < $guard_count; $i++ ){ //護衛成功かチェック
    $guard_array  = mysql_fetch_assoc($sql_guard);
    $guard_handle = $guard_array['handle_name'];
    $guard_uname  = $guard_array['target_uname'];

    if($guard_uname == $wolf_target_uname){ //護衛成功
      //護衛成功のメッセージ
      $system_message = $guard_handle . "\t" . $wolf_target_handle;
      InsertSystemMessage($system_message, 'GUARD_SUCCESS');
      $guard_success_flag = true;
    }
  }

  if($guard_success_flag){
    //護衛成功
  }
  elseif(strstr($wolf_target_role, 'fox')){ //食べる先が狐の場合食べれない
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //護衛されてなければ食べる
    DeadUser($wolf_target_uname); //食べられた人死亡
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED'); //システムメッセージ

    //食べられた人の遺言を残す
    SaveLastWords($wolf_target_handle);

    //食べられた人が埋毒者の場合
    if(strstr($wolf_target_role, 'poison')){
      if($GAME_CONF->poison_only_eater){ //噛んだ狼を取得
	$sql_wolf_list = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role
					FROM user_entry, vote WHERE user_entry.room_no = $room_no
					AND user_entry.uname = vote.uname AND vote.date = $date
					AND vote.situation = 'WOLF_EAT' AND user_no > 0");
      }
      else{ //生きている狼を取得
	$sql_wolf_list = mysql_query("SELECT uname, handle_name, role FROM user_entry
					WHERE room_no = $room_no AND role LIKE 'wolf%'
					AND live = 'live' AND user_no > 0");
      }
      $poison_wolf_count = mysql_num_rows($sql_wolf_list);

      $wolf_list_array = array();
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false){
	array_push($wolf_list_array, $wolf);
      }

      $rand_key = array_rand($wolf_list_array, 1);
      $poison_dead_wolf_array  = $wolf_list_array[$rand_key];
      $poison_dead_wolf_uname  = $poison_dead_wolf_array['uname'];
      $poison_dead_wolf_handle = $poison_dead_wolf_array['handle_name'];
      $poison_dead_wolf_role   = $poison_dead_wolf_array['role'];

      DeadUser($poison_dead_wolf_uname); //死亡処理
      InsertSystemMessage($poison_dead_wolf_handle, 'POISON_DEAD_night'); //システムメッセージ
      SaveLastWords($poison_dead_wolf_handle); //遺言処理
      if(strstr($poison_dead_wolf_role, 'lovers')) LoversFollowed(); //毒死した狼が恋人の場合
    }
    if(strstr($wolf_target_role, 'lovers')) LoversFollowed(); //食べられた人が恋人の場合
  }

  //占い師のユーザ名、ハンドルネームと、占い師の生存、占い師が占ったユーザ名取得
  $sql_mage = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.live, 
				vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'MAGE_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //占い師の人数分、処理
  for($i=0; $i < $mage_count; $i++){
    $array = mysql_fetch_assoc($sql_mage);
    $mage_uname  = $array['uname'];
    $mage_handle = $array['handle_name'];
    $mage_live   = $array['live'];
    $mage_target_uname = $array['target_uname'];

    //直前に狼に食べられていたらこの占いは無効
    if($mage_live == 'dead') continue;

    //占い師に占われた人のハンドルネームと生存、役割を取得
    $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$mage_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $mage_target_handle = $array['handle_name'];
    $mage_target_role   = $array['role'];
    $mage_target_live   = $array['live'];

    if(strstr($mage_target_role, 'fox') && $mage_target_live == 'live'){ //狐が占われたら死亡
      DeadUser($mage_target_uname);
      InsertSystemMessage($mage_target_handle, 'FOX_DEAD');
      SaveLastWords($mage_target_handle); //占われた狐の遺言を残す
      if(strstr($mage_target_role, 'lovers')) LoversFollowed(); //占われた狐が恋人の場合
    }

    //占い結果を出力
    $sentence = $mage_handle . "\t" . $mage_target_handle . "\t" .
      (strstr($mage_target_role, 'wolf') ? 'wolf' : 'human');
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  //次の日にする
  $time = TZTime(); //現在時刻を取得
  $next_date = $date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");

  //次の日の処刑投票のカウントを 1 に初期化(再投票で増える)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //夜が明けた通知
  InsertSystemTalk("MORNING\t" . $next_date, $time, $location = 'day system', $next_date);
  UpdateTime($time); //最終書き込みを更新
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
  $sql = mysql_query("SELECT count(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  if(mysql_result($sql, 0, 0)) OutputVoteResult('処刑：投票済み');

  //ユーザ一覧とアイコンのデータ取得
  $sql_user = mysql_query("SELECT user_entry.user_no, user_entry.uname,
			user_entry.handle_name, user_entry.live,
			user_icon.icon_filename, user_icon.color as color
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
  global $MESSAGE, $ICON_CONF, $room_no, $date, $uname, $role, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票済みチェック
  if(strstr($role, 'wolf')){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif(strstr($role, 'mage')){
    if($uname == 'dummy_boy') OutputVoteResult('夜：身代わり君の占いは無効です');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif(strstr($role, 'guard')){
    if($date == 1) OutputVoteResult('夜：初日の護衛はできません');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif(strstr($role, 'cupid')){
    if($date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('CUPID_DO');
  }
  else{
    OutputVoteResult('夜：あなたは投票できません');
  }

  //ゲームオプション取得(身代わり君用)
  $game_option = GetGameOption();

  if(strstr($role, 'wolf') && strstr($game_option, 'dummy_boy') && $date == 1){
    //身代わり君の時は身代わり君だけしか選べない
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
  $sql_count = mysql_num_rows($sql);

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  for($i=0; $i < $sql_count; $i++){
    $array = mysql_fetch_assoc($sql);

    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_role    = $array['role'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];

    //5個ごとに改行
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";

    if($this_live == 'live' && strstr($role, 'wolf') && strstr($this_role, 'wolf')){ //狼同士なら狼アイコン
      $location = $ICON_CONF->wolf;
    }
    elseif($this_live == 'live'){ //生きていればユーザアイコン
      $location = $ICON_CONF->path . '/' . $this_file;
    }
    else{ //死んでれば死亡アイコン
      $location = $ICON_CONF->dead;
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$location" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if(strstr($role, 'cupid')){
      if(! strstr($this_uname, 'dummy_boy')){
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname &&
	   ! (strstr($role, 'wolf') && strstr($this_role, 'wolf'))){
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

  if(strstr($role, 'wolf')){
    echo '<input type="hidden" name="situation" value="WOLF_EAT">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE->submit_wolf_eat . '"></td>'."\n";
  }
  elseif(strstr($role, 'mage')){
    echo '<input type="hidden" name="situation" value="MAGE_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE->submit_mage_do . '"></td>'."\n";
  }
  elseif(strstr($role, 'guard')){
    echo '<input type="hidden" name="situation" value="GUARD_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE->submit_guard_do . '"></td>'."\n";
  }
  elseif(strstr($role, 'cupid')){
    echo '<input type="hidden" name="situation" value="CUPID_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE->submit_cupid_do . '"></td>'."\n";
  }

  echo <<<EOF
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

//ゲームオプション取得(主に身代わり君処理用)
function GetGameOption(){
  global $room_no;

  $sql = mysql_query("SELECT game_option FROM room WHERE room_no = $room_no");
  return mysql_result($sql, 0, 0);
}

//遺言を取得して保存する
function SaveLastWords($handle_name){
  global $room_no;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$handle_name' AND user_no > 0");
  $last_words = mysql_result($sql, 0, 0);
  if($last_words != ''){
    InsertSystemMessage($handle_name . "\t" . $last_words, 'LAST_WORDS');
  }
}
?>
