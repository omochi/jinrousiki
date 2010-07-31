<?php
class ScriptInfo{
  //パッケージのバージョン
  var $version = 'Ver. 1.4.0 beta15';

  //パッケージの最終更新日
  var $last_update = '2010/07/31 (Sat) 19:37';

  //パッケージ化した時の Revision
  var $revision = 196;

  //PHP + パッケージのバージョン情報を出力する
  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION,
		  $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
