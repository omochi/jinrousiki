<?php
/*
  ◆サブ役職制限 (セレクタ)
*/
class Option_sub_role_limit extends SelectorRoomOptionItem {
  public $type = 'group';

  function __construct() {
    parent::__construct();
    $stack = array('no_sub_role' => 'no_sub_role');
    foreach (array('easy', 'normal', 'hard') as $name) {
      $stack[$name] = sprintf('%s_%s', $this->name, $name);
    }
    foreach ($stack as $name => $class) {
      $filter = OptionManager::GetClass($class);
      if (isset($filter) && $filter->enable) $this->item_list[$class] = $name;
    }
  }

  function GetCaption() { return 'サブ役職制限'; }

  function GetItems() {
    $items = array('no_sub_role' => OptionManager::GetClass('no_sub_role'),
		   'easy'        => OptionManager::GetClass('sub_role_limit_easy'),
		   'normal'      => OptionManager::GetClass('sub_role_limit_normal'),
		   'hard'        => OptionManager::GetClass('sub_role_limit_hard'),
		   ''            => OptionManager::GetClass('sub_role_limit_none'));
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
