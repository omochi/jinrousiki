<?php
/*
  このファイルは消去予定です。
  新しい情報を定義する場合は config/version.php を編集してください。
*/
class ScriptInformation{
  //スクリプト群のバージョン
  var $version = 'Ver. 1.4.0 beta1';

  // スクリプト群の最終更新日
  var $last_update = '2010/01/28 (Thu) 21:33';

  //パッケージ化したときの Revision
  var $revision = 149;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
