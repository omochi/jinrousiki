<?php
//-- Twitter 投稿クラス --//
class JinroTwitter {
  const LIMIT = 140; //制限文字数
  const URL   = 'https://api.twitter.com/1/statuses/update.json'; //送信先 URL
  const SHORT = 'http://tinyurl.com/api-create.php?url='; //短縮 URL

  //投稿処理
  static function Send($id, $name, $comment) {
    if (TwitterConfig::DISABLE || ServerConfig::SECRET_ROOM) return true;

    $str = TwitterConfig::GenerateMessage($id, $name, $comment);
    if (ServerConfig::ENCODE != 'UTF-8') { //Twitter は UTF-8
      $str = mb_convert_encoding($str, 'UTF-8', ServerConfig::ENCODE);
    }
    if (mb_strlen($str) > self::LIMIT) $str = mb_substr($str, 0, self::LIMIT - 1);

    if (TwitterConfig::ADD_URL) {
      $url = ServerConfig::SITE_ROOT;
      if (TwitterConfig::DIRECT_URL) $url .= 'login.php?room_no=' . $id;
      if (TwitterConfig::SHORT_URL) {
	$short_url = @file_get_contents(self::SHORT . $url);
	if ($short_url != '') $url = $short_url;
      }
      if (mb_strlen($str . $url) + 1 < self::LIMIT) $str .= ' ' . $url;
    }
    if (0 < strlen(TwitterConfig::HASH) &&
	mb_strlen($str . TwitterConfig::HASH) + 2 < self::LIMIT) {
      $str .= sprintf(' #%s', TwitterConfig::HASH);
    }

    //投稿
    $to = new TwitterOAuth(TwitterConfig::KEY_CK, TwitterConfig::KEY_CS,
			   TwitterConfig::KEY_AT, TwitterConfig::KEY_AS);
    $response = $to->OAuthRequest(self::URL, 'POST', array('status' => $str));

    if (! ($response === false || strrpos($response, 'error'))) return true;
    //エラー処理
    $error = 'Twitter への投稿に失敗しました。<br>'."\n" . 'メッセージ：' . $str;
    Text::p($error);
    return false;
  }
}
