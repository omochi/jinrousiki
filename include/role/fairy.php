<?php
/*
  ◆妖精 (fairy)
  ○仕様
  ・悪戯：文頭に共有者の囁きを追加する
*/
class Role_fairy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    global $MESSAGE;
    $str = $MESSAGE->common_talk . $str;
  }
}
