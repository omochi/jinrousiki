<?php
class ScriptInfo{
  //������ץȷ��ΥС������
  var $version = 'Ver. 1.4.0 beta2';

  // ������ץȷ��κǽ�������
  var $last_update = '2010/02/05 (Fri) 07:29';

  //�ѥå������������Ȥ��� Revision
  var $revision = 152;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version . '(Rev. ' . $this->revision . ')',
		  'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
