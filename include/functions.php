<?php
//-- テキスト処理クラス --//
class Text {
  //パスワード暗号化
  static function CryptPassword($str){
    return sha1(ServerConfig::$salt . $str);
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
  static function ConvertTrip($str){
    if (GameConfig::$trip) {
      if (get_magic_quotes_gpc()) $str = stripslashes($str); // \ を自動でつける処理系対策
      //トリップ関連のキーワードを置換
      $str = str_replace(array('◆', '＃'), array('◇', '#'), $str);
      if (($trip_start = mb_strpos($str, '#')) !== false) { //トリップキーの位置を検索
	$name = mb_substr($str, 0, $trip_start);
	$key  = mb_substr($str, $trip_start + 1);
	//PrintData("{$trip_start}, name: {$name}, key: {$key}", 'Trip Start'); //テスト用
	$key = mb_convert_encoding($key, 'SJIS', ServerConfig::$encode); //文字コードを変換

	if (GameConfig::$trip_2ch && strlen($key) >= 12) {
	  $trip_mark = substr($key, 0, 1);
	  if ($trip_mark == '#' || $trip_mark == '$') {
	    if (preg_match('|^#([[:xdigit:]]{16})([./0-9A-Za-z]{0,2})$|', $key, $stack)) {
	      $trip = substr(crypt(pack('H*', $stack[1]), "{$stack[2]}.."), -12);
	    }
	    else {
	      $trip = '???';
	    }
	  }
	  else {
	    $trip = str_replace('+', '.', substr(base64_encode(sha1($key, true)), 0, 12));
	  }
	}
	else {
	  $salt = substr($key . 'H.', 1, 2);

	  //$salt =~ s/[^\.-z]/\./go; にあたる箇所
	  $pattern = '/[\x00-\x20\x7B-\xFF]/';
	  $salt = preg_replace($pattern, '.', $salt);

	  //特殊文字の置換
	  $from_list = array(':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`');
	  $to_list   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'a', 'b', 'c', 'd', 'e', 'f');
	  $salt = str_replace($from_list, $to_list, $salt);

	  $trip = substr(crypt($key, $salt), -10);
	}
	$str = $name . '◆' . $trip;
      }
      //PrintData($str, 'Result'); //テスト用
    }
    elseif (strpos($str, '#') !== false || strpos($str, '＃') !== false) {
      $sentence = 'トリップは使用不可です。<br>' . "\n" . '"#" 又は "＃" の文字も使用不可です。';
      HTML::OutputResult('村人登録 [入力エラー]', $sentence);
    }

    return self::Escape($str); //特殊文字のエスケープ
  }

  //-- 更新系 --//

  //特殊文字のエスケープ処理
  //htmlentities() を使うと文字化けを起こしてしまうようなので敢えてべたに処理
  static function Escape(&$str, $trim = true){
    if (is_array($str)){
      $result = array();
      foreach ($str as $item) $result[] = self::Escape($item);
      return $result;
    }
    if (get_magic_quotes_gpc()) $str = stripslashes($str); //'\' を自動でつける処理系対策
    //$str = htmlentities($str, ENT_QUOTES); //UTF に移行したら機能する？
    $replace_list = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;',
			  '\\' => '&yen;', '"' => '&quot;', "'" => '&#039;');
    $str = strtr($str, $replace_list);
    $str = $trim ? trim($str) : str_replace(array("\r\n", "\r", "\n"), "\n", $str);
    return $str;
  }

  //改行コードを <br> に変換する (PHP5.3 以下の nl2br() だと <br /> 固定なので HTML 4.01 だと不向き)
  static function LineToBR(&$str){
    $str = str_replace("\n", '<br>', $str);
    return $str;
  }

  //POST されたデータの文字コードを統一する
  static function EncodePostData(){
    foreach ($_POST as $key => $value) {
      $encode = @mb_detect_encoding($value, 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
      if ($encode != '' && $encode != ServerConfig::$encode) {
	$_POST[$key] = mb_convert_encoding($value, ServerConfig::$encode, $encode);
      }
    }
  }
}

//-- セキュリティ関連クラス --//
class Security {
  //リファラチェック
  static function CheckReferer($page, $white_list = null){
    if (is_array($white_list)) { //ホワイトリストチェック
      foreach ($white_list as $host) {
	if (strpos($_SERVER['REMOTE_ADDR'], $host) === 0) return false;
      }
    }
    $url = ServerConfig::$site_root . $page;
    return strncmp(@$_SERVER['HTTP_REFERER'], $url, strlen($url)) != 0;
  }

  //ブラックリストチェック
  static function CheckBlackList(){
    $addr = $_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($addr);
    foreach (array('white' => false, 'black' => true) as $type => $flag) {
      foreach (RoomConfig::${$type . '_list_ip'} as $ip) {
	if (strpos($addr, $ip) === 0) return $flag;
      }
      $list = RoomConfig::${$type . '_list_host'};
      if (isset($list) && preg_match($list, $host)) return $flag;
    }
    return false;
  }

  /**
   * 実行環境にダメージを与える可能性がある値が含まれているかどうか検査します。
   * @param  : mixed   : $value 検査対象の変数
   * @param  : boolean : $found 疑わしい値が存在しているかどうかを示す値。
   この値がtrueの場合、強制的に詳細なスキャンが実行されます。
   * @return : boolean : 危険な値が発見された場合 true、それ以外の場合 false
   */
  static function CheckValue($value, $found = false){
    $num = '22250738585072011';
    if ($found || (strpos(str_replace('.', '', serialize($value)), $num) !== false)) {
      //文字列の中に問題の数字が埋め込まれているケースを排除する
      if (is_array($value)) {
	foreach ($value as $item) {
	  if (self::CheckValue($item, true)) return true;
	}
      }
      else {
	$preg = '/^([0.]*2[0125738.]{15,16}1[0.]*)e(-[0-9]+)$/i';
	$item = strval($value);
	$matches = '';
	if (preg_match($preg, $item, $matches)) {
	  $exp = intval($matches[2]) + 1;
	  if (2.2250738585072011e-307 === floatval("{$matches[1]}e{$exp}")) return true;
	}
      }
    }
    return false;
  }
}

//-- 日時関連 --//
class Time {
  //TZ 補正をかけた時刻を返す (環境変数 TZ を変更できない環境想定？)
  static function Get(){
    $time = time();
    if (ServerConfig::$adjust_time_difference) $time += ServerConfig::$offset_seconds;
    return $time;
    /*
    // ミリ秒対応のコード(案) 2009-08-08 enogu
    $preg = '/([0-9]+)( [0-9]+)?/i';
    return preg_replace($preg, '$$2.$$1', microtime()) + ServerConfig::$offset_seconds; // ミリ秒
    */
  }

  //TZ 補正をかけた日時を返す
  static function GetDate($format, $time){
    return ServerConfig::$adjust_time_difference ? gmdate($format, $time) : date($format, $time);
  }

  //時間 (秒) を変換する
  static function Convert($seconds){
    $sentence = '';
    $hours    = 0;
    $minutes  = 0;

    if ($seconds >= 60){
      $minutes = floor($seconds / 60);
      $seconds %= 60;
    }
    if ($minutes >= 60){
      $hours = floor($minutes / 60);
      $minutes %= 60;
    }

    if ($hours   > 0) $sentence .= $hours   . '時間';
    if ($minutes > 0) $sentence .= $minutes . '分';
    if ($seconds > 0) $sentence .= $seconds . '秒';
    return $sentence;
  }

  //TIMESTAMP 形式の時刻を変換する
  static function ConvertTimeStamp($time_stamp, $date = true){
    $time = strtotime($time_stamp);
    if (ServerConfig::$adjust_time_difference) $time += ServerConfig::$offset_seconds;
    return $date ? self::GetDate('Y/m/d (D) H:i:s', $time) : $time;
  }
}

//-- HTML 生成クラス --//
class HTML {
  //共通 HTML ヘッダ生成
  static function GenerateHeader($title, $css = 'action', $close = false){
    $str = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=%s">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>%s</title>

EOF;
    $data = sprintf($str, ServerConfig::$encode, $title);
    if (is_null($css)) $css = 'action';
    $data .= self::LoadCSS(sprintf('%s/%s', JINRO_CSS, $css));
    if ($close) $data .= self::GenerateBodyHeader();
    return $data;
  }

  //共通 HTML ヘッダ出力
  static function OutputHeader($title, $css = 'action', $close = false){
    echo self::GenerateHeader($title, $css, $close);
  }

  //CSS 読み込み
  static function LoadCSS($path){
    return sprintf('<link rel="stylesheet" href="%s.css">'."\n", $path);
  }

  //CSS 出力
  static function OutputCSS($path){
    echo self::LoadCSS($path);
  }

  //JavaScript 読み込み
  static function LoadJavaScript($file, $path = null){
    if (is_null($path)) $path = JINRO_ROOT . '/javascript';
    return sprintf('<script type="text/javascript" src="%s/%s.js"></script>'."\n", $path, $file);
  }

  //JavaScript 出力
  static function OutputJavaScript($file, $path = null){
    echo self::LoadJavaScript($file, $path);
  }

  //HTML ヘッダクローズ
  static function GenerateBodyHeader($css = null){
    $str = isset($css) ? self::LoadCSS($css) : '';
    return $str . "</head>\n<body>\n";
  }

  //HTML ヘッダクローズ出力
  static function OutputBodyHeader($css = null){
    echo self::GenerateBodyHeader($css);
  }

  //HTML フッタ出力
  static function OutputFooter($exit = false){
    DB::Disconnect();
    echo "</body>\n</html>";
    if ($exit) exit;
  }

  //結果ページ HTML ヘッダ出力
  static function OutputResultHeader($title, $url = ''){
    self::OutputHeader($title);
    if ($url != '') printf('<meta http-equiv="Refresh" content="1;URL=%s">'."\n", $url);
    if (is_object(DB::$ROOM)) echo DB::$ROOM->GenerateCSS();
    self::OutputBodyHeader();
  }

  //結果ページ出力
  static function OutputResult($title, $body, $url = ''){
    DB::Disconnect();
    self::OutputResultHeader($title, $url);
    echo $body . "<br>\n";
    self::OutputFooter(true);
  }

  //共有フレーム HTML ヘッダ出力
  static function OutputFrameHeader($title){
    $str = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=%s">
<title>%s</title>
</head>

EOF;
    printf($str, ServerConfig::$encode, $title);
  }

  //フレーム HTML フッタ出力
  static function OutputFrameFooter(){
    echo <<<EOF
<noframes>
<body>
フレーム非対応のブラウザの方は利用できません。
</body>
</noframes>
</frameset>
</html>
EOF;
  }
}

//-- 出力関連 --//
//変数表示関数 (デバッグ用)
function PrintData($data, $name = null){
  $str = is_null($name) ? '' : $name . ': ';
  $str .= (is_array($data) || is_object($data)) ? print_r($data, true) : $data;
  echo $str . '<br>';
}

//村情報のRSSファイルを更新する
function OutputSiteSummary(){
  global $INIT_CONF;
  $INIT_CONF->LoadFile('feedengine');

  $filename = 'rss/rooms.rss';
  $rss = FeedEngine::Initialize('site_summary.php');
  $rss->Build();

  $fp = fopen(dirname(__FILE__)."/{$filename}", 'w');
  fwrite($fp, $rss->Export($filename));
  fflush($fp);
  fclose($fp);

  return $rss;
}

//ページ送り用のリンクタグを出力する
function OutputPageLink($CONFIG){
  $page_count = ceil($CONFIG->count / $CONFIG->view);
  $start_page = $CONFIG->current== 'all' ? 1 : $CONFIG->current;
  if ($page_count - $CONFIG->current < $CONFIG->page){
    $start_page = $page_count - $CONFIG->page + 1;
    if ($start_page < 1) $start_page = 1;
  }
  $end_page = $CONFIG->current + $CONFIG->page - 1;
  if ($end_page > $page_count) $end_page = $page_count;

  $url_stack = array('[' . (is_null($CONFIG->title) ? 'Page' : $CONFIG->title) . ']');
  $url_header = '<a href="' . $CONFIG->url . '.php?';

  if ($page_count > $CONFIG->page && $CONFIG->current> 1){
    $url_stack[] = GeneratePageLink($CONFIG, 1, '[1]...');
    $url_stack[] = GeneratePageLink($CONFIG, $start_page - 1, '&lt;&lt;');
  }

  for($page_number = $start_page; $page_number <= $end_page; $page_number++){
    $url_stack[] = GeneratePageLink($CONFIG, $page_number);
  }

  if ($page_number <= $page_count){
    $url_stack[] = GeneratePageLink($CONFIG, $page_number, '&gt;&gt;');
    $url_stack[] = GeneratePageLink($CONFIG, $page_count, '...[' . $page_count . ']');
  }
  $url_stack[] = GeneratePageLink($CONFIG, 'all');

  if ($CONFIG->url == 'old_log'){
    $list = $CONFIG->option;
    $list['page'] = 'page=' . $CONFIG->current;
    $list['reverse'] = 'reverse=' . ($CONFIG->is_reverse ? 'off' : 'on');
    $url_stack[] = '[表示順]';
    $url_stack[] = $CONFIG->is_reverse ? '新↓古' : '古↓新';

    $url = $url_header . implode('&', $list) . '">';
    $name = ($CONFIG->is_reverse xor $CONFIG->reverse) ? '元に戻す' : '入れ替える';
    $url_stack[] =  $url . $name . '</a>';
  }
  echo implode(' ', $url_stack);
}

//ページ送り用のリンクタグを作成する
function GeneratePageLink($CONFIG, $page, $title = null){
  if ($page == $CONFIG->current) return '[' . $page . ']';
  $option = (is_null($CONFIG->page_type) ? 'page' : $CONFIG->page_type) . '=' . $page;
  $list = $CONFIG->option;
  array_unshift($list, $option);
  $url = $CONFIG->url . '.php?' . implode('&', $list);
  $attributes = array();
  if (isset($CONFIG->attributes)){
    foreach($CONFIG->attributes as $attr => $value){
      $attributes[] = $attr . '="'. eval($value) . '"';
    }
  }
  $attrs = implode(' ', $attributes);
  if (is_null($title)) $title = '[' . $page . ']';
  return '<a href="' . $url . '" ' . $attrs . '>' . $title . '</a>';
}

//ログへのリンクを生成
function GenerateLogLink($url, $watch = false, $header = '', $footer = ''){
  $str = <<<EOF
{$header} <a target="_top" href="{$url}"{$footer}>正</a>
<a target="_top" href="{$url}&reverse_log=on"{$footer}>逆</a>
<a target="_top" href="{$url}&heaven_talk=on"{$footer}>霊</a>
<a target="_top" href="{$url}&reverse_log=on&heaven_talk=on"{$footer}>逆&amp;霊</a>
<a target="_top" href="{$url}&heaven_only=on"{$footer} >逝</a>
<a target="_top" href="{$url}&reverse_log=on&heaven_only=on"{$footer}>逆&amp;逝</a>
EOF;

  if ($watch){
    $str .= <<<EOF

<a target="_top" href="{$url}&watch=on"{$footer}>観</a>
<a target="_top" href="{$url}&watch=on&reverse_log=on"{$footer}>逆&amp;観</a>
EOF;
  }
  return $str;
}

//ゲームオプションの画像タグを作成する (最大人数用)
function GenerateMaxUserImage($number){
  global $ROOM_IMG;
  return in_array($number, RoomConfig::$max_user_list) && $ROOM_IMG->Exists("max{$number}") ?
    $ROOM_IMG->Generate("max{$number}", "最大{$number}人") : "(最大{$number}人)";
}
