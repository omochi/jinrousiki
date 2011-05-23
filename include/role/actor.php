<?php
/*
  ◆役者 (actor)
  ○仕様
  ・自分の発言の一部が入れ替わる
  ・変換リストは GameConfig->actor_replace_list で定義する
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_actor extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$str){
    global $GAME_CONF;
    $str = strtr($str, $GAME_CONF->actor_replace_list);
  }
}
