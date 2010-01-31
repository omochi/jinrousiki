<?php
class ScriptInfo{
  //スクリプト群のバージョン
  var $version = 'Ver. 1.4.0 beta1';

  // スクリプト群の最終更新日
  var $last_update = '2010/01/31 (Sun) 03:03';

  //パッケージ化したときの Revision
  var $revision = 149;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
