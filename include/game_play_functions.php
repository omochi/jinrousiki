<?php
//ǽ�Ϥμ���Ȥ������������
function OutputAbility(){
  global $GAME_CONF, $MESSAGE, $ROLE_IMG, $ROOM, $USERS, $SELF;

  if(! $ROOM->IsPlaying()) return false; //��������Τ�ɽ������

  if($SELF->IsDead()){ //��˴��������󤻰ʳ���ɽ�����ʤ�
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
  elseif($SELF->IsRole('escaper')){ //ƨ˴��
    $ROLE_IMG->Output($SELF->main_role);
    //�����ɼ
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('fairy-do', 'escape_do', 'ESCAPE_DO');
  }
  elseif($SELF->IsRoleGroup('mage')){ //�ꤤ�շ�
    $ROLE_IMG->Output($SELF->IsRole('dummy_mage') ? 'mage' : $SELF->main_role);
    if($ROOM->date > 1) OutputSelfAbilityResult('MAGE_RESULT'); //�ꤤ���
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //�����ɼ
  }
  elseif($SELF->IsRole('voodoo_killer')){ //���ۻ�
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 1) OutputSelfAbilityResult('VOODOO_KILLER_SUCCESS'); //�ꤤ���
    //�����ɼ
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
  elseif($SELF->IsRoleGroup('necromancer')){ //��ǽ�Է�
    $ROLE_IMG->Output($SELF->IsRole('dummy_necromancer') ? 'necromancer' : $SELF->main_role);
    if($ROOM->date > 2 && ! $SELF->IsRole('yama_necromancer')){ //��ǽ���
      OutputSelfAbilityResult(strtoupper($SELF->main_role) . '_RESULT');
    }
  }
  elseif($SELF->IsRole('medium')){ //���
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 1) OutputSelfAbilityResult('MEDIUM_RESULT'); //�������
  }
  elseif($SELF->IsRoleGroup('priest')){ //�ʺ׷�
    $ROLE_IMG->Output($SELF->IsRole('crisis_priest') ? 'human' : $SELF->main_role);
    switch($SELF->main_role){ //�򿦤˱�����������̤�ɽ��
    case 'priest': //�ʺ�
      if($ROOM->date > 3 && ($ROOM->date % 2) == 0) OutputSelfAbilityResult('PRIEST_RESULT');
      break;

    case 'bishop_priest': //�ʶ�
      if($ROOM->date > 2 && ($ROOM->date % 2) == 1) OutputSelfAbilityResult('BISHOP_PRIEST_RESULT');
      break;

    case 'border_priest': //������
      if($ROOM->date > 2) OutputSelfAbilityResult('BORDER_PRIEST_RESULT');
      break;

    case 'crisis_priest': //�¸���
      if($ROOM->date > 1) OutputSelfAbilityResult('CRISIS_PRIEST_RESULT');
      break;
    }
  }
  elseif($SELF->IsRoleGroup('guard')){ //��ͷ�
    $ROLE_IMG->Output($SELF->IsRole('dummy_guard') ? 'guard' : $SELF->main_role);
    if($ROOM->date > 2){
      OutputSelfAbilityResult('GUARD_SUCCESS'); //��ҷ��
      OutputSelfAbilityResult('GUARD_HUNTED');  //�����
    }
    //�����ɼ
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO');
  }
  elseif($SELF->IsRole('reporter')){ //�֥�
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 2) OutputSelfAbilityResult('REPORTER_SUCCESS'); //���Է��
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO');
    }
  }
  elseif($SELF->IsRole('anti_voodoo')){ //���
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 2) OutputSelfAbilityResult('ANTI_VOODOO_SUCCESS'); //��ҷ��
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO');
    }
  }
  elseif($SELF->IsCommon()){ //��ͭ�Է�
    $ROLE_IMG->Output($SELF->IsRole('dummy_common') ? 'common' : $SELF->main_role);

    //��־�������
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if($SELF->IsRole('dummy_common')){
	if($user->IsDummyBoy()) $stack[] = $user->handle_name;
      }
      elseif($user->IsCommon(true)){
	$stack[] = $user->handle_name;
      }
    }
    OutputPartner($stack, 'common_partner'); //��֤�ɽ��
    unset($stack);
  }
  elseif($SELF->IsRoleGroup('cat')){ //ǭ����
    $ROLE_IMG->Output($SELF->main_role);

    if(! $ROOM->IsOpenCast()){
      if($ROOM->date > 2) OutputSelfAbilityResult('POISON_CAT_RESULT'); //�������
      if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
	OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($SELF->IsRoleGroup('pharmacist')){ //���շ�
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 2) OutputSelfAbilityResult('PHARMACIST_RESULT'); //������
  }
  elseif($SELF->IsRoleGroup('assassin')){ //�Ż��Է�
    $ROLE_IMG->Output($SELF->IsRole('eclipse_assassin') ? 'assassin' : $SELF->main_role);
    if($ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($SELF->IsRoleGroup('scanner')){ //���Ȥ��
    $ROLE_IMG->Output($SELF->main_role);

    if($SELF->IsRole('mind_scanner', 'evoke_scanner')){
      if($ROOM->date == 1){
	if($ROOM->IsNight()){ //���������ɼ
	  OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
	}
      }
      else{ //2���ܰʹߡ���ʬ�Υ��ȥ��/���󤻤�ɽ��
	$stack = array();
	$role = $SELF->IsRole('mind_scanner') ? 'mind_read' : 'mind_evoke';
	foreach($USERS->rows as $user){
	  if($user->IsPartner($role, $SELF->user_no)) $stack[] = $user->handle_name;
	}
	OutputPartner($stack, 'mind_scanner_target');
	unset($stack);
      }
    }
  }
  elseif($SELF->IsRoleGroup('doll')){ //�峤�ͷ���
    $ROLE_IMG->Output($SELF->main_role);
    if(! $SELF->IsRole('doll_master')){ //���ɽ��
      $stack = array();
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsRole('doll_master')){
	  $stack['master'][] = $user->handle_name;
	}
	elseif($user->IsDoll()){
	  $stack['doll'][] = $user->handle_name;
	}
      }
      OutputPartner($stack['master'], 'doll_master_list'); //�ͷ�����
      if($SELF->IsRole('friend_doll')) OutputPartner($stack['doll'], 'doll_partner'); //ʩ�����ͷ�
      unset($stack);
    }
  }
  elseif($SELF->IsWolf()){ //��ϵ��
    $ROLE_IMG->Output($SELF->main_role);

    //��־�������
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if($user->IsWolf(true)){
	$stack['wolf'][] = $USERS->GetHandleName($user->uname, true);
      }
      elseif($user->IsRole('whisper_mad')){
	$stack['mad'][] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious', 'scarlet_fox')){
	$stack['unconscious'][] = $user->handle_name;
      }
    }
    if($SELF->IsWolf(true)){
      OutputPartner($stack['wolf'], 'wolf_partner'); //��֤�ɽ��
      OutputPartner($stack['mad'], 'mad_partner'); //�񤭶��ͤ�ɽ��
    }
    if($ROOM->IsNight()){ //�����̵�ռ��ȹȸѤ�ɽ��
      OutputPartner($stack['unconscious'], 'unconscious_list');
    }
    unset($stack);

    switch($SELF->main_role){ //�ü�ϵ�ν���
    case 'tongue_wolf': //���ϵ
      if($ROOM->date > 1) OutputSelfAbilityResult('TONGUE_WOLF_RESULT'); //���߷��
      break;

    case 'sex_wolf': //��ϵ
      if($ROOM->date > 1) OutputSelfAbilityResult('SEX_WOLF_RESULT'); //���̾���
      break;

    case 'possessed_wolf': //��ϵ
      if($ROOM->date > 1) OutputPossessedTarget(); //���ߤ�������ɽ��
      break;

    case 'sirius_wolf': //ŷϵ
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
      $stack = array();
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)) $stack[] = $USERS->GetHandleName($user->uname, true);
      }
      OutputPartner($stack, 'wolf_partner');
      unset($stack);
      break;

    case 'whisper_mad': //�񤭶���
      //ϵ���񤭶��ͤ�ɽ��
      $stack = array();
      foreach($USERS->rows as $user){
	if($user->IsSelf() || $user->IsRole('silver_wolf')) continue;
	if($user->IsWolf()){
	  $stack['wolf'][] = $USERS->GetHandleName($user->uname, true);
	}
	elseif($user->IsRole('whisper_mad')){
	  $stack['mad'][] = $user->handle_name;
	}
      }
      OutputPartner($stack['wolf'], 'wolf_partner');
      OutputPartner($stack['mad'], 'mad_partner');
      unset($stack);
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
      if($ROOM->date > 2) OutputPossessedTarget(); //���ߤ�������ɽ��
      if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
      }
      break;
    }
  }
  elseif($SELF->IsFox()){ //�Ÿѷ�
    $ROLE_IMG->Output($SELF->main_role);

    if(! $SELF->IsLonely()){ //���ɽ��
      $stack = array();
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsFox(true)){
	  $stack['fox'][] = $user->handle_name;
	}
	elseif($user->IsChildFox() || $user->IsRole('scarlet_wolf')){
	  $stack['child_fox'][] = $user->handle_name;
	}
      }
      OutputPartner($stack['fox'], 'fox_partner'); //�Ÿѷ�
      OutputPartner($stack['child_fox'], 'child_fox_partner'); //�Ҹѷ�
      unset($stack);
    }

    if($SELF->IsChildFox()){ //�Ҹѷ�
      if($ROOM->date > 1) OutputSelfAbilityResult('CHILD_FOX_RESULT'); //�ꤤ���
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
	if($ROOM->date > 2) OutputSelfAbilityResult('POISON_CAT_RESULT'); //�������
	if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){ //�����ɼ
	  OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	}
	break;

      case 'possessed_fox': //���
	if($ROOM->date > 2) OutputPossessedTarget(); //���ߤ�������ɽ��
	if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	  OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
	}
	break;
      }
    }

    if($ROOM->date > 1 && ! ($SELF->IsRole('white_fox', 'poison_fox') || $SELF->IsChildFox())){
      OutputSelfAbilityResult('FOX_EAT'); //�����å�������ɽ��
    }
  }
  elseif($SELF->IsRoleGroup('chiroptera')){ //������
    if($SELF->IsRole('dummy_chiroptera')){ //̴�ᰦ��
      $ROLE_IMG->Output('self_cupid');

      //��ʬ������Ǥä�(�Ĥ��)������ (��ʬ���ȴޤ�) ��ɽ��
      $stack = $SELF->GetPartner('dummy_chiroptera');
      if(is_array($stack)){
	$stack[] = $SELF->user_no;
	asort($stack);
	$stack_pair = array();
	foreach($stack as $id) $stack_pair[] = $USERS->ById($id)->handle_name;
	OutputPartner($stack_pair, 'cupid_pair');
	unset($stack, $stack_pair);
      }

      if($ROOM->date == 1 && $ROOM->IsNight()){
	OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //���������ɼ
      }
    }
    else{
      $ROLE_IMG->Output($SELF->main_role);
    }
  }
  elseif($SELF->IsRoleGroup('fairy')){ //������
    $ROLE_IMG->Output($SELF->main_role);
    if($SELF->IsRole('mirror_fairy')){ //������
      if($ROOM->date == 1 && $ROOM->IsNight()){
	OutputVoteMessage('fairy-do', 'fairy_do', 'CUPID_DO'); //���������ɼ
      }
    }
    else{
      if($ROOM->IsNight()) OutputVoteMessage('fairy-do', 'fairy_do', 'FAIRY_DO'); //�����ɼ
    }
  }
  elseif($SELF->IsRole('incubate_poison')){ //���Ǽ�
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif($SELF->IsRole('guide_poison')){ //Ͷ�Ǽ�
    $ROLE_IMG->Output($SELF->main_role);
  }
  elseif($SELF->IsRole('chain_poison')){ //Ϣ�Ǽ�
    $ROLE_IMG->Output('human');
  }
  elseif($SELF->IsRoleGroup('poison')) $ROLE_IMG->Output('poison'); //���ǼԷ�
  elseif($SELF->IsRoleGroup('cupid', 'angel')){ //���塼�ԥåɷ�
    $ROLE_IMG->Output($SELF->main_role);

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ��
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $SELF->user_no)) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'cupid_pair');
    unset($stack);

    if($SELF->IsRole('ark_angel') && $ROOM->date == 2){
      OutputSelfAbilityResult('SYMPATHY_RESULT'); //��ŷ�Ȥ϶����Ծ�������Ƹ��뤳�Ȥ������
    }
    //���������ɼ
    if($ROOM->date == 1 && $ROOM->IsNight()) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO');
  }
  elseif($SELF->IsRoleGroup('jealousy')) $ROLE_IMG->Output($SELF->main_role); //��ɱ
  elseif($SELF->IsRole('quiz')){ //�����
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->Output('quiz_chaos');
  }
  elseif($SELF->IsRoleGroup('mania')){ //���åޥ˥�
    $ROLE_IMG->Output($SELF->IsRole('dummy_mania') ? 'soul_mania' : $SELF->main_role);
    //���������ɼ
    if($ROOM->date == 1 && $ROOM->IsNight()) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO');
    if($ROOM->date == 2 && $SELF->IsRole('soul_mania', 'dummy_mania')){
      OutputSelfAbilityResult('MANIA_RESULT'); //���üԡ�̴�����Υ��ԡ����
    }
  }

  //-- ���������Ǥ�� --//
  $fix_display_list = array(); //���ɽ�������򿦥ꥹ��

  //�����åޥ˥��Υ��ԡ���̤�ɽ��
  if($SELF->IsRoleGroup('copied') && ($ROOM->date == 2 || $ROOM->date == 4)){
    OutputSelfAbilityResult('MANIA_RESULT');
  }
  array_push($fix_display_list, 'copied', 'copied_trick', 'copied_soul', 'copied_teller');

  //ǽ���Ӽ� (���ϵ��櫻�)
  if($SELF->IsRole('lost_ability')) $ROLE_IMG->Output('lost_ability');
  $fix_display_list[] = 'lost_ability';

  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){ //����
    foreach($USERS->rows as $user){
      if(! $user->IsSelf() &&
	 ($user->IsPartner('lovers', $SELF->partner_list) ||
	  $SELF->IsPartner('dummy_chiroptera', $user->user_no))){
	$lovers_partner[] = $USERS->GetHandleName($user->uname, true);
      }
    }
    OutputPartner($lovers_partner, 'partner_header', 'lovers_footer');
  }
  $fix_display_list[] = 'lovers';

  if($SELF->IsRole('challenge_lovers')){ //����
    if($ROOM->date > 1) $ROLE_IMG->Output('challenge_lovers'); //ɽ����2���ܰʹ�
  }
  $fix_display_list[] = 'challenge_lovers';

  if($SELF->IsRole('possessed_exchange')){ //�����
    //���ߤ�������ɽ��
    $target_list = $SELF->partner_list['possessed_exchange'];
    if(is_array($target_list)){
      $target = $USERS->ByID(array_shift($target_list))->handle_name;
      if($target != ''){
	if($ROOM->date < 3){
	  OutputAbilityResult('exchange_header', $target, 'exchange_footer');
	}
	else{
	  OutputAbilityResult('partner_header', $SELF->handle_name, 'possessed_target');
	}
      }
    }
  }
  $fix_display_list[] = 'possessed_exchange';

  if($SELF->IsRole('febris')){ //Ǯ��
    $dead_date = max($SELF->GetPartner('febris'));
    if($ROOM->date == $dead_date){
      OutputAbilityResult('febris_header', $dead_date, 'sudden_death_footer');
    }
  }
  $fix_display_list[] = 'febris';

  if($SELF->IsRole('death_warrant')){ //������
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

//���ߤ�������ɽ������
function OutputPossessedTarget(){
  global $USERS, $SELF;

  $type = 'possessed_target';
  if(is_null($stack = $SELF->GetPartner($type))) return;

  $target = $USERS->ByID($stack[max(array_keys($stack))])->handle_name;
  if($target != '') OutputAbilityResult('partner_header', $target, $type);
}

//�ġ���ǽ��ȯư��̤�ɽ������
/*
  �����ν����ϡ�HN �˥��֤�����ȥѡ����˼��Ԥ���
  ��¼���� HN ���饿�֤���������б��Ǥ��뤬��
  ���⤽�⤳�Τ褦�ʥѡ����򤷤ʤ��Ȥ����ʤ� DB ��¤��
  ���꤬����Τǡ������Ǥ��ä��б����ʤ�
*/
function OutputSelfAbilityResult($action){
  global $RQ_ARGS, $ROOM, $SELF;

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

  case 'BORDER_PRIEST_RESULT':
    $type = 'mage';
    $header = 'border_priest_header';
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
  if($ROOM->test_mode){
    $stack = $RQ_ARGS->TestItems->system_message[$yesterday][$action];
    $result_list = is_array($stack) ? $stack : array();
  }
  else{
    $query = 'SELECT DISTINCT message FROM system_message WHERE room_no = ' .
      "{$ROOM->id} AND date = {$yesterday} AND type = '{$action}'";
    $result_list = FetchArray($query);
  }

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
