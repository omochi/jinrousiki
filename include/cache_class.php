<?php

/**
 * レンダリングされたページを指定した期間データベース上にキャッシュする機構を提供します。
 * 更新タイミングはInnoDBの行ロックに依存します。すべてのメソッドをトランザクションの内部で呼び出しください。
 */
class DocumentCache {
  private static $instance = null;

  public $name    = null;
  public $expire  = 0;
  public $updated = false; //更新済み判定

  //クラスの初期化
  private function __construct($name, $expire = 0) {
    $this->name   = $name;
    $this->expire = $expire;
    return self::$instance = $this;
  }

  //クラスのロード
  static function Load($name, $expire) {
    if (is_null(self::$instance)) new self($name, $expire);
  }

  //インスタンス取得
  static function Get() { return self::$instance; }

  //保存名取得
  static function GetName($serialize = false) {
    $name = ServerConfig::SITE_ROOT . self::Get()->name . DB::$ROOM->id;
    return $serialize ? serialize($name) : $name;
  }

  //会話情報取得
  static function GetTalk() {
    $data = DocumentCacheDB::Get(self::GetName(true));
    if (is_null($data) || Time::Get() > $data['expire']) return Talk::Get();

    self::Get()->updated = true;
    if (ServerConfig::DEBUG_MODE) {
      Text::p('Next Update', Time::GetDate('Y-m-d H:i:s', $data['expire']));
    }
    $filter = unserialize($data['content']);
    return $filter instanceOf TalkBuilder ? $filter : Talk::Get();
 }

  //保存処理
  static function Save($object) {
    if (self::Get()->updated) return;
    $name   = self::GetName(true);
    $exists = DocumentCacheDB::Exists($name);
    if ($exists) { //存在するならロックする
      DB::Transaction();
      $expire = DocumentCacheDB::Lock($name);
      if ($expire === false || $expire > Time::Get()) return DB::Rollback();
    }
    DocumentCacheDB::Save($name, serialize($object), self::Get()->expire);
    return $exists ? DB::Commit() : true;
  }
}

//-- DB アクセス (DocumentCache 拡張) --//
class DocumentCacheDB {
  //存在チェック
  static function Exists($name) {
    DB::Prepare('SELECT expire FROM document_cache WHERE name = ?', array($name));
    return DB::Count() > 0;
  }

  //取得
  static function Get($name) {
    DB::Prepare('SELECT content, expire FROM document_cache WHERE name = ?', array($name));
    return DB::FetchAssoc(true);
  }

  //排他更新用ロック
  static function Lock($name) {
    DB::Prepare('SELECT expire FROM document_cache WHERE name = ? FOR UPDATE', array($name));
    return DB::FetchResult();
  }

  //記録
  static function Save($name, $content, $expire) {
    $query = <<<EOF
INSERT INTO document_cache (name, content, expire) VALUES (?, ?, ?)
  ON DUPLICATE KEY UPDATE content = ?, expire = ?
EOF;
    $now    = Time::Get();
    $update = $now + $expire;
    if (ServerConfig::DEBUG_MODE) {
      Text::p('Updated', Time::GetDate('Y-m-d H:i:s', $now));
      Text::p('Next Update', Time::GetDate('Y-m-d H:i:s', $update));
    }
    DB::Prepare($query, array($name, $content, $update, $content, $update));
    DB::Execute();
  }

  //消去
  static function Clean($exceed) {
DB::d();
    DB::Prepare('DELETE FROM document_cache WHERE expire < ?', array(Time::Get() - $exceed));
    DB::Execute() && DB::Optimize('document_cache');
  }
}
