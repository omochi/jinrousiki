<?php
//-- クラス定義 --//
//時間設定表示用クラス
class TimeCalculation{
  public $spend_day;      //非リアルタイム制の発言で消費される時間 (昼)
  public $spend_night;    //非リアルタイム制の発言で消費される時間 (夜)
  public $silence_day;    //非リアルタイム制の沈黙で経過する時間 (昼)
  public $silence_night;  //非リアルタイム制の沈黙で経過する時間 (夜)
  public $silence;        //非リアルタイム制の沈黙になるまでの時間
  public $sudden_death;   //制限時間を消費後に突然死するまでの時間
  public $die_room;       //自動廃村になるまでの時間
  public $establish_wait; //次の村を立てられるまでの待ち時間

  function __construct(){
    global $ROOM_CONF, $TIME_CONF;

    $day_seconds   = floor(12 * 60 * 60 / $TIME_CONF->day);
    $night_seconds = floor( 6 * 60 * 60 / $TIME_CONF->night);

    $this->spend_day      = ConvertTime($day_seconds);
    $this->spend_night    = ConvertTime($night_seconds);
    $this->silence_day    = ConvertTime($TIME_CONF->silence_pass * $day_seconds);
    $this->silence_night  = ConvertTime($TIME_CONF->silence_pass * $night_seconds);
    $this->silence        = ConvertTime($TIME_CONF->silence);
    $this->sudden_death   = ConvertTime($TIME_CONF->sudden_death);
    $this->die_room       = ConvertTime($ROOM_CONF->die_room);
    $this->establish_wait = ConvertTime($ROOM_CONF->establish_wait);
  }
}

//-- 関数定義 --//
//情報一覧ページ HTML ヘッダ出力
function OutputInfoPageHeader($title, $level = 0, $css = 'info'){
  $top  = str_repeat('../', $level + 1);
  $info = $level == 0 ? './' : str_repeat('../', $level);
  OutputHTMLHeader('[' . $title . ']', $css);
  echo <<<EOF
</head>
<body>
<h1>{$title}</h1>
<p>
<a href="{$top}" target="_top">&lt;= TOP</a>
<a href="{$info}" target="_top">←情報一覧</a>
</p>

EOF;
}

//役職情報ページ HTML ヘッダ出力
function OutputRolePageHeader($title){
  OutputHTMLHeader('新役職情報 - ' . '[' . $title . ']', 'new_role');
  echo <<<EOF
</head>
<body>
<h1>{$title}</h1>
<p>
<a href="../" target="_top">&lt;=情報一覧</a>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>

EOF;
}

//村の最大人数設定出力
function OutputMaxUser(){
  global $ROOM_CONF, $CAST_CONF;

  $str = '[ ' . implode('人・', $ROOM_CONF->max_user_list);
  $min_user = min(array_keys($CAST_CONF->role_list));
  $str .= '人 ] のどれかを村に登録できる村人の最大人数として設定することができます。<br>';
  $str .= "ただしゲームを開始するには最低 [ {$min_user}人 ] の村人が必要です。";
  echo $str;
}

//身代わり君がなれない役職のリスト出力
function OutputDisableDummyBoyRole(){
  global $ROLE_DATA, $CAST_CONF;

  $stack = array('人狼', '妖狐');
  foreach($CAST_CONF->disable_dummy_boy_role_list as $role){
    $stack[] = $ROLE_DATA->main_role_list[$role];
  }
  echo implode($stack, '・');
}

//配役テーブル出力
function OutputCastTable($min = 0, $max = NULL){
  global $ROLE_DATA, $CAST_CONF;

  //設定されている役職名を取得
  $stack = array();
  foreach($CAST_CONF->role_list as $key => $value){
    if($key < $min) continue;
    $stack = array_merge($stack, array_keys($value));
    if($key == $max) break;
  }
  $role_list = $ROLE_DATA->SortRole(array_unique($stack)); //表示順を決定

  $header = '<table class="member">';
  $str = '<tr><th>人口</th>';
  foreach($role_list as $role) $str .= $ROLE_DATA->GenerateMainRoleTag($role, 'th');
  $str .= '</tr>'."\n";
  echo $header . $str;

  //人数毎の配役を表示
  foreach($CAST_CONF->role_list as $key => $value){
    if($key < $min) continue;
    $tag = "<td><strong>{$key}</strong></td>";
    foreach($role_list as $role) $tag .= '<td>' . (int)$value[$role] . '</td>';
    echo '<tr>' . $tag . '</tr>'."\n";
    if($key == $max) break;
    if($key % 20 == 0) echo $str;
  }
  echo '</table>';
}

//追加役職の人数と説明ページリンク出力
function OutputAddRole($role){
  global $ROLE_DATA, $CAST_CONF;
  echo '村の人口が' . $CAST_CONF->$role . '人以上になったら' .
    $ROLE_DATA->GenerateRoleLink($role) . 'が登場します';
}

//お祭り村の配役リスト出力
function OutputFestivalList(){
  global $ROLE_DATA, $CAST_CONF;

  $stack  = $CAST_CONF->festival_role_list;
  $format = '%' . strlen(max(array_keys($stack))) . 's人：';
  $str    = '<pre>'."\n";
  ksort($stack); //人数順に並び替え
  foreach($stack as $count => $list){
    $order_stack = array();
    foreach($ROLE_DATA->SortRole(array_keys($list)) as $role){ //役職順に並び替え
      $order_stack[] = $ROLE_DATA->main_role_list[$role] . $list[$role];
    }
    $str .= sprintf($format, $count) . implode('　', $order_stack) . "\n";
  }
  echo $str . '</pre>'."\n";
}

//村人置換系オプションのサーバ設定出力
function OutputReplaceRole($option){
  global $ROLE_DATA, $CAST_CONF;
  echo 'は管理人がカスタムすることを前提にしたオプションです<br>現在の初期設定は全員' .
    $ROLE_DATA->GenerateRoleLink($CAST_CONF->replace_role_list[$option]) . 'になります';
}
