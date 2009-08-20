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
  elseif(false){ //��Ʈ¼
    $temp_role_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'wolf') !== false)
	$temp_role_list['wolf'] += (int)$value;
      elseif(strpos($key, 'mad') !== false)
	$temp_role_list['trap_mad'] += (int)$value;
      else
	$temp_role_list['assassin'] += (int)$value;
    }
    if($user_count > 15){
      $temp_role_list['assassin'] -= 2;
      $temp_role_list['trap_mad']++;
      $temp_role_list['wolf']++;
    }
    $role_list = $temp_role_list;
  }
  elseif(strpos($ROOM->game_option, 'chaosfull') !== false){ //��������
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
      // elseif($rand <  5)  $role_list['poison_fox']++;
      // elseif($rand <  8)  $role_list['white_fox']++;
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
      elseif($rand < 230) $role_list['fox']++;
      elseif($rand < 235) $role_list['cursed_fox']++;
      // elseif($rand < 240) $role_list['poison_fox']++;
      // elseif($rand < 245) $role_list['white_fox']++;
      elseif($rand < 250) $role_list['child_fox']++;
      elseif($rand < 280) $role_list['mage']++;
      elseif($rand < 290) $role_list['soul_mage']++;
      elseif($rand < 300) $role_list['psycho_mage']++;
      elseif($rand < 320) $role_list['dummy_mage']++;
      elseif($rand < 360) $role_list['necromancer']++;
      elseif($rand < 370) $role_list['soul_necromancer']++;
      elseif($rand < 390) $role_list['dummy_necromancer']++;
      elseif($rand < 430) $role_list['medium']++;
      elseif($rand < 470) $role_list['mad']++;
      elseif($rand < 480) $role_list['fanatic_mad']++;
      elseif($rand < 500) $role_list['trap_mad']++;
      elseif($rand < 510) $role_list['whisper_mad']++;
      elseif($rand < 590) $role_list['common']++;
      elseif($rand < 600) $role_list['dummy_common']++;
      elseif($rand < 630) $role_list['guard']++;
      elseif($rand < 640) $role_list['poison_guard']++;
      elseif($rand < 650) $role_list['dummy_guard']++;
      elseif($rand < 670) $role_list['reporter']++;
      elseif($rand < 690) $role_list['poison']++;
      elseif($rand < 700) $role_list['strong_poison']++;
      elseif($rand < 710) $role_list['incubate_poison']++;
      elseif($rand < 720) $role_list['dummy_poison']++;
      elseif($rand < 730) $role_list['poison_cat']++;
      elseif($rand < 760) $role_list['pharmacist']++;
      elseif($rand < 790) $role_list['cupid']++;
      elseif($rand < 810) $role_list['mania']++;
      elseif($rand < 830) $role_list['assassin']++;
      elseif($rand < 850) $role_list['suspect']++;
      elseif($rand < 870) $role_list['unconscious']++;
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
  elseif(strpos($ROOM->game_option, 'chaos') !== false){ //����
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
?>
