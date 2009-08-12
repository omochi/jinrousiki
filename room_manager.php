<?php
require_once(dirname(__FILE__) . '/include/functions.php');

if(! $dbHandle = ConnectDatabase(true, false)) return false; //DB ��³

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  // ��ե�������å�
  if (strncmp(@$_SERVER['HTTP_REFERER'], $SERVER_CONF->site_root, strlen($SERVER_CONF->site_root)) != 0)
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʥ��������Ǥ���');
  // ���ꤵ�줿�Ϳ������򤬤��뤫�����å�
  elseif (!in_array($_POST['max_user'], $ROOM_CONF->max_user_list))
     OutputActionResult('¼���� [���ϥ��顼]', '̵���ʺ���Ϳ��Ǥ���');
  else
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
}
else{
  OutputRoomList();
}

DisconnectDatabase($dbHandle); //DB ��³���

//-- �ؿ� --//
//¼�Υ��ƥʥ󥹽���
function MaintenanceRoom(){
  global $ROOM_CONF;

  //������ֹ�����̵��¼����¼�ˤ���
  $list  = mysql_query("SELECT room_no, last_updated FROM room WHERE status <> 'finished'");
  $query = "UPDATE room SET status = 'finished', day_night = 'aftergame' WHERE room_no = ";
  MaintenanceRoomAction($list, $query, $ROOM_CONF->die_room);

  //��λ���������Υ��å����ID�Υǡ����򥯥ꥢ����
  $list = mysql_query("SELECT room.room_no, last_updated from room, user_entry
			WHERE room.room_no = user_entry.room_no
			AND !(user_entry.session_id is NULL) GROUP BY room_no");
  $query = "UPDATE user_entry SET session_id = NULL WHERE room_no = ";
  MaintenanceRoomAction($list, $query, $ROOM_CONF->clear_session_id);

  mysql_query('COMMIT'); //������ߥå�
}

//¼�Υ��ƥʥ󥹽��� (����)
function MaintenanceRoomAction($list, $query, $base_time){
  $time = TZTime();
  while(($array = mysql_fetch_assoc($list)) !== false){
    $room_no      = $array['room_no'];
    $last_updated = $array['last_updated'];
    $diff_time    = $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//¼(room)�κ���
function CreateRoom($room_name, $room_comment, $max_user){
  global $SERVER_CONF, $ROOM_CONF, $MESSAGE;

  //���ϥǡ����Υ��顼�����å�
  if($room_name == '' || $room_comment == '' || ! ctype_digit($max_user)){
    OutputRoomAction('empty');
    return false;
  }
  //���������׽���
  EscapeStrings(&$room_name);
  EscapeStrings(&$room_comment);

  //�����४�ץ����򥻥å�
  $game_option = '';
  $option_role = '';
  $chaos     = ($ROOM_CONF->chaos     && $_POST['chaos'] == 'chaos');
  $chaosfull = ($ROOM_CONF->chaosfull && $_POST['chaos'] == 'chaosfull');
  $quiz      = ($ROOM_CONF->quiz      && $_POST['quiz']  == 'on');
  $game_option_list = array('open_vote', 'not_open_cast');
  $option_role_list = array();
  if($quiz){
    $game_option .= 'quiz ';

    //GM ������ѥ���ɤ�����å�
    $quiz_password = $_POST['quiz_password'];
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    EscapeStrings(&$quiz_password);
    $game_option .= 'dummy_boy ';
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
    array_push($game_option_list, 'wish_role');
    $ip_address = $_SERVER['REMOTE_ADDR'];
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
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    if($chaos || $chaosfull){
      if($chaos) $game_option .= 'chaos ';
      if($chaosfull) $game_option .= 'chaosfull ';
      array_push($game_option_list, 'secret_sub_role');
      array_push($option_role_list, 'chaos_open_cast', 'no_sub_role');
    }
    else{
      array_push($game_option_list, 'wish_role');
      array_push($option_role_list, 'decide', 'authority', 'poison', 'cupid', 'boss_wolf',
		 'poison_wolf', 'mania', 'medium');
    }
    array_push($option_role_list, 'liar', 'gentleman', 'sudden_death', 'full_mania');
  }

  foreach($game_option_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
      $game_option .= $this_option . ' ';
    }
  }
  foreach($option_role_list as $this_option){
    if($ROOM_CONF->$this_option && $_POST[$this_option] == 'on'){
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

  $result = mysql_query('SELECT room_no FROM room ORDER BY room_no DESC'); //�߽�˥롼�� No �����
  $room_no_array = mysql_fetch_assoc($result); //�����(�Ǥ��礭�� No)�����
  $room_no = $room_no_array['room_no'] + 1;

  //��Ͽ
  $time = TZTime();
  $entry = mysql_query("INSERT INTO room(room_no, room_name, room_comment, game_option,
			option_role, max_user, status, date, day_night, last_updated)
			VALUES($room_no, '$room_name', '$room_comment', '$game_option',
			'$option_role', $max_user, 'waiting', 0, 'beforegame', '$time')");

  //�����귯����¼������
  if(strpos($game_option, 'dummy_boy') !== false){
    mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name, icon_no,
			profile, sex, password, live, last_words, ip_address)
			VALUES($room_no, 1, 'dummy_boy', '$dummy_boy_handle_name', 0,
			'{$MESSAGE->dummy_boy_comment}', 'male', '$dummy_boy_password',
			'live', '{$MESSAGE->dummy_boy_last_words}', '$ip_address')");
  }

  if($entry && mysql_query('COMMIT')) //������ߥå�
    OutputRoomAction('success', $room_name);
  else
    OutputRoomAction('busy');
  mysql_query('UNLOCK TABLES');
}

//��̽��� (CreateRoom() ��)
function OutputRoomAction($type, $room_name = ''){
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
    OutputActionResultHeader('¼����', 'index.php');
    echo "$room_name ¼��������ޤ������ȥåץڡ��������Ӥޤ���";
    echo '�ڤ��ؤ��ʤ��ʤ� <a href="index.php">����</a> ��';
    break;

  case 'busy':
    OutputActionResultHeader('¼���� [�ǡ����١������顼]');
    echo '�ǡ����١��������Ф��������Ƥ��ޤ���<br>'."\n";
    echo '���֤��֤��ƺ�����Ͽ���Ƥ���������';
    break;
  }
  OutputHTMLFooter(); //�եå�����
}

//¼(room)��waiting��playing�Υꥹ�Ȥ���Ϥ���
function OutputRoomList(){
  global $DEBUG_MODE, $MESSAGE, $ROOM_IMG;

  //�롼��No���롼��̾�������ȡ�����Ϳ������֤����
  $sql = mysql_query("SELECT room_no, room_name, room_comment, game_option, option_role, max_user,
			status FROM room WHERE status <> 'finished' ORDER BY room_no DESC ");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $room_no      = $array['room_no'];
    $room_name    = $array['room_name'];
    $room_comment = $array['room_comment'];
    $game_option  = $array['game_option'];
    $option_role  = $array['option_role'];
    $max_user     = $array['max_user'];
    $status       = $array['status'];

    $option_img_str = MakeGameOptionImage($game_option, $option_role); //�����४�ץ����β���
    // $option_img_str .= '<img src="' . $ROOM_IMG->max_user_list[$max_user] . '">'; //����Ϳ�

    echo <<<EOF
<a href="login.php?room_no=$room_no">
{$ROOM_IMG->GenerateTag($status)}<span>[{$room_no}����]</span>{$room_name}¼<br>
<div>��{$room_comment}�� {$option_img_str}(����{$max_user}��)</div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' . $room_no .
	' ���Ϥ��� (�۵���)</a><br>'."\n";
    }
  }
}

//¾�Υ����Ф��������̤����
function OutputSharedServerRoom(){
  global $SERVER_CONF, $ROOM_CONF, $ENCODE;

  if(! $SERVER_CONF->shared_server) return false;

  foreach($ROOM_CONF->shared_server_list as $server => $array){
    $this_name      = $array['name'];
    $this_url       = $array['url'];
    $this_encode    = $array['encode'];
    $this_separator = $array['separator'];
    $this_footer    = $array['footer'];

    if(($this_data = file_get_contents($this_url.'room_manager.php')) == '') continue;
    #echo $this_data; //�ǥХå���
    if($this_encode != '' && $this_encode != $ENCODE){
      $this_data = mb_convert_encoding($this_data, $ENCODE, $this_encode);
    }
    if($this_separator != ''){
      $this_split_list = mb_split($this_separator, $this_data);
      #print_r($this_split_list); //�ǥХå���
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
  global $GAME_CONF, $ROOM_CONF, $TIME_CONF, $MESSAGE;

  echo <<<EOF
<form method="POST" action="room_manager.php">
<input type="hidden" name="command" value="CREATE_ROOM">
<table>
<tr>
<td><label>¼��̾����</label></td>
<td><input type="text" name="room_name" size="{$ROOM_CONF->room_name}"> ¼</td>
</tr>
<tr>
<td><label>¼�ˤĤ��Ƥ�������</label></td>
<td><input type="text" name="room_comment" size="{$ROOM_CONF->room_comment}"></td>
</tr>
<tr>
<td><label>����Ϳ���</label></td>
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
<span class="explain">(����� <a href="rule.php">�롼��</a> ���ǧ���Ʋ�����)</span></td>
</tr>

EOF;

  if($ROOM_CONF->wish_role){
    $checked = ($ROOM_CONF->default_wish_role ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="wish_role">����˾����</label></td>
<td class="explain">
<input id="wish_role" type="checkbox" name="wish_role" value="on"{$checked}>
(��˾���������Ǥ��ޤ������ʤ�뤫�ϱ��Ǥ�)
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
(���»��֤��»��֤Ǿ��񤵤�ޤ����롧
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
<td><label>{$MESSAGE->game_option_dummy_boy}��</label></td>
<td class="explain">
<input type="radio" name="dummy_boy" value=""{$checked_nothing}>
�����귯�ʤ�<br>

<input type="radio" name="dummy_boy" value="on"{$checked_dummy_boy}>
�����귯����(�������롢�����귯��ϵ�˿��٤��ޤ�)<br>

<input type="radio" name="dummy_boy" value="gm_login"{$checked_gm_login}>
�����귯�� GM (���� GM �������귯�Ȥ��ƥ����󤷤ޤ�)<br>
<label for="gm_password">GM ������ѥ���ɡ�</label>
<input id="gm_password" type="password" name="gm_password" size="20"><br>
����������桼��̾�ϡ�dummy_boy�פǤ���GM ����¼ľ���ɬ��̾��äƤ���������
</td>
</tr>
<tr><td colspan="2"><hr></td></tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">{$MESSAGE->game_option_open_vote}��</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="open_vote" value="on"{$checked}>
(���ϼԤ���ɼ�ǥХ�ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">{$MESSAGE->game_option_not_open_cast}��</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="not_open_cast" value="on"{$checked}>
(��Ǥ�ï���ɤ��򿦤ʤΤ�����������ޤ���)
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$GAME_CONF->decide}�Ͱʾ��{$MESSAGE->game_option_decide}��</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="decide" value="on"{$checked}>
(��ɼ��Ʊ���λ�������Ԥ���ɼ�褬ͥ�褵��ޤ�����Ǥ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$GAME_CONF->authority}�Ͱʾ��{$MESSAGE->game_option_authority}��</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="authority" value="on"{$checked}>
(��ɼ��ɼ������ɼ�ˤʤ�ޤ�����Ǥ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$GAME_CONF->poison}�Ͱʾ��{$MESSAGE->game_option_poison}��</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="poison" value="on"{$checked}>
(�跺���줿��ϵ�˿��٤�줿��硢ƻϢ��ˤ��ޤ���¼��2������1����ϵ1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14�ͤ⤷����{$GAME_CONF->cupid}�Ͱʾ��<br>��{$MESSAGE->game_option_cupid}��</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="cupid" value="on"{$checked}>
(�������������������ͤˤ��ޤ������ͤȤʤä���ͤϾ�����郎�Ѳ����ޤ�)<br>
������(¼��1�����塼�ԥå�1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$GAME_CONF->boss_wolf}�Ͱʾ��{$MESSAGE->game_option_boss_wolf}��</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="boss_wolf" value="on"{$checked}>
(�ꤤ��̤���¼�͡ס���ǽ��̤�����ϵ�פ�ɽ�������ϵ�Ǥ��� ��ϵ1����ϵ1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison_wolf){
    $checked = ($ROOM_CONF->default_poison_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison_wolf">{$GAME_CONF->poison_wolf}�Ͱʾ��{$MESSAGE->game_option_poison_wolf}��</label></td>
<td class="explain">
<input id="role_poison_wolf" type="checkbox" name="poison_wolf" value="on"{$checked}>
(�ߤ�줿���˥������¼�Ͱ�ͤ򴬤�ź���ˤ���ϵ�Ǥ��� ��ϵ1����ϵ1��¼��1������1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->mania){
    $checked = ($ROOM_CONF->default_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_mania">{$GAME_CONF->mania}�Ͱʾ��{$MESSAGE->game_option_mania}��</label></td>
<td class="explain">
<input id="role_mania" type="checkbox" name="mania" value="on"{$checked}>
(�������¾��¼�ͤ��򿦤򥳥ԡ������ü���򿦤Ǥ���¼��1�����åޥ˥�1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->medium){
    $checked = ($ROOM_CONF->default_medium ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_medium">{$GAME_CONF->medium}�Ͱʾ��{$MESSAGE->game_option_medium}��</label></td>
<td class="explain">
<input id="role_medium" type="checkbox" name="medium" value="on"{$checked}>
(�����ष���ͤν�°�رĤ�ʬ�����ü����ǽ�ԤǤ���¼��2�����1��������1)
</td>
</tr>

EOF;
  }

  echo '<tr><td colspan ="2"><hr></td></tr>';
  if($ROOM_CONF->liar){
    $checked = ($ROOM_CONF->default_liar ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_liar">{$MESSAGE->game_option_liar}��</label></td>
<td class="explain">
<input id="role_liar" type="checkbox" name="liar" value="on"{$checked}>
(������ǡ�ϵ��ǯ�פ��Ĥ��ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->gentleman){
    $checked = ($ROOM_CONF->default_gentleman ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_gentleman">{$MESSAGE->game_option_gentleman}��</label></td>
<td class="explain">
<input id="role_gentleman" type="checkbox" name="gentleman" value="on"{$checked}>
(���������̤˱������ֿ»Ρסֽʽ��פ��Ĥ��ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->sudden_death){
    $checked = ($ROOM_CONF->default_sudden_death ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_sudden_death">{$MESSAGE->game_option_sudden_death}��</label></td>
<td class="explain">
<input id="role_sudden_death" type="checkbox" name="sudden_death" value="on"{$checked}>
(��������ɼ�ǥ���å��ह�륵���򿦤Τɤ줫���Ĥ��ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->full_mania){
    $checked = ($ROOM_CONF->default_full_mania ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_full_mania">{$MESSAGE->game_option_full_mania}��</label></td>
<td class="explain">
<input id="role_full_mania" type="checkbox" name="full_mania" value="on"{$checked}>
(��¼�͡פ������ֿ��åޥ˥��פ������ؤ��ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->chaos){
    if($ROOM_CONF->default_chaos)
      $checked_chaos = ' checked';
    elseif($ROOM_CONF->default_chaosfull)
      $checked_chaosfull = ' checked';
    else
      $checked_normal = ' checked';

    echo <<<EOF
<tr><td colspan="2"><hr></td></tr>
<tr>
<td><label>{$MESSAGE->game_option_chaos}��</label></td>
<td class="explain">
<input type="radio" name="chaos" value=""{$checked_normal}>
�̾��ϵ<br>

<input type="radio" name="chaos" value="chaos"{$checked_chaos}>
�̾�¼�ܦ����٤����򤬤֤�����⡼�ɤǤ�<br>

<input type="radio" name="chaos" value="chaosfull"{$checked_chaosfull}>
��ϵ1���ꤤ��1�ʳ������Ƥ��򿦤�������Ȥʤ뿿������⡼�ɤǤ�
</td>
</tr>

EOF;
    if($ROOM_CONF->chaos_open_cast){
      $checked = ($ROOM_CONF->default_chaos_open_cast ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="chaos_open_cast">{$MESSAGE->game_option_chaos_open_cast}��</label></td>
<td class="explain">
<input id="chaos_open_cast" type="checkbox" name="chaos_open_cast" value="on"{$checked}>
(��������Τ��ޤ�������⡼�����ѥ��ץ����)
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->secret_sub_role){
      $checked = ($ROOM_CONF->default_secret_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="secret_sub_role">{$MESSAGE->game_option_secret_sub_role}��</label></td>
<td class="explain">
<input id="secret_sub_role" type="checkbox" name="secret_sub_role" value="on"{$checked}>
(�����򿦤�ʬ����ʤ��ʤ�ޤ�������⡼�����ѥ��ץ����)
</td>
</tr>

EOF;
    }

    if($ROOM_CONF->no_sub_role){
      $checked = ($ROOM_CONF->default_no_sub_role ? ' checked' : '');
      echo <<<EOF
<tr>
<td><label for="no_sub_role">{$MESSAGE->game_option_no_sub_role}��</label></td>
<td class="explain">
<input id="no_sub_role" type="checkbox" name="no_sub_role" value="on"{$checked}>
(�����򿦤�Ĥ��ޤ��󡧰���⡼�����ѥ��ץ����)
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
<td><label for="quiz">{$MESSAGE->game_option_quiz}��</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="quiz" value="on"{$checked}>
(�����귯���ֽ���ԡפˤʤäƥ�������Ф��ޤ�)<br>
<label for="quiz_password">GM ������ѥ���ɡ�</label>
<input id="quiz_password" type="password" name="quiz_password" size="20"><br>
����������桼��̾�ϡ�dummy_boy�פǤ���GM ����¼ľ���ɬ��̾��äƤ���������
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
