<?php
class Role extends DocumentBuilder {
  var $actor;

  function Role($user){
    $this->actor = $user;
  }

  function __construct($user){
    $this->Role($user);
  }

  function Ignored(){
    global $day_night;
    return $day_night == 'beforegame' || $day_night == 'aftergame';
  }

  function SameUser($userinfo){
    $result = strpos($userinfo, $this->actor->handle_name);
    shot ("<p>{$userinfo}����{$this->actor->handle_name}��õ���ޤ���-> ���:{$result}</p>");
    return ($result !== false) && (0 < $result);
  }

  function Say($words, $volume){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function WriteLastWords($content){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function Object(){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function Vote($target){
    //TODO: �ʲ�����ɼ�ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function Action($target){
    //TODO: �ʲ�����ɼ�ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }
}
?>