<?php
//HTMLヘッダー出力
function OutputGamePageHeader(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $SELF;

  //引数を格納
  $url_header = 'game_frame.php?room_no=' . $ROOM->id . '&auto_reload=' . $RQ_ARGS->auto_reload;
  if($RQ_ARGS->play_sound) $url_header .= '&play_sound=on';
  if($RQ_ARGS->list_down)  $url_header .= '&list_down=on';

  $title = '汝は人狼なりや？ [プレイ]';
  $anchor_header = '<br>'."\n";
  if(preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){ //MAC かどうか判別
    $sentence = '';  //MAC は JavaScript でエラー？
    $anchor_header .= '<a href="';
    $anchor_footer = '">ここをクリックしてください</a>';
  }
  else{
    $sentence = '<script type="text/javascript"><!--'."\n" .
      'if(top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $anchor_header .= '切り替わらないなら <a href="';
    $anchor_footer = '" target="_top">ここ</a>';
  }

  //ゲーム中、死んで霊話モードに行くとき
  if(! $ROOM->IsAfterGame() && $SELF->IsDead() && ! $ROOM->log_mode &&
     ! $ROOM->dead_mode && ! $ROOM->heaven_mode){
    $jump_url =  $url_header . '&dead_mode=on';
    $sentence .= '天国モードに切り替えます。';
  }
  elseif($ROOM->IsAfterGame() && $ROOM->dead_mode){ //ゲームが終了して霊話から戻るとき
    $jump_url = $url_header;
    $sentence .= 'ゲーム終了後のお部屋に飛びます。';
  }
  elseif($SELF->IsLive() && ($ROOM->dead_mode || $ROOM->heaven_mode)){
    $jump_url = $url_header;
    $sentence .= 'ゲーム画面に飛びます。';
  }

  if($jump_url != ''){ //移動先が設定されていたら画面切り替え
    $sentence .= $anchor_header . $jump_url . $anchor_footer;
    OutputActionResult($title, $sentence, $jump_url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  if(! $ROOM->log_mode){ //過去ログ閲覧時は不要
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load = "change_css('{$ROOM->day_night}');";
  }

  if($RQ_ARGS->auto_reload != 0 && ! $ROOM->IsAfterGame()){ //自動リロードをセット
    echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
  }

  //ゲーム中、リアルタイム制なら経過時間を Javascript でリアルタイム表示
  if($ROOM->IsPlaying() && $ROOM->IsRealTime() && ! $ROOM->heaven_mode && ! $ROOM->log_mode){
    list($start_time, $end_time) = GetRealPassTime(&$left_time, true);
    $on_load .= 'output_realtime();';
    OutputRealTimer($start_time, $end_time);
  }
  echo '</head>'."\n";
  echo '<body onLoad="' . $on_load . '">'."\n";
  echo '<a name="#game_top"></a>'."\n";
}

//リアルタイム表示に使う JavaScript の変数を出力
function OutputRealTimer($start_time, $end_time){
  global $ROOM;

  echo '<script type="text/javascript" src="javascript/output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var realtime_message = "　' . ($ROOM->IsDay() ? '日没' : '夜明け') . 'まで ";'."\n";
  echo 'var start_time = "' . $start_time . '";'."\n";
  echo 'var end_time = "'   . $end_time   . '";'."\n";
  echo '// --></script>'."\n";
}

//自動更新のリンクを出力
function OutputAutoReloadLink($url){
  global $GAME_CONF, $RQ_ARGS;

  $str = '[自動更新](' . $url . '0">' . ($RQ_ARGS->auto_reload == 0 ? '【手動】' : '手動') . '</a>';
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '秒';
    $value = ($RQ_ARGS->auto_reload == $time ? '【' . $name . '】' : $name);
    $str .= ' ' . $url . $time . '">' . $value . '</a>';
  }
  echo $str . ')'."\n";
}

//ゲームオプション画像を出力
function OutputGameOption(){
  global $GAME_CONF, $ROOM;

  $array = FetchNameArray("SELECT option_role, max_user FROM room WHERE room_no = {$ROOM->id}");
  $str = '<table class="time-table"><tr>'."\n" .
    '<td>ゲームオプション：' . MakeGameOptionImage($ROOM->game_option, $array['option_role']) .
    ' 最大' . $array['max_user'] . '人</td>'."\n" . '</tr></table>'."\n";
  echo $str;
}

//日付と生存者の人数を出力
function OutputTimeTable(){
  global $ROOM;

  if($ROOM->IsBeforeGame()) return false; //ゲームが始まっていなければ表示しない

  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} " .
    "AND live = 'live' AND user_no > 0";
  echo '<td>' . $ROOM->date . ' 日目<span>(生存者' . FetchResult($query) . '人)</span></td>'."\n";
}

//プレイヤー一覧出力
function OutputPlayerList(){
  global $DEBUG_MODE, $GAME_CONF, $ICON_CONF, $ROOM, $USERS, $SELF;

  //アイコンの設定を取得
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  //ブラウザをチェック (MSIE @ Windows だけ 画像の Alt, Title 属性で改行できる)
  //IE の場合改行を \r\n に統一、その他のブラウザはスペースにする(画像のAlt属性)
  $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');

  $count = 0; //改行カウントを初期化

  //配役公開フラグを設定
  $is_open_role = ($ROOM->IsAfterGame() || $SELF->IsDummyBoy() ||
		   ($SELF->IsDead() && $ROOM->IsOpenCast()));

  //ゲーム開始投票チェック用クエリ
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
    "AND situation = 'GAMESTART' AND uname = ";

  $str = '<div class="player"><table cellspacing="5"><tr>'."\n";
  foreach($USERS->rows as $user_no => $user){
    if($count > 0 && ($count % 5) == 0) $str .= "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    //ゲーム開始投票をしていたら背景色を変える
    $td_header = '<td>';
    if($ROOM->IsBeforeGame() &&
       (($user->IsDummyBoy() && ! $ROOM->IsQuiz()) ||
	FetchResult($query . "'{$user->uname}'") > 0)){
      $td_header = '<td class="already-vote">';
    }

    //ユーザプロフィールと枠線の色を追加
    $profile = str_replace("\n", $replace, $user->profile);
    $str .= $td_header . '<img title="' . $profile . '" alt="' . $profile .
      '" style="border-color: ' . $user->color . ';"';

    //生死情報に応じたアイコンを設定
    $path = $ICON_CONF->path . '/' . $user->icon_filename;
    if($ROOM->IsBeforeGame() || $USERS->IsVirtualLive($user_no)){
      $live = '(生存中)';
    }
    else{
      $live = '(死亡)';
      $str .= ' onMouseover="this.src=' . "'$path'" . '"'; //元のアイコン

      $path = $ICON_CONF->dead; //アイコンを死亡アイコンに入れ替え
      $str .= ' onMouseout="this.src=' . "'$path'" . '"';
    }
    $str .= ' width="' . $width . '" height="' . $height . '" src="' . $path . '"></td>'."\n";

    //HN を追加
    $str .= $td_header . '<font color="' . $user->color . '">◆</font>' . $user->handle_name;
    if($DEBUG_MODE) $str .= ' (' . $user_no . ')';
    $str .= '<br>'."\n";

    //ゲーム終了後・死亡後＆霊界役職公開モードなら、役職・ユーザネームも表示
    if($is_open_role){
      $str .= '　(' . $user->uname; //ユーザ名を追加

      //憑依状態なら憑依しているユーザを追加
      $real_user = $USERS->ByReal($user_no);
      if(! $real_user->IsSameID($user_no) && $real_user->IsLive()){
	$str .= '<br>[' . $real_user->uname . ']';
      }
      $str .= ')<br>';

      //メイン役職を追加
      if($user->IsRole('human', 'suspect', 'unconscious'))
	$str .= MakeRoleName($user->main_role, 'human');
      elseif($user->IsRoleGroup('mage') || $user->IsRole('voodoo_killer'))
	$str .= MakeRoleName($user->main_role, 'mage');
      elseif($user->IsRoleGroup('necromancer', 'priest') || $user->IsRole('medium'))
	$str .= MakeRoleName($user->main_role, 'necromancer');
      elseif($user->IsRoleGroup('guard') || $user->IsRole('reporter', 'anti_voodoo'))
	$str .= MakeRoleName($user->main_role, 'guard');
      elseif($user->IsRoleGroup('common'))
	$str .= MakeRoleName($user->main_role, 'common');
      elseif($user->IsRole('mind_scanner'))
	$str .= MakeRoleName($user->main_role, 'mind');
      elseif($user->IsRoleGroup('jealousy'))
	$str .= MakeRoleName($user->main_role, 'jealousy');
      elseif($user->IsRoleGroup('mania'))
	$str .= MakeRoleName($user->main_role, 'mania');
      elseif($user->IsRole('assassin', 'quiz'))
	$str .= MakeRoleName($user->main_role);
      elseif($user->IsRoleGroup('wolf'))
	$str .= MakeRoleName($user->main_role, 'wolf');
      elseif($user->IsRoleGroup('mad'))
	$str .= MakeRoleName($user->main_role, 'mad');
      elseif($user->IsRoleGroup('fox'))
	$str .= MakeRoleName($user->main_role, 'fox');
      elseif($user->IsRoleGroup('chiroptera'))
	$str .= MakeRoleName($user->main_role, 'chiroptera');
      elseif($user->IsRoleGroup('cupid'))
	$str .= MakeRoleName($user->main_role, 'cupid');
      elseif($user->IsRoleGroup('poison') || $user->IsRole('pharmacist'))
	$str .= MakeRoleName($user->main_role, 'poison');

      if(($role_count = count($user->role_list)) > 1){ //兼任役職の表示
	$display_role_count = 1;
	foreach($GAME_CONF->sub_role_group_list as $class => $role_list){
	  foreach($role_list as $sub_role){
	    if($user->IsRole($sub_role)){
	      $str .= MakeRoleName($sub_role, $class, true);
	      if(++$display_role_count >= $role_count) break 2;
	    }
	  }
	}
      }

      $str .= '<br>'."\n";
    }
    $str .= $live . '</td>'."\n";
  }
  echo $str . '</tr></table></div>'."\n";
}

//役職名のタグを作成する //game_format.php に似たような関数があるかな？
function MakeRoleName($role, $css = '', $sub_role = false){
  global $GAME_CONF;

  $str = '';
  if($css == '') $css = $role;
  if($sub_role) $str .= '<br>';
  $str .= '<span class="' . $css . '">[';
  if($sub_role) $str .= $GAME_CONF->sub_role_list[$role];
  else $str .= $GAME_CONF->main_role_list[$role];
  $str .= ']</span>';

  return $str;
}

//勝敗の出力
function OutputVictory(){
  global $MESSAGE, $ROOM, $USERS, $SELF;

  //-- 村の勝敗結果 --//
  $victory = FetchResult("SELECT victory_role FROM room WHERE room_no = {$ROOM->id}");
  $class   = $victory;
  $winner  = 'victory_' . $victory;

  switch($victory){ //特殊ケース対応
    //狐勝利系
    case 'fox1':
    case 'fox2':
      $class = 'fox';
      break;

    //引き分け系
    case 'draw': //引き分け
    case 'vanish': //全滅
    case 'quiz_dead': //クイズ村 GM 死亡
      $class = 'none';
      break;

    case NULL: //廃村
      $class  = 'none';
      $winner = 'victory_none';
      break;
  }
  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$MESSAGE->$winner}</td>
</tr></table>

EOF;

  //-- 個々の勝敗結果 --//
  //勝敗未決定、観戦モード、ログ閲覧モードなら非表示
  if(is_null($victory) || $ROOM->view_mode || $ROOM->log_mode) return;

  $result = 'win';
  $target_user = $SELF;
  while($target_user->IsRole('unknown_mania')){
    if(! is_array($target_user->partner_list['unknown_mania'])) break;
    $target_user = $USERS->ByID($target_user->partner_list['unknown_mania'][0]);
    if($target_user->IsSelf()) break;
  }
  $camp = $target_user->DistinguishCamp(); //所属陣営を取得

  if($victory == 'draw' || $victory == 'vanish'){ //引き分け系
    $class  = 'none';
    $result = 'draw';
  }
  elseif($victory == 'quiz_dead'){ //出題者死亡
    $class  = 'none';
    $result = ($camp == 'quiz' ? 'lose' : 'draw');
  }
  elseif($camp == 'chiroptera' && $SELF->IsLive()){ //蝙蝠陣営は生きていれば勝利
    $class = 'chiroptera';
  }
  else{
    if($SELF->IsLovers()) $camp = 'lovers'; //恋人なら所属陣営を上書き

    if($victory == 'human' && $camp == 'human')
      $class = 'human';
    elseif($victory == 'wolf' && $camp == 'wolf')
      $class = 'wolf';
    elseif(strpos($victory, 'fox') !== false && $camp == 'fox')
      $class = 'fox';
    elseif($victory == 'lovers' && $camp == 'lovers')
      $class = 'lovers';
    elseif($victory == 'quiz' && $camp == 'quiz')
      $class = 'quiz';
    else{
      $class  = 'none';
      $result = 'lose';
    }
  }

  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$MESSAGE->$result}</td>
</tr></table>

EOF;
}

//再投票の時、メッセージを表示
function OutputReVoteList(){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $SELF, $COOKIE, $SOUND;

  if(! $ROOM->IsDay()) return false; //昼以外は出力しない
  if(($revote_times = GetVoteTimes(true)) == 0) return false; //再投票の回数を取得

  if($RQ_ARGS->play_sound && ! $ROOM->view_mode && $revote_times > $COOKIE->vote_times){
    $SOUND->Output('revote'); //音を鳴らす
  }

  //投票済みチェック
  $vote_times = $revote_times + 1;
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  if(FetchResult($query) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '回' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  OutputVoteListDay($ROOM->date); //投票結果を出力
}

//会話ログ出力
function OutputTalkLog(){
  global $MESSAGE, $ROOM, $SELF;

  //会話のユーザ名、ハンドル名、発言、発言のタイプを取得
  $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = {$ROOM->id} AND location LIKE '{$ROOM->day_night}%'
			AND date = {$ROOM->date} ORDER BY time DESC");

  $builder = DocumentBuilder::Generate();
  $builder->BeginTalk('talk');
  while(($row = mysql_fetch_object($sql, 'Talk')) !== false){
    OutputTalk($row, $builder); //会話出力
  }
  if($ROOM->IsBeforeGame()){ //村立て時刻を取得して表示
    $time = FetchResult("SELECT establish_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = '村作成';
  }
  elseif($ROOM->IsNight() && $ROOM->date == 1){ //ゲーム開始時刻を取得して表示
    $time = FetchResult("SELECT start_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = 'ゲーム開始';
  }
  elseif($ROOM->IsAfterGame()){ //ゲーム終了時刻を取得して表示
    $time = FetchResult("SELECT finish_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = 'ゲーム終了';
  }
  if($time != ''){
    $row->uname = 'system';
    $row->sentence .= '：' . ConvertTimeStamp($time);
    $row->location = $ROOM->day_night . 'system';
    OutputTalk($row, $builder);
  }
  $builder->EndTalk();
}

//会話出力
function OutputTalk($talk, &$builder){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $said_user = $USERS->ByVirtualUname($talk->uname);
  $real_user = $USERS->ByRealUname($talk->uname);
  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  /*
    $talk_uname は必ず $talk から取得すること。
    $USERS にはシステムユーザー 'system' が存在しないため、$said_user は常に NULL になっている。
  */
  $talk_uname = $talk->uname;
  $talk_handle_name = $said_user->handle_name;
  $talk_sex         = $said_user->sex;
  $talk_color       = $said_user->color;
  $sentence         = $talk->sentence;
  $font_type        = $talk->font_type;
  $location         = $talk->location;

  if($RQ_ARGS->add_role && $said_user->user_no > 0){ //役職表示モード対応
    if(strpos($location, 'heaven') === false)
      $real_user = $USERS->ByReal($said_user->user_no);
    else
      $real_user = $said_user;
    $talk_handle_name .= $real_user->MakeShortRoleName();
  }
  LineToBR($sentence); //改行コードを <br> に変換

  //投票情報をチェック
  $vote_action_list = array(
    'vote' => 'VOTE_DO',
    'wolf' => 'WOLF_EAT',
    'mage' => 'MAGE_DO', 'voodoo_killer' => 'VOODOO_KILLER_DO',
    'jammer_mad' => 'JAMMER_MAD_DO', 'voodoo_mad' => 'VOODOO_MAD_DO', 'dream_eat' => 'DREAM_EAT',
    'trap_mad' => 'TRAP_MAD_DO', 'not_trap_mad' => 'TRAP_MAD_NOT_DO',
    'guard' => 'GUARD_DO', 'reporter' => 'REPORTER_DO', 'anti_voodoo' => 'ANTI_VOODOO_DO',
    'poison_cat' => 'POISON_CAT_DO', 'not_poison_cat' => 'POISON_CAT_NOT_DO',
    'assassin' => 'ASSASSIN_DO', 'not_assassin' => 'ASSASSIN_NOT_DO',
    'mind_scanner' => 'MIND_SCANNER_DO',
    'voodoo_fox' => 'VOODOO_FOX_DO', 'child_fox' => 'CHILD_FOX_DO',
    'cupid' => 'CUPID_DO',
    'mania' => 'MANIA_DO');
  $flag_vote_action = false;
  foreach($vote_action_list as $this_role => $this_action){
    $flag_action->$this_role = (strpos($sentence, $this_action) === 0);
    $flag_vote_action |= $flag_action->$this_role;
  }

  $location_system = (strpos($location, 'system') !== false);
  $flag_system = ($location_system && $flag_vote_action && ! $ROOM->IsFinished());
  $flag_live_night = ($SELF->IsLive() && $ROOM->IsNight() && ! $ROOM->IsFinished());
  $flag_wolf_group = ($SELF->IsWolf(true) || $virtual_self->IsRole('whisper_mad') ||
		      $SELF->IsDummyBoy());
  $flag_fox_group  = ($virtual_self->IsFox(true) || $SELF->IsDummyBoy());
  $flag_mind_read  = (($ROOM->date > 1 && $SELF->IsLive() &&
		       (($said_user->IsPartner('mind_read', $virtual_self->user_no) &&
			 ! $said_user->IsRole('unconscious')) ||
			$virtual_self->IsPartner('mind_receiver', $said_user->user_no) ||
			$said_user->IsPartner('mind_friend', $virtual_self->partner_list))
		       ) || $said_user->IsRole('mind_open') ||
		      ($real_user->IsRole('possessed_wolf') && $flag_wolf_group));

  if($location_system && $sentence == 'OBJECTION'){ //異議あり
    $sentence = $talk_handle_name . ' ' . $MESSAGE->objection;
    $builder->AddSystemMessage('objection-' . $talk_sex, $sentence);
  }
  elseif($location_system && $sentence == 'GAMESTART_DO'); //ゲーム開始投票 (現在は何も表示しない仕様)
  elseif($location_system && strpos($sentence, 'KICK_DO') === 0){ //KICK 投票
    $target_handle_name = ParseStrings($sentence, 'KICK_DO');
    $sentence = "{$talk_handle_name} は {$target_handle_name} {$MESSAGE->kick_do}";
    $builder->AddSystemMessage('kick', $sentence);
  }
  elseif($SELF->IsLive() && ! $SELF->IsDummyBoy() && $flag_system); //生存中は投票情報は非表示
  elseif($talk_uname == 'system'){ //システムメッセージ
    if(strpos($sentence, 'MORNING') === 0){
      sscanf($sentence, "MORNING\t%d", $morning_date);
      $sentence = "{$MESSAGE->morning_header} {$morning_date} {$MESSAGE->morning_footer}";
    }
    elseif(strpos($sentence, 'NIGHT') === 0){
      $sentence = $MESSAGE->night;
    }
    $builder->AddSystemTalk($sentence);
  }
  elseif(strpos($location, 'dummy_boy') !== false){ //身代わり君専用システムメッセージ
    $builder->AddSystemTalk($MESSAGE->dummy_boy . $sentence, 'dummy-boy');
  }
  //ゲーム開始前後とゲーム中、生きている人の昼
  elseif(! $ROOM->IsPlaying() || ($SELF->IsLive() && $ROOM->IsDay() && $location == 'day')){
    $builder->AddTalk($said_user, $talk);
  }
  //ゲーム中、生きている人の夜の狼
  elseif($flag_live_night && $location == 'night wolf'){
    if($flag_wolf_group || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif(! $SELF->IsRole('mind_scanner')){ //さとりには遠吠えは見えない
      $builder->AddWhisper('wolf', $talk);
    }
  }
  //ゲーム中、生きている人の夜の囁き狂人
  elseif($flag_live_night && $location == 'night mad'){
    if($flag_wolf_group || $flag_mind_read) $builder->AddTalk($said_user, $talk);
  }
  //ゲーム中、生きている人の夜の共有者
  elseif($flag_live_night && $location == 'night common'){
    if($virtual_self->IsRole('common') || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif(! $SELF->IsRole('dummy_common')){ //夢共有者には何も見えない
      $builder->AddWhisper('common', $talk);
    }
  }
  //ゲーム中、生きている人の夜の妖狐
  elseif($flag_live_night && $location == 'night fox'){
    if($flag_fox_group || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif($SELF->IsRole('wise_wolf')){
      $builder->AddWhisper('common', $talk);
    }
  }
  //ゲーム中、生きている人の夜の独り言
  elseif($flag_live_night && $location == 'night self_talk'){
    if($virtual_self->IsSameUser($talk_uname) || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif($said_user->IsRole('silver_wolf') && ! $SELF->IsRole('mind_scanner')){
      $builder->AddWhisper('wolf', $talk); //銀狼の独り言はさとり以外には遠吠えに見える
    }
  }
  //ゲーム終了 / 身代わり君(仮想GM用) / ゲーム中、死亡者(非公開オプション時は不可)
  elseif($ROOM->IsFinished() || $SELF->IsDummyBoy() || ($SELF->IsDead() && $ROOM->IsOpenCast())){
    if($location_system && $flag_action->vote){ //処刑投票
      $target_handle_name = ParseStrings($sentence, 'VOTE_DO');
      $action = 'vote';
      $sentence =  $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->vote_do;
    }
    elseif($location_system && $flag_action->wolf){ //狼の投票
      $target_handle_name = ParseStrings($sentence, 'WOLF_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' たち人狼は '.$target_handle_name.' '.$MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_action->mage){ //占い師の投票
      $target_handle_name = ParseStrings($sentence, 'MAGE_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_action->voodoo_killer){ //陰陽師の投票
      $target_handle_name = ParseStrings($sentence, 'VOODOO_KILLER_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->voodoo_killer_do;
    }
    elseif($location_system && $flag_action->jammer_mad){ //月兎の投票
      $target_handle_name = ParseStrings($sentence, 'JAMMER_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->jammer_do;
    }
    elseif($location_system && $flag_action->voodoo_mad){ //呪術師の投票
      $target_handle_name = ParseStrings($sentence, 'VOODOO_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->voodoo_do;
    }
    elseif($location_system && $flag_action->dream_eat){ //獏の投票
      $target_handle_name = ParseStrings($sentence, 'DREAM_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_action->trap_mad){ //罠師の投票
      $target_handle_name = ParseStrings($sentence, 'TRAP_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->trap_do;
    }
    elseif($location_system && $flag_action->not_trap_mad){ //罠師のキャンセル投票
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' '.$MESSAGE->trap_not_do;
    }
    elseif($location_system && $flag_action->guard){ //狩人の投票
      $target_handle_name = ParseStrings($sentence, 'GUARD_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->guard_do;
    }
    elseif($location_system && $flag_action->reporter){ //ブン屋の投票
      $target_handle_name = ParseStrings($sentence, 'REPORTER_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->reporter_do;
    }
    elseif($location_system && $flag_action->anti_voodoo){ //厄神の投票
      $target_handle_name = ParseStrings($sentence, 'ANTI_VOODOO_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->anti_voodoo_do;
    }
    elseif($location_system && $flag_action->poison_cat){ //猫又の投票
      $target_handle_name = ParseStrings($sentence, 'POISON_CAT_DO');
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->revive_do;
    }
    elseif($location_system && $flag_action->not_poison_cat){ //猫又のキャンセル投票
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->revive_not_do;
    }
    elseif($location_system && $flag_action->assassin){ //暗殺者の投票
      $target_handle_name = ParseStrings($sentence, 'ASSASSIN_DO');
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->assassin_do;
    }
    elseif($location_system && $flag_action->not_assassin){ //暗殺者のキャンセル投票
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->assassin_not_do;
    }
    elseif($location_system && $flag_action->mind_scanner){ //さとりの投票
      $target_handle_name = ParseStrings($sentence, 'MIND_SCANNER_DO');
      $action = 'mind-scanner-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->mind_scanner_do;
    }
    elseif($location_system && $flag_action->voodoo_fox){ //九尾の投票
      $target_handle_name = ParseStrings($sentence, 'VOODOO_FOX_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->voodoo_do;
    }
    elseif($location_system && $flag_action->child_fox){ //子狐の投票
      $target_handle_name = ParseStrings($sentence, 'CHILD_FOX_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_action->cupid){ //キューピッドの投票
      $target_handle_name = ParseStrings($sentence, 'CUPID_DO');
      $action = 'cupid-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->cupid_do;
    }
    elseif($location_system && $flag_action->mania){ //神話マニアの投票
      $target_handle_name = ParseStrings($sentence, 'MANIA_DO');
      $action = 'mania-do';
      $sentence = $talk_handle_name.' は '.$target_handle_name.' '.$MESSAGE->mania_do;
    }
    else{ //その他の全てを表示(死者の場合)
      $base_class = 'user-talk';
      $talk_class = 'user-name';
      switch($location){
      case 'night self_talk':
	$talk_handle_name .= '<span>の独り言</span>';
	$talk_class .= ' night-self-talk';
	break;

      case 'night wolf':
	$talk_handle_name .= '<span>(人狼)</span>';
	$talk_class .= ' night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'night mad':
	$talk_handle_name .= '<span>(囁き狂人)</span>';
	$talk_class .= ' night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'night common':
	$talk_handle_name .= '<span>(共有者)</span>';
	$talk_class .= ' night-common';
	$font_type  .= ' night-common';
	break;

      case 'night fox':
	$talk_handle_name .= '<span>(妖狐)</span>';
	$talk_class .= ' night-fox';
	$font_type  .= ' night-fox';
	break;

      case 'heaven':
	$base_class .= ' heaven';
	break;
      }
    }
    if($action != ''){
      $builder->AddSystemMessage($action, $sentence);
    }
    else{
      $symbol = "<font color=\"{$talk_color}\">◆</font>";
      if($GAME_CONF->quote_words) $sentence = '「' . $sentence . '」';
      $builder->RawAddTalk($symbol, $talk_handle_name, $sentence, $font_type, $base_class, $talk_class);
    }
  }
  //ここからは観戦者と役職非公開モード
  elseif($flag_system); //投票情報は非表示
  else{ //観戦者
    if($ROOM->IsNight()){
      switch($location){
      case 'night wolf':
	if($flag_wolf_group){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif(! $SELF->IsRole('mind_scanner')){ //さとりには遠吠えは見えない
	  $builder->AddWhisper('wolf', $talk);
	}
	break;

      case 'night mad':
	if($flag_wolf_group) $builder->AddTalk($said_user, $talk);
	break;

      case 'night common':
	if($virtual_self->IsRole('common')){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif(! $SELF->IsRole('dummy_common')){ //夢共有者には何も見えない
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'night fox':
	if($flag_fox_group){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($pseud_self->IsRole('wise_wolf')){
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'night self_talk':
	if($virtual_self->IsSameUser($talk_uname)){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($said_user->IsRole('silver_wolf') && ! $SELF->IsRole('mind_scanner')){
	  $builder->AddWhisper('wolf', $talk); //銀狼の独り言はさとり以外には遠吠えに見える
	}
	break;
      }
    }
    else{
      $builder->AddTalk($said_user, $talk);
    }
  }
}

//死亡者の遺言を出力
function OutputLastWords(){
  global $MESSAGE, $ROOM, $USERS;

  //ゲーム中以外は出力しない
  if(! ($ROOM->IsPlaying() || $ROOM->log_mode)) return false;

  //前日の死亡者遺言を出力
  $set_date = $ROOM->date - 1;
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $set_date AND type = 'LAST_WORDS' ORDER BY RAND()";
  $array = FetchArray($query);
  if(count($array) < 1) return false;

  echo <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">

EOF;

  foreach($array as $result){
    list($handle_name, $str) = ParseStrings($result);
    LineToBR(&$str);

    echo <<<EOF
<tr>
<td class="lastwords-title">{$handle_name}<span>さんの遺言</span></td>
<td class="lastwords-body">{$str}</td>
</tr>

EOF;
  }
  echo '</table>'."\n";
}

//前の日の 狼が食べた、狐が占われて死亡、投票結果で死亡のメッセージ
function OutputDeadMan(){
  global $ROOM;

  //ゲーム中以外は出力しない
  if(! $ROOM->IsPlaying()) return false;

  $yesterday = $ROOM->date - 1;

  //共通クエリ
  $query_header = "SELECT message, type FROM system_message WHERE room_no = {$ROOM->id} AND date =";

  //処刑メッセージ、毒死メッセージ(昼)
  $type_day = "type = 'VOTE_KILLED' OR type = 'POISON_DEAD_day' OR type = 'LOVERS_FOLLOWED_day' " .
    "OR type LIKE 'SUDDEN_DEATH_%'";

  //前の日の夜に起こった死亡メッセージ
  $type_night = "type = 'WOLF_KILLED' OR type LIKE 'POSSESSED%' OR type = 'DREAM_KILLED' " .
    "OR type = 'TRAPPED' OR type = 'CURSED' OR type = 'FOX_DEAD' " .
    "OR type = 'HUNTED' OR type = 'REPORTER_DUTY' OR type = 'ASSASSIN_KILLED' " .
    "OR type = 'POISON_DEAD_night' OR type = 'LOVERS_FOLLOWED_night' OR type LIKE 'REVIVE_%'";

  if($ROOM->IsDay()){
    $set_date = $yesterday;
    $type = $type_night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_day;
  }

  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }

  //ログ閲覧モード以外なら二つ前も死亡者メッセージ表示
  if($ROOM->log_mode) return;
  $set_date = $yesterday;
  if($set_date < 2) return;
  $type = ($ROOM->IsDay() ? $type_day : $type_night);

  echo '<hr>'; //死者が無いときに境界線を入れない仕様にする場合は $array の中身をチェックする
  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }
}

//死者のタイプ別に死亡メッセージを出力
function OutputDeadManType($name, $type){
  global $MESSAGE, $ROOM, $SELF;

  $deadman_header = '<tr><td>'.$name.' '; //基本メッセージヘッダ
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //基本メッセージ
  $sudden_death   = $deadman_header.$MESSAGE->vote_sudden_death.'</td>'; //突然死用
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //追加共通ヘッダ
  $open_reason = ($ROOM->IsFinished() || ($SELF->IsDead() && $ROOM->IsOpenCast()) ||
		  $SELF->IsDummyBoy());
  $show_reason = ($open_reason || ($SELF->IsRole('yama_necromancer') && $SELF->IsLive()));

  echo '<table class="dead-type">'."\n";
  switch($type){
  case 'VOTE_KILLED':
    echo '<tr class="dead-type-vote">';
    echo '<td>'.$name.' '.$MESSAGE->vote_killed.'</td>';
    break;

  case 'WOLF_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->wolf_killed.')</td>';
    break;

  case 'POSSESSED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->possessed.')</td>';
    break;

  case 'POSSESSED_RESET':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->possessed_reset.')</td>';
    break;

  case 'POSSESSED_TARGETED':
    if($open_reason) echo '<tr><td>'.$name.' '.$MESSAGE->possessed_targeted.'</td>';
    break;

  case 'WOLF_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->wolf_killed.')</td>';
    break;

  case 'DREAM_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->dream_killed.')</td>';
    break;

  case 'TRAPPED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->trapped.')</td>';
    break;

  case 'FOX_DEAD':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->fox_dead.')</td>';
    break;

  case 'CURSED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->cursed.')</td>';
    break;

  case 'POISON_DEAD_day':
  case 'POISON_DEAD_night':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->poison_dead.')</td>';
    break;

  case 'HUNTED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->hunted.')</td>';
    break;

  case 'REPORTER_DUTY':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->reporter_duty.')</td>';
    break;

  case 'ASSASSIN_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->assassin_killed.')</td>';
    break;

  case 'LOVERS_FOLLOWED_day':
  case 'LOVERS_FOLLOWED_night':
    echo '<tr><td>'.$name.' '.$MESSAGE->lovers_followed.'</td>';
    break;

  case 'REVIVE_SUCCESS':
    echo '<tr class="dead-type-revive">';
    echo '<td>'.$name.' '.$MESSAGE->revive_success.'</td>';
    break;

  case 'REVIVE_FAILED':
    if($ROOM->IsFinished() || $SELF->IsDead()){
      echo '<tr class="dead-type-revive">';
      echo '<td>'.$name.' '.$MESSAGE->revive_failed.'</td>';
    }
    break;

  case 'SUDDEN_DEATH_CHICKEN':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->chicken.')</td>';
    break;

  case 'SUDDEN_DEATH_RABBIT':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->rabbit.')</td>';
    break;

  case 'SUDDEN_DEATH_PERVERSENESS':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->perverseness.')</td>';
    break;

  case 'SUDDEN_DEATH_FLATTERY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->flattery.')</td>';
    break;

  case 'SUDDEN_DEATH_IMPATIENCE':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->impatience.')</td>';
    break;

  case 'SUDDEN_DEATH_JEALOUSY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->jealousy.')</td>';
    break;

  case 'SUDDEN_DEATH_CELIBACY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->celibacy.')</td>';
    break;

  case 'SUDDEN_DEATH_PANELIST':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->panelist.')</td>';
    break;
  }
  echo "</tr>\n</table>\n";
}

//投票の集計出力
function OutputVoteList(){
  global $ROOM;

  if(! $ROOM->IsPlaying()) return false; //ゲーム中以外は出力しない

 //昼なら前日、夜ならの今日の集計を表示
  $set_date = ($ROOM->IsDay() && ! $ROOM->log_mode ? $ROOM->date - 1 : $ROOM->date);
  OutputVoteListDay($set_date);
}

//指定した日付の投票結果を出力する
function OutputVoteListDay($set_date){
  global $RQ_ARGS, $ROOM, $SELF;

  //指定された日付の投票結果を取得
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $set_date and type = 'VOTE_KILL'";
  $vote_message_list = FetchArray($query);
  if(count($vote_message_list) == 0) return false; //投票総数

  $result_array = array(); //投票結果を格納する
  $this_vote_times = -1; //出力する投票回数を記録
  $table_count = 0; //表の個数
  $is_open_vote = (($ROOM->IsOption('open_vote') || $SELF->IsDead() || $ROOM->log_mode) &&
		   ! $ROOM->view_mode);
  foreach($vote_message_list as $vote_message){ //いったん配列に格納する
    //タブ区切りのデータを分割する
    list($handle_name, $target_name, $voted_number,
	 $vote_number, $vote_times) = ParseStrings($vote_message, 'VOTE');

    if($this_vote_times != $vote_times){ //投票回数が違うデータだと別テーブルにする
      if($this_vote_times != -1) $result_array[$this_vote_times][] = '</table>'."\n";

      $this_vote_times = $vote_times;
      $result_array[$vote_times] = array();
      $result_array[$vote_times][] = '<table class="vote-list">'."\n";
      $result_array[$vote_times][] = '<td class="vote-times" colspan="4">' .
	$set_date . ' 日目 ( ' . $vote_times . ' 回目)</td>'."\n";

      $table_count++;
    }
    $vote_number_str = ($is_open_vote ? '投票先 ' . $vote_number . ' 票 →' : '投票先→');

    //表示されるメッセージ
    $result_array[$vote_times][] = '<tr><td class="vote-name">' . $handle_name . '</td><td>' .
      $voted_number . ' 票</td><td>' . $vote_number_str .
      '</td><td class="vote-name"> ' . $target_name . ' </td></tr>'."\n";
  }
  $result_array[$this_vote_times][] = '</table>'."\n";

  //配列に格納されたデータを出力
  if($RQ_ARGS->reverse_log){ //逆順表示
    for($i = 1; $i <= $table_count; $i++){
      if(is_array($result_array[$i])){
	foreach($result_array[$i] as $this_data) echo $this_data;
      }
    }
  }
  else{
    for($i = $table_count; $i > 0; $i--){
      if(is_array($result_array[$i])){
	foreach($result_array[$i] as $this_data) echo $this_data;
      }
    }
  }
}

//占う、狼が狙う、護衛する等、能力を使うメッセージ
function OutputAbilityAction(){
  global $MESSAGE, $ROOM;

  //昼間で役職公開が許可されているときのみ表示
  //(猫又は役職公開時は行動できないので不要)
  if(! ($ROOM->IsDay() && $ROOM->IsOpenCast())) return false;

  $yesterday = $ROOM->date - 1;
  $header = '<b>前日の夜、';
  $footer = '</b><br>'."\n";
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO');
  if($yesterday == 1){
    array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'DREAM_EAT',
	       'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }

  $action = '';
  foreach($action_list as $this_action){
    if($action != '') $action .= ' OR ';
    $action .= "type = '$this_action'";
  }

  $query = "SELECT message, type FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $yesterday AND ( $action )";
  $message_list = FetchAssoc($query);
  foreach($message_list as $array){
    $sentence = $array['message'];
    $type     = $array['type'];

    list($actor, $target) = ParseStrings($sentence);
    echo $header.$actor.' ';
    switch($type){
    case 'WOLF_EAT':
      echo '(人狼) たちは '.$target.' を狙いました';
      break;

    case 'MAGE_DO':
      echo '(占い師) は '.$target.' を占いました';
      break;

    case 'VOODOO_KILLER_DO':
      echo '(陰陽師) は '.$target.' の呪いを祓いました';
      break;

    case 'JAMMER_MAD_DO':
      echo '(月兎) は '.$target.' の占いを妨害しました';
      break;

    case 'TRAP_MAD_DO':
      echo '(罠師) は '.$target.' '.$MESSAGE->trap_do;
      break;

    case 'TRAP_MAD_NOT_DO':
      echo '(罠師) '.$MESSAGE->trap_not_do;
      break;

    case 'VOODOO_MAD_DO':
      echo '(呪術師) は '.$target.' に呪いをかけました';
      break;

    case 'DREAM_EAT':
      echo '(獏) は '.$target.' を狙いました';
      break;

    case 'GUARD_DO':
      echo '(狩人) は '.$target.' '.$MESSAGE->guard_do;
      break;

    case 'ANTI_VOODOO_DO':
      echo '(厄神) は '.$target.' の厄を祓いました';
      break;

    case 'REPORTER_DO':
      echo '(ブン屋) は '.$target.' '.$MESSAGE->reporter_do;
      break;

    case 'ASSASSIN_DO':
      echo '(暗殺者) は '.$target.' を狙いました';
      break;

    case 'ASSASSIN_NOT_DO':
      echo '(暗殺者) '.$MESSAGE->assassin_not_do;
      break;

    case 'MIND_SCANNER_DO':
      echo '(さとり) は '.$target.' の心を読みました';
      break;

    case 'VOODOO_FOX_DO':
      echo '(九尾) は '.$target.' に呪いをかけました';
      break;

    case 'CHILD_FOX_DO':
      echo '(子狐) は '.$target.' を占いました';
      break;

    case 'CUPID_DO':
      echo '(キューピッド) は '.$target.' '.$MESSAGE->cupid_do;
      break;

    case 'MANIA_DO':
      echo '(神話マニア) は '.$target.' を真似しました';
      break;
    }
    echo $footer;
  }
}

//勝敗をチェック
function CheckVictory($check_draw = false){
  global $GAME_CONF, $ROOM;

  $query_count = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} " .
    "AND live = 'live' AND user_no > 0 AND ";

  $human  = FetchResult($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')"); //村人
  $wolf   = FetchResult($query_count . "role LIKE '%wolf%'"); //人狼
  $fox    = FetchResult($query_count . "role LIKE '%fox%'"); //妖狐
  $lovers = FetchResult($query_count . "role LIKE '%lovers%'"); //恋人
  $quiz   = FetchResult($query_count . "role LIKE 'quiz%'"); //出題者

  $victory_role = ''; //勝利陣営
  if($wolf == 0 && $fox == 0 && $human == $quiz){ //全滅
    $victory_role = ($quiz > 0 ? 'quiz' : 'vanish');
  }
  elseif($wolf == 0){ //狼全滅
    if($lovers > 1)  $victory_role = 'lovers';
    elseif($fox > 0) $victory_role = 'fox1';
    else             $victory_role = 'human';
  }
  elseif($wolf >= $human){ //村全滅
    if($lovers > 1)  $victory_role = 'lovers';
    elseif($fox > 0) $victory_role = 'fox2';
    else             $victory_role = 'wolf';
  }
  elseif($human + $wolf + $fox == $lovers){ //生存者全員恋人
    $victory_role = 'lovers';
  }
  elseif($ROOM->IsQuiz() && $quiz == 0){ //クイズ村 GM 死亡
    $victory_role = 'quiz_dead';
  }
  elseif($check_draw && GetVoteTimes() > $GAME_CONF->draw){ //引き分け
    $victory_role = 'draw';
  }

  if($victory_role == '') return false;

  //ゲーム終了
  mysql_query("UPDATE room SET status = 'finished', day_night = 'aftergame',
		victory_role = '$victory_role', finish_time = NOW() WHERE room_no = {$ROOM->id}");
  mysql_query('COMMIT'); //一応コミット
  return true;
}

//恋人の後追い死処理
function LoversFollowed($sudden_death = false){
  global $MESSAGE, $ROOM, $USERS;

  $cupid_list      = array(); //キューピッドのID => 恋人のID
  $lost_cupid_list = array(); //恋人が死亡したキューピッドのリスト
  $checked_list    = array(); //処理済キューピッドのID

  foreach($USERS->rows as $user){ //キューピッドと死んだ恋人のリストを作成
    if(! $user->IsLovers()) continue;
    foreach($user->partner_list['lovers'] as $id){
      $cupid_list[$id][] = $user->user_no;
      if(($user->dead_flag || $user->revive_flag) && ! in_array($id, $lost_cupid_list)){
	$lost_cupid_list[] = $id;
      }
    }
  }

  while(count($lost_cupid_list) > 0){ //対象キューピッドがいれば処理
    $cupid_id = array_shift($lost_cupid_list);
    $checked_list[] = $cupid_id;
    foreach($cupid_list[$cupid_id] as $lovers_id){ //キューピッドのリストから恋人の ID を取得
      $user = $USERS->ById($lovers_id); //恋人の情報を取得

      if($sudden_death){ //突然死の処理
	if(! $user->ToDead()) continue;
	InsertSystemTalk($user->handle_name . $MESSAGE->lovers_followed, ++$ROOM->system_time);
	$user->SaveLastWords();
      }
      elseif(! $USERS->Kill($user->user_no, 'LOVERS_FOLLOWED_' . $ROOM->day_night)){ //通常処理
	continue;
      }
      $user->suicide_flag = true;

      foreach($user->partner_list['lovers'] as $id){ //後追いした恋人のキューピッドのIDを取得
	if(! (in_array($id, $checked_list) || in_array($id, $lost_cupid_list))){ //連鎖判定
	  $lost_cupid_list[] = $id;
	}
      }
    }
  }
}

//巫女の判定結果(システムメッセージ)
function InsertMediumMessage(){
  global $USERS;

  if(! $USERS->IsAppear('medium')) return false; //巫女の出現チェック
  foreach($USERS->rows as $user){
    if(! $user->suicide_flag) continue;
    $handle_name = $USERS->GetVirtualHandleName($user->uname);
    InsertSystemMessage($handle_name . "\t" . $user->DistinguishCamp(), 'MEDIUM_RESULT');
  }
}

//リアルタイムの経過時間
function GetRealPassTime(&$left_time, $flag = false){
  global $ROOM;

  $time_str = strstr($ROOM->game_option, 'real_time');
  //実時間の制限時間を取得
  sscanf($time_str, 'real_time:%d:%d', &$day_minutes, &$night_minutes);
  $day_time   = $day_minutes   * 60; //秒になおす
  $night_time = $night_minutes * 60; //秒になおす

  //最も小さな時間(場面の最初の時間)を取得
  $query = "SELECT MIN(time) FROM talk WHERE room_no = {$ROOM->id} " .
    "AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'";
  $sql = SendQuery($query);
  $start_time = (int)mysql_result($sql, 0, 0);

  if($start_time != NULL){
    $pass_time = $ROOM->system_time - $start_time; //経過した時間
  }
  else{
    $pass_time = 0;
    $start_time = $ROOM->system_time;
  }
  $base_time = ($ROOM->IsDay() ? $day_time : $night_time);
  $left_time = $base_time - $pass_time;
  if($left_time < 0) $left_time = 0; //マイナスになったらゼロにする
  if(! $flag) return;

  $format = 'Y, m, j, G, i, s';
  $start_date_str = TZDate($format, $start_time);
  $end_date_str   = TZDate($format, $start_time + $base_time);
  return array($start_date_str, $end_date_str);
}

//会話で時間経過制の経過時間
function GetTalkPassTime(&$left_time, $flag = false){
  global $TIME_CONF, $ROOM;

  $sql = mysql_query("SELECT SUM(spend_time) FROM talk WHERE room_no = {$ROOM->id}
			AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'");
  $spend_time = (int)mysql_result($sql, 0, 0);

  if($ROOM->IsDay()){ //昼は12時間
    $base_time = $TIME_CONF->day;
    $full_time = 12;
  }
  else{ //夜は6時間
    $base_time = $TIME_CONF->night;
    $full_time = 6;
  }
  $left_time = $base_time - $spend_time;
  if($left_time < 0){ //マイナスになったらゼロにする
    $left_time = 0;
  }

  //仮想時間の計算
  $base_left_time = ($flag ? $TIME_CONF->silence_pass : $left_time);
  return ConvertTime($full_time * $base_left_time * 60 * 60 / $base_time);
}

//システムメッセージ挿入 (talk Table)
function InsertSystemTalk($sentence, $time, $location = '', $date = '', $uname = 'system'){
  global $ROOM;

  if($location == '') $location = "{$ROOM->day_night} system";
  if($date == '') $date = $ROOM->date;
  if($ROOM->test_mode){
    echo "System Talk: $location : $sentence <br>";
    return;
  }
  InsertTalk($ROOM->id, $date, $location, $uname, $time, $sentence, NULL, 0);
}

//システムメッセージ挿入 (system_message Table)
function InsertSystemMessage($sentence, $type, $date = ''){
  global $ROOM;

  if($ROOM->test_mode){
    echo "System Message: $type : $sentence <br>";
    return;
  }
  if($date == '') $date = $ROOM->date;
  $values = "{$ROOM->id}, '$sentence', '$type', $date";
  InsertDatabase('system_message', 'room_no, message, type, date', $values);
}

//最終書き込み時刻を更新
function UpdateTime(){
  global $ROOM;
  mysql_query("UPDATE room SET last_updated = '{$ROOM->system_time}' WHERE room_no = {$ROOM->id}");
}

//今までの投票を全部削除
function DeleteVote(){
  global $ROOM;

  $query = "DELETE FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date}";
  if($ROOM->IsDay()){
    $vote_times = GetVoteTimes();
    $query .= " AND vote_times = $vote_times AND situation = 'VOTE_KILL'";
  }
  elseif($ROOM->IsNight()){
    if($ROOM->date == 1){
      $query .= " AND situation <> 'CUPID_DO'";
    }
    else{
      $query .= " AND situation <> 'VOTE_KILL'";
    }
  }
  mysql_query($query);
  mysql_query("OPTIMIZE TABLE vote");
}

//昼の投票回数を取得する
function GetVoteTimes($revote = false){
  global $ROOM;

  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$ROOM->date} AND type = ";
  $query .= ($revote ?  "'RE_VOTE' ORDER BY message DESC" : "'VOTE_TIMES'");

  return (int)FetchResult($query);
}

//夜の自分の投票済みチェック
function CheckSelfVoteNight($situation, $not_situation = ''){
  global $ROOM, $SELF;

  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} AND ";
  if($situation == 'WOLF_EAT'){
    $query .= "situation = '$situation'";
  }
  elseif($not_situation != ''){
    $query .= "uname = '{$SELF->uname}' AND (situation = '$situation' OR situation = '$not_situation')";
  }
  else{
    $query .= "uname = '{$SELF->uname}' AND situation = '$situation'";
  }
  return (FetchResult($query) > 0);
}

//配列からランダムに一つ取り出す
function GetRandom($array){
  return $array[array_rand($array)];
}

//スペースを復元する
function DecodeSpace(&$str){
  $str = str_replace("\\space;", ' ', $str);
}

//メッセージを分割して必要な情報を返す
function ParseStrings($str, $type = NULL){
  $str = str_replace(' ', "\\space;", $str); //スペースを退避する
  switch($type){
  case 'LAST_WORDS':
  case 'KICK_DO':
  case 'VOTE_DO':
  case 'WOLF_EAT':
  case 'MAGE_DO':
  case 'VOODOO_KILLER_DO':
  case 'JAMMER_MAD_DO':
  case 'TRAP_MAD_DO':
  case 'VOODOO_MAD_DO':
  case 'DREAM_EAT':
  case 'GUARD_DO':
  case 'ANTI_VOODOO_DO':
  case 'REPORTER_DO':
  case 'POISON_CAT_DO':
  case 'ASSASSIN_DO':
  case 'MIND_SCANNER_DO':
  case 'VOODOO_FOX_DO':
  case 'CHILD_FOX_DO':
  case 'CUPID_DO':
  case 'MANIA_DO':
    list($msg, $target) = explode("\t", $str);
    if($msg == $type){
      DecodeSpace(&$target);
      return $target;
    }
    return false;

  case 'MAGE_RESULT':
  case 'TONGUE_WOLF_RESULT':
  case 'REPORTER_SUCCESS':
  case 'POISON_CAT_RESULT':
  case 'PHARMACIST_RESULT':
  case 'MANIA_RESULT':
  case 'CHILD_FOX_RESULT':
    list($first, $second, $third) = explode("\t", $str);
    DecodeSpace(&$first);
    DecodeSpace(&$second);
    DecodeSpace(&$third);
    return array($first, $second, $third);

  case 'VOTE':
    list($self, $target, $voted, $vote, $times) = explode("\t", $str);
    DecodeSpace(&$self);
    DecodeSpace(&$target);

    //%d で取得してるんだから (int)要らないような気がするんだけど……しかもなぜ一つだけ？
    return array($self, $target, $voted, $vote, (int)$times);

  default:
    list($header, $footer) = explode("\t", $str, 2);
    DecodeSpace(&$header);
    DecodeSpace(&$footer);

    return array($header, $footer);
  }
}
?>
