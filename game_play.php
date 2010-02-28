<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
$INIT_CONF->LoadClass('SESSION', 'ROLES', 'ICON_CONF', 'TIME_CONF', 'ROOM_IMG');

//-- �ǡ������� --//
$INIT_CONF->LoadRequest('RequestGamePlay'); //���������
if($RQ_ARGS->play_sound) $INIT_CONF->LoadClass('SOUND', 'COOKIE'); //���Ǥ��Τ餻

$DB_CONF->Connect(); //DB ��³
$SESSION->Certify(); //���å����ǧ��

$ROOM =& new Room($RQ_ARGS); //¼��������
$ROOM->dead_mode    = $RQ_ARGS->dead_mode; //��˴�ԥ⡼��
$ROOM->heaven_mode  = $RQ_ARGS->heaven_mode; //���å⡼��
$ROOM->system_time  = TZTime(); //���߻�������
$ROOM->sudden_death = 0; //������¹ԤޤǤλĤ����

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF = $USERS->BySession(); //��ʬ�ξ�������

//-- �ƥ����� --//
//$SELF->ChangeRole('random_voice');
//$SELF->AddRole('strong_voice');

//������˱������ɲå��饹�����
if($ROOM->IsBeforeGame()){
  $INIT_CONF->LoadClass('CAST_CONF', 'ROOM_IMG', 'GAME_OPT_MESS'); //�����४�ץ����ɽ����
  $ROOM->LoadVote();
}
elseif($ROOM->IsFinished()){
  $INIT_CONF->LoadClass('VICT_MESS'); //���Է��ɽ����
}

//ɬ�פʥ��å����򥻥åȤ���
$objection_list = array(); //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���ξ���
$objection_left_count = 0;  //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���λĤ���
SendCookie();

//-- ȯ������ --//
ConvertSay(&$RQ_ARGS->say); //ȯ���ִ�����

if($RQ_ARGS->say == ''){
  CheckSilence(); //ȯ�������ʤ饲�������ڤΥ����å�(���ۡ�������)
}
elseif($RQ_ARGS->last_words && ! $SELF->IsDummyBoy()){
  EntryLastWords($RQ_ARGS->say); //�����Ͽ (�٤���Ƚ����ϴؿ���ǹԤ�)
}
elseif($SELF->IsDead() || $SELF->IsDummyBoy() || $SELF->last_load_day_night == $ROOM->day_night){
  Say($RQ_ARGS->say); //���Ǥ��� or �����귯 or �����ॷ���󤬰��פ��Ƥ���ʤ�񤭹���
}
else{
  CheckSilence(); //ȯ�����Ǥ��ʤ����֤ʤ饲�������ڥ����å�
}

if($SELF->last_load_day_night != $ROOM->day_night){ //�����ॷ����򹹿�
  $SELF->Update('last_load_day_night', $ROOM->day_night);
}

//-- �ǡ������� --//
OutputGamePageHeader(); //HTML�إå�
OutputGameHeader(); //�����Υ����ȥ�ʤ�

if(! $ROOM->heaven_mode){
  if(! $RQ_ARGS->list_down) OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
  OutputAbility(); //��ʬ����������
  if($ROOM->IsDay() && $SELF->IsLive()) CheckSelfVoteDay(); //�����ɼ�Ѥߥ����å�
  OutputRevoteList(); //����ɼ�λ�����å�������ɽ������
}

//���å������
($SELF->IsDead() && $ROOM->heaven_mode) ? OutputHeavenTalkLog() : OutputTalkLog();

if(! $ROOM->heaven_mode){
  if($SELF->IsDead()) OutputAbilityAction(); //ǽ��ȯ��
  OutputLastWords(); //���
  OutputDeadMan();   //��˴��
  OutputVoteList();  //��ɼ���
  if(! $ROOM->dead_mode) OutputSelfLastWords(); //��ʬ�ΰ��
  if($RQ_ARGS->list_down) OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
}
OutputHTMLFooter();

//-- �ؿ� --//
//ɬ�פʥ��å�����ޤȤ����Ͽ(�Ĥ��Ǥ˺ǿ��ΰ۵Ĥ���ξ��֤������������˳�Ǽ)
function SendCookie(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $SELF, $objection_list, $objection_left_count;

  //<�������򲻤Ǥ��Τ餻��>
  //���å����˳�Ǽ (�������˲��Ǥ��Τ餻�ǻȤ���ͭ�����°����)
  setcookie('day_night', $ROOM->day_night, $ROOM->system_time + 3600);

  //-- �ְ۵ġפ���򲻤Ǥ��Τ餻�� --//
  //���ޤǤ˼�ʬ���ְ۵ġפ���򤷤���������
  $query = "SELECT COUNT(message) FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND type = 'OBJECTION' AND message = '{$SELF->user_no}'";
  $self_objection_count = FetchResult($query);

  //�����Ƥ���(�����ཪλ��ϻ�ԤǤ�OK)�ְ۵ġפ��ꡢ�Υ��å��׵᤬����Х��åȤ���(����������ξ��)
  if($SELF->IsLive() && ! $ROOM->IsNight() && $RQ_ARGS->set_objection &&
     $self_objection_count < $GAME_CONF->objection){
    $ROOM->SystemMessage($SELF->user_no, 'OBJECTION');
    $ROOM->Talk('OBJECTION', $SELF->uname);
  }

  //�桼�������������ƿͿ�ʬ�Ρְ۵Ĥ���פΥ��å������ۤ���
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} AND user_no > 0";
  $user_count = FetchResult($query);
  // �����ꥻ�å� (0 ���ܤ��Ѥ��ͤ�����ʤ������ݾڤ���Ƥ�������פ��ʡ�)
  // ���å��Ƿ��֤��Ф�ȿ������ݤʻ��ˤʤꤽ��
  // $objection_list = array();
  // unset($objection_list[0]);
  $objection_list = array_fill(0, $user_count, 0); //index �� 0 ����

  //message:�۵Ĥ���򤷤��桼�� No �Ȥ��β�������
  $query = "SELECT message, COUNT(message) AS message_count FROM system_message " .
    "WHERE room_no = {$ROOM->id} AND type = 'OBJECTION' GROUP BY message";
  $array = FetchAssoc($query);
  foreach($array as $this_array){
    $this_user_no = (int)$this_array['message'];
    $this_count   = (int)$this_array['message_count'];
    $objection_list[$this_user_no - 1] = $this_count;
  }

  //���å����˳�Ǽ (ͭ�����°����)
  foreach($objection_list as $value){
    if($str != '') $str .= ','; //����޶��ڤ�
    $str .= $value;
  }
  setcookie('objection', $str, $ROOM->system_time + 3600);

  //�Ĥ�۵Ĥ���β��
  $objection_left_count = $GAME_CONF->objection - $objection_list[$SELF->user_no - 1];

  //<����ɼ�򲻤Ǥ��Τ餻��>
  //����ɼ�β�������
  if(($last_vote_times = GetVoteTimes(true)) > 0){ //���å����˳�Ǽ (ͭ�����°����)
    setcookie('vote_times', $last_vote_times, $ROOM->system_time + 3600);
  }
  else{ //���å��������� (ͭ�����°����)
    setcookie('vote_times', '', $ROOM->system_time - 3600);
  }
}

//ȯ���ִ�����
function ConvertSay(&$say){
  global $GAME_CONF, $MESSAGE, $ROOM, $ROLES, $USERS, $SELF;

  //����ɻ�����ԡ�������ץ쥤��ʳ��ʤ���������å�
  if($say == '' || $SELF->IsDead() || ! $ROOM->IsPlaying()) return false;
  #if($say == '' || $SELF->IsDead()) return false; //�ƥ�����

  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  $ROLES->actor = $virtual_self;

  //˨ϵ��˨�ѡ��Կ��Ԥϰ����Ψ��ȯ�������ʤ�(�ǥե���Ȼ�)�ˤʤ�
  if($virtual_self->IsRole('cute_wolf', 'cute_fox', 'suspect') &&
     mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate){
    $say = ($MESSAGE->cute_wolf != '' ? $MESSAGE->cute_wolf : $MESSAGE->wolf_howl);
  }
  //�»Ρ��ʽ��ϰ����Ψ��ȯ���������ؤ��
  elseif($virtual_self->IsRole('gentleman', 'lady') &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){
    $role_name = ($virtual_self->IsRole('gentleman') ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $target_list = array();
    foreach($USERS->rows as $user){ //��ʬ�ʳ�����¸�Ԥ� HN �����
      if(! $user->IsSelf() && $user->IsLive()) $target_list[] = $user->handle_name;
    }
    $say = $MESSAGE->$message_header . GetRandom($target_list) . $MESSAGE->$message_footer;
  }
  //ϵ��ǯ�ϰ����Ψ��ȯ�����Ƥ�ȿž�����
  elseif($virtual_self->IsRole('liar') && mt_rand(1, 100) <= $GAME_CONF->liar_rate){
    $say = strtr($say, $GAME_CONF->liar_replace_list);
  }

  $filter_list = $ROLES->Load('say_filter');
  foreach($filter_list as $filter) $filter->FilterSay($say);
}

//�����Ͽ
function EntryLastWords($say){
  global $ROOM, $USERS, $SELF;

  if($ROOM->IsFinished()) return false; //�����ཪλ��ʤ饹���å�

  if($SELF->IsLive()){ //�֥󲰡���������ɮ��������Ͽ���ʤ�
    if($SELF->IsRole('reporter', 'evoke_scanner', 'no_last_words')) return false;
    $SELF->Update('last_words', $say); //�����Ĥ�
  }
  elseif($SELF->IsDead() && $SELF->IsRole('mind_evoke')){
    //���󤻤��Ƥ��륤�������٤Ƥΰ���򹹿�����
    foreach($SELF->partner_list['mind_evoke'] as $target_id){
      $target = $USERS->ByID($target_id);
      if($target->IsLive()) $target->Update('last_words', $say);
    }
  }
}

//ȯ��
function Say($say){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  if($ROOM->IsRealTime()){ //�ꥢ�륿������
    GetRealPassTime(&$left_time);
    $spend_time = 0; //���äǻ��ַв���������̵���ˤ���
  }
  else{ //���äǻ��ַв���
    GetTalkPassTime(&$left_time); //�в���֤���
    $spend_time = floor(strlen($say) / 100); //�в����
    if($spend_time < 1) $spend_time = 1; //�Ǿ��� 1
    elseif($spend_time > 4) $spend_time = 4; //����� 4
  }

  if(! $ROOM->IsPlaying()){ //�����೫������Ϥ��Τޤ�ȯ��
    Write($say, $ROOM->day_night, 0, true);
  }
  //�����귯 (���� GM �б�) �ϰ�������ѤΥ����ƥ��å��������ڤ��ؤ�
  elseif($SELF->IsDummyBoy() && $RQ_ARGS->last_words){
    Write($say, "{$ROOM->day_night} dummy_boy", 0); //ȯ�����֤򹹿����ʤ�
  }
  elseif($SELF->IsDead()){ //��˴�Ԥ�����
    Write($say, 'heaven', 0); //ȯ�����֤򹹿����ʤ�
  }
  elseif($SELF->IsLive() && $left_time > 0){ //��¸�Ԥ����»�����
    if($ROOM->IsDay()){ //��Ϥ��Τޤ�ȯ��
      Write($say, 'day', $spend_time, true);
    }
    elseif($ROOM->IsNight()){ //��������ʬ����
      $update = $SELF->IsWolf(); //���ַв᤹��ΤϿ�ϵ��ȯ���Τ�
      if(! $update) $spend_time = 0;

      if($virtual_self->IsWolf(true)) //��ϵ
	$location = 'wolf';
      elseif($virtual_self->IsRole('whisper_mad')) //�񤭶���
	$location = 'mad';
      elseif($virtual_self->IsRole('common')) //��ͭ��
	$location = 'common';
      elseif($virtual_self->IsFox(true)) //�Ÿ�
	$location = 'fox';
      else //�Ȥ��
	$location = 'self_talk';

      Write($say, 'night ' . $location, $spend_time, $update);
    }
  }
}

//ȯ���� DB ����Ͽ����
function Write($say, $location, $spend_time, $update = false){
  global $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  //�����礭�������
  $voice = $RQ_ARGS->font_type;
  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  if($ROOM->IsPlaying() && $virtual_self->IsLive()){
    $ROLES->actor = $virtual_self;
    $filter_list = $ROLES->Load('voice');
    foreach($filter_list as $filter) $filter->FilterVoice($voice, $say);
  }

  $ROOM->Talk($say, $SELF->uname, $location, $voice, $spend_time);
  if($update) $ROOM->UpdateTime();
  SendCommit(); //������ߥå�
}

//���������ڤΥ����å�
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $ROOM, $USERS;

  //��������ʳ��Ͻ����򤷤ʤ�
  if(! $ROOM->IsPlaying()) return false;

  //�ơ��֥��å�
  $query = 'LOCK TABLES room WRITE, talk WRITE, vote WRITE, user_entry WRITE, system_message WRITE';
  if(! mysql_query($query)) return false;

  //�ǽ�ȯ�����狼��κ�ʬ�����
  $query = 'SELECT UNIX_TIMESTAMP() - last_updated FROM room WHERE room_no = ' . $ROOM->id;
  $last_updated_pass_time = FetchResult($query);

  //�в���֤����
  if($ROOM->IsRealTime()) //�ꥢ�륿������
    GetRealPassTime(&$left_time);
  else //���äǻ��ַв���
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //�ꥢ�륿�������Ǥʤ������»�������������ͤ�Ķ�����ʤ�ʤ����ֿʤ��(����)
  if(! $ROOM->IsRealTime() && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '�������������������� ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      $ROOM->Talk($sentence, '', '', NULL, $TIME_CONF->silence_pass);
      $ROOM->UpdateTime();
    }
  }
  elseif($left_time == 0){ //���»��֤�᤮�Ƥ�����ٹ��Ф�
    //������ȯư�ޤǤλ��֤����
    $left_time_str = ConvertTime($TIME_CONF->sudden_death); //ɽ���Ѥ��Ѵ�
    $sudden_death_announce = '����' . $left_time_str . '��' . $MESSAGE->sudden_death_announce;

    //���˷ٹ��Ф��Ƥ��뤫�����å�
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$ROOM->id} " .
      "AND date = {$ROOM->date} AND location = '{$ROOM->day_night} system' " .
      "AND uname = 'system' AND sentence = '$sudden_death_announce'";
    if(FetchResult($query) == 0){ //�ٹ��Ф��Ƥ��ʤ��ä���Ф�
      $ROOM->Talk($sudden_death_announce);
      $ROOM->UpdateTime(); //�������֤򹹿�
      $last_updated_pass_time = 0;
    }
    $ROOM->sudden_death = $TIME_CONF->sudden_death - $last_updated_pass_time;

    //���»��֤�᤮�Ƥ�����̤��ɼ�οͤ������व����
    if($ROOM->sudden_death <= 0){
      //��¸�Ԥ�������뤿��δ��� SQL ʸ
      $query_live = "SELECT uname FROM user_entry WHERE room_no = {$ROOM->id} " .
	"AND live = 'live' AND user_no > 0";

      //��ɼ�Ѥߤοͤ�������뤿��δ��� SQL ʸ
      $query_vote = "SELECT uname FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} AND ";

      if($ROOM->IsDay()){
	//��ɼ��������
	$vote_times = GetVoteTimes();

	//��ɼ�ѤߤοͤΥ桼��̾�����
	$add_action = "situation = 'VOTE_KILL' AND vote_times = $vote_times";
	$vote_uname_list = FetchArray($query_vote . $add_action);

	//��ɼ��ɬ�פʿͤΥ桼��̾�����
	$live_uname_list = FetchArray($query_live);

	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);
      }
      elseif($ROOM->IsNight()){
	//�о��򿦤Υǡ��������
	$action_list = array('MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO', 'VOODOO_MAD_DO',
			     'VOODOO_FOX_DO', 'CHILD_FOX_DO');
	$actor_list  = array('%mage', 'voodoo_killer', 'jammer_mad', 'voodoo_mad',
			     'voodoo_fox', 'child_fox');

	if($ROOM->date == 1){
	  array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
	  array_push($actor_list, '%scanner', '%cupid', '%mania');
	}
	else{
	  array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'DREAM_EAT',
		     'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
	  array_push($actor_list, '%guard', 'anti_voodoo', 'reporter', 'dream_eater_mad',
		     'assassin', 'trap_mad');
	  if(! $ROOM->IsOpenCast()){
	    array_push($action_list, 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	    array_push($actor_list, '%cat', 'revive_fox');
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

	//̤��ɼ�οͤ����
	$novote_uname_list = array_diff($live_uname_list, $vote_uname_list);

	if(FetchCount($query_vote . "situation = 'WOLF_EAT'") < 1){ //��ϵ����ɼ���ǧ
	  $novote_uname_list = array_merge($novote_uname_list, $USERS->GetLivingWolves());
	}
      }

      //̤��ɼ�Ԥ����������व����
      foreach($novote_uname_list as $uname){
	$USERS->SuddenDeath($USERS->ByUname($uname)->user_no);
      }
      LoversFollowed(true);
      InsertMediumMessage();

      $ROOM->Talk($MESSAGE->vote_reset); //��ɼ�ꥻ�åȥ�å�����
      $ROOM->Talk($sudden_death_announce); //��������Υ�å�����
      $ROOM->UpdateTime(); //���»��֥ꥻ�å�
      DeleteVote(); //��ɼ�ꥻ�å�
      CheckVictory(); //���ԥ����å�
    }
  }
  mysql_query('UNLOCK TABLES'); //�ơ��֥��å����
}

//¼̾�������ϡ������ܡ����פޤǡ����֤����(���Ԥ��Ĥ�����¼��̾�������ϡ����Ԥ����)
function OutputGameHeader(){
  global $GAME_CONF, $TIME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF,
    $COOKIE, $SOUND, $objection_list, $objection_left_count;

  $room_message = '<td class="room"><span>' . $ROOM->name . '¼</span>����' . $ROOM->comment .
    '��[' . $ROOM->id . '����]</td>'."\n";
  $url_room   = '?room_no=' . $ROOM->id;
  $url_reload = $RQ_ARGS->auto_reload > 0 ? '&auto_reload=' . $RQ_ARGS->auto_reload : '';
  $url_sound  = $RQ_ARGS->play_sound ? '&play_sound=on'  : '';
  $url_list   = $RQ_ARGS->list_down  ? '&list_down=on'   : '';
  $url_dead   = $ROOM->dead_mode     ? '&dead_mode=on'   : '';
  $url_heaven = $ROOM->heaven_mode   ? '&heaven_mode=on' : '';
  $real_time  = $ROOM->IsRealTime();

  echo '<table class="game-header"><tr>'."\n";
  if(($SELF->IsDead() && $ROOM->heaven_mode) || $ROOM->IsAfterGame()){ //��ȥ�������
    if($SELF->IsDead() && $ROOM->heaven_mode)
      echo '<td>&lt;&lt;&lt;ͩ��δ�&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //�������Υ��ؤΥ������
    echo '<td class="view-option">��';

    $url_header ='<a href="game_log.php' . $url_room . '&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '0&day_night=beforegame' . $url_footer . '0(������)</a>'."\n";
    echo $url_header . '1' . $url_night . '1(��)</a>'."\n";
    for($i = 2; $i < $ROOM->date; $i++){
      echo $url_header . $i . $url_day   . $i . '(��)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(��)</a>'."\n";
    }
    if($ROOM->IsNight() && $ROOM->heaven_mode){
      echo $url_header . $ROOM->date . $url_day . $ROOM->date . '(��)</a>'."\n";
    }
    elseif($ROOM->IsAfterGame()){
      $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$ROOM->id} " .
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
    if($SELF->IsDead() && $ROOM->dead_mode){ //��˴�Ԥξ��Ρ����������ɽ���Ͼ�⡼��
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="����">
</form>

EOF;
    }
  }

  if(! $ROOM->IsAfterGame()){ //�����ཪλ��ϼ�ư�������ʤ�
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
    if($COOKIE->day_night != $ROOM->day_night && $ROOM->IsDay()) $SOUND->Output('morning');

    //�۵Ĥ��ꡢ�򲻤��Τ餻��
    $cookie_objection_list = explode(',', $COOKIE->objection); //���å������ͤ�����˳�Ǽ����
    $count = count($objection_list);
    for($i = 0; $i < $count; $i++){ //��ʬ��׻� (index �� 0 ����)
      //��ʬ����������̤��ǧ���Ʋ����Ĥ餹
      if((int)$objection_list[$i] > (int)$cookie_objection_list[$i]){
	$SOUND->Output('objection_' . $USERS->ByID($i + 1)->sex, true);
      }
    }
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

  if($ROOM->IsBeforeGame()) OutputGameOption(); //�����४�ץ���������
  echo '<table class="time-table"><tr>'."\n";
  if(! $ROOM->IsAfterGame()){ //�����ཪλ��ʳ��ʤ顢�����ФȤλ��֥����ɽ��
    $date_str = TZDate('Y, m, j, G, i, s', $ROOM->system_time);
    echo '<script type="text/javascript" src="javascript/output_diff_time.js"></script>'."\n";
    echo '<td>�����Фȥ�����PC�λ��֥���(�饰��)�� ' . '<span><script type="text/javascript">' .
      "output_diff_time('$date_str');" . '</script>' . '��</span></td></td>'."\n";
    echo '<tr>';
  }
  OutputTimeTable(); //�в���������¸�Ϳ������

  $left_time = 0;
  if($ROOM->IsBeforeGame()){
    echo '<td class="real-time">';
    if($real_time){ //�»��֤����»��֤����
      echo "������֡� �� <span>{$ROOM->real_time->day}ʬ</span> / " .
	"�� <span>{$ROOM->real_time->night}ʬ</span>";
    }
    echo '�������ࡧ<span>' . ConvertTime($TIME_CONF->sudden_death) . '</span></td>';
  }
  if($ROOM->IsPlaying()){
    if($real_time){ //�ꥢ�륿������
      GetRealPassTime(&$left_time);
      echo '<td class="real-time"><form name="realtime_form">'."\n";
      echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
      echo '</form></td>'."\n";
    }
    else{ //ȯ���ˤ�벾�ۻ���
      echo '<td>' . $time_message . GetTalkPassTime(&$left_time) . '</td>'."\n";
    }
  }

  //�۵Ĥ��ꡢ�Υܥ���(��Ȼ�ԥ⡼�ɰʳ�)
  if($ROOM->IsBeforeGame() ||
     ($ROOM->IsDay() && ! $ROOM->dead_mode && ! $ROOM->heaven_mode && $left_time > 0)){
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

  if($ROOM->IsPlaying() && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($ROOM->sudden_death > 0){
      echo $MESSAGE->sudden_death_time . ConvertTime($ROOM->sudden_death) . '<br>'."\n";
    }
  }
}

//ŷ������å�����
function OutputHeavenTalkLog(){
  global $ROOM, $USERS;

  //���Ͼ�������å�
  // if($SELF->IsDead()) return false; //�ƤӽФ�¦�ǥ����å�����ΤǸ��ߤ�����

  $builder =& new DocumentBuilder();
  $builder->BeginTalk('talk');
  $talk_list = $ROOM->LoadTalk(true);
  foreach($talk_list as $talk){
    $user = $USERS->ByUname($talk->uname); //�桼�������

    $symbol = '<font color="' . $user->color . '">��</font>';
    $handle_name = $user->handle_name;
    //����򿦤���������Ƥ�����Τ� HN ���ɲ�
    if($ROOM->IsOpenCast()) $handle_name .= '<span>(' . $talk->uname . ')</span>';

    $builder->RawAddTalk($symbol, $handle_name, $talk->sentence, $talk->font_type);
  }
  $builder->EndTalk();
}

//��μ�ʬ��̤��ɼ�����å�
function CheckSelfVoteDay(){
  global $MESSAGE, $ROOM, $USERS, $SELF;

  //��ɼ��������
  $vote_times = GetVoteTimes();
  $sentence = '<div class="self-vote">��ɼ ' . $vote_times . ' ���ܡ�';

  //��ɼ�оݼԤ����
  $query = "SELECT target_uname FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  $target_uname = FetchResult($query);
  $sentence .= ($target_uname === false ? '<font color="red">�ޤ���ɼ���Ƥ��ޤ���</font>' :
		$USERS->GetHandleName($target_uname, true) . '�������ɼ�Ѥ�');
  $sentence .= '</div>'."\n";
  if($target_uname === false){
    $sentence .= '<span class="ability vote-do">' . $MESSAGE->ability_vote . '</span><br>'."\n";
  }
  echo $sentence;
}

//��ʬ�ΰ�������
function OutputSelfLastWords(){
  global $ROOM, $SELF;

  //�����ཪλ���ɽ�����ʤ�
  if($ROOM->IsAfterGame()) return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = {$ROOM->id}
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
