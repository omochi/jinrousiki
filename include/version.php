<?php
/*
  ���Υե�����Ͼõ�ͽ��Ǥ���
  ��������������������� config/version.php ���Խ����Ƥ���������
*/
class ScriptInformation{
  //������ץȷ��ΥС������
  var $version = 'Ver. 1.4.0 beta1';

  // ������ץȷ��κǽ�������
  var $last_update = '2010/01/28 (Thu) 21:33';

  //�ѥå������������Ȥ��� Revision
  var $revision = 149;

  function OutputVersion(){
    $list = array('PHP Ver. ' . PHP_VERSION, $this->version, 'LastUpdate: ' . $this->last_update);
    echo implode(', ', $list);
  }
}
?>
