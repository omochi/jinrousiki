<?php
class Talk{
  var $is_system;

/* ���Υ����ɤ�game_functions.php��OutputTalk�Υ�ե�������󥰤��ʹԤ���ޤǰ��Ū����뤵��Ƥ��ޤ���(2009-08-02 enogu)
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
*/
}
?>
