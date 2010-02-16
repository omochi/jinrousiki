<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO');

OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment . ' [�������]'); //HTML�إå�

if(! $DB_CONF->Connect(true, false)){ //DB ��³
  mysql_query("CREATE DATABASE {$DB_CONF->name} DEFAULT CHARSET ujis");
  echo "�ǡ����١��� {$DB_CONF->name} ��������ޤ�����<br>";
  $DB_CONF->Connect(true); //����� DB ��³
}
echo "</head><body>\n";

CheckTable(); //�ơ��֥����
OutputHTMLFooter(); //HTML�եå�

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
  global $SERVER_CONF, $DB_CONF, $SCRIPT_INFO;

  //����Υѥå������Υ�ӥ�����ֹ�����
  $revision = $SERVER_CONF->last_updated_revision;
  if($revision >= $SCRIPT_INFO->revision){
    echo '�������Ϥ��Ǥ˴�λ���Ƥ��ޤ���';
    return;
  }

  //�ơ��֥�Υꥹ�Ȥ�����˼���
  $sql   = mysql_list_tables($DB_CONF->name);
  $count = mysql_num_rows($sql);
  $table = array();
  for($i = 0; $i < $count; $i++) $table[] = mysql_tablename($sql, $i);

  //�����å����ƥơ��֥뤬¸�ߤ��ʤ���к�������
  $header = '�ơ��֥�';
  $footer = '<br>'."\n";
  $str = '��������ޤ���' . $footer;

  $title = $header . '(room)';
  if(! in_array('room', $table)){
    mysql_query("CREATE TABLE room(room_no INT PRIMARY KEY, room_name TEXT, room_comment TEXT,
		max_user INT, game_option TEXT, option_role TEXT, status TEXT, date INT,
		day_night TEXT, last_updated TEXT, victory_role TEXT, establisher_ip TEXT,
		establisher_ip TEXT, establish_time DATETIME, start_time DATETIME,
		finish_time DATETIME)");
    echo $title . $str;
  }
  elseif($revision > 0){ //�ɲåե�����ɽ���
    $sql = mysql_query("SHOW COLUMNS FROM room");
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	$flag->establisher_ip |= ($row['Field'] == 'establisher_ip');
	$flag->establish_time |= ($row['Field'] == 'establish_time');
	$flag->start_time     |= ($row['Field'] == 'start_time');
	$flag->finish_time    |= ($row['Field'] == 'finish_time');
      }
    }

    $query   = "ALTER TABLE room ADD ";
    $titile .= '�˥ե������(';
    $success = ')���ɲä��ޤ���';
    $failed  = ')���ɲäǤ��ޤ���Ǥ���';

    if(! $flag->establisher_ip){
      $status = (mysql_query("$query establisher_ip TEXT") ? $success : $failed);
      echo $header . 'establisher_ip' . $status . $footer;
    }
    if(! $flag->establish_time){
      $status = (mysql_query("$query establish_time DATETIME") ? $success : $failed);
      echo $header . 'establish_time' . $status . $footer;
    }
    if(! $flag->start_time){
      $status = (mysql_query("$query start_time DATETIME") ? $success : $failed);
      echo $header . 'start_time' . $status . $footer;
    }
    if(! $flag->finish_time){
      $status = (mysql_query("$query finish_time DATETIME") ? $success : $failed);
      echo $header . 'finish_time' . $status . $footer;
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
		password, role, live) VALUES(0, 0, 'system', '�����ƥ�', 1, '������ޥ�����',
		'{$SERVER_CONF->system_password}', 'GM', 'live')");
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE user_entry MODIFY room_no INT NOT NULL"); //room_no �η����ѹ�
    echo $title . '�� room_no �η��� "INT NOT NULL" ���ѹ����ޤ���' . $footer;

    if($revision < 140){ //INDEX ������
      mysql_query("ALTER TABLE user_entry ADD INDEX user_entry_index(room_no, user_no)");
      echo $title . '�� INDEX (room_no, user_no) �����ꤷ�ޤ���' . $footer;
    }
  }

  $title = $header . '(talk)';
  if(! in_array('talk', $table)){
    mysql_query("CREATE TABLE talk(room_no INT NOT NULL, date INT, location TEXT, uname TEXT,
		time INT NOT NULL, sentence TEXT, font_type TEXT, spend_time INT,
		INDEX talk_index(room_no, date, time))");
    echo $title . $str;
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE talk MODIFY room_no INT NOT NULL"); //room_no �η����ѹ�
    echo $title . '�� room_no �η��� "INT NOT NULL" ���ѹ����ޤ���' . $footer;

    if($revision < 140){ //time �η����ѹ���INDEX ������
      mysql_query("ALTER TABLE talk MODIFY time INT NOT NULL");
      echo $title . '�� time �η��� "INT NOT NULL" ���ѹ����ޤ���' . $footer;

      mysql_query("ALTER TABLE talk ADD INDEX talk_index(room_no, date, time)");
      echo $title . '�� INDEX (room_no, date, time) �����ꤷ�ޤ���' . $footer;
    }
  }

  $title = $header . '(vote)';
  if(! in_array('vote', $table)){
    mysql_query("CREATE TABLE vote(room_no INT NOT NULL, date INT, uname TEXT, target_uname TEXT,
		vote_number INT, vote_times INT, situation TEXT, INDEX vote_index(room_no, date))");
    echo $title . $str;
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE vote MODIFY room_no INT NOT NULL"); //room_no �η����ѹ�
    echo $title . '�� room_no �η��� "INT NOT NULL" ���ѹ����ޤ���' . $footer;

    if($revision < 140){ //INDEX ������
      mysql_query("ALTER TABLE vote ADD INDEX vote_index(room_no, date)");
      echo $title . '�� INDEX (room_no, date) �����ꤷ�ޤ���' . $footer;
    }
  }

  $title = $header . '(system_message)';
  if(! in_array('system_message', $table)){
    mysql_query("CREATE TABLE system_message(room_no INT NOT NULL, message TEXT, type TEXT, date INT,
		INDEX system_message_index(room_no, date))");
    echo $title . $str;
  }
  elseif($revision > 0 && $revision < 152){
    mysql_query("ALTER TABLE system_message MODIFY room_no INT NOT NULL"); //room_no �η����ѹ�
    echo $title . '�� room_no �η��� "INT NOT NULL" ���ѹ����ޤ���' . $footer;

    if($revision < 140){ //INDEX ������
      mysql_query("ALTER TABLE system_message ADD INDEX system_message_index(room_no, date)");
      echo $title . '�� INDEX (room_no, date) �����ꤷ�ޤ���' . $footer;
    }
  }

  $title = $header . '(user_icon)';
  if(! in_array('user_icon', $table)){
    mysql_query("CREATE TABLE user_icon(icon_no INT PRIMARY KEY, icon_name TEXT, icon_filename TEXT,
		icon_width INT, icon_height INT, color TEXT, session_id TEXT, appearance TEXT,
		category TEXT, author TEXT, regist_date DATETIME)");
    echo $title . $str;

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
	  echo "�桼����������($file $name $width �� $height $color)����Ͽ���ޤ���" . $footer;
	}
      }
      closedir($handle);
    }
  }
  elseif($revision > 0){ //�ɲåե�����ɽ���
    $sql = mysql_query("SHOW COLUMNS FROM user_icon");
    if(mysql_num_rows($sql) > 0){
      while(($row = mysql_fetch_assoc($sql)) !== false){
	$flag->appearance  |= ($row['Field'] == 'appearance');
	$flag->category    |= ($row['Field'] == 'category');
	$flag->author      |= ($row['Field'] == 'author');
	$flag->regist_date |= ($row['Field'] == 'regist_date');
      }
    }

    $query   = "ALTER TABLE user_icon ADD ";
    $titile .= '�˥ե������(';
    $success = ')���ɲä��ޤ���';
    $failed  = ')���ɲäǤ��ޤ���Ǥ���';

    if(! $flag->appearance){
      $status = (mysql_query("$query appearance TEXT") ? $success : $failed);
      echo $header . 'appearance' . $status . $footer;
    }
    if(! $flag->category){
      $status = (mysql_query("$query category TEXT") ? $success : $failed);
      echo $header . 'category' . $status . $footer;
    }
    if(! $flag->author){
      $status = (mysql_query("$query author TEXT") ? $success : $failed);
      echo $header . 'author' . $status . $footer;
    }
    if(! $flag->regist_date){
      $status = (mysql_query("$query regist_date DATETIME") ? $success : $failed);
      echo $header . 'regist_date' . $status . $footer;
    }
  }

  $title = $header . '(admin_manage)';
  if(! in_array('admin_manage', $table)){
    mysql_query("CREATE TABLE admin_manage(session_id TEXT)");
    mysql_query("INSERT INTO admin_manage VALUES('')");
    echo $title . $str;
  }

  mysql_query("GRANT ALL ON {$db_name}.* TO $db_uname");
  mysql_query('COMMIT'); //������ߥå�
  echo '��������̵����λ���ޤ���' . $footer;
}
