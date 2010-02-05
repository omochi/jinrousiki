<?php
class Option {
  var $options = array();

  function __construct($value) {
    foreach(explode(' ', $value) as $option) {
      $items = explode(':', $option);
      if (count($items) == 1) {
        $this->options[$items[0]] = true;
      }
      else {
        $this->options[$items[0]] = array_slice($items, 1);
      }
    }
  }
  function Option($value) {
    self::__construct($value);
    //キャッシュの生成
    foreach($this->options as $name => $value) {
      $this->__get($name);
    }
  }

  function __get($name) {
    return $this->$name = $array_key_exists($name, $this->options) ? $this->options[$name] : false;
  }
  function __set($name, $value) {
    if ($value === false) {
      unset($this->options[$name]);
    }
    else {
      $this->options[$name] = $value;
    }
  }

  function __toString() {
    $result = '';
    foreach($this->option as $name => $value) {
      $result = ' ' . is_array($value) ? "{$name}:" . implode(':', $value) : $name;
    }
    return $result;
  }
}
