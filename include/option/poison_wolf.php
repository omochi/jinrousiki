<?php
/*
  ◆毒狼登場 (poison_wolf)
  ○仕様
  ・配役：人狼 → 毒狼 / 村人 → 薬師
*/
class Option_poison_wolf extends CheckRoomOptionItem {
  function GetCaption() { return '毒狼登場'; }

  function GetExplain() {
    return '処刑時にランダムで村人一人を巻き添えにする狼です<br>' .
      '　　　[人狼1→毒狼1 / 村人1→薬師1]';
  }

  function SetRole(array &$list, $count) {
    if ($count >= CastConfig::${$this->name} &&
	isset($list['wolf']) && $list['wolf'] > 0 && isset($list['human']) && $list['human'] > 0) {
      OptionManager::Replace($list, 'wolf', $this->name);
      OptionManager::Replace($list, 'human', 'pharmacist');
    }
  }
}
