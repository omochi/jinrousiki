<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO');

OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment . ' [初期設定]'); //HTMLヘッダ

if(! $DB_CONF->Connect(true, false)){ //DB 接続
  mysql_query("CREATE DATABASE {$DB_CONF->name} DEFAULT CHARSET utf8");
  echo "データベース {$DB_CONF->name} を作成しました。<br>";
  $DB_CONF->Connect(true); //改めて DB 接続
}
echo "</head><body>\n";

CheckTable(); //テーブル作成
OutputHTMLFooter(); //HTMLフッタ

//-- クラス定義 --//
//ユーザアイコンの初期設定
//アイコンイメージをPHP設置時に追加する場合はここも必ず追加してください。
class DefaultIcon{
  //ユーザアイコンディレクトリ：setup.php からの相対パス
  //実際に運用する際は TOP からの相対パス (IconConfig->path) を参照する点に注意
  var $path   = '../user_icon';  //アイコン名のリスト

  var $name = array('明灰', '暗灰', '黄色', 'オレンジ', '赤', '水色', '青', '緑', '紫', 'さくら色');

  //アイコンの色 (アイコンのファイル名は必ず001〜の数字にしてください), 幅, 高さ
  var $color = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
		     '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
  var $width  = array(32, 32, 32, 32, 32, 32, 32, 32, 32, 32);
  var $height = array(32, 32, 32, 32, 32, 32, 32, 32, 32, 32);
}

//身代わり君アイコン
class DummyBoyIcon{
  var $path   = '../img/dummy_boy_user_icon.jpg'; //IconConfig->path からの相対パス
  var $name   = '身代わり君用'; //名前
  var $color  = '#000000'; //色
  var $width  = 45; //幅
  var $height = 45; //高さ
}

//-- 関数 --//
//必要なテーブルがあるか確認する
function CheckTable(){
  global $SERVER_CONF, $DB_CONF, $SCRIPT_INFO;

  //前回のパッケージのリビジョン番号を取得
  $revision = $SERVER_CONF->last_updated_revision;
  if($revision >= $SCRIPT_INFO->revision){
    echo '初期設定はすでに完了しています。';
    return;
  }

  //テーブルのリストを配列に取得
  $sql   = mysql_list_tables($DB_CONF->name);
  $count = mysql_num_rows($sql);
  $table = array();
  for($i = 0; $i < $count; $i++) $table[] = mysql_tablename($sql, $i);

  //チェックしてテーブルが存在しなければ作成する
  $header = 'テーブル';
  $footer = '<br>'."\n";
  $str = 'を作成しました' . $footer;
  $success = ')を追加しました';
  $failed  = ')を追加できませんでした';

  $title = $header . '(room)';
  if(! in_array('room', $table)){
    mysql_query("CREATE TABLE room(room_no INT PRIMARY KEY, room_name TEXT, room_comment TEXT,
		max_user INT, game_option TEXT, option_role TEXT, status TEXT, date INT,
		day_night TEXT, last_updated TEXT, victory_role TEXT, establisher_ip TEXT,
		establish_time DATETIME, start_time DATETIME, finish_time DATETIME)");
    echo $title . $str;
  }
  elseif($revision > 0){
    //追加フィールド処理
    $sql = mysql_query('SHOW COLUMNS FROM room');
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	$flag->establisher_ip |= ($row['Field'] == 'establisher_ip');
	$flag->establish_time |= ($row['Field'] == 'establish_time');
	$flag->start_time     |= ($row['Field'] == 'start_time');
	$flag->finish_time    |= ($row['Field'] == 'finish_time');
      }

      $query = 'ALTER TABLE room ADD ';
      $title .= 'にフィールド(';

      if(! $flag->establisher_ip){
	$status = mysql_query($query . 'establisher_ip TEXT') ? $success : $failed;
	echo $title . 'establisher_ip' . $status . $footer;
      }
      if(! $flag->establish_time){
	$status = mysql_query($query . 'establish_time DATETIME') ? $success : $failed;
	echo $title . 'establish_time' . $status . $footer;
      }
      if(! $flag->start_time){
	$status = mysql_query($query . 'start_time DATETIME') ? $success : $failed;
	echo $title . 'start_time' . $status . $footer;
      }
      if(! $flag->finish_time){
	$status = mysql_query($query . 'finish_time DATETIME') ? $success : $failed;
	echo $title . 'finish_time' . $status . $footer;
      }
    }
  }

  $title = $header . '(user_entry)';
  if(! in_array('user_entry', $table)){
    mysql_query("CREATE TABLE user_entry(room_no INT NOT NULL, user_no INT, uname TEXT,
		handle_name TEXT, icon_no INT, profile TEXT, sex TEXT, password TEXT,
		role TEXT, live TEXT, session_id CHAR(32) UNIQUE, last_words TEXT, ip_address TEXT,
		last_load_day_night TEXT, INDEX user_entry_index(room_no, user_no))");
    echo $title . $str;

    mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name, icon_no, profile,
		password, role, live) VALUES(0, 0, 'system', 'システム', 1, 'ゲームマスター',
		'{$SERVER_CONF->system_password}', 'GM', 'live')");
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query('ALTER TABLE user_entry MODIFY room_no INT NOT NULL'); //room_no の型を変更
    echo $title . 'の room_no の型を "INT NOT NULL" に変更しました' . $footer;

    if($revision < 140){ //INDEX を設定
      mysql_query('ALTER TABLE user_entry ADD INDEX user_entry_index(room_no, user_no)');
      echo $title . 'に INDEX (room_no, user_no) を設定しました' . $footer;
    }
  }

  $title = $header . '(talk)';
  if(! in_array('talk', $table)){
    mysql_query("CREATE TABLE talk(talk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		room_no INT NOT NULL, date INT, location TEXT, uname TEXT,
		time INT NOT NULL, sentence TEXT, font_type TEXT, spend_time INT,
		INDEX talk_index(room_no, date))");
    echo $title . $str;
  }
  elseif($revision > 0){
    //追加フィールド処理
    $sql = mysql_query('SHOW COLUMNS FROM talk');
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	$flag->talk_id  |= ($row['Field'] == 'talk_id');
      }
    }

    $query = 'ALTER TABLE talk ADD ';
    $title .= 'にフィールド(';

    if(! $flag->talk_id){
      $status = (mysql_query($query . 'talk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY') ?
		 $success : $failed);
      echo $title . 'talk_id' . $status . $footer;
    }

    if($revision < 152){
      mysql_query('ALTER TABLE talk MODIFY room_no INT NOT NULL'); //room_no の型を変更
      echo $title . 'の room_no の型を "INT NOT NULL" に変更しました' . $footer;

      if($revision < 140){ //time の型を変更、INDEX を設定
	mysql_query('ALTER TABLE talk MODIFY time INT NOT NULL');
	echo $title . 'の time の型を "INT NOT NULL" に変更しました' . $footer;

	mysql_query('ALTER TABLE talk ADD INDEX talk_index(room_no, date, time)');
	echo $title . 'に INDEX (room_no, date, time) を設定しました' . $footer;
      }
    }
  }

  $title = $header . '(vote)';
  if(! in_array('vote', $table)){
    mysql_query("CREATE TABLE vote(room_no INT NOT NULL, date INT, uname TEXT, target_uname TEXT,
		vote_number INT, vote_times INT, situation TEXT, INDEX vote_index(room_no, date))");
    echo $title . $str;
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE vote MODIFY room_no INT NOT NULL"); //room_no の型を変更
    echo $title . 'の room_no の型を "INT NOT NULL" に変更しました' . $footer;

    if($revision < 140){ //INDEX を設定
      mysql_query("ALTER TABLE vote ADD INDEX vote_index(room_no, date)");
      echo $title . 'に INDEX (room_no, date) を設定しました' . $footer;
    }
  }

  $title = $header . '(system_message)';
  if(! in_array('system_message', $table)){
    mysql_query("CREATE TABLE system_message(room_no INT NOT NULL, message TEXT, type TEXT, date INT,
		INDEX system_message_index(room_no, date))");
    echo $title . $str;
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE system_message MODIFY room_no INT NOT NULL"); //room_no の型を変更
    echo $title . 'の room_no の型を "INT NOT NULL" に変更しました' . $footer;

    if($revision < 140){ //INDEX を設定
      mysql_query("ALTER TABLE system_message ADD INDEX system_message_index(room_no, date)");
      echo $title . 'に INDEX (room_no, date) を設定しました' . $footer;
    }
  }

  $title = $header . '(user_icon)';
  if(! in_array('user_icon', $table)){
    mysql_query("CREATE TABLE user_icon(icon_no INT PRIMARY KEY, icon_name TEXT, icon_filename TEXT,
		icon_width INT, icon_height INT, color TEXT, session_id TEXT, appearance TEXT,
		category TEXT, author TEXT, regist_date DATETIME)");
    echo $title . $str;

    //身代わり君のアイコンを登録(アイコンNo：0)
    $class = new DummyBoyIcon(); //身代わり君アイコンの設定をロード
    mysql_query("INSERT INTO user_icon(icon_no, icon_name, icon_filename, icon_width,
		 icon_height,color)
		 VALUES(0, '{$class->name}', '{$class->path}', {$class->width},
		 {$class->height}, '{$class->color}')");

    //初期のアイコンのファイル名と色データを DB に登録する
    $icon_no = 1;
    $class = new DefaultIcon(); //ユーザアイコンの初期設定をロード

    //ディレクトリ内のファイル一覧を取得
    if($handle = opendir($class->path)){
      while(($file = readdir($handle)) !== false){
	if($file != '.' && $file != '..'){
	  //初期データの読み込み
	  $name   = $class->name[  $icon_no - 1];
	  $width  = $class->width[ $icon_no - 1];
	  $height = $class->height[$icon_no - 1];
	  $color  = $class->color[ $icon_no - 1];

	  mysql_query("INSERT INTO user_icon(icon_no, icon_name, icon_filename, icon_width,
			icon_height, color)
			VALUES($icon_no, '$name', '$file', $width, $height, '$color')");
	  $icon_no++;
	  echo "ユーザアイコン($file $name $width × $height $color)を登録しました" . $footer;
	}
      }
      closedir($handle);
    }
  }
  elseif($revision > 0){ //追加フィールド処理
    $sql = mysql_query("SHOW COLUMNS FROM user_icon");
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	$flag->appearance  |= ($row['Field'] == 'appearance');
	$flag->category    |= ($row['Field'] == 'category');
	$flag->author      |= ($row['Field'] == 'author');
	$flag->regist_date |= ($row['Field'] == 'regist_date');
      }
    }

    $query = 'ALTER TABLE user_icon ADD ';
    $title .= 'にフィールド(';

    if(! $flag->appearance){
      $status = mysql_query($query . 'appearance TEXT') ? $success : $failed;
      echo $title . 'appearance' . $status . $footer;
    }
    if(! $flag->category){
      $status = mysql_query($query . 'category TEXT') ? $success : $failed;
      echo $title . 'category' . $status . $footer;
    }
    if(! $flag->author){
      $status = mysql_query($query . 'author TEXT') ? $success : $failed;
      echo $title . 'author' . $status . $footer;
    }
    if(! $flag->regist_date){
      $status = mysql_query($query . 'regist_date DATETIME') ? $success : $failed;
      echo $title . 'regist_date' . $status . $footer;
    }
  }

  $title = $header . '(admin_manage)';
  if(! in_array('admin_manage', $table)){
    mysql_query("CREATE TABLE admin_manage(session_id TEXT)");
    mysql_query("INSERT INTO admin_manage VALUES('')");
    echo $title . $str;
  }

  mysql_query("GRANT ALL ON {$db_name}.* TO $db_uname");
  mysql_query('COMMIT'); //一応コミット
  echo '初期設定は無事完了しました' . $footer;
}
