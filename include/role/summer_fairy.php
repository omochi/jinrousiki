<?php
/*
  ◆夏妖精 (summer_fairy)
  ○仕様
  ・悪戯：文頭に「夏ですよー」を追加する
*/
class Role_summer_fairy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    $sentence = '夏ですよー' . $sentence;
  }
}
