<?php
require_once(dirname(__FILE__) . '/include/game_vote_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//���å���󳫻�
session_start();
$session_id = session_id();

//���������
if($_POST['situation'] == 'KICK_DO') EncodePostData(); //KICK �����б�
$RQ_ARGS = new RequestGameVote();
$room_no = $RQ_ARGS->room_no;

//PHP �ΰ��������
$php_argv = 'room_no=' . $room_no;
if($RQ_ARGS->auto_reload > 0) $php_argv .= '&auto_reload=' . $RQ_ARGS->auto_reload;
if($RQ_ARGS->play_sound) $php_argv .= '&play_sound=on';
if($RQ_ARGS->list_down)  $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">����� &amp; reload</a>';

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

$ROOM = new RoomDataSet($RQ_ARGS); //¼��������
$ROOM->system_time = TZTime(); //���߻�������

$USERS = new UserDataSet($RQ_ARGS); //�桼����������
$SELF  = $USERS->ByUname($uname); //��ʬ�ξ�������

if($ROOM->IsFinished()){ //������Ͻ�λ���ޤ���
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>������Ͻ�λ���ޤ���<br>'."\n" .
		     $back_url . '</div>');
}

if(! $SELF->IsLive()){ //��¸�԰ʳ���̵��
  OutputActionResult('��ɼ���顼',
		     '<div align="center">' .
		     '<a name="#game_top"></a>��¸�԰ʳ�����ɼ�Ǥ��ޤ���<br>'."\n" .
		     $back_url . '</div>');
}

if($RQ_ARGS->vote){ //��ɼ����
  if($ROOM->IsBeforeGame()){ //�����೫�� or Kick ��ɼ����
    if($RQ_ARGS->situation == 'GAMESTART'){
      VoteGameStart();
    }
    elseif($RQ_ARGS->situation == 'KICK_DO'){
      VoteKick($RQ_ARGS->target_handle_name);
    }
    else{ //�������褿����å����顼
      OutputActionResult('��ɼ���顼[�����೫������ɼ]',
			 '<div align="center">' .
			 '<a name="#game_top"></a>�ץ���२�顼�Ǥ���'.
			 '�����Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputActionResult('��ɼ���顼',
		       '<div align="center">' .
		       '<a name="#game_top"></a>��ɼ�����ꤷ�Ƥ�������<br>'."\n" .
		       $back_url . '</div>');
  }
  elseif($ROOM->IsDay()){ //��ν跺��ɼ����
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //�����ɼ����
    VoteNight();
  }
  else{ //�������褿����å����顼
    OutputActionResult('��ɼ���顼',
		       '<div align="center">' .
		       '<a name="#game_top"></a>�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($ROOM->IsBeforeGame()){ //�����೫�� or Kick ��ɼ�ڡ�������
  OutputVoteBeforeGame();
}
elseif($ROOM->IsDay()){ //��ν跺��ɼ�ڡ�������
  OutputVoteDay();
}
elseif($ROOM->IsNight()){ //�����ɼ�ڡ�������
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
  global $ROOM, $php_argv;

  OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[��ɼ]', 'game');
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="#game_top"></a>
<form method="POST" action="game_vote.php?${php_argv}#game_top">
<input type="hidden" name="vote" value="on">

EOF;
}

//�����೫����ɼ�ν���
function VoteGameStart(){
  global $room_no, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->IsDummyBoy() && ! $ROOM->IsQuiz()){
    OutputVoteResult('�����ॹ�����ȡ������귯����ɼ���פǤ�');
  }

  //��ɼ�Ѥߥ����å�
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = 0 " .
    "AND situation = 'GAMESTART' AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');

  LockTable(); //�ơ��֥����¾Ū��å�

  //��ɼ����
  $items = 'room_no, date, uname, situation';
  $values = "$room_no, 0, '{$SELF->uname}', 'GAMESTART'";
  if(InsertDatabase('vote', $items, $values) && mysql_query('COMMIT')){//������ߥå�
    AggregateVoteGameStart(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����೫����ɼ���׽���
function AggregateVoteGameStart(){
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $USERS;

  CheckSituation('GAMESTART');

  //��ɼ��������
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
    "AND date = 0 AND situation = 'GAMESTART'";
  $vote_count = FetchResult($query);

  //�����귯���Ѥʤ�����귯��ʬ��û�
  if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;

  //�桼����������
  $user_count = $USERS->GetUserCount();

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count < min(array_keys($GAME_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- �������롼���� --//
  //�������ꥪ�ץ����ξ�������
  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = $room_no");

  //����������ѿ��򥻥å�
  $uname_list        = $USERS->GetLivingUsers(); //�桼��̾������
  $role_list         = GetRoleList($user_count, $option_role); //�򿦥ꥹ�Ȥ����
  $fix_uname_list    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $fix_role_list     = array(); //�桼��̾���б��������
  $remain_uname_list = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ

  //�ե饰���å�
  $gerd  = $ROOM->IsOption('gerd');
  $chaos = $ROOM->IsOptionGroup('chaos'); //chaosfull ��ޤ�
  $quiz  = $ROOM->IsQuiz();

  //���顼��å�����
  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  if($ROOM->IsDummyBoy()){ //�����귯���򿦤����
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
	   strpos($this_role, 'poison') === false){
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
    unset($uname_list[array_search('dummy_boy', $uname_list)]); //�����귯����
  }

  //�桼���ꥹ�Ȥ������˼���
  shuffle($uname_list);

  //��˾�򿦤򻲾Ȥ��ư켡�����Ԥ�
  if($ROOM->IsOption('wish_role') && ! $chaos){ //����˾���ξ�� (����ϴ�˾��̵��)
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
  $test_role_list = array('blinder', 'speaker');
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
  $add_sub_role = 'perverseness';
  array_push($delete_role_list, $add_sub_role);
  for($i = 0; $i < $user_count; $i++){
    #if(mt_rand(1, 100) <= 70){
      $fix_role_list[$i] .= ' ' . $add_sub_role;
    #}
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
      $this_role = GetRandom($sub_role_list);
      $fix_role_list[$i] .= ' ' . $this_role;
      if($this_role == 'impatience'){ //û���ϰ�ͤ���
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif(strpos($option_role, 'perverseness') !== false){ //ŷ�ٵ�¼
    $this_role = 'perverseness';
    array_push($delete_role_list, $this_role);
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $this_role;
    }
  }

  if($chaos && strpos($option_role, 'no_sub_role') === false){
    //������ʥ����򿦤Υ����ɥꥹ�Ȥ����
    $sub_role_keys = array_keys($GAME_CONF->sub_role_list);
    // $sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //�ǥХå���
    // array_push($delete_role_list, 'earplug', 'speaker'); //�ǥХå���
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

  //�����೫�ϻ��֤�����
  $start_time = gmdate('Y/m/j (D) G:i:s', $ROOM->system_time);
  InsertSystemTalk('�����೫�ϡ�' . $start_time, $ROOM->system_time, 'night system', 1);

  //����DB�˹���
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $this_user = $USERS->ByUname($fix_uname_list[$i]);
    $this_role = $fix_role_list[$i];
    $this_user->ChangeRole($this_role);
    $this_role_list = explode(' ', $this_role);
    foreach($this_role_list as $this_role) $role_count_list[$this_role]++;
  }

  //���ꥹ������
  if($chaos){
    if(strpos($option_role, 'chaos_open_cast_camp') !== false){
      $sentence = MakeRoleNameList($role_count_list, 'camp');
    }
    elseif(strpos($option_role, 'chaos_open_cast_role') !== false){
      $sentence = MakeRoleNameList($role_count_list, 'role');
    }
    elseif(strpos($option_role, 'chaos_open_cast') !== false){
      $sentence = MakeRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = MakeRoleNameList($role_count_list);
  }
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system', 1);

  InsertSystemMessage('1', 'VOTE_TIMES', 1); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //��������Ϥ����ʤ꽪λ���Ƥ��ǽ������
  mysql_query('COMMIT'); //������ߥå�
}

//�������� Kick ��ɼ�ν��� ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $room_no, $ROOM, $SELF;

  //���顼�����å�
  CheckSituation('KICK_DO');
  if($target == '') OutputVoteResult('Kick����ɼ�����ꤷ�Ƥ�������');
  if($target == '�����귯') OutputVoteResult('Kick�������귯�ˤ���ɼ�Ǥ��ޤ���');
  if(($ROOM->IsQuiz() || $ROOM->IsOption('gm_login')) && $target == 'GM'){
    OutputVoteResult('Kick��GM �ˤ���ɼ�Ǥ��ޤ���'); //���� GM �б�
  }

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND user_entry.handle_name = '$target' AND vote.room_no = $room_no
			AND vote.uname = '{$SELF->uname}' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick��' . $target . ' �� Kick ��ɼ�Ѥ�');

  //��ʬ����ɼ�Ǥ��ޤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND handle_name ='$target' AND user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick����ʬ�ˤ���ɼ�Ǥ��ޤ���');

  LockTable(); //�ơ��֥����¾Ū��å�

  //�����೫�ϥ����å�
  if(FetchResult("SELECT day_night FROM room WHERE room_no = $room_no") != 'beforegame'){
    OutputVoteResult('Kick�����˥�����ϳ��Ϥ���Ƥ��ޤ�', true);
  }

  //�������åȤΥ桼��̾�����
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = $room_no
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick��'. $target . ' �Ϥ��Ǥ� Kick ����Ƥ��ޤ�', true);

  //��ɼ����
  $items = 'room_no, date, uname, target_uname, situation';
  $values = "$room_no, 0, '{$SELF->uname}', '$target_uname', 'KICK_DO'";
  $sql = InsertDatabase('vote', $items, $values);
  InsertSystemTalk("KICK_DO\t" . $target, $ROOM->system_time, '', 0, $SELF->uname); //��ɼ���ޤ�������

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
  global $GAME_CONF, $MESSAGE, $room_no, $ROOM, $SELF;

  CheckSituation('KICK_DO');

  //������ɼ�������ز�����ɼ���Ƥ��뤫
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = $room_no
			AND vote.room_no = $room_no AND vote.date = 0
			AND vote.situation = 'KICK_DO' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //��ɼ��������

  //������ʾ����ɼ�����ä������å����������귯�ξ��˽���
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy()) return $vote_count;

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

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$ROOM->system_time); //�ФƹԤä���å�����
  InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //��ɼ�ꥻ�å�����
  UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  mysql_query('COMMIT'); //������ߥå�
  return $vote_count;
}

//�����ɼ����
function VoteDay(){
  global $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  //��ɼ�Ѥߥ����å�
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
    "AND uname = '{$SELF->uname}' AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  $target = $USERS->ByID($RQ_ARGS->target_no); //��ɼ��Υ桼����������
  if($target->uname == '') OutputVoteResult('�跺����ɼ�褬���ꤵ��Ƥ��ޤ���');
  if($target->IsSelf()) OutputVoteResult('�跺����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  if(! $target->IsLive()) OutputVoteResult('�跺����¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');

  LockTable(); //�ơ��֥����¾Ū��å�

  //-- ��ɼ���� --//
  //�򿦤˱�����ɼ�������
  $vote_number = 1;
  if($SELF->IsRole('authority')){
    $vote_number++; //���ϼ�
  }
  elseif($SELF->IsRole('watcher', 'panelist')){
    $vote_number = 0; //˵�Ѽԡ�������
  }
  elseif($SELF->IsRole('random_voter')){
    $vote_number = mt_rand(0, 2); //��ʬ��
  }

  //��ɼ�������ƥ��å�����
  $items = 'room_no, date, uname, target_uname, vote_number, vote_times, situation';
  $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', '{$target->uname}', $vote_number, " .
    "{$RQ_ARGS->vote_times}, 'VOTE_KILL'";
  $sql = InsertDatabase('vote', $items, $values);
  $sentence = "VOTE_DO\t" . $target->handle_name;
  InsertSystemTalk($sentence, $ROOM->system_time, 'day system', '', $SELF->uname);

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    AggregateVoteDay(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����ɼ����
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  if($SELF->IsDummyBoy()) OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  switch($RQ_ARGS->situation){
  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('�롧��ϵ�ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage')) OutputVoteResult('�롧�ꤤ�հʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRoleGroup('voodoo_killer')) OutputVoteResult('�롧���ۻհʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRole('jammer_mad')) OutputVoteResult('�롧���ⶸ�Ͱʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->IsRole('trap_mad')) OutputVoteResult('�롧櫻հʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('lost_ability')) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    $not_type = ($RQ_ARGS->situation == 'TRAP_MAD_NOT_DO');
    break;

  case 'VOODOO_MAD_DO':
    if(! $SELF->IsRole('voodoo_mad')) OutputVoteResult('�롧���ѻհʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('�롧��Ͱʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('�롧����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('�롧�֥󲰰ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRole('poison_cat')) OutputVoteResult('�롧ǭ���ʳ�����ɼ�Ǥ��ޤ���');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    $not_type = ($RQ_ARGS->situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRole('assassin')) OutputVoteResult('�롧�Ż��԰ʳ�����ɼ�Ǥ��ޤ���');
    $not_type = ($RQ_ARGS->situation == 'ASSASSIN_NOT_DO');
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRole('mania')) OutputVoteResult('�롧���åޥ˥��ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsRole('child_fox')) OutputVoteResult('�롧�ҸѰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRole('cupid')) OutputVoteResult('�롧���塼�ԥåɰʳ�����ɼ�Ǥ��ޤ���');
    break;

  default:
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
    break;
  }
  CheckAlreadyVote($RQ_ARGS->situation); //��ɼ�Ѥߥ����å�

 //���顼��å������Υإå�
  $error_header = '�롧��ɼ�褬����������ޤ���<br>';

  if($not_type); //��ɼ����󥻥륿���פϲ��⤷�ʤ�
  elseif($SELF->IsRole('cupid')){  //���塼�ԥåɤξ�����ɼ����
    if(count($RQ_ARGS->target_no) != 2) OutputVoteResult('�롧����Ϳ������ͤǤϤ���ޤ���');
    $target_list = array();
    $self_shoot = false; //��ʬ����ե饰������
    foreach($RQ_ARGS->target_no as $this_target_no){
      //��ɼ���Υ桼���������
      $this_target = $USERS->ByID($this_target_no);

      //��¸�԰ʳ��ȿ����귯�ؤ���ɼ��̵��
      if(! $this_target->IsLive() || $this_target->IsDummyBoy()){
	OutputVoteResult('��¸�԰ʳ��ȿ����귯�ؤ���ɼ�Ǥ��ޤ���');
      }

      array_push($target_list, $this_target);
      if($this_target->IsSelf()) $self_shoot = true; //��ʬ������ɤ��������å�
    }

    //���ÿͿ�������å�
    if($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot && ! $self_shoot){
      OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
    }
  }
  else{ //���塼�ԥåɰʳ�����ɼ����
    $target = $USERS->ByID($RQ_ARGS->target_no); //��ɼ���Υ桼���������

    if($target->IsSelf() && ! $SELF->IsRole('trap_mad')){ //櫻հʳ��ϼ�ʬ�ؤ���ɼ��̵��
      OutputVoteResult($error_header . '��ʬ�ˤ���ɼ�Ǥ��ޤ���');
    }

    if($SELF->IsRole('poison_cat')){ //ǭ���ϻ�԰ʳ��ؤ���ɼ��̵��
      if(! $target->IsDead()){
	OutputVoteResult($error_header . '��԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
      }
    }
    elseif(! $target->IsLive()){
      OutputVoteResult($error_header . '��¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){ //��ϵ����ɼ
      if($SELF->IsWolf() && $target->IsWolf()){ //ϵƱ�Τؤ���ɼ��̵��
	OutputVoteResult($error_header . 'ϵƱ�Τˤ���ɼ�Ǥ��ޤ���');
      }

      if($ROOM->IsQuiz() && ! $target->IsDummyBoy()){ //������¼�� GM �ʳ�̵��
	OutputVoteResult($error_header . '������¼�Ǥ� GM �ʳ�����ɼ�Ǥ��ޤ���');
      }

      //�����귯���Ѥξ��ϡ������Ͽ����귯�ʳ�̵��
      if($ROOM->IsDummyBoy() && $ROOM->date == 1 && ! $target->IsDummyBoy()){
	OutputVoteResult($error_header . '�����귯���Ѥξ��ϡ������귯�ʳ�����ɼ�Ǥ��ޤ���');
      }
    }
  }

  LockTable(); //�ơ��֥����¾Ū��å�
  if($not_type){
    //��ɼ����
    $items = 'room_no, date, uname, vote_number, situation';
    $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
    InsertSystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    InsertSystemTalk($RQ_ARGS->situation, $ROOM->system_time, 'night system', '', $SELF->uname);
  }
  else{
    if($SELF->IsRole('cupid')){ // ���塼�ԥåɤν���
      $target_uname_str  = '';
      $target_handle_str = '';
      foreach($target_list as $this_target){
	if($target_uname_str != ''){
	  $target_uname_str  .= ' ';
	  $target_handle_str .= ' ';
	}
	$target_uname_str  .= $this_target->uname;
	$target_handle_str .= $this_target->handle_name;

	//�򿦤����ͤ��ɲ�
	$this_target->AddRole('lovers[' . strval($SELF->user_no) . ']');
      }
    }
    else{ // ���塼�ԥåɰʳ��ν���
      $target_uname_str  = $target->uname;
      $target_handle_str = $target->handle_name;
    }
    //��ɼ����
    $items = 'room_no, date, uname, target_uname, vote_number, situation';
    $values = "$room_no, {$ROOM->date}, '{$SELF->uname}', '$target_uname_str', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
    InsertSystemMessage($SELF->handle_name . "\t" . $target_handle_str, $RQ_ARGS->situation);
    $sentence = $RQ_ARGS->situation . "\t" . $target_handle_str;
    InsertSystemTalk($sentence, $ROOM->system_time, 'night system', '', $SELF->uname);
  }

  //��Ͽ����
  if($sql && mysql_query('COMMIT')){
    AggregateVoteNight(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else OutputVoteResult('�ǡ����١������顼', true);
}

//����������ɼ�ڡ�������
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $USERS, $room_no, $ROOM, $SELF, $php_argv;

  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_file   = $ICON_CONF->path . '/' . $this_user->icon_filename;
    $this_color  = $this_user->color;

    //HTML����
    echo <<<EOF
<td><label for="$this_handle">
<img src="$this_file" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if(! $this_user->IsDummyBoy() && $this_user->uname != $SELF->uname){
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
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$MESSAGE->submit_game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteDay(){
  global $MESSAGE, $ICON_CONF, $USERS, $room_no, $ROOM, $SELF, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ��������
  $vote_times = GetVoteTimes();

  //��ɼ�Ѥߤ��ɤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '{$SELF->uname}' AND date = {$ROOM->date}
			AND vote_times = $vote_times AND situation = 'VOTE_KILL'");
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
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_color  = $this_user->color;

    if($this_user->IsLive()) //�����Ƥ���Х桼����������
      $path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    else //���Ǥ�л�˴��������
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($this_user->IsLive() && $this_user->uname != $SELF->uname){
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
  global $GAME_CONF, $ICON_CONF, $MESSAGE, $room_no, $ROOM, $USERS, $SELF, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ�Ѥߥ����å�
  if($SELF->IsDummyBoy()) OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  if($role_wolf = $SELF->IsWolf()){
    CheckAlreadyVote('WOLF_EAT');
  }
  elseif($role_mage = $SELF->IsRoleGroup('mage')){
    CheckAlreadyVote('MAGE_DO');
  }
  elseif($role_voodoo_killer = $SELF->IsRole('voodoo_killer')){
    CheckAlreadyVote('VOODOO_KILLER_DO');
  }
  elseif($role_jammer_mad = $SELF->IsRole('jammer_mad')){
    CheckAlreadyVote('JAMMER_MAD_DO');
  }
  elseif($role_voodoo_mad = $SELF->IsRole('voodoo_mad')){
    CheckAlreadyVote('VOODOO_MAD_DO');
  }
  elseif($role_trap_mad = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('�롧����������֤ϤǤ��ޤ���');
    if($SELF->IsRole('lost_ability')) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_anti_voodoo = $SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('�롧��������ʧ���ϤǤ��ޤ���');
    CheckAlreadyVote('ANTI_VOODOO_DO');
  }
  elseif($role_guard = $SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = $SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('�롧���������ԤϤǤ��ޤ���');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_poison_cat = $SELF->IsRole('poison_cat')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����������ϤǤ��ޤ���');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
  }
  elseif($role_assassin = $SELF->IsRole('assassin')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ΰŻ��ϤǤ��ޤ���');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_mania = $SELF->IsRole('mania')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('MANIA_DO');
  }
  elseif($role_voodoo_fox = $SELF->IsRole('voodoo_fox')){
    CheckAlreadyVote('VOODOO_FOX_DO');
  }
  elseif($role_child_fox = $SELF->IsRole('child_fox')){
    CheckAlreadyVote('CHILD_FOX_DO');
  }
  elseif($role_cupid = $SELF->IsRole('cupid')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('CUPID_DO');
    $cupid_self_shoot = ($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot);
  }
  else OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');

  //�����귯���� or ������¼�λ��Ͽ����귯�����������٤ʤ�
  if($role_wolf && ($ROOM->IsDummyBoy() && $ROOM->date == 1 || $ROOM->IsQuiz())){
    //�����귯�Υ桼������
    $this_rows = array(1 => $USERS->rows[1]); //dummy_boy = 1�֤��ݾڤ���Ƥ��롩
  }
  else{
    $this_rows = $USERS->rows;
  }
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";
  foreach($this_rows as $this_user_no => $this_user){
    $this_color = $this_user->color;
    $this_wolf  = ($role_wolf && $this_user->IsWolf());

    if($this_user->IsLive() || $role_poison_cat){ //ǭ���ϻ�˴��������ˤ��ʤ�
      if($this_wolf) //ϵƱ�Τʤ�ϵ��������
	$path = $ICON_CONF->wolf;
      else //�����Ƥ���Х桼����������
	$path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    }
    else{
      $path = $ICON_CONF->dead; //���Ǥ�л�˴��������
    }

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_user->handle_name<br>

EOF;

    if($role_cupid){
      if(! $this_user->IsDummyBoy()){
	$checked = (($cupid_self_shoot && $this_user->IsSelf()) ? ' checked' : '');
	echo '<input type="checkbox" id="' . $this_user_no . '" name="target_no[]" value="' .
	  $this_user_no . '"' . $checked . '>'."\n";
      }
    }
    elseif($role_poison_cat){
      if($this_user->IsDead() && ! $this_user->IsSelf() && ! $this_user->IsDummyBoy()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($role_trap_mad){
      if($this_user->IsLive()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_user->IsLive() && ! $this_user->IsSelf() && ! $this_wolf){
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
  elseif($role_voodoo_killer){
    $type   = 'VOODOO_KILLER_DO';
    $submit = 'submit_voodoo_killer_do';
  }
  elseif($role_jammer_mad){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'submit_jammer_do';
  }
  elseif($role_voodoo_mad){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'submit_voodoo_do';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'submit_trap_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'submit_trap_not_do';
  }
  elseif($role_anti_voodoo){
    $type   = 'ANTI_VOODOO_DO';
    $submit = 'submit_anti_voodoo_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'submit_guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'submit_reporter_do';
  }
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'submit_revive_do';
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'submit_revive_not_do';
  }
  elseif($role_assassin){
    $type   = 'ASSASSIN_DO';
    $submit = 'submit_assassin_do';
    $not_type   = 'ASSASSIN_NOT_DO';
    $not_submit = 'submit_assassin_not_do';
  }
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'submit_mania_do';
  }
  elseif($role_voodoo_fox){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'submit_voodoo_do';
  }
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
    $submit = 'submit_mage_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'submit_cupid_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$MESSAGE->$submit}"></td></form>

EOF;

  if($not_type != ''){
    echo <<<EOF
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
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

//��ɼ������������äƤ��뤫�����å�
function CheckDayNight(){
  global $ROOM, $SELF;

  if($ROOM->day_night != $SELF->last_load_day_night){
    OutputVoteResult('��äƥ���ɤ��Ƥ�������');
  }
}

//��ɼ�Ѥߥ����å�
function CheckAlreadyVote($situation, $not_situation = ''){
  if(CheckSelfVoteNight($situation, $not_situation)) OutputVoteResult('�롧��ɼ�Ѥ�');
}
?>
