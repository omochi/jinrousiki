<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//セッション開始
session_start();
$session_id = session_id();

EncodePostData(); //ポストされた文字列を全てエンコードする

//引数を取得
$room_no     = (int)$_GET['room_no']; //部屋 No
$auto_reload = (int)$_GET['auto_reload']; //オートリロードの間隔
$dead_mode   = $_GET['dead_mode'];   //死亡者モード
$heaven_mode = $_GET['heaven_mode']; //霊話モード
// $view_mode   = $_GET['view_mode'];   //観戦モード
$list_down  = $_GET['list_down']; //プレイヤーリストを下にする
$play_sound = $_GET['play_sound'];//音でお知らせ
if($play_sound == 'on'){
  $cookie_day_night  = $_COOKIE['day_night'];       //夜明けを音でしらせるため
  $cookie_vote_times = (int)$_COOKIE['vote_times']; //再投票を音で知らせるため
  $cookie_objection  = $_COOKIE['objection'];       //「異議あり」を音で知らせるため
}

$say = $_POST['say']; //発言
$font_type = $_POST['font_type']; //フォントタイプ
$set_objection = $_POST['set_objection']; //「異議」あり、のセット

$dbHandle = ConnectDatabase(); //DB 接続
$uname = CheckSession($session_id); //セッション ID をチェック

//日付とシーンを取得
$sql = mysql_query("SELECT date, day_night, room_name, room_comment, game_option FROM room
			WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$room_name    = $array['room_name'];
$room_comment = $array['room_comment'];
$game_option  = $array['game_option'];
$date         = $array['date'];
$day_night    = $array['day_night'];

//自分のハンドルネーム、役割、生存を取得
$sql = mysql_query("SELECT user_no, handle_name, sex, role, live, last_load_day_night FROM user_entry
			WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no             = $array['user_no'];
$handle_name         = $array['handle_name'];
$sex                 = $array['sex'];
$role                = $array['role'];
$live                = $array['live'];
$last_load_day_night = $array['last_load_day_night'];

$system_time = TZTime(); //現在時刻を取得
$sudden_death_time = 0; //突然死実行までの残り時間

//必要なクッキーをセットする
$objection_array = array(); //SendCookie();で格納される・異議ありの情報
$objection_left_count = 0;  //SendCookie();で格納される・異議ありの残り回数
SendCookie();

// //勝敗のチェック //どこからも参照されてない模様
// $sql = mysql_query("SELECT victory_role FROM room WHERE room_no = $room_no");
// $victory_flag = (mysql_result($sql, 0, 0) != NULL);

//発言の有無をチェック
EscapeStrings(&$say, false); //エスケープ処理
if($say != '' && $live == 'live' && ($day_night == 'day' || $day_night == 'night')){ //発言置換系
  if(strpos($role, 'cute_wolf') !== false && mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate)
    $say = $MESSAGE->wolf_howl; //萌狼は低確率で発言が遠吠えになる
  elseif((strpos($role, 'gentleman') !== false || strpos($role, 'lady') !== false) &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){ //紳士・淑女の発言内容を置換
    $role_name = (strpos($role, 'gentleman') !== false ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $sql = mysql_query("SELECT handle_name FROM user_entry WHERE room_no = $room_no
			AND live = 'live' AND uname <> '$uname' AND user_no > 0");
    $count = mysql_num_rows($sql) - 1;
    $rand_key = mt_rand(0, $count);
    $say = $MESSAGE->$message_header . mysql_result($sql, $rand_key, 0) . $MESSAGE->$message_footer;
  }
  elseif(strpos($role, 'liar') !== false){ //狼少年の発言内容を置換
    if(mt_rand(1, 100) <= $GAME_CONF->liar_rate) $say = strtr($say, $GAME_CONF->liar_replace_list);
  }
  if(strpos($role, 'invisible') !== false){ //光学迷彩の処理
    $new_say = '';
    $count = mb_strlen($say);
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n" || $this_str == "\t") continue; //改行コード、タブは対象外
      if(mt_rand(1, 100) <= $GAME_CONF->invisible_rate)
	$new_say .= (strlen($this_str) == 2 ? '　' : ' ');
      else
	$new_say .= $this_str;
    }
    $say = $new_say;
  }
  if(strpos($role, 'silent') !== false){ //無口の処理
    if(mb_strlen($say) > $GAME_CONF->silent_length)
      $say = mb_substr($say, 0, $GAME_CONF->silent_length) . '……';
  }
}

if($say != '' && $font_type == 'last_words' && $live == 'live')
  EntryLastWords($say);  //生きていれば遺言登録
elseif($say != '' && ($last_load_day_night == $day_night ||
		      $live == 'dead' || $uname == 'dummy_boy'))
  Say($say); //死んでいるか、最後にリロードした時とシーンが一致しているか身代わり君なら書き込む
else
  CheckSilence(); //ゲーム停滞のチェック(沈黙、突然死)

//最後にリロードした時のシーンを更新
mysql_query("UPDATE user_entry SET last_load_day_night = '$day_night'
		WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
mysql_query('COMMIT');

OutputGamePageHeader(); //HTMLヘッダ
OutputGameHeader(); //部屋のタイトルなど

if($heaven_mode != 'on'){
  if($list_down != 'on') OutputPlayerList(); //プレイヤーリスト
  OutputAbility(); //自分の役割の説明
  if($day_night == 'day' && $live == 'live') CheckSelfVote(); //投票済みチェック
  OutputRevoteList(); //再投票の時、メッセージを表示する
}

//会話ログを出力
if($live == 'dead' && $heaven_mode == 'on')
  OutputHeavenTalkLog();
else
  OutputTalkLog();

if($heaven_mode != 'on'){
  if($live == 'dead') OutputAbilityAction(); //能力発揮
  OutputLastWords(); //遺言
  OutputDeadMan();   //死亡者
  OutputVoteList();  //投票結果
  if($dead_mode != 'on') OutputSelfLastWords(); //自分の遺言
  if($list_down == 'on') OutputPlayerList(); //プレイヤーリスト
}
OutputHTMLFooter();

DisconnectDatabase($dbHandle); //DB 接続解除

//-- 関数 --//
//必要なクッキーをまとめて登録(ついでに最新の異議ありの状態を取得して配列に格納)
function SendCookie(){
  global $GAME_CONF, $system_time, $room_no, $date, $day_night, $user_no, $live, $uname,
    $set_objection, $objection_array, $objection_left_count;

  //<夜明けを音でお知らせ用>
  //クッキーに格納 (夜明けに音でお知らせで使う・有効期限一時間)
  setcookie('day_night', $day_night, $system_time + 3600);

  //<「異議」ありを音でお知らせ用>
  //今までに自分が「異議」ありをした回数取得
  $sql = mysql_query("SELECT COUNT(message) FROM system_message WHERE room_no = $room_no
			AND type = 'OBJECTION' AND message = '$user_no'");

  //生きていて(ゲーム終了後は死者でもOK)「異議」あり、のセット要求があればセットする(最大回数以内の場合)
  if($live == 'live' && $day_night != 'night' && $set_objection == 'set' &&
     mysql_result($sql, 0, 0) < $GAME_CONF->objection){
    InsertSystemMessage($user_no, 'OBJECTION');
    InsertSystemTalk('OBJECTION', $system_time, '', '', $uname);
    mysql_query('COMMIT');
  }

  //ユーザ総数を取得して人数分の「異議あり」のクッキーを構築する
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  // //配列をリセット (0 番目に変な値が入らない事が保証されていれば不要かな？)
  // $objection_array = array();
  // unset($objection_array[0]);
  $objection_array = array_fill(1, mysql_result($sql, 0, 0), 0); //index は 1 から

  //message:異議ありをしたユーザ No とその回数を取得
  $sql = mysql_query("SELECT message, COUNT(message) AS message_count FROM system_message
			WHERE room_no = $room_no AND type = 'OBJECTION' GROUP BY message");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $this_user_no = (int)$array['message'];
    $this_count   = (int)$array['message_count'];
    $objection_array[$this_user_no] = $this_count;
  }

  //クッキーに格納 (有効期限一時間)
  $str = array_shift($objection_array);
  foreach($objection_array as $value) $str .= ',' . $value; //カンマ区切り
  setcookie('objection', $str, $system_time + 3600);

  //残り異議ありの回数
  $objection_left_count = $GAME_CONF->objection - $objection_array[$user_no];

  //<再投票を音でお知らせ用>
  //再投票の回数を取得
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $date AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) != 0){
    $last_vote_times = (int)mysql_result($sql, 0, 0); //何回目の再投票なのか取得
    setcookie('vote_times', $last_vote_times, $system_time + 3600); //クッキーに格納 (有効期限一時間)
  }
  else{
    setcookie('vote_times', '', $system_time - 3600); //クッキーから削除 (有効期限一時間)
  }
}

//遺言登録
function EntryLastWords($say){
  global $room_no, $day_night, $uname, $role, $live;

  //ゲーム終了後、死者、ブン屋、筆不精なら登録しない
  if($day_night == 'aftergame' || $live != 'live' || strpos($role, 'reporter') !== false ||
     strpos($role, 'no_last_words') !== false) return false;

  //遺言を残す
  mysql_query("UPDATE user_entry SET last_words = '$say' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
  mysql_query('COMMIT'); //一応コミット
}

//発言
function Say($say){
  global $room_no, $game_option, $day_night, $uname, $role, $live;

  if(strpos($game_option, 'real_time') !== false){ //リアルタイム制
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

  if($day_night == 'beforegame' || $day_night == 'aftergame') //ゲーム開始前後はそのまま発言
    Write($say, $day_night, 0, true);
  elseif($uname == 'dummy_boy'){ //身代わり君 (仮想 GM 対応)
    if($live == 'live' && $day_night == 'day' && $left_time > 0) //生きていて制限時間内の昼
      Write($say, 'day', $spend_time, true); //通常通り発言
    else //それ以外は専用のシステムメッセージに切り替え
      Write($say, "$day_night dummy_boy", 0, false); //発言時間を更新しない
  }
  elseif($live == 'dead') //死亡者の霊話
    Write($say, 'heaven', 0, false); //発言時間を更新しない
  elseif($live == 'live' && $left_time > 0){ //生存者で制限時間内
    if($day_night == 'day') //昼はそのまま発言
      Write($say, 'day', $spend_time, true);
    elseif($day_night == 'night'){ //夜は役職毎に分ける
      if(strpos($role, 'wolf') !== false) //狼
	Write($say, 'night wolf', $spend_time, true);
      elseif(strpos($role, 'common') !== false) //共有者
	Write($say, 'night common', 0);
      elseif(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false) //妖狐
	Write($say, 'night fox', 0);
      else //独り言
	Write($say, 'night self_talk', 0);
    }
  }
}

//発言を DB に登録する
function Write($say, $location, $spend_time, $update = false){
  global $system_time, $room_no, $date, $day_night, $uname, $role, $live, $font_type;

  //声の大きさを決定
  if($live == 'live' && ($day_night == 'day' || $day_night == 'night')){
    if(    strpos($role, 'strong_voice') !== false) $voice = 'strong';
    elseif(strpos($role, 'normal_voice') !== false) $voice = 'normal';
    elseif(strpos($role, 'weak_voice')   !== false) $voice = 'weak';
    elseif(strpos($role, 'random_voice') !== false){
      $voice_list = array('strong', 'normal', 'weak');
      $rand_key = array_rand($voice_list);
      $voice = $voice_list[$rand_key];
    }
    else $voice = $font_type;
  }
  else $voice = $font_type;

  InsertTalk($room_no, $date, $location, $uname, $system_time, $say, $voice, $spend_time);
  if($update) UpdateTime();
  mysql_query('COMMIT'); //一応コミット
}

//ゲーム停滞のチェック
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $system_time, $sudden_death_time,
    $room_no, $game_option, $date, $day_night;

  //ゲーム中以外は処理をしない
  if($day_night != 'day' && $day_night != 'night') return false;

  //テーブルロック
  if(! mysql_query("LOCK TABLES room WRITE, talk WRITE, vote WRITE,
			user_entry WRITE, system_message WRITE")){
    return false;
  }

  //最後に発言された時間を取得
  $sql = mysql_query("SELECT last_updated FROM room WHERE room_no = $room_no");
  $last_updated_time = mysql_result($sql, 0, 0);
  $last_updated_pass_time = $system_time - $last_updated_time;

  //経過時間を取得
  if(strpos($game_option, 'real_time') !== false) //リアルタイム制
    GetRealPassTime(&$left_time);
  else //会話で時間経過制
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //リアルタイム制でなく、制限時間内で沈黙閾値を超えたならなら一時間進める(沈黙)
  if(strpos($game_option, 'real_time') === false && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '・・・・・・・・・・ ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      InsertTalk($room_no, $date, "$day_night system", 'system', $system_time,
		 $sentence, NULL, $TIME_CONF->silence_pass);
      UpdateTime();
    }
  }
  elseif($left_time == 0){ //制限時間を過ぎていたら警告を出す
    //突然死発動までの時間を取得
    if(strpos($game_option, 'quiz') !== false)
      $sudden_death_base_time = $TIME_CONF->sudden_death_quiz;
    else
      $sudden_death_base_time = $TIME_CONF->sudden_death;

    $left_time_str = ConvertTime($sudden_death_base_time); //表示用に変換
    $sudden_death_announce = "あと" . $left_time_str . "で" . $MESSAGE->sudden_death_announce;

    //既に警告を出しているかチェック
    $sql = mysql_query("SELECT COUNT(uname) FROM talk WHERE room_no = $room_no
			AND date = $date AND location = '$day_night system'
			AND uname = 'system' AND sentence = '$sudden_death_announce'");
    if(mysql_result($sql, 0, 0) == 0){ //警告を出していなかったら出す
      InsertSystemTalk($sudden_death_announce, ++$system_time); //全会話の後に出るように
      UpdateTime(); //更新時間を更新
      $last_updated_pass_time = 0;
    }
    $sudden_death_time = $sudden_death_base_time - $last_updated_pass_time;

    //制限時間を過ぎていたら未投票の人を突然死させる
    if($sudden_death_time <= 0){
      //投票していない人を取得するための基本 SQL 文
      //(投票済みの人を左結合して、「投票済み=NULL・投票していない」を取得)
      $query = "SELECT user_entry.uname, user_entry.handle_name, user_entry.role
		FROM user_entry left join tmp_sd on user_entry.uname = tmp_sd.uname
		WHERE user_entry.room_no = $room_no AND user_entry.live = 'live'
		AND user_entry.user_no > 0 AND tmp_sd.uname is NULL";
      if($day_night == 'day'){
	//投票回数を取得
	$sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
				AND date = $date AND type = 'VOTE_TIMES'");
	$vote_times = mysql_result($sql, 0, 0);

	//投票済みの人のテンポラリテーブルを作成
	mysql_query("CREATE TEMPORARY TABLE tmp_sd SELECT uname FROM vote
			WHERE room_no = $room_no AND date = $date
			AND situation = 'VOTE_KILL' AND vote_times = $vote_times");
	//投票していない人を取得
	$sql_novote = mysql_query($query);
      }
      elseif($day_night == 'night'){
	//投票済みの人のテンポラリテーブルを作成
	mysql_query("CREATE TEMPORARY TABLE tmp_sd SELECT uname FROM vote
			WHERE room_no = $room_no AND date = $date
			AND (situation = 'WOLF_EAT' OR situation = 'MAGE_DO'
			OR situation = 'GUARD_DO' OR situation = 'CUPID_DO' OR situation = 'MANIA_DO')");

	//投票していない人を取得 (役職のみ)
	$query .= " AND (user_entry.role LIKE '%wolf%' OR user_entry.role LIKE '%mage%'";
	if ($date == 1) {
	  $query .= " OR user_entry.role LIKE 'cupid%' OR user_entry.role LIKE 'mania%')";
	}
	else {
	  $query .= " OR user_entry.role LIKE 'guard%')";
	}
	$sql_novote = mysql_query($query);
      }

      //未投票者の数
      $novote_count = mysql_num_rows($sql_novote);

      //未投票者を全員突然死させる
      for($i = 0; $i < $novote_count; $i++){
	$array = mysql_fetch_assoc($sql_novote);
	$this_uname  = $array['uname'];
	$this_handle = $array['handle_name'];
	$this_role   = $array['role'];

	SuddenDeath($this_uname, $this_handle, $this_role); //突然死実行
      }
      InsertSystemTalk($MESSAGE->vote_reset, ++$system_time); //投票リセットメッセージ
      InsertSystemTalk($sudden_death_announce, ++$system_time); //突然死告知メッセージ
      UpdateTime(); //制限時間リセット

      DeleteVote(); //投票リセット
      CheckVictory(); //勝敗チェック
    }
  }
  mysql_query('UNLOCK TABLES'); //テーブルロック解除
}

//村名前、番地、何日目、日没まで〜時間を出力(勝敗がついたら村の名前と番地、勝敗を出力)
function OutputGameHeader(){
  global $GAME_CONF, $MESSAGE, $SOUND, $system_time, $sudden_death_time, $room_no,
    $room_name, $room_comment, $game_option, $dead_mode, $heaven_mode,
    $date, $day_night, $live, $handle_name, $auto_reload, $play_sound, $list_down,
    $cookie_day_night, $cookie_objection, $objection_array, $objection_left_count;

  $room_message = '<td class="room"><span>' . $room_name . '村</span>　〜' . $room_comment .
    '〜[' . $room_no . '番地]</td>'."\n";
  $url_room   = '?room_no=' . $room_no;
  $url_reload = ($auto_reload > 0 ? '&auto_reload=' . $auto_reload : '');
  $url_sound  = ($play_sound  == 'on' ? '&play_sound=on'  : '');
  $url_list   = ($list_down   == 'on' ? '&list_down=on'   : '');
  $url_dead   = ($dead_mode   == 'on' ? '&dead_mode=on'   : '');
  $url_heaven = ($heaven_mode == 'on' ? '&heaven_mode=on' : '');
  $real_time  = (strpos($game_option, 'real_time') !== false);

  echo '<table class="game-header"><tr>'."\n";
  if(($live == 'dead' && $heaven_mode == 'on') || $day_night == 'aftergame'){ //霊界とログ閲覧時
    if($live == 'dead' && $heaven_mode == 'on')
      echo '<td>&lt;&lt;&lt;幽霊の間&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //過去の日のログへのリンク生成
    echo '<td class="view-option">ログ';

    $url_header ='<a href="game_log.php' . $url_room . '&log_mode=on&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '1' . $url_night . '1(夜)</a>'."\n";
    for($i=2; $i < $date; $i++){
      echo $url_header . $i . $url_day   . $i . '(昼)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(夜)</a>'."\n";
    }
    if($day_night == 'night' && $heaven_mode == 'on')
      echo $url_header . $date . $url_day . $date . '(昼)</a>'."\n";
    elseif($day_night == 'aftergame'){
      $sql = mysql_query("SELECT COUNT(uname) FROM talk WHERE room_no = $room_no
				AND date = $date AND location = 'day'");
      if(mysql_num_rows($sql) > 0)
	echo $url_header . $date . $url_day . $date . '(昼)</a>'."\n";
    }

    if($heaven_mode == 'on'){
      echo '</td>'."\n" . '</tr></table>'."\n";
      return;
    }
  }
  else{
    echo $room_message . '<td class="view-option">'."\n";
    if($live == 'dead' && $dead_mode == 'on'){ //死亡者の場合の、真ん中の全表示地上モード
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="更新">
</form>

EOF;
    }
  }

  if($day_night != 'aftergame'){ //ゲーム終了後は自動更新しない
    $url_header = '<a target="_top" href="game_frame.php' . $url_room .
      $url_dead . $url_heaven . $url_list;
    OutputAutoReloadLink($url_header . $url_sound  . '&auto_reload=');

    $url = $url_header . $url_reload . '&play_sound=';
    echo ' [音でお知らせ](' .
      ($play_sound == 'on' ?  'on ' . $url . 'off">off</a>' : $url . 'on">on</a> off') .
      ')'."\n";
  }

  //プレイヤーリストの表示位置
  echo '<a target="_top" href="game_frame.php' . $url_room . $url_dead . $url_heaven .
    $url_reload . $url_sound  . '&list_down=' . ($list_down == 'on' ? 'off">↑' : 'on">↓') .
    'リスト</a>'."\n";

  //夜明けを音でお知らせする
  if($play_sound == 'on'){
    //夜明けの場合
    if($cookie_day_night != $day_night && $day_night == 'day') OutputSound($SOUND->morning);

    //異議あり、を音で知らせる
    $cookie_objection_array = explode(',', $cookie_objection); //クッキーの値を配列に格納する

    $count = count($objection_array);
    for($i = 1; $i <= $count; $i++){ //差分を計算 (index は 1 から)
      //差分があれば性別を確認して音を鳴らす
      if((int)$objection_array[$i] > (int)$cookie_objection_array[$i]){
	$sql = mysql_query("SELECT sex FROM user_entry WHERE room_no = $room_no AND user_no = $i");
	$objection_sound = 'objection_' . mysql_result($sql, 0, 0);
	// OutputSound($SOUND->$objection_sound, false); //ループをいったん切る
      }
    }
  }
  echo '</td></tr>'."\n" . '</table>'."\n";

  switch($day_night){
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

  if($day_night == 'beforegame') OutputGameOption(); //ゲームオプションを説明
  echo '<table class="time-table"><tr>'."\n";
  if($day_night != 'aftergame'){ //ゲーム終了後以外なら、サーバとの時間ズレを表示
    $date_str = gmdate('Y, m, j, G, i, s', $system_time);
    echo '<script type="text/javascript" src="javascript/output_diff_time.js"></script>'."\n";
    echo '<td>サーバとローカルPCの時間ズレ(ラグ含)： ' . '<span><script type="text/javascript">' .
      "output_diff_time('$date_str');" . '</script></span>' . '秒</td></td>'."\n";
    echo '<tr>';
  }
  OutputTimeTable(); //経過日数と生存人数を出力

  $left_time = 0;
  //経過時間を取得
  if($real_time) //リアルタイム制
    GetRealPassTime(&$left_time);
  else //会話で時間経過制
    $left_talk_time = GetTalkPassTime(&$left_time);

  if($day_night == 'beforegame' && $real_time){
    //実時間の制限時間を取得
    sscanf(strstr($game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
    echo '<td class="real-time">';
    echo "設定時間： 昼 <span>{$day_minutes}</span>分 / 夜 <span>{$night_minutes}</span>分";
    echo '</td>';
  }
  if($day_night == 'day' || $day_night == 'night'){
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
  if($day_night == 'beforegame' ||
     ($day_night == 'day' && $dead_mode != 'on' && $heaven_mode != 'on' && $left_time > 0)){
    $url = 'game_play.php' . $url_room . $url_reload . $url_sound . $url_list . '#game_top';
    echo <<<EOF
<td class="objection"><form method="POST" action="$url">
<input type="hidden" name="set_objection" value="set">
<input type="image" name="objimage" src="{$GAME_CONF->objection_image}" border="0">
</form></td>
<td>($objection_left_count)</td>

EOF;
  }
  echo '</tr></table>'."\n";

  if(($day_night == 'day' || $day_night == 'night') && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($sudden_death_time > 0)
      echo $MESSAGE->sudden_death_time . $sudden_death_time . '秒<br>'."\n";
  }
}

//天国の霊話ログ出力
function OutputHeavenTalkLog(){
  global $room_no, $game_option, $heaven_mode, $date, $day_night;

  //出力条件をチェック
  // global $uname, $live, $role;
  // if($live != 'dead') return false; //呼び出し側でチェックするので現在は不要

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
    if(strpos($game_option, 'not_open_cast') === false)
      $talk_handle .= '<span>(' . $talk_uname . ')</span>';

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
  global $ROLE_IMG, $MESSAGE, $room_no, $game_option, $date, $day_night,
    $user_no, $uname, $handle_name, $role, $live;

  //ゲーム中のみ表示する
  if($day_night == 'beforegame' || $day_night == 'aftergame') return false;

  if($live == 'dead'){ //死亡したら能力を表示しない
    echo '<span class="ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  if(strpos($role, 'human') !== false || strpos($role, 'suspect') !== false ||
     strpos($role, 'unconscious') !== false) OutputRoleComment('human');
  elseif(strpos($role, 'wolf') !== false){
    if(    strpos($role, 'boss_wolf')   !== false) OutputRoleComment('boss_wolf');
    elseif(strpos($role, 'poison_wolf') !== false) OutputRoleComment('poison_wolf');
    elseif(strpos($role, 'tongue_wolf') !== false) OutputRoleComment('tongue_wolf');
    elseif(strpos($role, 'cute_wolf')   !== false) OutputRoleComment('cute_wolf');
    else OutputRoleComment('wolf');

    //仲間を表示
    OutputPartner("role LIKE '%wolf%' AND uname <> '$uname'", 'wolf_partner');

    //夜だけ無意識を表示
    if($day_night == 'night') OutputPartner("role LIKE 'unconscious%'", 'unconscious_list');

    //舌禍狼の噛み結果を表示
    $action = 'WOLF_EAT';
    $active_tongue_wolf = (strpos($role, 'tongue_wolf') !== false &&
			   strpos($role, 'lost_ability') === false);
    if($active_tongue_wolf){
      $sql = GetAbilityActionResult($action);
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	if($handle_name != $actor) continue; //自分の噛み結果のみ表示

	//噛んだ人の役職を取得
	$sql_target = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
					AND handle_name = '$target' AND user_no > 0");
	$target_role = GetMainRole(mysql_result($sql_target, 0, 0));
	if($target_role == 'human')
	  $result_role = 'lost_ability'; //村人なら能力失効
	else
	  $result_role = 'result_' . $target_role;
	OutputAbilityResult('wolf_result', $target, $result_role);
      }
    }

    if($day_night == 'night'){ //夜の噛み投票
      CheckNightVote($action, 'wolf-eat');

      //舌禍狼の能力失効判定
      if($active_tongue_wolf){ //ここで処理するのは無駄が多いので TONGUE_WOLF_RESULT を作るべき
	$sql = GetAbilityActionResult($action);
	$count = mysql_num_rows($sql);
	for($i = 0; $i < $count; $i++){
	  list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	  if($handle_name != $actor) continue; //自分の噛み結果のみ表示

	  //噛んだ人の役職を取得
	  $sql_target = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
					AND handle_name = '$target' AND user_no > 0");
	  $target_role = GetMainRole(mysql_result($sql_target, 0, 0));
	  if($target_role == 'human'){
	    $role .= ' lost_ability';
	    mysql_query("UPDATE user_entry SET role = '$role' WHERE room_no = $room_no
				AND uname = '$uname' AND user_no > 0");
	  }
	}
      }
    }
  }
  elseif(strpos($role, 'mage') !== false){
    if(strpos($role, 'soul_mage') !== false) OutputRoleComment('soul_mage');
    else OutputRoleComment('mage'); //夢見人 (dummy_mage) も含む

    //占い結果を表示
    $action = 'MAGE_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) //自分の占い結果のみ表示
	OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
    }

    if($day_night == 'night') CheckNightVote('MAGE_DO', 'mage-do'); //夜の占い投票
  }
  elseif(strpos($role, 'necromancer') !== false || strpos($role, 'medium') !== false){
    if(strpos($role, 'necromancer') !== false){
      $role_name = 'necromancer';
      $action    = 'NECROMANCER_RESULT';
      $result    = 'necromancer_result';
    }
    else{
      $role_name = 'medium';
      $action    = 'MEDIUM_RESULT';
      $result    = 'medium_result';
    }
    OutputRoleComment($role_name);

    //判定結果を表示
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif(strpos($role, 'fanatic_mad') !== false){
    OutputRoleComment('fanatic_mad');

    //狼を表示 //仲間じゃないから専用の画像を作るべき
    OutputPartner("role LIKE '%wolf%'", 'wolf_partner');
  }
  elseif(strpos($role, 'mad') !== false) OutputRoleComment('mad');
  elseif(strpos($role, 'guard') !== false){
    if(strpos($role, 'poison_guard') !== false) OutputRoleComment('poison_guard');
    else OutputRoleComment('guard');

    //護衛結果を表示
    $sql = GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'guard_success');
    }

    if($day_night == 'night' && $date != 1) CheckNightVote('GUARD_DO', 'guard-do'); //夜の護衛投票
  }
  elseif(strpos($role, 'reporter') !== false){
    OutputRoleComment('reporter');

    //尾行結果を表示
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name != $actor) continue; //自分の尾行結果のみ表示
      $target .= ' さんは ' . $wolf_handle;
      OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
    }

    if($day_night == 'night' && $date != 1) CheckNightVote('REPORTER_DO', 'guard-do'); //夜の尾行投票
  }
  elseif(strpos($role, 'common') !== false){
    OutputRoleComment('common');

    //仲間を表示
    OutputPartner("role LIKE 'common%' AND uname <> '$uname'", 'common_partner');
  }
  elseif(strpos($role, 'child_fox') !== false){
    // OutputRoleComment('child_fox');
    echo '[役割]<br>　あなたは「子狐」です。占われても死にませんが、人狼に襲われると死んでしまいます。<br>'."\n";

    //仲間を表示
    OutputPartner("role LIKE '%fox%' AND uname <> '$uname'", 'fox_partner');
  }
  elseif(strpos($role, 'fox') !== false){
    OutputRoleComment('fox');

    //子狐以外の仲間を表示
    OutputPartner("role LIKE 'fox%' AND uname <> '$uname'", 'fox_partner');

    //狐が狙われたメッセージを表示
    $sql = GetAbilityActionResult('FOX_EAT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //自分が狙われた場合のみ表示
      if($handle_name == mysql_result($sql, $i, 0)) OutputAbilityResult('fox_target', NULL);
    }
  }
  elseif(strpos($role, 'poison_cat') !== false){
    // OutputRoleComment('poison_cat');
    echo '[役割]<br>　あなたは「猫又」、毒をもっています。また、死んだ人を誰か一人蘇らせる事ができます。<br>'."\n";

    //蘇生結果を表示
    $action = 'POISON_CAT_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //自分の結果のみ表示
      list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
    }

    //夜の蘇生投票
    if($day_night == 'night' && $date != 1) CheckNightVote('POISON_CAT_DO', 'mania-do');
  }
  elseif(strpos($role, 'poison') !== false) OutputRoleComment('poison');
  elseif(strpos($role, 'pharmacist') !== false){
    // OutputRoleComment('pharmacist');
    echo '[役割]<br>　あなたは「薬師」、処刑対象者に投票していた場合に限りその人を無毒化させることができます。<br>'."\n";

    //解毒結果を表示
    $sql = GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //自分の解毒結果のみ表示する
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'pharmacist_success');
    }
  }
  elseif(strpos($role, 'cupid') !== false){
    OutputRoleComment('cupid');

    //自分が矢を打った恋人 (自分自身含む) を表示する
    $cupid_id = strval($user_no);
    OutputPartner("role LIKE '%lovers[$cupid_id]%'", 'cupid_pair');

    if($day_night == 'night' && $date == 1) CheckNightVote('CUPID_DO', 'cupid-do'); //初日夜の投票
  }
  elseif(strpos($role, 'mania') !== false){
    // OutputRoleComment('mania');
    echo '[役割]<br>　あなたは「神話マニア」です。1日目の夜に指定した人のメイン役職をコピーすることができます (仕様は変更される可能性があります)<br>'."\n";

    if($day_night == 'night') CheckNightVote('MANIA_DO', 'mania-do'); //夜のコピー投票
  }
  elseif(strpos($role, 'quiz') !== false){
    OutputRoleComment('quiz');
    if(strpos($game_option, 'chaos') !== false){
      // OutputRoleComment('quiz_chaos');
      echo '闇鍋モードではあなたの最大の能力である噛み無効がありません。<br>'."\n";
      echo 'はっきり言って無理ゲーなので好き勝手にクイズでも出して遊ぶと良いでしょう。<br>'."\n";
    }
  }

  //ここから兼任役職
  if(strpos($role, 'lovers') !== false){
    //恋人を表示する
    $lovers_str = GetLoversConditionString($role);
    OutputPartner("$lovers_str AND uname <> '$uname'", 'lovers_header', 'lovers_footer');
  }

  if(strpos($role, 'copied') !== false) {
    //コピー結果を表示
    $action = 'MANIA_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //自分の結果のみ表示
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'result_' . $target_role);
    }
  }

  //これ以降はサブ役職非公開オプションの影響を受ける
  if(strpos($game_option, 'secret_sub_role') !== false) return;

  //投票系
  if(    strpos($role, 'authority')    !== false) OutputRoleComment('authority');
  elseif(strpos($role, 'rebel')        !== false) OutputRoleComment('rebel');
  elseif(strpos($role, 'random_voter') !== false) OutputRoleComment('random_voter');
  elseif(strpos($role, 'watcher')      !== false) OutputRoleComment('watcher');
  elseif(strpos($role, 'decide')       !== false); //決定者＆疫病神は通知しない
  elseif(strpos($role, 'plague')       !== false);
  elseif(strpos($role, 'good_luck')    !== false); //幸運＆不運は通知しない
  elseif(strpos($role, 'bad_luck')     !== false);
  elseif(strpos($role, 'upper_luck')   !== false) OutputRoleComment('upper_luck');
  elseif(strpos($role, 'downer_luck' ) !== false) OutputRoleComment('downer_luck');
  elseif(strpos($role, 'random_luck' ) !== false) OutputRoleComment('random_luck');
  elseif(strpos($role, 'star')         !== false) OutputRoleComment('star');
  elseif(strpos($role, 'disfavor')     !== false) OutputRoleComment('disfaver');

  //発言変化系
  if(    strpos($role, 'strong_voice')  !== false) OutputRoleComment('strong_voice');
  elseif(strpos($role, 'normal_voice')  !== false) OutputRoleComment('normal_voice');
  elseif(strpos($role, 'weak_voice')    !== false) OutputRoleComment('weak_voice');
  elseif(strpos($role, 'random_voice')  !== false) OutputRoleComment('random_voice');

  //発言封印系
  if(strpos($role, 'no_last_words') !== false) OutputRoleComment('no_last_words');
  if(strpos($role, 'blinder')       !== false) OutputRoleComment('blinder');
  if(strpos($role, 'earplug')       !== false) OutputRoleComment('earplug');
  if(strpos($role, 'silent')        !== false) OutputRoleComment('silent');

  //発言変換系
  if(strpos($role, 'liar')      !== false) OutputRoleComment('liar');
  if(strpos($role, 'invisible') !== false) OutputRoleComment('invisible');
  if(strpos($role, 'gentleman') !== false) OutputRoleComment('gentleman');
  elseif(strpos($role, 'lady')  !== false) OutputRoleComment('lady');

  //投票ショック死系
  if(    strpos($role, 'chicken')      !== false) OutputRoleComment('chicken');
  elseif(strpos($role, 'rabbit')       !== false) OutputRoleComment('rabbit');
  elseif(strpos($role, 'perverseness') !== false) OutputRoleComment('perverseness');
  elseif(strpos($role, 'flattery')     !== false) OutputRoleComment('flattery');
  elseif(strpos($role, 'impatience')   !== false) OutputRoleComment('impatience');
}

//役職説明を表示する
function OutputRoleComment($role){
  global $ROLE_IMG;
  echo '<img src="' . $ROLE_IMG->$role . '"><br>'."\n";
}

//仲間を表示する
function OutputPartner($query, $header, $footer = NULL){
  global $ROLE_IMG, $room_no;

  $sql = mysql_query("SELECT handle_name FROM user_entry WHERE room_no = $room_no
			AND user_no > 0 AND " . $query);
  $count = mysql_num_rows($sql);
  if($count < 1) return false; //仲間がいなければ表示しない

  echo '<table class="ability-partner"><tr>'."\n";
  echo '<td><img src="' . $ROLE_IMG->$header . '"></td>'."\n";
  echo '<td>　';
  for($i = 0; $i < $count; $i++) echo mysql_result($sql, $i, 0) . 'さん　　';
  echo '</td>'."\n";
  if($footer) echo '<td><img src="' . $ROLE_IMG->$footer . '"></td>'."\n";
  echo '</tr></table>'."\n";
}

//能力発動結果をデータベースに問い合わせる
function GetAbilityActionResult($action){
  global $room_no, $date;

  $yesterday = $date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $yesterday AND type = '$action'");
}
//能力発動結果を表示する
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td><img src="' . $ROLE_IMG->$header . '"></td>'."\n";
  if($target) echo '<td>' . $target . '</td>';
  if($footer) echo '<td><img src="' . $ROLE_IMG->$footer . '"></td>'."\n";
  echo '</tr></table>'."\n";
}

//自分の未投票チェック
function CheckSelfVote(){
  global $room_no, $date, $uname;

  //投票回数を取得(再投票なら $vote_times は増える)
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND type = 'VOTE_TIMES' AND date = $date");
  $vote_times = (int)mysql_result($sql, 0, 0);
  echo '<div class="self-vote">投票 ' . $vote_times . ' 回目：';

  //投票済みかどうか
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  echo (mysql_result($sql, 0, 0) ? '投票済み' : 'まだ投票していません') . '</div>'."\n";
}

//夜の未投票チェック
function CheckNightVote($action, $class){
  global $MESSAGE, $room_no, $uname;

  $query = "SELECT uname FROM vote WHERE room_no = $room_no "; //共有クエリ
  if($action != 'WOLF_EAT') $query .= "AND uname = '$uname' "; //人狼は誰でも OK
  $sql = mysql_query($query . "AND situation = '$action'");

  if(mysql_num_rows($sql) != 0) return false; //投票済みならメッセージを表示しない
  $class_str   = 'ability-' . $class; //クラス名はアンダースコアを使わないでおく
  $message_str = 'ability_' . strtolower($action);
  echo '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}

//自分の遺言を出力
function OutputSelfLastWords(){
  global $room_no, $day_night, $uname;

  //ゲーム終了後は表示しない
  if($day_night == 'aftergame') return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");

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
