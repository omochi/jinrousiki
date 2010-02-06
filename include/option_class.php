<?php
class OptionManager{
  var $options = array();

  function OptionManager($value){ $this->__construct($value); }

  function __construct($value) {
    $list = explode(' ', $value);
    foreach($list as $option){
      $items = explode(':', $option);
      $this->options[$items[0]] = count($items) > 1 ? array_slice($items, 1) : true;
    }
  }

  function __get($name) {
    $this->$name = array_key_exists($name, $this->options) ? $this->options[$name] : false;
    return $this->$name;
  }

  function __set($name, $value) {
    if($value === false){
      unset($this->options[$name]);
    }
    else{
      $this->options[$name] = $value;
    }
  }

  function __toString() {
    $result = '';
    foreach($this->option as $name => $value){
      $result = ' ' . is_array($value) ? "{$name}:" . implode(':', $value) : $name;
    }
    return $result;
  }

  function Option($value) {
    $this->__construct($value);
    //キャッシュの生成
    foreach($this->options as $name => $value) {
      $this->__get($name);
    }
  }
}
