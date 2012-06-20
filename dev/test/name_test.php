<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('role_data_class');

//-- 表示 --//
HTML::OutputHeader('役職名表示ツール', 'game', true);
OutputNameTest();
HTML::OutputFooter();

//-- 関数 --//
function OutputNameTest(){
  echo <<<EOF
<form method="POST" action="name_test.php">
<input type="hidden" name="command" value="name_test">
<input type="submit" value=" 実 行 "><br>
<input type="radio" name="type" value="all-all" checked>全て

EOF;

  $stack = new StdClass();
  foreach (array_keys(RoleData::$main_role_list) as $role) { //役職データ収集
    $stack->group[RoleData::DistinguishRoleGroup($role)][] = $role;
    $stack->camp[RoleData::DistinguishCamp($role, true)][] = $role;
  }
  $count = 0;
  foreach (array('camp' => '陣営', 'group' => '系') as $type => $name) {
    foreach (array_keys($stack->$type) as $role) {
      $count++;
      if ($count > 0 && $count % 9 == 0) echo "<br>\n";
      $value = $role . '-' . $type;
      $label = RoleData::$main_role_list[$role] . $name;
      echo <<<EOF
<input type="radio" name="type" id="{$value}" value="{$value}"><label for="{$value}">{$label}</label>

EOF;
    }
  }
  echo "</form>\n";

  if (@$_POST['command'] != 'name_test') return; //実行判定
  list($role, $type) = explode('-', @$_POST['type']);
  switch ($type) {
  case 'all':
    $stack = array_keys(RoleData::$main_role_list);
    break;

  case 'camp':
  case 'group':
    $stack = $stack->{$type}[$role];
    break;

  default:
    return;
  }
  foreach ($stack as $role) Text::p(RoleData::GenerateMainRoleTag($role));
}
