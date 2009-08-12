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
$ROOM = new RoomDataSet($room_no);
$date        = $ROOM->date;
$day_night   = $ROOM->day_night;
$game_option = $ROOM->game_option;

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸���֤����
$USERS = new UserDataSet($room_no); //�桼����������
$user_no     = $USERS->UnameToNumber($uname);
$handle_name = $USERS->GetHandleName($uname);
$role        = $USERS->GetRole($uname);
$live        = $USERS->GetLive($uname);

$command = $_POST['command']; //��ɼ�ܥ���򲡤��� or ��ɼ�ڡ�����ɽ����������
$system_time = TZTime(); //���߻�������

if($ROOM->status == 'finished'){ //������Ͻ�λ���ޤ���
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
  $target_no = $_POST['target_no']; //��ɼ��� user_no (���塼�ԥåɤ����뤿��ñ����������˥��㥹�Ȥ��ƤϤ���)
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
  $delete_role_list = array('lovers', 'copied', 'panelist'); //��꿶���оݳ��򿦤Υꥹ��

  //�����򿦥ƥ�����
  /*
  $test_role_list = array('blinder', 'earplug');
  $delete_role_list = array_merge($delete_role_list, $test_role_list);
  for($i = 0; $i < $user_count; $i++){
    $this_test_role = array_shift($test_role_list);
    if($this_test_role == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      array_push($test_role_list, $this_test_role);
      continue;
    }
    $fix_role_list[$i] .= ' ' . $this_test_role;
  }
  */
  /*
  $add_sub_role = 'blinder';
  array_push($delete_role_list, $add_sub_role);
  for($i = 0; $i < $user_count; $i++){
    if(mt_rand(1, 100) <= 70){
      $fix_role_list[$i] .= ' ' . $add_sub_role;
    }
  }
  */

  $now_sub_role_list = array('decide', 'authority'); //���ץ����ǤĤ��륵���򿦤Υꥹ��
  $delete_role_list  = array_merge($delete_role_list, $now_sub_role_list);
  foreach($now_sub_role_list as $this_role){
    if(strpos($option_role, $this_role) !== false && $user_count >= $GAME_CONF->$this_role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $this_role;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //ϵ��ǯ¼
    $this_role = 'liar';
    array_push($delete_role_list, $this_role);
    for($i = 0; $i < $user_count; $i++){ //�����˰����Ψ��ϵ��ǯ��Ĥ���
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $this_role;
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //�»Ρ��ʽ�¼
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //���������̤˱����ƿ»Τ��ʽ���Ĥ���
      $this_uname = $fix_uname_list[$i];
      $this_role  = $sub_role_list[$USERS->GetSex($this_uname)];
      $fix_role_list[$i] .= ' ' . $this_role;
    }
  }
  if(strpos($option_role, 'sudden_death') !== false){ //�����μ�¼
    $sub_role_list = array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //�����˥���å���Ϥ򲿤��Ĥ���
      $rand_key = array_rand($sub_role_list);
      $this_role = $sub_role_list[$rand_key];
      $fix_role_list[$i] .= ' ' . $this_role;
      if($this_role == 'impatience'){ //û���ϰ�ͤ���
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //������ʥ����򿦤Υ����ɥꥹ�Ȥ����
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    // $sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //�ǥХå���
    array_push($delete_role_list, 'earplug', 'speaker'); //�Х�������Τǰ������
    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count) break;
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //���Ѳ����򥹥��å�
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
    }
  }
  if($quiz){ //������¼
    $this_role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //����԰ʳ��˲����Ԥ�Ĥ���
      if($fix_uname_list[$i] != 'dummy_boy') $fix_role_list[$i] .= ' ' . $this_role;
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
    $this_role_list = explode(' ', $entry_role);
    foreach($this_role_list as $this_role) $role_count_list[$this_role]++;
  }

  //���줾�����䤬���ͤ��ĤʤΤ������ƥ��å�����
  if($chaos && strpos($option_role, 'chaos_open_cast') === false){
    // $sentence = $MESSAGE->chaos;
    $sentence = MakeRoleNameList($role_count_list, true);
  }
  else{
    $sentence = MakeRoleNameList($role_count_list);
  }
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
  if((strpos($game_option, 'quiz') !== false || strpos($game_option, 'gm_login') !== false) &&
     $target == 'GM'){
    OutputVoteResult('Kick��GM �ˤ���ɼ�Ǥ��ޤ���'); //���� GM �б�
  }

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
      (int)$this_voted_number ."\t" . (int)$this_vote_number . "\t" . (int)$vote_times;
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
    KillUser($vote_kill_target, 'VOTE_KILLED', &$dead_lovers_list);

    //�跺�Ԥ���¸�ԥꥹ�Ȥ������
    $live_uname_list = array_diff($live_uname_list, array($vote_kill_target));

    //�跺���줿�ͤ��Ǥ���äƤ������
    do{
      if(strpos($target_role, 'poison') === false) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if(strpos($target_role, 'poison_guard') !== false) break;//���Τ��оݳ�
      if(strpos($target_role, 'dummy_poison') !== false) break;//̴�ǼԤ��оݳ�
      if(strpos($target_role, 'incubate_poison') !== false && $date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      $pharmacist_success = false; //���������ե饰������
      $poison_voter_list  = array_keys($vote_target_list, $vote_kill_target); //��ɼ�����ͤ����
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
      // $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
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

      KillUser($poison_target_uname, 'POISON_DEAD_day', &$dead_lovers_list); //��˴����
    }while(false);

    //��ǽ�Ϥνи������å�
    $flag_necromancer       = false;
    $flag_soul_necromancer  = false;
    $flag_dummy_necromancer = false;
    foreach($USERS->rows as $object){
      $this_main_role = GetMainRole($object->role);
      switch($this_main_role){
      case 'necromancer':
	$flag_necromancer = true;
	break;

      case 'soul_necromancer':
	$flag_soul_necromancer = true;
	break;

      case 'dummy_necromancer':
	$flag_dummy_necromancer = true;
	break;
      }
    }

    //��ǽ�Ϥ�Ƚ����
    $sentence = $target_handle . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽ�Ԥ�Ƚ����
    if(strpos($target_role, 'boss_wolf') !== false){
      $necromancer_result = 'boss_wolf';
    }
    elseif(strpos($target_role, 'wolf') !== false){
      $necromancer_result = 'wolf';
    }
    elseif(strpos($target_role, 'child_fox') !== false){
      $necromancer_result = 'child_fox';
    }
    elseif(strpos($target_role, 'cursed_fox') !== false || strpos($target_role, 'white_fox') !== false){
      $necromancer_result = 'fox';
    }
    else{
      $necromancer_result = 'human';
    }

    if($flag_necromancer){ //��ǽ�Ԥ�����Х����ƥ��å���������Ͽ
      InsertSystemMessage($sentence . $necromancer_result, $action);
    }

    if($flag_soul_necromancer){ //��������Ƚ����
      InsertSystemMessage($sentence . GetMainRole($target_role), 'SOUL_' . $action);
    }

    if($flag_dummy_necromancer){ //̴��ͤ�Ƚ���̤�¼�ͤȿ�ϵ��ȿž����
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //�ü쥵���򿦤����������
  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  $voted_target_member_list = array_count_values($vote_target_list);
  $flag_medium = CheckMedium(); //����νи������å�
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
    SuddenDeath($this_uname, $flag_medium, $this_type);
    if(strpos($this_role, 'lovers') !== false) array_push($dead_lovers_list, $this_role);
  }
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //���͸��ɤ�����
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
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation, $date,
    $user_no, $uname, $handle_name, $role, $target_no;

  if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  switch($situation){
  case 'WOLF_EAT':
    if(strpos($role, 'wolf') === false) OutputVoteResult('�롧��ϵ�ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MAGE_DO':
    if(strpos($role, 'mage') === false) OutputVoteResult('�롧�ꤤ�հʳ�����ɼ�Ǥ��ޤ���');
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
    break;

  case 'MANIA_DO':
    if(strpos($role, 'mania') === false) OutputVoteResult('�롧���åޥ˥��ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(strpos($role, 'poison_cat') === false) OutputVoteResult('�롧ǭ���ʳ�����ɼ�Ǥ��ޤ���');
    if(strpos($game_option, 'not_open_cast') === false){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    $not_type = ($situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(strpos($role, 'assassin') === false) OutputVoteResult('�롧�Ż��԰ʳ�����ɼ�Ǥ��ޤ���');
    $not_type = ($situation == 'ASSASSIN_NOT_DO');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(strpos($role, 'trap_mad') === false) OutputVoteResult('�롧櫻հʳ�����ɼ�Ǥ��ޤ���');
    if(strpos($role, 'lost_ability') !== false) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    $not_type = ($situation == 'TRAP_MAD_NOT_DO');
    break;

  default:
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
    break;
  }
  CheckAlreadyVote($situation); //��ɼ�Ѥߥ����å�

 //���顼��å������Υإå�
  $error_header = '�롧��ɼ�褬����������ޤ���<br>';

  if($not_type){ //��ɼ����󥻥륿���פϲ��⤷�ʤ�
  }
  elseif(strpos($role, 'cupid') !== false){  //���塼�ԥåɤξ�����ɼ����
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
      if($target_name == $uname || $target_live == 'live'){
	OutputVoteResult($error_header . '��ʬ�����Ԥˤ���ɼ�Ǥ��ޤ���');
      }
    }
    elseif(strpos($role, 'trap_mad') !== false){//櫻դϻ�԰�����ɼ��̵��
      if($target_live == 'dead'){
	OutputVoteResult($error_header . '��Ԥˤ���ɼ�Ǥ��ޤ���');
      }
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
  if($not_type){
    //��ɼ����
    $sql = mysql_query("INSERT INTO vote(room_no, date, uname, vote_number, situation)
			VALUES($room_no, $date, '$uname', 1, '$situation')");
    InsertSystemMessage($handle_name, $situation);
    InsertSystemTalk($situation, $system_time, 'night system', '', $uname);
  }
  else{
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
  }

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    AggregateVoteNight(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else OutputVoteResult('�ǡ����١������顼', true);
}

//����򿦤���ɼ����������å�������ɼ��̤��֤�
function CheckVoteNight($action, $role, $dummy_boy_role = '', $not_type = ''){
  global $room_no, $game_option, $date;

  //��ɼ��������
  $sql_vote = mysql_query("SELECT uname, target_uname FROM vote WHERE room_no = $room_no
				AND date = $date AND situation = '$action'");
  $vote_count = mysql_num_rows($sql_vote); //��ɼ�Ϳ������

  if($not_type != ''){ //����󥻥륿���פ���ɼ��������
    $query_not_type = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
      "AND date = $date AND situation = '$not_type'";
    $vote_count += FetchResult($query_not_type); //��ɼ�Ϳ����ɲ�
  }

  //ϵ�γ��ߤϰ�ͤ� OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $sql_vote : false);

  //�����Ƥ����о��򿦤οͿ��򥫥����
  $query_role = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no ".
    "AND live = 'live' AND user_no > 0 AND role LIKE '{$role}%'";
  if($action == 'TRAP_MAD_DO') $query_role .= " AND !(role LIKE '%lost_ability%')";
  $role_count = FetchResult($query_role);

  //�����������귯��������򿦤��ä����ϥ�����Ȥ��ʤ�
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $sql_vote : false);
}

//��ν��׽���
function AggregateVoteNight(){
  global $GAME_CONF, $USERS, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

  $situation_list = array('WOLF_EAT', 'MAGE_DO', 'GUARD_DO', 'REPORTER_DO', 'CUPID_DO',
			  'CHILD_FOX_DO', 'MANIA_DO', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO',
			  'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
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
    if(CheckVoteNight('CUPID_DO', 'cupid') === false) return false;
    if(($sql_mania = CheckVoteNight('MANIA_DO', 'mania', $dummy_boy_role)) === false) return false;
  }
  else{ //�����ܰʹ���ɼ�Ǥ����򿦤�����å�
    if(($sql_guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;
    if(($sql_reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;
    if(strpos($game_option, 'not_open_cast') !== false){
      $sql_poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat', '', 'POISON_CAT_NOT_DO');
      if($sql_poison_cat === false) return false;
    }
    $sql_assassin = CheckVoteNight('ASSASSIN_DO', 'assassin', '', 'ASSASSIN_NOT_DO');
    if($sql_assassin === false) return false;
    $sql_trap_mad = CheckVoteNight('TRAP_MAD_DO', 'trap_mad', '', 'TRAP_MAD_NOT_DO');
    if($sql_trap_mad === false) return false;
  }

  //ϵ����ɼ��桼��̾�Ȥ����������
  $wolf_target_array  = mysql_fetch_assoc($sql_wolf);
  $voted_wolf_uname   = $wolf_target_array['uname'];
  $wolf_target_uname  = $wolf_target_array['target_uname'];
  $wolf_target_handle = $USERS->GetHandleName($wolf_target_uname);
  $wolf_target_role   = $USERS->GetRole($wolf_target_uname);

  $guarded_uname = ''; //��Ҥ��줿�ͤΥ桼��̾ //ʣ�����ߤ��б�����ʤ餳����������Ѥ���
  $dead_uname_list    = array(); //��˴�ԥꥹ��
  $trapped_uname_list = array(); //櫤�������ꥹ��
  $dead_lovers_list   = array(); //���͸��ɤ��оݼԥꥹ��

  if($date != 1){
    //櫻դ�������ꥹ�Ȥ����
    $trap_uname_list = array();
    while(($array = mysql_fetch_assoc($sql_trap_mad)) !== false){
      $this_uname        = $array['uname'];
      $this_target_uname = $array['target_uname'];

      //�������֤�����ǽ�ϼ���
      UpdateRole($this_uname, $USERS->GetRole($this_uname) . ' lost_ability');

      //��ϵ�������Ƥ����鼫ʬ���Ȥؤ����ְʳ���̵��
      if($this_uname != $wolf_target_uname || $this_uname == $this_target_uname){
	$trap_uname_list[$this_uname] = $this_target_uname;
      }
    }

    //櫻դ���ʬ���Ȱʳ���櫤�ųݤ�����硢�������櫤����ä����ϻ�˴
    $trap_count_list = array_count_values($trap_uname_list);
    foreach($trap_uname_list as $this_uname => $this_target_uname){
      if($this_uname != $this_target_uname && $trap_count_list[$this_target_uname] > 1){
	KillUser($this_uname, 'TRAPPED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_uname);
      }
    }
    $trapped_uname_list = array_keys($trap_count_list);

    //��ͤθ������Ƚ��
    while(($array = mysql_fetch_assoc($sql_guard)) !== false){
      $this_uname         = $array['uname'];
      $this_handle        = $USERS->GetHandleName($this_uname);
      $this_role          = $USERS->GetRole($this_uname);
      $this_target_uname  = $array['target_uname'];
      $this_target_handle = $USERS->GetHandleName($this_target_uname);
      $this_target_role   = $USERS->GetRole($this_target_uname);

      if(strpos($this_role, 'dummy_guard') !== false){ //̴��ͤ�ɬ��������å������������Ф�
	InsertSystemMessage($this_handle . "\t" . $this_target_handle, 'GUARD_SUCCESS');
	continue;
      }

      if(strpos($this_target_role, 'trap_mad')   !== false ||
	 strpos($this_target_role, 'cursed_fox') !== false){ //櫻ա�ŷ�Ѹ�Ҥʤ���
	InsertSystemMessage($this_handle . "\t" . $this_target_handle, 'GUARD_HUNTED');
	KillUser($this_target_uname, 'HUNTED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_target_uname);
      }

      if(in_array($this_target_uname, $trapped_uname_list)){ //櫤����֤���Ƥ������˴
	KillUser($this_uname, 'TRAPPED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_uname);
	continue;
      }

      if($this_target_uname != $wolf_target_uname) continue; //��������ʤ��å����������
      InsertSystemMessage($this_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');

      //���ΤǤʤ���硢�������򿦤ϸ�Ҥ���Ƥ���ޤ��
      if(strpos($this_role, 'poison_guard') !== false ||
	 strpos($wolf_target_role, 'reporter') === false ||
	 strpos($wolf_target_role, 'assassin') === false){
	$guarded_uname = $this_target_uname;
      }
    }
  }

  //��ϵ�ν�������Ƚ��
  do{
    //������� or ������¼����
    if($guarded_uname != '' || strpos($game_option, 'quiz') !== false) break;

    //�����褬�ŸѤξ��ϼ��Ԥ���
    if(strpos($wolf_target_role, 'fox') !== false &&
       strpos($wolf_target_role, 'child_fox')  === false &&
       strpos($wolf_target_role, 'poison_fox') === false &&
       strpos($wolf_target_role, 'white_fox')  === false){
      InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
      break;
    }

    if(in_array($wolf_target_uname, $trapped_uname_list)){ //櫤����֤���Ƥ������˴
      KillUser($voted_wolf_uname, 'TRAPPED', &$dead_lovers_list);
      array_push($dead_uname_list, $voted_wolf_uname);
      break;
    }

    //�������
    KillUser($wolf_target_uname, 'WOLF_KILLED', &$dead_lovers_list);
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
    do{
      if(strpos($wolf_target_role, 'poison') === false) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if(strpos($wolf_target_role, 'dummy_poison') !== false) break;//̴�ǼԤ��оݳ�
      if(strpos($wolf_target_role, 'incubate_poison') !== false && $date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      //�����Ƥ���ϵ�����
      $wolf_list = ($GAME_CONF->poison_only_eater ? array($voted_wolf_uname) : GetLiveWolves());

      $rand_key = array_rand($wolf_list);
      $poison_target_uname  = $wolf_list[$rand_key];
      $poison_target_handle = $USERS->GetHandleName($poison_target_uname);
      $poison_target_role   = $USERS->GetRole($poison_target_uname);

      if(strpos($poison_target_role, 'resist_wolf') !== false &&
	 strpos($poison_target_role, 'lost_ability') === false){ //ǽ�Ϥ���ä�����ϵ
	UpdateRole($poison_target_uname, $poison_target_role . ' lost_ability');
	break;
      }

      //�ǻ����
      KillUser($poison_target_uname, 'POISON_DEAD_night', &$dead_lovers_list);
      array_push($dead_uname_list, $poison_target_uname);
    }while(false);
  }while(false);

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

  if($date != 1){ //�Ż��Ԥν���
    while(($array = mysql_fetch_assoc($sql_assassin)) !== false){
      $this_uname  = $array['uname'];
      if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

      //�Ż��ԤΥ������åȤȤʤä��ͤΥϥ�ɥ�͡�����򿦤����
      $this_target_uname  = $array['target_uname'];
      $this_target_handle = $USERS->GetHandleName($this_target_uname);
      $this_target_role   = $USERS->GetRole($this_target_uname);

      if(in_array($this_target_uname, $trapped_uname_list)){ //櫤����֤���Ƥ������˴
	KillUser($this_uname, 'TRAPPED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_uname);
	continue;
      }

      //�Ż�����
      KillUser($this_target_uname, 'ASSASSIN_KILLED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_target_uname);
    }
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

    if(strpos($this_role, 'dummy_mage') !== false){ //̴���ͤ��ꤤ��̤�¼�ͤȿ�ϵ��ȿž������
      $this_result = DistinguishMage($this_target_role);
      if($this_result == 'human')    $this_result = 'wolf';
      elseif($this_result == 'wolf') $this_result = 'human';
    }
    elseif(strpos($this_role, 'psycho_mage') !== false){ //��������Τ�Ƚ��
      $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
      $this_result = 'normal';
      foreach($psycho_mage_liar_list as $this_liar_role){
	if(strpos($this_target_role, $this_liar_role) !== false){
	  $this_result = 'liar';
	  break;
	}
      }
    }
    else{
      if(strpos($this_target_role, 'cursed') !== false){ //�����Ƥ����򿦤���ä����˴����
	KillUser($this_uname, 'CURSED', &$dead_lovers_list);
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
	  KillUser($this_uname, 'FOX_DEAD', &$dead_lovers_list);
	  array_push($dead_uname_list, $this_target_uname);
	}
	$this_result = DistinguishMage($this_target_role); //Ƚ���̤����
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
      KillUser($this_uname, 'CURSED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
      continue;
    }

    //�ꤤ��̤����
    if(mt_rand(1, 100) <= 30){ //�����Ψ�Ǽ��Ԥ���
      $this_result = 'failed';
    }
    else{
      $this_result = DistinguishMage($this_target_role);
    }
    $sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  if($date == 1){
    //���åޥ˥��ν���
    while(($array = mysql_fetch_assoc($sql_mania)) !== false){
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
  }
  else{
    //�֥󲰤ν���
    while(($array = mysql_fetch_assoc($sql_reporter)) !== false){
      $this_uname  = $array['uname'];
      $this_handle = $USERS->GetHandleName($this_uname);
      $this_role   = $USERS->GetRole($this_uname);
      if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

      //������ξ�������
      $this_target_uname = $array['target_uname'];
      if(in_array($this_target_uname, $trapped_uname_list)){ //櫤����֤���Ƥ������˴
	UpdateLive($this_uname);
	InsertSystemMessage($this_handle, 'TRAPPED');
	if(strpos($this_role, 'lovers') !== false){ //���͸��ɤ�����
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
	continue;
      }

      if($this_target_uname == $wolf_target_uname){ //��������
	if($this_target_uname == $guarded_uname) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
	$voted_wolf_handle = $USERS->GetHandleName($voted_wolf_uname);
	$sentence = $this_handle . "\t" . $wolf_target_handle . "\t" . $voted_wolf_handle;
	InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
	continue;
      }

      //�����оݤ�ľ���˻��Ǥ����鲿�ⵯ���ʤ�
      if(in_array($this_target_uname, $dead_uname_list)) continue;

      //���Ԥ����ͤξ�������
      $this_target_role = $USERS->GetRole($this_target_uname);
      if(strpos($this_target_role, 'wolf') !== false || strpos($this_target_role, 'fox') !== false){
	UpdateLive($this_uname); //ϵ���Ѥʤ黦�����
	InsertSystemMessage($this_handle, 'REPORTER_DUTY');
	if(strpos($this_role, 'lovers') !== false){ //���͸��ɤ�����
	  array_push($dead_lovers_list, $this_role);
	}
	array_push($dead_uname_list, $this_uname);
      }
    }

    //ǭ���ν���
    if(strpos($game_option, 'not_open_cast') !== false){
      while(($array = mysql_fetch_assoc($sql_poison_cat)) !== false){
	$this_uname  = $array['uname'];
	$this_handle = $USERS->GetHandleName($this_uname);
	if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

	//�����оݼԤξ�������
	$this_target_uname  = $array['target_uname'];
	$this_target_handle = $USERS->GetHandleName($this_target_uname);
	$this_target_role   = $USERS->GetRole($this_target_uname);

	//����Ƚ��
	$this_rand = mt_rand(1, 100); //����Ƚ�������
	if($this_rand <= 25){ //��������
	  $this_result = 'success';
	  $this_revive_uname = $this_target_uname;
	  if($this_rand <= 5){ //��������
	    $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no AND live = 'dead'
				AND uname <> 'dummy_boy' AND uname <> '$this_target_uname'
				AND user_no > 0 ORDER BY MD5(RAND()*NOW())");
	    //¾���оݤ���������������ؤ��
	    if(mysql_num_rows($sql) > 0) $this_revive_uname = mysql_result($sql, 0, 0);
	  }
	  $this_revive_handle = $USERS->GetHandleName($this_revive_uname);
	  $this_revive_role   = $USERS->GetRole($this_revive_uname);

	  UpdateLive($this_revive_uname, true);
	  InsertSystemMessage($this_revive_handle, 'REVIVE_SUCCESS');
	  if(strpos($revive_role, 'lovers') !== false){ //���ͤʤ�¨����
	    array_push($dead_lovers_list, $revive_role);
	  }
	}
	else{
	  $this_result = 'failed';
	  InsertSystemMessage($this_target_handle, 'REVIVE_FAILED');
	}
	$sentence = $this_handle . "\t" . $this_target_handle . "\t" . $this_result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }
  $flag_medium = CheckMedium();
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //���͸��ɤ�����
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

  //��ɼ��������
  $vote_times = GetVoteTimes();

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
    $date, $user_no, $uname, $role, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ�Ѥߥ����å�
  if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  if($role_wolf = (strpos($role, 'wolf') !== false)) CheckAlreadyVote('WOLF_EAT');
  elseif($role_mage = (strpos($role, 'mage') !== false)){
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
    if($date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('MANIA_DO');
  }
  elseif($role_assassin = (strpos($role, 'assassin') !== false)){
    if($date == 1) OutputVoteResult('�롧�����ΰŻ��ϤǤ��ޤ���');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_trap_mad = (strpos($role, 'trap_mad') !== false)){
    if($date == 1) OutputVoteResult('�롧����������֤ϤǤ��ޤ���');
    if(strpos($role, 'lost_ability') !== false) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_poison_cat = (strpos($role, 'poison_cat') !== false)){
    if($date == 1) OutputVoteResult('�롧�����������ϤǤ��ޤ���');
    if(strpos($game_option, 'not_open_cast') === false){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
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
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

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
    elseif($role_trap_mad){
      if($this_live == 'live'){
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
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'submit_poison_cat_not_do';
  }
  elseif($role_assassin){
    $type   = 'ASSASSIN_DO';
    $submit = 'submit_assassin_do';
    $not_type   = 'ASSASSIN_NOT_DO';
    $not_submit = 'submit_assassin_not_do';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'submit_trap_mad_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'submit_trap_mad_not_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$MESSAGE->$submit}"></td></form>

EOF;

  if($not_type != ''){
    echo <<<EOF
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="command" value="vote">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$user_no}">
<input type="submit" value="{$MESSAGE->$not_submit}"></form>
</td>

EOF;
  }

  echo <<<EOF
</tr></table></div>
</body></html>

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

//��˴����
function KillUser($uname, $reason, &$dead_lovers_list){
  global $USERS;

  $target_handle = $USERS->GetHandleName($uname);
  $target_role   = $USERS->GetRole($uname);

  UpdateLive($uname);
  InsertSystemMessage($target_handle, $reason);
  SaveLastWords($target_handle);
  if(strpos($target_role, 'lovers') !== false){ //���͸��ɤ�����
    array_push($dead_lovers_list, $target_role);
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
function CheckAlreadyVote($situation, $not_situation = ''){
  if(CheckSelfVoteNight($situation, $not_situation)) OutputVoteResult('�롧��ɼ�Ѥ�');
}
?>
