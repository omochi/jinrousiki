<?php
require_once(dirname(__FILE__).'/game_base.php');

class GameHeavenFormat extends GameBaseFormat {
  function LoadTalk() {
    $this->talk_resource = mysql_query(shot(
      "SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = {$this->room->id} AND location LIKE 'heaven'
			ORDER BY time DESC",
      'GamePlayFormat::LoadTalk'
      ));
    return $this->talk_resource !== false;
  }

  function OutputContentHeader(){
    $this->output .= '<body class="heaven"><div id="title"><h1>〜 幽霊の間 〜</h1>';
    $this->OutputWaybackLinks();
    $this->output .= "</div>";
    return 'success';
  }

  function OutputEndTalk($date, $situation){
    $this->output .= '<dt class="bottom"></dt></dl>';
    $this->output .= "</div>\n";
    return 'success';
  }

  function FilterWords($category, &$talk, $date, $situation){
    return true;
  }
}
?>
