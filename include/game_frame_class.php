<?php
//-- GameFrame 出力クラス --//
class GameFrame {
  const BORDER = ' border="1" frameborder="1" framespacing="1" bordercolor="#C0C0C0"';

  //出力
  static function Output() {
    HTML::OutputFrameHeader(ServerConfig::TITLE . '[プレイ]');
    if (RQ::$get->dead_mode) {
      $format = <<<EOF
<frameset rows="100, *, 20%%"%s>
<frame name="up" src="game_up.php%s&heaven_mode=on">
<frame name="middle" src="game_play.php%s">
<frame name="bottom" src="game_play.php%s">%s
EOF;
      $url = RQ::$get->url . '&dead_mode=on';
      printf($format, self::BORDER, $url, $url, RQ::$get->url . '&heaven_mode=on', "\n");
    }
    else {
      $format = <<<EOF
<frameset rows="100, *"%s>
<frame name="up" src="game_up.php%s">
<frame name="bottom" src="game_play.php%s">%s
EOF;
      $url = RQ::$get->url;
      printf($format, self::BORDER, $url, $url, "\n");
    }
    HTML::OutputFrameFooter();
  }
}