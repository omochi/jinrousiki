<?php
//��ɼ���ޥ�ɤ����äƤ��뤫�����å�
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return true;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return true;

  OutputVoteResult('̵������ɼ�Ǥ�');
}

//��ɼ��̽���
function OutputVoteResult($sentence, $unlock = false, $reset_vote = false){
  global $SERVER_CONF, $RQ_ARGS;

  if($reset_vote) DeleteVote(); //���ޤǤ���ɼ���������
  $title  = $SERVER_CONF->title . ' [��ɼ���]';
  $header = '<div align="center"><a id="#game_top"></a>';
  $footer = '<br>'."\n" . $RQ_ARGS->back_url . '</div>';
  OutputActionResult($title, $header . $sentence . $footer, '', $unlock);
}

//�Ϳ��ȥ����४�ץ����˱������򿦥ơ��֥���֤� (���顼�����ϻ���)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $CAST_CONF, $ROOM;

  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  $role_list = $CAST_CONF->role_list[$user_count]; //�Ϳ��˱���������ꥹ�Ȥ����
  if($role_list == NULL){ //�ꥹ�Ȥ�̵ͭ������å�
    $sentence = $user_count . '�ͤ����ꤵ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }
  $option_role_list = explode(' ', $option_role);
  $gerd = in_array('gerd', $option_role_list);
  //$gerd = true; //�ƥ�����
  //PrintData($option_role_list);

  if($ROOM->IsQuiz()){ //������¼
    $quiz_role_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false)
	$quiz_role_list['wolf'] += (int)$value;
      elseif(strpos($key, 'mad') !== false)
	$quiz_role_list['mad'] += (int)$value;
      elseif(strpos($key, 'common') !== false)
	$quiz_role_list['common'] += (int)$value;
      elseif(strpos($key, 'fox') !== false)
	$quiz_role_list['fox'] += (int)$value;
      else
	$quiz_role_list['human'] += (int)$value;
    }
    $quiz_role_list['human']--;
    $quiz_role_list['quiz'] = 1;
    $role_list = $quiz_role_list;
  }
  elseif(in_array('duel', $option_role_list)){ //��Ʈ¼
    $role_list = $CAST_CONF->SetDuel($user_count);
  }
  elseif($ROOM->IsOptionGroup('chaos')){ //�����
    $random_role_list = array();

    if($ROOM->IsOption('chaos')){
      $base_name = 'chaos';
    }
    elseif($ROOM->IsOption('chaosfull')){
      $base_name = 'chaosfull';
    }
    elseif($ROOM->IsOption('chaos_hyper')){
      $base_name = 'chaos_hyper';
    }
    $fix_role_name = $base_name . '_fix_role_list';
    $wolf_name     = $base_name . '_wolf_list';
    $fox_name      = $base_name . '_fox_list';
    $random_name   = $base_name . '_random_role_list';
    $human_name    = $base_name . '_replace_human_role_list';

    $chaos_fix_role_list = $CAST_CONF->$fix_role_name;
    $random_wolf_list    = $CAST_CONF->GenerateRandomList($CAST_CONF->$wolf_name);
    $random_fox_list     = $CAST_CONF->GenerateRandomList($CAST_CONF->$fox_name);
    $random_full_list    = $CAST_CONF->GenerateRandomList($CAST_CONF->$random_name);
    $replace_human_list  = $CAST_CONF->GenerateRandomList($CAST_CONF->$human_name);
    //PrintData(array_sum($CAST_CONF->$random_name));

    //-- �Ǿ����� --//
    //õ��¼�ʤ�����Ȥ�õ����ɲä���
    if(in_array('detective', $option_role_list) &&
       is_null($chaos_fix_role_list['detective_common'])){
      $chaos_fix_role_list['detective_common'] = 1;
    }

    //����ȷ��⡼�ɤʤ�����Ȥ�¼�ͤ��ɲä���
    if($gerd && is_null($chaos_fix_role_list['human'])){
      $chaos_fix_role_list['human'] = 1;
    }

    foreach($chaos_fix_role_list as $key => $value){ //�Ǿ������ѥꥹ��
      $fix_role_group_list[DistinguishRoleGroup($key)] = $value;
    }

    //��ϵ
    //PrintData($random_wolf_list);
    //$CAST_CONF->RateToProbability($CAST_CONF->$wolf_name); //�ƥ�����

    $add_count = round($user_count / $CAST_CONF->chaos_min_wolf_rate) - $fix_role_group_list['wolf'];
    $CAST_CONF->AddRandom($random_role_list, $random_wolf_list, $add_count);
    //PrintData($random_role_list);

    //�Ÿ�
    //PrintData($random_fox_list);
    //$CAST_CONF->RateToProbability($CAST_CONF->$fox_name); //�ƥ�����

    $add_count = floor($user_count / $CAST_CONF->chaos_min_fox_rate) - $fix_role_group_list['fox'];
    $CAST_CONF->AddRandom($random_role_list, $random_fox_list, $add_count);
    //PrintData($random_role_list);

    //-- ���������� --//
    //PrintData($random_full_list);
    //$CAST_CONF->RateToProbability($CAST_CONF->$random_name); //�ƥ�����
    $add_count = $user_count - (array_sum($random_role_list) + array_sum($chaos_fix_role_list));
    $CAST_CONF->AddRandom($random_role_list, $random_full_list, $add_count);
    //PrintData($random_role_list);

    //������ȸ������
    $role_list = $random_role_list;
    foreach($chaos_fix_role_list as $key => $value){
      $role_list[$key] += (int)$value;
    }
    //PrintData($role_list, '1st_list');

    //�򿦥��롼����˽���
    foreach($role_list as $key => $value){
      $role_group = DistinguishRoleGroup($key);
      $role_group_list->{$role_group}[$key] = $value;
    }
    foreach($random_role_list as $key => $value){ //�����ѥꥹ��
      $role_group = DistinguishRoleGroup($key);
      $random_role_group_list->{$role_group}[$key] = $value;
    }

    //-- �������� --//
    foreach($CAST_CONF->chaos_role_group_rate_list as $name => $rate){
      if(! (is_array($role_group_list->$name) && is_array($random_role_group_list->$name))){
	continue;
      }
      $over_count = array_sum($role_group_list->$name) - round($user_count * $rate);
      //if($over_count > 0) PrintData($over_count, $name); //�ƥ�����
      for(; $over_count > 0; $over_count--){
	if(array_sum($random_role_group_list->$name) < 1) break;
	//PrintData($random_role_group_list->$name, "����$over_count: before");
	arsort($random_role_group_list->$name);
	//PrintData($random_role_group_list->$name, "����$over_count: after");
	$key = key($random_role_group_list->$name);
	//PrintData($key, "����target");
	$random_role_group_list->{$name}[$key]--;
	$role_list[$key]--;
	$role_list['human']++;
	//PrintData($random_role_group_list->$name, "����$over_count: delete");

	//0 �ˤʤä��򿦤ϥꥹ�Ȥ������
	if($role_list[$key] < 1) unset($role_list[$key]);
	if($random_role_group_list->{$name}[$key] < 1){
	  unset($random_role_group_list->{$name}[$key]);
	}
      }
    }
    //PrintData($role_list, '2nd_list');

    //���åޥ˥�¼�ʳ��ʤ������ʾ��¼�ͤ��̤��򿦤˿����֤�
    if(count(array_intersect(array('full_mania', 'full_chiroptera', 'full_cupid', 'replace_human'),
			     $option_role_list)) == 0){
      $over_count = $role_list['human'] - round($user_count * $CAST_CONF->chaos_max_human_rate);
      if($over_count > 0){
	$CAST_CONF->AddRandom($role_list, $replace_human_list, $over_count);
	$role_list['human'] -= $over_count;
	//PrintData($role_list, '3rd_list');
      }
    }
  }
  elseif($ROOM->IsOption('chaos')){ //���� //��12������Ի���
    //-- �ƿرĤοͿ������ (�Ϳ� = �ƿͿ��νи�Ψ) --//
    $role_list = array(); //�����ꥻ�å�

    //��ϵ�ر�
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 5){
      $wolf_count = 1;
    }
    elseif($user_count < 8){ //1:2 = 80:20
      $wolf_count = ($rand <= 80 ? 1 : 2);
    }
    elseif($user_count < 16){ //1:2:3 = 15:70:15
      $wolf_count = 1;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
    }
    elseif($user_count < 21){ //1:2:3:4:5 = 5:10:70:10:5
      $wolf_count = 1;
      if($rand >  5) $wolf_count++;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
      if($rand > 95) $wolf_count++;
    }
    else{ //�ʸ塢5�������뤴�Ȥ� 1�ͤ�������
      $wolf_count = floor(($user_count - 20) / 5) + 1;
      if($rand >  5) $wolf_count++;
      if($rand > 15) $wolf_count++;
      if($rand > 85) $wolf_count++;
      if($rand > 95) $wolf_count++;
    }

    //�Ÿѿر�
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 8){
      $fox_count = 0;
    }
    elseif($user_count < 15){ //0:1 = 90:10
      $fox_count = ($rand <= 90 ? 0 : 1);
    }
    elseif($user_count < 23){ //1:2 = 90:10
      $fox_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $fox_count = ceil($user_count / 20) - 1;
      if($rand > 10) $fox_count++;
      if($rand > 90) $fox_count++;
    }

    //���Ϳر� (�¼����塼�ԥå�)
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 8){
      $lovers_count = 0;
    }
    elseif($user_count < 10){ //0:1 = 95:5
      $lovers_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1 = 70:30
      $lovers_count = ($rand <= 70 ? 0 : 1);
    }
    elseif($user_count < 23){ //0:1:2 = 5:90:5
      $lovers_count = 0;
      if($rand >  5) $lovers_count++;
      if($rand > 95) $lovers_count++;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      //����-1:����:����+1 = 5:90:5
      $lovers_count = floor($user_count / 20) - 1;
      if($rand >  5) $lovers_count++;
      if($rand > 95) $lovers_count++;
    }
    $role_list['cupid'] = $lovers_count;

    //¼�ͿرĤοͿ��򻻽�
    $human_count = $user_count - $wolf_count - $fox_count - $lovers_count;

    //��ϵ�Ϥ���������
    $special_wolf_count = 0; //�ü�ϵ�οͿ�
    $base_count = ceil($user_count / 15); //�ü�ϵȽ�����򻻽�
    for(; $base_count > 0; $base_count--){
      if(mt_rand(1, 100) <= $user_count) $special_wolf_count++; //���ÿͿ� % �γ�Ψ���ü�ϵ�и�
    }
    if($special_wolf_count > 0){ //�ü�ϵ�γ������
      //ϵ�������Ķ���Ƥ�������������
      if($special_wolf_count > $wolf_count) $special_wolf_count = $wolf_count;
      $wolf_count -= $special_wolf_count; //�ü�ϵ�ο������̾�ϵ�򸺤餹

      if($user_count <= 16){ //16��̤���ξ�����ϵ�Τ�
	if(mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
      elseif($user_count < 20){ //20��̤�������ϵ�и�
	if(mt_rand(1, 100) <= 40){
	  $role_list['tongue_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
      else{ //20�Ͱʾ�ʤ���ϵ�����Ƚ�ꤷ�Ƥ��Ф䤹������
	if(mt_rand(1, 100) <= $user_count){
	  $role_list['poison_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['tongue_wolf']++;
	  $special_wolf_count--;
	}
	if($special_wolf_count > 0 && mt_rand(1, 100) <= $user_count){
	  $role_list['cute_wolf']++;
	  $special_wolf_count--;
	}
	$role_list['boss_wolf'] = $special_wolf_count;
      }
    }
    $role_list['wolf'] = $wolf_count;

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
      $mage_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //1:2 = 95:5
      $mage_count = ($rand <= 95 ? 1 : 2);
    }
    elseif($user_count < 30){ //1:2 = 90:10
      $mage_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $mage_count = floor($user_count / 15) - 1;
      if($rand > 10) $mage_count++;
      if($rand > 90) $mage_count++;
    }

    //�ꤤ�Ϥ���������
    if($mage_count > 0 && $human_count >= $mage_count){
      $human_count -= $mage_count; //¼�ͿرĤλĤ�Ϳ�
      if($user_count < 16){ //16��̤���ξ����ü��ꤤ�դϤʤ�
	$role_list['mage'] = $mage_count;
      }
      else{ //���ÿͿ� % �Ǻ����ꤤ�դ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['soul_mage'] = 1;
	$role_list['mage'] = $mage_count - (int)$role_list['soul_mage'];
      }
    }

    //����οͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 9){ //0:1 = 70:30
      $medium_count = ($rand <= 70 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $medium_count = 0;
      if($rand > 10) $medium_count++;
      if($rand > 90) $medium_count++;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $medium_count = floor($user_count / 15) - 1;
      if($rand > 10) $medium_count++;
      if($rand > 90) $medium_count++;
    }
    if($cupid_count > 0 && $medium_count == 0) $medium_count++;

    //�������������
    if($medium_count > 0 && $human_count >= $medium_count){
      $human_count -= $medium_count; //¼�ͿرĤλĤ�Ϳ�
      $role_list['medium'] = $medium_count;
    }

    //��ǽ�ϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 9){ //0:1 = 10:90
      $necromancer_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //1:2 = 95:5
      $necromancer_count = ($rand <= 95 ? 1 : 2);
    }
    elseif($user_count < 30){ //1:2 = 90:10
      $necromancer_count = ($rand <= 90 ? 1 : 2);
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $necromancer_count = floor($user_count / 15) - 1;
      if($rand > 10) $necromancer_count++;
      if($rand > 90) $necromancer_count++;
    }

    //��ǽ�Ϥ���������
    if($necromancer_count > 0 && $human_count >= $necromancer_count){
      $human_count -= $necromancer_count; //¼�ͿرĤλĤ�Ϳ�
      $role_list['necromancer'] = $necromancer_count;
    }

    //���ͷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 10){ //0:1 = 30:70
      $mad_count = ($rand <= 30 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $mad_count = 0;
      if($rand > 10) $mad_count++;
      if($rand > 90) $mad_count++;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $mad_count = floor($user_count / 15) - 1;
      if($rand > 10) $mad_count++;
      if($rand > 90) $mad_count++;
    }

    //���ͷϤ���������
    if($mad_count > 0 && $human_count >= $mad_count){
      $human_count -= $mad_count; //¼�ͿرĤλĤ�Ϳ�
      if($user_count < 16){ //���͸���16��̤���ξ��϶����ԤϽи����ʤ�
	$role_list['mad'] = $mad_count;
	$role_list['fanatic_mad'] = 0;
      }
      else{ //���ÿͿ� % �Ƕ����Ԥ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['fanatic_mad'] = 1;
	$role_list['mad'] = $mad_count - (int)$role_list['fanatic_mad'];
      }
    }

    //��ͷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 11){ //0:1 = 10:90
      $guard_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 16){ //0:1:2 = 10:80:10
      $guard_count = 0;
      if($rand > 10) $guard_count++;
      if($rand > 90) $guard_count++;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $guard_count = floor($user_count / 15) - 1;
      if($rand > 10) $guard_count++;
      if($rand > 90) $guard_count++;
    }

    //��ͷϤ���������
    if($guard_count > 0 && $human_count >= $guard_count){
      $human_count -= $guard_count; //¼�ͿرĤλĤ�Ϳ�
      $special_guard_count = 0; //�ü��ͤοͿ�
      //16�Ͱʾ�ʤ��ü���Ƚ�����򻻽�
      $base_count = ($user_count >= 16 ? ceil($user_count / 15) : 0);
      for(; $base_count > 0; $base_count--){
	if(mt_rand(1, 100) <= $user_count) $special_guard_count++; //���ÿͿ� % �γ�Ψ���ü��ͽи�
      }

      if($special_guard_count > 0){ //�ü��ͤγ������
	//��ͤ������Ķ���Ƥ�������������
	if($special_guard_count > $guard_count) $special_guard_count = $guard_count;
	$guard_count -= $special_guard_count; //�ü��ͤο�������ͤ򸺤餹
	
	if($user_count < 20){ //20��̤���ξ��ϥ֥󲰤Τ�
	  $role_list['reporter'] = $special_guard_count;
	}
	else{
	  if(mt_rand(1, 100) <= $user_count){ //���ΤϺ�����
	    $role_list['poison_guard']++;
	    $special_guard_count--;
	  }
	  $role_list['reporter'] = $special_guard_count;
	}
      }
      $role_list['guard'] = $guard_count;
    }

    //��ͭ�ԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 13){ //0:1 = 10:90
      $common_count = ($rand <= 10 ? 0 : 1);
    }
    elseif($user_count < 22){ //1:2:3 = 10:80:10
      $common_count = 1;
      if($rand > 10) $common_count++;
      if($rand > 90) $common_count++;
    }
    else{ //�ʸ塢���ÿͿ���15�������뤴�Ȥ� 1�ͤ�������
      $common_count = floor($user_count / 15);
      if($rand > 10) $common_count++;
      if($rand > 90) $common_count++;
    }

    //��ͭ�Ԥ���������
    if($common_count > 0 && $human_count >= $common_count){
      $role_list['common'] = $common_count;
      $human_count -= $common_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //���ǼԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 95:5
      $poison_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 85:15
      $poison_count = ($rand <= 85 ? 0 : 1);
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $poison_count = floor($user_count / 20) - 1;
      if($rand > 10) $poison_count++;
      if($rand > 90) $poison_count++;
    }
    $poison_count -= $poison_guard_count; //���Το��������餹

    //���ǼԤ���������
    if($poison_count > 0 && $human_count >= $poison_count){
      $role_list['poison'] = $poison_count;
      $human_count -= $poison_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //���դοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 95:5
      $pharmacist_count = ($rand <= 95 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 85:15
      $pharmacist_count = ($rand <= 85 ? 0 : 1);
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $pharmacist_count = floor($user_count / 20) - 1;
      if($rand > 10) $pharmacist_count++;
      if($rand > 90) $pharmacist_count++;
    }
    if($poison_wolf_count > 0 && $pharmacist_count == 0) $pharmacist_count++;

    //���դ���������
    if($pharmacist_count > 0 && $human_count >= $pharmacist_count){
      $role_list['pharmacist'] = $pharmacist_count;
      $human_count -= $pharmacist_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //���åޥ˥��οͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 16){ //16��̤���ǤϽи����ʤ�
      $mania_count = 0;
    }
    elseif($user_count < 23){ //0:1 = 40:60
      $mania_count = ($rand <= 40 ? 0 : 1);
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $mania_count = floor($user_count / 20) - 1;
      if($rand > 10) $mania_count++;
      if($rand > 90) $mania_count++;
    }

    //���åޥ˥�����������
    if($mania_count > 0 && $human_count >= $mania_count){
      $role_list['mania'] = $mania_count;
      $human_count -= $mania_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //�Կ��ԷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 90:10
      $strangers_count = ($rand <= 90 ? 0 : 1);
    }
    elseif($user_count < 19){ //0:1 = 80:20
      $strangers_count = ($rand <= 80 ? 0 : 1);
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $strangers_count = floor($user_count / 20) - 1;
      if($rand > 10) $strangers_count++;
      if($rand > 90) $strangers_count++;
    }

    //�Կ��ԷϤ���������
    if($strangers_count > 0 && $human_count >= $strangers_count){
      //���͸���20��̤���ξ���̵�ռ�������ʾ�ʤ��Կ��Ԥ���Ф䤹������
      $strangers_rate = ($user_count < 20 ? 60 : 40);
      for($i = 0; $i < $strangers_count; $i++){
	$strangers_role = (mt_rand(1, 100) <= $strangers_rate ? 'unconscious' : 'suspect');
	$role_list[$strangers_role]++;
      }
      $human_count -= $strangers_count; //¼�ͿرĤλĤ�Ϳ�
    }

    $role_list['human'] = $human_count; //¼�ͤοͿ�

    //õ�� (��ͭ or ¼�� �� õ��)
    if(strpos($option_role, 'detective') !== false){
      if($role_list['common'] > 0){
	$role_list['common']--;
	$role_list['detective_common']++;
      }
      else{
	$role_list['human']--;
	$role_list['detective_common']++;
      }
    }
  }
  else{ //�̾�¼
    //���Ǽ� (¼��2 �� ���Ǽ�1����ϵ1)
    if(in_array('poison', $option_role_list) && $user_count >= $CAST_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //�Ż��� (¼��2 �� �Ż���1����ϵ1)
    if(in_array('assassin', $option_role_list) && $user_count >= $CAST_CONF->assassin){
      $role_list['human'] -= 2;
      $role_list['assassin']++;
      $role_list['wolf']++;
    }

    //��ϵ (��ϵ �� ��ϵ)
    if(in_array('boss_wolf', $option_role_list) && $user_count >= $CAST_CONF->boss_wolf){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //��ϵ (��ϵ �� ��ϵ��¼�� �� ����)
    if(in_array('poison_wolf', $option_role_list) && $user_count >= $CAST_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //��ϵ (��ϵ �� ��ϵ)
    if(in_array('possessed_wolf', $option_role_list) && $user_count >= $CAST_CONF->possessed_wolf){
      $role_list['wolf']--;
      $role_list['possessed_wolf']++;
    }

    //ŷϵ (��ϵ �� ŷϵ)
    if(in_array('sirius_wolf', $option_role_list) && $user_count >= $CAST_CONF->sirius_wolf){
      $role_list['wolf']--;
      $role_list['sirius_wolf']++;
    }

    //���塼�ԥå� (14�ͤϥϡ��ɥ����� / ¼�� �� ���塼�ԥå�)
    if(in_array('cupid', $option_role_list) && ! in_array('full_cupid', $option_role_list) &&
       ($user_count == 14 || $user_count >= $CAST_CONF->cupid)){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //��� (¼�� �� ���1������1)
    if(in_array('medium', $option_role_list) && $user_count >= $CAST_CONF->medium){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['mind_cupid']++;
    }

    //���åޥ˥� (¼�� �� ���åޥ˥�)
    if(in_array('mania', $option_role_list) && ! in_array('full_mania', $option_role_list) &&
       $user_count >= $CAST_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //õ�� (��ͭ or ¼�� �� õ��)
    if(in_array('detective', $option_role_list)){
      if($role_list['common'] > 0){
	$role_list['common']--;
	$role_list['detective_common']++;
      }
      else{
	$role_list['human']--;
	$role_list['detective_common']++;
      }
    }
  }

  //-- ¼���ִ�¼ --//
  $add_count = $role_list['human'];
  if($gerd && $add_count > 1) $add_count--;
  $CAST_CONF->ReplaceHuman($role_list, $add_count, $option_role_list);

  //$is_single_role = true;
  $is_single_role = false;
  if($is_single_role){ //��Ͱ쿦¼�б�
    $role_list = array(); //�����ꥻ�å�
    $base_role_list = array('wolf', 'mage', 'human', 'jammer_mad', 'necromancer',
			    'common', 'crisis_priest', 'boss_wolf', 'guard', 'dark_fairy',
			    'poison', 'agitate_mad', 'fox', 'cupid', 'soul_mage',
			    'resist_wolf', 'trap_common', 'yama_necromancer', 'child_fox', 'mania',
			    'tongue_wolf', 'assassin', 'fend_guard', 'cute_fox', 'ghost_common',
			    'cute_wolf', 'black_fox', 'light_fairy', 'poison_jealousy', 'self_cupid',
			    'silver_wolf','scarlet_wolf','wise_wolf', 'mind_cupid', 'dummy_chiroptera',);
    for($i = $user_count; $i > 0; $i--) $role_list[array_shift($base_role_list)]++;
  }

  if($ROOM->IsOption('festival') && is_array($CAST_CONF->festival_role_list[$user_count])){
    $role_list = $CAST_CONF->festival_role_list[$user_count]; //���פ�¼��������
  }

  if($role_list['human'] < 0){ //"¼��" �οͿ�������å�
    $sentence = '"¼��" �οͿ����ޥ��ʥ��ˤʤäƤޤ�';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }
  if($role_list['wolf'] < 0){ //"��ϵ" �οͿ�������å�
    $sentence = '"��ϵ" �οͿ����ޥ��ʥ��ˤʤäƤޤ�';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  //��̾���Ǽ�������������
  $now_role_list = array();
  foreach($role_list as $key => $value){
    for($i = 0; $i < $value; $i++) array_push($now_role_list, $key);
  }
  $role_count = count($now_role_list);

  if($role_count != $user_count){ //����Ĺ������å�
    if($ROOM->test_mode){
      PrintData($role_count, '���顼�������');
      return $now_role_list;
    }
    $sentence = '¼�� (' . $user_count . ') ������ο� (' . $role_count . ') �����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  return $now_role_list;
}

//�򿦤οͿ����Υꥹ�Ȥ��������
function GenerateRoleNameList($role_count_list, $chaos = NULL){
  global $ROLE_DATA;

  $main_role_key_list = array_keys($ROLE_DATA->main_role_list);
  switch($chaos){
  case 'camp':
    $header = '�и��رġ�';
    $main_type = '�ر�';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $main_role_key_list)) continue;
      if(strpos($key, 'wolf') !== false || strpos($key, 'mad') !== false)
	$main_role_list['wolf'] += $value;
      elseif(strpos($key, 'fox') !== false)
	$main_role_list['fox'] += $value;
      elseif(strpos($key, 'cupid') !== false || strpos($key, 'angel') !== false)
	$main_role_list['cupid'] += $value;
      elseif(strpos($key, 'mania') !== false)
	$main_role_list['mania'] += $value;
      elseif(strpos($key, 'quiz') !== false)
	$main_role_list['quiz'] += $value;
      elseif(strpos($key, 'vampire') !== false)
	$main_role_list['vampire'] += $value;
      elseif(strpos($key, 'chiroptera') !== false || strpos($key, 'fairy') !== false)
	$main_role_list['chiroptera'] += $value;
      else
	$main_role_list['human'] += $value;
    }
    break;

  case 'role':
    $header = '�и��򿦼';
    $main_type = '��';
    $main_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $main_role_key_list)) continue;
      $main_role_list[DistinguishRoleGroup($key)] += $value;
    }
    break;

  default:
    $header = '�и��򿦡�';
    $main_role_list = $role_count_list;
    break;
  }

  $sub_role_key_list = array_keys($ROLE_DATA->sub_role_list);
  switch($chaos){
  case 'camp':
  case 'role':
    $sub_type = '��';
    $sub_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $sub_role_key_list)) continue;
      foreach($ROLE_DATA->sub_role_group_list as $class => $list){
	if(! in_array($key, $list)) continue;
	$sub_role_list[$list[0]] += $value;
      }
    }
    break;

  default:
    $sub_role_list = $role_count_list;
    break;
  }

  $stack = array();
  foreach($ROLE_DATA->main_role_list as $key => $value){
    $count = (int)$main_role_list[$key];
    if($count > 0) $stack[] = $value . $main_type . $count;
  }

  foreach($ROLE_DATA->sub_role_list as $key => $value){
    $count = (int)$sub_role_list[$key];
    if($count > 0) $stack[] = '(' . $value . $sub_type . $count . ')';
  }
  return $header . implode('��', $stack);
}

//�����೫����ɼ���׽���
function AggregateVoteGameStart($force_start = false){
  global $CAST_CONF, $MESSAGE, $ROLE_DATA, $ROOM, $USERS;

  $user_count = $USERS->GetUserCount(true); //�桼����������
  if($ROOM->test_mode){
    $vote_count = $user_count;
  }
  else{
    CheckSituation('GAMESTART');

    //��ɼ��������
    if($force_start){ //�������ϥ⡼�ɻ��ϥ����å�
      $vote_count = $user_count;
    }
    else{
      $vote_count = $ROOM->LoadVote(); //��ɼ�������� (��å����ξ���ϻȤ�ʤ���)
      //������¼�ʳ��ο����귯��ʬ��û�
      if($ROOM->IsDummyBoy() && ! $ROOM->IsQuiz()) $vote_count++;
    }
  }

  //����Ϳ���­��ʤ�����������ɼ���Ƥ��ʤ���н�����λ
  if($vote_count != $user_count || $vote_count < min(array_keys($CAST_CONF->role_list))){
    return false;
  }

  //-- �������롼���� --//
  $ROOM->LoadOption(); //�������ꥪ�ץ����ξ�������
  //PrintData($ROOM->option_role);
  //PrintData($ROOM->option_list);

  //����������ѿ��򥻥å�
  $uname_list        = $USERS->GetLivingUsers(); //�桼��̾������
  $role_list         = GetRoleList($user_count, $ROOM->option_role->row); //�򿦥ꥹ�Ȥ����
  $fix_uname_list    = array(); //���η��ꤷ���桼��̾���Ǽ����
  $fix_role_list     = array(); //�桼��̾���б��������
  $remain_uname_list = array(); //��˾�����ˤʤ�ʤ��ä��桼��̾����Ū�˳�Ǽ
  //PrintData($uname_list, 'Uname');
  //PrintData($role_list, 'Role');

  //�ե饰���å�
  $gerd      = $ROOM->IsOption('gerd');
  $chaos     = $ROOM->IsOptionGroup('chaos'); //chaosfull ��ޤ�
  $quiz      = $ROOM->IsQuiz();
  $detective = $ROOM->IsOption('detective');
  //���顼��å�����
  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';
  $reset_flag   = ! $ROOM->test_mode;

  if($ROOM->IsDummyBoy()){ //�����귯���򿦤����
    if(($gerd && in_array('human', $role_list)) || $quiz){ //�����귯���򿦸��ꥪ�ץ����Ƚ��
      if($gerd) $fit_role = 'human'; //����ȷ�
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
	$role = array_shift($role_list); //����ꥹ�Ȥ�����Ƭ��ȴ���Ф�
	foreach($CAST_CONF->disable_dummy_boy_role_list as $disable_role){
	  if(strpos($role, $disable_role) !== false){
	    array_push($role_list, $role); //����ꥹ�Ȥ��������᤹
	    continue 2;
	  }
	}
	array_push($fix_role_list, $role);
	break;
      }
    }

    if(count($fix_role_list) < 1){ //�����귯����Ϳ�����Ƥ��뤫�����å�
      $sentence = '�����귯����Ϳ�����Ƥ��ޤ���';
      OutputVoteResult($error_header . $sentence . $error_footer, $reset_flag, $reset_flag);
    }
    array_push($fix_uname_list, 'dummy_boy'); //����Ѥߥꥹ�Ȥ˿����귯���ɲ�
    unset($uname_list[array_search('dummy_boy', $uname_list)]); //�����귯����
    //PrintData($fix_role_list, 'dummy_boy');
  }

  shuffle($uname_list); //�桼���ꥹ�Ȥ������˼���
  //PrintData($uname_list, 'ShuffleUname');

  //��˾�򿦤򻲾Ȥ��ư켡�����Ԥ�
  if($ROOM->IsOption('wish_role')){ //����˾���ξ��
    $wish_group = $ROOM->IsChaosWish(); //�ü�¼��
    foreach($uname_list as $uname){
      do{
	$role = $USERS->GetRole($uname); //��˾�򿦤����
	if($role == '' || mt_rand(1, 100) > $CAST_CONF->wish_role_rate) break;
	$fit_role = $role;

	if($wish_group){ //�ü�¼�ϥ��롼��ñ�̤Ǵ�˾������Ԥʤ�
	  $stack = array();
	  foreach($role_list as $stack_role){
	    if($role == DistinguishRoleGroup($stack_role)) $stack[] = $stack_role;
	  }
	  $fit_role = GetRandom($stack);
	}
	$role_key = array_search($fit_role, $role_list); //��˾�򿦤�¸�ߥ����å�
	if($role_key === false) break;

	//��˾�򿦤�����з���
	array_push($fix_uname_list, $uname);
	array_push($fix_role_list, $fit_role);
	unset($role_list[$role_key]);
	continue 2;
      }while(false);

      //��ޤ�ʤ��ä�����̤����ꥹ�ȹԤ�
      array_push($remain_uname_list, $uname);
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
  $roled_list = array(); //����Ѥ��ֹ�
  //��꿶���оݳ��򿦤Υꥹ��
  $delete_role_list = array('lovers', 'challenge_lovers', 'infected', 'copied', 'copied_trick',
			    'copied_soul', 'copied_teller', 'lost_ability', 'febris',
			    'death_warrant', 'panelist', 'mind_read', 'mind_evoke', 'mind_receiver',
			    'mind_friend', 'mind_lonely', 'mind_sympathy');

  //�����򿦥ƥ�����
  /*
  $stack = array('whisper_ringing', 'howl_ringing', 'critical_luck');
  $delete_role_list = array_merge($delete_role_list, $stack);
  for($i = 0; $i < $user_count; $i++){
    if(($role = array_shift($stack)) == '') break;
    if($fix_uname_list[$i] == 'dummy_boy'){
      $stack[] = $role;
      continue;
    }
    $fix_role_list[$i] .= ' ' . $role;
    $roled_list[] = $i;
  }
  */

  foreach(array('decide', 'authority') as $role){ //���ץ����ǤĤ��륵����
    if(! $ROOM->IsOption($role)) continue;
    $delete_role_list[] = $role;
    if($user_count >= $CAST_CONF->$role){
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
    $stack = array('male' => 'gentleman', 'female' => 'lady');
    $delete_role_list = array_merge($delete_role_list, $stack);
    for($i = 0; $i < $user_count; $i++){ //���������̤˱������»Τ��ʽ���Ĥ���
      $fix_role_list[$i] .= ' ' . $stack[$USERS->ByUname($fix_uname_list[$i])->sex];
    }
  }

  if($ROOM->IsOption('sudden_death')){ //�����μ�¼
    $stack = array_diff($ROLE_DATA->sub_role_group_list['sudden-death'],
			array('febris', 'death_warrant', 'panelist'));
    //PrintData($stack, 'SuddenDeath');
    $delete_role_list = array_merge($delete_role_list, $stack);
    for($i = 0; $i < $user_count; $i++){ //�����˾����ԷϤ򲿤��Ĥ���
      $role = GetRandom($stack);
      $fix_role_list[$i] .= ' ' . $role;
      if($role == 'impatience') $stack = array_diff($stack, array('impatience')); //û���ϰ�ͤ���
    }
  }
  elseif($ROOM->IsOption('perverseness')){ //ŷ�ٵ�¼
    $role = 'perverseness';
    $delete_role_list[] = $role;
    for($i = 0; $i < $user_count; $i++) $fix_role_list[$i] .= ' ' . $role;
  }

  if($chaos && ! $ROOM->IsOption('no_sub_role')){
    //������ʥ����򿦤Υ����ɥꥹ�Ȥ����
    if($ROOM->IsOption('sub_role_limit_easy'))
       $sub_role_keys = $CAST_CONF->chaos_sub_role_limit_easy_list;
    elseif($ROOM->IsOption('sub_role_limit_normal'))
       $sub_role_keys = $CAST_CONF->chaos_sub_role_limit_normal_list;
    else
      $sub_role_keys = array_keys($ROLE_DATA->sub_role_list);
    //$sub_role_keys = array('authority', 'rebel', 'upper_luck', 'random_voter'); //�ƥ�����
    //array_push($delete_role_list, 'earplug', 'speaker'); //�ƥ�����
    //PrintData($delete_role_list, 'DeleteRoleList');

    $sub_role_keys = array_diff($sub_role_keys, $delete_role_list);
    //PrintData($sub_role_keys, 'SubRoleList');
    shuffle($sub_role_keys);
    foreach($sub_role_keys as $key){
      if($rand_keys_index > $user_count - 1) break; //$rand_keys_index �� 0 ����
      $i = $rand_keys[$rand_keys_index++];
      if(! in_array($i, $roled_list)) $fix_role_list[$i] .= ' ' . $key;
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
  //�ƥ�����
  //PrintData($fix_uname_list); PrintData($fix_role_list); DeleteVote(); return false;

  //����DB�˹���
  $role_count_list = array();
  $detective_list = array();
  for($i = 0; $i < $user_count; $i++){
    $role = $fix_role_list[$i];
    $user = $USERS->ByUname($fix_uname_list[$i]);
    $user->ChangeRole($role);
    $stack = explode(' ', $role);
    foreach($stack as $role) $role_count_list[$role]++;
    if($detective && in_array('detective_common', $stack)) $detective_list[] = $user;
  }

  //KICK �θ����
  $user_no = 1;
  foreach($USERS->rows as $user){
    if($user->user_no != $user_no) $user->Update('user_no', $user_no);
    $user_no++;
  }
  foreach($USERS->kicked as $user) $user->Update('user_no', '-1');

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
  $ROOM->day_night = $ROOM->IsOption('open_day') ? 'day' : 'night';
  if(! $ROOM->test_mode){
    $query = "UPDATE room SET date = {$ROOM->date}, day_night = '{$ROOM->day_night}', " .
      "status = 'playing', start_time = NOW() WHERE room_no = {$ROOM->id}";
    SendQuery($query);
    //OutputSiteSummary(); //RSS��ǽ�ϥƥ�����
  }
  $ROOM->Talk($sentence);
  if($detective && count($detective_list) > 0){ //õ��¼�λ�̾
    $detective_user = GetRandom($detective_list);
    $ROOM->Talk('õ��� ' . $detective_user->handle_name . ' ����Ǥ�');
    if($ROOM->IsOption('gm_login') && $ROOM->IsOption('not_open_cast') && $user_count > 7){
      $detective_user->ToDead(); //�õ��⡼�ɤʤ�õ����������
    }
  }
  if($ROOM->test_mode) return true;

  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //�����ν跺��ɼ�Υ�����Ȥ�1�˽����(����ɼ��������)
  $ROOM->UpdateTime(); //�ǽ��񤭹��߻���򹹿�
  CheckVictory(); //������˾��Ԥ����ꤷ�Ƥ����ǽ��������ΤǾ���Ƚ���Ԥ�
  DeleteVote(); //���ޤǤ���ɼ���������
  return true;
}

//�����ɼ���׽���
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS;

  //-- ��ɼ�����¹�Ƚ�� --//
  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  $user_list = $USERS->GetLivingUsers(); //�����Ƥ���桼�������
  if($ROOM->LoadVote() != count($user_list)) return false; //��ɼ���Ⱦȹ�
  //PrintData($ROOM->vote, 'Vote');

  //-- ��������� --//
  $max_voted_number = 0; //��¿��ɼ��
  $vote_kill_uname = ''; //�跺�����ͤΥ桼��̾
  $live_uname_list             = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list           = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list            = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��桼��̾)
  $vote_count_list             = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list                = array(); //ǽ�ϼԤ�������ɼ���
  $pharmacist_target_list      = array(); //���դ���ɼ��
  $cure_pharmacist_target_list = array(); //��Ƹ����ɼ��
  $pharmacist_result_list      = array(); //���շϤδ�����

  //-- ��ɼ�ǡ������� --//
  foreach($ROOM->vote as $uname => $list){ //�����ɼ�ǡ��������
    $target_uname = $USERS->ByVirtualUname($list['target_uname'])->uname;
    $vote_count_list[$target_uname] += $list['vote_number'];
  }
  //PrintData($vote_count_list, 'VoteCountBase');

  foreach($user_list as $uname){ //���̤���ɼ�ǡ��������
    $user = $USERS->ByVirtualUname($uname); //���ۥ桼�������
    $list = $ROOM->vote[$uname]; //��ɼ�ǡ���
    $target = $USERS->ByVirtualUname($list['target_uname']); //��ɼ��β��ۥ桼��
    $vote_number  = (int)$list['vote_number']; //��ɼ��
    $voted_number = (int)$vote_count_list[$user->uname]; //��ɼ��

    //�����򿦤���ɼ����
    $ROLES->actor = $user;
    foreach($ROLES->Load('voted') as $filter) $filter->FilterVoted($voted_number);
    if($voted_number < 0) $voted_number = 0; //�ޥ��ʥ��ˤʤäƤ����� 0 �ˤ���

    //�����ƥ��å������Ѥ����������
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);
    //PrintData($message_list, $uname);

    //�ꥹ�Ȥ˥ǡ������ɲ�
    $live_uname_list[$user->user_no] = $user->uname;
    $vote_message_list[$user->uname] = $message_list;
    $vote_target_list[$user->uname]  = $target->uname;
    $vote_count_list[$user->uname]   = $voted_number;
    if($user->IsRole('authority')){ //���ϼԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['authority'] = $target->uname;
      $ability_list['authority_uname'] = $user->uname;
    }
    elseif($user->IsRole('rebel')){ //ȿ�ռԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['rebel'] = $target->uname;
      $ability_list['rebel_uname'] = $user->uname;
    }
    elseif($user->IsRole('decide')) //����Ԥʤ���ɼ���Ͽ
      $ability_list['decide'] = $target->uname;
    elseif($user->IsRole('plague')) //���¿��ʤ���ɼ���Ͽ
      $ability_list['plague'] = $target->uname;
    elseif($user->IsRole('impatience')) //û���ʤ���ɼ���Ͽ
      $ability_list['impatience'] = $target->uname;
    elseif($user->IsRole('good_luck')) //�����ʤ�桼��̾��Ͽ
      $ability_list['good_luck'] = $user->uname;
    elseif($user->IsRole('bad_luck')) //�Ա��ʤ�桼��̾��Ͽ
      $ability_list['bad_luck'] = $user->uname;
  }
  //PrintData($vote_count_list, 'VoteCount');

  //-- ȿ�ռԤ�Ƚ�� --//
  if(isset($ability_list['rebel']) && $ability_list['rebel'] == $ability_list['authority']){
    //���ϼԤ�ȿ�ռԤ���ɼ���� 0 �ˤ���
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //��ɼ�����ɼ������������
    $uname = $ability_list['rebel'];
    if($vote_message_list[$uname]['voted_number'] > 3)
      $vote_message_list[$uname]['voted_number'] -= 3;
    else
      $vote_message_list[$uname]['voted_number'] = 0;
    $vote_count_list[$uname] = $vote_message_list[$uname]['voted_number'];
  }
  //PrintData($vote_message_list, 'VoteMessage');

  //-- ��ɼ�����Ͽ --//
  foreach($live_uname_list as $uname){ //���ֶ��ڤ�Υǡ����򥷥��ƥ��å���������Ͽ
    extract($vote_message_list[$uname]); //�����Ÿ��
    if($voted_number > $max_voted_number) $max_voted_number = $voted_number; //������ɼ���򹹿�

    //(ï�� [TAB] ï�� [TAB] ��ʬ����ɼ�� [TAB] ��ʬ����ɼ�� [TAB] ��ɼ���)
    $sentence = $USERS->GetHandleName($uname) . "\t" . $target . "\t" .
      $voted_number ."\t" . $vote_number . "\t" . $RQ_ARGS->vote_times;
    $ROOM->SystemMessage($sentence, 'VOTE_KILL');
  }

  //-- �跺�Է������ --//
  //������ɼ���Υ桼��̾ (�跺�����) �Υꥹ�Ȥ����
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  //PrintData($max_voted_uname_list, 'MaxVoted');
  do{ //�跺�Է���롼����
    if(count($max_voted_uname_list) == 1){ //��ͤ����ʤ����
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    //-- ����ǽ�ϼ�Ƚ�� --//
    foreach(array('decide', 'bad_luck', 'impatience') as $role){
      $ability = $ability_list[$role];
      if(isset($ability) && in_array($ability, $max_voted_uname_list)){
	$vote_kill_uname = $ability;
	break 2;
      }
    }

    //-- ����ǽ�ϼ�Ƚ�� --//
    foreach(array('good_luck', 'plague') as $role){
      if(is_null($ability = $ability_list[$role])) continue;
      if(($key = array_search($ability, $max_voted_uname_list)) === false) continue;
      unset($max_voted_uname_list[$key]);
      if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
	$vote_kill_uname = array_shift($max_voted_uname_list);
	break 2;
      }
    }

    //-- ���Լ�Ƚ�� --//
    $stack = array();
    foreach($user_list as $uname){ //��¿��ɼ�Ԥ���ɼ�������ԼԤ���ɼ������
      $user = $USERS->ByUname($uname); //$uname �ϼ¥桼��
      if(! $user->IsRole('executor')) continue;
      $target = $USERS->ByVirtualUname($ROOM->vote[$uname]['target_uname']);
      if(in_array($target->uname, $max_voted_uname_list) &&
	 $target->GetCamp(true) != 'human'){ //��¿��ɼ�ԥꥹ�Ȥϲ��ۥ桼��
	$stack[$target->uname] = true;
      }
    }
    if(count($stack) == 1){ //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
      $vote_kill_uname = array_shift(array_keys($stack));
      break;
    }

    //-- ����Ƚ�� --//
    $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number); //��¿��ɼ�Ԥ�Ƽ���
    $stack = array();
    $target_stack = array();
    foreach($max_voted_uname_list as $uname){//��¿��ɼ�Ԥξ�������
      $user = $USERS->ByRealUname($uname); //$uname �ϲ��ۥ桼��
      if($user->IsRole('saint')) $stack[] = $uname;
      if($user->GetCamp(true) != 'human') $target_stack[] = $uname;
    }
    if(count($stack) > 0 && count($target_stack) < 2){ //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
      if(isset($target_stack[0])){
	$vote_kill_uname = $target_stack[0];
	break;
      }
      elseif(count($stack) == 1){
	$vote_kill_uname = $stack[0];
	break;
      }
    }

    //-- ��ư��Ƚ�� --//
    $stack = array();
    foreach($user_list as $uname){ //��¿��ɼ�Ԥ���ɼ������ư�Ԥ���ɼ������
      $user = $USERS->ByUname($uname); //$uname �ϼ¥桼��
      if(! $user->IsRole('agitate_mad')) continue;
      $target = $USERS->ByVirtualUname($ROOM->vote[$uname]['target_uname']);
      if(in_array($target->uname, $max_voted_uname_list)){ //��¿��ɼ�ԥꥹ�Ȥϲ��ۥ桼��
	$stack[$target->uname] = true;
      }
    }
    if(count($stack) == 1){ //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
      $vote_kill_uname = array_shift(array_keys($stack));
      foreach($max_voted_uname_list as $uname){
	if($uname == $vote_kill_uname) continue;
	$user = $USERS->ByRealUname($uname); //$uname �ϲ��ۥ桼��
	$USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_AGITATED');
      }
    }
  }while(false);
  //PrintData($vote_kill_uname, 'VoteTarget');

  if($vote_kill_uname != ''){ //-- �跺�¹Խ��� --//
    //-- �跺�Ծ������ --//
    $vote_target = $USERS->ByRealUname($vote_kill_uname); //�桼����������
    $USERS->Kill($vote_target->user_no, 'VOTE_KILLED'); //�跺����
    unset($live_uname_list[$vote_target->user_no]); //�跺�Ԥ���¸�ԥꥹ�Ȥ������
    $voter_list = array_keys($vote_target_list, $vote_target->uname); //��ɼ�����ͤ����

    foreach($user_list as $uname){ //-- ���դξ������ --//
      $user = $USERS->ByUname($uname);
      if(! $user->IsRoleGroup('pharmacist')) continue;

      $target = $USERS->ByUname($vote_target_list[$user->uname]); //��ɼ��ξ�������
      $pharmacist_target_list[$user->uname] = $target->uname;
      if($user->IsRole('cure_pharmacist')){ //��Ƹ�ˤ��Ǥμ��������
	$cure_pharmacist_target_list[$user->uname] = $target->uname;
	continue;
      }

      if(! $target->IsRoleGroup('poison') || $target->IsRole('dummy_poison')){
	$pharmacist_result_list[$user->uname] = 'nothing'; //����ǽ�ϼԡ�̴�Ǽ�
      }
      elseif($target->IsRole('strong_poison')){
	$pharmacist_result_list[$user->uname] = 'strong'; //���Ǽ�
      }
      elseif($target->IsRole('incubate_poison')){ //���ǼԤ� 5 ���ܰʹߤ˶��Ǥ����
	$pharmacist_result_list[$user->uname] = $ROOM->date >= 5 ? 'strong' : 'nothing';
      }
      elseif($target->IsRole('poison_guard', 'guide_poison', 'chain_poison', 'poison_jealousy')){
	$pharmacist_result_list[$user->uname] = 'limited'; //���Ρ�Ͷ�Ǽԡ�Ϣ�Ǽԡ��Ƕ�ɱ
      }
      else{
	$pharmacist_result_list[$user->uname] = 'poison';
      }
    }

    do{ //-- �跺�Ԥ��ǽ��� --//
      if(! $vote_target->IsPoison()) break; //��ǽ�Ϥ�ȯưȽ��

      //���շϤβ���Ƚ�� (̴�ǼԤ��оݳ�)
      if(in_array($vote_target->uname, $pharmacist_target_list) &&
	 ! $vote_target->IsRole('dummy_poison')){
	$stack = array_keys($pharmacist_target_list, $vote_target->uname); //��ɼ�Ԥ򸡽�
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	break;
      }

      //�Ǥ��оݥ��ץ���������å����Ƹ���ԥꥹ�Ȥ����
      $poison_target_list = array(); //�Ǥ��оݥꥹ��
      $target_list = $GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list;
      //PrintData($target_list);

      foreach($target_list as $uname){ //����оݳ����򿦤����
	$user = $USERS->ByRealUname($uname);
	if($user->IsLive(true) && ! $user->IsAvoid(true)) $poison_target_list[] = $uname;
      }
      //PrintData($poison_target_list, 'BasePoisonTarget');

      $limited_list = array(); //�ü��Ǥξ��ϥ������åȤ����ꤵ���
      if($vote_target->IsRole('strong_poison', 'incubate_poison')){ //���Ǽԡ����Ǽ�
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox')) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('guide_poison')){ //Ͷ�Ǽ�
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('poison')) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('dummy_poison')){ //̴�Ǽ�
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('dream_eater_mad', 'fairy')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_jealousy')){ //�Ƕ�ɱ
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsLovers()) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_doll')){ //�����ͷ�
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsDoll()) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_wolf')){ //��ϵ
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsWolf()) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_fox')){ //�ɸ�
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsFox()) $limited_list[] = $uname;
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_chiroptera')){ //������
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox', 'chiroptera', 'fairy')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      if(count($poison_target_list) < 1) break;

      //PrintData($poison_target_list, 'PoisonTarget');
      $poison_target = $USERS->ByRealUname(GetRandom($poison_target_list)); //�оݼԤ����

      if($poison_target->IsActive('resist_wolf')){ //����Ƚ��
	$poison_target->LostAbility();
	break;
      }
      $USERS->Kill($poison_target->user_no, 'POISON_DEAD_day'); //��˴����

      //-- Ϣ�ǼԤν��� --//
      if(! $poison_target->IsRole('chain_poison')) break; //Ϣ�Ǽ�Ƚ��
      if(in_array($poison_target->uname, $pharmacist_target_list)){ //���շϤβ���Ƚ��
	$stack = array_keys($pharmacist_target_list, $poison_target->uname); //��ɼ�Ԥ򸡽�
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	break;
      }

      $live_stack   = $USERS->GetLivingUsers(true); //��¸�Ԥ����
      $target_stack = array();
      foreach($live_stack as $uname){ //����оݳ����򿦤����
	$user = $USERS->ByRealUname($uname);
	if(! $user->IsAvoid(true)) $target_stack[] = $user->user_no;
      }
      //PrintData($target_stack, 'BaseChainPoisonTarget');

      $chain_count = 1; //Ϣ��������Ȥ�����
      while($chain_count > 0){
	$chain_count--;
	shuffle($target_stack); //����򥷥�åե�
	for($i = 0; $i < 2; $i++){
	  if(count($target_stack) < 1) break 2;
	  $id = array_shift($target_stack);
	  $target = $USERS->ByReal($id);

	  if($target->IsActive('resist_wolf')){ //����Ƚ��
	    $target->LostAbility();
	    $target_stack[] = $id;
	    continue;
	  }
	  $USERS->Kill($id, 'POISON_DEAD_day'); //��˴����

	  if(! $target->IsRole('chain_poison')) continue; //Ϣ��Ƚ��
	  if(in_array($target->uname, $pharmacist_target_list)){ //���շϤβ���Ƚ��
	    $stack = array_keys($pharmacist_target_list, $target->uname); //��ɼ�Ԥ򸡽�
	    foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
	  }
	  else $chain_count++;
	}
      }
    }while(false);

    //-- �ü���Ƚ�� --//
    if($vote_target->IsRole('doom_doll')){ //˩��ͷ�
      $stack = array(); //�оݼԤ�����
      foreach($voter_list as $uname){
	$user = $USERS->ByRealUname($uname);
	if(! $user->IsAvoid() && ! $user->IsDoll()) $stack[] = $user->user_no;
      }
      //PrintData($stack, 'Target [doom_doll]');

      if(count($stack) > 0){
	$add_role = 'death_warrant[' . ($ROOM->date + 2) . ']';
	$USERS->ByID(GetRandom($stack))->AddRole($add_role);
      }
    }
    elseif($vote_target->IsRole('miasma_fox')){ //굸�
      $stack = array(); //�оݼԤ�����
      foreach($voter_list as $uname){
	$user = $USERS->ByRealUname($uname);
	if(! $user->IsAvoid()) $stack[] = $user->user_no;
      }
      //PrintData($stack, 'Target [miasma_fox]');

      if(count($stack) > 0){
	$add_role = 'febris[' . ($ROOM->date + 1) . ']';
	$USERS->ByID(GetRandom($stack))->AddRole($add_role);
      }
    }

    //-- �����դν��� --//
    $stack = array('phantom_wolf', 'resist_wolf', 'tongue_wolf', 'trap_mad', 'possessed_mad',
		   'phantom_fox', 'emerald_fox', 'revive_fox', 'possessed_fox');
    foreach($user_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if($user->IsSame($vote_kill_uname) || ! $user->IsRole('seal_medium')) continue;

      $target = $USERS->ByRealUname($vote_target_list[$user->uname]); //��ɼ������
      if($target->IsSame($vote_kill_uname)) continue;
      if($target->IsActive($stack)) $target->LostAbility();
      elseif($target->IsRole('lost_ability')){
	$USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_SEALED');
      }
    }

    //-- ��ǽ�ԷϤν��� --//
    $sentence_header = $USERS->GetHandleName($vote_target->uname, true) . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽȽ��
    if($vote_target->IsRole('boss_wolf', 'phantom_wolf', 'cursed_wolf', 'possessed_wolf')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('white_fox', 'black_fox', 'phantom_fox', 'possessed_fox',
				'cursed_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsChildFox()){
      $necromancer_result = 'child_fox';
    }
    elseif($vote_target->IsWolf()){
      $necromancer_result = 'wolf';
    }
    elseif($vote_target->IsRole('vampire')){
      $necromancer_result = 'chiroptera';
    }
    else{
      $necromancer_result = 'human';
    }

    //�м֤�˸��Ƚ��
    $flag_stolen = false;
    foreach($voter_list as $this_uname){
      $flag_stolen |= $USERS->ByRealUname($this_uname)->IsRole('corpse_courier_mad');
    }

    //��ǽ�ԷϤνи�Ƚ��
    $necromacer_flag       = false;
    $soul_necromacer_flag  = false;
    $dummy_necromacer_flag = false;
    foreach($USERS->rows as $user){
      $necromacer_flag       |= $user->IsRole('necromancer');
      $soul_necromacer_flag  |= $user->IsRole('soul_necromancer');
      $dummy_necromacer_flag |= $user->IsRole('dummy_necromancer');
    }

    if($necromacer_flag){ //��ǽ�Ԥν���
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      $ROOM->SystemMessage($sentence, $action);
    }

    if($soul_necromacer_flag){ //�������ν���
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      $ROOM->SystemMessage($sentence, 'SOUL_' . $action);
    }

    if($dummy_necromacer_flag){ //̴��ͤϡ�¼�͡עΡֿ�ϵ��ȿž
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      $ROOM->SystemMessage($sentence_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //-- ���Τν��� --//
  $target_stack = array();
  foreach($user_list as $uname){ //��¼�ͿرĤ� ID �Ȳ�����ɼ��̾�����
    $user = $USERS->ByRealUname($uname);
    if($user->GetCamp(true) != 'human'){
      $target_stack[$user->user_no] = $USERS->ByVirtual($user->user_no)->uname;
    }
  }
  //PrintData($target_stack, '! HUMAN');
  //PrintData(array_values($target_stack), 'target');

  foreach($user_list as $uname){ //���Τ���ɼ�ԥꥹ�ȤȾȹ�
    $user = $USERS->ByRealUname($uname);
    if(! $user->IsRole('trap_common')) continue;
    $voted_uname_stack = array_keys($vote_target_list, $user->uname); //���Τؤ���ɼ�ԥꥹ��
    //PrintData($voted_uname_stack, 'voted');
    if($voted_uname_stack != array_values($target_stack)) continue;
    foreach($target_stack as $id => $uname) $USERS->Kill($id, 'TRAPPED');
  }

  foreach($user_list as $uname){ //-- ��ɱ�ν��� --//
    $user = $USERS->ByRealUname($uname);
    if($vote_kill_uname == $user->uname || ! $user->IsRole('jealousy')) continue;

    $cupid_list = array(); //���塼�ԥåɤ�ID => ���ͤ�ID
    $jealousy_voted_list = array_keys($vote_target_list, $user->uname); //��ɱ�ؤ���ɼ�ԥꥹ��
    foreach($jealousy_voted_list as $voted_uname){
      $voted_user = $USERS->ByRealUname($voted_uname);
      if($voted_user->dead_flag || ! $voted_user->IsLovers()) continue;
      foreach($voted_user->partner_list['lovers'] as $id){
	$cupid_list[$id][] = $voted_user->user_no;
      }
    }

    //Ʊ�쥭�塼�ԥåɤ����ͤ�ʣ�������饷��å���
    foreach($cupid_list as $cupid_id => $lovers_list){
      if(count($lovers_list) < 2) continue;
      foreach($lovers_list as $id) $USERS->SuddenDeath($id, 'SUDDEN_DEATH_JEALOUSY');
    }
  }

  if($vote_kill_uname != ''){ //-- ������ν��� --//
    foreach($user_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsRole('miasma_mad')) continue;

      $target = $USERS->ByUname($vote_target_list[$user->uname]); //���Τ��դ���
      if($target->IsLive(true) && ! $target->IsAvoid()){ //�ü�����Ƚ��
	$target->AddRole('febris[' . ($ROOM->date + 1) . ']');
      }
    }
  }

  //-- �����򿦤Υ���å������ --//
  //Ƚ���ѥǡ�������Ͽ
  $ROLES->stack->revote = $vote_kill_uname == '';
  $ROLES->stack->target = $vote_target_list;

  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  $ROLES->stack->count  = array_count_values($vote_target_list);

  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  $voted_target_member_list = array_count_values($vote_target_list);
  //PrintData($vote_target_list);

  $cupid_list = array(); //����Ƚ����
  foreach($user_list as $uname){
    $user = $USERS->ByRealUname($uname);
    if($user->IsLovers()){
      foreach($user->GetPartner('lovers') as $id){
	$cupid_list[$id][] = $user->user_no;
      }
    }
  }
  //PrintData($cupid_list, 'QP');

  foreach($live_uname_list as $this_uname){
    $user = $USERS->ByUname($this_uname);
    $reason = '';

    if($ROOM->date > 4 && $user->IsRole('challenge_lovers')){ //����
      $stack = array_keys($vote_target_list, $vote_target_list[$user->uname]);
      //PrintData($stack, $user->uname);
      $count = 0;
      foreach($user->GetPartner('lovers') as $cupid_id){
	foreach($cupid_list[$cupid_id] as $lovers_id){
	  if($lovers_id == $user->user_no) continue;
	  if(in_array($USERS->ByID($lovers_id)->uname, $stack)) $count++;
	}
      }
      if($count < 1) $reason = 'CHALLENGE';
    }

    $ROLES->actor = $user;
    foreach($ROLES->Load('sudden_death') as $filter) $filter->FilterSuddenDeath($reason);

    if($reason != ''){
      if(in_array($user->uname, $cure_pharmacist_target_list)){ //��Ƹ�μ���Ƚ��
	//��ɼ�Ԥ򸡽�
	$stack = array_keys($cure_pharmacist_target_list, $user->uname);
	foreach($stack as $uname) $pharmacist_result_list[$uname] = 'success';
      }
      else{
	$USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_' . $reason);
      }
    }
  }

  foreach($pharmacist_result_list as $uname => $result){ //���շϤδ����̤���Ͽ
    $user = $USERS->ByUname($uname);
    $target = $USERS->ByUname($pharmacist_target_list[$user->uname]); //��ɼ��ξ�������

    //�����̤���Ͽ
    $handle_name = $USERS->GetHandleName($target->uname, true);
    if($user->IsRole('cure_pharmacist')) $result = 'cured';
    $sentence = $user->handle_name . "\t" . $handle_name . "\t" . $result;
    $ROOM->SystemMessage($sentence, 'PHARMACIST_RESULT');
  }

  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����

  if($ROOM->test_mode) return $vote_message_list;

  if($vote_kill_uname != ''){ //����ڤ��ؤ�
    $ROOM->day_night = 'night';
    SendQuery("UPDATE room SET day_night = '{$ROOM->day_night}' WHERE room_no = {$ROOM->id}"); //��ˤ���
    $ROOM->Talk('NIGHT'); //�뤬��������
    if(! CheckVictory()) InsertRandomMessage(); //�������å�����
  }
  else{ //����ɼ����
    $next_vote_times = $RQ_ARGS->vote_times + 1; //��ɼ��������䤹
    $query = 'UPDATE system_message SET message = ' . $next_vote_times . $ROOM->GetQuery() .
      " AND type = 'VOTE_TIMES'";
    SendQuery($query);

    //�����ƥ��å�����
    $ROOM->SystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    $ROOM->Talk("����ɼ�ˤʤ�ޤ���( {$RQ_ARGS->vote_times} ����)");
    CheckVictory(true); //����Ƚ��
  }
  $ROOM->UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  SendCommit(); //������ߥå�
}

//��ν��׽���
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $ROOM->LoadVote(); //��ɼ��������
  //PrintData($ROOM->vote, 'Vote Row');

  $vote_data = $ROOM->ParseVote(); //���ޥ�����ʬ��
  //PrintData($vote_data, 'Vote Data');

  foreach($USERS->rows as $user){ //̤��ɼ�����å�
    if($user->CheckVote($vote_data) === false){
      //PrintData($user->uname, $user->main_role); //�ƥ�����
      return false;
    }
  }

  //�����оݥ��ޥ�ɥ����å�
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  if($ROOM->date == 1){
    array_push($action_list , 'MIND_SCANNER_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list , 'ESCAPE_DO', 'DREAM_EAT', 'TRAP_MAD_DO', 'POSSESSED_DO',
	       'GUARD_DO', 'ANTI_VOODOO_DO', 'REPORTER_DO', 'POISON_CAT_DO', 'ASSASSIN_DO',
	       'VAMPIRE_DO');
  }
  foreach($action_list as $action){
    if(is_null($vote_data[$action])) $vote_data[$action] = array();
  }
  //PrintData($vote_data);

  //-- �ѿ��ν���� --//
  $guarded_uname = ''; //������������ͤΥ桼��̾ //ʣ�����ߤ��б�����ʤ餳����������Ѥ���
  $trap_target_list         = array(); //櫤�������ꥹ��
  $trapped_list             = array(); //櫤ˤ����ä��ͥꥹ��
  $guard_target_list        = array(); //��ͷϤθ���оݥꥹ��
  $dummy_guard_target_list  = array(); //̴��ͤθ���оݥꥹ��
  $escaper_target_list      = array(); //ƨ˴�Ԥ�ƨ˴��ꥹ��
  $sacrifice_list           = array(); //������ष���ͥꥹ��

  $anti_voodoo_target_list  = array(); //����θ���оݥꥹ��
  $anti_voodoo_success_list = array(); //��ʧ�������ԥꥹ��

  $reverse_assassin_target_list = array(); //ȿ���դ��оݥꥹ��

  $possessed_target_list    = array(); //���ͽ��ԥꥹ�� => �����Ω�ե饰
  $possessed_target_id_list = array(); //����оݼԥꥹ��

  //-- �ܿ��ϥ쥤�䡼 --//
  foreach($vote_data['WOLF_EAT'] as $uname => $target_uname){ //��ϵ�ξ������
    $voted_wolf  = $USERS->ByUname($uname);
    $wolf_target = $USERS->ByUname($target_uname);
  }

  if($ROOM->date > 1){
    foreach($vote_data['TRAP_MAD_DO'] as $uname => $target_uname){ //櫻դξ������
      $user   = $USERS->ByUname($uname);
      $target = $USERS->ByUname($target_uname);

      $user->LostAbility(); //�������֤�����ǽ�ϼ���

      //��ϵ�������Ƥ����鼫ʬ���Ȥؤ����ְʳ���̵��
      if($user != $wolf_target || $user == $target){
	$trap_target_list[$user->uname] = $target->uname; //櫤򥻥å�
      }
    }

    //櫻դ���ʬ���Ȱʳ���櫤�ųݤ�����硢�������櫤����ä����ϻ�˴
    $stack = array_count_values($trap_target_list);
    foreach($trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $stack[$target_uname] > 1) $trapped_list[] = $uname;
    }

    foreach($vote_data['GUARD_DO'] as $uname => $target_uname){ //��ͷϤθ����򥻥å�
      $user = $USERS->ByUname($uname);
      if($user->IsRole('dummy_guard')) //̴��ͤ��̵��
	$dummy_guard_target_list[$user->uname] = $target_uname;
      elseif(in_array($target_uname, $trap_target_list)) //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
      else
	$guard_target_list[$user->uname] = $target_uname;
    }
    //PrintData($guard_target_list, 'Target [guard]');
    //PrintData($dummy_guard_target_list, 'Target [dummy_guard]');

    foreach($vote_data['ESCAPE_DO'] as $uname => $target_uname){ //ƨ˴�Ԥξ������
      $user = $USERS->ByUname($uname);
      if(in_array($target_uname, $trap_target_list)) //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
      elseif($USERS->ByUname($target_uname)->IsWolf()) //ƨ˴�褬��ϵ�ʤ��˴
	$USERS->Kill($user->user_no, 'WOLF_KILLED');
      else
	$escaper_target_list[$user->uname] = $target_uname; //ƨ˴��򥻥å�
    }
    //PrintData($escaper_target_list, 'Target [escaper]');
  }

  do{ //��ϵ�ν�������Ƚ��
    if($ROOM->IsQuiz()) break; //������¼����

    //ŷϵ�� LW Ƚ��
    $living_wolves_list = $USERS->GetLivingWolves();
    //PrintData($living_wolves_list, 'Live Wolf');
    $last_wolf_flag = count($living_wolves_list) == 1 && $voted_wolf->IsRole('sirius_wolf');

    //櫤����֤���Ƥ������˴
    if(in_array($wolf_target->uname, $trap_target_list) &&
       ! (count($living_wolves_list) <= 2  && $voted_wolf->IsRole('sirius_wolf'))){
      $trapped_list[] = $voted_wolf->uname;
      break;
    }

    //ƨ˴�Ԥδ���ź��Ƚ��
    foreach(array_keys($escaper_target_list, $wolf_target->uname) as $uname){
      $USERS->Kill($USERS->UnameToNumber($uname), 'WOLF_KILLED'); //��˴����
    }

    //��ͷϤθ��Ƚ��
    $stack = array_keys($guard_target_list, $wolf_target->uname); //��ҼԤ򸡽�
    //PrintData($stack, 'List [gurad]');
    if(count($stack) > 0){
      foreach($stack as $uname){
	$user = $USERS->ByUname($uname);

	//�������Ƚ�� (��������Τ��оݳ�)
	if($user->IsRole('blind_guard', 'poison_guard') || ! $wolf_target->IsGuardLimited()){
	  $guarded_uname = $wolf_target->uname;
	}

	//��ҼԤ��Ļդ��ä����Ͽ�ϵ�˻������
	if($user->IsRole('hunter_guard')) $USERS->Kill($user->user_no, 'WOLF_KILLED');

	//��ҼԤ�������ä����Ͻ��⤷����ϵ���ܱ����ˤ���
	if($user->IsRole('blind_guard')) $voted_wolf->AddRole('blinder');

	//���������å���������Ͽ
	$str = $user->handle_name . "\t" . $USERS->GetHandleName($wolf_target->uname, true);
	$ROOM->SystemMessage($str, 'GUARD_SUCCESS');
      }
      if(! $last_wolf_flag && $guarded_uname != '') break;
    }

    if($voted_wolf->IsRole('hungry_wolf')){ //��ϵ�Ͽ�ϵ���ŸѰʳ��ʤ齱�⼺��
      if(! $wolf_target->IsDummyBoy() &&
	 ! ($wolf_target->IsWolf() || $wolf_target->IsFox())) break;
    }
    else{
      if($wolf_target->IsWolf()){ //�����褬��ϵ�ξ��ϼ��Ԥ��� (�㡧��ϵ�и�)
	if($voted_wolf->IsRole('emerald_wolf')){ //��ϵ�ν���
	  $role = 'mind_friend[' . strval($voted_wolf->user_no) . ']';
	  $voted_wolf->AddRole($role);
	  $wolf_target->AddRole($role);
	}
	break;
      }

      //�����褬�ŸѤξ��ϼ��Ԥ���
      if($wolf_target->IsFox() && ! $wolf_target->IsRole('white_fox', 'poison_fox') &&
	 ! $wolf_target->IsChildFox()){
	if($voted_wolf->IsRole('blue_wolf') && ! $wolf_target->IsRole('silver_fox')){ //��ϵ�ν���
	  $wolf_target->AddRole('mind_lonely');
	}
	if($wolf_target->IsRole('blue_fox') && ! $voted_wolf->IsRole('silver_wolf')){ //��Ѥν���
	  $voted_wolf->AddRole('mind_lonely');
	}

	$ROOM->SystemMessage($wolf_target->handle_name, 'FOX_EAT');
	break;
      }
    }

    if(! $wolf_target->IsDummyBoy()){ //�ü�ǽ�ϼԤν��� (�����귯���㳰)
      if($voted_wolf->IsRole('sex_wolf')){ //��ϵ�ν���
	$str = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
	$ROOM->SystemMessage($str . $wolf_target->DistinguishSex(), 'SEX_WOLF_RESULT');
	break;
      }

      if($ROOM->date > 1 && $wolf_target->IsRole('escaper')) break; //ƨ˴���ƨ˴�Ԥξ��϶�����

      if(! $last_wolf_flag){ //����ŷϵ��̵��
	if($wolf_target->IsActive('fend_guard')){ //Ǧ�Ԥξ��ϰ��٤����Ѥ���
	  $wolf_target->LostAbility();
	  break;
	}
	if($wolf_target->IsChallengeLovers()) break; //����ξ���̵��

	if($wolf_target->IsRole('ghost_common')) //˴���ξ��Ͼ����Ԥ��դ�
	  $voted_wolf->AddRole('chicken');
	elseif($wolf_target->IsRole('miasma_fox')) //굸Ѥξ���Ǯ�¤��դ�
	  $voted_wolf->AddRole('febris[' . ($ROOM->date + 1) . ']');
      }

      //������ǽ�ϼԤ�Ƚ��
      $stack = array();
      if($wolf_target->IsRole('boss_chiroptera')){ //������ (¾�������ر�)
	foreach($USERS->rows as $user){
	  if($user->IsLive() && ! $user->IsSame($wolf_target->uname) &&
	     $user->IsRoleGroup('chiroptera', 'fairy')) $stack[] = $user->uname;
	}
      }
      elseif($wolf_target->IsRole('doll_master')){ //�ͷ����� (�ͷ���)
	foreach($USERS->rows as $user){
	  if($user->IsLive() && $user->IsDoll()) $stack[] = $user->uname;
	}
      }

      if(count($stack) > 0){
	$target = $USERS->ByUname(GetRandom($stack));
	$USERS->Kill($target->user_no, 'SACRIFICE');
	$sacrifice_list[] = $target->uname;
	break;
      }
    }

    //-- ������� --//
    //��ϵ�ν���
    if($voted_wolf->IsRole('possessed_wolf') && ! $wolf_target->IsDummyBoy() &&
       ! $wolf_target->IsFox() && ! $wolf_target->IsRole('detective_common', 'revive_priest')){
      $possessed_target_list[$voted_wolf->uname] = $wolf_target->uname;
      $wolf_target->dead_flag = true;
      //�����褬����ʤ���ͥꥻ�å�
      if($wolf_target->IsRole('anti_voodoo')) $voted_wolf->possessed_reset = true;
    }
    else{
      $action = $voted_wolf->IsRole('hungry_wolf') ? 'HUNGRY_WOLF_KILLED' : 'WOLF_KILLED';
      $USERS->Kill($wolf_target->user_no, $action); //�̾�ϵ�ν������
    }

    if($voted_wolf->IsActive('tongue_wolf')){ //���ϵ�ν���
      if($wolf_target->IsRole('human')) $voted_wolf->LostAbility(); //¼�ͤʤ�ǽ�ϼ���

      $str = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      $ROOM->SystemMessage($str . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');
    }

    if(! $last_wolf_flag && $wolf_target->IsPoison()){ //-- �ǻ�Ƚ�� --//
      //����Ԥ�����ϵ��������Ը�������ʤ��оݸ���
      if($voted_wolf->IsRole('resist_wolf') || $GAME_CONF->poison_only_eater){
	$poison_target = $voted_wolf;
      }
      else{ //�����Ƥ���ϵ�������������
	$poison_target = $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));
      }

      //����ʤ�̵�� / ��ϵ��ϵ�ˤ���ȯ / Ͷ�ǼԤ���ǽ�ϼԤ��� / �Ƕ�ɱ�����ͤ���
      if($poison_target->IsChallengeLovers() || $wolf_target->IsRole('poison_wolf') ||
	 ($wolf_target->IsRole('guide_poison') && ! $poison_target->IsRoleGroup('poison')) ||
	 ($wolf_target->IsRole('poison_jealousy') && ! $poison_target->IsLovers())) break;

      if($poison_target->IsActive('resist_wolf')) //����ϵ�ʤ�̵��
	$poison_target->LostAbility();
      else
	$USERS->Kill($poison_target->user_no, 'POISON_DEAD_night'); //�ǻ����
    }
  }while(false);
  //PrintData($possessed_target_list, 'Possessed Target [possessed_wolf]');

  if($ROOM->date > 1){
    //��ͷϤμ���оݥꥹ��
    $hunt_target_list = array('jammer_mad', 'voodoo_mad', 'corpse_courier_mad', 'agitate_mad',
			      'miasma_mad', 'dream_eater_mad', 'trap_mad', 'possessed_mad',
			      'phantom_fox', 'voodoo_fox', 'revive_fox', 'possessed_fox',
			      'cursed_fox', 'poison_chiroptera', 'cursed_chiroptera',
			      'boss_chiroptera');
    foreach($guard_target_list as $uname => $target_uname){ //��ͷϤμ��Ƚ��
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true) || $user->IsRole('blind_guard')) continue; //�����å�Ƚ�� (��˴ / ���)

      $target = $USERS->ByUname($target_uname);
      //�оݤ�������ष�Ƥ������ϥ����å�
      if(! in_array($target->uname, $sacrifice_list) &&
	 ($target->IsRole($hunt_target_list) ||
	  ($user->IsRole('hunter_guard') && $target->IsFox()))){
	$USERS->Kill($target->user_no, 'HUNTED');
	$str = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
	$ROOM->SystemMessage($str, 'GUARD_HUNTED');
      }
    }

    foreach($vote_data['VAMPIRE_DO'] as $uname => $target_uname){ //�۷쵴�ν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      if(in_array($target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
	continue;
      }

      $target = $USERS->ByUname($target_uname);
      if($target->IsDead(true) || $target->GetCamp() == 'vampire') continue; //�����å�Ƚ��

      //��ͷϤθ��Ƚ��
      $guard_flag = false;
      foreach(array_keys($guard_target_list, $target->uname) as $guard_uname){
	$guard_user = $USERS->ByUname($guard_uname);

	//�������Ƚ�� (��������Τ��оݳ�)
	if($guard_user->IsRole('blind_guard', 'poison_guard') || ! $target->IsGuardLimited()){
	  $guard_flag = true;
	}

	//��ҼԤ�������ä����Ͻ��⤷���۷쵴���ܱ����ˤ���
	if($guard_user->IsRole('blind_guard')) $user->AddRole('blinder');

	//���������å���������Ͽ
	$str = $guard_user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
	$ROOM->SystemMessage($str, 'GUARD_SUCCESS');
      }
      if(! $guard_flag) $target->AddRole('infected[' . $user->user_no . ']');
    }

    $assassin_target_list = array(); //�Ż��оݼԥꥹ��
    $anti_assassin_flag = count($living_wolves_list) <= 2;
    foreach($vote_data['ASSASSIN_DO'] as $uname => $target_uname){ //�Ż��Ԥξ������
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      if(in_array($target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
	continue;
      }

      $target = $USERS->ByUname($target_uname);
      if($target->IsRole('escaper')) continue; //ƨ˴�ԤϰŻ��Բ�

      if(($anti_assassin_flag && $target->IsRole('sirius_wolf')) ||
	 $target->IsRole('detective_common', 'cursed_fox') || $target->IsChallengeLovers()){
	$assassin_target_list[$uname] = true; //õ�塦ŷϵ��ŷ�ѡ�����ʤ�Ż�ȿ��
      }
      elseif($user->IsRole('doom_assassin')){
	$add_role = 'death_warrant[' . ($ROOM->date + 2) . ']';
	$USERS->ByVirtualUname($target_uname)->AddRole($add_role);
      }
      elseif($user->IsRole('reverse_assassin')){
	$reverse_assassin_target_list[$uname] = $target_uname; //ȿ���оݼԥꥹ�Ȥ��ɲ�
      }
      else{
	if($user->IsRole('soul_assassin')){ //�Ի¤�ν���
	  $sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $target->main_role;
	  $ROOM->SystemMessage($sentence, 'ASSASSIN_RESULT'); //�򿦤���Ͽ

	  //�Ż��褬��ǽ�ϼԤʤ��˴
	  if($target->IsPoison()) $USERS->Kill($user->user_no, 'POISON_DEAD_night');
	}
	elseif($user->IsRole('eclipse_assassin')){ //���Ż��Ԥμ���Ƚ��
	  if(mt_rand(1, 100) <= 30) $target_uname = $uname;
	}
	$assassin_target_list[$target_uname] = true; //�Ż��оݼԥꥹ�Ȥ��ɲ�
      }
    }

    foreach($trapped_list as $uname){ //櫤λ�˴����
      $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');
    }

    foreach($assassin_target_list as $uname => $flag){ //�Ż�����
      $USERS->Kill($USERS->UnameToNumber($uname), 'ASSASSIN_KILLED');
    }

    $reverse_list = array(); //ȿ���оݥꥹ��
    foreach($reverse_assassin_target_list as $uname => $target_uname){
      $target = $USERS->ByUname($target_uname);
      if($target->IsLive(true)){
	$USERS->Kill($target->user_no, 'ASSASSIN_KILLED');
      }
      elseif(! $target->IsLovers()){
	$reverse_list[$target_uname] = ! $reverse_list[$target_uname];
      }
    }
    //PrintData($reverse_list, 'Reverse List'); //�ƥ�����

    //-- ̴�ϥ쥤�䡼 --//
    foreach($vote_data['DREAM_EAT'] as $uname => $target_uname){ //�Ӥν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
      $str = "\t" . $user->handle_name;

      if($target->IsRole('dummy_guard') && $target->IsLive(true)){ //�оݤ�̴��ͤʤ��֤�Ƥ���˹礦
	$USERS->Kill($user->user_no, 'HUNTED');
	$ROOM->SystemMessage($target->handle_name . $str, 'GUARD_HUNTED');
	continue;
      }

      if(in_array($target->uname, $dummy_guard_target_list)){ //̴��ͤθ��Ƚ��
	$hunted_flag = false;
	foreach(array_keys($dummy_guard_target_list, $target->uname) as $uname){ //��ҼԤ򸡽�
	  $guard_user = $USERS->ByUname($uname);
	  if($guard_user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��
	  $hunted_flag = true;
	  $ROOM->SystemMessage($guard_user->handle_name . $str, 'GUARD_HUNTED');
	}

	if($hunted_flag){
	  $USERS->Kill($user->user_no, 'HUNTED');
	  continue;
	}
      }

      //̴��ǽ�ϼԤʤ鿩������
      if($target->IsRoleGroup('dummy', 'fairy')) $USERS->Kill($target->user_no, 'DREAM_KILLED');
    }

    $hunted_list = array(); //��������ԥꥹ��
    foreach($dummy_guard_target_list as $uname => $target_uname){ //̴��ͤμ��Ƚ��
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname);
      if(($target->IsRole('dream_eater_mad') || $target->IsRoleGroup('fairy')) &&
	 $target->IsLive(true)){ //�Ӥμ��Ƚ��
	$hunted_list[$user->handle_name] = $target;
      }

      //������������å������������Ф�
      $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
      $ROOM->SystemMessage($sentence, 'GUARD_SUCCESS');
    }

    foreach($hunted_list as $handle_name => $target){ //�Ӽ�����
      $USERS->Kill($target->user_no, 'HUNTED');
      $sentence = $handle_name . "\t" . $target->handle_name; //���ǽ�ϼԤ��оݳ�
      $ROOM->SystemMessage($sentence, 'GUARD_HUNTED');
    }
    unset($hunted_list);

    //-- �����ϥ쥤�䡼 --//
    foreach($vote_data['ANTI_VOODOO_DO'] as $uname => $target_uname){ //����ξ������
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname);
      $anti_voodoo_target_list[$user->uname] = $target->uname;

      //���ͽ����ʤ饭��󥻥�
      $possessed_list = array_keys($possessed_target_list, $target->uname);
      if(count($possessed_list) > 0){
	foreach($possessed_list as $possessed_uname){
	  $USERS->ByUname($possessed_uname)->possessed_cancel = true;
	}
      }
      elseif($target->IsPossessedGroup() &&
	     $target != $USERS->ByVirtual($target->user_no)){ //��ͼԤʤ鶯������
	if(! array_key_exists($target->uname, $possessed_target_list)){
	  $possessed_target_list[$target->uname] = NULL;
	}
	$target->possessed_reset = true;
      }
      else{
	continue;
      }
      $anti_voodoo_success_list[$target->uname] = true;
    }
    //PrintData($possessed_target_list, 'Possessed Target [anti_voodoo]'); //�ƥ�����
  }

  $voodoo_killer_target_list  = array(); //���ۻդβ���оݥꥹ��
  $voodoo_killer_success_list = array(); //���ۻդβ���������оݥꥹ��
  foreach($vote_data['VOODOO_KILLER_DO'] as $uname => $target_uname){ //���ۻդξ������
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

    $target = $USERS->ByUname($target_uname); //�оݼԤξ�������

    //����Ƚ�� (�����ϡ����ǽ�ϼ�)
    if($target->IsLive(true) && ($target->IsRoleGroup('cursed') || $target->IsPossessedGroup())){
      $USERS->Kill($target->user_no, 'CURSED');
      $voodoo_killer_success_list[$target->uname] = true;
    }

    //���ͽ����ʤ饭��󥻥�
    $possessed_list = array_keys($possessed_target_list, $target->uname);
    if(count($possessed_list) > 0){
      foreach($possessed_list as $possessed_uname){
	$USERS->ByUname($possessed_uname)->possessed_cancel = true;
      }
      $voodoo_killer_success_list[$target->uname] = true;
    }

    $voodoo_killer_target_list[$user->uname] = $target->uname; //����оݥꥹ�Ȥ��ɲ�
  }

  $voodoo_target_list = array(); //���ѷ�ǽ�ϼԤ��оݥꥹ��
  $voodoo_list = array_merge($vote_data['VOODOO_MAD_DO'], $vote_data['VOODOO_FOX_DO']);
  foreach($voodoo_list as $uname => $target_uname){ //���ѷ�ǽ�ϼԤν���
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

    $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
    if($target->IsRoleGroup('cursed') && $target->IsLive(true)){ //���֤�Ƚ��
      if(in_array($user->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $voodoo_killer_target_list)){ //���ۻդβ��Ƚ��
      $voodoo_killer_success_list[$target->uname] = true;
    }
    else{
      $voodoo_target_list[$user->uname] = $target->uname;
    }
  }

  //���ѷ�ǽ�ϼԤ��о��褬�Ťʤä����ϼ��֤��������
  $voodoo_count_list = array_count_values($voodoo_target_list);
  foreach($voodoo_target_list as $uname => $target_uname){
    if($voodoo_count_list[$target_uname] < 2) continue;

    if(in_array($uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
      $anti_voodoo_success_list[$uname] = true;
    }
    else{
      $USERS->Kill($USERS->UnameToNumber($uname), 'CURSED');
    }
  }

  //-- �ꤤ�ϥ쥤�䡼 --//
  $jammer_target_list = array(); //˸���оݥꥹ��
  foreach($vote_data['JAMMER_MAD_DO'] as $uname => $target_uname){ //���ơ���Ѥν���
    $user = $USERS->ByUname($uname);
    if($user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

    $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
    //���֤�Ƚ��
    if(($target->IsRoleGroup('cursed') && ! $target->dead_flag) ||
       in_array($target->uname, $voodoo_target_list)){
      if(in_array($user->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$USERS->Kill($user->user_no, 'CURSED');
	continue;
      }
    }

    if(in_array($target->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
      $anti_voodoo_success_list[$target->uname] = true;
    }
    else{ //˸���оݼԥꥹ�Ȥ��ɲ�
      if($user->IsRole('jammer_fox') && mt_rand(1, 100) > 70) continue; //��Ѥϰ����Ψ�Ǽ��Ԥ���
      $jammer_target_list[$user->uname] = $target->uname;
    }
  }
  //PrintData($jammer_target_list, 'Target [jammer_mad]');

  //��������Τα��Ĥ�Ƚ���о��򿦥ꥹ��
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');

  //�������Υ�å����������ѥꥹ��
  $flower_list = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
		       'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

  //�������Υ�å����������ѥꥹ��
  $star_list = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');

  //�ꤤǽ�ϼԤν�������� (array_merge() �� $uname ����������ź������ǧ�������ΤǻȤ�ʤ�����)
  $mage_list = array();
  $mage_action_list = array('MAGE_DO', 'CHILD_FOX_DO', 'FAIRY_DO');
  foreach($mage_action_list as $action){
    foreach($vote_data[$action] as $uname => $target_uname) $mage_list[$uname] = $target_uname;
  }

  $phantom_user_list = array();
  foreach($mage_list as $uname => $target_uname){ //�ꤤ�Ϥν���
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

    $target = $USERS->ByRealUname($target_uname); //�оݼԤξ�������

    $phantom_flag = false;
    if($target->IsRoleGroup('phantom') && $target->IsActive()){ //���Ϥ�Ƚ��
      if(in_array($user->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
	$anti_voodoo_success_list[$user->uname] = true;
      }
      else{
	$phantom_flag = true;
      }
    }

    if($user->IsRole('dummy_mage')){ //̴���ͤ�Ƚ�� (¼�ͤȿ�ϵ��ȿž������)
      $result = $target->DistinguishMage(true);
    }
    elseif(in_array($user->uname, $jammer_target_list)){ //���Ƥ�˸��Ƚ��
      $result = $user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
    }
    elseif($phantom_flag){ //���Ϥ�Ƚ��
      $result = $user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
      $phantom_user_list[] = $target;
    }
    elseif($user->IsRole('psycho_mage')){ //��������Τ�Ƚ�� (���� / ���Ĥ�)
      $result = 'psycho_mage_normal';
      foreach($psycho_mage_liar_list as $liar_role){
	if($target->IsRoleGroup($liar_role)){
	  $result = 'psycho_mage_liar';
	  break;
	}
      }
    }
    elseif($user->IsRole('sex_mage')){ //�Ҥ褳����Τ�Ƚ�� (���� / ����)
      $result = $target->DistinguishSex();
    }
    elseif($user->IsRole('sex_fox')){ //���Ѥ�Ƚ�� (���� / ���� + �����Ψ�Ǽ���)
      $result = mt_rand(1, 100) > 30 ? $target->DistinguishSex() : 'failed';
    }
    elseif($user->IsRole('stargazer_mage')){ //�����ѻդ�Ƚ�� (��ɼǽ�Ϥ�̵ͭ)
      $result = array_key_exists($target->uname, $ROOM->vote) || $target->IsWolf() ?
	'stargazer_mage_ability' : 'stargazer_mage_nothing';
    }
    elseif($user->IsRole('stargazer_fox')){ //���Ѥ�Ƚ�� (��ɼǽ�Ϥ�̵ͭ + �����Ψ�Ǽ���)
      $result = mt_rand(1, 100) > 30 ?
	(array_key_exists($target->uname, $ROOM->vote) || $target->IsWolf() ?
	 'stargazer_mage_ability' : 'stargazer_mage_nothing') : 'failed';
    }
    else{
      //���֤�Ƚ��
      if(($target->IsRoleGroup('cursed') && $target->IsLive(true)) ||
	 in_array($target->uname, $voodoo_target_list)){
	if(in_array($user->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
	  $anti_voodoo_success_list[$user->uname] = true;
	}
	else{
	  $USERS->Kill($user->user_no, 'CURSED');
	  continue;
	}
      }

      if($user->IsRole('emerald_fox')){ //��Ѥν���
	if($target->IsChildFox() || $target->IsLonely('fox')){
	  $add_role = 'mind_friend[' . strval($user->user_no) . ']';
	  $user->LostAbility();
	  $user->AddRole($add_role);
	  $target->AddRole($add_role);
	}
      }
      elseif($user->IsRole('child_fox')){ //�ҸѤ�Ƚ�� (�����Ψ�Ǽ��Ԥ���)
	$result = $last_wolf_flag ? 'human' :
	  (mt_rand(1, 100) > 30 ? $target->DistinguishMage() : 'failed');
      }
      elseif($user->IsRole('flower_fairy')){ //�������ν���
	$ROOM->SystemMessage($target->handle_name, 'FLOWERED_' . GetRandom($flower_list));
      }
      elseif($user->IsRole('star_fairy')){ //�������ν���
	$ROOM->SystemMessage($target->handle_name, 'CONSTELLATION_' . GetRandom($star_list));
      }
      elseif($user->IsRoleGroup('fairy')){ //�����Ϥν���
	$target_date = $ROOM->date + 1;
	$target->AddRole("bad_status[{$user->user_no}-{$target_date}]");
      }
      else{
	if(array_key_exists($target->uname, $possessed_target_list)){ //��ͥ���󥻥�Ƚ��
	  $target->possessed_cancel = true;
	}

	if($user->IsRole('soul_mage')){ //�����ꤤ�դ�Ƚ�� (�ᥤ����)
	  $result = $target->main_role;
	}
	else{ //�ꤤ�դν���
	  if($target->IsLive(true) && $target->IsFox() && ! $target->IsChildFox() &&
	     ! $target->IsRole('white_fox', 'black_fox')){ //����Ƚ��
	    $USERS->Kill($target->user_no, 'FOX_DEAD');
	  }
	  $result = $last_wolf_flag ? 'human' : $target->DistinguishMage(); //Ƚ���̤����
	}
      }
    }

    //�ꤤ��̤���Ͽ (�ü��ꤤǽ�ϼԤϽ���)
    if($user->IsRole('emerald_fox') || $user->IsRoleGroup('fairy')) continue;
    $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
    $action = $user->IsChildFox() ? 'CHILD_FOX_RESULT' : 'MAGE_RESULT';
    $ROOM->SystemMessage($sentence . "\t" . $result, $action);
  }
  foreach($phantom_user_list as $user) $user->LostAbility(); //���Ϥ�ǽ�ϼ�������

  if($ROOM->date == 1){
    //-- ���ԡ��ϥ쥤�䡼 --//
    //���Ȥ�Ϥ��ɲå����򿦥ꥹ�� (���Ȥ� => ���ȥ��, ������ => ����)
    $scanner_list = array('mind_scanner' => 'mind_read', 'evoke_scanner' => 'mind_evoke');
    foreach($vote_data['MIND_SCANNER_DO'] as $uname => $target_uname){ //���Ȥ�Ϥν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      //�оݼԤ˥����򿦤��ɲ�
      $add_role = $scanner_list[$user->main_role] . '[' . strval($user->user_no) . ']';
      $USERS->ByUname($target_uname)->AddRole($add_role);
    }

    foreach($vote_data['MANIA_DO'] as $uname => $target_uname){ //���åޥ˥��Ϥν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
      if($user->IsRole('unknown_mania')){ //�
	//���ԡ���򥻥å�
	$user->ReplaceRole('unknown_mania', 'unknown_mania[' . strval($target->user_no) . ']');

	//���ļԤ��ɲ�
	$add_role = 'mind_friend[' . strval($user->user_no) . ']';
	$user->AddRole($add_role);
	$target->AddRole($add_role);
      }
      elseif($user->IsRole('trick_mania')){ //���åޥ˥�
	//���ԡ�����
	$actor_flag = false;
	if($target->IsRoleGroup('mania')){ //���åޥ˥��Ϥ���ꤷ������¼��
	  $result =  'human';
	  $actor_flag = true;
	}
	elseif($target->IsRole('revive_priest')){
	  $result = $target->main_role;
	  $actor_flag = true;
	}
	else{
	  foreach($vote_data as $action => $stack){
	    if(array_key_exists($target->uname, $stack)){
	      $actor_flag = true;
	      break;
	    }
	  }
	  $result = $target->main_role;
	}
	$user->ReplaceRole('trick_mania', $result);
	$user->AddRole('copied_trick');
	if(! $actor_flag && ! $target->IsDummyBoy()){
	  switch($target->main_role){
	  case 'human':
	  case 'elder':
	  case 'saint':
	  case 'executor':
	  case 'escaper':
	  case 'suspect':
	  case 'unconscious':
	    $stack_role = 'human';
	    break;

	  case 'reporter':
	  case 'anti_voodoo':
	    $stack_role = 'guard';
	    break;

	  case 'poison_cat':
	  case 'revive_cat':
	  case 'sacrifice_cat':
	    $stack_role = 'poison_cat';
	    break;

	  case 'whisper_scanner':
	  case 'howl_scanner':
	  case 'telepath_scanner':
	    $stack_role = 'mind_scanner';
	    break;

	  case 'doll_master':
	    $stack_role = 'doll';
	    break;

	  case 'miasma_fox':
	    $stack_role = 'child_fox';
	    break;

	  default:
	    $stack_role = array_pop(explode('_', $target->main_role));
	    break;
	  }
	  $target->ReplaceRole($target->main_role, $stack_role);
	}

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'MANIA_RESULT');
      }
      elseif($user->IsRole('soul_mania', 'dummy_mania')){ //���üԡ�̴����
	//���ԡ���򥻥å�
	$user->ReplaceRole($user->main_role, $user->main_role . '[' . strval($target->user_no) . ']');

	//���ԡ���̤���� (���åޥ˥��Ϥ���ꤷ������¼��)
	$result = $target->IsRoleGroup('mania') ? 'human' : DistinguishRoleGroup($target->main_role);

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'MANIA_RESULT');
      }
      else{ //���åޥ˥�
	//���ԡ����� (���åޥ˥��Ϥ���ꤷ������¼��)
	$result = $target->IsRoleGroup('mania') ? 'human' : $target->main_role;
	$user->ReplaceRole('mania', $result);
	$user->AddRole('copied');

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'MANIA_RESULT');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($USERS->rows as $user){ //ŷ�ͤε��Խ���
	if($user->IsDummyBoy() || ! $user->IsRole('revive_priest')) continue;
	if($user->IsLovers()){
	  $user->LostAbility();
	}
	elseif($user->IsLive(true)){
	  $USERS->Kill($user->user_no, 'PRIEST_RETURNED');
	}
      }
    }

    //���ܻȤν���
    $exchange_angel_list  = array();
    $exchange_lovers_list = array();
    $fix_angel_stack      = array();
    $exec_exchange_stack  = array();
    foreach($USERS->rows as $user){ //���ܻȤ��Ǥä����ͤξ�������
      if($user->IsDummyBoy() || ! $user->IsLovers()) continue;
      $cupid_stack = $user->GetPartner('lovers');
      foreach($cupid_stack as $cupid_id){
	$cupid_user = $USERS->ById($cupid_id);
	if($cupid_user->IsRole('exchange_angel')){
	  $exchange_angel_list[$cupid_id][] = $user->user_no;
	  $exchange_lovers_list[$user->user_no][] = $cupid_id;
	  if($user->IsPossessedGroup()) $fix_angel_stack[$cupid_id] = true; //���ǽ�ϼԤʤ��оݳ�
	}
      }
    }
    //PrintData($exchange_angel_list, 'exchange_angel: 1st');
    //PrintData($exchange_lovers_list, 'exchange_lovers: 1st');

    foreach($exchange_angel_list as $id => $lovers_stack){ //��������
      if(array_key_exists($id, $fix_angel_stack)) continue;
      $duplicate_stack = array();
      //PrintData($fix_angel_stack, 'fix_angel:'. $id);
      foreach($lovers_stack as $lovers_id){
	foreach($exchange_lovers_list[$lovers_id] as $cupid_id){
	  if(! array_key_exists($cupid_id, $fix_angel_stack)){
	    $duplicate_stack[$cupid_id] = true;
	  }
	}
      }
      //PrintData($duplicate_stack, 'duplicate:' . $id);
      $duplicate_list = array_keys($duplicate_stack);
      if(count($duplicate_list) > 1){
	$exec_exchange_stack[] = GetRandom($duplicate_list);
	foreach($duplicate_list as $duplicate_id){
	  $fix_angel_stack[$duplicate_id] = true;
	}
      }
      else{
	$exec_exchange_stack[] = $id;
      }
      $fix_angel_stack[$id] = true;
    }
    //PrintData($exec_exchange_stack, 'exec_exchange');

    foreach($exec_exchange_stack as $id){
      $target_list = $exchange_angel_list[$id];
      $lovers_a = $USERS->ByID($target_list[0]);
      $lovers_b = $USERS->ByID($target_list[1]);
      $lovers_a->AddRole('mind_sympathy possessed_exchange[' . $target_list[1] . ']');
      $sentence = $lovers_a->handle_name . "\t" . $lovers_b->handle_name . "\t";
      $ROOM->SystemMessage($sentence . $lovers_b->main_role, 'SYMPATHY_RESULT');

      $lovers_b->AddRole('mind_sympathy possessed_exchange[' . $target_list[0] . ']');
      $sentence = $lovers_b->handle_name . "\t" . $lovers_a->handle_name . "\t";
      $ROOM->SystemMessage($sentence . $lovers_a->main_role, 'SYMPATHY_RESULT');
    }
  }
  else{
    //-- ���Էϥ쥤�䡼 --//
    foreach($vote_data['REPORTER_DO'] as $uname => $target_uname){ //�֥󲰤ν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
      if(in_array($target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$USERS->Kill($user->user_no, 'TRAPPED');
	continue;
      }

      if($target->IsSame($wolf_target->uname)){ //��������
	if($target->IsSame($guarded_uname)) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
	$str = $user->handle_name . "\t" .
	  $USERS->GetHandleName($wolf_target->uname, true) . "\t" .
	  $USERS->GetHandleName($voted_wolf->uname, true);
	$ROOM->SystemMessage($str, 'REPORTER_SUCCESS');
      }
      elseif($target->IsRoleGroup('wolf', 'fox') && $target->IsLive(true)){
	//�����оݤ���ϵ���ŸѤʤ黦�����
	$USERS->Kill($user->user_no, 'REPORTER_DUTY');
      }
    }

    //-- ȿ���ϥ쥤�䡼 --//
    foreach($reverse_list as $target_uname => $flag){
      if(! $flag) continue;
      $target = $USERS->ByUname($target_uname);
      if($target->IsPossessedGroup()){ //���ǽ�ϼ��б�
	if($target->revive_flag) break; //�����Ѥߤʤ饹���å�

	$virtual_target = $USERS->ByVirtual($target->user_no);
	if($target != $virtual_target){ //�����ʤ�ꥻ�å�
	  //�ܿͤ���ͥꥻ�åȽ���
	  $target->ReturnPossessed('possessed_target', $ROOM->date + 1);

	  //�����Υꥻ�åȽ���
	  $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
	}

	//���ͽ��Ԥ��錄�饭��󥻥�
	if(array_key_exists($target->uname, $possessed_target_list)){
	  $target->possessed_reset  = false;
	  $target->possessed_cancel = true;
	}
	elseif(in_array($target->uname, $possessed_target_list)){
	  //�����θ�������ͤ��褦�Ȥ�����ϵ�򸡽�
	  $stack = array_keys($possessed_target_list, $target->uname);
	  $USERS->ByUname($stack[0])->possessed_cancel = true;
	}

	//�ü쥱�����ʤΤǥ٥��˽���
	$virtual_target->Update('live', 'live');
	$virtual_target->revive_flag = true;
	$ROOM->SystemMessage($virtual_target->handle_name, 'REVIVE_SUCCESS');
      }
      else{ //��ͤ���Ƥ�����ꥻ�å�
	$real_target = $USERS->ByReal($target->user_no);
	if($target != $real_target){
	  $target->ReturnPossessed('possessed', $ROOM->date + 1);
	}
	$target->Revive(); //��������
      }
    }

    //-- �����ϥ쥤�䡼 --//
    if(! $ROOM->IsOpenCast()){
      foreach($vote_data['POISON_CAT_DO'] as $uname => $target_uname){ //����ǽ�ϼԤν���
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

	$target = $USERS->ByUname($target_uname); //�оݼԤξ�������

	//����Ƚ��
	if($user->IsRole('poison_cat', 'revive_medium')){
	  $revive_rate = 25;
	}
	elseif($user->IsRole('revive_cat')){
	  $revive_times = (int)$user->partner_list['revive_cat'][0];
	  $revive_rate = ceil(80 / pow(4, $revive_times));
	}
	elseif($user->IsRole('sacrifice_cat', 'revive_fox')){
	  $revive_rate = 100;
	}
	$rate = mt_rand(1, 100); //����Ƚ�������
	//$rate = 5; //mt_rand(1, 10); //�ƥ�����
	//PrintData($revive_rate, 'Revive Info: ' . $user->uname . ' => ' . $target->uname);

	$result = 'failed';
	do{
	  if($rate > $revive_rate) break; //��������
	  if(! $user->IsRole('sacrifice_cat') && $rate <= floor($revive_rate / 5)){ //��������
	    $revive_target_list = array();
	    //�������ο����귯��ǭ����������Ͱʳ��λ�Ԥ���ͼԤ򸡽�
	    foreach($USERS->rows as $revive_target){
	      if($revive_target->IsDummyBoy() || $revive_target->revive_flag ||
		 $target == $revive_target) continue;

	      if($revive_target->dead_flag ||
		 ! $USERS->IsVirtualLive($revive_target->user_no, true)){
		$revive_target_list[] = $revive_target->uname;
	      }
	    }
	    if($ROOM->test_mode) PrintData($revive_target_list, 'Revive Target');
	    if(count($revive_target_list) > 0){ //���䤬��������������ؤ���
	      $target = $USERS->ByUname(GetRandom($revive_target_list));
	    }
	  }
	  //$target = $USERS->ByID(3); //�ƥ�����
	  //PrintData($target->uname, 'Revive User');
	  if($target->IsRoleGroup('cat', 'revive') || $target->IsRole('detective_common') ||
	     $target->IsLovers() || $target->possessed_reset || $target->IsDrop()){
	    break; //����ǽ�ϼԡ����͡���������Ԥʤ���������
	  }

	  $result = 'success';
	  if($target->IsPossessedGroup()){ //���ǽ�ϼ��б�
	    if($target->revive_flag) break; //�����Ѥߤʤ饹���å�

	    $virtual_target = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //������
	      if($target != $virtual_target){ //��͸�˻�˴���Ƥ������ϥꥻ�åȽ�����Ԥ�
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      }
	    }
	    elseif($target->IsLive(true)){ //��¸�� (��;��ֳ���)
	      if($virtual_target->IsDrop()){ //����������б�
		$result = 'failed';
		break;
	      }

	      //�����������������
	      $target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      $ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');

	      //�����λ�Ԥ���������
	      $virtual_target->Revive(true);
	      $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);

	      //���ͽ��Ԥ��錄�饭��󥻥�
  	      if(array_key_exists($target->uname, $possessed_target_list)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	      break;
	    }
	    else{ //����˻���������
	      if($target != $virtual_target){ //�����ʤ�ꥻ�å�
		//�ܿͤ���ͥꥻ�åȽ���
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);

		//�����Υꥻ�åȽ���
		$virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
	      }

	      //���ͽ��Ԥ��錄�饭��󥻥�
	      if(array_key_exists($target->uname, $possessed_target_list)){
		$target->possessed_reset  = false;
		$target->possessed_cancel = true;
	      }
	    }
	  }
	  else{ //��ͤ���Ƥ�����ꥻ�å�
	    $real_target = $USERS->ByReal($target->user_no);
	    if($target != $real_target){
	      $target->ReturnPossessed('possessed', $ROOM->date + 1);
	    }
	  }
	  $target->Revive(); //��������
	}while(false);

	if($result == 'success'){
	  if($user->IsRole('revive_cat')){ //��ì����������������Ȥ򹹿�
	    //$revive_times = (int)$user->partner_list['revive_cat'][0]; //�����ѤߤΤϤ�
	    $base_role = $user->main_role;
	    if($revive_times > 0) $base_role .= '[' . strval($revive_times) . ']';

	    $new_role = $user->main_role . '[' . strval($revive_times + 1) . ']';
	    $user->ReplaceRole($base_role, $new_role);
	  }
	  elseif($user->IsRole('sacrifice_cat')){ //ǭ���λ�˴����
	    $USERS->Kill($user->user_no, 'SACRIFICE');
	  }
	  elseif($user->IsRole('revive_fox')){ //��Ѥ�ǽ�ϼ�������
	    $user->LostAbility();
	  }
	}
	else{
	  $ROOM->SystemMessage($target->handle_name, 'REVIVE_FAILED');
	}
	$sentence = $user->handle_name . "\t";
	$sentence .= $USERS->GetHandleName($target->uname) . "\t" . $result;
	$ROOM->SystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  //-- ��ͥ쥤�䡼 --//
  //PrintData($possessed_target_list, 'Target [possessed_wolf]');
  if($ROOM->date > 1){
    //���ǽ�ϼԤν���
    $possessed_do_stack = array(); //ͭ����;���ꥹ�� (��˴Ƚ�������ꥻ�å�Ƚ��)
    foreach($vote_data['POSSESSED_DO'] as $uname => $target_uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true) || $user->revive_flag) continue; //ľ���˻��Ǥ�����̵�� (�����Ǥ�̵��)

      if(in_array($user->uname, $anti_voodoo_target_list)){ //����θ��Ƚ��
	$anti_voodoo_success_list[$user->uname] = true;
	continue;
      }
      $possessed_do_stack[$uname] = $target_uname;
    }

    foreach($possessed_do_stack as $uname => $target_uname){ //���ǽ�ϼԤν���
      $user = $USERS->ByUname($uname);

      //����Ƚ��1������褬���� / ï������ͤ��Ƥ�
      if(count(array_keys($possessed_do_stack, $target_uname)) > 1 ||
	 $target_uname != $USERS->ByRealUname($target_uname)->uname) continue;

      $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
      //����Ƚ��2����������Ƥ��� / ��ϵ����������򿦤Ǥ���
      if($target->revive_flag || $target->IsRole('detective_common', 'revive_priest')) continue;

      //�ͳ���¾�رĤοͳ��ˤ���ͤǤ��ʤ�
      switch($target->GetCamp(true)){
      case 'wolf':
	if($user->IsRole('possessed_fox')) continue 2;
	break;

      case 'fox':
	if($user->IsRole('possessed_mad')) continue 2;
	break;

      case 'lovers':
	continue 2;
      }
      $possessed_target_list[$user->uname] = $target_uname;
    }
    //PrintData($possessed_target_list, 'Target [Possessed]');
  }

  //-- ��ͽ��� --//
  $possessed_date = $ROOM->date + 1; //��ͤ����������
  foreach($possessed_target_list as $uname => $target_uname){
    $user         = $USERS->ByUname($uname); //��ͼ�
    $target       = $USERS->ByUname($target_uname); //���ͽ����
    $virtual_user = $USERS->ByVirtual($user->user_no); //���ߤ������
    $array = array(); //�������

    if($user->IsDead(true)){ //��ͼԻ�˴
      $target->dead_flag = false; //��˴�ե饰��ꥻ�å�
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //�����б�
    }
    elseif($user->possessed_reset){ //��ͥꥻ�å�
      if(isset($target->user_no)){
	$target->dead_flag = false; //��˴�ե饰��ꥻ�å�
	$USERS->Kill($target->user_no, 'WOLF_KILLED');
	if($target->revive_flag) $target->Update('live', 'live'); //�����б�
      }

      if($user != $virtual_user){ //�����ʤ鸵���Τ��ᤵ���
	//�����Υꥻ�åȽ���
	$virtual_user->ReturnPossessed('possessed', $possessed_date);
	$virtual_user->SaveLastWords();
	$ROOM->SystemMessage($virtual_user->handle_name, 'POSSESSED_RESET');

	//�����������������
	$user->ReturnPossessed('possessed_target', $possessed_date);
	$user->SaveLastWords($virtual_user->handle_name);
	$ROOM->SystemMessage($user->handle_name, 'REVIVE_SUCCESS');
      }
      continue;
    }
    elseif($user->possessed_cancel || $target->revive_flag){ //��ͼ���
      $target->dead_flag = false; //��˴�ե饰��ꥻ�å�
      $USERS->Kill($target->user_no, 'WOLF_KILLED');
      if($target->revive_flag) $target->Update('live', 'live'); //�����б�
      continue;
    }
    else{ //�������
      if($user->IsRole('possessed_wolf')){
	$target->dead_flag = false; //��˴�ե饰��ꥻ�å�
	$USERS->Kill($target->user_no, 'POSSESSED_TARGETED'); //�����λ�˴����
	//����褬ï������ͤ��Ƥ��륱����������Τǲ��ۥ桼���Ǿ�񤭤���
	$target = $USERS->ByVirtual($target->user_no);
      }
      else{
	$ROOM->SystemMessage($target->handle_name, 'REVIVE_SUCCESS');
	$user->LostAbility();
      }
      $target->AddRole("possessed[{$possessed_date}-{$user->user_no}]");

      //��ͽ���
      $user->AddRole("possessed_target[{$possessed_date}-{$target->user_no}]");
      $ROOM->SystemMessage($virtual_user->handle_name, 'POSSESSED');
      $user->SaveLastWords($virtual_user->handle_name);
      $user->Update('last_words', '');
    }

    if($user != $virtual_user){
      $virtual_user->ReturnPossessed('possessed', $possessed_date);
      if($user->IsLive(true)) $virtual_user->SaveLastWords();
    }
  }

  //PrintData($voodoo_killer_success_list, 'SUCCESS [voodoo_killer]');
  foreach($voodoo_killer_success_list as $target_uname => $flag){ //���ۻդβ����̽���
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'VOODOO_KILLER_SUCCESS';

    $voodoo_killer_list = array_keys($voodoo_killer_target_list, $target_uname); //�����Ԥ򸡽�
    foreach($voodoo_killer_list as $uname){
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

  //PrintData($anti_voodoo_success_list, 'SUCCESS [anti_voodoo]');
  foreach($anti_voodoo_success_list as $target_uname => $flag){ //�������ʧ����̽���
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'ANTI_VOODOO_SUCCESS';

    $anti_voodoo_list = array_keys($anti_voodoo_target_list, $target_uname); //�����Ԥ򸡽�
    foreach($anti_voodoo_list as $uname){
      $ROOM->SystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

  if($ROOM->date == 3){ //���üԡ�̴�����Υ��ԡ�����
    $soul_mania_replace_list = array(
      'human' => 'executor',
      'mage' => 'soul_mage',
      'necromancer' => 'soul_necromancer',
      'medium' => 'revive_medium',
      'priest' => 'bishop_priest',
      'guard' => 'poison_guard',
      'common' => 'ghost_common',
      'poison' => 'strong_poison',
      'poison_cat' => 'revive_cat',
      'pharmacist' => 'pharmacist',
      'assassin' => 'soul_assassin',
      'mind_scanner' => 'howl_scanner',
      'jealousy' => 'poison_jealousy',
      'doll' => 'doll_master',
      'wolf' => 'sirius_wolf',
      'mad' => 'whisper_mad',
      'fox' => 'cursed_fox',
      'child_fox' => 'jammer_fox',
      'cupid' => 'mind_cupid',
      'angel' => 'ark_angel',
      'quiz' => 'quiz',
      'vampire' => 'vampire',
      'chiroptera' => 'boss_chiroptera',
      'fairy' => 'light_fairy');
    $dummy_mania_replace_list = array(
      'human' => 'suspect',
      'mage' => 'dummy_mage',
      'necromancer' => 'dummy_necromancer',
      'medium' => 'revive_medium',
      'priest' => 'crisis_priest',
      'guard' => 'dummy_guard',
      'common' => 'dummy_common',
      'poison' => 'dummy_poison',
      'poison_cat' => 'sacrifice_cat',
      'pharmacist' => 'cure_pharmacist',
      'assassin' => 'eclipse_assassin',
      'mind_scanner' => 'mind_scanner',
      'jealousy' => 'jealousy',
      'doll' => 'doll',
      'wolf' => 'cute_wolf',
      'mad' => 'mad',
      'fox' => 'cute_fox',
      'child_fox' => 'sex_fox',
      'cupid' => 'self_cupid',
      'angel' => 'angel',
      'quiz' => 'quiz',
      'vampire' => 'vampire',
      'chiroptera' => 'dummy_chiroptera',
      'fairy' => 'mirror_fairy');
    foreach($USERS->rows as $user){
      if($user->IsDummyBoy() || ! $user->IsRole('soul_mania', 'dummy_mania')) continue;
      $target_id = array_shift($user->GetPartner($user->main_role));
      $target = $USERS->ById($target_id);
      $target_role = DistinguishRoleGroup($target->main_role);
      //PrintData($target_role, $user->uname);
      if($user->IsRole('soul_mania')){
	$base_role = 'soul_mania[' . $target_id . ']';
	$replace_list = $soul_mania_replace_list;
	$copied_role = 'copied_soul';
      }
      else{
	$base_role = 'dummy_mania[' . $target_id . ']';
	$replace_list = $dummy_mania_replace_list;
	$copied_role = 'copied_teller';
      }
      $result = $target->IsRoleGroup('mania', 'copied') ? 'human' : $replace_list[$target_role];
      $user->ReplaceRole($base_role, $result);
      $user->AddRole($copied_role);

      $sentence = $user->handle_name . "\t" . $user->handle_name . "\t" . $result;
      $ROOM->SystemMessage($sentence, 'MANIA_RESULT');
    }
  }

  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����

  //-- �ʺ׷ϥ쥤�䡼 --//
  $priest_flag = false;
  $bishop_priest_flag = false;
  $border_priest_list = array();
  $crisis_priest_flag = false;
  $revive_priest_list = array();
  $live_count = array();
  $dead_count = 0;
  foreach($USERS->rows as $user){ //�ʺ׷Ϥξ������
    if(! $user->IsDummyBoy()){
      $priest_flag        |= $user->IsRole('priest');
      $bishop_priest_flag |= $user->IsRole('bishop_priest');
      if($user->IsRole('border_priest')) $border_priest_list[] = $user;
      $crisis_priest_flag |= $user->IsRole('crisis_priest');
      if($user->IsActive('revive_priest')) $revive_priest_list[] = $user->uname;
    }
    if($user->IsDead(true)){
      if($user->GetCamp() != 'human') $dead_count++;
      continue;
    }

    $live_count['total']++;
    if($user->IsWolf()) $live_count['wolf']++;
    elseif($user->IsFox()) $live_count['fox']++;
    else{
      $live_count['human']++;
      if($user->GetCamp() == 'human') $live_count['human_side']++;
    }
    if($user->IsLovers()) $live_count['lovers']++;
  }
  //PrintData($live_count, 'Live Count');

  if($priest_flag && $ROOM->date > 2 && ($ROOM->date % 2) == 1){ //�ʺפν���
    $ROOM->SystemMessage($live_count['human_side'], 'PRIEST_RESULT');
  }
  if($bishop_priest_flag && $ROOM->date > 1 && ($ROOM->date % 2) == 0){ //�ʶ��ν���
    $ROOM->SystemMessage($dead_count, 'BISHOP_PRIEST_RESULT');
  }
  if(count($border_priest_list) > 0 && $ROOM->date > 1){ //�����դν���
    foreach($border_priest_list as $user){
      $stack_count = 0;
      foreach($ROOM->vote as $uname => $stack){
	if($stack['target_uname'] == $user->uname) $stack_count++;
      }
      $ROOM->SystemMessage($user->handle_name . "\t" . $stack_count, 'BORDER_PRIEST_RESULT');
    }
  }

  if($crisis_priest_flag || count($revive_priest_list) > 0){ //�¸��ԡ�ŷ�ͤν���
    //�ֿͳ�����������Ƚ��
    $crisis_priest_result = '';
    if($live_count['total'] - $live_count['lovers'] <= 2){
      $crisis_priest_result = 'lovers';
    }
    elseif($live_count['human'] - $live_count['wolf'] <= 2 || $live_count['wolf'] == 1){
      if($live_count['lovers'] > 1)
	$crisis_priest_result = 'lovers';
      elseif($live_count['fox'] > 0)
	$crisis_priest_result = 'fox';
      elseif($live_count['human'] - $live_count['wolf'] <= 2)
	$crisis_priest_result = 'wolf';
    }

    if($crisis_priest_flag && $crisis_priest_result != ''){ //�¸��Ԥν���
      $ROOM->SystemMessage($crisis_priest_result, 'CRISIS_PRIEST_RESULT');
    }

    //ŷ�ͤ�����Ƚ�����
    if(! $ROOM->IsOpenCast() && count($revive_priest_list) > 0 &&
       ($ROOM->date == 4 || $crisis_priest_result != '' || $live_count['wolf'] == 1 ||
	count($USERS->rows) >= $live_count['total'] * 2)){
      foreach($revive_priest_list as $uname){
	$user = $USERS->ByUname($uname);
	if($user->IsLovers() || ($ROOM->date >= 4 && $user->IsLive(true))){
	  $user->LostAbility();
	}
	elseif($user->IsDead(true)){
	  $user->Revive();
	  $user->LostAbility();
	}
      }
    }
  }

  if($ROOM->test_mode) return;

  //�������ˤ���
  $ROOM->date++;
  $ROOM->day_night = 'day';
  SendQuery("UPDATE room SET date = {$ROOM->date}, day_night = 'day' WHERE room_no = {$ROOM->id}");

  //�뤬����������
  $ROOM->Talk("MORNING\t" . $ROOM->date);
  $ROOM->SystemMessage(1, 'VOTE_TIMES'); //�跺��ɼ�Υ�����Ȥ� 1 �˽����(����ɼ��������)
  $ROOM->UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  //DeleteVote(); //���ޤǤ���ɼ���������

  CheckVictory(); //���ԤΥ����å�
  SendCommit(); //������ߥå�
}

//�򿦤ν�°���롼�פ�Ƚ�̤���
function DistinguishRoleGroup($role){
  global $ROLE_DATA;

  foreach($ROLE_DATA->main_role_group_list as $key => $value){
    if(strpos($role, $key) !== false) return $value;
  }
  return 'human';
}

//�������å���������������
function InsertRandomMessage(){
  global $MESSAGE, $GAME_CONF, $ROOM;

  if(! $GAME_CONF->random_message) return;
  $ROOM->Talk(GetRandom($MESSAGE->random_message_list));
}
