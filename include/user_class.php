<?php
class User{
  var $main_role;
  var $role_list = array();
  var $partner_list = array();
  var $dead_flag = false;
  var $suicide_flag = false;
  var $revive_flag = false;

  function ParseCompoundParameters(){
    $this->ParseRoles();
    $this->main_role = $this->role_list[0];
  }

  function ParseRoles(){
    $role_list = explode(' ', $this->role);
    $regex = "/([^\[]*)\[(\d+)\]/";
    foreach($role_list as $role){
      if(preg_match($regex, $role, $matches)){
	$this->role_list[] = $matches[1];
	$this->partner_list[$matches[1]][] = $matches[2];
      }
      else{
	$this->role_list[] = $role;
      }
    }
  }

  function IsLive(){
    return ($this->live == 'live');
  }

  function IsDead(){
    return ($this->live == 'dead');
  }

  function IsSameUser($uname){
    return ($this->uname == $uname);
  }

  function IsSelf(){
    global $SELF;
    return $this->IsSameUser($SELF->uname);
  }

  function IsDummyBoy(){
    return $this->IsSameUser('dummy_boy');
  }

  function IsRole($role){
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

  function IsActiveRole($role){
    return ($this->IsRole($role) && ! $this->IsRole('lost_ability'));
  }

  function IsRoleGroup($role){
    $arg = func_get_args();
    foreach($arg as $this_role){
      if(strpos($this->role, $this_role) !== false) return true;
    }
    return false;
  }

  function IsWolf(){
    return $this->IsRoleGroup('wolf');
  }

  function IsFox(){
    return $this->IsRoleGroup('fox');
  }

  function IsLovers(){
    return $this->IsRole('lovers');
  }

  function ChangeLive($live){
    $this->Update('live', "$live");
  }

  function ToDead(){
    if((! ($this->IsLive() || $this->revive_flag)) || $this->dead_flag) return false;
    $this->ChangeLive('dead');
    $this->dead_flag = true;
    return true;
  }

  //死亡処理
  function Kill($reason){
    if(! $this->ToDead()) return false;
    InsertSystemMessage($this->handle_name, $reason);
    if(! $this->IsRole('reporter', 'no_last_words')) SaveLastWords($this->handle_name);
  }

  //突然死処理
  function SuddenDeath($reason = NULL){
    global $MESSAGE, $ROOM;

    if(! $this->ToDead()) return false;
    $this->suicide_flag = true;

    if($reason){ //ショック死は専用の処理を行う
      InsertSystemTalk($this->handle_name . $MESSAGE->vote_sudden_death, ++$ROOM->system_time);
      InsertSystemMessage($this->handle_name, 'SUDDEN_DEATH_' . $reason);
      if(! $this->IsRole('reporter', 'no_last_words')) SaveLastWords($this->handle_name);
    }
    else{
      InsertSystemTalk($this->handle_name . $MESSAGE->sudden_death, ++$ROOM->system_time);
    }
    mysql_query('COMMIT'); //一応コミット
  }

  //蘇生処理
  function Revive(){
    if(! $this->IsDead() || $this->revive_flag) return false;
    $this->ChangeLive('live');
    InsertSystemMessage($this->handle_name, 'REVIVE_SUCCESS');
    $this->revive_flag = true;
  }

  function ChangeRole($role){
    $this->Update('role', "$role");
  }

  function AddRole($role){
    $this->Update('role', $this->role . " $role");
    /* キャッシュの更新は行わないでおく
      $this->role .= " $role";
      $this->updated[] = 'role';
      $this->ParseRoles();
    */
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

  function Update($item, $value){
    global $ROOM;

    if($ROOM->test_mode){
      echo "User : {$this->uname} : Change $item : $value <br>";
      return;
    }
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}'";
    mysql_query("UPDATE user_entry SET $item = '$value' $query");
    mysql_query('COMMIT');
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

  //占い師の判定
  function DistinguishMage($reverse = false){
    //白狼以外の狼と不審者は人狼判定
    $result = (($this->IsWolf() && ! $this->IsRole('boss_wolf')) || $this->IsRole('suspect'));
    if($reverse) $result = (! $result);
    return ($result ? 'wolf' : 'human');
  }

  //所属陣営判別
  function DistinguishCamp(){
    if($this->IsWolf() || $this->IsRoleGroup('mad')) return 'wolf';
    if($this->IsFox()) return 'fox';
    if($this->IsRole('cupid')) return 'lovers';
    if($this->IsRole('quiz')) return 'quiz';
    return 'human';
  }

  //役職をパースして省略名を返す
  function MakeShortRoleName(){
    global $GAME_CONF;

    //メイン役職を取得
    $camp = $this->DistinguishCamp();
    $name = $GAME_CONF->short_role_list[$this->main_role];
    $role_str = '<span class="add-role"> [';
    $role_str .= ($camp == 'human' ? $name : '<span class="' . $camp . '">' . $name . '</span>');

    //サブ役職を追加
    $sub_role_list = array_slice($this->role_list, 1);
    foreach($GAME_CONF->short_role_list as $role => $name){
      if(in_array($role, $sub_role_list)){
	$role_str .= ($role == 'lovers' ? '<span class="lovers">' . $name . '</span>' : $name);
      }
    }

    return $role_str . '] (' . $this->uname . ')</span>';
  }
}

class UserDataSet{
  var $room_no;
  var $rows = array();
  var $kicked = array();
  var $names = array();

  function UserDataSet($request){
    $this->room_no = $request->room_no;
    if(isset($request->TestItems) && $request->TestItems->is_virtual_room){
      if(is_int($request->TestItems->test_users)){
	$this->LoadVirtualRoom($request->TestItems->test_users);
      }
      else{
	$this->LoadUsers($request->TestItems->test_users);
      }
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
      WHERE users.room_no = {$room_no}
      ORDER BY users.user_no"
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
    if($response === false) return false;
    $this->rows = array();
    $kicked_user_no = 0;
    while(($user = mysql_fetch_object($response, 'User')) !== false){
      $num_users++;
      $user->ParseCompoundParameters();
      if($user->user_no >= 0){
	$this->rows[$user->user_no] = $user;
      }
      else {
	$this->kicked[$user->user_no = --$kicked_user_no] = $user;
      }
      $this->names[$user->uname] = $user->user_no;
    }
    return $num_users; //または count($this->names)
  }

  function LoadUsers($user_list){
    if($user_list === false) return false;
    $this->rows = array();
    $kicked_user_no = 0;
    foreach($user_list as $user){
      $num_users++;
      $user->ParseCompoundParameters();
      if($user->user_no >= 0){
	$this->rows[$user->user_no] = $user;
      }
      else{
	$this->kicked[$user->user_no] = $user;
      }
      $this->names[$user->uname] = $user->user_no;
    }
    return $num_users; //または count($this->names)
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

  function ByID($user_no){
    return $this->rows[$user_no];
  }

  function ByUname($uname){
    $user_no = $this->UnameToNumber($uname);
    return ($user_no > 0 ? $this->rows[$user_no] : $this->kicked[$user_no]);
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

  function GetPartners($uname, $strict = false){
    $role = $this->GetRole($uname);
    $partners = array();
    foreach($this->rows as $user){
      if($strict ? ($user->IsRole($role)) : ($user->IsRoleGroup($role))){
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

  function is_appear($role){
    foreach($this->rows as $this_user){
      if($this_user->main_role == $role) return true;
    }
    return false;
  }

  function GetLivingUsers(){
    $array = array();
    foreach($this->rows as $this_user){
      if($this_user->IsLive()) $array[] = $this_user->uname;
    }
    return $array;
  }

  function GetLivingWolves(){
    $array = array();
    foreach($this->rows as $this_user){
      if($this_user->IsLive() && $this_user->IsWolf()) $array[] = $this_user->uname;
    }
    return $array;
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
