<?php
/*
  ◆虹色迷彩 (rainbow)
  ○仕様
  ・自分の発言の一部が虹の色の順番に従って循環変換される
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_rainbow extends Role{
  var $replace_list = array('赤' => '橙', '橙' => '黄', '黄' => '緑', '緑' => '青',
			    '青' => '藍', '藍' => '紫', '紫' => '赤');

  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = strtr($str, $this->replace_list);
  }
}
