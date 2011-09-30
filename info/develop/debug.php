<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('info_functions');
OutputInfoPageHeader('デバッグ情報', 1);
?>
<p>
Ver. 1.5.0
<a href="#ver150a2">α2</a>
<a href="#ver150a3">α3</a>
<a href="#ver150a4">α4</a>
<a href="#ver150a5">α5</a>
<a href="#ver150a7">α7</a>
<a href="#ver150b1">β1</a>
<a href="#ver150b5">β5</a>
<a href="#ver150b9">β9</a>
<a href="#ver150b11">β11</a>
<a href="#ver150b12">β12</a>
</p>
<p>
<a href="#ver140">Ver. 1.4.0</a>
<a href="#ver140a24">α24</a>
<a href="#ver140b2">β2</a>
<a href="#ver140b3">β3</a>
<a href="#ver140b4">β4</a>
<a href="#ver140b11">β11</a>
<a href="#ver140b12">β12</a>
<a href="#ver140b13">β13</a>
<a href="#ver140b16">β16</a>
<a href="#ver140b17">β17</a>
<a href="#ver140b18">β18</a>
<a href="#ver140b19">β19</a>
<a href="#ver140b20">β20</a>
<a href="#ver140b21">β21</a>
<a href="#ver140b22">β22</a>
</p>

<h2 id="ver150b12">Ver. 1.5.0 β12</h2>
<h3>include/role/role_class.php % 217行目付近 (2011/09/26 (Mon) 04:07)</h3>
<h4>[before]</h4>
<pre>
  function __construct(){
    $this->role = array_pop(explode('Role_', get_class($this)));
    if(isset($this->mix_in)) $this->LoadMix($this->mix_in);
  }
</pre>
<h4>[after]</h4>
<pre>
  function __construct(){
    $this->role = array_pop(explode('Role_', get_class($this)));
    if(isset($this->mix_in)) $this->LoadMix($this->mix_in);
  }

  function __call($name, $args){
    return call_user_func_array(array($this->filter, $name), $args);
  }
</pre>

<h3>include/game_vote_functions.php % 2433行目付近 (2011/09/26 (Mon) 19:25)</h3>
<h4>[before]</h4>
<pre>
PrintData($target->uname);
$target->ReturnPossessed('possessed');
#$USERS->ByReal($target->user_no)->ReturnPossessed('possessed_target');
</pre>
<h4>[after]</h4>
<pre>
$target->ReturnPossessed('possessed');
</pre>

<h3>include/role/priest.php % 58行目付近 (2011/09/29 (Thu) 03:20)</h3>
<pre>
× $flag = $ROOM->date > 3 && ($ROOM->date % 2) == 0;
○ $flag = $ROOM->date > 1 && ($ROOM->date % 2) == 0;
</pre>

<h2 id="ver150b11">Ver. 1.5.0 β11</h2>
<h3>room_manager.php % 265行目付近 (2011/08/27 (Sat) 05:20)</h3>
<h4>[before]</h4>
<pre>
  break;
}
</pre>
<h4>[after]</h4>
<pre>
  break;

default:
  continue 3;
}
</pre>

<h3>include/game_vote_functions.php % 2638行目付近 (2011/08/30 (Tue) 16:56)</h3>
<h4>[before]</h4>
<pre>
foreach($ROOM->vote as $stack){
 if($user->IsSame($stack['target_uname'])) $count++;
}
</pre>
<h4>[after]</h4>
<pre>
foreach($ROOM->vote as $vote_stack){
  foreach($vote_stack as $stack){
    if($user->IsSame($stack['target_uname'])) $count++;
  }
}
</pre>

<h3>include/role/power_yaksa.php % 8行目付近 (2011/09/23 (Fri) 01:13)</h3>
<h4>[before]</h4>
<pre>
class Role_power_yaksa extends Role{
</pre>
<h4>[after]</h4>
<pre>
RoleManager::LoadFile('yaksa');
class Role_power_yaksa extends Role_yaksa{
</pre>

<h2 id="ver150b9">Ver. 1.5.0 β9</h2>
<h3>room_manager.php % 250行目付近 (2011/08/11 (Thu) 13:19)</h3>
<h4>[before]</h4>
<pre>
case 'chaos_open_cast':
  if(! $ROOM_CONF->$option) continue 2;
  switch($target = $_POST[$option]){
  case 'full':
    break 2;

  case 'camp':
  case 'role':
    if($ROOM_CONF->{'_' . $target}){
      $option .= '_' . $target;
      break 2;
    }
  }
  continue 2;
</pre>
<h4>[after]</h4>
<pre>
case 'chaos_open_cast':
  switch($target = $_POST[$option]){
  case 'full':
    break;

  case 'camp':
  case 'role':
    $option .= '_' . $target;
    break;
  }
  if($ROOM_CONF->$option) break;
  continue 2;
</pre>

<h3>include/role/duelist.php % 11行目付近 (2011/08/14 (Sun) 00:59)</h3>
<pre>
× $role = $this->GetActor()->GetID('rival')
○ $role = $this->GetActor()->GetID('rival');
</pre>

<h2 id="ver150b6">Ver. 1.5.0 β6</h2>
<h3>include/game_play_functions.php % 651行目付近 (2011/07/12 (Tue) 03:41)</h3>
<h4>[before]</h4>
<pre>
}
OutputPartner($stack, 'partner_header', 'lovers_footer');
</pre>
<h4>[after]</h4>
<pre>
  OutputPartner($stack, 'partner_header', 'lovers_footer');
}
</pre>

<h2 id="ver150b5">Ver. 1.5.0 β5</h2>
<h3>include/game_play_functions.php % 332行目付近 (2011/06/29 (Wed) 00:21)</h3>
<h4>[before]</h4>
<pre>
  if($user->IsWolf(true)) $stack[] = $USERS->GetHandleName($user->uname, true);
}
OutputPartner($stack, 'wolf_partner');
</pre>
<h4>[after]</h4>
<pre>
}
OutputPartner($stack['wolf'], 'wolf_partner');
</pre>
<h3>include/role/mind_scanner.php % 8行目付近 (2011/07/01 (Fri) 01:42)</h3>
<pre>
× public $mind_role = 'mind_role';
○ public $mind_role = 'mind_read';
</pre>

<h2 id="ver150b1">Ver. 1.5.0 β1</h2>
<h3>include/game_vote_functions.php % 1640行目付近 (2011/05/19 (Thu) 02:55)</h3>
<pre>
× break;
○ break 2;
</pre>

<h2 id="ver150a7">Ver. 1.5.0 α7</h2>
<h3>include/role/betray_yaksa.php % 12行目付近 (2011/04/11 (Mon) 00:16)</h3>
<pre>
× function Ignored($user){ return ! $target->IsCamp('chiroptera', true); }
○ function Ignored($user){ return ! $user->IsCamp('chiroptera', true); }
</pre>

<h2 id="ver150a5">Ver. 1.5.0 α5</h2>
<h3>include/game_functions.php % 185行目付近 (2011/03/10 (Thu) 23:51)</h3>
<pre>
× return GetSelfVoteNight($situation, $not_situation) > 0;
○ return GetSelfVoteNight($situation, $not_situation) !== false;
</pre>

<h3>include/game_functions.php % 168行目付近 (2011/03/11 (Fri) 00:43)</h3>
<pre>
× $query = $ROOM->GetQueryHeader('vote', 'uname') . ' AND ';
○ $query = $ROOM->GetQueryHeader('vote', 'target_uname') . ' AND date = ' . $ROOM->date . ' AND ';
</pre>

<h2 id="ver150a4">Ver. 1.5.0 α4</h2>
<h3>include/game_vote_functions.php % 2648行目付近 (2011/02/24 (Thu) 08:24)</h3>
<h4>[before]</h4>
<pre>
$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
</pre>
<h4>[after]</h4>
<pre>
$target->ReturnPossessed('possessed_target', $ROOM->date + 1);
$stack = $virtual_target->GetPartner('possessed');
if($target->user_no == $stack[max(array_keys($stack))]){
  $virtual_target->ReturnPossessed('possessed', $ROOM->date + 1);
}
</pre>
<h3>include/game_vote_functions.php % 2103行目付近 (2011/02/25 (Fri) 02:54)</h3>
<h4>[before]</h4>
<pre>
else{
  continue;
}
</pre>
<h4>[after]</h4>
<pre>
elseif($voted_wolf->IsRole('possessed_wolf') && $voted_wolf->IsSame($target->uname)){
  $voted_wolf->possessed_cancel = true;
}
else{
  continue;
}
</pre>

<h2 id="ver150a3">Ver. 1.5.0 α3</h2>
<h3>include/game_vote_functions.php % 1527行目付近 (2011/02/07 (Mon) 22:22)</h3>
<h4>[before]</h4>
<pre>
if($user->IsRole('dummy_guard')){ //夢守人は罠無効
  if($ROOM->IsEvent('no_dream')) continue; //熱帯夜ならスキップ
  $dummy_guard_target_list[$user->uname] = $target_uname;
}
</pre>
<h4>[after]</h4>
<pre>
if($user->IsRole('dummy_guard')){ //夢守人は罠無効
  if($ROOM->IsEvent('no_dream')) continue; //熱帯夜ならスキップ
  $dummy_guard_target_list[$user->uname] = $target_uname;
  continue;
}
</pre>

<h2 id="ver150a2">Ver. 1.5.0 α2</h2>
<h3>include/game_vote_functions.php % 2247行目付近 (2011/01/24 (Mon) 02:14)</h3>
<pre>
× if($user->IsRole('emerald_fox') || $user->IsRoleGroup('fairy')) continue;
○ if($user->IsRole('emerald_fox') || $user->IsRoleGroup('fairy') || $wizard_target_list[$uname] == 'light_fairy') continue;
</pre>

<h2 id="ver140">Ver. 1.4.0</h2>
<h3>room_manager.php % 312行目付近 (2010/12/28 (Tue) 19:10)</h3>
<pre>
× 0, in_array('gerd', $option_role_list) ? $USER_ICON->gerd : 0)) break;
○ 1, in_array('gerd', $option_role_list) ? $USER_ICON->gerd : 0)) break;
</pre>
<h3>config/server_config % 93行目付近 (2010/12/28 (Tue) 19:10)</h3>
<h4>[before]</h4>
<pre>
  //表示する他のサーバのリスト
  var $server_list = array(
     /* 設定例
    'cirno' => array('name' => 'チルノ鯖',
                     'url' => 'http://www12.atpages.jp/cirno/',
                     'encode' => 'UTF-8',
                     'separator' =&gt; '&lt;!-- atpages banner tag --&gt;',
                     'footer' =&gt; '&lt;/a&gt;&lt;br&gt;',
                     'disable' => false),
     */
}
</pre>
<h4>[after]</h4>
<pre>
  //表示する他のサーバのリスト
  var $server_list = array(
     /* 設定例
    'cirno' => array('name' => 'チルノ鯖',
                     'url' => 'http://www12.atpages.jp/cirno/',
                     'encode' => 'UTF-8',
                     'separator' =&gt; '&lt;!-- atpages banner tag --&gt;',
                     'footer' =&gt; '&lt;/a&gt;&lt;br&gt;',
                     'disable' => false),
     */
                          );
}
</pre>

<h2 id="ver140b22">Ver. 1.4.0 β22</h2>
<h3>game_vote.php % 261行目付近 (2010/12/07 (Tue) 00:09)</h3>
<pre>
× if($SELF->IsRole('evoke_scanner')){
○ elseif($SELF->IsRole('evoke_scanner')){
</pre>

<h2 id="ver140b21">Ver. 1.4.0 β21</h2>
<h3>room_manager.php % 178行目付近 (2010/11/23 (Tue) 22:45)</h3>
<h4>[before]</h4>
<pre>
array_push($check_game_option_list, 'deep_sleep', 'mind_open', 'blinder');
$check_option_role_list[] = 'joker';
</pre>
<h4>[after]</h4>
<pre>
array_push($check_game_option_list, 'joker', 'deep_sleep', 'mind_open', 'blinder');
</pre>
<h3>include/user_class.php % 1200行目付近 (2010/11/23 (Tue) 22:45)</h3>
<h4>[before]</h4>
<pre>
$stack = array();
</pre>
<h4>[after]</h4>
<pre>
if(! $ROOM->IsOption('joker')) return false;
$stack = array();
</pre>
<h3>include/game_vote_functions.php % 1222行目付近 (2010/11/23 (Tue) 22:45)</h3>
<pre>
× $joker_flag = false; //ジョーカー移動成立フラグ
○ $joker_flag = ! $ROOM->IsOption('joker'); //ジョーカー移動成立フラグ
</pre>
<h3>include/game_vote_functions.php % 2724行目付近 (2010/11/24 (Wed) 21:00)</h3>
<pre>
× if($role_flag->bishop_priest && $user->GetCamp(true) != 'human') $live_count['dead']++;
○ if($user->GetCamp(true) != 'human') $live_count['dead']++;
</pre>
<h3>include/game_vote_functions.php % 2733行目付近 (2010/11/24 (Wed) 21:00)</h3>
<pre>
× if($role_flag->priest && $user->GetCamp() == 'human') $live_count['human_side']++;
○ if($user->GetCamp() == 'human') $live_count['human_side']++;
</pre>

<h2 id="ver140b20">Ver. 1.4.0 β20</h2>
<h3>include/game_format.php % 22行目付近 (2010/11/15 (Mon) 03:16)</h3>
<pre>
× $SELF->live->virtual_live = true;
○ $SELF->virtual_live = true;
</pre>
<h3>include/game_vote_functions.php % 817行目付近 (2010/11/16 (Tue) 05:57)</h3>
<h4>[before]</h4>
<pre>
  //リストにデータを追加
  $live_uname_list[$user->user_no] = $user->uname;
  $vote_message_list[$user->uname] = $message_list;
  $vote_target_list[$user->uname]  = $target->uname;
  $vote_count_list[$user->uname]   = $voted_number;
  foreach($ROLES->Load('vote_ability') as $filter) $filter->SetVoteAbility($target->uname);
}
</pre>
<h4>[after]</h4>
<pre>
  //リストにデータを追加
  $live_uname_list[$user->user_no]   = $user->uname;
  $vote_message_list[$user->user_no] = $message_list;
  $vote_target_list[$user->uname]    = $target->uname;
  $vote_count_list[$user->uname]     = $voted_number;
  foreach($ROLES->Load('vote_ability') as $filter) $filter->SetVoteAbility($target->uname);
}
ksort($vote_message_list);
$stack = array();
foreach($vote_message_list as $id => $list) $stack[$USERS->ByID($id)->uname] = $list;
$vote_message_list = $stack;
</pre>

<h2 id="ver140b19">Ver. 1.4.0 β19</h2>
<h3>game_play.php % 264行目付近 (2010/11/06 (Sat) 04:12)</h3>
<h4>[before]</h4>
<pre>
$user = $USERS->ByVirtual($SELF->user_no);
if($ROOM->IsPlaying() && $user->IsLive()){
  $ROLES->actor = $user;
  foreach($ROLES->Load('voice') as $filter) $filter->FilterVoice($voice, $say);
}
</pre>
<h4>[after]</h4>
<pre>
if($ROOM->IsPlaying() && $SELF->IsLive()){
  $ROLES->actor = $USERS->ByVirtual($SELF->user_no);
  foreach($ROLES->Load('voice') as $filter) $filter->FilterVoice($voice, $say);
}
</pre>
<h3>include/role/role_class.php % 132行目付近 (2010/11/06 (Sat) 04:12)</h3>
<h4>[before]</h4>
<pre>
function Ignored(){
  global $ROOM, $ROLES;
  //return false; //テスト用
  return ! ($ROOM->IsPlaying() && $ROLES->actor->IsLive());
}
</pre>
<h4>[after]</h4>
<pre>
function Ignored(){
  global $ROOM, $USERS, $ROLES;
  //return false; //テスト用
  return ! ($ROOM->IsPlaying() && $USERS->IsVirtualLive($ROLES->actor->user_no));
}
</pre>
<h3>include/game_vote_functions.php % 2591行目付近 (2010/11/06 (Sat) 05:09)</h3>
<pre>
× 'ogre' => 'yaksa');
○ 'yaksa' => 'yaksa');
</pre>
<h3>include/game_vote_functions.php % 2619行目付近 (2010/11/06 (Sat) 05:09)</h3>
<pre>
× 'ogre' => 'succubus_yaksa');
○ 'yaksa' => 'succubus_yaksa');
</pre>

<h2 id="ver140b18">Ver. 1.4.0 β18</h2>
<h3>include/user_class.php % 432行目付近 (2010/10/16 (Sat) 03:18)</h3>
<pre>
× if($this->IsRole('mind_scanner')) return $this->IsVoted($vote_data, 'MIND_SCANNER_DO');
○ if($this->IsRole('mind_scanner', 'presage_scanner')) return $this->IsVoted($vote_data, 'MIND_SCANNER_DO');
</pre>
<h3>img/role/ (2010/10/18 (Mon) 05:33)</h3>
<pre>
× result_succbus_vampire.gif
○ result_succubus_vampire.gif
</pre>
<h3>game_vote.php % 143行目付近 (2010/10/20 (Wed) 04:56)</h3>
<pre>
× if(FetchResult($ROOM->GetQueryHeader('room', 'day_night') != 'beforegame')){
○ if(FetchResult($ROOM->GetQueryHeader('room', 'day_night')) != 'beforegame'){
</pre>

<h2 id="ver140b17">Ver. 1.4.0 β17</h2>
<h3>include/user_class.php % 370行目付近 (2010/10/04 (Mon) 00:44)</h3>
<pre>
× return $result && ! $reverse ? 'wolf' : 'human';
○ return ($result xor $reverse) ? 'wolf' : 'human';
</pre>
<h3>include/room_class.php % 306行目付近 (2010/10/08 (Fri) 02:21)</h3>
<pre>
× if(empty($uname)) $uname = 'system';
○ if($uname == '') $uname = 'system';
</pre>

<h2 id="ver140b16">Ver. 1.4.0 β16</h2>
<h3>include/game_vote_functions.php % 1474行目付近</h3>
<h4>[before]</h4>
<pre>
if($target->IsSame($vote_kill_uname)) continue;
if($target->IsActive($stack)) $target->LostAbility();
elseif($target->IsRole('lost_ability')){
	$USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_SEALED');
}
</pre>
<h4>[after]</h4>
<pre>
if($target->IsSame($vote_kill_uname) || ! $target->IsRole($stack)) continue;
$target->IsActive() ? $target->LostAbility() :
  $USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_SEALED');
</pre>
<h3>include/user_class.php % 409行目付近 (2010/08/31 (Tue) 03:59)</h3>
<pre>
× $this->IsVoted($vote_data, 'MAGE_DO');
○ return $this->IsVoted($vote_data, 'MAGE_DO');
</pre>
<h3>include/user_class.php % 897行目付近 (2010/09/16 (Thu) 04:22)</h3>
<h4>[before]</h4>
<pre>
$target = $user;
do{ //覚醒者・夢語部ならコピー先を辿る
  if(! $target->IsRole('soul_mania', 'dummy_mania')) break;
  $stack = $target->GetPartner($target->main_role);
  if(is_null($stack)) break; //コピー先が見つからなければスキップ

  $target = $this->ByID($stack[0]);
  if($target->IsRoleGroup('mania')) $target = $user; //神話マニア系なら元に戻す
}while(false);

while($target->IsRole('unknown_mania')){ //鵺ならコピー先を辿る
  $stack = $target->GetPartner('unknown_mania');
  if(is_null($stack)) break; //コピー先が見つからなければスキップ

  $target = $this->ByID($stack[0]);
  if($target->IsSelf()) break; //自分に戻ったらスキップ
}
</pre>
<h4>[after]</h4>
<pre>
$target = $user;
$stack  = array();
while($target->IsRole('unknown_mania')){ //鵺ならコピー先を辿る
  $id = array_shift($target->GetPartner('unknown_mania', true));
  if(is_null($id) || in_array($id, $stack)) break;
  $stack[] = $id;
  $target  = $this->ByID($id);
}

//覚醒者・夢語部ならコピー先を辿る
if($target->IsRole('soul_mania', 'dummy_mania') &&
   is_array($stack = $target->GetPartner($target->main_role))){
  $target = $this->ByID(array_shift($stack));
  if($target->IsRoleGroup('mania')) $target = $user; //神話マニア系なら元に戻す
}
</pre>

<h2 id="ver140b13">Ver. 1.4.0 β13</h2>
<h3>include/game_vote_functions.php % 973行目付近</h3>
<pre>
× $delete_role_list = array('lovers', 'admire_lovers', 'copied', 'copied_trick', 'copied_soul',
○ $delete_role_list = array('lovers', 'challenge_lovers', 'copied', 'copied_trick', 'copied_soul',
</pre>
<h3>include/game_vote_functions.php % 2510行目付近 (2010/07/19 (Mon) 09:41)</h3>
<h4>[before]</h4>
<pre>
case 'doll_master':
</pre>
<h4>[after]</h4>
<pre>
case 'whisper_scanner':
case 'howl_scanner':
case 'telepath_scanner':
  $stack_role = 'mind_scanner';
  break;

case 'doll_master':
</pre>
<h3>game_vote.php % 490行目付近 (2010/07/20 (Tue) 01:58)</h3>
<h4>[before]</h4>
<pre>
$target->AddRole($add_role);
</pre>
<h4>[after]</h4>
<pre>
$target->AddRole($add_role);
$target->ParseRoles($target->GetRole());
</pre>
<h3>include/game_functions.php % 835行目付近 (2010/07/21 (Wed) 01:02)</h3>
<pre>
× elseif($said_user->IsLonely('silver_wolf')){
○ elseif($said_user->IsWolf() && $said_user->IsLonely()){
</pre>

<h2 id="ver140b12">Ver. 1.4.0 β12</h2>
<h3>include/game_vote_functinons.php % 176行目付近</h3>
<h4>[before]</h4>
<pre>
	$random_replace_list = $CAST_CONF->GenerateRandomList($replace_human_list);
	$CAST_CONF->AddRandom($role_list, $random_replace_list, $over_count);
</pre>
<h4>[after]</h4>
<pre>
	$CAST_CONF->AddRandom($role_list, $replace_human_list, $over_count);
</pre>

<h2 id="ver140b11">Ver. 1.4.0 β11</h2>
<h3>include/user_class.php % 190行目付近</h3>
<h4>[before]</h4>
<pre>
  function IsLonely(){
    return $is_role && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }
</pre>
<h4>[after]</h4>
<pre>
  function IsLonely($role = NULL){
    $is_role = is_null($role) ? true : $this->IsRole($role);
    return $is_role && ($this->IsRole('mind_lonely') || $this->IsRoleGroup('silver'));
  }
</pre>
<h3>include/user_class.php % 230行目付近 (2010/07/07 (Wed) 21:40)</h3>
<pre>
× return $ROOM->date > 1 && $ROOM < 5 && $this->IsRole('challenge_lovers');
○ return $ROOM->date > 1 && $ROOM->date < 5 && $this->IsRole('challenge_lovers');
</pre>
<h3>game_vote.php % 295行目付近 (2010/07/07 (Wed) 23:16)</h3>
<pre>
× if(! $SELF->IsRole('scanner', 'evoke_scanner')){
○ if(! $SELF->IsRole('mind_scanner', 'evoke_scanner')){
</pre>
<h3>include/game_vote_functions.php % 2009行目付近 (2010/07/09 (Fri) 01:18)</h3>
<pre>
× if($target->IsRole('escaper')) break; //逃亡者は暗殺不可
○ if($target->IsRole('escaper')) continue; //逃亡者は暗殺不可
</pre>
<h3>include/game_functions.php % 834行目付近 (2010/07/11 (Sun) 02:22)</h3>
<pre>
× elseif($said_user->IsLonely('wolf')){
○ elseif($said_user->IsLonely('silver_wolf')){
</pre>

<h2 id="ver140b4">Ver. 1.4.0 β4</h2>
<h3>user_manager.php % 35行目付近</h3>
<h4>[before]</h4>
<pre>
  //項目被りチェック
</pre>
<h4>[after]</h4>
<pre>
  $query = "SELECT COUNT(icon_no) FROM user_icon WHERE icon_no = " . $icon_no;
  if(FetchResult($query) < 1) OutputActionResult('村人登録 [入力エラー]', '無効なアイコン番号です');

  //項目被りチェック
</pre>

<h3>user_manager.php % 275行目付近 (2010/02/24 (Wed) 21:40)</h3>
<h4>[before]</h4>
<pre>
if($ROOM->IsOptionGroup('mania')) $wish_role_list[] = 'mania';
</pre>
<h4>[after]</h4>
<pre>
if($ROOM->IsOptionGroup('mania') && ! in_array('mania', $wish_role_list)){
  $wish_role_list[] = 'mania';
}
</pre>

<h3>include/game_functons.php % 751行目付近 (2010/02/28 (Sun) 02:00)</h3>
<h4>[before]</h4>
<pre>
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>
<h4>[after]</h4>
<pre>
LineToBR($sentence);
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>

<h3>game_vote.php % 352行目付近 (2010/02/28 (Sun) 20:25)</h3>
<pre>
× $sub_role_list = $GAME_CONF->sub_role_group_list['sudden-death'];
○ $sub_role_list = array_diff($GAME_CONF->sub_role_group_list['sudden-death'], array('panelist'));
</pre>

<h2 id="ver140b3">Ver. 1.4.0 β3</h2>
<h3>game_play.php % 259行目付近</h3>
<pre>
× if($ROOM->IsPlaying() && $virtual->IsLive()){
○ if($ROOM->IsPlaying() && $virtual_self->IsLive()){
</pre>

<h3>include/game_format.php % 60行目付近</h3>
<pre>
× global $RQ_ARGS;
○ global $GAME_CONF, $RQ_ARGS;
</pre>

<h3>include/game_format.php % 83行目付近</h3>
<h4>[before]</h4>
<pre>
if($RQ_ARGS->add_role) $handle_name .= $user->GenarateShortRoleName(); //役職表示モード対応
</pre>
<h4>[after]</h4>
<pre>
if($RQ_ARGS->add_role){ //役職表示モード対応
  $real_user = $talk->scene == 'heaven' ? $user : $USERS->ByReal($user->user_no);
  $handle_name .= $real_user->GenerateShortRoleName();
}
</pre>

<h3>include/talk_class.php % 38行目付近</h3>
<h4>[before]</h4>
<pre>
case 'dummy_boy':
  if($this->type == $this->uname){
</pre>
<h4>[after]</h4>
<pre>
case 'dummy_boy':
  if($this->type == 'system') break;
  if($this->type == $this->uname){
</pre>

<h3>include/game_functions.php % 236行目付近 (2010/02/22 (Mon) 23:00)</h3>
<pre>
× $handle_name .= $real_user->GenarateShortRoleName();
○ $handle_name .= $real_user->GenerateShortRoleName();
</pre>

<h3>include/user_class.php % 216行目付近</h3>
<pre>
× function GenarateShortRoleName(){
○ function GenerateShortRoleName(){
</pre>

<h3>include/game_functons.php % 461行目付近 (2010/02/28 (Sun) 02:00)</h3>
<h4>[before]</h4>
<pre>
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>
<h4>[after]</h4>
<pre>
LineToBR($sentence);
$builder->AddSystemTalk($sentence, 'dummy-boy');
</pre>


<h2 id="ver140b2">Ver. 1.4.0 β2</h2>
<h3>include/game_vote_functions.php % 1188行目</h3>
<pre>
× elseif(! $ROOM->IsOpenCast() && $user->IsGroup('evoke_scanner')){
○ elseif(! $ROOM->IsOpenCast() && $user->IsRole('evoke_scanner')){
</pre>

<h3>game_play.php % 449 行目</h3>
<pre>
× array_push($actor_list, 'poison_cat');
○ array_push($actor_list, '%cat', 'revive_fox');
</pre>

<h2 id="ver140a24">Ver. 1.4.0 α24</h2>
<h3>game_play.php % 731 行目</h3>
<pre>
× $USERS->GetHandleName($target_uname) . 'さんに投票済み');
○ $USERS->GetHandleName($target_uname, true) . 'さんに投票済み');
</pre>

<h3>include/game_functions.php % 705 行目</h3>
<pre>
×elseif($pseud_self->IsRole('wise_wolf')){
○elseif($virtual_self->IsRole('wise_wolf')){
</pre>

<h3>user_manager.php % 276 行目 (2010/01/30 02:30)</h3>
<pre>
× array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',
○ array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',
</pre>

<h3>include/game_functions.php % 400 行目付近 (2010/02/01 (Mon) 00:15)</h3>
<h4>[before]</h4>
<pre>
$said_user = $USERS->ByVirtualUname($talk->uname);
</pre>
<h4>[after]</h4>
<pre>
if(strpos($talk->location, 'heaven') === false)
  $said_user = $USERS->ByVirtualUname($talk->uname);
else
  $said_user = $USERS->ByUname($talk->uname);
</pre>

<h3>include/game_vote_functions % 1865 行目付近</h3>
<h4>[before]</h4>
<pre>
$target->dead_flag = false; //死亡フラグをリセット
$USERS->Kill($target->user_no, 'WOLF_KILLED');
if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
</pre>
<h4>[after]</h4>
<pre>
if(isset($target->user_no)){
  $target->dead_flag = false; //死亡フラグをリセット
  $USERS->Kill($target->user_no, 'WOLF_KILLED');
  if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応
}
</pre>
</body></html>
