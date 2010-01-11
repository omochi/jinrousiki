<?php
require_once(dirname(__FILE__) . '/../include/init.php');

$CSS_PATH = '../css'; //CSS �Υѥ�
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment . ' [�������]'); //HTML�إå�

if(! ($dbHandle = ConnectDatabase(true, false))){ //DB ��³
  mysql_query("CREATE DATABASE $db_name DEFAULT CHARSET ujis");
  echo "�ǡ����١��� $db_name ��������ޤ�����<br>";
  $dbHandle = ConnectDatabase(true); //����� DB ��³
}
echo '</head><body>'."\n";

CheckTable(); //�ơ��֥����
OutputHTMLFooter(); //HTML�եå�
DisconnectDatabase($dbHandle); //DB ��³���

//-- ���饹��� --//
//�桼����������ν������
//�������󥤥᡼����PHP���ֻ����ɲä�����Ϥ�����ɬ���ɲä��Ƥ���������
class DefaultIcon{
  //�桼����������ǥ��쥯�ȥꡧsetup.php ��������Хѥ�
  //�ºݤ˱��Ѥ���ݤ� TOP ��������Хѥ� (IconConfig->path) �򻲾Ȥ����������
  var $path   = '../user_icon';  //��������̾�Υꥹ��

  var $name = array('����', '�ų�', '����', '�����', '��', '�忧', '��', '��', '��', '�����鿧');

  //��������ο� (��������Υե�����̾��ɬ��001���ο����ˤ��Ƥ�������), ��, �⤵
  var $color = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
		     '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');
  var $width  = array(32, 32, 32, 32, 32, 32, 32, 32, 32, 32);
  var $height = array(32, 32, 32, 32, 32, 32, 32, 32, 32, 32);
}

//�����귯��������
class DummyBoyIcon{
  var $path   = '../img/dummy_boy_user_icon.jpg'; //IconConfig->path ��������Хѥ�
  var $name   = '�����귯��'; //̾��
  var $color  = '#000000'; //��
  var $width  = 45; //��
  var $height = 45; //�⤵
}

//-- �ؿ� --//
//ɬ�פʥơ��֥뤬���뤫��ǧ����
function CheckTable(){
  global $SERVER_CONF, $DB_CONF, $ICON_CONF;

  //�ơ��֥�Υꥹ�Ȥ�����˼���
  $sql   = mysql_list_tables($DB_CONF->name);
  $count = mysql_num_rows($sql);
  $table = array();
  for($i = 0; $i < $count; $i++) array_push($table, mysql_tablename($sql, $i));

  //�����å����ƥơ��֥뤬¸�ߤ��ʤ���к�������
  if(! in_array('room', $table)){
    mysql_query("CREATE TABLE room(room_no INT PRIMARY KEY, room_name TEXT, room_comment TEXT,
		establisher_ip TEXT, max_user INT, game_option TEXT, option_role TEXT, status TEXT,
		date INT, day_night TEXT,last_updated TEXT,victory_role TEXT)");
    echo '�ơ��֥�(room)��������ޤ���<br>'."\n";
  }
  else{
    //�ɲåե�����ɽ���
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
	echo '�ơ��֥�(room)�˥ե������(establisher_ip)���ɲä��ޤ���<br>'."\n";
      }
      else{
	echo '�ơ��֥�(room)�˥ե������(establisher_ip)���ɲäǤ��ޤ���Ǥ���<br>'."\n";
      }
    }
    if(! $flag->establish_time){
      if(mysql_query("ALTER TABLE room ADD establish_time DATETIME")){
	echo '�ơ��֥�(room)�˥ե������(establish_time)���ɲä��ޤ���<br>'."\n";
      }
      else{
	echo '�ơ��֥�(room)�˥ե������(establish_time)���ɲäǤ��ޤ���Ǥ���<br>'."\n";
      }
    }
    if(! $flag->start_time){
      if(mysql_query("ALTER TABLE room ADD start_time DATETIME")){
	echo '�ơ��֥�(room)�˥ե������(start_time)���ɲä��ޤ���<br>'."\n";
      }
      else{
	echo '�ơ��֥�(room)�˥ե������(start_time)���ɲäǤ��ޤ���Ǥ���<br>'."\n";
      }
    }
    if(! $flag->finish_time){
      if(mysql_query("ALTER TABLE room ADD finish_time DATETIME")){
	echo '�ơ��֥�(room)�˥ե������(finish_time)���ɲä��ޤ���<br>'."\n";
      }
      else{
	echo '�ơ��֥�(room)�˥ե������(finish_time)���ɲäǤ��ޤ���Ǥ���<br>'."\n";
      }
    }
  }

  if(! in_array('user_entry', $table)){
    mysql_query("CREATE TABLE user_entry(room_no INT NOT NULL, user_no INT, uname TEXT, handle_name TEXT,
		icon_no INT, profile TEXT, sex TEXT, password TEXT, role TEXT, live TEXT,
		session_id CHAR(32) UNIQUE, last_words TEXT, ip_address TEXT, last_load_day_night TEXT)");
    echo '�ơ��֥�(user_entry)��������ޤ���<br>'."\n";

    mysql_query("INSERT INTO user_entry(room_no, user_no, uname, handle_name, icon_no, profile,
		password, role, live) VALUES(0, 0, 'system', '�����ƥ�', 1, '������ޥ�����',
		'{$SERVER_CONF->system_password}', 'GM', 'live')");
  }
  mysql_query("ALTER TABLE user_entry ADD INDEX user_entry_index(room_no, user_no)");

  if(! in_array('talk', $table)){
    mysql_query("CREATE TABLE talk(room_no INT NOT NULL, date INT, location TEXT, uname TEXT,
		 time INT NOT NULL, sentence TEXT, font_type TEXT, spend_time INT)");
    echo '�ơ��֥�(talk)��������ޤ���<br>'."\n";
  }
  mysql_query("ALTER TABLE talk MODIFY time INT NOT NULL");
  mysql_query("ALTER TABLE talk ADD INDEX talk_index (room_no, date, time)");

  if(! in_array('vote', $table)){
    mysql_query("CREATE TABLE vote(room_no INT NOT NULL, date INT, uname TEXT, target_uname TEXT,
		 vote_number INT, vote_times INT, situation TEXT)");
    echo '�ơ��֥�(vote)��������ޤ���<br>'."\n";
  }
  mysql_query("ALTER TABLE vote ADD INDEX vote_index(room_no, date)");

  if(! in_array('system_message', $table)){
    mysql_query("CREATE TABLE system_message(room_no INT NOT NULL, message TEXT, type TEXT, date INT)");
    echo '�ơ��֥�(system_message)��������ޤ���<br>'."\n";
  }
  mysql_query("ALTER TABLE system_message ADD INDEX system_message_index(room_no, date)");

  if(! in_array('user_icon', $table)){
    mysql_query("CREATE TABLE user_icon(icon_no INT PRIMARY KEY, icon_name TEXT, icon_filename TEXT,
		icon_width INT, icon_height INT, color TEXT, session_id TEXT)");
    echo '�ơ��֥�(user_icon)��������ޤ���<br>'."\n";

    //�����귯�Υ����������Ͽ(��������No��0)
    $class = new DummyBoyIcon(); //�����귯�����������������
    mysql_query("INSERT INTO user_icon(icon_no, icon_name, icon_filename, icon_width,
		 icon_height,color)
		 VALUES(0, '{$class->name}', '{$class->path}', {$class->width},
		 {$class->height}, '{$class->color}')");

    //����Υ�������Υե�����̾�ȿ��ǡ����� DB ����Ͽ����
    $icon_no = 1;
    $class = new DefaultIcon(); //�桼����������ν����������

    //�ǥ��쥯�ȥ���Υե�������������
    if($handle = opendir($class->path)){
      while(($file = readdir($handle)) !== false){
	if($file != '.' && $file != '..'){
	  //����ǡ������ɤ߹���
	  $name   = $class->name[  $icon_no - 1];
	  $width  = $class->width[ $icon_no - 1];
	  $height = $class->height[$icon_no - 1];
	  $color  = $class->color[ $icon_no - 1];

	  mysql_query("INSERT INTO user_icon(icon_no, icon_name, icon_filename, icon_width,
			icon_height, color)
			VALUES($icon_no, '$name', '$file', $width, $height, '$color')");
	  $icon_no++;
	  echo "�桼����������($file $name $width �� $height $color)����Ͽ���ޤ���<br>"."\n";
	}
      }
      closedir($handle);
    }
  }

  if(! in_array('admin_manage', $table)){
    mysql_query("CREATE TABLE admin_manage(session_id TEXT)");
    mysql_query("INSERT INTO admin_manage VALUES('')");
    echo '�ơ��֥�(admin_manage)��������ޤ���<br>'."\n";
  }
  mysql_query("GRANT ALL ON {$db_name}.* TO $db_uname");
  mysql_query('COMMIT'); //������ߥå�
  echo '��������̵����λ���ޤ�����<br>'."\n";
}
?>
