<?php
require_once(dirname(__FILE__) . '/include/functions.php');

EncodePostData();//ポストされた文字列をエンコードする

if($_GET['room_no'] == ''){
  $sentence = 'エラー：村の番号が正常ではありません。<br>'."\n".'<a href="index.php">←戻る</a>';
  OutputActionResult('村人登録 [村番号エラー]', $sentence);
}

$dbHandle = ConnectDatabase(); //DB 接続

if($_POST['command'] == 'entry'){
  EntryUser((int)$_GET['room_no'], $_POST['uname'], $_POST['handle_name'], (int)$_POST['icon_no'],
	    $_POST['profile'], $_POST['password'], $_POST['sex'], $_POST['role']);
}
else{
  OutputEntryUserPage((int)$_GET['room_no']);
}

DisconnectDatabase($dbHandle); //DB 接続解除

// 関数 //
//ユーザを登録する
function EntryUser($room_no, $uname, $handle_name, $icon_no, $profile, $password, $sex, $role){
  global $GAME_CONF, $MESSAGE;

  //トリップ＆エスケープ処理
  ConvertTrip(&$uname);
  ConvertTrip(&$handle_name);
  EscapeStrings(&$profile, false);
  EscapeStrings(&$password);

  //記入漏れチェック
  if($uname == '' || $handle_name == '' || $icon_no == '' || $profile == '' ||
     $password == '' || $sex == '' || $role == ''){
    OutputActionResult('村人登録 [入力エラー]',
		       '記入漏れがあります。<br>'."\n" .
		       '全部入力してください (空白と改行コードは自動で削除されます)。');
  }

  //システムユーザチェック
  if($uname == 'dummy_boy' || $uname == 'system' ||
     $handle_name == '身代わり君' || $handle_name == 'システム'){
    OutputActionResult('村人登録 [入力エラー]',
		       '下記の名前は登録できません。<br>'."\n" .
		       'ユーザ名：dummy_boy or system<br>'."\n" .
		       '村人の名前：身代わり君 or システム');
  }

  //項目被りチェック
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no";

  //ユーザ名、村人名
  $sql = mysql_query("$query AND (uname = '$uname' OR handle_name = '$handle_name') AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0){
    OutputActionResult('村人登録 [重複登録エラー]',
		       'ユーザ名、または村人名が既に登録してあります。<br>'."\n" .
		       '別の名前にしてください。');
  }

  //キックされた人と同じユーザ名
  $sql = mysql_query("$query AND uname = '$uname' AND user_no = -1");
  if(mysql_result($sql, 0, 0) != 0){
    OutputActionResult('村人登録 [キックされたユーザ]',
		       'キックされた人と同じユーザ名は使用できません。 (村人名は可)<br>'."\n" .
		       '別の名前にしてください。');
  }

  //IPアドレスチェック
  $ip_address = $_SERVER['REMOTE_ADDR']; //ユーザのIPアドレスを取得
  if($GAME_CONF->entry_one_ip_address){
    $sql = mysql_query("$query AND ip_address = '$ip_address' AND user_no > 0");
    if(mysql_result($sql, 0, 0) != 0){
      OutputActionResult('村人登録 [多重登録エラー]', '多重登録はできません。');
    }
  }

  //テーブルをロック
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, talk WRITE, admin_manage READ')){
    OutputActionResult('村人登録 [サーバエラー]',
		       'サーバが混雑しています。<br>'."\n" .
		       '再度登録してください');
  }

  //クッキーの削除
  $system_time = TZTime(); //現在時刻を取得
  $cookie_time = $system_time - 3600;
  setcookie('day_night',  '', $cookie_time);
  setcookie('vote_times', '', $cookie_time);
  setcookie('objection',  '', $cookie_time);

  //DBからユーザNoを降順に取得
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = $room_no
			AND user_no > 0 ORDER BY user_no DESC");
  $array = mysql_fetch_assoc($sql);
  $user_no = (int)$array['user_no'] + 1; //最も大きい No + 1

  //DBから最大人数を取得
  $sql = mysql_query("SELECT day_night, status, max_user FROM room WHERE room_no = $room_no");
  $array  = mysql_fetch_assoc($sql);
  $day_night = $array['day_night'];
  $status    = $array['status'];
  $max_user  = $array['max_user'];

  //定員オーバーしているとき
  if($user_no > $max_user || $day_night != 'beforegame' || $status != 'waiting'){
    OutputActionResult('村人登録 [入村不可]',
		       '村が既に満員か、ゲームが開始されています。', '', true);
  }

  //セッション開始
  session_start();
  $session_id = '';

  do{ //DB に登録されているセッション ID と被らないようにする
    session_regenerate_id();
    $session_id = session_id();
    $sql = mysql_query("SELECT COUNT(room_no) FROM user_entry, admin_manage
			WHERE user_entry.session_id = '$session_id'
			OR admin_manage.session_id = '$session_id'");
  }while(mysql_result($sql, 0, 0) != 0);

  //DB にユーザデータ登録
  $entry = mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name,
			icon_no, profile, sex, password, role, live, session_id,
			last_words, ip_address, last_load_day_night)
			VALUES($room_no, $user_no, '$uname', '$handle_name', $icon_no,
			'$profile', '$sex', '$password', '$role', 'live',
			'$session_id', '', '$ip_address', 'beforegame')");

  //入村メッセージ
  InsertTalk($room_no, 0, 'beforegame system', 'system', $system_time,
	     $handle_name . ' ' . $MESSAGE->entry_user, NULL, 0);

  mysql_query('COMMIT'); //一応コミット
  //登録が成功していて、今回のユーザが最後のユーザなら募集を終了する
  // if($entry && ($user_no == $max_user))
  //   mysql_query("update room set status = 'playing' where room_no = $room_no");

  if($entry){
    $url = "game_frame.php?room_no=$room_no";
    OutputActionResult('村人登録',
		       $user_no . ' 番目の村人登録完了、村の寄り合いページに飛びます。<br>'."\n" .
		       '切り替わらないなら <a href="' . $url. '">ここ</a> 。',
		       $url, true);
  }
  else{
    OutputActionResult('村人登録 [データベースサーバエラー]',
		       'データベースサーバが混雑しています。<br>'."\n" .
		       '時間を置いて再度登録してください。', '', true);
  }
  mysql_query('UNLOCK TABLES'); //ロック解除
}

//トリップ変換
/*
  変換テスト結果＠2ch (2009/07/26)
  [入力文字列] => [変換結果] (ConvetTrip()の結果)
  test#test                     => test ◆.CzKQna1OU (test◆.CzKQna1OU)
  テスト#テスト                 => テスト ◆SQ2Wyjdi7M (テスト◆SQ2Wyjdi7M)
  てすと＃てすと                => てすと ◆ZUNa78GuQc (てすと◆ZUNa78GuQc)
  てすとテスト#てすと＃テスト   => てすとテスト ◆TBYWAU/j2qbJ (てすとテスト◆sXitOlnF0g)
  テストてすと＃テストてすと    => テストてすと ◆RZ9/PhChteSA (テストてすと◆XuUGgmt7XI)
  テストてすと＃テストてすと#   => テストてすと ◆rtfFl6edK5fK (テストてすと◆XuUGgmt7XI)
  テストてすと＃テストてすと＃  => テストてすと ◆rtfFl6edK5fK (テストてすと◆XuUGgmt7XI)
*/
function ConvertTrip(&$str){
  global $ENCODE, $GAME_CONF;

  if($GAME_CONF->trip){ //まだ実装されていません
    OutputActionResult('村人登録 [入力エラー]',
                       'トリップ変換処理は実装されていません。<br>'."\n" .
                       '管理者に問い合わせてください。');

    //トリップ関連のキーワードを置換
    $str = str_replace(array('◆', '＃'), array('◇', '#'), $str);
    if(($trip_start = mb_strpos($str, '#')) !== false){ //トリップキーの位置を検索
      $name = mb_substr($str, 0, $trip_start);
      $key  = mb_substr($str, $trip_start + 1);
      #echo 'trip_start: '.$trip_start.', name: '.$name.', key:'.$key.'<br>'; //デバッグ用

      //文字コードを変換
      $key  = mb_convert_encoding($key, 'SJIS', $ENCODE);
      $salt = substr($key.'H.', 1, 2);

      //$salt =~ s/[^\.-z]/\./go;にあたる箇所
      $pattern = '/[\x00-\x20\x7B-\xFF]/';
      $salt = preg_replace($pattern, '.', $salt);

      //特殊文字の置換
      $from_list = array(':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`');
      $to_list   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'a', 'b', 'c', 'd', 'e', 'f');
      $salt = str_replace($from_list, $to_list, $salt);

      $trip = crypt($key, $salt);
      $str = $name.'◆'.substr($trip, -10);
    }
    #echo 'result: '.$str.'<br>'; //デバッグ用
  }
  elseif(strpos($str, '#') !== false || strpos($str, '＃') !== false){
    OutputActionResult('村人登録 [入力エラー]',
		       'トリップは使用不可です。<br>'."\n" .
		       '"#" 又は "＃" の文字も使用不可です。');
  }

  EscapeStrings(&$str); //特殊文字のエスケープ
}

//ユーザ登録画面表示
function OutputEntryUserPage($room_no){
  global $SERVER_CONF, $ICON_CONF;

  $sql = mysql_query("SELECT room_name, room_comment, status, game_option, option_role
			FROM room WHERE room_no = $room_no");
  if(mysql_num_rows($sql) == 0){
    OutputActionResult('村人登録 [村番号エラー]', "No.$room_no 番地の村は存在しません。");
  }

  $array = mysql_fetch_assoc($sql);
  $room_name    = $array['room_name'];
  $room_comment = $array['room_comment'];
  $status       = $array['status'];
  $game_option  = $array['game_option'];
  $option_role  = $array['option_role'];
  if($status != 'waiting'){
    OutputActionResult('村人登録 [入村不可]', '村が既に満員か、ゲームが開始されています。');
  }

  //ユーザアイコン一覧
  $sql_icon = mysql_query("SELECT icon_no, icon_name, icon_filename, icon_width, icon_height, color
				FROM user_icon WHERE icon_no > 0 ORDER BY icon_no");
  $trip_str = '(トリップ使用' . ($GAME_CONF->trip ? '可能' : '不可') . ')';

  OutputHTMLHeader($SERVER_CONF->title .'[村人登録]', 'entry_user');
  echo <<<HEADER
</head>
<body>
<a href="index.php">←戻る</a><br>
<form method="POST" action="user_manager.php?room_no=$room_no">
<input type="hidden" name="command" value="entry">
<div align="center">
<table class="main">
<tr><td><img src="img/entry_user/title.gif"></td></tr>
<tr><td class="title">$room_name 村<img src="img/entry_user/top.gif"></td></tr>
<tr><td class="number">〜{$room_comment}〜 [{$room_no} 番地]</td></tr>
<tr><td>
<table class="input">
<tr>
<td class="img"><img src="img/entry_user/uname.gif"></td>
<td><input type="text" name="uname" size="30" maxlength="30"></td>
<td class="explain">普段は表示されず、他のユーザ名がわかるのは<br>死亡したときとゲーム終了後のみです{$trip_str}</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/handle_name.gif"></td>
<td><input type="text" name="handle_name" size="30" maxlength="30"></td>
<td class="explain">村で表示される名前です</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/password.gif"></td>
<td><input type="password" name="password" size="30" maxlength="30"></td>
<td class="explain">セッションが切れた場合にログイン時に使います<br> (暗号化されていないので要注意)</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/sex.gif"></td>
<td class="img">
<label for="male"><img src="img/entry_user/sex_male.gif"><input type="radio" id="male" name="sex" value="male"></label>
<label for="female"><img src="img/entry_user/sex_female.gif"><input type="radio" id="female" name="sex" value="female"></label>
</td>
<td class="explain">特に意味は無いかも……</td>
</tr>
<tr>
<td class="img"><img src="img/entry_user/profile.gif"></td>
<td colspan="2">
<textarea name="profile" cols="30" rows="2"></textarea>
<input type="hidden" name="role" value="none">
</td>
</tr>

HEADER;

  if(strpos($game_option, 'wish_role') !== false){
    echo <<<IMAGE
<tr>
<td class="role"><img src="img/entry_user/role.gif"></td>
<td colspan="2">

IMAGE;

    if(strpos($game_option, 'quiz') !== false){
      $wish_role_list = array('none', 'human', 'wolf', 'mad', 'common', 'fox');
    }
    else{
      $wish_role_list = array('none', 'human', 'wolf', 'mage', 'necromancer',
			      'mad', 'guard', 'common', 'fox');
    }
    if(strpos($option_role, 'poison')      !== false) array_push($wish_role_list, 'poison');
    if(strpos($option_role, 'cupid')       !== false) array_push($wish_role_list, 'cupid');
    if(strpos($option_role, 'boss_wolf')   !== false) array_push($wish_role_list, 'boss_wolf');
    if(strpos($option_role, 'poison_wolf') !== false){
      array_push($wish_role_list, 'poison_wolf');
      array_push($wish_role_list, 'pharmacist');
    }
    if(strpos($option_role, 'mania')       !== false) array_push($wish_role_list, 'mania');
    if(strpos($option_role, 'medium')      !== false){
      array_push($wish_role_list, 'medium');
      array_push($wish_role_list, 'fanatic_mad');
    }

    $count = 0;
    foreach($wish_role_list as $this_role){
      echo <<<TAG
<label for="{$this_role}"><img src="img/entry_user/role_{$this_role}.gif"><input type="radio" id="{$this_role}" name="role" value="{$this_role}"></label>

TAG;
      if(++$count % 4 == 0) echo '<br>'; //4個ごとに改行
    }
    echo '</td>';
  }
  else{
    echo '<input type="hidden" name="role" value="none">';
  }

  echo <<<BODY
</tr>
<tr>
<td class="submit" colspan="3">
<span class="explain">
ユーザ名、村人の名前、パスワードの前後の空白および改行コードは自動で削除されます
</span>
<input type="submit" value="村人登録申請"></td>
</tr>
</table>
</td></tr>

<tr><td>
<fieldset><legend><img src="img/entry_user/icon.gif"></legend>
<table class="icon">
<tr>

BODY;

  //アイコンの出力
  $count = 0;
  while(($array = mysql_fetch_assoc($sql_icon)) !== false){
    $icon_no       = $array['icon_no'];
    $icon_name     = $array['icon_name'];
    $icon_filename = $array['icon_filename'];
    $icon_width    = $array['icon_width'];
    $icon_height   = $array['icon_height'];
    $color         = $array['color'];
    $icon_location = $ICON_CONF->path . '/' . $icon_filename;

    echo <<<ICON
<td><label for="$icon_no"><img src="$icon_location" width="$icon_width" height="$icon_height" style="border-color:$color;">
$icon_name<br><font color="$color">◆</font><input type="radio" id="$icon_no" name="icon_no" value="$icon_no"></label></td>

ICON;
    if(++$count % 5 == 0) echo '</tr><tr>'; //5個ごとに改行
  }

  echo <<<FOOTER
</tr></table>
</fieldset>
</td></tr>

</table></div></form>
</body></html>

FOOTER;
}
?>
