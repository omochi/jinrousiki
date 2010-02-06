<?php
class GamePlayFormat extends ChatEngine{
  function GamePlayFormat(){ $this->__construct(); }

  function __construct(){
    parent::__construct();
  }

  function ParseUsers(){
    $user_cache = array();
    if ($this->room->IsNight()){
      $user_cache['wolf'] = array( 'display_name' => 'ϵ�α��ʤ�' );
      $user_cache['common'] = array( 'display_name' => '<span class="weak">��ͭ�Ԥξ���</span>' );
      $user_cache['self'] = array(
        'class_attr' => 'u'.$this->self->user_no,
        'color' => $this->self->color,
        'display_name' => '��'.$this->self->handle_name.'���Ȥ��'
        );
    }
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
    return 'game_layout';
  }

  function GetRequiredScripts(){
    return array('javascript/output_realtime.js', 'javascript/output_diff_time.js');
  }

  function GenerateScript(){
    $realtime_message = $this->room->IsDay() ? '���פޤ�' : '�������ޤ�';
    list($start_time, $end_time) = GetRealPassTime($left_time, true);
    return <<<SCRIPT
var realtime_message = "��{$realtime_message}";
var start_time = "{$start_time}";
var end_time = "{$end_time}";

window.onload = function(){
  output_realtime();
}

SCRIPT;
  }

  function OutputGameInfo(){
    global $SERVER_CONF;
    if ($this->room->IsBeforeGame()){
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
    return 'success';
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

  function OutputNotice(){
    $this->output .= '<div id="notice">'."\n";
    shot($this->OutputRoleNotice(), 'GamePlayFormat::OutputRoleNotice');
    shot($this->OutputVoteNotice(), 'GamePlayFormat::OutputVoteNotice');
    $this->output .= '</div>'."\n";
    return 'success';
  }

  function OutputVoteNotice(){
    //��ɼ��������
    $vote_times = GetVoteTimes();
    $sentence = '<div class="self-vote">��ɼ ' . $vote_times . ' ���ܡ�';
  
    //��ɼ�оݼԤ����
    $query = "SELECT target_uname FROM vote WHERE room_no = {$this->room->id} AND date = {$this->room->date} " .
      "AND situation = 'VOTE_KILL' AND vote_times = $vote_times AND uname = '{$this->self->uname}'";
    $target_uname = FetchResult($query);
    $sentence .= ($target_uname === false ? '<font color="red">�ޤ���ɼ���Ƥ��ޤ���</font>' :
  		$this->users->GetHandleName($target_uname) . '����ɼ�Ѥ�');
    $this->output .= $sentence . '</div>'."\n";

    //����ɼ
    if($this->room->IsDay() && 0 < ($revote_times = GetVoteTimes(true))) {
      global $GAME_CONF, $MESSAGE, $RQ_ARGS, $COOKIE, $SOUND;
      if($RQ_ARGS->play_sound && ! $this->room->view_mode && $revote_times > $COOKIE->vote_times){
        $SOUND->Output('revote'); //�����Ĥ餹
      }
    
      //��ɼ�Ѥߥ����å�
      $vote_times = $revote_times + 1;
      $query = "SELECT COUNT(uname) FROM vote WHERE room_no = {$this->room->id} AND date = {$this->room->date} " .
        "AND vote_times = $vote_times AND uname = '{$this->self->uname}'";
      if(FetchResult($query) == 0){
        $this->output .= '<div class="revote">' . $MESSAGE->revote . ' (' . $GAME_CONF->draw . '��' .
          $MESSAGE->draw_announce . ')</div>';
      }
    
      $this->OutputVoteList($this->room->date); //��ɼ��̤����
    }
  }

  //���ꤷ�����դ���ɼ��̤���Ϥ���
  function OutputVoteList($set_date){
    global $RQ_ARGS;

    //���ꤵ�줿���դ���ɼ��̤����
    $query = "SELECT message FROM system_message WHERE room_no = {$this->room->id} " .
      "AND date = {$set_date} and type = 'VOTE_KILL'";
    $vote_message_list = FetchArray($query);
    if(count($vote_message_list) == 0) return false; //��ɼ���

    $result_array = array(); //��ɼ��̤��Ǽ����
    $this_vote_times = -1; //���Ϥ�����ɼ�����Ͽ
    $is_open_vote = $this->room->IsOption('open_vote');
    foreach($vote_message_list as $vote_message){ //���ä�������˳�Ǽ����
      //���ֶ��ڤ�Υǡ�����ʬ�䤹��
      list($handle_name, $target_name, $voted_number, $vote_number, $vote_times)
        = explode("\t",$vote_message);
      $vote_number_str = ($is_open_vote ? '��ɼ�� ' . $vote_number . ' ɼ ��' : '��ɼ�袪');
      //ɽ��������å�����
      $result_array[$vote_times][]
        = '<tr><td class="name">' . $handle_name . '</td><td>' . $voted_number . ' ɼ</td><td>'
        . $vote_number_str . '</td><td class="name">' . $target_name . '</td></tr>';
    }

    //����˳�Ǽ���줿�ǡ��������
    if($RQ_ARGS->reverse_log){ //�ս�ɽ��
      $start = 1;
      $end = count($result_array);
    }
    else{
      $start = count($result_array);
      $end = 1;
    }
    for($vote_times = $start; $vote_times <= $end; $vote_times++){
      if(is_array($result_array[$vote_times])){
        $this->output .= <<<HEADER
<table class="vote-list">
<caption>{$set_date} ���� ( {$vote_times} ����)</caption>

HEADER;
        $this->output .= implode("\n", $result_array[$vote_times]) . "\n</table>\n";
      }
    }
  }


  function OutputRoleNotice() {
    global $ROLE_IMG, $GAME_CONF;
    $self = & $this->self;

    //��������Τ�ɽ������
    if(!$this->room->IsPlaying()) return 'not suppored';

    if($self->IsRole('human', 'suspect', 'unconscious')){ //¼�͡��Կ��ԡ�̵�ռ�
      $this->OutputRole_Human();
    }
    elseif($self->IsWolf()){ //��ϵ��
      $this->OutputRole_Wolf();
    }
    elseif($self->IsRoleGroup('mage')){ //�ꤤ��
      shot($this->OutputRole_Mage(), 'GamePlayFormat::OutputRole_Mage');
    }
    elseif($self->IsRole('voodoo_killer')){ //���ۻ�
      $this->OutputRole_VoodooKiller();
    }
    elseif($self->IsRole('yama_necromancer')) $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role); //����
    elseif($self->IsRole( 'necromancer') || $self->IsRole( 'medium') !== false){
      $this->OutputRole_Necromancer();
    }
    elseif($self->IsRoleGroup('mad')){ //���ͷ�
      $this->OutputRole_Mad();
    }
    elseif($self->IsRoleGroup('guard')){ //��ͷ�
      $this->OutputRole_Guard();
    }
    elseif($self->IsRole('anti_voodoo')){ //���
      $this->OutputRole_AntiVoodoo();
    }
    elseif($self->IsRole( 'reporter')){
      $this->OutputRole_Reporter();
    }
    elseif($self->IsRoleGroup('common')){
      $this->OutputRole_Common();
    }
    elseif($self->IsFox()){
      $this->OutputRole_Fox();
    }
    elseif($self->IsRole('incubate_poison')){
      $this->OutputRole_IncubatePoison();
    }
    elseif($self->IsRole( 'poison_cat')){
      $this->OutputRole_PoisonCat();
    }
    elseif($self->IsRoleGroup('poison')) $this->output .= $ROLE_IMG->GenerateTag('poison');
    elseif($self->IsRole( 'pharmacist')){
      $this->OutputRole_Pharmacist();
    }
    elseif($self->IsRole('assassin')){
      $this->OutputRole_Assasin();
    }
    elseif($self->IsRole( 'cupid')){
      $this->OutputRole_Cupid();
    }
    elseif($self->IsRole( 'mania')){
      $this->OutputRole_Mania();
    }
    elseif($self->IsRole( 'quiz')){
      $this->OutputRole_Quiz();
    }

    //���������Ǥ��
    if($this->self->IsRole('lost_ability')) $ROLE_IMG->DisplayImage('lost_ability'); //ǽ�ϼ���
    if($this->self->IsLovers()){ //���ͤ�ɽ������
      $this->OutputRole_Lovers();
    }

    //����ʹߤϥ�������������ץ����αƶ��������
    if($this->room->IsOption('secret_sub_role')) return;

    $role_keys_list   = array_keys($GAME_CONF->sub_role_list);
    $not_display_list = array('decide', 'plague', 'good_luck', 'bad_luck', 'lovers', 'copied');
    $display_list     = array_diff($role_keys_list, $not_display_list);
    $target_list      = array_intersect($display_list, array_slice($self->role_list, 1));

    foreach($target_list as $this_role){
      $this->output .= $ROLE_IMG->GenerateTag($this_role);
    }
  }

  function OutputRole_Human(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag('human');
  }

  function OutputRole_Wolf(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    foreach($this->users->rows as $user){ //��־�������
      if($user->IsSelf()) continue;
      if($user->IsWolf()){
	$wolf_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('whisper_mad')){
	$mad_partner[] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious')){
	$unconscious_list[] = $user->handle_name;
      }
    }
    $this->OutputPartner($wolf_partner, 'wolf_partner'); //��֤�ɽ��
    $this->OutputPartner($mad_partner, 'mad_partner'); //�񤭶��ͤ�ɽ��
    if($this->room->IsNight()) $this->OutputPartner($unconscious_list, 'unconscious_list'); //�����̵�ռ���ɽ��

    if($this->self->IsRole('tongue_wolf')){ //���ϵ�γ��߷�̤�ɽ��
      $action = 'TONGUE_WOLF_RESULT';
      $sql    = $this->GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
      	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      	if($this->self->handle_name == $actor){
      	  $this->OutputAbilityResult('wolf_result', $target, 'result_' . $target_role);
      	  break;
      	}
      }
    }

    if($this->room->IsNight()) $this->OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //�����ɼ
    return 'success';
  }

  function OutputRole_Mage(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->IsRole('dummy_mage') ? 'mage' : $this->self->main_role);

    //�ꤤ��̤�ɽ��
    $action = 'MAGE_RESULT';
    $sql    = $this->GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($this->self->handle_name == $actor){
        $this->OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
        break;
      }
    }
    
    if($this->room->IsNight()) $this->OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO'); //�����ɼ
    return 'success';
  }

  function OutputRole_VoodooKiller(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    //��ҷ�̤�ɽ��
    $sql = $this->GetAbilityActionResult('VOODOO_KILLER_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($this->self->handle_name == $actor){
        $this->OutputAbilityResult(NULL, $target, 'voodoo_killer_success');
      	break;
      }
    }

    //�����ɼ
    if($this->room->IsNight()) $this->OutputVoteMessage('mage-do', 'voodoo_killer_do', 'VOODOO_KILLER_DO');
  }

  function OutputRole_Necromancer(){
    global $ROLE_IMG;
    if($this->self->IsRoleGroup('necromancer')){
      $role_name = 'necromancer';
      $result    = 'necromancer_result';
      $action    = 'NECROMANCER_RESULT';
      switch($this->self->main_role){
      case 'soul_necromancer':
   $role_name = $this->self->main_role;
	$action    = 'SOUL_' . $action;
	break;

      case 'dummy_necromancer':
	$action = 'DUMMY_' . $action;
	break;
      }
    }
    else{
      $role_name = 'medium';
      $result    = 'medium_result';
      $action    = 'MEDIUM_RESULT';
    }
    $this->output .= $ROLE_IMG->GenerateTag($role_name);

    //Ƚ���̤�ɽ��
    $sql = $this->GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      $this->OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }

  function OutputRole_Mad() {
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);
    if($this->self->IsRole('fanatic_mad', 'whisper_mad')){
      foreach($this->users->rows as $user){
	if($user->IsSelf()) continue;
	if($user->IsWolf()){
	  $wolf_partner[] = $user->handle_name;
	}
	elseif($user->IsRole('whisper_mad')){
	  $mad_partner[] = $user->handle_name;
	}
      }
      $this->OutputPartner($wolf_partner, 'wolf_partner'); //ϵ��ɽ��
      if($this->self->IsRole('whisper_mad')) $this->OutputPartner($mad_partner, 'mad_partner'); //�񤭶��ͤ�ɽ��
    }
    elseif($this->self->IsRole('jammer_mad') && $this->room->IsNight()){ //���ⶸ��
      $this->OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
    }
    elseif($this->self->IsActiveRole('trap_mad') && $is_after_first_night){ //櫻�
      $this->OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
    elseif($this->self->IsRole('voodoo_mad') && $this->room->IsNight()){ //���ѻ�
      $this->OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
    }
  }

  function OutputRole_Guard(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->IsRole('dummy_guard') ? 'guard' : $this->self->main_role);

    //��ҷ�̤�ɽ��
    $sql = $this->GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($this->self->handle_name == $actor){
	$this->OutputAbilityResult(NULL, $target, 'guard_success');
	break;
      }
    }

    if(! $this->self->IsRole('dummy_guard')){ //����̤�ɽ��
      $sql = $this->GetAbilityActionResult('GUARD_HUNTED');
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	if($this->self->handle_name == $actor){
	  $this->OutputAbilityResult(NULL, $target, 'guard_hunted');
	  break;
	}
      }
    }

    if($is_after_first_night) $this->OutputVoteMessage('guard-do', 'guard_do', 'GUARD_DO'); //�����ɼ
  }

  function OutputRole_AntiVoodoo() {
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    //��ҷ�̤�ɽ��
    $sql = $this->GetAbilityActionResult('ANTI_VOODOO_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($this->self->handle_name == $actor){
	$this->OutputAbilityResult(NULL, $target, 'anti_voodoo_success');
	break;
      }
    }

    if($is_after_first_night){
      $this->OutputVoteMessage('guard-do', 'anti_voodoo_do', 'ANTI_VOODOO_DO'); //�����ɼ
    }
  }

  function OutputRole_Reporter(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    //���Է�̤�ɽ��
    $action = 'REPORTER_SUCCESS';
    $sql    = $this->GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($this->self->handle_name == $actor){
	$target .= ' ����� ' . $wolf_handle;
	$this->OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
	break;
      }
    }

    if($is_after_first_night) $this->OutputVoteMessage('guard-do', 'reporter_do', 'REPORTER_DO'); //�����ɼ
  }

  function OutputRole_Common(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag('common');

    //��֤�ɽ��
    foreach($this->users->rows as $user){
      if($user->IsSelf()) continue;
      if(($this->self->IsRole('common') && $user->IsRole('common')) ||
	 ($this->self->IsRole('dummy_common') && $user->IsDummyBoy())){
	$common_partner[] = $user->handle_name;
      }
    }
    $this->OutputPartner($common_partner, 'common_partner');
  }

  function OutputRole_Fox(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    foreach($this->users->rows as $user){
      if($user->IsSelf() || $user->IsRole('silver_fox')) continue;
      if($user->IsRole('child_fox')){
	$child_fox_partner[] = $user->handle_name;
      }
      elseif($user->IsFox()){
	$fox_partner[] = $user->handle_name;
      }
    }
    if(! $this->self->IsRole('silver_fox')){
      $this->OutputPartner($fox_partner, 'fox_partner'); //�ŸѤ���֤�ɽ��
      $this->OutputPartner($child_fox_partner, 'child_fox_partner'); //�ҸѤ���֤�ɽ��
    }

    if($this->self->IsRole('child_fox')){
      //�ꤤ��̤�ɽ��
      $action = 'CHILD_FOX_RESULT';
      $sql    = $this->GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($this->self->handle_name == $actor){
	  $this->OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
	  break;
	}
      }

      if($this->room->IsNight()) $this->OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //�����ɼ
    }
    elseif($this->self->IsRole('voodoo_fox') && $this->room->IsNight()){ //����
      $this->OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
    }

    if($this->self->IsRole('fox', 'cursed_fox', 'voodoo_fox')){
      //�Ѥ�����줿��å�������ɽ��
      $sql = $this->GetAbilityActionResult('FOX_EAT');
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	if($this->self->handle_name == mysql_result($sql, $i, 0)){
	  $this->OutputAbilityResult('fox_targeted', NULL);
	  break;
	}
      }
    }
  }

  function OutputRole_IncubatePoison(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);
    if($this->room->date > 4) $this->OutputAbilityResult('ability_poison', NULL);
  }

  function OutputRole_PoisonCat(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    if(! $this->room->IsOpenCast()){
      //������̤�ɽ��
      $action = 'POISON_CAT_RESULT';
      $sql    = $this->GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
	if($this->self->handle_name == $actor){
	  $this->OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
	  break;
	}
      }

      if($is_after_first_night){ //�����ɼ
	$this->OutputVoteMessage('poison-cat-do', 'revive_do', 'POISON_CAT_DO', 'POISON_CAT_NOT_DO');
      }
    }
  }

  function OutputRole_Pharmacist(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    //���Ƿ�̤�ɽ��
    $sql = $this->GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($this->self->handle_name == $actor){
	$this->OutputAbilityResult(NULL, $target, 'pharmacist_success');
	break;
      }
    }
  }

  function OutputRole_Assasin(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);
    if($is_after_first_night){ //�����ɼ
      $this->OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }    
  }

  function OutputRole_Cupid() {
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ������
    foreach($this->users->rows as $user){
      if($user->IsLovers() && in_array($this->self->user_no, $user->partner_list['lovers'])){
	$cupid_pair[] = $user->handle_name;
      }
    }
    $this->OutputPartner($cupid_pair, 'cupid_pair');

    if($is_first_night) $this->OutputVoteMessage('cupid-do', 'cupid_do', 'CUPID_DO'); //���������ɼ
  }

  function OutputRole_Mania() {
    global $ROLE_IMG;
    if($self->IsRole( 'mania')){
      $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);
      if($is_first_night) $this->OutputVoteMessage('mania-do', 'mania_do', 'MANIA_DO'); //���������ɼ
    }
    if ($this->self->IsRole('copied')) {
      $action = 'MANIA_RESULT';
      $sql    = $this->GetAbilityActionResult($action);
      $count  = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
        list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
        if($this->self->handle_name == $actor){
  	$this->OutputAbilityResult(NULL, $target, 'result_' . $target_role);
  	break;
        }
      }
    }
  }

  function OutputRole_Quiz(){
    global $ROLE_IMG;
    $this->output .= $ROLE_IMG->GenerateTag($this->self->main_role);
    if($this->room->IsOptionGroup('chaos')) $this->output .= $ROLE_IMG->GenerateTag('quiz_chaos');
  }

  function OutputRole_Lovers(){
    global $ROLE_IMG;
    foreach($this->users->rows as $user){
      if($user->IsLovers() && ! $user->IsSelf() &&
	 (count(array_intersect($this->self->partner_list['lovers'], $user->partner_list['lovers'])) > 0)){
	$lovers_partner[] = $user->handle_name;
      }
    }
    $this->OutputPartner($lovers_partner, 'lovers_header', 'lovers_footer');
  }

  //��������ɽ������
  function OutputRoleComment($role){
    global $ROLE_IMG;
    $this->output .=  '<img src="' . $ROLE_IMG->$role . '"><br>'."\n";
  }

  //��֤�ɽ������
  function OutputPartner($partner_list, $header, $footer = NULL){
    global $ROLE_IMG;

    if(count($partner_list) < 1) return false; //��֤����ʤ����ɽ�����ʤ�

    $message = $ROLE_IMG->GenerateTag($header);
    $partners = implode('���� ', $partner_list) . '����';  //implode�λ��;������ˤϷɾΤ��Ĥ��ʤ���
    if($footer) $message_end .= $ROLE_IMG->GenerateTag($footer);
    $this->output .= <<<LINE
<div id="partners">{$message}{$partners}{$message_end}</div>

LINE;
  }

  //ǽ��ȯư��̤�ǡ����١������䤤��碌��
  function GetAbilityActionResult($action){
    $yesterday = $this->room->date - 1;
    return mysql_query("SELECT message FROM system_message WHERE room_no = {$this->room->id}
  			AND date = $yesterday AND type = '$action'");
  }

  //ǽ��ȯư��̤�ɽ������
  function OutputAbilityResult($header, $target, $footer = NULL){
    global $ROLE_IMG;

    $this->output .= '<div id="ability-results">';
    if($header) $this->output .= $ROLE_IMG->GenerateTag($header);
    if($target) $this->output .= $target;
    if($footer) $this->output .= $ROLE_IMG->GenerateTag($footer);
    $this->output .= '</div>'."\n";
  }

  //��ʬ��̤��ɼ�����å�
  function CheckSelfVote(){
    $room_no = $this->room->id;
    $date = $this->room->date;
    $uname = $this->self->uname;

    //��ɼ��������(����ɼ�ʤ� $vote_times ��������)
    $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
  			AND type = 'VOTE_TIMES' AND date = $date");
    $vote_times = (int)mysql_result($sql, 0, 0);
    $this->output .=  '<div class="self-vote">��ɼ ' . $vote_times . ' ���ܡ�';

    //��ɼ�Ѥߤ��ɤ���
    $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
  			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
  			AND situation = 'VOTE_KILL'");
    $this->output .=  (mysql_result($sql, 0, 0) ? '��ɼ�Ѥ�' : '�ޤ���ɼ���Ƥ��ޤ���') . '</div>'."\n";
  }

  //���̤��ɼ�����å�
  function CheckNightVote($action, $class){
    global $MESSAGE;

    $query = "SELECT uname FROM vote WHERE room_no = {$this->room->id} "; //��ͭ������
    if($action != 'WOLF_EAT') $query .= "AND uname = '{$this->self->uname}' "; //��ϵ��ï�Ǥ� OK
    $sql = mysql_query($query . "AND situation = '$action'");

    if(mysql_num_rows($sql) != 0) return false; //��ɼ�Ѥߤʤ��å�������ɽ�����ʤ�
    $class_str   = 'ability-' . $class; //���饹̾�ϥ��������������Ȥ�ʤ��Ǥ���
    $message_str = 'ability_' . strtolower($action);
    $this->output .=  '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
  }

  //���̤��ɼ��å���������
  function OutputVoteMessage($class, $sentence, $situation, $not_situation = ''){
    global $MESSAGE, $ROOM;

    if(! $ROOM->test_mode){
      //��ɼ�Ѥߤʤ��å�������ɽ�����ʤ�
      if(CheckSelfVoteNight($situation, $not_situation)) return false;
    }

    $class_str   = 'ability-' . $class; //���饹̾�ϥ��������������Ȥ�ʤ��Ǥ���
    $message_str = 'ability_' . $sentence;
    $this->output .= '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
  }

  function OutputTermChanged($date, $situation, $new_date, $new_situation){
    $message = $new_situation == 'night' ? '����������Ť��Ť����뤬��äƤ��ޤ�����'
        : '�뤬������' . $new_date . '���ܤ�ī����äƤ��ޤ�����';
    $this->output .= '<dt class="system"><br></dt><dd class="system">&lt&lt' . $message . "&gt&gt</dd>\n";
    return false;
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

  function OutputContentFooter(){
    switch($this->room->day_night){
    case 'day':
      shot($this->OutputVoteList($this->room->date - 1), 'GamePlayFormat::OutputVoteList');
      break;
    case 'night':
      shot($this->OutputVoteList($this->room->date), 'GamePlayFormat::OutputVoteList');
      break;
    }
    return parent::OutputContentFooter();
  }

  function FilterWords($category, &$talk, $date, $situation) {
    shot("$category / $situation", 'GamePlayFormat::FilterWords');
    if($this->room->IsAfterGame() || $this->room->IsBeforeGame()) {
      return true;
    }

    switch($category){
    case 'say':
      if($this->room->IsDay()) {
        return true;
      }
      elseif($this->room->IsNight()) {
        global $MESSAGE;
        switch ($talk->type){
        case 'self_talk':
          if($talk->uname == $this->self->uname){
            $talk->uname = 'self';
            return true;
          }
          return false;
        case 'wolf':
          if (!$this->self->IsRole('wolf', 'whisper_mad')){
            $talk->uname = 'wolf';
            $talk->sentence = $MESSAGE->wolf_howl;
          }
          return true;
        case 'common':
          if ($this->self->IsRole('common')){
            $talk->font_type = 'common';
          }
          else{
            $talk->uname = 'common';
            $talk->font_type = 'weak';
            $talk->sentence = $MESSAGE->common_talk;
          }
          return true;
        default:
          return $this->self->main_role == $talk->type;
        }
      }
    case 'objection':
      return true;
    case 'system_talk': 
      return true;
    default:
      return false;
    }
  }
}
?>
