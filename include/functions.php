<?php
//データベース接続
//$header : すでに HTMLヘッダが出力されて [いる / いない]
//$exit   : エラー時に [HTML を閉じて exit を返す / false で終了]
function ConnectDatabase($header = false, $exit = true){
  global $DB_CONF, $ENCODE;

  //データベースサーバにアクセス
  $db_handle = mysql_connect($DB_CONF->host, $DB_CONF->user, $DB_CONF->password);
  if($db_handle){ //アクセス成功
    mysql_set_charset('ujis');
    if(mysql_select_db($DB_CONF->name, $db_handle)){ //データベース接続
      // mysql_query("SET NAMES utf8");
      return $db_handle; //成功したらハンドルを返して処理終了
    }
    else{
      $error_title = 'データベース接続失敗';
      $error_name  =$DB_CONF->name;
    }
  }
  else{
    $error_title = 'MySQLサーバ接続失敗';
    $error_name  = $DB_CONF->host;
  }

  $error_message = $error_title . ': ' . $error_name; //エラーメッセージ作成
  if($header){
    echo '<font color="#FF0000">' . $error_message . '</font><br>';
    if($exit) OutputHTMLFooter($exit);
    return false;
  }
  OutputActionResult($error_title, $error_message);
}

//データベースとの接続を閉じる
function DisconnectDatabase($dbHandle){
  mysql_close($dbHandle);
}

//データベース登録のラッパー関数
function InsertDatabase($table, $items, $values){
  return mysql_query("INSERT INTO $table($items) VALUES($values)");
}

//発言をデータベースに登録する (talk Table)
function InsertTalk($room_no, $date, $location, $uname, $time, $sentence, $font_type, $spend_time){
  $items  = 'room_no, date, location, uname, time, sentence, font_type, spend_time';
  $values = "$room_no, $date, '$location', '$uname', '$time', '$sentence', '$font_type', $spend_time";
  return InsertDatabase('talk', $items, $values);
}

//セッションIDを新しくする(PHPのバージョンが古いとこの関数が無いので定義する)
if(! function_exists('session_regenerate_id')){
  function session_regenerate_id(){
    $QQ = serialize($_SESSION);
    session_destroy();
    session_id(md5(uniqid(rand(), 1)));
    session_start();
    $_SESSION = unserialize($QQ);
  }
}

//セッション認証 返り値 OK:ユーザ名 / NG: false
function CheckSession($session_id, $exit = true){
  global $RQ_ARGS;
  // $ip_address = $_SERVER['REMOTE_ADDR']; //IPアドレス認証は現在は行っていない

  //セッション ID による認証
  $query = "SELECT uname FROM user_entry WHERE room_no = {$RQ_ARGS->room_no} " .
    "AND session_id ='$session_id' AND user_no > 0";
  $array = FetchArray($query);
  if(count($array) == 1) return $array[0];

  if($exit){ //エラー処理
    OutputActionResult('セッション認証エラー',
		       'セッション認証エラー<br>'."\n" .
		       '<a href="index.php" target="_top">トップページ</a>から' .
		       'ログインしなおしてください');
  }
  return false;
}

//DBに登録されているセッションIDと被らないようにする
function GetUniqSessionID(){
  //セッション開始
  session_start();
  $session_id = '';

  do{
    session_regenerate_id();
    $session_id = session_id();
    $query = "SELECT COUNT(room_no) FROM user_entry, admin_manage " .
      "WHERE user_entry.session_id = '$session_id' OR admin_manage.session_id = '$session_id'";
  }while(FetchResult($query) > 0);
  return $session_id;
}

//DB 問い合わせ処理のラッパー関数
function SendQuery($query){
  $sql = mysql_query($query);
  if($sql) return $sql;
  $backtrace = debug_backtrace();
  echo "SQLエラー：{$backtrace[1]['function']}()：{$backtrace[0]['line']}：$query <br>";
  return false;
}

//DB から単体の値を取得する処理のラッパー関数
function FetchResult($query){
  if(($sql = SendQuery($query)) === false) return false;
  $data = (mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false);
  mysql_free_result($sql);
  return $data;
}

//DB から該当するデータの行数を取得する処理のラッパー関数
function FetchCount($query){
  if(($sql = SendQuery($query)) === false) return false;
  $data = mysql_num_rows($sql);
  mysql_free_result($sql);
  return $data;
}

//DB から一次元の配列を取得する処理のラッパー関数
function FetchArray($query){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  $count = mysql_num_rows($sql);
  for($i = 0; $i < $count; $i++) $array[] = mysql_result($sql, $i, 0);
  mysql_free_result($sql);
  return $array;
}

//DB から単体の連想配列を取得する処理のラッパー関数
function FetchNameArray($query){
  if(($sql = SendQuery($query)) === false) return false;
  $array = (mysql_num_rows($sql) > 0 ? mysql_fetch_assoc($sql) : false);
  mysql_free_result($sql);
  return $array;
}

//DB から連想配列を取得する処理のラッパー関数
function FetchAssoc($query){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($assoc = mysql_fetch_assoc($sql)) !== false) $array[] = $assoc;
  mysql_free_result($sql);
  return $array;
}

//DB からオブジェクト形式の配列を取得する処理のラッパー関数
function FetchObjectArray($query, $class){
  $array = array();
  if(($sql = SendQuery($query)) === false) return $array;
  while(($object = mysql_fetch_object($sql, $class)) !== false) $array[] = $object;
  mysql_free_result($sql);
  return $array;
}

//TZ 補正をかけた時刻を返す (環境変数 TZ を変更できない環境想定？)
function TZTime(){
  global $SERVER_CONF;
  $cur_time = time();
  if ($SERVER_CONF->adjust_time_difference) $curtime += $SERVER_CONF->offset_seconds;
  return $cur_time;
  /* // ミリ秒対応のコード(案) 2009-08-08 enogu
     return preg_replace('/([0-9]+)( [0-9]+)?/i', '$$2.$$1', microtime()) + $SERVER_CONF->offset_seconds; // ミリ秒
     対応のコード(案) 2009-08-08 enogu
  */
}

//TIMESTAMP 形式の時刻を変換する
function ConvertTimeStamp($time){
  global $SERVER_CONF;

  $str = strtotime($time);
  if ($SERVER_CONF->adjust_time_difference) {
    $str += $SERVER_CONF->offset_seconds;
    return gmdate('Y/m/d (D) H:i:s', $str);
  }
  // else
  return date('Y/m/d (D) H:i:s', $str);
}

//時間(秒)を変換する
function ConvertTime($seconds){
  $sentence = '';
  $hours    = 0;
  $minutes  = 0;

  if($seconds >= 60){
    $minutes = floor($seconds / 60);
    $seconds %= 60;
  }
  if($minutes >= 60){
    $hours = floor($minutes / 60);
    $minutes %= 60;
  }

  if($hours   > 0) $sentence .= $hours   . '時間';
  if($minutes > 0) $sentence .= $minutes . '分';
  if($seconds > 0) $sentence .= $seconds . '秒';
  return $sentence;
}

//POSTされたデータの文字コードを統一する
function EncodePostData(){
  global $ENCODE;

  foreach($_POST as $key => $value){
    $encode_type = mb_detect_encoding($value, 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    if($encode_type != '' && $encode_type != $ENCODE)
      $_POST[$key] = mb_convert_encoding($value, $ENCODE, $encode_type);
  }
}

//入力禁止文字のチェック
function CheckForbiddenStrings($str){
  return (strstr($str, "'") || strstr($str, "\\"));
}

//特殊文字のエスケープ処理
//htmlentities() を使うと文字化けを起こしてしまうようなので敢えてべたに処理
function EscapeStrings(&$str, $trim = true){
  if(get_magic_quotes_gpc()) $str = stripslashes($str); // \ を自動でつける処理系対策
  // $str = htmlentities($str, ENT_QUOTES); //UTF に移行したら機能する？
  $replace_list = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;',
			'\\' => '&yen;', '"' => '&quot;', "'" => '&#039;');
  $str = strtr($str, $replace_list);
  $str = ($trim ? trim($str) : str_replace(array("\r\n", "\r", "\n"), "\n", $str));
  return $str;
}

//改行コードを <br> に変換する (nl2br() だと <br /> なので HTML 4.01 だと不向き)
function LineToBR(&$str){
  $str = str_replace("\n", '<br>', $str);
  return $str;
}

//パスワード暗号化
function CryptPassword($raw_password){
  global $SERVER_CONF;
  return sha1($SERVER_CONF->hash_salt . $raw_password);
}

//ゲームオプションの画像タグを作成する
function MakeGameOptionImage($game_option, $option_role = ''){
  global $GAME_CONF, $ROOM_IMG, $MESSAGE;

  $str = '';
  if(strpos($game_option, 'wish_role') !== false){
    $str .= $ROOM_IMG->GenerateTag('wish_role', '役割希望制');
  }
  if(strpos($game_option, 'real_time') !== false){ //実時間の制限時間を取得
    $real_time_str = strstr($game_option, 'real_time');
    sscanf($real_time_str, "real_time:%d:%d", &$day, &$night);
    $sentence = "リアルタイム制　昼： $day 分　夜： $night 分";
    $str .= $ROOM_IMG->GenerateTag('real_time', $sentence) . '['. $day . '：' . $night . ']';
  }

  $option_list = explode(' ', $game_option . ' ' .$option_role);
  // print_r($option_list);
  $display_order_list = array('dummy_boy', 'gm_login', 'open_vote', 'not_open_cast', 'decide',
			      'authority', 'poison', 'cupid', 'boss_wolf', 'poison_wolf',
			      'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			      'perverseness', 'full_mania', 'quiz', 'duel', 'chaos', 'chaosfull',
			      'chaos_open_cast', 'chaos_open_cast_camp', 'chaos_open_cast_role',
			      'secret_sub_role', 'no_sub_role');

  foreach($display_order_list as $this_option){
    if(! in_array($this_option, $option_list)) continue;
    $this_str = 'game_option_' . $this_option;
    if($MESSAGE->$this_str == '') continue;

    $sentence = '';
    if($this_option == 'cupid'){
      $sentence = '14人または' . $GAME_CONF->$this_option . '人以上で';
    }
    elseif(is_integer($GAME_CONF->$this_option)){
      $sentence = $GAME_CONF->$this_option . '人以上で';
    }
    $sentence .= $MESSAGE->$this_str;

    $str .= $ROOM_IMG->GenerateTag($this_option, $sentence);
  }

  /*
  $text_game_option_list = array();
  foreach($text_game_option_list as $this_option){
    if(strpos($game_option, $this_option) !== false){
      $message_str = 'game_option_' . $this_option;
      $str .= '[' . $MESSAGE->$message_str . ']';
    }
  }
  */

  /*
  $text_option_role_list = array(, 'open_sub_role');
  foreach($text_option_role_list as $this_option){
    if(ereg("{$this_option}([[:space:]]+[^[[:space:]]]*)?", $option_role)){
      $message_str = 'game_option_' . $this_option;
      $str .= '[' . $MESSAGE->$message_str . ']';
    }
  }
  */

  return $str;
}

//共通 HTML ヘッダ出力
function OutputHTMLHeader($title, $css = 'action'){
  global $ENCODE, $CSS_PATH;

  $path = ($CSS_PATH == '' ? 'css' : $CSS_PATH);
  echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset={$ENCODE}">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>{$title}</title>
<link rel="stylesheet" href="{$path}/{$css}.css">

EOF;
}

//結果ページ HTML ヘッダ出力
function OutputActionResultHeader($title, $url = ''){
  global $ROOM;

  OutputHTMLHeader($title);
  if($url != '') echo '<meta http-equiv="Refresh" content="1;URL=' . $url . '">'."\n";
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo '</head><body>'."\n";
}

//結果ページ出力
function OutputActionResult($title, $body, $url = '', $unlock = false){
  global $dbHandle;

  if($unlock) mysql_query('UNLOCK TABLES'); //ロック解除
  if($dbHandle != '') DisconnectDatabase($dbHandle); //DB 接続解除

  OutputActionResultHeader($title, $url);
  echo $body . "\n";
  OutputHTMLFooter(true);
}

//HTML フッタ出力
function OutputHTMLFooter($exit = false){
  echo '</body></html>'."\n";
  if($exit) exit;
}
?>
