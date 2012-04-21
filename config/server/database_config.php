<?php
//-- データベース設定 --//
class DatabaseConfig {
  //データベースサーバのホスト名 hostname:port
  //ポート番号を省略するとデフォルトポートがセットされます。(MySQL:3306)
  static $host = 'localhost';

  //データベースのユーザ名
  #static $user = 'xxxx';
  static $user = 'grayran';

  //データベースサーバのパスワード
  #static $password = 'xxxxxxxx';
  static $password = 'satorituri';

  //データベース名
  static $name = 'jinrou';

  //サブデータベースのリスト (サーバによってはサブのデータベースを作れないので注意)
  /*
    過去ログ表示専用です。old_log.php の引数に db_no=[数字] を追加すると
    設定したサブのデータベースに切り替えることができます。
    例) $name_list = array('log_a', 'log_b');
        old_log.php?db_no=2 => log_b のデータベースのログを表示
  */
  static $name_list = array();

  //文字コード
  static $encode = 'utf8';
}
