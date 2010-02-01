<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

$INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'TIME_CONF', 'ROOM_IMG');
$INIT_CONF->LoadFile('chatengine');
if(! $DEBUG_MODE){
  OutputActionResult('ChatEngine [テスト]', 'デバッグ機能の使用は許可されていません。');
}

// テスト対象のロードと実行を制御します。
class ChatEngineTestCore extends RequestBaseGamePlay{
  var $all_mode = array('game_play', 'game_after', 'game_heaven');

  function ChatEngineTestCore(){ $this->__construct(); }

  function __construct(){
    global $RQ_ARGS;
    $this->AttachTestParameters($this);
    switch ($this->TestItems->test_mode) {
    case 'game_play':
    case 'game_after':
    case 'game_heaven':
      parent::__construct();
      $this->GetItems(null, 'date', 'day_night', 'uno', 'time');
      $this->initiate = 'init_playing';
      $this->run = 'test_view';
      break; //view_playingの初期化 ここまで

    default:
      $this->initiate = 'test_nothing';
      $this->run = 'outputTestPanel';
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
      if($mode == $this->TestItems->test_mode) {
        $selected = true;
        $options .= '<option selected="selected">'.$mode."</option>\n";
      }
      else {
        $options .= "<option>$mode</option>\n";
      }
    }
    return ($selected ? '<option selected="selected">(none)</option>' : '') . $options;
  }

  function outputTestPanel(){
    echo $this->generateTestPanel();
  }
  function generateTestPanel(){
    $options = $this->generateModeOptions();
    return <<<FORM
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
<button type="submit">リロード</button>
</form>

FORM;
  }

  function init_playing(){
    global $DB_CONF, $ROOM, $USERS, $SELF, $ROLE_IMG, $room_no;
    shot("init_playing\r\n");
    shot(serialize($this), 'TestCore');

    $DB_CONF->Connect(true, true); //DB 接続

    $room_no = $this->room_no;

    $ROOM = new RoomDataSet($this); //村情報をロード
    $ROOM->status = 'playing';
    $ROOM->date = $this->date;
    $ROOM->day_night = $this->day_night;
    $ROOM->dead_mode    = $this->dead_mode; //死亡者モード
    $ROOM->heaven_mode  = $this->heaven_mode; //霊話モード
    $ROOM->system_time  = TZTime() - $this->time; //現在時刻を取得
    $ROOM->sudden_death = 0; //突然死実行までの残り時間

    $USERS = new UserDataSet($this); //ユーザ情報をロード
    //自分の情報をロード
    if (array_key_exists($this->uno, $USERS->rows))
      $SELF = $USERS->rows[$this->uno];
    else
      $SELF = $USERS->rows[2];
    $uname = $SELF->uname;
  }

  function terminate(){
    global $DB_CONF;
    shot("terminate\r\n");
    $DB_CONF->Disconnect();
  }

  function test_nothing(){ shot('pass'); }

  function test_view() {
    shot("test_view\r\n");
    $target = ChatEngine::Initialize($this->TestItems->test_mode . '.php');
    shot($target->OutputDocumentHeader(), 'ChatEngine::OutputDocumentHeader');
    $target->output .= $this->generateTestPanel();
    $target->Flush(); //ここでフラッシュしないとエラーで落ちた際にフォームがでない。
    $target->OutputContentHeader();
    $target->OutputContent();
    $target->OutputContentFooter();
    $target->Flush();
  }
}

register_shutdown_function('insertLog');

$test = & new ChatEngineTestCore();
PrintData($test->TestItems);
call_user_func(array($test, $test->initiate));
call_user_func(array($test, $test->run));
call_user_func(array($test, 'terminate'));
?>