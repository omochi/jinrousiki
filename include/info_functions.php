<?php
//-- クラス定義 --//
//時間設定表示用クラス
class TimeCalculation {
  public $spend_day;      //非リアルタイム制の発言で消費される時間 (昼)
  public $spend_night;    //非リアルタイム制の発言で消費される時間 (夜)
  public $silence_day;    //非リアルタイム制の沈黙で経過する時間 (昼)
  public $silence_night;  //非リアルタイム制の沈黙で経過する時間 (夜)
  public $silence;        //非リアルタイム制の沈黙になるまでの時間
  public $sudden_death;   //制限時間を消費後に突然死するまでの時間
  public $alert;          //警告音開始
  public $alert_distance; //警告音の間隔
  public $die_room;       //自動廃村になるまでの時間
  public $establish_wait; //次の村を立てられるまでの待ち時間

  function __construct() {
    $day_seconds   = floor(12 * 60 * 60 / TimeConfig::DAY);
    $night_seconds = floor( 6 * 60 * 60 / TimeConfig::NIGHT);

    $this->spend_day      = Time::Convert($day_seconds);
    $this->spend_night    = Time::Convert($night_seconds);
    $this->silence_day    = Time::Convert(TimeConfig::SILENCE_PASS * $day_seconds);
    $this->silence_night  = Time::Convert(TimeConfig::SILENCE_PASS * $night_seconds);
    $this->silence        = Time::Convert(TimeConfig::SILENCE);
    $this->sudden_death   = Time::Convert(TimeConfig::SUDDEN_DEATH);
    $this->alert          = Time::Convert(TimeConfig::ALERT);
    $this->alert_distance = Time::Convert(TimeConfig::ALERT_DISTANCE);
    $this->die_room       = Time::Convert(RoomConfig::DIE_ROOM);
    $this->establish_wait = Time::Convert(RoomConfig::ESTABLISH_WAIT);
  }
}

//-- 関数定義 --//
//情報一覧ページ HTML ヘッダ出力
function OutputInfoPageHeader($title, $level = 0, $css = 'info'){
  $top  = str_repeat('../', $level + 1);
  $info = $level == 0 ? './' : str_repeat('../', $level);
  HTML::OutputHeader(sprintf('[%s]', $title), 'info/' . $css, true);
  echo <<<EOF
<h1>{$title}</h1>
<p>
<a target="_top" href="{$top}">&lt;= TOP</a>
<a target="_top" href="{$info}">← 情報一覧</a>
</p>

EOF;
}

//役職情報ページ HTML ヘッダ出力
function OutputRolePageHeader($title){
  HTML::OutputHeader(sprintf('新役職情報 - [%s]', $title), 'new_role', true);
  echo <<<EOF
<h1>{$title}</h1>
<p>
<a target="_top" href="../">&lt;= 情報一覧</a>
<a target="_top" href="./">&lt;- メニュー</a>
<a href="summary.php">← 一覧表</a>
</p>

EOF;
}

//配役テーブル出力
function OutputCastTable($min = 0, $max = null){
  //設定されている役職名を取得
  $stack = array();
  foreach (CastConfig::$role_list as $key => $value) {
    if ($key < $min) continue;
    $stack = array_merge($stack, array_keys($value));
    if ($key == $max) break;
  }
  $role_list = RoleData::SortRole(array_unique($stack)); //表示順を決定

  $header = '<table class="member">';
  $str = '<tr><th>人口</th>';
  foreach ($role_list as $role) $str .= RoleData::GenerateMainRoleTag($role, 'th');
  $str .= '</tr>'."\n";
  echo $header . $str;

  //人数毎の配役を表示
  foreach (CastConfig::$role_list as $key => $value) {
    if ($key < $min) continue;
    $tag = "<td><strong>{$key}</strong></td>";
    foreach ($role_list as $role) {
      $tag .= '<td>' . (isset($value[$role]) ? $value[$role] : 0) . '</td>';
    }
    echo '<tr>' . $tag . '</tr>'."\n";
    if ($key == $max) break;
    if ($key % 20 == 0) echo $str;
  }
  echo '</table>';
}

//カテゴリ別ページ内リンク出力
function OutputCategoryLink($list) {
  foreach ($list as $name) {
    printf("<a href=\"#%s\">%s</a>\n", $name, OptionManager::GenerateCaption($name));
  }
}

//他のサーバの部屋画面ロード用データを出力
function OutputSharedRoomList(){
  if (SharedServerConfig::DISABLE) return false;

  $str = HTML::LoadJavaScript('shared_room');
  $count = 0;
  foreach (SharedServerConfig::$server_list as $server => $array) {
    $count++;
    extract($array);
    if ($disable) continue;

    $str .= <<<EOF
<div id="server{$count}"></div>
<script language="javascript"><!--
output_shared_room({$count}, "server{$count}");
--></script>

EOF;
  }
  echo $str;
}

//他のサーバの部屋画面を出力
function OutputSharedRoom($id){
  if (SharedServerConfig::DISABLE) return false;

  $count = 0;
  foreach (SharedServerConfig::$server_list as $server => $array) {
    if ($count++ == $id) break;
  }
  extract($array);
  if ($disable) return false;

  if (! ExternalLinkBuilder::CheckConnection($url)) { //サーバ通信状態チェック
    $data = ExternalLinkBuilder::GenerateTimeOut($url);
    echo ExternalLinkBuilder::GenerateSharedServerRoom($name, $url, $data);
    return false;
  }

  //部屋情報を取得
  if (($data = @file_get_contents($url.'room_manager.php')) == '') return false;
  if ($encode != '' && $encode != ServerConfig::ENCODE) {
    $data = mb_convert_encoding($data, ServerConfig::ENCODE, $encode);
  }
  if (ord($data{0}) == '0xef' && ord($data{1}) == '0xbb' && ord($data{2}) == '0xbf') { //BOM 消去
    $data = substr($data, 3);
  }
  if ($separator != '') {
    $split_list = mb_split($separator, $data);
    $data = array_pop($split_list);
  }
  if ($footer != '') {
    if (($position = mb_strrpos($data, $footer)) === false) return false;
    $data = mb_substr($data, 0, $position + mb_strlen($footer));
  }
  if ($data == '') return false;

  $replace_list = array('href="' => 'href="' . $url, 'src="'  => 'src="' . $url);
  $data = strtr($data, $replace_list);
  echo ExternalLinkBuilder::GenerateSharedServerRoom($name, $url, $data);
}
