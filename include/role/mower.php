<?php
/*
  ◆草刈り (mower)
  ○仕様
  ・自分の発言から草が消える
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_mower extends Role{
  public $replace_list = array('w' => '', 'ｗ' => '', 'W' => '', 'Ｗ' => '');

  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    $str = strtr($str, $this->replace_list);
  }
}
