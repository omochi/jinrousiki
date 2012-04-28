<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('SOUND');
$INIT_CONF->LoadFile('game_config');

HTML::OutputHeader('異議ありテストツール', null, true);
OutputObjectionTestPage();
HTML::OutputFooter();

//-- 関数 --//
function OutputObjectionTestPage(){
  global $SOUND;

  $image = JINRO_ROOT . '/' . GameConfig::$objection_image;
  echo <<<EOF
<a href="./objection_test.php">リセット</a>
<table>

EOF;

  $disable = true; //新規データテスト用
  $base_data = array('objection_male' => '男', 'objection_female' => '女');
  $add_data  = $disable ? array()
    : array('test1' => 'テスト1',
	    'test2' => 'テスト2');
  foreach (array_keys($add_data) as $name) $SOUND->$name = $name;

  $stack = array_merge($base_data, $add_data);
  foreach ($stack as $key => $value) {
    echo <<<EOF
<tr><td class="objection"><form method="POST" action="objection_test.php">
<input type="hidden" name="command" value="on">
<input type="hidden" name="set_objection" value="{$key}">
<input type="image" name="objimage" src="{$image}" border="0"> ({$value})
</form></td></tr>

EOF;
  }
  echo "</table>\n";

  if ($_POST['command'] == 'on' && array_key_exists($_POST['set_objection'], $stack)) {
    $SOUND->Output($_POST['set_objection']);
  }
}