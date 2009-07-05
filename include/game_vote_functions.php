<?php
require_once(dirname(__FILE__) . '/game_functions.php');

//��ɼ��̽���
function OutputVoteResult($str, $unlock = false, $reset_vote = false){
  global $back_url;

  if($reset_vote) DeleteVote(); //���ޤǤ���ɼ���������
  OutputActionResult('��Ͽ�ϵ�ʤ�䡩[��ɼ���]',
		     '<div align="center">' .
		     '<a name="#game_top"></a>' . $str . '<br>'."\n" .
		     $back_url . '</div>', '', $unlock);
}

//�Ϳ��ȥ����४�ץ����˱������򿦥ơ��֥���֤� (���顼�����ϻ���)
function GetRoleList($user_count, $option_role){
  global $GAME_CONF, $game_option;

  $error_header = '�����ॹ������[�������ꥨ�顼]��';
  $error_footer = '��<br>�����Ԥ��䤤��碌�Ʋ�������';

  $role_list = $GAME_CONF->role_list[$user_count]; //�Ϳ��˱���������ꥹ�Ȥ����
  if($role_list == NULL){ //�ꥹ�Ȥ�̵ͭ������å�
    OutputVoteResult($error_header . $user_count . '�ͤ����ꤵ��Ƥ��ޤ���' .
                     $error_footer, true, true);
  }

  //���Ǽ� (¼�ͣ� �� �ǣ���ϵ��)
  if(strpos($option_role, 'poison') !== false && $user_count >= $GAME_CONF->poison){
    $role_list['human'] -= 2;
    $role_list['poison']++;
    $role_list['wolf']++;
  }

  //���塼�ԥå� (14�ͤϥϡ��ɥ����� / ¼�ͣ� �� ���塼�ԥåɣ��������)
  if(strpos($option_role, 'cupid') !== false &&
     ($user_count == 14 || $user_count >= $GAME_CONF->cupid)){
    $role_list['human'] -= 2;
    $role_list['cupid']++;
    $role_list['medium']++;
  }

  //��ϵ (��ϵ �� ��ϵ)
  if(strpos($option_role, 'boss_wolf') !== false && $user_count >= $GAME_CONF->boss_wolf){
    $role_list['wolf']--; //�ޥ��ʥ��Υ����å����Ƥʤ��Τ����
    $role_list['boss_wolf']++;
  }

  if(strpos($game_option, 'quiz') !== false){  //������¼
    $temp_role_list = array();
    $temp_role_list['human'] = $role_list['human'];
    foreach($role_list as $key => $value){
      if($key == 'wolf' || $key == 'mad' || $key == 'common' || $key == 'fox'){
	$temp_role_list[$key] = (int)$value;
      }
      elseif($key != 'human'){
	$temp_role_list['human'] += (int)$value;
      }
    }
    $temp_role_list['human']--;
    $temp_role_list['quiz'] = 1;
    $role_list = $temp_role_list;
  }
  elseif(strpos($game_option, 'chaos') !== false){ //����
    if(strpos($game_option, 'chaosfull') !== false){ //��������
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
    }
    else{ //�̾����
      $wolf_count   = $role_list['wolf'] + $role_list['boss_wolf'];
      $fox_count    = $role_list['fox'] + $role_list['child_fox'];
      $lovers_count = $role_list['cupid'];

      $role_list = array(); //�����ꥻ�å�
    }
    //¼�ͿرĤοͿ��򻻽�
    $human_count = $user_count - $wolf_count - $fox_count - $lovers_count;

    //��ϵ�Ϥ���������
    $boss_wolf_count = 0; //��ϵ�οͿ�
    $base_count = ceil($user_count / 15); //�ü�ϵȽ�����򻻽�
    for(; $base_count > 0; $base_count--){
      if(mt_rand(1, 100) <= $user_count) $boss_wolf_count++; //���ÿͿ� % �γ�Ψ����ϵ�и�
    }
    if($boss_wolf_count > $wolf_count){ //ϵ�������Ķ�������ϵ�� 0 �ˤ���
      $role_list['boss_wolf'] = $wolf_count;
      $role_list['wolf'] = 0;
    }
    else{
      $role_list['boss_wolf'] = $boss_wolf_count;
      $role_list['wolf'] = $wolf_count - $boss_wolf_count;
    }

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
      if($user_count < 16){ //���͸���16��̤���ξ��Ϻ����ꤤ�դϽи����ʤ�
	$role_list['mage'] = $mage_count;
	$role_list['soul_mage'] = 0;
      }
      else{ //���ÿͿ� % �Ǻ����ꤤ�դ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['soul_mage'] = 1;
	$role_list['mage'] = $mage_count - (int)$role_list['soul_mage'];
      }
      $human_count -= $mage_count; //¼�ͿرĤλĤ�Ϳ�
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
      $role_list['medium'] = $medium_count;
      $human_count -= $medium_count; //¼�ͿرĤλĤ�Ϳ�
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
      $role_list['necromancer'] = $necromancer_count;
      $human_count -= $necromancer_count; //¼�ͿرĤλĤ�Ϳ�
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
      if($user_count < 16){ //���͸���16��̤���ξ��϶����ԤϽи����ʤ�
	$role_list['mad'] = $mad_count;
	$role_list['fanatic_mad'] = 0;
      }
      else{ //���ÿͿ� % �Ƕ����Ԥ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['fanatic_mad'] = 1;
	$role_list['mad'] = $mad_count - (int)$role_list['fanatic_mad'];
      }
      $human_count -= $mad_count; //¼�ͿرĤλĤ�Ϳ�
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
      if($user_count < 20){ //���͸���20��̤���ξ��ϵ��ΤϽи����ʤ�
	$role_list['guard'] = $guard_count;
	$role_list['poison_guard'] = 0;
      }
      else{ //���ÿͿ� % �ǵ��Τ���ͽи�
	if(mt_rand(1, 100) <= $user_count) $role_list['poison_guard'] = 1;
	$role_list['guard'] = $guard_count - (int)$role_list['poison_guard'];
      }
      $human_count -= $guard_count; //¼�ͿرĤλĤ�Ϳ�
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

    //�Կ��ԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 15){ //0:1 = 95:5
      if($rand <= 95) $suspect_count = 0;
      else $suspect_count = 1;
    }
    elseif($user_count < 19){ //0:1 = 85:15
      if($rand <= 85) $suspect_count = 0;
      else $suspect_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���20�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 20);
      if($rand <= 10) $suspect_count = $base_count - 1;
      elseif($rand <= 90) $suspect_count = $base_count;
      else $suspect_count = $base_count + 1;
    }

    //�Կ��Ԥ���������
    if($suspect_count > 0 && $human_count >= $suspect_count){
      $role_list['suspect'] = $suspect_count;
      $human_count -= $suspect_count; //¼�ͿرĤλĤ�Ϳ�
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

    //����ԤοͿ������
    $rand = mt_rand(1, 100); //�Ϳ����������
    if($user_count < 30){ //0:1 = 99:1
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }
    else{ //�ʸ塢���ÿͿ���30�������뤴�Ȥ� 1�ͤ�������
      $base_count = floor($user_count / 30) - 1;
      if($rand <= 99) $quiz_count = 0;
      else $quiz_count = 1;
    }

    //����Ԥ���������
    if($quiz_count > 0 && $human_count >= $quiz_count){
      $role_list['quiz'] = $quiz_count;
      $human_count -= $quiz_count; //¼�ͿرĤλĤ�Ϳ�
    }

    $role_list['human'] = $human_count; //¼�ͤοͿ�
  }

  if($role_list['human'] < 0){ //"¼��" �οͿ�������å�
    OutputVoteResult($error_header . '"¼��" �οͿ����ޥ��ʥ��ˤʤäƤޤ�' .
                     $error_footer, true, true);
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
    OutputVoteResult($error_header . '¼�� (' . $user_count . ') ������ο� (' . $role_count .
                     ') �����פ��Ƥ��ޤ���' . $error_footer, true, true);
  }

  return $now_role_list;
}

//�����귯���ʤ�ʤ��򿦤�����å�����
function CheckRole($role){
  return (strpos($role, 'wolf')   !== false ||
	  strpos($role, 'fox')    !== false ||
	  strpos($role, 'poison') !== false ||
	  strpos($role, 'cupid')  !== false);
}

//�򿦤οͿ����Υꥹ�Ȥ��������
function MakeRoleNameList($role_count_list){
  global $GAME_CONF;

  $sentence = '';
  foreach($GAME_CONF->main_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '��' . $value . $count;
  }
  foreach($GAME_CONF->sub_role_list as $key => $value){
    $count = (int)$role_count_list[$key];
    if($count > 0) $sentence .= '��(' . $value . $count . ')';
  }
  return $sentence;
}
?>
