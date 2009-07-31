<?php
class User{
  function ParseCompoundParameters(){
    $this->ParseRoles();
  }
  function IsLiving(){
    return $this->live == 'live';
  }
  function Kill(){
    $this->live = 'dead';
    $this->updated[] = 'live';
  }
  function ParseRoles(){
    $this->roles = explode(' ', $this->role);
  }
  function AddRole($role){
    $this->role .= " $role";
    $this->updated[] = 'role';
    $this->ParseRoles();
  }
  function RemoveRole($role){
/* このメソッドは橋姫実装時のために予約されています。
    //スペースが２つ続いている箇所は空の役職と認識されるおそれがあります。
    //本来はParseRole側でpreg_split()などを使用するべきですが、役職が減る状況の方が少ないため、削除側で調節するものとします。(2009-07-05 enogu)
    $this->role = str_replace('  ', ' ', str_replace($role, '', $this->role));
    $this->updated[] = 'role';
    $this->ParseRoles();
*/
  }
  function Save(){
    if (isset($this->updated)){
      foreach($this->updated as $item){
        $update_list[] = "$item = '{$this->item}'";
      }
      $update = implode(', ', $update_list);
      mysql_query("UPDATE user_entry SET $update WHERE room_no = {$this->room_no} AND uname = '{$this->uname}'");
    }
  }
}


class Users{
  var $room_no;
  var $rows = array();

  function Users($room_no){
    $this->room_no = intval($room_no);
    $this->Load();
  }

  function Load(){
    $result = mysql_query(
      "SELECT
	users.room_no,
	users.user_no,
	users.uname,
	users.handle_name,
	users.sex,
	users.profile,
	users.role,
	users.live,
	users.last_load_day_night,
	users.ip_address = '' AS is_system,
	icons.icon_filename,
	icons.color,
	icons.icon_width,
	icons.icon_height
      FROM user_entry users LEFT JOIN user_icon icons ON users.icon_no = icons.icon_no
      WHERE users.room_no = {$this->room_no}
      AND users.user_no >= 0"
    );
    if($result === false) return;
    $this->rows = array();
    while(($user = mysql_fetch_object($result, 'User')) !== false){
      $user->ParseCompoundParameters();
      $this->rows[$user->user_no] = $user;
      $this->names[$user->uname] = $user->user_no;
    }
  }

  function Save(){
    foreach ($this->rows as $user) $user->save();
  }

  function ParseCompoundParameters(){
    foreach($this->rows as $user) $user->ParseCompoundParameters();
  }

  function UnameToNumber($uname){
    return $this->names[$uname];
  }

  function ByUname($uname){
    return $this->rows[$this->UnameToNumber($uname)];
  }

  function GetHandleName($uname){
    return $this->rows[$this->UnameToNumber($uname)]->handle_name;
  }

  function GetRole($uname){
    return $this->rows[$this->UnameToNumber($uname)]->role;
  }

  function GetLive($uname){
    return $this->rows[$this->UnameToNumber($uname)]->live;
  }

  //現在のリクエスト情報に基づいて新しいユーザーをデータベースに登録します。
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

  //ユーザー情報を指定して新しいユーザーをデータベースに登録します。(ドラフト：この機能はテストされていません)
  function Register($uname, $password, $handle_name, $sex, $profile, $icon_no, $role, $ip_address = '', $session_id = ''){
    mysql_query(
      "INSERT INTO user_entry (room_no, user_no, uname, password, handle_name, sex, profile, icon_no, role)
      VALUES (
	$this->room_no,
	(SELECT MAX(user_no) + 1 FROM user_entry WHERE room_no = {$this->room_no}),
	'$uname', '$password', '$handle_name', '$sex', '$profile', $icon_no, '$role'"
    );
    $USERS->Load();
  }
}

//グローバルオブジェクトと操作関数

function GetNumber($user){
  global $USERS;
  return is_integer($user) ? $user : $USERS->UnameToNumber($user);
}

function GetHandleName($user){
  global $USERS;
  return $USERS->rows[GetNumber($user)]->handle_name;
}

function IsLiving($user){
  global $USERS;
  return $USERS->rows[GetNumber($user)]->live == 'live';
}

/*
function KillUser($user){
  global $USERS;
  LoadUsers();
  return $USERS->rows[GetNumber($user)]->Kill();
}
*/
?>
