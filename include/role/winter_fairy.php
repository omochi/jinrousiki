<?php
/*
  ◆冬妖精 (winter_fairy)
  ○仕様
  ・悪戯：文頭に「冬ですよー」を追加する
*/
RoleManager::LoadFile('fairy');
class Role_winter_fairy extends Role_fairy{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = '冬ですよー' . $str;
  }
}
