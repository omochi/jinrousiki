<?php
class RoomOptions extends OptionParser {
  //村オプションのカテゴリ
  static $definitions = array();
  static $categories = array();
  static $currentCategory = 'general';

  static function Category($category) {
    self::$categories[$category] = array();
    self::$currentCategory = $category;
  }

  static function End() {
    self::$currentCategory = null;
  }

  static function Enable($item) {
    if (isset(self::$categories[self::$currentCategory])) {
      self::$definitions[$item->name] = $item;
      self::$categories[self::$currentCategory][] = $item->name;
    }
  }

  static function Disable($item) {}

  static function OutputCategory($category, $border = false) {
    OutputView(self::$categories[$category], $border);
  }
  static function OutputView($items = 'all', $border = false) {
    if ($items == 'all') {
      $items = array_keys(self::$definitions);
    }
    else if (!is_array($items)) {
      $items = array_flip(func_get_args());
    }
    foreach(self::$categories as $category) {
      $target = array_intersect($category, $items);
      if (!empty($target)) {
        foreach ($target as $item) {
          echo(self::$definitions[$item]->GenerateView());
        }
        if ($border) {
          echo '<tr><td colspan="2"><hr></td></tr>';
        }
      }
    }
  }

	function  __construct($value = '') {
		parent::__construct($value);
	}

	function LoadRequestParams() {
		$row = '';
    foreach (array_intersect_key(self::$definitions, $_REQUEST) as $key => $def) {
		  $def->CollectRequestParam($this);
		}
		$this->row = $row;
	}
}
