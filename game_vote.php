<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ICON_CONF');

//-- �ǡ������� --//
$INIT_CONF->LoadRequest('RequestGameVote'); //���������
$DB_CONF->Connect(); //DB ��³
$SESSION->Certify(); //���å����ǧ��

$ROOM =& new Room($RQ_ARGS); //¼��������
if($ROOM->IsFinished()) OutputVoteError('�����ཪλ', '������Ͻ�λ���ޤ���');
$ROOM->system_time = TZTime(); //���߻�������

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF = $USERS->BySession(); //��ʬ�ξ�������

//-- �ᥤ��롼���� --//
if($RQ_ARGS->vote){ //��ɼ����
  if($ROOM->IsBeforeGame()){ //�����೫�� or Kick ��ɼ����
    switch($RQ_ARGS->situation){
    case 'GAMESTART':
      $INIT_CONF->LoadClass('CAST_CONF'); //�����������
      VoteGameStart();
      break;

    case 'KICK_DO':
      VoteKick($RQ_ARGS->target_handle_name);
      break;

    default: //�������褿����å����顼
      OutputVoteError('�����೫������ɼ');
      break;
    }
  }
  elseif($SELF->IsDead()){
    VoteDeadUser();
  }
  elseif($RQ_ARGS->target_no == 0){
    OutputVoteError('����ɼ', '��ɼ�����ꤷ�Ƥ�������');
  }
  elseif($ROOM->IsDay()){ //��ν跺��ɼ����
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //�����ɼ����
    VoteNight();
  }
  else{ //�������褿����å����顼
    OutputVoteError('��ɼ���ޥ�ɥ��顼', '��ɼ�����ꤷ�Ƥ�������');
  }
}
else{ //������˹�碌����ɼ�ڡ��������
  $INIT_CONF->LoadClass('VOTE_MESS');
  if($SELF->IsDead()){
    OutputVoteDeadUser();
  }
  else{
    switch($ROOM->day_night){
    case 'beforegame':
      OutputVoteBeforeGame();
      break;

    case 'day':
      OutputVoteDay();
      break;

    case 'night':
      OutputVoteNight();
      break;

    default: //�������褿����å����顼
      OutputVoteError('��ɼ�����󥨥顼');
      break;
    }
  }
}
$DB_CONF->Disconnect(); //DB ��³���

//-- �ؿ� --//
//���顼�ڡ�������
function OutputVoteError($title, $sentence = NULL){
  global $RQ_ARGS;

  $header = '<div align="center"><a name="game_top"></a>';
  $footer = "<br>\n" . $RQ_ARGS->back_url . '</div>';
  if(is_null($sentence)) $sentence = '�ץ���२�顼�Ǥ��������Ԥ��䤤��碌�Ƥ���������';
  OutputActionResult('��ɼ���顼 [' . $title .']', $header . $sentence . $footer);
}

//�ơ��֥����¾Ū��å�
function LockVote(){
  if(! LockTable('game')) OutputVoteResult('�����Ф��������Ƥ��ޤ���<br>������ɼ�򤪴ꤤ���ޤ���');
}

//�����೫����ɼ�ν���
function VoteGameStart(){
  global $GAME_CONF, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  if($SELF->IsDummyBoy(true)){ //����԰ʳ��ο����귯
    if($GAME_CONF->power_gm){ //���� GM �ˤ�붯���������Ƚ���
      LockVote(); //�ơ��֥����¾Ū��å�
      $sentence = AggregateVoteGameStart(true) ? '�����೫��' :
	'�����ॹ�����ȡ����ϿͿ���ã���Ƥ��ޤ���';
      OutputVoteResult($sentence, true);
    }
    else{
      OutputVoteResult('�����ॹ�����ȡ������귯����ɼ���פǤ�');
    }
  }

  //��ɼ�Ѥߥ����å�
  $ROOM->LoadVote();
  //PrintData($ROOM->vote); //�ƥ�����
  //DeleteVote(); //�ƥ�����
  if(isset($ROOM->vote[$SELF->uname])) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�');
  LockVote(); //�ơ��֥����¾Ū��å�

  if($SELF->Vote('GAMESTART')){ //��ɼ����
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
  $user_count = FetchResult($ROOM->GetQuery(false, 'user_entry') . ' AND user_no > 0');

  //��ɼ��������
  if($force_start){ //�������ϥ⡼�ɻ��ϥ����å�
    $vote_count = $user_count;
  }
  else{
    $vote_count = $ROOM->LoadVote(); //��ɼ�������� (��å����ξ���ϻȤ�ʤ���)
    if($ROOM->IsDummyBoy(true)) $vote_count++; //�����귯���Ѥʤ�����귯��ʬ��û�
  }

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count != $user_count || $vote_count < min(array_keys($CAST_CONF->role_list))){
    return false;
  }

  //-- �������롼���� --//
  $ROOM->LoadOption(); //�������ꥪ�ץ����ξ�������
  //PrintData($ROOM->option_role);

  //����������ѿ��򥻥å�
  $uname_list        = $USERS->GetLivingUsers(); //�桼��̾������
  $role_list         = GetRoleList($user_count, $ROOM->option_role->row); //�򿦥ꥹ�Ȥ����
  $fix_uname_list    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $fix_role_list     = array(); //�桼��̾���б��������
  $remain_uname_list = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ

  //�ե饰���å�
  $gerd      = $ROOM->IsOption('gerd');
  $chaos     = $ROOM->IsOptionGroup('chaos'); //chaosfull ��ޤ�
  $quiz      = $ROOM->IsQuiz();
  $detective = $ROOM->IsOption('detective');
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
      //õ��¼�ʤ�����귯���оݳ��򿦤��ɲä���
      if($detective && ! in_array('detective_common', $CAST_CONF->disable_dummy_boy_role_list)){
	$CAST_CONF->disable_dummy_boy_role_list[] = 'detective_common';
      }

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
  $delete_role_list = array('lovers', 'copied', 'febris', 'death_warrant', 'panelist',
			    'mind_read', 'mind_evoke', 'mind_receiver', 'mind_friend',
			    'mind_lonely', 'mind_sympathy');

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
  #$add_sub_role = 'perverseness';
  $add_sub_role = 'mind_open';
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
    if($ROOM->IsOption($role) && $user_count >= $CAST_CONF->$role){
      $fix_role_list[$rand_keys[$rand_keys_index++]] .= ' ' . $role;
    }
  }
  if($ROOM->IsOption('liar')){ //ϵ��ǯ¼
    $role = 'liar';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++){ //�����˰����Ψ��ϵ��ǯ��Ĥ���
      if(mt_rand(1, 100) <= 70) $fix_role_list[$i] .= ' ' . $role;
    }
  }
  if($ROOM->IsOption('gentleman')){ //�»Ρ��ʽ�¼
    $sub_role_list = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //���������̤˱����ƿ»Τ��ʽ���Ĥ���
      $role = $sub_role_list[$USERS->ByUname($fix_uname_list[$i])->sex];
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($ROOM->IsOption('sudden_death')){ //�����μ�¼
    $sub_role_list = array_diff($GAME_CONF->sub_role_group_list['sudden-death'], array('panelist'));
    $delete_role_list = array_merge($delete_role_list, $sub_role_list);
    for($i = 0; $i < $user_count; $i++){ //�����˥���å���Ϥ򲿤��Ĥ���
      $role = GetRandom($sub_role_list);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience'){ //û���ϰ�ͤ���
	$sub_role_list = array_diff($sub_role_list, array('impatience'));
      }
    }
  }
  elseif($ROOM->IsOption('perverseness')){ //ŷ�ٵ�¼
    $role = 'perverseness';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++){
      $fix_role_list[$i] .= ' ' . $role;
    }
  }

  if($chaos && ! $ROOM->IsOption('no_sub_role')){
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
  /*
  if($ROOM->IsOption('festival')){ //���פ�¼ (���Ƥϴ����ͤ���ͳ�˥������ह��)
    $role = 'nervy';
    for($i = 0; $i < $user_count; $i++){ //�����˼����Ȥ�Ĥ���
      $fix_role_list[$i] .= ' ' . $role;
    }
  }
  */
  //�ǥХå���
  //PrintData($fix_uname_list); PrintData($fix_role_list); DeleteVote(); return false;

  //����DB�˹���
  $role_count_list = array();
  $detective_list = array();
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $user = $USERS->ByUname($fix_uname_list[$i]);
    $user->ChangeRole($role);
    $role_list = explode(' ', $role);
    foreach($role_list as $role) $role_count_list[$role]++;
    if($detective && in_array('detective_common', $role_list)) $detective_list[] = $user;
  }

  //���ꥹ������
  if($chaos){
    if($ROOM->IsOption('chaos_open_cast_camp')){
      $sentence = GenerateRoleNameList($role_count_list, 'camp');
    }
    elseif($ROOM->IsOption('chaos_open_cast_role')){
      $sentence = GenerateRoleNameList($role_count_list, 'role');
    }
    elseif($ROOM->IsOption('chaos_open_cast')){
      $sentence = GenerateRoleNameList($role_count_list);
    }
    else{
      $sentence = $MESSAGE->chaos;
    }
  }
  else{
    $sentence = GenerateRoleNameList($role_count_list);
  }

  //�����೫��
  $ROOM->date++;
  $ROOM->day_night = 'night';
  $query = "UPDATE room SET date = {$ROOM->date}, day_night = '{$ROOM->day_night}', " .
    "status = 'playing', start_time = NOW() WHERE room_no = {$ROOM->id}";
  SendQuery($query);
  //OutputSiteSummary(); //RSS��ǽ�ϥƥ�����
  $ROOM->Talk($sentence);
  if($detective && count($detective_list) > 0){ //õ��¼�λ�̾
    $detective_user = GetRandom($detective_list);
    $ROOM->Talk('õ��� ' . $detective_user->handle_name . ' ����Ǥ�');
    if($ROOM->IsOption('gm_login') && $ROOM->IsOption('not_open_cast') && $user_count > 7){
      $detective_user->ToDead(); //�õ��⡼�ɤʤ�õ����������
    }
  }
  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  if($ROOM->IsOption('chaosfull')) CheckVictory(); //��������Ϥ����ʤ꽪λ���Ƥ��ǽ������
  DeleteVote(); //���ޤǤ���ɼ���������
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

  LockVote(); //�ơ��֥����¾Ū��å�

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

  if($SELF->Vote('KICK_DO', $target_uname)){ //��ɼ����
    $ROOM->Talk("KICK_DO\t" . $target, $SELF->uname); //��ɼ���ޤ�������
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
  $sql = mysql_query($ROOM->GetQuery(false, 'user_entry') . ' AND user_no > 0');
  $user_count = mysql_result($sql, 0, 0);

  //Kick ����ͤ� user_no �����
  $sql = mysql_query("SELECT user_no FROM user_entry WHERE room_no = {$ROOM->id}
			AND handle_name = '$target' AND user_no > 0");
  $target_no = mysql_result($sql, 0, 0);

  //Kick ���줿�ͤϻ�˴��user_no �� -1�����å���� ID ��������
  mysql_query("UPDATE user_entry SET user_no = -1, live = 'dead', session_id = NULL
		WHERE room_no = {$ROOM->id} AND handle_name = '$target' AND user_no > 0");

  //���å�����ƶ���������ͤ��
  for($i = $target_no; $i < $user_count; $i++){
    $next = $i + 1;
    mysql_query("UPDATE user_entry SET user_no = $i WHERE room_no = {$ROOM->id} AND user_no = $next");
  }

  $ROOM->Talk($target . $MESSAGE->kick_out); //�ФƹԤä���å�����
  $ROOM->Talk($MESSAGE->vote_reset); //��ɼ�ꥻ�å�����
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  return $vote_count;
}

//�����ɼ����
function VoteDay(){
  global $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  //��ɼ�Ѥߥ����å�
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //������ɼ�Ԥ����
  $target = $USERS->ByReal($RQ_ARGS->target_no); //��ɼ��Υ桼����������
  if($target->uname == '') OutputVoteResult('�跺����ɼ�褬���ꤵ��Ƥ��ޤ���');
  if($target->IsSelf())    OutputVoteResult('�跺����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  if(! $target->IsLive())  OutputVoteResult('�跺����¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');

  $vote_duel = $ROOM->event->vote_duel; //�ü쥤�٥�Ȥ����
  if(is_array($vote_duel) && ! in_array($RQ_ARGS->target_no, $vote_duel)){
    OutputVoteResult('�跺��������ɼ�оݼ԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
  }
  LockVote(); //�ơ��֥����¾Ū��å�

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
    $vote_number += mt_rand(0, 2) - 1;
  }

  if(! $SELF->Vote('VOTE_KILL', $target->uname, $vote_number)){ //��ɼ����
    OutputVoteResult('�ǡ����١������顼', true);
  }

  //�����ƥ��å�����
  $ROOM->Talk("VOTE_DO\t" . $USERS->GetHandleName($target->uname, true), $SELF->uname);

  AggregateVoteDay(); //���׽���
  OutputVoteResult('��ɼ��λ', true);
}

//�����ɼ����
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  //-- ���٥��̾���򿦤���������å� --//
  if($SELF->IsDummyBoy()) OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  switch($RQ_ARGS->situation){
  case 'MAGE_DO':
    if(! $SELF->IsRoleGroup('mage', 'emerald_fox')) OutputVoteResult('�롧�ꤤ�շϰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRole('voodoo_killer')) OutputVoteResult('�롧���ۻհʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('�롧��ͷϰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('�롧�֥󲰰ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('�롧����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsRoleGroup('cat', 'revive_fox')){
      OutputVoteResult('�롧ǭ���ϡ���Ѱʳ�����ɼ�Ǥ��ޤ���');
    }
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
       OutputVoteResult('�롧��Ѥ������ϰ��٤����Ǥ��ޤ���');
    }
    $not_type = $RQ_ARGS->situation == 'POISON_CAT_NOT_DO';
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRoleGroup('assassin')) OutputVoteResult('�롧�Ż��԰ʳ�����ɼ�Ǥ��ޤ���');
    $not_type = $RQ_ARGS->situation == 'ASSASSIN_NOT_DO';
    break;

  case 'MIND_SCANNER_DO':
    if(! $SELF->IsRoleGroup('scanner')) OutputVoteResult('�롧���Ȥ�ϰʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    break;

  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('�롧��ϵ�ϰʳ�����ɼ�Ǥ��ޤ���');
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
    $not_type = $RQ_ARGS->situation == 'TRAP_MAD_NOT_DO';
    break;

  case 'POSSESSED_DO':
  CASE 'POSSESSED_NOT_DO':
    if(! $SELF->IsRole('possessed_mad', 'possessed_fox')){
      OutputVoteResult('�롧��������Ѱʳ�����ɼ�Ǥ��ޤ���');
    }
    if(! $SELF->IsActive()) OutputVoteResult('�롧��ͤϰ��٤����Ǥ��ޤ���');
    $not_type = $RQ_ARGS->situation == 'POSSESSED_NOT_DO';
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsChildFox()) OutputVoteResult('�롧�ҸѰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'angel', 'dummy_chiroptera')){
      OutputVoteResult('�롧���塼�ԥåɷϰʳ�����ɼ�Ǥ��ޤ���');
    }
    $is_cupid = true;
    break;

  case 'FAIRY_DO':
    if(! $SELF->IsRoleGroup('fairy')) OutputVoteResult('�롧�����ϰʳ�����ɼ�Ǥ��ޤ���');
    $is_mirror_fairy = $SELF->IsRole('mirror_fairy');
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRoleGroup('mania')) OutputVoteResult('�롧���åޥ˥��ϰʳ�����ɼ�Ǥ��ޤ���');
    break;

  default:
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
    break;
  }
  CheckAlreadyVote($is_mirror_fairy ? 'CUPID_DO' : $RQ_ARGS->situation); //��ɼ�Ѥߥ����å�

  //-- ��ɼ���顼�����å� --//
  $error_header = '�롧��ɼ�褬����������ޤ���<br>'; //���顼��å������Υإå�

  if($not_type); //��ɼ����󥻥륿���פϲ��⤷�ʤ�
  elseif($is_cupid || $is_mirror_fairy){ //���塼�ԥåɷ�
    if($SELF->IsRole('triangle_cupid')){
      if(count($RQ_ARGS->target_no) != 3) OutputVoteResult('�롧����Ϳ������ͤǤϤ���ޤ���');
    }
    elseif(count($RQ_ARGS->target_no) != 2){
      OutputVoteResult('�롧����Ϳ�����ͤǤϤ���ޤ���');
    }

    $target_list = array();
    $self_shoot = false; //��ʬ����ե饰������
    foreach($RQ_ARGS->target_no as $target_no){
      $target = $USERS->ByID($target_no); //��ɼ��Υ桼����������

      //��¸�԰ʳ��ȿ����귯�ؤ���ɼ��̵��
      if(! $target->IsLive() || $target->IsDummyBoy()){
	OutputVoteResult('��¸�԰ʳ��ȿ����귯�ؤ���ɼ�Ǥ��ޤ���');
      }

      $target_list[] = $target;
      $self_shoot |= $target->IsSelf(); //��ʬ���Ƚ��
    }

    //��ʬ����Ǥ�̵����������Υ������ǥ��顼���֤�
    if($is_cupid && ! $self_shoot){
      if($SELF->IsRole('self_cupid', 'dummy_chiroptera')){ //�ᰦ��
	OutputVoteResult($error_header . '�ᰦ�Ԥ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
      }
      elseif($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot){ //���ÿͿ�
	OutputVoteResult($error_header . '���Ϳ�¼�ξ��ϡ�ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
      }
    }
  }
  else{ //���塼�ԥåɷϰʳ�
    $target  = $USERS->ByID($RQ_ARGS->target_no); //��ɼ��Υ桼����������
    $is_live = $USERS->IsVirtualLive($target->user_no); //����Ū�������Ƚ��

    if($RQ_ARGS->situation != 'TRAP_MAD_DO' && $target->IsSelf()){ //櫻հʳ��ϼ�ʬ�ؤ���ɼ��̵��
      OutputVoteResult($error_header . '��ʬ�ˤ���ɼ�Ǥ��ޤ���');
    }

    if($RQ_ARGS->situation == 'POISON_CAT_DO' || $RQ_ARGS->situation == 'POSSESSED_DO'){
      if($is_live){ //���������ǽ�ϼԤϻ�԰ʳ��ؤ���ɼ��̵��
	OutputVoteResult($error_header . '��԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
      }
    }
    elseif(! $is_live){
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

  //-- ��ɼ���� --//
  LockVote(); //�ơ��֥����¾Ū��å�
  if($not_type){
    if(! $SELF->Vote($RQ_ARGS->situation)){ //��ɼ����
      OutputVoteResult('�ǡ����١������顼', true);
    }
    $ROOM->SystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation, $SELF->uname);
  }
  else{
    if($is_cupid){ //���塼�ԥåɷϤν���
      $uname_stack  = array();
      $handle_stack = array();
      $is_self  = $SELF->IsRole('self_cupid');
      $is_mind  = $SELF->IsRole('mind_cupid');
      $is_dummy = $SELF->IsRole('dummy_chiroptera');
      foreach($target_list as $target){
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;

	if($is_dummy){ //̴�ᰦ�Ԥν���
	  if(! $target->IsSelf()){ //��ʬ�ʳ��ˤϲ��⤷�ʤ�
	    $main_role = 'dummy_chiroptera';
	    $change_role = $main_role . '[' . strval($target->user_no) . ']';
	    $SELF->ReplaceRole($main_role, $change_role);
	  }
	  continue;
	}

	//�򿦤����ͤ��ɲ�
	$add_role = 'lovers[' . strval($SELF->user_no) . ']';
	if($is_self && ! $target->IsSelf()){ //�ᰦ�Ԥʤ����˼����Ԥ��ɲ�
	  $add_role .= ' mind_receiver['. strval($SELF->user_no) . ']';
	}
	elseif($is_mind){ //�����ʤ鶦�ļԤ��ɲ�
	  $add_role .= ' mind_friend['. strval($SELF->user_no) . ']';
	  if(! $self_shoot){//¾�ͷ���ʤ��ܿͤ˼����Ԥ��ɲä���
	    $SELF->AddRole('mind_receiver[' . strval($target->user_no) . ']');
	  }
	}
	/*
	//�����ؤ�QP�ʤ鼫ʬ�������ؤ���
	elseif($SELF->IsRole('possessed_cupid') && ! $target->IsSelf()){
	  $SELF->AddRole('possessed_target[2-' . $target->user_no . '] ' .
			 'possessed[2-' . $target->user_no . ']');
	  $target->AddRole('possessed_target[2-' . $SELF->user_no . '] ' .
				'possessed[2-' . $SELF->user_no . ']');
	}
	*/
	$target->AddRole($add_role);
      }
      if($SELF->IsRoleGroup('angel')){
	$lovers_a = $target_list[0];
	$lovers_b = $target_list[1];
	if(($SELF->IsRole('angel') && $lovers_a->sex != $lovers_b->sex) ||
	   ($SELF->IsRole('rose_angel') && $lovers_a->sex == 'male' && $lovers_b->sex == 'male') ||
	   ($SELF->IsRole('lily_angel') && $lovers_a->sex == 'female' && $lovers_b->sex == 'female')){
	  $lovers_a->AddRole('mind_sympathy');
	  $sentence = $lovers_a->handle_name . "\t" . $lovers_b->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_b->main_role, 'SYMPATHY_RESULT');

	  $lovers_b->AddRole('mind_sympathy');
	  $sentence = $lovers_b->handle_name . "\t" . $lovers_a->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_a->main_role, 'SYMPATHY_RESULT');
	}
      }

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($is_mirror_fairy){ //�������ν���
      $id_stack     = array();
      $uname_stack  = array();
      $handle_stack = array();
      foreach($target_list as $target){ //�������
	$id_stack[]     = strval($target->user_no);
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;
      }
      $main_role = 'mirror_fairy';
      $change_role = $main_role . '[' . implode('-', $id_stack) . ']';
      $SELF->ReplaceRole($main_role, $change_role);

      $situation     = 'CUPID_DO';
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    else{ //�̾����
      $situation     = $RQ_ARGS->situation;
      $target_uname  = $USERS->ByReal($target->user_no)->uname;
      $target_handle = $target->handle_name;
    }

    if(! $SELF->Vote($situation, $target_uname)){ //��ɼ����
      OutputVoteResult('�ǡ����١������顼', true);
    }
    $ROOM->SystemMessage($SELF->handle_name . "\t" . $target_handle, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation . "\t" . $target_handle, $SELF->uname);
  }

  AggregateVoteNight(); //���׽���
  OutputVoteResult('��ɼ��λ', true);
}

//��Ԥ���ɼ����
function VoteDeadUser(){
  global $ROOM, $SELF;

  CheckSituation('REVIVE_REFUSE'); //���ޥ�ɥ����å�

  //��ɼ�Ѥߥ����å�
  if($SELF->IsDrop()) OutputVoteResult('�������ࡧ��ɼ�Ѥ�');
  if($ROOM->IsOpenCast()) OutputVoteResult('�������ࡧ��ɼ���פǤ�');
  LockVote(); //�ơ��֥����¾Ū��å�

  //-- ��ɼ���� --//
  if(! $SELF->Update('live', 'drop')) OutputVoteResult('�ǡ����١������顼', true);

  //�����ƥ��å�����
  $sentence = '�����ƥࡧ' . $SELF->handle_name . '������������ष�ޤ�����';
  $ROOM->Talk($sentence, $SELF->uname, 'heaven', 'normal');

  OutputVoteResult('��ɼ��λ', true);
}

//��ɼ�ڡ��� HTML �إå�����
function OutputVotePageHeader(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  OutputHTMLHeader($SERVER_CONF->title . ' [��ɼ]', 'game');
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a name="game_top"></a>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">

EOF;
}

//������ΰ��ץ����å�
function CheckScene(){
  global $ROOM, $SELF;

  if($ROOM->day_night != $SELF->last_load_day_night){
    OutputVoteResult('��äƥ���ɤ��Ƥ�������');
  }
}

//����������ɼ�ڡ�������
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //��ɼ������������äƤ��뤫�����å�
  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  foreach($USERS->rows as $user_no => $user){
    if($count > 0 && $count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;

    $icon = $ICON_CONF->path . '/' . $user->icon_filename;
    if(! $user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $user->IsSelf())){
      $radio = '<input type="radio" id="' . $user->handle_name .
	'" name="target_handle_name" value="' . $user->handle_name . '">'."\n";
    }
    else
      $radio = '';

    echo <<<EOF
<td><label for="{$user->handle_name}">
<img src="{$icon}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">��</font>{$user->handle_name}<br>
{$radio}</label></td>

EOF;
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* Kick ����ˤ� {$GAME_CONF->kick} �ͤ���ɼ��ɬ�פǤ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->kick_do}"></form></td>
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
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
  global $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene();  //��ɼ������������äƤ��뤫�����å�
  $vote_times = $ROOM->GetVoteTimes(); //��ɼ��������

  //��ɼ�Ѥߥ����å�
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="{$vote_times}">
<table class="vote-page" cellspacing="5"><tr>

EOF;

  $virtual_self = $USERS->ByVirtual($SELF->user_no); //������ɼ�Ԥ����
  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;
  $vote_duel = $ROOM->event->vote_duel; //�ü쥤�٥�Ȥ����
  $checkbox_header = "\n".'<input type="radio" name="target_no" id="';
  foreach($USERS->rows as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;
    if(is_array($vote_duel) && ! in_array($id, $vote_duel)) continue;
    $is_live = $USERS->IsVirtualLive($id);

    //�����Ƥ���Х桼���������󡢻��Ǥ�л�˴��������
    $path = $is_live ? $ICON_CONF->path . '/' . $user->icon_filename : $ICON_CONF->dead;
    $checkbox = ($is_live && ! $user->IsSame($virtual_self->uname)) ?
      $checkbox_header . $id . '" value="' . $id . '">' : '';

    echo <<<EOF
<td><label for="{$id}">
<img src="{$path}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">��</font>{$user->handle_name}<br>{$checkbox}
</label></td>

EOF;
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//�����ɼ�ڡ�������Ϥ���
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //��ɼ����������å�

  //��ɼ�Ѥߥ����å�
  if($SELF->IsDummyBoy()) OutputVoteResult('�롧�����귯����ɼ��̵���Ǥ�');
  if($SELF->IsRoleGroup('mage')){
    $type = 'MAGE_DO';
  }
  elseif($SELF->IsRole('voodoo_killer')){
    $type = 'VOODOO_KILLER_DO';
  }
  elseif($SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����θ�ҤϤǤ��ޤ���');
    $type = 'GUARD_DO';
  }
  elseif($SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('�롧���������ԤϤǤ��ޤ���');
    $type = 'REPORTER_DO';
  }
  elseif($SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('�롧��������ʧ���ϤǤ��ޤ���');
    $type = 'ANTI_VOODOO_DO';
  }
  elseif($role_revive = $SELF->IsRoleGroup('cat', 'revive_fox')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����������ϤǤ��ޤ���');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
      OutputVoteResult('�롧��Ѥ������ϰ��٤����Ǥ��ޤ���');
    }
    $type       = 'POISON_CAT_DO';
    $not_type   = 'POISON_CAT_NOT_DO';
    $submit     = 'revive_do';
    $not_submit = 'revive_not_do';
  }
  elseif($SELF->IsRoleGroup('assassin')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ΰŻ��ϤǤ��ޤ���');
    $type     = 'ASSASSIN_DO';
    $not_type = 'ASSASSIN_NOT_DO';
  }
  elseif($SELF->IsRoleGroup('scanner')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    $type = 'MIND_SCANNER_DO';
  }
  elseif($role_wolf = $SELF->IsWolf()){
    $type = 'WOLF_EAT';
  }
  elseif($SELF->IsRole('jammer_mad')){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'jammer_do';
  }
  elseif($SELF->IsRole('voodoo_mad')){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('dream_eater_mad')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ν���ϤǤ��ޤ���');
    $type = 'DREAM_EAT';
  }
  elseif($role_trap = $SELF->IsRole('trap_mad')){
    if($ROOM->date == 1) OutputVoteResult('�롧����������֤ϤǤ��ޤ���');
    if(! $SELF->IsActive()) OutputVoteResult('�롧櫤ϰ��٤������֤Ǥ��ޤ���');
    $type       = 'TRAP_MAD_DO';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $submit     = 'trap_do';
    $not_submit = 'trap_not_do';
  }
  elseif($SELF->IsRole('possessed_mad', 'possessed_fox')){
    if($ROOM->date == 1) OutputVoteResult('�롧��������ͤϤǤ��ޤ���');
    if(! $SELF->IsActive()) OutputVoteResult('�롧��ͤϰ��٤����Ǥ��ޤ���');
    $type       = 'POSSESSED_DO';
    $not_type   = 'POSSESSED_NOT_DO';
    $role_revive = true;
  }
  elseif($SELF->IsRole('voodoo_fox')){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('emerald_fox')){
    if(! $SELF->IsActive()) OutputVoteResult('�롧�ꤤ�ϰ��٤����Ǥ��ޤ���');
    $type   = 'MAGE_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsChildFox()){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera', 'mirror_fairy')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    $type = 'CUPID_DO';
    $role_cupid = $SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera');
    $role_mirror_fairy = $SELF->IsRole('mirror_fairy');
    $cupid_self_shoot  = $SELF->IsRole('self_cupid', 'dummy_chiroptera') ||
      $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot;
  }
  elseif($SELF->IsRoleGroup('fairy')){
    $type = 'FAIRY_DO';
  }
  elseif($SELF->IsRoleGroup('mania')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    $type = 'MANIA_DO';
  }
  else{
    OutputVoteResult('�롧���ʤ�����ɼ�Ǥ��ޤ���');
  }
  CheckAlreadyVote($type, $not_type);
  if($role_mirror_fairy) $type = 'FAIRY_DO'; //��������ɽ������������ (���������ϥ��塼�ԥåɷ�)

  //�����귯���� or ������¼�λ��Ͽ����귯�����������٤ʤ�
  if($role_wolf && (($ROOM->IsDummyBoy() && $ROOM->date == 1) || $ROOM->IsQuiz())){
    //�����귯�Υ桼������
    $user_stack = array(1 => $USERS->rows[1]); //dummy_boy = 1�֤��ݾڤ���Ƥ��롩
  }
  else{
    $user_stack = $USERS->rows;
  }

  $count  = 0;
  $width  = $ICON_CONF->width;
  $height = $ICON_CONF->height;

  OutputVotePageHeader();
  echo '<table class="vote-page" cellspacing="5"><tr>'."\n";
  foreach($user_stack as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;
    $is_live = $USERS->IsVirtualLive($id);
    $is_wolf = $role_wolf && ! $SELF->IsRole('silver_wolf') &&
      $USERS->ByReal($id)->IsWolf(true);

    /*
      ���Ǥ���л�˴�������� (����ǽ�ϼԤϻ�˴��������ˤ��ʤ�)
      ϵƱ�Τʤ�ϵ�������������Ƥ���Х桼����������
    */
    $path = ! ($is_live || $role_revive) ? $ICON_CONF->dead :
      ($is_wolf ? $ICON_CONF->wolf : $ICON_CONF->path . '/' . $user->icon_filename);

    $checkbox = '';
    $checkbox_header = '<input type="radio" name="target_no"';
    $checkbox_footer = ' id="' . $id . '"value="' . $id . '">'."\n";
    if($role_cupid || $role_mirror_fairy){
      if(! $user->IsDummyBoy() && $is_live){
	$checked = ($role_cupid && $cupid_self_shoot && $user->IsSelf()) ? ' checked' : '';
	$checkbox = '<input type="checkbox" name="target_no[]"' . $checked . $checkbox_footer;
      }
    }
    elseif($role_revive){
      if(! $is_live && ! $user->IsSelf() && ! $user->IsDummyBoy()){
	$checkbox = $checkbox_header . $checkbox_footer;
      }
    }
    elseif($role_trap){
      if($is_live) $checkbox = $checkbox_header . $checkbox_footer;
    }
    elseif($is_live && ! $user->IsSelf() && ! $is_wolf){
      $checkbox = $checkbox_header . $checkbox_footer;
    }

    echo <<<EOF
<td><label for="{$id}">
<img src="{$path}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">��</font>{$user->handle_name}<br>{$checkbox}
</label></td>

EOF;
  }

  if(empty($submit)) $submit = strtolower($type);
  echo <<<EOF
</tr></table>
<span class="vote-message">* ��ɼ����ѹ��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$VOTE_MESS->$submit}"></td></form>

EOF;

  if($not_type != ''){
    if(empty($not_submit)) $not_submit = strtolower($not_type);
    echo <<<EOF
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
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

//��Ԥ���ɼ�ڡ�������
function OutputVoteDeadUser(){
  global $VOTE_MESS, $RQ_ARGS, $ROOM, $SELF;

  //��ɼ�Ѥߥ����å�
  if($SELF->IsDummyBoy()) OutputVoteResult('�������ࡧ�����귯����ɼ��̵���Ǥ�');
  if($SELF->IsDrop()) OutputVoteResult('�������ࡧ��ɼ�Ѥ�');
  if($ROOM->IsOpenCast()) OutputVoteResult('�������ࡧ��ɼ���פǤ�');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="REVIVE_REFUSE">
<span class="vote-message">* ��ɼ�μ��ä��ϤǤ��ޤ��󡣿��Ťˡ�</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->revive_refuse}"></form></td>
</tr></table></div>
</body></html>

EOF;
}

//��ɼ�Ѥߥ����å�
function CheckAlreadyVote($situation, $not_situation = ''){
  if(CheckSelfVoteNight($situation, $not_situation)) OutputVoteResult('�롧��ɼ�Ѥ�');
}
