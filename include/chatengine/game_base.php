<?php
class GameBaseFormat extends ChatEngine {
  function ParseUsers(){
    $user_cache = array();
    foreach ($this->users->rows as $user){
      $user_cache[$user->uname] = array (
        'class_attr' => 'u'.$user->user_no,
        'color' => $user->color,
        'display_name' => '◆'.$user->handle_name
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
ゲームを開始するには全員がゲーム開始に投票する必要があります
<span>(投票した人は村人リストの背景が赤くなります)</span>
</div>
<table class="time-table">
<tr><td>ゲームオプション：{$option_image} </td></tr>
</table>

NOTICE;
  }

  function OutputTimelag() {
    global $SERVER_CONF;
    //時間ずれの表示
    $date_str = $SERVER_CONF->adjust_time_difference ?
                gmdate('Y, m, j, G, i, s', $this->room->system_time) : date('Y, m, j, G, i, s', $this->room->system_time);
    $this->output .= <<<NOTICE
<div>
サーバとローカルPCの時間ズレ(ラグ含)： <span>
<script type="text/javascript"><!--
output_diff_time('$date_str');
//--></script>秒</span>
</div>

NOTICE;
  }

  function OutputGameStatus() {
    //計測の結果、このクエリはパフォーマンスに大きな影響を与えないことが確認されました。
    $living_users = FetchResult(
      "SELECT COUNT(uname) FROM user_entry
      WHERE room_no = {$this->room->id}
        AND live = 'live' AND user_no > 0"
      );
    if($this->room->IsRealTime()){ //リアルタイム制
      GetRealPassTime($left_time);
      $time_text =
        '<form name="realtime_form"><input type="text" name="output_realtime" size="50" readonly></form>';
    }
    else{ //発言による仮想時間
      $time_text = $time_message . GetTalkPassTime($left_time);
    }
    $this->output .= <<<LIST
<ul id='game_info'>
<li id='date'>{$this->room->date} 日目</li>
<li id='alive'>(生存者{$living_users}人)</li>
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

    //アイコン
    $icon = $this->GenerateUserIcon($user);

    if($this->room->IsBeforeGame()){ //ゲームスタートに投票していれば色を変える
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
    //過去の日のログへのリンク生成
    $link_format ='<li><a href="game_log.php?room_no=' . $this->room->id .
      '&date=%d&day_night=%s#game_top" target="_blank">%s</a></li>';

    $list = '<div id="wayback_links"><h2>ログ</h2><ul>';
    $list .= sprintf($link_format, 0, 'beforegame', "0(開始前)");
    $list .= sprintf($link_format, 1, 'night', "1(夜)");
    for($day = 2; $day < $this->room->date; $day++){
      $list .= sprintf($link_format, 1, 'day', "{$day}(昼)");
      $list .= sprintf($link_format, 1, 'night', "{$day}(夜)");
    }
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$this->room->id} " .
      "AND date = {$this->room->date} AND location = 'day'";
    if(FetchResult($query) > 0){
      $list .= sprintf($link_format, $this->room->date, 'day', "{$this->room->date}(昼)");
    }
    $this->output .= $list . "</ul></div>\n";
  }

  //ユーザーを指定してアイコン表示用のimg要素を生成します。
  function GenerateUserIcon(&$user) {
    global $ICON_CONF;
    //ブラウザをチェック (MSIE @ Windows だけ 画像の Alt, Title 属性で改行できる)
    //IE の場合改行を \r\n に統一、その他のブラウザはスペースにする(画像のAlt属性)
    if($user->IsLive()){
      $icon_src = $ICON_CONF->path . '/' . $user->icon_filename;
      $display_live = '(生存中)';
    }
    else{
      $icon_src = $ICON_CONF->dead;
      $rollover_path = $ICON_CONF->path . '/' . $user->icon_filename;
      $display_live  = '(死亡)';
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