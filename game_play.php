<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//���å���󳫻�
session_start();
$session_id = session_id();

EncodePostData(); //�ݥ��Ȥ��줿ʸ��������ƥ��󥳡��ɤ���

//���������
$RQ_ARGS = new RequestGamePlay();
$room_no = $RQ_ARGS->room_no; //���� No
if($RQ_ARGS->play_sound){//���Ǥ��Τ餻
  $SOUND = new Sound(); //������������
  $cookie_day_night  = $_COOKIE['day_night'];       //�������򲻤Ǥ��餻�뤿��
  $cookie_vote_times = (int)$_COOKIE['vote_times']; //����ɼ�򲻤��Τ餻�뤿��
  $cookie_objection  = $_COOKIE['objection'];       //�ְ۵Ĥ���פ򲻤��Τ餻�뤿��
}

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

$ROOM = new RoomDataSet($room_no); //¼��������
$ROOM->view_mode    = $RQ_ARGS->view_mode; //����⡼��
$ROOM->dead_mode    = $RQ_ARGS->dead_mode; //��˴�ԥ⡼��
$ROOM->heaven_mode  = $RQ_ARGS->heaven_mode; //���å⡼��
$ROOM->system_time  = TZTime(); //���߻�������
$ROOM->sudden_death = 0; //������¹ԤޤǤλĤ����

$USERS = new UserDataSet($room_no); //�桼����������
$SELF = $USERS->ByUname($uname); //��ʬ�ξ�������
$ROLE_IMG = new RoleImage();

//ɬ�פʥ��å����򥻥åȤ���
$objection_array = array(); //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���ξ���
$objection_left_count = 0;  //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���λĤ���
SendCookie();

//ȯ����̵ͭ������å�
ConvertSay(&$RQ_ARGS->say); //ȯ���ִ�����

if($RQ_ARGS->say != '' && $RQ_ARGS->is_last_words() && $SELF->is_live() && ! $SELF->is_dummy_boy()){
  EntryLastWords($RQ_ARGS->say);  //�����Ƥ���а����Ͽ
}
elseif($RQ_ARGS->say != '' && ($ROOM->day_night == $SELF->last_load_day_night ||
			       $SELF->is_dead() || $SELF->is_dummy_boy())){
  Say($RQ_ARGS->say); //���Ǥ��뤫���Ǹ�˥���ɤ������ȥ����󤬰��פ��Ƥ��뤫�����귯�ʤ�񤭹���
}
else{
  CheckSilence(); //���������ڤΥ����å�(���ۡ�������)
}

//�Ǹ�˥���ɤ������Υ�����򹹿�
mysql_query("UPDATE user_entry SET last_load_day_night = '{$ROOM->day_night}'
		WHERE room_no = $room_no AND uname = '{$SELF->uname}' AND user_no > 0");
mysql_query('COMMIT');

OutputGamePageHeader(); //HTML�إå�
OutputGameHeader(); //�����Υ����ȥ�ʤ�

if(! $ROOM->heaven_mode){
  if(! $RQ_ARGS->list_down) OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
  OutputAbility(); //��ʬ����������
  if($ROOM->is_day() && $SELF->is_live()) CheckSelfVoteDay(); //�����ɼ�Ѥߥ����å�
  OutputRevoteList(); //����ɼ�λ�����å�������ɽ������
}

//���å������
if($SELF->is_dead() && $ROOM->heaven_mode)
  OutputHeavenTalkLog();
else
  OutputTalkLog();

if(! $ROOM->heaven_mode){
  if($SELF->is_dead()) OutputAbilityAction(); //ǽ��ȯ��
  OutputLastWords(); //���
  OutputDeadMan();   //��˴��
  OutputVoteList();  //��ɼ���
  if(! $ROOM->dead_mode) OutputSelfLastWords(); //��ʬ�ΰ��
  if($RQ_ARGS->list_down) OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
}
OutputHTMLFooter();

DisconnectDatabase($dbHandle); //DB ��³���

//-- �ؿ� --//
//ɬ�פʥ��å�����ޤȤ����Ͽ(�Ĥ��Ǥ˺ǿ��ΰ۵Ĥ���ξ��֤������������˳�Ǽ)
function SendCookie(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $SELF, $objection_array, $objection_left_count;

  //<�������򲻤Ǥ��Τ餻��>
  //���å����˳�Ǽ (�������˲��Ǥ��Τ餻�ǻȤ���ͭ�����°����)
  setcookie('day_night', $ROOM->day_night, $ROOM->system_time + 3600);

  //-- �ְ۵ġפ���򲻤Ǥ��Τ餻�� --//
  //���ޤǤ˼�ʬ���ְ۵ġפ���򤷤���������
  $query = "SELECT COUNT(message) FROM system_message WHERE room_no = $room_no " .
    "AND type = 'OBJECTION' AND message = '{$SELF->user_no}'";
  $self_objection_count = FetchResult($query);

  //�����Ƥ���(�����ཪλ��ϻ�ԤǤ�OK)�ְ۵ġפ��ꡢ�Υ��å��׵᤬����Х��åȤ���(����������ξ��)
  if($SELF->is_live() && ! $ROOM->is_night() && $RQ_ARGS->set_objection &&
     $self_objection_count < $GAME_CONF->objection){
    InsertSystemMessage($SELF->user_no, 'OBJECTION');
    InsertSystemTalk('OBJECTION', $ROOM->system_time, '', '', $SELF->uname);
    mysql_query('COMMIT');
  }

  //�桼�������������ƿͿ�ʬ�Ρְ۵Ĥ���פΥ��å������ۤ���
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0";
  $user_count = FetchResult($query);
  // //�����ꥻ�å� (0 ���ܤ��Ѥ��ͤ�����ʤ������ݾڤ���Ƥ�������פ��ʡ�)
  // $objection_array = array();
  // unset($objection_array[0]);
  $objection_array = array_fill(1, $user_count, 0); //index �� 1 ����

  //message:�۵Ĥ���򤷤��桼�� No �Ȥ��β�������
  $sql = mysql_query("SELECT message, COUNT(message) AS message_count FROM system_message
			WHERE room_no = $room_no AND type = 'OBJECTION' GROUP BY message");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $this_user_no = (int)$array['message'];
    $this_count   = (int)$array['message_count'];
    $objection_array[$this_user_no] = $this_count;
  }

  //���å����˳�Ǽ (ͭ�����°����)
  foreach($objection_array as $value){
    if($str != '') $str .= ','; //����޶��ڤ�
    $str .= $value;
  }
  setcookie('objection', $str, $ROOM->system_time + 3600);

  //�Ĥ�۵Ĥ���β��
  $objection_left_count = $GAME_CONF->objection - $objection_array[$SELF->user_no];

  //<����ɼ�򲻤Ǥ��Τ餻��>
  //����ɼ�β�������
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = {$ROOM->date} AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) != 0){ //���å����˳�Ǽ (ͭ�����°����)
    $last_vote_times = (int)mysql_result($sql, 0, 0); //�����ܤκ���ɼ�ʤΤ�����
    setcookie('vote_times', $last_vote_times, $ROOM->system_time + 3600);
  }
  else{ //���å��������� (ͭ�����°����)
    setcookie('vote_times', '', $ROOM->system_time - 3600);
  }
}

//ȯ���ִ�����
function ConvertSay(&$say){
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $SELF;

  //����ɻ�����ԡ�������ץ쥤��ʳ��ʤ���������å�
  if($say == '' || $SELF->is_dead() || ! $ROOM->is_playing()) return false;

  //˨ϵ���Կ��Ԥϰ����Ψ��ȯ�������ʤ�(�ǥե���Ȼ�)�ˤʤ�
  if(($SELF->is_role('cute_wolf') || $SELF->is_role('suspect')) &&
     mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate){
    $say = ($MESSAGE->cute_wolf != '' ? $MESSAGE->cute_wolf : $MESSAGE->wolf_howl);
  }
  //�»Ρ��ʽ��ϰ����Ψ��ȯ���������ؤ��
  elseif(($SELF->is_role('gentleman') || $SELF->is_role('lady')) &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){
    $role_name = ($SELF->is_role('gentleman') ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $query = "SELECT handle_name FROM user_entry WHERE room_no = $room_no " .
      "AND uname <> '{$SELF->uname}' AND live = 'live' AND user_no > 0";
    $target_list = FetchArray($query);
    $rand_key    = array_rand($target_list);
    $say = $MESSAGE->$message_header . $target_list[$rand_key] . $MESSAGE->$message_footer;
  }
  //ϵ��ǯ�ϰ����Ψ��ȯ�����Ƥ�ȿž�����
  elseif($SELF->is_role('liar') && mt_rand(1, 100) <= $GAME_CONF->liar_rate){
    $say = strtr($say, $GAME_CONF->liar_replace_list);
  }

  if($SELF->is_role('invisible')){ //�����º̤ν���
    $invisible_say = '';
    $count = mb_strlen($say);
    $rate = $GAME_CONF->invisible_rate;
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n" || $this_str == "\t" || $this_str == ' ' || $this_str == '��'){
	$invisible_say .= $this_str;
	continue;
      }
      if(mt_rand(1, 100) <= $rate)
	$invisible_say .= (strlen($this_str) == 2 ? '��' : '&nbsp;');
      else
	$invisible_say .= $this_str;
      if($rate++ > 100) break;
    }
    $say = $invisible_say;
  }

  if($SELF->is_role('rainbow')){ //�����º̤ν���
    $say = strtr($say, $GAME_CONF->rainbow_replace_list);
  }

  if($SELF->is_role('silent')){ //̵���ν���
    if(mb_strlen($say) > $GAME_CONF->silent_length){
      $say = mb_substr($say, 0, $GAME_CONF->silent_length) . '�ġ�';
    }
  }
}

//�����Ͽ
function EntryLastWords($say){
  global $room_no, $ROOM, $SELF;

  //�����ཪλ�塢��ԡ��֥󲰡�ɮ�����ʤ���Ͽ���ʤ�
  if($ROOM->is_finished() || $SELF->is_dead() || $SELF->is_role('reporter') ||
     $SELF->is_role('no_last_words')) return false;

  //�����Ĥ�
  mysql_query("UPDATE user_entry SET last_words = '$say' WHERE room_no = $room_no
		AND uname = '{$SELF->uname}' AND user_no > 0");
  mysql_query('COMMIT'); //������ߥå�
}

//ȯ��
function Say($say){
  global $RQ_ARGS, $room_no, $ROOM, $SELF;

  if($ROOM->is_real_time()){ //�ꥢ�륿������
    GetRealPassTime(&$left_time);
    $spend_time = 0; //���äǻ��ַв���������̵���ˤ���
  }
  else{ //���äǻ��ַв���
    GetTalkPassTime(&$left_time); //�в���֤���
    if(strlen($say) <= 100) //�в����
      $spend_time = 1;
    elseif(strlen($say) <= 200)
      $spend_time = 2;
    elseif(strlen($say) <= 300)
      $spend_time = 3;
    else
      $spend_time = 4;
  }

  if(! $ROOM->is_playing()){ //�����೫������Ϥ��Τޤ�ȯ��
    Write($say, $ROOM->day_night, 0, true);
  }
  //�����귯 (���� GM �б�) �ϰ�������ѤΥ����ƥ��å��������ڤ��ؤ�
  elseif($SELF->is_dummy_boy() && ($RQ_ARGS->is_last_words() || ($SELF->is_live() && $left_time == 0))){
    Write($say, "{$ROOM->day_night} dummy_boy", 0); //ȯ�����֤򹹿����ʤ�
  }
  elseif($SELF->is_dead()){ //��˴�Ԥ�����
    Write($say, 'heaven', 0); //ȯ�����֤򹹿����ʤ�
  }
  elseif($SELF->is_live() && $left_time > 0){ //��¸�Ԥ����»�����
    if($ROOM->is_day()){ //��Ϥ��Τޤ�ȯ��
      Write($say, 'day', $spend_time, true);
    }
    elseif($ROOM->is_night()){ //��������ʬ����
      if($SELF->is_wolf()) //��ϵ
	Write($say, 'night wolf', $spend_time, true);
      elseif($SELF->is_role('whisper_mad')) //�񤭶���
	Write($say, 'night mad', 0);
      elseif($SELF->is_role('common')) //��ͭ��
	Write($say, 'night common', 0);
      elseif($SELF->is_fox()) //�Ÿ�
	Write($say, 'night fox', 0);
      else //�Ȥ��
	Write($say, 'night self_talk', 0);
    }
  }
}

//ȯ���� DB ����Ͽ����
function Write($say, $location, $spend_time, $update = false){
  global $MESSAGE, $RQ_ARGS, $room_no, $ROOM, $SELF;

  //�����礭�������
  $voice = $RQ_ARGS->font_type;
  if($SELF->is_live() && $ROOM->is_playing()){
    $voice_list = array('strong', 'normal', 'weak');
    if(    $SELF->is_role('strong_voice')) $voice = 'strong';
    elseif($SELF->is_role('normal_voice')) $voice = 'normal';
    elseif($SELF->is_role('weak_voice'))   $voice = 'weak';
    elseif($SELF->is_role('upper_voice')){
      $voice_key = array_search($RQ_ARGS->font_type, $voice_list);
      if($voice_key == 0) $say = $MESSAGE->howling;
      else $voice = $voice_list[$voice_key - 1];
    }
    elseif($SELF->is_role('downer_voice')){
      $voice_key = array_search($RQ_ARGS->font_type, $voice_list);
      if($voice_key >= count($voice_list) - 1) $say = $MESSAGE->common_talk;
      else $voice = $voice_list[$voice_key + 1];
    }
    elseif($SELF->is_role('random_voice')){
      $rand_key = array_rand($voice_list);
      $voice = $voice_list[$rand_key];
    }
  }

  InsertTalk($room_no, $ROOM->date, $location, $SELF->uname,
	     $ROOM->system_time, $say, $voice, $spend_time);
  if($update) UpdateTime();
  mysql_query('COMMIT'); //������ߥå�
}

//���������ڤΥ����å�
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $room_no, $ROOM, $USERS;

  //��������ʳ��Ͻ����򤷤ʤ�
  if(! $ROOM->is_playing()) return false;

  //�ơ��֥��å�
  if(! mysql_query("LOCK TABLES room WRITE, talk WRITE, vote WRITE,
			user_entry WRITE, system_message WRITE")){
    return false;
  }

  //�Ǹ��ȯ�����줿���֤����
  $last_updated_time = FetchResult("SELECT last_updated FROM room WHERE room_no = $room_no");
  $last_updated_pass_time = $ROOM->system_time - $last_updated_time;

  //�в���֤����
  if($ROOM->is_real_time()) //�ꥢ�륿������
    GetRealPassTime(&$left_time);
  else //���äǻ��ַв���
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //�ꥢ�륿�������Ǥʤ������»�������������ͤ�Ķ�����ʤ�ʤ����ֿʤ��(����)
  if(! $ROOM->is_real_time() && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '�������������������� ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      InsertTalk($room_no, $ROOM->date, "{$ROOM->day_night} system", 'system',
		 $ROOM->system_time, $sentence, NULL, $TIME_CONF->silence_pass);
      UpdateTime();
    }
  }
  elseif($left_time == 0){ //���»��֤�᤮�Ƥ�����ٹ��Ф�
    //������ȯư�ޤǤλ��֤����
    $left_time_str = ConvertTime($TIME_CONF->sudden_death); //ɽ���Ѥ��Ѵ�
    $sudden_death_announce = '����' . $left_time_str . '��' . $MESSAGE->sudden_death_announce;

    //���˷ٹ��Ф��Ƥ��뤫�����å�
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = $room_no " .
      "AND date = {$ROOM->date} AND location = '{$ROOM->day_night} system' " .
      "AND uname = 'system' AND sentence = '$sudden_death_announce'";
    if(FetchResult($query) == 0){ //�ٹ��Ф��Ƥ��ʤ��ä���Ф�
      InsertSystemTalk($sudden_death_announce, ++$ROOM->system_time); //�����äθ�˽Ф�褦��
      UpdateTime(); //�������֤򹹿�
      $last_updated_pass_time = 0;
    }
    $ROOM->sudden_death = $TIME_CONF->sudden_death - $last_updated_pass_time;

    //���»��֤�᤮�Ƥ�����̤��ɼ�οͤ������व����
    if($ROOM->sudden_death <= 0){
      //��¸�Ԥ�������뤿��δ��� SQL ʸ
      $query_live = "SELECT uname FROM user_entry WHERE room_no = $room_no " .
	"AND live = 'live' AND user_no > 0";

      //��ɼ�Ѥߤοͤ�������뤿��δ��� SQL ʸ
      $query_vote = "SELECT uname FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} AND ";

      if($ROOM->is_day()){
	//��ɼ��������
	$vote_times = GetVoteTimes();

	//��ɼ�ѤߤοͤΥ桼��̾�����
	$add_action = "situation = 'VOTE_KILL' AND vote_times = $vote_times";
	$vote_uname_list = FetchArray($query_vote . $add_action);

	//��ɼ��ɬ�פʿͤΥ桼��̾�����
	$live_uname_list = FetchArray($query_live);

	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);
      }
      elseif($ROOM->is_night()){
	//��ϵ����ɼ���ǧ
	$wolf_vote_count = FetchCount($query_vote . "situation = 'WOLF_EAT'");
	$wolf_list = ($wolf_vote_count == 0 ? GetLiveWolves() : array());

	//�о��򿦤Υǡ��������
	$action_list = array('MAGE_DO', 'CHILD_FOX_DO');
	$actor_list  = array('%mage', 'child_fox');

	if($ROOM->date == 1){
	  array_push($action_list, 'CUPID_DO', 'MANIA_DO');
	  array_push($actor_list, 'cupid', 'mania');
	}
	else{
	  array_push($action_list, 'GUARD_DO', 'REPORTER_DO', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO',
		     'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
	  array_push($actor_list, '%guard', 'reporter', 'assassin', 'trap_mad');
	  if(! $ROOM->is_open_cast()){
	    array_push($action_list, 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	    array_push($actor_list, 'poison_cat');
	  }
	}

	//��ɼ�ѤߤοͤΥ桼��̾�����
	foreach($action_list as $this_action){
	  if($add_action != '') $add_action .= ' OR ';
	  $add_action .= "situation = '$this_action'";
	}
	$vote_uname_list = FetchArray($query_vote . '(' . $add_action . ')');

	//��ɼ��ɬ�פʿͤΥ桼��̾�����
	foreach($actor_list as $this_actor){
	  if($add_actor != '') $add_actor .= ' OR ';
	  if($this_actor == 'trap_mad'){
	    $add_actor .= "(role LIKE '{$this_actor}%' AND !(role LIKE '%lost_ability%'))";
	  }
	  else{
	    $add_actor .= "role LIKE '{$this_actor}%'";
	  }
	}
	$live_uname_list = FetchArray("$query_live AND uname <> 'dummy_boy' AND ($add_actor)");

	//̤��ɼ�ο�ϵ�Υꥹ�Ȥ��ɲ�
	$novote_uname_list = array_merge($wolf_list, array_diff($live_uname_list, $vote_uname_list));
      }

      //̤��ɼ�Ԥ����������व����
      $flag_medium = CheckMedium(); //����νи������å�
      $dead_lovers_list = array(); //���͸��ɤ��оݼԥꥹ��
      foreach($novote_uname_list as $this_uname){
	SuddenDeath($this_uname, $flag_medium);
	$this_role = $USERS->GetRole($this_uname);
	if(strpos($this_role, 'lovers') !== false){ //���ͤʤ��ǤޤȤ�Ƹ��ɤ�������Ԥ�
	  array_push($dead_lovers_list, $this_role);
	}
      }
      foreach($dead_lovers_list as $this_role){ //���͸��ɤ�����
	LoversFollowed($this_role, $flag_medium, true);
      }
      InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //��ɼ�ꥻ�åȥ�å�����
      InsertSystemTalk($sudden_death_announce, ++$ROOM->system_time); //��������Υ�å�����
      UpdateTime(); //���»��֥ꥻ�å�
      DeleteVote(); //��ɼ�ꥻ�å�
      CheckVictory(); //���ԥ����å�
    }
  }
  mysql_query('UNLOCK TABLES'); //�ơ��֥��å����
}

//¼̾�������ϡ������ܡ����פޤǡ����֤����(���Ԥ��Ĥ�����¼��̾�������ϡ����Ԥ����)
function OutputGameHeader(){
  global $GAME_CONF, $TIME_CONF, $MESSAGE, $RQ_ARGS, $room_no, $ROOM, $SELF,
    $cookie_day_night, $cookie_objection, $objection_array, $objection_left_count;

  $room_message = '<td class="room"><span>' . $ROOM->name . '¼</span>����' . $ROOM->comment .
    '��[' . $room_no . '����]</td>'."\n";
  $url_room   = '?room_no=' . $room_no;
  $url_reload = ($RQ_ARGS->auto_reload > 0 ? '&auto_reload=' . $RQ_ARGS->auto_reload : '');
  $url_sound  = ($RQ_ARGS->play_sound ? '&play_sound=on'  : '');
  $url_list   = ($RQ_ARGS->list_down  ? '&list_down=on'   : '');
  $url_dead   = ($ROOM->dead_mode     ? '&dead_mode=on'   : '');
  $url_heaven = ($ROOM->heaven_mode   ? '&heaven_mode=on' : '');
  $real_time  = $ROOM->is_real_time();

  echo '<table class="game-header"><tr>'."\n";
  if(($SELF->is_dead() && $ROOM->heaven_mode) || $ROOM->is_aftergame()){ //��ȥ�������
    if($SELF->is_dead() && $ROOM->heaven_mode)
      echo '<td>&lt;&lt;&lt;ͩ��δ�&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //�������Υ��ؤΥ������
    echo '<td class="view-option">��';

    $url_header ='<a href="game_log.php' . $url_room . '&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '1' . $url_night . '1(��)</a>'."\n";
    for($i = 2; $i < $ROOM->date; $i++){
      echo $url_header . $i . $url_day   . $i . '(��)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(��)</a>'."\n";
    }
    if($ROOM->is_night() && $ROOM->heaven_mode){
      echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(��)</a>'."\n";
    }
    elseif($ROOM->is_aftergame()){
      $query = "SELECT COUNT(uname) FROM talk WHERE room_no = $room_no " .
	"AND date = {$ROOM->date} AND location = 'day'";
      if(FetchResult($query) > 0){
	echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(��)</a>'."\n";
      }
    }

    if($ROOM->heaven_mode){
      echo '</td>'."\n" . '</tr></table>'."\n";
      return;
    }
  }
  else{
    echo $room_message . '<td class="view-option">'."\n";
    if($SELF->is_dead() && $ROOM->dead_mode){ //��˴�Ԥξ��Ρ����������ɽ���Ͼ�⡼��
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="����">
</form>

EOF;
    }
  }

  if(! $ROOM->is_aftergame()){ //�����ཪλ��ϼ�ư�������ʤ�
    $url_header = '<a target="_top" href="game_frame.php' . $url_room .
      $url_dead . $url_heaven . $url_list;
    OutputAutoReloadLink($url_header . $url_sound  . '&auto_reload=');

    $url = $url_header . $url_reload . '&play_sound=';
    echo ' [���Ǥ��Τ餻](' .
      ($RQ_ARGS->play_sound ?  'on ' . $url . 'off">off</a>' : $url . 'on">on</a> off') .
      ')'."\n";
  }

  //�ץ쥤�䡼�ꥹ�Ȥ�ɽ������
  echo '<a target="_top" href="game_frame.php' . $url_room . $url_dead . $url_heaven .
    $url_reload . $url_sound  . '&list_down=' . ($RQ_ARGS->list_down ? 'off">��' : 'on">��') .
    '�ꥹ��</a>'."\n";

  //�������򲻤Ǥ��Τ餻����
  if($RQ_ARGS->play_sound){
    //�������ξ��
    if($cookie_day_night != $ROOM->day_night && $ROOM->is_day()) OutputSound('morning');

    /*
    //�۵Ĥ��ꡢ�򲻤��Τ餻��
    $cookie_objection_array = explode(',', $cookie_objection); //���å������ͤ�����˳�Ǽ����

    $count = count($objection_array);
    for($i = 1; $i <= $count; $i++){ //��ʬ��׻� (index �� 1 ����)
      //��ʬ����������̤��ǧ���Ʋ����Ĥ餹
      if((int)$objection_array[$i] > (int)$cookie_objection_array[$i]){
	$sql = mysql_query("SELECT sex FROM user_entry WHERE room_no = $room_no AND user_no = $i");
	$objection_sound = 'objection_' . mysql_result($sql, 0, 0);
	OutputSound($objection_sound, true);
      }
    }
    */
  }
  echo '</td></tr>'."\n".'</table>'."\n";

  switch($ROOM->day_night){
  case 'beforegame': //����������դ����
    echo '<div class="caution">'."\n";
    echo '������򳫻Ϥ���ˤ������������೫�Ϥ���ɼ����ɬ�פ�����ޤ�';
    echo '<span>(��ɼ�����ͤ�¼�ͥꥹ�Ȥ��طʤ��֤��ʤ�ޤ�)</span>'."\n";
    echo '</div>'."\n";
    break;

  case 'day':
    $time_message = '�����פޤ� ';
    break;

  case 'night':
    $time_message = '���������ޤ� ';
    break;

  case 'aftergame': //���Է�̤���Ϥ��ƽ�����λ
    OutputVictory();
    return;
  }

  if($ROOM->is_beforegame()) OutputGameOption(); //�����४�ץ���������
  echo '<table class="time-table"><tr>'."\n";
  if(! $ROOM->is_aftergame()){ //�����ཪλ��ʳ��ʤ顢�����ФȤλ��֥����ɽ��
    $date_str = gmdate('Y, m, j, G, i, s', $ROOM->system_time);
    echo '<script type="text/javascript" src="javascript/output_diff_time.js"></script>'."\n";
    echo '<td>�����Фȥ�����PC�λ��֥���(�饰��)�� ' . '<span><script type="text/javascript">' .
      "output_diff_time('$date_str');" . '</script>' . '��</span></td></td>'."\n";
    echo '<tr>';
  }
  OutputTimeTable(); //�в���������¸�Ϳ������

  $left_time = 0;
  //�в���֤����
  if($real_time) //�ꥢ�륿������
    GetRealPassTime(&$left_time);
  else //���äǻ��ַв���
    $left_talk_time = GetTalkPassTime(&$left_time);

  if($ROOM->is_beforegame()){
    echo '<td class="real-time">';
    if($real_time){ //�»��֤����»��֤����
      sscanf(strstr($ROOM->game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
      echo "������֡� �� <span>{$day_minutes}ʬ</span> / �� <span>{$night_minutes}ʬ</span>";
    }
    echo '�������ࡧ<span>' . ConvertTime($TIME_CONF->sudden_death) . '</span></td>';
  }
  if($ROOM->is_playing()){
    if($real_time){ //�ꥢ�륿������
      echo '<td class="real-time"><form name="realtime_form">'."\n";
      echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
      echo '</form></td>'."\n";
    }
    elseif($left_talk_time){ //ȯ���ˤ�벾�ۻ���
      echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
    }
  }

  //�۵Ĥ��ꡢ�Υܥ���(��Ȼ�ԥ⡼�ɰʳ�)
  if($ROOM->is_beforegame() ||
     ($ROOM->is_day() && ! $ROOM->dead_mode && ! $ROOM->heaven_mode && $left_time > 0)){
    $url = 'game_play.php' . $url_room . $url_reload . $url_sound . $url_list . '#game_top';
    echo <<<EOF
<td class="objection"><form method="POST" action="$url">
<input type="hidden" name="set_objection" value="on">
<input type="image" name="objimage" src="{$GAME_CONF->objection_image}" border="0">
</form></td>
<td>($objection_left_count)</td>

EOF;
  }
  echo '</tr></table>'."\n";

  if($ROOM->is_playing() && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($ROOM->sudden_death > 0){
      echo $MESSAGE->sudden_death_time . ConvertTime($ROOM->sudden_death) . '<br>'."\n";
    }
  }
}

//ŷ������å�����
function OutputHeavenTalkLog(){
  global $room_no, $ROOM;

  //���Ͼ�������å�
  // if($SELF->is_dead()) return false; //�ƤӽФ�¦�ǥ����å�����ΤǸ��ߤ�����

  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.live AS talk_live,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry, talk, user_icon
			WHERE talk.room_no = $room_no
			AND talk.location LIKE 'heaven'
			AND ( (user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR (user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no) )
			ORDER BY time DESC");

  echo '<table class="talk">'."\n";
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $talk_uname  = $array['talk_uname'];
    $talk_handle = $array['talk_handle_name'];
    $talk_live   = $array['talk_live'];
    // $talk_sex    = $array['talk_sex'];  //����̤����
    $talk_color  = $array['talk_color'];
    $sentence    = $array['sentence'];
    $font_type   = $array['font_type'];
    // $location    = $array['location']; //����̤����

    LineToBR(&$sentence); //���Ԥ�<br>�������ִ�

    //����򿦤���������Ƥ�����Τ� HN ���ɲ�
    if($ROOM->is_open_cast()) $talk_handle .= '<span>(' . $talk_uname . ')</span>';

    //���ý���
    echo '<tr class="user-talk">'."\n";
    echo '<td class="user-name"><font color="' . $talk_color . '">��</font>' .
      $talk_handle . '</td>'."\n";
    echo '<td class="say ' . $font_type . '">' . $sentence . '</td>'."\n";
    echo '</tr>'."\n";
  }
  echo '</table>'."\n";
}

//ǽ�Ϥμ���Ȥ������������
function OutputAbility(){
  global $GAME_CONF, $ROLE_IMG, $MESSAGE, $room_no, $ROOM, $SELF;

  //��������Τ�ɽ������
  if(! $ROOM->is_playing()) return false;

  if($SELF->is_dead()){ //��˴������ǽ�Ϥ�ɽ�����ʤ�
    echo '<span class="ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  $role_list = $SELF->role_list;
  $main_role = array_shift($role_list);
  $is_first_night = ($ROOM->is_night() && $ROOM->date == 1);
  $is_after_first_night = ($ROOM->is_night() && $ROOM->date > 1);

  if($main_role == 'human' || $main_role == 'suspect' || $main_role == 'unconscious'){
    $ROLE_IMG->DisplayImage('human');
  }
  elseif(strpos($main_role, 'wolf') !== false){
    $ROLE_IMG->DisplayImage($main_role);
    OutputPartner("role LIKE '%wolf%' AND uname <> '{$SELF->uname}'", 'wolf_partner'); //��֤�ɽ��
    OutputPartner("role LIKE 'whisper_mad%'", 'mad_partner'); //�񤭶��ͤ�ɽ��

    //�����̵�ռ���ɽ��
    if($ROOM->is_night()) OutputPartner("role LIKE 'unconscious%'", 'unconscious_list');

    if($main_role == 'tongue_wolf'){ //���ϵ�γ��߷�̤�ɽ��
      $action = 'TONGUE_WOLF_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult('wolf_result', $target, 'result_' . $target_role);
	  break;
	}
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('wolf-eat', 'WOLF_EAT'); //�����ɼ
  }
  elseif(strpos($main_role, 'mage') !== false){
    $role_name = ($main_role == 'dummy_mage' ? 'mage' : $main_role);
    $ROLE_IMG->DisplayImage($role_name);

    //�ꤤ��̤�ɽ��
    $action = 'MAGE_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    $header = ($main_role == 'psycho_mage' ? $main_role : 'result') . '_';
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult('mage_result', $target, $header . $target_role);
	break;
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('mage-do', 'MAGE_DO'); //�����ɼ
  }
  elseif(strpos($main_role, 'necromancer') !== false || $main_role == 'medium'){
    if(strpos($role, 'necromancer') !== false){
      $role_name = 'necromancer';
      $result    = 'necromancer_result';
      $action    = 'NECROMANCER_RESULT';
      switch($main_role){
      case 'soul_necromancer':
	$role_name = $main_role;
	$action    = 'SOUL_' . $action;
	break;

      case 'dummy_necromancer':
	$action = 'DUMMY_' . $action;
	break;
      }
    }
    else{
      $role_name = 'medium';
      $result    = 'medium_result';
      $action    = 'MEDIUM_RESULT';
    }
    $ROLE_IMG->DisplayImage($role_name);

    //Ƚ���̤�ɽ��
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif($main_role == 'trap_mad'){
    $ROLE_IMG->DisplayImage($main_role);

    if(strpos($role, 'lost_ability') === false && $is_after_first_night){ //�����ɼ
      OutputVoteMessage('wolf-eat', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
  elseif(strpos($main_role, 'mad') !== false){
    $ROLE_IMG->DisplayImage($main_role);
    if($main_role != 'mad'){
      OutputPartner("role LIKE '%wolf%'", 'wolf_partner'); //ϵ��ɽ��
      if($main_role == 'whisper_mad'){ //�񤭶��ͤ�ɽ��
	OutputPartner("role LIKE 'whisper_mad%' AND uname <> '{$SELF->uname}'", 'mad_partner');
      }
    }
  }
  elseif(strpos($main_role, 'guard') !== false){
    $role_name = ($main_role == 'dummy_guard' ? 'guard' : $main_role);
    $ROLE_IMG->DisplayImage($role_name);

    //��ҷ�̤�ɽ��
    $sql = GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'guard_success');
	break;
      }
    }

    if($main_role != 'dummy_guard'){ //����̤�ɽ��
      $sql = GetAbilityActionResult('GUARD_HUNTED');
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	if($SELF->handle_name == $actor){
	  OutputAbilityResult(NULL, $target, 'guard_hunted');
	  break;
	}
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'GUARD_DO'); //�����ɼ
  }
  elseif($main_role == 'reporter'){
    $ROLE_IMG->DisplayImage($main_role);

    //���Է�̤�ɽ��
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	$target .= ' ����� ' . $wolf_handle;
	OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
	break;
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'REPORTER_DO'); //�����ɼ
  }
  elseif(strpos($main_role, 'common') !== false){
    $ROLE_IMG->DisplayImage('common');

    //��֤�ɽ��
    if($main_role == 'dummy_common'){
      OutputPartner("uname = 'dummy_boy' AND uname <> '{$SELF->uname}'", 'common_partner');
    }
    else{
      OutputPartner("role LIKE 'common%' AND uname <> '{$SELF->uname}'", 'common_partner');
    }
  }
  elseif($main_role == 'child_fox'){
    $ROLE_IMG->DisplayImage('child_fox');

    //��֤�ɽ��
    OutputPartner("role LIKE '%fox%' AND uname <> '{$SELF->uname}'", 'fox_partner');

    //�ꤤ��̤�ɽ��
    $action = 'CHILD_FOX_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
	break;
      }
    }

    if($ROOM->is_night()) OutputVoteMessage('mage-do', 'CHILD_FOX_DO'); //�����ɼ
  }
  elseif(strpos($main_role, 'fox') !== false){
    if($main_role == 'poison_fox'){
      echo '[���]<br>�����ʤ��ϡִɸѡס��Ǥ���äƤ��ޤ���(�٤���ǽ�Ϥ�Ĵ����Ǥ�)<br>'."\n";
    }
    elseif($main_role == 'white_fox'){
      echo '[���]<br>�����ʤ��ϡ���ѡפǤ�������Ƥ��ˤޤ��󤬡���ϵ�˽�����Ȼ��Ǥ��ޤ��ޤ���(�٤���ǽ�Ϥ�Ĵ����Ǥ�)<br>'."\n";
    }
    else
      $ROLE_IMG->DisplayImage($main_role);

    //�ҸѰʳ�����֤�ɽ��
    OutputPartner("role LIKE 'fox%' AND uname <> '{$SELF->uname}'", 'fox_partner');

    //�Ѥ�����줿��å�������ɽ��
    $sql = GetAbilityActionResult('FOX_EAT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      if($SELF->handle_name == mysql_result($sql, $i, 0)){
	OutputAbilityResult('fox_targeted', NULL);
	break;
      }
    }
  }
  elseif($main_role == 'poison_cat'){
    // $ROLE_IMG->DisplayImage('poison_cat');
    echo '[���]<br>�����ʤ��ϡ�ǭ���ס��Ǥ��äƤ��ޤ����ޤ��������ͤ�ï������ɤ餻������Ǥ��ޤ���<br>'."\n";

    if(! $ROOM->is_open_cast()){
      //������̤�ɽ��
      $action = 'POISON_CAT_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
	  break;
	}
      }

      if($is_after_first_night){ //�����ɼ
	OutputVoteMessage('poison-cat-do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($main_role == 'incubate_poison'){
    $ROLE_IMG->DisplayImage($main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif(strpos($main_role, 'poison') !== false) $ROLE_IMG->DisplayImage('poison');
  elseif($main_role == 'pharmacist'){
    $ROLE_IMG->DisplayImage($main_role);

    //���Ƿ�̤�ɽ��
    $sql = GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'pharmacist_success');
	break;
      }
    }
  }
  elseif($main_role == 'cupid'){
    $ROLE_IMG->DisplayImage($main_role);

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ������
    $cupid_id = strval($SELF->user_no);
    OutputPartner("role LIKE '%lovers[$cupid_id]%'", 'cupid_pair');

    if($is_first_night) OutputVoteMessage('cupid-do', 'CUPID_DO'); //���������ɼ
  }
  elseif($main_role == 'mania'){
    // $ROLE_IMG->DisplayImage($main_role);
    echo '[���]<br>�����ʤ��ϡֿ��åޥ˥��פǤ���1���ܤ���˻��ꤷ���ͤΥᥤ���򿦤򥳥ԡ����뤳�Ȥ��Ǥ��ޤ���<br>'."\n";

    if($is_first_night) OutputVoteMessage('mania-do', 'MANIA_DO'); //���������ɼ
  }
  elseif($main_role == 'assassin'){
    // $ROLE_IMG->DisplayImage($main_role);
    echo '[���]<br>�����ʤ��ϡְŻ��ԡפǤ������¼�Ͱ�ͤ�Ż����뤳�Ȥ��Ǥ��ޤ���<br>'."\n";

    if($is_after_first_night){ //�����ɼ
      OutputVoteMessage('assassin-do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($main_role == 'quiz'){
    $ROLE_IMG->DisplayImage($main_role);
    if(strpos($ROOM->game_option, 'chaos') !== false){
      // $ROLE_IMG->DisplayImage('quiz_chaos');
      echo '����⡼�ɤǤϤ��ʤ��κ����ǽ�ϤǤ������̵��������ޤ���<br>'."\n";
      echo '�Ϥä�����ä�̵�������ʤΤǹ�������˥������Ǥ�Ф���ͷ�֤��ɤ��Ǥ��礦��<br>'."\n";
    }
  }

  //���������Ǥ��
  if(in_array('lost_ability', $role_list)) $ROLE_IMG->DisplayImage('lost_ability'); //ǽ�ϼ���
  if($SELF->is_lovers()){ //���ͤ�ɽ������
    $lovers_str = GetLoversConditionString($SELF->role);
    OutputPartner("$lovers_str AND uname <> '{$SELF->uname}'", 'lovers_header', 'lovers_footer');
  }

  if(in_array('copied', $role_list)){ //���åޥ˥��Υ��ԡ���̤�ɽ��
    $action = 'MANIA_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'result_' . $target_role);
	break;
      }
    }
  }

  //����ʹߤϥ�������������ץ����αƶ��������
  if(strpos($ROOM->game_option, 'secret_sub_role') !== false) return;

  $role_keys_list   = array_keys($GAME_CONF->sub_role_list);
  $not_display_list = array('decide', 'plague', 'good_luck', 'bad_luck', 'lovers', 'copied');
  $display_list     = array_diff($role_keys_list, $not_display_list);
  $target_list      = array_intersect($display_list, $role_list);

  foreach($target_list as $this_role){
    $ROLE_IMG->DisplayImage($this_role);
  }
}

//��֤�ɽ������
function OutputPartner($query, $header, $footer = NULL){
  global $ROLE_IMG, $room_no;

  $query_header = "SELECT handle_name FROM user_entry WHERE room_no = '$room_no' AND user_no > 0 AND ";
  $partner_list = FetchArray($query_header . $query);
  if(count($partner_list) < 1) return false; //��֤����ʤ����ɽ�����ʤ�

  echo '<table class="ability-partner"><tr>'."\n";
  echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  echo '<td>��';
  foreach($partner_list as $partner) echo $partner . '���󡡡�';
  echo '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//ǽ��ȯư��̤�ǡ����١������䤤��碌��
function GetAbilityActionResult($action){
  global $room_no, $ROOM;

  $yesterday = $ROOM->date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $yesterday AND type = '$action'");
}

//ǽ��ȯư��̤�ɽ������
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  if($target) echo '<td>' . $target . '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//���̤��ɼ��å���������
function OutputVoteMessage($class, $situation, $not_situation = ''){
  global $MESSAGE;

  //��ɼ�Ѥߤʤ��å�������ɽ�����ʤ�
  if(CheckSelfVoteNight($situation, $not_situation)) return false;

  $class_str   = 'ability-' . $class; //���饹̾�ϥ��������������Ȥ�ʤ��Ǥ���
  $message_str = 'ability_' . strtolower($situation);
  echo '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}

//��μ�ʬ��̤��ɼ�����å�
function CheckSelfVoteDay(){
  global $room_no, $ROOM, $SELF;

  //��ɼ��������
  $vote_times = GetVoteTimes();
  echo '<div class="self-vote">��ɼ ' . $vote_times . ' ���ܡ�';

  //��ɼ�Ѥߤ��ɤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND date = {$ROOM->date} AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  echo (mysql_result($sql, 0, 0) ? '��ɼ�Ѥ�' : '�ޤ���ɼ���Ƥ��ޤ���') . '</div>'."\n";
}

//��ʬ�ΰ�������
function OutputSelfLastWords(){
  global $room_no, $ROOM, $SELF;

  //�����ཪλ���ɽ�����ʤ�
  if($ROOM->is_aftergame()) return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND user_no > 0");

  //�ޤ����Ϥ��Ƥʤ����ɽ�����ʤ�
  if(mysql_num_rows($sql) == 0) return false;

  $last_words = mysql_result($sql, 0, 0);
  LineToBR(&$last_words); //���ԥ����ɤ��Ѵ�
  if($last_words == '') return false;

  echo <<<EOF
<table class="lastwords" cellspacing="5"><tr>
<td class="lastwords-title">��ʬ�ΰ��</td>
<td class="lastwords-body">{$last_words}</td>
</tr></table>

EOF;
}
?>
