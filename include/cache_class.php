<?php
//キャッシュコントロールクラス
class DocumentCache {
  public  static $enable   = null; //有効設定
  private static $instance = null;

  public $room_no = 0;
  public $name    = null;
  public $expire  = 0;
  public $updated = false; //更新済み判定

  //クラスの初期化
  private function __construct($name, $expire = 0) {
    $this->room_no = isset(DB::$ROOM) ? DB::$ROOM->id : 0;
    $this->name    = $name;
    $this->expire  = $expire;
    return self::$instance = $this;
  }

  //クラスのロード
  static function Load($name, $expire) {
    if (is_null(self::$instance)) new self($name, $expire);
  }

  //有効判定
  static function Enable($type) {
    if (isset(self::$enable)) return self::$enable;
    switch ($type) {
    case 'talk_view':
      $enable = CacheConfig::ENABLE_TALK_VIEW;
      break;

    case 'old_log':
      $enable = CacheConfig::ENABLE_OLD_LOG;
      break;

    case 'old_log_list':
      $enable = CacheConfig::ENABLE_OLD_LOG_LIST;
      break;

    default:
      $enable = false;
      break;
    }
    self::$enable = CacheConfig::ENABLE && $enable;
    return self::$enable;
  }

  //インスタンス取得
  static function Get() { return self::$instance; }

  //保存名取得
  static function GetName($hash = false) {
    return $hash ? md5(self::Get()->name) : self::Get()->name;
  }

  //汎用検索情報取得
  static function GetKey() { return array(self::Get()->room_no, self::GetName(true)); }

  //保存情報取得
  static function GetData($serialize = false) {
    $data = DocumentCacheDB::Get();
    if (is_null($data) || Time::Get() > $data['expire']) return null;

    self::Get()->updated = true;
    if (CacheConfig::DEBUG_MODE) {
      Text::p('Next Update', Time::GetDate('Y-m-d H:i:s', $data['expire']));
    }
    $content = gzinflate($data['content']);
    return $serialize ? unserialize($content) : $content;
  }

  //会話情報取得
  static function GetTalk() {
    $filter = self::GetData(true);
    return (isset($filter) && $filter instanceOf TalkBuilder) ? $filter : Talk::Get();
  }

  //保存処理
  static function Save($object, $serialize = false) {
    if (self::Get()->updated) return;

    $content = $serialize ? gzdeflate(serialize($object)) : gzdeflate($object);
    if (DocumentCacheDB::Exists()) { //存在するならロックする
      DB::Transaction();
      $expire = DocumentCacheDB::Lock();
      if ($expire === false || $expire >= Time::Get()) return DB::Rollback();
      DocumentCacheDB::Update($content);
      return DB::Commit();
    }
    else {
      return DocumentCacheDB::Insert($content);
    }
  }
}

//-- DB アクセス (DocumentCache 拡張) --//
class DocumentCacheDB {
  //存在チェック
  static function Exists() {
    $query = 'SELECT expire FROM document_cache WHERE room_no = ? AND name = ?';
    DB::Prepare($query, DocumentCache::GetKey());
    return DB::Count() > 0;
  }

  //取得
  static function Get() {
    $query = 'SELECT content, expire FROM document_cache WHERE room_no = ? AND name = ?';
    DB::Prepare($query, DocumentCache::GetKey());
    return DB::FetchAssoc(true);
  }

  //排他更新用ロック
  static function Lock() {
    $query = 'SELECT expire FROM document_cache WHERE room_no = ? AND name = ? FOR UPDATE';
    DB::Prepare($query, DocumentCache::GetKey());
    return DB::FetchResult();
  }

  //新規作成
  static function Insert($content) {
    $query = <<<EOF
INSERT INTO document_cache (room_no, name, content, expire) VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE content = ?, expire = ?
EOF;
    $filter = DocumentCache::Get();
    $now    = Time::Get();
    $expire = $now + $filter->expire;
    if (CacheConfig::DEBUG_MODE) {
      Text::p('Insert',      Time::GetDate('Y-m-d H:i:s', $now));
      Text::p('Next Update', Time::GetDate('Y-m-d H:i:s', $expire));
    }
    $list = array($filter->room_no, $filter->GetName(true), $content, $expire, $content, $expire);
    DB::Prepare($query, $list);
    return DB::Execute();
  }

  //更新
  static function Update($content) {
    $query  = 'Update document_cache Set content = ?, expire = ? WHERE room_no = ? AND name = ?';
    $filter = DocumentCache::Get();
    $now    = Time::Get();
    $expire = $now + $filter->expire;
    if (CacheConfig::DEBUG_MODE) {
      Text::p('Updated',     Time::GetDate('Y-m-d H:i:s', $now));
      Text::p('Next Update', Time::GetDate('Y-m-d H:i:s', $expire));
    }
    DB::Prepare($query, array($content, $expire, $filter->room_no, $filter->GetName(true)));
    return DB::Execute();
  }

  //消去
  static function Clean() {
    $query = 'DELETE FROM document_cache WHERE expire < ?';
    DB::Prepare($query, array(Time::Get() - CacheConfig::EXCEED));
    return DB::Execute() && DB::Optimize('document_cache');
  }
}
