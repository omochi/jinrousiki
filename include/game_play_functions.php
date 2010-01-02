<?php
//能力の種類とその説明を出力
function OutputAbility(){
  global $GAME_CONF, $ROLE_IMG, $MESSAGE, $ROOM, $USERS, $SELF;

  //ゲーム中のみ表示する
  if(! $ROOM->IsPlaying()) return false;

  if($SELF->IsDead()){ //死亡したら能力を表示しない
    echo '<span class="ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
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

    if($ROOM->date > 1){
      //自分が心を読んでいる相手を表示
      foreach($USERS->rows as $user){
	if($user->IsRole('mind_read') && in_array($SELF->user_no, $user->partner_list['mind_read'])){
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
	$wolf_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('whisper_mad')){
	$mad_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious')){
	$unconscious_list[] = $user->handle_name;
      }
    }
    if(! $SELF->IsRole('silver_wolf')){
      OutputPartner($wolf_partner, 'wolf_partner'); //仲間を表示
      OutputPartner($mad_partner, 'mad_partner'); //囁き狂人を表示
    }
    if($ROOM->IsNight()) OutputPartner($unconscious_list, 'unconscious_list'); //夜だけ無意識を表示

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

    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //夜の投票
  }
  elseif($SELF->IsRoleGroup('mad')){ //狂人系
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($SELF->IsRole('fanatic_mad')){ //狂信者
      //狼を表示
      foreach($USERS->rows as $user){
	if($user->IsWolf(true)) $wolf_partner[] = $user->handle_name;
      }
      OutputPartner($wolf_partner, 'wolf_partner');
    }
    elseif($SELF->IsRole('whisper_mad')){ //囁き狂人
      //狼と囁き狂人を表示
      foreach($USERS->rows as $user){
	if($user->IsSelf() || $user->IsRole('silver_wolf')) continue;
	if($user->IsWolf()){
	  $wolf_partner[] = $user->handle_name;
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
      OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
    }
    elseif($SELF->IsActiveRole('trap_mad') && $is_after_first_night){ //罠師
      OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
  elseif($SELF->IsFox()){ //妖狐系
    $ROLE_IMG->DisplayImage($SELF->main_role);

    foreach($USERS->rows as $user){
      if($user->IsSelf() || $user->IsRole('silver_fox')) continue;
      if($user->IsRole('child_fox')){
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

    if($SELF->IsRole('fox', 'cursed_fox', 'voodoo_fox')){
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
  elseif($SELF->IsRoleGroup('chiroptera')) $ROLE_IMG->DisplayImage($SELF->main_role); //蝙蝠系
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
    $sql = GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($SELF->handle_name == $actor){
	OutputAbilityResult(NULL, $target, 'pharmacist_success');
	break;
      }
    }
  }
  elseif($SELF->IsRoleGroup('cupid')){ //キューピッド系
    $ROLE_IMG->DisplayImage($SELF->main_role);

    //自分が矢を打った恋人 (自分自身含む) を表示
    foreach($USERS->rows as $user){
      if($user->IsLovers() && in_array($SELF->user_no, $user->partner_list['lovers'])){
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
  elseif($SELF->IsRole('mania')){ //神話マニア
    $ROLE_IMG->DisplayImage($SELF->main_role);
    if($is_first_night) OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO'); //初日夜の投票
  }

  //-- ここから兼任役職 --//
  $fix_display_list = array('lost_ability', 'mind_open'); //常時表示する役職リスト
  foreach($fix_display_list as $this_role){
    if($SELF->IsRole($this_role)) $ROLE_IMG->DisplayImage($this_role);
  }

  if($SELF->IsLovers()){ //恋人
    foreach($USERS->rows as $user){
      if($user->IsLovers() && ! $user->IsSelf() &&
	 (count(array_intersect($SELF->partner_list['lovers'], $user->partner_list['lovers'])) > 0)){
	$lovers_partner[] = $user->handle_name;
      }
    }
    OutputPartner($lovers_partner, 'lovers_header', 'lovers_footer');
  }
  $fix_display_list[] = 'lovers';

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

  if($ROOM->date > 1){ //サトラレ系の表示は 2 日目以降
    if($SELF->IsRole('mind_read')) $ROLE_IMG->DisplayImage('mind_read');

    if($SELF->IsRole('mind_receiver')){
      $ROLE_IMG->DisplayImage('mind_receiver');

      $mind_scanner_target = array();
      foreach($SELF->partner_list['mind_receiver'] as $this_no){
	$mind_scanner_target[] = $USERS->ById($this_no)->handle_name;
      }
      OutputPartner($mind_scanner_target, 'mind_scanner_target');
    }
  }
  array_push($fix_display_list, 'mind_read', 'mind_receiver');

  //これ以降はサブ役職非公開オプションの影響を受ける
  if($ROOM->IsOption('secret_sub_role')) return;

  $role_keys_list    = array_keys($GAME_CONF->sub_role_list);
  $hide_display_list = array('decide', 'plague', 'good_luck', 'bad_luck');
  $not_display_list  = array_merge($fix_display_list, $hide_display_list);
  $display_list      = array_diff($role_keys_list, $not_display_list);
  $target_list       = array_intersect($display_list, array_slice($SELF->role_list, 1));

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

  if(! $ROOM->test_mode){
    //投票済みならメッセージを表示しない
    if(CheckSelfVoteNight($situation, $not_situation)) return false;
  }

  $class_str   = 'ability-' . $class; //クラス名はアンダースコアを使わないでおく
  $message_str = 'ability_' . $sentence;
  echo '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}
?>
