<?php
/*
  �������º� (invisible)
  ������
  ����ʬ��ȯ���ΰ����������Ψ�Ǿä���
  ��Ƚ��ϰ�ʸ����ǡ����򡢥��֡�����ʸ�����оݳ�
  ����Ψ�ν���ͤ� GameConfig->invisible_rate �������, ��ʸ����� 1% ���åפ���
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_invisible extends Role{
  function Role_invisible(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;

    $result = '';
    $regex  = "/[\t\r\n ��]/";
    $rate   = $GAME_CONF->invisible_rate;
    $count  = mb_strlen($sentence);
    for($i = 0; $i < $count; $i++){
      $str = mb_substr($sentence, $i, 1);
      if(preg_match($regex, $str)){
	$result .= $str;
	continue;
      }

      if(mt_rand(1, 100) <= $rate)
	$result .= (strlen($str) == 2 ? '��' : '&nbsp;');
      else
	$result .= $str;
      if(++$rate > 100) break;
    }
    $sentence = $result;
  }
}
