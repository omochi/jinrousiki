<?php
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/game_format.php');

//���å����ǧ�� �֤��� OK:�桼��̾ / NG: false
function CheckSession($session_id, $exit = true){
  global $room_no;
  // $ip_address = $_SERVER['REMOTE_ADDR']; //IP���ɥ쥹ǧ�ڤϸ��ߤϹԤäƤ��ʤ�

  //���å���� ID �ˤ��ǧ��
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND session_id ='$session_id' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  if(mysql_num_rows($sql) == 1) return $array['uname'];

  if($exit){ //���顼����
    OutputActionResult('���å����ǧ�ڥ��顼',
		       '���å����ǧ�ڥ��顼<br>'."\n" .
		       '<a href="index.php" target="_top">�ȥåץڡ���</a>����' .
		       '�����󤷤ʤ����Ƥ�������');
  }
  return false;
}

//HTML�إå�������
function OutputGamePageHeader(){
  global $GAME_CONF, $room_no, $game_option, $view_mode, $log_mode, $dead_mode,
    $heaven_mode, $date, $day_night, $live, $auto_reload, $play_sound, $list_down;

  //����ɴֳ֤�Ĵ��
  if($auto_reload != 0 && $auto_reload < $GAME_CONF->auto_reload_list[0])
    $auto_reload = $GAME_CONF->auto_reload_list[0];

  //�������Ǽ
  $url_header = 'game_frame.php?room_no=' . $room_no . '&auto_reload=' . $auto_reload;
  if($play_sound != '') $url_header .= '&play_sound=' . $play_sound;
  if($list_down  != '') $url_header .= '&list_down='  . $list_down;

  $title = '��Ͽ�ϵ�ʤ꤫��[�ץ쥤]';
  $body  = '<br>'."\n";

  if(preg_match('/Mac( OS|intosh|_PowerPC)/i', $_SERVER['HTTP_USER_AGENT'])){ //MAC ���ɤ���Ƚ��
    $location = '';  //MAC �� JavaScript �ǥ��顼��
    $body .= '<a href="';
    $body_footer = '">�����򥯥�å����Ƥ�������</a>';
  }
  else{
    $location = '<script type="text/javascript"><!--'."\n" .
      'if(top != self){ top.location.href = self.location.href; }'."\n" .
      '--></script>'."\n";
    $body .= '�ڤ��ؤ��ʤ��ʤ� <a href="';
    $body_footer = '" target="_top">����</a>';
  }

  //�������桢�������å⡼�ɤ˹Ԥ��Ȥ�
  if($day_night != 'aftergame' && $live == 'dead' && $view_mode != 'on' &&
     $log_mode != 'on' && $dead_mode != 'on' && $heaven_mode != 'on'){
    $url =  $url_header . '&dead_mode=on';
    OutputActionResult($title, $location . 'ŷ��⡼�ɤ��ڤ��ؤ��ޤ���' .
		       $body . $url . $body_footer, $url);
  }
  elseif($day_night == 'aftergame' && $dead_mode == 'on'){ //�����ब��λ�������ä������Ȥ�
    $url = $url_header;
    OutputActionResult($title, $location . '�����ཪλ��Τ����������Ӥޤ���' .
		       $body . $url . $body_footer, $url);
  }
  elseif($live == 'live' && ($dead_mode == 'on' || $heaven_mode == 'on')){
    $url = $url_header;
    OutputActionResult($title, $location . '��������̤����Ӥޤ���' .
		       $body . $url . $body_footer, $url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
  if($log_mode != 'on'){ //����������������
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load  = "change_css('$day_night');";
  }

  //��ư����ɤ򥻥å�
  if($auto_reload != 0 && $day_night != 'aftergame')
    echo '<meta http-equiv="Refresh" content="' . $auto_reload . '">'."\n";

  //�������桢�ꥢ�륿�������ʤ�в���֤� Javascript �ǥꥢ�륿����ɽ��
  if(($day_night == 'day' || $day_night == 'night') && strpos($game_option, 'real_time') !== false &&
     $heaven_mode != 'on' && $log_mode != 'on'){
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
  global $day_night;

  echo '<script type="text/javascript" src="javascript/output_realtime.js"></script>'."\n";
  echo '<script language="JavaScript"><!--'."\n";
  echo 'var realtime_message = "��' . ($day_night == 'day' ? '����' : '������') . '�ޤ� ";'."\n";
  echo 'var start_time = "' . $start_time . '";'."\n";
  echo 'var end_time = "'   . $end_time   . '";'."\n";
  echo '// --></script>'."\n";
}

//��ư�����Υ�󥯤����
function OutputAutoReloadLink($url){
  global $GAME_CONF, $auto_reload;

  echo '[��ư����](' . ($auto_reload == 0 ? $url . '0">�ڼ�ư��</a>' : $url . '0">��ư</a>');
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '��';
    echo  ' ' . ($auto_reload == $time ? $url . $time . '">��' . $name . '��</a>' :
		 $url . $time . '">' . $name . '</a>');
  }
  echo ')'."\n";
}

//���դ���¸�ԤοͿ������
function OutputGameOption(){
  global $MESSAGE, $room_no, $game_option;

  $real_time  = (strpos($game_option, 'real_time') !== false);
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);
  echo '<table class="time-table"><tr>'."\n";
  echo '<td>�����४�ץ���� (�ƥ���ɽ����) (ï���ǥ�����ͤ���)<br>';
  if($real_time){
    //�»��֤����»��֤����
    sscanf(strstr($game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
    echo '<div class="real-time">';
    echo "������֡� �� <span>{$day_minutes}</span>ʬ / �� <span>{$night_minutes}</span>ʬ";
    echo '</div>';
  }
  $option_list = array('dummy_boy', 'open_vote', 'not_open_cast', 'decide', 'authority',
		       'poison', 'cupid', 'boss_wolf', 'poison_wolf', 'mania', 'medium',
		       'liar', 'gentleman', 'sudden_death', 'quiz', 'chaosfull');
  foreach($option_list as $this_option){
    if(strpos($game_option, $this_option) !== false ||
       strpos($option_role, $this_option) !== false){
      $str = 'game_option_' . $this_option;
      echo $MESSAGE->$str . '��';
    }
  }
  if(strpos($game_option, 'chaos') !== false && strpos($game_option, 'chaosfull') === false){
    AddImgTag(&$option_img_str, $ROOM_IMG->$this_option, $MESSAGE->chaos);
  }
  $option_list = array('chaos_open_cast', 'secret_sub_role', 'no_sub_role');
  foreach($option_list as $this_option){
    if(strpos($game_option, $this_option) !== false ||
       strpos($option_role, $this_option) !== false){
      $str = 'game_option_' . $this_option;
      echo $MESSAGE->$str . '��';
    }
  }
  // echo "$game_option $option_role";

  echo '</td>';
  echo '</tr></table>'."\n";
}

//���դ���¸�ԤοͿ������
function OutputTimeTable(){
  global $room_no, $date;

  if($date < 1) return false; //�����ब�ϤޤäƤ��ʤ����ɽ�����ʤ�

  //��¸�Ԥο������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND live = 'live' AND user_no > 0");
  $count = mysql_result($sql, 0, 0);
  echo '<td>' . $date . ' ����<span>(��¸��' . $count . '��)</span></td>'."\n";
}

//�ץ쥤�䡼��������
function OutputPlayerList(){
  global $DEBUG_MODE, $GAME_CONF, $ICON_CONF, $room_no, $game_option, $day_night, $live;

  $sql = mysql_query("SELECT user_entry.uname,
			user_entry.handle_name,
			user_entry.profile,
			user_entry.live,
			user_entry.role,
			user_entry.user_no,
			user_icon.icon_filename,
			user_icon.color
			FROM user_entry, user_icon
			WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  $count  = mysql_num_rows($sql);
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  //�֥饦��������å� (MSIE @ Windows ���� ������ Alt, Title °���ǲ��ԤǤ���)
  //IE �ξ����Ԥ� \r\n �����졢����¾�Υ֥饦���ϥ��ڡ����ˤ���(������Alt°��)
  $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');

  echo '<div class="player"><table cellspacing="5"><tr>'."\n";
  for($i = 0; $i < $count; $i++){
    //5�Ĥ��Ȥ��������
    if($i > 0 && ($i % 5) == 0) echo '</tr>'."\n".'<tr>'."\n";

    $array = mysql_fetch_assoc($sql);
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_profile = $array['profile'];
    $this_live    = $array['live'];
    $this_role    = $array['role'];
    $this_user_no = $array['user_no'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];
    $profile_alt  = str_replace("\n", $replace, $this_profile);
    if($DEBUG_MODE) $this_handle .= ' (' . $this_user_no . ')';

    //��������
    $path = $ICON_CONF->path . '/' . $this_file;
    $img_tag = '<img title="' . $profile_alt . '" alt="' . $profile_alt .
      '" style="border-color: ' . $this_color . ';"';
    if($this_live == 'live') //�����Ƥ���Х桼����������
      $this_live_str = '(��¸��)';
    else{ //���Ǥ�л�˴��������
      $this_live_path = $path; //��������Υѥ��������ؤ�
      $path           = $ICON_CONF->dead;
      $this_live_str  = '(��˴)';
      $img_tag .= " onMouseover=\"this.src='$this_live_path'\" onMouseout=\"this.src='$path'\"";
    }
    $img_tag .= ' width="' . $width . '" height="' . $height . '"';
    $img_tag .= ' src="' . $path . '">';

    //�����ཪλ�夫��˴�夫����򿦸����ξ��ϡ��򿦡��桼���͡����ɽ��
    if($day_night == 'aftergame' ||
       ($live == 'dead' && strpos($game_option, 'not_open_cast') === false)){
      $role_str = '';
      if(strpos($this_role, 'human') !== false)
	$role_str = MakeRoleName('human');
      elseif(strpos($this_role, 'boss_wolf') !== false)
	$role_str = MakeRoleName('boss_wolf', 'wolf');
      elseif(strpos($this_role, 'poison_wolf') !== false)
	$role_str = MakeRoleName('poison_wolf', 'wolf');
      elseif(strpos($this_role, 'tongue_wolf') !== false)
	$role_str = MakeRoleName('tongue_wolf', 'wolf');
      elseif(strpos($this_role, 'cute_wolf') !== false)
	$role_str = MakeRoleName('cute_wolf', 'wolf');
      elseif(strpos($this_role, 'wolf') !== false)
	$role_str = MakeRoleName('wolf');
      elseif(strpos($this_role, 'soul_mage') !== false)
	$role_str = MakeRoleName('soul_mage', 'mage');
      elseif(strpos($this_role, 'dummy_mage') !== false)
	$role_str = MakeRoleName('dummy_mage', 'mage');
      elseif(strpos($this_role, 'mage') !== false)
	$role_str = MakeRoleName('mage');
      elseif(strpos($this_role, 'necromancer') !== false)
	$role_str = MakeRoleName('necromancer');
      elseif(strpos($this_role, 'medium') !== false)
	$role_str = MakeRoleName('medium', 'necromancer');
      elseif(strpos($this_role, 'fanatic_mad') !== false)
	$role_str = MakeRoleName('fanatic_mad', 'mad');
      elseif(strpos($this_role, 'mad') !== false)
	$role_str = MakeRoleName('mad');
      elseif(strpos($this_role, 'poison_guard') !== false)
	$role_str = MakeRoleName('poison_guard', 'guard');
      elseif(strpos($this_role, 'guard') !== false)
	$role_str = MakeRoleName('guard');
      elseif(strpos($this_role, 'reporter') !== false)
	$role_str = MakeRoleName('reporter', 'guard');
      elseif(strpos($this_role, 'common') !== false)
	$role_str = MakeRoleName('common');
      elseif(strpos($this_role, 'child_fox') !== false)
	$role_str = MakeRoleName('child_fox', 'fox');
      elseif(strpos($this_role, 'fox') !== false)
	$role_str = MakeRoleName('fox');
      elseif(strpos($this_role, 'poison_cat') !== false)
	$role_str = MakeRoleName('poison_cat', 'poison');
      elseif(strpos($this_role, 'poison') !== false)
	$role_str = MakeRoleName('poison');
      elseif(strpos($this_role, 'pharmacist') !== false)
	$role_str = MakeRoleName('pharmacist', 'poison');
      elseif(strpos($this_role, 'suspect') !== false)
	$role_str = MakeRoleName('suspect', 'human');
      elseif(strpos($this_role, 'unconscious') !== false)
	$role_str = MakeRoleName('unconscious', 'human');
      elseif(strpos($this_role, 'cupid') !== false)
	$role_str = MakeRoleName('cupid');
      elseif(strpos($this_role, 'mania') !== false)
	$role_str = MakeRoleName('mania');
      elseif(strpos($this_role, 'quiz') !== false)
	$role_str = MakeRoleName('quiz');

      //���������Ǥ��
      if(strpos($this_role, 'lovers') !== false)
	$role_str .= MakeRoleName('lovers', '', true);
      if(strpos($this_role, 'copied') !== false)
	$role_str .= MakeRoleName('copied', 'mania', true);

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
      elseif(strpos($this_role, 'random_voice') !== false)
	$role_str .= MakeRoleName('random_voice', 'voice', true);

      if(strpos($this_role, 'no_last_words') !== false)
	$role_str .= MakeRoleName('no_last_words', 'seal', true);
      if(strpos($this_role, 'blinder') !== false)
	$role_str .= MakeRoleName('blinder', 'seal', true);
      if(strpos($this_role, 'earplug') !== false)
	$role_str .= MakeRoleName('earplug', 'seal', true);

      if(strpos($this_role, 'silent') !== false)
	$role_str .= MakeRoleName('silent', 'convert', true);
      if(strpos($this_role, 'liar') !== false)
	$role_str .= MakeRoleName('liar', 'convert', true);
      if(strpos($this_role, 'invisible') !== false)
	$role_str .= MakeRoleName('invisible', 'convert', true);
      if(strpos($this_role, 'gentleman') !== false)
	$role_str .= MakeRoleName('gentleman', 'convert', true);
      elseif(strpos($this_role, 'lady') !== false)
	$role_str .= MakeRoleName('lady', 'convert', true);

      if(strpos($this_role, 'chicken') !== false)
	$role_str .= MakeRoleName('chicken', 'sudden-death', true);
      elseif(strpos($this_role, 'rabbit') !== false)
	$role_str .= MakeRoleName('rabbit', 'sudden-death', true);
      elseif(strpos($this_role, 'perverseness') !== false)
	$role_str .= MakeRoleName('perverseness', 'sudden-death', true);
      elseif(strpos($this_role, 'flattery') !== false)
	$role_str .= MakeRoleName('flattery', 'sudden-death', true);
      elseif(strpos($this_role, 'impatience') !== false)
	$role_str .= MakeRoleName('impatience', 'sudden-death', true);

      echo "<td>${img_tag}</td>"."\n";
      echo "<td><font color=\"$this_color\">��</font>$this_handle<br>"."\n";
      echo "��($this_uname)<br> $role_str";
    }
    elseif($day_night == 'beforegame'){ //��������
      //�����ॹ�����Ȥ���ɼ���Ƥ��뤫�����Ƥ���п����Ѥ���
      $sql_start = mysql_query("select count(uname) from vote where room_no = $room_no
				and situation = 'GAMESTART' and uname = '$this_uname'");

      if(mysql_result($sql_start, 0, 0) == 1 || $this_uname == 'dummy_boy')
	$already_vote_class = ' class="already-vote"';
      else
	$already_vote_class = '';

      echo "<td${already_vote_class}>{$img_tag}</td>"."\n";
      echo "<td${already_vote_class}><font color=\"$this_color\">��</font>$this_handle";
    }
    else{ //�����Ƥ��ƥ�������
      echo "<td>{$img_tag}</td>"."\n";
      echo "<td><font color=\"$this_color\">��</font>$this_handle";
    }
    echo '<br>'."\n" . $this_live_str . '</td>'."\n";
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

//��°�ر�Ƚ��
function DistinguishCamp($role){
  if(strpos($role, 'wolf')  !== false || strpos($role, 'mad') !== false) return 'wolf';
  if(strpos($role, 'fox')   !== false) return 'fox';
  if(strpos($role, 'cupid') !== false) return 'lovers';
  if(strpos($role, 'quiz')  !== false) return 'quiz';
  return 'human';
}

//���Ԥν���
function OutputVictory(){
  global $MESSAGE, $room_no, $view_mode, $log_mode, $role;

  //�����رĤ����
  $sql = mysql_query("SELECT victory_role FROM room WHERE room_no = $room_no");
  $victory = mysql_result($sql, 0, 0);
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
  if($victory == NULL || $view_mode == 'on' || $log_mode == 'on') return;

  $result = 'win';
  $camp   = DistinguishCamp($role); //��°�رĤ����
  $lovers = (strpos($role, 'lovers') !== false);
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
  global $GAME_CONF, $MESSAGE, $SOUND, $room_no, $view_mode, $date, $day_night,
    $uname, $play_sound, $cookie_vote_times;

  if($day_night != 'day') return false; //��ʳ��Ͻ��Ϥ��ʤ�

  //����ɼ�β�������
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $date AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) == 0) return false;

  //�����ܤκ���ɼ�ʤΤ�����
  $last_vote_times = (int)mysql_result($sql, 0, 0);

  //�����Ĥ餹
  if($play_sound == 'on' && $view_mode != 'on' && $last_vote_times > $cookie_vote_times)
    OutputSound($SOUND->revote);

  //��ɼ�Ѥߥ����å�
  $this_vote_times = $last_vote_times + 1;
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND vote_times = $this_vote_times AND uname = '$uname'");
  if(mysql_result($sql, 0, 0) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '��' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  OutputVoteListDay($date); //��ɼ��̤����
}

//�����Ĥ餹
function OutputSound($sound, $loop = false){
  if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$sound}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$sound}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
}

//���å�����
function OutputTalkLog(){
  global $MESSAGE, $room_no, $game_option, $status, $date, $day_night, $uname, $role, $live;

  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry,talk,user_icon
			WHERE talk.room_no = $room_no
			AND talk.location LIKE '$day_night%'
			AND talk.date = $date
			AND ((user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR (user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no))
			ORDER BY time DESC");
  $count = mysql_num_rows($sql);

  $builder = new DocumentBuilder();
  $builder->BeginTalk('talk');
  for($i = 0; $i < $count; $i++) OutputTalk(mysql_fetch_assoc($sql), $builder); //���ý���
  $builder->EndTalk();
}

//���ý���
function OutputTalk($array, &$builder){
  global $GAME_CONF, $MESSAGE, $game_option, $add_role, $status, $day_night, $uname, $live, $role;

  $talk_uname       = $array['talk_uname'];
  $talk_handle_name = $array['talk_handle_name'];
  $talk_sex         = $array['talk_sex'];
  $talk_color       = $array['talk_color'];
  $sentence         = $array['sentence'];
  $font_type        = $array['font_type'];
  $location         = $array['location'];

  if($add_role == 'on'){ //��ɽ���⡼���б�
    $talk_handle_name .= '<span class="add-role"> [' .
      MakeShortRoleName($array['talk_role']) . '] (' . $talk_uname . ')</span>';
  }

  LineToBR(&$sentence); //���ԥ����ɤ� <br> ���Ѵ�
  $location_system = (strpos($location, 'system') !== false);
  $flag_vote       = (strpos($sentence, 'VOTE_DO')       === 0);
  $flag_wolf       = (strpos($sentence, 'WOLF_EAT')      === 0);
  $flag_mage       = (strpos($sentence, 'MAGE_DO')       === 0);
  $flag_guard      = (strpos($sentence, 'GUARD_DO')      === 0);
  $flag_reporter   = (strpos($sentence, 'REPORTER_DO')   === 0);
  $flag_cupid      = (strpos($sentence, 'CUPID_DO')      === 0);
  $flag_mania      = (strpos($sentence, 'MANIA_DO')      === 0);
  $flag_poison_cat = (strpos($sentence, 'POISON_CAT_DO') === 0);
  $flag_system = ($location_system &&
		  ($flag_vote  || $flag_wolf || $flag_mage || $flag_guard || $flag_reporter ||
		   $flag_cupid || $flag_mania || $flag_poison_cat));

  if($location_system && $sentence == 'OBJECTION'){ //�۵Ĥ���
    $builder->AddSystemTalk(
      $talk_handle_name . ' ' . $MESSAGE->objection,
      'objection-'.$talk_sex
    );
  }
  elseif($location_system && $sentence == 'GAMESTART_DO'){ //�����೫����ɼ
    /*
      echo '<tr class="system-message">'."\n";
      echo '<td class="game-start" colspan="2">' . $talk_handle_name . ' ' .
      $MESSAGE->game_start . '</td>'."\n";
      echo '</tr>'."\n";
    */
  }
  elseif($location_system && strpos($sentence, 'KICK_DO') === 0){ //KICK ��ɼ
    $target_handle_name = ParseStrings($sentence, 'KICK_DO');
    $builder->AddSystemMessage(
      'kick',
      "{$talk_handle_name} �� {$target_handle_name} {$MESSAGE->kick_do}"
    );
  }
  //��¸�����ɼ�������ɽ��
  elseif($live == 'live' && $flag_system){
  }
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
  //�����귯���ѥ����ƥ��å�����
  elseif(strpos($location, 'dummy_boy') !== false){
    $builder->AddSystemTalk($MESSAGE->dummy_boy . $sentence);
  }
  //�������Ƚ�λ�� || �������桢�����Ƥ���ͤ���
  elseif($day_night == 'beforegame' || $day_night == 'aftergame' ||
	 ($live == 'live' && $day_night == 'day' && $location == 'day')){
    if($day_night == 'day' || $day_night == 'night'){ //����������������Υ�����ȯư
      if(strpos($role, 'blinder') !== false && $uname != $talk_uname) $talk_handle_name = '';
      if(strpos($role, 'earplug') !== false){
      	switch($font_type){
      	case 'strong':
      	  $font_type = 'normal';
      	  break;

      	case 'normal':
      	  $font_type = 'weak';
      	  break;

      	case 'weak':
      	  $sentence = '';
      	  break;
      	}
      }
    }
    if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
    $builder->AddTalk("<font color=\"{$talk_color}\">��</font>{$talk_handle_name}", $sentence, $font_type);
  }
  //�������桢�����Ƥ���ͤ����ϵ
  elseif($live == 'live' && $day_night == 'night' && $location == 'night wolf'){
    if(strpos($role, 'earplug') !== false){
      switch($font_type){
      case 'strong':
      	$font_type = 'normal';
      	break;

      case 'normal':
      	$font_type = 'weak';
      	break;

      case 'weak':
      	$sentence = '';
      	break;
      }
    }
    if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
    if(strpos($role, 'wolf') !== false){
      if(strpos($role, 'blinder') !== false && $uname != $talk_uname) $talk_handle_name = '';
      $builder->AddTalk("<font color=\"$talk_color\">��</font>$talk_handle_name", $sentence, $font_type);
    }
    else{
      $builder->AddWhisper('ϵ�α��ʤ�', $MESSAGE->wolf_howl, $font_type);
    }
  }
  //�������桢�����Ƥ���ͤ���ζ�ͭ��
  elseif($live == 'live' && $day_night == 'night' && $location == 'night common'){
    if(strpos($role, 'earplug') !== false){
      switch($font_type){
      case 'strong':
      	$font_type = 'normal';
      	break;

      case 'normal':
      	$font_type = 'weak';
      	break;

      case 'weak':
	$sentence = '';
	break;
      }
    }
    if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
    if(strpos($role, 'common') !== false){
      if(strpos($role, 'blinder') !== false && $uname != $talk_uname) $talk_handle_name = '';
      $builder->AddTalk("<font color=\"{$talk_color}\">��</font>$talk_handle_name");
    }
    else{
      $builder->AddWhisper('��ͭ�Ԥξ���', $MESSAGE->common_talk, '', 'talk-common', 'say-common');
    }
  }
  //�������桢�����Ƥ���ͤ�����Ÿ�
  elseif($live == 'live' && $day_night == 'night' && $location == 'night fox'){
    if(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false){
      if(strpos($role, 'blinder') !== false && $uname != $talk_uname) $talk_handle_name = '';
      if(strpos($role, 'earplug') !== false){
      	switch($font_type){
      	case 'strong':
      	  $font_type = 'normal';
      	  break;

      	case 'normal':
      	  $font_type = 'weak';
       	  break;

      	case 'weak':
      	  $sentence = '';
      	  break;
      	}
      }
      if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
      $builder->AddTalk("<font color=\"{$talk_color}\">��</font>{$talk_handle_name}", $sentence, $font_type);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ȥ��
  elseif($live == 'live' && $day_night == 'night' && $location == 'night self_talk'){
    if($uname == $talk_uname){
      if(strpos($role, 'earplug') !== false){
      	switch($font_type){
      	case 'strong':
      	  $font_type = 'normal';
      	  break;

      	case 'normal':
      	  $font_type = 'weak';
      	  break;

      	case 'weak':
      	  $sentence = '';
          break;
      	}
      }
      if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
      $builder->AddTalk("<font color=\"$talk_color\">��</font>$talk_handle_name<span>���Ȥ��</span>", $sentence, $font_type);
    }
  }
  //�����ཪλ / �����귯(����GM��) / �������桢��˴��(��������ץ��������Բ�)
  elseif($status == 'finished' || $uname == 'dummy_boy' ||
	 ($live == 'dead' && strpos($game_option, 'not_open_cast') === false)){
    if($location_system && $flag_vote){ //�跺��ɼ
      $target_handle_name = ParseStrings($sentence, 'VOTE_DO');
      $action = 'vote';
      $sentence =  $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->vote_do;
    }
    elseif($location_system && $flag_wolf){ //ϵ����ɼ
      $target_handle_name = ParseStrings($sentence, 'WOLF_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name . ' ������ϵ�� ' .  $target_handle_name . ' ' . $MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_mage){ //�ꤤ�դ���ɼ
      $target_handle_name = ParseStrings($sentence, 'MAGE_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->mage_do;
    }
    elseif($location_system && $flag_guard){ //��ͤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'GUARD_DO');
      $action = 'guard-do';
      $sentence =  $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->guard_do;
    }
    elseif($location_system && $flag_reporter){ //�֥󲰤���ɼ
      $target_handle_name = ParseStrings($sentence, 'REPORTER_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->reporter_do;
    }
    elseif($location_system && $flag_cupid){ //���塼�ԥåɤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'CUPID_DO');
      $action = 'cupid-do';
      $sentence = $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->cupid_do;
    }
    elseif($location_system && $flag_mania){ //���åޥ˥�����ɼ
      $target_handle_name = ParseStrings($sentence, 'MANIA_DO');
      $action = 'mania-do';
      $sentence = $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->mania_do;
    }
    elseif($location_system && $flag_poison_cat){ //ǭ������ɼ
      $target_handle_name = ParseStrings($sentence, 'POISON_CAT_DO');
      $action = 'mania-do';
      $sentence = $talk_handle_name . ' �� ' .  $target_handle_name . ' ' . $MESSAGE->poison_cat_do;
    }
    else{ //����¾�����Ƥ�ɽ��(��Ԥξ��)
      if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
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
    if (isset($action)){
      $builder->AddSystemMessage($action, $sentence);
    }
    else {
      $builder->AddTalk(
        "<font color=\"{$talk_color}\">��</font>{$talk_handle_name}",
        $sentence,
        $font_type,
        $base_class,
        $talk_class
      );
    }
  }
  else{ //�����
    if(strpos($role, 'blinder') !== false && $uname != $talk_uname) $talk_handle_name = '';
    if(strpos($role, 'earplug') !== false){
      switch($font_type){
      case 'strong':
	$font_type = 'normal';
	break;

      case 'normal':
	$font_type = 'weak';
	break;

      case 'weak':
	$sentence = '';
	break;
      }
    }
    if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';
    if($day_night == 'night' && $location == 'night wolf'){
      if(strpos($role, 'wolf') !== false){
      	$talk_handle_name = '<font color="' . $talk_color . '">��</font>' . $talk_handle_name;
      }
      else{
      	$talk_handle_name = 'ϵ�α��ʤ�';
      	$sentence = $MESSAGE->wolf_howl;
      }
      $builder->AddTalk($talk_handle_name, $sentence, $font_type);
    }
    elseif($day_night == 'night' && $location == 'night common'){
      if(strpos($role, 'common') !== false){
        $builder->AddTalk("<font color=\"$talk_color\">��</font>$talk_handle_name", $sentence, $font_type);
      }
      else{
        $builder->AddWhisper('��ͭ�Ԥξ���', $MESSAGE->common_talk, '', 'talk-common', 'say-common');
      }
    }
    elseif($day_night == 'night' && $location == 'night fox'){
      if(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false){
        $builder->AddTalk("<font color=\"$talk_color\">��</font>$talk_handle_name", $sentence, $font_type);
      }
    }
    elseif(! (($day_night == 'night' && $location == 'night self_talk') || $flag_system)){
      $builder->AddTalk("<font color=\"$talk_color\">��</font>$talk_handle_name", $sentence, $font_type);
    }
  }
}

//��˴�Ԥΰ�������
function OutputLastWords(){
  global $MESSAGE, $room_no, $date, $day_night;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if($day_night == 'beforegame' || $day_night == 'aftergame') return false;

  //�����λ�˴�԰�������
  $set_date = $date - 1;
  $sql = mysql_query("SELECT message, MD5(RAND()*NOW()) AS rand FROM system_message
			WHERE room_no = $room_no AND date = $set_date
			AND type = 'LAST_WORDS' ORDER BY rand");
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
  global $room_no, $date, $day_night, $log_mode;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if($day_night == 'beforegame' || $day_night == 'aftergame') return false;

  $yesterday = $date - 1;

  //���̥�����
  $query_header = "SELECT message, type, MD5(RAND()*NOW()) AS MyRand FROM system_message " .
    "WHERE room_no = $room_no AND date =";

  //�跺��å��������ǻ��å�����(��)
  $type_day = "type = 'VOTE_KILLED' OR type = 'POISON_DEAD_day' OR type = 'LOVERS_FOLLOWED_day' " .
    "OR type LIKE 'SUDDEN_DEATH%'";

  //����������˵����ä���˴��å�����
  $type_night = "type = 'WOLF_KILLED' OR type = 'FOX_DEAD' OR type = 'REPORTER_DUTY' " .
    "OR type = 'POISON_DEAD_night' OR type = 'LOVERS_FOLLOWED_night'";

  if($day_night == 'day'){
    $set_date = $yesterday;
    $type = $type_night;
  }
  else{
    $set_date = $date;
    $type = $type_day;
  }

  $sql = mysql_query("$query_header $set_date AND ( $type ) ORDER BY MyRand");
  $count = mysql_num_rows($sql); //��˴�ԤοͿ�
  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);
    OutputDeadManType($array['message'], $array['type']); //��ԤΥϥ�ɥ�͡���ȥ�����
  }

  //�������⡼�ɰʳ��ʤ���������˴�ԥ�å�����ɽ��
  if($log_mode == 'on') return;
  $set_date = $yesterday;
  $type = ($day_night == 'day' ? $type_day : $type_night);

  $sql = mysql_query("$query_header $set_date AND ( $type ) ORDER BY MyRand");
  $count = mysql_num_rows($sql); //��˴�ԤοͿ�
  for($i=0 ; $i < $count ;$i++){
    $array = mysql_fetch_assoc($sql);
    OutputDeadManType($array['message'], $array['type']);
  }
}

//��ԤΥ������̤˻�˴��å����������
function OutputDeadManType($name, $type){
  global $live, $game_option, $day_night, $status, $MESSAGE;

  $deadman_header = '<tr><td>'.$name.' '; //���ܥ�å������إå�
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //���ܥ�å�����
  $sudden_death   = $deadman_header.$MESSAGE->vote_sudden_death.'</td>'; //��������
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //�ɲö��̥إå�
  $show_reason = ($status == 'finished' ||
		  ($live == 'dead' && strpos($game_option, 'not_open_cast') === false));

  echo '<table class="dead-type">'."\n";
  switch($type){
  case 'WOLF_KILLED':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->wolf_killed.')</td>';
    break;

  case 'FOX_DEAD':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->fox_dead.')</td>';
    break;

  case 'POISON_DEAD_day':
  case 'POISON_DEAD_night':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->poison_dead.')</td>';
    break;

  case 'VOTE_KILLED':
    echo '<tr class="dead-type-vote">';
    echo '<td>'.$name.' '.$MESSAGE->vote_killed.'</td>';
    break;

  case 'REPORTER_DUTY':
    echo $deadman;
    if($show_reason) echo $reason_header.$MESSAGE->reporter_duty.')</td>';
    break;

  case 'LOVERS_FOLLOWED_day':
  case 'LOVERS_FOLLOWED_night':
    echo '<tr><td>'.$name.' '.$MESSAGE->lovers_followed.'</td>';
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
  }
  echo "</tr>\n</table>\n";
}

//��ɼ�ν��׽���
function OutputVoteList(){
  global $date, $day_night, $log_mode;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if($day_night == 'beforegame' || $day_night == 'aftergame' ) return false;

  if($day_night == 'day' && $log_mode != 'on') //����ä����������ν��פ����
    OutputVoteListDay($date - 1);
  else //����ä��麣���ν��פ����
    OutputVoteListDay($date);
}

//���ꤷ�����դ���ɼ��̤���Ϥ���
function OutputVoteListDay($set_date){
  global $room_no, $game_option, $live, $reverse_log, $view_mode;

  //���ꤵ�줿���դ���ɼ��̤����
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
		      AND date = $set_date and type = 'VOTE_KILL'");
  if(mysql_num_rows($sql) == 0) return false;

  $result_array = array(); //��ɼ��̤��Ǽ����
  $this_vote_times = -1; //���Ϥ�����ɼ�����Ͽ
  $this_vote_count = mysql_num_rows($sql); //��ɼ���
  $table_count = 0; //ɽ�θĿ�

  for($i=0; $i < $this_vote_count; $i++){ //���ä�������˳�Ǽ����
    $vote_array = mysql_fetch_assoc($sql);
    $vote_message = $vote_array['message'];

    //���ֶ��ڤ�Υǡ�����ʬ�䤹��
    list($handle_name, $target_name, $voted_number,
	 $vote_number, $vote_times) = ParseStrings($vote_message, 'VOTE');

    if($this_vote_times != $vote_times){ //��ɼ������㤦�ǡ��������̥ơ��֥�ˤ���
      if($this_vote_times != -1)
	array_push($result_array[$this_vote_times], '</table>'."\n");

      $this_vote_times = $vote_times;
      $result_array[$vote_times] = array();
      array_push($result_array[$vote_times], '<table class="vote-list">'."\n");
      array_push($result_array[$vote_times], '<td class="vote-times" colspan="4">' .
		 $set_date . ' ���� ( ' . $vote_times . ' ����)</td>'."\n");

      $table_count++;
    }

    if((strpos($game_option, 'open_vote') !== false || $live == 'dead') && $view_mode != 'on')
      $vote_number_str = '��ɼ�� ' . $vote_number . ' ɼ ��';
    else
      $vote_number_str = '��ɼ�袪';

    //ɽ��������å�����
    $this_vote_message = '<tr><td class="vote-name">' . $handle_name . '</td><td>' .
      $voted_number . ' ɼ</td><td>' . $vote_number_str .
      '</td><td class="vote-name"> ' . $target_name . ' </td></tr>'."\n";

    array_push($result_array[$vote_times], $this_vote_message);
  }
  array_push($result_array[$this_vote_times], '</table>'."\n");

  if($reverse_log == 'on'){ //�ս�ɽ��
    //����˳�Ǽ���줿�ǡ��������
    for($i = 1; $i <= $table_count; $i++){
      $this_vote_count = (int)count($result_array[$i]);
      for($j = 0; $j < $this_vote_count; $j++) echo $result_array[$i][$j];
    }
  }
  else{
    //����˳�Ǽ���줿�ǡ��������
    for($i = $table_count; $i > 0; $i--){
      $this_vote_count = (int)count($result_array[$i]);
      for($j = 0; $j < $this_vote_count; $j++) echo $result_array[$i][$j];
    }
  }
}

//�ꤦ��ϵ����������Ҥ�������ǽ�Ϥ�Ȥ���å�����
function OutputAbilityAction(){
  global $room_no, $date, $day_night, $game_option;

  //��֤��򿦸��������Ĥ���Ƥ���Ȥ��Τ�ɽ��
  if(strpos($game_option, 'not_open_cast') !== false || $day_night != 'day') return false;

  $yesterday = $date - 1;
  $result = mysql_query("SELECT message,type FROM system_message WHERE room_no = $room_no
			 AND date = $yesterday AND (type = 'MAGE_DO' OR type = 'WOLF_EAT'
			 OR type = 'GUARD_DO' OR type = 'REPORTER_DO' OR type = 'CUPID_DO'
			 OR type = 'MANIA_DO' OR type = 'POISON_CAT_DO')");
  $count = mysql_num_rows($result);
  $header = '<strong>�������롢';
  $footer = '�ޤ���</strong><br>'."\n";

  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($result);
    $message = $array['message'];
    $type    = $array['type'];

    list($handle_name, $target_name) = ParseStrings($message);
    switch($type){
    case 'WOLF_EAT':
      echo $header.$handle_name.' ���ϵ������ '.$target_name.' ������'.$footer;
      break;

    case 'MAGE_DO':
      echo $header.'�ꤤ�� '.$handle_name.'�� '.$target_name.' ���ꤤ'.$footer;
      break;

    case 'GUARD_DO':
      echo $header.'��� '.$handle_name.' �� '.$target_name.' ���Ҥ�'.$footer;
      break;

    case 'REPORTER_DO':
      echo $header.'�֥� '.$handle_name.' �� '.$target_name.'�����Ԥ�'.$footer;
      break;

    case 'CUPID_DO':
      echo $header.'���塼�ԥå� '.$handle_name.' �� '.$target_name.'�˰����������'.$footer;
      break;

    case 'MANIA_DO':
      echo $header.'���åޥ˥� '.$handle_name.' �� '.$target_name.'�򿿻�'.$footer;
      break;

    case 'POISON_CAT_DO':
      echo $header.'ǭ�� '.$handle_name.' �� '.$target_name.'���������֤�'.$footer;
      break;
    }
  }
}

//���Ԥ�����å�
function CheckVictory($check_draw = false){
  global $GAME_CONF, $room_no, $game_option, $date, $day_night, $vote_times;

  $query_count = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND ";

  //ϵ�ο������
  $sql = mysql_query($query_count . "role LIKE '%wolf%'");
  $wolf = (int)mysql_result($sql, 0, 0);

  //ϵ���Ѱʳ��ο������
  $sql = mysql_query($query_count . "!(role LIKE '%wolf%') AND !(role LIKE '%fox%')");
  $human = (int)mysql_result($sql, 0, 0);

  //�Ѥο������
  $sql = mysql_query($query_count . "role LIKE '%fox%'");
  $fox = (int)mysql_result($sql, 0, 0);

  //���ͤο������
  $sql = mysql_query($query_count . "role LIKE '%lovers%'");
  $lovers = (int)mysql_result($sql, 0, 0);

  //������¼ GM �ο������
  $sql = mysql_query($query_count . "role LIKE 'quiz%'");
  $quiz = (int)mysql_result($sql, 0, 0);

  $victory_role = ''; //�����ر�
  if($wolf == 0 && $human == 0 && $fox == 0){ //����
    if($quiz > 0) $victory_role = 'quiz';
    else          $victory_role = 'vanish';
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
  elseif($check_draw && $vote_times >= $GAME_CONF->draw) //����ʬ��
    $victory_role = 'draw';
  elseif(strpos($game_option, 'quiz') !== false && $quiz == 0) //������¼ GM ��˴
    $victory_role = 'quiz_dead';

  if($victory_role != ''){
    mysql_query("UPDATE room SET status = 'finished', day_night = 'aftergame',
			victory_role = '$victory_role' WHERE room_no = $room_no");
    mysql_query('COMMIT'); //������ߥå�
  }
}

//��˴����
function KillUser($uname){
  global $room_no;

  mysql_query("UPDATE user_entry SET live = 'dead' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
  mysql_query('COMMIT'); //���ߥå�
}

//��������������¸���� ($target : HN)
function SaveLastWords($target){
  global $room_no;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $last_words = mysql_result($sql, 0, 0);
  if($last_words != ''){
    InsertSystemMessage($target . "\t" . $last_words, 'LAST_WORDS');
  }
}

//���������
function SuddenDeath($uname, $handle_name, $role, $type = NULL){
  global $MESSAGE, $system_time, $room_no;

  //������ǧ
  $sql = mysql_query("SELECT live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 'live') return false;

  KillUser($uname); //������¹�
  if($type){ //����å�������Ѥν�����Ԥ�
    InsertSystemTalk($handle_name . $MESSAGE->vote_sudden_death, ++$system_time);
    InsertSystemMessage($handle_name, 'SUDDEN_DEATH_' . $type);
    SaveLastWords($handle_name);
  }
  else InsertSystemTalk($handle_name . $MESSAGE->sudden_death, ++$system_time);

  //���ͤθ��ɤ�����
  if(strpos($role, 'lovers') !== false) LoversFollowed($role, true);

  //�����Ƚ����(�����ƥ��å�����)
  InsertSystemMessage($handle_name . "\t" . DistinguishCamp($role), 'MEDIUM_RESULT');
  mysql_query('COMMIT'); //������ߥå�
}

//���ͤ�Ĵ�٤륯����ʸ��������
function GetLoversConditionString($role){
  $match_count = preg_match_all("/lovers\[\d+\]/", $role, $matches, PREG_PATTERN_ORDER);
  if($match_count <= 0) return '';

  $val = $matches[0];
  $str = "( role LIKE '%$val[0]%'";
  for($i = 1; $i < $match_count; $i++) $str .= " OR role LIKE '%$val[$i]%'";
  $str .= " )";
  return $str;
}

//���ͤθ��ɤ������
function LoversFollowed($role, $sudden_death = false){
  global $MESSAGE, $system_time, $room_no, $date, $day_night;

  //���ɤ�������ɬ�פ��������ͤ����
  $query = "SELECT uname, handle_name, role, last_words FROM user_entry
		WHERE room_no = $room_no AND live = 'live' AND user_no > 0 AND ";
  $query .= GetLoversConditionString($role);
  $sql = mysql_query($query);

  while(($array = mysql_fetch_assoc($sql)) !== false){
    $target_uname      = $array['uname'];
    $target_handle     = $array['handle_name'];
    $target_role       = $array['role'];
    $target_last_words = $array['last_words'];

    KillUser($target_uname); //���ɤ���

    if($sudden_death) //������ν���
      InsertSystemTalk($target_handle . $MESSAGE->lovers_followed, ++$system_time);
    else {
      //���ɤ���(�����ƥ��å�����)
      InsertSystemMessage($target_handle, 'LOVERS_FOLLOWED_' . $day_night);

      //���ɤ������ͤΰ����Ĥ�
      if($target_last_words != '')
	InsertSystemMessage($target_handle . "\t" . $target_last_words, 'LAST_WORDS');
    }

    //�����Ƚ����(�����ƥ��å�����)
    InsertSystemMessage($target_handle . "\t" . DistinguishCamp($target_role), 'MEDIUM_RESULT');

    //���ɤ�Ϣ������
    LoversFollowed($target_role, $sudden_death);
  }
}

//�ꥢ�륿����ηв����
function GetRealPassTime(&$left_time, $flag = false){
  global $system_time, $room_no, $game_option, $date, $day_night;

  $time_str = strstr($game_option, 'real_time');
  //�»��֤����»��֤����
  sscanf($time_str, 'real_time:%d:%d', &$day_minutes, &$night_minutes);
  $day_time   = $day_minutes   * 60; //�äˤʤ���
  $night_time = $night_minutes * 60; //�äˤʤ���

  //�Ǥ⾮���ʻ���(���̤κǽ�λ���)�����
  $sql = mysql_query("SELECT MIN(time) FROM talk WHERE room_no = $room_no
			AND date = $date AND location LIKE '$day_night%'");
  $start_time = (int)mysql_result($sql, 0, 0);

  if($start_time != NULL){
    $pass_time = $system_time - $start_time; //�вᤷ������
  }
  else{
    $pass_time = 0;
    $start_time = $system_time;
  }
  $base_time = ($day_night == 'day' ? $day_time : $night_time);
  $left_time = $base_time - $pass_time;
  if($left_time < 0) $left_time = 0; //�ޥ��ʥ��ˤʤä��饼��ˤ���
  if(! $flag) return;

  $start_date_str = gmdate('Y, m, j, G, i, s', $start_time);
  $end_date_str   = gmdate('Y, m, j, G, i, s', $start_time + $base_time);
  return array($start_date_str, $end_date_str);
}

//���äǻ��ַв����ηв����
function GetTalkPassTime(&$left_time, $flag = false){
  global $TIME_CONF, $room_no, $date, $day_night;

  $sql = mysql_query("SELECT SUM(spend_time) FROM talk WHERE room_no = $room_no
			AND date = $date AND location LIKE '$day_night%'");
  $spend_time = (int)mysql_result($sql, 0, 0);

  if($day_night == 'day'){ //���12����
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

//�����򿦤�ȴ���Ф����֤�
function GetMainRole($target_role){
  global $GAME_CONF;

  foreach($GAME_CONF->main_role_list as $this_role => $this_name){
    if(ereg("^{$this_role}([[:space:]]+[^[[:space:]]]*)?", $target_role)) return $this_role;
  }
  return NULL;
}

//�򿦤�ѡ������ƾ�ά̾���֤�
function MakeShortRoleName($target_role){
  global $GAME_CONF;

  //�ᥤ���򿦤����
  $main_role = GetMainRole($target_role);
  $camp = DistinguishCamp($main_role);
  $main_role_name = $GAME_CONF->GetRoleName($main_role, true);
  if($camp != 'human')
    $role_str = '<span class="' . $camp . '">' . $main_role_name . '</span>';
  else
    $role_str = $main_role_name;

  //�����򿦤��ɲ�
  foreach($GAME_CONF->sub_role_list as $this_role => $this_name){
    if(strpos($target_role, $this_role) !== false){
      $sub_role_name = $GAME_CONF->GetRoleName($this_role, true);
      if($sub_role_name == '��')
	$role_str .= '<span class="lovers">' . $sub_role_name . '</span>';
      else
	$role_str .= $sub_role_name;
    }
  }

  return $role_str;
}

//�����ƥ��å��������� (talk Table)
function InsertSystemTalk($sentence, $time, $location = '', $target_date = '', $target_uname = 'system'){
  global $room_no, $date, $day_night;

  if($location    == '') $location = "$day_night system";
  if($target_date == '') $target_date = $date;
  InsertTalk($room_no, $target_date, $location, $target_uname, $time, $sentence, NULL, 0);
}

//�����ƥ��å��������� (system_message Table)
function InsertSystemMessage($sentence, $type, $target_date = ''){
  global $room_no, $date;

  if($target_date == '') $target_date = $date;
  mysql_query("INSERT INTO system_message(room_no, message, type, date)
		VALUES($room_no, '$sentence', '$type', $target_date)");
}

//�ǽ��񤭹��߻���򹹿�
function UpdateTime(){
  global $system_time, $room_no;
  mysql_query("UPDATE room SET last_updated = '$system_time' WHERE room_no = $room_no");
}

//���ޤǤ���ɼ���������
function DeleteVote(){
  global $room_no;
  mysql_query("DELETE FROM vote WHERE room_no = $room_no");
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
  case 'GUARD_DO':
  case 'REPORTER_DO':
  case 'CUPID_DO':
  case 'MANIA_DO':
  case 'POISON_CAT_DO':
    sscanf($str, "{$type}\t%s", &$target);
    DecodeSpace(&$target);
    return $target;
    break;

  case 'MAGE_RESULT':
  case 'MANIA_RESULT':
  case 'REPORTER_SUCCESS':
  case 'POISON_CAT_RESULT':
    sscanf($str, "%s\t%s\t%s", &$first, &$second, &$third);
    DecodeSpace(&$first);
    DecodeSpace(&$second);
    DecodeSpace(&$third);

    return array($first, $second, $third);
    break;

  case 'VOTE':
    sscanf($str, "%s\t%s\t%d\t%d\t%d", &$self, &$target, &$voted, &$vote, &$times);
    DecodeSpace(&$self);
    DecodeSpace(&$target);

    //%d �Ǽ������Ƥ������� (int)�פ�ʤ��褦�ʵ������������ɡġĤ�����ʤ���Ĥ�����
    return array($self, $target, $voted, $vote, (int)$times);
    break;

  default:
    sscanf($str, "%s\t%s", &$header, &$footer);
    DecodeSpace(&$header);
    DecodeSpace(&$footer);

    return array($header, $footer);
    break;
  }
}
?>
