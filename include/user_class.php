<?php
class User{
  function IsLiving(){
    return $this->live == 'live';
  }
  function Kill(){
    $this->live = 'dead';
  }
  function ParseCompoundParameters(){
    $this->roles = explode(' ', $this->role);
  }
  //��̾����������桼����������ʣ�����򿦤��������ǽ�����⤤���ᡢ���δؿ��ϥ��֥������ȥ᥽�åɤǤϤʤ���Ū�ؿ��Ȥ��ư��äƤ��롣
  function GetRoleName($role, $short = false){
    static $role_names = array(
      'human'=>'¼��', 
      'wolf'=>'��ϵ',
      'mage'=>'�ꤤ��', 
      'necromancer'=>'��ǽ��', 
      'mad'=>'����', 
      'guard'=>'���', 
      'common'=>'��ͭ��',
      'fox'=>'�Ÿ�',
      'lovers'=>'����',
      'authority'=>'���ϼ�',
      'decide'=>'�����',
      'poison'=>'���Ǽ�'
      'cupid'=>'���塼�ԥå�',
      'mania'=>'���åޥ˥�'
      'copied'=>'�����åޥ˥�'
    );
    static $short_role_names = array(
      'human'=>'¼', 
      'wolf'=>'ϵ',
      'mage'=>'��', 
      'necromancer'=>'��', 
      'mad'=>'��', 
      'guard'=>'��', 
      'common'=>'��',
      'fox'=>'��',
      'lovers'=>'��',
      'authority'=>'��',
      'decide'=>'��',
      'poison'=>'��'
      'cupid'=>'��',
      'mania'=>'��'
      'copied'=>'��'
    );
    return $short ? $short_role_names[$role] : $role_names[$role];
  }
}


class Users {
  var $room_no;
  var $rows = array();

  function Users($room_no){
    $this->room_no = intval($room_no);
    $this->Load();
  }

  function Load(){
    $result = mysql_query(
      "SELECT
      	users.user_no,
      	users.uname,
      	users.handle_name,
      	users.sex,
      	users.profile,
	users.role,
	users.live,
	users.last_load_day_night,
	users.last_words,
      	icons.icon_filename,
      	icons.color,
      	icons.icon_width,
      	icons.icon_height
      FROM user_entry users LEFT JOIN user_icon icons ON users.icon_no = icons.icon_no
      WHERE users.room_no = {$this->room_no}
      AND users.user_no >= 0"
    );
    if ($result === false) {
      return;
    }
    while(($user = mysql_fetch_object($result, 'User')) !== false){
      $user->ParseCompoundParameters();
      $this->rows[$user->user_no] = $user;
      $this->names[$user->uname] = $user->user_no;
    }
  }

  function ParseCompoundParameters(){
    foreach($this->rows as $user)
      $user->ParseCompoundParameters();
  }

  function UnameToNumber($uname){
    return $this->names[$uname];
  }

  function ByUname($uname){
    return $this->rows[$this->UnameToNumber($uname)];
  }

  //���ߤΥꥯ�����Ⱦ���˴�Ť��ƿ������桼������ǡ����١�������Ͽ���ޤ���
  function RegisterByRequest(){
    extract($_REQUEST, EXTR_PREFIX_ALL, 'unsafe');
    session_regenerate_id();
    Users::Register(
      mysql_real_escape_string($unsafe_uname),
      mysql_real_escape_string($unsafe_password),
      mysql_real_escape_string($unsafe_handle_name),
      mysql_real_escape_string($unsafe_sex),
      mysql_real_escape_string($unsafe_profile),
      intval($unsafe_icon_no),
      mysql_real_escape_string($unsafe_role),
      $_SERVER['REMOTE_ADDR'],
      session_id()
    );
  }
  //�桼�����������ꤷ�ƿ������桼������ǡ����١�������Ͽ���ޤ���(�ɥ�եȡ����ε�ǽ�ϥƥ��Ȥ���Ƥ��ޤ���)
  function Register($uname, $password, $handle_name, $sex, $profile, $icon_no, $role, $ip_address = '', $session_id = ''){
    mysql_query(
      "INSERT INTO user_entry (room_no, user_no, uname, password, handle_name, sex, profile, icon_no, role)
      VALUES (
	$this->room_no,
	(SELECT MAX(user_no) + 1 FROM user_entry WHERE room_no = {$this->room_no}),
	'$uname', '$password', '$handle_name', '$sex', '$profile', $icon_no, '$role'"
    );
    Users::Load();
  }
}


//�����Х륪�֥������Ȥ����ؿ�
$USERS = new Users($room_no);

function GetNumber($user){
  return is_integer($user) ? $user : $USERS->UnameToNumber($user);
}

function GetHandleName($user){
  global $USERS;
  return $USERS->rows[GetNumber($user)]->handle_name;
}

function IsLiving($user){
  global $USERS;
  return $USERS->rows[GetNumber($user)]->handle_name;
}

function KillUser($user){
  global $USERS;
  return $USERS->rows[GetNumber($user)]->Kill();
}

?>
