<?php
class User{
  var $main_role;
  var $role_list = array();
  var $partner_list = array();
  var $vote_flag = false;
  var $dead_flag = false;
  var $suicide_flag = false;
  var $revive_flag = false;

  function ParseCompoundParameters(){
    $this->ParseRoles();
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

  //役職情報の展開処理
  function ParseRoles($role = NULL){
    //初期化処理
    if($role != NULL) $this->role = $role;
    $this->role_list = array();
    $this->partner_list = array();

    //展開用の正規表現をセット
    $regex_partner = '/([^\[]+)\[([^\]]+)\]/'; //恋人型 (role[id])
    $regex_status  = '/([^-]+)-(.+)/';         //憑狼型 (role[date-id])

    //展開処理
    $role_list = array();
    $explode_list = explode(' ', $this->role);
    foreach($explode_list as $role){
      if(preg_match($regex_partner, $role, $match_partner)){
	$role_list[] = $match_partner[1];
	if(preg_match($regex_status, $match_partner[2], $match_status)){
	  $this->partner_list[$match_partner[1]][$match_status[1]] = $match_status[2];
	}
	else{
	  $this->partner_list[$match_partner[1]][] = $match_partner[2];
	}
      }
      else{
	$role_list[] = $role;
      }
    }

    //代入処理
    $this->role_list = array_unique($role_list);
    $this->main_role = $this->role_list[0];
  }

  //現在の仮想的な生死情報
  function IsDeadFlag($strict = false){
    if(! $strict) return NULL;
    if($this->suicide_flag) return true;
    if($this->revive_flag) return false;
    if($this->dead_flag) return true;
    return NULL;
  }

  function IsLive($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return (is_null($dead) ? ($this->live == 'live') : ! $dead);
  }

  function IsDead($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return (is_null($dead) ? ($this->live == 'dead') : $dead);
  }

  function IsSame($uname){
    return ($this->uname == $uname);
  }

  function IsSelf(){
    global $SELF;
    return $this->IsSame($SELF->uname);
  }

  function IsDummyBoy(){
    return $this->IsSame('dummy_boy');
  }

  function IsRole($role){
    $role_list = func_get_args();
    if(is_array($role_list[0])) $role_list = $role_list[0];
    if(count($role_list) > 1){
      return (count(array_intersect($role_list, $this->role_list)) > 0);
    }
    else{
      return in_array($role_list[0], $this->role_list);
    }
  }

  function IsActiveRole($role){
    return ($this->IsRole($role) && ! $this->IsRole('lost_ability'));
  }

  function IsRoleGroup($role){
    $role_list = func_get_args();
    foreach($role_list as $target_role){
      foreach($this->role_list as $this_role){
	if(strpos($this_role, $target_role) !== false) return true;
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

  //毒能力の発動判定
  function IsPoison(){
    global $ROOM;

    if(! $this->IsRoleGroup('poison')) return false;
    if($this->IsRole('dummy_poison')) return false; //夢毒者
    if($this->IsRole('poison_guard')) return $ROOM->IsNight(); //騎士
    if($this->IsRole('incubate_poison')) return ($ROOM->date >= 5); //潜毒者は 5 日目以降
    return true;
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

  //所属陣営判別
  function DistinguishCamp(){
    if($this->IsWolf() || $this->IsRoleGroup('mad')) return 'wolf';
    if($this->IsFox()) return 'fox';
    if($this->IsRoleGroup('cupid')) return 'lovers';
    if($this->IsRole('quiz')) return 'quiz';
    if($this->IsRoleGroup('chiroptera')) return 'chiroptera';
    return 'human';
  }

  //占い師の判定
  function DistinguishMage($reverse = false){
    //白狼以外の狼、黒狐、不審者は人狼判定
    $result = (($this->IsWolf() && ! $this->IsRole('boss_wolf')) ||
	       $this->IsRole('black_fox', 'suspect'));
    if($reverse) $result = (! $result);
    return ($result ? 'wolf' : 'human');
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

  //個別 DB 更新処理
  function Update($item, $value){
    global $ROOM;

    if($ROOM->test_mode){
      PrintData($value, 'Change [' . $item . '] (' . $this->uname . ')');
      return;
    }
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    mysql_query("UPDATE user_entry SET $item = '$value' $query");
    mysql_query('COMMIT');
  }

  //総合 DB 更新処理 (この関数はまだ実用されていません)
  function Save(){
    if(empty($this->updated)) return false;
    foreach($this->updated as $item){
      $update_list[] = "$item = '{$this->item}'";
    }
    $update = implode(', ', $update_list);
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    mysql_query("UPDATE user_entry SET $update $query");
    mysql_query('COMMIT');
  }

  //基幹死亡処理
  function ToDead(){
    if($this->IsDead(true)) return false;
    $this->Update('live', 'dead');
    $this->dead_flag = true;
    return true;
  }

  //蘇生処理
  function Revive($virtual = false){
    if($this->IsLive(true)) return false;
    $this->Update('live', 'live');
    $this->revive_flag = true;
    if(! $virtual) InsertSystemMessage($this->handle_name, 'REVIVE_SUCCESS');
    return true;
  }

  //役職更新処理
  function ChangeRole($role){
    $this->Update('role', "$role");

    //$this->role .= " $role"; //キャッシュ本体の更新は行わない
    $this->updated['role'] = $role;
  }

  //役職追加処理
  function AddRole($role){
    $base_role = ($this->updated['role'] ? $this->updated['role'] : $this->role);
    if(strpos($base_role, $role) !== false) return false; //同じ役職は追加しない
    $this->ChangeRole($base_role . " $role");
  }

  //役職置換処理
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

  function ReturnPossessed($type, $date){
    $this->AddRole("${type}[{$date}-{$this->user_no}]");
    #$this->Revive();
    return true;
  }

  //遺言を取得して保存する
  function SaveLastWords($handle_name = NULL){
    global $ROOM;

    if(! $this->IsDummyBoy() && $this->IsRole('reporter', 'no_last_words')) return;
    if(is_null($handle_name)) $handle_name = $this->handle_name;
    if($ROOM->test_mode){
      InsertSystemMessage($handle_name . ' (' . $this->uname . ')', 'LAST_WORDS');
      return;
    }

    $query = "SELECT last_words FROM user_entry WHERE room_no = {$this->room_no} " .
      "AND uname = '{$this->uname}' AND user_no > 0";
    if(($last_words = FetchResult($query)) != ''){
      InsertSystemMessage($handle_name . "\t" . $last_words, 'LAST_WORDS');
    }
  }
}

class UserDataSet{
  var $room_no;
  var $rows = array();
  var $kicked = array();
  var $names = array();

  function UserDataSet($request){ $this->__construct($request); }

  function __construct($request){
    $this->room_no = $request->room_no;
    $this->LoadRoom($request);
  }

  function LoadRoom($request){
    if(isset($request->TestItems) && $request->TestItems->is_virtual_room){
      $user_list = $request->TestItems->test_users;
      if(is_int($user_list)) $user_list = $this->RetriveByUserCount($user_list);
    }
    else{
      $user_list = $this->RetriveByRoom($request->room_no);
    }
    $this->LoadUsers($user_list);
  }

  function RetriveByRoom($room_no){
    $query = "SELECT
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
      ORDER BY users.user_no";
    return FetchObjectArray($query, 'User');
  }

  function RetriveByUserCount($user_count){
    mysql_query('SET @new_user_no := 0');
    $query = "SELECT
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
      FROM (SELECT room_no, uname FROM user_entry WHERE room_no > 0 GROUP BY uname) finder
	LEFT JOIN user_entry users USING(room_no, uname)
	LEFT JOIN user_icon icons USING(icon_no)
      ORDER BY RAND()
      LIMIT $user_count";
    return FetchObjectArray($query, 'User');
  }

  function LoadUsers($user_list){
    if(! is_array($user_list)) return false;

    //初期化処理
    $this->rows = array();
    $this->kicked = array();
    $this->names = array();
    $kicked_user_no = 0;

    foreach($user_list as $user){
      $user->ParseCompoundParameters();
      if($user->user_no >= 0){
	$this->rows[$user->user_no] = $user;
      }
      else{
	$this->kicked[$user->user_no = --$kicked_user_no] = $user;
      }
      $this->names[$user->uname] = $user->user_no;
    }
    return count($this->names);
  }

  function LoadVote(){
    global $ROOM;
    switch($ROOM->day_night){
    case 'beforegame':
      break;

    case 'day':
      $data = "uname, target_uname, vote_number";
      $action = "situation = 'VOTE_KILL'";
      $vote_times = GetVoteTimes();
      $add_query = " AND vote_times = $vote_times";
      break;

    case 'night':
      $data = "uname, target_uname, situation";
      $action = "situation <> 'VOTE_KILL'";
      break;

    default:
      return false;
    }
    $query = "SELECT {$data} FROM vote WHERE room_no = {$ROOM->id} " .
      "AND date = {$ROOM->date} AND ";
    $vote_list = FetchAssoc($query . $action . $add_query);

    $this->vote_data = array();
    foreach($vote_list as $array){
      $id = $this->ByUname($array['uname'])->user_no;
      unset($array['uname']);
      $this->vote_data[$id] = $array;
    }
    ksort($this->vote_data);
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

  function GetHandleName($uname, $virtual = false){
    $user = ($virtual ? $this->ByVirtualUname($uname) : $this->ByUname($uname));
    return $user->handle_name;
  }

  function GetRole($uname){
    return $this->ByUname($uname)->role;
  }

  function GetUserCount(){
    return count($this->rows);
  }

  //役職の出現判定関数 (現在はメイン役職のみ対応)
  function IsAppear($role){
    foreach($this->rows as $user){
      if($user->main_role == $role) return true;
    }
    return false;
  }

  //仮想的な生死を返す
  function IsVirtualLive($user_no, $strict = false){
    //憑依されている場合は憑依者の生死を返す
    $real_user = $this->ByReal($user_no);
    if($real_user->user_no != $user_no) return $real_user->IsLive($strict);

    //憑依先に移動している場合は常に死亡扱い
    if($this->ByVirtual($user_no)->user_no != $user_no) return false;

    //憑依が無ければ本人の生死を返す
    return $this->ByID($user_no)->IsLive($strict);
  }

  function GetLivingUsers($strict = false){
    $array = array();
    foreach($this->rows as $user){
      if($user->IsLive($strict)) $array[] = $user->uname;
    }
    return $array;
  }

  function GetLivingWolves(){
    $array = array();
    foreach($this->rows as $user){
      if($user->IsLive() && $user->IsWolf()) $array[] = $user->uname;
    }
    return $array;
  }

  //死亡処理
  function Kill($user_no, $reason = NULL){
    $user = $this->ByReal($user_no);
    if(! $user->ToDead()) return false;

    if($reason){
      $virtual_user = $this->ByVirtual($user->user_no);
      InsertSystemMessage($virtual_user->handle_name, $reason);

      //遺言処理
      if($reason == 'POSSESSED_TARGETED') return true;
      $user->SaveLastWords($virtual_user->handle_name);
      if($user != $virtual_user) $virtual_user->SaveLastWords();
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
    $handle_name =  $this->GetHandleName($user->uname, true);
    InsertSystemTalk($handle_name . $MESSAGE->$sentence, ++$ROOM->system_time);
    return true;
  }

  //蘇生処理
  function Revive($user_no){

    $user = $this->ByID($user_no);
    if(! $user->IsDead() || $user->revive_flag) return false;
    $user->Update('live', 'live');
    InsertSystemMessage($user->handle_name, 'REVIVE_SUCCESS');
    $user->revive_flag = true;
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
