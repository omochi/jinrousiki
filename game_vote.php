<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_vote_functions', 'user_class');
$INIT_CONF->LoadClass('SESSION', 'ROLES', 'ICON_CONF');

//-- データ収集 --//
$INIT_CONF->LoadRequest('RequestGameVote'); //引数を取得
$DB_CONF->Connect(); //DB 接続
$SESSION->Certify(); //セッション認証

$ROOM =& new Room($RQ_ARGS); //村情報をロード
if($ROOM->IsFinished()) OutputVoteError('ゲーム終了', 'ゲームは終了しました');
$ROOM->system_time = TZTime(); //現在時刻を取得

$USERS =& new UserDataSet($RQ_ARGS); //ユーザ情報をロード
$SELF = $USERS->BySession(); //自分の情報をロード

//ロック処理
if(! LockTable('game')) OutputVoteResult('サーバが混雑しています。再度投票をお願いします。');

//-- メインルーチン --//
if($RQ_ARGS->vote){ //投票処理
  if($ROOM->IsBeforeGame()){ //ゲーム開始 or Kick 投票処理
    switch($RQ_ARGS->situation){
    case 'GAMESTART':
      $INIT_CONF->LoadClass('CAST_CONF'); //配役情報をロード
      VoteGameStart();
      break;

    case 'KICK_DO':
      VoteKick();
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('ゲーム開始前投票');
      break;
    }
  }
  elseif($SELF->IsDead()){ //死者の霊界投票処理
    VoteDeadUser();
  }
  elseif($RQ_ARGS->target_no == 0){ //空投票検出
    OutputVoteError('空投票', '投票先を指定してください');
  }
  elseif($ROOM->IsDay()){ //昼の処刑投票処理
    VoteDay();
  }
  elseif($ROOM->IsNight()){ //夜の投票処理
    VoteNight();
  }
  else{ //ここに来たらロジックエラー
    OutputVoteError('投票コマンドエラー', '投票先を指定してください');
  }
}
else{ //シーンに合わせた投票ページを出力
  $INIT_CONF->LoadClass('VOTE_MESS');
  if($SELF->IsDead()){
    OutputVoteDeadUser();
  }
  else{
    switch($ROOM->day_night){
    case 'beforegame':
      OutputVoteBeforeGame();
      break;

    case 'day':
      OutputVoteDay();
      break;

    case 'night':
      OutputVoteNight();
      break;

    default: //ここに来たらロジックエラー
      OutputVoteError('投票シーンエラー');
      break;
    }
  }
}
$DB_CONF->Disconnect(true); //DB 接続解除

//-- 関数 --//
//エラーページ出力
function OutputVoteError($title, $str = NULL){
  global $RQ_ARGS;

  $header = '<div align="center"><a id="game_top"></a>';
  $footer = "<br>\n" . $RQ_ARGS->back_url . '</div>';
  if(is_null($str)) $str = 'プログラムエラーです。管理者に問い合わせてください。';
  OutputActionResult('投票エラー [' . $title . ']', $header . $str . $footer, '', true);
}

//ゲーム開始投票の処理
function VoteGameStart(){
  global $GAME_CONF, $ROOM, $SELF;

  CheckSituation('GAMESTART');
  $str = 'ゲーム開始';
  if($SELF->IsDummyBoy(true)){ //出題者以外の身代わり君
    if($GAME_CONF->power_gm){ //強権モードによる強制開始処理
      if(! AggregateVoteGameStart(true)) $str .= '：開始人数に達していません。';
      OutputVoteResult($str);
    }
    else{
      OutputVoteResult($str . '：身代わり君は投票不要です');
    }
  }

  //投票済みチェック
  $ROOM->LoadVote();
  if(isset($ROOM->vote[$SELF->uname])) OutputVoteResult($str . '：投票済みです');

  if($SELF->Vote('GAMESTART')){ //投票処理
    AggregateVoteGameStart(); //集計処理
    OutputVoteResult('投票完了');
  }
  else{
    OutputVoteResult('データベースエラー');
  }
}

//開始前の Kick 投票の処理
function VoteKick(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('KICK_DO'); //コマンドチェック

  $target = $USERS->ByID($RQ_ARGS->target_no); //投票先のユーザ情報を取得
  if($target->uname == '' || $target->live == 'kick'){
    OutputVoteResult('Kick：投票先が指定されていないか、すでに Kick されています');
  }
  if($target->IsDummyBoy()) OutputVoteResult('Kick：身代わり君には投票できません');
  if(! $GAME_CONF->self_kick && $target->IsSelf()){
    OutputVoteResult('Kick：自分には投票できません');
  }

  //ゲーム開始チェック
  if(FetchResult($ROOM->GetQueryHeader('room', 'day_night')) != 'beforegame'){
    OutputVoteResult('Kick：既にゲームは開始されています');
  }

  $ROOM->LoadVote(true); //投票情報をロード
  $vote_data = $ROOM->vote[$SELF->uname];
  if(is_array($vote_data) && in_array($target->uname, $vote_data)){
    OutputVoteResult("Kick：{$target->handle_name} さんへ Kick 投票済み");
  }

  if($SELF->Vote('KICK_DO', $target->uname)){ //投票処理
    $ROOM->Talk("KICK_DO\t" . $target->handle_name, $SELF->uname); //投票しました通知
    $vote_count = AggregateVoteKick($target); //集計処理
    OutputVoteResult("投票完了：{$target->handle_name} さん：{$vote_count} 人目 " .
		     "(Kick するには {$GAME_CONF->kick} 人以上の投票が必要です)");
  }
  else{
    OutputVoteResult('データベースエラー');
  }
}

//Kick 投票の集計処理 ($target : 対象 HN, 返り値 : 対象 HN の投票合計数)
function AggregateVoteKick($target){
  global $GAME_CONF, $MESSAGE, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckSituation('KICK_DO'); //コマンドチェック

  //今回投票した相手にすでに投票している人数を取得
  $vote_count = 1;
  foreach($ROOM->vote as $stack){
    if(in_array($target->uname, $stack)) $vote_count++;
  }

  //規定数以上の投票があった / キッカーが身代わり君 / 自己 KICK が有効の場合に処理
  if($vote_count < $GAME_CONF->kick && ! $SELF->IsDummyBoy() &&
     ! ($GAME_CONF->self_kick && $target->IsSelf())){
    return $vote_count;
  }

  $query = "UPDATE user_entry SET live = 'kick', session_id = NULL " .
    "WHERE room_no = {$ROOM->id} AND user_no = '{$target->user_no}' AND user_no > 0";
  SendQuery($query);
  $ROOM->Talk($target->handle_name . $MESSAGE->kick_out); //出て行ったメッセージ
  $ROOM->Talk($MESSAGE->vote_reset); //投票リセット通知
  $ROOM->UpdateTime(); //最終書き込み時刻を更新
  $ROOM->DeleteVote(); //今までの投票を全部削除
  return $vote_count;
}

//夜の投票処理
function VoteNight(){
  global $GAME_CONF, $RQ_ARGS, $ROOM, $ROLES, $USERS, $SELF;

  //-- イベント名と役職の整合チェック --//
  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');
  switch($RQ_ARGS->situation){
  case 'MAGE_DO':
    if($SELF->IsRole('emerald_fox')){
      if(! $SELF->IsActive()) OutputVoteResult('夜：能力喪失しています');
    }
    elseif(! $SELF->IsRoleGroup('mage')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    break;

  case 'VOODOO_KILLER_DO':
    if(! $SELF->IsRole('voodoo_killer')) OutputVoteResult('夜：投票イベントが一致しません');
    break;

  case 'GUARD_DO':
    if(! $SELF->IsRoleGroup('guard')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'REPORTER_DO':
    if(! $SELF->IsRole('reporter')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'ANTI_VOODOO_DO':
    if(! $SELF->IsRole('anti_voodoo')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'POISON_CAT_DO':
  case 'POISON_CAT_NOT_DO':
    if(! $SELF->IsReviveGroup()) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
      OutputVoteResult('夜：能力喪失しています');
    }
    $not_type = $RQ_ARGS->situation == 'POISON_CAT_NOT_DO';
    break;

  case 'ASSASSIN_DO':
  case 'ASSASSIN_NOT_DO':
    if(! $SELF->IsRoleGroup('assassin') && ! $SELF->IsRole('doom_fox')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    $not_type = $RQ_ARGS->situation == 'ASSASSIN_NOT_DO';
    if($not_type && $ROOM->IsEvent('force_assassin_do')){
      OutputVoteResult('夜：天候「紅月」はキャンセル投票できません');
    }
    break;

  case 'MIND_SCANNER_DO':
    if($SELF->IsRole('mind_scanner', 'presage_scanner')){
      if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    }
    elseif($SELF->IsRole('evoke_scanner')){
      if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
      if($ROOM->IsOpenCast()){
	OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
      }
    }
    elseif($SELF->IsRole('clairvoyance_scanner')){
      if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    }
    else{
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    break;

  case 'WIZARD_DO':
    if(! $SELF->IsWizard(true) || $SELF->IsRole('barrier_wizard')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'SPREAD_WIZARD_DO':
    if(! $SELF->IsRole('barrier_wizard')) OutputVoteResult('夜：投票イベントが一致しません');
    if(! is_array($RQ_ARGS->target_no)) OutputVoteResult('夜：投票データが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    $is_wizard = true;
    break;

  case 'ESCAPE_DO':
    if(! $SELF->IsRoleGroup('escaper')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'WOLF_EAT':
    if(! $SELF->IsWolf()) OutputVoteResult('夜：投票イベントが一致しません');
    break;

  case 'JAMMER_MAD_DO':
    if(! $SELF->IsRoleGroup('jammer')) OutputVoteResult('夜：投票イベントが一致しません');
    break;

  case 'VOODOO_MAD_DO':
    if(! $SELF->IsRole('voodoo_mad')) OutputVoteResult('夜：投票イベントが一致しません');
    break;

  case 'DREAM_EAT':
    if(! $SELF->IsRole('dream_eater_mad')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'POSSESSED_DO':
  case 'POSSESSED_NOT_DO':
    if(! $SELF->IsRole('possessed_mad', 'possessed_fox')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：能力喪失しています');
    $not_type = $RQ_ARGS->situation == 'POSSESSED_NOT_DO';
    break;

  case 'TRAP_MAD_DO':
  case 'TRAP_MAD_NOT_DO':
    if(! $SELF->IsRoleGroup('trap_mad') && ! $SELF->IsRole('trap_fox')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    if($SELF->IsRole('trap_mad', 'trap_fox') && ! $SELF->IsActive()){
      OutputVoteResult('夜：能力喪失しています');
    }
    $not_type = $RQ_ARGS->situation == 'TRAP_MAD_NOT_DO';
    break;

  case 'VOODOO_FOX_DO':
    if(! $SELF->IsRole('voodoo_fox')) OutputVoteResult('夜：投票イベントが一致しません');
    break;

  case 'CHILD_FOX_DO':
    if(! $SELF->IsChildFox(true) && ! $SELF->IsRole('jammer_fox')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    break;

  case 'CUPID_DO':
    if(! $SELF->IsRoleGroup('cupid', 'angel') && ! $SELF->IsRole('dummy_chiroptera')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    if(! is_array($RQ_ARGS->target_no)) OutputVoteResult('夜：投票データが一致しません');
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $is_cupid = true;
    break;

  case 'VAMPIRE_DO':
    if(! $SELF->IsRoleGroup('vampire')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    break;

  case 'FAIRY_DO':
    if(! $SELF->IsRoleGroup('fairy') && ! $SELF->IsRole('enchant_mad')){
      OutputVoteResult('夜：投票イベントが一致しません');
    }
    $is_mirror_fairy = $SELF->IsRole('mirror_fairy', 'sweet_fairy');
    break;

  case 'OGRE_DO':
  case 'OGRE_NOT_DO':
    if(! $SELF->IsOgre()) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date == 1) OutputVoteResult('夜：初日は投票できません');
    $not_type = $RQ_ARGS->situation == 'OGRE_NOT_DO';
    if($not_type && $ROOM->IsEvent('force_assassin_do')){
      OutputVoteResult('夜：天候「紅月」はキャンセル投票できません');
    }
    break;

  case 'DUELIST_DO':
    if(! $SELF->IsDuelist()) OutputVoteResult('夜：投票イベントが一致しません');
    if(! is_array($RQ_ARGS->target_no)) OutputVoteResult('夜：投票データが一致しません');
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $is_duelist = $SELF->IsRoleGroup('duelist');
    $is_avenger = $SELF->IsRoleGroup('avenger');
    $is_patron  = $SELF->IsRoleGroup('patron');
    break;

  case 'MANIA_DO':
    if(! $SELF->IsRoleGroup('mania')) OutputVoteResult('夜：投票イベントが一致しません');
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    break;

  default:
    OutputVoteResult('夜：あなたは投票できません');
    break;
  }
  CheckAlreadyVote($is_mirror_fairy ? 'CUPID_DO' : $RQ_ARGS->situation); //投票済みチェック

  //-- 投票エラーチェック --//
  $error_header = '夜：投票先が正しくありません<br>'; //エラーメッセージのヘッダ

  if($not_type); //投票キャンセルタイプは何もしない
  elseif($is_wizard){ //魔法使い系 (拡散型)
    if(count($RQ_ARGS->target_no) < 1 || 4 < count($RQ_ARGS->target_no)){ //人数チェック
      OutputVoteResult($error_header . '指定人数は1～4人にしてください');
    }

    $target_list = array();
    foreach($RQ_ARGS->target_no as $target_no){
      $target = $USERS->ByID($target_no); //投票先のユーザ情報を取得
      //例外処理
      if($target->IsSelf() || ! $USERS->IsVirtualLive($target->user_no) || $target->IsDummyBoy()){
	OutputVoteResult($error_header . '自分自身・生存者以外・身代わり君には投票できません');
      }
      $target_list[] = $target;
    }
  }
  elseif($is_cupid || $is_mirror_fairy || $is_duelist){ //恋人陣営・決闘者系
    $count = 2; //対象人数
    if($SELF->IsRole('triangle_cupid', 'triangle_duelist')) $count++; //三人固定役職
    if(count($RQ_ARGS->target_no) != $count){ //人数チェック
      OutputVoteResult($error_header . '指定人数は' . $count . '人にしてください');
    }

    $self_shoot = false; //自分撃ちフラグ
    $target_list = array();
    foreach($RQ_ARGS->target_no as $target_no){
      $target = $USERS->ByID($target_no); //投票先のユーザ情報を取得
      if(! $target->IsLive() || $target->IsDummyBoy()){ //例外処理
	OutputVoteResult($error_header . '生存者以外と身代わり君には投票できません');
      }
      $target_list[] = $target;
      $self_shoot |= $target->IsSelf(); //自分撃ち判定
    }

    if(($is_cupid || $is_duelist) && ! $self_shoot){ //自分撃ちエラー判定
      if($SELF->IsRole('self_cupid', 'moon_cupid', 'dummy_chiroptera',
		       'duelist', 'critical_duelist', 'cowboy_duelist')){ //自分撃ち固定役職
	OutputVoteResult($error_header . '必ず自分を対象に含めてください');
      }
      elseif($USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot){ //参加人数
	OutputVoteResult($error_header . '少人数村の場合は、必ず自分を対象に含めてください');
      }
    }
  }
  elseif($is_avenger || $is_patron){ //復讐者系・後援者系
    $count = 1; //対象人数
    if($is_avenger){ //復讐者系
      $count = floor($USERS->GetUserCount() / 4);
    }
    elseif($SELF->IsRole('shepherd_patron')){ //羊飼い
      $count = floor($USERS->GetUserCount() / 6);
      if($count < 1) $count++;
    }

    if(count($RQ_ARGS->target_no) != $count){ //人数チェック
      OutputVoteResult($error_header . '指定人数は' . $count . '人にしてください');
    }

    $target_list = array();
    foreach($RQ_ARGS->target_no as $target_no){
      $target = $USERS->ByID($target_no); //投票先のユーザ情報を取得
      if($target->IsSelf() || ! $target->IsLive() || $target->IsDummyBoy()){ //例外処理
	OutputVoteResult($error_header . '自分自身・生存者以外・身代わり君には投票できません');
      }
      $target_list[] = $target;
    }
  }
  else{ //標準
    $target  = $USERS->ByID($RQ_ARGS->target_no); //投票先のユーザ情報を取得
    $is_live = $USERS->IsVirtualLive($target->user_no); //仮想的な生死を判定

    if($RQ_ARGS->situation != 'TRAP_MAD_DO' && $target->IsSelf()){ //自己投票判定
      OutputVoteResult($error_header . '自分には投票できません');
    }

    if($RQ_ARGS->situation == 'POISON_CAT_DO' || $RQ_ARGS->situation == 'POSSESSED_DO'){ //生死判定
      if($is_live) OutputVoteResult($error_header . '死者以外には投票できません');
    }
    elseif(! $is_live){
      OutputVoteResult($error_header . '生存者以外には投票できません');
    }

    if($RQ_ARGS->situation == 'WOLF_EAT'){ //人狼襲撃
      //仲間だと分かっている狼同士への投票は無効
      if($SELF->IsWolf(true) && ! $SELF->IsRole('hungry_wolf') &&
	 $USERS->ByReal($target->user_no)->IsWolf(true)){
	OutputVoteResult($error_header . '狼同士には投票できません');
      }

      if($ROOM->IsQuiz() && ! $target->IsDummyBoy()){ //クイズ村は GM 以外無効
	OutputVoteResult($error_header . 'クイズ村では GM 以外に投票できません');
      }

      //身代わり君使用の場合は、初日は身代わり君以外無効
      if($ROOM->IsDummyBoy() && $ROOM->date == 1 && ! $target->IsDummyBoy()){
	OutputVoteResult($error_header . '身代わり君使用の場合は、身代わり君以外に投票できません');
      }
    }
    elseif($RQ_ARGS->situation == 'MIND_SCANNER_DO' && $target->IsDummyBoy()){ //さとり系
      OutputVoteResult($error_header . '身代わり君には投票できません');
    }
  }

  //-- 投票処理 --//
  if($not_type){
    if(! $SELF->Vote($RQ_ARGS->situation)) OutputVoteResult('データベースエラー'); //投票処理
    $ROOM->SystemMessage($SELF->handle_name, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation, $SELF->uname);
  }
  else{
    if($is_wizard){ //魔法使い系 (拡散型) の処理
      $uname_stack  = array();
      $handle_stack = array();
      foreach($target_list as $target){
	$uname_stack[] = $USERS->ByReal($target->user_no)->user_no;
	$handle_stack[$target->user_no] = $target->handle_name;
      }
      sort($uname_stack);
      ksort($handle_stack);

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($SELF->IsRole('dummy_chiroptera')){ //夢求愛者の処理
      $uname_stack  = array();
      $handle_stack = array();
      foreach($target_list as $target){
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;
	if(! $target->IsSelf()) $SELF->AddMainRole($target->user_no);
      }

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($is_cupid){ //恋人陣営の処理
      $uname_stack  = array();
      $handle_stack = array();
      $ROLES->actor = $SELF;
      $filter = $ROLES->Load('main_role', true);
      foreach($target_list as $target){
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;

	$role = $SELF->GetID('lovers'); //役職に恋人を追加
	$filter->AddLoversRole($role, $target, $self_shoot); //特殊キューピッドの処理
	$target->AddRole($role);
	$target->ReparseRoles(); //再パース (魂移使判定用)
      }

      if($SELF->IsRoleGroup('angel')){ //天使系の処理
	$lovers_a = $target_list[0];
	$lovers_b = $target_list[1];
	if($filter->IsSympathy($lovers_a, $lovers_b)){
	  $lovers_a->AddRole('mind_sympathy');
	  $sentence = $lovers_a->handle_name . "\t" . $lovers_b->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_b->main_role, 'SYMPATHY_RESULT');

	  $lovers_b->AddRole('mind_sympathy');
	  $sentence = $lovers_b->handle_name . "\t" . $lovers_a->handle_name . "\t";
	  $ROOM->SystemMessage($sentence . $lovers_a->main_role, 'SYMPATHY_RESULT');
	}
      }

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($is_mirror_fairy){ //鏡妖精・恋妖精の処理
      $id_stack     = array();
      $uname_stack  = array();
      $handle_stack = array();
      $is_sweet     = $SELF->IsRole('sweet_fairy'); //恋妖精判定
      foreach($target_list as $target){ //情報収集
	$id_stack[]     = strval($target->user_no);
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;
	if($is_sweet) $target->AddRole($SELF->GetID('sweet_status')); //恋妖精の処理
      }
      if(! $is_sweet) $SELF->AddMainRole(implode('-', $id_stack)); //鏡妖精の処理

      $situation     = 'CUPID_DO';
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    elseif($is_duelist || $is_avenger || $is_patron){ //決闘者陣営の処理
      $uname_stack  = array();
      $handle_stack = array();
      $ROLES->actor = $SELF;
      $filter = $ROLES->Load('main_role', true);
      foreach($target_list as $target){
	$uname_stack[]  = $target->uname;
	$handle_stack[] = $target->handle_name;

	if($is_duelist){
	  $role = $SELF->GetID('rival'); //役職に宿敵を追加
	  $filter->AddRivalRole($role, $target, $self_shoot); //特殊決闘者の処理
	}
	elseif($is_avenger){
	  $role = $SELF->GetID('enemy'); //役職に仇敵を追加
	  $filter->AddEnemyRole($role, $target); //特殊復讐者の処理
	}
	elseif($is_patron){
	  $role = $SELF->GetID('supported'); //役職に受援者を追加
	  $filter->AddSupportedRole($role, $target); //特殊後援者の処理
	}
	$target->AddRole($role);
      }

      $situation     = $RQ_ARGS->situation;
      $target_uname  = implode(' ', $uname_stack);
      $target_handle = implode(' ', $handle_stack);
    }
    else{ //通常処理
      $situation     = $RQ_ARGS->situation;
      $target_uname  = $USERS->ByReal($target->user_no)->uname;
      $target_handle = $target->handle_name;
    }

    if(! $SELF->Vote($situation, $target_uname)) OutputVoteResult('データベースエラー'); //投票処理
    $ROOM->SystemMessage($SELF->handle_name . "\t" . $target_handle, $RQ_ARGS->situation);
    $ROOM->Talk($RQ_ARGS->situation . "\t" . $target_handle, $SELF->uname);
  }

  AggregateVoteNight(); //集計処理
  OutputVoteResult('投票完了');
}

//死者の投票処理
function VoteDeadUser(){
  global $ROOM, $SELF;

  CheckSituation('REVIVE_REFUSE'); //コマンドチェック
  if($SELF->IsDrop())     OutputVoteResult('蘇生辞退：投票済み'); //投票済みチェック
  if($ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です'); //霊界公開判定

  //-- 投票処理 --//
  if(! $SELF->Update('live', 'drop')) OutputVoteResult('データベースエラー');

  //システムメッセージ
  $str = 'システム：' . $SELF->handle_name . 'さんは蘇生を辞退しました。';
  $ROOM->Talk($str, $SELF->uname, 'heaven', 'normal');

  OutputVoteResult('投票完了');
}

//投票ページ HTML ヘッダ出力
function OutputVotePageHeader(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  OutputHTMLHeader($SERVER_CONF->title . ' [投票]', 'game');
  if($ROOM->day_night != ''){
    echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";
  }
  echo <<<EOF
<link rel="stylesheet" href="css/game_vote.css">
<link rel="stylesheet" id="day_night">
</head><body>
<a id="game_top"></a>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">

EOF;
}

//シーンの一致チェック
function CheckScene(){
  global $ROOM, $SELF;
  if($ROOM->day_night != $SELF->last_load_day_night) OutputVoteResult('戻ってリロードしてください');
}

//開始前の投票ページ出力
function OutputVoteBeforeGame(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //投票する状況があっているかチェック
  OutputVotePageHeader();
  echo '<input type="hidden" name="situation" value="KICK_DO">'."\n";
  echo '<table class="vote-page"><tr>'."\n";

  $count  = 0;
  $header = '<input type="radio" name="target_no" id="';
  foreach($USERS->rows as $id => $user){
    if($count > 0 && $count % 5 == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;

    $checkbox = ! $user->IsDummyBoy() && ($GAME_CONF->self_kick || ! $user->IsSelf()) ?
      $header . $id . '" value="' . $id . '">'."\n" : '';
    echo $user->GenerateVoteTag($ICON_CONF->path . '/' . $user->icon_filename, $checkbox);
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* Kick するには {$GAME_CONF->kick} 人の投票が必要です</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->kick_do}"></form></td>
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="GAMESTART">
<input type="submit" value="{$VOTE_MESS->game_start}"></form>
</td>
</tr></table></div>
</body></html>

EOF;
}

//昼の投票ページを出力する
function OutputVoteDay(){
  global $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //投票する状況があっているかチェック
  if($ROOM->date == 1) OutputVoteResult('処刑：初日は投票不要です');
  $vote_times = $ROOM->GetVoteTimes(); //投票回数を取得

  //投票済みチェック
  $query = $ROOM->GetQuery(true, 'vote') . " AND situation = 'VOTE_KILL' " .
    "AND vote_times = {$vote_times} AND uname = '{$SELF->uname}'";
  if(FetchResult($query) > 0) OutputVoteResult('処刑：投票済み');
  if(is_array($ROOM->event->vote_duel)){ //特殊イベントを取得
    $user_stack = array();
    foreach($ROOM->event->vote_duel as $id) $user_stack[$id] = $USERS->rows[$id];
  }
  else{
    $user_stack = $USERS->rows;
  }
  $virtual_self = $USERS->ByVirtual($SELF->user_no); //仮想投票者を取得

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="VOTE_KILL">
<input type="hidden" name="vote_times" value="{$vote_times}">
<table class="vote-page"><tr>

EOF;

  $checkbox_header = "\n".'<input type="radio" name="target_no" id="';
  $count = 0;
  foreach($user_stack as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    $is_live = $USERS->IsVirtualLive($id);

    //生きていればユーザアイコン、死んでれば死亡アイコン
    $path = $is_live ? $ICON_CONF->path . '/' . $user->icon_filename : $ICON_CONF->dead;
    $checkbox = ($is_live && ! $user->IsSame($virtual_self->uname)) ?
      $checkbox_header . $id . '" value="' . $id . '">' : '';
    echo $user->GenerateVoteTag($path, $checkbox);
  }

  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->vote_do}"></td>
</tr></table></div>
</form></body></html>

EOF;
}

//夜の投票ページを出力する
function OutputVoteNight(){
  global $GAME_CONF, $ICON_CONF, $VOTE_MESS, $RQ_ARGS, $ROOM, $USERS, $SELF;

  CheckScene(); //投票シーンチェック

  //投票済みチェック
  if($SELF->IsDummyBoy()) OutputVoteResult('夜：身代わり君の投票は無効です');

  if($role_wolf = $SELF->IsWolf()){
    $type = 'WOLF_EAT';
  }
  elseif($SELF->IsRoleGroup('mage')){
    $type = 'MAGE_DO';
  }
  elseif($SELF->IsRole('voodoo_killer')){
    $type = 'VOODOO_KILLER_DO';
  }
  elseif($SELF->IsRoleGroup('guard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の護衛はできません');
    $type = 'GUARD_DO';
  }
  elseif($SELF->IsRole('reporter')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の尾行はできません');
    $type = 'REPORTER_DO';
  }
  elseif($SELF->IsRole('anti_voodoo')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の厄払いはできません');
    $type = 'ANTI_VOODOO_DO';
  }
  elseif($role_revive = $SELF->IsReviveGroup()){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の蘇生はできません');
    if($ROOM->IsOpenCast()){
      OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
    }
    if($SELF->IsRole('revive_fox') && ! $SELF->IsActive()){
      OutputVoteResult('夜：能力喪失しています');
    }
    $type       = 'POISON_CAT_DO';
    $not_type   = 'POISON_CAT_NOT_DO';
    $submit     = 'revive_do';
    $not_submit = 'revive_not_do';
  }
  elseif($SELF->IsRoleGroup('assassin') || $SELF->IsRole('doom_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の暗殺はできません');
    $type = 'ASSASSIN_DO';
    if(! $ROOM->IsEvent('force_assassin_do')) $not_type = 'ASSASSIN_NOT_DO';
  }
  elseif($role_scanner = $SELF->IsRoleGroup('scanner')){
    if($SELF->IsRole('mind_scanner', 'presage_scanner')){
      if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    }
    elseif($SELF->IsRole('evoke_scanner')){
      if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
      if($ROOM->IsOpenCast()){
	OutputVoteResult('夜：「霊界で配役を公開しない」オプションがオフの時は投票できません');
      }
    }
    elseif($SELF->IsRole('clairvoyance_scanner')){
      if($ROOM->date == 1) OutputVoteResult('夜：初日の透視はできません');
    }
    else{
      OutputVoteResult('夜：あなたは投票できません');
    }
    $type = 'MIND_SCANNER_DO';
  }
  elseif($role_wizard = $SELF->IsRole('barrier_wizard')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日は魔法を使えません');
    $type   = 'SPREAD_WIZARD_DO';
    $submit = 'wizard_do';
  }
  elseif($SELF->IsWizard(true)){
    if($ROOM->date == 1) OutputVoteResult('夜：初日は魔法を使えません');
    $type = 'WIZARD_DO';
  }
  elseif($SELF->IsRoleGroup('escaper')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の逃亡はできません');
    $type = 'ESCAPE_DO';
  }
  elseif($SELF->IsRoleGroup('jammer')){
    $type   = 'JAMMER_MAD_DO';
    $submit = 'jammer_do';
  }
  elseif($SELF->IsRole('voodoo_mad')){
    $type   = 'VOODOO_MAD_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('dream_eater_mad')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の襲撃はできません');
    $type = 'DREAM_EAT';
  }
  elseif($role_revive = $SELF->IsRole('possessed_mad', 'possessed_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の憑依はできません');
    if(! $SELF->IsActive()) OutputVoteResult('夜：能力喪失しています');
    $type     = 'POSSESSED_DO';
    $not_type = 'POSSESSED_NOT_DO';
  }
  elseif($role_trap = $SELF->IsRoleGroup('trap_mad') || $SELF->IsRole('trap_fox')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の罠設置はできません');
    if($SELF->IsRole('trap_mad', 'trap_fox') && ! $SELF->IsActive()){
      OutputVoteResult('夜：能力喪失しています');
    }
    $type       = 'TRAP_MAD_DO';
    $not_type   = 'TRAP_MAD_NOT_DO';
    $submit     = 'trap_do';
    $not_submit = 'trap_not_do';
  }
  elseif($SELF->IsRole('voodoo_fox')){
    $type   = 'VOODOO_FOX_DO';
    $submit = 'voodoo_do';
  }
  elseif($SELF->IsRole('emerald_fox')){
    if(! $SELF->IsActive()) OutputVoteResult('夜：能力喪失しています');
    $type = 'MAGE_DO';
  }
  elseif($SELF->IsChildFox(true)){
    $type   = 'CHILD_FOX_DO';
    $submit = 'mage_do';
  }
  elseif($SELF->IsRoleGroup('cupid', 'angel') ||
	 $SELF->IsRole('dummy_chiroptera', 'mirror_fairy', 'sweet_fairy')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $type = 'CUPID_DO';
    $role_cupid = $SELF->IsRoleGroup('cupid', 'angel') || $SELF->IsRole('dummy_chiroptera');
    $role_mirror_fairy = $SELF->IsRole('mirror_fairy', 'sweet_fairy');
    $cupid_self_shoot  = $SELF->IsRole('self_cupid', 'moon_cupid', 'dummy_chiroptera') ||
      $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot;
  }
  elseif($SELF->IsRoleGroup('vampire')){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の襲撃はできません');
    $type = 'VAMPIRE_DO';
  }
  elseif($SELF->IsRoleGroup('fairy') || $SELF->IsRole('enchant_mad')){
    $type = 'FAIRY_DO';
  }
  elseif($SELF->IsOgre()){
    if($ROOM->date == 1) OutputVoteResult('夜：初日の人攫いはできません');
    $type = 'OGRE_DO';
    if(! $ROOM->IsEvent('force_assassin_do')) $not_type = 'OGRE_NOT_DO';
  }
  elseif($SELF->IsDuelist()){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $type = 'DUELIST_DO';
    $role_cupid   = $SELF->IsRoleGroup('duelist');
    $role_avenger = $SELF->IsRoleGroup('avenger', 'patron');
    $cupid_self_shoot = $SELF->IsRole('duelist', 'critical_duelist', 'cowboy_duelist') ||
      $USERS->GetUserCount() < $GAME_CONF->cupid_self_shoot;
  }
  elseif($SELF->IsRoleGroup('mania')){
    if($ROOM->date != 1) OutputVoteResult('夜：初日以外は投票できません');
    $type = 'MANIA_DO';
  }
  else{
    OutputVoteResult('夜：あなたは投票できません');
  }
  CheckAlreadyVote($type, $not_type);
  //鏡妖精・恋妖精は表示だけ妖精系 (内部処理はキューピッド系)
  if($role_mirror_fairy) $type = 'FAIRY_DO';

  //身代わり君使用 or クイズ村の時は身代わり君だけしか選べない
  if($role_wolf && (($ROOM->IsDummyBoy() && $ROOM->date == 1) || $ROOM->IsQuiz())){
    //身代わり君のユーザ情報
    $user_stack = array(1 => $USERS->rows[1]); //dummy_boy = 1番は保証されている？
  }
  else{
    $user_stack = $USERS->rows;
  }

  OutputVotePageHeader();
  echo '<table class="vote-page"><tr>'."\n";
  $radio_header    = '<input type="radio" name="target_no"';
  $checkbox_header = '<input type="checkbox" name="target_no[]"';
  $count = 0;
  foreach($user_stack as $id => $user){
    if($count > 0 && ($count % 5) == 0) echo "</tr>\n<tr>\n"; //5個ごとに改行
    $count++;
    $is_live  = $USERS->IsVirtualLive($id);
    $is_avoid = ! $user->IsSelf() && ! $user->IsDummyBoy();
    $is_wolf  = $role_wolf && ! $SELF->IsRole('hungry_wolf', 'silver_wolf') &&
      $USERS->ByReal($id)->IsWolf(true);

    /*
      死んでいれば死亡アイコン (蘇生能力者は死亡アイコンにしない)
      狼同士なら狼アイコン、生きていればユーザアイコン
    */
    $path = ! ($is_live || $role_revive) ? $ICON_CONF->dead :
      ($is_wolf ? $ICON_CONF->wolf : $ICON_CONF->path . '/' . $user->icon_filename);

    $checkbox = '';
    $checkbox_footer = ' id="' . $id . '"value="' . $id . '">'."\n";
    if($role_cupid || $role_mirror_fairy){
      if($is_live && ! $user->IsDummyBoy()){
	$checked = ($role_cupid && $cupid_self_shoot && $user->IsSelf()) ? ' checked' : '';
	$checkbox = $checkbox_header . $checked . $checkbox_footer;
      }
    }
    elseif($role_avenger){
      if($is_live && ! $user->IsDummyBoy() && ! $user->IsSelf()){
	$checkbox = $checkbox_header . $checkbox_footer;
      }
    }
    elseif($role_wizard){
      if($is_live && $is_avoid) $checkbox = $checkbox_header . $checkbox_footer;
    }
    elseif($role_revive){
      if(! $is_live && $is_avoid) $checkbox = $radio_header . $checkbox_footer;
    }
    elseif($role_scanner){
      if($is_live && $is_avoid) $checkbox = $radio_header . $checkbox_footer;
    }
    elseif($role_trap){
      if($is_live) $checkbox = $radio_header . $checkbox_footer;
    }
    elseif($is_live && ! $user->IsSelf() && ! $is_wolf){
      $checkbox = $radio_header . $checkbox_footer;
    }
    echo $user->GenerateVoteTag($path, $checkbox);
  }

  if(empty($submit)) $submit = strtolower($type);
  echo <<<EOF
</tr></table>
<span class="vote-message">* 投票先の変更はできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<input type="hidden" name="situation" value="{$type}">
<td><input type="submit" value="{$VOTE_MESS->$submit}"></td></form>

EOF;

  if($not_type != ''){
    if(empty($not_submit)) $not_submit = strtolower($not_type);
    echo <<<EOF
<td>
<form method="POST" action="{$RQ_ARGS->post_url}">
<input type="hidden" name="vote" value="on">
<input type="hidden" name="situation" value="{$not_type}">
<input type="hidden" name="target_no" value="{$SELF->user_no}">
<input type="submit" value="{$VOTE_MESS->$not_submit}"></form>
</td>

EOF;
  }

  echo <<<EOF
</tr></table></div>
</body></html>

EOF;
}

//死者の投票ページ出力
function OutputVoteDeadUser(){
  global $VOTE_MESS, $RQ_ARGS, $ROOM, $SELF;

  //投票済みチェック
  if($SELF->IsDummyBoy()) OutputVoteResult('蘇生辞退：身代わり君の投票は無効です');
  if($SELF->IsDrop())     OutputVoteResult('蘇生辞退：投票済み');
  if($ROOM->IsOpenCast()) OutputVoteResult('蘇生辞退：投票不要です');

  OutputVotePageHeader();
  echo <<<EOF
<input type="hidden" name="situation" value="REVIVE_REFUSE">
<span class="vote-message">* 投票の取り消しはできません。慎重に！</span>
<div class="vote-page-link" align="right"><table><tr>
<td>{$RQ_ARGS->back_url}</td>
<td><input type="submit" value="{$VOTE_MESS->revive_refuse}"></form></td>
</tr></table></div>
</body></html>

EOF;
}

//投票済みチェック
function CheckAlreadyVote($action, $not_action = ''){
  if(CheckSelfVoteNight($action, $not_action)) OutputVoteResult('夜：投票済み');
}
