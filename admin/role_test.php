<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF', 'ROLE_DATA');
$INIT_CONF->LoadFile('game_vote_functions', 'request_class');
OutputHTMLHeader('配役テストツール', 'role_table');

echo <<<EOF
</head>
<body>
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>人数</label><input type="text" name="user_count" size="3" value="20">
<label>試行回数</label><input type="text" name="try_count" size="2" value="100">
<input type="submit" value=" 実 行 "><br>
<input type="radio" name="game_option" value="">普通村
<input type="radio" name="game_option" value="chaos">闇鍋
<input type="radio" name="game_option" value="chaosfull">真・闇鍋
<input type="radio" name="game_option" value="chaos_hyper" checked>超・闇鍋
<input type="radio" name="game_option" value="duel">決闘
<input type="radio" name="game_option" value="duel_auto_open_cast">自動公開決闘
<input type="radio" name="game_option" value="duel_not_open_cast">非公開決闘
<input type="radio" name="game_option" value="gray_random">グレラン
<input type="radio" name="game_option" value="quiz">クイズ<br>
<input type="radio" name="replace_human" value="" checked>置換無し
<input type="radio" name="replace_human" value="full_mania">神話マニア村
<input type="radio" name="replace_human" value="full_chiroptera">蝙蝠村
<input type="radio" name="replace_human" value="full_cupid">キューピッド村
<input type="radio" name="replace_human" value="replace_human">村人置換村
<input type="checkbox" name="festival" value="on">お祭り
</form>

EOF;

if($_POST['command'] == 'role_test'){
  $RQ_ARGS =& new RequestBase();
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $stack->game_option = array();
  $stack->option_role = array();
  switch($_POST['game_option']){
  case 'chaos':
  case 'chaosfull':
  case 'chaos_hyper':
  case 'gray_random':
  case 'duel':
    $stack->game_option[] = $_POST['game_option'];
    break;

  case 'duel_auto_open_cast':
    $stack->game_option[] = 'duel';
    $stack->option_role[] = 'auto_open_cast';
    break;

  case 'duel_not_open_cast':
    $stack->game_option[] = 'duel';
    $stack->option_role[] = 'not_open_cast';
    break;
  }
  if($_POST['festival'] == 'on') $stack->game_option[] = ' festival';

  switch($_POST['replace_human']){
  case 'full_mania':
  case 'full_chiroptera':
  case 'full_cupid':
  case 'replace_human':
    $stack->option_role[] = $_POST['replace_human'];
    break;
  }
  $RQ_ARGS->TestItems->test_room['game_option'] = implode(' ', $stack->game_option);
  $RQ_ARGS->TestItems->test_room['option_role'] = implode(' ', $stack->option_role);
  $ROOM = new Room($RQ_ARGS);
  $ROOM->LoadOption();

  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $str = '%0' . strlen($try_count) . 'd回目: ';
  for($i = 1; $i <= $try_count; $i++){
    echo sprintf($str, $i);
    $role_list = GetRoleList($user_count);
    if($role_list == '') break;
    $role_count_list = array_count_values($role_list);
    PrintData(GenerateRoleNameList($role_count_list, true));
  }
}
OutputHTMLFooter(true);
