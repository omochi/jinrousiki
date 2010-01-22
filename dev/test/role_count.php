<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/include/init.php');
loadModule(
  USER_CLASSES,
  GAME_FUNCTIONS,
  VOTE_FUNCTIONS,
  ROOM_CONF,
  GAME_CONF,
  MESSAGE
  );

$dbHandle = ConnectDatabase(); // DB ��³
$CSS_PATH = '../../css';
OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[��������]', 'game'); //HTML�إå�
$SELF =& new User();

$role_count_list = array();
$room_list = FetchArray("SELECT room_no FROM room");
#$room_list = FetchArray("SELECT room_no FROM room WHERE game_option LIKE '%chaos%'");
$total_room = count($room_list);
$total_user = 0;
foreach($room_list as $id){
  $role_list = FetchArray("SELECT role FROM user_entry WHERE room_no = $id AND user_entry.user_no > 0");
  $total_user += count($role_list);
  foreach($role_list as $role){
    $SELF->role = $role;
    $SELF->role_list = array();
    $SELF->ParseRoles();
    if($SELF->IsRole('copied')) $SELF->role_list[0] = 'mania';
    foreach($SELF->role_list as $this_role){
      if($this_role != 'copied') $role_count_list[$this_role]++;
    }
  }
}

echo "¼����{$total_room}<br>";
echo "¼�͡�{$total_user}<br>";
# print_r($role_count_list);
echo MakeRoleNameList($role_count_list, 'camp') . '<br><br>';
echo MakeRoleNameList($role_count_list, 'role') . '<br><br>';
echo MakeRoleNameList($role_count_list) . '<br>';
OutputHTMLFooter(); //HTML�եå�
DisconnectDatabase($dbHandle); //DB ��³���
?>
