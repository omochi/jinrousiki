<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('room_class', 'user_class', 'icon_functions');
$INIT_CONF->LoadClass('SESSION', 'ROOM_CONF', 'GAME_CONF', 'MESSAGE');
$INIT_CONF->LoadRequest('RequestUserManager'); //引数を取得
$DB_CONF->Connect(); //DB 接続
$RQ_ARGS->entry ? EntryUser() : OutputEntryUserPage();
$DB_CONF->Disconnect(); //DB 接続解除

//-- 関数 --//
//ユーザを登録する
function EntryUser(){
  global $SERVER_CONF, $DB_CONF, $GAME_CONF, $MESSAGE, $RQ_ARGS, $SESSION;

  extract($RQ_ARGS->ToArray()); //引数を取得
  $back_url = '<br><a href="user_manager.php?room_no=' . $room_no . '">戻る</a>'; //バックリンク
  $is_dummy = false;
  if($user_no > 0){ //登録情報変更モード
    $back_url = '<br><a href="user_manager.php?room_no=' . $room_no . '&user_no=' . $user_no .
      '">戻る</a>'; //バックリンクを上書き
    if(! $DB_CONF->Transaction()){ //トランザクション開始
      $str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
      OutputActionResult('村人登録 [サーバエラー]', $str);
    }
    $query = "SELECT * FROM user_entry WHERE room_no = {$room_no} AND user_no = {$user_no}";
    $stack = FetchAssoc($query, true);
    if($stack['session_id'] != $SESSION->Get()){
      OutputActionResult('村人登録 [セッションエラー]', 'セッション ID が一致しません');
    }
    foreach($stack as $key => $value){
      if(array_key_exists($key, $RQ_ARGS)) $RQ_ARGS->$key = $value;
    }
    $query = 'SELECT u.uname, u.handle_name, u.sex, u.profile, u.role, u.icon_no, ' .
      'i.color, i.icon_name ' .
      'FROM user_entry AS u INNER JOIN user_icon AS i ON u.icon_no = i.icon_no ' .
      "WHERE u.room_no = {$room_no} AND u.user_no = {$user_no}";
    $target = FetchObject($query, 'User', true);
    $is_dummy = $target->IsDummyBoy();
    //PrintData($stack);
    //PrintData($RQ_ARGS);
  }
  if($GAME_CONF->trip && $trip != '') $uname .= ConvertTrip('#' . $trip); //トリップ変換

  //記入漏れチェック
  $title = '村人登録 [入力エラー]';
  $str   = 'が空です (空白と改行コードは自動で削除されます)' . $back_url;
  if($user_no < 1){
    if($uname       == '') OutputActionResult($title, 'ユーザ名'     . $str);
    if($password    == '') OutputActionResult($title, 'パスワード'   . $str);
  }
  if($handle_name == '') OutputActionResult($title, '村人の名前'   . $str);
  if($profile     == '') OutputActionResult($title, 'プロフィール' . $str);
  if(empty($sex))        OutputActionResult($title, '性別が入力されていません' . $back_url);
  if(empty($icon_no))    OutputActionResult($title, 'アイコン番号が入力されていません' . $back_url);

  //文字数制限チェック
  $str = '文字まで' . $back_url;
  if(strlen($uname) > $GAME_CONF->entry_uname_limit){
    OutputActionResult($title, 'ユーザ名は' . $GAME_CONF->entry_uname_limit . $str);
  }
  if(strlen($handle_name) > $GAME_CONF->entry_uname_limit){
    OutputActionResult($title, '村人の名前は' . $GAME_CONF->entry_uname_limit . $str);
  }
  if(strlen($profile) > $GAME_CONF->entry_profile_limit){
    OutputActionResult($title, 'プロフィールは' . $GAME_CONF->entry_profile_limit . $str);
  }

  //例外チェック
  if($uname == 'dummy_boy' || $uname == 'system'){
    OutputActionResult($title, 'ユーザ名「' . $uname . '」は使用できません' . $back_url);
  }
  if(! $is_dummy && ($handle_name == '身代わり君' || $handle_name == 'システム')){
    OutputActionResult($title, '村人名「' . $handle_name . '」は使用できません' . $back_url);
  }
  if($sex != 'male' && $sex != 'female') OutputActionResult($title, '無効な性別です' . $back_url);

  $query = 'SELECT COUNT(icon_no) FROM user_icon WHERE disable IS NOT TRUE AND icon_no = ';
  if($icon_no < 1 || FetchResult($query . $icon_no) < 1){
    OutputActionResult($title, '無効なアイコン番号です' . $back_url);
  }

  if(! $DB_CONF->Transaction()){ //トランザクション開始
    $str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
    OutputActionResult('村人登録 [サーバエラー]', $str);
  }

  $ROOM = RoomDataSet::LoadEntryUser($room_no); //現在の村情報を取得 (ロック付き)
  if(! $ROOM->IsBeforeGame() || $ROOM->status != 'waiting'){ //ゲーム開始判定
    OutputActionResult('村人登録 [入村不可]', 'すでにゲームが開始されています', '', true);
  }

  //DB から現在のユーザ情報を取得 (ロック付き)
  $request = new RequestBase();
  $request->room_no = $room_no;
  $request->entry_user = true;
  $USERS = new UserDataSet($request);
  //PrintData($USERS); //テスト用

  $user_count = $USERS->GetUserCount(); //現在の KICK されていない住人の数を取得
  if($user_no < 1 && $user_count >= $ROOM->max_user){ //定員オーバー判定
    OutputActionResult('村人登録 [入村不可]', '村が満員です。', '', true);
  }

  //重複チェック (比較演算子は大文字・小文字を区別しないのでクエリで直に判定する)
  $query  = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$room_no} AND ";
  $footer = '<br>別の名前にしてください。' . $back_url;

  //キックされた人と同じユーザ名
  if(FetchResult($query . "uname = '{$uname}' AND live = 'kick'") > 0){
    $str = 'キックされた人と同じユーザ名は使用できません (村人名は可)。';
    OutputActionResult('村人登録 [キックされたユーザ]', $str . $footer, '', true);
  }

  //ユーザ名・村人名
  $query .= "live = 'live' AND ";
  if($user_no > 0){
    if(FetchResult($query . "user_no <> '{$user_no}' AND handle_name = '{$handle_name}'") > 0){
      $str = '村人名が既に登録してあります。';
      OutputActionResult('村人登録 [重複登録エラー]', $str . $footer, '', true);
    }
    $str = "{$target->handle_name} さんが登録情報を変更しました\n";
    if($target->handle_name != $handle_name){
      $str .= "村人の名前：{$target->handle_name} → {$handle_name}\n";
    }
    if($target->icon_no != $icon_no){
      $icon_name = FetchResult('SELECT icon_name FROM user_icon WHERE icon_no = ' . $icon_no);
      $str .= "アイコン：No. {$target->icon_no} ({$target->icon_name}) → " .
	"{$icon_no} ({$icon_name})\n";
    }
    $stack = array();
    if($target->sex     != $sex)     $stack[] = '性別';
    if($target->profile != $profile) $stack[] = 'プロフィール';
    if($target->role    != $role)    $stack[] .= '役割希望';
    $str .= implode(', ', $stack);
    $ROOM->TalkBeforegame($str, $target->uname, $target->handle_name, $target->color);

    $query = "UPDATE user_entry SET handle_name = '{$handle_name}', sex = '{$sex}', ".
      "profile = '{$profile}', role = '{$role}', icon_no = '{$icon_no}' " .
      "WHERE room_no = {$room_no} AND user_no = {$user_no}";
    if(FetchBool($query, true)){
      $str = <<<EOF
登録データを変更しました。<br>
<form action="#" method="post">
<input type="button" value="ウィンドウを閉じる" onClick="window.close()">
</form>

EOF;
      OutputActionResult('村人登録 [登録情報変更]', $str);
    }
    else{
      $str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
      OutputActionResult('村人登録 [サーバエラー]', $str);
    }
  }
  elseif(FetchResult($query . "(uname = '{$uname}' OR handle_name = '{$handle_name}')") > 0){
    $str = 'ユーザ名、または村人名が既に登録してあります。';
    OutputActionResult('村人登録 [重複登録エラー]', $str . $footer, '', true);
  }
  //OutputActionResult('トリップテスト', $uname.'<br>'.$handle_name.$back_url); //テスト用

  //IP アドレスチェック
  $ip_address = $_SERVER['REMOTE_ADDR']; //ユーザの IP アドレスを取得
  if(! $SERVER_CONF->debug_mode){
    if($GAME_CONF->entry_one_ip_address &&
       FetchResult("{$query} ip_address = '{$ip_address}'") > 0){
      OutputActionResult('村人登録 [多重登録エラー]', '多重登録はできません。', '', true);
    }
    elseif(CheckBlackList()){
      OutputActionResult('村人登録 [入村制限]', '入村制限ホストです。', '', true);
    }
  }

  //DB にユーザデータを登録
  $user_no = count($USERS->names) + 1; //KICK された住人も含めた新しい番号を振る
  if(InsertUser($room_no, $uname, $handle_name, $password, $user_no, $icon_no, $profile,
		$sex, $role, $SESSION->Get(true))){
    //クッキーの初期化
    $ROOM->system_time = TZTime(); //現在時刻を取得
    $cookie_time = $ROOM->system_time - 3600;
    setcookie('scene',  '', $cookie_time);
    setcookie('vote_times', '', $cookie_time);
    setcookie('objection',  '', $cookie_time);

    $ROOM->Talk($handle_name . ' ' . $MESSAGE->entry_user); //入村メッセージ
    $ROOM->UpdateTime();
    $DB_CONF->Commit();

    $url = 'game_frame.php?room_no=' . $room_no;
    $user_count++;
    $str = $user_count . ' 番目の村人登録完了、村の寄り合いページに飛びます。<br>' .
      '切り替わらないなら <a href="' . $url. '">ここ</a> 。';
    OutputActionResult('村人登録', $str, $url, true);
  }
  else{
    $str = 'データベースサーバが混雑しています。<br>時間を置いて再度登録してください。';
    OutputActionResult('村人登録 [データベースサーバエラー]', $str, '', true);
  }
}

//ユーザ登録画面表示
function OutputEntryUserPage(){
  global $SERVER_CONF, $DB_CONF, $GAME_CONF, $ICON_CONF, $ROLE_DATA, $RQ_ARGS, $SESSION;

  extract($RQ_ARGS->ToArray()); //引数を取得
  if($user_no > 0){ //登録情報変更モード
    if(! $DB_CONF->Transaction()){ //トランザクション開始
      $str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
      OutputActionResult('村人登録 [サーバエラー]', $str);
    }
    $query = "SELECT * FROM user_entry WHERE room_no = {$room_no} AND user_no = {$user_no}";
    $stack = FetchAssoc($query, true);
    if($stack['session_id'] != $SESSION->Get()){
      OutputActionResult('村人登録 [セッションエラー]', 'セッション ID が一致しません');
    }
    foreach($stack as $key => $value){
      if(array_key_exists($key, $RQ_ARGS)) $RQ_ARGS->$key = $value;
    }
    //PrintData($stack);
    //PrintData($RQ_ARGS);
  }

  $ROOM = RoomDataSet::LoadEntryUserPage($room_no);
  $str = $room_no . ' 番地の村は';
  if(is_null($ROOM->id))  OutputActionResult('村人登録 [村番号エラー]', $str . '存在しません');
  if($ROOM->IsFinished()) OutputActionResult('村人登録 [入村不可]',     $str . '終了しました');
  if($ROOM->status != 'waiting'){
    OutputActionResult('村人登録 [入村不可]', $str . 'すでにゲームが開始されています。');
  }
  $ROOM->ParseOption(true);
  $path = 'img/entry_user';
  $male_checked   = '';
  $female_checked = '';
  switch($RQ_ARGS->sex){
  case 'male':
    $male_checked   = ' checked';
    break;

  case 'female':
    $female_checked = ' checked';
    break;
  }
  if($user_no > 0){
    $uname_form = <<<EOF
<tr>
<td class="img"><label for="uname"><img src="{$path}/uname.gif" alt="ユーザ名"></label></td>
<td>{$RQ_ARGS->uname}</td>
<td class="explain">普段は表示されず、他のユーザ名がわかるのは<br>死亡したときとゲーム終了後のみです</td>
</tr>

EOF;
  }
  elseif($GAME_CONF->trip){
    $uname_form = <<<EOF
<tr>
<td class="img"><label for="uname"><img src="{$path}/uname.gif" alt="ユーザ名"></label></td>
<td><input type="text" id="uname" name="uname" size="30" maxlength="30" value="{$RQ_ARGS->uname}"></td>
<td><label for="trip">＃</lable> <input type="text" id="trip" name="trip" size="15" maxlength="15" value="{$RQ_ARGS->trip}"></td>
</tr>
<tr>
<td></td>
<td colspan="2" class="explain">普段は表示されず、他のユーザ名がわかるのは死亡したときとゲーム終了後のみです<br>＃の右側はトリップ専用入力欄です。</td>
</tr>

EOF;
  }
  else{
    $uname_form = <<<EOF
<tr>
<td class="img"><label for="uname"><img src="{$path}/uname.gif" alt="ユーザ名"></label></td>
<td><input type="text" id="uname" name="uname" size="30" maxlength="30" value="{$RQ_ARGS->uname}"></td>
<td class="explain">普段は表示されず、他のユーザ名がわかるのは<br>死亡したときとゲーム終了後のみです(トリップ使用不可)</td>
</tr>

EOF;
  }


  if($user_no < 1){
    $password_form = <<<EOF
<tr>
<td class="img"><label for="password"><img src="{$path}/password.gif" alt="パスワード"></label></td>
<td><input type="password" id="password" name="password" size="30" maxlength="30" value=""></td>
<td class="explain">セッションが切れた場合のログイン時に使います<br> (暗号化されていないので要注意)</td>
</tr>

EOF;
  }
  OutputHTMLHeader($SERVER_CONF->title .'[村人登録]', 'entry_user');
  $action_url = 'user_manager.php?room_no=' . $ROOM->id;
  if($user_no > 0) $action_url .= '&user_no=' . $user_no;
  echo <<<EOF
<script type="text/javascript" src="javascript/submit_icon_search.js"></script>
</head>
<body>
<a href="./">←戻る</a><br>
<form method="POST" action="{$action_url}">
<div align="center">
<table class="main">
<tr><td><img src="{$path}/title.gif" alt="申請書"></td></tr>
<tr><td class="title">{$ROOM->name} 村<img src="{$path}/top.gif" alt="への住民登録を申請します"></td></tr>
<tr><td class="number">～{$ROOM->comment}～ [{$ROOM->id} 番地]</td></tr>
<tr><td>
<table class="input">
{$uname_form}
<tr>
<td class="img"><label for="handle_name"><img src="{$path}/handle_name.gif" alt="村人の名前"></label></td>
<td><input type="text" id="handle_name" name="handle_name" size="30" maxlength="30" value="{$RQ_ARGS->handle_name}"></td>
<td class="explain">村で表示される名前です</td>
</tr>
{$password_form}
<tr>
<td class="img"><img src="{$path}/sex.gif" alt="性別"></td>
<td class="img">
<label for="male"><img src="{$path}/sex_male.gif" alt="男性"><input type="radio" id="male" name="sex" value="male"{$male_checked}></label>
<label for="female"><img src="{$path}/sex_female.gif" alt="女性"><input type="radio" id="female" name="sex" value="female"{$female_checked}></label>
</td>
<td class="explain">特に意味は無いかも……</td>
</tr>
<tr>
<td class="img"><label for="profile"><img src="{$path}/profile.gif" alt="プロフィール"></label></td>
<td colspan="2">
<textarea id="profile" name="profile" cols="30" rows="2">{$RQ_ARGS->profile}</textarea>
</td>
</tr>
<tr>

EOF;

  if($ROOM->IsOption('wish_role')){
    echo <<<EOF
<td class="role"><img src="{$path}/role.gif" alt="役割希望"></td>
<td colspan="2">

EOF;

    $wish_role_list = array('none');
    if($ROOM->IsChaosWish()){
      array_push($wish_role_list, 'human', 'mage', 'necromancer', 'medium', 'priest', 'guard',
		 'common', 'poison', 'poison_cat', 'pharmacist', 'assassin', 'mind_scanner',
		 'jealousy', 'brownie', 'wizard', 'doll', 'escaper', 'wolf', 'mad', 'fox',
		 'child_fox', 'cupid', 'angel', 'quiz', 'vampire', 'chiroptera', 'fairy', 'ogre',
		 'yaksa', 'duelist', 'avenger', 'patron', 'mania', 'unknown_mania');
    }
    elseif($ROOM->IsOption('gray_random')){
      array_push($wish_role_list, 'human', 'wolf', 'mad', 'fox');
    }
    else{
      array_push($wish_role_list,  'human', 'wolf');
      if($ROOM->IsQuiz()){
	array_push($wish_role_list, 'mad', 'common', 'fox');
      }
      else{
	array_push($wish_role_list, 'mage', 'necromancer', 'mad', 'guard', 'common');
	if($ROOM->IsOption('detective')) $wish_role_list[] = 'detective_common';
	$wish_role_list[] = 'fox';
      }
      if($ROOM->IsOption('poison')) $wish_role_list[] = 'poison';
      if($ROOM->IsOption('assassin')) $wish_role_list[] = 'assassin';
      if($ROOM->IsOption('boss_wolf')) $wish_role_list[] = 'boss_wolf';
      if($ROOM->IsOption('poison_wolf')){
	array_push($wish_role_list, 'poison_wolf', 'pharmacist');
      }
      if($ROOM->IsOption('possessed_wolf')) $wish_role_list[] = 'possessed_wolf';
      if($ROOM->IsOption('sirius_wolf')) $wish_role_list[] = 'sirius_wolf';
      if($ROOM->IsOption('child_fox')) $wish_role_list[] = 'child_fox';
      if($ROOM->IsOption('cupid')) $wish_role_list[] = 'cupid';
      if($ROOM->IsOption('medium')) array_push($wish_role_list, 'medium', 'mind_cupid');
      if($ROOM->IsOptionGroup('mania') && ! in_array('mania', $wish_role_list)){
	$wish_role_list[] = 'mania';
      }
    }

    echo "<table>\n<tr>";
    $count = 0;
    foreach($wish_role_list as $role){
      if($count > 0 && $count % 4 == 0) echo "</tr>\n<tr>"; //4個ごとに改行
      $count++;
      $alt = '←' . ($role == 'none' ? '無し' : $ROLE_DATA->main_role_list[$role]);
      $checked = $RQ_ARGS->role == $role ? ' checked' : '';
      echo <<<EOF
<td><label for="{$role}"><input type="radio" id="{$role}" name="role" value="{$role}"{$checked}><img src="{$path}/role_{$role}.gif" alt="{$alt}"></label></td>
EOF;
    }
    echo "</tr>\n</table>";
  }
  else{
    echo '<td><input type="hidden" name="role" value="none">';
  }

  if(isset($RQ_ARGS->icon_no) && $RQ_ARGS->icon_no > 0){
    $icon_checked  = ' checked';
    $input_icon_no = $RQ_ARGS->icon_no;
  }
  else{
    $icon_checked  = '';
    $input_icon_no = '';
  }
  echo <<<EOF
</td>
</tr>
<tr>
<td class="submit" colspan="3">
<span class="explain">
ユーザ名、村人の名前、パスワードの前後の空白および改行コードは自動で削除されます
</span>
<input type="submit" id="entry" name="entry" value="村人登録申請"></td>
</tr>
</table>
</td></tr>

<tr><td>
<fieldset><legend><img src="{$path}/icon.gif" alt="アイコン"></legend>
<table class="icon">
<tr><td colspan="5">
<input id="fix_number" type="radio" name="icon_no"{$icon_checked}><label for="fix_number">手入力</label>
<input type="text" name="icon_no" size="10px" value="{$input_icon_no}">(半角英数で入力してください)
</td></tr>
<tr><td colspan="5">

EOF;
  OutputIconList('user_manager');
  echo <<<EOF
</tr></table>
</fieldset>
</td></tr>

</table></div></form>
</body></html>

EOF;
}
