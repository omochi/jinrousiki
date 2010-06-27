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

  //���ꤷ���桼�����ǡ����Υ��åȤ�̾���Ĥ�����ˤ����֤��ޤ���
  //���Υ᥽�åɤ� extract �ؿ�����Ѥ��ƥ��֥������ȤΥץ�ѥƥ���
  //��®�˥������Ÿ�����뤿��˻��ѤǤ��ޤ��� (���ߤ�̤����)
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

  //�򿦾����Ÿ������
  function ParseRoles($role = NULL){
    //���������
    if(isset($role)) $this->role = $role;
    $this->partner_list = array();

    //Ÿ���Ѥ�����ɽ���򥻥å�
    $regex_partner = '/([^\[]+)\[([^\]]+)\]/'; //���ͷ� (role[id])
    $regex_status  = '/([^-]+)-(.+)/';         //��ϵ�� (role[date-id])

    //Ÿ������
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

    //��������
    $this->role_list = array_unique($role_list);
    $this->main_role = $this->role_list[0];
  }

  //���ߤ��򿦤����
  function GetRole(){
    return $this->updated['role'] ? $this->updated['role'] : $this->role;
  }

  //���ߤν�°�رĤ����
  function GetCamp($strict = false){
    global $USERS;

    $type = $strict ? 'win_camp' : 'main_camp';
    if(is_null($this->$type)) $USERS->SetCamp($this, $type);
    return $this->$type;
  }

  //��ĥ��������
  function GetPartner($type){
    $list = $this->partner_list[$type];
    return is_array($list) ? $list : NULL;
  }

  //�����˱����������� ID �����
  function GetPossessedTarget($type, $today){
    if(is_null($target_list = $this->GetPartner($type))) return false;

    $date_list = array_keys($target_list);
    krsort($date_list);
    foreach($date_list as $date){
      if($date <= $today) return $target_list[$date];
    }
    return false;
  }

  //���ߤβ���Ū���������
  function IsDeadFlag($strict = false){
    if(! $strict) return NULL;
    if($this->suicide_flag) return true;
    if($this->revive_flag)  return false;
    if($this->dead_flag)    return true;
    return NULL;
  }

  //��¸�ե饰Ƚ��
  function IsLive($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return is_null($dead) ? ($this->live == 'live') : ! $dead;
  }

  //��˴�ե饰Ƚ��
  function IsDead($strict = false){
    $dead = $this->IsDeadFlag($strict);
    return is_null($dead) ? ($this->live == 'dead' || $this->IsDrop()) : $dead;
  }

  //��������ե饰Ƚ��
  function IsDrop(){
    return $this->live == 'drop';
  }

  //Ʊ��桼��Ƚ��
  function IsSame($uname){
    return $this->uname == $uname;
  }

  //��ʬ��Ʊ��桼��Ƚ��
  function IsSelf(){
    global $SELF;
    return $this->IsSame($SELF->uname);
  }

  //�����귯Ƚ��
  function IsDummyBoy($strict = false){
    global $ROOM;

    if($strict && $ROOM->IsQuiz()) return false;
    return $this->IsSame('dummy_boy');
  }

  //��Ƚ��
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

  //���������פ���Ƚ��
  function IsActive($role = NULL){
    $is_role = is_null($role) ? true : $this->IsRole($role);
    return ($is_role && ! $this->IsRole('lost_ability') && ! $this->lost_flag);
  }

  //�򿦥��롼��Ƚ��
  function IsRoleGroup($role){
    $role_list = func_get_args();
    foreach($role_list as $target_role){
      foreach($this->role_list as $this_role){
	if(strpos($this_role, $target_role) !== false) return true;
      }
    }
    return false;
  }

  //��Ω����Ƚ��
  function IsLonely(){
    return $is_role && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }

  //��ͭ�Է�Ƚ��
  function IsCommon($talk = false){
    if(! $this->IsRoleGroup('common')) return false;
    return $talk ? ! $this->IsRole('dummy_common') : true;
  }

  //�峤�ͷ���Ƚ�� (�ͷ������ϴޤޤʤ�)
  function IsDoll(){
    return $this->IsRoleGroup('doll') && ! $this->IsRole('doll_master');
  }

  //��ϵ��Ƚ��
  function IsWolf($talk = false){
    if(! $this->IsRoleGroup('wolf')) return false;
    return $talk ? ! $this->IsLonely() : true;
  }

  //�Ÿѷ�Ƚ��
  function IsFox($talk = false){
    if(! $this->IsRoleGroup('fox')) return false;
    return $talk ? ! ($this->IsChildFox() || $this->IsLonely()) : true;
  }

  //�Ҹѷ�Ƚ��
  function IsChildFox(){
    return $this->IsRole('child_fox', 'sex_fox');
  }

  //����Ƚ��
  function IsLovers(){
    return $this->IsRole('lovers');
  }

  //��ĥȽ��
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

  //��ǽ�Ϥ�ȯưȽ��
  function IsPoison(){
    global $ROOM;

    if(! $this->IsRoleGroup('poison') || $this->IsRole('chain_poison')) return false; //̵�ǡ�Ϣ�Ǽ�
    if($this->IsRole('dummy_poison')) return $ROOM->IsDay(); //̴�Ǽ�
    if($this->IsRole('poison_guard')) return $ROOM->IsNight(); //����
    if($this->IsRole('incubate_poison')) return $ROOM->date >= 5; //���ǼԤ� 5 ���ܰʹ�
    return true;
  }

  //���ǽ�ϼ�Ƚ�� (����ͼԤȥ����ɾ�Ƕ��̤��뤿��δؿ�)
  function IsPossessedGroup(){
    return $this->IsRole('possessed_wolf', 'possessed_mad', 'possessed_fox');
  }

  //��°�ر�Ƚ��
  function DistinguishCamp(){
    if($this->IsWolf() || $this->IsRoleGroup('mad')) return 'wolf';
    if($this->IsFox()) return 'fox';
    if($this->IsRole('quiz')) return 'quiz';
    if($this->IsRoleGroup('chiroptera', 'fairy')) return 'chiroptera';
    if($this->IsRoleGroup('cupid', 'angel')) return 'lovers';
    return 'human';
  }

  //�ꤤ�դ�Ƚ��
  function DistinguishMage($reverse = false){
    if($this->IsRole('boss_chiroptera')) return 'chiroptera'; //������������Ƚ��

    //��ϵ�ʳ���ϵ�����ѡ��Կ��ԤϿ�ϵȽ��
    $result = (($this->IsWolf() && ! $this->IsRole('boss_wolf')) ||
	       $this->IsRole('black_fox', 'suspect'));
    if($reverse) $result = (! $result);
    return $result ? 'wolf' : 'human';
  }

  //�Ҥ褳����Τ�Ƚ��
  function DistinguishSex(){
    return $this->IsRoleGroup('chiroptera', 'fairy', 'gold') ? 'chiroptera' : 'sex_' . $this->sex;
  }

  //̤��ɼ�����å�
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
    if($this->IsRole('jammer_mad')){
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
    if($this->IsChildFox()){
      return isset($vote_data['CHILD_FOX_DO'][$this->uname]);
    }
    if($this->IsRoleGroup('fairy') && ! $this->IsRole('mirror_fairy')){
      return isset($vote_data['FAIRY_DO'][$this->uname]);
    }

    if($ROOM->date == 1){ //��������
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

    //�����ܰʹ�
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

    if($ROOM->IsOpenCast()) return true;
    if($this->IsRoleGroup('cat') || $this->IsActive('revive_fox')){
      if(is_array($vote_data['POISON_CAT_NOT_DO']) &&
	 array_key_exists($this->uname, $vote_data['POISON_CAT_NOT_DO'])) return true;
      return isset($vote_data['POISON_CAT_DO'][$this->uname]);
    }
    return true;
  }

  //�򿦤�ѡ������ƾ�ά̾���֤�
  function GenerateShortRoleName(){
    global $GAME_CONF, $USERS;

    //�ᥤ���򿦤����
    $camp = $this->GetCamp();
    $name = $GAME_CONF->short_role_list[$this->main_role];
    $str = '<span class="add-role"> [';
    $str .= $camp == 'human' ? $name : '<span class="' . $camp . '">' . $name . '</span>';

    //�����򿦤��ɲ�
    $sub_role_list = array_slice($this->role_list, 1);
    foreach($GAME_CONF->short_role_list as $role => $name){
      if(in_array($role, $sub_role_list)){
	$str .= $role == 'lovers' ? '<span class="lovers">' . $name . '</span>' : $name;
      }
    }

    return $str . '] (' . $USERS->TraceExchange($this->user_no)->uname . ')</span>';
  }

  //���� DB ��������
  function Update($item, $value){
    global $ROOM;

    if($ROOM->test_mode){
      PrintData($value, 'Change [' . $item . '] (' . $this->uname . ')');
      return;
    }
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    return SendQuery("UPDATE user_entry SET {$item} = '{$value}' {$query}", true);
  }

  //��� DB �������� (���δؿ��Ϥޤ����Ѥ���Ƥ��ޤ���)
  function Save(){
    if(empty($this->updated)) return false;
    foreach($this->updated as $item){
      $update_list[] = "$item = '{$this->item}'";
    }
    $update = implode(', ', $update_list);
    $query = "WHERE room_no = {$this->room_no} AND uname = '{$this->uname}' AND user_no > 0";
    SendQuery("UPDATE user_entry SET {$update} {$query}", true);
  }

  //�𴴻�˴����
  function ToDead(){
    if($this->IsDead(true)) return false;
    $this->Update('live', 'dead');
    $this->dead_flag = true;
    return true;
  }

  //��������
  function Revive($virtual = false){
    global $ROOM;

    if($this->IsLive(true)) return false;
    $this->Update('live', 'live');
    $this->revive_flag = true;
    if(! $virtual) $ROOM->SystemMessage($this->handle_name, 'REVIVE_SUCCESS');
    return true;
  }

  //�򿦹�������
  function ChangeRole($role){
    $this->Update('role', $role);
    $this->updated['role'] = $role; //����å������Τι����ϹԤ�ʤ�
  }

  //���ɲý���
  function AddRole($role){
    $base_role = $this->GetRole();
    if(in_array($role, explode(' ', $base_role))) return false; //Ʊ���򿦤��ɲä��ʤ�
    $this->ChangeRole($base_role . ' ' . $role);
  }

  //�������ɲý��� (����å������)
  function AddVirtualRole($role){
    if(! in_array($role, $this->role_list)) $this->role_list[] = $role;
  }

  //���ִ�����
  function ReplaceRole($target, $replace){
    $this->ChangeRole(str_replace($target, $replace, $this->GetRole()));
  }

  /*
    ���Υ᥽�åɤ϶�ɱ�������Τ����ͽ�󤵤�Ƥ��ޤ���
     ���ڡ���������³���Ƥ���ս�϶����򿦤�ǧ������뤪���줬����ޤ���
     �����ParseRole¦��preg_split()�ʤɤ���Ѥ���٤��Ǥ������򿦤�����������������ʤ����ᡢ
     ���¦��Ĵ�᤹���ΤȤ��ޤ���(2009-07-05 enogu)
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

  //��������������¸����
  function SaveLastWords($handle_name = NULL){
    global $ROOM;

    if(! $this->IsDummyBoy() &&
       $this->IsRole('reporter', 'evoke_scanner', 'no_last_words', 'possessed_exchange')) return;
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

  //��ɼ����
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

  function LoadRoom($request){
    if($request->IsVirtualRoom()){
      $user_list = $request->TestItems->test_users;
      if(is_int($user_list)) $user_list = $this->RetriveByUserCount($user_list);
    }
    elseif($request->entry_user){
      $user_list = $this->RetriveByEntryUser($request->room_no);
    }
    else{
      $user_list = $this->RetriveByRoom($request->room_no);
    }
    $this->LoadUsers($user_list);
  }

  //�����¼�Υ桼��������������
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

  //���ꤷ���Ϳ�ʬ�Υ桼���������¼���������˼�������
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
    return FetchObject($query, 'User');
  }

  //��¼�����ѤΥ桼���ǡ������������
  function RetriveByEntryUser($room_no){
    $query = "SELECT room_no, user_no, uname, handle_name, live, ip_address
      FROM user_entry WHERE room_no = {$room_no} ORDER BY user_no";
    return FetchObject($query, 'User');
  }

  //���������桼������� User ���饹�ǥѡ���������Ͽ����
  function LoadUsers($user_list){
    if(! is_array($user_list)) return false;

    //���������
    $this->rows = array();
    $this->kicked = array();
    $this->names = array();
    $kicked_user_no = 0;

    foreach($user_list as $user){
      $user->ParseCompoundParameters();
      if($user->user_no >= 0 && $user->live != 'kick'){
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
    return $user_no > 0 ? $this->rows[$user_no] : $this->kicked[$user_no];
  }

  function ByUname($uname){
    return $this->ByID($this->UnameToNumber($uname));
  }

  function ByHandleName($handle_name){
    return $this->ByUname($this->HandleNameToUname($handle_name));
  }

  function BySession(){
    global $SESSION;
    return $this->TraceExchange($SESSION->GetUser());
  }

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

  function TraceExchange($user_no){
    global $ROOM;

    $user = $this->ByID($user_no);
    $type = 'possessed_exchange';
    if(! $user->IsRole($type) || ! $ROOM->IsPlaying() ||
       (! $ROOM->log_mode && $user->IsDead())) return $user;

    $stack = $user->GetPartner($type);
    return is_array($stack) && $ROOM->date > 2 ? $this->ByID(array_shift($stack)) : $user;
  }

  function ByVirtual($user_no){
    return $this->TraceVirtual($user_no, 'possessed_target');
  }

  function ByReal($user_no){
    return $this->TraceVirtual($user_no, 'possessed');
  }

  function ByVirtualUname($uname){
    return $this->ByVirtual($this->UnameToNumber($uname));
  }

  function ByRealUname($uname){
    return $this->ByReal($this->UnameToNumber($uname));
  }

  function GetHandleName($uname, $virtual = false){
    $user = $virtual ? $this->ByVirtualUname($uname) : $this->ByUname($uname);
    return $user->handle_name;
  }

  function GetRole($uname){
    return $this->ByUname($uname)->role;
  }

  function GetUserCount($strict = false){
    if(! $strict) return count($this->rows);
    $count = 0;
    foreach($this->rows as $user){
      if($user->user_no > 0) $count++;
    }
    return $count;
  }

  //��°�رĤ�Ƚ�ꤷ�ƥ���å��夹��
  function SetCamp($user, $type){
    if($type == 'win_camp' && $user->IsLovers()){
      $user->$type = 'lovers';
      return;
    }

    $target_user = $user;
    do{ //���üԡ�̴�����ʤ饳�ԡ����é��
      if(! $target_user->IsRole('soul_mania', 'dummy_mania')) break;
      $target_list = $target_user->GetPartner($target_user->main_role);
      if(is_null($target_list)) break; //���ԡ��褬���Ĥ���ʤ���Х����å�

      $target_user = $this->ByID($target_list[0]);
      if($target_user->IsRoleGroup('mania')) $target_user = $user; //���åޥ˥��Ϥʤ鸵���᤹
    }while(false);

    while($target_user->IsRole('unknown_mania')){ //�ʤ饳�ԡ����é��
      $target_list = $target_user->GetPartner('unknown_mania');
      if(is_null($target_list)) break; //���ԡ��褬���Ĥ���ʤ���Х����å�

      $target_user = $this->ByID($target_list[0]);
      if($target_user->IsSelf()) break; //��ʬ����ä��饹���å�
    }
    $user->$type = $target_user->DistinguishCamp();
  }

  //�ü쥤�٥�Ⱦ�������ꤹ��
  function SetEvent($force = false){
    global $ROOM;

    if($ROOM->room_no < 1) return;
    $event_rows = $ROOM->GetEvent($force);
    if(! is_array($event_rows)) return;
    foreach($event_rows as $event){
      switch($event['type']){
      case 'VOTE_KILLED':
	$user = $this->ByHandleName($event['message']);
	if(! $user->IsRole('mirror_fairy')) break;
	if(is_null($status_stack = $user->GetPartner('mirror_fairy'))) break;
	$duel_stack = array(); //��Ʈ�оݼԤ� ID �ꥹ��
	foreach($status_stack as $key => $value){ //��¸��ǧ
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
	  $ROOM->event->blind_day    |= $status_user->IsRole('dark_fairy') && $ROOM->IsDay();
	  $ROOM->event->bright_night |= $status_user->IsRole('light_fairy');
	}
	break;
      }
    }

    if($ROOM->IsEvent('blind_day')){
      foreach($this->rows as $user) $user->AddVirtualRole('blinder');
    }
    if($ROOM->IsEvent('bright_night')){
      foreach($this->rows as $user) $user->AddVirtualRole('mind_open');
    }
  }

  //�򿦤νи�Ƚ��ؿ�
  function IsAppear($role){
    $role_list = func_get_args();
    foreach($this->rows as $user){
      if($user->IsRole($role_list)) return true;
    }
    return false;
  }

  //����������Ƚ��
  function IsOpenCast(){
    foreach($this->rows as $user){
      if($user->IsDummyBoy()) continue;
      if($user->IsRoleGroup('cat')){
	if($user->IsLive()) return false;
      }
      elseif($user->IsRole('revive_fox')){
	if($user->IsLive() && $user->IsActive()) return false;
      }
      elseif($user->IsRole('revive_priest')){
	if($user->IsActive()) return false;
      }
      elseif($user->IsRole('evoke_scanner')){
	if($user->IsLive() && ! $user->IsRole('copied')) return false;
      }
    }
    return true;
  }

  //����Ū��������֤�
  function IsVirtualLive($user_no, $strict = false){
    //��ͤ���Ƥ��������ͼԤ�������֤�
    $real_user = $this->ByReal($user_no);
    if($real_user->user_no != $user_no) return $real_user->IsLive($strict);

    //�����˰�ư���Ƥ�����Ͼ�˻�˴����
    if($this->ByVirtual($user_no)->user_no != $user_no) return false;

    //��ͤ�̵������ܿͤ�������֤�
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

  //��˴����
  function Kill($user_no, $reason = NULL){
    global $ROOM;

    $user = $this->ByReal($user_no);
    if(! $user->ToDead()) return false;

    if($reason){
      $virtual_user = $this->ByVirtual($user->user_no);
      $ROOM->SystemMessage($virtual_user->handle_name, $reason);

      //�������
      if($reason == 'POSSESSED_TARGETED') return true;
      $user->SaveLastWords($virtual_user->handle_name);
      if($user != $virtual_user) $virtual_user->SaveLastWords();
    }
    return true;
  }

  //���������
  function SuddenDeath($user_no, $reason = NULL){
    global $MESSAGE, $ROOM;

    $user = $this->ByReal($user_no);
    if(! $this->Kill($user_no, $reason)) return false;
    $user->suicide_flag = true;

    $sentence = $reason ? 'vote_sudden_death' : 'sudden_death';
    $ROOM->Talk($this->GetHandleName($user->uname, true) . $MESSAGE->$sentence);
    return true;
  }

  function Save(){
    foreach($this->rows as $user) $user->Save();
  }

  //���ߤΥꥯ�����Ⱦ���˴�Ť��ƿ������桼������ǡ����١�������Ͽ���ޤ���
  //���δؿ��ϼ��Ѥ���Ƥ��ޤ���
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

  //�桼�����������ꤷ�ƿ������桼������ǡ����١�������Ͽ���ޤ���(�ɥ�եȡ����ε�ǽ�ϥƥ��Ȥ���Ƥ��ޤ���)
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
