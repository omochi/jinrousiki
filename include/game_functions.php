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

  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  //�֥饦��������å� (MSIE @ Windows ���� ������ Alt, Title °���ǲ��ԤǤ���)
  //IE �ξ����Ԥ� \r\n �����졢����¾�Υ֥饦���ϥ��ڡ����ˤ���(������Alt°��)
  $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');

  echo '<div class="player"><table cellspacing="5"><tr>'."\n";
  $count = 0;
  $is_open_role = ($ROOM->IsAfterGame() || $SELF->IsDummyBoy() ||
		   ($SELF->IsDead() && $ROOM->IsOpenCast()));
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_uname   = $this_user->uname;
    $this_handle  = $this_user->handle_name;
    $this_profile = $this_user->profile;
    $this_role    = $this_user->role;
    $this_file    = $this_user->icon_filename;
    $this_color   = $this_user->color;

    $profile_alt  = str_replace("\n", $replace, $this_profile);
    if($DEBUG_MODE) $this_handle .= ' (' . $this_user_no . ')';

    //��������
    $path = $ICON_CONF->path . '/' . $this_file;
    $img_tag = '<img title="' . $profile_alt . '" alt="' . $profile_alt .
      '" style="border-color: ' . $this_color . ';"';
    if($this_user->IsLive()){ //�����Ƥ���Х桼����������
      $this_live_str = '(��¸��)';
    }
    else{ //���Ǥ�л�˴��������
      $this_live_path = $path; //��������Υѥ��������ؤ�
      $path           = $ICON_CONF->dead;
      $this_live_str  = '(��˴)';
      $img_tag .= " onMouseover=\"this.src='$this_live_path'\" onMouseout=\"this.src='$path'\"";
    }
    $img_tag .= ' width="' . $width . '" height="' . $height . '" src="' . $path . '">';

    //�����ཪλ�塦��˴�����򿦸����⡼�ɤʤ顢�򿦡��桼���͡����ɽ��
    if($is_open_role){
      $role_str = '';
      if($this_user->IsRole('human', 'suspect', 'unconscious'))
	$role_str = MakeRoleName($this_user->main_role, 'human');
      elseif($this_user->IsRoleGroup('mage') || $this_user->IsRole('voodoo_killer'))
	$role_str = MakeRoleName($this_user->main_role, 'mage');
      elseif($this_user->IsRoleGroup('necromancer') || $this_user->IsRole('medium'))
	$role_str = MakeRoleName($this_user->main_role, 'necromancer');
      elseif($this_user->IsRoleGroup('guard') || $this_user->IsRole('reporter', 'anti_voodoo'))
	$role_str = MakeRoleName($this_user->main_role, 'guard');
      elseif($this_user->IsRoleGroup('common'))
	$role_str = MakeRoleName($this_user->main_role, 'common');
      elseif($this_user->IsRole('mind_scanner'))
	$role_str = MakeRoleName($this_user->main_role, 'mind');
      elseif($this_user->IsRoleGroup('jealousy'))
	$role_str = MakeRoleName($this_user->main_role, 'jealousy');
      elseif($this_user->IsRole('assassin', 'mania', 'quiz'))
	$role_str = MakeRoleName($this_user->main_role);
      elseif($this_user->IsRoleGroup('wolf'))
	$role_str = MakeRoleName($this_user->main_role, 'wolf');
      elseif($this_user->IsRoleGroup('mad'))
	$role_str = MakeRoleName($this_user->main_role, 'mad');
      elseif($this_user->IsRoleGroup('fox'))
	$role_str = MakeRoleName($this_user->main_role, 'fox');
      elseif($this_user->IsRoleGroup('chiroptera'))
	$role_str = MakeRoleName($this_user->main_role, 'chiroptera');
      elseif($this_user->IsRoleGroup('cupid'))
	$role_str = MakeRoleName($this_user->main_role, 'cupid');
      elseif($this_user->IsRoleGroup('poison') || $this_user->IsRole('pharmacist'))
	$role_str = MakeRoleName($this_user->main_role, 'poison');

      //���������Ǥ��
      if($this_user->IsLovers()) $role_str .= MakeRoleName('lovers', '', true);
      if($this_user->IsRole('mind_read')) $role_str .= MakeRoleName('mind_read', 'mind', true);
      if($this_user->IsRole('mind_open')) $role_str .= MakeRoleName('mind_open', 'mind', true);
      if($this_user->IsRole('mind_receiver')) $role_str .= MakeRoleName('mind_receiver', 'mind', true);
      if($this_user->IsRole('copied')) $role_str .= MakeRoleName('copied', 'mania', true);

      if(strpos($this_role, 'authority') !== false)
	$role_str .= MakeRoleName('authority', '', true);
      elseif(strpos($this_role, 'random_voter') !== false)
	$role_str .= MakeRoleName('random_voter', 'authority', true);
      elseif(strpos($this_role, 'rebel') !== false)
	$role_str .= MakeRoleName('rebel', 'authority', true);
      elseif(strpos($this_role, 'watcher') !== false)
	$role_str .= MakeRoleName('watcher', 'authority', true);
      elseif(strpos($this_role, 'decide') !== false)
	$role_str .= MakeRoleName('decide', '', true);
      elseif(strpos($this_role, 'plague') !== false)
	$role_str .= MakeRoleName('plague', 'decide', true);
      elseif(strpos($this_role, 'good_luck') !== false)
	$role_str .= MakeRoleName('good_luck', 'decide', true);
      elseif(strpos($this_role, 'bad_luck') !== false)
	$role_str .= MakeRoleName('bad_luck', 'decide', true);
      elseif(strpos($this_role, 'upper_luck') !== false)
	$role_str .= MakeRoleName('upper_luck', 'luck', true);
      elseif(strpos($this_role, 'downer_luck') !== false)
	$role_str .= MakeRoleName('downer_luck', 'luck', true);
      elseif(strpos($this_role, 'random_luck') !== false)
	$role_str .= MakeRoleName('random_luck', 'luck', true);
      elseif(strpos($this_role, 'star') !== false)
	$role_str .= MakeRoleName('star', 'luck', true);
      elseif(strpos($this_role, 'disfavor') !== false)
	$role_str .= MakeRoleName('disfavor', 'luck', true);

      if(strpos($this_role, 'strong_voice') !== false)
	$role_str .= MakeRoleName('strong_voice', 'voice', true);
      elseif(strpos($this_role, 'normal_voice') !== false)
	$role_str .= MakeRoleName('normal_voice', 'voice', true);
      elseif(strpos($this_role, 'weak_voice') !== false)
	$role_str .= MakeRoleName('weak_voice', 'voice', true);
      elseif(strpos($this_role, 'upper_voice') !== false)
	$role_str .= MakeRoleName('upper_voice', 'voice', true);
      elseif(strpos($this_role, 'downer_voice') !== false)
	$role_str .= MakeRoleName('downer_voice', 'voice', true);
      elseif(strpos($this_role, 'random_voice') !== false)
	$role_str .= MakeRoleName('random_voice', 'voice', true);

      if(strpos($this_role, 'no_last_words') !== false)
	$role_str .= MakeRoleName('no_last_words', 'seal', true);
      if(strpos($this_role, 'blinder') !== false)
	$role_str .= MakeRoleName('blinder', 'seal', true);
      if(strpos($this_role, 'earplug') !== false)
	$role_str .= MakeRoleName('earplug', 'seal', true);
      if(strpos($this_role, 'speaker') !== false)
	$role_str .= MakeRoleName('speaker', 'seal', true);
      if(strpos($this_role, 'silent') !== false)
	$role_str .= MakeRoleName('silent', 'seal', true);

      if(strpos($this_role, 'liar') !== false)
	$role_str .= MakeRoleName('liar', 'convert', true);
      if(strpos($this_role, 'invisible') !== false)
	$role_str .= MakeRoleName('invisible', 'convert', true);
      if(strpos($this_role, 'rainbow') !== false)
	$role_str .= MakeRoleName('rainbow', 'convert', true);
      if(strpos($this_role, 'weekly') !== false)
	$role_str .= MakeRoleName('weekly', 'convert', true);
      if(strpos($this_role, 'gentleman') !== false)
	$role_str .= MakeRoleName('gentleman', 'convert', true);
      elseif(strpos($this_role, 'lady') !== false)
	$role_str .= MakeRoleName('lady', 'convert', true);

      if(strpos($this_role, 'chicken') !== false)
	$role_str .= MakeRoleName('chicken', 'sudden-death', true);
      if(strpos($this_role, 'rabbit') !== false)
	$role_str .= MakeRoleName('rabbit', 'sudden-death', true);
      elseif(strpos($this_role, 'perverseness') !== false)
	$role_str .= MakeRoleName('perverseness', 'sudden-death', true);
      elseif(strpos($this_role, 'flattery') !== false)
	$role_str .= MakeRoleName('flattery', 'sudden-death', true);
      elseif(strpos($this_role, 'impatience') !== false)
	$role_str .= MakeRoleName('impatience', 'sudden-death', true);
      elseif(strpos($this_role, 'celibacy') !== false)
	$role_str .= MakeRoleName('celibacy', 'sudden-death', true);
      elseif(strpos($this_role, 'panelist') !== false)
	$role_str .= MakeRoleName('panelist', 'sudden-death', true);

      if($SELF->IsDummyBoy() && $ROOM->IsBeforeGame()){
	$query_game_start = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
	  "AND situation = 'GAMESTART' AND uname = '$this_uname'";
	if(($this_user->IsDummyBoy() && ! $ROOM->IsQuiz()) || FetchResult($query_game_start) > 0){
	  $already_vote_class = ' class="already-vote"';
	}
	else{
	  $already_vote_class = '';
	}
      }
      echo "<td${already_vote_class}>{$img_tag}</td>"."\n";
      echo "<td${already_vote_class}><font color=\"$this_color\">��</font>$this_handle<br>"."\n";
      echo "��($this_uname)<br> $role_str";
    }
    elseif($ROOM->IsBeforeGame()){ //��������
      //�����ॹ�����Ȥ���ɼ���Ƥ���п����Ѥ���
      $query_game_start = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
	"AND situation = 'GAMESTART' AND uname = '$this_uname'";
      if(($this_user->IsDummyBoy() && ! $ROOM->IsQuiz()) || FetchResult($query_game_start) > 0){
	$already_vote_class = ' class="already-vote"';
      }
      else{
	$already_vote_class = '';
      }

      echo "<td${already_vote_class}>{$img_tag}</td>"."\n";
      echo "<td${already_vote_class}><font color=\"$this_color\">��</font>$this_handle";
    }
    else{ //�����Ƥ��ƥ�������
      echo "<td>{$img_tag}</td>"."\n";
      echo "<td><font color=\"$this_color\">��</font>$this_handle";
    }
    echo '<br>'."\n" . $this_live_str . '</td>'."\n";

    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
  }
  echo '</tr></table></div>'."\n";
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
  global $MESSAGE, $ROOM, $SELF;

  //�����رĤ����
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

  //�ġ��ξ��Ԥ����
  //����̤���ꡢ����⡼�ɡ��������⡼�ɤʤ���ɽ��
  if($victory == NULL || $ROOM->view_mode || $ROOM->log_mode) return;

  $result = 'win';
  $camp   = $SELF->DistinguishCamp(); //��°�رĤ����
  $lovers = $SELF->IsLovers();
  if($victory == 'human' && $camp == 'human' && ! $lovers)
    $class = 'human';
  elseif($victory == 'wolf' && $camp == 'wolf' && ! $lovers)
    $class = 'wolf';
  elseif(strpos($victory, 'fox') !== false && $camp == 'fox' && ! $lovers)
    $class = 'fox';
  elseif($victory == 'lovers' && ($camp == 'lovers' || $lovers))
    $class = 'lovers';
  elseif($victory == 'quiz' && $camp == 'quiz')
    $class = 'quiz';
  elseif($victory == 'quiz_dead'){
    $class  = 'none';
    $result = ($camp == 'quiz' ? 'lose' : 'draw');
  }
  elseif($victory == 'draw' || $victory == 'vanish'){
    $class  = 'none';
    $result = 'draw';
  }
  elseif($camp == 'chiroptera' && $SELF->IsLive()){
    $class  = 'chiroptera';
  }
  else{
    $class  = 'none';
    $result = 'lose';
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
  $builder->EndTalk();
}

//���ý���
function OutputTalk($talk, &$builder){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $said_user = $USERS->ByUname($talk->uname);
  /*
    $talk_uname ��ɬ��$talk����������뤳�ȡ�
    $USERS�ˤϥ����ƥ�桼����'system'��¸�ߤ��ʤ����ᡢ$said_user�Ͼ��null�ˤʤäƤ��롣
  */
  $talk_uname       = $talk->uname;
  $talk_handle_name = $said_user->handle_name;
  $talk_sex         = $said_user->sex;
  $talk_color       = $said_user->color;
  $sentence         = $talk->sentence;
  $font_type        = $talk->font_type;
  $location         = $talk->location;

  if($RQ_ARGS->add_role && $said_user !== NULL){ //��ɽ���⡼���б�
    $talk_handle_name .= $said_user->MakeShortRoleName();
  }

  LineToBR($sentence); //���ԥ����ɤ� <br> ���Ѵ�
  $location_system     = (strpos($location, 'system') !== false);

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
    'mania' => 'MANIA_DO'
			    );
  $flag_vote_action = false;
  foreach($vote_action_list as $this_role => $this_action){
    $flag_action->$this_role = (strpos($sentence, $this_action) === 0);
    $flag_vote_action |= $flag_action->$this_role;
  }

  $flag_system = ($location_system && $flag_vote_action && ! $ROOM->IsFinished());
  $flag_live_night = ($SELF->IsLive() && $ROOM->IsNight() && ! $ROOM->IsFinished());
  $flag_mind_read  = ($said_user !== NULL &&
		      (($ROOM->date > 1 && $SELF->IsLive() &&
			((is_array($said_user->partner_list['mind_read']) &&
			  in_array($SELF->user_no, $said_user->partner_list['mind_read']) &&
			  ! $said_user->IsRole('unconscious')) ||
			 (is_array($SELF->partner_list['mind_receiver']) &&
			  in_array($said_user->user_no, $SELF->partner_list['mind_receiver'])))
			) || $said_user->IsRole('mind_open')));
  $flag_wolf_group = ($SELF->IsWolf(true) || $SELF->IsRole('whisper_mad') || $SELF->IsDummyBoy());
  $flag_fox_group  = ($SELF->IsFox(true) || $SELF->IsDummyBoy());

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
    $builder->AddSystemTalk($MESSAGE->dummy_boy . $sentence);
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
    else{
      $builder->AddWhisper('wolf', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����񤭶���
  elseif($flag_live_night && $location == 'night mad'){
    if($flag_wolf_group || $flag_mind_read) $builder->AddTalk($said_user, $talk);
  }
  //�������桢�����Ƥ���ͤ���ζ�ͭ��
  elseif($flag_live_night && $location == 'night common'){
    if($SELF->IsRole('common') || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
    }
    elseif(! $SELF->IsRole('dummy_common')){ //̴��ͭ�Ԥˤϲ��⸫���ʤ�
      $builder->AddWhisper('common', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ÿ�
  elseif($flag_live_night && $location == 'night fox'){
    if($flag_fox_group || $flag_mind_read) $builder->AddTalk($said_user, $talk);
  }
  //�������桢�����Ƥ���ͤ�����Ȥ��
  elseif($flag_live_night && $location == 'night self_talk'){
    if($SELF->IsSameUser($talk_uname) || $SELF->IsDummyBoy() || $flag_mind_read){
      $builder->AddTalk($said_user, $talk);
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
	else{
	  $builder->AddWhisper('wolf', $talk);
	}
	break;

      case 'night mad':
	if($flag_wolf_group) $builder->AddTalk($said_user, $talk);
	break;

      case 'night common':
	if($SELF->IsRole('common')){
	  $builder->AddTalk($said_user, $talk);
	}
	elseif(! $SELF->IsRole('dummy_common')){ //̴��ͭ�Ԥˤϲ��⸫���ʤ�
	  $builder->AddWhisper('common', $talk);
	}
	break;

      case 'night fox':
	if($flag_fox_group) $builder->AddTalk($said_user, $talk);
	break;

      case 'night self_talk':
	if($SELF->uname == $talk_uname) $builder->AddTalk($said_user, $talk);
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
  global $MESSAGE, $ROOM;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! ($ROOM->IsPlaying() || $ROOM->log_mode)) return false;

  //�����λ�˴�԰�������
  $set_date = $ROOM->date - 1;
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = {$ROOM->id}
			AND date = $set_date AND type = 'LAST_WORDS' ORDER BY MD5(RAND()*NOW())");
  $count = mysql_num_rows($sql);
  if($count < 1) return false;

  echo <<<EOF
<table class="system-lastwords"><tr>
<td>{$MESSAGE->lastwords}</td>
</tr></table>
<table class="lastwords">

EOF;

  for($i = 0; $i < $count; $i++){
    $result = mysql_result($sql, $i, 0);
    LineToBR(&$result);
    list($handle, $str) = ParseStrings($result);

    echo <<<EOF
<tr>
<td class="lastwords-title">{$handle}<span>����ΰ��</span></td>
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
  $type_night = "type = 'WOLF_KILLED' OR type = 'CURSED' OR type = 'FOX_DEAD' " .
    "OR type = 'HUNTED' OR type = 'REPORTER_DUTY' OR type = 'ASSASSIN_KILLED' " .
    "OR type = 'DREAM_KILLED' OR type = 'TRAPPED' OR type = 'POISON_DEAD_night' " .
    "OR type = 'LOVERS_FOLLOWED_night' OR type LIKE 'REVIVE_%'";

  if($ROOM->IsDay()){
    $set_date = $yesterday;
    $type = $type_night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_day;
  }

  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY MD5(RAND()*NOW())");
  foreach($array as $this_array){
    OutputDeadManType($this_array['message'], $this_array['type']);
  }

  //�������⡼�ɰʳ��ʤ���������˴�ԥ�å�����ɽ��
  if($ROOM->log_mode) return;
  $set_date = $yesterday;
  $type = ($ROOM->IsDay() ? $type_day : $type_night);

  echo '<hr>'; //��Ԥ�̵���Ȥ��˶�����������ʤ����ͤˤ������ $array ����Ȥ�����å�����
  $array = FetchAssoc("$query_header $set_date AND ( $type ) ORDER BY MD5(RAND()*NOW())");
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
  $show_reason = ($ROOM->IsFinished() || ($SELF->IsDead() && $ROOM->IsOpenCast()) ||
		  $SELF->IsDummyBoy() || $SELF->IsRole('yama_necromancer'));

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

  case 'FOX_DEAD':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->fox_dead.')</td>';
    break;

  case 'DREAM_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->dream_killed.')</td>';
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

  case 'TRAPPED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->trapped.')</td>';
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
		       'VOODOO_MAD_DO', 'DREAM_EAT', 'VOODOO_FOX_DO', 'CHILD_FOX_DO');
  if($yesterday == 1){
    array_push($action_list, 'MIND_SCANNER_DO', 'CUPID_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list, 'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'ASSASSIN_DO',
	       'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
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
		victory_role = '$victory_role' WHERE room_no = {$ROOM->id}");

  //�����ཪλ���֤�����
  $sentence = '�����ཪλ��' . gmdate('Y/m/d (D) H:i:s', $ROOM->system_time);
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'aftergame system');

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
      elseif(! $user->Kill('LOVERS_FOLLOWED_' . $ROOM->day_night)){ //�̾����
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

  if(! $USERS->is_appear('medium')) return false; //����νи������å�
  foreach($USERS->rows as $user){
    if($user->suicide_flag){
      InsertSystemMessage($user->handle_name . "\t" . $user->DistinguishCamp(), 'MEDIUM_RESULT');
    }
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
  $start_date_str = gmdate('Y, m, j, G, i, s', $start_time);
  $end_date_str   = gmdate('Y, m, j, G, i, s', $start_time + $base_time);
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
  mysql_query("DELETE FROM vote WHERE room_no = {$ROOM->id}");
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
