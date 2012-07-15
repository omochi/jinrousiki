<?php
/*
  ◆配役を通知する (セレクタ)
*/
class Option_chaos_open_cast extends SelectorRoomOptionItem {
  public $type = 'group';

  function __construct() {
    parent::__construct();
    foreach (array('camp', 'role', 'full') as $name) {
      $class  = sprintf('%s_%s', $this->name, $name);
      $filter = OptionManager::GetClass($class);
      if (isset($filter) && $filter->enable) $this->item_list[$class] = $name;
    }
  }

  function GetCaption() { return '配役を通知する'; }

  function GetItems() {
    $items = array(''     => OptionManager::GetClass('chaos_open_cast_none'),
		   'camp' => OptionManager::GetClass('chaos_open_cast_camp'),
		   'role' => OptionManager::GetClass('chaos_open_cast_role'),
		   'full' => OptionManager::GetClass('chaos_open_cast_full'));
    foreach ($items as $key => $item) {
      $item->form_name  = $this->form_name;
      $item->form_value = $this->item_list[$item->name];
    }
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }

  function LoadPost() {
    if (! isset($_POST[$this->name])) return false;
    $post = $_POST[$this->name];

    foreach ($this->item_list as $option => $value) {
      if ($value == $post) {
	RQ::$get->$option = true;
	array_push(RoomOption::${$this->group}, $option);
	break;
      }
    }
  }
}
