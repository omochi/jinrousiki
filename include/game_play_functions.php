<?php
//能力の種類とその説明を出力
function OutputAbility(){
  global $MESSAGE, $ROLE_DATA, $ROLE_IMG, $ROOM, $ROLES, $USERS, $SELF;

  if(! $ROOM->IsPlaying()) return false; //ゲーム中のみ表示する

  if($SELF->IsDead()){ //死亡したら口寄せ以外は表示しない
    echo '<span class="ability ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    if($SELF->IsRole('mind_evoke')) $ROLE_IMG->Output('mind_evoke');
    return;
  }

  $ROLES->LoadMain($SELF)->OutputAbility(); //メイン役職

  //-- ここからサブ役職 --//
  $fix_display_list = array(); //常時表示する役職リスト

  //元神話マニア系
  if(($ROOM->date == 2 && $SELF->IsRole('copied', 'copied_trick', 'copied_basic')) ||
     ($ROOM->date == 4 && $SELF->IsRole('copied_soul', 'copied_teller'))){
    OutputSelfAbilityResult('MANIA_RESULT'); //コピー結果
  }
  array_push($fix_display_list, 'copied', 'copied_trick', 'copied_basic', 'copied_soul',
	     'copied_teller');

  $role = 'lost_ability'; //能力喪失 (比丘尼は別画像)
  if($SELF->IsRole($role)){
    $ROLE_IMG->Output($SELF->IsRole('awake_wizard') ? 'ability_awake_wizard' : $role);
  }
  $fix_display_list[] = $role;

  $role = 'muster_ability'; //能力発現
  if($SELF->IsRole($role)) $ROLE_IMG->Output($role);
  $fix_display_list[] = $role;

  //恋人系・悲恋人
  $role = 'lovers'; //恋人
  if($SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera', 'sweet_status')){
    //悲恋人のみの場合、2 日目以降はシーク不要なのでスキップ
    if($ROOM->date == 1 || $SELF->IsLovers() || $SELF->IsRole('dummy_chiroptera')){
      $stack = array();
      foreach($USERS->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsPartner($role, $SELF->partner_list) ||
	   $SELF->IsPartner('dummy_chiroptera', $user->user_no) ||
	   ($ROOM->date == 1 && $user->IsPartner('sweet_status', $SELF->partner_list))){
	  $stack[] = $USERS->GetHandleName($user->uname, true); //憑依を追跡する
	}
      }
      OutputPartner($stack, 'partner_header', 'lovers_footer');
    }
    $role = 'sweet_status';
    if($ROOM->date == 2 && $SELF->IsRole($role)) $ROLE_IMG->Output($role);
  }

  $role = 'challenge_lovers'; //難題
  if($ROOM->date > 1 && $SELF->IsRole($role)) $ROLE_IMG->Output($role);

  $role = 'possessed_exchange'; //交換憑依
  if($SELF->IsRole($role)){
    do{ //現在の憑依先を表示
      if(! is_array($stack = $SELF->GetPartner($role))) break;
      if(is_null($target = $USERS->ByID(array_shift($stack))->handle_name)) break;
      $ROOM->date < 3 ?
	OutputAbilityResult('exchange_header', $target, 'exchange_footer') :
	OutputAbilityResult('partner_header', $SELF->handle_name, 'possessed_target');
    }while(false);
  }
  array_push($fix_display_list, 'lovers', 'challenge_lovers', 'possessed_exchange', 'sweet_status');

  //ジョーカー系
  $role = 'joker'; //ジョーカー
  if($SELF->IsJoker($ROOM->date)) $ROLE_IMG->Output($role);

  $role = 'rival'; //宿敵
  if($SELF->IsRival()){
    $stack = array();
    foreach($USERS->rows as $user){
      if(! $user->IsSelf() && $user->IsPartner($role, $SELF->partner_list)){
	$stack[] = $user->handle_name; //憑依は追跡しない
      }
    }
    OutputPartner($stack, 'partner_header', 'rival_footer');
  }
  array_push($fix_display_list, 'joker', 'rival');

  $role = 'death_note'; //デスノート
  if($SELF->IsDoomRole($role)){
    $ROLE_IMG->Output($role);
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('death-note-do', 'death_note_do', 'DEATH_NOTE_DO', 'DEATH_NOTE_NOT_DO');
    }
  }
  $fix_display_list[] = 'death_note';

  //-- ここからは憑依先の役職を表示 --//
  $virtual_self = $USERS->ByVirtual($SELF->user_no);

  //期間限定表示タイプ (オシラ遊び・特殊小心者・権力者系)
  $role = 'death_selected'; //オシラ遊び
  if($virtual_self->IsDoomRole($role)) $ROLE_IMG->Output($role);

  //熱病・凍傷
  foreach(array('febris' => 'sudden_death', 'frostbite' => 'frostbite') as $role => $footer){
    if($virtual_self->IsDoomRole($role)){
      OutputAbilityResult($role . '_header', $ROOM->date, $footer . '_footer');
    }
  }

  $role = 'death_warrant'; //死の宣告
  if($virtual_self->IsRole($role) &&
     ($date = $virtual_self->GetDoomDate($role)) >= $ROOM->date){
    OutputAbilityResult('death_warrant_header', $date, 'sudden_death_footer');
  }

  $role = 'day_voter'; //一日村長
  if($virtual_self->IsDoomRole($role)) $ROLE_IMG->Output($role);
  array_push($fix_display_list, 'death_selected', 'febris', 'frostbite', 'death_warrant',
	     'day_voter');

  //特殊権力・雑草魂系
  if($ROOM->date > 1){ //表示は 2 日目以降
    foreach(array('wirepuller_luck', 'occupied_luck') as $role){ //入道・ひんな持ち
      if($virtual_self->IsRole($role)) $ROLE_IMG->Output($role);
    }
  }
  array_push($fix_display_list, 'wirepuller_luck', 'occupied_luck');

  //サトラレ系
  $role = 'mind_open'; //公開者
  if($virtual_self->IsRole($role)) $ROLE_IMG->Output($role);

  if($ROOM->date > 1){ //サトラレ系の表示は 2 日目以降
    foreach(array('mind_read', 'mind_evoke', 'mind_lonely') as $role){ //サトラレ・口寄せ・はぐれ者
      if($virtual_self->IsRole($role)) $ROLE_IMG->Output($role);
    }

    $role = 'mind_receiver'; //受信者
    if($virtual_self->IsRole($role)){
      $ROLE_IMG->Output($role);

      $stack = array();
      foreach($virtual_self->GetPartner($role, true) as $id){
	$stack[$id] = $USERS->ById($id)->handle_name;
      }
      ksort($stack);
      OutputPartner($stack, 'mind_scanner_target');
      unset($stack);
    }

    $role = 'mind_friend'; //共鳴者
    if($virtual_self->IsRole($role)){
      $ROLE_IMG->Output($role);

      $stack = array();
      foreach($USERS->rows as $user){
	if(! $user->IsSame($virtual_self->uname) &&
	   $user->IsPartner($role, $virtual_self->partner_list)){
	  $stack[$user->user_no] = $user->handle_name;
	}
      }
      ksort($stack);
      OutputPartner($stack, 'mind_friend_list');
      unset($stack);
    }

    $role = 'mind_sympathy'; //共感者
    if($virtual_self->IsRole($role)){
      $ROLE_IMG->Output($role);
      if($ROOM->date == 2) OutputSelfAbilityResult('SYMPATHY_RESULT');
    }

    $role = 'mind_sheep'; //羊
    if($virtual_self->IsRole($role)){
      $ROLE_IMG->Output($role);

      $stack = array();
      foreach($virtual_self->GetPartner($role, true) as $id){
	$stack[$id] = $USERS->ById($id)->handle_name;
      }
      ksort($stack);
      OutputPartner($stack, 'shepherd_patron_list');
      unset($stack);
    }

    $role = 'mind_presage'; //受託者
    if($ROOM->date > 2 && $virtual_self->IsRole($role)) OutputSelfAbilityResult('PRESAGE_RESULT');
  }
  array_push($fix_display_list, 'mind_read', 'mind_open', 'mind_receiver', 'mind_friend',
	     'mind_sympathy', 'mind_evoke', 'mind_presage', 'mind_lonely', 'mind_sheep');

  //鬼火系
  foreach(array('wisp', 'black_wisp', 'spell_wisp', 'foughten_wisp', 'gold_wisp') as $role){
    if($virtual_self->IsRole($role)) $ROLE_IMG->Output($role);
  }
  if($ROOM->date > 1){
    $role = 'sheep_wisp'; //羊皮
    if($virtual_self->IsDoomRole($role)) $ROLE_IMG->Output($role);
  }
  array_push($fix_display_list, 'wisp', 'black_wisp', 'spell_wisp', 'foughten_wisp', 'gold_wisp',
	     'sheep_wisp');

  //-- これ以降はサブ役職非公開オプションの影響を受ける --//
  if($ROOM->IsOption('secret_sub_role')) return;

  array_push(
    $fix_display_list, 'decide', 'plague', 'counter_decide', 'dropout', 'good_luck', 'bad_luck',
    'critical_voter', 'critical_luck', 'enemy', 'supported', 'infected', 'psycho_infected',
    'possessed_target', 'possessed', 'bad_status', 'protected','changed_therian');
  $display_list = array_diff(array_keys($ROLE_DATA->sub_role_list), $fix_display_list);
  $target_list  = array_intersect($display_list, array_slice($virtual_self->role_list, 1));
  foreach($target_list as $role) $ROLE_IMG->Output($role);
}

//仲間を表示する
function OutputPartner($list, $header, $footer = NULL){
  global $ROLE_IMG;

  if(count($list) < 1) return false; //仲間がいなければ表示しない
  $list[] = '</td>';
  $str = '<table class="ability-partner"><tr>'."\n" .
    $ROLE_IMG->Generate($header, NULL, true) ."\n" .
    '<td>　' . implode('さん　', $list) ."\n";
  if($footer) $str .= $ROLE_IMG->Generate($footer, NULL, true) ."\n";
  echo $str . '</tr></table>'."\n";
}

//現在の憑依先を表示する
function OutputPossessedTarget(){
  global $USERS, $SELF;

  $type = 'possessed_target';
  if(is_null($stack = $SELF->GetPartner($type))) return;

  $target = $USERS->ByID($stack[max(array_keys($stack))])->handle_name;
  if($target != '') OutputAbilityResult('partner_header', $target, $type);
}

//個々の能力発動結果を表示する
/*
  一部の処理は、HN にタブが入るとパースに失敗する
  入村時に HN からタブを除く事で対応できるが、
  そもそもこのようなパースをしないといけない DB 構造に
  問題があるので、ここでは特に対応しない
*/
function OutputSelfAbilityResult($action){
  global $RQ_ARGS, $ROOM, $SELF;

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
  case 'SOUL_NECROMANCER_RESULT':
  case 'PSYCHO_NECROMANCER_RESULT':
  case 'EMBALM_NECROMANCER_RESULT':
  case 'ATTEMPT_NECROMANCER_RESULT':
  case 'DUMMY_NECROMANCER_RESULT':
  case 'SPIRITISM_WIZARD_RESULT':
    $type = 'necromancer';
    break;

  case 'EMISSARY_NECROMANCER_RESULT':
    $type = 'priest';
    $header = 'emissary_necromancer_header';
    $footer = 'priest_footer';
    break;

  case 'MEDIUM_RESULT':
    $type = 'necromancer';
    $header = 'medium';
    break;

  case 'PRIEST_RESULT':
  case 'DUMMY_PRIEST_RESULT':
  case 'PRIEST_JEALOUSY_RESULT':
    $type = 'priest';
    $header = 'priest_header';
    $footer = 'priest_footer';
    break;

  case 'BISHOP_PRIEST_RESULT':
    $type = 'priest';
    $header = 'bishop_priest_header';
    $footer = 'priest_footer';
    break;

  case 'DOWSER_PRIEST_RESULT':
    $type = 'priest';
    $header = 'dowser_priest_header';
    $footer = 'dowser_priest_footer';
    break;

  case 'WEATHER_PRIEST_RESULT':
    $type = 'weather_priest';
    $header = 'weather_priest_header';
    break;

  case 'BORDER_PRIEST_RESULT':
    $type = 'mage';
    $header = 'border_priest_header';
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

  case 'POISON_CAT_RESULT':
    $type = 'mage';
    $footer = 'poison_cat_';
    break;

  case 'PHARMACIST_RESULT':
    $type = 'mage';
    $footer = 'pharmacist_';
    break;

  case 'ASSASSIN_RESULT':
    $type = 'mage';
    $header = 'assassin_result';
    break;

  case 'CLAIRVOYANCE_RESULT':
    $type = 'reporter';
    $header = 'clairvoyance_result_header';
    $footer = 'clairvoyance_result_footer';
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

  case 'VAMPIRE_RESULT':
    $type = 'mage';
    $header = 'vampire_result';
    break;

  case 'MANIA_RESULT':
  case 'PATRON_RESULT':
    $type = 'mage';
    break;

  case 'SYMPATHY_RESULT':
    $type = 'sympathy';
    $header = 'sympathy_result';
    break;

  case 'PRESAGE_RESULT':
    $type = 'reporter';
    $header = 'presage_result_header';
    $footer = 'reporter_result_footer';
    break;

  default:
    return false;
  }

  $target_date = $ROOM->date - 1;
  if($ROOM->test_mode){
    $stack = $RQ_ARGS->TestItems->system_message[$target_date];
    $stack = array_key_exists($action, $stack) ? $stack[$action] : NULL;
    $result_list = is_array($stack) ? $stack : array();
  }
  else{
    $query = 'SELECT DISTINCT message FROM system_message WHERE room_no = ' .
      "{$ROOM->id} AND date = {$target_date} AND type = '{$action}'";
    $result_list = FetchArray($query);
  }
  //PrintData($result_list);

  switch($type){
  case 'mage':
    foreach($result_list as $result){
      list($actor, $target, $data) = explode("\t", $result);
      if($SELF->IsSameName($actor)) OutputAbilityResult($header, $target, $footer . $data);
    }
    break;

  case 'necromancer':
    if(is_null($header)) $header = 'necromancer';
    foreach($result_list as $result){
      list($target, $data) = explode("\t", $result);
      OutputAbilityResult($header . '_result', $target, $footer . $data);
    }
    break;

  case 'priest':
    foreach($result_list as $result) OutputAbilityResult($header, $result, $footer);
    break;

  case 'weather_priest':
    foreach($result_list as $result) OutputAbilityResult($header, NULL, $result);
    break;

  case 'crisis_priest':
    foreach($result_list as $result) OutputAbilityResult($header . $result, NULL, $footer);
    break;

  case 'guard':
    foreach($result_list as $result){
      list($actor, $target) = explode("\t", $result);
      if($SELF->IsSameName($actor)) OutputAbilityResult(NULL, $target, $footer);
    }
    break;

  case 'reporter':
    foreach($result_list as $result){
      list($actor, $target, $wolf) = explode("\t", $result);
      if($SELF->IsSameName($actor)){
	OutputAbilityResult($header, $target . ' さんは ' . $wolf, $footer);
      }
    }
    break;

  case 'fox':
    foreach($result_list as $result){
      if($SELF->IsSameName($result)) OutputAbilityResult($header, NULL);
    }
    break;

  case 'sympathy':
    foreach($result_list as $result){
      list($actor, $target, $data) = explode("\t", $result);
      if($SELF->IsSameName($actor) || $SELF->IsRole('ark_angel')){
	OutputAbilityResult($header, $target, $footer . $data);
      }
    }
    break;
  }
}

//能力発動結果を表示する
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  $str = '<table class="ability-result"><tr>'."\n";
  if(isset($header)) $str .= $ROLE_IMG->Generate($header, NULL, true) ."\n";
  if(isset($target)) $str .= '<td>' . $target . '</td>'."\n";
  if(isset($footer)) $str .= $ROLE_IMG->Generate($footer, NULL, true) ."\n";
  echo $str . '</tr></table>'."\n";
}

//夜の未投票メッセージ出力
function OutputVoteMessage($class, $sentence, $situation, $not_situation = ''){
  global $MESSAGE, $ROOM, $USERS;

  $stack = $ROOM->test_mode ? array() : GetSelfVoteNight($situation, $not_situation);
  if(count($stack) < 1){
    $str = $MESSAGE->{'ability_' . $sentence};
  }
  elseif($situation == 'WOLF_EAT' || $situation == 'CUPID_DO' || $situation == 'DUELIST_DO'){
    $str = '投票済み';
  }
  elseif($situation == 'SPREAD_WIZARD_DO'){
    $str_stack = array();
    foreach(explode(' ', $stack['target_uname']) as $id){
      $user = $USERS->ByVirtual($id);
      $str_stack[$user->user_no] = $user->handle_name;
    }
    ksort($str_stack);
    $str = implode('さん ', $str_stack) . 'さんに投票済み';
  }
  elseif($not_situation != '' && $stack['situation'] == $not_situation){
    $str = 'キャンセル投票済み';
  }
  else{
    $str = $USERS->GetHandleName($stack['target_uname'], true) . 'さんに投票済み';
  }
  echo '<span class="ability ' . $class . '">' . $str . '</span><br>'."\n";
}
