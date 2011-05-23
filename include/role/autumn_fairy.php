<?php
/*
  ◆秋妖精 (autumn_fairy)
  ○仕様
  ・悪戯：文頭に「秋ですよー」を追加する
*/
class Role_autumn_fairy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = '秋ですよー' . $str;
  }
}
