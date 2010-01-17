<?php
//能力の種類とその説明を出力
function OutputAbility(){
  global $GAME_CONF, $ROLE_IMG, $MESSAGE, $ROOM, $USERS, $SELF;

  //ゲーム中のみ表示する
  if(! $ROOM->IsPlaying()) return false;

  if($SELF->IsDead()){ //死亡したら能力を表示しない
    echo '<span class="ability ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  $is_first_night = ($ROOM->IsNight() && $ROOM->date == 1);
  $is_after_first_night = ($ROOM->IsNight() && $ROOM->date > 1);

  if($SELF->IsRole('human', 'suspect', 'unconscious')){ //村人・不審者・無意識
    $ROLE_IMG->DisplayImage('human');
  }
  elseif($SELF->IsRoleGroup('mage')){ //占い系
    $ROLE_IMG->DisplayImage($SELF->IsRole('dummy_mage') ? 'mage' : $SELF->main_role);

    //占い結果を表示
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

    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //夜の投票
  }
  elseif($SELF->IsRole('voodoo_killer')){ //陰陽師
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //護衛結果を表示
    $sql = GetAbilityActionResult('VOODOO_KILLER_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'voodoo_killer_success');
	break;
      }
    }

    //夜の投票
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }
  elseif($SELF->IsRole('yama_necromancer')) $ROLE_IMG->DisplayImage($SELF->main_role); //閻魔
  elseif($SELF->IsRoleGroup('necromancer') || $SELF->IsRole('medium')){ //霊能系
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

    //判定結果を表示
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif($SELF->IsRole('priest')){ //司祭
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //判定結果を表示
    $sql = GetAbilityActionResult('PRIEST_RESULT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      $result = mysql_result($sql, $i, 0);
      OutputAbilityResult('priest_header', $result, 'priest_footer');
    }
  }
  elseif($SELF->IsRoleGroup('guard')){ //狩人系
    $ROLE_IMG->DisplayImage($SELF->IsRole('dummy_guard') ? 'guard' : $SELF->main_role);

    //護衛結果を表示
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

    if($is_after_first_night) OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO'); //夜の投票
  }
  elseif($SELF->IsRole('reporter')){ //ブン屋
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //尾行結果を表示
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($SELF->handle_name == $actor){
	$target .= ' さんは ' . $wolf_handle;
	OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
	break;
      }
    }

    if($is_after_first_night) OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO'); //夜の投票
  }
  elseif($SELF->IsRole('anti_voodoo')){ //厄神
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //護衛結果を表示
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
      OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO'); //夜の投票
    }
  }
  elseif($SELF->IsRoleGroup('common')){ //共有者
    $ROLE_IMG->DisplayImage('common');

    //仲間を表示
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if(($SELF->IsRole('common') && $user->IsRole('common')) ||
	 ($SELF->IsRole('dummy_common') && $user->IsDummyBoy())){
	$common_partner[] = $user->handle_name;
      }
    }
    OutputPartner($common_partner, 'common_partner');
  }
  elseif($SELF->IsRole('assassin')){ //暗殺者
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($is_after_first_night){ //夜の投票
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }
  elseif($SELF->IsRole('mind_scanner')){ //さとり
    $ROLE_IMG->DisplayImage($SELF->main_role);

    if($ROOM->date > 1){ //2日目以降、自分が心を読んでいる相手を表示
      foreach($USERS->rows as $user){
	if($user->IsPartner('mind_read', $SELF->user_no)){
	  $mind_scanner_target[] = $user->handle_name;
	}
      }
      OutputPartner($mind_scanner_target, 'mind_scanner_target');
    }

    if($is_first_night){ //初日夜の投票
      OutputVoteMessage('mind-scanner-do', 'mind_scanner_do', 'MIND_SCANNER_DO');
    }
  }
  elseif($SELF->IsRoleGroup('jealousy')) $ROLE_IMG->DisplayImage($SELF->main_role); //橋姫系
  elseif($SELF->IsWolf()){ //人狼系
    $ROLE_IMG->DisplayImage($SELF->main_role);

    foreach($USERS->rows as $user){ //仲間情報を収集
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
      OutputPartner($wolf_partner, 'wolf_partner'); //仲間を表示
      OutputPartner($mad_partner, 'mad_partner'); //囁き狂人を表示
    }
    if($ROOM->IsNight()){ //夜だけ無意識と紅狐を表示
      OutputPartner($unconscious_list, 'unconscious_list');
    }

    if($SELF->IsRole('tongue_wolf')){ //舌禍狼の噛み結果を表示
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

    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //夜の投票
  }
  elseif($SELF->IsRoleGroup('mad')){ //狂人系
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($SELF->IsRole('fanatic_mad')){ //狂信者
      //狼を表示
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)){
	  $wolf_partner[] = $USERS->GetVirtualHandleName($user->uname);
	}
      }
      OutputPartner($wolf_partner, 'wolf_partner');
    }
    elseif($SELF->IsRole('whisper_mad')){ //囁き狂人
      //狼と囁き狂人を表示
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
    elseif($SELF->IsRole('jammer_mad') && $ROOM->IsNight()){ //月兎
      OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
    }
    elseif($SELF->IsRole('voodoo_mad') && $ROOM->IsNight()){ //呪術師
      OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
    }
    elseif($SELF->IsRole('dream_eater_mad') && $ROOM->IsNight()){ //獏
      if($is_after_first_night) OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
    }
    elseif($SELF->IsActiveRole('trap_mad') && $is_after_first_night){ //罠師
      OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
  elseif($SELF->IsFox()){ //妖狐系
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
      OutputPartner($fox_partner, 'fox_partner'); //妖狐の仲間を表示
      OutputPartner($child_fox_partner, 'child_fox_partner'); //子狐の仲間を表示
    }

    if($SELF->IsRole('child_fox')){
      //占い結果を表示
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

      if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //夜の投票
    }
    elseif($SELF->IsRole('voodoo_fox') && $ROOM->IsNight()){ //九尾
      OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
    }

    if(! $SELF->IsRole('white_fox', 'poison_fox', 'child_fox')){
      //狐が狙われたメッセージを表示
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
  elseif($SELF->IsRoleGroup('chiroptera')){ //蝙蝠系
    if($SELF->IsRole('dummy_chiroptera')){
      $ROLE_IMG->DisplayImage('self_cupid');

      //自分が矢を打った(つもり)の恋人 (自分自身含む) を表示
      $dummy_lovers_id = $SELF->partner_list['dummy_chiroptera'];
      if(is_array($dummy_lovers_id)){
	$cupid_id = array($SELF->user_no, $dummy_lovers_id[0]);
	asort($cupid_id);
	$cupid_pair = array();
	foreach($cupid_id as $id) $cupid_pair[] = $USERS->ById($id)->handle_name;
	OutputPartner($cupid_pair, 'cupid_pair');
      }

      if($is_first_night) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //初日夜の投票
    }
    else{
      $ROLE_IMG->DisplayImage($SELF->main_role);
    }
  }
  elseif($SELF->IsRole('incubate_poison')){ //潜毒者
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL);
  }
  elseif($SELF->IsRole('poison_cat')){ //猫又
    $ROLE_IMG->DisplayImage($SELF->main_role);

    if(! $ROOM->IsOpenCast()){
      //蘇生結果を表示
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

      if($is_after_first_night){ //夜の投票
	OutputVoteMessage('poison-cat-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }
  elseif($SELF->IsRoleGroup('poison')) $ROLE_IMG->DisplayImage('poison'); //埋毒者系
  elseif($SELF->IsRole('pharmacist')){ //薬師
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //解毒結果を表示
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
  elseif($SELF->IsRoleGroup('cupid')){ //キューピッド系
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //自分が矢を打った恋人 (自分自身含む) を表示
    foreach($USERS->rows as $user){
      if($user->IsPartner('lovers', $SELF->user_no)){
	$cupid_pair[] = $user->handle_name;
      }
    }
    OutputPartner($cupid_pair, 'cupid_pair');

    if($is_first_night) OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //初日夜の投票
  }
  elseif($SELF->IsRole('quiz')){ //出題者
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($ROOM->IsOptionGroup('chaos')) $ROLE_IMG->DisplayImage('quiz_chaos');
  }
  elseif($SELF->IsRoleGroup('mania')){ //神話マニア
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($is_first_night) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO'); //初日夜の投票
  }

  //-- ここから兼任役職 --//
  $fix_display_list = array(); //常時表示する役職リスト

  if($SELF->IsRole('copied')){ //元神話マニア
    //コピー結果を表示
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

  //能力喪失 (舌禍狼、罠師)
  if($SELF->IsRole('lost_ability')) $ROLE_IMG->DisplayImage('lost_ability');
  $fix_display_list[] = 'lost_ability';

  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){ //恋人
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

  //ここからは憑依先の役職を表示
  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  if($virtual_self->IsRole('mind_open')) $ROLE_IMG->DisplayImage('mind_open');
  $fix_display_list[] = 'mind_open';

  if($ROOM->date > 1){ //サトラレ系の表示は 2 日目以降
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

  //これ以降はサブ役職非公開オプションの影響を受ける
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

//仲間を表示する
function OutputPartner($partner_list, $header, $footer = NULL){
  global $ROLE_IMG;

  if(count($partner_list) < 1) return false; //仲間がいなければ表示しない

  $str = '<table class="ability-partner"><tr>'."\n" .
    '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n" . '<td>　';
  foreach($partner_list as $partner) $str .= $partner . 'さん　　';
  $str .= '</td>'."\n";
  if($footer) $str .= '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
  echo $str . '</tr></table>'."\n";
}

//能力発動結果をデータベースに問い合わせる
function GetAbilityActionResult($action){
  global $ROOM;

  $yesterday = $ROOM->date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = {$ROOM->id}
			AND date = $yesterday AND type = '$action'");
}

//能力発動結果を表示する
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td>' . $ROLE_IMG->GenerateTag($header) . '</td>'."\n";
  if($target) echo '<td>' . $target . '</td>'."\n";
  if($footer) echo '<td>' . $ROLE_IMG->GenerateTag($footer) . '</td>'."\n";
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
?>
