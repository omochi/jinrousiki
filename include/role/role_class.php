<?php
class Role extends DocumentBuilderExtension{
  var $actor;

  function Role($user){
    $this->actor = $user;
  }

  function __construct($user){
    $this->Role($user);
  }

  function Ignored(){
    global $ROOM, $SELF;
    return ! ($ROOM->IsPlaying() && $SELF->IsLive());
  }

  function SameUser($userinfo){
    $result = strpos($userinfo, $this->actor->handle_name);
    shot("<p>{$userinfo}����{$this->actor->handle_name}��õ���ޤ���-> ���:{$result}</p>");
    return ($result !== false && $result >= 0);
  }

  function OutputRoleNotification($writer){ return 'not implemented'; }
  function OutputVoteNotification($writer){ return 'not implemented'; }
  function FilterWords($talk, $date, $situation){ return false; }

  function Say($words, $volume){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function WriteLastWords($content){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ��������� //EntryLastWords() ����ʤ��Ρ�
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
