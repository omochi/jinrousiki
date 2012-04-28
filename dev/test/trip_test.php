<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('game_config');

HTML::OutputHeader('トリップテストツール', null, true);
  echo <<<EOF
<form method="POST" action="trip_test.php">
<input type="hidden" name="command" value="on">
<label>トリップキー</label><input type="text" name="key" size="20" value="">
</form>

EOF;
if ($_POST['command'] == 'on') PrintData(Text::ConvertTrip($_POST['key']), '変換結果');
HTML::OutputFooter();
