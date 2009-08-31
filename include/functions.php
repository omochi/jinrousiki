<?php
//mbstring非対応の場合、エミュレータを使用する
if(! extension_loaded('mbstring')){
  require_once(dirname(__FILE__) . '/../module/mb-emulator.php');
}
require_once(dirname(__FILE__) .  '/setting.php');

//データベース接続
//$header : すでに HTMLヘッダが出力されて [いる / いない]
//$exit   : エラー時に [HTML を閉じて exit を返す / false で終了]
function ConnectDatabase($header = false, $exit = true){
  global $DB_CONF;

  //データベースサーバにアクセス
  $db_handle = mysql_connect($DB_CONF->host, $DB_CONF->user, $DB_CONF->password);
  if($db_handle){ //アクセス成功
    mysql_set_charset('ujis');
    if(mysql_select_db($DB_CONF->name, $db_handle)) //データベース接続
      return $db_handle; //成功したらハンドルを返して処理終了
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

//発言を DB に登録する (talk Table)
function InsertTalk($room_no, $date, $location, $uname, $time, $sentence, $font_type, $spend_time){
  mysql_query("INSERT INTO talk(room_no, date, location, uname, time,
				sentence, font_type, spend_time)
		VALUES($room_no, $date, '$location', '$uname', '$time',
				'$sentence', '$font_type', $spend_time)");
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

//DB から単体の値を取得する処理のラッパー関数
function FetchResult($query){
  $sql = mysql_query($query);
  return (mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false);
}

//DB から該当するデータの行数を取得する処理のラッパー関数
function FetchCount($query){
  return mysql_num_rows(mysql_query($query));
}

//DB から一次元の配列を取得する処理のラッパー関数
function FetchArray($query){
  $array = array();
  $sql   = mysql_query($query);
  $count = mysql_num_rows($sql);
  for($i = 0; $i < $count; $i++) array_push($array, mysql_result($sql, $i, 0));
  return $array;
}

//DB から単体の連想配列を取得する処理のラッパー関数
function FetchNameArray($query){
  $sql = mysql_query($query);
  return (mysql_num_rows($sql) > 0 ? mysql_fetch_assoc($sql) : false);
}

//DB から連想配列を取得する処理のラッパー関数
function FetchAssoc($query){
  $array = array();
  $sql   = mysql_query($query);
  while(($this_array = mysql_fetch_assoc($sql)) !== false) array_push($array, $this_array);
  return  $array;
}

//DB からオブジェクト形式の配列を取得する処理のラッパー関数
function FetchObjectArray($query, $class){
  $array = array();
  $sql   = mysql_query($query);
  while(($user = mysql_fetch_object($sql, $class)) !== false) array_push($array, $user);
  return $array;
}

//TZ 補正をかけた時刻を返す (環境変数 TZ を変更できない環境想定？)
function TZTime(){
  global $SERVER_CONF;
  return time() + $SERVER_CONF->offset_seconds;
  /* // ミリ秒対応のコード(案) 2009-08-08 enogu
     return preg_replace('/([0-9]+)( [0-9]+)?/i', '$$2.$$1', microtime()) + $SERVER_CONF->offset_seconds; // ミリ秒
     対応のコード(案) 2009-08-08 enogu
  */
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
			      'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

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
