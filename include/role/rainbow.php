<?php
/*
  �������º� (rainbow)
  ������
  ����ʬ��ȯ���ΰ��������ο��ν��֤˽��äƽ۴��Ѵ������
  ���Ѵ��ơ��֥�� GameConfig->rainbow_replace_list ���������
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_rainbow extends Role{
  function Role_rainbow(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;
    $sentence = strtr($sentence, $GAME_CONF->rainbow_replace_list);
  }
}
