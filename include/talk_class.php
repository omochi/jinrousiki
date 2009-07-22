<?php
class SystemMessage {
  var $kick_do = '%s �� %s ��Kick��ɼ���ޤ�����';
  var $vote_do = '%s �� %s �˽跺��ɼ���ޤ�����';
  var $wolf_eat = '%s ���ϵ�� %s ��������Ĥ��ޤ�����';
  var $mage_do = '%s �� %s ���ꤤ�ޤ���';
  var $guard_do = '%s �� %s ���Ҥ��ޤ���';
  var $cupid_do = '%s �� %s �˽跺��ɼ���ޤ�����';

  var $morning = 'ī�������ꡢ%d���ܤ�ī����äƤ��ޤ�����';
  var $night = '����������Ť��Ť����뤬��äƤ��ޤ�����';
}

$MESSAGES =& new SystemMessage();

class Talk{
  function ParseParameters(){
    global $USERS;
    $this->player = $USERS->ByUname($this->uname);
    $this->day_night = $day_night = strtok($this->location, ' ');
    $this->role = $role = strtok(' ');
    $is_night = $day_night == 'night';
    $this->is_wolf = $is_night && $role == 'wolf' ;
    $this->is_common = $is_night && $role == 'common'; 
    LineToBR($this->sentence);
  }

  function ParseSentence(){
    global $USERS, $MESSAGE;
    $action = strtolower(strtok($this->sentence, "\t"));
    if ($this->player->is_system){
      switch ($this->uname){
      case 'system':
        $this->is_system = true;
        $this->action = $action;
        switch ($action){
        case 'morning':
          $morning_date = strtok("\t");
          return '&lt; &lt; ' . $MESSAGE->morning_header . ' ' . $morning_date . $MESSAGE->morning_footer . ' &gt; &gt;';
        case 'night':
          return '&lt; &lt; '.$MESSAGE->$action . ' &gt; &gt;';
        default:
          return $this->sentence;
        }
      case 'dummy_boy':
        //���μ���talk.location���ͤ�'beforegame', 'aftergame', 'day', 'night'�Σ��ĤΤߤǤ���Ȥ������ͤ˰�¸���롣
        $this->is_system = strpos($this->day_night, 'game') === false; 
        return $this->sentence;
      }
    } 
    if (strpos($this->location, 'system') !== false && isset($MESSAGE->$action)){
      $this->is_system = true;
      $this->action = $action;
      switch ($action){
      case 'objection':
	return $this->player->handle_name . ' ' . $MESSAGE->$action;
      case 'kick_do':
      case 'vote_do':
      case 'wolf_eat':
      case 'mage_do':
      case 'guard_do':
      case 'cupid_do':
        $target = strtok("");
	return $this->player->handle_name . '��' . $target . $MESSAGE->$action;
      }
    }
    return $this->sentence;
  }
}
?>