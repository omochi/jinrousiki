<?php
class User{
  var $role_list = array();
  var $main_role;

  function ParseCompoundParameters(){
    $this->ParseRoles();
    $this->main_role = $this->role_list[0];
  }

  function is_live(){
    return ($this->live == 'live');
  }

  function is_dead(){
    return ($this->live == 'dead');
  }

  function is_dummy_boy(){
    return ($this->uname == 'dummy_boy');
  }

  function is_role($role){
    if(! is_array($this->role_list)) return false;
    $arg = func_get_args();
    if(is_array($arg[0])) $arg = array_shift($arg);
    if(count($arg) > 1){
      return (count(array_intersect($arg, $this->role_list)) > 0);
    }
    else{
      return (in_array($arg[0], $this->role_list));
    }
  }

  function is_active_role($role){
    return ($this->is_role($role) && ! $this->is_role('lost_ability'));
  }

  function is_role_group($role){
    $arg = func_get_args();
    foreach($arg as $this_role){
      if(strpos($this->role, $this_role) !== false) return true;
    }
    return false;
  }

  function is_wolf(){
    return $this->is_role_group('wolf');
  }

  function is_fox(){
    return $this->is_role_group('fox');
  }

  function is_lovers(){
    return $this->is_role_group('lovers');
  }

  function Kill(){
    $this->live = 'dead';
    $this->updated[] = 'live';
  }

  function ParseRoles(){
    $this->role_list = explode(' ', $this->role);
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

class UserDataSet{
  var $room_no;
  var $rows = array();
  var $kicked = array();
  var $names = array();

  function UserDataSet($room_no){
    global $RQ_ARGS;
    $this->room_no = $room_no;
    if (isset($RQ_ARGS->TestItems) && $RQ_ARGS->TestItems->is_virtual_room){
      $this->LoadVirtualRoom($RQ_ARGS->TestItems->test_users);
    }
    else{
      $this->LoadRoom($this->room_no);
    }
  }

  function LoadRoom($room_no){
    $this->LoadQueryResponse(UserDataSet::RetriveByRoom($room_no));
  }
  
  function LoadVirtualRoom($user_count){
    $this->LoadQueryResponse(UserDataSet::RetrieveByUserCount($user_count));
  }

  function RetriveByRoom($room_no){
    return mysql_query(
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
      WHERE users.room_no = {$room_no}"
    );
  }

  function RetrieveByUserCount($user_count){
    mysql_query('SET @new_user_no := 0');
    return mysql_query(
      "SELECT
      users.room_no,
      (@new_user_no := @new_user_no + 1) AS user_no,
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
      FROM (SELECT room_no, uname FROM user_entry WHERE 0 < room_no GROUP BY uname) finder
        LEFT JOIN user_entry users USING (room_no, uname)
        LEFT JOIN user_icon icons USING(icon_no)
      ORDER BY RAND()
      LIMIT $user_count
      "
    );
  }

  function LoadQueryResponse($response){
    if ($response === false){
      return false;
    }
    $this->rows = array();
    while(($user = mysql_fetch_object($response, 'User')) !== false){
      $num_users++;
      $user->ParseCompoundParameters();
      if (0 <= $user->user_no) {
        $this->rows[$user->user_no] = $user;
      }
      else {
        $this->kicked[$user->user_no = --$kicked_user_no] = $user;
      }
      $this->names[$user->uname] = $user->user_no;
    }
    return num_users; //または count($this->names)
  }

  function Save(){
    foreach($this->rows as $user) $user->save();
  }

  function ParseCompoundParameters(){
    foreach($this->rows as $user) $user->ParseCompoundParameters();
  }

  function NumberToUname($user_no){
    return $this->rows[$user_no]->uname;
  }

  function UnameToNumber($uname){
    return $this->names[$uname];
  }

  function ByUname($uname){
    $user_no = $this->UnameToNumber($uname);
    return 0 < $user_no ? $this->rows[$user_no] : $this->kicked[$user_no];
  }

  function GetHandleName($uname){
    return $this->ByUname($uname)->handle_name;
  }

  function GetSex($uname){
    return $this->ByUname($uname)->sex;
  }

  function GetRole($uname){
    return $this->ByUname($uname)->role;
  }

  function GetPartners($uname, $strict=false){
    $role = $this->GetRole($uname);
    $partners = array();
    foreach ($this->rows as $user){
      if ($strict ? ($user->is_role($role)) : (strpos($role, $user->role) !== false)){
        $partners[] = $user;
      }
    }
    return $partners;
  }

  function GetLive($uname){
    return $this->ByUname($uname)->live;
  }

  function GetUserCount(){
    return count($this->rows);
  }

  //現在のリクエスト情報に基づいて新しいユーザーをデータベースに登録します。
  function RegisterByRequest(){
    extract($_REQUEST, EXTR_PREFIX_ALL, 'unsafe');
    session_regenerate_id();
    UserDataSet::Register(
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
  function Register($uname, $password, $handle_name, $sex, $profile, $icon_no, $role,
		    $ip_address = '', $session_id = ''){
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
?>
