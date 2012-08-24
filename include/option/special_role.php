<?php
/*
  ◆特殊配役モード (セレクタ)
  ○仕様
  ・モードリスト：GameOptionCofing::$special_role_list
*/
class Option_special_role extends SelectorRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;
  public $on_change  = ' onChange="change_special_role()"';
  public $javascript = "change_option_display('chaos', 'none')";

  function __construct() {
    parent::__construct();
    $this->form_list = GameOptionConfig::${$this->source};
  }

  function GetCaption() { return '特殊配役モード'; }

  function GetExplain() {
    return '詳細は<a href="info/game_option.php">ゲームオプション</a>を参照してください';
  }
}
