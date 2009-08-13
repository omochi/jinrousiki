<?php
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/game_format.php');
require_once(dirname(__FILE__) . '/user_class.php');
require_once(dirname(__FILE__) . '/talk_class.php');
require_once(dirname(__FILE__) . '/role/role_manager_class.php');

//���å����ǧ�� �֤��� OK:�桼��̾ / NG: false
function CheckSession($session_id, $exit = true){
  global $room_no;
  // $ip_address = $_SERVER['REMOTE_ADDR']; //IP���ɥ쥹ǧ�ڤϸ��ߤϹԤäƤ��ʤ�

  //���å���� ID �ˤ��ǧ��
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND session_id ='$session_id' AND user_no > 0");
  if(mysql_num_rows($sql) == 1) return mysql_result($sql, 0, 0);

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
  global $GAME_CONF, $room_no, $ROOM, $view_mode, $log_mode, $dead_mode,
    $heaven_mode, $live, $auto_reload, $play_sound, $list_down;

  //����ɴֳ֤�Ĵ��
  if($auto_reload != 0 && $auto_reload < $GAME_CONF->auto_reload_list[0]){
    $auto_reload = $GAME_CONF->auto_reload_list[0];
  }

  //�������Ǽ
  $url_header = 'game_frame.php?room_no=' . $room_no . '&auto_reload=' . $auto_reload;
  if($play_sound != '') $url_header .= '&play_sound=' . $play_sound;
  if($list_down  != '') $url_header .= '&list_down='  . $list_down;

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
  if(! $ROOM->is_aftergame() && $live == 'dead' && $view_mode != 'on' &&
     $log_mode != 'on' && $dead_mode != 'on' && $heaven_mode != 'on'){
    $jump_url =  $url_header . '&dead_mode=on';
    $sentence .= 'ŷ��⡼�ɤ��ڤ��ؤ��ޤ���';
  }
  elseif($ROOM->is_aftergame() && $dead_mode == 'on'){ //�����ब��λ�������ä������Ȥ�
    $jump_url = $url_header;
    $sentence .= '�����ཪλ��Τ����������Ӥޤ���';
  }
  elseif($live == 'live' && ($dead_mode == 'on' || $heaven_mode == 'on')){
    $jump_url = $url_header;
    $sentence .= '��������̤����Ӥޤ���';
  }

  if($jump_url != ''){ //��ư�褬���ꤵ��Ƥ���������ڤ��ؤ�
    $sentence .= $anchor_header . $jump_url . $anchor_footer;
    OutputActionResult($title, $sentence, $jump_url);
  }

  OutputHTMLHeader($title, 'game');
  echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  if($log_mode != 'on'){ //����������������
    echo '<script type="text/javascript" src="javascript/change_css.js"></script>'."\n";
    $on_load = "change_css('{$ROOM->day_night}');";
  }

  if($auto_reload != 0 && ! $ROOM->is_aftergame()){ //��ư����ɤ򥻥å�
    echo '<meta http-equiv="Refresh" content="' . $auto_reload . '">'."\n";
  }

  //�������桢�ꥢ�륿�������ʤ�в���֤� Javascript �ǥꥢ�륿����ɽ��
  if($ROOM->is_playing() && $ROOM->is_real_time() && $heaven_mode != 'on' && $log_mode != 'on'){
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
  echo 'var realtime_message = "��' . ($ROOM->is_day() ? '����' : '������') . '�ޤ� ";'."\n";
  echo 'var start_time = "' . $start_time . '";'."\n";
  echo 'var end_time = "'   . $end_time   . '";'."\n";
  echo '// --></script>'."\n";
}

//��ư�����Υ�󥯤����
function OutputAutoReloadLink($url){
  global $GAME_CONF, $auto_reload;

  echo '[��ư����](' . $url . '0">' . ($auto_reload == 0 ? '�ڼ�ư��' : '��ư') . '</a>';
  foreach($GAME_CONF->auto_reload_list as $time){
    $name = $time . '��';
    echo ' ' . $url . $time . '">' . ($auto_reload == $time ? '��' . $name . '��' : $name ) . '</a>';
  }
  echo ')'."\n";
}

//�����४�ץ������������
function OutputGameOption(){
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM;

  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = $room_no");
  echo '<table class="time-table"><tr>'."\n";
  echo '<td>�����४�ץ����' . MakeGameOptionImage($ROOM->game_option, $option_role) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//���դ���¸�ԤοͿ������
function OutputTimeTable(){
  global $room_no, $ROOM;

  if($ROOM->is_beforegame()) return false; //�����ब�ϤޤäƤ��ʤ����ɽ�����ʤ�

  //��¸�Ԥο������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND live = 'live' AND user_no > 0");
  $count = mysql_result($sql, 0, 0);
  echo '<td>' . $ROOM->date . ' ����<span>(��¸��' . $count . '��)</span></td>'."\n";
}

//�ץ쥤�䡼��������
function OutputPlayerList(){
  global $DEBUG_MODE, $GAME_CONF, $ICON_CONF, $USERS, $room_no, $ROOM, $uname, $live;

  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  //�֥饦��������å� (MSIE @ Windows ���� ������ Alt, Title °���ǲ��ԤǤ���)
  //IE �ξ����Ԥ� \r\n �����졢����¾�Υ֥饦���ϥ��ڡ����ˤ���(������Alt°��)
  $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');

  echo '<div class="player"><table cellspacing="5"><tr>'."\n";
  $count = 0;
  foreach($USERS->rows as $this_user_no => $this_object){
    $this_uname   = $this_object->uname;
    $this_handle  = $this_object->handle_name;
    $this_profile = $this_object->profile;
    $this_live    = $this_object->live;
    $this_role    = $this_object->role;
    $this_file    = $this_object->icon_filename;
    $this_color   = $this_object->color;

    $profile_alt  = str_replace("\n", $replace, $this_profile);
    if($DEBUG_MODE) $this_handle .= ' (' . $this_user_no . ')';

    //��������
    $path = $ICON_CONF->path . '/' . $this_file;
    $img_tag = '<img title="' . $profile_alt . '" alt="' . $profile_alt .
      '" style="border-color: ' . $this_color . ';"';
    if($this_live == 'live'){ //�����Ƥ���Х桼����������
      $this_live_str = '(��¸��)';
    }
    else{ //���Ǥ�л�˴��������
      $this_live_path = $path; //��������Υѥ��������ؤ�
      $path           = $ICON_CONF->dead;
      $this_live_str  = '(��˴)';
      $img_tag .= " onMouseover=\"this.src='$this_live_path'\" onMouseout=\"this.src='$path'\"";
    }
    $img_tag .= ' width="' . $width . '" height="' . $height . '"';
    $img_tag .= ' src="' . $path . '">';

    //�����ཪλ�塦��˴�����򿦸����⡼�ɤʤ顢�򿦡��桼���͡����ɽ��
    if($ROOM->is_aftergame() || ($live == 'dead' && $ROOM->is_open_cast())){
      $role_str = '';
      if(strpos($this_role, 'human') !== false)
	$role_str = MakeRoleName('human');
      elseif(strpos($this_role, 'boss_wolf') !== false)
	$role_str = MakeRoleName('boss_wolf', 'wolf');
      elseif(strpos($this_role, 'cursed_wolf') !== false)
	$role_str = MakeRoleName('cursed_wolf', 'wolf');
      elseif(strpos($this_role, 'cute_wolf') !== false)
	$role_str = MakeRoleName('cute_wolf', 'wolf');
      elseif(strpos($this_role, 'poison_wolf') !== false)
	$role_str = MakeRoleName('poison_wolf', 'wolf');
      elseif(strpos($this_role, 'resist_wolf') !== false)
	$role_str = MakeRoleName('resist_wolf', 'wolf');
      elseif(strpos($this_role, 'tongue_wolf') !== false)
	$role_str = MakeRoleName('tongue_wolf', 'wolf');
      elseif(strpos($this_role, 'wolf') !== false)
	$role_str = MakeRoleName('wolf');
      elseif(strpos($this_role, 'soul_mage') !== false)
	$role_str = MakeRoleName('soul_mage', 'mage');
      elseif(strpos($this_role, 'psycho_mage') !== false)
	$role_str = MakeRoleName('psycho_mage', 'mage');
      elseif(strpos($this_role, 'dummy_mage') !== false)
	$role_str = MakeRoleName('dummy_mage', 'mage');
      elseif(strpos($this_role, 'mage') !== false)
	$role_str = MakeRoleName('mage');
      elseif(strpos($this_role, 'soul_necromancer') !== false)
	$role_str = MakeRoleName('soul_necromancer', 'necromancer');
      elseif(strpos($this_role, 'dummy_necromancer') !== false)
	$role_str = MakeRoleName('dummy_necromancer', 'necromancer');
      elseif(strpos($this_role, 'necromancer') !== false)
	$role_str = MakeRoleName('necromancer');
      elseif(strpos($this_role, 'medium') !== false)
	$role_str = MakeRoleName('medium', 'necromancer');
      elseif(strpos($this_role, 'fanatic_mad') !== false)
	$role_str = MakeRoleName('fanatic_mad', 'mad');
      elseif(strpos($this_role, 'trap_mad') !== false)
	$role_str = MakeRoleName('trap_mad', 'mad');
      elseif(strpos($this_role, 'whisper_mad') !== false)
	$role_str = MakeRoleName('whisper_mad', 'mad');
      elseif(strpos($this_role, 'mad') !== false)
	$role_str = MakeRoleName('mad');
      elseif(strpos($this_role, 'poison_guard') !== false)
	$role_str = MakeRoleName('poison_guard', 'guard');
      elseif(strpos($this_role, 'dummy_guard') !== false)
	$role_str = MakeRoleName('dummy_guard', 'guard');
      elseif(strpos($this_role, 'guard') !== false)
	$role_str = MakeRoleName('guard');
      elseif(strpos($this_role, 'reporter') !== false)
	$role_str = MakeRoleName('reporter', 'guard');
      elseif(strpos($this_role, 'dummy_common') !== false)
	$role_str = MakeRoleName('dummy_common', 'common');
      elseif(strpos($this_role, 'common') !== false)
	$role_str = MakeRoleName('common');
      elseif(strpos($this_role, 'child_fox') !== false)
	$role_str = MakeRoleName('child_fox', 'fox');
      elseif(strpos($this_role, 'cursed_fox') !== false)
	$role_str = MakeRoleName('cursed_fox', 'fox');
      elseif(strpos($this_role, 'poison_fox') !== false)
	$role_str = MakeRoleName('poison_fox', 'fox');
      elseif(strpos($this_role, 'white_fox') !== false)
	$role_str = MakeRoleName('white_fox', 'fox');
      elseif(strpos($this_role, 'fox') !== false)
	$role_str = MakeRoleName('fox');
      elseif(strpos($this_role, 'poison_cat') !== false)
	$role_str = MakeRoleName('poison_cat', 'poison');
      elseif(strpos($this_role, 'strong_poison') !== false)
	$role_str = MakeRoleName('strong_poison', 'poison');
      elseif(strpos($this_role, 'incubate_poison') !== false)
	$role_str = MakeRoleName('incubate_poison', 'poison');
      elseif(strpos($this_role, 'dummy_poison') !== false)
	$role_str = MakeRoleName('dummy_poison', 'poison');
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
      elseif(strpos($this_role, 'assassin') !== false)
	$role_str = MakeRoleName('assassin');
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
      elseif(strpos($this_role, 'panelist') !== false)
	$role_str .= MakeRoleName('panelist', 'sudden-death', true);

      echo "<td>${img_tag}</td>"."\n";
      echo "<td><font color=\"$this_color\">��</font>$this_handle<br>"."\n";
      echo "��($this_uname)<br> $role_str";
    }
    elseif($ROOM->is_beforegame()){ //��������
      //�����ॹ�����Ȥ���ɼ���Ƥ���п����Ѥ���
      $query_game_start = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
	"AND situation = 'GAMESTART' AND uname = '$this_uname'";
      if((! $ROOM->is_quiz() && $this_uname == 'dummy_boy') || FetchResult($query_game_start) > 0){
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

//��°�ر�Ƚ��
function DistinguishCamp($role){
  if(strpos($role, 'wolf')  !== false || strpos($role, 'mad') !== false) return 'wolf';
  if(strpos($role, 'fox')   !== false) return 'fox';
  if(strpos($role, 'cupid') !== false) return 'lovers';
  if(strpos($role, 'quiz')  !== false) return 'quiz';
  return 'human';
}

//�ꤤ�դ�Ƚ��
function DistinguishMage($role){
  if(strpos($role, 'boss_wolf') !== false) return 'human'; //��ϵ��¼��Ƚ��
  //ϵ���Կ��ԤϿ�ϵȽ��
  if(strpos($role, 'wolf') !== false || strpos($role, 'suspect') !== false) return 'wolf';
  return 'human';
}

//���Ԥν���
function OutputVictory(){
  global $MESSAGE, $room_no, $view_mode, $log_mode, $role;

  //�����رĤ����
  $victory = FetchResult("SELECT victory_role FROM room WHERE room_no = $room_no");
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
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $view_mode, $uname, $play_sound, $cookie_vote_times;

  if(! $ROOM->is_day()) return false; //��ʳ��Ͻ��Ϥ��ʤ�

  //����ɼ�β�������
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = {$ROOM->date} AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) == 0) return false;

  //�����ܤκ���ɼ�ʤΤ�����
  $last_vote_times = (int)mysql_result($sql, 0, 0);

  //�����Ĥ餹
  if($play_sound == 'on' && $view_mode != 'on' && $last_vote_times > $cookie_vote_times)
    OutputSound('revote');

  //��ɼ�Ѥߥ����å�
  $this_vote_times = $last_vote_times + 1;
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date}
			AND vote_times = $this_vote_times AND uname = '$uname'");
  if(mysql_result($sql, 0, 0) == 0){
    echo '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '��' .
      $MESSAGE->draw_announce . ')</div><br>';
  }

  OutputVoteListDay($ROOM->date); //��ɼ��̤����
}

//�����Ĥ餹
function OutputSound($type, $loop = false){
  global $SOUND;

  if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$SOUND->type}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$SOUND->type}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
}

//���å�����
function OutputTalkLog(){
  global $MESSAGE, $room_no, $ROOM, $uname, $role, $live;

  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $sql = mysql_query("SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = $room_no AND location LIKE '{$ROOM->day_night}%'
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
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $uname, $live, $role;

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

  if($RQ_ARGS->add_role == 'on'){ //��ɽ���⡼���б�
    $talk_handle_name .= '<span class="add-role"> [' .
      MakeShortRoleName($USERS->GetRole($talk_uname)) . '] (' . $talk_uname . ')</span>';
  }

  LineToBR($sentence); //���ԥ����ɤ� <br> ���Ѵ�
  $location_system     = (strpos($location, 'system') !== false);
  $flag_vote           = (strpos($sentence, 'VOTE_DO')           === 0);
  $flag_wolf           = (strpos($sentence, 'WOLF_EAT')          === 0);
  $flag_mage           = (strpos($sentence, 'MAGE_DO')           === 0);
  $flag_child_fox      = (strpos($sentence, 'CHILD_FOX_DO')      === 0);
  $flag_guard          = (strpos($sentence, 'GUARD_DO')          === 0);
  $flag_reporter       = (strpos($sentence, 'REPORTER_DO')       === 0);
  $flag_cupid          = (strpos($sentence, 'CUPID_DO')          === 0);
  $flag_mania          = (strpos($sentence, 'MANIA_DO')          === 0);
  $flag_poison_cat     = (strpos($sentence, 'POISON_CAT_DO')     === 0);
  $flag_not_poison_cat = (strpos($sentence, 'POISON_CAT_NOT_DO') === 0);
  $flag_assassin       = (strpos($sentence, 'ASSASSIN_DO')       === 0);
  $flag_not_assassin   = (strpos($sentence, 'ASSASSIN_NOT_DO')   === 0);
  $flag_trap_mad       = (strpos($sentence, 'TRAP_MAD_DO')       === 0);
  $flag_not_trap_mad   = (strpos($sentence, 'TRAP_MAD_NOT_DO')   === 0);
  $flag_system = ($location_system &&
		  ($flag_vote  || $flag_wolf || $flag_mage || $flag_child_fox || $flag_guard ||
		   $flag_reporter || $flag_cupid || $flag_mania || $flag_poison_cat ||
		   $flag_not_poison_cat || $flag_assassin || $flag_not_assassin ||
		   $flag_trap_mad || $flag_not_trap_mad));

  if($location_system && $sentence == 'OBJECTION'){ //�۵Ĥ���
    $sentence = $talk_handle_name . ' ' . $MESSAGE->objection;
    $builder->AddSystemMessage('objection-' . $talk_sex, $sentence);
  }
  elseif($location_system && $sentence == 'GAMESTART_DO'){ //�����೫����ɼ (���ߤϲ���ɽ�����ʤ�����)
  }
  elseif($location_system && strpos($sentence, 'KICK_DO') === 0){ //KICK ��ɼ
    $target_handle_name = ParseStrings($sentence, 'KICK_DO');
    $sentence = "{$talk_handle_name} �� {$target_handle_name} {$MESSAGE->kick_do}";
    $builder->AddSystemMessage('kick', $sentence);
  }
  elseif($live == 'live' && $flag_system){ //��¸�����ɼ�������ɽ��
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
  //�����೫������ȥ������桢�����Ƥ���ͤ���
  elseif($ROOM->is_beforegame() || $ROOM->is_aftergame() ||
	 ($live == 'live' && $ROOM_is_day() && $location == 'day')){
    $builder->AddTalk($said_user, $talk);
  }
  //�������桢�����Ƥ���ͤ����ϵ
  elseif($live == 'live' && $ROOM->is_night() && $location == 'night wolf'){
    if(strpos($role, 'wolf') !== false || strpos($role, 'whisper_mad') !== false){
      $builder->AddTalk($said_user, $talk);
    }
    else{
      $builder->AddWhisper('wolf', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����񤭶���
  elseif($live == 'live' && $ROOM->is_night() && $location == 'night mad'){
    if(strpos($role, 'wolf') !== false || strpos($role, 'whisper_mad') !== false){
      $builder->AddTalk($said_user, $talk);
    }
  }
  //�������桢�����Ƥ���ͤ���ζ�ͭ��
  elseif($live == 'live' && $ROOM->is_night() && $location == 'night common'){
    if(strpos($role, 'dummy_common') !== false); //̴��ͭ�Ԥˤϲ��⸫���ʤ�
    elseif(strpos($role, 'common') !== false){
      $builder->AddTalk($said_user, $talk);
    }
    else{
      $builder->AddWhisper('common', $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ÿ�
  elseif($live == 'live' && $ROOM->is_night() && $location == 'night fox'){
    if(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false){
      $builder->AddTalk($said_user, $talk);
    }
  }
  //�������桢�����Ƥ���ͤ�����Ȥ��
  elseif($live == 'live' && $ROOM->is_night() && $location == 'night self_talk'){
    if($uname == $talk_uname){
      $builder->AddTalk($said_user, $talk);
    }
  }
  //�����ཪλ / �����귯(����GM��) / �������桢��˴��(��������ץ��������Բ�)
  elseif($ROOM->is_finished() || $uname == 'dummy_boy' || ($live == 'dead' && $ROOM->is_open_cast())){
    if($location_system && $flag_vote){ //�跺��ɼ
      $target_handle_name = ParseStrings($sentence, 'VOTE_DO');
      $action = 'vote';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->vote_do;
    }
    elseif($location_system && $flag_wolf){ //ϵ����ɼ
      $target_handle_name = ParseStrings($sentence, 'WOLF_EAT');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' ������ϵ�� '.$target_handle_name.' '.$MESSAGE->wolf_eat;
    }
    elseif($location_system && $flag_mage){ //�ꤤ�դ���ɼ
      $target_handle_name = ParseStrings($sentence, 'MAGE_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_child_fox){ //�ҸѤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'CHILD_FOX_DO');
      $action = 'mage-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mage_do;
    }
    elseif($location_system && $flag_guard){ //��ͤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'GUARD_DO');
      $action = 'guard-do';
      $sentence =  $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->guard_do;
    }
    elseif($location_system && $flag_reporter){ //�֥󲰤���ɼ
      $target_handle_name = ParseStrings($sentence, 'REPORTER_DO');
      $action = 'guard-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->reporter_do;
    }
    elseif($location_system && $flag_cupid){ //���塼�ԥåɤ���ɼ
      $target_handle_name = ParseStrings($sentence, 'CUPID_DO');
      $action = 'cupid-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->cupid_do;
    }
    elseif($location_system && $flag_mania){ //���åޥ˥�����ɼ
      $target_handle_name = ParseStrings($sentence, 'MANIA_DO');
      $action = 'mania-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->mania_do;
    }
    elseif($location_system && $flag_poison_cat){ //ǭ������ɼ
      $target_handle_name = ParseStrings($sentence, 'POISON_CAT_DO');
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->poison_cat_do;
    }
    elseif($location_system && $flag_not_poison_cat){ //ǭ���Υ���󥻥���ɼ
      $action = 'poison-cat-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->poison_cat_not_do;
    }
    elseif($location_system && $flag_assassin){ //�Ż��Ԥ���ɼ
      $target_handle_name = ParseStrings($sentence, 'ASSASSIN_DO');
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->assassin_do;
    }
    elseif($location_system && $flag_not_assassin){ //�Ż��ԤΥ���󥻥���ɼ
      $action = 'assassin-do';
      $sentence = $talk_handle_name.' '.$MESSAGE->assassin_not_do;
    }
    elseif($location_system && $flag_trap_mad){ //�Ż��Ԥ���ɼ
      $target_handle_name = ParseStrings($sentence, 'TRAP_MAD_DO');
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' �� '.$target_handle_name.' '.$MESSAGE->trap_mad_do;
    }
    elseif($location_system && $flag_not_trap_mad){ //�Ż��ԤΥ���󥻥���ɼ
      $action = 'wolf-eat';
      $sentence = $talk_handle_name.' '.$MESSAGE->trap_mad_not_do;
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
  elseif($flag_system){ //��ɼ�������ɽ��
  }
  else{ //�����
    if($ROOM->is_night()){
      if($location == 'night wolf'){
	if(strpos($role, 'wolf') !== false || strpos($role, 'whisper_mad') !== false){
	  $builder->AddTalk($said_user, $talk);
	}
	else{
	  $builder->AddWhisper('wolf', $talk);
	}
      }
      elseif($location == 'night mad'){
	if(strpos($role, 'wolf') !== false || strpos($role, 'whisper_mad') !== false){
	  $builder->AddTalk($said_user, $talk);
	}
      }
      elseif($location == 'night common'){
	if(strpos($role, 'dummy_common') !== false); //̴��ͭ�Ԥˤϲ��⸫���ʤ�
	elseif(strpos($role, 'common') !== false){
	  $builder->AddTalk($said_user, $talk);
	}
	else{
	  $builder->AddWhisper('wolf', $talk);
	}
      }
      elseif($location == 'night fox'){
	if(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false){
	  $builder->AddTalk($said_user, $talk);
	}
      }
      // elseif($location == 'night self_talk'); //�Ȥ������ɽ��
    }
    else{
      $builder->AddTalk($said_user, $talk);
    }
  }
}

//��˴�Ԥΰ�������
function OutputLastWords(){
  global $MESSAGE, $room_no, $ROOM;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! $ROOM->is_playing()) return false;

  //�����λ�˴�԰�������
  $set_date = $ROOM->date - 1;
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
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
  global $room_no, $ROOM, $log_mode;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! $ROOM->is_playing()) return false;

  $yesterday = $ROOM->date - 1;

  //���̥�����
  $query_header = "SELECT message, type FROM system_message WHERE room_no = $room_no AND date =";

  //�跺��å��������ǻ��å�����(��)
  $type_day = "type = 'VOTE_KILLED' OR type = 'POISON_DEAD_day' OR type = 'LOVERS_FOLLOWED_day' " .
    "OR type LIKE 'SUDDEN_DEATH%'";

  //����������˵����ä���˴��å�����
  $type_night = "type = 'WOLF_KILLED' OR type = 'CURSED' OR type = 'FOX_DEAD' " .
    "OR type = 'HUNTED' OR type = 'REPORTER_DUTY' OR type = 'ASSASSIN_KILLED' " .
    "OR type = 'TRAPPED' OR type = 'POISON_DEAD_night' OR type = 'LOVERS_FOLLOWED_night' " .
    "OR type LIKE 'REVIVE%'";

  if($ROOM->is_day()){
    $set_date = $yesterday;
    $type = $type_night;
  }
  else{
    $set_date = $ROOM->date;
    $type = $type_day;
  }

  $sql = mysql_query("$query_header $set_date AND ( $type ) ORDER BY MD5(RAND()*NOW())");
  $count = mysql_num_rows($sql); //��˴�ԤοͿ�
  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);
    OutputDeadManType($array['message'], $array['type']); //��ԤΥϥ�ɥ�͡���ȥ�����
  }

  //�������⡼�ɰʳ��ʤ���������˴�ԥ�å�����ɽ��
  if($log_mode == 'on') return;
  $set_date = $yesterday;
  $type = ($ROOM->is_day() ? $type_day : $type_night);

  $sql = mysql_query("$query_header $set_date AND ( $type ) ORDER BY MD5(RAND()*NOW())");
  $count = mysql_num_rows($sql); //��˴�ԤοͿ�
  for($i=0 ; $i < $count ;$i++){
    $array = mysql_fetch_assoc($sql);
    OutputDeadManType($array['message'], $array['type']);
  }
}

//��ԤΥ������̤˻�˴��å����������
function OutputDeadManType($name, $type){
  global $MESSAGE, $ROOM, $live;

  $deadman_header = '<tr><td>'.$name.' '; //���ܥ�å������إå�
  $deadman        = $deadman_header.$MESSAGE->deadman.'</td>'; //���ܥ�å�����
  $sudden_death   = $deadman_header.$MESSAGE->vote_sudden_death.'</td>'; //��������
  $reason_header  = "</tr>\n<tr><td>(".$name.' '; //�ɲö��̥إå�
  $show_reason = ($ROOM->is_finished() || ($live == 'dead' && $ROOM->is_open_cast()));

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
    if($ROOM->is_finished() || $live == 'dead'){
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

  case 'SUDDEN_DEATH_PANELIST':
    echo $sudden_death;
    if($show_reason) echo $reason_header.$MESSAGE->panelist.')</td>';
    break;
  }
  echo "</tr>\n</table>\n";
}

//��ɼ�ν��׽���
function OutputVoteList(){
  global $ROOM, $log_mode;

  //��������ʳ��Ͻ��Ϥ��ʤ�
  if(! $ROOM->is_playing()) return false;

  if($ROOM->is_day() && $log_mode != 'on') //����ä����������ν��פ����
    OutputVoteListDay($ROOM->date - 1);
  else //����ä��麣���ν��פ����
    OutputVoteListDay($ROOM->date);
}

//���ꤷ�����դ���ɼ��̤���Ϥ���
function OutputVoteListDay($set_date){
  global $RQ_ARGS, $room_no, $ROOM, $live, $view_mode;

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

    if((strpos($ROOM->game_option, 'open_vote') !== false || $live == 'dead') && $view_mode != 'on')
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

  if($RQ_ARGS->reverse_log == 'on'){ //�ս�ɽ��
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
  global $room_no, $ROOM;

  //��֤��򿦸��������Ĥ���Ƥ���Ȥ��Τ�ɽ��
  if(! ($ROOM->is_day() && $ROOM->is_open_cast())) return false;

  $yesterday = $ROOM->date - 1;
  $sql = mysql_query("SELECT message,type FROM system_message WHERE room_no = $room_no
			AND date = $yesterday AND (type = 'MAGE_DO' OR type = 'WOLF_EAT'
			OR type = 'GUARD_DO' OR type = 'REPORTER_DO' OR type = 'CUPID_DO'
			OR type = 'CHILD_FOX_DO' OR type = 'MANIA_DO' OR type = 'ASSASSIN_DO'
			OR type = 'TRAP_MAD_DO')");
  $header = '<strong>�������롢';
  $footer = '�ޤ���</strong><br>'."\n";

  while(($array = mysql_fetch_assoc($sql)) !== false){
    $sentence = $array['message'];
    $type     = $array['type'];

    list($actor, $target) = ParseStrings($sentence);
    echo $header.$actor.' ';
    switch($type){
    case 'WOLF_EAT':
      echo '(��ϵ) ������ '.$target.' ������';
      break;

    case 'MAGE_DO':
      echo '(�ꤤ��) �� '.$target.' ���ꤤ';
      break;

    case 'CHILD_FOX_DO':
      echo '(�Ҹ�) �� '.$target.' ���ꤤ';
      break;

    case 'GUARD_DO':
      echo '(���) �� '.$target.' ���Ҥ�';
      break;

    case 'REPORTER_DO':
      echo '(�֥�) �� '.$target.' �����Ԥ�';
      break;

    case 'CUPID_DO':
      echo '(���塼�ԥå�) �� '.$target.' �˰����������';
      break;

    case 'MANIA_DO':
      echo '(���åޥ˥�) �� '.$target.' �򿿻�';
      break;

    case 'ASSASSIN_DO':
      echo '(�Ż���) �� '.$target.' ������';
      break;

    case 'TRAP_MAD_DO':
      echo '(櫻�) �� '.$target.' �μ��դ�櫤�ųݤ�';
      break;
    }
    echo $footer;
  }
}

//���Ԥ�����å�
function CheckVictory($check_draw = false){
  global $GAME_CONF, $room_no, $ROOM, $vote_times;

  $query_count = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND ";

  //ϵ�ο������
  $sql = mysql_query($query_count . "role LIKE '%wolf%'");
  $wolf = (int)mysql_result($sql, 0, 0);

  //ϵ���ѡ�����԰ʳ��ο������
  $sql = mysql_query($query_count .
		     "!(role LIKE '%wolf%') AND !(role LIKE '%fox%') AND !(role LIKE 'quiz%')");
  $human = (int)mysql_result($sql, 0, 0);

  //�Ѥο������
  $sql = mysql_query($query_count . "role LIKE '%fox%'");
  $fox = (int)mysql_result($sql, 0, 0);

  //����Ԥο������
  $sql = mysql_query($query_count . "role LIKE 'quiz%'");
  $quiz = (int)mysql_result($sql, 0, 0);

  //���ͤο������
  $sql = mysql_query($query_count . "role LIKE '%lovers%'");
  $lovers = (int)mysql_result($sql, 0, 0);

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
  elseif($wolf >= $human + $quiz){ //¼����
    if($lovers > 1)  $victory_role = 'lovers';
    elseif($fox > 0) $victory_role = 'fox2';
    else             $victory_role = 'wolf';
  }
  elseif($check_draw && $vote_times >= $GAME_CONF->draw) //����ʬ��
    $victory_role = 'draw';
  elseif($ROOM->is_quiz() && $quiz == 0) //������¼ GM ��˴
    $victory_role = 'quiz_dead';

  if($victory_role != ''){
    mysql_query("UPDATE room SET status = 'finished', day_night = 'aftergame',
			victory_role = '$victory_role' WHERE room_no = $room_no");
    mysql_query('COMMIT'); //������ߥå�
  }
}

//�����ѹ�����
function UpdateLive($uname, $revive = false){
  global $room_no;

  $target_live = ($revive ? 'live' : 'dead');
  mysql_query("UPDATE user_entry SET live = '$target_live' WHERE room_no = $room_no
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
function SuddenDeath($uname, $medium, $type = NULL){
  global $MESSAGE, $USERS, $system_time, $room_no;

  //������ǧ
  $sql = mysql_query("SELECT live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 'live') return false;

  $target_handle = $USERS->GetHandleName($uname);
  UpdateLive($uname); //������¹�

  if($type){ //����å�������Ѥν�����Ԥ�
    InsertSystemTalk($target_handle . $MESSAGE->vote_sudden_death, ++$system_time);
    InsertSystemMessage($target_handle, 'SUDDEN_DEATH_' . $type);
    SaveLastWords($target_handle);
  }
  else{
    InsertSystemTalk($target_handle . $MESSAGE->sudden_death, ++$system_time);
  }

  if($medium){ //�����Ƚ����(�����ƥ��å�����)
    $target_camp = DistinguishCamp($USERS->GetRole($uname));
    InsertSystemMessage($target_handle . "\t" . $target_camp, 'MEDIUM_RESULT');
  }
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
function LoversFollowed($role, $medium, $sudden_death = false){
  global $MESSAGE, $USERS, $system_time, $room_no, $ROOM;

  //���ɤ�������ɬ�פ��������ͤ����
  $query = "SELECT uname, last_words FROM user_entry WHERE room_no = $room_no
		AND live = 'live' AND user_no > 0 AND ";
  $query .= GetLoversConditionString($role);
  $sql = mysql_query($query);

  while(($array = mysql_fetch_assoc($sql)) !== false){
    $target_uname      = $array['uname'];
    $target_last_words = $array['last_words'];
    $target_handle     = $USERS->GetHandleName($target_uname);
    $target_role       = $USERS->GetRole($target_uname);

    UpdateLive($target_uname); //���ɤ���

    if($sudden_death) //������ν���
      InsertSystemTalk($target_handle . $MESSAGE->lovers_followed, ++$system_time);
    else //���ɤ���(�����ƥ��å�����)
      InsertSystemMessage($target_handle, 'LOVERS_FOLLOWED_' . $ROOM->day_night);

    //���ɤ������ͤΰ����Ĥ�
    if($target_last_words != ''){
      InsertSystemMessage($target_handle . "\t" . $target_last_words, 'LAST_WORDS');
    }

    if($medium){ //�����Ƚ����(�����ƥ��å�����)
      InsertSystemMessage($target_handle . "\t" . DistinguishCamp($target_role), 'MEDIUM_RESULT');
    }

    //���ɤ�Ϣ������
    LoversFollowed($target_role, $medium, $sudden_death);
  }
}

//�ꥢ�륿����ηв����
function GetRealPassTime(&$left_time, $flag = false){
  global $system_time, $room_no, $ROOM;

  $time_str = strstr($ROOM->game_option, 'real_time');
  //�»��֤����»��֤����
  sscanf($time_str, 'real_time:%d:%d', &$day_minutes, &$night_minutes);
  $day_time   = $day_minutes   * 60; //�äˤʤ���
  $night_time = $night_minutes * 60; //�äˤʤ���

  //�Ǥ⾮���ʻ���(���̤κǽ�λ���)�����
  $sql = mysql_query("SELECT MIN(time) FROM talk WHERE room_no = $room_no
			AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'");
  $start_time = (int)mysql_result($sql, 0, 0);

  if($start_time != NULL){
    $pass_time = $system_time - $start_time; //�вᤷ������
  }
  else{
    $pass_time = 0;
    $start_time = $system_time;
  }
  $base_time = ($ROOM->is_day() ? $day_time : $night_time);
  $left_time = $base_time - $pass_time;
  if($left_time < 0) $left_time = 0; //�ޥ��ʥ��ˤʤä��饼��ˤ���
  if(! $flag) return;

  $start_date_str = gmdate('Y, m, j, G, i, s', $start_time);
  $end_date_str   = gmdate('Y, m, j, G, i, s', $start_time + $base_time);
  return array($start_date_str, $end_date_str);
}

//���äǻ��ַв����ηв����
function GetTalkPassTime(&$left_time, $flag = false){
  global $TIME_CONF, $room_no, $ROOM;

  $sql = mysql_query("SELECT SUM(spend_time) FROM talk WHERE room_no = $room_no
			AND date = {$ROOM->date} AND location LIKE '{$ROOM->day_night}%'");
  $spend_time = (int)mysql_result($sql, 0, 0);

  if($ROOM->is_day()){ //���12����
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

  if(($position = strpos($target_role, ' ')) === false) return $target_role;
  return substr($target_role, 0, $position);
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

//����νи������å�
function CheckMedium(){
  global $USERS;

  foreach($USERS->rows as $object){
    $this_main_role = GetMainRole($object->role);
    if($this_main_role == 'medium') return true;
  }
  return false;
}

//�����Ƥ���ϵ�Υ桼��̾��������������
function GetLiveWolves(){
  global $room_no;

  $query = "SELECT uname FROM user_entry WHERE room_no = $room_no " .
    "AND role LIKE '%wolf%' AND live = 'live' AND user_no > 0";
  return FetchArray($query);
}

//�����ƥ��å��������� (talk Table)
function InsertSystemTalk($sentence, $time, $location = '', $target_date = '', $target_uname = 'system'){
  global $room_no, $ROOM;

  if($location    == '') $location = "{$ROOM->day_night} system";
  if($target_date == '') $target_date = $ROOM->date;
  InsertTalk($room_no, $target_date, $location, $target_uname, $time, $sentence, NULL, 0);
}

//�����ƥ��å��������� (system_message Table)
function InsertSystemMessage($sentence, $type, $target_date = ''){
  global $room_no, $ROOM;

  if($target_date == '') $target_date = $ROOM->date;
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

//�����ɼ������������
function GetVoteTimes(){
  global $room_no, $ROOM;

  $query = "SELECT message FROM system_message WHERE room_no = $room_no " .
    "AND date = {$ROOM->date} AND type = 'VOTE_TIMES'";
  return (int)FetchResult($query);
}

//��μ�ʬ����ɼ�Ѥߥ����å�
function CheckSelfVoteNight($situation, $not_situation = ''){
  global $room_no, $ROOM, $uname;

  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} AND ";
  if($not_situation != ''){
    $sql = mysql_query("$query uname = '$uname' AND (situation = '$situation'
			OR situation = '$not_situation')");
  }
  elseif($situation == 'WOLF_EAT'){
    $sql = mysql_query("$query situation = '$situation'");
  }
  else{
    $sql = mysql_query("$query uname = '$uname' AND situation = '$situation'");
  }
  return (mysql_result($sql, 0, 0) != 0);
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
  case 'CHILD_FOX_DO':
  case 'GUARD_DO':
  case 'REPORTER_DO':
  case 'CUPID_DO':
  case 'MANIA_DO':
  case 'POISON_CAT_DO':
  case 'ASSASSIN_DO':
  case 'TRAP_MAD_DO':
    sscanf($str, "{$type}\t%s", &$target);
    DecodeSpace(&$target);
    return $target;
    break;

  case 'TONGUE_WOLF_RESULT':
  case 'MAGE_RESULT':
  case 'CHILD_FOX_RESULT':
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
