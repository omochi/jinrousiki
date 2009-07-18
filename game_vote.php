<?php
require_once(dirname(__FILE__) . '/include/game_vote_functions.php');

//���å���󳫻�
session_start();
$session_id = session_id();

//���������
$room_no     = (int)$_GET['room_no'];
$auto_reload = (int)$_GET['auto_reload'];
$play_sound  = $_GET['play_sound'];
$list_down   = $_GET['list_down'];

//PHP �ΰ��������
$php_argv = 'room_no=' . $room_no;
if($auto_reload > 0)     $php_argv .= '&auto_reload=' . $auto_reload;
if($play_sound  == 'on') $php_argv .= '&play_sound=on';
if($list_down   == 'on') $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">����� &amp; reload</a>';

//���å������饷�������� //DB ���䤤��碌��Τ�����
//$day_night = $_COOKIE['day_night'];

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

//�����४�ץ�������ա������󡢥��ơ����������
$sql = mysql_query("SELECT game_option, date, day_night, status FROM room WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$game_option = $array['game_option'];
$date        = $array['date'];
$day_night   = $array['day_night'];
$status      = $array['status'];

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸���֤����
$sql = mysql_query("SELECT user_no, handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no     = $array['user_no'];
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];

$command = $_POST['command']; //��ɼ�ܥ���򲡤��� or ��ɼ�ڡ�����ɽ����������
$system_time = TZTime(); //���߻�������

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
  $target_no = $_POST['target_no']; //��ɼ��� user_no
  $situation = $_POST['situation']; //��ɼ��ʬ�� (Kick���跺���ꤤ��ϵ�ʤ�) //SQL ���󥸥�����������

  if($date == 0){ //�����೫�� or Kick ��ɼ����
    if($situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($situation == 'KICK_DO'){
      //target_no �ϥ����ߥ󥰤������ؤ���ǽ��������Τ� Kick �Τ� target_handle_name �򻲾Ȥ���
      $target_handle_name = $_POST['target_handle_name'];
      EscapeStrings(&$target_handle_name); //���������׽���
      VoteKick($target_handle_name);
    }
    else{ //�������褿����å����顼
      OutputActionResult('��ɼ���顼[�����೫������ɼ]',
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
    $vote_times = (int)$_POST['vote_times']; //��ɼ��� (����ɼ�ξ��)
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
else{ //������ɼ����Ƥ���ޤ� //�������褿����å����顼����ʤ����ʡ�
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>������ɼ����Ƥ���ޤ�<br>'."\n" .
		     $back_url . '</div>');
}

DisconnectDatabase($dbHandle); //DB ��³���

// �ؿ� //
//��ɼ�ڡ��� HTML �إå�����
function OutputVotePageHeader(){
  global $day_night, $php_argv;

  OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[��ɼ]', 'game');
  if($day_night != '') echo '<link rel="stylesheet" href="css/game_' . $day_night . '.css">'."\n";
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="command" value="vote">

EOF;
}

//�����೫����ɼ�ν���
function VoteGameStart(){
  global $room_no, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');
  if($uname == 'dummy_boy') OutputVoteResult('�����ॹ�����ȡ������귯����ɼ���פǤ�');

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');

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
  global $GAME_CONF, $MESSAGE, $system_time, $room_no, $game_option, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');

  //��ɼ����������४�ץ��������
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);

  //�����귯���Ѥʤ�����귯��ʬ��û�
  if(strpos($game_option, 'dummy_boy') !== false) $vote_count++;

  //�桼����������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //��俶��ʬ��
  //����ԡ����ϼԡ����ǼԤΥ��ץ�������(¾�ȷ�Ǥ�Ǥ�����)�����
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);

  //�򿦥ꥹ�Ȥ����
  $now_role_list = GetRoleList($user_count, $option_role);
  shuffle($now_role_list); //����򥷥�åե�

  $fix_uname_list    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $fix_role_list     = array(); //�桼��̾���б��������
  $remain_uname_list = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ

  //�ե饰���å�
  $gerd      = (strpos($game_option, 'gerd')      !== false);
  $quiz      = (strpos($game_option, 'quiz')      !== false);
  $chaos     = (strpos($game_option, 'chaos')     !== false); //chaosfull ��ޤ�
  $chaosfull = (strpos($game_option, 'chaosfull') !== false);
  $wish_role = (strpos($game_option, 'wish_role') !== false);

  //���顼��å�����
  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  //�桼���ꥹ�Ȥ����
  //�����귯���򿦤����
  if(strpos($game_option, 'dummy_boy') !== false){
    // $gerd = true; //�ǥХå���
    $count = count($now_role_list);
    for($i = 0; $i < $count; $i++){
      $this_role = array_shift($now_role_list); //����ꥹ�Ȥ�����Ƭ��ȴ���Ф�
      if($gerd)     $fit_role = ($this_role == 'human'); //����ȷ�
      elseif($quiz) $fit_role = ($this_role == 'quiz');  //������¼
      else          $fit_role = (! CheckRole($this_role));

      if($fit_role){
	array_push($fix_role_list, $this_role);
	break;
      }
      array_push($now_role_list, $this_role); //����ꥹ�Ȥ��������᤹
    }

    if(count($fix_role_list) < 1){ //�����귯����Ϳ�����Ƥ��뤫�����å�
      OutputVoteResult($error_header . '�����귯����Ϳ�����Ƥ��ޤ���' .
		       $error_footer, true, true);
    }
    array_push($fix_uname_list, 'dummy_boy'); //����Ѥߥꥹ�Ȥ˿����귯���ɲ�
    shuffle($now_role_list); //ǰ�Τ���⤦��������򥷥�åե�

    $sql_user_list = mysql_query("SELECT uname, role FROM user_entry WHERE room_no = $room_no
					AND uname <> 'dummy_boy' AND user_no > 0 ORDER BY user_no");
  }
  else{
    $sql_user_list = mysql_query("SELECT uname, role FROM user_entry WHERE room_no = $room_no
					AND user_no > 0 ORDER BY user_no");
  }

  //��˾�򿦤򻲾Ȥ��ư켡�����Ԥ�
  while(($user_list_array = mysql_fetch_assoc($sql_user_list)) !== false){
    $this_uname = $user_list_array['uname'];
    $this_role  = array_shift($now_role_list); //����ꥹ�Ȥ�����Ƭ��ȴ���Ф�

    if($wish_role && ! $chaos){ //����˾���ξ�� (����ϴ�˾��̵��)
      $this_wish_role = $user_list_array['role']; //��˾�򿦤����
      $rand = mt_rand(1, 100); //����Ƚ�������
      if($this_role == $this_wish_role && $rand <= $GAME_CONF->wish_role_success){ //��˾�̤�
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list,  $this_role);
      }
      else{ //��ޤ�ʤ��ä�����̤����ꥹ�ȹԤ�
	array_push($remain_uname_list, $this_uname);
	array_push($now_role_list,  $this_role); //����ꥹ�Ȥ��������᤹
      }
    }
    else{ //����ʳ��Ϥ��Τޤ��������
      array_push($fix_uname_list, $this_uname);
      array_push($fix_role_list,  $this_role);
    }
  }

  //�켡����η�̤򸡾�
  $remain_uname_list_count = count($remain_uname_list); //̤����ԤοͿ�
  $now_role_list_count = count($now_role_list); //�Ĥ������
  if($remain_uname_list_count != $now_role_list_count){
    OutputVoteResult($error_header . '����̤����ԤοͿ� (' . $remain_uname_list_count .
		     ') ������ο� (' . $now_role_list_count . ') �����פ��Ƥ��ޤ���' .
		     $error_footer, true, true);
  }

  //̤����Ԥ�������
  foreach($remain_uname_list as $this_uname){
    array_push($fix_uname_list, $this_uname);
    array_push($fix_role_list, array_shift($now_role_list));
  }

  //������η�̤򸡾�
  $fix_uname_list_count = count($fix_uname_list); //����ԤοͿ�
  if($fix_uname_list_count != $user_count){
    OutputVoteResult($error_header . '¼�� (' . $user_count . ') ���������ԤοͿ� (' .
		     $fix_uname_list_count . ') �����פ��Ƥ��ޤ���' .
		     $error_footer, true, true);
  }

  $now_role_list_count = count($now_role_list); //�Ĥ������
  if($now_role_list_count > 0){
    OutputVoteResult($error_header . '����ꥹ�Ȥ�;�� (' . $now_role_list_count .
		     ') ������ޤ�' . $error_footer, true, true);
  }

  //��Ǥ�Ȥʤ���������
  $rand_keys = array_rand($fix_role_list, $user_count); //�����७�������
  $rand_keys_index = 0;
  $sub_role_count_list = array();

  // //�����򿦥ƥ�����
  // $test_role_list = array('plague', 'watcher');
  // for($i = 0; $i < $user_count; $i++){
  //   $this_test_role = array_shift($test_role_list);
  //   if($this_test_role == '') break;
  //   if($fix_uname_list[$i] == 'dummy_boy') continue;
  //   $fix_role_list[$i] .= ' ' . $this_test_role;
  //   $sub_role_count_list[$this_test_role]++;
  // }
  // // $test_role = 'gentleman';
  // for($i = 0; $i < $user_count; $i++){
  //   // if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $test_role;
  //   $test_role = (mt_rand(0, 1) == 1 ? 'gentleman' : 'lady');
  //   $fix_role_list[$i] .= ' ' . $test_role . ' liar';
  // }
  // $sub_role_count_list[$test_role]++;
  // $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $test_role;

  if(strpos($option_role, 'liar') !== false){ //ϵ��ǯ¼
    for($i = 0; $i < $user_count; $i++){ //�����˰����Ψ��ϵ��ǯ��Ĥ���
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' liar';
    }
  }
  if(strpos($game_option, 'sudden_death') !== false){ //�����μ�¼
    $sudden_death_list = array('chicken', 'rabbit', 'perverseness');
    for($i = 0; $i < $user_count; $i++){ //�����˥���å���Ϥ򲿤��Ĥ���
      $rand_key = array_rand($sudden_death_list);
      $fix_role_list[$i] .= ' ' . $sudden_death_list[$rand_key];
    }
  }
  if(strpos($option_role, 'decide') !== false && $user_count >= $GAME_CONF->decide){
    $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' decide';
    $sub_role_count_list['decide']++;
  }
  if(strpos($option_role, 'authority') !== false && $user_count >= $GAME_CONF->authority){
    $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' authority';
    $sub_role_count_list['authority']++;
  }
  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //������ʥ����򿦤Υ����ɥꥹ�Ȥ����
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    // $sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //�ǥХå���
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      //�������꿶���оݳ��򿦤򥹥��å�
      if($key == 'lovers' || $key == 'copied') continue;
      if((int)$sub_role_count_list[$key] > 0) continue; //����ï�����Ϥ��Ƥ���Х����å�
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
      $sub_role_count_list[$key]++;
    }
  }

  //�����೫��
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //���ޤǤ���ɼ���������

  //����DB�˹���
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $entry_uname = $fix_uname_list[$i];
    $entry_role  = $fix_role_list[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    $role_count_list[GetMainRole($entry_role)]++;
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if(strpos($entry_role, $key) !== false) $role_count_list[$key]++;
    }
  }

  //���줾�����䤬���ͤ��ĤʤΤ������ƥ��å�����
  if($chaos && strpos($option_role, 'chaos_open_cast') === false)
    $sentence = $MESSAGE->chaos;
  else
    $sentence = MakeRoleNameList($role_count_list);
  InsertSystemTalk($sentence, $system_time, 'night system', 1);  //���ꥹ������
  InsertSystemMessage('1', 'VOTE_TIMES', 1); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  if($chaosfull) CheckVictory(); //��������Ϥ����ʤ꽪λ���Ƥ��ǽ������
  mysql_query('COMMIT'); //������ߥå�
}

//�������� Kick ��ɼ�ν��� ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $day_night, $uname, $handle_name, $target_no;

  //���顼�����å�
  if($situation != 'KICK_DO') OutputVoteResult('Kick��̵������ɼ�Ǥ�');
  if($target == '') OutputVoteResult('Kick����ɼ�����ꤷ�Ƥ�������');
  if($target == '�����귯') OutputVoteResult('Kick�������귯�ˤ���ɼ�Ǥ��ޤ���');
  if(strpos($game_option, 'quiz') !== false && $target == 'GM')
    OutputVoteResult('Kick��GM �ˤ���ɼ�Ǥ��ޤ���'); //������¼�б�

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
  if($target_uname == '') OutputVoteResult('Kick��'. $target . ' �Ϥ��Ǥ� Kick ����Ƥ��ޤ�', true);

  //��ɼ����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, situation)
			VALUES($room_no, 0, '$uname', '$target_uname', 'KICK_DO')");
  InsertSystemTalk("KICK_DO\t" . $target, $system_time, '', 0, $uname); //��ɼ���ޤ�������

  //��ɼ����
  if($sql && mysql_query('COMMIT')){ //������ߥå�
    $vote_count = CheckVoteKick($target); //���׽���
    OutputVoteResult('��ɼ��λ��' . $target . '��' . $vote_count . '���� (Kick ����ˤ� ' .
		     $GAME_CONF->kick . ' �Ͱʾ����ɼ��ɬ�פǤ�)', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//Kick ��ɼ�ν��׽��� ($target : �о� HN, �֤��� : �о� HN ����ɼ��׿�)
function CheckVoteKick($target){
  global $GAME_CONF, $MESSAGE, $system_time, $room_no, $situation, $uname;

  if($situation != 'KICK_DO') OutputVoteResult('Kick��̵������ɼ�Ǥ�');

  //������ɼ�������ز�����ɼ���Ƥ��뤫
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = '$situation' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //��ɼ��������

  //������ʾ����ɼ�����ä������å����������귯�ξ��˽���
  if($vote_count < $GAME_CONF->kick && $uname != 'dummy_boy') return $vote_count;

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

  // //�����ξ�硢�罸����᤹ //���ߤ���������ɽ�����Ѥ��ʤ��ΤǤ��ν��������פ���ʤ����ʡ�
  // mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = $room_no");

  //���å�����ƶ���������ͤ��
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = $room_no AND user_no = $next");
  }

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$system_time); //�ФƹԤä���å�����
  InsertSystemTalk($MESSAGE->vote_reset, ++$system_time); //��ɼ�ꥻ�å�����
  UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  mysql_query('COMMIT'); //������ߥå�
  return $vote_count;
}

//�����ɼ����
function VoteDay(){
  global $system_time, $room_no, $situation, $date, $vote_times, $uname, $handle_name, $target_no;

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

  //�򿦤˱�����ɼ�������
  $vote_number = 1;
  if(strpos($role, 'authority') !== false) $vote_number++; //���ϼ�
  elseif(strpos($role, 'watcher') !== false) $vote_number = 0; //˵�Ѽ�
  elseif(strpos($role, 'random_voter') !== false) $vote_number = mt_rand(0, 2); //��ʬ��

  //��ɼ�������ƥ��å�����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number,
			vote_times, situation)
			VALUES($room_no, $date, '$uname', '$target_uname', $vote_number,
			$vote_times, '$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname);

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
  global $system_time, $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('�跺����ɼ���顼');

  //��ɼ��������
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ���桼���������
  $sql_user = mysql_query("SELECT uname, handle_name, role FROM user_entry WHERE room_no = $room_no
				AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false;  //��������ɼ���Ƥ��ʤ���н��������å�

  $max_voted_number = 0;  //��¿��ɼ��
  $vote_kill_target = ''; //�跺�����ͤΥ桼��̾
  $uname_to_handle_list = array(); //�桼��̾�ȥϥ�ɥ�͡�����б�ɽ
  $uname_to_role_list   = array(); //�桼��̾���򿦤��б�ɽ
  $live_uname_list      = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list    = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list     = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��ϥ�ɥ�͡���)
  $vote_count_list      = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list         = array(); //ǽ�ϼԤ�������ɼ���
  $query = " FROM vote WHERE room_no = $room_no AND date = $date AND situation = '$situation' " .
    "AND vote_times = $vote_times "; //���̥�����

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  while(($array = mysql_fetch_assoc($sql_user)) !== false){ //�桼�� No ��˽���
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    //��ʬ����ɼ�������
    $sql = mysql_query("SELECT SUM(vote_number)" . $query . "AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);
    //�ü쥵���򿦤���ɼ����
    if(strpos($this_role, 'upper_luck') !== false){ //����
      if($date == 2) $this_voted_number += 2;
      else{
	if($this_voted_number > 1) $this_voted_number -= 2;
	else $this_voted_number = 0;
      }
    }
    elseif(strpos($this_role, 'downer_luck') !== false){ //��ȯ��
      if($date == 2){
	if($this_voted_number > 1) $this_voted_number -= 2;
	else $this_voted_number = 0;
      }
      else $this_voted_number += 2;
    }
    elseif(strpos($this_role, 'star') !== false){ //�͵���
      if($this_voted_number > 0) $this_voted_number--;
    }
    elseif(strpos($this_role, 'disfavor') !== false){ //�Կ͵�
      $this_voted_number++;
    }

    //��ʬ����ɼ�������
    $sql =mysql_query("SELECT vote_number" . $query . "AND uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //��ʬ����ɼ�����ͤΥϥ�ɥ�͡�������
    $sql = mysql_query("SELECT user_entry.handle_name AS handle_name FROM user_entry, vote
			WHERE user_entry.room_no = $room_no AND vote.room_no = $room_no
			AND vote.date = $date AND vote.situation = '$situation'
			AND vote_times = $vote_times AND vote.uname = '$this_uname'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //�����ƥ��å������Ѥ����������
    $this_message_list = array('handle_name'  => $this_handle,
			       'target'       => $this_vote_target,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //�ꥹ�Ȥ˥ǡ������ɲ�
    array_push($live_uname_list, $this_uname);
    $uname_to_handle_list[$this_uname] = $this_handle;
    $uname_to_role_list[$this_uname]   = $this_role;
    $vote_message_list[$this_uname]    = $this_message_list;
    $vote_target_list[$this_uname]     = $this_vote_target;
    $vote_count_list[$this_uname]      = $this_voted_number;
    if(strpos($this_role, 'authority') !== false){ //���ϼԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['authority'] = $this_vote_target;
      $ability_list['authority_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'rebel') !== false){ //ȿ�ռԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['rebel'] = $this_vote_target;
      $ability_list['rebel_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'decide') !== false) //����Ԥʤ���ɼ���Ͽ
      $ability_list['decide'] = $this_vote_target;
    elseif(strpos($this_role, 'plague') !== false) //���¿��ʤ���ɼ���Ͽ
      $ability_list['plague'] = $this_vote_target;
    elseif(strpos($this_role, 'good_luck') !== false) //�����ʤ�桼��̾��Ͽ
      $ability_list['good_luck'] = $this_uname;
    elseif(strpos($this_role, 'bad_luck') !== false) //�Ա��ʤ�桼��̾��Ͽ
      $ability_list['bad_luck'] = $this_uname;
  }

  //�ϥ�ɥ�͡��� => �桼��̾ �����������
  $handle_to_uname_list = array_flip($uname_to_handle_list);

  //ȿ�ռԤ�Ƚ��
  if($ability_list['rebel'] == $ability_list['authority']){
    //���ϼԤ�ȿ�ռԤ���ɼ���� 0 �ˤ���
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //��ɼ���ɼ������
    $this_uname = $handle_to_uname_list[$ability_list['rebel']];
    if($vote_message_list[$this_uname]['voted_number'] > 3)
      $vote_message_list[$this_uname]['voted_number'] -= 3;
    else
      $vote_message_list[$this_uname]['voted_number'] = 0;
    $vote_count_list[$this_uname] = $vote_message_list[$this_uname]['voted_number'];
  }

  //��ɼ��̤򥿥ֶ��ڤ���������ƥ����ƥ��å���������Ͽ
  // print_r($vote_message_list); //�ǥХå���
  foreach($live_uname_list as $this_uname){
    $this_array = $vote_message_list[$this_uname];
    $this_handle       = $this_array['handle_name'];
    $this_target       = $this_array['target'];
    $this_voted_number = $this_array['voted_number'];
    $this_vote_number  = $this_array['vote_number'];

    //������ɼ���򹹿�
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //(ï�� [TAB] ï�� [TAB] ��ʬ����ɼ�� [TAB] ��ʬ����ɼ�� [TAB] ��ɼ���)
    $sentence = $this_handle . "\t" . $this_target . "\t" .
      $this_voted_number ."\t" . $this_vote_number . "\t" . (int)$vote_times;
    InsertSystemMessage($sentence, $situation);
  }

  //������ɼ���Υ桼��̾(�跺�����) �Υꥹ�Ȥ����
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);

  if(count($max_voted_uname_list) == 1) //��ͤ����ʤ�跺�Է���
    $vote_kill_target = array_shift($max_voted_uname_list);
  else{ //ʣ��������硢�����򿦤�����å�����
    $decide_uname = $handle_to_uname_list[$ability_list['decide']]; //����Ԥ���ɼ��桼��̾
    $plague_uname = $handle_to_uname_list[$ability_list['plague']]; //���¿�����ɼ��桼��̾
    $good_luck_uname = $ability_list['good_luck']; //�����Υ桼��̾
    $bad_luck_uname  = $ability_list['bad_luck'];  //�Ա��Υ桼��̾

    if(in_array($decide_uname, $max_voted_uname_list)) //����Ԥ���ɼ�褬����н跺�Է���
      $vote_kill_target = $decide_uname;
    elseif(in_array($bad_luck_uname, $max_voted_uname_list)) //�跺�Ը�����Թ�������н跺�Է���
      $vote_kill_target = $bad_luck_uname;
    else{
      //������跺�Ը��䤫�����
      $max_voted_uname_list = array_diff($max_voted_uname_list, array($good_luck_uname));
      if(count($max_voted_uname_list) == 1) //���λ����Ǹ��䤬��ͤʤ�跺�Է���
	$vote_kill_target = array_shift($max_voted_uname_list);
      else{ //���¿�����ɼ���跺�Ը��䤫�����
	$max_voted_uname_list = array_diff($max_voted_uname_list, array($plague_uname));
	if(count($max_voted_uname_list) == 1) //���λ����Ǹ��䤬��ͤʤ�跺�Է���
	  $vote_kill_target = array_shift($max_voted_uname_list);
      }
    }
  }

  if($vote_kill_target != ''){ //�跺�����¹�
    //�桼����������
    $target_handle = $uname_to_handle_list[$vote_kill_target];
    $target_role   = $uname_to_role_list[$vote_kill_target];

    //�跺����
    KillUser($vote_kill_target); //��˴����
    InsertSystemMessage($target_handle, 'VOTE_KILLED'); //�����ƥ��å�����
    SaveLastWords($target_handle); //�跺�Ԥΰ��

    //�跺���줿�ͤ��Ǥ���äƤ������
    if(strpos($target_role, 'poison') !== false &&
       strpos($target_role, 'poison_guard') === false){ //���Τ��оݳ�
      $poison_voter_list = array_keys($vote_target_list, $target_handle); //��ɼ�����ͤ����

      $poison_dead = true; //��ȯư�ե饰������
      foreach($poison_voter_list as $voter_uname){ //���դΥ����å�
	if(strpos($uname_to_role_list[$voter_uname], 'pharmacist') !== false){ //��������
	  InsertSystemMessage($uname_to_handle_list[$voter_uname] . "\t" . $target_handle,
			      'PHARMACIST_SUCCESS');
	  $poison_dead = false;
	}
      }

      if($poison_dead){
	if($GAME_CONF->poison_only_voter) //�Ǥ��оݥ��ץ���������å�
	  $poison_target_list = $poison_voter_list; //��ɼ�Ը���
	else{ //����������
	  //¾�οͤ��������˰������
	  //���͸��ɤ���������ˤ���ȸ��ɤ��������ͤ�ޤ�Ƥ��ޤ��Τ�
	  //����ơָ��ߤ���¸�ԡפ� DB ���䤤��碌��٤�����ʤ����ʡ�
	  $poison_target_list = array_diff($live_uname_list, array($vote_kill_target));
	}
	$rand_key = array_rand($poison_target_list);
	$poison_target_uname  = $poison_target_list[$rand_key];
	$poison_target_handle = $uname_to_handle_list[$poison_target_uname];
	$poison_target_role   = $uname_to_role_list[$poison_target_uname];

	if(strpos($target_role, 'poison_wolf') !== false &&
	   strpos($poison_target_role, 'wolf') !== false){ //��ϵ���Ǥ�ϵ�ˤ�̵��
	  //���ͤ��ǤޤäƤʤ��Τǥ����ƥ��å���������α
	  // InsertSystemMessage($poison_target_handle, 'POISON_WOLF_TARGET');
	  $poison_dead = false;
	}

	if($poison_dead){
	  KillUser($poison_target_uname); //��˴����
	  InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //�����ƥ��å�����
	  SaveLastWords($poison_target_handle); //�������

	  //�ǻष���ͤ����ͤξ��
	  if(strpos($poison_target_role, 'lovers') !== false) LoversFollowed($poison_target_role);
	}
      }
    }

    //�跺���줿�ͤ����ͤξ��
    //�跺�夹�����ɤ�����Τ��ڤ��Ȼפ�����
    //�����Ǥ����ǼԤΥ������å����н������Ť��ΤǤ����ǽ���
    if(strpos($target_role, 'lovers') !== false) LoversFollowed($target_role);

    //��ǽ�Ԥη��(�����ƥ��å�����)
    if(strpos($target_role, 'boss_wolf') !== false)
      $necromancer_result = 'boss_wolf';
    elseif(strpos($target_role, 'wolf') !== false)
      $necromancer_result = 'wolf';
    elseif(strpos($target_role, 'child_fox') !== false)
      $necromancer_result = 'child_fox';
    else
      $necromancer_result = 'human';

    InsertSystemMessage($target_handle . "\t" . $necromancer_result, 'NECROMANCER_RESULT');
  }

  //�ü쥵���򿦤����������
  //��ɼ���оݥϥ�ɥ�͡��� => �Ϳ� �����������
  //echo "sub<br>\n"; print_r($vote_target_list); echo "<br>\n";
  //print_r($uname_to_role_list); echo "<br>\n";
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($uname_to_handle_list as $this_uname => $this_handle){
    $this_role = $uname_to_role_list[$this_uname];
    //echo "$this_uname, $this_handle, $this_role, $voted_target_member_list[$this_handle]<br>\n";
    if($voted_target_member_list[$this_handle] > 0){ //��ɼ����Ƥ����龮���Ԥϥ���å���
      if(strpos($this_role, 'chicken') !== false)
	SuddenDeath($this_uname, $this_handle, $this_role, 'CHICKEN');
    }
    else{ //��ɼ����Ƥ��ʤ��ä��饦�����ϥ���å���
      if(strpos($this_role, 'rabbit') !== false)
	SuddenDeath($this_uname, $this_handle, $this_role, 'RABBIT');
    }
    if(strpos($this_role, 'perverseness') !== false){
      //��ʬ����ɼ���ʣ���οͤ���ɼ���Ƥ�����ŷ�ٵ��ϥ���å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1)
	SuddenDeath($this_uname, $this_handle, $this_role, 'PERVERSENESS');
    }
  }

  if($vote_kill_target != ''){ //����ڤ��ؤ�
    $check_draw = false; //����ʬ��Ƚ��¹ԥե饰�򥪥�
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //��ˤ���
    InsertSystemTalk('NIGHT', ++$system_time, 'night system'); //�뤬��������
    UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
    DeleteVote(); //���ޤǤ���ɼ���������
  }
  else{ //����ɼ����
    $check_draw = true; //����ʬ��Ƚ��¹ԥե饰�򥪥�
    $next_vote_times = $vote_times + 1; //��ɼ��������䤹
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

    //�����ƥ��å�����
    InsertSystemMessage($vote_times, 'RE_VOTE');
    InsertSystemTalk("����ɼ�ˤʤ�ޤ���( $vote_times ����)", ++$system_time);
    UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  }
  mysql_query('COMMIT'); //������ߥå�
  CheckVictory($check_draw);
}

//�����ɼ����
function VoteNight(){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation, $date,
    $user_no, $uname, $handle_name, $role, $target_no;

  switch($situation){
  case 'WOLF_EAT':
    if(strpos($role, 'wolf') === false) OutputVoteResult('�롧��ϵ�ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MAGE_DO':
    if(strpos($role, 'mage') === false) OutputVoteResult('�롧�ꤤ�հʳ�����ɼ�Ǥ��ޤ���');
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯���ꤤ��̵���Ǥ�');
    break;

  case 'GUARD_DO':
    if(strpos($role, 'guard') === false) OutputVoteResult('�롧��Ͱʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'REPORTER_DO':
    if(strpos($role, 'reporter') === false) OutputVoteResult('�롧�֥󲰰ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CUPID_DO':
    if(strpos($role, 'cupid') === false) OutputVoteResult('�롧���塼�ԥåɰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MANIA_DO':
    if(strpos($role, 'mania') === false) OutputVoteResult('�롧���åޥ˥��ʳ�����ɼ�Ǥ��ޤ���');
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯�Υ��ԡ���̵���Ǥ�');
    break;

  case 'POISON_CAT_DO':
    if(strpos($role, 'poison_cat') === false) OutputVoteResult('�롧ǭ���ʳ�����ɼ�Ǥ��ޤ���');
    break;

  default:
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
    break;
  }
  CheckAlreadyVote($situation); //��ɼ�Ѥߥ����å�

 //���顼��å������Υإå�
  $error_header = '�롧��ɼ�褬����������ޤ���<br>';

  if(strpos($role, 'cupid') !== false){  //���塼�ԥåɤξ�����ɼ����
    if(count($target_no) != 2) OutputVoteResult('�롧����Ϳ������ͤǤϤ���ޤ���');
    $self_shoot = false; //��ʬ����ե饰������
    foreach($target_no as $lovers_target_no){
      //��ɼ���Υ桼���������
      $sql = mysql_query("SELECT uname, live FROM user_entry WHERE room_no = $room_no
				AND user_no = $lovers_target_no");
      $array = mysql_fetch_assoc($sql);
      $target_uname = $array['uname'];
      $target_live  = $array['live'];

      //��ԡ������귯�ؤ���ɼ��̵��
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('��ԡ������귯�ؤ���ɼ�Ǥ��ޤ���');

      if($target_uname == $uname) $self_shoot = true; //��ʬ������ɤ��������å�
    }

    //�桼����������
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot)
      OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
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

    if(strpos($role, 'poison_cat') !== false){ //ǭ���ϼ�ʬ�������Ԥؤ���ɼ��̵��
      if($target_name == $uname || $target_live == 'live')
	OutputVoteResult($error_header . '��ʬ�����Ԥˤ���ɼ�Ǥ��ޤ���');
    }
    else{//��ʬ������԰���ϵƱ�Τ���ɼ��̵��
      if($target_uname == $uname || $target_live == 'dead' ||
	 (strpos($role, 'wolf') !== false && strpos($target_role, 'wolf') !== false))
	OutputVoteResult($error_header . '��ʬ����ԡ�ϵƱ�Τؤ���ɼ�Ǥ��ޤ���');
    }

    if($situation == 'WOLF_EAT'){
      //������¼�� GM �ʳ�̵��
      if(strpos($game_option, 'quiz') !== false && $target_uname != 'dummy_boy')
	OutputVoteResult($error_header . '������¼�Ǥ� GM �ʳ�����ɼ�Ǥ��ޤ���');

      //ϵ�ν�������ɼ�Ͽ����귯���Ѥξ��Ͽ����귯�ʳ�̵��
      if(strpos($game_option, 'dummy_boy') !== false && $target_uname != 'dummy_boy' && $date == 1)
	OutputVoteResult($error_header . '�����귯���Ѥξ��ϡ������귯�ʳ�����ɼ�Ǥ��ޤ���');
    }
  }

  LockTable(); //�ơ��֥����¾Ū��å�
  if(strpos($role, 'cupid') !== false){ // ���塼�ԥåɤν���
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
      $target_role .= ' lovers[' . strval($user_no) . ']';
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

  //��ɼ����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number, situation)
			VALUES($room_no, $date, '$uname', '$target_uname_str', 1, '$situation')");
  InsertSystemMessage($handle_name . "\t" . $target_handle_str, $situation);
  InsertSystemTalk($situation . "\t" . $target_handle_str, $system_time, 'night system', '', $uname);

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    CheckVoteNight(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else OutputVoteResult('�ǡ����١������顼', true);
}

//��ν��׽���
function CheckVoteNight(){
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'REPORTER_DO', 'GUARD_DO', 'CUPID_DO', 'MANIA_DO');
  if(! in_array($situation, $situation_list)) OutputVoteResult('�롧��ɼ���顼');

  //���̥�����򥻥å�
  $query_header = "SELECT COUNT(uname) FROM";
  $query_vote   = "$query_header vote WHERE room_no = $room_no AND date = $date AND situation = ";
  $query_role   = "$query_header user_entry WHERE room_no = $room_no " .
    "AND live = 'live' AND user_no > 0 AND role LIKE ";

  $role_count_list = array(); //�� => �Ϳ� �Υꥹ��

  //ϵ����ɼ�����å�
  $sql = mysql_query($query_vote . "'WOLF_EAT'");
  if(mysql_result($sql, 0, 0) < 1) return false; //ϵ�������ǰ��ʬ

  //�ꤤ�դ���ɼ�����å�
  $sql = mysql_query($query_vote . "'MAGE_DO'");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ����ꤤ�դο������
  $sql = mysql_query($query_role . "'%mage%'");
  $role_count_list['mage'] = mysql_result($sql, 0, 0);

  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    //�����������귯���򿦤��ꤤ�դξ��ϥ�����Ȥ��ʤ�
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    if(strpos(mysql_result($sql, 0, 0), 'mage') !== false) $role_count_list['mage']--;
  }
  if($vote_count != (int)$role_count_list['mage']) return false;

  if($date == 1){ //�����Τߥ��塼�ԥåɡ����åޥ˥�����ɼ�����å�
    $sql = mysql_query($query_vote . "'CUPID_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //�����Ƥ��륭�塼�ԥåɤο������
    $sql = mysql_query($query_role . "'cupid%'");
    $role_count_list['cupid'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['cupid']) return false;

    $sql = mysql_query($query_vote . "'MANIA_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    //�����Ƥ�����åޥ˥��ο������
    $sql = mysql_query($query_role . "'mania%'");
    $role_count_list['mania'] = mysql_result($sql, 0, 0);

    //����������ꤤ�դν����ȤޤȤ᤿��
    if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
      //�����������귯����䤬���åޥ˥��ξ��Ͽ�������ʤ�
      $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			  AND uname = 'dummy_boy' AND user_no > 0");
      if(strpos(mysql_result($sql, 0, 0), 'mania') !== false) $role_count_list['mania']--;
    }
    if($vote_count != (int)$role_count_list['mania']) return false;
  }
  else{ //�����ʳ��μ�͡��֥󲰤���ɼ�����å�
    $sql = mysql_query($query_vote . "'GUARD_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'%guard%'");
    $role_count_list['guard'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['guard']) return false;

    $sql = mysql_query($query_vote . "'REPORTER_DO'");
    $vote_count = mysql_result($sql, 0, 0);

    $sql = mysql_query($query_role . "'%reporter%'");
    $role_count_list['reporter'] = mysql_result($sql, 0, 0);
    if($vote_count != (int)$role_count_list['reporter']) return false;
  }

  //��ҷ϶��̥�����
  $query_vote_header = "SELECT vote.target_uname, user_entry.handle_name, user_entry.role " .
    "FROM vote, user_entry WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no " .
    "AND vote.date = $date AND vote.situation = '";
  $query_vote_footer = "' AND vote.uname = user_entry.uname AND user_entry.user_no > 0";

  //��ͤΥϥ�ɥ�͡������ɼ��桼��̾�����
  $sql_guard = mysql_query($query_vote_header . 'GUARD_DO' . $query_vote_footer);

  //ϵ����ɼ��桼��̾�Ȥ����������
  $sql_wolf = mysql_query("SELECT vote.target_uname, user_entry.handle_name, user_entry.role
				FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'WOLF_EAT'
				AND vote.target_uname = user_entry.uname AND user_entry.user_no > 0");
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_handle = $wolf_target_array['handle_name'];
  $wolf_target_role   = $wolf_target_array['role'];
  // $wolf_target_live   = $wolf_target_array['live'];//DB��������Ƥʤ��褦�ʡ�����

  $guarded_uname = ''; //��Ҥ��줿�ͤΥ桼��̾
  while(($array = mysql_fetch_assoc($sql_guard)) !== false){ //�������Ƚ��
    $this_target = $array['target_uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    if($this_target == $wolf_target_uname){ //��������ʤ��å����������
      InsertSystemMessage($this_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');

      //��Ҥ��줿�ͤ��֥󲰤ξ���������å������ϽФ뤬�֥󲰤ϳ��ޤ�� (���Τϸ�Ҳ�ǽ)
      if(strpos($this_role, 'poison_guard') !== false ||
	 strpos($wolf_target_role, 'reporter') === false) $guarded_uname = $this_target;
    }
  }

  if($guarded_uname != '' || strpos($game_option, 'quiz') !== false){ //���Ƚ�꤬��ͥ�褵���
    //������� or ������¼����
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox') === false){ //�����褬�ŸѤξ��ϼ��Ԥ���
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //��Ҥ���Ƥʤ���н�������
    KillUser($wolf_target_uname);
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED');
    SaveLastWords($wolf_target_handle);

    if(strpos($wolf_target_role, 'poison') !== false){ //���٤�줿�ͤ����ǼԤξ��
      if($GAME_CONF->poison_only_eater){ //�����ϵ�����
	$sql_wolf_list = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role
					FROM user_entry, vote WHERE user_entry.room_no = $room_no
					AND user_entry.uname = vote.uname AND vote.date = $date
					AND vote.situation = 'WOLF_EAT' AND user_no > 0");
      }
      else{ //�����Ƥ���ϵ�����
	$sql_wolf_list = mysql_query("SELECT uname, handle_name, role FROM user_entry
					WHERE room_no = $room_no AND role LIKE '%wolf%'
					AND live = 'live' AND user_no > 0");
      }
      $wolf_list = array();
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false) array_push($wolf_list, $wolf);

      $rand_key = array_rand($wolf_list);
      $poison_target_array  = $wolf_list[$rand_key];
      $poison_target_uname  = $poison_target_array['uname'];
      $poison_target_handle = $poison_target_array['handle_name'];
      $poison_target_role   = $poison_target_array['role'];

      KillUser($poison_target_uname);
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night');
      SaveLastWords($poison_target_handle);
      if(strpos($poison_target_role, 'lovers') !== false) //�ǻष��ϵ�����ͤξ��
	LoversFollowed($poison_target_role);
    }
    if(strpos($wolf_target_role, 'lovers') !== false) //���٤�줿�ͤ����ͤξ��
      LoversFollowed($wolf_target_role);
  }

  //����¾��ǽ�ϼԤ���ɼ����
  $query_action_header = "SELECT user_entry.uname, user_entry.handle_name, user_entry.role,
				user_entry.live, vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.uname = user_entry.uname
				AND user_entry.user_no > 0 AND vote.situation = ";

  $sql_mage = mysql_query($query_action_header . "'MAGE_DO'");  //�ꤤ�դξ�������
  while(($array = mysql_fetch_assoc($sql_mage)) !== false){ //�ꤤ�դοͿ�ʬ������
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //ľ���˻��Ǥ�����̵��

    if(strpos($this_role, 'dummy_mage') !== false) //̴���ͤ��ꤤ��̤ϥ�����
      $this_result = (mt_rand(0, 1) == 0 ? 'human' : 'wolf');
    else{ //���줿�ͤξ�������
      $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
				AND uname = '$this_target_uname' AND user_no > 0");
      $array = mysql_fetch_assoc($sql);
      $this_target_handle = $array['handle_name'];
      $this_target_role   = $array['role'];
      $this_target_live   = $array['live'];

      if($this_target_live == 'live' && strpos($this_target_role, 'fox') !== false &&
	 strpos($this_target_role, 'child_fox') === false){ //�ŸѤ����줿���˴
	KillUser($this_target_uname);
	InsertSystemMessage($this_target_handle, 'FOX_DEAD');
	SaveLastWords($this_target_handle);
	if(strpos($this_target_role, 'lovers') !== false) //���줿�Ѥ����ͤξ��
	  LoversFollowed($this_target_role);
      }

      //�ꤤ��̤����
      if(strpos($this_role, 'soul_mage') !== false) //�����ꤤ�դϥᥤ����
	$this_result = GetMainRole($this_target_role);
      else{
	if(strpos($this_target_role, 'boss_wolf') !== false) //��ϵ��¼��Ƚ��
	  $this_result = 'human';
	elseif(strpos($this_target_role, 'wolf') !== false ||
	       strpos($this_target_role, 'suspect') !== false) //����ʳ���ϵ���Կ��ԤϿ�ϵȽ��
	  $this_result = 'wolf';
	else
	  $this_result = 'human';
      }
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  $sql_reporter = mysql_query($query_action_header . "'REPORTER_DO'");  //�֥󲰤ξ�������
  while(($array = mysql_fetch_assoc($sql_reporter)) !== false){ //�֥󲰤οͿ�ʬ������
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //ľ���˻��Ǥ�����̵��

    if($this_target_uname == $wolf_target_uname){ //��������
      if($this_target_uname != $guarded_uname){ //��Ҥ���Ƥ������ϲ���Фʤ�
	//�����ϵ�Υ桼��̾�����
	$sql = mysql_query("SELECT user_entry.handle_name FROM user_entry, vote
				WHERE user_entry.room_no = $room_no
				AND user_entry.uname = vote.uname AND vote.date = $date
				AND vote.situation = 'WOLF_EAT' AND user_no > 0");
	$sentence = $this_handle . "\t" . $wolf_target_handle . "\t" . mysql_result($sql, 0, 0);
	InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
      }
    }
    else{ //���Ԥ����ͤξ�������
      $sql = mysql_query("SELECT role, live FROM user_entry WHERE room_no = $room_no
				AND uname = '$this_target_uname' AND user_no > 0");
      $array = mysql_fetch_assoc($sql);
      $this_target_role = $array['role'];
      $this_target_live = $array['live'];
      if($this_target_live == 'dead') continue; //ľ���˻��Ǥ����鲿�ⵯ���ʤ�

      if(strpos($this_target_role, 'wolf') !== false || strpos($this_target_role, 'fox') !== false){
	KillUser($this_uname); //ϵ���Ѥʤ黦�����
	InsertSystemMessage($this_handle, 'REPORTER_DUTY');
	if(strpos($this_role, 'lovers') !== false) LoversFollowed($this_role); //���͸��ɤ�����
      }
    }
  }

  $sql_mania = mysql_query($query_action_header . "'MANIA_DO'"); //���åޥ˥��ξ�������
  while(($array = mysql_fetch_assoc($sql_mania)) !== false){ //���åޥ˥��οͿ�ʬ������
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];
    $this_live   = $array['live'];
    $this_target_uname = $array['target_uname'];
    if($this_live == 'dead') continue; //ľ���˻��Ǥ�����̵��

    //���åޥ˥��Υ������åȤȤʤä��ͤΥϥ�ɥ�͡�����򿦤����
    $sql = mysql_query("SELECT handle_name, role FROM user_entry WHERE room_no = $room_no
			AND uname = '$this_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $this_target_handle = $array['handle_name'];
    $this_target_role   = $array['role'];

    //���ԡ����� (���åޥ˥�����ꤷ������¼�ͤˤ���)
    if(($this_result = GetMainRole($this_target_role)) == 'mania' ||
       strpos($this_target_role, 'copied') !== false) $this_result = 'human';
    $this_role = str_replace('mania', $this_result, $this_role) . ' copied';
    mysql_query("UPDATE user_entry SET role = '$this_role' WHERE room_no = $room_no
		 AND uname = '$this_uname' AND user_no > 0");

    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MANIA_RESULT');
  }

  //�������ˤ���
  $next_date = $date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");

  //�������ν跺��ɼ�Υ�����Ȥ� 1 �˽����(����ɼ��������)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //�뤬����������
  InsertSystemTalk("MORNING\t" . $next_date, ++$system_time, $location = 'day system', $next_date);
  UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������

  CheckVictory(); //���ԤΥ����å�
  mysql_query('COMMIT'); //������ߥå�
}

//����������ɼ�ڡ�������
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $day_night, $uname, $php_argv;

  //�桼����������
  $sql = mysql_query("SELECT user_entry.uname, user_entry.handle_name,
			user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  $count  = mysql_num_rows($sql);
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  for($i = 0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_file   = $array['icon_filename'];
    $this_color  = $array['color'];

    //5�Ĥ��Ȥ˲���
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";
    $location = $ICON_CONF->path . '/' . $this_file;

    //HTML����
    echo <<<EOF
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

  echo <<<EOF
</tr></table>
<span class="vote-message">* Kick ����ˤ� {$GAME_CONF->kick} �ͤ���ɼ��ɬ�פǤ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE->submit_kick_do}"></form></td>
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="command" value="vote">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$MESSAGE->submit_game_start}"></form>
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
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  if(mysql_result($sql, 0, 0)) OutputVoteResult('�跺����ɼ�Ѥ�');

  //�桼�������ȥ�������Υǡ�������
  $sql_user = mysql_query("SELECT user_entry.user_no, user_entry.uname,
			user_entry.handle_name, user_entry.live,
			user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_no > 0 ORDER BY user_entry.user_no");
  $user_count = mysql_num_rows($sql_user); //�桼����

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  for($i = 0; $i < $user_count; $i++){
    $array = mysql_fetch_assoc($sql_user);
    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];

    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n"; //5�Ĥ��Ȥ˲���
    if($this_live == 'live') //�����Ƥ���Х桼����������
      $path = $ICON_CONF->path . '/' . $this_file;
    else //���Ǥ�л�˴��������
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($this_live == 'live' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>
<td><input type="submit" value="{$MESSAGE->submit_vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $game_option,
    $date, $uname, $role, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ�Ѥߥ����å�
  if($role_wolf = (strpos($role, 'wolf') !== false)){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif($role_mage = (strpos($role, 'mage') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_guard = (strpos($role, 'guard') !== false)){
    if($date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = (strpos($role, 'reporter') !== false)){
    if($date == 1) OutputVoteResult('�롧���������ԤϤǤ��ޤ���');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_cupid = (strpos($role, 'cupid') !== false)){
    if($date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('CUPID_DO');
  }
  elseif($role_mania = (strpos($role, 'mania') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
    if($date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('MANIA_DO');
  }
  else{
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
  }

  //�����귯���� or ������¼�λ��Ͽ����귯�����������٤ʤ�
  if($role_wolf && (strpos($game_option, 'dummy_boy') !== false && $date == 1 ||
		    strpos($game_option, 'quiz') !== false)){
    //�����귯�Υ桼������
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.handle_name,
			user_entry.live, user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no
			AND user_entry.uname = 'dummy_boy' AND user_entry.live = 'live'");
  }
  else{
    $sql = mysql_query("SELECT user_entry.user_no, user_entry.uname, user_entry.handle_name,
			user_entry.live, user_entry.role, user_icon.icon_filename, user_icon.color
			FROM user_entry, user_icon WHERE user_entry.room_no = $room_no
			AND user_entry.icon_no = user_icon.icon_no AND user_entry.user_no > 0
			ORDER BY user_entry.user_no");
  }
  $count = mysql_num_rows($sql);
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $cupid_self_shoot = ($count < $GAME_CONF->cupid_self_shoot);

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  for($i = 0; $i < $count; $i++){
    $array = mysql_fetch_assoc($sql);

    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_role    = $array['role'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];
    $this_wolf    = ($role_wolf && strpos($this_role, 'wolf') !== false);

    //5�Ĥ��Ȥ˲���
    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n";
    if($this_live == 'live'){
      if($this_wolf) //ϵƱ�Τʤ�ϵ��������
	$path = $ICON_CONF->wolf;
      else //�����Ƥ���Х桼����������
	$path = $ICON_CONF->path . '/' . $this_file;
    }
    else{ //���Ǥ�л�˴��������
      $path = $ICON_CONF->dead;
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($role_cupid){
      if($this_uname != 'dummy_boy'){
	$checked = (($cupid_self_shoot && $this_uname == $uname) ? ' checked' : '');
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '"' . $checked . '>'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname && ! $this_wolf){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td><a href="game_up.php?$php_argv#game_top">����� &amp; reload</a></td>

EOF;

  if($role_wolf){
    $type   = 'WOLF_EAT';
    $submit = 'submit_wolf_eat';
  }
  elseif($role_mage){
    $type   = 'MAGE_DO';
    $submit = 'submit_mage_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'submit_guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'submit_reporter_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'submit_cupid_do';
  }
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'submit_mania_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$MESSAGE->$submit}"></td>
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
?>
