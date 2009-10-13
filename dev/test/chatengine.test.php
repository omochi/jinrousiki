<?php
$game_root = dirname(dirname(dirname(__FILE__)));
require_once($game_root.'/include/functions.php');
require_once($game_root.'/paparazzi.php');

if (!$DEBUG_MODE){
  die('�ǥХå���ǽ�λ��Ѥϵ��Ĥ���Ƥ��ޤ���');
}

require_once($game_root.'/include/request_class.php');
require_once($game_root.'/include/chatengine.php');

// �ƥ����оݤΥ��ɤȼ¹Ԥ����椷�ޤ���
class ChatEngineTestCore extends RequestBase {
  var $all_mode = array('view_playing');

  function ChatEngineTestCore(){
    global $RQ_ARGS, $game_root;
    AttachTestParameters($this);
    switch ($this->TestItems->test_mode) {
    case 'view_playing':
      $this->RequestBaseGamePlay();
      $this->GetItems(null, 'date', 'day_night', 'uno', 'time');
      include_once($game_root.'/include/gameformat_play.php');
      $this->initiate = 'init_playing';
      $this->run = 'test_view';
      break; //view_playing�ν���� �����ޤ�

    default:
      $this->initiate = 'test_nothing';
      $this->run = 'test_nothing';
      break;
    }
    $RQ_ARGS = $this;
  }

  function TryGetDefault($item, &$value){
    shot($item, 'ChatEngineTestCore::TryGetDefault');
    switch ($item){
    case 'date':
      $value = 2;
      return true;
    case 'day_night':
      $value = 'day';
      return true;
    case 'uno':
      $value = 2;
      return true;
    case 'time':
      $value = 0;
      return true;
    }
    return parent::TryGetDefault($item, &$value);
  }

  function generateModeOptions(){
    foreach ($this->all_mode as $mode){
      if($mode == $this->test_mode) {
        $selected = true;
        $options .= '<option selected="selected">'.$mode."</option>\n";
      }
      else {
        $options .= "<option>$mode</option>\n";
      }
    }
    return ($selected ? '<option selected="selected">(none)</option>' : '') . $options;
  }

  function init_playing(){
    shot("init_playing\r\n");
    shot(serialize($this), 'TestCore');
    global $ROOM, $USERS, $SELF, $ROLE_IMG, $game_root;

    require_once($game_root . '/include/user_class.php');

    $this->connection = ConnectDatabase(true, true); //DB ��³

    $ROOM = new RoomDataSet($this); //¼��������
    $ROOM->status = 'playing';
    $ROOM->date = $this->date;
    $ROOM->day_night = $this->day_night;
    $ROOM->dead_mode    = $this->dead_mode; //��˴�ԥ⡼��
    $ROOM->heaven_mode  = $this->heaven_mode; //���å⡼��
    $ROOM->system_time  = TZTime() - $this->time; //���߻�������
    $ROOM->sudden_death = 0; //������¹ԤޤǤλĤ����

    $USERS = new UserDataSet($this); //�桼����������
    $SELF = $USERS->rows[$this->uno]; //��ʬ�ξ�������
    $ROLE_IMG = new RoleImage();
    $uname = $SELF->uname;
  }

  function terminate(){
    shot("terminate\r\n");
    if( isset($this->connection)){
      DisconnectDatabase($this->connection);
    }
  }

  function test_nothing(){ shot('pass'); }

  function test_view() {
    $options = $this->generateModeOptions();
    shot("test_view\r\n");
    $target = new GamePlayFormat();
    $target->OutputDocumentHeader();
    $target->Flush();
    echo <<<FORM
<form name="request" method="GET">
<table>
<th><label for="test_mode">test_mode</label></th>
<td>
<select name="test_mode">
$options
</select>
</td>
<tr>
<th><label for="room_no">room_no</label></th>
<td><input name="room_no" type="text" value="{$this->room_no}"></td>
</tr>
<tr>
<th><label for="date">date</label></th>
<td><input name="date" type="text" value="{$this->date}"></td>
</tr>
<tr>
<th><label for="day_night">day_night</label></th>
<td><input name="day_night" type="text" value="{$this->day_night}"></td>
</tr>
<tr>
<th><label for="uno">user_no</label></th>
<td><input name="uno" type="text" value="{$this->uno}"></td>
</tr>
<tr>
<th><label for="dead_mode">dead_mode</label></th>
<td><input name="dead_mode" type="text" value="{$this->dead_mode}"></td>
</tr>
<tr>
<th><label for="heaven_mode">heaven_mode</label></th>
<td><input name="heaven_mode" type="text" value="{$this->heaven_mode}"></td>
</tr>
<tr>
<th><label for="time">time</label></th>
<td><input name="time" type="text" value="{$this->time}"></td>
</tr>
</table>
<button type="submit">�����</button>
</form>

FORM;
    $target->OutputContentHeader();
    $target->OutputContent();
    $target->OutputContentFooter();
    $target->Flush();
  }
}

register_shutdown_function('insertLog');

$test = & new ChatEngineTestCore();
echo <<<FORM
FORM;
call_user_func(array($test, $test->initiate));
call_user_func(array($test, $test->run));
call_user_func(array($test, 'terminate'));
?>