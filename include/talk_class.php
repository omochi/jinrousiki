<?php
class Talk{
  function ParseCompoundParameters(){
    $result = array('name'=>'say');
    $action = strtolower(strtok($this->sentence, "\t"));
    switch ($this->uname){
    case 'system':
      switch ($action){
      case 'morning':
        $result['name'] = 'daybreak';
        $result['date'] = strtok("\t");
        $result['situation'] = 'day';
        break;
      case 'night':
        $result['name'] = 'sunset';
        $result['date'] = strtok("\t");
        $result['situation'] = 'night';
        break;
      default:
        $result['name'] = 'system_message';
        break;
      }
      break;
    case 'dummy_boy':
      $result['name'] = 'system_talk';
      break;
    default:
      list($day_night, $type) = explode(' ', $this->location);
      if ($type == 'system'){
        global $USERS, $MESSAGE;
        $player = $USERS->ByUname($this->uname);
        $result['from'] = $player->handle_name;
        switch ($action){
        case 'objection':
          $result['name'] = 'objection';
          break;
        case 'kick_do':
        case 'vote_do':
        case 'wolf_eat':
        case 'mage_do':
        case 'guard_do':
        case 'cupid_do':
          $result['name'] = 'vote';
          $result['type'] = $action;
          $result['to'] = strtok("");
          break;
        }
      }
      else {
        $this->day_night = $day_night;
        $this->type = $type;
      }
      break;
    }
    $this->event = $result;
  }

  function GetEvent(){
    return $this->event;
  }
}
?>
