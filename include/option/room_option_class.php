<?php
class RoomOption extends OptionParser {
  static $icon_order = array(
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

  static $definitions = array();

  /*
    これらのプロパティは設定されたオプションのゲーム用/役職用の分割に使用されている。
    詳しくはGetOptionStringメソッドを見よ。
    異なるパラメータで同じクラスのグローバル変数を複数生成できるようになった場合、
    またはroomテーブルのオプション属性が統合された場合、
    これらのプロパティを使用する必要はなくなると思われる。(2012-01-15 enogu)
  */
  const NOT_OPTION  = '';
  const GAME_OPTION = 'game_option';
  const ROLE_OPTION = 'role_option';
  public $groups = array();

  static function SetGroup($group, $item) {
    $item->group = $group;
    if ($item instanceof RoomOptionItemGroup) {
      foreach ($item->items as $child) {
        self::SetGroup($group, $child);
      }
    }
  }

  static function Get($item) {
    if (!isset(self::$definitions[$item])) {
      $file = dirname(__FILE__)."/{$item}.php";
      if (file_exists($file)) {
	require_once($file);
	$class = 'Option_'.$item;
	self::$definitions[$item] = new $class();
      }
      else {
	self::$definitions[$item] = null;
      }
    }
    return self::$definitions[$item];
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

  function  __construct($value = '') {
    parent::__construct($value);
  }

  function LoadPostParams($target = null) {
    $items = is_array($target) ? $target : func_get_args();
    $all = empty($items);
    foreach ($_POST as $key => $value) {
      $def = self::Get($key);
      if (isset($def) && ($all || in_array($def->name, $items))) {
        $def->CollectPostParam($this);
      }
    }
  }

  function Set($item, $name, $value) {
    if ($item instanceof RoomOptionItem) {
      $this->groups[$item->group][$name] = true;
    }
    else {
      $this->groups[$item][$name] = true;
    }
    parent::__set($name, $value);
  }

  function GetCaption($name) {
    return is_object($object = self::Get($name)) ? $object->GetCaption() : false;
  }

  function GetMessage($name) {
    if (isset(self::$definitions[$name])) {
      return self::$definitions[$name]->description;
    }
    return false;
  }

  function GetOptionString($type = null) {
    if (! isset($type)) {
      return $this->ToString();
    }
    elseif (isset($this->groups[$type])) {
      return $this->ToString(array_keys($this->groups[$type]));
    }
  }

  /** ゲームオプションの画像タグを作成する */
  function GenerateImageList() {
    $str = '';
    foreach (self::$icon_order as $option) {
      $define = self::Get($option);
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
