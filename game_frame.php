<?php
require_once('include/init.php');
OutputFrameHTMLHeader($SERVER_CONF->title . '[プレイ]');
$option = 'frameborder="1" framespacing="1" bordercolor="#C0C0C0"';
$header = '?room_no=' . (int)$_GET['room_no'] . '&auto_reload=' . (int)$_GET['auto_reload'];
if($_GET['play_sound'] == 'on') $header .= '&play_sound=on';
if($_GET['list_down']  == 'on') $header .= '&list_down=on';

if($_GET['dead_mode'] == 'on'){
  $url = $header . '&dead_mode=on';
  echo <<< EOF
<frameset rows="100, *, 20%" border="2" $option>
<frame name="up" src="game_up.php{$url}&heaven_mode=on#game_top">
<frame name="middle" src="game_play.php${url}#game_top">
<frame name="bottom" src="game_play.php${header}&heaven_mode=on#game_top">

EOF;
}
else{
  echo <<< EOF
<frameset rows="100, *" border="1" $option>
<frame name="up" src="game_up.php{$header}#game_top">
<frame name="bottom" src="game_play.php${header}#game_top">

EOF;
}
OutputFrameHTMLFooter();
