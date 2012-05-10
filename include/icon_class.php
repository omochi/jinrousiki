<?php
//-- アイコン基底クラス --//
class Icon extends IconConfig {
  private static $path      = null;
  private static $dead_path = null;
  private static $wolf_path = null;
  private static $tag       = null;

  //パス取得
  static function GetPath(){
    if (is_null(self::$path)) self::$path = sprintf('%s/%s/', JINRO_ROOT, self::PATH);
    return self::$path;
  }

  //ファイルパス取得
  static function GetFile($file){ return self::GetPath() . $file; }

  //死亡アイコン取得
  static function GetDead(){
    if (is_null(self::$dead_path)) self::$dead_path =  sprintf('%s/%s', JINRO_IMG, self::$dead);
    return self::$dead_path;
  }

  //人狼アイコン取得
  static function GetWolf(){
    if (is_null(self::$wolf_path)) self::$wolf_path = sprintf('%s/%s/', JINRO_IMG, self::$wolf);
    return self::$wolf_path;
  }

  //タグ取得
  static function GetTag(){
    if (is_null(self::$tag)) {
      self::$tag = sprintf('width="%d" height="%d"', self::WIDTH, self::HEIGHT);
    }
    return self::$tag;
  }
}
