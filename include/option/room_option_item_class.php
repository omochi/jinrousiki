<?php
abstract class RoomOptionItem {
  var $id;
  var $name;
  var $enabled;
  var $value;
  var $caption;
  var $explain;
  var $checked;
  var $collect;

  function  __construct($id, $name, $caption, $explain) {
    global $GAME_OPT_CONF;
    $this->id = $id;
    $this->name = $name;
    $enable = "{$id}_enable";
    $this->enable = (isset($GAME_OPT_CONF->$enable) ? $GAME_OPT_CONF->$enable : true);
    $this->caption = $caption;
    $this->explain = $explain;
    $default = "default_{$name}";
    if (isset($GAME_OPT_CONF->$default)) {
      $this->value = $GAME_OPT_CONF->$default;
    }
  }

  static function Text($name, $caption, $explain) {
    return new TextRoomOptionItem($name, $name, 'text', $caption, $explain);
  }
  static function Password($name, $caption, $explain) {
    return new TextRoomOptionItem($name, $name, 'password', $caption, $explain);
  }
  static function Check($name, $caption, $explain) {
    return new CheckRoomOptionItem($name, $name, 'checkbox', 'on', $caption, $explain);
  }
  static function Radio($name, $value, $caption, $explain) {
    $id = empty($value) ? $name : "{$name}_{$value}";
    return new CheckRoomOptionItem($id, $name, 'radio', $value, $caption, $explain);
  }
  static function Selector($name, $label, $caption, $explain) {
    return new SelectorRoomOptionItem($name, $name, $label, $caption, $explain);
  }
  static function RealTime($name, $caption, $explain) {
    return new TimeRoomOptionItem($name, $name, $caption, $explain);
  }
  static function Group($name, $caption) {
    return new RoomOptionItemGroup($name, $caption);
  }

  function Value($value) {
    $this->value = $value;
    return $this;
  }

  function ON() {
    $this->checked = true;
    return $this;
  }
  function OFF() {
    $this->checked = false;
    return $this;
  }

  function Items($items) {
    foreach ($items as $key => $value) {
      $this->Item($key, $value);
    }
    return $this;
  }

  function Item($name, $item = null) {
    if (isset($this->items) && $this->ItemIsAvailable($name)) {
      $this->items[$name] = $item;
    }
    return $this;
  }

  function CollectOverride($method) {
    $this->collect = $method;
    return $this;
  }

  protected function CallCollectOverride(RoomOption $option) {
    if (isset($this->collect)) {
      call_user_func_array(array($this, $this->collect), array($option));
      return true;
    }
    return false;
  }

  function CollectPostParam(RoomOption $option) {
    if (isset($_POST[$this->name]) && !$this->CallCollectOverride($option)) {
      $value = $_POST[$this->name];
      $option->Set($this, $this->name, $value);
    }
  }

  function NotOption(RoomOption $option) {
  }

  function ItemIsAvailable($name) {
    global $GAME_OPT_CONF;
    $enable = "{$name}_enable";
    return isset($GAME_OPT_CONF->$enable) ? $GAME_OPT_CONF->$enable : true;
  }

  abstract function GenerateControl();

  function GenerateView() {
    return <<<HTML
<tr>
<td><label for="{$this->name}">{$this->caption}：</label></td>
<td class="explain">
{$this->GenerateControl()}
</td>
</tr>
HTML;
  }
}

/**
 * チェックボックス型の標準的な村立てオプション項目を提供します。
 */
class CheckRoomOptionItem extends RoomOptionItem {
  var $type;
  var $default;

  function  __construct($id, $name, $type, $value, $caption, $explain) {
    parent::__construct($id, $name, $caption, $explain);
    //ユーザー設定の退避
    if ($type == 'checkbox') {
      $this->checked = $this->value;
    }
    else if ($type == 'radio') {
      /* チェックされているかどうかの識別に使用する */
      $this->default = $this->value;
    }
    $this->type = $type;
    $this->value = $value;
  }

  function CollectPostParam(RoomOption $option) {
    if (isset($_POST[$this->name]) && !$this->CallCollectOverride($option)) {
      $checked = $_POST[$this->name] == $this->value && !empty($this->value);
      $option->Set($this, $this->name, $checked);
    }
  }

  function CollectId(RoomOption $option) {
    $checked = $_POST[$this->name] == $this->value && !empty($this->value);
    $option->Set($this, $this->id, $checked);
  }

  function CollectValue(RoomOption $option) {
    $checked = $_POST[$this->name] == $this->value && !empty($this->value);
    $option->Set($this, $this->value, $checked);
  }

  function CollectKeyValue(RoomOption $option) {
    $checked = $_POST[$this->name] == $this->value && !empty($this->value);
    if ($checked) {
      $option->Set($this, $this->name, $this->value);
    }
  }

  function GenerateControl() {
    $checked = $this->checked || (isset($this->default) && ($this->default == $this->value)) ? ' checked' : '';
    return <<<HTML
<input type="{$this->type}" id="{$this->id}" name="{$this->name}" value="{$this->value}"{$checked}>
({$this->explain})
HTML;
  }
}

/**
 * セレクタ型の村立てオプション項目を提供します。
 */
class SelectorRoomOptionItem extends RoomOptionItem {
  var $label;
  var $items = array();

  function  __construct($id, $name, $label, $caption, $explain) {
    parent::__construct($id, $name, $caption, $explain);
    $this->label = $label;
  }

  function CollectValue(RoomOption $option) {
    $value = $_POST[$this->name];
    if (isset($this->items[$value]) && !empty($value)) {
      $option->Set($this, $value, true);
    }
  }

  function GenerateControl() {
    $items = '';
    foreach ($this->items as $value => $label) {
      if (!is_string($value)) {
        $value = $label;
      }
      $selected = $value == $this->value ? ' selected' : '';
      $items .= "<option value=\"{$value}\" {$selected}>{$label}</option>\n";
    }
    return <<<HTML
<select id="{$this->id}" name="{$this->name}">
<optgroup label="{$this->label}">
{$items}</optgroup>
</select>
<span class="explain">({$this->explain})</span>
HTML;
  }
}

/**
 * テキスト型の村立てオプション項目を提供します。
 */
class TextRoomOptionItem extends RoomOptionItem {
  var $footer;

  function  __construct($id, $name, $type, $caption, $explain) {
    parent::__construct($id, $name, $caption, $explain);
    $this->type = $type;
    $this->value = '';
  }

  function Footer($text) {
    $this->footer = $text;
    return $this;
  }

  function GenerateControl() {
    $footer = isset($this->footer) ? $this->footer : '<span class="explain">'.$this->explain.'</span>';
    return <<<HTML
<input type="{$this->type}" id="{$this->id}" name="{$this->name}" value="{$this->value}">
$footer
HTML;
  }
}


/**
 * 村の時間進行を設定するオプション項目を提供します。
 */
class TimeRoomOptionItem extends RoomOptionItem {
  var $defaultDayTime = 5;
  var $defaultNightTime = 3;

  function  __construct($id, $name, $caption, $explain) {
    parent::__construct($id, $name, $caption, $explain);
    $this->value = 'on';
  }

  function Day($minutes) {
    $this->defaultDayTime = $minutes;
    return $this;
  }

  function Night($minutes) {
    $this->defaultNightTime = $minutes;
    return $this;
  }

  function Format($format) {
    $this->explain = $format;
    return $this;
  }

  function CollectPostParam(RoomOption $option) {
    if (isset($_POST[$this->name]) && !$this->CallCollectOverride($option)) {
      $value = $_POST[$this->name];
      if ($value == 'on') {
        global $TIME_CONF;
        $day = isset($_POST["{$this->name}_day"]) ? $_POST["{$this->name}_day"] : $TIME_CONF->default_day;
        $night = isset($_POST["{$this->name}_night"]) ? $_POST["{$this->name}_night"] : $TIME_CONF->default_night;
        $option->Set($this, $this->name, array(is_numeric($day) ? (int)$day : 0, is_numeric($night) ? (int)$night : 0));
      }
      else {
        $option->Set($this, $this->name, false);
      }
    }
  }

  function GenerateControl() {
    $checked = $this->checked ? ' checked' : '';
    return <<<HTML
<input type="checkbox" id="{$this->id}" name="{$this->name}" value="{$this->value}"{$checked}>
({$this->explain}　昼：<input type="text" name="{$this->name}_day" value="{$this->defaultDayTime}" size="2" maxlength="2">分 夜：<input type="text" name="{$this->name}_night" value="{$this->defaultNightTime}" size="2" maxlength="2">分)
</td>

HTML;
  }
}

/**
 * 複数のオプションを一行で表示するためのインフラを提供します。
 */
class RoomOptionItemGroup extends RoomOptionItem {
  var $items = array();

  function  __construct($name, $caption) {
    parent::__construct(null, $name, $caption, '');
  }

  function Item($item) {
    if ($item->enable) {
      $this->items[] = $item;
    }
    return $this;
  }

  function GenerateControl() {
    $result = '';
    foreach ($this->items as $item) {
      $result .= $item->GenerateControl();
      $result .= "<br>\n";
    }
    return $result;
  }
}
