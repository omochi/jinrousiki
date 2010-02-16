<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'TIME_CONF', 'ROOM_IMG', 'MESSAGE', 'GAME_OPT_CAPT');

if(! $DB_CONF->Connect(true, false)) return false; //DB ��³
MaintenanceRoom();
EncodePostData();
$_POST['command'] == 'CREATE_ROOM' ? CreateRoom() : OutputRoomList();
$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
//¼�Υ��ƥʥ󥹽���
function MaintenanceRoom(){
  global $ROOM_CONF;

  //������ֹ�����̵��¼����¼�ˤ���
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' " .
    "WHERE status <> 'finished' AND last_updated < UNIX_TIMESTAMP(NOW()) - {$ROOM_CONF->die_room}";
  mysql_query($query);

  //��λ���������Υ��å����ID�Υǡ����򥯥ꥢ����
  $query = <<<EOF
UPDATE room, user_entry SET user_entry.session_id = NULL
WHERE room.room_no = user_entry.room_no
AND room.status = 'finished' AND !(user_entry.session_id is NULL)
AND (room.finish_time is NULL OR
     room.finish_time < DATE_SUB(NOW(), INTERVAL {$ROOM_CONF->clear_session_id} SECOND))
EOF;
  mysql_query($query);
  mysql_query('COMMIT'); //������ߥå�
}

//¼(room)�κ���
function CreateRoom(){
  global $DEBUG_MODE, $SERVER_CONF, $ROOM_CONF, $MESSAGE;

  if(CheckReferer('', array('127.', '192.168.'))){ //��ե�������å�
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʥ��������Ǥ���');
  }

  //���ϥǡ����Υ��顼�����å�
  $room_name    = $_POST['room_name'];
  $room_comment = $_POST['room_comment'];
  if($room_name == '' || $room_comment == ''){
    OutputRoomAction('empty');
    return false;
  }

  //���ꤵ�줿�Ϳ������򤬤��뤫�����å�
  $max_user = (int)$_POST['max_user'];
  if(! in_array($max_user, $ROOM_CONF->max_user_list)){
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʺ���Ϳ��Ǥ���');
  }

  $query = "FROM room WHERE status <> 'finished'"; //�����å��Ѥζ��̥�����
  $ip_address = $_SERVER['REMOTE_ADDR']; //¼Ω�Ƥ�Ԥä��桼���� IP �����

  //�ǥХå��⡼�ɻ���¼Ω�����¤򤷤ʤ�
  if(! $DEBUG_MODE){
    //Ʊ���桼����Ω�Ƥ�¼����λ���Ƥ��ʤ���п�����¼����ʤ�
    if(FetchResult("SELECT COUNT(room_no) $query AND establisher_ip = '$ip_address'") > 0){
      OutputRoomAction('over_establish');
      return false;
    }

    //��������¼����Ķ���Ƥ���褦�Ǥ���п�����¼����ʤ�
    if(FetchResult("SELECT COUNT(room_no) $query") >= $ROOM_CONF->max_active_room){
      OutputRoomAction('full');
      return false;
    }

    //Ϣ³¼Ω�����¥����å�
    $time_stamp = FetchResult("SELECT establish_time $query ORDER BY room_no DESC");
    if(isset($time_stamp) &&
       TZTime() - ConvertTimeStamp($time_stamp, false) <= $ROOM_CONF->establish_wait){
      OutputRoomAction('establish_wait');
      return false;
    }
  }

  //�����४�ץ����򥻥å�
  $chaos        = ($ROOM_CONF->chaos        && $_POST['chaos'] == 'chaos');
  $chaosfull    = ($ROOM_CONF->chaosfull    && $_POST['chaos'] == 'chaosfull');
  $perverseness = ($ROOM_CONF->perverseness && $_POST['perverseness']  == 'on');
  $full_mania   = ($ROOM_CONF->full_mania   && $_POST['full_mania']  == 'on');
  $quiz         = ($ROOM_CONF->quiz         && $_POST['quiz']  == 'on');
  $duel         = ($ROOM_CONF->duel         && $_POST['duel']  == 'on');
  $game_option_list = array();
  $option_role_list = array();
  $check_game_option_list = array('wish_role', 'open_vote', 'not_open_cast');
  $check_option_role_list = array();
  if($quiz){
    $game_option_list[] = 'quiz';

    //GM ������ѥ���ɤ�����å�
    $quiz_password = $_POST['quiz_password'];
    EscapeStrings(&$quiz_password);
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    $game_option_list[]    = 'dummy_boy ';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
  }
  else{
    if($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'on'){
      $game_option_list[]    = 'dummy_boy ';
      $dummy_boy_handle_name = '�����귯';
      $dummy_boy_password    = $SERVER_CONF->system_password;
    }
    elseif($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'gm_login'){
      //GM ������ѥ���ɤ�����å�
      $gm_password = $_POST['gm_password'];
      if($gm_password == ''){
	OutputRoomAction('empty');
	return false;
      }
      EscapeStrings(&$gm_password);
      array_push($game_option_list, 'dummy_boy', 'gm_login');
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = $gm_password;
    }

    if($chaos || $chaosfull){
      $game_option_list[] = $chaos ? 'chaos' : 'chaosfull';
      $check_game_option_list[] = 'secret_sub_role';
      array_push($check_option_role_list, 'chaos_open_cast', 'chaos_open_cast_camp',
		 'chaos_open_cast_role');
      if($perverseness){
	$option_role_list[] = 'no_sub_role';
	$check_option_role_list[] = 'perverseness';
      }
      else{
	$check_option_role_list[] = 'no_sub_role';
      }
    }
    else{
      if($duel){
	$option_role_list[] = 'duel';
      }
      else{
	if(! $perverseness) array_push($check_option_role_list, 'decide', 'authority');
	array_push($check_option_role_list, 'poison', 'cupid', 'boss_wolf', 'poison_wolf', 'medium');
	if(! $full_mania) $check_option_role_list[] = 'mania';
      }
    }
    array_push($check_option_role_list, 'liar', 'gentleman');
    $check_option_role_list[] = $perverseness ? 'perverseness' : 'sudden_death';
    if(! $duel) $check_option_role_list[] = 'full_mania';
  }

  //PrintData($_POST); //�ƥ�����
  //PrintData($check_game_option_list, 'Check Game Option'); //�ƥ�����
  foreach($check_game_option_list as $option){
    if(! $ROOM_CONF->$option) continue;
    if($option == 'not_open_cast'){
      switch($_POST[$option]){
      case 'full':
	$option = 'not_open_cast';
	break;

      case 'auto':
	$option = 'auto_open_cast';
	break;

      default:
	continue 2;
      }
    }
    elseif($_POST[$option] != 'on') continue;
    $game_option_list[] = $option;
  }
  //PrintData($game_option_list); //�ƥ�����

  //PrintData($check_option_role_list, 'Check Option Role'); //�ƥ�����
  foreach($check_option_role_list as $option){
    if(! $ROOM_CONF->$option) continue;
    if($option == 'chaos_open_cast'){
      switch($_POST[$option]){
      case 'full':
	$option = 'chaos_open_cast';
	break;

      case 'camp':
	$option = 'chaos_open_cast_camp';
	break;

      case 'role':
	$option = 'chaos_open_cast_role';
	break;

      default:
	continue 2;
      }
    }
    elseif($_POST[$option] != 'on') continue;
    $option_role_list[] = $option;
  }

  if($ROOM_CONF->real_time && $_POST['real_time'] == 'on'){
    $day   = $_POST['real_time_day'];
    $night = $_POST['real_time_night'];

    //���»��֤�0����99����ο����������å�
    if($day   != '' && ! preg_match('/[^0-9]/', $day)   && $day   > 0 && $day   < 99 &&
       $night != '' && ! preg_match('/[^0-9]/', $night) && $night > 0 && $night < 99){
      $game_option_list[] = 'real_time:' . $day . ':' . $night;
    }
    else{
      OutputRoomAction('time');
      return false;
    }
  }

  //PrintData($game_option_list, 'Game Option'); //�ƥ�����
  //PrintData($option_role_list, 'Option Role'); //�ƥ�����
  //OutputHTMLFooter(true); //�ƥ�����

  //�ơ��֥���å�
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, vote WRITE, talk WRITE')){
    OutputRoomAction('busy');
    return false;
  }

  //�߽�˥롼�� No ��������ƺǤ��礭�� No �����
  $room_no = FetchResult('SELECT room_no FROM room ORDER BY room_no DESC') + 1;

  //��Ͽ
  $game_option = implode(' ', $game_option_list);
  $option_role = implode(' ', $option_role_list);
  $status = false;
  do{
    //¼����
    $time = TZTime();
    $items = 'room_no, room_name, room_comment, establisher_ip, establish_time, game_option, ' .
      'option_role, max_user, status, date, day_night, last_updated';
    $values = "$room_no, '$room_name', '$room_comment', '$ip_address', NOW(), '$game_option', " .
      "'$option_role', $max_user, 'waiting', 0, 'beforegame', '$time'";
    if(! InsertDatabase('room', $items, $values)) break;

    //�����귯����¼������
    if(strpos($game_option, 'dummy_boy') !== false &&
       FetchResult("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no") == 0){
      $crypt_dummy_boy_password = CryptPassword($dummy_boy_password);
      $items = 'room_no, user_no, uname, handle_name, icon_no, profile, sex, password, live, last_words';
      $values = "$room_no, 1, 'dummy_boy', '$dummy_boy_handle_name', 0, '{$MESSAGE->dummy_boy_comment}', " .
	"'male', '$crypt_dummy_boy_password', 'live', '{$MESSAGE->dummy_boy_last_words}'";
      if(! InsertDatabase('user_entry', $items, $values)) break;
    }

    if(! mysql_query('COMMIT')) break; //������ߥå�
    OutputRoomAction('success', $room_name);
    $status = true;
  }while(false);
  if(! $status) OutputRoomAction('busy');
  //mysql_query('UNLOCK TABLES'); ��å������ OutputRoomAction() ��ͳ�ǹԤ�
}

//��̽��� (CreateRoom() ��)
function OutputRoomAction($type, $room_name = ''){
  global $SERVER_CONF;

  switch($type){
  case 'empty':
    OutputActionResultHeader('¼���� [���ϥ��顼]');
    echo '���顼��ȯ�����ޤ�����<br>';
    echo '�ʲ��ι��ܤ���٤���ǧ����������<br>';
    echo '<ul><li>¼��̾������������Ƥ��ʤ���</li>';
    echo '<li>¼����������������Ƥ��ʤ���</li></ul>';
    break;

  case 'time':
    OutputActionResultHeader('¼���� [���ϥ��顼]');
    echo '���顼��ȯ�����ޤ�����<br>';
    echo '�ʲ��ι��ܤ���٤���ǧ����������<br>';
    echo '<ul><li>�ꥢ�륿���������롢��λ��֤������Ƥ��ʤ���</li>';
    echo '<li>�ꥢ�륿���������롢��λ��֤����Ѥ����Ϥ��Ƥ���</li>';
    echo '<li>�ꥢ�륿���������롢��λ��֤�0�ʲ����ޤ���99�ʾ�Ǥ���</li>';
    echo '<li>�ꥢ�륿���������롢��λ��֤������ǤϤʤ����ޤ��ϰ۾��ʸ����</li></ul>';
    break;

  case 'success':
    OutputActionResultHeader('¼����', $SERVER_CONF->site_root);
    echo $room_name . ' ¼��������ޤ������ȥåץڡ��������Ӥޤ���';
    echo '�ڤ��ؤ��ʤ��ʤ� <a href="' . $SERVER_CONF->site_root . '">����</a> ��';
    break;

  case 'busy':
    OutputActionResultHeader('¼���� [�ǡ����١������顼]');
    echo '�ǡ����١��������Ф��������Ƥ��ޤ���<br>'."\n";
    echo '���֤��֤��ƺ�����Ͽ���Ƥ���������';
    break;

  case 'full':
    OutputActionResultHeader('¼���� [�ǡ����١������顼]');
    echo '���ߥץ쥤���¼�ο������Υ����Ф����ꤵ��Ƥ�������ͤ�Ķ���Ƥ��ޤ���<br>'."\n";
    echo '�ɤ�����¼�Ƿ��夬�Ĥ��Τ��ԤäƤ��������Ͽ���Ƥ���������';
    break;

  case 'over_establish':
    OutputActionResultHeader('¼���� [�ǡ����١������顼]');
    echo '���ʤ���Ω�Ƥ�¼�����߲�Ư��Ǥ���<br>'."\n";
    echo 'Ω�Ƥ�¼�Ƿ��夬�Ĥ��Τ��ԤäƤ��������Ͽ���Ƥ���������';
    break;

  case 'establish_wait':
    OutputActionResultHeader('¼���� [�ǡ����١������顼]');
    echo '�����Ф����ꤵ��Ƥ���¼Ω�ƻ��ֳִ֤�вᤷ�Ƥ��ޤ���<br>'."\n";
    echo '���Ф餯���֤򳫤��Ƥ��������Ͽ���Ƥ���������';
    break;
  }
  OutputHTMLFooter(); //�եå�����
}

//¼(room)��waiting��playing�Υꥹ�Ȥ���Ϥ���
function OutputRoomList(){
  global $DEBUG_MODE, $ROOM_IMG;

  //�롼��No���롼��̾�������ȡ�����Ϳ������֤����
  $query = "SELECT room_no, room_name, room_comment, game_option, option_role, max_user, status " .
    "FROM room WHERE status <> 'finished' ORDER BY room_no DESC";
  $list = FetchAssoc($query);
  foreach($list as $array){
    extract($array);
    $option_img_str = GenerateGameOptionImage($game_option, $option_role); //�����४�ץ����β���
    //$option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //����Ϳ�

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->Generate($status)}<span>[{$room_no}����]</span>{$room_name}¼<br>
<div>��{$room_comment}�� {$option_img_str}(����{$max_user}��)</div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' .
	$room_no . ' ���Ϥ��� (�۵���)</a><br>'."\n";
    }
  }
}

//¾�Υ����Ф��������̤����
function OutputSharedServerRoom(){
  global $SERVER_CONF;

  $config =& new SharedServerConfig();
  if($config->disable) return false;

  foreach($config->server_list as $server => $array){
    extract($array, EXTR_PREFIX_ALL, 'this');
    //PrintData($this_url, 'URL'); //�ƥ�����
    if($this_disable || $this_url == $SERVER_CONF->site_root) continue;
    if(($this_data = file_get_contents($this_url.'room_manager.php')) == '') continue;
    //PrintData($this_data, 'Data'); //�ƥ�����
    if($this_encode != '' && $this_encode != $config->encode){
      $this_data = mb_convert_encoding($this_data, $config->encode, $this_encode);
    }
    if($this_separator != ''){
      $this_split_list = mb_split($this_separator, $this_data);
      //PrintData($this_split_list, 'Split'); //�ƥ�����
      $this_data = array_pop($this_split_list);
    }
    if($this_footer != ''){
      if(($this_position = mb_strrpos($this_data, $this_footer)) === false) continue;
      $this_data = mb_substr($this_data, 0, $this_position + mb_strlen($this_footer));
    }
    if($this_data == '') continue;

    $this_replace_list = array('href="' => 'href="' . $this_url, 'src="'  => 'src="' . $this_url);
    $this_data = strtr($this_data, $this_replace_list);
    echo <<<EOF
    <fieldset>
      <legend>��������� (<a href="$this_url">$this_name</a>)</legend>
      <div class="game-list">$this_data</div>
    </fieldset>

EOF;
  }
}

//�����������̤����
function OutputCreateRoomPage(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>
<tr>
<td><label>{$GAME_OPT_MESS->room_name}��</label></td>
<td><input type="text" name="room_name" size="{$ROOM_CONF->room_name}"> ¼</td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->room_comment}��</label></td>
<td><input type="text" name="room_comment" size="{$ROOM_CONF->room_comment}"></td>
</tr>
<tr>
<td><label>{$GAME_OPT_MESS->max_user}��</label></td>
<td>
<select name="max_user">
<optgroup label="����Ϳ�">

EOF;

  foreach($ROOM_CONF->max_user_list as $number){
    echo '<option' . ($number == $ROOM_CONF->default_max_user ? ' selected' : '') . '>' .
      $number . '</option>'."\n";
  }

  echo <<<EOF
</optgroup>
</select>
<span class="explain">({$GAME_OPT_CAPT->max_user})</span></td>
</tr>

EOF;

  OutputRoomOption(array('wish_role', 'real_time', 'open_vote'));
  OutputRoomOptionDummyBoy();
  OutputRoomOptionOpenCast();

  $option_list = array('decide', 'authority', 'poison', 'cupid', 'boss_wolf',
		       'poison_wolf', 'mania', 'medium');
  OutputRoomOption($option_list, 'role');

  $option_list = array('liar', 'gentleman', 'sudden_death', 'perverseness', 'full_mania');
  OutputRoomOption($option_list, 'role');

  OutputRoomOptionChaos();
  OutputRoomOption(array('quiz', 'duel'));

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr><td class="make" colspan="2"><input type="submit" value=" ���� "></td></tr>
</table>
</form>

EOF;
}

function GenerateRoomOption($option, $label = ''){
  global $ROOM_CONF, $TIME_CONF, $CAST_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->$option) return NULL;

  $default = 'default_' . $option;
  $checked = ($ROOM_CONF->$default ? ' checked' : '');
  if($label != '') $label .= '_';
  $label .= $option;

  $sentence = $CAST_CONF->$option;
  if(isset($sentence)) $sentence .= '�Ͱʾ��';
  if($option == 'cupid') $sentence = '14�ͤ⤷����' . $sentence . '<br>��';
  $sentence .= $GAME_OPT_MESS->$option;

  $caption = $GAME_OPT_CAPT->$option;
  switch($option){
  case 'real_time':
    $caption .= <<<EOF
���롧
<input type="text" name="real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">ʬ �롧
<input type="text" name="real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">ʬ
EOF;
    break;

  case 'quiz':
    $add_caption = <<<EOF
<br>
<label for="quiz_password">GM ������ѥ���ɡ�</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
����{$GAME_OPT_CAPT->gm_login_footer}
EOF;
    break;
  }

  return <<<EOF
<tr>
<td><label for="{$label}">{$sentence}��</label></td>
<td class="explain">
<input id="{$label}" type="checkbox" name="{$option}" value="on"{$checked}>
({$caption}){$add_caption}
</td>
</tr>

EOF;
}

function OutputRoomOption($option_list, $label = '', $border = true){
  $tag_list = array();
  foreach($option_list as $option) $tag_list[] = GenerateRoomOption($option, $label);
  if(count($tag_list) < 1) return NULL;
  if($border) array_unshift($tag_list, '<tr><td colspan="2"><hr></td></tr>');
  echo implode('', $tag_list);
}

function OutputRoomOptionDummyBoy(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->dummy_boy) return NULL;

  if($ROOM_CONF->default_dummy_boy)
    $checked_dummy_boy = ' checked';
  elseif($ROOM_CONF->default_gerd)
    $checked_gerd = ' checked';
  elseif($ROOM_CONF->default_gm_login)
    $checked_gm = ' checked';
  else
    $checked_nothing = ' checked';

    /*
<input type="radio" name="dummy_boy" value="gerd"{$checked_gerd}>
�����귯�ϥ���ȷ�(¼�ͳ���ο����귯�Ǥ�)<br>
*/
  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->dummy_boy}��</label></td>
<td class="explain">
<input type="radio" name="dummy_boy" value=""{$checked_nothing}>
{$GAME_OPT_CAPT->no_dummy_boy}<br>

<input type="radio" name="dummy_boy" value="on"{$checked_dummy_boy}>
{$GAME_OPT_CAPT->dummy_boy}<br>

<input type="radio" name="dummy_boy" value="gm_login"{$checked_gm_login}>
{$GAME_OPT_MESS->gm_login} ({$GAME_OPT_CAPT->gm_login_header})<br>
<label for="gm_password">GM ������ѥ���ɡ�</label>
<input id="gm_password" type="password" name="gm_password" size="20"><br>
����{$GAME_OPT_CAPT->gm_login_footer}
</td>
</tr>

EOF;
}

function OutputRoomOptionOpenCast(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->not_open_cast) return NULL;

  switch($ROOM_CONF->default_not_open_cast){
  case 'full':
    $checked_close = ' checked';
    break;

  case 'auto':
    if($ROOM_CONF->auto_open_cast){
      $checked_auto = ' checked';
      break;
    }

  default:
    $checked_open = ' checked';
    break;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->not_open_cast}��</label></td>
<td class="explain">
<input type="radio" name="not_open_cast" value=""{$checked_open}>
{$GAME_OPT_CAPT->no_close_cast}<br>

<input type="radio" name="not_open_cast" value="full"{$checked_full}>
{$GAME_OPT_CAPT->not_open_cast}<br>

EOF;

  if($ROOM_CONF->auto_open_cast){
    echo <<<EOF
<input type="radio" name="not_open_cast" value="auto"{$checked_auto}>
{$GAME_OPT_CAPT->auto_open_cast}
</td>

EOF;
  }
}

function OutputRoomOptionChaos(){
  global $ROOM_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

  if(! $ROOM_CONF->chaos) return NULL;

  switch($ROOM_CONF->default_chaos){
  case 'chaos':
    $checked_chaos = ' checked';
    break;

  case 'chaosfull':
    if($ROOM_CONF->chaosfull){
      $checked_chaosfull = ' checked';
      break;
    }

  default:
    $checked_normal = ' checked';
    break;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$GAME_OPT_MESS->chaos}��</label></td>
<td class="explain">
<input type="radio" name="chaos" value=""{$checked_normal}>
{$GAME_OPT_CAPT->no_chaos}<br>

<input type="radio" name="chaos" value="chaos"{$checked_chaos}>
{$GAME_OPT_CAPT->chaos}<br>

EOF;

  if($ROOM_CONF->chaosfull){
    echo <<<EOF
<input type="radio" name="chaos" value="chaosfull"{$checked_chaosfull}>
{$GAME_OPT_CAPT->chaosfull}
</td>

EOF;
  }
  echo '</tr>'."\n";

  if($ROOM_CONF->chaos_open_cast){
    switch($ROOM_CONF->default_chaos_open_cast){
    case 'full':
      $checked_chaos_open_cast_full = ' checked';
      break;

    case 'camp':
      $checked_chaos_open_cast_camp = ' checked';
      break;

    case 'role':
      $checked_chaos_open_cast_role = ' checked';
      break;

    default:
      $checked_chaos_open_cast_none = ' checked';
      break;
    }

    echo <<<EOF
<tr>
<td><label>{$GAME_OPT_MESS->chaos_open_cast}��</label></td>
<td class="explain">
<input type="radio" name="chaos_open_cast" value=""{$checked_chaos_open_cast_none}>
{$GAME_OPT_CAPT->chaos_not_open_cast}<br>

<input type="radio" name="chaos_open_cast" value="camp"{$checked_chaos_open_cast_camp}>
{$GAME_OPT_CAPT->chaos_open_cast_camp}<br>

<input type="radio" name="chaos_open_cast" value="role"{$checked_chaos_open_cast_role}>
{$GAME_OPT_CAPT->chaos_open_cast_role}<br>

<input type="radio" name="chaos_open_cast" value="full"{$checked_chaos_open_cast_full}>
{$GAME_OPT_CAPT->chaos_open_cast_full}
</td>
</tr>

EOF;
  }
  OutputRoomOption(array('secret_sub_role', 'no_sub_role'), '', false);
}
