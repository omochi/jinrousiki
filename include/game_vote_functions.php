<?php
require_once(dirname(__FILE__) . '/game_functions.php');

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
  global $GAME_CONF, $ROOM;

  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  $role_list = $GAME_CONF->role_list[$user_count]; //�Ϳ��˱���������ꥹ�Ȥ����
  if($role_list == NULL){ //�ꥹ�Ȥ�̵ͭ������å�
    $sentence = $user_count . '�ͤ����ꤵ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  if($ROOM->is_quiz()){ //������¼
    $temp_role_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false)
	$temp_role_list['wolf'] += (int)$value;
      elseif(strpos($key, 'mad') !== false)
	$temp_role_list['mad'] += (int)$value;
      elseif(strpos($key, 'common') !== false)
	$temp_role_list['common'] += (int)$value;
      elseif(strpos($key, 'fox') !== false)
	$temp_role_list['fox'] += (int)$value;
      else
	$temp_role_list['human'] += (int)$value;
    }
    $temp_role_list['human']--;
    $temp_role_list['quiz'] = 1;
    $role_list = $temp_role_list;
  }
  elseif(strpos($option_role, 'duel') !== false){ //��Ʈ¼
    $role_list = array(); //�����ꥻ�å�
    $role_list['wolf'] = round($user_count / 5);
    $role_list['trap_mad'] = round(($user_count - $role_list['wolf']) / 3);
    $role_list['assassin'] = $user_count - ($role_list['wolf'] + $role_list['trap_mad']);
  }
  elseif($ROOM->is_option('chaosfull')){ //��������
    $role_list = array(); //�����ꥻ�å�
    $role_list['wolf'] = 1; //ϵ1����
    $role_list['mage'] = 1; //�ꤤ��1����
    $start_count = 2;

    //����¿�ϵ��
    $wolf_count = ceil($user_count / 10) - 1;
    if($wolf_count > 0) $start_count += $wolf_count;
    for($i = 0; $i < $wolf_count; $i++){
      $rand = mt_rand(1, 100);
      if($rand < 3)      $role_list['boss_wolf']++;
      elseif($rand <  4) $role_list['cursed_wolf']++;
      elseif($rand < 14) $role_list['cute_wolf']++;
      elseif($rand < 17) $role_list['poison_wolf']++;
      elseif($rand < 21) $role_list['resist_wolf']++;
      elseif($rand < 24) $role_list['tongue_wolf']++;
      else               $role_list['wolf']++;
    }

    //������Ÿ���
    $fox_count = ceil($user_count / 15) - 1;
    if($fox_count > 0) $start_count += $fox_count;
    for($i = 0; $i < $fox_count; $i++){
      $rand = mt_rand(1, 100);
      if($rand < 1)       $role_list['cursed_fox']++;
      elseif($rand <  5)  $role_list['poison_fox']++;
      elseif($rand <  8)  $role_list['white_fox']++;
      elseif($rand < 15)  $role_list['child_fox']++;
      else                $role_list['fox']++;
    }

    for($i = $start_count; $i < $user_count; $i++){
      $rand = mt_rand(1, 1000);
      if($rand < 100)     $role_list['wolf']++;
      elseif($rand < 110) $role_list['boss_wolf']++;
      elseif($rand < 115) $role_list['cursed_wolf']++;
      elseif($rand < 145) $role_list['cute_wolf']++;
      elseif($rand < 160) $role_list['poison_wolf']++;
      elseif($rand < 170) $role_list['resist_wolf']++;
      elseif($rand < 200) $role_list['tongue_wolf']++;
      elseif($rand < 220) $role_list['fox']++;
      elseif($rand < 225) $role_list['cursed_fox']++;
      elseif($rand < 235) $role_list['poison_fox']++;
      elseif($rand < 240) $role_list['white_fox']++;
      elseif($rand < 250) $role_list['child_fox']++;
      elseif($rand < 280) $role_list['mage']++;
      elseif($rand < 290) $role_list['soul_mage']++;
      elseif($rand < 300) $role_list['psycho_mage']++;
      elseif($rand < 305) $role_list['sex_mage']++;
      elseif($rand < 320) $role_list['dummy_mage']++;
      elseif($rand < 360) $role_list['necromancer']++;
      elseif($rand < 370) $role_list['soul_necromancer']++;
      elseif($rand < 390) $role_list['dummy_necromancer']++;
      elseif($rand < 430) $role_list['medium']++;
      elseif($rand < 460) $role_list['mad']++;
      elseif($rand < 470) $role_list['fanatic_mad']++;
      elseif($rand < 480) $role_list['jammer_mad']++;
      elseif($rand < 500) $role_list['trap_mad']++;
      elseif($rand < 510) $role_list['whisper_mad']++;
      elseif($rand < 590) $role_list['common']++;
      elseif($rand < 600) $role_list['dummy_common']++;
      elseif($rand < 635) $role_list['guard']++;
      elseif($rand < 645) $role_list['poison_guard']++;
      elseif($rand < 655) $role_list['dummy_guard']++;
      elseif($rand < 680) $role_list['reporter']++;
      elseif($rand < 700) $role_list['poison']++;
      elseif($rand < 710) $role_list['strong_poison']++;
      elseif($rand < 720) $role_list['incubate_poison']++;
      elseif($rand < 730) $role_list['dummy_poison']++;
      elseif($rand < 740) $role_list['poison_cat']++;
      elseif($rand < 770) $role_list['pharmacist']++;
      elseif($rand < 800) $role_list['cupid']++;
      elseif($rand < 820) $role_list['mania']++;
      elseif($rand < 840) $role_list['assassin']++;
      elseif($rand < 860) $role_list['suspect']++;
      elseif($rand < 880) $role_list['unconscious']++;
      elseif($rand < 997) $role_list['human']++;
      else                $role_list['quiz']++;
    }

    //���������
    $wolf_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false) $wolf_count_list[$key] = $value;
    }
    $over_wolf_count = array_sum($wolf_count_list) - floor($user_count * 0.3);
    for(; $over_wolf_count > 0; $over_wolf_count--){
      arsort($wolf_count_list);
      $this_key = key($wolf_count_list);
      $wolf_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $fox_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'fox') !== false) $fox_count_list[$key] = $value;
    }
    $over_fox_count = array_sum($fox_count_list) - floor($user_count * 0.15);
    for(; $over_fox_count > 0; $over_fox_count--){
      arsort($fox_count_list);
      $this_key = key($fox_count_list);
      $fox_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $over_cupid_count = $role_list['cupid'] - floor($user_count * 0.15);
    if($over_cupid_count > 0){
      $role_list['cupid'] -= $over_cupid_count;
      $role_list['human'] += $over_cupid_count;;
    }

    $mage_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'mage') !== false) $mage_count_list[$key] = $value;
    }
    $over_mage_count = array_sum($mage_count_list) - floor($user_count * 0.25);
    for(; $over_mage_count > 0; $over_mage_count--){
      arsort($mage_count_list);
      $this_key = key($mage_count_list);
      $mage_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $guard_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'guard') !== false) $guard_count_list[$key] = $value;
    }
    $over_guard_count = array_sum($guard_count_list) - floor($user_count * 0.15);
    for(; $over_guard_count > 0; $over_guard_count--){
      arsort($guard_count_list);
      $this_key = key($guard_count_list);
      $guard_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $common_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'common') !== false) $common_count_list[$key] = $value;
    }
    $over_common_count = array_sum($common_count_list) - floor($user_count * 0.2);
    for(; $over_common_count > 0; $over_common_count--){
      arsort($common_count_list);
      $this_key = key($common_count_list);
      $common_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $reviver_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'poison_cat') !== false) $revivier_count_list[$key] = $value;
    }
    $over_reviver_count = array_sum($reviver_count_list) - round($user_count * 0.1);
    for(; $over_reviver_count > 0; $over_reviver_count--){
      arsort($reviver_count_list);
      $this_key = key($reviver_count_list);
      $reviver_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['poison']++;
    }

    $poison_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'poison') !== false) $poison_count_list[$key] = $value;
    }
    $over_poison_count = array_sum($poison_count_list) - floor($user_count * 0.2);
    for(; $over_poison_count > 0; $over_poison_count--){
      arsort($poison_count_list);
      $this_key = key($poison_count_list);
      $poison_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $mad_count_list = array();
    foreach($role_list as $key => $value){
      if($key == 'mad') $mad_count_list[$key] = $value;
    }
    $over_mad_count = array_sum($mad_count_list) - floor($user_count * 0.15);
    for(; $over_mad_count > 0; $over_mad_count--){
      arsort($mad_count_list);
      $this_key = key($mad_count_list);
      $mad_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $assassin_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'assassin') !== false) $assassin_count_list[$key] = $value;
    }
    $over_assassin_count = array_sum($assassin_count_list) - floor($user_count * 0.2);
    for(; $over_assassin_count > 0; $over_assassin_count--){
      arsort($assassin_count_list);
      $this_key = key($assassin_count_list);
      $assassin_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }
  }
  elseif($ROOM->is_option('chaos')){ //����
    //-- �ƿرĤοͿ������ (�Ϳ� = �ƿͿ��νи�Ψ) --//
    $role_list = array(); //�����ꥻ�å�

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
      if($user_count < 20){ //20��̤�������ϵ�и�
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
    if($cupid_count > 0 && $medium_count == 0) $medium_count++;

    //�������������
    if($medium_count > 0 && $human_count >= $medium_count){
      $human_count -= $medium_count; //¼�ͿرĤλĤ�Ϳ�
      $role_list['medium'] = $medium_count;
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
      $human_count -= $necromancer_count; //¼�ͿرĤλĤ�Ϳ�
      $role_list['necromancer'] = $necromancer_count;
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
    if($guard_count > 0 && $human_count >= $guard_count){
      $human_count -= $guard_count; //¼�ͿرĤλĤ�Ϳ�
      $special_guard_count = 0; //�ü��ͤοͿ�
      if($user_count < 16) $base_count = 0; //16��̤���ǤϽи����ʤ�
      else $base_count = ceil($user_count / 15); //�ü���Ƚ�����򻻽�
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

    //���դοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 95:5
      if($rand <= 95) $pharmacist_count = 0;
      else $pharmacist_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $pharmacist_count = 0;
      else $pharmacist_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 20);
      if($rand <= 10) $pharmacist_count = $base_count - 1;
      elseif($rand <= 90) $pharmacist_count = $base_count;
      else $pharmacist_count = $base_count + 1;
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
      if($rand <= 40) $mania_count = 0;
      else $mania_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 20);
      if($rand <= 10) $mania_count = $base_count - 1;
      elseif($rand <= 90) $mania_count = $base_count;
      else $mania_count = $base_count + 1;
    }

    //���åޥ˥�����������
    if($mania_count > 0 && $human_count >= $mania_count){
      $role_list['mania'] = $mania_count;
      $human_count -= $mania_count; //¼�ͿرĤλĤ�Ϳ�
    }

    //�Կ��ԷϤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 90:10
      if($rand <= 90) $strangers_count = 0;
      else $strangers_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 80:20
      if($rand <= 80) $strangers_count = 0;
      else $strangers_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 20);
      if($rand <= 10) $strangers_count = $base_count - 1;
      elseif($rand <= 90) $strangers_count = $base_count;
      else $strangers_count = $base_count + 1;
    }

    //�Կ��ԷϤ���������
    if($strangers_count > 0 && $human_count >= $strangers_count){
      if($user_count < 20){ //���͸���20��̤���ξ���̵�ռ���Ф䤹������
	for($i = 0; $i < $strangers_count; $i++){
	  $rand = mt_rand(1, 100);
	  if($rand <= 60) $role_list['unconscious']++;
	  else $role_list['suspect']++;
	}
      }
      else{ //20�Ͱʾ�ʤ����Կ��Ԥ�Ф䤹������
	for($i = 0; $i < $strangers_count; $i++){
	  $rand = mt_rand(1, 100);
	  if($rand <= 40) $role_list['unconscious']++;
	  else $role_list['suspect']++;
	}
      }
      $human_count -= $strangers_count; //¼�ͿرĤλĤ�Ϳ�
    }

    $role_list['human'] = $human_count; //¼�ͤοͿ�
  }
  else{ //�̾�¼
    //���Ǽ� (¼��2 �� ���Ǽ�1����ϵ1)
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
      $role_list['wolf']--;
      $role_list['boss_wolf']++;
    }

    //��ϵ (��ϵ �� ��ϵ��¼�� �� ����)
    if(strpos($option_role, 'poison_wolf') !== false && $user_count >= $GAME_CONF->poison_wolf){
      $role_list['wolf']--;
      $role_list['poison_wolf']++;
      $role_list['human']--;
      $role_list['pharmacist']++;
    }

    //���åޥ˥� (¼�� �� ���åޥ˥�)
    if(strpos($option_role, 'mania') !== false && $user_count >= $GAME_CONF->mania){
      $role_list['human']--;
      $role_list['mania']++;
    }

    //��� (¼�� �� ���1��������1)
    if(strpos($option_role, 'medium') !== false && $user_count >= $GAME_CONF->medium){
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
    // echo '���顼���������' . $role_count;
    // return $now_role_list;
    $sentence = '¼�� (' . $user_count . ') ������ο� (' . $role_count . ') �����פ��Ƥ��ޤ���';
    OutputVoteResult($error_header . $sentence . $error_footer, true, true);
  }

  return $now_role_list;
}

//�򿦤οͿ����Υꥹ�Ȥ��������
function MakeRoleNameList($role_count_list, $chaos = false){
  global $GAME_CONF;

  $sentence = ($chaos ? '�и��򿦡�' : '');
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0){
      $sentence .= '��' . $value;
      if(! $chaos) $sentence .= $count;
    }
  }
  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0){
      $sentence .= '��(' . $value;
      if(! $chaos) $sentence .= $count;
      $sentence .= ')';
    }
  }
  return $sentence;
}

//�����ɼ���׽���
function AggregateVoteDay(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS;

  $test_mode = isset($RQ_ARGS->TestItems);
  if(! $test_mode) CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  //�����Ƥ���桼���������
  $user_list = $USERS->GetLiveUsers();
  $user_count = count($user_list);

  if(! $test_mode){
    //��ɼ��������
    $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";
    $vote_count = FetchResult($query);
    if($vote_count != $user_count) return false; //��������ɼ���Ƥ��ʤ���н��������å�
  }

  $max_voted_number = 0;  //��¿��ɼ��
  $vote_kill_uname  = ''; //�跺�����ͤΥ桼��̾
  $live_uname_list   = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list  = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��桼��̾)
  $vote_count_list   = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list      = array(); //ǽ�ϼԤ�������ɼ���
  $dead_lovers_list  = array(); //���ɤ��������ͤΥꥹ��
  $query = "FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
    "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND"; //���̥�����

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  foreach($user_list as $this_uname){
    $this_user = $USERS->ByUname($this_uname);

    //��ʬ����ɼ�������
    $query_voted_number = "SELECT SUM(vote_number) $query target_uname = '{$this_user->uname}'";
    $this_voted_number = FetchResult($query_voted_number);

    //�ü쥵���򿦤���ɼ����
    if($this_user->is_role('upper_luck')) //����
      $this_voted_number += ($ROOM->date == 2 ? 4 : -2);
    elseif($this_user->is_role('downer_luck')) //��ȯ��
      $this_voted_number += ($ROOM->date == 2 ? -4 : 2);
    elseif($this_user->is_role('random_luck')) //��������
      $this_voted_number += (mt_rand(1, 5) - 3);
    elseif($this_user->is_role('star')) //�͵���
      $this_voted_number--;
    elseif($this_user->is_role('disfavor')) //�Կ͵�
      $this_voted_number++;
    if($this_voted_number < 0) $this_voted_number = 0; //�ޥ��ʥ��ˤʤäƤ����� 0 �ˤ���

    //��ʬ����ɼ��ξ�������
    $array = FetchNameArray("SELECT target_uname, vote_number $query uname = '{$this_user->uname}'");
    $this_target = $USERS->ByUname($array['target_uname']);
    $this_vote_number = (int)$array['vote_number'];

    //�����ƥ��å������Ѥ����������
    $this_message_list = array('target'       => $this_target->handle_name,
			       'voted_number' => $this_voted_number,
			       'vote_number'  => $this_vote_number);

    //�ꥹ�Ȥ˥ǡ������ɲ�
    array_push($live_uname_list, $this_user->uname);
    $vote_message_list[$this_user->uname] = $this_message_list;
    $vote_target_list[$this_user->uname]  = $this_target->uname;
    $vote_count_list[$this_user->uname]   = $this_voted_number;
    if($this_user->is_role('authority')){ //���ϼԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['authority'] = $this_target->uname;
      $ability_list['authority_uname'] = $this_user->uname;
    }
    elseif($this_user->is_role('rebel')){ //ȿ�ռԤʤ���ɼ��ȥ桼��̾��Ͽ
      $ability_list['rebel'] = $this_target->uname;
      $ability_list['rebel_uname'] = $this_user->uname;
    }
    elseif($this_user->is_role('decide')) //����Ԥʤ���ɼ���Ͽ
      $ability_list['decide'] = $this_target->uname;
    elseif($this_user->is_role('plague')) //���¿��ʤ���ɼ���Ͽ
      $ability_list['plague'] = $this_target->uname;
    elseif($this_user->is_role('impatience')) //û���ʤ���ɼ���Ͽ
      $ability_list['impatience'] = $this_target->uname;
    elseif($this_user->is_role('good_luck')) //�����ʤ�桼��̾��Ͽ
      $ability_list['good_luck'] = $this_user->uname;
    elseif($this_user->is_role('bad_luck')) //�Ա��ʤ�桼��̾��Ͽ
      $ability_list['bad_luck'] = $this_user->uname;
  }

  //ȿ�ռԤ�Ƚ��
  if($ability_list['rebel'] == $ability_list['authority']){
    //���ϼԤ�ȿ�ռԤ���ɼ���� 0 �ˤ���
    $vote_message_list[$ability_list['rebel_uname']]['vote_number'] = 0;
    $vote_message_list[$ability_list['authority_uname']]['vote_number'] = 0;

    //��ɼ���ɼ������
    $this_uname = $ability_list['rebel'];
    if($vote_message_list[$this_uname]['voted_number'] > 3)
      $vote_message_list[$this_uname]['voted_number'] -= 3;
    else
      $vote_message_list[$this_uname]['voted_number'] = 0;
    $vote_count_list[$this_uname] = $vote_message_list[$this_uname]['voted_number'];
  }

  //��ɼ��̤򥿥ֶ��ڤ���������ƥ����ƥ��å���������Ͽ
  // print_r($vote_message_list); //�ǥХå���
  foreach($live_uname_list as $this_uname){
    $this_array = $vote_message_list[$this_uname];
    $this_handle       = $USERS->GetHandleName($this_uname);
    $this_target       = $this_array['target'];
    $this_voted_number = $this_array['voted_number'];
    $this_vote_number  = $this_array['vote_number'];

    //������ɼ���򹹿�
    if($this_voted_number > $max_voted_number) $max_voted_number = $this_voted_number;

    //(ï�� [TAB] ï�� [TAB] ��ʬ����ɼ�� [TAB] ��ʬ����ɼ�� [TAB] ��ɼ���)
    $sentence = $this_handle . "\t" . $this_target . "\t" .
      (int)$this_voted_number ."\t" . (int)$this_vote_number . "\t" . $RQ_ARGS->vote_times;
    InsertSystemMessage($sentence, 'VOTE_KILL');
  }

  //������ɼ���Υ桼��̾(�跺�����) �Υꥹ�Ȥ����
  $max_voted_uname_list = array_keys($vote_count_list, $max_voted_number);
  do{
    if(count($max_voted_uname_list) == 1){ //��ͤ����ʤ�跺�Է���
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    if(in_array($ability_list['decide'], $max_voted_uname_list)){ //�����
      $vote_kill_uname = $ability_list['decide'];
      break;
    }

    if(in_array($ability_list['bad_luck'], $max_voted_uname_list)){ //�Թ�
      $vote_kill_uname = $ability_list['bad_luck'];
      break;
    }

    if(in_array($ability_list['impatience'], $max_voted_uname_list)){ //û��
      $vote_kill_uname = $ability_list['impatience'];
      break;
    }

    //������跺�Ը��䤫�����
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['good_luck']));
    if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }

    //���¿�����ɼ���跺�Ը��䤫�����
    $max_voted_uname_list = array_diff($max_voted_uname_list, array($ability_list['plague']));
    if(count($max_voted_uname_list) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
      $vote_kill_uname = array_shift($max_voted_uname_list);
      break;
    }
  }while(false);

  if($vote_kill_uname != ''){ //�跺�����¹�
    $vote_target = $USERS->ByUname($vote_kill_uname); //�桼����������

    //�跺����
    KillUser($vote_target->uname, 'VOTE_KILLED', &$dead_lovers_list);

    //�跺�Ԥ���¸�ԥꥹ�Ȥ������
    $live_uname_list = array_diff($live_uname_list, array($vote_target->uname));

    //�跺���줿�ͤ��Ǥ���äƤ������
    do{
      if(! $vote_target->is_role_group('poison')) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if($vote_target->is_role('dummy_poison', 'poison_guard')) break; //̴�Ǽԡ����Τ��оݳ�
      if($vote_target->is_role('incubate_poison') && $ROOM->date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      $pharmacist_success = false; //���������ե饰������
      $poison_voter_list  = array_keys($vote_target_list, $vote_target->uname); //��ɼ�����ͤ����
      foreach($poison_voter_list as $this_uname){ //���դΥ����å�
	$this_user = $USERS->ByUname($this_uname);
	if(! $this_user->is_role('pharmacist')) continue;

	//��������
	$sentence = $this_user->handle_name . "\t" . $vote_target->handle_name;
	InsertSystemMessage($sentence, 'PHARMACIST_SUCCESS');
	$pharmacist_success = true;
      }
      if($pharmacist_success) break;

      //�Ǥ��оݥ��ץ���������å����Ƹ���ԥꥹ�Ȥ����
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $poison_voter_list : $live_uname_list);
      if($vote_target->is_role('strong_poison')){ //���ǼԤʤ饿�����åȤ���¼�ͤ����
	$strong_poison_target_list = array();
	foreach($poison_target_list as $this_uname){
	  $this_user = $USERS->ByUname($this_uname);
	  if($this_user->is_role_group('wolf', 'fox')){
	    array_push($strong_poison_target_list, $this_uname);
	  }
	}
	$poison_target_list = $strong_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      //�оݼԤ����
      $rand_key = array_rand($poison_target_list);
      $poison_target = $USERS->ByUname($poison_target_list[$rand_key]);

      //��ȯȽ��
      if($vote_target->is_role('poison_wolf') && $poison_target->is_wolf()){ //��ϵ���ǤϿ�ϵ�ˤ�̵��
	//���ͤ��ǤޤäƤʤ��Τǥ����ƥ��å���������α
	// InsertSystemMessage($poison_target->handle_name, 'POISON_WOLF_TARGET');
	break;
      }

      if($vote_target->is_role('poison_fox') && $poison_target->is_fox()){ //�ɸѤ��Ǥ��ŸѤˤ�̵��
	break;
      }

      if($poison_target->is_active_role('resist_wolf')){ //����ϵ�ˤ�̵��
	UpdateRole($poison_target->uname, $poison_target->role . ' lost_ability');
	break;
      }

      KillUser($poison_target->uname, 'POISON_DEAD_day', &$dead_lovers_list); //��˴����
    }while(false);

    //��ǽ�Ϥ�Ƚ����
    $sentence = $vote_target->handle_name . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽ�Ԥ�Ƚ����
    if($vote_target->is_role('boss_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->is_role('cursed_fox', 'white_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->is_wolf()){
      $necromancer_result = 'wolf';
    }
    else{
      $necromancer_result = 'human';
    }

    if($USERS->is_appear('necromancer')){ //��ǽ�Ԥ�����Х����ƥ��å���������Ͽ
      InsertSystemMessage($sentence . $necromancer_result, $action);
    }

    if($USERS->is_appear('soul_necromancer')){ //��������Ƚ����
      InsertSystemMessage($sentence . $vote_target->main_role, 'SOUL_' . $action);
    }

    if($USERS->is_appear('dummy_necromancer')){ //̴��ͤ�Ƚ���̤�¼�ͤȿ�ϵ��ȿž����
      if($necromancer_result == 'human')    $necromancer_result = 'wolf';
      elseif($necromancer_result == 'wolf') $necromancer_result = 'human';
      InsertSystemMessage($sentence . $necromancer_result, 'DUMMY_' . $action);
    }
  }

  //�ü쥵���򿦤����������
  //��ɼ���оݥ桼��̾ => �Ϳ� �����������
  // print_r($vote_target_list); //�ǥХå���
  $voted_target_member_list = array_count_values($vote_target_list);
  $flag_medium = $USERS->is_appear('medium'); //����νи������å�
  foreach($live_uname_list as $this_uname){
    $this_user = $USERS->ByUname($this_uname);
    $this_type = '';

    if($this_user->is_role('chicken')){ //�����Ԥ���ɼ����Ƥ����饷��å���
      if($voted_target_member_list[$this_uname] > 0) $this_type = 'CHICKEN';
    }
    elseif($this_user->is_role('rabbit')){ //����������ɼ����Ƥ��ʤ��ä��饷��å���
      if($voted_target_member_list[$this_uname] == 0) $this_type = 'RABBIT';
    }
    elseif($this_user->is_role('perverseness')){
      //ŷ�ٵ��ϼ�ʬ����ɼ���ʣ���οͤ���ɼ���Ƥ����饷��å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] > 1) $this_type = 'PERVERSENESS';
    }
    elseif($this_user->is_role('flattery')){
      //���ޤ���ϼ�ʬ����ɼ���¾�οͤ���ɼ���Ƥ��ʤ���Х���å���
      if($voted_target_member_list[$vote_target_list[$this_uname]] < 2) $this_type = 'FLATTERY';
    }
    elseif($this_user->is_role('impatience')){
      if($vote_kill_uname == '') $this_type = 'IMPATIENCE'; //û���Ϻ���ɼ�ʤ饷��å���
    }
    elseif($this_user->is_role('panelist')){ //�����ԤϽ���Ԥ���ɼ�����饷��å���
      if($vote_target_list[$this_uname] == 'dummy_boy') $this_type = 'PANELIST';
    }

    if($this_type == '') continue;
    SuddenDeath($this_uname, $flag_medium, $this_type);
    if($this_user->is_lovers()) array_push($dead_lovers_list, $this_user->role);
  }
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //���͸��ɤ�����
  }

  if($vote_kill_uname != ''){ //����ڤ��ؤ�
    $check_draw = false; //����ʬ��Ƚ��¹ԥե饰�򥪥�
    mysql_query("UPDATE room SET day_night = 'night' WHERE room_no = $room_no"); //��ˤ���
    InsertSystemTalk('NIGHT', ++$ROOM->system_time, 'night system'); //�뤬��������
    UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
    // DeleteVote(); //���ޤǤ���ɼ���������
  }
  else{ //����ɼ����
    $check_draw = true; //����ʬ��Ƚ��¹ԥե饰�򥪥�
    $next_vote_times = $RQ_ARGS->vote_times + 1; //��ɼ��������䤹
    mysql_query("UPDATE system_message SET message = $next_vote_times WHERE room_no = $room_no
			AND date = {$ROOM->date} AND type = 'VOTE_TIMES'");

    //�����ƥ��å�����
    InsertSystemMessage($RQ_ARGS->vote_times, 'RE_VOTE');
    InsertSystemTalk("����ɼ�ˤʤ�ޤ���( {$RQ_ARGS->vote_times} ����)", ++$ROOM->system_time);
    UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  }
  mysql_query('COMMIT'); //������ߥå�
  CheckVictory($check_draw);
}

//����򿦤���ɼ����������å�������ɼ��̤��֤�
function CheckVoteNight($action, $role, $dummy_boy_role = '', $not_type = ''){
  global $room_no, $ROOM;

  //��ɼ��������
  $query_vote = "SELECT uname, target_uname FROM vote WHERE room_no = $room_no " .
    "AND date = {$ROOM->date} AND situation = '$action'";
  $vote_data = FetchAssoc($query_vote);
  $vote_count = count($vote_data); //��ɼ�Ϳ������

  if($not_type != ''){ //����󥻥륿���פ���ɼ��������
    $query_not_type = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no " .
      "AND date = {$ROOM->date} AND situation = '$not_type'";
    $vote_count += FetchResult($query_not_type); //��ɼ�Ϳ����ɲ�
  }

  //ϵ�γ��ߤϰ�ͤ� OK
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $vote_data : false);

  //�����Ƥ����о��򿦤οͿ��򥫥����
  $query_role = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no ".
    "AND live = 'live' AND user_no > 0 AND role LIKE '{$role}%'";
  if($action == 'TRAP_MAD_DO') $query_role .= " AND !(role LIKE '%lost_ability%')";
  $role_count = FetchResult($query_role);

  //�����������귯��������򿦤��ä����ϥ�����Ȥ��ʤ�
  if($dummy_boy_role != '' && strpos($role, $dummy_boy_role) !== false) $role_count--;

  return ($vote_count == $role_count ? $vote_data : false);
}

//��ν��׽���
function AggregateVoteNight(){
  global $GAME_CONF, $RQ_ARGS, $room_no, $ROOM, $USERS, $SELF;

  if($ROOM->test_mode){
    $vote_data = $RQ_ARGS->TestItems->vote_data;
  }
  else{
    //���ޥ�ɥ����å�
    $situation_list = array('WOLF_EAT', 'MAGE_DO', 'JAMMER_MAD_DO', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO',
			    'GUARD_DO', 'REPORTER_DO', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO',
			    'ASSASSIN_DO', 'ASSASSIN_NOT_DO', 'MANIA_DO', 'CHILD_FOX_DO', 'CUPID_DO');
    CheckSituation($situation_list);

    //ϵ����ɼ�����å�
    $vote_data->wolf = CheckVoteNight('WOLF_EAT', '%wolf');
    if($vote_data->wolf === false) return false;

    //�����������귯��������򿦤��ä����ϥ�����Ȥ��ʤ�
    if($ROOM->date == 1 && $ROOM->is_dummy_boy()){
      $this_dummy_boy_role = $USERS->GetRole('dummy_boy');
      $exclude_role_list   = array('mage', 'jammer_mad', 'mania', 'cupid'); //��������оݳ��򿦥ꥹ��

      foreach($exclude_role_list as $this_role){
	if(strpos($this_dummy_boy_role, $this_role) !== false){
	  $dummy_boy_role = $this_role;
	  break;
	}
      }
    }

    //�����ɼ�Ǥ����򿦤���ɼ�����å�
    $vote_data->mage = CheckVoteNight('MAGE_DO', '%mage', $dummy_boy_role);
    if($vote_data->mage === false) return false;

    $vote_data->jammer_mad = CheckVoteNight('JAMMER_MAD_DO', 'jammer_mad', $dummy_boy_role);
    if($vote_data->jammer_mad === false) return false;

    $vote_data->child_fox = CheckVoteNight('CHILD_FOX_DO', 'child_fox');
    if($vote_data->child_fox === false) return false;

    if($ROOM->date == 1){ //�����Τ���ɼ�Ǥ����򿦤�����å�
      $vote_data->mania = CheckVoteNight('MANIA_DO', 'mania', $dummy_boy_role);
      if($vote_data->mania === false) return false;

      if(CheckVoteNight('CUPID_DO', 'cupid', $dummy_boy_role) === false) return false;
    }
    else{ //�����ܰʹ���ɼ�Ǥ����򿦤�����å�
      $vote_data->trap_mad = CheckVoteNight('TRAP_MAD_DO', 'trap_mad', '', 'TRAP_MAD_NOT_DO');
      if($vote_data->trap_mad === false) return false;

      if(($vote_data->guard = CheckVoteNight('GUARD_DO', '%guard')) === false) return false;
      if(($vote_data->reporter = CheckVoteNight('REPORTER_DO', 'reporter')) === false) return false;
      if(! $ROOM->is_open_cast()){
	$vote_data->poison_cat = CheckVoteNight('POISON_CAT_DO', 'poison_cat', '', 'POISON_CAT_NOT_DO');
	if($vote_data->poison_cat === false) return false;
      }
      $vote_data->assassin = CheckVoteNight('ASSASSIN_DO', 'assassin', '', 'ASSASSIN_NOT_DO');
      if($vote_data->assassin === false) return false;
    }
  }

  //��ϵ�ν����������
  $voted_wolf  = $USERS->ByUname($vote_data->wolf['uname']);
  $wolf_target = $USERS->ByUname($vote_data->wolf['target_uname']);

  $guarded_uname = ''; //��Ҥ��줿�ͤΥ桼��̾ //ʣ�����ߤ��б�����ʤ餳����������Ѥ���
  $dead_uname_list    = array(); //��˴�ԥꥹ��
  $dead_lovers_list   = array(); //���͸��ɤ��оݼԥꥹ��
  $trap_target_list   = array(); //櫤�������ꥹ��
  $trapped_uname_list = array(); //櫤ˤ����ä��ͥꥹ��

  if($ROOM->date != 1){
    //櫻դ�������ꥹ�Ȥ����
    $trap_mad_list = array();
    foreach($vote_data->trap_mad as $array){
      $this_uname        = $array['uname'];
      $this_target_uname = $array['target_uname'];

      //�������֤�����ǽ�ϼ���
      UpdateRole($this_uname, $USERS->GetRole($this_uname) . ' lost_ability');

      //��ϵ�������Ƥ����鼫ʬ���Ȥؤ����ְʳ���̵��
      if($this_uname != $wolf_target->uname || $this_uname == $this_target_uname){
	$trap_mad_list[$this_uname] = $this_target_uname;
      }
    }

    //櫻դ���ʬ���Ȱʳ���櫤�ųݤ�����硢�������櫤����ä����ϻ�˴
    $trap_count_list = array_count_values($trap_mad_list);
    foreach($trap_mad_list as $this_uname => $this_target_uname){
      if($this_uname != $this_target_uname && $trap_count_list[$this_target_uname] > 1){
	array_push($trapped_uname_list, $this_uname);
      }
    }
    $trap_target_list = array_keys($trap_count_list);

    foreach($vote_data->guard as $array){ //��ͷϤν���
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);
      $sentence    = $this_user->handle_name . "\t";

      if($this_user->is_role('dummy_guard')){ //̴��ͤ�ɬ�����������å������������Ф�
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_SUCCESS');
	continue;
      }

      if($this_target->is_role('jammer_mad', 'trap_mad', 'cursed_fox')){ //���Ƚ��
	KillUser($this_target->uname, 'HUNTED', &$dead_lovers_list);
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_HUNTED');
	array_push($dead_uname_list, $this_target->uname);
      }

      if(in_array($this_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	array_push($trapped_uname_list, $this_user->uname);
	continue;
      }

      //�������Ƚ��
      if($this_target->uname != $wolf_target->uname) continue;
      InsertSystemMessage($sentence . $wolf_target->handle_name, 'GUARD_SUCCESS');

      //���ΤǤʤ���硢�������򿦤ϸ�Ҥ���Ƥ��Ƥ��ϵ�˽��⤵���
      if($this_user->is_role('poison_guard') || ! $wolf_target->is_role('reporter', 'assassin')){
	$guarded_uname = $this_target->uname;
      }
    }
  }

  do{ //��ϵ�ν�������Ƚ��
    //������� or ������¼����
    if($guarded_uname != '' || $ROOM->is_quiz()) break;

    //�����褬�ŸѤξ��ϼ��Ԥ���
    if($wolf_target->is_fox() && ! $wolf_target->is_role('child_fox', 'poison_fox', 'white_fox')){
      InsertSystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    if(in_array($wolf_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
      array_push($trapped_uname_list, $voted_wolf->uname);
      break;
    }

    //�������
    KillUser($wolf_target->uname, 'WOLF_KILLED', &$dead_lovers_list);
    array_push($dead_uname_list, $wolf_target->uname);

    if($voted_wolf->is_active_role('tongue_wolf')){ //���ϵ�ν���
      $wolf_target_main_role = GetMainRole($wolf_target->role);
      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      InsertSystemMessage($sentence . $wolf_target_main_role, 'TONGUE_WOLF_RESULT');

      if($wolf_target_main_role == 'human'){ //¼�ͤʤ�ǽ�ϼ���
	UpdateRole($voted_wolf->uname, $voted_wolf->role . ' lost_ability');
      }
    }

    //���٤�줿�ͤ��ǻ������ä����
    do{
      if(! $wolf_target->is_role_group('poison')) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if($wolf_target->is_role('dummy_poison')) break;//̴�ǼԤ��оݳ�
      if($wolf_target->is_role('incubate_poison') && $ROOM->date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      //�����Ƥ���ϵ�����
      $live_wolf_list = ($GAME_CONF->poison_only_eater ? array($voted_wolf->uname) : GetLiveWolves());

      $rand_key = array_rand($live_wolf_list);
      $poison_target = $USERS->ByUname($live_wolf_list[$rand_key]);

      if($poison_target->is_active_role('resist_wolf')){ //����ϵ�ʤ�̵��
	UpdateRole($poison_target->uname, $poison_target->role . ' lost_ability');
	break;
      }

      //�ǻ����
      KillUser($poison_target->uname, 'POISON_DEAD_night', &$dead_lovers_list);
      array_push($dead_uname_list, $poison_target->uname);
    }while(false);
  }while(false);

  //����¾��ǽ�ϼԤ���ɼ����
  /*
    ��ϵ���ꤤ�ա��֥󲰤ʤɡ���ư��̤ǻ�Ԥ��Ф륿���פ�Ƚ�������

    ������1) �ɤ����Ƚ�����˹Ԥ������ŸѤ����ब��ޤ� (����Ū�ˤϿ�ϵ�ν����ͥ�褹��)
    ��ϵ   �� �ꤤ��
    �ꤤ�� �� �Ÿ�

    ������2) �ɤ����Ƚ�����˹Ԥ����ǥ֥󲰤����ब��ޤ� (���ߤ��ꤤ�դ���)
    �ꤤ�� �� �Ÿ�
    �֥� �� �Ÿ�
  */

  if($ROOM->date != 1){
    $assassin_target_list = array(); //�Ż��оݼԥꥹ��
    foreach($vote_data->assassin as $array){ //�Ż��Ԥν���
      $this_uname = $array['uname'];
      if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

      $this_target_uname = $array['target_uname'];
      if(in_array($this_target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	array_push($trapped_uname_list, $this_uname);
	continue;
      }

      array_push($assassin_target_list, $this_target_uname); //�Ż��оݼԥꥹ�Ȥ��ɲ�
    }

    foreach($trapped_uname_list as $this_uname){ //櫤λ�˴����
      if(in_array($this_uname, $dead_uname_list)) continue;
      KillUser($this_uname, 'TRAPPED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
    }

    foreach($assassin_target_list as $this_uname){ //�Ż�����
      if(in_array($this_uname, $dead_uname_list)) continue;
      KillUser($this_uname, 'ASSASSIN_KILLED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
    }
  }

  $jammer_target_list = array(); //˸���оݥꥹ��
  foreach($vote_data->jammer_mad as $array){ //���ⶸ�ͤν���
    $this_uname = $array['uname'];
    if(in_array($this_uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
    if($this_target->is_role_group('cursed')){ //�оݤ����������ä����ϼ��֤��������
      KillUser($this_uname, 'CURSED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_uname);
      continue;
    }

    array_push($jammer_target_list, $this_target->uname); //˸���оݼԥꥹ�Ȥ��ɲ�
  }

  //��������Τα��Ĥ�Ƚ���о��򿦥ꥹ��
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  foreach($vote_data->mage as $array){ //�ꤤ�շϤν���
    $this_user = $USERS->ByUname($array['uname']);
    if(in_array($this_user->uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������

    if($this_user->is_role('dummy_mage')){ //̴���ͤ��ꤤ��̤�¼�ͤȿ�ϵ��ȿž������
      $this_result = $this_target->DistinguishMage(true);
      if($this_result == 'human')    $this_result = 'wolf';
      elseif($this_result == 'wolf') $this_result = 'human';
    }
    elseif($this_user->is_role('psycho_mage')){ //��������Τ�Ƚ��
      if(in_array($this_user->uname, $jammer_target_list)){ //���ⶸ�ͤ�˸��Ƚ��
	$this_result = 'mage_failed';
      }
      else{
	$this_result = 'psycho_mage_normal';
	foreach($psycho_mage_liar_list as $this_liar_role){
	  if($this_target->is_role_group($this_liar_role)){
	    $this_result = 'psycho_mage_liar';
	    break;
	  }
	}
      }
    }
    elseif($this_user->is_role('sex_mage')){ //�Ҥ褳����Τ�Ƚ��
      if(in_array($this_user->uname, $jammer_target_list)){ //���ⶸ�ͤ�˸��Ƚ��
	$this_result = 'mage_failed';
      }
      else{
	$this_result = 'sex_' . $this_target->sex;
      }
    }
    else{
      if($this_target->is_role_group('cursed')){ //������������ä�����֤��������
	KillUser($this_user->uname, 'CURSED', &$dead_lovers_list);
	array_push($dead_uname_list, $this_user->uname);
	continue;
      }

      if(in_array($this_user->uname, $jammer_target_list)){ //���ⶸ�ͤ�˸��Ƚ��
	$this_result = 'failed';
      }
      else{
	if($this_user->is_role('soul_mage')){ //�����ꤤ�դ��ꤤ��̤ϥᥤ����
	  $this_result = GetMainRole($this_target->role);
	}
	else{
	  do{ //����Ƚ��
	    if(in_array($this_target->uname, $dead_uname_list)) break; //���˻��Ǥ����饹���å�
	    if(! $this_target->is_fox()) break; //�ŸѰʳ����оݳ�
	    if($this_target->is_role('child_fox', 'white_fox')) break; //�������ŸѤ��оݳ�
	    KillUser($this_target->uname, 'FOX_DEAD', &$dead_lovers_list);
	    array_push($dead_uname_list, $this_target->uname);
	  }while(false);

	  $this_result = $this_target->DistinguishMage(); //Ƚ���̤����
	}
      }
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  foreach($vote_data->child_fox as $array){ //�ҸѤν���
    $this_user = $USERS->ByUname($array['uname']);
    if(in_array($this_user->uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
    if($this_target->is_role_group('cursed')){ //������������ä�����֤��������
      KillUser($this_user->uname, 'CURSED', &$dead_lovers_list);
      array_push($dead_uname_list, $this_user->uname);
      continue;
    }

    //�ꤤ��̤����
    if(in_array($this_user->uname, $jammer_target_list) || mt_rand(1, 100) <= 30){ //�����Ψ�Ǽ��Ԥ���
      $this_result = 'failed';
    }
    else{
      $this_result = $this_target->DistinguishMage();
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  if($ROOM->date == 1){
    foreach($vote_data->mania as $array){ //���åޥ˥��ν���
      $this_user = $USERS->ByUname($array['uname']);
      if(in_array($this_user->uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

      $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������

      //���ԡ����� (���åޥ˥�����ꤷ������¼�ͤˤ���)
      if(($this_result = GetMainRole($this_target->role)) == 'mania' ||
	 $this_target->is_role('copied')) $this_result = 'human';
      $this_new_role = str_replace('mania', $this_result, $this_user->role) . ' copied';
      UpdateRole($this_user->uname, $this_new_role);

      $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'MANIA_RESULT');
    }
  }
  else{
    //�֥󲰤ν���
    foreach($vote_data->reporter as $array){
      $this_user = $USERS->ByUname($array['uname']);
      if(in_array($this_user->uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

      $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
      if(in_array($this_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	UpdateLive($this_user->uname);
	InsertSystemMessage($this_user->handle_name, 'TRAPPED');
	if($this_user->is_lovers()){ //���͸��ɤ�����
	  array_push($dead_lovers_list, $this_user->role);
	}
	array_push($dead_uname_list, $this_user->uname);
	continue;
      }

      if($this_target->uname == $wolf_target->uname){ //��������
	if($this_target->uname == $guarded_uname) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
	$sentence = $this_user->handle_name . "\t" . $wolf_target->handle_name . "\t";
	InsertSystemMessage($sentence . $voted_wolf->handle_name, 'REPORTER_SUCCESS');
	continue;
      }

      //�����оݤ�ľ���˻��Ǥ����鲿�ⵯ���ʤ�
      if(in_array($this_target->uname, $dead_uname_list)) continue;

      if($this_target->is_role_group('wolf', 'fox')){ //�����оݤ���ϵ���ŸѤʤ黦�����
	UpdateLive($this_user->uname);
	InsertSystemMessage($this_user->handle_name, 'REPORTER_DUTY');
	if($this_user->is_lovers()){ //���͸��ɤ�����
	  array_push($dead_lovers_list, $this_user->role);
	}
	array_push($dead_uname_list, $this_user->uname);
      }
    }

    if(! $ROOM->is_open_cast()){ //ǭ���ν���
      $revive_uname_list = array(); //�����ԥꥹ��
      foreach($vote_data->poison_cat as $array){
	$this_user = $USERS->ByUname($array['uname']);
	if(in_array($this_user->uname, $dead_uname_list)) continue; //ľ���˻��Ǥ�����̵��

	$this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������

	//����Ƚ��
	$this_rand = mt_rand(1, 100); //����Ƚ�������
	$this_result = 'failed';
	if($ROOM->test_mode) echo "revive rand : $this_rand <br>";
	do{
	  if($this_rand > 25) break; //��������
	  if($this_rand <= 5){ //��������
	    if($ROOM->test_mode){
	      $new_target_list = array();
	      foreach($USERS->rows as $this_new_target){
		if($this_new_target->is_dead() && ! $this_new_target->is_dummy_boy()){
		  array_push($new_target_list, $this_new_target->uname);
		}
	      }
	      if(count($new_target_list) > 0){
		$new_target_key = array_rand($new_target_list);
		$this_target = $USERS->ByUname($new_target_list[$new_target_key]);
	      }
	    }
	    else{
	      $query = "SELECT uname FROM user_entry WHERE room_no = $room_no AND live = 'dead' " .
		"AND uname <> 'dummy_boy' AND uname <> '{$this_target->uname}' " .
		"AND user_no > 0 ORDER BY MD5(RAND()*NOW())";
	      if(($new_target = FetchResult($query)) !== false){ //¾���оݤ���������������ؤ��
		$this_target = $USERS->ByUname($new_target);
	      }
	    }
	  }
	  if($this_target->is_role('poison_cat')) break; //ǭ���ʤ���������

	  $this_result = 'success';
	  if(in_array($this_target->uname, $revive_uname_list)) break; //�����Ѥߤʤ饹���å�

	  UpdateLive($this_target->uname, true);
	  InsertSystemMessage($this_target->handle_name, 'REVIVE_SUCCESS');
	  if($this_target->is_lovers()){ //���ͤʤ�¨����
	    array_push($dead_lovers_list, $this_target->role);
	  }
	  array_push($revive_uname_list, $this_target->uname);
	}while(false);

	if($this_result == 'failed') InsertSystemMessage($this_target->handle_name, 'REVIVE_FAILED');
	$sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  if($ROOM->test_mode){
    echo "dead_lovers_list : "; print_r($dead_lovers_list); echo "<br>";
    return;
  }

  $flag_medium = $USERS->is_appear('medium'); //����νи������å�
  foreach($dead_lovers_list as $this_role){
    LoversFollowed($this_role, $flag_medium); //���͸��ɤ�����
  }

  //�������ˤ���
  $next_date = $ROOM->date + 1;
  mysql_query("UPDATE room SET date = $next_date, day_night = 'day' WHERE room_no = $room_no");

  //�������ν跺��ɼ�Υ�����Ȥ� 1 �˽����(����ɼ��������)
  InsertSystemMessage('1', 'VOTE_TIMES', $next_date);

  //�뤬����������
  InsertSystemTalk("MORNING\t" . $next_date, ++$ROOM->system_time, 'day system', $next_date);
  UpdateTime(); //�ǽ��񤭹��ߤ򹹿�
  // DeleteVote(); //���ޤǤ���ɼ���������

  CheckVictory(); //���ԤΥ����å�
  mysql_query('COMMIT'); //������ߥå�
}

//�򿦾���򹹿�����
function UpdateRole($uname, $role){
  global $room_no, $ROOM;

  if($ROOM->test_mode){
    echo "Change Role: $uname => $role <br>";
    return;
  }
  mysql_query("UPDATE user_entry SET role = '$role' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
}

//��ɼ���ޥ�ɤ����äƤ��뤫�����å�
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return;

  OutputVoteResult('̵������ɼ�Ǥ�');
}

//��˴����
function KillUser($uname, $reason, &$dead_lovers_list){
  global $USERS;

  $target_handle = $USERS->GetHandleName($uname);
  $target_role   = $USERS->GetRole($uname);

  UpdateLive($uname);
  InsertSystemMessage($target_handle, $reason);
  SaveLastWords($target_handle);
  if(strpos($target_role, 'lovers') !== false){ //���͸��ɤ�����
    array_push($dead_lovers_list, $target_role);
  }
}
?>
