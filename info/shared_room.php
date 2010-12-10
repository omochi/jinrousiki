<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
OutputInfoPageHeader('関連サーバ村情報', 0, 'shared_room');
OutputSharedServerRoom();
OutputHTMLFooter();

//他のサーバの部屋画面を出力
function OutputSharedServerRoom(){
  global $SERVER_CONF;

  $SHARED_CONF =& new SharedServerConfig();
  if($SHARED_CONF->disable) return false;

  foreach($SHARED_CONF->server_list as $server => $array){
    extract($array);
    //PrintData($url, 'URL'); //テスト用
    if($disable) continue;

    if(! $SHARED_CONF->CheckConnection($url)){ //サーバ通信状態チェック
      $data = $SHARED_CONF->host . ": Connection timed out ({$SHARED_CONF->time} seconds)";
      echo $SHARED_CONF->GenerateSharedServerRoom($name, $url, $data);
      continue;
    }

    //部屋情報を取得
    if(($data = @file_get_contents($url.'room_manager.php')) == '') continue;
    //PrintData($data, 'Data'); //テスト用
    if($encode != '' && $encode != $SHARED_CONF->encode){
      $data = mb_convert_encoding($data, $SHARED_CONF->encode, $encode);
    }
    if($separator != ''){
      $split_list = mb_split($separator, $data);
      //PrintData($split_list, 'Split'); //テスト用
      $data = array_pop($split_list);
    }
    if($footer != ''){
      if(($position = mb_strrpos($data, $footer)) === false) continue;
      $data = mb_substr($data, 0, $position + mb_strlen($footer));
    }
    if($data == '') continue;

    $replace_list = array('href="' => 'href="' . $url, 'src="'  => 'src="' . $url);
    $data = strtr($data, $replace_list);
    echo $SHARED_CONF->GenerateSharedServerRoom($name, $url, $data);
  }
}