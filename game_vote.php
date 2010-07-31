<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ROLES', 'ICON_CONF');

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
      VoteKick();
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

  $header = '<div align="center"><a id="game_top"></a>';
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
  LockVote(); //�ơ��֥����¾Ū��å�

  //��ɼ�Ѥߥ����å�
  $ROOM->LoadVote();
  if(isset($ROOM->vote[$SELF->uname])) OutputVoteResult('�����ॹ�����ȡ���ɼ�ѤߤǤ�', true);

  if($SELF->Vote('GAMESTART')){ //��ɼ����
    AggregateVoteGameStart(); //���׽���
    OutputVoteResult('��ɼ��λ', true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//�������� Kick ��ɼ�ν���
function VoteKick(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('KICK_DO'); //���ޥ�ɥ����å�

  $target = $USERS->ByID($RQ_ARGS->target_no); //��ɼ��Υ桼����������
  if($target->uname == '' || $target->live == 'kick'){
    OutputVoteResult('Kick����ɼ�褬���ꤵ��Ƥ��ʤ��������Ǥ� Kick ����Ƥ��ޤ�');
  }
  if($target->IsDummyBoy()) OutputVoteResult('Kick�������귯�ˤ���ɼ�Ǥ��ޤ���');
  if(! $GAME_CONF->self_kick && $target->IsSelf()){
    OutputVoteResult('Kick����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  }
  LockVote(); //�ơ��֥����¾Ū��å�

  //�����೫�ϥ����å�
  if(FetchResult("SELECT day_night FROM room WHERE room_no = {$ROOM->id}") != 'beforegame'){
    OutputVoteResult('Kick�����˥�����ϳ��Ϥ���Ƥ��ޤ�', true);
  }

  $ROOM->LoadVote(true); //��ɼ��������
  $vote_data = $ROOM->vote[$SELF->uname];
  if(is_array($vote_data) && in_array($target->uname, $vote_data)){
    OutputVoteResult("Kick��{$target->handle_name} ����� Kick ��ɼ�Ѥ�", true);
  }
  //PrintData($ROOM->vote); //�ƥ�����
  //OutputVoteResult('Kick���ƥ���', true);
  //return;

  if($SELF->Vote('KICK_DO', $target->uname)){ //��ɼ����
    $ROOM->Talk("KICK_DO\t" . $target->handle_name, $SELF->uname); //��ɼ���ޤ�������
    $vote_count = AggregateVoteKick($target); //���׽���
    OutputVoteResult("��ɼ��λ��{$target->handle_name} ����{$vote_count} ���� " .
		     "(Kick ����ˤ� {$GAME_CONF->kick} �Ͱʾ����ɼ��ɬ�פǤ�)", true);
  }
  else{
    OutputVoteResult('�ǡ����١������顼', true);
  }
}

//Kick ��ɼ�ν��׽��� ($target : �о� HN, �֤��� : �о� HN ����ɼ��׿�)
function AggregateVoteKick($target){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('KICK_DO'); //���ޥ�ɥ����å�

  //������ɼ�������ˤ��Ǥ���ɼ���Ƥ���Ϳ������
  $vote_count = 1;
  foreach($ROOM->vote as $stack){
    if(in_array($target->uname, $stack)) $vote_count++;
  }

  //������ʾ����ɼ�����ä� / ���å����������귯 / ���� KICK ��ͭ���ξ��˽���
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy() &&
     ! ($GAME_CONF->self_kick && $target->IsSelf())){
    return $vote_count;
  }

  $query = "UPDATE user_entry SET live = 'kick', session_id = NULL " .
    "WHERE room_no = {$ROOM->id} AND user_no = '{$target->user_no}' AND user_no > 0";
  SendQuery($query);
  $ROOM->Talk($target->handle_name . $MESSAGE->kick_out); //�ФƹԤä���å�����
  $ROOM->Talk($MESSAGE->vote_reset); //��ɼ�ꥻ�å�����
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  DeleteVote(); //���ޤǤ���ɼ���������
  return $vote_count;
}

//�����ɼ����
function VoteDay(){
  global $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  $target = $USERS->ByReal($RQ_ARGS->target_no); //��ɼ��Υ桼����������
  if($target->uname == '') OutputVoteResult('�跺����ɼ�褬���ꤵ��Ƥ��ޤ���');
  if($target->IsSelf())    OutputVoteResult('�跺����ʬ�ˤ���ɼ�Ǥ��ޤ���');
  if(! $target->IsLive())  OutputVoteResult('�跺����¸�԰ʳ��ˤ���ɼ�Ǥ��ޤ���');

  $vote_duel = $ROOM->event->vote_duel; //�ü쥤�٥�Ȥ����
  if(is_array($vote_duel) && ! in_array($RQ_ARGS->target_no, $vote_duel)){
    OutputVoteResult('�跺��������ɼ�оݼ԰ʳ��ˤ���ɼ�Ǥ��ޤ���');
  }
  LockVote(); //�ơ��֥����¾Ū��å�

  //��ɼ�Ѥߥ����å�
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$RQ_ARGS->vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('�跺����ɼ�Ѥ�');

  //-- ��ɼ���� --//
  //�򿦤˱�������ɼ��������
  $vote_number = 1;
  if($SELF->IsRoleGroup('elder')) $vote_number++; //ĹϷ��

  //�����򿦤ν���
  $ROLES->actor = $USERS->ByVirtual($SELF->user_no); //������ɼ�Ԥ򥻥å�
  foreach($ROLES->Load('vote_do') as $filter) $filter->FilterVoteDo($vote_number);

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
  case 'ESCAPE_DO':
    if(! $SELF->IsRole('escaper')) OutputVoteResult('�롧ƨ˴�԰ʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'MAGE_DO':
    if($SELF->IsRole('emerald_fox')){
      if(! $SELF->IsActive()) OutputVoteResult('�롧��Ѥϰ��٤����Ǥ��ޤ���');
    }
    elseif(! $SELF->IsRoleGroup('mage')){
      OutputVoteResult('�롧�ꤤǽ�ϼ԰ʳ�����ɼ�Ǥ��ޤ���');
    }
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
    if(! $SELF->IsReviveGroup()) OutputVoteResult('�롧����ǽ�ϼ԰ʳ�����ɼ�Ǥ��ޤ���');
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
    if(! $SELF->IsRoleGroup('assassin')) OutputVoteResult('�롧�Ż��Էϰʳ�����ɼ�Ǥ��ޤ���');
    $not_type = $RQ_ARGS->situation == 'ASSASSIN_NOT_DO';
    break;

  case 'MIND_SCANNER_DO':
    if(! $SELF->IsRole('mind_scanner', 'evoke_scanner')){
      OutputVoteResult('�롧���Ȥꡦ�������ʳ�����ɼ�Ǥ��ޤ���');
    }
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    break;

  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('�롧��ϵ�ϰʳ�����ɼ�Ǥ��ޤ���');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRole('jammer_mad', 'jammer_fox')){
      OutputVoteResult('�롧���ơ���Ѱʳ�����ɼ�Ǥ��ޤ���');
    }
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
    if(! $SELF->IsChildFox(true) && ! $SELF->IsRole('jammer_fox')){
      OutputVoteResult('�롧�Ҹѷϰʳ�����ɼ�Ǥ��ޤ���');
    }
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'angel', 'dummy_chiroptera')){
      OutputVoteResult('�롧���塼�ԥåɷϡ�ŷ�ȷϰʳ�����ɼ�Ǥ��ޤ���');
    }
    $is_cupid = true;
    break;

  case 'VAMPIRE_DO':
    if(! $SELF->IsRole('vampire')) OutputVoteResult('�롧�۷쵴�ʳ�����ɼ�Ǥ��ޤ���');
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
  LockVote(); //�ơ��֥����¾Ū��å�
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
      if($SELF->IsRole('self_cupid', 'moon_cupid', 'dummy_chiroptera')){ //�ᰦ��
	OutputVoteResult($error_header . '�ᰦ�ԡ�������ɱ��ɬ����ʬ���оݤ˴ޤ�Ƥ�������');
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
      if($SELF->IsWolf(true) && ! $SELF->IsRole('hungry_wolf') &&
	 $USERS->ByReal($target->user_no)->IsWolf(true)){
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
      $is_moon  = $SELF->IsRole('moon_cupid');
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
	elseif($is_moon){ //������ɱ
	  $add_role .= ' challenge_lovers'; //���˵ầ�Ԥ��ɲ�
	  if(! $target->IsSelf()){ //�ܿͤˤϼ����Ԥ��ɲ�
	    $SELF->AddRole('mind_receiver['. strval($target->user_no) . ']');
	  }
	}
	elseif($is_mind){ //�����ʤ鶦�ļԤ��ɲ�
	  $add_role .= ' mind_friend['. strval($SELF->user_no) . ']';
	  if(! $self_shoot){//¾�ͷ���ʤ��ܿͤ˼����Ԥ��ɲä���
	    $SELF->AddRole('mind_receiver[' . strval($target->user_no) . ']');
	  }
	}
	$target->AddRole($add_role);
	$target->ParseRoles($target->GetRole()); //�ƥѡ��� (���ܻ�Ƚ����)
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
<a id="game_top"></a>
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
  $header = '<input type="radio" name="target_no" id="';
  foreach($USERS->rows as $user_no => $user){
    if($count > 0 && $count % 5 == 0) echo "</tr>\n<tr>\n"; //5�Ĥ��Ȥ˲���
    $count++;

    $icon = $ICON_CONF->path . '/' . $user->icon_filename;
    $checkbox = ! $user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $user->IsSelf()) ?
      $header . $user_no . '" value="' . $user_no . '">'."\n" : '';

    echo <<<EOF
<td><label for="{$user_no}">
<img src="{$icon}" width="{$width}" height="{$height}" style="border-color: {$user->color};">
<font color="{$user->color}">��</font>{$user->handle_name}<br>
{$checkbox}</label></td>

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
  if($ROOM->date == 1) OutputVoteResult('�跺����������ɼ���פǤ�');
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
    if(is_array($vote_duel) && ! in_array($id, $vote_duel)) continue;
    $count++;
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
  if($SELF->IsRole('escaper')){
    if($ROOM->date == 1) OutputVoteResult('�롧������ƨ˴�ϤǤ��ޤ���');
    $type = 'ESCAPE_DO';
  }
  elseif($SELF->IsRoleGroup('mage')){
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
  elseif($role_revive = $SELF->IsReviveGroup()){
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
  elseif($SELF->IsRole('mind_scanner', 'evoke_scanner')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    if($SELF->IsRole('evoke_scanner') && $ROOM->IsOpenCast()){
      OutputVoteResult('�롧����������������ʤ��ץ��ץ���󤬥��դλ�����ɼ�Ǥ��ޤ���');
    }
    $type = 'MIND_SCANNER_DO';
  }
  elseif($role_wolf = $SELF->IsWolf()){
    $type = 'WOLF_EAT';
  }
  elseif($SELF->IsRole('jammer_mad', 'jammer_fox')){
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
  elseif($SELF->IsChildFox(true)){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera', 'mirror_fairy')){
    if($ROOM->date != 1) OutputVoteResult('�롧�����ʳ�����ɼ�Ǥ��ޤ���');
    $type = 'CUPID_DO';
    $role_cupid = $SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera');
    $role_mirror_fairy = $SELF->IsRole('mirror_fairy');
    $cupid_self_shoot  = $SELF->IsRole('self_cupid', 'dummy_chiroptera', 'moon_cupid') ||
      $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot;
  }
  elseif($SELF->IsRole('vampire')){
    if($ROOM->date == 1) OutputVoteResult('�롧�����ν���ϤǤ��ޤ���');
    $type = 'VAMPIRE_DO';
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
    $is_wolf = $role_wolf && ! $SELF->IsRole('hungry_wolf', 'silver_wolf') &&
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
