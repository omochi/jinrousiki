<?php
//-- 役職コントローラークラス --//
class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //発言表示
  var $talk_list = array('blinder', 'earplug', 'speaker');

  //悪戯発言変換
  var $say_bad_status_list = array('fairy', 'spring_fairy', 'summer_fairy', 'autumn_fairy',
				   'winter_fairy', 'greater_fairy');

  //発言変換
  var $say_list = array('liar', 'rainbow', 'weekly', 'passion', 'actor', 'grassy', 'invisible',
			'mower', 'silent', 'side_reverse', 'line_reverse');

  //声量
  var $voice_list = array('strong_voice', 'normal_voice', 'weak_voice', 'inside_voice',
			  'outside_voice', 'upper_voice', 'downer_voice', 'random_voice');

  //処刑投票(メイン)
  var $vote_do_main_list = array('human', 'elder', 'scripter', 'elder_wolf', 'elder_fox',
				 'elder_chiroptera');

  //処刑投票(サブ)
  var $vote_do_sub_list = array('authority', 'critical_voter', 'random_voter', 'wirepuller_luck',
				'watcher', 'panelist');

  //処刑得票
  var $voted_list = array('upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
			  'random_luck', 'wirepuller_luck');

  //処刑投票系能力者
  var $vote_ability_list = array('saint', 'executor', 'bacchus_medium', 'seal_medium',
				 'trap_common', 'pharmacist', 'cure_pharmacist',
				 'revive_pharmacist', 'alchemy_pharmacist', 'centaurus_pharmacist',
				 'jealousy', 'divorce_jealousy', 'cursed_brownie', 'agitate_mad',
				 'amaze_mad', 'miasma_mad', 'critical_mad', 'sweet_cupid', 'quiz',
				 'impatience', 'authority', 'rebel', 'decide', 'plague',
				 'good_luck', 'bad_luck');

  //反逆者判定
  var $rebel_list = array('rebel');

  //処刑者決定 (順番依存あり)
  var $vote_kill_list = array('decide', 'bad_luck', 'impatience', 'good_luck', 'plague',
			      'quiz', 'executor', 'saint', 'agitate_mad');

  //毒能力鑑定
  var $distinguish_poison_list = array('pharmacist', 'alchemy_pharmacist');

  //解毒判定
  var $detox_list = array('pharmacist', 'cure_pharmacist', 'alchemy_pharmacist');

  //特殊毒能力者
  var $poison_list = array('strong_poison', 'incubate_poison', 'guide_poison', 'dummy_poison',
			   'poison_jealousy', 'poison_doll', 'poison_wolf', 'poison_fox',
			   'poison_chiroptera', 'poison_ogre');

  //処刑者カウンター
  var $vote_kill_counter_list = array('brownie', 'doom_doll', 'miasma_fox');

  //処刑投票能力処理 (順番依存あり)
  var $vote_action_list = array('seal_medium', 'bacchus_medium', 'centaurus_pharmacist',
				'amaze_mad', 'miasma_mad', 'critical_mad', 'sweet_cupid');

  //得票カウンター
  var $voted_reaction_list = array('trap_common', 'jealousy');

  //ショック死
  var $sudden_death_list = array('challenge_lovers', 'febris', 'frostbite', 'death_warrant',
				 'chicken', 'rabbit', 'perverseness', 'flattery', 'impatience',
				 'celibacy', 'nervy', 'androphobia', 'gynophobia', 'panelist');

  //ショック死抑制
  var $cure_list = array('cure_pharmacist', 'revive_pharmacist');

  //処刑得票カウンター
  var $vote_kill_reaction_list = array('divorce_jealousy', 'cursed_brownie');

  //身代わり能力者
  var $sacrifice_list = array('doll_master', 'sacrifice_vampire', 'boss_chiroptera',
			      'sacrifice_ogre');

  //人狼襲撃カウンター
  var $wolf_eat_counter_list = array('ghost_common', 'presage_scanner', 'cursed_brownie',
				     'miasma_fox');

  function __construct(){
    $this->path = JINRO_INC . '/role';
    $this->loaded->file = array();
    $this->loaded->class = array();
  }

  function Load($type, $shift = false){
    $stack = array();
    foreach($this->GetList($type) as $role){
      if(! $this->actor->IsRole($role)) continue;
      $stack[] = $role;
      if($this->LoadFile($role)) $this->LoadClass($role, 'Role_' . $role);
    }
    $filter = $this->GetFilter($stack);
    return $shift ? array_shift($filter) : $filter;
  }

  function LoadFile($name){
    if(is_null($name) || ! file_exists($file = $this->path . '/' . $name . '.php')) return false;
    if(in_array($name, $this->loaded->file)) return true;
    require_once($file);
    $this->loaded->file[] = $name;
    return true;
  }

  function LoadClass($name, $class){
    if(is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->loaded->class[$name] =& new $class();
    return true;
  }

  function LoadFilter($type){
    return $this->GetFilter($this->GetList($type));
  }

  function GetList($type){
    $stack = $type == 'main_role' ? array($this->actor->GetMainRole(true)) :
      $this->{$type . '_list'};
    return is_array($stack) ? $stack : array();
  }

  function GetFilter($list){
    $stack = array();
    foreach($list as $key){ //順番依存があるので配列関数を使わないで処理する
      if(is_object(($class = $this->loaded->class[$key]))) $stack[] = $class;
    }
    return $stack;
  }

  function GetWhisperingUserInfo($role, &$class){
    global $ROOM, $SELF;

    if($SELF->IsRole('deep_sleep')) return false; //爆睡者にはいっさい見えない
    switch($role){
    case 'common': //共有者のささやき
      if($SELF->IsRole('dummy_common')) return false; //夢共有者には見えない
      $class = 'talk-common';
      return '共有者の小声';

    case 'wolf': //人狼の遠吠え
      if($SELF->IsRole('mind_scanner')) return false; //さとりには見えない
      return '狼の遠吠え';

    case 'lovers': //恋人の囁き
      return '恋人の囁き';
    }
    return false;
  }

  function GetWhisperingSound($role, $talk, &$class){
    global $MESSAGE;

    switch($role){
    case 'common':
      $class = 'say-common';
      return $MESSAGE->common_talk;

    case 'wolf':
      return $MESSAGE->wolf_howl;

    case 'lovers':
      return $MESSAGE->lovers_talk;
    }
  }
}

//-- 役職の基底クラス --//
class Role{
  var $role;

  function __construct(){
    $this->role = array_pop(explode('Role_', get_class($this)));
  }

  //-- 判定用関数 --//
  function GetActor(){
    global $ROLES;
    return $ROLES->actor;
  }

  function Ignored(){
    global $ROOM, $ROLES, $USERS;
    //return false; //テスト用
    return ! $ROOM->IsPlaying() ||
      ! ($USERS->IsVirtualLive($ROLES->actor->user_no) || $ROLES->actor->virtual_live);
  }

  function IsSameUser($uname){
    global $ROLES;
    return $ROLES->actor->IsSame($uname);
  }

  function IsLive($strict = false){
    global $ROLES;
    return $ROLES->actor->IsLive($strict);
  }

  function IsDead($strict = false){
    global $ROLES;
    return $ROLES->actor->IsDead($strict);
  }
}

//-- 発言フィルタリング用拡張クラス --//
class RoleTalkFilter extends Role{
  var $volume_list = array('weak', 'normal', 'strong');

  function __construct(){ parent::__construct(); }

  function AddTalk(   $user, $talk, &$user_info, &$volume, &$sentence){}
  function AddWhisper($role, $talk, &$user_info, &$volume, &$sentence){}

  function ChangeVolume($type, &$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;
    switch($type){
    case 'up':
      if(($key = array_search($volume, $this->volume_list)) === false) return;
      if(++$key >= count($this->volume_list))
	$sentence = $MESSAGE->howling;
      else
	$volume = $this->volume_list[$key];
      break;

    case 'down':
      if(($key = array_search($volume, $this->volume_list)) === false) return;
      if(--$key < 0)
	$sentence = $MESSAGE->common_talk;
      else
	$volume = $this->volume_list[$key];
      break;
    }
  }
}

//-- 処刑投票能力者用拡張クラス --//
class RoleVoteAbility extends Role{
  var $data_type;
  var $decide_type;

  function __construct(){
    global $ROLES;

    parent::__construct();
    if($this->init_stack && ! is_array($ROLES->stack->{$this->role})){
      $ROLES->stack->{$this->role} = array();
    }
 }

  //投票データ収拾
  function SetVoteAbility($uname){
    global $ROLES, $USERS;
    switch($this->data_type){
    case 'self':
      $ROLES->stack->{$this->role} = $ROLES->actor->uname;
      break;

    case 'target':
      $ROLES->stack->{$this->role} = $uname;
      break;

    case 'both':
      $ROLES->stack->{$this->role} = $ROLES->actor->uname;
      $ROLES->stack->{$this->role . '_uname'} = $uname;
      break;

    case 'array':
      $user = $USERS->ByRealUname($ROLES->actor->uname);
      if($user->IsRole($this->role)) $ROLES->stack->{$this->role}[] = $user->uname;
      break;

    case 'action':
      $user = $USERS->ByRealUname($ROLES->actor->uname);
      if($user->IsRole($this->role)) $ROLES->stack->{$this->role}[$user->uname] = $uname;
      break;
    }
  }

  //処刑者決定
  function DecideVoteKill(&$uname){
    global $ROLES;

    if($uname != '') return true;
    switch($this->decide_type){
    case 'decide':
      $target = $ROLES->stack->{$this->role};
      if(in_array($target, $ROLES->stack->vote_possible)) $uname = $target;
      return false;

    case 'escape':
      $key = array_search($ROLES->stack->{$this->role}, $ROLES->stack->vote_possible);
      if($key === false) return true;
      unset($ROLES->stack->vote_possible[$key]);
      if(count($ROLES->stack->vote_possible) == 1){ //候補が一人になった場合は処刑者決定
	$uname = array_shift($ROLES->stack->vote_possible);
      }
      return false;

    case 'same':
      if(! is_array($ROLES->stack->{$this->role}) ||
	 count($stack = $this->GetMaxVotedUname()) != 1) return true;
      $uname = array_shift($stack);
      return false;

    case 'action':
      return ! is_array($ROLES->stack->{$this->role});

    default:
      return false;
    }
  }

  //投票データ取得
  function GetStack(){
    global $ROLES;
    return $ROLES->stack->{$this->role};
  }

  //最大得票者投票者ユーザ名取得
  function GetMaxVotedUname(){
    global $ROLES;
    return array_intersect($ROLES->stack->vote_possible, $ROLES->stack->{$this->role});
  }

  //投票者ユーザ取得
  function GetVoteUser(){
    global $ROLES, $USERS;
    return $USERS->ByRealUname($ROLES->stack->target[$ROLES->actor->uname]);
  }

  //得票者名取得
  function GetVotedUname($uname = NULL){
    global $ROLES;
    return array_keys($ROLES->stack->target, is_null($uname) ? $ROLES->actor->uname : $uname);
  }

  //投票先人数取得 (ショック死判定用)
  function GetVoteCount(){
    global $ROLES;
    return $ROLES->stack->count[$ROLES->stack->target[$ROLES->actor->uname]];
  }

  //得票人数取得 (ショック死判定用)
  function GetVotedCount(){
    global $ROLES;
    return $ROLES->stack->count[$ROLES->actor->uname];
  }

  //処刑者判定
  function IsVoted($uname = NULL){
    global $ROLES;
    return $ROLES->stack->vote_kill_uname == (is_null($uname) ? $ROLES->actor->uname : $uname);
  }

  //発動日判定 (ショック死判定用)
  function IsDoom(){
    global $ROOM, $ROLES;
    return $ROOM->date == $ROLES->actor->GetDoomDate($this->role);
  }
}
