<?php
/**
 * リアルタイム制(real_time)
 * ゲームの進行時間を指定します。昼と夜の発言時間を分単位で指定します。
 * offに設定すると発言数によってシーンが切り替わります。
 * @author enogu
 */
class Option_real_time extends RoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption(){ return 'リアルタイム制'; }

  function GetExplain(){ return '制限時間が実時間で消費されます'; }

  function LoadMessages(){
    parent::LoadMessages();
    $this->defaultDayTime   = TimeConfig::$default_day;
    $this->defaultNightTime = TimeConfig::$default_night;
  }

  function CollectPostParam(RoomOption $option) {
    if (isset($_POST[$this->name])) {
      $value = $_POST[$this->name];
      if ($value == 'on') {
        $day   = isset($_POST["{$this->name}_day"])   ? $_POST["{$this->name}_day"] :
	  TimeConfig::$default_day;
        $night = isset($_POST["{$this->name}_night"]) ? $_POST["{$this->name}_night"] :
	  TimeConfig::$default_night;
        $option->Set($this, $this->name,
		     array(is_numeric($day) ? (int)$day : 0, is_numeric($night) ? (int)$night : 0));
      }
      else {
        $option->Set($this, $this->name, false);
      }
    }
  }
}
