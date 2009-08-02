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
$USERS = new Users($room_no); //�桼����������
$user_no     = $USERS->UnameToNumber($uname);
$handle_name = $USERS->GetHandleName($uname);
$role        = $USERS->GetRole($uname);
$live        = $USERS->GetLive($uname);
/*
$sql = mysql_query("SELECT user_no, handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no     = $array['user_no'];
$handle_name = $array['handle_name'];
$role        = $array['role'];
$live        = $array['live'];
*/
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
      EncodePostData(); //�ݥ��Ȥ��줿ʸ��������ƥ��󥳡��ɤ���
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
  global $room_no, $game_option, $situation, $uname;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');
  if(strpos($game_option, 'quiz') === false && $uname == 'dummy_boy'){
    OutputVoteResult('�����ॹ�����ȡ������귯����ɼ���פǤ�');
  }

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT uname FROM vote WHERE room_no = $room_no AND date = 0
			AND uname = '$uname' AND situation = 'GAMESTART'");
  if(mysql_num_rows($sql) != 0) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');

  LockTable(); //�ơ��֥����¾Ū��å�

  //��ɼ����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, situation)
			VALUES($room_no, 0, '$uname', 'GAMESTART')");
  if($sql && mysql_query('COMMIT')){//������ߥå�
    AggregateVoteGameStart(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����೫����ɼ���׽���
function AggregateVoteGameStart(){
  global $GAME_CONF, $MESSAGE, $USERS, $system_time, $room_no, $game_option, $situation;

  if($situation != 'GAMESTART') OutputVoteResult('�����ॹ�����ȡ�̵������ɼ�Ǥ�');

  //��ɼ��������
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = 0 AND situation = '$situation'");
  $vote_count  = mysql_result($sql, 0, 0);

  //�����귯���Ѥʤ�����귯��ʬ��û�
  if(strpos($game_option, 'quiz') === false && strpos($game_option, 'dummy_boy') !== false){
    $vote_count++;
  }

  //�桼����������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- �������롼���� --//
  //�������ꥪ�ץ����ξ�������
  $sql = mysql_query("SELECT option_role FROM room WHERE room_no = $room_no");
  $option_role = mysql_result($sql, 0, 0);

  //����������ѿ��򥻥å�
  $uname_list        = $USERS->names; //�桼��̾ => user_no ������
  $role_list         = GetRoleList($user_count, $option_role); //�򿦥ꥹ�Ȥ����
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

  if(strpos($game_option, 'dummy_boy') !== false){ //�����귯���򿦤����
    #$gerd = true; //�ǥХå���
    if($gerd || $quiz){ //�����귯���򿦸��ꥪ�ץ���������å�
      if($gerd)     $fit_role = 'human'; //����ȷ�
      elseif($quiz) $fit_role = 'quiz';  //������¼

      if(($key = array_search($fit_role, $role_list)) !== false){
	array_push($fix_role_list, $fit_role);
	unset($role_list[$key]);
      }
    }
    else{
      shuffle($role_list); //����򥷥�åե�
      $count = count($role_list);
      for($i = 0; $i < $count; $i++){
	$this_role = array_shift($role_list); //����ꥹ�Ȥ�����Ƭ��ȴ���Ф�
	if(strpos($this_role, 'wolf')   === false &&
	   strpos($this_role, 'fox')    === false &&
	   strpos($this_role, 'poison') === false &&
	   strpos($this_role, 'cupid')  === false){
	  array_push($fix_role_list, $this_role);
	  break;
	}
	array_push($role_list, $this_role); //����ꥹ�Ȥ��������᤹
      }
    }

    if(count($fix_role_list) < 1){ //�����귯����Ϳ�����Ƥ��뤫�����å�
      $sentence = '�����귯����Ϳ�����Ƥ��ޤ���';
      OutputVoteResult($error_header . $sentence . $error_footer, true, true);
    }
    array_push($fix_uname_list, 'dummy_boy'); //����Ѥߥꥹ�Ȥ˿����귯���ɲ�
    unset($uname_list['dummy_boy']); //�����귯����
  }

  //�桼���ꥹ�Ȥ������˼���
  $uname_list = array_keys($uname_list);
  shuffle($uname_list);

  //��˾�򿦤򻲾Ȥ��ư켡�����Ԥ�
  if($wish_role && ! $chaos){ //����˾���ξ�� (����ϴ�˾��̵��)
    foreach($uname_list as $this_uname){
      $this_role = $USERS->GetRole($this_uname); //��˾�򿦤����
      $role_key  = array_search($this_role, $role_list); //��˾�򿦤�¸�ߥ����å�
      if($role_key !== false && mt_rand(1, 100) <= $GAME_CONF->wish_role_rate){ //��˾�̤�
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list, $this_role);
	unset($role_list[$role_key]);
      }
      else{ //��ޤ�ʤ��ä�����̤����ꥹ�ȹԤ�
	array_push($remain_uname_list, $this_uname);
      }
    }
  }
  else{
    shuffle($role_list); //����򥷥�åե�
    $fix_uname_list = array_merge($fix_uname_list, $uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //�Ĥ�����ꥹ�Ȥ�ꥻ�å�
  }

  //�켡����η�̤򸡾�
  $remain_uname_list_count = count($remain_uname_list); //̤����ԤοͿ�
  $role_list_count         = count($role_list); //�Ĥ������
  if($remain_uname_list_count != $role_list_count){
    $uname_str = '����̤����ԤοͿ� (' . $remain_uname_list_count . ') ';
    $role_str  = '�Ĥ�����ο� (' . $role_list_count . ') ';
    $sentence  = $uname_str . '��' . $role_str . '�����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //̤����Ԥ�������
  if($remain_uname_list_count > 0){
    shuffle($role_list); //����򥷥�åե�
    $fix_uname_list = array_merge($fix_uname_list, $remain_uname_list);
    $fix_role_list  = array_merge($fix_role_list, $role_list);
    $role_list = array(); //�Ĥ�����ꥹ�Ȥ�ꥻ�å�
  }

  //������η�̤򸡾�
  $fix_uname_list_count = count($fix_uname_list); //����ԤοͿ�
  if($user_count != $fix_uname_list_count){
    $user_str  = '¼�ͤοͿ� (' . $user_count . ') ';
    $uname_str = '�������ԤοͿ� (' . $fix_uname_list_count . ') ';
    $sentence  = $user_str . '��' . $uname_str . '�����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $fix_role_list_count = count($fix_role_list); //����ο�
  if($fix_uname_list_count != $fix_role_list_count){
    $uname_str = '�������ԤοͿ� (' . $fix_uname_list_count . ') ';
    $role_str  = '����ο� (' . $fix_role_list_count . ') ';
    $sentence  = $uname_str . '��' . $role_str . '�����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  $role_list_count = count($role_list); //�Ĥ������
  if($role_list_count > 0){
    $sentence = '����ꥹ�Ȥ�;�� (' . $role_list_count .') ������ޤ�';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //��Ǥ�Ȥʤ���������
  $rand_keys = array_rand($fix_role_list, $user_count); //�����७�������
  $rand_keys_index = 0;
  $sub_role_count_list = array();

  //�����򿦥ƥ�����
  /*
  $test_role_list = array('blinder', 'earplug');
  for($i = 0; $i < $user_count; $i++){
    $this_test_role = array_shift($test_role_list);
    if($this_test_role == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      array_push($test_role_list, $this_test_role);
      continue;
    }
    $fix_role_list[$i] .= ' ' . $this_test_role;
    $sub_role_count_list[$this_test_role]++;
  }
  */
  /*
  $add_sub_role = 'blinder';
  for($i = 0; $i < $user_count; $i++){
    if(mt_rand(1, 100) <= 70){
      $fix_role_list[$i] .= ' ' . $add_sub_role;
      $sub_role_count_list[$add_sub_role]++;
    }
  }
  */

  $now_sub_role_list = array('decide', 'authority'); //���ץ����ǤĤ��륵���򿦤Υꥹ��
  foreach($now_sub_role_list as $this_role){
    if(strpos($option_role, $this_role) !== false && $user_count >= $GAME_CONF->$this_role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //ϵ��ǯ¼
    $this_role = 'liar';
    for($i = 0; $i < $user_count; $i++){ //�����˰����Ψ��ϵ��ǯ��Ĥ���
      if(mt_rand(1, 100) <= 70){
	$fix_role_list[$i] .= ' ' . $this_role;
	$sub_role_count_list[$this_role]++;
      }
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //�»Ρ��ʽ�¼
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    for($i = 0; $i < $user_count; $i++){ //���������̤˱����ƿ»Τ��ʽ���Ĥ���
      $this_uname = $fix_uname_list[$i];
      $this_role  = $sub_role_list[$USERS->GetSex($this_uname)];
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if(strpos($option_role, 'sudden_death') !== false){ //�����μ�¼
    $sub_role_list = array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience');
    for($i = 0; $i < $user_count; $i++){ //�����˥���å���Ϥ򲿤��Ĥ���
      $rand_key = array_rand($sub_role_list);
      $this_role = $sub_role_list[$rand_key];
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
      if($this_role == 'impatience'){ //û���ϰ�ͤ���
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  if($quiz){ //������¼
    $this_role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //����԰ʳ��˲����Ԥ�Ĥ���
      if($fix_uname_list[$i] == 'dummy_boy') continue;
      $fix_role_list[$i] .= ' ' . $this_role;
      $sub_role_count_list[$this_role]++;
    }
  }
  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //������ʥ����򿦤Υ����ɥꥹ�Ȥ����
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    #$sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //�ǥХå���
    $delete_role_list = array('lovers', 'copied', 'panelist'); //��꿶���оݳ��򿦤Υꥹ��
    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //���Ѳ����򥹥��å�
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
    UpdateRole($entry_uname, $entry_role);
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
    $vote_count = AggregateVoteKick($target); //���׽���
    OutputVoteResult('��ɼ��λ��' . $target . '��' . $vote_count . '���� (Kick ����ˤ� ' .
		     $GAME_CONF->kick . ' �Ͱʾ����ɼ��ɬ�פǤ�)', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//Kick ��ɼ�ν��׽��� ($target : �о� HN, �֤��� : �о� HN ����ɼ��׿�)
function AggregateVoteKick($target){
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
  global $USERS, $system_time, $room_no, $situation, $date, $vote_times,
    $uname, $handle_name, $role, $target_no;

  if($situation != 'VOTE_KILL') OutputVoteResult('�跺����ɼ���顼');

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND uname = '$uname' AND situation = '$situation' AND vote_times = $vote_times");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  //��ɼ���Υ桼���������
  $target_uname  = $USERS->NumberToUname($target_no);
  $target_handle = $USERS->GetHandleName($target_uname);
  $target_live   = $USERS->GetLive($target_uname);

  //��ʬ������԰�����꤬��ʤ�����̵��
  if($target_live == 'dead' || $target_uname == $uname || $target_uname == ''){
    OutputVoteResult('�跺����ɼ�褬����������ޤ���');
  }
  LockTable(); //�ơ��֥����¾Ū��å�

  //-- ��ɼ���� --//
  //�򿦤˱�����ɼ�������
  $vote_number = 1;
  if(strpos($role, 'authority') !== false){
    $vote_number++; //���ϼ�
  }
  elseif(strpos($role, 'watcher') !== false || strpos($role, 'panelist') !== false){
    $vote_number = 0; //˵�Ѽԡ�������
  }
  elseif(strpos($role, 'random_voter') !== false){
    $vote_number = mt_rand(0, 2); //��ʬ��
  }

  //��ɼ�������ƥ��å�����
  $sql = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number,
			vote_times, situation)
			VALUES($room_no, $date, '$uname', '$target_uname', $vote_number,
			$vote_times, '$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname);

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    AggregateVoteDay(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����ɼ���׽���
function AggregateVoteDay(){
  global $GAME_CONF, $USERS, $system_time, $room_no, $situation, $vote_times, $date;

  if($situation != 'VOTE_KILL') OutputVoteResult('�跺����ɼ���顼');

  //��ɼ��������
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ���桼���������
  $sql_user = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
				AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);
  if($vote_count != $user_count) return false; //��������ɼ���Ƥ��ʤ���н��������å�

  $max_voted_number = 0;  //��¿��ɼ��
  $vote_kill_target = ''; //�跺�����ͤΥ桼��̾
  $live_uname_list   = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list  = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��桼��̾)
  $vote_count_list   = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list      = array(); //ǽ�ϼԤ�������ɼ���
  $dead_lovers_list  = array(); //���ɤ��������ͤΥꥹ��
  $query = " FROM vote WHERE room_no = $room_no AND date = $date AND situation = '$situation' " .
    "AND vote_times = $vote_times "; //���̥�����

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  for($i = 0; $i < $user_count; $i++){ //�桼�� No ��˽���
    $this_uname = mysql_result($sql_user, $i, 0);
    $this_role  = $USERS->GetRole($this_uname);

    //��ʬ����ɼ�������
    $sql = mysql_query("SELECT SUM(vote_number)" . $query . "AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);
    //�ü쥵���򿦤���ɼ����
    if(strpos($this_role, 'upper_luck') !== false) //����
      $this_voted_number += ($date == 2 ? 4 : -2);
    elseif(strpos($this_role, 'downer_luck') !== false) //��ȯ��
      $this_voted_number += ($date == 2 ? -4 : 2);
    elseif(strpos($this_role, 'random_luck') !== false) //��������
      $this_voted_number += (mt_rand(1, 5) - 3);
    elseif(strpos($this_role, 'star') !== false) //�͵���
      $this_voted_number--;
    elseif(strpos($this_role, 'disfavor') !== false) //�Կ͵�
      $this_voted_number++;
    if($this_voted_number < 0) $this_voted_number = 0; //�ޥ��ʥ��ˤʤäƤ����� 0 �ˤ���

    //��ʬ����ɼ��ξ�������
    $sql =mysql_query("SELECT target_uname, vote_number" . $query . "AND uname = '$this_uname'");
    $array = mysql_fetch_assoc($sql);
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_vote_number   = (int)$array['vote_number'];

    //�����ƥ��å������Ѥ����������
    $this_message_list = array('target'       => $this_target_handle,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //�ꥹ�Ȥ˥ǡ������ɲ�
    array_push($live_uname_list, $this_uname);
    $vote_message_list[$this_uname] = $this_message_list;
    $vote_target_list[$this_uname]  = $this_target_uname;
    $vote_count_list[$this_uname]   = $this_voted_number;
    if(strpos($this_role, 'authority') !== false){ //���ϼԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['authority'] = $this_target_uname;
      $ability_list['authority_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'rebel') !== false){ //ȿ�ռԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['rebel'] = $this_target_uname;
      $ability_list['rebel_uname'] = $this_uname;
    }
    elseif(strpos($this_role, 'decide') !== false) //����Ԥʤ���ɼ���Ͽ
      $ability_list['decide'] = $this_target_uname;
    elseif(strpos($this_role, 'plague') !== false) //���¿��ʤ���ɼ���Ͽ
      $ability_list['plague'] = $this_target_uname;
    elseif(strpos($this_role, 'impatience') !== false) //û���ʤ���ɼ���Ͽ
      $ability_list['impatience'] = $this_target_uname;
    elseif(strpos($this_role, 'good_luck') !== false) //�����ʤ�桼��̾��Ͽ
      $ability_list['good_luck'] = $this_uname;
    elseif(strpos($this_role, 'bad_luck') !== false) //�Ա��ʤ�桼��̾��Ͽ
      $ability_list['bad_luck'] = $this_uname;
  }

  //ȿ�ռԤ�Ƚ��
  if($ability_list['rebel'] == $ability_list['authority']){
    //���ϼԤ�ȿ�ռԤ���ɼ���� 0 �ˤ���
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //��ɼ���ɼ������
    $this_uname = $ability_list['rebel'];
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
    $this_handle       = $USERS->GetHandleName($this_uname);
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
  elseif(in_array($ability_list['decide'], $max_voted_uname_list)) //�����
    $vote_kill_target = $ability_list['decide'];
  elseif(in_array($ability_list['bad_luck'], $max_voted_uname_list)) //�Թ�
    $vote_kill_target = $ability_list['bad_luck'];
  elseif(in_array($ability_list['impatience'], $max_voted_uname_list)) //û��
    $vote_kill_target = $ability_list['impatience'];
  else{
    //������跺�Ը��䤫�����
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['good_luck']));
    if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
      $vote_kill_target = array_shift($max_voted_uname_list);
    }
    else{ //���¿�����ɼ���跺�Ը��䤫�����
      $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['plague']));
      if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
	$vote_kill_target = array_shift($max_voted_uname_list);
      }
    }
  }

  if($vote_kill_target != ''){ //�跺�����¹�
    //�桼����������
    $target_handle = $USERS->GetHandleName($vote_kill_target);
    $target_role   = $USERS->GetRole($vote_kill_target);

    //�跺����
    KillUser($vote_kill_target); //��˴����
    InsertSystemMessage($target_handle, 'VOTE_KILLED'); //�����ƥ��å�����
    SaveLastWords($target_handle); //�跺�Ԥΰ��
    if(strpos($target_role, 'lovers') !== false){ //�跺���줿�ͤ����ͤξ��
      array_push($dead_lovers_list, $target_role);
    }

    //�跺�Ԥ���¸�ԥꥹ�Ȥ������
    $live_uname_list = array_diff($live_uname_list, array($vote_kill_target));

    //�跺���줿�ͤ��Ǥ���äƤ������
    $poison_dead = true; //��ȯư�ե饰������
    $pharmacist_success = false; //���������ե饰������
    do{
      if(strpos($target_role, 'poison') === false) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if(strpos($target_role, 'poison_guard') !== false) break;//���Τ��оݳ�
      if(strpos($target_role, 'dummy_poison') !== false) break;//̴�ǼԤ��оݳ�
      if(strpos($target_role, 'incubate_poison') !== false && $date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      $poison_voter_list = array_keys($vote_target_list, $vote_kill_target); //��ɼ�����ͤ����
      foreach($poison_voter_list as $voter_uname){ //���դΥ����å�
	if(strpos($USERS->GetRole($voter_uname), 'pharmacist') === false) continue;

	//��������
	$sentence = $USERS->GetHandleName($voter_uname) . "\t" . $target_handle;
	InsertSystemMessage($sentence, 'PHARMACIST_SUCCESS');
	$pharmacist_success = true;
      }
      if($pharmacist_success) break;

      //�Ǥ��оݥ��ץ���������å����Ƹ���ԥꥹ�Ȥ����
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $poison_voter_list : $live_uname_list);
      if(strpos($target_role, 'strong_poison') !== false){ //���ǼԤʤ饿�����åȤ���¼�ͤ����
	$strong_poison_target_list = array();
	foreach($poison_target_list as $this_uname){
	  $this_role = $USERS->GetRole($this_uname);
	  if(strpos($this_role, 'wolf') !== false || strpos($this_role, 'fox') !== false){
	    array_push($strong_poison_target_list, $this_uname);
	  }
	}
	$poison_target_list = $strong_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      //�оݼԤ����
      $rand_key = array_rand($poison_target_list);
      $poison_target_uname  = $poison_target_list[$rand_key];
      $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
      $poison_target_role   = $USERS->GetRole($poison_target_uname);

      //��ȯȽ��
      if(strpos($target_role, 'poison_wolf') !== false &&
	 strpos($poison_target_role, 'wolf') !== false){ //��ϵ���Ǥ�ϵ�ˤ�̵��
	//���ͤ��ǤޤäƤʤ��Τǥ����ƥ��å���������α
	// InsertSystemMessage($poison_target_handle, 'POISON_WOLF_TARGET');
	break;
      }
      if(strpos($target_role, 'poison_fox') !== false &&
	 strpos($poison_target_role, 'fox') !== false){ //�ɸѤ��ǤϸѤˤ�̵��
	break;
      }
      if(strpos($poison_target_role, 'resist_wolf') !== false &&
	 strpos($poison_target_role, 'lost_ability') === false){ //ǽ�Ϥ���ä�����ϵ
	UpdateRole($poison_target_uname, $poison_target_role . ' lost_ability');
	break;
      }

      KillUser($poison_target_uname); //��˴����
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //�����ƥ��å�����
      SaveLastWords($poison_target_handle); //�������
      if(strpos($poison_target_role, 'lovers') !== false){ //�ǻष���ͤ����ͤξ��
	array_push($dead_lovers_list, $poison_target_role);
      }
      $poison_dead = false; //̵�¥롼�ץХ��к�
      break;
    }while($poison_dead);

    //��ǽ�Ϥ�Ƚ����
    $sentence = $target_handle . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽ�Ԥ�Ƚ����
    $necromancer_result = 'human';
    $necromancer_result_list = array('child_fox', 'white_fox', 'boss_wolf', 'wolf');
    foreach($necromancer_result_list as $this_role){
      if(strpos($target_role, $this_role) !== false){
	$necromancer_result = $this_role;
	break;
      }
    }
    InsertSystemMessage($sentence . $necromancer_result, $action);

    //��������Ƚ����
    InsertSystemMessage($sentence . GetMainRole($target_role), 'SOUL_' . $action);

    //̴��ͤ�Ƚ����
    array_push($necromancer_result_list, 'human');
    $rand_key = array_rand($necromancer_result_list);
    InsertSystemMessage($sentence . $necromancer_result_list[$rand_key], 'DUMMY_' . $action);
  }

  //�ü쥵���򿦤����������
  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($live_uname_list as $this_uname){
    $this_role = $USERS->GetRole($this_uname);
    $this_type = '';

    if(strpos($this_role, 'chicken') !== false){ //�����Ԥ���ɼ����Ƥ����饷��å���
      if($voted_target_member_list[$this_uname] > 0) $this_type = 'CHICKEN';
    }
    elseif(strpos($this_role, 'rabbit') !== false){ //����������ɼ����Ƥ��ʤ��ä��饷��å���
      if($voted_target_member_list[$this_uname] == 0) $this_type = 'RABBIT';
    }
    elseif(strpos($this_role, 'perverseness') !== false){
      //ŷ�ٵ��ϼ�ʬ����ɼ���ʣ���οͤ���ɼ���Ƥ����饷��å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $this_type = 'PERVERSENESS';
    }
    elseif(strpos($this_role, 'flattery') !== false){
      //���ޤ���ϼ�ʬ����ɼ���¾�οͤ���ɼ���Ƥ��ʤ���Х���å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $this_type = 'FLATTERY';
    }
    elseif(strpos($this_role, 'impatience') !== false){
      if($vote_kill_target == '') $this_type = 'IMPATIENCE'; //û���Ϻ���ɼ�ʤ饷��å���
    }
    elseif(strpos($this_role, 'panelist') !== false){ //�����ԤϽ���Ԥ���ɼ�����饷��å���
      if($vote_target_list[$this_uname] == 'dummy_boy') $this_type = 'PANELIST';
    }

    if($this_type == '') continue;
    SuddenDeath($this_uname, $this_type);
    if(strpos($this_role, 'lovers') !== false) array_push($dead_lovers_list, $this_role);
  }
  foreach($dead_lovers_list as $this_role) LoversFollowed($this_role); //���͸��ɤ�����

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
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation, $date,
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

  case 'CHILD_FOX_DO':
    if(strpos($role, 'child_fox') === false) OutputVoteResult('�롧�ҸѰʳ�����ɼ�Ǥ��ޤ���');
    // if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯���ꤤ��̵���Ǥ�');
    break;

  case 'MANIA_DO':
    if(strpos($role, 'mania') === false) OutputVoteResult('�롧���åޥ˥��ʳ�����ɼ�Ǥ��ޤ���');
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯�Υ��ԡ���̵���Ǥ�');
    break;

  case 'POISON_CAT_DO':
    if(strpos($role, 'poison_cat') === false) OutputVoteResult('�롧ǭ���ʳ�����ɼ�Ǥ��ޤ���');
    if(strpos($game_option, 'not_open_cast') === false)
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
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
      $target_uname = $USERS->NumberToUname($lovers_target_no);
      $target_live  = $USERS->GetLive($target_uname);

      //��ԡ������귯�ؤ���ɼ��̵��
      if($target_live == 'dead' || $target_uname == 'dummy_boy')
	OutputVoteResult('��ԡ������귯�ؤ���ɼ�Ǥ��ޤ���');

      if($target_uname == $uname) $self_shoot = true; //��ʬ������ɤ��������å�
    }

    //�桼����������
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
    }
  }
  else{ //���塼�ԥåɰʳ�����ɼ����
    //��ɼ���Υ桼���������
    $target_uname  = $USERS->NumberToUname($target_no);
    $target_handle = $USERS->GetHandleName($target_uname);
    $target_role   = $USERS->GetRole($target_uname);
    $target_live   = $USERS->GetLive($target_uname);

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
      $target_uname  = $USERS->NumberToUname($lovers_target_no);
      $target_handle = $USERS->GetHandleName($target_uname);
      $target_role   = $USERS->GetRole($target_uname);
      $target_uname_str  .= $target_uname  . ' ';
      $target_handle_str .= $target_handle . ' ';

      //�򿦤����ͤ��ɲ�
      UpdateRole($target_uname, $target_role . ' lovers[' . strval($user_no) . ']');
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
    AggregateVoteNight(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else OutputVoteResult('�ǡ����١������顼', true);
}

//����򿦤���ɼ����������å�������ɼ��̤��֤�
function CheckVoteNight($action, $role, $dummy_boy_role = ''){
  global $room_no, $game_option, $date;

  //��ɼ��������
  $sql_vote = mysql_query("SELECT uname, target_uname FROM vote WHERE room_no = $room_no
				AND  date = $date AND situation = '$action'");
  $vote_count = mysql_num_rows($sql_vote); //��ɼ�Ϳ������

  //ϵ�γ��ߤϰ�ͤ� OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $sql_vote : false);

  //�����Ƥ����о��򿦤οͿ��򥫥����
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND role LIKE '{$role}%' AND live = 'live' AND user_no > 0");
  $role_count = mysql_result($sql, 0, 0);

  //�����������귯��������򿦤��ä����ϥ�����Ȥ��ʤ�
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $sql_vote : false);
}

//��ν��׽���
function AggregateVoteNight(){
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'GUARD_DO', 'REPORTER_DO', 'CUPID_DO',
			  'CHILD_FOX_DO', 'MANIA_DO', 'POISON_CAT_DO');
  if(! in_array($situation, $situation_list)) OutputVoteResult('�롧��ɼ���顼');

  //ϵ����ɼ�����å�
  if(($sql_wolf = CheckVoteNight('WOLF_EAT', '%wolf')) === false) return false;

  //�����������귯��������򿦤��ä����ϥ�����Ȥ��ʤ�
  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    $this_dummy_boy_role = $USERS->GetRole('dummy_boy');
    $exclude_role_list   = array('mage', 'mania'); //��������оݳ��򿦥ꥹ��

    foreach($exclude_role_list as $this_role){
      if(strpos($this_dummy_boy_role, $this_role) !== false){
	$dummy_boy_role = $this_role;
	break;
      }
    }
  }

  //�����ɼ�Ǥ����򿦤���ɼ�����å�
  if(($sql_mage = CheckVoteNight('MAGE_DO', '%mage', $dummy_boy_role)) === false) return false;
  if(($sql_child_fox = CheckVoteNight('CHILD_FOX_DO', 'child_fox')) === false) return false;

  if($date == 1){ //�����Τ���ɼ�Ǥ����򿦤�����å�
    if(($sql_cupid = CheckVoteNight('CUPID_DO', 'cupid')) === false) return false;
    if(($sql_mania = CheckVoteNight('MANIA_DO', 'mania', $dummy_boy_role)) === false) return false;
  }
  else{ //�����ܰʹ���ɼ�Ǥ����򿦤�����å�
    if(($sql_guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;
    if(($sql_reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;
    if(($sql_poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat')) === false) return false;
  }

  //ϵ����ɼ��桼��̾�Ȥ����������
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $voted_wolf_uname   = $wolf_target_array['uname'];
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_handle = $USERS->GetHandleName($wolf_target_uname);
  $wolf_target_role   = $USERS->GetRole($wolf_target_uname);

  $guarded_uname = ''; //��Ҥ��줿�ͤΥ桼��̾
  $dead_uname_list  = array(); //��˴�ԥꥹ��
  $dead_lovers_list = array(); //���͸��ɤ��оݼԥꥹ��
  $hunted_fox_list  = array(); //���줿�ѤΥꥹ��

  //��ͤθ������Ƚ��
  while($sql_guard != '' && ($array = mysql_fetch_assoc($sql_guard)) !== false){
    $this_uname        = $array['uname'];
    $this_target_uname = $array['target_uname'];
    $this_handle       = $USERS->GetHandleName($this_uname);
    $this_role         = $USERS->GetRole($this_uname);
    $this_target_role  = $USERS->GetRole($this_target_uname);

    if(strpos($this_role, 'dummy_guard') !== false){ //̴��ͤ�ɬ��������å������������Ф�
      $sentence = $this_handle . "\t" . $USERS->GetHandleName($this_target_uname);
      InsertSystemMessage($sentence, 'GUARD_SUCCESS');
      continue;
    }

    if(strpos($this_target_role, 'cursed_fox') !== false){ //ŷ�Ѹ�Ҥʤ���
      array_push($hunted_fox_list, $this_target_uname);
      $sentence = $this_handle . "\t" . $USERS->GetHandleName($this_target_uname);
      InsertSystemMessage($sentence, 'GUARD_HUNTED');
    }

    if($this_target_uname != $wolf_target_uname) continue; //��������ʤ��å����������
    InsertSystemMessage($this_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');
    if(strpos($this_role, 'dummy_guard') !== false) continue;

    //��Ҥ��줿�ͤ��֥󲰤ξ���������å������ϽФ뤬�֥󲰤ϳ��ޤ�� (���Τϸ�Ҳ�ǽ)
    if(strpos($this_role, 'poison_guard') !== false ||
       strpos($wolf_target_role, 'reporter') === false){
      $guarded_uname = $this_target_uname;
    }
  }

  if($guarded_uname != '' || strpos($game_option, 'quiz') !== false){ //���Ƚ�꤬��ͥ�褵���
    //������� or ������¼����
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox')  === false &&
	 strpos($wolf_target_role, 'poison_fox') === false &&
	 strpos($wolf_target_role, 'white_fox')  === false){ //�����褬�ŸѤξ��ϼ��Ԥ���
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //��Ҥ���Ƥʤ���н�������
    KillUser($wolf_target_uname);
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED');
    SaveLastWords($wolf_target_handle);
    if(strpos($wolf_target_role, 'lovers') !== false){ //���٤�줿�ͤ����ͤξ��
      array_push($dead_lovers_list, $wolf_target_role);
    }
    array_push($dead_uname_list, $wolf_target_uname);

    //�����ϵ�����
    $voted_wolf_handle = $USERS->GetHandleName($voted_wolf_uname);
    $voted_wolf_role   = $USERS->GetRole($voted_wolf_uname);

    if(strpos($voted_wolf_role, 'tongue_wolf') !== false &&
       strpos($voted_wolf_role, 'lost_ability') === false){ //ǽ�Ϥ���ä����ϵ
      $wolf_target_main_role = GetMainRole($wolf_target_role);
      $sentence = $voted_wolf_handle . "\t" . $wolf_target_handle . "\t" . $wolf_target_main_role;
      InsertSystemMessage($sentence, 'TONGUE_WOLF_RESULT');

      if($wolf_target_main_role == 'human'){ //¼�ͤʤ�ǽ�ϼ���
	UpdateRole($voted_wolf_uname, $voted_wolf_role . ' lost_ability');
      }
    }

    //���٤�줿�ͤ��ǻ������ä����
    $poison_dead = true; //��ȯư�ե饰������
    do{
      if(strpos($wolf_target_role, 'poison') === false) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if(strpos($wolf_target_role, 'dummy_poison') !== false) break;//̴�ǼԤ��оݳ�
      if(strpos($wolf_target_role, 'incubate_poison') !== false && $date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      $wolf_list = array();
      if($GAME_CONF->poison_only_eater){ //�����ϵ�����
	array_push($wolf_list, $voted_wolf_uname);
      }
      else{ //�����Ƥ���ϵ�����
	$sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
				AND role LIKE '%wolf%' AND live = 'live' AND user_no > 0");
	$count = mysql_num_rows($sql);
	for($i = 0; $i < $count; $i++) array_push($wolf_list, mysql_result($sql, $i, 0));
      }
      $rand_key = array_rand($wolf_list);
      $poison_target_uname  = $wolf_list[$rand_key];
      $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
      $poison_target_role   = $USERS->GetRole($poison_target_uname);

      if(strpos($poison_target_role, 'resist_wolf') !== false &&
	 strpos($poison_target_role, 'lost_ability') === false){ //ǽ�Ϥ���ä�����ϵ
	UpdateRole($poison_target_uname, $poison_target_role . ' lost_ability');
	break;
      }

      KillUser($poison_target_uname);
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night');
      SaveLastWords($poison_target_handle);
      if(strpos($poison_target_role, 'lovers') !== false){ //�ǻष��ϵ�����ͤξ��
	array_push($dead_lovers_list, $poison_target_role);
      }
      array_push($dead_uname_list, $poison_target_uname);
      $poison_dead = false; //̵�¥롼�ץХ��к�
      break;
    }while($poison_dead);
  }

  //����¾��ǽ�ϼԤ���ɼ����
  /*
    ��ϵ���ꤤ�ա��֥󲰤ʤɡ���ư��̤ǻ�Ԥ��Ф륿���פ�Ƚ�������

    ������1) �ɤ����Ƚ�����˹Ԥ������ŸѤ����ब��ޤ� (����Ū�ˤϿ�ϵ�ν����ͥ�褹��)
    ��ϵ   �� �ꤤ��
    �ꤤ�� �� �Ÿ�

    ������2) �ɤ����Ƚ�����˹Ԥ����ǥ֥󲰤����ब��ޤ� (���ߤ��ꤤ�դ���)
    �ꤤ�� �� �Ÿ�
    �֥� �� �Ÿ�
  */

  //ǭ���ν���
  while($sql_poison_cat != '' && ($array = mysql_fetch_assoc($sql_poison_cat)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    //�����оݼԤξ�������
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    //��������
    /*
      ���ͤ��������Ƥ��ޤä����Ϥɤ����롩
      �� �����ԥꥹ�Ȥ���ݤ��Ƥ����ơ����åץ뤬ξ���������Ƥ��������衩
      �� ���ͤ��ŤʤäƤ������ϰ�ͤǤ���Ǥ�����Ƥ�Ϣ���������뤳�Ȥ�
      �� �����դλ��ͤ���ޤ�ޤ�ǭ����ɽ�˽Ф��ʤ��褦�ˤ��뤳��
     */
    // if(mt_rand(1, 100) <= 100){ //�ƥ�����
    $this_rand = mt_rand(1, 100); //����Ƚ�������
    if($this_rand <= 20){
      $this_result = 'success';
      mysql_query("UPDATE user_entry SET live = 'live' WHERE room_no = $room_no
			AND uname = '$this_target_uname' AND user_no > 0");
    }
    else{
      $this_result = 'failed';
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
  }

  //�ꤤ�դν���
  while(($array = mysql_fetch_assoc($sql_mage)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    //�оݼԤξ�������
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);
    $this_target_live   = $USERS->GetLive($this_target_uname);

    if(strpos($this_role, 'dummy_mage') !== false){ //̴���ͤ��ꤤ��̤ϥ�����
      $this_result = (mt_rand(0, 1) == 0 ? 'human' : 'wolf');
    }
    else{
      if(strpos($this_target_role, 'cursed') !== false){ //�����Ƥ����򿦤���ä����˴����
	KillUser($this_uname);
	InsertSystemMessage($this_handle, 'CURSED');
	SaveLastWords($this_handle);
	if(strpos($this_role, 'lovers') !== false){ //���������줿�ꤤ�դ����ͤξ��
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
	continue;
      }

      if(strpos($this_role, 'soul_mage') !== false){ //�����ꤤ�դ��ꤤ��̤ϥᥤ����
	$this_result = GetMainRole($this_target_role);
      }
      else{
	if($this_target_live == 'live' && strpos($this_target_role, 'fox') !== false &&
	   strpos($this_target_role, 'child_fox') === false &&
	   strpos($this_target_role, 'white_fox') === false){//�ŸѤ����줿���˴
	  KillUser($this_target_uname);
	  InsertSystemMessage($this_target_handle, 'FOX_DEAD');
	  SaveLastWords($this_target_handle);
	  if(strpos($this_target_role, 'lovers') !== false){ //���줿�Ѥ����ͤξ��
	    array_push($dead_lovers_list, $this_target_role);
	  }
	  array_push($dead_uname_list, $this_target_uname);
	}

	//�ꤤ��̤����
	if(strpos($this_target_role, 'boss_wolf') !== false){ //��ϵ��¼��Ƚ��
	  $this_result = 'human';
	}
	elseif(strpos($this_target_role, 'wolf') !== false ||
	       strpos($this_target_role, 'suspect') !== false){ //����ʳ���ϵ���Կ��ԤϿ�ϵȽ��
	  $this_result = 'wolf';
	}
	else{
	  $this_result = 'human';
	}
      }
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  //�ҸѤν���
  while(($array = mysql_fetch_assoc($sql_child_fox)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    //�оݼԤξ�������
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    if(strpos($this_target_role, 'cursed') !== false){ //�����Ƥ����򿦤���ä����˴����
      KillUser($this_uname);
      InsertSystemMessage($this_handle, 'CURSED');
      SaveLastWords($this_handle);
      if(strpos($this_role, 'lovers') !== false){ //���������줿�ҸѤ����ͤξ��
	array_push($dead_lovers_list, $this_role);
      }
      array_push($dead_uname_list, $this_uname);
      continue;
    }

    //�ꤤ��̤����
    if(mt_rand(1, 100) <= 30){ //�����Ψ�Ǽ��Ԥ���
      $this_result = 'failed';
    }
    elseif(strpos($this_target_role, 'boss_wolf') !== false){ //��ϵ��¼��Ƚ��
      $this_result = 'human';
    }
    elseif(strpos($this_target_role, 'wolf') !== false ||
	   strpos($this_target_role, 'suspect') !== false){ //����ʳ���ϵ���Կ��ԤϿ�ϵȽ��
      $this_result = 'wolf';
    }
    else{
      $this_result = 'human';
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  foreach($hunted_fox_list as $this_uname){ //���줿ŷ�Ѥλ�˴����
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);

    KillUser($this_uname);
    InsertSystemMessage($this_handle, 'HUNTED_FOX');
    if(strpos($this_role, 'lovers') !== false){ //���͸��ɤ�����
      array_push($dead_lovers_list, $this_role);
    }
    array_push($dead_uname_list, $this_uname);
  }

  //�֥󲰤ν���
  while($sql_reporter != '' && ($array = mysql_fetch_assoc($sql_reporter)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    //������ξ�������
    $this_target_uname = $array['target_uname'];
    if($this_target_uname == $wolf_target_uname){ //��������
      if($this_target_uname == $guarded_uname) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
      $voted_wolf_handle = $USERS->GetHandleName($voted_wolf_uname);
      $sentence = $this_handle . "\t" . $wolf_target_handle . "\t" . $voted_wolf_handle;
      InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
    }
    elseif(in_array($this_target_uname, $dead_uname_list)){
      continue; //�����оݤ�ľ���˻��Ǥ����鲿�ⵯ���ʤ�
    }
    else{ //���Ԥ����ͤξ�������
      $this_target_role = $USERS->GetRole($this_target_uname);
      if(strpos($this_target_role, 'wolf') !== false || strpos($this_target_role, 'fox') !== false){
	KillUser($this_uname); //ϵ���Ѥʤ黦�����
	InsertSystemMessage($this_handle, 'REPORTER_DUTY');
	if(strpos($this_role, 'lovers') !== false){ //���͸��ɤ�����
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
      }
    }
  }

  //���åޥ˥��ν���
  while($sql_mania != '' && ($array = mysql_fetch_assoc($sql_mania)) !== false){
    $this_uname  = $array['uname'];
    $this_handle = $USERS->GetHandleName($this_uname);
    $this_role   = $USERS->GetRole($this_uname);
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    //���åޥ˥��Υ������åȤȤʤä��ͤΥϥ�ɥ�͡�����򿦤����
    $this_target_uname  = $array['target_uname'];
    $this_target_handle = $USERS->GetHandleName($this_target_uname);
    $this_target_role   = $USERS->GetRole($this_target_uname);

    //���ԡ����� (���åޥ˥�����ꤷ������¼�ͤˤ���)
    if(($this_result = GetMainRole($this_target_role)) == 'mania' ||
       strpos($this_target_role, 'copied') !== false) $this_result = 'human';
    $this_role = str_replace('mania', $this_result, $this_role) . ' copied';
    UpdateRole($this_uname, $this_role);

    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MANIA_RESULT');
  }

  foreach($dead_lovers_list as $this_role) LoversFollowed($this_role); //���͸��ɤ�����

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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $day_night, $uname, $php_argv;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_file   = $ICON_CONF->path . '/' . $this_object->icon_filename;
    $this_color  = $this_object->color;

    //HTML����
    echo <<<EOF
<td><label for="$this_handle">
<img src="$this_file" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($this_uname != 'dummy_boy' && $this_uname != $uname){
      echo '<input type="radio" id="' . $this_handle . '" name="target_handle_name" value="' .
	$this_handle . '">'."\n";
    }
    echo '</label></td>'."\n";
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
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
  global $MESSAGE, $ICON_CONF, $USERS, $room_no, $date, $uname, $php_argv;

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

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_live   = $this_object->live;
    $this_file   = $this_object->icon_filename;
    $this_color  = $this_object->color;

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
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $game_option,
    $date, $uname, $role, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ�Ѥߥ����å�
  if($role_wolf = (strpos($role, 'wolf') !== false)) CheckAlreadyVote('WOLF_EAT');
  elseif($role_mage = (strpos($role, 'mage') !== false)){
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_child_fox = (strpos($role, 'child_fox') !== false)){
    CheckAlreadyVote('CHILD_FOX_DO');
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
  elseif($role_poison_cat = (strpos($role, 'poison_cat') !== false)){
    if($date == 1) OutputVoteResult('�롧�����������ϤǤ��ޤ���');
    CheckAlreadyVote('POISON_CAT_DO');
  }
  else OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');

  //�����귯���� or ������¼�λ��Ͽ����귯�����������٤ʤ�
  if($role_wolf && (strpos($game_option, 'dummy_boy') !== false && $date == 1 ||
		    strpos($game_option, 'quiz') !== false)){
    //�����귯�Υ桼������
    $this_rows = array(1 => $USERS->rows[1]); //dummy_boy = 1�֤��ݾڤ���Ƥ��롩
  }
  else{
    $this_rows = $USERS->rows;
  }
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $cupid_self_shoot = ($count < $GAME_CONF->cupid_self_shoot);

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  foreach($this_rows as $this_user_no => $this_object){
    $this_uname  = $this_object->uname;
    $this_handle = $this_object->handle_name;
    $this_live   = $this_object->live;
    $this_role   = $this_object->role;
    $this_file   = $this_object->icon_filename;
    $this_color  = $this_object->color;
    $this_wolf   = ($role_wolf && strpos($this_role, 'wolf') !== false);

    if($this_live == 'live' || $role_poison_cat){ //ǭ���ϻ�˴��������ˤ��ʤ�
      if($this_wolf) //ϵƱ�Τʤ�ϵ��������
	$path = $ICON_CONF->wolf;
      else //�����Ƥ���Х桼����������
	$path = $ICON_CONF->path . '/' . $this_file;
    }
    else{
      $path = $ICON_CONF->dead; //���Ǥ�л�˴��������
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
    elseif($role_poison_cat){
      if($this_live == 'dead' && $this_uname != $uname && $this_uname != 'dummy_boy'){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live == 'live' && $this_uname != $uname && ! $this_wolf){
      echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	$this_user_no . '">'."\n";
    }
    echo '</label></td>'."\n";
    if(++$count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
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
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
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
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'submit_poison_cat_do';
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

//�򿦾���򹹿�����
function UpdateRole($uname, $role){
  global $room_no;

  mysql_query("UPDATE user_entry SET role = '$role' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
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
