<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ICON_CONF');

//-- �ǡ������� --//
$INIT_CONF->LoadRequest('RequestGameVote'); //���������

//PHP �ΰ��������
$php_argv = 'room_no=' . $RQ_ARGS->room_no;
if($RQ_ARGS->auto_reload > 0) $php_argv .= '&auto_reload=' . $RQ_ARGS->auto_reload;
if($RQ_ARGS->play_sound) $php_argv .= '&play_sound=on';
if($RQ_ARGS->list_down)  $php_argv .= '&list_down=on';
$back_url = '<a href="game_up.php?' . $php_argv . '#game_top">����� &amp; reload</a>';

$DB_CONF->Connect(); //DB ��³
$SESSION->Certify(); //���å����ǧ��

$ROOM =& new Room($RQ_ARGS); //¼��������
$ROOM->system_time = TZTime(); //���߻�������

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF = $USERS->BySession(); //��ʬ�ξ�������

if($ROOM->IsFinished()){ //�����ཪλ
  OutputActionResult('��ɼ���顼',
		     '<div align="center"><a name="#game_top"></a>' .
		     '������Ͻ�λ���ޤ���<br>'."\n" . $back_url . '</div>');
}

if(! $SELF->IsLive()){ //��¸�԰ʳ���̵��
  OutputActionResult('��ɼ���顼',
		     '<div align="center"><a name="#game_top"></a>' .
		     '��¸�԰ʳ�����ɼ�Ǥ��ޤ���<br>'."\n" . $back_url . '</div>');
}

//-- �ᥤ��롼���� --//
if($RQ_ARGS->vote){ //��ɼ����
  if($ROOM->IsBeforeGame()){ //�����೫�� or Kick ��ɼ����
    if($RQ_ARGS->situation == 'GAMESTART'){
      $INIT_CONF->LoadClass('CAST_CONF'); //�����������
      VoteGameStart();
    }
    elseif($RQ_ARGS->situation == 'KICK_DO'){
      VoteKick($RQ_ARGS->target_handle_name);
    }
    else{ //�������褿����å����顼
      OutputActionResult('��ɼ���顼[�����೫������ɼ]',
			 '<div align="center"><a name="#game_top"></a>' .
			 '�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
			 $back_url . '</div>');
    }
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputActionResult('��ɼ���顼',
		       '<div align="center"><a name="#game_top"></a>' .
		       '��ɼ�����ꤷ�Ƥ�������<br>'."\n" . $back_url . '</div>');
  }
  elseif($ROOM->IsDay()){ //��ν跺��ɼ����
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //�����ɼ����
    VoteNight();
  }
  else{ //�������褿����å����顼
    OutputActionResult('��ɼ���顼',
		       '<div align="center"><a name="#game_top"></a>' .
		       '�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ�������<br>'."\n" .
		       $back_url . '</div>');
  }
}
elseif($ROOM->IsBeforeGame()){ //�����೫�� or Kick ��ɼ�ڡ�������
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteBeforeGame();
}
elseif($ROOM->IsDay()){ //��ν跺��ɼ�ڡ�������
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteDay();
}
elseif($ROOM->IsNight()){ //�����ɼ�ڡ�������
  $INIT_CONF->LoadClass('VOTE_MESS');
  OutputVoteNight();
}
else{ //��ɼ�Ѥ� //�������褿����å����顼����ʤ����ʡ�
  OutputActionResult('��ɼ���顼',
		     '<div align="center"><a name="#game_top"></a>' .
		     '������ɼ����Ƥ���ޤ�<br>'."\n" . $back_url . '</div>');
}

$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
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
  global $GAME_CONF, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->IsDummyBoy() && ! $ROOM->IsQuiz()){
    if($GAME_CONF->power_gm){ //���� GM �ˤ�붯���������Ƚ���
      LockTable(); //�ơ��֥����¾Ū��å�
      if(AggregateVoteGameStart(true)){
	OutputVoteResult('�����೫��', true);
      }
      else{
	OutputVoteResult('�����ॹ�����ȡ����ϿͿ���ã���Ƥ��ޤ���', true);
      }
    }
    else{
      OutputVoteResult('�����ॹ�����ȡ������귯����ɼ���פǤ�');
    }
  }

  //��ɼ�Ѥߥ����å�
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = 0 " .
    "AND situation = 'GAMESTART' AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');
  LockTable(); //�ơ��֥����¾Ū��å�

  //��ɼ����
  $items = 'room_no, date, uname, situation';
  $values = "{$ROOM->id}, 0, '{$SELF->uname}', 'GAMESTART'";
  if(InsertDatabase('vote', $items, $values) && mysql_query('COMMIT')){//������ߥå�
    AggregateVoteGameStart(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�����೫����ɼ���׽���
function AggregateVoteGameStart($force_start = false){
  global $GAME_CONF, $CAST_CONF, $MESSAGE, $ROOM, $USERS;

  CheckSituation('GAMESTART');

  //�桼����������
  $user_count = $USERS->GetUserCount();

  //��ɼ��������
  if($force_start){ //�������ϥ⡼�ɻ��ϥ����å�
    $vote_count = $user_count;
  }
  else{
    $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} " .
      "AND date = 0 AND situation = 'GAMESTART'";
    $vote_count = FetchResult($query);

    //�����귯���Ѥʤ�����귯��ʬ��û�
    if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;
  }

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count < min(array_keys($CAST_CONF->role_list)) || $vote_count != $user_count) return false;

  //-- �������롼���� --//
  //�������ꥪ�ץ����ξ�������
  $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = {$ROOM->id}");

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
	foreach($CAST_CONF->disable_dummy_boy_role_list as $this_disable_role){
	  if(strpos($this_role, $this_disable_role) !== false){
	    array_push($role_list, $this_role); //����ꥹ�Ȥ��������᤹
	    continue 2;
	  }
	}
	array_push($fix_role_list, $this_role);
	break;
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
  if($ROOM->IsOption('wish_role')){ //����˾���ξ��
    foreach($uname_list as $this_uname){
      do{
	$this_role = $USERS->GetRole($this_uname); //��˾�򿦤����
	if($this_role  == '' || mt_rand(1, 100) > $CAST_CONF->wish_role_rate) break;
	$this_fit_role = $this_role;

	if($chaos){ //����⡼��
	  $this_fit_role_list = array();
	  foreach($role_list as $this_fit_role){
	    if($this_role == DistinguishRoleGroup($this_fit_role)){
	      $this_fit_role_list[] = $this_fit_role;
	    }
	  }
	  $this_fit_role = GetRandom($this_fit_role_list);
	}
	$role_key = array_search($this_fit_role, $role_list); //��˾�򿦤�¸�ߥ����å�
	if($role_key === false) break;

	//��˾�򿦤�����з���
	array_push($fix_uname_list, $this_uname);
	array_push($fix_role_list, $this_fit_role);
	unset($role_list[$role_key]);
	continue 2;
      }while(false);

      //��ޤ�ʤ��ä�����̤����ꥹ�ȹԤ�
      array_push($remain_uname_list, $this_uname);
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
  //��꿶���оݳ��򿦤Υꥹ��
  $delete_role_list = array('lovers', 'copied', 'panelist', 'mind_read', 'mind_evoke',
			    'mind_receiver', 'mind_friend');

  //�����򿦥ƥ�����
  /*
  $test_role_list = array('mind_open');
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
  foreach($now_sub_role_list as $role){
    if(strpos($option_role, $role) !== false && $user_count >= $CAST_CONF->$role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $role;
    }
  }
  if(strpos($option_role, 'liar') !== false){ //ϵ��ǯ¼
    $role = 'liar';
    array_push($delete_role_list, $role);
    for($i = 0; $i < $user_count; $i++){ //�����˰����Ψ��ϵ��ǯ��Ĥ���
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $role;
    }
  }
  if(strpos($option_role, 'gentleman') !== false){ //�»Ρ��ʽ�¼
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //���������̤˱����ƿ»Τ��ʽ���Ĥ���
      $role = $sub_role_list[$USERS->ByUname($fix_uname_list[$i])->sex];
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if(strpos($option_role, 'sudden_death') !== false){ //�����μ�¼
    $sub_role_list = $GAME_CONF->sub_role_group_list['sudden-death'];
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //�����˥���å���Ϥ򲿤��Ĥ���
      $role = GetRandom($sub_role_list);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience'){ //û���ϰ�ͤ���
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif(strpos($option_role, 'perverseness') !== false){ //ŷ�ٵ�¼
    $role = 'perverseness';
    array_push($delete_role_list, $role);
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $role;
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
      if($rand_keys_index > $user_count - 1) break; //$rand_keys_index �� 0 ����
      // if(strpos($key, 'voice') !== false || $key == 'earplug') continue; //���Ѳ����򥹥��å�
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $key;
    }
  }
  if($quiz){ //������¼
    $role = 'panelist';
    for($i = 0; $i < $user_count; $i++){ //����԰ʳ��˲����Ԥ�Ĥ���
      if($fix_uname_list[$i] != 'dummy_boy') $fix_role_list[$i] .= ' ' . $role;
    }
  }

  //�ǥХå���
  /*
  PrintData($option_role);
  PrintData($fix_uname_list);
  PrintData($fix_role_list);
  DeleteVote(); return false;
  */

  //�����೫��
  mysql_query("UPDATE room SET status = 'playing', date = 1, day_night = 'night',
		start_time = NOW() WHERE room_no = {$ROOM->id}");
  DeleteVote(); //���ޤǤ���ɼ���������

  //����DB�˹���
  $role_count_list = array();
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $USERS->ByUname($fix_uname_list[$i])->ChangeRole($role);
    $role_list = explode(' ', $role);
    foreach($role_list as $role) $role_count_list[$role]++;
  }

  //���ꥹ������
  if($chaos){
    if(strpos($option_role, 'chaos_open_cast_camp') !== false){
      $sentence = GenerateRoleNameList($role_count_list, 'camp');
    }
    elseif(strpos($option_role, 'chaos_open_cast_role') !== false){
      $sentence = GenerateRoleNameList($role_count_list, 'role');
    }
    elseif(strpos($option_role, 'chaos_open_cast') !== false){
      $sentence = GenerateRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = GenerateRoleNameList($role_count_list);
  }
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system', 1);

  InsertSystemMessage('1', 'VOTE_TIMES', 1); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //��������Ϥ����ʤ꽪λ���Ƥ��ǽ������
  mysql_query('COMMIT'); //������ߥå�
  return true;
}

//�������� Kick ��ɼ�ν��� ($target : HN)
function VoteKick($target){
  global $GAME_CONF, $ROOM, $SELF;

  //���顼�����å�
  CheckSituation('KICK_DO');
  if($target == '') OutputVoteResult('Kick����ɼ�����ꤷ�Ƥ�������');
  if($target == '�����귯') OutputVoteResult('Kick�������귯�ˤ���ɼ�Ǥ��ޤ���');
  if(($ROOM->IsQuiz() || $ROOM->IsOption('gm_login')) && $target == 'GM'){
    OutputVoteResult('Kick��GM �ˤ���ɼ�Ǥ��ޤ���'); //���� GM �б�
  }

  //��ɼ�Ѥߥ����å�
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = {$ROOM->id}
			AND user_entry.handle_name = '$target' AND vote.room_no = {$ROOM->id}
			AND vote.uname = '{$SELF->uname}' AND vote.date = 0 AND vote.situation = 'KICK_DO'
			AND user_entry.uname = vote.target_uname AND user_entry.user_no > 0");
  if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick��' . $target . ' �� Kick ��ɼ�Ѥ�');

  if(! $GAME_CONF->self_kick){ //��ʬ�ؤ� KICK
    $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id}
			AND uname = '{$SELF->uname}' AND handle_name ='$target' AND user_no > 0");
    if(mysql_result($sql, 0, 0) != 0) OutputVoteResult('Kick����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  }

  LockTable(); //�ơ��֥����¾Ū��å�

  //�����೫�ϥ����å�
  if(FetchResult("SELECT day_night FROM room WHERE room_no = {$ROOM->id}") != 'beforegame'){
    OutputVoteResult('Kick�����˥�����ϳ��Ϥ���Ƥ��ޤ�', true);
  }

  //�������åȤΥ桼��̾�����
  $sql = mysql_query("SELECT uname FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $array = mysql_fetch_assoc($sql);
  $target_uname = $array['uname'];
  if($target_uname == '') OutputVoteResult('Kick��'. $target . ' �Ϥ��Ǥ� Kick ����Ƥ��ޤ�', true);

  //��ɼ����
  $items = 'room_no, date, uname, target_uname, situation';
  $values = "{$ROOM->id}, 0, '{$SELF->uname}', '$target_uname', 'KICK_DO'";
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
  global $GAME_CONF, $MESSAGE, $ROOM, $SELF;

  CheckSituation('KICK_DO');

  //������ɼ�������ز�����ɼ���Ƥ��뤫
  $sql = mysql_query("SELECT COUNT(vote.uname) FROM user_entry, vote
			WHERE user_entry.room_no = {$ROOM->id}
			AND vote.room_no = {$ROOM->id} AND vote.date = 0
			AND vote.situation = 'KICK_DO' AND vote.target_uname = user_entry.uname
			AND user_entry.handle_name = '$target' AND user_entry.user_no > 0");
  $vote_count = mysql_result($sql, 0, 0); //��ɼ��������

  //������ʾ����ɼ�����ä� / ���å����������귯 / ���� KICK ��ͭ���ξ��˽���
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy() &&
     ! ($GAME_CONF->self_kick && $target == $SELF->handle_name)){
    return $vote_count;
  }

  //�桼����������
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = {$ROOM->id} AND user_no > 0");
  $user_count = mysql_result($sql, 0, 0);

  //Kick ����ͤ� user_no �����
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick ���줿�ͤϻ�˴��user_no �� -1�����å���� ID ��������
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = {$ROOM->id} AND handle_name = '$target' AND user_no > 0");

  // //�����ξ�硢�罸����᤹ //���ߤ���������ɽ�����Ѥ��ʤ��ΤǤ��ν��������פ���ʤ����ʡ�
  // mysql_query("UPDATE room SET status = 'waiting', day_night = 'beforegame' WHERE room_no = {$ROOM->id}");

  //���å�����ƶ���������ͤ��
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = {$ROOM->id} AND user_no = $next");
  }

  InsertSystemTalk($target . $MESSAGE->kick_out, ++$ROOM->system_time); //�ФƹԤä���å�����
  InsertSystemTalk($MESSAGE->vote_reset, ++$ROOM->system_time); //��ɼ�ꥻ�å�����
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  mysql_query('COMMIT'); //������ߥå�
  return $vote_count;
}

//�����ɼ����
function VoteDay(){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  //��ɼ�Ѥߥ����å�
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //������ɼ�Ԥ����
  $target = $USERS->ByReal($RQ_ARGS->target_no); //��ɼ��Υ桼����������
  if($target->uname == '') OutputVoteResult('�跺����ɼ�褬���ꤵ��Ƥ��ޤ���');
  if($target->IsSelf())    OutputVoteResult('�跺����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  if(! $target->IsLive())  OutputVoteResult('�跺����¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');

  LockTable(); //�ơ��֥����¾Ū��å�

  //-- ��ɼ���� --//
  //�򿦤˱�������ɼ��������
  $vote_number = 1;
  if($SELF->IsRoleGroup('elder')) $vote_number++; //ĹϷ�� (�ᥤ����)
  if($virtual_self->IsRole('authority')){ //���ϼ�
    $vote_number++;
  }
  elseif($virtual_self->IsRole('watcher', 'panelist')){ //˵�Ѽԡ�������
    $vote_number = 0;
  }
  elseif($virtual_self->IsRole('random_voter')){ //��ʬ��
    $vote_number = mt_rand(0, 2);
  }

  //��ɼ�������ƥ��å�����
  $items = 'room_no, date, uname, target_uname, vote_number, vote_times, situation';
  $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', '{$target->uname}', {$vote_number}, " .
    "{$RQ_ARGS->vote_times}, 'VOTE_KILL'";
  $sql = InsertDatabase('vote', $items, $values);
  $sentence = "VOTE_DO\t" . $USERS->GetHandleName($target->uname, true);
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
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  if($SELF->IsDummyBoy()) OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  switch($RQ_ARGS->situation){
  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('�롧��ϵ�ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage')) OutputVoteResult('�롧�ꤤ�հʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRole('voodoo_killer')) OutputVoteResult('�롧���ۻհʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRole('jammer_mad')) OutputVoteResult('�롧���ưʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'VOODOO_MAD_DO':
    if(! $SELF->IsRole('voodoo_mad')) OutputVoteResult('�롧���ѻհʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'DREAM_EAT':
    if(! $SELF->IsRole('dream_eater_mad')) OutputVoteResult('�롧�Ӱʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->IsRole('trap_mad')) OutputVoteResult('�롧櫻հʳ�����ɼ�Ǥ��ޤ���');
    if(! $SELF->IsActive()) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    $not_type = ($RQ_ARGS->situation == 'TRAP_MAD_NOT_DO');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('�롧��Ͱʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('�롧�֥󲰰ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('�롧����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRoleGroup('cat', 'revive_fox')) OutputVoteResult('�롧ǭ������Ѱʳ�����ɼ�Ǥ��ޤ���');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('�롧��Ѥ������ϰ��٤����Ǥ��ޤ���');
    }
    $not_type = ($RQ_ARGS->situation == 'POISON_CAT_NOT_DO');
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRole('assassin')) OutputVoteResult('�롧�Ż��԰ʳ�����ɼ�Ǥ��ޤ���');
    $not_type = ($RQ_ARGS->situation == 'ASSASSIN_NOT_DO');
    break;

  case 'MIND_SCANNER_DO':
    if(! $SELF->IsRoleGroup('scanner')) OutputVoteResult('�롧���Ȥ�ʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsRole('child_fox')) OutputVoteResult('�롧�ҸѰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'dummy_chiroptera')){
      OutputVoteResult('�롧���塼�ԥåɰʳ�����ɼ�Ǥ��ޤ���');
    }
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRoleGroup('mania')) OutputVoteResult('�롧���åޥ˥��ʳ�����ɼ�Ǥ��ޤ���');
    break;

  default:
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
    break;
  }
  CheckAlreadyVote($RQ_ARGS->situation); //��ɼ�Ѥߥ����å�

 //���顼��å������Υإå�
  $error_header = '�롧��ɼ�褬����������ޤ���<br>';

  if($not_type); //��ɼ����󥻥륿���פϲ��⤷�ʤ�
  elseif($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera')){  //���塼�ԥåɷ�
    if(count($RQ_ARGS->target_no) != 2) OutputVoteResult('�롧����Ϳ�����ͤǤϤ���ޤ���');
    $target_list = array();
    $self_shoot = false; //��ʬ����ե饰������
    foreach($RQ_ARGS->target_no as $this_target_no){
      $this_target = $USERS->ByID($this_target_no); //��ɼ��Υ桼����������

      //��¸�԰ʳ��ȿ����귯�ؤ���ɼ��̵��
      if(! $this_target->IsLive() || $this_target->IsDummyBoy()){
	OutputVoteResult('��¸�԰ʳ��ȿ����귯�ؤ���ɼ�Ǥ��ޤ���');
      }

      $target_list[] = $this_target;
      $self_shoot |= $this_target->IsSelf(); //��ʬ���Ƚ��
    }

    if(! $self_shoot){ //��ʬ����Ǥ�̵����������Υ������ǥ��顼���֤�
      if($SELF->IsRole('self_cupid', 'dummy_chiroptera')){ //�ᰦ��
	OutputVoteResult($error_header . '�ᰦ�Ԥ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
      }
      elseif($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot){ //���ÿͿ�
	OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
      }
    }
  }
  else{ //���塼�ԥåɷϰʳ�
    $target = $USERS->ByID($RQ_ARGS->target_no); //��ɼ��Υ桼����������
    $virtual_live = $USERS->IsVirtualLive($target->user_no); //����Ū�������Ƚ��

    if($target->IsSelf() && ! $SELF->IsRole('trap_mad')){ //櫻հʳ��ϼ�ʬ�ؤ���ɼ��̵��
      OutputVoteResult($error_header . '��ʬ�ˤ���ɼ�Ǥ��ޤ���');
    }

    if($SELF->IsRoleGroup('cat', 'revive_fox')){ //����ǽ�ϼԤϻ�԰ʳ��ؤ���ɼ��̵��
      if($virtual_live){
	OutputVoteResult($error_header . '��԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
      }
    }
    elseif(! $virtual_live){
      OutputVoteResult($error_header . '��¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){ //��ϵ����ɼ
      //��֤���ʬ���äƤ���ϵƱ�Τؤ���ɼ��̵��
      if($SELF->IsWolf(true) && $USERS->ByReal($target->user_no)->IsWolf(true)){
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
    $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', 1, '{$RQ_ARGS->situation}'";
    $sql = InsertDatabase('vote', $items, $values);
    InsertSystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    InsertSystemTalk($RQ_ARGS->situation, $ROOM->system_time, 'night system', '', $SELF->uname);
  }
  else{
    if($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera')){ //���塼�ԥåɷϤν���
      $target_uname_str  = '';
      $target_handle_str = '';
      foreach($target_list as $this_target){
	if($target_uname_str != ''){
	  $target_uname_str  .= ' ';
	  $target_handle_str .= ' ';
	}
	$target_uname_str  .= $this_target->uname;
	$target_handle_str .= $this_target->handle_name;

	if($SELF->IsRole('dummy_chiroptera')){ //̴�ᰦ�Ԥν���
	  if(! $this_target->IsSelf()){ //��ʬ�ʳ��ˤϲ��⤷�ʤ�
	    $main_role = 'dummy_chiroptera';
	    $change_role = $main_role . '[' . strval($this_target->user_no) . ']';
	    $SELF->ReplaceRole($main_role, $change_role);
	  }
	  continue;
	}

	//�򿦤����ͤ��ɲ�
	$add_role = 'lovers[' . strval($SELF->user_no) . ']';
	if($SELF->IsRole('self_cupid') && ! $this_target->IsSelf()){ //�ᰦ�Ԥʤ����˼����Ԥ��ɲ�
	  $add_role .= ' mind_receiver['. strval($SELF->user_no) . ']';
	}
	elseif($SELF->IsRole('mind_cupid')){ //�����ʤ鶦�ļԤ��ɲ�
	  $add_role .= ' mind_friend['. strval($SELF->user_no) . ']';
	  if(! $self_shoot){//¾�ͷ���ʤ��ܿͤ˼����Ԥ��ɲä���
	    $SELF->AddRole('mind_receiver[' . strval($this_target->user_no) . ']');
	  }
	}
	/*
	//�����ؤ�QP�ʤ鼫ʬ�������ؤ���
	elseif($SELF->IsRole('possessed_cupid') && ! $this_target->IsSelf()){
	  $SELF->AddRole('possessed_target[2-' . $this_target->user_no . '] ' .
			 'possessed[2-' . $this_target->user_no . ']');
	  $this_target->AddRole('possessed_target[2-' . $SELF->user_no . '] ' .
				'possessed[2-' . $SELF->user_no . ']');
	}
	*/
	$this_target->AddRole($add_role);
      }
    }
    else{ // ���塼�ԥåɰʳ��ν���
      $target_uname_str  = $USERS->ByReal($target->user_no)->uname;
      $target_handle_str = $target->handle_name;
    }
    //��ɼ����
    $items = 'room_no, date, uname, target_uname, situation';
    $values = "{$ROOM->id}, {$ROOM->date}, '{$SELF->uname}', '$target_uname_str', '{$RQ_ARGS->situation}'";
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
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

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

    if(! $this_user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $this_user->IsSelf())){
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
<td><input type="submit" value="{$VOTE_MESS->kick_do}"></form></td>
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$VOTE_MESS->game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteDay(){
  global $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

  //��ɼ������������äƤ��뤫�����å�
  CheckDayNight();

  //��ɼ��������
  $vote_times = GetVoteTimes();

  //��ɼ�Ѥߥ����å�
  $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$ROOM->id} AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = $vote_times AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="$vote_times">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //������ɼ�Ԥ����
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $this_user_no => $this_user){
    $this_handle = $this_user->handle_name;
    $this_color  = $this_user->color;
    $this_live   = $USERS->IsVirtualLive($this_user_no);
    if($this_live) //�����Ƥ���Х桼����������
      $path = $ICON_CONF->path . '/' . $this_user->icon_filename;
    else //���Ǥ�л�˴��������
      $path = $ICON_CONF->dead;

    echo <<<EOF
<td><label for="$this_user_no">
<img src="$path" width="$width" height="$height" style="border-color: $this_color;">
<font color="$this_color">��</font>$this_handle<br>

EOF;

    if($this_live && ! $this_user->IsSame($virtual_self->uname)){
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
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $ROOM, $USERS, $SELF, $php_argv;

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
  elseif($role_dream_eater_mad = $SELF->IsRole('dream_eater_mad')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ν���ϤǤ��ޤ���');
    CheckAlreadyVote('DREAM_EAT');
  }
  elseif($role_trap_mad = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('�롧����������֤ϤǤ��ޤ���');
    if(! $SELF->IsActive()) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    CheckAlreadyVote('TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
  }
  elseif($role_guard = $SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    CheckAlreadyVote('GUARD_DO');
  }
  elseif($role_reporter = $SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('�롧���������ԤϤǤ��ޤ���');
    CheckAlreadyVote('REPORTER_DO');
  }
  elseif($role_anti_voodoo = $SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('�롧��������ʧ���ϤǤ��ޤ���');
    CheckAlreadyVote('ANTI_VOODOO_DO');
  }
  elseif($role_poison_cat = $SELF->IsRoleGroup('cat', 'revive_fox')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����������ϤǤ��ޤ���');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('�롧��Ѥ������ϰ��٤����Ǥ��ޤ���');
    }
    CheckAlreadyVote('POISON_CAT_DO', 'POISON_CAT_NOT_DO');
  }
  elseif($role_assassin = $SELF->IsRole('assassin')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ΰŻ��ϤǤ��ޤ���');
    CheckAlreadyVote('ASSASSIN_DO', 'ASSASSIN_NOT_DO');
  }
  elseif($role_mind_scanner = $SELF->IsRoleGroup('scanner')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    CheckAlreadyVote('MIND_SCANNER_DO');
  }
  elseif($role_voodoo_fox = $SELF->IsRole('voodoo_fox')){
    CheckAlreadyVote('VOODOO_FOX_DO');
  }
  elseif($role_child_fox = $SELF->IsRole('child_fox')){
    CheckAlreadyVote('CHILD_FOX_DO');
  }
  elseif($role_cupid = ($SELF->IsRoleGroup('cupid') || $SELF->IsRole('dummy_chiroptera'))){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('CUPID_DO');
    $cupid_self_shoot = ($SELF->IsRole('self_cupid', 'dummy_chiroptera') ||
			 $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot);
  }
  elseif($role_mania = $SELF->IsRoleGroup('mania')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    CheckAlreadyVote('MANIA_DO');
  }
  else OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');

  //�����귯���� or ������¼�λ��Ͽ����귯�����������٤ʤ�
  if($role_wolf && (($ROOM->IsDummyBoy() && $ROOM->date == 1) || $ROOM->IsQuiz())){
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
    $this_live = $USERS->IsVirtualLive($this_user_no);
    $this_wolf = ($role_wolf && ! $SELF->IsRole('silver_wolf') &&
		  $USERS->ByReal($this_user_no)->IsWolf(true));

    if($this_live || $role_poison_cat){ //ǭ���ϻ�˴��������ˤ��ʤ�
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
      if(! $this_live && ! $this_user->IsSelf() && ! $this_user->IsDummyBoy()){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($role_trap_mad){
      if($this_live){
	echo '<input type="radio" id="' . $this_user_no . '" name="target_no" value="' .
	  $this_user_no . '">'."\n";
      }
    }
    elseif($this_live && ! $this_user->IsSelf() && ! $this_wolf){
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
    $submit = 'wolf_eat';
  }
  elseif($role_mage){
    $type   = 'MAGE_DO';
    $submit = 'mage_do';
  }
  elseif($role_voodoo_killer){
    $type   = 'VOODOO_KILLER_DO';
    $submit = 'voodoo_killer_do';
  }
  elseif($role_jammer_mad){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'jammer_do';
  }
  elseif($role_voodoo_mad){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'voodoo_do';
  }
  elseif($role_dream_eater_mad){
    $type   = 'DREAM_EAT';
    $submit = 'dream_eat';
  }
  elseif($role_trap_mad){
    $type   = 'TRAP_MAD_DO';
    $submit = 'trap_do';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $not_submit = 'trap_not_do';
  }
  elseif($role_guard){
    $type   = 'GUARD_DO';
    $submit = 'guard_do';
  }
  elseif($role_reporter){
    $type   = 'REPORTER_DO';
    $submit = 'reporter_do';
  }
  elseif($role_anti_voodoo){
    $type   = 'ANTI_VOODOO_DO';
    $submit = 'anti_voodoo_do';
  }
  elseif($role_poison_cat){
    $type   = 'POISON_CAT_DO';
    $submit = 'revive_do';
    $not_type   = 'POISON_CAT_NOT_DO';
    $not_submit = 'revive_not_do';
  }
  elseif($role_assassin){
    $type   = 'ASSASSIN_DO';
    $submit = 'assassin_do';
    $not_type   = 'ASSASSIN_NOT_DO';
    $not_submit = 'assassin_not_do';
  }
  elseif($role_mind_scanner){
    $type   = 'MIND_SCANNER_DO';
    $submit = 'mind_scanner_do';
  }
  elseif($role_voodoo_fox){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'voodoo_do';
  }
  elseif($role_child_fox){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($role_cupid){
    $type   = 'CUPID_DO';
    $submit = 'cupid_do';
  }
  elseif($role_mania){
    $type   = 'MANIA_DO';
    $submit = 'mania_do';
  }

  echo <<<EOF
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$VOTE_MESS->$submit}"></td></form>

EOF;

  if($not_type != ''){
    echo <<<EOF
<td>
<form method="POST" action="game_vote.php?$php_argv#game_top">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
<input type="submit" value="{$VOTE_MESS->$not_submit}"></form>
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
