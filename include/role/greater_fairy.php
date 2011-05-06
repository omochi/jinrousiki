<?php
/*
  ◆大妖精 (greater_fairy)
  ○仕様
  ・悪戯：文頭に妖精・春妖精・夏妖精・秋妖精・冬妖精相当のいずれかを追加する
*/
class Role_greater_fairy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $MESSAGE;
    $stack = array($MESSAGE->common_talk, '春ですよー', '夏ですよー', '秋ですよー', '冬ですよー');
    $sentence = GetRandom($stack) . $sentence;
  }
}
