<?php
/*
  ◆無口 (silent)
  ○仕様
  ・発言変換：文字数制限 (GameConfig->silent_length で定義)
*/
class Role_silent extends Role {
  function __construct(){ parent::__construct(); }

  function ConvertSay(){
    $str = $this->GetStack('say');
    $len = GameConfig::$silent_length;
    if (mb_strlen($str) > $len) $this->SetStack(mb_substr($str, 0, $len) . '……', 'say');
  }
}
