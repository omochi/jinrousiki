<?php
//-- 音源処理クラス --//
class Sound extends SoundConfig {
  private static $class_id = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
  private static $codebase_url  = 'http://download.macromedia.com/pub/shockwave/cabs/flash/';
  private static $codebase_file = 'swflash.cab#version=4,0,0,0';
  private static $embed_url  = 'http://www.macromedia.com/shockwave/download/';
  private static $embed_file = 'index.cgi?P1_Prod_Version=ShockwaveFlash';

  //HTML 生成
  static function Generate($type, $file = null, $javascript = false){
    $path = self::GetPath($type, $file);
    $format = <<<EOF
<object classid="%s" codebase="%s%s" width="0" height="0">
<param name="movie" value="%s">
<param name="quality" value="high">
<embed src="%s" type="%s" quality="high" width="0" height="0" loop="false" pluginspage="%s%s">
</embed></object>%s
EOF;
    return sprintf($format,
		   self::$class_id, self::$codebase_url, self::$codebase_file,
		   $path, $path, 'application/x-shockwave-flash',
		   self::$embed_url, self::$embed_file, "\n");
  }

  //HTML 生成 (JavaScript 用)
  static function GenerateJavaScript($type){
    $path = self::GetPath($type);
    $format = "<object classid='%s' codebase='%s%s' width='0' height='0'>" .
      "<param name='movie' value='%s'><param name='quality' value='high'>" .
      "<embed src='%s' type='%s' quality='high' width='0' height='0' loop='false'" .
      " pluginspage='%s%s'></embed></object>";
    return sprintf($format,
		   self::$class_id, self::$codebase_url, self::$codebase_file,
		   $path, $path, 'application/x-shockwave-flash',
		   self::$embed_url, self::$embed_file);
  }

  //出力
  static function Output($type){ echo self::Generate($type); }

  //ファイルパス生成
  private function GetPath($type, $file = null){
    $path = JINRO_ROOT . '/' . self::$path;
    return $path . '/' . (is_null($file) ? self::$$type : $file) . '.' . self::$extension;
  }
}
