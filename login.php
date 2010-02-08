<?php
require_once('include/init.php');

$INIT_CONF->LoadRequest('RequestLogin'); //引数を取得
$DB_CONF->Connect(); //DB 接続
session_start(); //セッション開始

//-- ログイン処理 --//
//DB 接続解除は OutputActionResult() が行う
if($RQ_ARGS->login_type == 'manually'){ //ユーザ名とパスワードで手動ログイン
  if(LoginManually()){
    OutputLoginResult('ログインしました', 'game_frame');
  }
  else{
    OutputLoginResult('ログイン失敗', NULL, 'ユーザ名とパスワードが一致しません。<br>' .
		      '(空白と改行コードは登録時に自動で削除されている事に注意してください)');
  }
}
elseif(CheckSession(session_id(), false)){ //セッションIDから自動ログイン
  OutputLoginResult('ログインしています', 'game_frame');
}
else{ //単に呼ばれただけなら観戦ページに移動させる
  OutputLoginResult('観戦ページにジャンプ', 'game_view', '観戦ページに移動します');
}

//-- 関数 --//
//結果出力関数
function OutputLoginResult($title, $jump, $body = NULL){
  global $RQ_ARGS;

  if(is_null($body)) $body = $title;
  if(is_null($jump)){
    $url = '';
  }
  else{
    $url = $jump . '.php?room_no=' . $RQ_ARGS->room_no;
    $body .= '。<br>' . "\n" . '切り替わらないなら <a href="' . $url . '" target="_top">ここ</a> 。';
  }
  OutputActionResult($title, $body, $url);
}

//ユーザ名とパスワードでログイン
//返り値：ログインできた true / できなかった false
function LoginManually(){
  global $RQ_ARGS;

  //セッションを失った場合、ユーザ名とパスワードでログインする
  $room_no  = $RQ_ARGS->room_no;
  $uname    = $RQ_ARGS->uname;
  $password = $RQ_ARGS->password;
  if($uname == '' || $password == '') return false;

  //共通クエリ
  $query = "WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0";

  // //IPアドレス取得
  // $ip_address = $_SERVER['REMOTE_ADDR']; //特に参照してないようだけど…？
  $crypt_password = CryptPassword($password);
  //$crypt_password = $password; //デバッグ用

  //該当するユーザ名とパスワードがあるか確認
  $query_password = "SELECT COUNT(uname) FROM user_entry $query AND password = '$crypt_password'";
  if(FetchResult($query_password) != 1) return false;

  //セッションIDの再登録
  $session_id = GetUniqSessionID();

  //DBのセッションIDを更新
  mysql_query("UPDATE user_entry SET session_id = '$session_id' $query");
  mysql_query('COMMIT'); //一応コミット
  return true;
}
