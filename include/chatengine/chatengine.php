<?php
define('CHEN_DIR', dirname(__FILE__));

class ChatEngine{
  var $room;
  var $users;
  var $self;

  var $user_cache;
  var $output;

  function ChatEngine(){ $this->__construct(); }

  function __construct(){
    shot('starting ChatEngine...');
    global $ROOM, $USERS, $SELF;
    $this->room = $ROOM;
    $this->users = $USERS;
    $this->self = $SELF;
    $this->ParseUsers();
  }

  function Initialize($filename){
    if (include(JINRO_INC . "/chatengine/$filename")) {
      $segments = explode('_', preg_replace('/\.php/i', '_format', $filename));
      foreach($segments as $segment){
        $class .= ucfirst($segment);
      }
      return new $class();
    }
    return false;
  }

   //Show
  //ページ出力を実施します。
  function Show(){
    shot($this->OutputDocumentHeader(), 'OutputDocumentHeader');
    shot($this->OutputContentHeader(), 'OutputContentHeader');
    shot($this->OutputContent(), 'OutputContent');
    shot($this->OutputContentFooter(), 'OutputContentFooter');
    include(dirname(__FILE__).'/contenttype_set.php');
    shot($this->Flush(), 'Flush');
  }

 // 拡張ポイント
  function ParseUsers(){}
  function LoadTalk(){ return false; }
  function FetchTalk(){ return false; }

  function GetStylePath(){}
  function GetRequiredScripts(){}
  function GenerateStyle(){
    foreach($this->user_cache as $user){
      extract($user);
      if (!empty($class_attr)){
        $style[$class_attr] = "*.{$class_attr}:first-letter { color:{$color} }";
      }
    }
    return implode("\n", $style);
  }
  function GenerateScript(){}

  function OutputDocumentHeader(){
    global $SERVER_CONF;

    $style_path = $this->GetStylePath();
    foreach($this->GetRequiredScripts() as $src){
      $load_scripts .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
    }
    $additional_style = $this->GenerateStyle();
    $additional_script = $this->GenerateScript();
    $this->output .= MakeHTMLHeader($this->room->name . '村', $style_path);
    $this->output .= <<<HEADER
<style>
{$additional_style}
</style>
{$load_scripts}
<script language="JavaScript"><!--
{$additional_script}
//--></script>
</head>
HEADER;
    return 'success';
  }

  function OutputRoomTitle(){
    $room = $this->room;
    $this->output .= '<div id="title"><h1>'.$room->name.'村</h1>'.
      '<p>〜' . $room->comment .  '〜 [' . $room->id . '番地]</p></div>'."\n";
    return 'success';
  }
  function OutputGameInfo(){}
  function OutputPlayerInfo(){
    $this->OutputPlayerTable();
    return 'success';
  }
  function OutputNotice(){
    return 'not implemented.';
  }

  function OutputBeginTalk($date, $situation){
    $this->output .= "<div class='section {$situation}'>\n".'<dl class="talk">';
    return 'success';
  }
  function OutputTalk(&$talk){
    static $fonts = array(
      'weak' => array ('open' => '<span class="weak">', 'close' => '</span>'),
      'strong' => array ('open' => '<span class="strong">', 'close' => '</span>'),
      'common' => array ('open' => '<span class="common">', 'close' => '</span>'),
      'fox' => array ('open' => '<span class="fox">', 'close' => '</span>'),
      'normal' => array()
      );
    $user = $this->user_cache[$talk->uname];
    $sentence = $talk->sentence;
    LineToBR($sentence);

    $font = $fonts[$talk->font_type];
    $this->output .= <<<WORDS
<dt class="{$user['class_attr']}">{$user['display_name']}{$user['additional_info']}</dt>
<dd>{$font['open']}{$sentence}{$font['close']}</dd>

WORDS;
    #return sprintf('success (%s, %s)', $talk->font_type, $volume[$talk->font_type]);
    return 'success';
  }
  function OutputEndTalk($date, $situation){
    shot($this->OutputDeadList(), 'ChatEngine::OutputDeadList');
    $this->output .= '<dt class="bottom"></dt></dl>';
    shot($this->OutputLastWords(), 'ChatEngine::OutputLastWords');
    $this->output .= "</div>\n";
    return 'success';
  }

//前の日の 狼が食べた、狐が占われて死亡、投票結果で死亡のメッセージ
function OutputDeadList(){
  //処刑メッセージ、毒死メッセージ(昼)
  $type_day = "type = 'VOTE_KILLED' OR type = 'POISON_DEAD_day' OR type = 'LOVERS_FOLLOWED_day' " .
    "OR type LIKE 'SUDDEN_DEATH%'";

  //前の日の夜に起こった死亡メッセージ
  $type_night = "type = 'WOLF_KILLED' OR type = 'CURSED' OR type = 'FOX_DEAD' " .
    "OR type = 'HUNTED' OR type = 'REPORTER_DUTY' OR type = 'ASSASSIN_KILLED' " .
    "OR type = 'TRAPPED' OR type = 'POISON_DEAD_night' OR type = 'LOVERS_FOLLOWED_night' " .
    "OR type LIKE 'REVIVE%'";

    if($this->room->IsDay()){
      //昼は前夜の死亡メッセージ
      shot($this->OutputDeadMessage($this->room->date - 1, $type_night), 'ChatEngine::OutputDeadMessage[night]');
      if (!$this->room->log_mode)
        shot($this->OutputDeadMessage($this->room->date - 1, $type_day), 'ChatEngine::OutputDeadMessage[day]');
    }
    else{
      //夜は夕方の死亡メッセージ
      shot($this->OutputDeadMessage($this->room->date, $type_day), 'ChatEngine::OutputDeadMessage[day]');
      if (!$this->room->log_mode)
        shot($this->OutputDeadMessage($this->room->date - 1, $type_night), 'ChatEngine::OutputDeadMessage[night]');
    }
    return 'success';
  }

  function OutputDeadMessage($date, $type){
    global $MESSAGE;
    $subset_query =
       "SELECT message, type FROM system_message
        WHERE room_no = {$this->room->id} AND date = {$date}";
    $array = FetchAssoc(shot("SELECT message, type FROM ({$subset_query}) event WHERE {$type} ORDER BY RAND()", 'OutputDeadMessage'));

    $show_reason = ($this->room->IsFinished() || ($this->self->IsDead() && $this->room->IsOpenCast()) ||
      $this->self->IsDummyBoy() || $this->self->IsRole('yama_necromancer'));
    foreach($array as $this_array){
      $name = $this_array['message'];
      $type = $this_array['type'];
      $visible = true;
      switch($type){
      case 'VOTE_KILLED':
        $message = $MESSAGE->vote_killed;
        break;
      case 'WOLF_KILLED':
      case 'FOX_DEAD':
      case 'CURSED':
      case 'HUNTED':
      case 'REPORTER_DUTY':
      case 'ASSASSIN_KILLED':
      case 'TRAPPED':
        $message = $MESSAGE->deadman;
        $reason_prop = strtolower($type);
        $reason = $MESSAGE->$reason_prop;
        break;
      case 'POISON_DEAD_day':
      case 'POISON_DEAD_night':
        $message = $MESSAGE->deadman;
        $reason = $MESSAGE->poison_dead;
        break;
      case 'LOVERS_FOLLOWED_day':
      case 'LOVERS_FOLLOWED_night':
        $message = $MESSAGE->lovers_followed;
        break;
      case 'REVIVE_SUCCESS':
        $class_attr = 'revive';
        $message = $MESSAGE->revive_success;
        break;
      case 'REVIVE_FAILED':
        if($ROOM->IsFinished() || $SELF->IsDead()){
          $class_attr = 'revive';
          $message = $MESSAGE->revive_failed;
        }
        else {
          $visible = false;
        }
        break;
      case 'SUDDEN_DEATH_CHICKEN':
      case 'SUDDEN_DEATH_RABBIT':
      case 'SUDDEN_DEATH_PERVERSENESS':
      case 'SUDDEN_DEATH_FLATTERY':
      case 'SUDDEN_DEATH_IMPATIENCE':
      case 'SUDDEN_DEATH_PANELIST':
        $message = $MESSAGE->sudden_death;
        $reason_prop = strtolower(substr($type, 13));
        $reason = $MESSAGE->$reason_prop;
        break;
      }
      if ($visible) {
        if ($show_reason){
          $message .= " ($reason)";
        }
        $this->output.= <<<LINE
<dt class="system"></dt><dd class="system">{$name} {$message}</dd>

LINE;
      }
    }
    return 'success';
  }

  //死亡者の遺言を出力
  function OutputLastWords(){
    global $MESSAGE, $ROOM;

    //前日の死亡者遺言を出力
    $set_date = $ROOM->date - 1;
    $sql = mysql_query("SELECT message FROM system_message WHERE room_no = {$ROOM->id}
  			AND date = $set_date AND type = 'LAST_WORDS' ORDER BY RAND()");

    $this->output .= <<<EOF
<div class="lastwords">
<h2>{$MESSAGE->lastwords}</h2>
<dl class="talk">

EOF;
    $num_row = mysql_num_rows($sql);
    $i = 0;
    while ($i < $num_row) {
      $result = mysql_result($sql, $i++, 0);
      LineToBR(&$result);
      list($handle, $str) = ParseStrings($result);

      $this->output .= '<dt>'.$handle.' さんの遺言</dt><dd>'.$str.'</dd>';
    }
    $this->output .= "<dt class='bottom'></dt></dl>\n</div>\n";
  }

  function FilterWords($category, &$talk, $date, $situation) {return false;}

  function OutputObjection($from){
    return 'not implemented.';
  }
  function OutputVote($type, $from, $to){
    return 'not implemented.';
  }
  function OutputVoteResult(){
    return 'not implemented.';
  }
  function OutputSystemTalk($talk){
    return 'not implemented.';
  }
  function OutputSystemMessage($talk){
    return 'not implemented.';
  }
  function OutputTermChanged($date, $situation, $new_date, $new_situation){
    return 'not implemented.';
  }

  function OutputContentHeader(){
    $this->output .= <<<HEADER
<body>
<div id="top" class="{$this->room->day_night}">

HEADER;
    shot($this->OutputRoomTitle(), 'OutputRoomTitle');
    shot($this->OutputGameInfo(), 'OutputGameInfo');
    shot($this->OutputPlayerInfo(), 'OutputPlayerInfo');
    shot($this->OutputNotice(), 'OutputNotice');
    $this->output .= "</div>\n";
    return 'success';
  }

  //OutputPlayerTable
  //プレイヤー一覧を表示します。
  //拡張点
  //OutputUserCell : ユーザー情報を出力キャッシュに出力します。
  //　この機能は投票フォームの出力などで使用されています。
  function OutputPlayerTable(){
    global $ICON_CONF, $USERS;
  
    $width  = $ICON_CONF->width;
    $height = $ICON_CONF->height;
  
    $this->output .= '<table id="players" cellspacing="5">'."\n";
    $count = 0;
    foreach($USERS->rows as $this_user){
      switch(++$count % 5) { //5個ごとに改行
        case 1:
          $this->output .= "<tr>\n";
          $this->OutputPlayerCell($this_user);
          break;
        case 0:
          $this->OutputPlayerCell($this_user);
          $this->output .= "</tr>\n";
          break;
        default:
          $this->OutputPlayerCell($this_user);
          break;
      }
    }
    $this->output .= "</table>\n";
    return 'success';
  }

  //OutputUserCell
  //ユーザー情報を表示します。
  function OutputPlayerCell(&$user){
    return 'not implemented.';
  }

 function OutputContent(){
    if (!$this->LoadTalk()){ 
      return false;
    }

    $date = $this->room->date;
    $situation = $this->room->day_night;
    
    shot($this->OutputBeginTalk($date, $situation), 'OutputBeginTalk');
    while($talk = $this->FetchTalk()) {
      $event = $talk->GetEvent();
      shot(implode(' ', $event));
      switch($event['name']){
      case 'say':
        if ($this->FilterWords('say', $talk, $date, $situation)){
          shot($this->OutputTalk($talk), 'OutputTalk');
        }
        break;
      case 'objection':
        $this->OutputObjection($event['from']);
        break;
      case 'vote':
        if ($this->FilterWords('vote', $talk, $date, $situation)){
          $this->OutputVote($event['type'], $event['from'], $event['to']);
        }
        break;
      case 'hunged':
        $this->OutputVoteResult(); 
        break;
      case 'daybreak':
      case 'sunset':
        $new_date = intval($event['date']);
        $new_situation = $event['situation'];
        if ($this->OutputTermChanged($date, $situation, $new_date, $new_situation)){
          $date = $new_date;
          $situation = $new_situation;
        }
        break;
      case 'system_talk':
        if ($this->FilterWords('system_talk', $talk, $date, $situation)){
          $this->OutputSystemTalk($talk);
        }
        break;
      case 'system_message':
        if ($this->FilterWords('system_message',$talk, $date, $situation)){
          $this->OutputSystemMessage($talk);
        }
        break;
      }
    }
    $this->OutputEndTalk($date, $situation);
    return 'success';
  }

  function OutputContentFooter(){
    $this->output .= collectLog();
    $this->output .= <<<FOOTER
</body>
</html>
FOOTER;
    return 'success';
  }

  function Flush(){
    echo $this->output;
    unset($this->output);
    $this->output = '';
    return 'success';
  }

  function GenerateRoleText(&$user, &$role=null){
    if (empty($role)){
      $role = $user->role;
    }
    $role_str = '';
    if($user->IsRole('human', 'suspect', 'unconscious'))
      $role_str = MakeRoleName($user->main_role, 'human');
    elseif($user->IsRoleGroup('wolf'))
      $role_str = MakeRoleName($user->main_role, 'wolf');
    elseif($user->IsRoleGroup('mage'))
      $role_str = MakeRoleName($user->main_role, 'mage');
    elseif($user->IsRoleGroup('necromancer') || $user->IsRole('medium'))
      $role_str = MakeRoleName($user->main_role, 'necromancer');
    elseif($user->IsRoleGroup('mad'))
      $role_str = MakeRoleName($user->main_role, 'mad');
    elseif($user->IsRoleGroup('guard') || $user->IsRole('reporter'))
      $role_str = MakeRoleName($user->main_role, 'guard');
    elseif($user->IsRoleGroup('common'))
      $role_str = MakeRoleName($user->main_role, 'common');
    elseif($user->IsRoleGroup('fox'))
      $role_str = MakeRoleName($user->main_role, 'fox');
    elseif($user->IsRoleGroup('poison') || $user->IsRole('pharmacist'))
      $role_str = MakeRoleName($user->main_role, 'poison');
    elseif($user->IsRole('assassin', 'mania', 'cupid', 'quiz'))
      $role_str = MakeRoleName($user->main_role);
  
    //ここから兼任役職
    if($user->IsLovers()) $role_str .= MakeRoleName('lovers', '', true);
    if($user->IsRole('copied')) $role_str .= MakeRoleName('copied', 'mania', true);
  
    if(strpos($role, 'authority') !== false)
      $role_str .= MakeRoleName('authority', '', true);
    elseif(strpos($role, 'random_voter') !== false)
      $role_str .= MakeRoleName('random_voter', 'authority', true);
    elseif(strpos($role, 'rebel') !== false)
      $role_str .= MakeRoleName('rebel', 'authority', true);
    elseif(strpos($role, 'watcher') !== false)
      $role_str .= MakeRoleName('watcher', 'authority', true);
    elseif(strpos($role, 'decide') !== false)
      $role_str .= MakeRoleName('decide', '', true);
    elseif(strpos($role, 'plague') !== false)
      $role_str .= MakeRoleName('plague', 'decide', true);
    elseif(strpos($role, 'good_luck') !== false)
      $role_str .= MakeRoleName('good_luck', 'decide', true);
    elseif(strpos($role, 'bad_luck') !== false)
      $role_str .= MakeRoleName('bad_luck', 'decide', true);
    elseif(strpos($role, 'upper_luck') !== false)
      $role_str .= MakeRoleName('upper_luck', 'luck', true);
    elseif(strpos($role, 'downer_luck') !== false)
      $role_str .= MakeRoleName('downer_luck', 'luck', true);
    elseif(strpos($role, 'random_luck') !== false)
      $role_str .= MakeRoleName('random_luck', 'luck', true);
    elseif(strpos($role, 'star') !== false)
      $role_str .= MakeRoleName('star', 'luck', true);
    elseif(strpos($role, 'disfavor') !== false)
      $role_str .= MakeRoleName('disfavor', 'luck', true);
  
    if(strpos($role, 'strong_voice') !== false)
      $role_str .= MakeRoleName('strong_voice', 'voice', true);
    elseif(strpos($role, 'normal_voice') !== false)
      $role_str .= MakeRoleName('normal_voice', 'voice', true);
    elseif(strpos($role, 'weak_voice') !== false)
      $role_str .= MakeRoleName('weak_voice', 'voice', true);
    elseif(strpos($role, 'upper_voice') !== false)
      $role_str .= MakeRoleName('upper_voice', 'voice', true);
    elseif(strpos($role, 'downer_voice') !== false)
      $role_str .= MakeRoleName('downer_voice', 'voice', true);
    elseif(strpos($role, 'random_voice') !== false)
      $role_str .= MakeRoleName('random_voice', 'voice', true);
  
    if(strpos($role, 'no_last_words') !== false)
      $role_str .= MakeRoleName('no_last_words', 'seal', true);
    if(strpos($role, 'blinder') !== false)
      $role_str .= MakeRoleName('blinder', 'seal', true);
    if(strpos($role, 'earplug') !== false)
      $role_str .= MakeRoleName('earplug', 'seal', true);
    if(strpos($role, 'speaker') !== false)
      $role_str .= MakeRoleName('speaker', 'seal', true);
    if(strpos($role, 'silent') !== false)
      $role_str .= MakeRoleName('silent', 'seal', true);
  
    if(strpos($role, 'liar') !== false)
      $role_str .= MakeRoleName('liar', 'convert', true);
    if(strpos($role, 'invisible') !== false)
      $role_str .= MakeRoleName('invisible', 'convert', true);
    if(strpos($role, 'rainbow') !== false)
      $role_str .= MakeRoleName('rainbow', 'convert', true);
    if(strpos($role, 'weekly') !== false)
      $role_str .= MakeRoleName('weekly', 'convert', true);
    if(strpos($role, 'gentleman') !== false)
      $role_str .= MakeRoleName('gentleman', 'convert', true);
    elseif(strpos($role, 'lady') !== false)
      $role_str .= MakeRoleName('lady', 'convert', true);
  
    if(strpos($role, 'chicken') !== false)
      $role_str .= MakeRoleName('chicken', 'sudden-death', true);
    elseif(strpos($role, 'rabbit') !== false)
      $role_str .= MakeRoleName('rabbit', 'sudden-death', true);
    elseif(strpos($role, 'perverseness') !== false)
      $role_str .= MakeRoleName('perverseness', 'sudden-death', true);
    elseif(strpos($role, 'flattery') !== false)
      $role_str .= MakeRoleName('flattery', 'sudden-death', true);
    elseif(strpos($role, 'impatience') !== false)
      $role_str .= MakeRoleName('impatience', 'sudden-death', true);
    elseif(strpos($role, 'panelist') !== false)
      $role_str .= MakeRoleName('panelist', 'sudden-death', true);
    return $role_str;
  }
}
?>
