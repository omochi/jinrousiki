<?php
require_once(dirname(__FILE__) . '/include/functions.php');

if(! $dbHandle = ConnectDatabase(true, false)) return false; //DB ��³

MaintenanceRoom();
EncodePostData();

if($_POST['command'] == 'CREATE_ROOM'){
  if(in_array($_POST['max_user'], $ROOM_CONF->max_user_list))
    CreateRoom($_POST['room_name'], $_POST['room_comment'], $_POST['max_user']);
  else
    OutputActionResult('¼���� [���ϥ��顼]', '̵���ʺ���Ϳ��Ǥ���');
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
}

//¼�Υ��ƥʥ󥹽��� (����)
function MaintenanceRoomAction($list, $query, $base_time){
  $count = mysql_num_rows($list);
  $time  = TZTime();

  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($list);
    $room_no      = $array['room_no'];
    $last_updated = $array['last_updated'];
    $diff_time    = $time - $last_updated;
    if($diff_time > $base_time) mysql_query($query . $room_no);
  }
}

//¼(room)�κ���
function CreateRoom($room_name, $room_comment, $max_user){
  global $ROOM_CONF, $MESSAGE, $system_password;

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
  $quiz = false;
  if($ROOM_CONF->quiz && $_POST['game_option_quiz'] == 'quiz'){
    $game_option .= 'quiz ';
    $quiz = true;

    //GM ������ѥ���ɤ�����å�
    $quiz_password = $_POST['quiz_password'];
    if($quiz_password == ''){
      OutputRoomAction('empty');
      return false;
    }
    EscapeStrings(&$quiz_password);
    $dummy_boy_handle_name = 'GM';
    $dummy_boy_password    = $quiz_password;
  }
  else{
    $dummy_boy_handle_name = '�����귯';
    $dummy_boy_password    = $system_password;
  }

  if($ROOM_CONF->wish_role && $_POST['game_option_wish_role'] == 'wish_role')
    $game_option .= 'wish_role ';
  if(($ROOM_CONF->dummy_boy && $_POST['game_option_dummy_boy'] == 'dummy_boy') || $quiz)
    $game_option .= 'dummy_boy ';
  if($ROOM_CONF->open_vote && $_POST['game_option_open_vote'] == 'open_vote')
    $game_option .= 'open_vote ';
  if($ROOM_CONF->not_open_cast && $_POST['game_option_not_open_cast'] == 'not_open_cast')
    $game_option .= 'not_open_cast ';
  if($ROOM_CONF->chaos && $_POST['game_option_chaos'] == 'chaos')
    $game_option .= 'chaos ';
  if($ROOM_CONF->chaosfull && $_POST['game_option_chaos'] == 'chaosfull')
    $game_option .= 'chaosfull ';
  if($ROOM_CONF->real_time && $_POST['game_option_real_time'] == 'real_time'){
    $day   = $_POST['game_option_real_time_day'];
    $night = $_POST['game_option_real_time_night'];

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

  $option_role = '';
  if(! $quiz){
    if($ROOM_CONF->decide && $_POST['option_role_decide'] == 'decide')
      $option_role .= 'decide ';
    if($ROOM_CONF->authority && $_POST['option_role_authority'] == 'authority')
      $option_role .= 'authority ';
    if($ROOM_CONF->poison && $_POST['option_role_poison'] == 'poison')
      $option_role .= 'poison ';
    if($ROOM_CONF->cupid && $_POST['option_role_cupid'] == 'cupid')
      $option_role .= 'cupid ';
    if($ROOM_CONF->boss_wolf && $_POST['option_role_boss_wolf'] == 'boss_wolf')
      $option_role .= 'boss_wolf ';
  }

  //�ơ��֥���å�
  if(! mysql_query('LOCK TABLES room WRITE, user_entry WRITE, vote WRITE, talk WRITE')){
    OutputRoomAction('busy');
    return false;
  }

  $result = mysql_query('SELECT room_no FROM room ORDER BY room_no DESC'); //�߽�˥롼��No�����
  $room_no_array = mysql_fetch_assoc($result); //�����(�Ǥ��礭��No)�����
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
			'live', '{$MESSAGE->dummy_boy_last_words}', '')");
  }

  if($entry && mysql_query('COMMIT')){ //������ߥå�
    OutputRoomAction('success', $room_name);
  }
  else{
    OutputRoomAction('busy');
  }
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
  global $DEBUG_MODE, $ROOM_IMG;

  //�롼��No���롼��̾�������ȡ�����Ϳ������֤����
  $sql = mysql_query("SELECT room_no, room_name, room_comment, game_option, option_role, max_user,
			status FROM room WHERE status <> 'finished' ORDER BY room_no DESC ");
  if($sql == NULL) return false;

  while($array = mysql_fetch_assoc($sql)){
    $room_no      = $array['room_no'];
    $room_name    = $array['room_name'];
    $room_comment = $array['room_comment'];
    $game_option  = $array['game_option'];
    $option_role  = $array['option_role'];
    $max_user     = $array['max_user'];
    $status       = $array['status'];

    switch($status){
      case 'waiting':
	$status_img = $ROOM_IMG->waiting;
	break;

      case 'playing':
	$status_img = $ROOM_IMG->playing;
	break;
    }

    $option_img_str = ''; //�����४�ץ����β���
    if(strpos($game_option, 'wish_role') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->wish_role, '����˾��');
    if(strpos($game_option, 'real_time') !== false){
      //�»��֤����»��֤����
      $real_time_str = strstr($game_option, 'real_time');
      sscanf($real_time_str, "real_time:%d:%d", &$day, &$night);
      AddImgTag(&$option_img_str, $ROOM_IMG->real_time,
		"�ꥢ�륿���������롧 $day ʬ���롧 $night ʬ");
    }
    if(strpos($game_option, 'dummy_boy') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->dummy_boy, '��������Ͽ����귯');
    if(strpos($game_option, 'open_vote') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->open_vote, '��ɼ����ɼ�����ɽ����');
    if(strpos($game_option, 'not_open_cast') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->not_open_cast, '��������������ʤ�');
    if(strpos($option_role, 'decide') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->decide, '16�Ͱʾ�Ƿ�����о�');
    if(strpos($option_role, 'authority') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->authority, '16�Ͱʾ�Ǹ��ϼ��о�');
    if(strpos($option_role, 'poison') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->poison, '20�Ͱʾ�����Ǽ��о�');
    if(strpos($option_role, 'cupid') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->cupid, '���塼�ԥå��о�');
    if(strpos($game_option, 'quiz') !== false)
      $option_img_str .= 'Qz';
    if(strpos($game_option, 'chaos ') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->chaos, '����');
    if(strpos($game_option, 'chaosfull') !== false)
      AddImgTag(&$option_img_str, $ROOM_IMG->chaosfull, '��������');

    $max_user_img = $ROOM_IMG -> max_user_list[$max_user]; //����Ϳ�

    echo <<<EOF
<a href="login.php?room_no=$room_no">
<img src="$status_img"><span>[{$room_no}����]</span>{$room_name}¼<br>
<div>��{$room_comment}�� {$option_img_str}<img src="$max_user_img"></div>
</a><br>

EOF;

    if($DEBUG_MODE){
      echo '<a href="admin/room_delete.php?room_no=' . $room_no . '">' . $room_no .
	' ���Ϥ��� (�۵���)</a><br>'."\n";
    }
  }
}

//���ץ������������ɲ� (OutputRoomList() ��)
function AddImgTag(&$tag, $src, $title){
  $tag .= "<img class=\"option\" src=\"$src\" title=\"$title\" alt=\"$title\">";
}

//�����������̤����
function OutputCreateRoom(){
  global $GAME_CONF, $ROOM_CONF, $TIME_CONF;

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
<input id="wish_role" type="checkbox" name="game_option_wish_role" value="wish_role"{$checked}>
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
<input id="real_time" type="checkbox" name="game_option_real_time" value="real_time"{$checked}>
(���»��֤��»��֤Ǿ��񤵤�ޤ����롧
<input type="text" name="game_option_real_time_day" value="{$TIME_CONF->default_day}" size="2" maxlength="2">ʬ �롧
<input type="text" name="game_option_real_time_night" value="{$TIME_CONF->default_night}" size="2" maxlength="2">ʬ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->dummy_boy){
    $checked = ($ROOM_CONF->default_dummy_boy ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="dummy_boy">��������Ͽ����귯��</label></td>
<td class="explain">
<input id="dummy_boy" type="checkbox" name="game_option_dummy_boy" value="dummy_boy"{$checked}>
(�������롢�����귯��ϵ�˿��٤��ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->open_vote){
    $checked = ($ROOM_CONF->default_open_vote ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="open_vote">��ɼ����ɼ�����ɽ���롧</label></td>
<td class="explain">
<input id="open_vote" type="checkbox" name="game_option_open_vote" value="open_vote"{$checked}>
(���ϼԤ���ɼ�ǥХ�ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->not_open_cast){
    $checked = ($ROOM_CONF->default_not_open_cast ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="not_open_cast">��������������ʤ���</label></td>
<td class="explain">
<input id="not_open_cast" type="checkbox" name="game_option_not_open_cast" value="not_open_cast"{$checked}>
(��Ǥ�ï���ɤ��򿦤ʤΤ�����������ޤ���)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->decide){
    $checked = ($ROOM_CONF->default_decide ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_decide">{$GAME_CONF->decide}�Ͱʾ�Ƿ�����о졧</label></td>
<td class="explain">
<input id="role_decide" type="checkbox" name="option_role_decide" value="decide"{$checked}>
(��ɼ��Ʊ���λ�������Ԥ���ɼ�褬ͥ�褵��ޤ�����Ǥ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->authority){
    $checked = ($ROOM_CONF->default_authority ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_authority">{$GAME_CONF->authority}�Ͱʾ�Ǹ��ϼ��о졧</label></td>
<td class="explain">
<input id="role_authority" type="checkbox" name="option_role_authority" value="authority"{$checked}>
(��ɼ��ɼ������ɼ�ˤʤ�ޤ�����Ǥ)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->poison){
    $checked = ($ROOM_CONF->default_poison ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_poison">{$GAME_CONF->poison}�Ͱʾ�����Ǽ��о졧</label></td>
<td class="explain">
<input id="role_poison" type="checkbox" name="option_role_poison" value="poison"{$checked}>
(�跺���줿��ϵ�˿��٤�줿��硢ƻϢ��ˤ��ޤ���¼����͢�����1 ϵ1)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->cupid){
    $checked = ($ROOM_CONF->default_cupid ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_cupid">14�ͤ⤷����{$GAME_CONF->cupid}�Ͱʾ��<br>�����塼�ԥå��о졧</label></td>
<td class="explain">
<input id="role_cupid" type="checkbox" name="option_role_cupid" value="cupid"{$checked}>
(�������������������ͤˤ��ޤ������ͤȤʤä���ͤϾ�����郎�Ѳ����ޤ�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->boss_wolf){
    $checked = ($ROOM_CONF->default_boss_wolf ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="role_boss_wolf">{$GAME_CONF->boss_wolf}�Ͱʾ����ϵ�о졧</label></td>
<td class="explain">
<input id="role_boss_wolf" type="checkbox" name="option_role_boss_wolf" value="boss_wolf"{$checked}>
(�ꤤ��̤���¼�͡ס���ǽ��̤�����ϵ�פ�ɽ�������ϵ�Ǥ��� ��ϵ��ͤ������ؤ����о�)
</td>
</tr>

EOF;
  }

  if($ROOM_CONF->quiz){
    $checked = ($ROOM_CONF->default_quiz ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label for="quiz">������¼��</label></td>
<td class="explain">
<input id="quiz" type="checkbox" name="game_option_quiz" value="quiz"{$checked}>
(ï������ʸ�ͤ���)<br>
<label for="quiz_password">GM ������ѥ���ɡ�</label>
<input id="quiz_password" type="password" name="quiz_password" size="20">
</td>
</tr>

EOF;
  }


  if($ROOM_CONF->chaos){
    $checked = ($ROOM_CONF->default_chaos ? ' checked' : '');
    echo <<<EOF
<tr>
<td><label>����⡼�ɡ�</label></td>
<td class="explain">
<input type="radio" name="game_option_chaos" value="" checked>
�̾��ϵ<br>

<input type="radio" name="game_option_chaos" value="chaos">
ϵ���Ѱʳ����Ƥ��򿦤�������Ȥʤ����⡼�ɤǤ�<br>

<input type="radio" name="game_option_chaos" value="chaosfull">
���Ƥ��򿦤�������Ȥʤ뿿������⡼�ɤǤ�
</td>
</tr>
EOF;
  }

  echo <<<EOF
<tr><td class="make" colspan="2"><input type="submit" value=" ���� "></td></tr>
</table>
</form>

EOF;
}
?>
