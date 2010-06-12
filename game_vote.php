<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ICON_CONF');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameVote'); //引数を取得
$DB_CONF->Connect(); //DB 接続
$SESSION->Certify(); //セッション認証

$ROOM =& new Room($RQ_ARGS); //村情報をロード
if($ROOM->IsFinished()) OutputVoteError('ゲーム終了', 'ゲームは終了しました');
$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->BySession(); //自分の情報をロード

//-- メインルーチン --//
if($RQ_ARGS->vote){ //投票処理
  if($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票処理
    switch($RQ_ARGS->situation){
    case 'GAMESTART':
      $INIT_CONF->LoadClass('CAST_CONF'); //配役情報をロード
      VoteGameStart();
      break;

    case 'KICK_DO':
      VoteKick($RQ_ARGS->target_handle_name);
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('ゲーム開始前投票');
      break;
    }
  }
  elseif($SELF->IsDead()){
    VoteDeadUser();
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputVoteError('空投票', '投票先を指定してください');
  }
  elseif($ROOM->IsDay()){ //昼の処刑投票処理
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputVoteError('投票コマンドエラー', '投票先を指定してください');
  }
}
else{ //シーンに合わせた投票ページを出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  if($SELF->IsDead()){
    OutputVoteDeadUser();
  }
  else{
    switch($ROOM->day_night){
    case 'beforegame':
      OutputVoteBeforeGame();
      break;

    case 'day':
      OutputVoteDay();
      break;

    case 'night':
      OutputVoteNight();
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('投票シーンエラー');
      break;
    }
  }
}
$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
//エラーページ出力
function OutputVoteError($title, $sentence = NULL){
  global $RQ_ARGS;

  $header = '<div align="center"><a name="game_top"></a>';
  $footer = "<br>\n" . $RQ_ARGS->back_url . '</div>';
  if(is_null($sentence)) $sentence = 'プログラムエラーです。管理者に問い合わせてください。';
  OutputActionResult('投票エラー [' . $title .']', $header . $sentence . $footer);
}

//テーブルを排他的ロック
function LockVote(){
  if(! LockTable('game')) OutputVoteResult('サーバが混雑しています。<br>再度投票をお願いします。');
}

//ゲーム開始投票の処理
function VoteGameStart(){
  global $GAME_CONF, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->IsDummyBoy(true)){ //出題者以外の身代わり君
    if($GAME_CONF->power_gm){ //強権 GM による強制スタート処理
      LockVote(); //テーブルを排他的ロック
      $sentence = AggregateVoteGameStart(true) ? 'ゲーム開始' :
	'ゲームスタート：開始人数に達していません。';
      OutputVoteResult($sentence, true);
    }
    else{
      OutputVoteResult('ゲームスタート：身代わり君は投票不要です');
    }
  }

  //投票済みチェック
  $ROOM->LoadVote();
  //PrintData($ROOM->vote); //テスト用
  //DeleteVote(); //テスト用
  if(isset($ROOM->vote[$SELF->uname])) OutputVoteResult('ゲームスタート：投票済みです');
  LockVote(); //テーブルを排他的ロック

  if($SELF->Vote('GAMESTART')){ //投票処理
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
  $user_count = FetchResult($ROOM->GetQuery(false, 'user_entry') . ' AND user_no > 0');

  //投票総数を取得
  if($force_start){ //強制開始モード時はスキップ
    $vote_count = $user_count;
  }
  else{
    $vote_count = $ROOM->LoadVote(); //投票情報をロード (ロック前の情報は使わない事)
    if($ROOM->IsDummyBoy(true)) $vote_count++; //身代わり君使用なら身代わり君の分を加算
  }

  //規定人数に足りないか、全員投票していなければ処理終了
  if($vote_count != $user_count || $vote_count < min(array_keys($CAST_CONF->role_list))){
    return false;
  }

  //-- 配役決定ルーチン --//
  $ROOM->LoadOption(); //配役設定オプションの情報を取得
  //PrintData($ROOM->option_role);

  //配役決定用変数をセット
  $uname_list        = $USERS->GetLivingUsers(); //ユーザ名の配列
  $role_list         = GetRoleList($user_count, $ROOM->option_role->row); //役職リストを取得
  $fix_uname_list    = array(); //役割の決定したユーザ名を格納する
  $fix_role_list     = array(); //ユーザ名に対応する役割
  $remain_uname_list = array(); //希望の役割になれなかったユーザ名を一時的に格納

  //フラグセット
  $gerd      = $ROOM->IsOption('gerd');
  $chaos     = $ROOM->IsOptionGroup('chaos'); //chaosfull も含む
  $quiz      = $ROOM->IsQuiz();
  $detective = $ROOM->IsOption('detective');
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
      //探偵村なら身代わり君の対象外役職に追加する
      if($detective && ! in_array('detective_common', $CAST_CONF->disable_dummy_boy_role_list)){
	$CAST_CONF->disable_dummy_boy_role_list[] = 'detective_common';
      }

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
  $delete_role_list = array('lovers', 'copied', 'febris', 'death_warrant', 'panelist',
			    'mind_read', 'mind_evoke', 'mind_receiver', 'mind_friend',
			    'mind_lonely', 'mind_sympathy');

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
  #$add_sub_role = 'perverseness';
  $add_sub_role = 'mind_open';
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
    if($ROOM->IsOption($role) && $user_count >= $CAST_CONF->$role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $role;
    }
  }
  if($ROOM->IsOption('liar')){ //狼少年村
    $role = 'liar';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++){ //全員に一定確率で狼少年をつける
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $role;
    }
  }
  if($ROOM->IsOption('gentleman')){ //紳士・淑女村
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員に性別に応じて紳士か淑女をつける
      $role = $sub_role_list[$USERS->ByUname($fix_uname_list[$i])->sex];
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($ROOM->IsOption('sudden_death')){ //虚弱体質村
    $sub_role_list = array_diff($GAME_CONF->sub_role_group_list['sudden-death'], array('panelist'));
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //全員にショック死系を何かつける
      $role = GetRandom($sub_role_list);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience'){ //短気は一人だけ
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif($ROOM->IsOption('perverseness')){ //天邪鬼村
    $role = 'perverseness';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($chaos && ! $ROOM->IsOption('no_sub_role')){
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
  /*
  if($ROOM->IsOption('festival')){ //お祭り村 (内容は管理人が自由にカスタムする)
    $role = 'nervy';
    for($i = 0; $i < $user_count; $i++){ //全員に自信家をつける
      $fix_role_list[$i] .= ' ' . $role;
    }
  }
  */
  //デバッグ用
  //PrintData($fix_uname_list); PrintData($fix_role_list); DeleteVote(); return false;

  //役割をDBに更新
  $role_count_list = array();
  $detective_list = array();
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $user = $USERS->ByUname($fix_uname_list[$i]);
    $user->ChangeRole($role);
    $role_list = explode(' ', $role);
    foreach($role_list as $role) $role_count_list[$role]++;
    if($detective && in_array('detective_common', $role_list)) $detective_list[] = $user;
  }

  //役割リスト通知
  if($chaos){
    if($ROOM->IsOption('chaos_open_cast_camp')){
      $sentence = GenerateRoleNameList($role_count_list, 'camp');
    }
    elseif($ROOM->IsOption('chaos_open_cast_role')){
      $sentence = GenerateRoleNameList($role_count_list, 'role');
    }
    elseif($ROOM->IsOption('chaos_open_cast')){
      $sentence = GenerateRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = GenerateRoleNameList($role_count_list);
  }

  //ゲーム開始
  $ROOM->date++;
  $ROOM->day_night = 'night';
  $query = "UPDATE room SET date = {$ROOM->date}, day_night = '{$ROOM->day_night}', " .
    "status = 'playing', start_time = NOW() WHERE room_no = {$ROOM->id}";
  SendQuery($query);
  //OutputSiteSummary(); //RSS機能はテスト中
  $ROOM->Talk($sentence);
  if($detective && count($detective_list) > 0){ //探偵村の指名
    $detective_user = GetRandom($detective_list);
    $ROOM->Talk('探偵は ' . $detective_user->handle_name . ' さんです');
    if($ROOM->IsOption('gm_login') && $ROOM->IsOption('not_open_cast') && $user_count > 7){
      $detective_user->ToDead(); //霊界探偵モードなら探偵を霊界に送る
    }
  }
  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //初日の処刑投票のカウントを1に初期化(再投票で増える)
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //真・闇鍋はいきなり終了してる可能性あり
  DeleteVote(); //今までの投票を全部削除
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

  LockVote(); //テーブルを排他的ロック

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

  if($SELF->Vote('KICK_DO', $target_uname)){ //投票処理
    $ROOM->Talk("KICK_DO\t" . $target, $SELF->uname); //投票しました通知
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
  $sql = mysql_query($ROOM->GetQuery(false, 'user_entry') . ' AND user_no > 0');
  $user_count = mysql_result($sql, 0, 0);

  //Kick する人の user_no を取得
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick された人は死亡、user_no を -1、セッション ID を削除する
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = {$ROOM->id} AND handle_name = '$target' AND user_no > 0");

  //キックされて空いた場所を詰める
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = {$ROOM->id} AND user_no = $next");
  }

  $ROOM->Talk($target . $MESSAGE->kick_out); //出て行ったメッセージ
  $ROOM->Talk($MESSAGE->vote_reset); //投票リセット通知
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  DeleteVote(); //今までの投票を全部削除
  return $vote_count;
}

//昼の投票処理
function VoteDay(){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //コマンドチェック

  //投票済みチェック
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //仮想投票者を取得
  $target = $USERS->ByReal($RQ_ARGS->target_no); //投票先のユーザ情報を取得
  if($target->uname == '') OutputVoteResult('処刑：投票先が指定されていません');
  if($target->IsSelf())    OutputVoteResult('処刑：自分には投票できません');
  if(! $target->IsLive())  OutputVoteResult('処刑：生存者以外には投票できません');

  $vote_duel = $ROOM->event->vote_duel; //特殊イベントを取得
  if(is_array($vote_duel) && ! in_array($RQ_ARGS->target_no, $vote_duel)){
    OutputVoteResult('処刑：決選投票対象者以外には投票できません');
  }
  LockVote(); //テーブルを排他的ロック

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
    $vote_number += mt_rand(0, 2) - 1;
  }

  if(! $SELF->Vote('VOTE_KILL', $target->uname, $vote_number)){ //投票処理
    OutputVoteResult('データベースエラー', true);
  }

  //システムメッセージ
  $ROOM->Talk("VOTE_DO\t" . $USERS->GetHandleName($target->uname, true), $SELF->uname);

  AggregateVoteDay(); //集計処理
  OutputVoteResult('投票完了', true);
}

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  //-- イベント名と役職の整合チェック --//
  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  switch($RQ_ARGS->situation){
  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage', 'emerald_fox')) OutputVoteResult('夜：占い師系以外は投票できません');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRole('voodoo_killer')) OutputVoteResult('夜：陰陽師以外は投票できません');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('夜：狩人系以外は投票できません');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('夜：ブン屋以外は投票できません');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('夜：厄神以外は投票できません');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRoleGroup('cat', 'revive_fox')){
      OutputVoteResult('夜：猫又系・仙狐以外は投票できません');
    }
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('夜：仙狐の蘇生は一度しかできません');
    }
    $not_type = $RQ_ARGS->situation == 'POISON_CAT_NOT_DO';
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRoleGroup('assassin')) OutputVoteResult('夜：暗殺者以外は投票できません');
    $not_type = $RQ_ARGS->situation == 'ASSASSIN_NOT_DO';
    break;

  case 'MIND_SCANNER_DO':
    if(! $SELF->IsRoleGroup('scanner')) OutputVoteResult('夜：さとり系以外は投票できません');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    break;

  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('夜：人狼系以外は投票できません');
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
    $not_type = $RQ_ARGS->situation == 'TRAP_MAD_NOT_DO';
    break;

  case 'POSSESSED_DO':
  CASE 'POSSESSED_NOT_DO':
    if(! $SELF->IsRole('possessed_mad', 'possessed_fox')){
      OutputVoteResult('夜：犬神・憑狐以外は投票できません');
    }
    if(! $SELF->IsActive()) OutputVoteResult('夜：憑依は一度しかできません');
    $not_type = $RQ_ARGS->situation == 'POSSESSED_NOT_DO';
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('夜：九尾以外は投票できません');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsChildFox()) OutputVoteResult('夜：子狐以外は投票できません');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'angel', 'dummy_chiroptera')){
      OutputVoteResult('夜：キューピッド系以外は投票できません');
    }
    $is_cupid = true;
    break;

  case 'FAIRY_DO':
    if(! $SELF->IsRoleGroup('fairy')) OutputVoteResult('夜：妖精系以外は投票できません');
    $is_mirror_fairy = $SELF->IsRole('mirror_fairy');
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRoleGroup('mania')) OutputVoteResult('夜：神話マニア系以外は投票できません');
    break;

  default:
    OutputVoteResult('夜：あなたは投票できません');
    break;
  }
  CheckAlreadyVote($is_mirror_fairy ? 'CUPID_DO' : $RQ_ARGS->situation); //投票済みチェック

  //-- 投票エラーチェック --//
  $error_header = '夜：投票先が正しくありません<br>'; //エラーメッセージのヘッダ

  if($not_type); //投票キャンセルタイプは何もしない
  elseif($is_cupid || $is_mirror_fairy){ //キューピッド系
    if($SELF->IsRole('triangle_cupid')){
      if(count($RQ_ARGS->target_no) != 3) OutputVoteResult('夜：指定人数が三人ではありません');
    }
    elseif(count($RQ_ARGS->target_no) != 2){
      OutputVoteResult('夜：指定人数が二人ではありません');
    }

    $target_list = array();
    $self_shoot = false; //自分撃ちフラグを初期化
    foreach($RQ_ARGS->target_no as $target_no){
      $target = $USERS->ByID($target_no); //投票先のユーザ情報を取得

      //生存者以外と身代わり君への投票は無効
      if(! $target->IsLive() || $target->IsDummyBoy()){
	OutputVoteResult('生存者以外と身代わり君へは投票できません');
      }

      $target_list[] = $target;
      $self_shoot |= $target->IsSelf(); //自分撃ち判定
    }

    //自分撃ちでは無い場合は特定のケースでエラーを返す
    if($is_cupid && ! $self_shoot){
      if($SELF->IsRole('self_cupid', 'dummy_chiroptera')){ //求愛者
	OutputVoteResult($error_header . '求愛者は必ず自分を対象に含めてください');
      }
      elseif($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot){ //参加人数
	OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
      }
    }
  }
  else{ //キューピッド系以外
    $target  = $USERS->ByID($RQ_ARGS->target_no); //投票先のユーザ情報を取得
    $is_live = $USERS->IsVirtualLive($target->user_no); //仮想的な生死を判定

    if($RQ_ARGS->situation != 'TRAP_MAD_DO' && $target->IsSelf()){ //罠師以外は自分への投票は無効
      OutputVoteResult($error_header . '自分には投票できません');
    }

    if($RQ_ARGS->situation == 'POISON_CAT_DO' || $RQ_ARGS->situation == 'POSSESSED_DO'){
      if($is_live){ //蘇生・憑依能力者は死者以外への投票は無効
	OutputVoteResult($error_header . '死者以外には投票できません');
      }
    }
    elseif(! $is_live){
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

  //-- 投票処理 --//
  LockVote(); //テーブルを排他的ロック
  if($not_type){
    if(! $SELF->Vote($RQ_ARGS->situation)){ //投票処理
      OutputVoteResult('データベースエラー', true);
    }
    $ROOM->SystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation, $SELF->uname);
  }
  else{
    if($is_cupid){ //キューピッド系の処理
      $uname_stack  = array();
      $handle_stack = array();
      $is_self  = $SELF->IsRole('self_cupid');
      $is_mind  = $SELF->IsRole('mind_cupid');
      $is_dummy = $SELF->IsRole('dummy_chiroptera');
      foreach($target_list as $target){
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;

	if($is_dummy){ //夢求愛者の処理
	  if(! $target->IsSelf()){ //自分以外には何もしない
	    $main_role = 'dummy_chiroptera';
	    $change_role = $main_role . '[' . strval($target->user_no) . ']';
	    $SELF->ReplaceRole($main_role, $change_role);
	  }
	  continue;
	}

	//役職に恋人を追加
	$add_role = 'lovers[' . strval($SELF->user_no) . ']';
	if($is_self && ! $target->IsSelf()){ //求愛者なら相手に受信者を追加
	  $add_role .= ' mind_receiver['. strval($SELF->user_no) . ']';
	}
	elseif($is_mind){ //女神なら共鳴者を追加
	  $add_role .= ' mind_friend['. strval($SELF->user_no) . ']';
	  if(! $self_shoot){//他人撃ちなら本人に受信者を追加する
	    $SELF->AddRole('mind_receiver[' . strval($target->user_no) . ']');
	  }
	}
	/*
	//入れ替えQPなら自分と入れ替える
	elseif($SELF->IsRole('possessed_cupid') && ! $target->IsSelf()){
	  $SELF->AddRole('possessed_target[2-' . $target->user_no . '] ' .
			 'possessed[2-' . $target->user_no . ']');
	  $target->AddRole('possessed_target[2-' . $SELF->user_no . '] ' .
				'possessed[2-' . $SELF->user_no . ']');
	}
	*/
	$target->AddRole($add_role);
      }
      if($SELF->IsRoleGroup('angel')){
	$lovers_a = $target_list[0];
	$lovers_b = $target_list[1];
	if(($SELF->IsRole('angel') && $lovers_a->sex != $lovers_b->sex) ||
	   ($SELF->IsRole('rose_angel') && $lovers_a->sex == 'male' && $lovers_b->sex == 'male') ||
	   ($SELF->IsRole('lily_angel') && $lovers_a->sex == 'female' && $lovers_b->sex == 'female')){
	  $lovers_a->AddRole('mind_sympathy');
	  $sentence = $lovers_a->handle_name . "\t" . $lovers_b->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_b->main_role, 'SYMPATHY_RESULT');

	  $lovers_b->AddRole('mind_sympathy');
	  $sentence = $lovers_b->handle_name . "\t" . $lovers_a->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_a->main_role, 'SYMPATHY_RESULT');
	}
      }

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($is_mirror_fairy){ //鏡妖精の処理
      $id_stack     = array();
      $uname_stack  = array();
      $handle_stack = array();
      foreach($target_list as $target){ //情報収集
	$id_stack[]     = strval($target->user_no);
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;
      }
      $main_role = 'mirror_fairy';
      $change_role = $main_role . '[' . implode('-', $id_stack) . ']';
      $SELF->ReplaceRole($main_role, $change_role);

      $situation     = 'CUPID_DO';
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    else{ //通常処理
      $situation     = $RQ_ARGS->situation;
      $target_uname  = $USERS->ByReal($target->user_no)->uname;
      $target_handle = $target->handle_name;
    }

    if(! $SELF->Vote($situation, $target_uname)){ //投票処理
      OutputVoteResult('データベースエラー', true);
    }
    $ROOM->SystemMessage($SELF->handle_name . "\t" . $target_handle, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation . "\t" . $target_handle, $SELF->uname);
  }

  AggregateVoteNight(); //集計処理
  OutputVoteResult('投票完了', true);
}

//死者の投票処理
function VoteDeadUser(){
  global $ROOM, $SELF;

  CheckSituation('REVIVE_REFUSE'); //コマンドチェック

  //投票済みチェック
  if($SELF->IsDrop()) OutputVoteResult('蘇生辞退：投票済み');
  if($ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です');
  LockVote(); //テーブルを排他的ロック

  //-- 投票処理 --//
  if(! $SELF->Update('live', 'drop')) OutputVoteResult('データベースエラー', true);

  //システムメッセージ
  $sentence = 'システム：' . $SELF->handle_name . 'さんは蘇生を辞退しました。';
  $ROOM->Talk($sentence, $SELF->uname, 'heaven', 'normal');

  OutputVoteResult('投票完了', true);
}

//投票ページ HTML ヘッダ出力
function OutputVotePageHeader(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  OutputHTMLHeader($SERVER_CONF->title . ' [投票]', 'game');
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="game_top"></a>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">

EOF;
}

//シーンの一致チェック
function CheckScene(){
  global $ROOM, $SELF;

  if($ROOM->day_night != $SELF->last_load_day_night){
    OutputVoteResult('戻ってリロードしてください');
  }
}

//開始前の投票ページ出力
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //投票する状況があっているかチェック
  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $user_no => $user){
    if($count > 0 && $count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    $icon = $ICON_CONF->path . '/' . $user->icon_filename;
    if(! $user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $user->IsSelf())){
      $radio = '<input type="radio" id="' . $user->handle_name .
	'" name="target_handle_name" value="' . $user->handle_name . '">'."\n";
    }
    else
      $radio = '';

    echo <<<EOF
<td><label for="{$user->handle_name}">
<img src="{$icon}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">◆</font>{$user->handle_name}<br>
{$radio}</label></td>

EOF;
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* Kick するには {$GAME_CONF->kick} 人の投票が必要です</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->kick_do}"></form></td>
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
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
  global $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene();  //投票する状況があっているかチェック
  $vote_times = $ROOM->GetVoteTimes(); //投票回数を取得

  //投票済みチェック
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="{$vote_times}">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //仮想投票者を取得
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $vote_duel = $ROOM->event->vote_duel; //特殊イベントを取得
  $checkbox_header = "\n".'<input type="radio" name="target_no" id="';
  foreach($USERS->rows as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    if(is_array($vote_duel) && ! in_array($id, $vote_duel)) continue;
    $is_live = $USERS->IsVirtualLive($id);

    //生きていればユーザアイコン、死んでれば死亡アイコン
    $path = $is_live ? $ICON_CONF->path . '/' . $user->icon_filename : $ICON_CONF->dead;
    $checkbox = ($is_live && ! $user->IsSame($virtual_self->uname)) ?
      $checkbox_header . $id . '" value="' . $id . '">' : '';

    echo <<<EOF
<td><label for="{$id}">
<img src="{$path}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">◆</font>{$user->handle_name}<br>{$checkbox}
</label></td>

EOF;
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//夜の投票ページを出力する
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //投票シーンチェック

  //投票済みチェック
  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  if($SELF->IsRoleGroup('mage')){
    $type = 'MAGE_DO';
  }
  elseif($SELF->IsRole('voodoo_killer')){
    $type = 'VOODOO_KILLER_DO';
  }
  elseif($SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の護衛はできません');
    $type = 'GUARD_DO';
  }
  elseif($SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の尾行はできません');
    $type = 'REPORTER_DO';
  }
  elseif($SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の厄払いはできません');
    $type = 'ANTI_VOODOO_DO';
  }
  elseif($role_revive = $SELF->IsRoleGroup('cat', 'revive_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
      OutputVoteResult('夜：仙狐の蘇生は一度しかできません');
    }
    $type       = 'POISON_CAT_DO';
    $not_type   = 'POISON_CAT_NOT_DO';
    $submit     = 'revive_do';
    $not_submit = 'revive_not_do';
  }
  elseif($SELF->IsRoleGroup('assassin')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の暗殺はできません');
    $type     = 'ASSASSIN_DO';
    $not_type = 'ASSASSIN_NOT_DO';
  }
  elseif($SELF->IsRoleGroup('scanner')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    $type = 'MIND_SCANNER_DO';
  }
  elseif($role_wolf = $SELF->IsWolf()){
    $type = 'WOLF_EAT';
  }
  elseif($SELF->IsRole('jammer_mad')){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'jammer_do';
  }
  elseif($SELF->IsRole('voodoo_mad')){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('dream_eater_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の襲撃はできません');
    $type = 'DREAM_EAT';
  }
  elseif($role_trap = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の罠設置はできません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：罠は一度しか設置できません');
    $type       = 'TRAP_MAD_DO';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $submit     = 'trap_do';
    $not_submit = 'trap_not_do';
  }
  elseif($SELF->IsRole('possessed_mad', 'possessed_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の憑依はできません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：憑依は一度しかできません');
    $type       = 'POSSESSED_DO';
    $not_type   = 'POSSESSED_NOT_DO';
    $role_revive = true;
  }
  elseif($SELF->IsRole('voodoo_fox')){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('emerald_fox')){
    if(! $SELF->IsActive()) OutputVoteResult('夜：占いは一度しかできません');
    $type   = 'MAGE_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsChildFox()){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera', 'mirror_fairy')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $type = 'CUPID_DO';
    $role_cupid = $SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera');
    $role_mirror_fairy = $SELF->IsRole('mirror_fairy');
    $cupid_self_shoot  = $SELF->IsRole('self_cupid', 'dummy_chiroptera') ||
      $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot;
  }
  elseif($SELF->IsRoleGroup('fairy')){
    $type = 'FAIRY_DO';
  }
  elseif($SELF->IsRoleGroup('mania')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $type = 'MANIA_DO';
  }
  else{
    OutputVoteResult('夜：あなたは投票できません');
  }
  CheckAlreadyVote($type, $not_type);
  if($role_mirror_fairy) $type = 'FAIRY_DO'; //鏡妖精は表示だけ妖精系 (内部処理はキューピッド系)

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && (($ROOM->IsDummyBoy() && $ROOM->date == 1) || $ROOM->IsQuiz())){
    //身代わり君のユーザ情報
    $user_stack = array(1 => $USERS->rows[1]); //dummy_boy = 1番は保証されている？
  }
  else{
    $user_stack = $USERS->rows;
  }

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";
  foreach($user_stack as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    $is_live = $USERS->IsVirtualLive($id);
    $is_wolf = $role_wolf && ! $SELF->IsRole('silver_wolf') &&
      $USERS->ByReal($id)->IsWolf(true);

    /*
      死んでいれば死亡アイコン (蘇生能力者は死亡アイコンにしない)
      狼同士なら狼アイコン、生きていればユーザアイコン
    */
    $path = ! ($is_live || $role_revive) ? $ICON_CONF->dead :
      ($is_wolf ? $ICON_CONF->wolf : $ICON_CONF->path . '/' . $user->icon_filename);

    $checkbox = '';
    $checkbox_header = '<input type="radio" name="target_no"';
    $checkbox_footer = ' id="' . $id . '"value="' . $id . '">'."\n";
    if($role_cupid || $role_mirror_fairy){
      if(! $user->IsDummyBoy() && $is_live){
	$checked = ($role_cupid && $cupid_self_shoot && $user->IsSelf()) ? ' checked' : '';
	$checkbox = '<input type="checkbox" name="target_no[]"' . $checked . $checkbox_footer;
      }
    }
    elseif($role_revive){
      if(! $is_live && ! $user->IsSelf() && ! $user->IsDummyBoy()){
	$checkbox = $checkbox_header . $checkbox_footer;
      }
    }
    elseif($role_trap){
      if($is_live) $checkbox = $checkbox_header . $checkbox_footer;
    }
    elseif($is_live && ! $user->IsSelf() && ! $is_wolf){
      $checkbox = $checkbox_header . $checkbox_footer;
    }

    echo <<<EOF
<td><label for="{$id}">
<img src="{$path}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">◆</font>{$user->handle_name}<br>{$checkbox}
</label></td>

EOF;
  }

  if(empty($submit)) $submit = strtolower($type);
  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$VOTE_MESS->$submit}"></td></form>

EOF;

  if($not_type != ''){
    if(empty($not_submit)) $not_submit = strtolower($not_type);
    echo <<<EOF
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
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

//死者の投票ページ出力
function OutputVoteDeadUser(){
  global $VOTE_MESS, $RQ_ARGS, $ROOM, $SELF;

  //投票済みチェック
  if($SELF->IsDummyBoy()) OutputVoteResult('蘇生辞退：身代わり君の投票は無効です');
  if($SELF->IsDrop()) OutputVoteResult('蘇生辞退：投票済み');
  if($ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="REVIVE_REFUSE">
<span class="vote-message">* 投票の取り消しはできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->revive_refuse}"></form></td>
</tr></table></div>
</body></html>

EOF;
}

//投票済みチェック
function CheckAlreadyVote($situation, $not_situation = ''){
  if(CheckSelfVoteNight($situation, $not_situation)) OutputVoteResult('夜：投票済み');
}
