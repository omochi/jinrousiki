<?php
class ScriptInformation{
  //������ץȷ��ΥС������
  var $version = 'Ver. 1.4.0 alpha24';

  // ������ץȷ��κǽ�������
  var $last_update = '2010/01/26 (Tue) 03:53';

  //�ѥå������������Ȥ��� Revision
  var $revision = 140;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
