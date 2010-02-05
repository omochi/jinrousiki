<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'TIME_CONF', 'ROOM_IMG');

EncodePostData(); //ポストされた文字列を全てエンコードする

$RQ_ARGS =& new RequestGamePlay(); //引数を取得
if($RQ_ARGS->play_sound) $INIT_CONF->LoadClass('SOUND', 'COOKIE'); //音でお知らせ

$DB_CONF->Connect(); //DB 接続
session_start(); //セッション開始
$uname = CheckSession(session_id()); //セッション ID をチェック

$ROOM =& new Room($RQ_ARGS); //村情報をロード
$ROOM->dead_mode    = $RQ_ARGS->dead_mode; //死亡者モード
$ROOM->heaven_mode  = $RQ_ARGS->heaven_mode; //霊話モード
$ROOM->system_time  = TZTime(); //現在時刻を取得
$ROOM->sudden_death = 0; //突然死実行までの残り時間

//シーンに応じた追加クラスをロード
if($ROOM->IsBeforeGame()){
  $INIT_CONF->LoadClass('CAST_CONF', 'ROOM_IMG', 'GAME_OPT_MESS'); //ゲームオプション表示用
}
elseif($ROOM->IsFinished()){
  $INIT_CONF->LoadClass('VICT_MESS'); //勝敗結果表示用
}

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->ByUname($uname); //自分の情報をロード

//必要なクッキーをセットする
$objection_array = array(); //SendCookie();で格納される・異議ありの情報
$objection_left_count = 0;  //SendCookie();で格納される・異議ありの残り回数
SendCookie();

//発言の有無をチェック
ConvertSay(&$RQ_ARGS->say); //発言置換処理

if($RQ_ARGS->say == ''){
  CheckSilence(); //発言が空ならゲーム停滞のチェック(沈黙、突然死)
}
elseif($RQ_ARGS->IsLastWords() && $SELF->IsLive() && ! $SELF->IsDummyBoy()){
  EntryLastWords($RQ_ARGS->say);  //生きていれば遺言登録
}
elseif($SELF->IsDead() || $SELF->IsDummyBoy() || $SELF->last_load_day_night == $ROOM->day_night){
  Say($RQ_ARGS->say); //死んでいる or 身代わり君 or ゲームシーンが一致しているなら書き込む
}
else{
  CheckSilence(); //発言ができない状態ならゲーム停滞チェック
}

if($SELF->last_load_day_night != $ROOM->day_night){ //ゲームシーンを更新
  $SELF->Update('last_load_day_night', $ROOM->day_night);
}

OutputGamePageHeader(); //HTMLヘッダ
OutputGameHeader(); //部屋のタイトルなど

if(! $ROOM->heaven_mode){
  if(! $RQ_ARGS->list_down) OutputPlayerList(); //プレイヤーリスト
  OutputAbility(); //自分の役割の説明
  if($ROOM->IsDay() && $SELF->IsLive()) CheckSelfVoteDay(); //昼の投票済みチェック
  OutputRevoteList(); //再投票の時、メッセージを表示する
}

//会話ログを出力
if($SELF->IsDead() && $ROOM->heaven_mode)
  OutputHeavenTalkLog();
else
  OutputTalkLog();

if(! $ROOM->heaven_mode){
  if($SELF->IsDead()) OutputAbilityAction(); //能力発揮
  OutputLastWords(); //遺言
  OutputDeadMan();   //死亡者
  OutputVoteList();  //投票結果
  if(! $ROOM->dead_mode) OutputSelfLastWords(); //自分の遺言
  if($RQ_ARGS->list_down) OutputPlayerList(); //プレイヤーリスト
}
OutputHTMLFooter();

//-- 関数 --//
//必要なクッキーをまとめて登録(ついでに最新の異議ありの状態を取得して配列に格納)
function SendCookie(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $SELF, $objection_array, $objection_left_count;

  //<夜明けを音でお知らせ用>
  //クッキーに格納 (夜明けに音でお知らせで使う・有効期限一時間)
  setcookie('day_night', $ROOM->day_night, $ROOM->system_time + 3600);

  //-- 「異議」ありを音でお知らせ用 --//
  //今までに自分が「異議」ありをした回数を取得
  $query = "SELECT COUNT(message) FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND type = 'OBJECTION' AND message = '{$SELF->user_no}'";
  $self_objection_count = FetchResult($query);

  //生きていて(ゲーム終了後は死者でもOK)「異議」あり、のセット要求があればセットする(最大回数以内の場合)
  if($SELF->IsLive() && ! $ROOM->IsNight() && $RQ_ARGS->set_objection &&
     $self_objection_count < $GAME_CONF->objection){
    InsertSystemMessage($SELF->user_no, 'OBJECTION');
    InsertSystemTalk('OBJECTION', $ROOM->system_time, '', '', $SELF->uname);
    mysql_query('COMMIT');
  }

  //ユーザ総数を取得して人数分の「異議あり」のクッキーを構築する
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} AND user_no > 0";
  $user_count = FetchResult($query);
  // 配列をリセット (0 番目に変な値が入らない事が保証されていれば不要かな？)
  // キックで欠番が出ると色々面倒な事になりそう
  // $objection_array = array();
  // unset($objection_array[0]);
  $objection_array = array_fill(0, $user_count, 0); //index は 0 から

  //message:異議ありをしたユーザ No とその回数を取得
  $query = "SELECT message, COUNT(message) AS message_count FROM system_message " .
    "WHERE room_no = {$ROOM->id} AND type = 'OBJECTION' GROUP BY message";
  $array = FetchAssoc($query);
  foreach($array as $this_array){
    $this_user_no = (int)$this_array['message'];
    $this_count   = (int)$this_array['message_count'];
    $objection_array[$this_user_no - 1] = $this_count;
  }

  //クッキーに格納 (有効期限一時間)
  foreach($objection_array as $value){
    if($str != '') $str .= ','; //カンマ区切り
    $str .= $value;
  }
  setcookie('objection', $str, $ROOM->system_time + 3600);

  //残り異議ありの回数
  $objection_left_count = $GAME_CONF->objection - $objection_array[$SELF->user_no - 1];

  //<再投票を音でお知らせ用>
  //再投票の回数を取得
  if(($last_vote_times = GetVoteTimes(true)) > 0){ //クッキーに格納 (有効期限一時間)
    setcookie('vote_times', $last_vote_times, $ROOM->system_time + 3600);
  }
  else{ //クッキーから削除 (有効期限一時間)
    setcookie('vote_times', '', $ROOM->system_time - 3600);
  }
}

//発言置換処理
function ConvertSay(&$say){
  global $GAME_CONF, $MESSAGE, $ROOM, $USERS, $SELF;

  //リロード時、死者、ゲームプレイ中以外なら処理スキップ
  if($say == '' || $SELF->IsDead() || ! $ROOM->IsPlaying()) return false;
  #if($say == '' || $SELF->IsDead()) return false; //テスト用

  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  //萌狼・萌狐・不審者は一定確率で発言が遠吠え(デフォルト時)になる
  if($virtual_self->IsRole('cute_wolf', 'cute_fox', 'suspect') &&
     mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate){
    $say = ($MESSAGE->cute_wolf != '' ? $MESSAGE->cute_wolf : $MESSAGE->wolf_howl);
  }
  //紳士・淑女は一定確率で発言が入れ替わる
  elseif($virtual_self->IsRole('gentleman', 'lady') &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){
    $role_name = ($virtual_self->IsRole('gentleman') ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $target_list = array();
    foreach($USERS->rows as $user){ //自分以外の生存者の HN を取得
      if(! $user->IsSelf() && $user->IsLive()) $target_list[] = $user->handle_name;
    }
    $say = $MESSAGE->$message_header . GetRandom($target_list) . $MESSAGE->$message_footer;
  }
  //狼少年は一定確率で発言内容が反転される
  elseif($virtual_self->IsRole('liar') && mt_rand(1, 100) <= $GAME_CONF->liar_rate){
    $say = strtr($say, $GAME_CONF->liar_replace_list);
  }

  if($virtual_self->IsRole('grassy')){ //草原迷彩の処理
    $convert_say = '';
    $count = mb_strlen($say);
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      $convert_say .= $this_str;
      if($this_str != "\n") $convert_say .= 'w ';
    }
    $say = $convert_say;
  }

  if($virtual_self->IsRole('invisible')){ //光学迷彩の処理
    $convert_say = '';
    $count = mb_strlen($say);
    $rate = $GAME_CONF->invisible_rate;
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n" || $this_str == "\t" || $this_str == ' ' || $this_str == '　'){
	$convert_say .= $this_str;
	continue;
      }
      if(mt_rand(1, 100) <= $rate)
	$convert_say .= (strlen($this_str) == 2 ? '　' : '&nbsp;');
      else
	$convert_say .= $this_str;
      if($rate++ > 100) break;
    }
    $say = $convert_say;
  }

  //虹色迷彩の処理
  if($virtual_self->IsRole('rainbow')) $say = strtr($say, $GAME_CONF->rainbow_replace_list);

  //七曜迷彩の処理
  if($virtual_self->IsRole('weekly')) $say = strtr($say, $GAME_CONF->weekly_replace_list);

  if($virtual_self->IsRole('silent') && mb_strlen($say) > $GAME_CONF->silent_length){ //無口の処理
    $say = mb_substr($say, 0, $GAME_CONF->silent_length) . '……';
  }

  //草刈りの処理
  if($virtual_self->IsRole('mower')) $say = strtr($say, array('w' => '', 'ｗ' => '', 'W' => '', 'Ｗ' => ''));

  if($virtual_self->IsRole('side_reverse')){ //鏡迷彩の処理
    $convert_say = '';
    $count = mb_strlen($say);
    $this_line = array();
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n"){
	if(count($this_line) > 0){
	  $this_line = array_reverse($this_line);
	  foreach($this_line as $this_line_str) $convert_say .= $this_line_str;
	}
	$convert_say .= $this_str;
	$this_line = array();
      }
      else{
	$this_line[] = $this_str;
      }
    }
    if(count($this_line) > 0){
      $this_line = array_reverse($this_line);
      foreach($this_line as $this_line_str) $convert_say .= $this_line_str;
    }
    $say = $convert_say;
  }

  if($virtual_self->IsRole('line_reverse')){ //天地迷彩の処理
    $convert_say = '';
    while(($line_break = mb_strrpos($say, "\n")) !== false){
      $convert_say .= mb_substr($say, $line_break + 1);
      $say = mb_substr($say, 0, $line_break);
      if($say != '') $convert_say .= "\n";
    }
    if($convert_say != '') $say = $convert_say . $say;
  }
}

//遺言登録
function EntryLastWords($say){
  global $ROOM, $SELF;

  //ゲーム終了後、死者、ブン屋、筆不精なら登録しない
  if($ROOM->IsFinished() || ! $SELF->IsLive() || $SELF->IsRole('reporter', 'no_last_words')){
    return false;
  }
  $SELF->Update('last_words', $say); //遺言を残す
}

//発言
function Say($say){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  if($ROOM->IsRealTime()){ //リアルタイム制
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

  if(! $ROOM->IsPlaying()){ //ゲーム開始前後はそのまま発言
    Write($say, $ROOM->day_night, 0, true);
  }
  //身代わり君 (仮想 GM 対応) は遺言を専用のシステムメッセージに切り替え
  elseif($SELF->IsDummyBoy() && $RQ_ARGS->IsLastWords()){
    Write($say, "{$ROOM->day_night} dummy_boy", 0); //発言時間を更新しない
  }
  elseif($SELF->IsDead()){ //死亡者の霊話
    Write($say, 'heaven', 0); //発言時間を更新しない
  }
  elseif($SELF->IsLive() && $left_time > 0){ //生存者で制限時間内
    if($ROOM->IsDay()){ //昼はそのまま発言
      Write($say, 'day', $spend_time, true);
    }
    elseif($ROOM->IsNight()){ //夜は役職毎に分ける
      if($virtual_self->IsWolf(true)) //人狼
	Write($say, 'night wolf', $spend_time, true);
      elseif($virtual_self->IsRole('whisper_mad')) //囁き狂人
	Write($say, 'night mad', 0);
      elseif($virtual_self->IsRole('common')) //共有者
	Write($say, 'night common', 0);
      elseif($virtual_self->IsFox(true)) //妖狐
	Write($say, 'night fox', 0);
      else //独り言
	Write($say, 'night self_talk', 0);
    }
  }
}

//発言を DB に登録する
function Write($say, $location, $spend_time, $update = false){
  global $MESSAGE, $RQ_ARGS, $ROOM, $SELF;

  //声の大きさを決定
  $voice = $RQ_ARGS->font_type;
  if($SELF->IsLive() && $ROOM->IsPlaying()){
    $voice_list = array('strong', 'normal', 'weak');
    if(    $SELF->IsRole('strong_voice'))  $voice = 'strong';
    elseif($SELF->IsRole('normal_voice'))  $voice = 'normal';
    elseif($SELF->IsRole('weak_voice'))    $voice = 'weak';
    elseif($SELF->IsRole('inside_voice'))  $voice = ($ROOM->IsDay() ? 'weak' : 'strong');
    elseif($SELF->IsRole('outside_voice')) $voice = ($ROOM->IsDay() ? 'strong' : 'weak');
    elseif($SELF->IsRole('upper_voice')){
      $voice_key = array_search($voice, $voice_list);
      if($voice_key == 0) $say = $MESSAGE->howling;
      else $voice = $voice_list[$voice_key - 1];
    }
    elseif($SELF->IsRole('downer_voice')){
      $voice_key = array_search($voice, $voice_list);
      if($voice_key >= count($voice_list) - 1) $say = $MESSAGE->common_talk;
      else $voice = $voice_list[$voice_key + 1];
    }
    elseif($SELF->IsRole('random_voice')){
      $voice = GetRandom($voice_list);
    }
  }

  InsertTalk($ROOM->id, $ROOM->date, $location, $SELF->uname, $ROOM->system_time,
	     $say, $voice, $spend_time);
  if($update) UpdateTime();
  mysql_query('COMMIT'); //一応コミット
}

//ゲーム停滞のチェック
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $ROOM, $USERS;

  //ゲーム中以外は処理をしない
  if(! $ROOM->IsPlaying()) return false;

  //テーブルロック
  $query = 'LOCK TABLES room WRITE, talk WRITE, vote WRITE, user_entry WRITE, system_message WRITE';
  if(! mysql_query($query)) return false;

  //最後に発言された時間を取得
  $last_updated_time = FetchResult("SELECT last_updated FROM room WHERE room_no = {$ROOM->id}");
  $last_updated_pass_time = $ROOM->system_time - $last_updated_time;

  //経過時間を取得
  if($ROOM->IsRealTime()) //リアルタイム制
    GetRealPassTime(&$left_time);
  else //会話で時間経過制
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //リアルタイム制でなく、制限時間内で沈黙閾値を超えたならなら一時間進める(沈黙)
  if(! $ROOM->IsRealTime() && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '・・・・・・・・・・ ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      InsertTalk($ROOM->id, $ROOM->date, "{$ROOM->day_night} system", 'system',
		 $ROOM->system_time, $sentence, NULL, $TIME_CONF->silence_pass);
      UpdateTime();
    }
  }
  elseif($left_time == 0){ //制限時間を過ぎていたら警告を出す
    //突然死発動までの時間を取得
    $left_time_str = ConvertTime($TIME_CONF->sudden_death); //表示用に変換
    $sudden_death_announce = 'あと' . $left_time_str . 'で' . $MESSAGE->sudden_death_announce;

    //既に警告を出しているかチェック
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$ROOM->id} " .
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
      $query_live = "SELECT uname FROM user_entry WHERE room_no = {$ROOM->id} " .
	"AND live = 'live' AND user_no > 0";

      //投票済みの人を取得するための基本 SQL 文
      $query_vote = "SELECT uname FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} AND ";

      if($ROOM->IsDay()){
	//投票回数を取得
	$vote_times = GetVoteTimes();

	//投票済みの人のユーザ名を取得
	$add_action = "situation = 'VOTE_KILL' AND vote_times = $vote_times";
	$vote_uname_list = FetchArray($query_vote . $add_action);

	//投票が必要な人のユーザ名を取得
	$live_uname_list = FetchArray($query_live);

	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);
      }
      elseif($ROOM->IsNight()){
	//対象役職のデータを作成
	$action_list = array('MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO', 'VOODOO_MAD_DO',
			     'VOODOO_FOX_DO', 'CHILD_FOX_DO');
	$actor_list  = array('%mage', 'voodoo_killer', 'jammer_mad', 'voodoo_mad',
			     'voodoo_fox', 'child_fox');

	if($ROOM->date == 1){
	  array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
	  array_push($actor_list, 'mind_scanner', '%cupid', '%mania');
	}
	else{
	  array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'DREAM_EAT',
		     'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
	  array_push($actor_list, '%guard', 'anti_voodoo', 'reporter', 'dream_eater_mad',
		     'assassin', 'trap_mad');
	  if(! $ROOM->IsOpenCast()){
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

	//未投票の人を取得
	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);

	if(FetchCount($query_vote . "situation = 'WOLF_EAT'") < 1){ //人狼の投票を確認
	  $novote_uname_list = array_merge($novote_uname_list, $USERS->GetLivingWolves());
	}
      }

      //未投票者を全員突然死させる
      foreach($novote_uname_list as $uname){
	$USERS->SuddenDeath($USERS->ByUname($uname)->user_no);
      }
      LoversFollowed(true);
      InsertMediumMessage();

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
  global $GAME_CONF, $TIME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF,
    $COOKIE, $SOUND, $objection_array, $objection_left_count;

  $room_message = '<td class="room"><span>' . $ROOM->name . '村</span>　〜' . $ROOM->comment .
    '〜[' . $ROOM->id . '番地]</td>'."\n";
  $url_room   = '?room_no=' . $ROOM->id;
  $url_reload = ($RQ_ARGS->auto_reload > 0 ? '&auto_reload=' . $RQ_ARGS->auto_reload : '');
  $url_sound  = ($RQ_ARGS->play_sound ? '&play_sound=on'  : '');
  $url_list   = ($RQ_ARGS->list_down  ? '&list_down=on'   : '');
  $url_dead   = ($ROOM->dead_mode     ? '&dead_mode=on'   : '');
  $url_heaven = ($ROOM->heaven_mode   ? '&heaven_mode=on' : '');
  $real_time  = $ROOM->IsRealTime();

  echo '<table class="game-header"><tr>'."\n";
  if(($SELF->IsDead() && $ROOM->heaven_mode) || $ROOM->IsAfterGame()){ //霊界とログ閲覧時
    if($SELF->IsDead() && $ROOM->heaven_mode)
      echo '<td>&lt;&lt;&lt;幽霊の間&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //過去の日のログへのリンク生成
    echo '<td class="view-option">ログ';

    $url_header ='<a href="game_log.php' . $url_room . '&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '0&day_night=beforegame' . $url_footer . '0(開始前)</a>'."\n";
    echo $url_header . '1' . $url_night . '1(夜)</a>'."\n";
    for($i = 2; $i < $ROOM->date; $i++){
      echo $url_header . $i . $url_day   . $i . '(昼)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(夜)</a>'."\n";
    }
    if($ROOM->IsNight() && $ROOM->heaven_mode){
      echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(昼)</a>'."\n";
    }
    elseif($ROOM->IsAfterGame()){
      $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$ROOM->id} " .
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
    if($SELF->IsDead() && $ROOM->dead_mode){ //死亡者の場合の、真ん中の全表示地上モード
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="更新">
</form>

EOF;
    }
  }

  if(! $ROOM->IsAfterGame()){ //ゲーム終了後は自動更新しない
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
    if($COOKIE->day_night != $ROOM->day_night && $ROOM->IsDay()) $SOUND->Output('morning');

    //異議あり、を音で知らせる
    $cookie_objection_array = explode(',', $COOKIE->objection); //クッキーの値を配列に格納する
    $count = count($objection_array);
    for($i = 0; $i < $count; $i++){ //差分を計算 (index は 0 から)
      //差分があれば性別を確認して音を鳴らす
      if((int)$objection_array[$i] > (int)$cookie_objection_array[$i]){
	$SOUND->Output('objection_' . $USERS->ByID($i + 1)->sex, true);
      }
    }
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

  if($ROOM->IsBeforeGame()) OutputGameOption(); //ゲームオプションを説明
  echo '<table class="time-table"><tr>'."\n";
  if(! $ROOM->IsAfterGame()){ //ゲーム終了後以外なら、サーバとの時間ズレを表示
    $date_str = TZDate('Y, m, j, G, i, s', $ROOM->system_time);
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

  if($ROOM->IsBeforeGame()){
    echo '<td class="real-time">';
    if($real_time){ //実時間の制限時間を取得
      sscanf(strstr($ROOM->game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
      echo "設定時間： 昼 <span>{$day_minutes}分</span> / 夜 <span>{$night_minutes}分</span>";
    }
    echo '　突然死：<span>' . ConvertTime($TIME_CONF->sudden_death) . '</span></td>';
  }
  if($ROOM->IsPlaying()){
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
  if($ROOM->IsBeforeGame() ||
     ($ROOM->IsDay() && ! $ROOM->dead_mode && ! $ROOM->heaven_mode && $left_time > 0)){
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

  if($ROOM->IsPlaying() && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($ROOM->sudden_death > 0){
      echo $MESSAGE->sudden_death_time . ConvertTime($ROOM->sudden_death) . '<br>'."\n";
    }
  }
}

//天国の霊話ログ出力
function OutputHeavenTalkLog(){
  global $ROOM;

  //出力条件をチェック
  // if($SELF->IsDead()) return false; //呼び出し側でチェックするので現在は不要

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
			WHERE talk.room_no = {$ROOM->id}
			AND talk.location LIKE 'heaven'
			AND ( (user_entry.room_no = {$ROOM->id} AND user_entry.uname = talk.uname
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
    if($ROOM->IsOpenCast()) $talk_handle .= '<span>(' . $talk_uname . ')</span>';

    //会話出力
    echo '<tr class="user-talk">'."\n";
    echo '<td class="user-name"><font color="' . $talk_color . '">◆</font>' .
      $talk_handle . '</td>'."\n";
    echo '<td class="say ' . $font_type . '">' . $sentence . '</td>'."\n";
    echo '</tr>'."\n";
  }
  echo '</table>'."\n";
}

//昼の自分の未投票チェック
function CheckSelfVoteDay(){
  global $MESSAGE, $ROOM, $USERS, $SELF;

  //投票回数を取得
  $vote_times = GetVoteTimes();
  $sentence = '<div class="self-vote">投票 ' . $vote_times . ' 回目：';

  //投票対象者を取得
  $query = "SELECT target_uname FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  $target_uname = FetchResult($query);
  $sentence .= ($target_uname === false ? '<font color="red">まだ投票していません</font>' :
		$USERS->GetHandleName($target_uname, true) . 'さんに投票済み');
  $sentence .= '</div>'."\n";
  if($target_uname === false){
    $sentence .= '<span class="ability vote">' . $MESSAGE->ability_vote . '</span><br>'."\n";
  }
  echo $sentence;
}

//自分の遺言を出力
function OutputSelfLastWords(){
  global $ROOM, $SELF;

  //ゲーム終了後は表示しない
  if($ROOM->IsAfterGame()) return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = {$ROOM->id}
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
