<?php
class ScriptInfo{
  //スクリプト群のバージョン
  var $version = 'Ver. 1.4.0 beta2';

  // スクリプト群の最終更新日
  var $last_update = '2010/02/05 (Fri) 07:29';

  //パッケージ化したときの Revision
  var $revision = 152;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
