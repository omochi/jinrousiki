<?php
require_once(dirname(__FILE__) . '/include/functions.php');

if(! $dbHandle = ConnectDatabase(true, false)) return false; //DB 接続

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  // リファラチェック
  if (strncmp(@$_SERVER['HTTP_REFERER'], $SERVER_CONF->site_root, strlen($SERVER_CONF->site_root)) != 0)
    OutputActionResult('村作成 [入力エラー]', '無効なアクセスです。');
  // 指定された人数の配役があるかチェック
  elseif (!in_array($_POST['max_user'], $ROOM_CONF->max_user_list))
     OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
  else
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
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

  mysql_query('COMMIT'); //一応コミット
}

//村のメンテナンス処理 (実体)
function MaintenanceRoomAction($list, $query, $base_time){
  $time = TZTime();
  while(($array = mysql_fetch_assoc($list)) !== false){
    $room_no      = $array['room_no'];
    $last_updated = $array['last_updated'];
    $diff_time    = $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//村(room)の作成
function CreateRoom($room_name, $room_comment, $max_user){
  global $SERVER_CONF, $ROOM_CONF, $MESSAGE;

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
  $option_role = '';
  $chaos     = ($ROOM_CONF->chaos     && $_POST['chaos'] == 'chaos');
  $chaosfull = ($ROOM_CONF->chaosfull && $_POST['chaos'] == 'chaosfull');
  $quiz      = ($ROOM_CONF->quiz      && $_POST['quiz']  == 'on');
  $game_option_list = array('open_vote', 'not_open_cast');
  $option_role_list = array();
  if($quiz){
    $game_option .= 'quiz ';

    //GM ログインパスワードをチェック
    $quiz_password = $_POST['quiz_password'];
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    EscapeStrings(&$quiz_password);
    $game_option .= 'dummy_boy ';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
    array_push($game_option_list, 'wish_role');
    $ip_address = $_SERVER['REMOTE_ADDR'];
  }
  else{
    if($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'on'){
      $game_option .= 'dummy_boy ';
      $dummy_boy_handle_name = '身代わり君';
      $dummy_boy_password    = $SERVER_CONF->system_password;
    }
    elseif($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'gm_login'){
      //GM ログインパスワードをチェック
      $gm_password = $_POST['gm_password'];
      if($gm_password == ''){
	OutputRoomAction('empty');
	return false;
      }
      EscapeStrings(&$gm_password);
      $game_option .= 'dummy_boy gm_login ';
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = $gm_password;
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    if($chaos || $chaosfull){
      if($chaos) $game_option .= 'chaos ';
      if($chaosfull) $game_option .= 'chaosfull ';
      array_push($game_option_list, 'secret_sub_role');
      array_push($option_role_list, 'chaos_open_cast', 'no_sub_role');
    }
    else{
      array_push($game_option_list, 'wish_role');
      array_push($option_role_list, 'decide', 'authority', 'poison', 'cupid', 'boss_wolf',
		 'poison_wolf', 'mania', 'medium');
    }
    array_push($option_role_list, 'liar', 'gentleman', 'sudden_death', 'full_mania');
  }

  foreach($game_option_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
      $game_option .= $this_option . ' ';
    }
  }
  foreach($option_role_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
      $option_role .= $this_option . ' ';
    }
  }

  if($ROOM_CONF->real_time && $_POST['real_time'] == 'on'){
    $day   = $_POST['real_time_day'];
    $night = $_POST['real_time_night'];

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

  //テーブルをロック
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, vote WRITE, talk WRITE')){
    OutputRoomAction('busy');
    return false;
  }

  $result = mysql_query('SELECT room_no FROM room ORDER BY room_no DESC'); //降順にルーム No を取得
  $room_no_array = mysql_fetch_assoc($result); //一行目(最も大きな No)を取得
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
			'live', '{$MESSAGE->dummy_boy_last_words}', '$ip_address')");
  }

  if($entry && mysql_query('COMMIT')) //一応コミット
    OutputRoomAction('success', $room_name);
  else
    OutputRoomAction('busy');
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
  global $DEBUG_MODE, $MESSAGE, $ROOM_IMG;

  //ルームNo、ルーム名、コメント、最大人数、状態を取得
  $sql = mysql_query("SELECT room_no, room_name, room_comment, game_option, option_role, max_user,
			status FROM room WHERE status <> 'finished' ORDER BY room_no DESC ");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $room_no      = $array['room_no'];
    $room_name    = $array['room_name'];
    $room_comment = $array['room_comment'];
    $game_option  = $array['game_option'];
    $option_role  = $array['option_role'];
    $max_user     = $array['max_user'];
    $status       = $array['status'];

    $option_img_str = MakeGameOptionImage($game_option, $option_role); //ゲームオプションの画像
    // $option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //最大人数

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->GenerateTag($status)}<span>[{$room_no}番地]</span>{$room_name}村<br>
<div>〜{$room_comment}〜 {$option_img_str}(最大{$max_user}人)</div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' . $room_no .
	' 番地を削除 (緊急用)</a><br>'."\n";
    }
  }
}

//他のサーバの部屋画面を出力
function OutputSharedServerRoom(){
  global $SERVER_CONF, $ROOM_CONF, $ENCODE;

  if(! $SERVER_CONF->shared_server) return false;

  foreach($ROOM_CONF->shared_server_list as $server => $array){
    $this_name      = $array['name'];
    $this_url       = $array['url'];
    $this_encode    = $array['encode'];
    $this_separator = $array['separator'];
    $this_footer    = $array['footer'];

    if(($this_data = file_get_contents($this_url.'room_manager.php')) == '') continue;
    #echo $this_data; //デバッグ用
    if($this_encode != '' && $this_encode != $ENCODE){
      $this_data = mb_convert_encoding($this_data, $ENCODE, $this_encode);
    }
    if($this_separator != ''){
      $this_split_list = mb_split($this_separator, $this_data);
      #print_r($this_split_list); //デバッグ用
      $this_data = array_pop($this_split_list);
    }
    if($this_footer != ''){
      if(($this_position = mb_strrpos($this_data, $this_footer)) === false) continue;
      $this_data = mb_substr($this_data, 0, $this_position + mb_strlen($this_footer));
    }
    if($this_data == '') continue;

    $this_replace_list = array('href="' => 'href="' . $this_url, 'src="'  => 'src="' . $this_url);
    $this_data = strtr($this_data, $this_replace_list);
    echo <<<EOF
    <fieldset>
      <legend>ゲーム一覧 (<a href="$this_url">$this_name</a>)</legend>
      <div class="game-list">$this_data</div>
    </fieldset>

EOF;
  }
}

//部屋作成画面を出力
function OutputCreateRoom(){
  global $GAME_CONF, $ROOM_CONF, $TIME_CONF, $MESSAGE;

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
<input id="wish_role" type="checkbox" name="wish_role" value="on"{$checked}>
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
<input id="real_time" type="checkbox" name="real_time" value="on"{$checked}>
(制限時間が実時間で消費されます　昼：
<input type="text" name="real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">分 夜：
<input type="text" name="real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">分)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->dummy_boy){
    if($ROOM_CONF->default_dummy_boy)
      $checked_dummy_boy = ' checked';
    elseif($ROOM_CONF->default_gerd)
      $checked_gerd = ' checked';
    elseif($ROOM_CONF->default_gm_login)
      $checked_gm = ' checked';
    else
      $checked_nothing = ' checked';

    /*
<input type="radio" name="dummy_boy" value="gerd"{$checked_gerd}>
身代わり君はゲルト君(村人確定の身代わり君です)<br>
*/
    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$MESSAGE->game_option_dummy_boy}：</label></td>
<td class="explain">
<input type="radio" name="dummy_boy" value=""{$checked_nothing}>
身代わり君なし<br>

<input type="radio" name="dummy_boy" value="on"{$checked_dummy_boy}>
身代わり君あり(初日の夜、身代わり君が狼に食べられます)<br>

<input type="radio" name="dummy_boy" value="gm_login"{$checked_gm_login}>
身代わり君は GM (仮想 GM が身代わり君としてログインします)<br>
<label for="gm_password">GM ログインパスワード：</label>
<input id="gm_password" type="password" name="gm_password" size="20"><br>
　　ログインユーザ名は「dummy_boy」です。GM は入村直後に必ず名乗ってください。
</td>
</tr>
<tr><td colspan="2"><hr></td></tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">{$MESSAGE->game_option_open_vote}：</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="open_vote" value="on"{$checked}>
(権力者が投票でバレます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">{$MESSAGE->game_option_not_open_cast}：</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="not_open_cast" value="on"{$checked}>
(霊界でも誰がどの役職なのかが公開されません)
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$GAME_CONF->decide}人以上で{$MESSAGE->game_option_decide}：</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="decide" value="on"{$checked}>
(投票が同数の時、決定者の投票先が優先されます・兼任)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$GAME_CONF->authority}人以上で{$MESSAGE->game_option_authority}：</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="authority" value="on"{$checked}>
(投票の票数が２票になります・兼任)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$GAME_CONF->poison}人以上で{$MESSAGE->game_option_poison}：</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="poison" value="on"{$checked}>
(処刑されたり狼に食べられた場合、道連れにします・村人2→埋毒1、人狼1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14人もしくは{$GAME_CONF->cupid}人以上で<br>　{$MESSAGE->game_option_cupid}：</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="cupid" value="on"{$checked}>
(初日夜に選んだ相手を恋人にします。恋人となった二人は勝利条件が変化します)<br>
　　　(村人1→キューピッド1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$GAME_CONF->boss_wolf}人以上で{$MESSAGE->game_option_boss_wolf}：</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="boss_wolf" value="on"{$checked}>
(占い結果が「村人」、霊能結果が「白狼」と表示される狼です。 人狼1→白狼1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison_wolf){
    $checked = ($ROOM_CONF->default_poison_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison_wolf">{$GAME_CONF->poison_wolf}人以上で{$MESSAGE->game_option_poison_wolf}：</label></td>
<td class="explain">
<input id="role_poison_wolf" type="checkbox" name="poison_wolf" value="on"{$checked}>
(吊られた時にランダムで村人一人を巻き添えにする狼です。 人狼1→毒狼1、村人1→薬師1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->mania){
    $checked = ($ROOM_CONF->default_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_mania">{$GAME_CONF->mania}人以上で{$MESSAGE->game_option_mania}：</label></td>
<td class="explain">
<input id="role_mania" type="checkbox" name="mania" value="on"{$checked}>
(初日夜に他の村人の役職をコピーする特殊な役職です。村人1→神話マニア1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->medium){
    $checked = ($ROOM_CONF->default_medium ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_medium">{$GAME_CONF->medium}人以上で{$MESSAGE->game_option_medium}：</label></td>
<td class="explain">
<input id="role_medium" type="checkbox" name="medium" value="on"{$checked}>
(突然死した人の所属陣営が分かる特殊な霊能者です。村人2→巫女1、狂信者1)
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->liar){
    $checked = ($ROOM_CONF->default_liar ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_liar">{$MESSAGE->game_option_liar}：</label></td>
<td class="explain">
<input id="role_liar" type="checkbox" name="liar" value="on"{$checked}>
(ランダムで「狼少年」がつきます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->gentleman){
    $checked = ($ROOM_CONF->default_gentleman ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_gentleman">{$MESSAGE->game_option_gentleman}：</label></td>
<td class="explain">
<input id="role_gentleman" type="checkbox" name="gentleman" value="on"{$checked}>
(全員に性別に応じた「紳士」「淑女」がつきます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->sudden_death){
    $checked = ($ROOM_CONF->default_sudden_death ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_sudden_death">{$MESSAGE->game_option_sudden_death}：</label></td>
<td class="explain">
<input id="role_sudden_death" type="checkbox" name="sudden_death" value="on"{$checked}>
(全員に投票でショック死するサブ役職のどれかがつきます)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->full_mania){
    $checked = ($ROOM_CONF->default_full_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_full_mania">{$MESSAGE->game_option_full_mania}：</label></td>
<td class="explain">
<input id="role_full_mania" type="checkbox" name="full_mania" value="on"{$checked}>
(「村人」が全員「神話マニア」に入れ替わります)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->chaos){
    if($ROOM_CONF->default_chaos)
      $checked_chaos = ' checked';
    elseif($ROOM_CONF->default_chaosfull)
      $checked_chaosfull = ' checked';
    else
      $checked_normal = ' checked';

    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$MESSAGE->game_option_chaos}：</label></td>
<td class="explain">
<input type="radio" name="chaos" value=""{$checked_normal}>
通常人狼<br>

<input type="radio" name="chaos" value="chaos"{$checked_chaos}>
通常村＋α程度に配役がぶれる闇鍋モードです<br>

<input type="radio" name="chaos" value="chaosfull"{$checked_chaosfull}>
人狼1、占い師1以外の全ての役職がランダムとなる真・闇鍋モードです
</td>
</tr>

EOF;
    if($ROOM_CONF->chaos_open_cast){
      $checked = ($ROOM_CONF->default_chaos_open_cast ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="chaos_open_cast">{$MESSAGE->game_option_chaos_open_cast}：</label></td>
<td class="explain">
<input id="chaos_open_cast" type="checkbox" name="chaos_open_cast" value="on"{$checked}>
(配役を通知します：闇鍋モード専用オプション)
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->secret_sub_role){
      $checked = ($ROOM_CONF->default_secret_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="secret_sub_role">{$MESSAGE->game_option_secret_sub_role}：</label></td>
<td class="explain">
<input id="secret_sub_role" type="checkbox" name="secret_sub_role" value="on"{$checked}>
(サブ役職が分からなくなります：闇鍋モード専用オプション)
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->no_sub_role){
      $checked = ($ROOM_CONF->default_no_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="no_sub_role">{$MESSAGE->game_option_no_sub_role}：</label></td>
<td class="explain">
<input id="no_sub_role" type="checkbox" name="no_sub_role" value="on"{$checked}>
(サブ役職をつけません：闇鍋モード専用オプション)
</td>
</tr>

EOF;
    }
  }


  if($ROOM_CONF->quiz){
    $checked = ($ROOM_CONF->default_quiz ? ' checked' : '');
    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label for="quiz">{$MESSAGE->game_option_quiz}：</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="quiz" value="on"{$checked}>
(身代わり君が「出題者」になってクイズを出します)<br>
<label for="quiz_password">GM ログインパスワード：</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
　　ログインユーザ名は「dummy_boy」です。GM は入村直後に必ず名乗ってください。
</td>
</tr>

EOF;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr><td class="make" colspan="2"><input type="submit" value=" 作成 "></td></tr>
</table>
</form>

EOF;
}
?>
