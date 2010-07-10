<?php
//-- ���ôؿ� --//
//���󤫤������˰�ļ��Ф�
function GetRandom($array){
  return $array[array_rand($array)];
}

//-- ���ִ�Ϣ --//
//�ꥢ�륿����ηв����
function GetRealPassTime(&$left_time){
  global $ROOM;

  //������κǽ�λ�������
  $query = 'SELECT MIN(time) FROM talk' . $ROOM->GetQuery() .
    " AND location LIKE '{$ROOM->day_night}%'";
  $start_time = FetchResult($query);
  if($start_time === false) $start_time = $ROOM->system_time;

  $base_time = $ROOM->real_time->{$ROOM->day_night} * 60; //���ꤵ�줿���»���
  $left_time = $base_time - ($ROOM->system_time - $start_time); //�Ĥ����
  if($left_time < 0) $left_time = 0; //�ޥ��ʥ��ˤʤä��饼��ˤ���
  return array($start_time, $start_time + $base_time);
}

//���äǻ��ַв����ηв����
function GetTalkPassTime(&$left_time, $silence = false){
  global $TIME_CONF, $ROOM;

  $query = 'SELECT SUM(spend_time) FROM talk' . $ROOM->GetQuery() .
    " AND location LIKE '{$ROOM->day_night}%'";
  $spend_time = (int)FetchResult($query);

  if($ROOM->IsDay()){ //���12����
    $base_time = $TIME_CONF->day;
    $full_time = 12;
  }
  else{ //���6����
    $base_time = $TIME_CONF->night;
    $full_time = 6;
  }
  $left_time = $base_time - $spend_time;
  if($left_time < 0) $left_time = 0; //�ޥ��ʥ��ˤʤä��饼��ˤ���

  //���ۻ��֤η׻�
  $base_left_time = $silence ? $TIME_CONF->silence_pass : $left_time;
  return ConvertTime($full_time * $base_left_time * 60 * 60 / $base_time);
}

//-- �򿦴�Ϣ --//
//�����Ƚ���� (�����ƥ��å�����)
function InsertMediumMessage(){
  global $ROOM, $USERS;

  $flag = false; //����νи�Ƚ��
  $stack = array();
  foreach($USERS->rows as $user){
    $flag |= $user->IsRole('medium');
    if($user->suicide_flag){
      $stack[] = $USERS->GetHandleName($user->uname, true) . "\t" . $user->GetCamp();
    }
  }
  if($flag) foreach($stack as $str) $ROOM->SystemMessage($str, 'MEDIUM_RESULT');
}

//���ͤθ��ɤ������
function LoversFollowed($sudden_death = false){
  global $MESSAGE, $ROOM, $USERS;

  $cupid_list      = array(); //���塼�ԥåɤ�ID => ���ͤ�ID
  $lost_cupid_list = array(); //���ͤ���˴�������塼�ԥåɤΥꥹ��
  $checked_list    = array(); //�����ѥ��塼�ԥåɤ�ID

  foreach($USERS->rows as $user){ //���塼�ԥåɤȻ������ͤΥꥹ�Ȥ����
    if(! $user->IsLovers()) continue;
    foreach($user->partner_list['lovers'] as $id){
      $cupid_list[$id][] = $user->user_no;
      if(($user->dead_flag || $user->revive_flag) && ! in_array($id, $lost_cupid_list)){
	$lost_cupid_list[] = $id;
      }
    }
  }

  while(count($lost_cupid_list) > 0){ //�оݥ��塼�ԥåɤ�����н���
    $cupid_id = array_shift($lost_cupid_list);
    $checked_list[] = $cupid_id;
    foreach($cupid_list[$cupid_id] as $lovers_id){ //���塼�ԥåɤΥꥹ�Ȥ������ͤ� ID �����
      $user = $USERS->ById($lovers_id); //���ͤξ�������

      if($sudden_death){ //������ν���
	if(! $user->ToDead()) continue;
	$ROOM->Talk($user->handle_name . $MESSAGE->lovers_followed);
	$user->SaveLastWords();
      }
      elseif(! $USERS->Kill($user->user_no, 'LOVERS_FOLLOWED_' . $ROOM->day_night)){ //�̾����
	continue;
      }
      $user->suicide_flag = true;

      foreach($user->partner_list['lovers'] as $id){ //���ɤ��������ͤΥ��塼�ԥåɤ�ID�����
	if(! (in_array($id, $checked_list) || in_array($id, $lost_cupid_list))){ //Ϣ��Ƚ��
	  $lost_cupid_list[] = $id;
	}
      }
    }
  }
}

//���Ԥ�����å�
function CheckVictory($check_draw = false){
  global $GAME_CONF, $ROOM;

  $query_count = $ROOM->GetQuery(false, 'user_entry') . " AND live = 'live' AND user_no > 0 AND ";
  $human  = FetchResult($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')"); //¼��
  $wolf   = FetchResult($query_count . "role LIKE '%wolf%'"); //��ϵ
  $fox    = FetchResult($query_count . "role LIKE '%fox%'"); //�Ÿ�
  $lovers = FetchResult($query_count . "role LIKE '%lovers%'"); //����
  $quiz   = FetchResult($query_count . "role LIKE 'quiz%'"); //�����

  $victory_role = ''; //�����ر�
  if($wolf == 0 && $fox == 0 && $human == $quiz){ //����
    $victory_role = $quiz > 0 ? 'quiz' : 'vanish';
  }
  elseif($wolf == 0){ //ϵ����
    if($lovers > 1)  $victory_role = 'lovers';
    elseif($fox > 0) $victory_role = 'fox1';
    else             $victory_role = 'human';
  }
  elseif($wolf >= $human){ //¼����
    if($lovers > 1)  $victory_role = 'lovers';
    elseif($fox > 0) $victory_role = 'fox2';
    else             $victory_role = 'wolf';
  }
  elseif($human + $wolf + $fox == $lovers){ //��¸����������
    $victory_role = 'lovers';
  }
  elseif($ROOM->IsQuiz() && $quiz == 0){ //������¼ GM ��˴
    $victory_role = 'quiz_dead';
  }
  elseif($check_draw && $ROOM->GetVoteTimes() > $GAME_CONF->draw){ //����ʬ��
    $victory_role = 'draw';
  }

  if($victory_role == '') return false;

  //�����ཪλ
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame', " .
    "victory_role = '{$victory_role}', finish_time = NOW() WHERE room_no = {$ROOM->id}";
  SendQuery($query, true);
  //OutputSiteSummary(); //RSS��ǽ�ϥƥ�����
  return true;
}

//-- ��ɼ��Ϣ --//
//���ޤǤ���ɼ���������
function DeleteVote(){
  global $ROOM;

  $query = 'DELETE FROM vote' . $ROOM->GetQuery();
  if($ROOM->IsDay()){
    $query .= " AND situation = 'VOTE_KILL' AND vote_times = " . $ROOM->GetVoteTimes();
  }
  elseif($ROOM->IsNight()){
    $query .= ' AND situation <> ' . ($ROOM->date == 1 ? "'CUPID_DO'" : "'VOTE_KILL'");
  }
  SendQuery($query);
  SendQuery('OPTIMIZE TABLE vote', true);
}

//��μ�ʬ����ɼ�Ѥߥ����å�
function CheckSelfVoteNight($situation, $not_situation = ''){
  global $ROOM, $SELF;

  $query = $ROOM->GetQuery(true, 'vote') . ' AND ';
  if($situation == 'WOLF_EAT'){
    $query .= "situation = '{$situation}'";
  }
  elseif($not_situation != ''){
    $query .= "uname = '{$SELF->uname}' " .
      "AND(situation = '{$situation}' OR situation = '{$not_situation}')";
  }
  else{
    $query .= "uname = '{$SELF->uname}' AND situation = '{$situation}'";
  }
  return (FetchResult($query) > 0);
}

//-- ���ϴ�Ϣ --//
//HTML�إå�������
function OutputGamePageHeader(){
  global $SERVER_CONF, $GAME_CONF, $RQ_ARGS, $ROOM, $SELF;

  //�������Ǽ
  $url_header = 'game_frame.php?room_no=' . $ROOM->id . '&auto_reload=' . $RQ_ARGS->auto_reload;
  if($RQ_ARGS->play_sound) $url_header .= '&play_sound=on';
  if($RQ_ARGS->list_down)  $url_header .= '&list_down=on';

  $title = $SERVER_CONF->title . ' [�ץ쥤]';
  $anchor_header = '<br>'."\n";
  /*
    Mac �� JavaScript �ǥ��顼���Ǥ��֥饦�������ä������Υ�����
    ���ߤ� Safari��Firefox �Ǥ����פʤΤ� false �ǥ����åפ��Ƥ���
    //if(preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){
  */
  if(false){
    $sentence = '';
    $anchor_header .= '<a href="';
    $anchor_footer = '" target="_top">�����򥯥�å����Ƥ�������</a>';
  }
  else{
    $sentence = '<script type="text/javascript"><!--'."\n" .
      'if(top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $anchor_header .= '�ڤ��ؤ��ʤ��ʤ� <a href="';
    $anchor_footer = '" target="_top">����</a>';
  }

  //�������桢�������å⡼�ɤ˹Ԥ��Ȥ�
  if($ROOM->IsPlaying() && $SELF->IsDead() &&
     ! ($ROOM->log_mode || $ROOM->dead_mode || $ROOM->heaven_mode)){
    $jump_url =  $url_header . '&dead_mode=on';
    $sentence .= 'ŷ��⡼�ɤ��ڤ��ؤ��ޤ���';
  }
  elseif($ROOM->IsAfterGame() && $ROOM->dead_mode){ //�����ब��λ�������ä������Ȥ�
    $jump_url = $url_header;
    $sentence .= '�����ཪλ��Τ����������Ӥޤ���';
  }
  elseif($SELF->IsLive() && ($ROOM->dead_mode || $ROOM->heaven_mode)){
    $jump_url = $url_header;
    $sentence .= '��������̤����Ӥޤ���';
  }

  if($jump_url != ''){ //��ư�褬���ꤵ��Ƥ���������ڤ��ؤ�
    $sentence .= $anchor_header . $jump_url . $anchor_footer;
    OutputActionResult($title, $sentence, $jump_url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  if(! $ROOM->log_mode){ //����������������
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load = "change_css('{$ROOM->day_night}');";
  }

  if($RQ_ARGS->auto_reload != 0 && ! $ROOM->IsAfterGame()){ //��ư����ɤ򥻥å�
    echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
  }

  //�������桢�ꥢ�륿�������ʤ�в���֤� Javascript �ǥꥢ�륿����ɽ��
  if($ROOM->IsPlaying() && $ROOM->IsRealTime() && ! ($ROOM->log_mode || $ROOM->heaven_mode)){
    list($start_time, $end_time) = GetRealPassTime($left_time);
    $on_load .= 'output_realtime();';
    OutputRealTimer($start_time, $end_time);
  }
  echo '</head>'."\n" . '<body onLoad="' . $on_load . '">'."\n" .
    '<a name="#game_top"></a>'."\n";
}

//�ꥢ�륿����ɽ���˻Ȥ� JavaScript ���ѿ������
function OutputRealTimer($start_time, $end_time){
  global $ROOM;

  $sentence    = '��' . ($ROOM->IsDay() ? '����' : '������') . '�ޤ� ';
  $start_date  = GenerateJavaScriptDate($start_time);
  $end_date    = GenerateJavaScriptDate($end_time);
  $server_date = GenerateJavaScriptDate($ROOM->system_time);

  echo '<script type="text/javascript" src="javascript/output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var sentence = "' . $sentence . '";'."\n";
  echo "var end_date = {$end_date} * 1 + (new Date() - {$server_date});\n";
  echo "var diff_seconds = Math.floor(({$end_date} - {$start_date}) / 1000);\n";
  echo '// --></script>'."\n";
}

//JavaScript �� Date() ���֥������Ⱥ��������ɤ���������
function GenerateJavaScriptDate($time){
  $time_list = explode(',', TZDate('Y,m,j,G,i,s', $time));
  $time_list[1]--;  //JavaScript �� Date() �� Month �� 0 ���饹�����Ȥ���
  return 'new Date(' . implode(',', $time_list) . ')';
}

//��ư�����Υ�󥯤����
function OutputAutoReloadLink($url){
  global $GAME_CONF, $RQ_ARGS;

  $str = '[��ư����](' . $url . '0">' . ($RQ_ARGS->auto_reload == 0 ? '�ڼ�ư��' : '��ư') . '</a>';
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '��';
    $value = $RQ_ARGS->auto_reload == $time ? '��' . $name . '��' : $name;
    $str .= ' ' . $url . $time . '">' . $value . '</a>';
  }
  echo $str . ')'."\n";
}

//���ؤΥ�󥯤����
function OutputLogLink(){
  global $ROOM;

  $url = 'old_log.php?room_no=' . $ROOM->id;
  echo GenerateLogLink($url, '<br>' . ($ROOM->view_mode ? '[��]' : '[���Υ�]')) .
    GenerateLogLink($url . '&add_role=on', '<br>[��ɽ����]');
}

//�����४�ץ������������
function OutputGameOption(){
  global $ROOM;

  $query = "SELECT game_option, option_role, max_user FROM room WHERE room_no = {$ROOM->id}";
  extract(FetchAssoc($query, true));
  echo '<table class="time-table"><tr>'."\n" .
    '<td>�����४�ץ����' . GenerateGameOptionImage($game_option, $option_role) .
    ' ����' . $max_user . '��</td>'."\n" . '</tr></table>'."\n";
}

//���դ���¸�ԤοͿ������
function OutputTimeTable(){
  global $ROOM;

  if($ROOM->IsBeforeGame()) return false; //�����ब�ϤޤäƤ��ʤ����ɽ�����ʤ�

  $query = $ROOM->GetQuery(false, 'user_entry') . " AND live = 'live' AND user_no > 0";
  echo '<td>' . $ROOM->date . ' ����<span>(��¸��' . FetchResult($query) . '��)</span></td>'."\n";
}

//�ץ쥤�䡼��������
function OutputPlayerList(){
  global $DEBUG_MODE, $GAME_CONF, $ICON_CONF, $ROOM, $USERS, $SELF;

  //�����������������
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  //�֥饦��������å� (MSIE @ Windows ���� ������ Alt, Title °���ǲ��ԤǤ���)
  //IE �ξ����Ԥ� \r\n �����졢����¾�Υ֥饦���ϥ��ڡ����ˤ���(������Alt°��)
  $replace = preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ';

  //��������ե饰��Ƚ��
  $is_open_role = $ROOM->IsAfterGame() || $SELF->IsDummyBoy() ||
    ($SELF->IsDead() && $ROOM->IsOpenCast());

  $count = 0; //���ԥ�����Ȥ�����
  $str = '<div class="player"><table cellspacing="5"><tr>'."\n";
  foreach($USERS->rows as $id => $user){
    if($count > 0 && ($count % 5) == 0) $str .= "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;

    //�����೫����ɼ�򤷤Ƥ������طʿ����Ѥ���
    if($ROOM->IsBeforeGame() && ($user->IsDummyBoy(true) || isset($ROOM->vote[$user->uname]))){
      $td_header = '<td class="already-vote">';
    }
    else{
      $td_header = '<td>';
    }

    //�桼���ץ�ե�����������ο����ɲ�
    $profile = str_replace("\n", $replace, $user->profile);
    $str .= $td_header . '<img title="' . $profile . '" alt="' . $profile .
      '" style="border-color: ' . $user->color . ';"';

    //�������˱������������������
    $path = $ICON_CONF->path . '/' . $user->icon_filename;
    if($ROOM->IsBeforeGame() || $USERS->IsVirtualLive($id)){
      $live = '(��¸��)';
    }
    else{
      $live = '(��˴)';
      $str .= ' onMouseover="this.src=' . "'$path'" . '"'; //���Υ�������

      $path = $ICON_CONF->dead; //����������˴��������������ؤ�
      $str .= ' onMouseout="this.src=' . "'$path'" . '"';
    }
    $str .= ' width="' . $width . '" height="' . $height . '" src="' . $path . '"></td>'."\n";

    //HN ���ɲ�
    $str .= $td_header . '<font color="' . $user->color . '">��</font>' . $user->handle_name;
    if($DEBUG_MODE) $str .= ' (' . $id . ')';
    $str .= '<br>'."\n";

    //�����ཪλ�塦��˴�����򿦸����⡼�ɤʤ顢�򿦡��桼���͡����ɽ��
    if($is_open_role){
      $uname = str_replace(array('��', '��'), array('��<br>', '��<br>'), $user->uname); //�ȥ�å��б�
      $str .= '��(' . $uname; //�桼��̾���ɲ�

      //��;��֤ʤ���ͤ��Ƥ���桼�����ɲ�
      $real_user = $USERS->ByReal($id);
      if($real_user == $user) $real_user = $USERS->TraceExchange($id);
      if($real_user != $user && $real_user->IsLive()) $str .= '<br>[' . $real_user->uname . ']';
      $str .= ')<br>';

      //�ᥤ���򿦤��ɲ�
      if($user->IsRole('human', 'elder', 'saint', 'executor', 'escaper', 'suspect', 'unconscious'))
	$str .= GenerateRoleName($user->main_role, 'human');
      elseif($user->IsRoleGroup('mage') || $user->IsRole('voodoo_killer'))
	$str .= GenerateRoleName($user->main_role, 'mage');
      elseif($user->IsRoleGroup('necromancer') || $user->IsRole('medium'))
	$str .= GenerateRoleName($user->main_role, 'necromancer');
      elseif($user->IsRoleGroup('priest'))
	$str .= GenerateRoleName($user->main_role, 'priest');
      elseif($user->IsRoleGroup('guard') || $user->IsRole('reporter', 'anti_voodoo'))
	$str .= GenerateRoleName($user->main_role, 'guard');
      elseif($user->IsRoleGroup('common'))
	$str .= GenerateRoleName($user->main_role, 'common');
      elseif($user->IsRoleGroup('cat'))
	$str .= GenerateRoleName($user->main_role, 'cat');
      elseif($user->IsRoleGroup('assassin'))
	$str .= GenerateRoleName($user->main_role, 'assassin');
      elseif($user->IsRoleGroup('scanner'))
	$str .= GenerateRoleName($user->main_role, 'mind');
      elseif($user->IsRoleGroup('jealousy'))
	$str .= GenerateRoleName($user->main_role, 'jealousy');
      elseif($user->IsRoleGroup('doll'))
	$str .= GenerateRoleName($user->main_role, 'doll');
      elseif($user->IsRoleGroup('mania'))
	$str .= GenerateRoleName($user->main_role, 'mania');
      elseif($user->IsRoleGroup('wolf'))
	$str .= GenerateRoleName($user->main_role, 'wolf');
      elseif($user->IsRoleGroup('mad'))
	$str .= GenerateRoleName($user->main_role, 'mad');
      elseif($user->IsRoleGroup('fox'))
	$str .= GenerateRoleName($user->main_role, 'fox');
      elseif($user->IsRole('quiz'))
	$str .= GenerateRoleName($user->main_role);
      elseif($user->IsRoleGroup('cupid', 'angel'))
	$str .= GenerateRoleName($user->main_role, 'cupid');
      elseif($user->IsRoleGroup('chiroptera', 'fairy'))
	$str .= GenerateRoleName($user->main_role, 'chiroptera');
      elseif($user->IsRoleGroup('poison', 'pharmacist'))
	$str .= GenerateRoleName($user->main_role, 'poison');

      if(($role_count = count($user->role_list)) > 1){ //��Ǥ�򿦤�ɽ��
	$display_role_count = 1;
	foreach($GAME_CONF->sub_role_group_list as $class => $role_list){
	  foreach($role_list as $sub_role){
	    if($user->IsRole($sub_role)){
	      $str .= GenerateRoleName($sub_role, $class, true);
	      if(++$display_role_count >= $role_count) break 2;
	    }
	  }
	}
      }

      $str .= '<br>'."\n";
    }
    $str .= $live . '</td>'."\n";
  }
  echo $str . '</tr></table></div>'."\n";
}

//��̾�Υ������������
//1. User->GenerateShortRoleName() �Ȥ��б���ͤ���
//2. GenerateRoleNameList() @ game_vote_functions.php �Ȥ��б���ͤ���
function GenerateRoleName($role, $css = '', $sub_role = false){
  global $GAME_CONF;

  $str = '';
  if($css == '') $css = $role;
  if($sub_role) $str .= '<br>';
  $str .= '<span class="' . $css . '">[';
  if($sub_role) $str .= $GAME_CONF->sub_role_list[$role];
  else $str .= $GAME_CONF->main_role_list[$role];
  $str .= ']</span>';

  return $str;
}

//���Ԥν���
function OutputVictory(){
  global $VICT_MESS, $ROOM, $USERS, $SELF;

  //-- ¼�ξ��Է�� --//
  $victory = FetchResult("SELECT victory_role FROM room WHERE room_no = {$ROOM->id}");
  $class   = $victory;
  $winner  = $victory;

  switch($victory){ //�ü쥱�����б�
  //�ŸѾ�����
  case 'fox1':
  case 'fox2':
    $class = 'fox';
    break;

  //����ʬ����
  case 'draw': //����ʬ��
  case 'vanish': //����
  case 'quiz_dead': //������¼ GM ��˴
    $class = 'none';
    break;

  //��¼��
  case NULL:
    $class  = 'none';
    $winner = $ROOM->date > 0 ? 'unfinished' : 'none';
    break;
  }
  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$VICT_MESS->$winner}</td>
</tr></table>

EOF;

  //-- �ġ��ξ��Է�� --//
  //����̤���ꡢ����⡼�ɡ��������⡼�ɤʤ饹���å�
  if(is_null($victory) || $ROOM->view_mode || $ROOM->log_mode) return;

  $result = 'win';
  $camp = $SELF->GetCamp(true); //��°�رĤ����

  if($victory == 'draw' || $victory == 'vanish'){ //����ʬ����
    $class  = 'none';
    $result = 'draw';
  }
  elseif($victory == 'quiz_dead'){ //����Ի�˴
    $class  = 'none';
    $result = $camp == 'quiz' ? 'lose' : 'draw';
  }
  else{
    switch($camp){
    case 'fox':
      if(strpos($victory, $camp) !== false){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    case 'chiroptera':
      if($SELF->IsLive()){ //�����رĤ������Ƥ���о���
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    case 'human':
      if($SELF->IsRole('escaper') && $SELF->IsDead()){ //ƨ˴�Ԥϻ�˴���Ƥ���������
	$class  = 'none';
	$result = 'lose';
	break;
      }
      elseif($SELF->IsDoll()){ //�ͷ��ϤϿͷ���������¸���Ƥ���������
	foreach($USERS->rows as $user){
	  if($user->IsRole('doll_master') && $user->IsLive()){
	    $class  = 'none';
	    $result = 'lose';
	    break 2;
	  }
	}
      }

      if($victory == $camp){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;

    default:
      if($victory == $camp){
	$class = $camp;
      }
      else{
	$class  = 'none';
	$result = 'lose';
      }
      break;
    }
  }
  $result = 'self_' . $result;

  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$VICT_MESS->$result}</td>
</tr></table>

EOF;
}

//��ɼ�ν��׽���
function OutputVoteList(){
  global $ROOM;

  if(! $ROOM->IsPlaying()) return false; //��������ʳ��Ͻ��Ϥ��ʤ�

 //��ʤ���������ʤ�κ����ν��פ�ɽ��
  $set_date = ($ROOM->IsDay() && ! $ROOM->log_mode) ? $ROOM->date - 1 : $ROOM->date;
  echo GetVoteList($set_date);
}

//����ɼ�λ�����å�������ɽ��
function OutputRevoteList(){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $SELF, $COOKIE, $SOUND;

  if(! $ROOM->IsDay()) return false; //��ʳ��Ͻ��Ϥ��ʤ�
  if(($revote_times = $ROOM->GetVoteTimes(true)) == 0) return false; //����ɼ�β�������

  if($RQ_ARGS->play_sound && ! $ROOM->view_mode && $revote_times > $COOKIE->vote_times){
    $SOUND->Output('revote'); //�����Ĥ餹
  }

  //��ɼ�Ѥߥ����å�
  $vote_times = $revote_times + 1;
  $query = $ROOM->GetQuery(true, 'vote') . " AND vote_times = {$vote_times} " .
    "AND uname = '{$SELF->uname}'";
  if(FetchResult($query) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '��' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  echo GetVoteList($ROOM->date); //��ɼ��̤����
}

//���ꤷ�����դ���ɼ��̤���ɤ��� GenerateVoteList() ���Ϥ�
function GetVoteList($date){
  global $ROOM;

  //���ꤵ�줿���դ���ɼ��̤����
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$date} and type = 'VOTE_KILL'";
  return GenerateVoteList(FetchArray($query), $date);
}

//��ɼ�ǡ��������̤���������
function GenerateVoteList($raw_data, $date){
  global $RQ_ARGS, $ROOM, $SELF;

  if(count($raw_data) < 1) return NULL; //��ɼ���

  //��ɼ������Ƚ��
  $is_open_vote = ($ROOM->IsFinished() || $ROOM->test_mode ||
		   ($ROOM->IsOption('open_vote') ? true :
		    ($SELF->IsDead() && $ROOM->IsOpenCast())));

  $table_stack = array();
  $header = '<td class="vote-name">';
  foreach($raw_data as $raw){ //������ɼ�ǡ����Υѡ���
    list($handle_name, $target_name, $voted_number,
	 $vote_number, $vote_times) = explode("\t", $raw);

    $stack = array('<tr>' .  $header . $handle_name, '<td>' . $voted_number . ' ɼ',
		   '<td>��ɼ��' . ($is_open_vote ? ' ' . $vote_number . ' ɼ' : '') . ' ��',
		   $header . $target_name, '</tr>');
    $table_stack[$vote_times][] = implode('</td>', $stack);
  }

  if(! $RQ_ARGS->reverse_log) krsort($table_stack); //����ʤ��ž������

  $str = '';
  $header = '<tr><td class="vote-times" colspan="4">' . $date . ' ���� ( ';
  $footer = ' ����)</td>';
  foreach($table_stack as $vote_times => $stack){
    array_unshift($stack, '<table class="vote-list">', $header . $vote_times . $footer);
    $stack[] = '</table>';
    $str .= implode("\n", $stack);
  }
  return $str;
}

//���å�����
function OutputTalkLog(){
  global $ROOM;

  $builder =& new DocumentBuilder();
  $builder->BeginTalk('talk');
  $talk_list = $ROOM->LoadTalk();
  foreach($talk_list as $talk) OutputTalk($talk, $builder); //���ý���
  OutputTimeStamp($builder);
  $builder->EndTalk();
}

//���ý���
function OutputTalk($talk, &$builder){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  //PrintData($talk);
  //ȯ���桼�������
  /*
    $uname ��ɬ�� $talk ����������뤳�ȡ�
    $USERS �ˤϥ����ƥ�桼���� 'system' ��¸�ߤ��ʤ����ᡢ$said_user �Ͼ�� NULL �ˤʤäƤ��롣
  */
  $said_user = $talk->scene == 'heaven' ? $USERS->ByUname($talk->uname) :
    $USERS->ByVirtualUname($talk->uname);

  //���ܥѥ�᡼�������
  $symbol      = '<font color="' . $said_user->color . '">��</font>';
  $handle_name = $said_user->handle_name;
  $sentence    = $talk->sentence;
  $font_type   = $talk->font_type;

  //���ۥ桼�������
  $virtual_self = $builder->actor;
  if($RQ_ARGS->add_role && $said_user->user_no > 0){ //��ɽ���⡼���б�
    $real_user = $talk->scene == 'heaven' ? $said_user : $USERS->ByReal($said_user->user_no);
    $handle_name .= $real_user->GenerateShortRoleName($talk->scene == 'heaven');
  }
  else{
    $real_user = $USERS->ByRealUname($talk->uname);
  }

  //[���ȥ�� or ������ or ���ļ�] Ƚ��
  $is_mind_read = $builder->flag->mind_read &&
    (($said_user->IsPartner('mind_read', $virtual_self->user_no) &&
      ! $said_user->IsRole('unconscious')) ||
     $virtual_self->IsPartner('mind_receiver', $said_user->user_no) ||
     $said_user->IsPartner('mind_friend', $virtual_self->partner_list));

  $flag_mind_read = $is_mind_read ||
    ($ROOM->date > 1 && ($said_user->IsRole('mind_open') ||
			 ($builder->flag->common && $said_user->IsRole('whisper_scanner')) ||
			 ($builder->flag->wolf   && $said_user->IsRole('howl_scanner')) ||
			 ($builder->flag->fox    && $said_user->IsRole('telepath_scanner')))) ||
    ($real_user->IsRole('possessed_wolf') && $builder->flag->wolf) ||
    ($real_user->IsRole('possessed_mad') && $said_user->IsSame($virtual_self->uname)) ||
    ($real_user->IsRole('possessed_fox') && $builder->flag->fox);

  //ȯ��ɽ���ե饰Ƚ��
  $flag_dummy_boy = $builder->flag->dummy_boy;
  $flag_common    = $builder->flag->common || $flag_mind_read;
  $flag_wolf      = $builder->flag->wolf   || $flag_mind_read;
  $flag_fox       = $builder->flag->fox    || $flag_mind_read;
  $flag_open_talk = $builder->flag->open_talk;

  if($talk->type == 'system' && isset($talk->action)){ //��ɼ����
    /*
      + �����೫��������ɼ (KICK ��) �Ͼ��ɽ��
      + �ְ۵ġפ���Ͼ��ɽ��
    */
    switch($talk->action){
    case 'OBJECTION':
      $builder->AddSystemMessage('objection-' . $said_user->sex, $handle_name . $sentence);
      break;

    case 'GAMESTART_DO':
      break;

    default:
      if($ROOM->IsBeforeGame() || $flag_open_talk){
	$builder->AddSystemMessage($talk->class, $handle_name . $sentence);
      }
      break;
    }
    return;
  }

  if($talk->uname == 'system'){ //�����ƥ��å�����
    $builder->AddSystemTalk($sentence);
    return;
  }

  if($talk->type == 'dummy_boy'){ //�����귯���ѥ����ƥ��å�����
    $builder->AddSystemTalk($sentence, 'dummy-boy');
    return;
  }

  switch($talk->scene){
  case 'night':
    if($flag_open_talk){
      $talk_class = '';
      switch($talk->type){
      case 'self_talk':
	$handle_name .= '<span>���Ȥ��</span>';
	$talk_class = 'night-self-talk';
	break;

      case 'wolf':
	$handle_name .= '<span>(��ϵ)</span>';
	$talk_class = 'night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'mad':
	$handle_name .= '<span>(�񤭶���)</span>';
	$talk_class = 'night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'common':
	$handle_name .= '<span>(��ͭ��)</span>';
	$talk_class = 'night-common';
	$font_type  .= ' night-common';
	break;

      case 'fox':
	$handle_name .= '<span>(�Ÿ�)</span>';
	$talk_class = 'night-fox';
	$font_type  .= ' night-fox';
	break;
      }
      $builder->RawAddTalk($symbol, $handle_name, $sentence, $font_type, '', $talk_class);
    }
    else{
      switch($talk->type){
      case 'wolf': //��ϵ
	if($flag_wolf){
	  $builder->AddTalk($said_user, $talk);
	}
	else{
	  $builder->AddWhisper('wolf', $talk);
	}
	break;

      case 'mad': //�񤭶���
	if($flag_wolf) $builder->AddTalk($said_user, $talk);
	break;

      case 'common': //��ͭ��
	if($flag_common){
	  $builder->AddTalk($said_user, $talk);
	}
	else{
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'fox': //�Ÿ�
	if($flag_fox){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($SELF->IsRole('wise_wolf')){
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'self_talk': //�Ȥ��
	if($virtual_self->IsSame($talk->uname) || $flag_dummy_boy || $flag_mind_read){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($said_user->IsLonely('wolf')){
	  $builder->AddWhisper('wolf', $talk); //��Ω����ϵ���Ȥ���ϱ��ʤ��˸�����
	}
	break;
      }
    }
    break;

  case 'heaven':
    if(! $flag_open_talk) return;
    $builder->RawAddTalk($symbol, $handle_name, $sentence, $font_type, $talk->scene);
    break;

  default:
    $builder->AddTalk($said_user, $talk);
    break;
  }
}

//[¼Ω�� / �����೫�� / �����ཪλ] ��������
function OutputTimeStamp($builder){
  global $ROOM;

  $talk =& new Talk();
  $query = ' FROM room' . $ROOM->GetQuery(false);
  if($ROOM->IsBeforeGame()){ //¼Ω�ƻ�����������ɽ��
    $time = FetchResult('SELECT establish_time' . $query);
    $talk->sentence = '¼����';
  }
  elseif($ROOM->IsNight() && $ROOM->date == 1){ //�����೫�ϻ�����������ɽ��
    $time = FetchResult('SELECT start_time' . $query);
    $talk->sentence = '�����೫��';
  }
  elseif($ROOM->IsAfterGame()){ //�����ཪλ������������ɽ��
    $time = FetchResult('SELECT finish_time' . $query);
    $talk->sentence = '�����ཪλ';
  }

  if(is_null($time)) return false;
  $talk->uname = 'system';
  $talk->sentence .= '��' . ConvertTimeStamp($time);
  $talk->ParseLocation($ROOM->day_night . ' system');
  OutputTalk($talk, $builder);
}

//�ꤦ��ϵ����������Ҥ�������ǽ�Ϥ�Ȥ���å�����
function OutputAbilityAction(){
  global $MESSAGE, $ROOM, $SELF;

  //��֤��򿦸��������Ĥ���Ƥ���Ȥ��Τ�ɽ��
  //(ǭ�����򿦸������Ϲ�ư�Ǥ��ʤ��Τ�����)
  if(! $ROOM->IsDay() || ! ($SELF->IsDummyBoy() || $ROOM->IsOpenCast())) return false;

  $yesterday = $ROOM->date - 1;
  $header = '<b>�������롢';
  $footer = '</b><br>'."\n";
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  if($yesterday == 1){
    array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list, 'ESCAPE_DO', 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO',
	       'DREAM_EAT', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
	       'POSSESSED_DO', 'POSSESSED_NOT_DO');
  }

  $action = '';
  foreach($action_list as $this_action){
    if($action != '') $action .= ' OR ';
    $action .= "type = '$this_action'";
  }

  $query = "SELECT message AS sentence, type FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$yesterday} AND ( {$action} )";
  $message_list = FetchAssoc($query);
  foreach($message_list as $array){
    extract($array);
    list($actor, $target) = explode("\t", $sentence);
    echo $header.$actor.' ';
    switch($type){
    case 'WOLF_EAT':
    case 'DREAM_EAT':
    case 'POSSESSED_DO':
    case 'ASSASSIN_DO':
      echo '�� '.$target.' �������ޤ���';
      break;

    case 'ESCAPE_DO':
      echo '�� '.$target.' '.$MESSAGE->escape_do;
      break;

    case 'MAGE_DO':
    case 'CHILD_FOX_DO':
      echo '�� '.$target.' ���ꤤ�ޤ���';
      break;

    case 'VOODOO_KILLER_DO':
      echo '�� '.$target.' �μ�����㱤��ޤ���';
      break;

    case 'JAMMER_MAD_DO':
      echo '�� '.$target.' ���ꤤ��˸�����ޤ���';
      break;

    case 'TRAP_MAD_DO':
      echo '�� '.$target.' '.$MESSAGE->trap_do;
      break;

    case 'TRAP_MAD_NOT_DO':
      echo $MESSAGE->trap_not_do;
      break;

    case 'POSSESSED_NOT_DO':
      echo $MESSAGE->possessed_not_do;
      break;

    case 'VOODOO_MAD_DO':
    case 'VOODOO_FOX_DO':
      echo '�� '.$target.' �˼����򤫤��ޤ���';
      break;

    case 'GUARD_DO':
      echo '�� '.$target.' '.$MESSAGE->guard_do;
      break;

    case 'ANTI_VOODOO_DO':
      echo '�� '.$target.' �����㱤��ޤ���';
      break;

    case 'REPORTER_DO':
      echo '�� '.$target.' '.$MESSAGE->reporter_do;
      break;

    case 'ASSASSIN_NOT_DO':
      echo $MESSAGE->assassin_not_do;
      break;

    case 'MIND_SCANNER_DO':
      echo '�� '.$target.' �ο����ɤߤޤ���';
      break;

    case 'CUPID_DO':
      echo '�� '.$target.' '.$MESSAGE->cupid_do;
      break;

    case 'FAIRY_DO':
      echo '�� '.$target.' '.$MESSAGE->fairy_do;;
      break;

    case 'MANIA_DO':
      echo '�� '.$target.' �򿿻����ޤ���';
      break;
    }
    echo $footer;
  }
}

//��˴�Ԥΰ�������
function OutputLastWords(){
  global $MESSAGE, $ROOM;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! ($ROOM->IsPlaying() || $ROOM->log_mode)) return false;

  //�����λ�˴�԰�������
  $set_date = $ROOM->date - 1;
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$set_date} AND type = 'LAST_WORDS' ORDER BY RAND()";
  $array = FetchArray($query);
  if(count($array) < 1) return false;

  echo <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">

EOF;

  foreach($array as $result){
    list($handle_name, $sentence) = explode("\t", $result, 2);
    LineToBR(&$sentence);

    echo <<<EOF
<tr>
<td class="lastwords-title">{$handle_name}<span>����ΰ��</span></td>
<td class="lastwords-body">{$sentence}</td>
</tr>

EOF;
  }
  echo '</table>'."\n";
}

//�������� ϵ�����٤����Ѥ�����ƻ�˴����ɼ��̤ǻ�˴�Υ�å�����
function OutputDeadMan(){
  global $ROOM;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! $ROOM->IsPlaying()) return false;

  $yesterday = $ROOM->date - 1;

  //���̥�����
  $query_header = "SELECT message, type FROM system_message WHERE room_no = {$ROOM->id} AND date =";

  //��˴�����ץꥹ��
  $dead_type_list = array(
    'day' => array('VOTE_KILLED' => true, 'POISON_DEAD_day' => true,
		   'LOVERS_FOLLOWED_day' => true, 'SUDDEN_DEATH_%' => false),

    'night' => array('WOLF_KILLED' => true, 'HUNGRY_WOLF_KILLED' => true, 'POSSESSED' => true,
		     'POSSESSED_TARGETED' => true, 'POSSESSED_RESET' => true,
		     'DREAM_KILLED' => true, 'TRAPPED' => true, 'CURSED' => true, 'FOX_DEAD' => true,
		     'HUNTED' => true, 'REPORTER_DUTY' => true, 'ASSASSIN_KILLED' => true,
		     'PRIEST_RETURNED' => true, 'POISON_DEAD_night' => true,
		     'LOVERS_FOLLOWED_night' => true, 'REVIVE_%' => false, 'SACRIFICE' => true));

  foreach($dead_type_list as $scene => $action_list){
    $query_list = array();
    foreach($action_list as $action => $type){
      $query_list[] = 'type ' . ($type ? '=' : 'LIKE') . " '{$action}'";
    }
    $type_list->$scene = implode(' OR ', $query_list);
  }

  if($ROOM->IsDay()){
    $set_date = $yesterday;
    $type = $type_list->night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_list->day;
  }

  $array = FetchAssoc("{$query_header} {$set_date} AND ( {$type} ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }

  //�������⡼�ɰʳ��ʤ���������˴�ԥ�å�����ɽ��
  if($ROOM->log_mode) return;
  $set_date = $yesterday;
  if($set_date < 2) return;
  $type = $type_list->{$ROOM->day_night};

  echo '<hr>'; //��Ԥ�̵���Ȥ��˶�����������ʤ����ͤˤ������ $array ����Ȥ�����å�����
  $array = FetchAssoc("{$query_header} {$set_date} AND ( {$type} ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }
}

//��ԤΥ������̤˻�˴��å����������
function OutputDeadManType($name, $type){
  global $MESSAGE, $ROOM, $SELF;

  $deadman_header = '<tr><td>'.$name.' '; //���ܥ�å������إå�
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //���ܥ�å�����
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //�ɲö��̥إå�
  $open_reason = $ROOM->IsFinished() || $SELF->IsDummyBoy() ||
    ($SELF->IsDead() && $ROOM->IsOpenCast());
  $show_reason = $open_reason || ($SELF->IsRole('yama_necromancer') && $SELF->IsLive());

  echo '<table class="dead-type">'."\n";
  switch($type){
  case 'VOTE_KILLED':
    echo '<tr class="dead-type-vote">';
    echo '<td>'.$name.' '.$MESSAGE->vote_killed.'</td>';
    break;

  case 'POISON_DEAD_day':
  case 'POISON_DEAD_night':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->poison_dead.')</td>';
    break;

  case 'LOVERS_FOLLOWED_day':
  case 'LOVERS_FOLLOWED_night':
    echo '<tr class="dead-type-lovers">';
    echo '<td>'.$name.' '.$MESSAGE->lovers_followed.'</td>';
    break;

  case 'REVIVE_SUCCESS':
    echo '<tr class="dead-type-revive">';
    echo '<td>'.$name.' '.$MESSAGE->revive_success.'</td>';
    break;

  case 'REVIVE_FAILED':
    if($ROOM->IsFinished() || $SELF->IsDead()){
      echo '<tr class="dead-type-revive">';
      echo '<td>'.$name.' '.$MESSAGE->revive_failed.'</td>';
    }
    break;

  case 'POSSESSED_TARGETED':
    if($open_reason) echo '<tr><td>'.$name.' '.$MESSAGE->possessed_targeted.'</td>';
    break;

  case 'SUDDEN_DEATH_CHICKEN':
  case 'SUDDEN_DEATH_RABBIT':
  case 'SUDDEN_DEATH_PERVERSENESS':
  case 'SUDDEN_DEATH_FLATTERY':
  case 'SUDDEN_DEATH_IMPATIENCE':
  case 'SUDDEN_DEATH_NERVY':
  case 'SUDDEN_DEATH_CELIBACY':
  case 'SUDDEN_DEATH_PANELIST':
  case 'SUDDEN_DEATH_JEALOUSY':
  case 'SUDDEN_DEATH_AGITATED':
  case 'SUDDEN_DEATH_FEBRIS':
  case 'SUDDEN_DEATH_WARRANT':
  case 'SUDDEN_DEATH_CHALLENGE':
    echo '<tr class="dead-type-sudden-death">';
    echo '<td>'.$name.' '.$MESSAGE->vote_sudden_death.'</td>';
    if($show_reason){
      $action = strtolower(array_pop(explode('_', $type)));
      echo $reason_header.$MESSAGE->$action.')</td>';
    }
    break;

  default:
    echo $deadman;
    if($show_reason){
      $action = strtolower($type);
      echo $reason_header.$MESSAGE->$action.')</td>';
    }
    break;
  }
  echo "</tr>\n</table>\n";
}
