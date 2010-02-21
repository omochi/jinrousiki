<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ICON_CONF');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameVote'); //引数を取得

//PHP の引数を作成
$php_argv = 'room_no=' . $RQ_ARGS->room_no;
if($RQ_ARGS->auto_reload > 0) $php_argv .= '&auto_reload=' . $RQ_ARGS->auto_reload;
if($RQ_ARGS->play_sound) $php_argv .= '&play_sound=on';
if($RQ_ARGS->list_down)  $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">←戻る &amp; reload</a>';

$DB_CONF->Connect(); //DB 接続
$SESSION->Certify(); //セッション認証

$ROOM =& new Room($RQ_ARGS); //村情報をロード
$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->BySession(); //自分の情報をロード

if($ROOM->IsFinished()){ //ゲーム終了
  OutputActionResult('投票エラー',
		     '<div align="center"><a name="#game_top"></a>' .
		     'ゲームは終了しました<br>'."\n" . $back_url . '</div>');
}

if(! $SELF->IsLive()){ //生存者以外は無効
  OutputActionResult('投票エラー',
		     '<div align="center"><a name="#game_top"></a>' .
		     '生存者以外は投票できません<br>'."\n" . $back_url . '</div>');
}

//-- メインルーチン --//
if($RQ_ARGS->vote){ //投票処理
  if($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票処理
    if($RQ_ARGS->situation == 'GAMESTART'){
      $INIT_CONF->LoadClass('CAST_CONF'); //配役情報をロード
      VoteGameStart();
    }
    elseif($RQ_ARGS->situation == 'KICK_DO'){
      VoteKick($RQ_ARGS->target_handle_name);
    }
    else{ //ここに来たらロジックエラー
      OutputActionResult('投票エラー[ゲーム開始前投票]',
			 '<div align="center"><a name="#game_top"></a>' .
			 'プログラムエラーです。管理者に問い合わせてください<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputActionResult('投票エラー',
		       '<div align="center"><a name="#game_top"></a>' .
		       '投票先を指定してください<br>'."\n" . $back_url . '</div>');
  }
  elseif($ROOM->IsDay()){ //昼の処刑投票処理
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputActionResult('投票エラー',
		       '<div align="center"><a name="#game_top"></a>' .
		       'プログラムエラーです。管理者に問い合わせてください<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票ページ出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteBeforeGame();
}
elseif($ROOM->IsDay()){ //昼の処刑投票ページ出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteDay();
}
elseif($ROOM->IsNight()){ //夜の投票ページ出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteNight();
}
else{ //投票済み //ここに来たらロジックエラーじゃないかな？
  OutputActionResult('投票エラー',
		     '<div align="center"><a name="#game_top"></a>' .
		     '既に投票されております<br>'."\n" . $back_url . '</div>');
}

$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
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
  global $GAME_CONF, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->IsDummyBoy() && ! $ROOM->IsQuiz()){
    if($GAME_CONF->power_gm){ //強権 GM による強制スタート処理
      LockTable(); //テーブルを排他的ロック
      if(AggregateVoteGameStart(true)){
	OutputVoteResult('ゲーム開始', true);
      }
      else{
	OutputVoteResult('ゲームスタート：開始人数に達していません。', true);
      }
    }
    else{
      OutputVoteResult('ゲームスタート：身代わり君は投票不要です');
    }
  }

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = 0 " .
    "AND situation = 'GAMESTART' AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('ゲームスタート：投票済みです');
  LockTable(); //テーブルを排他的ロック

  //投票処理
  $items = 'room_no, date, uname, situation';
  $values = "{$ROOM->id}, 0, '{$SELF->uname}', 'GAMESTART'";
  if(InsertDatabase('vote', $items, $values) && mysql_query('COMMIT')){//一応コミット
    AggregateVoteGameStart(); //集計処理
    OutputVoteResult('投票完了', true);
  }
  else{
    OutputVoteResult('データベースエラー', true);
  }
}

//ゲーム開始投票集計処理
function AggregateVoteGameStart($force_start = false){
  global $GAME_CONF, $CAST_CONF, $MESSAGE, $ROOM, $USERS;

  CheckSituation('GAMESTART');

  //ユーザ総数を取得
  $user_count = $USERS->GetUserCount();

  //投票総数を取得
  if($force_start){ //強制開始モード時はスキップ
    $vote_count = $user_count;
  }
  else{
    $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
      "AND date = 0 AND situation = 'GAMESTART'";
    $vote_count = FetchResult($query);

    //身代わり君使用なら身代わり君の分を加算
    if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;
  }

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count < min(array_keys($CAST_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- 配役決定ルーチン --//
  //配役設定オプションの情報を取得
  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = {$ROOM->id}");

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
	foreach($CAST_CONF->disable_dummy_boy_role_list as $this_disable_role){
	  if(strpos($this_role, $this_disable_role) !== false){
	    array_push($role_list, $this_role); //配役リストの末尾に戻す
	    continue 2;
	  }
	}
	array_push($fix_role_list, $this_role);
	break;
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
  if($ROOM->IsOption('wish_role')){ //役割希望制の場合
    foreach($uname_list as $this_uname){
      do{
	$this_role = $USERS->GetRole($this_uname); //希望役職を取得
	if($this_role  == '' || mt_rand(1, 100) > $CAST_CONF->wish_role_rate) break;
	$this_fit_role = $this_role;

	if($chaos){ //闇鍋モード
	  $this_fit_role_list = array();
	  foreach($role_list as $this_fit_role){
	    if($this_role == DistinguishRoleGroup($this_fit_role)){
	      $this_fit_role_list[] = $this_fit_role;
	    }
	  }
	  $this_fit_role = GetRandom($this_fit_role_list);
	}
	$role_key = array_search($this_fit_role, $role_list); //希望役職の存在チェック
	if($role_key === false) break;

	//希望役職があれば決定
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list, $this_fit_role);
	unset($role_list[$role_key]);
	continue 2;
      }while(false);

      //決まらなかった場合は未決定リスト行き
      array_push($remain_uname_list, $this_uname);
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
  //割り振り対象外役職のリスト
  $delete_role_list = array('lovers', 'copied', 'panelist', 'mind_read', 'mind_evoke',
			    'mind_receiver', 'mind_friend');

  //サブ役職テスト用
  /*
  $test_role_list = array('mind_open');
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
  foreach($now_sub_role_list as $role){
    if(strpos($option_role, $role) !== false && $user_count >= $CAST_CONF->$role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $role;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //狼少年村
    $role = 'liar';
    array_push($delete_role_list, $role);
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $role;
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //紳士・淑女村
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員に性別に応じて紳士か淑女をつける
      $role = $sub_role_list[$USERS->ByUname($fix_uname_list[$i])->sex];
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if(strpos($option_role, 'sudden_death') !== false){ //虚弱体質村
    $sub_role_list = $GAME_CONF->sub_role_group_list['sudden-death'];
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員にショック死系を何かつける
      $role = GetRandom($sub_role_list);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience'){ //短気は一人だけ
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif(strpos($option_role, 'perverseness') !== false){ //天邪鬼村
    $role = 'perverseness';
    array_push($delete_role_list, $role);
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $role;
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
      if($rand_keys_index > $user_count - 1) break; //$rand_keys_index は 0 から
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //声変化形をスキップ
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
    }
  }
  if($quiz){ //クイズ村
    $role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //出題者以外に解答者をつける
      if($fix_uname_list[$i] != 'dummy_boy') $fix_role_list[$i] .= ' ' . $role;
    }
  }

  //デバッグ用
  /*
  PrintData($option_role);
  PrintData($fix_uname_list);
  PrintData($fix_role_list);
  DeleteVote(); return false;
  */

  //ゲーム開始
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night',
		start_time = NOW() WHERE room_no = {$ROOM->id}");
  DeleteVote(); //今までの投票を全部削除

  //役割をDBに更新
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $USERS->ByUname($fix_uname_list[$i])->ChangeRole($role);
    $role_list = explode(' ', $role);
    foreach($role_list as $role) $role_count_list[$role]++;
  }

  //役割リスト通知
  if($chaos){
    if(strpos($option_role, 'chaos_open_cast_camp') !== false){
      $sentence = GenerateRoleNameList($role_count_list, 'camp');
    }
    elseif(strpos($option_role, 'chaos_open_cast_role') !== false){
      $sentence = GenerateRoleNameList($role_count_list, 'role');
    }
    elseif(strpos($option_role, 'chaos_open_cast') !== false){
      $sentence = GenerateRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = GenerateRoleNameList($role_count_list);
  }
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system', 1);

  InsertSystemMessage('1', 'VOTE_TIMES', 1); //初日の処刑投票のカウントを1に初期化(再投票で増える)
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //真・闇鍋はいきなり終了してる可能性あり
  mysql_query('COMMIT'); //一応コミット
  return true;
}

//開始前の Kick 投票の処理 ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $ROOM, $SELF;

  //エラーチェック
  CheckSituation('KICK_DO');
  if($target == '') OutputVoteResult('Kick：投票先を指定してください');
  if($target == '身代わり君') OutputVoteResult('Kick：身代わり君には投票できません');
  if(($ROOM->IsQuiz() || $ROOM->IsOption('gm_login')) && $target == 'GM'){
    OutputVoteResult('Kick：GM には投票できません'); //仮想 GM 対応
  }

  //投票済みチェック
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = {$ROOM->id}
			AND user_entry.handle_name = '$target' AND vote.room_no = {$ROOM->id}
			AND vote.uname = '{$SELF->uname}' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：' . $target . ' へ Kick 投票済み');

  if(! $GAME_CONF->self_kick){ //自分への KICK
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id}
			AND uname = '{$SELF->uname}' AND handle_name ='$target' AND user_no > 0");
    if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick：自分には投票できません');
  }

  LockTable(); //テーブルを排他的ロック

  //ゲーム開始チェック
  if(FetchResult("SELECT day_night FROM room WHERE room_no = {$ROOM->id}") != 'beforegame'){
    OutputVoteResult('Kick：既にゲームは開始されています', true);
  }

  //ターゲットのユーザ名を取得
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick：'. $target . ' はすでに Kick されています', true);

  //投票処理
  $items = 'room_no, date, uname, target_uname, situation';
  $values = "{$ROOM->id}, 0, '{$SELF->uname}', '$target_uname', 'KICK_DO'";
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
  global $GAME_CONF, $MESSAGE, $ROOM, $SELF;

  CheckSituation('KICK_DO');

  //今回投票した相手へ何人投票しているか
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = {$ROOM->id}
			AND vote.room_no = {$ROOM->id} AND vote.date = 0
			AND vote.situation = 'KICK_DO' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //投票総数を取得

  //規定数以上の投票があった / キッカーが身代わり君 / 自己 KICK が有効の場合に処理
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy() &&
     ! ($GAME_CONF->self_kick && $target == $SELF->handle_name)){
    return $vote_count;
  }

  //ユーザ総数を取得
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //Kick する人の user_no を取得
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick された人は死亡、user_no を -1、セッション ID を削除する
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = {$ROOM->id} AND handle_name = '$target' AND user_no > 0");

  // //満員の場合、募集中に戻す //現在は満員時に表示を変えないのでこの処理は不要じゃないかな？
  // mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = {$ROOM->id}");

  //キックされて空いた場所を詰める
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = {$ROOM->id} AND user_no = $next");
  }

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$ROOM->system_time); //出て行ったメッセージ
  InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //投票リセット通知
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  DeleteVote(); //今までの投票を全部削除
  mysql_query('COMMIT'); //一応コミット
  return $vote_count;
}

//昼の投票処理
function VoteDay(){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //コマンドチェック

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //仮想投票者を取得
  $target = $USERS->ByReal($RQ_ARGS->target_no); //投票先のユーザ情報を取得
  if($target->uname == '') OutputVoteResult('処刑：投票先が指定されていません');
  if($target->IsSelf())    OutputVoteResult('処刑：自分には投票できません');
  if(! $target->IsLive())  OutputVoteResult('処刑：生存者以外には投票できません');

  LockTable(); //テーブルを排他的ロック

  //-- 投票処理 --//
  //役職に応じて投票数を補正
  $vote_number = 1;
  if($SELF->IsRoleGroup('elder')) $vote_number++; //長老系 (メイン役職)
  if($virtual_self->IsRole('authority')){ //権力者
    $vote_number++;
  }
  elseif($virtual_self->IsRole('watcher', 'panelist')){ //傍観者・解答者
    $vote_number = 0;
  }
  elseif($virtual_self->IsRole('random_voter')){ //気分屋
    $vote_number = mt_rand(0, 2);
  }

  //投票＆システムメッセージ
  $items = 'room_no, date, uname, target_uname, vote_number, vote_times, situation';
  $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', '{$target->uname}', {$vote_number}, " .
    "{$RQ_ARGS->vote_times}, 'VOTE_KILL'";
  $sql = InsertDatabase('vote', $items, $values);
  $sentence = "VOTE_DO\t" . $USERS->GetHandleName($target->uname, true);
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
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  switch($RQ_ARGS->situation){
  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('夜：人狼以外は投票できません');
    break;

  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage')) OutputVoteResult('夜：占い師以外は投票できません');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRole('voodoo_killer')) OutputVoteResult('夜：陰陽師以外は投票できません');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRole('jammer_mad')) OutputVoteResult('夜：月兎以外は投票できません');
    break;

  case 'VOODOO_MAD_DO':
    if(! $SELF->IsRole('voodoo_mad')) OutputVoteResult('夜：呪術師以外は投票できません');
    break;

  case 'DREAM_EAT':
    if(! $SELF->IsRole('dream_eater_mad')) OutputVoteResult('夜：獏以外は投票できません');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->IsRole('trap_mad')) OutputVoteResult('夜：罠師以外は投票できません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：罠は一度しか設置できません');
    $not_type = ($RQ_ARGS->situation == 'TRAP_MAD_NOT_DO');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('夜：狩人以外は投票できません');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('夜：ブン屋以外は投票できません');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('夜：厄神以外は投票できません');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRoleGroup('cat', 'revive_fox')) OutputVoteResult('夜：猫又・仙狐以外は投票できません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('夜：仙狐の蘇生は一度しかできません');
    }
    $not_type = ($RQ_ARGS->situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRole('assassin')) OutputVoteResult('夜：暗殺者以外は投票できません');
    $not_type = ($RQ_ARGS->situation == 'ASSASSIN_NOT_DO');
    break;

  case 'MIND_SCANNER_DO':
    if(! $SELF->IsRoleGroup('scanner')) OutputVoteResult('夜：さとり以外は投票できません');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('夜：九尾以外は投票できません');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsRole('child_fox')) OutputVoteResult('夜：子狐以外は投票できません');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'dummy_chiroptera')){
      OutputVoteResult('夜：キューピッド以外は投票できません');
    }
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRoleGroup('mania')) OutputVoteResult('夜：神話マニア以外は投票できません');
    break;

  default:
    OutputVoteResult('夜：あなたは投票できません');
    break;
  }
  CheckAlreadyVote($RQ_ARGS->situation); //投票済みチェック

 //エラーメッセージのヘッダ
  $error_header = '夜：投票先が正しくありません<br>';

  if($not_type); //投票キャンセルタイプは何もしない
  elseif($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera')){  //キューピッド系
    if(count($RQ_ARGS->target_no) != 2) OutputVoteResult('夜：指定人数が二人ではありません');
    $target_list = array();
    $self_shoot = false; //自分撃ちフラグを初期化
    foreach($RQ_ARGS->target_no as $this_target_no){
      $this_target = $USERS->ByID($this_target_no); //投票先のユーザ情報を取得

      //生存者以外と身代わり君への投票は無効
      if(! $this_target->IsLive() || $this_target->IsDummyBoy()){
	OutputVoteResult('生存者以外と身代わり君へは投票できません');
      }

      $target_list[] = $this_target;
      $self_shoot |= $this_target->IsSelf(); //自分撃ち判定
    }

    if(! $self_shoot){ //自分撃ちでは無い場合は特定のケースでエラーを返す
      if($SELF->IsRole('self_cupid', 'dummy_chiroptera')){ //求愛者
	OutputVoteResult($error_header . '求愛者は必ず自分を対象に含めてください');
      }
      elseif($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot){ //参加人数
	OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
      }
    }
  }
  else{ //キューピッド系以外
    $target = $USERS->ByID($RQ_ARGS->target_no); //投票先のユーザ情報を取得
    $virtual_live = $USERS->IsVirtualLive($target->user_no); //仮想的な生死を判定

    if($target->IsSelf() && ! $SELF->IsRole('trap_mad')){ //罠師以外は自分への投票は無効
      OutputVoteResult($error_header . '自分には投票できません');
    }

    if($SELF->IsRoleGroup('cat', 'revive_fox')){ //蘇生能力者は死者以外への投票は無効
      if($virtual_live){
	OutputVoteResult($error_header . '死者以外には投票できません');
      }
    }
    elseif(! $virtual_live){
      OutputVoteResult($error_header . '生存者以外には投票できません');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){ //人狼の投票
      //仲間だと分かっている狼同士への投票は無効
      if($SELF->IsWolf(true) && $USERS->ByReal($target->user_no)->IsWolf(true)){
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
    $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
    InsertSystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    InsertSystemTalk($RQ_ARGS->situation, $ROOM->system_time, 'night system', '', $SELF->uname);
  }
  else{
    if($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera')){ //キューピッド系の処理
      $target_uname_str  = '';
      $target_handle_str = '';
      foreach($target_list as $this_target){
	if($target_uname_str != ''){
	  $target_uname_str  .= ' ';
	  $target_handle_str .= ' ';
	}
	$target_uname_str  .= $this_target->uname;
	$target_handle_str .= $this_target->handle_name;

	if($SELF->IsRole('dummy_chiroptera')){ //夢求愛者の処理
	  if(! $this_target->IsSelf()){ //自分以外には何もしない
	    $main_role = 'dummy_chiroptera';
	    $change_role = $main_role . '[' . strval($this_target->user_no) . ']';
	    $SELF->ReplaceRole($main_role, $change_role);
	  }
	  continue;
	}

	//役職に恋人を追加
	$add_role = 'lovers[' . strval($SELF->user_no) . ']';
	if($SELF->IsRole('self_cupid') && ! $this_target->IsSelf()){ //求愛者なら相手に受信者を追加
	  $add_role .= ' mind_receiver['. strval($SELF->user_no) . ']';
	}
	elseif($SELF->IsRole('mind_cupid')){ //女神なら共鳴者を追加
	  $add_role .= ' mind_friend['. strval($SELF->user_no) . ']';
	  if(! $self_shoot){//他人撃ちなら本人に受信者を追加する
	    $SELF->AddRole('mind_receiver[' . strval($this_target->user_no) . ']');
	  }
	}
	/*
	//入れ替えQPなら自分と入れ替える
	elseif($SELF->IsRole('possessed_cupid') && ! $this_target->IsSelf()){
	  $SELF->AddRole('possessed_target[2-' . $this_target->user_no . '] ' .
			 'possessed[2-' . $this_target->user_no . ']');
	  $this_target->AddRole('possessed_target[2-' . $SELF->user_no . '] ' .
				'possessed[2-' . $SELF->user_no . ']');
	}
	*/
	$this_target->AddRole($add_role);
      }
    }
    else{ // キューピッド以外の処理
      $target_uname_str  = $USERS->ByReal($target->user_no)->uname;
      $target_handle_str = $target->handle_name;
    }
    //投票処理
    $items = 'room_no, date, uname, target_uname, situation';
    $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', '$target_uname_str', '{$RQ_ARGS->situation}'";
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
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

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

    if(! $this_user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $this_user->IsSelf())){
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
<td><input type="submit" value="{$VOTE_MESS->kick_do}"></form></td>
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$VOTE_MESS->game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//昼の投票ページを出力する
function OutputVoteDay(){
  global $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

  //投票する状況があっているかチェック
  CheckDayNight();

  //投票回数を取得
  $vote_times = GetVoteTimes();

  //投票済みチェック
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //仮想投票者を取得
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_color  = $this_user->color;
    $this_live   = $USERS->IsVirtualLive($this_user_no);
    if($this_live) //生きていればユーザアイコン
      $path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    else //死んでれば死亡アイコン
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">◆</font>$this_handle<br>

EOF;

    if($this_live && ! $this_user->IsSame($virtual_self->uname)){
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
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//夜の投票ページを出力する
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

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
  elseif($role_dream_eater_mad = $SELF->IsRole('dream_eater_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の襲撃はできません');
    CheckAlreadyVote('DREAM_EAT');
  }
  elseif($role_trap_mad = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の罠設置はできません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：罠は一度しか設置できません');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_guard = $SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の護衛はできません');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = $SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の尾行はできません');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_anti_voodoo = $SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の厄払いはできません');
    CheckAlreadyVote('ANTI_VOODOO_DO');
  }
  elseif($role_poison_cat = $SELF->IsRoleGroup('cat', 'revive_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('夜：仙狐の蘇生は一度しかできません');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
  }
  elseif($role_assassin = $SELF->IsRole('assassin')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の暗殺はできません');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_mind_scanner = $SELF->IsRoleGroup('scanner')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    CheckAlreadyVote('MIND_SCANNER_DO');
  }
  elseif($role_voodoo_fox = $SELF->IsRole('voodoo_fox')){
    CheckAlreadyVote('VOODOO_FOX_DO');
  }
  elseif($role_child_fox = $SELF->IsRole('child_fox')){
    CheckAlreadyVote('CHILD_FOX_DO');
  }
  elseif($role_cupid = ($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera'))){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('CUPID_DO');
    $cupid_self_shoot = ($SELF->IsRole('self_cupid', 'dummy_chiroptera') ||
			 $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot);
  }
  elseif($role_mania = $SELF->IsRoleGroup('mania')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    CheckAlreadyVote('MANIA_DO');
  }
  else OutputVoteResult('夜：あなたは投票できません');

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && (($ROOM->IsDummyBoy() && $ROOM->date == 1) || $ROOM->IsQuiz())){
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
    $this_live = $USERS->IsVirtualLive($this_user_no);
    $this_wolf = ($role_wolf && ! $SELF->IsRole('silver_wolf') &&
		  $USERS->ByReal($this_user_no)->IsWolf(true));

    if($this_live || $role_poison_cat){ //猫又は死亡アイコンにしない
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
      if(! $this_live && ! $this_user->IsSelf() && ! $this_user->IsDummyBoy()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($role_trap_mad){
      if($this_live){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live && ! $this_user->IsSelf() && ! $this_wolf){
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
    $submit = 'wolf_eat';
  }
  elseif($role_mage){
    $type   = 'MAGE_DO';
    $submit = 'mage_do';
  }
  elseif($role_voodoo_killer){
    $type   = 'VOODOO_KILLER_DO';
    $submit = 'voodoo_killer_do';
  }
  elseif($role_jammer_mad){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'jammer_do';
  }
  elseif($role_voodoo_mad){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'voodoo_do';
  }
  elseif($role_dream_eater_mad){
    $type   = 'DREAM_EAT';
    $submit = 'dream_eat';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'trap_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'trap_not_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'reporter_do';
  }
  elseif($role_anti_voodoo){
    $type   = 'ANTI_VOODOO_DO';
    $submit = 'anti_voodoo_do';
  }
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'revive_do';
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'revive_not_do';
  }
  elseif($role_assassin){
    $type   = 'ASSASSIN_DO';
    $submit = 'assassin_do';
    $not_type   = 'ASSASSIN_NOT_DO';
    $not_submit = 'assassin_not_do';
  }
  elseif($role_mind_scanner){
    $type   = 'MIND_SCANNER_DO';
    $submit = 'mind_scanner_do';
  }
  elseif($role_voodoo_fox){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'voodoo_do';
  }
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'cupid_do';
  }
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'mania_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$VOTE_MESS->$submit}"></td></form>

EOF;

  if($not_type != ''){
    echo <<<EOF
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
<input type="submit" value="{$VOTE_MESS->$not_submit}"></form>
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
