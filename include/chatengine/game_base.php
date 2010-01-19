<?php
class GameBaseFormat extends ChatEngine {
  function ParseUsers(){
    $user_cache = array();
    foreach ($this->users->rows as $user){
      $user_cache[$user->uname] = array (
        'class_attr' => 'u'.$user->user_no,
        'color' => $user->color,
        'display_name' => '��'.$user->handle_name
      );
    }
    $this->user_cache = $user_cache;
  }

  function GetStylePath(){
    return 'css/game_layout.css';
  }

  function OutputGameStartAnnounce(){
    $option_role = FetchResult("SELECT option_role FROM room WHERE room_no = {$this->room->id}");
    $option_image = MakeGameOptionImage($this->room->game_option, $option_role);
    $this->output .= <<<NOTICE
<div class="caution">
������򳫻Ϥ���ˤ������������೫�Ϥ���ɼ����ɬ�פ�����ޤ�
<span>(��ɼ�����ͤ�¼�ͥꥹ�Ȥ��طʤ��֤��ʤ�ޤ�)</span>
</div>
<table class="time-table">
<tr><td>�����४�ץ����{$option_image} </td></tr>
</table>

NOTICE;
  }

  function OutputTimelag() {
    global $SERVER_CONF;
    //���֤����ɽ��
    $date_str = $SERVER_CONF->adjust_time_difference ?
                gmdate('Y, m, j, G, i, s', $this->room->system_time) : date('Y, m, j, G, i, s', $this->room->system_time);
    $this->output .= <<<NOTICE
<div>
�����Фȥ�����PC�λ��֥���(�饰��)�� <span>
<script type="text/javascript"><!--
output_diff_time('$date_str');
//--></script>��</span>
</div>

NOTICE;
  }

  function OutputGameStatus() {
    //��¬�η�̡����Υ�����ϥѥե����ޥ󥹤��礭�ʱƶ���Ϳ���ʤ����Ȥ���ǧ����ޤ�����
    $living_users = FetchResult(
      "SELECT COUNT(uname) FROM user_entry
      WHERE room_no = {$this->room->id}
        AND live = 'live' AND user_no > 0"
      );
    if($this->room->IsRealTime()){ //�ꥢ�륿������
      GetRealPassTime($left_time);
      $time_text =
        '<form name="realtime_form"><input type="text" name="output_realtime" size="50" readonly></form>';
    }
    else{ //ȯ���ˤ�벾�ۻ���
      $time_text = $time_message . GetTalkPassTime($left_time);
    }
    $this->output .= <<<LIST
<ul id='game_info'>
<li id='date'>{$this->room->date} ����</li>
<li id='alive'>(��¸��{$living_users}��)</li>
<li id='time'>{$time_text}</li>
</ul>

LIST;
  }

  function OutputPlayerCell($user){
    global $DEBUG_MODE;
    $this_uname   = $user->uname;
    $this_info = $this->user_cache[$this_uname];
    $this_handle  = $this_info['display_name'];

    if($DEBUG_MODE) $this_handle .= ' (' . $user->user_no . ')';

    //��������
    $icon = $this->GenerateUserIcon($user);

    if($this->room->IsBeforeGame()){ //�����ॹ�����Ȥ���ɼ���Ƥ���п����Ѥ���
      $query_game_start = "SELECT COUNT(uname) FROM vote WHERE room_no = {$this->room->id} " .
        "AND situation = 'GAMESTART' AND uname = '$this_uname'";
      if((! $this->room->IsQuiz() && $user->IsDummyBoy()) || FetchResult($query_game_start) > 0){
  	    $this_classes[] = 'already-vote';
      }
    }
    $class_attr = count($this_classes) ? ' class="'.implode(' ', $this_classes).'"' : '';
    $this->output .= <<<CELL
<td{$class_attr}>
{$icon}
<ul>
<li class="{$this_info['class_attr']}">$this_handle</li>
<li>$display_live</li>
</ul>
</td>

CELL;
    return 'success';
  }

  function OutputWaybackLinks() {
    //�������Υ��ؤΥ������
    $link_format ='<li><a href="game_log.php?room_no=' . $this->room->id .
      '&date=%d&day_night=%s#game_top" target="_blank">%s</a></li>';

    $list = '<div id="wayback_links"><h2>��</h2><ul>';
    $list .= sprintf($link_format, 0, 'beforegame', "0(������)");
    $list .= sprintf($link_format, 1, 'night', "1(��)");
    for($day = 2; $day < $this->room->date; $day++){
      $list .= sprintf($link_format, 1, 'day', "{$day}(��)");
      $list .= sprintf($link_format, 1, 'night', "{$day}(��)");
    }
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$this->room->id} " .
      "AND date = {$this->room->date} AND location = 'day'";
    if(FetchResult($query) > 0){
      $list .= sprintf($link_format, $this->room->date, 'day', "{$this->room->date}(��)");
    }
    $this->output .= $list . "</ul></div>\n";
  }

  //�桼��������ꤷ�ƥ�������ɽ���Ѥ�img���Ǥ��������ޤ���
  function GenerateUserIcon(&$user) {
    global $ICON_CONF;
    //�֥饦��������å� (MSIE @ Windows ���� ������ Alt, Title °���ǲ��ԤǤ���)
    //IE �ξ����Ԥ� \r\n �����졢����¾�Υ֥饦���ϥ��ڡ����ˤ���(������Alt°��)
    if($user->IsLive()){
      $icon_src = $ICON_CONF->path . '/' . $user->icon_filename;
      $display_live = '(��¸��)';
    }
    else{
      $icon_src = $ICON_CONF->dead;
      $rollover_path = $ICON_CONF->path . '/' . $user->icon_filename;
      $display_live  = '(��˴)';
      $rollover_handlers = " onMouseover=\"this.src='{$rollover_path}'\" onMouseout=\"this.src='{$icon_src}'\"";
    }
    $replace = (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? "\r\n" : ' ');
    $display_profile  = str_replace("\n", $replace, $user->profile);
    return <<<ELEMENT
<img src="{$icon_src}" class="icon" title="{$display_profile}" alt="{$display_profile}"
  width="{$ICON_CONF->width}" height="{$ICON_CONF->height}" style="border-color:{$this_info['color']};"{$rollover_handlers}>

ELEMENT;
  }

  function LoadTalk(){
    $this->talk_resource = mysql_query(shot(
      "SELECT uname, sentence, font_type, location FROM talk
			WHERE room_no = {$this->room->id} AND location LIKE '{$this->room->day_night}%'
			AND date = {$this->room->date} ORDER BY time DESC",
      'GamePlayFormat::LoadTalk'
      ));
    return $this->talk_resource !== false;
  }

  function FetchTalk(){
    $row = mysql_fetch_object($this->talk_resource, 'Talk');
    if(empty($row)){
      return false;
    }
    else {
      $row->ParseCompoundParameters();
      return $row;
    }
  }
}
?>