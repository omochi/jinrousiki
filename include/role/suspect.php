<?php
/*
  ◆不審者 (suspect)
  ○仕様
  ・役職表示：村人
  ・発言変換：完全置換 (人狼遠吠え or サーバ設定)
*/
class Role_suspect extends Role {
  public $display_role = 'human';
  function __construct(){ parent::__construct(); }

  function ConvertSay(){
    global $MESSAGE;

    if (! DB::$ROOM->IsDay()) return false; //スキップ判定
    $rate = GameConfig::$cute_wolf_rate * (DB::$ROOM->IsEvent('boost_cute') ? 5 : 1);
    //PrintData($rate);
    if (mt_rand(1, 100) > $rate) return false;
    $this->SetStack($MESSAGE->cute_wolf != '' ? $MESSAGE->cute_wolf : $MESSAGE->wolf_howl, 'say');
    return true;
  }
}
