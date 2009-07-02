<?php
 /**
  * filterKey2Trip
  *
  * 「name#key」を「name ◆trip」に変換するフィルタです
  * 同時に◆(\x81\x9f)を◇(\x81\x9e)に変換します
  * PHP versions >= 5.2.1
  *
  * Copyright (c) 2008 sanemat
  *
  * @param   string   $beforeFilter  変換前文字列
  * @param   string   $useEncoding   変換前文字列の文字エンコーディング　省略時はUTF-8
  *                                  UTF-8なら'UTF-8', EUC-JPなら'cp51932',
  *                                  Shift-JISなら'SJIS-win'
  * @param   string   $tripEncoding  トリップ変換時の文字エンコーディング　省略時はSJIS-win(通常SJIS-win)
  * @return  string   $afterFilter   変換後文字列
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  * @link    http://sane.justblog.jp/blog/2008/01/2chfilterkey2tr.html
  *
  *
  * mb_str_replace
  * マルチバイト対応 str_replace
  * @version     Release 2
  * @author      HiNa (hina@bouhime.com)
  * @copyright   Copyright (C) 2006-2007 by HiNa(hina@bouhime.com).
  * @link        http://fetus.k-hsu.net/document/programming/php/mb_str_replace.html
  *
  * 参考
  * ◆ 全サーバトリップ統一作戦
  * http://qb3.2ch.net/test/read.cgi/operate/1067245837/
  * XOOPS Cube日本サイト - BluesBBにトリップ機能を！
  * http://xoopscube.jp/modules/xhnewbb/viewtopic.php?topic_id=246&forum=2
  **/

function filterKey2Trip($beforeFilter, $useEncoding = 'UTF-8', $tripEncoding = 'SJIS-win'){
  $afterFilter = '';
  mb_convert_variables($tripEncoding, $useEncoding, &$beforeFilter);
  $beforeFilter = mb_str_replace("\x81\x9f", "\x81\x9e", $beforeFilter, $tripEncoding);

  if(preg_match("/([^#]*)#(.+)/", $beforeFilter, $match)){
    $salt = substr($match[2]."H.", 1, 2);
    $salt = preg_replace("/[^\.-z]/", ".", $salt);
    $salt = strtr($salt,"\x3A-\x40\x5B-\x60\x00-\x2D\x7B-\xFF","A-Ga-f.");
    $trip = substr(crypt($match[2], $salt),-10);
    $afterFilter = $match[1]."\x20\x81\x9f".$trip;
  }
  else{
    $afterFilter = $beforeFilter;
  }

  mb_convert_variables($useEncoding, $tripEncoding, &$afterFilter);
  return $afterFilter;
}

/**
 * マルチバイト対応 str_replace
 *
 * @version     Release 2
 * @author      HiNa (hina@bouhime.com)
 * @copyright   Copyright (C) 2006-2007 by HiNa(hina@bouhime.com).
 * @url         http://fetus.k-hsu.net/document/programming/php/mb_str_replace.html
 */

if(! function_exists('mb_str_replace')){
  /**
   * マルチバイト対応 str_replace
   *
   * @param   mixed   $search     検索文字列
   * @param   mixed   $replace    置換文字列
   * @param   mixed   $subject    対象文字列
   * @param   string  $encoding   文字列のエンコーディング(省略: 内部エンコーディング)
   *
   * @return  mixed   subject 内の search を replace で置き換えた文字列
   *
   * @note    この関数は配列に対応(search, replace, subject)しています。
   */
  function mb_str_replace($search, $replace, $subject, $encoding = 'auto') {
    if(! is_array($search)) {
      $search = array($search);
    }
    if(! is_array($replace)) {
      $replace = array($replace);
    }
    if(strtolower($encoding) === 'auto') {
      $encoding = mb_internal_encoding();
    }
    if(is_array($subject)) {
      $result = array();
      foreach($subject as $key => $val) {
	$result[$key] = mb_str_replace($search, $replace, $val, $encoding);
      }
      return $result;
    }

    $currentpos = 0;
    while(true) {
      $index = -1;
      $minpos = -1;
      foreach($search as $key => $find) {
	if($find == '') {
	  continue;
	}
	$findpos = mb_strpos($subject, $find, $currentpos, $encoding);
	if($findpos !== false) {
	  if($minpos < 0 || $findpos < $minpos) {
	    $minpos = $findpos;
	    $index = $key;
	  }
	}
      }
      if($minpos < 0) {
	break;
      }

      $r = array_key_exists($index, $replace) ? $replace[$index] : '';
      $subject = sprintf('%s%s%s',
			 mb_substr($subject, 0, $minpos, $encoding),
			 $r,
			 mb_substr(
				   $subject,
				   $minpos + mb_strlen($search[$index], $encoding),
				   mb_strlen($subject, $encoding),
				   $encoding
				   )
			 );
      $currentpos = $minpos + mb_strlen($r, $encoding);
    }
    return $subject;
  }
}

function ooo(){
  // 参考URL　2ちゃんねる準拠トリップにての全角文字 - PHPプロ！Q&A掲示板 <http://www.phppro.jp/qa/1166>
  $uname = "乗月#テスト";
  $uname = str_replace('◆','◇',$uname);
  print_r("uname　".$uname);
  $trip_start = strpos($uname, "#");
  print_r("<br />trip_start　".$trip_start);
  $name = substr($uname, 0, $trip_start);
  $trip_moji = substr($uname, $trip_start+1);
  print_r("<br />name　".$name);
  print_r("<br />trip_moji　".$trip_moji);
  $trip_moji = mb_convert_encoding($trip_moji, "SJIS", "utf-8");
  //"utf-8"部分はファイルの文字コードに合わせて変更します。EUC-JPなら"euc-jp"
  if( $trip_start !== false){
  $tripkey = htmlspecialchars($trip_moji,ENT_QUOTES);
  $salt = htmlspecialchars($trip_moji ,ENT_QUOTES);
  $salt = substr($tripkey.'H.',1,2);
  // $salt =~ s/[^\.-z]/\./go;にあたる箇所
  $pattern = '/[\x00-\x20\x7B-\xFF]/';
  $salt = preg_replace($pattern,".",$salt);

  $patterns = ":;<=>?@[\\]^_`";
  $mach = "ABCDEFGabcdef";
  for($i = 0; $i <= 13 - 1; $i++){
  $salt = str_replace($patterns[$i], $mach[$i], $salt);
  }

  $trip = crypt($tripkey,$salt);

  $trip = substr($trip,-10);
  $trip = '◆'.$trip;
  print_r("<br />結果　".$name.$trip);
  $uname = $name.$trip;
  }

  print_r("<br />uname　".$uname);
}
?>
