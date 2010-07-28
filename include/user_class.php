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
  }

  //指定したユーザーデータのセットを名前つき配列にして返します。
  //このメソッドは extract 関数を使用してオブジェクトのプロパティを
  //迅速にローカルに展開するために使用できます。 (現在は未使用)
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
      return array('user_no'     => $this->user_no,
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
    if(isset($role)) $this->role = $role;
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

  //現在の役職を取得
  function GetRole(){
    return $this->updated['role'] ? $this->updated['role'] : $this->role;
  }

  //現在の所属陣営を取得
  function GetCamp($strict = false){
    global $USERS;

    $type = $strict ? 'win_camp' : 'main_camp';
    if(is_null($this->$type)) $USERS->SetCamp($this, $type);
    return $this->$type;
  }

  //拡張情報を取得
  function GetPartner($type){
    $list = $this->partner_list[$type];
    return is_array($list) ? $list : NULL;
  }

  //日数に応じた憑依先の ID を取得
  function GetPossessedTarget($type, $today){
    if(is_null($target_list = $this->GetPartner($type))) return false;

    $date_list = array_keys($target_list);
    krsort($date_list);
    foreach($date_list as $date){
      if($date <= $today) return $target_list[$date];
    }
    return false;
  }

  //現在の仮想的な生死情報
  function IsDeadFlag($strict = false){
    if(! $strict) return NULL;
    if($this->suicide_flag) return true;
    if($this->revive_flag)  return false;
    if($this->dead_flag)    return true;
    return NULL;
  }

  //生存フラグ判定
  function IsLive($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return is_null($dead) ? ($this->live == 'live') : ! $dead;
  }

  //死亡フラグ判定
  function IsDead($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return is_null($dead) ? ($this->live == 'dead' || $this->IsDrop()) : $dead;
  }

  //蘇生辞退フラグ判定
  function IsDrop(){
    return $this->live == 'drop';
  }

  //同一ユーザ判定
  function IsSame($uname){
    return $this->uname == $uname;
  }

  //自分と同一ユーザ判定
  function IsSelf(){
    global $SELF;
    return $this->IsSame($SELF->uname);
  }

  //身代わり君判定
  function IsDummyBoy($strict = false){
    global $ROOM;

    if($strict && $ROOM->IsQuiz()) return false;
    return $this->IsSame('dummy_boy');
  }

  //役職判定
  function IsRole($role){
    $role_list = func_get_args();
    if(is_array($role_list[0])) $role_list = $role_list[0];
    if(count($role_list) > 1){
      return count(array_intersect($role_list, $this->role_list)) > 0;
    }
    else{
      return in_array($role_list[0], $this->role_list);
    }
  }

  //役職グループ判定
  function IsRoleGroup($role){
    $role_list = func_get_args();
    foreach($role_list as $target_role){
      foreach($this->role_list as $this_role){
	if(strpos($this_role, $target_role) !== false) return true;
      }
    }
    return false;
  }

  //拡張判定
  function IsPartner($type, $target){
    if(is_null($partner_list = $this->GetPartner($type))) return false;

    if(is_array($target)){
      if(! is_array($target_list = $target[$type])) return false;
      return count(array_intersect($partner_list, $target_list)) > 0;
    }
    else{
      return in_array($target, $partner_list);
    }
  }

  //失効タイプの役職判定
  function IsActive($role = NULL){
    $flag = is_null($role) ? true : $this->IsRole($role);
    return $flag && ! $this->lost_flag && ! $this->IsRole('lost_ability');
  }

  //孤立系役職判定
  function IsLonely($role = NULL){
    $flag = is_null($role) ? true : $this->IsRole($role);
    return $flag && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }

  //共有者系判定
  function IsCommon($talk = false){
    if(! $this->IsRoleGroup('common')) return false;
    return $talk ? ! $this->IsRole('dummy_common') : true;
  }

  //上海人形系判定 (人形遣いは含まない)
  function IsDoll(){
    return $this->IsRoleGroup('doll') && ! $this->IsRole('doll_master');
  }

  //人狼系判定
  function IsWolf($talk = false){
    if(! $this->IsRoleGroup('wolf')) return false;
    return $talk ? ! $this->IsLonely() : true;
  }

  //妖狐系判定
  function IsFox($talk = false){
    if(! $this->IsRoleGroup('fox')) return false;
    return $talk ? ! ($this->IsChildFox() || $this->IsLonely()) : true;
  }

  //子狐系判定
  function IsChildFox($vote = false){
    $stack = array('child_fox', 'sex_fox', 'stargazer_fox', 'jammer_fox');
    if(! $vote) $stack[] = 'miasma_fox';
    return $this->IsRole($stack);
  }

  //恋人判定
  function IsLovers(){
    return $this->IsRole('lovers');
  }

  //憑依能力者判定 (被憑依者とコード上で区別するための関数)
  function IsPossessedGroup(){
    return $this->IsRole('possessed_wolf', 'possessed_mad', 'possessed_fox');
  }

  //蘇生能力者判定
  function IsReviveGroup($active = false){
    if(! ($this->IsRoleGroup('cat') || $this->IsRole('revive_medium', 'revive_fox'))) return false;
    return $active ? $this->IsActive() : true;
  }

  //覚醒天狼判定
  function IsLastWolf(){
    global $USERS;

    if(! $this->IsRole('sirius_wolf')) return false;
    if(is_null($this->last_wolf)){
      $living_wolves_list = $USERS->GetLivingWolves();
      //PrintData($living_wolves_list);
      $this->last_wolf = count($living_wolves_list) == 1;
    }
    return $this->last_wolf;
  }

  //難題判定
  function IsChallengeLovers(){
    global $ROOM;
    return $ROOM->date > 1 && $ROOM->date < 5 && $this->IsRole('challenge_lovers');
  }

  //特殊耐性判定
  function IsAvoid($quiz = false){
    $stack = array('detective_common');
    if($quiz) $stack[] = 'quiz';
    return $this->IsRole($stack) || $this->IsLastWolf() || $this->IsChallengeLovers();
  }

  //毒能力の発動判定
  function IsPoison(){
    global $ROOM;

    if(! $this->IsRoleGroup('poison') || $this->IsRole('chain_poison')) return false; //無毒・連毒者
    if($this->IsRole('poison_guard')) return $ROOM->IsNight(); //騎士
    if($this->IsRole('incubate_poison')) return $ROOM->date >= 5; //潜毒者は 5 日目以降
    if($this->IsRole('dummy_poison')) return $ROOM->IsDay(); //夢毒者
    return true;
  }

  //護衛制限判定
  function IsGuardLimited(){
    return $this->IsRole('priest', 'bishop_priest', 'border_priest', 'detective_common',
			 'reporter', 'doll_master') || $this->IsRoleGroup('assassin');
  }

  //所属陣営判別
  function DistinguishCamp(){
    if($this->IsWolf() || $this->IsRoleGroup('mad')) return 'wolf';
    if($this->IsFox()) return 'fox';
    if($this->IsRole('quiz')) return 'quiz';
    if($this->IsRole('vampire')) return 'vampire';
    if($this->IsRoleGroup('chiroptera', 'fairy')) return 'chiroptera';
    if($this->IsRoleGroup('cupid', 'angel')) return 'lovers';
    return 'human';
  }

  //占い師の判定
  function DistinguishMage($reverse = false){
    if($this->IsRole('vampire', 'boss_chiroptera')) return 'chiroptera'; //吸血鬼・大蝙蝠は蝙蝠判定

    //白狼以外の人狼・黒狐・不審者は人狼判定
    $result = (($this->IsWolf() && ! $this->IsRole('boss_wolf')) ||
	       $this->IsRole('black_fox', 'suspect'));
    if($reverse) $result = (! $result);
    return $result ? 'wolf' : 'human';
  }

  //ひよこ鑑定士の判定
  function DistinguishSex(){
    return $this->IsRoleGroup('chiroptera', 'fairy', 'gold') ? 'chiroptera' : 'sex_' . $this->sex;
  }

  //未投票チェック
  function CheckVote($vote_data){
    global $ROOM;

    if($this->IsDummyBoy() || $this->IsDead()) return true;

    if($this->IsRoleGroup('mage')){
      return isset($vote_data['MAGE_DO'][$this->uname]);
    }
    if($this->IsRole('voodoo_killer')){
      return isset($vote_data['VOODOO_KILLER_DO'][$this->uname]);
    }
    if($this->IsWolf()){
      return isset($vote_data['WOLF_EAT']);
    }
    if($this->IsRole('jammer_mad', 'jammer_fox')){
      return isset($vote_data['JAMMER_MAD_DO'][$this->uname]);
    }
    if($this->IsRole('voodoo_mad')){
      return isset($vote_data['VOODOO_MAD_DO'][$this->uname]);
    }
    if($this->IsRole('emerald_fox')){
      if(! $this->IsActive()) return true;
      return isset($vote_data['MAGE_DO'][$this->uname]);
    }
    if($this->IsRole('voodoo_fox')){
      return isset($vote_data['VOODOO_FOX_DO'][$this->uname]);
    }
    if($this->IsChildFox(true)){
      return isset($vote_data['CHILD_FOX_DO'][$this->uname]);
    }
    if($this->IsRoleGroup('fairy') && ! $this->IsRole('mirror_fairy')){
      return isset($vote_data['FAIRY_DO'][$this->uname]);
    }

    if($ROOM->date == 1){ //初日限定
      if($this->IsRole('mind_scanner')){
	return isset($vote_data['MIND_SCANNER_DO'][$this->uname]);
      }
      if($this->IsRoleGroup('cupid', 'angel') || $this->IsRole('dummy_chiroptera', 'mirror_fairy')){
	return isset($vote_data['CUPID_DO'][$this->uname]);
      }
      if($this->IsRoleGroup('mania')){
	return isset($vote_data['MANIA_DO'][$this->uname]);
      }

      if($ROOM->IsOpenCast()) return true;
      if($this->IsRole('evoke_scanner')){
	return isset($vote_data['MIND_SCANNER_DO'][$this->uname]);
      }
      return true;
    }

    //二日目以降
    if($this->IsRole('escaper')){
      return isset($vote_data['ESCAPE_DO'][$this->uname]);
    }
    if($this->IsRoleGroup('guard')){
      return isset($vote_data['GUARD_DO'][$this->uname]);
    }
    if($this->IsRole('reporter')){
      return isset($vote_data['REPORTER_DO'][$this->uname]);
    }
    if($this->IsRole('anti_voodoo')){
      return isset($vote_data['ANTI_VOODOO_DO'][$this->uname]);
    }
    if($this->IsRoleGroup('assassin')){
      if(is_array($vote_data['ASSASSIN_NOT_DO']) &&
	 array_key_exists($this->uname, $vote_data['ASSASSIN_NOT_DO'])) return true;
      return isset($vote_data['ASSASSIN_DO'][$this->uname]);
    }
    if($this->IsRole('dream_eater_mad')){
      return isset($vote_data['DREAM_EAT'][$this->uname]);
    }
    if($this->IsRole('trap_mad')){
      if(! $this->IsActive()) return true;
      if(is_array($vote_data['TRAP_MAD_NOT_DO']) &&
	 array_key_exists($this->uname, $vote_data['TRAP_MAD_NOT_DO'])) return true;
      return isset($vote_data['TRAP_MAD_DO'][$this->uname]);
    }
    if($this->IsRole('possessed_mad', 'possessed_fox')){
      if(! $this->IsActive()) return true;
      if(is_array($vote_data['POSSESSED_NOT_DO']) &&
	 array_key_exists($this->uname, $vote_data['POSSESSED_NOT_DO'])) return true;
      return isset($vote_data['POSSESSED_DO'][$this->uname]);
    }
    if($this->IsRole('vampire')){
      return isset($vote_data['VAMPIRE_DO'][$this->uname]);
    }

    if($ROOM->IsOpenCast()) return true;
    if($this->IsReviveGroup(true)){
      if(is_array($vote_data['POISON_CAT_NOT_DO']) &&
	 array_key_exists($this->uname, $vote_data['POISON_CAT_NOT_DO'])) return true;
      return isset($vote_data['POISON_CAT_DO'][$this->uname]);
    }
    return true;
  }

  //役職をパースして省略名を返す
  function GenerateShortRoleName($heaven = false){
    global $ROLE_DATA, $USERS;

    //メイン役職を取得
    $camp = $this->GetCamp();
    $name = $ROLE_DATA->short_role_list[$this->main_role];
    $str = '<span class="add-role"> [';
    $str .= $camp == 'human' ? $name : '<span class="' . $camp . '">' . $name . '</span>';

    //サブ役職を追加
    $sub_role_list = array_slice($this->role_list, 1);
    foreach($ROLE_DATA->short_role_list as $role => $name){
      if(in_array($role, $sub_role_list)){
	$str .= ($role == 'lovers' || $role == 'challenge_lovers') ?
	  '<span class="lovers">' . $name . '</span>' : $name;
      }
    }
    $uname = $heaven ? $this->uname : $USERS->TraceExchange($this->user_no)->uname;
    return $str . '] (' . $uname . ')</span>';
  }

  //個別 DB 更新処理
  function Update($item, $value){
    global $ROOM;

    if($ROOM->test_mode){
      PrintData($value, 'Change [' . $item . '] (' . $this->uname . ')');
      return;
    }
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    return SendQuery("UPDATE user_entry SET {$item} = '{$value}' {$query}", true);
  }

  //総合 DB 更新処理 (この関数はまだ実用されていません)
  function Save(){
    if(empty($this->updated)) return false;
    foreach($this->updated as $item){
      $update_list[] = "$item = '{$this->item}'";
    }
    $update = implode(', ', $update_list);
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    SendQuery("UPDATE user_entry SET {$update} {$query}", true);
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
    global $ROOM;

    if($this->IsLive(true)) return false;
    $this->Update('live', 'live');
    $this->revive_flag = true;
    if(! $virtual) $ROOM->SystemMessage($this->handle_name, 'REVIVE_SUCCESS');
    return true;
  }

  //役職更新処理
  function ChangeRole($role){
    $this->Update('role', $role);
    $this->updated['role'] = $role; //キャッシュ本体の更新は行わない
  }

  //役職追加処理
  function AddRole($role){
    $base_role = $this->GetRole();
    if(in_array($role, explode(' ', $base_role))) return false; //同じ役職は追加しない
    $this->ChangeRole($base_role . ' ' . $role);
  }

  //仮想役職追加処理 (キャッシュ限定)
  function AddVirtualRole($role){
    if(! in_array($role, $this->role_list)) $this->role_list[] = $role;
  }

  //役職置換処理
  function ReplaceRole($target, $replace){
    $this->ChangeRole(str_replace($target, $replace, $this->GetRole()));
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

  function LostAbility(){
    $this->AddRole('lost_ability');
    $this->lost_flag = true;
  }

  function ReturnPossessed($type, $date){
    $this->AddRole("${type}[{$date}-{$this->user_no}]");
    return true;
  }

  //遺言を取得して保存する
  function SaveLastWords($handle_name = NULL){
    global $ROOM;

    //保存しない役職をチェック
    if(! $this->IsDummyBoy() &&
       $this->IsRole('escaper', 'reporter', 'soul_assassin', 'evoke_scanner', 'no_last_words',
		     'possessed_exchange')) return;
    if(is_null($handle_name)) $handle_name = $this->handle_name;
    if($ROOM->test_mode){
      $ROOM->SystemMessage($handle_name . ' (' . $this->uname . ')', 'LAST_WORDS');
      return;
    }

    $query = "SELECT last_words FROM user_entry WHERE room_no = {$this->room_no} " .
      "AND uname = '{$this->uname}' AND user_no > 0";
    if(($last_words = FetchResult($query)) != ''){
      $ROOM->SystemMessage($handle_name . "\t" . $last_words, 'LAST_WORDS');
    }
  }

  //投票処理
  function Vote($action, $target = NULL, $vote_number = NULL){
    global $RQ_ARGS, $ROOM;

    $items = 'room_no, date, uname, situation';
    $values = "{$ROOM->id}, $ROOM->date, '{$this->uname}', '{$action}'";
    if(isset($target)){
      $items .= ', target_uname';
      $values .= ", '{$target}'";
    }
    if(isset($vote_number)){
      $items .= ', vote_number, vote_times';
      $values .= ", '{$vote_number}', '{$RQ_ARGS->vote_times}'";
    }
    return InsertDatabase('vote', $items, $values);
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

  //村情報のロード処理
  function LoadRoom($request){
    if($request->IsVirtualRoom()){ //仮想モード
      $user_list = $request->TestItems->test_users;
      if(is_int($user_list)) $user_list = $this->RetriveByUserCount($user_list);
    }
    elseif($request->entry_user){ //入村処理用
      $user_list = $this->RetriveByEntryUser($request->room_no);
    }
    else{
      $user_list = $this->RetriveByRoom($request->room_no);
    }
    $this->LoadUsers($user_list);
  }

  //特定の村のユーザ情報を取得する
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
    return FetchObject($query, 'User');
  }

  //指定した人数分のユーザ情報を全村からランダムに取得する
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
      LIMIT {$user_count}";
    return FetchObject($query, 'User');
  }

  //入村処理用のユーザデータを取得する
  function RetriveByEntryUser($room_no){
    $query = "SELECT room_no, user_no, uname, handle_name, live, ip_address
      FROM user_entry WHERE room_no = {$room_no} ORDER BY user_no";
    return FetchObject($query, 'User');
  }

  //取得したユーザ情報を User クラスでパースして登録する
  function LoadUsers($user_list){
    if(! is_array($user_list)) return false;

    //初期化処理
    $this->rows = array();
    $this->kicked = array();
    $this->names = array();
    $kicked_user_no = 0;

    foreach($user_list as $user){
      $user->ParseCompoundParameters();
      if($user->user_no >= 0 && $user->live != 'kick'){ //KICK 判定
	$this->rows[$user->user_no] = $user;
      }
      else{
	$this->kicked[$user->user_no = --$kicked_user_no] = $user;
      }
      $this->names[$user->uname] = $user->user_no;
    }
    $this->SetEvent();
    return count($this->names);
  }

  function ParseCompoundParameters(){
    foreach($this->rows as $user) $user->ParseCompoundParameters();
  }

  //ユーザ ID - ユーザ名変換
  function NumberToUname($user_no){
    return $this->rows[$user_no]->uname;
  }

  //ユーザ名 - ユーザ ID 変換
  function UnameToNumber($uname){
    return $this->names[$uname];
  }

  //HN - ユーザ名変換
  function HandleNameToUname($handle_name){
    foreach($this->rows as $user){
      if($handle_name == $user->handle_name) return $user->uname;
    }
    return NULL;
  }

  //ユーザ情報取得 (ユーザ ID 経由)
  function ByID($user_no){
    if(is_null($user_no)) return new User();
    return $user_no > 0 ? $this->rows[$user_no] : $this->kicked[$user_no];
  }

  //ユーザ情報取得 (ユーザ名経由)
  function ByUname($uname){
    return $this->ByID($this->UnameToNumber($uname));
  }

  //ユーザ情報取得 (HN 経由)
  function ByHandleName($handle_name){
    return $this->ByUname($this->HandleNameToUname($handle_name));
  }

  //ユーザ情報取得 (クッキー経由)
  function BySession(){
    global $SESSION;
    return $this->TraceExchange($SESSION->GetUser());
  }

  //憑依情報追跡
  function TraceVirtual($user_no, $type){
    global $ROOM;

    $user = $this->ByID($user_no);
    if(! $ROOM->IsPlaying()) return $user;
    if($type == 'possessed'){
      if(! $user->IsRole($type)) return $user;
    }
    elseif(! $user->IsPossessedGroup()){
      return $user;
    }

    $id = $user->GetPossessedTarget($type, $ROOM->date);
    return $id === false ? $user : $this->ByID($id);
  }

  //交換憑依情報追跡
  function TraceExchange($user_no){
    global $ROOM;

    $user = $this->ByID($user_no);
    $type = 'possessed_exchange';
    if(! $user->IsRole($type) || ! $ROOM->IsPlaying() ||
       (! $ROOM->log_mode && $user->IsDead())) return $user;

    $stack = $user->GetPartner($type);
    return is_array($stack) && $ROOM->date > 2 ? $this->ByID(array_shift($stack)) : $user;
  }

  //ユーザ情報取得 (憑依先ユーザ ID 経由)
  function ByVirtual($user_no){
    return $this->TraceVirtual($user_no, 'possessed_target');
  }

  //ユーザ情報取得 (憑依元ユーザ ID 経由)
  function ByReal($user_no){
    return $this->TraceVirtual($user_no, 'possessed');
  }

  //ユーザ情報取得 (憑依先ユーザ名経由)
  function ByVirtualUname($uname){
    return $this->ByVirtual($this->UnameToNumber($uname));
  }

  //ユーザ情報取得 (憑依元ユーザ名経由)
  function ByRealUname($uname){
    return $this->ByReal($this->UnameToNumber($uname));
  }

  //HN 取得
  function GetHandleName($uname, $virtual = false){
    $user = $virtual ? $this->ByVirtualUname($uname) : $this->ByUname($uname);
    return $user->handle_name;
  }

  //役職情報取得
  function GetRole($uname){
    return $this->ByUname($uname)->role;
  }

  //ユーザ数カウント
  function GetUserCount($strict = false){
    if(! $strict) return count($this->rows);
    $count = 0;
    foreach($this->rows as $user){
      if($user->user_no > 0) $count++;
    }
    return $count;
  }

  //所属陣営を判定してキャッシュする
  function SetCamp($user, $type){
    if($type == 'win_camp' && $user->IsLovers()){
      $user->$type = 'lovers';
      return;
    }

    $target = $user;
    do{ //覚醒者・夢語部ならコピー先を辿る
      if(! $target->IsRole('soul_mania', 'dummy_mania')) break;
      $stack = $target->GetPartner($target->main_role);
      if(is_null($stack)) break; //コピー先が見つからなければスキップ

      $target = $this->ByID($stack[0]);
      if($target->IsRoleGroup('mania')) $target = $user; //神話マニア系なら元に戻す
    }while(false);

    while($target->IsRole('unknown_mania')){ //鵺ならコピー先を辿る
      $stack = $target->GetPartner('unknown_mania');
      if(is_null($stack)) break; //コピー先が見つからなければスキップ

      $target = $this->ByID($stack[0]);
      if($target->IsSelf()) break; //自分に戻ったらスキップ
    }
    $user->$type = $target->DistinguishCamp();
  }

  //特殊イベント情報を設定する
  function SetEvent($force = false){
    global $ROOM;

    if($ROOM->id < 1) return; //入村時対応
    $event_rows = $ROOM->GetEvent($force);
    if(! is_array($event_rows)) return;
    foreach($event_rows as $event){
      switch($event['type']){
      case 'VOTE_KILLED':
	$user = $this->ByHandleName($event['message']);
	if(! $user->IsRole('mirror_fairy')) break;
	if(is_null($status_stack = $user->GetPartner('mirror_fairy'))) break;
	$duel_stack = array(); //決闘対象者の ID リスト
	foreach($status_stack as $key => $value){ //生存確認
	  if($this->IsVirtualLive($key))   $duel_stack[] = $key;
	  if($this->IsVirtualLive($value)) $duel_stack[] = $value;
	}
	if(count($duel_stack) > 1) $ROOM->event->vote_duel = $duel_stack;
	break;

      case 'WOLF_KILLED':
	$user = $this->ByHandleName($event['message']);
	if(is_null($status_stack = $user->GetPartner('bad_status'))) break;
	foreach($status_stack as $id => $date){
	  if($date != $ROOM->date) continue;
	  $status_user = $this->ByID($id);
	  $ROOM->event->invisible |= $status_user->IsRole('sun_fairy')   && $ROOM->IsDay();
	  $ROOM->event->earplug   |= $status_user->IsRole('moon_fairy')  && $ROOM->IsDay();
	  $ROOM->event->grassy    |= $status_user->IsRole('grass_fairy') && $ROOM->IsDay();
	  $ROOM->event->blinder   |= $status_user->IsRole('dark_fairy')  && $ROOM->IsDay();
	  $ROOM->event->mind_open |= $status_user->IsRole('light_fairy');
	}
	break;
      }
    }

    $stack = array('invisible', 'earplug', 'grassy', 'blinder', 'mind_open');
    foreach($stack as $role){
      if($ROOM->IsEvent($role)){
	foreach($this->rows as $user) $user->AddVirtualRole($role);
      }
    }
  }

  //役職の出現判定関数 (現在は不使用)
  function IsAppear($role){
    $role_list = func_get_args();
    foreach($this->rows as $user){
      if($user->IsRole($role_list)) return true;
    }
    return false;
  }

  //霊界の配役公開判定
  function IsOpenCast(){
    foreach($this->rows as $user){
      if($user->IsDummyBoy()) continue;
      if($user->IsReviveGroup(true) || $user->IsRole('soul_mania', 'dummy_mania')){
	if($user->IsLive()) return false;
      }
      elseif($user->IsRole('revive_priest')){
	if($user->IsActive()) return false;
      }
      elseif($user->IsRole('evoke_scanner')){
	if($user->IsLive() && ! $user->IsRoleGroup('copied')) return false;
      }
    }
    return true;
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

  //生存者を取得する
  function GetLivingUsers($strict = false){
    $stack = array();
    foreach($this->rows as $user){
      if($user->IsLive($strict)) $stack[] = $user->uname;
    }
    return $stack;
  }

  //生存している狼を取得する
  function GetLivingWolves(){
    $stack = array();
    foreach($this->rows as $user){
      if($user->IsLive() && $user->IsWolf()) $stack[] = $user->uname;
    }
    return $stack;
  }

  //死亡処理
  function Kill($user_no, $reason){
    global $ROOM;

    $user = $this->ByReal($user_no);
    if(! $user->ToDead()) return false;

    $virtual_user = $this->ByVirtual($user->user_no);
    $ROOM->SystemMessage($virtual_user->handle_name, $reason);

    switch($reason){
    case 'NOVOTED_day':
    case 'NOVOTED_night':
    case 'POSSESSED_TARGETED':
      return true;

    default: //遺言処理
      $user->SaveLastWords($virtual_user->handle_name);
      if($user != $virtual_user) $virtual_user->SaveLastWords();
      return true;
    }
  }

  //突然死処理
  function SuddenDeath($user_no, $reason){
    global $MESSAGE, $ROOM;

    $user = $this->ByReal($user_no);
    if(! $this->Kill($user_no, $reason)) return false;
    $user->suicide_flag = true;

    $sentence = strpos($reason, 'NOVOTED') !== false ? 'sudden_death' : 'vote_sudden_death';
    $ROOM->Talk($this->GetHandleName($user->uname, true) . ' ' . $MESSAGE->$sentence);
    return true;
  }

  //保存処理 (実用されていません)
  function Save(){
    foreach($this->rows as $user) $user->Save();
  }

  //現在のリクエスト情報に基づいて新しいユーザーをデータベースに登録します。
  //この関数は実用されていません
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
