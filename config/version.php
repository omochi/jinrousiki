<?php
class ScriptInfo{
  //�ѥå������ΥС������
  var $version = 'Ver. 1.4.0 beta9';

  //�ѥå������κǽ�������
  var $last_update = '2010/04/07 (Wed) 06:39';

  //�ѥå��������������� Revision
  var $revision = 178;

  //PHP + �ѥå������ΥС������������Ϥ���
  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION,
		  $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
