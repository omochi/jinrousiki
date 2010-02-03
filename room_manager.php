<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'TIME_CONF', 'ROOM_IMG', 'MESSAGE', 'GAME_OPT_CAPT');

if(! $DB_CONF->Connect(true, false)) return false; //DB ��³

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  //��ե�������å�
  $white_list = array('127.', '192.168.');
  foreach($white_list as $host){ //�ۥ磻�ȥꥹ�ȥ����å�
    $trusted |= (strpos($_SERVER['REMOTE_ADDR'], $host) === 0);
  }
  if(! $trusted &&
     strncmp(@$_SERVER['HTTP_REFERER'], $SERVER_CONF->site_root,
	     strlen($SERVER_CONF->site_root)) != 0){
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʥ��������Ǥ���');
  }

  // ���ꤵ�줿�Ϳ������򤬤��뤫�����å�
  if(in_array($_POST['max_user'], $ROOM_CONF->max_user_list)){
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
  }
  else{
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʺ���Ϳ��Ǥ���');
  }
}
else{
  OutputRoomList();
}

$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
//¼�Υ��ƥʥ󥹽���
function MaintenanceRoom(){
  global $ROOM_CONF;

  //������ֹ�����̵��¼����¼�ˤ���
  $list  = mysql_query("SELECT room_no, last_updated FROM room WHERE status <> 'finished'");
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' WHERE room_no = ";
  MaintenanceRoomAction($list, $query, false, $ROOM_CONF->die_room);

  //��λ���������Υ��å����ID�Υǡ����򥯥ꥢ����
  $list = mysql_query("SELECT room.room_no, finish_time FROM room, user_entry
			WHERE room.room_no = user_entry.room_no
			AND !(user_entry.session_id is NULL) GROUP BY room_no");
  $query = "UPDATE user_entry SET session_id = NULL WHERE room_no = ";
  MaintenanceRoomAction($list, $query, true, $ROOM_CONF->clear_session_id);

  mysql_query('COMMIT'); //������ߥå�
}

//¼�Υ��ƥʥ󥹽��� (����)
function MaintenanceRoomAction($list, $query, $is_based_finish_time, $base_time){
  $time = TZTime();
  while(($array = mysql_fetch_assoc($list)) !== false){
    extract($array);
    $diff_time = $is_based_finish_time ?
                 $time - strtotime(finish_time) : $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//¼(room)�κ���
function CreateRoom($room_name, $room_comment, $max_user){
  global $DEBUG_MODE, $SERVER_CONF, $ROOM_CONF, $MESSAGE;

  $query = "FROM room WHERE status <> 'finished'"; //�����å��Ѥζ��̥�����
  $ip_address = $_SERVER['REMOTE_ADDR']; //¼Ω�Ƥ�Ԥä��桼���� IP �����

  //Ʊ���桼����Ω�Ƥ�¼����λ���Ƥ��ʤ���п�����¼����ʤ�
  if(! $DEBUG_MODE &&
     FetchResult("SELECT COUNT(room_no) $query AND establisher_ip = '$ip_address'") > 0){
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
  if(isset($time_stamp)){
    if(TZTime() - ConvertTimeStamp($time_stamp, false) <= $ROOM_CONF->establish_wait){
      OutputRoomAction('establish_wait');
      return false;
    }
  }

  //���ϥǡ����Υ��顼�����å�
  if($room_name == '' || $room_comment == '' || ! ctype_digit($max_user)){
    OutputRoomAction('empty');
    return false;
  }

  //�����४�ץ����򥻥å�
  $game_option = '';
  $option_role = '';
  $chaos        = ($ROOM_CONF->chaos        && $_POST['chaos'] == 'chaos');
  $chaosfull    = ($ROOM_CONF->chaosfull    && $_POST['chaos'] == 'chaosfull');
  $perverseness = ($ROOM_CONF->perverseness && $_POST['perverseness']  == 'on');
  $full_mania   = ($ROOM_CONF->full_mania   && $_POST['full_mania']  == 'on');
  $quiz         = ($ROOM_CONF->quiz         && $_POST['quiz']  == 'on');
  $duel         = ($ROOM_CONF->duel         && $_POST['duel']  == 'on');
  $game_option_list = array('wish_role', 'open_vote', 'not_open_cast');
  $option_role_list = array();
  if($quiz){
    $game_option .= 'quiz ';

    //GM ������ѥ���ɤ�����å�
    $quiz_password = $_POST['quiz_password'];
    EscapeStrings(&$quiz_password);
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    $game_option .= 'dummy_boy ';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
  }
  else{
    if($ROOM_CONF->dummy_boy && $_POST['dummy_boy'] == 'on'){
      $game_option .= 'dummy_boy ';
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
      $game_option .= 'dummy_boy gm_login ';
      $dummy_boy_handle_name = 'GM';
      $dummy_boy_password    = $gm_password;
    }
    if($chaos || $chaosfull){
      if($chaos) $game_option .= 'chaos ';
      if($chaosfull) $game_option .= 'chaosfull ';
      array_push($game_option_list, 'secret_sub_role');
      array_push($option_role_list, 'chaos_open_cast', 'chaos_open_cast_camp', 'chaos_open_cast_role');
      if($perverseness){
	$option_role .= 'no_sub_role ';
	array_push($option_role_list, 'perverseness');
      }
      else{
	array_push($option_role_list, 'no_sub_role');
      }
    }
    else{
      if($duel){
	$option_role .= 'duel ';
      }
      else{
	if(! $perverseness) array_push($option_role_list, 'decide', 'authority');
	array_push($option_role_list, 'poison', 'cupid', 'boss_wolf', 'poison_wolf', 'medium');
	if(! $full_mania) array_push($option_role_list, 'mania');
      }
    }
    array_push($option_role_list, 'liar', 'gentleman');
    if($perverseness){
      array_push($option_role_list, 'perverseness');
    }
    else{
      array_push($option_role_list, 'sudden_death');
    }
    if(! $duel) array_push($option_role_list, 'full_mania');
  }

  foreach($game_option_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
      $game_option .= $this_option . ' ';
    }
  }
  foreach($option_role_list as $this_option){
    if(! $ROOM_CONF->$this_option) continue;
    if($this_option == 'chaos_open_cast'){
      switch($_POST[$this_option]){
      case 'full':
	$add_option = 'chaos_open_cast';
	break;

      case 'camp':
	$add_option = 'chaos_open_cast_camp';
	break;

      case 'role':
	$add_option = 'chaos_open_cast_role';
	break;

      default:
	continue;
      }
      $option_role .= $add_option . ' ';
    }
    elseif($_POST[$this_option] == 'on'){
      $option_role .= $this_option . ' ';
    }
  }

  if($ROOM_CONF->real_time && $_POST['real_time'] == 'on'){
    $day   = $_POST['real_time_day'];
    $night = $_POST['real_time_night'];

    //���»��֤�0����99����ο����������å�
    if($day   != '' && ! preg_match('/[^0-9]/', $day)   && $day   > 0 && $day   < 99 &&
       $night != '' && ! preg_match('/[^0-9]/', $night) && $night > 0 && $night < 99){
      $game_option .= 'real_time:' . $day . ':' . $night;
    }
    else{
      OutputRoomAction('time');
      return false;
    }
  }

  //�ơ��֥���å�
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, vote WRITE, talk WRITE')){
    OutputRoomAction('busy');
    return false;
  }

  //�߽�˥롼�� No ��������ƺǤ��礭�� No �����
  $room_no = FetchResult('SELECT room_no FROM room ORDER BY room_no DESC') + 1;

  //��Ͽ
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
  mysql_query('UNLOCK TABLES');
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
    echo '<li>¼����������������Ƥ��ʤ���</li>';
    echo '<li>����Ϳ��������ǤϤʤ����ޤ��ϰ۾��ʸ����</li></ul>';
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
    $option_img_str = MakeGameOptionImage($game_option, $option_role); //�����४�ץ����β���
    //$option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //����Ϳ�

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->GenerateTag($status)}<span>[{$room_no}����]</span>{$room_name}¼<br>
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
function OutputCreateRoom(){
  global $ROOM_CONF, $TIME_CONF, $CAST_CONF, $GAME_OPT_MESS, $GAME_OPT_CAPT;

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

  if($ROOM_CONF->wish_role){
    $checked = ($ROOM_CONF->default_wish_role ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="wish_role">{$GAME_OPT_MESS->wish_role}��</label></td>
<td class="explain">
<input id="wish_role" type="checkbox" name="wish_role" value="on"{$checked}>
({$GAME_OPT_CAPT->wish_role})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->real_time){
    $checked = ($ROOM_CONF->default_real_time ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="real_time">�ꥢ�륿��������</label></td>
<td class="explain">
<input id="real_time" type="checkbox" name="real_time" value="on"{$checked}>
({$GAME_OPT_CAPT->real_time}���롧
<input type="text" name="real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">ʬ �롧
<input type="text" name="real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">ʬ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->dummy_boy){
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
<tr><td colspan="2"><hr></td></tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">{$GAME_OPT_MESS->open_vote}��</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="open_vote" value="on"{$checked}>
({$GAME_OPT_CAPT->open_vote})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">{$GAME_OPT_MESS->not_open_cast}��</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="not_open_cast" value="on"{$checked}>
({$GAME_OPT_CAPT->not_open_cast})
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$CAST_CONF->decide}�Ͱʾ��{$GAME_OPT_MESS->decide}��</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="decide" value="on"{$checked}>
({$GAME_OPT_CAPT->decide})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$CAST_CONF->authority}�Ͱʾ��{$GAME_OPT_MESS->authority}��</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="authority" value="on"{$checked}>
({$GAME_OPT_CAPT->authority})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$CAST_CONF->poison}�Ͱʾ��{$GAME_OPT_MESS->poison}��</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="poison" value="on"{$checked}>
({$GAME_OPT_CAPT->poison})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14�ͤ⤷����{$CAST_CONF->cupid}�Ͱʾ��<br>��{$GAME_OPT_MESS->cupid}��</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="cupid" value="on"{$checked}>
({$GAME_OPT_CAPT->cupid})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$CAST_CONF->boss_wolf}�Ͱʾ��{$GAME_OPT_MESS->boss_wolf}��</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="boss_wolf" value="on"{$checked}>
({$GAME_OPT_CAPT->boss_wolf})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison_wolf){
    $checked = ($ROOM_CONF->default_poison_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison_wolf">{$CAST_CONF->poison_wolf}�Ͱʾ��{$GAME_OPT_MESS->poison_wolf}��</label></td>
<td class="explain">
<input id="role_poison_wolf" type="checkbox" name="poison_wolf" value="on"{$checked}>
({$GAME_OPT_CAPT->poison_wolf})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->mania){
    $checked = ($ROOM_CONF->default_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_mania">{$CAST_CONF->mania}�Ͱʾ��{$GAME_OPT_MESS->mania}��</label></td>
<td class="explain">
<input id="role_mania" type="checkbox" name="mania" value="on"{$checked}>
({$GAME_OPT_CAPT->mania})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->medium){
    $checked = ($ROOM_CONF->default_medium ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_medium">{$CAST_CONF->medium}�Ͱʾ��{$GAME_OPT_MESS->medium}��</label></td>
<td class="explain">
<input id="role_medium" type="checkbox" name="medium" value="on"{$checked}>
({$GAME_OPT_CAPT->medium})
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->liar){
    $checked = ($ROOM_CONF->default_liar ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_liar">{$GAME_OPT_MESS->liar}��</label></td>
<td class="explain">
<input id="role_liar" type="checkbox" name="liar" value="on"{$checked}>
({$GAME_OPT_CAPT->liar})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->gentleman){
    $checked = ($ROOM_CONF->default_gentleman ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_gentleman">{$GAME_OPT_MESS->gentleman}��</label></td>
<td class="explain">
<input id="role_gentleman" type="checkbox" name="gentleman" value="on"{$checked}>
({$GAME_OPT_CAPT->gentleman})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->sudden_death){
    $checked = ($ROOM_CONF->default_sudden_death ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_sudden_death">{$GAME_OPT_MESS->sudden_death}��</label></td>
<td class="explain">
<input id="role_sudden_death" type="checkbox" name="sudden_death" value="on"{$checked}>
({$GAME_OPT_CAPT->sudden_death})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->perverseness){
    $checked = ($ROOM_CONF->default_perverseness ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_perverseness">{$GAME_OPT_MESS->perverseness}��</label></td>
<td class="explain">
<input id="role_perverseness" type="checkbox" name="perverseness" value="on"{$checked}>
({$GAME_OPT_CAPT->perverseness})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->full_mania){
    $checked = ($ROOM_CONF->default_full_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_full_mania">{$GAME_OPT_MESS->full_mania}��</label></td>
<td class="explain">
<input id="role_full_mania" type="checkbox" name="full_mania" value="on"{$checked}>
({$GAME_OPT_CAPT->full_mania})
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->chaos){
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

    if($ROOM_CONF->secret_sub_role){
      $checked = ($ROOM_CONF->default_secret_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="secret_sub_role">{$GAME_OPT_MESS->secret_sub_role}��</label></td>
<td class="explain">
<input id="secret_sub_role" type="checkbox" name="secret_sub_role" value="on"{$checked}>
({$GAME_OPT_CAPT->secret_sub_role})
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->no_sub_role){
      $checked = ($ROOM_CONF->default_no_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="no_sub_role">{$GAME_OPT_MESS->no_sub_role}��</label></td>
<td class="explain">
<input id="no_sub_role" type="checkbox" name="no_sub_role" value="on"{$checked}>
({$GAME_OPT_CAPT->no_sub_role})
</td>
</tr>

EOF;
    }
  }

  if($ROOM_CONF->quiz){
    $checked = ($ROOM_CONF->default_quiz ? ' checked' : '');
    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label for="quiz">{$GAME_OPT_MESS->quiz}��</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="quiz" value="on"{$checked}>
({$GAME_OPT_CAPT->quiz})<br>
<label for="quiz_password">GM ������ѥ���ɡ�</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
����{$GAME_OPT_CAPT->gm_login_footer}
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->duel){
    $checked = ($ROOM_CONF->default_duel ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="duel">{$GAME_OPT_MESS->duel}��</label></td>
<td class="explain">
<input id="duel" type="checkbox" name="duel" value="on"{$checked}>
({$GAME_OPT_CAPT->duel})
</td>
</tr>

EOF;
  }

  echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr><td class="make" colspan="2"><input type="submit" value=" ���� "></td></tr>
</table>
</form>

EOF;
}
?>
