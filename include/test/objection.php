<?php
//-- 異議ありテストツール --//
class ObjectionTest {
  const URL = 'objection_test.php';
  const RESET = '<p><a href="%s">リセット</a></p>%s<table>%s';

  //出力
  static function Output(){
    HTML::OutputHeader('異議ありテストツール', null, true);
    printf(self::RESET, self::URL, "\n", "\n");
    $form = <<<EOF
<tr><td class="objection"><form method="POST" action="%s">
<input type="hidden" name="command" value="on">
<input type="hidden" name="set_objection" value="%s">
<input type="image" name="objimage" src="%s" border="0"> (%s)
</form></td></tr>%s
EOF;
    $image = JINRO_ROOT . '/' . GameConfig::OBJECTION_IMAGE;
    $stack = array(
      'entry'            => '入村',
      'full'             => '定員',
      'morning'          => '夜明け',
      'revote'           => '再投票',
      'novote'           => '未投票告知',
      'alert'            => '未投票警告',
      'objection_male'   => '異議あり(男)',
      'objection_female' => '異議あり(女)');
    foreach ($stack as $key => $value) printf($form, self::URL, $key, $image, $value, "\n");
    echo "</table>\n";

    if ($_POST['command'] == 'on' && array_key_exists($_POST['set_objection'], $stack)) {
      Sound::Output($_POST['set_objection']);
    }
    HTML::OutputFooter();
  }
}