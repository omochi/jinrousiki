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

  if($ROOM->IsQuiz()){ //������¼
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
  elseif($ROOM->IsOption('chaosfull')){ //��������
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
      elseif($rand < 640) $role_list['guard']++;
      elseif($rand < 650) $role_list['poison_guard']++;
      elseif($rand < 660) $role_list['dummy_guard']++;
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
    $over_wolf_count = array_sum($wolf_count_list) - round($user_count * 0.25);
    if($over_wolf_count == array_sum($wolf_count_list)) $over_wolf_count--;
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
    $over_fox_count = array_sum($fox_count_list) - round($user_count * 0.12);
    for(; $over_fox_count > 0; $over_fox_count--){
      arsort($fox_count_list);
      $this_key = key($fox_count_list);
      $fox_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
    }

    $over_cupid_count = $role_list['cupid'] - round($user_count * 0.1);
    if($over_cupid_count > 0){
      $role_list['cupid'] -= $over_cupid_count;
      $role_list['human'] += $over_cupid_count;;
    }

    $mage_count_list = array();
    foreach($role_list as $key => $value){
      if(strpos($key, 'mage') !== false) $mage_count_list[$key] = $value;
    }
    $over_mage_count = array_sum($mage_count_list) - round($user_count * 0.2);
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
    $over_guard_count = array_sum($guard_count_list) - round($user_count * 0.15);
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
    $over_common_count = array_sum($common_count_list) - round($user_count * 0.2);
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
    $over_poison_count = array_sum($poison_count_list) - round($user_count * 0.15);
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
    $over_mad_count = array_sum($mad_count_list) - round($user_count * 0.15);
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
    $over_assassin_count = array_sum($assassin_count_list) - round($user_count * 0.15);
    for(; $over_assassin_count > 0; $over_assassin_count--){
      arsort($assassin_count_list);
      $this_key = key($assassin_count_list);
      $assassin_count_list[$this_key]--;
      $role_list[$this_key]--;
      $role_list['human']++;
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
    if($user_count < 8){
      $fox_count = 0;
    }
    elseif($user_count < 15){ //0:1 = 90:10
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
    if($user_count < 8){
      $lovers_count = 0;
    }
    elseif($user_count < 10){ //0:1 = 95:5
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

  if(! $ROOM->test_mode) CheckSituation('VOTE_KILL'); //���ޥ�ɥ����å�

  //�����Ƥ���桼���������
  $user_list = $USERS->GetLivingUsers();

  if(! $ROOM->test_mode){
    //��ɼ��������
    $query = "SELECT COUNT(uname) FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times}";
    if(FetchResult($query) != count($user_list)) return false; //��������ɼ���Ƥ��ʤ���н��������å�
  }

  $max_voted_number = 0;  //��¿��ɼ��
  $vote_kill_uname  = ''; //�跺�����ͤΥ桼��̾
  $live_uname_list   = array(); //�����Ƥ���ͤΥ桼��̾�ꥹ��
  $vote_message_list = array(); //�����ƥ��å������� (�桼��̾ => array())
  $vote_target_list  = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��桼��̾)
  $vote_count_list   = array(); //��ɼ�ꥹ�� (�桼��̾ => ��ɼ��)
  $ability_list      = array(); //ǽ�ϼԤ�������ɼ���
  if(! $ROOM->test_mode){
    $query = "FROM vote WHERE room_no = $room_no AND date = {$ROOM->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = {$RQ_ARGS->vote_times} AND"; //���̥�����
  }

  //��ͤ��ļ�ʬ����ɼ���줿����Ĵ�٤ƽ跺���٤��ͤ���ꤹ��
  foreach($user_list as $this_uname){
    $user = $USERS->ByUname($this_uname);

    //��ʬ����ɼ�������
    $voted_number = ($ROOM->test_mode ? (int)$RQ_ARGS->TestItems->vote_day_count_list[$user->uname] :
		     FetchResult("SELECT SUM(vote_number) $query target_uname = '{$user->uname}'"));

    //�ü쥵���򿦤���ɼ����
    if($user->IsRole('upper_luck')) //����
      $voted_number += ($ROOM->date == 2 ? 4 : -2);
    elseif($user->IsRole('downer_luck')) //��ȯ��
      $voted_number += ($ROOM->date == 2 ? -4 : 2);
    elseif($user->IsRole('random_luck')) //��������
      $voted_number += (mt_rand(1, 5) - 3);
    elseif($user->IsRole('star')) //�͵���
      $voted_number--;
    elseif($user->IsRole('disfavor')) //�Կ͵�
      $voted_number++;
    if($voted_number < 0) $voted_number = 0; //�ޥ��ʥ��ˤʤäƤ����� 0 �ˤ���

    //��ʬ����ɼ��ξ�������
    $array = ($ROOM->test_mode ? $RQ_ARGS->TestItems->vote_day[$user->uname] :
	      FetchNameArray("SELECT target_uname, vote_number $query uname = '{$user->uname}'"));
    $target = $USERS->ByUname($array['target_uname']);
    $vote_number = (int)$array['vote_number'];

    //�����ƥ��å������Ѥ����������
    $message_list = array('target'       => $target->handle_name,
			  'voted_number' => $voted_number,
			  'vote_number'  => $vote_number);

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

  //ȿ�ռԤ�Ƚ��
  if($ability_list['rebel'] != '' && $ability_list['rebel'] == $ability_list['authority']){
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
    $vote_target->Kill('VOTE_KILLED'); //�跺����
    unset($live_uname_list[$vote_target->user_no]); //�跺�Ԥ���¸�ԥꥹ�Ȥ������

    //�跺���줿�ͤ��Ǥ���äƤ������
    do{
      if(! $vote_target->IsRoleGroup('poison')) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if($vote_target->IsRole('dummy_poison', 'poison_guard')) break; //̴�Ǽԡ����Τ��оݳ�
      if($vote_target->IsRole('incubate_poison') && $ROOM->date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      $pharmacist_success = false; //���������ե饰������
      $poison_voter_list  = array_keys($vote_target_list, $vote_target->uname); //��ɼ�����ͤ����
      foreach($poison_voter_list as $this_uname){ //���դ�Ƚ��
	$user = $USERS->ByUname($this_uname);
	if(! $user->IsRole('pharmacist')) continue;

	//��������
	$sentence = $user->handle_name . "\t" . $vote_target->handle_name;
	InsertSystemMessage($sentence, 'PHARMACIST_SUCCESS');
	$pharmacist_success = true;
      }
      if($pharmacist_success) break;

      //�Ǥ��оݥ��ץ���������å����Ƹ���ԥꥹ�Ȥ����
      $poison_target_list = ($GAME_CONF->poison_only_voter ? $poison_voter_list : $live_uname_list);

      //���Ǽԡ����ǼԤʤ饿�����åȤ���¼�ͤ����
      if($vote_target->IsRole('strong_poison', 'incubate_poison')){
	$strong_poison_target_list = array();
	foreach($poison_target_list as $this_uname){
	  $user = $USERS->ByUname($this_uname);
	  if($user->IsRoleGroup('wolf', 'fox')) $strong_poison_target_list[] = $this_uname;
	}
	$poison_target_list = $strong_poison_target_list;
      }
      if(count($poison_target_list) < 1) break;

      $poison_target = $USERS->ByUname(GetRandom($poison_target_list)); //�оݼԤ����

      //��ȯȽ��
      if($vote_target->IsRole('poison_wolf') && $poison_target->IsWolf()){ //��ϵ���ǤϿ�ϵ�ˤ�̵��
	//���ͤ��ǤޤäƤʤ��Τǥ����ƥ��å���������α
	// InsertSystemMessage($poison_target->handle_name, 'POISON_WOLF_TARGET');
	break;
      }

      if($vote_target->IsRole('poison_fox') && $poison_target->IsFox()){ //�ɸѤ��Ǥ��ŸѤˤ�̵��
	break;
      }

      if($poison_target->IsActiveRole('resist_wolf')){ //����ϵ�ˤ�̵��
	$poison_target->AddRole('lost_ability');
	break;
      }

      $poison_target->Kill('POISON_DEAD_day'); //��˴����
    }while(false);

    //��ǽ�Ϥ�Ƚ����
    $sentence = $vote_target->handle_name . "\t";
    $action = 'NECROMANCER_RESULT';

    //��ǽ�Ԥ�Ƚ����
    if($vote_target->IsRole('boss_wolf', 'child_fox')){
      $necromancer_result = $vote_target->main_role;
    }
    elseif($vote_target->IsRole('cursed_fox', 'white_fox')){
      $necromancer_result = 'fox';
    }
    elseif($vote_target->IsWolf()){
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
  foreach($live_uname_list as $this_uname){
    $user = $USERS->ByUname($this_uname);
    $reason = '';

    if($user->IsRole('chicken')){ //�����Ԥ���ɼ����Ƥ����饷��å���
      if($voted_target_member_list[$this_uname] > 0) $reason = 'CHICKEN';
    }
    elseif($user->IsRole('rabbit')){ //����������ɼ����Ƥ��ʤ��ä��饷��å���
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
    elseif($user->IsRole('impatience')){
      if($vote_kill_uname == '') $reason = 'IMPATIENCE'; //û���Ϻ���ɼ�ʤ饷��å���
    }
    elseif($user->IsRole('panelist')){ //�����ԤϽ���Ԥ���ɼ�����饷��å���
      if($vote_target_list[$this_uname] == 'dummy_boy') $reason = 'PANELIST';
    }

    if($reason != '') $user->SuddenDeath($reason);
  }
  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����
  if($ROOM->test_mode) return $vote_message_list;

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
  if($action == 'WOLF_EAT') return ($vote_count > 0 ? $vote_data[0] : false);

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
    $vote_data = $RQ_ARGS->TestItems->vote_night;
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
    if($ROOM->date == 1 && $ROOM->IsDummyBoy()){
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
      if(! $ROOM->IsOpenCast()){
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
  $trap_target_list   = array(); //櫤�������ꥹ��
  $trapped_uname_list = array(); //櫤ˤ����ä��ͥꥹ��

  if($ROOM->date != 1){
    //櫻դ�������ꥹ�Ȥ����
    $trap_mad_list = array();
    foreach($vote_data->trap_mad as $array){
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);

      $this_user->AddRole('lost_ability'); //�������֤�����ǽ�ϼ���

      //��ϵ�������Ƥ����鼫ʬ���Ȥؤ����ְʳ���̵��
      if($this_user != $wolf_target || $this_user == $this_target){
	$trap_mad_list[$this_user->uname] = $this_target->uname;
      }
    }

    //櫻դ���ʬ���Ȱʳ���櫤�ųݤ�����硢�������櫤����ä����ϻ�˴
    $trap_count_list = array_count_values($trap_mad_list);
    foreach($trap_mad_list as $this_uname => $this_target_uname){
      if($this_uname != $this_target_uname && $trap_count_list[$this_target_uname] > 1){
	$trapped_uname_list[] = $this_uname;
      }
    }
    $trap_target_list = array_keys($trap_count_list);

    foreach($vote_data->guard as $array){ //��ͷϤν���
      $this_user   = $USERS->ByUname($array['uname']);
      $this_target = $USERS->ByUname($array['target_uname']);
      $sentence    = $this_user->handle_name . "\t";

      if($this_user->IsRole('dummy_guard')){ //̴��ͤ�ɬ�����������å������������Ф�
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_SUCCESS');
	continue;
      }

      if($this_target->IsRole('jammer_mad', 'trap_mad', 'cursed_fox')){ //���Ƚ��
	$this_target->Kill('HUNTED');
	InsertSystemMessage($sentence . $this_target->handle_name, 'GUARD_HUNTED');
      }

      if(in_array($this_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_uname_list[] = $this_user->uname;
	continue;
      }

      if($this_target != $wolf_target) continue; //�������Ƚ��
      InsertSystemMessage($sentence . $wolf_target->handle_name, 'GUARD_SUCCESS');

      //���ΤǤʤ���硢�������򿦤ϸ�Ҥ���Ƥ��Ƥ��ϵ�˽��⤵���
      if($this_user->IsRole('poison_guard') || ! $wolf_target->IsRole('reporter', 'assassin')){
	$guarded_uname = $this_target->uname;
      }
    }
  }

  do{ //��ϵ�ν�������Ƚ��
    if($guarded_uname != '' || $ROOM->IsQuiz()) break; //������� or ������¼����

    //�����褬�ŸѤξ��ϼ��Ԥ���
    if($wolf_target->IsFox() && ! $wolf_target->IsRole('poison_fox', 'white_fox', 'child_fox')){
      InsertSystemMessage($wolf_target->handle_name, 'FOX_EAT');
      break;
    }

    if(in_array($wolf_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
      $trapped_uname_list[] = $voted_wolf->uname;
      break;
    }

    $wolf_target->Kill('WOLF_KILLED'); //�������

    if($voted_wolf->IsActiveRole('tongue_wolf')){ //���ϵ�ν���
      $sentence = $voted_wolf->handle_name . "\t" . $wolf_target->handle_name . "\t";
      InsertSystemMessage($sentence . $wolf_target->main_role, 'TONGUE_WOLF_RESULT');

      if($wolf_target->main_role == 'human') $voted_wolf->AddRole('lost_ability'); //¼�ͤʤ�ǽ�ϼ���
    }

    do{ //�ǻ�Ƚ�����
      if(! $wolf_target->IsRoleGroup('poison')) break; //�Ǥ���äƤ��ʤ����ȯư���ʤ�
      if($wolf_target->IsRole('dummy_poison')) break; //̴�ǼԤ��оݳ�
      if($wolf_target->IsRole('incubate_poison') && $ROOM->date < 5) break; //���ǼԤ� 5 ���ܰʹ�

      //�����Ƥ���ϵ�����
      $live_wolf_list = ($GAME_CONF->poison_only_eater ? array($voted_wolf->uname) :
			 $USERS->GetLivingWolves());

      $poison_target = $USERS->ByUname(GetRandom($live_wolf_list));

      if($poison_target->IsActiveRole('resist_wolf')){ //����ϵ�ʤ�̵��
	$poison_target->AddRole('lost_ability');
	break;
      }

      $poison_target->Kill('POISON_DEAD_night'); //�ǻ����
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
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

      $this_target_uname = $array['target_uname'];
      if(in_array($this_target_uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$trapped_uname_list[] = $this_user->uname;
	continue;
      }

      if(! in_array($this_target_uname, $assassin_target_list)){
	$assassin_target_list[] = $this_target_uname; //�Ż��оݼԥꥹ�Ȥ��ɲ�
      }
    }

    foreach($trapped_uname_list as $this_uname){ //櫤λ�˴����
      $USERS->ByUname($this_uname)->Kill('TRAPPED');
    }

    foreach($assassin_target_list as $this_uname){ //�Ż�����
      $USERS->ByUname($this_uname)->Kill('ASSASSIN_KILLED');
    }
  }

  $jammer_target_list = array(); //˸���оݥꥹ��
  foreach($vote_data->jammer_mad as $array){ //���ⶸ�ͤν���
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
    if($this_target->IsRoleGroup('cursed')){ //���֤�Ƚ��
      $this_user->Kill('CURSED');
      continue;
    }

    if(! in_array($this_target->uname, $jammer_target_list)){
      $jammer_target_list[] = $this_target->uname; //˸���оݼԥꥹ�Ȥ��ɲ�
    }
  }

  //��������Τα��Ĥ�Ƚ���о��򿦥ꥹ��
  $psycho_mage_liar_list = array('mad', 'dummy', 'suspect', 'unconscious');
  foreach($vote_data->mage as $array){ //�ꤤ�շϤν���
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������

    if($this_user->IsRole('dummy_mage')){ //̴���ͤ��ꤤ��̤�¼�ͤȿ�ϵ��ȿž������
      $this_result = $this_target->DistinguishMage(true);
    }
    elseif(in_array($this_user->uname, $jammer_target_list)){ //���ⶸ�ͤ�˸��Ƚ��
      $this_result = ($this_user->IsRole('psycho_mage', 'sex_mage') ? 'mage_failed' : 'failed');
    }
    elseif($this_user->IsRole('soul_mage')){ //�����ꤤ�դ��ꤤ��̤ϥᥤ����
      $this_result = $this_target->main_role;
    }
    elseif($this_user->IsRole('psycho_mage')){ //��������Τ�Ƚ��
      $this_result = 'psycho_mage_normal';
      foreach($psycho_mage_liar_list as $this_liar_role){
	if($this_target->IsRoleGroup($this_liar_role)){
	  $this_result = 'psycho_mage_liar';
	  break;
	}
      }
    }
    elseif($this_user->IsRole('sex_mage')){ //�Ҥ褳����Τ�Ƚ��
      $this_result =  'sex_' . $this_target->sex;
    }
    else{
      if($this_target->IsRoleGroup('cursed')){ //���֤�Ƚ��
	$this_user->Kill('CURSED');
	continue;
      }

      if($this_target->IsFox() && ! $this_target->IsRole('white_fox', 'child_fox')){ //����Ƚ��
	$this_target->Kill('FOX_DEAD');
      }

      $this_result = $this_target->DistinguishMage(); //Ƚ���̤����
    }
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'MAGE_RESULT');
  }

  foreach($vote_data->child_fox as $array){ //�ҸѤν���
    $this_user = $USERS->ByUname($array['uname']);
    if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

    $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
    if($this_target->IsRoleGroup('cursed')){ //������������ä�����֤��������
      $this_user->Kill('CURSED');
      continue;
    }

    //�ꤤ��̤����
    //���ⶸ�ͤ˼��⤵��뤫�������Ψ�Ǽ��Ԥ���
    $failed_flag = (in_array($this_user->uname, $jammer_target_list) || mt_rand(1, 100) <= 30);
    $this_result = ($failed_flag ? 'failed' : $this_target->DistinguishMage());
    $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
    InsertSystemMessage($sentence, 'CHILD_FOX_RESULT');
  }

  if($ROOM->date == 1){
    foreach($vote_data->mania as $array){ //���åޥ˥��ν���
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

      $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������

      //���ԡ����� (���åޥ˥�����ꤷ������¼�ͤˤ���)
      $this_result = ($this_target->IsRole('mania', 'copied') ? 'human' : $this_target->main_role);
      $this_new_role = str_replace('mania', $this_result, $this_user->role) . ' copied';
      $this_user->ChangeRole($this_new_role);

      $sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
      InsertSystemMessage($sentence, 'MANIA_RESULT');
    }
  }
  else{
    foreach($vote_data->reporter as $array){ //�֥󲰤ν���
      $this_user = $USERS->ByUname($array['uname']);
      if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

      $this_target = $USERS->ByUname($array['target_uname']); //�оݼԤξ�������
      if(in_array($this_target->uname, $trap_target_list)){ //櫤����֤���Ƥ������˴
	$this_user->Kill('TRAPPED');
	continue;
      }

      if($this_target == $wolf_target){ //��������
	if($this_target->uname == $guarded_uname) continue; //��Ҥ���Ƥ������ϲ���Фʤ�
	$sentence = $this_user->handle_name . "\t" . $wolf_target->handle_name . "\t";
	InsertSystemMessage($sentence . $voted_wolf->handle_name, 'REPORTER_SUCCESS');
	continue;
      }

      if($this_target->dead_flag) continue; //�����оݤ�ľ���˻��Ǥ����鲿�ⵯ���ʤ�

      if($this_target->IsRoleGroup('wolf', 'fox')){ //�����оݤ���ϵ���ŸѤʤ黦�����
	$this_user->Kill('REPORTER_DUTY');
      }
    }

    if(! $ROOM->IsOpenCast()){
      foreach($vote_data->poison_cat as $array){ //ǭ���ν���
	$this_user = $USERS->ByUname($array['uname']);
	if($this_user->dead_flag) continue; //ľ���˻��Ǥ�����̵��

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
		if($this_new_target->IsDead() && ! $this_new_target->IsDummyBoy()){
		  $new_target_list[] = $this_new_target->uname;
		}
	      }
	      if(count($new_target_list) > 0){
		$this_target = $USERS->ByUname(GetRandom($new_target_list));
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
	  if($this_target->IsRole('poison_cat')) break; //ǭ���ʤ���������

	  $this_result = 'success';
	  $this_target->Revive(); //��������
	}while(false);

	if($this_result == 'failed') InsertSystemMessage($this_target->handle_name, 'REVIVE_FAILED');
	$sentence = $this_user->handle_name . "\t" . $this_target->handle_name . "\t" . $this_result;
	InsertSystemMessage($sentence, 'POISON_CAT_RESULT');
      }
    }
  }

  LoversFollowed(); //���͸��ɤ�����
  InsertMediumMessage(); //����Υ����ƥ��å�����
  if($ROOM->test_mode) return;

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

//��ɼ���ޥ�ɤ����äƤ��뤫�����å�
function CheckSituation($applay_situation){
  global $RQ_ARGS;

  if(is_array($applay_situation)){
    if(in_array($RQ_ARGS->situation, $applay_situation)) return;
  }
  elseif($RQ_ARGS->situation == $applay_situation) return;

  OutputVoteResult('̵������ɼ�Ǥ�');
}
?>
