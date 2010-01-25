<?php
require_once('include/init.php');

$dbHandle = ConnectDatabase(); //DB 接続

//セッション開始
session_start();
$session_id = session_id();

$RQ_ARGS =& new RequestLogin(); //引数を取得

//変数をセット
$url     = 'game_frame.php?room_no=' . $RQ_ARGS->room_no;
$header  = '。<br>' . "\n" . '切り替わらないなら <a href="';
$footer  = '" target="_top">ここ</a> 。';
$anchor  = $header . $url . $footer;

//ログイン処理
//DB 接続解除は OutputActionResult() が行う
if($RQ_ARGS->login_type == 'manually'){ //ユーザ名とパスワードで手動ログイン
  if(LoginManually()){
    OutputActionResult('ログインしました', 'ログインしました' . $anchor, $url);
  }
  else{
    OutputActionResult('ログイン失敗', 'ユーザ名とパスワードが一致しません。<br>' .
		       '(空白と改行コードは登録時に自動で削除されている事に注意してください)');
  }
}
elseif(CheckSession($session_id, false)){ //セッションIDから自動ログイン
  OutputActionResult('ログインしています', 'ログインしています' . $anchor, $url);
}
else{ //単に呼ばれただけなら観戦ページに移動させる
  $url    = 'game_view.php?room_no=' . $RQ_ARGS->room_no;
  $anchor = $header . $url . $footer;
  OutputActionResult('観戦ページにジャンプ', '観戦ページに移動します' . $anchor, $url);
}

//-- 関数 --//
//ユーザ名とパスワードでログイン
//返り値：ログインできた true / できなかった false
function LoginManually(){
  global $RQ_ARGS;

  //セッションを失った場合、ユーザ名とパスワードでログインする
  $room_no  = $RQ_ARGS->room_no;
  $uname    = $RQ_ARGS->uname;
  $password = $RQ_ARGS->password;
  if($uname == '' || $password == '') return false;

  // //IPアドレス取得
  // $ip_address = $_SERVER['REMOTE_ADDR']; //特に参照してないようだけど…？
  $crypt_password = CryptPassword($password);
  // $crypt_password = $password; //デバッグ用

  //該当するユーザ名とパスワードがあるか確認
  $query = "SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no " .
    "AND uname = '$uname' AND password = '$crypt_password' AND user_no > 0";
  if(FetchResult($query) != 1) return false;

  //セッションIDの再登録
  $session_id = GetUniqSessionID();

  //DBのセッションIDを更新
  mysql_query("UPDATE user_entry SET session_id = '$session_id'
		WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
  mysql_query('COMMIT'); //一応コミット
  return true;
}
?>
