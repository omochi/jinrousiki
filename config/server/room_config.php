<?php
//-- 村メンテナンス・作成設定 --//
class RoomConfig {
  //-- 村メンテナンス設定 --//
  //村内の最後の発言から廃村になるまでの時間 (秒) (あまり短くすると沈黙等と競合する可能性あり)
  static $die_room = 1200;

  //最大並列プレイ可能村数
  static $max_active_room = 4;

  //次の村を立てられるまでの待ち時間 (秒)
  static $establish_wait = 120;

  //終了した村のユーザのセッション ID データをクリアするまでの時間 (秒)
  //(この時間内であれば、過去ログページに再入村のリンクが出現します)
  static $clear_session_id = 86400; //24時間

  //村立て・入村制限
  /* IP アドレスは strpos() による先頭一致、ホスト名は正規表現 */
  static $white_list_ip = array(); //IP アドレス (ホワイトリスト)
  static $black_list_ip = array(); //IP アドレス (ブラックリスト)
  static $white_list_host = null; //ホスト名 (ホワイトリスト)
  static $black_list_host = null; //ホスト名 (ブラックリスト)
  //static $black_list_host = '/localhost.localdomain/'; //入力例

  //-- 村作成設定 --//
  static $room_name          = 90; //村名の最大文字数 (byte)
  static $room_name_input    = 50; //村名の入力欄サイズ (文字数)
  static $room_comment       = 90; //村の説明の最大文字数 (byte)
  static $room_comment_input = 50; //村の説明の入力欄サイズ (文字数)
  static $gm_password        = 50; //GM ログインパスワードの最大文字数 (byte)
  static $gm_password_input  = 20; //GM ログインパスワードの入力欄サイズ
  static $ng_word = '/http:\/\//i'; //入力禁止文字列 (正規表現)

  //最大人数のリスト
  static $max_user_list = array(8, 11, 16, 22, 32, 50);
  static $default_max_user = 22; //デフォルトの最大人数 ($max_user_list にある値を入れること)
}
