<?php
if($DEBUG_MODE){
  //�����Ȥȥ��ƥ������ꤷ�ơ������˿������Ԥ��ɲä��ޤ���
  //����
  //$comment : �������ɲä����å����������Τ���ꤷ�ޤ���
  //$category : �������ɲä���ǡ�����ʬ��̾����ꤷ�ޤ������ΰ����Ͼ�ά��ǽ�Ǥ���
  //	���ꤷ�ʤ��ä���硢�����ͤȤ���'general'�����ꤵ��ޤ���
  function shot($comment, $category = 'general'){
    global $PAPARAZZI;
    return $PAPARAZZI->shot($comment, $category);
  }

  //�ƥ����оݤ�ư���Ƥ��餳�δؿ����ƤФ��ޤǤλ��֤��¬������̤��������ޤ���
  //����
  //$label : ¬����֤˥�٥���դ��ޤ������ΰ����Ͼ�ά��ǽ�Ǥ������ꤷ�ʤ��ä���硢��٥��ɽ������ޤ���
  function InsertBenchResult($label = false){
    global $PAPARAZZI;
    $PAPARAZZI->InsertBenchResult($label);
  }

  //�ȥ졼���������������ޤ���
  function InsertLog(){
    global $PAPARAZZI;
    $PAPARAZZI->InsertLog();
  }

  //�ȥ졼�������ν���ʸ�����������ޤ���
  function CollectLog($force = false){
    global $PAPARAZZI;
    return $PAPARAZZI->CollectLog($force);
  }

  //�ȥ졼��������ǡ����١����˽񤭹��ߤޤ���
  function SaveLog($room_no, $uname, $action){
    global $PAPARAZZI;
    $PAPARAZZI->save($room_no, $uname, $action);
  }
}
else{
  //�ǥХå��⡼�ɤǤʤ���硢���δؿ�������󶡤���ޤ���
  function shot($comment, $category = 'general'){ return $comment; }
  function InsertBenchResult(){}
  function InsertLog(){}
  function CollectLog($force = false){}
  function SaveLog($room_no, $uname, $action){}
}