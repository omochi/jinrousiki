<?php
class ScriptInfo{
  //パッケージのバージョン
  var $version = 'Ver. 1.4.0 beta6';

  //パッケージの最終更新日
  var $last_update = '2010/03/10 (Wed) 08:17';

  //パッケージ化した時の Revision
  var $revision = 171;

  //PHP + パッケージのバージョン情報を出力する
  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION,
		  $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
