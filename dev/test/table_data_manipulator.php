<?php
/*
  ���Υե�����ϥǡ����١����񤭴�����Ȼٱ�ؿ��򽸤᤿��ΤǤ�
  �����Ԥ�ɬ�פ˱������Խ����뢪���åץ��ɢ��֥饦���ǥ�������
  �Ȥ����Ȥ��������ꤷ�Ƥ��ޤ���

  ��ȯ�ԤΥƥ����ѥ����ɤ��ΤޤޤʤΤ�����ա�
 */
#exit;
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB ��³
//UpdateIconInfo('category', '�������', 1, 10);
//UpdateIconInfo('category', '����Project', 11, 78);
//UpdateIconInfo('appearance', '�������⶿', 13, 21);
//UpdateIconInfo('appearance', '�����š�̴', 22, 33);
//UpdateIconInfo('appearance', '������̴��', 34);
//UpdateIconInfo('appearance', '�������뾶', 35, 42);
//UpdateIconInfo('appearance', '�����ֱ���', 43, 47);
//UpdateIconInfo('appearance', '��������Ͽ', 48, 55);
//UpdateIconInfo('appearance', '��������ŷ', 56, 57);
//UpdateIconInfo('appearance', '����������', 58, 65);
//UpdateIconInfo('appearance', '��������Ʋ', 66, 67);
//UpdateIconInfo('appearance', '����������', 68, 70);
//UpdateIconInfo('appearance', '������ʹ�˵�', 71);
//UpdateIconInfo('appearance', '����ѳ�', 72);
//UpdateIconInfo('appearance', '���������', 76, 77);
//UpdateIconInfo('appearance', '�����Ͱ���', 91, 92);
//UpdateIconInfo('appearance', '����̴����', 181);
//UpdateIconInfo('appearance', '���������', 185, 186);
//UpdateIconInfo('appearance', '������', 121);
//UpdateIconInfo('category', '������', 121);
//UpdateIconInfo('category', '�ݥ��åȥ�󥹥���', 96, 97);
//UpdateIconInfo('appearance', '�ݥ��åȥ�󥹥��� �⡦��', 96);
//UpdateIconInfo('appearance', '�Ϥ���ʹ֥��㡼�ȥ륺', 99);
//UpdateIconInfo('appearance', '�ȥ�󥹥ե����ޡ�G1', 106);
//UpdateIconInfo('category', '�ȥ�󥹥ե����ޡ�', 106);
//UpdateIconInfo('appearance', 'Rozen Maiden', 118);
//UpdateIconInfo('category', '������ᥤ�ǥ�', 118);
//UpdateIconInfo('appearance', '�餭������', 144);
//UpdateIconInfo('author', '�Ʒ�', 12, 77);
//UpdateIconInfo('author', '���������Τ����Ϥ�', 109, 111);
//SendCommit();
//ReconstructEstablishTime();
//ReconstructStartTime();
//ReconstructFinishTime();
OutputActionResult('������λ', '������λ��');

//-- �ؿ� --//
/*
  Ver. 1.4.0 ��3 ���������줿�桼����������ơ��֥���ɲþ������ϻٱ�ؿ�
  type:[appearance / category / author] (��ŵ / ���ƥ��� / ���)
  value: ��������
  from / to: �����оݥ������� (from �� to �ޤ�)
*/
function UpdateIconInfo($type, $value, $from, $to = NULL){
  $query = isset($to) ? "{$from} <= icon_no AND icon_no <= {$to}" : "icon_no = {$from}";
  mysql_query("UPDATE user_icon SET {$type} = '{$value}' WHERE {$query}");
}

function ReconstructEstablishTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE establish_time IS NULL ORDER BY room_no");
  //PrintData($room_list);
  $keyword = '¼������';
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET establish_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} ORDER BY talk_id";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult('SELECT establish_time FROM room WHERE room_no = ' . $room_no); //FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	//$time = date('Y/m/d (D) H:i:s', $talk);
	//$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}')");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET establish_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}

function ReconstructStartTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE start_time IS NULL ORDER BY room_no");
  $keyword = '�����೫�ϡ�';
  //PrintData($room_list);
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET start_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} AND date = 1 ORDER BY talk_id";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET start_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}

function ReconstructFinishTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE finish_time IS NULL ORDER BY room_no");
  //PrintData($room_list);
  $keyword = '�����ཪλ��';
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET finish_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} AND ! (location LIKE '%aftergame%') ORDER BY talk_id DESC";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET finish_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}