<?php
//-- 外部リンク生成の基底クラス --//
class ExternalLinkBuilder {
  const TIME    = 5; //タイムアウト時間 (秒)
  const TIMEOUT = "%s: Connection timed out (%d seconds)\n";
  const GET     = "GET / HTTP/1.1\r\nHost: %s\r\nConnection: Close\r\n\r\n";

  //サーバ通信状態チェック
  static function CheckConnection($url) {
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
  static function Generate($title, $data) {
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  //タイムアウトメッセージ生成
  static function GenerateTimeOut($url) {
    $stack  = explode('/', $url);
    return sprintf(self::TIMEOUT, $stack[2], self::TIME);
  }

  //外部村リンク生成
  function GenerateSharedServerRoom($name, $url, $data) {
    $format = 'ゲーム一覧 (<a href="%s">%s</a>)';
    return self::Generate(sprintf($format, $url, $name), $data);
  }
}

//-- 「福引」クラス --//
class Lottery {
  //「福引き」を一定回数行ってリストに追加する
  static function AddRandom(array &$list, array $random_list, $count) {
    $total = count($random_list) - 1;
    for (; $count > 0; $count--) {
      $role = $random_list[mt_rand(0, $total)];
      isset($list[$role]) ? $list[$role]++ : $list[$role] = 1;
    }
  }

  //「比」の配列から一つ引く
  static function Get(array $list) { return GetRandom(self::GenerateRandomList($list)); }

  //「比」の配列から「福引き」を作成する
  static function GenerateRandomList(array $list) {
    $stack = array();
    foreach ($list as $role => $rate) {
      for (; $rate > 0; $rate--) $stack[] = $role;
    }
    return $stack;
  }

  //「比」から「確率」に変換する (テスト用)
  static function RateToProbability(array $list) {
    $stack = array();
    $total = array_sum($list);
    foreach ($list as $role => $rate) {
      $stack[$role] = sprintf('%01.2f', $rate / $total * 100);
    }
    PrintData($stack);
  }

  //闇鍋モードの配役リスト取得
  static function GetChaosRateList(array $list, array $filter) {
    foreach ($filter as $role => $rate) { //出現率補正
      if (isset($list[$role])) $list[$role] = round($list[$role] * $rate);
    }
    return $list;
  }
}
