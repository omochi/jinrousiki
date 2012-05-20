<?php
abstract class RoomOptionItem {
  public $name;
  public $enable;
  public $value;

  public $collect = 'SetOption';

  /*
  public $formtype;
  public $formname;
  public $formvalue;
  public $caption;
  public $explain;
  */

  function  __construct($group) {
    $this->name = array_pop(explode('Option_', get_class($this)));
    RoomOption::SetGroup($group, $this);
    $enable  = sprintf('%s_enable',  $this->name);
    $default = sprintf('default_%s', $this->name);
    $this->enable = isset(GameOptionConfig::$$enable) ? GameOptionConfig::$$enable : true;
    if (isset(GameOptionConfig::$$default)) {
      $this->value = GameOptionConfig::$$default;
    }
    $this->formname = $this->name;
    $this->formvalue = $this->value;
  }

  function SetOption(RoomOption $option, $value) {
    $option->Set($this, $this->name, $value);
  }

  function CollectPostParam(RoomOption $option) {
    if (isset($_POST[$this->name]) && isset($this->collect)) {
      call_user_func_array(array($this, $this->collect), array($option, $_POST[$this->name]));
    }
  }

  function GetName() { return $this->GetCaption(); }

  abstract function GetCaption();

  function GetExplain() { return $this->GetCaption(); }

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
  function  __construct($group) {
    parent::__construct($group);
    $this->formtype = 'checkbox';
    $this->formvalue = 'on';
  }

  function SetOption(RoomOption $option, $value) {
    $checked = $value == $this->formvalue && !empty($this->formvalue);
    $option->Set($this, $this->name, $checked);
  }

  function SetOptionAsKeyValue(RoomOption $option, $value) {
    $checked = $value == $this->formvalue && !empty($this->formvalue);
    if ($checked) {
      $option->Set($this, $this->name, $this->formvalue);
    }
  }

  function SetOptionAsValue(RoomOption $option, $value) {
    $checked = $value == $this->formvalue && !empty($this->formvalue);
    $option->Set($this, $this->formvalue, $checked);
  }
}

/**
 * セレクタ型の村立てオプション項目を提供します。
 */
abstract class SelectorRoomOptionItem extends RoomOptionItem {
  public $label;
  public $items;
  public $items_source;
  public $conf_name;

  function  __construct($group) {
    parent::__construct($group);
    $this->formtype = 'select';
    $this->items_source = "{$this->name}_items";
  }

  function CollectValue(RoomOption $option, $value) {
    $items = $this->GetItems();
    if (isset($items[$value]) && !empty($value)) {
      $child = $items[$value];
      if ($child instanceof RoomOptionItem) {
	$option->Set($this, $child->name, true);
      }
      else {
	$option->Set($this, $value, true);
      }
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
	    $item = RoomOption::Get($value);
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
  public $size;
  public $footer;

  function  __construct($group) {
    parent::__construct($group);
    $this->formtype = 'textbox';
  }

  function GetCaption() { return $this->caption; }

  function  LoadMessages() {
    $size = "{$this->name}_input";
    if (isset(RoomConfig::$$size)) $this->size = RoomConfig::$$size;
  }
}
