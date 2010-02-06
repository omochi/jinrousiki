<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF');
$INIT_CONF->LoadFile('game_vote_functions');
OutputHTMLHeader('����⡼������ƥ���');

echo <<<EOF
</head>
<body>
<form method="POST" action="role_test.php">
<input type="hidden" name="command" value="role_test">
<label>�Ϳ�</label><input type="text" name="user_count" size="3" value="20">
<label>��Բ��</label><input type="text" name="try_count" size="2" value="100">
<input type="radio" name="game_option" value="chaos">
����
<input type="radio" name="game_option" value="chaosfull" checked>
��������
<input type="radio" name="game_option" value="duel">
��Ʈ
<input type="radio" name="game_option" value="full_mania">
���åޥ˥�
<input type="submit" value=" �� �� "></form>
</form>

EOF;

if($_POST['command'] == 'role_test'){
  $user_count = (int)$_POST['user_count'];
  $try_count  = (int)$_POST['try_count'];
  $option_role = $_POST['game_option'];
  $RQ_ARGS->TestItems->is_virtual_room = true;
  $RQ_ARGS->TestItems->test_room = array('game_option' => $_POST['game_option']);
  $ROOM = new Room($RQ_ARGS);
  for($i = 1; $i <= $try_count; $i++){
    echo "$i ����";
    $role_list = GetRoleList($user_count, $option_role);
    if($role_list == '') break;
    $role_count_list = array_count_values($role_list);
    PrintData(MakeRoleNameList($role_count_list));
  }
}

OutputHTMLFooter(true);
