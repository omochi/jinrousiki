<?php
class ScriptInformation{
  //スクリプト群のバージョン
  var $version = 'Ver. 1.4.0 alpha24';

  // スクリプト群の最終更新日
  var $last_update = '2010/01/13 (Wed) 21:43';

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
