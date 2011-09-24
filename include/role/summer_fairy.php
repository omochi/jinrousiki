<?php
/*
  ◆夏妖精 (summer_fairy)
  ○仕様
  ・悪戯：文頭に「夏ですよー」を追加する
*/
RoleManager::LoadFile('fairy');
class Role_summer_fairy extends Role_fairy{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = '夏ですよー' . $str;
  }
}
