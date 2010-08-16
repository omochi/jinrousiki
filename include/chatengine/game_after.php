<?php
require_once(CHEN_DIR.'/game_base.php');

class GameAfterFormat extends GameBaseFormat {
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
    $this->OutputWaybackLinks();
    return 'success';
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

  function OutputEndTalk($date, $situation){
    $this->output .= '<dt class="bottom"></dt></dl>';
    $this->output .= "</div>\n";
    return 'success';
  }

  function FilterWords($category, &$talk, $date, $situation){
    return $this->room->IsAfterGame();
  }
}
?>
