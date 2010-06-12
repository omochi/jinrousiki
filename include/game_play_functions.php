<?php
//能力の種類とその説明を出力
function OutputAbility(){
  global $GAME_CONF, $MESSAGE, $ROLE_IMG, $ROOM, $USERS, $SELF;

  //ゲーム中のみ表示する
  if(! $ROOM->IsPlaying()) return false;

  if($SELF->IsDead()){ //死亡したら本人の能力を表示しない
    echo '<span class="ability ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    if($SELF->IsRole('mind_evoke')) $ROLE_IMG->Output('mind_evoke');
    return;
  }

  if($SELF->IsRole('human', 'saint', 'executor', 'suspect', 'unconscious')){ //村人系
    $ROLE_IMG->Output('human');
  }
  elseif($SELF->IsRole('elder')){ //長老
    $ROLE_IMG->Output($SELF->main_role);
  }
  elseif($SELF->IsRoleGroup('mage')){ //占い師系
    $ROLE_IMG->Output($SELF->IsRole('dummy_mage') ? 'mage' : $SELF->main_role);
    OutputSelfAbilityResult('MAGE_RESULT'); //占い結果を表示
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //夜の投票
  }
  elseif($SELF->IsRole('voodoo_killer')){ //陰陽師
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('VOODOO_KILLER_SUCCESS'); //占い結果を表示
    //夜の投票
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
  elseif($SELF->IsRoleGroup('necromancer')){ //霊能者系
    $ROLE_IMG->Output($SELF->IsRole('dummy_necromancer') ? 'necromancer' : $SELF->main_role);
    if(! $SELF->IsRole('yama_necromancer')){ //霊能結果を表示
      OutputSelfAbilityResult(strtoupper($SELF->main_role) . '_RESULT');
    }
  }
  elseif($SELF->IsRole('medium')){ //巫女
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('MEDIUM_RESULT'); //神託結果を表示
  }
  elseif($SELF->IsRoleGroup('priest')){ //司祭系
    $ROLE_IMG->Output($SELF->IsRole('crisis_priest') ? 'human' : $SELF->main_role);
    switch($SELF->main_role){
    case 'priest':
      OutputSelfAbilityResult('PRIEST_RESULT'); //神託結果を表示
      break;

    case 'bishop_priest':
      OutputSelfAbilityResult('BISHOP_PRIEST_RESULT'); //神託結果を表示
      break;

    case 'crisis_priest':
      OutputSelfAbilityResult('CRISIS_PRIEST_RESULT'); //神託結果を表示
      break;
    }
  }
  elseif($SELF->IsRoleGroup('guard')){ //狩人系
    $ROLE_IMG->Output($SELF->IsRole('dummy_guard') ? 'guard' : $SELF->main_role);
    OutputSelfAbilityResult('GUARD_SUCCESS'); //護衛結果を表示
    OutputSelfAbilityResult('GUARD_HUNTED'); //狩り結果を表示
    //夜の投票
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO');
  }
  elseif($SELF->IsRole('reporter')){ //ブン屋
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('REPORTER_SUCCESS'); //尾行結果を表示
    if($ROOM->date > 1 && $ROOM->IsNight()){ //夜の投票
      OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO');
    }
  }
  elseif($SELF->IsRole('anti_voodoo')){ //厄神
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('ANTI_VOODOO_SUCCESS'); //護衛結果を表示
    if($ROOM->date > 1 && $ROOM->IsNight()){ //夜の投票
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO');
    }
  }
  elseif($SELF->IsCommon()){ //共有者
    $ROLE_IMG->Output($SELF->IsRole('dummy_common') ? 'common' : $SELF->main_role);

    //仲間情報を取得
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
    OutputPartner($partner, 'common_partner'); //仲間を表示
  }
  elseif($SELF->IsRoleGroup('assassin')){ //暗殺者系
    $ROLE_IMG->Output($SELF->IsRole('eclipse_assassin') ? 'assassin' : $SELF->main_role);
    if($ROOM->date > 1 && $ROOM->IsNight()){ //夜の投票
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($SELF->IsRoleGroup('scanner')){ //さとり系
    $ROLE_IMG->Output($SELF->main_role);

    if($ROOM->date == 1){
      if($ROOM->IsNight()){ //初日夜の投票
	OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
      }
    }
    else{ //二日目以降、自分のサトラレ/口寄せを表示
      $target = array();
      $target_role = $SELF->IsRole('mind_scanner') ? 'mind_read' : 'mind_evoke';
      foreach($USERS->rows as $user){
	if($user->IsPartner($target_role, $SELF->user_no)) $target[] = $user->handle_name;
      }
      OutputPartner($target, 'mind_scanner_target');
    }
  }
  elseif($SELF->IsWolf()){ //人狼系
    $ROLE_IMG->Output($SELF->main_role);

    //仲間情報を収集
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
      OutputPartner($wolf_partner, 'wolf_partner'); //仲間を表示
      OutputPartner($mad_partner, 'mad_partner'); //囁き狂人を表示
    }
    if($ROOM->IsNight()){ //夜だけ無意識と紅狐を表示
      OutputPartner($unconscious_list, 'unconscious_list');
    }

    switch($SELF->main_role){
    case 'tongue_wolf':
      OutputSelfAbilityResult('TONGUE_WOLF_RESULT'); //噛み結果を表示
      break;

    case 'sex_wolf':
      OutputSelfAbilityResult('SEX_WOLF_RESULT'); //噛み結果を表示
      break;

    case 'possessed_wolf':
      //現在の憑依先を表示
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

    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //夜の投票
  }
  elseif($SELF->IsRoleGroup('mad')){ //狂人系
    $ROLE_IMG->Output($SELF->main_role);

    switch($SELF->main_role){
    case 'fanatic_mad': //狂信者
      //狼を表示
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)){
	  $wolf_partner[] = $USERS->GetHandleName($user->uname, true);
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
      break;

    case 'whisper_mad': //囁き狂人
      //狼と囁き狂人を表示
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

    case 'jammer_mad': //月兎
      if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
      break;

    case 'voodoo_mad': //呪術師
      if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
      break;

    case 'dream_eater_mad': //獏
      if($ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
      }
      break;

    case 'trap_mad': //罠師
      if($SELF->IsActive() && $ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
      }
      break;

    case 'possessed_mad': //犬神
      //現在の憑依先を表示
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
  elseif($SELF->IsFox()){ //妖狐系
    $ROLE_IMG->Output($SELF->main_role);

    if(! $SELF->IsRole('silver_fox', 'mind_lonely')){ //仲間表示
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsFox(true)){
	  $fox_partner[] = $user->handle_name;
	}
	elseif($user->IsChildFox() || $user->IsRole('scarlet_wolf')){
	  $child_fox_partner[] = $user->handle_name;
	}
      }
      OutputPartner($fox_partner, 'fox_partner'); //妖狐系
      OutputPartner($child_fox_partner, 'child_fox_partner'); //子狐系
    }

    if($SELF->IsChildFox()){ //子狐系
      OutputSelfAbilityResult('CHILD_FOX_RESULT'); //占い結果を表示
      if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //夜の投票
    }
    else{
      switch($SELF->main_role){
      case 'emerald_fox': //翠狐
	if($SELF->IsActive() && $ROOM->IsNight()){
	  OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
	}
	break;

      case 'voodoo_fox': //九尾
	if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
	break;

      case 'revive_fox': //仙狐
	if($ROOM->IsOpenCast()) break;
	OutputSelfAbilityResult('POISON_CAT_RESULT'); //蘇生結果を表示
	if($SELF->IsActive() && $ROOM->IsNight() && $ROOM->date > 1){ //夜の投票
	  OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
	}
	break;

      case 'possessed_fox': //憑狐
	//現在の憑依先を表示
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
      OutputSelfAbilityResult('FOX_EAT'); //襲撃メッセージを表示
    }
  }
  elseif($SELF->IsRoleGroup('chiroptera')){ //蝙蝠系
    if($SELF->IsRole('dummy_chiroptera')){
      $ROLE_IMG->Output('self_cupid');

      //自分が矢を打った(つもり)の恋人 (自分自身含む) を表示
      $dummy_lovers_id = $SELF->partner_list['dummy_chiroptera'];
      if(is_array($dummy_lovers_id)){
	$cupid_id = array($SELF->user_no, $dummy_lovers_id[0]);
	asort($cupid_id);
	$cupid_pair = array();
	foreach($cupid_id as $id) $cupid_pair[] = $USERS->ById($id)->handle_name;
	OutputPartner($cupid_pair, 'cupid_pair');
      }

      if($ROOM->IsNight() && $ROOM->date == 1){
	OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //初日夜の投票
      }
    }
    else{
      $ROLE_IMG->Output($SELF->main_role);
    }
  }
  elseif($SELF->IsRoleGroup('fairy')){ //妖精系
    $ROLE_IMG->Output($SELF->main_role);
    if($SELF->IsRole('mirror_fairy')){ //初日夜の投票
      if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('fairy-do', 'fairy_do', 'CUPID_DO');
    }
    else{
      if($ROOM->IsNight()) OutputVoteMessage('fairy-do', 'fairy_do', 'FAIRY_DO'); //夜の投票
    }
  }
  elseif($SELF->IsRoleGroup('cat')){ //猫又系
    $ROLE_IMG->Output($SELF->main_role);

    if(! $ROOM->IsOpenCast()){
      OutputSelfAbilityResult('POISON_CAT_RESULT'); //蘇生結果を表示
      if($ROOM->IsNight() && $ROOM->date > 1){ //夜の投票
	OutputVoteMessage('revive-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($SELF->IsRoleGroup('doll')){ //上海人形系
    $ROLE_IMG->Output($SELF->main_role);
    if(! $SELF->IsRole('doll_master')){ //仲間表示
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsRole('doll_master')){
	  $doll_master[] = $user->handle_name;
	}
	elseif($user->IsDoll()){
	  $doll_partner[] = $user->handle_name;
	}
      }
      OutputPartner($doll_master, 'doll_master_list'); //人形遣い
      if($SELF->IsRole('friend_doll')){
	OutputPartner($doll_partner, 'doll_partner'); //人形遣い
      }
    }
  }
  elseif($SELF->IsRole('incubate_poison')){ //潜毒者
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif($SELF->IsRole('chain_poison')){ //連毒者
    $ROLE_IMG->Output('human');
  }
  elseif($SELF->IsRoleGroup('poison')) $ROLE_IMG->Output('poison'); //埋毒者系
  elseif($SELF->IsRoleGroup('pharmacist')){ //薬師
    $ROLE_IMG->Output($SELF->main_role);
    OutputSelfAbilityResult('PHARMACIST_RESULT'); //鑑定結果を表示
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel')){ //キューピッド系
    $ROLE_IMG->Output($SELF->main_role);

    //自分が矢を打った恋人 (自分自身含む) を表示
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $SELF->user_no)){
	$cupid_pair[] = $user->handle_name;
      }
    }
    OutputPartner($cupid_pair, 'cupid_pair');

    if($SELF->IsRole('ark_angel') && $ROOM->date == 2) OutputSelfAbilityResult('SYMPATHY_RESULT');
    //初日夜の投票
    if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO');
  }
  elseif($SELF->IsRoleGroup('jealousy')) $ROLE_IMG->Output($SELF->main_role); //橋姫
  elseif($SELF->IsRole('quiz')){ //出題者
    $ROLE_IMG->Output($SELF->main_role);
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->Output('quiz_chaos');
  }
  elseif($SELF->IsRoleGroup('mania')){ //神話マニア
    $ROLE_IMG->Output($SELF->main_role);
    //初日夜の投票
    if($ROOM->IsNight() && $ROOM->date == 1) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO');
  }

  //-- ここから兼任役職 --//
  $fix_display_list = array(); //常時表示する役職リスト

  //元神話マニアのコピー結果を表示
  if($SELF->IsRole('copied')) OutputSelfAbilityResult('MANIA_RESULT');
  $fix_display_list[] = 'copied';

  //能力喪失 (舌禍狼、罠師)
  if($SELF->IsRole('lost_ability')) $ROLE_IMG->Output('lost_ability');
  $fix_display_list[] = 'lost_ability';

  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){ //恋人
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

  //ここからは憑依先の役職を表示
  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  if($virtual_self->IsRole('mind_open')) $ROLE_IMG->Output('mind_open');
  $fix_display_list[] = 'mind_open';

  if($ROOM->date > 1){ //サトラレ系の表示は 2 日目以降
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

  //これ以降はサブ役職非公開オプションの影響を受ける
  if($ROOM->IsOption('secret_sub_role')) return;

  $role_keys_list    = array_keys($GAME_CONF->sub_role_list);
  $hide_display_list = array('decide', 'plague', 'good_luck', 'bad_luck');
  $not_display_list  = array_merge($fix_display_list, $hide_display_list);
  $display_list      = array_diff($role_keys_list, $not_display_list);
  $target_list       = array_intersect($display_list, array_slice($virtual_self->role_list, 1));

  foreach($target_list as $role) $ROLE_IMG->Output($role);
}

//仲間を表示する
function OutputPartner($partner_list, $header, $footer = NULL){
  global $ROLE_IMG;

  if(count($partner_list) < 1) return false; //仲間がいなければ表示しない

  $str = '<table class="ability-partner"><tr>'."\n" .
    '<td>' . $ROLE_IMG->Generate($header) . '</td>'."\n" .
    '<td>　' . implode('さん　', $partner_list) . 'さん　</td>'."\n";
  if($footer) $str .= '<td>' . $ROLE_IMG->Generate($footer) . '</td>'."\n";
  echo $str . '</tr></table>'."\n";
}

//個々の能力発動結果を表示する
/*
  一部の処理は、HN にタブが入るとパースに失敗する
  入村時に HN からタブを除く事で対応できるが、
  そもそもこのようなパースをしないといけない DB 構造に
  問題があるので、ここでは特に対応しない
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
	OutputAbilityResult($header, $target . ' さんは ' . $wolf_handle, $footer);
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

//能力発動結果を表示する
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if(isset($header)) echo '<td>' . $ROLE_IMG->Generate($header) . '</td>'."\n";
  if(isset($target)) echo '<td>' . $target . '</td>'."\n";
  if(isset($footer)) echo '<td>' . $ROLE_IMG->Generate($footer) . '</td>'."\n";
  echo '</tr></table>'."\n";
}

//夜の未投票メッセージ出力
function OutputVoteMessage($class, $sentence, $situation, $not_situation = ''){
  global $MESSAGE, $ROOM;

  //投票済みならメッセージを表示しない
  if(! $ROOM->test_mode && CheckSelfVoteNight($situation, $not_situation)) return false;

  $message_str = 'ability_' . $sentence;
  echo '<span class="ability ' . $class . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}
