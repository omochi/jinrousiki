<?php
/*
  ◆妖狐追加 (fox)
  ○仕様
  ・配役：村人 → 妖狐
*/
class Option_fox extends Option{
  function __construct(){ parent::__construct(); }

  function SetRole(&$list, $count){
    global $CAST_CONF;
    if($count >= $CAST_CONF->{$this->name} && $list['human'] > 0){
      $list['human']--;
      $list[$this->name]++;
    }
  }
}
