<?php
//-- データベースアクセス --//
class DB {
  const DSN = 'mysql:dbname=%s;host=%s';

  public  static $ROOM = null;
  public  static $USER = null;
  public  static $SELF = null;
  public  static $display     = false;
  private static $instance    = null;
  private static $statement   = null;
  private static $parameter   = null;
  private static $transaction = false;
  private static $table_list = array(
    'room', 'user_entry', 'player', 'talk', 'talk_beforegame', 'talk_aftergame', 'system_message',
    'result_ability', 'result_dead', 'result_lastwords', 'result_vote_kill', 'vote');

  //データベース接続クラス生成
  /*
    $id     : DatabaseConfig->name_list から選択
    $header : HTML ヘッダ出力情報 [true: 出力済み    / false: 未出力]
    $exit   : エラー処理          [true: exit を返す / false で終了]
  */
  private function __construct($id = null, $header = false, $exit = true) {
    //error_reporting(E_ALL);
    //データベース名設定
    $name = isset($id) ? @DatabaseConfig::$name_list[is_int($id) ? $id - 1 : $id] : null;
    if (is_null($name)) $name = DatabaseConfig::NAME;

    //コンストラクタ用パラメータセット
    $host = DatabaseConfig::HOST;
    $dsn  = sprintf(self::DSN, $name, $host);

    try {
      $pdo = new PDO($dsn, DatabaseConfig::USER, DatabaseConfig::PASSWORD);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
      self::$instance = $pdo;
      self::Execute(sprintf('SET NAMES %s', DatabaseConfig::ENCODE));
      return self::$instance;
    }
    catch (PDOException $e) {
      return self::Output($header, $exit, 'MySQL サーバ' . $e->getMessage(), $host);
    }
  }

  //データベース接続
  static function Connect($id = null) {
    if (is_null(self::$instance)) new self($id);
    return isset(self::$instance);
  }

  //データベース接続 (ヘッダ出力あり)
  static function ConnectInHeader() {
    if (is_null(self::$instance)) new self(null, true, false);
    return isset(self::$instance);
  }

  //データベース再接続
  static function Reconnect() {
    new self(null, true);
    return isset(self::$instance);
  }

  //データベース切断
  static function Disconnect() {
    if (empty(self::$instance)) return;
    if (self::$transaction) self::Rollback();
    self::$instance = null;
  }

  //トランザクション開始
  static function Transaction() {
    if (self::$transaction) return true; //トランザクション中ならスキップ
    return self::$transaction = self::FetchBool('START TRANSACTION', true);
  }

  //カウンタロック処理
  static function Lock($type) {
    $query = sprintf("SELECT count FROM count_limit WHERE type = '%s' FOR UPDATE", $type);
    return self::Transaction() && self::FetchBool($query);
  }

  //ロールバック処理
  static function Rollback() {
    self::$transaction = false; //必要なら事前にフラグ判定を行う
    return self::FetchBool('ROLLBACK', true);
  }

  //コミット処理
  static function Commit() {
    self::$transaction = false;
    return self::FetchBool('COMMIT', true);
  }

  //Prepare 処理
  static function Prepare($query, $list = array()) {
    self::$statement = self::$instance->prepare($query);
    self::$parameter = $list;
  }

  //SQL 実行
  static function Execute($query = null, $quiet = false) {
    try {
      if (isset($query)) {
	return self::$instance->query($query);
      }
      elseif (isset(self::$statement)) {
	self::$statement->execute(self::$parameter);
	if (self::$display) { //statement 表示 (デバッグ用)
	  Text::p(self::$statement);
	  Text::p(self::$parameter);
	}
	return self::$statement;
      } else {
	return false;
      }
    }
    catch (PDOException $e) {
      self::Reset();
      if ($quiet) return false;
      $error = sprintf('%d: %s', $e->getCode(), $e->getMessage());
    }
    $backtrace = debug_backtrace(); //バックトレースを取得

    //Execute() を call した関数と位置を取得して「SQLエラー」として返す
    $trace_stack = array_shift($backtrace);
    $stack       = array($trace_stack['line'], $error, $query);
    $trace_stack = array_shift($backtrace);
    array_unshift($stack, $trace_stack['function'] . '()');
    $str = sprintf("SQLエラー: %s<br>\n", implode(': ', $stack));

    foreach ($backtrace as $trace_stack) { //呼び出し元があるなら追加で出力
      $stack = array($trace_stack['function'] . '()', $trace_stack['line']);
      $str .= sprintf("Caller: %s<br>\n", implode(': ', $stack));
    }
    HTML::OutputResult(ServerConfig::TITLE . ' [エラー]', $str);
  }

  //コミット付き実行
  static function ExecuteCommit($query) {
    return self::FetchBool($query) && self::Commit();
  }

  //実行結果を bool で受け取る
  static function FetchBool($query = null, $quiet = false) {
    return self::Execute($query, $quiet) !== false;
  }

  //単体の値を取得
  static function FetchResult($query = null) {
    $stmt = self::Execute($query);
    self::Reset();
    return $stmt instanceOf PDOStatement && $stmt->rowCount() > 0 ? $stmt->fetchColumn() : false;
  }

  //該当するデータの行数を取得
  static function Count($query = null) {
    $stmt = self::Execute($query);
    self::Reset();
    return $stmt instanceOf PDOStatement ? $stmt->rowCount() : 0;
  }

  //一次元の配列を取得
  static function FetchArray($query = null) {
    $stmt = self::Execute($query);
    self::Reset();
    return $stmt instanceOf PDOStatement ? $stmt->fetchAll(PDO::FETCH_COLUMN) : array();
  }

  //連想配列を取得
  static function FetchAssoc($query = null, $shift = false) {
    $stmt = self::Execute($query);
    self::Reset();
    $stack = $stmt instanceOf PDOStatement ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
    return $shift ? array_shift($stack) : $stack;
  }

  //オブジェクト形式の配列を取得
  static function FetchObject($query, $class, $shift = false) {
    $stmt = self::Execute($query);
    self::Reset();
    $stack = $stmt instanceOf PDOStatement ? $stmt->fetchAll(PDO::FETCH_CLASS, $class) : array();
    return $shift ? array_shift($stack) : $stack;
  }

  //オブジェクト形式の配列を取得 (Prepare 経由用)
  static function FetchClass($class, $shift = false) {
    $stmt = self::Execute();
    self::Reset();
    $stack = $stmt instanceOf PDOStatement ? $stmt->fetchAll(PDO::FETCH_CLASS, $class) : array();
    return $shift ? array_shift($stack) : $stack;
  }

  //データベース登録
  static function Insert($table, $items, $values) {
    return self::FetchBool("INSERT INTO {$table}({$items}) VALUES({$values})");
  }

  //ユーザ登録処理
  static function InsertUser($room_no, $uname, $handle_name, $password, $user_no = 1, $icon_no = 0,
			     $profile = null, $sex = 'male', $role = null, $session_id = null) {
    $crypt_password = Text::Crypt($password);
    $items  = 'room_no, user_no, uname, handle_name, icon_no, sex, password, live';
    $values = "{$room_no}, {$user_no}, '{$uname}', '{$handle_name}', {$icon_no}, '{$sex}', " .
      "'{$crypt_password}', 'live'";

    if ($uname == 'dummy_boy') {
      $profile    = Message::$dummy_boy_comment;
      $last_words = Message::$dummy_boy_last_words;
    }
    else {
      $ip_address = Security::GetIP(); //ユーザのIPアドレスを取得
      $items  .= ', ip_address, last_load_scene';
      $values .= ", '{$ip_address}', 'beforegame'";
    }

    foreach (array('profile', 'role', 'session_id', 'last_words') as $value) {
      if (is_null($$value)) continue;
      $items  .= ", {$value}";
      $values .= ", '{$$value}'";
    }
    return self::Insert('user_entry', $items, $values);
  }

  //村削除
  static function DeleteRoom($room_no) {
    $query = 'DELETE FROM %s WHERE room_no = %d';
    foreach (self::$table_list as $table) {
      if (! self::FetchBool(sprintf($query, $table, $room_no))) return false;
    }
    return true;
  }

  //最適化
  static function Optimize($name = null) {
    $query = is_null($name) ? implode(',', self::$table_list) : $name;
    return self::ExecuteCommit('OPTIMIZE TABLE ' . $query);
  }

  //SQL リセット
  private static function Reset() {
    self::$statement = null;
    self::$parameter = null;
  }

  //データベース接続エラー出力 ($header, $exit は Connect() 参照)
  private function Output($header, $exit, $title, $type) {
    $title .= '接続失敗';
    $str = $title . ': ' . $type;
    if ($header) {
      printf('<font color="#FF0000">%s</font><br>', $str);
      if ($exit) HTML::OutputFooter($exit);
      return false;
    }
    HTML::OutputResult($title, $str);
  }
}