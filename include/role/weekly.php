<?php
/*
  ◆七曜迷彩 (weekly)
  ○仕様
  ・自分の発言の一部が曜日の順番に従って循環変換される
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_weekly extends Role{
  public $replace_list = array('月' => '火', '火' => '水', '水' => '木', '木' => '金',
			       '金' => '土', '土' => '日', '日' => '月');

  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = strtr($str, $this->replace_list);
  }
}
