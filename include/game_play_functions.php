<?php
//ǽ�Ϥμ���Ȥ������������
function OutputAbility(){
  global $GAME_CONF, $MESSAGE, $ROLE_IMG, $ROOM, $USERS, $SELF;

  //��������Τ�ɽ������
  if(! $ROOM->IsPlaying()) return false;

  if($SELF->IsDead()){ //��˴�������ܿͤ�ǽ�Ϥ�ɽ�����ʤ�
    echo '<span class="ability ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    if($SELF->IsRole('mind_evoke')) $ROLE_IMG->Output('mind_evoke');
    return;
  }

  if($SELF->IsRole('human', 'saint', 'executor', 'suspect', 'unconscious')){ //¼�ͷ�
    $ROLE_IMG->Output('human');
  }
  elseif($SELF->IsRole('elder')){ //ĹϷ
    $ROLE_IMG->Output($SELF->main_role);
  }
  elseif($SELF->IsRoleGroup('mage')){ //�ꤤ�շ�
    $ROLE_IMG->Output($SELF->IsRole('dummy_mage') ? 'mage' : $SELF->main_role);
    OutputSelfAbilityResult('MAGE_RESULT'); //�ꤤ��̤�ɽ��
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //�����ɼ
  }
  elseif($SELF->IsRole('voodoo_killer')){ //���ۻ�
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('VOODOO_KILLER_SUCCESS'); //�ꤤ��̤�ɽ��
    //�����ɼ
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
  elseif($SELF->IsRoleGroup('necromancer')){ //��ǽ�Է�
    $ROLE_IMG->Output($SELF->IsRole('dummy_necromancer') ? 'necromancer' : $SELF->main_role);
    if(! $SELF->IsRole('yama_necromancer')){ //��ǽ��̤�ɽ��
      OutputSelfAbilityResult(strtoupper($SELF->main_role) . '_RESULT');
    }
  }
  elseif($SELF->IsRole('medium')){ //���
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('MEDIUM_RESULT'); //������̤�ɽ��
  }
  elseif($SELF->IsRoleGroup('priest')){ //�ʺ׷�
    $ROLE_IMG->Output($SELF->IsRole('crisis_priest') ? 'human' : $SELF->main_role);
    switch($SELF->main_role){
    case 'priest':
      OutputSelfAbilityResult('PRIEST_RESULT'); //������̤�ɽ��
      break;

    case 'bishop_priest':
      OutputSelfAbilityResult('BISHOP_PRIEST_RESULT'); //������̤�ɽ��
      break;

    case 'crisis_priest':
      OutputSelfAbilityResult('CRISIS_PRIEST_RESULT'); //������̤�ɽ��
      break;
    }
  }
  elseif($SELF->IsRoleGroup('guard')){ //��ͷ�
    $ROLE_IMG->Output($SELF->IsRole('dummy_guard') ? 'guard' : $SELF->main_role);
    OutputSelfAbilityResult('GUARD_SUCCESS'); //��ҷ�̤�ɽ��
    OutputSelfAbilityResult('GUARD_HUNTED'); //����̤�ɽ��
    //�����ɼ
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO');
  }
  elseif($SELF->IsRole('reporter')){ //�֥�
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('REPORTER_SUCCESS'); //���Է�̤�ɽ��
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO');
    }
  }
  elseif($SELF->IsRole('anti_voodoo')){ //���
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('ANTI_VOODOO_SUCCESS'); //��ҷ�̤�ɽ��
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO');
    }
  }
  elseif($SELF->IsCommon()){ //��ͭ��
    $ROLE_IMG->Output($SELF->IsRole('dummy_common') ? 'common' : $SELF->main_role);

    //��־�������
    $parter = array();
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if($SELF->IsRole('dummy_common')){
	if($user->IsDummyBoy()) $partner[] = $user->handle_name;
      }
      elseif($user->IsCommon(true)){
	$partner[] = $user->handle_name;
      }
    }
    OutputPartner($partner, 'common_partner'); //��֤�ɽ��
  }
  elseif($SELF->IsRoleGroup('assassin')){ //�Ż��Է�
    $ROLE_IMG->Output($SELF->IsRole('eclipse_assassin') ? 'assassin' : $SELF->main_role);
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($SELF->IsRoleGroup('scanner')){ //���Ȥ��
    $ROLE_IMG->Output($SELF->main_role);

    if($ROOM->date == 1){
      if($ROOM->IsNight()){ //���������ɼ
	OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
      }
    }
    else{ //�����ܰʹߡ���ʬ�Υ��ȥ��/���󤻤�ɽ��
      $target = array();
      $target_role = $SELF->IsRole('mind_scanner') ? 'mind_read' : 'mind_evoke';
      foreach($USERS->rows as $user){
	if($user->IsPartner($target_role, $SELF->user_no)) $target[] = $user->handle_name;
      }
      OutputPartner($target, 'mind_scanner_target');
    }
  }
  elseif($SELF->IsWolf()){ //��ϵ��
    $ROLE_IMG->Output($SELF->main_role);

    //��־�������
    $wolf_partner = array();
    $mad_partner = array();
    $unconscious_list = array();
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if($user->IsWolf(true)){
	$wolf_partner[] = $USERS->GetHandleName($user->uname, true);
      }
      elseif($user->IsRole('whisper_mad')){
	$mad_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious', 'scarlet_fox')){
	$unconscious_list[] = $user->handle_name;
      }
    }
    if($SELF->IsWolf(true)){
      OutputPartner($wolf_partner, 'wolf_partner'); //��֤�ɽ��
      OutputPartner($mad_partner, 'mad_partner'); //�񤭶��ͤ�ɽ��
    }
    if($ROOM->IsNight()){ //�����̵�ռ��ȹȸѤ�ɽ��
      OutputPartner($unconscious_list, 'unconscious_list');
    }

    switch($SELF->main_role){
    case 'tongue_wolf':
      OutputSelfAbilityResult('TONGUE_WOLF_RESULT'); //���߷�̤�ɽ��
      break;

    case 'sex_wolf':
      OutputSelfAbilityResult('SEX_WOLF_RESULT'); //���߷�̤�ɽ��
      break;

    case 'possessed_wolf':
      //���ߤ�������ɽ��
      $target_list = $SELF->partner_list['possessed_target'];
      if(is_array($target_list)){
	$date = max(array_keys($target_list));
	$target = $USERS->ByID($target_list[$date])->handle_name;
	if($target != '') OutputAbilityResult('partner_header', $target, 'possessed_target');
      }
      break;

    case 'sirius_wolf':
      switch(strval(count($USERS->GetLivingWolves()))){
      case '2':
	OutputAbilityResult('ability_sirius_wolf', NULL);
	break;

      case '1':
	OutputAbilityResult('ability_full_sirius_wolf', NULL);
	break;
      }
      break;
    }

    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //�����ɼ
  }
  elseif($SELF->IsRoleGroup('mad')){ //���ͷ�
    $ROLE_IMG->Output($SELF->main_role);

    switch($SELF->main_role){
    case 'fanatic_mad': //������
      //ϵ��ɽ��
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)){
	  $wolf_partner[] = $USERS->GetHandleName($user->uname, true);
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
      break;

    case 'whisper_mad': //�񤭶���
      //ϵ���񤭶��ͤ�ɽ��
      foreach($USERS->rows as $user){
	if($user->IsSelf() || $user->IsRole('silver_wolf')) continue;
	if($user->IsWolf()){
	  $wolf_partner[] = $USERS->GetHandleName($user->uname, true);
	}
	elseif($user->IsRole('whisper_mad')){
	  $mad_partner[] = $user->handle_name;
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
      OutputPartner($mad_partner, 'mad_partner');
      break;

    case 'jammer_mad': //����
      if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
      break;

    case 'voodoo_mad': //���ѻ�
      if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
      break;

    case 'dream_eater_mad': //��
      if($ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
      }
      break;

    case 'trap_mad': //櫻�
      if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
      }
      break;

    case 'possessed_mad': //����
      //���ߤ�������ɽ��
      $target_list = $SELF->partner_list['possessed_target'];
      if(is_array($target_list)){
	$date = max(array_keys($target_list));
	$target = $USERS->ByID($target_list[$date])->handle_name;
	if($target != '') OutputAbilityResult('partner_header', $target, 'possessed_target');
      }
      if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
      }
      break;
    }
  }
  elseif($SELF->IsFox()){ //�Ÿѷ�
    $ROLE_IMG->Output($SELF->main_role);

    if(! $SELF->IsRole('silver_fox', 'mind_lonely')){ //���ɽ��
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsFox(true)){
	  $fox_partner[] = $user->handle_name;
	}
	elseif($user->IsChildFox() || $user->IsRole('scarlet_wolf')){
	  $child_fox_partner[] = $user->handle_name;
	}
      }
      OutputPartner($fox_partner, 'fox_partner'); //�Ÿѷ�
      OutputPartner($child_fox_partner, 'child_fox_partner'); //�Ҹѷ�
    }

    if($SELF->IsChildFox()){ //�Ҹѷ�
      OutputSelfAbilityResult('CHILD_FOX_RESULT'); //�ꤤ��̤�ɽ��
      if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //�����ɼ
    }
    else{
      switch($SELF->main_role){
      case 'emerald_fox': //���
	if($SELF->IsActive() && $ROOM->IsNight()){
	  OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
	}
	break;

      case 'voodoo_fox': //����
	if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
	break;

      case 'revive_fox': //���
	if($ROOM->IsOpenCast()) break;
	OutputSelfAbilityResult('POISON_CAT_RESULT'); //������̤�ɽ��
	if($SELF->IsActive() && $ROOM->IsNight() && $ROOM->date > 1){ //�����ɼ
	  OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	}
	break;

      case 'possessed_fox': //���
	//���ߤ�������ɽ��
	$target_list = $SELF->partner_list['possessed_target'];
	if(is_array($target_list)){
	  $date = max(array_keys($target_list));
	  $target = $USERS->ByID($target_list[$date])->handle_name;
	  if($target != '') OutputAbilityResult('partner_header', $target, 'possessed_target');
	}
	if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	  OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
	}
	break;
      }
    }

    if(! ($SELF->IsRole('white_fox', 'poison_fox') || $SELF->IsChildFox())){
      OutputSelfAbilityResult('FOX_EAT'); //�����å�������ɽ��
    }
  }
  elseif($SELF->IsRoleGroup('chiroptera')){ //������
    if($SELF->IsRole('dummy_chiroptera')){
      $ROLE_IMG->Output('self_cupid');

      //��ʬ������Ǥä�(�Ĥ��)������ (��ʬ���ȴޤ�) ��ɽ��
      $dummy_lovers_id = $SELF->partner_list['dummy_chiroptera'];
      if(is_array($dummy_lovers_id)){
	$cupid_id = array($SELF->user_no, $dummy_lovers_id[0]);
	asort($cupid_id);
	$cupid_pair = array();
	foreach($cupid_id as $id) $cupid_pair[] = $USERS->ById($id)->handle_name;
	OutputPartner($cupid_pair, 'cupid_pair');
      }

      if($ROOM->IsNight() && $ROOM->date == 1){
	OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //���������ɼ
      }
    }
    else{
      $ROLE_IMG->Output($SELF->main_role);
    }
  }
  elseif($SELF->IsRoleGroup('fairy')){ //������
    $ROLE_IMG->Output($SELF->main_role);
    if($SELF->IsRole('mirror_fairy')){ //���������ɼ
      if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('fairy-do', 'fairy_do', 'CUPID_DO');
    }
    else{
      if($ROOM->IsNight()) OutputVoteMessage('fairy-do', 'fairy_do', 'FAIRY_DO'); //�����ɼ
    }
  }
  elseif($SELF->IsRoleGroup('cat')){ //ǭ����
    $ROLE_IMG->Output($SELF->main_role);

    if(! $ROOM->IsOpenCast()){
      OutputSelfAbilityResult('POISON_CAT_RESULT'); //������̤�ɽ��
      if($ROOM->IsNight() && $ROOM->date > 1){ //�����ɼ
	OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($SELF->IsRoleGroup('doll')){ //�峤�ͷ���
    $ROLE_IMG->Output($SELF->main_role);
    if(! $SELF->IsRole('doll_master')){ //���ɽ��
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsRole('doll_master')){
	  $doll_master[] = $user->handle_name;
	}
	elseif($user->IsDoll()){
	  $doll_partner[] = $user->handle_name;
	}
      }
      OutputPartner($doll_master, 'doll_master_list'); //�ͷ�����
      if($SELF->IsRole('friend_doll')){
	OutputPartner($doll_partner, 'doll_partner'); //�ͷ�����
      }
    }
  }
  elseif($SELF->IsRole('incubate_poison')){ //���Ǽ�
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif($SELF->IsRole('chain_poison')){ //Ϣ�Ǽ�
    $ROLE_IMG->Output('human');
  }
  elseif($SELF->IsRoleGroup('poison')) $ROLE_IMG->Output('poison'); //���ǼԷ�
  elseif($SELF->IsRoleGroup('pharmacist')){ //����
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('PHARMACIST_RESULT'); //�����̤�ɽ��
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel')){ //���塼�ԥåɷ�
    $ROLE_IMG->Output($SELF->main_role);

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ��
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $SELF->user_no)){
	$cupid_pair[] = $user->handle_name;
      }
    }
    OutputPartner($cupid_pair, 'cupid_pair');

    if($SELF->IsRole('ark_angel') && $ROOM->date == 2) OutputSelfAbilityResult('SYMPATHY_RESULT');
    //���������ɼ
    if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO');
  }
  elseif($SELF->IsRoleGroup('jealousy')) $ROLE_IMG->Output($SELF->main_role); //��ɱ
  elseif($SELF->IsRole('quiz')){ //�����
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->Output('quiz_chaos');
  }
  elseif($SELF->IsRoleGroup('mania')){ //���åޥ˥�
    $ROLE_IMG->Output($SELF->main_role);
    //���������ɼ
    if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO');
  }

  //-- ���������Ǥ�� --//
  $fix_display_list = array(); //���ɽ�������򿦥ꥹ��

  //�����åޥ˥��Υ��ԡ���̤�ɽ��
  if($SELF->IsRole('copied')) OutputSelfAbilityResult('MANIA_RESULT');
  $fix_display_list[] = 'copied';

  //ǽ���Ӽ� (���ϵ��櫻�)
  if($SELF->IsRole('lost_ability')) $ROLE_IMG->Output('lost_ability');
  $fix_display_list[] = 'lost_ability';

  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){ //����
    $dummy_lovers_list = $SELF->partner_list['dummy_chiroptera'];
    if(is_array($dummy_lovers_list)) $dummy_lovers_id = $dummy_lovers_list[0];
    foreach($USERS->rows as $user){
      if(! $user->IsSelf() &&
	 ($user->IsPartner('lovers', $SELF->partner_list) ||
	  $user->user_no == $dummy_lovers_id)){
	$lovers_partner[] = $USERS->GetHandleName($user->uname, true);
      }
    }
    OutputPartner($lovers_partner, 'partner_header', 'lovers_footer');
  }
  $fix_display_list[] = 'lovers';

  if($SELF->IsRole('febris')){
    $dead_date = max($SELF->GetPartner('febris'));
    if($ROOM->date == $dead_date){
      OutputAbilityResult('febris_header', $dead_date, 'sudden_death_footer');
    }
  }
  $fix_display_list[] = 'febris';

  if($SELF->IsRole('death_warrant')){
    $dead_date = max($SELF->GetPartner('death_warrant'));
    if($ROOM->date <= $dead_date){
      OutputAbilityResult('death_warrant_header', $dead_date, 'sudden_death_footer');
    }
  }
  $fix_display_list[] = 'death_warrant';

  //����������������򿦤�ɽ��
  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  if($virtual_self->IsRole('mind_open')) $ROLE_IMG->Output('mind_open');
  $fix_display_list[] = 'mind_open';

  if($ROOM->date > 1){ //���ȥ��Ϥ�ɽ���� 2 ���ܰʹ�
    if($virtual_self->IsRole('mind_read')) $ROLE_IMG->Output('mind_read');
    if($virtual_self->IsRole('mind_evoke')) $ROLE_IMG->Output('mind_evoke');
    if($virtual_self->IsRole('mind_lonely')) $ROLE_IMG->Output('mind_lonely');

    if($virtual_self->IsRole('mind_receiver')){
      $ROLE_IMG->Output('mind_receiver');

      $mind_scanner_target = array();
      foreach($virtual_self->partner_list['mind_receiver'] as $this_no){
	$mind_scanner_target[] = $USERS->ById($this_no)->handle_name;
      }
      OutputPartner($mind_scanner_target, 'mind_scanner_target');
    }

    if($virtual_self->IsRole('mind_friend')){
      $ROLE_IMG->Output('mind_friend');

      $mind_friend = array();
      foreach($USERS->rows as $user){
	if(! $user->IsSame($virtual_self->uname) &&
	   $user->IsPartner('mind_friend', $virtual_self->partner_list)){
	  $mind_friend[] = $user->handle_name;
	}
      }
      OutputPartner($mind_friend, 'mind_friend_list');
    }
    if($SELF->IsRole('mind_sympathy')){
      $ROLE_IMG->Output('mind_sympathy');
      if($ROOM->date == 2) OutputSelfAbilityResult('SYMPATHY_RESULT');
    }
  }
  array_push($fix_display_list, 'mind_read', 'mind_evoke', 'mind_lonely', 'mind_receiver',
	     'mind_friend', 'mind_sympathy');

  //����ʹߤϥ�������������ץ����αƶ��������
  if($ROOM->IsOption('secret_sub_role')) return;

  $role_keys_list    = array_keys($GAME_CONF->sub_role_list);
  $hide_display_list = array('decide', 'plague', 'good_luck', 'bad_luck');
  $not_display_list  = array_merge($fix_display_list, $hide_display_list);
  $display_list      = array_diff($role_keys_list, $not_display_list);
  $target_list       = array_intersect($display_list, array_slice($virtual_self->role_list, 1));

  foreach($target_list as $role) $ROLE_IMG->Output($role);
}

//��֤�ɽ������
function OutputPartner($partner_list, $header, $footer = NULL){
  global $ROLE_IMG;

  if(count($partner_list) < 1) return false; //��֤����ʤ����ɽ�����ʤ�

  $str = '<table class="ability-partner"><tr>'."\n" .
    '<td>' . $ROLE_IMG->Generate($header) . '</td>'."\n" .
    '<td>��' . implode('����', $partner_list) . '����</td>'."\n";
  if($footer) $str .= '<td>' . $ROLE_IMG->Generate($footer) . '</td>'."\n";
  echo $str . '</tr></table>'."\n";
}

//�ġ���ǽ��ȯư��̤�ɽ������
/*
  �����ν����ϡ�HN �˥��֤�����ȥѡ����˼��Ԥ���
  ��¼���� HN ���饿�֤���������б��Ǥ��뤬��
  ���⤽�⤳�Τ褦�ʥѡ����򤷤ʤ��Ȥ����ʤ� DB ��¤��
  ���꤬����Τǡ������Ǥ��ä��б����ʤ�
*/
function OutputSelfAbilityResult($action){
  global $ROOM, $SELF;

  $header = NULL;
  $footer = 'result_';
  switch($action){
  case 'MAGE_RESULT':
    $type = 'mage';
    $header = 'mage_result';
    break;

  case 'VOODOO_KILLER_SUCCESS':
    $type = 'guard';
    $footer = 'voodoo_killer_success';
    break;

  case 'NECROMANCER_RESULT':
  case 'DUMMY_NECROMANCER_RESULT':
  case 'SOUL_NECROMANCER_RESULT':
    $type = 'necromancer';
    break;

  case 'MEDIUM_RESULT':
    $type = 'necromancer';
    $header = 'medium';
    break;

  case 'PRIEST_RESULT':
    $type = 'priest';
    $header = 'priest_header';
    $footer = 'priest_footer';
    break;

  case 'BISHOP_PRIEST_RESULT':
    $type = 'priest';
    $header = 'bishop_priest_header';
    $footer = 'priest_footer';
    break;

  case 'CRISIS_PRIEST_RESULT':
    $type = 'crisis_priest';
    $header = 'side_';
    $footer = 'crisis_priest_result';
    break;

  case 'GUARD_SUCCESS':
    $type = 'guard';
    $footer = 'guard_success';
    break;

  case 'GUARD_HUNTED':
    $type = 'guard';
    $footer = 'guard_hunted';
    break;

  case 'REPORTER_SUCCESS':
    $type = 'reporter';
    $header = 'reporter_result_header';
    $footer = 'reporter_result_footer';
    break;

  case 'ANTI_VOODOO_SUCCESS':
    $type = 'guard';
    $footer = 'anti_voodoo_success';
    break;

  case 'TONGUE_WOLF_RESULT':
  case 'SEX_WOLF_RESULT':
    $type = 'mage';
    $header = 'wolf_result';
    break;

  case 'CHILD_FOX_RESULT':
    $type = 'mage';
    $header = 'mage_result';
    break;

  case 'FOX_EAT':
    $type = 'fox';
    $header = 'fox_targeted';
    break;

  case 'POISON_CAT_RESULT':
    $type = 'mage';
    $footer = 'poison_cat_';
    break;

  case 'PHARMACIST_RESULT':
    $type = 'mage';
    $footer = 'pharmacist_';
    break;

  case 'MANIA_RESULT':
    $type = 'mage';
    break;

  case 'SYMPATHY_RESULT':
    $type = 'sympathy';
    $header = 'sympathy_result';
    break;

  default:
    return false;
  }

  $yesterday = $ROOM->date - 1;
  $query = 'SELECT DISTINCT message FROM system_message WHERE room_no = ' .
    "{$ROOM->id} AND date = {$yesterday} AND type = '{$action}'";
  $result_list = FetchArray($query);

  switch($type){
  case 'mage':
    foreach($result_list as $result){
      list($actor, $target, $target_role) = explode("\t", $result);
      if($SELF->handle_name == $actor){
	OutputAbilityResult($header, $target, $footer . $target_role);
	break;
      }
    }
    break;

  case 'necromancer':
    if(is_null($header)) $header = 'necromancer';
    foreach($result_list as $result){
      list($target, $target_role) = explode("\t", $result);
      OutputAbilityResult($header . '_result', $target, $footer . $target_role);
    }
    break;

  case 'priest':
    foreach($result_list as $result){
      OutputAbilityResult($header, $result, $footer);
    }
    break;

  case 'crisis_priest':
    foreach($result_list as $result){
      OutputAbilityResult($header . $result, NULL, $footer);
    }
    break;

  case 'guard':
    foreach($result_list as $result){
      list($actor, $target) = explode("\t", $result);
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, $footer);
	break;
      }
    }
    break;

  case 'reporter':
    foreach($result_list as $result){
      list($actor, $target, $wolf_handle) = explode("\t", $result);
      if($SELF->handle_name == $actor){
	OutputAbilityResult($header, $target . ' ����� ' . $wolf_handle, $footer);
	break;
      }
    }
    break;

  case 'fox':
    foreach($result_list as $result){
      if($SELF->handle_name == $result){
	OutputAbilityResult($header, NULL);
	break;
      }
    }
    break;

  case 'sympathy':
    foreach($result_list as $result){
      list($actor, $target, $target_role) = explode("\t", $result);
      if($SELF->IsRole('ark_angel') || $SELF->handle_name == $actor){
	OutputAbilityResult($header, $target, $footer . $target_role);
      }
    }
    break;
  }
}

//ǽ��ȯư��̤�ɽ������
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if(isset($header)) echo '<td>' . $ROLE_IMG->Generate($header) . '</td>'."\n";
  if(isset($target)) echo '<td>' . $target . '</td>'."\n";
  if(isset($footer)) echo '<td>' . $ROLE_IMG->Generate($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//���̤��ɼ��å���������
function OutputVoteMessage($class, $sentence, $situation, $not_situation = ''){
  global $MESSAGE, $ROOM;

  //��ɼ�Ѥߤʤ��å�������ɽ�����ʤ�
  if(! $ROOM->test_mode && CheckSelfVoteNight($situation, $not_situation)) return false;

  $message_str = 'ability_' . $sentence;
  echo '<span class="ability ' . $class . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}
