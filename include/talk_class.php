<?php
class SystemMessage {
  var $kick_do = '%s は %s にKick投票しました。';
  var $vote_do = '%s は %s に処刑投票しました。';
  var $wolf_eat = '%s ら人狼は %s に狙いをつけました。';
  var $mage_do = '%s は %s を占います。';
  var $guard_do = '%s は %s を護衛します。';
  var $cupid_do = '%s は %s に処刑投票しました。';

  var $morning = '朝日が昇り、%d日目の朝がやってきました。';
  var $night = '日が落ち、暗く静かな夜がやってきました。';
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
        //この式はtalk.locationの値が'beforegame', 'aftergame', 'day', 'night'の４つのみであるという仕様に依存する。
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
	return $this->player->handle_name . 'は' . $target . $MESSAGE->$action;
      }
    }
    return $this->sentence;
  }
}
?>
