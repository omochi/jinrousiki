<?php
class ScriptInfo{
  //������ץȷ��ΥС������
  var $version = 'Ver. 1.4.0 beta1';

  // ������ץȷ��κǽ�������
  var $last_update = '2010/01/31 (Sun) 03:03';

  //�ѥå������������Ȥ��� Revision
  var $revision = 149;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
