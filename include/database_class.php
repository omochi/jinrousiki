<?php
//-- データベース基底クラス --//
class DB {
  //問い合わせ処理
  static function SendQuery($query, $commit = false){
    global $DB_CONF;

    if (($sql = mysql_query($query)) !== false) return $commit ? $DB_CONF->Commit() : $sql;
    $error = sprintf('MYSQL_ERROR(%d):%s', mysql_errno(), mysql_error());
    $backtrace = debug_backtrace(); //バックトレースを取得

    //SendQuery() を call した関数と位置を取得して「SQLエラー」として返す
    $trace_stack = array_shift($backtrace);
    $stack = array($trace_stack['line'], $error, $query);
    $trace_stack = array_shift($backtrace);
    array_unshift($stack, $trace_stack['function'] . '()');
    PrintData(implode(': ', $stack), 'SQLエラー');

    foreach ($backtrace as $trace_stack){ //呼び出し元があるなら追加で出力
      $stack = array($trace_stack['function'] . '()', $trace_stack['line']);
      PrintData(implode(': ', $stack), 'Caller');
    }
    return false;
  }

  //実行結果を bool で受け取る
  static function FetchBool($query, $commit = false){
    return self::SendQuery($query, $commit) !== false;
  }

  //単体の値を取得
  static function FetchResult($query){
    if (($sql = self::SendQuery($query)) === false) return false;

    $data = mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false;
    mysql_free_result($sql);

    return $data;
  }

  //該当するデータの行数を取得
  static function FetchCount($query){
    if (($sql = self::SendQuery($query)) === false) return 0;

    $data = mysql_num_rows($sql);
    mysql_free_result($sql);

    return $data;
  }

  //一次元の配列を取得
  static function FetchArray($query){
    $stack = array();
    if (($sql = self::SendQuery($query)) === false) return $stack;

    $count = mysql_num_rows($sql);
    for ($i = 0; $i < $count; $i++) $stack[] = mysql_result($sql, $i, 0);
    mysql_free_result($sql);

    return $stack;
  }

  //連想配列を取得
  static function FetchAssoc($query, $shift = false){
    $stack = array();
    if (($sql = self::SendQuery($query)) === false) return $stack;

    while(($array = mysql_fetch_assoc($sql)) !== false) $stack[] = $array;
    mysql_free_result($sql);

    return $shift ? array_shift($stack) : $stack;
  }

  //オブジェクト形式の配列を取得
  static function FetchObject($query, $class, $shift = false){
    $stack = array();
    if (($sql = self::SendQuery($query)) === false) return $stack;

    while (($object = mysql_fetch_object($sql, $class)) !== false) $stack[] = $object;
    mysql_free_result($sql);

    return $shift ? array_shift($stack) : $stack;
  }

  //データベース登録
  static function Insert($table, $items, $values){
    return self::FetchBool("INSERT INTO {$table}({$items}) VALUES({$values})");
  }

  //ユーザ登録処理
  static function InsertUser($room_no, $uname, $handle_name, $password, $user_no = 1, $icon_no = 0,
			     $profile = null, $sex = 'male', $role = null, $session_id = null){
    global $MESSAGE;

    $crypt_password = CryptPassword($password);
    $items  = 'room_no, user_no, uname, handle_name, icon_no, sex, password, live';
    $values = "{$room_no}, {$user_no}, '{$uname}', '{$handle_name}', {$icon_no}, '{$sex}', " .
      "'{$crypt_password}', 'live'";

    if ($uname == 'dummy_boy'){
      $profile    = $MESSAGE->dummy_boy_comment;
      $last_words = $MESSAGE->dummy_boy_last_words;
    }
    else{
      $ip_address = $_SERVER['REMOTE_ADDR']; //ユーザのIPアドレスを取得
      $items  .= ', ip_address, last_load_scene';
      $values .= ", '{$ip_address}', 'beforegame'";
    }

    foreach (array('profile', 'role', 'session_id', 'last_words') as $var){
      if (is_null($$var)) continue;
      $items  .= ", {$var}";
      $values .= ", '{$$var}'";
    }
    return self::Insert('user_entry', $items, $values);
  }

  //村削除
  static function DeleteRoom($room_no){
    $header = 'DELETE FROM ';
    $footer = ' WHERE room_no = ' . $room_no;
    $stack  = array('room', 'user_entry', 'player', 'talk', 'talk_beforegame', 'talk_aftergame',
		    'system_message', 'result_ability', 'result_dead', 'result_lastwords',
		    'result_vote_kill', 'vote');
    foreach ($stack as $name){
      if (! self::FetchBool($header . $name . $footer)) return false;
    }
    return true;
  }

  //最適化
  static function Optimize($name = null){
    $tables = 'room, user_entry, talk, talk_beforegame, talk_aftergame, system_message' .
      'result_lastwords, vote';
    $query = is_null($name) ? $tables : $name;
    return self::FetchBool('OPTIMIZE TABLE ' . $query, true);
  }
}