<?php
//HTML�إå�������
function OutputGamePageHeader(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $SELF;

  //�������Ǽ
  $url_header = 'game_frame.php?room_no=' . $ROOM->id . '&auto_reload=' . $RQ_ARGS->auto_reload;
  if($RQ_ARGS->play_sound) $url_header .= '&play_sound=on';
  if($RQ_ARGS->list_down)  $url_header .= '&list_down=on';

  $title = '��Ͽ�ϵ�ʤ�䡩 [�ץ쥤]';
  $anchor_header = '<br>'."\n";
  if(preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){ //MAC ���ɤ���Ƚ��
    $sentence = '';  //MAC �� JavaScript �ǥ��顼��
    $anchor_header .= '<a href="';
    $anchor_footer = '">�����򥯥�å����Ƥ�������</a>';
  }
  else{
    $sentence = '<script type="text/javascript"><!--'."\n" .
      'if(top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $anchor_header .= '�ڤ��ؤ��ʤ��ʤ� <a href="';
    $anchor_footer = '" target="_top">����</a>';
  }

  //�������桢�������å⡼�ɤ˹Ԥ��Ȥ�
  if(! $ROOM->IsAfterGame() && $SELF->IsDead() && ! $ROOM->log_mode &&
     ! $ROOM->dead_mode && ! $ROOM->heaven_mode){
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
  if($ROOM->IsPlaying() && $ROOM->IsRealTime() && ! $ROOM->heaven_mode && ! $ROOM->log_mode){
    list($start_time, $end_time) = GetRealPassTime(&$left_time, true);
    $on_load .= 'output_realtime();';
    OutputRealTimer($start_time, $end_time);
  }
  echo '</head>'."\n";
  echo '<body onLoad="' . $on_load . '">'."\n";
  echo '<a name="#game_top"></a>'."\n";
}

//�ꥢ�륿����ɽ���˻Ȥ� JavaScript ���ѿ������
function OutputRealTimer($start_time, $end_time){
  global $ROOM;

  echo '<script type="text/javascript" src="javascript/output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var realtime_message = "��' . ($ROOM->IsDay() ? '����' : '������') . '�ޤ� ";'."\n";
  echo 'var start_time = "' . $start_time . '";'."\n";
  echo 'var end_time = "'   . $end_time   . '";'."\n";
  echo '// --></script>'."\n";
}

//��ư�����Υ�󥯤����
function OutputAutoReloadLink($url){
  global $GAME_CONF, $RQ_ARGS;

  $str = '[��ư����](' . $url . '0">' . ($RQ_ARGS->auto_reload == 0 ? '�ڼ�ư��' : '��ư') . '</a>';
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '��';
    $value = ($RQ_ARGS->auto_reload == $time ? '��' . $name . '��' : $name);
    $str .= ' ' . $url . $time . '">' . $value . '</a>';
  }
  echo $str . ')'."\n";
}

//�����४�ץ������������
function OutputGameOption(){
  global $GAME_CONF, $ROOM;

  $array = FetchNameArray("SELECT option_role, max_user FROM room WHERE room_no = {$ROOM->id}");
  $str = '<table class="time-table"><tr>'."\n" .
    '<td>�����४�ץ����' . MakeGameOptionImage($ROOM->game_option, $array['option_role']) .
    ' ����' . $array['max_user'] . '��</td>'."\n" . '</tr></table>'."\n";
  echo $str;
}

//���դ���¸�ԤοͿ������
function OutputTimeTable(){
  global $ROOM;

  if($ROOM->IsBeforeGame()) return false; //�����ब�ϤޤäƤ��ʤ����ɽ�����ʤ�

  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} " .
    "AND live = 'live' AND user_no > 0";
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
  $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');

  $count = 0; //���ԥ�����Ȥ�����

  //��������ե饰������
  $is_open_role = ($ROOM->IsAfterGame() || $SELF->IsDummyBoy() ||
		   ($SELF->IsDead() && $ROOM->IsOpenCast()));

  //�����೫����ɼ�����å��ѥ�����
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
    "AND situation = 'GAMESTART' AND uname = ";

  $str = '<div class="player"><table cellspacing="5"><tr>'."\n";
  foreach($USERS->rows as $user_no => $user){
    if($count > 0 && ($count % 5) == 0) $str .= "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;

    //�����೫����ɼ�򤷤Ƥ������طʿ����Ѥ���
    $td_header = '<td>';
    if($ROOM->IsBeforeGame() &&
       (($user->IsDummyBoy() && ! $ROOM->IsQuiz()) ||
	FetchResult($query . "'{$user->uname}'") > 0)){
      $td_header = '<td class="already-vote">';
    }

    //�桼���ץ�ե�����������ο����ɲ�
    $profile = str_replace("\n", $replace, $user->profile);
    $str .= $td_header . '<img title="' . $profile . '" alt="' . $profile .
      '" style="border-color: ' . $user->color . ';"';

    //�������˱������������������
    $path = $ICON_CONF->path . '/' . $user->icon_filename;
    if($ROOM->IsBeforeGame() || $USERS->IsVirtualLive($user_no)){
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
    if($DEBUG_MODE) $str .= ' (' . $user_no . ')';
    $str .= '<br>'."\n";

    //�����ཪλ�塦��˴�����򿦸����⡼�ɤʤ顢�򿦡��桼���͡����ɽ��
    if($is_open_role){
      $str .= '��(' . $user->uname; //�桼��̾���ɲ�

      //��;��֤ʤ���ͤ��Ƥ���桼�����ɲ�
      $real_user = $USERS->ByReal($user_no);
      if(! $real_user->IsSameID($user_no) && $real_user->IsLive()){
	$str .= '<br>[' . $real_user->uname . ']';
      }
      $str .= ')<br>';

      //�ᥤ���򿦤��ɲ�
      if($user->IsRole('human', 'suspect', 'unconscious'))
	$str .= MakeRoleName($user->main_role, 'human');
      elseif($user->IsRoleGroup('mage') || $user->IsRole('voodoo_killer'))
	$str .= MakeRoleName($user->main_role, 'mage');
      elseif($user->IsRoleGroup('necromancer', 'priest') || $user->IsRole('medium'))
	$str .= MakeRoleName($user->main_role, 'necromancer');
      elseif($user->IsRoleGroup('guard') || $user->IsRole('reporter', 'anti_voodoo'))
	$str .= MakeRoleName($user->main_role, 'guard');
      elseif($user->IsRoleGroup('common'))
	$str .= MakeRoleName($user->main_role, 'common');
      elseif($user->IsRole('mind_scanner'))
	$str .= MakeRoleName($user->main_role, 'mind');
      elseif($user->IsRoleGroup('jealousy'))
	$str .= MakeRoleName($user->main_role, 'jealousy');
      elseif($user->IsRoleGroup('mania'))
	$str .= MakeRoleName($user->main_role, 'mania');
      elseif($user->IsRole('assassin', 'quiz'))
	$str .= MakeRoleName($user->main_role);
      elseif($user->IsRoleGroup('wolf'))
	$str .= MakeRoleName($user->main_role, 'wolf');
      elseif($user->IsRoleGroup('mad'))
	$str .= MakeRoleName($user->main_role, 'mad');
      elseif($user->IsRoleGroup('fox'))
	$str .= MakeRoleName($user->main_role, 'fox');
      elseif($user->IsRoleGroup('chiroptera'))
	$str .= MakeRoleName($user->main_role, 'chiroptera');
      elseif($user->IsRoleGroup('cupid'))
	$str .= MakeRoleName($user->main_role, 'cupid');
      elseif($user->IsRoleGroup('poison') || $user->IsRole('pharmacist'))
	$str .= MakeRoleName($user->main_role, 'poison');

      if(($role_count = count($user->role_list)) > 1){ //��Ǥ�򿦤�ɽ��
	$display_role_count = 1;
	foreach($GAME_CONF->sub_role_group_list as $class => $role_list){
	  foreach($role_list as $sub_role){
	    if($user->IsRole($sub_role)){
	      $str .= MakeRoleName($sub_role, $class, true);
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

//��̾�Υ������������ //game_format.php �˻����褦�ʴؿ������뤫�ʡ�
function MakeRoleName($role, $css = '', $sub_role = false){
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
  global $MESSAGE, $ROOM, $USERS, $SELF;

  //-- ¼�ξ��Է�� --//
  $victory = FetchResult("SELECT victory_role FROM room WHERE room_no = {$ROOM->id}");
  $class   = $victory;
  $winner  = 'victory_' . $victory;

  switch($victory){ //�ü쥱�����б�
    //�Ѿ�����
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

    case NULL: //��¼
      $class  = 'none';
      $winner = 'victory_none';
      break;
  }
  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$MESSAGE->$winner}</td>
</tr></table>

EOF;

  //-- �ġ��ξ��Է�� --//
  //����̤���ꡢ����⡼�ɡ��������⡼�ɤʤ���ɽ��
  if(is_null($victory) || $ROOM->view_mode || $ROOM->log_mode) return;

  $result = 'win';
  $target_user = $SELF;
  while($target_user->IsRole('unknown_mania')){
    if(! is_array($target_user->partner_list['unknown_mania'])) break;
    $target_user = $USERS->ByID($target_user->partner_list['unknown_mania'][0]);
    if($target_user->IsSelf()) break;
  }
  $camp = $target_user->DistinguishCamp(); //��°�رĤ����

  if($victory == 'draw' || $victory == 'vanish'){ //����ʬ����
    $class  = 'none';
    $result = 'draw';
  }
  elseif($victory == 'quiz_dead'){ //����Ի�˴
    $class  = 'none';
    $result = ($camp == 'quiz' ? 'lose' : 'draw');
  }
  elseif($camp == 'chiroptera' && $SELF->IsLive()){ //�����رĤ������Ƥ���о���
    $class = 'chiroptera';
  }
  else{
    if($SELF->IsLovers()) $camp = 'lovers'; //���ͤʤ��°�رĤ���

    if($victory == 'human' && $camp == 'human')
      $class = 'human';
    elseif($victory == 'wolf' && $camp == 'wolf')
      $class = 'wolf';
    elseif(strpos($victory, 'fox') !== false && $camp == 'fox')
      $class = 'fox';
    elseif($victory == 'lovers' && $camp == 'lovers')
      $class = 'lovers';
    elseif($victory == 'quiz' && $camp == 'quiz')
      $class = 'quiz';
    else{
      $class  = 'none';
      $result = 'lose';
    }
  }

  echo <<<EOF
<table class="victory victory-{$class}"><tr>
<td>{$MESSAGE->$result}</td>
</tr></table>

EOF;
}

//����ɼ�λ�����å�������ɽ��
function OutputReVoteList(){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $SELF, $COOKIE, $SOUND;

  if(! $ROOM->IsDay()) return false; //��ʳ��Ͻ��Ϥ��ʤ�
  if(($revote_times = GetVoteTimes(true)) == 0) return false; //����ɼ�β�������

  if($RQ_ARGS->play_sound && ! $ROOM->view_mode && $revote_times > $COOKIE->vote_times){
    $SOUND->Output('revote'); //�����Ĥ餹
  }

  //��ɼ�Ѥߥ����å�
  $vote_times = $revote_times + 1;
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  if(FetchResult($query) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '��' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  OutputVoteListDay($ROOM->date); //��ɼ��̤����
}

//���å�����
function OutputTalkLog(){
  global $MESSAGE, $ROOM, $SELF;

  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = {$ROOM->id} AND location LIKE '{$ROOM->day_night}%'
			AND date = {$ROOM->date} ORDER BY time DESC");

  $builder = DocumentBuilder::Generate();
  $builder->BeginTalk('talk');
  while(($row = mysql_fetch_object($sql, 'Talk')) !== false){
    OutputTalk($row, $builder); //���ý���
  }
  if($ROOM->IsBeforeGame()){ //¼Ω�ƻ�����������ɽ��
    $time = FetchResult("SELECT establish_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = '¼����';
  }
  elseif($ROOM->IsNight() && $ROOM->date == 1){ //�����೫�ϻ�����������ɽ��
    $time = FetchResult("SELECT start_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = '�����೫��';
  }
  elseif($ROOM->IsAfterGame()){ //�����ཪλ������������ɽ��
    $time = FetchResult("SELECT finish_time FROM room WHERE room_no = {$ROOM->id}");
    $row->sentence = '�����ཪλ';
  }
  if($time != ''){
    $row->uname = 'system';
    $row->sentence .= '��' . ConvertTimeStamp($time);
    $row->location = $ROOM->day_night . 'system';
    OutputTalk($row, $builder);
  }
  $builder->EndTalk();
}

//���ý���
function OutputTalk($talk, &$builder){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $said_user = $USERS->ByVirtualUname($talk->uname);
  $real_user = $USERS->ByRealUname($talk->uname);
  $virtual_self = $USERS->ByVirtual($SELF->user_no);
  /*
    $talk_uname ��ɬ�� $talk ����������뤳�ȡ�
    $USERS �ˤϥ����ƥ�桼���� 'system' ��¸�ߤ��ʤ����ᡢ$said_user �Ͼ�� NULL �ˤʤäƤ��롣
  */
  $talk_uname = $talk->uname;
  $talk_handle_name = $said_user->handle_name;
  $talk_sex         = $said_user->sex;
  $talk_color       = $said_user->color;
  $sentence         = $talk->sentence;
  $font_type        = $talk->font_type;
  $location         = $talk->location;

  if($RQ_ARGS->add_role && $said_user->user_no > 0){ //��ɽ���⡼���б�
    if(strpos($location, 'heaven') === false)
      $real_user = $USERS->ByReal($said_user->user_no);
    else
      $real_user = $said_user;
    $talk_handle_name .= $real_user->MakeShortRoleName();
  }
  LineToBR($sentence); //���ԥ����ɤ� <br> ���Ѵ�

  //��ɼ���������å�
  $vote_action_list = array(
    'vote' => 'VOTE_DO',
    'wolf' => 'WOLF_EAT',
    'mage' => 'MAGE_DO', 'voodoo_killer' => 'VOODOO_KILLER_DO',
    'jammer_mad' => 'JAMMER_MAD_DO', 'voodoo_mad' => 'VOODOO_MAD_DO', 'dream_eat' => 'DREAM_EAT',
    'trap_mad' => 'TRAP_MAD_DO', 'not_trap_mad' => 'TRAP_MAD_NOT_DO',
    'guard' => 'GUARD_DO', 'reporter' => 'REPORTER_DO', 'anti_voodoo' => 'ANTI_VOODOO_DO',
    'poison_cat' => 'POISON_CAT_DO', 'not_poison_cat' => 'POISON_CAT_NOT_DO',
    'assassin' => 'ASSASSIN_DO', 'not_assassin' => 'ASSASSIN_NOT_DO',
    'mind_scanner' => 'MIND_SCANNER_DO',
    'voodoo_fox' => 'VOODOO_FOX_DO', 'child_fox' => 'CHILD_FOX_DO',
    'cupid' => 'CUPID_DO',
    'mania' => 'MANIA_DO');
  $flag_vote_action = false;
  foreach($vote_action_list as $this_role => $this_action){
    $flag_action->$this_role = (strpos($sentence, $this_action) === 0);
    $flag_vote_action |= $flag_action->$this_role;
  }

  $location_system = (strpos($location, 'system') !== false);
  $flag_system = ($location_system && $flag_vote_action && ! $ROOM->IsFinished());
  $flag_live_night = ($SELF->IsLive() && $ROOM->IsNight() && ! $ROOM->IsFinished());
  $flag_wolf_group = ($SELF->IsWolf(true) || $virtual_self->IsRole('whisper_mad') ||
		      $SELF->IsDummyBoy());
  $flag_fox_group  = ($virtual_self->IsFox(true) || $SELF->IsDummyBoy());
  $flag_mind_read  = (($ROOM->date > 1 && $SELF->IsLive() &&
		       (($said_user->IsPartner('mind_read', $virtual_self->user_no) &&
			 ! $said_user->IsRole('unconscious')) ||
			$virtual_self->IsPartner('mind_receiver', $said_user->user_no) ||
			$said_user->IsPartner('mind_friend', $virtual_self->partner_list))
		       ) || $said_user->IsRole('mind_open') ||
		      ($real_user->IsRole('possessed_wolf') && $flag_wolf_group));

  if($location_system && $sentence == 'OBJECTION'){ //�۵Ĥ���
    $sentence = $talk_handle_name . ' ' . $MESSAGE->objection;
    $builder->AddSystemMessage('objection-' . $talk_sex, $sentence);
  }
  elseif($location_system && $sentence == 'GAMESTART_DO'); //�����೫����ɼ (���ߤϲ���ɽ�����ʤ�����)
  elseif($location_system && strpos($sentence, 'KICK_DO') === 0){ //KICK ��ɼ
    $target_handle_name = ParseStrings($sentence, 'KICK_DO');
    $sentence = "{$talk_handle_name} �� {$target_handle_name} {$MESSAGE->kick_do}";
    $builder->AddSystemMessage('kick', $sentence);
  }
  elseif($SELF->IsLive() && ! $SELF->IsDummyBoy() && $flag_system); //��¸�����ɼ�������ɽ��
  elseif($talk_uname == 'system'){ //�����ƥ��å�����
    if(strpos($sentence, 'MORNING') === 0){
      sscanf($sentence, "MORNING\t%d", $morning_date);
      $sentence = "{$MESSAGE->morning_header} {$morning_date} {$MESSAGE->morning_footer}";
    }
    elseif(strpos($sentence, 'NIGHT') === 0){
      $sentence = $MESSAGE->night;
    }
    $builder->AddSystemTalk($sentence);
  }
  elseif(strpos($location, 'dummy_boy') !== false){ //�����귯���ѥ����ƥ��å�����
    $builder->AddSystemTalk($MESSAGE->dummy_boy . $sentence, 'dummy-boy');
  }
  //�����೫������ȥ������桢�����Ƥ���ͤ���
  elseif(! $ROOM->IsPlaying() || ($SELF->IsLive() && $ROOM->IsDay() && $location == 'day')){
    $builder->AddTalk($said_user, $talk);
  }
  //�������桢�����Ƥ���ͤ����ϵ
  elseif($flag_live_night && $location == 'night wolf'){
    if($flag_wolf_group || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif(! $SELF->IsRole('mind_scanner')){ //���Ȥ�ˤϱ��ʤ��ϸ����ʤ�
      $builder->AddWhisper('wolf', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����񤭶���
  elseif($flag_live_night && $location == 'night mad'){
    if($flag_wolf_group || $flag_mind_read) $builder->AddTalk($said_user, $talk);
  }
  //�������桢�����Ƥ���ͤ���ζ�ͭ��
  elseif($flag_live_night && $location == 'night common'){
    if($virtual_self->IsRole('common') || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif(! $SELF->IsRole('dummy_common')){ //̴��ͭ�Ԥˤϲ��⸫���ʤ�
      $builder->AddWhisper('common', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ÿ�
  elseif($flag_live_night && $location == 'night fox'){
    if($flag_fox_group || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif($SELF->IsRole('wise_wolf')){
      $builder->AddWhisper('common', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ȥ��
  elseif($flag_live_night && $location == 'night self_talk'){
    if($virtual_self->IsSameUser($talk_uname) || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif($said_user->IsRole('silver_wolf') && ! $SELF->IsRole('mind_scanner')){
      $builder->AddWhisper('wolf', $talk); //��ϵ���Ȥ���Ϥ��Ȥ�ʳ��ˤϱ��ʤ��˸�����
    }
  }
  //�����ཪλ / �����귯(����GM��) / �������桢��˴��(��������ץ��������Բ�)
  elseif($ROOM->IsFinished() || $SELF->IsDummyBoy() || ($SELF->IsDead() && $ROOM->IsOpenCast())){
    if($location_system && $flag_action->vote){ //�跺��ɼ
      $target_handle_name = ParseStrings($sentence, 'VOTE_DO');
      $action = 'vote';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->vote_do;
    }
    elseif($location_system && $flag_action->wolf){ //ϵ����ɼ
      $target_handle_name = ParseStrings($sentence, 'WOLF_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' ������ϵ�� '.$target_handle_name.' '.$MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_action->mage){ //�ꤤ�դ���ɼ
      $target_handle_name = ParseStrings($sentence, 'MAGE_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_action->voodoo_killer){ //���ۻդ���ɼ
      $target_handle_name = ParseStrings($sentence, 'VOODOO_KILLER_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->voodoo_killer_do;
    }
    elseif($location_system && $flag_action->jammer_mad){ //���Ƥ���ɼ
      $target_handle_name = ParseStrings($sentence, 'JAMMER_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->jammer_do;
    }
    elseif($location_system && $flag_action->voodoo_mad){ //���ѻդ���ɼ
      $target_handle_name = ParseStrings($sentence, 'VOODOO_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->voodoo_do;
    }
    elseif($location_system && $flag_action->dream_eat){ //�Ӥ���ɼ
      $target_handle_name = ParseStrings($sentence, 'DREAM_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_action->trap_mad){ //櫻դ���ɼ
      $target_handle_name = ParseStrings($sentence, 'TRAP_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->trap_do;
    }
    elseif($location_system && $flag_action->not_trap_mad){ //櫻դΥ���󥻥���ɼ
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' '.$MESSAGE->trap_not_do;
    }
    elseif($location_system && $flag_action->guard){ //��ͤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'GUARD_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->guard_do;
    }
    elseif($location_system && $flag_action->reporter){ //�֥󲰤���ɼ
      $target_handle_name = ParseStrings($sentence, 'REPORTER_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->reporter_do;
    }
    elseif($location_system && $flag_action->anti_voodoo){ //�������ɼ
      $target_handle_name = ParseStrings($sentence, 'ANTI_VOODOO_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->anti_voodoo_do;
    }
    elseif($location_system && $flag_action->poison_cat){ //ǭ������ɼ
      $target_handle_name = ParseStrings($sentence, 'POISON_CAT_DO');
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->revive_do;
    }
    elseif($location_system && $flag_action->not_poison_cat){ //ǭ���Υ���󥻥���ɼ
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->revive_not_do;
    }
    elseif($location_system && $flag_action->assassin){ //�Ż��Ԥ���ɼ
      $target_handle_name = ParseStrings($sentence, 'ASSASSIN_DO');
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->assassin_do;
    }
    elseif($location_system && $flag_action->not_assassin){ //�Ż��ԤΥ���󥻥���ɼ
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->assassin_not_do;
    }
    elseif($location_system && $flag_action->mind_scanner){ //���Ȥ����ɼ
      $target_handle_name = ParseStrings($sentence, 'MIND_SCANNER_DO');
      $action = 'mind-scanner-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mind_scanner_do;
    }
    elseif($location_system && $flag_action->voodoo_fox){ //��������ɼ
      $target_handle_name = ParseStrings($sentence, 'VOODOO_FOX_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->voodoo_do;
    }
    elseif($location_system && $flag_action->child_fox){ //�ҸѤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'CHILD_FOX_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_action->cupid){ //���塼�ԥåɤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'CUPID_DO');
      $action = 'cupid-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->cupid_do;
    }
    elseif($location_system && $flag_action->mania){ //���åޥ˥�����ɼ
      $target_handle_name = ParseStrings($sentence, 'MANIA_DO');
      $action = 'mania-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mania_do;
    }
    else{ //����¾�����Ƥ�ɽ��(��Ԥξ��)
      $base_class = 'user-talk';
      $talk_class = 'user-name';
      switch($location){
      case 'night self_talk':
	$talk_handle_name .= '<span>���Ȥ��</span>';
	$talk_class .= ' night-self-talk';
	break;

      case 'night wolf':
	$talk_handle_name .= '<span>(��ϵ)</span>';
	$talk_class .= ' night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'night mad':
	$talk_handle_name .= '<span>(�񤭶���)</span>';
	$talk_class .= ' night-wolf';
	$font_type  .= ' night-wolf';
	break;

      case 'night common':
	$talk_handle_name .= '<span>(��ͭ��)</span>';
	$talk_class .= ' night-common';
	$font_type  .= ' night-common';
	break;

      case 'night fox':
	$talk_handle_name .= '<span>(�Ÿ�)</span>';
	$talk_class .= ' night-fox';
	$font_type  .= ' night-fox';
	break;

      case 'heaven':
	$base_class .= ' heaven';
	break;
      }
    }
    if($action != ''){
      $builder->AddSystemMessage($action, $sentence);
    }
    else{
      $symbol = "<font color=\"{$talk_color}\">��</font>";
      if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
      $builder->RawAddTalk($symbol, $talk_handle_name, $sentence, $font_type, $base_class, $talk_class);
    }
  }
  //��������ϴ���Ԥ���������⡼��
  elseif($flag_system); //��ɼ�������ɽ��
  else{ //�����
    if($ROOM->IsNight()){
      switch($location){
      case 'night wolf':
	if($flag_wolf_group){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif(! $SELF->IsRole('mind_scanner')){ //���Ȥ�ˤϱ��ʤ��ϸ����ʤ�
	  $builder->AddWhisper('wolf', $talk);
	}
	break;

      case 'night mad':
	if($flag_wolf_group) $builder->AddTalk($said_user, $talk);
	break;

      case 'night common':
	if($virtual_self->IsRole('common')){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif(! $SELF->IsRole('dummy_common')){ //̴��ͭ�Ԥˤϲ��⸫���ʤ�
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'night fox':
	if($flag_fox_group){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($pseud_self->IsRole('wise_wolf')){
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'night self_talk':
	if($virtual_self->IsSameUser($talk_uname)){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif($said_user->IsRole('silver_wolf') && ! $SELF->IsRole('mind_scanner')){
	  $builder->AddWhisper('wolf', $talk); //��ϵ���Ȥ���Ϥ��Ȥ�ʳ��ˤϱ��ʤ��˸�����
	}
	break;
      }
    }
    else{
      $builder->AddTalk($said_user, $talk);
    }
  }
}

//��˴�Ԥΰ�������
function OutputLastWords(){
  global $MESSAGE, $ROOM, $USERS;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! ($ROOM->IsPlaying() || $ROOM->log_mode)) return false;

  //�����λ�˴�԰�������
  $set_date = $ROOM->date - 1;
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $set_date AND type = 'LAST_WORDS' ORDER BY RAND()";
  $array = FetchArray($query);
  if(count($array) < 1) return false;

  echo <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">

EOF;

  foreach($array as $result){
    list($handle_name, $str) = ParseStrings($result);
    LineToBR(&$str);

    echo <<<EOF
<tr>
<td class="lastwords-title">{$handle_name}<span>����ΰ��</span></td>
<td class="lastwords-body">{$str}</td>
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

  //�跺��å��������ǻ��å�����(��)
  $type_day = "type = 'VOTE_KILLED' OR type = 'POISON_DEAD_day' OR type = 'LOVERS_FOLLOWED_day' " .
    "OR type LIKE 'SUDDEN_DEATH_%'";

  //����������˵����ä���˴��å�����
  $type_night = "type = 'WOLF_KILLED' OR type LIKE 'POSSESSED%' OR type = 'DREAM_KILLED' " .
    "OR type = 'TRAPPED' OR type = 'CURSED' OR type = 'FOX_DEAD' " .
    "OR type = 'HUNTED' OR type = 'REPORTER_DUTY' OR type = 'ASSASSIN_KILLED' " .
    "OR type = 'POISON_DEAD_night' OR type = 'LOVERS_FOLLOWED_night' OR type LIKE 'REVIVE_%'";

  if($ROOM->IsDay()){
    $set_date = $yesterday;
    $type = $type_night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_day;
  }

  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }

  //�������⡼�ɰʳ��ʤ���������˴�ԥ�å�����ɽ��
  if($ROOM->log_mode) return;
  $set_date = $yesterday;
  if($set_date < 2) return;
  $type = ($ROOM->IsDay() ? $type_day : $type_night);

  echo '<hr>'; //��Ԥ�̵���Ȥ��˶�����������ʤ����ͤˤ������ $array ����Ȥ�����å�����
  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY RAND()");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }
}

//��ԤΥ������̤˻�˴��å����������
function OutputDeadManType($name, $type){
  global $MESSAGE, $ROOM, $SELF;

  $deadman_header = '<tr><td>'.$name.' '; //���ܥ�å������إå�
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //���ܥ�å�����
  $sudden_death   = $deadman_header.$MESSAGE->vote_sudden_death.'</td>'; //��������
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //�ɲö��̥إå�
  $open_reason = ($ROOM->IsFinished() || ($SELF->IsDead() && $ROOM->IsOpenCast()) ||
		  $SELF->IsDummyBoy());
  $show_reason = ($open_reason || ($SELF->IsRole('yama_necromancer') && $SELF->IsLive()));

  echo '<table class="dead-type">'."\n";
  switch($type){
  case 'VOTE_KILLED':
    echo '<tr class="dead-type-vote">';
    echo '<td>'.$name.' '.$MESSAGE->vote_killed.'</td>';
    break;

  case 'WOLF_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->wolf_killed.')</td>';
    break;

  case 'POSSESSED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->possessed.')</td>';
    break;

  case 'POSSESSED_RESET':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->possessed_reset.')</td>';
    break;

  case 'POSSESSED_TARGETED':
    if($open_reason) echo '<tr><td>'.$name.' '.$MESSAGE->possessed_targeted.'</td>';
    break;

  case 'WOLF_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->wolf_killed.')</td>';
    break;

  case 'DREAM_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->dream_killed.')</td>';
    break;

  case 'TRAPPED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->trapped.')</td>';
    break;

  case 'FOX_DEAD':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->fox_dead.')</td>';
    break;

  case 'CURSED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->cursed.')</td>';
    break;

  case 'POISON_DEAD_day':
  case 'POISON_DEAD_night':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->poison_dead.')</td>';
    break;

  case 'HUNTED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->hunted.')</td>';
    break;

  case 'REPORTER_DUTY':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->reporter_duty.')</td>';
    break;

  case 'ASSASSIN_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->assassin_killed.')</td>';
    break;

  case 'LOVERS_FOLLOWED_day':
  case 'LOVERS_FOLLOWED_night':
    echo '<tr><td>'.$name.' '.$MESSAGE->lovers_followed.'</td>';
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

  case 'SUDDEN_DEATH_CHICKEN':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->chicken.')</td>';
    break;

  case 'SUDDEN_DEATH_RABBIT':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->rabbit.')</td>';
    break;

  case 'SUDDEN_DEATH_PERVERSENESS':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->perverseness.')</td>';
    break;

  case 'SUDDEN_DEATH_FLATTERY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->flattery.')</td>';
    break;

  case 'SUDDEN_DEATH_IMPATIENCE':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->impatience.')</td>';
    break;

  case 'SUDDEN_DEATH_JEALOUSY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->jealousy.')</td>';
    break;

  case 'SUDDEN_DEATH_CELIBACY':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->celibacy.')</td>';
    break;

  case 'SUDDEN_DEATH_PANELIST':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->panelist.')</td>';
    break;
  }
  echo "</tr>\n</table>\n";
}

//��ɼ�ν��׽���
function OutputVoteList(){
  global $ROOM;

  if(! $ROOM->IsPlaying()) return false; //��������ʳ��Ͻ��Ϥ��ʤ�

 //��ʤ���������ʤ�κ����ν��פ�ɽ��
  $set_date = ($ROOM->IsDay() && ! $ROOM->log_mode ? $ROOM->date - 1 : $ROOM->date);
  OutputVoteListDay($set_date);
}

//���ꤷ�����դ���ɼ��̤���Ϥ���
function OutputVoteListDay($set_date){
  global $RQ_ARGS, $ROOM, $SELF;

  //���ꤵ�줿���դ���ɼ��̤����
  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $set_date and type = 'VOTE_KILL'";
  $vote_message_list = FetchArray($query);
  if(count($vote_message_list) == 0) return false; //��ɼ���

  $result_array = array(); //��ɼ��̤��Ǽ����
  $this_vote_times = -1; //���Ϥ�����ɼ�����Ͽ
  $table_count = 0; //ɽ�θĿ�
  $is_open_vote = (($ROOM->IsOption('open_vote') || $SELF->IsDead() || $ROOM->log_mode) &&
		   ! $ROOM->view_mode);
  foreach($vote_message_list as $vote_message){ //���ä�������˳�Ǽ����
    //���ֶ��ڤ�Υǡ�����ʬ�䤹��
    list($handle_name, $target_name, $voted_number,
	 $vote_number, $vote_times) = ParseStrings($vote_message, 'VOTE');

    if($this_vote_times != $vote_times){ //��ɼ������㤦�ǡ��������̥ơ��֥�ˤ���
      if($this_vote_times != -1) $result_array[$this_vote_times][] = '</table>'."\n";

      $this_vote_times = $vote_times;
      $result_array[$vote_times] = array();
      $result_array[$vote_times][] = '<table class="vote-list">'."\n";
      $result_array[$vote_times][] = '<td class="vote-times" colspan="4">' .
	$set_date . ' ���� ( ' . $vote_times . ' ����)</td>'."\n";

      $table_count++;
    }
    $vote_number_str = ($is_open_vote ? '��ɼ�� ' . $vote_number . ' ɼ ��' : '��ɼ�袪');

    //ɽ��������å�����
    $result_array[$vote_times][] = '<tr><td class="vote-name">' . $handle_name . '</td><td>' .
      $voted_number . ' ɼ</td><td>' . $vote_number_str .
      '</td><td class="vote-name"> ' . $target_name . ' </td></tr>'."\n";
  }
  $result_array[$this_vote_times][] = '</table>'."\n";

  //����˳�Ǽ���줿�ǡ��������
  if($RQ_ARGS->reverse_log){ //�ս�ɽ��
    for($i = 1; $i <= $table_count; $i++){
      if(is_array($result_array[$i])){
	foreach($result_array[$i] as $this_data) echo $this_data;
      }
    }
  }
  else{
    for($i = $table_count; $i > 0; $i--){
      if(is_array($result_array[$i])){
	foreach($result_array[$i] as $this_data) echo $this_data;
      }
    }
  }
}

//�ꤦ��ϵ����������Ҥ�������ǽ�Ϥ�Ȥ���å�����
function OutputAbilityAction(){
  global $MESSAGE, $ROOM;

  //��֤��򿦸��������Ĥ���Ƥ���Ȥ��Τ�ɽ��
  //(ǭ�����򿦸������Ϲ�ư�Ǥ��ʤ��Τ�����)
  if(! ($ROOM->IsDay() && $ROOM->IsOpenCast())) return false;

  $yesterday = $ROOM->date - 1;
  $header = '<b>�������롢';
  $footer = '</b><br>'."\n";
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO');
  if($yesterday == 1){
    array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'DREAM_EAT',
	       'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }

  $action = '';
  foreach($action_list as $this_action){
    if($action != '') $action .= ' OR ';
    $action .= "type = '$this_action'";
  }

  $query = "SELECT message, type FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = $yesterday AND ( $action )";
  $message_list = FetchAssoc($query);
  foreach($message_list as $array){
    $sentence = $array['message'];
    $type     = $array['type'];

    list($actor, $target) = ParseStrings($sentence);
    echo $header.$actor.' ';
    switch($type){
    case 'WOLF_EAT':
      echo '(��ϵ) ������ '.$target.' �������ޤ���';
      break;

    case 'MAGE_DO':
      echo '(�ꤤ��) �� '.$target.' ���ꤤ�ޤ���';
      break;

    case 'VOODOO_KILLER_DO':
      echo '(���ۻ�) �� '.$target.' �μ�����㱤��ޤ���';
      break;

    case 'JAMMER_MAD_DO':
      echo '(����) �� '.$target.' ���ꤤ��˸�����ޤ���';
      break;

    case 'TRAP_MAD_DO':
      echo '(櫻�) �� '.$target.' '.$MESSAGE->trap_do;
      break;

    case 'TRAP_MAD_NOT_DO':
      echo '(櫻�) '.$MESSAGE->trap_not_do;
      break;

    case 'VOODOO_MAD_DO':
      echo '(���ѻ�) �� '.$target.' �˼����򤫤��ޤ���';
      break;

    case 'DREAM_EAT':
      echo '(��) �� '.$target.' �������ޤ���';
      break;

    case 'GUARD_DO':
      echo '(���) �� '.$target.' '.$MESSAGE->guard_do;
      break;

    case 'ANTI_VOODOO_DO':
      echo '(���) �� '.$target.' �����㱤��ޤ���';
      break;

    case 'REPORTER_DO':
      echo '(�֥�) �� '.$target.' '.$MESSAGE->reporter_do;
      break;

    case 'ASSASSIN_DO':
      echo '(�Ż���) �� '.$target.' �������ޤ���';
      break;

    case 'ASSASSIN_NOT_DO':
      echo '(�Ż���) '.$MESSAGE->assassin_not_do;
      break;

    case 'MIND_SCANNER_DO':
      echo '(���Ȥ�) �� '.$target.' �ο����ɤߤޤ���';
      break;

    case 'VOODOO_FOX_DO':
      echo '(����) �� '.$target.' �˼����򤫤��ޤ���';
      break;

    case 'CHILD_FOX_DO':
      echo '(�Ҹ�) �� '.$target.' ���ꤤ�ޤ���';
      break;

    case 'CUPID_DO':
      echo '(���塼�ԥå�) �� '.$target.' '.$MESSAGE->cupid_do;
      break;

    case 'MANIA_DO':
      echo '(���åޥ˥�) �� '.$target.' �򿿻����ޤ���';
      break;
    }
    echo $footer;
  }
}

//���Ԥ�����å�
function CheckVictory($check_draw = false){
  global $GAME_CONF, $ROOM;

  $query_count = "SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} " .
    "AND live = 'live' AND user_no > 0 AND ";

  $human  = FetchResult($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')"); //¼��
  $wolf   = FetchResult($query_count . "role LIKE '%wolf%'"); //��ϵ
  $fox    = FetchResult($query_count . "role LIKE '%fox%'"); //�Ÿ�
  $lovers = FetchResult($query_count . "role LIKE '%lovers%'"); //����
  $quiz   = FetchResult($query_count . "role LIKE 'quiz%'"); //�����

  $victory_role = ''; //�����ر�
  if($wolf == 0 && $fox == 0 && $human == $quiz){ //����
    $victory_role = ($quiz > 0 ? 'quiz' : 'vanish');
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
  elseif($check_draw && GetVoteTimes() > $GAME_CONF->draw){ //����ʬ��
    $victory_role = 'draw';
  }

  if($victory_role == '') return false;

  //�����ཪλ
  mysql_query("UPDATE room SET status = 'finished', day_night = 'aftergame',
		victory_role = '$victory_role', finish_time = NOW() WHERE room_no = {$ROOM->id}");
  mysql_query('COMMIT'); //������ߥå�
  return true;
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
	InsertSystemTalk($user->handle_name . $MESSAGE->lovers_followed, ++$ROOM->system_time);
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

//�����Ƚ����(�����ƥ��å�����)
function InsertMediumMessage(){
  global $USERS;

  if(! $USERS->IsAppear('medium')) return false; //����νи������å�
  foreach($USERS->rows as $user){
    if(! $user->suicide_flag) continue;
    $handle_name = $USERS->GetVirtualHandleName($user->uname);
    InsertSystemMessage($handle_name . "\t" . $user->DistinguishCamp(), 'MEDIUM_RESULT');
  }
}

//�ꥢ�륿����ηв����
function GetRealPassTime(&$left_time, $flag = false){
  global $ROOM;

  $time_str = strstr($ROOM->game_option, 'real_time');
  //�»��֤����»��֤����
  sscanf($time_str, 'real_time:%d:%d', &$day_minutes, &$night_minutes);
  $day_time   = $day_minutes   * 60; //�äˤʤ���
  $night_time = $night_minutes * 60; //�äˤʤ���

  //�Ǥ⾮���ʻ���(���̤κǽ�λ���)�����
  $query = "SELECT MIN(time) FROM talk WHERE room_no = {$ROOM->id} " .
    "AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'";
  $sql = SendQuery($query);
  $start_time = (int)mysql_result($sql, 0, 0);

  if($start_time != NULL){
    $pass_time = $ROOM->system_time - $start_time; //�вᤷ������
  }
  else{
    $pass_time = 0;
    $start_time = $ROOM->system_time;
  }
  $base_time = ($ROOM->IsDay() ? $day_time : $night_time);
  $left_time = $base_time - $pass_time;
  if($left_time < 0) $left_time = 0; //�ޥ��ʥ��ˤʤä��饼��ˤ���
  if(! $flag) return;

  $format = 'Y, m, j, G, i, s';
  $start_date_str = TZDate($format, $start_time);
  $end_date_str   = TZDate($format, $start_time + $base_time);
  return array($start_date_str, $end_date_str);
}

//���äǻ��ַв����ηв����
function GetTalkPassTime(&$left_time, $flag = false){
  global $TIME_CONF, $ROOM;

  $sql = mysql_query("SELECT SUM(spend_time) FROM talk WHERE room_no = {$ROOM->id}
			AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'");
  $spend_time = (int)mysql_result($sql, 0, 0);

  if($ROOM->IsDay()){ //���12����
    $base_time = $TIME_CONF->day;
    $full_time = 12;
  }
  else{ //���6����
    $base_time = $TIME_CONF->night;
    $full_time = 6;
  }
  $left_time = $base_time - $spend_time;
  if($left_time < 0){ //�ޥ��ʥ��ˤʤä��饼��ˤ���
    $left_time = 0;
  }

  //���ۻ��֤η׻�
  $base_left_time = ($flag ? $TIME_CONF->silence_pass : $left_time);
  return ConvertTime($full_time * $base_left_time * 60 * 60 / $base_time);
}

//�����ƥ��å��������� (talk Table)
function InsertSystemTalk($sentence, $time, $location = '', $date = '', $uname = 'system'){
  global $ROOM;

  if($location == '') $location = "{$ROOM->day_night} system";
  if($date == '') $date = $ROOM->date;
  if($ROOM->test_mode){
    echo "System Talk: $location : $sentence <br>";
    return;
  }
  InsertTalk($ROOM->id, $date, $location, $uname, $time, $sentence, NULL, 0);
}

//�����ƥ��å��������� (system_message Table)
function InsertSystemMessage($sentence, $type, $date = ''){
  global $ROOM;

  if($ROOM->test_mode){
    echo "System Message: $type : $sentence <br>";
    return;
  }
  if($date == '') $date = $ROOM->date;
  $values = "{$ROOM->id}, '$sentence', '$type', $date";
  InsertDatabase('system_message', 'room_no, message, type, date', $values);
}

//�ǽ��񤭹��߻���򹹿�
function UpdateTime(){
  global $ROOM;
  mysql_query("UPDATE room SET last_updated = '{$ROOM->system_time}' WHERE room_no = {$ROOM->id}");
}

//���ޤǤ���ɼ���������
function DeleteVote(){
  global $ROOM;

  $query = "DELETE FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date}";
  if($ROOM->IsDay()){
    $vote_times = GetVoteTimes();
    $query .= " AND vote_times = $vote_times AND situation = 'VOTE_KILL'";
  }
  elseif($ROOM->IsNight()){
    if($ROOM->date == 1){
      $query .= " AND situation <> 'CUPID_DO'";
    }
    else{
      $query .= " AND situation <> 'VOTE_KILL'";
    }
  }
  mysql_query($query);
  mysql_query("OPTIMIZE TABLE vote");
}

//�����ɼ������������
function GetVoteTimes($revote = false){
  global $ROOM;

  $query = "SELECT message FROM system_message WHERE room_no = {$ROOM->id} " .
    "AND date = {$ROOM->date} AND type = ";
  $query .= ($revote ?  "'RE_VOTE' ORDER BY message DESC" : "'VOTE_TIMES'");

  return (int)FetchResult($query);
}

//��μ�ʬ����ɼ�Ѥߥ����å�
function CheckSelfVoteNight($situation, $not_situation = ''){
  global $ROOM, $SELF;

  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} AND ";
  if($situation == 'WOLF_EAT'){
    $query .= "situation = '$situation'";
  }
  elseif($not_situation != ''){
    $query .= "uname = '{$SELF->uname}' AND (situation = '$situation' OR situation = '$not_situation')";
  }
  else{
    $query .= "uname = '{$SELF->uname}' AND situation = '$situation'";
  }
  return (FetchResult($query) > 0);
}

//���󤫤������˰�ļ��Ф�
function GetRandom($array){
  return $array[array_rand($array)];
}

//���ڡ�������������
function DecodeSpace(&$str){
  $str = str_replace("\\space;", ' ', $str);
}

//��å�������ʬ�䤷��ɬ�פʾ�����֤�
function ParseStrings($str, $type = NULL){
  $str = str_replace(' ', "\\space;", $str); //���ڡ��������򤹤�
  switch($type){
  case 'LAST_WORDS':
  case 'KICK_DO':
  case 'VOTE_DO':
  case 'WOLF_EAT':
  case 'MAGE_DO':
  case 'VOODOO_KILLER_DO':
  case 'JAMMER_MAD_DO':
  case 'TRAP_MAD_DO':
  case 'VOODOO_MAD_DO':
  case 'DREAM_EAT':
  case 'GUARD_DO':
  case 'ANTI_VOODOO_DO':
  case 'REPORTER_DO':
  case 'POISON_CAT_DO':
  case 'ASSASSIN_DO':
  case 'MIND_SCANNER_DO':
  case 'VOODOO_FOX_DO':
  case 'CHILD_FOX_DO':
  case 'CUPID_DO':
  case 'MANIA_DO':
    list($msg, $target) = explode("\t", $str);
    if($msg == $type){
      DecodeSpace(&$target);
      return $target;
    }
    return false;

  case 'MAGE_RESULT':
  case 'TONGUE_WOLF_RESULT':
  case 'REPORTER_SUCCESS':
  case 'POISON_CAT_RESULT':
  case 'PHARMACIST_RESULT':
  case 'MANIA_RESULT':
  case 'CHILD_FOX_RESULT':
    list($first, $second, $third) = explode("\t", $str);
    DecodeSpace(&$first);
    DecodeSpace(&$second);
    DecodeSpace(&$third);
    return array($first, $second, $third);

  case 'VOTE':
    list($self, $target, $voted, $vote, $times) = explode("\t", $str);
    DecodeSpace(&$self);
    DecodeSpace(&$target);

    //%d �Ǽ������Ƥ������� (int)�פ�ʤ��褦�ʵ������������ɡġĤ�����ʤ���Ĥ�����
    return array($self, $target, $voted, $vote, (int)$times);

  default:
    list($header, $footer) = explode("\t", $str, 2);
    DecodeSpace(&$header);
    DecodeSpace(&$footer);

    return array($header, $footer);
  }
}
?>
