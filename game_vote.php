<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

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

//��ɼ��̽���
function OutputVoteResult($str, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //���ޤǤ���ɼ���������
  OutputActionResult('��Ͽ�ϵ�ʤ�䡩[��ɼ���]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $str . '<br>'."\n" .
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

  $uname_array    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $role_array     = array(); //�桼��̾���б��������
  $re_uname_array = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ

  //�ե饰���å�
  $quiz      = (strpos($game_option, 'quiz')      !== false);
  $chaos     = (strpos($game_option, 'chaos')     !== false); //chaosfull ��ޤ�
  $chaosfull = (strpos($game_option, 'chaosfull') !== false);

  //�桼���ꥹ�Ȥ������˼���
  //������¼�б� //Ʊ����ˡ�ǥ���ȷ������Ǥ�����
  if($quiz){
    array_push($uname_array, 'dummy_boy');
    array_push($role_array, 'quiz');
    $now_role_list = array_diff($now_role_list, $role_array);

    $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) AS MyRand FROM user_entry
					WHERE room_no = $room_no AND uname <> 'dummy_boy'
					AND user_no > 0 ORDER BY MyRand");
  }
  else{
    $sql_user_list = mysql_query("SELECT uname, role, MD5(RAND()*NOW()) AS MyRand FROM user_entry
					WHERE room_no = $room_no AND user_no > 0 ORDER BY MyRand");
  }

  for($i = 0; $i < $user_count; $i++){ //��˾����������
    $user_list_array = mysql_fetch_assoc($sql_user_list); //������ʥ桼����������
    $this_uname = $user_list_array['uname'];

    //����˾���ξ�硢��˾�����
    if(strpos($game_option, 'wish_role') !== false && ! $chaos)
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
  for($i = 0; $i < $re_count; $i++){ //;�ä����������Ƥ�
    array_push($uname_array, $re_uname_array[$i]);
    array_push($role_array,  $now_role_list[$i]);
  }

  //��Ǥ�Ȥʤ���������
  $rand_keys = array_rand($role_array, $user_count); //�����७�������

  //��Ǥ�Ȥʤ륪�ץ�������(����ԡ����ϼ�)
  $option_subrole = array();
  $option_subrole_count = 0;
  if(strpos($option_role, 'decide') !== false && $user_count >= $GAME_CONF->decide){
    $role_array[$rand_keys[$option_subrole_count]] .= ' decide';
    $option_subrole_count++;
    $option_subrole['decide']++;
  }
  if(strpos($option_role, 'authority') !== false && $user_count >= $GAME_CONF->authority){
    $role_array[$rand_keys[$option_subrole_count]] .= ' authority';
    $option_subrole_count++;
    $option_subrole['authority']++;
  }
  if($chaos){
    foreach($GAME_CONF->sub_role_list as $key => $value){
      if($user_count < $option_subrole_count) break;
      if($key == 'decite' || $key == 'authority') continue; //����Ԥȸ��ϼԤϥ��ץ��������椹��
      if((int)$option_subrole[$key] > 0) continue; //����ï�����Ϥ��Ƥ���Х����å�
      $role_array[$rand_keys[$option_subrole_count]] .= ' ' . $key;
      $option_subrole[$key]++;
    }
  }

  //�����귯���Ѥξ�硢�����귯��ϵ���ѡ����Ǽԡ����塼�ԥåɰʳ��ˤ���
  if(strpos($game_option, 'dummy_boy') !== false){
    $dummy_boy_index = array_search('dummy_boy', $uname_array); //�����귯�����󥤥�ǥå��������
    if(CheckRole($role_array[$dummy_boy_index])){
      for($i = 0; $i < $user_count; $i++){
	//ϵ���ѡ����Ǽԡ����塼�ԥåɰʳ������Ĥ��ä��������ؤ���
	if(! CheckRole($role_array[$i])){
	  $tmp_role = $role_array[$dummy_boy_index];
	  $role_array[$dummy_boy_index] = $role_array[$i];
	  $role_array[$i] = $tmp_role;
	  break;
	}
      }
      if(CheckRole($role_array[$dummy_boy_index])){ //�����귯���򿦤���٥����å�
	if($chaosfull){ //��������λ��϶��������ؤ�
	  $role_array[$dummy_boy_index] = 'human';
	}
	else{
	  OutputVoteResult('�����ॹ������[�������ꥨ�顼]��' .
			   '�����귯��ϵ���ѡ����Ǽԡ����塼�ԥåɤΤ����줫�ˤʤäƤ��ޤ���<br>' .
			   '�����Ԥ��䤤��碌�Ʋ�������', true, true);
	}
      }
    }
  }

  //�����೫��
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night'
		WHERE room_no = $room_no");
  DeleteVote(); //���ޤǤ���ɼ���������

  //����DB�˹���
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $entry_uname = $uname_array[$i];
    $entry_role  = $role_array[$i];
    mysql_query("UPDATE user_entry SET role = '$entry_role' WHERE room_no = $room_no
			AND uname = '$entry_uname' AND user_no > 0");
    $role_count_list[GetMainRole($entry_role)]++;
    if(strpos($entry_role, 'decide')        !== false) $role_count_list['decide']++;
    if(strpos($entry_role, 'authority')     !== false) $role_count_list['authority']++;
    if(strpos($entry_role, 'strong_voice')  !== false) $role_count_list['strong_voice']++;
    if(strpos($entry_role, 'normal_voice')  !== false) $role_count_list['normal_voice']++;
    if(strpos($entry_role, 'weak_voice')    !== false) $role_count_list['weak_voice']++;
    if(strpos($entry_role, 'no_last_words') !== false) $role_count_list['no_last_words']++;
    if(strpos($entry_role, 'chicken')       !== false) $role_count_list['chicken']++;
    if(strpos($entry_role, 'rabbit')        !== false) $role_count_list['rabbit']++;
    if(strpos($entry_role, 'perverseness' ) !== false) $role_count_list['perverseness']++;
  }

  //���줾�����䤬���ͤ��ĤʤΤ������ƥ��å�����
  if($chaos && ! $GAME_CONF->chaos_open_role)
    $sentence = $MESSAGE->chaos;
  else
    $sentence = MakeRoleNameList($role_count_list);
  InsertSystemTalk($sentence, $system_time, 'night system', 1);  //���ꥹ������
  InsertSystemMessage('1', 'VOTE_TIMES', 1); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  if($chaosfull) CheckVictory(); //��������Ϥ����ʤ꽪λ���Ƥ��ǽ������
  mysql_query('COMMIT'); //������ߥå�
}

//�Ϳ��ȥ����४�ץ����˱������򿦥ơ��֥���֤� (���顼�����ϻ���)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $game_option;

  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  $role_list = $GAME_CONF->role_list[$user_count]; //�Ϳ��˱���������ꥹ�Ȥ����
  if($role_list == NULL){ //�ꥹ�Ȥ�̵ͭ������å�
    OutputVoteResult($error_header . $user_count . '�ͤ����ꤵ��Ƥ��ޤ���' .
                     $error_footer, true, true);
  }

  //���Ǽ� (¼�ͣ� �� �ǣ���ϵ��)
  if(strpos($option_role, 'poison') !== false && $user_count >= $GAME_CONF->poison){
    $role_list['human'] -= 2;
    $role_list['poison']++;
    $role_list['wolf']++;
  }

  //���塼�ԥå� (14�ͤϥϡ��ɥ����� / ¼�� �� ���塼�ԥå�)
  if(strpos($option_role, 'cupid') !== false &&
     ($user_count == 14 || $user_count >= $GAME_CONF->cupid)){
    $role_list['human']--;
    $role_list['cupid']++;
  }

  //��ϵ (��ϵ �� ��ϵ)
  if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $GAME_CONF->boss_wolf){
    $role_list['wolf']--; //�ޥ��ʥ��Υ����å����Ƥʤ��Τ����
    $role_list['boss_wolf']++;
  }

  if(strpos($game_option, 'quiz') !== false){  //������¼
    $temp_role_list = array();
    $temp_role_list['human'] = $role_list['human'];
    foreach($role_list as $key => $value){
      if($key == 'wolf' || $key == 'mad' || $key == 'common' || $key == 'fox'){
	$temp_role_list[$key] = (int)$value;
      }
      elseif($key != 'human'){
	$temp_role_list['human'] += (int)$value;
      }
    }
    $temp_role_list['human']--;
    $temp_role_list['quiz'] = 1;
    $role_list = $temp_role_list;
  }
  elseif(strpos($game_option, 'chaos') !== false){ //����
    if(strpos($game_option, 'chaosfull') !== false){ //��������
      //-- �ƿرĤοͿ������ (�Ϳ� = �ƿͿ��νи�Ψ) --//
      //��ϵ�ر�
      $rand = mt_rand(1, 100); //�Ϳ����������
      if($user_count < 8){ //1:2 = 80:20
	if($rand <= 80) $wolf_count = 1;
	else $wolf_count = 2;
      }
      elseif($user_count < 16){ //1:2:3 = 15:70:15
	if($rand <= 15) $wolf_count = 1;
	elseif($rand <= 85) $wolf_count = 2;
	else $wolf_count = 3;
      }
      elseif($user_count < 21){ //1:2:3:4:5 = 5:10:70:10:5
	if($rand <= 5) $wolf_count = 1;
	elseif($rand <= 15) $wolf_count = 2;
	elseif($rand <= 85) $wolf_count = 3;
	elseif($rand <= 95) $wolf_count = 4;
	else $wolf_count = 5;
      }
      else{ //�ʸ塢5�������뤴�Ȥ� 1�ͤ�������
	$base_count = floor(($user_count - 20) / 5) + 3;
	if($rand <= 5) $wolf_count = $base_count - 2;
	elseif($rand <= 15) $wolf_count = $base_count - 1;
	elseif($rand <= 85) $wolf_count = $base_count;
	elseif($rand <= 95) $wolf_count = $base_count + 1;
	else $wolf_count = $base_count + 2;
      }

      //�Ÿѿر�
      $rand = mt_rand(1, 100); //�Ϳ����������
      if($user_count < 15){ //0:1 = 90:10
	if($rand <= 90) $fox_count = 0;
	else $fox_count = 1;
      }
      elseif($user_count < 23){ //1:2 = 90:10
	if($rand <= 90) $fox_count = 1;
	else $fox_count = 2;
      }
      else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
	$base_count = ceil($user_count / 20);
	if($rand <= 10) $fox_count = $base_count - 1;
	elseif($rand <= 90) $fox_count = $base_count;
	else $fox_count = $base_count + 1;
      }

      //���Ϳر� (�¼����塼�ԥå�)
      $rand = mt_rand(1, 100); //�Ϳ����������
      if($user_count < 10){ //0:1 = 95:5
	if($rand <= 95) $lovers_count = 0;
	else $lovers_count = 1;
      }
      elseif($user_count < 16){ //0:1 = 70:30
	if($rand <= 70) $lovers_count = 0;
	else $lovers_count = 1;
      }
      elseif($user_count < 23){ //0:1:2 = 5:90:5
	if($rand <= 5) $lovers_count = 0;
	elseif($rand <= 95) $lovers_count = 1;
	else $lovers_count = 2;
      }
      else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
	//����-1:����:����+1 = 5:90:5
	$base_count = floor($user_count / 20);
	if($rand <= 5) $lovers_count = $base_count - 1;
	elseif($rand <= 95) $lovers_count = $base_count;
	else $lovers_count = $base_count + 1;
      }
      $role_list['cupid'] = $lovers_count;
    }
    else{ //�̾����
      $wolf_count   = $role_list['wolf'] + $role_list['boss_wolf'];
      $fox_count    = $role_list['fox'] + $role_list['child_fox'];
      $lovers_count = $role_list['cupid'];
    }
    //¼�ͿرĤοͿ��򻻽�
    $human_count = $user_count - $wolf_count - $fox_count - $lovers_count;

    //��ϵ�Ϥ���������
    $boss_wolf_count = 0; //��ϵ�οͿ�
    $base_count = ceil($user_count / 15); //�ü�ϵȽ�����򻻽�
    for(; $base_count > 0; $base_count--){
      if(mt_rand(1, 100) <= $user_count) $boss_wolf_count++; //���ÿͿ� % �γ�Ψ����ϵ�и�
    }
    if($boss_wolf_count > $wolf_count){ //ϵ�������Ķ�������ϵ�� 0 �ˤ���
      $role_list['boss_wolf'] = $wolf_count;
      $role_list['wolf'] = 0;
    }
    else{
      $role_list['boss_wolf'] = $boss_wolf_count;
      $role_list['wolf'] = $boss_wolf_count - $wolf_count;
    }

    //�ŸѷϤ���������
    if($user_count < 20){ //���͸���20��̤���ξ��ϻҸѤϽи����ʤ�
      $role_list['fox'] = $fox_count;
      $role_list['child_fox'] = 0;
    }
    else{ //���ÿͿ� % �ǻҸѤ���ͽи�
      if(mt_rand(1, 100) <= $user_count) $role_list['child_fox'] = 1;
      $role_list['fox'] = $fox_count - (int)$role_list['child_fox'];
    }

    //�ꤤ�ϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 8){ //0:1 = 10:90
      if($rand <= 10) $mage_count = 0;
      else $mage_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $mage_count = 1;
      else $mage_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $mage_count = 1;
      else $mage_count = 2;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mage_count = $base_count - 1;
      elseif($rand <= 90) $mage_count = $base_count;
      else $mage_count = $base_count + 1;
    }

    //�ꤤ�Ϥ���������
    if($mage_count > 0 && $human_count >= $mage_count){
      if($user_count < 16){ //���͸���16��̤���ξ��Ϻ����ꤤ�դϽи����ʤ�
	$role_list['mage'] = $mage_count;
	$role_list['soul_mage'] = 0;
      }
      else{ //���ÿͿ� % �Ǻ����ꤤ�դ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['soul_mage'] = 1;
	$role_list['mage'] = $mage_count - (int)$role_list['soul_mage'];
      }
      $human_count -= $mage_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //����οͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 9){ //0:1 = 70:30
      if($rand <= 70) $medium_count = 0;
      else $medium_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $medium_count = 0;
      elseif($rand <= 90) $medium_count = 1;
      else $medium_count = 2;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15);
      if($rand <= 10) $medium_count = $base_count - 1;
      elseif($rand <= 90) $medium_count = $base_count;
      else $medium_count = $base_count + 1;
    }
    if($cupid_count > 0 && $medium_count == 0) && $medium_count++;

    //�������������
    if($medium_count > 0 && $human_count >= $medium_count){
      $role_list['medium'] = $medium_count;
      $human_count -= $medium_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //��ǽ�ϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 9){ //0:1 = 10:90
      if($rand <= 10) $necromancer_count = 0;
      else $necromancer_count = 1;
    }
    elseif($user_count < 16){ //1:2 = 95:5
      if($rand <= 95) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    elseif($user_count < 30){ //1:2 = 90:10
      if($rand <= 90) $necromancer_count = 1;
      else $necromancer_count = 2;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15);
      if($rand <= 10) $necromancer_count = $base_count - 1;
      elseif($rand <= 90) $necromancer_count = $base_count;
      else $necromancer_count = $base_count + 1;
    }

    //��ǽ�Ϥ���������
    if($necromancer_count > 0 && $human_count >= $necromancer_count){
      $role_list['necromancer'] = $necromancer_count;
      $human_count -= $necromancer_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //���ͷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 10){ //0:1 = 30:70
      if($rand <= 30) $mad_count = 0;
      else $mad_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $mad_count = 0;
      elseif($rand <= 90) $mad_count = 1;
      else $mad_count = 2;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15);
      if($rand <= 10) $mad_count = $base_count - 1;
      elseif($rand <= 90) $mad_count = $base_count;
      else $mad_count = $base_count + 1;
    }

    //���ͷϤ���������
    if($human_count > 0 && $human_count >= $mad_count){
      if($user_count < 16){ //���͸���16��̤���ξ��϶����ԤϽи����ʤ�
	$role_list['mad'] = $mad_count;
	$role_list['fanatic_mad'] = 0;
      }
      else{ //���ÿͿ� % �Ƕ����Ԥ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['fanatic_mad'] = 1;
	$role_list['mad'] = $mad_count - (int)$role_list['fanatic_mad'];
      }
      $human_count -= $mad_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //��ͷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 11){ //0:1 = 10:90
      if($rand <= 10) $guard_count = 0;
      else $guard_count = 1;
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      if($rand <= 10) $guard_count = 0;
      elseif($rand <= 90) $guard_count = 1;
      else $guard_count = 2;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15);
      if($rand <= 10) $guard_count = $base_count - 1;
      elseif($rand <= 90) $guard_count = $base_count;
      else $guard_count = $base_count + 1;
    }

    //��ͷϤ���������
    if($human_count > 0 && $human_count >= $guard_count){
      if($user_count < 20){ //���͸���20��̤���ξ��ϵ��ΤϽи����ʤ�
	$role_list['guard'] = $guard_count;
	$role_list['poison_guard'] = 0;
      }
      else{ //���ÿͿ� % �ǵ��Τ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['poison_guard'] = 1;
	$role_list['guard'] = $guard_count - (int)$role_list['poison_guard'];
      }
      $human_count -= $guard_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //��ͭ�ԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 13){ //0:1 = 10:90
      if($rand <= 10) $common_count = 0;
      else $common_count = 1;
    }
    elseif($user_count < 22){ //1:2:3 = 10:80:10
      if($rand <= 10) $common_count = 1;
      elseif($rand <= 90) $common_count = 2;
      else $common_count = 3;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 15) + 1;
      if($rand <= 10) $common_count = $base_count - 1;
      elseif($rand <= 90) $common_count = $base_count;
      else $common_count = $base_count + 1;
    }

    //��ͭ�Ԥ���������
    if($common_count > 0 && $human_count >= $common_count){
      $role_list['common'] = $common_count;
      $human_count -= $common_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //���ǼԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 95:5
      if($rand <= 95) $poison_count = 0;
      else $poison_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $poison_count = 0;
      else $poison_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 20);
      if($rand <= 10) $poison_count = $base_count - 1;
      elseif($rand <= 90) $poison_count = $base_count;
      else $poison_count = $base_count + 1;
    }
    $poison_count -= $poison_guard_count; //���Το��������餹

    //���ǼԤ���������
    if($poison_count > 0 && $human_count >= $poison_count){
      $role_list['poison'] = $poison_count;
      $human_count -= $poison_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //����ԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 30){ //0:1 = 99:1
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���30�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 30) - 1;
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }

    //����Ԥ���������
    if($quiz_count > 0 && $human_count >= $quiz_count){
      $role_list['quiz'] = $quiz_count;
      $human_count -= $quiz_count; //¼�ͿرĤλĤ�Ϳ�
    }

    $role_list['human'] = $human_count; //¼�ͤοͿ�
  }

  if($role_list['human'] < 0){ //"¼��" �οͿ�������å�
    OutputVoteResult($error_header . '"¼��" �οͿ����ޥ��ʥ��ˤʤäƤޤ�' .
                     $error_footer, true, true);
  }

  //��̾���Ǽ�������������
  $now_role_list = array();
  foreach($role_list as $key => $value){
    for($i = 0; $i < $value; $i++) array_push($now_role_list, $key);
  }
  $role_count = count($now_role_list);

  if($role_count != $user_count){ //����Ĺ������å�
    OutputVoteResult($error_header . '¼�� (' . $user_count . ') ������ο� (' . $role_count .
                     ') �����פ��Ƥ��ޤ���' . $error_footer, true, true);
  }

  return $now_role_list;
}

//�����귯���ʤ�ʤ��򿦤�����å�����
function CheckRole($role){
  return (strpos($role, 'wolf')   !== false ||
	  strpos($role, 'fox')    !== false ||
	  strpos($role, 'poison') !== false ||
	  strpos($role, 'cupid')  !== false);
}


//�򿦤οͿ����Υꥹ�Ȥ��������
function MakeRoleNameList($role_count_list){
  global $GAME_CONF;

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '��' . $value . $count;
  }
  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '��(' . $value . $count . ')';
  }
  return $sentence;
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

  //���ϼԤʤ���ɼ������
  $vote_number = (strpos($role, 'authority') !== false ? 2 : 1);

  //��ɼ
  $sql = mysql_query("INSERT INTO vote(room_no,date,uname,target_uname,vote_number,vote_times,situation)
		VALUES($room_no,$date,'$uname','$target_uname',$vote_number,$vote_times,'$situation')");
  InsertSystemTalk("VOTE_DO\t" . $target_handle, $system_time, 'day system', '', $uname); //��ɼ���ޤ�������

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
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND date = $date AND situation = '$situation' AND vote_times = $vote_times");
  $vote_count = mysql_result($sql, 0, 0);

  //�����Ƥ���桼���������
  $sql_user = mysql_query("SELECT uname, handle_name, role FROM user_entry WHERE room_no = $room_no
		AND live = 'live' AND user_no > 0 ORDER BY user_no");
  $user_count = mysql_num_rows($sql_user);

  //��������ɼ���Ƥ������
  if($vote_count != $user_count) return false;

  $check_draw = true; //����ʬ��Ƚ��¹ԥե饰
  $max_voted_number = 0; //��¿��ɼ��
  $handle_list = array(); //�桼��̾�ȥϥ�ɥ�͡�����б�ɽ
  $role_list   = array(); //�桼��̾���򿦤��б�ɽ
  $live_list   = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_target_list = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��ϥ�ɥ�͡���)
  $vote_count_list  = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  for($i = 0; $i < $user_count; $i++){ //�桼�� No ��˽���
    $array = mysql_fetch_assoc($sql_user);
    $this_uname  = $array['uname'];
    $this_handle = $array['handle_name'];
    $this_role   = $array['role'];

    //��ʬ����ɼ�������
    $sql = mysql_query("SELECT SUM(vote_number) FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times
			AND target_uname = '$this_uname'");
    $this_voted_number = (int)mysql_result($sql, 0, 0);

    //��ʬ����ɼ������� //DB ���䤤��碌�ʤ��Ƥ��򿦤��黻�ФǤ���ΤǤϡ�
    $sql =mysql_query("SELECT vote_number FROM vote WHERE room_no = $room_no AND date = $date
			AND situation = '$situation' AND vote_times = $vote_times
			AND uname = '$this_uname'");
    $this_vote_number = (int)mysql_result($sql, 0, 0);

    //��ʬ����ɼ�����ͤΥϥ�ɥ�͡�������
    $sql = mysql_query("SELECT user_entry.handle_name AS handle_name FROM user_entry, vote
			WHERE user_entry.room_no = $room_no AND vote.room_no = $room_no
			AND vote.date = $date AND vote.situation = '$situation'
			AND vote_times = $vote_times AND vote.uname = '$this_uname'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
    $this_vote_target = mysql_result($sql, 0, 0);

    //��ɼ��̤򥿥ֶ��ڤ�ǽ��� (ï�� [TAB] ï�� [TAB] ��ʬ����ɼ�� [TAB] ��ʬ����ɼ�� [TAB] ��ɼ���)
    $sentence = $this_handle . "\t" .  $this_vote_target . "\t" .
      $this_voted_number ."\t" . $this_vote_number . "\t" . (int)$vote_times ;

    //��ɼ����򥷥��ƥ��å���������Ͽ
    InsertSystemMessage($sentence, $situation);

    //������ɼ���򹹿�
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //�ꥹ�Ȥ˥ǡ������ɲ�
    $handle_list[$this_uname] = $this_handle;
    $role_list[$this_uname]   = $this_role;
    $vote_target_list[$this_uname] = $this_vote_target;
    $vote_count_list[$this_uname]  = $this_voted_number;
    array_push($live_list, $this_uname);
  }

  //������ɼ���򽸤᤿�ͤο������
  $voted_member_list = array_count_values($vote_count_list); //��ɼ�� => �Ϳ� �����������
  $max_voted_member = $voted_member_list[$max_voted_number]; //������ɼ���򽸤᤿�ͤο�

  //������ɼ���Υ桼��̾�Υꥹ�Ȥ����
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);

  if($max_voted_member == 1){ //��ͤ����ξ�硢�跺������ˤ���
    VoteKill($max_voted_uname_list[0], $vote_count_list, $vote_target_list,
	     $handle_list, $role_list, $live_list);
    $check_draw = false;
  }
  else{ //ʣ�������Ф���������Ԥ���ʤ���к���ɼ
    $revote_flag = true; //����ɼ�ե饰������
    $target_uname = '';

    foreach($max_voted_uname_list as $max_voted_uname){
      //��ɼ�Ԥ˷���Ԥ����뤫õ��
      $sql = mysql_query("SELECT user_entry.role FROM user_entry, vote
				WHERE user_entry.room_no = $room_no
				AND user_entry.role LIKE '%decide&'
				AND vote.room_no = $room_no AND vote.date = $date
				AND vote.situation = '$situation'
				AND vote.vote_times = $vote_times
				AND vote.uname = user_entry.uname
				AND vote.target_uname = '$max_voted_uname'
				AND user_entry.user_no > 0");
      if(mysql_num_rows($sql) > 0){ //����Ԥ�����н跺
	$revote_flag = false;
	$target_uname = $max_voted_uname; //�跺�оݼԤ򥻥å�
	break;
      }
    }

    if($revote_flag){ //����ɼ
      //�ü쥵���򿦤����������
      VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list);

      $next_vote_times = $vote_times + 1; //��ɼ��������䤹
      mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = $date AND type = 'VOTE_TIMES'");

      //�����ƥ��å�����
      InsertSystemMessage($vote_times, 'RE_VOTE');
      InsertSystemTalk("����ɼ�ˤʤ�ޤ���( $vote_times ����)", ++$system_time);
      UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
    }
    else{ //�跺������ˤ���
      VoteKill($target_uname, $vote_count_list, $vote_target_list,
	       $handle_list, $role_list, $live_list);
      $check_draw = false;
    }
  }
  CheckVictory($check_draw);
}

//��ɼ�ǽ跺����
function VoteKill($target_uname, $vote_count_list, $vote_target_list,
		  $handle_list, $role_list, $live_list){
  global $system_time, $room_no, $date;

  //�桼����������
  $target_handle = $handle_list[$target_uname];
  $target_role   = $role_list[$target_uname];

  //�跺����
  KillUser($target_uname); //��˴����
  InsertSystemMessage($target_handle, 'VOTE_KILLED'); //�����ƥ��å�����
  SaveLastWords($target_handle); //�跺�Ԥΰ��

  //�跺���줿�ͤ����ǼԤξ��
  if(strpos($target_role, 'poison') !== false &&
     strpos($target_role, 'poison_guard') === false){ //���Τ��оݳ�
    //¾�οͤ��������˰������
    //���͸��ɤ���������ˤ���ȸ��ɤ��������ͤ�ޤ�Ƥ��ޤ��Τ�
    //����ơָ��ߤ���¸�ԡפ� DB ���䤤��碌��٤�����ʤ����ʡ�
    $array = array_diff($live_list, array($target_uname));
    $rand_key = array_rand($array, 1);
    $poison_target_uname  = $array[$rand_key];
    $poison_target_handle = $handle_list[$target_uname];
    $poison_target_role   = $role_list[$target_uname];

    KillUser($poison_target_uname); //��˴����
    InsertSystemMessage($poison_target_handle, 'POISON_DEAD_day'); //�����ƥ��å�����
    SaveLastWords($poison_target_handle); //�������

    //�ǻष���ͤ����ͤξ��
    if(strpos($poison_target_role, 'lovers') !== false) LoversFollowed($poison_target_role);
  }

  //�跺���줿�ͤ����ͤξ��
  //�跺�夹�����ɤ�����Τ��ڤ��Ȼפ�����
  //�����Ǥ����ǼԤΥ������å����н������Ť��ΤǤ����ǽ���
  if(strpos($target_role, 'lovers') !== false) LoversFollowed($target_role);

  //��ǽ�Ԥη��(�����ƥ��å�����)
  if(strpos($target_role, 'boss_wolf') !== false)
    $necro_max_voted_role = 'boss_wolf';
  elseif(strpos($target_role, 'wolf') !== false)
    $necro_max_voted_role = 'wolf';
  else
    $necro_max_voted_role = 'human';

  InsertSystemMessage($target_handle . "\t" . $necro_max_voted_role, 'NECROMANCER_RESULT');

  //�ü쥵���򿦤����������
  VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list);

  mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //��ˤ���
  InsertSystemTalk('NIGHT', ++$system_time, 'night system'); //�뤬��������
  UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  mysql_query('COMMIT'); //������ߥå�
}

//��ɼ�ˤ���ü쥵���򿦤����������
function VoteSuddenDeath($vote_count_list, $vote_target_list, $handle_list, $role_list){
  $uname_list = array_flip($handle_list); //�ϥ�ɥ�͡��� => �桼��̾
  foreach($vote_count_list as $key => $value){
    $this_role = $role_list[$key];
    if($value > 0){
      if(strpos($this_role, 'chicken') !== false)
	SuddenDeath($key, $handle_list[$key], $this_role, 'CHICKEN');
    }
    else{
      if(strpos($this_role, 'rabbit') !== false)
	SuddenDeath($key, $handle_list[$key], $this_role, 'RABBIT');
    }
    if(strpos($this_role, 'perverseness') !== false){
      $target_value = $vote_count_list[$uname_list[$vote_target_list[$key]]]; //��ɼ�оݼԤ���ɼ
      if($target_value > 1 || (strpos($this_role, 'authority') !== false && $target_value > 2))
	SuddenDeath($key, $handle_list[$key], $this_role, 'PERVERSENESS');
    }
  }
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

    case 'CUPID_DO':
      if(strpos($role, 'cupid') === false) OutputVoteResult('�롧���塼�ԥåɰʳ�����ɼ�Ǥ��ޤ���');
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
    if(mysql_result($sql, 0, 0) < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
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
       (strpos($role, 'wolf') !== false && strpos($target_role, 'wolf') !== false)){
      OutputVoteResult($error_header . '��ԡ���ʬ��ϵƱ�Τ���ɼ�Ǥ��ޤ���');
    }

    if($situation == 'WOLF_EAT'){
      //������¼�� GM �ʳ�̵��
      if(strpos($game_option, 'quiz') !== false && $target_uname != 'dummy_boy'){
	OutputVoteResult($error_header . '������¼�Ǥ� GM �ʳ�����ɼ�Ǥ��ޤ���');
      }

      //ϵ�ν�������ɼ�Ͽ����귯���Ѥξ��Ͽ����귯�ʳ�̵��
      if(strpos($game_option, 'dummy_boy') !== false && $target_uname != 'dummy_boy' && $date == 1){
	OutputVoteResult($error_header . '�����귯���Ѥξ��ϡ������귯�ʳ�����ɼ�Ǥ��ޤ���');
      }
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

  //��ɼ
  $sql_vote = mysql_query("INSERT INTO vote(room_no, date, uname, target_uname, vote_number, situation)
			VALUES($room_no, $date, '$uname', '$target_uname_str', 1, '$situation')");
  //�����ƥ��å�����
  InsertSystemMessage($handle_name . "\t" . $target_handle_str, $situation);
  //��ɼ���ޤ�������
  InsertSystemTalk($situation . "\t" . $target_handle_str, $system_time, 'night system', '', $uname);

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
  global $GAME_CONF, $system_time, $room_no, $game_option, $situation,
    $date, $day_night, $vote_times, $user_no, $uname, $handle_name, $target_no;

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
  $sql = mysql_query($query_role . "'%mage%'");
  $mage_count = mysql_result($sql, 0, 0);

  if($date == 1 && strpos($game_option, 'dummy_boy') !== false){
    //�����������귯����䤬�ꤤ�դξ���ꤤ�դο�������ʤ�
    $sql = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
			AND uname = 'dummy_boy' AND user_no > 0");
    if(strpos(mysql_result($sql, 0, 0), 'mage') !== false) $mage_count--;
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

    $sql = mysql_query($query_role . "'%guard%'");
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

  $guard_success_flag = false;
  for($i = 0; $i < $guard_count; $i++ ){ //��������������å�
    $guard_array  = mysql_fetch_assoc($sql_guard);
    $guard_uname  = $guard_array['target_uname'];
    $guard_handle = $guard_array['handle_name'];

    if($guard_uname == $wolf_target_uname){ //�������
      //��������Υ�å�����
      InsertSystemMessage($guard_handle . "\t" . $wolf_target_handle, 'GUARD_SUCCESS');
      $guard_success_flag = true;
    }
  }

  if($guard_success_flag || strpos($game_option, 'quiz') !== false){ //���Ƚ��ϸ�Ƚ������˹Ԥ�����
    //������� or ������¼����
  }
  elseif(strpos($wolf_target_role, 'fox') !== false &&
	 strpos($wolf_target_role, 'child_fox') === false){ //���٤��褬�Ѥξ�翩�٤�ʤ�
    InsertSystemMessage($wolf_target_handle, 'FOX_EAT');
  }
  else{ //��Ҥ���Ƥʤ���п��٤�
    KillUser($wolf_target_uname); //���٤�줿�ͻ�˴
    InsertSystemMessage($wolf_target_handle, 'WOLF_KILLED'); //�����ƥ��å�����
    SaveLastWords($wolf_target_handle); //���٤�줿�ͤΰ����Ĥ�

    //���٤�줿�ͤ����ǼԤξ��
    if(strpos($wolf_target_role, 'poison') !== false){
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
      while(($wolf = mysql_fetch_assoc($sql_wolf_list)) !== false){
	array_push($wolf_list, $wolf);
      }

      $rand_key = array_rand($wolf_list, 1);
      $poison_target_array  = $wolf_list[$rand_key];
      $poison_target_uname  = $poison_target_array['uname'];
      $poison_target_handle = $poison_target_array['handle_name'];
      $poison_target_role   = $poison_target_array['role'];

      KillUser($poison_target_uname); //��˴����
      InsertSystemMessage($poison_target_handle, 'POISON_DEAD_night'); //�����ƥ��å�����
      SaveLastWords($poison_target_handle); //�������
      if(strpos($poison_target_role, 'lovers') !== false)
	LoversFollowed($poison_target_role); //�ǻष��ϵ�����ͤξ��
    }
    if(strpos($wolf_target_role, 'lovers') !== false)
      LoversFollowed($wolf_target_role); //���٤�줿�ͤ����ͤξ��
  }

  //�ꤤ�դΥ桼��̾���ϥ�ɥ�͡���ȡ��ꤤ�դ���¸���ꤤ�դ���ä��桼��̾����
  $sql_mage = mysql_query("SELECT user_entry.uname, user_entry.handle_name, user_entry.role,
				user_entry.live, vote.target_uname FROM vote, user_entry
				WHERE vote.room_no = $room_no AND user_entry.room_no = $room_no
				AND vote.date = $date AND vote.situation = 'MAGE_DO'
				AND vote.uname = user_entry.uname AND user_entry.user_no > 0");

  //�ꤤ�դοͿ�ʬ������
  for($i = 0; $i < $mage_count; $i++){
    $array = mysql_fetch_assoc($sql_mage);
    $mage_uname  = $array['uname'];
    $mage_handle = $array['handle_name'];
    $mage_role   = $array['role'];
    $mage_live   = $array['live'];
    $mage_target_uname = $array['target_uname'];

    //ľ���˻��Ǥ������ꤤ̵��
    if($mage_live == 'dead') continue;

    //�ꤤ�դ����줿�ͤΥϥ�ɥ�͡������¸���������
    $sql = mysql_query("SELECT handle_name, role, live FROM user_entry WHERE room_no = $room_no
			AND uname = '$mage_target_uname' AND user_no > 0");
    $array = mysql_fetch_assoc($sql);
    $mage_target_handle = $array['handle_name'];
    $mage_target_role   = $array['role'];
    $mage_target_live   = $array['live'];

    if($mage_target_live == 'live' && strpos($mage_target_role, 'fox') !== false &&
       strpos($mage_target_role, 'child_fox') === false){ //�Ѥ����줿���˴
      KillUser($mage_target_uname);
      InsertSystemMessage($mage_target_handle, 'FOX_DEAD');
      SaveLastWords($mage_target_handle); //���줿�Ѥΰ����Ĥ�
      if(strpos($mage_target_role, 'lovers') !== false)
	LoversFollowed($mage_target_role); //���줿�Ѥ����ͤξ��
    }

    //�ꤤ��̤����
    if(strpos($mage_role, 'soul_mage') !== false)
      $mage_result = GetMainRole($mage_target_role);
    else{
      if(strpos($mage_target_role, 'boss_wolf') !== false)
	$mage_result = 'human';
      elseif(strpos($mage_target_role, 'wolf') !== false)
	$mage_result = 'wolf';
      else
	$mage_result = 'human';
    }
    $sentence = $mage_handle . "\t" . $mage_target_handle . "\t" . $mage_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
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

  for($i=0; $i < $count; $i++){
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
  for($i=0; $i < $user_count; $i++){
    $array = mysql_fetch_assoc($sql_user);
    $this_user_no = $array['user_no'];
    $this_uname   = $array['uname'];
    $this_handle  = $array['handle_name'];
    $this_live    = $array['live'];
    $this_file    = $array['icon_filename'];
    $this_color   = $array['color'];

    if($i > 0 && ($i % 5) == 0) echo '</tr><tr>'."\n"; //5�Ĥ��Ȥ˲���
    if($this_live == 'live'){ //�����Ƥ���Х桼����������
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
    if($uname == 'dummy_boy') OutputVoteResult('�롧�����귯���ꤤ��̵���Ǥ�');
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_guard = (strpos($role, 'guard') !== false)){
    if($date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_cupid = (strpos($role, 'cupid') !== false)){
    if($date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('CUPID_DO');
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
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'submit_cupid_do';
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

//�����򿦤�ȴ���Ф����֤�
function GetMainRole($target_role){
  //�����򿦥ꥹ�� (strpos() ��Ȥ��Τ�Ƚ�������)
  //�����Ѥ� �� => �и�Ψ �� config ���������ΤϤɤ����ʡ�
  $role_list = array('human', 'boss_wolf', 'wolf', 'soul_mage', 'mage', 'necromancer',
		     'medium', 'fanatic_mad', 'mad', 'poison_guard', 'guard', 'common',
		     'child_fox', 'fox', 'poison', 'cupid', 'quiz');

  foreach($role_list as $this_role){
    if(strpos($target_role, $this_role) !== false) return $this_role;
  }
  return NULL;
}
?>
