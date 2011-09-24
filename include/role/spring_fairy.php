<?php
/*
  ◆春妖精 (spring_fairy)
  ○仕様
  ・悪戯：文頭に「春ですよー」を追加する
*/
RoleManager::LoadFile('fairy');
class Role_spring_fairy extends Role_fairy{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = '春ですよー' . $str;
  }
}
