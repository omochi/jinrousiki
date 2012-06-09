<?php
//オプションパーサ
class OptionParser {
  public $row;
  public $options = array();

  function __construct($value){
    $this->row = $value;
    foreach (explode(' ', $this->row) as $option){
      if (empty($option)) continue;
      $items = explode(':', $option);
      $this->options[$items[0]] = count($items) > 1 ? array_slice($items, 1) : true;
    }
  }

  function  __isset($name) {
    return isset($this->options[$name]);
  }

  function  __unset($name) {
    unset($this->options[$name]);
  }

  function __get($name){
    if (isset($this->options[$name])) {
      $value = $this->options[$name];
      $this->$name = $value;
      return $value;
    }
    $this->$name = false;
    return null;
  }

  function __set($name, $value){
    //Note:$value === falseの時unsetする代わりに__toStringで値がfalseの項目を省略する仕様に改めた(2011-01-14 enogu)
    $this->options[$name] = $value;
  }

  function __toString(){
    return $this->ToString();
  }

  function ToString($items = null) {
    if (isset($items)) {
      $filter = array_flip(is_array($items) ? $items : func_get_args());
    }
    else {
      $filter = $this->options;
    }
    $result = array();
    foreach (array_intersect_key($this->options, $filter) as $name => $value) {
      if (is_bool($value)) {
        if ($value) $result[] = $name;
      }
      elseif (is_array($value)) {
        $result[] = "{$name}:" . implode(':', $value);
      }
      elseif (! empty($value)) {
        $result[] = "{$name}:{$value}";
      }
    }
    return implode(' ', $result);
  }

  function Option($value){
    $this->__construct($value);
    foreach ($this->options as $name => $value) $this->__get($name);
  }

  function Exists($name){ return array_key_exists($name, $this->options); }
}

//-- オプションマネージャ --//
class OptionManager {
  const PATH = '%s/option/%s.php';
  public  static $stack;
  private static $file  = array();
  private static $class = array();

  //特殊普通村編成リスト
  private static $role_list = array(
    'detective', 'poison', 'assassin', 'wolf', 'boss_wolf', 'poison_wolf', 'possessed_wolf',
    'sirius_wolf', 'fox', 'child_fox', 'cupid', 'medium', 'mania');

  //特殊サブ配役リスト
  private static $cast_list = array(
    'decide', 'authority', 'joker', 'deep_sleep', 'blinder', 'mind_open',
    'perverseness', 'liar', 'gentleman', 'critical', 'sudden_death', 'quiz');

  //クラス取得
  static function GetClass($name) {
    return self::Load($name) ? self::LoadClass($name) : null;
  }

  //ファイルロード
  static function Load($name) {
    if (is_null($name) || ! file_exists($file = self::GetPath($name))) return false;
    if (in_array($name, self::$file)) return true;
    require_once($file);
    self::$file[] = $name;
    return true;
  }

  //特殊普通村の配役処理
  static function SetRole(&$list, $count) {
    foreach (self::$role_list as $option) {
      if (DB::$ROOM->IsOption($option) && self::Load($option)) {
	self::LoadClass($option)->SetRole($list, $count);
      }
    }
  }

  //ユーザ配役処理
  function Cast(&$list, &$rand) {
    $delete = self::$stack;
    foreach (self::$cast_list as $option) {
      if (DB::$ROOM->IsOption($option) && self::Load($option)) {
	$stack = self::LoadClass($option)->Cast($list, $rand);
	if (is_array($stack)) $delete = array_merge($delete, $stack);
      }
    }
    self::$stack = $delete;
  }

  //オプション名生成
  static function GenerateCaption($name) {
    return self::Load($name) ? self::LoadClass($name)->GetName() : '';
  }

  //オプション名出力
  static function OutputCaption($name) { echo self::GenerateCaption($name); }

  //オプション説明出力
  static function OutputExplain($name) {
    echo self::Load($name) ? self::LoadClass($name)->GetExplain() : '';
  }

  //ファイルパス取得
  private function GetPath($name) { return sprintf(self::PATH, JINRO_INC, $name); }

  //クラスロード
  private function LoadClass($name) {
    if (! isset(self::$class[$name])) {
      $class_name = 'Option_' . $name;
      self::$class[$name] = new $class_name();
    }
    return self::$class[$name];
  }
}
