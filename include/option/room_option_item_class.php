<?php
abstract class RoomOptionItem {
  var $name;
  var $value;
  var $caption;
  var $explain;

  function  __construct($name, $caption, $explain) {
    $this->name = $name;
    $this->caption = $caption;
    $this->explain = $explain;
  }

  static function Text($name, $caption, $explain) {
    return new TextRoomOptionItem($name, 'text', $caption, $explain);
  }
  static function Password($name, $caption, $explain) {
    return new TextRoomOptionItem($name, 'password', $caption, $explain);
  }
  static function Check($name, $caption, $explain) {
    return new CheckRoomOptionItem($name, 'checkbox', $caption, $explain);
  }
  static function Radio($name, $caption, $explain) {
    return new CheckRoomOptionItem($name, 'radio', $caption, $explain);
  }
  static function Selector($name, $label, $caption, $explain) {
    return new SelectorRoomOptionItem($name, $label, $caption, $explain);
  }
  static function RealTime($name, $caption, $explain) {
    return new TimeRoomOptionItem($name, $caption, $explain);
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
    $this->items = $items;
    return $this;
  }

  function Item($name, $item = null) {
    if (isset($this->items)) {
      $this->items[$name] = $item;
    }
    return $this;
  }

  function CollectRequestParam(OptionParser $option) {
    if (isset($_REQUEST[$this->name])) {
      $value = $_REQUEST[$this->name];
      $option->__set($this->name, $value);
    }
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

  function IsChecked() {
    if (isset($this->checked)) {
      return $this->checked;
    }
    else {
      $value = $this->value;
      return isset($value) ? ($value === true) || ($value == 'on') || ($value == $this->name) : false;
    }
  }
}

/**
 * チェックボックス型の標準的な村立てオプション項目を提供します。
 */
class CheckRoomOptionItem extends RoomOptionItem {
  var $type;

  function  __construct($name, $type, $caption, $explain) {
    parent::__construct($name, $caption, $explain);
    $this->value = 'on';
    $this->type = $type;
    $this->checked = false;
  }

  function CollectRequestParam(OptionParser $option) {
    if (isset($_REQUEST[$this->name])) {
      $value = $_REQUEST[$this->name];
      $option->__set($this->name, $value == $this->value);
    }
  }

  function GenerateControl() {
    $checked = $this->IsChecked() ? ' checked' : '';
    return <<<HTML
<input type="{$this->type}" id="{$this->name}" name="{$this->name}" value="{$this->value}"{$checked}>
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

  function  __construct($name, $label, $caption, $explain) {
    parent::__construct($name, $caption, $explain);
    $this->value = null;
    $this->label = $label;
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
<select id="{$this->name}" name="{$this->name}">
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

  function  __construct($name, $type, $caption, $explain) {
    parent::__construct($name, $caption, $explain);
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
<input type="{$this->type}" id="{$this->name}" name="{$this->name}" value="{$this->value}">
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

  function  __construct($name, $caption, $explain) {
    parent::__construct($name, $caption, $explain);
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

  function CollectRequestParam(OptionParser $option) {
    if (isset($_REQUEST[$this->name])) {
      $value = $_REQUEST[$this->name];
      if ($value == 'on') {
        global $TIME_CONF;
        $day = isset($_REQUEST["{$this->name}_day"]) ? $_REQUEST["{$this->name}_day"] : $TIME_CONF->default_day;
        $night = isset($_REQUEST["{$this->name}_night"]) ? $_REQUEST["{$this->name}_night"] : $TIME_CONF->default_night;
        $option->__set($this->name, array($day, $night));
      }
      else {
        $option->__set($this->name, false);
      }
    }
  }

  function GenerateControl() {
    $checked = $this->IsChecked() ? ' checked' : '';
    return <<<HTML
<input type="checkbox" id="{$this->name}" name="{$this->name}" value="{$this->value}"{$checked}>
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
    parent::__construct($name, $caption, '');
  }

  function Item($item) {
    $this->items[] = $item;
    return $this;
  }

  function CollectRequestParam(OptionParser $option) {
    parent::CollectRequestParam($option);
    foreach ($this->items as $item) {
      if ($item->name != $this->name) {
        $item->CollectRequestParam($option);
      }
    }
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
