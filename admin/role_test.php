<?php
require_once(dirname(__FILE__) . '/../include/init.php');
loadModule(
   CONFIG,
   SYSTEM_CLASSES,
   ROOM_CONF,
   GAME_CONF,
   VOTE_FUNCTIONS
   );
// require_once(dirname(__FILE__) . '/../include/game_vote_functions.php');

$CSS_PATH = '../css'; //CSS のパス設定

OutputHTMLHeader('闇鍋モード配役テスト');

echo <<<EOF
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>人数</label><input type="text" name="user_count" size="3" value="20">
<label>試行回数</label><input type="text" name="try_count" size="2" value="100">
<input type="radio" name="game_option" value="chaos">
闇鍋
<input type="radio" name="game_option" value="chaosfull" checked>
真・闇鍋
<input type="submit" value=" 実 行 "></form>
</form>

EOF;

if($_POST['command'] == 'role_test'){
  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $RQ_ARGS->TestItems->test_room = array('game_option' => $_POST['game_option']);
  $ROOM = new RoomDataSet($RQ_ARGS);
  for($i = 1; $i <= $try_count; $i++){
    echo "$i 回目";
    $role_list = GetRoleList($user_count, '');
    if($role_list == '') break;
    $role_count_list = array_count_values($role_list);
    echo MakeRoleNameList($role_count_list) .'<br>';
  }
}

OutputHTMLFooter(true);
?>
