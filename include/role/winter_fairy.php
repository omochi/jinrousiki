<?php
/*
  ◆冬妖精 (winter_fairy)
  ○仕様
  ・悪戯：文頭に「冬ですよー」を追加する
*/
class Role_winter_fairy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = '冬ですよー' . $str;
  }
}
