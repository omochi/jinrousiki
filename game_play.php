<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//セッション開始
session_start();
$session_id = session_id();

EncodePostData(); //ポストされた文字列を全てエンコードする

//引数を取得
$RQ_ARGS = new RequestGamePlay();
$room_no = $RQ_ARGS->room_no; //部屋 No
if($RQ_ARGS->play_sound){//音でお知らせ
  $SOUND = new Sound(); //音源情報をロード
  $cookie_day_night  = $_COOKIE['day_night'];       //夜明けを音でしらせるため
  $cookie_vote_times = (int)$_COOKIE['vote_times']; //再投票を音で知らせるため
  $cookie_objection  = $_COOKIE['objection'];       //「異議あり」を音で知らせるため
}

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

$ROOM = new RoomDataSet($room_no); //村情報をロード
$ROOM->view_mode    = $RQ_ARGS->view_mode; //観戦モード
$ROOM->dead_mode    = $RQ_ARGS->dead_mode; //死亡者モード
$ROOM->heaven_mode  = $RQ_ARGS->heaven_mode; //霊話モード
$ROOM->system_time  = TZTime(); //現在時刻を取得
$ROOM->sudden_death = 0; //突然死実行までの残り時間

$USERS = new UserDataSet($room_no); //ユーザ情報をロード
$SELF = $USERS->ByUname($uname); //自分の情報をロード
$ROLE_IMG = new RoleImage();

//必要なクッキーをセットする
$objection_array = array(); //SendCookie();で格納される・異議ありの情報
$objection_left_count = 0;  //SendCookie();で格納される・異議ありの残り回数
SendCookie();

//発言の有無をチェック
ConvertSay(&$RQ_ARGS->say); //発言置換処理

if($RQ_ARGS->say != '' && $RQ_ARGS->is_last_words() && $SELF->is_live() && ! $SELF->is_dummy_boy()){
  EntryLastWords($RQ_ARGS->say);  //生きていれば遺言登録
}
elseif($RQ_ARGS->say != '' && ($ROOM->day_night == $SELF->last_load_day_night ||
			       $SELF->is_dead() || $SELF->is_dummy_boy())){
  Say($RQ_ARGS->say); //死んでいるか、最後にリロードした時とシーンが一致しているか身代わり君なら書き込む
}
else{
  CheckSilence(); //ゲーム停滞のチェック(沈黙、突然死)
}

//最後にリロードした時のシーンを更新
mysql_query("UPDATE user_entry SET last_load_day_night = '{$ROOM->day_night}'
		WHERE room_no = $room_no AND uname = '{$SELF->uname}' AND user_no > 0");
mysql_query('COMMIT');

OutputGamePageHeader(); //HTMLヘッダ
OutputGameHeader(); //部屋のタイトルなど

if(! $ROOM->heaven_mode){
  if(! $RQ_ARGS->list_down) OutputPlayerList(); //プレイヤーリスト
  OutputAbility(); //自分の役割の説明
  if($ROOM->is_day() && $SELF->is_live()) CheckSelfVoteDay(); //昼の投票済みチェック
  OutputRevoteList(); //再投票の時、メッセージを表示する
}

//会話ログを出力
if($SELF->is_dead() && $ROOM->heaven_mode)
  OutputHeavenTalkLog();
else
  OutputTalkLog();

if(! $ROOM->heaven_mode){
  if($SELF->is_dead()) OutputAbilityAction(); //能力発揮
  OutputLastWords(); //遺言
  OutputDeadMan();   //死亡者
  OutputVoteList();  //投票結果
  if(! $ROOM->dead_mode) OutputSelfLastWords(); //自分の遺言
  if($RQ_ARGS->list_down) OutputPlayerList(); //プレイヤーリスト
}
OutputHTMLFooter();

DisconnectDatabase($dbHandle); //DB 接続解除

//-- 関数 --//
//必要なクッキーをまとめて登録(ついでに最新の異議ありの状態を取得して配列に格納)
function SendCookie(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $SELF, $objection_array, $objection_left_count;

  //<夜明けを音でお知らせ用>
  //クッキーに格納 (夜明けに音でお知らせで使う・有効期限一時間)
  setcookie('day_night', $ROOM->day_night, $ROOM->system_time + 3600);

  //-- 「異議」ありを音でお知らせ用 --//
  //今までに自分が「異議」ありをした回数を取得
  $query = "SELECT COUNT(message) FROM system_message WHERE room_no = $room_no " .
    "AND type = 'OBJECTION' AND message = '{$SELF->user_no}'";
  $self_objection_count = FetchResult($query);

  //生きていて(ゲーム終了後は死者でもOK)「異議」あり、のセット要求があればセットする(最大回数以内の場合)
  if($SELF->is_live() && ! $ROOM->is_night() && $RQ_ARGS->set_objection &&
     $self_objection_count < $GAME_CONF->objection){
    InsertSystemMessage($SELF->user_no, 'OBJECTION');
    InsertSystemTalk('OBJECTION', $ROOM->system_time, '', '', $SELF->uname);
    mysql_query('COMMIT');
  }

  //ユーザ総数を取得して人数分の「異議あり」のクッキーを構築する
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0";
  $user_count = FetchResult($query);
  // //配列をリセット (0 番目に変な値が入らない事が保証されていれば不要かな？)
  // $objection_array = array();
  // unset($objection_array[0]);
  $objection_array = array_fill(1, $user_count, 0); //index は 1 から

  //message:異議ありをしたユーザ No とその回数を取得
  $sql = mysql_query("SELECT message, COUNT(message) AS message_count FROM system_message
			WHERE room_no = $room_no AND type = 'OBJECTION' GROUP BY message");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $this_user_no = (int)$array['message'];
    $this_count   = (int)$array['message_count'];
    $objection_array[$this_user_no] = $this_count;
  }

  //クッキーに格納 (有効期限一時間)
  foreach($objection_array as $value){
    if($str != '') $str .= ','; //カンマ区切り
    $str .= $value;
  }
  setcookie('objection', $str, $ROOM->system_time + 3600);

  //残り異議ありの回数
  $objection_left_count = $GAME_CONF->objection - $objection_array[$SELF->user_no];

  //<再投票を音でお知らせ用>
  //再投票の回数を取得
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = {$ROOM->date} AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) != 0){ //クッキーに格納 (有効期限一時間)
    $last_vote_times = (int)mysql_result($sql, 0, 0); //何回目の再投票なのか取得
    setcookie('vote_times', $last_vote_times, $ROOM->system_time + 3600);
  }
  else{ //クッキーから削除 (有効期限一時間)
    setcookie('vote_times', '', $ROOM->system_time - 3600);
  }
}

//発言置換処理
function ConvertSay(&$say){
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $SELF;

  //リロード時、死者、ゲームプレイ中以外なら処理スキップ
  if($say == '' || $SELF->is_dead() || ! $ROOM->is_playing()) return false;

  //萌狼・不審者は一定確率で発言が遠吠え(デフォルト時)になる
  if(($SELF->is_role('cute_wolf') || $SELF->is_role('suspect')) &&
     mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate){
    $say = ($MESSAGE->cute_wolf != '' ? $MESSAGE->cute_wolf : $MESSAGE->wolf_howl);
  }
  //紳士・淑女は一定確率で発言が入れ替わる
  elseif(($SELF->is_role('gentleman') || $SELF->is_role('lady')) &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){
    $role_name = ($SELF->is_role('gentleman') ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $query = "SELECT handle_name FROM user_entry WHERE room_no = $room_no " .
      "AND uname <> '{$SELF->uname}' AND live = 'live' AND user_no > 0";
    $target_list = FetchArray($query);
    $rand_key    = array_rand($target_list);
    $say = $MESSAGE->$message_header . $target_list[$rand_key] . $MESSAGE->$message_footer;
  }
  //狼少年は一定確率で発言内容が反転される
  elseif($SELF->is_role('liar') && mt_rand(1, 100) <= $GAME_CONF->liar_rate){
    $say = strtr($say, $GAME_CONF->liar_replace_list);
  }

  if($SELF->is_role('invisible')){ //光学迷彩の処理
    $invisible_say = '';
    $count = mb_strlen($say);
    $rate = $GAME_CONF->invisible_rate;
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n" || $this_str == "\t" || $this_str == ' ' || $this_str == '　'){
	$invisible_say .= $this_str;
	continue;
      }
      if(mt_rand(1, 100) <= $rate)
	$invisible_say .= (strlen($this_str) == 2 ? '　' : '&nbsp;');
      else
	$invisible_say .= $this_str;
      if($rate++ > 100) break;
    }
    $say = $invisible_say;
  }

  if($SELF->is_role('rainbow')){ //虹色迷彩の処理
    $say = strtr($say, $GAME_CONF->rainbow_replace_list);
  }

  if($SELF->is_role('silent')){ //無口の処理
    if(mb_strlen($say) > $GAME_CONF->silent_length){
      $say = mb_substr($say, 0, $GAME_CONF->silent_length) . '……';
    }
  }
}

//遺言登録
function EntryLastWords($say){
  global $room_no, $ROOM, $SELF;

  //ゲーム終了後、死者、ブン屋、筆不精なら登録しない
  if($ROOM->is_finished() || $SELF->is_dead() || $SELF->is_role('reporter') ||
     $SELF->is_role('no_last_words')) return false;

  //遺言を残す
  mysql_query("UPDATE user_entry SET last_words = '$say' WHERE room_no = $room_no
		AND uname = '{$SELF->uname}' AND user_no > 0");
  mysql_query('COMMIT'); //一応コミット
}

//発言
function Say($say){
  global $RQ_ARGS, $room_no, $ROOM, $SELF;

  if($ROOM->is_real_time()){ //リアルタイム制
    GetRealPassTime(&$left_time);
    $spend_time = 0; //会話で時間経過制の方は無効にする
  }
  else{ //会話で時間経過制
    GetTalkPassTime(&$left_time); //経過時間の和
    if(strlen($say) <= 100) //経過時間
      $spend_time = 1;
    elseif(strlen($say) <= 200)
      $spend_time = 2;
    elseif(strlen($say) <= 300)
      $spend_time = 3;
    else
      $spend_time = 4;
  }

  if(! $ROOM->is_playing()){ //ゲーム開始前後はそのまま発言
    Write($say, $ROOM->day_night, 0, true);
  }
  //身代わり君 (仮想 GM 対応) は遺言を専用のシステムメッセージに切り替え
  elseif($SELF->is_dummy_boy() && ($RQ_ARGS->is_last_words() || ($SELF->is_live() && $left_time == 0))){
    Write($say, "{$ROOM->day_night} dummy_boy", 0); //発言時間を更新しない
  }
  elseif($SELF->is_dead()){ //死亡者の霊話
    Write($say, 'heaven', 0); //発言時間を更新しない
  }
  elseif($SELF->is_live() && $left_time > 0){ //生存者で制限時間内
    if($ROOM->is_day()){ //昼はそのまま発言
      Write($say, 'day', $spend_time, true);
    }
    elseif($ROOM->is_night()){ //夜は役職毎に分ける
      if($SELF->is_wolf()) //人狼
	Write($say, 'night wolf', $spend_time, true);
      elseif($SELF->is_role('whisper_mad')) //囁き狂人
	Write($say, 'night mad', 0);
      elseif($SELF->is_role('common')) //共有者
	Write($say, 'night common', 0);
      elseif($SELF->is_fox()) //妖狐
	Write($say, 'night fox', 0);
      else //独り言
	Write($say, 'night self_talk', 0);
    }
  }
}

//発言を DB に登録する
function Write($say, $location, $spend_time, $update = false){
  global $MESSAGE, $RQ_ARGS, $room_no, $ROOM, $SELF;

  //声の大きさを決定
  $voice = $RQ_ARGS->font_type;
  if($SELF->is_live() && $ROOM->is_playing()){
    $voice_list = array('strong', 'normal', 'weak');
    if(    $SELF->is_role('strong_voice')) $voice = 'strong';
    elseif($SELF->is_role('normal_voice')) $voice = 'normal';
    elseif($SELF->is_role('weak_voice'))   $voice = 'weak';
    elseif($SELF->is_role('upper_voice')){
      $voice_key = array_search($RQ_ARGS->font_type, $voice_list);
      if($voice_key == 0) $say = $MESSAGE->howling;
      else $voice = $voice_list[$voice_key - 1];
    }
    elseif($SELF->is_role('downer_voice')){
      $voice_key = array_search($RQ_ARGS->font_type, $voice_list);
      if($voice_key >= count($voice_list) - 1) $say = $MESSAGE->common_talk;
      else $voice = $voice_list[$voice_key + 1];
    }
    elseif($SELF->is_role('random_voice')){
      $rand_key = array_rand($voice_list);
      $voice = $voice_list[$rand_key];
    }
  }

  InsertTalk($room_no, $ROOM->date, $location, $SELF->uname,
	     $ROOM->system_time, $say, $voice, $spend_time);
  if($update) UpdateTime();
  mysql_query('COMMIT'); //一応コミット
}

//ゲーム停滞のチェック
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $room_no, $ROOM, $USERS;

  //ゲーム中以外は処理をしない
  if(! $ROOM->is_playing()) return false;

  //テーブルロック
  if(! mysql_query("LOCK TABLES room WRITE, talk WRITE, vote WRITE,
			user_entry WRITE, system_message WRITE")){
    return false;
  }

  //最後に発言された時間を取得
  $last_updated_time = FetchResult("SELECT last_updated FROM room WHERE room_no = $room_no");
  $last_updated_pass_time = $ROOM->system_time - $last_updated_time;

  //経過時間を取得
  if($ROOM->is_real_time()) //リアルタイム制
    GetRealPassTime(&$left_time);
  else //会話で時間経過制
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //リアルタイム制でなく、制限時間内で沈黙閾値を超えたならなら一時間進める(沈黙)
  if(! $ROOM->is_real_time() && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '・・・・・・・・・・ ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      InsertTalk($room_no, $ROOM->date, "{$ROOM->day_night} system", 'system',
		 $ROOM->system_time, $sentence, NULL, $TIME_CONF->silence_pass);
      UpdateTime();
    }
  }
  elseif($left_time == 0){ //制限時間を過ぎていたら警告を出す
    //突然死発動までの時間を取得
    $left_time_str = ConvertTime($TIME_CONF->sudden_death); //表示用に変換
    $sudden_death_announce = 'あと' . $left_time_str . 'で' . $MESSAGE->sudden_death_announce;

    //既に警告を出しているかチェック
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = $room_no " .
      "AND date = {$ROOM->date} AND location = '{$ROOM->day_night} system' " .
      "AND uname = 'system' AND sentence = '$sudden_death_announce'";
    if(FetchResult($query) == 0){ //警告を出していなかったら出す
      InsertSystemTalk($sudden_death_announce, ++$ROOM->system_time); //全会話の後に出るように
      UpdateTime(); //更新時間を更新
      $last_updated_pass_time = 0;
    }
    $ROOM->sudden_death = $TIME_CONF->sudden_death - $last_updated_pass_time;

    //制限時間を過ぎていたら未投票の人を突然死させる
    if($ROOM->sudden_death <= 0){
      //生存者を取得するための基本 SQL 文
      $query_live = "SELECT uname FROM user_entry WHERE room_no = $room_no " .
	"AND live = 'live' AND user_no > 0";

      //投票済みの人を取得するための基本 SQL 文
      $query_vote = "SELECT uname FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} AND ";

      if($ROOM->is_day()){
	//投票回数を取得
	$vote_times = GetVoteTimes();

	//投票済みの人のユーザ名を取得
	$add_action = "situation = 'VOTE_KILL' AND vote_times = $vote_times";
	$vote_uname_list = FetchArray($query_vote . $add_action);

	//投票が必要な人のユーザ名を取得
	$live_uname_list = FetchArray($query_live);

	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);
      }
      elseif($ROOM->is_night()){
	//人狼の投票を確認
	$wolf_vote_count = FetchCount($query_vote . "situation = 'WOLF_EAT'");
	$wolf_list = ($wolf_vote_count == 0 ? GetLiveWolves() : array());

	//対象役職のデータを作成
	$action_list = array('MAGE_DO', 'CHILD_FOX_DO');
	$actor_list  = array('%mage', 'child_fox');

	if($ROOM->date == 1){
	  array_push($action_list, 'CUPID_DO', 'MANIA_DO');
	  array_push($actor_list, 'cupid', 'mania');
	}
	else{
	  array_push($action_list, 'GUARD_DO', 'REPORTER_DO', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO',
		     'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
	  array_push($actor_list, '%guard', 'reporter', 'assassin', 'trap_mad');
	  if(! $ROOM->is_open_cast()){
	    array_push($action_list, 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	    array_push($actor_list, 'poison_cat');
	  }
	}

	//投票済みの人のユーザ名を取得
	foreach($action_list as $this_action){
	  if($add_action != '') $add_action .= ' OR ';
	  $add_action .= "situation = '$this_action'";
	}
	$vote_uname_list = FetchArray($query_vote . '(' . $add_action . ')');

	//投票が必要な人のユーザ名を取得
	foreach($actor_list as $this_actor){
	  if($add_actor != '') $add_actor .= ' OR ';
	  if($this_actor == 'trap_mad'){
	    $add_actor .= "(role LIKE '{$this_actor}%' AND !(role LIKE '%lost_ability%'))";
	  }
	  else{
	    $add_actor .= "role LIKE '{$this_actor}%'";
	  }
	}
	$live_uname_list = FetchArray("$query_live AND uname <> 'dummy_boy' AND ($add_actor)");

	//未投票の人狼のリストを追加
	$novote_uname_list = array_merge($wolf_list, array_diff($live_uname_list, $vote_uname_list));
      }

      //未投票者を全員突然死させる
      $flag_medium = CheckMedium(); //巫女の出現チェック
      $dead_lovers_list = array(); //恋人後追い対象者リスト
      foreach($novote_uname_list as $this_uname){
	SuddenDeath($this_uname, $flag_medium);
	$this_role = $USERS->GetRole($this_uname);
	if(strpos($this_role, 'lovers') !== false){ //恋人なら後でまとめて後追い処理を行う
	  array_push($dead_lovers_list, $this_role);
	}
      }
      foreach($dead_lovers_list as $this_role){ //恋人後追い処理
	LoversFollowed($this_role, $flag_medium, true);
      }
      InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //投票リセットメッセージ
      InsertSystemTalk($sudden_death_announce, ++$ROOM->system_time); //突然死告知メッセージ
      UpdateTime(); //制限時間リセット
      DeleteVote(); //投票リセット
      CheckVictory(); //勝敗チェック
    }
  }
  mysql_query('UNLOCK TABLES'); //テーブルロック解除
}

//村名前、番地、何日目、日没まで〜時間を出力(勝敗がついたら村の名前と番地、勝敗を出力)
function OutputGameHeader(){
  global $GAME_CONF, $TIME_CONF, $MESSAGE, $RQ_ARGS, $room_no, $ROOM, $SELF,
    $cookie_day_night, $cookie_objection, $objection_array, $objection_left_count;

  $room_message = '<td class="room"><span>' . $ROOM->name . '村</span>　〜' . $ROOM->comment .
    '〜[' . $room_no . '番地]</td>'."\n";
  $url_room   = '?room_no=' . $room_no;
  $url_reload = ($RQ_ARGS->auto_reload > 0 ? '&auto_reload=' . $RQ_ARGS->auto_reload : '');
  $url_sound  = ($RQ_ARGS->play_sound ? '&play_sound=on'  : '');
  $url_list   = ($RQ_ARGS->list_down  ? '&list_down=on'   : '');
  $url_dead   = ($ROOM->dead_mode     ? '&dead_mode=on'   : '');
  $url_heaven = ($ROOM->heaven_mode   ? '&heaven_mode=on' : '');
  $real_time  = $ROOM->is_real_time();

  echo '<table class="game-header"><tr>'."\n";
  if(($SELF->is_dead() && $ROOM->heaven_mode) || $ROOM->is_aftergame()){ //霊界とログ閲覧時
    if($SELF->is_dead() && $ROOM->heaven_mode)
      echo '<td>&lt;&lt;&lt;幽霊の間&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //過去の日のログへのリンク生成
    echo '<td class="view-option">ログ';

    $url_header ='<a href="game_log.php' . $url_room . '&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '1' . $url_night . '1(夜)</a>'."\n";
    for($i = 2; $i < $ROOM->date; $i++){
      echo $url_header . $i . $url_day   . $i . '(昼)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(夜)</a>'."\n";
    }
    if($ROOM->is_night() && $ROOM->heaven_mode){
      echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(昼)</a>'."\n";
    }
    elseif($ROOM->is_aftergame()){
      $query = "SELECT COUNT(uname) FROM talk WHERE room_no = $room_no " .
	"AND date = {$ROOM->date} AND location = 'day'";
      if(FetchResult($query) > 0){
	echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(昼)</a>'."\n";
      }
    }

    if($ROOM->heaven_mode){
      echo '</td>'."\n" . '</tr></table>'."\n";
      return;
    }
  }
  else{
    echo $room_message . '<td class="view-option">'."\n";
    if($SELF->is_dead() && $ROOM->dead_mode){ //死亡者の場合の、真ん中の全表示地上モード
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="更新">
</form>

EOF;
    }
  }

  if(! $ROOM->is_aftergame()){ //ゲーム終了後は自動更新しない
    $url_header = '<a target="_top" href="game_frame.php' . $url_room .
      $url_dead . $url_heaven . $url_list;
    OutputAutoReloadLink($url_header . $url_sound  . '&auto_reload=');

    $url = $url_header . $url_reload . '&play_sound=';
    echo ' [音でお知らせ](' .
      ($RQ_ARGS->play_sound ?  'on ' . $url . 'off">off</a>' : $url . 'on">on</a> off') .
      ')'."\n";
  }

  //プレイヤーリストの表示位置
  echo '<a target="_top" href="game_frame.php' . $url_room . $url_dead . $url_heaven .
    $url_reload . $url_sound  . '&list_down=' . ($RQ_ARGS->list_down ? 'off">↑' : 'on">↓') .
    'リスト</a>'."\n";

  //夜明けを音でお知らせする
  if($RQ_ARGS->play_sound){
    //夜明けの場合
    if($cookie_day_night != $ROOM->day_night && $ROOM->is_day()) OutputSound('morning');

    /*
    //異議あり、を音で知らせる
    $cookie_objection_array = explode(',', $cookie_objection); //クッキーの値を配列に格納する

    $count = count($objection_array);
    for($i = 1; $i <= $count; $i++){ //差分を計算 (index は 1 から)
      //差分があれば性別を確認して音を鳴らす
      if((int)$objection_array[$i] > (int)$cookie_objection_array[$i]){
	$sql = mysql_query("SELECT sex FROM user_entry WHERE room_no = $room_no AND user_no = $i");
	$objection_sound = 'objection_' . mysql_result($sql, 0, 0);
	OutputSound($objection_sound, true);
      }
    }
    */
  }
  echo '</td></tr>'."\n".'</table>'."\n";

  switch($ROOM->day_night){
  case 'beforegame': //開始前の注意を出力
    echo '<div class="caution">'."\n";
    echo 'ゲームを開始するには全員がゲーム開始に投票する必要があります';
    echo '<span>(投票した人は村人リストの背景が赤くなります)</span>'."\n";
    echo '</div>'."\n";
    break;

  case 'day':
    $time_message = '　日没まで ';
    break;

  case 'night':
    $time_message = '　夜明けまで ';
    break;

  case 'aftergame': //勝敗結果を出力して処理終了
    OutputVictory();
    return;
  }

  if($ROOM->is_beforegame()) OutputGameOption(); //ゲームオプションを説明
  echo '<table class="time-table"><tr>'."\n";
  if(! $ROOM->is_aftergame()){ //ゲーム終了後以外なら、サーバとの時間ズレを表示
    $date_str = gmdate('Y, m, j, G, i, s', $ROOM->system_time);
    echo '<script type="text/javascript" src="javascript/output_diff_time.js"></script>'."\n";
    echo '<td>サーバとローカルPCの時間ズレ(ラグ含)： ' . '<span><script type="text/javascript">' .
      "output_diff_time('$date_str');" . '</script>' . '秒</span></td></td>'."\n";
    echo '<tr>';
  }
  OutputTimeTable(); //経過日数と生存人数を出力

  $left_time = 0;
  //経過時間を取得
  if($real_time) //リアルタイム制
    GetRealPassTime(&$left_time);
  else //会話で時間経過制
    $left_talk_time = GetTalkPassTime(&$left_time);

  if($ROOM->is_beforegame()){
    echo '<td class="real-time">';
    if($real_time){ //実時間の制限時間を取得
      sscanf(strstr($ROOM->game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
      echo "設定時間： 昼 <span>{$day_minutes}分</span> / 夜 <span>{$night_minutes}分</span>";
    }
    echo '　突然死：<span>' . ConvertTime($TIME_CONF->sudden_death) . '</span></td>';
  }
  if($ROOM->is_playing()){
    if($real_time){ //リアルタイム制
      echo '<td class="real-time"><form name="realtime_form">'."\n";
      echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
      echo '</form></td>'."\n";
    }
    elseif($left_talk_time){ //発言による仮想時間
      echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
    }
  }

  //異議あり、のボタン(夜と死者モード以外)
  if($ROOM->is_beforegame() ||
     ($ROOM->is_day() && ! $ROOM->dead_mode && ! $ROOM->heaven_mode && $left_time > 0)){
    $url = 'game_play.php' . $url_room . $url_reload . $url_sound . $url_list . '#game_top';
    echo <<<EOF
<td class="objection"><form method="POST" action="$url">
<input type="hidden" name="set_objection" value="on">
<input type="image" name="objimage" src="{$GAME_CONF->objection_image}" border="0">
</form></td>
<td>($objection_left_count)</td>

EOF;
  }
  echo '</tr></table>'."\n";

  if($ROOM->is_playing() && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($ROOM->sudden_death > 0){
      echo $MESSAGE->sudden_death_time . ConvertTime($ROOM->sudden_death) . '<br>'."\n";
    }
  }
}

//天国の霊話ログ出力
function OutputHeavenTalkLog(){
  global $room_no, $ROOM;

  //出力条件をチェック
  // if($SELF->is_dead()) return false; //呼び出し側でチェックするので現在は不要

  //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
  $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.live AS talk_live,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry, talk, user_icon
			WHERE talk.room_no = $room_no
			AND talk.location LIKE 'heaven'
			AND ( (user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR (user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no) )
			ORDER BY time DESC");

  echo '<table class="talk">'."\n";
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $talk_uname  = $array['talk_uname'];
    $talk_handle = $array['talk_handle_name'];
    $talk_live   = $array['talk_live'];
    // $talk_sex    = $array['talk_sex'];  //現在未使用
    $talk_color  = $array['talk_color'];
    $sentence    = $array['sentence'];
    $font_type   = $array['font_type'];
    // $location    = $array['location']; //現在未使用

    LineToBR(&$sentence); //改行を<br>タグに置換

    //霊界で役職が公開されている場合のみ HN を追加
    if($ROOM->is_open_cast()) $talk_handle .= '<span>(' . $talk_uname . ')</span>';

    //会話出力
    echo '<tr class="user-talk">'."\n";
    echo '<td class="user-name"><font color="' . $talk_color . '">◆</font>' .
      $talk_handle . '</td>'."\n";
    echo '<td class="say ' . $font_type . '">' . $sentence . '</td>'."\n";
    echo '</tr>'."\n";
  }
  echo '</table>'."\n";
}

//能力の種類とその説明を出力
function OutputAbility(){
  global $GAME_CONF, $ROLE_IMG, $MESSAGE, $room_no, $ROOM, $SELF;

  //ゲーム中のみ表示する
  if(! $ROOM->is_playing()) return false;

  if($SELF->is_dead()){ //死亡したら能力を表示しない
    echo '<span class="ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  $role_list = $SELF->role_list;
  $main_role = array_shift($role_list);
  $is_first_night = ($ROOM->is_night() && $ROOM->date == 1);
  $is_after_first_night = ($ROOM->is_night() && $ROOM->date > 1);

  if($main_role == 'human' || $main_role == 'suspect' || $main_role == 'unconscious'){
    $ROLE_IMG->DisplayImage('human');
  }
  elseif(strpos($main_role, 'wolf') !== false){
    $ROLE_IMG->DisplayImage($main_role);
    OutputPartner("role LIKE '%wolf%' AND uname <> '{$SELF->uname}'", 'wolf_partner'); //仲間を表示
    OutputPartner("role LIKE 'whisper_mad%'", 'mad_partner'); //囁き狂人を表示

    //夜だけ無意識を表示
    if($ROOM->is_night()) OutputPartner("role LIKE 'unconscious%'", 'unconscious_list');

    if($main_role == 'tongue_wolf'){ //舌禍狼の噛み結果を表示
      $action = 'TONGUE_WOLF_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult('wolf_result', $target, 'result_' . $target_role);
	  break;
	}
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('wolf-eat', 'WOLF_EAT'); //夜の投票
  }
  elseif(strpos($main_role, 'mage') !== false){
    $role_name = ($main_role == 'dummy_mage' ? 'mage' : $main_role);
    $ROLE_IMG->DisplayImage($role_name);

    //占い結果を表示
    $action = 'MAGE_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    $header = ($main_role == 'psycho_mage' ? $main_role : 'result') . '_';
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult('mage_result', $target, $header . $target_role);
	break;
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('mage-do', 'MAGE_DO'); //夜の投票
  }
  elseif(strpos($main_role, 'necromancer') !== false || $main_role == 'medium'){
    if(strpos($role, 'necromancer') !== false){
      $role_name = 'necromancer';
      $result    = 'necromancer_result';
      $action    = 'NECROMANCER_RESULT';
      switch($main_role){
      case 'soul_necromancer':
	$role_name = $main_role;
	$action    = 'SOUL_' . $action;
	break;

      case 'dummy_necromancer':
	$action = 'DUMMY_' . $action;
	break;
      }
    }
    else{
      $role_name = 'medium';
      $result    = 'medium_result';
      $action    = 'MEDIUM_RESULT';
    }
    $ROLE_IMG->DisplayImage($role_name);

    //判定結果を表示
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif($main_role == 'trap_mad'){
    $ROLE_IMG->DisplayImage($main_role);

    if(strpos($role, 'lost_ability') === false && $is_after_first_night){ //夜の投票
      OutputVoteMessage('wolf-eat', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
  elseif(strpos($main_role, 'mad') !== false){
    $ROLE_IMG->DisplayImage($main_role);
    if($main_role != 'mad'){
      OutputPartner("role LIKE '%wolf%'", 'wolf_partner'); //狼を表示
      if($main_role == 'whisper_mad'){ //囁き狂人を表示
	OutputPartner("role LIKE 'whisper_mad%' AND uname <> '{$SELF->uname}'", 'mad_partner');
      }
    }
  }
  elseif(strpos($main_role, 'guard') !== false){
    $role_name = ($main_role == 'dummy_guard' ? 'guard' : $main_role);
    $ROLE_IMG->DisplayImage($role_name);

    //護衛結果を表示
    $sql = GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'guard_success');
	break;
      }
    }

    if($main_role != 'dummy_guard'){ //狩り結果を表示
      $sql = GetAbilityActionResult('GUARD_HUNTED');
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	if($SELF->handle_name == $actor){
	  OutputAbilityResult(NULL, $target, 'guard_hunted');
	  break;
	}
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'GUARD_DO'); //夜の投票
  }
  elseif($main_role == 'reporter'){
    $ROLE_IMG->DisplayImage($main_role);

    //尾行結果を表示
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	$target .= ' さんは ' . $wolf_handle;
	OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
	break;
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'REPORTER_DO'); //夜の投票
  }
  elseif(strpos($main_role, 'common') !== false){
    $ROLE_IMG->DisplayImage('common');

    //仲間を表示
    if($main_role == 'dummy_common'){
      OutputPartner("uname = 'dummy_boy' AND uname <> '{$SELF->uname}'", 'common_partner');
    }
    else{
      OutputPartner("role LIKE 'common%' AND uname <> '{$SELF->uname}'", 'common_partner');
    }
  }
  elseif($main_role == 'child_fox'){
    $ROLE_IMG->DisplayImage('child_fox');

    //仲間を表示
    OutputPartner("role LIKE '%fox%' AND uname <> '{$SELF->uname}'", 'fox_partner');

    //占い結果を表示
    $action = 'CHILD_FOX_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
	break;
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('mage-do', 'CHILD_FOX_DO'); //夜の投票
  }
  elseif(strpos($main_role, 'fox') !== false){
    if($main_role == 'poison_fox'){
      echo '[役割]<br>　あなたは「管狐」、毒を持っています。(細かい能力は調整中です)<br>'."\n";
    }
    elseif($main_role == 'white_fox'){
      echo '[役割]<br>　あなたは「白狐」です。占われても死にませんが、人狼に襲われると死んでしまいます。(細かい能力は調整中です)<br>'."\n";
    }
    else
      $ROLE_IMG->DisplayImage($main_role);

    //子狐以外の仲間を表示
    OutputPartner("role LIKE 'fox%' AND uname <> '{$SELF->uname}'", 'fox_partner');

    //狐が狙われたメッセージを表示
    $sql = GetAbilityActionResult('FOX_EAT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      if($SELF->handle_name == mysql_result($sql, $i, 0)){
	OutputAbilityResult('fox_targeted', NULL);
	break;
      }
    }
  }
  elseif($main_role == 'poison_cat'){
    // $ROLE_IMG->DisplayImage('poison_cat');
    echo '[役割]<br>　あなたは「猫又」、毒をもっています。また、死んだ人を誰か一人蘇らせる事ができます。<br>'."\n";

    if(! $ROOM->is_open_cast()){
      //蘇生結果を表示
      $action = 'POISON_CAT_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
	  break;
	}
      }

      if($is_after_first_night){ //夜の投票
	OutputVoteMessage('poison-cat-do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($main_role == 'incubate_poison'){
    $ROLE_IMG->DisplayImage($main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif(strpos($main_role, 'poison') !== false) $ROLE_IMG->DisplayImage('poison');
  elseif($main_role == 'pharmacist'){
    $ROLE_IMG->DisplayImage($main_role);

    //解毒結果を表示
    $sql = GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'pharmacist_success');
	break;
      }
    }
  }
  elseif($main_role == 'cupid'){
    $ROLE_IMG->DisplayImage($main_role);

    //自分が矢を打った恋人 (自分自身含む) を表示する
    $cupid_id = strval($SELF->user_no);
    OutputPartner("role LIKE '%lovers[$cupid_id]%'", 'cupid_pair');

    if($is_first_night) OutputVoteMessage('cupid-do', 'CUPID_DO'); //初日夜の投票
  }
  elseif($main_role == 'mania'){
    // $ROLE_IMG->DisplayImage($main_role);
    echo '[役割]<br>　あなたは「神話マニア」です。1日目の夜に指定した人のメイン役職をコピーすることができます。<br>'."\n";

    if($is_first_night) OutputVoteMessage('mania-do', 'MANIA_DO'); //初日夜の投票
  }
  elseif($main_role == 'assassin'){
    // $ROLE_IMG->DisplayImage($main_role);
    echo '[役割]<br>　あなたは「暗殺者」です。夜に村人一人を暗殺することができます。<br>'."\n";

    if($is_after_first_night){ //夜の投票
      OutputVoteMessage('assassin-do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($main_role == 'quiz'){
    $ROLE_IMG->DisplayImage($main_role);
    if(strpos($ROOM->game_option, 'chaos') !== false){
      // $ROLE_IMG->DisplayImage('quiz_chaos');
      echo '闇鍋モードではあなたの最大の能力である噛み無効がありません。<br>'."\n";
      echo 'はっきり言って無理ゲーなので好き勝手にクイズでも出して遊ぶと良いでしょう。<br>'."\n";
    }
  }

  //ここから兼任役職
  if(in_array('lost_ability', $role_list)) $ROLE_IMG->DisplayImage('lost_ability'); //能力失効
  if($SELF->is_lovers()){ //恋人を表示する
    $lovers_str = GetLoversConditionString($SELF->role);
    OutputPartner("$lovers_str AND uname <> '{$SELF->uname}'", 'lovers_header', 'lovers_footer');
  }

  if(in_array('copied', $role_list)){ //神話マニアのコピー結果を表示
    $action = 'MANIA_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'result_' . $target_role);
	break;
      }
    }
  }

  //これ以降はサブ役職非公開オプションの影響を受ける
  if(strpos($ROOM->game_option, 'secret_sub_role') !== false) return;

  $role_keys_list   = array_keys($GAME_CONF->sub_role_list);
  $not_display_list = array('decide', 'plague', 'good_luck', 'bad_luck', 'lovers', 'copied');
  $display_list     = array_diff($role_keys_list, $not_display_list);
  $target_list      = array_intersect($display_list, $role_list);

  foreach($target_list as $this_role){
    $ROLE_IMG->DisplayImage($this_role);
  }
}

//仲間を表示する
function OutputPartner($query, $header, $footer = NULL){
  global $ROLE_IMG, $room_no;

  $query_header = "SELECT handle_name FROM user_entry WHERE room_no = '$room_no' AND user_no > 0 AND ";
  $partner_list = FetchArray($query_header . $query);
  if(count($partner_list) < 1) return false; //仲間がいなければ表示しない

  echo '<table class="ability-partner"><tr>'."\n";
  echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  echo '<td>　';
  foreach($partner_list as $partner) echo $partner . 'さん　　';
  echo '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//能力発動結果をデータベースに問い合わせる
function GetAbilityActionResult($action){
  global $room_no, $ROOM;

  $yesterday = $ROOM->date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $yesterday AND type = '$action'");
}

//能力発動結果を表示する
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  if($target) echo '<td>' . $target . '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//夜の未投票メッセージ出力
function OutputVoteMessage($class, $situation, $not_situation = ''){
  global $MESSAGE;

  //投票済みならメッセージを表示しない
  if(CheckSelfVoteNight($situation, $not_situation)) return false;

  $class_str   = 'ability-' . $class; //クラス名はアンダースコアを使わないでおく
  $message_str = 'ability_' . strtolower($situation);
  echo '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}

//昼の自分の未投票チェック
function CheckSelfVoteDay(){
  global $room_no, $ROOM, $SELF;

  //投票回数を取得
  $vote_times = GetVoteTimes();
  echo '<div class="self-vote">投票 ' . $vote_times . ' 回目：';

  //投票済みかどうか
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND date = {$ROOM->date} AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  echo (mysql_result($sql, 0, 0) ? '投票済み' : 'まだ投票していません') . '</div>'."\n";
}

//自分の遺言を出力
function OutputSelfLastWords(){
  global $room_no, $ROOM, $SELF;

  //ゲーム終了後は表示しない
  if($ROOM->is_aftergame()) return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND user_no > 0");

  //まだ入力してなければ表示しない
  if(mysql_num_rows($sql) == 0) return false;

  $last_words = mysql_result($sql, 0, 0);
  LineToBR(&$last_words); //改行コードを変換
  if($last_words == '') return false;

  echo <<<EOF
<table class="lastwords" cellspacing="5"><tr>
<td class="lastwords-title">自分の遺言</td>
<td class="lastwords-body">{$last_words}</td>
</tr></table>

EOF;
}
?>
