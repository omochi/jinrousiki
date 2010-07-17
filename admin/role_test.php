<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF', 'ROLE_DATA');
$INIT_CONF->LoadFile('game_vote_functions', 'request_class');
OutputHTMLHeader('����⡼������ƥ���');

echo <<<EOF
</head>
<body>
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>�Ϳ�</label><input type="text" name="user_count" size="3" value="20">
<label>��Բ��</label><input type="text" name="try_count" size="2" value="100">
<input type="submit" value=" �� �� "><br>
<input type="radio" name="game_option" value="">
����¼
<input type="radio" name="game_option" value="chaos">
����
<input type="radio" name="game_option" value="chaosfull">
��������
<input type="radio" name="game_option" value="chaos_hyper" checked>
Ķ������
<input type="radio" name="game_option" value="duel">
��Ʈ
<input type="radio" name="game_option" value="duel auto_open_cast">
��ư������Ʈ
<input type="radio" name="game_option" value="duel not_open_cast">
�������Ʈ
<input type="checkbox" name="full_mania" value="on">
���åޥ˥�
<input type="checkbox" name="festival" value="on">
���פ�
</form>

EOF;

if($_POST['command'] == 'role_test'){
  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $option_role = $_POST['game_option'];
  $RQ_ARGS = new RequestBase();
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $game_option = $_POST['game_option'];
  if($_POST['full_mania'] == 'on') $option_role .= ' full_mania';
  if($_POST['festival']   == 'on') $game_option .= ' festival';
  $RQ_ARGS->TestItems->test_room = array('game_option' => $game_option);
  $ROOM = new Room($RQ_ARGS);
  for($i = 1; $i <= $try_count; $i++){
    echo "$i ����";
    $role_list = GetRoleList($user_count, $option_role);
    if($role_list == '') break;
    $role_count_list = array_count_values($role_list);
    PrintData(GenerateRoleNameList($role_count_list));
  }
}
OutputHTMLFooter(true);
