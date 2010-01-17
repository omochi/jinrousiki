<?php
//ǽ�Ϥμ���Ȥ������������
function OutputAbility(){
  global $GAME_CONF, $ROLE_IMG, $MESSAGE, $ROOM, $USERS, $SELF;

  //��������Τ�ɽ������
  if(! $ROOM->IsPlaying()) return false;

  if($SELF->IsDead()){ //��˴������ǽ�Ϥ�ɽ�����ʤ�
    echo '<span class="ability ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  $is_first_night = ($ROOM->IsNight() && $ROOM->date == 1);
  $is_after_first_night = ($ROOM->IsNight() && $ROOM->date > 1);

  if($SELF->IsRole('human', 'suspect', 'unconscious')){ //¼�͡��Կ��ԡ�̵�ռ�
    $ROLE_IMG->DisplayImage('human');
  }
  elseif($SELF->IsRoleGroup('mage')){ //�ꤤ��
    $ROLE_IMG->DisplayImage($SELF->IsRole('dummy_mage') ? 'mage' : $SELF->main_role);

    //�ꤤ��̤�ɽ��
    $action = 'MAGE_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
	break;
      }
    }

    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //�����ɼ
  }
  elseif($SELF->IsRole('voodoo_killer')){ //���ۻ�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //��ҷ�̤�ɽ��
    $sql = GetAbilityActionResult('VOODOO_KILLER_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'voodoo_killer_success');
	break;
      }
    }

    //�����ɼ
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
  elseif($SELF->IsRole('yama_necromancer')) $ROLE_IMG->DisplayImage($SELF->main_role); //����
  elseif($SELF->IsRoleGroup('necromancer') || $SELF->IsRole('medium')){ //��ǽ��
    if($SELF->IsRoleGroup('necromancer')){
      $role_name = 'necromancer';
      $result    = 'necromancer_result';
      $action    = 'NECROMANCER_RESULT';
      switch($SELF->main_role){
      case 'soul_necromancer':
	$role_name = $SELF->main_role;
	$action    = 'SOUL_' . $action;
	break;

      case 'dummy_necromancer':
	$action = 'DUMMY_' . $action;
	break;
      }
    }
    else{
      $role_name = 'medium';
      $result    = 'medium_result';
      $action    = 'MEDIUM_RESULT';
    }
    $ROLE_IMG->DisplayImage($role_name);

    //Ƚ���̤�ɽ��
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif($SELF->IsRole('priest')){ //�ʺ�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //Ƚ���̤�ɽ��
    $sql = GetAbilityActionResult('PRIEST_RESULT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      $result = mysql_result($sql, $i, 0);
      OutputAbilityResult('priest_header', $result, 'priest_footer');
    }
  }
  elseif($SELF->IsRoleGroup('guard')){ //��ͷ�
    $ROLE_IMG->DisplayImage($SELF->IsRole('dummy_guard') ? 'guard' : $SELF->main_role);

    //��ҷ�̤�ɽ��
    $sql = GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'guard_success');
	break;
      }
    }

    $sql = GetAbilityActionResult('GUARD_HUNTED');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'guard_hunted');
	break;
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO'); //�����ɼ
  }
  elseif($SELF->IsRole('reporter')){ //�֥�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //���Է�̤�ɽ��
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	$target .= ' ����� ' . $wolf_handle;
	OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
	break;
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO'); //�����ɼ
  }
  elseif($SELF->IsRole('anti_voodoo')){ //���
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //��ҷ�̤�ɽ��
    $sql = GetAbilityActionResult('ANTI_VOODOO_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'anti_voodoo_success');
	break;
      }
    }

    if($is_after_first_night){
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO'); //�����ɼ
    }
  }
  elseif($SELF->IsRoleGroup('common')){ //��ͭ��
    $ROLE_IMG->DisplayImage('common');

    //��֤�ɽ��
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if(($SELF->IsRole('common') && $user->IsRole('common')) ||
	 ($SELF->IsRole('dummy_common') && $user->IsDummyBoy())){
	$common_partner[] = $user->handle_name;
      }
    }
    OutputPartner($common_partner, 'common_partner');
  }
  elseif($SELF->IsRole('assassin')){ //�Ż���
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($is_after_first_night){ //�����ɼ
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($SELF->IsRole('mind_scanner')){ //���Ȥ�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    if($ROOM->date > 1){ //2���ܰʹߡ���ʬ�������ɤ�Ǥ�������ɽ��
      foreach($USERS->rows as $user){
	if($user->IsPartner('mind_read', $SELF->user_no)){
	  $mind_scanner_target[] = $user->handle_name;
	}
      }
      OutputPartner($mind_scanner_target, 'mind_scanner_target');
    }

    if($is_first_night){ //���������ɼ
      OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
    }
  }
  elseif($SELF->IsRoleGroup('jealousy')) $ROLE_IMG->DisplayImage($SELF->main_role); //��ɱ��
  elseif($SELF->IsWolf()){ //��ϵ��
    $ROLE_IMG->DisplayImage($SELF->main_role);

    foreach($USERS->rows as $user){ //��־�������
      if($user->IsSelf() || $user->IsRole('silver_wolf')) continue;
      if($user->IsWolf()){
	$wolf_partner[] = $USERS->GetVirtualHandleName($user->uname);
      }
      elseif($user->IsRole('whisper_mad')){
	$mad_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious', 'scarlet_fox')){
	$unconscious_list[] = $user->handle_name;
      }
    }
    if(! $SELF->IsRole('silver_wolf')){
      OutputPartner($wolf_partner, 'wolf_partner'); //��֤�ɽ��
      OutputPartner($mad_partner, 'mad_partner'); //�񤭶��ͤ�ɽ��
    }
    if($ROOM->IsNight()){ //�����̵�ռ��ȹȸѤ�ɽ��
      OutputPartner($unconscious_list, 'unconscious_list');
    }

    if($SELF->IsRole('tongue_wolf')){ //���ϵ�γ��߷�̤�ɽ��
      $action = 'TONGUE_WOLF_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult('wolf_result', $target, 'result_' . $target_role);
	  break;
	}
      }
    }

    if($SELF->IsRole('possessed_wolf')){
      $possessed_target = $SELF->partner_list['possessed_target'];
      do{
	if(! is_array($possessed_target)) break;
	$date = max(array_keys($possessed_target));
	$target = $USERS->ByID($possessed_target[$date])->handle_name;
	if($target == '') break;
	OutputAbilityResult('partner_header', $target, 'possessed_target');
      }while(false);
    }

    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //�����ɼ
  }
  elseif($SELF->IsRoleGroup('mad')){ //���ͷ�
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($SELF->IsRole('fanatic_mad')){ //������
      //ϵ��ɽ��
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)){
	  $wolf_partner[] = $USERS->GetVirtualHandleName($user->uname);
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
    }
    elseif($SELF->IsRole('whisper_mad')){ //�񤭶���
      //ϵ���񤭶��ͤ�ɽ��
      foreach($USERS->rows as $user){
	if($user->IsSelf() || $user->IsRole('silver_wolf')) continue;
	if($user->IsWolf()){
	  $wolf_partner[] = $USERS->GetVirtualHandleName($user->uname);
	}
	elseif($user->IsRole('whisper_mad')){
	  $mad_partner[] = $user->handle_name;
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
      OutputPartner($mad_partner, 'mad_partner');
    }
    elseif($SELF->IsRole('jammer_mad') && $ROOM->IsNight()){ //����
      OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
    }
    elseif($SELF->IsRole('voodoo_mad') && $ROOM->IsNight()){ //���ѻ�
      OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
    }
    elseif($SELF->IsRole('dream_eater_mad') && $ROOM->IsNight()){ //��
      if($is_after_first_night) OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
    }
    elseif($SELF->IsActiveRole('trap_mad') && $is_after_first_night){ //櫻�
      OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
  elseif($SELF->IsFox()){ //�Ÿѷ�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    foreach($USERS->rows as $user){
      if($user->IsSelf() || $user->IsRole('silver_fox')) continue;
      if($user->IsRole('child_fox', 'scarlet_wolf')){
	$child_fox_partner[] = $user->handle_name;
      }
      elseif($user->IsFox()){
	$fox_partner[] = $user->handle_name;
      }
    }
    if(! $SELF->IsRole('silver_fox')){
      OutputPartner($fox_partner, 'fox_partner'); //�ŸѤ���֤�ɽ��
      OutputPartner($child_fox_partner, 'child_fox_partner'); //�ҸѤ���֤�ɽ��
    }

    if($SELF->IsRole('child_fox')){
      //�ꤤ��̤�ɽ��
      $action = 'CHILD_FOX_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
	  break;
	}
      }

      if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //�����ɼ
    }
    elseif($SELF->IsRole('voodoo_fox') && $ROOM->IsNight()){ //����
      OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
    }

    if(! $SELF->IsRole('white_fox', 'poison_fox', 'child_fox')){
      //�Ѥ�����줿��å�������ɽ��
      $sql = GetAbilityActionResult('FOX_EAT');
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	if($SELF->handle_name == mysql_result($sql, $i, 0)){
	  OutputAbilityResult('fox_targeted', NULL);
	  break;
	}
      }
    }
  }
  elseif($SELF->IsRoleGroup('chiroptera')){ //������
    if($SELF->IsRole('dummy_chiroptera')){
      $ROLE_IMG->DisplayImage('self_cupid');

      //��ʬ������Ǥä�(�Ĥ��)������ (��ʬ���ȴޤ�) ��ɽ��
      $dummy_lovers_id = $SELF->partner_list['dummy_chiroptera'];
      if(is_array($dummy_lovers_id)){
	$cupid_id = array($SELF->user_no, $dummy_lovers_id[0]);
	asort($cupid_id);
	$cupid_pair = array();
	foreach($cupid_id as $id) $cupid_pair[] = $USERS->ById($id)->handle_name;
	OutputPartner($cupid_pair, 'cupid_pair');
      }

      if($is_first_night) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //���������ɼ
    }
    else{
      $ROLE_IMG->DisplayImage($SELF->main_role);
    }
  }
  elseif($SELF->IsRole('incubate_poison')){ //���Ǽ�
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif($SELF->IsRole('poison_cat')){ //ǭ��
    $ROLE_IMG->DisplayImage($SELF->main_role);

    if(! $ROOM->IsOpenCast()){
      //������̤�ɽ��
      $action = 'POISON_CAT_RESULT';
      $sql    = GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($SELF->handle_name == $actor){
	  OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
	  break;
	}
      }

      if($is_after_first_night){ //�����ɼ
	OutputVoteMessage('poison-cat-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($SELF->IsRoleGroup('poison')) $ROLE_IMG->DisplayImage('poison'); //���ǼԷ�
  elseif($SELF->IsRole('pharmacist')){ //����
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //���Ƿ�̤�ɽ��
    $action = 'PHARMACIST_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'pharmacist_' . $result);
	break;
      }
    }
  }
  elseif($SELF->IsRoleGroup('cupid')){ //���塼�ԥåɷ�
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ��
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $SELF->user_no)){
	$cupid_pair[] = $user->handle_name;
      }
    }
    OutputPartner($cupid_pair, 'cupid_pair');

    if($is_first_night) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //���������ɼ
  }
  elseif($SELF->IsRole('quiz')){ //�����
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->DisplayImage('quiz_chaos');
  }
  elseif($SELF->IsRoleGroup('mania')){ //���åޥ˥�
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($is_first_night) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO'); //���������ɼ
  }

  //-- ���������Ǥ�� --//
  $fix_display_list = array(); //���ɽ�������򿦥ꥹ��

  if($SELF->IsRole('copied')){ //�����åޥ˥�
    //���ԡ���̤�ɽ��
    $action = 'MANIA_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'result_' . $target_role);
	break;
      }
    }
  }
  $fix_display_list[] = 'copied';

  //ǽ���Ӽ� (���ϵ��櫻�)
  if($SELF->IsRole('lost_ability')) $ROLE_IMG->DisplayImage('lost_ability');
  $fix_display_list[] = 'lost_ability';

  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){ //����
    $dummy_lovers_list = $SELF->partner_list['dummy_chiroptera'];
    if(is_array($dummy_lovers_list)) $dummy_lovers_id = $dummy_lovers_list[0];
    foreach($USERS->rows as $user){
      if(! $user->IsSelf() &&
	 ($user->IsPartner('lovers', $SELF->partner_list) ||
	  $user->user_no == $dummy_lovers_id)){
	$lovers_partner[] = $user->handle_name;
      }
    }
    OutputPartner($lovers_partner, 'partner_header', 'lovers_footer');
  }
  $fix_display_list[] = 'lovers';

  //����������������򿦤�ɽ��
  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  if($virtual_self->IsRole('mind_open')) $ROLE_IMG->DisplayImage('mind_open');
  $fix_display_list[] = 'mind_open';

  if($ROOM->date > 1){ //���ȥ��Ϥ�ɽ���� 2 ���ܰʹ�
    if($virtual_self->IsRole('mind_read')) $ROLE_IMG->DisplayImage('mind_read');

    if($virtual_self->IsRole('mind_receiver')){
      $ROLE_IMG->DisplayImage('mind_receiver');

      $mind_scanner_target = array();
      foreach($virtual_self->partner_list['mind_receiver'] as $this_no){
	$mind_scanner_target[] = $USERS->ById($this_no)->handle_name;
      }
      OutputPartner($mind_scanner_target, 'mind_scanner_target');
    }

    if($virtual_self->IsRole('mind_friend')){
      $ROLE_IMG->DisplayImage('mind_friend');

      $mind_friend = array();
      foreach($USERS->rows as $user){
	if(! $user->IsSameUser($virtual_self->uname) &&
	   $user->IsPartner('mind_friend', $virtual_self->partner_list)){
	  $mind_friend[] = $user->handle_name;
	}
      }
      OutputPartner($mind_friend, 'mind_friend_list');
    }
  }
  array_push($fix_display_list, 'mind_read', 'mind_receiver', 'mind_friend');

  //����ʹߤϥ�������������ץ����αƶ��������
  if($ROOM->IsOption('secret_sub_role')) return;

  $role_keys_list    = array_keys($GAME_CONF->sub_role_list);
  $hide_display_list = array('decide', 'plague', 'good_luck', 'bad_luck');
  $not_display_list  = array_merge($fix_display_list, $hide_display_list);
  $display_list      = array_diff($role_keys_list, $not_display_list);
  $target_list       = array_intersect($display_list, array_slice($virtual_self->role_list, 1));

  foreach($target_list as $this_role){
    $ROLE_IMG->DisplayImage($this_role);
  }
}

//��֤�ɽ������
function OutputPartner($partner_list, $header, $footer = NULL){
  global $ROLE_IMG;

  if(count($partner_list) < 1) return false; //��֤����ʤ����ɽ�����ʤ�

  $str = '<table class="ability-partner"><tr>'."\n" .
    '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n" . '<td>��';
  foreach($partner_list as $partner) $str .= $partner . '���󡡡�';
  $str .= '</td>'."\n";
  if($footer) $str .= '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo $str . '</tr></table>'."\n";
}

//ǽ��ȯư��̤�ǡ����١������䤤��碌��
function GetAbilityActionResult($action){
  global $ROOM;

  $yesterday = $ROOM->date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = {$ROOM->id}
			AND date = $yesterday AND type = '$action'");
}

//ǽ��ȯư��̤�ɽ������
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  if($target) echo '<td>' . $target . '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
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
?>
