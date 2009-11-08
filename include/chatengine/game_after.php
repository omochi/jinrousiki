<?php
require_once(CHEN_DIR.'/game_play.php');

class GameAfterFormat extends GamePlayFormat {
  function ParseUsers(){
    $user_cache = array();
    foreach ($this->users->rows as $user){
      $user_cache[$user->uname] = array (
        'class_attr' => 'u'.$user->user_no,
        'color' => $user->color,
        'display_name' => '◆'.$user->handle_name, 
        'additional_info' => "($user->uname)"
      );
    }
    $this->user_cache = $user_cache;
  }

  function OutputPlayerCell($user){
    global $DEBUG_MODE;
    $this_uname   = $user->uname;
    $this_info = $this->user_cache[$this_uname];
    $this_handle  = $this_info['display_name'];

    if($DEBUG_MODE) $this_handle .= ' (' . $user->user_no . ')';

    $icon = $this->GenerateUserIcon($user);
    $roles = $this->GenerateRoleText($user);

    $class_attr = count($this_classes) ? ' class="'.implode(' ', $this_classes).'"' : '';
    $this->output .= <<<CELL
<td{$class_attr}>
{$icon}
<ul>
<li class="{$this_info['class_attr']}">$this_handle</li>
<li>$display_live</li>
<li>$roles</li>
<li>({$this_uname})</li>
</ul>
</td>

CELL;
    return 'success';
  }

  function OutputGameInfo(){
    //過去の日のログへのリンク生成

    $link_format ='<li><a href="game_log.php?room_no=' . $this->room->id .
      '&date=%d&day_night=%s#game_top" target="_blank">%s</a></li>';

    $this->output .= '<div id="wayback_links"><h2>ログ</h2><ul>';
    $this->output .= sprintf($link_format, 0, 'beforegame', "0(開始前)");
    $this->output .= sprintf($link_format, 1, 'night', "1(夜)");
    for($day = 2; $day < $this->room->date; $day++){
      $this->output .= sprintf($link_format, 1, 'day', "{$day}(昼)");
      $this->output .= sprintf($link_format, 1, 'night', "{$day}(夜)");
    }
    $query = "SELECT COUNT(uname) FROM talk WHERE room_no = {$this->room->id} " .
      "AND date = {$this->room->date} AND location = 'day'";
    if(FetchResult($query) > 0){
      $this->output .= sprintf($link_format, $this->room->date, 'day', "{$this->room->date}(昼)");
    }
    $this->output .= "</ul></div>\n";
  }

  function OutputNotice(){
    //勝敗の出力
    global $MESSAGE;

    //勝利陣営を取得
    $game_result = FetchResult("SELECT victory_role FROM room WHERE room_no = {$this->room->id}");
    switch($game_result){
    //狐勝利系
      case 'fox1':
      case 'fox2':
        $camp_won = 'fox';
        break;
    //引き分け系
      case 'draw': //引き分け
      case 'vanish': //全滅
      case 'quiz_dead': //クイズ村 GM 死亡
        $camp_won = 'none';
        break;
    //廃村
      case NULL: 
        $game_result = 'none';
        break;
    //通常
      default:
        $camp_won = $game_result;
    }
    $winner  = 'victory_' . $game_result;

    //個人の勝敗をチェック
    if ($game_result != 'none') {
       //所属陣営を取得
      $my_camp   = $this->self->IsLovers() ? 'lovers' : $this->self->DistinguishCamp();
      //クイズGM死亡による引き分けの場合、クイズGMは敗者扱いになる。
      $my_result = ($camp_won == 'none') ?
        (($game_result == 'quiz_dead' && $my_camp == 'quiz') ? 'lose' : 'draw')
        : ($camp_won == $my_camp ? 'win' : 'lose');
      $li_personal_result = "<li>{$MESSAGE->$my_result}</li>";
    }

    //出力
    $this->output .= <<<EOF
<ul id="winner" class="victory-{$camp_won}">
<li>{$MESSAGE->$winner}</li>
{$li_personal_result}
</ul>

EOF;
    return 'success';
  }

  function OutputDeadList(){
  }

  function OutputLastWords(){
  }

  function FilterWords($category, &$talk, $date, $situation){
    return $this->room->IsAfterGame();
  }
}
?>