<?php
/*
  ◆埋毒者登場 (poison)
  ○仕様
  ・配役：村人2 → 埋毒者1・人狼1
*/
class Option_poison extends CheckRoomOptionItem {
  function GetCaption() { return '埋毒者登場'; }

  function GetExplain() {
    return '処刑されたり狼に食べられた場合、道連れにします [村人2→埋毒1・人狼1]';
  }

  function SetRole(array &$list, $count) {
    $role = 'human';
    if ($count >= CastConfig::${$this->name} && isset($list[$role]) && $list[$role] > 1) {
      OptionManager::Replace($list, $role, $this->name);
      OptionManager::Replace($list, $role, 'wolf');
    }
  }
}
