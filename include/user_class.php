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

  //指定したユーザーデータのセットを名前つき配列にして返します。
  //このメソッドはextract関数を使用してオブジェクトのプロパティを
  //迅速にローカルに展開するために使用できます。
  function ToArray($type = NULL){
    switch($type){
      case 'profiles':
	$result['profile'] = $this->profile;
	$result['color'] = $this->color;
	$result['icon_width'] = $this->icon_width;
	$result['icon_height'] = $this->icon_height;
	break;

    case 'flags':
      $result['dead_flag'] = $this->dead_flag;
      $result['suicide_flag'] = $this->suicide_flag;
      $result['revive_flag'] = $this->revive_flag;
      break;

    case 'roles':
      $result['main_role'] = $this->main_role;
      $result['role_list'] = $this->role_list;
      $result['partner_list'] = $this->partner_list;
      break;

    default:
      return array ('user_no'     => $this->user_no,
		    'uname'       => $this->uname,
		    'handle_name' => $this->handle_name,
		    'role'        => $this->role,
		    'profile'     => $this->profile,
		    'icon'        => $this->icon_filename,
		    'color'       => $this->color);
    }
    return $result;
  }

  function ParseRoles(){
    $role_list = explode(' ', $this->role);
    $regex_partner = '/([^\[]+)\[([^\]]+)\]/';
    $regex_status  = '/([^-]+)-(.+)/';
    foreach($role_list as $role){
      if(preg_match($regex_partner, $role, $match_partner)){
	$this->role_list[] = $match_partner[1];
	if(preg_match($regex_status, $match_partner[2], $match_status)){
	  $this->partner_list[$match_partner[1]][$match_status[1]] = $match_status[2];
	}
	else{
	  $this->partner_list[$match_partner[1]][] = $match_partner[2];
	}
      }
      else{
	$this->role_list[] = $role;
      }
    }
    $this->role_list = array_unique($this->role_list);
  }

  function IsLive($strict = false){
    if($strict){
      if($this->suicide_flag) return false;
      if($this->revive_flag) return true;
      if($this->dead_flag) return false;
    }
    return ($this->live == 'live');
  }

  function IsDead(){
    return ($this->live == 'dead');
  }

  function IsSameID($user_no){
    return ($this->user_no == $user_no);
  }

  function IsSameUser($uname){
    return ($this->uname == $uname);
  }

  function IsSelf(){
    global $SELF;
    return $this->IsSameID($SELF->user_no);
  }

  function IsDummyBoy(){
    return $this->IsSameUser('dummy_boy');
  }

  function IsRole($role){
    if(! is_array($this->role_list)) return false;
    $arg = func_get_args();
    if(is_array($arg[0])) $arg = $arg[0];
    if(count($arg) > 1){
      return (count(array_intersect($arg, $this->role_list)) > 0);
    }
    else{
      return in_array($arg[0], $this->role_list);
    }
  }

  function IsActiveRole($role){
    return ($this->IsRole($role) && ! $this->IsRole('lost_ability'));
  }

  function IsRoleGroup($role){
    $arg = func_get_args();
    foreach($arg as $this_target_role){
      foreach($this->role_list as $this_role){
	if(strpos($this_role, $this_target_role) !== false) return true;
      }
    }
    return false;
  }

  function IsWolf($talk_flag = false){
    if(! $this->IsRoleGroup('wolf')) return false;
    return ($talk_flag ? ! $this->IsRole('silver_wolf') : true);
  }

  function IsFox($talk_flag = false){
    if(! $this->IsRoleGroup('fox')) return false;
    return ($talk_flag ? ! $this->IsRole('silver_fox', 'child_fox') : true);
  }

  function IsLovers(){
    return $this->IsRole('lovers');
  }

  function IsPartner($type, $target){
    $array = $this->partner_list[$type];
    if(! is_array($array)) return false;
    if(is_array($target)){
      if(! is_array($target[$type])) return false;
      return (count(array_intersect($array, $target[$type])) > 0);
    }
    else{
      return in_array($target, $array);
    }
  }

  //日数に応じた憑依先の ID を取得
  function GetPossessedTarget($type, $date){
    $target_list = $this->partner_list[$type];
    if(! is_array($target_list)) return false;

    $date_list = array_keys($target_list);
    krsort($date_list);
    foreach($date_list as $this_date){
      if($this_date <= $date) return $target_list[$this_date];
    }
    return false;
  }

  //基幹死亡処理
  function ToDead(){
    if(! ($this->IsLive() || $this->revive_flag) || $this->dead_flag) return false;
    $this->Update('live', 'dead');
    $this->dead_flag = true;
    return true;
  }

  //蘇生処理
  function Revive(){
    if(! $this->IsDead() || $this->revive_flag) return false;
    $this->Update('live', 'live');
    InsertSystemMessage($this->handle_name, 'REVIVE_SUCCESS');
    $this->revive_flag = true;
    return true;
  }

  function ChangeRole($role){
    $this->Update('role', "$role");

    //$this->role .= " $role"; //キャッシュ本体の更新は行わない
    $this->updated['role'] = $role;
  }

  function AddRole($role){
    $base_role = ($this->updated['role'] ? $this->updated['role'] : $this->role);
    if(strpos($base_role, $role) !== false) return false; //同じ役職は追加しない
    $this->ChangeRole($base_role . " $role");
  }

  function ReplaceRole($target, $replace){
    $base_role = ($this->updated['role'] ? $this->updated['role'] : $this->role);
    $new_role = str_replace($target, $replace, $base_role);
    $this->ChangeRole($new_role);
  }

  /*
    このメソッドは橋姫実装時のために予約されています。
     スペースが２つ続いている箇所は空の役職と認識されるおそれがあります。
     本来はParseRole側でpreg_split()などを使用するべきですが、役職が減る状況の方が少ないため、
     削除側で調節するものとします。(2009-07-05 enogu)
  */
  /*
  function RemoveRole($role){
    $this->role = str_replace('  ', ' ', str_replace($role, '', $this->role));
    $this->updated[] = 'role';
    $this->ParseRoles();
  }
  */

  //遺言を取得して保存する
  function SaveLastWords($handle_name = NULL){
    global $ROOM;

    if(! $this->IsDummyBoy() && $this->IsRole('reporter', 'no_last_words')) return;
    if(is_null($handle_name)) $handle_name = $this->handle_name;
    if($ROOM->test_mode){
      InsertSystemMessage($handle_name, 'LAST_WORDS');
      return;
    }

    $query = "SELECT last_words FROM user_entry WHERE room_no = {$this->room_no} " .
      "AND uname = '{$this->uname}' AND user_no > 0";
    if(($last_words = FetchResult($query)) != ''){
      InsertSystemMessage($handle_name . "\t" . $last_words, 'LAST_WORDS');
    }
  }

  function Update($item, $value){
    global $ROOM;

    if($ROOM->test_mode){
      echo "User : {$this->uname} : Change $item : $value <br>";
      return;
    }
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}'";
    mysql_query("UPDATE user_entry SET $item = '$value' $query AND user_no > 0");
    mysql_query('COMMIT');
  }

  function Save(){
    if(isset($this->updated)){
      foreach($this->updated as $item){
        $update_list[] = "$item = '{$this->item}'";
      }
      $update = implode(', ', $update_list);
      mysql_query("UPDATE user_entry SET $update WHERE room_no = {$this->room_no} AND uname = '{$this->uname}'");
    }
  }

  //占い師の判定
  function DistinguishMage($reverse = false){
    //白狼以外の狼、黒狐、不審者は人狼判定
    $result = (($this->IsWolf() && ! $this->IsRole('boss_wolf')) ||
	       $this->IsRole('black_fox', 'suspect'));
    if($reverse) $result = (! $result);
    return ($result ? 'wolf' : 'human');
  }

  //所属陣営判別
  function DistinguishCamp(){
    if($this->IsWolf() || $this->IsRoleGroup('mad')) return 'wolf';
    if($this->IsFox()) return 'fox';
    if($this->IsRoleGroup('cupid')) return 'lovers';
    if($this->IsRole('quiz')) return 'quiz';
    if($this->IsRoleGroup('chiroptera')) return 'chiroptera';
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
    if(! is_array($user_list)) return false;
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

  function HandleNameToUname($handle_name){
    foreach($this->rows as $user){
      if($handle_name == $user->handle_name) return $user->uname;
    }
    return NULL;
  }

  function ByID($user_no){
    if(is_null($user_no)) return new User();
    return ($user_no > 0 ? $this->rows[$user_no] : $this->kicked[$user_no]);
  }

  function ByUname($uname){
    return $this->ByID($this->UnameToNumber($uname));
  }

  function TraceVirtual($user_no, $role, $type){
    global $ROOM;

    $user = $this->ByID($user_no);
    if(! $ROOM->IsPlaying() || ! $user->IsRole($role)) return $user;

    $virtual_id = $user->GetPossessedTarget($type, $ROOM->date);
    if($virtual_id === false) return $user;
    return $this->ByID($virtual_id);
  }

  function ByVirtual($user_no){
    return $this->TraceVirtual($user_no, 'possessed_wolf', 'possessed_target');
  }

  function ByReal($user_no){
    return $this->TraceVirtual($user_no, 'possessed', 'possessed');
  }

  function ByVirtualUname($uname){
    return $this->ByVirtual($this->UnameToNumber($uname));
  }

  function ByRealUname($uname){
    return $this->ByReal($this->UnameToNumber($uname));
  }

  function GetHandleName($uname){
    return $this->ByUname($uname)->handle_name;
  }

  function GetVirtualHandleName($uname){
    return $this->ByVirtualUname($uname)->handle_name;
  }

  function GetSex($uname){
    return $this->ByUname($uname)->sex;
  }

  function GetRole($uname){
    return $this->ByUname($uname)->role;
  }

  function GetLive($uname){
    return $this->ByUname($uname)->live;
  }

  function GetUserCount(){
    return count($this->rows);
  }

  //役職の出現判定関数
  function IsAppear($role){
    foreach($this->rows as $this_user){
      if($this_user->main_role == $role) return true;
    }
    return false;
  }

  //仮想的な生死状態を返す
  function IsVirtualLive($user_no){
    //憑依されている場合は憑依者の生死を返す
    $real_user = $this->ByReal($user_no);
    if($real_user->user_no != $user_no) return $real_user->IsLive();

    //憑依先に移動している場合は常に死亡扱い
    if($this->ByVirtual($user_no)->user_no != $user_no) return false;

    //憑依が無ければ本人の生死を返す
    return $this->ByID($user_no)->IsLive();
  }

  function GetLivingUsers($strict = false){
    $array = array();
    foreach($this->rows as $this_user){
      if($this_user->IsLive($strict)) $array[] = $this_user->uname;
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

  //死亡処理
  function Kill($user_no, $reason = NULL){
    $user = $this->ByReal($user_no);
    if(! $user->ToDead()) return false;

    if($reason){
      $handle_name = $this->GetVirtualHandleName($user->uname);
      InsertSystemMessage($handle_name, $reason);
      if($reason != 'POSSESSED_TARGETED') $user->SaveLastWords($handle_name);
    }
    return true;
  }

  //突然死処理
  function SuddenDeath($user_no, $reason = NULL){
    global $MESSAGE, $ROOM;

    $user = $this->ByReal($user_no);
    if(! $this->Kill($user_no, $reason)) return false;
    $user->suicide_flag = true;

    $sentence = ($reason ? 'vote_sudden_death' : 'sudden_death');
    $handle_name =  $this->GetVirtualHandleName($user->uname);
    InsertSystemTalk($handle_name . $MESSAGE->$sentence, ++$ROOM->system_time);
    return true;
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
    $items = 'room_no, user_no, uname, password, handle_name, sex, profile, icon_no, role';
    $values = "$this->room_no, " .
      "(SELECT MAX(user_no) + 1 FROM user_entry WHERE room_no = {$this->room_no}), " .
      "'$uname', '$password', '$handle_name', '$sex', '$profile', $icon_no, '$role'";
    InsertDatabase('user_entry', $items, $value);
    $USERS->Load();
  }
}
?>
