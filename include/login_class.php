<?php
//-- ログイン処理クラス --//
class Login {
  //基幹処理
  static function Execute(){
    global $SESSION;

    DB::Connect();
    if (RQ::$get->login_manually) { //ユーザ名とパスワードで手動ログイン
      if (self::LoginManually()) {
	self::Output('ログインしました', 'game_frame');
      }
      else {
	$str = 'ユーザ名とパスワードが一致しません。<br>' .
	  '(空白と改行コードは登録時に自動で削除されている事に注意してください)';
	self::Output('ログイン失敗', null, $str);
      }
    }

    if ($SESSION->Certify(false)) { //セッション ID から自動ログイン
      self::Output('ログインしています', 'game_frame');
    }
    else { //単に呼ばれただけなら観戦ページに移動させる
      self::Output('観戦ページにジャンプ', 'game_view', '観戦ページに移動します');
    }
  }

  //手動ログイン処理
  /*
    セッションを失った場合、ユーザ名とパスワードでログインする
    ログイン成功/失敗を true/false で返す
  */
  static function LoginManually(){
    global $SESSION;

    extract(RQ::ToArray()); //引数を展開
    if ($uname == '' || $password == '') return false;

    //$ip = $_SERVER['REMOTE_ADDR']; //IPアドレス取得 //現在は IP アドレス認証は行っていない
    $crypt = CryptPassword($password);
    //$crypt = $password; //デバッグ用

    //該当するユーザ名とパスワードがあるか確認
    $where = sprintf("WHERE room_no = %d AND uname = '%s' AND live <> 'kick'", $room_no, $uname);
    $query = sprintf("SELECT uname FROM user_entry %s AND password = '%s'", $where, $crypt);
    if (DB::Count($query) != 1) return false;

    //DB のセッション ID を再登録
    $session_id = $SESSION->Get(true); //新しいセッション ID を取得
    $query = sprintf("UPDATE user_entry SET session_id = '%s' %s", $session_id, $where);
    return DB::FetchBool($query);
  }

  //結果出力関数
  static function Output($title, $jump, $body = null){
    if (is_null($body)) $body = $title;
    if (is_null($jump)) {
      $url = '';
    }
    else {
      $url = sprintf('%s.php?room_no=%s', $jump, RQ::$get->room_no);
      $str = "。<br>\n".'切り替わらないなら <a href="%s" target="_top">ここ</a> 。';
      $body .= sprintf($str, $url);
    }
    OutputActionResult($title, $body, $url);
  }
}