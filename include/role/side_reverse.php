<?php
/*
  �������º� (side_reverse)
  ������
  ����ʬ��ȯ������ñ�̤Ǻ����������ؤ��
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_side_reverse extends Role{
  function Role_side_reverse(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    $result = '';
    $line = array();
    $count = mb_strlen($sentence);
    for($i = 0; $i < $count; $i++){
      $str = mb_substr($sentence, $i, 1);
      if($str == "\n"){
	if(count($line) > 0) $result .= implode('', array_reverse($line));
	$result .= $str;
	$line = array();
      }
      else{
	$line[] = $str;
      }
    }
    if(count($line) > 0) $result .= implode('', array_reverse($line));
    $sentence = $result;
  }
}
