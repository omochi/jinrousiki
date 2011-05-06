<?php
/*
  ◆狼少年 (liar)
  ○仕様
  ・自分の発言の一部が反転される
  ・発動率は GameConfig->liar_rate で定義する
  ・変換テーブルは GameConfig->liar_replace_list で定義する
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_liar extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;
    if(mt_rand(1, 100) <= $GAME_CONF->liar_rate){
      $sentence = strtr($sentence, $GAME_CONF->liar_replace_list);
    }
  }
}
