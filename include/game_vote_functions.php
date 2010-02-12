<?php
//��ɼ��̽���
function OutputVoteResult($sentence, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //���ޤǤ���ɼ���������
  $title  = '��Ͽ�ϵ�ʤ�䡩[��ɼ���]';
  $header = '<div align="center"><a name="#game_top"></a>';
  $footer = '<br>'."\n" . $back_url . '</div>';
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
  elseif(strpos($option_role, 'duel') !== false){ //��Ʈ¼
    $role_list = array(); //�����ꥻ�å�
    //ϵ1.5:�Ż�2:�ᰦ6.5 -> wolf:3 / assassin:4 / self_cupid:13 / Total:20
    $role_list['wolf'] = round($user_count / 20 * 3);
    $role_list['assassin'] = round($user_count / 20 * 4);
    $role_list['self_cupid'] = $user_count - ($role_list['wolf'] + $role_list['assassin']);
    /*
    $role_list['wolf'] = round($user_count / 5);
    $role_list['trap_mad'] = round(($user_count - $role_list['wolf']) / 3);
    $role_list['assassin'] = $user_count - ($role_list['wolf'] + $role_list['trap_mad']);
    */
  }
  elseif($ROOM->IsOption('chaosfull')){ //��������
    $random_role_list = array();

    //-- �Ǿ����� --//
    foreach($CAST_CONF->chaos_fix_role_list as $key => $value){ //�Ǿ������ѥꥹ��
      $fix_role_group_list[DistinguishRoleGroup($key)] = $value;
    }

    //��ϵ
    $add_count = round($user_count / $CAST_CONF->min_wolf_rate) - $fix_role_group_list['wolf'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if(    $rand <  3) $random_role_list['boss_wolf']++;
      elseif($rand <  5) $random_role_list['tongue_wolf']++;
      elseif($rand <  8) $random_role_list['wise_wolf']++;
      elseif($rand < 11) $random_role_list['poison_wolf']++;
      elseif($rand < 15) $random_role_list['resist_wolf']++;
      elseif($rand < 16) $random_role_list['cursed_wolf']++;
      elseif($rand < 18) $random_role_list['possessed_wolf']++;
      elseif($rand < 28) $random_role_list['cute_wolf']++;
      elseif($rand < 31) $random_role_list['scarlet_wolf']++;
      elseif($rand < 33) $random_role_list['silver_wolf']++;
      else               $random_role_list['wolf']++;
    }

    //�Ÿ�
    $add_count = floor($user_count / $CAST_CONF->min_fox_rate) - $fix_role_group_list['fox'];
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 100);
      if(    $rand <  2)  $random_role_list['white_fox']++;
      elseif($rand <  5)  $random_role_list['black_fox']++;
      elseif($rand <  8)  $random_role_list['poison_fox']++;
      elseif($rand < 10)  $random_role_list['voodoo_fox']++;
      elseif($rand < 11)  $random_role_list['revive_fox']++;
      elseif($rand < 12)  $random_role_list['cursed_fox']++;
      elseif($rand < 15)  $random_role_list['cute_fox']++;
      elseif($rand < 18)  $random_role_list['scarlet_fox']++;
      elseif($rand < 20)  $random_role_list['silver_fox']++;
      elseif($rand < 22)  $random_role_list['child_fox']++;
      else                $random_role_list['fox']++;
    }

    //-- ���������� --//
    $add_count = $user_count - (array_sum($random_role_list) +
				array_sum($CAST_CONF->chaos_fix_role_list));
    for(; $add_count > 0; $add_count--){
      $rand = mt_rand(1, 1000);
      if(    $rand <  20) $random_role_list['mage']++;
      elseif($rand <  25) $random_role_list['soul_mage']++;
      elseif($rand <  35) $random_role_list['psycho_mage']++;
      elseif($rand <  45) $random_role_list['sex_mage']++;
      elseif($rand <  55) $random_role_list['voodoo_killer']++;
      elseif($rand <  65) $random_role_list['dummy_mage']++;
      elseif($rand < 100) $random_role_list['necromancer']++;
      elseif($rand < 105) $random_role_list['soul_necromancer']++;
      elseif($rand < 115) $random_role_list['yama_necromancer']++;
      elseif($rand < 130) $random_role_list['dummy_necromancer']++;
      elseif($rand < 155) $random_role_list['medium']++;
      elseif($rand < 165) $random_role_list['priest']++;
      elseif($rand < 170) $random_role_list['crisis_priest']++;
      elseif($rand < 180) $random_role_list['revive_priest']++;
      elseif($rand < 260) $random_role_list['common']++;
      elseif($rand < 270) $random_role_list['dummy_common']++;
      elseif($rand < 310) $random_role_list['guard']++;
      elseif($rand < 315) $random_role_list['poison_guard']++;
      elseif($rand < 325) $random_role_list['reporter']++;
      elseif($rand < 340) $random_role_list['anti_voodoo']++;
      elseif($rand < 360) $random_role_list['dummy_guard']++;
      elseif($rand < 380) $random_role_list['poison']++;
      elseif($rand < 385) $random_role_list['strong_poison']++;
      elseif($rand < 395) $random_role_list['incubate_poison']++;
      elseif($rand < 410) $random_role_list['dummy_poison']++;
      elseif($rand < 420) $random_role_list['poison_cat']++;
      elseif($rand < 425) $random_role_list['revive_cat']++;
      elseif($rand < 455) $random_role_list['pharmacist']++;
      elseif($rand < 475) $random_role_list['assassin']++;
      elseif($rand < 495) $random_role_list['mind_scanner']++;
      elseif($rand < 505) $random_role_list['evoke_scanner']++;
      elseif($rand < 520) $random_role_list['jealousy']++;
      elseif($rand < 530) $random_role_list['suspect']++;
      elseif($rand < 540) $random_role_list['unconscious']++;
      elseif($rand < 590) $random_role_list['wolf']++;
      elseif($rand < 600) $random_role_list['boss_wolf']++;
      elseif($rand < 615) $random_role_list['tongue_wolf']++;
      elseif($rand < 630) $random_role_list['wise_wolf']++;
      elseif($rand < 645) $random_role_list['poison_wolf']++;
      elseif($rand < 660) $random_role_list['resist_wolf']++;
      elseif($rand < 665) $random_role_list['cursed_wolf']++;
      elseif($rand < 675) $random_role_list['possessed_wolf']++;
      elseif($rand < 705) $random_role_list['cute_wolf']++;
      elseif($rand < 715) $random_role_list['scarlet_wolf']++;
      elseif($rand < 730) $random_role_list['silver_wolf']++;
      elseif($rand < 750) $random_role_list['mad']++;
      elseif($rand < 760) $random_role_list['fanatic_mad']++;
      elseif($rand < 765) $random_role_list['whisper_mad']++;
      elseif($rand < 775) $random_role_list['jammer_mad']++;
      elseif($rand < 785) $random_role_list['voodoo_mad']++;
      elseif($rand < 800) $random_role_list['corpse_courier_mad']++;
      elseif($rand < 810) $random_role_list['dream_eater_mad']++;
      elseif($rand < 820) $random_role_list['trap_mad']++;
      elseif($rand < 830) $random_role_list['fox']++;
      elseif($rand < 837) $random_role_list['white_fox']++;
      elseif($rand < 842) $random_role_list['black_fox']++;
      elseif($rand < 849) $random_role_list['poison_fox']++;
      elseif($rand < 854) $random_role_list['voodoo_fox']++;
      elseif($rand < 859) $random_role_list['revive_fox']++;
      elseif($rand < 862) $random_role_list['cursed_fox']++;
      elseif($rand < 869) $random_role_list['cute_fox']++;
      elseif($rand < 874) $random_role_list['scarlet_fox']++;
      elseif($rand < 880) $random_role_list['silver_fox']++;
      elseif($rand < 890) $random_role_list['child_fox']++;
      elseif($rand < 915) $random_role_list['cupid']++;
      elseif($rand < 925) $random_role_list['self_cupid']++;
      elseif($rand < 930) $random_role_list['mind_cupid']++;
      elseif($rand < 945) $random_role_list['chiroptera']++;
      elseif($rand < 950) $random_role_list['poison_chiroptera']++;
      elseif($rand < 955) $random_role_list['cursed_chiroptera']++;
      elseif($rand < 960) $random_role_list['dummy_chiroptera']++;
      elseif($rand < 980) $random_role_list['mania']++;
      elseif($rand < 990) $random_role_list['unknown_mania']++;
      elseif($rand < 993) $random_role_list['quiz']++;
      else                $random_role_list['human']++;
    }

    //������ȸ������
    $role_list = $random_role_list;
    foreach($CAST_CONF->chaos_fix_role_list as $key => $value){
      $role_list[$key] += (int)$value;
    }
    //PrintData($role_list, '1st_list'); //�ƥ�����

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
	$this_key = key($random_role_group_list->$name);
	//PrintData($this_key, "����target");
	$random_role_group_list->{$name}[$this_key]--;
	$role_list[$this_key]--;
	$role_list['human']++;
	//PrintData($random_role_group_list->$name, "����$over_count: delete");

	//0 �ˤʤä��򿦤ϥꥹ�Ȥ������
	if($role_list[$this_key] < 1) unset($role_list[$this_key]);
	if($random_role_group_list->{$name}[$this_key] < 1){
	  unset($random_role_group_list->{$name}[$this_key]);
	}
      }
    }
    //PrintData($role_list, '2nd_list'); //�ƥ�����

    //���åޥ˥�¼�ʳ��ʤ������ʾ��¼�ͤ��̤��򿦤˿����֤�
    if(strpos($option_role, 'full_mania') === false){
      $over_count = $role_list['human'] - round($user_count * $CAST_CONF->max_human_rate);
      if($over_count > 0){
	$role_list[$CAST_CONF->chaos_replace_human_role] += $over_count;
	$role_list['human'] -= $over_count;
	//PrintData($role_list, '3rd_list'); //�ƥ�����
      }
    }
  }
  elseif($ROOM->IsOption('chaos')){ //����
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
  }
  else{ //�̾�¼
    //���Ǽ� (¼��2 �� ���Ǽ�1����ϵ1)
    if(strpos($option_role, 'poison') !== false && $user_count >= $CAST_CONF->poison){
      $role_list['human'] -= 2;
      $role_list['poison']++;
      $role_list['wolf']++;
    }

    //���塼�ԥå� (14�ͤϥϡ��ɥ����� / ¼�� �� ���塼�ԥå�)
    if(strpos($option_role, 'cupid') !== false &&
       ($user_count == 14 || $user_count >= $CAST_CONF->cupid)){
      $role_list['human']--;
      $role_list['cupid']++;
    }

    //��ϵ (��ϵ �� ��ϵ)
    if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $CAST_CONF->boss_wolf){
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //��ϵ (��ϵ �� ��ϵ��¼�� �� ����)
    if(strpos($option_role, 'poison_wolf') !== false && $user_count >= $CAST_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //���åޥ˥� (¼�� �� ���åޥ˥�)
    if(strpos($option_role, 'mania') !== false && $user_count >= $CAST_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //��� (¼�� �� ���1��������1)
    if(strpos($option_role, 'medium') !== false && $user_count >= $CAST_CONF->medium){
      $role_list['human'] -= 2;
      $role_list['medium']++;
      $role_list['fanatic_mad']++;
    }
  }

  //���åޥ˥�¼
  if(strpos($option_role, 'full_mania') !== false){
    $role_list['mania'] += $role_list['human'];
    $role_list['human'] = 0;
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
    //PrintData($role_count, '���顼�������');
    //return $now_role_list;
    $sentence = '¼�� (' . $user_count . ') ������ο� (' . $role_count . ') �����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  return $now_role_list;
}

//�򿦤οͿ����Υꥹ�Ȥ��������
function MakeRoleNameList($role_count_list, $chaos = NULL){
  global $GAME_CONF;

  $main_role_key_list = array_keys($GAME_CONF->main_role_list);
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
      elseif(strpos($key, 'cupid') !== false)
	$main_role_list['cupid'] += $value;
      elseif(strpos($key, 'mania') !== false)
	$main_role_list['mania'] += $value;
      elseif(strpos($key, 'quiz') !== false)
	$main_role_list['quiz'] += $value;
      elseif(strpos($key, 'chiroptera') !== false)
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

  $sub_role_key_list = array_keys($GAME_CONF->sub_role_list);
  switch($chaos){
  case 'camp':
  case 'role':
    $sub_type = '��';
    $sub_role_list = array();
    foreach($role_count_list as $key => $value){
      if(! in_array($key, $sub_role_key_list)) continue;
      foreach($GAME_CONF->sub_role_group_list as $class => $list){
	if(! in_array($key, $list)) continue;
	$sub_role_list[$list[0]] += $value;
      }
    }
    break;

  default:
    $sub_role_list = $role_count_list;
    break;
  }

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$main_role_list[$key];
    if($count < 1) continue;
    if($sentence != '') $sentence .= '��';
    $sentence .= $value . $main_type . $count;
  }

  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$sub_role_list[$key];
    if($count > 0) $sentence .= '��(' . $value . $sub_type . $count . ')';
  }
  return $header . $sentence;
}

//�����ɼ���׽���
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS;

  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  $user_list = $USERS->GetLivingUsers(); //�����Ƥ���桼�������

  //��ɼ��������������������ɼ���Ƥ��ʤ���н��������å�
  if($ROOM->LoadVote() != count($user_list)) return false;
  //PrintData($ROOM->vote, 'Vote'); //�ƥ�����

  $max_voted_number = 0; //��¿��ɼ��
  $vote_kill_uname = ''; //�跺�����ͤΥ桼��̾
  $live_uname_list   = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list  = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��桼��̾)
  $vote_count_list   = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list      = array(); //ǽ�ϼԤ�������ɼ���

  foreach($ROOM->vote as $uname => $list){ //�����ɼ�ǡ��������
    $target_uname = $USERS->ByVirtualUname($list['target_uname'])->uname;
    $vote_count_list[$target_uname] += $list['vote_number'];
  }
  //PrintData($vote_count_list, 'Vote Count Base');

  foreach($user_list as $uname){ //���̤���ɼ�ǡ��������
    $user = $USERS->ByVirtualUname($uname); //���ۥ桼�������
    $list = $ROOM->vote[$uname]; //��ɼ�ǡ���
    $target = $USERS->ByVirtualUname($list['target_uname']); //��ɼ��β��ۥ桼��
    $vote_number  = (int)$list['vote_number']; //��ɼ��
    $voted_number = (int)$vote_count_list[$user->uname]; //��ɼ��

    //�򿦤���ɼ����
    if($user->IsRole('upper_luck')) //����
      $voted_number += ($ROOM->date == 2 ?  4 : -2);
    elseif($user->IsRole('downer_luck')) //��ȯ��
      $voted_number += ($ROOM->date == 2 ? -4 :  2);
    elseif($user->IsRole('random_luck')) //��������
      $voted_number += (mt_rand(1, 5) - 3);
    elseif($user->IsRole('star')) //�͵���
      $voted_number--;
    elseif($user->IsRole('disfavor')) //�Կ͵�
      $voted_number++;

    if($voted_number < 0) $voted_number = 0; //�ޥ��ʥ��ˤʤäƤ����� 0 �ˤ���

    //�����ƥ��å������Ѥ����������
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);
    //PrintData($message_list, $uname); //�ƥ�����

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
  //PrintData($vote_count_list, 'Vote Count'); //�ƥ�����

  //ȿ�ռԤ�Ƚ��
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

  //��ɼ��̤򥿥ֶ��ڤ���������ƥ����ƥ��å���������Ͽ
  //PrintData($vote_message_list, 'Vote Message'); //�ƥ�����
  foreach($live_uname_list as $uname){
    extract($vote_message_list[$uname]); //�����Ÿ��

    //������ɼ���򹹿�
    if($voted_number > $max_voted_number) $max_voted_number = $voted_number;

    //(ï�� [TAB] ï�� [TAB] ��ʬ����ɼ�� [TAB] ��ʬ����ɼ�� [TAB] ��ɼ���)
    $sentence = $USERS->GetHandleName($uname) . "\t" . $target . "\t" .
      $voted_number ."\t" . $vote_number . "\t" . $RQ_ARGS->vote_times;
    InsertSystemMessage($sentence, 'VOTE_KILL');
  }

  //������ɼ���Υ桼��̾ (�跺�����) �Υꥹ�Ȥ����
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  //PrintData($max_voted_uname_list, 'Max Voted'); //�ƥ�����
  do{ //�跺�Է���롼����
    if(count($max_voted_uname_list) == 1){ //��ͤ����ʤ����
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    $decide_order_list = array('decide', 'bad_luck', 'impatience');
    foreach($decide_order_list as $role){ //����ǽ�ϼԤ�Ƚ��
      $ability = $ability_list[$role];
      if(isset($ability) && in_array($ability, $max_voted_uname_list)){
	$vote_kill_uname = $ability;
	break 2;
      }
    }

    $luck_order_list = array('good_luck', 'plague');
    foreach($luck_order_list as $role){ //����ǽ�ϼԤ�Ƚ��
      if(is_null($ability = $ability_list[$role])) continue;
      if(($key = array_search($ability, $max_voted_uname_list)) === false) continue;
      unset($max_voted_uname_list[$key]);
      if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
	$vote_kill_uname = array_shift($max_voted_uname_list);
	break 2;
      }
    }
  }while(false);

  if($vote_kill_uname != ''){ //�跺�����¹�
    $vote_target = $USERS->ByRealUname($vote_kill_uname); //�桼����������
    $USERS->Kill($vote_target->user_no, 'VOTE_KILLED'); //�跺����
    unset($live_uname_list[$vote_target->user_no]); //�跺�Ԥ���¸�ԥꥹ�Ȥ������
    $voter_list = array_keys($vote_target_list, $vote_target->uname); //��ɼ�����ͤ����

    $pharmacist_success = false; //���������ե饰������
    foreach($user_list as $this_uname){ //���դν���
      $user = $USERS->ByUname($this_uname);
      if(! $user->IsRole('pharmacist')) continue;

      $this_target = $USERS->ByUname($vote_target_list[$user->uname]); //��ɼ��ξ�������
      if(! $this_target->IsRoleGroup('poison') || $this_target->IsRole('dummy_poison')){
	$this_result = 'nothing'; //����ǽ�ϼԤ�̴�Ǽ�
      }
      elseif($this_target->IsRole('poison_guard')) $this_result = 'limited'; //���Τ��оݳ�
      else{
	if($this_target->IsRole('strong_poison')) $this_result = 'strong'; //���Ǽ�
	elseif($this_target->IsRole('incubate_poison')){ //���ǼԤ� 5 ���ܰʹߤ˶��Ǥ����
	  $this_result = ($ROOM->date >= 5 ? 'strong' : 'nothing');
	}
	else $this_result = 'poison';

	//�跺�Ԥʤ���Ǥ���
	if($this_target == $vote_target && ($this_result == 'strong' || $this_result == 'poison')){
	  $this_result = 'success';
	  $pharmacist_success = true;
	}
      }

      //�����̤���Ͽ
      $virtual_handle_name = $USERS->ByVirtual($this_target->user_no)->handle_name;
      $sentence = $user->handle_name . "\t" . $virtual_handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'PHARMACIST_RESULT');
    }

    //�跺���줿�ͤ��Ǥ���äƤ������
    do{
      if($pharmacist_success || ! $vote_target->IsPoison()) break; //��ǽ�Ϥ�ȯưȽ��

      //�Ǥ��оݥ��ץ���������å����Ƹ���ԥꥹ�Ȥ����
      $poison_target_list = array(); //�Ǥ��оݥꥹ��
      $target_list = ($GAME_CONF->poison_only_voter ? $voter_list : $live_uname_list);
      //PrintData($target_list); //�ƥ�����

      foreach($target_list as $uname){ //����оݳ����򿦤����
	if(! $USERS->ByRealUname($uname)->IsRole('quiz')){
	  $poison_target_list[] = $uname;
	}
      }
      //PrintData($poison_target_list); //�ƥ�����

      $limited_list = array(); //�ü��Ǥξ��ϥ������åȤ����ꤵ���
      if($vote_target->IsRole('strong_poison', 'incubate_poison')){ //���Ǽԡ����Ǽ�
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_wolf')){ //��ϵ
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsWolf()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_fox')){ //�ɸ�
	foreach($poison_target_list as $uname){
	  if(! $USERS->ByRealUname($uname)->IsFox()){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      elseif($vote_target->IsRole('poison_chiroptera')){ //������
	foreach($poison_target_list as $uname){
	  if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox', 'chiroptera')){
	    $limited_list[] = $uname;
	  }
	}
	$poison_target_list = $limited_list;
      }
      if(count($poison_target_list) < 1) break;

      //PrintData($poison_target_list, 'Poison Target'); //�ƥ�����
      $poison_target = $USERS->ByRealUname(GetRandom($poison_target_list)); //�оݼԤ����

      if($poison_target->IsActiveRole('resist_wolf')){ //����ϵ�ˤ�̵��
	$poison_target->AddRole('lost_ability');
	break;
      }

      $USERS->Kill($poison_target->user_no, 'POISON_DEAD_day'); //��˴����
    }while(false);

    //��ǽ�ԷϤν���
    $sentence_header = $USERS->GetHandleName($vote_target->uname, true) . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽȽ��
    if($vote_target->IsRole('boss_wolf', 'possessed_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('cursed_fox', 'white_fox', 'black_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsWolf()){
      $necromancer_result = 'wolf';
    }
    else{
      $necromancer_result = 'human';
    }

    //�м֤�˸��Ƚ��
    $flag_stolen = false;
    foreach($voter_list as $this_uname){
      $flag_stolen |= $USERS->ByRealUname($this_uname)->IsRole('corpse_courier_mad');
    }

    if($USERS->IsAppear('necromancer')){ //��ǽ�Ԥν���
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $necromancer_result);
      InsertSystemMessage($sentence, $action);
    }

    if($USERS->IsAppear('soul_necromancer')){ //�������ν���
      $sentence = $sentence_header . ($flag_stolen ? 'stolen' : $vote_target->main_role);
      InsertSystemMessage($sentence, 'SOUL_' . $action);
    }

    if($USERS->IsAppear('dummy_necromancer')){ //̴��ͤϡ�¼�͡עΡֿ�ϵ��ȿž
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence_header . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  foreach($user_list as $uname){ //��ɱ�ν���
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

  //�ü쥵���򿦤����������
  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  //PrintData($vote_target_list); //�ǥХå���
  $voted_target_member_list = array_count_values($vote_target_list);
  foreach($live_uname_list as $this_uname){
    $user = $USERS->ByUname($this_uname);
    $reason = '';

    if($user->IsRole('chicken')){ //�����Ԥ���ɼ����Ƥ����饷��å���
      if($voted_target_member_list[$this_uname] > 0) $reason = 'CHICKEN';
    }
    if($user->IsRole('rabbit')){ //����������ɼ����Ƥ��ʤ��ä��饷��å���
      if($voted_target_member_list[$this_uname] == 0) $reason = 'RABBIT';
    }
    elseif($user->IsRole('perverseness')){
      //ŷ�ٵ��ϼ�ʬ����ɼ���ʣ���οͤ���ɼ���Ƥ����饷��å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $reason = 'PERVERSENESS';
    }
    elseif($user->IsRole('flattery')){
      //���ޤ���ϼ�ʬ����ɼ���¾�οͤ���ɼ���Ƥ��ʤ���Х���å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $reason = 'FLATTERY';
    }
    elseif($user->IsRole('impatience')){ //û���Ϻ���ɼ�ʤ饷��å���
      if($vote_kill_uname == '') $reason = 'IMPATIENCE';
    }
    elseif($user->IsRole('celibacy')){ //�ȿȵ�²�����ͤ���ɼ���줿�饷��å���
      $celibacy_voted_list = array_keys($vote_target_list, $user->uname); //��ʬ�ؤ���ɼ�ԥꥹ��
      foreach($celibacy_voted_list as $this_voted_uname){
	if($USERS->ByUname($this_voted_uname)->IsLovers()){
	  $reason = 'CELIBACY';
	  break;
	}
      }
    }
    elseif($user->IsRole('panelist')){ //�����ԤϽ���Ԥ���ɼ�����饷��å���
      if($vote_target_list[$this_uname] == 'dummy_boy') $reason = 'PANELIST';
    }

    if($reason != '') $USERS->SuddenDeath($user->user_no, 'SUDDEN_DEATH_' . $reason);
  }
  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����

  if($ROOM->test_mode) return $vote_message_list;

  if($vote_kill_uname != ''){ //����ڤ��ؤ�
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = {$ROOM->id}"); //��ˤ���
    InsertSystemTalk('NIGHT', ++$ROOM->system_time, 'night system'); //�뤬��������
    if(! CheckVictory()) InsertRandomMessage(); //�������å�����
  }
  else{ //����ɼ����
    $next_vote_times = $RQ_ARGS->vote_times + 1; //��ɼ��������䤹
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = {$ROOM->id}
			AND date = {$ROOM->date} AND type = 'VOTE_TIMES'");

    //�����ƥ��å�����
    InsertSystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    InsertSystemTalk("����ɼ�ˤʤ�ޤ���( {$RQ_ARGS->vote_times} ����)", ++$ROOM->system_time);
    CheckVictory(true); //����Ƚ��
  }
  UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  mysql_query('COMMIT'); //������ߥå�
}

//��ν��׽���
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  $ROOM->LoadVote(); //��ɼ��������
  //PrintData($ROOM->vote, 'Vote Row');

  $vote_data = array(); //���ޥ�����ʬ��
  foreach($ROOM->vote as $uname => $list){
    extract($list);
    $vote_data[$situation][$uname] = $target_uname;
  }
  //PrintData($vote_data, 'Vote Data');

  foreach($USERS->rows as $user){ //̤��ɼ�����å�
    //PrintData($user->uname);
    if($user->IsDummyBoy() || $user->IsDead()) continue;
    if($user->IsWolf()){
      if(is_null($vote_data['WOLF_EAT'])) return false;
    }
    elseif($user->IsRoleGroup('mage')){
      if(is_null($vote_data['MAGE_DO'][$user->uname])) return false;
      //PrintData($vote_data['MAGE_DO'][$user->uname], $user->uname);
    }
    elseif($user->IsRole('voodoo_killer')){
      if(is_null($vote_data['VOODOO_KILLER_DO'][$user->uname])) return false;
    }
    elseif($user->IsRole('jammer_mad')){
      if(is_null($vote_data['JAMMER_MAD_DO'][$user->uname])) return false;
    }
    elseif($user->IsRole('voodoo_mad')){
      if(is_null($vote_data['VOODOO_MAD_DO'][$user->uname])) return false;
    }
    elseif($user->IsRole('voodoo_fox')){
      if(is_null($vote_data['VOODOO_FOX_DO'][$user->uname])) return false;
    }
    elseif($user->IsRole('child_fox')){
      if(is_null($vote_data['CHILD_FOX_DO'][$user->uname])) return false;
    }
    elseif($ROOM->date == 1){
      if($user->IsRole('mind_scanner')){
	if(is_null($vote_data['MIND_SCANNER_DO'][$user->uname])) return false;
      }
      elseif($user->IsRoleGroup('cupid')){
	if(is_null($vote_data['CUPID_DO'][$user->uname])) return false;
      }
      elseif($user->IsRoleGroup('mania')){
	if(is_null($vote_data['MANIA_DO'][$user->uname])) return false;
      }
      elseif(! $ROOM->IsOpenCast() && $user->IsRole('evoke_scanner')){
	if(is_null($vote_data['MIND_SCANNER_DO'][$user->uname])) return false;
      }
    }
    else{
      if($user->IsRole('dream_eater_mad')){
	if(is_null($vote_data['DREAM_EAT'][$user->uname])) return false;
      }
      elseif($user->IsActiveRole('trap_mad')){
	if(is_null($vote_data['TRAP_MAD_DO'][$user->uname]) &&
	   ! (is_array($vote_data['TRAP_MAD_NOT_DO']) &&
	      array_key_exists($user->uname, $vote_data['TRAP_MAD_NOT_DO']))) return false;
      }
      elseif($user->IsRoleGroup('guard')){
	if(is_null($vote_data['GUARD_DO'][$user->uname])) return false;
      }
      elseif($user->IsRole('anti_voodoo')){
	if(is_null($vote_data['ANTI_VOODOO_DO'][$user->uname])) return false;
      }
      elseif($user->IsRole('reporter')){
	if(is_null($vote_data['REPORTER_DO'][$user->uname])) return false;
      }
      elseif($user->IsRole('assassin')){
	if(is_null($vote_data['ASSASSIN_DO'][$user->uname]) &&
	   ! (is_array($vote_data['ASSASSIN_NOT_DO']) &&
	      array_key_exists($user->uname, $vote_data['ASSASSIN_NOT_DO']))) return false;
      }
      elseif(! $ROOM->IsOpenCast()){
	if($user->IsRoleGroup('cat') || $user->IsActiveRole('revive_fox')){
	  if(is_null($vote_data['POISON_CAT_DO'][$user->uname]) &&
	     ! (is_array($vote_data['POISON_CAT_NOT_DO']) &&
		array_key_exists($user->uname, $vote_data['POISON_CAT_NOT_DO']))) return false;
	}
      }
    }
  }

  //�����оݥ��ޥ�ɥ����å�
  $action_list = array('WOLF_EAT', 'MAGE_DO', 'VOODOO_KILLER_DO', 'JAMMER_MAD_DO',
		       'VOODOO_MAD_DO', 'VOODOO_FOX_DO', 'CHILD_FOX_DO');
  if($ROOM->date == 1){
    array_push($action_list , 'MIND_SCANNER_DO', 'MANIA_DO');
  }
  else{
    array_push($action_list , 'DREAM_EAT', 'TRAP_MAD_DO', 'GUARD_DO', 'ANTI_VOODOO_DO',
	       'REPORTER_DO', 'POISON_CAT_DO', 'ASSASSIN_DO');
  }
  foreach($action_list as $action){
    if(is_null($vote_data[$action])) $vote_data[$action] = array();
  }
  //PrintData($vote_data);

  //-- ǽ��Ƚ��δ��ܥ롼�� --//
  /*
    + �쥤�䡼 (����) �̤ν������
      - ���� �� �ܿ� �� ̴ �� �ꤤ �� <���ˤ��̽���> �� ��� �� ���ɤ� �� �ʺ�
        <[����] ���ԡ� �� ���� / [�����ܰʹ�] ���� �� ����>

    + ���� (���塼�ԥåɷ�)
      - ��ߺ��ѤϤʤ��Τ���ɼľ��˽�����Ԥ�

    + �ܿ� (��ϵ����͡��Ż��ԡ�櫻�)
      - � > ��͸�� > ��ϵ���� �� ��ͤμ�� �� �Ż�

    + ̴ (̴��͡���)
      - ̴��͸�� > �ӽ��� �� ̴��ͤμ��

    + �ꤤ (�ꤤ�ϡ������̴��͡����ơ����ѷ�)
      �� ��ʧ�� > ���� > �ꤤ˸�� > �ꤤ (����)

    ��ϵ���ꤤ�ա��֥󲰤ʤɡ���ư��̤ǻ�Ԥ��Ф륿���פ�Ƚ�������
    ��1) �ɤ����Ƚ�����˹Ԥ������ŸѤ����ब��ޤ� (����Ū�ˤϿ�ϵ�ν����ͥ�褹��)
    ��ϵ �� �ꤤ�� �� �Ÿ�

    ��2) �ɤ����Ƚ�����˹Ԥ����ǥ֥󲰤����ब��ޤ� (���ߤ��ꤤ�դ���)
    �ꤤ�� �� �Ÿ� �� �֥�
  */

  //-- �ѿ��ν���� --//
  $guarded_uname = ''; //������������ͤΥ桼��̾ //ʣ�����ߤ��б�����ʤ餳����������Ѥ���
  $trap_target_list         = array(); //櫤�������ꥹ��
  $trapped_list             = array(); //櫤ˤ����ä��ͥꥹ��
  $guard_target_list        = array(); //��ͷϤθ���оݥꥹ��
  $dummy_guard_target_list  = array(); //̴��ͤθ���оݥꥹ��

  $anti_voodoo_target_list  = array(); //����θ���оݥꥹ��
  $anti_voodoo_success_list = array(); //��ʧ�������ԥꥹ��

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

      $user->AddRole('lost_ability'); //�������֤�����ǽ�ϼ���

      //��ϵ�������Ƥ����鼫ʬ���Ȥؤ����ְʳ���̵��
      if($user != $wolf_target || $user == $target){
	$trap_target_list[$user->uname] = $target->uname; //櫤򥻥å�
      }
    }

    //櫻դ���ʬ���Ȱʳ���櫤�ųݤ�����硢�������櫤����ä����ϻ�˴
    $trap_count_list = array_count_values($trap_target_list);
    foreach($trap_target_list as $uname => $target_uname){
      if($uname != $target_uname && $trap_count_list[$target_uname] > 1){
	$trapped_list[] = $uname;
      }
    }

    foreach($vote_data['GUARD_DO'] as $uname => $target_uname){ //��ͷϤξ������
      $user = $USERS->ByUname($uname);
      if($user->IsRole('dummy_guard')){ //̴���
	$dummy_guard_target_list[$user->uname] = $target_uname; //�����򥻥å�
	continue;
      }

      if(in_array($target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
      }
      $guard_target_list[$user->uname] = $target_uname; //�����򥻥å�
    }
    //PrintData($guard_target_list, 'Target [guard]');
    //PrintData($dummy_guard_target_list, 'Target [dummy_guard]');
  }

  do{ //��ϵ�ν�������Ƚ��
    if($ROOM->IsQuiz()) break; //������¼����

    if(in_array($wolf_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
      $trapped_list[] = $voted_wolf->uname;
      break;
    }

    //��ͷϤθ��Ƚ��
    $guard_list = array_keys($guard_target_list, $wolf_target->uname); //��ҼԤ򸡽�
    //PrintData($guard_list, 'Guard List');
    if(count($guard_list) > 0){
      foreach($guard_list as $uname){
	$user = $USERS->ByUname($uname);

	//���ΤǤʤ���硢�������򿦤ϸ�Ҥ��Ƥ��Ƥ��ϵ�˽��⤵���
	if($user->IsRole('poison_guard') ||
	   ! $wolf_target->IsRole('priest', 'reporter', 'assassin')){
	  $guarded_uname = $wolf_target->uname;
	}

	//���������å����������
	$sentence = $user->handle_name . "\t" . $USERS->GetHandleName($wolf_target->uname, true);
	InsertSystemMessage($sentence, 'GUARD_SUCCESS');
      }
      if($guarded_uname != '') break;
    }

    //�����褬��ϵ�ξ��ϼ��Ԥ��� (��ϵ���и����Ƥ�����Τߵ�����)
    if($wolf_target->IsWolf()) break;

    //�����褬�ŸѤξ��ϼ��Ԥ���
    if($wolf_target->IsFox() && ! $wolf_target->IsRole('poison_fox', 'white_fox', 'child_fox')){
      InsertSystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    //�������
    if($voted_wolf->IsRole('possessed_wolf') && ! $wolf_target->IsDummyBoy() &&
       ! $wolf_target->IsFox() && ! $wolf_target->IsRole('revive_priest')){ //��ϵ�ν���
      $possessed_target_list[$voted_wolf->uname] = $wolf_target->uname;
      $wolf_target->dead_flag = true;
      if($wolf_target->IsRole('anti_voodoo')){ //�����褬����ʤ���ͥꥻ�å�
	$voted_wolf->possessed_reset = true;
      }
    }
    else{
      $USERS->Kill($wolf_target->user_no, 'WOLF_KILLED'); //�̾�ϵ�ν������
    }

    if($voted_wolf->IsActiveRole('tongue_wolf')){ //���ϵ�ν���
      if($wolf_target->IsRole('human')) $voted_wolf->AddRole('lost_ability'); //¼�ͤʤ�ǽ�ϼ���

      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      InsertSystemMessage($sentence . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');
    }

    if($wolf_target->IsPoison()){ //�ǻ�Ƚ�����
      //����Ԥ�����ϵ��������Ը�������ʤ��оݸ���
      if($voted_wolf->IsRole('resist_wolf') || $GAME_CONF->poison_only_eater){
	$poison_target = $voted_wolf;
      }
      else{ //�����Ƥ���ϵ�������������
	$poison_target = $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));
      }

      if($poison_target->IsActiveRole('resist_wolf')){ //����ϵ�ʤ�̵��
	$poison_target->AddRole('lost_ability');
      }
      else{
	$USERS->Kill($poison_target->user_no, 'POISON_DEAD_night'); //�ǻ����
      }
    }
  }while(false);
  //PrintData($possessed_target_list, 'Possessed Target [possessed_wolf]');

  if($ROOM->date > 1){
    //��ͷϤμ���оݥꥹ��
    $hunt_target_list = array('jammer_mad', 'voodoo_mad', 'corpse_courier_mad', 'dream_eater_mad',
			      'trap_mad', 'cursed_fox', 'voodoo_fox', 'revive_fox',
			      'poison_chiroptera', 'cursed_chiroptera');
    foreach($guard_target_list as $uname => $target_uname){ //��ͷϤμ��Ƚ��
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      /*
	���λ����ǻ��Ǥ���ΤϿ�ϵ�ν����衢��ϵ���ǻࡢ�(ͽ��)�Τߡ�
	����о� + ���̵���Ȥʤ��򿦤�¸�ߤ��ʤ��Τǡ�
	��ͷϤθ���褬���ʳ��ǻ�̤��ȤϤ��ꤨ�ʤ���
	���ǽ�ϼԤ��оݤ˴ޤޤ�Ƥ��ʤ��Τǡ����ۥ桼�������ɬ�פϤʤ���
      */
      $target = $USERS->ByUname($target_uname);
      if($target->IsRole($hunt_target_list)){
	$USERS->Kill($target->user_no, 'HUNTED');
	$sentence = $user->handle_name . "\t" . $target->handle_name;
	InsertSystemMessage($sentence, 'GUARD_HUNTED');
      }
    }

    $assassin_target_list = array(); //�Ż��оݼԥꥹ��
    foreach($vote_data['ASSASSIN_DO'] as $uname => $target_uname){ //�Ż��Ԥξ������
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      if(in_array($target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_list[] = $user->uname;
	continue;
      }

      $assassin_target_list[$target_uname] = true; //�Ż��оݼԥꥹ�Ȥ��ɲ�
    }

    foreach($trapped_list as $uname){ //櫤λ�˴����
      $USERS->Kill($USERS->UnameToNumber($uname), 'TRAPPED');
    }

    foreach($assassin_target_list as $uname => $flag){ //�Ż�����
      $USERS->Kill($USERS->UnameToNumber($uname), 'ASSASSIN_KILLED');
    }

    //-- ̴�ϥ쥤�䡼 --//
    foreach($vote_data['DREAM_EAT'] as $uname => $target_uname){ //�Ӥν���
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname); //�оݼԤξ�������
      $sentence = "\t" . $user->handle_name;

      if($target->IsRole('dummy_guard') && $target->IsLive(true)){ //�оݤ�̴��ͤʤ��֤�Ƥ���˹礦
	$USERS->Kill($user->user_no, 'HUNTED');
	InsertSystemMessage($target->handle_name . $sentence, 'GUARD_HUNTED');
	continue;
      }

      if(in_array($target->uname, $dummy_guard_target_list)){ //̴��ͤθ��Ƚ��
	$hunted_flag = false;
	$guard_list = array_keys($dummy_guard_target_list, $target->uname); //��ҼԤ򸡽�
	foreach($guard_list as $uname){
	  $guard_user = $USERS->ByUname($uname);
	  if($guard_user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��
	  $hunted_flag = true;
	  InsertSystemMessage($guard_user->handle_name . $sentence, 'GUARD_HUNTED');
	}

	if($hunted_flag){
	  $USERS->Kill($user->user_no, 'HUNTED');
	  continue;
	}
      }

      //̴��ǽ�ϼԤʤ鿩������
      if($target->IsRoleGroup('dummy')) $USERS->Kill($target->user_no, 'DREAM_KILLED');
    }

    $hunted_list = array(); //��������ԥꥹ��
    foreach($dummy_guard_target_list as $uname => $target_uname){ //̴��ͤμ��Ƚ��
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

      $target = $USERS->ByUname($target_uname);
      if($target->IsRole('dream_eater_mad') && $target->IsLive(true)){ //�Ӥμ��Ƚ��
	$hunted_list[$user->handle_name] = $target;
      }

      //������������å������������Ф�
      $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
      InsertSystemMessage($sentence, 'GUARD_SUCCESS');
    }

    foreach($hunted_list as $handle_name => $target){ //�Ӽ�����
      $USERS->Kill($target->user_no, 'HUNTED');
      $sentence = $handle_name . "\t" . $target->handle_uname; //���ǽ�ϼԤ��оݳ�
      InsertSystemMessage($sentence, 'GUARD_HUNTED');
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
      elseif($target->IsRole('possessed_wolf') &&
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

    //����Ƚ�� (������ǽ�ϼԤ���ϵ)
    if($target->IsRoleGroup('cursed', 'possessed_wolf') && $target->IsLive(true)){
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
  foreach($vote_data['JAMMER_MAD_DO'] as $uname => $target_uname){ //���Ƥν���
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
      $jammer_target_list[$user->uname] = $target->uname;
    }
  }
  //PrintData($jammer_target_list, 'Target [jammer_mad]');

  //��������Τα��Ĥ�Ƚ���о��򿦥ꥹ��
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  $mage_list = array_merge($vote_data['MAGE_DO'], $vote_data['CHILD_FOX_DO']);
  foreach($mage_list as $uname => $target_uname){ //�ꤤ�շϤν���
    $user = $USERS->ByUname($uname);
    if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

    $target = $USERS->ByRealUname($target_uname); //�оݼԤξ�������
    if($user->IsRole('dummy_mage')){ //̴���ͤ�Ƚ�� (¼�ͤȿ�ϵ��ȿž������)
      $result = $target->DistinguishMage(true);
    }
    elseif(in_array($user->uname, $jammer_target_list)){ //���Ƥ�˸��Ƚ��
      $result = $user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed';
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
      $result = $target->IsRoleGroup('chiroptera') ? 'chiroptera' : 'sex_' . $target->sex;
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

      if($user->IsRole('child_fox')){ //�ҸѤ�Ƚ�� (�����Ψ�Ǽ��Ԥ���)
	$result = mt_rand(1, 100) > 30 ? $target->DistinguishMage() : 'failed';
      }
      else{
	if(array_key_exists($target->uname, $possessed_target_list)){ //��ͥ���󥻥�Ƚ��
	  $target->possessed_cancel = true;
	}

	if($user->IsRole('soul_mage')){ //�����ꤤ�դ�Ƚ�� (�ᥤ����)
	  $result = $target->main_role;
	}
	else{ //�ꤤ�դν���
	  if($target->IsLive(true) && $target->IsFox() &&
	     ! $target->IsRole('white_fox', 'black_fox', 'child_fox')){ //����Ƚ��
	    $USERS->Kill($target->user_no, 'FOX_DEAD');
	  }
	  $result = $target->DistinguishMage(); //Ƚ���̤����
	}
      }
    }
    $sentence = $user->handle_name . "\t" . $USERS->GetHandleName($target->uname, true);
    $action = $user->IsRole('child_fox') ? 'CHILD_FOX_RESULT' : 'MAGE_RESULT';
    InsertSystemMessage($sentence . "\t" . $result, $action);
  }

  //PrintData($voodoo_killer_success_list, 'SUCCESS [voodoo_killer]');
  foreach($voodoo_killer_success_list as $target_uname => $flag){ //���ۻդβ����̽���
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'VOODOO_KILLER_SUCCESS';

    $voodoo_killer_list = array_keys($voodoo_killer_target_list, $target_uname); //�����Ԥ򸡽�
    foreach($voodoo_killer_list as $uname){
      InsertSystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

  //PrintData($anti_voodoo_success_list, 'SUCCESS [anti_voodoo]');
  foreach($anti_voodoo_success_list as $target_uname => $flag){ //�������ʧ����̽���
    $sentence = "\t" . $USERS->GetHandleName($target_uname, true);
    $action = 'ANTI_VOODOO_SUCCESS';

    $anti_voodoo_list = array_keys($anti_voodoo_target_list, $target_uname); //�����Ԥ򸡽�
    foreach($anti_voodoo_list as $uname){
      InsertSystemMessage($USERS->GetHandleName($uname) . $sentence, $action);
    }
  }

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
      else{ //���åޥ˥�
	//���ԡ����� (���åޥ˥��Ϥ���ꤷ������¼��)
	$result = $target->IsRoleGroup('mania') ? 'human' : $target->main_role;
	$user->ReplaceRole('mania', $result);
	$user->AddRole('copied');

	$sentence = $user->handle_name . "\t" . $target->handle_name . "\t" . $result;
	InsertSystemMessage($sentence, 'MANIA_RESULT');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($USERS->rows as $user){ //ŷ�ͤε��Խ���
	if($user->IsLive(true) && $user->IsRole('revive_priest') &&
	   ! $user->IsDummyBoy() && ! $user->IsLovers()){
	  $USERS->Kill($user->user_no, 'PRIEST_RETURNED');
	}
      }
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

      if($target == $wolf_target){ //��������
	if($target->uname == $guarded_uname) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
	$sentence = $user->handle_name . "\t" .
	  $USERS->GetHandleName($wolf_target->uname, true) . "\t" .
	  $USERS->GetHandleName($voted_wolf->uname, true);
	InsertSystemMessage($sentence, 'REPORTER_SUCCESS');
      }
      elseif($target->IsRoleGroup('wolf', 'fox') && $target->IsLive(true)){
	//�����оݤ���ϵ���ŸѤʤ黦�����
	$USERS->Kill($user->user_no, 'REPORTER_DUTY');
      }
    }

    //-- �����ϥ쥤�䡼 --//
    if(! $ROOM->IsOpenCast()){
      foreach($vote_data['POISON_CAT_DO'] as $uname => $target_uname){ //����ǽ�ϼԤν���
	$user = $USERS->ByUname($uname);
	if($user->IsDead(true)) continue; //ľ���˻��Ǥ�����̵��

	$target = $USERS->ByUname($target_uname); //�оݼԤξ�������

	//����Ƚ��
	if($user->IsRole('poison_cat')){
	  $revive_rate = 25;
	}
	elseif($user->IsRole('revive_cat')){
	  $revive_times = (int)$user->partner_list['revive_cat'][0];
	  $revive_rate = ceil(80 / pow(4, $revive_times));
	}
	elseif($user->IsRole('revive_fox')){
	  $revive_rate = 100;
	}
	$rate = mt_rand(1, 100); //����Ƚ�������
	//$rate = 5; //mt_rand(1, 10); //�ƥ�����
	//PrintData($revive_rate, 'Revive Info: ' . $user->uname . ' => ' . $target->uname);
	//PrintData($rate, 'Revive Rate');

	$result = 'failed';
	do{
	  if($rate > $revive_rate) break; //��������
	  if($rate <= floor($revive_rate / 5)){ //��������
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
	    //if($ROOM->test_mode) PrintData($revive_target_list, 'Revive Target');
	    if(count($revive_target_list) > 0){ //���䤬��������������ؤ���
	      $target = $USERS->ByUname(GetRandom($revive_target_list));
	    }
	  }
	  //$target = $USERS->ByID(8); //�ƥ�����
	  //PrintData($target->uname, 'Revive User');
	  if($target->IsRoleGroup('cat', 'revive') || $target->IsLovers()){
	    break; //����ǽ�ϼԤ����ͤʤ���������
	  }

	  $result = 'success';
	  if($target->IsRole('possessed_wolf')){ //��ϵ�б�
	    if($target->revive_flag) break; //�����Ѥߤʤ饹���å�

	    $virtual_target = $USERS->ByVirtual($target->user_no);
	    if($target->IsDead()){ //������
	      if($target != $virtual_target){ //��͸�˻�˴���Ƥ������ϥꥻ�åȽ�����Ԥ�
		$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      }
	    }
	    elseif($target->IsLive(true)){ //��¸�� (��;��ֳ���)
	      //�����������������
	      $target->ReturnPossessed('possessed_target', $ROOM->date + 1);
	      InsertSystemMessage($target->handle_name, 'REVIVE_SUCCESS');

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
	  elseif($user->IsRole('revive_fox')){ //��Ѥ�ǽ�ϼ�������
	    $user->AddRole('lost_ability');
	  }
	}
	else{
	  InsertSystemMessage($target->handle_name, 'REVIVE_FAILED');
	}
	$sentence = $user->handle_name . "\t";
	$sentence .= $USERS->GetHandleName($target->uname) . "\t" . $result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  //-- ��ͽ��� --//
  //PrintData($possessed_target_list, 'Possessed Target');
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
	InsertSystemMessage($virtual_user->handle_name, 'POSSESSED_RESET');

	//�����������������
	$user->ReturnPossessed('possessed_target', $possessed_date);
	$user->SaveLastWords($virtual_user->handle_name);
	InsertSystemMessage($user->handle_name, 'REVIVE_SUCCESS');
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
      $target->dead_flag = false; //��˴�ե饰��ꥻ�å�
      $USERS->Kill($target->user_no, 'POSSESSED_TARGETED'); //�����λ�˴����
      $target->AddRole("possessed[{$possessed_date}-{$user->user_no}]");

      //��ͽ���
      $user->AddRole("possessed_target[{$possessed_date}-{$target->user_no}]");
      InsertSystemMessage($virtual_user->handle_name, 'POSSESSED');
      $user->SaveLastWords($virtual_user->handle_name);
      $user->Update('last_words', '');
    }

    if($user != $virtual_user){
      $virtual_user->ReturnPossessed('possessed', $possessed_date);
      if($user->IsLive(true)) $virtual_user->SaveLastWords();
    }
  }

  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����

  //-- �ʺ׷ϥ쥤�䡼 --//
  $priest_flag = false;
  $crisis_priest_flag = false;
  $revive_priest_list = array();
  $live_count = array();
  foreach($USERS->rows as $user){ //�ʺ׷Ϥξ������
    if(! $user->IsDummyBoy()){
      $priest_flag        |= $user->IsRole('priest');
      $crisis_priest_flag |= $user->IsRole('crisis_priest');
      if($user->IsActiveRole('revive_priest')) $revive_priest_list[] = $user->uname;
    }
    if($user->IsDead(true)) continue;

    $live_count['total']++;
    if($user->IsWolf()) $live_count['wolf']++;
    elseif($user->IsFox()) $live_count['fox']++;
    else{
      $live_count['human']++;
      if($user->DistinguishCamp() == 'human') $live_count['human_side']++;
    }
    if($user->IsLovers()) $live_count['lovers']++;
  }
  //PrintData($live_count, 'Live Count');

  if($priest_flag && $ROOM->date > 2 && ($ROOM->date % 2) == 1){ //�ʺפν���
    InsertSystemMessage($live_count['human_side'], 'PRIEST_RESULT');
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
      InsertSystemMessage($crisis_priest_result, 'CRISIS_PRIEST_RESULT');
    }

    //ŷ�ͤ�����Ƚ�����
    if(! $ROOM->IsOpenCast() && count($revive_priest_list) > 0 &&
       ($ROOM->date == 4 || $crisis_priest_result != '' || $live_count['wolf'] == 1 ||
	count($USERS->rows) >= $live_count['total'] * 2)){
      foreach($revive_priest_list as $uname){
	$user = $USERS->ByUname($uname);
	if($user->IsLovers() || ($ROOM->date >= 4 && $user->IsLive(true))){
	  $user->AddRole('lost_ability');
	}
	elseif($user->IsDead(true)){
	  $user->Revive();
	  $user->AddRole('lost_ability');
	}
      }
    }
  }

  if($ROOM->test_mode) return;

  //�������ˤ���
  $next_date = $ROOM->date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = {$ROOM->id}");

  //�������ν跺��ɼ�Υ�����Ȥ� 1 �˽����(����ɼ��������)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //�뤬����������
  InsertSystemTalk("MORNING\t" . $next_date, ++$ROOM->system_time, 'day system', $next_date);
  UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  //DeleteVote(); //���ޤǤ���ɼ���������

  CheckVictory(); //���ԤΥ����å�
  mysql_query('COMMIT'); //������ߥå�
}

//�򿦤ν�°���롼�פ�Ƚ�̤���
function DistinguishRoleGroup($role){
  global $GAME_CONF;

  foreach($GAME_CONF->main_role_group_list as $key => $value){
    if(strpos($role, $key) !== false) return $value;
  }
  return 'human';
}

//��ɼ���ޥ�ɤ����äƤ��뤫�����å�
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return true;
  }
  if($RQ_ARGS->situation == $applay_situation) return true;

  OutputVoteResult('̵������ɼ�Ǥ�');
}

//�������å���������������
function InsertRandomMessage(){
  global $MESSAGE, $GAME_CONF, $ROOM;

  if(! $GAME_CONF->random_message) return;
  $sentence = GetRandom($MESSAGE->random_message_list);
  InsertSystemTalk($sentence, ++$ROOM->system_time, 'night system');
}
