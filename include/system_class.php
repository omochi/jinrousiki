<?php
//-- データベース処理の規定クラス --//
class DatabaseConfigBase{
  //データベース接続
  /*
    $header : HTMLヘッダ出力情報 [true: 出力済み / false: 未出力]
    $exit   : エラー処理 [true: exit を返す / false で終了]
  */
  function Connect($header = false, $exit = true){
    //データベースサーバにアクセス
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //アクセス成功
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //データベース接続
	//mysql_query("SET NAMES utf8");
	//成功したらハンドルを返して処理終了
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = 'データベース接続失敗';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQLサーバ接続失敗';
      $error_name  = $this->host;
    }

    $error_message = $error_title . ': ' . $error_name; //エラーメッセージ作成
    if($header){
      echo '<font color="#FF0000">' . $error_message . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($error_title, $error_message);
  }

  //データベースとの接続を閉じる
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) mysql_query('UNLOCK TABLES'); //ロック解除
    mysql_close($this->db_handle);
    unset($this->db_handle); //ハンドルをクリア
  }
}

//-- クッキーデータのロード処理 --//
class CookieDataSet{
  var $day_night;  //夜明け
  var $vote_times; //投票回数
  var $objection;  //「異議あり」の情報

  function CookieDataSet(){ $this->__construct(); }
  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- 画像管理の基底クラス --//
class ImageManager{
  function GenerateTag($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . JINRO_IMG . '/' . $this->path . '/' . $name . '.' . $this->extension . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }

  function Output($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }
}

//-- 勝利陣営の画像処理の基底クラス --//
class VictoryImageBase extends ImageManager{
  function GenerateTag($name){
    switch($name){
    case 'human':
      $alt = '村人勝利';
      break;

    case 'wolf':
      $alt = '人狼勝利';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '妖狐勝利';
      break;

    case 'lovers':
      $alt = '恋人勝利';
      break;

    case 'quiz':
      $alt = '出題者勝利';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '引き分け';
      break;

    default:
      return '-';
      break;
    }
    return parent::GenerateTag($name, $alt);
  }
}

//-- 音源処理の基底クラス --//
class SoundBase{
  //音を鳴らす
  function Output($type, $loop = false){
    $path = JINRO_ROOT . '/' . $this->path . '/' . $this->$type . '.' . $this->extension;
    if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

    echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$path}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$path}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
  }
}
