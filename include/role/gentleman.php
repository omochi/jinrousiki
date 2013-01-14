<?php
/*
  ◆紳士 (gentleman)
  ○仕様
  ・発言変換：完全置換 (生存者ユーザ名 (ランダム) + サーバ設定)
*/
class Role_gentleman extends Role {
  function ConvertSay() {
    if (mt_rand(1, 100) > GameConfig::GENTLEMAN_RATE) return false; //スキップ判定

    $stack = DB::$USER->GetLivingUsers(); //生存者のユーザ名を取得
    unset($stack[array_search($this->GetUname(), $stack)]); //自分を削除
    $target = DB::$USER->GetHandleName(Lottery::Get($stack), true);
    $this->SetStack(sprintf(Message::${$this->role}, $target), 'say');
    return true;
  }
}
