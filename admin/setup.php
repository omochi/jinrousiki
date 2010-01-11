<?php
require_once(dirname(__FILE__) . '/../include/init.php');

$CSS_PATH = '../css'; //CSS のパス
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment . ' [初期設定]'); //HTMLヘッダ

if(! ($dbHandle = ConnectDatabase(true, false))){ //DB 接続
  mysql_query("CREATE DATABASE $db_name DEFAULT CHARSET ujis");
  echo "データベース $db_name を作成しました。<br>";
  $dbHandle = ConnectDatabase(true); //改めて DB 接続
}
echo '</head><body>'."\n";

CheckTable(); //テーブル作成
OutputHTMLFooter(); //HTMLフッタ
DisconnectDatabase($dbHandle); //DB 接続解除

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
  global $SERVER_CONF, $DB_CONF, $ICON_CONF;

  //テーブルのリストを配列に取得
  $sql   = mysql_list_tables($DB_CONF->name);
  $count = mysql_num_rows($sql);
  $table = array();
  for($i = 0; $i < $count; $i++) array_push($table, mysql_tablename($sql, $i));

  //チェックしてテーブルが存在しなければ作成する
  if(! in_array('room', $table)){
    mysql_query("CREATE TABLE room(room_no INT PRIMARY KEY, room_name TEXT, room_comment TEXT,
		establisher_ip TEXT, max_user INT, game_option TEXT, option_role TEXT, status TEXT,
		date INT, day_night TEXT,last_updated TEXT,victory_role TEXT)");
    echo 'テーブル(room)を作成しました<br>'."\n";
  }
  else{
    //追加フィールド処理
    $sql = mysql_query("SHOW COLUMNS FROM room");
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	if($row['Field'] == 'establisher_ip') $flag->establisher_ip = true;
	if($row['Field'] == 'establish_time') $flag->establish_time = true;
	if($row['Field'] == 'start_time') $flag->start_time = true;
	if($row['Field'] == 'finish_time') $flag->finish_time = true;
      }
    }
    if(! $flag->establisher_ip){
      if(mysql_query("ALTER TABLE room ADD establisher_ip TEXT")){
	echo 'テーブル(room)にフィールド(establisher_ip)を追加しました<br>'."\n";
      }
      else{
	echo 'テーブル(room)にフィールド(establisher_ip)を追加できませんでした<br>'."\n";
      }
    }
    if(! $flag->establish_time){
      if(mysql_query("ALTER TABLE room ADD establish_time DATETIME")){
	echo 'テーブル(room)にフィールド(establish_time)を追加しました<br>'."\n";
      }
      else{
	echo 'テーブル(room)にフィールド(establish_time)を追加できませんでした<br>'."\n";
      }
    }
    if(! $flag->start_time){
      if(mysql_query("ALTER TABLE room ADD start_time DATETIME")){
	echo 'テーブル(room)にフィールド(start_time)を追加しました<br>'."\n";
      }
      else{
	echo 'テーブル(room)にフィールド(start_time)を追加できませんでした<br>'."\n";
      }
    }
    if(! $flag->finish_time){
      if(mysql_query("ALTER TABLE room ADD finish_time DATETIME")){
	echo 'テーブル(room)にフィールド(finish_time)を追加しました<br>'."\n";
      }
      else{
	echo 'テーブル(room)にフィールド(finish_time)を追加できませんでした<br>'."\n";
      }
    }
  }

  if(! in_array('user_entry', $table)){
    mysql_query("CREATE TABLE user_entry(room_no INT NOT NULL, user_no INT, uname TEXT, handle_name TEXT,
		icon_no INT, profile TEXT, sex TEXT, password TEXT, role TEXT, live TEXT,
		session_id CHAR(32) UNIQUE, last_words TEXT, ip_address TEXT, last_load_day_night TEXT)");
    echo 'テーブル(user_entry)を作成しました<br>'."\n";

    mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name, icon_no, profile,
		password, role, live) VALUES(0, 0, 'system', 'システム', 1, 'ゲームマスター',
		'{$SERVER_CONF->system_password}', 'GM', 'live')");
  }
  mysql_query("ALTER TABLE user_entry ADD INDEX user_entry_index(room_no, user_no)");

  if(! in_array('talk', $table)){
    mysql_query("CREATE TABLE talk(room_no INT NOT NULL, date INT, location TEXT, uname TEXT,
		 time INT NOT NULL, sentence TEXT, font_type TEXT, spend_time INT)");
    echo 'テーブル(talk)を作成しました<br>'."\n";
  }
  mysql_query("ALTER TABLE talk MODIFY time INT NOT NULL");
  mysql_query("ALTER TABLE talk ADD INDEX talk_index (room_no, date, time)");

  if(! in_array('vote', $table)){
    mysql_query("CREATE TABLE vote(room_no INT NOT NULL, date INT, uname TEXT, target_uname TEXT,
		 vote_number INT, vote_times INT, situation TEXT)");
    echo 'テーブル(vote)を作成しました<br>'."\n";
  }
  mysql_query("ALTER TABLE vote ADD INDEX vote_index(room_no, date)");

  if(! in_array('system_message', $table)){
    mysql_query("CREATE TABLE system_message(room_no INT NOT NULL, message TEXT, type TEXT, date INT)");
    echo 'テーブル(system_message)を作成しました<br>'."\n";
  }
  mysql_query("ALTER TABLE system_message ADD INDEX system_message_index(room_no, date)");

  if(! in_array('user_icon', $table)){
    mysql_query("CREATE TABLE user_icon(icon_no INT PRIMARY KEY, icon_name TEXT, icon_filename TEXT,
		icon_width INT, icon_height INT, color TEXT, session_id TEXT)");
    echo 'テーブル(user_icon)を作成しました<br>'."\n";

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
	  echo "ユーザアイコン($file $name $width × $height $color)を登録しました<br>"."\n";
	}
      }
      closedir($handle);
    }
  }

  if(! in_array('admin_manage', $table)){
    mysql_query("CREATE TABLE admin_manage(session_id TEXT)");
    mysql_query("INSERT INTO admin_manage VALUES('')");
    echo 'テーブル(admin_manage)を作成しました<br>'."\n";
  }
  mysql_query("GRANT ALL ON {$db_name}.* TO $db_uname");
  mysql_query('COMMIT'); //一応コミット
  echo '初期設定は無事完了しました。<br>'."\n";
}
?>
