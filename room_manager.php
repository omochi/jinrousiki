<?php
require_once(dirname(__FILE__) . '/include/functions.php');

if(! $dbHandle = ConnectDatabase(true, false)) return false; //DB 接続

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  if(in_array($_POST['max_user'], $ROOM_CONF->max_user_list))
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
  else
    OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
}
else{
  OutputRoomList();
}

DisconnectDatabase($dbHandle); //DB 接続解除

//-- 関数 --//
//村のメンテナンス処理
function MaintenanceRoom(){
  global $ROOM_CONF;

  //一定時間更新の無い村は廃村にする
  $list  = mysql_query("SELECT room_no, last_updated FROM room WHERE status <> 'finished'");
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' WHERE room_no = ";
  MaintenanceRoomAction($list, $query, $ROOM_CONF->die_room);

  //終了した部屋のセッションIDのデータをクリアする
  $list = mysql_query("SELECT room.room_no, last_updated from room, user_entry
			WHERE room.room_no = user_entry.room_no
			AND !(user_entry.session_id is NULL) GROUP BY room_no");
  $query = "UPDATE user_entry SET session_id = NULL WHERE room_no = ";
  MaintenanceRoomAction($list, $query, $ROOM_CONF->clear_session_id);
}

//村のメンテナンス処理 (実体)
function MaintenanceRoomAction($list, $query, $base_time){
  $count = mysql_num_rows($list);
  $time  = TZTime();

  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($list);
    $room_no      = $array['room_no'];
    $last_updated = $array['last_updated'];
    $diff_time    = $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//村(room)の作成
function CreateRoom($room_name, $room_comment, $max_user){
  global $ROOM_CONF, $MESSAGE, $system_password;

  //入力データのエラーチェック
  if($room_name == '' || $room_comment == '' || ! ctype_digit($max_user)){
    OutputRoomAction('empty');
    return false;
  }
  //エスケープ処理
  EscapeStrings(&$room_name);
  EscapeStrings(&$room_comment);

  //ゲームオプションをセット
  $game_option = '';
  $quiz = false;
  if($ROOM_CONF->quiz && $_POST['game_option_quiz'] == 'quiz'){
    $game_option .= 'quiz ';
    $quiz = true;

    //GM ログインパスワードをチェック
    $quiz_password = $_POST['quiz_password'];
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    EscapeStrings(&$quiz_password);
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
  }
  else{
    $dummy_boy_handle_name = '身代わり君';
    $dummy_boy_password    = $system_password;
  }

  if($ROOM_CONF->wish_role && $_POST['game_option_wish_role'] == 'wish_role')
    $game_option .= 'wish_role ';
  if(($ROOM_CONF->dummy_boy && $_POST['game_option_dummy_boy'] == 'dummy_boy') || $quiz)
    $game_option .= 'dummy_boy ';
  if($ROOM_CONF->open_vote && $_POST['game_option_open_vote'] == 'open_vote')
    $game_option .= 'open_vote ';
  if($ROOM_CONF->not_open_cast && $_POST['game_option_not_open_cast'] == 'not_open_cast')
    $game_option .= 'not_open_cast ';
  if($ROOM_CONF->chaos && $_POST['game_option_chaos'] == 'chaos')
    $game_option .= 'chaos ';
  if($ROOM_CONF->chaosfull && $_POST['game_option_chaos'] == 'chaosfull')
    $game_option .= 'chaosfull ';
  if($ROOM_CONF->real_time && $_POST['game_option_real_time'] == 'real_time'){
    $day   = $_POST['game_option_real_time_day'];
    $night = $_POST['game_option_real_time_night'];

    //制限時間が0から99以内の数字かチェック
    if($day   != '' && ! preg_match('/[^0-9]/', $day)   && $day   > 0 && $day   < 99 &&
       $night != '' && ! preg_match('/[^0-9]/', $night) && $night > 0 && $night < 99){
      $game_option .= 'real_time:' . $day . ':' . $night;
    }
    else{
      OutputRoomAction('time');
      return false;
    }
  }

  $option_role = '';
  if(! $quiz){
    if($ROOM_CONF->decide && $_POST['option_role_decide'] == 'decide')
      $option_role .= 'decide ';
    if($ROOM_CONF->authority && $_POST['option_role_authority'] == 'authority')
      $option_role .= 'authority ';
    if($ROOM_CONF->poison && $_POST['option_role_poison'] == 'poison')
      $option_role .= 'poison ';
    if($ROOM_CONF->cupid && $_POST['option_role_cupid'] == 'cupid')
      $option_role .= 'cupid ';
    if($ROOM_CONF->boss_wolf && $_POST['option_role_boss_wolf'] == 'boss_wolf')
      $option_role .= 'boss_wolf ';
  }

  //テーブルをロック
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, vote WRITE, talk WRITE')){
    OutputRoomAction('busy');
    return false;
  }

  $result = mysql_query('SELECT room_no FROM room ORDER BY room_no DESC'); //降順にルームNoを取得
  $room_no_array = mysql_fetch_assoc($result); //一行目(最も大きなNo)を取得
  $room_no = $room_no_array['room_no'] + 1;

  //登録
  $time = TZTime();
  $entry = mysql_query("INSERT INTO room(room_no, room_name, room_comment, game_option,
			option_role, max_user, status, date, day_night, last_updated)
			VALUES($room_no, '$room_name', '$room_comment', '$game_option',
			'$option_role', $max_user, 'waiting', 0, 'beforegame', '$time')");

  //身代わり君を入村させる
  if(strpos($game_option, 'dummy_boy') !== false){
    mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name, icon_no,
			profile, sex, password, live, last_words, ip_address)
			VALUES($room_no, 1, 'dummy_boy', '$dummy_boy_handle_name', 0,
			'{$MESSAGE->dummy_boy_comment}', 'male', '$dummy_boy_password',
			'live', '{$MESSAGE->dummy_boy_last_words}', '')");
  }

  if($entry && mysql_query('COMMIT')){ //一応コミット
    OutputRoomAction('success', $room_name);
  }
  else{
    OutputRoomAction('busy');
  }
  mysql_query('UNLOCK TABLES');
}

//結果出力 (CreateRoom() 用)
function OutputRoomAction($type, $room_name = ''){
  switch($type){
    case 'empty':
      OutputActionResultHeader('村作成 [入力エラー]');
      echo 'エラーが発生しました。<br>';
      echo '以下の項目を再度ご確認ください。<br>';
      echo '<ul><li>村の名前が記入されていない。</li>';
      echo '<li>村の説明が記入されていない。</li>';
      echo '<li>最大人数が数字ではない、または異常な文字列。</li></ul>';
      break;

    case 'time':
      OutputActionResultHeader('村作成 [入力エラー]');
      echo 'エラーが発生しました。<br>';
      echo '以下の項目を再度ご確認ください。<br>';
      echo '<ul><li>リアルタイム制の昼、夜の時間を記入していない。</li>';
      echo '<li>リアルタイム制の昼、夜の時間を全角で入力している</li>';
      echo '<li>リアルタイム制の昼、夜の時間が0以下、または99以上である</li>';
      echo '<li>リアルタイム制の昼、夜の時間が数字ではない、または異常な文字列</li></ul>';
      break;

    case 'success':
      OutputActionResultHeader('村作成', 'index.php');
      echo "$room_name 村を作成しました。トップページに飛びます。";
      echo '切り替わらないなら <a href="index.php">ここ</a> 。';
      break;

    case 'busy':
      OutputActionResultHeader('村作成 [データベースエラー]');
      echo 'データベースサーバが混雑しています。<br>'."\n";
      echo '時間を置いて再度登録してください。';
      break;
  }
  OutputHTMLFooter(); //フッタ出力
}

//村(room)のwaitingとplayingのリストを出力する
function OutputRoomList(){
  global $DEBUG_MODE, $ROOM_IMG;

  //ルームNo、ルーム名、コメント、最大人数、状態を取得
  $sql = mysql_query("SELECT room_no, room_name, room_comment, game_option, option_role, max_user,
			status FROM room WHERE status <> 'finished' ORDER BY room_no DESC ");
  if($sql == NULL) return false;

  while($array = mysql_fetch_assoc($sql)){
    $room_no      = $array['room_no'];
    $room_name    = $array['room_name'];
    $room_comment = $array['room_comment'];
    $game_option  = $array['game_option'];
    $option_role  = $array['option_role'];
    $max_user     = $array['max_user'];
    $status       = $array['status'];

    switch($status){
      case 'waiting':
	$status_img = $ROOM_IMG->waiting;
	break;

      case 'playing':
	$status_img = $ROOM_IMG->playing;
	break;
    }

    $option_img_str = ''; //ゲームオプションの画像
    if(strpos($game_option, 'wish_role') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->wish_role, '役割希望制');
    if(strpos($game_option, 'real_time') !== false){
      //実時間の制限時間を取得
      $real_time_str = strstr($game_option, 'real_time');
      sscanf($real_time_str, "real_time:%d:%d", &$day, &$night);
      AddImgTag(&$option_img_str, $ROOM_IMG->real_time,
		"リアルタイム制　昼： $day 分　夜： $night 分");
    }
    if(strpos($game_option, 'dummy_boy') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->dummy_boy, '初日の夜は身代わり君');
    if(strpos($game_option, 'open_vote') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->open_vote, '投票した票数を公表する');
    if(strpos($game_option, 'not_open_cast') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->not_open_cast, '霊界で配役を公開しない');
    if(strpos($option_role, 'decide') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->decide, '16人以上で決定者登場');
    if(strpos($option_role, 'authority') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->authority, '16人以上で権力者登場');
    if(strpos($option_role, 'poison') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->poison, '20人以上で埋毒者登場');
    if(strpos($option_role, 'cupid') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->cupid, 'キューピッド登場');
    if(strpos($game_option, 'quiz') !== false)
      $option_img_str .= 'Qz';
    if(strpos($game_option, 'chaos ') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->chaos, '闇鍋');
    if(strpos($game_option, 'chaosfull') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->chaosfull, '真・闇鍋');

    $max_user_img = $ROOM_IMG -> max_user_list[$max_user]; //最大人数

    echo <<<EOF
<a href="login.php?room_no=$room_no">
<img src="$status_img"><span>[{$room_no}番地]</span>{$room_name}村<br>
<div>〜{$room_comment}〜 {$option_img_str}<img src="$max_user_img"></div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' . $room_no .
	' 番地を削除 (緊急用)</a><br>'."\n";
    }
  }
}

//オプション画像タグ追加 (OutputRoomList() 用)
function AddImgTag(&$tag, $src, $title){
  $tag .= "<img class=\"option\" src=\"$src\" title=\"$title\" alt=\"$title\">";
}

//部屋作成画面を出力
function OutputCreateRoom(){
  global $GAME_CONF, $ROOM_CONF, $TIME_CONF;

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>
<tr>
<td><label>村の名前：</label></td>
<td><input type="text" name="room_name" size="{$ROOM_CONF->room_name}"> 村</td>
</tr>
<tr>
<td><label>村についての説明：</label></td>
<td><input type="text" name="room_comment" size="{$ROOM_CONF->room_comment}"></td>
</tr>
<tr>
<td><label>最大人数：</label></td>
<td>
<select name="max_user">
<optgroup label="最大人数">

EOF;

  foreach($ROOM_CONF->max_user_list as $number){
    echo '<option' . ($number == $ROOM_CONF->default_max_user ? ' selected' : '') . '>' .
      $number . '</option>'."\n";
  }

  echo <<<EOF
</optgroup>
</select>
<span class="explain">(配役は <a href="rule.php">ルール</a> を確認して下さい)</span></td>
</tr>

EOF;

  if($ROOM_CONF->wish_role){
    $checked = ($ROOM_CONF->default_wish_role ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="wish_role">役割希望制：</label></td>
<td class="explain">
<input id="wish_role" type="checkbox" name="game_option_wish_role" value="wish_role"{$checked}>
(希望の役割を指定できますが、なれるかは運です)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->real_time){
    $checked = ($ROOM_CONF->default_real_time ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="real_time">リアルタイム制：</label></td>
<td class="explain">
<input id="real_time" type="checkbox" name="game_option_real_time" value="real_time"{$checked}>
(制限時間が実時間で消費されます　昼：
<input type="text" name="game_option_real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">分 夜：
<input type="text" name="game_option_real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">分)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->dummy_boy){
    $checked = ($ROOM_CONF->default_dummy_boy ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="dummy_boy">初日の夜は身代わり君：</label></td>
<td class="explain">
<input id="dummy_boy" type="checkbox" name="game_option_dummy_boy" value="dummy_boy"{$checked}>
(初日の夜、身代わり君が狼に食べられます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">投票した票数を公表する：</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="game_option_open_vote" value="open_vote"{$checked}>
(権力者が投票でバレます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">霊界で配役を公開しない：</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="game_option_not_open_cast" value="not_open_cast"{$checked}>
(霊界でも誰がどの役職なのかが公開されません)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$GAME_CONF->decide}人以上で決定者登場：</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="option_role_decide" value="decide"{$checked}>
(投票が同数の時、決定者の投票先が優先されます・兼任)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$GAME_CONF->authority}人以上で権力者登場：</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="option_role_authority" value="authority"{$checked}>
(投票の票数が２票になります・兼任)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$GAME_CONF->poison}人以上で埋毒者登場：</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="option_role_poison" value="poison"{$checked}>
(処刑されたり狼に食べられた場合、道連れにします・村人二人→埋毒1 狼1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14人もしくは{$GAME_CONF->cupid}人以上で<br>　キューピッド登場：</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="option_role_cupid" value="cupid"{$checked}>
(初日夜に選んだ相手を恋人にします。恋人となった二人は勝利条件が変化します)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$GAME_CONF->boss_wolf}人以上で白狼登場：</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="option_role_boss_wolf" value="boss_wolf"{$checked}>
(占い結果が「村人」、霊能結果が「白狼」と表示される狼です。 人狼一人と入れ替わりで登場)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->quiz){
    $checked = ($ROOM_CONF->default_quiz ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="quiz">クイズ村：</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="game_option_quiz" value="quiz"{$checked}>
(誰か説明文考えて)<br>
<label for="quiz_password">GM ログインパスワード：</label>
<input id="quiz_password" type="password" name="quiz_password" size="20">
</td>
</tr>

EOF;
  }


  if($ROOM_CONF->chaos){
    $checked = ($ROOM_CONF->default_chaos ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label>闇鍋モード：</label></td>
<td class="explain">
<input type="radio" name="game_option_chaos" value="" checked>
通常人狼<br>

<input type="radio" name="game_option_chaos" value="chaos">
狼、狐以外全ての役職がランダムとなる闇鍋モードです<br>

<input type="radio" name="game_option_chaos" value="chaosfull">
全ての役職がランダムとなる真・闇鍋モードです
</td>
</tr>
EOF;
  }

  echo <<<EOF
<tr><td class="make" colspan="2"><input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
}
?>
