<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'TIME_CONF', 'ROOM_IMG', 'MESSAGE', 'GAME_OPT_CAPT');

if(! $DB_CONF->Connect(true, false)) return false; //DB 接続

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  //リファラチェック
  $white_list = array('127.', '192.168.');
  foreach($white_list as $host){ //ホワイトリストチェック
    $trusted |= (strpos($_SERVER['REMOTE_ADDR'], $host) === 0);
  }
  if(! $trusted &&
     strncmp(@$_SERVER['HTTP_REFERER'], $SERVER_CONF->site_root,
	     strlen($SERVER_CONF->site_root)) != 0){
    OutputActionResult('村作成 [入力エラー]', '無効なアクセスです。');
  }

  // 指定された人数の配役があるかチェック
  if(in_array($_POST['max_user'], $ROOM_CONF->max_user_list)){
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
  }
  else{
    OutputActionResult('村作成 [入力エラー]', '無効な最大人数です。');
  }
}
else{
  OutputRoomList();
}

$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
//村のメンテナンス処理
function MaintenanceRoom(){
  global $ROOM_CONF;

  //一定時間更新の無い村は廃村にする
  $list  = mysql_query("SELECT room_no, last_updated FROM room WHERE status <> 'finished'");
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' WHERE room_no = ";
  MaintenanceRoomAction($list, $query, false, $ROOM_CONF->die_room);

  //終了した部屋のセッションIDのデータをクリアする
  $list = mysql_query("SELECT room.room_no, finish_time FROM room, user_entry
			WHERE room.room_no = user_entry.room_no
			AND !(user_entry.session_id is NULL) GROUP BY room_no");
  $query = "UPDATE user_entry SET session_id = NULL WHERE room_no = ";
  MaintenanceRoomAction($list, $query, true, $ROOM_CONF->clear_session_id);

  mysql_query('COMMIT'); //一応コミット
}

//村のメンテナンス処理 (実体)
function MaintenanceRoomAction($list, $query, $is_based_finish_time, $base_time){
  $time = TZTime();
  while(($array = mysql_fetch_assoc($list)) !== false){
    extract($array);
    $diff_time = $is_based_finish_time ?
                 $time - strtotime(finish_time) : $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//村(room)の作成
function CreateRoom($room_name, $room_comment, $max_user){
  global $DEBUG_MODE, $SERVER_CONF, $ROOM_CONF, $MESSAGE;

  $query = "FROM room WHERE status <> 'finished'"; //チェック用の共通クエリ
  $ip_address = $_SERVER['REMOTE_ADDR']; //村立てを行ったユーザの IP を取得

  //同じユーザが立てた村が終了していなければ新しい村を作らない
  if(! $DEBUG_MODE &&
     FetchResult("SELECT COUNT(room_no) $query AND establisher_ip = '$ip_address'") > 0){
    OutputRoomAction('over_establish');
    return false;
  }

  //最大並列村数を超えているようであれば新しい村を作らない
  if(FetchResult("SELECT COUNT(room_no) $query") >= $ROOM_CONF->max_active_room){
    OutputRoomAction('full');
    return false;
  }

  //連続村立て制限チェック
  $time_stamp = FetchResult("SELECT establish_time $query ORDER BY room_no DESC");
  if(isset($time_stamp)){
    if(TZTime() - ConvertTimeStamp($time_stamp, false) <= $ROOM_CONF->establish_wait){
      OutputRoomAction('establish_wait');
      return false;
    }
  }

  //入力データのエラーチェック
  if($room_name == '' || $room_comment == '' || ! ctype_digit($max_user)){
    OutputRoomAction('empty');
    return false;
  }

  //ゲームオプションをセット
  $game_option = '';
  $option_role = '';
  $chaos        = ($ROOM_CONF->chaos        && $_POST['chaos'] == 'chaos');
  $chaosfull    = ($ROOM_CONF->chaosfull    && $_POST['chaos'] == 'chaosfull');
  $perverseness = ($ROOM_CONF->perverseness && $_POST['perverseness']  == 'on');
  $full_mania   = ($ROOM_CONF->full_mania   && $_POST['full_mania']  == 'on');
  $quiz         = ($ROOM_CONF->quiz         && $_POST['quiz']  == 'on');
  $duel         = ($ROOM_CONF->duel         && $_POST['duel']  == 'on');
  $game_option_list = array('wish_role', 'open_vote', 'not_open_cast');
  $option_role_list = array();
  if($quiz){
    $game_option .= 'quiz ';

    //GM ログインパスワードをチェック
    $quiz_password = $_POST['quiz_password'];
    EscapeStrings(&$quiz_password);
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    $game_option .= 'dummy_boy ';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
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
    }
    if($chaos || $chaosfull){
      if($chaos) $game_option .= 'chaos ';
      if($chaosfull) $game_option .= 'chaosfull ';
      array_push($game_option_list, 'secret_sub_role');
      array_push($option_role_list, 'chaos_open_cast', 'chaos_open_cast_camp', 'chaos_open_cast_role');
      if($perverseness){
	$option_role .= 'no_sub_role ';
	array_push($option_role_list, 'perverseness');
      }
      else{
	array_push($option_role_list, 'no_sub_role');
      }
    }
    else{
      if($duel){
	$option_role .= 'duel ';
      }
      else{
	if(! $perverseness) array_push($option_role_list, 'decide', 'authority');
	array_push($option_role_list, 'poison', 'cupid', 'boss_wolf', 'poison_wolf', 'medium');
	if(! $full_mania) array_push($option_role_list, 'mania');
      }
    }
    array_push($option_role_list, 'liar', 'gentleman');
    if($perverseness){
      array_push($option_role_list, 'perverseness');
    }
    else{
      array_push($option_role_list, 'sudden_death');
    }
    if(! $duel) array_push($option_role_list, 'full_mania');
  }

  foreach($game_option_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
      $game_option .= $this_option . ' ';
    }
  }
  foreach($option_role_list as $this_option){
    if(! $ROOM_CONF->$this_option) continue;
    if($this_option == 'chaos_open_cast'){
      switch($_POST[$this_option]){
      case 'full':
	$add_option = 'chaos_open_cast';
	break;

      case 'camp':
	$add_option = 'chaos_open_cast_camp';
	break;

      case 'role':
	$add_option = 'chaos_open_cast_role';
	break;

      default:
	continue;
      }
      $option_role .= $add_option . ' ';
    }
    elseif($_POST[$this_option] == 'on'){
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

  //降順にルーム No を取得して最も大きな No を取得
  $room_no = FetchResult('SELECT room_no FROM room ORDER BY room_no DESC') + 1;

  //登録
  $status = false;
  do{
    //村作成
    $time = TZTime();
    $items = 'room_no, room_name, room_comment, establisher_ip, establish_time, game_option, ' .
      'option_role, max_user, status, date, day_night, last_updated';
    $values = "$room_no, '$room_name', '$room_comment', '$ip_address', NOW(), '$game_option', " .
      "'$option_role', $max_user, 'waiting', 0, 'beforegame', '$time'";
    if(! InsertDatabase('room', $items, $values)) break;

    //身代わり君を入村させる
    if(strpos($game_option, 'dummy_boy') !== false &&
       FetchResult("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no") == 0){
      $crypt_dummy_boy_password = CryptPassword($dummy_boy_password);
      $items = 'room_no, user_no, uname, handle_name, icon_no, profile, sex, password, live, last_words';
      $values = "$room_no, 1, 'dummy_boy', '$dummy_boy_handle_name', 0, '{$MESSAGE->dummy_boy_comment}', " .
	"'male', '$crypt_dummy_boy_password', 'live', '{$MESSAGE->dummy_boy_last_words}'";
      if(! InsertDatabase('user_entry', $items, $values)) break;
    }

    if(! mysql_query('COMMIT')) break; //一応コミット
    OutputRoomAction('success', $room_name);
    $status = true;
  }while(false);
  if(! $status) OutputRoomAction('busy');
  mysql_query('UNLOCK TABLES');
}

//結果出力 (CreateRoom() 用)
function OutputRoomAction($type, $room_name = ''){
  global $SERVER_CONF;

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
    OutputActionResultHeader('村作成', $SERVER_CONF->site_root);
    echo $room_name . ' 村を作成しました。トップページに飛びます。';
    echo '切り替わらないなら <a href="' . $SERVER_CONF->site_root . '">ここ</a> 。';
    break;

  case 'busy':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo 'データベースサーバが混雑しています。<br>'."\n";
    echo '時間を置いて再度登録してください。';
    break;

  case 'full':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo '現在プレイ中の村の数がこのサーバで設定されている最大値を超えています。<br>'."\n";
    echo 'どこかの村で決着がつくのを待ってから再度登録してください。';
    break;

  case 'over_establish':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo 'あなたが立てた村が現在稼働中です。<br>'."\n";
    echo '立てた村で決着がつくのを待ってから再度登録してください。';
    break;

  case 'establish_wait':
    OutputActionResultHeader('村作成 [データベースエラー]');
    echo 'サーバで設定されている村立て時間間隔を経過していません。<br>'."\n";
    echo 'しばらく時間を開けてから再度登録してください。';
    break;
  }
  OutputHTMLFooter(); //フッタ出力
}

//村(room)のwaitingとplayingのリストを出力する
function OutputRoomList(){
  global $DEBUG_MODE, $ROOM_IMG;

  //ルームNo、ルーム名、コメント、最大人数、状態を取得
  $query = "SELECT room_no, room_name, room_comment, game_option, option_role, max_user, status " .
    "FROM room WHERE status <> 'finished' ORDER BY room_no DESC";
  $list = FetchAssoc($query);
  foreach($list as $array){
    extract($array);
    $option_img_str = MakeGameOptionImage($game_option, $option_role); //ゲームオプションの画像
    //$option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //最大人数

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->GenerateTag($status)}<span>[{$room_no}番地]</span>{$room_name}村<br>
<div>〜{$room_comment}〜 {$option_img_str}(最大{$max_user}人)</div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' .
	$room_no . ' 番地を削除 (緊急用)</a><br>'."\n";
    }
  }
}

//他のサーバの部屋画面を出力
function OutputSharedServerRoom(){
  global $SERVER_CONF;

  $config =& new SharedServerConfig();
  if($config->disable) return false;

  foreach($config->server_list as $server => $array){
    extract($array, EXTR_PREFIX_ALL, 'this');
    //PrintData($this_url, 'URL'); //テスト用
    if($this_disable || $this_url == $SERVER_CONF->site_root) continue;
    if(($this_data = file_get_contents($this_url.'room_manager.php')) == '') continue;
    //PrintData($this_data, 'Data'); //テスト用
    if($this_encode != '' && $this_encode != $config->encode){
      $this_data = mb_convert_encoding($this_data, $config->encode, $this_encode);
    }
    if($this_separator != ''){
      $this_split_list = mb_split($this_separator, $this_data);
      //PrintData($this_split_list, 'Split'); //テスト用
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
  global $ROOM_CONF, $TIME_CONF, $CAST_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>
<tr>
<td><label>{$GAME_OPT_MESS->room_name}：</label></td>
<td><input type="text" name="room_name" size="{$ROOM_CONF->room_name}"> 村</td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->room_comment}：</label></td>
<td><input type="text" name="room_comment" size="{$ROOM_CONF->room_comment}"></td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->max_user}：</label></td>
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
<span class="explain">({$GAME_OPT_CAPT->max_user})</span></td>
</tr>

EOF;

  if($ROOM_CONF->wish_role){
    $checked = ($ROOM_CONF->default_wish_role ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="wish_role">{$GAME_OPT_MESS->wish_role}：</label></td>
<td class="explain">
<input id="wish_role" type="checkbox" name="wish_role" value="on"{$checked}>
({$GAME_OPT_CAPT->wish_role})
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
({$GAME_OPT_CAPT->real_time}　昼：
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
<td><label>{$GAME_OPT_MESS->dummy_boy}：</label></td>
<td class="explain">
<input type="radio" name="dummy_boy" value=""{$checked_nothing}>
{$GAME_OPT_CAPT->no_dummy_boy}<br>

<input type="radio" name="dummy_boy" value="on"{$checked_dummy_boy}>
{$GAME_OPT_CAPT->dummy_boy}<br>

<input type="radio" name="dummy_boy" value="gm_login"{$checked_gm_login}>
{$GAME_OPT_MESS->gm_login} ({$GAME_OPT_CAPT->gm_login_header})<br>
<label for="gm_password">GM ログインパスワード：</label>
<input id="gm_password" type="password" name="gm_password" size="20"><br>
　　{$GAME_OPT_CAPT->gm_login_footer}
</td>
</tr>
<tr><td colspan="2"><hr></td></tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">{$GAME_OPT_MESS->open_vote}：</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="open_vote" value="on"{$checked}>
({$GAME_OPT_CAPT->open_vote})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">{$GAME_OPT_MESS->not_open_cast}：</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="not_open_cast" value="on"{$checked}>
({$GAME_OPT_CAPT->not_open_cast})
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$CAST_CONF->decide}人以上で{$GAME_OPT_MESS->decide}：</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="decide" value="on"{$checked}>
({$GAME_OPT_CAPT->decide})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$CAST_CONF->authority}人以上で{$GAME_OPT_MESS->authority}：</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="authority" value="on"{$checked}>
({$GAME_OPT_CAPT->authority})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$CAST_CONF->poison}人以上で{$GAME_OPT_MESS->poison}：</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="poison" value="on"{$checked}>
({$GAME_OPT_CAPT->poison})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14人もしくは{$CAST_CONF->cupid}人以上で<br>　{$GAME_OPT_MESS->cupid}：</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="cupid" value="on"{$checked}>
({$GAME_OPT_CAPT->cupid})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$CAST_CONF->boss_wolf}人以上で{$GAME_OPT_MESS->boss_wolf}：</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="boss_wolf" value="on"{$checked}>
({$GAME_OPT_CAPT->boss_wolf})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison_wolf){
    $checked = ($ROOM_CONF->default_poison_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison_wolf">{$CAST_CONF->poison_wolf}人以上で{$GAME_OPT_MESS->poison_wolf}：</label></td>
<td class="explain">
<input id="role_poison_wolf" type="checkbox" name="poison_wolf" value="on"{$checked}>
({$GAME_OPT_CAPT->poison_wolf})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->mania){
    $checked = ($ROOM_CONF->default_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_mania">{$CAST_CONF->mania}人以上で{$GAME_OPT_MESS->mania}：</label></td>
<td class="explain">
<input id="role_mania" type="checkbox" name="mania" value="on"{$checked}>
({$GAME_OPT_CAPT->mania})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->medium){
    $checked = ($ROOM_CONF->default_medium ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_medium">{$CAST_CONF->medium}人以上で{$GAME_OPT_MESS->medium}：</label></td>
<td class="explain">
<input id="role_medium" type="checkbox" name="medium" value="on"{$checked}>
({$GAME_OPT_CAPT->medium})
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->liar){
    $checked = ($ROOM_CONF->default_liar ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_liar">{$GAME_OPT_MESS->liar}：</label></td>
<td class="explain">
<input id="role_liar" type="checkbox" name="liar" value="on"{$checked}>
({$GAME_OPT_CAPT->liar})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->gentleman){
    $checked = ($ROOM_CONF->default_gentleman ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_gentleman">{$GAME_OPT_MESS->gentleman}：</label></td>
<td class="explain">
<input id="role_gentleman" type="checkbox" name="gentleman" value="on"{$checked}>
({$GAME_OPT_CAPT->gentleman})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->sudden_death){
    $checked = ($ROOM_CONF->default_sudden_death ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_sudden_death">{$GAME_OPT_MESS->sudden_death}：</label></td>
<td class="explain">
<input id="role_sudden_death" type="checkbox" name="sudden_death" value="on"{$checked}>
({$GAME_OPT_CAPT->sudden_death})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->perverseness){
    $checked = ($ROOM_CONF->default_perverseness ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_perverseness">{$GAME_OPT_MESS->perverseness}：</label></td>
<td class="explain">
<input id="role_perverseness" type="checkbox" name="perverseness" value="on"{$checked}>
({$GAME_OPT_CAPT->perverseness})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->full_mania){
    $checked = ($ROOM_CONF->default_full_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_full_mania">{$GAME_OPT_MESS->full_mania}：</label></td>
<td class="explain">
<input id="role_full_mania" type="checkbox" name="full_mania" value="on"{$checked}>
({$GAME_OPT_CAPT->full_mania})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->chaos){
    switch($ROOM_CONF->default_chaos){
    case 'chaos':
      $checked_chaos = ' checked';
      break;

    case 'chaosfull':
      if($ROOM_CONF->chaosfull){
	$checked_chaosfull = ' checked';
	break;
      }

    default:
      $checked_normal = ' checked';
      break;
    }

    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->chaos}：</label></td>
<td class="explain">
<input type="radio" name="chaos" value=""{$checked_normal}>
{$GAME_OPT_CAPT->no_chaos}<br>

<input type="radio" name="chaos" value="chaos"{$checked_chaos}>
{$GAME_OPT_CAPT->chaos}<br>

EOF;

    if($ROOM_CONF->chaosfull){
      echo <<<EOF
<input type="radio" name="chaos" value="chaosfull"{$checked_chaosfull}>
{$GAME_OPT_CAPT->chaosfull}
</td>

EOF;
    }
    echo '</tr>'."\n";

    if($ROOM_CONF->chaos_open_cast){
      switch($ROOM_CONF->default_chaos_open_cast){
      case 'full':
	$checked_chaos_open_cast_full = ' checked';
	break;

      case 'camp':
	$checked_chaos_open_cast_camp = ' checked';
	break;

      case 'role':
	$checked_chaos_open_cast_role = ' checked';
	break;

      default:
	$checked_chaos_open_cast_none = ' checked';
	break;
      }

      echo <<<EOF
<tr>
<td><label>{$GAME_OPT_MESS->chaos_open_cast}：</label></td>
<td class="explain">
<input type="radio" name="chaos_open_cast" value=""{$checked_chaos_open_cast_none}>
{$GAME_OPT_CAPT->chaos_not_open_cast}<br>

<input type="radio" name="chaos_open_cast" value="camp"{$checked_chaos_open_cast_camp}>
{$GAME_OPT_CAPT->chaos_open_cast_camp}<br>

<input type="radio" name="chaos_open_cast" value="role"{$checked_chaos_open_cast_role}>
{$GAME_OPT_CAPT->chaos_open_cast_role}<br>

<input type="radio" name="chaos_open_cast" value="full"{$checked_chaos_open_cast_full}>
{$GAME_OPT_CAPT->chaos_open_cast_full}
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->secret_sub_role){
      $checked = ($ROOM_CONF->default_secret_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="secret_sub_role">{$GAME_OPT_MESS->secret_sub_role}：</label></td>
<td class="explain">
<input id="secret_sub_role" type="checkbox" name="secret_sub_role" value="on"{$checked}>
({$GAME_OPT_CAPT->secret_sub_role})
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->no_sub_role){
      $checked = ($ROOM_CONF->default_no_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="no_sub_role">{$GAME_OPT_MESS->no_sub_role}：</label></td>
<td class="explain">
<input id="no_sub_role" type="checkbox" name="no_sub_role" value="on"{$checked}>
({$GAME_OPT_CAPT->no_sub_role})
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
<td><label for="quiz">{$GAME_OPT_MESS->quiz}：</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="quiz" value="on"{$checked}>
({$GAME_OPT_CAPT->quiz})<br>
<label for="quiz_password">GM ログインパスワード：</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
　　{$GAME_OPT_CAPT->gm_login_footer}
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->duel){
    $checked = ($ROOM_CONF->default_duel ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="duel">{$GAME_OPT_MESS->duel}：</label></td>
<td class="explain">
<input id="duel" type="checkbox" name="duel" value="on"{$checked}>
({$GAME_OPT_CAPT->duel})
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
