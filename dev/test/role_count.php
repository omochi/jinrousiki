<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'MESSAGE');

$DB_CONF->Connect(); // DB 接続
OutputHTMLHeader($SERVER_CONF->title . '[配役統計]', 'game'); //HTMLヘッダ

$SELF =& new User();
$role_count_list = array();
//$room_list = FetchArray("SELECT room_no FROM room");
$room_list = FetchArray("SELECT room_no FROM room WHERE game_option LIKE '%chaos%'");
$total_room = count($room_list);
$total_user = 0;
foreach($room_list as $id){
  $role_list = FetchArray("SELECT role FROM user_entry WHERE room_no = $id AND user_entry.user_no > 0");
  $total_user += count($role_list);
  foreach($role_list as $role){
    $SELF->ParseRoles($role);
    if($SELF->IsRole('copied')) $SELF->role_list[0] = 'mania';
    foreach($SELF->role_list as $this_role){
      if($this_role != 'copied') $role_count_list[$this_role]++;
    }
  }
}

PrintData($total_room, '村数');
PrintData($total_user, '村人');
//PrintData($role_count_list);
echo GenerateRoleNameList($role_count_list, 'camp') . '<br><br>';
echo GenerateRoleNameList($role_count_list, 'role') . '<br><br>';
echo GenerateRoleNameList($role_count_list) . '<br>';
OutputHTMLFooter(); //HTMLフッタ
