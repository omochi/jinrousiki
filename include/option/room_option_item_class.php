<?php
//-- 村作成オプションの基底クラス --//
abstract class RoomOptionItem {
  public $name;
  public $enable;

  public $value;

  public $type;
  /*
  public $formtype;
  public $formname;
  public $formvalue;
  public $caption;
  public $explain;
  */

  function __construct($group = null) {
    $this->name = array_pop(explode('Option_', get_class($this)));

    if (isset($group)) RoomOption::SetGroup($group, $this);

    $enable  = sprintf('%s_enable',  $this->name);
    $default = sprintf('default_%s', $this->name);
    $this->enable = isset(GameOptionConfig::$$enable) ? GameOptionConfig::$$enable : true;
    if (isset(GameOptionConfig::$$default)) {
      $this->value = GameOptionConfig::$$default;
    }

    $this->formname = $this->name;
    if (! isset($this->formvalue)) $this->formvalue = $this->value;
  }

  //オプション名取得
  function GetName() { return $this->GetCaption(); }

  //キャプション取得
  abstract function GetCaption();

  //説明文取得
  function GetExplain() { return $this->GetCaption(); }

  //フォームデータ取得
  abstract function LoadPost();

  function LoadMessages() {
    $this->caption = $this->GetCaption();
    $this->explain = $this->GetExplain();
  }

  function CastOnce(&$list, &$rand, $str = '') {
    $list[array_pop($rand)] .= ' ' . $this->name . $str;
    return array($this->name);
  }

  function CastAll(&$list) {
    foreach (array_keys($list) as $id) $list[$id] .= ' ' . $this->name;
    return array($this->name);
  }
}

/**
 * チェックボックス型の標準的な村立てオプション項目を提供します。
 */
abstract class CheckRoomOptionItem extends RoomOptionItem {
  public $group = RoomOption::ROLE_OPTION;
  public $formtype = 'checkbox';
  public $formvalue = 'on';

  function LoadPost() {
    RQ::$get->Parse('IsOn', 'post.' . $this->name);
    if (RQ::$get->{$this->name}) array_push(RoomOption::${$this->group}, $this->name);
    return RQ::$get->{$this->name};
  }
}

/**
 * セレクタ型の村立てオプション項目を提供します。
 */
abstract class SelectorRoomOptionItem extends RoomOptionItem {
  public $group = RoomOption::ROLE_OPTION;
  public $type  = 'selector';

  public $formtype = 'select';

  public $label = 'モード名';
  public $items;
  public $items_source;
  public $conf_name;

  function __construct() {
    parent::__construct();
    $this->items_source = sprintf('%s_list', $this->name);
  }

  function LoadPost() {
    if (! isset($_POST[$this->name]) || empty($_POST[$this->name])) return false;
    $post = $_POST[$this->name];

    if (in_array($post, $this->item_list)) {
      RQ::$get->$post = true;
      array_push(RoomOption::${$this->group}, $post);
    }
  }

  function GetItems() {
    if (!isset($this->items)) {
      $this->items = array();
      $stack = is_array($this->conf_name) ? $this->conf_name :
	GameOptionConfig::${$this->items_source};
      if (isset($stack)) {
	foreach ($stack as $key => $value) {
	  if (is_string($key)) {
	    if ($this->ItemIsAvailable($key)) {
	      $this->items[$key] = $value;
	    }
	  }
	  else if (is_string($value)) {
	    $item = OptionManager::GetClass($value);
	    if (isset($item) && $item->enable) {
	      $this->items[$item->name] = $item;
	    }
	  }
	  else {
	    $this->items[] = $value;
	  }
	}
      }
    }
    return $this->items;
  }

  function ItemIsAvailable($name) {
    $enable = sprintf('%s_enable', $name);
    return isset(GameOptionConfig::$$enable) ? GameOptionConfig::$$enable : true;
  }
}

/**
 * テキスト型の村立てオプション項目を提供します。
 */
abstract class TextRoomOptionItem extends RoomOptionItem {
  public $group = RoomOption::NOT_OPTION;
  public $type = 'textbox';

  function LoadPost() { RQ::$get->Parse('Escape', 'post.' . $this->name); }
}
