<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF', 'GAME_OPT_MESS', 'ROLE_DATA');
$INIT_CONF->LoadFile('game_vote_functions', 'request_class');
OutputHTMLHeader('配役テストツール', 'role_table');
OutputRoleTestForm();
if($_POST['command'] == 'role_test'){
  $RQ_ARGS =& new RequestBase();
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $stack->game_option = array('dummy_boy');
  $stack->option_role = array();
  switch($_POST['game_option']){
  case 'chaos':
  case 'chaosfull':
  case 'chaos_hyper':
  case 'chaos_verso':
  case 'duel':
  case 'gray_random':
  case 'quiz':
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
  foreach(array('festival', 'gerd', 'detective') as $option){
    if($_POST[$option] == 'on') $stack->game_option[] = ' '.$option;
  }

  foreach(array('replace_human', 'change_mad') as $option){
    if(array_search($_POST[$option], $ROOM_CONF->{$option.'_list'}) !== false){
      $stack->option_role[] = $_POST[$option];
    }
  }
  if(array_search($_POST['topping'], $ROOM_CONF->topping_list) !== false){
    $stack->option_role[] = 'topping:' . $_POST['topping'];
  }
  if($_POST['limit_off'] == 'on') $CAST_CONF->chaos_role_group_rate_list = array();

  $RQ_ARGS->TestItems->test_room['game_option'] = implode(' ', $stack->game_option);
  $RQ_ARGS->TestItems->test_room['option_role'] = implode(' ', $stack->option_role);
  $ROOM = new Room($RQ_ARGS);
  $ROOM->LoadOption();

  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $str = '%0' . strlen($try_count) . 'd回目: ';
  for($i = 1; $i <= $try_count; $i++){
    printf($str, $i);
    $role_list = GetRoleList($user_count);
    if($role_list == '') break;
    PrintData(GenerateRoleNameList(array_count_values($role_list), true));
  }
}
OutputHTMLFooter(true);

function OutputRoleTestForm(){
  global $ROOM_CONF, $GAME_OPT_MESS;

  echo <<<EOF
</head>
<body>
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>人数</label><input type="text" name="user_count" size="2" value="20">
<label>試行回数</label><input type="text" name="try_count" size="2" value="100">
<input type="submit" value=" 実 行 "><br>
<input type="radio" name="game_option" value="">普通
<input type="radio" name="game_option" value="chaos">闇鍋
<input type="radio" name="game_option" value="chaosfull">真・闇鍋
<input type="radio" name="game_option" value="chaos_hyper" checked>超・闇鍋
<input type="radio" name="game_option" value="chaos_verso">裏・闇鍋
<input type="radio" name="game_option" value="duel">決闘
<input type="radio" name="game_option" value="duel_auto_open_cast">自動公開決闘
<input type="radio" name="game_option" value="duel_not_open_cast">非公開決闘
<input type="radio" name="game_option" value="gray_random">グレラン
<input type="radio" name="game_option" value="quiz">クイズ<br>
<input type="radio" name="topping" value="" checked>標準

EOF;

  $count = 0;
  foreach($ROOM_CONF->topping_list as $mode){
    $count++;
    if($count > 0 && $count % 8 == 0) echo "<br>\n";
    echo <<<EOF
<input type="radio" name="topping" value="{$mode}">{$GAME_OPT_MESS->{'topping_'.$mode}}

EOF;
  }

  echo <<<EOF
<br>
<input type="radio" name="replace_human" value="" checked>標準

EOF;
  foreach($ROOM_CONF->replace_human_list as $mode){
    echo <<<EOF
<input type="radio" name="replace_human" value="{$mode}">{$GAME_OPT_MESS->$mode}

EOF;
  }

  echo <<<EOF
<br>
<input type="radio" name="change_mad" value="" checked>標準

EOF;
  foreach($ROOM_CONF->change_mad_list as $mode){
    echo <<<EOF
<input type="radio" name="change_mad" value="{$mode}">{$GAME_OPT_MESS->$mode}

EOF;
  }

  echo <<<EOF
<input type="checkbox" value="on" name="festival">お祭り
<input type="checkbox" value="on" name="gerd">ゲルト君
<input type="checkbox" value="on" name="detective">探偵
<input type="checkbox" value="on" name="limit_off">リミッタオフ
</form>

EOF;
}
