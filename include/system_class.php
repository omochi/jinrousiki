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
  //配列からランダムに一つ取り出す
  static function Get(array $array) {
    return count($array) > 0 ? $array[array_rand($array)] : null;
  }

  //闇鍋モードの配役リスト取得
  static function GetChaos(array $list, array $filter) {
    foreach ($filter as $role => $rate) { //出現率補正
      if (isset($list[$role])) $list[$role] = round($list[$role] * $rate);
    }
    return $list;
  }

  //「比」の配列から一つ引く
  static function Draw(array $list) { return self::Get(self::Generate($list)); }

  //「比」の配列から「福引き」を作成する
  static function Generate(array $list) {
    $stack = array();
    foreach ($list as $role => $rate) {
      for (; $rate > 0; $rate--) $stack[] = $role;
    }
    return $stack;
  }

  //「福引き」を一定回数行ってリストに追加する
  static function Add(array &$list, array $random_list, $count) {
    $total = count($random_list) - 1;
    for (; $count > 0; $count--) {
      $role = $random_list[mt_rand(0, $total)];
      isset($list[$role]) ? $list[$role]++ : $list[$role] = 1;
    }
  }

  //「比」から「確率」に変換する (テスト用)
  static function ToProbability(array $list) {
    $stack = array();
    $total = array_sum($list);
    foreach ($list as $role => $rate) {
      $stack[$role] = sprintf('%01.2f', $rate / $total * 100);
    }
    Text::p($stack);
  }
}

//-- 管理用クラス --//
class JinroAdmin {
  //村削除
  static function DeleteRoom() {
    if (! ServerConfig::DEBUG_MODE) {
      HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
    }
    extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
    $room_no = intval($unsafe_room_no);
    $title   = '部屋削除[エラー]';
    if ($room_no < 1) HTML::OutputResult($title, '無効な村番号です。');

    DB::Connect();
    if (DB::Lock('room') && DB::DeleteRoom($room_no)) {
      DB::Optimize();
      $str = $room_no . ' 番地を削除しました。トップページに戻ります。';
      HTML::OutputResult('部屋削除', $str, '../');
    }
    else {
      HTML::OutputResult($title, $room_no . ' 番地の削除に失敗しました。');
    }
  }

  //アイコン削除
  static function DeleteIcon() {
    if (! ServerConfig::DEBUG_MODE) {
      HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
    }
    extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
    $icon_no = intval($unsafe_icon_no);
    $title   = 'アイコン削除[エラー]';
    if ($icon_no < 1) HTML::OutputResult($title, '無効なアイコン番号です。');

    Loader::LoadFile('icon_functions');
    DB::Connect();

    $error = "サーバが混雑しています。<br>\n時間を置いてから再度アクセスしてください。";
    if (! DB::Lock('icon')) HTML::OutputResult($title, $error); //トランザクション開始
    if (IconDB::IsUsing($icon_no)) { //使用中判定
      HTML::OutputResult($title, '募集中・プレイ中の村で使用されているアイコンは削除できません。');
    }
    $file = IconDB::GetFile($icon_no);
    if ($file === false || is_null($file)) HTML::OutputResult($title, 'ファイルが存在しません');
    if (IconDB::Delete($icon_no, $file)) {
      $url = '../icon_upload.php';
      $str = '削除完了：登録ページに飛びます。<br>'."\n" .
	'切り替わらないなら <a href="' . $url . '">ここ</a> 。';
      HTML::OutputResult('アイコン削除完了', $str, $url);
    }
    else {
      HTML::OutputResult($title, $error);
    }
  }
}
