<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF', 'ROLE_DATA');
$INIT_CONF->LoadFile('game_vote_functions', 'request_class');
OutputHTMLHeader('闇鍋モード配役テスト');

echo <<<EOF
</head>
<body>
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>人数</label><input type="text" name="user_count" size="3" value="20">
<label>試行回数</label><input type="text" name="try_count" size="2" value="100">
<input type="submit" value=" 実 行 "><br>
<input type="radio" name="game_option" value="">
普通村
<input type="radio" name="game_option" value="chaos">
闇鍋
<input type="radio" name="game_option" value="chaosfull">
真・闇鍋
<input type="radio" name="game_option" value="chaos_hyper" checked>
超・闇鍋
<input type="radio" name="game_option" value="duel">
決闘
<input type="radio" name="game_option" value="duel auto_open_cast">
自動公開決闘
<input type="radio" name="game_option" value="duel not_open_cast">
非公開決闘
<input type="checkbox" name="festival" value="on">
お祭り
<br>
<input type="radio" name="replace_human" value="" checked>
置換無し
<input type="radio" name="replace_human" value="full_mania">
神話マニア村
<input type="radio" name="replace_human" value="full_chiroptera">
蝙蝠村
<input type="radio" name="replace_human" value="full_cupid">
キューピッド村
<input type="radio" name="replace_human" value="replace_human">
村人置換村
</form>

EOF;

if($_POST['command'] == 'role_test'){
  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $option_role = $_POST['game_option'];
  $RQ_ARGS = new RequestBase();
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $game_option = $_POST['game_option'];
  if($_POST['festival']   == 'on') $game_option .= ' festival';
  switch($_POST['replace_human']){
  case 'full_mania':
  case 'full_chiroptera':
  case 'full_cupid':
  case 'replace_human':
    $option_role .= ' ' . $_POST['replace_human'];
    break;
  }
  $RQ_ARGS->TestItems->test_room = array('game_option' => $game_option);
  $ROOM = new Room($RQ_ARGS);
  for($i = 1; $i <= $try_count; $i++){
    echo "$i 回目";
    $role_list = GetRoleList($user_count, $option_role);
    if($role_list == '') break;
    $role_count_list = array_count_values($role_list);
    PrintData(GenerateRoleNameList($role_count_list));
  }
}
OutputHTMLFooter(true);
