<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//���å���󳫻�
session_start();
$session_id = session_id();

$room_no     = $_GET['room_no'];
$auto_reload = (int)$_GET['auto_reload'];
$play_sound  = $_GET['play_sound'];
$list_down   = $_GET['list_down'];

//php�ΰ������Ǽ
$php_argv = 'room_no=' . $room_no;
if($auto_reload != '') $php_argv .= '&auto_reload=' . $auto_reload;
if($play_sound  != '') $php_argv .= '&play_sound='  . $play_sound;
if($list_down   != '') $php_argv .= '&list_down='   . $list_down;
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">����� &amp; reload</a>';

//����������
//$day_night = $_COOKIE['day_night'];

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

//���դ��뤫�뤫�������ཪλ�夫�ɤ��������
$sql = mysql_query("SELECT date, day_night, status FROM room WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$date      = $array['date'];
$day_night = $array['day_night'];
$status    = $array['status'];

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸���֤����
$sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];

$command = $_POST['command'];
$type    = $_POST['type']; //��ɼ��ʬ�� (Kick���跺���ꤤ��ϵ�ʤ�)

if($status == 'finished'){ //������Ͻ�λ���ޤ���
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>������Ͻ�λ���ޤ���<br>'."\n" .
		     $back_url . '</div>');
}

if($live == 'dead'){ //���Ǥޤ�
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>��Ԥ���ɼ�Ǥ��ޤ���<br>'."\n" .
		     $back_url . '</div>');
}

if($command == 'vote'){ //��ɼ����
  $target_no = $_POST['target_no'];
  $situation = $_POST['situation'];

  if($date == 0){ //�����೫�� or Kick ��ɼ����
    if($situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($situation == 'KICK_DO'){
      $target_handle_name = $_POST['target_handle_name'];
      VoteKick($_POST['target_handle_name']);
    }
    else{ //�������褿����å����顼
      OutputActionResult('��ɼ���顼',
			 '<div align="center">' .
			 '<a name="#game_top"></a>�ץ���२�顼�Ǥ���'.
			 '�����Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($target_no == 0){
    OutputActionResult('��ɼ���顼',
		       '<div align="center">' .
		       '<a name="#game_top"></a>��ɼ�����ꤷ�Ƥ�������<br>'."\n" .
		       $back_url . '</div>');
  }
  elseif($day_night == 'day'){ //��ν跺��ɼ����
    $vote_times = $_POST['vote_times']; //��ɼ��� (����ɼ�ξ��)
    VoteDay();
  }
  elseif($day_night == 'night'){ //�����ɼ����
    VoteNight();
  }
  else{ //�������褿����å����顼
    OutputActionResult('��ɼ���顼',
		       '<div align="center">' .
		       '<a name="#game_top"></a>�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($date == 0){ //�����೫�� or Kick ��ɼ�ڡ�������
  OutputVoteBeforeGame();
}
elseif($day_night == 'day'){ //��ν跺��ɼ�ڡ�������
  OutputVoteDay();
}
elseif($day_night == 'night'){ //�����ɼ�ڡ�������
  OutputVoteNight();
}
else{  //������ɼ����Ƥ���ޤ�
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>������ɼ����Ƥ���ޤ�<br>'."\n" .
		     $back_url . '</div>');
}

DisconnectDatabase($dbHandle); //MySQL�Ȥ���³���Ĥ���

// �ؿ� //
//��ɼ�ڡ��� HTML �إå�����
function OutputVotePageHeader(){
  global $day_night, $php_argv;

  OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[��ɼ]', 'game');
  if($day_night != '')  echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
  echo <<< EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="command" value="vote">

EOF;
}

//��ɼ��̽���
function OutputVoteResult($message, $unlock = false){
  global $back_url;
  OutputActionResult('��Ͽ�ϵ�ʤ�䡩[��ɼ���]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $message . '<br>'."\n" .
		     $back_url . '</div>', '', $unlock);
}

//�����೫����ɼ�ν���
function VoteGameStart(){
  global $room_no, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0 || $uname == 'dummy_boy')
    OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');

  LockTable(); //�ơ��֥����¾Ū��å�

  //��ɼ����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, situation)
			VALUES($room_no, 0, '$uname', 'GAMESTART')");
  if($sql && mysql_query('COMMIT')){//������ߥå�
    CheckVoteGameStart(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����೫����ɼ���׽���
function CheckVoteGameStart(){
  global $role_list, $room_no, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');

  //��ɼ����������४�ץ��������
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);
  $game_option = GetGameOption();

  //�����귯���Ѥʤ�����귯��ʬ��û�
  if(strstr($game_option, 'dummy_boy')) $vote_count++;

  //�桼����������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count < 4 || $vote_count != $user_count) return false;

  //�����೫��
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //���ޤǤ���ɼ���������

  //��俶��ʬ��
  $now_role_list = $role_list[$user_count]; //�Ϳ��ˤ�����ꥹ�Ȥ�����

  //����ԡ����ϼԡ����ǼԤΥ��ץ�������(¾�ȷ�Ǥ�Ǥ�����)�����
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);

  //20�Ͱʾ�����Ǽ�
  //���ץ��������������Ƭ����(¼��)�����񤭤���
  $option_role_count = 0;
  //���ǼԤ�20�Ͱʾ���о�(���κݡ�¼��2�͢��ǡ�ϵ�ˤ���)
  if(strstr($option_role, 'poison') && $user_count >= 20){
    $now_role_list[$option_role_count] = 'poison';
    $option_role_count++;

    $now_role_list[$option_role_count] = 'wolf';
    $option_role_count ++;
  }
  //���塼�ԥåɤ�14�� or 16�Ͱʾ���о��¼�͢����塼�ԥåɤ��ѹ���
  if(strstr($option_role, 'cupid') && ($user_count == 14 || $user_count >= 16)){
    $now_role_list[$option_role_count] = 'cupid';
    $option_role_count ++;
  }

  //�桼���ꥹ�Ȥ������˼���
  $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) as MyRand FROM user_entry
				WHERE room_no = $room_no AND user_no > 0 ORDER BY MyRand");

  $uname_array    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $role_array     = array(); //�桼��̾���б��������
  $re_uname_array = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ

  for($i=0; $i < $user_count; $i++){ //��˾����������
    $user_list_array = mysql_fetch_assoc($sql_user_list); //������ʥ桼����������
    $this_uname = $user_list_array['uname'];

    if(strstr($game_option, 'wish_role')) //����˾���ξ�硢��˾�����
      $this_role = $user_list_array['role'];
    else
      $this_role = 'none';

    if(($this_index = array_search($this_role, $now_role_list)) != false){ //��˾�ɤ���
      array_push($uname_array, $this_uname);
      array_push($role_array,  $this_role);

      array_splice($now_role_list, $this_index, 1); //��꿶�ä����Ϻ������
    }
    else{ //��˾����䤬�ʤ�
      array_push($re_uname_array, $this_uname);
    }
  }

  $re_count = count($re_uname_array); //��䤬��ޤ�ʤ��ä��ͤο�
  for($i=0; $i < $re_count; $i++){ //;�ä����������Ƥ�
    array_push($uname_array, $re_uname_array[$i]);
    array_push($role_array,  $now_role_list[$i]);
  }

  //��Ǥ�Ȥʤ���������
  $rand_keys = array_rand($role_array, $user_count); //�����७�������

  //��Ǥ�Ȥʤ륪�ץ�������(���ϼԡ������)
  $option_subrole_count = 0;
  if(strstr($option_role, 'authority') && $user_count >= 16){
    $role_array[$rand_keys[$option_subrole_count]] .= ' authority';
    $option_subrole_count++;
    $authority_count++;
  }
  if(strstr($option_role, 'decide') && $user_count >= 16){
    $role_array[$rand_keys[$option_subrole_count]] .= ' decide';
    $option_subrole_count++;
    $decide_count++;
  }

  $dummy_boy_index = array_search('dummy_boy', $uname_array); //�����귯�����󥤥�ǥå��������

  //�����귯���Ѥξ�硢�����귯��ϵ���ѡ����Ǽԡ����塼�ԥåɰʳ��ˤ���
  if(strstr($game_option, 'dummy_boy') &&
     (strstr($role_array[$dummy_boy_index], 'wolf')   ||
      strstr($role_array[$dummy_boy_index], 'fox')    ||
      strstr($role_array[$dummy_boy_index], 'poison') ||
      strstr($role_array[$dummy_boy_index], 'cupid'))){
    for($i=0; $i < $user_count; $i++){
      //ϵ���ѡ����Ǽԡ����塼�ԥåɰʳ������Ĥ��ä��������ؤ���
      if(! (strstr($role_array[$i], 'wolf')   || strstr($role_array[$i], 'fox') ||
	    strstr($role_array[$i], 'poison') || strstr($role_array[$i], 'cupid'))){
	$tmp_role = $role_array[$dummy_boy_index];
	$role_array[$dummy_boy_index] = $role_array[$i];
	$role_array[$i] = $tmp_role;
	break;
      }
    }
  }

  //����DB�˹���
  for($i=0; $i < $user_count; $i++){
    $entry_uname = $uname_array[$i];
    $entry_role  = $role_array[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    if(strstr($entry_role, 'human'))       $role_count_list['human']++;
    if(strstr($entry_role, 'wolf'))        $role_count_list['wolf']++;
    if(strstr($entry_role, 'mage'))        $role_count_list['mage']++;
    if(strstr($entry_role, 'necromancer')) $role_count_list['necromancer']++;
    if(strstr($entry_role, 'mad'))         $role_count_list['mad']++;
    if(strstr($entry_role, 'guard'))       $role_count_list['guard']++;
    if(strstr($entry_role, 'common'))      $role_count_list['common']++;
    if(strstr($entry_role, 'fox'))         $role_count_list['fox']++;
    if(strstr($entry_role, 'poison'))      $role_count_list['poison']++;
    if(strstr($entry_role, 'cupid'))       $role_count_list['cupid']++;
    if(strstr($entry_role, 'decide'))      $role_count_list['decide']++;
    if(strstr($entry_role, 'authority'))   $role_count_list['authority']++;
  }

  //���줾�����䤬���ͤ��ĤʤΤ������ƥ��å�����
  $sentence = '¼��' . (int)$role_count_list['human'] .
    '����ϵ'         . (int)$role_count_list['wolf'] .
    '���ꤤ��'       . (int)$role_count_list['mage'] .
    '����ǽ��'       . (int)$role_count_list['necromancer'] .
    '������'         . (int)$role_count_list['mad'] .
    '�����'         . (int)$role_count_list['guard'] .
    '����ͭ��'       . (int)$role_count_list['common'] .
    '���Ÿ�'         . (int)$role_count_list['fox'] .
    '�����Ǽ�'       . (int)$role_count_list['poison'] .
    '�����塼�ԥå�' . (int)$role_count_list['cupid'] .
    '��(�����'      . (int)$role_count_list['decide'] . ')' .
    '��(���ϼ�'      . (int)$role_count_list['authority'] . ')';

  //���ꥹ������
  $time = TZTime(); //���߻�������
  InsertSystemTalk($sentence, $time, 'night system', 1);
  UpdateTime($time); //�ǽ��񤭹��߻���򹹿�

  //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  InsertSystemMessage('1', 'VOTE_TIMES', 1);
  mysql_query('COMMIT'); //������ߥå�
}

//�������� Kick ��ɼ�ν���
function VoteKick($target){
  global $GAME_CONF, $room_no, $situation, $day_night, $uname, $handle_name, $target_no;

  //���顼�����å�
  if($situation != 'KICK_DO') OutputVoteResult('Kick��̵������ɼ�Ǥ�');
  if($target == '') OutputVoteResult('Kick����ɼ�����ꤷ�Ƥ�������');
  if($target == '�����귯') OutputVoteResult('Kick�������귯�ˤ���ɼ�Ǥ��ޤ���');

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND user_entry.handle_name = '$target' AND vote.room_no = $room_no
			AND vote.uname = '$uname' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick��' . $target . ' �� Kick ��ɼ�Ѥ�');

  //��ʬ����ɼ�Ǥ��ޤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND handle_name ='$target' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick����ʬ�ˤ���ɼ�Ǥ��ޤ���');

  LockTable(); //�ơ��֥����¾Ū��å�

  //�����೫�ϥ����å�
  $sql = mysql_query("SELECT day_night FROM room WHERE room_no = $room_no");
  if(mysql_result($sql, 0, 0) != 'beforegame')
    OutputVoteResult('Kick�����˥�����ϳ��Ϥ���Ƥ��ޤ�', true);

  //�������åȤΥ桼��̾�����
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick��'. $target . '�Ϥ��Ǥ� Kick ����Ƥ��ޤ�', true);

  //��ɼ����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, situation)
			VALUES($room_no, 0, '$uname', '$target_uname', 'KICK_DO')");
  //��ɼ���ޤ�������
  InsertSystemTalk("KICK_DO\t" . $target, TZTime(), '', 0, $uname);

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){ //������ߥå�
    CheckVoteKick($target); //���׽���
    OutputVoteResult('��ɼ��λ(Kick ����ˤ� ' . $GAME_CONF -> kick . ' �Ͱʾ����ɼ��ɬ�פǤ�)', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//Kick ��ɼ�ν��׽���
function CheckVoteKick($target){
  global $GAME_CONF, $MESSAGE, $room_no, $situation;

  if($situation != 'KICK_DO') OutputVoteResult('Kick��̵������ɼ�Ǥ�');

  //������ɼ�������ز�����ɼ���Ƥ��뤫
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = '$situation' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //��ɼ��������

  //������ʾ����ɼ����ä����˽���
  if($vote_count < $GAME_CONF -> kick) return false;
  //�桼����������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //Kick ����ͤ� user_no �����
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick ���줿�ͤϻ�˴��user_no �� -1�����å���� ID ��������
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = $room_no AND handle_name = '$target' AND user_no > 0");

  //�����ξ�硢�罸����᤹
  mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = $room_no");
  DeleteVote(); //���ޤǤ���ɼ���������

  //���å�����ƶ���������ͤ��
  for($i = $target_no; $i < $user_count ; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE user_no = $next");
  }

  //�ǽ��񤭹��߻���򹹿�
  $time = TZTime();  //���߻�������
  UpdateTime($time);

  //�ФƹԤä���å�����
  $time++; //��ɼ��å����������ɽ�������褦��
  InsertSystemTalk($target . $MESSAGE -> kick_out, $time);

  $time++; //�ФƹԤä���å����������ɽ�������褦��
  InsertSystemTalk($MESSAGE -> vote_reset, $time);

  mysql_query('COMMIT'); //������ߥå�
}

//�����ɼ����
function VoteDay(){
  global $room_no, $situation, $date, $vote_times, $uname, $handle_name, $target_no;

  if($situation != 'VOTE_KILL') OutputVoteResult('�跺����ɼ���顼');

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation' AND vote_times = $vote_times");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  //��ɼ���Υ桼���������
  $sql = mysql_query("SELECT uname, handle_name, live FROM user_entry
			WHERE room_no = $room_no AND user_no = $target_no");
  $array = mysql_fetch_assoc($sql);
  $target_uname  = $array['uname'];
  $target_handle = $array['handle_name'];
  $target_live   = $array['live'];

  //��ʬ������԰�����꤬��ʤ�����̵��
  if($target_live == 'dead' || $target_uname == $uname || $target_uname == '')
    OutputVoteResult('�跺����ɼ�褬����������ޤ���');

  LockTable(); //�ơ��֥����¾Ū��å�

  //��ɼ����
  //��ʬ���������
  $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  $role = mysql_result($sql, 0, 0);

  //���ϼԤʤ���ɼ������
  $vote_number = (strstr($role, 'authority') ? 2 : 1);

  //��ɼ
  $sql = mysql_query("INSERT INTO vote(room_no,date,uname,target_uname,vote_number,vote_times,situation)
		VALUES($room_no,$date,'$uname','$target_uname',$vote_number,$vote_times,'$situation')");

  //��ɼ���ޤ�������
  InsertSystemTalk("VOTE_DO\t" . $target_handle, TZTime(), 'day system', '', $uname);

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    CheckVoteDay(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����ɼ���׽���
function CheckVoteDay(){
  global $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('�跺����ɼ���顼');

  //��ɼ��������
  $sql = mysql_query("select count(uname) from vote where room_no = $room_no
			and date = $date and situation = '$situation'
			and vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ���桼���������
  $sql_user = mysql_query("select uname,handle_name,role from user_entry
		where room_no = $room_no and live = 'live' and user_no > 0 order by user_no");
  $user_count = mysql_num_rows($sql_user);

  //��������ɼ���Ƥ������
  if($vote_count != $user_count) return false;

  $check_draw = true; //����ʬ��Ƚ��¹ԥե饰
  $max_voted_number = 0; //�Ǥ�ɼ�������줿�ͤ�ɼ��
  $vote_number_list = array(); //��ɼ���줿�ͤȼ�������ɼ���Υꥹ�ȡ�user1�ˣ�ɼ���äƤ��� ��$vote_number_list['user1'] => 3��
  $vote_role_list = array(); //��ɼ���줿�ͤ����ꥹ��
  $live_handle_name_list = array(); //�����Ƥ���ͤΥϥ�ɥ�͡���ꥹ��

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  for($i = 0 ; $i < $user_count ; $i++){
    //�桼��No�μ㤤�礫�����
    $this_user_arr = mysql_fetch_assoc($sql_user);
    $this_uname = $this_user_arr['uname'];
    $this_handle_name = $this_user_arr['handle_name'];
    $this_role = $this_user_arr['role'];

    //��ʬ����ɼ���줿��ɾ��
    $sql = mysql_query("select sum(vote_number) from vote where room_no = $room_no and date = $date
			and situation = '$situation' and vote_times = $vote_times
			and target_uname = '$this_uname'");
    //��ɼ���줿��ɼ��
    $this_voted_number = (int)mysql_result($sql, 0, 0);

    //��ʬ����ɼ����ɼ��
    $sql =mysql_query("select vote_number from vote where room_no = $room_no and date = $date
			and situation = '$situation' and vote_times = $vote_times
			and uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //��ʬ����ɼ�����ͤΥϥ�ɥ�͡�������
    $sql = mysql_query("select user_entry.handle_name as handle_name from user_entry,vote 
			where user_entry.room_no = $room_no and vote.room_no = $room_no 
			and vote.date = $date
			and vote.situation = '$situation' and vote_times = $vote_times
			and vote.uname = '$this_uname' and user_entry.uname = vote.target_uname
			and user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //��ɼ��̤򥿥ֶ��ڤ�ǽ��� ( ï�� [TAB] ï�� [TAB] ��ʬ�ؤ���ɼ�� [TAB] ��ʬ����ɼ�� [TAB] vote_times)
    $sentence = $this_handle_name . "\t" .  $this_vote_target . "\t" .
      (int)$this_voted_number ."\t" . (int)$this_vote_number . "\t" . (int)$vote_times ;

    //��ɼ����򥷥��ƥ��å���������Ͽ
    InsertSystemMessage($sentence, $situation);

    //����ɼ���򹹿�
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //��ɼ���줿�ͤȼ�������ɼ���Υꥹ�ȡ�user1�ˣ�ɼ���äƤ��� ��$vote_HN_number_list['user1_handle_name'] => 3��
    $vote_HN_number_list[$this_handle_name] = $this_voted_number;
    $vote_uname_number_list[$this_uname] = $this_voted_number; //$vote_uname_number_list['user1_uname'] => 3��

    $vote_role_list[$this_handle_name] = $this_role; //$vote_role_list['user1'] => 'human'
    array_push($live_handle_name_list,$this_handle_name); //�����Ƥ���ͤΥꥹ��
  }

  //����ɼ���򽸤᤿�ͤο������
  $max_voted_num_arr = array_count_values($vote_HN_number_list); // $max_voted_num_arr[ɼ��] = ����ɼ���ϲ��Ĥ��ä���
  $max_voted_num = $max_voted_num_arr[$max_voted_number]; //$max_voted_num_arr[����ɼ��]�οͤοͿ�

  //����ɼ���οͤΥϥ�ɥ�͡���Υꥹ�Ȥ����
  //$max_voted_HN_arr[0,1,2������] = ����ɼ���οͤΥϥ�ɥ�͡���
  $max_voted_HN_arr = array_keys($vote_HN_number_list,$max_voted_number);

  //$max_voted_HN_arr[0,1,2������] = ����ɼ���οͤΥϥ�ɥ�͡���
  $max_voted_uname_arr = array_keys($vote_uname_number_list,$max_voted_number);

  if($max_voted_num == 1){ //��ͤ����ξ�硢�跺������ˤ���
    $max_voted_handle_name = $max_voted_HN_arr[0];
    //�跺�����ͤ����
    $max_voted_role = $vote_role_list[$max_voted_handle_name];

    //�跺
    VoteKill($max_voted_handle_name,$max_voted_role,$live_handle_name_list);
    $check_draw = false;
  }
  else{ //ʣ�������Ф���������Ԥ���ʤ���к���ɼ
    $re_voting_flag = true; //����ɼ�ե饰�����

    for($i=0 ; $i < $max_voted_num ; $i++){
      $max_vote_uname = $max_voted_uname_arr[$i]; //��ɼ���줿�ͤΥ桼��̾����
      $max_voted_handle_name = $max_voted_HN_arr[$i]; //��ɼ���줿�ͤΥϥ�ɥ�͡������
      $max_voted_role = $vote_role_list[$max_voted_handle_name]; //��ɼ���줿�ͤ�������

      //��ɼ�Ԥ�������
      $sql_max_voter_role = mysql_query("select user_entry.role from user_entry,vote 
					where user_entry.room_no = $room_no 
					and vote.room_no = $room_no and vote.date = $date
						and vote.situation = '$situation'
					and vote.vote_times = $vote_times
					and vote.uname = user_entry.uname
					and vote.target_uname = '$max_vote_uname'
					and user_entry.user_no > 0");
      $max_voter_count = mysql_num_rows($sql_max_voter_role);

      for($j=0 ; $j < $max_voter_count ; $j++){
	$max_voter_role = mysql_result($sql_max_voter_role,$j,0);

	if(strstr($max_voter_role,"decide")){ //��ɼ�Ԥ�����Ԥʤ�跺
	  $re_voting_flag = false;
	  break;
	}
      }
      if($re_voting_flag == false) break;
    }

    if($re_voting_flag == true){ //����ɼ
      //��ɼ��������䤹
      $next_vote_times = $vote_times +1 ;

      mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

      //�����ƥ��å�����
      $time = TZTime();
      InsertSystemMessage($vote_times, 'RE_VOTE');
      InsertSystemTalk("����ɼ�ˤʤ�ޤ���( $vote_times ����)", $time);

      $time++;
      UpdateTime($time); //�ǽ��񤭹��ߤ򹹿�
    }
    else{ //�跺������ˤ���
      VoteKill($max_voted_handle_name, $max_voted_role, $live_handle_name_list);
      $check_draw = false;
    }
  }
  CheckVictory($check_draw);
}

//��ɼ�ǽ跺����
function VoteKill($handle_name, $role, $live_list){
  global $room_no, $date;

  DeadUser($handle_name, true); //�跺

  //�跺���줿�ͤ����ǼԤξ��
  if(strstr($role,"poison")){
    //¾�οͤ��������˰������
    $diff  = array("$handle_name");
    $array = array_diff($live_list, $diff);
    $rand_key = array_rand($array, 1);
    $poison_dead_handle = $array[$rand_key];

    DeadUser($poison_dead_handle, true); //�ǻ�
    InsertSystemMessage($poison_dead_handle, 'POISON_DEAD_day'); //�ǻ�(�����ƥ��å�����)

    //�ǻष���ͤ��򿦡���������
    $res_poison = mysql_query("select role,last_words from user_entry where room_no = $room_no
				and handle_name = '$poison_dead_handle' and user_no > 0");
    $poison_array = mysql_fetch_assoc($res_poison);
    $poison_role  = $poison_array['role'];
    $poison_last_words = $poison_array['last_words'];

    //�ǻष���ͤΰ����Ĥ�
    if($poison_last_words != '')
      InsertSystemMessage($poison_dead_handle . "\t" . $poison_last_words, 'LAST_WORDS');

    //�ǻष���ͤ����ͤξ��
    if(strstr($poison_role, 'lovers')) LoversFollowed();
  }

  //�跺���줿�ͤ����ͤξ��
  if(strstr($role, 'lovers')) LoversFollowed();

  //��ǽ�Ԥη��(�����ƥ��å�����)
  if(strstr($role, 'wolf'))
    $necro_max_voted_role = 'wolf';
  else
    $necro_max_voted_role = 'human';

  InsertSystemMessage($handle_name . "\t" . $necro_max_voted_role, 'NECROMANCER_RESULT');

  //�跺���줿��å�����
  InsertSystemMessage($handle_name, 'VOTE_KILLED');

  //�跺���줿�ͤΰ����Ĥ�
  SaveLastWords($handle_name);

  $time = TZTime();  //���߻�������
  UpdateTime($time); //�ǽ��񤭹��ߤ򹹿�
  mysql_query("update room set day_night = 'night' where room_no = $room_no"); //��ˤ���
  DeleteVote(); //���ޤǤ���ɼ���������

  //�뤬��������
  $time++;
  InsertSystemTalk('NIGHT', $time, 'night system');

  mysql_query('COMMIT'); //������ߥå�
}

//�����ɼ����
function VoteNight(){
  global $GAME_CONF, $room_no, $situation, $date, $uname, $handle_name, $role, $target_no;

  switch($situation){
    case('WOLF_EAT'):
      if(! strstr($role, 'wolf')) OutputVoteResult('�롧��ϵ�ʳ�����ɼ�Ǥ��ޤ���');
      break;

    case('MAGE_DO'):
      if(! strstr($role, 'mage')) OutputVoteResult('�롧�ꤤ�հʳ�����ɼ�Ǥ��ޤ���');
      if($uname == 'dummy_boy')   OutputVoteResult('�롧�����귯���ꤤ��̵���Ǥ�');
      break;

    case('GUARD_DO'):
      if(! strstr($role, 'guard')) OutputVoteResult('�롧��Ͱʳ�����ɼ�Ǥ��ޤ���');
      break;

    case('CUPID_DO'):
      if(! strstr($role, 'cupid')) OutputVoteResult('�롧���塼�ԥåɰʳ�����ɼ�Ǥ��ޤ���');
      break;

    default:
      OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
      break;
  }
  CheckAlreadyVote($situation); //��ɼ�Ѥߥ����å�

 //���顼��å������Υإå�
  $error_header = '�롧��ɼ�褬����������ޤ���<br>';

  if(strstr($role, 'cupid')){  //���塼�ԥåɤξ�����ɼ����
    if(count($target_no) != 2) OutputVoteResult('�롧����Ϳ������ͤǤϤ���ޤ���');
    $self_shoot = false;
    foreach($target_no as $lovers_target_no){
      //��ɼ���Υ桼���������
      $sql = mysql_query("SELECT uname, live FROM user_entry WHERE room_no = $room_no
				AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname = $array['uname'];
      $target_live  = $array['live'];

      //��ʬ�Ǥ����ɤ��������å�
      if($target_uname == $uname) $self_shoot = true;

      //��԰��������귯�ؤ���ɼ��̵��
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('��ԡ������귯�ؤ���ɼ�Ǥ��ޤ���');
    }

    //�桼����������
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF -> cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
    }
  }
  else{ //���塼�ԥåɰʳ�����ɼ����
    //��ɼ���Υ桼���������
    $sql = mysql_query("SELECT uname, handle_name, role, live FROM user_entry
			WHERE room_no = $room_no AND user_no = $target_no");
    $array = mysql_fetch_assoc($sql);
    $target_uname  = $array['uname'];
    $target_handle = $array['handle_name'];
    $target_role   = $array['role'];
    $target_live   = $array['live'];

    //��ʬ������԰���ϵƱ�Τ���ɼ��̵��
    if($target_live == 'dead' || $target_uname == $uname ||
       (strstr($role, 'wolf') && strstr($target_role, 'wolf'))){
      OutputVoteResult($error_header . '��ԡ���ʬ��ϵƱ�Τ���ɼ�Ǥ��ޤ���');
    }

    //ϵ�ν�������ɼ�Ͽ����귯���Ѥξ��Ͽ����귯�ʳ�̵��
    if($situation == 'WOLF_EAT'){
      $game_option = GetGameOption();
      if(strstr($game_option, 'dummy_boy') && $target_uname != 'dummy_boy' && $date == 1){
	OutputVoteResult($error_header . '�����귯���Ѥξ��ϡ������귯�ʳ�����ɼ�Ǥ��ޤ���');
      }
    }
  }

  LockTable(); //�ơ��֥����¾Ū��å�
  if(strstr($role, 'cupid')){ // ���塼�ԥåɤν���
    $target_uname_str  = '';
    $target_handle_str = '';
    foreach ($target_no as $lovers_target_no){
      //��ɼ���Υ桼���������
      $sql = mysql_query("SELECT uname, handle_name, role FROM user_entry
				WHERE room_no = $room_no AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname  = $array['uname'];
      $target_handle = $array['handle_name'];
      $target_role   = $array['role'];

      $target_uname_str  .= $target_uname  . ' ';
      $target_handle_str .= $target_handle . ' ';

      //�򿦤����ͤ��ɲ�
      $target_role .= " lovers";
      mysql_query("UPDATE user_entry SET role = '$target_role' WHERE room_no = $room_no
			AND uname = '$target_uname' AND user_no > 0");
    }
    $target_uname_str  = rtrim($target_uname_str);
    $target_handle_str = rtrim($target_handle_str);
  }
  else{ // ���塼�ԥåɰʳ��ν���
    $target_uname_str  = $target_uname;
    $target_handle_str = $target_handle;
  }

  //��ɼ
  $sql_vote = mysql_query("INSERT INTO vote(room_no,date,uname,target_uname,vote_number,situation)
			VALUES($room_no,$date,'$uname','$target_uname_str',1,'$situation')");
  //�����ƥ��å�����
  InsertSystemMessage($handle_name . "\t" . $target_handle_str, $situation);

  //��ɼ���ޤ�������
  $sentence = $situation . "\t" . $target_handle_str;
  InsertSystemTalk($sentence, TZTime(), 'night system', '', $uname);

  //��Ͽ����
  if($sql_vote && mysql_query('COMMIT')){
    CheckVoteNight(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//��ν��׽���
function CheckVoteNight(){
  global $GAME_CONF, $room_no, $situation, $date, $day_night, $vote_times,
    $uname, $handle_name, $target_no, $target_handle_name;

  //�����४�ץ�������
  $game_option = GetGameOption();

  if(! ($situation == 'WOLF_EAT' || $situation == 'MAGE_DO' ||
	$situation == 'GUARD_DO' || $situation == 'CUPID_DO')){
    OutputVoteResult('�롧��ɼ���顼');
  }

  $query_header = "SELECT COUNT(uname) FROM";
  $query_vote   = "$query_header vote WHERE room_no = $room_no AND date = $date AND situation = ";
  $query_role   = "$query_header user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND role LIKE ";

  //ϵ����ɼ�����å�
  $sql = mysql_query($query_vote . "'WOLF_EAT'");
  if(mysql_result($sql, 0, 0) < 1) return false; //ϵ�������ǰ��ʬ

  //�ꤤ�դ���ɼ�����å�
  $sql = mysql_query($query_vote . "'MAGE_DO'");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ����ꤤ�դο������
  $sql = mysql_query($query_role . "'mage%'");
  $mage_count = mysql_result($sql, 0, 0);

  if($date == 1 && strstr($game_option, 'dummy_boy')){
    //�����������귯����䤬�ꤤ�դξ���ꤤ�դο�������ʤ�
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    $dummy_boy_role = mysql_result($sql, 0, 0);
    if(strstr($dummy_boy_role, 'mage')) $mage_count--;
  }
  if($vote_count != $mage_count) return false;

  $guard_count = 0;
  $cupid_count = 0;
  if($date == 1){ //�����Τߥ��塼�ԥåɤ���ɼ�����å�
    $sql = mysql_query($query_vote . "'CUPID_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //�����Ƥ��륭�塼�ԥåɤο������
    $sql = mysql_query($query_role . "'cupid%'");
    $cupid_count = mysql_result($sql, 0, 0);
    if($vote_count != $cupid_count) return false;
  }
  else{ //�����ʳ��μ�ͤ���ɼ�����å�
    $sql = mysql_query($query_vote . "'GUARD_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'guard%'");
    $guard_count = mysql_result($sql, 0, 0);
    if($vote_count != $guard_count) return false;
  }

  //ϵ�ȼ�ͤ�Ʊ���˽���
  //��ͤ���ɼ��桼��̾����ͤΥϥ�ɥ�͡�������
  $sql_guard = mysql_query("SELECT vote.target_uname, user_entry.handle_name FROM vote,	user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'GUARD_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //ϵ����ɼ��桼��̾�Ȥ����������
  $sql_wolf = mysql_query("SELECT vote.target_uname, user_entry.role, user_entry.handle_name
				FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'WOLF_EAT'
				AND vote.target_uname = user_entry.uname AND user_entry.user_no > 0");
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_role   = $wolf_target_array['role'];
  $wolf_target_live   = $wolf_target_array['live'];//��DB��������Ƥʤ��褦�ʡ�����
  $wolf_target_handle = $wolf_target_array['handle_name'];

  $guard_success_flag = false;
  for($i=0; $i < $guard_count; $i++ ){ //��������������å�
    $guard_array  = mysql_fetch_assoc($sql_guard);
    $guard_handle = $guard_array['handle_name'];
    $guard_uname  = $guard_array['target_uname'];

    if($guard_uname == $wolf_target_uname){ //�������
      //��������Υ�å�����
      $system_message = $guard_handle . "\t" . $wolf_target_handle;
      InsertSystemMessage($system_message, 'GUARD_SUCCESS');
      $guard_success_flag = true;
    }
  }

  if($guard_success_flag){
    //�������
  }
  elseif(strstr($wolf_target_role, 'fox')){ //���٤��褬�Ѥξ�翩�٤�ʤ�
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //��Ҥ���Ƥʤ���п��٤�
    DeadUser($wolf_target_uname); //���٤�줿�ͻ�˴
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED'); //�����ƥ��å�����

    //���٤�줿�ͤΰ����Ĥ�
    SaveLastWords($wolf_target_handle);

    //���٤�줿�ͤ����ǼԤξ��
    if(strstr($wolf_target_role, 'poison')){
      if($GAME_CONF -> poison_only_eater){ //�����ϵ�����
	$sql_wolf_list = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role
					FROM user_entry, vote WHERE user_entry.room_no = $room_no
					AND user_entry.uname = vote.uname AND vote.date = $date
					AND vote.situation = 'WOLF_EAT' AND user_no > 0");
      }
      else{ //�����Ƥ���ϵ�����
	$sql_wolf_list = mysql_query("SELECT uname, handle_name, role FROM user_entry
					WHERE room_no = $room_no AND role LIKE 'wolf%'
					AND live = 'live' AND user_no > 0");
      }
      $poison_wolf_count = mysql_num_rows($sql_wolf_list);

      $wolf_list_array = array();
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false){
	array_push($wolf_list_array, $wolf);
      }

      $rand_key = array_rand($wolf_list_array, 1);
      $poison_dead_wolf_array  = $wolf_list_array[$rand_key];
      $poison_dead_wolf_uname  = $poison_dead_wolf_array['uname'];
      $poison_dead_wolf_handle = $poison_dead_wolf_array['handle_name'];
      $poison_dead_wolf_role   = $poison_dead_wolf_array['role'];

      DeadUser($poison_dead_wolf_uname); //�ǻ�
      InsertSystemMessage($poison_dead_wolf_handle, 'POISON_DEAD_night'); //�ǻ�(�����ƥ��å�����)
      SaveLastWords($poison_dead_wolf_handle); //�ǻष���ͤΰ����Ĥ�
      if(strstr($poison_dead_wolf_role, 'lovers')) LoversFollowed(); //�ǻष��ϵ�����ͤξ��
    }
    if(strstr($wolf_target_role, 'lovers')) LoversFollowed(); //���٤�줿�ͤ����ͤξ��
  }

  //�ꤤ�դΥ桼��̾���ϥ�ɥ�͡���ȡ��ꤤ�դ���¸���ꤤ�դ���ä��桼��̾����
  $sql_mage = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.live, 
				vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'MAGE_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //�ꤤ�դοͿ�ʬ������
  for($i=0; $i < $mage_count; $i++){
    $array = mysql_fetch_assoc($sql_mage);
    $mage_uname  = $array['uname'];
    $mage_handle = $array['handle_name'];
    $mage_live   = $array['live'];
    $mage_target_uname = $array['target_uname'];

    //ľ����ϵ�˿��٤��Ƥ����餳���ꤤ��̵��
    if($mage_live == 'dead') continue;

    //�ꤤ�դ����줿�ͤΥϥ�ɥ�͡������¸���������
    $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$mage_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $mage_target_handle = $array['handle_name'];
    $mage_target_role   = $array['role'];
    $mage_target_live   = $array['live'];

    if(strstr($mage_target_role, 'fox') && $mage_target_live == 'live'){ //�Ѥ����줿���˴
      DeadUser($mage_target_uname);
      InsertSystemMessage($mage_target_handle, 'FOX_DEAD');
      SaveLastWords($mage_target_handle); //���줿�Ѥΰ����Ĥ�
      if(strstr($mage_target_role, 'lovers')) LoversFollowed(); //���줿�Ѥ����ͤξ��
    }

    //�ꤤ��̤����
    $sentence = $mage_handle . "\t" . $mage_target_handle . "\t" .
      (strstr($mage_target_role, 'wolf') ? 'wolf' : 'human');
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  //�������ˤ���
  $next_date = $date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");
  DeleteVote(); //���ޤǤ���ɼ���������

  $time = TZTime(); //���߻�������
  UpdateTime($time); //�ǽ��񤭹��ߤ򹹿�

  //�������ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //�뤬����������
  InsertSystemTalk("MORNING\t" . $next_date, $time, $location = 'day system', $next_date);

  //���ԤΥ����å�
  CheckVictory();
  mysql_query('COMMIT'); //������ߥå�
}

//����������ɼ�ڡ�������
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $day_night, $uname, $php_argv;

  //�桼����������
  $sql = mysql_query("SELECT user_entry.uname, user_entry.handle_name,
			user_icon.icon_filename, user_icon.color,
			user_icon.icon_width, user_icon.icon_height
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  $count  = mysql_num_rows($sql);
  $width  = $ICON_CONF -> width;
  $height = $ICON_CONF -> height;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  for($i=0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);

    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_file   = $array['icon_filename'];
    $this_color  = $array['color'];
    // $this_width  = $array['icon_width'];
    // $this_height = $array['icon_height'];

    //5�Ĥ��Ȥ˲���
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";
    $location = $ICON_CONF -> path . '/' . $this_file;

    //HTML����
    echo <<< EOF
<td><label for="$this_handle">
<img src="$location" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;
    if($this_uname != 'dummy_boy' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_handle . '" name="target_handle_name" value="' .
	$this_handle . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<< EOF
</tr></table>
<span class="vote-message">* Kick ����ˤ� {$GAME_CONF -> kick} �ͤ���ɼ��ɬ�פǤ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE -> submit_kick_do}"></form></td>
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="command" value="vote">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$MESSAGE -> submit_game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteDay(){
  global $MESSAGE, $ICON_CONF, $room_no, $date, $uname, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ��������(����ɼ�ʤ� $vote_times ��������)
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND type = 'VOTE_TIMES' AND date = $date");
  $vote_times = (int)mysql_result($sql, 0, 0);

  //��ɼ�Ѥߤ��ɤ���
  $sql = mysql_query("SELECT count(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  if(mysql_result($sql, 0, 0)) OutputVoteResult('�跺����ɼ�Ѥ�');

  //�桼�������ȥ�������Υǡ�������
  $sql_user = mysql_query("SELECT user_entry.user_no, user_entry.uname,
			user_entry.handle_name, user_entry.live,
			user_icon.icon_filename, user_icon.color as color,
			user_icon.icon_width, user_icon.icon_height
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_no > 0 ORDER BY user_entry.user_no");
  $user_count = mysql_num_rows($sql_user); //�桼����

  OutputVotePageHeader();
  echo <<< EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $width  = $ICON_CONF -> width;
  $height = $ICON_CONF -> height;
  for($i=0; $i < $user_count; $i++){
    $array = mysql_fetch_assoc($sql_user);

    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];
    //��������Υ������ϻ��Ȥ����ѿ����Ѥ���(���Ѥ����Х����������)
    //$this_width   = $array['icon_width'];
    //$this_height  = $array['icon_height'];

    //5�Ĥ��Ȥ˲���
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";

    if($this_live == 'live'){ //�����Ƥ���Х桼����������
      $location = $ICON_CONF -> path . '/' . $this_file;
    }
    else{ //���Ǥ�л�˴��������
      $location = $ICON_CONF -> dead;
    }

    echo <<< EOF
<td><label for="$this_user_no">
<img src="$location" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($this_live == 'live' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<< EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE -> submit_vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteNight(){
  global $MESSAGE, $ICON_CONF, $room_no, $date, $uname, $role, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();
  if(! (strstr($role, 'wolf') || strstr($role, 'mage') || strstr($role, 'guard') ||
	(strstr($role, 'cupid') && $date == 1))){
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
  }

  //��ɼ�Ѥߥ����å�
  if(strstr($role, 'wolf')){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif(strstr($role, 'mage')){
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯���ꤤ��̵���Ǥ�');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif(strstr($role, 'guard')){
    if($date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif(strstr($role, 'cupid')){
    CheckAlreadyVote('CUPID_DO');
  }
  else{ //�������褿����å����顼
    OutputVoteResult(�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ�������);
  }

  //�����४�ץ�������(�����귯��)
  $game_option = GetGameOption();

  if(strstr($role, 'wolf') && strstr($game_option, 'dummy_boy') && $date == 1){
    //�����귯�λ��Ͽ����귯�����������٤ʤ�
    //�����귯�Υ桼������
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.handle_name,
			user_entry.live, user_icon.icon_filename, user_icon.color,
			user_icon.icon_width, user_icon.icon_height
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.uname = 'dummy_boy' AND user_entry.live = 'live'");
  }
  else{
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.uname, user_entry.handle_name,
			user_entry.live, user_entry.role, user_icon.icon_filename,
			user_icon.color, user_icon.icon_width, user_icon.icon_height
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  }
  $sql_count = mysql_num_rows($sql);

  OutputVotePageHeader();
  echo <<< EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $width  = $ICON_CONF -> width;
  $height = $ICON_CONF -> height;
  for($i=0; $i < $sql_count; $i++){
    $array = mysql_fetch_assoc($sql);

    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_role    = $array['role'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];
    //��������Υ������ϻ��Ȥ����ѿ����Ѥ���(���Ѥ����Х����������)
    //$this_width   = $array['icon_width'];
    //$this_height  = $array['icon_height'];

    //5�Ĥ��Ȥ˲���
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";

    if($this_live == 'live' && strstr($role, 'wolf') && strstr($this_role, 'wolf')){ //ϵƱ�Τʤ�ϵ��������
      $location = $ICON_CONF -> wolf;
    }
    elseif($this_live == 'live'){ //�����Ƥ���Х桼����������
      $location = $ICON_CONF -> path . '/' . $this_file;
    }
    else{ //���Ǥ�л�˴��������
      $location = $ICON_CONF -> dead;
    }

    echo <<< EOF
<td><label for="$this_user_no">
<img src="$location" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if(strstr($role, 'cupid')){
      if(! strstr($this_uname, 'dummy_boy')){
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname &&
	   ! (strstr($role, 'wolf') && strstr($this_role, 'wolf'))){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<< EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>

EOF;

  if(strstr($role, 'wolf')){
    echo '<input type="hidden" name="situation" value="WOLF_EAT">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE -> submit_wolf_eat . '"></td>'."\n";
  }
  elseif(strstr($role, 'mage')){
    echo '<input type="hidden" name="situation" value="MAGE_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE -> submit_mage_do . '"></td>'."\n";
  }
  elseif(strstr($role, 'guard')){
    echo '<input type="hidden" name="situation" value="GUARD_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE -> submit_guard_do . '"></td>'."\n";
  }
  elseif(strstr($role, 'cupid')){
    echo '<input type="hidden" name="situation" value="CUPID_DO">'."\n";
    echo '<td><input type="submit" value="' . $MESSAGE -> submit_cupid_do . '"></td>'."\n";
  }

  echo <<< EOF
</tr></table></div>
</form></body></html>

EOF;
}

//�ơ��֥����¾Ū��å�
function LockTable(){
  if(! mysql_query("LOCK TABLES room WRITE, user_entry WRITE, vote WRITE,
			system_message WRITE, talk WRITE")){
    OutputVoteResult('�����Ф��������Ƥ��ޤ���<br>������ɼ�򤪴ꤤ���ޤ���');
  }
}

//��ɼ������������äƤ��뤫�����å�
function CheckDayNight(){
  global $room_no, $day_night, $uname;

  $sql = mysql_query("SELECT last_load_day_night FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != $day_night) OutputVoteResult('��äƥ���ɤ��Ƥ�������');
}

//��ɼ�Ѥߥ����å�
function CheckAlreadyVote($situation){
  global $room_no, $date, $uname;

  if($situation == 'WOLF_EAT'){
    $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' GROUP BY situation");
    $count = mysql_num_rows($sql);
  }
  else{
    $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation'");
    $count = mysql_result($sql, 0, 0);
  }
  if($count != 0) OutputVoteResult('�롧��ɼ�Ѥ�');
}

//�����४�ץ�������(��˿����귯������)
function GetGameOption(){
  global $room_no;

  $sql = mysql_query("SELECT game_option FROM room WHERE room_no = $room_no");
  return mysql_result($sql, 0, 0);
}

//��������������¸����
function SaveLastWords($handle_name){
  global $room_no;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$handle_name' AND user_no > 0");
  $last_words = mysql_result($sql, 0, 0);
  if($last_words != ''){
    InsertSystemMessage($handle_name . "\t" . $last_words, 'LAST_WORDS');
  }
}
?>
