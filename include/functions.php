<?php
//mbstring非対応の場合、エミュレータを使用する
if(! extension_loaded('mbstring')){
  require_once(dirname(__FILE__) . '/../module/mb-emulator.php');
}
require_once(dirname(__FILE__) .  '/setting.php');

//データベース接続
function ConnectDatabase($header = false, $exit = true){
  global $db_host, $db_uname, $db_pass, $db_name;

  if(! ($db_handle = mysql_connect($db_host, $db_uname, $db_pass))){
    if($header){
      echo "<font color=\"#FF0000\">MySQL接続失敗: $db_host</font><br>";
      if($exit)
	OutputHTMLFooter($exit);
      else
	return false;
    }
    else{
      OutputActionResult('MySQL接続失敗', "MySQL接続失敗: $db_host");
    }
  }

  mysql_set_charset('ujis');
  if(! mysql_select_db($db_name, $db_handle)){
    if($header){
      echo "<font color=\"#FF0000\">データベース接続失敗: $db_name</font><br>";
      if($exit)
	OutputHTMLFooter($exit);
      else
	return false;
    }
    else{
      OutputActionResult('データベース接続失敗', "データベース接続失敗: $db_name");
    }
  }

  return $db_handle;
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

//TZ 補正をかけた時刻を返す (環境変数 TZ を変更できない環境想定)
function TZTime(){
  global $OFFSET_SECONDS;
  return time() + $OFFSET_SECONDS;
}

//時間(秒)を変換する
function ConvertTime($time){
  $minutes = floor($time / 60);
  if($minutes >= 60){
    $hours   = floor($minutes / 60);
    $minutes = $minutes % 60;
  }
  $seconds = $minutes % 60;

  $sentence = '';
  if($hours   > 0) $sentence .= $hours   . '時間';
  if($minutes > 0) $sentence .= $minutes . '分';
  if($seconds > 0) $sentence .= $seconds . '秒';
  return $sentence;
}

//POSTされたデータをEUC-JPに統一する
function ToEUC_PostData(){
  foreach($_POST as $key => $value){
    $encode_type = mb_detect_encoding($value, 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    if(($encode_type != '') && ($encode_type != 'EUC-JP'))
      $_POST[$key] = mb_convert_encoding($value, 'EUC-JP', $encode_type);
  }
}

//入力禁止文字のチェック
function CheckForbiddenStrings($str){
  return (strstr($str, "'") || strstr($str, "\\"));
}

//特殊文字のエスケープ処理
function EscapeStrings(&$str, $type = ''){
  if($type == 'full' || $type == 'backslash') $str = str_replace('\\', '\\\\', $str);
  if($type != 'backslash'){
    $str = str_replace('&', '&amp;', $str);
    $str = str_replace('<', '&lt;',  $str);
    $str = str_replace('>', '&gt;',  $str);
    if($type == 'full' || $type != 'single') $str = str_replace("'", "\\'", $str);
  }
}

//改行コードを LF に統一する
function ConvertLF(&$str){
  $str = str_replace("\r\n", "\n", $str);
  $str = str_replace("\r"  , "\n", $str);
}

//改行コードを <br> に変換する (nl2br() だと <br /> なので HTML 4.01 だと不向き)
function LineToBR(&$str){
  $str = str_replace("\n", '<br>', $str);
}

//共通 HTML ヘッダ出力
function OutputHTMLHeader($title, $css = 'action', $path = 'css'){
  echo <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>{$title}</title>
<link rel="stylesheet" href="{$path}/{$css}.css">

EOF;
}

//結果ページ HTML ヘッダ出力
function OutputActionResultHeader($title, $url = ''){
  global $day_night;

  OutputHTMLHeader($title);
  if($url != '') echo '<meta http-equiv="Refresh" content="1;URL=' . $url . '">'."\n";
  if($day_night != '')  echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
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
