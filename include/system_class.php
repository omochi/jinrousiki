<?php
//-- クッキーデータのロード処理 --//
class CookieDataSet {
  public $scene;      //夜明け
  public $objection;  //「異議あり」の情報
  public $vote_times; //投票回数
  public $user_count; //参加人数

  function __construct(){
    $this->scene      = @$_COOKIE['scene'];
    $this->objection  = @$_COOKIE['objection'];
    $this->vote_times = @(int)$_COOKIE['vote_times'];
    $this->user_count = @(int)$_COOKIE['user_count'];
  }
}

//-- 外部リンク生成の基底クラス --//
class ExternalLinkBuilder {
  const TIME    = 5; //タイムアウト時間 (秒)
  const TIMEOUT = "%s: Connection timed out (%d seconds)\n";
  const GET     = "GET / HTTP/1.1\r\nHost: %s\r\nConnection: Close\r\n\r\n";

  //サーバ通信状態チェック
  static function CheckConnection($url){
    $url_stack  = explode('/', $url);
    $host = $url_stack[2];
    if (! ($io = @fsockopen($host, 80, $status, $str, self::TIME))) return false;

    stream_set_timeout($io, self::TIME);
    fwrite($io, sprintf(self::GET, $host));
    $data = fgets($io, 128);
    $stream_stack = stream_get_meta_data($io);
    fclose($io);
    return ! $stream_stack['timed_out'];
  }

  //HTML タグ生成
  static function Generate($title, $data){
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  //タイムアウトメッセージ生成
  static function GenerateTimeOut($url){
    $stack  = explode('/', $url);
    return sprintf(self::TIMEOUT, $stack[2], self::TIME);
  }

  //外部村リンク生成
  function GenerateSharedServerRoom($name, $url, $data){
    $format = 'ゲーム一覧 (<a href="%s">%s</a>)';
    return $this->Generate(sprintf($format, $url, $name), $data);
  }
}

//-- Twitter 投稿用の基底クラス --//
class TwitterConfigBase {
  //メッセージのセット
  function GenerateMessage($id, $name, $comment){ return true; }

  //投稿処理
  function Send($id, $name, $comment){
    if ($this->disable) return;

    $message = $this->GenerateMessage($id, $name, $comment);
    if (ServerConfig::$encode != 'UTF-8') { //Twitter は UTF-8
      $message = mb_convert_encoding($message, 'UTF-8', ServerConfig::$encode);
    }
    if (mb_strlen($message) > 140) $message = mb_substr($message, 0, 139);

    if ($this->add_url) {
      $url = ServerConfig::$site_root;
      if ($this->direct_url) $url .= 'login.php?room_no=' . $id;
      if ($this->short_url) {
	$short_url = @file_get_contents('http://tinyurl.com/api-create.php?url=' . $url);
	if ($short_url != '') $url = $short_url;
      }
      if (mb_strlen($message . $url) + 1 < 140) $message .= ' ' . $url;
    }
    if (strlen($this->hash) > 0 && mb_strlen($message . $this->hash) + 2 < 140) {
      $message .= " #{$this->hash}";
    }

    //投稿
    $to  = new TwitterOAuth($this->key_ck, $this->key_cs, $this->key_at, $this->key_as);
    $url = 'https://twitter.com/statuses/update.json';
    $response = $to->OAuthRequest($url, 'POST', array('status' => $message));

    if (! ($response === false || (strrpos($response, 'error')))) return true;
    //エラー処理
    $sentence = 'Twitter への投稿に失敗しました。<br>'."\n" .
      'ユーザ名：' . $this->user . '<br>'."\n" . 'メッセージ：' . $message;
    PrintData($sentence);
    return false;
  }
}

//-- 「福引」クラス --//
class Lottery {
  //「福引き」を一定回数行ってリストに追加する
  static function AddRandom(&$list, $random_list, $count){
    $total = count($random_list) - 1;
    for (; $count > 0; $count--) {
      $role = $random_list[mt_rand(0, $total)];
      isset($list[$role]) ? $list[$role]++ : $list[$role] = 1;
    }
  }

  //「比」の配列から一つ引く
  static function Get($list){ return GetRandom(self::GenerateRandomList($list)); }

  //「比」の配列から「福引き」を作成する
  static function GenerateRandomList($list){
    $stack = array();
    foreach ($list as $role => $rate) {
      for (; $rate > 0; $rate--) $stack[] = $role;
    }
    return $stack;
  }

  //「比」から「確率」に変換する (テスト用)
  static function RateToProbability($list){
    $stack = array();
    $total = array_sum($list);
    foreach ($list as $role => $rate) {
      $stack[$role] = sprintf('%01.2f', $rate / $total * 100);
    }
    PrintData($stack);
  }

  //闇鍋モードの配役リスト取得
  static function GetChaosRateList($list, $filter) {
    foreach ($filter as $role => $rate) { //出現率補正
      if (isset($list[$role])) $list[$role] = round($list[$role] * $rate);
    }
    return $list;
  }
}

//-- 配役基礎クラス --//
class Cast {
  //身代わり君の配役対象外役職リスト取得
  static function GetDummyBoyRoleList(){
    $stack = CastConfig::$disable_dummy_boy_role_list; //サーバ個別設定を取得
    array_push($stack, 'wolf', 'fox'); //常時対象外の役職を追加

    //探偵村対応
    $role = 'detective_common';
    if (DB::$ROOM->IsOption('detective') && ! in_array($role, $stack)) $stack[] = $role;
    return $stack;
  }

  //村人置換村の処理
  static function ReplaceRole(&$role_list){
    $stack = array();
    foreach (array_keys(DB::$ROOM->option_role->options) as $option) { //処理順にオプションを登録
      if ($option == 'replace_human' || strpos($option, 'full_') === 0) {
	$stack[0][] = $option;
      }
      elseif (strpos($option, 'change_') === 0) {
	$stack[1][] = $option;
      }
    }
    //PrintData($stack);

    foreach ($stack as $order => $option_list) {
      foreach ($option_list as $option) {
	if (array_key_exists($option, CastConfig::$replace_role_list)) { //管理者設定
	  $target = CastConfig::$replace_role_list[$option];
	  $role   = array_pop(explode('_', $option));
	}
	elseif ($order == 0) { //村人置換
	  $target = array_pop(explode('_', $option, 2));
	  $role   = 'human';
	}
	else { //共有者・狂人・キューピッド置換
	  $target = array_pop(explode('_', $option, 2));
	  $role   = $target == 'angel' ? 'cupid' : array_pop(explode('_', $target));
	}

	$count = isset($role_list[$role]) ? $role_list[$role] : 0;
	if ($role == 'human' && DB::$ROOM->IsOption('gerd')) $count--; //ゲルト君モード
	if ($count > 0) { //置換処理
	  isset($role_list[$target]) ? $role_list[$target] += $count : $role_list[$target] = $count;
	  $role_list[$role] -= $count;
	}
      }
    }
  }

  //決闘村の配役処理
  static function SetDuel($user_count){
    $role_list = array(); //初期化処理

    CastConfig::InitializeDuel($user_count);
    if (array_sum(CastConfig::$duel_fix_list) <= $user_count) {
      foreach (CastConfig::$duel_fix_list as $role => $count) $role_list[$role] = $count;
    }
    $rest_user_count = $user_count - array_sum($role_list);
    asort(CastConfig::$duel_rate_list);
    $total_rate = array_sum(CastConfig::$duel_rate_list);
    $max_rate_role = array_pop(array_keys(CastConfig::$duel_rate_list));
    foreach (CastConfig::$duel_rate_list as $role => $rate) {
      if ($role == $max_rate_role) continue;
      $role_list[$role] = round($rest_user_count / $total_rate * $rate);
    }
    $role_list[$max_rate_role] = $user_count - array_sum($role_list);

    CastConfig::FinalizeDuel($user_count, $role_list);
    return $role_list;
  }

  //クイズ村の配役処理
  static function SetQuiz($user_count){
    $stack = self::FilterRole($user_count, array('common', 'wolf', 'mad', 'fox'));
    $stack['human']--;
    $stack['quiz'] = 1;
    return $stack;
  }

  //グレラン村の配役処理
  static function SetGrayRandom($user_count){
    return self::FilterRole($user_count, array('wolf', 'mad', 'fox'));
  }

  //配役フィルタリング処理
  private function FilterRole($user_count, $filter) {
    $stack = array();
    foreach (CastConfig::$role_list[$user_count] as $key => $value) {
      $role = 'human';
      foreach ($filter as $set_role) {
	if (strpos($key, $set_role) !== false) {
	  $role = $set_role;
	  break;
	}
      }
      isset($stack[$role]) ? $stack[$role] += $value : $stack[$role] = $value;
    }
    return $stack;
  }
}
