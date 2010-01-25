<?php
//-- スクリプト群の文字コード --//
/*
  変更する場合は全てのファイル自体の文字コードを自前で変更してください
  $ENCODE = 'EUC-JP';

  Ver. 1.4.0 α24 からは文字コードを設定する場所を以下の場所に変更しました
  include/init.php
*/

// エンコーディング指定 PHPバージョンによって指定方法が異なる //
$php_version_array = explode('.', phpversion());
if($php_version_array[0] <= 4 && $php_version_array[1] < 3){ //4.3.x未満
  //	encoding $SERVER_CONF->encode;  //エラーが出る？？
}
else{ //4.3.x以降
  declare(encoding='EUC-JP'); //変数を入れるとパースエラーが返るのでハードコード
}

// マルチバイト入出力指定 //
if(extension_loaded('mbstring')){
  mb_language('ja');
  mb_internal_encoding($SERVER_CONF->encode);
  mb_http_input ('auto');
  mb_http_output($SERVER_CONF->encode);
}

// 海外のサーバでも動くようにヘッダ強制指定   //
// 海外サーバ等で文字化けする場合に指定します //

//ヘッダがまだ何も送信されていない場合送信する
if(! headers_sent()){
  header("Content-type: text/html; charset={$SERVER_CONF->encode}");
  header('Content-Language: ja');
}
?>
