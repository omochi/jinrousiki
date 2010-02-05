<?php
class Table {
  var $name;
  var $default_charset;
  var $engine;
  var $fields = array();
  var $indices = array();

  function __construct() {

  }
  function Table() {
    $this->construct();
  }

  function Exists($use_cache = true) {
    if ($use_cache) {
      return isset($this->_exists) ? $this->_exists : ($this->_exists = $this->Exists(false));
    }
    $r_list = mysql_query("SHOW TABLES LIKE {$this->name}");
    $totalRows = mysql_num_rows($r_list);
    for ($row = 0; $row < $totalRows; $row++) {
      if (mysql_result($row) == $this->name)
        return true;
    }
    return false;
  }

  function Update() {

  }
}
?>
