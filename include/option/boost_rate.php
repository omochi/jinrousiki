<?php
class Option_boost_rate extends SelectorRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return '出現率変動モード'; }

  function GetExplain() { return '役職の出現率に補正がかかります'; }

  function LoadMessages() {
    parent::LoadMessages();
    $this->label = 'モード名';
  }
}
