<?php
/*
  �������º� (weekly)
  ������
  ����ʬ��ȯ���ΰ����������ν��֤˽��äƽ۴��Ѵ������
  ���Ѵ��ơ��֥�� GameConfig->weekly_replace_list ���������
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_weekly extends Role{
  function Role_weekly(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;
    $sentence = strtr($sentence, $GAME_CONF->weekly_replace_list);
  }
}
