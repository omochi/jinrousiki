<?php
//-- データベース基底クラス --//
class DB extends DatabaseConfig {
  public  static $ROOM = null;
  public  static $USER = null;
  public  static $SELF = null;
  private static $instance    = null;
  private static $transaction = false;

  //データベース接続クラス生成
  /*
    $id     : DatabaseConfig->name_list から選択
    $header : HTML ヘッダ出力情報 [true: 出力済み / false: 未出力]
    $exit   : エラー処理 [true: exit を返す / false で終了]
  */
  private function __construct($id = null, $header = false, $exit = true){
    //error_reporting(E_ALL);
    //データベースサーバにアクセス
    if (! ($db_handle = mysql_connect(self::$host, self::$user, self::$password))) {
      return self::OutputConnectError($header, $exit, 'MySQL サーバ', self::$host);
    }

    //データベース名設定
    $name = isset($id) ? @self::$name_list[is_int($id) ? $id - 1 : $id] : null;
    if (is_null($name)) $name = self::$name;
    if (! mysql_select_db($name, $db_handle)) { //データベース接続
      return self::OutputConnectError($header, $exit, 'データベース', $name);
    }

    mysql_set_charset(self::$encode); //文字コード設定
    if (self::$encode == 'utf8') self::Execute('SET NAMES utf8');

    return self::$instance = $db_handle;
  }

  //データベース接続 (ヘッダ出力あり)
  static function Connect($id = null){
    if (is_null(self::$instance)) new self($id);
    return isset(self::$instance);
  }

  //データベース接続 (ヘッダ出力あり)
  static function ConnectInHeader(){
    if (is_null(self::$instance)) new self(null, true, false);
    return isset(self::$instance);
  }

  //データベース再接続
  static function ConnectSecond(){
    new self(null, true);
    return isset(self::$instance);
  }

  //データベース切断
  static function Disconnect(){
    if (empty(self::$instance)) return;
    if (self::$transaction) self::Rollback();
    mysql_close(self::$instance);
    self::$instance = null;
  }

  //トランザクション開始
  static function Transaction(){
    if (self::$transaction) return true; //トランザクション中ならスキップ
    return self::$transaction = self::FetchBool('START TRANSACTION', true);
  }

  //カウンタロック処理
  static function Lock($type){
    $query = sprintf("SELECT count FROM count_limit WHERE type = '%s' FOR UPDATE", $type);
    return self::Transaction() && self::FetchBool($query);
  }

  //ロールバック処理
  static function Rollback(){
    self::$transaction = false; //必要なら事前にフラグ判定を行う
    return self::FetchBool('ROLLBACK', true);
  }

  //コミット処理
  static function Commit(){
    self::$transaction = false;
    return self::FetchBool('COMMIT', true);
  }

  //SQL 実行
  static function Execute($query, $quiet = false){
    if (($sql = mysql_query($query)) !== false) return $sql;
    if ($quiet) return false;

    $error = sprintf('MYSQL_ERROR(%d):%s', mysql_errno(), mysql_error());
    $backtrace = debug_backtrace(); //バックトレースを取得

    //Execute() を call した関数と位置を取得して「SQLエラー」として返す
    $trace_stack = array_shift($backtrace);
    $stack = array($trace_stack['line'], $error, $query);
    $trace_stack = array_shift($backtrace);
    array_unshift($stack, $trace_stack['function'] . '()');
    PrintData(implode(': ', $stack), 'SQLエラー');

    foreach ($backtrace as $trace_stack) { //呼び出し元があるなら追加で出力
      $stack = array($trace_stack['function'] . '()', $trace_stack['line']);
      PrintData(implode(': ', $stack), 'Caller');
    }
    return false;
  }

  //コミット付き実行
  static function ExecuteCommit($query){
    return self::FetchBool($query) && self::Commit();
  }

  //実行結果を bool で受け取る
  static function FetchBool($query, $quiet = false){
    return self::Execute($query, $quiet) !== false;
  }

  //単体の値を取得
  static function FetchResult($query){
    if (($sql = self::Execute($query)) === false) return false;

    $data = mysql_num_rows($sql) > 0 ? mysql_result($sql, 0, 0) : false;
    mysql_free_result($sql);

    return $data;
  }

  //該当するデータの行数を取得
  static function Count($query){
    if (($sql = self::Execute($query)) === false) return 0;

    $data = mysql_num_rows($sql);
    mysql_free_result($sql);

    return $data;
  }

  //一次元の配列を取得
  static function FetchArray($query){
    $stack = array();
    if (($sql = self::Execute($query)) === false) return $stack;

    $count = mysql_num_rows($sql);
    for ($i = 0; $i < $count; $i++) $stack[] = mysql_result($sql, $i, 0);
    mysql_free_result($sql);

    return $stack;
  }

  //連想配列を取得
  static function FetchAssoc($query, $shift = false){
    $stack = array();
    if (($sql = self::Execute($query)) === false) return $stack;

    while(($array = mysql_fetch_assoc($sql)) !== false) $stack[] = $array;
    mysql_free_result($sql);

    return $shift ? array_shift($stack) : $stack;
  }

  //オブジェクト形式の配列を取得
  static function FetchObject($query, $class, $shift = false){
    $stack = array();
    if (($sql = self::Execute($query)) === false) return $stack;

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

    $crypt_password = Text::CryptPassword($password);
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
    return self::ExecuteCommit('OPTIMIZE TABLE ' . $query);
  }

  //データベース接続エラー出力 ($header, $exit は Connect() 参照)
  private function OutputConnectError($header, $exit, $title, $type){
    $title .= '接続失敗';
    $str = $title . ': ' . $type; //エラーメッセージ作成
    if ($header) {
      printf('<font color="#FF0000">%s</font><br>', $str);
      if ($exit) HTML::OutputFooter($exit);
      return false;
    }
    HTML::OutputResult($title, $str);
  }
}