<?php
/*
  ����� (actor)
  ������
  ����ʬ��ȯ���ΰ����������ؤ��
  ��������ץ쥤�����¸���Τ�ͭ�� (�ƤӽФ��ؿ�¦���б�)
*/
class Role_actor extends Role{
  function Role_actor(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSay(&$sentence){
    global $GAME_CONF;
    $sentence = strtr($sentence, $GAME_CONF->actor_replace_list);
  }
}
