<?php
class RoomOption extends OptionParser {
  const NOT_OPTION  = '';
  const GAME_OPTION = 'game_option';
  const ROLE_OPTION = 'role_option';

  static $game_option = array();
  static $role_option = array();
  static $icon_order  = array(
    'wish_role', 'real_time', 'dummy_boy', 'gm_login', 'gerd', 'wait_morning', 'open_vote',
    'seal_message', 'open_day', 'not_open_cast', 'auto_open_cast', 'poison', 'assassin', 'wolf',
    'boss_wolf', 'poison_wolf', 'possessed_wolf', 'sirius_wolf', 'fox', 'child_fox', 'cupid',
    'medium', 'mania', 'decide', 'authority', 'detective', 'liar', 'gentleman', 'deep_sleep',
    'blinder', 'mind_open', 'sudden_death', 'perverseness', 'critical', 'joker', 'death_note',
    'weather', 'festival', 'replace_human', 'full_mad', 'full_cupid', 'full_quiz', 'full_vampire',
    'full_chiroptera', 'full_mania', 'full_unknown_mania', 'change_common', 'change_hermit_common',
    'change_mad', 'change_fanatic_mad', 'change_whisper_mad', 'change_immolate_mad', 'change_cupid',
    'change_mind_cupid', 'change_triangle_cupid', 'change_angel', 'duel', 'gray_random', 'quiz',
    'chaos', 'chaosfull', 'chaos_hyper', 'chaos_verso', 'topping', 'boost_rate', 'chaos_open_cast',
    'chaos_open_cast_camp', 'chaos_open_cast_role', 'secret_sub_role', 'no_sub_role',
    'sub_role_limit_easy', 'sub_role_limit_normal', 'sub_role_limit_hard');

  //登録されたオプションを取得
  static function GetOption($type) { return implode(' ', self::$$type); }

  //オプションを登録
  static function SetOption($type, $name) {
    RQ::$get->$name = true;
    if (! in_array($name, self::$$type)) array_push(self::$$type, $name);
  }

  //フォームからの入力値を取得
  static function LoadPost($name) {
    foreach (func_get_args() as $option) {
      $filter = OptionManager::GetClass($option);
      if (isset($filter)) $filter->LoadPost();
    }
  }

  static function SetGroup($group, $item) {
    $item->group = $group;
    if ($item instanceof RoomOptionItemGroup) {
      foreach ($item->items as $child) {
        self::SetGroup($group, $child);
      }
    }
  }

  static function Wrap($option) {
    $result = new RoomOption();
    foreach (func_get_args() as $opt) {
      if ($opt instanceof OptionParser) {
        array_merge($result->options, $opt->options);
      }
      elseif (is_string($opt)) {
        $result->Option($opt);
      }
    }
    return $result;
  }

  function __construct($value = '') { parent::__construct($value); }

  /** ゲームオプションの画像タグを作成する */
  function GenerateImageList() {
    $str = '';
    foreach (self::$icon_order as $option) {
      $define = OptionManager::GetClass($option);
      if (! isset($define, $this->$option)) continue;
      $define->LoadMessages();
      $footer = '';
      $sentence = $define->caption;
      if (isset(CastConfig::$option) && is_int(CastConfig::$$option)) {
	$sentence .= sprintf('(%d人～)', CastConfig::$$option);
      }
      switch ($option) {
      case 'real_time':
        list($day, $night) = $this->options[$option];
        $sentence .= "　昼： {$day} 分　夜： {$night} 分";
	$footer = '['. $day . '：' . $night . ']';
	break;
	
      case 'topping':
      case 'boost_rate':
	$type = $this->options[$option][0];
	$items = $define->GetItems();
	$sentence .= '(Type' . $items[$type] . ')';
	$footer = '['. strtoupper($type) . ']';
	break;
      }
      $str .= Image::Room()->Generate($option, $sentence) . $footer;
    }
    return $str;
  }
}
