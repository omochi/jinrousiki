<?php
/*
  ��̵�� (silent)
  ������
  ����ʬ��ȯ��������ʸ������Ķ�����餽��ʹߤ��ä���
  ��ʸ�������¤� GameConfig->silent_length �����
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_silent extends Role{
  function Role_silent(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;
    if(mb_strlen($sentence) > $GAME_CONF->silent_length){
      $sentence = mb_substr($sentence, 0, $GAME_CONF->silent_length) . '�ġ�';
    }
  }
}
