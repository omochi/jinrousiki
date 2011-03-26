<?php
/*
  ◆光学迷彩 (invisible)
  ○仕様
  ・自分の発言が一定割合で消える
  ・判定は一文字毎で、空白、タブ、改行文字は対象外
  ・割合は GameConfig->invisible_rate で定義する
  ・ゲームプレイ中で生存時のみ有効 (呼び出し関数側で対応)
*/
class Role_invisible extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;

    $result = '';
    $regex  = "/[\t\r\n\s]/";
    $count  = mb_strlen($sentence);
    $stack  = range(0, $count);
    shuffle($stack);
    $target_stack = array_slice($stack, 0, ceil($count * $GAME_CONF->invisible_rate / 100));
    for($i = 0; $i < $count; $i++){
      $str = mb_substr($sentence, $i, 1);
      if(preg_match($regex, $str))
	$result .= $str;
      elseif(in_array($i, $target_stack))
	$result .= (strlen($str) == 2 ? '　' : '&nbsp;');
      else
	$result .= $str;
    }
    $sentence = $result;
  }
}
