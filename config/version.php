<?php
class ScriptInfo{
  //�ѥå������ΥС������
  var $version = 'Ver. 1.4.0 beta3';

  //�ѥå������κǽ�������
  var $last_update = '2010/02/14 (Sun) 20:59';

  //�ѥå��������������� Revision
  var $revision = 157;

  //PHP + �ѥå������ΥС������������Ϥ���
  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION,
		  $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
