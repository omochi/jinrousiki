<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

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

//投票結果出力
function OutputVoteResult($str, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //今までの投票を全部削除
  OutputActionResult('汝は人狼なりや？[投票結果]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $str . '<br>'."\n" .
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
  $option_subrole = array();
  $option_subrole_count = 0;
  if(strpos($option_role, 'decide') !== false && $user_count >= $GAME_CONF->decide){
    $role_array[$rand_keys[$option_subrole_count]] .= ' decide';
    $option_subrole_count++;
    $option_subrole['decide']++;
  }
  if(strpos($option_role, 'authority') !== false && $user_count >= $GAME_CONF->authority){
    $role_array[$rand_keys[$option_subrole_count]] .= ' authority';
    $option_subrole_count++;
    $option_subrole['authority']++;
  }
  if($chaos){
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if($user_count < $option_subrole_count) break;
      if($key == 'decite' || $key == 'authority') continue; //決定者と権力者はオプションで制御する
      if((int)$option_subrole[$key] > 0) continue; //既に誰かに渡していればスキップ
      $role_array[$rand_keys[$option_subrole_count]] .= ' ' . $key;
      $option_subrole[$key]++;
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
    if(strpos($entry_role, 'decide')        !== false) $role_count_list['decide']++;
    if(strpos($entry_role, 'authority')     !== false) $role_count_list['authority']++;
    if(strpos($entry_role, 'strong_voice')  !== false) $role_count_list['strong_voice']++;
    if(strpos($entry_role, 'normal_voice')  !== false) $role_count_list['normal_voice']++;
    if(strpos($entry_role, 'weak_voice')    !== false) $role_count_list['weak_voice']++;
    if(strpos($entry_role, 'no_last_words') !== false) $role_count_list['no_last_words']++;
    if(strpos($entry_role, 'chicken')       !== false) $role_count_list['chicken']++;
    if(strpos($entry_role, 'rabbit')        !== false) $role_count_list['rabbit']++;
    if(strpos($entry_role, 'perverseness' ) !== false) $role_count_list['perverseness']++;
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

//人数とゲームオプションに応じた役職テーブルを返す (エラー処理は暫定)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $game_option;

  $error_header = 'ゲームスタート[配役設定エラー]：';
  $error_footer = '。<br>管理者に問い合わせて下さい。';

  $role_list = $GAME_CONF->role_list[$user_count]; //人数に応じた設定リストを取得
  if($role_list == NULL){ //リストの有無をチェック
    OutputVoteResult($error_header . $user_count . '人は設定されていません' .
                     $error_footer, true, true);
  }

  //埋毒者 (村人２ → 毒１、狼１)
  if(strpos($option_role, 'poison') !== false && $user_count >= $GAME_CONF->poison){
    $role_list['human'] -= 2;
    $role_list['poison']++;
    $role_list['wolf']++;
  }

  //キューピッド (14人はハードコード / 村人 → キューピッド)
  if(strpos($option_role, 'cupid') !== false &&
     ($user_count == 14 || $user_count >= $GAME_CONF->cupid)){
    $role_list['human']--;
    $role_list['cupid']++;
  }

  //白狼 (人狼 → 白狼)
  if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $GAME_CONF->boss_wolf){
    $role_list['wolf']--; //マイナスのチェックしてないので注意
    $role_list['boss_wolf']++;
  }

  if(strpos($game_option, 'quiz') !== false){  //クイズ村
    $temp_role_list = array();
    $temp_role_list['human'] = $role_list['human'];
    foreach($role_list as $key => $value){
      if($key == 'wolf' || $key == 'mad' || $key == 'common' || $key == 'fox'){
	$temp_role_list[$key] = (int)$value;
      }
      elseif($key != 'human'){
	$temp_role_list['human'] += (int)$value;
      }
    }
    $temp_role_list['human']--;
    $temp_role_list['quiz'] = 1;
    $role_list = $temp_role_list;
  }
  elseif(strpos($game_option, 'chaos') !== false){ //闇鍋
    if(strpos($game_option, 'chaosfull') !== false){ //真・闇鍋
      //-- 各陣営の人数を決定 (人数 = 各人数の出現率) --//
      //人狼陣営
      $rand = mt_rand(1, 100); //人数決定用乱数
      if($user_count < 8){ //1:2 = 80:20
	if($rand <= 80) $wolf_count = 1;
	else $wolf_count = 2;
      }
      elseif($user_count < 16){ //1:2:3 = 15:70:15
	if($rand <= 15) $wolf_count = 1;
	elseif($rand <= 85) $wolf_count = 2;
	else $wolf_count = 3;
      }
      elseif($user_count < 21){ //1:2:3:4:5 = 5:10:70:10:5
	if($rand <= 5) $wolf_count = 1;
	elseif($rand <= 15) $wolf_count = 2;
	elseif($rand <= 85) $wolf_count = 3;
	elseif($rand <= 95) $wolf_count = 4;
	else $wolf_count = 5;
      }
      else{ //以後、5人増えるごとに 1人ずつ増加
	$base_count = floor(($user_count - 20) / 5) + 3;
	if($rand <= 5) $wolf_count = $base_count - 2;
	elseif($rand <= 15) $wolf_count = $base_count - 1;
	elseif($rand <= 85) $wolf_count = $base_count;
	elseif($rand <= 95) $wolf_count = $base_count + 1;
	else $wolf_count = $base_count + 2;
      }

      //妖狐陣営
      $rand = mt_rand(1, 100); //人数決定用乱数
      if($user_count < 15){ //0:1 = 90:10
	if($rand <= 90) $fox_count = 0;
	else $fox_count = 1;
      }
      elseif($user_count < 23){ //1:2 = 90:10
	if($rand <= 90) $fox_count = 1;
	else $fox_count = 2;
      }
      else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
	$base_count = ceil($user_count / 20);
	if($rand <= 10) $fox_count = $base_count - 1;
	elseif($rand <= 90) $fox_count = $base_count;
	else $fox_count = $base_count + 1;
      }

      //恋人陣営 (実質キューピッド)
      $rand = mt_rand(1, 100); //人数決定用乱数
      if($user_count < 10){ //0:1 = 95:5
	if($rand <= 95) $lovers_count = 0;
	else $lovers_count = 1;
      }
      elseif($user_count < 16){ //0:1 = 70:30
	if($rand <= 70) $lovers_count = 0;
	else $lovers_count = 1;
      }
      elseif($user_count < 23){ //0:1:2 = 5:90:5
	if($rand <= 5) $lovers_count = 0;
	elseif($rand <= 95) $lovers_count = 1;
	else $lovers_count = 2;
      }
      else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
	//基礎-1:基礎:基礎+1 = 5:90:5
	$base_count = floor($user_count / 20);
	if($rand <= 5) $lovers_count = $base_count - 1;
	elseif($rand <= 95) $lovers_count = $base_count;
	else $lovers_count = $base_count + 1;
      }
      $role_list['cupid'] = $lovers_count;
    }
    else{ //通常闇鍋
      $wolf_count   = $role_list['wolf'] + $role_list['boss_wolf'];
      $fox_count    = $role_list['fox'] + $role_list['child_fox'];
      $lovers_count = $role_list['cupid'];
    }
    //村人陣営の人数を算出
    $human_count = $user_count - $wolf_count - $fox_count - $lovers_count;

    //人狼系の配役を決定
    $boss_wolf_count = 0; //白狼の人数
    $base_count = ceil($user_count / 15); //特殊狼判定回数を算出
    for(; $base_count > 0; $base_count--){
      if(mt_rand(1, 100) <= $user_count) $boss_wolf_count++; //参加人数 % の確率で白狼出現
    }
    if($boss_wolf_count > $wolf_count){ //狼の総数を超えたら人狼は 0 にする
      $role_list['boss_wolf'] = $wolf_count;
      $role_list['wolf'] = 0;
    }
    else{
      $role_list['boss_wolf'] = $boss_wolf_count;
      $role_list['wolf'] = $boss_wolf_count - $wolf_count;
    }

    //妖狐系の配役を決定
    if($user_count < 20){ //全人口が20人未満の場合は子狐は出現しない
      $role_list['fox'] = $fox_count;
      $role_list['child_fox'] = 0;
    }
    else{ //参加人数 % で子狐が一人出現
      if(mt_rand(1, 100) <= $user_count) $role_list['child_fox'] = 1;
      $role_list['fox'] = $fox_count - (int)$role_list['child_fox'];
    }

    //占い系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 8){ //0:1 = 10:90
      if($rand <= 10) $mage_count = 0;
      else $mage_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $mage_count = 1;
      else $mage_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $mage_count = 1;
      else $mage_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mage_count = $base_count - 1;
      elseif($rand <= 90) $mage_count = $base_count;
      else $mage_count = $base_count + 1;
    }

    //占い系の配役を決定
    if($mage_count > 0 && $human_count >= $mage_count){
      if($user_count < 16){ //全人口が16人未満の場合は魂の占い師は出現しない
	$role_list['mage'] = $mage_count;
	$role_list['soul_mage'] = 0;
      }
      else{ //参加人数 % で魂の占い師が一人出現
	if(mt_rand(1, 100) <= $user_count) $role_list['soul_mage'] = 1;
	$role_list['mage'] = $mage_count - (int)$role_list['soul_mage'];
      }
      $human_count -= $mage_count; //村人陣営の残り人数
    }

    //巫女の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 9){ //0:1 = 70:30
      if($rand <= 70) $medium_count = 0;
      else $medium_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $medium_count = 0;
      elseif($rand <= 90) $medium_count = 1;
      else $medium_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $medium_count = $base_count - 1;
      elseif($rand <= 90) $medium_count = $base_count;
      else $medium_count = $base_count + 1;
    }
    if($cupid_count > 0 && $medium_count == 0) && $medium_count++;

    //巫女の配役を決定
    if($medium_count > 0 && $human_count >= $medium_count){
      $role_list['medium'] = $medium_count;
      $human_count -= $medium_count; //村人陣営の残り人数
    }

    //霊能系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 9){ //0:1 = 10:90
      if($rand <= 10) $necromancer_count = 0;
      else $necromancer_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $necromancer_count = $base_count - 1;
      elseif($rand <= 90) $necromancer_count = $base_count;
      else $necromancer_count = $base_count + 1;
    }

    //霊能系の配役を決定
    if($necromancer_count > 0 && $human_count >= $necromancer_count){
      $role_list['necromancer'] = $necromancer_count;
      $human_count -= $necromancer_count; //村人陣営の残り人数
    }

    //狂人系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 10){ //0:1 = 30:70
      if($rand <= 30) $mad_count = 0;
      else $mad_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $mad_count = 0;
      elseif($rand <= 90) $mad_count = 1;
      else $mad_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mad_count = $base_count - 1;
      elseif($rand <= 90) $mad_count = $base_count;
      else $mad_count = $base_count + 1;
    }

    //狂人系の配役を決定
    if($human_count > 0 && $human_count >= $mad_count){
      if($user_count < 16){ //全人口が16人未満の場合は狂信者は出現しない
	$role_list['mad'] = $mad_count;
	$role_list['fanatic_mad'] = 0;
      }
      else{ //参加人数 % で狂信者が一人出現
	if(mt_rand(1, 100) <= $user_count) $role_list['fanatic_mad'] = 1;
	$role_list['mad'] = $mad_count - (int)$role_list['fanatic_mad'];
      }
      $human_count -= $mad_count; //村人陣営の残り人数
    }

    //狩人系の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 11){ //0:1 = 10:90
      if($rand <= 10) $guard_count = 0;
      else $guard_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $guard_count = 0;
      elseif($rand <= 90) $guard_count = 1;
      else $guard_count = 2;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15);
      if($rand <= 10) $guard_count = $base_count - 1;
      elseif($rand <= 90) $guard_count = $base_count;
      else $guard_count = $base_count + 1;
    }

    //狩人系の配役を決定
    if($human_count > 0 && $human_count >= $guard_count){
      if($user_count < 20){ //全人口が20人未満の場合は騎士は出現しない
	$role_list['guard'] = $guard_count;
	$role_list['poison_guard'] = 0;
      }
      else{ //参加人数 % で騎士が一人出現
	if(mt_rand(1, 100) <= $user_count) $role_list['poison_guard'] = 1;
	$role_list['guard'] = $guard_count - (int)$role_list['poison_guard'];
      }
      $human_count -= $guard_count; //村人陣営の残り人数
    }

    //共有者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 13){ //0:1 = 10:90
      if($rand <= 10) $common_count = 0;
      else $common_count = 1;
    }
    elseif($user_count < 22){ //1:2:3 = 10:80:10
      if($rand <= 10) $common_count = 1;
      elseif($rand <= 90) $common_count = 2;
      else $common_count = 3;
    }
    else{ //以後、参加人数が15人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 15) + 1;
      if($rand <= 10) $common_count = $base_count - 1;
      elseif($rand <= 90) $common_count = $base_count;
      else $common_count = $base_count + 1;
    }

    //共有者の配役を決定
    if($common_count > 0 && $human_count >= $common_count){
      $role_list['common'] = $common_count;
      $human_count -= $common_count; //村人陣営の残り人数
    }

    //埋毒者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 15){ //0:1 = 95:5
      if($rand <= 95) $poison_count = 0;
      else $poison_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $poison_count = 0;
      else $poison_count = 1;
    }
    else{ //以後、参加人数が20人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 20);
      if($rand <= 10) $poison_count = $base_count - 1;
      elseif($rand <= 90) $poison_count = $base_count;
      else $poison_count = $base_count + 1;
    }
    $poison_count -= $poison_guard_count; //騎士の数だけ減らす

    //埋毒者の配役を決定
    if($poison_count > 0 && $human_count >= $poison_count){
      $role_list['poison'] = $poison_count;
      $human_count -= $poison_count; //村人陣営の残り人数
    }

    //出題者の人数を決定
    $rand = mt_rand(1, 100); //人数決定用乱数
    if($user_count < 30){ //0:1 = 99:1
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }
    else{ //以後、参加人数が30人増えるごとに 1人ずつ増加
      $base_count = floor($user_count / 30) - 1;
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }

    //出題者の配役を決定
    if($quiz_count > 0 && $human_count >= $quiz_count){
      $role_list['quiz'] = $quiz_count;
      $human_count -= $quiz_count; //村人陣営の残り人数
    }

    $role_list['human'] = $human_count; //村人の人数
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

//身代わり君がなれない役職をチェックする
function CheckRole($role){
  return (strpos($role, 'wolf')   !== false ||
	  strpos($role, 'fox')    !== false ||
	  strpos($role, 'poison') !== false ||
	  strpos($role, 'cupid')  !== false);
}


//役職の人数通知リストを作成する
function MakeRoleNameList($role_count_list){
  global $GAME_CONF;

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '　' . $value . $count;
  }
  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '　(' . $value . $count . ')';
  }
  return $sentence;
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

  //権力者なら投票数が２
  $vote_number = (strpos($role, 'authority') !== false ? 2 : 1);

  //投票
  $sql = mysql_query("INSERT INTO vote(room_no,date,uname,target_uname,vote_number,vote_times,situation)
		VALUES($room_no,$date,'$uname','$target_uname',$vote_number,$vote_times,'$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname); //投票しました通知

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

  //全員が投票していた場合
  if($vote_count != $user_count) return false;

  $check_draw = true; //引き分け判定実行フラグ
  $max_voted_number = 0; //最多得票数
  $handle_list = array(); //ユーザ名とハンドルネームの対応表
  $role_list   = array(); //ユーザ名と役職の対応表
  $live_list   = array(); //生きている人のユーザ名リスト
  $vote_target_list = array(); //投票リスト (ユーザ名 => 投票先ハンドルネーム)
  $vote_count_list  = array(); //得票リスト (ユーザ名 => 投票数)

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

    //投票結果をタブ区切りで出力 (誰が [TAB] 誰に [TAB] 自分の得票数 [TAB] 自分の投票数 [TAB] 投票回数)
    $sentence = $this_handle . "\t" .  $this_vote_target . "\t" .
      $this_voted_number ."\t" . $this_vote_number . "\t" . (int)$vote_times ;

    //投票情報をシステムメッセージに登録
    InsertSystemMessage($sentence, $situation);

    //最大得票数を更新
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //リストにデータを追加
    $handle_list[$this_uname] = $this_handle;
    $role_list[$this_uname]   = $this_role;
    $vote_target_list[$this_uname] = $this_vote_target;
    $vote_count_list[$this_uname]  = $this_voted_number;
    array_push($live_list, $this_uname);
  }

  //最大得票数を集めた人の数を取得
  $voted_member_list = array_count_values($vote_count_list); //得票数 => 人数 の配列を生成
  $max_voted_member = $voted_member_list[$max_voted_number]; //最大得票数を集めた人の数

  //最大得票数のユーザ名のリストを取得
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);

  if($max_voted_member == 1){ //一人だけの場合、処刑して夜にする
    VoteKill($max_voted_uname_list[0], $vote_count_list, $vote_target_list,
	     $handle_list, $role_list, $live_list);
    $check_draw = false;
  }
  else{ //複数いたばあい、決定者が居なければ再投票
    $revote_flag = true; //再投票フラグを初期化
    $target_uname = '';

    foreach($max_voted_uname_list as $max_voted_uname){
      //投票者に決定者がいるか探す
      $sql = mysql_query("SELECT user_entry.role FROM user_entry, vote
				WHERE user_entry.room_no = $room_no
				AND user_entry.role LIKE '%decide&'
				AND vote.room_no = $room_no AND vote.date = $date
				AND vote.situation = '$situation'
				AND vote.vote_times = $vote_times
				AND vote.uname = user_entry.uname
				AND vote.target_uname = '$max_voted_uname'
				AND user_entry.user_no > 0");
      if(mysql_num_rows($sql) > 0){ //決定者がいれば処刑
	$revote_flag = false;
	$target_uname = $max_voted_uname; //処刑対象者をセット
	break;
      }
    }

    if($revote_flag){ //再投票
      //特殊サブ役職の突然死処理
      VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list);

      $next_vote_times = $vote_times + 1; //投票回数を増やす
      mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

      //システムメッセージ
      InsertSystemMessage($vote_times, 'RE_VOTE');
      InsertSystemTalk("再投票になりました( $vote_times 回目)", ++$system_time);
      UpdateTime(); //最終書き込みを更新
    }
    else{ //処刑して夜にする
      VoteKill($target_uname, $vote_count_list, $vote_target_list,
	       $handle_list, $role_list, $live_list);
      $check_draw = false;
    }
  }
  CheckVictory($check_draw);
}

//投票で処刑する
function VoteKill($target_uname, $vote_count_list, $vote_target_list,
		  $handle_list, $role_list, $live_list){
  global $system_time, $room_no, $date;

  //ユーザ情報を取得
  $target_handle = $handle_list[$target_uname];
  $target_role   = $role_list[$target_uname];

  //処刑処理
  KillUser($target_uname); //死亡処理
  InsertSystemMessage($target_handle, 'VOTE_KILLED'); //システムメッセージ
  SaveLastWords($target_handle); //処刑者の遺言

  //処刑された人が埋毒者の場合
  if(strpos($target_role, 'poison') !== false &&
     strpos($target_role, 'poison_guard') === false){ //騎士は対象外
    //他の人からランダムに一人選ぶ
    //恋人後追い処理を先にすると後追いした恋人も含めてしまうので
    //改めて「現在の生存者」を DB に問い合わせるべきじゃないかな？
    $array = array_diff($live_list, array($target_uname));
    $rand_key = array_rand($array, 1);
    $poison_target_uname  = $array[$rand_key];
    $poison_target_handle = $handle_list[$target_uname];
    $poison_target_role   = $role_list[$target_uname];

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
    $necro_max_voted_role = 'boss_wolf';
  elseif(strpos($target_role, 'wolf') !== false)
    $necro_max_voted_role = 'wolf';
  else
    $necro_max_voted_role = 'human';

  InsertSystemMessage($target_handle . "\t" . $necro_max_voted_role, 'NECROMANCER_RESULT');

  //特殊サブ役職の突然死処理
  VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list);

  mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //夜にする
  InsertSystemTalk('NIGHT', ++$system_time, 'night system'); //夜がきた通知
  UpdateTime(); //最終書き込みを更新
  DeleteVote(); //今までの投票を全部削除
  mysql_query('COMMIT'); //一応コミット
}

//投票による特殊サブ役職の突然死処理
function VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list){
  $uname_list = array_flip($handle_list); //ハンドルネーム => ユーザ名
  foreach($vote_count_list as $key => $value){
    $this_role = $role_list[$key];
    if($value > 0){
      if(strpos($this_role, 'chicken') !== false)
	SuddenDeath($key, $handle_list[$key], $this_role, 'CHICKEN');
    }
    else{
      if(strpos($this_role, 'rabbit') !== false)
	SuddenDeath($key, $handle_list[$key], $this_role, 'RABBIT');
    }
    if(strpos($this_role, 'perverseness') !== false){
      $target_value = $vote_count_list[$uname_list[$vote_target_list[$key]]]; //投票対象者の得票
      if($target_value > 1 || (strpos($this_role, 'authority') !== false && $target_value > 2))
	SuddenDeath($key, $handle_list[$key], $this_role, 'PERVERSENESS');
    }
  }
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
  $sql = mysql_query($query_role . "'%mage%'");
  $mage_count = mysql_result($sql, 0, 0);

  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    //初日、身代わり君の役割が占い師の場合占い師の数に入れない
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    if(strpos(mysql_result($sql, 0, 0), 'mage') !== false) $mage_count--;
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

    $sql = mysql_query($query_role . "'%guard%'");
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

  $guard_success_flag = false;
  for($i = 0; $i < $guard_count; $i++ ){ //護衛成功かチェック
    $guard_array  = mysql_fetch_assoc($sql_guard);
    $guard_uname  = $guard_array['target_uname'];
    $guard_handle = $guard_array['handle_name'];

    if($guard_uname == $wolf_target_uname){ //護衛成功
      //護衛成功のメッセージ
      InsertSystemMessage($guard_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');
      $guard_success_flag = true;
    }
  }

  if($guard_success_flag || strpos($game_option, 'quiz') !== false){ //護衛判定は狐判定の前に行う仕様
    //護衛成功 or クイズ村仕様
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox') === false){ //食べる先が狐の場合食べれない
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
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
      elseif(strpos($mage_target_role, 'wolf') !== false)
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
